<?php
/**
 * إصلاح مشكلة تفعيل الباقات قبل اكتمال الدفع
 * 
 * المشكلة: الباقات تُفعّل عند حالة "processing" بدلاً من انتظار "completed"
 * الحل: إزالة الاستماع لحالة processing والاكتفاء بحالة completed
 * 
 * @package AqarGate
 * @since 1.0.0
 */

// منع الوصول المباشر
if (!defined('ABSPATH')) {
    exit;
}

/**
 * إزالة الـ hooks الأصلية من plugin houzez-woo-addon
 * وإضافة hooks محسنة تنتظر اكتمال الدفع
 */
add_action('init', 'aqargate_fix_woo_package_activation', 999);

function aqargate_fix_woo_package_activation() {
    // التحقق من وجود الكلاس الأصلي
    if (!class_exists('Houzez_Woo_Payment')) {
        return;
    }
    
    // الحصول على instance من الكلاس الأصلي
    global $houzez_woo_payment_instance;
    
    // البحث عن الـ instance في الـ hooks المسجلة
    $hooks_to_check = array(
        'woocommerce_order_status_processing',
        'woocommerce_order_status_completed'
    );
    
    foreach ($hooks_to_check as $hook) {
        // إزالة جميع الـ callbacks المسجلة على هذا الـ hook
        remove_all_actions($hook, 10);
    }
    
    // إضافة الـ hook الصحيح - فقط عند اكتمال الدفع
    add_action('woocommerce_order_status_completed', 'aqargate_woo_payment_complete', 10, 1);
    
    // للحالات التي تحتاج معالجة يدوية (مثل التحويل البنكي)
    add_action('woocommerce_order_status_processing', 'aqargate_woo_payment_pending_notice', 10, 1);
}

/**
 * معالجة اكتمال الدفع - تفعيل الباقة فقط عند التأكد من الدفع
 */
function aqargate_woo_payment_complete($order_id) {
    $order = wc_get_order($order_id);
    
    if (!$order) {
        return;
    }
    
    // التحقق من أن الدفع مكتمل فعلاً
    if ($order->get_status() !== 'completed') {
        return;
    }
    
    $products = $order->get_items();
    
    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $order_title = $product['name'];
        
        // التحقق أولاً من البيانات المحفوظة في الأوردر
        $saved_package_id = $order->get_meta('_houzez_package_id');
        $saved_user_id = $order->get_meta('_houzez_user_id');
        $saved_payment_mode = $order->get_meta('_houzez_payment_mode');
        
        // إذا لم توجد بيانات محفوظة، جلبها من المنتج
        if (!$saved_package_id) {
            $is_woocommerce = intval(get_post_meta($product_id, '_is_houzez_woocommerce', true));
            $payment_mode = get_post_meta($product_id, '_is_houzez_payment_mode', true);
            $package_id = get_post_meta($product_id, '_houzez_package_id', true);
            $user_id = get_post_meta($product_id, '_houzez_user_id', true);
            
            // حفظ البيانات في الأوردر للاستخدام المستقبلي
            if ($payment_mode == 'package' && $package_id) {
                $order->update_meta_data('_houzez_package_id', $package_id);
                $order->update_meta_data('_houzez_user_id', $user_id);
                $order->update_meta_data('_houzez_payment_mode', $payment_mode);
                $order->update_meta_data('_houzez_product_id', $product_id);
                $order->save();
            }
        } else {
            // استخدام البيانات المحفوظة
            $payment_mode = $saved_payment_mode;
            $package_id = $saved_package_id;
            $user_id = $saved_user_id;
        }
        
        // تفعيل الباقة أو الإعلان حسب نوع الدفع
        if ($payment_mode == 'package') {
            // إذا كانت البيانات محفوظة، استخدمها مباشرة
            if ($saved_package_id) {
                aqargate_activate_package_from_saved_data($order, $saved_package_id, $saved_user_id);
            } else {
                aqargate_activate_package($product_id, $order, $order_title);
            }
        } else if ($payment_mode == 'per_listing') {
            aqargate_activate_listing($product_id, $order, $order_title);
        }
    }
}

/**
 * إشعار للطلبات قيد المعالجة
 */
function aqargate_woo_payment_pending_notice($order_id) {
    $order = wc_get_order($order_id);
    
    if (!$order) {
        return;
    }
    
    // إضافة ملاحظة للطلب
    $order->add_order_note(
        'الباقة لن يتم تفعيلها حتى يتم تأكيد الدفع واكتمال الطلب.',
        false,
        true
    );
    
    // يمكن إرسال إيميل للعميل هنا لإخباره بأن الباقة قيد الانتظار
    $user_id = $order->get_user_id();
    if ($user_id) {
        $user_email = get_userdata($user_id)->user_email;
        
        // إضافة meta للمستخدم للإشارة إلى وجود باقة قيد الانتظار
        update_user_meta($user_id, 'pending_package_order', $order_id);
    }
}

/**
 * تفعيل الباقة من البيانات المحفوظة في الأوردر
 */
function aqargate_activate_package_from_saved_data($woo_order, $package_id, $userID) {
    $admin_email = get_bloginfo('admin_email');
    $payment_method_title = $woo_order->get_payment_method_title();
    
    $time = time();
    $date = date('Y-m-d H:i:s', $time);
    
    // إذا لم يتم تحديد المستخدم، استخدم مستخدم الأوردر
    if (!$userID) {
        $userID = $woo_order->get_user_id();
    }
    
    $user_email = get_userdata($userID)->user_email;
    $order_title = get_the_title($package_id);
    
    // التحقق من أن البيانات صحيحة قبل التفعيل
    if (!$package_id || !$userID) {
        $woo_order->add_order_note(
            'خطأ: لم يتم العثور على معلومات الباقة أو المستخدم.',
            false,
            true
        );
        return;
    }
    
    // حفظ الباقة الحالية قبل الاستبدال (إن وجدت)
    aqargate_archive_current_package($userID, $woo_order->get_id());
    
    // تفعيل الباقة
    houzez_save_user_packages_record($userID);
    
    if (houzez_check_user_existing_package_status($userID, $package_id)) {
        houzez_downgrade_package($userID, $package_id);
        houzez_update_membership_package($userID, $package_id);
    } else {
        houzez_update_membership_package($userID, $package_id);
    }
    
    // إنشاء الفاتورة
    $invoiceID = houzez_generate_invoice($order_title, 'one_time', $package_id, $date, $userID, 0, 0, '', $payment_method_title, 1);
    update_post_meta($invoiceID, 'invoice_payment_status', 1);
    
    // إزالة علامة الباقة المعلقة
    delete_user_meta($userID, 'pending_package_order');
    
    // تحديث معلومات الاشتراك مع ربطها بالأوردر
    update_user_meta($userID, 'houzez_has_stripe_recurring', 0);
    update_user_meta($userID, 'package_activation_date', current_time('mysql'));
    update_user_meta($userID, 'active_package_order_id', $woo_order->get_id());
    
    // حساب تاريخ الانتهاء
    $pack_billing_period = get_post_meta($package_id, 'pack_billing_period', true);
    $pack_billing_frequency = get_post_meta($package_id, 'pack_billing_frequency', true);
    $expiry_date = aqargate_calculate_package_expiry($pack_billing_period, $pack_billing_frequency);
    update_user_meta($userID, 'package_expiry_date', $expiry_date);
    
    // إرسال إيميل التأكيد
    $args = array(
        'package_title' => get_the_title($package_id),
        'package_id' => $package_id,
        'invoice_no' => $invoiceID,
        'activation_date' => date_i18n(get_option('date_format'), strtotime($date))
    );
    
    houzez_email_type($user_email, 'purchase_activated_pack', $args);
    
    // إضافة ملاحظة للطلب
    $woo_order->add_order_note(
        sprintf('تم تفعيل الباقة "%s" بنجاح للمستخدم #%d (من البيانات المحفوظة)', get_the_title($package_id), $userID),
        false,
        true
    );
}

/**
 * تفعيل الباقة عند اكتمال الدفع
 */
function aqargate_activate_package($product_id, $woo_order, $order_title) {
    $admin_email = get_bloginfo('admin_email');
    $payment_method_title = $woo_order->get_payment_method_title();
    
    $time = time();
    $date = date('Y-m-d H:i:s', $time);
    
    $package_id = intval(get_post_meta($product_id, '_houzez_package_id', true));
    $userID = intval(get_post_meta($product_id, '_houzez_user_id', true));
    $user_email = get_post_meta($product_id, '_houzez_user_email', true);
    
    // التحقق من أن البيانات صحيحة قبل التفعيل
    if (!$package_id || !$userID) {
        $woo_order->add_order_note(
            'خطأ: لم يتم العثور على معلومات الباقة أو المستخدم.',
            false,
            true
        );
        return;
    }
    
    // حفظ الباقة الحالية قبل الاستبدال (إن وجدت)
    aqargate_archive_current_package($userID, $woo_order->get_id());
    
    // تفعيل الباقة
    houzez_save_user_packages_record($userID);
    
    if (houzez_check_user_existing_package_status($userID, $package_id)) {
        houzez_downgrade_package($userID, $package_id);
        houzez_update_membership_package($userID, $package_id);
    } else {
        houzez_update_membership_package($userID, $package_id);
    }
    
    // إنشاء الفاتورة
    $invoiceID = houzez_generate_invoice($order_title, 'one_time', $package_id, $date, $userID, 0, 0, '', $payment_method_title, 1);
    update_post_meta($invoiceID, 'invoice_payment_status', 1);
    
    // إزالة علامة الباقة المعلقة
    delete_user_meta($userID, 'pending_package_order');
    
    // تحديث معلومات الاشتراك مع ربطها بالأوردر
    update_user_meta($userID, 'houzez_has_stripe_recurring', 0);
    update_user_meta($userID, 'package_activation_date', current_time('mysql'));
    update_user_meta($userID, 'active_package_order_id', $woo_order->get_id());
    
    // حساب تاريخ الانتهاء
    $pack_billing_period = get_post_meta($package_id, 'pack_billing_period', true);
    $pack_billing_frequency = get_post_meta($package_id, 'pack_billing_frequency', true);
    $expiry_date = aqargate_calculate_package_expiry($pack_billing_period, $pack_billing_frequency);
    update_user_meta($userID, 'package_expiry_date', $expiry_date);
    
    // إرسال إيميل التأكيد
    $args = array(
        'package_title' => get_the_title($package_id),
        'package_id' => $package_id,
        'invoice_no' => $invoiceID,
        'activation_date' => date_i18n(get_option('date_format'), strtotime($date))
    );
    
    houzez_email_type($user_email, 'purchase_activated_pack', $args);
    
    // إضافة ملاحظة للطلب
    $woo_order->add_order_note(
        sprintf('تم تفعيل الباقة "%s" بنجاح للمستخدم #%d', get_the_title($package_id), $userID),
        false,
        true
    );
}

/**
 * تفعيل الإعلان عند اكتمال الدفع
 */
function aqargate_activate_listing($product_id, $woo_order, $order_title) {
    $admin_email = get_bloginfo('admin_email');
    $payment_method_title = $woo_order->get_payment_method_title();
    
    $time = time();
    $date = date('Y-m-d H:i:s', $time);
    
    $is_featured = get_post_meta($product_id, '_houzez_is_featured', true);
    $listing_id = intval(get_post_meta($product_id, '_houzez_listing_id', true));
    $userID = intval(get_post_meta($product_id, '_houzez_user_id', true));
    $user_email = get_post_meta($product_id, '_houzez_user_email', true);
    
    if ($is_featured == 1) {
        update_post_meta($listing_id, 'fave_featured', 1);
        update_post_meta($listing_id, 'houzez_featured_listing_date', current_time('mysql'));
        
        $invoice_id = houzez_generate_invoice($order_title, 'one_time', $listing_id, $date, $userID, 0, 1, '', $payment_method_title);
        update_post_meta($invoice_id, 'invoice_payment_status', 1);
        
        $args = array(
            'listing_title' => get_the_title($listing_id),
            'listing_id' => $listing_id,
            'invoice_no' => $invoice_id,
        );
        
        houzez_email_type($user_email, 'featured_submission_listing', $args);
        houzez_email_type($admin_email, 'admin_featured_submission_listing', $args);
        
    } else {
        update_post_meta($listing_id, 'fave_payment_status', 'paid');
        
        $paid_submission_status = houzez_option('enable_paid_submission');
        $listings_admin_approved = houzez_option('listings_admin_approved');
        
        if ($listings_admin_approved != 'yes' && $paid_submission_status == 'per_listing') {
            $post = array(
                'ID' => $listing_id,
                'post_status' => 'publish',
                'post_date' => current_time('mysql')
            );
            wp_update_post($post);
        }
        
        $invoice_id = houzez_generate_invoice($order_title, 'one_time', $listing_id, $date, $userID, 0, 0, '', $payment_method_title);
        update_post_meta($invoice_id, 'invoice_payment_status', 1);
        
        $args = array(
            'listing_title' => get_the_title($listing_id),
            'listing_id' => $listing_id,
            'invoice_no' => $invoice_id,
        );
        
        houzez_email_type($user_email, 'paid_submission_listing', $args);
        houzez_email_type($admin_email, 'admin_paid_submission_listing', $args);
    }
}

/**
 * مراقبة تغيير حالة الطلب من مكتمل إلى أي حالة أخرى
 * عند التغيير، يتم إلغاء تفعيل الباقة فوراً
 */
add_action('woocommerce_order_status_changed', 'aqargate_handle_order_status_change', 999, 4);

function aqargate_handle_order_status_change($order_id, $old_status, $new_status, $order) {
    // التحقق من أن الطلب كان مكتملاً وتم تغييره لحالة أخرى
    if ($old_status === 'completed' && $new_status !== 'completed') {
        aqargate_deactivate_package_on_order_change($order);
    }
    
    // إذا تم تغيير الحالة إلى ملغي أو فاشل أو مسترد
    if (in_array($new_status, array('cancelled', 'failed', 'refunded'))) {
        aqargate_deactivate_package_on_order_change($order);
    }
}

/**
 * مراقبة حذف الطلبات ونقلها إلى سلة المهملات
 */
add_action('wp_trash_post', 'aqargate_handle_order_trash', 10, 1);
add_action('before_delete_post', 'aqargate_handle_order_deletion', 10, 1);
add_action('woocommerce_before_delete_order', 'aqargate_handle_woo_order_deletion', 10, 1);
add_action('woocommerce_before_trash_order', 'aqargate_handle_woo_order_trash', 10, 2);

// معالجة نقل الطلب إلى سلة المهملات
function aqargate_handle_order_trash($post_id) {
    // التحقق من نوع المنشور
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, array('shop_order', 'wc_order'))) {
        return;
    }
    
    $order = wc_get_order($post_id);
    if (!$order) {
        return;
    }
    
    // إلغاء تفعيل الباقة المرتبطة
    aqargate_deactivate_package_on_order_change($order);
}

function aqargate_handle_order_deletion($post_id) {
    // التحقق من نوع المنشور - دعم كلا النوعين
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, array('shop_order', 'wc_order'))) {
        return;
    }
    
    $order = wc_get_order($post_id);
    if (!$order) {
        return;
    }
    
    // إلغاء تفعيل الباقة المرتبطة
    aqargate_deactivate_package_on_order_change($order);
}

// Hooks خاصة بـ WooCommerce HPOS (High Performance Order Storage)
function aqargate_handle_woo_order_deletion($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    
    // إلغاء تفعيل الباقة المرتبطة
    aqargate_deactivate_package_on_order_change($order);
}

function aqargate_handle_woo_order_trash($order_id, $order) {
    if (!$order) {
        $order = wc_get_order($order_id);
    }
    
    if (!$order) {
        return;
    }
    
    // إلغاء تفعيل الباقة المرتبطة
    aqargate_deactivate_package_on_order_change($order);
}

/**
 * أرشفة الباقة الحالية قبل تفعيل باقة جديدة
 */
function aqargate_archive_current_package($user_id, $new_order_id) {
    // التحقق من وجود باقة نشطة
    $current_package_id = get_user_meta($user_id, 'package_id', true);
    if (!$current_package_id) {
        return;
    }
    
    // جلب معلومات الباقة الحالية
    $current_order_id = get_user_meta($user_id, 'active_package_order_id', true);
    
    // التحقق من أن الأوردر مازال صالحاً
    $order = wc_get_order($current_order_id);
    if (!$order || $order->get_status() !== 'completed') {
        return;
    }
    
    // حفظ الباقة في الأرشيف
    $package_archive = get_user_meta($user_id, 'package_archive', true);
    if (!is_array($package_archive)) {
        $package_archive = array();
    }
    
    $package_archive[] = array(
        'package_id' => $current_package_id,
        'order_id' => $current_order_id,
        'listings_remaining' => get_user_meta($user_id, 'package_listings', true),
        'featured_remaining' => get_user_meta($user_id, 'package_featured_listings', true),
        'activation_date' => get_user_meta($user_id, 'package_activation_date', true),
        'expiry_date' => get_user_meta($user_id, 'package_expiry_date', true),
        'archived_date' => current_time('mysql'),
        'replaced_by_order' => $new_order_id,
        'status' => 'replaced'
    );
    
    update_user_meta($user_id, 'package_archive', $package_archive);
}

/**
 * حساب تاريخ انتهاء الباقة
 */
function aqargate_calculate_package_expiry($billing_period, $billing_frequency) {
    $frequency = intval($billing_frequency);
    if ($frequency <= 0) $frequency = 1;
    
    switch ($billing_period) {
        case 'Day':
            $expiry = strtotime("+{$frequency} days");
            break;
        case 'Week':
            $expiry = strtotime("+{$frequency} weeks");
            break;
        case 'Month':
            $expiry = strtotime("+{$frequency} months");
            break;
        case 'Year':
            $expiry = strtotime("+{$frequency} years");
            break;
        default:
            $expiry = strtotime("+30 days");
    }
    
    return date('Y-m-d H:i:s', $expiry);
}

/**
 * إلغاء تفعيل الباقة عند تغيير حالة الطلب أو حذفه
 */
function aqargate_deactivate_package_on_order_change($order) {
    if (!$order) {
        return;
    }
    
    $order_id = $order->get_id();
    $user_id = $order->get_user_id();
    
    if (!$user_id) {
        return;
    }
    
    $products = $order->get_items();
    
    // التحقق من أن هذا هو الأوردر النشط
    $active_order_id = get_user_meta($user_id, 'active_package_order_id', true);
    $current_package_id = get_user_meta($user_id, 'package_id', true);
    
    // إذا لا يوجد active_package_order_id (أوردرات قديمة)، نتحقق من الباقة
    if (!$active_order_id && $current_package_id) {
        // نبحث عن الأوردر المرتبط بالباقة الحالية
        $products = $order->get_items();
        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $payment_mode = get_post_meta($product_id, '_is_houzez_payment_mode', true);
            
            if ($payment_mode == 'package') {
                $package_id = intval(get_post_meta($product_id, '_houzez_package_id', true));
                
                // إذا كانت هذه هي الباقة النشطة، اعتبر هذا الأوردر نشطاً
                if ($package_id == $current_package_id) {
                    update_user_meta($user_id, 'active_package_order_id', $order_id);
                    $active_order_id = $order_id;
                    break;
                }
            }
        }
    }
    
    // إذا لم يكن هذا هو الأوردر النشط، لا نفعل شيئاً
    if ($active_order_id && $active_order_id != $order_id) {
        return;
    }
    
    // إلغاء تفعيل الباقة الحالية
    $current_package_id = get_user_meta($user_id, 'package_id', true);
    if ($current_package_id) {
        aqargate_deactivate_user_package($user_id, $current_package_id, $order_id);
        
        // محاولة استعادة باقة سابقة صالحة
        aqargate_restore_previous_package($user_id);
    }
}

/**
 * استعادة باقة سابقة صالحة
 */
function aqargate_restore_previous_package($user_id) {
    $package_archive = get_user_meta($user_id, 'package_archive', true);
    
    if (!is_array($package_archive) || empty($package_archive)) {
        return false;
    }
    
    // البحث من الأحدث للأقدم
    $package_archive = array_reverse($package_archive);
    
    foreach ($package_archive as $key => $archived_package) {
        $order = wc_get_order($archived_package['order_id']);
        
        // التحقق من صلاحية الأوردر
        if (!$order || $order->get_status() !== 'completed') {
            continue;
        }
        
        // التحقق من تاريخ الانتهاء
        if (strtotime($archived_package['expiry_date']) <= time()) {
            continue;
        }
        
        // وجدنا باقة صالحة! نستعيدها
        
        // استعادة البيانات
        update_user_meta($user_id, 'package_id', $archived_package['package_id']);
        update_user_meta($user_id, 'package_listings', $archived_package['listings_remaining']);
        update_user_meta($user_id, 'package_featured_listings', $archived_package['featured_remaining']);
        update_user_meta($user_id, 'package_activation_date', $archived_package['activation_date']);
        update_user_meta($user_id, 'package_expiry_date', $archived_package['expiry_date']);
        update_user_meta($user_id, 'active_package_order_id', $archived_package['order_id']);
        update_user_meta($user_id, 'package_activation', $archived_package['activation_date']);
        
        // تحديث حالة الباقة في الأرشيف
        $package_archive = array_reverse($package_archive);
        $package_archive[$key]['status'] = 'restored';
        $package_archive[$key]['restored_date'] = current_time('mysql');
        update_user_meta($user_id, 'package_archive', $package_archive);
        
        // إشعار المستخدم
        $user = get_userdata($user_id);
        if ($user) {
            $days_remaining = round((strtotime($archived_package['expiry_date']) - time()) / (60 * 60 * 24));
            $message = sprintf(
                'تم استعادة باقتك السابقة "%s" بنجاح. متبقي لديك %d إعلان و %d يوم من الصلاحية.',
                get_the_title($archived_package['package_id']),
                $archived_package['listings_remaining'],
                $days_remaining
            );
            
            // يمكن إضافة إشعار أو إيميل هنا
        }
        
        return true;
    }
    
    return false;
}

/**
 * إلغاء تفعيل باقة المستخدم
 */
function aqargate_deactivate_user_package($user_id, $package_id, $order_id) {
    // حفظ سجل بالباقة الملغاة قبل الحذف
    $deactivation_data = array(
        'package_id' => $package_id,
        'package_title' => get_the_title($package_id),
        'order_id' => $order_id,
        'deactivation_date' => current_time('mysql'),
        'listings_remaining' => get_user_meta($user_id, 'package_listings', true),
        'featured_remaining' => get_user_meta($user_id, 'package_featured_listings', true),
        'activation_date' => get_user_meta($user_id, 'package_activation', true)
    );
    
    // حفظ السجل
    $deactivation_history = get_user_meta($user_id, 'deactivated_packages_history', true);
    if (!is_array($deactivation_history)) {
        $deactivation_history = array();
    }
    $deactivation_history[] = $deactivation_data;
    update_user_meta($user_id, 'deactivated_packages_history', $deactivation_history);
    
    // إلغاء تفعيل الباقة
    delete_user_meta($user_id, 'package_id');
    delete_user_meta($user_id, 'package_listings');
    delete_user_meta($user_id, 'package_featured_listings');
    delete_user_meta($user_id, 'package_activation');
    delete_user_meta($user_id, 'package_order_id');
    delete_user_meta($user_id, 'package_activation_date');
    
    // إضافة علامة للباقة الملغاة
    update_user_meta($user_id, 'last_deactivated_package', $package_id);
    update_user_meta($user_id, 'last_deactivation_date', current_time('mysql'));
    update_user_meta($user_id, 'last_deactivation_reason', 'order_status_changed_or_deleted');
    
    // حذف ربط الأوردر النشط
    delete_user_meta($user_id, 'active_package_order_id');
    
    // إرسال إشعار للمستخدم
    $user = get_userdata($user_id);
    if ($user) {
        // إضافة ملاحظة في سجل النشاطات إذا كان متاحاً
        if (function_exists('aal_insert_log')) {
            aal_insert_log(array(
                'action' => 'package_deactivated',
                'object_type' => 'User',
                'object_subtype' => 'package',
                'object_id' => $user_id,
                'object_name' => $user->user_email,
                'user_id' => $user_id,
                'hist_ip' => $_SERVER['REMOTE_ADDR'],
                'hist_time' => current_time('timestamp'),
            ));
        }
        
        // يمكن إضافة إرسال إيميل هنا
        $email_args = array(
            'user_email' => $user->user_email,
            'user_name' => $user->display_name,
            'package_title' => $deactivation_data['package_title'],
            'order_id' => $order_id,
            'deactivation_date' => date_i18n(get_option('date_format'), strtotime($deactivation_data['deactivation_date']))
        );
        
        // استخدام دالة Houzez لإرسال الإيميل إذا كانت متاحة
        if (function_exists('houzez_email_type')) {
            // يمكن إنشاء قالب إيميل مخصص لهذا الغرض
            // houzez_email_type($user->user_email, 'package_deactivated', $email_args);
        }
        
        // إضافة إشعار في لوحة التحكم
        if (function_exists('houzez_add_notification')) {
            houzez_add_notification($user_id, 
                'تم إلغاء تفعيل باقتك "' . $deactivation_data['package_title'] . '" بسبب تغيير حالة الطلب أو حذفه.',
                'package_deactivated'
            );
        }
    }
    
}

/**
 * إضافة إشعار في لوحة التحكم عند وجود باقة ملغاة
 */
add_action('houzez_dashboard_membership_before', 'aqargate_show_deactivated_package_notice');

function aqargate_show_deactivated_package_notice() {
    $user_id = get_current_user_id();
    $last_deactivated = get_user_meta($user_id, 'last_deactivated_package', true);
    $deactivation_date = get_user_meta($user_id, 'last_deactivation_date', true);
    
    if ($last_deactivated) {
        $package_title = get_the_title($last_deactivated);
        $days_ago = round((time() - strtotime($deactivation_date)) / (60 * 60 * 24));
        
        // عرض الإشعار فقط إذا كان الإلغاء خلال آخر 30 يوم
        if ($days_ago <= 30) {
            ?>
            <div class="alert alert-danger" role="alert">
                <i class="houzez-icon icon-alert-triangle mr-1"></i>
                <strong>باقة ملغاة:</strong>
                تم إلغاء تفعيل باقتك "<?php echo esc_html($package_title); ?>" 
                <?php 
                if ($days_ago == 0) {
                    echo 'اليوم';
                } elseif ($days_ago == 1) {
                    echo 'أمس';
                } else {
                    echo 'منذ ' . $days_ago . ' يوم';
                }
                ?> بسبب تغيير حالة الطلب أو حذفه.
                <br>
                <small>يرجى التواصل مع الإدارة إذا كان هناك خطأ.</small>
            </div>
            <?php
        }
    }
}

/**
 * إضافة إشعار في لوحة تحكم المستخدم إذا كان لديه باقة قيد الانتظار
 */
add_action('houzez_dashboard_membership_before', 'aqargate_show_pending_package_notice');

function aqargate_show_pending_package_notice() {
    $user_id = get_current_user_id();
    $pending_order_id = get_user_meta($user_id, 'pending_package_order', true);
    
    if ($pending_order_id) {
        $order = wc_get_order($pending_order_id);
        
        if ($order && $order->get_status() == 'processing') {
            ?>
            <div class="alert alert-warning" role="alert">
                <i class="houzez-icon icon-info-circle mr-1"></i>
                <strong>باقة قيد الانتظار:</strong>
                لديك باقة قيد انتظار تأكيد الدفع. سيتم تفعيلها تلقائياً بمجرد تأكيد الدفع.
                <br>
                <small>رقم الطلب: #<?php echo esc_html($pending_order_id); ?></small>
            </div>
            <?php
        } else if ($order && $order->get_status() == 'completed') {
            // إذا اكتمل الطلب، احذف العلامة
            delete_user_meta($user_id, 'pending_package_order');
        }
    }
}
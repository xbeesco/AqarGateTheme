<?php
/**
 * نظام الدفع للعقارات عبر WooCommerce
 * 
 * @package AqarGate
 * @since 1.0.0
 */

// منع الوصول المباشر
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class AqarGate_Property_Payment
 * يدير نظام الدفع للعقارات الفردية
 */
class AqarGate_Property_Payment {
    
    /**
     * المُنشئ
     */
    public function __construct() {
        // Meta Box للعقارات
        add_action('add_meta_boxes', array($this, 'add_payment_metabox'));
        add_action('save_post_property', array($this, 'save_payment_meta'));
        
        // عرض زر الدفع بعد الخريطة وتفاصيل العقار
        add_action('houzez_single_property_after_map', array($this, 'display_payment_button'), 20);
        
        // إذا لم يكن الـ hook متاح، نستخدم hook آخر
        add_action('houzez_single_property_after_address', array($this, 'display_payment_button'), 20);
        
        // معالجات AJAX
        add_action('wp_ajax_process_property_payment', array($this, 'process_payment'));
        add_action('wp_ajax_nopriv_process_property_payment', array($this, 'process_payment_nopriv'));
        
        // تحميل الأصول
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * إضافة Meta Box لإعدادات الدفع
     */
    public function add_payment_metabox() {
        add_meta_box(
            'property_payment_settings',
            'إعدادات الدفع للعقار',
            array($this, 'render_payment_metabox'),
            'property',
            'side',
            'high'
        );
    }
    
    /**
     * عرض محتوى Meta Box
     */
    public function render_payment_metabox($post) {
        // Nonce field للأمان
        wp_nonce_field('property_payment_metabox', 'property_payment_nonce');
        
        // جلب القيم المحفوظة
        $enable_payment = get_post_meta($post->ID, '_enable_property_payment', true);
        $property_price = get_post_meta($post->ID, '_property_payment_price', true);
        $payment_type = get_post_meta($post->ID, '_property_payment_type', true);
        $payment_label = get_post_meta($post->ID, '_property_payment_label', true);
        ?>
        
        <div class="property-payment-metabox">
            <p>
                <label>
                    <input type="checkbox" name="enable_property_payment" value="yes" 
                           <?php checked($enable_payment, 'yes'); ?>>
                    <strong>تفعيل زر الدفع لهذا العقار</strong>
                </label>
            </p>
            
            <div class="payment-fields" style="<?php echo $enable_payment !== 'yes' ? 'display:none;' : ''; ?>">
                <p>
                    <label for="property_payment_price"><strong>السعر (ريال سعودي):</strong></label>
                    <input type="number" 
                           id="property_payment_price"
                           name="property_payment_price" 
                           value="<?php echo esc_attr($property_price); ?>" 
                           class="widefat"
                           min="0"
                           step="0.01">
                </p>
                
                <p>
                    <label for="property_payment_type"><strong>نوع المعاملة:</strong></label>
                    <select name="property_payment_type" id="property_payment_type" class="widefat">
                        <option value="sale" <?php selected($payment_type, 'sale'); ?>>شراء العقار</option>
                        <option value="rent" <?php selected($payment_type, 'rent'); ?>>إيجار العقار</option>
                        <option value="booking" <?php selected($payment_type, 'booking'); ?>>حجز العقار</option>
                        <option value="commission" <?php selected($payment_type, 'commission'); ?>>دفع عمولة</option>
                        <option value="inspection" <?php selected($payment_type, 'inspection'); ?>>معاينة العقار</option>
                    </select>
                </p>
                
                <p>
                    <label for="property_payment_label"><strong>نص الزر المخصص (اختياري):</strong></label>
                    <input type="text" 
                           id="property_payment_label"
                           name="property_payment_label" 
                           value="<?php echo esc_attr($payment_label); ?>" 
                           class="widefat"
                           placeholder="مثال: احجز الآن">
                    <small>اتركه فارغاً لاستخدام النص الافتراضي</small>
                </p>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('input[name="enable_property_payment"]').on('change', function() {
                if($(this).is(':checked')) {
                    $('.payment-fields').slideDown();
                } else {
                    $('.payment-fields').slideUp();
                }
            });
        });
        </script>
        
        <style>
        .property-payment-metabox p {
            margin: 15px 0;
        }
        .property-payment-metabox label {
            display: block;
            margin-bottom: 5px;
        }
        .property-payment-metabox small {
            color: #666;
            display: block;
            margin-top: 3px;
        }
        </style>
        <?php
    }
    
    /**
     * حفظ بيانات Meta Box
     */
    public function save_payment_meta($post_id) {
        // التحقق من Nonce
        if (!isset($_POST['property_payment_nonce']) || 
            !wp_verify_nonce($_POST['property_payment_nonce'], 'property_payment_metabox')) {
            return;
        }
        
        // التحقق من الصلاحيات
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // حفظ البيانات
        $enable = isset($_POST['enable_property_payment']) ? 'yes' : 'no';
        update_post_meta($post_id, '_enable_property_payment', $enable);
        
        if (isset($_POST['property_payment_price'])) {
            update_post_meta($post_id, '_property_payment_price', 
                           sanitize_text_field($_POST['property_payment_price']));
        }
        
        if (isset($_POST['property_payment_type'])) {
            update_post_meta($post_id, '_property_payment_type', 
                           sanitize_text_field($_POST['property_payment_type']));
        }
        
        if (isset($_POST['property_payment_label'])) {
            update_post_meta($post_id, '_property_payment_label', 
                           sanitize_text_field($_POST['property_payment_label']));
        }
    }
    
    /**
     * عرض زر الدفع في صفحة العقار
     */
    public function display_payment_button() {
        global $post;
        
        // التحقق من تفعيل الدفع
        $enable_payment = get_post_meta($post->ID, '_enable_property_payment', true);
        
        if ($enable_payment !== 'yes') {
            return;
        }
        
        // جلب البيانات
        $price = get_post_meta($post->ID, '_property_payment_price', true);
        $type = get_post_meta($post->ID, '_property_payment_type', true);
        $custom_label = get_post_meta($post->ID, '_property_payment_label', true);
        
        // تحديد نص الزر
        if (!empty($custom_label)) {
            $button_text = $custom_label;
        } else {
            $button_text = $this->get_button_text($type);
        }
        
        // عرض الزر
        ?>
        <div class="property-payment-wrapper">
            <div class="payment-info">
                <div class="price-label">السعر المطلوب:</div>
                <div class="price-amount"><?php echo number_format($price, 2); ?> ريال سعودي</div>
            </div>
            
            <button class="btn btn-primary btn-lg property-payment-btn" 
                    data-property-id="<?php echo $post->ID; ?>"
                    data-price="<?php echo esc_attr($price); ?>"
                    data-type="<?php echo esc_attr($type); ?>"
                    data-nonce="<?php echo wp_create_nonce('property_payment_' . $post->ID); ?>">
                <i class="houzez-icon icon-shopping-cart-1 mr-1"></i>
                <span class="btn-text"><?php echo esc_html($button_text); ?></span>
            </button>
            
            <div class="payment-secure-note">
                <i class="houzez-icon icon-lock-5"></i>
                عملية دفع آمنة ومشفرة
            </div>
        </div>
        <?php
    }
    
    /**
     * الحصول على نص الزر حسب النوع
     */
    private function get_button_text($type) {
        $texts = array(
            'sale' => 'اشتري الآن',
            'rent' => 'استأجر الآن',
            'booking' => 'احجز الآن',
            'commission' => 'ادفع العمولة',
            'inspection' => 'احجز معاينة'
        );
        
        return isset($texts[$type]) ? $texts[$type] : 'ادفع الآن';
    }
    
    /**
     * معالج AJAX للدفع - للمستخدمين المسجلين
     */
    public function process_payment() {
        // التحقق من Nonce
        $property_id = intval($_POST['property_id']);
        if (!wp_verify_nonce($_POST['nonce'], 'property_payment_' . $property_id)) {
            wp_send_json_error(array('message' => 'خطأ في الأمان. حاول تحديث الصفحة.'));
        }
        
        // معالجة الدفع
        $this->handle_payment_process($property_id);
    }
    
    /**
     * معالج AJAX للدفع - للزوار
     */
    public function process_payment_nopriv() {
        wp_send_json_error(array(
            'message' => 'يجب تسجيل الدخول أولاً للمتابعة',
            'redirect' => wp_login_url(get_permalink($_POST['property_id']))
        ));
    }
    
    /**
     * معالجة عملية الدفع الفعلية
     */
    private function handle_payment_process($property_id) {
        // جلب بيانات العقار
        $property = get_post($property_id);
        if (!$property) {
            wp_send_json_error(array('message' => 'العقار غير موجود'));
        }
        
        $price = get_post_meta($property_id, '_property_payment_price', true);
        $type = get_post_meta($property_id, '_property_payment_type', true);
        
        // جلب بيانات المستخدم
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        
        // التحقق من وجود WooCommerce
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(array('message' => 'نظام الدفع غير متاح حالياً'));
        }
        
        try {
            // إنشاء منتج WooCommerce مؤقت
            $product_id = $this->create_woo_product($property, $price, $type);
            
            // إذا كان يوجد كلاس Aqargate_woo، استخدمه
            if (class_exists('Aqargate_woo')) {
                $aqar_woo = new Aqargate_woo();
                // يمكن استخدام وظائفه هنا
            }
            
            // إنشاء طلب WooCommerce
            $order_id = $this->create_woo_order($product_id, $user_id, $price);
            
            if ($order_id) {
                $order = wc_get_order($order_id);
                $checkout_url = $order->get_checkout_payment_url();
                
                wp_send_json_success(array(
                    'message' => 'تم إنشاء الطلب بنجاح',
                    'redirect_url' => $checkout_url
                ));
            } else {
                wp_send_json_error(array('message' => 'فشل إنشاء الطلب'));
            }
            
        } catch (Exception $e) {
            wp_send_json_error(array('message' => 'خطأ: ' . $e->getMessage()));
        }
    }
    
    /**
     * إنشاء منتج WooCommerce
     */
    private function create_woo_product($property, $price, $type) {
        // البحث عن منتج موجود للعقار
        $existing_product = get_posts(array(
            'post_type' => 'product',
            'meta_key' => '_property_id',
            'meta_value' => $property->ID,
            'posts_per_page' => 1
        ));
        
        if ($existing_product) {
            $product_id = $existing_product[0]->ID;
            // تحديث السعر
            update_post_meta($product_id, '_regular_price', $price);
            update_post_meta($product_id, '_price', $price);
        } else {
            // إنشاء منتج جديد
            $product_args = array(
                'post_title' => sprintf('دفعة للعقار: %s (#%d)', $property->post_title, $property->ID),
                'post_content' => sprintf('دفعة %s للعقار: %s', $this->get_button_text($type), $property->post_title),
                'post_status' => 'publish',
                'post_type' => 'product',
                'meta_input' => array(
                    '_regular_price' => $price,
                    '_price' => $price,
                    '_property_id' => $property->ID,
                    '_property_type' => $type,
                    '_virtual' => 'yes',
                    '_sold_individually' => 'yes',
                    '_manage_stock' => 'no',
                    '_stock_status' => 'instock'
                )
            );
            
            $product_id = wp_insert_post($product_args);
            
            // تعيين تصنيف المنتج
            wp_set_object_terms($product_id, 'simple', 'product_type');
        }
        
        return $product_id;
    }
    
    /**
     * إنشاء طلب WooCommerce
     */
    private function create_woo_order($product_id, $user_id, $price) {
        // جلب بيانات المستخدم
        $user = get_userdata($user_id);
        
        // إعداد بيانات العنوان
        $address = array(
            'first_name' => get_user_meta($user_id, 'first_name', true),
            'last_name' => get_user_meta($user_id, 'last_name', true),
            'email' => $user->user_email,
            'phone' => get_user_meta($user_id, 'fave_author_mobile', true),
            'address_1' => get_user_meta($user_id, 'billing_address_1', true) ?: 'غير محدد',
            'city' => get_user_meta($user_id, 'billing_city', true) ?: 'الرياض',
            'state' => get_user_meta($user_id, 'billing_state', true) ?: '',
            'postcode' => get_user_meta($user_id, 'billing_postcode', true) ?: '11111',
            'country' => 'SA'
        );
        
        // إنشاء الطلب
        $order = wc_create_order(array(
            'customer_id' => $user_id,
            'created_via' => 'property_payment',
            'status' => 'pending'
        ));
        
        if (!$order) {
            return false;
        }
        
        // إضافة المنتج للطلب
        $order->add_product(wc_get_product($product_id), 1);
        
        // تعيين العناوين
        $order->set_address($address, 'billing');
        $order->set_address($address, 'shipping');
        
        // حساب المجاميع
        $order->calculate_totals();
        
        // إضافة ملاحظة
        $order->add_order_note('طلب دفعة عقار تم إنشاؤه من صفحة العقار');
        
        return $order->get_id();
    }
    
    /**
     * تحميل ملفات JavaScript و CSS
     */
    public function enqueue_scripts() {
        if (!is_singular('property')) {
            return;
        }
        
        // تحميل JavaScript
        wp_enqueue_script(
            'property-payment-js',
            get_stylesheet_directory_uri() . '/assets/js/property-payment.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // تمرير البيانات للـ JavaScript
        wp_localize_script('property-payment-js', 'property_payment_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('property_payment_ajax'),
            'messages' => array(
                'confirm' => 'هل أنت متأكد من المتابعة للدفع؟',
                'processing' => 'جاري المعالجة...',
                'error' => 'حدث خطأ، حاول مرة أخرى'
            )
        ));
        
        // تحميل CSS
        wp_enqueue_style(
            'property-payment-css',
            get_stylesheet_directory_uri() . '/assets/css/property-payment.css',
            array(),
            '1.0.0'
        );
    }
}

// تهيئة الكلاس
new AqarGate_Property_Payment();
<?php get_header(); ?>
<style>
    .dashboard-content {
        padding: 20px;
    }

    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
    }

    .dashboard-table th,
    .dashboard-table td {
        
    }

    .dashboard-table th {
        
    }


    .dashboard-content .btn-primary {
        background-color: #2196F3;
        border-color: #03A9F4;
    }

    .dashboard-content .btn-primary:hover {
        background-color: #1672ba;
        border-color: #1672ba;
    }

    .pagination {
        margin-top: 20px;
        text-align: center;
    }

    .pagination a,
    .pagination span {
        margin: 0 5px;
        padding: 8px 16px;
        border: 4px solid #fff;
        color: #fff;
        text-decoration: none;
        background: #1f3864;
        box-shadow: 0px 0px 7px 3px #0000002b;
        border-radius: 4px;
        transition: box-shadow 0.5s linear;
    }

    .pagination .current {
        background-color: #fff;
        color: #1f3864;
        border: 4px solid #1f3864;
    }

    .pagination a:hover {
        box-shadow: none;
        background: #1f3864;
        border: 4px solid #fff;
        border-radius: 4px;
    }
    </style>
<?php 
global $wpdb, $paged, $current_user;

wp_get_current_user();
$user_id = $current_user->ID;
$select_packages_link = houzez_get_template_link('template/template-packages.php'); 
$table_name = $wpdb->prefix . 'property_requests';

// التحقق من تسجيل الدخول
// if ( !is_user_logged_in() ) {
//     wp_redirect( home_url() );
//     exit;
// }

// التحقق من الاشتراك في الباقة
$has_subscription = houzez_user_has_membership($user_id);

// تحديد عدد الطلبات لكل صفحة
$requests_per_page = 20;

// الحصول على الصفحة الحالية
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$offset = ($paged - 1) * $requests_per_page;

if ($has_subscription && is_user_logged_in()) {
    $post_statuses = ['publish', 'contracted', 'canceled'];
    $postStatus = implode(',', $post_statuses);
    // استرداد منشورات property_request التي حالتها "منشور"
    $published_post_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM $wpdb->posts WHERE post_type = 'property_request' AND post_status IN ('publish','contracted','canceled') ORDER BY ID DESC LIMIT %d, %d",
        $offset,
        $requests_per_page
    ));
    
    // استرداد تفاصيل الطلبات من الجدول المخصص بناءً على معرفات المنشورات المستردة
    $requests = [];
    if (!empty($published_post_ids)) {
        $placeholders = implode(',', array_fill(0, count($published_post_ids), '%d'));
        $requests = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE post_id IN ($placeholders) ORDER BY post_id DESC",
            ...$published_post_ids
        ));
    }

    // استرداد العدد الإجمالي للطلبات التي حالتها "منشور"
    $total_requests = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'property_request' AND post_status IN ('publish','contracted','canceled')"
    ));

    // حساب العدد الإجمالي للصفحات
    $total_pages = ceil($total_requests / $requests_per_page);

    $request_types = array(
        'sell' => 'شراء',
        'rent' => 'إيجار'
    );
    
    
    ?>
    <div class="dashboard-content container mt-5 pt-5 mb-5 pb-5">
        <h1 class="mb-5">الطلبات العقارية</h1>
        <?php if ( !empty($requests) ) : ?>
            <table class="dashboard-table table-lined table-hover responsive-table">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>نوع الطلب</th>
                        <th>نوع العقار</th>
                        <th>المدينة</th>
                        <th>المساحة</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                        <th>التفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $requests as $request ) :
                        $postStatus = get_post_status($request->post_id);
                        switch ($postStatus) {
                            case 'publish':
                                $status = '<span style="background-color: #ff9800; color: #fff; padding: 5px 10px; border-radius: 3px;">طلب جديد</span>';
                                break;
                            case 'contracted':
                            case 'canceled':
                                $status = '<span style="background-color: #28a745; color: #fff; padding: 5px 10px; border-radius: 3px;">تم التعاقد</span>';
                                break;    
                        }
                        ?>
                        <tr>
                            <td><?php echo esc_html($request->post_id); ?></td>
                            <td><?php echo esc_html($request_types[$request->property_request]); ?></td>
                            <td><?php echo esc_html($request->prop_type); ?></td>
                            <td><?php echo esc_html($request->city); ?></td>
                            <td><?php echo esc_html($request->land_area); ?></td>
                            <td><?php echo esc_html(number_format($request->price)); ?></td>
                            <td><?php echo $status; ?></td>
                            <td><a href="<?php echo get_permalink($request->post_id); ?>" class="btn btn-primary">عرض التفاصيل</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'total' => $total_pages,
                    'current' => $paged,
                    'format' => '?paged=%#%',
                    'show_all' => false,
                    'end_size' => 1,
                    'mid_size' => 2,
                    'prev_next' => true,
                    'prev_text' => __('« السابق'),
                    'next_text' => __('التالي »'),
                    'type' => 'plain',
                ));
                ?>
            </div>
        <?php else : ?>
            <p>لا توجد طلبات عقارات متاحة.</p>
        <?php endif; ?>
    </div>

    <?php
} else if( ! is_user_logged_in() ) {
    echo '<div class="dashboard-content container text-center mt-5 pt-5 mb-5 pb-5">';
        get_template_part('template-parts/dashboard/submit/partials/login-required-property');
    echo '</div>';
} else if ( ! $has_subscription ) {
    ?>
    <div class="dashboard-content container text-center mt-5 pt-5 mb-5 pb-5">
        <div style="border: 1px solid #1f3864;padding-top: 20px;background-color: #e5fde1;margin-bottom: 20px;border-radius: 10px;">
            <p>عزيزنا العميل للاطلاع على الطلبات العقارية يجب أن تكون مشتركًا في إحدى باقات العضوية.</p>
        </div>
        <p>للاشتراك في إحدى الباقات، يرجى الضغط على الزر التالي</p>
        <a href="<?php echo $select_packages_link; ?>" class="btn btn-primary">باقات العضوية</a>
    </div>
    <?php
}

get_footer();

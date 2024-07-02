<?php
/**
 * Template Name: User Dashboard Property Request
 */

 if ( !is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

get_header();

global $wpdb, $current_user, $paged;
wp_get_current_user();
$user_id = $current_user->ID;

$table_name = $wpdb->prefix . 'property_requests';

// تحديد عدد الطلبات لكل صفحة
$requests_per_page = 20;

// الحصول على الصفحة الحالية
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$offset = ($paged - 1) * $requests_per_page;

// استرداد منشورات property_request التي حالتها "منشور" للمستخدم الحالي
$published_post_ids = $wpdb->get_col($wpdb->prepare(
    "SELECT ID FROM $wpdb->posts WHERE post_type = 'property_request' AND post_status = 'publish' AND post_author = %d ORDER BY ID DESC LIMIT %d, %d",
    $user_id,
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
    "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'property_request' AND post_status = 'publish' AND post_author = %d",
    $user_id
));

// حساب العدد الإجمالي للصفحات
$total_pages = ceil($total_requests / $requests_per_page);

$request_types = array(
    'sell' => 'شراء',
    'rent' => 'إيجار'
);
?>
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
    border: 1px solid #b3cbf5;
    padding: 8px;
    text-align: center;
}

.dashboard-table th {
    background-color: #1f3864;
    font-weight: bold;
    color: #fff;
}

.dashboard-table tr:nth-child(even) {
    background-color: #d3e3ff;
}

.dashboard-table tr:hover {
    background-color: #d3e3ff;
}

.btn-primary{
    background: #1f3864;
    border: 4px solid #fff;
    box-shadow: 0px 0px 7px 3px #0000002b;
    border-radius: 4px;
    transition: box-shadow 0.5s linear;
}

.btn-primary:hover {
    box-shadow: none;
    background: #1f3864;
    border: 4px solid #fff;
    border-radius: 4px;
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

/* .pagination a:hover {
    box-shadow: none;
    background: #1f3864;
    border: 4px solid #fff;
    border-radius: 4px;
} */
</style>
<div class="dashboard-content container mt-5 pt-5 mb-5 pb-5">
    <h1 class="mb-5">طلباتي العقارية</h1>
    <?php if ( !empty($requests) ) : ?>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>نوع الطلب</th>
                    <th>نوع العقار</th>
                    <th>المدينة</th>
                    <th>المساحة</th>
                    <th>السعر</th>
                    <th>التفاصيل</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $requests as $request ) : ?>
                    <?php 
                    // تحقق من أن حالة المنشور هي "منشور"
                    if ( get_post_status($request->post_id) == 'publish' ) : ?>
                        <tr>
                            <td><?php echo esc_html($request->post_id); ?></td>
                            <td><?php echo esc_html($request_types[$request->property_request]); ?></td>
                            <td><?php echo esc_html($request->prop_type); ?></td>
                            <td><?php echo esc_html($request->city); ?></td>
                            <td><?php echo esc_html($request->land_area); ?></td>
                            <td><?php echo esc_html(number_format($request->price)); ?></td>
                            <td><a href="<?php echo get_permalink($request->post_id); ?>" class="btn btn-primary">عرض التفاصيل</a></td>
                        </tr>
                    <?php endif; ?>
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

<?php get_footer(); ?>
<?php
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        if (get_post_status() == 'publish') :
            global $wpdb;
            $table_name = $wpdb->prefix . 'property_requests';
            $post_id = get_the_ID();
            
            // استرداد تفاصيل الطلب من الجدول المخصص
            $request = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE post_id = %d",
                $post_id
            ));
            
            if ($request) :
                // ترجمات "نوع الطلب" و "طريقة الدفع"
                $request_types = array(
                    'sell' => 'شراء',
                    'rent' => 'إيجار'
                );

                $payment_methods = array(
                    'cash' => 'نقداً',
                    'mortgage' => 'تمويل عقاري'
                );
                $property_ages = array(
                    'new' => 'جديد',
                    'less-year' => 'اقل من سنة',
                    'year' => 'سنة',
                    'two-year' => 'سنتين',
                    'three-year' => 'ثلاث سنوات',
                    'four-year' => 'اربع سنوات',
                    'five-year' => 'خمس سنوات',
                    'six-year' => 'ست سنوات',
                    'seven-year' => 'سبع سنوات',
                    'eight-year' => 'ثمان سنوات',
                    'nine-year' => 'تسع سنوات',
                    'ten-year' => 'عشر سنوات',
                    'more-than-ten' => 'اكثر من عشر سنوات'
                );
?>

<style>
.dashboard-content {
    padding: 20px;
}

.details-table {
    width: 100%;
    border-collapse: separate;
}

.details-table th,
.details-table td {
    border: 1px solid #b3cbf5;
    padding: 8px;
    text-align: right;
}

.details-table th {
    background-color: #1f3864;
    font-weight: bold;
    color: #fff;
}

.details-table tr:nth-child(even) {
    background-color: #d3e3ff;
}

.details-table tr:hover {
    background-color: #d3e3ff;
}

.details-table .section-header {
    background-color: #1f3864;
    color: #fff;
    font-weight: bold;
    text-align: center;
}
h2.section-head {
    display: inline-block;
    background: #1f3864;
    color: #fff;
    padding: 10px 10px;
    margin: 20px 0;
    font-size: 21px;
}
</style>

<div class="dashboard-content container">
    <h1>تفاصيل الطلب رقم <?php echo esc_html($request->post_id); ?></h1>
    <h2 class="section-head">بيانات العقار</h2>
    <table class="details-table">
        <thead>
        </thead>
        <tbody>
            <tr>
                <th>رقم الطلب</th>
                <td><?php echo esc_html($request->post_id); ?></td>
                <th>نوع الطلب</th>
                <td><?php echo esc_html($request_types[$request->property_request]); ?></td>
                <th>نوع العقار</th>
                <td><?php echo esc_html($request->prop_type); ?></td>
            </tr>
            <tr>
                <th>المنطقة</th>
                <td><?php echo esc_html($request->state); ?></td>
                <th>المدينة</th>
                <td><?php echo esc_html($request->city); ?></td>
                <th>الحي</th>
                <td><?php echo esc_html($request->area); ?></td>
            </tr>
            <tr>
                <th>المساحة</th>
                <td><?php echo esc_html($request->land_area); ?></td>
                <th>السعر</th>
                <td><?php echo esc_html(number_format($request->price)); ?></td>
                <th>عمر العقار</th>
                <td><?php echo esc_html($property_ages[$request->prop_age]); ?></td>
            </tr>
            <tr>
                <th colspan="1">طريقة الدفع</th>
                <td colspan="5"><?php echo esc_html($payment_methods[$request->payment_method]); ?></td>
            </tr>
            <tr>
                <th colspan="1">تفاصيل أخرى</th>
                <td colspan="5"><?php echo esc_html($request->more_info); ?></td>
            </tr>
        </tbody>
    </table>
    <h2 class="section-head">بيانات طالب العقار</h2>
    <table class="details-table text-center">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>البريد الإلكتروني</th>
                <th>رقم الجوال</th>
                <th>رقم الواتساب</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $user_info = get_userdata($request->user_id);
            ?>
            <tr>
                <td><?php echo esc_html($user_info->display_name); ?></td>
                <td><?php echo esc_html($user_info->user_email); ?></td>               
                <td dir="ltr"><?php echo esc_html(get_user_meta($request->user_id, 'fave_author_mobile', true)); ?></td>                
                <td><?php echo esc_html(get_user_meta($request->user_id, 'fave_author_whatsapp', true)); ?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php
            else :
                echo '<p>عذرًا، هذا الطلب غير متاح.</p>';
            endif;
        else :
            echo '<p>عذرًا، هذا الطلب غير متاح.</p>';
        endif;
    endwhile;
else :
    echo '<p>لا يوجد محتوى لعرضه</p>';
endif;

get_footer();
?>

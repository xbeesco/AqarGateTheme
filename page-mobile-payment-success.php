<?php
/**
 * Template Name: Mobile Payment Status
 * Description: صفحة موحدة لعرض حالة الدفع (نجاح / فشل) من تطبيق الموبايل.
 */

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$status   = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$order    = $order_id ? wc_get_order($order_id) : false;

function render_status_icon($status) {
    if ($status === 'success') {
        return '<div style="width: 100px; height: 100px; margin: 0 auto 20px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 10px rgba(0,0,0,0.1);"><span style="font-size: 50px; color: white;">✔</span></div>';
    } elseif ($status === 'failed') {
        return '<div style="width: 100px; height: 100px; margin: 0 auto 20px; background-color: #f44336; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 10px rgba(0,0,0,0.1);"><span style="font-size: 50px; color: white;">✖</span></div>';
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>بوابة العقار - حالة الدفع</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" id="bootstrap-css" href="<?php echo get_parent_theme_file_uri('css/bootstrap.min.css'); ?>" type="text/css" media="all">
    <style>
        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container" style="padding: 60px 15px;">
        <div class="payment-status"
            style="max-width: 600px; margin: 0 auto; padding: 30px; border: 2px solid #ccc; background: #fff; border-radius: 12px; text-align: center;">
            <?php if ($order && in_array($status, ['success', 'failed'])): ?>
                <div style="margin-bottom: 20px;">
                    <?php echo render_status_icon($status); ?>
                    <h2 style="color: <?php echo ($status === 'success') ? '#2e7d32' : '#c62828'; ?>; margin: 0;">
                        <?php echo ($status === 'success') ? 'تم الدفع بنجاح' : 'فشل في عملية الدفع'; ?>
                    </h2>
                </div>
                <p><?php echo ($status === 'success') ? 'شكراً لك! تم استلام طلبك بنجاح.' : 'نأسف، لم يتم إتمام عملية الدفع.'; ?></p>
                <hr style="margin: 20px 0;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">رقم الطلب</td>
                        <td style="padding: 10px; border: 1px solid #ccc;">#<?php echo esc_html($order->get_id()); ?></td>
                    </tr>
                    <tr style="display: none">
                        <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">الاسم</td>
                        <td style="padding: 10px; border: 1px solid #ccc;">
                            <?php echo esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">المنتج</td>
                        <td style="padding: 10px; border: 1px solid #ccc;">
                            <?php
                            $items = $order->get_items();
                            $product_names = array();
                            foreach ($items as $item) {
                                $product_names[] = $item->get_name();
                            }
                            echo esc_html(implode(', ', $product_names));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">المبلغ الإجمالي</td>
                        <td style="padding: 10px; border: 1px solid #ccc;"><?php echo wc_price($order->get_total()); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">حالة الطلب</td>
                        <td style="padding: 10px; border: 1px solid #ccc;"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></td>
                    </tr>
                </table>
            <?php else: ?>
                <h2 style="color: red;">بيانات غير صحيحة</h2>
                <p>لا يمكن عرض حالة الطلب. تأكد من صحة الرابط أو رقم الطلب.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

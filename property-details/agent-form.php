<?php
/**
 * Property Agent Contact Form (Override)
 *
 * ========================================
 * REGA COMPLIANCE - STRICT POLICY
 * ========================================
 *
 * السياسة الصارمة وفقاً لتعليمات الهيئة العامة للعقار:
 *
 * ✓ إذا كان العقار مرخص → عرض معلومات مسؤول الإعلان من الرخصة فقط
 * ✗ إذا لم يكن العقار مرخص → لا يتم عرض أي معلومات اتصال نهائياً
 *
 * "يجب أن يظهر في الإعلان المعروض لديكم: رقم مسؤول الإعلان المسجل في رخصة الإعلان فقط"
 * "أهمية إخفاء أرقام التواصل المضافة من قبل المعلنين على صفحتهم بمنصتكم"
 */

global $post;

// التحقق من وجود رخصة إعلان ومعلومات مسؤول الإعلان
$adLicenseNumber = get_post_meta( get_the_ID(), 'adLicenseNumber', true );
$responsibleEmployeeName = get_post_meta( get_the_ID(), 'responsibleEmployeeName', true );
$responsibleEmployeePhoneNumber = get_post_meta( get_the_ID(), 'responsibleEmployeePhoneNumber', true );

// ============================================================================
// STRICT POLICY: عرض معلومات الرخصة فقط أو لا شيء
// ============================================================================

if( !empty( $adLicenseNumber ) && !empty( $responsibleEmployeePhoneNumber ) ) {
    // العقار مرخص - عرض معلومات مسؤول الإعلان من الرخصة فقط

    $phone_clean = str_replace(array('(',')',' ','-'),'', $responsibleEmployeePhoneNumber);
    $whatsapp_text = urlencode(houzez_option('spl_con_interested', "Hello, I am interested in").' ['.get_the_title().'] '.get_permalink());
    ?>

    <div class="property-form-wrap">
        <div class="property-form clearfix">
            <!-- معلومات التواصل من الرخصة -->
            <div class="agent-details" style="padding: 15px; background: #f8f9fa; border-radius: 6px; margin-bottom: 15px; text-align: center;">
                <h4 style="margin-bottom: 10px; color: #333; font-size: 16px; font-weight: 600;">معلومات التواصل</h4>

                <?php if (!empty($responsibleEmployeeName)) { ?>
                <div style="margin-bottom: 8px;">
                    <i class="houzez-icon icon-single-neutral mr-1"></i>
                    <strong style="color: #666;">مسؤول الإعلان:</strong>
                    <span style="color: #333;"><?php echo esc_html($responsibleEmployeeName); ?></span>
                </div>
                <?php } ?>

                <div style="margin-bottom: 8px;">
                    <i class="houzez-icon icon-phone mr-1"></i>
                    <strong style="color: #666;">رقم مسؤول الإعلان:</strong>
                    <span style="color: #00aaef; font-size: 15px; font-weight: 500;">
                        <?php echo esc_html($responsibleEmployeePhoneNumber); ?>
                    </span>
                </div>

                <p style="font-size: 11px; color: #999; margin-top: 12px; margin-bottom: 0; line-height: 1.4;">
                    <i class="houzez-icon icon-common-file-double-1 mr-1"></i>
                    معلومات التواصل المسجلة في رخصة الإعلان رقم: <?php echo esc_html($adLicenseNumber); ?><br>
                    وفقاً لتعليمات الهيئة العامة للعقار
                </p>
            </div>

            <!-- أزرار الاتصال -->
            <div class="form-group" style="margin-bottom: 0;">
                <a href="tel:<?php echo esc_attr($phone_clean); ?>"
                   data-property-id="<?php echo intval($post->ID); ?>"
                   class="btn hz-btn-call btn-secondary btn-full-width"
                   style="margin-bottom: 10px;">
                    <i class="houzez-icon icon-phone mr-1"></i>
                    <span class="hide-on-click"><?php echo houzez_option('spl_btn_call', 'اتصال'); ?></span>
                    <span class="show-on-click"><?php echo esc_attr($responsibleEmployeePhoneNumber); ?></span>
                </a>

                <a target="_blank"
                   href="https://api.whatsapp.com/send?phone=<?php echo esc_attr($phone_clean); ?>&text=<?php echo $whatsapp_text; ?>"
                   data-property-id="<?php echo intval($post->ID); ?>"
                   class="btn btn-secondary-outlined btn-full-width hz-btn-whatsapp">
                    <i class="houzez-icon icon-messaging-whatsapp mr-1"></i>
                    واتساب
                </a>
            </div>
        </div>
    </div>

    <?php
    return; // إنهاء العرض - تم عرض معلومات الرخصة فقط

} else {
    // ============================================================================
    // العقار غير مرخص أو لا يحتوي على معلومات مسؤول الإعلان
    // السياسة الصارمة: لا نعرض أي معلومات اتصال نهائياً
    //
    // هذا يحدث فقط للإعلانات القديمة التي لم يتم تحديثها بمعلومات الرخصة الجديدة
    // ============================================================================

    // لا يتم عرض أي شيء - تم إلغاء النموذج القديم نهائياً
    return;
}
?>

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
    // العقار غير مرخص - عرض النموذج الكامل بدون معلومات اتصال مسبقة
    // ============================================================================

    global $current_user, $ele_settings;
    $return_array = houzez20_property_contact_form();
    if(empty($return_array)) {
        return;
    }

    $agent_info = isset($ele_settings['agent_detail']) ? $ele_settings['agent_detail'] : 'yes';
    $terms_page_id = houzez_option('terms_condition');
    $terms_page_id = apply_filters( 'wpml_object_id', $terms_page_id, 'page', true );
    $hide_form_fields = houzez_option('hide_prop_contact_form_fields');
    $gdpr_checkbox = houzez_option('gdpr_hide_checkbox', 1);
    $agent_display = houzez_get_listing_data('agent_display_option');
    $property_id = houzez_get_listing_data('property_id');

    $user_name = $user_email = '';
    if(!houzez_is_admin()) {
        $user_name =  $current_user->display_name;
        $user_email =  $current_user->user_email;
    }

    $agent_email = is_email( $return_array['agent_email'] );

    if ($agent_email && $agent_display != 'none') {
    ?>
    <div class="property-form-wrap">
        <div class="property-form clearfix">
            <form method="post" action="#">

                <?php
                // عرض معلومات الوسيط من الشريط الجانبي (يأتي من ag_helpers.php)
                if( $agent_info == 'yes' ) {
                    echo $return_array['agent_data'];
                }?>

                <?php if( $hide_form_fields['name'] != 1 ) { ?>
                <div class="form-group">
                    <input class="form-control" name="name" value="<?php echo esc_attr($user_name); ?>" type="text" placeholder="<?php echo houzez_option('spl_con_name', 'Name'); ?>">
                </div>
                <?php } ?>

                <?php if( $hide_form_fields['phone'] != 1 ) { ?>
                <div class="form-group">
                    <input class="form-control" name="mobile" value="" type="text" placeholder="<?php echo houzez_option('spl_con_phone', 'Phone'); ?>">
                </div>
                <?php } ?>

                <div class="form-group">
                    <input class="form-control" name="email" value="<?php echo esc_attr($user_email); ?>" type="email" placeholder="<?php echo houzez_option('spl_con_email', 'Email'); ?>">
                </div>

                <?php if( $hide_form_fields['message'] != 1 ) { ?>
                <div class="form-group form-group-textarea">
                    <textarea class="form-control hz-form-message" name="message" rows="4" placeholder="<?php echo houzez_option('spl_con_message', 'Message'); ?>"><?php echo houzez_option('spl_con_interested', "Hello, I am interested in"); ?> [<?php echo get_the_title(); ?>]</textarea>
                </div>
                <?php } ?>

                <?php if( $hide_form_fields['usertype'] != 1 ) { ?>
                <div class="form-group">
                    <select name="user_type" class="selectpicker form-control bs-select-hidden" title="<?php echo houzez_option('spl_con_select', 'Select'); ?>">
                        <?php if( houzez_option('spl_con_buyer') != "" ) { ?>
                        <option value="buyer"><?php echo houzez_option('spl_con_buyer', "I'm a buyer"); ?></option>
                        <?php } ?>

                        <?php if( houzez_option('spl_con_tennant') != "" ) { ?>
                        <option value="tennant"><?php echo houzez_option('spl_con_tennant', "I'm a tennant"); ?></option>
                        <?php } ?>

                        <?php if( houzez_option('spl_con_agent') != "" ) { ?>
                        <option value="agent"><?php echo houzez_option('spl_con_agent', "I'm an agent"); ?></option>
                        <?php } ?>

                        <?php if( houzez_option('spl_con_other') != "" ) { ?>
                        <option value="other"><?php echo houzez_option('spl_con_other', 'Other'); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>

                <?php do_action('houzez_property_agent_contact_fields'); ?>

                <?php if( houzez_option('gdpr_and_terms_checkbox', 1) ) { ?>
                <div class="form-group">
                    <label class="control control--checkbox m-0 hz-terms-of-use <?php if( $gdpr_checkbox ){ echo 'hz-no-gdpr-checkbox';}?>">
                        <?php if( ! $gdpr_checkbox ) { ?>
                        <input type="checkbox" name="privacy_policy">
                        <span class="control__indicator"></span>
                        <?php } ?>
                        <div class="gdpr-text-wrap">
                            <?php echo houzez_option('spl_sub_agree', 'By submitting this form I agree to'); ?> <a target="_blank" href="<?php echo esc_url(get_permalink($terms_page_id)); ?>"><?php echo houzez_option('spl_term', 'Terms of Use'); ?></a>
                        </div>
                    </label>
                </div>
                <?php } ?>

                <input type="hidden" name="property_agent_contact_security" value="<?php echo wp_create_nonce('property_agent_contact_nonce'); ?>"/>
                <input type="hidden" name="property_permalink" value="<?php echo esc_url(get_permalink($post->ID)); ?>"/>
                <input type="hidden" name="property_title" value="<?php echo esc_attr(get_the_title($post->ID)); ?>"/>
                <input type="hidden" name="property_id" value="<?php echo esc_attr($property_id); ?>"/>
                <input type="hidden" name="action" value="houzez_property_agent_contact">
                <input type="hidden" name="listing_id" value="<?php echo intval($post->ID)?>">
                <input type="hidden" name="is_listing_form" value="yes">
                <input type="hidden" name="agent_id" value="<?php echo intval($return_array['agent_id'])?>">
                <input type="hidden" name="agent_type" value="<?php echo esc_attr($return_array['agent_type'])?>">

                <?php get_template_part('template-parts/google', 'reCaptcha'); ?>
                <div class="form_messages"></div>
                <button type="button" class="houzez-ele-button houzez_agent_property_form btn btn-secondary btn-full-width">
                    <?php get_template_part('template-parts/loader'); ?>
                    <?php echo houzez_option('spl_btn_send', 'Send Email'); ?>
                </button>
            </form>
        </div>
    </div>
    <?php
    } // end if agent_email
} // end else
?>

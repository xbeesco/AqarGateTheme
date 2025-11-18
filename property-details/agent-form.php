<?php
/**
 * Property Agent Contact Form (Override for REGA)
 *
 * This template maintains the original design but replaces phone/whatsapp numbers
 * with responsible employee information from REGA license when available.
 */

global $post, $current_user, $ele_settings;
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

// ============================================================================
// REGA Integration: Use responsible employee phone if available
// ============================================================================
$responsibleEmployeeName = get_post_meta( get_the_ID(), 'responsibleEmployeeName', true );
$responsibleEmployeePhoneNumber = get_post_meta( get_the_ID(), 'responsibleEmployeePhoneNumber', true );
$adLicenseNumber = get_post_meta( get_the_ID(), 'adLicenseNumber', true );

// Determine which phone number to use
if( !empty($adLicenseNumber) && !empty($responsibleEmployeePhoneNumber) ) {
	// Use responsible employee phone from REGA license
	$agent_number = $responsibleEmployeePhoneNumber;
	$agent_whatsapp_call = str_replace(array('(',')',' ','-'),'', $responsibleEmployeePhoneNumber);
	$agent_mobile_call = str_replace(array('(',')',' ','-'),'', $responsibleEmployeePhoneNumber);
	$display_name = !empty($responsibleEmployeeName) ? $responsibleEmployeeName : $return_array['agent_name'];
} else {
	// Use regular agent phone
	$agent_number = $return_array['agent_mobile'];
	$agent_whatsapp_call = $return_array['agent_whatsapp_call'];
	$agent_mobile_call = $return_array['agent_mobile_call'];
	$display_name = $return_array['agent_name'];

	if( empty($agent_number) ) {
		$agent_number = $return_array['agent_phone'];
		$agent_mobile_call = $return_array['agent_phone_call'];
	}
}
// ============================================================================

// Override agent_data to show REGA responsible employee info
if( !empty($adLicenseNumber) && !empty($responsibleEmployeePhoneNumber) ) {
    // Build custom agent data HTML with REGA info
    $custom_agent_data = '<div class="property-agent-info clearfix">';
    
    // Agent/Agency image (keep original)
    if (!empty($return_array['picture'])) {
        $custom_agent_data .= '<div class="agent-image">';
        $custom_agent_data .= '<img src="' . esc_url($return_array['picture']) . '" alt="' . esc_attr($display_name) . '" width="150" height="150">';
        $custom_agent_data .= '</div>';
    }
    
    $custom_agent_data .= '<div class="agent-details">';
    $custom_agent_data .= '<h3 class="agent-name">' . esc_html($display_name) . '</h3>';
    
    // Show position/company if available (from original agent)
    if (!empty($return_array['agent_position'])) {
        $custom_agent_data .= '<div class="agent-position">' . esc_html($return_array['agent_position']) . '</div>';
    }
    if (!empty($return_array['agent_company'])) {
        $custom_agent_data .= '<div class="agent-company">' . esc_html($return_array['agent_company']) . '</div>';
    }
    
    // Show REGA phone number
    $custom_agent_data .= '<div class="agent-phone">';
    $custom_agent_data .= '<i class="houzez-icon icon-phone mr-1"></i>';
    $custom_agent_data .= '<a href="tel:' . esc_attr($agent_mobile_call) . '">' . esc_html($responsibleEmployeePhoneNumber) . '</a>';
    $custom_agent_data .= '</div>';
    
    // Show ad license number
    if (!empty($adLicenseNumber)) {
        $custom_agent_data .= '<div class="agent-license text-muted small mt-2">';
        $custom_agent_data .= '<i class="houzez-icon icon-certificate-streamline mr-1"></i>';
        $custom_agent_data .= 'رقم الترخيص: ' . esc_html($adLicenseNumber);
        $custom_agent_data .= '</div>';
    }
    
    $custom_agent_data .= '</div>'; // .agent-details
    $custom_agent_data .= '</div>'; // .property-agent-info
    
    // Override the agent_data in return_array
    $return_array['agent_data'] = $custom_agent_data;
}

$user_name = $user_email = '';
if(!houzez_is_admin()) {
	$user_name =  $current_user->display_name;
	$user_email =  $current_user->user_email;
}

// زر الإرسال يكون دائماً بالعرض الكامل
$send_btn_class = 'btn-full-width';

$action_class = "houzez-send-message";
$login_class = '';
$dataModel = '';
if( !is_user_logged_in() ) {
	$action_class = '';
	$login_class = 'msg-login-required';
	$dataModel = 'data-toggle="modal" data-target="#login-register-form"';
}

$agent_email = is_email( $return_array['agent_email'] );

$agent_mobile_num = houzez_option('agent_mobile_num', 1 );
$agent_whatsapp_num = houzez_option('agent_whatsapp_num', 1);

// زر الاتصال والواتساب يكونان جنب بعض بنصف العرض
$whatsappBtnClass = "hz-btn-whatsapp btn-half-width mt-10";
$messageBtnClass = "btn-full-width mt-10";

if ($agent_email && $agent_display != 'none') {
?>
<div class="property-form-wrap">

	<?php
	if(houzez_form_type()) {

		if( $agent_info == 'yes' ) {
			echo $return_array['agent_data'];
		}

		if(!empty(houzez_option('contact_form_agent_above_image'))) {
			echo do_shortcode(houzez_option('contact_form_agent_above_image'));
		}

	} else { ?>
		<div class="property-form clearfix">
			<form method="post" action="#">

				<?php
				if( $agent_info == 'yes' ) {
					echo $return_array['agent_data'];
				}?>

				<?php if( $hide_form_fields['name'] != 1 ) { ?>
				<div class="form-group">
					<input class="form-control" name="name" value="<?php echo esc_attr($user_name); ?>" type="text" placeholder="<?php echo houzez_option('spl_con_name', 'Name'); ?>">
				</div><!-- form-group -->
				<?php } ?>

				<?php if( $hide_form_fields['phone'] != 1 ) { ?>
				<div class="form-group">
					<input class="form-control" name="mobile" value="" type="text" placeholder="<?php echo houzez_option('spl_con_phone', 'Phone'); ?>">
				</div><!-- form-group -->
				<?php } ?>

				<div class="form-group">
					<input class="form-control" name="email" value="<?php echo esc_attr($user_email); ?>" type="email" placeholder="<?php echo houzez_option('spl_con_email', 'Email'); ?>">
				</div><!-- form-group -->

				<?php if( $hide_form_fields['message'] != 1 ) { ?>
				<div class="form-group form-group-textarea">
					<textarea class="form-control hz-form-message" name="message" rows="4" placeholder="<?php echo houzez_option('spl_con_message', 'Message'); ?>"><?php echo houzez_option('spl_con_interested', "Hello, I am interested in"); ?> [<?php echo get_the_title(); ?>]</textarea>
				</div><!-- form-group -->
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
					</select><!-- selectpicker -->
				</div><!-- form-group -->
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
				</div><!-- form-group -->
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
				<button type="button" class="houzez-ele-button houzez_agent_property_form btn btn-secondary <?php echo esc_attr($send_btn_class); ?>">
					<?php get_template_part('template-parts/loader'); ?>
					<?php echo houzez_option('spl_btn_send', 'Send Email'); ?>

				</button>

				<?php
				// عرض أزرار الاتصال والواتساب فقط إذا كان هناك رقم موثق من REGA
				if ( $return_array['is_single_agent'] == true && !empty($adLicenseNumber) && !empty($responsibleEmployeePhoneNumber) && !wp_is_mobile() ) :
				?>
					<?php if( $agent_mobile_num ) : ?>
					<a href="tel:<?php echo esc_attr($agent_mobile_call); ?>" data-property-id="<?php echo intval($post->ID); ?>" data-agent-id="<?php echo intval($return_array['agent_id'])?>" class="btn hz-btn-call btn-secondary-outlined btn-half-width mt-10">
						<span class="hide-on-click"><?php echo houzez_option('spl_btn_call', 'Call'); ?></span>
						<span class="show-on-click"><?php echo esc_attr($agent_number); ?></span>
					</a>
					<?php endif; ?>

					<?php if( $agent_whatsapp_num ) : ?>
					<a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo esc_attr( $agent_whatsapp_call ); ?>&text=<?php echo urlencode(houzez_option('spl_con_interested', "Hello, I am interested in").' ['.get_the_title().'] '.get_permalink()); ?>" data-property-id="<?php echo intval($post->ID); ?>" data-agent-id="<?php echo intval($return_array['agent_id'])?>" class="btn btn-secondary-outlined <?php echo esc_attr($whatsappBtnClass); ?>"><i class="houzez-icon icon-messaging-whatsapp mr-1"></i> <?php esc_html_e('WhatsApp', 'houzez'); ?></a>
					<?php endif; ?>
				<?php endif; ?>

				<?php if( $return_array['is_single_agent'] == true && houzez_option('agent_direct_messages', 0) ) { ?>
				<button type="button" <?php echo $dataModel; ?> class="<?php echo esc_attr($action_class).' '.esc_attr($login_class); ?> btn btn-secondary-outlined <?php echo esc_attr($messageBtnClass); ?>">
					<?php get_template_part('template-parts/loader'); ?>
					<?php echo houzez_option('spl_btn_message', 'Send Message'); ?>
				</button>
				<?php } ?>
			</form>
		</div><!-- property-form -->

	<?php } ?>
</div><!-- property-form-wrap -->
<?php } ?>

<?php
global $current_user;
wp_get_current_user();
$userID  = $current_user->ID;
check_ajax_referer( 'aqargate_profile_ajax_nonce', 'aqargate-security-profile' );

$agent_id =  $firstname =  $lastname =  $title =  $about =  $userphone =  $usermobile =  $whatsapp =  $userskype =  $facebook =  $twitter =  $linkedin =  $instagram =  $pinterest =  $youtube =  $vimeo =  $googleplus =  $profile_pic =  $profile_pic_id =  $website =  $useremail =  $license =  $tax_number =  $fax_number =  $userlangs =  $user_address =  $user_company =  $service_areas =  $specialties =  $tiktok =  $telegram = '';

// Update first name
if ( !empty( $_POST['firstname'] ) ) {
    $firstname = sanitize_text_field( $_POST['firstname'] );
    update_user_meta( $userID, 'first_name', $firstname );
} else {
    delete_user_meta( $userID, 'first_name' );
}

// Update GDPR
if ( !empty( $_POST['gdpr_agreement'] ) ) {
    $gdpr_agreement = sanitize_text_field( $_POST['gdpr_agreement'] );
    update_user_meta( $userID, 'gdpr_agreement', $gdpr_agreement );
} else {
    delete_user_meta( $userID, 'gdpr_agreement' );
}

// Update last name
if ( !empty( $_POST['lastname'] ) ) {
    $lastname = sanitize_text_field( $_POST['lastname'] );
    update_user_meta( $userID, 'last_name', $lastname );
} else {
    delete_user_meta( $userID, 'last_name' );
}

// Update Language
if ( !empty( $_POST['userlangs'] ) ) {
    $userlangs = sanitize_text_field( $_POST['userlangs'] );
    update_user_meta( $userID, 'fave_author_language', $userlangs );
} else {
    delete_user_meta( $userID, 'fave_author_language' );
}


// Update user_company
if ( !empty( $_POST['user_company'] ) ) {
    $agency_id = get_user_meta($userID, 'fave_author_agency_id', true);
    $user_company = sanitize_text_field( $_POST['user_company'] );
    if( !empty($agency_id) ) {
        $user_company = get_the_title($agency_id);
    }
    update_user_meta( $userID, 'fave_author_company', $user_company );
} else {
    $agency_id = get_user_meta($userID, 'fave_author_agency_id', true);
    if( !empty($agency_id) ) {
        update_user_meta( $userID, 'fave_author_company', get_the_title($agency_id) );
    } else {
        delete_user_meta($userID, 'fave_author_company');
    }
}

// Update Title
if ( !empty( $_POST['title'] ) ) {
    $title = sanitize_text_field( $_POST['title'] );
    update_user_meta( $userID, 'fave_author_title', $title );
} else {
    delete_user_meta( $userID, 'fave_author_title' );
}

// Update About
if ( !empty( $_POST['about'] ) ) {
    $about = wp_kses_post(  wpautop( wptexturize( $_POST['about'] ) ) );
    update_user_meta( $userID, 'description', $about );
} else {
    delete_user_meta( $userID, 'description' );
}

// Update Phone
if ( !empty( $_POST['userphone'] ) ) {
    $userphone = sanitize_text_field( $_POST['userphone'] );
    update_user_meta( $userID, 'fave_author_phone', $userphone );
} else {
    delete_user_meta( $userID, 'fave_author_phone' );
}

// Update Fax
if ( !empty( $_POST['fax_number'] ) ) {
    $fax_number = sanitize_text_field( $_POST['fax_number'] );
    update_user_meta( $userID, 'fave_author_fax', $fax_number );
} else {
    delete_user_meta( $userID, 'fave_author_fax' );
}

// Update id number
if ( !empty( $_POST['id_number'] ) ) {
    $id_number = sanitize_text_field( $_POST['id_number'] );
    update_user_meta( $userID, 'aqar_author_id_number', $id_number );
} else {
    delete_user_meta( $userID, 'aqar_author_id_number' );
}

// Update ad number
if ( !empty( $_POST['ad_number'] ) ) {
    $ad_number = sanitize_text_field( $_POST['ad_number'] );
    update_user_meta( $userID, 'aqar_author_ad_number', $ad_number );
} else {
    delete_user_meta( $userID, 'aqar_author_ad_number' );
}

if( !empty( $_POST['brokerage_license_number'] ) ){
    $brokerage_license_number = $_POST['brokerage_license_number'];
    update_user_meta( $userID, 'brokerage_license_number', $brokerage_license_number );
}else{
    delete_user_meta( $userID, 'brokerage_license_number' ); 
}

if( !empty( $_POST['license_expiration_date'] ) ){
    $license_expiration_date = $_POST['license_expiration_date'];
    update_user_meta( $userID, 'license_expiration_date', $license_expiration_date );
}else{
    delete_user_meta( $userID, 'license_expiration_date' ); 
}

if( !empty( $_POST['aqar_state'] ) ){
    $aqar_state = $_POST['aqar_state'];
    update_user_meta( $userID, 'aqar_state', $aqar_state );
}else{
    delete_user_meta( $userID, 'aqar_state' ); 
}
if( !empty( $_POST['aqar_city'] ) ){
    $aqar_city = $_POST['aqar_city'];
    update_user_meta( $userID, 'aqar_city', $aqar_city );
}else{
    delete_user_meta( $userID, 'aqar_city' ); 
}
if( !empty( $_POST['aqar_area'] ) ){
    $aqar_area = $_POST['aqar_area'];
    update_user_meta( $userID, 'aqar_area', $aqar_area );
}else{
    delete_user_meta( $userID, 'aqar_area' ); 
}
if( !empty( $_POST['aqar_zip'] ) ){
    $aqar_zip = $_POST['aqar_zip'];
    update_user_meta( $userID, 'aqar_zip', $aqar_zip );
}else{
    delete_user_meta( $userID, 'aqar_zip' ); 
}
if( !empty( $_POST['aqar_building_number'] ) ){
    $aqar_building_number = $_POST['aqar_building_number'];
    update_user_meta( $userID, 'aqar_building_number', $aqar_building_number );
}else{
    delete_user_meta( $userID, 'aqar_building_number' ); 
}

if( !empty( $_POST['aqar_additional_number'] ) ){
    $aqar_additional_number = $_POST['aqar_additional_number'];
    update_user_meta( $userID, 'aqar_additional_number', $aqar_additional_number );
}else{
    delete_user_meta( $userID, 'aqar_additional_number' ); 
}

if( !empty( $_POST['aqar_Shortcode'] ) ){
    $aqar_Shortcode = $_POST['aqar_Shortcode'];
    update_user_meta( $userID, 'aqar_Shortcode', $aqar_Shortcode );
}else{
    delete_user_meta( $userID, 'aqar_Shortcode' ); 
}
// type_id
if ( !empty( $_POST['aqar_author_type_id'] ) ) {
    $ad_number = sanitize_text_field( $_POST['aqar_author_type_id'] );
    update_user_meta( $userID, 'aqar_author_type_id', $ad_number );
} else {
    delete_user_meta( $userID, 'aqar_author_type_id' );
}
// fave_author_service_areas
if ( !empty( $_POST['service_areas'] ) ) {
    $service_areas = sanitize_text_field( $_POST['service_areas'] );
    update_user_meta( $userID, 'fave_author_service_areas', $service_areas );
} else {
    delete_user_meta( $userID, 'fave_author_service_areas' );
}

// Specialties
if ( !empty( $_POST['specialties'] ) ) {
    $specialties = sanitize_text_field( $_POST['specialties'] );
    update_user_meta( $userID, 'fave_author_specialties', $specialties );
} else {
    delete_user_meta( $userID, 'fave_author_specialties' );
}

// Update Mobile
if ( !empty( $_POST['usermobile'] ) ) {
    $usermobile = sanitize_text_field( $_POST['usermobile'] );
    update_user_meta( $userID, 'fave_author_mobile', $usermobile );
} else {
    delete_user_meta( $userID, 'fave_author_mobile' );
}

// Update WhatsApp
if ( !empty( $_POST['whatsapp'] ) ) {
    $whatsapp = sanitize_text_field( $_POST['whatsapp'] );
    update_user_meta( $userID, 'fave_author_whatsapp', $whatsapp );
} else {
    delete_user_meta( $userID, 'fave_author_whatsapp' );
}

// Update Skype
if ( !empty( $_POST['userskype'] ) ) {
    $userskype = sanitize_text_field( $_POST['userskype'] );
    update_user_meta( $userID, 'fave_author_skype', $userskype );
} else {
    delete_user_meta( $userID, 'fave_author_skype' );
}

// Update facebook
if ( !empty( $_POST['facebook'] ) ) {
    $facebook = sanitize_text_field( $_POST['facebook'] );
    update_user_meta( $userID, 'fave_author_facebook', $facebook );
} else {
    delete_user_meta( $userID, 'fave_author_facebook' );
}

// Update twitter
if ( !empty( $_POST['twitter'] ) ) {
    $twitter = sanitize_text_field( $_POST['twitter'] );
    update_user_meta( $userID, 'fave_author_twitter', $twitter );
} else {
    delete_user_meta( $userID, 'fave_author_twitter' );
}

// Update linkedin
if ( !empty( $_POST['linkedin'] ) ) {
    $linkedin = sanitize_text_field( $_POST['linkedin'] );
    update_user_meta( $userID, 'fave_author_linkedin', $linkedin );
} else {
    delete_user_meta( $userID, 'fave_author_linkedin' );
}

// Update instagram
if ( !empty( $_POST['instagram'] ) ) {
    $instagram = sanitize_text_field( $_POST['instagram'] );
    update_user_meta( $userID, 'fave_author_instagram', $instagram );
} else {
    delete_user_meta( $userID, 'fave_author_instagram' );
}

// Update pinterest
if ( !empty( $_POST['pinterest'] ) ) {
    $pinterest = sanitize_text_field( $_POST['pinterest'] );
    update_user_meta( $userID, 'fave_author_pinterest', $pinterest );
} else {
    delete_user_meta( $userID, 'fave_author_pinterest' );
}

// Update youtube
if ( !empty( $_POST['youtube'] ) ) {
    $youtube = sanitize_text_field( $_POST['youtube'] );
    update_user_meta( $userID, 'fave_author_youtube', $youtube );
} else {
    delete_user_meta( $userID, 'fave_author_youtube' );
}

// Update vimeo
if ( !empty( $_POST['vimeo'] ) ) {
    $vimeo = sanitize_text_field( $_POST['vimeo'] );
    update_user_meta( $userID, 'fave_author_vimeo', $vimeo );
} else {
    delete_user_meta( $userID, 'fave_author_vimeo' );
}

// Update Googleplus
if ( !empty( $_POST['googleplus'] ) ) {
    $googleplus = sanitize_text_field( $_POST['googleplus'] );
    update_user_meta( $userID, 'fave_author_googleplus', $googleplus );
} else {
    delete_user_meta( $userID, 'fave_author_googleplus' );
}

// Update website
if ( !empty( $_POST['website'] ) ) {
    $website = sanitize_text_field( $_POST['website'] );
    wp_update_user( array( 'ID' => $userID, 'user_url' => $website ) );
} else {
    $website = '';
    wp_update_user( array( 'ID' => $userID, 'user_url' => $website ) );
}

//For agency Role

if ( !empty( $_POST['license'] ) ) {
    $license = sanitize_text_field( $_POST['license'] );
    update_user_meta( $userID, 'fave_author_license', $license );
} else {
    delete_user_meta( $userID, 'fave_author_license' );
}

if ( !empty( $_POST['tax_number'] ) ) {
    $tax_number = sanitize_text_field( $_POST['tax_number'] );
    update_user_meta( $userID, 'fave_author_tax_no', $tax_number );
} else {
    delete_user_meta( $userID, 'fave_author_tax_no' );
}

if ( !empty( $_POST['user_address'] ) ) {
    $user_address = sanitize_text_field( $_POST['user_address'] );
    update_user_meta( $userID, 'fave_author_address', $user_address );
} else {
    delete_user_meta( $userID, 'fave_author_address' );
}

if ( !empty( $_POST['user_location'] ) ) {
    $user_location = sanitize_text_field( $_POST['user_location'] );
    update_user_meta( $userID, 'fave_author_google_location', $user_location );
} else {
    delete_user_meta( $userID, 'fave_author_google_location' );
}

if ( !empty( $_POST['latitude'] ) ) {
    $latitude = sanitize_text_field( $_POST['latitude'] );
    update_user_meta( $userID, 'fave_author_google_latitude', $latitude );
} else {
    delete_user_meta( $userID, 'fave_author_google_latitude' );
}

if ( !empty( $_POST['longitude'] ) ) {
    $longitude = sanitize_text_field( $_POST['longitude'] );
    update_user_meta( $userID, 'fave_author_google_longitude', $longitude );
} else {
    delete_user_meta( $userID, 'fave_author_google_longitude' );
}

// Update email
if( !empty( $_POST['useremail'] ) ) {
    $useremail = sanitize_email( $_POST['useremail'] );
    $useremail = is_email( $useremail );
    if( !$useremail ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('The Email you entered is not valid. Please try again.', 'houzez') ) );
        wp_die();
    } else {
        $email_exists = email_exists( $useremail );
        if( $email_exists ) {
            if( $email_exists != $userID ) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('This Email is already used by another user. Please try a different one.', 'houzez') ) );
                wp_die();
            }
        } else {
            $return = wp_update_user( array ('ID' => $userID, 'user_email' => $useremail ) );
            if ( is_wp_error( $return ) ) {
                $error = $return->get_error_message();
                echo esc_attr( $error );
                wp_die();
            }
        }

        $profile_pic_id = isset($_POST['profile-pic-id'] ) ? intval( $_POST['profile-pic-id'] ) : '';

        $agent_id = get_user_meta( $userID, 'fave_author_agent_id', true );
        $agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );
        $user_as_agent = houzez_option('user_as_agent');

        if (in_array('houzez_agent', ( array ) $current_user->roles)) {
            houzez_update_user_agent ( $agent_id, $firstname, $lastname, $title, $about, $userphone, $usermobile, $whatsapp, $userskype, $facebook, $twitter, $linkedin, $instagram, $pinterest, $youtube, $vimeo, $googleplus, $profile_pic, $profile_pic_id, $website, $useremail, $license, $tax_number, $fax_number, $userlangs, $user_address, $user_company, $service_areas, $specialties, $tiktok, $telegram);
        } elseif(in_array('houzez_agency', (array)$current_user->roles)) {
            houzez_update_user_agency ( $agency_id, $firstname, $lastname, $title, $about, $userphone, $usermobile, $whatsapp, $userskype, $facebook, $twitter, $linkedin, $instagram, $pinterest, $youtube, $vimeo, $googleplus, $profile_pic, $profile_pic_id, $website, $useremail, $license, $tax_number, $user_address, $user_location, $latitude, $longitude, $fax_number, $userlangs );
        }

    }
}

// include_once ( AG_DIR.'classes/class-rega.php' );
// $valid_status = REGA::is_valid_ad( $prop_id='', $userID );
// wp_update_user( array ('ID' => $userID, 'display_name' => $_POST['display_name'] ) );

// if( $valid_status === true ){
//     update_user_meta( $userID, 'is_valid_ad', 'is_valid' );
// }else{
//     delete_user_meta( $userID, 'is_valid_ad' );
//     echo json_encode( array( 'success' => true, 'msg' => $valid_status ) );
//     die();
// }

wp_update_user( array ('ID' => $userID, 'display_name' => $_POST['display_name'] ) );
echo json_encode( array( 'success' => true, 'msg' => esc_html__('تم تحديث الملف الشخصي', 'houzez') ) );
die();
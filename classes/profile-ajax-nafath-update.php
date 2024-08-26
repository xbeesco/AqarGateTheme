<?php
global $current_user;
wp_get_current_user();
$userID  = $current_user->ID;
check_ajax_referer( 'aqargate_profile_ajax_nonce', 'aqargate-security-profile' );

$user_company = $userlangs = $latitude = $longitude = $tax_number = $user_location = $license = $user_address = $fax_number = $firstname = $lastname = $title = $about = $userphone = $usermobile = $userskype = $facebook = $tiktok = $telegram = $twitter = $linkedin = $instagram = $pinterest = $profile_pic = $profile_pic_id = $website = $useremail = $service_areas = $specialties = $whatsapp = $line_id = $zillow = $realtor_com = '';



$user_roles = array ( 'houzez_agency', 'houzez_agent', 'houzez_buyer', 'houzez_seller', 'houzez_owner', 'houzez_manager' );

if( isset( $_POST['role'] ) && $_POST['role'] != '' && in_array( $_POST['role'], $user_roles ) ) {
    $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
}


$firstname = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
$lastname = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
update_user_meta( $userID, 'first_name', $firstname);
update_user_meta( $userID, 'last_name', $lastname);

$phone_number = isset( $_POST['phone_number'] ) ? $_POST['phone_number'] : '';
if( $user_role == 'houzez_agency' ) {
    update_user_meta( $userID, 'fave_author_phone', $phone_number);
    update_user_meta( $userID, 'fave_author_mobile', $phone_number);
    update_user_meta( $userID, 'aqar_author_type_id', 2);
} else {
    update_user_meta( $userID, 'fave_author_mobile', $phone_number);
    update_user_meta( $userID, 'fave_author_mobile', $phone_number);
    update_user_meta( $userID, 'aqar_author_type_id', 1);
}

if ( !empty( $_POST['full_name'] ) ) {    
    wp_update_user( array (
        'ID' => $userID, 
        'display_name' => $_POST['full_name'],
    ));
    update_user_meta( $userID, 'display_name', $_POST['full_name'] );
}

// Update first name
if ( !empty( $_POST['firstname'] ) ) {
    $firstname = sanitize_text_field( $_POST['firstname'] );
    update_user_meta( $userID, 'first_name', $firstname );
} 

// Update last name
if ( !empty( $_POST['lastname'] ) ) {
    $lastname = sanitize_text_field( $_POST['lastname'] );
    update_user_meta( $userID, 'last_name', $lastname );
} 

// Update id number
if ( !empty( $_POST['id_number'] ) ) {
    $id_number = sanitize_text_field( $_POST['id_number'] );
    update_user_meta( $userID, 'aqar_author_id_number', $id_number );
} 



if( !empty( $_POST['brokerage_license_number'] ) ){
    $brokerage_license_number = $_POST['brokerage_license_number'];
    update_user_meta( $userID, 'brokerage_license_number', $brokerage_license_number );
}

if( !empty( $_POST['license_expiration_date'] ) ){
    $license_expiration_date = $_POST['license_expiration_date'];
    update_user_meta( $userID, 'license_expiration_date', $license_expiration_date );
}else{
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('تاريخ انتهاء الرخصة خالية', 'houzez') ) );
    wp_die();
    delete_user_meta( $userID, 'license_expiration_date' ); 
}


// Update Mobile
if ( !empty( $_POST['usermobile'] ) ) {
    $usermobile = sanitize_text_field( $_POST['usermobile'] );
    update_user_meta( $userID, 'fave_author_mobile', $usermobile );
} 




//For agency Role

if ( !empty( $_POST['license'] ) ) {
    $license = sanitize_text_field( $_POST['license'] );
    update_user_meta( $userID, 'fave_author_license', $license );
} 

if ( !empty( $_POST['tax_number'] ) ) {
    $tax_number = sanitize_text_field( $_POST['tax_number'] );
    update_user_meta( $userID, 'fave_author_tax_no', $tax_number );
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

        if (in_array('houzez_agent', (array)$current_user->roles)) {
            houzez_update_user_agent ( $agent_id, $firstname, $lastname, $title, $about, $userphone, $usermobile, $whatsapp, $userskype, $facebook, $twitter, $linkedin, $instagram, $pinterest, $youtube, $vimeo, $googleplus, $profile_pic, $profile_pic_id, $website, $useremail, $license, $tax_number, $fax_number, $userlangs, $user_address, $user_company, $service_areas, $specialties, $tiktok, $telegram, $line_id, $zillow, $realtor_com );

        } elseif(in_array('houzez_agency', (array)$current_user->roles)) {
            houzez_update_user_agency ( $agency_id, $firstname, $lastname, $title, $about, $userphone, $usermobile, $whatsapp, $userskype, $facebook, $twitter, $linkedin, $instagram, $pinterest, $youtube, $vimeo, $googleplus, $profile_pic, $profile_pic_id, $website, $useremail, $license, $tax_number, $user_address, $user_location, $latitude, $longitude, $fax_number, $userlangs, $tiktok, $telegram, $line_id, $service_areas, $specialties, $zillow, $realtor_com );
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

echo json_encode( array( 'success' => true, 'msg' => esc_html__('شكرا لك , تم تحديث البيانات وجاري اعادة التوجية', 'houzez') ) );
die();
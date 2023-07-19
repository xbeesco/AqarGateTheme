<?php

check_ajax_referer('AqarGat_register_nonce', 'AqarGat_register_security');

$allowed_html = array();

$usermane          = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));
$email             = trim( sanitize_text_field( wp_kses( $_POST['useremail'], $allowed_html ) ));
$term_condition    = wp_kses( $_POST['term_condition'], $allowed_html );
$enable_password = houzez_option('enable_password');
$response = isset($_POST["g-recaptcha-response"]) ? $_POST["g-recaptcha-response"] : '';


/* -------------------------------------------------------------------------- */
/*                             otp post functions                             */
/* -------------------------------------------------------------------------- */
// if( ( isset( $_POST['otp'] ) && is_numeric( $_POST['otp'] ) )  && isset( $_POST['user'] ) && is_numeric( $_POST['user'] )  ) {
   
//     $otp = get_user_meta( (int) $_POST['user'] , 'aqar_author_last_otp', true );

//         // if( (int) $_GET['otp'] === 123456 ) {
//         if( (int) $_POST['otp'] === (int) $otp ) {
//             update_user_meta(  (int) $_POST['user'] ,'aqar_phone_confirm', 1 );
//             wp_set_current_user ( intval( $_POST['user'] ) );
//             echo json_encode( array(
//                 'success' => true,
//                 'redirect_to' => esc_url($_POST['redirect_to']),
//                 'msg' => __('تم تاكيد رقم التفعيل - يمكنك تسجيل الدخول الان' , 'aqargate') 
//             ) );
    
//         } else {
//             echo json_encode( array(
//                 'success' => false,
//                 'msg' => esc_html__('الرقم المدخل غير صحيح', 'aqargate') 
//             ) );
//         }
//     wp_die();
// }

/* -------------------------------------------------------------------------- */
/*                             register functions                             */
/* -------------------------------------------------------------------------- */
do_action('houzez_before_register');

$user_role = get_option( 'default_role' );

if( isset( $_POST['role'] ) && $_POST['role'] != '' ){
    $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
} else {
    $user_role = $user_role;
}

$term_condition = ( $term_condition == 'on') ? true : false;

if( !$term_condition ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('You need to agree with terms & conditions.', 'houzez-login-register') ) );
    wp_die();
}

$firstname = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
if( empty($firstname) && houzez_option('register_first_name', 0) == 1 ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('The first name field is empty.', 'houzez-login-register') ) );
    wp_die();
}

$lastname = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
if( empty($lastname) && houzez_option('register_last_name', 0) == 1 ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('The last name field is empty.', 'houzez-login-register') ) );
    wp_die();
}

$phone_number = isset( $_POST['phone_number'] ) ? $_POST['phone_number'] : '';
$code = isset( $_POST['code'] ) ? $_POST['code'] : '';


if( empty($phone_number) && houzez_option('register_mobile', 0) == 1 ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('Please enter your number', 'houzez-login-register') ) );
    wp_die();
}

if( empty($code) ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('Please enter your country code number', 'houzez-login-register') ) );
    wp_die();
}

if( !empty($phone_number) && houzez_option('register_mobile', 0) == 1  ) {
    $user_query = new WP_User_Query( array( 'number' => -1 ) );
    // User Loop
    if ( ! empty( $user_query->results ) ) {
        foreach ( $user_query->results as $user ) {
            $user_id = $user->ID;
            $fave_author_phone  = get_user_meta( $user_id, 'fave_author_phone', true);
            $fave_author_mobile = get_user_meta( $user_id, 'fave_author_mobile', true);
            
            if( (int) $fave_author_phone === (int) $phone_number || (int) $fave_author_mobile === (int) $phone_number) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('This phone number is already registered !', 'houzez-login-register') ) );
                wp_die();
            }
        }
    }
}



$id_number = isset( $_POST['id_number'] ) ? $_POST['id_number'] : '';
$ad_number = isset( $_POST['ad_number'] ) ? $_POST['ad_number'] : '';
$type_id   = isset( $_POST['aqar_author_type_id'] ) ? $_POST['aqar_author_type_id'] : '';

if( empty($phone_number) && houzez_option('register_mobile', 0) == 1 ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('Please enter your number', 'houzez-login-register') ) );
    wp_die();
}
if( empty( $usermane ) ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username field is empty.', 'houzez-login-register') ) );
    wp_die();
}
if( strlen( $usermane ) < 3 ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('Minimum 3 characters required', 'houzez-login-register') ) );
    wp_die();
}
if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username (do not use special characters or spaces)!', 'houzez-login-register') ) );
    wp_die();
}
if( username_exists( $usermane ) ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('This username is already registered.', 'houzez-login-register') ) );
    wp_die();
}
if( empty( $email ) ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('The email field is empty.', 'houzez-login-register') ) );
    wp_die();
}

if( email_exists( $email ) ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('This email address is already registered.', 'houzez-login-register') ) );
    wp_die();
}

if( !is_email( $email ) ) {
    echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid email address.', 'houzez-login-register') ) );
    wp_die();
}

if( $enable_password == 'yes' ){
    $user_pass         = trim( sanitize_text_field(wp_kses( $_POST['register_pass'] ,$allowed_html) ) );
    $user_pass_retype  = trim( sanitize_text_field(wp_kses( $_POST['register_pass_retype'] ,$allowed_html) ) );

    if ($user_pass == '' || $user_pass_retype == '' ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('One of the password field is empty!', 'houzez-login-register') ) );
        wp_die();
    }

    if ($user_pass !== $user_pass_retype ){
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('Passwords do not match', 'houzez-login-register') ) );
        wp_die();
    }
}


houzez_google_recaptcha_callback();

if($enable_password == 'yes' ) {
    $user_password = $user_pass;
} else {
    $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
}
$user_id = wp_create_user( $usermane, $user_password, $email );




if ( is_wp_error($user_id) ) {
    echo json_encode( array( 'success' => false, 'msg' => $user_id ) );
    wp_die();
} else {

    wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );
    // $otp_number = onlySendOTPSMS( $code, $phone_number );

    // if (!empty( $otp_number ) && is_numeric( $otp_number )) {
    //     update_user_meta(  $user_id,'aqar_author_last_otp', $otp_number );
    //     $massege = __( 'تم ارسال رقم التحقيق', 'aqargate' ); 
    //     $api = true;
    // } else {
    //     $massege = $otp_number; 
    //     $api = false;
    // }

    if( $enable_password =='yes' ) {
        if( $api ) {
            echo json_encode( array( 
                'success' => true, 
                'msg' => esc_html__('Your account was created and you can login now!', 'houzez-login-register') . ' - ' . $massege ,
                'user' => $user_id
            ) );
        }else{
            echo json_encode( array( 
                'success' => true, 
                'msg' => esc_html__('Your account was created and you can login now!', 'houzez-login-register') . ' - ' . $massege ,
            ) );
        }
    } else {
        echo json_encode( array( 
            'success' => true, 
            'msg' => esc_html__('An email with the generated password was sent!', 'houzez-login-register') . ' - ' . $massege,
            'user' => $user_id
        ) );
    }

    update_user_meta( $user_id, 'first_name', $firstname);
    update_user_meta( $user_id, 'last_name', $lastname);

    if( $user_role == 'houzez_agency' ) {
        update_user_meta( $user_id, 'fave_author_phone', $code.$phone_number);
        update_user_meta( $user_id, 'fave_author_mobile', $code.$phone_number);
    } else {
        update_user_meta( $user_id, 'fave_author_phone', $code.$phone_number);
        update_user_meta( $user_id, 'fave_author_mobile', $code.$phone_number);
    }

    if( $user_role == 'houzez_agency' ) {
        update_user_meta( $user_id, 'aqar_author_id_number', $id_number);
    } else {
        update_user_meta( $user_id, 'aqar_author_id_number', $id_number);
    }
    if( $user_role == 'houzez_agency' ) {
        update_user_meta( $user_id, 'aqar_author_ad_number', $ad_number);
    } else {
        update_user_meta( $user_id, 'aqar_author_ad_number', $ad_number);
    }

    update_user_meta( $user_id, 'aqar_author_type_id', $type_id);

    $user_as_agent = houzez_option('user_as_agent');

    if( $user_as_agent == 'yes' ) {


        if( !empty($firstname) && !empty($lastname) ) {
            $usermane = $firstname.' '.$lastname;
        }

        if ($user_role == 'houzez_agent' || $user_role == 'author') {
            houzez_register_as_agent($usermane, $email, $user_id, $code.$phone_number);

        } else if ($user_role == 'houzez_agency') {
            houzez_register_as_agency($usermane, $email, $user_id, $code.$phone_number);
        }
    }
    houzez_wp_new_user_notification( $user_id, $user_password, $code.$phone_number );
    

    do_action('houzez_after_register', $user_id);
}
wp_die();

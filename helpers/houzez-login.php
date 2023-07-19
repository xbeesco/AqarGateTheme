<?php 
/*-----------------------------------------------------------------------------------*/
// Login
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_aqargate_login', 'aqargate_login' );
add_action( 'wp_ajax_nopriv_aqargate_login', 'aqargate_login' );

function aqargate_login() {


        $allowed_html = array();

        $allowed_html_array = array('strong' => array());
        $username = wp_kses( $_POST['username'], $allowed_html );
        $pass = isset( $_POST['password'] ) ? $_POST['password'] : "";
        $is_submit_listing = isset( $_POST['is_submit_listing'] ) ? $_POST['is_submit_listing'] : '';
        $is_submit_listing = wp_kses( $is_submit_listing, $allowed_html );
        $response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : "";
        $phone = isset( $_POST['phone'] ) ? $_POST['phone'] : '';

        /* -------------------------------------------------------------------------- */
        /*                             otp post functions                             */
        /* -------------------------------------------------------------------------- */
        // if( ( isset( $_POST['otp'] ) && is_numeric( $_POST['otp'] ) )  ) {
        
        //     $otp = get_user_meta( get_current_user_id() , 'aqar_author_last_otp', true );

        //         // if( (int) $_GET['otp'] === 123456 ) {
        //         if( (int) $_POST['otp'] === (int) $otp ) {
        //             update_user_meta(  get_current_user_id() ,'aqar_phone_confirm', 1 );
        //             // wp_set_current_user ( get_current_user_id() );
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

        do_action('houzez_before_login');

        // if( $is_submit_listing == 'yes' ) {
        //     check_ajax_referer('houzez_register_nonce2', 'houzez_register_security2');
        // } else {
        //     check_ajax_referer( 'houzez_login_nonce', 'houzez_login_security' );
        // }

        if( isset( $_POST['remember'] ) ) {
            $remember = wp_kses( $_POST['remember'], $allowed_html );
        } else {
            $remember = '';
        }

        if( empty( $username ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username or email field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( empty( $pass ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The password field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( !username_exists( $username ) && !email_exists($username)) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username or email', 'houzez-login-register') ) );
            wp_die();
        }


        if($is_submit_listing != 'yes') {
            houzez_google_recaptcha_callback();
        }


        wp_clear_auth_cookie();

        $remember = ($remember == 'on') ? true : false;

        if(is_email($username)) {
            $user = get_user_by( 'email', $username );
            $username = $user->user_login;
        }
        

        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] = $pass;
        $creds['remember'] = $remember;
        $user = wp_signon( $creds, false );


        if ( is_wp_error( $user ) ) {

            $error_code = $user->get_error_code();

            if( $error_code == 'incorrect_password' ) {

                echo json_encode( array(
                    'success' => false,
                    'msg' => sprintf( wp_kses(__('The password you entered for the username <strong>%s</strong> is incorrect.', 'houzez-login-register'), $allowed_html_array), $username )
                ) );

            } else {

                echo json_encode( array(
                    'success' => false,
                    'msg' => $user->get_error_message()
                ) );

            }
            

            wp_die();
        } else {

            $fave_author_phone  = get_user_meta( $user->ID, 'fave_author_phone', true );
            $fave_author_mobile = get_user_meta( $user->ID, 'fave_author_mobile', true );

            $otp = get_user_meta( $user->ID, 'aqar_author_last_otp', true );
            $is_verify = get_user_meta( $user->ID, 'aqar_phone_confirm', true );


            // if( is_numeric( $fave_author_mobile ) && ! $is_verify ){
            //     $otp_number = onlySendOTPSMS( '', $fave_author_mobile );

            //     if (!empty( $otp_number ) && is_numeric( $otp_number )) {
            //         update_user_meta(  $user->ID,'aqar_author_last_otp', $otp_number );
            //         $massege = __( 'تم ارسال رقم التحقيق', 'aqargate' ); 
            //         $api = true;
            //     } else {
            //         $api = false;
            //         $massege = $otp_number; 
            //     }
            //     if( $api  ) {
            //         echo json_encode( array(
            //             'success' => false,
            //             'msg' => 'تم ارسال رقم تفعيل الهاتف',
            //             'show_otp' => 'yes'
            //         ) );
            //     }else{
            //         echo json_encode( array(
            //             'success' => false,
            //             'msg' => $massege,
            //             'show_otp' => 'no'
            //         ) ); 
            //     }
            //     wp_die();
            // }


            wp_set_current_user ( $user->ID ); // Set the current user detail
            //wp_set_auth_cookie( $user->ID, $remember );

            echo json_encode( array( 
                'success' => true,
                'redirect_to' => esc_url($_POST['redirect_to']),
                'msg' => esc_html__('Login successful, redirecting...', 'houzez-login-register') 
            ) );

            do_action('houzez_after_login');

        }
        wp_die();
}



    /**
     * send_otp
     *
     * @param  mixed $data
     * @return void
     */
    function send_otp( $data ){

        if( !is_user_logged_in() ){
            echo json_encode( array(
                'success' => false,
                'msg' => 'خطا في المستخدم'
            ) );
        }
  
        $user = wp_get_current_user();
        $userID  = $user->ID;

        if( !isset( $_POST['phone'] ) || empty( $_POST['phone'] )){
            echo json_encode( array(
                'success' => false,
                'msg' => 'Missing Phone Number'
            ));
        }

        if( !isset( $_POST['code'] ) || empty( $_POST['code'] )){
            echo json_encode( array(
                'success' => false,
                'msg' => 'Missing Country Code'
            ));
        }

        $massege = __( 'تم ارسال رقم التحقيق', 'aqargate' );

        $otp_number = onlySendOTPSMS( $data['code'], $data['phone'] );

        if (!empty( $otp_number ) && is_numeric( $otp_number )) {
            update_user_meta(  $userID ,'aqar_author_last_otp', $otp_number );
            $massege = __( 'تم ارسال رقم التحقيق', 'aqargate' ); 
        } else {
            $massege = $otp_number; 
        }

        return $massege;  
    }
       
    /**
     * check_user_otp
     *
     * @param  mixed $data
     * @return void
     */
    function check_user_otp( $data ){

        if( !is_user_logged_in() ){
            echo json_encode( array(
                'success' => false,
                'msg' => 'خطا في المستخدم'
            ) );
        }

        if( !isset( $_POST['otp'] ) ){
            echo json_encode( array(
                'success' => false,
                'msg' => 'Missing Otp Number'
            ) );
        }

        global $current_user;
        $user = wp_get_current_user();
        $userID  = $user->ID;
        
        if( isset( $_POST['user_id'] ) && !empty( $_POST['user_id'] ) ) {
            $userID = $_POST['user_id'];
        }
        
        // update_user_meta(  $userID ,'aqar_author_last_otp', 123456 );

        $otp = get_user_meta( $userID, 'aqar_author_last_otp', true );

        // if( (int) $_GET['otp'] === 123456 ) {
        if( (int) $_POST['otp'] === (int) $otp ) {
            echo json_encode( array(
                'success' => true,
                'msg' => __('تم تاكيد التسجيل' , 'aqargate') 
            ) );
    
        } else {
            echo json_encode( array(
                'success' => false,
                'msg' => esc_html__('الرقم المدخل غير صحيح', 'aqargate') 
            ) );
        }

    }
    
    /**
     * generate_otp_digits
     *
     * @return void
     */
    function generate_otp_digits(){
		$digits = carbon_get_theme_option('otp-digits') ? carbon_get_theme_option('otp-digits') : 6;
		return rand( pow( 10, $digits - 1 ) , pow( 10, $digits ) - 1 );
	}

    /**
	 * This will only send OTP SMS.
	 * @return OTP
	*/
	function onlySendOTPSMS( $phone_code, $phone_no ){

		$operator = aq_wp_twilio();

		if( !$operator ){
            return new WP_Error( 'no-operator', 
            __( "Operator not found. Please download operator SDK from the plugin settings. Check documentation for how to setup.", 'mobile-login-woocommerce' ) 
            );
			
		}

		$otp =  generate_otp_digits();
    
		//$otpSent = $operator->Add_Caller_ID( $phone_code.$phone_no, self::getOTPSMSText( $otp ) );
        if( empty($phone_code) ) {
            $Phone = $phone_no;
        }else{
            $Phone = $phone_code.$phone_no; 
        }
		$otpSent = $operator->sendSMS( $phone_code.$phone_no, getOTPSMSText( $otp ) );

		//$otpSent = true;

		if( is_wp_error( $otpSent ) ){
			return $otpSent->get_error_message();
		}

		return $otp;
	}
    
    /**
     * getOTPSMSText
     *
     * @param  mixed $otp
     * @return void
     */
    function getOTPSMSText( $otp ){
		
		$sms_text = carbon_get_theme_option('r-sms-txt');

		$placeholders = array(
			'[otp]'		=> $otp,
		);
		foreach ( $placeholders as $placeholder => $placeholder_value ) {
			$sms_text = str_replace( $placeholder , $placeholder_value , $sms_text );
		}

		return $sms_text;
	}

  
add_action( 'wp_ajax_nafathApi', 'nafathApi' );
add_action( 'wp_ajax_nopriv_nafathApi', 'nafathApi' );

function nafathApi() {
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    if( empty($id) ) {
        wp_send_json( array('success' => false) );
        wp_die();
    }

    
    $nath_id = get_users('meta_value=' . $id );

    $message = 'رقم الهوية مسجل مسبقا في الموقع , يمكنك تسجيل الدخول';
    if( is_array( $nath_id ) && count( $nath_id ) > 0 ) {
        wp_send_json( array('success' => false, 'message' => $message ) );
        wp_die();
    }

    require_once ( AG_DIR . 'module/class-nafath-api.php' );

    $NafathMoudle = new NafathMoudle();

    $response = $NafathMoudle->login( $id );
    
    /**----------------Test--------------------- */
    // $trans = 'c6c5085d-13e7-4408-ad11-2afa44fe2e49';
    // $rand  = '44';
    // wp_send_json( array('success' => true, 'number' => $response->random, 'transId' => $trans ) );
    // wp_die();
    /**----------------------------------------- */


    if( isset( $response->random ) ) {
        $data['userInfo'] = [];
        $data['response'] = $response;
        $data['transId']  = $response->transId;
        $data['cardId']   = $id;
        $data['status']   = 'PENDING';
       
        $NafathDB = new NafathDB();
       
        $NafathDB->update_nafath_callback($data);

        wp_send_json( array('success' => true, 'number' => $response->random , 'transId' => $response->transId ) );
        wp_die();
    }else{
        wp_send_json( array('success' => false, 'message' => isset($response->message) ? $response->message : 'هناك خطأ ! حاول مرة اخري' ) );
        wp_die(); 
    }
} 


add_action( 'wp_ajax_fetchdata', 'fetchdata' );
add_action( 'wp_ajax_nopriv_fetchdata', 'fetchdata' );
function fetchdata()
{

    $id = isset($_POST['authorid']) ? $_POST['authorid'] : '';
    $transId = isset($_POST['transId']) ? $_POST['transId'] : '';

    if( empty($id) ) {
        wp_send_json( array('success' => false, 'message' => '' ) );
        wp_die(); 
    }

    $NafathDB = new NafathDB();

    $data = [
        'transId'  => $transId,
        'cardId'   => $id,
        'userInfo' => '',
    ];

    $get_status = $NafathDB->get_status($data);

    if( $get_status ){
        $get_data = $NafathDB->get_nafath_data($data);
            wp_send_json( array(
                'success'    => $get_status,
                'id'         => $id,
                'arFullName' => $get_data['arFullName'],
                'arFirst'    => $get_data['arFirst'],
                'arGrand'    => $get_data['arGrand'],
                'arTwoNames' => $get_data['arTwoNames'],
            ) );
        wp_die();
    }else{
        wp_send_json( array('success' => false, 'message' => 'لم يتم اكنمال الربط' ) );
        wp_die();
    }
}

/*-----------------------------------------------------------------------------------*/
// Register
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_ag_houzez_register', 'ag_houzez_register' );
add_action( 'wp_ajax_ag_houzez_register', 'ag_houzez_register' );
function ag_houzez_register() {
        
        check_ajax_referer('houzez_register_nonce', 'houzez_register_security');

        $allowed_html = array();

        $usermane          = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));
        $email             = trim( sanitize_text_field( wp_kses( $_POST['useremail'], $allowed_html ) ));
        $term_condition    = isset( $_POST['term_condition'] ) ? wp_kses( $_POST['term_condition'], $allowed_html ) : "off";
        $enable_password   = houzez_option('enable_password');

        $response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : "";

        do_action('houzez_before_register');

        $user_roles = array ( 'houzez_agency', 'houzez_agent', 'houzez_buyer', 'houzez_seller', 'houzez_owner', 'houzez_manager' );

        $user_role = get_option( 'default_role' );

        if( isset( $_POST['role'] ) && $_POST['role'] != '' && in_array( $_POST['role'], $user_roles ) ) {
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
        // if( empty($firstname) && houzez_option('register_first_name', 0) == 1 ) {
        //     echo json_encode( array( 'success' => false, 'msg' => esc_html__('The first name field is empty.', 'houzez-login-register') ) );
        //     wp_die();
        // }

        $lastname = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
        // if( empty($lastname) && houzez_option('register_last_name', 0) == 1 ) {
        //     echo json_encode( array( 'success' => false, 'msg' => esc_html__('The last name field is empty.', 'houzez-login-register') ) );
        //     wp_die();
        // }

        $phone_number = isset( $_POST['phone_number'] ) ? $_POST['phone_number'] : '';
        if( empty($phone_number) && houzez_option('register_mobile', 0) == 1 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Please enter your number', 'houzez-login-register') ) );
            wp_die();
        }

  
        $brokerage_license_number = isset( $_POST['brokerage_license_number'] ) ? $_POST['brokerage_license_number'] : '';
        if( empty($brokerage_license_number) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('رقم رخصة الفال مطلوب', 'houzez-login-register') ) );
            wp_die();
        }
        $license_expiration_date = isset( $_POST['license_expiration_date'] ) ? $_POST['license_expiration_date'] : '';
        if( empty($license_expiration_date)  ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('تاريخ انتهاء الرخصة مطلوب', 'houzez-login-register') ) );
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

            if( $enable_password =='yes' ) {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('Your account was created and you can login now!', 'houzez-login-register') ) );
            } else {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('An email with the generated password was sent!', 'houzez-login-register') ) );
            }

            update_user_meta( $user_id, 'first_name', $firstname);
            update_user_meta( $user_id, 'last_name', $lastname);

            if( $user_role == 'houzez_agency' ) {
                update_user_meta( $user_id, 'fave_author_phone', $phone_number);
                update_user_meta( $user_id, 'fave_author_mobile', $phone_number);
                update_user_meta( $user_id, 'aqar_author_type_id', 2);

            } else {
                update_user_meta( $user_id, 'fave_author_mobile', $phone_number);
                update_user_meta( $user_id, 'fave_author_mobile', $phone_number);
                update_user_meta( $user_id, 'aqar_author_type_id', 1);

            }

            if ( !empty( $_POST['id_number'] ) ) {
                $id_number = sanitize_text_field( $_POST['id_number'] );
                update_user_meta( $user_id, 'aqar_author_id_number', $id_number );
            }

            if( !empty( $_POST['brokerage_license_number'] ) ){
                $brokerage_license_number = $_POST['brokerage_license_number'];
                update_user_meta( $user_id, 'brokerage_license_number', $brokerage_license_number );
            }
            
            if( !empty( $_POST['license_expiration_date'] ) ){
                $license_expiration_date = $_POST['license_expiration_date'];
                update_user_meta( $user_id, 'license_expiration_date', $license_expiration_date );
            }

            if ( !empty( $_POST['full_name'] ) ) {    
                wp_update_user( array (
                    'ID' => $user_id, 
                    'display_name' => $_POST['full_name'],
                    'nickname'     => $_POST['full_name']
                ));
                update_user_meta( $user_id, 'display_name', $_POST['full_name'] );
                update_user_meta( $user_id, 'nickname', $_POST['full_name'] );
            }
 
            $user_as_agent = houzez_option('user_as_agent');

            if( $user_as_agent == 'yes' ) {

                if( !empty($firstname) && !empty($lastname) ) {
                    $usermane = $firstname.' '.$lastname;
                }

                if ($user_role == 'houzez_agent' || $user_role == 'author') {
                    houzez_register_as_agent($usermane, $email, $user_id, $phone_number);

                } else if ($user_role == 'houzez_agency') {
                    houzez_register_as_agency($usermane, $email, $user_id, $phone_number);
                }
            }
            houzez_wp_new_user_notification( $user_id, $user_password, $phone_number );

            do_action('houzez_after_register', $user_id);
        }
        wp_die();

}

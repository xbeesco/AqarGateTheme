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
        if( ( isset( $_POST['otp'] ) && is_numeric( $_POST['otp'] ) )  ) {
        
            $otp = get_user_meta( get_current_user_id() , 'aqar_author_last_otp', true );

                // if( (int) $_GET['otp'] === 123456 ) {
                if( (int) $_POST['otp'] === (int) $otp ) {
                    update_user_meta(  get_current_user_id() ,'aqar_phone_confirm', 1 );
                    // wp_set_current_user ( get_current_user_id() );
                    echo json_encode( array(
                        'success' => true,
                        'redirect_to' => esc_url($_POST['redirect_to']),
                        'msg' => __('تم تاكيد رقم التفعيل - يمكنك تسجيل الدخول الان' , 'aqargate') 
                    ) );
            
                } else {
                    echo json_encode( array(
                        'success' => false,
                        'msg' => esc_html__('الرقم المدخل غير صحيح', 'aqargate') 
                    ) );
                }
            wp_die();
        }

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


            if( is_numeric( $fave_author_mobile ) && ! $is_verify ){
                $otp_number = onlySendOTPSMS( '', $fave_author_mobile );

                if (!empty( $otp_number ) && is_numeric( $otp_number )) {
                    update_user_meta(  $user->ID,'aqar_author_last_otp', $otp_number );
                    $massege = __( 'تم ارسال رقم التحقيق', 'aqargate' ); 
                    $api = true;
                } else {
                    $api = false;
                    $massege = $otp_number; 
                }
                if( $api  ) {
                    echo json_encode( array(
                        'success' => false,
                        'msg' => 'تم ارسال رقم تفعيل الهاتف',
                        'show_otp' => 'yes'
                    ) );
                }else{
                    echo json_encode( array(
                        'success' => false,
                        'msg' => $massege,
                        'show_otp' => 'no'
                    ) ); 
                }
                wp_die();
            }

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
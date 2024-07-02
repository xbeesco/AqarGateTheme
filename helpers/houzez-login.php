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

/* -------------------------------------------------------------------------- */
/*                                  nafathApi                                 */
/* -------------------------------------------------------------------------- */
add_action( 'wp_ajax_nafathApi', 'nafathApi' );
add_action( 'wp_ajax_nopriv_nafathApi', 'nafathApi' );
function nafathApi() {

    $id = isset($_POST['id']) ? $_POST['id'] : '';

    /* --------------------- false this line for live site -------------------- */
        $test = false; 
        $COMPLETED = ['1000000000', '1000000446'];
        if( in_array( $id, $COMPLETED ) ) {
            $test = true;
        }
    /* ------------------------------------ . ----------------------------------- */
     
    

    if( empty($id) ) {
        wp_send_json( array('success' => false, 'message'=> 'رقم الهوية مطلوب') );
        wp_die();
    }

    aq_black_list($id);

    if( ! is_user_logged_in() && ! $test ){       
        $nath_id = get_users('meta_value=' . $id );
        $message = 'رقم الهوية مسجل مسبقا في الموقع , يمكنك تسجيل الدخول';
        if( is_array( $nath_id ) && count( $nath_id ) > 0 ) {
            wp_send_json( array('success' => false, 'message' => $message ) );
            wp_die();
        }
    }

    /* ---------------------------------- Test ---------------------------------- */
        // $trans = 'c6c5085d-13e7-4408-ad11-2afa44fe2e49';
        // $rand  = '44';
        // aq_send_nafath_dummy_response($transId, $id);
        // wp_send_json( array('success' => true, 'number' => $rand, 'transId' => $trans ) );
        // wp_die();
   /* ------------------------------------ . ----------------------------------- */ 

    require_once ( AG_DIR . 'module/class-nafath-api.php' );

    $NafathMoudle = new NafathMoudle();

    $response = $NafathMoudle->login( $id );
    
    if( isset( $response->random ) ) {
        $data['userInfo'] = [];
        $data['response'] = $response;
        $data['transId']  = $response->transId;
        $data['cardId']   = $id;
        $data['status']   = 'PENDING';
       
        $NafathDB = new NafathDB();
       
        $NafathDB->update_nafath_callback($data);

        if( $test ) {
            aq_send_nafath_dummy_response($response->transId, $id);
        }
        
        wp_send_json( array('success' => true, 'number' => $response->random , 'transId' => $response->transId ) );
        wp_die();
    }else{
        wp_send_json( array('success' => false, 'message' => isset($response->message) ? $response->message : 'هناك خطأ ! حاول مرة اخري' ) );
        wp_die(); 
    }
} 


function aq_send_nafath_dummy_response($transId, $id)
{
    $status = 'REJECTED';
    $COMPLETED = ['1000000000', '1000000446'];
    if( in_array( $id, $COMPLETED ) ) {
        $status = 'COMPLETED';
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://aqargate.com/nafazcallback',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
    "response":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2luZm8iOnsiaWQiOjEwMDAwMDA0NDYsImZpcnN0X25hbWUjYXIiOiLZhdit2YXYryIsImZhdGhlcl9uYW1lI2FyIjoi2LnYqNiv2KfZhNmE2YciLCJncmFuZF9uYW1lI2FyIjoi2YXYrdmF2K8iLCJmYW1pbHlfbmFtZSNhciI6Itin2YTYrdmF2LHZiiIsImZpcnN0X25hbWUjZW4iOiJNb2hhbWVkIiwiZmF0aGVyX25hbWUjZW4iOiJBYmR1bGxhaCIsImdyYW5kX25hbWUjZW4iOiJNb2hhbW1lZCIsImZhbWlseV9uYW1lI2VuIjoiQWwtQWhtYXJpIiwidHdvX25hbWVzI2FyIjoi2YXYrdmF2K8g2KfZhNit2YXYsdmKIiwidHdvX25hbWVzI2VuIjoiTW9oYW1lZCBBbC1BaG1hcmkiLCJmdWxsX25hbWUjYXIiOiLZhdit2YXYryDYudio2K_Yp9mE2YTZhyDZhdit2YXYryDYp9mE2K3Zhdix2YoiLCJmdWxsX25hbWUjZW4iOiJNb2hhbWVkIEFiZHVsbGFoIE1vaGFtbWVkIEFsLUFobWFyaSIsImdlbmRlciI6Ik0iLCJkb2IjZyI6IjIwMDEtMDEtMDEiLCJkb2IjaCI6MTQyMTEwMDYsIm5hdGlvbmFsaXR5IjoxMTMsIm5hdGlvbmFsaXR5I2FyIjoi2KfZhNmF2YXZhNmD2Kkg2KfZhNi52LHYqNmK2Kkg2KfZhNiz2LnZiNiv2YrYqSIsIm5hdGlvbmFsaXR5I2VuIjoiS2luZ2RvbSBvZiBTYXVkaSBBcmFiaWEiLCJsYW5ndWFnZSI6IkEifSwiYXVkIjoiVENDX1NQX1RFU1QiLCJpc3MiOiJodHRwczovL3d3dy5pYW0uc2EvbmFmYXRoIiwiaWF0IjoxNjkyMjUyMzgxLCJuYmYiOjE2OTIyNTIzODEsImV4cCI6MTY5MjI1NTg3Nn0.lbfJqGv7t0QDvRw5OYr-8HFcCK1oIim700iSOo2yjMKxCr40097GK6Gx78mKQNasMK_auVJuOayq7H9_p0wz_oTI8Y78gfVr6ugfcj5r8x1KUOnJaG4jujjzdHwJtYLawBCMOrGr2uQNeYt7LfYAmNw7-RGJ4Qs3rrTk8VK_P_rJILpOajgGb0ekVZthB2F_-KMerK8HExyMuA8ZKD4axsueK6a60SOYwWKQATAuPdJlYZpqldkdpo4-oGAk3N1uqy4i1qMuKlxs89PUkrpEPv1Y3vH-kDMSiQ1M_AZA7XmH-Tpd76pPq8UYsHeMUKQ5XLqlwRlUhVJuCHxf_1g",
    "status": "'. $status .'",
    "transId": "'.$transId.'",
    "ServiceName": "DigitalServiceEnrollmentWithoutBio"
    }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
}

/* -------------------------------------------------------------------------- */
/*                                  fetchdata                                 */
/* -------------------------------------------------------------------------- */
add_action( 'wp_ajax_fetchdata', 'fetchdata' );
add_action( 'wp_ajax_nopriv_fetchdata', 'fetchdata' );
function fetchdata()
{

    $id = isset($_POST['authorid']) ? $_POST['authorid'] : '';
    $transId = isset($_POST['transId']) ? $_POST['transId'] : '';
    $aqar_author_type_id = isset($_POST['aqar_author_type_id']) ? $_POST['aqar_author_type_id'] : '1';

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
        if( ! is_user_logged_in() ){  
            wp_send_json( array(
                'success'    => $get_status,
                'id'         => $id,
                'arFullName' => $get_data['arFullName'],
                'arFirst'    => $get_data['arFirst'],
                'arGrand'    => $get_data['arGrand'],
                'arTwoNames' => $get_data['arTwoNames'],
                'html'       => aqar_register_form($aqar_author_type_id),

            ) );
            wp_die();
        } else {
            $userID = get_current_user_id();
            update_user_meta( $userID, 'aqar_author_id_number', $id );
            wp_update_user( array (
                'ID' => $userID, 
                'display_name' => $get_data['arFullName'],
            ));
            update_user_meta( $userID, 'display_name', $get_data['arFullName'] );
            update_user_meta( $userID, 'aqar_author_type_id', $aqar_author_type_id);
            update_user_meta( $userID, 'first_name', $get_data['arFirst']);
            update_user_meta( $userID, 'last_name', $get_data['arGrand']);
            wp_send_json( array('success' => true, 'message' => 'تم الاكتمال', 'reload' => true) );
        }
    }else{
        wp_send_json( array('success' => false, 'message' => 'لم يتم اكنمال الربط' ) );
        wp_die();
    }
}

/* -------------------------------------------------------------------------- */
/*                                Register form                               */
/* -------------------------------------------------------------------------- */
function aqar_register_form ($aqar_author_type_id){
    $author_type = [
        '1' => 'houzez_agent',
        '2' => 'houzez_agency',
        '3' => 'houzez_owner',
        '4' => 'houzez_buyer',
        '5' => 'houzez_seller',

    ];
    $author_type_name = $author_type[$aqar_author_type_id];
    $class = 'col-md-12';
    if( $author_type_name === 'houzez_agency' ) {
        $class = 'col-md-6';
    }
    $html = '<div class="modal-header-ajax">
                <span style="color: #bdb290;font-size: 12px;">انشاء حساب</span>
                <h5 style="margin-bottom: 40px;">مرحبا بك عميلنا العزيز</h5>
            </div>';
    $html .= '<form>
                <input type="hidden" id="first_name" name="first_name" value="">
                <input type="hidden" id="last_name" name="last_name" value="">
                <input type="hidden" id="role" name="role" value="">
                <input type="hidden" id="transId" name="transId" value="">
                <div class="register-form row">';

                $html .= '<div class="form-group ' . $class . ' mb-3 col-xs-12">
                        <div class="form-group-field username-field">
                            <label for="title">' . __('الاسم بالكامل', 'houzez') . '</label>
                            <input class="form-control" name="full_name" type="text"
                                placeholder="' . __('full Name','houzez') . '" readonly />
                        </div><!-- input-group -->
                    </div><!-- form-group -->';
                if( $author_type_name === 'houzez_agency' ) {
                    $html .= '<div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="title">' . __('اسم المؤسسة / الشركة', 'houzez') . '</label>
                                    <input type="text" name="title" class="selectpicker form-control" id="title">
                                </div>
                            </div>';
                }
                $html .= '<div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>' . __('رقم الهوية','houzez') . '</label>
                        <input type="text" name="id_number" value="" class="form-control"
                            placeholder="' . __('يرجي ادخال رقم الهوية','houzez') . '" readonly>
                    </div>
                </div>';

                $html .= '<div class="form-group col-sm-6 col-xs-12 mb-3">
                    <div class="form-group">
                        <label for="username">' . __('Username','houzez') . '</label>
                        <input class="form-control" name="username" type="text"
                            placeholder="' .  __('Username','houzez') . '" />
                    </div><!-- input-group -->
                </div><!-- form-group -->';


                $html .= '<div class="form-group col-sm-6 col-xs-12 mb-3">
                    <div class="form-group">
                        <label for="useremail">' . __('Email','houzez') . '</label>
                        <input class="form-control" name="useremail" type="email"
                            placeholder="' . __('Email','houzez') . '" />
                    </div><!-- input-group -->
                </div><!-- form-group --> ';

                if( houzez_option('register_mobile', 0) == 1 ) { 
                    $html .= '<div class="form-group col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="phone_number">' . __('Phone','houzez') . '</label>
                            <input class="form-control" name="phone_number" type="number"
                                placeholder="' . __('Phone','houzez') . '" />
                        </div><!-- input-group -->
                    </div><!-- form-group --> ';
                } 

                if( houzez_option('enable_password') == 'yes' ) { 
                    $html .= '<div class="form-group col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="register_pass">' . __('Password','houzez') . '</label>
                            <input class="form-control" name="register_pass" placeholder="' . __('Password','houzez') . '"
                                type="password" />
                        </div><!-- input-group -->
                    </div><!-- form-group -->
                    <div class="form-group col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="register_pass_retype"> ' . __('Retype Password','houzez') . '</label>
                            <input class="form-control" name="register_pass_retype"
                                placeholder="' . __('Retype Password','houzez') . '" type="password" />
                        </div><!-- input-group -->
                    </div><!-- form-group --> ';
                } 
                if( $author_type_name === 'houzez_agency' || $author_type_name === 'houzez_agent' ) {
                    $html .= '<div class="col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label
                                for="brokerage_license_number">' . __('رقم رخصة الوساطة العقارية ( فال )','houzez') . '</label>
                            <input type="text" name="brokerage_license_number" value="" class="form-control"
                                placeholder="' .__('يرجي ادخال رقم رخصة الوساطة العقارية','houzez') . '">
                        </div>
                    </div>';

                    $html .= '<div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="license_expiration_date">' . __('تاريخ انتهاء الرخصة	','houzez') . '</label>
                                <input type="date" name="license_expiration_date" value="" class="form-control" placeholder="">
                            </div>
                        </div>
                    </div><!-- login-form-wrap -->';
                }

                $html .= '<div class="form-tools">
                    <label class="control control--checkbox">
                        <input name="term_condition" type="checkbox">
                        ' . sprintf( __( 'I agree with your <a target="_blank" href="%s">Terms & Conditions</a>', 'houzez' ), 
                        get_permalink(houzez_option('login_terms_condition') )) . '
                        <span class="control__indicator"></span>
                    </label>
                </div><!-- form-tools --> ';

                if(houzez_option('agent_forms_terms')) {
                    $html .= '<div class="form-tools">
                        <label class="control control--checkbox">
                            <input name="privacy_policy" type="checkbox">' . houzez_option('agent_forms_terms_text') . '
                            <span class="control__indicator"></span>
                        </label>
                    </div><!-- form-tools -->';
                }
                $html .= '' . get_template_part('template-parts/google', 'reCaptcha') . '';

        $html .= '<input type="hidden" name="action" value="ag_houzez_register" id="register_action">
                <button id="houzez-register" class="btn btn-primary btn-full-width">
                <span class="btn-loader houzez-loader-js"></span>
                    ' . __('Register','houzez') . '
                </button>
            </form>';
    return $html;   
    
}
/*-----------------------------------------------------------------------------------*/
// Register
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_ag_houzez_register', 'ag_houzez_register' );
add_action( 'wp_ajax_ag_houzez_register', 'ag_houzez_register' );
function ag_houzez_register() {
        
        // check_ajax_referer('houzez_register_nonce', 'houzez_register_security');
        // define( 'WP_DEBUG_DISPLAY', true );
        // @ini_set( 'display_errors', 1 );

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

        $agency_name = isset( $_POST['title'] ) ? $_POST['title'] : '';
        if( empty($agency_name) && $user_role === 'houzez_agency' ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('أسم الشركة / المؤسسة مطلوب', 'houzez-login-register') ) );
            wp_die();
        }

  
        $brokerage_license_number = isset( $_POST['brokerage_license_number'] ) ? $_POST['brokerage_license_number'] : '';
        if( empty($brokerage_license_number) && in_array($user_role, ['houzez_agent', 'houzez_agency']) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('رقم رخصة الفال مطلوب', 'houzez-login-register') ) );
            wp_die();
        }
        
        $license_expiration_date = isset( $_POST['license_expiration_date'] ) ? $_POST['license_expiration_date'] : '';
        if( empty($license_expiration_date) && in_array($user_role, ['houzez_agent', 'houzez_agency']) ) {
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
                update_user_meta( $user_id, 'fave_author_title', $agency_name);
                update_user_meta( $user_id, 'aqar_display_name', $agency_name);
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
                    $agency_name = !empty($agency_name) ? $agency_name : $usermane;
                    aq_register_as_agency($agency_name, $email, $user_id, $phone_number);
                }
            }
            houzez_wp_new_user_notification( $user_id, $user_password, $phone_number );

            do_action('houzez_after_register', $user_id);
        }
        wp_die();
}

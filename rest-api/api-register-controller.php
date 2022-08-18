<?php

function ag_register( $request )
{
    $_POST = $request;

    $allowed_html = array();

    $usermane          = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));
    $email             = trim( sanitize_text_field( wp_kses( $_POST['useremail'], $allowed_html ) ));
    $term_condition    = wp_kses( $_POST['term_condition'], $allowed_html );
    $enable_password   = houzez_option('enable_password');
    $response          = isset($_POST["g-recaptcha-response"]) ? $_POST["g-recaptcha-response"] : '';
    
    do_action('houzez_before_register');
    
    $user_role = get_option( 'default_role' );
    
    if( isset( $_POST['role'] ) && $_POST['role'] != '' ){
        $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
    } else {
        $user_role = $user_role;
    }
    
    $term_condition = ( $term_condition == 'on') ? true : false;
    
    // if( !$term_condition ) {
    //     return AqarGateApi::error_response(
    //         'rest_invalid_terms',
    //         esc_html__('You need to agree with terms & conditions.', 'houzez-login-register') 
    //     );
    // }
    
    $firstname = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
    // if( empty($firstname) && houzez_option('register_first_name', 0) == 1 ) {
    //     return AqarGateApi::error_response(
    //         'rest_invalid_first_name',
    //         esc_html__('The first name field is empty.', 'houzez-login-register') 
    //     );
    // }
    
    $lastname = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
    // if( empty($lastname) && houzez_option('register_last_name', 0) == 1 ) {
    //     return AqarGateApi::error_response(
    //         'rest_invalid_last_name',
    //         esc_html__('The last name field is empty.', 'houzez-login-register') 
    //     );
    // }
    
    $phone_number = isset( $_POST['phone_number'] ) ? $_POST['phone_number'] : '';
    if( empty($phone_number) && houzez_option('register_mobile', 0) == 1 ) {
        return AqarGateApi::error_response(
            'rest_invalid_number',
            esc_html__('Please enter your number', 'houzez-login-register') 
        );
    }

    $user_query = new WP_User_Query( array( 'number' => -1 ) );
    $return = '';
    // User Loop
    if ( ! empty( $user_query->results ) ) {
        foreach ( $user_query->results as $user ) {
            $user_id = $user->ID;
            $fave_author_phone = get_user_meta( $user_id, 'fave_author_phone', true);
            $fave_author_mobile = get_user_meta( $user_id, 'fave_author_mobile', true);
            
            if( (int) $fave_author_phone === (int) $phone_number || (int) $fave_author_mobile === (int) $phone_number) {
                return AqarGateApi::error_response(
                    'rest_invalid_number',
                    esc_html__('This phone number is already registered !', 'houzez-login-register') 
                );
            }
        }
    }

    $id_number = isset( $_POST['id_number'] ) ? $_POST['id_number'] : '';
    $ad_number = isset( $_POST['ad_number'] ) ? $_POST['ad_number'] : '';
    $type_id   = isset( $_POST['aqar_author_type_id'] ) ? $_POST['aqar_author_type_id'] : '';
    
    if( empty( $usermane ) ) {
        return AqarGateApi::error_response(
            'rest_invalid_username',
            esc_html__('The username field is empty.', 'houzez-login-register')
        );
    }
    if( strlen( $usermane ) < 3 ) {
        return AqarGateApi::error_response(
            'rest_invalid_username',
            esc_html__('Minimum 3 characters required', 'houzez-login-register') 
        );
    }
    if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
        return AqarGateApi::error_response(
            'rest_invalid_username',
            esc_html__('Invalid username (do not use special characters or spaces)!', 'houzez-login-register')
        );
    }
    if( username_exists( $usermane ) ) {
        return AqarGateApi::error_response(
            'rest_invalid_username',
            esc_html__('This username is already registered.', 'houzez-login-register')
        );
    }

    // if( empty( $email ) ) {
    //     return AqarGateApi::error_response(
    //         'rest_invalid_email',
    //         esc_html__('The email field is empty.', 'houzez-login-register') 
    //     );
    // }
    
    // if( email_exists( $email ) ) {
    //     return AqarGateApi::error_response(
    //         'rest_invalid_email',
    //         esc_html__('This email address is already registered.', 'houzez-login-register') 
    //     );
    // }
    
    // if( !is_email( $email ) ) {
    //     return AqarGateApi::error_response(
    //         'rest_invalid_email',
    //         esc_html__('Invalid email address.', 'houzez-login-register') 
    //     );
    // }
    
    // if( $enable_password == 'yes' ){
    //     $user_pass         = trim( sanitize_text_field(wp_kses( $_POST['register_pass'] ,$allowed_html) ) );
    //     $user_pass_retype  = trim( sanitize_text_field(wp_kses( $_POST['register_pass_retype'] ,$allowed_html) ) );
    
    //     if ($user_pass == '' || $user_pass_retype == '' ) {
    //         return AqarGateApi::error_response(
    //             'rest_invalid_password',
    //             esc_html__('One of the password field is empty!', 'houzez-login-register') 
    //         );
    //     }
    
    //     if ($user_pass !== $user_pass_retype ){
    //         return AqarGateApi::error_response(
    //             'rest_invalid_password',
    //             esc_html__('Passwords do not match', 'houzez-login-register') 
    //         );
    //     }
    // }
    
    
    // houzez_google_recaptcha_callback();
    $enable_password = false;
    if( $enable_password ) {
        $user_password = $user_pass;
    } else {
        $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
    }
    $user_id = wp_create_user( $usermane, $user_password, $email );
    
    
    
    
    if ( is_wp_error($user_id) ) {
        return array( 'user_id' =>  $user_id );
    } else {
    
        wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );
    
        // if( $enable_password =='yes' ) {
        //     echo json_encode( array( 'success' => true, 'msg' => esc_html__('Your account was created and you can login now!', 'houzez-login-register') ) );
        // } else {
        //     echo json_encode( array( 'success' => true, 'msg' => esc_html__('An email with the generated password was sent!', 'houzez-login-register') ) );
        // }
    
        update_user_meta( $user_id, 'first_name', $firstname);
        update_user_meta( $user_id, 'last_name', $lastname);
    
        if( $user_role == 'houzez_agency' ) {
            update_user_meta( $user_id, 'fave_author_phone', $phone_number);
        } else {
            update_user_meta( $user_id, 'fave_author_mobile', $phone_number);
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

        $static_otp = 1234;

        update_user_meta( $user_id, 'aqar_author_last_otp', $static_otp );
    
        $user_as_agent = houzez_option('user_as_agent');
    
        if( $user_as_agent == 'yes' ) {
    
    
            if( !empty($firstname) && !empty($lastname) ) {
                $user_mane = $firstname.' '.$lastname;
            }
    
            if ($user_role == 'houzez_agent' || $user_role == 'author') {
                houzez_register_as_agent($user_mane, $email, $user_id, $phone_number);
    
            } else if ($user_role == 'houzez_agency') {
                houzez_register_as_agency($user_mane, $email, $user_id, $phone_number);
            }
        }

        //houzez_wp_new_user_notification( $user_id, $user_password, $phone_number );

        do_action('houzez_after_register', $user_id);

        $user_token = aqargate_token_after_register( $usermane, $user_password );


        $author_data = [ 'user_id' => $user_id ];

        $user_token = array_merge( $user_token, $author_data );

    }

    return $user_token;
}



/**
 * api_update_profile
 *
 * @param  mixed $data
 * @return void
 */
function api_update_profile( $data ){

    $_POST = $data ;

    global $current_user;
    $user = wp_get_current_user();
    $userID  = $user->ID;
    
    if( isset( $_POST['user_id'] ) && !empty( $_POST['user_id'] ) ) {
        $userID = $_POST['user_id'];
    }
    
    $user_company = $userlangs = $latitude = $longitude = $tax_number = $user_location = $license = $user_address = $fax_number = $firstname = $lastname = $title = $about = $userphone = $usermobile = $userskype = $facebook = $twitter = $linkedin = $instagram = $pinterest = $profile_pic = $profile_pic_id = $website = $useremail = $service_areas = $specialties = $whatsapp = '';
    
    if( isset( $_POST['firstname'] ) ) {
        // Update first name
        if ( !empty( $_POST['firstname'] ) ) {
            $firstname = sanitize_text_field( $_POST['firstname'] );
            update_user_meta( $userID, 'first_name', $firstname );
        } else {
            delete_user_meta( $userID, 'first_name' );
        }
    }

    if( isset( $_POST['gdpr_agreement'] ) ) {
        // Update GDPR
        if ( !empty( $_POST['gdpr_agreement'] ) ) {
            $gdpr_agreement = sanitize_text_field( $_POST['gdpr_agreement'] );
            update_user_meta( $userID, 'gdpr_agreement', $gdpr_agreement );
        } else {
            delete_user_meta( $userID, 'gdpr_agreement' );
        }
    }
    
    if( isset( $_POST['lastname'] ) ) {
        // Update last name
        if ( !empty( $_POST['lastname'] ) ) {
            $lastname = sanitize_text_field( $_POST['lastname'] );
            update_user_meta( $userID, 'last_name', $lastname );
        } else {
            delete_user_meta( $userID, 'last_name' );
        }
    }
    
    if( isset( $_POST['userlangs'] ) ) {
        // Update Language
        if ( !empty( $_POST['userlangs'] ) ) {
            $userlangs = sanitize_text_field( $_POST['userlangs'] );
            update_user_meta( $userID, 'fave_author_language', $userlangs );
        } else {
            delete_user_meta( $userID, 'fave_author_language' );
        }
    }
    
    if( isset( $_POST['user_company'] ) ) {
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
    }
    
    if( isset( $_POST['title'] ) ) {
        // Update Title
        if ( !empty( $_POST['title'] ) ) {
            $title = sanitize_text_field( $_POST['title'] );
            update_user_meta( $userID, 'fave_author_title', $title );
        } else {
            delete_user_meta( $userID, 'fave_author_title' );
        }
    }

    if( isset( $_POST['bio'] ) ) {
        // Update About
        if ( !empty( $_POST['bio'] ) ) {
            $about = wp_kses_post(  wpautop( wptexturize( $_POST['bio'] ) ) );
            update_user_meta( $userID, 'description', $about );
        } else {
            delete_user_meta( $userID, 'description' );
        }
    }
    
    if( isset( $_POST['userphone'] ) ) {
        // Update Phone
        if ( !empty( $_POST['userphone'] ) ) {
            $userphone = sanitize_text_field( $_POST['userphone'] );
            update_user_meta( $userID, 'fave_author_phone', $userphone );
        } else {
            delete_user_meta( $userID, 'fave_author_phone' );
        }
    }
    
    if( isset( $_POST['fax_number'] ) ) {
        // Update Fax
        if ( !empty( $_POST['fax_number'] ) ) {
            $fax_number = sanitize_text_field( $_POST['fax_number'] );
            update_user_meta( $userID, 'fave_author_fax', $fax_number );
        } else {
            delete_user_meta( $userID, 'fave_author_fax' );
        }
    }

    if( isset( $_POST['id_number'] ) ) {
        // Update id number
        if ( !empty( $_POST['id_number'] ) ) {
            $id_number = sanitize_text_field( $_POST['id_number'] );
            update_user_meta( $userID, 'aqar_author_id_number', $id_number );
        } else {
            delete_user_meta( $userID, 'aqar_author_id_number' );
        }
    }
    if( isset( $_POST['ad_number'] ) ) {
        // Update ad number
        if ( !empty( $_POST['ad_number'] ) ) {
            $ad_number = sanitize_text_field( $_POST['ad_number'] );
            update_user_meta( $userID, 'aqar_author_ad_number', $ad_number );
        } else {
            delete_user_meta( $userID, 'aqar_author_ad_number' );
        }
    }

    if( isset( $_POST['aqar_author_type_id'] ) ) {
        // type_id
        if ( !empty( $_POST['aqar_author_type_id'] ) ) {
            $ad_number = sanitize_text_field( $_POST['aqar_author_type_id'] );
            update_user_meta( $userID, 'aqar_author_type_id', $ad_number );
        } else {
            delete_user_meta( $userID, 'aqar_author_type_id' );
        }
    }

    if( isset( $_POST['service_areas'] ) ) {
        // fave_author_service_areas
        if ( !empty( $_POST['service_areas'] ) ) {
            $service_areas = sanitize_text_field( $_POST['service_areas'] );
            update_user_meta( $userID, 'fave_author_service_areas', $service_areas );
        } else {
            delete_user_meta( $userID, 'fave_author_service_areas' );
        }
    }
    
    if( isset( $_POST['specialties'] ) ) {
        // Specialties
        if ( !empty( $_POST['specialties'] ) ) {
            $specialties = sanitize_text_field( $_POST['specialties'] );
            update_user_meta( $userID, 'fave_author_specialties', $specialties );
        } else {
            delete_user_meta( $userID, 'fave_author_specialties' );
        }
    }

    if( isset( $_POST['usermobile'] ) ) {
        // Update Mobile
        if ( !empty( $_POST['usermobile'] ) ) {
            $usermobile = sanitize_text_field( $_POST['usermobile'] );
            update_user_meta( $userID, 'fave_author_mobile', $usermobile );
        } else {
            delete_user_meta( $userID, 'fave_author_mobile' );
        }
    }

    if( isset( $_POST['whatsapp'] ) ) {
            // Update WhatsApp
            if ( !empty( $_POST['whatsapp'] ) ) {
                $whatsapp = sanitize_text_field( $_POST['whatsapp'] );
                update_user_meta( $userID, 'fave_author_whatsapp', $whatsapp );
            } else {
                delete_user_meta( $userID, 'fave_author_whatsapp' );
            }
        }

    if( isset( $_POST['userskype'] ) ) {    
        // Update Skype
        if ( !empty( $_POST['userskype'] ) ) {
            $userskype = sanitize_text_field( $_POST['userskype'] );
            update_user_meta( $userID, 'fave_author_skype', $userskype );
        } else {
            delete_user_meta( $userID, 'fave_author_skype' );
        }
    }

    if( isset( $_POST['facebook'] ) ) {
   
        // Update facebook
        if ( !empty( $_POST['facebook'] ) ) {
            $facebook = sanitize_text_field( $_POST['facebook'] );
            update_user_meta( $userID, 'fave_author_facebook', $facebook );
        } else {
            delete_user_meta( $userID, 'fave_author_facebook' );
        }
    }

    if( isset( $_POST['twitter'] ) ) {    
        // Update twitter
        if ( !empty( $_POST['twitter'] ) ) {
            $twitter = sanitize_text_field( $_POST['twitter'] );
            update_user_meta( $userID, 'fave_author_twitter', $twitter );
        } else {
            delete_user_meta( $userID, 'fave_author_twitter' );
        }
    }

    if( isset( $_POST['linkedin'] ) ) {    
        // Update linkedin
        if ( !empty( $_POST['linkedin'] ) ) {
            $linkedin = sanitize_text_field( $_POST['linkedin'] );
            update_user_meta( $userID, 'fave_author_linkedin', $linkedin );
        } else {
            delete_user_meta( $userID, 'fave_author_linkedin' );
        }
    }

    if( isset( $_POST['instagram'] ) ) {    
        // Update instagram
        if ( !empty( $_POST['instagram'] ) ) {
            $instagram = sanitize_text_field( $_POST['instagram'] );
            update_user_meta( $userID, 'fave_author_instagram', $instagram );
        } else {
            delete_user_meta( $userID, 'fave_author_instagram' );
        }
    }
    if( isset( $_POST['pinterest'] ) ) {    
        // Update pinterest
        if ( !empty( $_POST['pinterest'] ) ) {
            $pinterest = sanitize_text_field( $_POST['pinterest'] );
            update_user_meta( $userID, 'fave_author_pinterest', $pinterest );
        } else {
            delete_user_meta( $userID, 'fave_author_pinterest' );
        }
    }

    if( isset( $_POST['youtube'] ) ) {    
        // Update youtube
        if ( !empty( $_POST['youtube'] ) ) {
            $youtube = sanitize_text_field( $_POST['youtube'] );
            update_user_meta( $userID, 'fave_author_youtube', $youtube );
        } else {
            delete_user_meta( $userID, 'fave_author_youtube' );
        }
    }

    if( isset( $_POST['vimeo'] ) ) {    
        // Update vimeo
        if ( !empty( $_POST['vimeo'] ) ) {
            $vimeo = sanitize_text_field( $_POST['vimeo'] );
            update_user_meta( $userID, 'fave_author_vimeo', $vimeo );
        } else {
            delete_user_meta( $userID, 'fave_author_vimeo' );
        }
    }

    if( isset( $_POST['googleplus'] ) ) {    
        // Update Googleplus
        if ( !empty( $_POST['googleplus'] ) ) {
            $googleplus = sanitize_text_field( $_POST['googleplus'] );
            update_user_meta( $userID, 'fave_author_googleplus', $googleplus );
        } else {
            delete_user_meta( $userID, 'fave_author_googleplus' );
        }
    }

    if( isset( $_POST['website'] ) ) {    
        // Update website
        if ( !empty( $_POST['website'] ) ) {
            $website = sanitize_text_field( $_POST['website'] );
            wp_update_user( array( 'ID' => $userID, 'user_url' => $website ) );
        } else {
            $website = '';
            wp_update_user( array( 'ID' => $userID, 'user_url' => $website ) );
        }
    }

    if( isset( $_POST['license'] ) ) {    
        //For agency Role
        if ( !empty( $_POST['license'] ) ) {
            $license = sanitize_text_field( $_POST['license'] );
            update_user_meta( $userID, 'fave_author_license', $license );
        } else {
            delete_user_meta( $userID, 'fave_author_license' );
        }
    }
    
    if( isset( $_POST['tax_number'] ) ) {    

        if ( !empty( $_POST['tax_number'] ) ) {
            $tax_number = sanitize_text_field( $_POST['tax_number'] );
            update_user_meta( $userID, 'fave_author_tax_no', $tax_number );
        } else {
            delete_user_meta( $userID, 'fave_author_tax_no' );
        }
    }
 
    if( isset( $_POST['user_address'] ) ) {    
        if ( !empty( $_POST['user_address'] ) ) {
            $user_address = sanitize_text_field( $_POST['user_address'] );
            update_user_meta( $userID, 'fave_author_address', $user_address );
        } else {
            delete_user_meta( $userID, 'fave_author_address' );
        }
    }
    
    if( isset( $_POST['user_location'] ) ) {    
        if ( !empty( $_POST['user_location'] ) ) {
            $user_location = sanitize_text_field( $_POST['user_location'] );
            update_user_meta( $userID, 'fave_author_google_location', $user_location );
        } else {
            delete_user_meta( $userID, 'fave_author_google_location' );
        }
    }

    $profile_pic_id = isset($_FILES['profile-pic-id'] ) ? AqarGateApi::upload_images( (array)$_FILES, 'profile-pic-id' ) : '';

    $thumbnail_url = wp_get_attachment_image_src( $profile_pic_id[0] );

    if( !empty($profile_pic_id) ){
        houzez_save_user_photo($userID, $profile_pic_id[0], $thumbnail_url);
    }

    if( isset( $_POST['useremail'] ) ) { 
        // Update email
        if( !empty( $_POST['useremail'] ) ) {
            $useremail = sanitize_email( $_POST['useremail'] );
            $useremail = is_email( $useremail );
            if( !$useremail ) {
                return json_encode( array( 'success' => false, 'msg' => esc_html__('The Email you entered is not valid. Please try again.', 'houzez') ) );

            } else {
                $email_exists = email_exists( $useremail );
                if( $email_exists ) {
                    if( $email_exists != $userID ) {
                        return json_encode( array( 'success' => false, 'msg' => esc_html__('This Email is already used by another user. Please try a different one.', 'houzez') ) );

                    }
                } else {
                    $return = wp_update_user( array ('ID' => $userID, 'user_email' => $useremail ) );
                    if ( is_wp_error( $return ) ) {
                        $error = $return->get_error_message();
                        return esc_attr( $error );

                    }
                }
        
                

                $agent_id  = get_user_meta( $userID, 'fave_author_agent_id', true );
                $agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );
                $user_as_agent = houzez_option('user_as_agent');
        
                if (in_array('houzez_agent', (array)$current_user->roles)) {
                    houzez_update_user_agent ( $agent_id, $firstname, $lastname, $title, $about, $userphone, $usermobile, $whatsapp, $userskype, $facebook, $twitter, $linkedin, $instagram, $pinterest, $youtube, $vimeo, $googleplus, $profile_pic, $profile_pic_id, $website, $useremail, $license, $tax_number, $fax_number, $userlangs, $user_address, $user_company, $service_areas, $specialties );
                } elseif(in_array('houzez_agency', (array)$current_user->roles)) {
                    houzez_update_user_agency ( $agency_id, $firstname, $lastname, $title, $about, $userphone, $usermobile, $whatsapp, $userskype, $facebook, $twitter, $linkedin, $instagram, $pinterest, $youtube, $vimeo, $googleplus, $profile_pic, $profile_pic_id, $website, $useremail, $license, $tax_number, $user_address, $user_location, $latitude, $longitude, $fax_number, $userlangs );
                }
        
            }
        }
    }

    $allowed_html = array();

    $user_role = get_option( 'default_role' );
    
    if( isset( $_POST['role'] ) && $_POST['role'] != '' ){
        $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
    } else {
        $user_role = $user_role;
    }
    
    wp_update_user( array ('ID' => $userID, 'role' => $user_role, 'display_name' => $_POST['display_name'] ) );
    return  array( 'success' => true, 'msg' => __('تم تحديث الملف الشخصي', 'houzez') );

}



/**
 * aqargate_token_after_register
 *
 * @param  mixed $var
 * @return void
 */

function aqargate_token_after_register( $username , $password ){

        $_request = new WP_REST_Request( 'POST', '/jwt-auth/v1/token' );
        $_request->set_header( 'content-type', 'application/json' );
        $_request->set_body(
            json_encode(
                [
                    'username' => $username,
                    'password' => $password,
                ]
            )
        );
        $response = rest_do_request( $_request );

        return $response->data; // this will return a token
   
}

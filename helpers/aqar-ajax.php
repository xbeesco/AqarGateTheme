<?php 

/* --------------------------------------------------------------------------
* Property delete ajax
* --------------------------------------------------------------------------- */
add_action( 'wp_ajax_nopriv_aqar_update_property', 'aqar_update_property' );
add_action( 'wp_ajax_aqar_update_property', 'aqar_update_property' );
if ( !function_exists( 'aqar_update_property' ) ) {

    function aqar_update_property()
    {

        $dashboard_listings = houzez_get_template_link_2('template/user_dashboard_properties.php');
        $dashboard_listings = add_query_arg( 'deleted', 1, $dashboard_listings );

    
        if ( !isset( $_REQUEST['prop_id'] ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'No Property ID found', 'houzez' ) );
            echo json_encode( $ajax_response );
            die;
        }

        $propID = $_REQUEST['prop_id'];
        $post_author = get_post_field( 'post_author', $propID );

        global $current_user;
        wp_get_current_user();
        $userID      =   $current_user->ID;

        if ( $post_author == $userID ) {

            update_post_meta($propID, 'adverst_update_reason', $_REQUEST['content']);   
            update_post_meta($propID, 'adverst_can_edit', 1);          
            
            $ajax_response = array( 'success' => true , 'redirect' => $_REQUEST['edit_link'], 'mesg' => 'جاري المعالجة' );
            echo json_encode( $ajax_response );
            die;
        } else {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'Permission denied', 'houzez' ) );
            echo json_encode( $ajax_response );
            die;
        }

    }

}


/* --------------------------------------------------------------------------
* Property cancel ajax
* --------------------------------------------------------------------------- */
add_action( 'wp_ajax_nopriv_aqar_cancel_property', 'aqar_cancel_property' );
add_action( 'wp_ajax_aqar_cancel_property', 'aqar_cancel_property' );
if ( !function_exists( 'aqar_cancel_property' ) ) {

    function aqar_cancel_property()
    {

        $dashboard_listings = houzez_get_template_link_2('template/user_dashboard_properties.php');
        $dashboard_listings = add_query_arg( 'deleted', 1, $dashboard_listings );

    
        if ( !isset( $_REQUEST['prop_id'] ) ) {
            $ajax_response = ['success' => false, 'reason' => esc_html__('No Property ID found', 'houzez')];
            echo json_encode( $ajax_response );
            die;
        }

        $propID = $_REQUEST['prop_id'];
        $post_author = get_post_field( 'post_author', $propID );

        global $current_user;
        wp_get_current_user();
        $userID      =   $current_user->ID;
        $AdLinkInPlatform = get_the_permalink($propID);

        if ( $post_author == $userID ) {
            $jayParsedAry = [
                "platformOwnerId" => get_option( '_platformownerid' ), //Mandatory
                "platformId" => get_option( '_platformid' ), //Mandatory
                "operationType" => "CancelAd", //Mandatory
                "operationReason" => get_post_meta( $propID, 'adverst_update_reason', true ) ?? "Other", //Mandatory
                "adLinkInPlatform" => "' . $AdLinkInPlatform . '", //Mandatory
                "adLicenseNumber" => get_post_meta( $propID, 'adLicenseNumber', true ) ?? "", //Mandatory
                "brokerageAndMarketingLicenseNumber" => get_post_meta( $propID, 'brokerageAndMarketingLicenseNumber', true ) ?? "", 
                "titleDeedNumber" => get_post_meta( $propID, 'deedNumber', true ) ?? "", //Mandatory
                "creationDate" => "2024-02-15T09:44:34.057Z", //Mandatory
                "endDate" => "2024-08-01T09:44:34.057Z", //Mandatory
                "advertiserId" => get_post_meta( $propID, 'advertiserId', true ) ?? "", //Mandatory
                "advertiserName " => "sherif ali saad", 
                "advertiserMobile" => "0548241599", 
                "channels" => [
                   "LicensedPlatform" //Mandatory
                ], 
                "nationalAddress" => [
                   "region" => "", //Mandatory
                   "city" => "", //Mandatory
                   "district" => "", //Mandatory
                   "postalCode" => 99999, 
                   "streetName" => "", 
                   "buildingNo" => 1231, 
                   "additionalNo" => 1111, 
                   "adMapLongitude" => null, 
                   "adMapLatitude" => null 
                ], 
                "propertyFace" => "Western", 
                "propertyType" => "Hotel", //Mandatory
                "propertyArea" => 525, //Mandatory
                "propertyUsage" => [
                   "Commercial" 
                ], 
                "streetWidth" => 1564, 
                "propertyAge" => "FiveYears", 
                "price" => 12000,  //Mandatory
                "roomsNumber" => 5, 
                "propertyUtilities" => [ //Mandatory
                   "Electricity", 
                   "Waters", 
                   "Sanitation", 
                   "FixedPhone", 
                   "FibreOptics", 
                   "FloodDrainage" 
                ], 
                "adType" => "Sell", //Mandatory
                "constraints" => "True", //Mandatory
                "obligationsOnTheProperty" => "452543857", 
                "guaranteesAndTheirDuration" => "452543857", 
                "planNumber" => "2351", 
                "landNumber" => "8461 / 2", 
                "complianceWithTheSaudiBuildingCode" => true, 
                "qrCode" => "", 
                "titleDeedType" => "ElectronicDeed", 
                "northLimitName" => "", 
                "northLimitDescription" => "", 
                "northLimitLengthChar" => "", 
                "eastLimitName" => "", 
                "eastLimitDescription" => "", 
                "eastLimitLengthChar" => "", 
                "westLimitName" => "", 
                "westLimitDescription" => "", 
                "westLimitLengthChar" => "", 
                "southLimitName" => "", 
                "southLimitDescription" => "", 
                "southLimitLengthChar" => "", 
                "borders" => [
                   [
                      "direction" => "", 
                      "type" => "", 
                      "length" => "30" 
                   ] 
                ], 
                "adSource" => "REGA", //Mandatory
                "landTotalPrice" => null, 
                "totalAnnualRentForTheLand" => null, 
                "notes" => "452543857", 
                "locationDescriptionAccordingToDeed" => "string" 
             ]; 
    
    
            require_once ( AG_DIR . 'module/class-rega-module.php' );
    
            $RegaMoudle = new RegaMoudle();
    
            $response = $RegaMoudle->PlatformCompliance($jayParsedAry);
            $response = json_decode( $response );

                     
            
            if( isset($response->response) && $response->response === true ){

                update_post_meta( $propID, 'adverst_cancel_reason', $_REQUEST['content'] );   
                update_post_meta( $propID, 'adverst_can_edit', 0 ); 
    
                echo json_encode(['success' => true, 'reason' => '']);
                wp_die();
    
            } else if( isset($response->response) && $response->response === false ) {
                echo json_encode(['success' => false, 'reason' => $response->message] );
                wp_die(); 
            } else {
                if( isset($response->httpCode)  ) {
                    echo json_encode(['success' => false, 'reason' => $response->httpMessage . ' ' . $response->moreInformation] );
                    wp_die(); 
                }
            }
        } else {
            $ajax_response = ['success' => false, 'reason' => esc_html__('Permission denied', 'houzez')];
            echo json_encode( $ajax_response );
            die;
        }

    }

}
/* -----------------------------------------------------------------------------------------------------------
 *  draft property
 -------------------------------------------------------------------------------------------------------------*/
 add_action( 'wp_ajax_aqargate_property_draft', 'aqargate_property_draft' );
 if( !function_exists('aqargate_property_draft') ) {
 
     function aqargate_property_draft()
     {
 
         global $current_user;
         $prop_id = intval($_POST['propID']);
 
         wp_get_current_user();
         $userID = $current_user->ID;
         $post = get_post($prop_id);
 
         if ($post->post_author != $userID) {
             wp_die('no kidding');
         }
 
         $available_listings = get_user_meta($userID, 'package_listings', true);
 
         //if ($available_listings > 0 || $available_listings == -1) {
             
             $post_status = get_post_status( $prop_id );
 
             if($post_status == 'publish') { 
                 $post = array(
                     'ID'            => $prop_id,
                     'post_status'   => 'draft'
                 );
                 /*if ($available_listings != -1) { // if !unlimited
                     update_user_meta($userID, 'package_listings', $available_listings + 1);
                 }*/
             } elseif ($post_status == 'draft') {
                 $post = array(
                     'ID'            => $prop_id,
                     'post_status'   => 'publish'
                 );
                 /*if ($available_listings != -1) { // if !unlimited
                     update_user_meta($userID, 'package_listings', $available_listings - 1);
                 }*/
             }
             $prop_id =  wp_update_post($post);
 
             echo json_encode(array('success' => true, 'msg' => esc_html__('Listings set on hold', 'houzez')));
 
         /*} else {
             echo json_encode(array('success' => false, 'msg' => esc_html__('No listings available', 'houzez')));
             wp_die();
         }*/
         wp_die();
 
     }
 }

 /* -----------------------------------------------------------------------------------------------------------
 *  draft property
 -------------------------------------------------------------------------------------------------------------*/
add_action( 'wp_ajax_aqargate_edit_api_property', 'aqargate_edit_api_property' );
if( !function_exists('aqargate_edit_api_property') ) {
 
    /**
     * Summary of aqargate_edit_api_property
     * @return void
     */
    function aqargate_edit_api_property()
    {
 
        global $current_user;
        $prop_id = intval($_POST['propID']);
        $serializedData = $_POST['formData']; // 'formData' is the key name sent from AJAX
        // Process the serialized form data
        parse_str($serializedData, $formDataArray);
        $userID       = get_current_user_id();
        $id_number    = get_the_author_meta( 'aqar_author_id_number' , $userID );
        $display_name = get_the_author_meta( 'aqar_display_name' , $userID );
        if( empty($display_name) ) {
            $display_name = $current_user->display_name;
        }
        
        if( houzez_is_agency() ) {
            $user_agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );

            if( !empty( $user_agency_id ) ) {
                $display_name = get_the_title($user_agency_id);
            }else if( !empty( get_the_author_meta( 'fave_author_company' , $userID ) ) ) {
                $display_name = get_the_author_meta( 'fave_author_company' , $userID );
            }

            $unified_number = get_the_author_meta( 'aqar_author_unified_number' , $userID );

            $advertiserId = !empty($unified_number) ? $unified_number : $id_number;
            $advertiserName = $display_name;
        } else {
            $advertiserId = $id_number;
            $advertiserName = $display_name;
        }


         $AdLinkInPlatform = get_the_permalink($prop_id); 

        $jayParsedAry = [
            "platformOwnerId" => get_option( '_platformownerid' ), //Mandatory
            "platformId" => get_option( '_platformid' ), //Mandatory
            "operationType" => "UpdateAd", //Mandatory
            "operationReason" => get_post_meta( $prop_id, 'adverst_update_reason', true ) ?? "Other", //Mandatory
            "adLinkInPlatform" => "{$AdLinkInPlatform}", //Mandatory
            "adLicenseNumber" => get_post_meta( $prop_id, 'adLicenseNumber', true ) ?? "", //Mandatory
            "brokerageAndMarketingLicenseNumber" => get_post_meta( $prop_id, 'brokerageAndMarketingLicenseNumber', true ) ?? "", 
            "titleDeedNumber" => get_post_meta( $prop_id, 'deedNumber', true ) ?? "", //Mandatory
            "creationDate" => get_post_meta( $prop_id, 'creationDate', true ), //Mandatory
            "endDate" => get_post_meta( $prop_id, 'endDate', true ), //Mandatory
            "advertiserId" => $advertiserId, //Mandatory
            "advertiserName" => $advertiserName, 
            "advertiserMobile" => get_the_author_meta( 'fave_author_mobile' , $userID ), 
            "channels" => [
               "LicensedPlatform" //Mandatory
            ], 
            "nationalAddress" => [
               "region" => (new PropertyMoudle)->locations_number_name($formDataArray["administrative_area_level_1"], false, 'property_state'), //Mandatory
               "city" => (new PropertyMoudle)->locations_number_name($formDataArray["locality"], false, 'property_city'), //Mandatory
               "district" => (new PropertyMoudle)->locations_number_name($formDataArray["neighborhood"], false, 'property_area'), //Mandatory
               "postalCode" => $formDataArray["postal_code"],  
               "adMapLongitude" => $formDataArray['lng'], 
               "adMapLatitude" => $formDataArray['lat'] 
            ], 
            // "propertyFace" => "Western", 
            "propertyType" => $formDataArray['prop_type'][0], //Mandatory
            "propertyArea" => intval($formDataArray['prop_size']), //Mandatory
            // "propertyUsage" => [
            //    "Commercial" 
            // ], ..
            "streetWidth" => intval($formDataArray['d8b9d8b1d8b6-d8a7d984d8b4d8a7d8b1d8b9']), 
            "propertyAge" => "FiveYears", 
            "price" => intval($formDataArray['prop_price']),  //Mandatory
            // "roomsNumber" => 5, 
            "propertyUtilities" => [ //Mandatory
               "Electricity", 
               "Waters", 
               "Sanitation", 
               "FixedPhone", 
               "FibreOptics", 
               "FloodDrainage" 
            ], 
            "adType" => "Sell", //Mandatory
            "constraints" => "True", //Mandatory
            // "obligationsOnTheProperty" => "", 
            // "guaranteesAndTheirDuration" => "", 
            "planNumber" => $formDataArray['d8b1d982d985-d8a7d984d985d8aed8b7d8b7'],   
            "landNumber" => "8461 / 2", 
            "complianceWithTheSaudiBuildingCode" => true, 
            "qrCode" => "", 
            "titleDeedType" => "ElectronicDeed", 
            "northLimitName" => $formDataArray['borders']['northLimitName'], 
            "northLimitDescription" => $formDataArray['borders']['northLimitDescription'], 
            "northLimitLengthChar" => $formDataArray['borders']['northLimitLengthChar'], 
            "eastLimitName" => $formDataArray['borders']['eastLimitName'], 
            "eastLimitDescription" => $formDataArray['borders']['eastLimitDescription'], 
            "eastLimitLengthChar" => $formDataArray['borders']['eastLimitLengthChar'], 
            "westLimitName" => $formDataArray['borders']['westLimitName'], 
            "westLimitDescription" => $formDataArray['borders']['westLimitDescription'], 
            "westLimitLengthChar" => $formDataArray['borders']['westLimitLengthChar'], 
            "southLimitName" => $formDataArray['borders']['southLimitName'], 
            "southLimitDescription" => $formDataArray['borders']['southLimitDescription'], 
            "southLimitLengthChar" => $formDataArray['borders']['southLimitLengthChar'], 
            // "borders" => [
            //    [
            //       "direction" => "", 
            //       "type" => "", 
            //       "length" => "30" 
            //    ] 
            // ], 
            "adSource" => "REGA", //Mandatory
            "landTotalPrice" => intval($formDataArray['prop_price']), 
            "totalAnnualRentForTheLand" => null, 
            // "notes" => "452543857", 
            // "locationDescriptionAccordingToDeed" => "string" 
         ]; 


        require_once ( AG_DIR . 'module/class-rega-module.php' );

        $RegaMoudle = new RegaMoudle();

        $response = $RegaMoudle->PlatformCompliance($jayParsedAry);
        $response = json_decode( $response );

        // print_r($response);
        // print_r($jayParsedAry);

        if( !aqar_can_edit($prop_id) ){
            echo json_encode(['success' => false, 'reason' => 'التعديل من خلال صفحة كل العقارات الخاصة بكم'] );
            wp_die();
        }

        if( isset($response->response) && $response->response === true ){
            update_post_meta($propID, 'adverst_can_edit', 0);  
            echo json_encode(['success' => true, 'reason' => '']);
            wp_die();

        } else if( isset($response->response) && $response->response === false ) {
            echo json_encode(['success' => false, 'reason' => $response->message] );
            wp_die(); 
        } else {
            if( isset($response->httpCode)  ) {
                echo json_encode(['success' => false, 'reason' => $response->httpMessage . ' ' . $response->moreInformation] );
                wp_die(); 
            }elseif( isset($response->Header) ){    
                echo json_encode(['success' => false, 'reason' => $response->Header->Status->Description] );
                wp_die(); 
            }
        }
    }
}
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
            update_post_meta($propID, 'adverst_can_edit', 0);          
            
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
            $advertisement_response = get_post_meta($propID, 'advertisement_response', true);

        if( empty($advertisement_response) ){
            echo json_encode(['success' => false, 'reason' => 'لم يتم التعديل من خلال الربط التقني المحدث'] );
            wp_die();
        }

        // جلب القيم من المصفوفة
        $advertiserId = $advertisement_response['advertiserId'];
        $adLicenseNumber = $advertisement_response['adLicenseNumber'];
        $deedNumber = $advertisement_response['deedNumber'];
        $advertiserName = $advertisement_response['advertiserName'];
        // Remove any non-numeric characters from the phone number
        $phoneNumber = preg_replace('/\D/', '', $advertisement_response['phoneNumber']);
        $brokerageAndMarketingLicenseNumber = $advertisement_response['brokerageAndMarketingLicenseNumber'];
        $isConstrained = $advertisement_response['isConstrained'];
        $isPawned = $advertisement_response['isPawned'];
        $isHalted = $advertisement_response['isHalted'];
        $isTestment = $advertisement_response['isTestment'];
        $rerConstraints = $advertisement_response['rerConstraints'];
        $streetWidth = $advertisement_response['streetWidth'];
        $propertyArea = $advertisement_response['propertyArea'];
        $propertyPrice = $advertisement_response['propertyPrice'];
        $landTotalPrice = $advertisement_response['landTotalPrice'];
        $landTotalAnnualRent = $advertisement_response['landTotalAnnualRent'];
        $propertyType = $advertisement_response['propertyType'];
        $propertyAge = $advertisement_response['propertyAge'];
        $advertisementType = $advertisement_response['advertisementType'];
        $creationDate = $advertisement_response['creationDate'];
        $endDate = $advertisement_response['endDate'];
        $adLicenseUrl = $advertisement_response['adLicenseUrl'];
        $adSource = $advertisement_response['adSource'];
        $titleDeedTypeName = $advertisement_response['titleDeedTypeName'];
        $locationDescriptionOnMOJDeed = $advertisement_response['locationDescriptionOnMOJDeed'];
        $notes = $advertisement_response['notes'];
        $channels = $advertisement_response['channels'];
        
        $guaranteesAndTheirDuration = $advertisement_response['guaranteesAndTheirDuration'];
        // جلب القيم من المصفوفة المضمنة (borders)
        $borders = $advertisement_response['borders'];

        // Mapping between Arabic property utility names and their codes
        $propertyUtilitiesMapping = array(
            'كهرباء' => 'Electricity',
            'مياه' => 'Waters',
            'صرف صحي' => 'Sanitation',
            'لايوجد خدمات' => 'NoServices',
            'هاتف' => 'FixedPhone',
            'الياف ضوئية' => 'FibreOptics',
            'تصريف الفيضانات' => 'FloodDrainage'
        );

        $advertisementTypeMapping = [
            'إيجار' => 'Rent',
            'بيع' => 'Sell',
        ];

        $property_status = get_term_by( 'id', $_POST['prop_status'][0], 'property_status' );
        $property_statusName = $property_status->name;
        $property_statusName  = isset($advertisementTypeMapping[$property_statusName]) ? $advertisementTypeMapping[$property_statusName] : 'Sell';
 
        $user_title             =   get_the_author_meta( 'fave_author_title' , $userID );
        $display_name           =   get_the_author_meta( 'aqar_display_name' , $userID );
        if( empty($display_name) ) {
            $display_name = $current_user->display_name;
        }
        if( empty($user_title) ){
            $user_title = $display_name;
        }
        $advertiserMobile = get_the_author_meta( 'fave_author_mobile' , $userID );
        $pattern = '/^\+\s*966/';
        // Remove +966 if it exists at the start
        $cleanedPhoneNumber = preg_replace($pattern, '', $advertiserMobile);

        // Add leading zero if it doesn't already start with one
        if (substr($cleanedPhoneNumber, 0, 1) !== '0') {
            $cleanedPhoneNumber = '0' . $cleanedPhoneNumber;
        }
        // prr($advertisement_response);
        $adLinkInPlatform = esc_url(get_the_permalink($propID));

        if( !empty($isConstrained) || !empty($isPawned) || !empty($isHalted) || !empty($isTestment) || !empty($rerConstraints) ) {
            $constraints = "True";
        }else {
            $constraints = "False";

        }

        // جلب القيم من المصفوفة المضمنة (location)
        $region = $advertisement_response['location']['region'] ?? 'null';
        $regionCode = $advertisement_response['location']['regionCode'] ?? 'null';
        $city = $advertisement_response['location']['city'] ?? 'null';
        $cityCode = $advertisement_response['location']['cityCode'] ?? 'null';
        $district = $advertisement_response['location']['district'] ?? 'null';
        $districtCode = $advertisement_response['location']['districtCode'] ?? 'null';
        $street = $advertisement_response['location']['street'] ?? 'null';
        $postalCode = $advertisement_response['location']['postalCode'] ?? 'null';
        $buildingNumber = $advertisement_response['location']['buildingNumber'] ?? 'null';
        $additionalNumber = $advertisement_response['location']['additionalNumber'] ?? 'null';
        $longitude = $advertisement_response['location']['longitude'] ?? 'null';
        $latitude = $advertisement_response['location']['latitude'] ?? 'null';
        $operationReason = $_POST['content'] ?? "Other"; 


        $property_type = get_the_terms( $propID, 'property_type');

        $property_typeName = $property_type[0]->name;
        
        $mappingPropertyTypes = [
            'أرض' => 'Land',
            'ارض' => 'Land',
            'دور' => 'Floor',
            'شقة' => 'Apartment',
            'فيلا' => 'Villa',
            'شقَّة صغيرة (استوديو)' => 'Studio',
            'غرفة' => 'Room',
            'استراحة' => 'RestHouse',
            'مجمع' => 'Compound',
            'برج' => 'Tower',
            'معرض' => 'Exhibition',
            'مكتب' => 'Office',
            'مستودع' => 'Warehouses',
            'كشك' => 'Booth',
            'سينما' => 'Cinema',
            'فندق' => 'Hotel',
            'مواقف سيارات' => 'CarParking',
            'ورشة' => 'RepairShop',
            'صراف' => 'Teller',
            'مصنع' => 'Factory',
            'مدرسة' => 'School',
            'مستشفى، مركز صحي' => 'HospitalOrHealthCenter',
            'محطة كهرباء' => 'ElectricityStation',
            'برج اتصالات' => 'TelecomTower',
            'محطة' => 'Station',
            'مزرعة' => 'Farm',
            'عمارة' => 'Building'
        ];


        $property_typeName = isset($mappingPropertyTypes[$property_typeName]) ? $mappingPropertyTypes[$property_typeName] : 'Land';

        $mappedUtilities = array();
        foreach ($advertisement_response['propertyUtilities'] as $utility) {
            if (isset($propertyUtilitiesMapping[$utility])) {
                $mappedUtilities[] = $propertyUtilitiesMapping[$utility];
            } else {
                // If the Arabic utility name doesn't exist in the mapping
                $mappedUtilities[] = 'NoServices';
            }
        }

        // Function to add double quotes around each item
        $quotedUtilities = array_map(function($item) {
            return '"' . $item . '"';
        }, $mappedUtilities);

        // Implode the array to create a string
        $implodedUtilities = implode(', ', $quotedUtilities);

        $advertisementTypeMapping = [
            'إيجار' => 'Rent',
            'بيع' => 'Sell',
        ];
        $property_status = get_the_terms( $propID, 'property_status');
        $property_statusName = $property_status[0]->name;
        $property_statusName  = isset($advertisementTypeMapping[$property_statusName]) ? $advertisementTypeMapping[$property_statusName] : 'Sell';
        

        $advertisement_request = '{
            "adLicenseNumber": "7200001037",
            "adLinkInPlatform": "'.$adLinkInPlatform.'",
            "adSource": "REGA",
            "adType": "'.$property_statusName.'",
            "advertiserId": "'.$advertiserId.'",
            "advertiserMobile": "'.$cleanedPhoneNumber.'",
            "advertiserName": "'.$user_title.'",
            "brokerageAndMarketingLicenseNumber": "'.$brokerageAndMarketingLicenseNumber.'",
            "channels": [
                "LicensedPlatform"
            ],
            "constraints": "'.$constraints.'",
            "creationDate": "'.$creationDate.'",
            "endDate": "'.$endDate.'",
            "nationalAddress": {
                "additionalNo": '.$additionalNumber.',
                "adMapLatitude": '.$latitude.',
                "adMapLongitude": '.$longitude.',
                "buildingNo": '.$buildingNumber.',
                "city": "'.$city.'",
                "district": "'.$district.'",
                "postalCode": '. $postalCode .',
                "region": "'.$region.'",
                "streetName": "'.$street.'"
            },
            "operationReason": "'.$operationReason.'",
            "operationType": "UpdateAd",
            "platformId": "'.get_option( '_platformid' ).'",
            "platformOwnerId": "'.get_option( '_platformownerid' ).'",
            "price": '.intval(get_post_meta( $propID, 'fave_property_price', true)).',
            "propertyArea": '.$propertyArea.',
            "propertyType": "'.$property_typeName.'",
            "propertyUsage": [
                "Commercial"
            ],
            "propertyUtilities": ['. $implodedUtilities .'],
            "qrCode": "",
            "titleDeedNumber": "'.$deedNumber.'",
            "titleDeedType": "ElectronicDeed",
        }';
    
    
            require_once ( AG_DIR . 'module/class-rega-module.php' );
    
            $RegaMoudle = new RegaMoudle();
    
            $response = $RegaMoudle->PlatformCompliance($advertisement_request);
            $response = json_decode( $response );

            
            if( isset($response->Body) && $response->Body->result->response === true  ){

                update_post_meta( $propID, 'adverst_cancel_reason', $_REQUEST['content'] );   
                update_post_meta( $propID, 'adverst_can_edit', 0 ); 
                $post_status = get_post_status( $propID );
                if($post_status == 'publish') { 
                    $post = array(
                        'ID'            => $propID,
                        'post_status'   => 'draft'
                    );
                    wp_update_post($post);
                } 
    
                echo json_encode(['success' => true, 'reason' => 'تم ارسال الغاء الاعلان بنجاح الي الهيئة العقارية']);
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
 
        $AdLinkInPlatform = get_the_permalink($prop_id); 

        $advertisement_response = get_post_meta($prop_id, 'advertisement_response', true);

        if( empty($advertisement_response) ){
            echo json_encode(['success' => false, 'reason' => 'لم يتم التعديل من خلال الربط التقني المحدث'] );
            wp_die();
        }

        // جلب القيم من المصفوفة
        $advertiserId = $advertisement_response['advertiserId'];
        $adLicenseNumber = $advertisement_response['adLicenseNumber'];
        $deedNumber = $advertisement_response['deedNumber'];
        $advertiserName = $advertisement_response['advertiserName'];
        // Remove any non-numeric characters from the phone number
        $phoneNumber = preg_replace('/\D/', '', $advertisement_response['phoneNumber']);
        $brokerageAndMarketingLicenseNumber = $advertisement_response['brokerageAndMarketingLicenseNumber'];
        $isConstrained = $advertisement_response['isConstrained'];
        $isPawned = $advertisement_response['isPawned'];
        $isHalted = $advertisement_response['isHalted'];
        $isTestment = $advertisement_response['isTestment'];
        $rerConstraints = $advertisement_response['rerConstraints'];
        $streetWidth = $advertisement_response['streetWidth'];
        $propertyArea = $advertisement_response['propertyArea'];
        $propertyPrice = $advertisement_response['propertyPrice'];
        $landTotalPrice = $advertisement_response['landTotalPrice'];
        $landTotalAnnualRent = $advertisement_response['landTotalAnnualRent'];
        $propertyType = $advertisement_response['propertyType'];
        $propertyAge = $advertisement_response['propertyAge'];
        $advertisementType = $advertisement_response['advertisementType'];
        $creationDate = $advertisement_response['creationDate'];
        $endDate = $advertisement_response['endDate'];
        $adLicenseUrl = $advertisement_response['adLicenseUrl'];
        $adSource = $advertisement_response['adSource'];
        $titleDeedTypeName = $advertisement_response['titleDeedTypeName'];
        $locationDescriptionOnMOJDeed = $advertisement_response['locationDescriptionOnMOJDeed'];
        $notes = $advertisement_response['notes'];
        $channels = $advertisement_response['channels'];
        
        $guaranteesAndTheirDuration = $advertisement_response['guaranteesAndTheirDuration'];
        // جلب القيم من المصفوفة المضمنة (borders)
        $borders = $advertisement_response['borders'];

        // Mapping between Arabic property utility names and their codes
        $propertyUtilitiesMapping = array(
            'كهرباء' => 'Electricity',
            'مياه' => 'Waters',
            'صرف صحي' => 'Sanitation',
            'لايوجد خدمات' => 'NoServices',
            'هاتف' => 'FixedPhone',
            'الياف ضوئية' => 'FibreOptics',
            'تصريف الفيضانات' => 'FloodDrainage'
        );

        $advertisementTypeMapping = [
            'إيجار' => 'Rent',
            'بيع' => 'Sell',
        ];
      
		$current_property_price = $advertisement_response['propertyPrice'];
        $new_property_price = intval($formDataArray['prop_price']);
      
        $property_status = get_term_by('id', $formDataArray['prop_status'][0], 'property_status');
        $property_statusName = $property_status->name;
      
      	// Check if the values have changed
        if ($property_statusName == $advertisement_response['advertisementType'] && $current_property_price == $new_property_price) {
            echo json_encode(['success' => true, 'reason' => 'لا حاجة لارسال الاعلان للهيئة العقارية . جاري تحديث الاعلان !']);
            wp_die();
        }
      
        $property_statusName = $advertisementTypeMapping[$property_statusName] ?? 'Sell';

        $user_title             =   get_the_author_meta( 'fave_author_title' , $userID );
        $display_name           =   get_the_author_meta( 'aqar_display_name' , $userID );
        if( empty($display_name) ) {
            $display_name = $current_user->display_name;
        }
        if( empty($user_title) ){
            $user_title = $display_name;
        }
        $advertiserMobile = get_the_author_meta( 'fave_author_mobile' , $userID );
        $pattern = '/^\+\s*966/';
        // Remove +966 if it exists at the start
        $cleanedPhoneNumber = preg_replace($pattern, '', $advertiserMobile);

        // Add leading zero if it doesn't already start with one
        if (substr($cleanedPhoneNumber, 0, 1) !== '0') {
            $cleanedPhoneNumber = '0' . $cleanedPhoneNumber;
        }
        // prr($advertisement_response);
        $adLinkInPlatform = esc_url(get_the_permalink($prop_id));

        if( !empty($isConstrained) || !empty($isPawned) || !empty($isHalted) || !empty($isTestment) || !empty($rerConstraints) ) {
            $constraints = "True";
        }else {
            $constraints = "False";

        }

        // جلب القيم من المصفوفة المضمنة (location)
        $region = $advertisement_response['location']['region'] ?? 'null';
        $regionCode = $advertisement_response['location']['regionCode'] ?? 'null';
        $city = $advertisement_response['location']['city'] ?? 'null';
        $cityCode = $advertisement_response['location']['cityCode'] ?? 'null';
        $district = $advertisement_response['location']['district'] ?? 'null';
        $districtCode = $advertisement_response['location']['districtCode'] ?? 'null';
        $street = $advertisement_response['location']['street'] ?? 'null';
        $postalCode = $advertisement_response['location']['postalCode'] ?? 'null';
        $buildingNumber = $advertisement_response['location']['buildingNumber'] ?? 'null';
        $additionalNumber = $advertisement_response['location']['additionalNumber'] ?? 'null';
        $longitude = $advertisement_response['location']['longitude'] ?? 'null';
        $latitude = $advertisement_response['location']['latitude'] ?? 'null';
        $operationReason = !empty(get_post_meta( $prop_id, 'adverst_update_reason', true ))  ? get_post_meta( $prop_id, 'adverst_update_reason', true ) :  "Other"; 

        $property_type = get_term_by( 'id', $formDataArray['prop_type'][0], 'property_type' );
        $property_typeName = $property_type->name;
        
        $mappingPropertyTypes = [
            'أرض' => 'Land',
            'ارض' => 'Land',
            'دور' => 'Floor',
            'شقة' => 'Apartment',
            'فيلا' => 'Villa',
            'شقَّة صغيرة (استوديو)' => 'Studio',
            'غرفة' => 'Room',
            'استراحة' => 'RestHouse',
            'مجمع' => 'Compound',
            'برج' => 'Tower',
            'معرض' => 'Exhibition',
            'مكتب' => 'Office',
            'مستودع' => 'Warehouses',
            'كشك' => 'Booth',
            'سينما' => 'Cinema',
            'فندق' => 'Hotel',
            'مواقف سيارات' => 'CarParking',
            'ورشة' => 'RepairShop',
            'صراف' => 'Teller',
            'مصنع' => 'Factory',
            'مدرسة' => 'School',
            'مستشفى، مركز صحي' => 'HospitalOrHealthCenter',
            'محطة كهرباء' => 'ElectricityStation',
            'برج اتصالات' => 'TelecomTower',
            'محطة' => 'Station',
            'مزرعة' => 'Farm',
            'عمارة' => 'Building'
        ];


        $property_typeName = isset($mappingPropertyTypes[$property_typeName]) ? $mappingPropertyTypes[$property_typeName] : 'Land';

        $mappedUtilities = array();
        foreach ($advertisement_response['propertyUtilities'] as $utility) {
            if (isset($propertyUtilitiesMapping[$utility])) {
                $mappedUtilities[] = $propertyUtilitiesMapping[$utility];
            } else {
                // If the Arabic utility name doesn't exist in the mapping
                $mappedUtilities[] = 'NoServices';
            }
        }

        // Function to add double quotes around each item
        $quotedUtilities = array_map(function($item) {
            return '"' . $item . '"';
        }, $mappedUtilities);

        // Implode the array to create a string
        $implodedUtilities = implode(', ', $quotedUtilities);

        $advertisementTypeMapping = [
            'إيجار' => 'Rent',
            'بيع' => 'Sell',
        ];

        $property_status = get_term_by( 'id', $formDataArray['prop_status'][0], 'property_status' );
        $property_statusName = $property_status->name;
        $property_statusName  = isset($advertisementTypeMapping[$property_statusName]) ? $advertisementTypeMapping[$property_statusName] : 'Sell';
        

        $advertisement_request = '{
            "adLicenseNumber": "7200001037",
            "adLinkInPlatform": "'.$adLinkInPlatform.'",
            "adSource": "REGA",
            "adType": "'.$property_statusName.'",
            "advertiserId": "'.$advertiserId.'",
            "advertiserMobile": "'.$cleanedPhoneNumber.'",
            "advertiserName": "'.$user_title.'",
            "brokerageAndMarketingLicenseNumber": "'.$brokerageAndMarketingLicenseNumber.'",
            "channels": [
                "LicensedPlatform"
            ],
            "constraints": "'.$constraints.'",
            "creationDate": "'.$creationDate.'",
            "endDate": "'.$endDate.'",
            "nationalAddress": {
                "additionalNo": '.$additionalNumber.',
                "adMapLatitude": '.$latitude.',
                "adMapLongitude": '.$longitude.',
                "buildingNo": '.$buildingNumber.',
                "city": "'.$city.'",
                "district": "'.$district.'",
                "postalCode": '. $postalCode .',
                "region": "'.$region.'",
                "streetName": "'.$street.'"
            },
            "operationReason": "'.$operationReason.'",
            "operationType": "UpdateAd",
            "platformId": "'.get_option( '_platformid' ).'",
            "platformOwnerId": "'.get_option( '_platformownerid' ).'",
            "price": '.intval($formDataArray['prop_price']).',
            "propertyArea": '.$propertyArea.',
            "propertyType": "'.$property_typeName.'",
            "propertyUsage": [
                "Commercial"
            ],
            "propertyUtilities": ['. $implodedUtilities .'],
            "qrCode": "",
            "titleDeedNumber": "'.$deedNumber.'",
            "titleDeedType": "ElectronicDeed",
        }';

        require_once ( AG_DIR . 'module/class-rega-module.php' );

        $RegaMoudle = new RegaMoudle();

        $response = $RegaMoudle->PlatformCompliance($advertisement_request);
        $response = json_decode( $response );
        // prr($advertisement_request);
        // echo json_encode( $advertisement_request, JSON_PRETTY_PRINT);

        // if( !aqar_can_edit($prop_id) ){
        //     echo json_encode(['success' => false, 'reason' => 'التعديل من خلال صفحة كل العقارات الخاصة بكم'] );
        //     wp_die();
        // }

        if( isset($response->Body) && $response->Body->result->response === true ){
            update_post_meta($prop_id, 'adverst_can_edit', 0);  
            update_post_meta($prop_id, 'advertisement_response', $advertisement_response );
            echo json_encode(['success' => true, 'reason' => 'تم ارسال التعديل بنجاح الي الهيئة العقارية']);
            wp_die();
        } else {
            if( isset($response->httpCode)  ) {
                echo json_encode(['success' => false, 'reason' => $response->httpMessage . ' ' . $response->moreInformation] );
                wp_die(); 
            }elseif( isset($response->Body->result->message) ){    
                // prr($response);
                echo json_encode(['success' => false, 'reason' => $response->Body->result->message] );
                wp_die(); 
            }
        }
    }
}


function custom_columns_manage($columns){
    global $post;
    switch ($columns){
        case 'status':
            $advertiserId = get_post_meta($post->ID, 'advertiserId', true);
            $adLicenseNumber = get_post_meta($post->ID, 'adLicenseNumber', true);
            $advertisement_response = get_post_meta($post->ID, 'advertisement_response', true);
            $REDF_UID = get_post_meta($post->ID, 'REDF_UID', true);
            $loader = '<span class="btn-loader houzez-loader-js"></span>';
            $buttonClass = $REDF_btnClass = ' button-primary';
            if( !empty($advertisement_response) ) {
                $buttonClass = ' button-sysnc';
            }
            if( !empty($REDF_UID) ) {
                $REDF_btnClass = ' button-sysnc';
            }
            if( get_post_status($post->ID) === 'publish' && !empty($advertiserId) && !empty($adLicenseNumber)) {
                echo '<a href="javascript:void(0);" class="sysnc_listing button'.$buttonClass.'" data-id="' .$post->ID. '" style="margin-top: 0.5rem;display:inline-flex;">' . $loader . '<span>REGA SYNC</span></a>';
                echo '<a href="javascript:void(0);" class="sysnc_listing_redf button'.$REDF_btnClass.'" data-id="' .$post->ID. '" style="margin-top: 0.5rem;display:inline-flex;">' . $loader . '<span>REDF SYNC</span></a>';
            
            }
            break;
    }

    return $columns;
}
add_action( 'manage_pages_custom_column', 'custom_columns_manage', 10, 1 );

add_action('admin_head', 'aqar_loader_style');
function aqar_loader_style(){
    echo '<style>
    @-webkit-keyframes btn-loader {
        0% {
          -webkit-transform: rotate(0deg);
                  transform: rotate(0deg);
        }
        100% {
          -webkit-transform: rotate(360deg);
                  transform: rotate(360deg);
        }
      }
      
      @keyframes btn-loader {
        0% {
          -webkit-transform: rotate(0deg);
                  transform: rotate(0deg);
        }
        100% {
          -webkit-transform: rotate(360deg);
                  transform: rotate(360deg);
        }
      }
      .wp-core-ui .button-sysnc, .wp-core-ui .button-secondary:focus, .wp-core-ui .button.focus, .wp-core-ui .button:focus {
        background: #8BC34A;
        border-color: #4CAF50;
        color: #fff;
        text-decoration: none;
        text-shadow: none;
    }
    </style>';
}

function sync_advertisement_ajax() {
    if (isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];
        $advertiserId = get_post_meta($post_id, 'advertiserId', true);
        $adLicenseNumber = get_post_meta($post_id, 'adLicenseNumber', true);
        
        require_once(AG_DIR . 'module/class-rega-module.php');
        $RegaMoudle = new RegaMoudle();
        $response = $RegaMoudle->sysnc_AdvertisementValidator($adLicenseNumber, $advertiserId);
        $response = json_decode($response);
        if( $response->Header->Status->Code != 200  ) {  
            $msg = 'هنالك مشكلة في الاتصال مع هيئة العقار' . '<br>';
            if( isset($response->Body->error->message) ) {
                $msg .= $response->Body->error->message . '<br>';
            } 
            if( isset($response->Header->Status->Description) ) {
                $msg .= $response->Header->Status->Description . '<br>';
            }
            if( isset($response->Body->error->message) ) {
                $msg .= $response->Body->error->message . '<br>';
            }
            $ajax_response = array( 'success' => false , 'message' => $msg );
            echo wp_send_json( $ajax_response );
            wp_die();
        } else {
            if( isset($response->Body->result->advertisement) ) {
                $data = $response->Body->result->advertisement;
                $advertisement_response = json_decode(json_encode($data), true);
                /**
                 * update response
                 *---------------------------------------------------------------------*/ 
                update_post_meta( $post_id, 'advertisement_response', $advertisement_response );
                /**
                 * update locations & insert term if not exsit
                 *---------------------------------------------------------------------*/

                $ajax_response = array( 'success' => true , 'message' => 'Data synchronized successfully!' );
                echo wp_send_json( $ajax_response );
                wp_die();
            } else if ($response->Body->result->isValid === false ) {
                $ajax_response = array( 'success' => false , 'message' => $response->Body->result->message );
                echo wp_send_json( $ajax_response );
                wp_die();
            } else{
                $ajax_response = array( 'success' => false , 'message' => $response->Body->result->message );
                echo wp_send_json( $ajax_response );
                wp_die();
            }
        }
 
    }
    wp_die();
}
add_action('wp_ajax_sync_advertisement', 'sync_advertisement_ajax');

function add_response_modal_to_admin_footer() {
    // Check if we are on the post type property page
    $screen = get_current_screen();
    if ($screen->post_type === 'property') {
        ?>
        <!-- Modal HTML -->
        <div id="responseModal" title="Response" style="display:none;">
            <p id="responseMessage"></p>
        </div>
        <?php
    }
}
add_action('admin_footer', 'add_response_modal_to_admin_footer');
function enqueue_custom_admin_scripts($hook) {
    // Only load on post type property pages
    $screen = get_current_screen();
    if ($screen->post_type === 'property') {
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_scripts');


/** -------------------------------------------------------------------------
 * REDF_SYNC  
 *-------------------------------------------------------------------------*/
function REDF_SYNC() {
    $postID = $_POST['post_id'] ?? '';
    require_once AG_DIR . 'classes/REDFBrokerageAPI.php';
    $api = new REDFBrokerageAPI();

    $data = get_post_meta($postID, 'advertisement_response', true);

    $brokerPropertyUrl = get_permalink($postID);
    $price = $data['propertyPrice'] ?? 1;
    $regionId = $data['location']['regionCode'] ?? 1;
    $cityId = $data['location']['cityCode'] ?? 1;
    $longitude = $data['location']['longitude'] ?? 1;
    $latitude = $data['location']['latitude'] ?? 1;

    $area = get_post_meta($postID, 'fave_property_size', true);
    $deedNumber = $data['deedNumber'];
    $propertyFace = $data['propertyFace'];
    $planNumber = $data['planNumber'];
    $AdvertiserId = $data['advertiserId'];
    $numberOfRooms = $data['numberOfRooms'];
    // Property Data
    $propertyData = json_encode([
        "brokerPropertyUrl" => $brokerPropertyUrl,
        "advertiser" => 3,
        "type" => "1",
        "price" => $price,
        "regionId" => $regionId,
        "cityId" => $cityId,
        "direction" => "South",
        "area" => $area,
        "parking" => 0,
        "isHidden" => false,
        "status" => 1,
        "buildingNumber" => "$planNumber",
        "condition" => 0,
        "brokerPropertyId" => "$deedNumber",
        "AdvertiserId" => "$AdvertiserId",
        "AuthorizationId" => "$AdvertiserId ",
        "Bedrooms" => $numberOfRooms,
        "BuildYear" => "2010",
        "DeedNumber" => $deedNumber,
        "Latitude" => $latitude,
        "Longitude" => $longitude,
        "Length" => 1,
        "Width" => 1,
        "media" => [
            [
                "type" => 0,
                "isDefault"=> true,
                "source"=> "https://aqargate.com/staging/wp-content/uploads/2024/05/IMG_1536-1-1170x785.jpg"
            ],
    
        ]
    ]);
    
    $REDF_UID = get_post_meta($postID, 'REDF_UID', true);
    if (!empty($REDF_UID)) {
        // Update a property
        $response = $api->createProperty($propertyData);
    } else {
        // Create a property
        $response = $api->createProperty($propertyData);
    }

    $ajax_response = ['success' => false, 'message' => ''];

    $response_data = json_decode($response, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($response_data['errors'])) {
            // Extract error messages
            $errors = $response_data['errors'];
            foreach ($errors as $field => $error) {
                $ajax_response['message'] .= implode(', ', $error) . ' ';
            }
        } elseif( isset($response_data['uid']) && !empty($response_data['uid']) ) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Property synced successfully. | UID : ' . $response_data['uid'];
            update_post_meta( $postID, 'REDF_UID', $response_data['uid']);
        }
    } else {
        $ajax_response['message'] = 'Invalid API response.';
    }

    echo wp_send_json($ajax_response);
    wp_die();
}
add_action('wp_ajax_REDF_SYNC', 'REDF_SYNC');


/** -------------------------------------------------------------------------
 * del_terms 
 *-------------------------------------------------------------------------*/
add_action('wp_ajax_del_terms', 'del_terms');
function del_terms(){
    $taxonomy_name = $_POST['taxonomy_name'] ?? '';
    $offset =  0;
    $limit = 100; // Delete 50 terms at a time

    if (empty($taxonomy_name)) {
        wp_send_json_error(['message' => 'No taxonomy selected.']);
        return;
    }

    $terms = get_terms([
        'taxonomy' => $taxonomy_name,
        'number' => $limit,
        'offset' => $offset,
        'hide_empty' => false
    ]);

    $processed_terms = [];
    foreach ($terms as $term) {
        wp_delete_term($term->term_id, $taxonomy_name);
        $processed_terms[] = $term->name; // Collecting term names
    }

    $continue = !empty($terms);
    wp_send_json_success(['continue' => $continue, 'message' => 'Batch delete processed.', 'terms' => $processed_terms]);
	wp_die();
}

add_action('wp_ajax_get_total_terms', 'get_total_terms');
function get_total_terms() {
    $taxonomy_name = $_POST['taxonomy_name'] ?? '';
    if (empty($taxonomy_name)) {
        wp_send_json_error(['message' => 'Taxonomy name is required.']);
        return;
    }

    $terms_count = wp_count_terms($taxonomy_name, ['hide_empty' => false]);
    if (is_wp_error($terms_count)) {
        wp_send_json_error(['message' => 'Error retrieving term count.']);
        wp_die();
    }

    wp_send_json_success(['totalTerms' => $terms_count]);
    wp_die();
}
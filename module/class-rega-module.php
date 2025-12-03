<?php

class RegaMoudle{

    protected $BaseUrl;

    protected $sandbox = true;

    protected $dummy = true;


    public function __construct() {
        $this->sandbox  = get_option( '_sandbox' );
        $this->dummy    = get_option( '_dummy' );

        // Production api url .
        $this->BaseUrl = 'https://integration-gw.nhc.sa/nhc/prod/';

        if( $this->sandbox ){
            // Sandbox api url .
            $this->BaseUrl = 'https://integration-gw.housingapps.sa/nhc/dev/';
        }
    }
    
	public function credential(){
		
		$credential = array(
			'X-IBM-Client-Id: ' . get_option( '_client_id' ),
			'X-IBM-Client-Secret: ' . get_option( '_client_secret' ),
            'RefId: ' . get_option( '_client_id' ),
            'CallerReqTime: ' . strtotime( date('Y-m-d') ) ,
		);

        return $credential;
    }

    public function do_request($url='', $type, $endpoint, $headers = array(), $body = array() , $params = array() )
    {
        if( empty($url) ) {
            $url = $this->BaseUrl . $endpoint;
            $url .= !empty( $params ) ? '?'.http_build_query($params) : '';
        }
        // prr($url);
        $curl = curl_init();
        curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLINFO_HEADER_OUT => true,
		  CURLOPT_CUSTOMREQUEST => $type,
          CURLOPT_POSTFIELDS => $body,
		  CURLOPT_HTTPHEADER => $headers,
		));

        $response = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        
        curl_close($curl);

        $data = [];
		
        if( $this->dummy && $endpoint === 'v1/brokerage/AdvertisementValidator' ) {
            if ($httpcode == '200') {
                $data = $this->valid_response();
            // local test only
            } elseif( $httpcode == '403' ){  $data = $this->valid_response();  
            } elseif( $httpcode == '401' ){  $data = $this->rega_errors($response); 
            } else {
                // $response =  json_decode($response , true);
                $data = $this->error_response();
            }
        }elseif ( $httpcode == '403' ){
            $data = $response; 
        }elseif ($httpcode == '502') {
            $data = '{
                "httpCode": "502",
                "httpMessage": "502 bad gateway",
                "moreInformation": null
            }'; 
        } else {
            $data = $response;
        }
        
        return $data;
        
    }
    
    public function AdvertisementValidator( $adLicenseNumber =''){

        $userID    = get_current_user_id();
        $id_number = get_the_author_meta( 'aqar_author_id_number' , $userID );
        $type_id   = get_the_author_meta( 'aqar_author_type_id' , $userID );
        // check the id if it start with 7 use it [user type agency] :bool
        $is_unified_number = $this->numberStartsWith($type_id, '7');
                
        if( $type_id === '2' && ! $is_unified_number ){
            $id_number = get_the_author_meta( 'aqar_author_unified_number' ,$userID );
            if( empty($id_number) ) {
                $id_number = get_the_author_meta( 'aqar_author_id_number' , $userID ); 
            }
        }
        // prr([
        //     'adLicenseNumber' => $adLicenseNumber,
        //     'advertiserId'    => $id_number,
        //     'idType'          => $type_id,
        // ]);
        $response = $this->do_request(
            '',
            'GET',
            'v2/brokerage/AdvertisementValidator',
            $this->credential(),
            array(),
            [
                'adLicenseNumber' => $adLicenseNumber,
                'advertiserId'    => $id_number,
                'idType'          => $type_id,
            ]
        );

        // $response = $this->test_response();
        return $response;
    }

    public function sysnc_AdvertisementValidator( $adLicenseNumber ='' , $id_number=''){

        $userID    = get_current_user_id();

        // check the id if it start with 7 use it [user type agency] :bool
        $is_unified_number = $this->numberStartsWith($id_number, '7');
                
        if( ! $is_unified_number ){
            $type_id = '1';
        } else {
            $type_id = '2';
        }
        // prr([
        //     'adLicenseNumber' => $adLicenseNumber,
        //     'advertiserId'    => $id_number,
        //     'idType'          => $type_id,
        // ]);
        $response = $this->do_request(
            '',
            'GET',
            'v2/brokerage/AdvertisementValidator',
            $this->credential(),
            array(),
            [
                'adLicenseNumber' => $adLicenseNumber,
                'advertiserId'    => $id_number,
                'idType'          => $type_id,
            ]
        );

        // $response = $this->test_response();
        return $response;
    }
    public function PlatformCompliance( $bodyData = array() ){

        $testJson = '{
            "adLicenseNumber": "7200001037",
            "adLinkInPlatform": "url",
            "adSource": "REGA",
            "adType": "Sell",
            "advertiserId": "1101588869",
            "advertiserMobile": "0548241599",
            "advertiserName": "بدر حمد محمد اليحياء",
            "borders": [
                {
                    "direction": "رقم /8461 1",
                    "length": "30",
                    "type": "قطعة"
                }
            ],
            "brokerageAndMarketingLicenseNumber": "6200000341",
            "channels": [
                "LicensedPlatform"
            ],
            "complianceWithTheSaudiBuildingCode": true,
            "constraints": "True",
            "creationDate": "2024-02-15T09:44:34.057Z",
            "eastLimitDescription": "القطعه رقم 8460",
            "eastLimitLengthChar": "سبعة عشر متر و خمسون سنتمتر",
            "eastLimitName": "جزء من",
            "endDate": "2024-08-01T09:44:34.057Z",
            "guaranteesAndTheirDuration": "452543857",
            "landNumber": "8461 / 2",
            "landTotalPrice": null,
            "locationDescriptionAccordingToDeed": "string",
            "nationalAddress": {
                "additionalNo": 1111,
                "adMapLatitude": null,
                "adMapLongitude": null,
                "buildingNo": 1231,
                "city": "ام خنرص",
                "district": "ام خنرص",
                "postalCode": 99999,
                "region": "منطقة الحدود الشماليه",
                "streetName": "مكة"
            },
            "northLimitDescription": "رقم /8461 1",
            "northLimitLengthChar": "ثالثون متر",
            "northLimitName": "قطعة",
            "notes": "452543857",
            "obligationsOnTheProperty": "452543857",
            "operationReason": "Other",
            "operationType": "DisplayAd",
            "planNumber": "2351",
            "platformId": "08dbc98d-c1b8-43ed-87f1-503766822382",
            "platformOwnerId": "7033987871",
            "price": 12000,
            "propertyAge": "FiveYears",
            "propertyArea": 525,
            "propertyFace": "Western",
            "propertyType": "Hotel",
            "propertyUsage": [
                "Commercial"
            ],
            "propertyUtilities": [
                "Electricity",
                "Waters",
                "Sanitation",
                "FixedPhone",
                "FibreOptics",
                "FloodDrainage"
            ],
            "qrCode": "",
            "roomsNumber": 5,
            "southLimitDescription": "رقم 8463",
            "southLimitLengthChar": "ثالثون متر",
            "southLimitName": "قطعة",
            "streetWidth": 1564,
            "titleDeedNumber": "810117041224",
            "titleDeedType": "ElectronicDeed",
            "totalAnnualRentForTheLand": null,
            "westLimitDescription": "عرض 20 متر",
            "westLimitLengthChar": "سبعة عشر متر و خمسون سنتمتر",
            "westLimitName": "شارع"
        }
        ';
        $data = json_encode($bodyData);
        $response = $this->do_request(
            '',
            'POST',
            'v1/brokerage/PlatformCompliance',
            $this->credential(),
            $data,
            array(),
        );

        // $response = $this->test_response();
        return $response;
    }

    public function CreateADLicense( $bodyData = array() )
    {
  
        $response = $this->do_request(
            '',
            'POST',
            'v2/brokerage/CreateADLicense',
            $this->credential(),
            $bodyData,
            array(),
        );

        return $response;
    }

    public function SendAttachment( $bodyData = array() )
    {
        $testUrl = 'https://integration-gw.housingapps.sa/nhc/dev/v1/brokerage/SendAttachment';
        $response = $this->do_request(
            $testUrl,
            'POST',
            'v1/brokerage/SendAttachment',
            array(
                'X-IBM-Client-Id: 7170eb897cb971a3a35a55a887121d42',
                'X-IBM-Client-Secret: 7bd077b49b8238ef23c6ee05215cf9f7',
                'RefId: 7170eb897cb971a3a35a55a887121d42',
                'CallerReqTime: ' . strtotime( date('Y-m-d') ),
              ), 
            $bodyData,
            array(),
        );

        return $response;
    }
    

  
    public function rega_errors( $response ){
        $errors = [];

        if( isset( $response ['errors'] ) ){
            foreach ( $response ['errors'] as $error_type) {
                foreach ($error_type as $error_msg) {
                    $errors[] = $error_msg;
                }
            }
    
        }

        if( isset( $response ['errorCode'] ) ){
            $errors[] = $response['errorMsg_AR'];
        }

        if($response !== true && empty( $errors ) ){
            $errors[] = 'هنالك مشكلة في الاتصال مع هيئة العقار';
        }

        return $errors ;
    }

    public function valid_response()
    {
        return '{
            "Header":{
               "ResTime":"2023-05-30T10:02:19.357Z",
               "ChId":"realestateportals",
               "ReqID":"adc3e9106475c9a9002e6c10",
               "Status":{
                  "Code":200,
                  "Description":"OK"
               }
            },
            "Body":{
               "result": {
    "isValid": true,
    "advertisement": {
      "advertiserId": "7011349102",
      "adLicenseNumber": "7200000895",
      "deedNumber": "1234567898888",
      "advertiserName": "شركة مأوى الحلول للخدمات العقارية شركة شخص واحد",
      "phoneNumber": "+966554881599",
      "brokerageAndMarketingLicenseNumber": "1200000333",
      "isConstrained": false,
      "isPawned": false,
      "isHalted": false,
      "isTestment": false,
      "rerConstraints": null,
      "streetWidth": 15224,
      "propertyArea": 5464,
      "propertyPrice": 120,
      "landTotalPrice": null,
      "landTotalAnnualRent": 655680,
      "numberOfRooms": null,
      "propertyType": "ارض",
      "propertyAge": null,
      "advertisementType": "إيجار",
      "location": {
        "region": "منطقة الجوف",
        "regionCode": "13",
        "city": "العيساويه",
        "cityCode": "12548",
        "district": "الربوة",
        "districtCode": "106",
        "street": "مكة",
        "postalCode": "99999",
        "buildingNumber": "1111",
        "additionalNumber": "1111",
        "longitude": "46.6415725",
        "latitude": "24.8181109"
      },
      "propertyFace": "شمالية شرقية",
      "planNumber": "12345",
      "landNumber": "123",
      "obligationsOnTheProperty": "452543857",
      "guaranteesAndTheirDuration": "",
      "complianceWithTheSaudiBuildingCode": null,
      "channels": [
        "لوحة اعالنية"
      ],
      "propertyUsages": [
        "سكني"
      ],
      "propertyUtilities": [
        "اليوجد خدمات"
      ],
      "creationDate": "08/01/2024",
      "endDate": "01/01/2025",
      "adLicenseURL": "https://localhost:44311//public/OfficesBroker/ElanDetails/08dc1012-2d47-4e83-8c46-8099d0771a0f",
      "adSource": "الهيئة العامة للعقار",
      "titleDeedTypeName": "صك السجل العقاري",
      "locationDescriptionOnMOJDeed": null,
      "notes": null,
      "borders": {
        "northLimitName": null,
        "northLimitDescription": null,
        "northLimitLengthChar": null,
        "eastLimitName": null,
        "eastLimitDescription": null,
        "eastLimitLengthChar": null,
        "westLimitName": null,
        "westLimitDescription": null,
        "westLimitLengthChar": null,
        "southLimitName": null,
        "southLimitDescription": null,
        "southLimitLengthChar": null
      },
      "rerBorders": [
        {
          "direction": "رقم /8461 1",
          "type": "قطعة",
          "length": "ثالثون متر"
        },
        {
          "direction": "القطعه رقم 8460",
          "type": "جزء من",
          "length": "سبعة عشر متر و خمسون سنتمتر"
        },
        {
          "direction": "عرض 20 متر",
          "type": "شارع",
          "length": "سبعة عشر متر و خمسون سنتمتر"
        },
        {
          "direction": "رقم 8463",
          "type": "قطعة",
          "length": "ثالثون متر"
        }
      ],
      "responsibleEmployeeName": "عبدالله العتيبي",
      "responsibleEmployeePhoneNumber": "0500000000",
      "ownershipTransferFeeType": "المالك"
    },
    "message": null
  }
            }
         }
        ';
    }

    public function error_response()
    {
        return '{
            "Header":{
               "ResTime":"2023-05-30T10:05:10.055Z",
               "ChId":"realestateportals",
               "ReqID":"adc3e9106475ca55002e7570",
               "Status":{
                  "Code":400,
                  "Description":"Bad Request"
               }
            },
            "Body":{
               "result":null,
               "targetUrl":null,
               "success":false,
               "error":{
                  "code":400,
                  "message":"رقم الهوية / السجل التجاري غير مرتبط برقم تصريح الإعلان العقاري المدخل _IV0003",
                  "details":null,
                  "validationErrors":null
               },
               "unAuthorizedRequest":false,
               "__abp":true
            }
         }
        ';
    }

    public function numberStartsWith($number, $prefix) {
        return substr($number, 0, strlen($prefix)) === $prefix;
    }
}
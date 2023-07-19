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
		  );

        return $credential;
    }

    public function do_request( $type, $endpoint, $headers = array(), $body = array() , $params = array() )
    {
        $url = $this->BaseUrl . $endpoint;

        $url .= !empty( $params ) ? '?'.http_build_query($params) : '';
        $curl = curl_init();
        curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		//   CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => $headers,
        //   CURLOPT_SSL_VERIFYHOST => 0,
        //   CURLOPT_SSL_VERIFYPEER => 0,
		));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        
        $data = [];
		
        if( $this->dummy ) {
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
        }else{
            $data = $response;
        }
        
        return $data;
        
    }
    
    public function AdvertisementValidator( $adLicenseNumber ='', $advertiserId='', $idType){
        $response = $this->do_request(
            'GET',
            'v1/brokerage/AdvertisementValidator',
            $this->credential(),
            array(),
            [
                'adLicenseNumber' => $adLicenseNumber,
                'advertiserId'    => $advertiserId,
                'idType'          => $idType,
            ]
        );

        // $response = $this->test_response();

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
               "result":{
                  "isValid":true,
                  "advertisement":{
                     "advertiserId":"1034758704",
                     "adLicenseNumber":"7100000031",
                     "deedNumber":311010000240,
                     "advertiserName":"خالد بن عبدالعزيز بن عبدالرحمن المحسن",
                     "phoneNumber":"0583727427",
                     "brokerageAndMarketingLicenseNumber":"1100000139",
                     "isConstrained":false,
                     "isPawned":false,
                     "streetWidth":0,
                     "propertyArea":3000,
                     "propertyPrice":300,
                     "numberOfRooms":0,
                     "propertyType":"ارض",
                     "propertyAge":"جديد",
                     "advertisementType":"إيجار",
                     "location":{
                        "region":"منطقة الحدود الشماليه",
                        "regionCode":"9",
                        "city":"اعيوج لينه",
                        "cityCode":"901030",
                        "district":"اعيوج لينه",
                        "districtCode":"901030",
                        "street":"dd",
                        "postalCode":"56456",
                        "buildingNumber":"5645",
                        "additionalNumber":"5555",
                        "longitude":"24",
                        "latitude":"24"
                     },
                     "propertyFace":null,
                     "planNumber":null,
                     "obligationsOnTheProperty":null,
                     "guaranteesAndTheirDuration":null,
                     "theBordersAndLengthsOfTheProperty":null,
                     "complianceWithTheSaudiBuildingCode":null,
                     "channels": [                       
                            "منصة مرخصة",
                            "لوحة اعلانية"  
                    ],
                    "propertyUsages": [                  
                            "سكني"                   
                    ],
                    "propertyUtilities": [                     
                            "مياه"                    
                    ],
                     "creationDate":"31/06/2023",
                     "endDate":"15/07/2023",
                     "qrCodeUrl":"https://test-brokerage.housingapps.sa/public/IndividualBroker/ElanDetails/08db0361-4dc7-414a-8a75-6b1b9e971160"
                  }
               },
               "targetUrl":null,
               "success":true,
               "error":null,
               "unAuthorizedRequest":false,
               "__abp":true
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
}
<?php

class REGA{
    
    public static function credential(){
        // Will create a dasbborad for it later
        return array(
            'client_id' => carbon_get_theme_option( 'client_id' ),
            'client_secret' => carbon_get_theme_option( 'client_secret' )
        );
    }

    public static function token(){
        
        $response = self::do_request(
            'POST',
            'DelegatedAd/Authorize',
            array( 'Content-Type: application/json' ),
            self::credential()
        ) ;

        if($response){
            $token = $response['token_type'] . ' '. $response['access_token'];
            update_option ('rega_token' , $token );
            update_option ('rega_token_expires_in' , $response['expires_in']);
            return $token;
        }
        
    }
    

    public static function do_request( $type, $endpoint, $headers = array(), $body = array() , $params = array() )
    {
        $url = 'https://apigateway.rega.gov.sa/api/v1/'.$endpoint;
        $url .= !empty( $body ) ? '?'.http_build_query($body) : '';
        
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array( 
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING =>'',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $type,
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => $headers,
            )
        );
        // for local testing only remove it later .
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl); 
        curl_close($curl);
        
         $response =  json_decode($response , true);
          return $response;
        
    }
    
    public static function id_types(){
        $response =   self:: do_request(
            'GET',
            'DelegatedAd/IdTypes',
            array( 
                'Authorization: ' . self::token(),
                'Content-Type: application/json'
            )
        );

        return $response;
    }

    /* 
    * التاكد من ان فعلا المعلومات دي خاصه بسمسار واخد التصريح من الحكومه 
    * 
    */

    public static function is_valid_ad($prop_id, $userID = 0){

        $enable_api = carbon_get_theme_option( 'aq_show_api' );
 
        if ( $enable_api !== true ) {
            return true;
        }

        $id_number = get_user_meta( $userID, 'aqar_author_id_number', true );
        $ad_number = get_user_meta( $userID, 'brokerage_license_number', true);
        $type_id   = get_user_meta( $userID, 'aqar_author_type_id', true);
    
        $response = self::do_request(
            'GET',
            'DelegatedAd/isValidAd',
            array( 
                'Authorization: ' . self::token(),
                'Content-Type: application/json'
            ),
            array(
                'Type_Id'   => $type_id,
                'Id_Number' => $id_number,
                'Ad_Number' => $ad_number
            )
        );

        if ($response === true) {
            return $response;
        }
          
        $errors = self::rega_errors($response);
        return $errors;
     
        
    }

    
    /* 
    * check if the advrtiser is allowed to publish 
    * an ad about this prob
    * 
    */
    public static function is_valid_auth_ad( $prop_id, $userID ){

     
      
        $id_number = get_user_meta( $userID, 'aqar_author_id_number', true );
        $ad_number = get_user_meta( $userID, 'aqar_author_ad_number', true);
        $type_id   = get_user_meta( $userID, 'aqar_author_type_id', true);
    
         $body_data = array(
            'Type_Id'     => $type_id ,
            'Id_Number'   => $id_number,
            'Ad_Number'   => $ad_number,
            'Auth_Number' => get_post_meta($prop_id, 'fave_d8b1d982d985-d8a7d984d8aad981d988d98ad8b6', true),
        );

         $response = self::do_request(
             'GET',
             'DelegatedAd/isValidAuthAd',
             array(
                'Authorization: ' . self::token(),
                'Content-Type: application/json'
            ),
             $body_data
         );

         if ($response === true) {
             return $response;
         }
           
         $errors = self::rega_errors($response);
         return $errors;
      
        
    }

    
    public static function rega_errors( $response ){
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
}
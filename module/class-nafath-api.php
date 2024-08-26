<?php

class NafathMoudle{

    protected $BaseUrl;

    protected $apikey;

    protected $sandbox = true;

    protected $dummy = true;

    public function __construct() {

        $this->sandbox  = !empty( get_option( '_nafath_sandbox' )) ? get_option( '_nafath_sandbox' ) : '';
        $this->dummy    = !empty( get_option( '_nafath_dummy' )) ? get_option( '_nafath_dummy' ) : false ;
        $this->apikey   = !empty(get_option( '_nafath_apikey' )) ? get_option( '_nafath_apikey' ) : '69efac5a-e1a4-4250-9af2-7aa0a78fc422';

        // Production api url .
        $this->BaseUrl = 'https://iamservices.semati.sa/';

        if( $this->sandbox ){
            // Sandbox api url .
            $this->BaseUrl = 'https://test-iamservices.semati.sa/';
        }
    }
    
	public function credential(){
		
		$credential = array(
			'X-IBM-Client-Id: ' . get_option( '_client_id' ),
			'X-IBM-Client-Secret: ' . get_option( '_client_secret' ),
		  );

        return $credential;
    }

    public function do_request( $id )
    {
        $url = $this->BaseUrl;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url . 'nafath/api/v1/client/authorize/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{"id": "' . $id . '","action": "SpRequest","service": "DigitalServiceEnrollmentWithoutBio"}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: apikey ' . $this->apikey,
            'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        
        $data = [];

		$data = json_decode($response);
 
        return $data;   
    }
    
    public function login( $nafath_id ){

        if( empty( $nafath_id ) ) {
            return;
        }

        $response = $this->do_request( $nafath_id );

        return $response;
    }

    public function test_data(){
        return '{"response":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2luZm8iOnsiaWQiOjEwMDAwMDA0NDYsImlkX3ZlcnNpb24iOjUsImZpcnN0X25hbWUjYXIiOiLYtNix2YrZgSIsImZhdGhlcl9uYW1lI2FyIjoi2LnZhNmKIiwiZ3JhbmRfbmFtZSNhciI6Itiz2LnYryIsImZhbWlseV9uYW1lI2FyIjoi2LnZhNmKIiwiZmlyc3RfbmFtZSNlbiI6InNoZXJpZiIsImZhdGhlcl9uYW1lI2VuIjoiYWxpIiwiZ3JhbmRfbmFtZSNlbiI6IkEiLCJmYW1pbHlfbmFtZSNlbiI6InNhYWQiLCJ0d29fbmFtZXMjYXIiOiLYudmE2YoiLCJ0d29fbmFtZXMjZW4iOiJBTEkgQk8gTU9aQUgiLCJmdWxsX25hbWUjYXIiOiIg2LTYsdmK2YEg2LnZhNmKINiz2LnYryDYudmE2YoiLCJmdWxsX25hbWUjZW4iOiJTSEVSSUYgQUxJIFNBQUQiLCJnZW5kZXIiOiJNIiwiaWRfaXNzdWVfZGF0ZSNnIjoiMjAyMC0xMS0zMCIsImlkX2lzc3VlX2RhdGUjaCI6MTQ0MjA0MTUsImlkX2V4cGlyeV9kYXRlI2ciOiIyMDQwLTA0LTI2IiwiaWRfZXhwaXJ5X2RhdGUjaCI6MTQ2MjA0MTQsImxhbmd1YWdlIjoiQSIsIm5hdGlvbmFsaXR5IjoxMTMsIm5hdGlvbmFsaXR5I2FyIjoi2KfZhNi52LHYqNmK2Kkg2KfZhNiz2LnZiNiv2YrYqSIsIm5hdGlvbmFsaXR5I2VuIjoiU2F1ZGkgQXJhYmlhIiwiZG9iI2ciOiIxOTY0LTExLTA1IiwiZG9iI2giOjEzODQwNzAxLCJjYXJkX2lzc3VlX3BsYWNlI2FyIjoi2KfYrdmI2KfZhCDYp9mE2KPYrdiz2KfYoSIsImNhcmRfaXNzdWVfcGxhY2UjZW4iOiJQZXJzb25hbCBTdGF0dXMsIEFoc2EiLCJ0aXRsZV9kZXNjI2FyIjpudWxsLCJ0aXRsZV9kZXNjI2VuIjpudWxsfSwiYXVkIjoiQVFBUkdBVEUiLCJpc3MiOiJodHRwczovL3d3dy5pYW0uZ292LnNhL25hZmF0aCIsImlhdCI6MTcyMzM3NjI5MCwibmJmIjoxNzIzMzc2MjkwLCJleHAiOjE3MjMzNzk4OTF9.UhUAkN-QDpzhmHJqgqPujQ3_Q3kEFhxRsYjW61cjZTq0tXFdpRx8-GHYSd9qEUqy6VsGpxUSyc2zla6nkSIaNwEfVcjpWmi6v_Bqsg10YFJjP8g6QUYqXWD9wF2xtLc-cocpHyLtcI2HkDMVSJ3fiqZpHEF1lo9Vl9koK7KyBvR81AQdhhx5cbnpJa40JhV_1n9bba-mep_iML4Amgtmlvd7qF6SFt3XqOTWn3LZ8MiaTXOuM_LDa3TGI4zUDy_o0jgyX42NDplSm4tU2f4BD67betJx_BAAtV1eYccDOTih1POYykhLu4kafDlRyemNcaypILadeOTQKnEG-sbdvw","status":"COMPLETED","transId":"0c4349fc-07a5-4679-9468-d5790762eb4f","ServiceName":"DigitalServiceEnrollmentWithoutBio"}';
    }
  
}
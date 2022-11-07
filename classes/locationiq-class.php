<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class ApiNaAddress
 *
 * @package		aqargate
 * @subpackage	Classes/locationiq-class
 * @author		Sherif Ali
 * @since		1.0.0
 */
class ApiNaAddress{

    /**
     * @var string
     */
    protected static $apiKey = '7088f7e2fc2949049c40abf3d0734e67';
      
    /**
     * __construct
     *
     * @param  mixed $lat
     * @param  mixed $lon
     * @return void
     */
    public function __construct(){}

    /**
     * create_connection
     *
     * @param  mixed $lat
     * @param  mixed $lon
     * @param  mixed $key
     * @return object
     */
    public static function create_connection( $lat = '', $long = '', $type = '', $suptype ='regions' , $lookupID = '' ){

        if( empty( $type ) ){
            return;
        }
        $urlQuery = [];
        $baseurl = "";
        if( $type === 'Address' && !empty( $lat ) && !empty( $long ) ) {
            $baseurl = "https://apina.address.gov.sa/NationalAddress/v3.1/$type/address-geocode";
            $urlQuery = array(
                'lat' => $lat,
                'long'   => $long,
                'language' => 'A',
                'format'   => 'json',
                'encode'   => 'utf8'
            );
        }

        $allow_suptyype = ['regions', 'cities', 'districts'];

        if( $type === 'lookup' && !empty( $suptype ) && in_array( $suptype, $allow_suptyype) ) {
            
            $baseurl = "https://apina.address.gov.sa/NationalAddress/v3.1/$type/$suptype";
            $urlQuery = array(
                'language' => 'A',
                'format'   => 'json',
                'encode'   => 'utf8'
            );
            // if( $suptype === 'regions' )  { $urlQuery['regionid'] = $lookupID; }
            if( $suptype === 'cities' )   { $urlQuery['regionid'] = $lookupID; }
            if( $suptype === 'districts' ){ $urlQuery['cityid']   = $lookupID; }

        }

        $apiUrl  = $baseurl . "?" . http_build_query($urlQuery);

        // return $apiUrl;

        $key  = self::$apiKey;
        $apiResponse = wp_remote_post( $apiUrl,
            [
                'method'    => 'GET',
                'sslverify' => false,
                'headers'   => [
                    'content-type' => 'application/json',
                    'api_key' =>  $key,
                ],
            ]
        );
        $apiBody = json_decode( wp_remote_retrieve_body( $apiResponse ) );

        return $apiBody;
    
    }

}
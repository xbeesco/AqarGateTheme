<?php

add_action( 'wp_enqueue_scripts', 'ag_enqueue_styles' );
function ag_enqueue_styles() {
    wp_enqueue_style( 'houzez-child', get_stylesheet_uri() );
}
// Constants
define('AG_DIR', __DIR__.'/');

// Helpers
include_once ( AG_DIR.'helpers/ag_helpers.php' );
include_once ( AG_DIR.'helpers/ag_get_array_property_data.php' );
// Classes
include_once ( AG_DIR.'classes/class-crb.php' );
new AG_CF;

include_once ( AG_DIR.'classes/class-prop.php' );
new AG_Prop;
require_once ( AG_DIR. 'classes/class-otp-twilio.php' );
include_once ( AG_DIR. 'classes/aqargate-class.php' );
new AqarGate();

include_once ( AG_DIR . 'rest-api/class-aqargate-api.php' );

require_once ( AG_DIR. 'rest-api/api-fields-controller.php' );

include_once ( AG_DIR . 'classes/aqargate-export.php' );

include_once ( AG_DIR. 'libs/jwt/jwt-auth.php' );

function csv_to_array($file) {

    if (($handle = fopen($file, 'r')) === false) {
        die('Error opening file');
    }
    
    $headers = fgetcsv($handle, 10000, ';');
    // $headers = preg_replace('/ ^[\pZ\p{Cc}\x{feff}]+|[\pZ\p{Cc}\x{feff}]+$/ux', '', $headers);
    $_data = array();
    
    while ($row = fgetcsv($handle, 10000, ';')) {
        // $row = preg_replace('/ ^[\pZ\p{Cc}\x{feff}]+|[\pZ\p{Cc}\x{feff}]+$/ux', '', $row);
        if (count($row) == count($headers)) {
            $_data[] = array_combine($headers, $row);
        }else{
            $_data[] = array_merge($headers, $row);
        }
    }
    fclose($handle);

    return $_data;
  
  }

// add_action('init', 'debug_post');
function debug_post(){
    prr($_POST);
    wp_die();
}
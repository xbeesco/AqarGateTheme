<?php
// add_action( 'wp_enqueue_scripts', 'ag_enqueue_styles' );
function ag_enqueue_styles() {
    wp_enqueue_style( 'aqar-child', get_stylesheet_uri() );
}
/* -------------------------------------------------------------------------- */
/*                                // Constants                                */
/* -------------------------------------------------------------------------- */
define('AG_DIR', __DIR__.'/');


add_action('wp_enqueue_scripts', 'aqar_enqueue_scripts');

if( ! function_exists('aqar_enqueue_scripts') ){
    function aqar_enqueue_scripts()
    {
        wp_enqueue_script( 'aqar_custom', trailingslashit( get_stylesheet_directory_uri() )  .'/assets/js/aqar_custom.js', array(), '1.0', true );
        $userID = get_current_user_id();
        $ajax_object = array(
            'ajaxurl' => admin_url( 'admin-ajax.php'),
            'userID'  => $userID, 
            'verify_file_type' => esc_html__('Valid file formats', 'houzez'),          
        );
        wp_localize_script( 'aqar_custom', 'ajax_aqar', $ajax_object );  
    }
}


/* -------------------------------------------------------------------------- */
/*                                 // Helpers                                 */
/* -------------------------------------------------------------------------- */
include_once ( AG_DIR . 'helpers/houzez-login.php' );
include_once ( AG_DIR . 'helpers/ag_helpers.php' );
include_once ( AG_DIR . 'helpers/ag_get_array_property_data.php' );
require_once ( AG_DIR . 'helpers/api-fields-controller.php' );
/* -------------------------------------------------------------------------- */
/*                                 // Classes                                 */
/* -------------------------------------------------------------------------- */
include_once ( AG_DIR . 'classes/class-crb.php' );
new AG_CF;
// include_once ( AG_DIR .'classes/class-prop.php' );
// new AG_Prop;
require_once ( AG_DIR . 'classes/class-otp-twilio.php' );
include_once ( AG_DIR . 'classes/aqargate-class.php' );
new AqarGate();
// require_once ( AG_DIR . 'rest-api/class-aqargate-api.php' );
include_once ( AG_DIR . 'classes/aqargate-export.php' );

/* -------------------------------------------------------------------------- */
/*                                   Module                                   */
/* -------------------------------------------------------------------------- */
require_once ( AG_DIR . 'module/class-property-module.php' );
require_once ( AG_DIR . 'module/class-list-user.php' );
require_once ( AG_DIR . 'module/class-custom-column.php' );
require_once ( AG_DIR . 'module/metaboxes/metaboxes.php' );
require_once ( AG_DIR . 'module/metaboxes/metaboxes.php' );

require_once ( AG_DIR . 'module/class-nafath-db.php' );

/* -------------------------------------------------------------------------- */
/*                             houzez activation                              */
/* -------------------------------------------------------------------------- */
update_option( 'houzez_activation', 'activated' );
update_option( 'houzez_purchase_code', '123456789');
// set_transient( 'houzez_verification_success', true, 5 );


/* -------------------------------------------------------------------------- */
/*                             AQARGATE LOCATIONS                             */
/* -------------------------------------------------------------------------- */
$Regions     = AG_DIR . 'module/locations-data/Regions.csv';
$Cities      = AG_DIR . 'module/locations-data/Cities.csv';
$Districts_1 = AG_DIR . 'module/locations-data/Districts-1.csv';
$Districts_2 = AG_DIR . 'module/locations-data/Districts-2.csv';
$Districts_3 = AG_DIR . 'module/locations-data/Districts-3.csv';


define('REGIONS',   $Regions);
define('CITIES',    $Cities);
define('DISTRICTS1', $Districts_1);
define('DISTRICTS2', $Districts_2);
define('DISTRICTS3', $Districts_3);

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
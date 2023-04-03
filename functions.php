<?php
add_action( 'wp_enqueue_scripts', 'ag_enqueue_styles' );
function ag_enqueue_styles() {
    wp_enqueue_style( 'houzez-child', get_stylesheet_uri() );
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
          
        $ajax_object = array(
            'ajaxurl' => admin_url( 'admin-ajax.php')            
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
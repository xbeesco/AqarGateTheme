<?php
if( ! defined('AGDEBUG') ) {
    define( 'AGDEBUG', false );
}
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
        // Deregister the parent theme's JavaScript file
        // wp_deregister_script('houzez-custom');

        wp_enqueue_script( 'aqar_custom', trailingslashit( get_stylesheet_directory_uri() )  .'/assets/js/aqar_custom.js', array(), rand(), true );

        //wp_enqueue_script('houzez-custom', get_theme_file_uri( '/js/custom' . houzez_minify_js() . '.js' ), array('jquery'), HOUZEZ_THEME_VERSION, true);
        
        $userID = get_current_user_id();
        $ajax_object = array(
            'ajaxurl' => admin_url( 'admin-ajax.php'),
            'userID'  => $userID, 
            'verify_file_type' => esc_html__('Valid file formats', 'houzez'), 
            'add_listing' => houzez_get_template_link_2('template/user_dashboard_submit.php'),         
        );
        wp_localize_script( 'aqar_custom', 'ajax_aqar', $ajax_object );  
        wp_enqueue_script('bootbox-min', HOUZEZ_JS_DIR_URI . 'vendors/bootbox.min.js', array('jquery'), '4.4.0', true);
    }
}

function aqar_enqueue_custom_scripts() {
    wp_enqueue_script('aqar-script', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/admin-script.js', array('jquery'), rand(), true);
    wp_localize_script(
        'aqar-script',
         'ajax_params',
          array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'are_you_sure_text' => esc_html__('Are you sure you want to do this?', 'houzez'),
            'processing_text' => esc_html__('Processing, Please wait...', 'houzez'),
            'houzez_rtl' => is_rtl() ? 'yes' : 'no',
          )
        );
}
add_action('admin_enqueue_scripts', 'aqar_enqueue_custom_scripts');

/* -------------------------------------------------------------------------- */
/*                                 // Helpers                                 */
/* -------------------------------------------------------------------------- */
require_once ( AG_DIR . 'helpers/houzez-login.php' );
require_once ( AG_DIR . 'helpers/ag_helpers.php' );
// require_once ( AG_DIR . 'helpers/ag_get_array_property_data.php' );
require_once ( AG_DIR . 'helpers/api-fields-controller.php' );
require_once ( AG_DIR . 'helpers/aqar-ajax.php' );
require_once ( AG_DIR . "helpers/property-sync-helper.php" );
require_once ( AG_DIR . "helpers/ajax-props-resync.php" );
require_once ( AG_DIR . "helpers/enqueue-props-resync.php" );
require_once ( AG_DIR . "helpers/ajax-single-sync.php" );
require_once ( AG_DIR . "helpers/enqueue-single-sync.php" );
require_once ( AG_DIR . "helpers/sync-buttons.php" );

/* -------------------------------------------------------------------------- */
/*                                 // Classes                                 */
/* -------------------------------------------------------------------------- */
require_once AG_DIR . 'libs/cf/vendor/autoload.php';
require_once AG_DIR . 'classes/class-crb.php';
new AG_CF;
// include_once ( AG_DIR .'classes/class-prop.php' );
// new AG_Prop;
// require_once ( AG_DIR . 'classes/class-otp-twilio.php' );
require_once AG_DIR . 'classes/REDFBrokerageAPI.php';
require_once AG_DIR . 'classes/aqargate-class.php';
new AqarGate();
require_once AG_DIR . 'classes/CustomTableDatastore.php';
require_once AG_DIR . 'classes/property-request.php';
require_once AG_DIR . 'classes/aqargate-export.php';
require_once AG_DIR . 'classes/aqargate-woo.php';

/* -------------------------------------------------------------------------- */
/*                                   Module                                   */
/* -------------------------------------------------------------------------- */
require_once ( AG_DIR . 'module/class-property-module.php' );
require_once ( AG_DIR . 'module/class-list-user.php' );
require_once ( AG_DIR . 'module/class-custom-column.php' );
require_once ( AG_DIR . 'module/metaboxes/metaboxes.php' );
require_once ( AG_DIR . 'module/metaboxes/metaboxes.php' );

require_once ( AG_DIR . 'module/class-nafath-db.php' );

require_once ( AG_DIR . 'module/nafath-log/nafath-log.php' );

require_once ( AG_DIR . 'helpers/aqargate-functionality.php' );
/* -------------------------------------------------------------------------- */
/*                             houzez activation                              */
/* -------------------------------------------------------------------------- */
update_option( 'houzez_activation', 'activated' );
update_option( 'houzez_purchase_code', '123456789');
// set_transient( 'houzez_verification_success', true, 5 );

/* -------------------------------------------------------------------------- */
/*                            Elementor                                       */
/* -------------------------------------------------------------------------- */
require_once AG_DIR . 'module/elementor/widgets/elementor.php';

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


function update_all_users_free_package_meta() {
    // Get all users
    $users = get_users();

    // Loop through each user
    foreach ( $users as $user ) {
        // Update user meta
        update_user_meta( $user->ID, 'user_had_free_package', 'no' );
    }
}

// Hook the function to run when you visit a specific admin page, or you can directly call it
//add_action( 'init', 'update_all_users_free_package_meta' );


function override_houzez_option( $pre_option ) {
    // Check if we're on the page where we want to override the option
    $current_user_id = get_current_user_id();
    $target_user_id = 1219; // 1401
    
    if ( intval($current_user_id) ===  $target_user_id ) {
        // Ensure $pre_option is an array
        // Override specific key value
        $pre_option['enable_paid_submission'] = 'membership';
    } else {
        $pre_option['enable_paid_submission'] = 'no';
    }
    

    return $pre_option; // Return the modified array
}

add_filter( 'option_houzez_options', 'override_houzez_option' );


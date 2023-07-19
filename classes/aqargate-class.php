<?php

class AqarGate{

    public function __construct() {
        $this->init_actions();
    }

    public function init_actions() {

        add_action( 'show_user_profile', array( $this, 'AqarGate_custom_user_profile_fields') );
        add_action( 'edit_user_profile', array( $this, 'AqarGate_custom_user_profile_fields') );
        add_action( 'edit_user_profile_update', array( $this,'AqarGate_update_extra_profile_fields') );
        add_action( 'personal_options_update', array( $this,'AqarGate_update_extra_profile_fields') );
        	
        /* ------------------------------------------------------------------------------
        * Ajax Update Profile function
        /------------------------------------------------------------------------------ */
        add_action( 'wp_ajax_nopriv_AqarGat_ajax_update_profile', array( $this,'AqarGat_ajax_update_profile' ));
        add_action( 'wp_ajax_AqarGat_ajax_update_profile', array( $this,'AqarGat_ajax_update_profile' ));

        /*-----------------------------------------------------------------------------------*/
        //  Ajax Register
        /*-----------------------------------------------------------------------------------*/
        add_action( 'wp_ajax_nopriv_AqarGat_register', array( $this,'AqarGat_register' ));
        add_action( 'wp_ajax_AqarGat_register', array( $this,'AqarGat_register' ));

        /*-----------------------------------------------------------------------------------*/
        //  sa_cities.sql only run once if we need it .
        /*-----------------------------------------------------------------------------------*/
        if( isset($_GET['ag-state']) && $_GET['ag-state'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_provinces' ));
        }
        if( isset($_GET['ag-city']) && $_GET['ag-city'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_cities' ));  
        }
        if( isset($_GET['ag-area-1']) && $_GET['ag-area-1'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_area' ));
        }
        if( isset($_GET['ag-area-2']) && $_GET['ag-area-2'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_area' ));
        }if( isset($_GET['ag-area-3']) && $_GET['ag-area-3'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_area' ));
        }
   
    }

    public function AqarGate_custom_user_profile_fields($user){
        include plugin_dir_path( __FILE__ ) . '/profile-fields.php';
    }

    public function AqarGate_update_extra_profile_fields($user_id){
        if (current_user_can('edit_user', $user_id)){
        /*
         * Agent and agency Info aqar_author_id_number
        --------------------------------------------------------------------------------*/
        update_user_meta($user_id, 'aqar_author_id_number', $_POST['aqar_author_id_number']);
        update_user_meta($user_id, 'aqar_author_ad_number', $_POST['aqar_author_ad_number']);
        update_user_meta($user_id, 'aqar_author_type_id', $_POST['aqar_author_type_id']);
        update_user_meta($user_id, 'brokerage_license_number', $_POST['brokerage_license_number']);
        update_user_meta($user_id, 'license_expiration_date', $_POST['license_expiration_date']);

        }
    }

    public function AqarGat_ajax_update_profile(){
        include plugin_dir_path( __FILE__ ) . '/profile-ajax-update.php';
    }

    public function AqarGat_register(){
        include plugin_dir_path( __FILE__ ) . '/register-ajax-update.php';
    }

    public function add_sa_provinces(){
        include  plugin_dir_path( dirname( __FILE__ ) ) . '/module/sa-data/add-sa-provinces.php';
    }

    public function add_sa_cities(){
        include  plugin_dir_path( dirname( __FILE__ ) ) . '/module/sa-data/add_sa_cities.php';
    }

    public function add_sa_area(){
        if( isset($_GET['ag-area-1']) && $_GET['ag-area-1'] == 1 && is_admin() ) {
            $file = DISTRICTS1;
        }else if( isset($_GET['ag-area-2']) && $_GET['ag-area-2'] == 1 && is_admin() ) {
            $file = DISTRICTS2;
        }else if( isset($_GET['ag-area-3']) && $_GET['ag-area-3'] == 1 && is_admin() ) {
            $file = DISTRICTS3;
        }
        if( $file ) {
            include  plugin_dir_path( dirname( __FILE__ ) ) . '/module/sa-data/add_sa_area.php';
        }
    }

    /**
     * create qrcode for post type 
    */
    public function aqargate_display_qr_code(){
        $content = '';
        $current_post_id = get_the_ID();
        
        // if device has no task link to device .
        $current_post_title = get_the_title($current_post_id);
        $current_post_url   = urlencode(get_the_permalink($current_post_id));
            
        $current_post_type  = get_post_type($current_post_id);

        // Post Type Check
        $excluded_post_types = apply_filters('jas_excluded_post_types', array('acf-field-group', 'page'));
        if (in_array($current_post_type, $excluded_post_types)) {
            return;
        }
        if ( $current_post_type == 'property'  ) {

        //Dimension Hook
        $dimension = apply_filters('jas_qrcode_dimension', '100x100');

        //Image Attributes
        $image_attributes = apply_filters('jas_image_attributes', null);

        $image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $dimension, $current_post_url);
        $content   .= sprintf("<div id='section-to-print' class='qrcode'><img %s  src='%s' alt='%s' /></div>", $image_attributes, $image_src, $current_post_title);

            return $content;
        }else{
            return $content;
        }
    }
}
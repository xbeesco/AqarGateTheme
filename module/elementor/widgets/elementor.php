<?php 

class AqarGate_Elementor {

    function __construct(){
        add_action( 'elementor/widgets/register', array( $this, 'elementor_widgets' ) );
    }

    /**
         * Widgets
         *
         * @since  1.0.0
         * @access public
         */
        public function elementor_widgets( $widgets_manager ) {

            if( class_exists('houzez_data_source') ) {
                require_once 'properties-ajax-tabs.php';
            }
        }


}

new AqarGate_Elementor();
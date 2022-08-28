<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class AG_CF
{
    public function __construct(){
        add_action( 'after_setup_theme', array ( $this , 'crb_load' ) );
        add_action( 'carbon_fields_register_fields', array( $this , 'ag_settings_panel' ) );
        add_action( 'carbon_fields_register_fields', array( $this , 'ag_tax_select' ) );
        // add_action( 'carbon_fields_theme_options_container_saved', [ $this, 'fields_steps' ] );
    }

    public function crb_load() {
        include_once ( AG_DIR.'libs/cf/vendor/autoload.php' );
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public function ag_settings_panel() {
        if ( is_admin() && isset($_GET['page']) == 'crb_carbon_fields_container_ag_settings.php') {
            $current_page = admin_url("admin.php?page=".$_GET["page"]);
            $html = '<a href="' . add_query_arg('export', '1', $current_page) . '" class="button button-primary">Export</a>';
        } else {
            $html = '';
        }
        Container::make( 'theme_options','ag_settings', __( 'AG Settings' ) )
        
        ->add_tab(
            __( 'REGA', 'ag' ),
            array(
                Field::make( 'text', 'client_id', __( 'Client ID', 'ag' ) ),
                Field::make( 'text', 'client_secret', __( 'Client Secret', 'ag' ) ),
                Field::make( 'checkbox', 'aq_show_api', 'Enable Api' ),
               
            )
        )
        ->add_tab( __( 'Property Export' ), array(
            Field::make( 'html', 'crb_information_text' )
            ->set_html( $html )
        ) )

        ->add_tab(
            __( 'APP Options', 'ag' ),
            array(
                Field::make( 'file', 'ag_logo', __( 'app logo' ) )
	            ->set_type( array( 'image' ) )->set_value_type( 'url' ),
                Field::make( 'file', 'ag_reload_gif', __( 'app reload gif' ) )
	            ->set_type( array( 'image' ) )->set_value_type( 'url' ),
                Field::make( 'file', 'ag_json', __( 'app json' ) )
	            ->set_type( array( 'json' ) )->set_value_type( 'url' ),
                Field::make( 'complex', 'app_available_fields', __( 'Add Property Steps' ) )
                ->add_fields( array(
                    Field::make( 'multiselect', 'fields', __( 'Select Fileds To Step' ) )
                    ->add_options( $this->prop_multi_step_fileds() )
                ) )
                ->set_header_template( '
                    <% if (fields) { %>
                        Step: <%- $_index + 1 %>
                    <% } %>
                ' )
                                
               
            )
        )
        ->add_tab( __( 'Twilio Settings' ), array(
            Field::make( 'text', 'twilio-account-sid' ),
            Field::make( 'text', 'twilio-auth-token' ),
            Field::make( 'text', 'twilio-sender-number' ),
            Field::make( 'textarea', 'r-sms-txt' )
            ->set_attribute( 'placeholder', __("[otp] is your One Time Verification(OTP) to confirm your phone no at AqarGate.",'aqargate') ),

        ) );


             // Display container on Book Category taxonomy
            Container::make( 'term_meta', __( 'Icon Font' ) )
            ->where( 'term_taxonomy', '=', 'property_type')
            ->add_fields( array( 
                Field::make( 'icon', 'property_type_icon', __( 'Property Icon', 'crb' ) ),
            ) );

            Container::make( 'term_meta', __( 'Icon Font' ) )
            ->where( 'term_taxonomy', '=', 'property_status' )
            ->add_fields( array( 
                Field::make( 'icon', 'property_type_icon', __( 'Property Icon', 'crb' ) ),
            ) );

            Container::make( 'term_meta', __( 'Icon Font' ) )
             ->where( 'term_taxonomy', '=', 'property_feature' )
             ->add_fields( array( 
                 Field::make( 'icon', 'property_type_icon', __( 'Property Icon', 'crb' ) ),
            ) );

            Container::make( 'term_meta', __( 'Icon Font' ) )
             ->where( 'term_taxonomy', '=', 'property_label' )
             ->add_fields( array( 
                 Field::make( 'icon', 'property_type_icon', __( 'Property Icon', 'crb' ) ),
            ) );

    }

   
    
    /**
     * ag_tax_select
     *
     * @return void
     */
    public function ag_tax_select(){
        Container::make( 'term_meta', __( 'Select Fileds To Show' ) )
        ->where( 'term_taxonomy', '=', 'property_type' )
        ->add_fields( array(
        Field::make( 'multiselect', 'crb_available_fields', __( 'Select Fileds To Show' ) )
        ->add_options( $this->ag_fields_array() )
        
    ) );
    }
    
    /**
     * ag_fields_array
     *
     * @return void
     */
    public function ag_fields_array(){
        //get Fields
        $fields_builder = array();
        $adp_details_fields = houzez_option('adp_details_fields');
        if( is_array($adp_details_fields) ){
            $fields_builder = $adp_details_fields['enabled'];
            unset($fields_builder['placebo']);
        }
          
         return $fields_builder;
    }

    public function prop_multi_step_fileds(){      
        $main_fields  = ag_get_property_fields();
        $extra_fields = ag_get_property_fields_extra();
        $all_fields   = array_merge($main_fields , (array)$extra_fields);
        $prop_multi_step_fileds = [];
        if( !empty( $all_fields ) && is_array( $all_fields ) ) {
            foreach ($all_fields as $field) {
                $field_key = $field['field_id'];
                $prop_multi_step_fileds[$field_key] = $field['label'];
            }
        }

        return $prop_multi_step_fileds;
        
    }
    
}

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
            $export_btn   = '<a href="' . add_query_arg('export', '1', $current_page) . '" class="button button-primary">Export</a>';
            $cache_btn    = '<a href="' . add_query_arg('ag-update', '1', $current_page) . '" class="button button-primary">Update Cache</a>';
       
        } else {
            $export_btn = '';$cache_btn = '';
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
            ->set_html( $export_btn )
        ) )

        ->add_tab( __( 'Update Site api Cache' ), array(
            Field::make( 'html', 'last_update_cache' )
            ->set_html( $cache_btn )
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
                    Field::make( 'text', 'tilte', __( 'عنوان الصفحة' ) ),
                    Field::make( 'multiselect', 'fields', __( 'Select Fileds To Step' ) )
                    ->add_options( $this->prop_multi_step_fileds() )
                ) )
                ->set_header_template( '
                    <% if (tilte) { %>
                        <%- tilte %>
                    <% } %>
                ' ),
                                   
            )
        )
        ->add_tab( __( 'Apk Pages' ), array(
            Field::make( 'textarea', 'ag_policy', __( 'policy page content' ) )
            ->set_rows( 4 ),
            Field::make( 'textarea', 'ag_adv', __( 'شروط الإعلان' ) )
            ->set_rows( 4 ),
            Field::make( 'complex', 'ag_how_adv', __( 'طريقة الإعلان' ) )
            ->add_fields( array(
                Field::make( 'text', 'tilte', __( 'عنوان العنصر' ) ),
                Field::make( 'textarea', 'content', __( 'محتوي العنصر' ) )
                ->set_rows( 4 ),
            ) )->set_header_template( '
            <% if (tilte) { %>
                <%- tilte %>
            <% } %>
        ' )
        ))
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
                Field::make( 'multiselect', 'crb_overview_fields', __( 'Select app overview Fileds To Show' ) )
                ->add_options( $this->ag_fields_array() ),
                Field::make( 'multiselect', 'crb_details_fields', __( 'Select app details Fileds To Show' ) )
                ->add_options( $this->ag_fields_array() )      
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
        Field::make( 'multiselect', 'crb_available_fields', __( 'Select Fileds To Show for web' ) )
        ->add_options( $this->ag_fields_array() )
        
        ) );

        Container::make( 'term_meta', __( 'Select Fileds To Show in apk' ) )
        ->where( 'term_taxonomy', '=', 'property_type' )
        ->add_fields( array(
            Field::make( 'complex', 'app_available_extra_fields', __( 'Add Property Steps for Mobile' ) )
            ->add_fields( array(
                Field::make( 'text', 'tilte', __( 'عنوان الصفحة' ) ),
                Field::make( 'multiselect', 'fields', __( 'اختيار الحقول:' ) )
                ->add_options( $this->ag_fields_array() )
            ) )
        
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
        if( is_array( $adp_details_fields ) ){
            $fields_builder = $adp_details_fields['enabled'];
            unset($fields_builder['placebo']);
        }
        return $fields_builder;
    }
    
    /**
     * prop_multi_step_fileds
     *
     * @return void
     */
    public function prop_multi_step_fileds(){      
        $main_fields  = ag_get_property_fields();
        // $extra_fields = ag_get_property_fields_extra();
        $extra_fields = [];
        // $all_fields   = array_merge($main_fields , (array)$extra_fields);
        $all_fields   = $main_fields;
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

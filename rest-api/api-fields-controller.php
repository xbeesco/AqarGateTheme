<?php


/**
 * theme_get_fields_fun
 *
 * @return array/fields
 */
function ag_get_property_fields()
{
    $property_label_terms = get_terms (
        array(
            "property_label"
        ),
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
        )
    );
    $property_status_terms = get_terms (
        array(
            "property_status"
        ),
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
        )
    );
    $prop_type = get_terms (
        array(
            "property_type"
        ),
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
        )
    );

    $property_state_terms = get_terms (
        array(
            "property_state"
        ),
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
            'parent' => 0
        )
    );

    $property_city_terms = get_terms (
        array(
            "property_city"
        ),
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
            'parent' => 0
        )
    );
    $property_area_terms = get_terms (
        array(
            "property_area"
        ),
        array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
            'parent' => 0
        )
    );

    $property_state = ag_get_taxonomies_with_id_value( 'property_state', $property_state_terms, -1);
    $property_city  = ag_get_taxonomies_with_id_value( 'property_state', $property_city_terms, -1);
    $property_area  = ag_get_taxonomies_with_id_value( 'property_state', $property_area_terms, -1);
    
    /**
     *  Theme field builder
     * -------------------------------------------------------------------------
     */
   $themeFields = [ 
            [
                'id'          => 'prop_title',
                'field_id'    => 'prop_title',
                'type'        => 'text',
                'label'       => houzez_option('cl_prop_title', 'Property Title').houzez_required_field('title'),
                'placeholder' => houzez_option('cl_prop_title_plac', 'Enter your property title'),
                'options'     => '',
                'required'    => 1,
            ],
            [
                'id'          => 'prop_des',
                'field_id'    => 'prop_des',
                'type'        => 'textarea',
                'label'       => houzez_option('cl_content', 'Content'),
                'placeholder' => '',
                'options'     => '',
                'required'    => 0,
            ],
            [
                'id'          => 'prop_labels',
                'field_id'    => 'prop_labels[]',
                'type'        => 'select',
                'label'       => houzez_option('cl_prop_label', 'Property Label').houzez_required_field('prop_labels'),
                'placeholder' => '',
                'options'     => ag_get_taxonomies_with_id_value( 'property_label', $property_label_terms, -1),
                'required'    => 1,
            ],
            [
                'id'          => 'prop_status',
                'field_id'    => 'prop_status[]',
                'type'        => 'select',
                'label'       => houzez_option('cl_prop_status', 'Property Status').houzez_required_field('prop_status'),
                'placeholder' => '',
                'options'     => ag_get_taxonomies_with_id_value( 'property_status', $property_status_terms, -1),
                'required'    => 1,
            ],
            [
                'id'          => 'prop_type',
                'field_id'    => 'prop_type[]',
                'type'        => 'select',
                'label'       => houzez_option('cl_prop_type', 'Property Type').houzez_required_field('prop_type'),
                'placeholder' => '',
                'options'     => ag_get_taxonomies_with_id_value( 'property_type', $prop_type, -1),
                'required'    => 1,
            ],
            [
                'id'          => 'prop_price',
                'field_id'    => 'prop_price',
                'type'        => 'text',
                'label'       => houzez_option('cl_sale_price', 'Sale or Rent Price').houzez_required_field('sale_rent_price'),
                'placeholder' => houzez_option('cl_sale_price_plac', 'Enter the price'),
                'options'     => '',
                'required'    => 1,
            ],

            //location
            [
                'id'          => 'geocomplete',
                'field_id'    => 'geocomplete',
                'type'        => 'text',
                'label'       => houzez_option('cl_address', 'Address').houzez_required_field('property_map_address'),
                'placeholder' => houzez_option('cl_address_plac', 'Enter your property address'),
                'options'     => '',
                'required'    => 1,
            ],
            [
                'id'          => 'administrative_area_level_1',
                'field_id'    => 'administrative_area_level_1',
                'type'        => 'select',
                'label'       => houzez_option('cl_state', 'County/State').houzez_required_field('state'),
                'placeholder' => '',
                'options'     => $property_state,
                'required'    => 1,
            ],
            [
                'id'          => 'city',
                'field_id'    => 'locality',
                'type'        => 'select',
                'label'       => houzez_option( 'cl_city', 'City' ).houzez_required_field('city'),
                'placeholder' => '',
                'options'     => $property_city,
                'required'    => 1,
            ],
            [
                'id'          => 'neighborhood',
                'field_id'    => 'neighborhood',
                'type'        => 'select',
                'label'       => houzez_option( 'cl_area', 'Area' ).houzez_required_field('area'),
                'placeholder' => '',
                'options'     => $property_area,
                'required'    => 1,
            ],
            [
                'id'          => 'latitude',
                'field_id'    => 'lat',
                'type'        => 'text',
                'label'       => houzez_option( 'cl_latitude', 'Latitude' ),
                'placeholder' => houzez_option('cl_latitude_plac', 'Enter address latitude'),
                'options'     => '',
                'required'    => 0,
            ],
            [
                'id'          => 'longitude',
                'field_id'    => 'lng',
                'type'        => 'text',
                'label'       => houzez_option( 'cl_longitude', 'Longitude' ),
                'placeholder' => houzez_option('cl_longitude_plac', 'Enter address longitude'),
                'options'     => '',
                'required'    => 0,
            ],
            [
                'id'          => 'prop_featured',
                'field_id'    => 'prop_featured',
                'label'       => houzez_option('cl_make_featured', 'Do you want to mark this property as featured?'),
                'type'        => 'radio',
                'label'       => houzez_option( 'cl_longitude', 'Longitude' ),
                'placeholder' => '',
                'options'     => '',
                'value'       => 0,
                'required'    => 0,
            ],
            [
                'id'          => 'login-required',
                'field_id'    => 'login-required',
                'label'       => houzez_option('cl_login_view', 'The user must be logged in to view this property'),
                'type'        => 'radio',
                'placeholder' => '',
                'options'     => '',
                'value'       => 0,
                'required'    => 0,
            ],
            [
                'id'          => 'property_disclaimer',
                'field_id'    => 'property_disclaimer',
                'type'        => 'textarea',
                'label'       => houzez_option('cl_disclaimer', 'Disclaimer'),
                'placeholder' => '',
                'options'     => '',
                'required'    => 0,
            ],
            [
                'id'          => 'gdpr_agreement',
                'field_id'    => 'gdpr_agreement',
                'type'        => 'checkbox',
                'label'       => houzez_option( 'cls_gdpr', 'GDPR Agreement *' ),
                'placeholder' => '',
                'options'     => '',
                'required'    => 1,
            ],
            
        
    ];

     /**
     *  Property feature fields
     * -------------------------------------------------------------------------
     */
    $property_features = get_terms( 'property_feature', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false ) );

    $features_terms_id = array();
    if (houzez_edit_property()) {
        $features_terms = get_the_terms( $property_data->ID, 'property_feature' );
        if ( $features_terms && ! is_wp_error( $features_terms ) ) {
            foreach( $features_terms as $feature ) {
                $features_terms_id[] = intval( $feature->term_id );
            }
        }
    }
    if( !empty( $property_features ) ) {
        $count = 1;
        foreach( $property_features as $feature ) {
            if ( in_array( $feature->term_id, $features_terms_id ) ) {
                $themeFields[] = [
                    'id'          => 'feature-' . esc_attr( $count ) . '',
                    'label'       => esc_attr( $feature->name ),
                    'field_id'    => 'prop_features[]',
                    'type'        => 'checkbox',
                    'value'       => esc_attr( $feature->term_id ),
                    'placeholder' => '',
                    'options'     => '',
                    'required'    => 0,
                ];

            } else {
                $themeFields[] = [
                    'id'          => 'feature-' . esc_attr( $count ) . '',
                    'label'       => esc_attr( $feature->name ),
                    'field_id'    => 'prop_features[]',
                    'type'        => 'checkbox',
                    'value'       => esc_attr( $feature->term_id ),
                    'placeholder' => '',
                    'options'     => '',
                    'required'    => 0,
                ];
            }
            $count++;
        }
    }

    // $allFields = array_merge( $themeFields, $fields_custom );

    return $themeFields;

}

/**
 * ag_get_property_fields_builder
 *
 * @return void
 */
function ag_get_property_fields_builder(){

     /**
     *  custom field builder
     * -------------------------------------------------------------------------
     */
    $adp_details_fields = houzez_option('adp_details_fields');
    $fields_builder = $adp_details_fields['enabled'];
    unset($fields_builder['placebo']);
    if ( isset($_GET) && !empty( $_GET['tax_id'] ) ) {
          $ag_fields    = carbon_get_term_meta( $_GET['tax_id'], 'crb_available_fields' );
        if( empty( $ag_fields ) ) {
          $term = get_term( $_GET['tax_id'], 'property_type');
          $termParent = ( $term->parent == 0 ) ? $term : get_term( $term->parent, 'property_type' );
          $ag_fields  = carbon_get_term_meta( $termParent->term_id, 'crb_available_fields' );
        }
          
        // return houzez_details_section_fields(); 

        if ( !empty( $ag_fields ) ) {
            $fields_builder = array_flip( $ag_fields );
        }else{
            return $ag_fields;
        }
        $listing_data_composer = houzez_option('preview_data_composer');
        $data_composer = $listing_data_composer['enabled'];
        // return $fields_builder;
    }
    
    if ( $fields_builder ) {
        $fields_custom = [];
        foreach ( $fields_builder as $key => $value ) {
            if( !in_array($key, houzez_details_section_fields())) { 
                $field_array = Houzez_Fields_Builder::get_field_by_slug($key);
                $field_title = houzez_wpml_translate_single_string($field_array['label']);
                $placeholder = houzez_wpml_translate_single_string($field_array['placeholder']);

                $field_name = $field_array['field_id'];
                $field_type = $field_array['type'];
                
                if($field_type === 'select' || $field_type ==='multiselect' || $field_type ==='checkbox_list') { 
                    $field_options = $field_array['fvalues'];
                    $options = unserialize( $field_options );
                    $option = [];
                    foreach ( $options as $key => $val ) {
                        $option[] = [
                            "id" => $key,
                            "name" => $val
                        ];
                    }
                    $field_array['options'] = $option;
                 } else {
                    $field_array['options'] = '';
                 }
                
                $fields_custom[] = $field_array;

           } else if( in_array($key, houzez_details_section_fields() ) ){

        //    array( "beds","baths","rooms","area-size","area-size-unit","land-area","land-area-unit",
        //     "garage","garage-size","property-id", "year" );
             
            if( $key === 'beds' ) {
                $fields_custom[] = [
                    'id'          => 'prop_beds',
                    'field_id'    => 'prop_beds',
                    'type'        => 'number',
                    'label'       => houzez_option('cl_bedrooms', 'Bedrooms').houzez_required_field('bedrooms'),
                    'placeholder' => houzez_option('cl_bedrooms_plac', 'Enter number of bedrooms'),
                    'options'     => '',
                    'required'    => 0,
                ];
              }
              if( $key === 'baths' ) {
                $fields_custom[] = [
                    'id'          => 'prop_baths',
                    'field_id'    => 'prop_baths',
                    'type'        => 'number',
                    'label'       => houzez_option('cl_bathrooms', 'Bathrooms').houzez_required_field('bathrooms'),
                    'placeholder' => houzez_option('cl_bathrooms_plac', 'Enter number of bathrooms'),
                    'options'     => '',
                    'required'    => 0,
                ];
              }
              if( $key === 'rooms' ) {
                $fields_custom[] =             [
                    'id'          => 'prop_rooms',
                    'field_id'    => 'prop_rooms',
                    'type'        => 'number',
                    'label'       => houzez_option('cl_rooms', 'Rooms').houzez_required_field('rooms'),
                    'placeholder' => houzez_option('cl_bedrooms_plac', 'Enter number of bedrooms'),
                    'options'     => '',
                    'required'    => 0,
                ];
              }
              if( $key === 'area-size' ) {
                $fields_custom[] = [
                    'id'          => 'prop_size',
                    'field_id'    => 'prop_size',
                    'type'        => 'text',
                    'label'       => houzez_option('cl_area_size', 'Area Size').houzez_required_field('area_size'),
                    'placeholder' => houzez_option('cl_area_size_plac', 'Enter property area size'),
                    'options'     => '',
                    'required'    => 1,
                ];
              }
              if( $key === 'area-size-unit' ) {
                global $area_prefix_default, $area_prefix_changeable;
                $fields_custom[] = [
                    'id'          => 'prop_size_prefix',
                    'field_id'    => 'prop_size_prefix',
                    'type'        => 'text',
                    'label'       => houzez_option('cl_area_size_postfix', 'Size Postfix'),
                    'placeholder' => houzez_option('cl_area_size_postfix_plac', 'Enter the size postfix'),
                    'options'     => '',
                    'required'    => 1,
                ];
              }
              if( $key === 'land-area' ) {
                $fields_custom[] =             [
                    'id'          => 'prop_land_area',
                    'field_id'    => 'prop_land_area',
                    'type'        => 'number',
                    'label'       => houzez_option('cl_land_size', 'Land Area').houzez_required_field( 'land_area' ),
                    'placeholder' => houzez_option('cl_land_size_postfix_plac', 'Enter property land area postfix'),
                    'options'     => '',
                    'required'    => 0,
                ];
              }
              if( $key === 'land-area-unit' ) {
                $fields_custom[] = [
                    'id'          => 'prop_land_area_prefix',
                    'field_id'    => 'prop_land_area_prefix',
                    'type'        => 'text',
                    'label'       => houzez_option('cl_land_size_postfix', 'Land Area Size Postfix'),
                    'placeholder' => houzez_option('cl_land_size_postfix_plac', 'Enter property land area postfix'),
                    'options'     => '',
                    'required'    => 1,
                ];
              }
              if( $key === 'garage' ) {
                $fields_custom[] =  [
                    'id'          => 'prop_garage',
                    'field_id'    => 'prop_garage',
                    'type'        => 'number',
                    'label'       => houzez_option('cl_garage', 'Garages').houzez_required_field('garages'),
                    'placeholder' => houzez_option('cl_garage_plac', 'Enter number of garages'),
                    'options'     => '',
                    'required'    => 0,
                ];
              }
              if( $key === 'garage-size' ) {
                $fields_custom[] =  [
                    'id'          => 'prop_garage_size',
                    'field_id'    => 'prop_garage_size',
                    'type'        => 'number',
                    'label'       => houzez_option('cl_garage_size', 'Garages Size'),
                    'placeholder' => houzez_option('cl_garage_size_plac', 'Enter the garages size'),
                    'options'     => '',
                    'required'    => 0,
                ];
              }
              if( $key === 'property-id' ) {
                $fields_custom[] =  [
                    'id'          => 'property_id',
                    'field_id'    => 'property_id',
                    'type'        => 'text',
                    'label'       => houzez_option('cl_prop_id', 'Property ID').houzez_required_field( 'prop_id' ),
                    'placeholder' => houzez_option('cl_prop_id_plac', 'Enter property ID'),
                    'options'     => '',
                    'required'    => 1,
                ];
              }
              if( $key === 'year' ) {
                $fields_custom[] = [
                    'id'          => 'prop_year_built',
                    'field_id'    => 'prop_year_built',
                    'type'        => 'text',
                    'label'       => houzez_option('cl_year_built', 'Year Built').houzez_required_field( 'year_built' ),
                    'placeholder' => houzez_option('cl_year_built_plac', 'Enter year built'),
                    'options'     => '',
                    'required'    => 1,
                ];
              }

           }
        }

    
        return $fields_custom;
    }
    
}

/**
 * ag_get_taxonomies_with_id_value
 *
 * @param  mixed $taxonomy
 * @param  mixed $parent_taxonomy
 * @param  mixed $taxonomy_id
 * @param  mixed $prefix
 * @return void
 */
function ag_get_taxonomies_with_id_value($taxonomy, $parent_taxonomy, $taxonomy_id, $child_terms = false ){
    
    
    if (!empty($parent_taxonomy)) {

        foreach ( $parent_taxonomy as $key => $term ) {

         $property_type_icon = carbon_get_term_meta( $term->term_id, 'property_type_icon' );
         $icon_type  = get_term_meta($term->term_id, 'fave_feature_icon_type', true);
         $icon_class = get_term_meta($term->term_id, 'fave_prop_features_icon', true);
         $img_icon   = get_term_meta($term->term_id, 'fave_feature_img_icon', true);
         $term_link  = get_term_link($term->term_id, 'property_feature');
         $feature_icon = '';


            if( $icon_type == 'custom' ) {
                $icon_url = wp_get_attachment_url( $img_icon );
                if(!empty($icon_url)) {
                    $feature_icon = esc_url($icon_url);
                }
            } else {
                if( !empty( $icon_class ) )
                $feature_icon = $icon_class;
            }

            if( 'property_type' === $taxonomy ) {
                $options[$key] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'category_icon' => $property_type_icon
                ];
            } 
            elseif( 'property_feature' === $taxonomy ){
                $options[$key] = [
                    'id' => $term->term_id,
                    'name' =>  $term->name,
                    'category_icon' => $feature_icon
                ];
            }
            
            else {
                $options[$key] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                ];
            }
                

            $get_child_terms = get_terms( 
                array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'parent' => $term->term_id
            ));

            if ( !empty($get_child_terms)  ) {
                ag_get_taxonomies_with_id_value( $taxonomy, $get_child_terms, $taxonomy_id, $child_terms );
            } 

            if( empty( $term->parent ) && $child_terms === true ) {
                unset($options[$key]);
            }
        }
        return array_values( $options );
    }

    
}

/*-----------------------------------------------------------------------------------*/
// Generate Hirarchical terms
/*-----------------------------------------------------------------------------------*/
    
    /**
     * ag_hirarchical_options
     *
     * @param  mixed $taxonomy_name
     * @param  mixed $taxonomy_terms
     * @param  mixed $searched_term
     * @param  mixed $prefix
     * @return void
     */
    function ag_hirarchical_options($taxonomy_name, $taxonomy_terms, $searched_term, $prefix = " " ){
        $options = [];
        if (!empty($taxonomy_terms) && taxonomy_exists($taxonomy_name)) {
            foreach ($taxonomy_terms as $term) {

                if( $taxonomy_name == 'property_area' ) {
                    $term_meta= get_option( "_houzez_property_area_$term->term_id");
                    $parent_city = sanitize_title($term_meta['parent_city']);
                    $parent_city_id = get_term_by( 'slug', urldecode($parent_city), 'property_city' )->term_id;

                    if ( class_exists( 'sitepress' ) ) {
                        $default_lang = apply_filters( 'wpml_default_language', NULL );
                        $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_area', true, $default_lang );
                        $term_meta= get_option( "_houzez_property_area_$term_id_default");
                        $parent_city = sanitize_title($term_meta['parent_city']);
                        $parent_city = get_term_by( 'slug', $parent_city, 'property_city' )->slug;
                    }

                    if ( $parent_city_id  == $searched_term ) {
                        $options[] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'parent-id'   => $parent_city_id,
                            'parent-name' => urldecode($parent_city)
                        ];
                    } 
                    if ( $searched_term == -1 ) { 
                        $options[] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'parent-id'   => $parent_city_id,
                            'parent-name' => urldecode($parent_city)
                        ];
                    }
                    
                } elseif( $taxonomy_name == 'property_city' ) {
                    $term_meta= get_option( "_houzez_property_city_$term->term_id");
                    $parent_state = sanitize_title($term_meta['parent_state']);
                    $parent_state_id = get_term_by( 'slug', urldecode($parent_state), 'property_state' )->term_id;
                    if ( class_exists( 'sitepress' ) ) {
                        $default_lang = apply_filters( 'wpml_default_language', NULL );
                        $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_city', true, $default_lang );
                        $term_meta= get_option( "_houzez_property_city_$term_id_default");
                        $parent_state = sanitize_title($term_meta['parent_state']);
                        $parent_state = get_term_by( 'slug', $parent_state, 'property_state' )->slug;
                    }

                    if ( $searched_term == $parent_state_id ) {
                        $options[] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'parent-id'   => $parent_state_id,
                            'parent-name' => urldecode($parent_state)
                        ];
                    } 
                    if ( $searched_term == -1 ) {
                        $options[] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'parent-id'   => $parent_state_id,
                            'parent-name' => urldecode($parent_state)
                        ];
                    }

                } elseif( $taxonomy_name == 'property_state' ) {

                    $term_meta = get_option( "_houzez_property_state_$term->term_id");
                    $parent_country = sanitize_title($term_meta['parent_country']);
                    $parent_country_id = get_term_by( 'slug', urldecode($parent_country), 'property_country' )->term_id;

                    if ( class_exists( 'sitepress' ) ) {
                        $default_lang = apply_filters( 'wpml_default_language', NULL );
                        $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_state', true, $default_lang );
                        $term_meta= get_option( "_houzez_property_state_$term_id_default");
                        $parent_country = sanitize_title($term_meta['parent_country']);
                        $parent_country = get_term_by( 'slug', $parent_country, 'property_country' )->slug;       
                    }

                    if ( $searched_term == $parent_country_id ) {
                        $options[] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'parent-id'   => $parent_country_id,
                            'parent-name' => urldecode($parent_country)
                        ];
                    } 
                    if ( $searched_term == -1 ) {
                        $options[] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'parent-id'   => $parent_country_id,
                            'parent-name' => urldecode($parent_country)
                        ];
                    }

                } elseif( $taxonomy_name == 'property_country' ) {
            
                    // if ($searched_term == $term->slug) {
                    //     echo '<option data-ref="' . urldecode($term->slug) . '" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    // } else {
                    //     echo '<option data-ref="' . urldecode($term->slug) . '" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    // }

                } else {

                    // if ($searched_term == $term->slug) {
                    //     echo '<option value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    // } else {
                    //     echo '<option value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    // }
                }


                $child_terms = get_terms($taxonomy_name, array(
                    'hide_empty' => false,
                    'parent' => $term->term_id
                ));

                if (!empty($child_terms)) {
                    ag_hirarchical_options( $taxonomy_name, $child_terms, $searched_term, "- ".$prefix );
                }
            }

            return $options;
        }
    }

<?php
add_filter( 'rwmb_meta_boxes', 'houzez_listings_templates_metaboxes', 7 );
function houzez_listings_templates_metaboxes( $meta_boxes ) {
    $houzez_prefix = 'fave_';

    $page_filters = houzez_option('houzez_page_filters');

    $agents_for_templates = array_slice( houzez_get_agents_array(), 1, null, true );
    $agencies_for_templates = array_slice( houzez_get_agency_array(), 1, null, true );

    $prop_states = array();
    $prop_locations = array();
    $prop_types = array();
    $prop_status = array();
    $prop_features = array();
    $prop_neighborhood = array();
    $prop_label = array();
    $prop_country = array();

    
    // houzez_get_terms_array( 'property_feature', $prop_features );
    // houzez_get_terms_array( 'property_status', $prop_status );
    // houzez_get_terms_array( 'property_type', $prop_types );
    // houzez_get_terms_array( 'property_city', $prop_locations );
    // houzez_get_terms_array( 'property_state', $prop_states );
    // houzez_get_terms_array( 'property_label', $prop_label );
    // houzez_get_terms_array( 'property_area', $prop_neighborhood );
    // houzez_get_terms_array( 'property_country', $prop_country );
    
    
    $meta_boxes[] = array(
        'id'        => 'fave_page_content_area',
        'title'     => esc_html__('Content Area', 'houzez'),
        'post_types'     => array( 'page' ),
        'context'    => 'normal',
        //'priority'   => 'normal',
        'show'       => array(
            'template' => array(
                'template/template-listing-list-v1.php',
                'template/template-listing-list-v1-fullwidth.php',
                'template/template-listing-list-v2.php',
                'template/template-listing-list-v2-fullwidth.php',
                'template/template-listing-list-v5.php',
                'template/template-listing-list-v5-fullwidth.php',
                'template/template-listing-grid-v1.php',
                'template/template-listing-grid-v1-fullwidth-2cols.php',
                'template/template-listing-grid-v1-fullwidth-3cols.php',
                'template/template-listing-grid-v1-fullwidth-4cols.php',
                'template/template-listing-grid-v2.php',
                'template/template-listing-grid-v2-fullwidth-2cols.php',
                'template/template-listing-grid-v2-fullwidth-3cols.php',
                'template/template-listing-grid-v2-fullwidth-4cols.php',
                'template/template-listing-grid-v4.php',
                'template/template-listing-list-v4.php',
                'template/template-listing-grid-v5.php',
                'template/template-listing-grid-v5-fullwidth-2cols.php',
                'template/template-listing-grid-v5-fullwidth-3cols.php',
                'template/template-listing-grid-v6.php',
                'template/template-listing-grid-v6-fullwidth-2cols.php',
                'template/template-listing-grid-v6-fullwidth-3cols.php',
                'template/template-listing-grid-v3.php',
                'template/template-listing-grid-v3-fullwidth-2cols.php',
                'template/template-listing-grid-v3-fullwidth-3cols.php',
                'template/template-listing-list-v7.php',
                'template/template-listing-list-v7-fullwidth.php',
                'template/template-listing-grid-v7.php',
                'template/template-listing-grid-v7-fullwidth-2cols.php',
                'template/template-listing-grid-v7-fullwidth-3cols.php',
                'template/template-listing-grid-v7-fullwidth-4cols.php',
                'template/properties-parallax.php',
                'template/template-agents.php',
                'template/template-agencies.php',
                'template/template-compare.php',
                'template/template-search.php',
                'template/property-listings-map.php'
            ),
        ),
        'fields'    => array(
            array(
                'id' => $houzez_prefix."listing_page_content_area",
                'name' => esc_html__('Show Content Above Footer?', 'houzez'),
                'desc' => esc_html__( 'Yes', 'houzez' ),
                'type' => 'checkbox',
                'std' => 0,
            ),
        )
    );


    $property_area_filter = array(
        'id'   => 'field_id_area',
        'type' => 'divider',
        'class' => 'houzez_hidden',
        'columns' => 6,
    );
    if( !in_array('property_area', (array)$page_filters) ) {
        $property_area_filter = array(
                'name'      => esc_html__('Areas', 'houzez'),
                'id'        => $houzez_prefix . 'area',
                'type'      => 'select',
                'options'   => $prop_neighborhood,
                'desc'      => '',
                'columns' => 6,
                'select_all_none' => true,
                'multiple' => true
            );
    }

    $property_type_filter = array(
        'id'   => 'field_id_type',
        'type' => 'divider',
        'class' => 'houzez_hidden',
        'columns' => 6,
    );
    if( !in_array('property_type', (array)$page_filters) ) {
        $property_type_filter = array(
                'name'      => esc_html__('Types', 'houzez'),
                'id'        => $houzez_prefix . 'types',
                'type'      => 'select',
                'options'   => $prop_types,
                'desc'      => '',
                'columns' => 6,
                'select_all_none' => true,
                'multiple' => true
            );
    }

    $property_status_filter = array(
        'id'   => 'field_id_statuses',
        'type' => 'divider',
        'class' => 'houzez_hidden',
        'columns' => 6,
    );
    if( !in_array('property_status', (array)$page_filters) ) {
        $property_status_filter = array(
                'name'      => esc_html__('Status', 'houzez' ),
                'id'        => $houzez_prefix . 'status',
                'type'      => 'select',
                'options'   => $prop_status,
                'desc'      => '',
                'columns' => 6,
                'select_all_none' => true,
                'multiple' => true
            );
    }

    $property_label_filter = array(
        'id'   => 'field_id_label',
        'type' => 'divider',
        'class' => 'houzez_hidden',
        'columns' => 6,
    );
    if( !in_array('property_label', (array)$page_filters) ) {
        $property_label_filter = array(
                'name'      => esc_html__('Labels', 'houzez'),
                'id'        => $houzez_prefix . 'labels',
                'type'      => 'select',
                'options'   => $prop_label,
                'desc'      => '',
                'columns' => 6,
                'select_all_none' => true,
                'multiple' => true
            );
    }

    $property_country_filter = array(
        'id'   => 'field_id_country',
        'type' => 'divider',
        'class' => 'houzez_hidden',
        'columns' => 6,
    );
    if( !in_array('property_country', (array)$page_filters) ) {
        $property_country_filter = array(
                'name'      => esc_html__('Countries', 'houzez'),
                'id'        => $houzez_prefix . 'countries',
                'type'      => 'select',
                'options'   => $prop_country,
                'desc'      => '',
                'columns' => 6,
                'select_all_none' => true,
                'multiple' => true
            );
    }

    $property_state_filter = array(
        'id'   => 'field_id_state',
        'type' => 'divider',
        'class' => 'houzez_hidden',
        'columns' => 6,
    );
    if( !in_array('property_state', (array)$page_filters) ) {
        $property_state_filter = array(
                'name'      => esc_html__('States', 'houzez'),
                'id'        => $houzez_prefix . 'states',
                'type'      => 'select',
                'options'   => $prop_states,
                'desc'      => '',
                'columns' => 6,
                'select_all_none' => true,
                'multiple' => true
            );
    }

    $property_city_filter = array(
        'id'   => 'field_id_city',
        'type' => 'divider',
        'class' => 'houzez_hidden',
        'columns' => 6,
    );
    if( !in_array('property_city', (array)$page_filters) ) {
        $property_city_filter = array(
                'name'      => esc_html__('Cities', 'houzez'),
                'id'        => $houzez_prefix . 'locations',
                'type'      => 'select',
                'options'   => $prop_locations,
                'desc'      => '',
                'columns' => 6,
                'select_all_none' => true,
                'multiple' => true
            );
    }

    $property_feature_filter = array(
        'id'   => 'field_id_feature',
        'type' => 'divider',
        'class' => 'houzez_hidden',
        'columns' => 6,
    );
    if( !in_array('property_feature', (array)$page_filters) ) {
        $property_feature_filter = array(
                'name'      => esc_html__('Features', 'houzez' ),
                'id'        => $houzez_prefix . 'features',
                'type'      => 'select',
                'options'   => $prop_features,
                'desc'      => '',
                'columns' => 6,
                'select_all_none' => true,
                'multiple' => true
            );
    }

    /*------------------------------------------------------------------------
    * Listings templates
    *-----------------------------------------------------------------------*/
    $meta_boxes[] = array(
        'id'        => 'fave_listing_template',
        'title'     => esc_html__('Listings Template Settings', 'houzez'),
        'post_types'     => array( 'page' ),
        'context'    => 'normal',
        'priority'   => 'high',
        'show'       => array(
            'template' => array(
                'template/template-listing-list-v1.php',
                'template/template-listing-list-v1-fullwidth.php',
                'template/template-listing-list-v2.php',
                'template/template-listing-list-v2-fullwidth.php',
                'template/template-listing-list-v5.php',
                'template/template-listing-list-v5-fullwidth.php',
                'template/template-listing-grid-v1.php',
                'template/template-listing-grid-v1-fullwidth-2cols.php',
                'template/template-listing-grid-v1-fullwidth-3cols.php',
                'template/template-listing-grid-v1-fullwidth-4cols.php',
                'template/template-listing-grid-v2.php',
                'template/template-listing-grid-v2-fullwidth-2cols.php',
                'template/template-listing-grid-v2-fullwidth-3cols.php',
                'template/template-listing-grid-v2-fullwidth-4cols.php',
                'template/template-listing-grid-v4.php',
                'template/template-listing-list-v4.php',
                'template/template-listing-grid-v5.php',
                'template/template-listing-grid-v5-fullwidth-2cols.php',
                'template/template-listing-grid-v5-fullwidth-3cols.php',
                'template/template-listing-grid-v6.php',
                'template/template-listing-grid-v6-fullwidth-2cols.php',
                'template/template-listing-grid-v6-fullwidth-3cols.php',
                'template/template-listing-grid-v3.php',
                'template/template-listing-grid-v3-fullwidth-2cols.php',
                'template/template-listing-grid-v3-fullwidth-3cols.php',
                'template/template-listing-list-v7.php',
                'template/template-listing-list-v7-fullwidth.php',
                'template/template-listing-grid-v7.php',
                'template/template-listing-grid-v7-fullwidth-2cols.php',
                'template/template-listing-grid-v7-fullwidth-3cols.php',
                'template/template-listing-grid-v7-fullwidth-4cols.php',
                'template/property-listings-map.php',
                'template/properties-parallax.php',
            ),
        ),
        'fields'    => array(
            array(
                'id' => $houzez_prefix."prop_no",
                'name' => esc_html__('Number of listings to display', 'houzez'),
                'desc' => "",
                'type' => 'number',
                'std' => "9",
                'tab' => 'listing_temp_general',
                'columns' => 6
            ),
            array(
                'name'      => esc_html__('Order Properties By', 'houzez'),
                'id'        => $houzez_prefix . 'properties_sort',
                'type'      => 'select',
                'options'   => array(
                    'a_title'  => esc_html__('Title - ASC', 'houzez'),
                    'd_title'  => esc_html__('Title - DESC', 'houzez'),
                    'd_date'  => esc_html__('Date New to Old', 'houzez'),
                    'a_date'  => esc_html__('Date Old to New', 'houzez'),
                    'd_price' => esc_html__('Price (High to Low)', 'houzez'),
                    'a_price' => esc_html__('Price (Low to High)', 'houzez'),
                    'featured_first' => esc_html__('Show Featured Listings on Top', 'houzez'),
                    'featured' => esc_html__('Show Featured Listings', 'houzez'),
                ),
                'std'       => array( 'd_date' ),
                'desc'      => '',
                'columns' => 6
            ),
            
            array(
                'id' => $houzez_prefix."listings_tabs",
                'name' => esc_html__('Tabs', 'houzez'),
                'desc' => esc_html__('Enable/disable the tabs on the listing page(not work for half map and parallax listing template)', 'houzez'),
                'type' => 'select',
                'std' => "disable",
                'options' => array('enable' => esc_html__('Enabled', 'houzez'), 'disable' => esc_html__('Disabled', 'houzez')),
                'columns' => 12
            ),
            array(
                'id' => $houzez_prefix."listings_tab_1",
                'name' => esc_html__('Tabs One', 'houzez'),
                'desc' => esc_html__('Choose the property status for this tab', 'houzez'),
                'type' => 'select',
                'std' => "",
                'options' => $prop_status,
                'columns' => 6
            ),
            array(
                'id' => $houzez_prefix."listings_tab_2",
                'name' => esc_html__('Tabs Two', 'houzez'),
                'desc' => esc_html__('Choose the property status for this tab', 'houzez'),
                'type' => 'select',
                'std' => "",
                'options' => $prop_status,
                'columns' => 6
            ),

            //Filters
            $property_type_filter,
            $property_status_filter,
            $property_label_filter,
            $property_country_filter,
            $property_state_filter,
            $property_city_filter,
            $property_feature_filter,
            $property_area_filter,
            

            array(
                'name'            => esc_html__( 'Properties by Agents', 'houzez' ),
                'id'              => $houzez_prefix. 'properties_by_agents',
                'type'            => 'select',
                'options'         => $agents_for_templates,
                'multiple'        => true,
                'select_all_none' => true,
                'columns'         => 6,
            ),

            array(
                'name'            => esc_html__( 'Properties by Agencies', 'houzez' ),
                'id'              => $houzez_prefix. 'properties_by_agency',
                'type'            => 'select',
                'options'         => $agencies_for_templates,
                'multiple'        => true,
                'select_all_none' => true,
                'columns'         => 6,
            ),

            array(
                'name'      => esc_html__('Min Price', 'houzez'),
                'id'        => $houzez_prefix . 'min_price',
                'type'      => 'number',
                'options'   => '',
                'desc'      => '',
                'columns' => 6
            ),
            array(
                'name'      => esc_html__('Max Price', 'houzez'),
                'id'        => $houzez_prefix . 'max_price',
                'type'      => 'number',
                'options'   => '',
                'desc'      => '',
                'columns' => 6
            ),

            array(
                'id'      => $houzez_prefix. 'properties_min_beds',
                'name'    => esc_html__( 'Minimum Beds', 'houzez' ),
                'type'    => 'number',
                'step'    => 'any',
                'min'     => 0,
                'std'     => 0,
                'columns' => 6,
            ),

            array(
                'id'      => $houzez_prefix. 'properties_min_baths',
                'name'    => esc_html__( 'Minimum Baths', 'houzez' ),
                'type'    => 'number',
                'step'    => 'any',
                'min'     => 0,
                'std'     => 0,
                'columns' => 6,
            ),
        )
    );
    

    return apply_filters('houzez_listings_templates_meta', $meta_boxes);

}

function houzez_page_header_metaboxes( $meta_boxes ) {
    $houzez_prefix = 'fave_';
    
    $prop_locations = array();

    // houzez_get_terms_array( 'property_city', $prop_locations );
    

    $meta_boxes[] = array(
        'id'        => 'fave_page_settings',
        'title'     => esc_html__('Page Header Options', 'houzez' ),
        'post_types'     => array( 'page' ),
        'context' => 'normal',
        'hide'       => array(
            'template' => array(
                'template/template-splash.php',
                'template/property-listings-map.php',
                'template/user_dashboard_submit.php',
                'template/template-compare.php',
                'template/template-thankyou.php',
                'template/template-packages.php',
                'template/template-payment.php',
                'template/template-stripe-charge.php',
                'template/user_dashboard_crm.php',
                'template/user_dashboard_favorites.php',
                'template/user_dashboard_insight.php',
                'template/user_dashboard_invoices.php',
                'template/user_dashboard_membership.php',
                'template/user_dashboard_messages.php',
                'template/user_dashboard_profile.php',
                'template/user_dashboard_properties.php',
                'template/user_dashboard_saved_search.php',
            ),
        ),

        'fields'    => array(
            array(
                'name'      => esc_html__('Header Type', 'houzez' ),
                'id'        => $houzez_prefix . 'header_type',
                'type'      => 'select',
                'options'   => array(
                    'none' => esc_html__('None', 'houzez' ),
                    'property_slider' => esc_html__('Properties Slider', 'houzez' ),
                    'rev_slider' => esc_html__('Revolution Slider', 'houzez' ),
                    'property_map' => esc_html__('Properties Map', 'houzez' ),
                    'static_image' => esc_html__('Image', 'houzez' ),
                    'video' => esc_html__('Video', 'houzez' ),
                    'elementor' => esc_html__('Elementor', 'houzez' ),
                ),
                'std'       => array( 'none' ),
                'desc'      => esc_html__('Select the page header type','houzez')
            ),
            array(
                'name'      => esc_html__('Title', 'houzez' ),
                'id'        => $houzez_prefix . 'page_header_title',
                'placeholder' => esc_html__( 'Enter the title', 'houzez' ),
                'type' => 'text',
                'std' => '',
                'desc' => '',
                'visible' => array( $houzez_prefix.'header_type', 'in', array( 'static_image', 'video' ) )
            ),
            array(
                'name'      => esc_html__('Subtitle', 'houzez' ),
                'id'        => $houzez_prefix . 'page_header_subtitle',
                'placeholder' => esc_html__( 'Enter the subtitle', 'houzez' ),
                'type' => 'text',
                'std' => '',
                'desc' => '',
                'visible' => array( $houzez_prefix.'header_type', 'in', array( 'static_image', 'video' ) )
            ),
            array(
                'name'      => esc_html__('Image', 'houzez' ),
                'id'        => $houzez_prefix . 'page_header_image',
                'type' => 'image_advanced',
                'max_file_uploads' => 1,
                'desc'      => '',
                'visible' => array( $houzez_prefix.'header_type', '=', 'static_image' )
            ),

            array(
                'name' => esc_html__('MP4 File', 'houzez'),
                'id' => "{$houzez_prefix}page_header_bg_mp4",
                'placeholder' => esc_html__( 'Upload the video file', 'houzez' ),
                'desc' => esc_html__( 'This file is mandatory', 'houzez' ),
                'type' => 'file_input',
                'visible' => array( $houzez_prefix.'header_type', '=', 'video' )
            ),
            array(
                'name' => esc_html__('WEBM File', 'houzez'),
                'id' => "{$houzez_prefix}page_header_bg_webm",
                'placeholder' => esc_html__( 'Upload the video file', 'houzez' ),
                'desc' => esc_html__( 'This file is mandatory', 'houzez' ),
                'type' => 'file_input',
                'visible' => array( $houzez_prefix.'header_type', '=', 'video' )
            ),
            array(
                'name' => esc_html__('OGV File', 'houzez'),
                'id' => "{$houzez_prefix}page_header_bg_ogv",
                'placeholder' => esc_html__( 'Upload the video file', 'houzez' ),
                'desc' => esc_html__( 'This file is mandatory', 'houzez' ),
                'type' => 'file_input',
                'visible' => array( $houzez_prefix.'header_type', '=', 'video' )
            ),

            array(
                'name'      => esc_html__('Video Image', 'houzez'),
                'id'        => $houzez_prefix . 'page_header_video_img',
                'placeholder' => esc_html__( 'Upload a video cover image', 'houzez' ),
                'desc' => esc_html__( 'This file is mandatory', 'houzez' ),
                'type' => 'image_advanced',
                'max_file_uploads' => 1,
                'visible' => array( $houzez_prefix.'header_type', '=', 'video' )
            ),

            array(
                'name'      => esc_html__('Height', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the banner height', 'houzez' ),
                'id'        => $houzez_prefix . 'page_header_image_height',
                'type' => 'text',
                'std' => '',
                'desc' => esc_html__('Default is 600px', 'houzez'),
                'visible' => array( $houzez_prefix.'header_type', 'in', array( 'static_image', 'video' ) )
            ),

            array(
                'name'      => esc_html__('Height Mobile', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the banner height for mobile devices', 'houzez' ),
                'id'        => $houzez_prefix . 'header_image_height_mobile',
                'type' => 'text',
                'std' => '',
                'desc' => esc_html__('Default is 400px', 'houzez'),
                'visible' => array( $houzez_prefix.'header_type', 'in', array( 'static_image', 'video' ) )
            ),

            array(
                'name'      => esc_html__('Overlay Color Opacity', 'houzez' ),
                'id'        => $houzez_prefix . 'page_header_image_opacity',
                'type' => 'select',
                'options' => array(
                    '0' => '0',
                    '0.1' => '1',
                    '0.2' => '2',
                    '0.3' => '3',
                    '0.35' => '3.5',
                    '0.4' => '4',
                    '0.5' => '5',
                    '0.6' => '6',
                    '0.7' => '7',
                    '0.8' => '8',
                    '0.9' => '9',
                    '1' => '10',
                ),
                'std'       => array( '0.35' ),
                'visible' => array( $houzez_prefix.'header_type', 'in', array( 'static_image', 'video' ) )
            ),

            array(
                'name'      => esc_html__('Banner Search', 'houzez' ),
                'id'        => $houzez_prefix . 'page_header_search',
                'type' => 'switch',
                'style'     => 'rounded',
                'on_label'  => esc_html__('Enable', 'houzez' ),
                'off_label' => esc_html__('Disable', 'houzez' ),
                'std'       => 0,
                'desc' => '',
                'visible' => array( $houzez_prefix.'header_type', 'in', array( 'static_image', 'video' ) )
            ),
            
            array(
                'name'      => esc_html__('Full Screen', 'houzez' ),
                'id'        => $houzez_prefix . 'header_full_screen',
                'type' => 'switch',
                'style'     => 'rounded',
                'on_label'  => esc_html__('Enable', 'houzez' ),
                'off_label' => esc_html__('Disable', 'houzez' ),
                'std'       => 0,
                'desc'      => esc_html__('If "Enable" it will fit according to screen size' ,'houzez'),
                'visible' => array( $houzez_prefix.'header_type', 'in', array( 'static_image', 'video', 'property_map', 'property_slider' ) )
            ),
    
            /*------------------ Slider Revolution -------------*/
            array(
                'name'      => esc_html__('Revolution Slider', 'houzez' ),
                'id'        => $houzez_prefix . 'page_header_revslider',
                'type' => 'select_advanced',
                'std' => '',
                'options' => houzez_get_revolution_slider(),
                'multiple'    => false,
                'placeholder' => esc_html__( 'Select an Slider', 'houzez' ),
                'desc' => '',
                'hidden' => array( $houzez_prefix.'header_type', '!=', 'rev_slider' )
            ),

            /*----------------- Map Settings ----------------*/
            array(
                'name'      => esc_html__('Select City', 'houzez'),
                'id'        => $houzez_prefix . 'map_city',
                'type'      => 'select',
                'options'   => $prop_locations,
                'desc'      => esc_html__('Select a city where to start the property map on header page. You can select multiple cities or keep all un-select to display properties from all the cities', 'houzez'),
                'multiple' => true,
                'class' => 'houzez-map-cities',
                'hidden' => array( 'fave_header_type', '!=', 'property_map' )
            ),
        )
    );

    return apply_filters('houzez_page_header_meta', $meta_boxes);

}

add_filter( 'rwmb_meta_boxes', 'houzez_page_header_metaboxes' );
<?php

function ag_publish_edit_alert( $prop_id, $msg, $user_id ){

    include_once ( AG_DIR.'classes/class-rega.php' );
    $valid_status = REGA::is_valid_ad( $prop_id, $user_id );

    if( $valid_status === true ){
        $class = 'success';
    }else{
        $msg = 'عذرا لن يتم نشر الاعلان بناء علي تعليمات الهيئة العامه للعقار للاسباب الاتية </br></br>';
        foreach ($valid_status as $error_msg) {
            $msg .= "- $error_msg </br>";
        }
        $class = 'danger';
    }

    if( (isset($_GET['success']) && $_GET['success'] == 1 ) || (isset($_GET['updated']) && $_GET['updated'] == 1 )  ) { ?>
<div class="alert alert-<?= $class; ?>" role="alert">
    <?= $msg; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php } 

};

/* -------------------------------------------------------------------------- */
/*                              home page filter                              */
/* -------------------------------------------------------------------------- */
function houzez_get_search_taxonomies($taxonomy_name, $searched_data = "", $args = array() ){
        
    $hide_empty = false;
    if($taxonomy_name == 'property_city' || $taxonomy_name == 'property_area' || $taxonomy_name == 'property_country' || $taxonomy_name == 'property_state') {
        $hide_empty = houzez_hide_empty_taxonomies();
    }
    
    $defaults = array(
        'taxonomy' => $taxonomy_name,
        'orderby'       => 'name',
        'order'         => 'ASC',
        'hide_empty'    => $hide_empty,
    );

    $args       = wp_parse_args( $args, $defaults );
    $taxonomies = get_terms( $args );

    if ( empty( $taxonomies ) || is_wp_error( $taxonomies ) ) {
        return false;
    }

    $output = '';
    foreach( $taxonomies as $category ) {
        if( $category->parent == 0 ) {

            $data_attr = $data_subtext = '';

            if( $taxonomy_name == 'property_city' ) {
                $term_meta= get_option( "_houzez_property_city_$category->term_id");
                $parent_state = isset($term_meta['parent_state']) ? $term_meta['parent_state'] : '';
                $parent_state = sanitize_title($parent_state);
                $data_attr = 'data-belong="'.esc_attr($parent_state).'"';
                $data_subtext = '';

            } elseif( $taxonomy_name == 'property_area' ) {
                $term_meta= get_option( "_houzez_property_area_$category->term_id");
                $parent_city = isset($term_meta['parent_city']) ? $term_meta['parent_city'] : '';
                $parent_city = sanitize_title($parent_city);
                $data_attr = 'data-belong="'.esc_attr($parent_city).'"';
                $data_subtext = '';

            } elseif( $taxonomy_name == 'property_state' ) {
                $term_meta = get_option( "_houzez_property_state_$category->term_id");
                $parent_country = isset($term_meta['parent_country']) ? $term_meta['parent_country'] : '';
                $parent_country = sanitize_title($parent_country);
                $data_attr = 'data-belong="'.esc_attr($parent_country).'"';
                $data_subtext = 'data-subtext=""';

            }

      if ( (!empty($searched_data) && is_array( $searched_data ) ) && in_array( $category->slug, $searched_data ) ) {
                $output.= '<option data-ref="'.esc_attr($category->slug).'" '.$data_attr.' '.$data_subtext.' value="' . esc_attr($category->slug) . '" selected="selected">'. esc_attr($category->name) . '</option>';
            } else {
                $output.= '<option data-ref="'.esc_attr($category->slug).'" '.$data_attr.' '.$data_subtext.' value="' . esc_attr($category->slug) . '">' . esc_attr($category->name) . '</option>';
            }

            foreach( $taxonomies as $subcategory ) {
                if($subcategory->parent == $category->term_id) {

                    $data_attr_child = '';
                    if( $taxonomy_name == 'property_city' ) {
                        $term_meta= get_option( "_houzez_property_city_$subcategory->term_id");
                        $parent_state = isset($term_meta['parent_state']) ? $term_meta['parent_state'] : '';
                        $parent_state = sanitize_title($parent_state);
                        $data_attr_child = 'data-belong="'.esc_attr($parent_state).'"';

                    } elseif( $taxonomy_name == 'property_area' ) {
                        $term_meta= get_option( "_houzez_property_area_$subcategory->term_id");
                        $parent_city = isset($term_meta['parent_city']) ? $term_meta['parent_city'] : '';
                        $parent_city = sanitize_title($parent_city);
                        $data_attr_child = 'data-belong="'.esc_attr($parent_city).'"';

                    } elseif( $taxonomy_name == 'property_state' ) {
                        $term_meta= get_option( "_houzez_property_state_$subcategory->term_id");
                        $parent_country = isset($term_meta['parent_country']) ? $term_meta['parent_country'] : '';
                        $parent_country = sanitize_title($parent_country);
                        $data_attr_child = 'data-belong="'.esc_attr($parent_country).'"';
                    }

       if ( (!empty($searched_data) && is_array( $searched_data ) ) && in_array( $category->slug, $searched_data ) ) {
                        $output.= '<option data-ref="'.esc_attr($subcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subcategory->slug) . '" selected="selected"> - '. esc_attr($subcategory->name) . '</option>';
                    } else {
                        $output.= '<option data-ref="'.esc_attr($subcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subcategory->slug) . '"> - ' . esc_attr($subcategory->name) . '</option>';
                    }

                    foreach( $taxonomies as $subsubcategory ) {
                        if($subsubcategory->parent == $subcategory->term_id) {

                            $data_attr_child = '';
                            if( $taxonomy_name == 'property_city' ) {
                                $term_meta= get_option( "_houzez_property_city_$subsubcategory->term_id");
                                $parent_state = isset($term_meta['parent_state']) ? $term_meta['parent_state'] : '';
                                $parent_state = sanitize_title($parent_state);
                                $data_attr_child = '';

                            } elseif( $taxonomy_name == 'property_area' ) {
                                $term_meta= get_option( "_houzez_property_area_$subsubcategory->term_id");
                                $parent_city = isset($term_meta['parent_city']) ? $term_meta['parent_city'] : '';
                                $parent_city = sanitize_title($parent_city);
                                $data_attr_child = 'data-belong="'.esc_attr($parent_city).'"';

                            } elseif( $taxonomy_name == 'property_state' ) {
                                $term_meta= get_option( "_houzez_property_state_$subsubcategory->term_id");
                                $parent_country = isset($term_meta['parent_country']) ? $term_meta['parent_country'] : '';
                                $parent_country = sanitize_title($parent_country);
                                $data_attr_child = 'data-belong="'.esc_attr($parent_country).'"';
                            }

                            if ( !empty($searched_data) && in_array( $subsubcategory->slug, $searched_data ) ) {
                                $output.= '<option data-ref="'.esc_attr($subsubcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subsubcategory->slug) . '" selected="selected"> - '. esc_attr($subsubcategory->name) . '</option>';
                            } else {
                                $output.= '<option data-ref="'.esc_attr($subsubcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subsubcategory->slug) . '"> -- ' . esc_attr($subsubcategory->name) . '</option>';
                            }
                        }
                    }
                }
            }
        }
    }
    echo $output;

}

function get_houzez_listing_expire( $postID ) {
    global $post;
    if(empty($post->ID)){
        $postID = $postID;
    }else{
        $postID = $post->ID;
    }
    //If manual expire date set
    $manual_expire = get_post_meta( $postID, 'houzez_manual_expire', true );
    if( !empty( $manual_expire )) {
        $expiration_date = get_post_meta( $postID,'_houzez_expiration_date',true );
        return ( $expiration_date ? get_date_from_gmt(gmdate('Y-m-d H:i:s', $expiration_date), get_option('date_format').' '.get_option('time_format')) : __('Never', 'houzez'));
    } else {
        $submission_type = houzez_option('enable_paid_submission');
        // Per listing
        if( $submission_type == 'per_listing' || $submission_type == 'free_paid_listing' || $submission_type == 'no' ) {
            $per_listing_expire_unlimited = houzez_option('per_listing_expire_unlimited');
            if( $per_listing_expire_unlimited != 0 ) {
                $per_listing_expire = houzez_option('per_listing_expire');

                $publish_date = isset($post->post_date) ? $post->post_date : '0';
                return date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $publish_date. ' + '.$per_listing_expire.' days' ) );
            }
        } elseif( $submission_type == 'membership' ) {
            $post_author = get_post_field( 'post_author', $postID );
            $post_date = get_post_field( 'post_date', $postID );
            // $author_id = get_post_field ('post_author', $postID);
            $package_id = get_post_field ('post_author', $postID);
            if( !empty($package_id) ) {
                $billing_time_unit = get_post_meta( $package_id, 'fave_billing_time_unit', true );
                $billing_unit = get_post_meta( $package_id, 'fave_billing_unit', true );

                if( $billing_time_unit == 'Day')
                    $billing_time_unit = 'days';
                elseif( $billing_time_unit == 'Week')
                    $billing_time_unit = 'weeks';
                elseif( $billing_time_unit == 'Month')
                    $billing_time_unit = 'months';
                elseif( $billing_time_unit == 'Year')
                    $billing_time_unit = 'years';

                $publish_date = $post_date;
                return date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $publish_date. ' + '.$billing_unit.' '.$billing_time_unit ) );
            }
        }
    }
}

function houzez_get_agent_info_top($args, $type, $is_single = true)
{
    global $post;

    $view_listing = houzez_option('agent_view_listing');
    $agent_phone_num = houzez_option('agent_phone_num');

    if( empty($args['agent_name']) ) {
        return '';
    }
    
    $author_id = get_post_field ( 'post_author', $args['agent_id'] );
    if( empty( $author_id ) ){
        $author_id = $post->post_author;
    }

    $id_number = get_user_meta( $author_id, 'aqar_author_id_number', true );
    $ad_number = get_user_meta( $author_id, 'aqar_author_ad_number', true);
    $type_id   = get_user_meta( $author_id, 'aqar_author_type_id', true);
    $brokerage_license_number = get_user_meta( $author_id, 'brokerage_license_number', true);
    $user_role = houzez_user_role_by_user_id( $author_id );
    $Advertiser_character = '';
    if( $user_role == "houzez_agent"  ) { $Advertiser_character =  "مفوض";}
    elseif( $user_role == "houzez_agency" ) { $Advertiser_character =  "مفوض"; }
    elseif( $user_role == "houzez_owner"  ) { $Advertiser_character =  "مالك"; } 
    elseif( $user_role == "houzez_buyer"  ) { $Advertiser_character =  "مفوض"; } 
    elseif( $user_role == "houzez_seller" ) { $Advertiser_character =  "مفوض" ; }
    elseif( $user_role == "houzez_manager") { $Advertiser_character = "مفوض"; }

    if ($type == 'for_grid_list') {
        return '<a href="' . $args['link'] . '">' . $args['agent_name'] . '</a> ';

    } elseif ($type == 'agent_form') {
        $output = '';

        $output .= '<div class="agent-details cc">';
            $output .= '<div class="d-flex flex-column align-items-center">';
            $output .= '<h3> معلومات الوسيط </h3>';
                $output .= '<div class="agent-image" style="margin-bottom: 20px;">';
                    
                    if ( $is_single == false ) {
                        $output .= '<input type="checkbox" class="houzez-hidden" checked="checked" class="multiple-agent-check" name="target_email[]" value="' . $args['agent_email'] . '" >';
                    }

                    $output .= '<img class="rounded" src="' . $args['picture'] . '" alt="' . $args['agent_name'] . '">';

                $output .= '</div>';

                $output .= '<ul class="agent-information list-unstyled">';

                    if (!empty($args['agent_name'])) {
                        $output .= '<li class="agent-name">';
                            $output .= '<i class="houzez-icon icon-single-neutral mr-1"></i> اسم الوسيط :  '.$args['agent_name'];
                        $output .= '</li>';
                    }
                    if( $author_id  && !empty( $brokerage_license_number ) ) {
                        $output .= '<li class="agent-ad-number">';
                          $output .= '<i class="houzez-icon icon-accounting-document mr-1"></i> رقم رخصة فال :  ' . esc_attr( $brokerage_license_number );
                        $output .= '</li>';
                    }

                    // if( $author_id  && !empty( $Advertiser_character ) ) {
                    //     $output .= '<li class="Advertiser_character">';
                    //       $output .= '<i class="houzez-icon icon-accounting-document mr-1"></i>  صفه المعلن :  ' . esc_attr( $Advertiser_character );
                    //     $output .= '</li>';
                    // }
                    
                    if ( $is_single == false && !empty($args['agent_mobile'])) {
                        $output .= '<li class="agent-phone agent-phone-hidden">';
                            $output .= '<i class="houzez-icon icon-phone mr-1"></i> ' . esc_attr($args['agent_mobile']);
                        $output .= '</li>';
                    }

                    
                    if($view_listing != 0) {
                        $output .= '<li class="agent-link">';
                            $output .= '<a href="' . $args['link'] . '">' . houzez_option('spl_con_view_listings', 'View listings') . '</a>';
                        $output .= '</li>';
                    }


                $output .= '</ul>';
            $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}

function houzez_get_localization() {


    $localization = array(

        /*------------------------------------------------------
        * Theme
        *------------------------------------------------------*/
        'choose_currency'   => esc_html__( 'Choose Currency', 'houzez' ),
        'disable' 			=> esc_html__( 'Disable', 'houzez' ),
        'enable' 			=> esc_html__( 'Enable', 'houzez' ),
        'any' 				=> esc_html__( 'Any', 'houzez' ),
        'none'				=> esc_html__( 'None', 'houzez' ),
        'by_text' 			=> esc_html__( 'by', 'houzez' ),
        'at_text' 			=> esc_html__( 'at', 'houzez' ),
        'goto_dash' 		=> esc_html__( 'Go to Dashboard', 'houzez' ),
        'read_more' 		=> esc_html__( 'Read More', 'houzez' ),
        'continue_reading' 	=> esc_html__( 'Continue reading', 'houzez' ),
        'follow_us' 		=> esc_html__( 'Follow us', 'houzez' ),
        'property' 			=> esc_html__( 'Property', 'houzez' ),
        'properties' 		=> esc_html__( 'Properties', 'houzez' ),
        '404_page' 			=> esc_html__( 'Back to Homepage', 'houzez' ),
        'at' 				=> esc_html__( 'at', 'houzez' ),
        'licenses' 			=> esc_html__( 'License', 'houzez' ),
        'agent_license' 	=> esc_html__( 'Agent License', 'houzez' ),
        'agent_ad_number' 	=> esc_html__( 'Agent Ad Number', 'houzez' ),
        'tax_number' 		=> esc_html__( 'Tax Number', 'houzez' ),
        'languages' 		=> esc_html__( 'Language', 'houzez' ),
        'specialties_label' => esc_html__( 'Specialties', 'houzez' ),
        'service_area' 		=> esc_html__( 'Service Areas', 'houzez' ),
        'agency_agents' 	=> esc_html__( 'Agents:', 'houzez' ),
        'agency_properties' => esc_html__( 'Properties listed', 'houzez' ),
        'email' 			=> esc_html__( 'Email', 'houzez' ),
        'website' 			=> esc_html__( 'Website', 'houzez' ),
        'submit' 			=> esc_html__( 'Submit', 'houzez' ),
        'join_discussion' 	=> esc_html__( 'Join The Discussion', 'houzez' ),
        'your_name'	 		=> esc_html__( 'Your Name', 'houzez' ),
        'your_email'	 	=> esc_html__( 'Your Email', 'houzez' ),
        'blog_search'	 	=> esc_html__( 'Search', 'houzez' ),
        'featured'	 		=> esc_html__( 'Featured', 'houzez' ),
        'label_featured'	=> esc_html__( 'Featured', 'houzez' ),
        'view_my_prop'	 	=> esc_html__( 'View Listings', 'houzez' ),
        'office_colon'	 	=> esc_html__( 'Office', 'houzez' ),
        'mobile_colon'	 	=> esc_html__( 'Mobile', 'houzez' ),
        'fax_colon'	 	    => esc_html__( 'Fax', 'houzez' ),
        'email_colon'	 	=> esc_html__( 'Email', 'houzez' ),
        'follow_us'	 		=> esc_html__( 'Follow us', 'houzez' ),
        'type'		 		=> esc_html__( 'Type', 'houzez' ),
        'address'		 	=> esc_html__( 'Address', 'houzez' ),
        'city'		 		=> esc_html__( 'City', 'houzez' ),
        'state_county'      => esc_html__( 'State/County', 'houzez' ),
        'zip_post'		    => esc_html__( 'Zip/Post Code', 'houzez' ),
        'country'		    => esc_html__( 'Country', 'houzez' ),
        'prop_size'		    => esc_html__( 'Property Size', 'houzez' ),
        'prop_id'		    => esc_html__( 'Property ID', 'houzez' ),
        'garage'		    => esc_html__( 'Garage', 'houzez' ),
        'garage_size'		=> esc_html__( 'Garage Size', 'houzez' ),
        'year_built'		=> esc_html__( 'Year Built', 'houzez' ),
        'time_period'		=> esc_html__( 'Time Period', 'houzez' ),
        'unlimited_listings'=> esc_html__( 'Unlimited Listings', 'houzez' ),
        'featured_listings' => esc_html__( 'Featured Listings', 'houzez' ),
        'package_taxes' 	=> esc_html__( 'Tax', 'houzez' ),
        'get_started' 		=> esc_html__( 'Get Started', 'houzez' ),
        'save_search'	 	=> esc_html__( 'Save this Search?', 'houzez' ),
        'sort_by'		 	=> esc_html__( 'Sort by:', 'houzez' ),
        'default_order'	    => esc_html__( 'Default Order', 'houzez' ),
        'price_low_high'	=> esc_html__( 'Price (Low to High)', 'houzez' ),
        'price_high_low'	=> esc_html__( 'Price (High to Low)', 'houzez' ),
        'date_old_new'		=> esc_html__( 'Date Old to New', 'houzez' ),
        'date_new_old'		=> esc_html__( 'Date New to Old', 'houzez' ),
        'bank_transfer'		=> esc_html__( 'Direct Bank Transfer', 'houzez' ),
        'order_number'		=> esc_html__( 'Order Number', 'houzez' ),
        'payment_method' 	=> esc_html__( 'Payment Method', 'houzez' ),
        'date'				=> esc_html__( 'Date', 'houzez' ),
        'total'				=> esc_html__( 'Total', 'houzez' ),
        'submit'			=> esc_html__( 'Submit', 'houzez' ),
        'search_listing'	=> esc_html__( 'Search Listing', 'houzez' ),


        'view_all_results'	=> esc_html__( 'View All Results', 'houzez' ),
        'listins_found'		=> esc_html__( 'Listings found', 'houzez' ),
        'auto_result_not_found'		=> esc_html__( 'We didn’t find any results', 'houzez' ),
        'auto_view_lists'		=> esc_html__( 'View Listing', 'houzez' ),
        'auto_listings'		=> esc_html__( 'Listing', 'houzez' ),
        'auto_city'		=> esc_html__( 'City', 'houzez' ),
        'auto_area'		=> esc_html__( 'Area', 'houzez' ),
        'auto_state'		=> esc_html__( 'State', 'houzez' ),


        'search_invoices'	=> esc_html__( 'Search Invoices', 'houzez' ),
        'total_invoices'	=> esc_html__( 'Total Invoices:', 'houzez' ),
        'start_date'		=> esc_html__( 'Start date', 'houzez' ),
        'end_date'			=> esc_html__( 'End date', 'houzez' ),
        'invoice_type'		=> esc_html__( 'Type', 'houzez' ),
        'invoice_listing'	=> esc_html__( 'Listing', 'houzez' ),
        'invoice_package'	=> esc_html__( 'Package', 'houzez' ),
        'invoice_feat_list'		=> esc_html__( 'Listing with Featured', 'houzez' ),
        'invoice_upgrade_list'	=> esc_html__( 'Upgrade to Featured', 'houzez' ),
        'invoice_status'	=> esc_html__( 'Status', 'houzez' ),
        'paid'				=> esc_html__( 'Paid', 'houzez' ),
        'not_paid'			=> esc_html__( 'Not Paid', 'houzez' ),
        'order'				=> esc_html__( 'Order', 'houzez' ),
        'view_details'		=> esc_html__( 'View Details', 'houzez' ),
        'payment_details'	=> esc_html__( 'Payment details', 'houzez' ),
        'property_title'	=> esc_html__( 'Property Title', 'houzez' ),
        'billing_type'		=> esc_html__( 'Billing Type', 'houzez' ),
        'billing_for'		=> esc_html__( 'Billing For', 'houzez' ),
        'invoice_price'		=> esc_html__( 'Total Price:', 'houzez' ),
        'customer_details'	=> esc_html__( 'Customer details:', 'houzez' ),
        'customer_name'		=> esc_html__( 'Name:', 'houzez' ),
        'customer_email'	=> esc_html__( 'Email:', 'houzez' ),
        'search_agency_name'	=> esc_html__( 'Enter agency name', 'houzez' ),
        'search_agent_name'	=> esc_html__( 'Enter agent name', 'houzez' ),
        'search_agent_btn'	=> esc_html__( 'Search Agent', 'houzez' ),
        'search_agency_btn'	=> esc_html__( 'Search Agency', 'houzez' ),
        'all_agent_cats'	=> esc_html__( 'All Categories', 'houzez' ),
        'all_agent_cities'	=> esc_html__( 'All Cities', 'houzez' ),


        'saved_search_not_found'=> esc_html__( 'You don\'t have any saved search', 'houzez' ),
        'properties_not_found'=> esc_html__( 'You don\'t have any properties yet!', 'houzez' ),
        'favorite_not_found'=> esc_html__( 'You don\'t have any favorite properties yet!', 'houzez' ),
        'email_already_registerd' => esc_html__( 'This email address is already registered', 'houzez' ),
        'invalid_email' => esc_html__( 'Invalid email address.', 'houzez' ),
        'houzez_plugin_required' => esc_html__( 'Please install and activate Houzez theme functionality plugin', 'houzez' ),

        // Agents
        'view_profile' => esc_html__( 'View Profile', 'houzez' ),

        /*------------------------------------------------------
        * Common
        *------------------------------------------------------*/
        'next_text' => esc_html__('Next', 'houzez'),
        'prev_text' => esc_html__('Prev', 'houzez'),
        'view_label' => esc_html__('View', 'houzez'),
        'views_label' => esc_html__('Views', 'houzez'),
        'visits_label' => esc_html__('Visits', 'houzez'),
        'unique_label' => esc_html__('Unique', 'houzez'),

        /*------------------------------------------------------
        * Custom Post Types
        *------------------------------------------------------*/


    );

    return $localization;
}

/*-----------------------------------------------------------------------------------*/
// Property edit taxonomy for multiple
/*-----------------------------------------------------------------------------------*/
    function ag_get_taxonomies_for_edit_listing_multivalue( $listing_id, $taxonomy ){

        $taxonomy_terms_ids= array();
        $taxonomy_terms = get_the_terms( $listing_id, $taxonomy );

        if ( $taxonomy_terms && ! is_wp_error( $taxonomy_terms ) ) {
            foreach( $taxonomy_terms as $term ) {
                $taxonomy_terms_ids[] = intval( $term->term_id );
            }
        }

        return $taxonomy_terms_ids;

    }
/*-----------------------------------------------------------------------------------*/
// Property edit meta
/*-----------------------------------------------------------------------------------*/
    function ag_get_field_meta( $key, $prop_id = '' ) {

        $prefix = 'fave_';
        $field_name = $prefix.$key;

        if ( !empty( $field_name ) && !empty( $prop_id )) {
            return get_post_meta( $prop_id, $field_name, true );
        } else {
            return;
        }
    }

/*-----------------------------------------------------------------------------------*/
// make json file for rest api
/*-----------------------------------------------------------------------------------*/
add_action( 'init', 'ag_make_json_file');
function ag_make_json_file( $data = array(), $filename = 'ag-cache-data' ){
    if (is_admin() && isset($_GET['page']) == 'crb_carbon_fields_container_ag_settings.php') {
        if ( isset($_GET['ag-update']) && $_GET['ag-update'] === '1') {
            
            $data = [];
            $data = ag_generate_cache_file();
            $data_2 = [];
            $data_2 = ag_generate_cache_location_file();

            if( !empty( $data ) || !empty( $data_2 ) ){
                $data      = json_encode( $data );
                $data_2    = json_encode( $data_2 );
                $folder    = AG_DIR. 'rest-json/';
                $file_name = $filename.'.json';
                $ag_cache_location_name = 'ag-cache-location-data.json';
                $file   = file_put_contents( $folder.$file_name, $data );
                $file_2 = file_put_contents( $folder.$ag_cache_location_name, $data_2 );
                $time   = date("F d, Y h:i:s A");
                if( !empty( $data ) ){
                    update_option( 'cache_last_update', $time, true );
                }
                if( !empty( $data_2 ) ){
                    update_option( 'cache_last_location_update', $time, true );
                }
                // return $file;
            }
        }
    }  
}   

/*-----------------------------------------------------------------------------------*/
// make json file for rest api
/*-----------------------------------------------------------------------------------*/
function ag_generate_cache_file(){

    $cache_data = [];
    $data ='';
    $time = date("F d, Y h:i:s A");
    update_option( 'cache_last_update', $time, true );
    $cache_data['cache_last_update'] =  $time;
 
    $args = array(
        'public'   => true,
        '_builtin' => false
      ); 

      $output = 'names'; // or objects
      $operator = 'and'; // 'and' or 'or'
      $taxonomies = get_taxonomies( $args, $output, $operator ); 
      if ( $taxonomies ) {
        unset($taxonomies['product_cat']);
        unset($taxonomies['product_tag']);
        unset($taxonomies['product_shipping_class']);
        unset($taxonomies['property_country']);
        unset($taxonomies['property_state']);
        unset($taxonomies['property_city']);
        unset($taxonomies['property_area']);

          foreach ( $taxonomies  as $taxonomy ) {
            $property_terms = get_terms ( array( 'taxonomy' => $taxonomy,'hide_empty' => false ) ); 
            if( $taxonomy === 'property_type' ){
                $cache_data[$taxonomy] = ag_get_taxonomies_with_id_value( $taxonomy, $property_terms, -1, true);     
            }else{
                $cache_data[$taxonomy] = ag_get_taxonomies_with_id_value( $taxonomy, $property_terms, -1, false);     
            }   
          }
      }

        $response['name'] = get_bloginfo( 'name' );
        $response['description'] = get_bloginfo( 'description' );
        $response['timezone_string'] = get_option( 'timezone_string' );
        $response['gmt_offset']  = get_option( 'gmt_offset' );
        $response['logo'] = carbon_get_theme_option( 'ag_logo' );
        $response['reload_gif'] = carbon_get_theme_option( 'ag_reload_gif' );
        $response['json'] = carbon_get_theme_option( 'ag_json' );
        $response['policy_page'] = carbon_get_theme_option( 'ag_policy' );
        $response['adv_term'] = carbon_get_theme_option( 'ag_adv' );
        $response['how_to_adv'] = carbon_get_theme_option( 'ag_how_adv' );

        $cache_data['siteinfo'] = $response;


        $app_available_fields = carbon_get_theme_option( 'app_available_fields' );      
        $fields_steps = [];
        foreach ( $app_available_fields as $key => $value ) {            
            $searchForId = AqarGateApi::searchForId ( $value['fields'], $data );  
            $fields_steps['screens_count'] = count($app_available_fields);
            $fields_steps['screen_'.$key] = [
                'title' => $value['tilte'],
                'data'  => $searchForId,
            ];  
        }

        $cache_data['main_fields'] = $fields_steps;


        $property_types = get_terms (
            array(
                "property_type"
            ),
            array(
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
            )
        );
        $term_extra_fields = [];
        foreach( $property_types as $key => $type ) {
            $app_available_fields = carbon_get_term_meta( $type->term_id, 'app_available_extra_fields' );
            if( !empty( $app_available_fields ) ) {
                $step = [];
                $screen = [];
                foreach ( $app_available_fields as $key => $value ) {            
                    $searchForId = AqarGateApi::searchForId ( $value['fields'], $data, true ); 
                    $step['screens_count'] = count($app_available_fields);
                    $step['screen_'.$key] = [
                            'title' => $value['tilte'],
                            'data'  => $searchForId,
                    ];
                }
                $term_extra_fields[$type->term_id] = $step;
            }else{
                $term_extra_fields[$type->term_id] = [];
            }
        }

        $cache_data['extra_fields'] = $term_extra_fields ;
        include_once ( AG_DIR . 'rest-api/api-membership-controller.php' );
        $cache_data['houzez_packages'] = ag_membership_type();
        $cache_data['profile_fields'] = AqarGateApi::profile_fields($data='');

        $user_types= ['houzez_owner','houzez_agent','houzez_seller','houzez_agency'];
        $signup_type_profile_fields = [];
        foreach ($user_types as $key => $type ) {
            $signup_type_profile_fields[$type] = AqarGateApi::signup_type_profile_fields( array('user_type' => $type) );
        }

        $cache_data['signup_type_profile_fields'] = $signup_type_profile_fields;

        $cache_data['signup_fields'] = [
            [
                'id'          => 'username',
                'field_id'    => 'username',
                'type'        => 'text',
                'label'       => __('اسم المستخدم','houzez'),
                'placeholder' => '',
                'options'     => '',
                'required'    => 1,
            ],
            [
                'id'          => 'email',
                'field_id'    => 'email',
                'type'        => 'email',
                'label'       => __('البريد الإلكتروني','houzez'),
                'placeholder' => '',
                'options'     => '',
                'required'    => 1,
            ],
            [
                'id'          => 'phone_number',
                'field_id'    => 'phone_number',
                'type'        => 'number',
                'label'       => __('رقم الجوال','houzez'),
                'placeholder' => '',
                'options'     => '',
                'required'    => 1,
            ],
        ];


        $cache_data['add_agent_fields'] = [
            [
                'id'          => 'aa_username',
                'field_id'    => 'aa_username',
                'type'        => 'text',
                'label'       => __('اسم المستخدم','houzez'),
                'placeholder' => '',
                'options'     => '',
                'value'       => '',
                'required'    => 1,
            ],
            [
                'id'          => 'aa_password',
                'field_id'    => 'aa_password',
                'type'        => 'password',
                'label'       => __('الباسورد','houzez'),
                'placeholder' => '',
                'options'     => '',
                'value'       => '',
                'required'    => 1,
            ],
            [
                'id'          => 'aa_email',
                'field_id'    => 'aa_email',
                'type'        => 'eamil',
                'label'       => __('الايميل','houzez'),
                'placeholder' => '',
                'options'     => '',
                'value'       => '',
                'required'    => 1,
            ],
            [
                'id'          => 'aa_phone',
                'field_id'    => 'aa_phone',
                'type'        => 'number',
                'label'       => __('التليفون','houzez'),
                'placeholder' => '',
                'options'     => '',
                'value'       => '',
                'required'    => 1,
            ]
        ];

        $user_role = [];
        $user_show_roles = houzez_option('user_show_roles');
        $show_hide_roles = houzez_option('show_hide_roles');

        if( $show_hide_roles['agent'] != 1 ) {
            $user_role[] = [ 'id' => 'houzez_agent', 'name' => houzez_option('agent_role')];
        }
        if( $show_hide_roles['agency'] != 1 ) {
            $user_role[] = [ 'id' => 'houzez_agency', 'name' => houzez_option('agency_role')];
        }
        if( $show_hide_roles['owner'] != 1 ) {
            $user_role[] = [ 'id' => 'houzez_owner', 'name' => houzez_option('owner_role')];
        }
        // if( $show_hide_roles['buyer'] != 1 ) {
        //     $user_role[] = [ 'id' => 'houzez_buyer', 'name' => houzez_option('buyer_role')];
        // }
        if( $show_hide_roles['seller'] != 1 ) {
            $user_role[] = [ 'id' => 'houzez_seller', 'name' => houzez_option('seller_role')];
        }

        $cache_data['user_role'] = $user_role;

   return $cache_data;
   
  }


/**
 * ag_generate_cache_location_file
 *
 * @return void
 */
function ag_generate_cache_location_file(){

    $cache_data = [];

    $time = date("F d, Y h:i:s A");
    $cache_data['cache_last_location_update'] =  $time;
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
    $searched_term = isset( $_POST[ 'country' ] ) ? $_POST[ 'country' ] : -1 ;
    $property_state = ag_hirarchical_options( 'property_state', $property_state_terms, $searched_term );
    if( count( $property_state ) == 0 ) {
        $property_state = [] ;
    }
    $cache_data['state']= $property_state;

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
    $searched_term = isset( $_POST[ 'state' ] ) ? $_POST[ 'state' ] : -1 ;
    $property_city = ag_hirarchical_options( 'property_city', $property_city_terms, $searched_term);

    if( count( $property_city ) == 0 ) {
        $property_city = [] ;
    }

    $cache_data['city']= $property_city;

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
    $searched_term = isset( $_POST[ 'city' ] ) ? $_POST[ 'city' ] : -1 ;
    $property_area = ag_hirarchical_options( 'property_area', $property_area_terms, $searched_term);

    if( count( $property_area ) == 0 ) {
        $property_area = [] ;
    }

    $cache_data['area']= $property_area ;

    return $cache_data;
}


/**
 * ag_get_generate_cache_file
 *
 * @return void
 */
function ag_get_generate_cache_file(){
    $generate_cache_file['cache_data'] = get_stylesheet_directory_uri(). "/rest-json/ag-cache-data.json" ;
    $generate_cache_file['cache_location_data'] = get_stylesheet_directory_uri(). "/rest-json/ag-cache-location-data.json" ;

    return $generate_cache_file;
}

function ag_hirarchical_location_data($taxonomy_name, $taxonomy_terms, $searched_term, $prefix = " " ){
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
                    $term_meta = get_option( "_houzez_property_area_$term_id_default");
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
  
  /**
   * houzez_packages_metaboxes
   *
   * @param  mixed $meta_boxes
   * @return void
   */
  function houzez_packages_metaboxes( $meta_boxes ) {
    $houzez_prefix = 'fave_';
    
    $meta_boxes[] = array(
        'title'  => esc_html__( 'Package Details', 'houzez' ),
        'post_types'  => array('houzez_packages'),
        'fields' => array(
            array(
                'id' => "{$houzez_prefix}billing_time_unit",
                'name' => esc_html__( 'Billing Period', 'houzez' ),
                'type' => 'select',
                'std' => "",
                'options' => array( 'Day' => esc_html__('Day', 'houzez' ), 'Week' => esc_html__('Week', 'houzez' ), 'Month' => esc_html__('Month', 'houzez' ), 'Year' => esc_html__('Year', 'houzez' ) ),
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}billing_unit",
                'name' => esc_html__( 'Billing Frequency', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the frequency number', 'houzez' ),
                'type' => 'text',
                'std' => "0",
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_listings",
                'name' => esc_html__( 'How many listings are included?', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the number of listings', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'columns' => 6,

            ),
            array(
                'id' => "{$houzez_prefix}package_users",
                'name' => esc_html__( 'How many Users are included?', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the number of Users', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'columns' => 6,

            ),
            array(
                'name'         => esc_html__( 'Upload the The Image For Package', 'houzez' ),
                'id'           => "{$houzez_prefix}package_image",
                'type'         => 'image',
                'force_delete' => false,
                'max_file_uploads' => 1,
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_color",
                'name' => esc_html__( 'Enter the Color Of Package', 'houzez' ),
                'type' => 'color',
                'std' => "",
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}unlimited_listings",
                'name' => esc_html__( "Unlimited listings", 'houzez' ),
                'type' => 'checkbox',
                'desc' => esc_html__('Unlimited listings', 'houzez'),
                'std' => "",
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_featured_listings",
                'name' => esc_html__( 'How many Featured listings are included?', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the number of listings', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_price",
                'name' => esc_html__( 'Package Price ', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the price', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_stripe_id",
                'name' => esc_html__( 'Package Stripe id (Example: gold_pack)', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_visible",
                'name' => esc_html__( 'Is It Visible?', 'houzez' ),
                'type' => 'select',
                'std' => "",
                'options' => array( 'yes' => esc_html__( 'Yes', 'houzez' ), 'no' => esc_html__( 'No', 'houzez' ) ),
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}stripe_taxId",
                'name' => esc_html__( 'Stripe Tax ID', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'placeholder' => esc_html__( 'Enter your stripe account tax id.', 'houzez' ),
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_tax",
                'name' => esc_html__( 'Taxes', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the tax percentage (Only digits)', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'columns' => 6,

            ),
            array(
                'id' => "{$houzez_prefix}package_images",
                'name' => esc_html__( 'How many images are included per listing?', 'houzez' ),
                'placeholder' => esc_html__( 'Enter the number of images', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'columns' => 6,

            ),
            array(
                'id' => "{$houzez_prefix}unlimited_images",
                'name' => esc_html__( "Unlimited Images", 'houzez' ),
                'type' => 'checkbox',
                'desc' => esc_html__('Same as defined in Theme Options', 'houzez'),
                'std' => "",
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_popular",
                'name' => esc_html__( 'Is Popular/Featured?', 'houzez' ),
                'type' => 'select',
                'std' => "no",
                'options' => array( 'no' => esc_html__( 'No', 'houzez' ), 'yes' => esc_html__( 'Yes', 'houzez' ) ),
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}package_custom_link",
                'name' => esc_html__( 'Custom Link', 'houzez' ),
                'desc' => esc_html__('Leave empty if you do not want to custom link.', 'houzez'),
                'placeholder' => esc_html__( 'Enter the custom link', 'houzez' ),
                'type' => 'text',
                'std' => "",
                'columns' => 6,

            ),
        ),
    );
    

    return apply_filters('houzez_packages_meta', $meta_boxes);

}

add_filter( 'rwmb_meta_boxes', 'houzez_packages_metaboxes' );

function aqargate_register_agency_agent( $username, $email, $user_id, $agency_id_cpt, $agency_ids_cpt, $agent_agency, $agent_category, $agent_city, $aa_phone  ) {

    // Create post object
    $args = array(
        'post_title'    => $username,
        'post_type' => 'houzez_agent',
        'post_status'   => 'publish'
    );

    // Insert the post into the database
    $post_id =  wp_insert_post( $args );
    update_post_meta( $post_id, 'houzez_user_meta_id', $user_id);  // used when agent custom post type updated
    update_user_meta( $user_id, 'fave_author_agent_id', $post_id) ;
    update_user_meta( $user_id, 'fave_author_agency_id', $agency_id_cpt) ;
    update_user_meta( $user_id, 'fave_author_company', get_the_title($agency_id_cpt)) ;
    update_user_meta( $user_id, 'fave_agent_agency', $agent_agency) ; // used for get user created by agency
    //update_user_meta( $user_id, 'fave_author_agency_id', $agent_agency) ; // used for get user created by agency
    update_post_meta( $post_id, 'fave_agent_email', $email) ;
    update_post_meta( $post_id, 'fave_agent_mobile', $aa_phone ) ;
    update_post_meta( $post_id, 'fave_agent_company', get_the_title($agency_id_cpt)) ;
    update_post_meta( $post_id, 'fave_agent_agencies', $agency_id_cpt) ;
    delete_post_meta( $agency_id_cpt, 'fave_agency_cpt_agent' );
    
    array_push( $agency_ids_cpt, $post_id);

    foreach ( $agency_ids_cpt as $agentID ) {
        if( !empty($agentID))
        add_post_meta( $agency_id_cpt, 'fave_agency_cpt_agent', $agentID );
    }

    if( ! empty( $agent_category ) ) {
        $agent_category = intval($agent_category);
        wp_set_object_terms( $post_id, $agent_category, 'agent_category' );
    } else {
        wp_set_object_terms( $post_id, '', 'agent_category' );
    }
    if( ! empty( $agent_city ) ) {
        $agent_city = intval($agent_city);
        wp_set_object_terms( $post_id, $agent_city, 'agent_city' );
    } else {
        wp_set_object_terms( $post_id, '', 'agent_city' );
    }
}



/**
 * ag_user_has_membership
 *
 * @param  mixed $user_id
 * @return void
 */
function ag_user_has_membership( $user_id ) {
    $has_package = get_the_author_meta( 'package_id', $user_id );
    $has_listing = get_the_author_meta( 'package_listings', $user_id );
    $has_role    = houzez_user_role_by_user_id( $user_id );

    $gency_users = get_agency_agent_ids( $user_id );
    if( in_array( $user_id, $gency_users ) ) {
        return true;
    }

    $agent_parent = get_user_meta( $user_id, 'fave_agent_agency', true );
    if( !empty( $agent_parent ) && is_numeric( $agent_parent ) ) {
        return ag_user_has_membership( $agent_parent );
    }

    if( houzez_is_admin() ) {
        return true;

    } else if( !empty( $has_package ) && ( $has_listing != 0 || $has_listing != '' ) ) { 
        return true;
    }
    return false;
}


/**
 * get_agency_agent_ids
 *
 * @param  mixed $user_id
 * @return void
 */
function get_agency_agent_ids( $user_id ){

    $wp_user_query = new WP_User_Query( array(
        array( 
        'role' => 'houzez_agent' 
        ),
        'meta_key' => 'fave_agent_agency',
        'meta_value' => $user_id
    ));

    $agents = $wp_user_query->get_results();

    $agents_list = [];
    if( !empty( $agents ) ) {
        foreach ( $agents as $agent ) {
            $agents_list[] = $agent->ID;
        }
    }

    return $agents_list;
}

/**
 * ag_get_user_package_id
 *
 * @param  mixed $user_id
 * @return void
 */
function ag_get_user_package_id( $user_id ){
    $remaining_listings = houzez_get_remaining_listings( $user_id );
    $pack_featured_remaining_listings = houzez_get_featured_remaining_listings( $user_id );
    $package_id = houzez_get_user_package_id( $user_id );

    if( empty( $package_id ) ) {
        $agent_parent = get_user_meta($user_id, 'fave_agent_agency', true);
        $package_id = houzez_get_user_package_id( $agent_parent );
    }

    return $package_id;
}

/**
 * ag_check_user_existing_package_status
 *
 * @param  mixed $userID
 * @param  mixed $packID
 * @return void
 */
function ag_check_user_existing_package_status( $userID, $packID ) {

    $has_package = get_the_author_meta( 'package_id', $userID );
    if( !empty( $has_package) ){
        $user_id = $userID ;
    } else {
        $agent_parent = get_user_meta( $userID, 'fave_agent_agency', true );
        if( !empty( $agent_parent ) && is_numeric( $agent_parent ) ) {
        $user_id = $agent_parent ;
        }   
    }

    $has_package = get_the_author_meta( 'package_id', $user_id );

    if( empty( $has_package) ){
        return true;
    }
    

    $pack_listings            =  get_post_meta( $packID, 'fave_package_listings', true );
    $pack_featured_listings   =  get_post_meta( $packID, 'fave_package_featured_listings', true );
    $pack_unlimited_listings  =  get_post_meta( $packID, 'fave_unlimited_listings', true );

    $has_role    = houzez_user_role_by_user_id( $user_id );
    $agent_num_posted_listings = 0;
    $agent_num_posted_featured_listings = 0;
    if( $has_role === 'houzez_agency' ) {
        $agency_agent = get_agency_agent_ids( $user_id );
        if( !empty( $agency_agent ) && is_array ($agency_agent ) ){
            foreach ($agency_agent as $agent) {
                $agent_num_posted_listings += houzez_get_user_num_posted_listings( $agent );
                $agent_num_posted_featured_listings += houzez_get_user_num_posted_featured_listings( $agent->ID ); 
            }
        }
    }
    $user_num_posted_listings = houzez_get_user_num_posted_listings( $user_id );
    $user_num_posted_listings = $user_num_posted_listings + $agent_num_posted_listings;
    $user_num_posted_featured_listings = houzez_get_user_num_posted_featured_listings( $user_id );
    $user_num_posted_featured_listings = $user_num_posted_featured_listings + $agent_num_posted_featured_listings;

    $current_listings =  get_user_meta( $user_id, 'package_listings', true ) ;

    if( $pack_unlimited_listings == 1 || $current_listings > 0 ) {
        return false;
    }
    
    // if is unlimited and go to non unlimited pack
    if ( $current_listings == -1 && $pack_unlimited_listings != 1 ) {
        return true;
    }

    if ( ( $user_num_posted_listings > $pack_listings ) || ( $user_num_posted_featured_listings > $pack_featured_listings ) ) {
        return true;
    } else {
        return false;
    }
}

/**================================================================= */

/**
 * ag_maybe_create_term
 *
 * @param  mixed $prop_id
 * @param  mixed $request
 * @return term_id / city / state / area 
 */
function ag_maybe_create_term ( $prop_id = '' , $response = '' ){

    if( empty ( $response ) ) {
        return;
    }

    $term_ids = [];

    if( isset( $response->Addresses ) && !empty( $response->Addresses ) ) {
        $District = remove_non_arabic_lang( $response->Addresses[0]->District );
        $City     = remove_non_arabic_lang( $response->Addresses[0]->City );
        $RegionName = remove_non_arabic_lang( $response->Addresses[0]->RegionName );
      }elseif( isset( $response->PostCode ) && !empty( $response->PostCode ) ){
        $District = remove_non_arabic_lang( $response->PostCode[0]->districtName );
        $City     = remove_non_arabic_lang( $response->PostCode[0]->cityName );
        $RegionName = remove_non_arabic_lang( $response->PostCode[0]->regionName );
      }
    
    // property state .
    if( !empty( $RegionName ) ) {

        $inserted_term =  wp_insert_term( $RegionName, 'property_state');
        if (is_wp_error($inserted_term)) {
            $new_term_id = $inserted_term->error_data['term_exists'];
        } else {
            $new_term_id = $inserted_term['term_id'];
        }
        // var_dump($new_term_id);wp_die();
        $houzez_state_meta['parent_country'] = 'saudi-arabia';
        $houzez_state_meta['provinceId'] = $new_term_id;
        $state_id = wp_set_object_terms( $prop_id, $new_term_id, 'property_state' );
        update_option('_houzez_property_state_'.$new_term_id, $houzez_state_meta);
        $term_ids['property_state'] = $new_term_id;
    }
    
    // property city .
    if( !empty( $City ) ) {  
        $terms = get_terms( array(
            'taxonomy' => 'property_state',
            'hide_empty' => false,
        ) );

        foreach($terms as $term){
            $state_Id = get_option( '_houzez_property_state_'.$term->term_id, true );
            if($term_ids['property_state'] == $state_Id['provinceId'] ){
                $houzez_city_meta['parent_state'] = $term->slug;
            }
        }

        $inserted_term =  wp_insert_term($City, 'property_city');
        if (is_wp_error($inserted_term)) {
            $new_term_id = $inserted_term->error_data['term_exists'];
        } else {
            $new_term_id = $inserted_term['term_id'];
        }

        $houzez_city_meta['cityId'] = $new_term_id;
        $city_id = wp_set_object_terms( $prop_id, $new_term_id, 'property_city' );
        update_option( '_houzez_property_city_'.$new_term_id, $houzez_city_meta );
        $term_ids['property_city'] = $new_term_id;
    }

    // property area .
    

    if( !empty( $District ) ) {
        $terms = get_terms( array(
            'taxonomy' => 'property_city',
            'hide_empty' => false,
        ) );

        foreach( $terms as $term ){
            $City_Id = get_option( '_houzez_property_city_'.$term->term_id, true );
            if( $term_ids['property_city'] == $City_Id['cityId'] ){
                $houzez_area_meta['parent_city'] = $term->slug;
            }
        }

        $inserted_term =  wp_insert_term($District, 'property_area');
        if (is_wp_error($inserted_term)) {
            $new_term_id = $inserted_term->error_data['term_exists'];
        } else {
            $new_term_id = $inserted_term['term_id'];
        }

        $houzez_area_meta['areaId'] = $new_term_id;
        $area_id = wp_set_object_terms( $prop_id, $new_term_id, 'property_area' );
        update_option( '_houzez_property_area_'.$new_term_id, $houzez_area_meta );
        $term_ids['property_erea'] = $new_term_id;
    }

    return $term_ids;
}

if( !function_exists('remove_non_arabic_lang') ){    
    /**
     * remove_non_arabic_lang
     *
     * @param  mixed $name
     * @return void
     */
    function remove_non_arabic_lang($name = ''){
        if( empty( $name )){
           return;
        }
        $remove_non_arabic_lang = str_replace('.', '', $name);
        $remove_non_arabic_lang = str_replace(',', '', $remove_non_arabic_lang);
        $remove_non_arabic_lang = preg_replace("/[a-zA-Z0-9]+/", '',$remove_non_arabic_lang);
        return $remove_non_arabic_lang;
      
      }
}

/**
 * houzez_user_role_by_user_id
 *
 * @param  mixed $user_id
 * @return void
 */
function ag_user_role_by_user_id($user_id) {

    $user = new WP_User($user_id);

    if( $user->ID == 0 ) {
        return 'houzez_guest';
    }
    $user_role = array_pop($user->roles);
    return $user_role;
}

function houzez_user_role_by_user_id($user_id) {

    $user = new WP_User($user_id);

    if( $user->ID == 0 ) {
        return 'houzez_guest';
    }
    $user_role = array_pop($user->roles);
    return $user_role;
}

/**
 * ag_get_token_cache_data
 *
 * @param  mixed $request
 * @return void
 */
function ag_get_token_cache_data( $request ){
    
    $response_data = [];
    $current_user = wp_get_current_user();
    $userID  = $current_user->ID;
    if( empty( $userID ) ){
        $response_data = [
           'success' => false,
           'error_code' => 209,
           'message' => __('هناك خطأ في التوكين'),
        ];
    }
    include_once ( AG_DIR . 'rest-api/api-membership-controller.php' );
    $response_data['profile_fields']   = AqarGateApi::profile_fields( $request );
    $response_data['user_property']    = AqarGateApi::get_user_properties( $request );
    $response_data['user_membership']  = AqarGateApi::membership( $request );
    $response_data['user_favorite_properties']  = AqarGateApi::favorite_properties( $request );

    $AqarGateApi = new AqarGateApi;
    global $wpdb, $current_user;
    $tabel = $wpdb->prefix . 'houzez_threads';
    $conversations = $wpdb->get_results(
        "
        SELECT * 
        FROM $tabel
        WHERE sender_id = $userID OR receiver_id = $userID 
        "
    );

    if( ! empty( $conversations ) ) {
        $conversation_id = [];
        
        foreach( (array)$conversations as $conversation ) {    

            $conversation_id[] = $AqarGateApi->get_all_conversation( $userID,  $conversation ) ;
        }

        // $response =  $conversation_id;
        $response_data['user_conversations']  = $conversation_id;
    }else{
        $response_data['user_conversations']  = [];
    }
    // $response_data['main_fields']   = $AqarGateApi->fields_steps( $request );
    $response_data['user_info']     = $AqarGateApi->author( $request );
    $response_data['user_invoices'] = $AqarGateApi->get_invoices( $request );
    if( houzez_is_agency() === true ) {
        $response_data['user_agents'] = $AqarGateApi->get_agents( $request );
    }else{
        $response_data['user_agents'] = []; 
    }

    $response['data'] = $response_data;

    return $response;
}

/**
 * property_overview_details
 *
 * @param  mixed $options
 * @return void
 */
function property_overview_details($options = 'crb_overview_fields', $prop_id = ''){
    $data = [];
    $propertyType  = wp_get_post_terms( $prop_id, 'property_type' );
    if( $propertyType && !is_wp_error($propertyType) ){
        $overview_data = carbon_get_term_meta($propertyType[0]->term_id, $options);
    }else{
        return $data;
    }
    
    if (!empty($overview_data)) {
        $overview_data = array_flip($overview_data);
    }

    $i = 0;
    
    $field_title = '';
    $custom_field_value ='';
    $icon_class = '';
    if ($overview_data) {
        unset($overview_data['placebo']);
        foreach ($overview_data as $key => $value) { $i ++;
            if(in_array($key, houzez_details_section_fields())) {

                if( $key === 'baths' ){
                    $bathrooms 	= houzez_get_listing_data('property_bathrooms');
                    $field_title = ($bathrooms > 1 ) ? houzez_option('spl_bathrooms', 'Bathrooms') : houzez_option('spl_bathroom', 'Bathroom');
                    $custom_field_value = $bathrooms;
                    $icon_class = houzez_option('fa_bath');
                }
                if( $key === 'beds' ){
                    $bedrooms 	= houzez_get_listing_data('property_bedrooms');
                    $field_title = ($bedrooms > 1 ) ? houzez_option('spl_bedrooms', 'Bedrooms') : houzez_option('spl_bedroom', 'Bedroom');
                    $custom_field_value = $bedrooms;
                    $icon_class = houzez_option('fa_bed');
                }
                if( $key === 'rooms' ){
                    $rooms = houzez_get_listing_data('property_rooms');
                    $field_title = ($rooms > 1 ) ? houzez_option('spl_rooms', 'Rooms') : houzez_option('spl_room', 'Room');
                    $custom_field_value = $rooms;
                    $icon_class = houzez_option('fa_room');
                }
                if( $key === 'area-size' ){
                    $field_title = houzez_option('spl_prop_size', 'Property Size');
                    $custom_field_value = houzez_get_listing_area_size( get_the_ID() );
                    $icon_class = houzez_option('fa_area-size');
                }
                if( $key === 'type' ){
                    $property_type = houzez_taxonomy_simple('property_type');
                    $field_title = houzez_option('spl_prop_type', 'Property Type');
                    $custom_field_value = $property_type;
                    $icon_class = '';
                }
                if( $key === 'land-area' ){
                    $land_area 	= houzez_get_listing_data('property_land');
                    $field_title = houzez_option('spl_land', 'Land Area');
                    $custom_field_value = houzez_property_land_area( 'after' );
                    $icon_class = houzez_option('fa_land-area');
                }
                if( $key === 'garage' ){
                    $garage = houzez_get_listing_data('property_garage');
                    $field_title = ($garage > 1 ) ? houzez_option('spl_garages', 'Garages') : houzez_option('spl_garage', 'Garage');
                    $custom_field_value = esc_attr($garage);
                    $icon_class = houzez_option('fa_garage');
                }
                if( $key === 'property-id' ){
                    $prop_id = houzez_get_listing_data('property_id');
                    $field_title = houzez_option('spl_prop_id', 'Property ID');
                    $custom_field_value = houzez_propperty_id_prefix(get_the_ID( ));
                    $icon_class = '';
                }
                if( $key === 'year' ){
                    $year_built = houzez_get_listing_data('property_year');
                    $field_title = houzez_option('spl_year_built', 'Year Built');
                    $custom_field_value = esc_attr( $year_built );
                    $icon_class = houzez_option('fa_year-built');
                }

                $data[] = [
                    'title' => esc_attr($field_title),
                    'value' => esc_attr($custom_field_value),
                    'icon_class' => $icon_class 
                ];

            } else {
                
                $meta_type = false;
                $custom_field_value = get_post_meta( get_the_ID(), 'fave_'.$key, $meta_type );
                $field_array = Houzez_Fields_Builder::get_field_by_slug($key);
                $field_title = houzez_wpml_translate_single_string($field_array['label']);
                $placeholder = houzez_wpml_translate_single_string($field_array['placeholder']);
                $field_name = $field_array['field_id'];
                $field_type = $field_array['type'];
                $data_value = ag_get_field_meta( $key,  get_the_ID( )  );
                if( ! empty( $data_value ) ) {
                    // $field_title = houzez_wpml_translate_single_string($value);
                    if( is_array($custom_field_value) ) {
                        $custom_field_value = houzez_array_to_comma($custom_field_value);
                    } else {
                        $custom_field_value = houzez_wpml_translate_single_string($custom_field_value);
                    }

                    $data[] = [
                        'title' => esc_attr($field_title),
                        'value' => $data_value,
                        'icon_class' => houzez_option('fa_'.$key) 
                    ];

                }

            }
        }
    }

    return $data;

}

/**
 * ag_free_membership_package
 *
 * @param  mixed $userID
 * @param  mixed $package_id
 * @return void
 */
function ag_free_membership_package( $userID, $package_id ) {

    $total_price = get_post_meta($package_id, 'fave_package_price', true);
    $currency = esc_html(houzez_option('currency_symbol'));
    $where_currency = esc_html(houzez_option('currency_position'));
    $wire_payment_instruction = houzez_option('direct_payment_instruction');
    $is_featured = 0;
    $is_upgrade = 0;
    $paypal_tax_id = '';
    $paymentMethod = '';
    $time = time();
    $date = date('Y-m-d H:i:s', $time);

    if ($total_price != 0) {
        if ($where_currency == 'before') {
            $total_price = $currency . ' ' . $total_price;
        } else {
            $total_price = $total_price . ' ' . $currency;
        }
    }

    // insert invoice
    $invoiceID = houzez_generate_invoice('package', 'one_time', $package_id, $date, $userID, $is_featured, $is_upgrade, $paypal_tax_id, $paymentMethod, 1);

    // houzez_save_user_packages_record($userID, $package_id);
    houzez_update_membership_package($userID, $package_id);
    update_post_meta( $invoiceID, 'invoice_payment_status', 1 );
    update_user_meta( $userID, 'user_had_free_package', 'yes' );

    $args = array(
        'payzaty_url' => false,
        'order_id'    => $invoiceID,
    );
    
    return $args;

}

function houzez_update_membership_package( $user_id, $package_id ) {

    // Get selected package listings
    $pack_listings            =   get_post_meta( $package_id, 'fave_package_listings', true );
    $pack_featured_listings   =   get_post_meta( $package_id, 'fave_package_featured_listings', true );
    $pack_unlimited_listings  =   get_post_meta( $package_id, 'fave_unlimited_listings', true );

    $user_current_posted_listings           =   houzez_get_user_num_posted_listings ( $user_id ); // get user current number of posted listings ( no expired )
    $user_current_posted_featured_listings  =   houzez_get_user_num_posted_featured_listings( $user_id ); // get user number of posted featured listings ( no expired )

    

    if( houzez_check_user_existing_package_status( $user_id, $package_id ) ) {
        $new_pack_listings           =  $pack_listings - $user_current_posted_listings;
        $new_pack_featured_listings  =  $pack_featured_listings -  $user_current_posted_featured_listings;
    } else {
        $new_pack_listings           =  $pack_listings;
        $new_pack_featured_listings  =  $pack_featured_listings;
    }

    if( $new_pack_listings < 0 ) {
        $new_pack_listings = 0;
    }

    if( $new_pack_featured_listings < 0 ||  empty($new_pack_featured_listings)) {
        $new_pack_featured_listings = 0;
    }

    if ( $pack_unlimited_listings == 1 ) {
        $new_pack_listings = -1 ;
    }


    

    update_user_meta( $user_id, 'package_listings', $new_pack_listings);
    update_user_meta( $user_id, 'package_featured_listings', $new_pack_featured_listings);

    // Use for user who submit property without having account and membership
    $user_submit_has_no_membership = get_the_author_meta( 'user_submit_has_no_membership', $user_id );
    if( !empty( $user_submit_has_no_membership ) ) {
        houzez_update_package_listings( $user_id );
        houzez_update_property_from_draft( $user_submit_has_no_membership ); // change property status from draft to pending or publish
        delete_user_meta( $user_id, 'user_submit_has_no_membership' );
    }


    $time = time();
    $date = date('Y-m-d H:i:s',$time);
    update_user_meta( $user_id, 'package_activation', $date );
    update_user_meta( $user_id, 'package_id', $package_id );
    update_user_meta( $user_id, 'houzez_membership_id', $package_id);

}

function houzez_required_field( $field ) {
    $required_fields = houzez_option('required_fields');
    
    if( array_key_exists($field, ( array ) $required_fields) ) {
        $field = $required_fields[$field];
        if( $field == 1 ) {
            return ' *';
        }
    }
    
    return '';
}



// function houzez_option( $id, $fallback = false, $param = false ) {
//     if ( isset( $_GET['fave_'.$id] ) ) {
//         if ( '-1' == $_GET['fave_'.$id] ) {
//             return false;
//         } else {
//             return esc_attr($_GET['fave_'.$id]);
//         }
//     } else {
//         global $houzez_options;
//         if ( $fallback == false ) $fallback = '';
//         $output = ( isset($houzez_options[$id]) && $houzez_options[$id] !== '' ) ? $houzez_options[$id] : $fallback;
//         if ( !empty($houzez_options[$id]) && $param ) {
//             $output = $houzez_options[$id][$param];
//         }
//     }
//     return $output;
// }


/* -------------------------------------------------------------------------- */
/*                       reassign property to new agent                       */
/* -------------------------------------------------------------------------- */
function aqar_reassign_property( $agent_cpt_id, $agent_id, $assign_to )
{

    global $wpdb;

	if ( ! is_numeric( $agent_id ) ) {
		return false;
	}

	$agent_id   = (int) $agent_id;
	$user = new WP_User( $agent_id );

	if ( ! $user->exists() ) {
		return false;
	}


    $post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d", $agent_id ) );  
    $wpdb->update( $wpdb->posts, array( 'post_author' => $assign_to ), array( 'post_author' => $agent_id ) );
    if ( ! empty( $post_ids ) ) {
        foreach ( $post_ids as $post_id ) {
            clean_post_cache( $post_id );
        }
    }
    $link_ids = $wpdb->get_col( $wpdb->prepare( "SELECT link_id FROM $wpdb->links WHERE link_owner = %d", $agent_id ) );
    $wpdb->update( $wpdb->links, array( 'link_owner' => $assign_to ), array( 'link_owner' => $agent_id ) );
    if ( ! empty( $link_ids ) ) {
        foreach ( $link_ids as $link_id ) {
            clean_bookmark_cache( $link_id );
        }
    }
    $houzez_threads  = $wpdb->prefix . 'houzez_threads';
    $thread_messages = $wpdb->prefix . 'houzez_thread_messages';
    $conversations_messages = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $houzez_threads WHERE sender_id = %d OR receiver_id = %d", $agent_id ) );
    $wpdb->update( 
        $houzez_threads, 
            array( 
            'sender_id'   => $assign_to,
            'receiver_id' => $assign_to, 
            ), 
            array( 
            'sender_id'   => $agent_id ,
            'receiver_id' => $agent_id ,
    ) );
    $conversations_messages = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $thread_messages WHERE created_by = %d", $agent_id ) );
    $wpdb->update( 
        $thread_messages, 
            array( 
            'created_by'   => $assign_to,
            ), 
            array( 
            'created_by'   => $agent_id ,
    ) );

}

/* -------------------------------------------------------------------------- */
/*                   ajax action for loading city choices                  */
/* -------------------------------------------------------------------------- */
add_action( 'wp_ajax_load_prop_field', 'load_prop_field');
add_action( 'wp_ajax_nopriv_load_prop_field', 'load_prop_field' );
function load_prop_field() { 
  $term = 0;
  if (isset($_POST['term'])) {
    $term_id = intval($_POST['term']);
  }

  if ( $term_id ) {
    $ag_fields = carbon_get_term_meta( $term_id, 'crb_available_fields' );
    // $ip = $_SERVER['REMOTE_ADDR'];
    // $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
    // $is_worldWide = false;
    // if( isset( $details->country ) && $details->country != 'SA' ){
    //     $is_worldWide = true;
    // }

    $fields_ids = [];
    if ( !empty( $ag_fields ) ) {
        foreach ( $ag_fields as $value ) {
                $fields_ids[] = $value;   
        } 
        $fields_builder = array_flip( $fields_ids );
    }
    unset($fields_builder['d986d988d8b9-d8a7d984d8a5d8b9d984d8a7d986-d8a7d984d8b1d8a6d98ad8b3d98a']);
    ob_start();
    ?>
        <div class="row">
			<?php
			$wide_field = array(
				'd987d984-d98ad988d8acd8af-d8a7d984d8b1d987d986-d8a3d988-d8a7d984d982d98ad8af-d8a7d984d8b0d98a-d98ad985d986d8b9-d8a7d988-d98ad8add8af',
				'd8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985',
				'd8a7d984d985d8b9d984d988d985d8a7d8aa-d8a7d984d8aad98a-d982d8af-d8aad8a4d8abd8b1-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8b3d988d8a7d8a1',
				'd8a7d984d986d8b2d8a7d8b9d8a7d8aa-d8a7d984d982d8a7d8a6d985d8a9-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1',
			);
			if ($fields_builder) {
				foreach ($fields_builder as $key => $value) {
					// prr($key);
					if(in_array($key, $wide_field)){
						$class = 'col-md-12';
					}else{
						$class = 'col-md-4';
					}

					if(in_array($key, houzez_details_section_fields())) { 

						if( $key == 'property-id' ) {

							if( $auto_property_id != 1 ) {
								echo '<div class="col-md-4 col-sm-12">';
									get_template_part('template-parts/dashboard/submit/form-fields/'.$key); 
								echo '</div>';
							}

						} else {
							echo '<div class="col-md-4 col-sm-12">';
								get_template_part('template-parts/dashboard/submit/form-fields/'.$key); 
							echo '</div>';
						}
						

					} else {

						echo '<div class="'. $class .' col-sm-12">';
						    do_action('get_custom_add_listing_field', $key);
							// houzez_get_custom_add_listing_field($key);
						echo '</div>';
					}
				}
			}
			?>
			
			
		</div><!-- row -->
    
    <?php
    
  }else{
     get_template_part('template-parts/dashboard/submit/ajax/details');
  }
  $html = ob_get_clean();
  echo $html;
  wp_die();
} // end public function ajax_load_location_field_choices


add_action( 'get_custom_add_listing_field', 'aqar_get_custom_add_listing_field' );
function aqar_get_custom_add_listing_field($key)
{
    if(class_exists('Houzez_Fields_Builder')) {

        $field_array = Houzez_Fields_Builder::get_field_by_slug($key);
        $field_title = houzez_wpml_translate_single_string($field_array['label']);
        $placeholder = houzez_wpml_translate_single_string($field_array['placeholder']);

        $field_name = $field_array['field_id'];
        $field_type = $field_array['type'];
        $field_options = $field_array['fvalues'];

        $disabled = 'disabled';
        $readonly = 'readonly';

        $selected = '';
        if (!houzez_edit_property()) {
            $selected = 'selected=selected';
        }

        $data_value = '';
        if (houzez_edit_property()) {
            global $prop_meta_data;
            $data_value = isset( $prop_meta_data[ 'fave_'.$key ] ) ? ( ( 'checkbox_list' == $field_type || 'radio' == $field_type ) || 'multiselect' == $field_type ? $prop_meta_data[ 'fave_'.$key ] : $prop_meta_data[ 'fave_'.$key ][0] ) : '';
        }


        if($field_type == 'select' ) { ?>

            <div class="form-group">
                <label for="<?php echo esc_attr($field_name); ?>">
                    <?php echo $field_title.houzez_required_field($field_name); ?>
                </label>

                <select name="<?php echo esc_attr($field_name);?>" data-size="5" class="selectpicker <?php houzez_required_field_2($field_name); ?> form-control bs-select-hidden" title="<?php echo esc_attr($placeholder); ?>" data-live-search="false" <?php echo $disabled; ?>>
                    
                    <option <?php echo esc_attr($selected); ?> value=""><?php esc_html_e('None', 'houzez'); ?> </option>
                    <?php
                    $options = unserialize($field_options);
                    
                    foreach ($options as $key => $val) {
                        $val = houzez_wpml_translate_single_string($val);
                        
                        $selected_val = houzez_get_field_meta($field_name);

                        echo '<option '.selected($selected_val, $key, false).' value="'.esc_attr($key).'">'.esc_attr($val).'</option>';
                    }
                    ?>

                </select><!-- selectpicker -->
            </div>

        <?php
        } else if($field_type == 'multiselect' ) { ?>

            <div class="form-group">
                <label for="<?php echo esc_attr($field_name); ?>">
                    <?php echo $field_title.houzez_required_field($field_name); ?>
                </label>

                <select name="<?php echo esc_attr($field_name).'[]'; ?>" data-size="5" data-actions-box="true" class="selectpicker <?php houzez_required_field_2($field_name); ?> form-control bs-select-hidden" title="<?php echo esc_attr($placeholder); ?>" data-live-search="false" data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-count-selected-text="{0}" multiple <?php echo $disabled; ?>>
                    
                    <?php
                    $options = unserialize($field_options);
                    
                    foreach ($options as $key => $val) {
                        $val = houzez_wpml_translate_single_string($val);
                        $selected = ( houzez_edit_property() && ! empty( $data_value ) && in_array( $key, $data_value ) ) ? 'selected' : '';

                        echo '<option '.esc_attr($selected).' value="'.esc_attr($key).'">'.esc_attr($val).'</option>';
                    }
                    ?>

                </select><!-- selectpicker -->
            </div>

        <?php
        } else if( $field_type == 'checkbox_list' ) { ?>

            <div class="form-group">
                <label for="<?php echo esc_attr($field_name); ?>">
                    <?php echo $field_title.houzez_required_field($field_name); ?>
                </label>
                <div class="features-list houzez-custom-field">
                    <?php
                    $options    = unserialize( $field_options );
                    $options    = explode( ',', $options );
                    $options    = array_filter( array_map( 'trim', $options ) );
                    $checkboxes = array_combine( $options, $options );

                    foreach ($checkboxes as $checkbox) {

                        $checked = ( houzez_edit_property() && ! empty( $data_value ) && in_array( $checkbox, $data_value ) ) ? 'checked' : '';
                        $checkbox_title = houzez_wpml_translate_single_string($checkbox);
                        echo '<label class="control control--checkbox">';
                            echo '<input type="checkbox" '.esc_attr($checked).' name="'.esc_attr($field_name).'[]" value="'.esc_attr($checkbox).'">'.esc_attr($checkbox_title);
                            echo '<span class="control__indicator"></span>';
                        echo '</label>';

                    }
                    ?>
                </div><!-- features-list -->
            </div>

        <?php
        } else if( $field_type == 'radio' ) { ?>

            <div class="form-group">
                <label for="<?php echo esc_attr($field_name); ?>">
                    <?php echo $field_title.houzez_required_field($field_name); ?>
                </label>
                <div class="features-list houzez-custom-field">
                    <?php
                    $options    = unserialize( $field_options );
                    $options    = explode( ',', $options );
                    $options    = array_filter( array_map( 'trim', $options ) );
                    $radios     = array_combine( $options, $options );

                    echo '<label class="control control--radio">';
                        echo '<input type="radio" name="'.esc_attr($field_name).'" value="">'.esc_html__('None', 'houzez');
                        echo '<span class="control__indicator"></span>';
                    echo '</label>';

                    foreach ($radios as $radio) {

                        $radio_checked = ( houzez_edit_property() && ! empty( $data_value ) && in_array( $radio, $data_value ) ) ? 'checked' : '';

                        $radio_title = houzez_wpml_translate_single_string($radio);
                        echo '<label class="control control--radio">';
                            echo '<input type="radio" '.esc_attr($radio_checked).' name="'.esc_attr($field_name).'" value="'.esc_attr($radio).'">'.esc_attr($radio_title);
                            echo '<span class="control__indicator"></span>';
                        echo '</label>';

                    }
                    ?>
                </div><!-- features-list -->
            </div>

        <?php
        } else if( $field_type == 'number' ) { ?>

            <div class="form-group">
                <label for="<?php echo esc_attr($field_name); ?>">
                    <?php echo $field_title.houzez_required_field($field_name); ?>
                </label>
                <input name="<?php echo esc_attr($field_name);?>" <?php houzez_required_field_2($field_name); ?> type="number" min="1" class="form-control" value="<?php
                if (houzez_edit_property()) {
                    houzez_field_meta($field_name);
                } ?>" placeholder="<?php echo esc_attr($placeholder);?>" <?php echo $readonly; ?>>
            </div>

        <?php
        } else if( $field_type == 'textarea' ) { ?>

            <div class="form-group">
                <label for="<?php echo esc_attr($field_name); ?>">
                    <?php echo $field_title.houzez_required_field($field_name); ?>
                </label>
                <textarea class="form-control" name="<?php echo esc_attr($field_name);?>" placeholder="<?php echo esc_attr($placeholder);?>" <?php houzez_required_field_2($field_name); ?> <?php echo $readonly; ?>><?php
                if (houzez_edit_property()) {
                    houzez_field_meta($field_name);
                } ?></textarea>
            </div>

        <?php
        } else if( $field_type == 'url' ) { ?>

            <div class="form-group">
                <label for="<?php echo esc_attr($field_name); ?>">
                    <?php echo $field_title.houzez_required_field($field_name); ?>
                </label>

                <input name="<?php echo esc_attr($field_name);?>" <?php houzez_required_field_2($field_name); ?> type="url" class="form-control" value="<?php
                if (houzez_edit_property()) {
                    houzez_field_meta($field_name);
                } ?>" placeholder="<?php echo esc_attr($placeholder);?>">
            </div>

        <?php
        } else { ?>

            <div class="form-group">
                <label for="<?php echo esc_attr($field_name); ?>">
                    <?php echo $field_title.houzez_required_field($field_name); ?>
                </label>

                <input name="<?php echo esc_attr($field_name);?>" <?php houzez_required_field_2($field_name); ?> type="text" class="form-control" value="<?php
                if (houzez_edit_property()) {
                    houzez_field_meta($field_name);
                } ?>" placeholder="<?php echo esc_attr($placeholder);?>" <?php echo $readonly; ?>>
            </div>

        <?php
        } 

    }
}
/* -------------------------------------------------------------------------- */
/*                                    debug                                   */
/* -------------------------------------------------------------------------- */
/****************************************** Debug Helpers ******************************************/
if( ! function_exists('get_line_info') ){
	function get_line_info(){
		$excuting_line = debug_backtrace()[1]['line'];

		$excuting_file = debug_backtrace()[1]['file'];
		$excuting_file = explode("\\" ,$excuting_file);
		
		$count = count( $excuting_file);


		$excuting_folder 	= @$excuting_file[( $count-2)];		
		$excuting_file		= $excuting_file[( $count-1)];
		$excuting_file		= explode('.',$excuting_file)[0];
		return "$excuting_folder/$excuting_file@$excuting_line";
	}
}

if( ! function_exists('echo_line') ){
	function echo_line( $echo = true){
		$line = get_line_info();

		if( $echo){
			echo "<h2>$line</h2>";
		}else {
			return $line ;
		}
	}
}

if( ! function_exists('prr') ){
	function prr( $element, $title = '', $echo = true ){
		$title = is_string( $title) && strlen( $title) ? $title.' ' : '';
		$title = "<h3 style='position: relative;
		background: #2271b1;
		margin: 0;
		padding: 20px;
		border: 1px solid #2271b1;
		color: #fff;
		max-width: 95%;'>$title(".get_line_info().")</h3>";

		if( $echo ){
			echo "<div class='d-debug' style='margin: 20px 0;'>$title<pre style='
			background: #1d2327;
			max-width: 95%;
			padding: 20px;
			margin: 0;
			border: 1px solid #2271b1;
			color: #f1f1f1;'>";
			print_r( $element );
			echo "</pre></div>";
		}else {
			return "$title<pre>".print_r( $element)."</pre>";
		}
	}
}

if( ! function_exists('csv_to_array') ){    
    /**
     * csv_to_array
     *
     * @param  mixed $file
     * @return void
     */
    function csv_to_array($file) {

        if (($handle = fopen($file, 'r')) === false) {
            die('Error opening file');
        }
        
        $headers = fgetcsv($handle, 10000, ',');
        $headers = preg_replace('/ ^[\pZ\p{Cc}\x{feff}]+|[\pZ\p{Cc}\x{feff}]+$/ux', '', $headers);
        $_data = array();
        
        while ($row = fgetcsv($handle, 10000, ',')) {
            $row = preg_replace('/ ^[\pZ\p{Cc}\x{feff}]+|[\pZ\p{Cc}\x{feff}]+$/ux', '', $row);
            if (count($row) == count($headers)) {
                $_data[] = array_combine($headers, $row);
            }else{
                $_data[] = array_merge($headers, $row);
            }
        }
        fclose($handle);
    
        return $_data;
      
      }
}


function edit_prop_input()
{

    if ( houzez_edit_property() && get_option( '_aq_show_api' ) == 'yes' ) {
        global $property_data;
        $property_type             = wp_get_post_terms($property_data->ID, 'property_type', array("fields" => "ids"));
        $property_status           = wp_get_post_terms($property_data->ID, 'property_status', array("fields" => "ids"));
        $property_label            = wp_get_post_terms($property_data->ID, 'property_label', array("fields" => "ids"));
        $city_id                   = wp_get_post_terms( $property_data->ID,'property_city', array("fields" => "ids") );
        $property_area             = wp_get_post_terms( $property_data->ID,'property_area', array("fields" => "ids") );
        $property_state            = wp_get_post_terms( $property_data->ID,'property_state', array("fields" => "ids") );
        $prop_price                = get_post_meta( $property_data->ID, 'fave_property_price', true );
        $postal_code               = get_post_meta( $property_data->ID, 'fave_property_zip', true );
        $prop_size                 = get_post_meta( $property_data->ID, 'fave_property_size', true );
        $meta_1                    = get_post_meta( $property_data->ID, 'fave_d8add8afd988d8af-d988d8a3d8b7d988d8a7d984-d8a7d984d8b9d982d8a7d8b1', true );
        $meta_2                    = get_post_meta( $property_data->ID, 'fave_d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9', true );
        $meta_3                    = get_post_meta( $property_data->ID, 'fave_d8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985', true );
        $meta_4                    = get_post_meta( $property_data->ID, 'fave_d8b1d982d985-d8a7d984d985d8aed8b7d8b7', true );       
        $meta_5                    = get_post_meta( $property_data->ID, 'fave_d8aed8afd985d8a7d8aa-d8a7d984d8b9d982d8a7d8b1', true ); 
        $meta_6                    = get_post_meta( $property_data->ID, 'fave_d988d8a7d8acd987d8a9-d8a7d984d8b9d982d8a7d8b1', true ); 
        $meta_7                    = get_post_meta( $property_data->ID, 'fave_d982d986d988d8a7d8aa-d8a7d984d8a5d8b9d984d8a7d986', true ); 
        
            
        $fave_property_map_address = get_post_meta( $property_data->ID, 'fave_property_map_address', true );
        $fave_property_address     = get_post_meta( $property_data->ID, 'fave_property_address', true );
        $lat                       = get_post_meta( $property_data->ID, 'houzez_geolocation_lat', true );
        $lng                       = get_post_meta( $property_data->ID, 'houzez_geolocation_long', true );
        $prop_land_area             = get_post_meta( $property_data->ID, 'fave_property_land', true );
   ?>
    
    <input type="hidden" name="prop_type[]" id="prop_type" value="<?php echo $property_type[0]; ?>">
    <input type="hidden" name="prop_status[]" value="<?php echo $property_status[0]; ?>">
    <input type="hidden" name="prop_labels[]" value="<?php echo ( isset($property_label[0]) ? $property_label[0] : '' ); ?>">
    <input type="hidden" name="prop_price" value="<?php echo $prop_price; ?>">
    <input type="hidden" name="postal_code" value="<?php echo $postal_code; ?>">

    <input type="hidden" name="prop_size" value="<?php echo $prop_size; ?>">
    <input type="hidden" name="d8add8afd988d8af-d988d8a3d8b7d988d8a7d984-d8a7d984d8b9d982d8a7d8b1" value="<?php echo $meta_1; ?>">
    <input type="hidden" name="d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9" value="<?php echo $meta_2; ?>">
    <input type="hidden" name="d8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985" value="<?php echo $meta_3; ?>">
    <input type="hidden" name="property_map_address" value="<?php echo $fave_property_address; ?>">
    <input type="hidden" name="lat" value="<?php echo $lat; ?>">
    <input type="hidden" name="lng" value="<?php echo $lng; ?>">
    <input type="hidden" name="prop_land_area" value="<?php echo $prop_land_area; ?>">
    <input type="hidden" name="d8b1d982d985-d8a7d984d985d8aed8b7d8b7" value="<?php echo $meta_4; ?>">
    <input type="hidden" name="d8aed8afd985d8a7d8aa-d8a7d984d8b9d982d8a7d8b1[]" value="<?php echo $meta_5; ?>">
    <input type="hidden" name="d988d8a7d8acd987d8a9-d8a7d984d8b9d982d8a7d8b1" value="<?php echo $meta_6; ?>">
    <input type="hidden" name="d982d986d988d8a7d8aa-d8a7d984d8a5d8b9d984d8a7d986[]" value="<?php echo $meta_7; ?>">
   <?php 
    } 
}

function aqar_is_verify_msg($userID)
{
    if( $userID > 0 ) {
        $is_verify = get_user_meta( $userID, 'aqar_is_verify_user', true );
        $dash_profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');

        if( ! $is_verify ) { ?>
        <div id="errors-messages" class="validate-errors alert alert-danger" role="alert">
            <strong id="messages">
            غير مسموح لك الدخول الي هذه الصفحه قبل ملأ الداتا <a href="<?php echo $dash_profile_link ; ?>"> الملف الشخصي </a>
            </strong> 
        </div>
        <?php }
        return;
    }
}

function aqar_is_verify($userID)
{
    if( $userID > 0 ) {
        $is_verify = get_user_meta( $userID, 'aqar_is_verify_user', true );
        if( $is_verify ) { 
            return true;
        }
        return false;
    }
}


/*-----------------------------------------------------------------------------------*/
/*   Upload picture for user profile using ajax
/*-----------------------------------------------------------------------------------*/
if( !function_exists( 'aqar_user_picture_upload' ) ) {
    function aqar_user_picture_upload( ) {

        $user_id = $_REQUEST['user_id'];
        // $verify_nonce = $_REQUEST['verify_nonce'];
        // if ( ! wp_verify_nonce( $verify_nonce, 'aqar_upload_nonce' ) ) {
        //     echo json_encode( array( 'success' => false , 'reason' => 'Invalid request' ) );
        //     die;
        // }

        $aqar_user_image = $_FILES['aqar_file_data_name'];
        $aqar_wp_handle_upload = wp_handle_upload( $aqar_user_image, array( 'test_form' => false ) );

        if ( isset( $aqar_wp_handle_upload['file'] ) ) {
            $file_name  = basename( $aqar_user_image['name'] );
            $file_type  = wp_check_filetype( $aqar_wp_handle_upload['file'] );

            $uploaded_image_details = array(
                'guid'           => $aqar_wp_handle_upload['url'],
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $profile_attach_id      =   wp_insert_attachment( $uploaded_image_details, $aqar_wp_handle_upload['file'] );
            $profile_attach_data    =   wp_generate_attachment_metadata( $profile_attach_id, $aqar_wp_handle_upload['file'] );
            wp_update_attachment_metadata( $profile_attach_id, $profile_attach_data );

            $thumbnail_url = wp_get_attachment_image_src( $profile_attach_id, 'large' );
            aqar_save_user_photo($user_id, $profile_attach_id, $thumbnail_url);

            echo json_encode( array(
                'success'   => true,
                'url' => $thumbnail_url[0],
                'attachment_id'    => $profile_attach_id
            ));
            die;

        } else {
            echo json_encode( array( 'success' => false, 'reason' => 'Profile Photo upload failed!' ) );
            die;
        }

    }
}
add_action( 'wp_ajax_aqar_user_picture_upload', 'aqar_user_picture_upload' );    // only for logged in user

if( !function_exists('aqar_save_user_photo')) {
    function aqar_save_user_photo($user_id, $pic_id, $thumbnail_url) {
        
        update_user_meta( $user_id, 'fave_author_logo_id', $pic_id );
        update_user_meta( $user_id, 'fave_author_custom_logo', $thumbnail_url[0] );

        $user_agent_id = get_the_author_meta('fave_author_agent_id', $user_id);
        $user_agency_id = get_the_author_meta('fave_author_agency_id', $user_id);
        
        if( !empty($user_agent_id) && aqar_is_agent() ) {
            update_post_meta( $user_agent_id, '_thumbnail_id', $pic_id );
        }
        
        if( !empty($user_agency_id) && aqar_is_agency() ) {
            update_post_meta( $user_agency_id, '_thumbnail_id', $pic_id );
        }

    }
}

function ag_urlsafeB64Decode($input)
{
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $input .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}
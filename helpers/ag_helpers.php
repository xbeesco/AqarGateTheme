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
    $hide_empty = true;
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
                $output.= '<option data-ref="'.esc_attr($category->slug).'" '.$data_attr.' '.$data_subtext.' value="' . esc_attr($category->slug) . '" selected="selected">'. esc_attr(aqarRemoveNumberFromString($category->name)) . '</option>';
            } else {
                $output.= '<option data-ref="'.esc_attr($category->slug).'" '.$data_attr.' '.$data_subtext.' value="' . esc_attr($category->slug) . '">' . esc_attr(aqarRemoveNumberFromString($category->name)) . '</option>';
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
                        $output.= '<option data-ref="'.esc_attr($subcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subcategory->slug) . '" selected="selected"> - '. esc_attr(aqarRemoveNumberFromString($subcategory->name)) . '</option>';
                    } else {
                        $output.= '<option data-ref="'.esc_attr($subcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subcategory->slug) . '"> - ' . esc_attr(aqarRemoveNumberFromString($subcategory->name)) . '</option>';
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
                                $output.= '<option data-ref="'.esc_attr($subsubcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subsubcategory->slug) . '" selected="selected"> - '. esc_attr(aqarRemoveNumberFromString($subcategory->name)) . '</option>';
                            } else {
                                $output.= '<option data-ref="'.esc_attr($subsubcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subsubcategory->slug) . '"> -- ' . esc_attr(aqarRemoveNumberFromString($subcategory->name)) . '</option>';
                            }
                        }
                    }
                }
            }
        }
    }
    echo $output;

}

function aqarRemoveNumberFromString($string) {
    // Use regex to remove the number at the beginning of the string
    $result = preg_replace('/-\d+/', '', $string);
    return $result;
}

function get_houzez_listing_expire( $post_id ) {
    global $post;
    if(empty($post->ID)){
        $postID = $post_id;
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
    elseif( $user_role == "houzez_seller" ) { $Advertiser_character =  "مفوض" ;}
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
                'id' => "{$houzez_prefix}view_prop_req",
                'name' => esc_html__( "الاطلاع علي الطلبات", 'houzez' ),
                'type' => 'checkbox',
                'desc' => esc_html__('الاطلاع علي الطلبات', 'houzez'),
                'std' => "",
                'columns' => 6,
            ),
            array(
                'id' => "{$houzez_prefix}view_prop_req_info",
                'name' => esc_html__( "التواصل مع العملاء", 'houzez' ),
                'type' => 'checkbox',
                'desc' => esc_html__('التواصل مع العملاء', 'houzez'),
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


/**
 * Summary of aqar_can_edit
 * @return bool
 */
function aqar_can_edit($propID = ''){

    if( isset($_GET) && !empty($_GET['edit_property']) ){
        $propID = $_GET['edit_property'];
    }
 
    if( get_post_meta( $propID , 'adverst_can_edit', true ) ) {
        return true;
    }

    return false;
}

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
        $propID = '';
        if( isset($_GET) && !empty($_GET['edit_property']) ){
            $propID = $_GET['edit_property'];
        }
        $disabled = 'disabled';
        $readonly = 'readonly';
        
        if( aqar_can_edit() ) {
            $disabled = '';
            $readonly = '';
        }

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

                <select name="<?php echo esc_attr($field_name).'[]'; ?>" data-size="5" data-actions-box="true" class="selectpicker <?php houzez_required_field_2($field_name); ?> form-control bs-select-hidden" title="<?php echo esc_attr($placeholder); ?>" data-live-search="false" data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-count-selected-text="{0}" multiple <?php //echo $disabled; ?>>
                    
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
/*                            Debug Helpers                                   */
/* -------------------------------------------------------------------------- */

if( ! function_exists('get_line_info') ){
	function get_line_info(){
		$excuting_line   = debug_backtrace()[1]['line'];
		$excuting_file   = debug_backtrace()[1]['file'];
		$excuting_file   = explode("\\" ,$excuting_file);
		$count           = count( $excuting_file);
		$excuting_folder = @$excuting_file[( $count-2)];
		$excuting_file   = $excuting_file[( $count-1)];
		$excuting_file   = explode('.',$excuting_file)[0];

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
			echo "<div dir='ltr' class='d-debug' style='margin: 20px 0;text-align: left;'>$title<pre style='
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
     * @return void|array
     */
    function csv_to_array($file) {

        if (($handle = fopen($file, 'r')) === false) {
            die('Error opening file');
        }
        
        $headers = fgetcsv($handle, 10000, ',');
        $headers = preg_replace('/ ^[\pZ\p{Cc}\x{feff}]+|[\pZ\p{Cc}\x{feff}]+$/ux', '', $headers);
        $_data = [];
        
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
        $prop_land_area            = get_post_meta( $property_data->ID, 'fave_property_land', true );
   ?>
    
    <input type="hidden" name="prop_type[]" id="prop_type" value="<?php echo $property_type[0]; ?>">
    <input type="hidden" name="prop_status[]" value="<?php echo $property_status[0]; ?>">
    <input type="hidden" name="prop_labels[]" value="<?php echo ( isset($property_label[0]) ? $property_label[0] : '' ); ?>">
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
            <style>
                #submit_property_form, #save_as_draft, .dashboard-header-wrap {
                        display: none;
                }
            </style>
        <div id="errors-messages" class="validate-errors alert alert-danger" role="alert">
            <strong id="messages">
            غير مسموح لك الدخول الي هذه الصفحه قبل ملأ الداتا <a href="<?php echo $dash_profile_link ; ?>"> الملف الشخصي </a>
            </strong> 
        </div>
        <?php }  
        if ( aq_is_black_list() ) { 
            $error = !empty(carbon_get_theme_option( 'can_add_property_content' )) ? carbon_get_theme_option( 'can_add_property_content' ) : 'غير مسموح لك الدخول الي هذه الصفحه';
            ?>
            <style>
                #submit_property_form, #save_as_draft, .dashboard-header-wrap {
                        display: none;
                }
            </style>
            <div id="errors-messages" class="validate-errors alert alert-danger" role="alert">
            <strong id="messages"><?php echo $error ; ?></strong> 
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

/* ------------------------------- empty title ------------------------------ */
add_filter('pre_post_title', 'aqargate_mask_empty');
function aqargate_mask_empty($value)
{
    if ( empty($value) ) {
        return ' ';
    }
    return $value;
}
/* -------------- Add Authors to Any WordPress Custom Post Type ------------- */
function aqar_add_author_support_to_posts() {
    add_post_type_support( 'houzez_agency', 'author' ); 
    add_post_type_support( 'houzez_agent', 'author' ); 

 }
 add_action( 'init', 'aqar_add_author_support_to_posts' );
 /* --------------------------- aqar_is_verify_user -------------------------- */
 function aqar_is_verify_user()
 {
    global $wpdb;
    $userID          = get_current_user_id();
    $nafath_callback = $wpdb->prefix."nafath_callback";
    $user_meta       = $wpdb->prefix."usermeta";

    $users_ids = $wpdb->get_results( 
        "SELECT user_id
         FROM {$user_meta}
         INNER JOIN {$nafath_callback} ON $nafath_callback.cardId = {$user_meta}.meta_value
         WHERE meta_key = 'aqar_author_id_number'
         AND {$nafath_callback}.status = 'COMPLETED';"
    );
    $userIDs = [];
    if( count($users_ids) > 0 ) {
        foreach ( $users_ids as  $id ) {
            $userIDs[] = $id->user_id;
        }
    }

    if( in_array($userID, $userIDs) ) {
        return true;
    }
    
    return false ;
 }
 /* ---------------------------- validateDateTime ---------------------------- */
 function validateDate($date, $format = 'Y-m-d')
 {
     $d = DateTime::createFromFormat($format, $date);
     // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
     return $d && $d->format($format) === $date;
 }

function propertyType_select()
{
  ?>
<select name="propertyType" id="propertyType" class="selectpicker labels-select-picker form-control" data-size="5" data-selected-text-format="count > 2" title="يرجى الاختيار" data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="true" data-actions-box="true" data-select-all-text="أختر الكل" data-deselect-all-text="إلغاء الاختيار" data-count-selected-text="{0} أنوع الاستخدام" required>
    <option value="Land">أرض</option>
	<option value="Floor">دور</option>
	<option value="Apartment">شقة</option>
	<option value="Villa">فيلا</option>
	<option value="Studio">شقَّة صغيرة (استوديو)</option>
	<option value="Room">غرفة</option>
	<option value="RestHouse">استراحة</option>
	<option value="Compound">مجمع</option>
	<option value="Tower">برج</option>
	<option value="Exhibition">معرض</option>
	<option value="Office">مكتب</option>
	<option value="Warehouses">مستودع</option>
	<option value="Booth">كشك</option>
	<option value="Cinema">سينما</option>
	<option value="Hotel">فندق</option>
	<option value="CarParking">مواقف سيارات</option>
	<option value="RepairShop">ورشة</option>
	<option value="Teller">صراف</option>
	<option value="Factory">مصنع</option>
	<option value="School">مدرسة</option>
	<option value="HospitalOrHealthCenter">مستشفى، مركز صحي</option>
	<option value="ElectricityStation">محطة كهرباء</option>
	<option value="TelecomTower">برج اتصالات</option>
	<option value="Station">محطة</option>
	<option value="Farm">مزرعة</option>
	<option value="Building">عمارة</option>
</select>
  <?php 
} 

function advertisementType_select()
{
 ?>
 <select name="advertisementType" data-size="5" id="advertisementType" class="selectpicker form-control" title="يرجى الاختيار" data-selected-text-format="count > 2" data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="true" data-actions-box="true" data-select-all-text="أختر الكل" data-deselect-all-text="إلغاء الاختيار" data-count-selected-text="{0} الحالات" required>
	<option value="">يرجى الاختيار</option>
	<option value="Rent"> إيجار</option>
	<option value="Sell"> بيع</option>
</select>
 <?php     
}

function propertyUsages_select()
{
    ?>
    <select name="propertyUsages[]" id="propertyUsages" class="selectpicker labels-select-picker form-control" data-selected-text-format="count > 2" title="يرجى الاختيار" data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="false" data-actions-box="true" data-select-all-text="أختر الكل" data-deselect-all-text="إلغاء الاختيار" data-count-selected-text="{0} أنوع الاستخدام" required>
        <option value="Agricultural"> زراعي</option>
        <option value="Residential"> سكني</option>
        <option value="Commercial"> تجاري</option>
        <option value="Industrial"> صناعي</option>
        <option value="Healthy"> صحي</option>	
        <option value="Educational"> تعليمي</option>	
    </select>
    <?php 
    
}

function theAdThrough_select() {
    $user_id = get_current_user_id();

    if( houzez_is_agency() ) { ?>
        <select name="theAdThrough" id="theAdThrough" class="selectpicker labels-select-picker form-control" data-size="5" data-selected-text-format="count > 2" title="يرجى الاختيار" data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="true" data-actions-box="true" data-select-all-text="أختر الكل" data-deselect-all-text="إلغاء الاختيار" data-count-selected-text="{0} أنوع الاستخدام" required>
            <option value="OwnerOffice">مالك منشأة</option>
            <option value="BrokerOffice">وسيط منشأة</option>
        </select>
    <?php } else {?>
        <select name="theAdThrough" id="theAdThrough" class="selectpicker labels-select-picker form-control" data-size="5" data-selected-text-format="count > 2" title="يرجى الاختيار" data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="true" data-actions-box="true" data-select-all-text="أختر الكل" data-deselect-all-text="إلغاء الاختيار" data-count-selected-text="{0} أنوع الاستخدام" required>
            <option value="OwnerAgent">وكيل المالك </option>
            <option value="OwnerIndividual">مالك فرد </option>
            <option value="BrokerIndividual">وسيط فرد</option>
        </select>

    <?php }
}
/* -------------------------------------------------------------------------- */
/*                                 for testing                                */
/* -------------------------------------------------------------------------- */

function aq_register_as_agency( $username, $email, $user_id, $phone_number = null ) {
    // Create post object
    $args = array(
        'post_title'  => $username,
        'post_type'   => 'houzez_agency',
        'post_status' => 'publish',
        'post_author' => $user_id,
        
    );

    // Insert the post into the database
    $post_id =  wp_insert_post( $args );
    update_post_meta( $post_id, 'houzez_user_meta_id', $user_id);  // used when agent custom post type updated
    update_user_meta( $user_id, 'fave_author_agency_id', $post_id);
    update_post_meta( $post_id, 'fave_agency_email', $email) ;
    update_post_meta( $post_id, 'fave_agency_phone', $phone_number);

    if( houzez_option('realtor_visible', 0) ) {
        update_post_meta( $post_id, 'fave_agency_visible', 1);
    }
}

/* -------------------------------------------------------------------------- */
/*                         aq_delete_account_function                         */
/* -------------------------------------------------------------------------- */
function aq_delete_account_function() {
    check_ajax_referer('delete_account_nonce', 'security');

    $user_email = sanitize_email($_POST['user_email']);
    $user_password = sanitize_text_field($_POST['user_password']);

    // Validate user email and password
    $user = wp_authenticate($user_email, $user_password);

    if ( is_wp_error( $user ) ) {
        // Display the error message
        echo 'خطأ : ' . esc_html($user->get_error_message());
    } else {
        // Add code here to delete the account
        wp_delete_user($user->ID);
        echo 'تم حذف الحساب بنجاح';
    }
    die();
}
add_action('wp_ajax_aq_delete_account_function', 'aq_delete_account_function');
add_action('wp_ajax_nopriv_aq_delete_account_function', 'aq_delete_account_function' );

//
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
    function is_woocommerce_activated() {
        return class_exists( 'woocommerce' );
    }
}
// functions.php or your custom plugin file
add_action("wp_ajax_handle_contract_submission", "handle_contract_submission");
add_action("wp_ajax_nopriv_handle_contract_submission", "handle_contract_submission");

function handle_contract_submission() {
    ini_set( 'display_errors', 1 );
    global $current_user;
    $userID = get_current_user_id();
    $first_name             =   get_the_author_meta( 'first_name' , $userID );
    $last_name              =   get_the_author_meta( 'last_name' , $userID );
    $user_email             =   get_the_author_meta( 'user_email' , $userID );
    $user_mobile            =   get_the_author_meta( 'fave_author_mobile' , $userID );

    $address = array(
        'first_name' => $first_name ,
        'last_name'  => $last_name,
        'email'      => $user_email ,
        'phone'      => $user_mobile,
        'address_1'  => 'address_1',
        'address_2'  => '',
        'city'       => '',
        'state'      => '',
        'postcode'   => '11461',
        'country'    => 'SA'
    );

    // Retrieve form data
    $form_data = isset($_POST['form_data']) ? urldecode($_POST['form_data']) : '';
    mb_parse_str($form_data, $unserialized_data);

    // Translate form data
    $translate = [
        "owner-id" => "رقم الهوية",
        "owner-birth" => "تاريخ الميلاد",
        "id-type" => [
            "national-id" => "هوية وطنية",
            "residence-permit" => "اقامة",
            "commercial-record" => "سجل تجاري",
        ],
        "owner-mobile" => "رقم الموبايل",
        "property-document" => [
            "net-deed" => "صك الكتروني",
            "paper-deed" => "صك ورقي",
            "property-deed" => "صك عقار مع حصر ورثة",
            "inheritance-certificate" => "حجة استحكام",
        ],
        "document-number" => "رقم وثيقة الملكية",
        "property-type" => "نوع العقار",
        "property-area" => "مساحة العقار",
        "city" => "المدينة",
        "neighborhood" => "الحي",
        "parcel-number" => "رقم القطعة",
        "price" => "السعر المطلوب",
        'buildingNumber' => 'رقم المبنى',
        "street" => "الشارع",
        "postalCode" => "الرمز البريدي",
        "additionalNumber" => "رقم اضافي",
    ];

    $translated_data = [];
    foreach ($unserialized_data as $field_name => $field_value) {
        if (isset($translate[$field_name])) {
            if (is_array($translate[$field_name])) {
                // If the translation value is an array, get the specific translation for the field value
                $translated_value = isset($translate[$field_name][$field_value]) ? $translate[$field_name][$field_value] : $field_value;
                $translated_data[$field_name] = $translated_value;
            } else {
                // If the translation value is a string, use it directly
                $translated_value = $translate[$field_name];
                $translated_data[$translated_value] = $field_value;
            }
        } else {
            $translated_data[$field_name] = $field_value;
        }
    }

    $display_name = get_the_author_meta('aqar_display_name', $userID) ?: $current_user->display_name;
    $translated_data["اسم العميل"] = $display_name;
    
    // Email content
    // Email content
    $email_content = "طلب عقد تسويق\n";
    foreach ($translated_data as $key => $value) {
        $email_content .= "\n{$key}: {$value}";
    }

    $random_number = str_pad(rand(0, 9999999), 8, '0', STR_PAD_LEFT);
    $req_borkerage_license_number = "{$random_number}-{$userID}";

    $email_content .= "\n نشكر لكم ثقتكم في منصة بوابة العقار";

    // Send email
    $admin_email  =  get_bloginfo('admin_email');
    $to = $admin_email;
    $subject = "{$req_borkerage_license_number} - طلب عقد تسويق";
    $message = $email_content;
    $headers = [];
    $send = wp_mail($to, $subject, $message, $headers);

    // Create WooCommerce order
    if (is_woocommerce_activated()) {
        $product_id = get_or_create_product();

        if (!$product_id || !wc_get_product($product_id)) {
            wp_send_json_error(['message' => 'Product not found.']);
        }

        WC()->cart->empty_cart();
        WC()->cart->add_to_cart($product_id);

        // session_start();//place this at the top of all code
        // $_SESSION['brokerage_form_data'] = $unserialized_data;

        // WC()->session->set('brokerage_form_data', $unserialized_data);

        $args = [
            'created_via' => 'checkout', // default values are "admin", "checkout", "store-api"
            'customer_id' => $userID,
        ];
        // Create order and assign to user
        $order = wc_create_order($args);
        $order->add_product(wc_get_product($product_id), 1);
        $order->set_address( $address, 'billing' );
        $order->calculate_totals();
        $order->update_meta_data('_brokerage_form_data', $unserialized_data);
        $order->save();
    
        // Get payment link
        $checkout_url = $order->get_checkout_payment_url();

        wp_send_json_success(['redirect' => $checkout_url]);
    } else {
        wp_send_json_error(['message' => 'WooCommerce is not activated.']);
    }
    
}

function get_or_create_product() {
    // Check if the product ID is stored in the options table
    $product_id = get_option('custom_brokerage_product_id');
    
    // If product ID exists, return it
    if ($product_id) {
        return $product_id;
    }

    // Otherwise, create a new product
    $product = new WC_Product_Simple();
    $product->set_name('رسوم عقد التسويق');
    $product->set_price(200);
    $product->set_regular_price(200);
    $product->set_description('Marketing contract and advertisement license fee');
    $product->set_sku('brokerage-license-fee'); // Optional: set a unique SKU
    $product->set_manage_stock(false);
    $product->save();

    // Save the new product ID in the options table
    $product_id = $product->get_id();
    update_option('custom_brokerage_product_id', $product_id);

    return $product_id;
}

// Add the custom data to the order
//add_action('woocommerce_checkout_update_order_meta', 'add_brokerage_data_to_order');

function add_brokerage_data_to_order($order_id) {
    $order = wc_get_order($order_id);
    if ($order) {
        $custom_brokerage_product_id = get_option('custom_brokerage_product_id');

        // Check if the order contains the specified product
        $contains_custom_product = false;
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() == $custom_brokerage_product_id) {
                $contains_custom_product = true;
                break;
            }
        }

        // If the order contains the specified product, proceed with adding session data
        if ($contains_custom_product) {
            session_start(); // Ensure the session is started
            $brokerage_form_data = $_SESSION['brokerage_form_data'] ?? '';
            if (!empty($brokerage_form_data)) {
                $order->update_meta_data('_brokerage_form_data', $brokerage_form_data);
                $order->save();
                // Clear the session data
                unset($_SESSION['brokerage_form_data']);
            }
        }
    }
}

// Customize the thank you page text
add_filter('woocommerce_thankyou_order_received_text', 'custom_thankyou_text_with_table', 10, 2);

function custom_thankyou_text_with_table($thank_you_text, $order) {
    $brokerage_form_data = $order->get_meta('_brokerage_form_data');
    
    // var_dump( can_view_property_req_info() );
    if (!empty($brokerage_form_data)) {
        $thank_you_text = "شكرًا لطلبكم عقد التسويق. يتم الآن إنشاء العقد الخاص بكم وسنقوم بمعالجته قريبًا.";

        // إنشاء الجدول
        $table_html = '<table class="dashboard-table table-lined table-hover responsive-table">';
        // $table_html .= '<thead><tr><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">الحقل</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">القيمة</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">الحقل</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">القيمة</th></tr></thead>';
        $table_html .= '<tbody>';

        $keys = array_keys($brokerage_form_data);
        for ($i = 0; $i < count($keys); $i += 2) {
            $key1 = $keys[$i];
            $key2 = $keys[$i + 1] ?? null;

            $translated_key1 = translate_key($key1);
            $translated_value1 = translate_value($key1, $brokerage_form_data[$key1]);

            $translated_key2 = $key2 ? translate_key($key2) : '';
            $translated_value2 = $key2 ? translate_value($key2, $brokerage_form_data[$key2]) : '';

            $table_html .= "<tr>";
            $table_html .= "<td style='border: 1px solid #ddd; padding: 8px;color: #2271b1;font-weight: bold;'>{$translated_key1}</td>";
            $table_html .= "<td style='border: 1px solid #ddd; padding: 8px;'>{$translated_value1}</td>";
            $table_html .= "<td style='border: 1px solid #ddd; padding: 8px;color: #2271b1;font-weight: bold;'>{$translated_key2}</td>";
            $table_html .= "<td style='border: 1px solid #ddd; padding: 8px;'>{$translated_value2}</td>";
            $table_html .= "</tr>";
        }

        $table_html .= '</tbody></table>';

        // إضافة الجدول إلى نص الشكر
        $thank_you_text .= $table_html;
    }

    return $thank_you_text;
}

function can_view_property_req(){
    $package_id    = get_user_meta( get_current_user_id(), 'package_id', true );
    if( empty($package_id ) ) {
        return false;
    }
    $view_prop_req = get_post_meta( $package_id, 'fave_view_prop_req', true );

    if( empty($view_prop_req) ) {
        return false;
    } else {
        return true;
    }
}

function can_view_property_req_info(){
    $package_id         = get_user_meta( get_current_user_id(), 'package_id', true );
    if( empty($package_id ) ) {
        return false;
    }
    $view_prop_req_info = get_post_meta( $package_id, 'fave_view_prop_req_info', true );
    if( empty($view_prop_req_info) ) {
        return false;
    } else {
        return true;
    }
   
}

add_action('add_meta_boxes', 'add_brokerage_data_meta_box');

function add_brokerage_data_meta_box() {
    add_meta_box(
        'brokerage_data_meta_box',    // ID of the meta box
        'بيانات عقد التسويق',          // Title of the meta box
        'display_brokerage_data_meta_box',  // Callback function
        'shop_order',                 // Screen where the meta box will appear
        'normal',                     // Context (normal, side, advanced)
        'high'                        // Priority
    );
}

function display_brokerage_data_meta_box($post) {
    $order = wc_get_order($post->ID);
    $brokerage_form_data = $order->get_meta('_brokerage_form_data');

    if (!empty($brokerage_form_data)) {
        echo '<div dir="rtl" class="brokerage_form_data">';

        // إنشاء الجدول
        echo '<table style="width:100%; border-collapse: collapse; margin-top: 20px;">';
        // echo '<thead><tr><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">الحقل</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">القيمة</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">الحقل</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">القيمة</th></tr></thead>';
        echo '<tbody>';

        $keys = array_keys($brokerage_form_data);
        for ($i = 0; $i < count($keys); $i += 2) {
            $key1 = $keys[$i];
            $key2 = $keys[$i + 1] ?? null;

            $translated_key1 = translate_key($key1);
            $translated_value1 = translate_value($key1, $brokerage_form_data[$key1]);

            $translated_key2 = $key2 ? translate_key($key2) : '';
            $translated_value2 = $key2 ? translate_value($key2, $brokerage_form_data[$key2]) : '';

            echo "<tr>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;color: #2271b1;font-weight: bold;'>{$translated_key1}</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$translated_value1}</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;color: #2271b1;font-weight: bold;'>{$translated_key2}</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$translated_value2}</td>";
            echo "</tr>";
        }

        echo '</tbody></table></div>';
    }
}

function translate_key($key) {
    $translate = [
        "owner-id" => "رقم الهوية",
        "owner-birth" => "تاريخ الميلاد",
        "id-type" => "نوع الهوية",
        "owner-mobile" => "رقم الموبايل",
        "property-document" => "وثيقة الملكية",
        "document-number" => "رقم الوثيقة",
        "property-type" => "نوع العقار",
        "property-area" => "مساحة العقار",
        "city" => "المدينة",
        "neighborhood" => "الحي",
        "parcel-number" => "رقم القطعة",
        "price" => "السعر",
        "street" => "الشارع",
        "postalCode" => "الرمز البريدي",
        "buildingNumber" => "رقم المبنى",
        "additionalNumber" => "رقم إضافي"
    ];

    return isset($translate[$key]) ? $translate[$key] : $key;
}

function translate_value($key, $value) {
    $translate = [
        "id-type" => [
            "national-id" => "هوية وطنية",
            "residence-permit" => "إقامة",
            "commercial-record" => "سجل تجاري"
        ],
        "property-document" => [
            "net-deed" => "صك إلكتروني",
            "paper-deed" => "صك ورقي",
            "property-deed" => "صك عقار مع حصر ورثة",
            "inheritance-certificate" => "حجة استحكام"
        ]
    ];

    return isset($translate[$key][$value]) ? $translate[$key][$value] : $value;
}



	
        
            

function aq_black_list($id){
    if( get_option( '_can_login' ) === 'yes' ) {
        $id_black_list = carbon_get_theme_option( 'id_black_list' );
        $can_login_content = !empty(carbon_get_theme_option( 'can_login_content' )) ? carbon_get_theme_option( 'can_login_content' ) : 'رقم الهوية غير مسموح به';

        if( ! empty( $id_black_list ) ) { 
            $ids = [];
            foreach( $id_black_list as $key => $value ) {
                 $ids[] = $value['id'];
            }
            if( in_array( $id, $ids ) ) {
                wp_send_json(['success' => false, 'message' => $can_login_content] );
                wp_die();
            }
        }
    }
}


function aq_is_black_list(){
    $check_number = [];
    $userID         = get_current_user_id();
    $id_number      = get_the_author_meta( 'aqar_author_id_number' , $userID );
    $type_id        = get_the_author_meta( 'aqar_author_type_id' , $userID );
    $user_mobile    = get_the_author_meta( 'fave_author_mobile' , $userID );

    // check the id if it start with 7 use it [user type agency] :bool
    $is_unified_number = aq_numberStartsWith($type_id, '7');
    
    if( $type_id === '2' && ! $is_unified_number ){
        $id_number = get_the_author_meta( 'aqar_author_unified_number' ,$userID );
        if( empty($id_number) ) {
            $id_number = get_the_author_meta( 'aqar_author_id_number' , $userID ); 
        }
    }
    $check_number[] =  $id_number ;
    $check_number[] =  $user_mobile ;

    
    if( get_option( '_can_add_property' ) === 'yes' ) {
        $id_black_list = carbon_get_theme_option( 'id_black_list' );
        if( ! empty( $id_black_list ) ) { 
            $ids = [];
            foreach( $id_black_list as $key => $value ) {
                $ids[] = $value['id'];
                if( in_array( $value['id'], $check_number ) ) {
                    return true;
                }
            }
        } else {
            return false;
        }

    }
    return false;
}

function aq_numberStartsWith($number, $prefix) {
    return substr($number, 0, strlen($prefix)) === $prefix;
}


function houzez_submit_listing($new_property) {

    $userID = get_current_user_id();
    $listings_admin_approved = houzez_option('listings_admin_approved');
    $edit_listings_admin_approved = houzez_option('edit_listings_admin_approved');
    $enable_paid_submission = houzez_option('enable_paid_submission');

    // Title
    if( isset( $_POST['prop_title']) ) {
        $new_property['post_title'] = sanitize_text_field( $_POST['prop_title'] );
    }

    if( $enable_paid_submission == 'membership' ) {
        $user_submit_has_no_membership = isset($_POST['user_submit_has_no_membership']) ? $_POST['user_submit_has_no_membership'] : '';
    } else {
        $user_submit_has_no_membership = 'no';
    }

    // Description
    if( isset( $_POST['prop_des'] ) ) {
        $new_property['post_content'] = wp_kses_post( wpautop( wptexturize( $_POST['prop_des'] ) ) );
    }

    $new_property['post_author'] = $userID;

    $submission_action = $_POST['action'];
    $prop_id = 0;

    if( $submission_action == 'add_property' ) {

        if( houzez_is_admin() ) {
            $new_property['post_status'] = 'publish';
        } else {
            if( $listings_admin_approved != 'yes' && ( $enable_paid_submission == 'no' || $enable_paid_submission == 'free_paid_listing' || $enable_paid_submission == 'membership' ) ) {
                if( $user_submit_has_no_membership == 'yes' ) {
                    $new_property['post_status'] = 'draft';
                } else {
                    $new_property['post_status'] = 'publish';
                }
            } else {
                if( $user_submit_has_no_membership == 'yes' && $enable_paid_submission = 'membership' ) {
                    $new_property['post_status'] = 'draft';
                } else {
                    $new_property['post_status'] = 'pending';
                }
            }
        }

        /*
         * Filter submission arguments before insert into database.
         */
        $new_property = apply_filters( 'houzez_before_submit_property', $new_property );
        $prop_id = wp_insert_post( $new_property );

        if( $prop_id > 0 ) {
            $submitted_successfully = true;
            if( $enable_paid_submission == 'membership'){ // update package status
                houzez_update_package_listings( $userID );
            }
        }

    } else if( $submission_action == 'update_property' ) {

        $new_property['ID'] = intval( $_POST['prop_id'] );

        if( get_post_status( intval( $_POST['prop_id'] ) ) == 'draft' ) {
            if( $enable_paid_submission == 'membership') {
                houzez_update_package_listings($userID);
            }
            if( $listings_admin_approved != 'yes' && ( $enable_paid_submission == 'no' || $enable_paid_submission == 'free_paid_listing' || $enable_paid_submission == 'membership' ) ) {
                $new_property['post_status'] = 'publish';
            } else {
                $new_property['post_status'] = 'pending';
            }
        } elseif( $edit_listings_admin_approved == 'yes' ) {
                $new_property['post_status'] = 'pending';
        }

        if( ! houzez_user_has_membership($userID) && $enable_paid_submission == 'membership' ) {
            $new_property['post_status'] = 'publish';

        }

        if( houzez_is_admin() ) {
            $new_property['post_status'] = 'publish';
        }

        /*
         * Filter submission arguments before update property.
         */
        $new_property = apply_filters( 'houzez_before_update_property', $new_property );
        $prop_id = wp_update_post( $new_property );

    }

    if( $prop_id > 0 ) {


        if(class_exists('Houzez_Fields_Builder')) {
            $fields_array = Houzez_Fields_Builder::get_form_fields();
            if(!empty($fields_array)):
                foreach ( $fields_array as $value ):
                    $field_name = $value->field_id;
                    $field_type = $value->type;

                    if( isset( $_POST[$field_name] ) && !empty( $_POST[$field_name] ) ) {

                        if( $field_type == 'checkbox_list' || $field_type == 'multiselect' ) {
                            delete_post_meta( $prop_id, 'fave_'.$field_name );
                            foreach ( $_POST[ $field_name ] as $value ) {
                                add_post_meta( $prop_id, 'fave_'.$field_name, sanitize_text_field( $value ) );
                            }
                        } else {
                            update_post_meta( $prop_id, 'fave_'.$field_name, sanitize_text_field( $_POST[$field_name] ) );
                        }

                    } else {
                        delete_post_meta( $prop_id, 'fave_'.$field_name );
                    }

                endforeach; 
            endif;
        }


        if( $user_submit_has_no_membership == 'yes' ) {
            update_user_meta( $userID, 'user_submit_has_no_membership', $prop_id );
            update_user_meta( $userID, 'user_submitted_without_membership', 'yes' );
        }

        // Add price post meta
        if( isset( $_POST['prop_price'] ) ) {
            update_post_meta( $prop_id, 'fave_property_price', sanitize_text_field( $_POST['prop_price'] ) );

            if( isset( $_POST['prop_label'] ) ) {
                update_post_meta( $prop_id, 'fave_property_price_postfix', sanitize_text_field( $_POST['prop_label']) );
            }
        }

        //price prefix
        if( isset( $_POST['prop_price_prefix'] ) ) {
            update_post_meta( $prop_id, 'fave_property_price_prefix', sanitize_text_field( $_POST['prop_price_prefix']) );
        }

        // Second Price
        if( isset( $_POST['prop_sec_price'] ) ) {
            update_post_meta( $prop_id, 'fave_property_sec_price', sanitize_text_field( $_POST['prop_sec_price'] ) );
        }

        // currency
        if( isset( $_POST['currency'] ) ) {
            update_post_meta( $prop_id, 'fave_currency', sanitize_text_field( $_POST['currency'] ) );
            if(class_exists('Houzez_Currencies')) {
                $currencies = Houzez_Currencies::get_property_currency_2($prop_id, $_POST['currency']);

                update_post_meta( $prop_id, 'fave_currency_info', $currencies );
            }
        }


        // Area Size
        if( isset( $_POST['prop_size'] ) ) {
            update_post_meta( $prop_id, 'fave_property_size', sanitize_text_field( $_POST['prop_size'] ) );
        }

        // Area Size Prefix
        if( isset( $_POST['prop_size_prefix'] ) ) {
            update_post_meta( $prop_id, 'fave_property_size_prefix', sanitize_text_field( $_POST['prop_size_prefix'] ) );
        }

        // Land Area Size
        if( isset( $_POST['prop_land_area'] ) ) {
            update_post_meta( $prop_id, 'fave_property_land', sanitize_text_field( $_POST['prop_land_area'] ) );
        }

        // Land Area Size Prefix
        if( isset( $_POST['prop_land_area_prefix'] ) ) {
            update_post_meta( $prop_id, 'fave_property_land_postfix', sanitize_text_field( $_POST['prop_land_area_prefix'] ) );
        }

        // Bedrooms
        if( isset( $_POST['prop_beds'] ) ) {
            update_post_meta( $prop_id, 'fave_property_bedrooms', sanitize_text_field( $_POST['prop_beds'] ) );
        }

        // Rooms
        if( isset( $_POST['prop_rooms'] ) ) {
            update_post_meta( $prop_id, 'fave_property_rooms', sanitize_text_field( $_POST['prop_rooms'] ) );
        }

        // Bathrooms
        if( isset( $_POST['prop_baths'] ) ) {
            update_post_meta( $prop_id, 'fave_property_bathrooms', sanitize_text_field( $_POST['prop_baths'] ) );
        }

        // Garages
        if( isset( $_POST['prop_garage'] ) ) {
            update_post_meta( $prop_id, 'fave_property_garage', sanitize_text_field( $_POST['prop_garage'] ) );
        }

        // Garages Size
        if( isset( $_POST['prop_garage_size'] ) ) {
            update_post_meta( $prop_id, 'fave_property_garage_size', sanitize_text_field( $_POST['prop_garage_size'] ) );
        }

        // Virtual Tour
        if( isset( $_POST['virtual_tour'] ) ) {
            update_post_meta( $prop_id, 'fave_virtual_tour', $_POST['virtual_tour'] );
        }

        // Year Built
        if( isset( $_POST['prop_year_built'] ) ) {
            update_post_meta( $prop_id, 'fave_property_year', sanitize_text_field( $_POST['prop_year_built'] ) );
        }

        // Property ID
        $auto_property_id = houzez_option('auto_property_id');
        if( $auto_property_id != 1 ) {
            if (isset($_POST['property_id'])) {
                update_post_meta($prop_id, 'fave_property_id', sanitize_text_field($_POST['property_id']));
            }
        } else {
                update_post_meta($prop_id, 'fave_property_id', $prop_id );
        }

        // Property Video Url
        if( isset( $_POST['prop_video_url'] ) ) {
            update_post_meta( $prop_id, 'fave_video_url', sanitize_text_field( $_POST['prop_video_url'] ) );
        }

        // property video image - in case of update
        $property_video_image = "";
        $property_video_image_id = 0;
        if( $submission_action == "update_property" ) {
            $property_video_image_id = get_post_meta( $prop_id, 'fave_video_image', true );
            if ( ! empty ( $property_video_image_id ) ) {
                $property_video_image_src = wp_get_attachment_image_src( $property_video_image_id, 'houzez-property-detail-gallery' );
                $property_video_image = $property_video_image_src[0];
            }
        }

        // clean up the old meta information related to images when property update
        if( $submission_action == "update_property" ){
            delete_post_meta( $prop_id, 'fave_property_images' );
            delete_post_meta( $prop_id, 'fave_attachments' );
            delete_post_meta( $prop_id, 'fave_agents' );
            delete_post_meta( $prop_id, 'fave_property_agency' );
            delete_post_meta( $prop_id, '_thumbnail_id' );
        }

        // Property Images
        if( isset( $_POST['propperty_image_ids'] ) ) {
            if (!empty($_POST['propperty_image_ids']) && is_array($_POST['propperty_image_ids'])) {
                $property_image_ids = array();
                foreach ($_POST['propperty_image_ids'] as $prop_img_id ) {
                    $property_image_ids[] = intval( $prop_img_id );
                    add_post_meta($prop_id, 'fave_property_images', $prop_img_id);
                }

                // featured image
                if( isset( $_POST['featured_image_id'] ) ) {
                    $featured_image_id = intval( $_POST['featured_image_id'] );
                    if( in_array( $featured_image_id, $property_image_ids ) ) {
                        update_post_meta( $prop_id, '_thumbnail_id', $featured_image_id );

                        /* if video url is provided but there is no video image then use featured image as video image */
                        if ( empty( $property_video_image ) && !empty( $_POST['prop_video_url'] ) ) {
                            update_post_meta( $prop_id, 'fave_video_image', $featured_image_id );
                        }
                    }
                } elseif ( ! empty ( $property_image_ids ) ) {
                    update_post_meta( $prop_id, '_thumbnail_id', $property_image_ids[0] );

                    /* if video url is provided but there is no video image then use featured image as video image */
                    if ( empty( $property_video_image ) && !empty( $_POST['prop_video_url'] ) ) {
                        update_post_meta( $prop_id, 'fave_video_image', $property_image_ids[0] );
                    }
                }
            }
        }

        if( isset( $_POST['propperty_attachment_ids'] ) ) {
                $property_attach_ids = array();
                foreach ($_POST['propperty_attachment_ids'] as $prop_atch_id ) {
                    $property_attach_ids[] = intval( $prop_atch_id );
                    add_post_meta($prop_id, 'fave_attachments', $prop_atch_id);
                }
        }


        // Add property type
        if( isset( $_POST['prop_type'] ) && ( $_POST['prop_type'] != '-1' ) ) {
            $type = array_map( 'intval', $_POST['prop_type'] );
            wp_set_object_terms( $prop_id, $type, 'property_type' );
        } else {
            wp_set_object_terms( $prop_id, '', 'property_type' );
        }

        // Add property status
        if( isset( $_POST['prop_status'] ) && ( $_POST['prop_status'] != '-1' ) ) {
            $prop_status = array_map( 'intval', $_POST['prop_status'] );
            wp_set_object_terms( $prop_id, $prop_status, 'property_status' );
        } else {
            wp_set_object_terms( $prop_id, '', 'property_status' );
        }

        // Add property status
        if( isset( $_POST['prop_labels'] ) ) {
            $prop_labels = array_map( 'intval', $_POST['prop_labels'] );
            wp_set_object_terms( $prop_id, $prop_labels, 'property_label' );
        } else {
            wp_set_object_terms( $prop_id, '', 'property_label' );
        }

        // Country
        if( isset( $_POST['country'] ) ) {
            $property_country = sanitize_text_field( $_POST['country'] );
            $country_id = wp_set_object_terms( $prop_id, $property_country, 'property_country' );
        } else {
            $default_country = houzez_option('default_country');
            $country_id = wp_set_object_terms( $prop_id, $default_country, 'property_country' );
        }
        
        // Postal Code
        if( isset( $_POST['postal_code'] ) ) {
            update_post_meta( $prop_id, 'fave_property_zip', sanitize_text_field( $_POST['postal_code'] ) );
        }

        
        if( isset( $_POST['locality'] ) ) {
            $property_city = sanitize_text_field( $_POST['locality'] );
            $city_id = wp_set_object_terms( $prop_id, $property_city, 'property_city' );

            $houzez_meta = array();
            $houzez_meta['parent_state'] = isset( $_POST['administrative_area_level_1'] ) ? $_POST['administrative_area_level_1'] : '';
            if( !empty( $city_id) && isset( $_POST['administrative_area_level_1'] ) ) {
                update_option('_houzez_property_city_' . $city_id[0], $houzez_meta);
            }
        }

        if( isset( $_POST['neighborhood'] ) ) {
            $property_area = sanitize_text_field( $_POST['neighborhood'] );
            $area_id = wp_set_object_terms( $prop_id, $property_area, 'property_area' );

            $houzez_meta = array();
            $houzez_meta['parent_city'] = isset( $_POST['locality'] ) ? $_POST['locality'] : '';
            if( !empty( $area_id) && isset( $_POST['locality'] ) ) {
                update_option('_houzez_property_area_' . $area_id[0], $houzez_meta);
            }
        }


        // Add property state
        if( isset( $_POST['administrative_area_level_1'] ) ) {
            $property_state = sanitize_text_field( $_POST['administrative_area_level_1'] );
            $state_id = wp_set_object_terms( $prop_id, $property_state, 'property_state' );

            $houzez_meta = array();
            $country_short = isset( $_POST['country'] ) ? $_POST['country'] : '';
            if(!empty($country_short)) {
               $country_short = strtoupper($country_short); 
            } else {
                $country_short = '';
            }
            
            $houzez_meta['parent_country'] = $country_short;
            if( !empty( $state_id) ) {
                update_option('_houzez_property_state_' . $state_id[0], $houzez_meta);
            }
        }
       
        // Add property features
        if( isset( $_POST['prop_features'] ) ) {
            $features_array = array();
            foreach( $_POST['prop_features'] as $feature_id ) {
                $features_array[] = intval( $feature_id );
            }
            wp_set_object_terms( $prop_id, $features_array, 'property_feature' );
        }

        // additional details
        if( isset( $_POST['additional_features'] ) ) {
            $additional_features = $_POST['additional_features'];
            if( ! empty( $additional_features ) ) {
                update_post_meta( $prop_id, 'additional_features', $additional_features );
                update_post_meta( $prop_id, 'fave_additional_features_enable', 'enable' );
            }
        } else {
            update_post_meta( $prop_id, 'additional_features', '' );
        }
         
        // rerBorders
        if( isset( $_POST['rerBorders'] ) ) {
            $rerBorders = $_POST['rerBorders'];
            if( ! empty( $rerBorders ) ) {
                update_post_meta( $prop_id, 'rerBorders', $rerBorders );
                update_post_meta( $prop_id, 'fave_rerBorders_enable', 'enable' );
            }
        } else {
            update_post_meta( $prop_id, 'rerBorders', '' );
        }

        // Borders
        if( isset( $_POST['borders'] ) ) {
            $borders = $_POST['borders'];
            if( ! empty( $borders ) ) {
                update_post_meta( $prop_id, 'borders', $borders );
            }
        } else {
            update_post_meta( $prop_id, 'borders', '' );
        }

        //Floor Plans
        if( isset( $_POST['floorPlans_enable'] ) ) {
            $floorPlans_enable = $_POST['floorPlans_enable'];
            if( ! empty( $floorPlans_enable ) ) {
                update_post_meta( $prop_id, 'fave_floor_plans_enable', $floorPlans_enable );
            }
        }

        if( isset( $_POST['floor_plans'] ) ) {
            $floor_plans_post = $_POST['floor_plans'];
            if( ! empty( $floor_plans_post ) ) {
                update_post_meta( $prop_id, 'floor_plans', $floor_plans_post );
            }
        } else {
            update_post_meta( $prop_id, 'floor_plans', '');
        }

        //Multi-units / Sub-properties
        if( isset( $_POST['multiUnits'] ) ) {
            $multiUnits_enable = $_POST['multiUnits'];
            if( ! empty( $multiUnits_enable ) ) {
                update_post_meta( $prop_id, 'fave_multiunit_plans_enable', $multiUnits_enable );
            }
        }

        if( isset( $_POST['fave_multi_units'] ) ) {
            $fave_multi_units = $_POST['fave_multi_units'];
            if( ! empty( $fave_multi_units ) ) {
                update_post_meta( $prop_id, 'fave_multi_units', $fave_multi_units );
            }
        } else {
            update_post_meta( $prop_id, 'fave_multi_units', '');
        }

        // Make featured
        if( isset( $_POST['prop_featured'] ) ) {
            $featured = intval( $_POST['prop_featured'] );
            update_post_meta( $prop_id, 'fave_featured', $featured );
        }

        // fave_loggedintoview
        if( isset( $_POST['login-required'] ) ) {
            $featured = intval( $_POST['login-required'] );
            update_post_meta( $prop_id, 'fave_loggedintoview', $featured );
        }

        // Mortgage
        if( $submission_action == 'add_property' ) {
            update_post_meta( $prop_id, 'fave_mortgage_cal', 0 );
            
        }

        // Private Note
        if( isset( $_POST['private_note'] ) ) {
            $private_note = wp_kses_post( $_POST['private_note'] );
            update_post_meta( $prop_id, 'fave_private_note', $private_note );
        }

        // disclaimer 
        if( isset( $_POST['property_disclaimer'] ) ) {
            $property_disclaimer = wp_kses_post( $_POST['property_disclaimer'] );
            update_post_meta( $prop_id, 'fave_property_disclaimer', $property_disclaimer );
        }

        //Energy Class
        if(isset($_POST['energy_class'])) {
            $energy_class = sanitize_text_field($_POST['energy_class']);
            update_post_meta( $prop_id, 'fave_energy_class', $energy_class );
        }
        if(isset($_POST['energy_global_index'])) {
            $energy_global_index = sanitize_text_field($_POST['energy_global_index']);
            update_post_meta( $prop_id, 'fave_energy_global_index', $energy_global_index );
        }
        if(isset($_POST['renewable_energy_global_index'])) {
            $renewable_energy_global_index = sanitize_text_field($_POST['renewable_energy_global_index']);
            update_post_meta( $prop_id, 'fave_renewable_energy_global_index', $renewable_energy_global_index );
        }
        if(isset($_POST['energy_performance'])) {
            $energy_performance = sanitize_text_field($_POST['energy_performance']);
            update_post_meta( $prop_id, 'fave_energy_performance', $energy_performance );
        }
        if(isset($_POST['epc_current_rating'])) {
            $epc_current_rating = sanitize_text_field($_POST['epc_current_rating']);
            update_post_meta( $prop_id, 'fave_epc_current_rating', $epc_current_rating );
        }
        if(isset($_POST['epc_potential_rating'])) {
            $epc_potential_rating = sanitize_text_field($_POST['epc_potential_rating']);
            update_post_meta( $prop_id, 'fave_epc_potential_rating', $epc_potential_rating );
        }


        // Property Payment
        if( isset( $_POST['prop_payment'] ) ) {
            $prop_payment = sanitize_text_field( $_POST['prop_payment'] );
            update_post_meta( $prop_id, 'fave_payment_status', $prop_payment );
        }


        if( isset( $_POST['fave_agent_display_option'] ) ) {

            $prop_agent_display_option = sanitize_text_field( $_POST['fave_agent_display_option'] );

            if( $prop_agent_display_option == 'agent_info' ) {

                $prop_agent = $_POST['fave_agents'];

                if(is_array($prop_agent)) {
                    foreach ($prop_agent as $agent) {
                        add_post_meta($prop_id, 'fave_agents', intval($agent) );
                    }
                }
                update_post_meta( $prop_id, 'fave_agent_display_option', $prop_agent_display_option );

                if (houzez_is_agency()) {
                    $user_agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );
                    if( !empty($user_agency_id)) {
                        update_post_meta($prop_id, 'fave_property_agency', $user_agency_id);
                    }
                }

            } elseif( $prop_agent_display_option == 'agency_info' ) {

                $user_agency_ids = $_POST['fave_property_agency'];

                if (houzez_is_agency()) {
                    $user_agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );
                    if( !empty($user_agency_id)) {
                        update_post_meta($prop_id, 'fave_property_agency', $user_agency_id);
                        update_post_meta($prop_id, 'fave_agent_display_option', $prop_agent_display_option);
                    } else {
                        update_post_meta( $prop_id, 'fave_agent_display_option', 'author_info' );
                    }

                } else {

                    if(is_array($user_agency_ids)) {
                        foreach ($user_agency_ids as $agency) {
                            add_post_meta($prop_id, 'fave_property_agency', intval($agency) );
                        }
                    }
                    update_post_meta($prop_id, 'fave_agent_display_option', $prop_agent_display_option);
                }
                
                
            } else {
                update_post_meta( $prop_id, 'fave_agent_display_option', $prop_agent_display_option );
            }

        } else {

            if (houzez_is_agency()) {
                $user_agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );
                if( !empty($user_agency_id) ) {
                    update_post_meta($prop_id, 'fave_agent_display_option', 'agency_info');
                    update_post_meta($prop_id, 'fave_property_agency', $user_agency_id);
                } else {
                    update_post_meta( $prop_id, 'fave_agent_display_option', 'author_info' );
                }

            } elseif(houzez_is_agent()){
                $user_agent_id = get_user_meta( $userID, 'fave_author_agent_id', true );

                if ( !empty( $user_agent_id ) ) {

                    update_post_meta($prop_id, 'fave_agent_display_option', 'agent_info');
                    update_post_meta($prop_id, 'fave_agents', $user_agent_id);

                } else {
                    update_post_meta($prop_id, 'fave_agent_display_option', 'author_info');
                }

            } else {
                update_post_meta($prop_id, 'fave_agent_display_option', 'author_info');
            }
        }

        // Address
        if( isset( $_POST['property_map_address'] ) ) {
            update_post_meta( $prop_id, 'fave_property_map_address', sanitize_text_field( $_POST['property_map_address'] ) );
            update_post_meta( $prop_id, 'fave_property_address', sanitize_text_field( $_POST['property_map_address'] ) );
        }

        if( ( isset($_POST['lat']) && !empty($_POST['lat']) ) && (  isset($_POST['lng']) && !empty($_POST['lng'])  ) ) {
            $lat = sanitize_text_field( $_POST['lat'] );
            $lng = sanitize_text_field( $_POST['lng'] );
            $streetView = isset( $_POST['prop_google_street_view'] ) ? sanitize_text_field( $_POST['prop_google_street_view'] ) : '';
            $lat_lng = $lat.','.$lng;

            update_post_meta( $prop_id, 'houzez_geolocation_lat', $lat );
            update_post_meta( $prop_id, 'houzez_geolocation_long', $lng );
            update_post_meta( $prop_id, 'fave_property_location', $lat_lng );
            update_post_meta( $prop_id, 'fave_property_map', '1' );
            update_post_meta( $prop_id, 'fave_property_map_street_view', $streetView );

        }
        

        if( $submission_action == 'add_property' ) {
            do_action( 'houzez_after_property_submit', $prop_id );

            if( houzez_option('add_new_property') == 1 ) {
                houzez_webhook_post( $_POST, 'houzez_add_new_property' );
            }

        } else if ( $submission_action == 'update_property' ) {
            do_action( 'houzez_after_property_update', $prop_id );

            if( houzez_option('add_new_property') == 1 ) {
                houzez_webhook_post( $_POST, 'houzez_update_property' );
            }
        }

    return $prop_id;
    }
}

function get_term_id_by_meta($meta_key, $meta_value, $taxonomy) {
    global $wpdb;
    $term_id = $wpdb->get_var($wpdb->prepare("
        SELECT term_id 
        FROM $wpdb->termmeta 
        WHERE meta_key = %s AND meta_value = %s
    ", $meta_key, $meta_value));
    
    return $term_id ? intval($term_id) : null;
}


/** -------------------------------------------------------------------------
 * add_registration_date_column 
 *-------------------------------------------------------------------------*/

// إضافة عمود جديد
function add_registration_date_column($columns) {
    $columns['registration_date'] = __('Registration Date', 'your-textdomain');
    return $columns;
}
add_filter('manage_users_columns', 'add_registration_date_column');
/** -------------------------------------------------------------------------
 * ملء البيانات في العمود الجديد  
 *-------------------------------------------------------------------------*/
function show_registration_date_column_content($value, $column_name, $user_id) {
    if ($column_name == 'registration_date') {
        $user = get_userdata($user_id);
        return date('Y/m/d', strtotime($user->user_registered));
    }
    return $value;
}
add_action('manage_users_custom_column', 'show_registration_date_column_content', 10, 3);
/** -------------------------------------------------------------------------
 * جعل العمود قابلاً للترتيب  
 *-------------------------------------------------------------------------*/
function make_registration_date_column_sortable($columns) {
    $columns['registration_date'] = 'user_registered';
    return $columns;
}
add_filter('manage_users_sortable_columns', 'make_registration_date_column_sortable');
/** -------------------------------------------------------------------------
 * ترتيب البيانات 
 *-------------------------------------------------------------------------*/
function sort_users_by_registration_date($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ('user_registered' === $query->get('orderby')) {
        $query->set('orderby', 'user_registered');
    }
}
add_action('pre_get_users', 'sort_users_by_registration_date');

function houzez_is_dashboard() {

    $files = apply_filters( 'houzez_is_dashboard_filter', array(
        'template/user_dashboard_profile.php',
        'template/user_dashboard_insight.php',
        'template/user_dashboard_crm.php',
        'template/user_dashboard_properties.php',
        'template/user_dashboard_favorites.php',
        'template/user_dashboard_invoices.php',
        'template/user_dashboard_saved_search.php',
        'template/user_dashboard_floor_plans.php',
        'template/user_dashboard_multi_units.php',
        'template/user_dashboard_membership.php',
        'template/user_dashboard_gdpr.php',
        'template/user_dashboard_submit.php',
        'template/user_dashboard_messages.php',
        'template/user_property_request.php',
        'property-request-page.php'
        
    ) );

    if ( is_page_template($files) ) {
        return true;
    }
    return false;
}

/** -------------------------------------------------------------------------
 * locations edit select 
 *-------------------------------------------------------------------------*/
function houzez_hirarchical_options($taxonomy_name, $taxonomy_terms, $searched_term, $prefix = " " ){

    if (!empty($taxonomy_terms) && taxonomy_exists($taxonomy_name)) {
        foreach ($taxonomy_terms as $term) {

            // Function to format term name
            // Get term meta
            $term_meta = get_option("_houzez_{$taxonomy_name}_{$term->term_id}");

            // Format term name
            $formatted_term_name = format_term_name($term->name, $term_meta);

            if( $taxonomy_name == 'property_area' ) {
                $term_meta= get_option( "_houzez_property_area_$term->term_id");
                $parent_city = sanitize_title($term_meta['parent_city']);

                if ( class_exists( 'sitepress' ) ) {
                    $default_lang = apply_filters( 'wpml_default_language', NULL );
                    $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_area', true, $default_lang );
                    $term_meta= get_option( "_houzez_property_area_$term_id_default");
                    $parent_city = sanitize_title($term_meta['parent_city']);
                    $parent_city = get_term_by( 'slug', $parent_city, 'property_city' )->slug;
                }

                echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_city).'" value="' . urldecode($term->slug) . '"' . ($searched_term == $term->slug ? ' selected="selected"' : '') . '>' . esc_attr($prefix) . $formatted_term_name . '</option>';

            } elseif( $taxonomy_name == 'property_city' ) {
                $term_meta= get_option( "_houzez_property_city_$term->term_id");
                $parent_state = sanitize_title($term_meta['parent_state']);

                if ( class_exists( 'sitepress' ) ) {
                    $default_lang = apply_filters( 'wpml_default_language', NULL );
                    $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_city', true, $default_lang );
                    $term_meta= get_option( "_houzez_property_city_$term_id_default");
                    $parent_state = sanitize_title($term_meta['parent_state']);
                    $parent_state = get_term_by( 'slug', $parent_state, 'property_state' )->slug;
                }

                echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_state).'" value="' . urldecode($term->slug) . '"' . ($searched_term == $term->slug ? ' selected="selected"' : '') . '>' . esc_attr($prefix) . esc_attr($formatted_term_name) . '</option>';

            } elseif( $taxonomy_name == 'property_state' ) {
                $term_meta = get_option( "_houzez_property_state_$term->term_id");
                $parent_country = sanitize_title($term_meta['parent_country']);

                if ( class_exists( 'sitepress' ) ) {
                    $default_lang = apply_filters( 'wpml_default_language', NULL );
                    $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_state', true, $default_lang );
                    $term_meta= get_option( "_houzez_property_state_$term_id_default");
                    $parent_country = sanitize_title($term_meta['parent_country']);
                    $parent_country = get_term_by( 'slug', $parent_country, 'property_country' )->slug;
                }

                echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_country).'" value="' . urldecode($term->slug) . '"' . ($searched_term == $term->slug ? ' selected="selected"' : '') . '>' . esc_attr($prefix) . esc_attr($formatted_term_name) . '</option>';

            } elseif( $taxonomy_name == 'property_country' ) {
        
                echo '<option data-ref="' . urldecode($term->slug) . '" value="' . urldecode($term->slug) . '"' . ($searched_term == $term->slug ? ' selected="selected"' : '') . '>' . esc_attr($prefix) . esc_attr($formatted_term_name) . '</option>';

            } else {

                echo '<option value="' . urldecode($term->slug) . '"' . ($searched_term == $term->slug ? ' selected="selected"' : '') . '>' . esc_attr($prefix) . esc_attr($formatted_term_name) . '</option>';
            }

            $child_terms = get_terms($taxonomy_name, array(
                'hide_empty' => false,
                'parent' => $term->term_id
            ));

            if (!empty($child_terms)) {
                houzez_hirarchical_options( $taxonomy_name, $child_terms, $searched_term, "- ".$prefix );
            }
        }
    }
}


function ag_get_term_id_by_name($term_name, $taxonomy) {
    $term = get_term_by('name', $term_name, $taxonomy);
    if ($term) {
        return $term->term_id;
    } else {
        return null; // Term not found
    }
}
function lowercase_rawurlencode($str) {
    return preg_replace_callback(
        '/%[0-9A-F]{2}/',
        function ($matches) {
            return strtolower($matches[0]);
        },
        rawurlencode($str)
    );
}

// Helper function to format term name
function format_term_name($name, $term_meta) {
    // Replace all hyphens with spaces
    $name = str_replace('-', ' ', $name);
    
    // Remove REGION_ID, CITY_ID, DISTRICT_ID if they exist in term meta
    $region_id = isset($term_meta['REGION_ID']) ? $term_meta['REGION_ID'] : '';
    $city_id = isset($term_meta['CITY_ID']) ? $term_meta['CITY_ID'] : '';
    $district_id = isset($term_meta['DISTRICT_ID']) ? $term_meta['DISTRICT_ID'] : '';
    
    // Remove these IDs from the name
    if ($region_id) {
        $name = str_replace($region_id, '', $name);
    }
    if ($city_id) {
        $name = str_replace($city_id, '', $name);
    }
    if ($district_id) {
        $name = str_replace($district_id, '', $name);
    }

    // Remove extra spaces
    $name = preg_replace('/\s+/', ' ', $name);
    $name = trim($name);

    return $name;
}


function custom_redirect_from_my_account() {
    if (is_account_page() ) {
        wp_redirect(home_url()); // Replace with the URL you want to redirect to.
        exit;
    }
}
add_action('template_redirect', 'custom_redirect_from_my_account');

/**
 * Save REGA property data to WordPress post meta
 * This function is used both when adding a new property and when syncing existing properties
 *
 * @param int $property_id The WordPress post ID of the property
 * @param object $data The REGA API response data object
 * @return bool True on success, false on failure
 */
function save_rega_property_data($property_id, $data) {
    if (empty($property_id) || empty($data)) {
        return false;
    }

    // Helper function to remove leading zero from codes
    $removeLeadingZero = function($string) {
        if (substr($string, 0, 1) === '0' && ctype_digit(substr($string, 1, 1))) {
            return substr($string, 1);
        }
        return $string;
    };

    // Save the complete advertisement response
    $advertisement_response = json_decode(json_encode($data), true);
    update_post_meta($property_id, 'advertisement_response', $advertisement_response);

    /* -----------------------------------------------------------------------
     *  New fields added in recent versions
     * ----------------------------------------------------------------------*/

    // حدود واطوال العقار من وزارة العدل
    if (isset($data->borders)) {
        $borders = json_decode(json_encode($data->borders), true);
        update_post_meta($property_id, 'borders', $borders);
    }

    // وجود وقف ؟
    if (isset($data->isHalted)) {
        update_post_meta($property_id, 'fave_d988d8acd988d8af-d988d982d981', $data->isHalted);
    }

    // وجود وصية ؟
    if (isset($data->isTestment)) {
        update_post_meta($property_id, 'fave_d988d8acd988d8af-d988d8b5d98ad8a9', $data->isTestment);
    }

    // قيود السجل العيني
    if (isset($data->rerConstraints)) {
        update_post_meta($property_id, 'fave_d982d98ad988d8af-d8a7d984d8b3d8acd984-d8a7d984d8b9d98ad986d98a', $data->rerConstraints);
    }

    // رقم القطعة
    if (isset($data->landNumber)) {
        update_post_meta($property_id, 'fave_d8b1d982d985-d8a7d984d982d8b7d8b9d8a9', $data->landNumber);
    }

    // رابط ترخيص الاعلان
    if (isset($data->adLicenseURL)) {
        update_post_meta($property_id, 'fave_d8b1d8a7d8a8d8b7-d8aad8b1d8aed98ad8b5-d8a7d984d8a7d8b9d984d8a7d986', $data->adLicenseURL);
    }

    // مصدر ترخيص الاعلان
    if (isset($data->adSource)) {
        update_post_meta($property_id, 'fave_d985d8b5d8afd8b1-d8aad8b1d8aed98ad8b5-d8a7d984d8a7d8b9d984d8a7d986', $data->adSource);
    }

    // اسم مسؤول الإعلان (New field V3.2)
    if (isset($data->responsibleEmployeeName)) {
        update_post_meta($property_id, 'responsibleEmployeeName', $data->responsibleEmployeeName);
    }

    // رقم مسؤول الإعلان (New field V3.2)
    if (isset($data->responsibleEmployeePhoneNumber)) {
        update_post_meta($property_id, 'responsibleEmployeePhoneNumber', $data->responsibleEmployeePhoneNumber);
    }


    // نوع رسوم نقل الملكية (New field V3.2)
    if (isset($data->ownershipTransferFeeType)) {
        update_post_meta($property_id, "ownershipTransferFeeType", $data->ownershipTransferFeeType);
    }

    // نوع وثيقة الملكية
    if (isset($data->titleDeedTypeName)) {
        update_post_meta($property_id, 'fave_d986d988d8b9-d988d8abd98ad982d8a9-d8a7d984d985d984d983d98ad8a9', $data->titleDeedTypeName);
    }

    // وصف موقع العقار حسب الصك
    if (isset($data->LocationDescriptionOnMOJDeed)) {
        update_post_meta($property_id, 'fave_d988d8b5d981-d985d988d982d8b9-d8a7d984d8b9d982d8a7d8b1', $data->LocationDescriptionOnMOJDeed);
        update_post_meta($property_id, 'LocationDescriptionOnMOJDeed', $data->LocationDescriptionOnMOJDeed);
    }

    // ملاحظات
    if (isset($data->Notes)) {
        update_post_meta($property_id, 'fave_d8a7d984d985d984d8a7d8add8b8d8a7d8aa', $data->Notes);
    }

    if (isset($data->adLicenseURL)) {
        update_post_meta($property_id, 'qrCodeUrl', $data->adLicenseURL);
        update_post_meta($property_id, 'adLicenseURL', $data->adLicenseURL);
    }

    /* -----------------------------------------------------------------------
     *  Core property fields
     * ----------------------------------------------------------------------*/

    // Add price post meta
    if (isset($data->propertyPrice)) {
        update_post_meta($property_id, 'fave_property_price', $data->propertyPrice);
    }

    // Add property type
    if (isset($data->propertyType) && ($data->propertyType != '')) {
        $type = $data->propertyType;
        wp_set_object_terms($property_id, $type, 'property_type');
    } else {
        wp_set_object_terms($property_id, '', 'property_type');
    }

    // Add property status
    if (isset($data->advertisementType) && ($data->advertisementType != '')) {
        $prop_status = $data->advertisementType;
        wp_set_object_terms($property_id, $prop_status, 'property_status');
    } else {
        wp_set_object_terms($property_id, '', 'property_status');
    }

    // Postal Code
    if (isset($data->location->postalCode)) {
        update_post_meta($property_id, 'fave_property_zip', $data->location->postalCode);
    }

    // Street Width
    if (isset($data->streetWidth)) {
        update_post_meta($property_id, 'fave_d8b9d8b1d8b6-d8a7d984d8b4d8a7d8b1d8b9', $data->streetWidth);
    }

    // Property Age
    if (isset($data->propertyAge)) {
        update_post_meta($property_id, 'fave_property_year', $data->propertyAge);
    }

    // Number of Rooms
    if (isset($data->numberOfRooms)) {
        update_post_meta($property_id, 'fave_property_rooms', $data->numberOfRooms);
    }

    // Location taxonomies
    $state_id = [];
    // Add property state
    if (isset($data->location->region) && !empty($data->location->regionCode)) {
        $state_code = $removeLeadingZero($data->location->regionCode);
        $property_state = str_replace(' ', '-', $data->location->region) . '-' . $state_code;
        $term_id = get_term_id_by_meta('REGION_ID', $state_code, 'property_state');
        if ($term_id !== null) {
            $state_id = wp_set_object_terms($property_id, $term_id, 'property_state');
        } else {
            $state_id = wp_set_object_terms($property_id, $property_state, 'property_state');
            if (!empty($state_id) && !is_wp_error($state_id)) {
                update_term_meta($state_id[0], 'REGION_ID', $state_code);
            }
        }
    }

    $city_id = [];
    // Add property city
    if (isset($data->location->city) && !empty($data->location->cityCode)) {
        $city_code = $removeLeadingZero($data->location->cityCode);
        $property_city = str_replace(' ', '-', $data->location->city) . '-' . $city_code;
        $term_id = get_term_id_by_meta('CITY_ID', $city_code, 'property_city');
        if ($term_id !== null) {
            $city_id = wp_set_object_terms($property_id, $term_id, 'property_city');
        } else {
            $city_id = wp_set_object_terms($property_id, $property_city, 'property_city');
        }

        if (!empty($state_id) && !is_wp_error($state_id)) {
            $term_object = get_term($state_id[0]);
            $parent_state = $term_object->slug;
            $houzez_meta = array();
            $houzez_meta['parent_state'] = $parent_state;
            if (!empty($city_id) && !empty($houzez_meta['parent_state'])) {
                update_option('_houzez_property_city_' . $city_id[0], $houzez_meta);
                update_term_meta($city_id[0], 'CITY_ID', $city_code);
            }
        }
    }

    $area_id = [];
    // Add property area
    if (isset($data->location->district) && !empty($data->location->districtCode)) {
        $area_code = $removeLeadingZero($data->location->districtCode);
        $property_area = str_replace(' ', '-', $data->location->district) . '-' . $area_code;
        $term_id = get_term_id_by_meta('DISTRICT_ID', $area_code, 'property_area');
        if ($term_id !== null) {
            $area_id = wp_set_object_terms($property_id, $term_id, 'property_area');
        } else {
            $area_id = wp_set_object_terms($property_id, $property_area, 'property_area');
        }

        if (!empty($city_id) && !is_wp_error($city_id)) {
            $term_object = get_term($city_id[0]);
            $parent_city = $term_object->slug;
            $houzez_meta = array();
            $houzez_meta['parent_city'] = $parent_city;
            if (!empty($area_id) && !empty($houzez_meta['parent_city'])) {
                update_option('_houzez_property_area_' . $area_id[0], $houzez_meta);
                update_term_meta($area_id[0], 'DISTRICT_ID', $area_code);
            }
        }
    }

    // Property size
    if (isset($data->propertyArea)) {
        update_post_meta($property_id, 'fave_property_size', $data->propertyArea);
    }

    // Plan number
    if (isset($data->planNumber)) {
        update_post_meta($property_id, 'fave_d8b1d982d985-d8a7d984d985d8aed8b7d8b7', $data->planNumber);
    }

    // سعر متر البيع
    if (isset($data->propertyPrice)) {
        update_post_meta($property_id, 'fave_d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9', $data->propertyPrice);
    }

    // obligationsOnTheProperty
    if (isset($data->obligationsOnTheProperty)) {
        update_post_meta($property_id, 'fave_d8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985', $data->obligationsOnTheProperty);
    }

    $adress = 'المملكة العربية السعودية';
    // Address
    if (isset($data->location->region) && isset($data->location->city)) {
        $country = 'المملكة العربية السعودية';
        $adress = $country . ', ' . $data->location->region . ', ' . $data->location->city;
        update_post_meta($property_id, 'fave_property_map_address', $adress);
        update_post_meta($property_id, 'fave_property_address', $adress);
    }

    // lat & long
    if ((isset($data->location->latitude) && !empty($data->location->latitude)) && (isset($data->location->longitude) && !empty($data->location->latitude))) {
        $lat = $data->location->latitude;
        $lng = $data->location->longitude;

        /* Note: OpenStreet address search has been disabled to avoid dependency on PropertyModule */

        $streetView = '';
        $lat_lng = $lat . ',' . $lng;

        update_post_meta($property_id, 'houzez_geolocation_lat', $lat);
        update_post_meta($property_id, 'houzez_geolocation_long', $lng);
        update_post_meta($property_id, 'fave_property_location', $lat_lng);
        update_post_meta($property_id, 'fave_property_map', '1');
        update_post_meta($property_id, 'fave_property_map_street_view', $streetView);
    }

    // Land Area Size
    if (isset($data->propertyArea)) {
        update_post_meta($property_id, 'fave_property_land', $data->propertyArea);
    }

    // Property Face
    if (isset($data->propertyFace)) {
        update_post_meta($property_id, 'fave_d988d8a7d8acd987d8a9-d8a7d984d8b9d982d8a7d8b1', $data->propertyFace);
    }

    // Advertiser and license information
    update_post_meta($property_id, 'advertiserId', $data->advertiserId);
    update_post_meta($property_id, 'adLicenseNumber', $data->adLicenseNumber);
    /* ---------------------------- رقم ترخيص الاعلان --------------------------- */
    update_post_meta($property_id, 'fave_d8b1d982d985-d8a7d984d8aad981d988d98ad8b6', $data->adLicenseNumber);

    update_post_meta($property_id, 'brokerageAndMarketingLicenseNumber', $data->brokerageAndMarketingLicenseNumber);
    /* --------------------------- //رقم وثيقة الملكية -------------------------- */
    update_post_meta($property_id, 'deedNumber', $data->deedNumber);
    update_post_meta($property_id, 'fave_d8b1d982d985-d988d8abd98ad982d8a9-d8a7d984d985d984d983d98ad8a9', $data->deedNumber);

    update_post_meta($property_id, 'TitleDeed', $data->deedNumber);

    /*---------------------------------------------------------------------------------*
    * Save expiration meta
    *----------------------------------------------------------------------------------*/
    update_post_meta($property_id, 'creationDate', $data->creationDate);
    /* --------------------------- تاريخ اصدار الاعلان -------------------------- */
    update_post_meta($property_id, 'fave_d8a7d8b3d8aad8aed8a7d985-d8a7d984d8b9d982d8a7d8b1', $data->creationDate);

    update_post_meta($property_id, 'endDate', $data->endDate);
    /* ----------------------- //تاريخ انتهاء رخصة الاعلان ---------------------- */
    update_post_meta($property_id, 'fave_d8aad8a7d8b1d98ad8ae-d8a7d986d8aad987d8a7d8a1-d8b1d8aed8b5d8a9-d8a7d984d8a5d8b9d984d8a7d986', $data->creationDate);

    update_post_meta($property_id, 'houzez_manual_expire', 'checked');

    // Schedule/Update Expiration
    $options = [];
    $options['id'] = $property_id;
    $datetime = DateTime::createFromFormat('d/m/Y', $data->endDate);
    $timestamp = $datetime->getTimestamp();

    if (wp_next_scheduled('houzez_property_expirator_expire', [$property_id]) !== false) {
        wp_clear_scheduled_hook('houzez_property_expirator_expire', [$property_id]); //Remove any existing hooks
    }

    wp_schedule_single_event($timestamp, 'houzez_property_expirator_expire', [$property_id]);

    // Update Post Meta
    update_post_meta($property_id, '_houzez_expiration_date', $timestamp);
    update_post_meta($property_id, '_houzez_expiration_date_options', $options);

    /*---------------------------------------------------------------------------------*
    * End expiration meta
    *----------------------------------------------------------------------------------*/

    // Additional property information
    if (isset($data->obligationsOnTheProperty)) {
        update_post_meta($property_id, 'fave_d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8a7d984d8a3d8aed8b1d989-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1', $data->obligationsOnTheProperty);
    }
    if (isset($data->guaranteesAndTheirDuration)) {
        update_post_meta($property_id, 'fave_d8a7d984d8b6d985d8a7d986d8a7d8aa-d988d985d8afd8aad987d8a7', $data->guaranteesAndTheirDuration);
    }

    if (isset($data->theBordersAndLengthsOfTheProperty)) {
        update_post_meta($property_id, 'fave_d8add8afd988d8af-d988d8a3d8b7d988d8a7d984-d8a7d984d8b9d982d8a7d8b1', $data->theBordersAndLengthsOfTheProperty);
    }

    if (isset($data->complianceWithTheSaudiBuildingCode)) {
        update_post_meta($property_id, 'fave_d985d8b7d8a7d8a8d982d8a9-d983d988d8af-d8a7d984d8a8d986d8a7d8a1-d8a7d984d8b3d8b9d988d8afd98a', $data->complianceWithTheSaudiBuildingCode);
    }

    // Property utilities (multiple values)
    if (isset($data->propertyUtilities)) {
        // First, delete all existing utilities for this property
        delete_post_meta($property_id, 'fave_d8aed8afd985d8a7d8aa-d8a7d984d8b9d982d8a7d8b1');

        if (is_array($data->propertyUtilities)) {
            foreach ($data->propertyUtilities as $propertyUtiliti) {
                add_post_meta($property_id, 'fave_d8aed8afd985d8a7d8aa-d8a7d984d8b9d982d8a7d8b1', $propertyUtiliti);
            }
        }
    }

    // Channels (multiple values)
    if (isset($data->channels)) {
        // First, delete all existing channels for this property
        delete_post_meta($property_id, 'fave_d982d986d988d8a7d8aa-d8a7d984d8a5d8b9d984d8a7d986');

        if (is_array($data->channels)) {
            foreach ($data->channels as $channel) {
                add_post_meta($property_id, 'fave_d982d986d988d8a7d8aa-d8a7d984d8a5d8b9d984d8a7d986', $channel);
            }
        }
    }

    // Property labels/usages
    if (isset($data->propertyUsages)) {
        if (is_array($data->propertyUsages)) {
            foreach ($data->propertyUsages as $propertyUsage) {
                wp_set_object_terms($property_id, $propertyUsage, 'property_label');
            }
        }
    }

    return true;
}

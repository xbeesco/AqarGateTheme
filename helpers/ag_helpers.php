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

function get_houzez_listing_expire($postID) {
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

                $publish_date = $post->post_date;
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
    $user_role = houzez_user_role_by_user_id( $author_id );
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
                
                $output .= '<div class="agent-image" style="margin-bottom: 20px;">';
                    
                    if ( $is_single == false ) {
                        $output .= '<input type="checkbox" class="houzez-hidden" checked="checked" class="multiple-agent-check" name="target_email[]" value="' . $args['agent_email'] . '" >';
                    }

                    $output .= '<img class="rounded" src="' . $args['picture'] . '" alt="' . $args['agent_name'] . '">';

                $output .= '</div>';

                $output .= '<ul class="agent-information list-unstyled">';

                    if (!empty($args['agent_name'])) {
                        $output .= '<li class="agent-name">';
                            $output .= '<i class="houzez-icon icon-single-neutral mr-1"></i> '.$args['agent_name'];
                        $output .= '</li>';
                    }
                    if( $author_id  && !empty( $ad_number ) ) {
                        $output .= '<li class="agent-ad-number">';
                          $output .= '<i class="houzez-icon icon-accounting-document mr-1"></i> رقم المعلن :  ' . esc_attr( $ad_number );
                        $output .= '</li>';
                    }

                    if( $author_id  && !empty( $Advertiser_character ) ) {
                        $output .= '<li class="Advertiser_character">';
                          $output .= '<i class="houzez-icon icon-accounting-document mr-1"></i>  صفه المعلن :  ' . esc_attr( $Advertiser_character );
                        $output .= '</li>';
                    }
                    
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
        'choose_currency' 			=> esc_html__( 'Choose Currency', 'houzez' ),
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
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
    $tabel = $wpdb->prefix . 'houzez_threads';
    global $wpdb, $current_user;
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

            $conversation_id[] = $AqarGateApi->get_all_conversation( $current_user_id,  $conversation ) ;
        }

        $response['conversations'] =  $conversation_id;
        $response_data['user_conversations']  = AqarGateApi::response( $response );
    }else{
        $response_data['user_conversations']  = [];
    }

    $response_data['user_info']     = $AqarGateApi->author( $request );
    $response_data['user_invoices'] = $AqarGateApi->get_invoices( $request );
    if( houzez_is_agency() === true ) {
        $response_data['user_agents']   = $AqarGateApi->get_agents( $request );
    }else{
        $response_data['user_agents']   = []; 
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
    $propertyType  = wp_get_post_terms( $prop_id, 'property_type' );
    $overview_data ="";
    if( $propertyType ){
        $overview_data = carbon_get_term_meta($propertyType[0]->term_id, $options);
    }
    
    if (!empty($overview_data)) {
        $overview_data = array_flip($overview_data);
    }
    // return var_export(get_the_ID());

    $i = 0;
    $data = [];
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
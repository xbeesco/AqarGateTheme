<?php

/**
 * get_prop_data
 *
 * @param  mixed $prop_id
 * @param  mixed $data_collection
 * @return array/prop/data
 */
function get_prop_data( $prop_id , $data_collection, $user_id ){
    
    
    global $post;   
    $post = $prop_id;
    setup_postdata( $post ); 

    $fav_ids = 'houzez_favorites-'.$user_id;
    $fav_ids = get_option( $fav_ids );

    $data['id'] = $prop_id;
    $statuses = get_post_statuses();
      
    $data['prop_status'] = [ 
        "status" => $statuses[ get_post_status($prop_id ) ] ,
        "key" => get_post_status( $prop_id ) 
    ];
    $data['is_favorite'] = 0;
    if( !empty($fav_ids) && is_array($fav_ids)){
        if( in_array(get_the_ID(), $fav_ids) ) {
            $data['is_favorite'] = 1;
        }
    }
    
    $is_featured = get_post_meta( get_the_ID( ), 'fave_featured', true);
    if( empty( $is_featured ) ) {
        $is_featured = 0;
    }else{
        $is_featured = intval( $is_featured );
    }
    $data['is_featured'] = $is_featured;
    $data['property_overview'] = property_overview_details( 'crb_overview_fields', get_the_ID() );
    $data['property_details']  = property_overview_details( 'crb_details_fields', get_the_ID() );
    $author_picture_id         =   get_the_author_meta( 'fave_author_picture_id' , $user_id );
    $user_custom_picture       =   get_the_author_meta( 'fave_author_custom_picture' , $user_id );

        if( !empty( $author_picture_id ) ) {
            $author_picture_id = intval( $author_picture_id );
            if ( $author_picture_id ) {
                $author_picture =  [
                        'url' => wp_get_attachment_image_url( $author_picture_id, 'large' ),
                        'id'  => $author_picture_id
                ];
            }
        } else {
            $author_picture =  [
                'url' => $user_custom_picture,
                'id'  => null
            ];
        }

    if ( 'search' === $data_collection ){
        $data['title'] = get_the_title();
        $data['description'] = get_the_content();
        $data['author'] = get_the_author();
        $data['author_pic'] = $author_picture;
        $data['pricePin'] = houzez_listing_price_map_pins();
        $data['property_type'] = houzez_taxonomy_simple('property_type'); 
        //Featured image
        if ( has_post_thumbnail() ) {
            $thumbnail_id         = get_post_thumbnail_id();
            $thumbnail_array = wp_get_attachment_image_src( $thumbnail_id, 'houzez-item-image-1' );
            if ( ! empty( $thumbnail_array[ 0 ] ) ) {
                $data[ 'thumbnail' ] = $thumbnail_array[ 0 ];
            }
        }  
        $data['bedrooms'] = houzez_get_listing_data('property_bedrooms');
        $data['rooms'] = houzez_get_listing_data('property_rooms');
        $data['bathrooms'] = houzez_get_listing_data('property_bathrooms');
        $data['garage'] = houzez_get_listing_data('property_garage'); 
    }

    if ( 'popup-search' === $data_collection || 'list' === $data_collection ){
        $data[ 'title' ] = get_the_title();
        $data['author']  = get_the_author();
        $data['author_pic'] = $author_picture;
        $data['pricePin'] = houzez_listing_price_map_pins();
        $data['property_type'] = houzez_taxonomy_simple('property_type'); 
        //Featured image
        if ( has_post_thumbnail() ) {
            $thumbnail_id         = get_post_thumbnail_id();
            $thumbnail_array = wp_get_attachment_image_src( $thumbnail_id, 'houzez-item-image-1' );
            if ( ! empty( $thumbnail_array[ 0 ] ) ) {
                $data[ 'thumbnail' ] = $thumbnail_array[ 0 ];
            }
        }
        $data['bedrooms'] = houzez_get_listing_data('property_bedrooms');
        $data['rooms'] = houzez_get_listing_data('property_rooms');
        $data['bathrooms'] = houzez_get_listing_data('property_bathrooms');
        $data['garage'] = houzez_get_listing_data('property_garage');

    }

    if ( 'list' === $data_collection ){
        $data[ 'date' ] = get_the_date();
        $address = houzez_get_listing_data('property_map_address');
        if(!empty($address)) {
            $data['address'] = $address;
        }else{
            $data['address'] = '';
        }

        $propID = get_the_ID();
        $prop_size = houzez_get_listing_data('property_size');
        $listing_area_size = houzez_get_listing_area_size( $propID );
        $listing_size_unit = houzez_get_listing_size_unit( $propID );
        $data['prop_size'] = $listing_area_size; 

        $prop_bath  = houzez_get_listing_data('property_bathrooms');
        $prop_bath_label = ($prop_bath > 1 ) ? houzez_option('glc_baths', 'Baths') : houzez_option('glc_bath', 'Bath');
        if( $prop_bath != '' ) {
            $data['prop_bath'] = $prop_bath . ' ' . $prop_bath_label; 
        }
        $prop_bed  = houzez_get_listing_data('property_bedrooms');
        $prop_bed_label = ($prop_bed > 1 ) ? houzez_option('glc_beds', 'Beds') : houzez_option('glc_bed', 'Bed');
        if( $prop_bed != '' ) { 
            $data['prop_bed'] = $prop_bed . ' ' . $prop_bed_label; 
        }

        $prop_garage = houzez_get_listing_data('property_garage');
        $prop_garage_label = ($prop_garage > 1 ) ? houzez_option('glc_garages', 'Garages') : houzez_option('glc_garage', 'Garage');
        if($prop_garage != '') {
            $data['prop_garage'] = $prop_garage . ' ' . $prop_garage_label;
        }

        $data['city'] = houzez_taxonomy_simple('property_city');
        $data['area'] = houzez_taxonomy_simple('property_area');
        $data['status'] = houzez_taxonomy_simple('property_status');
        $data['label'] = houzez_taxonomy_simple('property_label');

    }
    $location = houzez_get_listing_data('property_location');
    $location = explode(',', $location);
    $data['coordinate'] = [
        'latitude'   => isset($location[0]) ? $location[0] : '',
        'longitude'  => isset($location[1]) ? $location[1] : ''
    ];
    if ( 'property' === $data_collection ){ 

        $data = property_details( $user_id ); 
    }

    wp_reset_postdata();
    return $data ;

}
 
    /**
     * property_details
     *
     * @param  mixed $user_id
     * @return void
     */
    function property_details($user_id){

        $data['id'] = get_the_ID();
        $statuses = get_post_statuses();
        $data['prop_status'] = [ 
            "status" => $statuses[ get_post_status( get_the_ID() ) ] ,
            "key" => get_post_status( get_the_ID() ) 
        ];
        $fav_ids = 'houzez_favorites-'.$user_id;
        $fav_ids = get_option( $fav_ids );
        $data['is_favorite'] = 0;
            if( !empty($fav_ids) && is_array($fav_ids)){
                if( in_array(get_the_ID(), $fav_ids) ) {
                    $data['is_favorite'] = 1;
                }
            }
            
        $is_featured = get_post_meta( get_the_ID( ), 'fave_featured', true);
        if( empty( $is_featured ) ) {
            $is_featured = 0;
        }else{
            $is_featured = intval( $is_featured );
        }
        $data['is_featured'] = $is_featured;
        $data['location'] = houzez_get_listing_data('property_location');
        $data[ 'title' ] = get_the_title();

        $author_id = get_the_author_meta( 'ID' ); 
        $user_role  = houzez_user_role_by_user_id( $author_id );
        if( $user_role == "houzez_agent"  ) { $Advertiser_character =  "مفوض";}
        elseif( $user_role == "houzez_agency" ) { $Advertiser_character =  "مفوض"; }
        elseif( $user_role == "houzez_owner"  ) { $Advertiser_character =  "مالك"; } 
        elseif( $user_role == "houzez_buyer"  ) { $Advertiser_character =  "مفوض"; } 
        elseif( $user_role == "houzez_seller" ) { $Advertiser_character =  "مفوض" ; }
        elseif( $user_role == "houzez_manager") { $Advertiser_character = "مفوض"; }
        $author_picture_id         =   get_the_author_meta( 'fave_author_picture_id' , $user_id );
        $user_custom_picture    =   get_the_author_meta( 'fave_author_custom_picture' , $user_id );
        if( !empty( $author_picture_id ) ) {
            $author_picture_id = intval( $author_picture_id );
            if ( $author_picture_id ) {
                $author_picture =  [
                        'url' => wp_get_attachment_image_url( $author_picture_id, 'large' ),
                        'id'  => $author_picture_id
                ];
            }
        } else {
            $author_picture =  [
                'url' => $user_custom_picture,
                'id'  => null
            ];
        }
        $data['author_pic'] = $author_picture;
        $data['author'] = get_the_author();
        $data['author_character'] = $Advertiser_character;
        $data['pricePin'] = houzez_listing_price_map_pins();
        $data['property_type'] = houzez_taxonomy_simple('property_type'); 
        //Featured image
        if ( has_post_thumbnail() ) {
            $thumbnail_id         = get_post_thumbnail_id();
            $thumbnail_array = wp_get_attachment_image_src( $thumbnail_id, 'houzez-item-image-1' );
            if ( ! empty( $thumbnail_array[ 0 ] ) ) {
                $data[ 'thumbnail' ] = $thumbnail_array[ 0 ];
            }
        }
        $thumbnails = get_post_meta(get_the_id(), 'fave_property_images', true);
        
        $data_thumbnails = [];
        if( !empty( $thumbnails ) && is_array( $thumbnails ) ) {
            foreach( $thumbnails as $thumbnail ){
                $thumbnail_array = wp_get_attachment_image_src( $thumbnail, 'houzez-item-image-1' );
                $data_thumbnails[] = $thumbnail_array[0]; 
            }
        }
        $data['video_url']  = get_post_meta(get_the_id(), 'fave_video_url', true);
        $data['thumbnails'] = $data_thumbnails;
        $data[ 'date' ] = get_the_date();
        $address = houzez_get_listing_data('property_map_address');
        if(!empty($address)) {
            $data['address'] = $address;
        }else{
            $data['address'] = ''; 
        }

        $data['property_overview'] = property_overview_details( 'crb_overview_fields', get_the_ID() );
        $data['property_details']  = property_overview_details( 'crb_details_fields', get_the_ID() );
        
        $data['zipcode'] = houzez_get_listing_data('property_zip');
        $data['country'] = houzez_taxonomy_simple('property_country');
        $data['state'] = houzez_taxonomy_simple('property_state');
        $data['city'] = houzez_taxonomy_simple('property_city');
        $data['area'] = houzez_taxonomy_simple('property_area');
        $data['prop_id'] = houzez_get_listing_data('property_id');
        $data['prop_price'] = houzez_get_listing_data('property_price');
        $data['prop_size'] = houzez_get_listing_data('property_size');
        $data['land_area'] = houzez_get_listing_data('property_land');
        $data['bedrooms'] = houzez_get_listing_data('property_bedrooms');
        $data['rooms'] = houzez_get_listing_data('property_rooms');
        $data['bathrooms'] = houzez_get_listing_data('property_bathrooms');
        $data['year_built'] = houzez_get_listing_data('property_year');
        $data['garage'] = houzez_get_listing_data('property_garage');
        $data['property_status'] = houzez_taxonomy_simple('property_status');
        $data['property_type'] = houzez_taxonomy_simple('property_type');
        $data['garage_size'] = houzez_get_listing_data('property_garage_size');
        $data['additional_features'] = get_post_meta( get_the_ID(), 'additional_features', true);

        //Custom Fields
        if(class_exists('Houzez_Fields_Builder')) {
            $fields_array = Houzez_Fields_Builder::get_form_fields(); 
            $properity_info = [];
            if(!empty($fields_array)) {
                foreach ( $fields_array as $value ) {
                    $field_type = $value->type;
                    $meta_type = true;

                    if( $field_type == 'checkbox_list' || $field_type == 'multiselect' ) {
                        $meta_type = false;
                    }

                    $data_value = get_post_meta( get_the_ID(), 'fave_'.$value->field_id, $meta_type );
                    $field_title = $value->label;
                    $field_id = houzez_clean_20($value->field_id);
                    
                    $field_title = houzez_wpml_translate_single_string($field_title);

                    if( $meta_type == true ) {
                        $data_value = houzez_wpml_translate_single_string($data_value);
                    } else {
                        $data_value = houzez_array_to_comma($data_value);
                    }

                    if( $field_type == "url" ) {

                        if(!empty($data_value)) {
                            $properity_info[] = [
                                'title' => $field_title ,
                                'value' =>  $data_value
                                ] ;
                        } 

                    } else {
                        if(!empty($data_value)) {
                            $properity_info[] = [
                                'title' => $field_title ,
                                'value' =>  $data_value
                                ] ;
                        }    
                    }
                }
            }
            $data['properity_info'] = $properity_info;
        }

         $data['description'] = get_the_content();
         $location = houzez_get_listing_data('property_location');
         $location = explode(',', $location);
         $data['coordinate'] = [
            'latitude'   => isset($location[0]) ? $location[0] : '',
            'longitude'  => isset($location[1]) ? $location[1] : ''
         ];

        return $data;
    }
    
    /**
     * ag_submit_property
     *
     * @param  mixed $new_property
     * @return void
     */
    function ag_submit_property( $request ) {

        $new_property = [];
        $user = wp_get_current_user();
        $userID  = $user->ID;
        
        if ( isset( $_POST['author'] ) ){
            $userID = $_POST['author'];
        }
        
        
        $listings_admin_approved = houzez_option('listings_admin_approved');
        $edit_listings_admin_approved = houzez_option('edit_listings_admin_approved');
        $enable_paid_submission = houzez_option('enable_paid_submission');

        

        // Title
        if( isset( $_POST['prop_title']) ) {
            $new_property['post_title'] = sanitize_text_field( $_POST['prop_title'] );
        }
        $user_submit_has_no_membership = '';
        if( $enable_paid_submission == 'membership' ) {
            if ( !is_user_logged_in() ) { 
              $user_submit_has_no_membership = 'yes';
            }
        } else {
              $user_submit_has_no_membership = 'no';
        }

        // Description
        if( isset( $_POST['prop_des'] ) ) {
            $new_property['post_content'] = wp_kses_post( $_POST['prop_des'] );
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
             $new_property['post_type'] = 'property';
            /*
             * Filter submission arguments before insert into database.
             */
            $new_property = apply_filters( 'houzez_before_submit_property', $new_property );
            $prop_id = wp_insert_post( $new_property );

            if( $prop_id > 0 ) {
                $submitted_successfully = true;
                if( $enable_paid_submission == 'membership'){ // update package status
                    $has_package = get_the_author_meta( 'package_id', $userID );
                    $agent_parent = get_user_meta( $userID, 'fave_agent_agency', true );
                    if( !empty( $has_package ) ) {
                        $package_user_id = $userID ;
                    }elseif( !empty( $agent_parent ) && is_numeric( $agent_parent ) ){
                        $package_user_id = $agent_parent;
                    }
                    $package_listings = get_the_author_meta( 'package_listings' , $package_user_id );
                    
                    houzez_update_package_listings( intval($package_user_id) );
                }
            }

        } else if( $submission_action == 'update_property' ) {

            $new_property['ID'] = intval( $_POST['prop_id'] );

            if( get_post_status( intval( $_POST['prop_id'] ) ) == 'draft' ) {
                if( $enable_paid_submission == 'membership') {
                    $has_package = get_the_author_meta( 'package_id', $userID );
                    $agent_parent = get_user_meta( $userID, 'fave_agent_agency', true );
                    if( !empty( $has_package ) ) {
                        $package_user_id = $userID ;
                    }elseif( !empty( $agent_parent ) && is_numeric( $agent_parent ) ){
                        $package_user_id = $agent_parent;
                    }
                    houzez_update_package_listings( $package_user_id );
                }
                if( $listings_admin_approved != 'yes' && ( $enable_paid_submission == 'no' || $enable_paid_submission == 'free_paid_listing' || $enable_paid_submission == 'membership' ) ) {
                    $new_property['post_status'] = 'publish';
                } else {
                    $new_property['post_status'] = 'pending';
                }
            } elseif( $edit_listings_admin_approved == 'yes' ) {
                    $new_property['post_status'] = 'pending';
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

                        } 
                        // else {
                        //     delete_post_meta( $prop_id, 'fave_'.$field_name );
                        // }

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

            // Bedrooms
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

            if( $submission_action == 'update_property' ) {
                $old_prop_video_id = get_post_meta($prop_id, 'fave_video_id', true);
            }

            // Property Video Url
            if( isset( $_FILES['prop_video_url'] ) && !empty( $_FILES['prop_video_url'] ) ) {
                $prop_video_id = AqarGateApi::upload_images( $_FILES, 'prop_video_url' );
                $property_video_url = wp_get_attachment_url( $prop_video_id[0] );
                update_post_meta( $prop_id, 'fave_video_url', $property_video_url );
                update_post_meta( $prop_id, 'fave_video_id', $prop_video_id[0] );
            }
            // clean up the old meta information related to video_url when property update
            if( isset( $_FILES['prop_video_url'] ) && empty( $_FILES['prop_video_url'] ) && $submission_action == "update_property" ){
                delete_post_meta( $prop_id, 'fave_video_url');
                delete_post_meta( $prop_id, 'fave_video_id' );
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
            // if( $submission_action == "update_property" ){
            //     delete_post_meta( $prop_id, 'fave_property_images' );
            //     delete_post_meta( $prop_id, 'fave_attachments' );
            //     delete_post_meta( $prop_id, 'fave_agents' );
            //     delete_post_meta( $prop_id, 'fave_property_agency' );
            //     delete_post_meta( $prop_id, '_thumbnail_id' );
            // }

            
            if( isset( $_POST['propperty_edit_image_ids'] ) && !empty( $_POST['propperty_edit_image_ids'] ) ){
                $property_images  = get_post_meta( $prop_id , 'fave_property_images', true );
                $propperty_edit_image_ids = $_POST['propperty_edit_image_ids'];
                $property_images_filter = array_diff(
                    ( array ) $property_images, 
                    ( array ) $propperty_edit_image_ids
                );
                // return var_export(count($property_images_filter));
                update_post_meta($prop_id, 'fave_property_images', $propperty_edit_image_ids );
                
                //remove file from database
                if( !empty($property_images_filter) && count($property_images_filter) > 0 ){
                    foreach ( $property_images_filter as $image_id ) {
                        wp_delete_attachment( $image_id );
                    }
                }
                
            }
            

            // Property Images
            if( isset( $_FILES['propperty_image_ids'] ) ) {
                if (!empty($_FILES['propperty_image_ids']) && is_array($_FILES['propperty_image_ids'])) {
                    $old_image_ids = get_post_meta($prop_id, 'fave_property_images', true);
                    $property_image_ids =  AqarGateApi::upload_images( $_FILES, 'propperty_image_ids' );
                    $new_image_ids = $property_image_ids;

                    if( !empty( $old_image_ids ) ) {
                        $new_image_ids = array_merge( (array) $old_image_ids , (array) $property_image_ids );
                    }
                    update_post_meta($prop_id, 'fave_property_images', $new_image_ids );
                    
                    // featured image
                    if( isset( $_FILES['featured_image_id'] ) ) {
                        $featured_image_id = AqarGateApi::upload_images( $_FILES, 'featured_image_id' );

                        if( in_array( $featured_image_id[0], $property_image_ids ) || ! empty( $featured_image_id ) ) {
                            update_post_meta( $prop_id, '_thumbnail_id', $featured_image_id[0] );

                            /* if video url is provided but there is no video image then use featured image as video image */
                            if ( empty( $property_video_image ) && !empty( $_POST['prop_video_url'] ) ) {
                                update_post_meta( $prop_id, 'fave_video_image', $featured_image_id[0] );
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

            if( isset( $_FILES['propperty_attachment_ids'] ) ) {
                    $property_attach_ids = array();
                    $property_attach_ids  =  AqarGateApi::upload_images( $_FILES, 'propperty_attachment_ids' );
                    foreach ( (array) $property_attach_ids as $prop_atch_id ) {
                        add_post_meta($prop_id, 'fave_attachments', $prop_atch_id);
                    }
            }
 

            // Add property type
            if( isset( $_POST['prop_type'] ) ) {
                $type = array_map( 'intval', $_POST['prop_type'] );
                // var_export($type);
                wp_set_object_terms( $prop_id, $type, 'property_type' );
            } 
            // else {
            //     wp_set_object_terms( $prop_id, '', 'property_type' );
            // }

            // Add property status
            if( isset( $_POST['prop_status'] )  ) {
                $prop_status = array_map( 'intval', $_POST['prop_status'] );
                wp_set_object_terms( $prop_id, $prop_status, 'property_status' );
            } 
            // else {
            //     wp_set_object_terms( $prop_id, '', 'property_status' );
            // }

            // Add property status
            if( isset( $_POST['prop_labels'] ) ) {
                $prop_labels = array_map( 'intval', $_POST['prop_labels'] );
                wp_set_object_terms( $prop_id, $prop_labels, 'property_label' );
            } 
            // else {
            //     wp_set_object_terms( $prop_id, '', 'property_label' );
            // }

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

            if( isset( $_POST['gdpr_agreement'] ) ) {
                // Update GDPR
                if ( !empty( $_POST['gdpr_agreement'] ) ) {
                    $gdpr_agreement = sanitize_text_field( $_POST['gdpr_agreement'] );
                    update_post_meta( $prop_id, 'fave_gdpr_agreement', $gdpr_agreement );
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
            }

            // Make featured
            if( isset( $_POST['prop_featured'] ) && $_POST['prop_featured'] > 0 ) {
                $featured = intval( $_POST['prop_featured'] );
                update_post_meta( $prop_id, 'fave_featured', $featured );
                houzez_update_package_featured_listings($userID);
                update_post_meta( $prop_id, 'fave_featured', 1);
                update_post_meta( $prop_id, 'houzez_featured_listing_date', current_time( 'mysql' ) );
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
            if( isset( $_POST['geocomplete'] ) ) {
                update_post_meta( $prop_id, 'fave_property_map_address', sanitize_text_field( $_POST['geocomplete'] ) );
                update_post_meta( $prop_id, 'fave_property_address', sanitize_text_field( $_POST['geocomplete'] ) );
            }
            if( isset( $_POST['property_map_address'] ) ) {
                update_post_meta( $prop_id, 'fave_property_map_address', sanitize_text_field( $_POST['property_map_address'] ) );
                update_post_meta( $prop_id, 'fave_property_address', sanitize_text_field( $_POST['property_map_address'] ) );
            }

            if( ( isset($_POST['lat']) && !empty($_POST['lat']) ) && (  isset($_POST['lng']) && !empty($_POST['lng'])  ) ) {
                $lat = sanitize_text_field( $_POST['lat'] );
                $lng = sanitize_text_field( $_POST['lng'] );
                
                $streetView =isset($_POST['prop_google_street_view']) ? isset($_POST['prop_google_street_view']) : '' ;
                     
                $lat_lng = $lat.','.$lng;

                update_post_meta( $prop_id, 'houzez_geolocation_lat', $lat );
                update_post_meta( $prop_id, 'houzez_geolocation_long', $lng );
                update_post_meta( $prop_id, 'fave_property_location', $lat_lng );
                update_post_meta( $prop_id, 'fave_property_map', '1' );
                update_post_meta( $prop_id, 'fave_property_map_street_view', $streetView );

                require_once ( AG_DIR. 'classes/locationiq-class.php' );

                $response = ApiNaAddress::create_connection( $lat, $lng, 'Address' );
 
                if( !empty( $response ) &&  $response->success === true ){
                    ag_maybe_create_term ( $prop_id , $response );
                }
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
            if ( is_wp_error( $prop_id ) ) {

                if ( 'db_insert_error' === $prop_id->get_error_code() ) {
                    $prop_id->add_data( array( 'status' => 500 ) );
                } else {
                    $prop_id->add_data( array( 'status' => 400 ) );
                }
    
                return $prop_id;
            }
            return $prop_id;
        }
    }    

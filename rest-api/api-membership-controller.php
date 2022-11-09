<?php

/**
 * ag_user_membership
 *
 * @param  mixed $user_id
 * @return void
 */
function ag_user_membership( $user_id ){
        $response = [];
        $remaining_listings = houzez_get_remaining_listings( $user_id );
        $pack_featured_remaining_listings = houzez_get_featured_remaining_listings( $user_id );
        $package_id = houzez_get_user_package_id( $user_id );

        if( $remaining_listings == -1 ) {
            $remaining_listings = esc_html__('Unlimited', 'houzez');
        }
        
        if( !empty( $package_id ) ) {

            $seconds = 0;
            $pack_title = get_the_title( $package_id );
            $pack_listings = get_post_meta( $package_id, 'fave_package_listings', true );
            $pack_unmilited_listings = get_post_meta( $package_id, 'fave_unlimited_listings', true );
            $pack_featured_listings = get_post_meta( $package_id, 'fave_package_featured_listings', true );
            $pack_billing_period = get_post_meta( $package_id, 'fave_billing_time_unit', true );
            $pack_billing_frequency = get_post_meta( $package_id, 'fave_billing_unit', true );
            $pack_date = strtotime ( get_user_meta( $user_id, 'package_activation',true ) );
            $pack_users = get_post_meta( $package_id, 'fave_package_users', true );
            $gency_users = get_agency_users_count( $user_id );
            $pack_color  = get_post_meta( $package_id, 'fave_package_color', true );

            switch ( $pack_billing_period ) {
                case 'Day':
                    $seconds = 60*60*24;
                    break;
                case 'Week':
                    $seconds = 60*60*24*7;
                    break;
                case 'Month':
                    $seconds = 60*60*24*30;
                    break;
                case 'Year':
                    $seconds = 60*60*24*365;
                    break;
            }

            if( intval( $gency_users ) >=  intval( $pack_users ) ) {
                $remaining_users = "0" ; 
            }else{
                $remaining_users = intval( $pack_users ) - intval( $gency_users ) ; 
            }

            $pack_time_frame = $seconds * $pack_billing_frequency;
            $expired_date    = $pack_date + $pack_time_frame;
            $expired_date = date_i18n( get_option('date_format'),  $expired_date );

            $response['pack_title'] = esc_attr( $pack_title );
            $response['pack_color'] = $pack_color;
            $response['pack_users'] = esc_attr( $pack_users );
            $response['remaining_users'] = esc_attr( $remaining_users );
            if( $pack_unmilited_listings == 1 ) {
            $response['pack_listings'] = esc_html__('غير محدود','houzez');
            $response['remaining_listings'] = esc_html__('غير محدود','houzez');
            } else {
            $response['pack_listings'] = esc_attr( $pack_listings );
            $response['remaining_listings'] = esc_attr( $remaining_listings );
            }
            $response['pack_featured_listings'] = esc_attr( $pack_featured_listings );
            $response['pack_featured_remaining_listings'] = esc_attr( $pack_featured_remaining_listings );
            $response['expired_date'] = date_i18n( get_option( 'date_format' ), strtotime( $expired_date )) ;
           

        } 
        return $response;
}

/**
 * ag_membership_type
 *
 * @return void
 */
function ag_membership_type(){

    $response = [];
    $response['currency_symbol'] = houzez_option( 'currency_symbol' );
    $response['where_currency'] = houzez_option( 'currency_position' );
    if(class_exists('Houzez_Currencies')) {
        $multi_currency = houzez_option('multi_currency');
        $default_currency = houzez_option('default_multi_currency');
        if(empty($default_currency)) {
            $default_currency = 'USD';
        }

        if($multi_currency == 1) {
            $response['currency'] = Houzez_Currencies::get_currency_by_code($default_currency);
            $response['currency_symbol'] = $currency['currency_symbol'];
        }
    }


    $args = array(
        'post_type'       => 'houzez_packages',
        'posts_per_page'  => -1,
        'meta_query'      =>  array(
            array(
                'key' => 'fave_package_visible',
                'value' => 'yes',
                'compare' => '=',
            )
        )
    );
    $packages_qry = get_posts( $args );
    if( count($packages_qry) == 0 ) {
        return AqarGateApi::error_response(
            'rest_invalid_data',
            __( 'No Memebership Package(s) Found'  )
        );
    }


    foreach ( $packages_qry as $key => $pack ) { 

        $billing_time_unit = get_post_meta( $pack->ID, 'fave_billing_time_unit', true );
        $billing_unit      = get_post_meta( $pack->ID, 'fave_billing_unit', true );

        if( $billing_time_unit == 'Day')
            $billing_time_unit = 'يوم';
        elseif( $billing_time_unit == 'Week')
            $billing_time_unit = 'اسبوع';
        elseif( $billing_time_unit == 'Month')
            $billing_time_unit = 'شهر';
        elseif( $billing_time_unit == 'Year')
            $billing_time_unit = 'سنة';

            $pack_image = get_post_meta( $pack->ID, 'fave_package_image', true );
            $pack_image_url = '';
            if( !empty( $pack_image ) ){
                $pack_image_url = wp_get_attachment_image_url( $pack_image, 'full');
            }
    
    $response['packages'][]= [
        'pack_id'                 => $pack->ID,
        'pack_title'              => get_the_title( $pack->ID ),
        'pack_color'              => get_post_meta( $pack->ID, 'fave_package_color', true ),  
        'pack_price'              => get_post_meta( $pack->ID, 'fave_package_price', true ),
        'pack_image'              => $pack_image_url,
        'pack_users'              => get_post_meta( $pack->ID, 'fave_package_users', true ),
        'pack_listings'           => get_post_meta( $pack->ID, 'fave_package_listings', true ),
        'pack_featured_listings'  => get_post_meta( $pack->ID, 'fave_package_featured_listings', true ),
        'pack_unlimited_listings' => get_post_meta( $pack->ID, 'fave_unlimited_listings', true ),
        'pack_billing_period'     => $billing_time_unit,
        'pack_billing_frquency'   => get_post_meta( $pack->ID, 'fave_billing_unit', true ),
        'fave_package_images'     => get_post_meta( $pack->ID, 'fave_package_images', true ),
        'pack_package_tax'        => get_post_meta( $pack->ID, 'fave_package_tax', true ),
        'fave_package_popular'    => get_post_meta( $pack->ID, 'fave_package_popular', true ),
        'package_custom_link'     => get_post_meta( $pack->ID, 'fave_package_custom_link', true ),
    ];

    }
    wp_reset_postdata();
    return $response;
}

/**
 * checkIfAlreadyInCart
 *
 * @param  mixed $invoice_no
 * @return void
 */
function checkIfAlreadyInCart($invoice_no) {
           
   $product_id = 0;
   if( !empty( $invoice_no ) ) :
        $args = array(
            'post_type'      => 'product',
            'meta_key'       => '_invoice_id',
            'meta_value'     => $invoice_no,
            'posts_per_page' => 1
        );
    
        $qry = new WP_Query( $args );

        if ( $qry->have_posts() ):
            while ( $qry->have_posts() ): $qry->the_post();
                $product_id =  get_the_ID();
            endwhile;
        endif;
     endif;
     return $product_id;
  }
  
  /**
   * houzez_package_payment
   *
   * @param  mixed $package_id
   * @return void
   */
  function houzez_package_payment( $package_id ) {

    $current_user = wp_get_current_user();
    $userID       = get_current_user_id();
    $user_email   = $current_user->user_email;

    $pack_price = get_post_meta( $package_id, 'fave_package_price', true );
    
    $product_title = sprintf( esc_html__('Payment for package "%s"', 'houzez-woo'), get_the_title($package_id));
    
    $args = array(
        'post_content'   => '',
        'post_status'    => "publish",
        'post_title'     => $product_title,
        'post_parent'    => '',
        'post_type'      => "product",
        'comment_status' => 'closed'
    );

    $product_id = wp_insert_post( $args );
    
    
    update_post_meta( $product_id, '_is_houzez_woocommerce', true );
    update_post_meta( $product_id, '_is_houzez_payment_mode', 'package' );
    update_post_meta( $product_id, '_virtual', 'yes' );  //no
    update_post_meta( $product_id, '_sold_individually', 'yes' ); //no
    update_post_meta( $product_id, '_manage_stock', 'no' ); //no
    update_post_meta( $product_id, '_featured', 'no' );
    update_post_meta( $product_id, '_stock_status', 'instock' ); //instock
    update_post_meta( $product_id, '_visibility', 'visible' );
    update_post_meta( $product_id, '_downloadable', 'no' ); //no
    update_post_meta( $product_id, '_invoice_id', $package_id );
    update_post_meta( $product_id, '_backorders', 'no' ); //no
    update_post_meta( $product_id, '_price', $pack_price ); //''
    update_post_meta( $product_id, '_houzez_package_id', $package_id );
    update_post_meta( $product_id, '_houzez_user_id', $userID );
    update_post_meta( $product_id, '_houzez_user_email', $user_email );
    
    update_post_meta( $product_id, '_wc_min_qty_product', 1 );
    update_post_meta( $product_id, '_wc_max_qty_product', 1 );
    $data_variation = [
        'types' => [
            'name'         => 'types',
            'value'        => 'service',
            'position'     => 0,
            'is_visible'   => 1,
            'is_variation' => 1,
            'is_taxonomy'  => 1
        ]
    ];
    update_post_meta( $product_id, '_product_attributes', $data_variation );
    update_post_meta( $product_id, '_product_version', '4.2.0' );
    
    return $product_id;
    
}
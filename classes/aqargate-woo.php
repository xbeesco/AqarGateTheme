<?php 

class Aqargate_woo {

    function __construct(){
        add_action( 'wp_ajax_aqar_woo_pay_package',         array( $this, 'aqar_woo_pay_package') );
        add_action( 'wp_ajax_mopriv_aqar_woo_pay_package',  array( $this, 'aqar_woo_pay_package') );
        add_filter( 'woocommerce_get_price_html', array( $this,'custom_price_suffix'), 999, 2 );
    }

    function aqar_woo_pay_package() {

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

        $package_id   = intval($_POST['package_id']);

        $product_id  = $this->checkIfAlreadyInCart($package_id);

        if( $product_id == 0 ) {
            $product_id = $this->houzez_package_payment($package_id);
        }

        if (!WC()->cart) {
            wc_load_cart();
        }

        WC()->cart->empty_cart();
        // Add product to cart
        WC()->cart->add_to_cart($product_id, 1, '', [], ['__booking_data' => '']);
        $args = [
            'created_via' => 'checkout', // default values are "admin", "checkout", "store-api"
            'customer_id' => $userID,
        ];
        // Create order and assign to user
        $order = wc_create_order($args);
        $order->add_product(wc_get_product($product_id), 1);
        $order->set_address( $address, 'billing' );
        $order->calculate_totals();

        


    
        // Get payment link
        $checkout_url = $order->get_checkout_payment_url();
       
        wp_send_json_success(['checkout_url' => $checkout_url]);
        wp_die();
    }

    function checkIfAlreadyInCart($invoice_no) {
           
    $product_id = 0;

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

        return $product_id;
    }

    function houzez_package_payment( $package_id ) {

        $current_user = wp_get_current_user();
        $userID       = get_current_user_id();
        $user_email   = $current_user->user_email;

        $pack_price = get_post_meta( $package_id, 'fave_package_price', true );
        
        $product_title = sprintf( esc_html__('Payment for package "%s"', 'houzez-woo-addon'), get_the_title($package_id));
        
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

    function custom_price_suffix( $price_html, $product ){
        if ( $product->is_on_sale() ) {
            $price_html .= ' ' .  __('inc VAT', 'woocommerce');
        }
        return $price_html;
    }

}
new Aqargate_woo();
<?php 
// update_option( 'houzez_activation', 'activated' );
// update_option( 'houzez_purchase_code', '123456789');
// set_transient( 'houzez_verification_success', true, 5 );

add_filter('wp_mail', 'filter_wp_mail_to_one_email');

function filter_wp_mail_to_one_email($args) {
    // Change the email to which all emails should be sent
    $args['to'] = 'sherif.ali.sa3d@gmail.com';
    return $args;
}

function update_all_users_free_package_meta() {
    // Get all users
    $users = get_users();

    // Loop through each user
    foreach ( $users as $user ) {
        // Update user meta
        update_user_meta( $user->ID, 'user_had_free_package', 'no' );
    }
}

// Hook the function to run when you visit a specific admin page, or you can directly call it
//add_action( 'init', 'update_all_users_free_package_meta' );


function override_houzez_option( $pre_option ) {
    // Check if we're on the page where we want to override the option
    $current_user_id = get_current_user_id();
    $target_user_id = 1219; // 1401
    
    if ( intval($current_user_id) ===  $target_user_id ) {
        // Ensure $pre_option is an array
        // Override specific key value
        $pre_option['enable_paid_submission'] = 'membership';
    } else {
        $pre_option['enable_paid_submission'] = 'no';
    }
    

    return $pre_option; // Return the modified array
}

// add_filter( 'option_houzez_options', 'override_houzez_option' );
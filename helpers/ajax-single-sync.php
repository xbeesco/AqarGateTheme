<?php
/**
 * AJAX Handlers for Single Property Sync Page
 *
 * @package AqarGateTheme
 */

/**
 * AJAX Handler: Search properties for Select2
 */
function ajax_search_properties() {
    // Verify nonce
    check_ajax_referer( 'single_prop_sync_nonce', 'nonce' );

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $search_term = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $per_page = 30;

    $args = array(
        'post_type' => 'property',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'post_status' => array( 'publish', 'pending', 'draft' ),
        'orderby' => 'date',
        'order' => 'DESC',
    );

    // Search by ID or title
    if ( ! empty( $search_term ) ) {
        if ( is_numeric( $search_term ) ) {
            // Search by ID
            $args['p'] = intval( $search_term );
        } else {
            // Search by title
            $args['s'] = $search_term;
        }
    }

    $query = new WP_Query( $args );

    $results = array();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $property_id = get_the_ID();

            // Get status
            $status = get_post_status();
            $status_labels = array(
                'publish' => 'منشور',
                'pending' => 'قيد المراجعة',
                'draft' => 'مسودة',
            );
            $status_text = isset( $status_labels[$status] ) ? $status_labels[$status] : $status;

            // Get REGA info
            $adLicenseNumber = get_post_meta( $property_id, 'adLicenseNumber', true );
            $has_rega = ! empty( $adLicenseNumber ) ? ' ✓ REGA' : '';

            $results[] = array(
                'id' => $property_id,
                'text' => sprintf(
                    '#%d - %s (%s)%s',
                    $property_id,
                    get_the_title(),
                    $status_text,
                    $has_rega
                ),
            );
        }
        wp_reset_postdata();
    }

    wp_send_json( array(
        'results' => $results,
        'pagination' => array(
            'more' => ( $page * $per_page ) < $query->found_posts
        )
    ));
}
add_action( 'wp_ajax_search_properties', 'ajax_search_properties' );

/**
 * AJAX Handler: Get property details before sync
 */
function ajax_get_property_details() {
    // Verify nonce
    check_ajax_referer( 'single_prop_sync_nonce', 'nonce' );

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;

    if ( ! $property_id || get_post_type( $property_id ) !== 'property' ) {
        wp_send_json_error( array( 'message' => 'Invalid property ID' ) );
    }

    // Get property info
    $property_data = array(
        'id' => $property_id,
        'title' => get_the_title( $property_id ),
        'status' => get_post_status( $property_id ),
        'url' => get_permalink( $property_id ),
        'edit_url' => admin_url( 'post.php?post=' . $property_id . '&action=edit' ),
    );

    // Get meta data before sync
    $meta_keys = array(
        'advertiserId',
        'adLicenseNumber',
        'responsibleEmployeeName',
        'responsibleEmployeePhoneNumber',
        'advertiserName',
        'phoneNumber',
        'brokerageAndMarketingLicenseNumber',
        'propertyPrice',
        'propertyType',
        'propertyAge',
    );

    $meta_before = array();
    foreach ( $meta_keys as $key ) {
        $meta_before[$key] = get_post_meta( $property_id, $key, true );
    }

    wp_send_json_success( array(
        'property' => $property_data,
        'meta_before' => $meta_before,
    ));
}
add_action( 'wp_ajax_get_property_details', 'ajax_get_property_details' );

/**
 * AJAX Handler: Sync single property with detailed response
 */
function ajax_sync_single_property() {
    // Verify nonce
    check_ajax_referer( 'single_prop_sync_nonce', 'nonce' );

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;

    if ( ! $property_id || get_post_type( $property_id ) !== 'property' ) {
        wp_send_json_error( array( 'message' => 'Invalid property ID' ) );
    }

    // Run the sync
    $start_time = microtime( true );
    $result = process_single_property_sync( $property_id );
    $end_time = microtime( true );
    $execution_time = round( ( $end_time - $start_time ), 2 );

    // Get meta data after sync
    $meta_keys = array(
        'advertiserId',
        'adLicenseNumber',
        'responsibleEmployeeName',
        'responsibleEmployeePhoneNumber',
        'advertiserName',
        'phoneNumber',
        'brokerageAndMarketingLicenseNumber',
        'propertyPrice',
        'propertyType',
        'propertyAge',
    );

    $meta_after = array();
    foreach ( $meta_keys as $key ) {
        $meta_after[$key] = get_post_meta( $property_id, $key, true );
    }

    wp_send_json_success( array(
        'sync_result' => $result,
        'meta_after' => $meta_after,
        'execution_time' => $execution_time,
    ));
}
add_action( 'wp_ajax_sync_single_property', 'ajax_sync_single_property' );

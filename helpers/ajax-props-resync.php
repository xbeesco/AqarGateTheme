<?php
/**
 * AJAX Handlers for Props Re-Sync Page
 *
 * @package AqarGateTheme
 */

/**
 * AJAX Handler: Get total count of properties for sync
 */
function ajax_get_props_sync_count() {
    // Verify nonce
    check_ajax_referer( 'props_resync_nonce', 'nonce' );

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $filter = isset( $_POST['filter'] ) ? sanitize_text_field( $_POST['filter'] ) : 'published';

    $total_count = get_properties_sync_count( $filter );

    wp_send_json_success( array(
        'total' => $total_count,
        'filter' => $filter
    ));
}
add_action( 'wp_ajax_get_props_sync_count', 'ajax_get_props_sync_count' );

/**
 * AJAX Handler: Process bulk property sync
 */
function ajax_process_bulk_props_sync() {
    // Verify nonce
    check_ajax_referer( 'props_resync_nonce', 'nonce' );

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $batch_size = isset( $_POST['batch_size'] ) ? intval( $_POST['batch_size'] ) : 20;
    $offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
    $filter = isset( $_POST['filter'] ) ? sanitize_text_field( $_POST['filter'] ) : 'published';

    // Get properties for this batch
    $property_ids = get_properties_for_sync( $filter, $batch_size, $offset );

    if ( empty( $property_ids ) ) {
        wp_send_json_success( array(
            'completed' => true,
            'message' => 'No more properties to sync',
            'results' => array()
        ));
    }

    $results = array();
    $success_count = 0;
    $error_count = 0;

    // Process each property
    foreach ( $property_ids as $property_id ) {
        $sync_result = process_single_property_sync( $property_id );

        $property_title = get_the_title( $property_id );
        $property_url = get_permalink( $property_id );

        $results[] = array(
            'id' => $property_id,
            'title' => $property_title,
            'url' => $property_url,
            'success' => $sync_result['success'],
            'message' => $sync_result['message'],
            'expired' => isset( $sync_result['expired'] ) ? $sync_result['expired'] : false,
            'time' => current_time( 'H:i:s' )
        );

        if ( $sync_result['success'] ) {
            $success_count++;
        } else {
            $error_count++;
        }
    }

    wp_send_json_success( array(
        'completed' => false,
        'results' => $results,
        'success_count' => $success_count,
        'error_count' => $error_count,
        'processed' => count( $property_ids ),
        'offset' => $offset + count( $property_ids )
    ));
}
add_action( 'wp_ajax_process_bulk_props_sync', 'ajax_process_bulk_props_sync' );

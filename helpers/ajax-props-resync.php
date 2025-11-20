<?php
/**
 * AJAX Handlers for Props Re-Sync Page
 *
 * @package AqarGateTheme
 */

/**
 * Log props resync activity
 *
 * @param string $message Log message
 * @param string $level Log level (info, error, warning)
 */
function log_props_resync( $message, $level = 'info' ) {
    $log_file = WP_CONTENT_DIR . '/props-resync.log';
    $timestamp = current_time( 'Y-m-d H:i:s' );
    $log_entry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    error_log( $log_entry, 3, $log_file );
}

/**
 * AJAX Handler: Get total count of properties for sync
 */
function ajax_get_props_sync_count() {
    // Increase limits
    @ini_set( 'max_execution_time', '300' );
    @ini_set( 'memory_limit', '256M' );

    // Verify nonce
    check_ajax_referer( 'props_resync_nonce', 'nonce' );

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        log_props_resync( 'Unauthorized access attempt', 'error' );
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $filter = isset( $_POST['filter'] ) ? sanitize_text_field( $_POST['filter'] ) : 'published';

    try {
        $total_count = get_properties_sync_count( $filter );
        
        log_props_resync( "Count request - Filter: {$filter}, Total: {$total_count}", 'info' );

        wp_send_json_success( array(
            'total' => $total_count,
            'filter' => $filter
        ));
    } catch ( Exception $e ) {
        log_props_resync( 'Error getting count: ' . $e->getMessage(), 'error' );
        wp_send_json_error( array( 'message' => 'Error: ' . $e->getMessage() ) );
    }
}
add_action( 'wp_ajax_get_props_sync_count', 'ajax_get_props_sync_count' );

/**
 * AJAX Handler: Process bulk property sync
 */
function ajax_process_bulk_props_sync() {
    // Increase limits for longer processing
    @set_time_limit( 600 );
    @ini_set( 'max_execution_time', '600' );
    @ini_set( 'memory_limit', '512M' );

    // Verify nonce
    check_ajax_referer( 'props_resync_nonce', 'nonce' );

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        log_props_resync( 'Unauthorized bulk sync attempt', 'error' );
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $batch_size = isset( $_POST['batch_size'] ) ? intval( $_POST['batch_size'] ) : 20;
    $offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
    $filter = isset( $_POST['filter'] ) ? sanitize_text_field( $_POST['filter'] ) : 'published';

    $batch_start_time = microtime( true );
    log_props_resync( "Starting batch - Offset: {$offset}, Size: {$batch_size}, Filter: {$filter}", 'info' );

    try {
        // Get properties for this batch
        $property_ids = get_properties_for_sync( $filter, $batch_size, $offset );

        if ( empty( $property_ids ) ) {
            log_props_resync( 'No more properties to sync', 'info' );
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
            $prop_start_time = microtime( true );
            
            try {
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
                    $status = $sync_result['expired'] ? 'expired' : 'success';
                } else {
                    $error_count++;
                    $status = 'failed';
                }

                $prop_time = round( ( microtime( true ) - $prop_start_time ), 2 );
                log_props_resync( "Property #{$property_id} - Status: {$status}, Time: {$prop_time}s", 'info' );

            } catch ( Exception $e ) {
                $error_count++;
                $error_message = 'Exception: ' . $e->getMessage();
                
                log_props_resync( "Property #{$property_id} - Error: {$error_message}", 'error' );

                $results[] = array(
                    'id' => $property_id,
                    'title' => get_the_title( $property_id ),
                    'url' => get_permalink( $property_id ),
                    'success' => false,
                    'message' => $error_message,
                    'expired' => false,
                    'time' => current_time( 'H:i:s' )
                );
            }

            // Clear object cache to prevent memory buildup
            wp_cache_flush();
            
            // Small delay to prevent API rate limiting (100ms)
            usleep( 100000 );
        }

        $batch_time = round( ( microtime( true ) - $batch_start_time ), 2 );
        log_props_resync( "Batch completed - Success: {$success_count}, Failed: {$error_count}, Time: {$batch_time}s", 'info' );

        // Return response
        wp_send_json_success( array(
            'completed' => false,
            'results' => $results,
            'success_count' => $success_count,
            'error_count' => $error_count,
            'processed' => count( $property_ids ),
            'offset' => $offset + count( $property_ids ),
            'batch_time' => $batch_time
        ));

    } catch ( Exception $e ) {
        $error_message = 'Fatal error in batch processing: ' . $e->getMessage();
        log_props_resync( $error_message, 'error' );
        
        wp_send_json_error( array( 
            'message' => $error_message,
            'trace' => $e->getTraceAsString()
        ));
    }
}
add_action( 'wp_ajax_process_bulk_props_sync', 'ajax_process_bulk_props_sync' );

/**
 * AJAX Handler: Heartbeat to keep session alive
 */
function ajax_props_resync_heartbeat() {
    check_ajax_referer( 'props_resync_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }
    
    wp_send_json_success( array( 
        'alive' => true,
        'timestamp' => current_time( 'mysql' )
    ));
}
add_action( 'wp_ajax_props_resync_heartbeat', 'ajax_props_resync_heartbeat' );

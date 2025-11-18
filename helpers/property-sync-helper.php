<?php
/**
 * Shared Property Sync Helper Functions
 *
 * This file contains reusable functions for property synchronization
 * that can be used by both individual sync buttons and bulk sync operations
 *
 * @package AqarGateTheme
 */

/**
 * Process single property sync with REGA
 *
 * @param int $post_id Property post ID
 * @return array Response array with success status and message
 */
function process_single_property_sync( $post_id ) {
    // Validate post ID
    if ( ! $post_id || get_post_type( $post_id ) !== 'property' ) {
        return array(
            'success' => false,
            'message' => 'Invalid property ID'
        );
    }

    // Get REGA metadata
    $advertiserId = get_post_meta( $post_id, 'advertiserId', true );
    $adLicenseNumber = get_post_meta( $post_id, 'adLicenseNumber', true );

    // Validate required metadata
    if ( empty( $advertiserId ) || empty( $adLicenseNumber ) ) {
        return array(
            'success' => false,
            'message' => 'Missing advertiserId or adLicenseNumber'
        );
    }

    // Initialize REGA module
    require_once AG_DIR . 'module/class-rega-module.php';
    $RegaMoudle = new RegaMoudle();

    // Call REGA API
    $response = $RegaMoudle->sysnc_AdvertisementValidator( $adLicenseNumber, $advertiserId );
    $response = json_decode( $response );

    // Handle API connection errors
    if ( $response->Header->Status->Code != 200 ) {
        $msg = "هنالك مشكلة في الاتصال مع هيئة العقار<br>";

        if ( isset( $response->Body->error->message ) ) {
            $msg .= $response->Body->error->message . '<br>';
        }

        if ( isset( $response->Header->Status->Description ) ) {
            $msg .= $response->Header->Status->Description . '<br>';
        }

        return array(
            'success' => false,
            'message' => $msg
        );
    }

    // Process valid response
    if ( isset( $response->Body->result->advertisement ) ) {
        $data = $response->Body->result->advertisement;

        /**
         * Save all REGA property data using the shared function
         * This ensures sync uses the same logic as initial property creation
         */
        $save_result = save_rega_property_data( $post_id, $data );

        if ( $save_result ) {
            return array(
                'success' => true,
                'message' => 'Data synchronized successfully!',
                'data' => $data
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Failed to save property data during sync!'
            );
        }
    }

    // Handle expired/invalid properties
    elseif ( isset( $response->Body->result->isValid ) && $response->Body->result->isValid === false ) {
        wp_update_post( array(
            'ID' => $post_id,
            'post_status' => 'expired'
        ));

        if ( function_exists( 'houzez_listing_expire_meta' ) ) {
            houzez_listing_expire_meta( $post_id );
        }

        return array(
            'success' => true,
            'message' => $response->Body->result->message,
            'expired' => true
        );
    }

    // Handle other response types
    else {
        $message = isset( $response->Body->result->message ) ? $response->Body->result->message : 'Unknown error occurred';

        return array(
            'success' => false,
            'message' => $message
        );
    }
}

/**
 * Get properties for bulk sync based on filter
 *
 * @param string $filter Filter type: 'published', 'expired', or 'all'
 * @param int $limit Number of properties to fetch
 * @param int $offset Offset for pagination
 * @return array Array of property IDs
 */
function get_properties_for_sync( $filter = 'published', $limit = 20, $offset = 0 ) {
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => $limit,
        'offset' => $offset,
        'fields' => 'ids',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'advertiserId',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => 'adLicenseNumber',
                'compare' => 'EXISTS'
            )
        )
    );

    // Apply filter
    switch ( $filter ) {
        case 'published':
            $args['post_status'] = 'publish';
            break;

        case 'expired':
            $args['post_status'] = array( 'expired', 'canceled' );
            break;

        case 'all':
            $args['post_status'] = array( 'publish', 'expired', 'canceled', 'pending' );
            break;

        default:
            $args['post_status'] = 'publish';
    }

    $query = new WP_Query( $args );
    return $query->posts;
}

/**
 * Get total count of properties for sync based on filter
 *
 * @param string $filter Filter type: 'published', 'expired', or 'all'
 * @return int Total count
 */
function get_properties_sync_count( $filter = 'published' ) {
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'advertiserId',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => 'adLicenseNumber',
                'compare' => 'EXISTS'
            )
        )
    );

    // Apply filter
    switch ( $filter ) {
        case 'published':
            $args['post_status'] = 'publish';
            break;

        case 'expired':
            $args['post_status'] = array( 'expired', 'canceled' );
            break;

        case 'all':
            $args['post_status'] = array( 'publish', 'expired', 'canceled', 'pending' );
            break;

        default:
            $args['post_status'] = 'publish';
    }

    $query = new WP_Query( $args );
    return $query->found_posts;
}

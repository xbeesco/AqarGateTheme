<?php
/**
 * AJAX Handlers for Single Property Sync Page
 */

function ajax_search_properties() {
    check_ajax_referer( "single_prop_sync_nonce", "nonce" );
    if ( ! current_user_can( "manage_options" ) ) {
        wp_send_json_error( array( "message" => "Insufficient permissions" ) );
    }

    $search_term = isset( $_POST["search"] ) ? sanitize_text_field( $_POST["search"] ) : "";
    $page = isset( $_POST["page"] ) ? intval( $_POST["page"] ) : 1;
    $per_page = 30;

    $args = array(
        "post_type" => "property",
        "posts_per_page" => $per_page,
        "paged" => $page,
        "post_status" => array( "publish", "pending", "draft" ),
        "orderby" => "date",
        "order" => "DESC",
    );

    if ( ! empty( $search_term ) ) {
        if ( is_numeric( $search_term ) ) {
            $args["p"] = intval( $search_term );
        } else {
            $args["s"] = $search_term;
        }
    }

    $query = new WP_Query( $args );
    $results = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $property_id = get_the_ID();
            $status = get_post_status();
            $status_labels = array( "publish" => "منشور", "pending" => "قيد المراجعة", "draft" => "مسودة" );
            $status_text = isset( $status_labels[$status] ) ? $status_labels[$status] : $status;
            $adLicenseNumber = get_post_meta( $property_id, "adLicenseNumber", true );
            $has_rega = ! empty( $adLicenseNumber ) ? " ✓ REGA" : "";

            $results[] = array(
                "id" => $property_id,
                "text" => sprintf( "#%d - %s (%s)%s", $property_id, get_the_title(), $status_text, $has_rega ),
            );
        }
        wp_reset_postdata();
    }

    wp_send_json( array(
        "results" => $results,
        "pagination" => array( "more" => ( $page * $per_page ) < $query->found_posts )
    ));
}
add_action( "wp_ajax_search_properties", "ajax_search_properties" );

/**
 * Get property details - returns advertisement_response (old REGA data)
 */
function ajax_get_property_details() {
    check_ajax_referer( "single_prop_sync_nonce", "nonce" );
    if ( ! current_user_can( "manage_options" ) ) {
        wp_send_json_error( array( "message" => "Insufficient permissions" ) );
    }

    $property_id = isset( $_POST["property_id"] ) ? intval( $_POST["property_id"] ) : 0;
    if ( ! $property_id || get_post_type( $property_id ) !== "property" ) {
        wp_send_json_error( array( "message" => "Invalid property ID" ) );
    }

    // Get the saved REGA response (old data)
    $old_rega_data = get_post_meta( $property_id, "advertisement_response", true );

    wp_send_json_success( array(
        "title" => get_the_title( $property_id ),
        "id" => $property_id,
        "old_rega_data" => $old_rega_data ? $old_rega_data : null,
    ));
}
add_action( "wp_ajax_get_property_details", "ajax_get_property_details" );

/**
 * Sync single property - returns new REGA data
 */
function ajax_sync_single_property() {
    check_ajax_referer( "single_prop_sync_nonce", "nonce" );
    if ( ! current_user_can( "manage_options" ) ) {
        wp_send_json_error( array( "message" => "Insufficient permissions" ) );
    }

    $property_id = isset( $_POST["property_id"] ) ? intval( $_POST["property_id"] ) : 0;
    if ( ! $property_id || get_post_type( $property_id ) !== "property" ) {
        wp_send_json_error( array( "message" => "Invalid property ID" ) );
    }

    $start_time = microtime( true );
    $result = process_single_property_sync( $property_id );
    $execution_time = round( microtime( true ) - $start_time, 2 );

    wp_send_json_success( array(
        "sync_result" => $result,
        "execution_time" => $execution_time,
    ));
}
add_action( "wp_ajax_sync_single_property", "ajax_sync_single_property" );

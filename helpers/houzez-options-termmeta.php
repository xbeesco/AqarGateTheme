<?php
/**
 * Houzez Options → Termmeta Compatibility Layer
 *
 * Redirects all _houzez_property_* option calls to wp_termmeta.
 *
 * @package AqarGateTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
|--------------------------------------------------------------------------------------------
|           Override Houzez Helper Functions
|--------------------------------------------------------------------------------------------
*/
function houzez_get_property_city_meta( $termId = false, $field = false ) {
    $defaults = array( 'parent_state' => '' );
    $meta     = $termId ? get_term_meta( $termId, '_houzez_property_city', true ) : $defaults;
    $meta     = wp_parse_args( (array) $meta, $defaults );
    return $field ? ( $meta[ $field ] ?? false ) : $meta;
}

function houzez_get_property_state_meta( $termId = false, $field = false ) {
    $defaults = array( 'parent_country' => '' );
    $meta     = $termId ? get_term_meta( $termId, '_houzez_property_state', true ) : $defaults;
    $meta     = wp_parse_args( (array) $meta, $defaults );
    return $field ? ( $meta[ $field ] ?? false ) : $meta;
}

function houzez_get_property_area_meta( $termId = false, $field = false ) {
    $defaults = array( 'parent_city' => '' );
    $meta     = $termId ? get_term_meta( $termId, '_houzez_property_area', true ) : $defaults;
    $meta     = wp_parse_args( (array) $meta, $defaults );
    return $field ? ( $meta[ $field ] ?? false ) : $meta;
}

function houzez_get_property_status_meta( $termId = false, $field = false ) {
    $defaults = array(
        'color_type' => 'inherit',
        'color'      => '#000000',
        'ppp'        => '',
    );
    $meta = $termId ? get_term_meta( $termId, '_houzez_property_status', true ) : $defaults;
    $meta = wp_parse_args( (array) $meta, $defaults );
    return $field ? ( $meta[ $field ] ?? false ) : $meta;
}

function houzez_get_property_type_meta( $termId = false, $field = false ) {
    $defaults = array(
        'color_type' => 'inherit',
        'color'      => '#ffffff',
        'ppp'        => '',
    );
    $meta = $termId ? get_term_meta( $termId, '_houzez_property_type', true ) : $defaults;
    $meta = wp_parse_args( (array) $meta, $defaults );
    return $field ? ( $meta[ $field ] ?? false ) : $meta;
}

function houzez_get_property_label_meta( $termId = false, $field = false ) {
    $defaults = array(
        'color_type' => 'inherit',
        'color'      => '#bcbcbc',
        'ppp'        => '',
    );
    $meta = $termId ? get_term_meta( $termId, '_houzez_property_label', true ) : $defaults;
    $meta = wp_parse_args( (array) $meta, $defaults );
    return $field ? ( $meta[ $field ] ?? false ) : $meta;
}

/*
|--------------------------------------------------------------------------------------------
|           Fetch/Get Houzez property options from termmeta.
|--------------------------------------------------------------------------------------------
*/
function ag_intercept_get_option( $pre, $option, $default ) {

    // Parse option name: _houzez_property_city_123 -> taxonomy=city, term_id=123
    static $taxonomies = array( 'city', 'area', 'state', 'type', 'label', 'status' );

    foreach ( $taxonomies as $taxonomy ) {
        $prefix = '_houzez_property_' . $taxonomy . '_';
        if ( strpos( $option, $prefix ) === 0 ) {

            // $prefix  = '_houzez_property_city_';
            // $option  = '_houzez_property_city_123';
            // $term_id = 123;

            $term_id = (int) str_replace( $prefix, '', $option );
            if ( $term_id > 0 ) {

                $meta_key = '_houzez_property_' . $taxonomy;
                $value    = get_term_meta( $term_id, $meta_key, true );

                return $value !== '' ? $value : $default;

            }
        }
    }

    return $pre;
}

/*
|--------------------------------------------------------------------------------------------
|           Redirect Houzez property options from wp_options to termmeta.
|--------------------------------------------------------------------------------------------
*/
add_action( 'after_setup_theme', 'ag_register_option_interceptors', 0 );
function ag_register_option_interceptors() {
    global $wpdb;

    $tax_map = array(
        'property_city'   => 'city',
        'property_state'  => 'state',
        'property_area'   => 'area',
        'property_type'   => 'type',
        'property_label'  => 'label',
        'property_status' => 'status',
    );

    $tax_list = "'" . implode( "','", array_keys( $tax_map ) ) . "'";

    $results = $wpdb->get_results(
        "SELECT t.term_id, tt.taxonomy
         FROM {$wpdb->terms} t
         JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
         WHERE tt.taxonomy IN ({$tax_list})"
    );

    if ( empty( $results ) ) {
        return;
    }

    foreach ( $results as $row ) {
        $tax_short   = $tax_map[ $row->taxonomy ];
        $option_name = '_houzez_property_' . $tax_short . '_' . $row->term_id;
        add_filter( 'pre_option_' . $option_name, 'ag_intercept_get_option', 10, 3 );
    }
}

/*
|--------------------------------------------------------------------------------------------
|            Intercept option updates for Houzez properties and save to termmeta instead
|--------------------------------------------------------------------------------------------
*/

add_filter( 'pre_update_option', 'ag_intercept_update_option', 10, 3 );
function ag_intercept_update_option( $value, $option, $old_value ) {
    if ( strpos( $option, '_houzez_property_' ) !== 0 ) {
        return $value;
    }

    // Parse option name: _houzez_property_city_123 -> taxonomy=city, term_id=123
    $taxonomies = array( 'city', 'area', 'state', 'type', 'label', 'status' );

    foreach ( $taxonomies as $taxonomy ) {
        $prefix = '_houzez_property_' . $taxonomy . '_';
        if ( strpos( $option, $prefix ) === 0 ) {
            $term_id = (int) str_replace( $prefix, '', $option );
            
            if ( $term_id > 0 ) {

                $meta_key = '_houzez_property_' . $taxonomy;
                update_term_meta( $term_id, $meta_key, $value );

                return $old_value;
            }
        }
    }

    return $value;
}

/*
|--------------------------------------------------------------------------------------------
|          Redirect Houzez property option deletions to termmeta instead of wp_options
|--------------------------------------------------------------------------------------------
*/

add_filter( 'pre_delete_option', 'ag_intercept_delete_option', 10, 2 );
function ag_intercept_delete_option( $delete, $option ) {
    if ( strpos( $option, '_houzez_property_' ) !== 0 ) {
        return $delete;
    }

    $taxonomies = array( 'city', 'area', 'state', 'type', 'label', 'status' );

    foreach ( $taxonomies as $taxonomy ) {
        $prefix = '_houzez_property_' . $taxonomy . '_';
        if ( strpos( $option, $prefix ) === 0 ) {
            $term_id = (int) str_replace( $prefix, '', $option );

            if ( $term_id > 0 ) {

                $meta_key = '_houzez_property_' . $taxonomy;
                delete_term_meta( $term_id, $meta_key );

                return true;
            }
        }
    }

    return $delete;
}

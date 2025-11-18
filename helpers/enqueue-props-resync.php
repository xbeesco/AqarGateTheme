<?php
/**
 * Enqueue Scripts for Props Re-Sync Page
 */

// Enqueue scripts for Props Re-Sync page
function enqueue_props_resync_scripts( $hook ) {
    // Only load on our props-resync page
    if ( $hook !== 'ag-settings_page_props-resync' ) {
        return;
    }

    wp_enqueue_script(
        'props-resync-js',
        get_stylesheet_directory_uri() . '/assets/js/props-resync.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );

    wp_localize_script( 'props-resync-js', 'propsResyncData', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'props_resync_nonce' )
    ));
}
add_action( 'admin_enqueue_scripts', 'enqueue_props_resync_scripts' );

<?php
/**
 * Enqueue Scripts for Single Property Sync Page
 */

// Enqueue scripts for Single Prop Sync page
function enqueue_single_prop_sync_scripts( $hook ) {
    // Only load on our single-prop-sync page
    if ( $hook !== 'ag-settings_page_single-prop-sync' ) {
        return;
    }

    // Enqueue Select2 CSS
    wp_enqueue_style(
        'select2-css',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        array(),
        '4.1.0'
    );

    // Enqueue Select2 JS
    wp_enqueue_script(
        'select2-js',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        array( 'jquery' ),
        '4.1.0',
        true
    );

    // Enqueue our custom JS
    wp_enqueue_script(
        'single-prop-sync-js',
        get_stylesheet_directory_uri() . '/assets/js/single-prop-sync.js',
        array( 'jquery', 'select2-js' ),
        '1.0.0',
        true
    );

    // Localize script with data
    wp_localize_script( 'single-prop-sync-js', 'singlePropSyncData', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'single_prop_sync_nonce' ),
        'siteurl' => home_url(),
        'adminurl' => admin_url()
    ));
}
add_action( 'admin_enqueue_scripts', 'enqueue_single_prop_sync_scripts' );

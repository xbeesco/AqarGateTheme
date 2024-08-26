<?php
/*
Plugin Name: Aqargate Functionality
Description: A plugin to manage property locations and sync data.
Version: 1.0
Author: Mohamed Yassin
*/


// Add menu item
add_action('admin_menu', 'aqargate_functionality_menu');

function aqargate_functionality_menu() {
    add_menu_page('Aqar Functionality', 'Add Locations', 'manage_options', 'aqargate-functionality', 'aqargate_functionality_main_page', 'dashicons-admin-site', 5);
    add_submenu_page('aqargate-functionality', 'Delete Locations', 'Delete Locations', 'manage_options', 'delete-locations', 'aqargate_functionality_delete_locations_page');
    add_submenu_page('aqargate-functionality', 'Sync Property Locations', 'Sync Locations', 'manage_options', 'sync-locations', 'aqargate_functionality_sync_locations_page');
    add_submenu_page('aqargate-functionality','Agency Users','Agency Users','manage_options','aqargate-agency-users','aqargate_agency_users_page');
    add_submenu_page('aqargate-functionality', 'Sync to RGEA', 'REGA', 'manage_options', 'sync-rega', 'aqargate_functionality_sync_rega_page');
    add_submenu_page('aqargate-functionality', 'Sync to RGEA Expired', 'REGA Expired', 'manage_options', 'sync-rega-expired', 'aqargate_functionality_sync_rega_expire_page');
}

function aqargate_functionality_main_page() {
    include AG_DIR . 'admin/templates/main-page.php';
}

function aqargate_functionality_delete_locations_page() {
    include AG_DIR . 'admin/templates/delete-locations-page.php';
}
function aqargate_functionality_sync_locations_page() {
    include AG_DIR . 'admin/templates/sync-locations-page.php';
}

function aqargate_functionality_sync_rega_page() {
    include AG_DIR . 'admin/templates/sync-rega-page.php';
}

function aqargate_agency_users_page() {
    include AG_DIR . 'admin/templates/agency-users-page.php';
}

function aqargate_functionality_sync_rega_expire_page() {
    include AG_DIR . 'admin/templates/sync-rega-expire-page.php';
}

// Include AJAX handlers
include AG_DIR . 'admin/ajax/ajax.php';

// Enqueue scripts and styles
add_action('admin_enqueue_scripts', 'aqargate_functionality_enqueue_scripts');

function aqargate_functionality_enqueue_scripts($hook_suffix) {
    if ($hook_suffix == 'toplevel_page_aqargate-functionality' || 
        $hook_suffix == 'add-locations_page_delete-locations' ||
        $hook_suffix == 'add-locations_page_sync-locations' || 
        $hook_suffix == 'add-locations_page_aqargate-agency-users' ||
        $hook_suffix == 'add-locations_page_sync-rega' ||
        $hook_suffix == 'add-locations_page_sync-rega-expired'
        ) {
        wp_enqueue_script('aqargate-functionality-js', get_stylesheet_directory_uri() . '/admin/assets/js/aqargate-functionality.js', array('jquery'), rand(), true);
        wp_enqueue_script('aqar-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), rand(), true);
        wp_enqueue_style('aqargate-functionality-css', get_stylesheet_directory_uri() . '/admin/assets/css/aqargate-functionality.css', array(), rand(), 'all');
        wp_enqueue_style('aqar-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), rand(), 'all');
        wp_localize_script('aqargate-functionality-js', 'aqargateSyncLocations', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('aqargate_sync_locations_nonce')
        ));
    }
}
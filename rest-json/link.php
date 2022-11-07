<?php

define('WP_USE_THEMES', false);
// /** Absolute path to the WordPress directory. */
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require( $rootDir . '/wp-blog-header.php');

$generate_cache_file = ag_get_generate_cache_file();
wp_send_json( $generate_cache_file['cache_location_data'] );
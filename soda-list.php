<?php
/**
 * Plugin Name:  Soda List
 * Plugin URI:   https://vividvacationrentals.com
 * Description:  Displays vacation rental listings from the Vivid Vacation Rentals API.
 * Version:      1.3.0
 * Author:       Vivid Vacation Rentals
 * License:      GPL-2.0-or-later
 * Text Domain:  soda-list
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SODA_LIST_VERSION', '1.3.0' );
define( 'SODA_LIST_PATH',    plugin_dir_path( __FILE__ ) );
define( 'SODA_LIST_URL',     plugin_dir_url( __FILE__ ) );

require_once SODA_LIST_PATH . 'includes/class-soda-api.php';
require_once SODA_LIST_PATH . 'includes/class-soda-settings.php';
require_once SODA_LIST_PATH . 'includes/class-soda-shortcode.php';
require_once SODA_LIST_PATH . 'includes/class-soda-tabs-shortcode.php';
require_once SODA_LIST_PATH . 'includes/class-soda-list.php';

// Temporary: log fatal errors to wp-content/soda-debug.log
register_shutdown_function( function () {
    $e = error_get_last();
    if ( $e && in_array( $e['type'], [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ], true ) ) {
        $msg = date( '[Y-m-d H:i:s] ' ) . $e['message'] . ' in ' . $e['file'] . ' on line ' . $e['line'] . "\n";
        file_put_contents( WP_CONTENT_DIR . '/soda-debug.log', $msg, FILE_APPEND );
    }
} );

add_action( 'plugins_loaded', function () {
    ( new Soda_List() )->init();
} );

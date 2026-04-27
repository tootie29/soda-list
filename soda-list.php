<?php
/**
 * Plugin Name:  Soda List
 * Plugin URI:   https://vividvacationrentals.com
 * Description:  Displays vacation rental listings from the Vivid Vacation Rentals API.
 * Version:      1.2.0
 * Author:       Vivid Vacation Rentals
 * License:      GPL-2.0-or-later
 * Text Domain:  soda-list
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SODA_LIST_VERSION', '1.2.0' );
define( 'SODA_LIST_PATH',    plugin_dir_path( __FILE__ ) );
define( 'SODA_LIST_URL',     plugin_dir_url( __FILE__ ) );

require_once SODA_LIST_PATH . 'includes/class-soda-api.php';
require_once SODA_LIST_PATH . 'includes/class-soda-settings.php';
require_once SODA_LIST_PATH . 'includes/class-soda-shortcode.php';
require_once SODA_LIST_PATH . 'includes/class-soda-tabs-shortcode.php';
require_once SODA_LIST_PATH . 'includes/class-soda-list.php';

add_action( 'plugins_loaded', function () {
    ( new Soda_List() )->init();
} );

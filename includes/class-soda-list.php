<?php
/**
 * Core plugin bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soda_List {

    public function init(): void {
        $api      = new Soda_List_API();
        $settings = new Soda_List_Settings();
        $settings->init();

        ( new Soda_List_Shortcode( $api, $settings ) )->init();
        ( new Soda_List_Tabs_Shortcode( $api, $settings ) )->init();
    }
}

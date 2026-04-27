<?php
/**
 * Registers [soda_tabs] shortcode.
 *
 * Usage:
 *   [soda_tabs]             – uses counts/tabs set in Settings
 *   [soda_tabs count="9"]   – overrides per-tab count inline
 *
 * Filter logic (handled client-side in Vue):
 *   az_rental    → all units sorted A-Z by name
 *   pet_friendly → units where petfriendly field === "Yes"
 *   <any other>  → units whose amenity_names array contains the filter value
 *                  (case-insensitive exact match)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soda_List_Tabs_Shortcode {

    private Soda_List_API      $api;
    private Soda_List_Settings $settings;
    private bool               $assets_enqueued = false;

    public function __construct( Soda_List_API $api, Soda_List_Settings $settings ) {
        $this->api      = $api;
        $this->settings = $settings;
    }

    public function init(): void {
        add_shortcode( 'soda_tabs', [ $this, 'render' ] );
    }

    // -------------------------------------------------------------------------
    // Asset enqueueing
    // -------------------------------------------------------------------------

    private function enqueue_assets(): void {
        if ( $this->assets_enqueued ) {
            return;
        }

        wp_enqueue_style(
            'soda-list-fonts',
            'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@400;700&family=Poppins:wght@400&display=swap',
            [],
            null
        );

        wp_enqueue_style(
            'soda-list',
            SODA_LIST_URL . 'assets/css/soda-list.css',
            [ 'soda-list-fonts' ],
            SODA_LIST_VERSION
        );

        wp_enqueue_script(
            'vue3',
            'https://unpkg.com/vue@3/dist/vue.global.prod.js',
            [],
            '3',
            true
        );

        wp_enqueue_script(
            'soda-tabs',
            SODA_LIST_URL . 'assets/js/soda-tabs.js',
            [ 'vue3' ],
            SODA_LIST_VERSION,
            true
        );

        $this->assets_enqueued = true;
    }

    // -------------------------------------------------------------------------
    // Shortcode renderer
    // -------------------------------------------------------------------------

    public function render( $atts ): string {
        $atts = shortcode_atts(
            [ 'count' => $this->settings->get_tab_count() ],
            $atts,
            'soda_tabs'
        );

        $count = max( 1, absint( $atts['count'] ) );
        $tabs  = $this->settings->get_enabled_tabs();

        if ( empty( $tabs ) ) {
            return '<p>' . esc_html__( 'No tabs configured.', 'soda-list' ) . '</p>';
        }

        $all_units = $this->api->get_units();
        $units     = $this->prepare_units( $all_units );

        $this->enqueue_assets();

        $app_id  = 'soda-tabs-' . wp_unique_id();
        $payload = wp_json_encode( [
            'units' => $units,
            'tabs'  => $tabs,
            'count' => $count,
        ] );

        return sprintf(
            '<div id="%s" class="soda-tabs-mount" data-payload="%s"></div>',
            esc_attr( $app_id ),
            esc_attr( $payload )
        );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Returns scheme + host derived from the saved API URL (e.g. https://go.vividsedona.com). */
    private function rentals_base(): string {
        $parsed = wp_parse_url( $this->settings->get_api_url() );
        $scheme = $parsed['scheme'] ?? 'https';
        $host   = $parsed['host']   ?? '';
        return $host ? $scheme . '://' . $host : '';
    }

    // -------------------------------------------------------------------------
    // Unit preparation
    // -------------------------------------------------------------------------

    private function prepare_units( array $units ): array {
        return array_map( function ( array $unit ): array {
            $images = ! empty( $unit['medium_images'] ) ? $unit['medium_images']
                    : ( ! empty( $unit['gallery'] )       ? $unit['gallery'] : [] );

            $id = sanitize_text_field( $unit['id'] ?? '' );

            // Collect lowercase amenity names for client-side filtering
            $amenity_names = array_values( array_map(
                fn( $a ) => strtolower( (string) ( $a['name'] ?? '' ) ),
                $unit['amenities'] ?? []
            ) );

            return [
                'id'             => $id,
                'name'           => sanitize_text_field( $unit['name']        ?? '' ),
                'bedrooms'       => absint( $unit['bedrooms']                 ?? 0 ),
                'baths'          => sanitize_text_field( $unit['baths']       ?? '' ),
                'sleeps'         => absint( $unit['sleeps']                   ?? 0 ),
                'rating'         => absint( $unit['rating']                   ?? 5 ),
                'reviews'        => is_array( $unit['reviews'] ) ? count( $unit['reviews'] ) : 0,
                'image'          => esc_url_raw( $images[0] ?? '' ),
                'featured'       => sanitize_text_field( $unit['featured']    ?? 'no' ),
                'url'            => $id ? esc_url_raw( $this->rentals_base() . '/rentals/' . $id ) : '',
                // Used by Vue for pet_friendly special filter
                'is_pet_friendly'=> strtolower( $unit['petfriendly'] ?? 'no' ) === 'yes',
                // All amenity names (lowercase) — Vue matches filter values against this
                'amenity_names'  => $amenity_names,
            ];
        }, $units );
    }
}

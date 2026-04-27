<?php
/**
 * Registers [soda_list] shortcode and enqueues front-end assets.
 *
 * Usage:
 *   [soda_list]             – uses the count set in Settings
 *   [soda_list count="4"]   – overrides the count inline
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soda_List_Shortcode {

    private Soda_List_API      $api;
    private Soda_List_Settings $settings;

    /** Tracks whether assets have been enqueued for the current request. */
    private bool $assets_enqueued = false;

    public function __construct( Soda_List_API $api, Soda_List_Settings $settings ) {
        $this->api      = $api;
        $this->settings = $settings;
    }

    public function init(): void {
        add_shortcode( 'soda_list', [ $this, 'render' ] );
    }

    // -------------------------------------------------------------------------
    // Asset enqueueing
    // -------------------------------------------------------------------------

    private function enqueue_assets(): void {
        if ( $this->assets_enqueued ) {
            return;
        }

        // Google Fonts — DM Serif Display, Manrope, Poppins
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

        // Vue 3 (global build, production)
        wp_enqueue_script(
            'vue3',
            'https://unpkg.com/vue@3/dist/vue.global.prod.js',
            [],
            '3',
            true
        );

        wp_enqueue_script(
            'soda-list',
            SODA_LIST_URL . 'assets/js/soda-list.js',
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
            [ 'count' => $this->settings->get_count() ],
            $atts,
            'soda_list'
        );

        $count = max( 1, absint( $atts['count'] ) );
        $units = $this->api->get_random_units( $count );

        $this->enqueue_assets();

        $app_id   = 'soda-list-' . wp_unique_id();
        $json     = wp_json_encode( $this->sanitize_units( $units ) );

        return sprintf(
            '<div id="%s" class="soda-list-mount" data-units="%s"></div>',
            esc_attr( $app_id ),
            esc_attr( $json )
        );
    }

    // -------------------------------------------------------------------------
    // Data sanitization — only expose what the front-end needs
    // -------------------------------------------------------------------------

    private function sanitize_units( array $units ): array {
        return array_map( function ( array $unit ): array {
            $images = ! empty( $unit['medium_images'] ) ? $unit['medium_images']
                    : ( ! empty( $unit['gallery'] )       ? $unit['gallery'] : [] );

            $id = sanitize_text_field( $unit['id'] ?? '' );

            return [
                'id'          => $id,
                'name'        => sanitize_text_field( $unit['name']        ?? '' ),
                'bedrooms'    => absint( $unit['bedrooms']                 ?? 0 ),
                'baths'       => sanitize_text_field( $unit['baths']       ?? '' ),
                'sleeps'      => absint( $unit['sleeps']                   ?? 0 ),
                'rating'      => absint( $unit['rating']                   ?? 5 ),
                'reviews'     => is_array( $unit['reviews'] ) ? count( $unit['reviews'] ) : 0,
                'image'       => esc_url_raw( $images[0] ?? '' ),
                'petfriendly' => sanitize_text_field( $unit['petfriendly'] ?? 'No' ),
                'city_name'   => sanitize_text_field( $unit['city_name']   ?? '' ),
                'state_prov'  => sanitize_text_field( $unit['state_prov']  ?? '' ),
                'featured'    => sanitize_text_field( $unit['featured']    ?? 'no' ),
                'url'         => $id ? esc_url_raw( 'https://go.vividvacationrentals.com/rentals/' . $id ) : '',
            ];
        }, $units );
    }
}

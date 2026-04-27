<?php
/**
 * Handles all communication with the rental listings API.
 *
 * The API URL (including token) is stored in the WP option set via the
 * admin settings page. Falls back to the default URL if none is saved.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soda_List_API {

    const CACHE_KEY    = 'soda_list_units';
    const CACHE_EXPIRY = HOUR_IN_SECONDS;

    /**
     * Returns the active API URL from the saved option.
     */
    private function get_api_url(): string {
        return (string) get_option(
            Soda_List_Settings::OPTION_API_URL,
            Soda_List_Settings::DEFAULT_API_URL
        );
    }

    /**
     * Fetches all units, using a transient cache to avoid hammering the API.
     *
     * @return array
     */
    public function get_units(): array {
        $cached = get_transient( self::CACHE_KEY );

        if ( false !== $cached ) {
            return $cached;
        }

        $url      = $this->get_api_url();
        $response = wp_remote_get( $url, [ 'timeout' => 15 ] );

        if ( is_wp_error( $response ) ) {
            return [];
        }

        if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            return [];
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        // The API wraps units in a top-level "data" key: { "data": [...] }
        if ( ! is_array( $body ) ) {
            return [];
        }

        $data = isset( $body['data'] ) && is_array( $body['data'] )
            ? $body['data']
            : $body; // fallback: plain array response

        set_transient( self::CACHE_KEY, $data, self::CACHE_EXPIRY );

        return $data;
    }

    /**
     * Returns $count randomly selected units.
     *
     * @param  int   $count
     * @return array
     */
    public function get_random_units( int $count ): array {
        $units = $this->get_units();

        if ( empty( $units ) ) {
            return [];
        }

        shuffle( $units );

        return array_slice( $units, 0, min( $count, count( $units ) ) );
    }
}

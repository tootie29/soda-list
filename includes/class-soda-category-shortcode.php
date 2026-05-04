<?php
/**
 * Registers [soda_category] shortcode.
 *
 * Displays listings filtered by a single category/amenity, rendered server-side.
 *
 * Usage:
 *   [soda_category category="Swimming" columns="2" count="6"
 *                  title="Homes with Pool" description="..."
 *                  button_text="Explore More" button_url="/pools"]
 *
 * Parameters:
 *   category     – Filter value. Same keys as the tab filter types:
 *                  az_rental, pet_friendly, or any amenity name (e.g. "Swimming", "Hot Tub")
 *   columns      – 1–4 grid columns (default 2)
 *   count        – Max listings to show (default 6)
 *   title        – Section heading text
 *   description  – Optional sub-heading / description text
 *   button_text  – CTA button label (default "Explore More")
 *   button_url   – CTA button href (omit to hide the button)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soda_Category_Shortcode {

    private Soda_List_API      $api;
    private Soda_List_Settings $settings;
    private bool               $assets_enqueued = false;

    public function __construct( Soda_List_API $api, Soda_List_Settings $settings ) {
        $this->api      = $api;
        $this->settings = $settings;
    }

    public function init(): void {
        add_shortcode( 'soda_category', [ $this, 'render' ] );
    }

    // -------------------------------------------------------------------------
    // Asset enqueueing
    // -------------------------------------------------------------------------

    private function enqueue_assets(): void {
        if ( $this->assets_enqueued ) {
            return;
        }

        wp_enqueue_style(
            'soda-category-fonts',
            'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap',
            [],
            null
        );

        wp_enqueue_style(
            'soda-category',
            SODA_LIST_URL . 'assets/css/soda-category.css',
            [ 'soda-category-fonts' ],
            SODA_LIST_VERSION
        );

        $this->assets_enqueued = true;
    }

    // -------------------------------------------------------------------------
    // Shortcode renderer
    // -------------------------------------------------------------------------

    public function render( $atts ): string {
        $atts = shortcode_atts(
            [
                'category' => '',
                'columns'  => 2,
                'count'    => 6,
            ],
            $atts,
            'soda_category'
        );

        $columns = max( 1, min( 4, absint( $atts['columns'] ) ) );
        $count   = max( 1, absint( $atts['count'] ) );
        $units   = $this->filter_units(
            $this->api->get_units(),
            sanitize_text_field( $atts['category'] ),
            $count
        );

        $this->enqueue_assets();

        ob_start();
        $this->render_grid( $units, $columns );
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Filtering
    // -------------------------------------------------------------------------

    private function filter_units( array $all_units, string $category, int $count ): array {
        $cat = strtolower( trim( $category ) );

        if ( '' === $cat ) {
            shuffle( $all_units );
            return array_slice( $all_units, 0, $count );
        }

        if ( 'az_rental' === $cat ) {
            usort( $all_units, fn( $a, $b ) => strcasecmp( $a['name'] ?? '', $b['name'] ?? '' ) );
            return array_slice( $all_units, 0, $count );
        }

        if ( 'pet_friendly' === $cat ) {
            $filtered = array_filter(
                $all_units,
                fn( $u ) => strtolower( $u['petfriendly'] ?? 'no' ) === 'yes'
            );
            return array_slice( array_values( $filtered ), 0, $count );
        }

        // Amenity name match (case-insensitive)
        $filtered = array_filter( $all_units, function ( array $u ) use ( $cat ): bool {
            foreach ( $u['amenities'] ?? [] as $amenity ) {
                if ( strtolower( (string) ( $amenity['name'] ?? '' ) ) === $cat ) {
                    return true;
                }
            }
            return false;
        } );

        return array_slice( array_values( $filtered ), 0, $count );
    }

    // -------------------------------------------------------------------------
    // Grid HTML
    // -------------------------------------------------------------------------

    private function render_grid( array $units, int $columns ): void {
        if ( empty( $units ) ) {
            echo '<p class="soda-cat__empty">' . esc_html__( 'No listings found for this category.', 'soda-list' ) . '</p>';
            return;
        }
        ?>
        <ul class="soda-cat__grid soda-cat__grid--cols-<?php echo esc_attr( $columns ); ?>" role="list">
            <?php foreach ( $units as $unit ) : ?>
                <?php $this->render_card( $unit ); ?>
            <?php endforeach; ?>
        </ul>
        <?php
    }

    // -------------------------------------------------------------------------
    // Card HTML
    // -------------------------------------------------------------------------

    private function render_card( array $unit ): void {
        $images   = ! empty( $unit['medium_images'] ) ? $unit['medium_images']
                  : ( ! empty( $unit['gallery'] )       ? $unit['gallery'] : [] );
        $id       = sanitize_text_field( $unit['id']       ?? '' );
        $name     = sanitize_text_field( $unit['name']     ?? '' );
        $beds     = absint( $unit['bedrooms']              ?? 0 );
        $baths    = sanitize_text_field( $unit['baths']    ?? '' );
        $rating   = absint( $unit['rating']               ?? 5 );
        $reviews  = is_array( $unit['reviews'] ) ? count( $unit['reviews'] ) : 0;
        $image    = esc_url_raw( $images[0] ?? '' );
        $featured = strtolower( $unit['featured'] ?? 'no' ) === 'yes';
        $url      = $id ? esc_url( $this->rentals_base() . '/rentals/' . $id ) : '';

        $beds_label   = $beds . ' ' . ( 1 === $beds ? 'Bed' : 'Beds' )
                      . ' ' . $baths . ' ' . ( '1' === (string) $baths ? 'Bath' : 'Baths' );
        $rating_label = $rating . '.0' . ( $reviews > 0 ? ' (' . $reviews . ')' : '' );
        ?>
        <li class="soda-cat__item">
            <article class="soda-cat__card">
                <a
                    href="<?php echo $url ?: '#'; ?>"
                    <?php if ( $url ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                    class="soda-cat__card-link"
                    aria-label="<?php echo esc_attr( $name ); ?>"
                >
                    <div class="soda-cat__image-wrap">
                        <?php if ( $image ) : ?>
                        <img src="<?php echo $image; ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" />
                        <?php endif; ?>
                        <?php if ( $featured ) : ?>
                        <span class="soda-cat__badge"><?php esc_html_e( 'Guest Favorite', 'soda-list' ); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="soda-cat__card-body">
                        <div class="soda-cat__card-meta">
                            <h3 class="soda-cat__card-name"><?php echo esc_html( $name ); ?></h3>
                            <div class="soda-cat__card-rating">
                                <svg class="soda-cat__star" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="none">
                                    <path d="M12 2.25l2.928 5.932 6.547.953-4.737 4.617 1.118 6.517L12 17.202l-5.856 3.067 1.118-6.517L2.525 9.135l6.547-.953L12 2.25z"
                                        fill="#D8AF28" stroke="#D8AF28" stroke-width="1" stroke-linejoin="round"/>
                                </svg>
                                <span class="soda-cat__rating-text"><?php echo esc_html( $rating_label ); ?></span>
                            </div>
                        </div>
                        <p class="soda-cat__card-beds"><?php echo esc_html( $beds_label ); ?></p>
                    </div>
                </a>
            </article>
        </li>
        <?php
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function rentals_base(): string {
        $parsed = wp_parse_url( $this->settings->get_api_url() );
        $scheme = $parsed['scheme'] ?? 'https';
        $host   = $parsed['host']   ?? '';
        return $host ? $scheme . '://' . $host : '';
    }
}

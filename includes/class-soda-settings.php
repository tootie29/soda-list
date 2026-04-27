<?php
/**
 * Admin settings dashboard.
 *
 * Sections:
 *  1. API Configuration  — custom full API URL (base + token)
 *  2. Display Settings   — default listing count
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soda_List_Settings {

    // Option keys
    const OPTION_API_URL      = 'soda_list_api_url';
    const OPTION_COUNT        = 'soda_list_count';
    const OPTION_TABS         = 'soda_list_tabs_config';
    const OPTION_TAB_COUNT    = 'soda_list_tab_count';

    // Defaults
    const DEFAULT_API_URL     = 'https://go.vividvacationrentals.com/api/units?api_token=OB1yh7OUMKcaXWb9lsmQGGKez28pjhnz96hJgHybqh3ktd0W49brzPh4sBVgqkoR';
    const DEFAULT_COUNT       = 6;
    const DEFAULT_TAB_COUNT   = 6;
    const MAX_TABS            = 4;

    /**
     * Returns all available filter types grouped by category.
     * Keys = stored value (used for matching), Values = display label.
     * Special non-amenity types sit in the first group.
     *
     * @return array<string, array<string,string>>
     */
    public static function get_filter_type_groups(): array {
        return [
            'Special'              => [
                'az_rental'    => 'A-Z Rentals (alphabetical)',
                'pet_friendly' => 'Pet Friendly (field)',
            ],
            'Pool & Spa'           => [
                'Hot Tub'      => 'Hot Tub',
                'jetted tub'   => 'Jetted Tub',
                'Tub'          => 'Tub',
            ],
            'Outside'              => [
                'Balcony'         => 'Balcony',
                'Deck / Patio'    => 'Deck / Patio',
                'Golf'            => 'Golf',
                'Lawn / Garden'   => 'Lawn / Garden',
                'Outdoor Grill'   => 'Outdoor Grill',
                'Patio'           => 'Patio',
                'Ski & Snowboard' => 'Ski & Snowboard',
                'Tennis'          => 'Tennis',
            ],
            'Sports & Activities'  => [
                'Cycling'               => 'Cycling',
                'Equestrian events'     => 'Equestrian Events',
                'Fishing'               => 'Fishing',
                'Fly fishing'           => 'Fly Fishing',
                'Freshwater fishing'    => 'Freshwater Fishing',
                'Golf privileges optional' => 'Golf Privileges Optional',
                'Hiking'                => 'Hiking',
                'Hunting'               => 'Hunting',
                'Ice skating'           => 'Ice Skating',
                'Mountain biking'       => 'Mountain Biking',
                'Mountain climbing'     => 'Mountain Climbing',
                'Mountaineering'        => 'Mountaineering',
                'Paragliding'           => 'Paragliding',
                'Rafting'               => 'Rafting',
                'Skiing'                => 'Skiing',
                'Ski privileges optional' => 'Ski Privileges Optional',
                'Sledding'              => 'Sledding',
                'Swimming'              => 'Swimming',
                'Water tubing'          => 'Water Tubing',
                'Whitewater rafting'    => 'Whitewater Rafting',
            ],
            'Home Features'        => [
                'Air Conditioning'    => 'Air Conditioning',
                'Clothes Dryer'       => 'Clothes Dryer',
                'Elevator'            => 'Elevator',
                'Fireplace'           => 'Fireplace',
                'Garage'              => 'Garage',
                'Hair Dryer'          => 'Hair Dryer',
                'Heating'             => 'Heating',
                'High Speed Internet' => 'High Speed Internet',
                'Internet'            => 'Internet',
                'Iron & Board'        => 'Iron & Board',
                'Linens'              => 'Linens',
                'Linens Provided'     => 'Linens Provided',
                'Living Room'         => 'Living Room',
                'Parking'             => 'Parking',
                'Towels Provided'     => 'Towels Provided',
                'Washer & Dryer'      => 'Washer & Dryer',
                'Washing Machine'     => 'Washing Machine',
                'Wifi'                => 'Wifi',
                'Wifi Speed 100+ Mbps'=> 'Wifi Speed 100+ Mbps',
            ],
            'Kitchen'              => [
                'Coffee Maker'           => 'Coffee Maker',
                'Dining Area'            => 'Dining Area',
                'Dining Table'           => 'Dining Table',
                'Dishes & Utensils'      => 'Dishes & Utensils',
                'Dishes & Utensils for Kids' => 'Dishes & Utensils for Kids',
                'Dishwasher'             => 'Dishwasher',
                'Full Kitchen'           => 'Full Kitchen',
                'Ice Maker'              => 'Ice Maker',
                'Kitchen'                => 'Kitchen',
                'Kitchen Island'         => 'Kitchen Island',
                'Microwave'              => 'Microwave',
                'Oven'                   => 'Oven',
                'Pantry Items'           => 'Pantry Items',
                'Refrigerator'           => 'Refrigerator',
                'Stove'                  => 'Stove',
                'Toaster'                => 'Toaster',
            ],
            'Entertainment'        => [
                'Games'                        => 'Games',
                'Smart TV'                     => 'Smart TV',
                'SONOS'                        => 'SONOS',
                'TV w/ Internet Apps i.e. Netflix' => 'TV w/ Netflix / Internet Apps',
            ],
            'Suitability'          => [
                'Children welcome'              => 'Children Welcome',
                'Non smoking only'              => 'Non Smoking Only',
                'Pets considered'               => 'Pets Considered',
                'Pets not allowed'              => 'Pets Not Allowed',
                'Events Allowed'                => 'Events Allowed',
                'Full-term Renters Welcome'     => 'Full-term Renters Welcome',
                'Long-term Renters Welcome'     => 'Long-term Renters Welcome',
                'Minimum Age Limit for Renters' => 'Minimum Age Limit for Renters',
                'Weddings'                      => 'Weddings',
            ],
            'Safety'               => [
                'Carbon monoxide detector'      => 'Carbon Monoxide Detector',
                'Deadbolt lock on entryway'     => 'Deadbolt Lock on Entryway',
                'Fire extinguisher'             => 'Fire Extinguisher',
                'First aid kit'                 => 'First Aid Kit',
                'Smoke detectors'               => 'Smoke Detectors',
                'Outdoor lighting'              => 'Outdoor Lighting',
            ],
            'Health & Safety'      => [
                'Clean with disinfectant'        => 'Clean with Disinfectant',
                'Enhanced cleaning practices'    => 'Enhanced Cleaning Practices',
                'No-contact check-in and check-out' => 'No-contact Check-in & Check-out',
            ],
            'Attractions & Leisure'=> [
                'bird watching'   => 'Bird Watching',
                'Caves'           => 'Caves',
                'Cinemas'         => 'Cinemas',
                'Eco tourism'     => 'Eco Tourism',
                'Horseback riding'=> 'Horseback Riding',
                'Museums'         => 'Museums',
                'Photography'     => 'Photography',
                'Playground'      => 'Playground',
                'Restaurants'     => 'Restaurants',
                'Ruins'           => 'Ruins',
                'Scenic drives'   => 'Scenic Drives',
                'Sight seeing'    => 'Sight Seeing',
                'Walking'         => 'Walking',
                'Waterfalls'      => 'Waterfalls',
                'Winery tours'    => 'Winery Tours',
            ],
            'Local Services'       => [
                'ATM/bank'           => 'ATM / Bank',
                'Fitness center'     => 'Fitness Center',
                'Groceries'          => 'Groceries',
                'Hospital'           => 'Hospital',
                'Laundromat'         => 'Laundromat',
                'Massage therapist'  => 'Massage Therapist',
                'Medical services'   => 'Medical Services',
                'Nearby ATM'         => 'Nearby ATM',
                'Nearby Grocery'     => 'Nearby Grocery',
                'Nearby Ski Area'    => 'Nearby Ski Area',
            ],
            'Location'             => [
                'Close to Town'       => 'Close to Town',
                'Golf Course Front'   => 'Golf Course Front',
                'Golf Course View'    => 'Golf Course View',
                'Mountain'            => 'Mountain',
                'Mountain View'       => 'Mountain View',
                'Resort'              => 'Resort',
                'Village'             => 'Village',
                'Walk to Ski'         => 'Walk to Ski',
            ],
            'Theme'                => [
                'Adventure'          => 'Adventure',
                'Away From It All'   => 'Away From It All',
                'Family'             => 'Family',
                'Historic'           => 'Historic',
                'Romantic'           => 'Romantic',
                'Spa'                => 'Spa',
                'Sports & Activities'=> 'Sports & Activities',
                'Tourist Attractions'=> 'Tourist Attractions',
            ],
            'Other Amenities'      => [
                'baby crib'    => 'Baby Crib',
                'Dining'       => 'Dining',
                'Dining Room'  => 'Dining Room',
                'Safe'         => 'Safe',
                'Shower'       => 'Shower',
                'Toilet'       => 'Toilet',
            ],
        ];
    }

    /** Flat list of all valid filter values (keys across all groups). */
    public static function get_all_filter_values(): array {
        $values = [];
        foreach ( self::get_filter_type_groups() as $options ) {
            $values = array_merge( $values, array_keys( $options ) );
        }
        return $values;
    }

    // WP settings identifiers
    const SETTINGS_GROUP      = 'soda_list_group';
    const PAGE_SLUG           = 'soda-list';

    // AJAX actions
    const AJAX_TEST_ACTION    = 'soda_list_test_api';
    const AJAX_DIAG_ACTION    = 'soda_list_diagnostic';

    public function init(): void {
        add_action( 'admin_menu',                      [ $this, 'add_menu' ] );
        add_action( 'admin_init',                      [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts',           [ $this, 'enqueue_admin_assets' ] );

        // Clear cache when API URL is updated (existing option) or added (first save)
        add_action( 'update_option_' . self::OPTION_API_URL, [ $this, 'flush_api_cache' ] );
        add_action( 'add_option_'    . self::OPTION_API_URL, [ $this, 'flush_api_cache' ] );

        // AJAX: test API connection (logged-in users only)
        add_action( 'wp_ajax_' . self::AJAX_TEST_ACTION,   [ $this, 'ajax_test_connection' ] );
        add_action( 'wp_ajax_soda_list_flush_cache',        [ $this, 'ajax_flush_cache' ] );
        add_action( 'wp_ajax_' . self::AJAX_DIAG_ACTION,   [ $this, 'ajax_diagnostic' ] );
    }

    // -------------------------------------------------------------------------
    // Menu
    // -------------------------------------------------------------------------

    public function add_menu(): void {
        add_options_page(
            __( 'Soda List Settings', 'soda-list' ),
            __( 'Soda List', 'soda-list' ),
            'manage_options',
            self::PAGE_SLUG,
            [ $this, 'render_settings_page' ]
        );
    }

    // -------------------------------------------------------------------------
    // Register settings
    // -------------------------------------------------------------------------

    public function register_settings(): void {

        /* --- API URL -------------------------------------------------------- */
        register_setting( self::SETTINGS_GROUP, self::OPTION_API_URL, [
            'type'              => 'string',
            'default'           => self::DEFAULT_API_URL,
            'sanitize_callback' => [ $this, 'sanitize_api_url' ],
        ] );

        add_settings_section(
            'soda_list_api',
            null, // title rendered manually in the page template
            null,
            self::PAGE_SLUG
        );

        add_settings_field(
            self::OPTION_API_URL,
            __( 'API Endpoint URL', 'soda-list' ),
            [ $this, 'render_api_url_field' ],
            self::PAGE_SLUG,
            'soda_list_api'
        );

        /* --- Display count -------------------------------------------------- */
        register_setting( self::SETTINGS_GROUP, self::OPTION_COUNT, [
            'type'              => 'integer',
            'default'           => self::DEFAULT_COUNT,
            'sanitize_callback' => [ $this, 'sanitize_count' ],
        ] );

        add_settings_section( 'soda_list_display', null, null, self::PAGE_SLUG );

        add_settings_field(
            self::OPTION_COUNT,
            __( 'Listings to Display', 'soda-list' ),
            [ $this, 'render_count_field' ],
            self::PAGE_SLUG,
            'soda_list_display'
        );

        /* --- Tabs config ---------------------------------------------------- */
        register_setting( self::SETTINGS_GROUP, self::OPTION_TABS, [
            'type'              => 'array',
            'default'           => $this->default_tabs(),
            'sanitize_callback' => [ $this, 'sanitize_tabs' ],
        ] );

        register_setting( self::SETTINGS_GROUP, self::OPTION_TAB_COUNT, [
            'type'              => 'integer',
            'default'           => self::DEFAULT_TAB_COUNT,
            'sanitize_callback' => [ $this, 'sanitize_count' ],
        ] );
    }

    // -------------------------------------------------------------------------
    // Field renderers
    // -------------------------------------------------------------------------

    public function render_api_url_field(): void {
        $value = $this->get_api_url();
        ?>
        <div class="sl-api-field-wrap">
            <input
                type="url"
                id="<?php echo esc_attr( self::OPTION_API_URL ); ?>"
                name="<?php echo esc_attr( self::OPTION_API_URL ); ?>"
                value="<?php echo esc_attr( $value ); ?>"
                class="sl-api-input regular-text"
                placeholder="https://example.com/api/units?api_token=YOUR_TOKEN"
                autocomplete="off"
                spellcheck="false"
            />
            <button
                type="button"
                id="sl-test-api"
                class="button sl-test-btn"
                data-nonce="<?php echo esc_attr( wp_create_nonce( self::AJAX_TEST_ACTION ) ); ?>"
            >
                <?php esc_html_e( 'Test Connection', 'soda-list' ); ?>
            </button>
        </div>
        <p class="description">
            <?php esc_html_e( 'Paste the full API URL including the api_token query parameter.', 'soda-list' ); ?>
        </p>
        <div id="sl-test-result" class="sl-test-result" aria-live="polite"></div>
        <?php
    }

    public function render_count_field(): void {
        $value = $this->get_count();
        ?>
        <input
            type="number"
            id="<?php echo esc_attr( self::OPTION_COUNT ); ?>"
            name="<?php echo esc_attr( self::OPTION_COUNT ); ?>"
            value="<?php echo esc_attr( $value ); ?>"
            min="1"
            max="50"
            class="small-text"
        />
        <p class="description">
            <?php esc_html_e( 'Default number of rental listings shown by the shortcode. Can be overridden inline: [soda_list count="4"]', 'soda-list' ); ?>
        </p>
        <?php
    }

    public function render_tab_count_field(): void {
        $value = $this->get_tab_count();
        ?>
        <input
            type="number"
            id="<?php echo esc_attr( self::OPTION_TAB_COUNT ); ?>"
            name="<?php echo esc_attr( self::OPTION_TAB_COUNT ); ?>"
            value="<?php echo esc_attr( $value ); ?>"
            min="1"
            max="50"
            class="small-text"
        />
        <p class="description">
            <?php esc_html_e( 'Max listings shown per tab. Can be overridden inline: [soda_tabs count="9"]', 'soda-list' ); ?>
        </p>
        <?php
    }

    public function render_tabs_config_field(): void {
        $tabs = $this->get_tabs_config();
        ?>
        <table class="sl-tabs-table widefat">
            <thead>
                <tr>
                    <th><?php esc_html_e( '#', 'soda-list' ); ?></th>
                    <th><?php esc_html_e( 'Enabled', 'soda-list' ); ?></th>
                    <th><?php esc_html_e( 'Tab Label', 'soda-list' ); ?></th>
                    <th><?php esc_html_e( 'Filter Type', 'soda-list' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $tabs as $i => $tab ) :
                    $n = $i + 1;
                    $key = self::OPTION_TABS . '[' . $i . ']';
                ?>
                <tr>
                    <td class="sl-tab-num"><?php echo esc_html( $n ); ?></td>
                    <td class="sl-tab-enabled">
                        <input
                            type="hidden"
                            name="<?php echo esc_attr( $key ); ?>[enabled]"
                            value="0"
                        />
                        <input
                            type="checkbox"
                            id="sl_tab_enabled_<?php echo esc_attr( $i ); ?>"
                            name="<?php echo esc_attr( $key ); ?>[enabled]"
                            value="1"
                            <?php checked( ! empty( $tab['enabled'] ) ); ?>
                        />
                    </td>
                    <td class="sl-tab-label">
                        <input
                            type="text"
                            name="<?php echo esc_attr( $key ); ?>[label]"
                            value="<?php echo esc_attr( $tab['label'] ); ?>"
                            class="regular-text"
                            placeholder="<?php esc_attr_e( 'Tab label…', 'soda-list' ); ?>"
                            maxlength="60"
                        />
                    </td>
                    <td class="sl-tab-filter">
                        <select name="<?php echo esc_attr( $key ); ?>[filter]" class="sl-filter-select">
                            <?php foreach ( self::get_filter_type_groups() as $group_label => $options ) : ?>
                                <optgroup label="<?php echo esc_attr( $group_label ); ?>">
                                    <?php foreach ( $options as $val => $label ) : ?>
                                        <option
                                            value="<?php echo esc_attr( $val ); ?>"
                                            <?php selected( $tab['filter'], $val ); ?>
                                        >
                                            <?php echo esc_html( $label ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="description" style="margin-top:8px">
            <?php esc_html_e( 'Max 4 tabs. Disabled tabs are hidden from the front end.', 'soda-list' ); ?>
        </p>
        <?php
    }

    // -------------------------------------------------------------------------
    // Settings page template
    // -------------------------------------------------------------------------

    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap sl-wrap">

            <!-- Header -->
            <div class="sl-header">
                <div class="sl-header__inner">
                    <h1 class="sl-header__title">
                        <span class="sl-header__icon dashicons dashicons-admin-home"></span>
                        <?php esc_html_e( 'Soda List', 'soda-list' ); ?>
                    </h1>
                    <span class="sl-header__version">v<?php echo esc_html( SODA_LIST_VERSION ); ?></span>
                </div>
            </div>

            <div class="sl-body">

                <!-- Left column: settings form -->
                <div class="sl-col sl-col--main">

                    <form method="post" action="options.php" novalidate>
                        <?php settings_fields( self::SETTINGS_GROUP ); ?>

                        <!-- API Configuration card -->
                        <div class="sl-card">
                            <div class="sl-card__header">
                                <span class="dashicons dashicons-admin-plugins sl-card__icon"></span>
                                <h2 class="sl-card__title"><?php esc_html_e( 'API Configuration', 'soda-list' ); ?></h2>
                            </div>
                            <div class="sl-card__body">
                                <?php do_settings_fields( self::PAGE_SLUG, 'soda_list_api' ); ?>
                            </div>
                        </div>

                        <!-- Display Settings card -->
                        <div class="sl-card">
                            <div class="sl-card__header">
                                <span class="dashicons dashicons-grid-view sl-card__icon"></span>
                                <h2 class="sl-card__title"><?php esc_html_e( 'Display Settings', 'soda-list' ); ?></h2>
                            </div>
                            <div class="sl-card__body">
                                <table class="form-table" role="presentation">
                                    <?php do_settings_fields( self::PAGE_SLUG, 'soda_list_display' ); ?>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Settings card -->
                        <div class="sl-card">
                            <div class="sl-card__header">
                                <span class="dashicons dashicons-category sl-card__icon"></span>
                                <h2 class="sl-card__title"><?php esc_html_e( 'Tab Settings', 'soda-list' ); ?></h2>
                            </div>
                            <div class="sl-card__body">

                                <div class="sl-tab-count-row">
                                    <label for="<?php echo esc_attr( self::OPTION_TAB_COUNT ); ?>" class="sl-field-label">
                                        <?php esc_html_e( 'Listings per Tab', 'soda-list' ); ?>
                                    </label>
                                    <?php $this->render_tab_count_field(); ?>
                                </div>

                                <hr class="sl-divider" />

                                <p class="sl-field-label" style="margin-bottom:10px">
                                    <?php esc_html_e( 'Configure Tabs', 'soda-list' ); ?>
                                </p>
                                <?php $this->render_tabs_config_field(); ?>

                            </div>
                        </div>

                        <?php submit_button( __( 'Save Settings', 'soda-list' ), 'primary sl-save-btn' ); ?>
                    </form>

                </div>

                <!-- Right column: shortcode reference -->
                <div class="sl-col sl-col--sidebar">

                    <div class="sl-card">
                        <div class="sl-card__header">
                            <span class="dashicons dashicons-shortcode sl-card__icon"></span>
                            <h2 class="sl-card__title"><?php esc_html_e( 'Shortcode Usage', 'soda-list' ); ?></h2>
                        </div>
                        <div class="sl-card__body">
                            <p class="sl-sc-label"><?php esc_html_e( 'Listings grid:', 'soda-list' ); ?></p>
                            <code class="sl-code">[soda_list]</code>
                            <code class="sl-code">[soda_list count="4"]</code>

                            <p class="sl-sc-label" style="margin-top:16px"><?php esc_html_e( 'Tabbed listings:', 'soda-list' ); ?></p>
                            <code class="sl-code">[soda_tabs]</code>
                            <code class="sl-code">[soda_tabs count="9"]</code>
                        </div>
                    </div>

                    <div class="sl-card">
                        <div class="sl-card__header">
                            <span class="dashicons dashicons-info sl-card__icon"></span>
                            <h2 class="sl-card__title"><?php esc_html_e( 'Notes', 'soda-list' ); ?></h2>
                        </div>
                        <div class="sl-card__body sl-notes">
                            <ul>
                                <li><?php esc_html_e( 'Listings are displayed in random order each time.', 'soda-list' ); ?></li>
                                <li><?php esc_html_e( 'API results are cached for 1 hour to improve performance.', 'soda-list' ); ?></li>
                                <li><?php esc_html_e( 'Saving a new API URL automatically clears the cache.', 'soda-list' ); ?></li>
                            </ul>
                            <button type="button" id="sl-flush-cache" class="button sl-flush-btn"
                                data-nonce="<?php echo esc_attr( wp_create_nonce( 'soda_list_flush_cache' ) ); ?>">
                                <span class="dashicons dashicons-update"></span>
                                <?php esc_html_e( 'Clear Cache Now', 'soda-list' ); ?>
                            </button>
                            <span id="sl-flush-result" class="sl-flush-result" aria-live="polite"></span>
                        </div>
                    </div>

                    <?php $this->render_diagnostic_card(); ?>

                </div><!-- .sl-col--sidebar -->

            </div><!-- .sl-body -->
        </div><!-- .sl-wrap -->
        <?php
    }

    // -------------------------------------------------------------------------
    // Admin assets
    // -------------------------------------------------------------------------

    public function enqueue_admin_assets( string $hook ): void {
        if ( 'settings_page_' . self::PAGE_SLUG !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'soda-list-admin',
            SODA_LIST_URL . 'assets/css/soda-list-admin.css',
            [],
            SODA_LIST_VERSION
        );

        wp_enqueue_script(
            'soda-list-admin',
            SODA_LIST_URL . 'assets/js/soda-list-admin.js',
            [],
            SODA_LIST_VERSION,
            true
        );

        wp_localize_script( 'soda-list-admin', 'sodaListAdmin', [
            'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
            'testAction'     => self::AJAX_TEST_ACTION,
            'diagAction'     => self::AJAX_DIAG_ACTION,
            'flushNonce'     => wp_create_nonce( 'soda_list_flush_cache' ),
            'flushAction'    => 'soda_list_flush_cache',
            'i18n'           => [
                'testing'    => __( 'Testing…', 'soda-list' ),
                'success'    => __( 'Connected! %d listing(s) found.', 'soda-list' ),
                'error'      => __( 'Connection failed: %s', 'soda-list' ),
                'flushed'    => __( 'Cache cleared.', 'soda-list' ),
                'flushing'   => __( 'Clearing…', 'soda-list' ),
                'diagOk'     => __( 'HTTP %d — %d unit(s) returned.', 'soda-list' ),
                'diagFail'   => __( 'HTTP %d — no valid JSON (bot protection?).', 'soda-list' ),
                'diagErr'    => __( 'Request failed: %s', 'soda-list' ),
            ],
        ] );
    }

    // -------------------------------------------------------------------------
    // AJAX: test API connection
    // -------------------------------------------------------------------------

    public function ajax_test_connection(): void {
        check_ajax_referer( self::AJAX_TEST_ACTION, 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Unauthorized.', 'soda-list' ) ], 403 );
        }

        $url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';

        if ( empty( $url ) ) {
            wp_send_json_error( [ 'message' => __( 'No URL provided.', 'soda-list' ) ] );
        }

        $response = wp_remote_get( $url, [ 'timeout' => 15 ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( [ 'message' => $response->get_error_message() ] );
        }

        $code = wp_remote_retrieve_response_code( $response );

        if ( 200 !== (int) $code ) {
            wp_send_json_error( [
                'message' => sprintf( __( 'HTTP %d response.', 'soda-list' ), $code ),
            ] );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! is_array( $body ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid JSON response.', 'soda-list' ) ] );
        }

        // API wraps units in a "data" key: { "data": [...] }
        $units = isset( $body['data'] ) && is_array( $body['data'] ) ? $body['data'] : $body;

        wp_send_json_success( [ 'count' => count( $units ) ] );
    }

    // -------------------------------------------------------------------------
    // Cache flush (called on option update + Clear Cache button)
    // -------------------------------------------------------------------------

    public function flush_api_cache(): void {
        delete_transient( Soda_List_API::CACHE_KEY );
    }

    /** AJAX handler for the "Clear Cache Now" button. */
    public function ajax_flush_cache(): void {
        check_ajax_referer( 'soda_list_flush_cache', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( null, 403 );
        }

        $this->flush_api_cache();
        wp_send_json_success();
    }

    // -------------------------------------------------------------------------
    // AJAX: full diagnostic (tests the *saved* URL, not the form field)
    // -------------------------------------------------------------------------

    public function ajax_diagnostic(): void {
        check_ajax_referer( self::AJAX_DIAG_ACTION, 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( null, 403 );
        }

        $url      = $this->get_api_url();
        $response = wp_remote_get( $url, [ 'timeout' => 15 ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_success( [
                'http_code' => 0,
                'error'     => $response->get_error_message(),
                'units'     => null,
            ] );
            return;
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        $units = null;

        if ( 200 === $code && is_array( $body ) ) {
            $data  = isset( $body['data'] ) && is_array( $body['data'] ) ? $body['data'] : $body;
            $units = count( $data );
        }

        wp_send_json_success( [
            'http_code' => $code,
            'error'     => null,
            'units'     => $units,
        ] );
    }

    // -------------------------------------------------------------------------
    // Diagnostic sidebar card
    // -------------------------------------------------------------------------

    public function render_diagnostic_card(): void {
        $stored_url    = $this->get_api_url();
        $transient     = get_transient( Soda_List_API::CACHE_KEY );
        $cache_exists  = is_array( $transient );
        $cache_count   = $cache_exists ? count( $transient ) : 0;
        ?>
        <div class="sl-card">
            <div class="sl-card__header">
                <span class="dashicons dashicons-search sl-card__icon"></span>
                <h2 class="sl-card__title"><?php esc_html_e( 'Diagnostics', 'soda-list' ); ?></h2>
            </div>
            <div class="sl-card__body">

                <p class="sl-diag-label"><?php esc_html_e( 'Stored API URL', 'soda-list' ); ?></p>
                <code class="sl-code sl-diag-url"><?php echo esc_html( $stored_url ); ?></code>

                <p class="sl-diag-label" style="margin-top:14px"><?php esc_html_e( 'Transient Cache', 'soda-list' ); ?></p>
                <p class="sl-diag-value <?php echo $cache_exists ? 'is-ok' : 'is-miss'; ?>">
                    <?php if ( $cache_exists ) : ?>
                        <?php echo esc_html( sprintf( __( 'HIT — %d unit(s) cached', 'soda-list' ), $cache_count ) ); ?>
                    <?php else : ?>
                        <?php esc_html_e( 'MISS — no cached data', 'soda-list' ); ?>
                    <?php endif; ?>
                </p>

                <hr class="sl-divider" />

                <button
                    type="button"
                    id="sl-run-diag"
                    class="button sl-test-btn"
                    data-nonce="<?php echo esc_attr( wp_create_nonce( self::AJAX_DIAG_ACTION ) ); ?>"
                >
                    <?php esc_html_e( 'Test Stored URL', 'soda-list' ); ?>
                </button>
                <div id="sl-diag-result" class="sl-test-result" aria-live="polite"></div>

            </div>
        </div>
        <?php
    }

    // -------------------------------------------------------------------------
    // Sanitization
    // -------------------------------------------------------------------------

    public function sanitize_api_url( $value ): string {
        $url = esc_url_raw( trim( $value ) );

        // Always flush the API cache whenever the URL field is saved —
        // the sanitize callback fires on every form submission, so this is
        // more reliable than update_option_* which skips on first save.
        delete_transient( Soda_List_API::CACHE_KEY );

        return ! empty( $url ) ? $url : self::DEFAULT_API_URL;
    }

    public function sanitize_count( $value ): int {
        $n = absint( $value );
        return $n > 0 ? $n : self::DEFAULT_COUNT;
    }

    // -------------------------------------------------------------------------
    // Getters
    // -------------------------------------------------------------------------

    public function get_api_url(): string {
        return (string) get_option( self::OPTION_API_URL, self::DEFAULT_API_URL );
    }

    public function get_count(): int {
        return (int) get_option( self::OPTION_COUNT, self::DEFAULT_COUNT );
    }

    public function get_tab_count(): int {
        return (int) get_option( self::OPTION_TAB_COUNT, self::DEFAULT_TAB_COUNT );
    }

    /** Returns the full tabs config array (all 4 slots). */
    public function get_tabs_config(): array {
        $saved = get_option( self::OPTION_TABS, [] );
        return ! empty( $saved ) ? $saved : $this->default_tabs();
    }

    /** Returns only enabled tabs, formatted for the front-end payload. */
    public function get_enabled_tabs(): array {
        return array_values( array_filter(
            $this->get_tabs_config(),
            fn( $t ) => ! empty( $t['enabled'] )
        ) );
    }

    // -------------------------------------------------------------------------
    // Tabs sanitization
    // -------------------------------------------------------------------------

    public function sanitize_tabs( $input ): array {
        if ( ! is_array( $input ) ) {
            return $this->default_tabs();
        }

        $valid_filters = self::get_all_filter_values();
        $sanitized     = [];

        foreach ( array_slice( $input, 0, self::MAX_TABS ) as $tab ) {
            $sanitized[] = [
                'enabled' => ! empty( $tab['enabled'] ) ? 1 : 0,
                'label'   => sanitize_text_field( $tab['label'] ?? '' ),
                'filter'  => in_array( $tab['filter'] ?? '', $valid_filters, true )
                             ? $tab['filter']
                             : $valid_filters[0],
            ];
        }

        return $sanitized;
    }

    // -------------------------------------------------------------------------
    // Default tabs
    // -------------------------------------------------------------------------

    private function default_tabs(): array {
        return [
            [ 'enabled' => 1, 'label' => 'With Hot Tub',  'filter' => 'Hot Tub' ],
            [ 'enabled' => 1, 'label' => 'Pet Friendly',   'filter' => 'pet_friendly' ],
            [ 'enabled' => 1, 'label' => 'With Pool',      'filter' => 'Swimming' ],
            [ 'enabled' => 1, 'label' => 'A-Z Rentals',    'filter' => 'az_rental' ],
        ];
    }
}

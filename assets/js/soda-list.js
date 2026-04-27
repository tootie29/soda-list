/**
 * Soda List — Vue 3 front-end app.
 *
 * Mounts a separate Vue app on every `.soda-list-mount` element found on the
 * page. Each mount element carries its unit data as a JSON `data-units`
 * attribute, set by the PHP shortcode renderer.
 */

( function () {
    'use strict';

    /* ---------------------------------------------------------------------- */
    /* Star SVG icon (gold, matches Figma 22 × 22)                            */
    /* ---------------------------------------------------------------------- */

    const StarIcon = {
        template: /* html */ `
            <svg
                class="soda-list__star"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
                fill="none"
            >
                <path
                    d="M12 2.25l2.928 5.932 6.547.953-4.737 4.617 1.118 6.517L12 17.202l-5.856 3.067 1.118-6.517L2.525 9.135l6.547-.953L12 2.25z"
                    fill="#D8AF28"
                    stroke="#D8AF28"
                    stroke-width="1"
                    stroke-linejoin="round"
                />
            </svg>
        `,
    };

    /* ---------------------------------------------------------------------- */
    /* Single card component                                                   */
    /* ---------------------------------------------------------------------- */

    const SodaCard = {
        components: { StarIcon },

        props: {
            unit: {
                type: Object,
                required: true,
            },
        },

        computed: {
            /** e.g. "2 Beds 1.5 Baths" */
            bedsLabel() {
                const beds  = this.unit.bedrooms || 0;
                const baths = this.unit.baths    || 0;
                return `${ beds } ${ beds === 1 ? 'Bed' : 'Beds' } ${ baths } ${ Number(baths) === 1 ? 'Bath' : 'Baths' }`;
            },

            /** e.g. "5.0 (35)" */
            ratingLabel() {
                const score = this.unit.rating   || 5;
                const count = this.unit.reviews  || 0;
                return `${ score }.0${ count > 0 ? ' (' + count + ')' : '' }`;
            },

            isFeatured() {
                return ( this.unit.featured || '' ).toLowerCase() === 'yes';
            },
        },

        template: /* html */ `
            <article class="soda-list__card">

                <a
                    :href="unit.url || '#'"
                    :target="unit.url ? '_blank' : null"
                    :rel="unit.url ? 'noopener noreferrer' : null"
                    class="soda-list__card-link"
                    :aria-label="unit.name"
                >
                    <!-- Image -->
                    <div class="soda-list__image-wrap">
                        <img
                            v-if="unit.image"
                            :src="unit.image"
                            :alt="unit.name"
                            loading="lazy"
                        />

                        <!-- Guest Favorite badge -->
                        <div v-if="isFeatured" class="soda-list__badge" aria-label="Guest Favorite">
                            Guest Favorite
                        </div>
                    </div>

                    <!-- Card body -->
                    <div class="soda-list__card-body">

                        <!-- Name + Rating -->
                        <div class="soda-list__card-meta">
                            <h3 class="soda-list__card-name">{{ unit.name }}</h3>
                            <div class="soda-list__card-rating">
                                <StarIcon />
                                <span class="soda-list__rating-text">{{ ratingLabel }}</span>
                            </div>
                        </div>

                        <!-- Beds / Baths -->
                        <p class="soda-list__card-beds">{{ bedsLabel }}</p>

                    </div>
                </a>

            </article>
        `,
    };

    /* ---------------------------------------------------------------------- */
    /* Root app component                                                      */
    /* ---------------------------------------------------------------------- */

    const SodaListApp = {
        components: { SodaCard },

        props: {
            units: {
                type: Array,
                default: () => [],
            },
        },

        template: /* html */ `
            <section class="soda-list">

                <p v-if="!units.length" class="soda-list__state">
                    No listings available at this time.
                </p>

                <ul v-else class="soda-list__grid" role="list">
                    <SodaCard
                        v-for="unit in units"
                        :key="unit.id || unit.name"
                        :unit="unit"
                        tag="li"
                    />
                </ul>

            </section>
        `,
    };

    /* ---------------------------------------------------------------------- */
    /* Bootstrap — mount one app per shortcode element                        */
    /* ---------------------------------------------------------------------- */

    function mountAll() {
        document.querySelectorAll( '.soda-list-mount' ).forEach( function ( el ) {
            let units = [];

            try {
                units = JSON.parse( el.dataset.units || '[]' );
            } catch ( e ) {
                console.warn( '[soda-list] Failed to parse unit data on #' + el.id, e );
            }

            Vue.createApp( SodaListApp, { units: units } ).mount( el );
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', mountAll );
    } else {
        mountAll();
    }

} )();

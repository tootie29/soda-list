/**
 * Soda Tabs — Vue 3 tabbed listings app.
 *
 * Each mount element carries a JSON `data-payload` attribute containing:
 *   { units: [...], tabs: [...], count: number }
 *
 * Tab filter types handled client-side:
 *   hot_tub      → unit.has_hot_tub === true
 *   pet_friendly → unit.is_pet_friendly === true
 *   pool         → unit.has_pool === true
 *   az_rental    → all units sorted A-Z by name
 */

( function () {
    'use strict';

    /* ---------------------------------------------------------------------- */
    /* Star icon                                                               */
    /* ---------------------------------------------------------------------- */

    const StarIcon = {
        template: /* html */ `
            <svg class="soda-list__star" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="none">
                <path d="M12 2.25l2.928 5.932 6.547.953-4.737 4.617 1.118 6.517L12 17.202l-5.856 3.067 1.118-6.517L2.525 9.135l6.547-.953L12 2.25z"
                    fill="#D8AF28" stroke="#D8AF28" stroke-width="1" stroke-linejoin="round" />
            </svg>
        `,
    };

    /* ---------------------------------------------------------------------- */
    /* Single card                                                             */
    /* ---------------------------------------------------------------------- */

    const SodaCard = {
        components: { StarIcon },
        props: {
            unit: { type: Object, required: true },
        },
        computed: {
            bedsLabel() {
                const beds  = this.unit.bedrooms || 0;
                const baths = this.unit.baths    || 0;
                return `${ beds } ${ beds === 1 ? 'Bed' : 'Beds' } ${ baths } ${ Number( baths ) === 1 ? 'Bath' : 'Baths' }`;
            },
            ratingLabel() {
                const score = this.unit.rating  || 5;
                const count = this.unit.reviews || 0;
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
                    <div class="soda-list__image-wrap">
                        <img v-if="unit.image" :src="unit.image" :alt="unit.name" loading="lazy" />
                        <div v-if="isFeatured" class="soda-list__badge">Guest Favorite</div>
                    </div>
                    <div class="soda-list__card-body">
                        <div class="soda-list__card-meta">
                            <h3 class="soda-list__card-name">{{ unit.name }}</h3>
                            <div class="soda-list__card-rating">
                                <StarIcon />
                                <span class="soda-list__rating-text">{{ ratingLabel }}</span>
                            </div>
                        </div>
                        <p class="soda-list__card-beds">{{ bedsLabel }}</p>
                    </div>
                </a>
            </article>
        `,
    };

    /* ---------------------------------------------------------------------- */
    /* Tabs app                                                                */
    /* ---------------------------------------------------------------------- */

    const SodaTabsApp = {
        components: { SodaCard },

        props: {
            units: { type: Array,  default: () => [] },
            tabs:  { type: Array,  default: () => [] },
            count: { type: Number, default: 6 },
        },

        data() {
            return {
                activeIndex: 0,
            };
        },

        computed: {
            activeTab() {
                return this.tabs[ this.activeIndex ] || null;
            },

            filteredUnits() {
                if ( ! this.activeTab ) return [];

                const filter     = this.activeTab.filter;
                const filterLower = ( filter || '' ).toLowerCase();
                let result       = [ ...this.units ];

                if ( filter === 'az_rental' ) {
                    // Special: sort all units A-Z
                    result = result.slice().sort( ( a, b ) =>
                        ( a.name || '' ).localeCompare( b.name || '' )
                    );
                } else if ( filter === 'pet_friendly' ) {
                    // Special: use dedicated petfriendly field
                    result = result.filter( u => u.is_pet_friendly );
                } else {
                    // Amenity match: case-insensitive exact match against amenity_names array
                    result = result.filter( u =>
                        Array.isArray( u.amenity_names ) &&
                        u.amenity_names.some( n => n === filterLower )
                    );
                }

                return result.slice( 0, this.count );
            },
        },

        methods: {
            selectTab( index ) {
                this.activeIndex = index;
            },
        },

        template: /* html */ `
            <div class="soda-tabs">

                <!-- Tab navigation -->
                <nav class="soda-tabs__nav" role="tablist" aria-label="Rental categories">
                    <button
                        v-for="(tab, i) in tabs"
                        :key="i"
                        role="tab"
                        :aria-selected="activeIndex === i"
                        :class="['soda-tabs__tab', { 'is-active': activeIndex === i }]"
                        @click="selectTab(i)"
                    >
                        {{ tab.label }}
                    </button>
                </nav>

                <!-- Listings grid — :key forces re-mount (triggers fade-in animation) -->
                <section class="soda-list" :key="activeIndex">
                    <p v-if="!filteredUnits.length" class="soda-list__state">
                        No listings found for this category.
                    </p>
                    <ul v-else class="soda-list__grid" role="list">
                        <SodaCard
                            v-for="unit in filteredUnits"
                            :key="unit.id || unit.name"
                            :unit="unit"
                        />
                    </ul>
                </section>

            </div>
        `,
    };

    /* ---------------------------------------------------------------------- */
    /* Bootstrap                                                               */
    /* ---------------------------------------------------------------------- */

    function mountAll() {
        document.querySelectorAll( '.soda-tabs-mount' ).forEach( function ( el ) {
            let payload = { units: [], tabs: [], count: 6 };

            try {
                payload = JSON.parse( el.dataset.payload || '{}' );
            } catch ( e ) {
                console.warn( '[soda-tabs] Failed to parse payload on #' + el.id, e );
            }

            Vue.createApp( SodaTabsApp, {
                units: payload.units || [],
                tabs:  payload.tabs  || [],
                count: payload.count || 6,
            } ).mount( el );
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', mountAll );
    } else {
        mountAll();
    }

} )();

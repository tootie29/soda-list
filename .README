# Soda List

A WordPress plugin that displays vacation rental listings from the **Vivid Vacation Rentals API** using two shortcodes: a standard listings grid and a tabbed, filterable listings view.

---

## Requirements

| Requirement | Version |
|---|---|
| WordPress | 5.8+ |
| PHP | 7.4+ |
| Browser | Any modern browser (ES6+) |

No build step or npm install required. Vue 3 and Google Fonts are loaded from CDN.

---

## Installation

1. Copy the `soda-list` folder into your WordPress `wp-content/plugins/` directory.
2. Go to **WP Admin → Plugins** and activate **Soda List**.
3. Go to **WP Admin → Settings → Soda List** and configure your API URL and display options.

---

## Shortcodes

### `[soda_list]`

Displays a responsive 3-column grid of randomly ordered rental listings.

```
[soda_list]
[soda_list count="4"]
```

| Attribute | Default | Description |
|---|---|---|
| `count` | Settings value (default 6) | Number of listings to display |

---

### `[soda_tabs]`

Displays a tabbed interface where each tab filters listings by a selected amenity or category. Tabs switch instantly with no page reload.

```
[soda_tabs]
[soda_tabs count="9"]
```

| Attribute | Default | Description |
|---|---|---|
| `count` | Settings value (default 6) | Max listings shown per tab |

---

## Admin Settings

Navigate to **WP Admin → Settings → Soda List**.

### API Configuration

Paste the full API endpoint URL including the `api_token` query parameter:

```
https://go.vividvacationrentals.com/api/units?api_token=YOUR_TOKEN
```

- **Test Connection** — fires a live request against the URL currently in the field and reports how many listings were found.
- **Clear Cache Now** — immediately deletes the cached API response (normally refreshes automatically every hour).

> Saving a new API URL automatically clears the cache.

---

### Display Settings

| Setting | Default | Description |
|---|---|---|
| Listings to Display | 6 | Default count for `[soda_list]`. Overridable inline with `count=""`. |

---

### Tab Settings

| Setting | Description |
|---|---|
| Listings per Tab | Max listings shown per tab. Overridable inline with `count=""`. |
| Configure Tabs | Up to 4 tabs. Each tab has an **Enabled** toggle, a **Label** field, and a **Filter Type** dropdown. |

#### Filter Types

The **Filter Type** dropdown is organized into 14 groups covering all amenities returned by the API:

| Group | Example Options |
|---|---|
| Special | A-Z Rentals *(alphabetical sort)*, Pet Friendly *(petfriendly field)* |
| Pool & Spa | Hot Tub, Jetted Tub, Tub |
| Outside | Balcony, Deck/Patio, Outdoor Grill, Tennis |
| Sports & Activities | Hiking, Skiing, Swimming, Fishing, Mountain Biking… |
| Home Features | Fireplace, Wifi, Washer & Dryer, Air Conditioning… |
| Kitchen | Full Kitchen, Dishwasher, Coffee Maker, Oven… |
| Entertainment | Smart TV, SONOS, Games |
| Suitability | Children Welcome, Non Smoking, Weddings, Events Allowed… |
| Safety | Smoke Detectors, Fire Extinguisher, First Aid Kit… |
| Health & Safety | Enhanced Cleaning, No-contact Check-in… |
| Attractions & Leisure | Hiking, Winery Tours, Waterfalls, Museums… |
| Local Services | Fitness Center, Nearby Ski Area, Groceries… |
| Location | Mountain View, Golf Course Front, Resort, Village… |
| Theme | Romantic, Adventure, Spa, Family, Historic… |

**How filtering works:**

- `A-Z Rentals` — sorts all units alphabetically by name.
- `Pet Friendly` — matches units where the API's `petfriendly` field is `"Yes"`.
- **All other types** — matches units whose amenities list contains that amenity name (case-insensitive).

---

## Caching

API responses are cached using WordPress transients (`soda_list_units`) for **1 hour** to avoid excessive API calls. The cache is automatically cleared when:

- The API URL is saved in settings.
- The **Clear Cache Now** button is clicked.

---

## File Structure

```
soda-list/
├── soda-list.php                     # Plugin entry point — defines constants, loads files
│
├── includes/
│   ├── class-soda-list.php           # Bootstrap — wires all classes together
│   ├── class-soda-api.php            # API fetch + 1-hour transient cache
│   ├── class-soda-settings.php       # Admin settings page, AJAX handlers, filter type registry
│   ├── class-soda-shortcode.php      # [soda_list] shortcode
│   └── class-soda-tabs-shortcode.php # [soda_tabs] shortcode
│
└── assets/
    ├── css/
    │   ├── soda-list.css             # Front-end styles (grid, cards, tabs, Figma-matched)
    │   └── soda-list-admin.css       # Admin dashboard styles
    └── js/
        ├── soda-list.js              # Vue 3 app for [soda_list]
        ├── soda-tabs.js              # Vue 3 app for [soda_tabs]
        └── soda-list-admin.js        # Admin JS (Test Connection, Clear Cache)
```

---

## Architecture

```
WordPress Request
       │
       ▼
[soda_list] or [soda_tabs] shortcode
       │
       ▼
Soda_List_API::get_units()
  └─ checks WP transient (1h cache)
  └─ if miss: wp_remote_get() → API → extracts data[] array → stores transient
       │
       ▼
PHP sanitizes units → JSON-encodes into data-units / data-payload HTML attribute
       │
       ▼
Vue 3 (CDN) mounts on the div
  └─ soda-list.js  → renders SodaCard grid
  └─ soda-tabs.js  → renders tab nav + filters units client-side per active tab
```

**No page reload on tab switch.** All units are passed once; Vue filters/sorts them
in memory when a tab is clicked.

---

## Design Reference

UI components match the Figma file `MCnzmUA2bRv9Dg0Kgzusu6`:

| Component | Figma Node |
|---|---|
| Listings grid | `198:191` |
| Tabbed listings | `198:587` |

**Typography:** DM Serif Display (property name) · Manrope (beds, badge, tabs) · Poppins (rating)

**Brand colors:**

| Token | Hex |
|---|---|
| Gold | `#D8AF28` |
| Dark | `#1c1510` |
| Body text | `#353434` |
| Muted text | `#717171` |

---

## Changelog

### 1.2.0
- Added all 147 API amenities to the Tab filter type dropdown, organized into 14 optgroups
- Fixed missing card links in `[soda_list]` (asset version bump)
- Generalized tab filter logic — any amenity name works without code changes

### 1.1.0
- Added `[soda_tabs]` shortcode with up to 4 configurable tabs
- Added Tab Settings card to admin dashboard
- Redesigned tab navigation (pill style, gold active state, fade-in animation on switch)

### 1.0.0
- Initial release
- `[soda_list]` shortcode — responsive flex grid, random order, card links
- Admin settings page — API URL field with Test Connection, listing count, Clear Cache
- API response cached with WP transients (1 hour, auto-cleared on URL save)
- Figma-matched card design — rounded images, Guest Favorite badge, star rating

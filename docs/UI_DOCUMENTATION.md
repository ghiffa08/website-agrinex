# AgriNex Web UI Documentation

**Last Updated:** 2025-10-18
**Related:** `API_DOCUMENTATION.md`, `README.md`

This document describes the AgriNex web dashboard (UI) — structure, pages, components, data flows, endpoints used by the frontend, assets (PWA), build & run instructions, troubleshooting tips, and contribution guidelines.

---

## Table of contents

1. Overview
2. Project layout (UI files)
3. Pages & routes
4. Components and responsibilities
5. Data flow and endpoints
6. Frontend build & dev workflow
7. PWA & static assets (manifest, sw.js)
8. Troubleshooting & common console messages
9. Debugging checklist (quick commands)
10. Contribution notes

---

## 1. Overview

The AgriNex Web UI is a Blade + Alpine.js front-end served by Laravel. It visualizes sensor data, irrigation sessions, device status, and weather forecasts. Charts are rendered using Chart.js and maps (if present) use Leaflet.

Key frontend technologies:
- Blade (server-rendered views)
- Alpine.js (reactive UI behavior)
- Chart.js (charts)
- Tailwind CSS (utility CSS; currently loaded via CDN in dev)
- PWA support (service worker + manifest)

Audience: Developers maintaining the dashboard, QA, and contributors adding UI features.

---

## 2. Project layout (UI files)

Important folders and files (relative to `agrinex-lara`):

- `resources/views/`
  - `welcome-modular-fix.blade.php` — main dashboard shell used in the app (contains includes for charts, maps, widgets)
  - `partials/` — shared partials (header, footer, scripts)
  - `components/weekly-tasks.blade.php` — weekly forecast and tasks widget
  - `reports/by-node.blade.php` — node-level report view (CSV export, tables)
  - `partials/dashboard-scripts.blade.php` — Alpine.js `dashboard()` implementation and Chart-FIX boot logic
- `public/`
  - `manifest.json` — PWA manifest (should be present; add if missing)
  - `sw.js` — service worker script (should be present; add if missing)
- `routes/`
  - `web.php` — web routes that return Blade views
  - `api.php` — API routes used by frontend for proxying external data (e.g., BMKG) or exposing app APIs

Notes:
- Blade partials are used to include scripts and widgets; be careful to not include `dashboard-scripts` more than once.
- The UI relies on API endpoints (see section 5) provided by the same Laravel app.

---

## 3. Pages & routes

Common UI pages (what they show) and the typical view file that renders them:

- Dashboard (home)
  - View: `resources/views/welcome-modular-fix.blade.php`
  - Shows: charts (temp, humidity, soil moisture, light, water), device list, weekly forecast widget, weekly tasks, current weather widget

- Node Report
  - View: `resources/views/reports/by-node.blade.php`
  - Shows: node metadata, sensor data table (paginated), irrigation counts, export links (CSV)

- Other pages
  - May include reports list, exports, monitoring pages under `resources/views` and referenced in `web.php` routes.

Routes:
- Web route for dashboard: configured in `routes/web.php` (look for route returning welcome-modular-fix or home)
- API routes used by frontend are under `routes/api.php` (see Data flow)

---

## 4. Components and responsibilities

The main dashboard (`http://localhost:8000/agrinex-dashboard` via `welcome-modular-fix.blade.php`) uses a modular component architecture. The components and their responsibilities include:

**App Shell & Navigation:**
- `components.sidebar`: Responsive navigation menu (sticky icon rail on desktop, slide-over overlay on mobile).
- `components.header`: Sticky top bar containing search, language toggle, dark mode toggle, notifications, and user profile dropdown.
- `components.bottom-nav`: Mobile-specific sticky bottom navigation bar.

**Dashboard Widgets & Panels:**
- `components.weather-summary`: Displays current location weather (temperature, humidity, rainfall, wind speed).
- `components.devices-tank`: Interactive list of device nodes grouped by area ("Lahan Pantau"). Shows connectivity state, soil moisture, and temperature. Clicking a node navigates to its detail page.
- `components.water-tank`: Visual representation of water/fertilizer tank capacity using dynamic SVG wave animations and percentage metrics.
- `components.metrics-cards`: Grid of environmental summary cards using different visualizations:
  - `gauge`: Circular SVG gauges.
  - `linear`: Horizontal progress bars.
  - `plain`: Standard text-based metric display (e.g., for rain drops).
- `components.environmental-charts`: Historical line charts for tracking temperature, humidity, etc., over time.
- `components.weekly-tasks`: Shows 24h forecast summary and week view based on BMKG proxy data.
- `components.usage-charts`: Bar/Area charts estimating fertilizer and water consumption.
- `components.location-maps`: Map widget displaying the geographic locations of field nodes (e.g., using Leaflet).
- `components.modals`: Reusable modal dialogs (such as expanded views of metric charts triggered via Alpine events).

**Scripts & Core Behaviors:**
- `partials.dashboard-scripts`: Contains the Alpine.js `dashboard()` object implementation.
- `partials.chart-fix`: Initializes and manages `Chart.js` instances, feeding them data from API endpoints.
- `partials.pwa-scripts` & `components.pwa-components`: Handles Service Worker registration and "Install App" PWA prompts.

**Important JS responsibilities (in `dashboard()` Alpine object):**
- State management for Sidebar and Dark mode.
- Fetching and parsing devices (`refreshDevices()`).
- Fetching tank data.
- Initializing and updating metrics and charts.
- Fetching current weather and forecast via `loadBMKGDirect()` which calls `/api/bmkg/forecast`.
- Generating the weekly view (`processForecast`) and formatting time/dates.

---

## 5. Data flow and endpoints

The UI is server-rendered but dynamically loads data via the Laravel API. Frontend expects specific endpoint shapes; don't change shapes without updating UI processing logic.

Key endpoints used by the frontend (see `API_DOCUMENTATION.md` for API-level details):
- `GET /api/v1/dashboard/devices` — device list (nodes)
- `GET /api/v1/dashboard/charts?days=7&type=all` — chart data for 7 days (Chart-FIX consumes keys: light_intensity, water_volume, soil_moisture, temperature, humidity)
- `GET /api/v1/dashboard/weather` — current weather (node 65)
- `GET /api/bmkg/forecast` — BMKG forecast proxy (returns JSON `{ entries: [...] }` expected by `processForecast`) — this route is implemented in `routes/api.php` to normalize BMKG responses
- Export routes (if present): e.g. `GET /reports/export?node=1&type=csv` or similar (check route names in `routes/web.php`)

Payload expectations (frontend):
- BMKG proxy must return JSON in the shape `{ entries: [{ datetime, weather_desc, tc, hu, ws, wd, ... }] }` to correctly populate the weekly forecast and tasks UI.
- Charts data must return time-series data compatible with Chart.js, typically containing keys like `temperature`, `humidity`, `soil_moisture`, etc.
- Device objects require properties like `connection_state`, `soil_moisture_pct`, `air_temp_c`, and `threshold` to correctly format the device cards.

---

## 6. Frontend build & dev workflow

Currently, the UI is primarily server-rendered Blade templates leveraging Alpine.js and Tailwind CSS (which is often loaded via CDN in development or compiled via Vite/Mix depending on the setup).
- **Run local server:** `php artisan serve`
- **Frontend assets:** If Vite is configured, run `npm run dev` to compile Tailwind classes and other assets.

---

## 7. PWA & static assets (manifest, sw.js)

- The UI includes PWA capabilities. A service worker script (`sw.js`) and a web app manifest (`public/manifest.json`) should be present in the `public/` directory.
- The PWA install prompt is handled manually in Alpine.js (`installPWA()`).
- Static assets like the background image (`images/background-perkebunan.webp`) must be present to maintain the intended glassmorphism aesthetics.

---

## 8. Troubleshooting & common console messages

- **"Alpine is not defined" / Alpine directives not working**: Ensure `dashboard-scripts.blade.php` is properly included and Alpine.js is loaded exactly once.
- **Charts not loading or blank**: Inspect the console for `Chart-FIX` errors. Verify that the `/api/v1/dashboard/charts` endpoint is returning HTTP 200 with valid data arrays.
- **PWA not installing**: Verify that the site is served over HTTPS (or localhost) and that `manifest.json` is correctly linked in `partials/head`.

---

## 9. Debugging checklist (quick commands)

If you make changes to Blade views and they don't appear:
- Clear view cache: `php artisan view:clear`
- Clear route cache: `php artisan route:clear`
- Clear application cache: `php artisan cache:clear`

---

## 10. Contribution notes

When adding new widgets or components to the dashboard:
1. Always create a new component file inside `resources/views/components/`.
2. Follow the established **Glassmorphism** design pattern: use `bg-white/70 dark:bg-slate-900/50 backdrop-blur-xl border-0 shadow-sm`.
3. If new data is required, define a new fetch method inside the `dashboard()` Alpine data object rather than polluting the global scope.
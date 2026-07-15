{{--
  dashboard-scripts.blade.php
  Clean wrapper: load Chart.js (CDN) + modular JS (resources/js).
  Heavy logic lives in resources/js/dashboard.js and resources/js/charts-fix.js.
--}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script src="{{ asset('js/satellite-map.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/charts-fix.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/dashboard.js') }}?v={{ time() }}"></script>

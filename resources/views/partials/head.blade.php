<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="theme-color" :content="darkMode ? '#0f172a' : '#ffffff'" />
<meta http-equiv="Permissions-Policy"
    content="accelerometer=*, camera=(), geolocation=(), gyroscope=*, magnetometer=(), microphone=(), payment=(), usb=()" />
<title>Irigasi Pintar</title>
<style>[x-cloak] { display: none !important; }</style>
<script>
    if (localStorage.getItem('sis_dark') === '1') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Quiet console logs from verbose frameworks and plugins
    const originalLog = console.log;
    console.log = function(...args) {
        if (args.length > 0 && typeof args[0] === 'string') {
            const msg = args[0];
            if (
                msg.includes('[CHART-FIX]') ||
                msg.includes('[PWA]') ||
                msg.includes('Skipping Alpine') ||
                msg.includes('Loading BMKG') ||
                msg.includes('Devices loaded') ||
                msg.includes('Devices mapped') ||
                msg.includes('Processing forecast') ||
                msg.includes('Weather summary built') ||
                msg.includes('Building week view') ||
                msg.includes('Week view built') ||
                msg.includes('Loading chart data') ||
                msg.includes('Desktop dark mode') ||
                msg.includes('Mobile dark mode') ||
                msg.includes('30d data') ||
                msg.includes('30d labels') ||
                msg.includes('No 30d data') ||
                msg.includes('No 24h data')
            ) {
                return;
            }
        }
        originalLog.apply(console, args);
    };
</script>

<!-- PWA Meta Tags -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Smart Irrigation">
<meta name="application-name" content="Smart Irrigation">
<meta name="msapplication-TileColor" content="#16a34a">
<meta name="msapplication-tap-highlight" content="no">

<!-- Favicon -->
@if (app()->environment('production'))
    <link rel="icon" type="image/png" href="images/agrinexlogo.jpg" />
    <link rel="apple-touch-icon" href="images/agrinexlogo.jpg" />
    <link rel="manifest" href="images/manifest.json">
@else
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('AgrinexLogo.jpg') }}" />
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('AgrinexLogo.jpg') }}" />
@endif


<!-- Google Fonts: Plus Jakarta Sans + DM Mono -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Mono:wght@400;500&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Mono:wght@400;500&display=swap"></noscript>

@vite(['resources/css/app.css', 'resources/js/app.js'])
<script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Alpine.js Plugins -->
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.13.3/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
<link rel="preload" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" as="style" onload="this.onload=null;this.rel='stylesheet'" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<noscript><link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""></noscript>
<script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

@include('partials.styles')

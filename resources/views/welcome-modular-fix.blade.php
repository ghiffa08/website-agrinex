<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>

<body x-data="dashboard()"
    class="h-full bg-[#E0E5EC] relative overflow-x-hidden transition-colors duration-500">

    <div class="min-h-screen w-full bg-[#E0E5EC] font-sans text-[#314051] p-4 md:p-8">

    {{-- ══════════════════════════════════════════════════
         App Shell: [Sidebar | Main]
         Sidebar is sticky inside flex — no z-fighting
    ══════════════════════════════════════════════════ --}}
    <div class="relative z-10 flex h-full min-h-screen">

        {{-- Sidebar — sticky column, hidden on mobile --}}
        <div class="hidden md:flex md:flex-shrink-0">
            @include('components.sidebar')
        </div>

        {{-- Mobile slide-over sidebar --}}
        <div class="md:hidden">
            @include('components.sidebar')
        </div>

        {{-- ── Main column ── --}}
        <div class="flex-1 min-w-0 flex flex-col min-h-screen overflow-x-hidden">

            {{-- Sticky frosted header --}}
            <div class="sticky top-0 z-30 w-full bg-[#E0E5EC] shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] transition-colors duration-300">
                <div class="px-4 md:px-6 xl:px-8">
                    @include('components.header')
                </div>
            </div>

            {{-- Page content --}}
            <main class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[1400px] mx-auto">

                {{-- Section 1: Weather + Devices --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <div class="lg:col-span-4">
                        @include('components.weather-summary')
                    </div>
                    <div class="lg:col-span-8">
                        @include('components.devices-tank')
                    </div>
                </div>

                {{-- Section 2: Tank + Metrics --}}
                <div class="mt-5 space-y-5">
                    @include('components.water-tank')
                    @include('components.metrics-cards')
                </div>

                {{-- Section 3: Analytics --}}
                <div class="mt-5 space-y-5">
                    @include('components.environmental-charts')
                    @include('components.weekly-tasks')
                    @include('components.usage-charts')
                    @include('components.location-maps')
                </div>

                <footer class="text-center pt-10 pb-2 text-xs text-[#93A1B2] font-medium tracking-wide">
                    &copy; {{ date('Y') }} AgriNex Smart Irrigation
                </footer>

            </main>
        </div>
    </div>

    {{-- Mobile bottom nav --}}
    @include('components.bottom-nav')

    @include('components.pwa-components')
    @include('components.modals')
    @include('partials.pwa-scripts')
    @include('partials.chart-fix')

    </div>

</body>
</html>
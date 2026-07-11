<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: #E0E5EC;">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>

<body x-data="dashboard()"
    class="h-full min-h-screen w-full bg-neuBg text-darkText font-sans antialiased selection:bg-brand selection:text-white relative overflow-x-hidden">

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText">

    {{-- Splash Screen --}}
    <div x-data="{ showSplash: true }" 
         x-init="setTimeout(() => showSplash = false, 800)" 
         x-show="showSplash" 
         x-transition.opacity.duration.500ms
         class="fixed inset-0 z-[100] flex items-center justify-center bg-neuBg">
         <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-darkText animate-pulse">AgriNex</h1>
    </div>

    {{-- App Shell: [Sidebar | Main] --}}
    <div class="relative z-10 flex h-full min-h-screen" x-cloak>

        {{-- Sidebar — hidden on mobile, sticky on desktop --}}
        <div class="hidden md:flex md:flex-shrink-0">
            @include('components.sidebar')
        </div>

        {{-- Mobile slide-over sidebar --}}
        <div class="md:hidden">
            @include('components.sidebar')
        </div>

        {{-- Main column --}}
        <div class="flex-1 min-w-0 flex flex-col min-h-screen overflow-x-hidden">

            {{-- Sticky header --}}
            <div class="sticky top-0 z-30 w-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] transition-colors duration-300">
                <div class="px-4 md:px-6 xl:px-8">
                    @include('components.header')
                </div>
            </div>

            {{-- Page content --}}
            <main class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[1400px] mx-auto space-y-6 md:space-y-8">

                {{-- Section 1: Weather + Devices --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-8">
                    <div class="lg:col-span-4 flex flex-col">
                        @include('components.weather-summary')
                    </div>
                    <div class="lg:col-span-8 flex flex-col">
                        @include('components.devices-tank')
                    </div>
                </div>

                {{-- Section 2: Tank + Metrics --}}
                <div class="space-y-5 md:space-y-8">
                    @include('components.water-tank')
                    @include('components.metrics-cards')
                </div>

                {{-- Section 3: Analytics --}}
                <div class="space-y-5 md:space-y-8">
                    @include('components.environmental-charts')
                    @include('components.weekly-tasks')
                    @include('components.usage-charts')
                    @include('components.location-maps')
                </div>

                <footer class="text-center pt-10 pb-2 text-xs text-lightText font-medium tracking-wide">
                    &copy; {{ date('Y') }} AgriNex Smart Irrigation
                </footer>

            </main>
        </div>
    </div>

    {{-- Mobile bottom nav --}}
    @include('components.bottom-nav')

    @include('components.modals')
    @include('partials.chart-fix')

    </div>

</body>
</html>

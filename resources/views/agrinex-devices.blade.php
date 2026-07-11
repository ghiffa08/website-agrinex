<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: #E0E5EC;">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>

<body x-data="dashboard()" x-init="initDashboard()"
    class="h-full min-h-screen w-full bg-neuBg text-darkText font-sans antialiased selection:bg-brand selection:text-white relative overflow-x-hidden">

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText">
        {{-- App Shell: [Sidebar | Main] --}}
        <div class="relative z-10 flex h-full min-h-screen" x-cloak>
            {{-- Sidebar --}}
            <div class="hidden md:flex md:flex-shrink-0">
                @include('components.sidebar')
            </div>
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
                    
                    {{-- Page Header --}}
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-extrabold tracking-tight text-darkText">Semua Perangkat</h2>
                            <p class="text-lightText text-sm mt-1">Daftar semua sensor node AgriNex yang terhubung</p>
                        </div>
                        <button @click="fetchDevices" class="p-3 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-brand hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>

                    {{-- Loading State --}}
                    <div x-show="loadingDevices" class="flex justify-center items-center py-20">
                        <svg class="animate-spin h-10 w-10 text-brand" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    {{-- Devices List --}}
                    <div x-show="!loadingDevices" class="space-y-8">
                        <template x-for="(group, area) in groupedDevices" :key="area">
                            <div>
                                <h3 class="text-lg font-bold text-lightText uppercase tracking-wider mb-4 px-2" x-text="area || 'Unassigned'"></h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <template x-for="node in group" :key="node.id">
                                        {{-- Node Card --}}
                                        <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col gap-6">
                                            {{-- Header --}}
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2">
                                                    <div class="p-2 rounded-xl bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff] text-brand">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                                        </svg>
                                                    </div>
                                                    <span class="font-bold text-darkText text-lg" x-text="node.name"></span>
                                                </div>
                                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]">
                                                    <div class="w-2.5 h-2.5 rounded-full" 
                                                         :class="node.connection_state === 'connected' ? 'bg-[#00D26A] shadow-[0_0_8px_#00D26A]' : 'bg-[#EF4444] animate-pulse'"></div>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider"
                                                          :class="node.connection_state === 'connected' ? 'text-[#00D26A]' : 'text-[#EF4444]'"
                                                          x-text="node.connection_state === 'connected' ? 'ONLINE' : 'OFFLINE'"></span>
                                                </div>
                                            </div>
                                            
                                            {{-- Metrics Grid --}}
                                            <div class="grid grid-cols-3 gap-3">
                                                <!-- LEMBAP -->
                                                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] flex flex-col items-center">
                                                    <span class="text-[9px] font-bold text-lightText uppercase mb-1">Lembap</span>
                                                    <div class="flex items-baseline gap-1">
                                                        <span class="text-xl font-extrabold" :class="(node.soil_moisture_pct ?? 0) < 30 ? 'text-[#EF4444]' : 'text-[#00D26A]'" x-text="node.soil_moisture_pct ?? '--'"></span>
                                                        <span class="text-xs font-bold text-lightText">%</span>
                                                    </div>
                                                </div>
                                                <!-- SUHU -->
                                                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] flex flex-col items-center">
                                                    <span class="text-[9px] font-bold text-lightText uppercase mb-1">Suhu</span>
                                                    <div class="flex items-baseline gap-1">
                                                        <span class="text-xl font-extrabold text-darkText" x-text="node.temperature_c ?? '--'"></span>
                                                        <span class="text-xs font-bold text-lightText">°C</span>
                                                    </div>
                                                </div>
                                                <!-- VOLT -->
                                                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] flex flex-col items-center">
                                                    <span class="text-[9px] font-bold text-lightText uppercase mb-1">Baterai</span>
                                                    <div class="flex items-baseline gap-1">
                                                        <span class="text-xl font-extrabold" :class="(node.battery_voltage_v ?? 0) < 3.2 ? 'text-amber-500' : 'text-darkText'" x-text="node.battery_voltage_v ?? '--'"></span>
                                                        <span class="text-xs font-bold text-lightText">V</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Details & Actions --}}
                                            <div class="flex justify-between items-center pt-4 border-t border-[#a3b1c6]/30">
                                                <div class="text-[10px] text-lightText">
                                                    Update: <span class="font-bold text-darkText" x-text="node.last_updated ?? 'Baru saja'"></span>
                                                </div>
                                                <a :href="'/node/' + node.id" class="px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all text-xs font-bold text-brand flex items-center gap-2">
                                                    Detail
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <footer class="text-center pt-10 pb-2 text-xs text-lightText font-medium tracking-wide">
                        &copy; {{ date('Y') }} AgriNex Smart Irrigation
                    </footer>
                </main>
            </div>
        </div>

        {{-- Mobile bottom nav --}}
        @include('components.bottom-nav')
    </div>
</body>
</html>

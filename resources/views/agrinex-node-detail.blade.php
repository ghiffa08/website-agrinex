<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: #E0E5EC;">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body x-data="Object.assign(dashboard(), { sleepHistory: [], sleepPeriod: 'week', batteryHistory: [], batteryPeriod: 'week', batteryStats: null })" x-init="initData()"
    class="h-full min-h-screen w-full bg-neuBg text-darkText font-sans antialiased selection:bg-brand selection:text-white relative overflow-x-hidden">

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText" x-data="nodeDetailApp('{{ $deviceId }}')" x-init="initDetail()">
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
                    <div class="px-4 md:px-6 xl:px-8 flex items-center h-[72px]">
                        <a href="{{ route('agrinex.devices') }}" class="p-2 mr-4 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText hover:text-brand hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div>
                            <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-darkText" x-text="node ? node.name : 'Memuat Perangkat...'"></h2>
                            <p class="text-lightText text-[10px] md:text-xs mt-0.5" x-text="'ID: {{ $deviceId }}'"></p>
                        </div>
                    </div>
                </div>

                {{-- Page content --}}
                <main class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[1400px] mx-auto space-y-6 md:space-y-8">
                    
                    {{-- Loading State - Skeleton --}}
                    <div x-show="loading" class="space-y-6 md:space-y-8">
                        {{-- Skeleton: Stats Cards --}}
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            {{-- Status Card Skeleton --}}
                            <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col justify-center">
                                <div class="h-4 w-32 bg-neuBg rounded-lg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] mb-4 animate-pulse"></div>
                                <div class="h-6 w-20 bg-neuBg rounded-full shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] mb-4 animate-pulse"></div>
                                <div class="h-3 w-24 bg-neuBg rounded shadow-[inset_1px_1px_2px_#a3b1c6,inset_-1px_-1px_2px_#ffffff] animate-pulse"></div>
                            </div>
                            
                            {{-- Metrics Skeleton --}}
                            <div class="lg:col-span-3 grid grid-cols-3 gap-4 md:gap-6">
                                <template x-for="i in 3" :key="i">
                                    <div class="bg-neuBg rounded-[2rem] p-5 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col items-center justify-center">
                                        <div class="h-3 w-16 bg-neuBg rounded shadow-[inset_1px_1px_2px_#a3b1c6,inset_-1px_-1px_2px_#ffffff] mb-2 animate-pulse"></div>
                                        <div class="h-8 w-12 bg-neuBg rounded shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] animate-pulse"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Skeleton: Charts --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <template x-for="i in 2" :key="i">
                                <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="h-5 w-40 bg-neuBg rounded-lg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] animate-pulse"></div>
                                        <div class="flex gap-2">
                                            <div class="h-6 w-16 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] animate-pulse"></div>
                                            <div class="h-6 w-16 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] animate-pulse"></div>
                                        </div>
                                    </div>
                                    <div class="w-full h-[300px] bg-neuBg rounded-2xl shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] animate-pulse"></div>
                                </div>
                            </template>
                        </div>

                        {{-- Skeleton: Tables --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <template x-for="i in 3" :key="i">
                                <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="h-5 w-40 bg-neuBg rounded-lg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] animate-pulse"></div>
                                        <div class="flex gap-2">
                                            <div class="h-5 w-12 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] animate-pulse"></div>
                                            <div class="h-5 w-12 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] animate-pulse"></div>
                                        </div>
                                    </div>
                                    <div class="bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-4 space-y-3">
                                        <template x-for="row in 5" :key="row">
                                            <div class="flex justify-between items-center">
                                                <div class="h-3 w-20 bg-neuBg rounded shadow-[inset_1px_1px_2px_#a3b1c6,inset_-1px_-1px_2px_#ffffff] animate-pulse"></div>
                                                <div class="h-3 w-16 bg-neuBg rounded shadow-[inset_1px_1px_2px_#a3b1c6,inset_-1px_-1px_2px_#ffffff] animate-pulse"></div>
                                                <div class="h-3 w-12 bg-neuBg rounded shadow-[inset_1px_1px_2px_#a3b1c6,inset_-1px_-1px_2px_#ffffff] animate-pulse"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="!loading" class="space-y-6 md:space-y-8">
                        {{-- Quick Stats & Status --}}
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            
                            {{-- Status Card --}}
                            <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col justify-center">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm font-bold text-lightText uppercase tracking-wider">Status Koneksi</span>
                                    <div class="px-3 py-1.5 rounded-full shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full" 
                                             :class="node?.connection_state === 'online' ? 'bg-[#00D26A] shadow-[0_0_8px_#00D26A]' : 'bg-[#EF4444] animate-pulse'"></div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider"
                                              :class="node?.connection_state === 'online' ? 'text-[#00D26A]' : 'text-[#EF4444]'"
                                              x-text="node?.connection_state === 'online' ? 'ONLINE' : 'OFFLINE'"></span>
                                    </div>
                                </div>
                                <div class="text-xs text-lightText font-medium">
                                    Update terakhir:<br>
                                    <span class="text-darkText font-bold text-sm" x-text="node?.last_updated ?? 'Belum ada data'"></span>
                                </div>
                            </div>

                            {{-- Metrics --}}
                            <div class="lg:col-span-3 grid grid-cols-3 gap-4 md:gap-6">
                                <div class="bg-neuBg rounded-[2rem] p-5 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col items-center justify-center">
                                    <span class="text-[10px] md:text-xs font-bold text-lightText uppercase mb-2">Lembap Tanah</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl md:text-3xl font-extrabold" :class="(node?.soil_moisture_pct ?? 0) < 30 ? 'text-[#EF4444]' : 'text-[#00D26A]'" x-text="node?.soil_moisture_pct ?? '--'"></span>
                                        <span class="text-sm md:text-base font-bold text-lightText">%</span>
                                    </div>
                                </div>
                                <div class="bg-neuBg rounded-[2rem] p-5 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col items-center justify-center">
                                    <span class="text-[10px] md:text-xs font-bold text-lightText uppercase mb-2">Suhu Lingkungan</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl md:text-3xl font-extrabold text-darkText" x-text="node?.temperature_c ?? '--'"></span>
                                        <span class="text-sm md:text-base font-bold text-lightText">°C</span>
                                    </div>
                                </div>
                                <div class="bg-neuBg rounded-[2rem] p-5 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col items-center justify-center">
                                    <span class="text-[10px] md:text-xs font-bold text-lightText uppercase mb-2">Voltase Baterai</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl md:text-3xl font-extrabold" :class="(node?.battery_voltage_v ?? 0) < 3.2 ? 'text-amber-500' : 'text-darkText'" x-text="node?.battery_voltage_v ?? '--'"></span>
                                        <span class="text-sm md:text-base font-bold text-lightText">V</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Chart Section --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Sensor Chart --}}
                            <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                <h3 class="text-lg font-bold tracking-tight text-darkText mb-6">Grafik Sensor (24 Jam)</h3>
                                <div class="w-full h-[300px]">
                                    <canvas id="nodeChartCanvas"></canvas>
                                </div>
                            </div>

                            {{-- Irrigation Sessions Chart --}}
                            <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-bold tracking-tight text-darkText">Chart Sesi Irigasi</h3>
                                    <div class="flex gap-2">
                                        <button @click="irrigationPeriod='today'; fetchSessions()" 
                                                :class="irrigationPeriod==='today' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all">
                                            Hari Ini
                                        </button>
                                        <button @click="irrigationPeriod='week'; fetchSessions()" 
                                                :class="irrigationPeriod==='week' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all">
                                            Minggu Ini
                                        </button>
                                        <button @click="irrigationPeriod='month'; fetchSessions()" 
                                                :class="irrigationPeriod==='month' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all">
                                            Bulan Ini
                                        </button>
                                    </div>
                                </div>
                                <div class="w-full h-[300px] relative">
                                    <canvas id="irrigationChartCanvas"></canvas>
                                    <div x-show="!deviceSessions.length" class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-lightText mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            <p class="text-sm font-bold text-lightText">Tidak ada data sesi irigasi</p>
                                            <p class="text-xs text-lightText mt-1">untuk periode yang dipilih</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tables Section --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            
                            {{-- Sleep History --}}
                            <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold tracking-tight text-darkText">Riwayat Sleep Mode</h3>
                                    <div class="flex gap-2">
                                        <button @click="sleepPeriod='today'; fetchSleepHistory()" 
                                                :class="sleepPeriod==='today' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider transition-all">
                                            Hari Ini
                                        </button>
                                        <button @click="sleepPeriod='week'; fetchSleepHistory()" 
                                                :class="sleepPeriod==='week' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider transition-all">
                                            Minggu
                                        </button>
                                        <button @click="sleepPeriod='month'; fetchSleepHistory()" 
                                                :class="sleepPeriod==='month' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider transition-all">
                                            Bulan
                                        </button>
                                    </div>
                                </div>
                                <div class="overflow-x-auto bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-2 max-h-[300px] overflow-y-auto no-scrollbar">
                                    <table class="min-w-full text-xs text-left">
                                        <thead class="text-lightText font-bold border-b-2 border-white/30 sticky top-0 bg-neuBg/90 backdrop-blur-md">
                                            <tr>
                                                <th class="px-4 py-3">Waktu Mulai</th>
                                                <th class="px-4 py-3">Waktu Selesai</th>
                                                <th class="px-4 py-3 text-right">Durasi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-darkText font-medium">
                                            <tr x-show="!sleepHistory.length">
                                                <td colspan="3" class="px-4 py-4 text-center italic text-lightText">Tidak ada riwayat sleep mode.</td>
                                            </tr>
                                            <template x-for="(s, index) in sleepHistory" :key="s.sleep_start || index">
                                                <tr class="border-b border-white/20 last:border-0 hover:bg-white/20 transition-colors">
                                                    <td class="px-4 py-3 text-[10px]" x-text="s.sleep_start_human || formatDateTime(s.sleep_start)"></td>
                                                    <td class="px-4 py-3 text-[10px]" x-text="s.sleep_end_human || formatDateTime(s.sleep_end)"></td>
                                                    <td class="px-4 py-3 text-right text-brand font-bold" x-text="s.duration_formatted"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            {{-- Sesi Irigasi --}}
                            <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                <h3 class="text-lg font-bold tracking-tight text-darkText mb-4">Sesi Irigasi (Hari Ini)</h3>
                                
                                <template x-if="deviceSessionsSummary">
                                    <div class="text-[11px] font-bold text-lightText mb-4 flex flex-wrap gap-2">
                                        <span class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-2 py-1 rounded-md" x-text="'Rencana: ' + fmt(deviceSessionsSummary.total_planned_l,' L')"></span>
                                        <span class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-2 py-1 rounded-md" x-text="'Aktual: ' + fmt(deviceSessionsSummary.total_actual_l,' L')"></span>
                                        <span class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-2 py-1 rounded-md text-brand" x-text="'Efisien: ' + (deviceSessionsSummary.efficiency_pct!=null? deviceSessionsSummary.efficiency_pct+'%':'-')"></span>
                                    </div>
                                </template>

                                <div class="overflow-x-auto bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-2 max-h-[300px] overflow-y-auto no-scrollbar">
                                    <table class="min-w-full text-xs text-left">
                                        <thead class="text-lightText font-bold border-b-2 border-white/30 sticky top-0 bg-neuBg/90 backdrop-blur-md">
                                            <tr>
                                                <th class="px-4 py-3">Sesi</th>
                                                <th class="px-4 py-3">Waktu</th>
                                                <th class="px-4 py-3 text-right">Target (L)</th>
                                                <th class="px-4 py-3 text-right">Aktual (L)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-darkText font-medium">
                                            <tr x-show="!deviceSessions.length">
                                                <td colspan="4" class="px-4 py-4 text-center italic text-lightText">Tidak ada data sesi hari ini.</td>
                                            </tr>
                                            <template x-for="s in deviceSessions" :key="s.id || s.index">
                                                <tr class="border-b border-white/20 last:border-0 hover:bg-white/20 transition-colors">
                                                    <td class="px-4 py-3" x-text="s.index || s.session || '-' "></td>
                                                    <td class="px-4 py-3" x-text="s.time || s.start_time || '-' "></td>
                                                    <td class="px-4 py-3 text-right" x-text="s.planned_l ? s.planned_l.toFixed(1) : (s.planned_volume_l?.toFixed(1) || '-')"></td>
                                                    <td class="px-4 py-3 text-right text-brand font-bold" x-text="s.actual_l ? s.actual_l.toFixed(1) : (s.actual_volume_l?.toFixed(1) || '-')"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Riwayat --}}
                            <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                <h3 class="text-lg font-bold tracking-tight text-darkText mb-4">Riwayat Penggunaan (7 Hari)</h3>
                                <div class="overflow-x-auto bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-2 max-h-[300px] overflow-y-auto no-scrollbar">
                                    <table class="min-w-full text-xs text-left">
                                        <thead class="text-lightText font-bold border-b-2 border-white/30 sticky top-0 bg-neuBg/90 backdrop-blur-md">
                                            <tr>
                                                <th class="px-4 py-3">Tanggal</th>
                                                <th class="px-4 py-3 text-right">Sesi</th>
                                                <th class="px-4 py-3 text-right">Total Air (L)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-darkText font-medium">
                                            <tr x-show="!deviceUsageHistory.length">
                                                <td colspan="3" class="px-4 py-4 text-center italic text-lightText">Tidak ada riwayat.</td>
                                            </tr>
                                            <template x-for="h in deviceUsageHistory" :key="h.date || h.day || h.id">
                                                <tr class="border-b border-white/20 last:border-0 hover:bg-white/20 transition-colors">
                                                    <td class="px-4 py-3" x-text="h.date || h.day || '-' "></td>
                                                    <td class="px-4 py-3 text-right" x-text="h.sessions || h.session_count || '-' "></td>
                                                    <td class="px-4 py-3 text-right text-brand font-bold" x-text="h.total_l ? h.total_l.toFixed(1) : (h.volume_l?.toFixed(1) || '-')"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Battery History Chart (NEW) --}}
                            <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold tracking-tight text-darkText">Riwayat Baterai</h3>
                                    <div class="flex gap-2">
                                        <button @click="batteryPeriod='today'; fetchBatteryHistory()" 
                                                :class="batteryPeriod==='today' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider transition-all">
                                            Hari Ini
                                        </button>
                                        <button @click="batteryPeriod='week'; fetchBatteryHistory()" 
                                                :class="batteryPeriod==='week' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider transition-all">
                                            Minggu
                                        </button>
                                        <button @click="batteryPeriod='month'; fetchBatteryHistory()" 
                                                :class="batteryPeriod==='month' ? 'shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-lightText'"
                                                class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider transition-all">
                                            Bulan
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Battery Stats --}}
                                <template x-if="batteryStats">
                                    <div class="grid grid-cols-4 gap-2 mb-4">
                                        <div class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-3 py-2 rounded-xl text-center">
                                            <div class="text-[9px] font-bold text-lightText uppercase mb-1">Rata-rata</div>
                                            <div class="text-sm font-extrabold text-darkText" x-text="batteryStats.avg_percentage + '%'"></div>
                                        </div>
                                        <div class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-3 py-2 rounded-xl text-center">
                                            <div class="text-[9px] font-bold text-lightText uppercase mb-1">Min</div>
                                            <div class="text-sm font-extrabold text-amber-500" x-text="batteryStats.min_voltage + 'V'"></div>
                                        </div>
                                        <div class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-3 py-2 rounded-xl text-center">
                                            <div class="text-[9px] font-bold text-lightText uppercase mb-1">Max</div>
                                            <div class="text-sm font-extrabold text-green-500" x-text="batteryStats.max_voltage + 'V'"></div>
                                        </div>
                                        <div class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-3 py-2 rounded-xl text-center">
                                            <div class="text-[9px] font-bold text-lightText uppercase mb-1">Data</div>
                                            <div class="text-sm font-extrabold text-brand" x-text="batteryStats.readings_count"></div>
                                        </div>
                                    </div>
                                </template>

                                <div class="w-full h-[250px] relative">
                                    <canvas id="batteryChartCanvas"></canvas>
                                    <div x-show="!batteryHistory.length" class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-lightText mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <p class="text-sm font-bold text-lightText">Tidak ada data baterai</p>
                                            <p class="text-xs text-lightText mt-1">untuk periode yang dipilih</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('nodeDetailApp', (deviceId) => ({
                deviceId: deviceId,
                node: null,
                loading: true,
                deviceSessions: [],
                deviceSessionsSummary: null,
                deviceUsageHistory: [],
                sleepHistory: [], // Initialize empty array to prevent Alpine error
                batteryHistory: [], // Initialize empty array
                batteryStats: null,
                chartObj: null,
                irrigationChartObj: null,
                batteryChartObj: null,
                irrigationPeriod: 'today',
                sleepPeriod: 'week',
                batteryPeriod: 'week',

                async initDetail() {
                    // Fetch all devices to find this specific node
                    try {
                        const devicesResp = await fetch('/api/v1/dashboard/devices');
                        if (devicesResp.ok) {
                            const res = await devicesResp.json();
                            const devices = res.data || res;
                            this.node = devices.find(d => String(d.id) === String(this.deviceId) || String(d.device_id) === String(this.deviceId));
                        }
                        
                        // Fetch detailed data
                        await Promise.all([
                            this.fetchSessions(),
                            this.fetchHistory(),
                            this.fetchSleepHistory(),
                            this.fetchBatteryHistory(),
                            this.fetchChartData()
                        ]);
                    } catch (e) {
                        console.error("Error loading detail:", e);
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchSessions() {
                    try {
                        const resp = await fetch(`/api/v1/devices/${this.deviceId}/irrigation-sessions?period=${this.irrigationPeriod}`);
                        if (resp.ok) {
                            const data = await resp.json();
                            this.deviceSessions = data.sessions || [];
                            this.deviceSessionsSummary = data.summary || null;
                            this.renderIrrigationChart();
                        }
                    } catch (e) { console.error(e); }
                },

                async fetchHistory() {
                    try {
                        const resp = await fetch(`/api/v1/devices/${this.deviceId}/usage-history`);
                        if (resp.ok) {
                            const data = await resp.json();
                            this.deviceUsageHistory = data.history || [];
                        }
                    } catch (e) { console.error(e); }
                },

                async fetchSleepHistory() {
                    try {
                        const resp = await fetch(`/api/v1/devices/${this.deviceId}/sleep-history?period=${this.sleepPeriod}`);
                        if (resp.ok) {
                            const data = await resp.json();
                            this.sleepHistory = data.history || [];
                        }
                    } catch (e) { console.error(e); }
                },

                async fetchBatteryHistory() {
                    try {
                        const resp = await fetch(`/api/v1/devices/${this.deviceId}/battery-history?period=${this.batteryPeriod}`);
                        if (resp.ok) {
                            const data = await resp.json();
                            this.batteryHistory = data.history || [];
                            this.batteryStats = data.stats || null;
                            this.renderBatteryChart();
                        }
                    } catch (e) { console.error(e); }
                },

                async fetchChartData() {
                    try {
                        const resp = await fetch(`/api/v1/devices/${this.deviceId}/chart-data`);
                        if (resp.ok) {
                            const data = await resp.json();
                            const ds = data.datasets || {};
                            this.renderChart(data.labels || [], ds.soil_moisture || [], ds.temperature || []);
                        }
                    } catch (e) { console.error(e); }
                },

                renderChart(labels, soilData, tempData) {
                    const ctx = document.getElementById('nodeChartCanvas');
                    if(!ctx) return;
                    
                    // Destroy existing chart
                    if(this.chartObj) {
                        this.chartObj.destroy();
                        this.chartObj = null;
                    }
                    
                    // If no data, don't create chart
                    if(!labels.length || (!soilData.length && !tempData.length)) return;

                    // Neumorphism styling
                    Chart.defaults.color = '#7e8a9f';
                    Chart.defaults.font.family = "'Inter', sans-serif";

                    this.chartObj = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Kelembapan Tanah (%)',
                                    data: soilData,
                                    borderColor: '#0ea5e9',
                                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#E0E5EC',
                                    pointBorderColor: '#0ea5e9',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Suhu (°C)',
                                    data: tempData,
                                    borderColor: '#f59e0b',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    borderDash: [5, 5],
                                    pointBackgroundColor: '#E0E5EC',
                                    pointBorderColor: '#f59e0b',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            weight: 'bold'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(224, 229, 236, 0.95)',
                                    titleColor: '#2b313b',
                                    bodyColor: '#4b5563',
                                    borderColor: 'rgba(255, 255, 255, 0.8)',
                                    borderWidth: 1,
                                    padding: 12,
                                    boxPadding: 6,
                                    cornerRadius: 12,
                                    titleFont: { size: 13, weight: 'bold' },
                                    bodyFont: { size: 12, weight: 'bold' }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { maxTicksLimit: 8 }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    grid: { color: 'rgba(163, 177, 198, 0.2)', borderDash: [5, 5] },
                                    min: 0,
                                    max: 100,
                                    title: { display: true, text: 'Kelembapan (%)', font: {weight: 'bold'} }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: { display: false },
                                    min: 0,
                                    max: 50,
                                    title: { display: true, text: 'Suhu (°C)', font: {weight: 'bold'} }
                                }
                            }
                        }
                    });
                },

                renderIrrigationChart() {
                    const ctx = document.getElementById('irrigationChartCanvas');
                    if(!ctx) return;
                    
                    // Destroy existing chart
                    if(this.irrigationChartObj) {
                        this.irrigationChartObj.destroy();
                        this.irrigationChartObj = null;
                    }
                    
                    // If no data, don't create chart (empty state will show)
                    if(!this.deviceSessions.length) return;

                    const labels = this.deviceSessions.map(s => s.index || s.session || '#' + (this.deviceSessions.indexOf(s) + 1));
                    const plannedData = this.deviceSessions.map(s => s.planned_l || s.planned_volume_l || 0);
                    const actualData = this.deviceSessions.map(s => s.actual_l || s.actual_volume_l || 0);

                    Chart.defaults.color = '#7e8a9f';
                    Chart.defaults.font.family = "'Inter', sans-serif";

                    this.irrigationChartObj = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Target (L)',
                                    data: plannedData,
                                    backgroundColor: 'rgba(163, 177, 198, 0.6)',
                                    borderColor: '#a3b1c6',
                                    borderWidth: 2,
                                    borderRadius: 8,
                                },
                                {
                                    label: 'Aktual (L)',
                                    data: actualData,
                                    backgroundColor: 'rgba(14, 165, 233, 0.7)',
                                    borderColor: '#0ea5e9',
                                    borderWidth: 2,
                                    borderRadius: 8,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
                                        font: { weight: 'bold', size: 11 }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(224, 229, 236, 0.95)',
                                    titleColor: '#2b313b',
                                    bodyColor: '#4b5563',
                                    borderColor: 'rgba(255, 255, 255, 0.8)',
                                    borderWidth: 1,
                                    padding: 10,
                                    cornerRadius: 10,
                                    titleFont: { size: 12, weight: 'bold' },
                                    bodyFont: { size: 11, weight: 'bold' }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false, drawBorder: false }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(163, 177, 198, 0.2)', borderDash: [5, 5] },
                                    title: { display: true, text: 'Volume (Liter)', font: {weight: 'bold', size: 11} }
                                }
                            }
                        }
                    });
                },

                renderBatteryChart() {
                    const ctx = document.getElementById('batteryChartCanvas');
                    if(!ctx) return;
                    
                    // Destroy existing chart
                    if(this.batteryChartObj) {
                        this.batteryChartObj.destroy();
                        this.batteryChartObj = null;
                    }
                    
                    // If no data, don't create chart (empty state will show)
                    if(!this.batteryHistory.length) return;

                    // Sort by timestamp ascending for proper timeline
                    const sortedHistory = [...this.batteryHistory].reverse();
                    
                    const labels = sortedHistory.map(b => {
                        const date = new Date(b.recorded_at || b.timestamp);
                        return date.toLocaleString('id-ID', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                    });
                    const voltageData = sortedHistory.map(b => b.voltage);
                    const percentageData = sortedHistory.map(b => b.percentage);

                    Chart.defaults.color = '#7e8a9f';
                    Chart.defaults.font.family = "'Inter', sans-serif";

                    this.batteryChartObj = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Voltase (V)',
                                    data: voltageData,
                                    borderColor: '#f59e0b',
                                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    pointBackgroundColor: '#E0E5EC',
                                    pointBorderColor: '#f59e0b',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    yAxisID: 'y',
                                    fill: true
                                },
                                {
                                    label: 'Persentase (%)',
                                    data: percentageData,
                                    borderColor: '#0ea5e9',
                                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    pointBackgroundColor: '#E0E5EC',
                                    pointBorderColor: '#0ea5e9',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    yAxisID: 'y1',
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
                                        font: { weight: 'bold', size: 11 }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(224, 229, 236, 0.95)',
                                    titleColor: '#2b313b',
                                    bodyColor: '#4b5563',
                                    borderColor: 'rgba(255, 255, 255, 0.8)',
                                    borderWidth: 1,
                                    padding: 10,
                                    cornerRadius: 10,
                                    titleFont: { size: 12, weight: 'bold' },
                                    bodyFont: { size: 11, weight: 'bold' },
                                    callbacks: {
                                        afterBody: function(context) {
                                            const index = context[0].dataIndex;
                                            const status = sortedHistory[index].status;
                                            return 'Status: ' + status;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { maxTicksLimit: 8, font: { size: 9 } }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    grid: { color: 'rgba(163, 177, 198, 0.2)', borderDash: [5, 5] },
                                    min: 3.0,
                                    max: 4.3,
                                    title: { display: true, text: 'Voltase (V)', font: {weight: 'bold', size: 11} }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: { display: false },
                                    min: 0,
                                    max: 100,
                                    title: { display: true, text: 'Persentase (%)', font: {weight: 'bold', size: 11} }
                                }
                            }
                        }
                    });
                },

                formatDateTime(dateStr) {
                    if (!dateStr) return '-';
                    const date = new Date(dateStr);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${day}/${month} ${hours}:${minutes}`;
                },

                fmt(val, unit) {
                    if(val == null) return '--' + unit;
                    return parseFloat(val).toFixed(1) + unit;
                }
            }));
        });
    </script>
</body>
</html>
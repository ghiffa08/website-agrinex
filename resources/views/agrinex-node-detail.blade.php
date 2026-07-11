<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: #E0E5EC;">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body x-data="dashboard()" x-init="initData()"
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
                    
                    {{-- Loading State --}}
                    <div x-show="loading" class="flex justify-center items-center py-20">
                        <svg class="animate-spin h-10 w-10 text-brand" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
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
                                             :class="node?.connection_state === 'connected' ? 'bg-[#00D26A] shadow-[0_0_8px_#00D26A]' : 'bg-[#EF4444] animate-pulse'"></div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider"
                                              :class="node?.connection_state === 'connected' ? 'text-[#00D26A]' : 'text-[#EF4444]'"
                                              x-text="node?.connection_state === 'connected' ? 'ONLINE' : 'OFFLINE'"></span>
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
                        <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                            <h3 class="text-lg font-bold tracking-tight text-darkText mb-6">Grafik Sensor (24 Jam)</h3>
                            <div class="w-full h-[300px] md:h-[400px]">
                                <canvas id="nodeChartCanvas"></canvas>
                            </div>
                        </div>

                        {{-- Tables Section --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            
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
                                            <template x-if="!deviceSessions.length">
                                                <tr><td colspan="4" class="px-4 py-4 text-center italic text-lightText">Tidak ada data sesi hari ini.</td></tr>
                                            </template>
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
                                            <template x-if="!deviceUsageHistory.length">
                                                <tr><td colspan="3" class="px-4 py-4 text-center italic text-lightText">Tidak ada riwayat.</td></tr>
                                            </template>
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
                chartObj: null,

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
                        const resp = await fetch(`/api/devices/${this.deviceId}/irrigation/sessions`);
                        if (resp.ok) {
                            const data = await resp.json();
                            this.deviceSessions = data.sessions || [];
                            this.deviceSessionsSummary = data.summary || null;
                        }
                    } catch (e) { console.error(e); }
                },

                async fetchHistory() {
                    try {
                        const resp = await fetch(`/api/devices/${this.deviceId}/usage-history`);
                        if (resp.ok) {
                            const data = await resp.json();
                            this.deviceUsageHistory = data.history || [];
                        }
                    } catch (e) { console.error(e); }
                },

                async fetchChartData() {
                    try {
                        const resp = await fetch(`/api/devices/${this.deviceId}/chart-data`);
                        if (resp.ok) {
                            const data = await resp.json();
                            this.renderChart(data.labels || [], data.soil_moisture || [], data.temperature || []);
                        }
                    } catch (e) { console.error(e); }
                },

                renderChart(labels, soilData, tempData) {
                    const ctx = document.getElementById('nodeChartCanvas');
                    if(!ctx) return;
                    
                    if(this.chartObj) {
                        this.chartObj.destroy();
                    }

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

                fmt(val, unit) {
                    if(val == null) return '--' + unit;
                    return parseFloat(val).toFixed(1) + unit;
                }
            }));
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>

    <body x-data="dashboard()" x-init="applyPersistedTheme();
        let historyFetched = false;
        // Wait for devices to load, then select the right one
        $watch('devices', value => {
            if (value.length > 0) {
                selectedDevice = value.find(d => d.device_id == '{{ $deviceId }}');
                if (selectedDevice && !historyFetched) {
                    historyFetched = true;
                    // Fetch device details if necessary
                    loadingDeviceDetail = true;
                    
                    // Fetch device sessions & usage
                    fetch(`/api/devices/${selectedDevice.device_id}/irrigation/sessions`)
                        .then(r => r.json())
                        .then(d => {
                            deviceSessions = d.sessions || [];
                            deviceSessionsSummary = d.summary || null;
                        })
                        .catch(e => console.error('Error fetching sessions:', e));
                        
                    fetch(`/api/devices/${selectedDevice.device_id}/usage-history`)
                        .then(r => r.json())
                        .then(d => {
                            deviceUsageHistory = d.history || [];
                        })
                        .catch(e => console.error('Error fetching history:', e));

                    // Fetch chart data
                    fetch(`/api/devices/${selectedDevice.device_id}/chart-data`)
                        .then(r => r.json())
                        .then(d => {
                            if(d.success) {
                                initNodeChart(d.labels, d.datasets.temperature, d.datasets.soil_moisture);
                            }
                            loadingDeviceDetail = false;
                        })
                        .catch(e => {
                            console.error('Error fetching chart data:', e);
                            loadingDeviceDetail = false;
                        });
                }
            }
        });
    "
    class="h-full bg-[#f0f5f0] text-gray-800 relative overflow-x-hidden transition-colors duration-500">

    {{-- ── Fixed background image ── --}}
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.12] transition-opacity duration-700"
            style="background-image: url('{{ asset('images/background-perkebunan.webp') }}');"></div>
        {{-- Soft gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/80 via-transparent to-transparent"></div>
    </div>

    {{-- ══════════════════════════════════════════════════
         App Shell: [Sidebar | Main]
    ══════════════════════════════════════════════════ --}}
    <div class="relative z-10 flex h-full min-h-screen">

        {{-- Sidebar --}}
        <div class="hidden md:flex md:flex-shrink-0">
            @include('components.sidebar')
        </div>
        <div class="md:hidden">
            @include('components.sidebar')
        </div>

        {{-- ── Main column ── --}}
        <div class="flex-1 min-w-0 flex flex-col min-h-screen overflow-x-hidden">

            {{-- Sticky frosted header --}}
            <div class="sticky top-0 z-30 w-full bg-white/55 backdrop-blur-2xl border-b border-white/40 shadow-sm shadow-black/[0.05] transition-colors duration-300">
                <div class="px-4 md:px-6 xl:px-8">
                    @include('components.header')
                </div>
            </div>

            {{-- Page content --}}
            <main class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[1400px] mx-auto">
                <div class="mb-4">
                    <a href="{{ route('agrinex.dashboard') }}" class="text-emerald-600 hover:text-emerald-700:text-emerald-300 flex items-center gap-2 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>

                <div x-show="!selectedDevice && loadingAll" class="flex justify-center items-center py-20">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-500"></div>
                </div>

                <x-ui.card x-show="selectedDevice" x-cloak class="border-0 shadow-lg lg:p-4">
                    <x-ui.card-header class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-6 mb-8">
                        <div>
                            <x-ui.card-title class="text-2xl flex items-center gap-3 text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                                <span x-text="selectedDevice?.device_name || 'Device Detail'"></span>
                            </x-ui.card-title>
                            <x-ui.card-description class="mt-2 flex items-center gap-2">
                                <x-ui.badge variant="secondary" class="font-mono" x-text="'ID: '+selectedDevice?.device_id"></x-ui.badge>
                                <x-ui.badge variant="secondary" x-show="selectedDevice?.lahan_pantau_name" class="bg-blue-50 text-blue-600 hover:bg-blue-50" x-text="selectedDevice?.lahan_pantau_name"></x-ui.badge>
                            </x-ui.card-description>
                        </div>
                        <div>
                            <x-ui.badge variant="outline" class="gap-2 px-3 py-1.5 shadow-sm text-sm uppercase tracking-wider">
                                <div class="w-2 h-2 rounded-full animate-pulse" 
                                    :class="selectedDevice?.connection_state === 'online' ? 'bg-emerald-500' : 'bg-gray-400'"></div>
                                <span :class="selectedDevice?.connection_state === 'online' ? 'text-emerald-600' : 'text-slate-500'"
                                    x-text="selectedDevice?.connection_state === 'online' ? 'Online' : 'Offline'"></span>
                            </x-ui.badge>
                        </div>
                    </x-ui.card-header>

                    <x-ui.card-content>

                    <!-- Quick stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
                        <div class="bg-white/80 p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:-translate-y-1 transition-transform">
                            <div class="text-sm font-semibold text-secondary mb-1">Suhu</div>
                            <div class="text-3xl font-bold text-primary" x-text="selectedDevice?.temperature_c ? selectedDevice.temperature_c + '°C' : '-'"></div>
                        </div>
                        <div class="bg-white/80 p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:-translate-y-1 transition-transform">
                            <div class="text-sm font-semibold text-secondary mb-1">Kelembapan Tanah</div>
                            <div class="text-3xl font-bold text-[#1c73a5]" x-text="selectedDevice?.soil_moisture_pct ? Math.round(selectedDevice.soil_moisture_pct) + '%' : '-'"></div>
                        </div>
                        <div class="bg-white/80 p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:-translate-y-1 transition-transform">
                            <div class="text-sm font-semibold text-secondary mb-1">Baterai</div>
                            <div class="text-3xl font-bold text-emerald-600" x-text="selectedDevice?.battery_voltage_v ? Math.round(Math.max(0, Math.min(100, ((selectedDevice.battery_voltage_v - 3.3) / (4.2 - 3.3)) * 100))) + '%' : '-'"></div>
                        </div>
                        <div class="bg-white/80 p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:-translate-y-1 transition-transform">
                            <div class="text-sm font-semibold text-secondary mb-1">FC Target</div>
                            <div class="text-3xl font-bold text-purple-600" x-text="selectedDevice?.fc_target ? selectedDevice.fc_target.toFixed(1) + '%' : '-'"></div>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="mb-8 bg-white/50 p-6 rounded-2xl border border-gray-200/50 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-primary flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg> 
                                Riwayat Sensor Node
                            </h4>
                        </div>
                        <div class="relative h-72 w-full">
                            <canvas id="nodeHistoryChart"></canvas>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Sessions table -->
                        <div class="bg-white/50 p-6 rounded-2xl border border-gray-200/50">
                            <h4 class="text-lg font-semibold text-primary mb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a6.002 6.002 0 0 0 3.6-10.8c-.8-.8-2.6-2.9-3.6-4.2-1 1.3-2.8 3.4-3.6 4.2A6.002 6.002 0 0 0 12 21Z" /></svg> 
                                Penggunaan Air per Sesi
                                <template x-if="loadingDeviceDetail"><span class="text-sm text-secondary font-normal ml-2 animate-pulse">(memuat...)</span></template>
                            </h4>
                            <template x-if="deviceSessionsSummary">
                                <div class="text-sm text-secondary mb-4 bg-white p-3 rounded-lg border border-gray-100 flex flex-wrap gap-4">
                                    <div><span class="font-medium">Total Rencana:</span> <span x-text="deviceSessionsSummary.total_planned_l ? deviceSessionsSummary.total_planned_l.toFixed(1) + ' L' : '-'"></span></div>
                                    <div class="hidden sm:block text-gray-300">|</div>
                                    <div><span class="font-medium">Total Aktual:</span> <span x-text="deviceSessionsSummary.total_actual_l ? deviceSessionsSummary.total_actual_l.toFixed(1) + ' L' : '-'"></span></div>
                                    <div class="hidden sm:block text-gray-300">|</div>
                                    <div><span class="font-medium">Efisiensi:</span> <span x-text="deviceSessionsSummary.efficiency_pct != null ? deviceSessionsSummary.efficiency_pct + '%' : '-'"></span></div>
                                </div>
                            </template>
                            <template x-if="!loadingDeviceDetail && !deviceSessions.length">
                                <div class="text-sm text-secondary bg-gray-50 p-4 rounded-lg text-center">Belum ada data sesi untuk device ini.</div>
                            </template>
                            <template x-if="deviceSessions.length">
                                <div class="overflow-x-auto border border-gray-200 rounded-xl bg-white">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50 text-secondary border-b border-gray-200">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">Sesi</th>
                                                <th class="px-4 py-3 text-left font-medium">Waktu</th>
                                                <th class="px-4 py-3 text-right font-medium">Rencana (L)</th>
                                                <th class="px-4 py-3 text-right font-medium">Aktual (L)</th>
                                                <th class="px-4 py-3 text-right font-medium">Efisiensi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="s in deviceSessions" :key="s.id || s.index">
                                                <tr class="hover:bg-gray-50:bg-white/5 transition-colors">
                                                    <td class="px-4 py-3" x-text="s.index || s.session || '-' "></td>
                                                    <td class="px-4 py-3" x-text="s.time || s.start_time || '-' "></td>
                                                    <td class="px-4 py-3 text-right font-medium" x-text="s.planned_l ? s.planned_l.toFixed(1) : (s.planned_volume_l?.toFixed(1) || '-')"></td>
                                                    <td class="px-4 py-3 text-right font-medium" x-text="s.actual_l ? s.actual_l.toFixed(1) : (s.actual_volume_l?.toFixed(1) || '-')"></td>
                                                    <td class="px-4 py-3 text-right">
                                                        <span class="px-2 py-1 rounded text-xs font-bold"
                                                            :class="{
                                                                'bg-emerald-100 text-emerald-700': parseInt(s.efficiency_pct || ((s.actual_l / (s.planned_l||1))*100)) >= 80,
                                                                'bg-yellow-100 text-yellow-700': parseInt(s.efficiency_pct || ((s.actual_l / (s.planned_l||1))*100)) >= 50 && parseInt(s.efficiency_pct || ((s.actual_l / (s.planned_l||1))*100)) < 80,
                                                                'bg-red-100 text-red-700': parseInt(s.efficiency_pct || ((s.actual_l / (s.planned_l||1))*100)) < 50
                                                            }"
                                                            x-text="(s.actual_l && s.planned_l) ? ((s.actual_l / (s.planned_l||1))*100).toFixed(0)+'%' : (s.efficiency_pct ? s.efficiency_pct+'%' : '-')">
                                                        </span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>

                        <!-- Usage history table -->
                        <div class="bg-white/50 p-6 rounded-2xl border border-gray-200/50">
                            <h4 class="text-lg font-semibold text-primary mb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> 
                                Riwayat Penggunaan Air
                                <template x-if="loadingDeviceDetail"><span class="text-sm text-secondary font-normal ml-2 animate-pulse">(memuat...)</span></template>
                            </h4>
                            <template x-if="!loadingDeviceDetail && !deviceUsageHistory.length">
                                <div class="text-sm text-secondary bg-gray-50 p-4 rounded-lg text-center">Belum ada data penggunaan sebelumnya.</div>
                            </template>
                            <template x-if="deviceUsageHistory.length">
                                <div class="overflow-x-auto border border-gray-200 rounded-xl bg-white">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50 text-secondary border-b border-gray-200">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                                                <th class="px-4 py-3 text-right font-medium">Total (L)</th>
                                                <th class="px-4 py-3 text-right font-medium">Sesi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="h in deviceUsageHistory" :key="h.date || h.id">
                                                <tr class="hover:bg-gray-50:bg-white/5 transition-colors">
                                                    <td class="px-4 py-3 font-medium" x-text="h.date || h.day || '-' "></td>
                                                    <td class="px-4 py-3 text-right font-bold text-[#1c73a5]"
                                                        x-text="h.total_l ? h.total_l.toFixed(1) : (h.volume_l?.toFixed(1) || '-')">
                                                    </td>
                                                    <td class="px-4 py-3 text-right"
                                                        x-text="h.sessions || h.session_count || '-' "></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-8 text-center text-xs text-secondary opacity-70">
                        <span x-text="'Terakhir update: ' + timeAgo(selectedDevice?.recorded_at || selectedDevice?.last_seen)"></span>
                    </div>
                    </x-ui.card-content>
                </x-ui.card>

                <footer class="text-center pt-10 pb-2 text-xs text-gray-400 font-medium tracking-wide">
                    &copy; {{ date('Y') }} AgriNex Smart Irrigation
                </footer>

            </main>
        </div>
    </div>

    {{-- Mobile bottom nav --}}
    @include('components.bottom-nav')

    @include('components.pwa-components')
    @include('partials.pwa-scripts')

    <script>
        let nodeChartInstance = null;
        function initNodeChart(labels, tempData, soilData) {
            const ctx = document.getElementById('nodeHistoryChart');
            if(!ctx) return;
            
            if (nodeChartInstance) {
                nodeChartInstance.destroy();
            }

            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            nodeChartInstance = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Suhu (°C)',
                            data: tempData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Kelembapan Tanah (%)',
                            data: soilData,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
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
                            labels: { color: textColor, usePointStyle: true, boxWidth: 8 }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor, maxTicksLimit: 12 }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor },
                            title: { display: true, text: 'Suhu (°C)', color: textColor }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { color: textColor },
                            title: { display: true, text: 'Kelembapan (%)', color: textColor },
                            min: 0,
                            max: 100
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
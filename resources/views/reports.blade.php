<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: #E0E5EC;">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>

<body x-data="reportApp()" x-init="init()"
    class="h-full min-h-screen w-full bg-neuBg text-darkText font-sans antialiased selection:bg-brand selection:text-white relative overflow-x-hidden">

    {{-- Global Splash Screen --}}
    @include('components.splash')

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText">

    {{-- App Shell: [Sidebar | Main] --}}
    <div class="relative z-10 flex h-full min-h-screen">

        {{-- Sidebar — hidden on mobile, sticky on desktop --}}
        <div class="hidden md:flex md:flex-shrink-0">
            @include('components.sidebar')
        </div>

        {{-- Main column --}}
        <div class="flex-1 min-w-0 flex flex-col min-h-screen overflow-x-hidden">

            {{-- Sticky header --}}
            <div class="sticky top-0 z-30 w-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] transition-colors duration-300">
                <div class="px-4 md:px-6 xl:px-8 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-darkText">Laporan & Analisis</h1>
                        <p class="text-sm text-lightText mt-1">Data irigasi dan penggunaan air</p>
                    </div>
                    <a href="{{ route('agrinex.dashboard') }}" class="text-lightText hover:text-brand transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Page content --}}
            <main class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[1400px] mx-auto space-y-6">

                {{-- Filter Card --}}
                <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 md:p-8 border border-white/50">
                    <h3 class="text-lg font-bold text-darkText mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter Laporan
                    </h3>
                    
                    <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-lightText ml-1">Tipe Laporan</label>
                            <select name="type" x-model="reportType"
                                class="w-full px-4 py-3 rounded-xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText font-medium">
                                <option value="all">Semua Laporan</option>
                                <option value="irrigation">Irigasi</option>
                                <option value="sensor">Data Sensor</option>
                                <option value="usage">Penggunaan Air</option>
                                <option value="summary">Ringkasan</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-lightText ml-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" x-model="startDate"
                                class="w-full px-4 py-3 rounded-xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-lightText ml-1">Tanggal Akhir</label>
                            <input type="date" name="end_date" x-model="endDate"
                                class="w-full px-4 py-3 rounded-xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText font-medium">
                        </div>
                        <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-brand text-white font-bold shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] active:scale-95 transition-all">
                            Terapkan Filter
                        </button>
                    </form>
                </div>

                {{-- Quick Stats --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    {{-- Total Irigasi --}}
                    <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 border border-white/50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-2xl font-extrabold text-darkText" x-text="summary.total_irrigation_sessions || 0"></h4>
                        <p class="text-sm text-lightText font-medium">Total Sesi Irigasi</p>
                    </div>

                    {{-- Penggunaan Air --}}
                    <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 border border-white/50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-2xl font-extrabold text-darkText" x-text="formatNumber(summary.total_water_usage_l) + ' L'"></h4>
                        <p class="text-sm text-lightText font-medium">Total Penggunaan Air</p>
                    </div>

                    {{-- Total Devices --}}
                    <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 border border-white/50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-2xl font-extrabold text-darkText" x-text="(summary.active_devices || 0) + '/' + (summary.total_devices || 0)"></h4>
                        <p class="text-sm text-lightText font-medium">Devices Aktif</p>
                    </div>

                    {{-- Sensor Readings --}}
                    <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 border border-white/50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-2xl font-extrabold text-darkText" x-text="formatNumber(summary.total_sensor_readings || 0)"></h4>
                        <p class="text-sm text-lightText font-medium">Pembacaan Sensor</p>
                    </div>

                </div>

                {{-- Report List --}}
                <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 md:p-8 border border-white/50">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-darkText">Laporan Tersedia</h3>
                    </div>

                    <div class="space-y-4">
                        
                        {{-- Quick Export Buttons --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            
                            {{-- Sensor Data --}}
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-darkText">Data Sensor</h4>
                                        <p class="text-sm text-lightText">Pembacaan sensor lengkap</p>
                                    </div>
                                </div>
                                <button @click="exportReport('sensor', 'excel')" :disabled="loading"
                                    class="px-4 py-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand font-bold text-sm transition-all disabled:opacity-50">
                                    <span x-show="!loading">Excel</span>
                                    <span x-show="loading">...</span>
                                </button>
                            </div>

                            {{-- Weather Data --}}
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-darkText">Data Cuaca</h4>
                                        <p class="text-sm text-lightText">Kondisi lingkungan</p>
                                    </div>
                                </div>
                                <button @click="exportReport('weather', 'excel')" :disabled="loading"
                                    class="px-4 py-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand font-bold text-sm transition-all disabled:opacity-50">
                                    <span x-show="!loading">Excel</span>
                                    <span x-show="loading">...</span>
                                </button>
                            </div>

                            {{-- Irrigation Logs --}}
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-darkText">Log Irigasi</h4>
                                        <p class="text-sm text-lightText">Riwayat sesi irigasi</p>
                                    </div>
                                </div>
                                <button @click="exportReport('irrigation', 'excel')" :disabled="loading"
                                    class="px-4 py-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand font-bold text-sm transition-all disabled:opacity-50">
                                    <span x-show="!loading">Excel</span>
                                    <span x-show="loading">...</span>
                                </button>
                            </div>

                            {{-- Water Usage --}}
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-darkText">Penggunaan Air</h4>
                                        <p class="text-sm text-lightText">Statistik per device</p>
                                    </div>
                                </div>
                                <button @click="exportReport('usage', 'excel')" :disabled="loading"
                                    class="px-4 py-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand font-bold text-sm transition-all disabled:opacity-50">
                                    <span x-show="!loading">Excel</span>
                                    <span x-show="loading">...</span>
                                </button>
                            </div>

                        </div>

                        {{-- Comprehensive Reports --}}
                        <div class="border-t border-gray-300 pt-4">
                            <h4 class="text-sm font-bold text-lightText mb-3">Laporan Komprehensif</h4>
                            
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button @click="exportReport('comprehensive', 'pdf')" :disabled="loading"
                                    class="flex-1 px-6 py-3 rounded-xl bg-red-500 text-white font-bold shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] hover:bg-red-600 active:scale-95 transition-all disabled:opacity-50">
                                    <span x-show="!loading">📄 Download PDF</span>
                                    <span x-show="loading">Memproses...</span>
                                </button>
                                
                                <button @click="exportReport('comprehensive', 'excel')" :disabled="loading"
                                    class="flex-1 px-6 py-3 rounded-xl bg-green-600 text-white font-bold shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] hover:bg-green-700 active:scale-95 transition-all disabled:opacity-50">
                                    <span x-show="!loading">📊 Download Excel</span>
                                    <span x-show="loading">Memproses...</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <footer class="text-center pt-10 pb-12 text-xs text-lightText font-medium tracking-wide">
                    &copy; {{ date('Y') }} AgriNex Smart Irrigation
                </footer>

            </main>
        </div>
    </div>

    {{-- Mobile bottom nav --}}
    @include('components.bottom-nav')

    </div>

    {{-- Alpine.js Report App --}}
    <script>
    function reportApp() {
        return {
            reportType: '{{ $reportType }}',
            startDate: '{{ $startDate }}',
            endDate: '{{ $endDate }}',
            summary: {},
            reportTypes: [],
            loading: false,
            exportModal: false,

            async init() {
                await this.loadSummary();
                await this.loadReportTypes();
            },

            async loadSummary() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        start_date: this.startDate,
                        end_date: this.endDate
                    });
                    
                    const response = await fetch(`/api/v1/reports/preview?${params}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.summary = data.summary;
                    }
                } catch (error) {
                    console.error('Load summary failed:', error);
                } finally {
                    this.loading = false;
                }
            },

            async loadReportTypes() {
                try {
                    const response = await fetch('/api/v1/reports/types');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.reportTypes = data.types;
                    }
                } catch (error) {
                    console.error('Load report types failed:', error);
                }
            },

            formatNumber(num) {
                if (!num) return '0';
                return new Intl.NumberFormat('id-ID').format(num);
            },

            async exportReport(type, format) {
                this.loading = true;
                try {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("reports.index") }}/export';
                    
                    // CSRF Token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    // Type
                    const typeInput = document.createElement('input');
                    typeInput.type = 'hidden';
                    typeInput.name = 'type';
                    typeInput.value = type;
                    form.appendChild(typeInput);
                    
                    // Format
                    const formatInput = document.createElement('input');
                    formatInput.type = 'hidden';
                    formatInput.name = 'format';
                    formatInput.value = format;
                    form.appendChild(formatInput);
                    
                    // Start Date
                    const startInput = document.createElement('input');
                    startInput.type = 'hidden';
                    startInput.name = 'start_date';
                    startInput.value = this.startDate;
                    form.appendChild(startInput);
                    
                    // End Date
                    const endInput = document.createElement('input');
                    endInput.type = 'hidden';
                    endInput.name = 'end_date';
                    endInput.value = this.endDate;
                    form.appendChild(endInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } catch (error) {
                    console.error('Export failed:', error);
                    alert('Gagal mengekspor laporan');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
    </script>

</body>
</html>

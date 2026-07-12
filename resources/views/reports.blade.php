<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: #E0E5EC;">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>

<body x-data="{ reportType: '{{ $reportType }}', startDate: '{{ $startDate }}', endDate: '{{ $endDate }}' }"
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
                        <h4 class="text-2xl font-extrabold text-darkText">45</h4>
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
                        <h4 class="text-2xl font-extrabold text-darkText">1,250 L</h4>
                        <p class="text-sm text-lightText font-medium">Total Penggunaan Air</p>
                    </div>

                    {{-- Efisiensi --}}
                    <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 border border-white/50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-2xl font-extrabold text-darkText">87%</h4>
                        <p class="text-sm text-lightText font-medium">Efisiensi Irigasi</p>
                    </div>

                    {{-- Durasi Rata-rata --}}
                    <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 border border-white/50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-2xl font-extrabold text-darkText">28 min</h4>
                        <p class="text-sm text-lightText font-medium">Durasi Rata-rata</p>
                    </div>

                </div>

                {{-- Report List --}}
                <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-6 md:p-8 border border-white/50">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-darkText">Laporan Tersedia</h3>
                        <button @click="$dispatch('export-modal')" 
                            class="px-4 py-2 rounded-xl bg-brand text-white text-sm font-bold shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] active:scale-95 transition-all">
                            Ekspor Data
                        </button>
                    </div>

                    <div class="space-y-4">
                        
                        {{-- Report Item 1 --}}
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-darkText">Laporan Irigasi Bulanan</h4>
                                    <p class="text-sm text-lightText">Juni 2026 • 45 sesi • 1,250 L</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand font-bold text-sm transition-all">
                                Lihat
                            </button>
                        </div>

                        {{-- Report Item 2 --}}
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-darkText">Analisis Data Sensor</h4>
                                    <p class="text-sm text-lightText">30 hari terakhir • 4 node aktif</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand font-bold text-sm transition-all">
                                Lihat
                            </button>
                        </div>

                        {{-- Report Item 3 --}}
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-darkText">Efisiensi Penggunaan Air</h4>
                                    <p class="text-sm text-lightText">Q2 2026 • Tren meningkat 12%</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand font-bold text-sm transition-all">
                                Lihat
                            </button>
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

</body>
</html>

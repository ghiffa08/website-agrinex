<!-- Water Usage Charts Section -->
<section class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-0">
        <h2 class="text-2xl font-bold tracking-tight text-darkText" x-text="t('waterUsageHistory')">Riwayat Penggunaan Air</h2>
        <button @click="loadUsage(); loadUsageDaily()"
            class="bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand px-6 py-2.5 rounded-full transition-all duration-300 font-bold text-sm flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span x-text="t('refresh')">Refresh</span>
        </button>
    </div>

    <!-- Skeleton Loading -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-show="loadingUsage">
        <!-- Skeleton Card Kiri -->
        <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8 animate-pulse">
            <div class="mb-6 space-y-3">
                <div class="h-6 bg-gray-300 rounded-full w-1/2"></div>
                <div class="h-4 bg-gray-300 rounded-full w-3/4"></div>
            </div>
            <div class="flex-1 flex items-end justify-between space-x-4 bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-4" style="height: 160px;">
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-1/4"></div>
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-2/4"></div>
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-1/3"></div>
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-3/4"></div>
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-1/2"></div>
            </div>
        </div>
        <!-- Skeleton Card Kanan -->
        <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8 animate-pulse">
            <div class="mb-6 space-y-3">
                <div class="h-6 bg-gray-300 rounded-full w-1/2"></div>
                <div class="h-4 bg-gray-300 rounded-full w-3/4"></div>
            </div>
            <div class="flex-1 flex items-end justify-between space-x-4 bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-4" style="height: 160px;">
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-1/4"></div>
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-2/4"></div>
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-1/3"></div>
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-3/4"></div>
                <div class="w-1/6 bg-gray-300 rounded-t-lg h-1/2"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-show="!loadingUsage" style="display: none;">
        <!-- Card Kiri: 30 Hari -->
        <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-darkText tracking-tight mb-2" x-text="t('last30Days')">Penggunaan 30 Hari Terakhir</h3>
                <p class="text-xs font-medium text-lightText" x-text="t('dailyData30')">Data harian dalam 30 hari terakhir</p>
            </div>
            <div class="flex-1 flex items-center justify-center bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-4" style="height: 180px;">
                <canvas id="usageChart30d" width="100%" height="150"></canvas>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm font-bold text-lightText bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-4 py-2 rounded-xl">
                    <span class="text-brand" x-text="usage.length ? 'Total ' + totalUsage() + ' L' : 'Belum ada data'"></span>
                    <span x-show="usage.length" x-text="' / ' + usage.length + ' hari'"></span>
                </div>
                <div class="text-sm font-bold text-lightText bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-4 py-2 rounded-xl">
                    <span class="text-brand" x-text="'Rata: ' + avgUsage() + ' L'"></span>
                    <span>/hari</span>
                </div>
            </div>
        </div>

        <!-- Card Kanan: 24 Jam -->
        <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-darkText tracking-tight mb-2" x-text="t('last24Hours')">Penggunaan 24 Jam Terakhir</h3>
                <p class="text-xs font-medium text-lightText" x-text="t('hourlyData24')">Data per jam dalam 24 jam terakhir</p>
            </div>
            <div class="flex-1 flex items-center justify-center bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-4" style="height: 180px;">
                <canvas id="usageChart24h" width="100%" height="150"></canvas>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm font-bold text-lightText bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-4 py-2 rounded-xl">
                    <span class="text-brand" x-text="usage24h && usage24h.length ? 'Total ' + totalUsage24h() + ' L' : 'Belum ada data'"></span>
                    <span x-show="usage24h && usage24h.length" x-text="' / 24 jam'"></span>
                </div>
                <div class="text-sm font-bold text-lightText bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-4 py-2 rounded-xl">
                    <span class="text-brand" x-text="'Rata: ' + avgUsage24h() + ' L'"></span>
                    <span>/jam</span>
                </div>
            </div>
        </div>
    </div>
</section>

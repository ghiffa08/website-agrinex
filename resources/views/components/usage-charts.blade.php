{{-- Water Usage Charts Section --}}
<section class="space-y-4">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
 <h2 class="font-semibold text-lg text-primary" x-text="t('waterUsageHistory')">Riwayat Penggunaan Air</h2>
 <button @click="loadUsage(); loadUsageDaily()"
 class="bg-[#E0E5EC] hover:bg-white: text-secondary px-4 py-2 rounded-full transition-all shadow-sm font-medium text-xs border border-white/50" x-text="t('refresh')">Refresh</button>
 </div>

 <!-- Skeleton Loading -->
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-show="loadingUsage">
 <!-- Skeleton Card Kiri -->
 <div class="card !rounded-3xl !p-6 flex flex-col animate-pulse">
 <div class="mb-4 space-y-2">
 <div class="h-5 bg-gray-200 rounded w-1/2"></div>
 <div class="h-3 bg-gray-200 rounded w-3/4"></div>
 </div>
 <div class="flex-1 flex items-end justify-between space-x-2" style="height: 140px;">
 <div class="w-1/6 bg-gray-200 rounded-t h-1/4"></div>
 <div class="w-1/6 bg-gray-200 rounded-t h-2/4"></div>
 <div class="w-1/6 bg-gray-200 rounded-t h-1/3"></div>
 <div class="w-1/6 bg-gray-200 rounded-t h-3/4"></div>
 <div class="w-1/6 bg-gray-200 rounded-t h-1/2"></div>
 </div>
 </div>
 <!-- Skeleton Card Kanan -->
 <div class="card !rounded-3xl !p-6 flex flex-col animate-pulse">
 <div class="mb-4 space-y-2">
 <div class="h-5 bg-gray-200 rounded w-1/2"></div>
 <div class="h-3 bg-gray-200 rounded w-3/4"></div>
 </div>
 <div class="flex-1 flex items-end justify-between space-x-2" style="height: 140px;">
 <div class="w-1/6 bg-gray-200 rounded-t h-1/4"></div>
 <div class="w-1/6 bg-gray-200 rounded-t h-2/4"></div>
 <div class="w-1/6 bg-gray-200 rounded-t h-1/3"></div>
 <div class="w-1/6 bg-gray-200 rounded-t h-3/4"></div>
 <div class="w-1/6 bg-gray-200 rounded-t h-1/2"></div>
 </div>
 </div>
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-show="!loadingUsage" style="display: none;">
 <!-- Card Kiri: 30 Hari -->
 <div class="card !rounded-3xl !p-6 flex flex-col">
 <div class="mb-4">
 <h3 class="text-base font-semibold text-primary mb-1" x-text="t('last30Days')">Penggunaan 30 Hari Terakhir</h3>
 <p class="text-xs text-secondary" x-text="t('dailyData30')">Data harian dalam 30 hari terakhir</p>
 </div>
 <div class="flex-1 flex items-center justify-center" style="height: 140px;">
 <canvas id="usageChart30d" width="100%" height="140"></canvas>
 </div>
 <div class="mt-3 flex items-center justify-between">
 <div class="text-xs text-secondary">
 <span class="font-semibold text-emerald-600"
 x-text="usage.length ? 'Total ' + totalUsage() + ' L' : 'Belum ada data'"></span>
 <span x-show="usage.length" x-text="' / ' + usage.length + ' hari'"></span>
 </div>
 <div class="text-xs text-secondary">
 <span class="font-semibold text-emerald-600"
 x-text="'Rata-rata: ' + avgUsage() + ' L'"></span>
 <span>/hari</span>
 </div>
 </div>
 </div>

 <!-- Card Kanan: 24 Jam -->
 <div class="card !rounded-3xl !p-6 flex flex-col">
 <div class="mb-4">
 <h3 class="text-base font-semibold text-primary mb-1" x-text="t('last24Hours')">Penggunaan 24 Jam Terakhir</h3>
 <p class="text-xs text-secondary" x-text="t('hourlyData24')">Data per jam dalam 24 jam terakhir</p>
 </div>
 <div class="flex-1 flex items-center justify-center" style="height: 140px;">
 <canvas id="usageChart24h" width="100%" height="140"></canvas>
 </div>
 <div class="mt-3 flex items-center justify-between">
 <div class="text-xs text-secondary">
 <span class="font-semibold text-emerald-600"
 x-text="usage24h && usage24h.length ? 'Total ' + totalUsage24h() + ' L' : 'Belum ada data'"></span>
 <span x-show="usage24h && usage24h.length" x-text="' / 24 jam'"></span>
 </div>
 <div class="text-xs text-secondary">
 <span class="font-semibold text-emerald-600"
 x-text="'Rata-rata: ' + avgUsage24h() + ' L'"></span>
 <span>/jam</span>
 </div>
 </div>
 </div>
 </div>
</section>

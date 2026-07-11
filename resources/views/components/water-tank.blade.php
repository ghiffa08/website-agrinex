<!-- Water Tank Skeleton -->
<div>
 <template x-if="loadingTank">
 <section class="mt-6">
 <div class="bg-[#E0E5EC] rounded-[2.5rem] border border-white/60 p-6 xl:p-8 shadow-lg w-full relative overflow-hidden animate-pulse">
 <div class="flex items-center gap-3 mb-6">
 <div class="w-10 h-10 bg-gray-200 rounded-lg"></div>
 <div class="space-y-2 flex-1">
 <div class="h-5 bg-gray-200 rounded w-1/3"></div>
 <div class="h-3 bg-gray-200 rounded w-1/4"></div>
 </div>
 </div>
 <div class="flex flex-col md:flex-row gap-8 items-center">
 <div class="w-[120px] h-[200px] bg-gray-200 rounded-2xl border-4 border-gray-100"></div>
 <div class="flex-1 grid grid-cols-2 gap-4 w-full">
 <div class="h-24 bg-gray-200 rounded-xl"></div>
 <div class="h-24 bg-gray-200 rounded-xl"></div>
 <div class="h-24 bg-gray-200 rounded-xl"></div>
 <div class="h-24 bg-gray-200 rounded-xl"></div>
 </div>
 </div>
 </div>
 </section>
 </template>

 <!-- Water Tank Section (Separate) -->
 <section class="mt-6" x-show="!loadingTank" x-cloak>
 <!-- Actual Tank Data -->
 <x-ui.card x-show="tank" class="border-0 shadow-sm bg-[#E0E5EC] ">
 <x-ui.card-header class="pb-4">
 <div class="flex items-center gap-3">
 <div class="p-2 bg-emerald-100 rounded-lg shadow-sm border border-emerald-200">
 <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
 </svg>
 </div>
 <div>
 <x-ui.card-title x-text="tank ? (tank.tank_name || 'Tangki Air') : 'Tangki Air'"></x-ui.card-title>
 <x-ui.card-description>Monitoring kapasitas air</x-ui.card-description>
 </div>
 </div>
 </x-ui.card-header>

 <x-ui.card-content class="pt-0">
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
 <!-- Visual Tank -->
 <div class="flex justify-center items-center">
 <div class="relative" style="width:120px; height:200px;">
 <div class="absolute inset-0 rounded-2xl border-4 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100 overflow-hidden">
 <!-- Percentage Markers -->
 <template x-for="lvl in [100,75,50,25]" :key="lvl">
 <div class="absolute left-0 right-0 flex items-center" :style="`bottom: calc(${lvl}% - 1px);`">
 <div class="w-full h-px bg-gray-300"></div>
 <div class="absolute -right-10 text-xs text-muted-foreground font-medium" x-text="lvl+'%'"></div>
 </div>
 </template>
 
 <!-- Water Fill -->
 <div class="absolute left-0 right-0 bottom-0 transition-all duration-1000 ease-out"
 :style="`height:${tank ? (tank.percentage || 0) : 0}%;`">
 <div class="absolute inset-0 bg-gradient-to-t from-blue-600 via-blue-500 to-blue-400"></div>
 
 <!-- Wave Animation -->
 <svg class="absolute inset-x-0 -top-3 h-6 w-full opacity-70" viewBox="0 0 120 20" preserveAspectRatio="none">
 <path fill="#3b82f6" fill-opacity="0.7"
 d="M0 10 Q 10 5 20 10 T 40 10 T 60 10 T 80 10 T 100 10 T 120 10 V20 H0 Z">
 <animate attributeName="d" 
 values="M0 10 Q 10 5 20 10 T 40 10 T 60 10 T 80 10 T 100 10 T 120 10 V20 H0 Z;
 M0 10 Q 10 15 20 10 T 40 10 T 60 10 T 80 10 T 100 10 T 120 10 V20 H0 Z;
 M0 10 Q 10 5 20 10 T 40 10 T 60 10 T 80 10 T 100 10 T 120 10 V20 H0 Z"
 dur="3s" repeatCount="indefinite"/>
 </path>
 </svg>
 
 <!-- Percentage Label -->
 <div class="absolute top-2 left-1/2 transform -translate-x-1/2 bg-[#E0E5EC] px-3 py-1 rounded-full shadow-lg">
 <span class="text-sm font-bold text-blue-600" x-text="tank && tank.percentage ? tank.percentage.toFixed(0) + '%' : '0%'">67%</span>
 </div>
 </div>
 </div>
 </div>
 </div>

 <!-- Tank Info -->
 <div class="lg:col-span-2 grid grid-cols-2 gap-4">
 <div class="bg-muted/30 rounded-xl p-4 border border-border">
 <div class="text-sm text-muted-foreground font-medium mb-1">Kapasitas Total</div>
 <div class="text-3xl font-bold text-foreground" x-text="tank ? (tank.capacity || '-') : '-'">-</div>
 <div class="text-xs text-muted-foreground mt-1">cm (tinggi)</div>
 </div>

 <div class="bg-muted/30 rounded-xl p-4 border border-border">
 <div class="text-sm text-muted-foreground font-medium mb-1">Level Saat Ini</div>
 <div class="text-3xl font-bold text-foreground" x-text="tank && tank.water_level_cm ? tank.water_level_cm.toFixed(1) : '-'">-</div>
 <div class="text-xs text-muted-foreground mt-1">cm</div>
 </div>

 <div class="bg-muted/30 rounded-xl p-4 border border-border">
 <div class="text-sm text-muted-foreground font-medium mb-1">Persentase</div>
 <div class="text-3xl font-bold text-foreground" x-text="tank && tank.percentage ? tank.percentage.toFixed(1) + '%' : '-'">-</div>
 <div class="text-xs text-muted-foreground mt-1">dari kapasitas</div>
 </div>

 <div class="bg-muted/30 rounded-xl p-4 border border-border">
 <div class="text-sm text-muted-foreground font-medium mb-1">Status</div>
 <div class="text-3xl font-bold" 
 :class="tank && tank.percentage < 20 ? 'text-destructive' : 'text-emerald-500'"
 x-text="tank && tank.percentage < 20 ? 'Kritis' : 'Normal'">-</div>
 <div class="text-xs text-muted-foreground mt-1" x-text="'Update: ' + (tank ? timeAgo(tank.last_update) : '-')">-</div>
 </div>
 </div>
 </div>
 </x-ui.card-content>
 </x-ui.card>

 <!-- Empty State -->
 <div x-show="!tank" x-cloak class="w-full bg-[#E0E5EC] border border-white/60 rounded-3xl p-8 text-center shadow-sm">
 <div class="inline-block p-4 bg-gray-100 rounded-full mb-4">
 <svg class="w-12 h-12 text-secondary opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
 </svg>
 </div>
 <h3 class="text-lg font-semibold text-primary mb-1">Data Tangki Belum Tersedia</h3>
 <p class="text-sm text-secondary">Belum ada data level air yang diterima dari sensor tangki.</p>
 </div>
 </section>
</div>

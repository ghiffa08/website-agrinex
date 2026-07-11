<section class="flex flex-col gap-4 mt-8">
 <!-- Header -->
 <div class="flex justify-between items-center px-1">
 <h2 class="section-title">Lahan Pantau</h2>
 <a href="#" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700:text-emerald-400 transition">Lihat Semua</a>
 </div>

 <!-- Filter Chips (Static for now, can be made dynamic later) -->
 <div class="flex overflow-x-auto hide-scrollbar gap-3 pb-2 px-1 -mx-1" x-show="devices.length > 0">
 <button class="whitespace-nowrap px-5 py-2 rounded-full bg-emerald-500 text-white font-medium text-sm shadow-sm">Semua</button>
 <template x-for="device in devices" :key="'filter-'+device.id">
 <button class="whitespace-nowrap px-5 py-2 rounded-full bg-[#E0E5EC] text-secondary font-medium text-sm shadow-sm border border-white/60 hover:bg-[#E0E5EC]: transition" x-text="device.group ? 'Zona ' + device.group : device.device_name"></button>
 </template>
 </div>

 <!-- 🖥️ DESKTOP UI: Skeleton Loader (Hidden on Mobile) -->
 <template x-if="loadingDevices || loadingAll">
 <div class="hidden md:grid md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 pb-4">
 <template x-for="i in 5">
 <div class="w-full card !p-0 !rounded-3xl overflow-hidden animate-pulse flex flex-col">
 <div class="h-36 bg-gray-200"></div>
 <div class="p-4 flex flex-col gap-2">
 <div class="h-5 bg-gray-200 rounded w-2/3"></div>
 <div class="h-3 bg-gray-200 rounded w-1/2"></div>
 </div>
 </div>
 </template>
 </div>
 </template>

 <!-- 📱 MOBILE UI: Skeleton Loader (Hidden on Desktop) -->
 <template x-if="loadingDevices || loadingAll">
 <div class="md:hidden flex overflow-x-auto hide-scrollbar gap-4 pb-4 px-1 -mx-1 snap-x w-full">
 <template x-for="i in 3">
 <div class="min-w-[240px] w-[240px] card !p-0 !rounded-3xl overflow-hidden animate-pulse flex flex-col flex-shrink-0 snap-center">
 <div class="h-36 bg-gray-200"></div>
 <div class="p-4 flex flex-col gap-2">
 <div class="h-5 bg-gray-200 rounded w-2/3"></div>
 <div class="h-3 bg-gray-200 rounded w-1/2"></div>
 </div>
 </div>
 </template>
 </div>
 </template>

 <!-- Empty State -->
 <div x-show="!loadingDevices && !loadingAll && devices.length === 0" style="display: none;" class="w-full card !rounded-3xl p-8 text-center">
 <div class="text-secondary opacity-80 mb-3">
 <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 </div>
 <h3 class="text-lg font-bold text-primary mb-1">Tidak Ada Data Lahan</h3>
 <p class="text-sm text-secondary">Belum ada node/lahan yang terdaftar di database.</p>
 </div>

 <!-- Lahan Cards -->
 <div x-show="!loadingDevices && !loadingAll && devices.length > 0" style="display: none;" class="flex overflow-x-auto md:grid md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 hide-scrollbar gap-4 pb-4 px-1 -mx-1 md:mx-0 snap-x">
 <template x-for="(device, index) in devices" :key="device.id">
 <div class="min-w-[240px] md:min-w-0 md:w-full w-[240px] card !p-0 !rounded-3xl overflow-hidden snap-center flex-shrink-0 flex flex-col transition-all duration-300 cursor-pointer hover:-translate-y-1" @click="selectedDevice = device; showDeviceModal = true">
 <!-- Image Area -->
 <div class="h-36 relative bg-gray-100 overflow-hidden">
 <!-- Show image if available, otherwise show gradient placeholder -->
 <template x-if="device.image_url">
 <img :src="device.image_url" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" alt="Lahan" />
 </template>
 <template x-if="!device.image_url">
 <div class="w-full h-full bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
 <svg class="w-12 h-12 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
 </div>
 </template>
 
 <!-- Status Badge -->
 <div class="absolute top-3 left-3 text-white text-xs font-medium px-3 py-1.5 rounded-full flex items-center gap-1.5">
 <span class="w-2 h-2 rounded-full shadow-[0_0_5px]" 
 :class="{
 'bg-emerald-500 shadow-emerald-500': device.status === 'normal' && device.connection_status === 'online',
 'bg-yellow-400 shadow-yellow-400': device.status === 'warning' || device.connection_status === 'idle',
 'bg-red-500 shadow-red-500': device.status === 'no_data' || device.connection_status === 'offline'
 }"></span>
 <span x-text="device.connection_status === 'online' ? 'Aktif' : (device.connection_status === 'idle' ? 'Idle' : 'Offline')"></span>
 </div>
 </div>
 <!-- Info Area -->
 <div class="p-4">
 <h3 class="font-bold text-primary text-base mb-1" x-text="device.group ? 'Zona ' + device.group : device.device_name"></h3>
 <p class="text-xs text-secondary font-medium truncate" x-text="'Perlakuan: ' + (device.treatment_description || '-')"></p>
 <div class="mt-3 flex justify-between items-center">
 <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded" x-show="device.soil_moisture_pct !== null" x-text="'Moisture: ' + device.soil_moisture_pct + '%'"></span>
 </div>
 </div>
 </div>
 </template>
 </div>
</section>

<style>
/* Utility to hide scrollbar but keep functionality */
.hide-scrollbar::-webkit-scrollbar {
 display: none;
}
.hide-scrollbar {
 -ms-overflow-style: none;
 scrollbar-width: none;
}
</style>

<div class="h-full flex flex-col">
 <!-- Skeleton Load -->
 <template x-if="loadingWeather">
 <div class="animate-pulse bg-[#E0E5EC] rounded-3xl p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] h-full flex flex-col justify-between">
 <div class="flex justify-between items-start">
 <div class="h-4 bg-[#93A1B2]/50 rounded w-1/2"></div>
 <div class="h-8 w-8 bg-[#93A1B2]/50 rounded-full shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]"></div>
 </div>
 <div class="flex justify-between items-center mt-6">
 <div class="h-16 bg-[#93A1B2]/50 rounded w-24"></div>
 <div class="h-6 bg-[#93A1B2]/50 rounded w-16"></div>
 </div>
 <div class="grid grid-cols-3 gap-4 mt-8">
 <div class="h-12 bg-[#93A1B2]/50 rounded-2xl shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]"></div>
 <div class="h-12 bg-[#93A1B2]/50 rounded-2xl shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]"></div>
 <div class="h-12 bg-[#93A1B2]/50 rounded-2xl shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]"></div>
 </div>
 </div>
 </template>

 <!-- Actual Content -->
 <template x-if="!loadingWeather">
 <div class="bg-[#E0E5EC] rounded-3xl p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] h-full flex flex-col justify-between">
 <!-- 1. Top Section: Location & Icon -->
 <div class="flex justify-between items-start">
 <div class="flex items-center gap-2">
 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#93A1B2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" />
 <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
 </svg>
 <span class="text-sm font-medium text-[#93A1B2]">Kecamatan Kuningan, ID</span>
 </div>
 <!-- Weather Icon Container -->
 <div class="p-2 rounded-2xl bg-[#E0E5EC] shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
 <img x-show="weatherSummary && weatherSummary.icon" x-cloak :src="weatherSummary?.icon" alt="Weather" class="h-8 w-8 object-contain" />
 <svg x-show="!(weatherSummary && weatherSummary.icon)" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#93A1B2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
 <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
 </svg>
 </div>
 </div>

 <!-- 2. Middle Section: Temp & Status -->
 <div class="flex justify-between items-end mt-8">
 <!-- Temperature -->
 <div class="flex items-start">
 <span class="text-6xl font-extrabold text-[#314051] tracking-tighter" x-text="weatherSummary ? weatherSummary.temp : '-'"></span>
 <span class="text-2xl font-bold text-[#93A1B2] mt-2 ml-1">°C</span>
 </div>
 <!-- Status & Time -->
 <div class="text-right pb-1">
 <div class="text-xl font-bold text-[#00D26A]" x-text="weatherSummary ? weatherSummary.label : 'Loading...'"></div>
 <div class="text-xs text-[#93A1B2] mt-1 font-medium" x-text="clock.dateLong + ' | ' + clock.time"></div>
 </div>
 </div>

 <!-- 3. Bottom Section: 3-column Grid for Stats -->
 <div class="grid grid-cols-3 gap-4 mt-8 pt-4">
 
 <!-- Humidity -->
 <div class="flex flex-col items-center">
 <span class="text-xs font-semibold text-[#93A1B2] uppercase tracking-wider mb-2">Lembap</span>
 <div class="w-full py-3 rounded-2xl bg-[#E0E5EC] shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex justify-center items-center">
 <span class="text-base font-bold text-[#314051]" x-text="weatherSummary ? (weatherSummary.humidity+'%') : '-'"></span>
 </div>
 </div>

 <!-- Rain -->
 <div class="flex flex-col items-center">
 <span class="text-xs font-semibold text-[#93A1B2] uppercase tracking-wider mb-2">Hujan</span>
 <div class="w-full py-3 rounded-2xl bg-[#E0E5EC] shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex justify-center items-center">
 <span class="text-base font-bold text-[#314051]" x-text="weatherSummary ? (weatherSummary.rain+' mm') : '-'"></span>
 </div>
 </div>

 <!-- Wind -->
 <div class="flex flex-col items-center">
 <span class="text-xs font-semibold text-[#93A1B2] uppercase tracking-wider mb-2">Angin</span>
 <div class="w-full py-3 rounded-2xl bg-[#E0E5EC] shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex justify-center items-center">
 <span class="text-base font-bold text-[#314051]" x-text="weatherSummary ? (weatherSummary.wind_speed+' km/h') : '-'"></span>
 </div>
 </div>

 </div>
 </div>
 </template>
</div>

{{-- Devices & Water Tank Section --}}
<x-ui.card class="h-full flex flex-col border-0 shadow-sm bg-[#E0E5EC] ">
 <x-ui.card-header class="flex flex-row items-center justify-between pb-2 border-b border-gray-100">
 <div class="flex items-center gap-3">
 <x-ui.card-title class="flex items-center gap-2">
 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
 <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
 </svg>
 Perangkat
 <x-ui.badge variant="secondary" class="ml-2 bg-emerald-100 text-emerald-700 border-none" x-text="devices.length + ' Node'"></x-ui.badge>
 </x-ui.card-title>
 </div>
 <x-ui.button variant="outline" size="sm" x-on:click="refreshDevices()" class="gap-2 text-xs">
 <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
 </svg>
 Refresh
 </x-ui.button>
 </x-ui.card-header>

 <x-ui.card-content class="pt-6 flex-1">
 <!-- Devices Grid by Lahan Pantau -->
 <div x-show="Object.keys(groupedDevices).length > 0 && !loadingDevices" class="space-y-8">
 <template x-for="(groupDevices, groupName) in groupedDevices" :key="groupName">
 <div>
 <h3 class="text-sm font-semibold text-secondary mb-4 flex items-center gap-2">
 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
 </svg>
 <span x-text="groupName"></span>
 </h3>
 
 <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
 <template x-for="d in groupDevices" :key="d.device_id">
 <x-ui.card class="hover:shadow-md transition-all duration-200 cursor-pointer overflow-hidden flex flex-col"
 x-on:click="window.location.href = '{{ url('/agrinex-dashboard/node') }}/' + d.device_id">
 
 <!-- Header -->
 <x-ui.card-header class="!px-4 !py-3 flex flex-row items-center justify-between border-b border-gray-50 !space-y-0 bg-[#E0E5EC]">
 <x-ui.card-title class="text-sm" x-text="`Node ${d.plot_number}`"></x-ui.card-title>
 <div class="flex items-center gap-1.5">
 <div class="w-1.5 h-1.5 rounded-full" 
 :class="d.connection_state === 'online' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400'"></div>
 <span class="text-[10px] font-semibold tracking-wide uppercase" 
 :class="d.connection_state === 'online' ? 'text-emerald-600' : 'text-gray-500'"
 x-text="d.connection_state"></span>
 </div>
 </x-ui.card-header>

 <!-- Body -->
 <x-ui.card-content class="!p-4 flex-1 bg-[#E0E5EC]">
 <div class="flex items-center justify-between gap-3">
 <!-- Kelembaban -->
 <div class="flex flex-col flex-1 items-center justify-center p-2.5 rounded-lg border border-border bg-blue-50/50">
 <div class="flex items-center gap-1.5 mb-1 text-blue-600">
 <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C12 2 6 9 6 13a6 6 0 0 0 12 0c0-4-6-11-6-11z" /></svg>
 <span class="text-[10px] font-semibold uppercase tracking-wider">Lembap</span>
 </div>
 <div class="text-xl font-bold text-primary" x-text="d.soil_moisture_pct ? Math.round(d.soil_moisture_pct) + '%' : '-'"></div>
 </div>

 <!-- Suhu -->
 <div class="flex flex-col flex-1 items-center justify-center p-2.5 rounded-lg border border-border bg-orange-50/50">
 <div class="flex items-center gap-1.5 mb-1 text-orange-600">
 <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
 <span class="text-[10px] font-semibold uppercase tracking-wider">Suhu</span>
 </div>
 <div class="text-xl font-bold text-primary" x-text="d.air_temp_c ? Math.round(d.air_temp_c) + '°' : (d.soil_temp_c ? Math.round(d.soil_temp_c) + '°' : '-')"></div>
 </div>
 </div>
 
 <div class="mt-4 flex justify-between items-center text-xs px-1">
 <div class="text-muted-foreground">Target: <span class="font-medium text-foreground" x-text="d.fc_target ? Math.round(d.fc_target) + '%' : '-'"></span></div>
 <div class="text-muted-foreground">Thres: <span class="font-medium text-foreground" x-text="d.threshold ? Math.round(d.threshold) + '%' : '-'"></span></div>
 </div>
 </x-ui.card-content>
 
 <div class="px-4 py-2 bg-muted/30 border-t border-border text-[10px] text-center text-muted-foreground">
 <span x-text="'Diperbarui ' + timeAgo(d.recorded_at || d.last_seen)"></span>
 </div>
 </x-ui.card>
 </template>
 </div>
 </div>
 </template>
 </div>

 <!-- Loading State -->
 <template x-if="loadingDevices">
 <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
 <template x-for="i in 3" :key="'skeleton-'+i">
 <div class="h-44 bg-gray-100 rounded-xl animate-pulse"></div>
 </template>
 </div>
 </template>

 <!-- Empty State -->
 <div x-show="!devices.length && !loadingDevices" x-cloak class="flex flex-col items-center justify-center py-12">
 <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
 </svg>
 <p class="text-sm font-medium text-secondary">Belum ada perangkat terhubung</p>
 </div>
 </x-ui.card-content>
</x-ui.card>

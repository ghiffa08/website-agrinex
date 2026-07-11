<div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
    <div class="flex justify-between items-center mb-2">
        <div class="flex items-center gap-3">
            <h3 class="text-xl font-bold tracking-tight text-darkText">Perangkat</h3>
            <div class="px-3 py-1 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-xs font-bold text-darkText">
                <span x-text="typeof totalDevices !== 'undefined' ? totalDevices : (devices ? devices.length : 0)"></span> Node
            </div>
        </div>
        <!-- Refresh Button -->
        <button @click="fetchDevices" class="p-2 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-brand hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        </button>
    </div>

    <!-- Alpine Loop for groupedDevices -->
    <template x-for="(group, area) in groupedDevices" :key="area">
        <div class="mb-6 last:mb-0">
            <div class="text-sm font-bold text-lightText mb-6" x-text="area || 'Unassigned'"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Loop over nodes in group -->
                <template x-for="node in group" :key="node.id">
                    <!-- Node Card -->
                    <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col gap-6">
                        <!-- Node Header -->
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-lightText" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                                <span class="font-bold text-darkText text-lg" x-text="node.name"></span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1 rounded-full shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]">
                                <div class="w-2 h-2 rounded-full" 
                                     :class="node.connection_state === 'connected' ? 'bg-[#00D26A]' : 'bg-[#EF4444] animate-pulse'"></div>
                                <span class="text-[10px] font-bold uppercase tracking-wider"
                                      :class="node.connection_state === 'connected' ? 'text-[#00D26A]' : 'text-[#EF4444]'"
                                      x-text="node.connection_state === 'connected' ? 'ONLINE' : 'OFFLINE'"></span>
                            </div>
                        </div>
                        
                        <!-- Metrics Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- LEMBAP -->
                            <div class="bg-neuBg rounded-2xl p-4 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center">
                                <span class="text-[10px] font-bold text-lightText uppercase mb-1">LEMBAP</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-extrabold text-darkText" x-text="node.soil_moisture_pct ?? '--'"></span>
                                    <span class="text-sm font-bold text-brand">%</span>
                                </div>
                            </div>
                            
                            <!-- SUHU -->
                            <div class="bg-neuBg rounded-2xl p-4 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center">
                                <span class="text-[10px] font-bold text-lightText uppercase mb-1">SUHU</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-extrabold text-darkText" x-text="node.air_temp_c ?? '--'"></span>
                                    <span class="text-sm font-bold text-brand">°C</span>
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="flex flex-col gap-2 pt-2 border-t border-[#a3b1c6]/30">
                            <div class="flex justify-between text-xs">
                                <span class="text-lightText font-semibold">Target</span>
                                <span class="text-darkText font-bold" x-text="(node.target ?? '--') + '%'"></span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-lightText font-semibold">Thres</span>
                                <span class="text-darkText font-bold" x-text="'±' + (node.threshold ?? '--') + '%'"></span>
                            </div>
                            <div class="flex justify-between text-[10px] mt-2">
                                <span class="text-lightText">Last updated: <span class="font-semibold text-darkText" x-text="node.last_updated ?? 'Just now'"></span></span>
                                <button class="text-brand font-bold uppercase tracking-wider hover:underline">Edit</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>

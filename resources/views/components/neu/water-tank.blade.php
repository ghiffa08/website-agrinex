<div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-xl font-bold tracking-tight text-darkText">Tangki Air Utama</h3>
        <button @click="fetchTank" class="p-3 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-brand hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Left Side: Progress Bar -->
        <div class="md:col-span-3 flex justify-center">
            <div class="flex flex-col items-center gap-4">
                <div class="w-16 h-64 rounded-[2rem] bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] p-2 flex items-end">
                    <div class="w-full rounded-[1.5rem] bg-brand transition-all duration-1000 ease-in-out" :style="`height: ${tank.percentage ?? 0}%`"></div>
                </div>
                <div class="px-5 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-sm font-bold text-darkText tracking-wide">
                    Air
                </div>
            </div>
        </div>

        <!-- Right Side: Metrics 2x2 Grid -->
        <div class="md:col-span-9 grid grid-cols-2 gap-6 h-full items-center">
            <!-- Kapasitas Total -->
            <div class="bg-neuBg rounded-3xl p-6 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col justify-center">
                <span class="text-xs font-bold text-lightText uppercase tracking-wider mb-2">Kapasitas Total</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-extrabold text-darkText" x-text="tank.capacity ?? '--'"></span>
                    <span class="text-sm font-bold text-brand">Liter</span>
                </div>
            </div>
            
            <!-- Level Saat Ini -->
            <div class="bg-neuBg rounded-3xl p-6 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col justify-center">
                <span class="text-xs font-bold text-lightText uppercase tracking-wider mb-2">Level Saat Ini</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-extrabold text-darkText" x-text="tank.current_level ?? '--'"></span>
                    <span class="text-sm font-bold text-brand">Liter</span>
                </div>
            </div>

            <!-- Persentase -->
            <div class="bg-neuBg rounded-3xl p-6 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col justify-center">
                <span class="text-xs font-bold text-lightText uppercase tracking-wider mb-2">Persentase</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-extrabold text-darkText" x-text="tank.percentage ?? '--'"></span>
                    <span class="text-sm font-bold text-brand">%</span>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-neuBg rounded-3xl p-6 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col justify-center">
                <span class="text-xs font-bold text-lightText uppercase tracking-wider mb-2">Status</span>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full animate-pulse" :class="(tank.status || '').toLowerCase() === 'kritis' ? 'bg-[#EF4444]' : 'bg-[#00D26A]'"></div>
                    <span class="text-2xl font-extrabold tracking-tight" :class="(tank.status || '').toLowerCase() === 'kritis' ? 'text-[#EF4444]' : 'text-[#00D26A]'" x-text="tank.status ?? '--'"></span>
                </div>
            </div>
        </div>
    </div>
</div>

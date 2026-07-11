<div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col items-center justify-center text-center h-full">
    <!-- Top: Location & Icon -->
    <div class="flex flex-col items-center gap-2 mb-6">
        <div class="flex items-center gap-1 text-lightText">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-xs font-bold uppercase tracking-wide">Kecamatan Kuningan, ID</span>
        </div>
        <!-- Weather Icon (Placeholder Sun) -->
        <div class="w-16 h-16 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] flex items-center justify-center text-yellow-500 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
    </div>

    <!-- Middle: Temp & Status -->
    <div class="mb-8">
        <div class="flex items-start justify-center">
            <span class="text-6xl font-extrabold text-[#314051] tracking-tighter" x-text="weatherSummary.temp ?? '--'"></span>
            <span class="text-2xl font-bold text-[#314051] mt-1">°C</span>
        </div>
        <div class="text-[#00D26A] font-bold text-lg mt-2 uppercase tracking-widest" x-text="weatherSummary.label ?? '--'"></div>
    </div>

    <!-- Bottom: 3-column Metrics Grid -->
    <div class="grid grid-cols-3 gap-4 w-full mt-auto">
        <!-- LEMBAP -->
        <div class="bg-neuBg rounded-2xl p-4 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center justify-center">
            <span class="text-[10px] font-bold text-lightText uppercase mb-1">LEMBAP</span>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-extrabold text-darkText" x-text="weatherSummary.humidity ?? '--'"></span>
                <span class="text-xs font-bold text-brand">%</span>
            </div>
        </div>

        <!-- HUJAN -->
        <div class="bg-neuBg rounded-2xl p-4 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center justify-center">
            <span class="text-[10px] font-bold text-lightText uppercase mb-1">HUJAN</span>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-extrabold text-darkText" x-text="weatherSummary.rain ?? '--'"></span>
                <span class="text-xs font-bold text-brand">mm</span>
            </div>
        </div>

        <!-- ANGIN -->
        <div class="bg-neuBg rounded-2xl p-4 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center justify-center">
            <span class="text-[10px] font-bold text-lightText uppercase mb-1">ANGIN</span>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-extrabold text-darkText" x-text="weatherSummary.wind_speed ?? '--'"></span>
                <span class="text-xs font-bold text-brand">km/h</span>
            </div>
        </div>
    </div>
</div>

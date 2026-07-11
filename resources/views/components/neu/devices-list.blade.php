<div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold tracking-tight text-darkText">Perangkat / Nodes</h3>
        <span class="text-sm font-bold text-lightText">Total: 3</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Node Card 1 -->
        <div class="bg-neuBg rounded-3xl p-5 shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <span class="font-bold text-darkText">Node 1</span>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-brand"></div>
                    <span class="text-xs font-bold text-brand uppercase">Online</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <!-- Node Metric 1 (Suhu) -->
                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center">
                    <span class="text-[10px] font-bold text-lightText uppercase mb-1">Suhu</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg font-extrabold text-darkText">25</span>
                        <span class="text-xs font-bold text-brand">°C</span>
                    </div>
                </div>
                
                <!-- Node Metric 2 (Lembap) -->
                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center">
                    <span class="text-[10px] font-bold text-lightText uppercase mb-1">Lembap</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg font-extrabold text-darkText">68</span>
                        <span class="text-xs font-bold text-brand">%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Node Card 2 (Offline Example) -->
        <div class="bg-neuBg rounded-3xl p-5 shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <span class="font-bold text-darkText">Node 2</span>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <span class="text-xs font-bold text-red-500 uppercase">Offline</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center">
                    <span class="text-[10px] font-bold text-lightText uppercase mb-1">Suhu</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg font-extrabold text-lightText">--</span>
                    </div>
                </div>
                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center">
                    <span class="text-[10px] font-bold text-lightText uppercase mb-1">Lembap</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg font-extrabold text-lightText">--</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Node Card 3 -->
        <div class="bg-neuBg rounded-3xl p-5 shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <span class="font-bold text-darkText">Node 3</span>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-brand"></div>
                    <span class="text-xs font-bold text-brand uppercase">Online</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center">
                    <span class="text-[10px] font-bold text-lightText uppercase mb-1">Suhu</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg font-extrabold text-darkText">23</span>
                        <span class="text-xs font-bold text-brand">°C</span>
                    </div>
                </div>
                <div class="bg-neuBg rounded-2xl p-3 shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] flex flex-col items-center">
                    <span class="text-[10px] font-bold text-lightText uppercase mb-1">Lembap</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg font-extrabold text-darkText">70</span>
                        <span class="text-xs font-bold text-brand">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

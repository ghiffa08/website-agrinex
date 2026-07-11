<!-- Charts Section -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Light Intensity Chart -->
    <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold tracking-tight text-darkText" x-text="t('lightIntensity')">Light Intensity</h3>
                <span id="lightChartBadge" class="text-[10px] px-3 py-1 rounded-xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] text-lightText font-semibold">
                    Loading...
                </span>
            </div>
            <div class="flex gap-6 text-sm">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-cyan-400 shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]"></div>
                    <span class="text-lightText font-bold">LI2</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-red-500 shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]"></div>
                    <span class="text-lightText font-bold">LI1</span>
                </div>
            </div>
        </div>
        <div class="relative bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-4" style="height: 320px;">
            <div x-show="loadingCharts" class="absolute inset-0 bg-neuBg z-10 p-4 animate-pulse rounded-2xl flex items-center justify-center">
                <div class="w-full h-full flex items-end justify-around p-2 gap-4 opacity-50">
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 30%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 60%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 40%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 80%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 50%"></div>
                </div>
            </div>
            <canvas id="lightIntensityChart"></canvas>
        </div>
        <div class="mt-4 text-center">
            <p class="text-xs text-lightText font-medium">Data 7 hari terakhir, diperbarui otomatis setiap 10 menit</p>
        </div>
    </div>

    <!-- Water Level Chart -->
    <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold tracking-tight text-darkText" x-text="t('waterLevel')">Water Level</h3>
                <span id="waterChartBadge" class="text-[10px] px-3 py-1 rounded-xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] text-lightText font-semibold">
                    Loading...
                </span>
            </div>
            <div class="flex gap-6 text-sm">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-lime-500 shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]"></div>
                    <span class="text-lightText font-bold">WL</span>
                </div>
            </div>
        </div>
        <div class="relative bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-4" style="height: 320px;">
            <div x-show="loadingCharts" class="absolute inset-0 bg-neuBg z-10 p-4 animate-pulse rounded-2xl flex items-center justify-center">
                <div class="w-full h-full flex items-end justify-around p-2 gap-4 opacity-50">
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 30%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 60%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 40%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 80%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 50%"></div>
                </div>
            </div>
            <canvas id="waterLevelChart"></canvas>
        </div>
        <div class="mt-4 text-center">
            <p class="text-xs text-lightText font-medium">Data 7 hari terakhir, diperbarui otomatis setiap 10 menit</p>
        </div>
    </div>
</section>

<!-- Additional Environmental Charts -->
<section class="grid grid-cols-1 gap-8">
    <!-- Soil Moisture Chart -->
    <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold tracking-tight text-darkText" x-text="t('soilMoisture')">Soil Moisture</h3>
                <span id="soilChartBadge" class="text-[10px] px-3 py-1 rounded-xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] text-lightText font-semibold">
                    Loading...
                </span>
            </div>
            <div class="flex flex-wrap gap-4 text-xs">
                <template x-for="(sensor, idx) in soilMoistureSensors" :key="sensor.id">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]" :style="'background-color: ' + sensor.color"></div>
                        <span class="text-lightText font-bold" x-text="sensor.label"></span>
                    </div>
                </template>
            </div>
        </div>
        <div class="relative bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-4" style="height: 320px;">
            <div x-show="loadingCharts" class="absolute inset-0 bg-neuBg z-10 p-4 animate-pulse rounded-2xl flex items-center justify-center">
                <div class="w-full h-full flex items-end justify-around p-2 gap-4 opacity-50">
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 30%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 60%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 40%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 80%"></div>
                    <div class="w-full bg-gray-300 rounded-t-xl" style="height: 50%"></div>
                </div>
            </div>
            <canvas id="soilMoistureChart"></canvas>
        </div>
        <div class="mt-4 text-center">
            <p class="text-xs text-lightText font-medium">Kelembapan tanah dari berbagai sensor (Data 7 hari terakhir)</p>
        </div>
    </div>

    <!-- Temperature and Humidity Charts (Side by Side) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Temperature Chart -->
        <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold tracking-tight text-darkText" x-text="t('temperature')">Temperature</h3>
                    <span id="tempChartBadge" class="text-[10px] px-3 py-1 rounded-xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] text-lightText font-semibold">
                        Loading...
                    </span>
                </div>
                <div class="flex gap-6 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-purple-500 shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]"></div>
                        <span class="text-lightText font-bold">T1</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-cyan-500 shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]"></div>
                        <span class="text-lightText font-bold">T2</span>
                    </div>
                </div>
            </div>
            <div class="relative bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-4" style="height: 280px;">
                <div x-show="loadingCharts" class="absolute inset-0 bg-neuBg z-10 p-4 animate-pulse rounded-2xl flex items-center justify-center">
                    <div class="w-full h-full flex items-end justify-around p-2 gap-4 opacity-50">
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 30%"></div>
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 60%"></div>
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 40%"></div>
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 80%"></div>
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 50%"></div>
                    </div>
                </div>
                <canvas id="temperatureChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <p class="text-xs text-lightText font-medium">Suhu dari sensor (Data 7 hari terakhir)</p>
            </div>
        </div>

        <!-- Humidity Chart -->
        <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold tracking-tight text-darkText" x-text="t('humidity')">Humidity</h3>
                    <span id="humidityChartBadge" class="text-[10px] px-3 py-1 rounded-xl bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] text-lightText font-semibold">
                        Loading...
                    </span>
                </div>
                <div class="flex gap-6 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-blue-500 shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]"></div>
                        <span class="text-lightText font-bold">H2</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-orange-500 shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]"></div>
                        <span class="text-lightText font-bold">H1</span>
                    </div>
                </div>
            </div>
            <div class="relative bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-4" style="height: 280px;">
                <div x-show="loadingCharts" class="absolute inset-0 bg-neuBg z-10 p-4 animate-pulse rounded-2xl flex items-center justify-center">
                    <div class="w-full h-full flex items-end justify-around p-2 gap-4 opacity-50">
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 30%"></div>
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 60%"></div>
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 40%"></div>
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 80%"></div>
                        <div class="w-full bg-gray-300 rounded-t-xl" style="height: 50%"></div>
                    </div>
                </div>
                <canvas id="humidityChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <p class="text-xs text-lightText font-medium">Kelembapan dari sensor (Data 7 hari terakhir)</p>
            </div>
        </div>
    </div>
</section>

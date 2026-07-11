<section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <!-- Light Intensity Chart -->
 <div class="card !p-6 flex flex-col group">
 <div class="mb-4">
 <div class="flex items-center justify-between mb-3">
 <h3 class="text-primary text-xl font-bold" x-text="t('lightIntensity')">Light Intensity</h3>
 <span id="lightChartBadge" class="text-[10px] px-2 py-0.5 rounded border border-gray-200 bg-[#E0E5EC] text-secondary">
 Loading...
 </span>
 </div>
 <div class="flex gap-6 text-sm">
 <div class="chart-legend-item flex items-center gap-2">
 <div class="w-4 h-4 rounded bg-cyan-400"></div>
 <span class="text-secondary font-medium">LI2</span>
 </div>
 <div class="chart-legend-item flex items-center gap-2">
 <div class="w-4 h-4 rounded bg-red-500"></div>
 <span class="text-secondary font-medium">LI1</span>
 </div>
 </div>
 </div>
 <div class="relative bg-[#E0E5EC] border border-white/60 rounded-2xl p-4" style="height: 320px;">
 <div x-show="loadingCharts" class="absolute inset-0 bg-[#E0E5EC] z-10 p-4 animate-pulse rounded-lg flex items-center justify-center">
 <div class="w-full h-full border-b-2 border-l-2 border-gray-200 flex items-end justify-around p-2 gap-4 opacity-50">
 <div class="w-full bg-gray-300 rounded-t" style="height: 30%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 60%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 40%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 80%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 50%"></div>
 </div>
 </div>
 <canvas id="lightIntensityChart"></canvas>
 </div>
 <div class="mt-3 text-center">
 <p class="text-xs text-secondary">Data 7 hari terakhir, diperbarui otomatis setiap 10 menit</p>
 </div>
 </div>

 <!-- Water Level Chart -->
 <div class="card !p-6 flex flex-col group">
 <div class="mb-4">
 <div class="flex items-center justify-between mb-3">
 <h3 class="text-primary text-xl font-bold" x-text="t('waterLevel')">Water Level</h3>
 <span id="waterChartBadge" class="text-[10px] px-2 py-0.5 rounded border border-gray-200 bg-[#E0E5EC] text-secondary">
 Loading...
 </span>
 </div>
 <div class="flex gap-6 text-sm">
 <div class="chart-legend-item flex items-center gap-2">
 <div class="w-4 h-4 rounded bg-lime-500"></div>
 <span class="text-secondary font-medium">WL</span>
 </div>
 </div>
 </div>
 <div class="relative bg-[#E0E5EC] border border-white/60 rounded-2xl p-4" style="height: 320px;">
 <div x-show="loadingCharts" class="absolute inset-0 bg-[#E0E5EC] z-10 p-4 animate-pulse rounded-lg flex items-center justify-center">
 <div class="w-full h-full border-b-2 border-l-2 border-gray-200 flex items-end justify-around p-2 gap-4 opacity-50">
 <div class="w-full bg-gray-300 rounded-t" style="height: 30%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 60%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 40%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 80%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 50%"></div>
 </div>
 </div>
 <canvas id="waterLevelChart"></canvas>
 </div>
 <div class="mt-3 text-center">
 <p class="text-xs text-secondary">Data 7 hari terakhir, diperbarui otomatis setiap 10 menit</p>
 </div>
 </div>
</section>

<!-- Additional Environmental Charts -->
<section class="grid grid-cols-1 gap-6">
 <!-- Soil Moisture Chart -->
 <div class="card !p-6 flex flex-col group">
 <div class="mb-4">
 <div class="flex items-center justify-between">
 <h3 class="text-primary text-xl font-bold" x-text="t('soilMoisture')">Soil Moisture</h3>
 <span id="soilChartBadge" class="text-[10px] px-2 py-0.5 rounded border border-gray-200 bg-[#E0E5EC] text-secondary">
 Loading...
 </span>
 </div>
 <div class="flex flex-wrap gap-3 text-xs">
 <template x-for="(sensor, idx) in soilMoistureSensors" :key="sensor.id">
 <div class="flex items-center gap-2">
 <div class="w-3 h-3 rounded" :style="'background-color: ' + sensor.color"></div>
 <span class="text-secondary font-medium" x-text="sensor.label"></span>
 </div>
 </template>
 </div>
 </div>
 <div class="relative bg-[#E0E5EC] border border-white/60 rounded-2xl p-4" style="height: 320px;">
 <div x-show="loadingCharts" class="absolute inset-0 bg-[#E0E5EC] z-10 p-4 animate-pulse rounded-lg flex items-center justify-center">
 <div class="w-full h-full border-b-2 border-l-2 border-gray-200 flex items-end justify-around p-2 gap-4 opacity-50">
 <div class="w-full bg-gray-300 rounded-t" style="height: 30%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 60%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 40%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 80%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 50%"></div>
 </div>
 </div>
 <canvas id="soilMoistureChart"></canvas>
 </div>
 <div class="mt-3 text-center">
 <p class="text-xs text-secondary">Kelembapan tanah dari berbagai sensor (Data 7 hari terakhir)</p>
 </div>
 </div>

 <!-- Temperature and Humidity Charts (Side by Side) -->
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <!-- Temperature Chart -->
 <div class="card !p-6 flex flex-col group">
 <div class="mb-4">
 <div class="flex items-center justify-between mb-3">
 <h3 class="text-primary text-xl font-bold" x-text="t('temperature')">Temperature</h3>
 <span id="tempChartBadge" class="text-[10px] px-2 py-0.5 rounded border border-gray-200 bg-[#E0E5EC] text-secondary">
 Loading...
 </span>
 </div>
 <div class="flex gap-4 text-sm">
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 rounded bg-purple-500"></div>
 <span class="text-secondary font-medium">T1</span>
 </div>
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 rounded bg-cyan-500"></div>
 <span class="text-secondary font-medium">T2</span>
 </div>
 </div>
 </div>
 <div class="relative bg-[#E0E5EC] border border-white/60 rounded-2xl p-4" style="height: 280px;">
 <div x-show="loadingCharts" class="absolute inset-0 bg-[#E0E5EC] z-10 p-4 animate-pulse rounded-lg flex items-center justify-center">
 <div class="w-full h-full border-b-2 border-l-2 border-gray-200 flex items-end justify-around p-2 gap-4 opacity-50">
 <div class="w-full bg-gray-300 rounded-t" style="height: 30%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 60%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 40%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 80%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 50%"></div>
 </div>
 </div>
 <canvas id="temperatureChart"></canvas>
 </div>
 <div class="mt-3 text-center">
 <p class="text-xs text-secondary">Suhu dari sensor (Data 7 hari terakhir)</p>
 </div>
 </div>

 <!-- Humidity Chart -->
 <div class="card !p-6 flex flex-col group">
 <div class="mb-4">
 <div class="flex items-center justify-between mb-3">
 <h3 class="text-primary text-xl font-bold" x-text="t('humidity')">Humidity</h3>
 <span id="humidityChartBadge" class="text-[10px] px-2 py-0.5 rounded border border-gray-200 bg-[#E0E5EC] text-secondary">
 Loading...
 </span>
 </div>
 <div class="flex gap-4 text-sm">
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 rounded bg-blue-500"></div>
 <span class="text-secondary font-medium">H2</span>
 </div>
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 rounded bg-orange-500"></div>
 <span class="text-secondary font-medium">H1</span>
 </div>
 </div>
 </div>
 <div class="relative bg-[#E0E5EC] border border-white/60 rounded-2xl p-4" style="height: 280px;">
 <div x-show="loadingCharts" class="absolute inset-0 bg-[#E0E5EC] z-10 p-4 animate-pulse rounded-lg flex items-center justify-center">
 <div class="w-full h-full border-b-2 border-l-2 border-gray-200 flex items-end justify-around p-2 gap-4 opacity-50">
 <div class="w-full bg-gray-300 rounded-t" style="height: 30%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 60%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 40%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 80%"></div>
 <div class="w-full bg-gray-300 rounded-t" style="height: 50%"></div>
 </div>
 </div>
 <canvas id="humidityChart"></canvas>
 </div>
 <div class="mt-3 text-center">
 <p class="text-xs text-secondary">Kelembapan dari sensor (Data 7 hari terakhir)</p>
 </div>
 </div>
 </div>
</section>

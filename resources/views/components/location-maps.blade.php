<!-- Location Maps Section -->
<section class="grid lg:grid-cols-2 gap-8">
    <!-- Satellite View Kiri (Leaflet) -->
    <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8 group relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold tracking-tight text-darkText">Citra Satelit Lahan</h2>
            <div class="flex gap-2">
                <button @click="switchSatelliteLayer('esri')" 
                    :class="satelliteProvider === 'esri' ? 'shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]' : 'shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]'"
                    class="text-[10px] font-bold px-3 py-1 rounded-xl bg-neuBg text-brand transition-all">
                    Esri
                </button>
                <button @click="switchSatelliteLayer('google')" 
                    :class="satelliteProvider === 'google' ? 'shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]' : 'shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]'"
                    class="text-[10px] font-bold px-3 py-1 rounded-xl bg-neuBg text-brand transition-all">
                    Google
                </button>
            </div>
        </div>
        <div class="relative bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-2">
            <div id="satelliteMap" class="w-full rounded-xl overflow-hidden" 
                style="height:340px; min-height:300px; z-index: 1; position: relative;"></div>
            
            <div class="absolute bottom-4 left-4 flex flex-wrap gap-2 pointer-events-none z-10">
                <template x-for="m in topMetricCards.filter(x=>['temp','humidity','light','wind'].includes(x.key))" :key="m.key">
                    <div class="bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-[10px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-2 pointer-events-auto cursor-help"
                        :data-metric-chip="m.key">
                        <span class="w-4 h-4 text-brand" x-html="metricIcon(m.key)"></span>
                        <span class="text-darkText" x-text="m.display"></span>
                    </div>
                </template>
            </div>
        </div>
        <p class="mt-4 text-xs font-medium text-lightText leading-relaxed">
            Tampilan Citra Satelit interaktif area lahan di desa Geresik. Gunakan mouse untuk zoom & pan. Tanpa API key Google Maps.
        </p>
    </div>
    
    <!-- Denah Desa Kanan -->
    <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8 relative group">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4 sm:gap-0">
            <h2 class="text-xl font-bold tracking-tight text-darkText">Denah Desa (Interaktif)</h2>
            <div class="flex gap-2">
                <a :href="googleMapsLink" target="_blank" rel="noopener"
                    class="text-xs font-bold px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand transition-all duration-300">
                    Buka di Google Maps
                </a>
            </div>
        </div>
        <div class="relative bg-neuBg shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] rounded-2xl p-2">
            <div id="leafletMap" class="w-full rounded-xl overflow-hidden"
                style="height:340px; min-height:300px; z-index: 1; position: relative;"></div>
            <button @click="initLeaflet()"
                class="absolute top-4 right-4 text-[10px] font-bold px-3 py-1.5 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] text-lightText transition-all duration-300 z-10"
                x-show="!leafletInited">Muat Ulang</button>
        </div>
        <p class="mt-4 text-xs font-medium text-lightText">Batas poligon desa Geresik dan marker lokasi pusat (estimasi). Interaktif tanpa API key.</p>
        <p class="mt-1 text-[10px] font-medium text-lightText opacity-80">Sumber data: OpenStreetMap & inisialisasi manual.</p>
    </div>
</section>

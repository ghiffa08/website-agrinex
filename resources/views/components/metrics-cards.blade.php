<!-- Metrics Gauge Cards Section -->
<section>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold tracking-tight text-darkText" x-text="t('environmentSummary')">Ringkasan Lingkungan</h2>
        <div class="text-xs text-lightText"
            x-text="lastUpdated ? ('Update: '+ lastUpdated.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})) : ''">
        </div>
    </div>
    
    <!-- Skeleton Load -->
    <template x-if="loadingAll">
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-6">
            <template x-for="i in 6" :key="'skeleton-'+i">
                <div class="flex flex-col h-36 rounded-3xl bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-5 animate-pulse">
                    <div class="flex items-center gap-3 opacity-70 mb-auto">
                        <div class="h-8 w-8 rounded-xl bg-gray-300"></div>
                        <div class="h-3 w-16 bg-gray-300 rounded-full"></div>
                    </div>
                    <div class="mt-4">
                        <div class="h-6 w-20 bg-gray-300 rounded-full mb-2"></div>
                        <div class="h-3 w-24 bg-gray-300 rounded-full"></div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- Actual Cards -->
    <div x-show="!loadingAll" x-cloak class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-6">
        <template x-for="m in topMetricCards" :key="m.key">
            <div class="relative flex flex-col overflow-hidden group cursor-pointer rounded-3xl bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] hover:shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] transition-all duration-300 p-5"
                x-on:click="$dispatch('open-metric', { metric: m.key })">

                <!-- Header with icon and title -->
                <div class="relative z-10 flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand">
                            <div class="h-5 w-5" x-html="metricIcon(m.key)"></div>
                        </div>
                        <h3 class="text-xs font-bold text-darkText" x-text="m.label"></h3>
                    </div>
                </div>

                <div class="relative z-10 flex-1 flex flex-col justify-end">
                    <!-- Gauge Type - Circular Design -->
                    <template x-if="m.type==='gauge'">
                        <div class="flex flex-col items-center">
                            <!-- Large circular gauge (inset neumorphism) -->
                            <div class="relative w-20 h-20 mb-3 rounded-full bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center p-2">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 80 80">
                                    <circle cx="40" cy="40" r="34" stroke="transparent" stroke-width="4" fill="none" />
                                    <!-- Progress circle -->
                                    <circle cx="40" cy="40" r="34" stroke="#00D26A"
                                        stroke-width="4" fill="none" stroke-linecap="round"
                                        :stroke-dasharray="`${2 * Math.PI * 34}`"
                                        :stroke-dashoffset="`${2 * Math.PI * 34 * (1 - m.pct / 100)}`"
                                        class="transition-all duration-1000" />
                                </svg>
                                <!-- Center value -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-sm font-extrabold text-darkText" x-text="m.display"></span>
                                    <span class="text-[9px] text-lightText font-semibold mt-0.5" x-text="m.unit"></span>
                                </div>
                            </div>
                            <!-- Range indicators -->
                            <div class="flex items-center justify-between w-full text-[10px] text-lightText font-medium px-1">
                                <span x-text="m.min + m.unit"></span>
                                <span class="font-bold text-brand" x-text="Math.round(m.pct) + '%'"></span>
                                <span x-text="m.max + m.unit"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Linear Type - Horizontal Bar Design -->
                    <template x-if="m.type==='linear'">
                        <div class="flex flex-col w-full">
                            <!-- Value and unit -->
                            <div class="flex items-end justify-between mb-3">
                                <div class="text-2xl font-extrabold text-darkText" x-text="m.display"></div>
                                <div class="text-xs font-semibold text-lightText mb-1" x-text="m.unit"></div>
                            </div>

                            <!-- Horizontal progress bar (inset track, outset thumb) -->
                            <div class="w-full h-4 bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-full relative overflow-hidden mb-2">
                                <div class="absolute left-0 top-0 bottom-0 rounded-full bg-brand transition-all duration-1000"
                                    :style="`width: ${Math.max(5, m.pct)}%;`">
                                </div>
                            </div>

                            <!-- Range indicators -->
                            <div class="flex items-center justify-between text-[10px] text-lightText font-medium">
                                <span x-text="m.min + m.unit"></span>
                                <span x-text="m.desc" class="text-darkText"></span>
                                <span x-text="m.max + m.unit"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Plain Type - Clean Design -->
                    <template x-if="m.type==='plain'">
                        <div class="flex flex-col h-full w-full justify-end">
                            <div class="flex items-baseline gap-1 mb-2">
                                <div class="text-3xl font-extrabold text-darkText" x-text="m.display"></div>
                                <div class="text-xs font-semibold text-lightText" x-text="m.unit"></div>
                            </div>
                            <!-- Status indicator -->
                            <div class="text-[10px] text-brand font-bold bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-3 py-1.5 rounded-lg w-max"
                                x-text="m.desc"></div>
                        </div>
                    </template>
                </div>
                
                <!-- Subtle background icon -->
                <div class="absolute right-4 bottom-4 opacity-[0.03] pointer-events-none" style="transform: scale(3);">
                    <div class="w-6 h-6 text-darkText" x-html="metricIcon(m.key)"></div>
                </div>
            </div>
        </template>
    </div>
</section>

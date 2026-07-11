<div class="mt-8 grid md:grid-cols-2 gap-8" x-show="weekViewDays.length">
    <!-- Current Tasks & 24h Forecast -->
    <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8">
        
        <div class="flex flex-col space-y-6">
            <!-- Header Prakiraan -->
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-lightText uppercase tracking-wider" x-text="t('forecast')">Prakiraan</div>
                <div class="flex bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-xl overflow-hidden p-1">
                    <button type="button" class="px-4 py-2 text-xs font-bold rounded-lg transition-all"
                        :class="forecastView === '24h' ? 'bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-brand' : 'text-lightText hover:text-darkText'"
                        @click="forecastView='24h'" x-text="t('next24h')" disabled>24 Jam</button>
                </div>
            </div>

            <!-- Skeleton Load 24h -->
            <template x-if="loadingWeather">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <template x-for="i in 4">
                        <div class="bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] rounded-2xl p-4 text-center animate-pulse">
                            <div class="h-4 bg-gray-300 rounded-full w-1/2 mx-auto mb-3"></div>
                            <div class="h-8 w-8 bg-gray-300 rounded-full mx-auto mb-3"></div>
                            <div class="h-6 bg-gray-300 rounded-full w-2/3 mx-auto mb-2"></div>
                            <div class="h-3 bg-gray-300 rounded-full w-full mx-auto"></div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- 24h Forecast -->
            <div x-show="forecastView==='24h' && !loadingWeather" style="display: none;" class="grid grid-cols-2 sm:grid-cols-4 gap-4" x-cloak>
                <template x-for="f in forecast24h" :key="f.local_datetime">
                    <div class="bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] rounded-2xl p-4 text-center hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all duration-300 cursor-pointer">
                        <div class="text-sm font-bold text-darkText mb-2" x-text="f.hour"></div>
                        <template x-if="f.icon">
                            <img :src="f.icon" class="h-8 w-8 mx-auto mb-3 drop-shadow-md" width="32" height="32" loading="lazy" :alt="f.label" />
                        </template>
                        <div class="text-lg font-extrabold text-brand tabular-nums" x-text="f.temp+'°C'"></div>
                        <div class="text-xs font-semibold text-lightText truncate mt-1" x-text="f.label"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Week & Activities -->
    <div class="flex flex-col bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] rounded-3xl p-6 md:p-8 gap-6">
        
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold tracking-tight text-darkText" x-text="t('activities')">Aktivitas / Peringatan</h3>
            <button class="w-10 h-10 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] flex items-center justify-center text-brand hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all duration-300" @click="refreshTasks()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        <template x-if="!currentTasks.length">
            <div class="text-sm font-semibold text-lightText text-center p-4" x-text="t('noTasks')">Tidak ada aktivitas.</div>
        </template>

        <!-- Skeleton Load Tasks -->
        <template x-if="loadingSchedule">
            <div class="space-y-4">
                <template x-for="i in 2">
                    <div class="flex gap-4 items-stretch animate-pulse">
                        <div class="w-14 h-14 shrink-0 rounded-2xl bg-gray-300"></div>
                        <div class="flex-1 bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-4 space-y-3">
                            <div class="h-3 bg-gray-300 rounded-full w-1/2"></div>
                            <div class="h-2 bg-gray-300 rounded-full w-3/4"></div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <div x-show="!loadingSchedule" style="display: none;" class="space-y-4">
            <template x-for="t in currentTasks" :key="t.id">
                <div class="flex gap-4 items-stretch group cursor-pointer">
                    <div :class="['w-14 shrink-0 rounded-2xl flex flex-col items-center justify-center text-white text-xs font-bold shadow-md', t.color]">
                        <span x-text="t.badgeValue" class="text-base"></span>
                        <span x-text="t.badgeLabel"></span>
                    </div>
                    <div class="flex-1 bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] group-hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all duration-300 rounded-2xl p-4">
                        <div class="text-sm font-bold text-darkText" x-text="t.title"></div>
                        <div class="text-xs text-lightText mt-1 font-medium leading-relaxed" x-html="t.desc"></div>
                        <div class="mt-3 text-[10px] font-bold px-3 py-1 rounded-lg inline-block shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]"
                            :class="t.tagColor" x-text="t.tag"></div>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex items-center justify-between mt-2">
            <h3 class="text-lg font-bold text-darkText" x-text="t('upcomingWeek')">Minggu Ini</h3>
            <div class="flex gap-3">
                <button class="w-8 h-8 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center text-lightText hover:text-brand transition-all"
                    @click="shiftWeek(-1)" :title="t('prevWeek')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button class="w-8 h-8 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center text-lightText hover:text-brand transition-all"
                    @click="shiftWeek(1)" :title="t('nextWeek')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex justify-between text-xs font-bold text-lightText px-2 uppercase tracking-wide mt-2">
            <template x-for="d in weekViewDays" :key="d.date">
                <div class="flex-1 text-center" x-text="d.weekdayShort"></div>
            </template>
        </div>
        
        <div class="flex justify-between gap-2">
            <template x-for="d in weekViewDays" :key="d.date">
                <div @click="selectWeekDay(d)"
                    :class="['flex-1 relative rounded-2xl py-4 flex flex-col items-center gap-2 cursor-pointer transition-all duration-300',
                    d.active ? 'bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] scale-95' : 'bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:scale-105']">
                    
                    <!-- Color dot indicator replacing categoryBg full color -->
                    <div :class="['w-2 h-2 rounded-full mb-1', d.categoryBg.replace('bg-', 'bg-').replace('100', '500').replace('text-', 'bg-')]"></div>

                    <div class="text-xs font-extrabold text-darkText" x-text="d.day"></div>
                    <template x-if="d.icon"><img :src="d.icon" class="h-8 w-8 drop-shadow-sm" width="32" height="32" loading="lazy" /></template>
                    <div class="text-sm font-bold text-brand" x-text="d.temp"></div>
                </div>
            </template>
        </div>

        <div class="flex flex-wrap gap-4 mt-2 text-[10px] font-bold text-lightText">
            <template x-for="l in weekLegend" :key="l.key">
                <div class="flex items-center gap-2">
                    <span :class="['inline-block w-3 h-3 rounded-full shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff]', l.bg.replace('100', '500').replace('text-', 'bg-')]"></span>
                    <span x-text="l.label"></span>
                </div>
            </template>
        </div>
    </div>
</div>

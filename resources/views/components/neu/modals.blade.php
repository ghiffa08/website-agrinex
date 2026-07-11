<!-- Device Detail Modal -->
<div x-cloak x-show="showDeviceModal"
    class="fixed inset-0 z-50 flex items-start md:items-center justify-center p-4 md:p-8"
    @keydown.escape.window="closeDeviceModal()" style="z-index: 9999 !important;">
    
    <!-- Backdrop overlay -->
    <div class="fixed inset-0 bg-neuBg/60 backdrop-blur-sm" @click="closeDeviceModal()"></div>

    <!-- Modal Container -->
    <div x-show="showDeviceModal" x-transition.opacity x-transition.scale.origin.top
        class="bg-neuBg w-full max-w-3xl rounded-3xl shadow-[12px_12px_24px_#a3b1c6,-12px_-12px_24px_#ffffff] overflow-hidden flex flex-col max-h-[92vh] relative border-2 border-white/50"
        style="z-index: 10000 !important;">
        
        <!-- Header -->
        <div class="flex items-start justify-between px-6 py-5 border-b border-white/40">
            <div>
                <h3 class="text-xl font-bold tracking-tight text-darkText" x-text="selectedDevice?.device_name || 'Device'"></h3>
                <p class="text-xs font-semibold text-lightText mt-1" x-text="selectedDevice ? ('ID: '+selectedDevice.device_id) : ''"></p>
            </div>
            <button class="w-8 h-8 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-lightText hover:text-brand transition-all" @click="closeDeviceModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <!-- Body -->
        <div class="px-6 pt-6 pb-8 overflow-y-auto space-y-8 no-scrollbar">
            <!-- Quick stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                <div class="bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-4 flex flex-col items-center justify-center">
                    <div class="text-[10px] font-bold text-lightText uppercase tracking-wider mb-1">Suhu</div>
                    <div class="text-lg font-extrabold text-brand" x-text="fmt(selectedDevice?.temperature_c,'°C')"></div>
                </div>
                <div class="bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-4 flex flex-col items-center justify-center">
                    <div class="text-[10px] font-bold text-lightText uppercase tracking-wider mb-1">Tanah</div>
                    <div class="text-lg font-extrabold text-brand" x-text="fmt(selectedDevice?.soil_moisture_pct,'%')"></div>
                </div>
                <div class="bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-4 flex flex-col items-center justify-center col-span-2 md:col-span-1">
                    <div class="text-[10px] font-bold text-lightText uppercase tracking-wider mb-1">Baterai</div>
                    <div class="text-lg font-extrabold text-brand" x-text="batteryDisplay(selectedDevice)"></div>
                </div>
            </div>

            <!-- Sessions table -->
            <div>
                <h4 class="text-sm font-bold text-darkText mb-3 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-neuBg shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a6.002 6.002 0 0 0 3.6-10.8c-.8-.8-2.6-2.9-3.6-4.2-1 1.3-2.8 3.4-3.6 4.2A6.002 6.002 0 0 0 12 21Z" /></svg>
                    </div>
                    Penggunaan Air per Sesi
                    <template x-if="loadingDeviceDetail"><span class="text-xs font-medium text-lightText ml-2 animate-pulse">(memuat...)</span></template>
                </h4>
                <template x-if="deviceSessionsSummary">
                    <div class="text-[11px] font-bold text-lightText mb-4 flex flex-wrap gap-3">
                        <span class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-3 py-1 rounded-lg" x-text="'Total Rencana: ' + fmt(deviceSessionsSummary.total_planned_l,' L')"></span>
                        <span class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-3 py-1 rounded-lg" x-text="'Total Aktual: ' + fmt(deviceSessionsSummary.total_actual_l,' L')"></span>
                        <span class="bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] px-3 py-1 rounded-lg text-brand" x-text="'Efisiensi: ' + (deviceSessionsSummary.efficiency_pct!=null? deviceSessionsSummary.efficiency_pct+'%':'-')"></span>
                    </div>
                </template>
                <template x-if="!loadingDeviceDetail && !deviceSessions.length">
                    <div class="text-xs font-medium text-lightText italic p-4 text-center">Belum ada data sesi untuk device ini.</div>
                </template>
                <template x-if="deviceSessions.length">
                    <div class="overflow-x-auto bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-2">
                        <table class="min-w-full text-xs text-left">
                            <thead class="text-lightText font-bold border-b-2 border-white/30">
                                <tr>
                                    <th class="px-4 py-3">Sesi</th>
                                    <th class="px-4 py-3">Waktu</th>
                                    <th class="px-4 py-3 text-right">Rencana (L)</th>
                                    <th class="px-4 py-3 text-right">Aktual (L)</th>
                                    <th class="px-4 py-3 text-right">Efisiensi</th>
                                </tr>
                            </thead>
                            <tbody class="text-darkText font-medium">
                                <template x-for="s in deviceSessions" :key="s.id || s.index">
                                    <tr class="border-b border-white/20 last:border-0 hover:bg-white/20 transition-colors">
                                        <td class="px-4 py-3" x-text="s.index || s.session || '-' "></td>
                                        <td class="px-4 py-3" x-text="s.time || s.start_time || '-' "></td>
                                        <td class="px-4 py-3 text-right" x-text="s.planned_l ? s.planned_l.toFixed(1) : (s.planned_volume_l?.toFixed(1) || '-')"></td>
                                        <td class="px-4 py-3 text-right" x-text="s.actual_l ? s.actual_l.toFixed(1) : (s.actual_volume_l?.toFixed(1) || '-')"></td>
                                        <td class="px-4 py-3 text-right text-brand font-bold" x-text="(s.actual_l && s.planned_l) ? ((s.actual_l / (s.planned_l||1))*100).toFixed(0)+'%' : (s.efficiency_pct ? s.efficiency_pct+'%' : '-')"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <!-- Usage history table -->
            <div>
                <h4 class="text-sm font-bold text-darkText mb-3 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-neuBg shadow-[2px_2px_4px_#a3b1c6,-2px_-2px_4px_#ffffff] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    Riwayat Penggunaan Air
                    <template x-if="loadingDeviceDetail"><span class="text-xs font-medium text-lightText ml-2 animate-pulse">(memuat...)</span></template>
                </h4>
                <template x-if="!loadingDeviceDetail && !deviceUsageHistory.length">
                    <div class="text-xs font-medium text-lightText italic p-4 text-center">Belum ada data penggunaan sebelumnya.</div>
                </template>
                <template x-if="deviceUsageHistory.length">
                    <div class="overflow-x-auto bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-2">
                        <table class="min-w-full text-xs text-left">
                            <thead class="text-lightText font-bold border-b-2 border-white/30">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3 text-right">Total (L)</th>
                                    <th class="px-4 py-3 text-right">Sesi</th>
                                </tr>
                            </thead>
                            <tbody class="text-darkText font-medium">
                                <template x-for="h in deviceUsageHistory" :key="h.date || h.id">
                                    <tr class="border-b border-white/20 last:border-0 hover:bg-white/20 transition-colors">
                                        <td class="px-4 py-3" x-text="h.date || h.day || '-' "></td>
                                        <td class="px-4 py-3 text-right text-brand font-bold" x-text="h.total_l ? h.total_l.toFixed(1) : (h.volume_l?.toFixed(1) || '-')"></td>
                                        <td class="px-4 py-3 text-right" x-text="h.sessions || h.session_count || '-' "></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-5 border-t border-white/40 flex justify-end gap-3">
            <button @click="closeDeviceModal()" class="px-6 py-2.5 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] text-xs font-bold text-lightText hover:text-darkText transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>

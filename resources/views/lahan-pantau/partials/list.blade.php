{{-- Loading Skeleton --}}
<div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <template x-for="i in 3" :key="i">
        <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
            <div class="h-6 w-32 bg-neuBg rounded-lg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] mb-4 animate-pulse"></div>
            <div class="space-y-2">
                <div class="h-4 w-full bg-neuBg rounded shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] animate-pulse"></div>
                <div class="h-4 w-3/4 bg-neuBg rounded shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] animate-pulse"></div>
            </div>
        </div>
    </template>
</div>

{{-- Lahan List --}}
<div x-show="!loading">
    {{-- Empty State --}}
    <div x-show="lahans.length === 0" class="text-center py-16">
        <svg class="mx-auto h-16 w-16 text-lightText mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
        </svg>
        <h3 class="text-lg font-semibold text-darkText mb-2">Belum Ada Lahan</h3>
        <p class="text-lightText mb-4">Mulai dengan menambahkan lahan pertama Anda</p>
        <button @click="openCreateModal" 
            class="px-6 py-3 rounded-xl bg-brand text-white font-semibold shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_rgba(0,0,0,0.1)] transition-all">
            + Tambah Lahan
        </button>
    </div>

    {{-- Lahan Grid --}}
    <div x-show="lahans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="lahan in lahans" :key="lahan.id">
            <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all duration-300 cursor-pointer"
                @click="viewDetail(lahan.id)">
                
                {{-- Info --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-darkText mb-2" x-text="lahan.nama_lahan"></h3>
                        <p class="text-sm text-lightText mb-2 flex items-start gap-2">
                            <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-text="lahan.lokasi || 'Lokasi belum diatur'"></span>
                        </p>
                        <p class="text-xs text-lightText line-clamp-2" x-text="lahan.deskripsi || 'Tidak ada deskripsi'"></p>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="flex items-center justify-between pt-4 border-t border-[#a3b1c6]/30">
                    <div class="flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center">
                            <svg class="h-4 w-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-darkText" x-text="lahan.total_devices"></span>
                            <span class="text-lightText">Device</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button @click.stop="openEditModal(lahan)" 
                            class="p-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all">
                            <svg class="h-4 w-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button @click.stop="deleteLahan(lahan.id, lahan.nama_lahan)" 
                            class="p-2 rounded-lg bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all">
                            <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

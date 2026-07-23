{{-- Modal Create/Edit Lahan --}}
<div x-show="showModal" 
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
    
    {{-- Background overlay --}}
    <div class="fixed inset-0 bg-black/50 transition-opacity" 
        @click="closeModal"></div>

    {{-- Modal panel --}}
    <div class="relative z-10 w-full max-w-lg bg-neuBg rounded-[2rem] text-left overflow-hidden shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] transform transition-all"
        @click.stop>
            
            <div class="p-6 md:p-8">
                {{-- Header --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-darkText" x-text="editMode ? 'Edit Lahan' : 'Tambah Lahan Baru'"></h3>
                    <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="h-5 w-5 text-lightText" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form --}}
                <form @submit.prevent="submitForm" class="space-y-4">
                    {{-- Nama Lahan --}}
                    <div>
                        <label class="block text-sm font-semibold text-darkText mb-2">Nama Lahan *</label>
                        <input type="text" 
                            x-model="formData.nama_lahan" 
                            required
                            placeholder="Contoh: Lahan Blok A"
                            class="w-full px-4 py-3 rounded-xl bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] border-none focus:outline-none focus:ring-2 focus:ring-brand text-darkText">
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label class="block text-sm font-semibold text-darkText mb-2">Lokasi *</label>
                        <input type="text" 
                            x-model="formData.lokasi" 
                            required
                            placeholder="Contoh: Desa Sumberjo, Kec. Batu"
                            class="w-full px-4 py-3 rounded-xl bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] border-none focus:outline-none focus:ring-2 focus:ring-brand text-darkText">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-darkText mb-2">Deskripsi</label>
                        <textarea 
                            x-model="formData.deskripsi" 
                            rows="3"
                            placeholder="Deskripsi lahan..."
                            class="w-full px-4 py-3 rounded-xl bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] border-none focus:outline-none focus:ring-2 focus:ring-brand text-darkText resize-none"></textarea>
                    </div>

                    {{-- Daftar Perangkat --}}
                    <div>
                        <label class="block text-sm font-semibold text-darkText mb-2">Daftar Perangkat (Node)</label>
                        <div class="bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-xl p-4 max-h-48 overflow-y-auto space-y-2">
                            <template x-for="device in availableDevices" :key="device.id">
                                <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-gray-100/50 transition-colors">
                                    <input type="checkbox" :value="device.id" x-model="formData.device_ids"
                                        class="w-5 h-5 rounded text-brand focus:ring-brand border-gray-300">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-darkText" x-text="device.device_name || device.name"></p>
                                    </div>
                                </label>
                            </template>
                            <template x-if="availableDevices.length === 0">
                                <p class="text-sm text-lightText text-center py-2">Tidak ada perangkat yang tersedia</p>
                            </template>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-4">
                        <button type="button" 
                            @click="closeModal"
                            class="flex-1 px-6 py-3 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-lightText font-semibold transition-all">
                            Batal
                        </button>
                        <button type="submit" 
                            :disabled="submitting"
                            class="flex-1 px-6 py-3 rounded-xl bg-brand text-white font-semibold shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_rgba(0,0,0,0.1)] transition-all disabled:opacity-50">
                            <span x-show="!submitting" x-text="editMode ? 'Simpan Perubahan' : 'Tambah Lahan'"></span>
                            <span x-show="submitting">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>

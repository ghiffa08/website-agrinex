{{-- Bottom Navigation — Mobile Only (md:hidden) --}}
<div class="fixed bottom-0 left-0 right-0 z-50 md:hidden pb-[env(safe-area-inset-bottom)]">
    <div class="mx-3 mb-3">
        <nav class="flex items-center justify-around
            bg-neuBg rounded-2xl
            shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff]
            px-1 py-2">

            {{-- Dashboard (Active) --}}
            <button class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]
                text-brand transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
                        M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
                        M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
                        M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Beranda</span>
            </button>

            {{-- Perangkat --}}
            <button class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                text-lightText
                hover:text-brand active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]
                transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Perangkat</span>
            </button>

            {{-- FAB Center --}}
            <button class="w-14 h-14 -mt-6 rounded-2xl bg-neuBg
                flex items-center justify-center
                shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff]
                text-brand
                active:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                transition-all duration-200 active:scale-90
                flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </button>

            {{-- Laporan --}}
            <button class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                text-lightText
                hover:text-brand active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]
                transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Laporan</span>
            </button>

            {{-- Profil --}}
            <button class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                text-lightText
                hover:text-brand active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]
                transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Profil</span>
            </button>

        </nav>
    </div>
</div>

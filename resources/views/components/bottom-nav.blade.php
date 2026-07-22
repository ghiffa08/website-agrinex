{{-- Bottom Navigation — Mobile Only (md:hidden) --}}
<div class="fixed bottom-0 left-0 right-0 z-50 md:hidden pb-[env(safe-area-inset-bottom)]"
     x-data="{ currentHash: window.location.hash || '' }"
     @hashchange.window="currentHash = window.location.hash || ''">
    <div class="mx-3 mb-3">
        <nav class="flex items-center justify-around
            bg-neuBg rounded-2xl
            shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff]
            px-1 py-2">

            {{-- Dashboard --}}
            <a href="{{ route('agrinex.dashboard') }}" 
                :class="currentHash === '' && '{{ request()->routeIs('agrinex.dashboard') ? 'yes' : 'no' }}' === 'yes' ? 'bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff] text-brand' : 'text-lightText hover:text-brand active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]'"
                class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
                        M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
                        M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
                        M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Beranda</span>
            </a>

            {{-- Lahan Pantau --}}
            <a href="{{ route('lahan-pantau.index') }}" 
                :class="'{{ request()->routeIs('lahan-pantau.*') ? 'active' : '' }}' === 'active' ? 'bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff] text-brand' : 'text-lightText hover:text-brand active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]'"
                class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Lahan</span>
            </a>

            {{-- Flasher --}}
            <a href="{{ route('flasher.index') }}" 
                :class="'{{ request()->routeIs('flasher.*') ? 'active' : '' }}' === 'active' ? 'bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff] text-brand' : 'text-lightText hover:text-brand active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]'"
                class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Flasher</span>
            </a>

            {{-- Laporan --}}
            <a href="{{ route('reports.index') }}"
                :class="'{{ request()->routeIs('reports.index') ? 'active' : '' }}' === 'active' ? 'bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff] text-brand' : 'text-lightText hover:text-brand active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]'"
                class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Laporan</span>
            </a>

            {{-- Profil --}}
            <a href="/#profile"
                :class="currentHash === '#profile' ? 'bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff] text-brand' : 'text-lightText hover:text-brand active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]'"
                class="flex flex-col items-center justify-center
                w-14 h-12 rounded-xl
                transition-all duration-200 active:scale-90">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[9px] font-bold mt-0.5 leading-none">Profil</span>
            </a>

        </nav>
    </div>
</div>

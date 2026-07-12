{{-- Sidebar panel --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    class="fixed md:sticky inset-y-0 left-0 top-0
        z-50 md:z-auto
        w-[72px] md:w-[88px]
        h-screen
        bg-neuBg
        shadow-[8px_0_16px_#a3b1c6] md:shadow-[4px_0_10px_#a3b1c6]
        hidden md:flex flex-col flex-shrink-0
        overflow-hidden
        transition-transform duration-300 ease-out
        py-4 md:py-6"
    x-data="{ currentHash: window.location.hash || '' }"
    @hashchange.window="currentHash = window.location.hash || ''"
>

    {{-- Logo / Brand --}}
    <div class="flex items-center justify-center px-2 mb-8">
        <a href="{{ route('agrinex.dashboard') }}" 
            class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-brand
                shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff]
                hover:shadow-[inset_4px_4px_8px_rgba(0,0,0,0.1),inset_-4px_-4px_8px_rgba(255,255,255,0.7)]
                flex items-center justify-center text-white font-black text-xl
                transition-all duration-300 active:scale-95"
            title="AgriNex">
            A
        </a>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 flex flex-col items-center gap-4 px-2 overflow-y-auto">
        
        {{-- Dashboard --}}
        <a href="{{ route('agrinex.dashboard') }}"
            :class="currentHash === '' ? 'bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'text-lightText hover:text-brand'"
            class="w-12 h-12 md:w-14 md:h-14 rounded-2xl
                shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                flex items-center justify-center
                transition-all duration-300 active:scale-95 group"
            title="Dashboard">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
                    M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
                    M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
                    M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </a>

        {{-- Devices --}}
        <a href="{{ route('agrinex.devices') }}"
            :class="'{{ request()->routeIs('agrinex.devices') || request()->routeIs('agrinex.node-detail') ? 'active' : '' }}' === 'active' ? 'bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'text-lightText hover:text-brand'"
            class="w-12 h-12 md:w-14 md:h-14 rounded-2xl
                shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                flex items-center justify-center
                transition-all duration-300 active:scale-95"
            title="Perangkat">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </a>

        {{-- Weather --}}
        <a href="#"
            class="w-12 h-12 md:w-14 md:h-14 rounded-2xl
                text-lightText hover:text-brand
                shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                flex items-center justify-center
                transition-all duration-300 active:scale-95"
            title="Cuaca">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
            </svg>
        </a>

        {{-- Reports --}}
        <a href="{{ route('reports.index') }}"
            :class="'{{ request()->routeIs('reports.index') ? 'active' : '' }}' === 'active' ? 'bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-brand' : 'text-lightText hover:text-brand'"
            class="w-12 h-12 md:w-14 md:h-14 rounded-2xl
                shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                flex items-center justify-center
                transition-all duration-300 active:scale-95"
            title="Laporan">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </a>

    </nav>

    {{-- Bottom section: Home + Profile --}}
    <div class="mt-auto space-y-4 px-2">
        
        {{-- Home Button --}}
        <div class="flex items-center justify-center">
            <a href="{{ route('agrinex.dashboard') }}"
                :class="currentHash === '' ? 'text-brand shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]' : 'text-lightText hover:text-brand'"
                class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-neuBg
                    shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                    hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                    transition-all duration-300
                    flex items-center justify-center active:scale-95"
                title="Beranda">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a2 2 0 002 2h10a2 2 0 002-2V10M9 21h6"/>
                </svg>
            </a>
        </div>

        {{-- Profile Button --}}
        <div class="flex items-center justify-center">
            <a href="/#profile"
                :class="currentHash === '#profile' ? 'text-brand shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]' : 'text-lightText hover:text-brand'"
                class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-neuBg
                    shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                    hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                    transition-all duration-300
                    flex items-center justify-center active:scale-95 overflow-hidden"
                title="Profil Saya">
                @include('components.profile-avatar', [
                    'user' => Auth::user(),
                    'size' => 'sm',
                    'showBorder' => false,
                    'showShadow' => false,
                ])
            </a>
        </div>
        
    </div>

</aside>

{{-- Mobile sidebar backdrop --}}
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     x-transition.opacity
     class="fixed inset-0 z-40 bg-black/30 md:hidden"></div>

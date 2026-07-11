<header class="flex justify-between items-center py-3 md:py-4">
    <div class="flex items-center gap-3">
        {{-- Mobile sidebar toggle (Hidden since we have bottom bar) --}}
        <button @click="sidebarOpen = !sidebarOpen"
            class="hidden w-10 h-10 rounded-xl bg-neuBg
                shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]
                items-center justify-center text-lightText
                transition-all duration-200 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Brand --}}
        <div class="px-2 md:px-4 py-2 md:py-3">
            <h1 class="text-xl md:text-3xl font-extrabold tracking-tight text-darkText">AgriNex</h1>
        </div>
    </div>

    <div class="flex gap-2 md:gap-4">
        {{-- Notification Bell --}}
        <button class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-neuBg
            shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
            active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]
            flex items-center justify-center
            transition-all duration-200 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-lightText" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>

        {{-- Profile Dropdown --}}
        <div class="relative" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-neuBg
                shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]
                flex items-center justify-center
                transition-all duration-200 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-lightText" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </button>
            
            <div x-cloak x-show="profileOpen"
                class="absolute right-0 mt-3 w-48 rounded-xl bg-neuBg p-2 z-50
                shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">
                
                <div class="px-4 py-2 border-b border-[#a3b1c6]/30 mb-2">
                    <p class="text-sm font-bold text-darkText truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-lightText truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded-lg text-sm font-semibold text-red-500 hover:bg-[#a3b1c6]/10 flex items-center gap-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

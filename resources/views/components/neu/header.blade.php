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
        {{-- Settings --}}
        <button class="hidden md:flex w-12 h-12 rounded-full bg-neuBg
            shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
            active:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]
            items-center justify-center
            transition-all duration-200 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-lightText" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>
    </div>
</header>

<header class="bg-white shadow-sm border-b border-slate-200 z-20 h-[70px] flex-shrink-0">
    <div class="flex items-center justify-between h-full px-4 sm:px-6 lg:px-8">
        
        {{-- Left side: Sidebar Toggle & Search --}}
        <div class="flex items-center gap-4">
            {{-- Hamburger Menu for Mobile & Desktop collapse --}}
            <button @click="toggleSidebar()" class="p-2 -ml-2 text-slate-500 hover:text-emerald-600 focus:outline-none transition-colors rounded-lg hover:bg-slate-100:bg-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 6h16M4 12h16M4 18h7" />
                </svg>
            </button>

            {{-- Search Bar (Optional, like Stisla) --}}
            <div class="hidden md:flex relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" class="block w-full p-2 pl-10 text-sm text-slate-900 bg-slate-50 border border-slate-200 rounded-full focus:ring-emerald-500 focus:border-emerald-500:ring-emerald-500:border-emerald-500 transition-all w-48 focus:w-64" placeholder="Search...">
            </div>
        </div>

        {{-- Right side: Notifications & Profile --}}
        <div class="flex items-center gap-3">
            
            {{-- Notifications --}}
            <button class="relative p-2 text-slate-500 hover:text-emerald-600 transition-colors rounded-full hover:bg-slate-100:bg-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            </button>

            {{-- Profile Dropdown --}}
            <div class="relative ml-2" x-data="{ profileOpen: false }" @click.away="profileOpen = false">
                <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 focus:outline-none rounded-full hover:bg-slate-50:bg-slate-700 p-1 pr-2 transition">
                    @if(auth()->check() && auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200 shadow-sm">
                    @else
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
                            {{ auth()->check() ? substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1) : 'U' }}
                        </div>
                    @endif
                    
                    <div class="hidden md:flex flex-col text-left">
                        <span class="text-sm font-semibold text-slate-700">Hi, {{ auth()->check() ? (auth()->user()->full_name ?? auth()->user()->username) : 'Guest' }}</span>
                    </div>
                </button>

                <!-- Dropdown -->
                <div x-show="profileOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 focus:outline-none z-50"
                     style="display: none;">
                    
                    <div class="px-4 py-3">
                        <p class="text-sm text-slate-500">Signed in as</p>
                        <p class="text-sm font-medium text-slate-900 truncate">
                            {{ auth()->check() ? auth()->user()->email : '-' }}
                        </p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50:bg-slate-700/50 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Profile
                        </a>
                        <a href="{{ route('agrinex.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50:bg-slate-700/50 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                            Main Dashboard
                        </a>
                    </div>

                    <div class="py-1">
                        @if(auth()->check())
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50:bg-red-900/20 flex items-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

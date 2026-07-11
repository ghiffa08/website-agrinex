{{-- ═══════════════════════════════════════════════════
 Sidebar — Desktop sticky icon rail
 Mobile: slide-over overlay
═══════════════════════════════════════════════════ --}}

{{-- Mobile backdrop --}}
<div
 x-show="sidebarOpen"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 x-transition:leave="transition ease-in duration-150"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 @click="sidebarOpen = false"
 class="fixed inset-0 z-40 [2px] md:hidden"
 x-cloak
></div>

{{-- Sidebar panel --}}
<aside
 :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
 class="{{-- Mobile: fixed overlay --}}
 fixed md:sticky
 inset-y-0 left-0
 top-0
 z-50 md:z-auto

 {{-- Size --}}
 w-[72px]
 h-screen md:h-screen

 {{-- Glass surface --}}
 bg-[#E0E5EC]
 
 border-r border-white/50

 {{-- Shadow only on mobile overlay --}}
 shadow-2xl shadow-black/10 md:shadow-none

 {{-- Layout --}}
 flex flex-col
 flex-shrink-0
 overflow-hidden

 transition-transform duration-300 ease-out
 "
>
 {{-- ── Brand mark ── --}}
 <div class="flex items-center justify-center h-14 flex-shrink-0">
 <a href="/"
 class="flex items-center justify-center w-10 h-10 rounded-2xl
 bg-emerald-500 hover:bg-emerald-600
 shadow-md shadow-emerald-200/50
 transition-all duration-200 hover:scale-105">
 <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
 stroke="currentColor" stroke-width="2.5">
 <path stroke-linecap="round" stroke-linejoin="round"
 d="M12 3C8.5 7 6 10 6 13a6 6 0 0012 0c0-3-2.5-6-6-10z"/>
 </svg>
 </a>
 </div>

 {{-- Hairline divider --}}
 <div class="mx-3.5 h-px flex-shrink-0"></div>

 {{-- ── Nav items ── --}}
 <nav class="flex-1 flex flex-col items-center gap-1 py-3 px-2 overflow-y-auto overflow-x-visible no-scrollbar">

 {{-- Dashboard (active) --}}
 <a href="/" class="sidebar-item active" title="Dashboard">
 <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24"
 stroke="currentColor" stroke-width="2">
 <rect x="3" y="3" width="7" height="7" rx="1.5"/>
 <rect x="14" y="3" width="7" height="7" rx="1.5"/>
 <rect x="3" y="14" width="7" height="7" rx="1.5"/>
 <rect x="14" y="14" width="7" height="7" rx="1.5"/>
 </svg>
 <span class="sidebar-tooltip">Dashboard</span>
 </a>

 {{-- Lahan Pantau --}}
 <a href="#" class="sidebar-item" title="Lahan Pantau">
 <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24"
 stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round"
 d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945
 M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064
 M15 20.488V18a2 2 0 012-2h3.064
 M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 <span class="sidebar-tooltip">Lahan Pantau</span>
 </a>

 {{-- Perangkat --}}
 <a href="#" class="sidebar-item" title="Perangkat">
 <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24"
 stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round"
 d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
 </svg>
 <span class="sidebar-tooltip">Perangkat</span>
 </a>

 {{-- Riwayat --}}
 <a href="#" class="sidebar-item" title="Riwayat">
 <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24"
 stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round"
 d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
 </svg>
 <span class="sidebar-tooltip">Riwayat</span>
 </a>

 {{-- Pengaturan --}}
 <a href="#" class="sidebar-item" title="Pengaturan">
 <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24"
 stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round"
 d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
 <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
 </svg>
 <span class="sidebar-tooltip">Pengaturan</span>
 </a>

 </nav>

 {{-- Hairline divider --}}
 <div class="mx-3.5 h-px flex-shrink-0"></div>

 {{-- Account button --}}
 <div class="flex items-center justify-center py-4 flex-shrink-0">
 <button
 class="w-9 h-9 rounded-2xl bg-emerald-500 hover:bg-emerald-600
 flex items-center justify-center
 shadow-md shadow-emerald-200/50
 hover:scale-105 transition-all duration-200"
 title="Akun Saya"
 >
 <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
 stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round"
 d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
 <circle cx="12" cy="7" r="4"/>
 </svg>
 </button>
 </div>

</aside>

{{-- ═══════════════════════════════════════════════════
 Bottom Navigation — Mobile Only (md:hidden)
═══════════════════════════════════════════════════ --}}
<div class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 md:hidden w-[calc(100%-2rem)] max-w-sm">
 <nav class="flex items-center justify-around
 bg-[#E0E5EC]
 
 border border-white/60
 rounded-2xl
 shadow-xl shadow-black/10
 px-2 py-2">

 {{-- Dashboard (Active) --}}
 <button class="bottom-nav-item active">
 <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round"
 d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
 M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
 M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
 M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
 </svg>
 <span>Beranda</span>
 </button>

 {{-- Perangkat --}}
 <button class="bottom-nav-item">
 <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
 </svg>
 <span>Perangkat</span>
 </button>

 {{-- FAB Center --}}
 <button class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center shadow-md text-white hover:bg-emerald-600 hover:scale-105 active:scale-95 transition-all flex-shrink-0 -mt-5">
 <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
 <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
 </svg>
 </button>

 {{-- Laporan --}}
 <button class="bottom-nav-item">
 <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round"
 d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
 </svg>
 <span>Laporan</span>
 </button>

 {{-- Profile --}}
 <button class="bottom-nav-item">
 <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
 </svg>
 <span>Profil</span>
 </button>

 </nav>
</div>

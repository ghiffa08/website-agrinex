<!-- PWA Install Prompt -->
<div x-data="pwaInstall()" x-show="showInstallPrompt" x-cloak
 class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 z-50 animate-pop">
 <div class="flex items-start gap-3">
 <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
 <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
 </svg>
 </div>
 <div class="flex-1">
 <h3 class="font-bold text-primary text-sm mb-1">Install Aplikasi</h3>
 <p class="text-xs text-secondary mb-3">Install Smart Irrigation sebagai aplikasi di perangkat Anda untuk akses lebih cepat!</p>
 <div class="flex gap-2">
 <button @click="installPWA()" 
 class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 px-4 rounded-lg transition">
 Install
 </button>
 <button @click="dismissInstall()" 
 class="px-4 py-2 text-xs text-secondary hover:text-primary transition">
 Nanti
 </button>
 </div>
 </div>
 <button @click="dismissInstall()" class="text-secondary opacity-80 hover:text-secondary">
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
 </svg>
 </button>
 </div>
</div>

<!-- PWA Update Available Banner -->
<div x-data="{ showUpdateBanner: false }" x-show="showUpdateBanner" x-cloak
 class="fixed top-20 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-blue-600 text-white rounded-xl shadow-2xl p-4 z-50">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-3">
 <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
 </svg>
 <div>
 <p class="font-semibold text-sm">Update Tersedia!</p>
 <p class="text-xs opacity-90">Versi baru aplikasi sudah siap</p>
 </div>
 </div>
 <button @click="location.reload()" 
 class="bg-white text-blue-600 px-4 py-2 rounded-lg text-xs font-semibold hover:bg-blue-50 transition">
 Update
 </button>
 </div>
</div>

<!-- Offline Indicator -->
<div x-data="{ isOffline: false }" 
 @offline.window="isOffline = true" 
 @online.window="isOffline = false"
 x-show="isOffline" 
 x-cloak
 class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-yellow-500 text-white px-6 py-3 rounded-full shadow-lg z-50 flex items-center gap-2">
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"/>
 </svg>
 <span class="text-sm font-semibold">Mode Offline - Menampilkan data cache</span>
</div>

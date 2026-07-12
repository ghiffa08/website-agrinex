{{-- Global Splash Screen Component
     Displays once per session with localStorage caching
     Best practice: prevent re-renders on navigation --}}

<div x-data="splashScreen()" 
     x-init="init()"
     x-show="showSplash" 
     x-transition:leave="transition ease-in duration-500"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     @click.self="hideSplash()"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-neuBg pointer-events-auto">
     
     {{-- Splash Content --}}
     <div class="flex flex-col items-center gap-6">
         <div class="relative">
             <div class="absolute inset-0 bg-brand/20 blur-2xl rounded-full animate-pulse"></div>
             <h1 class="relative text-6xl font-extrabold tracking-tight text-brand drop-shadow-lg">
                 AgriNex
             </h1>
         </div>
         <p class="text-lightText font-medium text-sm tracking-wide">
             Smart Irrigation System
         </p>
         <div class="w-1 h-1 bg-brand rounded-full animate-pulse"></div>
     </div>
</div>

<script>
function splashScreen() {
    return {
        showSplash: true,
        splashKey: 'agrinex_splash_shown',
        
        init() {
            // Check if splash was shown in this session
            const splashShown = sessionStorage.getItem(this.splashKey);
            
            if (splashShown) {
                this.showSplash = false;
            } else {
                // Mark as shown and hide after 1 second
                setTimeout(() => this.hideSplash(), 1000);
            }
        },
        
        hideSplash() {
            this.showSplash = false;
            sessionStorage.setItem(this.splashKey, 'true');
        }
    }
}
</script>

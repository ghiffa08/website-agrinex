<div x-data="nodeConfig()" class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] mb-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <h3 class="text-xl font-bold tracking-tight text-darkText">Global Node Configuration</h3>
            <div class="px-3 py-1 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-xs font-bold text-darkText">
                <span x-text="config.mode.toUpperCase()"></span>
            </div>
        </div>
        <button @click="fetchConfig()" class="p-2 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-brand hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-shadow" :class="loading ? 'animate-spin' : ''">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        </button>
    </div>

    <div x-show="errorMessage" x-cloak class="mb-4 p-3 rounded-xl bg-red-100 border border-red-300 text-red-700 text-xs font-semibold flex justify-between items-center">
        <span x-text="errorMessage"></span>
        <button @click="errorMessage = null" class="text-red-500 hover:text-red-700 font-bold ml-2">&times;</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Mode Toggle -->
        <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <span class="font-bold text-darkText text-lg">Operating Mode</span>
            </div>
            <div class="flex bg-neuBg rounded-full shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] p-1 relative z-10">
                <button @click="updateConfig('auto', config.valve)" 
                        class="flex-1 py-2 rounded-full font-bold text-sm transition-all"
                        :class="config.mode === 'auto' ? 'bg-brand text-white shadow-[4px_4px_8px_rgba(0,0,0,0.2)]' : 'text-lightText hover:text-darkText'">
                    AUTO
                </button>
                <button @click="updateConfig('manual', config.valve)" 
                        class="flex-1 py-2 rounded-full font-bold text-sm transition-all"
                        :class="config.mode === 'manual' ? 'bg-brand text-white shadow-[4px_4px_8px_rgba(0,0,0,0.2)]' : 'text-lightText hover:text-darkText'">
                    MANUAL
                </button>
            </div>
        </div>

        <!-- Valve Toggle -->
        <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] flex flex-col gap-4 relative"
             :class="config.mode === 'auto' ? 'opacity-50' : ''">
             <div x-show="config.mode === 'auto'" class="absolute inset-0 z-20 cursor-not-allowed"></div>
            <div class="flex justify-between items-center">
                <span class="font-bold text-darkText text-lg">Valve Override</span>
                <span class="text-xs text-lightText" x-show="config.mode === 'auto'">(Disabled in Auto)</span>
            </div>
            <div class="flex bg-neuBg rounded-full shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff] p-1 relative z-10">
                <button @click="updateConfig('manual', 'OFF')" 
                        class="flex-1 py-2 rounded-full font-bold text-sm transition-all"
                        :class="config.valve === 'OFF' ? 'bg-[#EF4444] text-white shadow-[4px_4px_8px_rgba(0,0,0,0.2)]' : 'text-lightText hover:text-darkText'">
                    OFF
                </button>
                <button @click="updateConfig('manual', 'ON')" 
                        class="flex-1 py-2 rounded-full font-bold text-sm transition-all"
                        :class="config.valve === 'ON' ? 'bg-[#00D26A] text-white shadow-[4px_4px_8px_rgba(0,0,0,0.2)]' : 'text-lightText hover:text-darkText'">
                    ON
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('nodeConfig', () => ({
        config: {
            mode: 'auto',
            valve: 'OFF'
        },
        loading: false,
        isUpdating: false,
        errorMessage: null,
        init() {
            this.fetchConfig();
            
            // Poll for external updates every 30 seconds
            setInterval(() => {
                this.fetchConfig(true);
            }, 30000);
        },
        async fetchConfig(silent = false) {
            if (!silent) this.loading = true;
            try {
                const response = await fetch('/api/nodes/config');
                if(response.ok) {
                    const data = await response.json();
                    this.config = data;
                }
            } catch (error) {
                console.error('Failed to fetch config:', error);
            } finally {
                if (!silent) this.loading = false;
            }
        },
        async updateConfig(newMode, newValve) {
            if (this.isUpdating) return; // Prevent concurrent requests
            
            if (this.config.mode === 'auto' && newMode !== 'manual' && newValve !== this.config.valve) {
                // If it's auto, we don't allow valve changes
                return;
            }

            // Don't send request if values haven't changed
            if (this.config.mode === newMode && this.config.valve === newValve) {
                return;
            }
            
            // Optimistic update
            const oldConfig = { ...this.config };
            this.config = { mode: newMode, valve: newValve };
            this.loading = true;
            this.isUpdating = true;
            this.errorMessage = null;
            
            try {
                const response = await fetch('/api/nodes/config', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ mode: newMode, valve: newValve })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.data) {
                        this.config = data.data;
                    }
                } else {
                    if (response.status === 429) {
                        this.errorMessage = 'Terlalu banyak permintaan. Harap tunggu beberapa detik.';
                        console.warn('Rate limit exceeded (429)');
                    }
                    // Revert if failed
                    this.config = oldConfig;
                }
            } catch (error) {
                console.error('Failed to update config:', error);
                this.config = oldConfig; // Revert if failed
            } finally {
                this.loading = false;
                this.isUpdating = false;
            }
        }
    }));
});
</script>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: #E0E5EC;">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
    <title>ESP32 Web Flasher - AgriNex</title>
</head>

<body x-data="{ sidebarOpen: false, ...flasherApp() }"
    class="h-full min-h-screen w-full bg-neuBg text-darkText font-sans antialiased selection:bg-brand selection:text-white relative overflow-x-hidden">

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText">
        {{-- App Shell: [Sidebar | Main] --}}
        <div class="relative z-10 flex h-full min-h-screen" x-cloak>
            {{-- Sidebar --}}
            <div class="hidden md:flex md:flex-shrink-0">
                @include('components.sidebar')
            </div>
            <div class="md:hidden">
                @include('components.sidebar')
            </div>

            {{-- Main column --}}
            <div class="flex-1 min-w-0 flex flex-col min-h-screen overflow-x-hidden">
                {{-- Sticky header --}}
                <div class="sticky top-0 z-30 w-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] transition-colors duration-300">
                    <div class="px-4 md:px-6 xl:px-8">
                        @include('components.header')
                    </div>
                </div>

                {{-- Page content --}}
                <main class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[1400px] mx-auto space-y-6 md:space-y-8">
                    
                    {{-- Main Embossed Card Container --}}
                    <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                        
                        {{-- Page Header --}}
                        <div class="border-b border-[#a3b1c6]/30 pb-6 mb-8">
                            <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-darkText">ESP32 Web Flasher</h2>
                            <p class="text-lightText text-xs md:text-sm mt-1">Flash firmware ke ESP32 XIAO S3 langsung dari browser</p>
                        </div>

                        {{-- Browser Compatibility Alert --}}
                        <template x-if="!isWebSerialSupported">
                            <div class="mb-6 p-6 rounded-3xl bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                                <div class="flex items-start gap-4">
                                    <div class="p-3 rounded-2xl bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]">
                                        <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-darkText mb-2">Browser Tidak Mendukung Web Serial API</h3>
                                        <p class="text-sm text-lightText mb-3">
                                            Web Serial API diperlukan untuk flash ESP32. Gunakan browser yang mendukung:
                                        </p>
                                        <div class="flex flex-wrap gap-3 text-sm">
                                            <span class="px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
                                                Chrome 89+
                                            </span>
                                            <span class="px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
                                                Edge 89+
                                            </span>
                                            <span class="px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
                                                Opera 75+
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            
                            {{-- Left Panel: Firmware Selection --}}
                            <div class="lg:col-span-2 space-y-6">
                                
                                {{-- Firmware Cards --}}
                                <div>
                                    <h3 class="text-lg font-bold text-darkText mb-4">Pilih Firmware</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        {{-- Sender Firmware --}}
                                        <div class="p-5 rounded-2xl bg-neuBg cursor-pointer transition-all duration-300"
                                            :class="selectedFirmware === 'sender' ? 'shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_6px_#a3b1c6,inset_-4px_-4px_6px_#ffffff]'"
                                            @click="selectFirmware('sender')">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="p-2 rounded-xl bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]">
                                                    <svg class="w-5 h-5 text-brand" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-darkText">Sender</h4>
                                                    <span class="text-xs text-lightText">NODE01</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-lightText">Sensor node yang mengirim data melalui LoRa</p>
                                            <div class="mt-3 text-xs font-semibold text-brand" x-show="selectedFirmware === 'sender'" x-cloak>
                                                ✓ Terpilih
                                            </div>
                                        </div>

                                        {{-- Receiver Firmware --}}
                                        <div class="p-5 rounded-2xl bg-neuBg cursor-pointer transition-all duration-300"
                                            :class="selectedFirmware === 'receiver' ? 'shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_6px_#a3b1c6,inset_-4px_-4px_6px_#ffffff]'"
                                            @click="selectFirmware('receiver')">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="p-2 rounded-xl bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]">
                                                    <svg class="w-5 h-5 text-brand" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-darkText">Receiver</h4>
                                                    <span class="text-xs text-lightText">NODE02</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-lightText">Gateway yang menerima data dan kontrol valve</p>
                                            <div class="mt-3 text-xs font-semibold text-brand" x-show="selectedFirmware === 'receiver'" x-cloak>
                                                ✓ Terpilih
                                            </div>
                                        </div>

                                        {{-- Tester Firmware --}}
                                        <div class="p-5 rounded-2xl bg-neuBg cursor-pointer transition-all duration-300"
                                            :class="selectedFirmware === 'tester' ? 'shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_6px_#a3b1c6,inset_-4px_-4px_6px_#ffffff]'"
                                            @click="selectFirmware('tester')">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="p-2 rounded-xl bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]">
                                                    <svg class="w-5 h-5 text-brand" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-darkText">Tester</h4>
                                                    <span class="text-xs text-lightText">QC Tool</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-lightText">Firmware testing untuk quality control</p>
                                            <div class="mt-3 text-xs font-semibold text-brand" x-show="selectedFirmware === 'tester'" x-cloak>
                                                ✓ Terpilih
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- WiFi Config (Only for Receiver) --}}
                                <div x-show="selectedFirmware === 'receiver'" x-cloak>
                                    <h3 class="text-lg font-bold text-darkText mb-4">Konfigurasi WiFi (Opsional)</h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-lightText mb-2">SSID</label>
                                            <input type="text" x-model="wifiSSID" placeholder="Nama WiFi"
                                                class="w-full px-5 py-4 rounded-2xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-lightText mb-2">Password</label>
                                            <input type="password" x-model="wifiPassword" placeholder="Password WiFi"
                                                class="w-full px-5 py-4 rounded-2xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText transition-all">
                                        </div>
                                    </div>
                                </div>

                                {{-- Connect & Flash Buttons --}}
                                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                                    {{-- Connect/Disconnect Button --}}
                                    <button @click="port ? disconnectPort() : connectPort()" 
                                        :disabled="isFlashing || isConnecting || !isWebSerialSupported"
                                        class="px-6 py-4 rounded-2xl font-bold transition-all duration-300"
                                        :class="isFlashing || isConnecting || !isWebSerialSupported
                                            ? 'bg-gray-400 text-white cursor-not-allowed shadow-[4px_4px_8px_#8a96a8,-4px_-4px_8px_#d4dce6]'
                                            : port
                                                ? 'bg-red-500 text-white hover:shadow-[inset_4px_4px_8px_rgba(0,0,0,0.2)] shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff]'
                                                : 'bg-blue-500 text-white hover:shadow-[inset_4px_4px_8px_rgba(0,0,0,0.2)] shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff]'">
                                        <span x-show="!port && !isConnecting">🔌 Connect ESP32</span>
                                        <span x-show="isConnecting" x-cloak>🔄 Connecting...</span>
                                        <span x-show="port && !isConnecting" x-cloak>❌ Disconnect</span>
                                    </button>

                                    {{-- Flash Button --}}
                                    <button @click="startFlash" :disabled="!selectedFirmware || isFlashing || !isWebSerialSupported"
                                        class="px-8 py-4 rounded-2xl font-bold text-white transition-all duration-300"
                                        :class="!selectedFirmware || isFlashing || !isWebSerialSupported 
                                            ? 'bg-gray-400 cursor-not-allowed shadow-[4px_4px_8px_#8a96a8,-4px_-4px_8px_#d4dce6]' 
                                            : 'bg-brand hover:shadow-[inset_4px_4px_8px_rgba(0,0,0,0.2),inset_-4px_-4px_8px_rgba(255,255,255,0.1)] shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] active:shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]'">
                                        <span x-text="isFlashing ? '⚡ Flashing...' : '⚡ Flash Firmware'"></span>
                                    </button>
                                </div>
                            </div>

                            {{-- Right Panel: Status & Console --}}
                            <div class="space-y-6">
                                
                                {{-- Status Card --}}
                                <div>
                                    <h3 class="text-lg font-bold text-darkText mb-4">Status</h3>
                                    
                                    <div class="p-5 rounded-2xl bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-lightText">Firmware:</span>
                                                <span class="text-sm font-bold text-darkText" x-text="selectedFirmware || '-'"></span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-lightText">Connection:</span>
                                                <span class="text-xs font-bold px-2 py-1 rounded-lg"
                                                    :class="port ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                                    x-text="port ? '✓ Connected' : '○ Disconnected'"></span>
                                            </div>
                                            <template x-if="chipInfo">
                                                <div class="pt-2 border-t border-[#a3b1c6]/30">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-sm text-lightText">Chip:</span>
                                                        <span class="text-sm font-bold text-darkText" x-text="chipInfo.type"></span>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm text-lightText">MAC:</span>
                                                        <span class="text-xs font-mono text-darkText" x-text="chipInfo.macAddress"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div class="flex items-center justify-between pt-2 border-t border-[#a3b1c6]/30">
                                                <span class="text-sm text-lightText">Progress:</span>
                                                <span class="text-sm font-bold text-brand" x-text="progress + '%'"></span>
                                            </div>
                                        </div>

                                        {{-- Progress Bar --}}
                                        <div class="mt-4 h-3 rounded-full bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] overflow-hidden">
                                            <div class="h-full bg-brand transition-all duration-300 rounded-full"
                                                :style="'width: ' + progress + '%'"></div>
                                        </div>

                                        <div class="mt-4 text-xs text-lightText" x-text="statusMessage"></div>
                                    </div>
                                </div>

                                {{-- Serial Monitor --}}
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-bold text-darkText">Serial Monitor</h3>
                                        <button @click="clearConsole" 
                                            class="px-3 py-1 text-xs rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff] text-lightText font-semibold transition-all">
                                            Clear
                                        </button>
                                    </div>
                                    
                                    <div class="h-64 p-4 rounded-2xl bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] overflow-y-auto font-mono text-xs text-darkText"
                                        x-ref="console">
                                        <template x-for="(log, idx) in consoleLog" :key="idx">
                                            <div x-text="log" class="mb-1"></div>
                                        </template>
                                        <div x-show="consoleLog.length === 0" class="text-lightText text-center py-8">
                                            Console output akan muncul di sini...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                {{-- Bottom Navigation (Mobile) --}}
                @include('components.bottom-nav')
            </div>
        </div>
    </div>

    <script type="module">
        import { ESPLoader, Transport } from 'https://unpkg.com/esptool-js@0.4.4/bundle.js';
        window.esptooljs = { ESPLoader, Transport };
    </script>
    <script>
    function flasherApp() {
        return {
            isWebSerialSupported: 'serial' in navigator,
            selectedFirmware: null,
            wifiSSID: '',
            wifiPassword: '',
            isFlashing: false,
            isConnecting: false,
            progress: 0,
            statusMessage: 'Pilih firmware untuk memulai',
            consoleLog: [],
            port: null,
            transport: null,
            esploader: null,
            chipInfo: null,

            selectFirmware(type) {
                if (!this.isFlashing) {
                    this.selectedFirmware = type;
                    this.statusMessage = `Firmware ${type} dipilih. ${this.port ? 'Klik "Flash Firmware"' : 'Connect ke ESP32 terlebih dahulu'}.`;
                    this.addLog(`Selected firmware: ${type}`);
                }
            },

            addLog(message) {
                this.consoleLog.push(`[${new Date().toLocaleTimeString()}] ${message}`);
                this.$nextTick(() => {
                    this.$refs.console.scrollTop = this.$refs.console.scrollHeight;
                });
            },

            clearConsole() {
                this.consoleLog = [];
            },

            async _cleanupPort() {
                if (this.transport) {
                    try { await this.transport.disconnect(); } catch (e) {}
                    this.transport = null;
                }
                if (this.port) {
                    try { await this.port.close(); } catch (e) {}
                    this.port = null;
                }
                this.esploader = null;
                this.chipInfo = null;
            },

            async connectPort() {
                if (this.port || this.isConnecting || this.isFlashing) return;

                try {
                    this.isConnecting = true;
                    this.statusMessage = 'Membuka port serial...';
                    this.addLog('=== PORT CONNECTION ===');
                    this.addLog('Requesting serial port...');

                    this.port = await navigator.serial.requestPort({
                        filters: [
                            { usbVendorId: 0x303a },
                            { usbVendorId: 0x10c4 },
                            { usbVendorId: 0x1a86 },
                            { usbVendorId: 0x2886 },
                        ]
                    });

                    this.addLog('Port selected, initializing transport...');

                    const espLoaderTerminal = {
                        clean: () => {},
                        writeLine: (data) => this.addLog(data),
                        write: (data) => this.addLog(data)
                    };

                    // Let Transport handle port.open() internally
                    this.transport = new esptooljs.Transport(this.port);
                    this.esploader = new esptooljs.ESPLoader({
                        transport: this.transport,
                        baudrate: 115200,
                        terminal: espLoaderTerminal
                    });

                    this.statusMessage = 'Mendeteksi chip ESP32...';
                    this.addLog('Detecting chip...');
                    
                    await this.esploader.main();
                    this.addLog('Serial port opened at 115200 baud');
                    
                    this.chipInfo = {
                        type: this.esploader.chipName || 'ESP32',
                        macAddress: this.esploader.macAddr ? this.esploader.macAddr() : 'Unknown'
                    };

                    this.addLog(`✓ Chip detected: ${this.chipInfo.type}`);
                    this.addLog(`✓ MAC Address: ${this.chipInfo.macAddress}`);
                    this.statusMessage = `✓ Connected: ${this.chipInfo.type}`;

                    alert(`✓ ESP32 terdeteksi!\nChip: ${this.chipInfo.type}\nMAC: ${this.chipInfo.macAddress}`);

                } catch (error) {
                    if (error.name === 'NotFoundError') {
                        this.addLog('Port selection cancelled by user');
                        this.statusMessage = 'Pilih firmware untuk memulai';
                        await this._cleanupPort();
                        return;
                    }
                    console.error('Connection error:', error);
                    this.addLog(`ERROR: ${error.message}`);
                    this.statusMessage = '✗ Connection failed';
                    alert('Gagal connect: ' + error.message);
                    await this._cleanupPort();
                } finally {
                    this.isConnecting = false;
                }
            },

            async disconnectPort() {
                await this._cleanupPort();
                this.statusMessage = 'Disconnected';
                this.addLog('Port disconnected');
            },

            async startFlash() {
                if (!this.selectedFirmware || this.isFlashing || !this.isWebSerialSupported) return;

                if (!this.port) {
                    await this.connectPort();
                    if (!this.port) return;
                }

                try {
                    this.isFlashing = true;
                    this.progress = 0;
                    this.statusMessage = 'Mempersiapkan flash...';
                    this.addLog('=== FLASH STARTED ===');

                    const manifestUrl = `/flasher-firmware/${this.selectedFirmware}/manifest.json`;
                    const response = await fetch(manifestUrl);
                    if (!response.ok) throw new Error(`Manifest tidak ditemukan: ${manifestUrl}`);
                    const manifest = await response.json();

                    this.addLog(`Firmware: ${manifest.name} v${manifest.version}`);
                    this.addLog(`Chip Family: ${manifest.chipFamily}`);
                    this.statusMessage = 'Mengunduh firmware...';

                    const fileArray = await Promise.all(
                        manifest.parts.map(async (part) => {
                            const url = `/flasher-firmware/${this.selectedFirmware}/${part.path}`;
                            this.addLog(`Downloading: ${part.path}`);
                            const resp = await fetch(url);
                            if (!resp.ok) throw new Error(`Gagal download: ${url}`);
                            return { data: await resp.arrayBuffer(), address: part.offset };
                        })
                    );

                    if (!this.esploader) {
                        const espLoaderTerminal = {
                            clean: () => {},
                            writeLine: (data) => this.addLog(data),
                            write: (data) => this.addLog(data)
                        };
                        this.transport = new esptooljs.Transport(this.port);
                        this.esploader = new esptooljs.ESPLoader({
                            transport: this.transport,
                            baudrate: 115200,
                            terminal: espLoaderTerminal
                        });
                        this.statusMessage = 'Connecting to ESP32...';
                        await this.esploader.main();
                    }

                    this.statusMessage = 'Flashing firmware...';
                    this.addLog('Starting flash process...');

                    await this.esploader.writeFlash({
                        fileArray: fileArray,
                        flashSize: 'detect',
                        eraseAll: false,
                        compress: true,
                        reportProgress: (fileIndex, written, total) => {
                            this.progress = Math.round((written / total) * 100);
                            this.statusMessage = `Flashing: ${this.progress}%`;
                        }
                    });

                    this.progress = 100;
                    this.statusMessage = '✓ Flash berhasil!';
                    this.addLog('=== FLASH COMPLETE ===');
                    this.addLog('Rebooting ESP32...');

                    await this.esploader.hardReset();
                    await this._cleanupPort();

                    alert('✓ Firmware berhasil di-flash! ESP32 akan reboot otomatis.');

                } catch (error) {
                    console.error('Flash error:', error);
                    this.addLog(`ERROR: ${error.message}`);
                    this.statusMessage = '✗ Flash gagal: ' + error.message;
                    alert('Flash gagal: ' + error.message);
                    await this._cleanupPort();
                } finally {
                    this.isFlashing = false;
                }
            }
        }
    }
    </script>

</body>
</html>

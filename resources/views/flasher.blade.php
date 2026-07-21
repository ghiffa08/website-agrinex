@extends('layouts.app')

@section('title', 'ESP32 Web Flasher - AgriNex')

@section('content')
<div class="container-fluid px-4 py-6" x-data="flasherApp()">
    
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-darkText mb-2">ESP32 Web Flasher</h1>
        <p class="text-lightText">Flash firmware ke ESP32 XIAO S3 langsung dari browser</p>
    </div>

    {{-- Browser Compatibility Alert --}}
    <template x-if="!isWebSerialSupported">
        <div class="mb-6 p-6 rounded-3xl bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-2xl bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]">
                    <i class="bi bi-exclamation-triangle text-warning text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-darkText mb-2">Browser Tidak Mendukung Web Serial API</h3>
                    <p class="text-sm text-lightText mb-3">
                        Web Serial API diperlukan untuk flash ESP32. Gunakan browser yang mendukung:
                    </p>
                    <div class="flex gap-4 text-sm">
                        <span class="px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
                            <i class="bi bi-browser-chrome text-brand"></i> Chrome 89+
                        </span>
                        <span class="px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
                            <i class="bi bi-browser-edge text-brand"></i> Edge 89+
                        </span>
                        <span class="px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
                            <i class="bi bi-browser-safari text-brand"></i> Opera 75+
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
            <div class="p-6 rounded-3xl bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                <h2 class="text-xl font-bold text-darkText mb-4">Pilih Firmware</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Sender Firmware --}}
                    <div class="p-5 rounded-2xl bg-neuBg cursor-pointer transition-all duration-300"
                        :class="selectedFirmware === 'sender' ? 'shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_6px_#a3b1c6,inset_-4px_-4px_6px_#ffffff]'"
                        @click="selectFirmware('sender')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 rounded-xl bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]">
                                <i class="bi bi-broadcast text-brand text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-darkText">Sender</h3>
                                <span class="text-xs text-lightText">NODE01</span>
                            </div>
                        </div>
                        <p class="text-xs text-lightText">Sensor node yang mengirim data melalui LoRa</p>
                        <div class="mt-3 text-xs font-semibold text-brand" x-show="selectedFirmware === 'sender'">
                            <i class="bi bi-check-circle-fill"></i> Terpilih
                        </div>
                    </div>

                    {{-- Receiver Firmware --}}
                    <div class="p-5 rounded-2xl bg-neuBg cursor-pointer transition-all duration-300"
                        :class="selectedFirmware === 'receiver' ? 'shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_6px_#a3b1c6,inset_-4px_-4px_6px_#ffffff]'"
                        @click="selectFirmware('receiver')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 rounded-xl bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]">
                                <i class="bi bi-router text-brand text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-darkText">Receiver</h3>
                                <span class="text-xs text-lightText">NODE02</span>
                            </div>
                        </div>
                        <p class="text-xs text-lightText">Gateway yang menerima data dan kontrol valve</p>
                        <div class="mt-3 text-xs font-semibold text-brand" x-show="selectedFirmware === 'receiver'">
                            <i class="bi bi-check-circle-fill"></i> Terpilih
                        </div>
                    </div>

                    {{-- Tester Firmware --}}
                    <div class="p-5 rounded-2xl bg-neuBg cursor-pointer transition-all duration-300"
                        :class="selectedFirmware === 'tester' ? 'shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]' : 'shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_6px_#a3b1c6,inset_-4px_-4px_6px_#ffffff]'"
                        @click="selectFirmware('tester')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 rounded-xl bg-neuBg shadow-[inset_3px_3px_6px_#a3b1c6,inset_-3px_-3px_6px_#ffffff]">
                                <i class="bi bi-wrench text-brand text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-darkText">Tester</h3>
                                <span class="text-xs text-lightText">QC Tool</span>
                            </div>
                        </div>
                        <p class="text-xs text-lightText">Firmware testing untuk quality control</p>
                        <div class="mt-3 text-xs font-semibold text-brand" x-show="selectedFirmware === 'tester'">
                            <i class="bi bi-check-circle-fill"></i> Terpilih
                        </div>
                    </div>
                </div>
            </div>

            {{-- WiFi Config (Only for Receiver) --}}
            <div class="p-6 rounded-3xl bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]"
                x-show="selectedFirmware === 'receiver'" x-cloak>
                <h2 class="text-xl font-bold text-darkText mb-4">Konfigurasi WiFi (Opsional)</h2>
                
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

            {{-- Flash Button --}}
            <div class="flex justify-center">
                <button @click="startFlash" :disabled="!selectedFirmware || isFlashing || !isWebSerialSupported"
                    class="px-8 py-4 rounded-2xl font-bold text-white transition-all duration-300"
                    :class="!selectedFirmware || isFlashing || !isWebSerialSupported 
                        ? 'bg-gray-400 cursor-not-allowed shadow-[4px_4px_8px_#8a96a8,-4px_-4px_8px_#d4dce6]' 
                        : 'bg-brand hover:shadow-[inset_4px_4px_8px_rgba(0,0,0,0.2),inset_-4px_-4px_8px_rgba(255,255,255,0.1)] shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] active:shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]'">
                    <i class="bi" :class="isFlashing ? 'bi-hourglass-split animate-spin' : 'bi-lightning-charge-fill'"></i>
                    <span x-text="isFlashing ? 'Flashing...' : 'Flash Firmware'"></span>
                </button>
            </div>
        </div>

        {{-- Right Panel: Status & Console --}}
        <div class="space-y-6">
            
            {{-- Status Card --}}
            <div class="p-6 rounded-3xl bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                <h2 class="text-lg font-bold text-darkText mb-4">Status</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-lightText">Firmware:</span>
                        <span class="text-sm font-bold text-darkText" x-text="selectedFirmware || '-'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-lightText">Port:</span>
                        <span class="text-sm font-bold text-darkText" x-text="port ? 'Connected' : 'Not Connected'"></span>
                    </div>
                    <div class="flex items-center justify-between">
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

            {{-- Serial Monitor --}}
            <div class="p-6 rounded-3xl bg-neuBg shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-darkText">Serial Monitor</h2>
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

@push('scripts')
<script src="https://unpkg.com/esptool-js@0.4.4/bundle.js"></script>
<script>
function flasherApp() {
    return {
        isWebSerialSupported: 'serial' in navigator,
        selectedFirmware: null,
        wifiSSID: '',
        wifiPassword: '',
        isFlashing: false,
        progress: 0,
        statusMessage: 'Pilih firmware untuk memulai',
        consoleLog: [],
        port: null,
        esploader: null,

        selectFirmware(type) {
            if (!this.isFlashing) {
                this.selectedFirmware = type;
                this.statusMessage = `Firmware ${type} dipilih. Klik "Flash Firmware" untuk memulai.`;
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

        async startFlash() {
            if (!this.selectedFirmware || this.isFlashing || !this.isWebSerialSupported) return;

            try {
                this.isFlashing = true;
                this.progress = 0;
                this.statusMessage = 'Menghubungkan ke ESP32...';
                this.addLog('=== FLASH STARTED ===');
                this.addLog('Requesting serial port...');

                // Request serial port
                this.port = await navigator.serial.requestPort();
                await this.port.open({ baudRate: 115200 });

                this.addLog('Serial port opened');
                this.statusMessage = 'Mengunduh firmware...';

                // Load firmware files
                const manifestUrl = `/flasher-firmware/${this.selectedFirmware}/manifest.json`;
                const response = await fetch(manifestUrl);
                const manifest = await response.json();

                this.addLog(`Firmware: ${manifest.name} v${manifest.version}`);
                this.addLog(`Chip: ${manifest.chipFamily}`);

                // Download all parts
                this.statusMessage = 'Mempersiapkan flashing...';
                const fileArray = await Promise.all(
                    manifest.parts.map(async (part) => {
                        const url = `/flasher-firmware/${this.selectedFirmware}/${part.path}`;
                        const blob = await fetch(url).then(r => r.blob());
                        return {
                            data: await blob.arrayBuffer(),
                            address: part.offset
                        };
                    })
                );

                // Initialize ESPLoader
                const espLoaderTerminal = {
                    clean: () => {},
                    writeLine: (data) => this.addLog(data),
                    write: (data) => this.addLog(data)
                };

                this.esploader = new esptooljs.ESPLoader({
                    transport: new esptooljs.Transport(this.port),
                    baudrate: 115200,
                    terminal: espLoaderTerminal
                });

                // Connect
                this.statusMessage = 'Connecting to ESP32...';
                await this.esploader.main();

                // Flash
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

                // Hard reset
                await this.esploader.hardReset();

                // Close port
                await this.port.close();
                this.port = null;

                alert('✓ Firmware berhasil di-flash! ESP32 akan reboot otomatis.');

            } catch (error) {
                console.error('Flash error:', error);
                this.addLog(`ERROR: ${error.message}`);
                this.statusMessage = '✗ Flash gagal: ' + error.message;
                alert('Flash gagal: ' + error.message);

                if (this.port) {
                    try {
                        await this.port.close();
                    } catch (e) {}
                    this.port = null;
                }
            } finally {
                this.isFlashing = false;
            }
        }
    }
}
</script>
@endpush

@endsection

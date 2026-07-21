@extends('layouts.app')

@section('title', 'ESP32 Web Flasher - AgriNex IoT')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-2">
                        <i class="bi bi-cpu"></i> ESP32 Web Flasher
                    </h3>
                    <p class="mb-0 opacity-75">Flash firmware NODE01-Sender, NODE02-Receiver, atau Tester langsung dari browser</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Browser Compatibility Warning --}}
    <div class="row mb-4" id="browserWarning" style="display: none;">
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Browser Tidak Didukung!</strong> Web Serial API hanya tersedia di Chrome, Edge, atau Opera versi terbaru.
                Silakan gunakan browser tersebut untuk mengakses fitur ini.
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left Panel: Firmware Selection --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-binary"></i> Pilih Firmware</h5>
                </div>
                <div class="card-body">
                    {{-- Firmware Type Selection --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipe Firmware</label>
                        <select class="form-select" id="firmwareType">
                            <option value="">-- Pilih Firmware --</option>
                            <option value="sender">NODE01 - Sender (LoRa TX)</option>
                            <option value="receiver">NODE02 - Receiver (LoRa RX + WiFi)</option>
                            <option value="tester">Node Tester (Diagnostics)</option>
                        </select>
                    </div>

                    {{-- Firmware Info --}}
                    <div id="firmwareInfo" style="display: none;" class="alert alert-info">
                        <h6 class="fw-bold mb-2" id="firmwareName"></h6>
                        <p class="mb-1 small" id="firmwareDesc"></p>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between small">
                            <span><i class="bi bi-chip"></i> Board:</span>
                            <span class="fw-bold" id="firmwareBoard">Seeed XIAO ESP32-S3</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span><i class="bi bi-hdd"></i> Size:</span>
                            <span class="fw-bold" id="firmwareSize">~500 KB</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span><i class="bi bi-calendar-check"></i> Version:</span>
                            <span class="fw-bold" id="firmwareVersion">v2.1.0</span>
                        </div>
                    </div>

                    {{-- Configuration (for Receiver) --}}
                    <div id="configSection" style="display: none;">
                        <hr>
                        <h6 class="fw-bold mb-3"><i class="bi bi-gear"></i> Konfigurasi WiFi</h6>
                        <div class="mb-2">
                            <label class="form-label small">WiFi SSID</label>
                            <input type="text" class="form-control form-control-sm" id="wifiSSID" placeholder="AgriNex_WiFi">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">WiFi Password</label>
                            <input type="password" class="form-control form-control-sm" id="wifiPassword" placeholder="********">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">API Key (dari Server)</label>
                            <input type="text" class="form-control form-control-sm" id="apiKey" placeholder="your-api-key-here" value="{{ config('iot.api_key', 'default-key') }}">
                        </div>
                    </div>

                    {{-- Flash Button --}}
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-success btn-lg" id="flashBtn" disabled>
                            <i class="bi bi-lightning-charge-fill"></i> Flash Firmware
                        </button>
                    </div>
                </div>
            </div>

            {{-- Quick Guide --}}
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Panduan Cepat</h6>
                </div>
                <div class="card-body small">
                    <ol class="ps-3 mb-0">
                        <li class="mb-2">Hubungkan ESP32 ke laptop via USB-C</li>
                        <li class="mb-2">Pilih tipe firmware yang ingin di-flash</li>
                        <li class="mb-2">Klik tombol <strong>"Flash Firmware"</strong></li>
                        <li class="mb-2">Pilih port serial yang muncul (biasanya <code>ttyACM0</code> atau <code>USB Serial</code>)</li>
                        <li class="mb-2">Tunggu proses flashing selesai (~30-60 detik)</li>
                        <li>Cek log serial untuk memastikan device boot dengan benar</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Right Panel: Flash Progress & Serial Monitor --}}
        <div class="col-lg-8">
            {{-- Flash Progress --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-activity"></i> Status Flashing</h5>
                </div>
                <div class="card-body">
                    <div id="flashStatus" class="text-center text-muted py-4">
                        <i class="bi bi-lightning-charge" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-2">Belum ada proses flashing. Pilih firmware dan klik tombol "Flash Firmware".</p>
                    </div>

                    <div id="flashProgress" style="display: none;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="spinner-border text-primary me-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold" id="progressText">Connecting to ESP32...</div>
                                <div class="text-muted small" id="progressSubtext">Initializing...</div>
                            </div>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                 role="progressbar" 
                                 id="progressBar" 
                                 style="width: 0%">0%</div>
                        </div>
                        <div class="mt-2 small text-muted">
                            <span id="progressBytes">0 / 0 KB</span>
                            <span class="float-end" id="progressSpeed">0 KB/s</span>
                        </div>
                    </div>

                    <div id="flashSuccess" style="display: none;" class="text-center py-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h4 class="text-success mt-3">Flashing Berhasil!</h4>
                        <p class="text-muted">Firmware berhasil di-upload ke ESP32. Device akan restart otomatis.</p>
                        <button class="btn btn-primary mt-2" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Flash Lagi
                        </button>
                    </div>

                    <div id="flashError" style="display: none;" class="text-center py-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        <h4 class="text-danger mt-3">Flashing Gagal!</h4>
                        <p class="text-muted" id="errorMessage">Terjadi kesalahan saat flashing.</p>
                        <button class="btn btn-warning mt-2" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                        </button>
                    </div>
                </div>
            </div>

            {{-- Serial Monitor --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-terminal"></i> Serial Monitor</h5>
                    <div>
                        <button class="btn btn-sm btn-light" id="connectSerial" disabled>
                            <i class="bi bi-plug"></i> Connect
                        </button>
                        <button class="btn btn-sm btn-outline-light" id="clearSerial">
                            <i class="bi bi-trash"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="serialOutput" class="bg-dark text-light font-monospace p-3" style="height: 400px; overflow-y: auto; font-size: 0.85rem;">
                        <div class="text-muted">Serial monitor ready. Connect to ESP32 to see output...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ESP Web Tools Script --}}
<script type="module">
    import { ESPLoader, Transport } from 'https://unpkg.com/esptool-js@0.4.2/bundle.js';

    const firmwareData = {
        sender: {
            name: 'NODE01 - Sender',
            desc: 'LoRa Transmitter dengan sensor suhu, kelembaban tanah, INA226 power monitor',
            size: '~480 KB',
            version: 'v2.1.0',
            manifest: '/flasher-firmware/sender/manifest.json'
        },
        receiver: {
            name: 'NODE02 - Receiver',
            desc: 'LoRa Receiver dengan WiFi sync ke cloud API dan Fuzzy AI control',
            size: '~520 KB',
            version: 'v2.1.0',
            manifest: '/flasher-firmware/receiver/manifest.json'
        },
        tester: {
            name: 'Node Tester',
            desc: 'Diagnostics tool untuk testing sensor dan komunikasi LoRa',
            size: '~450 KB',
            version: 'v1.0.0',
            manifest: '/flasher-firmware/tester/manifest.json'
        }
    };

    let port, transport, esploader;

    // Check browser compatibility
    if (!('serial' in navigator)) {
        document.getElementById('browserWarning').style.display = 'block';
        document.getElementById('flashBtn').disabled = true;
    }

    // Firmware type selection
    document.getElementById('firmwareType').addEventListener('change', function() {
        const type = this.value;
        if (type) {
            const fw = firmwareData[type];
            document.getElementById('firmwareName').textContent = fw.name;
            document.getElementById('firmwareDesc').textContent = fw.desc;
            document.getElementById('firmwareSize').textContent = fw.size;
            document.getElementById('firmwareVersion').textContent = fw.version;
            document.getElementById('firmwareInfo').style.display = 'block';
            document.getElementById('flashBtn').disabled = false;

            // Show config section for receiver
            if (type === 'receiver') {
                document.getElementById('configSection').style.display = 'block';
            } else {
                document.getElementById('configSection').style.display = 'none';
            }
        } else {
            document.getElementById('firmwareInfo').style.display = 'none';
            document.getElementById('configSection').style.display = 'none';
            document.getElementById('flashBtn').disabled = true;
        }
    });

    // Flash firmware button
    document.getElementById('flashBtn').addEventListener('click', async function() {
        const type = document.getElementById('firmwareType').value;
        if (!type) return;

        try {
            // Show progress UI
            document.getElementById('flashStatus').style.display = 'none';
            document.getElementById('flashSuccess').style.display = 'none';
            document.getElementById('flashError').style.display = 'none';
            document.getElementById('flashProgress').style.display = 'block';

            updateProgress(0, 'Requesting serial port access...', 'Select your ESP32 device');

            // Request port
            port = await navigator.serial.requestPort({
                filters: [
                    { usbVendorId: 0x303A }, // Espressif
                    { usbVendorId: 0x10C4 }, // Silicon Labs CP210x
                    { usbVendorId: 0x1A86 }, // QinHeng CH340
                    { usbVendorId: 0x2886 }  // Seeed Studio
                ]
            });

            await port.open({ baudRate: 115200 });
            transport = new Transport(port, true);
            esploader = new ESPLoader({
                transport: transport,
                baudrate: 115200,
                terminal: {
                    clean() {},
                    writeLine(data) {
                        logSerial(data);
                    },
                    write(data) {
                        logSerial(data, false);
                    }
                }
            });

            updateProgress(10, 'Connecting to ESP32...', 'Detecting chip type');

            const chip = await esploader.main();
            updateProgress(20, `Connected: ${chip}`, 'Reading MAC address');

            const macAddr = await esploader.chipId();
            updateProgress(30, `MAC: ${macAddr}`, 'Loading firmware');

            // Load firmware manifest
            const manifestUrl = firmwareData[type].manifest;
            const manifestResp = await fetch(manifestUrl);
            const manifest = await manifestResp.json();

            updateProgress(40, 'Erasing flash...', 'This may take a minute');

            // Flash firmware
            let progress = 40;
            for (const file of manifest.parts) {
                const fileResp = await fetch(file.path);
                const fileData = await fileResp.arrayBuffer();
                
                updateProgress(progress, `Writing ${file.path}...`, `Offset: 0x${file.offset.toString(16)}`);
                
                await esploader.writeFlash({
                    fileArray: [{
                        data: new Uint8Array(fileData),
                        address: file.offset
                    }],
                    flashSize: 'keep',
                    flashMode: 'dio',
                    flashFreq: '80m',
                    eraseAll: false,
                    compress: true,
                    reportProgress: (idx, written, total) => {
                        const percent = (written / total) * 20; // 20% per file
                        updateProgress(progress + percent, `Writing ${file.path}...`, `${written} / ${total} bytes`);
                    }
                });
                
                progress += 20;
            }

            updateProgress(100, 'Flashing complete!', 'Device will restart');

            // Hard reset
            await esploader.hardReset();

            // Show success
            document.getElementById('flashProgress').style.display = 'none';
            document.getElementById('flashSuccess').style.display = 'block';

            // Enable serial monitor
            document.getElementById('connectSerial').disabled = false;

        } catch (error) {
            console.error('Flash error:', error);
            document.getElementById('flashProgress').style.display = 'none';
            document.getElementById('flashError').style.display = 'block';
            document.getElementById('errorMessage').textContent = error.message || 'Unknown error occurred';
        }
    });

    function updateProgress(percent, text, subtext) {
        document.getElementById('progressBar').style.width = percent + '%';
        document.getElementById('progressBar').textContent = Math.round(percent) + '%';
        document.getElementById('progressText').textContent = text;
        document.getElementById('progressSubtext').textContent = subtext;
    }

    function logSerial(text, newline = true) {
        const output = document.getElementById('serialOutput');
        const line = document.createElement('div');
        line.textContent = text;
        if (newline) line.style.marginBottom = '2px';
        output.appendChild(line);
        output.scrollTop = output.scrollHeight;
    }

    // Serial monitor connect
    document.getElementById('connectSerial').addEventListener('click', async function() {
        if (this.textContent.includes('Connect')) {
            try {
                if (!port) {
                    port = await navigator.serial.requestPort();
                    await port.open({ baudRate: 115200 });
                }

                const reader = port.readable.getReader();
                const textDecoder = new TextDecoder();

                this.innerHTML = '<i class="bi bi-x-circle"></i> Disconnect';
                this.classList.remove('btn-light');
                this.classList.add('btn-danger');

                while (true) {
                    const { value, done } = await reader.read();
                    if (done) break;
                    logSerial(textDecoder.decode(value), false);
                }
            } catch (error) {
                console.error('Serial error:', error);
                logSerial('Error: ' + error.message);
            }
        } else {
            if (port) {
                await port.close();
                port = null;
            }
            this.innerHTML = '<i class="bi bi-plug"></i> Connect';
            this.classList.remove('btn-danger');
            this.classList.add('btn-light');
        }
    });

    // Clear serial
    document.getElementById('clearSerial').addEventListener('click', function() {
        document.getElementById('serialOutput').innerHTML = '<div class="text-muted">Serial monitor cleared...</div>';
    });
</script>

<style>
    #serialOutput::-webkit-scrollbar {
        width: 8px;
    }
    #serialOutput::-webkit-scrollbar-track {
        background: #1a1a1a;
    }
    #serialOutput::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 4px;
    }
    #serialOutput::-webkit-scrollbar-thumb:hover {
        background: #777;
    }
</style>
@endsection

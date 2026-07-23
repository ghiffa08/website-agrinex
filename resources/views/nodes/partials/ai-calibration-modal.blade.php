<!-- AI Calibration Modal -->
<div x-data="aiCalibrationModal({{ $node->id }})" x-cloak>
    <!-- Trigger Button -->
    <button 
        @click="openModal()" 
        class="btn btn-success"
        :disabled="isProcessing || status === 'analyzing'"
    >
        <i class="bi bi-robot"></i> 
        <span x-text="getButtonText()"></span>
    </button>

    <!-- Modal -->
    <div 
        x-show="modalOpen" 
        @click.away="closeModal()"
        class="modal fade show d-block" 
        style="background: rgba(0,0,0,0.5);"
        x-transition
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-robot text-success"></i>
                        AI Kalibrasi Field Capacity - Node {{ $node->id }}
                    </h5>
                    <button type="button" class="btn-close" @click="closeModal()"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <!-- Step 1: Idle / Start -->
                    <div x-show="status === 'idle'">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Tentang Kalibrasi AI Field Capacity</strong>
                            <p class="mb-0 mt-2">
                                Sistem AI akan menganalisis data sensor selama 24-72 jam untuk mendeteksi 
                                kapasitas lapang (field capacity) tanah secara otomatis. Ini adalah titik 
                                kelembaban maksimal yang dapat ditahan tanah setelah drainase gravitasi selesai.
                            </p>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">📋 Langkah-langkah:</h6>
                                <ol class="mb-0">
                                    <li>Klik <strong>"Mulai Kalibrasi"</strong></li>
                                    <li>Siram tanah di sekitar sensor hingga jenuh air (saturasi penuh)</li>
                                    <li>Konfirmasi setelah penyiraman selesai</li>
                                    <li>Tunggu 24-72 jam, AI akan analisis secara otomatis</li>
                                    <li>Sistem akan memberitahu hasil kalibrasi</li>
                                </ol>
                            </div>
                        </div>

                        <button 
                            @click="startCalibration()" 
                            class="btn btn-success w-100"
                            :disabled="isProcessing"
                        >
                            <span x-show="!isProcessing">
                                <i class="bi bi-play-circle"></i> Mulai Kalibrasi
                            </span>
                            <span x-show="isProcessing">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Memulai...
                            </span>
                        </button>
                    </div>

                    <!-- Step 2: User Saturating -->
                    <div x-show="status === 'user_saturating'">
                        <div class="alert alert-warning">
                            <i class="bi bi-droplet-fill"></i>
                            <strong>Langkah 1: Saturasi Tanah</strong>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <i class="bi bi-water" style="font-size: 4rem; color: #0dcaf0;"></i>
                                </div>
                                <h5>Siram Tanah Hingga Jenuh</h5>
                                <p class="text-muted">
                                    Pastikan tanah di sekitar sensor benar-benar basah (saturasi penuh). 
                                    Air harus meresap ke seluruh area perakaran.
                                </p>
                                
                                <!-- Live Sensor Reading -->
                                <div class="bg-light p-3 rounded mt-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Kelembaban Saat Ini</small>
                                            <h3 class="mb-0" x-text="currentMoisture + '%'"></h3>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">ADC Raw</small>
                                            <h3 class="mb-0" x-text="currentADC"></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button 
                            @click="confirmSaturation()" 
                            class="btn btn-primary w-100"
                            :disabled="isProcessing"
                        >
                            <span x-show="!isProcessing">
                                <i class="bi bi-check-circle"></i> Konfirmasi Saturasi Selesai
                            </span>
                            <span x-show="isProcessing">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Menyimpan...
                            </span>
                        </button>

                        <button 
                            @click="cancelCalibration()" 
                            class="btn btn-outline-secondary w-100 mt-2"
                            :disabled="isProcessing"
                        >
                            Batalkan
                        </button>
                    </div>

                    <!-- Step 3: Waiting 24h -->
                    <div x-show="status === 'waiting_24h'">
                        <div class="alert alert-info">
                            <i class="bi bi-hourglass-split"></i>
                            <strong>Menunggu Drainase Gravitasi</strong>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <i class="bi bi-clock-history" style="font-size: 4rem; color: #0d6efd;"></i>
                                </div>
                                <h5>AI Sedang Memantau</h5>
                                <p class="text-muted">
                                    Sistem AI mengumpulkan data sensor untuk analisis field capacity. 
                                    Analisis otomatis akan dilakukan setiap 2 jam.
                                </p>
                                
                                <!-- Progress Info -->
                                <div class="bg-light p-3 rounded mt-3">
                                    <div class="row text-start">
                                        <div class="col-6 mb-2">
                                            <small class="text-muted">Saturasi Dimulai</small><br>
                                            <strong x-text="formatDate(saturationAt)"></strong>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <small class="text-muted">Iterasi Analisis</small><br>
                                            <strong x-text="'#' + iteration"></strong>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <small class="text-muted">Waktu Tersisa</small><br>
                                            <strong x-text="hoursRemaining ? hoursRemaining + ' jam' : 'Siap untuk analisis'"></strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Manual Trigger (if ready) -->
                                <div x-show="canAnalyze" class="mt-3">
                                    <div class="alert alert-success mb-2">
                                        <i class="bi bi-check-circle"></i>
                                        Data sudah cukup untuk analisis!
                                    </div>
                                    <button 
                                        @click="triggerAnalysis()" 
                                        class="btn btn-success w-100"
                                        :disabled="isProcessing"
                                    >
                                        <span x-show="!isProcessing">
                                            <i class="bi bi-play-fill"></i> Analisis Sekarang
                                        </span>
                                        <span x-show="isProcessing">
                                            <span class="spinner-border spinner-border-sm me-2"></span>
                                            Menganalisis...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button 
                            @click="closeModal()" 
                            class="btn btn-outline-secondary w-100"
                        >
                            Tutup
                        </button>
                    </div>

                    <!-- Step 4: Analyzing -->
                    <div x-show="status === 'analyzing'">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="mb-3">
                                    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                <h5>🤖 AI Sedang Menganalisis Data...</h5>
                                <p class="text-muted">
                                    Gemini AI sedang memproses data sensor untuk mendeteksi field capacity. 
                                    Ini mungkin memakan waktu 30-60 detik.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Completed -->
                    <div x-show="status === 'completed'">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <strong>Kalibrasi Berhasil!</strong>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">📊 Hasil Analisis AI</h5>
                                
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Field Capacity (ADC)</small>
                                            <h3 class="mb-0 text-success" x-text="results?.field_capacity_adc"></h3>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Confidence Score</small>
                                            <h3 class="mb-0" :class="getConfidenceClass(results?.confidence_score)" x-text="results?.confidence_score + '%'"></h3>
                                        </div>
                                    </div>
                                    <div class="col-12" x-show="results?.wilting_point_adc">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Wilting Point (ADC)</small>
                                            <h3 class="mb-0 text-danger" x-text="results?.wilting_point_adc"></h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3" x-show="results?.analysis_quality">
                                    <small class="text-muted">Kualitas Analisis:</small>
                                    <span class="badge ms-2" :class="getQualityBadge(results?.analysis_quality)" x-text="results?.analysis_quality?.toUpperCase()"></span>
                                </div>

                                <div class="mt-3" x-show="results?.reasoning">
                                    <small class="text-muted d-block mb-1">💡 Insight AI:</small>
                                    <p class="text-sm bg-light p-2 rounded mb-0" x-text="results?.reasoning"></p>
                                </div>

                                <div class="mt-3" x-show="results?.recommendations?.length">
                                    <small class="text-muted d-block mb-1">📌 Rekomendasi:</small>
                                    <ul class="text-sm mb-0">
                                        <template x-for="rec in results?.recommendations" :key="rec">
                                            <li x-text="rec"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <button @click="closeModal()" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg"></i> Selesai
                        </button>
                    </div>

                    <!-- Step 6: Failed -->
                    <div x-show="status === 'failed'">
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle-fill"></i>
                            <strong>Kalibrasi Gagal</strong>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <p x-text="errorMessage || 'Terjadi kesalahan saat analisis. Silakan coba lagi.'"></p>
                            </div>
                        </div>

                        <button @click="resetCalibration()" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function aiCalibrationModal(nodeId) {
    return {
        modalOpen: false,
        isProcessing: false,
        status: 'idle',
        startedAt: null,
        saturationAt: null,
        completedAt: null,
        iteration: 0,
        canAnalyze: false,
        hoursRemaining: null,
        results: null,
        errorMessage: null,
        currentMoisture: 0,
        currentADC: 0,
        pollInterval: null,

        init() {
            // Fetch initial status
            this.fetchStatus();
            
            // Poll status every 30 seconds when modal is open
            this.$watch('modalOpen', (value) => {
                if (value) {
                    this.startPolling();
                } else {
                    this.stopPolling();
                }
            });
        },

        openModal() {
            this.modalOpen = true;
            this.fetchStatus();
        },

        closeModal() {
            this.modalOpen = false;
        },

        startPolling() {
            this.pollInterval = setInterval(() => {
                this.fetchStatus();
            }, 30000); // 30 seconds
        },

        stopPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        },

        async fetchStatus() {
            try {
                const response = await fetch(`/api/v1/devices/${nodeId}/ai-calibration/status`);
                const data = await response.json();
                
                if (data.success) {
                    this.status = data.data.status;
                    this.startedAt = data.data.started_at;
                    this.saturationAt = data.data.saturation_at;
                    this.completedAt = data.data.completed_at;
                    this.iteration = data.data.iteration;
                    this.canAnalyze = data.data.can_analyze;
                    this.hoursRemaining = data.data.hours_remaining;
                    
                    if (data.data.results) {
                        this.results = data.data.results;
                    }
                }
            } catch (error) {
                console.error('Failed to fetch calibration status:', error);
            }
        },

        async startCalibration() {
            this.isProcessing = true;
            
            try {
                const response = await fetch(`/api/v1/devices/${nodeId}/ai-calibration/start`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.status = data.status;
                    this.startedAt = data.started_at;
                    alert('✅ ' + data.message);
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Gagal memulai kalibrasi: ' + error.message);
            } finally {
                this.isProcessing = false;
            }
        },

        async confirmSaturation() {
            this.isProcessing = true;
            
            try {
                const response = await fetch(`/api/v1/devices/${nodeId}/ai-calibration/confirm-saturation`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.status = data.status;
                    this.saturationAt = data.saturation_at;
                    alert('✅ ' + data.message);
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Gagal konfirmasi saturasi: ' + error.message);
            } finally {
                this.isProcessing = false;
            }
        },

        async triggerAnalysis() {
            this.isProcessing = true;
            this.status = 'analyzing';
            
            try {
                const response = await fetch(`/api/v1/devices/${nodeId}/ai-calibration/analyze`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.status = data.status === 'completed' ? 'completed' : 'waiting_24h';
                    
                    if (data.results) {
                        this.results = data.results;
                    }
                    
                    alert('✅ ' + data.message);
                } else {
                    this.status = 'failed';
                    this.errorMessage = data.message;
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                this.status = 'failed';
                this.errorMessage = error.message;
                alert('❌ Gagal analisis: ' + error.message);
            } finally {
                this.isProcessing = false;
            }
        },

        async cancelCalibration() {
            if (!confirm('Yakin ingin membatalkan kalibrasi?')) return;
            
            this.isProcessing = true;
            
            try {
                const response = await fetch(`/api/v1/devices/${nodeId}/ai-calibration/cancel`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.status = 'idle';
                    this.closeModal();
                    alert('✅ Kalibrasi dibatalkan');
                }
            } catch (error) {
                alert('❌ Gagal membatalkan: ' + error.message);
            } finally {
                this.isProcessing = false;
            }
        },

        async resetCalibration() {
            await this.cancelCalibration();
            this.status = 'idle';
        },

        getButtonText() {
            const statusText = {
                'idle': 'Kalibrasi AI',
                'user_saturating': 'Lanjutkan Kalibrasi',
                'waiting_24h': 'Status Kalibrasi',
                'analyzing': 'Menganalisis...',
                'completed': 'Lihat Hasil',
                'failed': 'Coba Lagi'
            };
            return statusText[this.status] || 'Kalibrasi AI';
        },

        formatDate(isoString) {
            if (!isoString) return '-';
            const date = new Date(isoString);
            return date.toLocaleString('id-ID', { 
                day: '2-digit', 
                month: 'short', 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },

        getConfidenceClass(score) {
            if (score >= 80) return 'text-success';
            if (score >= 60) return 'text-warning';
            return 'text-danger';
        },

        getQualityBadge(quality) {
            const badges = {
                'excellent': 'bg-success',
                'good': 'bg-primary',
                'fair': 'bg-warning',
                'poor': 'bg-danger'
            };
            return badges[quality] || 'bg-secondary';
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>

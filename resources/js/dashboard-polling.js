/**
 * Dashboard Polling System
 * Menggantikan WebSocket dengan AJAX polling yang optimal untuk hosting shared
 */

class DashboardPoller {
    constructor(options = {}) {
        this.interval = options.interval || 20000; // 20 detik default
        this.endpoint = options.endpoint || '/api/v1/dashboard/poll';
        this.lastUpdate = 0;
        this.pollTimer = null;
        this.isPolling = false;
        this.callbacks = {
            onUpdate: options.onUpdate || (() => {}),
            onError: options.onError || (() => {}),
        };
    }

    start() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        this.poll(); // Poll langsung saat start
        
        // Set interval polling
        this.pollTimer = setInterval(() => {
            this.poll();
        }, this.interval);
        
        console.log('[Polling] Started with interval:', this.interval, 'ms');
    }

    stop() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
        this.isPolling = false;
        console.log('[Polling] Stopped');
    }

    async poll() {
        try {
            const response = await axios.get(this.endpoint, {
                params: { last_update: this.lastUpdate },
                timeout: 10000, // 10 detik timeout
            });

            if (response.data.success) {
                if (response.data.has_changes) {
                    this.lastUpdate = response.data.last_update;
                    this.callbacks.onUpdate(response.data.data);
                    console.log('[Polling] Data updated at:', new Date(this.lastUpdate * 1000));
                } else {
                    console.log('[Polling] No changes detected');
                }
            }
        } catch (error) {
            console.error('[Polling] Error:', error.message);
            this.callbacks.onError(error);
        }
    }

    // Update interval dinamis (misal saat tab tidak aktif, perlambat polling)
    setInterval(newInterval) {
        this.interval = newInterval;
        if (this.isPolling) {
            this.stop();
            this.start();
        }
    }
}

// Export untuk digunakan di file lain
window.DashboardPoller = DashboardPoller;

// Auto-start jika ada element dashboard
document.addEventListener('DOMContentLoaded', function() {
    const dashboardElement = document.querySelector('[data-dashboard-polling]');
    
    if (dashboardElement) {
        const pollingInterval = parseInt(dashboardElement.dataset.pollingInterval) || 20000;
        
        const poller = new DashboardPoller({
            interval: pollingInterval,
            endpoint: '/api/v1/dashboard/poll',
            
            onUpdate: (data) => {
                // Trigger custom event yang bisa di-listen di Alpine.js
                window.dispatchEvent(new CustomEvent('dashboard:updated', {
                    detail: data
                }));
            },
            
            onError: (error) => {
                console.error('Dashboard polling error:', error);
            }
        });
        
        poller.start();
        
        // Simpan instance ke window untuk kontrol manual
        window.dashboardPoller = poller;
        
        // Pause polling saat tab tidak aktif (hemat resource)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                poller.setInterval(60000); // 1 menit saat tab hidden
                console.log('[Polling] Tab hidden, slowing down to 60s');
            } else {
                poller.setInterval(pollingInterval); // Kembali normal
                console.log('[Polling] Tab visible, resuming normal interval');
            }
        });
        
        // Stop polling saat user akan keluar dari halaman
        window.addEventListener('beforeunload', function() {
            poller.stop();
        });
    }
});

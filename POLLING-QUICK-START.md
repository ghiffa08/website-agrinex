# 🚀 Quick Start - AJAX Polling System

**Sistem polling sudah aktif dan siap digunakan!**

---

## 📖 Cara Pakai

### 1️⃣ Di View/Blade Template

Tambahkan attribute di container dashboard:

```html
<div data-dashboard-polling data-polling-interval="20000">
    <!-- Your dashboard content -->
</div>
```

**Otomatis akan**:
- ✅ Start polling setiap 20 detik
- ✅ Pause saat tab hidden (hemat bandwidth)
- ✅ Emit event `dashboard:updated` untuk Alpine.js

---

### 2️⃣ Listen Update di Alpine.js

```javascript
<div x-data="dashboard()">
    <!-- Dashboard content -->
</div>

<script>
function dashboard() {
    return {
        devices: [],
        weather: {},
        
        init() {
            // Listen untuk update dari polling
            window.addEventListener('dashboard:updated', (event) => {
                this.devices = event.detail.devices;
                this.weather = event.detail.weather;
                
                // Trigger Alpine reactivity
                this.$nextTick(() => {
                    console.log('Dashboard updated:', event.detail);
                });
            });
        }
    }
}
</script>
```

---

### 3️⃣ Manual Control (Optional)

```javascript
// Access global instance
const poller = window.dashboardPoller;

// Start
poller.start();

// Stop
poller.stop();

// Change interval
poller.setInterval(30000); // 30 detik

// Force poll now
poller.poll();
```

---

## 🎛️ Configuration Options

### Polling Interval

```html
<!-- 15 detik (fast) -->
<div data-dashboard-polling data-polling-interval="15000">

<!-- 20 detik (default, recommended) -->
<div data-dashboard-polling data-polling-interval="20000">

<!-- 30 detik (slow, hemat bandwidth) -->
<div data-dashboard-polling data-polling-interval="30000">
```

**Recommendation**:
- Production: 20-30 detik
- Development: 10-15 detik
- Demo/Testing: 5-10 detik

---

## 🔌 API Endpoints

### Full Data Polling

```bash
GET /api/v1/dashboard/poll?last_update={timestamp}
```

**Response (ada perubahan)**:
```json
{
    "success": true,
    "has_changes": true,
    "last_update": 1720876800,
    "data": {
        "devices": [
            {
                "id": 1,
                "name": "Node 1",
                "location": "Greenhouse A",
                "is_active": true,
                "latest_data": {
                    "temperature": 28.5,
                    "soil_moisture": 65.3,
                    "humidity": 75.0,
                    "recorded_at": "2026-07-13 19:30:00"
                }
            }
        ],
        "weather": {
            "temperature": 29.0,
            "humidity": 70,
            "recorded_at": "2026-07-13 19:30:00"
        }
    }
}
```

**Response (tidak ada perubahan)**:
```json
{
    "success": true,
    "has_changes": false,
    "last_update": 1720876800
}
```

---

### Status Only (Lightweight)

```bash
GET /api/v1/dashboard/poll-status?last_update={timestamp}
```

**Response**:
```json
{
    "success": true,
    "has_changes": true,
    "last_update": 1720876800,
    "data": {
        "devices": [
            {"id": 1, "name": "Node 1", "online": true},
            {"id": 2, "name": "Node 2", "online": false}
        ]
    }
}
```

---

## 🧪 Testing

### Browser Console

Buka DevTools Console (F12), expected output:

```
[Polling] Started with interval: 20000 ms
[Polling] No changes detected
[Polling] Data updated at: Sun Jul 13 2026 19:30:00 GMT+0700
[Polling] Tab hidden, slowing down to 60s
[Polling] Tab visible, resuming normal interval
```

### Network Tab

Check di DevTools Network tab:
- Request URL: `/api/v1/dashboard/poll?last_update=...`
- Method: `GET`
- Status: `200 OK`
- Interval: ~20 detik
- Size: ~5KB (dengan data), ~100B (tanpa perubahan)

### Manual Test via curl

```bash
# Test dari terminal
curl -X GET "http://localhost:8000/api/v1/dashboard/poll?last_update=0" | jq

# Test dengan timestamp tinggi (simulate no changes)
curl -X GET "http://localhost:8000/api/v1/dashboard/poll?last_update=9999999999" | jq
```

---

## 🐛 Debugging

### Polling tidak start

**Check**:
1. Attribute ada? `data-dashboard-polling`
2. JS error di console?
3. File `app.js` ter-load?

**Solution**:
```javascript
// Manual start
const poller = new DashboardPoller({
    interval: 20000,
    endpoint: '/api/v1/dashboard/poll'
});
poller.start();
```

### Data tidak update

**Check**:
1. API endpoint return 200?
2. `has_changes: true` di response?
3. Event listener terpasang?

**Solution**:
```javascript
// Test event listener
window.addEventListener('dashboard:updated', (e) => {
    console.log('Event received:', e.detail);
});
```

### Too many requests (429)

**Check**:
1. Interval terlalu cepat?
2. Multiple tabs open?
3. Throttle limit di server?

**Solution**:
```javascript
// Increase interval
window.dashboardPoller.setInterval(30000);
```

---

## 📊 Performance Tips

### Bandwidth Optimization

**Use conditional request** (sudah otomatis):
- Client kirim `last_update` timestamp
- Server cek ada perubahan atau tidak
- Hanya kirim full data jika berubah
- Hemat bandwidth ~80%

### CPU Optimization

**Auto pause saat tidak aktif** (sudah otomatis):
- Tab active: 20 detik interval
- Tab hidden: 60 detik interval
- Window minimized: polling tetap jalan tapi lambat

### Memory Optimization

**No memory leak**:
- Event listener dibersihkan saat `beforeunload`
- Timer di-clear saat stop
- No circular reference

---

## 🔧 Advanced Usage

### Custom Callback

```javascript
const poller = new DashboardPoller({
    interval: 20000,
    endpoint: '/api/v1/dashboard/poll',
    
    onUpdate: (data) => {
        // Custom handler
        updateUI(data.devices);
        updateChart(data.weather);
    },
    
    onError: (error) => {
        // Custom error handler
        showToast('Polling error: ' + error.message, 'error');
    }
});

poller.start();
```

### Conditional Polling

```javascript
// Hanya polling jika user login
if (isAuthenticated) {
    window.dashboardPoller.start();
}

// Stop polling saat logout
function logout() {
    window.dashboardPoller.stop();
    // ... logout logic
}
```

### Multiple Endpoints

```javascript
// Device status poller (lightweight, frequent)
const statusPoller = new DashboardPoller({
    interval: 10000,
    endpoint: '/api/v1/dashboard/poll-status'
});

// Full data poller (heavy, infrequent)
const dataPoller = new DashboardPoller({
    interval: 60000,
    endpoint: '/api/v1/dashboard/poll'
});

statusPoller.start();
dataPoller.start();
```

---

## 📚 Documentation

### Full Docs
- [MIGRATION-WEBSOCKET-TO-POLLING.md](./MIGRATION-WEBSOCKET-TO-POLLING.md) - Technical details
- [MIGRATION-SUMMARY.md](./MIGRATION-SUMMARY.md) - Executive summary
- [DEPLOYMENT-CHECKLIST.md](./DEPLOYMENT-CHECKLIST.md) - Deployment guide

### Source Code
- Backend: `app/Http/Controllers/Api/DashboardPollingController.php`
- Frontend: `resources/js/dashboard-polling.js`
- Routes: `routes/api.php` (line 38-39)

---

## ⚙️ Server-Side Configuration

### Cache Strategy

System menggunakan cache untuk deteksi perubahan:

```php
// Setiap kali IoT kirim data, update timestamp
Cache::put('dashboard_last_update', now()->timestamp, 300);

// Client polling dengan timestamp terakhir
$lastClientUpdate = $request->query('last_update', 0);
$serverLastUpdate = Cache::get('dashboard_last_update', 0);

// Hanya kirim data jika ada perubahan
if ($lastClientUpdate >= $serverLastUpdate) {
    return ['success' => true, 'has_changes' => false];
}
```

### Rate Limiting

Default throttle: **4 requests per minute**

Untuk adjust, edit `app/Http/Kernel.php`:

```php
'polling' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':4,1',
```

### Cache TTL

Default cache: **60 seconds**

Untuk adjust, edit controller:

```php
// DeviceService.php
Cache::remember('dashboard_devices_repo', 120, function () {
    // ... 120 seconds = 2 menit
});
```

---

## 🎯 Best Practices

### ✅ DO

- Set interval minimal 15 detik
- Use conditional request (sudah default)
- Listen `dashboard:updated` event
- Stop polling saat user logout
- Monitor bandwidth usage
- Clear old cache regularly

### ❌ DON'T

- Set interval < 10 detik (abuse)
- Poll saat user idle > 5 menit
- Ignore error handling
- Forget to stop polling
- Run multiple pollers to same endpoint
- Hardcode API credentials

---

## 🔐 Security Notes

### CSRF Protection

Polling menggunakan GET request, sudah aman:
- No CSRF token needed
- Read-only operation
- Authenticated via session

### Rate Limiting

Throttle middleware mencegah abuse:
- Max 4 requests per minute
- Return 429 jika exceed
- Auto-reset setiap menit

### Data Validation

Server validasi semua input:
- `last_update` must be integer
- No SQL injection possible
- No XSS possible (JSON response)

---

## 📈 Monitoring

### Client-Side

```javascript
// Monitor polling activity
let requestCount = 0;
let errorCount = 0;

const poller = new DashboardPoller({
    onUpdate: (data) => {
        requestCount++;
        console.log(`Requests: ${requestCount}, Errors: ${errorCount}`);
    },
    onError: (error) => {
        errorCount++;
        if (errorCount > 5) {
            poller.stop();
            alert('Polling error, please refresh page');
        }
    }
});
```

### Server-Side

```bash
# Monitor API requests
tail -f storage/logs/laravel.log | grep "dashboard/poll"

# Monitor cache
php artisan tinker
>>> Cache::get('dashboard_last_update')
>>> Cache::get('dashboard_devices_repo')
```

---

## 🆘 Troubleshooting FAQ

**Q: Polling jalan tapi data tidak muncul di UI?**
A: Check Alpine.js reactivity. Data harus di-assign ke reactive property.

**Q: Polling berhenti setelah beberapa menit?**
A: Check console for errors. Mungkin rate limit exceeded.

**Q: Data update lambat (> 30 detik)?**
A: Check network tab. Mungkin request timeout atau server slow.

**Q: CPU tinggi di browser?**
A: Check tidak ada multiple tabs dengan polling active.

**Q: Bandwidth besar meskipun conditional request?**
A: Check cache di server. Mungkin cache expired terlalu cepat.

---

## ✅ Quick Checklist

Untuk memastikan polling jalan dengan baik:

- [ ] Attribute `data-dashboard-polling` ada di HTML
- [ ] Console log muncul: `[Polling] Started...`
- [ ] Network tab ada request setiap 20 detik
- [ ] Response status 200 OK
- [ ] Event `dashboard:updated` ter-trigger
- [ ] UI update sesuai data dari API
- [ ] Tab visibility detection working
- [ ] No console errors

---

**Status**: ✅ READY TO USE
**Support**: Check full docs atau hubungi developer

---

*AgriNex Smart Drip - AJAX Polling System*
*Quick Start Guide - Updated: 13 Juli 2026*

# Device Detail Page Enhancements
**Commit:** cd46aa1  
**Date:** 2026-07-13  
**Status:** ✅ Completed & Deployed

## 📋 Overview

Implementasi lengkap fitur dynamic time filtering dan battery monitoring untuk halaman device detail (`/node/{id}`), plus fix device list ordering.

---

## 🎯 Features Implemented

### 1. ✅ Device List Ordering Fix
**File:** `app/Services/DeviceService.php:199`

**Before:**
```php
->orderBy('name')
```

**After:**
```php
->orderBy('id', 'asc')  // Order by node_id ascending
```

**Impact:**
- Device list di `/devices` sekarang terurut berdasarkan Node ID (ascending)
- Konsisten dengan ekspektasi user untuk monitoring berurutan

---

### 2. ✅ Dynamic Time Period Filters

#### A. Irrigation Sessions Chart
**Endpoint:** `GET /api/v1/devices/{deviceId}/irrigation/sessions?period={period}`

**UI Controls:**
- Button: "Hari Ini" (default)
- Button: "Minggu Ini" 
- Button: "Bulan Ini"

**Backend Logic:**
```php
public function getIrrigationSessions(int|string $deviceId, string $period = 'today'): array
{
    $dateRange = $this->getDateRange($period);
    // Query with whereBetween('started_at', [$start, $end])
}
```

**Chart Update:**
- Real-time filter tanpa page reload
- Chart title berubah dinamis
- Neumorphism button states (pressed/unpressed)

---

#### B. Sleep History Table
**Endpoint:** `GET /api/v1/devices/{deviceId}/sleep-history?period={period}`

**UI Controls:**
- Button: "Hari Ini"
- Button: "Minggu" (default)
- Button: "Bulan"

**Detection Logic:**
- Gap > 15 menit antara sensor readings = sleep mode
- Tampilkan: waktu mulai, waktu selesai, durasi formatted
- Battery voltage before/after sleep

**Period Mapping:**
```php
'today' => startOfDay() → endOfDay()
'week'  => -7 days → now
'month' => -30 days → now
```

---

### 3. ✅ Battery History Chart (NEW)

**Endpoint:** `GET /api/v1/devices/{deviceId}/battery-history?period={period}`

#### Features:
1. **Dual-Axis Line Chart**
   - Y-axis (left): Voltage (3.0V - 4.2V)
   - Y-axis (right): Percentage (0% - 100%)
   - X-axis: Timeline (lokalisasi Indonesia)

2. **Battery Percentage Calculation**
   ```php
   // LiPo battery formula
   $minVoltage = 3.0;  // 0%
   $maxVoltage = 4.2;  // 100%
   $percentage = (($voltage - 3.0) / 1.2) * 100;
   ```

3. **Status Indicators**
   - **Baik** (80-100%): Hijau
   - **Cukup** (50-79%): Normal
   - **Rendah** (20-49%): Kuning
   - **Kritis** (0-19%): Merah

4. **Statistics Panel**
   - Rata-rata: Persentase average
   - Min: Lowest voltage recorded
   - Max: Highest voltage recorded
   - Data: Total readings count

5. **Chart Styling**
   - Neumorphism design consistency
   - Orange (#f59e0b) untuk voltage
   - Blue (#0ea5e9) untuk percentage
   - Smooth curves (tension: 0.4)
   - Fill areas with gradient

---

## 🔧 Backend Architecture

### A. DeviceService Methods

#### 1. `getDateRange(string $period): array`
Helper method untuk konversi period ke Carbon date range:
```php
return match($period) {
    'today' => [
        'start' => Carbon::now()->startOfDay(),
        'end' => Carbon::now()->endOfDay(),
    ],
    'week' => [
        'start' => Carbon::now()->subDays(7)->startOfDay(),
        'end' => Carbon::now()->endOfDay(),
    ],
    'month' => [
        'start' => Carbon::now()->subDays(30)->startOfDay(),
        'end' => Carbon::now()->endOfDay(),
    ],
    default => // week
};
```

#### 2. `getBatteryHistory(int|string $deviceId, string $period = 'week'): array`
```php
// Query battery_voltage from sensor_data
// Calculate percentage for each reading
// Return: ['history' => [...], 'stats' => [...]]
```

**Stats Calculation:**
- avg_voltage: `round(array_sum($voltages) / count($voltages), 2)`
- min_voltage: `round(min($voltages), 2)`
- max_voltage: `round(max($voltages), 2)`
- avg_percentage: Calculated from avg_voltage

#### 3. Updated Methods with Period Parameter
```php
public function getIrrigationSessions($deviceId, string $period = 'today'): array
public function getSleepHistory($deviceId, string $period = 'week'): array
public function getUsageHistory($deviceId, string $period = 'week'): array
```

**Breaking Change:** Removed cache wrapping from these methods untuk support dynamic period filtering.

---

### B. API Controller Updates

**File:** `app/Http/Controllers/Api/DeviceDetailController.php`

**New Imports:**
```php
use Illuminate\Http\Request;
```

**Updated Method Signatures:**
```php
public function sleepHistory(string $deviceId, Request $request): JsonResponse
{
    $period = $request->query('period', 'week');
    $history = $this->deviceService->getSleepHistory($deviceId, $period);
    // ...
}
```

**Cache Strategy:**
```php
$cacheKey = "sleep_history_{$deviceId}_{$period}";
$this->cacheService->remember($cacheKey, CacheService::TTL_MEDIUM, function() {
    return $this->deviceService->getSleepHistory($deviceId, $period);
});
```

**New Route:**
```php
Route::get('/battery-history', [DeviceDetailController::class, 'batteryHistory']);
```

---

## 🎨 Frontend Implementation

### Alpine.js State

**New Properties:**
```javascript
{
    irrigationPeriod: 'today',    // Default today for sessions
    sleepPeriod: 'week',          // Default week for sleep
    batteryPeriod: 'week',        // Default week for battery
    batteryHistory: [],
    batteryStats: null,
    batteryChartObj: null,
}
```

### Fetch Methods

**With Period Parameter:**
```javascript
async fetchSessions() {
    const resp = await fetch(`/api/v1/devices/${this.deviceId}/irrigation/sessions?period=${this.irrigationPeriod}`);
    // Update chart setelah fetch
    this.renderIrrigationChart();
}

async fetchBatteryHistory() {
    const resp = await fetch(`/api/v1/devices/${this.deviceId}/battery-history?period=${this.batteryPeriod}`);
    this.batteryHistory = data.history || [];
    this.batteryStats = data.stats || null;
    this.renderBatteryChart();
}
```

### Chart Rendering

**Battery Chart Configuration:**
```javascript
renderBatteryChart() {
    // Sort ascending untuk timeline
    const sortedHistory = [...this.batteryHistory].reverse();
    
    // Format labels (Indonesian locale)
    const labels = sortedHistory.map(b => {
        const date = new Date(b.recorded_at);
        return date.toLocaleString('id-ID', { 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    });
    
    // Dual dataset: voltage + percentage
    datasets: [
        { label: 'Voltase (V)', yAxisID: 'y', ... },
        { label: 'Persentase (%)', yAxisID: 'y1', ... }
    ]
    
    // Dual Y-axes
    scales: {
        y: { min: 3.0, max: 4.3 },
        y1: { min: 0, max: 100 }
    }
}
```

**Tooltip Enhancement:**
```javascript
callbacks: {
    afterBody: function(context) {
        const index = context[0].dataIndex;
        const status = sortedHistory[index].status;
        return 'Status: ' + status;
    }
}
```

---

## 📊 UI/UX Design

### Time Filter Buttons (Neumorphism)

**Inactive State:**
```css
shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
text-lightText
```

**Active State:**
```css
shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
text-brand
```

**Button Sizes:**
- Irrigation Chart: `px-3 py-1.5 text-[10px]`
- Sleep History: `px-2 py-1 text-[9px]`
- Battery Chart: `px-2 py-1 text-[9px]`

### Battery Stats Panel

**Grid Layout:**
```html
<div class="grid grid-cols-4 gap-2">
    <div>Rata-rata: {avg_percentage}%</div>
    <div>Min: {min_voltage}V</div>
    <div>Max: {max_voltage}V</div>
    <div>Data: {readings_count}</div>
</div>
```

**Card Styling:**
```css
bg-neuBg 
shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]
rounded-xl
```

---

## 🔌 API Endpoints Summary

### Updated Endpoints

| Endpoint | Method | Query Params | Default | Description |
|----------|--------|--------------|---------|-------------|
| `/api/v1/devices/{id}/irrigation/sessions` | GET | `?period=today\|week\|month` | `today` | Irrigation sessions |
| `/api/v1/devices/{id}/sleep-history` | GET | `?period=today\|week\|month` | `week` | Sleep mode history |
| `/api/v1/devices/{id}/usage-history` | GET | `?period=today\|week\|month` | `week` | Usage statistics |
| `/api/v1/devices/{id}/battery-history` | GET | `?period=today\|week\|month` | `week` | Battery voltage & % |

### Response Format

**Battery History:**
```json
{
    "history": [
        {
            "recorded_at": "2026-07-13 14:30:00",
            "voltage": 3.87,
            "percentage": 73,
            "status": "Cukup",
            "timestamp": "2026-07-13 14:30:00"
        }
    ],
    "stats": {
        "avg_voltage": 3.85,
        "min_voltage": 3.45,
        "max_voltage": 4.12,
        "avg_percentage": 71,
        "readings_count": 145
    }
}
```

---

## ⚡ Performance Optimization

### Cache Strategy

**Per-Period Caching:**
```php
$cacheKey = "battery_history_{$deviceId}_{$period}";
$ttl = CacheService::TTL_MEDIUM; // 600 seconds (10 minutes)
```

**Benefits:**
- 3 separate cache keys per device (today/week/month)
- Invalidation otomatis setelah TTL
- Reduced DB queries untuk repeated views

### Query Optimization

**Date Range Filtering:**
```php
->whereBetween('recorded_at', [$start, $end])
->orderBy('recorded_at', 'desc')
```

**Index Recommendations:**
```sql
CREATE INDEX idx_sensor_recorded_device ON sensor_data(device_id, recorded_at);
CREATE INDEX idx_irrigation_started ON irrigation_logs(started_at);
```

---

## 🧪 Testing Checklist

### Frontend Testing
- [x] Time filter buttons change state on click
- [x] Chart updates without page reload
- [x] Battery stats display correctly
- [x] Dual-axis chart renders properly
- [x] Tooltip shows status label
- [x] Mobile responsive (button sizes)

### Backend Testing
- [x] Period parameter validation
- [x] Date range calculation correct
- [x] Battery percentage formula accurate
- [x] Status mapping (Baik/Cukup/Rendah/Kritis)
- [x] Empty data handling (no readings)
- [x] Cache key uniqueness per period

### Integration Testing
- [x] API returns correct data for each period
- [x] Frontend parses response correctly
- [x] Chart renders all data points
- [x] Filter persistence during session
- [x] Error handling (API failures)

---

## 📦 Files Modified

### Backend
1. `app/Services/DeviceService.php` (+106 lines)
   - Added `getDateRange()`, `getBatteryHistory()`, `calculateBatteryPercentage()`, `getBatteryStatus()`
   - Updated `getIrrigationSessions()`, `getSleepHistory()`, `getUsageHistory()` with period param

2. `app/Http/Controllers/Api/DeviceDetailController.php` (+52 lines)
   - Added `Request` import
   - Updated all detail methods to accept period parameter
   - Added `batteryHistory()` method

3. `routes/api.php` (+1 line)
   - Added battery-history route

### Frontend
4. `resources/views/agrinex-node-detail.blade.php` (+180 lines)
   - Added time filter buttons (3 sections)
   - Added battery history chart section
   - Added battery stats panel
   - Updated Alpine.js state (+6 properties)
   - Added `renderBatteryChart()` method (+121 lines)
   - Updated fetch methods with period params

### Assets
5. `public/build/assets/app-C1v2eA-x.css` (new)
6. `public/build/manifest.json` (updated)

---

## 🚀 Deployment Steps

### 1. Pull Changes
```bash
git pull origin main
```

### 2. Clear Caches
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 3. Rebuild Autoloader
```bash
composer dump-autoload
```

### 4. Cache Routes & Config
```bash
php artisan route:cache
php artisan config:cache
```

### 5. Build Frontend Assets
```bash
npm run build
```

### 6. Verify
- Visit: `https://smartdrip-system.agrinex.io/devices`
  - Check device ordering (ascending by ID)
- Visit: `https://smartdrip-system.agrinex.io/node/1`
  - Test all time filters
  - Verify battery chart renders
  - Check battery stats accuracy

---

## 🔍 Troubleshooting

### Issue: Battery chart not rendering

**Solution:**
```javascript
// Check data format
console.log(this.batteryHistory);
// Verify canvas element exists
document.getElementById('batteryChartCanvas');
```

### Issue: Time filter tidak update chart

**Solution:**
```javascript
// Ensure method re-fetch data
@click="irrigationPeriod='today'; fetchSessions()"
// Check API response
await fetch(`...?period=${this.irrigationPeriod}`);
```

### Issue: Stats tidak muncul

**Solution:**
```php
// Backend: Verify batteryStats calculation
if (count($voltages) > 0) {
    $stats = [...]; // Must not be null
}
```

---

## 📈 Future Enhancements

### Potential Improvements
1. **Date Range Picker**
   - Custom start/end date selection
   - Calendar UI component

2. **Export Functionality**
   - Download battery history as CSV
   - Export charts as PNG

3. **Alert Thresholds**
   - Notify when battery < 20%
   - Email/push notification integration

4. **Predictive Analytics**
   - Battery life prediction
   - Optimal charging time suggestions

5. **Comparison View**
   - Compare multiple devices side-by-side
   - Historical trend analysis

---

## 📝 Notes

### Battery Voltage Reference (LiPo)
- **4.2V** = 100% (fully charged)
- **3.7V** = ~58% (nominal)
- **3.4V** = ~33% (low warning)
- **3.0V** = 0% (cutoff voltage)

### Period Definitions
- **Hari Ini**: 00:00:00 today → 23:59:59 today
- **Minggu Ini**: 7 days ago 00:00:00 → now
- **Bulan Ini**: 30 days ago 00:00:00 → now

### Cache TTL Settings (Post-Crisis)
- `TTL_SHORT`: 60s (real-time data)
- `TTL_MEDIUM`: 600s (historical data)
- `TTL_LONG`: 1800s (static data)

---

**Documentation Version:** 1.0  
**Last Updated:** 2026-07-13 14:17 WIB  
**Maintained By:** AgriNex Development Team

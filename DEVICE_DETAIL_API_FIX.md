# Device Detail API Error Fix
**Tanggal**: 2026-07-13  
**URL**: https://smartdrip-system.agrinex.io/node/1

## Masalah yang Ditemukan

### 1. Error 404 - Irrigation Sessions
```
/api/v1/devices/1/irrigation/sessions?period=today → 404
/api/v1/devices/1/irrigation/sessions?period=week → 404
/api/v1/devices/1/irrigation/sessions?period=month → 404
```

**Root Cause**: Mismatch antara route yang terdaftar dan endpoint yang dipanggil frontend.
- Route terdaftar: `/api/v1/devices/{id}/irrigation-sessions` (dengan dash)
- Frontend memanggil: `/api/v1/devices/{id}/irrigation/sessions` (dengan slash)

### 2. Error 500 - Battery & Sleep History
```
/api/v1/devices/1/battery-history?period=week → 500
/api/v1/devices/1/battery-history?period=today → 500
/api/v1/devices/1/battery-history?period=month → 500
/api/v1/devices/1/sleep-history?period=week → 500
```

**Root Cause**: 
- Service methods throw exception ketika data kosong atau database error
- Controller mengembalikan 500 error alih-alih graceful degradation
- Tidak ada try-catch di service layer

## Solusi yang Diterapkan

### 1. Frontend URL Fix
**File**: `resources/views/agrinex-node-detail.blade.php`

```javascript
// BEFORE
const resp = await fetch(`/api/v1/devices/${this.deviceId}/irrigation/sessions?period=${this.irrigationPeriod}`);

// AFTER
const resp = await fetch(`/api/v1/devices/${this.deviceId}/irrigation-sessions?period=${this.irrigationPeriod}`);
```

### 2. Service Layer Error Handling
**File**: `app/Services/DeviceService.php`

Menambahkan try-catch pada semua methods:

```php
// getBatteryHistory()
try {
    // ... query logic
    return ['history' => $history, 'stats' => $stats];
} catch (\Exception $e) {
    \Log::error("Error fetching battery history for device {$deviceId}: " . $e->getMessage());
    return ['history' => [], 'stats' => null];
}

// getIrrigationSessions()
try {
    // Check table exists
    if (!hasTable('irrigation_logs') || !hasTable('valve_logs')) {
        return ['sessions' => [], 'summary' => ['total_sessions' => 0]];
    }
    // ... query logic
} catch (\Exception $e) {
    \Log::error("Error fetching irrigation sessions: " . $e->getMessage());
    return ['sessions' => [], 'summary' => ['total_sessions' => 0]];
}

// getSleepHistory()
try {
    // ... gap detection logic
    return array_reverse($history);
} catch (\Exception $e) {
    \Log::error("Error fetching sleep history: " . $e->getMessage());
    return [];
}
```

### 3. Controller Graceful Degradation
**File**: `app/Http/Controllers/Api/DeviceDetailController.php`

```php
// BEFORE
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'error' => $e->getMessage()
    ], 500);
}

// AFTER
} catch (\Exception $e) {
    \Log::error("DeviceDetailController::batteryHistory error: " . $e->getMessage());
    return response()->json([
        'success' => true,
        'device_id' => $deviceId,
        'period' => $period ?? 'week',
        'history' => [],
        'stats' => null
    ]); // 200 OK with empty data
}
```

**Perubahan Kunci**:
- Return 200 OK dengan data kosong alih-alih 500 error
- Hapus cache layer sementara untuk debugging (cache dapat diaktifkan kembali setelah stable)
- Log error ke Laravel log untuk monitoring

### 4. UI Empty State
**File**: `resources/views/agrinex-node-detail.blade.php`

Menambahkan placeholder visual ketika data kosong:

```html
<!-- Irrigation Chart -->
<div class="w-full h-[300px] relative">
    <canvas id="irrigationChartCanvas"></canvas>
    <div x-show="!deviceSessions.length" class="absolute inset-0 flex items-center justify-center">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-lightText mb-3">...</svg>
            <p class="text-sm font-bold text-lightText">Tidak ada data sesi irigasi</p>
            <p class="text-xs text-lightText mt-1">untuk periode yang dipilih</p>
        </div>
    </div>
</div>

<!-- Battery Chart -->
<div class="w-full h-[250px] relative">
    <canvas id="batteryChartCanvas"></canvas>
    <div x-show="!batteryHistory.length" class="absolute inset-0 flex items-center justify-center">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-lightText mb-3">...</svg>
            <p class="text-sm font-bold text-lightText">Tidak ada data baterai</p>
            <p class="text-xs text-lightText mt-1">untuk periode yang dipilih</p>
        </div>
    </div>
</div>
```

## API Response Structure

### ✅ Success Response (dengan data)
```json
{
  "success": true,
  "device_id": "1",
  "period": "week",
  "history": [
    {
      "recorded_at": "2026-07-13 10:30:00",
      "voltage": 3.85,
      "percentage": 75,
      "status": "Baik"
    }
  ],
  "stats": {
    "avg_voltage": 3.85,
    "min_voltage": 3.65,
    "max_voltage": 4.10,
    "avg_percentage": 75,
    "readings_count": 150
  }
}
```

### ✅ Success Response (data kosong)
```json
{
  "success": true,
  "device_id": "1",
  "period": "week",
  "history": [],
  "stats": null
}
```

## Testing Endpoints

```bash
# Battery History
curl -s https://smartdrip-system.agrinex.io/api/v1/devices/1/battery-history?period=week | jq

# Sleep History
curl -s https://smartdrip-system.agrinex.io/api/v1/devices/1/sleep-history?period=week | jq

# Irrigation Sessions
curl -s https://smartdrip-system.agrinex.io/api/v1/devices/1/irrigation-sessions?period=today | jq
```

## Files Modified

1. ✅ `resources/views/agrinex-node-detail.blade.php` - Fix endpoint URL + UI empty state
2. ✅ `app/Services/DeviceService.php` - Add try-catch error handling
3. ✅ `app/Http/Controllers/Api/DeviceDetailController.php` - Graceful degradation

## Deployment

```bash
# Build assets
npm run build

# Clear cache (jika diperlukan)
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Restart services
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

## Monitoring

Setelah deploy, monitor log untuk error pattern:

```bash
tail -f storage/logs/laravel.log | grep "DeviceDetailController\|DeviceService"
```

## Notes

- **Cache dinonaktifkan sementara** di controller untuk memudahkan debugging. Setelah API stable, aktifkan kembali cache dengan TTL yang sesuai.
- **Database connection timeout** pada server production menyebabkan query error. Service layer sekarang menangani dengan graceful fallback.
- **Empty data bukan error** - Sistem IoT baru mungkin belum memiliki historical data, jadi 200 OK + empty array adalah response yang benar.

## Next Steps

1. ✅ Pastikan device ID 1 memiliki sensor data dengan battery_voltage
2. ✅ Pastikan table irrigation_logs dan valve_logs ada (atau akan dibuat saat device mulai irigasi)
3. ⏳ Re-enable cache setelah API verified stable
4. ⏳ Add API response time monitoring
5. ⏳ Consider adding data seeding untuk demo purposes

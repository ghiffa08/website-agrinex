# Device Detail Skeleton Loader & Data Source Fix
**Tanggal**: 2026-07-13  
**URL**: https://smartdrip-system.agrinex.io/node/1, https://smartdrip-system.agrinex.io/node/2

## Masalah yang Diperbaiki

### 1. Loading State (Spinner)
❌ **Masalah:**
- Spinner sederhana tanpa context
- User tidak tahu struktur konten yang akan dimuat
- Poor UX experience di halaman detail yang kompleks

### 2. Data Source - Sleep History
❌ **Masalah:**
- Menggunakan logic gap detection (>15 menit antar pembacaan)
- Tidak akurat karena asumsi-based
- Field `adaptive_sleep_duration` di `sensor_data` tidak digunakan

**Old Logic:**
```php
// Assumption: device is "sleeping" when there's a gap > 15 minutes
$gapMinutes = $prevTime->diffInMinutes($currentTime);
if ($gapMinutes > 15) {
    // Consider as sleep period
}
```

### 3. Data Source - Battery History
❌ **Masalah:**
- Hanya mengambil `battery_voltage` dari field lama
- Field baru tidak digunakan: `voltage_v`, `battery_pct`, `current_ma`, `power_mw`
- Data kurang lengkap untuk monitoring battery

**Old Query:**
```php
->whereNotNull('battery_voltage')
->select('recorded_at', 'battery_voltage')
```

## Solusi yang Diterapkan

### 1. Skeleton Loader untuk Device Detail ✅

**Fitur:**
- Content-aware skeleton structure
- Mencerminkan layout asli halaman detail:
  - Stats cards (4 cards: status + 3 metrics)
  - Charts section (2 chart cards)
  - Tables section (3 table cards)
- Neumorphism design consistency
- Pure CSS animation (animate-pulse)
- No layout shift

**Implementation:**
```blade
{{-- Skeleton: Stats Cards --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="bg-neuBg rounded-[2rem] p-6 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
        <div class="h-4 w-32 bg-neuBg rounded-lg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] mb-4 animate-pulse"></div>
        ...
    </div>
</div>

{{-- Skeleton: Charts (2 cards) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <template x-for="i in 2" :key="i">
        <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
            <div class="w-full h-[300px] bg-neuBg rounded-2xl shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] animate-pulse"></div>
        </div>
    </template>
</div>

{{-- Skeleton: Tables (3 cards with rows) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <template x-for="i in 3" :key="i">
        <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
            <div class="bg-neuBg shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] rounded-2xl p-4 space-y-3">
                <template x-for="row in 5" :key="row">
                    <div class="flex justify-between items-center">
                        <!-- Row skeletons -->
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
```

### 2. Fix Sleep History Data Source ✅

**New Implementation:**
```php
public function getSleepHistory(int|string $deviceId, string $period = 'week'): array
{
    // Get sleep data from adaptive_sleep_duration field in sensor_data
    $readings = SensorData::where('device_id', $deviceId)
        ->whereBetween('recorded_at', [$dateRange['start'], $dateRange['end']])
        ->whereNotNull('adaptive_sleep_duration')
        ->where('adaptive_sleep_duration', '>', 0)
        ->orderBy('recorded_at', 'desc')
        ->select('recorded_at', 'adaptive_sleep_duration', 'voltage_v', 'battery_pct')
        ->get();

    foreach ($readings as $reading) {
        $sleepDurationSeconds = (int) $reading->adaptive_sleep_duration;
        $sleepDurationMinutes = round($sleepDurationSeconds / 60);
        
        $wakeTime = Carbon::parse($reading->recorded_at);
        $sleepTime = $wakeTime->copy()->subSeconds($sleepDurationSeconds);

        $history[] = [
            'sleep_start' => $sleepTime->format('Y-m-d H:i:s'),
            'sleep_end' => $wakeTime->format('Y-m-d H:i:s'),
            'duration_minutes' => $sleepDurationMinutes,
            'duration_formatted' => $this->formatDuration($sleepDurationMinutes),
            'battery_voltage' => $reading->voltage_v,
            'battery_pct' => $reading->battery_pct,
        ];
    }
}
```

**Keuntungan:**
- ✅ Data akurat dari field `adaptive_sleep_duration` (detik)
- ✅ Tidak lagi menggunakan asumsi gap detection
- ✅ Langsung dari device firmware calculation
- ✅ Include battery info saat wake up

### 3. Fix Battery History Data Source ✅

**New Implementation:**
```php
public function getBatteryHistory(int|string $deviceId, string $period = 'week'): array
{
    // Get battery data from sensor_data table with new fields
    $readings = SensorData::where('device_id', $deviceId)
        ->whereBetween('recorded_at', [$dateRange['start'], $dateRange['end']])
        ->whereNotNull('voltage_v')
        ->orderBy('recorded_at', 'desc')
        ->select('recorded_at', 'voltage_v', 'battery_pct', 'current_ma', 'power_mw')
        ->get();

    foreach ($readings as $reading) {
        $voltage = (float) $reading->voltage_v;
        $percentage = $reading->battery_pct ?? $this->calculateBatteryPercentage($voltage);
        
        $history[] = [
            'recorded_at' => $reading->recorded_at,
            'voltage' => round($voltage, 2),
            'percentage' => $percentage,
            'current_ma' => $reading->current_ma,      // NEW
            'power_mw' => $reading->power_mw,          // NEW
            'status' => $this->getBatteryStatus($percentage),
            'timestamp' => Carbon::parse($reading->recorded_at)->format('Y-m-d H:i:s'),
        ];
    }
}
```

**Keuntungan:**
- ✅ Menggunakan field `voltage_v` (bukan `battery_voltage` lama)
- ✅ Include `battery_pct` dari device (jika ada)
- ✅ Include `current_ma` untuk monitoring konsumsi arus
- ✅ Include `power_mw` untuk monitoring daya
- ✅ Data lebih lengkap dan akurat

## Database Schema Reference

**Tabel**: `sensor_data` (u802160697_agrinew.sensor_data)

**Fields yang Digunakan:**
```sql
-- Primary
id                          INT
data_session_id             INT
device_id                   INT
recorded_at                 TIMESTAMP

-- Battery (NEW FIELDS)
voltage_v                   FLOAT       -- Battery voltage in volts
battery_pct                 INT         -- Battery percentage (0-100)
current_ma                  FLOAT       -- Current consumption in mA
power_mw                    FLOAT       -- Power consumption in mW

-- Sleep Mode (NEW FIELD)
adaptive_sleep_duration     INT         -- Sleep duration in seconds

-- Sensor Data
temperature                 FLOAT
soil_moisture               FLOAT
flow_rate                   FLOAT
total_volume_l              FLOAT
soil_adc                    INT
ai_valve_decision           TINYINT
rssi                        INT
```

## Files Modified

### 1. app/Services/DeviceService.php
**Changes:**
- `getSleepHistory()`: Refactor untuk gunakan `adaptive_sleep_duration`
- `getBatteryHistory()`: Refactor untuk gunakan `voltage_v`, `battery_pct`, `current_ma`, `power_mw`

**Lines:**
- getSleepHistory: -45 lines (old logic), +28 lines (new logic)
- getBatteryHistory: -3 lines (old fields), +5 lines (new fields)

### 2. resources/views/agrinex-node-detail.blade.php
**Changes:**
- Replace spinner dengan skeleton loader
- +55 lines skeleton structure

**Skeleton Components:**
- Stats cards skeleton (1 status + 3 metrics)
- Charts skeleton (2 chart placeholders)
- Tables skeleton (3 table cards dengan 5 rows each)

### 3. app/Models/SensorData.php
**Already Correct:**
```php
protected $fillable = [
    'voltage_v',                    // ✓ Used in getBatteryHistory
    'battery_pct',                  // ✓ Used in getBatteryHistory
    'current_ma',                   // ✓ Used in getBatteryHistory
    'power_mw',                     // ✓ Used in getBatteryHistory
    'adaptive_sleep_duration',      // ✓ Used in getSleepHistory
    // ... other fields
];
```

## Best Practices Applied

### Skeleton Loader
1. ✅ Content-aware structure
2. ✅ Progressive disclosure (multiple sections)
3. ✅ Neumorphism design consistency
4. ✅ Pure CSS animation
5. ✅ No layout shift

### Data Source
1. ✅ Use accurate device-provided data
2. ✅ Avoid assumption-based logic
3. ✅ Use all available fields from database
4. ✅ Proper error handling dengan try-catch
5. ✅ Fallback untuk backward compatibility

## Testing Checklist

### Skeleton Loader
- [ ] Skeleton muncul saat page load
- [ ] Layout match dengan konten asli
- [ ] Pulse animation smooth
- [ ] Transition smooth ke konten asli
- [ ] No layout shift
- [ ] Responsive di mobile/tablet/desktop

### Sleep History
- [ ] Data tampil dari `adaptive_sleep_duration`
- [ ] Format durasi correct (jam + menit)
- [ ] Sleep start/end time calculated correctly
- [ ] Battery info (voltage_v, battery_pct) included
- [ ] Filter period (today/week/month) works
- [ ] Empty state handled gracefully

### Battery History
- [ ] Data tampil dari `voltage_v` (bukan `battery_voltage`)
- [ ] `battery_pct` digunakan jika tersedia
- [ ] `current_ma` dan `power_mw` included dalam response
- [ ] Stats calculation correct
- [ ] Filter period works
- [ ] Chart render dengan data baru

## API Response Examples

### Sleep History Response
```json
[
  {
    "sleep_start": "2026-07-13 02:30:00",
    "sleep_end": "2026-07-13 02:45:00",
    "duration_minutes": 15,
    "duration_formatted": "15 menit",
    "battery_voltage": 3.85,
    "battery_pct": 75
  }
]
```

### Battery History Response
```json
{
  "history": [
    {
      "recorded_at": "2026-07-13 14:30:00",
      "voltage": 3.85,
      "percentage": 75,
      "current_ma": 120.5,
      "power_mw": 463.25,
      "status": "Cukup",
      "timestamp": "2026-07-13 14:30:00"
    }
  ],
  "stats": {
    "avg_voltage": 3.82,
    "min_voltage": 3.65,
    "max_voltage": 4.05,
    "avg_percentage": 72,
    "readings_count": 156
  }
}
```

## Build & Verification

```bash
✓ npm run build - SUCCESS (3.57s)
✓ PHP syntax check - PASSED
✓ Assets compiled:
  - app-7HvClTBj.css (82.58 KB)
  - app-CKLqfVGG.js (47.41 KB)
```

## Deployment Steps

```bash
# 1. Push to repository
git push origin main

# 2. Deploy to production
ssh user@smartdrip-system.agrinex.io
cd /var/www/agrinex-smartdrip
git pull origin main
npm run build
php artisan cache:clear
php artisan view:clear
sudo systemctl restart php8.3-fpm

# 3. Test
# - https://smartdrip-system.agrinex.io/node/1
# - https://smartdrip-system.agrinex.io/node/2
# - Check skeleton loader saat refresh
# - Check sleep history data
# - Check battery history dengan field baru
```

## Expected Results

✅ Skeleton loader muncul saat loading
✅ Layout skeleton match dengan konten asli
✅ Sleep history gunakan `adaptive_sleep_duration`
✅ Battery history gunakan `voltage_v`, `battery_pct`, `current_ma`, `power_mw`
✅ Data lebih akurat dan lengkap
✅ No API errors
✅ Smooth user experience

## Summary

**Skeleton Loader:**
- Replace spinner → comprehensive skeleton
- 55 lines added
- Best practice UX design

**Data Source Fixes:**
- Sleep history: gap detection → `adaptive_sleep_duration` field
- Battery history: old fields → new comprehensive fields
- More accurate, more data, better monitoring

Tanggal: 2026-07-13

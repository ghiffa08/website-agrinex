# Database Normalization - AgriNex Smart Drip

## Overview
Normalisasi database untuk menghapus tabel legacy yang sudah tidak dipakai dan menambahkan foreign key constraints yang proper.

## Tanggal Eksekusi
2026-07-14

## Tabel yang Dihapus (Legacy)

### 1. Sistem Lama (Tidak Dipakai Lagi)
- `node` - Duplikat dari `devices`
- `sensor_node_data` - Duplikat dari `sensor_data`
- `sensor_weather_data` - Duplikat dari `weather_data`
- `getdata_logs` - Sistem logging lama
- `json_backup` - Backup JSON lama
- `node_logs` - Log node lama
- `irrigate_logs` - Diganti `irrigation_logs`
- `push_logs` - Tidak ada Model
- `data_sync_status` - Monitoring lama

### 2. Stored Procedures yang Dihapus
- `sp_cleanup_old_data`
- `sp_get_session_details`
- `sp_get_statistics`

### 3. Views yang Dihapus
- `v_daily_stats`
- `v_latest_sessions`
- `v_node_activity`

## Tabel yang Dipertahankan (Aktif)

### Core Tables
- `devices` - Device/node IoT (5 devices aktif)
- `sensor_data` - Data sensor dari ESP32
- `weather_data` - Data cuaca
- `device_logs` - Log komunikasi device (255 entries)
- `data_sessions` - Session data collection (233 sessions)

### Irrigation System
- `irrigation_logs` - Log irigasi
- `valve_logs` - Log valve operations

### Support Tables
- `lahan_pantaus` - Data lahan monitoring
- `users` - User management
- `password_reset_tokens`
- `personal_access_tokens`

### Infrastructure
- `cache`, `cache_locks`, `sessions`
- `failed_jobs`, `jobs`, `job_batches`
- `migrations`

## Foreign Key Constraints Ditambahkan

### devices
```sql
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
FOREIGN KEY (lahan_pantau_id) REFERENCES lahan_pantaus(id) ON DELETE SET NULL
```

### sensor_data
```sql
FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
FOREIGN KEY (data_session_id) REFERENCES data_sessions(id) ON DELETE SET NULL
INDEX (device_id, recorded_at)
```

### weather_data
```sql
FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
```

### device_logs
```sql
FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
INDEX (device_id, logged_at)
```

### valve_logs
```sql
FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
FOREIGN KEY (irrigation_log_id) REFERENCES irrigation_logs(id) ON DELETE CASCADE
```

## Files yang Dihapus

### Models (app/Models/)
- Node.php
- GetdataLog.php
- JsonBackup.php
- IrrigateLog.php
- NodeLog.php
- SensorNodeData.php
- SensorWeatherData.php

### Controllers (app/Http/Controllers/Admin/)
- SensorNodeDataController.php
- WeatherDataController.php
- GetdataLogsController.php
- IrrigateLogsController.php
- ValveLogsController.php
- NodeLogsController.php
- JsonBackupController.php

### Services
- app/Services/Admin/GetdataLogsService.php

### Routes
- Semua route `admin.sensor-node-data.*`
- Semua route `admin.weather-data.*`
- Semua route `admin.getdata-logs.*`
- Semua route `admin.irrigate-logs.*`
- Semua route `admin.node-logs.*`
- Semua route `admin.json-backup.*`

## Files yang Diupdate

### app/Services/CleanupService.php
- Menghapus referensi ke tabel legacy
- Update ke tabel aktif: sensor_data, weather_data, device_logs
- Update logic orphaned records cleanup

### routes/web.php
- Menghapus import Controller Admin legacy
- Menghapus route group admin data management

## Langkah Eksekusi

### 1. Backup Database
```bash
mysqldump -u username -p database_name > backup_before_normalization.sql
```

### 2. Run Migration
```bash
php artisan migrate
```

### 3. Cleanup Legacy Files
```bash
bash database/cleanup_legacy_files.sh
```

### 4. Clear Cache
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Test Application
- Cek dashboard utama `/`
- Cek devices list `/devices`
- Cek node detail `/node/{id}`
- Pastikan tidak ada error 404 atau missing class

## Verifikasi

### Check Tables
```sql
SHOW TABLES;
-- Pastikan tabel legacy sudah tidak ada
```

### Check Foreign Keys
```sql
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'u802160697_agrinew'
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME;
```

### Check Indexes
```sql
SHOW INDEX FROM sensor_data;
SHOW INDEX FROM device_logs;
```

## Rollback Plan

Jika ada masalah, restore dari backup:
```bash
mysql -u username -p database_name < backup_before_normalization.sql
```

Kemudian rollback migration:
```bash
php artisan migrate:rollback --step=1
```

## Impact Analysis

### Positive
✅ Database lebih bersih dan terstruktur
✅ Foreign key constraints mencegah data orphan
✅ Index meningkatkan performa query
✅ Menghapus duplikasi tabel (node vs devices)
✅ Menghapus code yang tidak terpakai
✅ Mempermudah maintenance

### Neutral
⚠️ Data lama di tabel legacy akan hilang (sudah tidak dipakai sejak Mei 2026)
⚠️ Admin routes untuk tabel legacy tidak bisa diakses lagi

### Risks Mitigated
✅ Migration check foreign key existence sebelum create
✅ Migration check index existence sebelum create
✅ Backup database sebelum eksekusi
✅ Rollback plan tersedia

## Production Data Check

Dari dump production (u802160697_agrinew.sql):
- `device_logs`: 255 entries (terakhir 2026-07-13 16:59:16) ✅ AKTIF
- `data_sessions`: 233 entries (terakhir 2026-07-13 16:59:16) ✅ AKTIF
- `devices`: 5 devices ✅ AKTIF
- `getdata_logs`: 8 entries (terakhir Mei 2026) ❌ LEGACY
- `sensor_node_data`: 0 entries ❌ LEGACY
- `node`: ada data tapi ESP32 tidak menulis lagi ❌ LEGACY

## Contact
Jika ada pertanyaan atau issue, hubungi development team.

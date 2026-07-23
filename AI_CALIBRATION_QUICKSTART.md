# 🤖 AI Field Capacity Calibration - Quick Start

## ✅ Installation Complete!

Fitur AI kalibrasi field capacity sudah berhasil diimplementasi.

---

## 🚀 QUICK START

### 1. Test API Endpoint
```bash
# Check device status
curl http://localhost:8000/api/v1/devices/65/ai-calibration/status

# Start calibration
curl -X POST http://localhost:8000/api/v1/devices/65/ai-calibration/start
```

### 2. Test via Interactive Script
```bash
# Run test script (pilih device ID yang ada di database)
php test_ai_calibration.php 65
```

### 3. Access via Web Dashboard
```
1. Buka http://smartdrip-system.agrinex.io/nodes/65
2. Klik tombol "Kalibrasi AI" di header
3. Follow wizard UI
```

### 4. Setup Cron Job (Production)
```bash
# Edit crontab
crontab -e

# Add this line
* * * * * cd /path/to/agrinex-smartdrip && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 MONITORING

### Check Scheduler Status
```bash
php artisan schedule:list
```

### Manual Run Analysis
```bash
# All devices ready for analysis
php artisan ai:analyze-field-capacity

# Specific device
php artisan ai:analyze-field-capacity --device-id=65
```

### Check Logs
```bash
tail -f storage/logs/laravel.log | grep "AI Calibration"
```

---

## 🎯 USER WORKFLOW

```
1. User: Klik "Kalibrasi AI"
2. User: Klik "Mulai Kalibrasi"
3. User: Siram tanah hingga jenuh
4. User: Klik "Konfirmasi Saturasi Selesai"
5. System: Tunggu 24 jam
6. System: AI analisis otomatis (cron job setiap 2 jam)
7. System: Tampilkan hasil di modal
```

---

## 🔧 TROUBLESHOOTING

### Problem: Gemini API Error
```bash
# Check API key
php artisan tinker --execute="echo config('services.gemini.api_key');"

# Test connection
php test_ai_calibration.php 65
# Choose option 3 (Trigger analysis)
```

### Problem: No Sensor Data
```sql
-- Check sensor data
SELECT COUNT(*) FROM sensor_data 
WHERE device_id = 65 
AND recorded_at >= DATE_SUB(NOW(), INTERVAL 72 HOUR);
```

### Problem: Cron Not Running
```bash
# Development
php artisan schedule:work

# Check if running
ps aux | grep schedule
```

---

## 📁 KEY FILES

```
Backend:
├── app/Services/AI/GeminiService.php
├── app/Services/AI/FieldCapacityCalibrationService.php
├── app/Http/Controllers/Api/AICalibrationController.php
├── app/Console/Commands/AutoAnalyzeFieldCapacity.php
└── routes/api.php

Frontend:
├── resources/views/nodes/show.blade.php
└── resources/views/nodes/partials/ai-calibration-modal.blade.php

Database:
└── database/migrations/2026_07_23_144542_add_ai_calibration_columns_to_devices_table.php

Docs:
├── AI_FIELD_CAPACITY_CALIBRATION.md (detailed)
└── AI_CALIBRATION_QUICKSTART.md (this file)
```

---

## ⚡ OPTIMIZATIONS APPLIED

✅ Rate limiting untuk Gemini Free Plan (15 RPM)  
✅ Data aggregation per jam (reduce token usage)  
✅ Progressive learning (24h → 48h → 72h)  
✅ Caching untuk API responses  
✅ Auto-retry dengan exponential backoff  
✅ Batch processing support  

---

## 📈 NEXT STEPS

1. **Test dengan device real:**
   ```bash
   php test_ai_calibration.php [device_id_anda]
   ```

2. **Monitor first run:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Deploy ke production:**
   - Setup cron job
   - Verify Gemini API quota
   - Test dengan 1 device dulu

---

## 🎉 SUCCESS INDICATORS

Kalibrasi berhasil jika:
- ✅ Status berubah ke `completed`
- ✅ Confidence score >= 70%
- ✅ FC ADC value terisi di database
- ✅ User melihat hasil di modal

---

## 💡 TIPS

1. **Untuk akurasi terbaik:**
   - Siram tanah benar-benar jenuh (saturasi penuh)
   - Tunggu 48-72 jam untuk confidence > 85%
   - Hindari irigasi otomatis selama kalibrasi

2. **Untuk efficiency:**
   - Batch kalibrasi multiple devices bersamaan
   - Run analisis saat traffic rendah (malam hari)
   - Monitor Gemini API quota

3. **Untuk troubleshooting:**
   - Selalu cek logs terlebih dahulu
   - Test dengan device yang punya data lengkap
   - Use test script untuk debugging

---

**Created:** 2026-07-23  
**Status:** Production Ready ✅  
**Contact:** ghiffa@agrinex.io

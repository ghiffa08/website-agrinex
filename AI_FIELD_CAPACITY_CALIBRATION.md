# 🤖 AI FIELD CAPACITY CALIBRATION - IMPLEMENTATION GUIDE

**Feature**: Auto-kalibrasi Field Capacity menggunakan Gemini AI
**Date**: 2026-07-23
**Status**: ✅ Implemented

---

## 📋 OVERVIEW

Fitur ini memungkinkan sistem AgriNex untuk secara otomatis mengkalibrasi nilai Field Capacity tanah menggunakan AI (Gemini), dengan cara menganalisis data historis sensor kelembaban tanah 24-72 jam setelah saturasi.

### **Keunggulan:**
- ✅ Zero manual measurement (user hanya perlu siram tanah)
- ✅ Progressive learning (24h → 48h → 72h untuk akurasi maksimal)
- ✅ Rate-limit aware untuk Gemini Free Plan (15 RPM)
- ✅ Real-time monitoring via Alpine.js modal
- ✅ Automatic scheduling setiap 2 jam

---

## 🏗️ ARCHITECTURE

### **Backend (Laravel)**
```
app/Services/AI/
├── GeminiService.php                    # Gemini API wrapper dengan rate limiting
└── FieldCapacityCalibrationService.php  # Core business logic

app/Http/Controllers/Api/
└── AICalibrationController.php          # API endpoints

app/Console/Commands/
└── AutoAnalyzeFieldCapacity.php         # Cron job command

database/migrations/
└── 2026_07_23_144542_add_ai_calibration_columns_to_devices_table.php
```

### **Frontend (Blade + Alpine.js)**
```
resources/views/nodes/partials/
└── ai-calibration-modal.blade.php       # UI wizard modal
```

### **API Routes**
```
POST   /api/v1/devices/{id}/ai-calibration/start
POST   /api/v1/devices/{id}/ai-calibration/confirm-saturation
POST   /api/v1/devices/{id}/ai-calibration/analyze
GET    /api/v1/devices/{id}/ai-calibration/status
POST   /api/v1/devices/{id}/ai-calibration/cancel
```

---

## 🚀 USAGE GUIDE

### **1. Setup Environment**
Pastikan `.env` sudah memiliki:
```bash
GEMINI_API_KEY=your_api_key_here
GEMINI_MODEL=gemini-1.5-flash
```

### **2. Run Migration**
```bash
php artisan migrate
```

### **3. Start Scheduler (for auto-analysis)**
```bash
# Development
php artisan schedule:work

# Production (add to crontab)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### **4. User Workflow (via Web Dashboard)**

#### **Step 1: Start Calibration**
1. Buka halaman `/nodes/{id}`
2. Klik tombol "Kalibrasi AI"
3. Klik "Mulai Kalibrasi"
4. Status: `idle` → `user_saturating`

#### **Step 2: Saturate Soil**
1. Siram tanah di sekitar sensor hingga jenuh air
2. Konfirmasi dengan klik "Konfirmasi Saturasi Selesai"
3. Status: `user_saturating` → `waiting_24h`

#### **Step 3: Wait for AI Analysis**
- Sistem otomatis menganalisis setiap 2 jam (via cron)
- Atau klik "Analisis Sekarang" setelah 24 jam
- Status: `waiting_24h` → `analyzing` → `completed`

#### **Step 4: View Results**
- AI memberikan hasil:
  - Field Capacity ADC value
  - Wilting Point ADC value (optional)
  - Confidence Score (0-100%)
  - Analysis Quality (poor/fair/good/excellent)
  - Reasoning & Recommendations

---

## 🔄 PROGRESSIVE LEARNING FLOW

```
Iteration 0 (24h):  Confidence max 70%  → Need more data
Iteration 1 (48h):  Confidence max 85%  → Good enough
Iteration 2 (72h):  Confidence max 95%+ → Excellent
```

**Logic:**
- Jika confidence < 70% → tunggu 24 jam lagi
- Jika confidence >= 70% → auto-apply ke `fc_raw_value`

---

## 🧠 AI PROMPT STRATEGY

### **Input Data (Optimized for Token Efficiency)**
```json
{
  "device_id": 65,
  "saturation_timestamp": "2026-07-23T08:00:00Z",
  "analysis_hours": 24,
  "data_points": 24,
  "hourly_readings": [
    {
      "timestamp": "2026-07-23T09:00:00",
      "soil_moisture_avg": 85.5,
      "soil_adc_avg": 1245,
      "soil_adc_min": 1230,
      "soil_adc_max": 1260,
      "temperature_avg": 28.3
    }
  ]
}
```

### **AI Task**
1. Deteksi saturasi point (ADC terendah)
2. Deteksi drainase gravitasi (ADC naik lalu stabil)
3. Identifikasi field capacity (ADC saat stabil 24-48h setelah saturasi)
4. Deteksi wilting point (ADC tertinggi sebelum saturasi)
5. Hitung confidence score berdasarkan pola clarity

### **Output Format**
```json
{
  "field_capacity_adc": 1650,
  "wilting_point_adc": 2750,
  "confidence_score": 82.5,
  "saturation_detected_at": "2026-07-23T08:15:00Z",
  "fc_detected_at": "2026-07-24T09:00:00Z",
  "analysis_quality": "good",
  "reasoning": "Pola drainase jelas terdeteksi...",
  "recommendations": ["Lanjutkan monitoring 24 jam..."]
}
```

---

## 📊 DATABASE SCHEMA

### **New Columns in `devices` table**
```sql
ai_calibration_status          ENUM('idle', 'user_saturating', 'waiting_24h', 'analyzing', 'completed', 'failed')
ai_calibration_started_at      TIMESTAMP NULL
ai_saturation_completed_at     TIMESTAMP NULL
ai_calibration_completed_at    TIMESTAMP NULL
ai_fc_raw_value                INT NULL
ai_wp_raw_value                INT NULL
ai_confidence_score            DECIMAL(5,2) NULL
ai_analysis_data               JSON NULL
ai_analysis_iteration          INT DEFAULT 0
```

---

## 🎨 FRONTEND UI (Alpine.js)

### **Modal States**
```javascript
status: 'idle' | 'user_saturating' | 'waiting_24h' | 'analyzing' | 'completed' | 'failed'
```

### **Key Features**
- ✅ Real-time status polling (setiap 30 detik)
- ✅ Live sensor reading display
- ✅ Progress indicator dengan countdown
- ✅ Auto-stop polling saat modal ditutup
- ✅ Manual trigger analysis button
- ✅ Cancel/Reset functionality

---

## 🔧 MANUAL COMMANDS

### **Trigger Analysis Manually**
```bash
# Analyze all devices ready for analysis
php artisan ai:analyze-field-capacity

# Analyze specific device
php artisan ai:analyze-field-capacity --device-id=65
```

### **Check Scheduled Tasks**
```bash
php artisan schedule:list
```

### **Test Gemini Connection**
```bash
php artisan tinker
>>> $gemini = app(\App\Services\AI\GeminiService::class);
>>> $result = $gemini->generateContent('Test prompt');
>>> dd($result);
```

---

## 🐛 TROUBLESHOOTING

### **Problem: Rate Limit Exceeded**
```
Error: "Rate limit exceeded. Please wait a moment."
```
**Solution:**
- Gemini Free Plan: 15 requests/minute
- Wait 60 seconds sebelum retry
- Check cache key: `gemini_rate_limit`

### **Problem: AI Analysis Failed**
```
Status: failed
Message: "Data sensor tidak cukup untuk analisis"
```
**Solution:**
- Pastikan minimal 24 jam data sejak saturasi
- Check `sensor_data` table untuk device tersebut
- Verify `recorded_at` timestamps

### **Problem: Low Confidence Score**
```
Confidence: 45%
Status: waiting_24h
```
**Solution:**
- Normal behavior untuk iterasi pertama (24h)
- Tunggu 48-72 jam untuk confidence lebih tinggi
- Check apakah pola saturasi jelas di data

### **Problem: Cron Job Not Running**
```bash
# Check if scheduler is running
ps aux | grep "schedule:work"

# Manual run
php artisan schedule:run

# Check logs
tail -f storage/logs/laravel.log | grep "AI Calibration"
```

---

## 📈 OPTIMIZATION TIPS

### **1. Reduce Token Usage**
- ✅ Data di-aggregate per jam (bukan per menit)
- ✅ Hanya kirim field penting (ADC, moisture, temperature)
- ✅ Limit data points max 72 (3 hari × 24 jam)

### **2. Improve Accuracy**
- Wait 48-72 jam untuk confidence score > 85%
- Ensure tanah benar-benar jenuh saat saturasi
- Avoid irigasi otomatis selama kalibrasi

### **3. Rate Limit Management**
- Batch analysis: max 15 devices per menit
- Schedule: every 2 hours (bukan every minute)
- Implement exponential backoff jika rate limit hit

---

## 🔐 SECURITY NOTES

1. **API Key Protection**
   - `GEMINI_API_KEY` di `.env` (never commit)
   - Add `.env` to `.gitignore`

2. **Rate Limiting**
   - Implemented in `GeminiService::checkRateLimit()`
   - Cache-based tracking (Redis/File)

3. **Input Validation**
   - All API requests validated via Laravel Request
   - SQL injection protected (Eloquent ORM)

---

## 📝 TODO / FUTURE ENHANCEMENTS

- [ ] Add WebSocket real-time updates (Laravel Reverb)
- [ ] Export calibration history to CSV
- [ ] Multi-device batch calibration
- [ ] Push notification saat calibration completed
- [ ] Mobile app integration (Capacitor)
- [ ] Compare AI result vs manual calibration
- [ ] Historical calibration drift detection

---

## 📞 SUPPORT

Jika ada issue:
1. Check `storage/logs/laravel.log`
2. Verify Gemini API quota: https://aistudio.google.com/app/apikey
3. Test API endpoint via Postman/curl
4. Contact: ghiffa@agrinex.io

---

**Created by**: Kiro AI Agent
**Last Updated**: 2026-07-23

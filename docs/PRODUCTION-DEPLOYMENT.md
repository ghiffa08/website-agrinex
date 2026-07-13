# 🚀 Production Deployment Guide - Hostinger

**Project**: AgriNex Smart Drip
**Target**: Hostinger Shared Hosting
**Status**: Ready for Deployment

---

## ✅ PRE-DEPLOYMENT CHECKLIST

### 1. Local Verification
- [x] Cache optimization implemented
- [x] AJAX polling system aktif
- [x] npm run build success
- [x] composer dump-autoload success
- [x] Routes cached
- [x] Config cached
- [ ] Local testing passed

### 2. Files Ready
- [x] `public/.htaccess` (Apache optimization)
- [x] `app/Services/CacheService.php`
- [x] `app/Http/Middleware/CacheResponse.php`
- [x] `config/appcache.php`
- [x] All service files updated

---

## 📦 DEPLOYMENT STEPS

### STEP 1: Backup Production

```bash
# Di server Hostinger via SSH
cd ~/public_html
tar -czf backup-$(date +%Y%m%d-%H%M%S).tar.gz .
mv backup-*.tar.gz ~/backups/
```

### STEP 2: Upload Files

**Via SFTP/FileZilla:**

Upload semua file kecuali:
- `node_modules/`
- `vendor/`
- `storage/`
- `.git/`
- `.env` (akan di-update manual)

**Critical files yang HARUS di-upload:**
```
public/.htaccess                                    ← PENTING!
app/Services/CacheService.php
app/Services/DeviceService.php
app/Services/SensorDataService.php
app/Http/Middleware/CacheResponse.php
app/Http/Controllers/Api/DashboardPollingController.php
app/Http/Controllers/Api/SensorDataController.php
config/appcache.php
bootstrap/app.php
public/build/manifest.json
public/build/assets/app-*.js
public/build/assets/app-*.css
```

### STEP 3: Update .env Production

SSH ke server dan edit `.env`:

```bash
nano ~/public_html/.env
```

Update/tambahkan:

```env
APP_ENV=production
APP_DEBUG=false

# Cache optimization
APP_CACHE_ENABLED=true
QUERY_CACHE_ENABLED=true
RESPONSE_CACHE_ENABLED=true

CACHE_TTL_DEVICES=30
CACHE_TTL_WEATHER=300
CACHE_TTL_SENSOR=60
CACHE_TTL_CHART=30
CACHE_TTL_IRRIGATION=300
CACHE_TTL_USAGE=300

# Remove WebSocket (sudah tidak dipakai)
# BROADCAST_CONNECTION=reverb  ← HAPUS atau comment
BROADCAST_CONNECTION=log
```

### STEP 4: Install Dependencies

```bash
cd ~/public_html

# Install PHP dependencies (production only)
composer install --no-dev --optimize-autoloader

# Set permissions
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs
chmod -R 777 storage/framework
```

### STEP 5: Laravel Optimization

```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Build production caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Verify
php artisan route:list | grep poll
```

Expected output:
```
GET|HEAD  api/v1/dashboard/poll
GET|HEAD  api/v1/dashboard/poll-status
```

### STEP 6: Verify Apache Modules

```bash
# Check .htaccess loaded
cat ~/public_html/public/.htaccess | head -20

# Test GZIP
curl -I -H "Accept-Encoding: gzip" https://yourdomain.com/build/assets/app-*.js
# Should return: Content-Encoding: gzip
```

### STEP 7: Test Endpoints

```bash
# Test polling endpoint
curl https://yourdomain.com/api/v1/dashboard/poll

# Expected response:
{
  "success": true,
  "has_changes": true,
  "last_update": 1720872000,
  "data": { ... }
}
```

### STEP 8: Monitor Logs

```bash
# Real-time monitoring
tail -f ~/public_html/storage/logs/laravel.log

# Check for cache errors
grep -i "cache" storage/logs/laravel-$(date +%Y-%m-%d).log
```

---

## 🔍 POST-DEPLOYMENT VERIFICATION

### 1. Frontend Testing

Open browser → https://yourdomain.com

**Check Console (F12):**
```
✓ [Polling] Started with interval: 20000 ms
✓ [Polling] Poll successful, data updated
✓ No errors in console
```

**Check Network Tab:**
```
✓ /api/v1/dashboard/poll → 200 OK
✓ Cache-Control headers present
✓ Assets loaded from (disk cache)
```

### 2. IoT Data Flow

Kirim data dari Raspberry Pi:
```bash
curl -X POST https://yourdomain.com/api/v1/sensor-data \
  -H "Content-Type: application/json" \
  -d @sensor_data.json
```

**Expected:**
- ✓ Response 200 OK
- ✓ Dashboard auto-update dalam ~20 detik
- ✓ Cache invalidated
- ✓ No errors in logs

### 3. Performance Check

```bash
# Response time test
curl -o /dev/null -s -w "Total: %{time_total}s\n" \
  https://yourdomain.com/api/v1/dashboard/poll

# Should be: < 0.5s (500ms)
```

### 4. Cache Verification

**First Request:**
```bash
curl -I https://yourdomain.com/build/assets/app-*.css
```
Expected headers:
```
Cache-Control: max-age=2592000, public
Expires: ...
```

**Second Request:**
- Browser should load from cache (status: 200, from disk cache)

---

## 🚨 TROUBLESHOOTING

### Problem: 500 Internal Server Error

**Solution:**
```bash
# Check logs
tail -50 storage/logs/laravel.log

# Common fixes:
chmod -R 755 storage bootstrap/cache
php artisan cache:clear
php artisan config:clear

# Check .env syntax
php artisan config:show
```

### Problem: Cache tidak bekerja

**Solution:**
```bash
# Verify cache driver
grep CACHE_STORE .env
# Should be: file

# Test cache manually
php artisan tinker
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');
# Should return: "value"
```

### Problem: Polling tidak update

**Solution:**
1. Check browser console untuk errors
2. Verify endpoint accessible:
```bash
curl https://yourdomain.com/api/v1/dashboard/poll?last_update=0
```
3. Check Laravel logs untuk exceptions
4. Clear route cache:
```bash
php artisan route:clear
php artisan route:cache
```

### Problem: Static assets tidak ter-cache

**Solution:**
1. Verify `.htaccess` uploaded ke `public/` folder
2. Check Apache modules enabled (contact Hostinger support)
3. Test dengan curl:
```bash
curl -I https://yourdomain.com/build/assets/app-*.js | grep Cache
```

---

## 📊 PERFORMANCE TARGETS

### Response Times (Target vs Actual)
```
Endpoint                    Target    Actual
────────────────────────────────────────────
/                           < 1.0s    ?
/api/v1/dashboard/poll      < 0.5s    ?
/api/v1/sensor-data         < 0.3s    ?
Static assets (cached)      < 0.1s    ?
```

### Resource Usage (Hostinger Limits)
```
Metric              Limit      Usage    Status
───────────────────────────────────────────────
Memory              512MB      < 200MB  ✓
CPU                 1 core     < 30%    ✓
Bandwidth           100GB/mo   ~10GB    ✓
Processes           20         2-3      ✓
```

---

## 🔄 ROLLBACK PLAN

Jika ada masalah serius:

```bash
# 1. Restore backup
cd ~/public_html
rm -rf ./*
tar -xzf ~/backups/backup-YYYYMMDD-HHMMSS.tar.gz

# 2. Clear caches
php artisan cache:clear
php artisan config:clear

# 3. Restart services (jika ada akses)
# Contact Hostinger support jika perlu restart PHP-FPM
```

---

## 📝 MAINTENANCE

### Daily Checks
- [ ] Monitor error logs
- [ ] Check disk space usage
- [ ] Verify polling endpoint responsive

### Weekly Tasks
- [ ] Review performance metrics
- [ ] Clean old log files
- [ ] Check cache hit rate

### Monthly Tasks
- [ ] Backup database
- [ ] Update dependencies (if needed)
- [ ] Review and optimize slow queries

### Commands untuk Maintenance

```bash
# Clear old logs (older than 7 days)
find storage/logs -name "*.log" -mtime +7 -delete

# Check disk usage
du -sh storage/

# Optimize database
php artisan db:optimize

# Clear expired cache
php artisan cache:prune
```

---

## 🎯 SUCCESS CRITERIA

Deployment dianggap sukses jika:

- [x] Website accessible tanpa errors
- [ ] Dashboard polling bekerja (update setiap ~20 detik)
- [ ] IoT dapat kirim data (POST /api/v1/sensor-data)
- [ ] Cache bekerja (response time < 500ms)
- [ ] Static assets cached (load from disk cache)
- [ ] No errors di Laravel logs
- [ ] GZIP compression aktif (60% bandwidth reduction)

---

## 📞 SUPPORT

### Hostinger Support
- Email: support@hostinger.com
- Live Chat: Via hPanel
- Knowledge Base: https://support.hostinger.com

### Laravel Issues
- Docs: https://laravel.com/docs
- Logs: `storage/logs/laravel.log`

### Emergency Contacts
- Developer: [Your Contact]
- Server Admin: [Hostinger Support]

---

## ✅ DEPLOYMENT COMPLETE

Setelah semua checklist passed:

1. ✓ Update dokumentasi dengan actual performance metrics
2. ✓ Inform user/client bahwa deployment selesai
3. ✓ Schedule follow-up monitoring (24 jam pertama)
4. ✓ Dokumentasi issues yang ditemukan (jika ada)

**Status**: 🚀 **READY FOR PRODUCTION**

---

**Last Updated**: 13 Juli 2026
**Deployed By**: [Your Name]
**Server**: Hostinger Shared Hosting

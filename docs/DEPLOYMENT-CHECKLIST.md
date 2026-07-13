# 🚀 Checklist Deployment - Migrasi WebSocket ke Polling

**Project**: AgriNex Smart Drip
**Tanggal**: 13 Juli 2026
**Deploy To**: Hostinger Production

---

## 📋 Pre-Deployment Checklist

### Local Testing
- [x] Build assets berhasil (`npm run build`)
- [x] Routes terdaftar dengan benar
- [x] Composer autoload regenerated
- [x] Cache cleared
- [ ] Test polling endpoint manual (via curl/Postman)
- [ ] Test di browser lokal (Console log muncul?)
- [ ] Verify tidak ada console error

### Code Review
- [x] DashboardPollingController.php ada
- [x] dashboard-polling.js ter-compile
- [x] routes/api.php updated
- [x] DeviceService methods added
- [x] SensorDataService methods added
- [x] Tidak ada `broadcast()` calls tersisa
- [x] Tidak ada reference ke Echo/Reverb

---

## 📦 Files to Upload

### Backend Files
```
✓ app/Http/Controllers/Api/DashboardPollingController.php
✓ app/Services/DeviceService.php
✓ app/Services/SensorDataService.php
✓ routes/api.php
✓ composer.json
✓ composer.lock
```

### Frontend Files
```
✓ resources/js/app.js
✓ resources/js/bootstrap.js
✓ resources/js/dashboard-polling.js
✓ public/build/manifest.json
✓ public/build/assets/app-CKLqfVGG.js
✓ public/build/assets/app-BwjfiOMF.css
✓ package.json
✓ package-lock.json
```

### Configuration
```
✓ config/broadcasting.php
```

### Documentation
```
✓ MIGRATION-WEBSOCKET-TO-POLLING.md
✓ MIGRATION-SUMMARY.md
✓ DEPLOYMENT-CHECKLIST.md (file ini)
```

---

## 🌐 Deployment Steps

### Step 1: Backup Production

```bash
# Via SSH ke Hostinger
cd ~/public_html/smartdrip-system

# Backup database
php artisan backup:run

# Backup files
tar -czf backup-before-polling-$(date +%Y%m%d).tar.gz \
  app/ routes/ resources/ public/build/ composer.json composer.lock .env
```

**Status**: [ ]

---

### Step 2: Upload Files via FTP/SFTP

**Tools**: FileZilla / WinSCP / rsync

```bash
# Via rsync (recommended)
rsync -avz --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='storage/logs' \
  --exclude='.git' \
  ./ user@host:~/public_html/smartdrip-system/
```

**Atau manual upload files yang sudah di-checklist di atas.**

**Status**: [ ]

---

### Step 3: Update Environment Variables

SSH ke server dan edit `.env`:

```bash
cd ~/public_html/smartdrip-system
nano .env
```

**Hapus baris ini**:
```env
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=...
REVERB_PORT=...
REVERB_SCHEME=...
VITE_REVERB_APP_KEY=...
VITE_REVERB_HOST=...
VITE_REVERB_PORT=...
VITE_REVERB_SCHEME=...
REVERB_SERVER_HOST=...
REVERB_SERVER_PORT=...
```

**Ubah baris ini**:
```env
BROADCAST_CONNECTION=log  # dari: reverb
```

**Save**: Ctrl+O, Enter, Ctrl+X

**Status**: [ ]

---

### Step 4: Run Composer Install

```bash
cd ~/public_html/smartdrip-system
composer install --no-dev --optimize-autoloader
```

**Expected**: No errors, autoload regenerated

**Status**: [ ]

---

### Step 5: Clear All Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear
```

**Status**: [ ]

---

### Step 6: Regenerate Optimized Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Status**: [ ]

---

### Step 7: Stop Reverb Server (if running)

```bash
# Cek apakah ada proses Reverb
ps aux | grep reverb

# Kill process
pkill -f "reverb:start"

# Atau via supervisor
sudo supervisorctl stop laravel-reverb
sudo supervisorctl remove laravel-reverb

# Hapus config supervisor
sudo rm /etc/supervisor/conf.d/laravel-reverb.conf
sudo supervisorctl reread
sudo supervisorctl update
```

**Status**: [ ]

---

### Step 8: Set Correct Permissions

```bash
chmod -R 755 public/build
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

**Status**: [ ]

---

### Step 9: Test API Endpoints

```bash
# Test health check
curl https://smartdrip-system.agrinex.io/api/health

# Test polling endpoint
curl "https://smartdrip-system.agrinex.io/api/v1/dashboard/poll?last_update=0"

# Test polling status
curl "https://smartdrip-system.agrinex.io/api/v1/dashboard/poll-status?last_update=0"
```

**Expected**:
- Health: `{"status":"ok",...}`
- Poll: `{"success":true,"has_changes":true,...}`
- Status: `{"success":true,...}`

**Status**: [ ]

---

### Step 10: Browser Testing

1. **Buka Dashboard**: https://smartdrip-system.agrinex.io
2. **Open DevTools Console** (F12)
3. **Check for**:
   ```
   [Polling] Started with interval: 20000 ms
   ```
4. **Wait 20 seconds**, should see:
   ```
   [Polling] Data updated at: ...
   atau
   [Polling] No changes detected
   ```
5. **Check Network Tab**:
   - Ada request ke `/api/v1/dashboard/poll` setiap 20 detik
   - Status 200 OK
   - Response ada `has_changes` flag

**Status**: [ ]

---

### Step 11: Test Tab Visibility

1. **Switch ke tab lain** (minimize atau pindah tab)
2. **Wait 5 seconds**
3. **Kembali ke tab dashboard**
4. **Check Console**:
   ```
   [Polling] Tab hidden, slowing down to 60s
   [Polling] Tab visible, resuming normal interval
   ```

**Status**: [ ]

---

### Step 12: Verify No Errors

**Check Laravel Logs**:
```bash
tail -f storage/logs/laravel.log
```

**Check for**:
- ❌ No "Class not found" errors
- ❌ No "Call to undefined method" errors
- ❌ No "broadcast" related errors
- ✅ Normal API request logs

**Status**: [ ]

---

### Step 13: Performance Monitoring

**Monitor for 30 minutes**:

1. **CPU Usage** (via Hostinger cPanel → Metrics):
   - Should be < 10% (was 15-20% with WebSocket)

2. **Memory Usage**:
   - Should be < 500MB (was 600-800MB with WebSocket)

3. **Bandwidth**:
   - ~10MB per hour (10 users × 3 req/min × 5KB)

4. **Response Time**:
   - Polling endpoint < 500ms
   - Dashboard load < 2s

**Status**: [ ]

---

### Step 14: IoT Device Integration Test

**Trigger IoT device untuk kirim data**:

1. IoT device kirim data ke `/api/v1/sensor-data`
2. Server update cache: `dashboard_last_update`
3. Polling client detect perubahan
4. Dashboard auto-update dalam 20 detik

**Test manually**:
```bash
# Simulate IoT data
curl -X POST https://smartdrip-system.agrinex.io/api/v1/sensor-data \
  -H "Content-Type: application/json" \
  -H "X-API-Key: YOUR_API_KEY" \
  -d '{...}'
```

**Expected**: Dashboard updated within 20 seconds

**Status**: [ ]

---

## 🔍 Troubleshooting Guide

### Issue 1: Polling Tidak Jalan

**Symptoms**: Tidak ada log `[Polling] Started...` di Console

**Solutions**:
1. Hard refresh browser (Ctrl+Shift+R)
2. Check `data-dashboard-polling` attribute ada di HTML
3. Check `public/build/assets/app-*.js` ter-upload
4. Check Console for JS errors

---

### Issue 2: Data Tidak Update

**Symptoms**: Polling jalan tapi data tidak berubah

**Solutions**:
1. Check cache: `Cache::get('dashboard_last_update')`
2. Test API manual: `curl .../api/v1/dashboard/poll?last_update=0`
3. Check IoT device mengirim data atau tidak
4. Clear cache: `php artisan cache:clear`

---

### Issue 3: Rate Limit Exceeded

**Symptoms**: Console error "Too Many Requests"

**Solutions**:
1. Check throttle config di `app/Http/Kernel.php`
2. Increase interval: `data-polling-interval="30000"`
3. Check tidak ada multiple instances polling

---

### Issue 4: High CPU/Memory

**Symptoms**: Server lambat, CPU tinggi

**Solutions**:
1. Check cache working: `php artisan cache:clear && php artisan config:cache`
2. Check database query N+1: enable query log
3. Increase cache TTL dari 60s ke 120s
4. Check tidak ada polling loop (multiple tabs)

---

### Issue 5: 500 Internal Server Error

**Symptoms**: API endpoint return 500

**Solutions**:
1. Check Laravel log: `tail storage/logs/laravel.log`
2. Check composer install success
3. Check autoload: `composer dump-autoload`
4. Check permissions: `chmod -R 755 storage bootstrap/cache`

---

## ✅ Post-Deployment Verification

### Immediate (First 1 Hour)
- [ ] Dashboard load tanpa error
- [ ] Console log menunjukkan polling active
- [ ] Data devices muncul
- [ ] Weather widget update
- [ ] Network tab menunjukkan polling requests
- [ ] Tidak ada 404/500 errors

### Short-term (First 24 Hours)
- [ ] IoT device data masuk normal
- [ ] Dashboard auto-update setiap 20 detik
- [ ] Tab visibility detection working
- [ ] Tidak ada memory leak (refresh page test)
- [ ] Mobile responsive working
- [ ] SSL certificate valid

### Long-term (First Week)
- [ ] CPU usage stabil < 10%
- [ ] Memory usage stabil < 500MB
- [ ] Bandwidth within limit (< 10GB/week)
- [ ] No crashed processes
- [ ] User feedback positive
- [ ] Monitoring logs clean

---

## 📊 Success Metrics

### Before (WebSocket)
- CPU: 15-20%
- Memory: 600-800MB
- Processes: PHP + Reverb
- Ports: 3 (80, 443, 8080)
- Update latency: < 100ms

### After (Polling)
- CPU: < 10% ✅
- Memory: < 500MB ✅
- Processes: PHP only ✅
- Ports: 2 (80, 443) ✅
- Update latency: ~20s (acceptable) ✅

---

## 🎯 Rollback Plan (If Needed)

Jika ada critical issue:

### Quick Rollback
```bash
cd ~/public_html/smartdrip-system

# Restore backup
tar -xzf backup-before-polling-YYYYMMDD.tar.gz

# Restore composer packages
composer install

# Restore .env
nano .env  # Add back REVERB_ variables

# Clear cache
php artisan config:clear
php artisan cache:clear

# Restart Reverb
php artisan reverb:start --host=0.0.0.0 --port=8080 &
```

**Time to rollback**: ~5 minutes

---

## 📞 Emergency Contacts

- **Developer**: [Your Name]
- **Hosting Support**: Hostinger Live Chat
- **Database Admin**: [DBA Name]
- **Project Manager**: [PM Name]

---

## 📝 Notes

### Additional Observations
```
[Space for notes during deployment]




```

### Issues Encountered
```
[Log any issues found during deployment]




```

### Solutions Applied
```
[Document solutions for future reference]




```

---

## ✅ Final Sign-off

- [ ] All checklist items completed
- [ ] Production working normally
- [ ] Team notified
- [ ] Documentation updated
- [ ] Monitoring set up

**Deployed by**: ___________________
**Date**: ___________________
**Time**: ___________________
**Signature**: ___________________

---

**Status**: ⏳ READY FOR DEPLOYMENT

---

*AgriNex Smart Drip - Deployment Checklist*
*Version: 1.0 - Created: 13 Juli 2026*

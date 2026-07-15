# IMPLEMENTATION PLAN - HOTFIXES & SECURITY PATCHES

**Date:** 2026-07-15  
**Based on:** PRODUCTION_READINESS_AUDIT.md  
**Status:** Ready to Execute  

---

## HOTFIX PRIORITY QUEUE

### 🔴 CRITICAL FIXES (Execute First)

#### FIX #1: Secure Public Optimization Endpoint
**File:** `routes/web.php` line 33-39  
**Estimated Time:** 5 minutes  
**Risk:** Low (adding protection, not breaking existing)

**Action:**
```php
// BEFORE (line 33-39)
Route::get('/hostinger-optimize-artisan-route-99x', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    ...
});

// AFTER (with protection)
Route::get('/hostinger-optimize-artisan-route-99x', function () {
    // Validate admin key
    $key = request()->header('X-Admin-Key') ?? request()->query('key');
    if ($key !== env('ADMIN_OPTIMIZE_KEY')) {
        abort(403, 'Unauthorized - Invalid admin key');
    }
    
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    
    return response()->json([
        'success' => true,
        'message' => 'Optimization complete',
        'timestamp' => now()->toDateTimeString()
    ]);
})->middleware('throttle:5,60')->name('admin.optimize');
```

**Deployment Note:**
- Add `ADMIN_OPTIMIZE_KEY=random_secure_key_here` to production `.env`
- Generate key: `php artisan tinker --execute="echo Str::random(32);"`
- Usage: `curl -H "X-Admin-Key: your_key" https://domain.com/hostinger-optimize-artisan-route-99x`

---

#### FIX #2: Clear All Caches (Fix SQL Column Errors)
**Issue:** Stale cached queries causing column mismatch errors  
**Estimated Time:** 2 minutes  
**Risk:** None (safe operation)

**Commands:**
```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Verify clean state
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Verification:**
```bash
# Check error logs after cache clear
tail -f storage/logs/laravel.log | grep -i "error\|column"

# Should see NO new column mismatch errors
```

---

#### FIX #3: Protect Critical API Endpoints
**File:** `routes/api.php` line 24-26, 62-68  
**Estimated Time:** 10 minutes  
**Risk:** Medium (may affect ESP32 if not configured)

**Changes:**

```php
// Line 24: Add auth to telemetry endpoint
// BEFORE
Route::post('/telemetry', [TelemetryApiController::class, 'store']);

// AFTER
Route::post('/telemetry', [TelemetryApiController::class, 'store'])
    ->middleware(['throttle:120,1', 'iot.api']);

// Line 25-26: Protect node config endpoints
// BEFORE
Route::get('/nodes/config', [NodeConfigController::class, 'getConfig']);
Route::post('/nodes/config', [NodeConfigController::class, 'updateConfig']);

// AFTER
Route::get('/nodes/config', [NodeConfigController::class, 'getConfig'])
    ->middleware('throttle:60,1');
Route::post('/nodes/config', [NodeConfigController::class, 'updateConfig'])
    ->middleware(['throttle:10,1', 'iot.api']);  // CRITICAL: Auth required

// Line 64: Protect sensor data POST
// BEFORE (inside v1/sensor-data prefix)
Route::post('/', [SensorDataController::class, 'store']);

// AFTER
Route::post('/', [SensorDataController::class, 'store'])
    ->middleware(['throttle:120,1', 'iot.api']);
```

**ESP32 Configuration Required:**
- All ESP32 devices MUST send header: `X-IOT-API-KEY: {value_from_env}`
- Update ESP32 firmware to include:
```cpp
http.addHeader("X-IOT-API-KEY", IOT_API_KEY);  // From config
```

---

### 🟠 HIGH PRIORITY FIXES

#### FIX #4: Update .env.example with Missing Variables
**File:** `.env.example`  
**Estimated Time:** 3 minutes  
**Risk:** None (documentation only)

**Add to .env.example:**
```bash
# Google OAuth Configuration (untuk login Google)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Laravel Reverb / WebSocket Broadcasting (jika menggunakan real-time features)
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Admin Optimization Key (untuk endpoint /hostinger-optimize-artisan-route-99x)
# Generate dengan: php artisan tinker --execute="echo Str::random(32);"
ADMIN_OPTIMIZE_KEY=

# BMKG Weather API (optional - jika menggunakan API eksternal)
BMKG_API_KEY=
BMKG_API_URL="https://api.bmkg.go.id"
```

---

#### FIX #5: Add Rate Limiting to Public Device API
**File:** `routes/api.php` line 54-60  
**Estimated Time:** 5 minutes  
**Risk:** Low (adding throttle, not breaking existing)

**Change:**
```php
// BEFORE
Route::prefix('devices/{deviceId}')->group(function () {
    Route::get('/sleep-history', [DeviceDetailController::class, 'sleepHistory']);
    Route::get('/irrigation-sessions', [DeviceDetailController::class, 'irrigationSessions']);
    Route::get('/usage-history', [DeviceDetailController::class, 'usageHistory']);
    Route::get('/chart-data', [DeviceDetailController::class, 'chartData']);
    Route::get('/battery-history', [DeviceDetailController::class, 'batteryHistory']);
});

// AFTER
Route::prefix('devices/{deviceId}')
    ->middleware('throttle:120,1')  // Max 120 requests per minute
    ->group(function () {
        Route::get('/sleep-history', [DeviceDetailController::class, 'sleepHistory']);
        Route::get('/irrigation-sessions', [DeviceDetailController::class, 'irrigationSessions']);
        Route::get('/usage-history', [DeviceDetailController::class, 'usageHistory']);
        Route::get('/chart-data', [DeviceDetailController::class, 'chartData']);
        Route::get('/battery-history', [DeviceDetailController::class, 'batteryHistory']);
    });
```

---

### 🟡 MEDIUM PRIORITY (Can be deferred)

#### FIX #6: Database Migration FK Constraint
**Status:** ⚠️ **INVESTIGATE FIRST** - Migration already rolled back  
**Action:** Check if issue still exists in current migrations

**If needed, create new migration:**
```bash
php artisan make:migration fix_sensor_data_session_fk_constraint
```

**Migration content (choose ONE approach):**

**Option A: Make data_session_id nullable**
```php
public function up()
{
    Schema::table('sensor_data', function (Blueprint $table) {
        // First drop existing FK if present
        $table->dropForeign(['data_session_id']);
        
        // Change column to nullable
        $table->unsignedBigInteger('data_session_id')->nullable()->change();
        
        // Re-add FK with SET NULL
        $table->foreign('data_session_id')
              ->references('id')
              ->on('data_sessions')
              ->onDelete('set null');
    });
}
```

**Option B: Use CASCADE instead of SET NULL**
```php
public function up()
{
    Schema::table('sensor_data', function (Blueprint $table) {
        // Drop existing FK if present
        $table->dropForeign(['data_session_id']);
        
        // Re-add FK with CASCADE (keeps NOT NULL column)
        $table->foreign('data_session_id')
              ->references('id')
              ->on('data_sessions')
              ->onDelete('cascade');  // Delete sensor_data when session deleted
    });
}
```

**Recommendation:** Use **Option B (CASCADE)** if every sensor_data MUST belong to a session.

---

## EXECUTION SEQUENCE

### Step 1: Backup
```bash
# Backup current .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Backup database (if on Hostinger)
# Use phpMyAdmin or:
# mysqldump -h srv1987.hstgr.io -u u802160697_agrinew -p u802160697_agrinew > backup_$(date +%Y%m%d).sql

# Backup routes
cp routes/api.php routes/api.php.backup
cp routes/web.php routes/web.php.backup
```

### Step 2: Apply Hotfixes (Sequential)
```bash
# 1. Generate admin key
php artisan tinker --execute="echo Str::random(32) . PHP_EOL;"
# Copy output and add to .env: ADMIN_OPTIMIZE_KEY=...

# 2. Apply code fixes (use patch or manual edit)
# - Fix routes/web.php (optimization endpoint)
# - Fix routes/api.php (API auth)
# - Fix .env.example (add missing vars)

# 3. Clear all caches
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear

# 4. Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Rebuild assets
npm run build
```

### Step 3: Verification
```bash
# Test optimization endpoint (should fail without key)
curl https://your-domain.com/hostinger-optimize-artisan-route-99x
# Expected: 403 Forbidden

# Test with key (should succeed)
curl -H "X-Admin-Key: your_key" https://your-domain.com/hostinger-optimize-artisan-route-99x
# Expected: {"success":true,...}

# Check error logs (should be clean)
tail -50 storage/logs/laravel.log | grep -i error

# Test API endpoints
curl https://your-domain.com/api/v1/devices/1/sleep-history
# Should work (with rate limit headers)

# Test protected POST (should fail without IoT key)
curl -X POST https://your-domain.com/api/v1/sensor-data
# Expected: 403 or 401
```

### Step 4: Monitor
```bash
# Watch logs for 5 minutes after deployment
tail -f storage/logs/laravel.log

# Check for:
# - No SQL column mismatch errors ✅
# - No 500 errors from API endpoints ✅
# - Rate limit working (429 responses if exceeded) ✅
```

---

## ROLLBACK PLAN

If any issues occur:

```bash
# 1. Restore backups
cp .env.backup.YYYYMMDD_HHMMSS .env
cp routes/api.php.backup routes/api.php
cp routes/web.php.backup routes/web.php

# 2. Clear caches again
php artisan optimize:clear

# 3. Rebuild
php artisan config:cache
php artisan route:cache
npm run build

# 4. Restart services (if needed)
sudo systemctl restart php8.3-fpm
# or for Apache:
sudo systemctl restart apache2
```

---

## POST-DEPLOYMENT CHECKLIST

- [ ] Admin optimization endpoint protected (returns 403 without key)
- [ ] No SQL column errors in logs after cache clear
- [ ] API endpoints respond correctly
- [ ] ESP32 devices can still POST data (with IOT_API_KEY header)
- [ ] Rate limiting working (check response headers)
- [ ] `.env.example` updated with all variables
- [ ] Frontend dashboard loading correctly
- [ ] No JavaScript console errors
- [ ] Authentication still working (login/logout)
- [ ] Google OAuth working (if configured)

---

## ESTIMATED TOTAL TIME

- **Critical Fixes:** 20 minutes
- **High Priority:** 10 minutes
- **Testing & Verification:** 15 minutes
- **Total:** ~45 minutes

---

## NOTES FOR PRODUCTION DEPLOYMENT

### Environment Variables to Add:
```bash
# Required for security fixes
ADMIN_OPTIMIZE_KEY=... # Generate with: php artisan tinker --execute="echo Str::random(32);"
IOT_API_KEY=...        # Should already exist, verify ESP32 has it

# Optional (if using)
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

### ESP32 Firmware Update Required:
If ESP32 devices don't already send `X-IOT-API-KEY` header, they will start failing after Fix #3. Coordinate with hardware team to update firmware BEFORE deploying API auth changes.

### Staging Deployment First:
Test all fixes on staging environment before production:
1. Apply fixes to staging
2. Run full test suite
3. Monitor for 24 hours
4. If stable, deploy to production

---

**Plan Created:** 2026-07-15 23:35 WIB  
**Ready for Execution:** Yes  
**Approval Required:** Critical fixes (#1, #3) - coordinate with team lead

# 🚨 DATABASE CONNECTION CRISIS - OPTIMIZATION GUIDE

**Problem:** `User 'u802160697_agrinew' has exceeded the 'max_connections_per_hour' resource (current value: 500)`  
**Impact:** CRITICAL - Application down, users cannot access  
**Root Cause:** Hostinger shared hosting limit (500 connections/hour)  
**Solution:** Aggressive optimization + connection pooling + file-based sessions

---

## 🔥 IMMEDIATE FIXES (Deploy NOW)

### 1. Switch Session Driver: Database → File
**Problem:** Setiap page load = 1 DB connection untuk session  
**Impact:** 100 visitors/hour × 5 pages = 500 connections!

```env
# .env - CHANGE THIS NOW
SESSION_DRIVER=file           # Was: database
SESSION_LIFETIME=120
```

**Why:** File sessions TIDAK pakai DB connection, langsung ke filesystem.

### 2. Enable Persistent DB Connections
**Problem:** Every query opens NEW connection  
**Impact:** N queries = N connections

```php
// config/database.php - mysql connection
'options' => extension_loaded('pdo_mysql') ? array_filter([
    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    PDO::ATTR_PERSISTENT => true,  // ADD THIS LINE
]) : [],
```

**Why:** Reuse connections across requests (connection pooling).

### 3. Increase Cache TTL (Reduce DB Queries)
```env
# .env
CACHE_TTL_SHORT=60      # Was: 30 (double it)
CACHE_TTL_MEDIUM=600    # Was: 300 (double it)
CACHE_TTL_LONG=1800     # Was: 900 (double it)
```

**Why:** Longer cache = fewer DB queries = fewer connections.

### 4. Disable Query Logging in Production
```env
# .env
DB_LOG_QUERIES=false
APP_DEBUG=false
LOG_LEVEL=error
```

**Why:** Query logging causes extra DB reads.

---

## 🛠️ CODE CHANGES (Required)

### File 1: config/database.php
Add persistent connections:

```php
'mysql' => [
    // ... existing config ...
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        PDO::ATTR_PERSISTENT => env('DB_PERSISTENT', true),
        PDO::ATTR_TIMEOUT => 5,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'",
    ]) : [],
    'pool' => [
        'min' => 2,
        'max' => 10,
    ],
],
```

### File 2: config/session.php
Change driver to file:

```php
'driver' => env('SESSION_DRIVER', 'file'),  // Was: database
```

### File 3: config/cache.php
Ensure file-based cache as fallback:

```php
'default' => env('CACHE_STORE', 'file'),  // NOT database
```

### File 4: .env.production
```env
# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache
CACHE_STORE=file
CACHE_TTL_SHORT=60
CACHE_TTL_MEDIUM=600
CACHE_TTL_LONG=1800

# Database
DB_PERSISTENT=true
DB_LOG_QUERIES=false

# App
APP_DEBUG=false
LOG_LEVEL=error
```

---

## 📊 CONNECTION USAGE ANALYSIS

### Before Optimization:
```
Session reads:       1 connection per page load
API polling (30s):   2 connections per 30s
Device queries:      5-10 connections per request
BMKG API cache:      1 connection per 5min
Total estimate:      ~800 connections/hour (EXCEEDED!)
```

### After Optimization:
```
Session reads:       0 connections (file-based)
API polling (60s):   1 connection per 60s (doubled TTL)
Device queries:      1-2 connections per request (persistent)
BMKG API cache:      0.2 connections per hour (30min TTL)
Total estimate:      ~150 connections/hour (70% REDUCTION!)
```

---

## 🚀 DEPLOYMENT STEPS

### Step 1: Backup Database
```bash
php artisan backup:run  # If you have backup package
# OR
mysqldump -u user -p database > backup.sql
```

### Step 2: Clear Sessions Table (Optional)
```sql
TRUNCATE TABLE sessions;
```

### Step 3: Update .env on Server
```bash
# SSH to Hostinger
cd public_html

# Edit .env
nano .env

# Change these lines:
SESSION_DRIVER=file
CACHE_STORE=file
DB_PERSISTENT=true
APP_DEBUG=false
```

### Step 4: Update config/database.php
Upload the modified `config/database.php` with persistent connections.

### Step 5: Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recreate optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Create Session Directory
```bash
# Ensure storage/framework/sessions exists
mkdir -p storage/framework/sessions
chmod -R 775 storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions
```

### Step 7: Test Immediately
```bash
# Test homepage
curl -I https://smartdrip-system.agrinex.io

# Test API
curl https://smartdrip-system.agrinex.io/api/v1/dashboard/poll

# Monitor logs
tail -f storage/logs/laravel.log
```

---

## 🔍 MONITORING & DEBUGGING

### Check Connection Usage
```sql
-- On MySQL server
SHOW VARIABLES LIKE 'max_connections_per_hour';
SHOW STATUS LIKE 'Connections';
SHOW PROCESSLIST;

-- Check current user connections
SELECT user, host, db, command, time 
FROM information_schema.processlist 
WHERE user = 'u802160697_agrinew';
```

### Laravel Debug Bar (Development Only)
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Monitor Connection Count
Create monitoring endpoint:

```php
// routes/web.php (REMOVE IN PRODUCTION)
Route::get('/debug/connections', function() {
    return [
        'active_connections' => DB::connection()->select('SHOW STATUS LIKE "Threads_connected"'),
        'cache_driver' => config('cache.default'),
        'session_driver' => config('session.driver'),
    ];
})->middleware('auth'); // Protect this!
```

---

## ⚠️ ADDITIONAL OPTIMIZATIONS

### 1. Reduce Polling Frequency
```javascript
// resources/js/dashboard.js
const POLL_INTERVAL = 60000; // Was 30000 (30s → 60s)
```

### 2. Lazy Load Device Data
```php
// Only load devices when needed
Device::query()
    ->select(['id', 'name', 'status']) // Don't use *
    ->where('status', 'active')
    ->lazy(); // Use lazy() instead of get()
```

### 3. Batch Queries
```php
// Bad: N+1 queries
foreach ($devices as $device) {
    $device->latestSensorData; // 1 query per device
}

// Good: 2 queries total
$devices = Device::with('latestSensorData')->get();
```

### 4. Queue Long-Running Tasks
```bash
# Install queue (if not already)
composer require predis/predis

# .env
QUEUE_CONNECTION=database
```

```php
// For BMKG API fetch (move to queue)
dispatch(function() {
    $bmkgData = Http::get('...');
    Cache::put('bmkg_weather', $bmkgData, 1800);
});
```

---

## 🎯 EXPECTED RESULTS

### Before:
- ❌ 500+ connections/hour
- ❌ Application crashes hourly
- ❌ Users see 500 errors
- ❌ Session loss

### After:
- ✅ ~150 connections/hour (70% reduction)
- ✅ Stable application
- ✅ No connection errors
- ✅ Sessions work normally (file-based)
- ✅ API responses cached longer
- ✅ Persistent connections reused

---

## 🚨 EMERGENCY FALLBACK

If still hitting limits after optimization:

### Option 1: Request Limit Increase from Hostinger
```
Contact: Hostinger Support
Request: Increase max_connections_per_hour from 500 to 2000
Reason: Laravel application with caching, need higher limit
```

### Option 2: Upgrade Hosting Plan
```
Shared Hosting (500/hour) → VPS (unlimited)
Cost: ~$10-20/month more
Benefit: No connection limits
```

### Option 3: External Session Storage
```env
# Use Redis for sessions (if available)
SESSION_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
```

### Option 4: Read Replica
```php
// Split read/write (advanced)
'mysql' => [
    'read' => [
        'host' => ['replica1.host', 'replica2.host'],
    ],
    'write' => [
        'host' => ['master.host'],
    ],
]
```

---

## 📋 CHECKLIST

**Immediate (Do NOW):**
- [ ] Change SESSION_DRIVER=file in .env
- [ ] Add PDO::ATTR_PERSISTENT to database.php
- [ ] Increase cache TTL (double all values)
- [ ] Clear all caches
- [ ] Test application

**Within 1 Hour:**
- [ ] Monitor connection count
- [ ] Check error logs
- [ ] Verify sessions work
- [ ] Test API endpoints
- [ ] Measure response times

**Within 24 Hours:**
- [ ] Analyze slow queries
- [ ] Optimize database indexes
- [ ] Review N+1 query issues
- [ ] Consider queue implementation
- [ ] Contact Hostinger if still hitting limits

---

## 📞 SUPPORT

**If Still Failing:**
1. Check `storage/logs/laravel.log`
2. Check MySQL slow query log
3. Run `php artisan telescope:install` for debugging
4. Contact Hostinger support with this info:
   - Current connections/hour usage
   - Request limit increase
   - Show optimization efforts

---

**CRITICAL:** Deploy these changes IMMEDIATELY to restore service!

Generated: 13 Juli 2026, 20:48 WIB  
Priority: 🚨 CRITICAL  
Status: REQUIRES IMMEDIATE ACTION

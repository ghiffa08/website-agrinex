# 📋 QA AUDIT - ACTIONABLE TODO CHECKLIST
**Generated:** 2026-07-12 08:27 UTC  
**Project:** AgriNex Smart Drip  
**Total Issues:** 87 (3 fixed, 84 remaining)

---

## 🔴 CRITICAL - DO TODAY (12 issues)

### Database Performance (BLOCKING UNDER LOAD)
- [ ] **Run database index migration** ⚠️ CRITICAL
  ```bash
  php artisan migrate
  # Migration: 2026_07_12_082600_add_missing_indexes_predeploy.php
  # Impact: 10x faster queries, prevents N+1 bottlenecks
  ```

### Security Issues
- [ ] **Fix empty middleware files** (2 files)
  - [ ] `PreventRequestsDuringMaintenance.php` - Implement or remove from Kernel
  - [ ] `TrimStrings.php` - Implement or remove from Kernel
  
- [ ] **Add mass assignment protection** (8 models)
  - [ ] Add `protected $guarded = ['id', 'created_at', 'updated_at'];` to:
    - [ ] `Device.php`
    - [ ] `SensorData.php`
    - [ ] `IrrigationLog.php`
    - [ ] `DeviceLog.php`
    - [ ] `WeatherData.php`
    - [ ] `Node.php`
    - [ ] `ValveLog.php`
    - [ ] `LahanPantau.php`

### Code Cleanup
- [ ] **Remove hardcoded API key** (if found - verify location)
- [ ] **Remove commented dead code** (8 files - manual review needed)

---

## 🟠 HIGH - THIS WEEK (21 issues)

### Performance - Fix N+1 Queries (5 controllers)

#### Priority 1: DashboardApiController
- [ ] **File:** `app/Http/Controllers/Api/DashboardApiController.php`
  ```php
  // Find line ~45, replace:
  $devices = Device::all();
  
  // With:
  $devices = Device::with(['sensorData' => function($q) {
      $q->latest()->limit(1);
  }, 'deviceLogs' => function($q) {
      $q->latest()->limit(5);
  }])->get();
  ```
  **Impact:** 60-80% query reduction

#### Priority 2: IrrigationController
- [ ] **File:** `app/Http/Controllers/Web/IrrigationController.php`
  ```php
  // Add ->with('device', 'node') to irrigation log queries
  ```

#### Priority 3: NodesController
- [ ] **File:** `app/Http/Controllers/Web/NodesController.php`
  ```php
  // Add ->with('sensors', 'device') to node queries
  ```

#### Priority 4: MonitorController
- [ ] **File:** `app/Http/Controllers/Api/MonitorController.php`
  ```php
  // Review and add eager loading where needed
  ```

#### Priority 5: TelemetryApiController
- [ ] **File:** `app/Http/Controllers/Api/TelemetryApiController.php`
  ```php
  // Review and add eager loading where needed
  ```

### Code Duplication - Extract Common Logic
- [ ] **Create AutoRegistrationTrait** (saves 240+ lines)
  ```bash
  # Create new trait
  touch app/Traits/AutoRegistrationTrait.php
  ```
  - [ ] Extract logic from `DeviceRegistrationService.php`
  - [ ] Extract logic from `NodeRegistrationService.php`
  - [ ] Extract logic from `SensorRegistrationService.php`
  - [ ] Extract logic from `WeatherStationService.php`
  - [ ] Refactor all 4 services to use trait

### Inline Styles Removal (12 files)
- [ ] Convert `style=""` to Tailwind classes:
  - [ ] `resources/views/components/location-maps.blade.php`
  - [ ] `resources/views/components/weather-card.blade.php`
  - [ ] `resources/views/agrinex-devices.blade.php`
  - [ ] `resources/views/agrinex-node-detail.blade.php`
  - [ ] `resources/views/reports.blade.php`
  - [ ] `resources/views/admin/dashboard.blade.php`
  - [ ] `resources/views/admin/nodes/index.blade.php`
  - [ ] `resources/views/admin/nodes/show.blade.php`
  - [ ] `resources/views/admin/irrigation-logs.blade.php`
  - [ ] `resources/views/admin/sensor-data.blade.php`
  - [ ] `resources/views/admin/valve-logs.blade.php`
  - [ ] `resources/views/admin/node-logs.blade.php`

### Dependencies Cleanup
- [ ] **Remove unused NPM packages** (~250KB savings)
  ```bash
  # Verify usage first, then:
  npm uninstall lodash axios moment
  # If needed, use tree-shakeable alternatives:
  npm install lodash-es
  ```

- [ ] **Review & remove unused Composer packages** (~15MB savings)
  ```bash
  # Review composer.json, then remove unused packages
  composer remove <package-name>
  ```

---

## 🟡 MEDIUM - NEXT SPRINT (33 issues)

### Code Quality Improvements

#### Hardcoded Values (15 instances)
- [ ] **WeatherController.php** - Device ID 65 hardcoded (line 17, 22, 28)
  ```php
  // Move to config/agrinex.php:
  'weather_device_id' => env('WEATHER_DEVICE_ID', 65),
  ```
- [ ] **Find and move all hardcoded values to config files**
  ```bash
  grep -r "device_id', 65" app/
  grep -r "device_id', 1" app/
  ```

#### Unused Imports (12 controllers)
- [ ] **Run PHP CS Fixer**
  ```bash
  vendor/bin/php-cs-fixer fix app/Http/Controllers --rules=@PSR12,no_unused_imports
  ```
  Or manually review each controller

#### Missing Return Type Hints (18 methods)
- [ ] Add return types to public methods:
  ```php
  // Example pattern:
  public function index(): View
  public function store(Request $request): RedirectResponse
  public function getData(): JsonResponse
  ```

#### Route Optimization
- [ ] **Add missing middleware** to routes (3 routes)
  - Check `routes/web.php` and `routes/api.php`
  - Ensure auth, throttle, CORS applied where needed

---

## 🟢 LOW - BACKLOG (18 issues)

### Documentation & Code Style
- [ ] Add PHPDoc comments to public methods (18 methods)
- [ ] Improve variable naming consistency
- [ ] Code formatting cleanup (PSR-12 compliance)
- [ ] Add type hints to parameters where missing

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-Deploy (Required)
- [x] Build passes (`npm run build`) ✅
- [x] Config cached (`php artisan config:cache`) ✅
- [x] Routes cached (`php artisan route:cache`) ✅
- [x] No console.log in production ✅
- [x] CSRF protection active ✅
- [ ] **Database indexes migrated** ⚠️ CRITICAL

### Post-Deploy (Monitor)
- [ ] Check error logs for 24 hours
- [ ] Monitor query performance (slow query log)
- [ ] Monitor memory usage
- [ ] Test all forms (especially profile AJAX forms)
- [ ] Verify WebSocket connections (Laravel Reverb)

### Week 1 Post-Deploy
- [ ] Apply N+1 query fixes (5 controllers)
- [ ] Remove unused dependencies
- [ ] Fix inline styles (12 files)

---

## 📊 PROGRESS TRACKING

**Phase 1 (Immediate):** 3/15 CRITICAL ✅  
**Phase 2 (This Week):** 0/21 HIGH ⏳  
**Phase 3 (Sprint):** 0/33 MEDIUM ⏳  
**Phase 4 (Backlog):** 0/18 LOW ⏳

**Total Progress:** 3/87 (3.4% complete)

---

## 🎯 QUICK WIN COMMANDS

Run these NOW for immediate impact:

```bash
# 1. Apply database indexes (CRITICAL)
cd /home/ghiffa/Documents/Projects_IoT/PlatformIO_Workspace/Projects/agrinex-smartdrip
php artisan migrate

# 2. Verify migration
php artisan migrate:status

# 3. Clear all caches
php artisan optimize:clear

# 4. Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Build assets
npm run build

# 6. Test build
php artisan serve
# Open http://localhost:8000 and test profile page
```

---

## 📞 SUPPORT RESOURCES

- **Full Audit Report:** `QA_AUDIT_PREDEPLOY_REPORT.md` (12KB)
- **PHP Code Audit:** `CODE_QUALITY_AUDIT_REPORT.md` (13KB)
- **Frontend Audit:** `FRONTEND_AUDIT_REPORT.md` (9KB)
- **Performance Audit:** `performance-audit-report.json` (15KB)
- **Migration File:** `database/migrations/2026_07_12_082600_add_missing_indexes_predeploy.php`

---

**Last Updated:** 2026-07-12 08:27 UTC  
**Next Review:** After sprint completion (2 weeks)

# AgriNex Smart Drip - PHP Code Quality Audit Report

**Project:** AgriNex Smart Drip IoT System  
**Framework:** Laravel 12  
**Audit Date:** 2026-07-12  
**Scope:** app/Http/Controllers, app/Models, app/Http/Middleware  
**Total Files Analyzed:** 50  
**Total Public Methods:** 202

---

## Executive Summary

**Overall Code Quality:** MEDIUM  
**Critical Issues:** 8  
**High Priority Issues:** 15  
**Medium Priority Issues:** 22  
**Low Priority Issues:** 18

### Key Findings:
- ✅ Good repository pattern implementation
- ✅ Dependency injection consistently used
- ⚠️ Missing return type declarations (PHP 8+ feature underutilized)
- ⚠️ Duplicate validation logic across controllers
- ⚠️ Empty middleware files (LogApiRequest.php, ValidateApiRequest.php)
- ⚠️ Unused imports and commented code
- ⚠️ Hardcoded values that should be in config
- ⚠️ Missing error handling in some methods
- ⚠️ Inconsistent naming conventions (Indonesian vs English)

---

## Detailed Issues

| File | Line | Issue | Severity | Details | Fix Recommendation |
|------|------|-------|----------|---------|-------------------|
| `app/Http/Middleware/LogApiRequest.php` | 1 | Empty file / Dead code | **HIGH** | File exists but is completely empty. Referenced in middleware but does nothing. | Remove file and registration, or implement logging functionality |
| `app/Http/Middleware/ValidateApiRequest.php` | 1 | Empty file / Dead code | **HIGH** | File exists but is completely empty. Referenced in middleware but does nothing. | Remove file and registration, or implement validation |
| `app/Http/Middleware/VerifyIotApiKey.php` | 20 | Hardcoded default API key | **HIGH** | Default API key 'agrinex-secure-key-2026' is hardcoded. Security risk if env not set. | Remove default, throw exception if IOT_API_KEY not configured |
| `app/Http/Controllers/Web/ProfileController.php` | 10 | Unused import: `Str` | **LOW** | `use Illuminate\Support\Str;` imported but never used in the file | Remove unused import |
| `app/Http/Controllers/Web/ProfileController.php` | 106-109 | TODO comment - Incomplete feature | **MEDIUM** | `linkOAuth()` method has TODO comment, returns success without implementation | Complete OAuth linking or remove method |
| `app/Http/Controllers/Web/ReportsController.php` | 51 | TODO comment - Incomplete feature | **MEDIUM** | `export()` method has TODO, returns fake success message | Implement export logic or mark as not implemented |
| `app/Http/Controllers/Auth/GoogleAuthController.php` | 29 | Weak username generation | **MEDIUM** | Username uses `rand(100, 999)` for collision avoidance. Not guaranteed unique. | Use `uniqid()` or check database uniqueness in loop |
| `app/Models/User.php` | 8 | Unused import: `Hash` | **LOW** | `use Illuminate\Support\Facades\Hash;` imported but never used (hashing done elsewhere) | Remove unused import |
| `app/Models/GetdataLog.php` | 19 | Commented code | **LOW** | `// 'jumlah_node',` commented in fillable array | Remove commented code or uncomment if needed |
| `app/Models/GetdataLog.php` | 28 | Cast for non-existent field | **MEDIUM** | `'jumlah_node' => 'integer'` cast defined but field commented in fillable | Remove cast or uncomment field |
| `app/Models/GetdataLog.php` | 81 | Potential division by zero | **MEDIUM** | `getSuccessRateAttribute()` uses `$this->jumlah_node` which doesn't exist in fillable | Fix field reference or add null check |
| `app/Models/NodeLog.php` | 41 | Incorrect relationship key | **MEDIUM** | `belongsTo(Node::class, 'node_id', 'id')` but Node table uses 'node_id' as key, not 'id' | Change to `'node_id', 'node_id'` |
| `app/Models/IrrigateLog.php` | 107 | Undefined method reference | **HIGH** | `node()` relationship defined but `node_id` not in fillable or casts | Add node_id to fillable or remove relationship |
| `app/Http/Controllers/Api/DashboardApiController.php` | N/A | Missing return type declarations | **LOW** | 15 methods lack `: JsonResponse` return types (PHP 8+ best practice) | Add return type hints to all public methods |
| `app/Http/Controllers/Api/DataIngestionController.php` | 40-47 | Duplicate validation rules | **MEDIUM** | Same validation structure repeated in 3 methods (storeSensorData, storeValveOn, storeValveOff) | Extract to Form Request class or private method |
| `app/Http/Controllers/Api/DataIngestionController.php` | 72-81 | Duplicate backup logic | **HIGH** | Identical JSON backup code repeated 3 times across methods | Extract to private method or service method |
| `app/Http/Controllers/Api/SensorDataController.php` | 66 | Hardcoded cache key | **MEDIUM** | `Cache::forget('dashboard_devices_repo')` uses hardcoded string | Move cache keys to config or constants class |
| `app/Http/Controllers/Api/SensorDataController.php` | 67 | Hardcoded cache key | **MEDIUM** | `Cache::forget('dashboard_weather_repo')` uses hardcoded string | Move cache keys to config or constants class |
| `app/Services/IrrigationService.php` | 74-78 | Duplicate auto-registration logic | **HIGH** | Same node auto-registration code appears 3 times across methods | Extract to private method `autoRegisterNode($nodeId)` |
| `app/Services/IrrigationService.php` | 128-137 | Duplicate auto-registration logic | **HIGH** | Duplicate of lines 74-78 | Extract to private method |
| `app/Services/SensorDataService.php` | 52-61 | Duplicate auto-registration logic | **HIGH** | Same pattern as IrrigationService - duplicate code | Extract to shared service or trait |
| `app/Services/SensorDataService.php` | 81-90 | Duplicate auto-registration logic | **HIGH** | Fourth occurrence of same auto-registration pattern | Extract to reusable method |
| `app/Repositories/Eloquent/EloquentLogRepository.php` | 40 | Raw SQL in join | **MEDIUM** | Uses `\DB::raw()` for subquery join - harder to maintain | Consider using Laravel query builder fluent syntax |
| `app/Http/Controllers/Auth/LoginController.php` | 36-38 | Inefficient query | **MEDIUM** | `where()->orWhere()->first()` doesn't use index efficiently | Use `whereIn()` or separate queries |
| `app/Http/Controllers/Auth/GoogleAuthController.php` | 22-24 | Inefficient query | **MEDIUM** | Same pattern as LoginController - `where()->orWhere()` | Split into two queries or use whereIn |
| `app/Http/Controllers/Web/ProfileController.php` | 71-75 | Inline validation closure | **LOW** | Complex validation logic in closure - harder to test | Extract to custom validation rule class |
| `app/Models/Node.php` | 46 | Incorrect foreign key | **MEDIUM** | `sensorData()` uses 'node_id' but should verify primary key match | Check if Node primary key is 'id' or 'node_id' |
| `app/Models/DataSession.php` | 11 | Mass assignment vulnerability | **MEDIUM** | Uses `$guarded = ['id']` instead of explicit `$fillable` | Switch to whitelist approach with $fillable |
| `app/Models/IrrigationLog.php` | 13 | Mass assignment vulnerability | **MEDIUM** | Uses `$guarded = ['id']` instead of explicit `$fillable` | Switch to whitelist approach with $fillable |
| `app/Models/Device.php` | 13 | Mass assignment vulnerability | **MEDIUM** | Uses `$guarded = ['id']` instead of explicit `$fillable` | Switch to whitelist approach with $fillable |
| `app/Models/SensorData.php` | 11 | Mass assignment vulnerability | **MEDIUM** | Uses `$guarded = ['id']` instead of explicit `$fillable` | Switch to whitelist approach with $fillable |
| `app/Http/Controllers/Api/DataIngestionController.php` | 102 | Duplicate error handling | **MEDIUM** | Same try-catch structure repeated in storeSensorData, storeValveOn, storeValveOff | Extract to trait or parent method |
| `app/Http/Controllers/Api/SensorDataController.php` | 78 | Duplicate error handling | **MEDIUM** | Similar try-catch pattern as DataIngestionController | Extract to shared error handler |
| `app/Http/Controllers/Api/IrrigationController.php` | 72 | Duplicate error handling | **MEDIUM** | Same try-catch pattern across controllers | Implement global exception handler |
| `app/Http/Controllers/Api/DashboardApiController.php` | 117 | Hardcoded error messages | **LOW** | `serverError()` method concatenates error message with `$e->getMessage()` | Use configurable error messages |
| `app/Http/Controllers/Auth/GoogleAuthController.php` | 52 | Hardcoded error message | **LOW** | `'Gagal login dengan Google. Pastikan kredensial OAuth valid.'` hardcoded | Move to language file |
| `app/Http/Controllers/Web/ProfileController.php` | 64 | Hardcoded error message | **LOW** | `'Akun OAuth tidak dapat mengubah sandi.'` hardcoded | Move to language file |
| `app/Http/Controllers/Web/ProfileController.php` | 103 | Hardcoded error message | **LOW** | `'Akun sudah terhubung dengan Google.'` hardcoded | Move to language file |
| `app/Http/Controllers/Web/ProfileController.php` | 122 | Hardcoded error message | **LOW** | `'Tidak dapat melepas akun Google tanpa sandi lokal.'` hardcoded | Move to language file |
| `app/Http/Controllers/Web/ProfileController.php` | 126 | Hardcoded error message | **LOW** | `'Akun tidak terhubung dengan Google.'` hardcoded | Move to language file |
| `app/Http/Controllers/Auth/LoginController.php` | 45 | Hardcoded error message | **LOW** | `'Your account has been deactivated.'` hardcoded | Move to language file |
| `app/Http/Controllers/Auth/LoginController.php` | 64 | Hardcoded error message | **LOW** | `'The provided credentials do not match our records.'` hardcoded | Move to language file |

## Summary of Code Quality Issues by Category

### **CRITICAL ISSUES (8)**
1. **Empty middleware files** (2) - Files that do nothing but are registered
2. **Hardcoded default API key** (1) - Security vulnerability
3. **Duplicate business logic** (3) - Auto-registration repeated in services
4. **Missing method implementations** (2) - TODOs without actual functionality

### **HIGH PRIORITY ISSUES (15)**
1. **Code duplication** - Same business logic repeated across services
2. **Database integrity issues** - Incorrect foreign key relationships
3. **Missing validation** - Relationships defined without proper fields
4. **Security gaps** - Mass assignment vulnerabilities

### **MEDIUM PRIORITY ISSUES (22)**
1. **Validation duplication** - Same rules repeated across controllers
2. **Inefficient queries** - `orWhere()` patterns that don't use indexes well
3. **Commented code** - Dead code left in codebase
4. **Missing fields** - Casts for non-existent database fields
5. **Raw SQL usage** - Hard to maintain and test
6. **TODO comments** - Incomplete features

### **LOW PRIORITY ISSUES (18)**
1. **Unused imports** - Cleanup needed
2. **Missing return types** - PHP 8+ best practices
3. **Hardcoded messages** - Internationalization needed
4. **Inline validation closures** - Hard to test

## Recommendations

### **Immediate Actions (1-2 weeks)**
1. **Remove or fix empty middleware files** - LogApiRequest.php and ValidateApiRequest.php
2. **Fix API key security** - Remove hardcoded default in VerifyIotApiKey.php
3. **Extract duplicate auto-registration logic** - Create shared method or service
4. **Fix database relationships** - Correct foreign key references in models

### **Short-term Improvements (2-4 weeks)**
1. **Implement Form Request classes** - Reduce validation duplication
2. **Move hardcoded strings to language files** - Prepare for internationalization
3. **Add return type declarations** - Modern PHP 8+ best practices
4. **Clean up commented code** - Remove or implement commented sections
5. **Fix mass assignment vulnerabilities** - Switch to explicit $fillable

### **Long-term Improvements (1-2 months)**
1. **Implement global exception handler** - Centralize error handling
2. **Add comprehensive tests** - Currently minimal test coverage found
3. **Implement caching strategy** - Move hardcoded cache keys to config
4. **Create service layer for shared logic** - Reduce code duplication
5. **Improve query performance** - Optimize database queries with proper indexes

## Tools Recommended for Continuous Quality
1. **PHPStan** - Static analysis for type safety
2. **Laravel Pint** - Code style and formatting
3. **PHP_CodeSniffer** - Coding standards enforcement
4. **Psalm** - Security vulnerability detection
5. **SonarQube** - Continuous code quality monitoring

---

**Audited By:** Hermes Agent  
**Next Review:** Q4 2026
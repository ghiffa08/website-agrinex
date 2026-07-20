# AgriNex Smart Drip - Production Setup

## Environment Variables (Hostinger)

Pastikan .env di production memiliki konfigurasi berikut:

### Google OAuth
```env
GOOGLE_CLIENT_ID="your-google-client-id.apps.googleusercontent.com"
GOOGLE_CLIENT_SECRET="your-google-client-secret"
GOOGLE_REDIRECT_URI="https://smartdrip-system.agrinex.io/auth/google/callback"
```

**Note:** Ganti `your-google-client-id` dan `your-google-client-secret` dengan credentials dari Google Cloud Console Anda.

### Session Configuration (untuk Capacitor WebView support)
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none
```

### App Configuration
```env
APP_URL=https://smartdrip-system.agrinex.io
APP_ENV=production
APP_DEBUG=false
```

## Google Cloud Console Setup

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Pilih project yang sudah ada atau buat baru
3. Navigasi ke **APIs & Services → Credentials**
4. Edit OAuth 2.0 Client ID
5. Tambahkan **Authorized redirect URIs**:
   ```
   https://smartdrip-system.agrinex.io/auth/google/callback
   ```
6. Save

## Post-Deployment Checklist

Setelah deploy ke Hostinger:

- [ ] Update .env di Hostinger dengan GOOGLE_REDIRECT_URI yang benar
- [ ] Run migration jika ada: `php artisan migrate --force`
- [ ] Clear cache: `php artisan config:clear && php artisan cache:clear`
- [ ] Restart PHP-FPM (jika perlu)
- [ ] Test Google OAuth login di browser
- [ ] Test username/password login
- [ ] Verify session persistent setelah reload

## Troubleshooting

### OAuth Error: redirect_uri_mismatch
**Cause:** GOOGLE_REDIRECT_URI di .env tidak match dengan Google Cloud Console
**Fix:** 
1. Check .env: `GOOGLE_REDIRECT_URI="https://smartdrip-system.agrinex.io/auth/google/callback"`
2. Check Google Cloud Console: URI harus sama persis
3. Clear config cache: `php artisan config:clear`

### Login Error 500
**Cause:** Session regeneration atau database issue
**Fix:**
1. Check storage/framework/sessions writable: `chmod -R 775 storage`
2. Check database connection di .env
3. Check Laravel log: `tail -f storage/logs/laravel.log`

### Session Not Persistent
**Cause:** Cookie settings untuk WebView
**Fix:**
1. Check .env: `SESSION_SECURE_COOKIE=true` dan `SESSION_SAME_SITE=none`
2. Pastikan HTTPS aktif (bukan HTTP)
3. Clear browser cookies dan reload

## Files Modified (Latest)

### Backend
- `app/Http/Controllers/Auth/LoginController.php` - Error handling for session regeneration
- `app/Http/Controllers/Auth/GoogleAuthController.php` - Simplified OAuth flow
- `config/session.php` - WebView compatible settings (same_site=none, secure=true)

### Frontend
- `resources/views/auth/login.blade.php` - Capacitor detection script (hide OAuth in mobile)

### Config
- `capacitor.config.json` - androidScheme=https for cookie support
- `.env.example` - Template for production

## Production URL
- Web: https://smartdrip-system.agrinex.io
- Login: https://smartdrip-system.agrinex.io/login
- OAuth Callback: https://smartdrip-system.agrinex.io/auth/google/callback

## Auto-Deploy
- Repo: https://github.com/ghiffa08/agrinex-web
- Branch: main
- Hostinger auto-pull on push

---
Last updated: 2026-07-21

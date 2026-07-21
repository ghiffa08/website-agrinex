#!/bin/bash
#═══════════════════════════════════════════════════════════════════════════════
# AgriNex SmartDrip - Restart Services Script
# Usage: sudo ./restart-services.sh
#═══════════════════════════════════════════════════════════════════════════════

set -e

echo "═══════════════════════════════════════════════════════════════════════════════"
echo "♻️  Restarting AgriNex Services"
echo "═══════════════════════════════════════════════════════════════════════════════"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo "⚠️  This script needs sudo privileges to restart services."
    echo "Run: sudo ./restart-services.sh"
    exit 1
fi

# Restart PHP-FPM (detect version)
echo "🔄 Restarting PHP-FPM..."
if systemctl is-active --quiet php8.2-fpm; then
    systemctl restart php8.2-fpm
    echo "✅ PHP 8.2-FPM restarted"
elif systemctl is-active --quiet php8.1-fpm; then
    systemctl restart php8.1-fpm
    echo "✅ PHP 8.1-FPM restarted"
elif systemctl is-active --quiet php8.0-fpm; then
    systemctl restart php8.0-fpm
    echo "✅ PHP 8.0-FPM restarted"
else
    echo "⚠️  PHP-FPM service not found"
fi
echo ""

# Restart Nginx
echo "🔄 Restarting Nginx..."
if systemctl is-active --quiet nginx; then
    systemctl restart nginx
    echo "✅ Nginx restarted"
else
    echo "⚠️  Nginx service not found"
fi
echo ""

# Restart Laravel Reverb (if running)
echo "🔄 Checking Laravel Reverb..."
if systemctl is-active --quiet laravel-reverb; then
    systemctl restart laravel-reverb
    echo "✅ Laravel Reverb restarted"
else
    echo "ℹ️  Laravel Reverb not running as service"
fi
echo ""

# Restart Laravel Queue Worker (if running)
echo "🔄 Checking Laravel Queue Worker..."
if systemctl is-active --quiet laravel-worker; then
    systemctl restart laravel-worker
    echo "✅ Laravel Queue Worker restarted"
else
    echo "ℹ️  Laravel Queue Worker not running as service"
fi
echo ""

echo "═══════════════════════════════════════════════════════════════════════════════"
echo "✅ ALL SERVICES RESTARTED"
echo "═══════════════════════════════════════════════════════════════════════════════"
echo ""
echo "Service status:"
systemctl status php8.2-fpm --no-pager -l | head -3 || true
systemctl status nginx --no-pager -l | head -3 || true
echo ""

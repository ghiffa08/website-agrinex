#!/bin/bash
#═══════════════════════════════════════════════════════════════════════════════
# AgriNex SmartDrip - Production Deployment Script
# Usage: ./deploy.sh
#═══════════════════════════════════════════════════════════════════════════════

set -e  # Exit on error

echo "═══════════════════════════════════════════════════════════════════════════════"
echo "🚀 AgriNex SmartDrip - Production Deployment"
echo "═══════════════════════════════════════════════════════════════════════════════"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Are you in the Laravel root directory?"
    exit 1
fi

echo "📍 Current directory: $(pwd)"
echo ""

# Enable maintenance mode
echo "🔧 Enabling maintenance mode..."
php artisan down --retry=60 || true
echo ""

# Pull latest changes
echo "📥 Pulling latest changes from GitHub..."
git fetch origin
git pull origin main
echo "✅ Git pull complete"
echo ""

# Show latest commits
echo "📝 Latest commits:"
git log --oneline -3
echo ""

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "✅ Composer install complete"
echo ""

# Install NPM dependencies & build
echo "📦 Installing NPM dependencies..."
npm ci --silent
echo "✅ NPM install complete"
echo ""

echo "🔨 Building frontend assets..."
npm run build
echo "✅ Build complete"
echo ""

# Clear all caches
echo "🗑️  Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
echo "✅ Caches cleared"
echo ""

# Rebuild optimized caches
echo "🔨 Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✅ Caches rebuilt"
echo ""

# Fix storage permissions
echo "🔐 Fixing storage permissions..."
chmod -R 775 storage bootstrap/cache
echo "✅ Permissions fixed"
echo ""

# Restart queue workers (if any)
echo "♻️  Restarting queue workers..."
php artisan queue:restart || true
echo "✅ Queue workers restarted"
echo ""

# Disable maintenance mode
echo "🔓 Disabling maintenance mode..."
php artisan up
echo "✅ Application is now live"
echo ""

echo "═══════════════════════════════════════════════════════════════════════════════"
echo "✅ DEPLOYMENT COMPLETE!"
echo "═══════════════════════════════════════════════════════════════════════════════"
echo ""
echo "🌐 URL: https://smartdrip-system.agrinex.io/"
echo "📊 Check dashboard: /admin/dashboard"
echo "🔍 Check logs: tail -f storage/logs/laravel.log"
echo ""
echo "Next steps:"
echo "  1. Hard reload browser (Ctrl+Shift+R)"
echo "  2. Check console (F12) for errors"
echo "  3. Test sidebar navigation"
echo "  4. Verify no Alpine.js errors"
echo ""
echo "═══════════════════════════════════════════════════════════════════════════════"

#!/bin/bash

# Panduan eksekusi lengkap normalisasi database AgriNex
# Jalankan script ini untuk eksekusi otomatis semua langkah

set -e  # Exit on error

echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║         NORMALISASI DATABASE - AGRINEX SMART DRIP                    ║"
echo "║                  Automated Execution Script                          ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Step 1: Confirmation
echo -e "${YELLOW}PERINGATAN:${NC} Script ini akan:"
echo "  - Drop 9 tabel legacy dari database"
echo "  - Hapus 7 Model files"
echo "  - Hapus folder Admin Controllers"
echo "  - Tambah foreign key constraints"
echo ""
read -p "Apakah Anda sudah backup database? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${RED}Aborted. Backup database terlebih dahulu!${NC}"
    exit 1
fi

# Step 2: Pre-migration check
echo ""
echo -e "${BLUE}[STEP 1/5]${NC} Pre-migration checks..."
if [ -f "database/pre_migration_check.sh" ]; then
    bash database/pre_migration_check.sh
else
    echo -e "${YELLOW}Warning: pre_migration_check.sh not found, skipping...${NC}"
fi

read -p "Continue to migration? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Aborted."
    exit 1
fi

# Step 3: Run migration
echo ""
echo -e "${BLUE}[STEP 2/5]${NC} Running database migration..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migration completed${NC}"
else
    echo -e "${RED}✗ Migration failed!${NC}"
    exit 1
fi

# Step 4: Cleanup legacy files
echo ""
echo -e "${BLUE}[STEP 3/5]${NC} Removing legacy files..."
bash database/cleanup_legacy_files.sh
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Legacy files removed${NC}"
else
    echo -e "${RED}✗ Cleanup failed!${NC}"
    exit 1
fi

# Step 5: Clear caches
echo ""
echo -e "${BLUE}[STEP 4/5]${NC} Clearing application caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✓ Caches cleared${NC}"

# Step 6: Verification
echo ""
echo -e "${BLUE}[STEP 5/5]${NC} Verification..."
echo ""
echo "Checking migration status..."
php artisan migrate:status | grep "normalize_database_drop_legacy_tables"
echo ""

echo -e "${GREEN}╔══════════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                    NORMALISASI SELESAI!                              ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo "Next steps:"
echo "  1. Test dashboard: http://your-domain.com/"
echo "  2. Test devices page: http://your-domain.com/devices"
echo "  3. Test node detail: http://your-domain.com/node/1"
echo "  4. Test API telemetry: POST /api/telemetry"
echo ""
echo "Jika ada masalah, restore dari backup:"
echo "  mysql -u username -p database_name < backup.sql"
echo ""

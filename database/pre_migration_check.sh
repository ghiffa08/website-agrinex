#!/bin/bash
# Tes koneksi database dan verifikasi struktur sebelum migration

echo "=== Pre-Migration Database Check ==="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Database credentials (update sesuai .env)
DB_HOST="localhost"
DB_NAME="u802160697_agrinew"
DB_USER="u802160697_ghiffa"

echo "Checking database connection..."
if mysql -h "$DB_HOST" -u "$DB_USER" -p -e "USE $DB_NAME;" 2>/dev/null; then
    echo -e "${GREEN}✓ Database connection OK${NC}"
else
    echo -e "${RED}✗ Database connection FAILED${NC}"
    exit 1
fi

echo ""
echo "Checking legacy tables exist..."

LEGACY_TABLES=("node" "sensor_node_data" "sensor_weather_data" "getdata_logs" "json_backup" "node_logs" "irrigate_logs" "push_logs" "data_sync_status")

for table in "${LEGACY_TABLES[@]}"; do
    COUNT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p -D "$DB_NAME" -se "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME' AND table_name='$table';" 2>/dev/null)
    if [ "$COUNT" -eq 1 ]; then
        echo -e "${YELLOW}  $table - exists${NC}"
    else
        echo -e "${RED}  $table - NOT FOUND${NC}"
    fi
done

echo ""
echo "Checking current tables exist..."

CURRENT_TABLES=("devices" "sensor_data" "weather_data" "device_logs" "data_sessions" "irrigation_logs" "valve_logs")

for table in "${CURRENT_TABLES[@]}"; do
    COUNT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p -D "$DB_NAME" -se "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME' AND table_name='$table';" 2>/dev/null)
    if [ "$COUNT" -eq 1 ]; then
        echo -e "${GREEN}  ✓ $table${NC}"
    else
        echo -e "${RED}  ✗ $table - MISSING!${NC}"
    fi
done

echo ""
echo "Pre-migration check completed."
echo ""
echo "If all checks pass, proceed with:"
echo "  php artisan migrate"

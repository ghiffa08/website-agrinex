#!/bin/bash
# Script to remove legacy files after database normalization
# Run this after migration is successful

echo "=== AgriNex Database Normalization - Legacy Files Cleanup ==="
echo ""
echo "This script will remove:"
echo "  - Legacy Model files (Node, GetdataLog, JsonBackup, etc.)"
echo "  - Legacy Admin Controllers"
echo "  - Legacy Service files"
echo ""

read -p "Continue? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    echo "Aborted."
    exit 1
fi

echo ""
echo "Removing legacy Model files..."
rm -fv app/Models/Node.php
rm -fv app/Models/GetdataLog.php
rm -fv app/Models/JsonBackup.php
rm -fv app/Models/IrrigateLog.php
rm -fv app/Models/NodeLog.php
rm -fv app/Models/SensorNodeData.php
rm -fv app/Models/SensorWeatherData.php

echo ""
echo "Removing legacy Admin Controllers..."
rm -rfv app/Http/Controllers/Admin/

echo ""
echo "Removing legacy Service files..."
rm -fv app/Services/Admin/GetdataLogsService.php
rmdir app/Services/Admin/ 2>/dev/null || true

echo ""
echo "Cleanup completed!"
echo ""
echo "Next steps:"
echo "  1. Run: php artisan migrate"
echo "  2. Run: php artisan optimize:clear"
echo "  3. Test the application"

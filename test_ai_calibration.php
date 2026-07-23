#!/usr/bin/env php
<?php

/**
 * Test AI Field Capacity Calibration
 * 
 * Usage:
 *   php test_ai_calibration.php [device_id]
 * 
 * Example:
 *   php test_ai_calibration.php 65
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Device;
use App\Models\SensorData;
use App\Services\AI\FieldCapacityCalibrationService;
use Carbon\Carbon;

// Get device ID from command line
$deviceId = $argv[1] ?? null;

if (!$deviceId) {
    echo "❌ Usage: php test_ai_calibration.php [device_id]\n";
    exit(1);
}

echo "🧪 Testing AI Field Capacity Calibration\n";
echo "========================================\n\n";

// Check if device exists
$device = Device::find($deviceId);
if (!$device) {
    echo "❌ Device #{$deviceId} not found\n";
    exit(1);
}

echo "✅ Device found: #{$device->id}\n";
echo "   Name: {$device->kode_perlakuan}\n";
echo "   Current status: {$device->ai_calibration_status}\n\n";

// Check sensor data availability
$dataCount = SensorData::where('device_id', $deviceId)
    ->where('recorded_at', '>=', now()->subHours(72))
    ->count();

echo "📊 Sensor Data (last 72h): {$dataCount} records\n\n";

if ($dataCount < 10) {
    echo "⚠️  WARNING: Minimal 10 data points dibutuhkan untuk analisis yang baik\n";
    echo "   Current: {$dataCount} records\n";
    echo "   Analisis tetap bisa dilakukan, tapi confidence akan rendah\n\n";
}

// Get latest sensor reading
$latest = SensorData::where('device_id', $deviceId)
    ->latest('recorded_at')
    ->first();

if ($latest) {
    echo "📈 Latest Sensor Reading:\n";
    echo "   Time: {$latest->recorded_at}\n";
    echo "   Moisture: {$latest->soil_moisture}%\n";
    echo "   ADC: {$latest->soil_adc}\n";
    echo "   Temperature: {$latest->temperature}°C\n\n";
}

// Test service instantiation
try {
    $service = app(FieldCapacityCalibrationService::class);
    echo "✅ FieldCapacityCalibrationService loaded\n\n";
} catch (\Exception $e) {
    echo "❌ Failed to load service: {$e->getMessage()}\n";
    exit(1);
}

// Get current calibration status
echo "🔍 Fetching current calibration status...\n";
$status = $service->getStatus($deviceId);

echo "   Status: {$status['status']}\n";
echo "   Iteration: {$status['iteration']}\n";
echo "   Can analyze: " . ($status['can_analyze'] ? 'YES' : 'NO') . "\n";

if ($status['started_at']) {
    echo "   Started at: {$status['started_at']}\n";
}
if ($status['saturation_at']) {
    echo "   Saturated at: {$status['saturation_at']}\n";
}
if ($status['hours_remaining']) {
    echo "   Hours remaining: {$status['hours_remaining']}\n";
}

if (isset($status['results'])) {
    echo "\n✅ Calibration Results:\n";
    echo "   FC ADC: {$status['results']['field_capacity_adc']}\n";
    echo "   WP ADC: " . ($status['results']['wilting_point_adc'] ?? 'N/A') . "\n";
    echo "   Confidence: {$status['results']['confidence_score']}%\n";
    echo "   Quality: {$status['results']['analysis_quality']}\n";
    echo "   Reasoning: {$status['results']['reasoning']}\n";
}

echo "\n";

// Interactive menu
echo "🎯 Available Actions:\n";
echo "   1. Start new calibration\n";
echo "   2. Confirm saturation (if status = user_saturating)\n";
echo "   3. Trigger analysis (if ready)\n";
echo "   4. Cancel calibration\n";
echo "   5. View full status\n";
echo "   0. Exit\n\n";

echo "Choose action (0-5): ";
$action = trim(fgets(STDIN));

switch ($action) {
    case '1':
        echo "\n🚀 Starting calibration...\n";
        $result = $service->startCalibration($deviceId);
        echo ($result['success'] ? '✅' : '❌') . " {$result['message']}\n";
        break;
        
    case '2':
        echo "\n💧 Confirming saturation...\n";
        $result = $service->confirmSaturation($deviceId);
        echo ($result['success'] ? '✅' : '❌') . " {$result['message']}\n";
        break;
        
    case '3':
        echo "\n🤖 Triggering AI analysis...\n";
        echo "⚠️  This will consume Gemini API quota!\n";
        echo "Continue? (yes/no): ";
        $confirm = trim(fgets(STDIN));
        
        if (strtolower($confirm) === 'yes') {
            echo "\nAnalyzing (this may take 30-60 seconds)...\n";
            $result = $service->analyzeFieldCapacity($deviceId, force: true);
            
            echo "\n";
            echo ($result['success'] ? '✅' : '❌') . " {$result['message']}\n";
            
            if (isset($result['results'])) {
                echo "\n📊 Results:\n";
                echo json_encode($result['results'], JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo "Cancelled.\n";
        }
        break;
        
    case '4':
        echo "\n🛑 Cancelling calibration...\n";
        $device->update([
            'ai_calibration_status' => 'idle',
            'ai_calibration_started_at' => null,
            'ai_saturation_completed_at' => null
        ]);
        echo "✅ Calibration cancelled\n";
        break;
        
    case '5':
        echo "\n📋 Full Status:\n";
        echo json_encode($status, JSON_PRETTY_PRINT) . "\n";
        break;
        
    case '0':
        echo "👋 Bye!\n";
        break;
        
    default:
        echo "❌ Invalid option\n";
}

echo "\n";

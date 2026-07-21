<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FlasherController extends Controller
{
    /**
     * Display ESP32 Web Flasher interface
     */
    public function index()
    {
        return view('admin.flasher.index');
    }

    /**
     * Get available firmware list
     */
    public function firmwareList()
    {
        $firmwares = [
            [
                'id' => 'sender',
                'name' => 'NODE01 - Sender',
                'description' => 'LoRa Transmitter dengan sensor suhu, kelembaban tanah, INA226 power monitor',
                'board' => 'Seeed XIAO ESP32-S3',
                'version' => 'v2.1.0',
                'size' => '~480 KB',
                'features' => [
                    'LoRa SX1278 433MHz',
                    'DS18B20 Temperature Sensor',
                    'Capacitive Soil Moisture',
                    'INA226 Power Monitor',
                    'Adaptive Sleep Mode',
                    'Battery Voltage Monitoring'
                ],
                'manifest' => '/flasher-firmware/sender/manifest.json'
            ],
            [
                'id' => 'receiver',
                'name' => 'NODE02 - Receiver',
                'description' => 'LoRa Receiver dengan WiFi sync ke cloud API dan Fuzzy AI control',
                'board' => 'Seeed XIAO ESP32-S3',
                'version' => 'v2.1.0',
                'size' => '~520 KB',
                'features' => [
                    'LoRa SX1278 433MHz Receiver',
                    'WiFi Cloud Sync',
                    'Fuzzy Logic AI Control',
                    'Local Fallback Mode',
                    'Valve Control Output',
                    'Real-time Dashboard Broadcast'
                ],
                'manifest' => '/flasher-firmware/receiver/manifest.json',
                'requires_config' => true
            ],
            [
                'id' => 'tester',
                'name' => 'Node Tester',
                'description' => 'Diagnostics tool untuk testing sensor dan komunikasi LoRa',
                'board' => 'Seeed XIAO ESP32-S3',
                'version' => 'v1.0.0',
                'size' => '~450 KB',
                'features' => [
                    'Sensor Health Check',
                    'LoRa Range Test',
                    'Battery Test',
                    'Serial Diagnostics',
                    'Pin Connectivity Test'
                ],
                'manifest' => '/flasher-firmware/tester/manifest.json'
            ]
        ];

        return response()->json([
            'status' => 'success',
            'firmwares' => $firmwares
        ]);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FlasherController extends Controller
{
    /**
     * Display ESP32 Web Flasher page (Neumorphic Design)
     */
    public function index()
    {
        return view('flasher');
    }

    /**
     * Get list of available firmware with manifest details
     */
    public function firmwareList()
    {
        $firmwarePath = public_path('flasher-firmware');
        
        if (!File::exists($firmwarePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Firmware directory not found'
            ], 404);
        }

        $firmwares = [];
        $types = ['sender', 'receiver', 'tester'];

        foreach ($types as $type) {
            $typePath = $firmwarePath . '/' . $type;
            $manifestPath = $typePath . '/manifest.json';

            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                
                $firmwares[] = [
                    'type' => $type,
                    'name' => $manifest['name'] ?? ucfirst($type),
                    'version' => $manifest['version'] ?? 'unknown',
                    'chipFamily' => $manifest['chipFamily'] ?? 'ESP32-S3',
                    'parts' => $manifest['parts'] ?? [],
                    'url' => asset("flasher-firmware/{$type}/manifest.json")
                ];
            }
        }

        return response()->json([
            'success' => true,
            'firmwares' => $firmwares
        ]);
    }
}

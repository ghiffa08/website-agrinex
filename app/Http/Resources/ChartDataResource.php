<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class ChartDataResource extends ResourceCollection
{
    protected $type;

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function toArray($request)
    {
        $type = $this->type ?? 'all';
        $chartData = [
            'temperature' => [],
            'humidity' => [],
            'light' => [],
            'soilMoisture' => [],
            'voltage' => [],
            'power' => []
        ];

        foreach ($this->collection as $session) {
            $sessionTime = Carbon::parse($session->started_at);
            $timestamp = $sessionTime->format('H:i');
            
            $weather = null;
            $weatherList = $session->weatherData;
            if ($weatherList && $weatherList->count() > 0) {
                $weather = $weatherList->first();
                
                if ($type === 'all' || $type === 'temperature') {
                    $chartData['temperature'][] = [
                        'time' => $timestamp,
                        'value' => (float) $weather->temp_c,
                        'temperature' => (float) $weather->temp_c
                    ];
                }
                
                if ($type === 'all' || $type === 'humidity') {
                    $chartData['humidity'][] = [
                        'time' => $timestamp,
                        'value' => (float) $weather->humidity_pct,
                        'humidity' => (float) $weather->humidity_pct
                    ];
                }
                
                if ($type === 'all' || $type === 'light') {
                    $chartData['light'][] = [
                        'time' => $timestamp,
                        'value' => (float) $weather->light_lux,
                        'radiation' => (float) $weather->light_lux
                    ];
                }
                
                if ($type === 'all' || $type === 'voltage') {
                    $chartData['voltage'][] = [
                        'time' => $timestamp,
                        'value' => (float) $weather->voltage_v,
                        'voltage' => (float) $weather->voltage_v
                    ];
                }
                
                if ($type === 'all' || $type === 'power') {
                    $chartData['power'][] = [
                        'time' => $timestamp,
                        'value' => (float) $weather->power_mw,
                        'power' => (float) $weather->power_mw
                    ];
                }
            }
            
            if ($type === 'all' || $type === 'soilMoisture') {
                $soilValues = [];
                foreach ($session->sensorData as $node) {
                    $sensorId = "SM{$node->device_id}";
                    
                    if (!isset($chartData['soilMoisture'][$sensorId])) {
                        $chartData['soilMoisture'][$sensorId] = [];
                    }
                    
                    $chartData['soilMoisture'][$sensorId][] = [
                        'time' => $timestamp,
                        'value' => (float) $node->soil_moisture
                    ];
                    
                    $soilValues[] = (float) $node->soil_moisture;
                }
                
                if (!empty($soilValues)) {
                    if (!isset($chartData['soil'])) {
                        $chartData['soil'] = [];
                    }
                    $chartData['soil'][] = [
                        'time' => $timestamp,
                        'average' => array_sum($soilValues) / count($soilValues)
                    ];
                }
            }
            
            if (($type === 'all' || $type === 'water') && $weather && isset($weather->level) && $weather->level !== null) {
                if (!isset($chartData['water'])) {
                    $chartData['water'] = [];
                }
                $chartData['water'][] = [
                    'time' => $timestamp,
                    'level' => (float) $weather->level
                ];
            }
        }
        
        return $chartData;
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgriNexDashboardController extends Controller
{
    /**
     * Display the AgriNex Dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('welcome-neumorphism', [
            'pageTitle' => 'AgriNex Dashboard - IoT Smart Agriculture System',
            'pageDescription' => 'Real-time monitoring and control for smart irrigation system'
        ]);
    }

    public function nodeDetail($id)
    {
        return view('agrinex-node-detail', [
            'deviceId' => $id,
            'pageTitle' => 'Node Details - AgriNex',
            'pageDescription' => 'Detailed view for node monitoring'
        ]);
    }
}

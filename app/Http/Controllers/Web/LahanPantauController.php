<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class LahanPantauController extends Controller
{
    /**
     * Display the Lahan Pantau page
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $allDevices = \App\Models\Device::select('id', 'name', 'lahan_pantau_id')->get();
        return view('lahan-pantau.index', [
            'pageTitle' => 'Lahan Pantau - AgriNex',
            'pageDescription' => 'Kelola dan monitor area lahan pertanian Anda',
            'allDevices' => $allDevices,
        ]);
    }

    /**
     * Display detail of specific lahan
     */
    public function show(int $id)
    {
        $lahan = \App\Models\LahanPantau::findOrFail($id);
        $devices = \App\Models\Device::where('lahan_pantau_id', $id)->get();
        
        return view('lahan-pantau.show', [
            'lahan' => $lahan,
            'devices' => $devices,
            'pageTitle' => $lahan->nama_lahan . ' - Lahan Pantau',
            'pageDescription' => 'Detail monitoring lahan pertanian'
        ]);
    }
}

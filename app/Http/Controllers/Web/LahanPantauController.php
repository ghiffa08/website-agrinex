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
        return view('lahan-pantau.index', [
            'pageTitle' => 'Lahan Pantau - AgriNex',
            'pageDescription' => 'Kelola dan monitor area lahan pertanian Anda'
        ]);
    }

    /**
     * Display detail of specific lahan
     */
    public function show(int $id)
    {
        return view('lahan-pantau.detail', [
            'lahanId' => $id,
            'pageTitle' => 'Detail Lahan Pantau - AgriNex',
            'pageDescription' => 'Detail monitoring lahan pertanian'
        ]);
    }
}

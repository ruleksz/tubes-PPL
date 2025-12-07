<?php

namespace App\Http\Controllers;

use App\Services\NasaService;

class NasaController extends Controller
{
    public function apod(NasaService $nasa)
    {
        $data = $nasa->getApod();
        return view('apod', compact('data'));
    }
}

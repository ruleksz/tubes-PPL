<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValidatorUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:php', // validasi ekstensi .php
                'min:1'
            ],
        ]);

        return back()->with('success', 'File uploaded successfully');
    }
}

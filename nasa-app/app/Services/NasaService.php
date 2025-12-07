<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NasaService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('NASA_API_KEY');
    }

    // APOD (Astronomy Picture of the Day)
    public function getApod()
    {
        return Http::get("https://api.nasa.gov/planetary/apod", [
            'api_key' => $this->apiKey
        ])->json();
    }
}

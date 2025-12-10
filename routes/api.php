<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\Api\CurhatController;
use App\Http\Controllers\Api\CurhatMessageController;

Route::post('/curhat', [CurhatController::class,'store']);             // buat curhat baru
Route::get('/curhat', [CurhatController::class,'index']);             // list curhat (public)
Route::get('/curhat/{id}', [CurhatController::class,'show']);        // detail curhat + messages

Route::post('/curhat/{id}/message', [CurhatMessageController::class,'send']); // user balas
// admin reply bisa pakai middleware admin (tidak dipakai di contoh sederhana)

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

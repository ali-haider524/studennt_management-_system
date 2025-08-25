<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => ['ok' => true, 'time' => now()->toISOString()]);

// Example protected endpoint (works if you later enable Sanctum/API auth):
Route::middleware('auth')->get('/me', function (Request $request) {
    return $request->user();
});

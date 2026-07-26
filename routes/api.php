<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComplaintApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Complaint Management - Web Service (Provider)
Route::get('/complaints/water-source/{id}', [ComplaintApiController::class, 'statsByWaterSource']); 
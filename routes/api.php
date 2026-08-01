<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComplaintApiController;
use App\Http\Controllers\Api\WaterSourceApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Complaint Management - Web Service (Provider)
Route::get('/complaints/water-source/{id}', [ComplaintApiController::class, 'statsByWaterSource']); 

// Water Source Management - Web Service (Provider)
Route::get('/water-sources/{id?}', [WaterSourceApiController::class, 'show']);

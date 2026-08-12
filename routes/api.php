<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComplaintApiController;
use App\Http\Controllers\Api\WaterSourceApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\QualityReadingApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Complaint Management - Web Service (Provider)
Route::get('/complaints/water-source/{id}', [ComplaintApiController::class, 'statsByWaterSource']); 

// Water Source Management - Web Service (Provider)
Route::get('/water-sources', [WaterSourceApiController::class, 'index']);
Route::get('/water-sources/{id}', [WaterSourceApiController::class, 'show']);

// Quality Reading Management - Web Service (Provider)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/quality-readings', [QualityReadingApiController::class, 'index']);
    Route::get('/quality-readings/{id}', [QualityReadingApiController::class, 'show']);
    Route::get('/quality-readings/water-source/{id}', [QualityReadingApiController::class, 'byWaterSource']);
});

// User Management - Web Service (Provider)
Route::middleware(['web', 'auth'])->get('/inspectors', [UserApiController::class, 'inspectors']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserApiController::class, 'users']);
});

<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\WaterSourceController;
use App\Http\Controllers\QualityReadingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AlertController;

Route::get('/', function () {
    return view('welcome');
});

// Resource Routes
Route::resource('users', UserController::class);

Route::resource('water-sources', WaterSourceController::class);

Route::resource('quality-readings', QualityReadingController::class);

Route::resource('complaints', ComplaintController::class);

Route::resource('alerts', AlertController::class);
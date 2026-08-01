<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\WaterSourceController;
use App\Http\Controllers\QualityReadingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Guest routes: only accessible when NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('complaints', ComplaintController::class);
    Route::resource('water-sources', WaterSourceController::class);

    // User Management
    Route::resource('users', UserController::class)->except(['update']);
    Route::patch('users/{user}/status', [UserController::class, 'update'])
        ->name('users.update.status');

    // API
    Route::get('api/inspectors', [UserController::class, 'inspectorInfo'])
        ->name('api.inspectors');
    Route::get('api/users', [UserController::class, 'userInfo'])
        ->name('api.users');
});

// Other Resources
Route::resource('quality-readings', QualityReadingController::class);
Route::resource('alerts', AlertController::class);
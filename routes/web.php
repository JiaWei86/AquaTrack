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

// Authentication routes (temporary — to be taken over by Yi Teng)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes: login required
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])
        ->name('users.update.status');
    Route::resource('users', UserController::class)->only([
        'index',
        'create',
        'store',
        'destroy',
    ]);
    Route::get('/api/inspectors', [UserController::class, 'inspectorInfo'])
        ->name('api.inspectors');
    Route::get('/api/users', [UserController::class, 'userInfo'])
        ->name('api.users');

    Route::resource('complaints', ComplaintController::class);
    Route::resource('water-sources', WaterSourceController::class);
    Route::resource('quality-readings', QualityReadingController::class);
    Route::resource('alerts', AlertController::class);
});

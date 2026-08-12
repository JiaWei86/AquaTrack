<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\WaterSourceController;
use App\Http\Controllers\QualityReadingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // The signed-in user's own profile.
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    Route::resource('complaints', ComplaintController::class);

    Route::resource('water-sources', WaterSourceController::class);

    // User Management
    Route::resource('users', UserController::class)->except(['update']);

    Route::patch(
        'users/{user}/status',
        [UserController::class, 'updateStatus']
    )->name('users.update.status');

    Route::resource('quality-readings', QualityReadingController::class)
        ->middlewareFor('store', 'throttle:60,1');

});

// Other Resources
Route::resource('alerts', AlertController::class);

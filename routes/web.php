<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GatewayController;

// Halaman awal
Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

// Route untuk user yang belum login
Route::middleware('guest')->group(function () {

    Route::get('/login', [
        LoginController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login'
    ])->name('login.process');

});

// Route untuk user yang sudah login
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/targets', [GatewayController::class, 'index'])
    ->name('targets.index');

    Route::get('/targets/create', [GatewayController::class, 'create'])
    ->name('targets.create');

    Route::post('/targets', [GatewayController::class, 'store'])
    ->name('targets.store');

    Route::get('/targets/{gateway}/edit', [GatewayController::class, 'edit'])
    ->name('targets.edit');

    Route::put('/targets/{gateway}', [GatewayController::class, 'update'])
    ->name('targets.update');

    Route::delete('/targets/{gateway}', [GatewayController::class, 'destroy'])
    ->name('targets.destroy');

    Route::get('/history', function () {
        return view('history.index');
    })->name('history.index');

    Route::get('/incidents', function () {
        return view('incidents.index');
    })->name('incidents.index');

    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');

    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

    // Logout
    Route::post('/logout', [
        LoginController::class,
        'logout'
    ])->name('logout');

});
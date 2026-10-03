<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConnectionLogController;
use App\Http\Controllers\ReportController;
use App\Models\User;


// ============================================================
// Halaman awal
// ============================================================

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');


// ============================================================
// User yang belum login
// ============================================================

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


// ============================================================
// User yang sudah login
// ============================================================

Route::middleware('auth')->group(function () {


    // ========================================================
    // SEMUA ROLE
    // ========================================================

    // Dashboard
    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');


    // Histori
    Route::get('/history', [
        ConnectionLogController::class,
        'index'
    ])->name('history.index');


    // ========================================================
    // ADMIN + PEMILIK
    // ========================================================

    // Laporan
    Route::middleware('role:admin,pemilik')->group(function () {

        Route::get('/reports', [
            ReportController::class,
            'index'
        ])->name('reports.index');

    });


    // ========================================================
    // ADMIN
    // ========================================================

    Route::middleware('role:admin')->group(function () {

    // Pengguna
    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');

});


    // ========================================================
    // TEKNISI
    // ========================================================

    Route::middleware('role:teknisi')->group(function () {

    // Gateway / Target
    Route::get('/targets', [
        GatewayController::class,
        'index'
    ])->name('targets.index');

    Route::get('/targets/create', [
        GatewayController::class,
        'create'
    ])->name('targets.create');

    Route::post('/targets', [
        GatewayController::class,
        'store'
    ])->name('targets.store');

    Route::get('/targets/{gateway}/edit', [
        GatewayController::class,
        'edit'
    ])->name('targets.edit');

    Route::put('/targets/{gateway}', [
        GatewayController::class,
        'update'
    ])->name('targets.update');

    Route::delete('/targets/{gateway}', [
        GatewayController::class,
        'destroy'
    ])->name('targets.destroy');

    // Cek Gateway dari Histori
    Route::post('/history/{gateway}/check', [
        ConnectionLogController::class,
        'check'
    ])->name('history.check');

    // Pengaturan
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');
});

// ========================================================
// ADMIN + TEKNISI
// ========================================================

Route::middleware('role:admin,teknisi')->group(function () {

    Route::get('/incidents', function () {
        return view('incidents.index');
    })->name('incidents.index');

});


    // ========================================================
    // LOGOUT
    // ========================================================

    Route::post('/logout', [
        LoginController::class,
        'logout'
    ])->name('logout');

});
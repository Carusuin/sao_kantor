<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanFakturController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Password Update Route
    Route::put('/password', function () {
        return redirect()->back()->with('success', 'Password berhasil diubah.');
    })->name('password.update');
    
    // Laporan Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/create', [LaporanController::class, 'create'])->name('create');
    Route::post('/', [LaporanController::class, 'store'])->name('store');
    Route::get('/{laporan}', [LaporanController::class, 'show'])->name('show');
    Route::get('/{laporan}/edit', [LaporanController::class, 'edit'])->name('edit');
    Route::put('/{laporan}', [LaporanController::class, 'update'])->name('update');
    Route::delete('/{laporan}', [LaporanController::class, 'destroy'])->name('destroy');
    
    // XML Export routes
    Route::get('/{laporan}/export-xml', [LaporanController::class, 'exportXML'])->name('export.xml');
    Route::get('/{laporan}/preview-xml', [LaporanController::class, 'previewXML'])->name('preview.xml');
    
    // AJAX routes
    Route::post('/generate', [LaporanController::class, 'generateLaporan'])->name('generate');
    });
    
    // Laporan Faktur Routes
    Route::prefix('laporan-faktur')->name('laporan_faktur.')->group(function () {
    Route::get('/', [LaporanFakturController::class, 'index'])->name('index');
    Route::get('/create', [LaporanFakturController::class, 'create'])->name('create');
    Route::post('/', [LaporanFakturController::class, 'store'])->name('store');
    Route::get('/{laporan}', [LaporanFakturController::class, 'show'])->name('show');
    Route::get('/{laporan}/edit', [LaporanFakturController::class, 'edit'])->name('edit');
    Route::put('/{laporan}', [LaporanFakturController::class, 'update'])->name('update');
    Route::delete('/{laporan}', [LaporanFakturController::class, 'destroy'])->name('destroy');
    
    // XML Export routes
    Route::get('/{laporan}/export-xml', [LaporanFakturController::class, 'exportXML'])->name('export.xml');
    Route::get('/{laporan}/preview-xml', [LaporanFakturController::class, 'previewXML'])->name('preview.xml');
    
    // AJAX routes
    Route::post('/generate', [LaporanFakturController::class, 'generateLaporan'])->name('generate');
});
});

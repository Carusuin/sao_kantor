<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanFakturController;
use App\Http\Controllers\EFakturXmlExportController;
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
        Route::get('/create-header', [LaporanFakturController::class, 'createHeader'])->name('create_header');
        Route::post('/store-header', [LaporanFakturController::class, 'storeHeader'])->name('store_header');
        Route::get('/{laporanFaktur}', [LaporanFakturController::class, 'show'])->name('show');
    });

    // E-Faktur XML Export Routes
    Route::prefix('laporan-faktur/export')->name('efaktur.export.')->group(function () {
        Route::get('all', [EFakturXmlExportController::class, 'exportAll'])->name('all');
        Route::get('single/{faktur}', [EFakturXmlExportController::class, 'exportSingle'])->name('single');
        Route::post('date-range', [EFakturXmlExportController::class, 'exportByDateRange'])->name('date-range');
        Route::post('preview', [EFakturXmlExportController::class, 'preview'])->name('preview');
    });
});

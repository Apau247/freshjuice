<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\QualityController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Production
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/production/create', [ProductionController::class, 'create'])->name('production.create');
    Route::post('/production', [ProductionController::class, 'store'])->name('production.store');
    Route::get('/production/{id}/edit', [ProductionController::class, 'edit'])->name('production.edit');
    Route::put('/production/{id}', [ProductionController::class, 'update'])->name('production.update');
    Route::delete('/production/{id}', [ProductionController::class, 'destroy'])->name('production.destroy');

    // Quality
    Route::get('/quality', [QualityController::class, 'index'])->name('quality.index');
    Route::get('/quality/create', [QualityController::class, 'create'])->name('quality.create');
    Route::post('/quality', [QualityController::class, 'store'])->name('quality.store');
    Route::get('/quality/{id}/edit', [QualityController::class, 'edit'])->name('quality.edit');
    Route::put('/quality/{id}', [QualityController::class, 'update'])->name('quality.update');
    Route::delete('/quality/{id}', [QualityController::class, 'destroy'])->name('quality.destroy');
});

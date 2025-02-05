<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DocumentController;

// Authentication Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

// Protected Routes (accessible only to authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{id}', [DocumentController::class, 'show'])->name('documents.show');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
	Route::put('/documents/{id}', [DocumentController::class, 'update'])->name('documents.update');
	Route::put('/documents/{id}/update-file', [DocumentController::class, 'updateFile'])->name('documents.updateFile');
    // Routes for Locations
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
});

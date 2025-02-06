<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;

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

Route::post('/documents/upload-chunk', [DocumentController::class, 'uploadChunk'])->name('documents.upload_chunk');

    
    // Routes for Locations
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');


    Route::middleware('auth')->group(function () {
        // Show the change password form
        Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');

        // Process the password update
        Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');
    });

  // Profile editing routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

});

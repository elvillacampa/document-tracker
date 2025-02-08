<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Middleware\AdminMiddleware;
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

Route::get('/documents/download/{id}', [DocumentController::class, 'downloadFile'])
    ->name('documents.downloadFile');
    
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


Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', AdminMiddleware::class], // using FQCN
], function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::put('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('admin.users.approve');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/{user}/update', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])
        ->name('admin.users.update');
});
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\MedecalRecordController;

// Public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected routes - only accessible with valid token
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/users', UserController::class);
    Route::get('/doctors', [UserController::class, 'index'])->name('doctors');       // List doctors
    Route::get('/patients', [UserController::class, 'index'])->name('patients');       // List patients
    Route::post('/logout', [UserController::class, 'logout']);    // Logout
    
    Route::resource('/appointments', AppointmentController::class);
    Route::resource('/medecal-records', MedecalRecordController::class);
});
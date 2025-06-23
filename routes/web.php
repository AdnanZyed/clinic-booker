<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use Illuminate\Support\Facades\Route;

Auth::routes();

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::resource('appointments', AppointmentController::class);
});

// صلاحيات للمسؤول فقط
Route::middleware(['auth', 'user.type:admin'])->group(function () {
    Route::resource('users', UserController::class);
});

// صلاحيات مشتركة: طبيب أو مسؤول (للسماح بإنشاء وإدارة المواعيد)
Route::middleware(['auth', 'user.type:doctor,admin'])->group(function () {
});
Route::middleware(['auth', 'user.type:doctor'])->group(function () {
    Route::get('/patients', [UserController::class, 'patients'])->name('patients');
    Route::get('patients/{user}', [UserController::class, 'show'])->name('patients.show');
});

// صلاحيات مشتركة: مريض، طبيب، مسؤول لعرض وإنشاء تقاريرهم فقط
Route::middleware(['auth'])->group(function () {
    Route::resource('medical-records', MedicalRecordController::class);
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/notifications/read/{id}', function ($id) {
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->markAsRead();
    return redirect($notification->data['url']);
})->name('notifications.read');

Route::get('/notifications/read-all', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return back();
})->name('notifications.readAll');
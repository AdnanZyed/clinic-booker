<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Auth::routes();

require __DIR__.'/auth.php';

Route::get('/home', function () {
    return redirect()->route('home');
});
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/dashboard/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/dashboard/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/dashboard/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::resource('/dashboard/appointments', AppointmentController::class);
});

// صلاحيات للمسؤول فقط
Route::middleware(['auth', 'user.type:admin'])->group(function () {
    Route::resource('/dashboard/users', UserController::class);
});

// صلاحيات مشتركة: طبيب أو مسؤول (للسماح بإنشاء وإدارة المواعيد)
Route::middleware(['auth', 'user.type:doctor,admin'])->group(function () {
});

Route::middleware(['auth', 'user.type:doctor'])->group(function () {
    Route::get('/dashboard/patients', [UserController::class, 'patients'])->name('patients');
    Route::get('/dashboard/patients{user}', [UserController::class, 'show'])->name('patients.show');
});

// صلاحيات مشتركة: مريض، طبيب، مسؤول لعرض وإنشاء تقاريرهم فقط
Route::middleware(['auth'])->group(function () {
    Route::resource('/dashboard/medical-records', MedicalRecordController::class);
});

// روابط الإشعارات
Route::get('/dashboard/notifications/read/{id}', function ($id) {
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->markAsRead();
    return redirect($notification->data['url']);
})->name('notifications.read');

Route::get('/dashboard/notifications/read-all', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return back();
})->name('notifications.readAll');

Route::get('/dashboard/notifications', function () {
    $notifications = Auth::user()->notifications;
    return view('notifications.index', compact('notifications'));
})->name('notifications.index');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\CreateUser;
use App\Livewire\Settings\Users;
use App\Livewire\Settings\Teams;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file contains all web-accessible routes for your Laravel app.
| Routes are typically protected with 'auth' middleware for logged-in users.
|
*/

// Redirect root → dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

// Everything in this group requires the user to be authenticated
Route::middleware(['auth', 'verified'])->group(function () {
  
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Create & Edit Schedule routes
    Route::get('/schedule/new', [ScheduleController::class, 'create'])->name('schedule.create');
    Route::get('/schedule/{shift}/edit', [ScheduleController::class, 'edit'])->name('schedule.edit');
    Route::post('/schedule', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::put('/schedule/{shift}', [ScheduleController::class, 'update'])->name('schedule.update');
    Route::delete('/schedule/{shift}', [ScheduleController::class, 'destroy'])->name('schedule.destroy');
    Route::post('/schedule/check-office-access', [ScheduleController::class, 'checkOfficeAccess'])->name('schedule.check-office-access');

    Route::get('/settings/profile', Profile::class)->name('settings.profile');
    Route::get('/settings/password', \App\Livewire\Settings\Password::class)->name('settings.password');
    Route::get('/settings/appearance', Appearance::class)->name('settings.appearance');
    
    // Admin-only routes
    Route::get('/settings/notifications', \App\Livewire\Settings\Notifications::class)->name('settings.notifications')->middleware('admin');

    // Admin-only routes
    Route::get('/settings/create-user', \App\Livewire\Settings\CreateUser::class)->name('settings.create_user')->middleware('admin');
    Route::get('/settings/users', \App\Livewire\Settings\Users::class)->name('settings.users')->middleware('admin');
    Route::get('/settings/teams', Teams::class)->name('settings.teams')->middleware('admin');

    // You can add more authenticated routes here later
});

// Auth scaffolding routes (Laravel Breeze/Jetstream/etc.)
require __DIR__ . '/auth.php';
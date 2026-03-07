<?php

declare(strict_types=1);

use App\Http\Controllers\GuestSessionController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::resource('/room', RoomController::class);
});

Route::post('/guest-login', GuestSessionController::class)->name('guest-login');

require __DIR__ . '/settings.php';

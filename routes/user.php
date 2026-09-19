<?php

use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stores/{store:slug}', [StoreController::class, 'show'])->name('stores.show');

    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking:booking_code}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::post('/bookings/{booking}/upload-proof', [BookingController::class, 'uploadProof'])->name('bookings.upload-proof');
    Route::get('/bookings/{booking:booking_code}/ticket', [BookingController::class, 'ticket'])->name('bookings.ticket');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('bookings.review');

    Route::post('/chat/start/{store:slug}', [ChatController::class, 'start'])->name('chat.start');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/{conversation}/poll', [ChatController::class, 'poll'])->name('chat.poll');
});

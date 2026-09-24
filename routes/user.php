<?php

use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\FavoriteController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\StoreController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stores/{store:slug}', [StoreController::class, 'show'])->name('stores.show');

    // Favorit / Wishlist Spot
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{store}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking:booking_code}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::post('/bookings/{booking}/upload-proof', [BookingController::class, 'uploadProof'])->name('bookings.upload-proof');
    Route::post('/bookings/{booking}/upload-remaining-proof', [BookingController::class, 'uploadRemainingProof'])->name('bookings.upload-remaining-proof');
    Route::get('/bookings/{booking:booking_code}/ticket', [BookingController::class, 'ticket'])->name('bookings.ticket');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('bookings.review');

    // Pusat Bantuan / Tiket
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    Route::post('/chat/start/{store:slug}', [ChatController::class, 'start'])->name('chat.start');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/{conversation}/poll', [ChatController::class, 'poll'])->name('chat.poll');
});

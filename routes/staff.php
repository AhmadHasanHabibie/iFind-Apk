<?php

use App\Http\Controllers\Staff\BookingController;
use App\Http\Controllers\Staff\ChatController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\ScanController;
use App\Http\Controllers\Staff\SlotController;
use App\Http\Controllers\Staff\StoreProfileController;
use App\Http\Controllers\Staff\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil toko - TIDAK pakai middleware store.exists (create harus selalu bisa diakses)
    Route::get('/store/create', [StoreProfileController::class, 'create'])->name('store.create');
    Route::post('/store', [StoreProfileController::class, 'store'])->name('store.store');
    Route::get('/store/edit', [StoreProfileController::class, 'edit'])->name('store.edit');
    Route::put('/store', [StoreProfileController::class, 'update'])->name('store.update');
    Route::delete('/store/photos/{photo}', [StoreProfileController::class, 'deletePhoto'])->name('store.photos.delete');
    Route::patch('/store/photos/{photo}/primary', [StoreProfileController::class, 'setPrimaryPhoto'])->name('store.photos.primary');

    // Tiket Bantuan Staf ke Admin (Bisa diakses tanpa harus punya toko)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    Route::middleware('store.exists')->group(function () {
        Route::resource('slots', SlotController::class)->except(['show']);
        Route::post('/slots/bulk-generate', [SlotController::class, 'bulkGenerate'])->name('slots.bulk-generate');
        Route::patch('/slots/{slot}/close', [SlotController::class, 'close'])->name('slots.close');

        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::patch('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
        Route::patch('/bookings/{booking}/refund', [BookingController::class, 'refund'])->name('bookings.refund');

        // Scan QR Check-in
        Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
        Route::post('/scan/check-in', [ScanController::class, 'checkIn'])->name('scan.check-in');

        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{conversation}/send', [ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/{conversation}/poll', [ChatController::class, 'poll'])->name('chat.poll'); // AJAX polling, return JSON
        Route::post('/chat/contact-admin', [ChatController::class, 'contactAdmin'])->name('chat.contact-admin');
        Route::match(['get', 'post'], '/chat/start-user/{user}', [ChatController::class, 'startUserChat'])->name('chat.start-user');
    });
});

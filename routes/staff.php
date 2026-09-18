<?php

use App\Http\Controllers\Staff\BookingController;
use App\Http\Controllers\Staff\ChatController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\SlotController;
use App\Http\Controllers\Staff\StoreProfileController;
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

    Route::middleware('store.exists')->group(function () {
        Route::resource('slots', SlotController::class)->except(['show']);
        Route::post('/slots/bulk-generate', [SlotController::class, 'bulkGenerate'])->name('slots.bulk-generate');
        Route::patch('/slots/{slot}/close', [SlotController::class, 'close'])->name('slots.close');

        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::patch('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{conversation}/send', [ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/{conversation}/poll', [ChatController::class, 'poll'])->name('chat.poll'); // AJAX polling, return JSON
        Route::post('/chat/contact-admin', [ChatController::class, 'contactAdmin'])->name('chat.contact-admin');
    });
});

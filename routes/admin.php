<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\StaffVerificationController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Toko & Pengguna
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/{store}', [StoreController::class, 'show'])->name('stores.show');
    Route::patch('/stores/{store}/toggle-active', [StoreController::class, 'toggleActive'])->name('stores.toggle-active');
    Route::patch('/stores/{store}/approve', [StoreController::class, 'approve'])->name('stores.approve');
    Route::patch('/stores/{store}/reject', [StoreController::class, 'reject'])->name('stores.reject');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Staff Verification
    Route::get('/staff-verification', [StaffVerificationController::class, 'index'])->name('staff-verification.index');
    Route::get('/staff-verification/{user}', [StaffVerificationController::class, 'show'])->name('staff-verification.show');
    Route::post('/staff-verification/{user}/approve', [StaffVerificationController::class, 'approve'])->name('staff-verification.approve');
    Route::post('/staff-verification/{user}/reject', [StaffVerificationController::class, 'reject'])->name('staff-verification.reject');

    // Categories & Facilities CRUD
    Route::resource('categories', CategoryController::class);
    Route::resource('facilities', FacilityController::class);

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');

    // Chat Staf
    Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [\App\Http\Controllers\Admin\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [\App\Http\Controllers\Admin\ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/{conversation}/poll', [\App\Http\Controllers\Admin\ChatController::class, 'poll'])->name('chat.poll');
    Route::post('/chat/start/{staff}', [\App\Http\Controllers\Admin\ChatController::class, 'start'])->name('chat.start');
});

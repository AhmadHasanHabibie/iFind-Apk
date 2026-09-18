<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StaffVerificationController;
use App\Http\Controllers\Admin\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Staff Verification
    Route::get('/staff-verification', [StaffVerificationController::class, 'index'])->name('staff-verification.index');
    Route::get('/staff-verification/{user}', [StaffVerificationController::class, 'show'])->name('staff-verification.show');
    Route::post('/staff-verification/{user}/approve', [StaffVerificationController::class, 'approve'])->name('staff-verification.approve');
    Route::post('/staff-verification/{user}/reject', [StaffVerificationController::class, 'reject'])->name('staff-verification.reject');

    // Categories CRUD
    Route::resource('categories', CategoryController::class);

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

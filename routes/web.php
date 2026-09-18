<?php

use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Quick redirect helper
    Route::get('/dashboard', function () {
        return match(Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    })->name('dashboard');

    // Role-specific Dashboards
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/staff/dashboard', [DashboardController::class, 'staff'])->name('staff.dashboard');
    Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');

    // Admin Features: Manajemen Staf Toko
    Route::get('/admin/staff', [AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::post('/admin/staff', [AdminStaffController::class, 'store'])->name('admin.staff.store');
    Route::delete('/admin/staff/{id}', [AdminStaffController::class, 'destroy'])->name('admin.staff.destroy');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

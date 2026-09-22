<?php

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
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    $stores = \App\Models\Store::where('is_active', true)
        ->where('status', 'approved')
        ->with(['photos', 'category', 'slots'])
        ->withCount('reviews')
        ->latest()
        ->take(9)
        ->get();

    return view('welcome', compact('stores'));
})->name('home');

// Role-based dashboard redirect helper
Route::middleware('auth')->get('/dashboard', function () {
    $user = Auth::user();
    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'staff' => redirect()->route('staff.dashboard'),
        default => redirect()->route('user.dashboard'),
    };
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Authentication routes (Breeze)
require __DIR__.'/auth.php';

// Role routes
require __DIR__.'/admin.php';
require __DIR__.'/staff.php';
require __DIR__.'/user.php';

// Custom Error Pages Preview (Development / Testing)
Route::get('/preview-error/{code?}', function ($code = null) {
    if (!$code) {
        return view('errors.index');
    }

    $validCodes = ['400', '401', '402', '403', '404', '419', '429', '500', '502', '503', '504', '506'];
    if (in_array((string)$code, $validCodes) && view()->exists("errors.{$code}")) {
        $messages = [
            '400' => 'Permintaan yang dikirimkan tidak memenuhi spesifikasi server.',
            '401' => 'Sesi login tidak valid atau kredensial autentikasi belum disediakan.',
            '402' => 'Sistem mendeteksi reservasi menunggu konfirmasi pembayaran aktif.',
            '403' => 'Hak akses dibatasi untuk tingkat otoritas akun Anda.',
            '404' => 'Halaman atau spot reservasi tidak ditemukan di direktori i-Find.',
            '419' => 'Token keamanan form (CSRF) kedaluwarsa karena lama tidak berinteraksi.',
            '429' => 'Terlalu banyak request dikirim dalam waktu singkat (Rate limit reached).',
            '500' => 'Simulasi pengecualian internal server error pada sistem backend.',
            '502' => 'Server gateway gagal berkomunikasi dengan cluster upstream.',
            '503' => 'Sistem sedang dalam mode pemeliharaan berkala (Maintenance mode).',
            '504' => 'Waktu tunggu respon server gateway habis melampaui timeout default.',
            '506' => 'Terjadi siklus negosiasi varian konfigurasi internal (Variant Also Negotiates).'
        ];

        $exception = new \Symfony\Component\HttpKernel\Exception\HttpException(
            (int)$code,
            $messages[$code] ?? "Simulasi pesan error untuk kode status {$code}."
        );

        return response()->view("errors.{$code}", compact('exception'), (int)$code);
    }

    abort(404, "Halaman pratinjau error untuk kode {$code} tidak ditemukan.");
})->name('preview.error');


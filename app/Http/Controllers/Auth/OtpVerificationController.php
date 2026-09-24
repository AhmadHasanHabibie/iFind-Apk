<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    /**
     * Tampilkan halaman input kode OTP
     */
    public function show(Request $request): View|RedirectResponse
    {
        $userId = session('verify_user_id');

        if (! $userId) {
            return redirect()->route('register')
                ->with('error', 'Sesi verifikasi tidak ditemukan. Silakan lakukan registrasi.');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('register');
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')
                ->with('status', 'Email Anda sudah terverifikasi. Silakan login.');
        }

        // Mask email untuk keamanan (contoh: ahm***@gmail.com)
        $maskedEmail = Str::mask($user->email, '*', 3, strpos($user->email, '@') - 3);

        return view('auth.verify-otp', [
            'user' => $user,
            'maskedEmail' => $maskedEmail,
        ]);
    }

    /**
     * Proses verifikasi kode OTP dari user
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP 6-digit wajib dimasukkan.',
            'otp.size' => 'Kode OTP harus tepat 6 digit angka.',
        ]);

        $userId = session('verify_user_id');

        if (! $userId) {
            return redirect()->route('register')
                ->with('error', 'Sesi verifikasi berakhir. Silakan daftar kembali.');
        }

        $user = User::findOrFail($userId);

        // Cek kedaluwarsa OTP
        if (empty($user->otp_expires_at) || Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'Kode OTP telah kedaluwarsa. Silakan klik "Kirim Ulang Kode OTP".',
            ])->withInput();
        }

        // Cek kecocokan OTP
        if ($user->otp_code !== trim($request->otp)) {
            return back()->withErrors([
                'otp' => 'Kode OTP salah. Pastikan Anda memasukkan kode terbaru dari kotak masuk Gmail.',
            ])->withInput();
        }

        // Verifikasi berhasil
        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // Hapus sesi verifikasi
        session()->forget('verify_user_id');

        if ($user->role === 'staff') {
            return redirect()->route('login')
                ->with('status', 'Email berhasil diverifikasi! Akun Staf Toko Anda kini sedang menunggu verifikasi/persetujuan oleh Admin.');
        }

        Auth::login($user);

        return redirect()->route('user.dashboard')
            ->with('success', "Selamat datang di i-Find, {$user->name}! Akun Anda telah berhasil diverifikasi.");
    }

    /**
     * Kirim ulang kode OTP baru ke Gmail
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = session('verify_user_id');

        if (! $userId) {
            return redirect()->route('register')
                ->with('error', 'Sesi verifikasi berakhir. Silakan daftar kembali.');
        }

        $user = User::findOrFail($userId);

        // Generate 6 digit OTP baru
        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed sending resend OTP mail: ' . $e->getMessage());
        }

        return back()->with('success', 'Kode OTP baru telah dikirim ke Gmail Anda. Silakan periksa inbox / spam.');
    }
}

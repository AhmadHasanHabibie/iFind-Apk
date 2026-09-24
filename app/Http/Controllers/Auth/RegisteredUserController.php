<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'in:user,staff'],
        ]);

        $role = $request->input('role', 'user');
        $isStaff = ($role === 'staff');

        // Generate 6 digit OTP
        $otp = (string) random_int(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $role,
            'is_active' => !$isStaff,
            'verification_status' => $isStaff ? 'pending' : null,
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        event(new Registered($user));

        // Kirim email OTP via Mail
        try {
            Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));
        } catch (\Throwable $e) {
            Log::error('Failed sending OTP email: ' . $e->getMessage());
        }

        // Simpan id user di sesi verifikasi
        session(['verify_user_id' => $user->id]);

        return redirect()->route('otp.verify.show')
            ->with('success', "Kode OTP verifikasi telah dikirim ke {$user->email}. Silakan cek kotak masuk Gmail Anda.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna atau admin.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            return match($user->role) {
                'admin' => redirect()->route('admin.dashboard')->with('success', "Selamat datang di Panel Administrator, {$user->name}."),
                'staff' => redirect()->route('staff.dashboard')->with('success', "Selamat datang di Panel Staf Toko, {$user->name}."),
                default => redirect()->route('user.dashboard')->with('success', "Selamat datang kembali di i-Find, {$user->name}."),
            };
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ]);
    }

    /**
     * Tampilkan formulir registrasi untuk user/siswa baru.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi akun baru (otomatis role: user).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'institution' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8'],
            'terms' => ['accepted'],
        ], [
            'first_name.required' => 'Nama depan wajib diisi.',
            'last_name.required' => 'Nama belakang wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau langsung masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'terms.accepted' => 'Anda harus menyetujui Ketentuan Layanan & Kebijakan Privasi.',
        ]);

        $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

        // Akun register publik SELALU ditetapkan role 'user' demi keamanan
        $user = User::create([
            'name' => $fullName,
            'email' => $validated['email'],
            'institution' => $validated['institution'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // Otomatis login setelah berhasil mendaftar
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard')
            ->with('success', "Pendaftaran berhasil! Selamat datang di i-Find, {$user->name}.");
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Anda telah berhasil keluar.');
    }
}

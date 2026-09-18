<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminStaffController extends Controller
{
    /**
     * Tampilkan halaman daftar dan form penambahan staf toko.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $staffs = User::where('role', 'staff')->latest()->get();

        return view('admin.staff', compact('user', 'staffs'));
    }

    /**
     * Simpan data staf toko baru (otomatis role: staff).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'store_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'store_name.required' => 'Nama toko mitra wajib diisi.',
            'email.required' => 'Alamat email staf wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar sebagai pengguna lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'institution' => $validated['store_name'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', "Akun staf toko '{$validated['name']}' untuk mitra '{$validated['store_name']}' berhasil dibuat.");
    }

    /**
     * Hapus akun staf toko.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $staff = User::where('role', 'staff')->findOrFail($id);
        $name = $staff->name;
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', "Akun staf toko '{$name}' berhasil dihapus dari sistem.");
    }
}

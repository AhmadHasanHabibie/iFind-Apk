<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $query = User::where('role', 'staff');

        if ($status !== 'all') {
            $query->where('verification_status', $status);
        }

        $staffList = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'pending' => User::where('role', 'staff')->where('verification_status', 'pending')->count(),
            'approved' => User::where('role', 'staff')->where('verification_status', 'approved')->count(),
            'rejected' => User::where('role', 'staff')->where('verification_status', 'rejected')->count(),
            'all' => User::where('role', 'staff')->count(),
        ];

        return view('admin.staff-verification.index', compact('staffList', 'status', 'counts'));
    }

    public function show(User $user): View
    {
        if ($user->role !== 'staff') {
            abort(404, 'Data staf tidak ditemukan.');
        }

        $user->load('store');

        return view('admin.staff-verification.show', compact('user'));
    }

    public function approve(User $user): RedirectResponse
    {
        if ($user->role !== 'staff') {
            abort(403, 'Aksi tidak valid untuk pengguna ini.');
        }

        $user->update([
            'is_active' => true,
            'verification_status' => 'approved',
            'verification_note' => null,
            'verified_at' => now(),
        ]);

        return redirect()->route('admin.staff-verification.index')
            ->with('success', "Akun staf {$user->name} berhasil disetujui dan diaktifkan.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'staff') {
            abort(403, 'Aksi tidak valid untuk pengguna ini.');
        }

        $request->validate([
            'verification_note' => ['required', 'string', 'min:5'],
        ], [
            'verification_note.required' => 'Alasan penolakan wajib diisi.',
            'verification_note.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $user->update([
            'is_active' => false,
            'verification_status' => 'rejected',
            'verification_note' => $request->verification_note,
            'verified_at' => now(),
        ]);

        return redirect()->route('admin.staff-verification.index')
            ->with('success', "Pendaftaran staf {$user->name} telah ditolak.");
    }
}

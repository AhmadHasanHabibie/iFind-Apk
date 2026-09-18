<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk Role Admin.
     */
    public function admin()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $totalStaff = User::where('role', 'staff')->count();

        return view('admin.dashboard', compact('user', 'totalStaff'));
    }

    /**
     * Dashboard untuk Role Staff Toko.
     */
    public function staff()
    {
        $user = Auth::user();
        if ($user->role !== 'staff' && $user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Staf Toko mitra.');
        }

        return view('staff.dashboard', compact('user'));
    }

    /**
     * Dashboard untuk Role User (Siswa/Pelajar).
     */
    public function user()
    {
        $user = Auth::user();
        return view('user.dashboard', compact('user'));
    }
}

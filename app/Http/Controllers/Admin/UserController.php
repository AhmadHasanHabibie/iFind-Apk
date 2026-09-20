<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->get('role', 'user');
        $search = $request->get('search');

        $query = User::latest();

        if ($role && in_array($role, ['user', 'staff', 'admin'])) {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => User::count(),
            'user' => User::where('role', 'user')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'role', 'search', 'counts'));
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->role === 'admin' && $user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan / diblokir';

        return back()->with('success', "Akun pengguna '{$user->name}' berhasil {$statusText}.");
    }
}

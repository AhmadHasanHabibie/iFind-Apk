@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-xs">
                    <i class="fa-solid fa-users"></i>
                </span>
                Manajemen Pengguna
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data pelanggan, staf toko, dan administrator terdaftar di platform iFind.</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <!-- Role Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'user'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $role === 'user' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                Pelanggan ({{ $counts['user'] }})
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'staff'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $role === 'staff' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                Staf Toko ({{ $counts['staff'] }})
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $role === 'admin' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                Admin ({{ $counts['admin'] }})
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => ''])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !$role ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                Semua ({{ $counts['all'] }})
            </a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
            @if($role)
                <input type="hidden" name="role" value="{{ $role }}">
            @endif
            <div class="relative flex-1 sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/email/telepon..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-hidden">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        @if($users->isEmpty())
            <div class="p-12 text-center">
                <p class="text-sm text-slate-500">Tidak ada pengguna yang sesuai.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-6">Nama Pengguna</th>
                            <th class="py-4 px-6">Role</th>
                            <th class="py-4 px-6">Kontak</th>
                            <th class="py-4 px-6">Status Akun</th>
                            <th class="py-4 px-6">Terdaftar</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($user->role === 'admin')
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Admin
                                        </span>
                                    @elseif($user->role === 'staff')
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Staf Toko
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                            Customer
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    {{ $user->phone ?: '-' }}
                                </td>
                                <td class="py-4 px-6">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Diblokir
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-400">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun {{ $user->name }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $user->is_active ? 'bg-rose-50 text-rose-600 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} transition">
                                                {{ $user->is_active ? 'Blokir' : 'Buka Blokir' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
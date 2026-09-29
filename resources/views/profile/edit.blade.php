@php
    $role = auth()->user()?->role ?? 'user';
    $layout = match($role) {
        'admin' => 'layouts.admin',
        'staff' => 'layouts.staff',
        default => 'layouts.user',
    };
@endphp

@extends($layout)

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'profile' }">
    <!-- Header Banner / Hero Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <!-- User Avatar / Initials -->
                <div class="relative">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-2xl sm:text-3xl flex items-center justify-center shadow-lg shadow-blue-500/25 ring-4 ring-blue-50">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center shadow-xs" title="Akun Aktif">
                        <i class="fa-solid fa-check text-[10px] text-white"></i>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        @if($user->role === 'admin')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-purple-50 border border-purple-200 text-purple-700">Administrator</span>
                        @elseif($user->role === 'staff')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 border border-amber-200 text-amber-700">Mitra Staf Toko</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-50 border border-blue-200 text-blue-700">
                                <i class="fa-solid fa-graduation-cap mr-1"></i> Student Customer
                            </span>
                        @endif

                        @if($user->hasVerifiedEmail())
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 border border-emerald-200 text-emerald-700">
                                <i class="fa-solid fa-circle-check mr-1"></i> Terverifikasi
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 border border-amber-200 text-amber-700">
                                <i class="fa-solid fa-clock mr-1"></i> Belum Verifikasi
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 flex items-center gap-2">
                        <span><i class="fa-regular fa-envelope text-slate-400 mr-1"></i> {{ $user->email }}</span>
                        @if($user->phone)
                            <span>&bull;</span>
                            <span><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $user->phone }}</span>
                        @endif
                    </p>
                    <p class="text-[11px] text-slate-400">
                        <i class="fa-regular fa-calendar mr-1"></i> Bergabung sejak {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}
                    </p>
                </div>
            </div>

            <!-- Quick Action / Link back to Dashboard -->
            <div class="flex items-center gap-2 self-end md:self-center">
                @if($user->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        <i class="fa-solid fa-arrow-left"></i> Dashboard Admin
                    </a>
                @elseif($user->role === 'staff')
                    <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        <i class="fa-solid fa-arrow-left"></i> Dashboard Staf
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        <i class="fa-solid fa-compass"></i> Jelajah Spot
                    </a>
                    <a href="{{ route('user.bookings.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition">
                        <i class="fa-solid fa-calendar-check"></i> Pesanan Saya
                    </a>
                @endif
            </div>
        </div>

        @if($user->role === 'user')
            <!-- Student Stats Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-100">
                <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Booking</p>
                        <p class="text-base font-black text-slate-900">{{ $user->bookings()->count() }}</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100/70 text-rose-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Spot Favorit</p>
                        <p class="text-base font-black text-slate-900">{{ $user->favorites()->count() }}</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100/70 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ulasan Toko</p>
                        <p class="text-base font-black text-slate-900">{{ $user->reviews()->count() }}</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100/70 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiket Bantuan</p>
                        <p class="text-base font-black text-slate-900">{{ $user->tickets()->count() }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Grid: Sidebar Anchor Navigation on Left + Content Sections on Right -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Sidebar Navigation / Quick Anchors -->
        <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-24">
            <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-sm space-y-2">
                <p class="text-xs font-black text-slate-400 uppercase tracking-wider px-3 mb-2">Navigasi Pengaturan</p>

                <a href="#informasi-profil"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold text-slate-700 hover:text-blue-600 hover:bg-blue-50/70 transition">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <p class="text-slate-900 font-bold">Informasi Profil</p>
                        <p class="text-[11px] text-slate-400 font-normal">Nama, email & kontak</p>
                    </div>
                </a>

                <a href="#ganti-password"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold text-slate-700 hover:text-blue-600 hover:bg-blue-50/70 transition">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <p class="text-slate-900 font-bold">Kata Sandi</p>
                        <p class="text-[11px] text-slate-400 font-normal">Perbarui keamanan password</p>
                    </div>
                </a>

                <a href="#hapus-akun"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold text-slate-700 hover:text-rose-600 hover:bg-rose-50/70 transition">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-rose-700 font-bold">Zona Bahaya</p>
                        <p class="text-[11px] text-slate-400 font-normal">Hapus akun secara permanen</p>
                    </div>
                </a>
            </div>

            <!-- Helpful Tips Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-6 text-white shadow-md shadow-blue-500/20 space-y-3">
                <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-base">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h4 class="text-sm font-black tracking-tight">Keamanan Akun Siswa</h4>
                <p class="text-xs text-blue-100 leading-relaxed">
                    Pastikan nomor WhatsApp dan email Anda selalu aktif untuk menerima e-tiket reservasi, QR code check-in, dan informasi promo nongkrong hemat di i-Find.
                </p>
            </div>
        </div>

        <!-- Right Side Sections -->
        <div class="lg:col-span-8 space-y-8">
            <!-- 1. Profile Information Card -->
            <div id="informasi-profil" class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-sm scroll-mt-28">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- 2. Update Password Card -->
            <div id="ganti-password" class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-sm scroll-mt-28">
                @include('profile.partials.update-password-form')
            </div>

            <!-- 3. Delete Account Card -->
            <div id="hapus-akun" class="bg-white rounded-3xl border border-rose-200/80 p-6 sm:p-8 shadow-sm scroll-mt-28">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection

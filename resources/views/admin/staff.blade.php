<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Staf Toko — Admin i-Find</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full font-sans bg-slate-50 text-slate-800 antialiased flex flex-col md:flex-row">

    <!-- Mobile Topbar & Hamburger -->
    <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-black text-base text-white shadow-sm shadow-blue-500/30">i</div>
            <span class="text-xl font-extrabold tracking-tight text-slate-900">i-Find</span>
            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">Admin</span>
        </div>
        <button type="button" id="mobile-menu-toggle" aria-label="Buka Menu Sidebar" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Drawer Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden md:hidden"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/90 flex flex-col justify-between transition-transform duration-300 -translate-x-full md:translate-x-0 md:static md:h-screen md:sticky md:top-0">
        <div>
            <!-- Sidebar Header / Brand -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100">
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-black text-xl shadow-md shadow-blue-500/20 text-white group-hover:scale-105 transition-transform">i</div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-extrabold tracking-tight text-slate-900">i-Find</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider -mt-1">Administrator Hub</span>
                    </div>
                </a>
                <button type="button" id="mobile-menu-close" class="md:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Role Badge Banner -->
            <div class="px-6 py-4">
                <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl p-3 flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        AD
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-indigo-900 truncate">{{ $user->name }}</p>
                        <p class="text-[10px] font-semibold text-indigo-600 uppercase tracking-wide">Akses: Super Admin</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 space-y-1 mt-2">
                <div class="px-3 pb-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Menu Utama</p>
                </div>

                <!-- 1. Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="group flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Tambah Staf Toko (Active) -->
                <a href="{{ route('admin.staff.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-blue-50 text-blue-600 shadow-sm shadow-blue-500/5 transition-all">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Tambah Staf Toko</span>
                </a>

                <!-- 3. Tambah Toko -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Tambah Toko</span>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">Segera</span>
                </a>

                <div class="px-3 pt-4 pb-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Transaksi & Percakapan</p>
                </div>

                <!-- 4. Booking Order -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span>Booking Order</span>
                    </div>
                </a>

                <!-- 5. Riwayat Booking -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Riwayat Booking</span>
                    </div>
                </a>

                <!-- 6. Chat Box -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Chat Box</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Staff Direct</span>
                </a>

                <div class="px-3 pt-4 pb-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Pengaturan</p>
                </div>

                <!-- 7. Profil Admin -->
                <a href="#" class="group flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Profil Admin</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center justify-between mb-3 px-2">
                <a href="/" class="text-xs font-semibold text-slate-500 hover:text-blue-600 flex items-center gap-1 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Lihat Website
                </a>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 text-xs font-bold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Header Topbar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 px-6 sm:px-8 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen Staf Toko</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftarkan akun staf pengelola operasional untuk masing-masing spot toko mitra</p>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </header>

        <!-- Body Content -->
        <div class="p-6 sm:p-8 space-y-8 max-w-7xl">
            
            <!-- Toast Success Notification -->
            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Global Error Validation Alert -->
            @if (isset($errors) && $errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs leading-relaxed">
                    <div class="font-bold mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Gagal Menyimpan Data Staf:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-600 ml-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 2-Column Grid: Left (Form Tambah), Right (Daftar Staf) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- LEFT COLUMN: Form Tambah Staf Toko (5 Cols) -->
                <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-sm">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                            +
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 tracking-tight">Formulir Tambah Staf</h2>
                            <p class="text-xs text-slate-500">Akun akan langsung aktif dengan role Staf Toko</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Nama Lengkap Staf -->
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Nama Lengkap Staf <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   required 
                                   placeholder="Contoh: Budi Pratama"
                                   class="w-full bg-slate-50 border @error('name') border-rose-300 ring-1 ring-rose-500 @else border-slate-200 @enderror text-slate-900 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all placeholder:text-slate-400">
                        </div>

                        <!-- Toko Mitra / Cabang -->
                        <div>
                            <label for="store_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Nama Toko Mitra / Cabang <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="store_name" 
                                   id="store_name" 
                                   value="{{ old('store_name') }}"
                                   required 
                                   placeholder="Contoh: Kopi Titik Temu Cabang 1"
                                   class="w-full bg-slate-50 border @error('store_name') border-rose-300 ring-1 ring-rose-500 @else border-slate-200 @enderror text-slate-900 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all placeholder:text-slate-400">
                        </div>

                        <!-- Alamat Email Staf -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Alamat Email Login <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}"
                                   required 
                                   placeholder="staf@kopititiktemu.id"
                                   class="w-full bg-slate-50 border @error('email') border-rose-300 ring-1 ring-rose-500 @else border-slate-200 @enderror text-slate-900 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all placeholder:text-slate-400">
                        </div>

                        <!-- Kata Sandi Akun -->
                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       name="password" 
                                       id="staff-password" 
                                       required 
                                       placeholder="Minimal 8 karakter"
                                       class="w-full bg-slate-50 border @error('password') border-rose-300 ring-1 ring-rose-500 @else border-slate-200 @enderror text-slate-900 rounded-xl px-4 py-2.5 pr-11 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all placeholder:text-slate-400">
                                <button type="button" 
                                        id="toggle-staff-pwd" 
                                        aria-label="Tampilkan atau sembunyikan kata sandi"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eye-slash-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl py-3 shadow-lg shadow-blue-600/20 hover:-translate-y-0.5 active:scale-95 transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan & Buat Akun Staf</span>
                        </button>
                    </form>
                </div>

                <!-- RIGHT COLUMN: Tabel Daftar Staf Toko (7 Cols) -->
                <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-sm">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 tracking-tight">Daftar Akun Staf Toko</h2>
                            <p class="text-xs text-slate-500">Seluruh staf yang memiliki akses ke dashboard mitra</p>
                        </div>
                        <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full">
                            {{ $staffs->count() }} Staf Terdaftar
                        </span>
                    </div>

                    @if ($staffs->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">Belum ada akun staf toko</p>
                            <p class="text-xs text-slate-500 mt-1">Gunakan formulir di sebelah kiri untuk menambahkan staf toko pertama.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="text-[11px] font-bold uppercase text-slate-400 bg-slate-50/80 border-y border-slate-100">
                                    <tr>
                                        <th class="py-3 px-3">Nama & Email</th>
                                        <th class="py-3 px-3">Toko Mitra</th>
                                        <th class="py-3 px-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($staffs as $staff)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-3.5 px-3">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                                                        {{ strtoupper(substr($staff->name, 0, 2)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $staff->name }}</p>
                                                        <p class="text-[11px] text-slate-500 truncate">{{ $staff->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-3">
                                                <span class="inline-block text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                                                    {{ $staff->institution ?? 'Mitra Spot' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-3 text-right">
                                                <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus akun staf {{ $staff->name }}?" data-confirm-title="Hapus Akun Staf" data-confirm-btn="Ya, Hapus" data-confirm-danger="true" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition" title="Hapus Staf">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </main>

    <!-- Mobile Drawer & Password Toggle Script -->
    <script>
        // Drawer toggle
        const toggleBtn = document.getElementById('mobile-menu-toggle');
        const closeBtn = document.getElementById('mobile-menu-close');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        // Password visibility toggle
        const togglePassword = document.getElementById('toggle-staff-pwd');
        const passwordInput = document.getElementById('staff-password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeSlashIcon = document.getElementById('eye-slash-icon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeIcon.classList.toggle('hidden', isPassword);
                eyeSlashIcon.classList.toggle('hidden', !isPassword);
            });
        }
    </script>
    <x-sweetalert-notifications />
</body>
</html>

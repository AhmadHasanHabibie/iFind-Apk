<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staf Toko Dashboard — i-Find</title>
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
            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">Staf Toko</span>
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
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider -mt-1">Mitra Toko Portal</span>
                    </div>
                </a>
                <button type="button" id="mobile-menu-close" class="md:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Shop / Staff Info Badge -->
            <div class="px-6 py-4">
                <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-3 flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        TK
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ $user->institution ?? 'Kopi Titik Temu' }}</p>
                        <p class="text-[10px] font-medium text-blue-600 truncate">{{ $user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 space-y-1 mt-2">
                <div class="px-3 pb-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Menu Toko</p>
                </div>

                <!-- 1. Dashboard (Active) -->
                <a href="{{ route('staff.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-blue-50 text-blue-600 shadow-sm shadow-blue-500/5 transition-all">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Atur Toko -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Atur Toko</span>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">Segera</span>
                </a>

                <!-- 3. Chat Box (Terintegrasi Admin) -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Chat Box</span>
                    </div>
                    <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Admin Link</span>
                </a>

                <div class="px-3 pt-4 pb-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Kelola Reservasi</p>
                </div>

                <!-- 4. Booking Order -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span>Booking Order</span>
                    </div>
                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">3 Baru</span>
                </a>

                <!-- 5. Riwayat Booking -->
                <a href="#" class="group flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Riwayat Booking</span>
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
                    Halaman Depan
                </a>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Toko Aktif</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 text-xs font-bold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar Sesi</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Header Topbar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 px-6 sm:px-8 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Dashboard Operasional Toko</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola meja, booking pelajar, dan koordinasi dengan admin</p>
            </div>

            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Spot Siap Menerima Booking
                </span>
            </div>
        </header>

        <!-- Body Dashboard Content -->
        <div class="p-6 sm:p-8 space-y-8 max-w-7xl">
            
            <!-- Alert Banner Success (if any) -->
            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 4 Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Ketersediaan Meja -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Spot / Meja</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900">14 <span class="text-sm font-medium text-slate-400">/ 20 Meja</span></p>
                    <p class="text-xs text-blue-600 font-semibold mt-1">Tersedia untuk reservasi</p>
                </div>

                <!-- Card 2: Permintaan Booking Baru -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perlu Konfirmasi</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900">3</p>
                    <p class="text-xs text-amber-600 font-semibold mt-1">Siswa menunggu konfirmasi</p>
                </div>

                <!-- Card 3: Total Kunjungan Hari Ini -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selesai Hari Ini</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900">19</p>
                    <p class="text-xs text-emerald-600 font-semibold mt-1">Kunjungan siswa terselesaikan</p>
                </div>

                <!-- Card 4: Rating Toko -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rating Toko</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900">4.9</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">Dari 86 review pelajar</p>
                </div>
            </div>

            <!-- Booking Inbound & Shop Quick Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Table: Permintaan Booking Siswa -->
                <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Booking Masuk Menunggu Konfirmasi</h2>
                            <p class="text-xs text-slate-500">Konfirmasi reservasi meja agar siswa dapat langsung datang</p>
                        </div>
                        <span class="text-xs font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">Perlu Tindakan</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs font-bold uppercase text-slate-400 bg-slate-50/70 border-y border-slate-100">
                                <tr>
                                    <th class="py-3 px-4">Nama Siswa</th>
                                    <th class="py-3 px-4">Jumlah Orang</th>
                                    <th class="py-3 px-4">Waktu Reservasi</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        Rifqi Siswa<br>
                                        <span class="text-xs font-normal text-slate-500">SMK Negeri 1</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">3 Orang</td>
                                    <td class="py-3.5 px-4 text-slate-600 text-xs font-medium">Hari Ini, 16:00 WIB</td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex gap-2">
                                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition">
                                                Terima
                                            </button>
                                            <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        Dimas Setiawan<br>
                                        <span class="text-xs font-normal text-slate-500">SMA Negeri 2</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">2 Orang</td>
                                    <td class="py-3.5 px-4 text-slate-600 text-xs font-medium">Hari Ini, 17:30 WIB</td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex gap-2">
                                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition">
                                                Terima
                                            </button>
                                            <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Side Panel: Status Toko & Chat Admin -->
                <div class="space-y-6">
                    <!-- Shop Summary Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-900 tracking-tight">Status Profil Spot Toko</h3>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        </div>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Alamat: Jl. Hangout Pelajar No. 12, Jakarta Timur.
                        </p>
                        <div class="space-y-2.5 text-xs text-slate-600 border-t border-slate-100 pt-4">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Jam Buka:</span>
                                <span class="font-bold text-slate-800">10:00 - 22:00 WIB</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Paket Pelajar:</span>
                                <span class="font-bold text-emerald-600">&lt; Rp 25.000 / porsi</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Fasilitas:</span>
                                <span class="font-semibold text-slate-800">Wi-Fi, AC, Stopkontak Meja</span>
                            </div>
                        </div>

                        <button type="button" class="w-full mt-5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-2.5 rounded-xl text-xs transition">
                            Buka Menu Atur Toko
                        </button>
                    </div>

                    <!-- Direct Chat with Admin -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 shadow-sm">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Chat Box dengan Admin</h4>
                                <p class="text-[11px] text-slate-500">Hubungi pengelola sistem jika butuh bantuan</p>
                            </div>
                        </div>
                        <button type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-xs shadow-md shadow-indigo-500/10 transition">
                            Mulai Percakapan
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <!-- Mobile Drawer Script -->
    <script>
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
    </script>
</body>
</html>

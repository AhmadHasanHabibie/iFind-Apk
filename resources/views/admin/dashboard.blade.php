<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — i-Find</title>
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

                <!-- 1. Dashboard (Active) -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-blue-50 text-blue-600 shadow-sm shadow-blue-500/5 transition-all">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Tambah Staf Toko -->
                <a href="{{ route('admin.staff.index') }}" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Tambah Staf Toko</span>
                    </div>
                    <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Kelola</span>
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
                    <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Aktif</span>
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

                <!-- 6. Chat Box (Admin-Staff) -->
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
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Dashboard Administrator</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pemantauan data operasional platform reservasi spot i-Find</p>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden sm:flex items-center gap-2 bg-slate-100/80 px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ date('d M Y') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-sm text-slate-700">
                    {{ substr($user->name, 0, 1) }}
                </div>
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
                <!-- Card 1: Total Toko Aktif -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm hover:border-blue-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Toko</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900">24</p>
                    <p class="text-xs text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                        <span>↑ 4 mitra baru</span> bulan ini
                    </p>
                </div>

                <!-- Card 2: Total Staf Toko -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm hover:border-indigo-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Staf Toko</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900">{{ $totalStaff ?? 1 }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">Terverifikasi di sistem</p>
                </div>

                <!-- Card 3: Booking Order Masuk -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm hover:border-emerald-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Booking Hari Ini</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900">142</p>
                    <p class="text-xs text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                        <span>↑ 18% dari kemarin</span>
                    </p>
                </div>

                <!-- Card 4: Pengguna Pelajar -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm hover:border-sky-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">User Siswa</span>
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-900">1,280</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">Pelajar terdaftar aktif</p>
                </div>
            </div>

            <!-- Action Placeholders & Table Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Table: Booking Order Real-Time -->
                <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Pantauan Booking Terkini</h2>
                            <p class="text-xs text-slate-500">Daftar reservasi spot yang masuk di seluruh mitra toko</p>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Live Feed</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs font-bold uppercase text-slate-400 bg-slate-50/70 border-y border-slate-100">
                                <tr>
                                    <th class="py-3 px-4">Kode / Siswa</th>
                                    <th class="py-3 px-4">Toko Hangout</th>
                                    <th class="py-3 px-4">Jadwal & Meja</th>
                                    <th class="py-3 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        #BK-9021<br>
                                        <span class="text-xs font-normal text-slate-500">Ahmad Habibie</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">Kopi Titik Temu</td>
                                    <td class="py-3.5 px-4 text-slate-600 text-xs">Hari Ini, 16:30<br><span class="font-semibold text-blue-600">Meja 04 (4 Org)</span></td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Dikonfirmasi
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        #BK-9020<br>
                                        <span class="text-xs font-normal text-slate-500">Farhan Fauzi</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">Ruang Sela Cafe</td>
                                    <td class="py-3.5 px-4 text-slate-600 text-xs">Hari Ini, 18:00<br><span class="font-semibold text-blue-600">Meja 12 (2 Org)</span></td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Konfirmasi
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        #BK-9019<br>
                                        <span class="text-xs font-normal text-slate-500">Nabila Putri</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">Taman Literasi Hub</td>
                                    <td class="py-3.5 px-4 text-slate-600 text-xs">Kemarin, 14:00<br><span class="font-semibold text-slate-500">Meja Belajar 01</span></td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                            Selesai
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Side Panel: Quick Access & Chat Overview -->
                <div class="space-y-6">
                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 shadow-sm">
                        <h3 class="text-base font-bold text-slate-900 mb-4 tracking-tight">Aksi Cepat Admin</h3>
                        <div class="space-y-3">
                            <button type="button" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-blue-50/70 border border-slate-200/80 hover:border-blue-200 text-left transition group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                        +
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition">Buat Toko Baru</p>
                                        <p class="text-[11px] text-slate-500">Tambah data spot mitra baru</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            <a href="{{ route('admin.staff.index') }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-indigo-50/70 border border-slate-200/80 hover:border-indigo-200 text-left transition group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                        ST
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition">Buat Akun Staf Toko</p>
                                        <p class="text-[11px] text-slate-500">Daftarkan pengelola toko</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Chat Box Integration Widget -->
                    <div class="bg-gradient-to-br from-slate-900 to-blue-950 rounded-3xl p-6 text-white shadow-xl shadow-slate-900/10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold text-blue-300 uppercase tracking-wider">Chat Box Terintegrasi</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        </div>
                        <h4 class="text-base font-bold">Saluran Komunikasi Admin & Staf</h4>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Terhubung langsung dengan seluruh staf operasional mitra toko secara real-time.
                        </p>
                        <div class="mt-5 pt-4 border-t border-white/10 flex items-center justify-between">
                            <span class="text-xs text-slate-300">2 pesan belum dibaca</span>
                            <button type="button" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition shadow-sm">
                                Buka Chat Box
                            </button>
                        </div>
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

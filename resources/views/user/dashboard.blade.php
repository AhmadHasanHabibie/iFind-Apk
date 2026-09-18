<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelajar — i-Find</title>
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
            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200">Siswa</span>
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
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider -mt-1">Student Hangout Area</span>
                    </div>
                </a>
                <button type="button" id="mobile-menu-close" class="md:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Student Profile Badge -->
            <div class="px-6 py-4">
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3 flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ $user->name }}</p>
                        <p class="text-[10px] font-medium text-slate-500 truncate">{{ $user->institution ?? 'Pelajar Umum' }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 space-y-1 mt-2">
                <div class="px-3 pb-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Jelajah & Reservasi</p>
                </div>

                <!-- 1. Dashboard (Active) -->
                <a href="{{ route('user.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-blue-50 text-blue-600 shadow-sm shadow-blue-500/5 transition-all">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span>Dashboard Spot</span>
                </a>

                <!-- 2. Order Booking -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Order Booking</span>
                    </div>
                    <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Pesan Meja</span>
                </a>

                <!-- 3. Riwayat Booking -->
                <a href="#" class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Riwayat Booking</span>
                    </div>
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
                    Beranda i-Find
                </a>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 text-xs font-bold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Header Topbar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 px-6 sm:px-8 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Eksplorasi Spot Hangout Siswa</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Temukan tempat kumpul hemat dan pesan meja instan</p>
            </div>

            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    GPS LBS Aktif
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

            <!-- PILIH TANGGAL & FILTER SECTION -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-sm">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Pilih Tanggal Hangout</h2>
                        <p class="text-xs text-slate-500">Sesuaikan jadwal kunjunganmu untuk melihat ketersediaan meja live</p>
                    </div>

                    <!-- Date Picker Input -->
                    <div class="flex items-center space-x-2 w-full md:w-auto">
                        <label for="booking_date" class="sr-only">Tanggal Kunjungan</label>
                        <div class="relative w-full md:w-56">
                            <input type="date" 
                                   id="booking_date" 
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl px-4 py-2.5 focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all cursor-pointer">
                        </div>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition">
                            Terapkan
                        </button>
                    </div>
                </div>

                <!-- Quick Filter Tags -->
                <div class="flex flex-wrap items-center gap-2 pt-4">
                    <span class="text-xs font-bold text-slate-400 mr-2">Filter Cepat:</span>
                    <button type="button" class="bg-blue-600 text-white text-xs font-semibold px-3.5 py-1.5 rounded-full shadow-sm">
                        Semua Spot
                    </button>
                    <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium px-3.5 py-1.5 rounded-full transition">
                        Harga Pelajar (&lt; 25rb)
                    </button>
                    <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium px-3.5 py-1.5 rounded-full transition">
                        Wi-Fi Kencang & Stopkontak
                    </button>
                    <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium px-3.5 py-1.5 rounded-full transition">
                        Terdekat (&lt; 1 km)
                    </button>
                </div>
            </div>

            <!-- CARD TOKO-TOKO YANG TERSEDIA -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 tracking-tight">Rekomendasi Toko & Spot Hangout Tersedia</h3>
                        <p class="text-xs text-slate-500">Spot terpilih dengan harga bersahabat untuk pelajar</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Menampilkan 3 Spot Mitra</span>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- Card Toko 1 -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <!-- Header / Tag Banner -->
                            <div class="h-44 bg-gradient-to-br from-blue-600 to-indigo-700 p-5 flex flex-col justify-between relative overflow-hidden">
                                <div class="absolute inset-0 bg-black/15"></div>
                                <div class="relative z-10 flex justify-between items-start">
                                    <span class="bg-white/95 text-blue-700 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                        Favorit Pelajar
                                    </span>
                                    <span class="bg-emerald-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                        <span>4.9</span>
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </span>
                                </div>
                                <div class="relative z-10 text-white">
                                    <h4 class="text-xl font-black">Kopi Titik Temu</h4>
                                    <p class="text-xs text-blue-100 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        0.4 km dari lokasimu
                                    </p>
                                </div>
                            </div>

                            <!-- Content Details -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Ketersediaan Meja:</span>
                                    <span class="font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                        6 Meja Kosong
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Rentang Harga:</span>
                                    <span class="font-bold text-slate-800">Rp 12.000 - Rp 25.000</span>
                                </div>

                                <!-- Amenities -->
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Wi-Fi 100 Mbps</span>
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Colokan di Tiap Meja</span>
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">AC Dingin</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action -->
                        <div class="px-6 pb-6 pt-2">
                            <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-3 rounded-2xl shadow-lg shadow-blue-600/20 hover:-translate-y-0.5 active:scale-95 transition-all">
                                Booking Spot Ini
                            </button>
                        </div>
                    </div>

                    <!-- Card Toko 2 -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <!-- Header / Tag Banner -->
                            <div class="h-44 bg-gradient-to-br from-indigo-600 to-sky-700 p-5 flex flex-col justify-between relative overflow-hidden">
                                <div class="absolute inset-0 bg-black/15"></div>
                                <div class="relative z-10 flex justify-between items-start">
                                    <span class="bg-white/95 text-indigo-700 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                        Cocok Buat Nugas
                                    </span>
                                    <span class="bg-emerald-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                        <span>4.8</span>
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </span>
                                </div>
                                <div class="relative z-10 text-white">
                                    <h4 class="text-xl font-black">Ruang Sela Cafe</h4>
                                    <p class="text-xs text-indigo-100 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        0.9 km dari lokasimu
                                    </p>
                                </div>
                            </div>

                            <!-- Content Details -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Ketersediaan Meja:</span>
                                    <span class="font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                        4 Meja Kosong
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Rentang Harga:</span>
                                    <span class="font-bold text-slate-800">Rp 15.000 - Rp 28.000</span>
                                </div>

                                <!-- Amenities -->
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Suasana Tenang</span>
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Free Air Mineral</span>
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Meja Luas</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action -->
                        <div class="px-6 pb-6 pt-2">
                            <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-3 rounded-2xl shadow-lg shadow-blue-600/20 hover:-translate-y-0.5 active:scale-95 transition-all">
                                Booking Spot Ini
                            </button>
                        </div>
                    </div>

                    <!-- Card Toko 3 -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <!-- Header / Tag Banner -->
                            <div class="h-44 bg-gradient-to-br from-teal-600 to-emerald-700 p-5 flex flex-col justify-between relative overflow-hidden">
                                <div class="absolute inset-0 bg-black/15"></div>
                                <div class="relative z-10 flex justify-between items-start">
                                    <span class="bg-white/95 text-teal-700 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                        Buka 24 Jam
                                    </span>
                                    <span class="bg-emerald-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                        <span>4.7</span>
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </span>
                                </div>
                                <div class="relative z-10 text-white">
                                    <h4 class="text-xl font-black">Taman Literasi Hub</h4>
                                    <p class="text-xs text-teal-100 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        1.2 km dari lokasimu
                                    </p>
                                </div>
                            </div>

                            <!-- Content Details -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Ketersediaan Meja:</span>
                                    <span class="font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                        8 Meja Kosong
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Rentang Harga:</span>
                                    <span class="font-bold text-slate-800">Rp 10.000 - Rp 22.000</span>
                                </div>

                                <!-- Amenities -->
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Buka 24 Jam</span>
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Area Outdoor</span>
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Musholla Terdekat</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action -->
                        <div class="px-6 pb-6 pt-2">
                            <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-3 rounded-2xl shadow-lg shadow-blue-600/20 hover:-translate-y-0.5 active:scale-95 transition-all">
                                Booking Spot Ini
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

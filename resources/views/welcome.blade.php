<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>i-Find — Temukan Spot Nongkrong & Hangout Hemat Pelajar</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-pattern {
            background-image: radial-gradient(#3b82f6 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- NAVBAR -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-white/90 border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-black text-xl shadow-md shadow-blue-500/20 text-white">i</div>
                <span class="text-2xl font-extrabold tracking-tight text-slate-900">i-Find</span>
                <span class="text-[10px] uppercase tracking-wider bg-blue-50 border border-blue-200 text-blue-600 font-bold px-2.5 py-1 rounded-full">Student Ver.</span>
            </div>
            
            <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-600">
                <a href="#fitur" class="hover:text-blue-600 transition">Fitur Utama</a>
                <a href="#kategori" class="hover:text-blue-600 transition">Kategori Spot</a>
                <a href="#keunggulan" class="hover:text-blue-600 transition">Keunggulan LBS</a>
            </nav>

            <div class="flex items-center space-x-4">
                <a href="#" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Masuk</a>
                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-blue-600/20 transition-all transform hover:-translate-y-0.5">
                    Mulai Jelajah
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative pt-20 pb-32 overflow-hidden z-10 hero-pattern bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full text-xs font-semibold bg-blue-50 border border-blue-200 text-blue-700 mb-8 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Platform Reservasi & LBS Khusus Pelajar #1
            </div>

            <!-- Heading -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-slate-900 max-w-4xl mx-auto leading-[1.15]">
                Tempat Kumpul Spot Hemat dan <span class="text-blue-600">Hangout</span> Terbaik
            </h1>

            <!-- Subtitle -->
            <p class="mt-6 text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Rekomendasi tempat nongkrong yang pas di kantong siswa. Temukan spot terdekat menggunakan teknologi GPS akurat dan booking meja instan tanpa ribet.
            </p>

            <!-- CTA Buttons -->
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="#fitur" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-2xl shadow-xl shadow-blue-600/25 transition-all flex items-center justify-center gap-2 text-base group">
                    Cari Spot Terdekat 
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
                <a href="#kategori" class="bg-white hover:bg-slate-50 text-slate-700 font-semibold px-8 py-4 rounded-2xl border border-slate-200 transition-all text-base flex items-center justify-center shadow-sm">
                    Jelajahi Hangout Pedia
                </a>
            </div>

            <!-- Floating Stats Card Preview -->
            <div class="mt-16 max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 p-6 rounded-3xl bg-white border border-slate-200 shadow-xl shadow-slate-200/50">
                <div class="text-left p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Akurasi GPS</p>
                    <p class="text-2xl font-black text-blue-600 mt-1">Real-Time</p>
                </div>
                <div class="text-left p-4 border-l border-slate-100">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Budget Pelajar</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">&lt; Rp 25rb</p>
                </div>
                <div class="text-left p-4 border-l border-slate-100">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Fitur Booking</p>
                    <p class="text-2xl font-black text-indigo-600 mt-1">Instant</p>
                </div>
                <div class="text-left p-4 border-l border-slate-100">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Panduan Lokasi</p>
                    <p class="text-2xl font-black text-sky-600 mt-1">Hangout Pedia</p>
                </div>
            </div>

        </div>
    </section>

    <!-- FITUR UTAMA SECTION -->
    <section id="fitur" class="py-24 bg-slate-50 border-t border-slate-200 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-xs font-bold tracking-widest text-blue-600 uppercase mb-3">Fitur Unggulan Sistem</h2>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Dirancang Khusus untuk Kebutuhan Nongkrongmu</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1 -->
                <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-blue-50 border border-blue-100 text-blue-600 rounded-2xl flex items-center justify-center font-bold text-2xl mb-6 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all">📍</div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">GPS Pencarian Spot</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">Temukan spot ngumpul terdekat dari posisimu saat ini dengan navigasi peta yang interaktif.</p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl hover:border-indigo-300 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center font-bold text-2xl mb-6 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all">🎟️</div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Pemesanan Tempat</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">Booking meja atau area kumpul bareng teman sekolah jadi lebih gampang, aman, dan anti kehabisan.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center font-bold text-2xl mb-6 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all">📖</div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Hangout Pedia</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">Panduan lengkap berbagai tempat nongkrong ramah budget, fasilitas Wi-Fi cepat, dan colokan listrik.</p>
                </div>

                <!-- Card 4 -->
                <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl hover:border-sky-300 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-sky-50 border border-sky-100 text-sky-600 rounded-2xl flex items-center justify-center font-bold text-2xl mb-6 group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white transition-all">🎯</div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Akurasi GPS Spasial</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">Perhitungan jarak divalidasi presisi dengan posisi terdekat menggunakan formula geolokasi handal.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- KATEGORI SECTION -->
    <section id="kategori" class="py-24 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16">
                <div>
                    <h2 class="text-xs font-bold tracking-widest text-blue-600 uppercase mb-3">Kategori Pilihan</h2>
                    <h3 class="text-3xl font-extrabold text-slate-900">Spot Favorit Anak Sekolah</h3>
                </div>
                <p class="text-slate-600 text-sm max-w-sm mt-4 md:mt-0">Semua tempat direkomendasikan khusus berdasarkan kriteria kantong pelajar dan kenyamanan nugas kelompok.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="relative group overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 p-8 flex flex-col justify-between h-72 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full border border-blue-200">Super Hemat</span>
                        <span class="text-2xl">☕</span>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-1">Warkop & Cafe Mini</h4>
                        <p class="text-sm text-slate-600">Kisaran harga mulai Rp 10.000-an, lengkap dengan fasilitas colokan dan kopi santai.</p>
                    </div>
                </div>

                <div class="relative group overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 p-8 flex flex-col justify-between h-72 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-full border border-indigo-200">Nyaman & Tenang</span>
                        <span class="text-2xl">📚</span>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-1">Spot Nugas & Diskusi</h4>
                        <p class="text-sm text-slate-600">Tempat kumpul yang kondusif buat ngerjain tugas kelompok, kerja bakti, atau belajar bareng.</p>
                    </div>
                </div>

                <div class="relative group overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 p-8 flex flex-col justify-between h-72 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <span class="bg-sky-100 text-sky-700 text-xs font-bold px-3 py-1.5 rounded-full border border-sky-200">Seru & Ramai</span>
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-1">Tempat Main & Hangout</h4>
                        <p class="text-sm text-slate-600">Pilihan spot santai buat lepas penat setelah jam sekolah selesai bersama geng sekolahmu.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white shadow">i</div>
                <span class="text-lg font-bold text-white">i-Find</span>
                <span class="text-xs text-slate-500">— Student Hangout & Booking Platform</span>
            </div>
            <p class="text-sm text-slate-400">&copy; 2026 i-Find. Dibangun dengan Laravel & Tailwind CSS.</p>
        </div>
    </footer>

</body>
</html>

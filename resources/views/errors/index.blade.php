<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Koleksi Custom Error Pages — {{ config('app.name', 'i-Find') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-pattern {
            background-image: radial-gradient(#cbd5e1 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="min-h-full bg-slate-50 hero-pattern text-slate-800 antialiased py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-5xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center font-black text-2xl shadow-lg shadow-blue-500/30 text-white hover:scale-105 transition-transform">
                    i
                </a>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Koleksi Custom Error Pages</h1>
                    <p class="text-xs text-slate-500">Pratinjau langsung seluruh halaman penanganan error i-Find</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-xs transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>

        <!-- Category: Server Errors (5xx) -->
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">5xx Server Side Errors</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- 506 -->
                <a href="{{ url('/preview-error/506') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-fuchsia-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-fuchsia-600 group-hover:scale-105 transition-transform">506</span>
                            <span class="w-8 h-8 rounded-xl bg-fuchsia-50 text-fuchsia-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-arrows-split-up-and-left"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-fuchsia-600 transition-colors">Variant Also Negotiates</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Konflik loop negosiasi konfigurasi konten internal server.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-fuchsia-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 500 -->
                <a href="{{ url('/preview-error/500') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-rose-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-rose-600 group-hover:scale-105 transition-transform">500</span>
                            <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-server"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-rose-600 transition-colors">Internal Server Error</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Terjadi gangguan internal tak terduga pada sistem backend.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-rose-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 502 -->
                <a href="{{ url('/preview-error/502') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-red-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-red-600 group-hover:scale-105 transition-transform">502</span>
                            <span class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-link-slash"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-red-600 transition-colors">Bad Gateway</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Server proxy menerima respon tidak valid dari upstream.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-red-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 503 -->
                <a href="{{ url('/preview-error/503') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-cyan-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-cyan-600 group-hover:scale-105 transition-transform">503</span>
                            <span class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-cyan-600 transition-colors">Service Unavailable</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Pemeliharaan sistem terjadwal atau server kelebihan beban.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-cyan-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 504 -->
                <a href="{{ url('/preview-error/504') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-amber-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-amber-600 group-hover:scale-105 transition-transform">504</span>
                            <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-hourglass-end"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-amber-600 transition-colors">Gateway Timeout</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Waktu respons server gateway habis saat menunggu upstream.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-amber-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>
            </div>
        </div>

        <!-- Category: Client Errors (4xx) -->
        <div class="space-y-4 pt-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">4xx Client Side Errors</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- 404 -->
                <a href="{{ url('/preview-error/404') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-blue-600 group-hover:scale-105 transition-transform">404</span>
                            <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-blue-600 transition-colors">Page Not Found</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Spot hangout atau tautan halaman tidak ditemukan.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-blue-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 403 -->
                <a href="{{ url('/preview-error/403') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-amber-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-amber-600 group-hover:scale-105 transition-transform">403</span>
                            <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-amber-600 transition-colors">Forbidden Access</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Akses dibatasi khusus otorisasi tertentu atau role khusus.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-amber-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 401 -->
                <a href="{{ url('/preview-error/401') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-indigo-600 group-hover:scale-105 transition-transform">401</span>
                            <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-key"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-indigo-600 transition-colors">Unauthorized</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Perlu login terlebih dahulu untuk mengakses sumber daya.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-indigo-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 419 -->
                <a href="{{ url('/preview-error/419') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-orange-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-orange-600 group-hover:scale-105 transition-transform">419</span>
                            <span class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-orange-600 transition-colors">Page / Session Expired</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Sesi token form CSRF habis karena inaktivitas.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-orange-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 429 -->
                <a href="{{ url('/preview-error/429') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-yellow-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-yellow-600 group-hover:scale-105 transition-transform">429</span>
                            <span class="w-8 h-8 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-gauge-high"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-yellow-600 transition-colors">Too Many Requests</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Batas rate limiting terlampaui karena request terlalu rapat.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-yellow-600 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>

                <!-- 400 -->
                <a href="{{ url('/preview-error/400') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-black text-slate-700 group-hover:scale-105 transition-transform">400</span>
                            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-file-circle-xmark"></i>
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-slate-800 transition-colors">Bad Request</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Sintaks permintaan atau payload form tidak valid.</p>
                    </div>
                    <span class="mt-4 text-xs font-semibold text-slate-700 inline-flex items-center gap-1">
                        Buka Pratinjau <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>
            </div>
        </div>

    </div>

</body>
</html>

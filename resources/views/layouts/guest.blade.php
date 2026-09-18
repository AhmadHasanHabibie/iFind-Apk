<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'i-Find') }} — Masuk & Registrasi Pelajar</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="min-h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 text-slate-800 bg-white hero-pattern antialiased selection:bg-blue-600 selection:text-white">
        <div class="w-full max-w-md space-y-6">
            <!-- Signature Brand Logo -->
            <div class="text-center">
                <a href="/" class="inline-flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center font-black text-2xl shadow-lg shadow-blue-500/30 text-white group-hover:scale-105 transition-transform duration-200">
                        i
                    </div>
                    <span class="text-3xl font-black tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">i-Find</span>
                    <span class="text-[10px] uppercase tracking-wider bg-blue-50 border border-blue-200 text-blue-600 font-bold px-2.5 py-1 rounded-full shadow-2xs">Student Ver.</span>
                </a>
            </div>

            <!-- Card Container -->
            <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200/90 shadow-xl shadow-slate-200/50 p-6 sm:p-8 space-y-6">
                {{ $slot }}
            </div>

            <!-- Footer link back -->
            <div class="text-center text-xs text-slate-500">
                <a href="/" class="font-bold text-slate-600 hover:text-blue-600 transition flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Beranda i-Find</span>
                </a>
            </div>
        </div>
    </body>
</html>

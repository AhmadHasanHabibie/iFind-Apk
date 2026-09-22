<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code', 'Error') — @yield('title', 'Terjadi Kesalahan') | {{ config('app.name', 'i-Find') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS (CDN standalone to ensure error pages never break if asset bundling fails) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        pulseSlow: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.05)' },
                        }
                    },
                    animation: {
                        float: 'float 4s ease-in-out infinite',
                        'pulse-slow': 'pulseSlow 5s ease-in-out infinite',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .hero-pattern {
            background-image: radial-gradient(#cbd5e1 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between items-center py-10 px-4 sm:px-6 lg:px-8 text-slate-800 bg-slate-50 hero-pattern antialiased selection:bg-blue-600 selection:text-white relative overflow-x-hidden">

    <!-- Background Ambient Glow Blobs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -left-32 w-96 h-96 @yield('ambient_color_1', 'bg-blue-300/30') rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 @yield('ambient_color_2', 'bg-indigo-300/30') rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2.5s;"></div>
    </div>

    <!-- Header / Brand Navigation -->
    <header class="w-full max-w-2xl flex items-center justify-between z-10 mb-6 sm:mb-8">
        <a href="{{ url('/') }}" class="inline-flex items-center space-x-3 group">
            <div class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center font-black text-xl shadow-lg shadow-blue-500/30 text-white group-hover:scale-105 transition-transform duration-200">
                i
            </div>
            <div class="text-left">
                <span class="text-2xl font-black tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">i-Find</span>
                <span class="block text-[10px] font-bold text-slate-400 -mt-1 tracking-wider uppercase">Spot Nongkrong Pelajar</span>
            </div>
        </a>

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 backdrop-blur-md border border-slate-200/80 shadow-xs text-xs font-semibold text-slate-600">
            <span class="w-2 h-2 rounded-full @yield('status_dot', 'bg-amber-500 animate-ping')"></span>
            <span>HTTP @yield('code', 'Error')</span>
        </div>
    </header>

    <!-- Main Error Container -->
    <main class="w-full max-w-xl z-10 my-auto">
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-200/80 shadow-2xl shadow-slate-200/70 p-6 sm:p-10 text-center relative overflow-hidden transition-all duration-300 hover:shadow-slate-300/80">
            
            <!-- Top Subtle Light Accent Line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 @yield('accent_gradient', 'bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500')"></div>

            <!-- Animated Icon Badge -->
            <div class="mx-auto w-24 h-24 sm:w-28 sm:h-28 rounded-3xl @yield('badge_bg', 'bg-blue-50 border border-blue-100/80') flex items-center justify-center shadow-inner relative mb-6 animate-float">
                <div class="absolute inset-0 rounded-3xl @yield('badge_glow', 'bg-blue-400/20') blur-xl -z-10"></div>
                <i class="@yield('icon_class', 'fa-solid fa-triangle-exclamation') text-4xl sm:text-5xl @yield('icon_color', 'text-blue-600')"></i>
            </div>

            <!-- Big Status Code & Tag -->
            <div class="space-y-1 mb-4">
                <div class="inline-block">
                    <span class="text-6xl sm:text-7xl font-black tracking-tight @yield('code_gradient', 'bg-gradient-to-br from-slate-900 via-slate-800 to-slate-600 bg-clip-text text-transparent')">
                        @yield('code', 'Error')
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    @yield('title', 'Terjadi Kesalahan')
                </h1>
            </div>

            <!-- Descriptive Message -->
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-md mx-auto mb-8">
                @yield('message', 'Maaf, sistem mendeteksi kendala dalam memproses permintaan Anda. Silakan coba beberapa saat lagi.')
            </p>

            <!-- Custom Content Slot (if any) -->
            @yield('custom_content')

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-98 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200">
                    <i class="fa-solid fa-house text-xs"></i>
                    <span>Kembali ke Beranda</span>
                </a>

                <button type="button" onclick="window.location.reload();" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200/80 active:scale-98 text-slate-700 font-semibold text-sm transition-all duration-200">
                    <i class="fa-solid fa-arrow-rotate-right text-xs"></i>
                    <span>Muat Ulang</span>
                </button>
            </div>

            <!-- Back Link -->
            <div class="mt-5">
                <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}';" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                    <span>Kembali ke halaman sebelumnya</span>
                </button>
            </div>

            <!-- Developer Debug Accordion (Active only when APP_DEBUG is true) -->
            @if(config('app.debug'))
                <div class="mt-8 pt-6 border-t border-slate-100 text-left">
                    <details class="group bg-slate-50 border border-slate-200/80 rounded-2xl overflow-hidden transition-all duration-200">
                        <summary class="flex items-center justify-between p-3.5 text-xs font-bold text-slate-700 cursor-pointer select-none hover:bg-slate-100/80">
                            <span class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-code text-blue-600"></i>
                                <span>Informasi Debugging Developer</span>
                            </span>
                            <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform text-xs"></i>
                        </summary>
                        <div class="p-3.5 pt-1 text-xs text-slate-600 space-y-2 font-mono bg-white border-t border-slate-200/60">
                            <div><strong class="text-slate-800">Status Code:</strong> @yield('code', '500')</div>
                            <div><strong class="text-slate-800">Path:</strong> {{ request()->path() }}</div>
                            <div><strong class="text-slate-800">Method:</strong> {{ request()->method() }}</div>
                            @if(isset($exception) && $exception->getMessage())
                                <div class="bg-rose-50 border border-rose-200 rounded-lg p-2 text-rose-800 break-words font-sans">
                                    <span class="font-bold font-mono text-rose-900 block mb-0.5">Exception Message:</span>
                                    {{ $exception->getMessage() }}
                                </div>
                            @endif
                            <div class="text-[11px] text-slate-400 font-sans">
                                <em>Catatan: Panel ini hanya muncul di lingkungan pengembangan (APP_DEBUG=true).</em>
                            </div>
                        </div>
                    </details>
                </div>
            @endif

        </div>

        <!-- Quick Helpful Navigation Links -->
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2 sm:gap-4 text-xs font-medium text-slate-500">
            <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Beranda i-Find</a>
            <span class="text-slate-300">•</span>
            <a href="{{ route('login') }}" class="hover:text-blue-600 transition-colors">Masuk Akun</a>
            <span class="text-slate-300">•</span>
            <a href="{{ route('register') }}" class="hover:text-blue-600 transition-colors">Daftar Pelajar</a>
            <span class="text-slate-300">•</span>
            <a href="mailto:support@ifind.local" class="hover:text-blue-600 transition-colors">Bantuan Teknis</a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-2xl text-center text-xs text-slate-400 z-10 mt-8 space-y-1">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'i-Find') }}. All rights reserved.</p>
        <div class="inline-flex items-center gap-1.5 text-[11px] text-slate-400">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>i-Find Cloud Services &amp; Reservation Platform</span>
        </div>
    </footer>

</body>
</html>

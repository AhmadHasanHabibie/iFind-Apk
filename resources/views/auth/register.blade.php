<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — i-Find Platform Reservasi & Spot Nongkrong</title>
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
<body class="min-h-full font-sans bg-slate-50 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:24px_24px] flex flex-col justify-center items-center px-4 py-12 relative selection:bg-blue-600 selection:text-white">

    <!-- Subtle Ambient Glow -->
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <!-- Centered Card -->
    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-100 p-8 sm:p-10 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] relative z-10">
        
        <!-- Header & Logo -->
        <div class="flex flex-col items-center text-center">
            <a href="/" class="group flex items-center space-x-2.5 mb-5 focus:outline-none">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-black text-xl shadow-md shadow-blue-500/20 text-white group-hover:scale-105 transition-transform">i</div>
                <span class="text-2xl font-extrabold tracking-tight text-slate-900">i-Find</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Bergabung dengan <span class="text-blue-600">i-Find</span>
            </h1>
            <p class="text-sm text-slate-500 mt-2 mb-6">
                Buat akun baru untuk mulai menjelajahi spot hangout hemat
            </p>
        </div>

        <!-- Global Validation Error Alert -->
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs leading-relaxed">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Mohon periksa data yang Anda masukkan:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-600 ml-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- 2-Column Name Inputs -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nama Depan
                    </label>
                    <input type="text" 
                           name="first_name" 
                           id="first_name" 
                           value="{{ old('first_name') }}"
                           required 
                           placeholder="Rifqi"
                           class="w-full bg-slate-50/50 border @error('first_name') border-rose-300 ring-1 ring-rose-500 @else border-slate-200 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all duration-300 placeholder:text-slate-400">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nama Belakang
                    </label>
                    <input type="text" 
                           name="last_name" 
                           id="last_name" 
                           value="{{ old('last_name') }}"
                           required 
                           placeholder="Habibie"
                           class="w-full bg-slate-50/50 border @error('last_name') border-rose-300 ring-1 ring-rose-500 @else border-slate-200 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all duration-300 placeholder:text-slate-400">
                </div>
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Alamat Email
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}"
                       required 
                       placeholder="nama@email.com"
                       class="w-full bg-slate-50/50 border @error('email') border-rose-300 ring-1 ring-rose-500 @else border-slate-200 @enderror text-slate-900 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all duration-300 placeholder:text-slate-400">
            </div>

            <!-- Optional School / Institution Input -->
            <div>
                <label for="institution" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Asal Sekolah / Kampus <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                </label>
                <input type="text" 
                       name="institution" 
                       id="institution" 
                       value="{{ old('institution') }}"
                       placeholder="Contoh: SMA Negeri 1 / Universitas"
                       class="w-full bg-slate-50/50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all duration-300 placeholder:text-slate-400">
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Kata Sandi
                </label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           placeholder="Minimal 8 karakter"
                           class="w-full bg-slate-50/50 border @error('password') border-rose-300 ring-1 ring-rose-500 @else border-slate-200 @enderror text-slate-900 rounded-xl px-4 py-3 pr-11 text-sm focus:bg-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 transition-all duration-300 placeholder:text-slate-400">
                    <button type="button" 
                            id="toggle-password" 
                            aria-label="Tampilkan atau sembunyikan kata sandi"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Terms and Conditions Checkbox -->
            <div class="flex items-start pt-1">
                <input id="terms" 
                       name="terms" 
                       type="checkbox" 
                       value="1"
                       required
                       class="w-4 h-4 mt-0.5 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500/20 focus:ring-2 cursor-pointer">
                <label for="terms" class="ml-2 block text-xs text-slate-500 leading-relaxed select-none cursor-pointer">
                    Saya menyetujui <a href="#" class="text-blue-600 font-semibold hover:underline">Ketentuan Layanan</a> dan <a href="#" class="text-blue-600 font-semibold hover:underline">Kebijakan Privasi</a> i-Find.
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl py-3.5 mt-4 shadow-lg shadow-blue-600/20 hover:-translate-y-0.5 active:scale-95 transition-all duration-300">
                Daftar Akun Baru
            </button>
        </form>

        <!-- Footer Link -->
        <span class="text-sm text-slate-500 text-center mt-6 block">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Masuk</a>
        </span>

        <!-- Back to Home -->
        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
            <a href="/" class="inline-flex items-center text-xs font-medium text-slate-400 hover:text-slate-600 transition">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Script for Password Visibility Toggle -->
    <script>
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeSlashIcon = document.getElementById('eye-slash-icon');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.classList.toggle('hidden', isPassword);
            eyeSlashIcon.classList.toggle('hidden', !isPassword);
        });
    </script>
</body>
</html>

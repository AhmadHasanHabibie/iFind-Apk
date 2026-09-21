<x-guest-layout>
    <div class="text-center space-y-1">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
        <p class="text-xs text-slate-500 font-medium">Masuk untuk mengecek ketersediaan spot & reservasi meja</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
            <input id="email"
                   class="block w-full text-sm font-semibold rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3 shadow-2xs"
                   type="email"
                   name="email"
                   :value="old('email')"
                   required autofocus autocomplete="username"
                   placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <!-- <a class="text-xs font-bold text-blue-600 hover:text-blue-800 transition" href="{{ route('password.request') }}">
                        Lupa password?
                    </a> -->
                @endif
            </div>

            <input id="password"
                   class="block w-full text-sm font-semibold rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3 shadow-2xs"
                   type="password"
                   name="password"
                   required autocomplete="current-password"
                   placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-lg border-slate-300 text-blue-600 shadow-xs focus:ring-blue-500" name="remember">
                <span class="ms-2 text-xs font-semibold text-slate-600">Ingat saya</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full py-3.5 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-sm shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center space-x-2">
                <span>Masuk Sekarang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-500">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-800 transition">
                    Daftar di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

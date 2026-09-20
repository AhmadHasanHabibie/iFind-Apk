<x-guest-layout>
    <div class="text-center space-y-1">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Akun Baru</h2>
        <p class="text-xs text-slate-500 font-medium">Temukan spot nongkrong hemat dan booking meja instan</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Role Selection -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Daftar sebagai:</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative flex items-center justify-between p-3.5 border border-slate-200 rounded-2xl cursor-pointer hover:border-blue-300 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/60 shadow-2xs">
                    <div class="flex items-center">
                        <input type="radio" name="role" value="user" class="h-4 w-4 text-blue-600 border-slate-300 focus:ring-blue-500" {{ old('role', 'user') === 'user' ? 'checked' : '' }}>
                        <span class="ml-2.5 text-xs font-bold text-slate-800">Pelajar / Tamu</span>
                    </div>
                    <i class="fa-solid fa-graduation-cap text-slate-400"></i>
                </label>

                <label class="relative flex items-center justify-between p-3.5 border border-slate-200 rounded-2xl cursor-pointer hover:border-blue-300 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/60 shadow-2xs">
                    <div class="flex items-center">
                        <input type="radio" name="role" value="staff" class="h-4 w-4 text-blue-600 border-slate-300 focus:ring-blue-500" {{ old('role') === 'staff' ? 'checked' : '' }}>
                        <span class="ml-2.5 text-xs font-bold text-slate-800">Mitra Staf Toko</span>
                    </div>
                    <i class="fa-solid fa-store text-slate-400"></i>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1 text-xs" />
        </div>

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
            <input id="name"
                   class="block w-full text-sm font-semibold rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3 shadow-2xs"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   required autofocus autocomplete="name"
                   placeholder="Nama lengkap Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
            <input id="email"
                   class="block w-full text-sm font-semibold rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3 shadow-2xs"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required autocomplete="username"
                   placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp / HP (Opsional)</label>
            <input id="phone"
                   class="block w-full text-sm font-semibold rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3 shadow-2xs"
                   type="text"
                   name="phone"
                   value="{{ old('phone') }}"
                   placeholder="08123456789" />
            <x-input-error :messages="$errors->get('phone')" class="mt-1 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Password</label>
            <input id="password"
                   class="block w-full text-sm font-semibold rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3 shadow-2xs"
                   type="password"
                   name="password"
                   required autocomplete="new-password"
                   placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Password</label>
            <input id="password_confirmation"
                   class="block w-full text-sm font-semibold rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3 shadow-2xs"
                   type="password"
                   name="password_confirmation"
                   required autocomplete="new-password"
                   placeholder="Ulangi password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full py-3.5 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-sm shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center space-x-2">
                <span>Daftar Sekarang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 transition">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

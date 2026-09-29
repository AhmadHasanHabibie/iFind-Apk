<section x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
    <div class="flex items-center gap-3 pb-5 border-b border-slate-100">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 shadow-xs">
            <i class="fa-solid fa-lock text-base"></i>
        </div>
        <div>
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                {{ __('Perbarui Kata Sandi') }}
            </h2>
            <p class="text-xs text-slate-500">
                {{ __('Pastikan akun Anda menggunakan kata sandi yang aman dan tidak mudah ditebak.') }}
            </p>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <!-- Kata Sandi Saat Ini -->
        <div class="space-y-1.5">
            <label for="update_password_current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ __('Kata Sandi Saat Ini') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-key text-xs"></i>
                </span>
                <input id="update_password_current_password" name="current_password"
                       :type="showCurrent ? 'text' : 'password'"
                       autocomplete="current-password"
                       placeholder="Masukkan kata sandi saat ini"
                       class="w-full pl-10 pr-11 py-3 rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition @if($errors->updatePassword->has('current_password')) border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @endif">
                <button type="button" @click="showCurrent = !showCurrent"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                    <i :class="showCurrent ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-xs"></i>
                </button>
            </div>
            @if($errors->updatePassword->has('current_password'))
                <p class="text-xs text-rose-500 font-semibold flex items-center gap-1.5 mt-1">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->updatePassword->first('current_password') }}
                </p>
            @endif
        </div>

        <!-- Kata Sandi Baru -->
        <div class="space-y-1.5">
            <label for="update_password_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ __('Kata Sandi Baru') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-shield-halved text-xs"></i>
                </span>
                <input id="update_password_password" name="password"
                       :type="showNew ? 'text' : 'password'"
                       autocomplete="new-password"
                       placeholder="Minimal 8 karakter"
                       class="w-full pl-10 pr-11 py-3 rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition @if($errors->updatePassword->has('password')) border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @endif">
                <button type="button" @click="showNew = !showNew"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                    <i :class="showNew ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-xs"></i>
                </button>
            </div>
            @if($errors->updatePassword->has('password'))
                <p class="text-xs text-rose-500 font-semibold flex items-center gap-1.5 mt-1">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->updatePassword->first('password') }}
                </p>
            @endif
        </div>

        <!-- Konfirmasi Kata Sandi Baru -->
        <div class="space-y-1.5">
            <label for="update_password_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ __('Konfirmasi Kata Sandi Baru') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-lock text-xs"></i>
                </span>
                <input id="update_password_password_confirmation" name="password_confirmation"
                       :type="showConfirm ? 'text' : 'password'"
                       autocomplete="new-password"
                       placeholder="Ulangi kata sandi baru"
                       class="w-full pl-10 pr-11 py-3 rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition @if($errors->updatePassword->has('password_confirmation')) border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @endif">
                <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                    <i :class="showConfirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-xs"></i>
                </button>
            </div>
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="text-xs text-rose-500 font-semibold flex items-center gap-1.5 mt-1">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->updatePassword->first('password_confirmation') }}
                </p>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 pt-3 border-t border-slate-100">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white text-xs sm:text-sm font-bold shadow-md shadow-indigo-600/25 transition duration-200">
                <i class="fa-solid fa-shield-halved text-xs"></i>
                {{ __('Perbarui Kata Sandi') }}
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     x-init="setTimeout(() => show = false, 4000)"
                     class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ __('Kata sandi berhasil diperbarui!') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>

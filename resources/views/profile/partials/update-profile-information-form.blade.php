<section>
    <div class="flex items-center gap-3 pb-5 border-b border-slate-100">
        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 shadow-xs">
            <i class="fa-solid fa-user-pen text-base"></i>
        </div>
        <div>
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                {{ __('Informasi Profil') }}
            </h2>
            <p class="text-xs text-slate-500">
                {{ __('Perbarui data diri, nama lengkap, kontak WhatsApp, dan alamat email Anda.') }}
            </p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <!-- Nama Lengkap -->
        <div class="space-y-1.5">
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ __('Nama Lengkap') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-user text-xs"></i>
                </span>
                <input id="name" name="name" type="text"
                       value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                       placeholder="Masukkan nama lengkap Anda"
                       class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('name') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror">
            </div>
            @error('name')
                <p class="text-xs text-rose-500 font-semibold flex items-center gap-1.5 mt-1">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Alamat Email -->
        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ __('Alamat Email') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-envelope text-xs"></i>
                </span>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $user->email) }}" required autocomplete="username"
                       placeholder="nama@email.com"
                       class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('email') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror">
            </div>
            @error('email')
                <p class="text-xs text-rose-500 font-semibold flex items-center gap-1.5 mt-1">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-900 space-y-2">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5 text-sm"></i>
                        <div class="text-xs leading-relaxed">
                            <p class="font-bold text-amber-900">{{ __('Alamat email Anda belum diverifikasi.') }}</p>
                            <p class="text-amber-700 mt-0.5">{{ __('Verifikasi email diperlukan untuk notifikasi bukti pembayaran dan tiket reservasi.') }}</p>
                        </div>
                    </div>

                    <div class="pt-1">
                        <button form="send-verification" type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                            {{ __('Kirim Ulang Email Verifikasi') }}
                        </button>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-xs font-bold text-emerald-700 flex items-center gap-1.5 pt-1">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ __('Tautan verifikasi baru telah berhasil dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Nomor Telepon / WhatsApp -->
        <div class="space-y-1.5">
            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ __('Nomor WhatsApp / HP') }}
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-brands fa-whatsapp text-xs"></i>
                </span>
                <input id="phone" name="phone" type="text"
                       value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                       placeholder="081234567890"
                       class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition @error('phone') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror">
            </div>
            <p class="text-[11px] text-slate-400">
                {{ __('Nomor ini memudahkan pihak pengelola spot menghubungi Anda terkait reservasi.') }}
            </p>
            @error('phone')
                <p class="text-xs text-rose-500 font-semibold flex items-center gap-1.5 mt-1">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 pt-3 border-t border-slate-100">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-600/25 transition duration-200">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
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
                    <span>{{ __('Perubahan profil berhasil disimpan!') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>

<section class="space-y-6">
    <div class="flex items-center gap-3 pb-5 border-b border-rose-100">
        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-base"></i>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base sm:text-lg font-black text-rose-900 tracking-tight">
                    {{ __('Hapus Akun') }}
                </h2>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-100 text-rose-700">Zona Bahaya</span>
            </div>
            <p class="text-xs text-rose-600/80">
                {{ __('Setelah akun dihapus, seluruh data profil, reservasi, dan ulasan akan dihapus permanen.') }}
            </p>
        </div>
    </div>

    <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-100 text-rose-800 text-xs leading-relaxed space-y-2">
        <p class="font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
            {{ __('Perhatian Sebelum Menghapus Akun:') }}
        </p>
        <ul class="list-disc list-inside space-y-1 text-rose-700 pl-1 text-[11px]">
            <li>Semua riwayat booking dan e-tiket reservasi Anda tidak akan bisa diakses lagi.</li>
            <li>Ulasan dan penilaian kafe yang pernah Anda berikan akan dihapus.</li>
            <li>Akun yang telah dihapus tidak dapat dipulihkan kembali dengan alasan apapun.</li>
        </ul>
    </div>

    <div>
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 active:scale-[0.98] text-white text-xs sm:text-sm font-bold shadow-md shadow-rose-600/20 transition duration-200">
            <i class="fa-solid fa-trash-can text-xs"></i>
            {{ __('Hapus Akun Saya') }}
        </button>
    </div>

    <!-- Confirmation Modal Component -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 space-y-5">
            @csrf
            @method('delete')

            <div class="w-14 h-14 rounded-3xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto text-2xl shadow-xs">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="text-center space-y-1">
                <h3 class="text-lg font-black text-slate-900 tracking-tight">
                    {{ __('Apakah Anda yakin ingin menghapus akun?') }}
                </h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                    {{ __('Tindakan ini permanen. Masukkan kata sandi akun Anda untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini.') }}
                </p>
            </div>

            <div class="space-y-1.5 text-left">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    {{ __('Kata Sandi Akun') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </span>
                    <input id="password" name="password" type="password"
                           placeholder="Masukkan kata sandi untuk konfirmasi"
                           class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition @if($errors->userDeletion->has('password')) border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @endif">
                </div>
                @if($errors->userDeletion->has('password'))
                    <p class="text-xs text-rose-500 font-semibold flex items-center gap-1.5 mt-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->userDeletion->first('password') }}
                    </p>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" x-on:click="$dispatch('close')"
                        class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    {{ __('Batal') }}
                </button>

                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition">
                    <i class="fa-solid fa-trash-can mr-1.5 text-[10px]"></i>
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

<x-guest-layout>
    <div class="text-center space-y-2">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-3xl border border-blue-100 flex items-center justify-center mx-auto text-2xl shadow-sm">
            <i class="fa-solid fa-envelope-circle-check"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Verifikasi Email Anda</h2>
        <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto leading-relaxed">
            Kode verifikasi 6-digit (OTP) telah dikirimkan ke kotak masuk Gmail:
            <span class="block font-bold text-slate-800 text-sm mt-0.5">{{ $maskedEmail }}</span>
        </p>
    </div>

    <!-- Alert Notifikasi -->
    @if (session('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify.submit') }}" class="space-y-6" id="otp-form">
        @csrf

        <!-- 6-Digit OTP Input Container -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 text-center mb-3">
                Masukkan 6 Digit Kode OTP
            </label>

            <!-- Hidden input that holds the actual combined OTP -->
            <input type="hidden" name="otp" id="final-otp">

            <div class="flex items-center justify-center gap-2 sm:gap-3" id="otp-inputs">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit w-12 h-14 text-center text-xl font-black rounded-2xl border-2 border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-xs transition" autofocus>
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit w-12 h-14 text-center text-xl font-black rounded-2xl border-2 border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-xs transition">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit w-12 h-14 text-center text-xl font-black rounded-2xl border-2 border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-xs transition">
                <span class="text-slate-300 font-bold">-</span>
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit w-12 h-14 text-center text-xl font-black rounded-2xl border-2 border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-xs transition">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit w-12 h-14 text-center text-xl font-black rounded-2xl border-2 border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-xs transition">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit w-12 h-14 text-center text-xl font-black rounded-2xl border-2 border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-xs transition">
            </div>

            <x-input-error :messages="$errors->get('otp')" class="mt-2 text-xs text-center font-bold" />
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm py-3.5 rounded-2xl shadow-lg shadow-blue-600/25 hover:shadow-blue-600/40 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2">
            <span>Verifikasi Akun Sekarang</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
    </form>

    <!-- Resend OTP & Cooldown Section -->
    <div class="pt-2 border-t border-slate-100 text-center space-y-3" x-data="otpResendTimer()">
        <p class="text-xs text-slate-500">
            Tidak menerima email atau kode kedaluwarsa?
        </p>

        <form method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button type="submit"
                    :disabled="countdown > 0"
                    class="text-xs font-bold text-blue-600 hover:text-blue-700 disabled:text-slate-400 disabled:cursor-not-allowed transition flex items-center justify-center gap-1.5 mx-auto">
                <i class="fa-solid fa-rotate-right text-xs" :class="{ 'animate-spin': countdown > 0 }"></i>
                <span x-show="countdown === 0">Kirim Ulang Kode OTP</span>
                <span x-show="countdown > 0" x-text="`Kirim ulang dalam ${countdown} detik`"></span>
            </button>
        </form>

        <div class="pt-2">
            <a href="{{ route('register') }}" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 transition">
                &larr; Gunakan alamat email lain
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const digits = document.querySelectorAll('.otp-digit');
            const hiddenOtp = document.getElementById('final-otp');
            const form = document.getElementById('otp-form');

            function syncOtp() {
                let code = '';
                digits.forEach(d => code += d.value.trim());
                hiddenOtp.value = code;
                return code;
            }

            digits.forEach((input, index) => {
                // Auto focus next on input
                input.addEventListener('input', (e) => {
                    const val = e.target.value;
                    if (val.length > 1) {
                        e.target.value = val.slice(-1);
                    }
                    if (e.target.value && index < digits.length - 1) {
                        digits[index + 1].focus();
                    }
                    syncOtp();
                });

                // Handle backspace navigation
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !input.value && index > 0) {
                        digits[index - 1].focus();
                    }
                });

                // Handle paste whole code (e.g. user pastes 6 digits from Gmail)
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
                    if (/^\d{6}$/.test(pastedData)) {
                        pastedData.split('').forEach((char, i) => {
                            if (digits[i]) digits[i].value = char;
                        });
                        digits[digits.length - 1].focus();
                        syncOtp();
                    }
                });
            });

            form.addEventListener('submit', (e) => {
                const code = syncOtp();
                if (code.length !== 6) {
                    e.preventDefault();
                    alert('Harap lengkapi 6 digit kode OTP sebelum verifikasi.');
                }
            });
        });

        function otpResendTimer() {
            return {
                countdown: 60,
                init() {
                    const timer = setInterval(() => {
                        if (this.countdown > 0) {
                            this.countdown--;
                        } else {
                            clearInterval(timer);
                        }
                    }, 1000);
                }
            }
        }
    </script>
</x-guest-layout>

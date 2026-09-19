@extends('layouts.user')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="paymentTimer({{ $remainingSeconds }})">
    <!-- Back to bookings -->
    <div>
        <a href="{{ route('user.bookings.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition group">
            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            <span>Kembali ke Pesanan Saya</span>
        </a>
    </div>

    <!-- Payment Notice / Countdown Banner -->
    <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 rounded-3xl p-6 text-white shadow-lg shadow-orange-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
                <span class="text-xs font-black uppercase tracking-wider text-amber-100">Selesaikan Pembayaran</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black mt-1">Menunggu Bukti Transfer</h1>
            <p class="text-xs text-amber-100/90 mt-1 max-w-md">
                Transfer nominal di bawah ini ke rekening toko dan unggah bukti sebelum batas waktu berakhir agar reservasi tidak dibatalkan.
            </p>
        </div>

        <div class="bg-black/20 backdrop-blur-sm px-5 py-3.5 rounded-2xl border border-white/20 text-center shrink-0">
            <span class="text-[11px] font-bold text-amber-100 uppercase tracking-wider block">Sisa Waktu</span>
            <div class="text-2xl sm:text-3xl font-mono font-black tracking-tight" x-text="timerDisplay"></div>
            <span class="text-[10px] text-amber-200" x-show="!isExpired">Batas waktu upload</span>
            <span class="text-[10px] text-rose-200 font-bold" x-show="isExpired">Waktu Pembayaran Habis</span>
        </div>
    </div>

    <!-- Expired Warning Banner (if expired) -->
    <template x-if="isExpired">
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 flex items-center space-x-3">
            <i class="fa-solid fa-circle-xmark text-xl text-rose-600 shrink-0"></i>
            <div>
                <strong class="font-bold block">Waktu pembayaran telah habis!</strong>
                <span>Booking ini dibatalkan otomatis oleh sistem dan kursi telah dilepaskan kembali. Silakan buat reservasi baru jika ingin berkunjung.</span>
            </div>
        </div>
    </template>

    <!-- Order & Price Breakdown Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="font-mono text-xs font-bold text-slate-500">Kode Booking:</span>
                <h2 class="text-base sm:text-lg font-black text-slate-900 font-mono">#{{ $booking->booking_code }}</h2>
            </div>
            <span class="px-3 py-1 rounded-xl bg-amber-50 text-amber-700 font-extrabold text-xs border border-amber-200 shadow-2xs">
                Awaiting Payment
            </span>
        </div>

        <!-- Store & Schedule Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <span class="text-slate-400 font-medium block">Toko / Tempat:</span>
                <strong class="text-slate-900 font-bold text-sm block">{{ $booking->store->name }}</strong>
                <p class="text-slate-500">{{ $booking->store->address }}, {{ $booking->store->city }}</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <span class="text-slate-400 font-medium block">Jadwal Reservasi:</span>
                <strong class="text-slate-900 font-bold text-sm block">
                    {{ \Carbon\Carbon::parse($booking->booking_date)->isoFormat('dddd, D MMMM Y') }}
                </strong>
                <p class="text-slate-600 font-mono">
                    {{ substr($booking->slot->start_time ?? '', 0, 5) }} &ndash; {{ substr($booking->slot->end_time ?? '', 0, 5) }} &bull;
                    <span class="font-bold text-slate-800">{{ $booking->seat_count }} Kursi (Pax)</span>
                </p>
            </div>
        </div>

        <!-- Rincian Biaya -->
        <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Rincian Pembayaran</h3>

            <div class="divide-y divide-slate-200/60 text-xs space-y-2">
                <div class="flex justify-between pt-2">
                    <span class="text-slate-600">Tarif per Kursi:</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($booking->price_per_pax_snapshot, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-600">Jumlah Kursi:</span>
                    <span class="font-bold text-slate-800">{{ $booking->seat_count }} Orang</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-600">Total Tarif:</span>
                    <strong class="text-slate-900">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between items-center pt-3 text-sm">
                    <div>
                        <span class="font-black text-slate-900 block">Nominal Wajib Ditransfer Sekarang:</span>
                        <span class="text-[11px] text-blue-600 font-bold">
                            @if($booking->dp_percentage_snapshot >= 100)
                                Bayar Penuh (100% Lunas)
                            @else
                                DP {{ $booking->dp_percentage_snapshot }}% dari total biaya
                            @endif
                        </span>
                    </div>
                    <span class="text-xl sm:text-2xl font-black text-blue-700">
                        Rp {{ number_format($booking->amount_due, 0, ',', '.') }}
                    </span>
                </div>

                @if($booking->dp_percentage_snapshot < 100)
                    <div class="flex justify-between items-center pt-2 text-xs bg-emerald-50/60 p-3 rounded-xl border border-emerald-100">
                        <span class="text-emerald-800 font-medium">Sisa Dibayar di Tempat Saat Datang (Tunai):</span>
                        <strong class="text-emerald-700 font-bold text-sm">
                            Rp {{ number_format(max(0, $booking->total_amount - $booking->amount_due), 0, ',', '.') }}
                        </strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Rekening & QRIS Toko -->
        <div class="space-y-3 pt-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Tujuan Pembayaran Mitra Toko</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($booking->store->bank_account_number)
                    <!-- Rekening Bank -->
                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 space-y-2">
                        <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">Transfer Bank</span>
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-700">{{ $booking->store->bank_name }}</span>
                            <div class="flex items-center justify-between bg-white p-2.5 rounded-xl border border-blue-200">
                                <span class="font-mono text-sm font-black text-slate-900" id="bank-number">{{ $booking->store->bank_account_number }}</span>
                                <button type="button"
                                        @click="copyAccountNumber('{{ $booking->store->bank_account_number }}')"
                                        class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition">
                                    <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-500">Atas Nama: <strong class="text-slate-800">{{ $booking->store->bank_account_holder }}</strong></p>
                        </div>
                    </div>
                @endif

                @if($booking->store->qris_image_path)
                    <!-- QRIS Image -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-center">
                        <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block">Scan QRIS Toko</span>
                        <div class="w-48 h-48 mx-auto bg-white p-2 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-center">
                            <img src="{{ asset('storage/' . $booking->store->qris_image_path) }}"
                                 alt="QRIS {{ $booking->store->name }}"
                                 class="w-full h-full object-contain">
                        </div>
                        <p class="text-[10px] text-slate-400">Scan menggunakan aplikasi e-wallet atau mobile banking apa saja.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Form Upload Bukti Transfer -->
        <div class="pt-4 border-t border-slate-100 space-y-4">
            <div>
                <h3 class="text-sm font-black text-slate-900">Unggah Bukti Transfer</h3>
                <p class="text-xs text-slate-500">Pastikan nominal transfer, nama pengirim, dan tanggal transaksi terbaca jelas.</p>
            </div>

            <form action="{{ route('user.bookings.upload-proof', $booking) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Gambar Bukti Transfer <span class="text-rose-500">*</span>
                    </label>
                    <input type="file"
                           name="payment_proof"
                           id="payment_proof"
                           required
                           :disabled="isExpired"
                           accept="image/png,image/jpeg,image/jpg"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <p class="text-[11px] text-slate-400 mt-1">Format gambar: JPG, JPEG, atau PNG. Ukuran maksimal 2MB.</p>
                    @error('payment_proof')
                        <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3">
                    <a href="{{ route('user.bookings.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Nanti Saja
                    </a>
                    <button type="submit"
                            :disabled="isExpired"
                            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/25 transition hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        Kirim Bukti Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function paymentTimer(initialSeconds) {
    return {
        secondsLeft: initialSeconds,
        isExpired: initialSeconds <= 0,
        timerDisplay: '00:00',
        copied: false,

        init() {
            this.updateDisplay();
            if (this.secondsLeft > 0) {
                const interval = setInterval(() => {
                    this.secondsLeft--;
                    this.updateDisplay();
                    if (this.secondsLeft <= 0) {
                        clearInterval(interval);
                        this.isExpired = true;
                        this.timerDisplay = '00:00';
                    }
                }, 1000);
            }
        },

        updateDisplay() {
            if (this.secondsLeft <= 0) {
                this.timerDisplay = '00:00';
                this.isExpired = true;
                return;
            }

            const m = Math.floor(this.secondsLeft / 60);
            const s = this.secondsLeft % 60;
            this.timerDisplay = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        copyAccountNumber(num) {
            navigator.clipboard.writeText(num).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            });
        }
    }
}
</script>
@endpush

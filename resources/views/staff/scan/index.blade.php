@extends('layouts.staff')

@section('header_title', 'Scan QR Code Check-in')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="scannerApp()">
    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Check-in Pengunjung via QR Code</h2>
            <p class="text-xs text-slate-500 mt-1">Arahkan kamera ke E-Ticket QR milik pelanggan atau masukkan kode token secara manual.</p>
        </div>
        <a href="{{ route('staff.bookings.index', ['status' => 'active']) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition inline-flex items-center space-x-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Daftar Booking Aktif</span>
        </a>
    </div>

    <!-- Scanner Box & Camera Feed -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full" :class="cameraActive ? 'bg-emerald-500 animate-ping' : 'bg-slate-300'"></span>
                <h3 class="text-sm font-bold text-slate-800">Scanner Kamera Realtime</h3>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button"
                        @click="startCamera()"
                        x-show="!cameraActive"
                        class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span>Nyalakan Kamera</span>
                </button>
                <button type="button"
                        @click="stopCamera()"
                        x-show="cameraActive"
                        class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                    <span>Matikan Kamera</span>
                </button>
            </div>
        </div>

        <!-- Kamera Viewport Container -->
        <div class="relative bg-slate-900 rounded-2xl overflow-hidden min-h-[300px] flex items-center justify-center">
            <div id="reader" class="w-full max-w-md"></div>

            <div x-show="!cameraActive" class="text-center p-8 text-slate-400 space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-2xl shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <p class="font-bold text-sm text-slate-300">Kamera Sedang Nonaktif</p>
                    <p class="text-xs text-slate-500 mt-0.5">Klik tombol "Nyalakan Kamera" di atas atau gunakan input manual di bawah.</p>
                </div>
            </div>
        </div>

        <p class="text-[11px] text-slate-400 italic">
            * Akses kamera browser membutuhkan izin (permission) dan konteks aman (HTTPS atau localhost).
        </p>

        <!-- Fallback Input Manual -->
        <div class="pt-5 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Fallback Input Manual</h4>
                <span class="text-[11px] text-slate-400">Gunakan jika kamera bermasalah</span>
            </div>

            <form @submit.prevent="submitManualToken" class="flex flex-col sm:flex-row gap-2">
                <input type="text"
                       x-model="manualToken"
                       placeholder="Tempel / ketik token QR atau Kode Booking (cth: IFD-20260919-XXXXX)..."
                       class="flex-1 text-xs font-mono rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3.5">
                <button type="submit"
                        :disabled="processing"
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-sm shrink-0 flex items-center justify-center space-x-1.5 disabled:opacity-50">
                    <span x-show="!processing">Proses Check-in</span>
                    <span x-show="processing">Memverifikasi...</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Check-in Success Result Modal -->
    <div x-show="successModalOpen"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         @keydown.escape.window="successModalOpen = false">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5"
             @click.outside="successModalOpen = false">
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-lg font-black text-slate-900">Check-in Berhasil!</h3>
                <p class="text-xs text-emerald-700 font-bold" x-text="resultMessage"></p>
            </div>

            <template x-if="checkInData">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3 text-xs">
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-medium">Kode Reservasi:</span>
                        <strong class="font-mono text-slate-900 font-bold" x-text="'#' + checkInData.booking_code"></strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-medium">Nama Tamu:</span>
                        <strong class="text-slate-900 font-bold" x-text="checkInData.customer_name"></strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-medium">Jumlah Kursi (Pax):</span>
                        <strong class="text-slate-900 font-bold" x-text="checkInData.seat_count + ' Orang'"></strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-medium">Sesi Waktu:</span>
                        <strong class="text-blue-700 font-mono font-bold" x-text="checkInData.time_session"></strong>
                    </div>

                    <!-- Payment Status Highlight based on remaining_payment_status -->
                    <div class="pt-1">
                        <!-- Case 1: not_required (Store with 100% DP or no remaining payment required) -->
                        <template x-if="checkInData.remaining_payment_status === 'not_required' || (checkInData.remaining_payment_status === null && checkInData.remaining_to_pay <= 0)">
                            <div class="p-3 bg-emerald-100/70 border border-emerald-300 rounded-xl flex items-center justify-between">
                                <span class="font-bold text-emerald-800">Status Pembayaran:</span>
                                <span class="px-2.5 py-0.5 rounded-lg bg-emerald-600 text-white font-black text-xs">
                                    LUNAS (100% DP)
                                </span>
                            </div>
                        </template>

                        <!-- Case 2: paid (Remaining payment already settled) -->
                        <template x-if="checkInData.remaining_payment_status === 'paid'">
                            <div class="p-3 bg-emerald-50 border border-emerald-300 rounded-xl space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-emerald-800 uppercase text-[10px]">Sisa Pembayaran:</span>
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-600 text-white font-black text-[10px]">
                                        SUDAH LUNAS
                                    </span>
                                </div>
                                <div class="text-xs text-emerald-800 font-semibold flex items-center justify-between">
                                    <span>Metode: <strong class="uppercase" x-text="checkInData.remaining_payment_method === 'cash' ? 'Tunai di Toko' : (checkInData.remaining_payment_method || 'Online')"></strong></span>
                                    <span class="text-[11px] text-emerald-600" x-show="checkInData.remaining_verified_at" x-text="checkInData.remaining_verified_at"></span>
                                </div>
                            </div>
                        </template>

                        <!-- Case 3: pending_verification (Customer uploaded remaining proof online) -->
                        <template x-if="checkInData.remaining_payment_status === 'pending_verification'">
                            <div class="p-3 bg-blue-50 border border-blue-300 rounded-xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-blue-800 uppercase text-[10px]">Sisa Pembayaran:</span>
                                    <span class="px-2 py-0.5 rounded-md bg-blue-600 text-white font-black text-[10px]">
                                        MENUNGGU VERIFIKASI
                                    </span>
                                </div>
                                <p class="text-xs text-blue-700 font-medium">User sudah upload bukti pelunasan secara online &mdash; Menunggu verifikasi staf.</p>
                                <div class="pt-1">
                                    <a :href="'{{ route('staff.bookings.index') }}?status=remaining'" target="_blank"
                                       class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-xs">
                                        <span>Buka Menu Verifikasi Pelunasan</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </template>

                        <!-- Case 4: unpaid (Remaining payment unpaid) -->
                        <template x-if="checkInData.remaining_payment_status === 'unpaid' || (!checkInData.remaining_payment_status && checkInData.remaining_to_pay > 0)">
                            <div class="p-3.5 bg-amber-50 border border-amber-300 rounded-xl space-y-3" x-data="{ showCashInput: false, cashAmount: checkInData.remaining_amount || checkInData.remaining_to_pay, cashProcessing: false, cashError: '' }">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-amber-900 uppercase text-[10px]">Sisa Pembayaran:</span>
                                    <span class="px-2 py-0.5 rounded-md bg-amber-600 text-white font-black text-[10px]">
                                        WAJIB DITAGIH DI LOKASI
                                    </span>
                                </div>
                                <div>
                                    <div class="text-xl font-black text-amber-900" x-text="formatRupiah(checkInData.remaining_amount || checkInData.remaining_to_pay)"></div>
                                    <p class="text-[11px] text-amber-700 mt-0.5">Tagih pelunasan sisa tagihan langsung di kasir / meja reservasi.</p>
                                </div>

                                <!-- Inline Cash Confirmation Form -->
                                <div class="pt-2 border-t border-amber-200/80">
                                    <template x-if="!showCashInput">
                                        <button type="button"
                                                @click="showCashInput = true; cashAmount = checkInData.remaining_amount || checkInData.remaining_to_pay"
                                                class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition shadow-xs flex items-center justify-center space-x-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            <span>Konfirmasi Sudah Dibayar Tunai</span>
                                        </button>
                                    </template>

                                    <template x-if="showCashInput">
                                        <div class="space-y-2">
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Nominal Tunai Diterima (Rp):</label>
                                                <input type="number"
                                                       x-model="cashAmount"
                                                       class="w-full text-xs font-mono font-bold rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 py-1.5 px-2.5">
                                            </div>
                                            <template x-if="cashError">
                                                <p class="text-[10px] text-rose-600 font-bold" x-text="cashError"></p>
                                            </template>
                                            <div class="flex items-center space-x-2">
                                                <button type="button"
                                                        @click="confirmCashPayment(checkInData.id, cashAmount, () => { cashProcessing = true; }, () => { cashProcessing = false; }, (err) => { cashError = err; })"
                                                        :disabled="cashProcessing"
                                                        class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-xs font-bold rounded-lg transition">
                                                    <span x-show="!cashProcessing">Simpan & Tandai Lunas</span>
                                                    <span x-show="cashProcessing">Menyimpan...</span>
                                                </button>
                                                <button type="button"
                                                        @click="showCashInput = false; cashError = ''"
                                                        class="py-1.5 px-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-lg transition">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="pt-2 text-[11px] text-slate-500">
                        <strong class="block text-slate-700 font-bold mb-0.5">Catatan Booking:</strong>
                        <span class="italic" x-text="checkInData.notes"></span>
                    </div>
                </div>
            </template>

            <button type="button"
                    @click="closeSuccessModal()"
                    class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm">
                Tutup & Siap Scan Berikutnya
            </button>
        </div>
    </div>

    <!-- Error Alert Box -->
    <template x-if="errorMessage">
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 flex items-start justify-between gap-3 shadow-xs">
            <div class="flex items-start space-x-2.5">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <strong class="font-bold block">Gagal Melakukan Check-in:</strong>
                    <span x-text="errorMessage"></span>
                </div>
            </div>
            <button type="button" @click="errorMessage = ''" class="text-rose-400 hover:text-rose-600 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<!-- HTML5 QR Code CDN Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
function scannerApp() {
    return {
        cameraActive: false,
        html5QrCode: null,
        manualToken: '',
        processing: false,
        successModalOpen: false,
        resultMessage: '',
        checkInData: null,
        errorMessage: '',

        formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        },

        startCamera() {
            this.errorMessage = '';
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            this.html5QrCode = new Html5Qrcode("reader");
            this.html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText, decodedResult) => {
                    this.onQrCodeScanned(decodedText);
                },
                (error) => {
                    // Scanning errors (e.g. no QR in frame), ignored
                }
            ).then(() => {
                this.cameraActive = true;
            }).catch(err => {
                console.error("Camera error:", err);
                this.errorMessage = "Tidak dapat mengakses kamera: " + (err.message || err) + ". Pastikan izin kamera telah diberikan di browser.";
                this.cameraActive = false;
            });
        },

        stopCamera() {
            if (this.html5QrCode && this.cameraActive) {
                this.html5QrCode.stop().then(() => {
                    this.cameraActive = false;
                }).catch(err => {
                    console.error("Failed to stop camera:", err);
                });
            }
        },

        onQrCodeScanned(qrText) {
            if (this.processing) return;
            // Temporarily stop scanning to prevent double scan
            this.stopCamera();
            this.sendCheckInRequest(qrText);
        },

        submitManualToken() {
            if (!this.manualToken.trim()) {
                this.errorMessage = "Silakan ketikkan token atau kode booking terlebih dahulu.";
                return;
            }
            this.sendCheckInRequest(this.manualToken.trim());
        },

        sendCheckInRequest(token) {
            this.processing = true;
            this.errorMessage = '';

            fetch("{{ route('staff.scan.check-in') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ qr_token: token })
            })
            .then(res => res.json().then(data => ({ status: res.status, data: data })))
            .then(result => {
                this.processing = false;
                if (result.status === 200 && result.data.success) {
                    this.checkInData = result.data.data;
                    this.resultMessage = result.data.message;
                    this.successModalOpen = true;
                    this.manualToken = '';
                } else {
                    this.errorMessage = result.data.message || "Terjadi kesalahan saat memproses check-in.";
                }
            })
            .catch(err => {
                this.processing = false;
                console.error("Check-in error:", err);
                this.errorMessage = "Gagal menghubungi server. Periksa koneksi internet Anda.";
            });
        },

        confirmCashPayment(bookingId, amount, onStart, onEnd, onError) {
            onStart();
            fetch(`/staff/bookings/${bookingId}/cash-remaining`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    remaining_amount_received: amount,
                    amount_received: amount
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, data: data })))
            .then(result => {
                onEnd();
                if (result.status === 200 && result.data.success) {
                    if (this.checkInData) {
                        this.checkInData.remaining_payment_status = 'paid';
                        this.checkInData.remaining_payment_method = 'cash';
                        this.checkInData.remaining_to_pay = 0;
                        this.checkInData.remaining_verified_at = 'Baru saja';
                    }
                    this.resultMessage = result.data.message || 'Sisa pembayaran tunai berhasil dikonfirmasi!';
                } else {
                    onError(result.data.message || 'Gagal mengonfirmasi pelunasan tunai.');
                }
            })
            .catch(err => {
                onEnd();
                console.error("Cash payment error:", err);
                onError("Terjadi kesalahan koneksi saat memproses pelunasan tunai.");
            });
        },

        closeSuccessModal() {
            this.successModalOpen = false;
            this.checkInData = null;
        }
    }
}
</script>
@endpush

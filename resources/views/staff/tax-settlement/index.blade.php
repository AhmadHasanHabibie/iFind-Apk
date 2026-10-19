@extends('layouts.staff')

@section('header_title', 'Penyetoran Pajak & Biaya Layanan')

@section('content')
<div class="space-y-6" x-data="{
    proofModalOpen: false,
    proofModalImage: '',
    previewUrl: null,
    amount: {{ old('amount', $outstandingAmount > 0 ? (int)$outstandingAmount : "''") }},
    copied: false,
    copyAccount(text) {
        navigator.clipboard.writeText(text);
        this.copied = true;
        setTimeout(() => this.copied = false, 2000);
    },
    formatRupiah(val) {
        if (!val || isNaN(val)) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
    }
}">
    <!-- Header Page Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 rounded-3xl p-6 sm:p-8 text-white shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-slate-800">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 mb-3">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                <span>Penyetoran Komisi Platform 5%</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Setor Pajak & Biaya Layanan via QRIS</h2>
            <p class="text-slate-300 text-xs sm:text-sm mt-1.5 max-w-2xl leading-relaxed">
                Biaya layanan 5% yang dibayarkan oleh pelanggan ke rekening toko Anda disetorkan kembali ke rekening atau QRIS resmi admin platform iFind.
            </p>
        </div>
        <div class="shrink-0 bg-slate-800/80 backdrop-blur-md rounded-2xl p-5 border border-slate-700/80 text-center min-w-[220px] shadow-sm">
            <p class="text-[11px] uppercase tracking-wider text-slate-300 font-bold">Sisa Tagihan Belum Disetor</p>
            <p class="text-2xl sm:text-3xl font-black {{ $outstandingAmount > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1">
                Rp {{ number_format($outstandingAmount, 0, ',', '.') }}
            </p>
            <div class="mt-1 flex items-center justify-center gap-1.5 text-[11px] text-slate-300">
                <span class="w-1.5 h-1.5 rounded-full {{ $outstandingAmount > 0 ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>
                <span>{{ $outstandingAmount > 0 ? 'Perlu Disetor' : 'Lunas (Tidak Ada Tagihan)' }}</span>
            </div>
        </div>
    </div>

    <!-- 4 Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Pajak Terkumpul -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pajak Terkumpul (5%)</p>
                <h3 class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($totalLiability, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Dari booking terkonfirmasi</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <!-- 2. Sudah Disetor & Terverifikasi -->
        <div class="bg-white p-5 rounded-3xl border border-emerald-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Sudah Diverifikasi</p>
                <h3 class="text-xl font-black text-emerald-950 mt-1">Rp {{ number_format($settledAmount, 0, ',', '.') }}</h3>
                <p class="text-xs text-emerald-600 mt-0.5">Setoran disetujui admin</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- 3. Sedang Menunggu Verifikasi -->
        <div class="bg-white p-5 rounded-3xl border border-blue-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-blue-800">Menunggu Verifikasi</p>
                <h3 class="text-xl font-black text-blue-950 mt-1">Rp {{ number_format($pendingAmount, 0, ',', '.') }}</h3>
                <p class="text-xs text-blue-600 mt-0.5">Sedang ditinjau admin</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- 4. Sisa Tagihan Pajak -->
        <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5 rounded-3xl border border-amber-300 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-900">Sisa Tagihan Pajak</p>
                <h3 class="text-xl font-black text-amber-950 mt-1">Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</h3>
                <p class="text-xs text-amber-700 mt-0.5">{{ $outstandingAmount > 0 ? 'Perlu disetorkan' : 'Semua pajak telah lunas' }}</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- 2 Column Section: QRIS Admin Info (Left) & Form Setor (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Kolom Kiri: QRIS & Rekening Resmi Admin (5 Kolom) -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">QRIS Resmi Admin iFind</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pindai menggunakan aplikasi m-Banking atau E-Wallet.</p>
                </div>
                <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded-lg bg-teal-50 text-teal-700 border border-teal-200">
                    QRIS Universal
                </span>
            </div>

            <!-- Tampilan Frame QRIS -->
            <div class="p-4 bg-gradient-to-b from-slate-50 to-slate-100 rounded-2xl border border-slate-200/80 flex flex-col items-center">
                @if($adminQrisUrl)
                    <div class="relative group cursor-pointer max-w-[280px] w-full" @click="proofModalImage = '{{ $adminQrisUrl }}'; proofModalOpen = true">
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200/90 hover:shadow-md transition">
                            <img src="{{ $adminQrisUrl }}" alt="QRIS Admin iFind" class="w-full h-auto object-contain rounded-lg">
                        </div>
                        <div class="absolute inset-0 bg-slate-900/40 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5 backdrop-blur-[2px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                            <span>Klik untuk Perbesar</span>
                        </div>
                    </div>
                    <div class="mt-3.5 flex items-center gap-3">
                        <button type="button" @click="proofModalImage = '{{ $adminQrisUrl }}'; proofModalOpen = true" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>Layar Penuh</span>
                        </button>
                        <span class="text-slate-300">•</span>
                        <a href="{{ $adminQrisUrl }}" download="QRIS-Admin-iFind.jpg" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh QRIS</span>
                        </a>
                    </div>
                @else
                    <div class="w-56 h-56 rounded-xl bg-slate-200 flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span class="text-xs font-semibold">QRIS Resmi belum diunggah oleh admin</span>
                    </div>
                @endif
            </div>

            <!-- Rekening Alternatif Transfer Bank -->
            <div class="bg-gradient-to-br from-blue-50/80 via-slate-50 to-blue-50/40 border border-blue-200/80 rounded-2xl p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-blue-900 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        Rekening Bank Alternatif
                    </span>
                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-md bg-blue-100 text-blue-800">{{ $adminBankName }}</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <div>
                        <p class="text-xl font-black text-slate-900 tracking-wider font-mono">{{ $adminBankAccountNumber }}</p>
                        <p class="text-xs text-slate-600 font-semibold mt-0.5">a.n. {{ $adminBankAccountHolder }}</p>
                    </div>
                    <button type="button"
                            @click="copyAccount('{{ $adminBankAccountNumber }}')"
                            class="px-3 py-2 rounded-xl bg-white hover:bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200 shadow-xs transition flex items-center gap-1.5 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                    </button>
                </div>
            </div>

            <!-- Panduan Singkat -->
            <div class="text-xs text-slate-500 space-y-1.5 pt-1">
                <p class="font-bold text-slate-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Panduan Penyetoran Pajak:</span>
                </p>
                <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-600 pl-1">
                    <li>Scan barcode QRIS di atas via aplikasi Mobile Banking atau E-Wallet.</li>
                    <li>Pastikan nominal yang ditransfer sesuai dengan sisa tagihan pajak Anda.</li>
                    <li>Simpan struk / screenshot bukti transfer dan unggah pada formulir.</li>
                    <li>Administrator akan memverifikasi setoran dalam waktu 1x24 jam kerja.</li>
                </ol>
            </div>
        </div>

        <!-- Kolom Kanan: Form Penyetoran Pajak (7 Kolom) -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900">Formulir Konfirmasi Setor Pajak</h3>
                <p class="text-xs text-slate-500 mt-0.5">Unggah bukti screenshot/struk transfer setelah melakukan pembayaran ke QRIS Admin.</p>
            </div>

            <form method="POST" action="{{ route('staff.tax-settlements.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Nominal Setoran (Input Group yang Rapi & Bebas Overlap) -->
                <div>
                    <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nominal Transfer (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex rounded-2xl border border-slate-200 overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-blue-600 focus-within:border-blue-600 bg-white transition">
                        <span class="inline-flex items-center px-4 bg-slate-100 text-slate-700 font-extrabold text-sm border-r border-slate-200 select-none">
                            Rp
                        </span>
                        <input type="number"
                               id="amount"
                               name="amount"
                               min="1000"
                               step="100"
                               x-model.number="amount"
                               required
                               placeholder="0"
                               class="w-full px-4 py-3 text-base font-bold text-slate-900 border-0 focus:ring-0 focus:outline-none placeholder:text-slate-300">
                    </div>
                    <div class="flex items-center justify-between text-xs mt-1.5">
                        <span class="text-slate-500">Terbaca: <strong class="text-blue-700" x-text="formatRupiah(amount)"></strong></span>
                        @if($outstandingAmount > 0)
                            <button type="button" @click="amount = {{ (int)$outstandingAmount }}" class="text-blue-600 font-bold hover:underline">
                                Pakai Sisa Tagihan (Rp {{ number_format($outstandingAmount, 0, ',', '.') }})
                            </button>
                        @endif
                    </div>
                    @error('amount')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unggah Bukti Transfer -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Bukti Transfer QRIS / Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-6 text-center transition bg-slate-50/70 relative">
                        <input type="file"
                               name="proof_image"
                               id="proof_image"
                               accept="image/png, image/jpeg, image/jpg"
                               required
                               @change="const file = $event.target.files[0]; if (file) { previewUrl = URL.createObjectURL(file); }"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <template x-if="!previewUrl">
                            <div class="space-y-2 pointer-events-none">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto shadow-xs border border-blue-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="text-xs text-slate-700">
                                    <span class="font-extrabold text-blue-600">Klik untuk upload bukti transfer</span> atau seret file ke sini
                                </div>
                                <p class="text-[11px] text-slate-400 font-medium">Format gambar: JPG, JPEG, PNG (Maksimal 2MB)</p>
                            </div>
                        </template>

                        <template x-if="previewUrl">
                            <div class="space-y-2 pointer-events-none">
                                <div class="relative inline-block">
                                    <img :src="previewUrl" alt="Pratinjau Bukti" class="max-h-48 mx-auto rounded-xl shadow-sm border border-slate-200 bg-white p-1">
                                </div>
                                <p class="text-xs font-bold text-blue-600 flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span>Klik untuk mengganti foto bukti</span>
                                </p>
                            </div>
                        </template>
                    </div>
                    @error('proof_image')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Catatan Staf (Opsional)
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="2"
                              placeholder="Misal: Penyetoran pajak booking minggu ke-3 Oktober 2026..."
                              class="w-full text-xs rounded-2xl border-slate-200 focus:border-blue-600 focus:ring-blue-600">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3.5 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-md shadow-blue-600/25 transition-all flex items-center justify-center gap-2 hover:shadow-lg hover:shadow-blue-600/30 active:scale-[0.99] cursor-pointer">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Kirim Bukti Pembayaran ke Admin</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Penyetoran Pajak Toko -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Riwayat Penyetoran Pajak Toko</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar semua bukti transfer setoran pajak yang pernah dikirimkan ke admin.</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-xl">
                Total: {{ $settlements->total() }} Transaksi
            </span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-100">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-400 bg-slate-50/70">
                        <th class="py-3 px-4">Kode Setoran</th>
                        <th class="py-3 px-4">Tanggal Pengajuan</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4 text-center">Bukti Transfer</th>
                        <th class="py-3 px-4">Status Verifikasi</th>
                        <th class="py-3 px-4">Catatan / Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($settlements as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $item->settlement_code }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900 text-sm">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->proof_url)
                                    <button type="button"
                                            @click="proofModalImage = '{{ $item->proof_url }}'; proofModalOpen = true"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Lihat Bukti</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span>Disetujui Admin</span>
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        <span>Ditolak Admin</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Menunggu Verifikasi</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                @if($item->notes)
                                    <p class="text-slate-600"><span class="font-bold text-slate-800">Catatan Staf:</span> {{ $item->notes }}</p>
                                @endif
                                @if($item->status === 'rejected' && $item->admin_notes)
                                    <p class="text-rose-600 font-bold mt-0.5">Alasan Penolakan: {{ $item->admin_notes }}</p>
                                @endif
                                @if($item->verified_at)
                                    <p class="text-[10px] text-slate-400 mt-0.5">Diverifikasi: {{ $item->verified_at->translatedFormat('d M Y H:i') }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-2 border border-slate-100">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="font-bold text-slate-600 text-xs">Belum ada riwayat penyetoran pajak</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Semua riwayat transfer pembayaran QRIS ke admin akan tercatat di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($settlements->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $settlements->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Preview Gambar Bukti / QRIS -->
    <div x-show="proofModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
         @click.self="proofModalOpen = false"
         @keydown.escape.window="proofModalOpen = false">
        <div class="relative bg-white rounded-3xl p-4 sm:p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="text-base font-extrabold text-slate-900">Pratinjau Gambar</h4>
                <button type="button" @click="proofModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex justify-center bg-slate-50 p-2 rounded-2xl border">
                <img :src="proofModalImage" alt="Preview Image" class="max-h-[70vh] object-contain rounded-xl">
            </div>
            <div class="flex justify-end">
                <button type="button" @click="proofModalOpen = false" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

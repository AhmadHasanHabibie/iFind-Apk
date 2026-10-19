@extends('layouts.admin')

@section('header_title', 'Pengaturan QRIS & Rekening Admin')

@section('content')
<div class="space-y-6" x-data="{
    qrisPreviewUrl: '{{ $adminQrisUrl ?? '' }}'
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-xs">
                    <i class="fa-solid fa-qrcode"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan QRIS & Rekening Admin</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola gambar barcode QRIS dan rekening bank resmi platform sebagai tujuan transfer pajak staf toko.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto text-xs font-bold">
        <a href="{{ route('admin.commissions.index') }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80">
            <i class="fa-solid fa-list-check text-slate-500"></i>
            <span>Rekap Booking & Transaksi (5%)</span>
        </a>
        <a href="{{ route('admin.tax-settlements.index') }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80">
            <i class="fa-solid fa-file-invoice-dollar text-slate-500"></i>
            <span>Verifikasi Setoran Toko</span>
            @php $pCount = \App\Models\TaxSettlement::where('status', 'pending')->count(); @endphp
            @if($pCount > 0)
                <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-400 text-slate-950 font-black animate-pulse">{{ $pCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.tax-settlements.settings') }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2 bg-teal-600 text-white shadow-xs">
            <i class="fa-solid fa-qrcode"></i>
            <span>Pengaturan QRIS & Rekening Admin</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.tax-settlements.settings.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf

        <!-- Kolom Kiri: QRIS Resmi (5 Kolom) -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900">Gambar QRIS Platform</h3>
                <p class="text-xs text-slate-500 mt-0.5">Barcode QRIS resmi yang akan ditampilkan di layar dashboard semua pemilik toko mitra.</p>
            </div>

            <!-- Tampilan Box QRIS & Upload -->
            <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 relative group">
                <template x-if="qrisPreviewUrl">
                    <div class="space-y-3 text-center">
                        <img :src="qrisPreviewUrl" alt="QRIS Admin" class="max-w-[240px] w-full h-auto object-contain rounded-xl bg-white p-2 shadow-xs border mx-auto">
                        <p class="text-xs font-bold text-teal-700">QRIS Aktif Saat Ini</p>
                    </div>
                </template>
                <template x-if="!qrisPreviewUrl">
                    <div class="py-12 text-center text-slate-400 space-y-2">
                        <i class="fa-solid fa-qrcode text-4xl text-slate-300"></i>
                        <p class="text-xs font-semibold">Belum ada gambar QRIS diunggah</p>
                    </div>
                </template>

                <div class="mt-4 w-full">
                    <label for="admin_qris_image" class="block text-center py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs cursor-pointer shadow-xs transition">
                        <i class="fa-solid fa-upload mr-1.5"></i>
                        <span>Pilih Gambar QRIS Baru</span>
                    </label>
                    <input type="file"
                           id="admin_qris_image"
                           name="admin_qris_image"
                           accept="image/png, image/jpeg, image/jpg"
                           class="hidden"
                           @change="const file = $event.target.files[0]; if (file) { qrisPreviewUrl = URL.createObjectURL(file); }">
                    <p class="text-[11px] text-slate-400 text-center mt-2">Mendukung format PNG, JPG, JPEG (Maks. 2MB)</p>
                </div>
            </div>
            @error('admin_qris_image')
                <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kolom Kanan: Detail Rekening Bank & Tombol Simpan (7 Kolom) -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900">Rekening Penerima Alternatif</h3>
                <p class="text-xs text-slate-500 mt-0.5">Informasi nomor rekening bank resmi admin jika staf ingin transfer via mobile/internet banking manual.</p>
            </div>

            <div class="space-y-4">
                <!-- Nama Bank -->
                <div>
                    <label for="admin_bank_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Bank Admin <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="admin_bank_name"
                           name="admin_bank_name"
                           value="{{ old('admin_bank_name', $adminBankName) }}"
                           required
                           placeholder="Cth: Bank Central Asia (BCA) / Mandiri / BRI"
                           class="w-full px-4 py-2.5 text-xs font-bold rounded-xl border-slate-200 focus:ring-teal-500 focus:border-teal-500">
                    @error('admin_bank_name')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rekening -->
                <div>
                    <label for="admin_bank_account_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor Rekening Admin <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="admin_bank_account_number"
                           name="admin_bank_account_number"
                           value="{{ old('admin_bank_account_number', $adminBankAccountNumber) }}"
                           required
                           placeholder="Cth: 8735029188"
                           class="w-full px-4 py-2.5 text-xs font-bold font-mono rounded-xl border-slate-200 focus:ring-teal-500 focus:border-teal-500">
                    @error('admin_bank_account_number')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Atas Nama / Pemilik Rekening -->
                <div>
                    <label for="admin_bank_account_holder" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Atas Nama Pemilik Rekening <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="admin_bank_account_holder"
                           name="admin_bank_account_holder"
                           value="{{ old('admin_bank_account_holder', $adminBankAccountHolder) }}"
                           required
                           placeholder="Cth: PT iFind Layanan Indonesia / Administrator"
                           class="w-full px-4 py-2.5 text-xs font-bold rounded-xl border-slate-200 focus:ring-teal-500 focus:border-teal-500">
                    @error('admin_bank_account_holder')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Alert -->
                <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200 text-teal-900 text-xs space-y-1">
                    <p class="font-extrabold flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-teal-600"></i>
                        <span>Pemberitahuan Sinkronisasi Real-Time</span>
                    </p>
                    <p class="text-teal-800 leading-relaxed text-[11px]">
                        Perubahan gambar QRIS dan nomor rekening di sini akan langsung tampil pada halaman <strong>"Setor Pajak (5%)"</strong> di seluruh dashboard akun staf toko yang terdaftar.
                    </p>
                </div>

                <div class="pt-3">
                    <button type="submit"
                            class="w-full py-3 px-6 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs shadow-md shadow-teal-600/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Pengaturan Pembayaran Admin</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

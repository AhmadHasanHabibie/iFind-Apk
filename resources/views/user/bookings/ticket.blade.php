@extends('layouts.user')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <!-- Back to bookings -->
    <div class="flex items-center justify-between">
        <a href="{{ route('user.bookings.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition group">
            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            <span>Kembali ke Pesanan Saya</span>
        </a>

        <button onclick="window.print()" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
            <i class="fa-solid fa-print"></i>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center space-x-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center space-x-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- E-Ticket Main Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden print:shadow-none print:border-slate-400">
        <!-- Header Ribbon -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 p-6 sm:p-7 text-white text-center space-y-1 relative">
            <div class="flex items-center justify-center space-x-2 mb-1">
                <span class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center font-black text-xs">i</span>
                <span class="text-sm font-extrabold tracking-tight">i-Find Electronic Pass</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight">E-TICKET CHECK-IN</h1>
            <p class="text-xs text-blue-100">Tunjukkan QR Code ini kepada staf toko saat tiba di lokasi</p>

            @if($booking->status === 'checked_in')
                <div class="mt-3 inline-flex items-center px-3.5 py-1 rounded-full bg-emerald-500/25 border border-emerald-300 text-white font-bold text-xs">
                    <i class="fa-solid fa-circle-check mr-1.5"></i>
                    <span>Sudah Check-in pada {{ $booking->checked_in_at ? $booking->checked_in_at->isoFormat('D MMM Y, HH:mm') : 'Lokasi' }}</span>
                </div>
            @elseif($booking->status === 'completed')
                <div class="mt-3 inline-flex items-center px-3.5 py-1 rounded-full bg-slate-800/40 border border-white/20 text-white font-bold text-xs">
                    <i class="fa-solid fa-flag-checkered mr-1.5"></i>
                    <span>Kunjungan Selesai</span>
                </div>
            @else
                <div class="mt-3 inline-flex items-center px-3.5 py-1 rounded-full bg-white/20 border border-white/30 text-white font-bold text-xs">
                    <i class="fa-solid fa-qrcode mr-1.5"></i>
                    <span>Tiket Aktif &bull; Siap Digunakan</span>
                </div>
            @endif
        </div>

        <!-- QR Code Container -->
        <div class="p-6 sm:p-8 flex flex-col items-center justify-center bg-slate-50/50 border-b border-dashed border-slate-200 relative">
            <!-- Cutout Circles for ticket effect -->
            <div class="absolute -left-3 bottom-0 translate-y-1/2 w-6 h-6 rounded-full bg-slate-100 border border-slate-200/90 print:hidden"></div>
            <div class="absolute -right-3 bottom-0 translate-y-1/2 w-6 h-6 rounded-full bg-slate-100 border border-slate-200/90 print:hidden"></div>

            <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-md flex items-center justify-center max-w-[260px] w-full aspect-square">
                {!! $qrCodeSvg !!}
            </div>

            <div class="mt-4 text-center">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block">Kode Reservasi</span>
                <span class="font-mono text-base font-black text-slate-800 tracking-wider">#{{ $booking->booking_code }}</span>
            </div>
        </div>

        <!-- Ticket Metadata Body -->
        <div class="p-6 sm:p-8 space-y-6">
            <!-- Store Details -->
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tempat / Spot Kunjungan</span>
                <h3 class="text-lg font-black text-slate-900 mt-0.5">{{ $booking->store->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $booking->store->address }}, {{ $booking->store->city }}</p>
            </div>

            <!-- Schedule & Seat Details -->
            <div class="grid grid-cols-2 gap-4 text-xs pt-4 border-t border-slate-100">
                <div class="space-y-1">
                    <span class="text-slate-400 font-medium block">Tanggal Kunjungan</span>
                    <strong class="text-slate-900 font-bold block text-sm">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->isoFormat('dddd, D MMMM Y') }}
                    </strong>
                </div>
                <div class="space-y-1">
                    <span class="text-slate-400 font-medium block">Sesi Waktu</span>
                    <strong class="text-blue-700 font-mono font-bold block text-sm">
                        {{ substr($booking->slot->start_time ?? '', 0, 5) }} &ndash; {{ substr($booking->slot->end_time ?? '', 0, 5) }}
                    </strong>
                </div>
                <div class="space-y-1">
                    <span class="text-slate-400 font-medium block">Nama Pemesan</span>
                    <strong class="text-slate-900 font-bold block">{{ $booking->user->name }}</strong>
                </div>
                <div class="space-y-1">
                    <span class="text-slate-400 font-medium block">Kapasitas Kursi</span>
                    <strong class="text-slate-900 font-bold block">{{ $booking->seat_count }} Orang (Pax)</strong>
                </div>
            </div>

            <!-- Payment Status in Ticket -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Status Pembayaran</span>
                    @if($booking->is_paid_in_full)
                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-black bg-emerald-100 text-emerald-800">
                            LUNAS
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-black bg-blue-100 text-blue-800">
                            DP TERBAYAR ({{ $booking->dp_percentage_snapshot }}%)
                        </span>
                    @endif
                </div>

                <div class="divide-y divide-slate-200/60 pt-1 space-y-1.5">
                    <div class="flex justify-between text-slate-600 pt-1">
                        <span>Total Biaya Reservasi:</span>
                        <strong class="text-slate-800">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between text-slate-600 pt-1">
                        <span>Sudah Ditransfer:</span>
                        <strong class="text-blue-700">Rp {{ number_format($booking->amount_due, 0, ',', '.') }}</strong>
                    </div>

                    @if(! $booking->is_paid_in_full)
                        <div class="flex justify-between items-center text-amber-900 pt-1.5 font-bold">
                            <span>Sisa Pembayaran:</span>
                            <span class="text-sm font-black text-amber-700">
                                Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section Pelunasan Sisa Pembayaran (Hanya jika ada sisa) -->
            @if(in_array($booking->status, ['confirmed', 'checked_in']) && $booking->remaining_payment_status !== 'not_required')
                <div class="p-5 rounded-2xl border transition-all space-y-4 {{ $booking->remaining_payment_status === 'paid' ? 'bg-emerald-50/60 border-emerald-200' : ($booking->remaining_payment_status === 'pending_verification' ? 'bg-blue-50/60 border-blue-200' : 'bg-amber-50/60 border-amber-200') }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $booking->remaining_payment_status === 'paid' ? 'bg-emerald-500' : ($booking->remaining_payment_status === 'pending_verification' ? 'bg-blue-500 animate-pulse' : 'bg-amber-500') }}"></span>
                            <h4 class="text-xs font-black uppercase tracking-wider {{ $booking->remaining_payment_status === 'paid' ? 'text-emerald-900' : ($booking->remaining_payment_status === 'pending_verification' ? 'text-blue-900' : 'text-amber-900') }}">
                                Pelunasan Sisa Pembayaran
                            </h4>
                        </div>

                        @if($booking->remaining_payment_status === 'paid')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white shadow-xs">
                                <i class="fa-solid fa-check mr-1"></i> Lunas
                            </span>
                        @elseif($booking->remaining_payment_status === 'pending_verification')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white shadow-xs">
                                Menunggu Verifikasi Pelunasan oleh Toko
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-600 text-white shadow-xs">
                                Belum Lunas
                            </span>
                        @endif
                    </div>

                    @if($booking->remaining_payment_status === 'paid')
                        <div class="p-3 bg-white/80 rounded-xl border border-emerald-200 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Metode Pelunasan:</span>
                                <strong class="text-slate-800">{{ $booking->remaining_payment_method === 'cash' ? 'Tunai di Lokasi' : 'Transfer / QRIS' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nominal Diterima:</span>
                                <strong class="text-emerald-700">Rp {{ number_format($booking->remaining_amount_received ?? $booking->remaining_amount, 0, ',', '.') }}</strong>
                            </div>
                            @if($booking->remaining_verified_at)
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Waktu Verifikasi:</span>
                                    <span class="text-slate-700">{{ $booking->remaining_verified_at->isoFormat('D MMMM Y, HH:mm') }}</span>
                                </div>
                            @endif
                        </div>
                    @elseif($booking->remaining_payment_status === 'pending_verification')
                        <div class="p-3.5 bg-white/90 rounded-xl border border-blue-200 text-xs space-y-2">
                            <p class="text-slate-700">
                                Bukti pelunasan Anda sebesar <strong class="text-blue-700 font-black">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</strong> telah diunggah pada <strong>{{ $booking->remaining_uploaded_at?->isoFormat('D MMM Y, HH:mm') }}</strong> dan sedang ditinjau oleh pihak toko.
                            </p>
                            @if($booking->remaining_proof_path)
                                <a href="{{ asset('storage/' . $booking->remaining_proof_path) }}" target="_blank" class="inline-flex items-center text-[11px] font-bold text-blue-600 hover:underline">
                                    <i class="fa-solid fa-image mr-1"></i> Lihat Bukti yang Telah Diunggah &rarr;
                                </a>
                            @endif
                        </div>
                    @else
                        <!-- UNPAID: Tampilkan form upload dan info rekening/QRIS -->
                        <div class="space-y-3">
                            <div class="p-3 bg-white/90 rounded-xl border border-amber-200 text-xs flex justify-between items-center">
                                <div>
                                    <span class="text-slate-500 block text-[11px]">Sisa Tagihan yang Harus Dilunasi:</span>
                                    <span class="text-base font-black text-amber-900">
                                        Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-slate-500 text-right">
                                    Bisa bayar online sekarang<br>atau tunai di lokasi
                                </span>
                            </div>

                            @if($booking->remaining_rejection_reason)
                                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800">
                                    <strong class="block font-bold mb-0.5">Catatan Penolakan Pelunasan Sebelumnya:</strong>
                                    {{ $booking->remaining_rejection_reason }}
                                </div>
                            @endif

                            <!-- Info Rekening / QRIS Toko -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-2">
                                <span class="font-bold text-slate-700 block text-[11px]">Rekening Tujuan Pelunasan:</span>
                                @if($booking->store->bank_name && $booking->store->bank_account_number)
                                    <div class="flex items-center justify-between text-slate-800">
                                        <span>{{ $booking->store->bank_name }}: <strong class="font-mono font-bold">{{ $booking->store->bank_account_number }}</strong> (a.n. {{ $booking->store->bank_account_name }})</span>
                                    </div>
                                @endif
                                @if($booking->store->qris_image_path)
                                    <div class="pt-1">
                                        <a href="{{ asset('storage/' . $booking->store->qris_image_path) }}" target="_blank" class="inline-flex items-center text-[11px] text-blue-600 hover:underline font-bold">
                                            <i class="fa-solid fa-qrcode mr-1"></i> Buka Gambar QRIS Toko
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Form Upload Bukti Pelunasan -->
                            <form method="POST" action="{{ route('user.bookings.upload-remaining-proof', $booking) }}" enctype="multipart/form-data" class="space-y-2.5 pt-1">
                                @csrf
                                <label class="block text-xs font-bold text-slate-800">
                                    Upload Bukti Transfer Pelunasan:
                                </label>
                                <input type="file" name="remaining_proof" accept="image/*" required
                                       class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:cursor-pointer border border-slate-300 rounded-xl bg-white p-1 focus:outline-hidden">
                                @error('remaining_proof')
                                    <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                                @enderror

                                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center space-x-1.5">
                                    <i class="fa-solid fa-upload text-xs"></i>
                                    <span>Kirim Bukti Pelunasan</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            @if($booking->notes)
                <div class="text-xs p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-600 italic">
                    <strong class="not-italic text-slate-700 block font-bold mb-0.5">Catatan Khusus:</strong>
                    "{{ $booking->notes }}"
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 text-center text-[11px] text-slate-400">
            Terima kasih telah menggunakan i-Find. Harap tiba 10 menit sebelum sesi dimulai.
        </div>
    </div>
</div>
@endsection

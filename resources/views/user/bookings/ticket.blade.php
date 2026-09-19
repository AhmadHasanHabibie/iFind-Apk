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
                            <span>Sisa yang Harus Dibayar di Lokasi:</span>
                            <span class="text-sm font-black text-amber-700">
                                Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

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

@extends('layouts.staff')

@section('header_title', 'Dashboard Operasional')

@section('content')
<div class="space-y-6">
    @if(! $hasStore)
        <!-- Empty State CTA: Belum memiliki toko -->
        <div class="bg-white rounded-3xl border border-dashed border-blue-300 p-8 sm:p-12 text-center shadow-sm">
            <div class="w-20 h-20 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-5 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900">Selamat Datang di Portal Mitra iFind!</h2>
            <p class="text-slate-500 text-sm max-w-lg mx-auto mt-2">
                Akun staf Anda telah terverifikasi. Untuk mulai mengelola jadwal slot reservasi meja dan menerima booking pelanggan, silakan lengkapi profil toko Anda terlebih dahulu.
            </p>
            <div class="mt-8">
                <a href="{{ route('staff.store.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-blue-600/30 space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Lengkapi Profil Toko Sekarang</span>
                </a>
            </div>
        </div>
    @else
        <!-- Store Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    Operasional Aktif
                </span>
                <h2 class="text-2xl font-extrabold mt-2">{{ $store->name }}</h2>
                <p class="text-slate-300 text-xs mt-1">
                    {{ $store->address }}, {{ $store->city }} &bull; Kategori: <span class="text-blue-400 font-semibold">{{ $store->category->name ?? 'Tempat' }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('staff.store.edit') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition border border-slate-700">
                    Edit Profil Toko
                </a>
                <a href="{{ route('staff.slots.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Atur Slot Waktu
                </a>
            </div>
        </div>

        <!-- 4 Key Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Booking Masuk Hari Ini -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Booking Masuk Hari Ini</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($todayPendingBookings) }}</h3>
                    <p class="text-xs text-amber-600 font-semibold mt-0.5">Menunggu konfirmasi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- 2. Booking Dikonfirmasi Hari Ini -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Dikonfirmasi Hari Ini</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($todayConfirmedBookings) }}</h3>
                    <p class="text-xs text-emerald-600 font-semibold mt-0.5">Tamu dijadwalkan hadir</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- 3. Sisa Kapasitas Hari Ini -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Sisa Kapasitas Hari Ini</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($todayRemainingCapacity) }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Total kursi tersedia</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
            </div>

            <!-- 4. Rating Rata-rata Toko -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Rating Toko</p>
                    <div class="flex items-center space-x-1.5 mt-1">
                        <h3 class="text-2xl font-extrabold text-slate-900">{{ number_format($averageRating, 1) }}</h3>
                        <span class="text-amber-400 text-lg">★</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Skala 1.0 - 5.0</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </div>
            </div>
        </div>

        <!-- 2 Column Section: Status Meja Hari Ini & Booking Masuk Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tabel Status Meja/Slot Hari Ini -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Status Meja / Slot Hari Ini</h4>
                        <p class="text-xs text-slate-500">Jadwal ketersediaan kursi tanggal {{ \Carbon\Carbon::today()->format('d M Y') }}</p>
                    </div>
                    <a href="{{ route('staff.slots.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Semua Slot &rarr;</a>
                </div>

                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-5">Waktu</th>
                                <th class="py-3 px-5 text-center">Kapasitas</th>
                                <th class="py-3 px-5 text-center">Terisi</th>
                                <th class="py-3 px-5 text-center">Sisa</th>
                                <th class="py-3 px-5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($todaySlots as $slot)
                                <tr>
                                    <td class="py-3 px-5 font-mono text-xs font-bold text-slate-800">
                                        {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }}
                                    </td>
                                    <td class="py-3 px-5 text-center text-xs text-slate-700 font-semibold">
                                        {{ $slot->capacity }} kursi
                                    </td>
                                    <td class="py-3 px-5 text-center text-xs font-semibold text-blue-700">
                                        {{ $slot->booked_seats }} kursi
                                    </td>
                                    <td class="py-3 px-5 text-center text-xs font-bold {{ ($slot->capacity - $slot->booked_seats) > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ max(0, $slot->capacity - $slot->booked_seats) }} kursi
                                    </td>
                                    <td class="py-3 px-5 text-center">
                                        @if($slot->status === 'available')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Tersedia
                                            </span>
                                        @elseif($slot->status === 'full')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Penuh
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                Tutup
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                        Belum ada slot waktu yang digenerate untuk hari ini.
                                        <div class="mt-2">
                                            <a href="{{ route('staff.slots.create') }}" class="text-blue-600 font-bold hover:underline">Tambah Slot Baru &rarr;</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- List Ringkas 5 Booking Pending Terbaru -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Booking Menunggu Konfirmasi</h4>
                        <p class="text-xs text-slate-500">Permintaan reservasi terbaru yang perlu ditindaklanjuti</p>
                    </div>
                    <a href="{{ route('staff.bookings.index', ['status' => 'pending']) }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
                </div>

                <div class="flex-1 divide-y divide-slate-100">
                    @forelse($recentPendingBookings as $booking)
                        <div class="p-4 hover:bg-slate-50/50 transition flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">
                                        #{{ $booking->booking_code }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-800 truncate">{{ $booking->user->name ?? 'User' }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }} &bull;
                                    {{ substr($booking->slot->start_time ?? '', 0, 5) }} - {{ substr($booking->slot->end_time ?? '', 0, 5) }} &bull;
                                    <strong class="text-slate-700">{{ $booking->seat_count }} Kursi</strong>
                                </p>
                            </div>

                            <a href="{{ route('staff.bookings.index', ['status' => 'pending']) }}" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-lg transition border border-blue-200 shrink-0">
                                Proses
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Tidak ada booking yang menunggu konfirmasi saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

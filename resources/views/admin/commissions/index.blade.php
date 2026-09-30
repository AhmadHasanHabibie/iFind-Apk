@extends('layouts.admin')

@section('header_title', 'Biaya Layanan & Komisi Website')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-xs">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Biaya Layanan & Komisi Platform</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Pemantauan real-time komisi platform website sebesar 5% dari setiap reservasi meja.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.commissions.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                <i class="fa-solid fa-file-csv"></i>
                <span>Unduh Rekap CSV</span>
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Komisi Website Berhasil -->
        <div class="bg-gradient-to-br from-teal-500/10 via-teal-500/5 to-transparent p-5 rounded-3xl border border-teal-200/80 shadow-xs flex items-center justify-between">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    <p class="text-xs font-bold uppercase tracking-wider text-teal-800">Total Komisi Website</p>
                </div>
                <h3 class="text-2xl font-black text-teal-950 mt-1.5">Rp {{ number_format($totalCommission, 0, ',', '.') }}</h3>
                <p class="text-xs text-teal-700 mt-0.5">5% dari transaksi berhasil & selesai</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white flex items-center justify-center shadow-sm shadow-teal-600/20">
                <i class="fa-solid fa-wallet text-xl"></i>
            </div>
        </div>

        <!-- Card 2: Komisi Bulan Ini -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Komisi Bulan Ini</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1.5">Rp {{ number_format($thisMonthCommission, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Periode {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i class="fa-solid fa-calendar-check text-xl"></i>
            </div>
        </div>

        <!-- Card 3: Total Transaksi Berhasil -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Transaksi Berhasil</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1.5">{{ number_format($totalTransactionsCount) }} Booking</h3>
                <p class="text-xs text-slate-500 mt-0.5">Gross: Rp {{ number_format($totalGross, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="fa-solid fa-receipt text-xl"></i>
            </div>
        </div>

        <!-- Card 4: Komisi Pending -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Komisi Menunggu / Pending</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1.5">Rp {{ number_format($pendingCommission, 0, ',', '.') }}</h3>
                <p class="text-xs text-amber-700 mt-0.5">Menunggu pembayaran / verifikasi</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Form -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
        <form method="GET" action="{{ route('admin.commissions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Booking / Pelanggan / Toko</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cth: IFD-2026, Ahmad, Kopi..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>

            <!-- Filter Toko -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Filter Toko</label>
                <select name="store_id" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">Semua Toko</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Reservasi</label>
                <select name="status" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">Semua Status</option>
                    <option value="successful" {{ request('status') === 'successful' ? 'selected' : '' }}>Berhasil (Confirmed / Completed)</option>
                    <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Menunggu Verifikasi Toko</option>
                    <option value="awaiting_payment" {{ request('status') === 'awaiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled_expired" {{ request('status') === 'cancelled_expired' ? 'selected' : '' }}>Dibatalkan (Expired)</option>
                </select>
            </div>

            <!-- Rentang Tanggal & Tombol Submit -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan</span>
                </button>
                @if(request()->anyFilled(['search', 'store_id', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('admin.commissions.index') }}"
                       class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
                       title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Rentang Tanggal Bar (Opsional) -->
        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-100 text-xs">
            <span class="text-slate-400 font-medium">Filter Tanggal Booking:</span>
            <input type="date" form="search-form" name="start_date" value="{{ request('start_date') }}"
                   onchange="this.form.submit()"
                   class="py-1 px-2.5 text-xs rounded-lg border border-slate-200 text-slate-700">
            <span class="text-slate-400">s/d</span>
            <input type="date" form="search-form" name="end_date" value="{{ request('end_date') }}"
                   onchange="this.form.submit()"
                   class="py-1 px-2.5 text-xs rounded-lg border border-slate-200 text-slate-700">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        @if($bookings->isEmpty())
            <div class="p-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-receipt text-2xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">Belum Ada Data Transaksi Komisi</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Data biaya layanan akan otomatis tercatat setiap kali ada reservasi masuk dan terkonfirmasi di platform.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                            <th class="py-3.5 px-4 rounded-tl-3xl">Kode & Tanggal</th>
                            <th class="py-3.5 px-4">Pelanggan</th>
                            <th class="py-3.5 px-4">Toko / Tempat</th>
                            <th class="py-3.5 px-4 text-center">Kursi</th>
                            <th class="py-3.5 px-4 text-right">Subtotal Toko (Rp)</th>
                            <th class="py-3.5 px-4 text-right bg-teal-50/50 text-teal-800">Komisi 5% (Rp)</th>
                            <th class="py-3.5 px-4 text-right">Total Bayar (Rp)</th>
                            <th class="py-3.5 px-4 text-center rounded-tr-3xl">Status Booking</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-black text-slate-900 block">{{ $booking->booking_code }}</span>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d M Y') }}
                                        @if($booking->slot)
                                            &bull; {{ substr($booking->slot->start_time, 0, 5) }} - {{ substr($booking->slot->end_time, 0, 5) }}
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $booking->user->name ?? 'Pengguna' }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $booking->user->email ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $booking->store->name ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $booking->store->city ?? '' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-800 font-bold text-xs">
                                        {{ $booking->seat_count }} Pax
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                    Rp {{ number_format($booking->subtotal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-teal-700 bg-teal-50/30">
                                    Rp {{ number_format($booking->service_fee, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-950">
                                    Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $badgeClasses = match($booking->status) {
                                            'confirmed', 'completed', 'checked_in' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'pending_verification' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'awaiting_payment' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-rose-50 text-rose-700 border-rose-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $badgeClasses }}">
                                        {{ $booking->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

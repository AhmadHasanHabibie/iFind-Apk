<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Reservasi - {{ $store->name }} ({{ $startDate }} s/d {{ $endDate }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; font-size: 11pt; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 text-slate-800 antialiased">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <!-- Action Bar (No Print) -->
        <div class="no-print flex items-center justify-between pb-6 mb-6 border-b border-slate-200">
            <a href="{{ route('staff.bookings.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                &larr; Kembali ke Booking
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('staff.bookings.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition">
                    Unduh CSV
                </a>
                <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <!-- Report Header -->
        <div class="text-center pb-6 border-b border-slate-200 space-y-1">
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Laporan Rekapitulasi Reservasi</h1>
            <p class="text-base font-bold text-blue-600">{{ $store->name }}</p>
            <p class="text-xs text-slate-500">{{ $store->address }}, {{ $store->city }}</p>
            <p class="text-xs font-semibold text-slate-700 pt-2">Periode Data: <span class="bg-slate-100 px-2 py-0.5 rounded">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span></p>
        </div>

        <!-- Summary Metrics -->
        <div class="grid grid-cols-3 gap-4 my-6">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Reservasi</span>
                <span class="text-xl font-black text-slate-900 mt-1 block">{{ $bookings->count() }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Kursi Terisi</span>
                <span class="text-xl font-black text-slate-900 mt-1 block">{{ $totalPax }} Pax</span>
            </div>
            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 text-center">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Total Pendapatan Terkonfirmasi</span>
                <span class="text-xl font-black text-emerald-800 mt-1 block">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold uppercase">
                        <th class="p-2.5 rounded-l-lg">No</th>
                        <th class="p-2.5">Kode</th>
                        <th class="p-2.5">Pelanggan</th>
                        <th class="p-2.5">Tgl Reservasi</th>
                        <th class="p-2.5">Slot Waktu</th>
                        <th class="p-2.5 text-center">Kursi</th>
                        <th class="p-2.5 text-right">Total (Rp)</th>
                        <th class="p-2.5 text-right">DP (Rp)</th>
                        <th class="p-2.5 text-center rounded-r-lg">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $idx => $b)
                        <tr class="hover:bg-slate-50">
                            <td class="p-2.5 text-slate-500">{{ $idx + 1 }}</td>
                            <td class="p-2.5 font-mono font-bold text-slate-900">{{ $b->booking_code }}</td>
                            <td class="p-2.5">
                                <span class="font-bold text-slate-800 block">{{ $b->user->name ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $b->user->phone ?? '-' }}</span>
                            </td>
                            <td class="p-2.5 text-slate-700">{{ \Carbon\Carbon::parse($b->booking_date)->translatedFormat('d M Y') }}</td>
                            <td class="p-2.5 text-slate-600">{{ $b->slot ? substr($b->slot->start_time, 0, 5) . '-' . substr($b->slot->end_time, 0, 5) : '-' }}</td>
                            <td class="p-2.5 text-center font-bold text-slate-800">{{ $b->seat_count }}</td>
                            <td class="p-2.5 text-right font-bold text-slate-900">{{ number_format($b->total_amount, 0, ',', '.') }}</td>
                            <td class="p-2.5 text-right font-medium text-slate-600">{{ number_format($b->amount_due, 0, ',', '.') }}</td>
                            <td class="p-2.5 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ in_array($b->status, ['confirmed', 'checked_in', 'completed']) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ str_replace('_', ' ', $b->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400 font-medium">Tidak ada data reservasi pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-200 flex justify-between text-xs text-slate-500">
            <span>Dicetak secara otomatis oleh sistem i-Find pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
            <span>Tanda Tangan Pengelola Toko</span>
        </div>
    </div>
</body>
</html>

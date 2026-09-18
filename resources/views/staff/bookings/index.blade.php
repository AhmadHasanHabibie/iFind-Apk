@extends('layouts.staff')

@section('header_title', 'Manajemen Booking Order')

@section('content')
<div class="space-y-6" x-data="{ rejectModalOpen: false, rejectBookingId: '', rejectBookingCode: '', rejectActionUrl: '' }">
    <!-- Header Summary & Tab Navigation -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Daftar Reservasi & Booking</h2>
            <p class="text-xs text-slate-500 mt-1">Konfirmasi pesanan masuk, pantau tamu yang akan hadir, dan kelola riwayat</p>
        </div>

        <!-- 3 Filter Tabs -->
        <div class="flex items-center space-x-1.5 p-1 bg-slate-100 rounded-xl">
            <a href="{{ route('staff.bookings.index', ['status' => 'pending']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 {{ $statusTab === 'pending' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Masuk</span>
                @if($counts['pending'] > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold">{{ $counts['pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('staff.bookings.index', ['status' => 'confirmed']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 {{ $statusTab === 'confirmed' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Dikonfirmasi</span>
                @if($counts['confirmed'] > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">{{ $counts['confirmed'] }}</span>
                @endif
            </a>
            <a href="{{ route('staff.bookings.index', ['status' => 'history']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $statusTab === 'history' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Riwayat ({{ $counts['history'] }})</span>
            </a>
        </div>
    </div>

    <!-- Date Filter Bar for History Tab -->
    @if($statusTab === 'history')
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('staff.bookings.index') }}" class="flex items-center gap-3">
                <input type="hidden" name="status" value="history">
                <span class="text-xs font-bold text-slate-500">Filter Tanggal:</span>
                <input type="date"
                       name="date"
                       value="{{ request('date') }}"
                       class="text-xs rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2.5">
                <button type="submit" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition">
                    Filter
                </button>
                @if(request('date'))
                    <a href="{{ route('staff.bookings.index', ['status' => 'history']) }}" class="text-xs font-bold text-rose-600 hover:text-rose-700">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    @endif

    <!-- Bookings Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Kode Booking</th>
                        <th class="py-3.5 px-6">Nama Pelanggan</th>
                        <th class="py-3.5 px-6">Jadwal & Sesi</th>
                        <th class="py-3.5 px-6 text-center">Kursi</th>
                        <th class="py-3.5 px-6">Catatan</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Kode -->
                            <td class="py-4 px-6">
                                <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">
                                    #{{ $booking->booking_code }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                            </td>

                            <!-- Pelanggan -->
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-800">{{ $booking->user->name ?? 'User' }}</p>
                                <p class="text-xs text-slate-400">{{ $booking->user->phone ?: $booking->user->email }}</p>
                            </td>

                            <!-- Jadwal -->
                            <td class="py-4 px-6">
                                <div class="text-xs font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->isoFormat('dddd, D MMM Y') }}
                                </div>
                                <div class="text-xs font-mono text-blue-600 mt-0.5">
                                    {{ substr($booking->slot->start_time ?? '', 0, 5) }} &ndash; {{ substr($booking->slot->end_time ?? '', 0, 5) }}
                                </div>
                            </td>

                            <!-- Kursi -->
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold text-xs bg-slate-100 text-slate-800">
                                    {{ $booking->seat_count }} Kursi
                                </span>
                            </td>

                            <!-- Catatan -->
                            <td class="py-4 px-6 text-xs text-slate-600 max-w-xs">
                                @if($booking->notes)
                                    <p class="line-clamp-2">{{ $booking->notes }}</p>
                                @else
                                    <span class="text-slate-400 italic">Tidak ada catatan</span>
                                @endif

                                @if($booking->rejection_reason)
                                    <div class="mt-1 p-1.5 bg-rose-50 text-rose-800 rounded text-[11px] border border-rose-100">
                                        <strong>Alasan Tolak:</strong> {{ $booking->rejection_reason }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6 text-center">
                                @if($booking->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Dikonfirmasi
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Selesai
                                    </span>
                                @elseif($booking->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-6 text-right">
                                @if($booking->status === 'pending')
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Tombol Konfirmasi -->
                                        <form method="POST" action="{{ route('staff.bookings.confirm', $booking) }}" onsubmit="return confirm('Konfirmasi booking #{{ $booking->booking_code }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                                                Konfirmasi
                                            </button>
                                        </form>

                                        <!-- Tombol Tolak (Buka Modal) -->
                                        <button type="button"
                                                @click="rejectBookingId = '{{ $booking->id }}'; rejectBookingCode = '{{ $booking->booking_code }}'; rejectActionUrl = '{{ route('staff.bookings.reject', $booking) }}'; rejectModalOpen = true;"
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-lg transition border border-rose-200">
                                            Tolak
                                        </button>
                                    </div>
                                @elseif($booking->status === 'confirmed')
                                    @php
                                        $canComplete = $booking->booking_date <= $today;
                                    @endphp
                                    @if($canComplete)
                                        <form method="POST" action="{{ route('staff.bookings.complete', $booking) }}" onsubmit="return confirm('Tandai booking #{{ $booking->booking_code }} telah selesai?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                                                Tandai Selesai
                                            </button>
                                        </form>
                                    @else
                                        <span title="Hanya dapat ditandai selesai pada atau setelah hari H" class="px-3 py-1.5 bg-slate-100 text-slate-400 font-semibold text-xs rounded-lg cursor-not-allowed select-none">
                                            Belum Hari H
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 italic">Tidak ada aksi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Tidak ada data booking pada tab ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

    <!-- Reject Booking Modal -->
    <div x-show="rejectModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100" @click.away="rejectModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-rose-600 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Tolak Booking #<span x-text="rejectBookingCode"></span></span>
                </h3>
                <button @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="rejectActionUrl" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label for="rejection_reason" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="rejection_reason"
                              name="rejection_reason"
                              rows="3"
                              required
                              placeholder="Contoh: Tempat telah dibooking untuk acara internal mendadak..."
                              class="w-full text-sm rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Alasan ini akan dapat dilihat oleh pelanggan.</p>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm">
                        Tolak Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

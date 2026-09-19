@extends('layouts.staff')

@section('header_title', 'Manajemen Booking Order')

@section('content')
<div class="space-y-6" x-data="{
    rejectModalOpen: false,
    rejectBookingId: '',
    rejectBookingCode: '',
    rejectActionUrl: '',
    rejectType: 'invalid_payment',
    defaultStoreFullReason: 'Tempat penuh pada sesi waktu ini. Dana Anda akan diproses untuk pengembalian (refund).',
    reasonInput: '',

    previewModalOpen: false,
    previewImageSrc: '',

    refundModalOpen: false,
    refundBookingCode: '',
    refundActionUrl: '',
    refundNote: ''
}">

    <!-- Banner Peringatan Refund Manual -->
    @if(isset($unrefundedBookings) && $unrefundedBookings->count() > 0)
        <div class="p-4 bg-orange-50 border-l-4 border-orange-500 rounded-2xl shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-orange-950">
                        Ada {{ $unrefundedBookings->count() }} booking menunggu proses refund manual
                    </h4>
                    <p class="text-xs text-orange-800 mt-0.5">
                        Booking ditolak karena tempat penuh dan dana transfer wajib dikembalikan manual kepada customer.
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                @php
                    $firstUnrefunded = $unrefundedBookings->first();
                @endphp
                <button type="button"
                        @click="refundBookingCode = '{{ $firstUnrefunded->booking_code }}'; refundActionUrl = '{{ route('staff.bookings.refund', $firstUnrefunded) }}'; refundModalOpen = true;"
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl transition shadow-sm shrink-0 flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Tandai Sudah Direfund</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Header Summary & Tab Navigation -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Daftar Reservasi & Booking</h2>
            <p class="text-xs text-slate-500 mt-1">Verifikasi bukti transfer, pantau kehadiran tamu, dan kelola riwayat</p>
        </div>

        <!-- 3 Filter Tabs -->
        <div class="flex items-center space-x-1.5 p-1 bg-slate-100 rounded-xl">
            <!-- Tab 1: Menunggu Verifikasi -->
            <a href="{{ route('staff.bookings.index', ['status' => 'pending']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 {{ $statusTab === 'pending' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Menunggu Verifikasi</span>
                @if($counts['pending'] > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-100 text-blue-800 font-extrabold">{{ $counts['pending'] }}</span>
                @endif
            </a>

            <!-- Tab 2: Aktif -->
            <a href="{{ route('staff.bookings.index', ['status' => 'active']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 {{ $statusTab === 'active' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Aktif</span>
                @if($counts['active'] > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">{{ $counts['active'] }}</span>
                @endif
            </a>

            <!-- Tab 3: Riwayat -->
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
                        <th class="py-3.5 px-6">Pembayaran & Bukti</th>
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
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $booking->user->name ?? 'User' }}</p>
                                        <p class="text-xs text-slate-400">{{ $booking->user->phone ?: $booking->user->email }}</p>
                                    </div>
                                    @if($booking->user)
                                        <form method="POST" action="{{ route('staff.chat.start-user', $booking->user) }}">
                                            @csrf
                                            <button type="submit"
                                                    title="Chat Customer"
                                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition shadow-2xs inline-flex items-center space-x-1 shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                <span>Chat</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
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

                            <!-- Pembayaran & Bukti Transfer -->
                            <td class="py-4 px-6 text-xs">
                                <div class="space-y-1">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Wajib Transfer:</span>
                                        <strong class="text-blue-700 text-sm font-extrabold">Rp {{ number_format($booking->amount_due, 0, ',', '.') }}</strong>
                                        <span class="text-[10px] text-slate-400 block">Total: Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                                    </div>

                                    @if($booking->payment_proof_path)
                                        <div class="pt-1">
                                            <button type="button"
                                                    @click="previewImageSrc = '{{ asset('storage/' . $booking->payment_proof_path) }}'; previewModalOpen = true;"
                                                    class="inline-flex items-center space-x-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-blue-700 font-bold text-[11px] border border-slate-200 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span>Lihat Bukti</span>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum upload</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6 text-center">
                                @if($booking->status === 'pending_verification')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 animate-pulse"></span>
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Terkonfirmasi (E-Ticket)
                                    </span>
                                @elseif($booking->status === 'checked_in')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        <svg class="w-3.5 h-3.5 mr-1 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Sudah Check-in
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        Selesai
                                    </span>
                                @elseif($booking->status === 'rejected_invalid_payment')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Bukti Tidak Valid
                                    </span>
                                @elseif($booking->status === 'rejected_store_full')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                        Tempat Penuh (Perlu Refund)
                                    </span>
                                @elseif($booking->status === 'refunded')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        Sudah Direfund
                                    </span>
                                @elseif($booking->status === 'cancelled_expired')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        Kadaluarsa (Batal)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        {{ $booking->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-6 text-right">
                                @if($booking->status === 'pending_verification')
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Tombol Terima (Konfirmasi) -->
                                        <form method="POST" action="{{ route('staff.bookings.confirm', $booking) }}" onsubmit="return confirm('Terima pembayaran dan konfirmasi booking #{{ $booking->booking_code }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                                                Terima
                                            </button>
                                        </form>

                                        <!-- Tombol Tolak (Buka Modal) -->
                                        <button type="button"
                                                @click="rejectBookingId = '{{ $booking->id }}'; rejectBookingCode = '{{ $booking->booking_code }}'; rejectActionUrl = '{{ route('staff.bookings.reject', $booking) }}'; rejectType = 'invalid_payment'; reasonInput = ''; rejectModalOpen = true;"
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-lg transition border border-rose-200">
                                            Tolak
                                        </button>
                                    </div>
                                @elseif($booking->status === 'checked_in')
                                    <!-- Tandai Selesai (HANYA AKTIF DARI CHECKED_IN) -->
                                    <form method="POST" action="{{ route('staff.bookings.complete', $booking) }}" onsubmit="return confirm('Tandai kunjungan booking #{{ $booking->booking_code }} telah selesai?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                                            Tandai Selesai
                                        </button>
                                    </form>
                                @elseif($booking->status === 'confirmed')
                                    <a href="{{ route('staff.scan.index') }}"
                                       title="Scan E-Ticket QR saat customer tiba"
                                       class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition inline-flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        <span>Menunggu Check-in</span>
                                    </a>
                                @elseif($booking->status === 'rejected_store_full')
                                    <!-- Aksi Tandai Sudah Direfund -->
                                    <button type="button"
                                            @click="refundBookingCode = '{{ $booking->booking_code }}'; refundActionUrl = '{{ route('staff.bookings.refund', $booking) }}'; refundNote = ''; refundModalOpen = true;"
                                            class="px-2.5 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-800 font-bold text-xs rounded-lg border border-orange-200 transition">
                                        Tandai Refund
                                    </button>
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

    <!-- 1. Reject Booking Modal -->
    <div x-show="rejectModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Pilih Kategori Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 gap-2">
                        <label class="p-3 border rounded-xl flex items-start space-x-2.5 cursor-pointer hover:border-rose-300"
                               :class="rejectType === 'invalid_payment' ? 'border-rose-500 bg-rose-50/40' : 'border-slate-200'">
                            <input type="radio" name="reject_type" value="invalid_payment" x-model="rejectType" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div>
                                <strong class="text-xs font-bold text-slate-800 block">Bukti Tidak Valid / Palsu</strong>
                                <span class="text-[11px] text-slate-500">Dana belum masuk atau bukti palsu. Tidak ada pengembalian dana.</span>
                            </div>
                        </label>

                        <label class="p-3 border rounded-xl flex items-start space-x-2.5 cursor-pointer hover:border-orange-300"
                               :class="rejectType === 'store_full' ? 'border-orange-500 bg-orange-50/40' : 'border-slate-200'">
                            <input type="radio" name="reject_type" value="store_full" x-model="rejectType" class="mt-0.5 text-orange-600 focus:ring-orange-500">
                            <div>
                                <strong class="text-xs font-bold text-slate-800 block">Tempat Penuh (Perlu Refund)</strong>
                                <span class="text-[11px] text-slate-500">Dana masuk namun kuota penuh. Anda wajib mengembalikan dana customer.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="payment_rejection_reason" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="payment_rejection_reason"
                              name="payment_rejection_reason"
                              rows="3"
                              x-model="reasonInput"
                              :placeholder="rejectType === 'store_full' ? defaultStoreFullReason : 'Contoh: Nomor rekening tidak cocok / mutasi rekening belum masuk...'"
                              class="w-full text-xs rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Kapasitas kursi slot akan otomatis dilepaskan kembali.</p>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm">
                        Proses Tolak Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Modal Preview Gambar Bukti Transfer -->
    <div x-show="previewModalOpen"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         @keydown.escape.window="previewModalOpen = false">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4"
             @click.outside="previewModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Preview Bukti Transfer</h3>
                <button type="button" @click="previewModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="w-full max-h-[70vh] overflow-auto bg-slate-100 rounded-2xl flex items-center justify-center p-2">
                <img :src="previewImageSrc" alt="Bukti Transfer" class="max-w-full max-h-[65vh] object-contain rounded-xl">
            </div>

            <div class="flex items-center justify-end">
                <button type="button" @click="previewModalOpen = false" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Modal Tandai Refund Selesai -->
    <div x-show="refundModalOpen"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         @keydown.escape.window="refundModalOpen = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4"
             @click.outside="refundModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-orange-600 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Konfirmasi Refund #<span x-text="refundBookingCode"></span></span>
                </h3>
                <button @click="refundModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="refundActionUrl" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="refund_note" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Catatan Pengembalian Dana <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="refund_note"
                              name="refund_note"
                              rows="3"
                              required
                              x-model="refundNote"
                              placeholder="Contoh: Telah ditransfer kembali Rp 50.000 ke rekening BCA 1234567890 an Budi pada 19/09 15:30. Bukti terlampir via chat."
                              class="w-full text-xs rounded-xl border-slate-300 focus:border-orange-500 focus:ring-orange-500"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Catatan ini akan disimpan dan dapat dilihat pelanggan.</p>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="refundModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl transition shadow-sm">
                        Simpan Bukti Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

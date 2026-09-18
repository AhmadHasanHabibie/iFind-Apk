@extends('layouts.user')

@section('content')
<div class="space-y-8" x-data="{ reviewModalOpen: false, activeBookingId: null, activeBookingCode: '', selectedRating: 5 }">

    <!-- Page Header & Tab Pills -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 pb-5">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pesanan & Reservasi Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola reservasi meja aktif dan riwayat kunjungan tempat Anda.</p>
        </div>

        <!-- Modern Tab Pills -->
        <div class="flex items-center bg-slate-100/90 p-1.5 rounded-2xl border border-slate-200/60 shadow-xs">
            <a href="{{ route('user.bookings.index', ['tab' => 'active']) }}"
               class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 {{ $tab === 'active' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                <i class="fa-regular fa-clock text-xs"></i>
                <span>Pesanan Aktif</span>
            </a>
            <a href="{{ route('user.bookings.index', ['tab' => 'history']) }}"
               class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 {{ $tab === 'history' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                <span>Riwayat Selesai</span>
            </a>
        </div>
    </div>

    <!-- Bookings Cards Stream -->
    @if($bookings->count() > 0)
        <div class="space-y-4">
            @foreach($bookings as $booking)
                @php
                    $primaryPhoto = $booking->store->photos->firstWhere('is_primary', true) ?? $booking->store->photos->first();
                    $photoUrl = $primaryPhoto ? asset('storage/' . $primaryPhoto->photo_path) : null;
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 transition-all duration-200 hover:shadow-md hover:border-slate-300 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <!-- Store Thumbnail & Booking Details -->
                    <div class="flex items-start space-x-5 min-w-0">
                        <!-- Store Image Thumbnail -->
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200 flex items-center justify-center shadow-xs">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}"
                                     alt="{{ $booking->store->name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none;" class="w-full h-full bg-slate-100 items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-store text-2xl text-slate-300"></i>
                                </div>
                            @else
                                <i class="fa-solid fa-store text-2xl text-slate-300"></i>
                            @endif
                        </div>

                        <!-- Details Body -->
                        <div class="space-y-1.5 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs font-black text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80">
                                    #{{ $booking->booking_code }}
                                </span>

                                <!-- Status Badge -->
                                @if($booking->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2 animate-ping"></span>
                                        Menunggu Konfirmasi Toko
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                                        <i class="fa-solid fa-circle-check text-[11px] mr-1.5 text-emerald-600"></i>
                                        Dikonfirmasi Toko
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200 shadow-2xs">
                                        <i class="fa-solid fa-check text-[11px] mr-1.5 text-blue-600"></i>
                                        Selesai
                                    </span>
                                @elseif($booking->status === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200 shadow-2xs">
                                        <i class="fa-solid fa-ban text-[11px] mr-1.5 text-rose-600"></i>
                                        Ditolak
                                    </span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        Dibatalkan
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('user.stores.show', $booking->store->slug) }}" class="text-base sm:text-lg font-black text-slate-900 hover:text-blue-600 transition-colors block truncate">
                                {{ $booking->store->name }}
                            </a>

                            <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500 font-medium">
                                <span class="flex items-center space-x-1.5">
                                    <i class="fa-regular fa-calendar text-slate-400"></i>
                                    <span>{{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d M Y') }}</span>
                                </span>
                                <span class="flex items-center space-x-1.5 font-mono">
                                    <i class="fa-regular fa-clock text-slate-400"></i>
                                    <span>{{ substr($booking->slot->start_time ?? '00:00', 0, 5) }} - {{ substr($booking->slot->end_time ?? '00:00', 0, 5) }}</span>
                                </span>
                                <span class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-chair text-slate-400"></i>
                                    <strong class="text-slate-800 font-bold">{{ $booking->seat_count }} Kursi</strong>
                                </span>
                            </div>

                            @if($booking->notes)
                                <p class="text-xs text-slate-600 italic bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    "{{ $booking->notes }}"
                                </p>
                            @endif

                            @if($booking->status === 'rejected' && $booking->rejection_reason)
                                <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-900 leading-relaxed">
                                    <strong class="block mb-0.5 font-bold">Alasan Penolakan Toko:</strong>
                                    {{ $booking->rejection_reason }}
                                </div>
                            @endif

                            <!-- Display Review if already submitted -->
                            @if($booking->review)
                                <div class="p-3 bg-amber-50/80 border border-amber-200 rounded-2xl text-xs text-amber-950 space-y-1">
                                    <div class="flex items-center space-x-1 text-amber-500 text-xs">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="fa-solid fa-star {{ $s <= $booking->review->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                        @endfor
                                        <span class="font-bold ml-1.5 text-amber-900">{{ $booking->review->rating }} Bintang</span>
                                    </div>
                                    @if($booking->review->comment)
                                        <p class="text-slate-700 italic">"{{ $booking->review->comment }}"</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Column -->
                    <div class="flex flex-col sm:flex-row md:flex-col items-start md:items-end justify-between gap-3 shrink-0 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100">
                        @if($booking->status === 'pending')
                            <!-- Cancel Booking Button using Custom Confirmation Dialog (No Browser confirm popup) -->
                            <form x-ref="cancelForm{{ $booking->id }}" action="{{ route('user.bookings.cancel', $booking) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="button"
                                        @click="$dispatch('custom-confirm', {
                                            title: 'Batalkan Reservasi?',
                                            message: 'Apakah Anda yakin ingin membatalkan pesanan #{{ $booking->booking_code }}? Tindakan ini tidak dapat dibatalkan.',
                                            confirmText: 'Ya, Batalkan',
                                            onConfirm: () => $refs.cancelForm{{ $booking->id }}.submit()
                                        })"
                                        class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-all hover:shadow-xs">
                                    <i class="fa-solid fa-xmark mr-1.5"></i> Batalkan Reservasi
                                </button>
                            </form>
                        @elseif($booking->status === 'confirmed')
                            <div class="text-left md:text-right space-y-2">
                                <span class="block text-[11px] text-slate-500 max-w-[220px]">
                                    Reservasi telah dikonfirmasi toko.
                                </span>
                                <form action="{{ route('user.chat.start', $booking->store->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200 transition-all flex items-center space-x-1.5">
                                        <i class="fa-solid fa-comments"></i>
                                        <span>Hubungi Toko</span>
                                    </button>
                                </form>
                            </div>
                        @elseif($booking->status === 'completed' && ! $booking->review)
                            <!-- Review Button -->
                            <button type="button"
                                    @click="activeBookingId = {{ $booking->id }}; activeBookingCode = '{{ $booking->booking_code }}'; reviewModalOpen = true"
                                    class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-lg shadow-amber-500/25 hover:shadow-amber-500/35 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center space-x-1.5">
                                <i class="fa-solid fa-star text-xs"></i>
                                <span>Beri Ulasan</span>
                            </button>
                        @endif

                        <a href="{{ route('user.stores.show', $booking->store->slug) }}" class="text-xs font-bold text-blue-600 hover:underline flex items-center space-x-1">
                            <span>Lihat Spot</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty Bookings State -->
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 space-y-4 max-w-md mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center mx-auto text-2xl shadow-xs">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    {{ $tab === 'active' ? 'Tidak Ada Pesanan Aktif' : 'Belum Ada Riwayat Pesanan' }}
                </h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    {{ $tab === 'active' ? 'Anda belum memiliki reservasi meja yang sedang menunggu konfirmasi atau aktif.' : 'Pesanan yang telah selesai atau dibatalkan akan tersimpan rapi di sini.' }}
                </p>
            </div>
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/25 hover:-translate-y-0.5 transition-all">
                Cari & Reservasi Tempat Sekarang
            </a>
        </div>
    @endif

    <!-- Review Modal Partial -->
    @include('user.partials._review-form')

</div>
@endsection

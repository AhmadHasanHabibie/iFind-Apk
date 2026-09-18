@extends('layouts.user')

@section('content')
<div class="space-y-8" x-data="storeDetail({{ json_encode($slots->map(function($s) {
    $isPast = \Carbon\Carbon::parse($s->date)->isPast() && ! \Carbon\Carbon::parse($s->date)->isToday();
    return [
        'id' => $s->id,
        'start_time' => substr($s->start_time, 0, 5),
        'end_time' => substr($s->end_time, 0, 5),
        'capacity' => $s->capacity,
        'booked_seats' => $s->booked_seats,
        'pending_seats' => $s->pending_seats,
        'estimated_available' => $s->estimated_available,
        'status' => $s->status,
        'is_bookable' => ($s->status !== 'closed' && $s->estimated_available > 0 && ! $isPast),
    ];
})) }}, '{{ $selectedDate }}', '{{ $store->slug }}')">

    <!-- Back Navigation & Quick Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition group">
            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            <span>Kembali ke Pencarian Spot</span>
        </a>

        <!-- Chat with Store Button -->
        <form action="{{ route('user.chat.start', $store->slug) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                <i class="fa-solid fa-comment-dots text-sm"></i>
                <span>Chat dengan Toko</span>
            </button>
        </form>
    </div>

    <!-- Store Header / Photo Gallery Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        @php
            $photos = $store->photos;
        @endphp

        <!-- Carousel Gallery Container -->
        <div class="relative w-full h-72 sm:h-96 md:h-[420px] bg-slate-100 overflow-hidden" x-data="{ activeIndex: 0 }">
            @if($photos->count() > 0)
                @foreach($photos as $idx => $photo)
                    <div x-show="activeIndex === {{ $idx }}"
                         class="w-full h-full transition duration-300">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}"
                             alt="{{ $store->name }}"
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none;" class="w-full h-full bg-slate-200 items-center justify-center text-slate-400">
                            <i class="fa-solid fa-store text-5xl"></i>
                        </div>
                    </div>
                @endforeach

                @if($photos->count() > 1)
                    <!-- Prev/Next Controls -->
                    <button @click="activeIndex = (activeIndex === 0) ? {{ $photos->count() - 1 }} : activeIndex - 1"
                            type="button"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center backdrop-blur-sm transition-all hover:scale-105">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button @click="activeIndex = (activeIndex === {{ $photos->count() - 1 }}) ? 0 : activeIndex + 1"
                            type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center backdrop-blur-sm transition-all hover:scale-105">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>

                    <!-- Indicators -->
                    <div class="absolute bottom-4 inset-x-0 flex justify-center space-x-2">
                        @foreach($photos as $idx => $photo)
                            <button @click="activeIndex = {{ $idx }}"
                                    class="w-3 h-1.5 rounded-full transition-all"
                                    :class="activeIndex === {{ $idx }} ? 'bg-white w-6' : 'bg-white/50'"></button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="w-full h-full bg-gradient-to-tr from-blue-600/10 to-indigo-600/20 flex flex-col items-center justify-center text-slate-400">
                    <i class="fa-solid fa-store text-6xl text-slate-300 mb-2"></i>
                    <span class="text-xs text-slate-400 font-bold">Foto Tempat Segera Hadir</span>
                </div>
            @endif
        </div>

        <!-- Store Main Details Body -->
        <div class="p-6 sm:p-8 md:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-100 pb-6">
                <div>
                    <div class="flex items-center space-x-2.5 mb-2.5">
                        <span class="px-3 py-1 rounded-xl bg-blue-50 text-blue-700 font-extrabold text-xs border border-blue-200 shadow-xs">
                            {{ $store->category->name ?? 'Kategori' }}
                        </span>
                        @if($isOpenNow)
                            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-xs border border-emerald-200 shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-ping"></span>
                                Buka Sekarang
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-slate-100 text-slate-600 font-extrabold text-xs border border-slate-200">
                                Tutup
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">{{ $store->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-2 flex items-center space-x-1.5 font-medium">
                        <i class="fa-solid fa-location-dot text-slate-400"></i>
                        <span>{{ $store->address }}, {{ $store->city }}</span>
                    </p>
                </div>

                <div class="flex items-center space-x-3 shrink-0">
                    <!-- Rating Badge -->
                    <div class="flex items-center space-x-3 bg-amber-50 border border-amber-200 px-5 py-3 rounded-2xl shadow-xs">
                        <i class="fa-solid fa-star text-amber-400 text-2xl"></i>
                        <div>
                            <div class="text-xl font-black text-amber-950 leading-none">{{ number_format($store->average_rating, 1) }}</div>
                            <div class="text-[11px] text-amber-800 font-bold mt-1">{{ $store->reviews->count() }} Ulasan</div>
                        </div>
                    </div>

                    <!-- Google Maps Button -->
                    @if($store->latitude && $store->longitude)
                        <a href="https://www.google.com/maps?q={{ $store->latitude }},{{ $store->longitude }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="px-5 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center space-x-2 transition-all border border-slate-200 shadow-xs hover:shadow hover:-translate-y-0.5">
                            <i class="fa-solid fa-map-location-dot text-blue-600 text-sm"></i>
                            <span>Buka di Maps</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-2">Tentang Tempat Ini</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    {{ $store->description ?? 'Tidak ada deskripsi detail untuk toko ini.' }}
                </p>
            </div>

            <!-- Facilities Grid -->
            @if($store->facilities->count() > 0)
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-3">Fasilitas Tersedia</h3>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($store->facilities as $fac)
                            <span class="inline-flex items-center px-4 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs shadow-2xs hover:border-blue-200 transition">
                                <i class="{{ $fac->icon ?? 'fa-solid fa-check' }} text-blue-600 mr-2.5"></i>
                                <span>{{ $fac->name }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 7-Day Opening Hours Table -->
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-3">Jam Operasional</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5">
                    @php
                        $daysMap = [
                            'monday' => 'Senin',
                            'tuesday' => 'Selasa',
                            'wednesday' => 'Rabu',
                            'thursday' => 'Kamis',
                            'friday' => 'Jumat',
                            'saturday' => 'Sabtu',
                            'sunday' => 'Minggu',
                        ];
                    @endphp

                    @foreach($daysMap as $key => $label)
                        @php
                            $schedule = $store->opening_hours[$key] ?? null;
                            $isToday = ($key === $todayDay);
                        @endphp
                        <div class="p-3.5 rounded-2xl border text-center transition {{ $isToday ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-500/20 shadow-xs' : 'bg-slate-50/70 border-slate-200/80' }}">
                            <div class="text-xs font-bold {{ $isToday ? 'text-blue-800 font-black' : 'text-slate-700' }}">
                                {{ $label }}
                                @if($isToday)
                                    <span class="block text-[9px] text-blue-600 uppercase font-black tracking-wider mt-0.5">(Hari Ini)</span>
                                @endif
                            </div>
                            <div class="mt-1.5 text-xs">
                                @if(! $schedule || ($schedule['is_closed'] ?? false))
                                    <span class="text-rose-600 font-bold text-[11px]">Tutup</span>
                                @else
                                    <span class="font-mono text-slate-800 font-bold text-[11px]">{{ $schedule['open'] ?? '08:00' }} - {{ $schedule['close'] ?? '22:00' }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- REAL-TIME SLOT PICKER & RESERVATION SECTION -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 md:p-10 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-xl font-black text-slate-900 flex items-center space-x-2">
                    <i class="fa-regular fa-calendar-days text-blue-600"></i>
                    <span>Cek Ketersediaan Meja & Reservasi</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Sinkronisasi ketersediaan meja instan langsung dengan staf toko.
                </p>
            </div>

            <!-- Date Selectors (7 Days Ahead) -->
            <div class="flex items-center space-x-2 overflow-x-auto pb-1 sm:pb-0">
                @for($i = 0; $i < 7; $i++)
                    @php
                        $targetDate = \Carbon\Carbon::today()->addDays($i);
                        $formattedValue = $targetDate->toDateString();
                        $dayName = $targetDate->isToday() ? 'Hari Ini' : ($targetDate->isTomorrow() ? 'Besok' : $targetDate->translatedFormat('D'));
                    @endphp
                    <button type="button"
                            @click="selectDate('{{ $formattedValue }}')"
                            class="px-3.5 py-2.5 rounded-2xl text-center font-bold text-xs transition-all border shrink-0 flex flex-col items-center hover:-translate-y-0.5"
                            :class="currentDate === '{{ $formattedValue }}' ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/25' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'">
                        <span class="text-[10px] uppercase font-bold" :class="currentDate === '{{ $formattedValue }}' ? 'text-blue-100' : 'text-slate-400'">
                            {{ $dayName }}
                        </span>
                        <span class="font-black text-xs mt-0.5">{{ $targetDate->format('d M') }}</span>
                    </button>
                @endfor
            </div>
        </div>

        <!-- Slot Stream Area -->
        <div class="space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>Daftar slot waktu: <strong class="text-slate-800 font-bold" x-text="formatDateLabel(currentDate)"></strong></span>
                <span x-show="loadingSlots" class="text-blue-600 font-bold animate-pulse flex items-center space-x-1.5">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <span>Memperbarui ketersediaan...</span>
                </span>
            </div>

            <!-- Slot Grid -->
            <template x-if="slots.length > 0">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="slot in slots" :key="slot.id">
                        <div class="p-5 rounded-3xl border transition-all duration-200 flex flex-col justify-between"
                             :class="{
                                 'bg-white border-slate-200/90 shadow-sm hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5': slot.is_bookable,
                                 'bg-slate-50/80 border-slate-200 opacity-60 cursor-not-allowed': !slot.is_bookable
                             }">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-sm font-black text-slate-900" x-text="slot.start_time + ' - ' + slot.end_time"></span>

                                    <!-- Status Badge -->
                                    <template x-if="slot.status === 'closed'">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">Ditutup</span>
                                    </template>
                                    <template x-if="slot.status !== 'closed' && slot.estimated_available <= 0">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Penuh</span>
                                    </template>
                                    <template x-if="slot.status !== 'closed' && slot.estimated_available > 0">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                                    </template>
                                </div>

                                <div class="mt-4 flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-medium">Kapasitas Slot:</span>
                                    <span class="font-bold text-slate-800" x-text="slot.capacity + ' Kursi'"></span>
                                </div>

                                <div class="mt-1 flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-medium">Sisa Kursi Estimasi:</span>
                                    <span class="font-black"
                                          :class="slot.estimated_available > 0 ? 'text-emerald-600' : 'text-slate-400'"
                                          x-text="slot.estimated_available + ' Kursi'"></span>
                                </div>
                            </div>

                            <div class="mt-5 pt-3.5 border-t border-slate-100">
                                <template x-if="slot.is_bookable">
                                    <button type="button"
                                            @click="openBookingModal(slot)"
                                            class="w-full py-2.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition flex items-center justify-center space-x-1.5 hover:-translate-y-0.5">
                                        <i class="fa-solid fa-ticket text-xs"></i>
                                        <span>Ajukan Booking</span>
                                    </button>
                                </template>

                                <template x-if="!slot.is_bookable">
                                    <button type="button"
                                            disabled
                                            class="w-full py-2.5 px-4 rounded-2xl bg-slate-200 text-slate-500 font-bold text-xs cursor-not-allowed">
                                        <span x-text="slot.status === 'closed' ? 'Slot Ditutup' : 'Slot Penuh'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Empty Slot State -->
            <template x-if="slots.length === 0">
                <div class="text-center py-12 bg-slate-50 rounded-3xl border border-dashed border-slate-200 p-6">
                    <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300 mb-2"></i>
                    <p class="text-xs font-bold text-slate-700">Belum ada slot waktu pada tanggal ini.</p>
                    <p class="text-[11px] text-slate-400 mt-1">Silakan pilih tanggal lain untuk mengecek jadwal meja.</p>
                </div>
            </template>
        </div>
    </div>

    <!-- CUSTOM BOOKING MODAL -->
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="modalOpen = false">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5"
             @click.outside="modalOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div>
                    <h3 class="text-base font-black text-slate-900">Reservasi Meja & Kursi</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="'{{ $store->name }}'"></p>
                </div>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Booking Form -->
            <form action="{{ route('user.bookings.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="store_id" value="{{ $store->id }}">
                <input type="hidden" name="slot_id" :value="selectedSlot?.id">

                <!-- Schedule Summary Box -->
                <div class="p-3.5 bg-blue-50/80 rounded-2xl border border-blue-100 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-blue-700 font-medium">Tanggal:</span>
                        <strong class="text-blue-950 font-bold" x-text="formatDateLabel(currentDate)"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-blue-700 font-medium">Waktu:</span>
                        <strong class="text-blue-950 font-mono font-bold" x-text="selectedSlot ? (selectedSlot.start_time + ' - ' + selectedSlot.end_time) : ''"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-blue-700 font-medium">Sisa Kursi:</span>
                        <strong class="text-emerald-700 font-bold" x-text="selectedSlot ? (selectedSlot.estimated_available + ' Kursi') : ''"></strong>
                    </div>
                </div>

                <!-- Seat Count Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jumlah Kursi yang Dipesan <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="seat_count"
                           required
                           min="1"
                           :max="selectedSlot?.estimated_available || 1"
                           value="1"
                           class="w-full text-sm font-bold rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3">
                    <p class="text-[11px] text-slate-400 mt-1">Minimal 1 kursi, maksimal sisa kuota estimasi.</p>
                </div>

                <!-- Notes Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea name="notes"
                              rows="3"
                              placeholder="Cth: Meja dekat stopkontak, nugas kelompok 2 laptop..."
                              class="w-full text-xs rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 p-3"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition hover:-translate-y-0.5">
                        Kirim Permintaan Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- REVIEWS & VISITOR EXPERIENCE SECTION -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 md:p-10 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-xl font-black text-slate-900">Ulasan & Pengalaman Pengunjung</h2>
                <p class="text-xs text-slate-500 mt-1">Ulasan otentik dari pengunjung yang telah menyelesaikan reservasi meja.</p>
            </div>
            <div class="flex items-center space-x-2 text-amber-500 font-bold text-sm bg-amber-50 px-4 py-1.5 rounded-2xl border border-amber-200">
                <i class="fa-solid fa-star"></i>
                <span class="text-amber-950 font-black">{{ number_format($store->average_rating, 1) }}</span>
                <span class="text-xs text-amber-700 font-semibold">/ 5.0</span>
            </div>
        </div>

        @if($store->reviews->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($store->reviews as $rev)
                    <div class="py-5 first:pt-0 last:pb-0 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-black text-xs flex items-center justify-center border border-blue-100 shadow-2xs">
                                    {{ strtoupper(substr($rev->user->name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">
                                        {{ $rev->user ? Str::mask($rev->user->name, '*', 3) : 'Pelanggan i-Find' }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400">{{ $rev->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <!-- Star Rating -->
                            <div class="flex items-center space-x-0.5 text-amber-400 text-xs">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="fa-solid fa-star {{ $s <= $rev->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>
                        </div>

                        @if($rev->comment)
                            <p class="text-xs text-slate-600 pl-11 leading-relaxed font-normal">
                                "{{ $rev->comment }}"
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 text-slate-400 text-xs">
                <i class="fa-regular fa-comments text-3xl mb-2 text-slate-300"></i>
                <p class="font-bold text-slate-600">Belum ada ulasan untuk tempat ini.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Jadilah pengunjung pertama yang memesan dan berbagi pengalaman!</p>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function storeDetail(initialSlots, initialDate, storeSlug) {
    return {
        slots: initialSlots,
        currentDate: initialDate,
        storeSlug: storeSlug,
        loadingSlots: false,
        modalOpen: false,
        selectedSlot: null,

        formatDateLabel(dateStr) {
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        },

        selectDate(dateStr) {
            this.currentDate = dateStr;
            this.loadingSlots = true;

            // Fetch slots realtime via AJAX
            fetch(`/user/stores/${this.storeSlug}?date=${dateStr}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.slots = data.slots || [];
                this.loadingSlots = false;
            })
            .catch(err => {
                console.error('Failed to fetch slots:', err);
                this.loadingSlots = false;
            });
        },

        openBookingModal(slot) {
            this.selectedSlot = slot;
            this.modalOpen = true;
        }
    }
}
</script>
@endpush
@endsection

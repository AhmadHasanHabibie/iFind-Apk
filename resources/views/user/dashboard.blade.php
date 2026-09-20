@extends('layouts.user')

@section('content')
<div class="space-y-12" x-data="userSearch()">

    <!-- HERO SECTION (IDENTIK DENGAN GAMBAR REFERENSI) -->
    <section class="relative pt-6 pb-12 text-center">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Badge Pill -->
            <div class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full text-xs font-bold bg-blue-50 border border-blue-200 text-blue-700 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span>Platform Reservasi & LBS Khusus Pelajar #1</span>
            </div>

            <!-- Big Heading with Accent Blue -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-slate-900 leading-[1.15]">
                Tempat Kumpul Spot Hemat dan <span class="text-blue-600">Hangout</span> Terbaik
            </h1>

            <!-- Subtitle -->
            <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed font-medium">
                Rekomendasi tempat nongkrong yang pas di kantong siswa. Temukan spot terdekat menggunakan teknologi GPS akurat dan booking meja instan tanpa ribet.
            </p>

            <!-- Quick Action CTA Buttons -->
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-2">
                <button type="button"
                        @click="detectLocation()"
                        :disabled="locating"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-2xl shadow-xl shadow-blue-600/25 hover:shadow-blue-600/40 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2.5 text-base group">
                    <i class="fa-solid fa-location-crosshairs text-base" :class="{ 'animate-spin': locating }"></i>
                    <span x-show="!locating">Lokasi Terdekat</span>
                    <span x-show="locating">Mencari Lokasi GPS...</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform" x-show="!locating"></i>
                </button>

                <a href="#spotList"
                   class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-700 font-bold px-8 py-4 rounded-2xl border border-slate-200 hover:border-slate-300 shadow-xs hover:shadow hover:-translate-y-0.5 active:translate-y-0 transition-all text-base flex items-center justify-center">
                    Jelajahi Hangout Pedia
                </a>
            </div>

            <!-- Floating Stats Card Preview -->
            <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 p-5 rounded-3xl bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-xl shadow-slate-200/40 text-left">
                <div class="p-3">
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-bold">Akurasi GPS</p>
                    <p class="text-xl sm:text-2xl font-black text-blue-600 mt-0.5">Real-Time</p>
                </div>
                <div class="p-3 border-l border-slate-100">
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-bold">Budget Pelajar</p>
                    <p class="text-xl sm:text-2xl font-black text-emerald-600 mt-0.5">&lt; Rp 25rb</p>
                </div>
                <div class="p-3 border-l sm:border-l-0 md:border-l border-slate-100">
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-bold">Fitur Booking</p>
                    <p class="text-xl sm:text-2xl font-black text-indigo-600 mt-0.5">Instant</p>
                </div>
                <div class="p-3 border-l border-slate-100">
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-bold">Panduan Lokasi</p>
                    <p class="text-xl sm:text-2xl font-black text-sky-600 mt-0.5">Hangout Pedia</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FLOATING SEARCH & FILTER BAR -->
    <section id="searchSection" class="max-w-4xl mx-auto">
        <form action="{{ route('user.dashboard') }}" method="GET" id="searchForm" class="space-y-3">
            <input type="hidden" name="lat" id="userLat" value="{{ request('lat') }}">
            <input type="hidden" name="lng" id="userLng" value="{{ request('lng') }}">

            <!-- Main Input Container with Glassmorphic Card Style -->
            <div class="bg-white rounded-3xl p-3 sm:p-4 shadow-xl shadow-slate-200/60 border border-slate-200 flex flex-col sm:flex-row gap-3 items-center">
                <!-- Search Keyword -->
                <div class="flex-1 flex items-center w-full px-3">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-lg mr-3"></i>
                    <input type="text"
                           name="keyword"
                           value="{{ request('keyword') }}"
                           placeholder="Cari spot nongkrong, coworking, kafe, atau kota (Jakarta, Bandung, Depok)..."
                           class="w-full text-sm font-semibold border-0 focus:ring-0 text-slate-800 placeholder-slate-400 p-0">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 sm:border-l border-slate-100 pl-0 sm:pl-3">
                    <!-- Category Select -->
                    <select name="category_id" class="text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-3 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Submit Button -->
                    <button type="submit" class="px-7 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/25 hover:shadow-blue-600/40 transition-all shrink-0 flex items-center justify-center space-x-2">
                        <span>Cari</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Secondary Filter Chips -->
            <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                <!-- Geolocation Button -->
                <div class="inline-flex items-center">
                    <button type="button"
                            @click="detectLocation()"
                            :disabled="locating"
                            class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl transition-all border font-bold shadow-xs hover:-translate-y-0.5"
                            :class="hasLocation ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200'"
                            title="Klik untuk mencari toko dari jarak terdekat ke posisi Anda">
                        <i class="fa-solid fa-location-crosshairs" :class="{ 'text-emerald-600': hasLocation, 'text-blue-600': !hasLocation, 'animate-spin': locating }"></i>
                        <span x-text="locationLabel">{{ request()->filled('lat') ? 'Lokasi Terdekat Aktif' : 'Lokasi Terdekat' }}</span>
                        <template x-if="hasLocation">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse ml-0.5"></span>
                        </template>
                    </button>
                    @if(request()->filled('lat') && request()->filled('lng'))
                        <a href="{{ route('user.dashboard', request()->except(['lat', 'lng', 'radius'])) }}#spotList"
                           class="ml-1 text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-slate-100 transition"
                           title="Hapus filter lokasi terdekat">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                </div>

                <!-- Radius Filter -->
                <div class="inline-flex items-center space-x-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-slate-700 shadow-xs">
                    <i class="fa-solid fa-route text-blue-500 text-xs"></i>
                    <span class="font-bold text-slate-500">Radius:</span>
                    <select name="radius" onchange="document.getElementById('searchForm').submit()" class="bg-transparent text-slate-800 border-0 py-0 pl-1 pr-6 text-xs font-bold focus:ring-0 cursor-pointer">
                        <option value="">Semua Jarak</option>
                        <option value="5" {{ request('radius') == '5' ? 'selected' : '' }}>Maks. 5 km</option>
                        <option value="10" {{ request('radius') == '10' ? 'selected' : '' }}>Maks. 10 km</option>
                        <option value="25" {{ request('radius') == '25' ? 'selected' : '' }}>Maks. 25 km</option>
                    </select>
                </div>

                <!-- Rating Filter -->
                <div class="inline-flex items-center space-x-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-slate-700 shadow-xs">
                    <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                    <span class="font-bold text-slate-500">Rating:</span>
                    <select name="min_rating" onchange="document.getElementById('searchForm').submit()" class="bg-transparent text-slate-800 border-0 py-0 pl-1 pr-6 text-xs font-bold focus:ring-0 cursor-pointer">
                        <option value="">Semua Rating</option>
                        <option value="4.0" {{ request('min_rating') == '4.0' ? 'selected' : '' }}>4.0+ Bintang</option>
                        <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>4.5+ Bintang</option>
                    </select>
                </div>

<<<<<<< HEAD
                <!-- Facility Filter Dropdown -->
                <div class="relative" x-data="{ openFac: false }" @click.outside="openFac = false">
                    <button type="button" @click="openFac = !openFac"
                            class="inline-flex items-center space-x-1.5 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-3 py-1.5 text-slate-700 shadow-xs font-bold transition">
                        <i class="fa-solid fa-layer-group text-teal-600 text-xs"></i>
                        <span>Fasilitas</span>
                        @php
                            $selectedFacCount = is_array(request('facilities')) ? count(request('facilities')) : 0;
                        @endphp
                        @if($selectedFacCount > 0)
                            <span class="w-4 h-4 rounded-full bg-blue-600 text-white text-[10px] font-black flex items-center justify-center">{{ $selectedFacCount }}</span>
                        @endif
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-0.5"></i>
                    </button>

                    <div x-show="openFac" x-cloak style="display: none;"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-64 p-3 bg-white rounded-2xl shadow-xl border border-slate-200 z-50 space-y-2">
                        <p class="text-xs font-bold text-slate-900 border-b border-slate-100 pb-1.5">Pilih Fasilitas</p>
                        <div class="max-h-48 overflow-y-auto space-y-1.5 pr-1">
                            @foreach($facilities as $facility)
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 p-1.5 rounded-lg cursor-pointer transition">
                                    <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                           {{ is_array(request('facilities')) && in_array($facility->id, request('facilities')) ? 'checked' : '' }}
                                           class="rounded-md border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <i class="{{ $facility->icon }} text-slate-400 text-xs"></i>
                                    <span class="truncate">{{ $facility->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>

                @if(request()->hasAny(['keyword', 'category_id', 'min_rating', 'radius', 'lat', 'facilities']))
=======
                @if(request()->hasAny(['keyword', 'category_id', 'min_rating', 'radius', 'lat']))
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d
                    <a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-bold px-3 py-1.5 rounded-xl hover:bg-blue-50 transition ml-auto">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </section>

    <!-- SPOT LIST / RESULTS SECTION -->
    <section id="spotList" class="space-y-6 pt-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center space-x-2.5">
                    <span>
                        @if(request('keyword') || request('category_id') || request('min_rating') || request('radius'))
                            Hasil Pencarian Spot
                        @elseif(request('lat') && request('lng'))
                            Rekomendasi Spot Terdekat dari Posisi Anda
                        @else
                            Rekomendasi Tempat Populer
                        @endif
                    </span>
                    <span class="text-xs font-black px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $stores->total() }} Tempat
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    @if(request('lat') && request('lng'))
                        Diurutkan presisi berdasarkan jarak geolokasi dan status ketersediaan meja.
                    @else
                        Diurutkan berdasarkan rating pengunjung tertinggi & kenyamanan belajar kelompok.
                    @endif
                </p>
            </div>
        </div>

        @if($stores->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($stores as $store)
                    @include('user.partials._store-card', ['store' => $store])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-6 flex justify-center">
                {{ $stores->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 space-y-4 max-w-xl mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center mx-auto text-2xl shadow-xs">
                    <i class="fa-solid fa-mug-saucer"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Belum ada spot yang cocok</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                        Coba gunakan kata kunci kota lain, kurangi filter radius jarak, atau jelajahi kategori tempat lainnya.
                    </p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition">
                    Lihat Semua Tempat
                </a>
            </div>
        @endif
    </section>

</div>

@push('scripts')
<script>
function userSearch() {
    return {
        locating: false,
        hasLocation: {{ request()->filled('lat') && request()->filled('lng') ? 'true' : 'false' }},
        locationLabel: '{{ request()->filled('lat') ? "Lokasi Terdekat Aktif" : "Lokasi Terdekat" }}',

        detectLocation() {
            if (!navigator.geolocation) {
                window.dispatchEvent(new CustomEvent('custom-toast', {
                    detail: {
                        title: 'Tidak Didukung',
                        message: 'Browser Anda tidak mendukung deteksi lokasi geografis.',
                        type: 'error'
                    }
                }));
                return;
            }

            this.locating = true;
            this.locationLabel = 'Mencari GPS...';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    document.getElementById('userLat').value = lat;
                    document.getElementById('userLng').value = lng;

                    this.locating = false;
                    this.hasLocation = true;
                    this.locationLabel = 'Lokasi Terdekat Aktif';

                    window.dispatchEvent(new CustomEvent('custom-toast', {
                        detail: {
                            title: 'Lokasi Terdeteksi',
                            message: 'Mengurutkan spot nongkrong dari yang paling dekat di urutan teratas.',
                            type: 'success'
                        }
                    }));

                    // Submit form pencarian untuk refresh hasil berdasar jarak terdekat
                    setTimeout(() => {
                        const form = document.getElementById('searchForm');
                        form.action = "{{ route('user.dashboard') }}#spotList";
                        form.submit();
                    }, 350);
                },
                (error) => {
                    this.locating = false;
                    this.locationLabel = this.hasLocation ? 'Lokasi Terdekat Aktif' : 'Lokasi Terdekat';
                    console.warn('Geolocation error:', error.message);

                    window.dispatchEvent(new CustomEvent('custom-toast', {
                        detail: {
                            title: 'Izin Lokasi Ditolak',
                            message: 'Mohon izinkan akses lokasi pada browser Anda untuk mencari spot terdekat.',
                            type: 'error'
                        }
                    }));
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
        }
    }
}
</script>
@endpush
@endsection

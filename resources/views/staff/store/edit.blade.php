@extends('layouts.staff')

@section('header_title', $isCreate ? 'Konfigurasi Profil Toko' : 'Edit Profil Toko')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">
                    {{ $isCreate ? 'Pendaftaran Profil Toko Baru' : 'Perbarui Profil Toko' }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $isCreate ? 'Lengkapi informasi toko atau kafe Anda agar dapat mulai menerima reservasi pelanggan.' : 'Perbarui informasi operasional, fasilitas, dan foto etalase toko Anda.' }}
                </p>
            </div>
            @if(! $isCreate)
                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Status: Approved & Aktif
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ $isCreate ? route('staff.store.store') : route('staff.store.update') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if(! $isCreate)
                @method('PUT')
            @endif

            <!-- 1. Informasi Pokok -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">Informasi Pokok</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Nama Toko / Tempat <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $store->name) }}"
                               required
                               placeholder="Contoh: Titik Temu Coffee & Space"
                               class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Kategori Tempat <span class="text-rose-500">*</span>
                        </label>
                        <select id="category_id"
                                name="category_id"
                                required
                                class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $store->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Nomor Telepon Toko / Kontak
                        </label>
                        <input type="text"
                               id="phone"
                               name="phone"
                               value="{{ old('phone', $store->phone) }}"
                               placeholder="08123456789"
                               class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Deskripsi Singkat & Suasana Tempat
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="Jelaskan keunggulan tempat, suasana belajar, aturan umum, atau menu andalan..."
                                  class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $store->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Lokasi & Alamat -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Alamat & Lokasi Geografis</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Digunakan untuk fitur pencarian <strong>"Lokasi Terdekat" (LBS)</strong> dan petunjuk arah pelanggan.</p>
                    </div>
                    <button type="button"
                            onclick="detectLocation()"
                            id="btn-geolocation"
                            class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200 transition">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Gunakan GPS Saat Ini</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Alamat Lengkap -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Alamat Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="address"
                                  name="address"
                                  rows="2"
                                  required
                                  placeholder="Nama jalan, nomor bangunan, patokan..."
                                  class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('address', $store->address) }}</textarea>
                    </div>

                    <!-- Kota -->
                    <div class="sm:col-span-2 sm:grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Kota / Wilayah <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   id="city"
                                   name="city"
                                   value="{{ old('city', $store->city) }}"
                                   required
                                   placeholder="Contoh: Jakarta Selatan, Bandung"
                                   class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Opsi Input Lokasi (Box Pilihan Metode) -->
                    <div class="sm:col-span-2 bg-gradient-to-br from-slate-50 to-blue-50/30 p-4 sm:p-5 rounded-2xl border border-slate-200/80 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-blue-900 block flex items-center gap-1.5">
                                    <i class="fa-solid fa-map-pin text-blue-600"></i>
                                    <span>Penentuan Titik Lokasi Peta</span>
                                </span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Pilih metode paling mudah bagi Anda: tempel link Google Maps atau isi koordinat manual.</p>
                            </div>

                            <!-- Mode Selector Pills -->
                            <div class="inline-flex bg-slate-200/80 p-1 rounded-xl text-xs font-bold shrink-0" id="location-mode-wrapper">
                                <button type="button"
                                        onclick="switchLocationMode('link')"
                                        id="tab-mode-link"
                                        class="px-3 py-1 rounded-lg transition bg-white text-blue-700 shadow-xs">
                                    🔗 Link Google Maps
                                </button>
                                <button type="button"
                                        onclick="switchLocationMode('manual')"
                                        id="tab-mode-manual"
                                        class="px-3 py-1 rounded-lg transition text-slate-600 hover:text-slate-900">
                                    📍 Input Koordinat
                                </button>
                            </div>
                        </div>

                        <!-- 1. OPSI TEMPEL LINK GOOGLE MAPS (DEFAULT & PALING MUDAH) -->
                        <div id="container-mode-link" class="space-y-2">
                            <label for="maps_url" class="block text-xs font-bold text-slate-700">
                                Tempel Link Google Maps Toko Anda
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                        <i class="fa-brands fa-google text-sm"></i>
                                    </span>
                                    <input type="url"
                                           id="maps_url"
                                           name="maps_url"
                                           placeholder="Contoh: https://maps.app.goo.gl/xxx atau https://google.com/maps/place/..."
                                           class="w-full pl-9 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <button type="button"
                                        id="btn-extract-maps"
                                        onclick="extractFromMapsLink()"
                                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-1.5 shrink-0">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                    <span>Ekstrak Koordinat</span>
                                </button>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px] text-slate-500">
                                <i class="fa-regular fa-lightbulb text-amber-500 mt-0.5"></i>
                                <span><strong>Cara mudah:</strong> Buka Google Maps di HP / Laptop &rarr; Cari tempat toko Anda &rarr; Klik tombol <strong>Bagikan (Share)</strong> &rarr; Pilih <strong>Salin Link</strong> &rarr; Tempel di kotak atas.</span>
                            </div>
                        </div>

                        <!-- 2. OPSI KOORDINAT MANUAL -->
                        <div id="container-mode-manual" class="grid grid-cols-2 gap-3 hidden">
                            <div>
                                <label for="latitude" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Latitude</label>
                                <input type="number"
                                       step="any"
                                       id="latitude"
                                       name="latitude"
                                       value="{{ old('latitude', $store->latitude) }}"
                                       placeholder="-6.2000000"
                                       oninput="onManualCoordinateChange()"
                                       class="w-full text-xs font-mono rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="longitude" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Longitude</label>
                                <input type="number"
                                       step="any"
                                       id="longitude"
                                       name="longitude"
                                       value="{{ old('longitude', $store->longitude) }}"
                                       placeholder="106.8166660"
                                       oninput="onManualCoordinateChange()"
                                       class="w-full text-xs font-mono rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- 3. INTERACTIVE MAP PICKER & LIVE PREVIEW -->
                        <div class="space-y-2 pt-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-700 flex items-center gap-1">
                                    <i class="fa-solid fa-map-location-dot text-blue-600"></i>
                                    Peta Interaktif Lokasi Toko
                                </span>
                                <span class="text-[11px] text-slate-500 italic">
                                    Klik pada peta atau geser pin merah untuk koreksi posisi
                                </span>
                            </div>
                            <div id="map-picker" class="w-full rounded-xl border border-slate-300 shadow-inner overflow-hidden" style="height: 300px; min-height: 300px; width: 100%; position: relative; z-index: 1;"></div>
                        </div>

                        <!-- STATUS PREVIEW KOORDINAT TERPASANG -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-200/60 text-xs">
                            <div class="flex items-center space-x-2" id="status-coords-container">
                                @if($store->latitude && $store->longitude)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-slate-700">Koordinat terpasang: <strong class="font-mono text-emerald-700" id="label-coords">{{ $store->latitude }}, {{ $store->longitude }}</strong></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span class="text-slate-500" id="label-coords">Belum ada titik koordinat yang terpasang.</span>
                                @endif
                            </div>

                            <a id="preview-maps-link"
                               href="{{ ($store->latitude && $store->longitude) ? 'https://www.google.com/maps?q=' . $store->latitude . ',' . $store->longitude : '#' }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="{{ ($store->latitude && $store->longitude) ? 'inline-flex' : 'hidden' }} items-center space-x-1.5 font-bold text-blue-600 hover:text-blue-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-2xs shrink-0">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                <span>Cek Titik di Google Maps</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Fasilitas Tempat -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-100 pb-2">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Fasilitas Tersedia</h3>
                    <p class="text-xs text-slate-500">Pilih fasilitas yang tersedia untuk pelanggan di tempat Anda</p>
                </div>

                @php
                    $selectedFacilities = old('facilities', $store->facilities ? $store->facilities->pluck('id')->toArray() : []);
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($facilities as $facility)
                        <label class="relative flex items-center p-3 border rounded-xl cursor-pointer hover:border-blue-400 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40">
                            <input type="checkbox"
                                   name="facilities[]"
                                   value="{{ $facility->id }}"
                                   class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                   {{ in_array($facility->id, $selectedFacilities) ? 'checked' : '' }}>
                            <span class="ml-2.5 text-xs font-bold text-slate-800">{{ $facility->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 4. Pengaturan Harga & Pembayaran Partial -->
            @include('staff.store.partials._payment')

            <!-- 5. Jam Operasional Partial -->
            @include('staff.store.partials._hours')

            <!-- 6. Foto Galeri Partial -->
            @include('staff.store.partials._photos')

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                <a href="{{ route('staff.dashboard') }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    {{ $isCreate ? 'Daftarkan Profil Toko' : 'Simpan Perubahan Toko' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Hidden Forms for Photo Actions -->
    @if(! $isCreate && $store->photos)
        @foreach($store->photos as $photo)
            <form id="delete-photo-form-{{ $photo->id }}"
                  action="{{ route('staff.store.photos.delete', $photo) }}"
                  method="POST"
                  class="hidden">
                @csrf
                @method('DELETE')
            </form>

            <form id="set-primary-form-{{ $photo->id }}"
                  action="{{ route('staff.store.photos.primary', $photo) }}"
                  method="POST"
                  class="hidden">
                @csrf
                @method('PATCH')
            </form>
        @endforeach
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container {
        font-family: inherit;
        z-index: 1 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map = null;
    let mapMarker = null;

    document.addEventListener('DOMContentLoaded', function() {
        initStoreMap();
    });

    function initStoreMap() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        let initialLat = parseFloat(latInput ? latInput.value : null);
        let initialLng = parseFloat(lngInput ? lngInput.value : null);

        const hasValidCoords = !isNaN(initialLat) && !isNaN(initialLng) && initialLat !== 0 && initialLng !== 0;
        const defaultCenter = hasValidCoords ? [initialLat, initialLng] : [-6.200000, 106.816666]; // Default Jakarta
        const defaultZoom = hasValidCoords ? 16 : 13;

        try {
            if (typeof L === 'undefined') {
                console.error('Leaflet JS belum termuat.');
                return;
            }

            map = L.map('map-picker', {
                center: defaultCenter,
                zoom: defaultZoom,
                zoomControl: true,
                scrollWheelZoom: 'center'
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            if (hasValidCoords) {
                setMapMarker(initialLat, initialLng, false);
            }

            // Click map to reposition marker
            map.on('click', function(e) {
                const { lat, lng } = e.latlng;
                updateCoordinatesFromMap(lat, lng);
            });

            // Trigger map resize fix
            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 300);
        } catch (err) {
            console.error('Error initializing map:', err);
        }
    }

    function setMapMarker(lat, lng, fly = true) {
        if (!map) return;

        lat = parseFloat(lat);
        lng = parseFloat(lng);

        if (isNaN(lat) || isNaN(lng)) return;

        if (mapMarker) {
            mapMarker.setLatLng([lat, lng]);
        } else {
            mapMarker = L.marker([lat, lng], {
                draggable: true,
                autoPan: true
            }).addTo(map);

            mapMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateCoordinatesFromMap(pos.lat, pos.lng);
            });
        }

        mapMarker.bindPopup('<strong>📍 Titik Lokasi Toko</strong><br><span class="text-xs text-slate-500">Geser pin untuk penyesuaian presisi</span>').openPopup();

        if (fly) {
            map.flyTo([lat, lng], 17, { duration: 1 });
        }
    }

    function updateCoordinatesFromMap(lat, lng) {
        const cleanLat = parseFloat(lat).toFixed(7);
        const cleanLng = parseFloat(lng).toFixed(7);

        document.getElementById('latitude').value = cleanLat;
        document.getElementById('longitude').value = cleanLng;

        setMapMarker(cleanLat, cleanLng, false);
        updateStatusLabels(cleanLat, cleanLng);
    }

    function onManualCoordinateChange() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);

        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            setMapMarker(lat, lng, true);
            updateStatusLabels(lat, lng);
        } else {
            updateStatusLabels(null, null);
        }
    }

    function updateStatusLabels(lat, lng) {
        const link = document.getElementById('preview-maps-link');
        const statusContainer = document.getElementById('status-coords-container');

        if (lat !== null && lng !== null && !isNaN(lat) && !isNaN(lng)) {
            link.href = 'https://www.google.com/maps?q=' + encodeURIComponent(lat) + ',' + encodeURIComponent(lng);
            link.classList.remove('hidden');
            link.classList.add('inline-flex');

            if (statusContainer) {
                statusContainer.innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-slate-700">Koordinat terpasang: <strong class="font-mono text-emerald-700">${lat}, ${lng}</strong></span>
                `;
            }
        } else {
            link.classList.add('hidden');
            link.classList.remove('inline-flex');
            if (statusContainer) {
                statusContainer.innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="text-slate-500">Belum ada titik koordinat yang terpasang.</span>
                `;
            }
        }
    }

    function switchLocationMode(mode) {
        const linkTab = document.getElementById('tab-mode-link');
        const manualTab = document.getElementById('tab-mode-manual');
        const linkContainer = document.getElementById('container-mode-link');
        const manualContainer = document.getElementById('container-mode-manual');

        if (mode === 'link') {
            linkTab.className = 'px-3 py-1 rounded-lg transition bg-white text-blue-700 shadow-xs';
            manualTab.className = 'px-3 py-1 rounded-lg transition text-slate-600 hover:text-slate-900';
            linkContainer.classList.remove('hidden');
            manualContainer.classList.add('hidden');
        } else {
            manualTab.className = 'px-3 py-1 rounded-lg transition bg-white text-blue-700 shadow-xs';
            linkTab.className = 'px-3 py-1 rounded-lg transition text-slate-600 hover:text-slate-900';
            manualContainer.classList.remove('hidden');
            linkContainer.classList.add('hidden');
        }

        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 150);
    }

    // Client-side quick regex parser for Google Maps URL
    function parseCoordinatesClientSide(rawUrl) {
        if (!rawUrl) return null;
        let url = decodeURIComponent(rawUrl.trim());

        // Pattern 1: !3dlat!4dlng (Highest accuracy pin)
        let m = url.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
        if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

        // Pattern 2: !2dlng!3dlat (Embed pb)
        m = url.match(/!2d(-?\d+\.\d+)!3d(-?\d+\.\d+)/);
        if (m) return { lat: parseFloat(m[2]), lng: parseFloat(m[1]) };

        // Pattern 3: /place/lat,lng or /search/lat,lng
        m = url.match(/\/(?:place|search)\/(-?\d{1,2}\.\d+)[,\s+]+(-?\d{1,3}\.\d+)/i);
        if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

        // Pattern 4: query params ?q=lat,lng or query=lat,lng or destination=lat,lng
        m = url.match(/[?&](?:q|query|destination|daddr|ll|markers)=(-?\d{1,2}\.\d+)[,\s+]+(-?\d{1,3}\.\d+)/i);
        if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

        // Pattern 5: raw coordinates
        m = url.match(/^\s*(-?\d{1,2}(?:\.\d+)?)\s*,\s*(-?\d{1,3}(?:\.\d+)?)\s*$/);
        if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

        return null;
    }

    async function extractFromMapsLink() {
        const urlInput = document.getElementById('maps_url');
        const btn = document.getElementById('btn-extract-maps');
        const url = urlInput.value.trim();

        if (!url) {
            alert('Silakan tempel link Google Maps terlebih dahulu.');
            urlInput.focus();
            return;
        }

        // Coba ekstrak instan di sisi klien jika format link langsung lengkap
        const clientParsed = parseCoordinatesClientSide(url);
        if (clientParsed && clientParsed.lat && clientParsed.lng) {
            document.getElementById('latitude').value = clientParsed.lat;
            document.getElementById('longitude').value = clientParsed.lng;
            setMapMarker(clientParsed.lat, clientParsed.lng, true);
            updateStatusLabels(clientParsed.lat, clientParsed.lng);
        }

        const originalBtnHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin mr-1 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Membaca Link...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('{{ route("staff.store.parse-maps-url") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ url: url })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                document.getElementById('latitude').value = data.latitude;
                document.getElementById('longitude').value = data.longitude;

                setMapMarker(data.latitude, data.longitude, true);
                updateStatusLabels(data.latitude, data.longitude);

                btn.innerHTML = '<span class="text-emerald-300 font-bold">✓ Berhasil</span>';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }, 2500);
            } else {
                if (!clientParsed) {
                    alert(data.message || 'Gagal mengekstrak koordinat dari link tersebut.');
                }
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
            }
        } catch (e) {
            console.error('Error parsing maps URL:', e);
            if (!clientParsed) {
                alert('Terjadi kesalahan saat menghubungi server untuk membaca link.');
            }
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
        }
    }

    function detectLocation() {
        const btn = document.getElementById('btn-geolocation');
        if (!navigator.geolocation) {
            alert('Geolocation tidak didukung oleh browser Anda.');
            return;
        }

        btn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin mr-1 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Mendeteksi GPS...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude.toFixed(7);
                const lng = position.coords.longitude.toFixed(7);
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                btn.innerHTML = '<span class="text-emerald-600 font-bold">✓ Lokasi GPS Terpasang</span>';
                setMapMarker(lat, lng, true);
                updateStatusLabels(lat, lng);
            },
            (error) => {
                alert('Gagal mengambil lokasi GPS: ' + error.message);
                btn.innerHTML = '<svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span>Gunakan GPS Saat Ini</span>';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
</script>
@endpush

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
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Alamat & Lokasi Geografis</h3>
                    <button type="button"
                            onclick="detectLocation()"
                            id="btn-geolocation"
                            class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700 space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Gunakan Lokasi Saat Ini</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Alamat Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="address"
                                  name="address"
                                  rows="2"
                                  required
                                  placeholder="Nama jalan, nomor, patokan..."
                                  class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('address', $store->address) }}</textarea>
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Kota / Wilayah <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="city"
                               name="city"
                               value="{{ old('city', $store->city) }}"
                               required
                               placeholder="Contoh: Jakarta Selatan, Surabaya"
                               class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="latitude" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Latitude</label>
                            <input type="number"
                                   step="any"
                                   id="latitude"
                                   name="latitude"
                                   value="{{ old('latitude', $store->latitude) }}"
                                   placeholder="-6.2000000"
                                   class="w-full text-xs font-mono rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="longitude" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Longitude</label>
                            <input type="number"
                                   step="any"
                                   id="longitude"
                                   name="longitude"
                                   value="{{ old('longitude', $store->longitude) }}"
                                   placeholder="106.8166660"
                                   class="w-full text-xs font-mono rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
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

@push('scripts')
<script>
    function detectLocation() {
        const btn = document.getElementById('btn-geolocation');
        if (!navigator.geolocation) {
            alert('Geolocation tidak didukung oleh browser Anda.');
            return;
        }

        btn.innerHTML = 'Mendeteksi lokasi...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(7);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(7);
                btn.innerHTML = '<span class="text-emerald-600 font-bold">✓ Lokasi terpasang</span>';
            },
            (error) => {
                alert('Gagal mengambil lokasi: ' + error.message);
                btn.innerHTML = 'Gunakan Lokasi Saat Ini';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
</script>
@endpush

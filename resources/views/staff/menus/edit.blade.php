@extends('layouts.staff')

@section('title', 'Edit Menu - ' . $menu->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('staff.menus.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-blue-600 mb-2 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Katalog
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Edit Menu: {{ $menu->name }}</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('staff.menus.update', $menu) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nama Menu -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Menu <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $menu->name) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori & Harga Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Kategori Menu <span class="text-rose-500">*</span></label>
                    <select name="category" id="category" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('category') border-rose-500 @enderror">
                        <option value="minuman" {{ old('category', $menu->category) === 'minuman' ? 'selected' : '' }}>☕ Minuman</option>
                        <option value="makanan" {{ old('category', $menu->category) === 'makanan' ? 'selected' : '' }}>🍛 Makanan</option>
                        <option value="snack" {{ old('category', $menu->category) === 'snack' ? 'selected' : '' }}>🍟 Snack / Cemilan</option>
                        <option value="paket_hemat" {{ old('category', $menu->category) === 'paket_hemat' ? 'selected' : '' }}>🎓 Paket Hemat Pelajar</option>
                        <option value="lainnya" {{ old('category', $menu->category) === 'lainnya' ? 'selected' : '' }}>✨ Lainnya</option>
                    </select>
                    @error('category')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Harga (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="price" id="price" value="{{ old('price', (int)$menu->price) }}" required min="0" step="500"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('price') border-rose-500 @enderror">
                    </div>
                    @error('price')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi Menu -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Deskripsi / Komposisi (Opsional)</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('description') border-rose-500 @enderror">{{ old('description', $menu->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto Menu Saat Ini & Ganti Foto -->
            <div>
                <label for="photo" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Foto Menu</label>
                @if($menu->photo)
                    <div class="flex items-center space-x-4 mb-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <img src="{{ asset('storage/' . $menu->photo) }}" alt="{{ $menu->name }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                        <span class="text-xs text-slate-500">Pilih file baru di bawah jika ingin mengganti foto saat ini.</span>
                    </div>
                @endif
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                <p class="text-[11px] text-slate-400 mt-1">Format gambar: JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                @error('photo')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkbox Opsi Tambahan -->
            <div class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menu->is_available ? '1' : '0') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Menu Tersedia</span>
                        <span class="text-[11px] text-slate-500">Bisa langsung dipesan oleh pelanggan</span>
                    </div>
                </label>

                <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="is_recommended" value="1" {{ old('is_recommended', $menu->is_recommended ? '1' : '0') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-500">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">⭐ Menu Rekomendasi (Chef's Pick)</span>
                        <span class="text-[11px] text-slate-500">Ditampilkan dengan badge khusus di katalog</span>
                    </div>
                </label>
            </div>

            <!-- Tombol Submit -->
            <div class="flex items-center justify-end space-x-3 pt-4">
                <a href="{{ route('staff.menus.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

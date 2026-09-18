@extends('layouts.admin')

@section('header_title', 'Edit Kategori Toko')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-teal-700 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Kategori
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-900">Edit Kategori: {{ $category->name }}</h2>
            <span class="text-xs font-mono text-slate-400">ID #{{ $category->id }}</span>
        </div>

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $category->name) }}"
                       required
                       placeholder="Contoh: Coffee Shop, Coworking Space"
                       class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                <p class="text-[11px] text-slate-400 mt-1">Slug saat ini: <code class="font-mono text-teal-700 bg-teal-50 px-1 py-0.5 rounded">{{ $category->slug }}</code></p>
            </div>

            <!-- Icon -->
            <div>
                <label for="icon" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Icon Class (Opsional)
                </label>
                <input type="text"
                       id="icon"
                       name="icon"
                       value="{{ old('icon', $category->icon) }}"
                       placeholder="Contoh: fa-solid fa-mug-saucer atau coffee"
                       class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                <p class="text-[11px] text-slate-400 mt-1">Nama icon FontAwesome atau class identifier.</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Deskripsi Kategori (Opsional)
                </label>
                <textarea id="description"
                          name="description"
                          rows="3"
                          placeholder="Penjelasan singkat mengenai kategori tempat ini..."
                          class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('description', $category->description) }}</textarea>
            </div>

            <!-- Is Active Toggle -->
            <div class="pt-2">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                    <span class="ml-3 text-sm font-semibold text-slate-700">Status Aktif (Ditampilkan pada pencarian publik)</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.facilities.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 transition shadow-xs">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-black text-slate-900">Tambah Fasilitas Baru</h1>
            <p class="text-xs text-slate-500">Tambahkan opsi fasilitas tempat ke dalam sistem.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.facilities.store') }}" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-xs space-y-5">
        @csrf

        <div class="space-y-1.5">
            <label for="name" class="block text-xs font-bold text-slate-700">Nama Fasilitas <span class="text-rose-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   placeholder="Contoh: Free WiFi Cepat, Stopkontak Setiap Meja"
                   class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-hidden transition @error('name') border-rose-500 @enderror">
            @error('name')
                <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="icon" class="block text-xs font-bold text-slate-700">Class Icon (FontAwesome / SVG)</label>
            <input type="text" id="icon" name="icon" value="{{ old('icon', 'fa-solid fa-check') }}"
                   placeholder="Contoh: fa-solid fa-wifi, fa-solid fa-plug"
                   class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-hidden transition @error('icon') border-rose-500 @enderror">
            <p class="text-[11px] text-slate-400">Gunakan class icon FontAwesome seperti <code>fa-solid fa-wifi</code>, <code>fa-solid fa-plug</code>, <code>fa-solid fa-snowflake</code>.</p>
            @error('icon')
                <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.facilities.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
                Simpan Fasilitas
            </button>
        </div>
    </form>
</div>
@endsection
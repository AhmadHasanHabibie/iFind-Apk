@extends('layouts.user')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('user.tickets.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 transition shadow-xs">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-black text-slate-900">Buat Tiket Bantuan</h1>
            <p class="text-xs text-slate-500">Kirimkan rincian kendala yang Anda alami ke tim dukungan kami.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('user.tickets.store') }}" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-xs space-y-5">
        @csrf

        <!-- Subject -->
        <div class="space-y-1.5">
            <label for="subject" class="block text-xs font-bold text-slate-700">Subjek Tiket <span class="text-rose-500">*</span></label>
            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                   placeholder="Contoh: Kendala Pembayaran QRIS Tidak Terverifikasi"
                   class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-hidden transition @error('subject') border-rose-500 @enderror">
            @error('subject')
                <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category -->
        <div class="space-y-1.5">
            <label for="category" class="block text-xs font-bold text-slate-700">Kategori Masalah <span class="text-rose-500">*</span></label>
            <select id="category" name="category" required
                    class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-hidden transition bg-white @error('category') border-rose-500 @enderror">
                <option value="">-- Pilih Kategori --</option>
                <option value="booking" {{ old('category') === 'booking' ? 'selected' : '' }}>Masalah Reservasi & Jadwal</option>
                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Kendala Teknis / Bug Aplikasi</option>
                <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Akun & Keamanan</option>
                <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Pertanyaan Umum / Lainnya</option>
            </select>
            @error('category')
                <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="space-y-1.5">
            <label for="description" class="block text-xs font-bold text-slate-700">Penjelasan Kendala <span class="text-rose-500">*</span></label>
            <textarea id="description" name="description" rows="5" required
                      placeholder="Jelaskan kendala secara terperinci beserta kode booking terkait (jika ada)..."
                      class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-hidden transition @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('user.tickets.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition">
                <i class="fa-solid fa-paper-plane mr-1.5"></i> Kirim Tiket Bantuan
            </button>
        </div>
    </form>
</div>
@endsection
@extends('layouts.staff')

@section('header_title', 'Edit Slot Waktu')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div>
        <a href="{{ route('staff.slots.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-blue-700 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Slot
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-xl font-extrabold text-slate-900">Perbarui Slot Waktu</h2>
            <p class="text-xs text-slate-500 mt-1">
                {{ \Carbon\Carbon::parse($slot->date)->isoFormat('dddd, D MMMM Y') }} &bull;
                <span class="font-mono font-bold text-blue-700">{{ substr($slot->start_time, 0, 5) }} &ndash; {{ substr($slot->end_time, 0, 5) }}</span>
            </p>
        </div>

        <form method="POST" action="{{ route('staff.slots.update', $slot) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Current Booked Info -->
            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100 text-xs text-blue-900 flex items-center justify-between">
                <div>
                    <span class="font-semibold">Kursi Terisi Saat Ini:</span>
                    <strong class="text-sm font-bold text-blue-800 ml-1">{{ $slot->booked_seats }} kursi</strong>
                </div>
                <span class="text-slate-500">Status saat ini: <strong class="uppercase font-bold text-blue-700">{{ $slot->status }}</strong></span>
            </div>

            <!-- Capacity Input -->
            <div>
                <label for="capacity" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Kapasitas Kursi Baru <span class="text-rose-500">*</span>
                </label>
                <input type="number"
                       id="capacity"
                       name="capacity"
                       value="{{ old('capacity', $slot->capacity) }}"
                       required
                       min="{{ max(1, $slot->booked_seats) }}"
                       max="500"
                       class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                <p class="text-[11px] text-slate-400 mt-1">
                    Minimal harus {{ max(1, $slot->booked_seats) }} kursi (tidak boleh lebih kecil dari kursi yang sudah terisi).
                </p>
            </div>

            <!-- Status Override -->
            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Status Operasional Slot <span class="text-rose-500">*</span>
                </label>
                <select id="status"
                        name="status"
                        required
                        class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="available" {{ old('status', $slot->status) !== 'closed' ? 'selected' : '' }}>
                        Buka (Sistem otomatis menandai Tersedia atau Penuh)
                    </option>
                    <option value="closed" {{ old('status', $slot->status) === 'closed' ? 'selected' : '' }}>
                        Tutup Manual (Tidak menerima booking baru)
                    </option>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('staff.slots.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

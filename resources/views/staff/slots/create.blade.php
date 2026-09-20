@extends('layouts.staff')

@section('header_title', 'Tambah / Generate Slot Waktu')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ mode: 'bulk' }">
    <!-- Back Button -->
    <div>
        <a href="{{ route('staff.slots.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-blue-700 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Slot
        </a>
    </div>

    <!-- Mode Selector Tabs -->
    <div class="p-1.5 bg-slate-200/80 rounded-2xl flex max-w-md mx-auto">
        <button type="button"
                @click="mode = 'bulk'"
                :class="mode === 'bulk' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="flex-1 py-2.5 text-xs font-bold rounded-xl transition text-center">
            Generate Otomatis (Bulk)
        </button>
        <button type="button"
                @click="mode = 'single'"
                :class="mode === 'single' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="flex-1 py-2.5 text-xs font-bold rounded-xl transition text-center">
            Tambah 1 Slot Manual
        </button>
    </div>

    <!-- Mode 1: Bulk Generate Form -->
    <div x-show="mode === 'bulk'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-xl font-extrabold text-slate-900">Bulk Generate Slot Waktu</h2>
            <p class="text-xs text-slate-500 mt-1">
                Buat jadwal slot waktu sekaligus untuk periode tertentu dan hari-hari operasional pilihan Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('staff.slots.bulk-generate') }}" class="space-y-6">
            @csrf

            <!-- Date Range -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Tanggal Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date"
                           id="start_date"
                           name="start_date"
                           value="{{ old('start_date', \Carbon\Carbon::today()->toDateString()) }}"
                           required
                           min="{{ \Carbon\Carbon::today()->toDateString() }}"
                           class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Tanggal Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date"
                           id="end_date"
                           name="end_date"
                           value="{{ old('end_date', \Carbon\Carbon::today()->addDays(6)->toDateString()) }}"
                           required
                           min="{{ \Carbon\Carbon::today()->toDateString() }}"
                           class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Operational Days Checkbox -->
            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Pilih Hari Aktif <span class="text-rose-500">*</span>
                </label>
                @php
                    $allDays = [
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                    ];
                    $selectedDays = old('days', array_keys($allDays));
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($allDays as $dayKey => $dayLabel)
                        <label class="flex items-center p-2.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50">
                            <input type="checkbox"
                                   name="days[]"
                                   value="{{ $dayKey }}"
                                   class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                   {{ in_array($dayKey, $selectedDays) ? 'checked' : '' }}>
                            <span class="ml-2 text-xs font-bold text-slate-800">{{ $dayLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Time Window & Duration -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="bulk_start_time" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Jam Buka Sesi Awal <span class="text-rose-500">*</span>
                    </label>
                    <input type="time"
                           id="bulk_start_time"
                           name="start_time"
                           value="{{ old('start_time', '09:00') }}"
                           required
                           class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="bulk_end_time" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Jam Tutup Sesi Akhir <span class="text-rose-500">*</span>
                    </label>
                    <input type="time"
                           id="bulk_end_time"
                           name="end_time"
                           value="{{ old('end_time', '21:00') }}"
                           required
                           class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="duration_minutes" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Durasi per Sesi <span class="text-rose-500">*</span>
                    </label>
                    <select id="duration_minutes"
                            name="duration_minutes"
                            required
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="60" {{ old('duration_minutes') == '60' ? 'selected' : '' }}>60 Menit (1 Jam)</option>
                        <option value="90" {{ old('duration_minutes') == '90' ? 'selected' : '' }}>90 Menit (1.5 Jam)</option>
                        <option value="120" {{ old('duration_minutes', '120') == '120' ? 'selected' : '' }}>120 Menit (2 Jam)</option>
                        <option value="180" {{ old('duration_minutes') == '180' ? 'selected' : '' }}>180 Menit (3 Jam)</option>
                    </select>
                </div>
            </div>

            <!-- Capacity -->
            <div>
                <label for="bulk_capacity" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Kapasitas Kursi per Sesi <span class="text-rose-500">*</span>
                </label>
                <input type="number"
                       id="bulk_capacity"
                       name="capacity"
                       value="{{ old('capacity', 10) }}"
                       required
                       min="1"
                       max="500"
                       class="w-full sm:w-1/3 text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                <p class="text-[11px] text-slate-400 mt-1">Jumlah kursi/meja yang dialokasikan untuk setiap slot waktu.</p>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Mulai Bulk Generate Slot
                </button>
            </div>
        </form>
    </div>

    <!-- Mode 2: Single Manual Form -->
    <div x-show="mode === 'single'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8" style="display: none;">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-xl font-extrabold text-slate-900">Tambah 1 Slot Waktu Manual</h2>
            <p class="text-xs text-slate-500 mt-1">Buat sesi spesifik untuk satu jam dan tanggal tertentu.</p>
        </div>

        <form method="POST" action="{{ route('staff.slots.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="single_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Tanggal Slot <span class="text-rose-500">*</span>
                </label>
                <input type="date"
                       id="single_date"
                       name="date"
                       value="{{ old('date', \Carbon\Carbon::today()->toDateString()) }}"
                       required
                       min="{{ \Carbon\Carbon::today()->toDateString() }}"
                       class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="single_start_time" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Jam Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="time"
                           id="single_start_time"
                           name="start_time"
                           value="{{ old('start_time', '10:00') }}"
                           required
                           class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="single_end_time" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Jam Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="time"
                           id="single_end_time"
                           name="end_time"
                           value="{{ old('end_time', '12:00') }}"
                           required
                           class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="single_capacity" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Kapasitas Kursi <span class="text-rose-500">*</span>
                </label>
                <input type="number"
                       id="single_capacity"
                       name="capacity"
                       value="{{ old('capacity', 8) }}"
                       required
                       min="1"
                       max="500"
                       class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Simpan Slot Manual
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

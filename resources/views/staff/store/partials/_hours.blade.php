@php
    $days = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Minggu',
    ];
    $hours = $store->opening_hours ?? [];
@endphp

<div class="space-y-4">
    <div class="border-b border-slate-100 pb-2">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Jadwal Jam Operasional</h3>
        <p class="text-xs text-slate-500">Atur jam buka & tutup atau tandai libur untuk masing-masing hari</p>
    </div>

    <div class="space-y-3">
        @foreach($days as $key => $label)
            @php
                $dayData = $hours[$key] ?? ['open' => '08:00', 'close' => '22:00', 'is_closed' => false];
                $isClosed = old("opening_hours.{$key}.is_closed", $dayData['is_closed'] ?? false);
                $openTime = old("opening_hours.{$key}.open", $dayData['open'] ?? '08:00');
                $closeTime = old("opening_hours.{$key}.close", $dayData['close'] ?? '22:00');
            @endphp
            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                 x-data="{ closed: {{ $isClosed ? 'true' : 'false' }} }">
                <div class="w-28 flex items-center space-x-2">
                    <span class="font-bold text-sm text-slate-800">{{ $label }}</span>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Toggle Switch Libur/Tutup -->
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox"
                               name="opening_hours[{{ $key }}][is_closed]"
                               value="1"
                               class="sr-only peer"
                               x-model="closed">
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-rose-500"></div>
                        <span class="ml-2 text-xs font-semibold" :class="closed ? 'text-rose-600 font-bold' : 'text-slate-500'" x-text="closed ? 'Tutup / Libur' : 'Buka'"></span>
                    </label>
                </div>

                <!-- Jam Buka - Tutup (Disabled saat Libur) -->
                <div class="flex items-center space-x-2" x-show="!closed" x-transition>
                    <input type="time"
                           name="opening_hours[{{ $key }}][open]"
                           value="{{ $openTime }}"
                           :disabled="closed"
                           class="text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2.5">
                    <span class="text-xs text-slate-400 font-bold">&ndash;</span>
                    <input type="time"
                           name="opening_hours[{{ $key }}][close]"
                           value="{{ $closeTime }}"
                           :disabled="closed"
                           class="text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2.5">
                </div>
                <div x-show="closed" class="text-xs text-slate-400 italic py-1.5" style="display: none;">
                    Tidak menerima reservasi di hari ini
                </div>
            </div>
        @endforeach
    </div>
</div>

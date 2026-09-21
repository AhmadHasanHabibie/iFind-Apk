@extends('layouts.staff')

@section('header_title', 'Manajemen Slot Waktu')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Action Buttons -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Jadwal & Kapasitas Slot Meja</h2>
            <p class="text-xs text-slate-500 mt-1">Atur ketersediaan kursi per sesi waktu untuk booking pelanggan</p>
        </div>

        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <a href="{{ route('staff.slots.create') }}"
               class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah / Generate Slot</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('staff.slots.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-slate-500">Rentang Tanggal:</span>
                <input type="date"
                       name="start_date"
                       value="{{ $startDate }}"
                       class="text-xs rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2.5">
                <span class="text-xs text-slate-400 font-bold">&ndash;</span>
                <input type="date"
                       name="end_date"
                       value="{{ $endDate }}"
                       class="text-xs rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2.5">
            </div>

            <button type="submit" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition">
                Terapkan Filter
            </button>

            @if(request()->has('start_date') || request()->has('end_date'))
                <a href="{{ route('staff.slots.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 px-2 py-1">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Slots Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Tanggal</th>
                        <th class="py-3.5 px-6">Jam Sesi</th>
                        <th class="py-3.5 px-6 text-center">Kapasitas</th>
                        <th class="py-3.5 px-6 text-center">Kursi Terisi</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($slots as $slot)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($slot->date)->isoFormat('dddd, D MMMM Y') }}</span>
                                <div class="text-[11px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($slot->date)->isToday() ? 'Hari ini' : (\Carbon\Carbon::parse($slot->date)->isTomorrow() ? 'Besok' : '') }}
                                </div>
                            </td>
                            <td class="py-4 px-6 font-mono text-xs font-bold text-blue-700">
                                {{ substr($slot->start_time, 0, 5) }} &ndash; {{ substr($slot->end_time, 0, 5) }}
                            </td>
                            <td class="py-4 px-6 text-center text-xs font-semibold text-slate-700">
                                {{ $slot->capacity }} kursi
                            </td>
                            <td class="py-4 px-6 text-center text-xs font-bold text-slate-800">
                                {{ $slot->booked_seats }} kursi
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($slot->status === 'available')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Tersedia
                                    </span>
                                @elseif($slot->status === 'full')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Penuh
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Tutup
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center space-x-1.5">
                                    @if($slot->status !== 'closed')
                                        <form method="POST" action="{{ route('staff.slots.close', $slot) }}" data-confirm="Tutup slot waktu sesi ini sekarang?" data-confirm-title="Tutup Slot Sesi" data-confirm-btn="Ya, Tutup">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Tutup Manual"
                                                    class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('staff.slots.edit', $slot) }}"
                                       title="Edit Kapasitas"
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form method="POST" action="{{ route('staff.slots.destroy', $slot) }}" data-confirm="Apakah Anda yakin ingin menghapus slot waktu ini secara permanen?" data-confirm-title="Hapus Slot Waktu" data-confirm-btn="Ya, Hapus" data-confirm-danger="true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Slot"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Tidak ada slot waktu pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($slots->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $slots->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

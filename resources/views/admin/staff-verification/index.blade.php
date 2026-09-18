@extends('layouts.admin')

@section('header_title', 'Verifikasi Pendaftaran Staf Toko')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Filter Tabs -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Daftar Pengajuan Akun Staf</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola dan verifikasi identitas calon staf mitra sebelum dapat mengakses dashboard toko</p>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center space-x-1.5 p-1 bg-slate-100 rounded-xl">
            <a href="{{ route('admin.staff-verification.index', ['status' => 'pending']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 {{ $status === 'pending' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Pending</span>
                @if($counts['pending'] > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold">{{ $counts['pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.staff-verification.index', ['status' => 'approved']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 {{ $status === 'approved' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Disetujui</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800">{{ $counts['approved'] }}</span>
            </a>
            <a href="{{ route('admin.staff-verification.index', ['status' => 'rejected']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 {{ $status === 'rejected' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Ditolak</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-100 text-rose-800">{{ $counts['rejected'] }}</span>
            </a>
            <a href="{{ route('admin.staff-verification.index', ['status' => 'all']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'all' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Semua ({{ $counts['all'] }})</span>
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Staf / Pengguna</th>
                        <th class="py-3.5 px-6">Kontak</th>
                        <th class="py-3.5 px-6">Tanggal Daftar</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($staffList as $staff)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-700 font-bold flex items-center justify-center text-sm">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $staff->name }}</p>
                                        <p class="text-xs text-slate-400">ID: #{{ $staff->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-slate-700">{{ $staff->email }}</p>
                                <p class="text-xs text-slate-500">{{ $staff->phone ?: 'Tidak ada nomor telepon' }}</p>
                            </td>
                            <td class="py-4 px-6 text-slate-600 text-xs">
                                <div>{{ $staff->created_at->format('d M Y') }}</div>
                                <div class="text-slate-400">{{ $staff->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($staff->verification_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Pending
                                    </span>
                                @elseif($staff->verification_status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.staff-verification.show', $staff) }}"
                                   class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold rounded-lg text-teal-700 bg-teal-50 hover:bg-teal-100 transition border border-teal-200">
                                    <span>Lihat Detail</span>
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="font-medium">Tidak ada data staf dengan status {{ $status }}.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $staffList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

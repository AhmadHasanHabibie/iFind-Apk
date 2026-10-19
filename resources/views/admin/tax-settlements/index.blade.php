@extends('layouts.admin')

@section('header_title', 'Verifikasi Setoran Pajak Toko')

@section('content')
<div class="space-y-6" x-data="{
    proofModalOpen: false,
    proofModalImage: '',
    approveModalOpen: false,
    approveAction: '',
    approveCode: '',
    approveAmount: '',
    rejectModalOpen: false,
    rejectAction: '',
    rejectCode: ''
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-xs">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Verifikasi Setoran Pajak Toko</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Tinjau dan konfirmasi bukti transfer penyetoran pajak 5% dari pemilik toko ke rekening/QRIS admin.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto text-xs font-bold">
        <a href="{{ route('admin.commissions.index') }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80">
            <i class="fa-solid fa-list-check text-slate-500"></i>
            <span>Rekap Booking & Transaksi (5%)</span>
        </a>
        <a href="{{ route('admin.tax-settlements.index') }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2 bg-teal-600 text-white shadow-xs">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Verifikasi Setoran Toko</span>
            @if($pendingCount > 0)
                <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-400 text-slate-950 font-black animate-pulse">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.tax-settlements.settings') }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80">
            <i class="fa-solid fa-qrcode text-slate-500"></i>
            <span>Pengaturan QRIS & Rekening Admin</span>
        </a>
    </div>

    <!-- 4 Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Setoran Disetujui -->
        <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent p-5 rounded-3xl border border-emerald-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Total Setoran Diterima</p>
                <h3 class="text-2xl font-black text-emerald-950 mt-1.5">Rp {{ number_format($totalApprovedAmount, 0, ',', '.') }}</h3>
                <p class="text-xs text-emerald-700 mt-0.5">{{ $approvedCount }} transaksi telah disetujui</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
        </div>

        <!-- 2. Setoran Menunggu Verifikasi -->
        <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5 rounded-3xl border border-amber-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Menunggu Verifikasi</p>
                <h3 class="text-2xl font-black text-amber-950 mt-1.5">Rp {{ number_format($totalPendingAmount, 0, ',', '.') }}</h3>
                <p class="text-xs text-amber-700 mt-0.5">{{ $pendingCount }} bukti transfer masuk</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
            </div>
        </div>

        <!-- 3. Toko dengan Pengajuan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Mitra Toko</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1.5">{{ $stores->count() }} Mitra</h3>
                <p class="text-xs text-slate-500 mt-0.5">Terdaftar di platform</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i class="fa-solid fa-store text-xl"></i>
            </div>
        </div>

        <!-- 4. Total Pengajuan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengajuan Setor</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1.5">{{ $settlements->total() }} Data</h3>
                <p class="text-xs text-slate-500 mt-0.5">Riwayat keseluruhan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="fa-solid fa-receipt text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <form method="GET" action="{{ route('admin.tax-settlements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Kode Setoran / Toko / Staf</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cth: TAX-2026, Kopi Titik Temu, Ahmad..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>

            <!-- Filter Toko -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Filter Toko</label>
                <select name="store_id" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">Semua Toko Mitra</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} ({{ $s->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Verifikasi</label>
                    <select name="status" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5 h-[38px]">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Table of Submissions -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Daftar Pengajuan Penyetoran Pajak Toko</h3>
                <p class="text-xs text-slate-500 mt-0.5">Periksa bukti struk/QRIS sebelum mengonfirmasi status pembayaran.</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-xl">
                Menampilkan {{ $settlements->count() }} dari {{ $settlements->total() }} data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-400 bg-slate-50/60">
                        <th class="py-3 px-4">Kode Setoran</th>
                        <th class="py-3 px-4">Toko / Merchant</th>
                        <th class="py-3 px-4">Tanggal Pengajuan</th>
                        <th class="py-3 px-4">Nominal Disetor</th>
                        <th class="py-3 px-4 text-center">Bukti Transfer</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($settlements as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $item->settlement_code }}
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-900">{{ $item->store->name ?? '-' }}</p>
                                <p class="text-[11px] text-slate-400">Pengirim: {{ $item->user->name ?? 'Staf' }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900 text-sm">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->proof_url)
                                    <button type="button"
                                            @click="proofModalImage = '{{ $item->proof_url }}'; proofModalOpen = true"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs border border-teal-200 transition">
                                        <i class="fa-solid fa-eye"></i>
                                        <span>Lihat Bukti</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-check-circle text-[10px]"></i>
                                        <span>Disetujui</span>
                                    </span>
                                    @if($item->verifiedBy)
                                        <p class="text-[10px] text-slate-400 mt-1">Oleh: {{ $item->verifiedBy->name }}</p>
                                    @endif
                                @elseif($item->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                        <span>Ditolak</span>
                                    </span>
                                    @if($item->admin_notes)
                                        <p class="text-[10px] text-rose-600 font-semibold mt-1 max-w-xs">{{ $item->admin_notes }}</p>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Menunggu</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                @if($item->status === 'pending')
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                @click="approveAction = '{{ route('admin.tax-settlements.approve', $item) }}'; approveCode = '{{ $item->settlement_code }}'; approveAmount = 'Rp {{ number_format($item->amount, 0, ',', '.') }}'; approveModalOpen = true"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i>
                                            <span>Setujui</span>
                                        </button>
                                        <button type="button"
                                                @click="rejectAction = '{{ route('admin.tax-settlements.reject', $item) }}'; rejectCode = '{{ $item->settlement_code }}'; rejectModalOpen = true"
                                                class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>Tolak</span>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-semibold">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <i class="fa-solid fa-inbox text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ditemukan pengajuan setoran pajak.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($settlements->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $settlements->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Pratinjau Bukti Transfer -->
    <div x-show="proofModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
         @click.self="proofModalOpen = false"
         @keydown.escape.window="proofModalOpen = false">
        <div class="relative bg-white rounded-3xl p-5 max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="text-base font-extrabold text-slate-900">Bukti Transfer Setoran Pajak</h4>
                <button type="button" @click="proofModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="flex justify-center bg-slate-50 p-2 rounded-2xl border">
                <img :src="proofModalImage" alt="Bukti Transfer" class="max-h-[70vh] object-contain rounded-xl">
            </div>
            <div class="flex justify-end">
                <button type="button" @click="proofModalOpen = false" class="px-5 py-2 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Setujui (Approve) -->
    <div x-show="approveModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
         @click.self="approveModalOpen = false"
         @keydown.escape.window="approveModalOpen = false">
        <div class="relative bg-white rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="text-center">
                <h4 class="text-lg font-extrabold text-slate-900">Konfirmasi Setujui Setoran</h4>
                <p class="text-xs text-slate-500 mt-1">
                    Anda akan menyetujui setoran pajak <strong class="text-slate-800 font-mono" x-text="approveCode"></strong> sejumlah <strong class="text-emerald-700" x-text="approveAmount"></strong>.
                </p>
            </div>
            <form :action="approveAction" method="POST" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan Admin (Opsional)</label>
                    <textarea name="admin_notes" rows="2" placeholder="Cth: Dana telah masuk rekening BCA..." class="w-full text-xs rounded-xl border-slate-200 focus:ring-teal-500 focus:border-teal-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="approveModalOpen = false" class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Tolak (Reject) -->
    <div x-show="rejectModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
         @click.self="rejectModalOpen = false"
         @keydown.escape.window="rejectModalOpen = false">
        <div class="relative bg-white rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mx-auto">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="text-center">
                <h4 class="text-lg font-extrabold text-slate-900">Tolak Bukti Setoran</h4>
                <p class="text-xs text-slate-500 mt-1">
                    Masukkan alasan penolakan setoran <strong class="text-slate-800 font-mono" x-text="rejectCode"></strong> agar staf toko dapat memperbaiki bukti transfer.
                </p>
            </div>
            <form :action="rejectAction" method="POST" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="rejection_reason" required rows="3" placeholder="Cth: Bukti transfer buram / nominal tidak sesuai / dana belum masuk mutasi..." class="w-full text-xs rounded-xl border-slate-200 focus:ring-rose-500 focus:border-rose-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="rejectModalOpen = false" class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition">
                        Tolak Bukti
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-xs">
                    <i class="fa-solid fa-store"></i>
                </span>
                Manajemen Toko & Tempat
            </h1>
            <p class="text-sm text-slate-500 mt-1">Daftar semua kafe/toko terdaftar di platform iFind beserta status moderasi dan status aktif.</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('admin.stores.index', array_merge(request()->except('status', 'page'), ['status' => ''])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !$status ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                Semua ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.stores.index', array_merge(request()->except('status', 'page'), ['status' => 'approved'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'approved' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                Approved ({{ $counts['approved'] }})
            </a>
            <a href="{{ route('admin.stores.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'pending' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                Pending ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.stores.index', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'rejected' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                Ditolak ({{ $counts['rejected'] }})
            </a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.stores.index') }}" class="flex items-center gap-2">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative flex-1 sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama toko/kota/owner..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-hidden">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Stores Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        @if($stores->isEmpty())
            <div class="p-12 text-center">
                <p class="text-sm text-slate-500">Tidak ada toko yang sesuai dengan filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-6">Toko</th>
                            <th class="py-4 px-6">Pemilik (Staf)</th>
                            <th class="py-4 px-6">Kategori & Lokasi</th>
                            <th class="py-4 px-6">Total Booking</th>
                            <th class="py-4 px-6">Status Toko</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($stores as $store)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        @if($store->primary_photo_url)
                                            <img src="{{ $store->primary_photo_url }}" alt="{{ $store->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200 shadow-xs">
                                        @else
                                            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($store->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.stores.show', $store) }}" class="font-bold text-slate-900 hover:text-teal-600 transition">
                                                {{ $store->name }}
                                            </a>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                                                <span class="text-xs font-bold text-slate-700">{{ $store->average_rating ?? '0.0' }}</span>
                                                <span class="text-xs text-slate-400">({{ $store->reviews_count }} review)</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-semibold text-slate-800 text-xs">{{ $store->user->name ?? '-' }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $store->user->email ?? '-' }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                        {{ $store->category->name ?? 'Kategori' }}
                                    </span>
                                    <p class="text-xs text-slate-500 mt-1"><i class="fa-solid fa-location-dot mr-1 text-slate-400"></i> {{ $store->city }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-mono font-bold text-slate-800">{{ $store->bookings_count }}</span>
                                    <span class="text-xs text-slate-400">order</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1">
                                        @if($store->status === 'approved')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                Approved
                                            </span>
                                        @elseif($store->status === 'pending')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                                Pending Review
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                                Rejected
                                            </span>
                                        @endif

                                        <div>
                                            @if($store->is_active)
                                                <span class="inline-flex items-center text-[10px] font-bold text-emerald-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span> Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-[10px] font-bold text-rose-500">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1"></span> Suspended / Nonaktif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.stores.show', $store) }}"
                                           class="p-2 rounded-xl text-slate-500 hover:text-teal-600 hover:bg-teal-50 transition" title="Lihat Detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.stores.toggle-active', $store) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="p-2 rounded-xl {{ $store->is_active ? 'text-rose-500 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition"
                                                    title="{{ $store->is_active ? 'Nonaktifkan / Suspend Toko' : 'Aktifkan Toko' }}">
                                                <i class="fa-solid {{ $store->is_active ? 'fa-ban' : 'fa-circle-check' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $stores->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
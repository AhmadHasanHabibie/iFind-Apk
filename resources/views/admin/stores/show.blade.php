@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.stores.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 transition shadow-xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-md bg-teal-50 text-teal-700 border border-teal-100 uppercase">
                        {{ $store->category->name ?? 'Kategori' }}
                    </span>
                    @if($store->is_active)
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-600 border border-emerald-200">
                            Aktif
                        </span>
                    @else
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-md bg-rose-50 text-rose-600 border border-rose-200">
                            Suspended / Nonaktif
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1">{{ $store->name }}</h1>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            @if($store->status === 'pending')
                <form method="POST" action="{{ route('admin.stores.approve', $store) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition">
                        <i class="fa-solid fa-check mr-1.5"></i> Setujui Toko
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.stores.toggle-active', $store) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 rounded-xl {{ $store->is_active ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-teal-600 hover:bg-teal-700 text-white' }} font-bold text-xs shadow-md transition">
                    <i class="fa-solid {{ $store->is_active ? 'fa-ban' : 'fa-circle-check' }} mr-1.5"></i>
                    {{ $store->is_active ? 'Suspend Toko' : 'Aktifkan Toko' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Store Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Details -->
        <div class="md:col-span-2 space-y-6">
            <!-- Gallery / Photos -->
            @if($store->photos->isNotEmpty())
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Foto Toko</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($store->photos as $photo)
                            <div class="relative group aspect-4/3 rounded-2xl overflow-hidden border border-slate-100">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto Toko" class="w-full h-full object-cover">
                                @if($photo->is_primary)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 text-[10px] font-black bg-blue-600 text-white rounded-md shadow-xs">Utama</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Description & Facilities -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi & Fasilitas</h3>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $store->description ?: 'Tidak ada deskripsi yang dicantumkan.' }}</p>

                <div class="pt-3 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-700 mb-2">Fasilitas Tersedia:</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse($store->facilities as $fac)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700">
                                <i class="{{ $fac->icon }} mr-1.5 text-teal-600"></i> {{ $fac->name }}
                            </span>
                        @empty
                            <p class="text-xs text-slate-400">Belum ada fasilitas yang dipilih.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Owner & Meta -->
        <div class="space-y-6">
            <!-- Owner Details -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Informasi Pemilik</h3>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-base">
                        {{ strtoupper(substr($store->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $store->user->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500">{{ $store->user->email ?? '-' }}</p>
                        <p class="text-xs text-slate-500">{{ $store->user->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Pricing & Payment -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengaturan Reservasi</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Harga per Pax:</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($store->price_per_pax ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Uang Muka (DP):</span>
                        <span class="font-bold text-slate-800">{{ $store->dp_percentage ?? 100 }}%</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Batas Waktu Bayar:</span>
                        <span class="font-bold text-slate-800">{{ $store->payment_timeout_minutes ?? 60 }} Menit</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Bank:</span>
                        <span class="font-bold text-slate-800">{{ $store->bank_name ?: '-' }} ({{ $store->bank_account_number ?: '-' }})</span>
                    </div>
                </div>
            </div>

            <!-- Address & Contact -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-2 text-xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Alamat Toko</h3>
                <p class="text-slate-700 leading-relaxed">{{ $store->address }}, {{ $store->city }}</p>
                <p class="text-slate-500">No. Telepon Toko: <span class="font-bold text-slate-800">{{ $store->phone ?: '-' }}</span></p>
            </div>
        </div>
    </div>
</div>
@endsection
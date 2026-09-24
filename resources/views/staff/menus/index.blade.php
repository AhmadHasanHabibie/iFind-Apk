@extends('layouts.staff')

@section('title', 'Katalog Menu - ' . $store->name)

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Katalog Menu & Harga</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola daftar makanan, minuman, dan snack untuk toko <span class="font-semibold text-slate-700">{{ $store->name }}</span>.</p>
        </div>
        <a href="{{ route('staff.menus.create') }}"
           class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Menu Baru</span>
        </a>
    </div>

    <!-- Filter Kategori Tabs -->
    @php
        $currentCat = request('category');
        $categories = [
            '' => 'Semua Kategori',
            'makanan' => 'Makanan',
            'minuman' => 'Minuman',
            'snack' => 'Snack / Cemilan',
            'paket_hemat' => 'Paket Hemat Pelajar',
            'lainnya' => 'Lainnya',
        ];
    @endphp
    <div class="flex items-center space-x-2 overflow-x-auto pb-2">
        @foreach($categories as $key => $label)
            <a href="{{ route('staff.menus.index', $key ? ['category' => $key] : []) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $currentCat === $key || (!$currentCat && $key === '') ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Grid Menu List -->
    @if($menus->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($menus as $menu)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-all group">
                    <div>
                        <!-- Foto Menu -->
                        <div class="relative h-44 bg-slate-100 overflow-hidden">
                            @if($menu->photo)
                                <img src="{{ asset('storage/' . $menu->photo) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-100">
                                    <svg class="w-10 h-10 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-xs font-medium">Tanpa Foto</span>
                                </div>
                            @endif

                            <!-- Badge Kategori -->
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg shadow-sm backdrop-blur-md {{ $menu->category === 'paket_hemat' ? 'bg-emerald-500/90 text-white' : 'bg-slate-900/80 text-white' }}">
                                    {{ str_replace('_', ' ', $menu->category) }}
                                </span>
                            </div>

                            @if($menu->is_recommended)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-amber-500 text-white shadow-sm flex items-center space-x-1">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span>Favorit</span>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Info Menu -->
                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $menu->name }}</h3>
                            </div>
                            <p class="text-base font-black text-blue-600">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            @if($menu->description)
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $menu->description }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="p-4 pt-0 border-t border-slate-100 flex items-center justify-between gap-2 mt-2">
                        <!-- Toggle Status Ketersediaan -->
                        <form action="{{ route('staff.menus.toggle', $menu) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $menu->is_available ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                {{ $menu->is_available ? '🟢 Tersedia' : '🔴 Habis' }}
                            </button>
                        </form>

                        <div class="flex items-center space-x-1.5">
                            <a href="{{ route('staff.menus.edit', $menu) }}"
                               class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                               title="Edit Menu">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="{{ route('staff.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Hapus menu ini dari daftar?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Menu">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $menus->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Menu Terdaftar</h3>
                <p class="text-xs text-slate-500 mt-1">Tambahkan menu makanan & minuman agar siswa/pelanggan dapat melihat katalog harga toko Anda.</p>
            </div>
            <a href="{{ route('staff.menus.create') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                <span>+ Tambah Menu Pertama</span>
            </a>
        </div>
    @endif
</div>
@endsection

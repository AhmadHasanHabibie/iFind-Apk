@extends('layouts.user')

@section('content')
<div class="space-y-8" x-data="userFavorites()">
    <!-- Header Section -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 border border-rose-200 text-rose-600">
                <i class="fa-solid fa-heart"></i>
                <span>Wishlist Spot Siswa</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Spot Nongkrong Favorit Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500">Daftar kafe dan tempat nongkrong hemat yang kamu simpan untuk dikunjungi.</p>
        </div>

        <a href="{{ route('user.dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition">
            <i class="fa-solid fa-compass"></i>
            <span>Jelajahi Spot Lainnya</span>
        </a>
    </div>

    <!-- Favorite Stores Grid -->
    @if($favorites->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $store)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-lg hover:border-slate-300 transition-all duration-300 group">
                    <div>
                        <!-- Cover Photo -->
                        <div class="relative h-48 bg-slate-100 overflow-hidden">
                            @if($store->primary_photo_url)
                                <img src="{{ $store->primary_photo_url }}" alt="{{ $store->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-100">
                                    <i class="fa-solid fa-store text-3xl mb-1 opacity-40"></i>
                                    <span class="text-xs font-bold">i-Find Spot</span>
                                </div>
                            @endif

                            <!-- Kategori Tag -->
                            @if($store->category)
                                <div class="absolute top-3.5 left-3.5">
                                    <span class="px-3 py-1 rounded-xl text-[10px] font-extrabold uppercase tracking-wider bg-slate-900/80 text-white backdrop-blur-md shadow-sm">
                                        {{ $store->category->name }}
                                    </span>
                                </div>
                            @endif

                            <!-- Remove/Toggle Bookmark Button -->
                            <div class="absolute top-3.5 right-3.5">
                                <button type="button"
                                        @click="toggleFavorite({{ $store->id }}, $event)"
                                        class="w-9 h-9 rounded-xl bg-white/90 backdrop-blur-md text-rose-500 hover:bg-white shadow-md flex items-center justify-center transition hover:scale-110 active:scale-95"
                                        title="Hapus dari Favorit">
                                    <i class="fa-solid fa-heart text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Content Info -->
                        <div class="p-5 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1 leading-snug">
                                        {{ $store->name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-location-dot text-slate-400 text-[10px]"></i>
                                        <span class="truncate">{{ $store->city }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 font-extrabold text-xs shrink-0">
                                    <i class="fa-solid fa-star text-[10px]"></i>
                                    <span>{{ number_format($store->average_rating, 1) }}</span>
                                </div>
                            </div>

                            <!-- Fasilitas Icons -->
                            @if($store->facilities->count() > 0)
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach($store->facilities->take(3) as $facility)
                                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-semibold flex items-center gap-1">
                                            <span>{{ $facility->name }}</span>
                                        </span>
                                    @endforeach
                                    @if($store->facilities->count() > 3)
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-500 text-[10px] font-bold">
                                            +{{ $store->facilities->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Price per pax info -->
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-400 font-medium">Estimasi per orang</span>
                                <span class="font-black text-emerald-600 text-sm">
                                    @if($store->price_per_pax > 0)
                                        Rp {{ number_format($store->price_per_pax, 0, ',', '.') }}
                                    @else
                                        Mulai Rp 10.000
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer CTA Buttons -->
                    <div class="p-5 pt-0 grid grid-cols-2 gap-2">
                        @if($store->latitude && $store->longitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $store->latitude }},{{ $store->longitude }}"
                               target="_blank"
                               class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-diamond-turn-right text-blue-600"></i>
                                <span>Rute Maps</span>
                            </a>
                        @endif

                        <a href="{{ route('user.stores.show', $store->slug) }}"
                           class="{{ (!$store->latitude || !$store->longitude) ? 'col-span-2' : '' }} py-2.5 px-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-1.5">
                            <span>Detail & Booking</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-12 text-center max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center mx-auto text-2xl shadow-inner">
                <i class="fa-regular fa-heart"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900">Belum Ada Spot Favorit</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Kamu belum menandai spot nongkrong manapun. Klik ikon hati pada kartu spot untuk menyimpannya ke daftar ini.
                </p>
            </div>
            <a href="{{ route('user.dashboard') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-lg shadow-blue-600/25 transition">
                <i class="fa-solid fa-compass"></i>
                <span>Cari Spot Nongkrong Sekarang</span>
            </a>
        </div>
    @endif
</div>

<script>
    function userFavorites() {
        return {
            async toggleFavorite(storeId, event) {
                const button = event.currentTarget;
                try {
                    const response = await fetch(`/user/favorites/${storeId}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        window.location.reload();
                    }
                } catch (e) {
                    console.error('Error toggling favorite:', e);
                }
            }
        }
    }
</script>
@endsection

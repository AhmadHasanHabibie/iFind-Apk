@php
    $primaryPhoto = $store->photos->firstWhere('is_primary', true) ?? $store->photos->first();
    $photoUrl = $primaryPhoto ? asset('storage/' . $primaryPhoto->photo_path) : null;
    $hasTodayAvailable = $store->slots->where('date', today())->where('status', 'available')->count() > 0;
    $isFav = $store->isFavoritedBy(auth()->user());
@endphp

<div class="group flex flex-col bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-blue-300 hover:-translate-y-1.5 active:translate-y-0 transition-all duration-300 overflow-hidden"
     x-data="{ isFavorited: {{ $isFav ? 'true' : 'false' }}, favoriting: false }">
    
    <!-- Image Thumbnail Container -->
    <div class="relative w-full h-52 bg-slate-100 overflow-hidden flex items-center justify-center">
        @if($photoUrl)
            <img src="{{ $photoUrl }}"
                 alt="{{ $store->name }}"
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div style="display: none;" class="w-full h-full bg-gradient-to-tr from-blue-600/10 to-indigo-600/20 items-center justify-center text-slate-400">
                <i class="fa-solid fa-store text-4xl text-slate-300"></i>
            </div>
        @else
            <div class="w-full h-full bg-gradient-to-tr from-blue-600/10 to-indigo-600/20 flex items-center justify-center text-slate-400">
                <i class="fa-solid fa-store text-4xl text-slate-300"></i>
            </div>
        @endif

        <!-- Gradient Vignette Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>

        <!-- Category Tag -->
        <span class="absolute top-3.5 left-3.5 px-3 py-1.5 rounded-xl bg-white/95 backdrop-blur-md text-slate-800 font-extrabold text-[11px] shadow-sm flex items-center space-x-1.5 border border-white/40">
            <i class="{{ $store->category->icon ?? 'fa-solid fa-tag' }} text-blue-600 text-xs"></i>
            <span>{{ $store->category->name ?? 'Tempat' }}</span>
        </span>

        <!-- Quick Bookmark / Wishlist Button -->
        <button type="button"
                @click.prevent="
                    if (favoriting) return;
                    favoriting = true;
                    fetch('/user/favorites/{{ $store->id }}/toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) isFavorited = data.is_favorited;
                    })
                    .catch(e => console.error(e))
                    .finally(() => favoriting = false);
                "
                class="absolute top-3.5 right-3.5 w-9 h-9 rounded-xl bg-white/95 backdrop-blur-md shadow-md flex items-center justify-center transition hover:scale-110 active:scale-95 z-10"
                :class="isFavorited ? 'text-rose-500' : 'text-slate-400 hover:text-rose-500'"
                title="Simpan ke Favorit">
            <i :class="isFavorited ? 'fa-solid text-rose-500' : 'fa-regular'" class="fa-heart text-sm"></i>
        </button>

        <!-- Available Today Badge -->
        @if($hasTodayAvailable)
            <span class="absolute bottom-3.5 left-3.5 px-2.5 py-1 rounded-xl bg-emerald-500/95 backdrop-blur-md text-white font-extrabold text-[10px] shadow-sm flex items-center space-x-1.5 border border-emerald-400/40">
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                <span>Slot Hari Ini Ada</span>
            </span>
        @endif

        <!-- Nearest Top Spot Badge -->
        @if(isset($loop) && $loop->first && isset($store->distance_km) && $store->distance_km !== null)
            <span class="absolute bottom-3.5 right-3.5 px-2.5 py-1 rounded-xl bg-blue-600/95 backdrop-blur-md text-white font-extrabold text-[10px] shadow-sm flex items-center space-x-1.5 border border-blue-400/40">
                <i class="fa-solid fa-location-crosshairs text-[10px]"></i>
                <span>#1 Terdekat</span>
            </span>
        @endif
    </div>

    <!-- Store Info Body -->
    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
        <div>
            <div class="flex items-start justify-between gap-2">
                <a href="{{ route('user.stores.show', $store->slug) }}" class="font-black text-slate-900 group-hover:text-blue-600 transition-colors duration-200 line-clamp-1 text-base leading-snug">
                    {{ $store->name }}
                </a>
                <!-- Rating -->
                <div class="flex items-center space-x-1 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200/80 shrink-0">
                    <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                    <span class="text-xs font-black text-amber-900">{{ number_format($store->average_rating, 1) }}</span>
                    <span class="text-[10px] text-amber-700 font-semibold">({{ $store->reviews_count ?? $store->reviews->count() }})</span>
                </div>
            </div>

            <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed font-normal">
                {{ $store->description }}
            </p>
        </div>

        <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <!-- City Location -->
            <div class="flex items-center space-x-1.5 text-slate-600 font-medium">
                <i class="fa-solid fa-location-dot text-slate-400 text-xs"></i>
                <span class="truncate max-w-[120px]">{{ $store->city }}</span>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Distance (if calculated) -->
                @if(isset($store->distance_km) && $store->distance_km !== null)
                    <div class="flex items-center space-x-1 text-blue-700 font-bold bg-blue-50 px-2.5 py-1 rounded-lg text-[11px] border border-blue-100">
                        <i class="fa-solid fa-route text-[10px] text-blue-500"></i>
                        <span>
                            @if($store->distance_km < 1)
                                {{ round($store->distance_km * 1000) }} m
                            @else
                                {{ number_format($store->distance_km, 1) }} km
                            @endif
                        </span>
                    </div>
                @endif

                <!-- Direction Map Icon Link -->
                @if($store->latitude && $store->longitude)
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $store->latitude }},{{ $store->longitude }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       @click.stop
                       title="Buka Navigasi Rute Maps"
                       class="p-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-500 transition">
                        <i class="fa-solid fa-diamond-turn-right text-xs"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="pt-1">
            <a href="{{ route('user.stores.show', $store->slug) }}"
               class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-blue-600 text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-xs">
                <span>Cek Menu & Booking</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>
</div>

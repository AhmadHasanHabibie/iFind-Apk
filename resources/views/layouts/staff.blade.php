<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'iFind') }} - Mitra Staf Toko</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
             @click="sidebarOpen = false"
             style="display: none;"></div>

        <!-- Sidebar -->
        @php
            $user = Auth::user();
            $hasStore = $user && $user->store !== null;
            $store = $hasStore ? $user->store : null;

            $pendingBookingsCount = $hasStore
                ? \App\Models\Booking::where('store_id', $store->id)->where('status', 'pending')->count()
                : 0;

            $unreadChatCount = 0;
            if ($hasStore) {
                $storeConvIds = \App\Models\Conversation::where('store_id', $store->id)->pluck('id');
                $adminConvIds = \App\Models\Conversation::whereNull('store_id')
                    ->where(function($q) use ($user) {
                        $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
                    })->pluck('id');
                $allConvIds = $storeConvIds->merge($adminConvIds)->unique();

                $unreadChatCount = \App\Models\ChatMessage::whereIn('conversation_id', $allConvIds)
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
            }
        @endphp
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-100 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
            <!-- Brand Logo -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-black text-lg text-white shadow-md shadow-blue-500/30">i</div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">i-Find</span>
                        <span class="ml-1.5 px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase bg-blue-500/20 text-blue-300 rounded-md border border-blue-500/40">STAFF</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Store Info in Sidebar -->
            <div class="px-5 py-3 border-b border-slate-800/80 bg-slate-950/40">
                <p class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Toko Anda</p>
                @if($hasStore)
                    <p class="text-sm font-bold text-slate-100 truncate mt-0.5">{{ $store->name }}</p>
                    <p class="text-xs text-blue-400 truncate">{{ $store->city }}</p>
                @else
                    <p class="text-xs text-amber-400 font-semibold mt-0.5 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                        Belum Dikonfigurasi
                    </p>
                @endif
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
                <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Operasional Toko</div>

                <!-- Dashboard -->
                <a href="{{ route('staff.dashboard') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('staff.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                        <span>Dashboard</span>
                    </div>
                </a>

                <!-- Profil Toko (Selalu Aktif) -->
                <a href="{{ $hasStore ? route('staff.store.edit') : route('staff.store.create') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('staff.store.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Profil Toko</span>
                    </div>
                    @if(! $hasStore)
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-slate-950 animate-pulse">Wajib</span>
                    @endif
                </a>

                <!-- Slot Waktu (Disabled jika belum punya toko) -->
                @if($hasStore)
                    <a href="{{ route('staff.slots.index') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('staff.slots.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Slot Waktu</span>
                        </div>
                    </a>
                @else
                    <div title="Lengkapi profil toko dulu" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-slate-800/30 cursor-not-allowed select-none">
                        <div class="flex items-center space-x-3 opacity-60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Slot Waktu</span>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400">Terkunci</span>
                    </div>
                @endif

                <!-- Booking Order (Disabled jika belum punya toko) -->
                @if($hasStore)
                    <a href="{{ route('staff.bookings.index') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('staff.bookings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            <span>Booking Order</span>
                        </div>
                        @if($pendingBookingsCount > 0)
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-500 text-slate-900 shadow-sm animate-pulse">{{ $pendingBookingsCount }}</span>
                        @endif
                    </a>

                    <!-- Scan QR Check-in -->
                    <a href="{{ route('staff.scan.index') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('staff.scan.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span>Scan QR Check-in</span>
                        </div>
                    </a>
                @else
                    <div title="Lengkapi profil toko dulu" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-slate-800/30 cursor-not-allowed select-none">
                        <div class="flex items-center space-x-3 opacity-60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            <span>Booking Order</span>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400">Terkunci</span>
                    </div>
                @endif

                <!-- Chat (Disabled jika belum punya toko) -->
                @if($hasStore)
                    <a href="{{ route('staff.chat.index') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('staff.chat.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>Chat Pelanggan</span>
                        </div>
                        @if($unreadChatCount > 0)
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-rose-500 text-white shadow-sm">{{ $unreadChatCount }}</span>
                        @endif
                    </a>
                @else
                    <div title="Lengkapi profil toko dulu" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-slate-800/30 cursor-not-allowed select-none">
                        <div class="flex items-center space-x-3 opacity-60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>Chat Pelanggan</span>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400">Terkunci</span>
                    </div>
                @endif

                <!-- Tiket Bantuan ke Admin -->
                <a href="{{ route('staff.tickets.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('staff.tickets.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Tiket Bantuan</span>
                    </div>
                </a>
            </div>

            <!-- User Footer -->
            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 truncate">
                        <div class="w-9 h-9 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-400 flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-sm font-medium text-white truncate">{{ $user->name ?? 'Staf Toko' }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $user->email ?? '' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-600 hover:text-slate-900 rounded-lg lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-lg font-bold text-slate-800 truncate">
                        @yield('header_title', 'Dashboard Staf Toko')
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex items-center text-xs text-slate-500 space-x-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="font-medium">Portal Mitra Toko</span>
                    </div>

                    <div class="h-6 w-px bg-slate-200"></div>

                    <!-- Staff Badge & Profile -->
                    <div class="flex items-center space-x-3">
                        <div class="text-right hidden md:block">
                            <p class="text-xs font-semibold text-slate-800">{{ $user->name ?? 'Staf Mitra' }}</p>
                            <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wider uppercase bg-blue-100 text-blue-800 rounded">
                                STAFF
                            </span>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-slate-900 text-blue-400 font-bold flex items-center justify-center text-sm ring-2 ring-blue-500/20">
                            {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Alerts -->
            <div class="px-4 sm:px-6 lg:px-8 pt-6">
                @if (session('success'))
                    <div class="mb-4 flex items-center p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200" role="alert">
                        <svg class="w-5 h-5 inline mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <div><span class="font-bold">Berhasil!</span> {{ session('success') }}</div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 flex items-center p-4 text-sm text-amber-800 rounded-xl bg-amber-50 border border-amber-200" role="alert">
                        <svg class="w-5 h-5 inline mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <div><span class="font-bold">Pemberitahuan:</span> {{ session('warning') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 flex items-center p-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200" role="alert">
                        <svg class="w-5 h-5 inline mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <div><span class="font-bold">Perhatian!</span> {{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200" role="alert">
                        <div class="font-bold mb-1">Terdapat kesalahan pengisian:</div>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 pb-10">
                @yield('content')
            </main>
        </div>
    </div>

    <x-sweetalert-notifications />
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'iFind') }} - Admin Panel</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
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
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-100 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
            <!-- Brand Logo -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-black text-lg text-white shadow-md shadow-blue-500/30">i</div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">i-Find</span>
                        <span class="ml-1.5 px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase bg-blue-500/20 text-blue-300 rounded-md border border-blue-500/40">ADMIN</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            @php
                $pendingStaffCount = \App\Models\User::pendingStaff()->count();
                $openTicketsCount = \App\Models\Ticket::where('status', 'open')->count();
                $adminUnreadChatCount = \App\Models\ChatMessage::whereHas('conversation', function ($q) {
                    $q->where('type', 'staff_admin')->where(function ($sub) {
                        $sub->where('user_one_id', Auth::id())->orWhere('user_two_id', Auth::id());
                    });
                })->where('sender_id', '!=', Auth::id())->where('is_read', false)->count();
            @endphp
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
                <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Navigasi Utama</div>

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                        <span>Dashboard</span>
                    </div>
                </a>

                <!-- Verifikasi Staf -->
                <a href="{{ route('admin.staff-verification.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.staff-verification.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Verifikasi Staf</span>
                    </div>
                    @if($pendingStaffCount > 0)
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-500 text-slate-900 shadow-sm animate-pulse">{{ $pendingStaffCount }}</span>
                    @endif
                </a>

                <!-- Kategori -->
                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <span>Kategori Toko</span>
                    </div>
                </a>

                <!-- Pusat Bantuan / Tiket -->
                <a href="{{ route('admin.tickets.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.tickets.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Pusat Bantuan</span>
                    </div>
                    @if($openTicketsCount > 0)
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-rose-500 text-white shadow-sm">{{ $openTicketsCount }}</span>
                    @endif
                </a>

                <!-- Chat Staf -->
                <a href="{{ route('admin.chat.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.chat.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Chat Staf</span>
                    </div>
                    @if($adminUnreadChatCount > 0)
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-teal-500 text-white shadow-sm animate-pulse">{{ $adminUnreadChatCount }}</span>
                    @endif
                </a>
            </div>

            <!-- User Footer -->
            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 truncate">
                        <div class="w-9 h-9 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-400 flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
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
                        @yield('header_title', 'Admin Panel')
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex items-center text-xs text-slate-500 space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Sistem Aktif</span>
                    </div>

                    <div class="h-6 w-px bg-slate-200"></div>

                    <!-- Admin Profile Dropdown -->
                    <div class="flex items-center space-x-3">
                        <div class="text-right hidden md:block">
                            <p class="text-xs font-semibold text-slate-800">{{ Auth::user()->name ?? 'Administrator' }}</p>
                            <p class="text-[11px] text-teal-600 font-medium capitalize">{{ Auth::user()->role ?? 'admin' }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-slate-900 text-teal-400 font-bold flex items-center justify-center text-sm ring-2 ring-teal-500/20">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
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

                @if (session('error'))
                    <div class="mb-4 flex items-center p-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200" role="alert">
                        <svg class="w-5 h-5 inline mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <div><span class="font-bold">Perhatian!</span> {{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200" role="alert">
                        <div class="font-bold mb-1">Terdapat beberapa kesalahan:</div>
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

    @stack('scripts')
</body>
</html>

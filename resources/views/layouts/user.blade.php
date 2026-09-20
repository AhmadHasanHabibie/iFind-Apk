<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'i-Find — Platform Reservasi & LBS Khusus Pelajar' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col text-slate-800 bg-white hero-pattern antialiased selection:bg-blue-600 selection:text-white pb-16 md:pb-0"
      x-data="globalApp()">

    @php
        $unreadChatCount = 0;
        if (auth()->check()) {
            $unreadChatCount = \App\Models\ChatMessage::whereHas('conversation', function($q) {
                $q->where('user_one_id', auth()->id())->orWhere('user_two_id', auth()->id());
            })->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
        }
    @endphp

    <!-- CUSTOM TOAST NOTIFICATION STACK -->
    <div class="fixed top-6 right-4 sm:right-6 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-[calc(100%-2rem)]">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl p-4 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] flex items-center justify-between gap-3 transition-all duration-300 transform"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-4 scale-95">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                         :class="{
                             'bg-emerald-50 text-emerald-600 border border-emerald-100': toast.type === 'success',
                             'bg-rose-50 text-rose-600 border border-rose-100': toast.type === 'error',
                             'bg-blue-50 text-blue-600 border border-blue-100': toast.type === 'info'
                         }">
                        <i :class="{
                            'fa-solid fa-circle-check text-base': toast.type === 'success',
                            'fa-solid fa-circle-xmark text-base': toast.type === 'error',
                            'fa-solid fa-circle-info text-base': toast.type === 'info'
                        }"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold text-slate-900 leading-tight" x-text="toast.title"></p>
                        <p class="text-xs text-slate-600 truncate mt-0.5" x-text="toast.message"></p>
                    </div>
                </div>
                <button type="button" @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition shrink-0">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- GLOBAL CUSTOM CONFIRMATION MODAL -->
    <div x-show="confirmModal.open" x-cloak style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="confirmModal.open = false">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 space-y-4 transform transition-all"
             @click.outside="confirmModal.open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="text-center">
                <h3 class="text-base font-bold text-slate-900" x-text="confirmModal.title"></h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed" x-text="confirmModal.message"></p>
            </div>
            <div class="flex items-center justify-center gap-2 pt-2">
                <button type="button" @click="confirmModal.open = false"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
                <button type="button" @click="executeConfirm()"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition">
                    <span x-text="confirmModal.confirmText"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- NAVBAR (MATCHING REFERENCE IMAGE) -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-100 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Signature Brand Logo -->
            <div class="flex items-center space-x-6">
                <a href="{{ route('user.dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center font-black text-xl shadow-md shadow-blue-500/20 text-white group-hover:scale-105 transition-transform duration-200">
                        i
                    </div>
                    <span class="text-2xl font-black tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors duration-200">i-Find</span>
                    <span class="text-[10px] uppercase tracking-wider bg-blue-50 border border-blue-200 text-blue-600 font-bold px-2.5 py-1 rounded-full shadow-xs">Student Ver.</span>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center space-x-1 pl-4 border-l border-slate-200">
                    <a href="{{ route('user.dashboard') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('user.dashboard*') && !request()->routeIs('user.stores.*') ? 'text-blue-600 bg-blue-50/80 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <i class="fa-solid fa-compass mr-1.5 text-xs"></i>
                        Jelajah Tempat
                    </a>
                    <a href="{{ route('user.bookings.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('user.bookings.*') ? 'text-blue-600 bg-blue-50/80 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <i class="fa-solid fa-calendar-check mr-1.5 text-xs"></i>
                        Pesanan Saya
                    </a>
                    <a href="{{ route('user.chat.index') }}"
                       class="relative px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('user.chat.*') ? 'text-blue-600 bg-blue-50/80 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <i class="fa-solid fa-comments mr-1.5 text-xs"></i>
                        Chat
                        @if($unreadChatCount > 0)
                            <span class="ml-1.5 px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-600 text-white shadow-xs animate-pulse">
                                {{ $unreadChatCount }}
                            </span>
                        @endif
                    </a>
<<<<<<< HEAD
                    <a href="{{ route('user.tickets.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('user.tickets.*') ? 'text-blue-600 bg-blue-50/80 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <i class="fa-solid fa-headset mr-1.5 text-xs"></i>
                        Bantuan
                    </a>
=======
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d
                </nav>
            </div>

            <!-- Right Menu: Profile Dropdown & Logout -->
            <div class="flex items-center space-x-3">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" type="button" class="flex items-center space-x-2.5 p-1.5 rounded-2xl hover:bg-slate-100 transition focus:outline-hidden group">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 font-black text-xs flex items-center justify-center group-hover:scale-105 transition shadow-xs">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-blue-600 font-semibold uppercase tracking-wider">Student Customer</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:text-slate-600 transition"></i>
                    </button>

                    <div x-show="open" x-cloak style="display: none;"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-52 rounded-2xl bg-white shadow-xl border border-slate-200 py-2 z-50">
                        <div class="px-4 py-2.5 border-b border-slate-100">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                            <i class="fa-solid fa-user-gear mr-2 text-slate-400"></i> Pengaturan Profil
                        </a>
<<<<<<< HEAD
                        <a href="{{ route('user.tickets.index') }}" class="flex items-center px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                            <i class="fa-solid fa-headset mr-2 text-slate-400"></i> Tiket Bantuan
                        </a>
=======
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-2 text-red-400"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area with dot matrix background -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 z-40 px-2 py-1 flex items-center justify-around shadow-lg">
        <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center py-1.5 px-3 text-[10px] font-bold {{ request()->routeIs('user.dashboard*') && !request()->routeIs('user.stores.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">
            <i class="fa-solid fa-compass text-base mb-0.5"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('user.bookings.index') }}" class="flex flex-col items-center py-1.5 px-3 text-[10px] font-bold {{ request()->routeIs('user.bookings.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">
            <i class="fa-solid fa-calendar-check text-base mb-0.5"></i>
            <span>Pesanan</span>
        </a>
        <a href="{{ route('user.chat.index') }}" class="relative flex flex-col items-center py-1.5 px-3 text-[10px] font-bold {{ request()->routeIs('user.chat.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">
            <i class="fa-solid fa-comments text-base mb-0.5"></i>
            <span>Chat</span>
            @if($unreadChatCount > 0)
                <span class="absolute top-1 right-3 w-2 h-2 rounded-full bg-blue-600 ring-2 ring-white animate-ping"></span>
            @endif
        </a>
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center py-1.5 px-3 text-[10px] font-bold {{ request()->routeIs('profile.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">
            <i class="fa-solid fa-user text-base mb-0.5"></i>
            <span>Profil</span>
        </a>
    </nav>

    <!-- Footer matching theme -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center font-black text-white text-base shadow">i</div>
                <span class="text-lg font-black text-white tracking-tight">i-Find</span>
                <span class="text-xs text-slate-500">— Student Hangout & Booking Platform</span>
            </div>
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} i-Find. Platform Reservasi & LBS Khusus Pelajar #1.</p>
        </div>
    </footer>

    <!-- Global App Script: Custom Toasts & Modal Dialogs (No Native Browser Alert/Confirm) -->
    <script>
    function globalApp() {
        return {
            toasts: [],
            confirmModal: {
                open: false,
                title: '',
                message: '',
                confirmText: 'Lanjutkan',
                onConfirm: null,
            },

            init() {
                // Flash session toasts from Laravel
                @if (session('success'))
                    this.showToast('Berhasil', '{{ session('success') }}', 'success');
                @endif
                @if (session('error'))
                    this.showToast('Pemberitahuan', '{{ session('error') }}', 'error');
                @endif
                @if ($errors->any())
                    this.showToast('Gagal Memproses', '{{ $errors->first() }}', 'error');
                @endif

                // Listen for custom toast events from client-side scripts
                window.addEventListener('custom-toast', (e) => {
                    this.showToast(e.detail.title || 'Info', e.detail.message || '', e.detail.type || 'info');
                });

                // Listen for custom confirm events from client-side scripts
                window.addEventListener('custom-confirm', (e) => {
                    this.openConfirm(e.detail.title, e.detail.message, e.detail.confirmText, e.detail.onConfirm);
                });
            },

            showToast(title, message, type = 'info') {
                const id = Date.now() + Math.random();
                this.toasts.push({ id, title, message, type });
                setTimeout(() => this.removeToast(id), 4000);
            },

            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },

            openConfirm(title, message, confirmText, onConfirm) {
                this.confirmModal.title = title;
                this.confirmModal.message = message;
                this.confirmModal.confirmText = confirmText || 'Ya, Lanjutkan';
                this.confirmModal.onConfirm = onConfirm;
                this.confirmModal.open = true;
            },

            executeConfirm() {
                this.confirmModal.open = false;
                if (typeof this.confirmModal.onConfirm === 'function') {
                    this.confirmModal.onConfirm();
                }
            }
        }
    }
    </script>
    @stack('scripts')
</body>
</html>

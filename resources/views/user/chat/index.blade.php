@extends('layouts.user')

@section('content')
<div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden h-[calc(100vh-13rem)] flex flex-col md:flex-row">

    <!-- Left Sidebar: Conversations List -->
    <div class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200/80 flex flex-col shrink-0 {{ $activeConversation ? 'hidden md:flex' : 'flex' }}">
        <!-- Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-sm font-black text-slate-900 tracking-tight">Chat Toko</h2>
                <p class="text-[11px] text-slate-500 font-medium">Percakapan langsung dengan staf pengelola tempat</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                {{ $conversations->count() }} Percakapan
            </span>
        </div>

        <!-- Conversations Scrollable Area -->
        <div class="flex-1 overflow-y-auto p-3 space-y-1">
            @forelse($conversations as $conv)
                @include('user.chat.partials._thread', ['conv' => $conv])
            @empty
                <div class="text-center py-16 px-4 text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-xl mb-2">
                        <i class="fa-regular fa-comments"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700">Belum ada obrolan</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto leading-relaxed">Pilih tempat yang Anda minati lalu klik tombol "Chat dengan Toko" pada halaman detail.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Area: Chat Window / Empty State -->
    <div class="flex-1 flex flex-col h-full bg-slate-50/40 {{ ! $activeConversation ? 'hidden md:flex' : 'flex' }}">
        @if($activeConversation)
            <div class="md:hidden p-2 bg-white border-b border-slate-100">
                <a href="{{ route('user.chat.index') }}" class="inline-flex items-center text-xs font-bold text-slate-600 hover:text-blue-600 px-3 py-1.5 rounded-lg bg-slate-100">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i>
                    <span>Kembali ke Daftar Chat</span>
                </a>
            </div>
            <x-chat-thread :conversation="$activeConversation"
                           :messages="$messages"
                           send-route-name="user.chat.send"
                           poll-route-name="user.chat.poll" />
        @else
            <!-- Empty Chat View Placeholder -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
                <div class="w-16 h-16 rounded-3xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center mb-4 text-2xl shadow-xs">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h3 class="text-base font-black text-slate-800">Pilih Percakapan Toko</h3>
                <p class="text-xs text-slate-500 max-w-sm mt-1 leading-relaxed">
                    Pilih salah satu percakapan di sebelah kiri untuk berdiskusi dengan staf pengelola tempat secara langsung.
                </p>
            </div>
        @endif
    </div>

</div>
@endsection

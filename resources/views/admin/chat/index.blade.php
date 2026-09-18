@extends('layouts.admin')

@section('header_title', 'Chat dengan Staf Toko')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row h-[calc(100vh-12rem)] min-h-[550px]">
    <!-- Left Column: Conversations List -->
    <div class="w-full md:w-80 lg:w-96 border-r border-slate-200/80 flex flex-col bg-slate-50/50 shrink-0">
        <div class="p-4 border-b border-slate-200 bg-white flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Chat dengan Staf Toko</h2>
                <p class="text-[11px] text-slate-500">Dukungan teknis, pertanyaan & koordinasi mitra</p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                {{ $conversations->count() }} Percakapan
            </span>
        </div>

        <!-- Conversations Scroll List -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
            @forelse($conversations as $conv)
                @php
                    $partner = $conv->otherParticipant(Auth::id());
                    $isActive = ($activeConversation && $activeConversation->id === $conv->id);
                    $lastMessage = $conv->messages->first();
                    $unreadCount = $conv->unreadCountFor(Auth::id());
                    $storeName = ($partner && $partner->store) ? $partner->store->name : 'Tidak ada toko';
                @endphp
                <a href="{{ route('admin.chat.show', $conv) }}"
                   class="block p-4 transition hover:bg-slate-100/70 {{ $isActive ? 'bg-teal-50/80 border-l-4 border-teal-600' : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-9 h-9 rounded-full font-bold text-xs flex items-center justify-center shrink-0 bg-teal-100 text-teal-700">
                                {{ strtoupper(substr($partner->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">
                                    {{ $partner->name ?? 'Staf' }} <span class="text-slate-500 font-normal">({{ $storeName }})</span>
                                </p>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ $lastMessage ? $lastMessage->message : 'Belum ada pesan' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right shrink-0 ml-2">
                            <span class="text-[10px] text-slate-400 block font-mono">
                                {{ $conv->last_message_at ? $conv->last_message_at->format('H:i') : '' }}
                            </span>
                            @if($unreadCount > 0)
                                <span class="inline-block mt-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-teal-600 text-white">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    <p>Belum ada percakapan dengan staf.</p>
                    <p class="mt-1 text-[11px] text-slate-400">Gunakan tombol "Chat Staf Ini" pada halaman Verifikasi Staf atau Tiket Bantuan untuk memulai percakapan.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Chat Box or Empty State -->
    <div class="flex-1 flex flex-col bg-white">
        @if($activeConversation)
            <x-chat-thread :conversation="$activeConversation"
                           :messages="$messages"
                           send-route-name="admin.chat.send"
                           poll-route-name="admin.chat.poll" />
        @else
            <!-- Empty State -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-700">Pilih Percakapan Staf</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-xs">Pilih salah satu staf dari daftar di sebelah kiri untuk membaca dan merespon obrolan.</p>
            </div>
        @endif
    </div>
</div>
@endsection

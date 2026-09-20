@extends('layouts.staff')

@section('header_title', 'Pusat Pesan & Chatbox')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row h-[calc(100vh-12rem)] min-h-[550px]">

    <!-- Left Column: Conversations List -->
    <div class="w-full md:w-80 lg:w-96 border-r border-slate-200/80 flex flex-col bg-slate-50/50 shrink-0">
        <!-- Chat Context Tabs -->
        <div class="p-3 border-b border-slate-200 bg-white">
            <div class="flex items-center space-x-1 p-1 bg-slate-100 rounded-xl">
                <a href="{{ route('staff.chat.index', ['tab' => 'customer']) }}"
                   class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg transition {{ $tab === 'customer' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    Chat Pelanggan
                </a>
                <a href="{{ route('staff.chat.index', ['tab' => 'admin']) }}"
                   class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg transition {{ $tab === 'admin' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    Chat ke Admin
                </a>
            </div>

            <!-- Hubungi Admin Quick Action (di tab admin) -->
            @if($tab === 'admin')
                <div class="mt-2.5">
                    <form method="POST" action="{{ route('staff.chat.contact-admin') }}">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-3 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Mulai Chat Admin</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Conversations Scroll List -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
            @php
                $list = ($tab === 'customer') ? $customerConversations : $adminConversations;
            @endphp

            @forelse($list as $conv)
                @php
                    $partner = $conv->otherParticipant(Auth::id());
                    $isActive = ($activeConversation && $activeConversation->id === $conv->id);
                    $lastMessage = $conv->messages->first();
                    $unreadCount = $conv->unreadCountFor(Auth::id());

                    if ($tab === 'customer') {
                        $storeName = $conv->store ? $conv->store->name : (Auth::user()->store ? Auth::user()->store->name : 'Toko');
                        $title = $partner ? $partner->name : 'Pelanggan';
                        $subtitleContext = '· Booking di ' . $storeName;
                    } else {
                        $title = 'Admin iFind';
                        $subtitleContext = '· Bantuan & Dukungan Sistem';
                    }
                @endphp
                <a href="{{ route('staff.chat.show', ['conversation' => $conv->id, 'tab' => $tab]) }}"
                   class="block p-4 transition hover:bg-slate-100/70 {{ $isActive ? ($tab === 'admin' ? 'bg-teal-50/80 border-l-4 border-teal-600' : 'bg-blue-50/80 border-l-4 border-blue-600') : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-9 h-9 rounded-full font-bold text-xs flex items-center justify-center shrink-0 {{ $tab === 'admin' ? 'bg-teal-100 text-teal-700' : 'bg-blue-100 text-blue-700' }}">
                                @if($tab === 'admin')
                                    <i class="fa-solid fa-headset text-xs"></i>
                                @else
                                    {{ strtoupper(substr($title, 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">
                                    {{ $title }}
                                    <span class="text-[11px] text-slate-400 font-normal">{{ $subtitleContext }}</span>
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
                                <span class="inline-block mt-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $tab === 'admin' ? 'bg-teal-600 text-white' : 'bg-blue-600 text-white' }}">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    @if($tab === 'customer')
                        <p>Belum ada percakapan dengan pelanggan.</p>
                        <p class="mt-1 text-[11px] text-slate-400">Pesan dari customer toko Anda atau inisiasi chat dari tabel booking akan muncul di sini.</p>
                    @else
                        <p>Belum ada percakapan dengan Admin.</p>
                        <p class="mt-1 text-[11px] text-slate-400">Klik "Mulai Chat Admin" di atas untuk bertanya seputar kendala mitra.</p>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Active Conversation Messages & Input Box -->
    <div class="flex-1 flex flex-col bg-white">
        @if($activeConversation)
            <x-chat-thread :conversation="$activeConversation"
                           :messages="$messages"
                           send-route-name="staff.chat.send"
                           poll-route-name="staff.chat.poll" />
        @else
            <!-- No Conversation Selected Empty State -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-700">Pilih Percakapan</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-xs">Pilih salah satu kontak obrolan di samping kiri untuk membuka riwayat pesan.</p>
            </div>
        @endif
    </div>
</div>
@endsection

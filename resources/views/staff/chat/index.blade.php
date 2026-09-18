@extends('layouts.staff')

@section('header_title', 'Pusat Pesan & Chatbox')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row h-[calc(100vh-12rem)] min-h-[550px]"
     x-data="chatApp({
        activeId: {{ $activeConversation ? $activeConversation->id : 'null' }},
        pollUrl: '{{ $activeConversation ? route('staff.chat.poll', $activeConversation) : '' }}',
        lastMessageId: {{ $messages->count() > 0 ? $messages->last()->id : 0 }}
     })">

    <!-- Left Column: Conversations List -->
    <div class="w-full md:w-80 border-r border-slate-200/80 flex flex-col bg-slate-50/50 shrink-0">
        <!-- Chat Context Tabs -->
        <div class="p-3 border-b border-slate-200 bg-white">
            <div class="flex items-center space-x-1 p-1 bg-slate-100 rounded-xl">
                <a href="{{ route('staff.chat.index', ['tab' => 'customer']) }}"
                   class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg transition {{ $tab === 'customer' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    Pelanggan
                </a>
                <a href="{{ route('staff.chat.index', ['tab' => 'admin']) }}"
                   class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg transition {{ $tab === 'admin' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    Admin Support
                </a>
            </div>

            <!-- Hubungi Admin Quick Action (di tab admin) -->
            @if($tab === 'admin')
                <div class="mt-2">
                    <form method="POST" action="{{ route('staff.chat.contact-admin') }}">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center space-x-1.5">
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
                    $isUserOneMe = $conv->user_one_id === Auth::id();
                    $partner = $isUserOneMe ? $conv->userTwo : $conv->userOne;
                    $isActive = $activeConversation && $activeConversation->id === $conv->id;
                    $lastMessage = $conv->messages->first();
                    $unreadCount = $conv->messages()->where('sender_id', '!=', Auth::id())->where('is_read', false)->count();
                @endphp
                <a href="{{ route('staff.chat.show', ['conversation' => $conv->id, 'tab' => $tab]) }}"
                   class="block p-4 transition hover:bg-slate-100/70 {{ $isActive ? 'bg-blue-50/80 border-l-4 border-blue-600' : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-9 h-9 rounded-full font-bold text-xs flex items-center justify-center shrink-0 {{ $tab === 'admin' ? 'bg-teal-100 text-teal-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ strtoupper(substr($partner->name ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $partner->name ?? 'Partner' }}</p>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ $lastMessage ? $lastMessage->message : 'Belum ada pesan' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right shrink-0 ml-2">
                            <span class="text-[10px] text-slate-400 block">
                                {{ $conv->last_message_at ? $conv->last_message_at->format('H:i') : '' }}
                            </span>
                            @if($unreadCount > 0)
                                <span class="inline-block mt-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-600 text-white">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Tidak ada percakapan di tab ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Active Conversation Messages & Input Box -->
    <div class="flex-1 flex flex-col bg-white">
        @if($activeConversation)
            @php
                $isUserOneMe = $activeConversation->user_one_id === Auth::id();
                $partner = $isUserOneMe ? $activeConversation->userTwo : $activeConversation->userOne;
            @endphp
            <!-- Chat Header -->
            <div class="h-16 px-6 border-b border-slate-200/80 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full font-bold text-xs flex items-center justify-center {{ $activeConversation->store_id ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700' }}">
                        {{ strtoupper(substr($partner->name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ $partner->name ?? 'Pengguna' }}</h3>
                        <p class="text-[11px] text-slate-400">
                            {{ $partner->role === 'admin' ? 'Administrator iFind' : ($partner->role === 'staff' ? 'Staf Toko' : 'Customer Pelanggan') }}
                            &bull; {{ $partner->phone ?: $partner->email }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $activeConversation->store_id ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                        {{ $activeConversation->store_id ? 'Chat Toko' : 'Chat Internal Admin' }}
                    </span>
                </div>
            </div>

            <!-- Messages Thread Box -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/40" id="messages-container">
                <template x-for="msg in messageList" :key="msg.id">
                    <div class="flex flex-col" :class="msg.is_me ? 'items-end' : 'items-start'">
                        <div class="max-w-md rounded-2xl px-4 py-2.5 text-sm shadow-xs leading-relaxed"
                             :class="msg.is_me ? 'bg-blue-600 text-white rounded-br-xs' : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-xs'">
                            <p x-text="msg.message" class="whitespace-pre-line"></p>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 px-1" x-text="msg.time"></span>
                    </div>
                </template>
            </div>

            <!-- Chat Input Box -->
            <div class="p-4 border-t border-slate-200 bg-white shrink-0">
                <form @submit.prevent="sendMessage" class="flex items-center space-x-3">
                    <input type="text"
                           x-model="newMessage"
                           required
                           placeholder="Ketik pesan balasan Anda di sini..."
                           class="flex-1 text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-4">
                    <button type="submit"
                            :disabled="isSending || !newMessage.trim()"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center space-x-1.5 shrink-0">
                        <span>Kirim</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
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

@push('scripts')
<script>
    function chatApp(config) {
        return {
            activeId: config.activeId,
            pollUrl: config.pollUrl,
            messageList: @json($formattedMessages),
            newMessage: '',
            isSending: false,
            pollTimer: null,

            init() {
                this.scrollToBottom();

                if (this.activeId && this.pollUrl) {
                    // AJAX polling every 4 seconds
                    this.pollTimer = setInterval(() => {
                        this.pollNewMessages();
                    }, 4000);
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            },

            async pollNewMessages() {
                if (!this.activeId || !this.pollUrl) return;

                try {
                    const response = await fetch(`${this.pollUrl}?last_id=${this.lastMessageId}`);
                    if (!response.ok) return;

                    const data = await response.json();
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            this.messageList.push(msg);
                            if (msg.id > this.lastMessageId) {
                                this.lastMessageId = msg.id;
                            }
                        });
                        this.scrollToBottom();
                    }
                } catch (e) {
                    // Silently ignore polling errors
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || this.isSending) return;

                const text = this.newMessage;
                this.isSending = true;

                try {
                    const response = await fetch("{{ $activeConversation ? route('staff.chat.send', $activeConversation) : '' }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: text })
                    });

                    if (response.ok) {
                        const result = await response.json();
                        if (result.message) {
                            this.messageList.push(result.message);
                            if (result.message.id > this.lastMessageId) {
                                this.lastMessageId = result.message.id;
                            }
                            this.newMessage = '';
                            this.scrollToBottom();
                        }
                    }
                } catch (err) {
                    console.error('Error sending message:', err);
                } finally {
                    this.isSending = false;
                }
            }
        };
    }
</script>
@endpush

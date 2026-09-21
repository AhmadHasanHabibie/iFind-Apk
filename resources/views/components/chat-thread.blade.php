@props(['conversation', 'messages', 'sendRouteName', 'pollRouteName'])

@php
    $currentUserId = auth()->id();
    $currentUserRole = auth()->user()->role ?? 'user';
    $partner = $conversation->otherParticipant($currentUserId);
    $initialLastId = $messages->count() > 0 ? $messages->last()->id : 0;

    // Tentukan context header berdasarkan tipe percakapan dan role
    if ($conversation->type === 'staff_admin') {
        if ($currentUserRole === 'admin') {
            $headerTitle = $partner ? $partner->name : 'Staf Toko';
            $headerSubtitle = 'Staf Toko' . ($partner && $partner->store ? ' (' . $partner->store->name . ')' : ' (Tidak ada toko)');
            $headerBadge = 'Dukungan Staf';
        } else {
            $headerTitle = 'Admin iFind';
            $headerSubtitle = 'Dukungan & Layanan Mitra Resmi iFind';
            $headerBadge = 'Bantuan Admin';
        }
    } else {
        // user_staff
        if ($currentUserRole === 'user') {
            $headerTitle = $conversation->store ? $conversation->store->name : ($partner ? $partner->name : 'Toko');
            $headerSubtitle = 'Staf Toko Siaga';
            $headerBadge = 'Chat Toko';
        } else {
            $headerTitle = $partner ? $partner->name : 'Pelanggan';
            $headerSubtitle = 'Customer Pelanggan · Booking di ' . ($conversation->store ? $conversation->store->name : 'Toko');
            $headerBadge = 'Chat Pelanggan';
        }
    }
@endphp

<div class="flex-1 flex flex-col h-full bg-white relative"
     x-data="chatThread({
        sendUrl: '{{ route($sendRouteName, $conversation) }}',
        pollUrl: '{{ route($pollRouteName, $conversation) }}',
        initialLastId: {{ $initialLastId }}
     })"
     x-init="init()">

    <!-- Top Chat Header -->
    <div class="h-16 px-5 sm:px-6 border-b border-slate-200/80 flex items-center justify-between bg-white shrink-0 shadow-2xs">
        <div class="flex items-center space-x-3.5 min-w-0">
            <!-- Avatar -->
            <div class="w-10 h-10 rounded-2xl font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs {{ $conversation->type === 'staff_admin' ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                @if($conversation->type === 'user_staff' && $currentUserRole === 'user')
                    <i class="fa-solid fa-store text-sm"></i>
                @elseif($conversation->type === 'staff_admin' && $currentUserRole === 'staff')
                    <i class="fa-solid fa-headset text-sm"></i>
                @else
                    {{ strtoupper(substr($partner->name ?? 'P', 0, 1)) }}
                @endif
            </div>

            <!-- Titles -->
            <div class="min-w-0">
                <h3 class="text-sm font-bold text-slate-900 truncate flex items-center gap-2">
                    <span>{{ $headerTitle }}</span>
                </h3>
                <p class="text-[11px] text-slate-500 font-medium truncate flex items-center gap-1.5 mt-0.5">
                    @if($conversation->type === 'user_staff' && $currentUserRole === 'user')
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    @endif
                    <span>{{ $headerSubtitle }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2 shrink-0">
            @if($conversation->type === 'user_staff' && $currentUserRole === 'user' && $conversation->store)
                <a href="{{ route('user.stores.show', $conversation->store->slug) }}"
                   class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200 shadow-2xs hidden sm:inline-flex items-center space-x-1.5">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    <span>Lihat Toko</span>
                </a>
            @endif

            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wide {{ $conversation->type === 'staff_admin' ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                {{ $headerBadge }}
            </span>
        </div>
    </div>

    <!-- Messages Container (Direct Server Render + Alpine Append) -->
    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-3.5 bg-slate-50/40"
         id="chat-messages-scroll"
         x-ref="messagesContainer">

        <!-- 1. Server-side Rendered Initial Messages (Never Blank) -->
        @forelse($messages as $msg)
            @php
                $isMe = ($msg->sender_id === $currentUserId);
            @endphp
            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}" data-message-id="{{ $msg->id }}">
                <div class="max-w-[85%] sm:max-w-md rounded-2xl px-4 py-2.5 text-sm shadow-2xs leading-relaxed {{ $isMe ? 'bg-blue-600 text-white rounded-br-xs' : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-xs' }}">
                    <p class="whitespace-pre-line break-words">{{ $msg->message }}</p>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 px-1 font-mono">
                    {{ $msg->created_at ? $msg->created_at->format('H:i') : '' }}
                </span>
            </div>
        @empty
            <div id="chat-empty-state" x-show="newMessages.length === 0" class="text-center py-16 text-slate-400 text-xs">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400 text-xl mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <p class="font-bold text-slate-600">Belum ada percakapan</p>
                <p class="text-[11px] text-slate-400 mt-1">Kirim pesan pertama untuk memulai diskusi.</p>
            </div>
        @endforelse

        <!-- 2. Dynamic Messages Appended from Polling / Sending -->
        <template x-for="msg in newMessages" :key="msg.id">
            <div class="flex flex-col" :class="msg.is_me ? 'items-end' : 'items-start'">
                <div class="max-w-[85%] sm:max-w-md rounded-2xl px-4 py-2.5 text-sm shadow-2xs leading-relaxed"
                     :class="msg.is_me ? 'bg-blue-600 text-white rounded-br-xs' : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-xs'">
                    <p class="whitespace-pre-line break-words" x-text="msg.message"></p>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 px-1 font-mono" x-text="msg.time"></span>
            </div>
        </template>
    </div>

    <!-- Chat Input Box -->
    <div class="p-3.5 sm:p-4 bg-white border-t border-slate-200/80 shrink-0">
        <form @submit.prevent="sendMessage()" class="flex items-center space-x-2.5">
            <input type="text"
                   x-model="messageInput"
                   x-ref="inputField"
                   placeholder="Ketik pesan Anda di sini..."
                   autocomplete="off"
                   :disabled="isSending"
                   class="flex-1 text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-4 disabled:bg-slate-100 disabled:text-slate-400">

            <button type="submit"
                    :disabled="!messageInput.trim() || isSending"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center space-x-2 shrink-0">
                <span x-show="!isSending">Kirim</span>
                <span x-show="isSending" style="display: none;">Mengirim...</span>
                <svg x-show="!isSending" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </form>
    </div>
</div>

<script>
    function chatThread(config) {
        return {
            sendUrl: config.sendUrl,
            pollUrl: config.pollUrl,
            lastMessageId: config.initialLastId,
            newMessages: [],
            messageInput: '',
            isSending: false,
            pollInterval: null,

            init() {
                this.scrollToBottom();

                // Polling setiap 4 detik
                if (this.pollUrl) {
                    this.pollInterval = setInterval(() => {
                        this.pollNewMessages();
                    }, 4000);
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            },

            async pollNewMessages() {
                if (!this.pollUrl) return;

                try {
                    const url = new URL(this.pollUrl, window.location.origin);
                    url.searchParams.set('after_id', this.lastMessageId);
                    url.searchParams.set('last_id', this.lastMessageId);

                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            // Cegah duplikasi jika sudah ada di newMessages
                            if (!this.newMessages.some(m => m.id === msg.id) && msg.id > this.lastMessageId) {
                                this.newMessages.push(msg);
                                if (msg.id > this.lastMessageId) {
                                    this.lastMessageId = msg.id;
                                }
                            }
                        });
                        this.scrollToBottom();
                    }
                } catch (e) {
                    // Abaikan polling error sementara
                }
            },

            async sendMessage() {
                const text = this.messageInput.trim();
                if (!text || this.isSending) return;

                // Langsung kosongkan input agar responsif (bug input tidak reset diperbaiki)
                this.messageInput = '';
                this.isSending = true;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch(this.sendUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ message: text })
                    });

                    if (res.ok) {
                        const data = await res.json();
                        if (data.message) {
                            this.newMessages.push(data.message);
                            if (data.message.id > this.lastMessageId) {
                                this.lastMessageId = data.message.id;
                            }
                            this.scrollToBottom();
                        }
                    } else {
                        // Jika gagal, kembalikan teks pesan agar user tidak kehilangan input
                        this.messageInput = text;
                        if (window.toastError) {
                            window.toastError('Gagal mengirim pesan. Silakan coba lagi.');
                        } else {
                            alert('Gagal mengirim pesan. Silakan coba lagi.');
                        }
                    }
                } catch (err) {
                    this.messageInput = text;
                    if (window.toastError) {
                        window.toastError('Terjadi kesalahan jaringan saat mengirim pesan.');
                    }
                    console.error('Send error:', err);
                } finally {
                    this.isSending = false;
                    this.$nextTick(() => {
                        this.$refs.inputField?.focus();
                    });
                }
            }
        };
    }
</script>

@php
    $lastMsg = $conv->messages->first();
    $unreadCount = $conv->unreadCountFor(auth()->id());
    $isActive = (isset($activeConversation) && $activeConversation && $activeConversation->id === $conv->id);
    $store = $conv->store;
@endphp

<a href="{{ route('user.chat.show', $conv) }}"
   class="block p-3.5 rounded-2xl transition {{ $isActive ? 'bg-blue-50/90 border border-blue-200' : 'hover:bg-slate-50 border border-transparent' }}">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-bold text-sm flex items-center justify-center shrink-0">
            <i class="fa-solid fa-store"></i>
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold truncate {{ $isActive ? 'text-blue-900' : 'text-slate-800' }}">
                    {{ $store->name ?? 'Toko' }}
                </h4>
                @if($lastMsg)
                    <span class="text-[10px] text-slate-400 shrink-0 ml-1 font-mono">
                        {{ $lastMsg->created_at->format('H:i') }}
                    </span>
                @endif
            </div>

            <div class="flex items-center space-x-1.5 mt-0.5">
                @if($store && $store->is_active)
                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span>
                        Staf online/aktif
                    </span>
                @endif
                <p class="text-[11px] text-slate-500 truncate">
                    {{ $lastMsg ? $lastMsg->message : 'Belum ada pesan...' }}
                </p>
            </div>
        </div>

        @if($unreadCount > 0)
            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-600 text-white shrink-0">
                {{ $unreadCount }}
            </span>
        @endif
    </div>
</a>

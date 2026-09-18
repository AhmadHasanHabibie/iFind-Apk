@extends('layouts.admin')

@section('header_title', 'Detail Tiket Bantuan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Nav & Status Changer -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-teal-700 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Semua Tiket
        </a>

        <!-- Status Changer Form -->
        <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}" class="flex items-center space-x-2">
            @csrf
            @method('PATCH')
            <span class="text-xs font-bold text-slate-500">Status Tiket:</span>
            <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-xl border-slate-300 text-slate-800 focus:border-teal-500 focus:ring-teal-500">
                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </form>
    </div>

    <!-- Ticket Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                        {{ $ticket->ticket_code }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                        Kategori: {{ $ticket->category }}
                    </span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900">{{ $ticket->subject }}</h2>
            </div>

            <div class="text-right">
                <span class="text-xs text-slate-400 block">Dibuat pada</span>
                <span class="text-xs font-bold text-slate-700">{{ $ticket->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
        </div>

        <!-- Sender Info & Initial Message -->
        <div class="pt-6">
            <div class="flex items-start space-x-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-sm flex-shrink-0">
                    {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <p class="font-bold text-slate-800">{{ $ticket->user->name ?? 'User' }}</p>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded {{ $ticket->sender_role === 'staff' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $ticket->sender_role === 'staff' ? 'Staf Toko' : 'Customer (User)' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">{{ $ticket->user->email ?? '-' }}</p>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                {{ $ticket->description }}
            </div>
        </div>
    </div>

    <!-- Conversation / Reply Thread -->
    <div class="space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">
            Riwayat Tanggapan & Diskusi ({{ $ticket->replies->count() }})
        </h3>

        @forelse($ticket->replies as $reply)
            @php
                $isAdminReply = $reply->user && $reply->user->role === 'admin';
            @endphp
            <div class="p-6 rounded-2xl border transition {{ $isAdminReply ? 'bg-teal-50/40 border-teal-200/80 ml-4 sm:ml-8' : 'bg-white border-slate-200/80 mr-4 sm:mr-8 shadow-sm' }}">
                <div class="flex items-center justify-between mb-3 pb-2 border-b {{ $isAdminReply ? 'border-teal-100' : 'border-slate-100' }}">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs {{ $isAdminReply ? 'bg-teal-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ strtoupper(substr($reply->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-800">{{ $reply->user->name ?? 'Pengguna' }}</span>
                            @if($isAdminReply)
                                <span class="ml-1 px-1.5 py-0.2 text-[10px] font-bold bg-teal-100 text-teal-800 rounded">Admin Support</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400">{{ $reply->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                <div class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                    {{ $reply->message }}
                </div>
            </div>
        @empty
            <div class="p-8 text-center bg-white rounded-2xl border border-dashed border-slate-200 text-slate-400 text-xs">
                Belum ada tanggapan untuk tiket ini. Kirim balasan pertama melalui formulir di bawah.
            </div>
        @endforelse
    </div>

    <!-- Reply Form Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <h4 class="text-base font-bold text-slate-900 mb-4 flex items-center space-x-2">
            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
            <span>Beri Balasan Sebagai Admin</span>
        </h4>

        <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" class="space-y-4">
            @csrf
            <div>
                <textarea name="message"
                          rows="4"
                          required
                          placeholder="Tulis pesan respon atau solusi untuk pengirim tiket..."
                          class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Status tiket akan otomatis beralih ke <strong>In Progress</strong> jika sebelumnya berstatus <strong>Open</strong>.</p>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    <span>Kirim Tanggapan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

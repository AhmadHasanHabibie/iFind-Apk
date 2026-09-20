@extends('layouts.user')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.tickets.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 transition shadow-xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-100">
                        {{ $ticket->ticket_code }}
                    </span>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 uppercase">
                        {{ $ticket->category }}
                    </span>
                </div>
                <h1 class="text-lg font-black text-slate-900 mt-1">{{ $ticket->subject }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($ticket->status === 'open')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">
                    <i class="fa-solid fa-clock mr-1"></i> Menunggu Respon Admin
                </span>
            @elseif($ticket->status === 'in_progress')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200">
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Sedang Ditangani Admin
                </span>
            @elseif($ticket->status === 'resolved')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <i class="fa-solid fa-circle-check mr-1"></i> Selesai
                </span>
            @else
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                    Ditutup
                </span>
            @endif
        </div>
    </div>

    <!-- Ticket Original Post -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">{{ auth()->user()->name }} (Anda)</p>
                    <p class="text-[11px] text-slate-400">{{ $ticket->created_at->isoFormat('D MMMM Y, HH:mm') }}</p>
                </div>
            </div>
            <span class="text-xs text-slate-400">Pengirim Tiket</span>
        </div>
        <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $ticket->description }}</p>
    </div>

    <!-- Replies Thread -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Diskusi & Balasan ({{ $ticket->replies->count() }})</h3>

        @forelse($ticket->replies as $reply)
            @php $isAdmin = $reply->user->role === 'admin'; @endphp
            <div class="bg-white rounded-3xl border {{ $isAdmin ? 'border-teal-200 bg-teal-50/20' : 'border-slate-100' }} p-6 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b {{ $isAdmin ? 'border-teal-100' : 'border-slate-100' }} pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl {{ $isAdmin ? 'bg-teal-600 text-white' : 'bg-blue-50 text-blue-600' }} flex items-center justify-center font-bold text-xs">
                            {{ $isAdmin ? 'A' : strtoupper(substr($reply->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-bold text-slate-900">{{ $reply->user->name }}</p>
                                @if($isAdmin)
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-teal-100 text-teal-700">Official Admin</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400">{{ $reply->created_at->isoFormat('D MMMM Y, HH:mm') }}</p>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $reply->message }}</p>
            </div>
        @empty
            <div class="bg-slate-50 rounded-2xl p-6 text-center text-xs text-slate-400">
                Belum ada balasan untuk tiket ini. Tim admin akan segera meninjau tiket Anda.
            </div>
        @endforelse
    </div>

    <!-- Reply Form -->
    @if($ticket->status !== 'closed')
        <form method="POST" action="{{ route('user.tickets.reply', $ticket) }}" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
            @csrf
            <div>
                <label for="message" class="block text-xs font-bold text-slate-700 mb-1.5">Tulis Balasan</label>
                <textarea id="message" name="message" rows="3" required
                          placeholder="Tulis pesan tanggapan atau informasi tambahan..."
                          class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-hidden transition @error('message') border-rose-500 @enderror"></textarea>
                @error('message')
                    <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition">
                    <i class="fa-solid fa-paper-plane mr-1.5"></i> Kirim Balasan
                </button>
            </div>
        </form>
    @else
        <div class="bg-slate-100 rounded-2xl p-4 text-center text-xs text-slate-500 font-medium">
            Tiket ini telah ditutup oleh Admin. Jika masih ada kendala lain, silakan buat tiket baru.
        </div>
    @endif
</div>
@endsection
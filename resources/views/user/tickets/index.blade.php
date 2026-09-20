@extends('layouts.user')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-xs">
                    <i class="fa-solid fa-headset"></i>
                </span>
                Pusat Bantuan & Tiket
            </h1>
            <p class="text-sm text-slate-500 mt-1">Sampaikan keluhan, pertanyaan, atau kendala Anda seputar reservasi ke tim admin kami.</p>
        </div>
        <a href="{{ route('user.tickets.create') }}"
           class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/20 transition group">
            <i class="fa-solid fa-plus mr-2 text-xs group-hover:rotate-90 transition-transform"></i>
            Buat Tiket Baru
        </a>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2">
        <a href="{{ route('user.tickets.index') }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !$status ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Semua ({{ $counts['all'] }})
        </a>
        <a href="{{ route('user.tickets.index', ['status' => 'open']) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'open' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Menunggu Respon ({{ $counts['open'] }})
        </a>
        <a href="{{ route('user.tickets.index', ['status' => 'in_progress']) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'in_progress' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Sedang Diproses ({{ $counts['in_progress'] }})
        </a>
        <a href="{{ route('user.tickets.index', ['status' => 'resolved']) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'resolved' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Selesai ({{ $counts['resolved'] }})
        </a>
        <a href="{{ route('user.tickets.index', ['status' => 'closed']) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'closed' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Ditutup ({{ $counts['closed'] }})
        </a>
    </div>

    <!-- List Tiket -->
    @if($tickets->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-2xl mb-4">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Tiket Bantuan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Jika Anda mengalami kendala reservasi atau pembayaran, jangan ragu untuk membuat tiket bantuan baru.</p>
            <a href="{{ route('user.tickets.create') }}" class="inline-flex items-center mt-5 px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-1.5"></i> Buat Tiket Sekarang
            </a>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xs divide-y divide-slate-100 overflow-hidden">
            @foreach($tickets as $ticket)
                <a href="{{ route('user.tickets.show', $ticket) }}" class="block p-5 hover:bg-slate-50/80 transition group">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                                    {{ $ticket->ticket_code }}
                                </span>
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 uppercase">
                                    {{ $ticket->category }}
                                </span>
                                <span class="text-xs text-slate-400">• {{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                {{ $ticket->subject }}
                            </h4>
                            <p class="text-xs text-slate-500 line-clamp-1">
                                {{ $ticket->description }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if($ticket->status === 'open')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                    <i class="fa-solid fa-clock mr-1"></i> Menunggu Respon
                                </span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200">
                                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Sedang Diproses
                                </span>
                            @elseif($ticket->status === 'resolved')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Selesai
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                    Ditutup
                                </span>
                            @endif
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-blue-600 group-hover:translate-x-1 transition-all"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
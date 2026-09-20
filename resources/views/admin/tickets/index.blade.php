@extends('layouts.admin')

@section('header_title', 'Pusat Bantuan & Tiket')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}"
           class="p-4 rounded-2xl border transition {{ $status === 'open' ? 'bg-rose-50 border-rose-300 ring-2 ring-rose-200' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-slate-400">Open</span>
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">{{ $openCount }}</p>
            <p class="text-[11px] text-slate-400">Perlu tindak lanjut</p>
        </a>

        <a href="{{ route('admin.tickets.index', ['status' => 'in_progress']) }}"
           class="p-4 rounded-2xl border transition {{ $status === 'in_progress' ? 'bg-amber-50 border-amber-300 ring-2 ring-amber-200' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-slate-400">Diproses</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">{{ $inProgressCount }}</p>
            <p class="text-[11px] text-slate-400">Sedang ditangani</p>
        </a>

        <a href="{{ route('admin.tickets.index', ['status' => 'resolved']) }}"
           class="p-4 rounded-2xl border transition {{ $status === 'resolved' ? 'bg-emerald-50 border-emerald-300 ring-2 ring-emerald-200' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-slate-400">Selesai</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">{{ $resolvedCount }}</p>
            <p class="text-[11px] text-slate-400">Masalah terselesaikan</p>
        </a>

        <a href="{{ route('admin.tickets.index', ['status' => 'closed']) }}"
           class="p-4 rounded-2xl border transition {{ $status === 'closed' ? 'bg-slate-100 border-slate-400 ring-2 ring-slate-300' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-slate-400">Ditutup</span>
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">{{ $closedCount }}</p>
            <p class="text-[11px] text-slate-400">Tiket diarsipkan</p>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <select name="status" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 text-slate-700 focus:border-teal-500 focus:ring-teal-500">
                    <option value="">Semua Status</option>
                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div>
                <select name="sender_role" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 text-slate-700 focus:border-teal-500 focus:ring-teal-500">
                    <option value="">Semua Peran Pengirim</option>
                    <option value="user" {{ $senderRole === 'user' ? 'selected' : '' }}>Customer (User)</option>
                    <option value="staff" {{ $senderRole === 'staff' ? 'selected' : '' }}>Staf Toko</option>
                </select>
            </div>

            @if($status || $senderRole)
                <a href="{{ route('admin.tickets.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 px-2 py-1">
                    Reset Filter
                </a>
            @endif
        </form>

        <span class="text-xs text-slate-400">Total ditemukan: {{ $tickets->total() }} tiket</span>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Kode Tiket</th>
                        <th class="py-3.5 px-6">Pengirim</th>
                        <th class="py-3.5 px-6">Subjek & Kategori</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6">Ditangani Oleh</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="font-mono text-xs font-bold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200">
                                    {{ $ticket->ticket_code }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-semibold text-slate-800">{{ $ticket->user->name ?? 'User Terhapus' }}</p>
                                <span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $ticket->sender_role === 'staff' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    {{ $ticket->sender_role === 'staff' ? 'Staf Toko' : 'User' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-800 line-clamp-1">{{ $ticket->subject }}</p>
                                <div class="flex items-center space-x-2 mt-0.5">
                                    <span class="text-xs text-slate-500 capitalize">Kategori: {{ $ticket->category }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($ticket->status === 'open')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-pulse"></span>
                                        Open
                                    </span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        In Progress
                                    </span>
                                @elseif($ticket->status === 'resolved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Resolved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                @if($ticket->handler)
                                    <div class="flex items-center space-x-1.5 font-medium text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>{{ $ticket->handler->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.tickets.show', $ticket) }}"
                                   class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold rounded-lg text-teal-700 bg-teal-50 hover:bg-teal-100 transition border border-teal-200">
                                    <span>Buka Respon</span>
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Tidak ada tiket yang ditemukan dengan filter tersebut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

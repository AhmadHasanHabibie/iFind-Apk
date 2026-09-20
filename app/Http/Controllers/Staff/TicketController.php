<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status');

        $query = Ticket::where('user_id', Auth::id())
            ->latest();

        if ($status && in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            $query->where('status', $status);
        }

        $tickets = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => Ticket::where('user_id', Auth::id())->count(),
            'open' => Ticket::where('user_id', Auth::id())->where('status', 'open')->count(),
            'in_progress' => Ticket::where('user_id', Auth::id())->where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('user_id', Auth::id())->where('status', 'resolved')->count(),
            'closed' => Ticket::where('user_id', Auth::id())->where('status', 'closed')->count(),
        ];

        return view('staff.tickets.index', compact('tickets', 'status', 'counts'));
    }

    public function create(): View
    {
        return view('staff.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:technical,account,booking,other'],
            'description' => ['required', 'string', 'min:10'],
        ], [
            'subject.required' => 'Subjek pengaduan wajib diisi.',
            'category.required' => 'Pilih kategori pengaduan.',
            'description.required' => 'Jelaskan kendala operasional toko Anda.',
            'description.min' => 'Penjelasan kendala minimal 10 karakter.',
        ]);

        do {
            $ticketCode = 'TCK-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (Ticket::where('ticket_code', $ticketCode)->exists());

        $ticket = Ticket::create([
            'ticket_code' => $ticketCode,
            'user_id' => Auth::id(),
            'sender_role' => 'staff',
            'subject' => $request->subject,
            'category' => $request->category,
            'description' => $request->description,
            'status' => 'open',
        ]);

        return redirect()->route('staff.tickets.show', $ticket)
            ->with('success', "Tiket bantuan #{$ticket->ticket_code} berhasil dibuat. Tim admin akan segera menindaklanjuti.");
    }

    public function show(Ticket $ticket): View
    {
        abort_if($ticket->user_id !== Auth::id(), 403, 'Akses tiket ditolak.');

        $ticket->load(['handler', 'replies.user']);

        return view('staff.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_if($ticket->user_id !== Auth::id(), 403, 'Akses tiket ditolak.');

        if ($ticket->status === 'closed') {
            return back()->with('error', 'Tiket ini telah ditutup.');
        }

        $request->validate([
            'message' => ['required', 'string', 'min:2'],
        ], [
            'message.required' => 'Pesan balasan tidak boleh kosong.',
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()->route('staff.tickets.show', $ticket)
            ->with('success', 'Balasan Anda berhasil terkirim ke Admin.');
    }
}

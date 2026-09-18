<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status');
        $senderRole = $request->get('sender_role');

        $query = Ticket::with(['user', 'handler'])->latest();

        if ($status && in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            $query->where('status', $status);
        }

        if ($senderRole && in_array($senderRole, ['user', 'staff'])) {
            $query->where('sender_role', $senderRole);
        }

        $tickets = $query->paginate(10)->withQueryString();

        $openCount = Ticket::where('status', 'open')->count();
        $inProgressCount = Ticket::where('status', 'in_progress')->count();
        $resolvedCount = Ticket::where('status', 'resolved')->count();
        $closedCount = Ticket::where('status', 'closed')->count();

        return view('admin.tickets.index', compact(
            'tickets',
            'status',
            'senderRole',
            'openCount',
            'inProgressCount',
            'resolvedCount',
            'closedCount'
        ));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'handler', 'replies.user']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'min:2'],
        ], [
            'message.required' => 'Pesan balasan wajib diisi.',
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Auto update status to in_progress if currently open, and assign handled_by if empty
        $updates = [];
        if (! $ticket->handled_by) {
            $updates['handled_by'] = Auth::id();
        }
        if ($ticket->status === 'open') {
            $updates['status'] = 'in_progress';
        }

        if (! empty($updates)) {
            $ticket->update($updates);
        }

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Balasan berhasil dikirim.');
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
        ]);

        $updates = [
            'status' => $request->status,
        ];

        if (! $ticket->handled_by) {
            $updates['handled_by'] = Auth::id();
        }

        $ticket->update($updates);

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', "Status tiket berhasil diubah menjadi '{$request->status}'.");
    }
}

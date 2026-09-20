<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService
    ) {}

    public function index(Request $request): View
    {
        $admin = $request->user();

        $conversations = Conversation::where('type', 'staff_admin')
            ->where(function ($q) use ($admin) {
                $q->where('user_one_id', $admin->id)->orWhere('user_two_id', $admin->id);
            })
            ->with(['userOne.store', 'userTwo.store', 'messages' => fn($q) => $q->latest()->take(1)])
            ->orderByDesc('last_message_at')
            ->get();

        return view('admin.chat.index', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => collect(),
        ]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $admin = $request->user();

        abort_unless($conversation->isParticipant($admin->id), 403, 'Akses percakapan ditolak.');
        abort_unless($conversation->type === 'staff_admin', 403, 'Admin hanya memiliki akses percakapan staff_admin.');
        abort_unless($admin->role === 'admin', 403, 'Role tidak sesuai.');

        // Mark unread messages as read
        $this->chatService->markAsRead($conversation, $admin);

        $messages = $conversation->messages()
            ->orderBy('id', 'asc')
            ->get();

        $conversations = Conversation::where('type', 'staff_admin')
            ->where(function ($q) use ($admin) {
                $q->where('user_one_id', $admin->id)->orWhere('user_two_id', $admin->id);
            })
            ->with(['userOne.store', 'userTwo.store', 'messages' => fn($q) => $q->latest()->take(1)])
            ->orderByDesc('last_message_at')
            ->get();

        return view('admin.chat.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function start(Request $request, User $staff): RedirectResponse
    {
        $admin = $request->user();

        abort_unless($staff->role === 'staff', 404, 'Staf tidak ditemukan.');
        abort_unless($admin->role === 'admin', 403, 'Aksi khusus administrator.');

        $conversation = $this->chatService->findOrCreateStaffAdmin($staff, $admin);

        return redirect()->route('admin.chat.show', $conversation);
    }

    public function send(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $admin = $request->user();

        abort_unless($conversation->isParticipant($admin->id), 403, 'Akses percakapan ditolak.');
        abort_unless($conversation->type === 'staff_admin', 403, 'Admin hanya memiliki akses percakapan staff_admin.');
        abort_unless($admin->role === 'admin', 403, 'Role tidak sesuai.');

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $this->chatService->send($conversation, $admin, $request->input('message'));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'is_me' => true,
                    'time' => $message->created_at ? $message->created_at->format('H:i') : '',
                ],
            ]);
        }

        return redirect()->route('admin.chat.show', $conversation);
    }

    public function poll(Request $request, Conversation $conversation): JsonResponse
    {
        $admin = $request->user();

        abort_unless($conversation->isParticipant($admin->id), 403, 'Akses percakapan ditolak.');
        abort_unless($conversation->type === 'staff_admin', 403, 'Admin hanya memiliki akses percakapan staff_admin.');
        abort_unless($admin->role === 'admin', 403, 'Role tidak sesuai.');

        $lastId = (int) ($request->input('after_id') ?? $request->input('last_id', 0));

        $this->chatService->markAsRead($conversation, $admin);
        $newMessages = $this->chatService->newMessagesSince($conversation, $lastId);

        $formatted = $newMessages->map(function ($msg) use ($admin) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'is_me' => $msg->sender_id === $admin->id,
                'time' => $msg->created_at ? $msg->created_at->format('H:i') : '',
            ];
        });

        return response()->json([
            'messages' => $formatted,
        ]);
    }
}

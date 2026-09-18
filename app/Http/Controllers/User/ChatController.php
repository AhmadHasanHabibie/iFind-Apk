<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Store;
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

    public function start(Request $request, Store $store): RedirectResponse
    {
        abort_if(! $store->is_active || $store->status !== 'approved', 404, 'Toko tidak aktif.');

        $user = $request->user();
        abort_unless($user->role === 'user', 403, 'Aksi ini khusus customer.');

        $conversation = $this->chatService->findOrCreateUserStaff($user, $store);

        return redirect()->route('user.chat.show', $conversation);
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = Conversation::where('type', 'user_staff')
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->with([
                'store',
                'userOne',
                'userTwo',
                'messages' => fn($q) => $q->latest()->take(1),
            ])
            ->orderByDesc('last_message_at')
            ->get();

        return view('user.chat.index', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => collect(),
        ]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user = $request->user();

        // Validasi partisipan & role kesesuaian
        abort_unless($conversation->isParticipant($user->id), 403, 'Akses percakapan ditolak.');
        abort_unless($conversation->type === 'user_staff', 403, 'Tipe percakapan tidak valid untuk user.');
        abort_unless(in_array($user->role, ['user', 'staff']), 403, 'Role tidak sesuai.');

        // Tandai pesan sebagai telah dibaca
        $this->chatService->markAsRead($conversation, $user);

        $messages = $conversation->messages()
            ->orderBy('id', 'asc')
            ->get();

        $conversations = Conversation::where('type', 'user_staff')
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->with([
                'store',
                'userOne',
                'userTwo',
                'messages' => fn($q) => $q->latest()->take(1),
            ])
            ->orderByDesc('last_message_at')
            ->get();

        return view('user.chat.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        abort_unless($conversation->isParticipant($user->id), 403, 'Akses ditolak.');
        abort_unless($conversation->type === 'user_staff', 403, 'Tipe percakapan tidak valid.');
        abort_unless($user->role === 'user', 403, 'Role tidak sesuai.');

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $this->chatService->send($conversation, $user, $request->input('message'));

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

        return redirect()->route('user.chat.show', $conversation);
    }

    public function poll(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        abort_unless($conversation->isParticipant($user->id), 403, 'Akses ditolak.');
        abort_unless($conversation->type === 'user_staff', 403, 'Tipe percakapan tidak valid.');
        abort_unless($user->role === 'user', 403, 'Role tidak sesuai.');

        $lastId = (int) ($request->input('after_id') ?? $request->input('last_id', 0));

        $this->chatService->markAsRead($conversation, $user);
        $newMessages = $this->chatService->newMessagesSince($conversation, $lastId);

        $formatted = $newMessages->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'is_me' => $msg->sender_id === $user->id,
                'time' => $msg->created_at ? $msg->created_at->format('H:i') : '',
            ];
        });

        return response()->json([
            'messages' => $formatted,
        ]);
    }
}

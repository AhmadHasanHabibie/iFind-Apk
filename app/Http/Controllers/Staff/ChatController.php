<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $store = $user->store;
        $tab = $request->input('tab', 'customer'); // 'customer' or 'admin'

        $customerConversations = Conversation::where('store_id', $store->id)
            ->with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->take(1)])
            ->orderByDesc('last_message_at')
            ->get();

        $adminConversations = Conversation::whereNull('store_id')
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->take(1)])
            ->orderByDesc('last_message_at')
            ->get();

        return view('staff.chat.index', [
            'tab' => $tab,
            'customerConversations' => $customerConversations,
            'adminConversations' => $adminConversations,
            'activeConversation' => null,
            'messages' => collect(),
            'formattedMessages' => [],
        ]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user = $request->user();
        $store = $user->store;

        // Validasi akses percakapan
        $isParticipant = ($conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id);
        $isStoreOwner = ($conversation->store_id === $store->id);
        abort_if(! $isParticipant && ! $isStoreOwner, 403, 'Akses percakapan ditolak.');

        $tab = $conversation->store_id ? 'customer' : 'admin';

        // Tandai pesan dari lawan bicara sebagai telah dibaca
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('id', 'asc')
            ->get();

        $formattedMessages = $messages->map(function ($m) use ($user) {
            return [
                'id' => $m->id,
                'message' => $m->message,
                'sender_id' => $m->sender_id,
                'is_me' => $m->sender_id === $user->id,
                'time' => $m->created_at->format('H:i'),
            ];
        })->values();

        $customerConversations = Conversation::where('store_id', $store->id)
            ->with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->take(1)])
            ->orderByDesc('last_message_at')
            ->get();

        $adminConversations = Conversation::whereNull('store_id')
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->take(1)])
            ->orderByDesc('last_message_at')
            ->get();

        return view('staff.chat.index', [
            'tab' => $tab,
            'customerConversations' => $customerConversations,
            'adminConversations' => $adminConversations,
            'activeConversation' => $conversation,
            'messages' => $messages,
            'formattedMessages' => $formattedMessages,
        ]);
    }

    public function send(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        $isParticipant = ($conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id);
        $isStoreOwner = ($conversation->store_id === $store->id);
        abort_if(! $isParticipant && ! $isStoreOwner, 403, 'Akses ditolak.');

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'is_me' => true,
                    'time' => $message->created_at->format('H:i'),
                ],
            ]);
        }

        return redirect()->route('staff.chat.show', $conversation);
    }

    public function poll(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        $isParticipant = ($conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id);
        $isStoreOwner = ($conversation->store_id === $store->id);
        abort_if(! $isParticipant && ! $isStoreOwner, 403, 'Akses ditolak.');

        $lastId = (int) $request->input('last_id', 0);

        $newMessages = $conversation->messages()
            ->where('id', '>', $lastId)
            ->with('sender')
            ->orderBy('id', 'asc')
            ->get();

        // Tandai pesan dari lawan bicara yang baru diambil sebagai read
        $conversation->messages()
            ->where('id', '>', $lastId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $formatted = $newMessages->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name ?? 'Pengguna',
                'is_me' => $msg->sender_id === $user->id,
                'time' => $msg->created_at->format('H:i'),
            ];
        });

        return response()->json([
            'messages' => $formatted,
        ]);
    }

    /**
     * Hubungi Admin via Chat
     * Catatan asumsi: 1 percakapan staf-admin = 2 partisipan tetap.
     * Jika nanti ada banyak admin, staf diarahkan ke akun admin dengan ID paling awal.
     */
    public function contactAdmin(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Cari admin pertama di sistem
        $admin = User::where('role', 'admin')->orderBy('id', 'asc')->first();
        abort_if(! $admin, 404, 'Akun admin tidak ditemukan di sistem.');

        // Cek apakah sudah pernah ada percakapan langsung staf & admin
        $conversation = Conversation::whereNull('store_id')
            ->where(function ($q) use ($user, $admin) {
                $q->where(function ($sub) use ($user, $admin) {
                    $sub->where('user_one_id', $user->id)->where('user_two_id', $admin->id);
                })->orWhere(function ($sub) use ($user, $admin) {
                    $sub->where('user_one_id', $admin->id)->where('user_two_id', $user->id);
                });
            })
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $user->id,
                'user_two_id' => $admin->id,
                'store_id' => null,
                'last_message_at' => now(),
            ]);
        }

        return redirect()->route('staff.chat.show', ['conversation' => $conversation->id, 'tab' => 'admin']);
    }
}

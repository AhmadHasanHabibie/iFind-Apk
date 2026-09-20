<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
        $user = $request->user();
        $store = $user->store;
        $tab = $request->input('tab', 'customer'); // 'customer' or 'admin'

        $customerConversations = $store
            ? Conversation::where('type', 'user_staff')
                ->where('store_id', $store->id)
                ->with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->take(1)])
                ->orderByDesc('last_message_at')
                ->get()
            : collect();

        $adminConversations = Conversation::where('type', 'staff_admin')
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
        ]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user = $request->user();
        $store = $user->store;

        // Validasi partisipan & kesesuaian role
        abort_unless($conversation->isParticipant($user->id), 403, 'Akses percakapan ditolak.');

        if ($conversation->type === 'user_staff') {
            abort_unless(in_array($user->role, ['user', 'staff']), 403, 'Role tidak diizinkan.');
            abort_unless($store && $conversation->store_id === $store->id, 403, 'Bukan percakapan toko Anda.');
            $tab = 'customer';
        } elseif ($conversation->type === 'staff_admin') {
            abort_unless(in_array($user->role, ['staff', 'admin']), 403, 'Role tidak diizinkan.');
            $tab = 'admin';
        } else {
            abort(403, 'Tipe percakapan tidak valid.');
        }

        // Tandai pesan sebagai dibaca
        $this->chatService->markAsRead($conversation, $user);

        $messages = $conversation->messages()
            ->orderBy('id', 'asc')
            ->get();

        $customerConversations = $store
            ? Conversation::where('type', 'user_staff')
                ->where('store_id', $store->id)
                ->with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->take(1)])
                ->orderByDesc('last_message_at')
                ->get()
            : collect();

        $adminConversations = Conversation::where('type', 'staff_admin')
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
        ]);
    }

    public function send(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        abort_unless($conversation->isParticipant($user->id), 403, 'Akses percakapan ditolak.');

        if ($conversation->type === 'user_staff') {
            abort_unless(in_array($user->role, ['user', 'staff']), 403);
            abort_unless($store && $conversation->store_id === $store->id, 403);
        } elseif ($conversation->type === 'staff_admin') {
            abort_unless(in_array($user->role, ['staff', 'admin']), 403);
        } else {
            abort(403);
        }

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

        return redirect()->route('staff.chat.show', $conversation);
    }

    public function poll(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        abort_unless($conversation->isParticipant($user->id), 403, 'Akses percakapan ditolak.');

        if ($conversation->type === 'user_staff') {
            abort_unless(in_array($user->role, ['user', 'staff']), 403);
            abort_unless($store && $conversation->store_id === $store->id, 403);
        } elseif ($conversation->type === 'staff_admin') {
            abort_unless(in_array($user->role, ['staff', 'admin']), 403);
        } else {
            abort(403);
        }

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

    /**
     * Staf memulai chat ke Customer yang pernah memiliki minimal 1 booking di tokonya.
     */
    public function startUserChat(Request $request, User $user): RedirectResponse
    {
        $staff = $request->user();
        $store = $staff->store;

        abort_unless($store, 403, 'Anda belum memiliki profil toko.');
        abort_unless($user->role === 'user', 404, 'Pelanggan tidak ditemukan.');

        // Batasan keamanan: Staf hanya boleh chat ke User yang pernah memiliki minimal 1 booking
        abort_unless(
            Booking::where('store_id', $store->id)->where('user_id', $user->id)->exists(),
            403,
            'Anda hanya bisa menghubungi pelanggan yang pernah booking di toko Anda.'
        );

        $conversation = $this->chatService->findOrCreateUserStaff($user, $store);

        return redirect()->route('staff.chat.show', [
            'conversation' => $conversation->id,
            'tab' => 'customer',
        ]);
    }

    /**
     * Hubungi Admin via Chat (staff_admin)
     */
    public function contactAdmin(Request $request): RedirectResponse
    {
        $user = $request->user();

        $admin = User::where('role', 'admin')->orderBy('id', 'asc')->first();
        abort_if(! $admin, 404, 'Akun admin tidak ditemukan di sistem.');

        $conversation = $this->chatService->findOrCreateStaffAdmin($user, $admin);

        return redirect()->route('staff.chat.show', [
            'conversation' => $conversation->id,
            'tab' => 'admin',
        ]);
    }
}

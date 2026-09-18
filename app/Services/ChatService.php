<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Collection;

class ChatService
{
    public function findOrCreateUserStaff(User $user, Store $store): Conversation
    {
        $staffId = $store->user_id;

        $conversation = Conversation::where('type', 'user_staff')
            ->where('store_id', $store->id)
            ->where(function ($q) use ($user, $staffId) {
                $q->where(function ($sub) use ($user, $staffId) {
                    $sub->where('user_one_id', $user->id)->where('user_two_id', $staffId);
                })->orWhere(function ($sub) use ($user, $staffId) {
                    $sub->where('user_one_id', $staffId)->where('user_two_id', $user->id);
                });
            })->first();

        return $conversation ?? Conversation::create([
            'type' => 'user_staff',
            'user_one_id' => $user->id,
            'user_two_id' => $staffId,
            'store_id' => $store->id,
            'last_message_at' => now(),
        ]);
    }

    public function findOrCreateStaffAdmin(User $staff, User $admin): Conversation
    {
        $conversation = Conversation::where('type', 'staff_admin')
            ->where(function ($q) use ($staff, $admin) {
                $q->where(function ($sub) use ($staff, $admin) {
                    $sub->where('user_one_id', $staff->id)->where('user_two_id', $admin->id);
                })->orWhere(function ($sub) use ($staff, $admin) {
                    $sub->where('user_one_id', $admin->id)->where('user_two_id', $staff->id);
                });
            })->first();

        return $conversation ?? Conversation::create([
            'type' => 'staff_admin',
            'user_one_id' => $staff->id,
            'user_two_id' => $admin->id,
            'store_id' => null,
            'last_message_at' => now(),
        ]);
    }

    public function send(Conversation $conversation, User $sender, string $message): ChatMessage
    {
        abort_unless($conversation->isParticipant($sender->id), 403, 'Anda bukan partisipan percakapan ini.');

        $chatMessage = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'message' => trim($message),
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return $chatMessage;
    }

    public function markAsRead(Conversation $conversation, User $reader): void
    {
        $conversation->messages()
            ->where('sender_id', '!=', $reader->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function newMessagesSince(Conversation $conversation, int $lastId): Collection
    {
        return $conversation->messages()
            ->where('id', '>', $lastId)
            ->with('sender')
            ->orderBy('id', 'asc')
            ->get();
    }
}

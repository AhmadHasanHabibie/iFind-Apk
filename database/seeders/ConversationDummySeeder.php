<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationDummySeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('role', 'staff')->where('verification_status', 'approved')->first();
        $user = User::where('role', 'user')->first();
        $admin = User::where('role', 'admin')->first();
        $store = Store::first();

        if (! $staff || ! $user || ! $admin || ! $store) {
            return;
        }

        // 1. Percakapan Staf & Pelanggan (store_id != null)
        $customerConv = Conversation::updateOrCreate(
            [
                'user_one_id' => $user->id,
                'user_two_id' => $staff->id,
                'store_id' => $store->id,
            ],
            [
                'type' => 'user_staff',
                'last_message_at' => now()->subMinutes(15),
            ]
        );

        if ($customerConv->messages()->count() === 0) {
            ChatMessage::create([
                'conversation_id' => $customerConv->id,
                'sender_id' => $user->id,
                'message' => 'Halo kak, apakah di Titik Temu tersedia papan tulis kecil (whiteboard) yang bisa dipinjam untuk diskusi?',
                'is_read' => true,
                'created_at' => now()->subHours(1),
            ]);

            ChatMessage::create([
                'conversation_id' => $customerConv->id,
                'sender_id' => $staff->id,
                'message' => 'Halo Kak Maya! Ya, kami menyediakan 2 unit standing whiteboard dan spidol gratis untuk reservasi kelompok di lantai 2.',
                'is_read' => true,
                'created_at' => now()->subMinutes(45),
            ]);

            ChatMessage::create([
                'conversation_id' => $customerConv->id,
                'sender_id' => $user->id,
                'message' => 'Wah mantap sekali kak, saya sudah buat booking untuk nanti malam ya.',
                'is_read' => true,
                'created_at' => now()->subMinutes(30),
            ]);

            ChatMessage::create([
                'conversation_id' => $customerConv->id,
                'sender_id' => $staff->id,
                'message' => 'Baik kak, booking sudah kami konfirmasi. Ditunggu kedatangannya ya!',
                'is_read' => true,
                'created_at' => now()->subMinutes(15),
            ]);
        }

        // 2. Percakapan Staf & Admin (store_id = null)
        $adminConv = Conversation::updateOrCreate(
            [
                'user_one_id' => $staff->id,
                'user_two_id' => $admin->id,
                'store_id' => null,
            ],
            [
                'type' => 'staff_admin',
                'last_message_at' => now()->subHours(2),
            ]
        );

        if ($adminConv->messages()->count() === 0) {
            ChatMessage::create([
                'conversation_id' => $adminConv->id,
                'sender_id' => $staff->id,
                'message' => 'Selamat siang Admin iFind, apakah ada panduan untuk resolusi banner foto etalase yang paling optimal?',
                'is_read' => true,
                'created_at' => now()->subHours(3),
            ]);

            ChatMessage::create([
                'conversation_id' => $adminConv->id,
                'sender_id' => $admin->id,
                'message' => 'Halo Mitra iFind, rasio aspek 16:9 dengan resolusi minimal 1280x720 px sangat disarankan agar foto terlihat jernih di aplikasi dan browser.',
                'is_read' => true,
                'created_at' => now()->subHours(2),
            ]);
        }
    }
}

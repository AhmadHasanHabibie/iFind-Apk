<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TicketDummySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1 Akun Customer (User) dummy
        $customer = User::updateOrCreate(
            ['email' => 'maya.user@ifind.id'],
            [
                'name' => 'Maya Anggraini',
                'phone' => '081298765432',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $staff = User::where('role', 'staff')->where('verification_status', 'approved')->first()
                 ?? User::where('role', 'staff')->first();

        // Ticket 1: Open (dari user)
        Ticket::updateOrCreate(
            ['ticket_code' => 'TCK-202609-001'],
            [
                'user_id' => $customer->id,
                'sender_role' => 'user',
                'subject' => 'Kendala Pembatalan Jadwal Booking',
                'description' => 'Halo admin iFind, saya ingin mengubah jadwal booking tempat belajar kelompok untuk besok sore. Apakah proses ini bisa dibantu oleh admin atau harus via toko langsung?',
                'category' => 'booking',
                'status' => 'open',
                'handled_by' => null,
                'created_at' => now()->subHours(3),
            ]
        );

        // Ticket 2: In Progress (dari staf)
        $t2 = Ticket::updateOrCreate(
            ['ticket_code' => 'TCK-202609-002'],
            [
                'user_id' => $staff ? $staff->id : $customer->id,
                'sender_role' => 'staff',
                'subject' => 'Pertanyaan Pengaturan Jam Operasional Toko',
                'description' => 'Selamat siang admin, bagaimana cara mengatur jam buka khusus pada hari libur nasional atau cuti bersama di dashboard staf?',
                'category' => 'technical',
                'status' => 'in_progress',
                'handled_by' => $admin ? $admin->id : null,
                'created_at' => now()->subHours(8),
            ]
        );

        if ($admin && $t2->replies()->count() === 0) {
            TicketReply::create([
                'ticket_id' => $t2->id,
                'user_id' => $admin->id,
                'message' => 'Halo Mitra iFind, saat ini Anda dapat menonaktifkan slot hari bersangkutan pada menu Manajemen Slot atau menyesuaikan jam buka di pengaturan toko. Tim kami sedang menyiapkan fitur kalender libur otomatis.',
                'created_at' => now()->subHours(6),
            ]);
        }

        // Ticket 3: Resolved (dari user dengan thread balasan)
        $t3 = Ticket::updateOrCreate(
            ['ticket_code' => 'TCK-202609-003'],
            [
                'user_id' => $customer->id,
                'sender_role' => 'user',
                'subject' => 'Pembaruan Nomor WhatsApp Profil',
                'description' => 'Nomor WhatsApp lama saya sudah tidak aktif, mohon panduan untuk mengganti nomor baru pada profil.',
                'category' => 'account',
                'status' => 'resolved',
                'handled_by' => $admin ? $admin->id : null,
                'created_at' => now()->subDays(1),
            ]
        );

        if ($admin && $t3->replies()->count() === 0) {
            TicketReply::create([
                'ticket_id' => $t3->id,
                'user_id' => $admin->id,
                'message' => 'Halo Kak Maya, nomor WhatsApp dapat Anda ubah langsung melalui halaman Pengaturan Profil akun Anda. Sistem telah memverifikasi perubahan tersebut.',
                'created_at' => now()->subHours(20),
            ]);

            TicketReply::create([
                'ticket_id' => $t3->id,
                'user_id' => $customer->id,
                'message' => 'Sudah bisa sekarang kak, terima kasih banyak bantuannya!',
                'created_at' => now()->subHours(18),
            ]);
        }

        // Ticket 4: Closed (dari staff)
        Ticket::updateOrCreate(
            ['ticket_code' => 'TCK-202609-004'],
            [
                'user_id' => $staff ? $staff->id : $customer->id,
                'sender_role' => 'staff',
                'subject' => 'Informasi Penambahan Foto Etalase Toko',
                'description' => 'Berapa jumlah maksimal foto etalase yang dapat diunggah untuk satu toko kafe?',
                'category' => 'other',
                'status' => 'closed',
                'handled_by' => $admin ? $admin->id : null,
                'created_at' => now()->subDays(3),
            ]
        );
    }
}

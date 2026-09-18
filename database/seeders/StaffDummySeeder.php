<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffDummySeeder extends Seeder
{
    public function run(): void
    {
        $staffs = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.staff@ifind.id',
                'phone' => '081234567801',
                'role' => 'staff',
                'is_active' => false,
                'verification_status' => 'pending',
                'verification_note' => null,
                'verified_at' => null,
            ],
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti.staff@ifind.id',
                'phone' => '081234567802',
                'role' => 'staff',
                'is_active' => false,
                'verification_status' => 'pending',
                'verification_note' => null,
                'verified_at' => null,
            ],
            [
                'name' => 'Arief Wicaksono',
                'email' => 'arief.staff@ifind.id',
                'phone' => '081234567803',
                'role' => 'staff',
                'is_active' => true,
                'verification_status' => 'approved',
                'verification_note' => null,
                'verified_at' => now()->subDays(2),
            ],
            [
                'name' => 'Dani Prasetyo',
                'email' => 'dani.staff@ifind.id',
                'phone' => '081234567804',
                'role' => 'staff',
                'is_active' => false,
                'verification_status' => 'rejected',
                'verification_note' => 'Foto identitas usaha tidak jelas dan nomor telepon tidak dapat dihubungi saat verifikasi.',
                'verified_at' => now()->subDay(),
            ],
        ];

        foreach ($staffs as $staff) {
            User::updateOrCreate(
                ['email' => $staff['email']],
                array_merge($staff, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}

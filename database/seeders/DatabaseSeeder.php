<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Seeder
        User::updateOrCreate(
            ['email' => 'admin@ifind.id'],
            [
                'name' => 'Administrator iFind',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Staff Toko Seeder
        User::updateOrCreate(
            ['email' => 'staff@ifind.id'],
            [
                'name' => 'Staf Toko Kopi Titik Temu',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'institution' => 'Kopi Titik Temu Cabang 1',
                'email_verified_at' => now(),
            ]
        );

        // User (Siswa) Seeder
        User::updateOrCreate(
            ['email' => 'user@ifind.id'],
            [
                'name' => 'Rifqi Siswa',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'institution' => 'SMK Negeri 1 Jakarta',
                'email_verified_at' => now(),
            ]
        );
    }
}

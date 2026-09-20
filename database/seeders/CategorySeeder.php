<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Coffee Shop',
                'icon' => 'fa-solid fa-mug-saucer',
                'description' => 'Kafe nyaman dengan seduhan kopi nikmat dan suasana kondusif untuk kerja atau tugas.',
                'is_active' => true,
            ],
            [
                'name' => 'Coworking Space',
                'icon' => 'fa-solid fa-laptop-code',
                'description' => 'Ruang kerja bersama dengan internet berkecepatan tinggi, meja kerja ergonomis, dan fasilitas kantor.',
                'is_active' => true,
            ],
            [
                'name' => 'Perpustakaan',
                'icon' => 'fa-solid fa-book-open',
                'description' => 'Ruang baca tenang dengan koleksi buku lengkap dan suasana hening untuk fokus penuh.',
                'is_active' => true,
            ],
            [
                'name' => 'Restoran & Resto Kafe',
                'icon' => 'fa-solid fa-utensils',
                'description' => 'Tempat makan dengan menu lengkap sekaligus spot nyaman untuk diskusi kelompok atau rapat santai.',
                'is_active' => true,
            ],
            [
                'name' => 'Taman Baca',
                'icon' => 'fa-solid fa-tree',
                'description' => 'Area ruang terbuka hijau dan semi-outdoor ramah literasi untuk belajar sambil menikmati udara segar.',
                'is_active' => true,
            ],
            [
                'name' => 'Study Hub',
                'icon' => 'fa-solid fa-graduation-cap',
                'description' => 'Ruang belajar privat atau kelompok khusus pelajar & mahasiswa dengan papan tulis dan stopkontak per meja.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'description' => $cat['description'],
                    'is_active' => $cat['is_active'],
                ]
            );
        }
    }
}

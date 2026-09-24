<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Store;
use Illuminate\Database\Seeder;

class MenuDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all();

        $sampleMenus = [
            [
                'name' => 'Kopi Susu Gula Aren Siswa',
                'category' => 'minuman',
                'price' => 12000,
                'description' => 'Espresso robusta dengan susu segar creamy dan sirup gula aren asli khas lokal.',
                'is_available' => true,
                'is_recommended' => true,
            ],
            [
                'name' => 'Ice Americano / Cold Brew',
                'category' => 'minuman',
                'price' => 10000,
                'description' => 'Kopi hitam dingin segar penambah fokus nugas dan belajar kelompok.',
                'is_available' => true,
                'is_recommended' => false,
            ],
            [
                'name' => 'Matcha Latte Ice',
                'category' => 'minuman',
                'price' => 15000,
                'description' => 'Bubuk matcha jepang dipadu susu hangat/dingin yang manis lembut.',
                'is_available' => true,
                'is_recommended' => false,
            ],
            [
                'name' => 'Nasi Goreng Spesial i-Find',
                'category' => 'makanan',
                'price' => 18000,
                'description' => 'Nasi goreng bumbu rempah lengkap dengan telur mata sapi, sosis, dan kerupuk.',
                'is_available' => true,
                'is_recommended' => true,
            ],
            [
                'name' => 'Mie Goreng Dok-Dok Nyemek',
                'category' => 'makanan',
                'price' => 14000,
                'description' => 'Mie kuah nyemek gurih pedas khas warung kopi dengan taburan daun bawang dan telur.',
                'is_available' => true,
                'is_recommended' => false,
            ],
            [
                'name' => 'Kentang Goreng Crispy Mayo',
                'category' => 'snack',
                'price' => 12000,
                'description' => 'French fries renyah bertabur bumbu keju/bbq disajikan dengan saus sambal dan mayones.',
                'is_available' => true,
                'is_recommended' => false,
            ],
            [
                'name' => 'Roti Bakar Cokelat Keju Lumer',
                'category' => 'snack',
                'price' => 13000,
                'description' => 'Roti bakar empuk isi limpahan meses cokelat dan parutan keju cheddar gurih.',
                'is_available' => true,
                'is_recommended' => false,
            ],
            [
                'name' => 'Paket Mabar: 2 Kopi + 1 Snack Platter',
                'category' => 'paket_hemat',
                'price' => 30000,
                'description' => 'Paket hemat untuk 2 orang: 2 Kopi Susu Aren + 1 Porsi Cireng Crispy / Kentang.',
                'is_available' => true,
                'is_recommended' => true,
            ],
        ];

        foreach ($stores as $store) {
            // Hindari duplikasi jika sudah ada menu
            if ($store->menus()->count() === 0) {
                foreach ($sampleMenus as $menuData) {
                    $store->menus()->create($menuData);
                }
            }
        }
    }
}

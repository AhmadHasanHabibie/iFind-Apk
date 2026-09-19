<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Facility;
use App\Models\Store;
use App\Models\StorePhoto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdditionalStaffAndStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Catatan: Seeder ini membuat 3 akun staf langsung berstatus 'approved' & 'is_active = true'
     * sebagai shortcut kebutuhan seeding data pengujian User, bukan representasi alur registrasi asli.
     */
    public function run(): void
    {
        $coworkingCategory = Category::where('slug', 'coworking-space')->first() ?? Category::first();
        $libraryCategory = Category::where('slug', 'perpustakaan')->first() ?? Category::first();
        $restoCategory = Category::where('slug', 'restoran-resto-kafe')->first() ?? Category::first();

        $openingHoursDefault = [
            'monday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'tuesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'wednesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'thursday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'friday' => ['open' => '08:00', 'close' => '23:00', 'is_closed' => false],
            'saturday' => ['open' => '09:00', 'close' => '23:00', 'is_closed' => false],
            'sunday' => ['open' => '09:00', 'close' => '21:00', 'is_closed' => false],
        ];

        $storesData = [
            [
                'staff' => [
                    'name' => 'Dimas Anggara',
                    'email' => 'dimas.staff@ifind.id',
                    'phone' => '081234567805',
                ],
                'store' => [
                    'category_id' => $coworkingCategory ? $coworkingCategory->id : 1,
                    'name' => 'Kolektif Space & Roastery',
                    'slug' => 'kolektif-space-roastery',
                    'description' => 'Coworking space modern di pusat kota Bandung dengan suasana asri, kopi artisanal, dan meja kerja produktif.',
                    'address' => 'Jl. Riau No. 88, Citarum',
                    'city' => 'Bandung',
                    'latitude' => -6.9175000,
                    'longitude' => 107.6191000,
                    'phone' => '081234567805',
                    'average_rating' => 4.6,
                    'price_per_pax' => 35000,
                    'dp_percentage' => 100,
                    'payment_timeout_minutes' => 30,
                    'qris_image_path' => 'qris/dummy/kolektif-qris.png',
                ],
                'facilities_count' => 2,
                'photo' => 'stores/dummy/kolektif.jpg',
            ],
            [
                'staff' => [
                    'name' => 'Nabila Putri',
                    'email' => 'nabila.staff@ifind.id',
                    'phone' => '081234567806',
                ],
                'store' => [
                    'category_id' => $libraryCategory ? $libraryCategory->id : 1,
                    'name' => 'Ruang Literasi & Teahouse',
                    'slug' => 'ruang-literasi-teahouse',
                    'description' => 'Perpustakaan privat & kedai teh tenang di Kemang untuk membaca buku, belajar mandiri, dan fokus tanpa distraksi.',
                    'address' => 'Jl. Kemang Timur No. 12, Bangka',
                    'city' => 'Jakarta Selatan',
                    'latitude' => -6.2615000,
                    'longitude' => 106.8106000,
                    'phone' => '081234567806',
                    'average_rating' => 4.7,
                    'price_per_pax' => 20000,
                    'dp_percentage' => 50,
                    'payment_timeout_minutes' => 60,
                    'bank_name' => 'Bank Mandiri',
                    'bank_account_number' => '1370019283921',
                    'bank_account_holder' => 'Ruang Literasi Kemang',
                ],
                'facilities_count' => 3,
                'photo' => 'stores/dummy/ruang-literasi.jpg',
            ],
            [
                'staff' => [
                    'name' => 'Hendra Gunawan',
                    'email' => 'hendra.staff@ifind.id',
                    'phone' => '081234567807',
                ],
                'store' => [
                    'category_id' => $restoCategory ? $restoCategory->id : 1,
                    'name' => 'Sudut Temu Eatery & Cafe',
                    'slug' => 'sudut-temu-eatery-cafe',
                    'description' => 'Resto kafe luas ramah mahasiswa di Margonda dengan menu lezat, area ber-AC, dan meja kelompok lengkap colokan.',
                    'address' => 'Jl. Margonda Raya No. 200, Pondok Cina',
                    'city' => 'Depok',
                    'latitude' => -6.4025000,
                    'longitude' => 106.7942000,
                    'phone' => '081234567807',
                    'average_rating' => 4.2,
                    'price_per_pax' => 15000,
                    'dp_percentage' => 30,
                    'payment_timeout_minutes' => 60,
                    'bank_name' => 'Bank Rakyat Indonesia (BRI)',
                    'bank_account_number' => '012901002938501',
                    'bank_account_holder' => 'Sudut Temu Margonda',
                    'qris_image_path' => 'qris/dummy/sudut-temu-qris.png',
                ],
                'facilities_count' => 2,
                'photo' => 'stores/dummy/sudut-temu.jpg',
            ],
        ];

        foreach ($storesData as $item) {
            $staff = User::updateOrCreate(
                ['email' => $item['staff']['email']],
                [
                    'name' => $item['staff']['name'],
                    'phone' => $item['staff']['phone'],
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'is_active' => true,
                    'verification_status' => 'approved',
                    'verified_at' => now()->subDays(5),
                ]
            );

            $store = Store::updateOrCreate(
                ['user_id' => $staff->id],
                array_merge($item['store'], [
                    'opening_hours' => $openingHoursDefault,
                    'status' => 'approved',
                    'is_active' => true,
                ])
            );

            // Sync facilities
            $facilityIds = Facility::take($item['facilities_count'])->pluck('id');
            $store->facilities()->sync($facilityIds);

            // Create photo
            StorePhoto::updateOrCreate(
                ['store_id' => $store->id, 'photo_path' => $item['photo']],
                ['is_primary' => true]
            );
        }
    }
}

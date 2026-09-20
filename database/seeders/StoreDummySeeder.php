<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Facility;
use App\Models\Store;
use App\Models\StorePhoto;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreDummySeeder extends Seeder
{
    public function run(): void
    {
        $approvedStaff = User::where('role', 'staff')
            ->where('verification_status', 'approved')
            ->first();

        if (! $approvedStaff) {
            return;
        }

        $category = Category::where('slug', 'coffee-shop')->first()
            ?? Category::first();

        $openingHours = [
            'monday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'tuesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'wednesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'thursday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'friday' => ['open' => '08:00', 'close' => '23:00', 'is_closed' => false],
            'saturday' => ['open' => '09:00', 'close' => '23:00', 'is_closed' => false],
            'sunday' => ['open' => '09:00', 'close' => '21:00', 'is_closed' => false],
        ];

        $store = Store::updateOrCreate(
            ['user_id' => $approvedStaff->id],
            [
                'category_id' => $category ? $category->id : 1,
                'name' => 'Titik Temu Coffee & Study Space',
                'slug' => 'titik-temu-coffee-study-space',
                'description' => 'Tempat belajar bersama dan kafe tenang di kawasan Tebet dengan internet fiber optik, stopkontak per meja, dan sajian kopi spesialti.',
                'address' => 'Jl. Tebet Raya No. 45, Tebet Barat',
                'city' => 'Jakarta Selatan',
                'latitude' => -6.2255000,
                'longitude' => 106.8550000,
                'phone' => '081234567803',
                'opening_hours' => $openingHours,
                'status' => 'approved',
                'is_active' => true,
                'average_rating' => 4.8,
                'price_per_pax' => 25000,
                'dp_percentage' => 50,
                'payment_timeout_minutes' => 60,
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_account_number' => '8830912831',
                'bank_account_holder' => 'Titik Temu Coffee',
                'qris_image_path' => 'qris/dummy/titik-temu-qris.png',
            ]
        );

        // Attach 3 facilities
        $facilityIds = Facility::take(3)->pluck('id');
        $store->facilities()->sync($facilityIds);

        // Create 2 store photos dummy
        StorePhoto::updateOrCreate(
            ['store_id' => $store->id, 'photo_path' => 'stores/dummy/photo1.jpg'],
            ['is_primary' => true]
        );

        StorePhoto::updateOrCreate(
            ['store_id' => $store->id, 'photo_path' => 'stores/dummy/photo2.jpg'],
            ['is_primary' => false]
        );
    }
}

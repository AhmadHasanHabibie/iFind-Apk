<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReviewDummySeeder extends Seeder
{
    public function run(): void
    {
        $primaryStore = Store::first();
        if (! $primaryStore) {
            return;
        }

        // 1. Buat review untuk booking completed yang sudah ada dari BookingDummySeeder (#IF-BKG-105)
        $completedBooking = Booking::where('booking_code', 'IF-BKG-105')->first();
        if ($completedBooking && ! $completedBooking->review()->exists()) {
            Review::create([
                'booking_id' => $completedBooking->id,
                'user_id' => $completedBooking->user_id,
                'store_id' => $completedBooking->store_id,
                'rating' => 5,
                'comment' => 'Tempatnya sangat nyaman untuk kerja remote! WiFi kencang dan kopi arennya enak sekali.',
            ]);
        }

        // 2. Buat akun user kedua untuk menambah ulasan kedua pada toko pertama
        $secondUser = User::firstOrCreate(
            ['email' => 'rizky.user@ifind.id'],
            [
                'name' => 'Rizky Pratama',
                'phone' => '081234567812',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
            ]
        );

        $slot = $primaryStore->slots()->first();
        if ($slot) {
            $secondBooking = Booking::updateOrCreate(
                ['booking_code' => 'IFD-DUMMY-REV2'],
                [
                    'user_id' => $secondUser->id,
                    'store_id' => $primaryStore->id,
                    'slot_id' => $slot->id,
                    'booking_date' => now()->subDay()->toDateString(),
                    'seat_count' => 2,
                    'status' => 'completed',
                    'confirmed_at' => now()->subDay(),
                    'notes' => 'Diskusi skripsi',
                ]
            );

            if (! $secondBooking->review()->exists()) {
                Review::create([
                    'booking_id' => $secondBooking->id,
                    'user_id' => $secondUser->id,
                    'store_id' => $primaryStore->id,
                    'rating' => 4,
                    'comment' => 'Suasana kondusif, stopkontak banyak. Sedikit ramai di jam istirahat siang tapi tetap oke.',
                ]);
            }
        }

        // 3. Sinkronisasi average_rating toko pertama dengan data riil reviews
        $avgRating = round($primaryStore->reviews()->avg('rating') ?: 4.5, 1);
        $primaryStore->update([
            'average_rating' => $avgRating,
        ]);
    }
}

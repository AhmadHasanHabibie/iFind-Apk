<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingDummySeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();
        $user = User::where('role', 'user')->first();

        if (! $store || ! $user) {
            return;
        }

        $slots = $store->slots()->orderBy('date')->orderBy('start_time')->get();

        if ($slots->count() < 5) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        // 1. Pending (Hari ini)
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-101'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[0]->id,
                'booking_date' => $today,
                'seat_count' => 2,
                'status' => 'pending',
                'notes' => 'Tolong sediakan meja dekat colokan untuk 2 laptop.',
            ]
        );

        // 2. Pending (Besok)
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-102'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[1]->id,
                'booking_date' => $tomorrow,
                'seat_count' => 4,
                'status' => 'pending',
                'notes' => 'Diskusi kelompok tugas akhir.',
            ]
        );

        // 3. Confirmed (Hari ini)
        $b3 = Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-103'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[2]->id,
                'booking_date' => $today,
                'seat_count' => 3,
                'status' => 'confirmed',
                'confirmed_at' => now()->subHour(),
                'notes' => 'Datang sekitar jam 19.15.',
            ]
        );
        $slots[2]->booked_seats = 3;
        $slots[2]->save(); // SlotObserver will auto update status

        // 4. Rejected (Besok)
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-104'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[3]->id,
                'booking_date' => $tomorrow,
                'seat_count' => 2,
                'status' => 'rejected',
                'rejection_reason' => 'Area semi-outdoor sedang dalam pemeliharaan berkala pada jam tersebut.',
                'notes' => 'Ingin di area smoking / semi outdoor.',
            ]
        );

        // 5. Completed (Hari ini)
        $b5 = Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-105'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[1]->id,
                'booking_date' => $today,
                'seat_count' => 2,
                'status' => 'completed',
                'confirmed_at' => now()->subHours(4),
                'notes' => 'Terima kasih atas pelayanannya.',
            ]
        );
        $slots[1]->booked_seats = 2;
        $slots[1]->save();
    }
}

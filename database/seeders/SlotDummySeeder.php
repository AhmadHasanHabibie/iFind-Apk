<?php

namespace Database\Seeders;

use App\Models\Slot;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SlotDummySeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();

        if (! $store) {
            return;
        }

        $sessions = [
            ['start' => '09:00', 'end' => '11:00'],
            ['start' => '13:00', 'end' => '15:00'],
            ['start' => '19:00', 'end' => '21:00'],
        ];

        for ($day = 0; $day < 7; $day++) {
            $date = Carbon::today()->addDays($day)->toDateString();

            foreach ($sessions as $session) {
                Slot::firstOrCreate(
                    [
                        'store_id' => $store->id,
                        'date' => $date,
                        'start_time' => $session['start'],
                    ],
                    [
                        'end_time' => $session['end'],
                        'capacity' => 8,
                        'booked_seats' => 0,
                        'status' => 'available',
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Slot;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdditionalSlotSeeder extends Seeder
{
    public function run(): void
    {
        $additionalSlugs = [
            'kolektif-space-roastery',
            'ruang-literasi-teahouse',
            'sudut-temu-eatery-cafe',
        ];

        $stores = Store::whereIn('slug', $additionalSlugs)->get();

        $timeBlocks = [
            ['start' => '10:00:00', 'end' => '12:00:00', 'capacity' => 8],
            ['start' => '13:00:00', 'end' => '15:00:00', 'capacity' => 10],
            ['start' => '16:00:00', 'end' => '18:00:00', 'capacity' => 6],
            ['start' => '19:00:00', 'end' => '21:00:00', 'capacity' => 8],
        ];

        foreach ($stores as $storeIndex => $store) {
            for ($d = 0; $d < 3; $d++) {
                $targetDate = Carbon::today()->addDays($d)->toDateString();

                foreach ($timeBlocks as $blockIndex => $block) {
                    $slot = Slot::firstOrCreate(
                        [
                            'store_id' => $store->id,
                            'date' => $targetDate,
                            'start_time' => $block['start'],
                        ],
                        [
                            'end_time' => $block['end'],
                            'capacity' => $block['capacity'],
                            'booked_seats' => 0,
                            'status' => 'available',
                        ]
                    );

                    // Buat beberapa slot sengaja PENUH untuk menguji tampilan sisa kursi dan status penuh di sisi User
                    if ($blockIndex === 1 && $d === 0) {
                        $slot->booked_seats = $slot->capacity;
                        $slot->status = 'full';
                        $slot->save(); // Observer will maintain status
                    } elseif ($blockIndex === 2 && $d === 1 && $storeIndex === 0) {
                        $slot->booked_seats = $slot->capacity;
                        $slot->status = 'full';
                        $slot->save();
                    }
                }
            }
        }
    }
}

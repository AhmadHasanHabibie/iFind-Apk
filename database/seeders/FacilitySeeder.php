<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            ['name' => 'WiFi Gratis', 'icon' => 'fa-solid fa-wifi'],
            ['name' => 'Colokan Listrik', 'icon' => 'fa-solid fa-plug'],
            ['name' => 'AC', 'icon' => 'fa-solid fa-snowflake'],
            ['name' => 'Ruang Meeting', 'icon' => 'fa-solid fa-people-roof'],
            ['name' => 'Mushola', 'icon' => 'fa-solid fa-mosque'],
            ['name' => 'Parkir Luas', 'icon' => 'fa-solid fa-square-parking'],
        ];

        foreach ($facilities as $facility) {
            Facility::updateOrCreate(
                ['name' => $facility['name']],
                ['icon' => $facility['icon']]
            );
        }
    }
}

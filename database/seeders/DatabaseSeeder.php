<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            FacilitySeeder::class,
            StaffDummySeeder::class,
            TicketDummySeeder::class,
            StoreDummySeeder::class,
            SlotDummySeeder::class,
            BookingDummySeeder::class,
            ConversationDummySeeder::class,
        ]);
    }
}

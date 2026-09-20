<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('awaiting_payment','pending_verification','confirmed','rejected_invalid_payment','rejected_store_full','refunded','cancelled_expired','checked_in','completed') NOT NULL DEFAULT 'awaiting_payment'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','rejected','cancelled','completed') NOT NULL DEFAULT 'pending'");
        }
    }
};

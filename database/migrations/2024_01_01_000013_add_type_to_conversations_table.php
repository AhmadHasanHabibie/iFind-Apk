<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->enum('type', ['user_staff', 'staff_admin'])->after('id')->default('user_staff');
        });

        // Backfill data lama
        DB::table('conversations')->whereNull('store_id')->update(['type' => 'staff_admin']);
        DB::table('conversations')->whereNotNull('store_id')->update(['type' => 'user_staff']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

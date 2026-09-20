<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->decimal('price_per_pax', 10, 2)->nullable()->after('opening_hours');
            $table->unsignedTinyInteger('dp_percentage')->default(100)->after('price_per_pax');
            $table->unsignedInteger('payment_timeout_minutes')->default(60)->after('dp_percentage');
            $table->string('bank_name')->nullable()->after('payment_timeout_minutes');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_holder')->nullable()->after('bank_account_number');
            $table->string('qris_image_path')->nullable()->after('bank_account_holder');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'price_per_pax',
                'dp_percentage',
                'payment_timeout_minutes',
                'bank_name',
                'bank_account_number',
                'bank_account_holder',
                'qris_image_path',
            ]);
        });
    }
};

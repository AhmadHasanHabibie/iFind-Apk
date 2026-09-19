<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('price_per_pax_snapshot', 10, 2)->default(0)->after('seat_count');
            $table->unsignedTinyInteger('dp_percentage_snapshot')->default(100)->after('price_per_pax_snapshot');
            $table->decimal('total_amount', 10, 2)->default(0)->after('dp_percentage_snapshot');
            $table->decimal('amount_due', 10, 2)->default(0)->after('total_amount');
            $table->string('payment_proof_path')->nullable()->after('amount_due');
            $table->timestamp('payment_deadline')->nullable()->after('payment_proof_path');
            $table->timestamp('payment_uploaded_at')->nullable()->after('payment_deadline');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_uploaded_at');
            $table->foreignId('payment_verified_by')->nullable()->after('payment_verified_at')->constrained('users')->nullOnDelete();
            $table->text('payment_rejection_reason')->nullable()->after('payment_verified_by');
            $table->string('qr_token')->nullable()->unique()->after('payment_rejection_reason');
            $table->timestamp('checked_in_at')->nullable()->after('qr_token');
            $table->foreignId('checked_in_by')->nullable()->after('checked_in_at')->constrained('users')->nullOnDelete();
            $table->text('refund_note')->nullable()->after('checked_in_by');
            $table->timestamp('refunded_at')->nullable()->after('refund_note');
            $table->foreignId('refunded_by')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['payment_verified_by']);
            $table->dropForeign(['checked_in_by']);
            $table->dropForeign(['refunded_by']);

            $table->dropColumn([
                'price_per_pax_snapshot',
                'dp_percentage_snapshot',
                'total_amount',
                'amount_due',
                'payment_proof_path',
                'payment_deadline',
                'payment_uploaded_at',
                'payment_verified_at',
                'payment_verified_by',
                'payment_rejection_reason',
                'qr_token',
                'checked_in_at',
                'checked_in_by',
                'refund_note',
                'refunded_at',
                'refunded_by',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('remaining_payment_status', ['not_required', 'unpaid', 'pending_verification', 'paid'])
                ->default('not_required')
                ->after('amount_due');
            $table->enum('remaining_payment_method', ['qris_transfer', 'cash'])
                ->nullable()
                ->after('remaining_payment_status');
            $table->string('remaining_proof_path')
                ->nullable()
                ->after('remaining_payment_method');
            $table->timestamp('remaining_uploaded_at')
                ->nullable()
                ->after('remaining_proof_path');
            $table->timestamp('remaining_verified_at')
                ->nullable()
                ->after('remaining_uploaded_at');
            $table->foreignId('remaining_verified_by')
                ->nullable()
                ->after('remaining_verified_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->text('remaining_rejection_reason')
                ->nullable()
                ->after('remaining_verified_by');
            $table->decimal('remaining_amount_received', 10, 2)
                ->nullable()
                ->after('remaining_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['remaining_verified_by']);

            $table->dropColumn([
                'remaining_payment_status',
                'remaining_payment_method',
                'remaining_proof_path',
                'remaining_uploaded_at',
                'remaining_verified_at',
                'remaining_verified_by',
                'remaining_rejection_reason',
                'remaining_amount_received',
            ]);
        });
    }
};

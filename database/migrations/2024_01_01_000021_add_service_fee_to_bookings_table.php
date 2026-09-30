<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->after('dp_percentage_snapshot');
            $table->unsignedTinyInteger('service_fee_percentage')->default(5)->after('subtotal');
            $table->decimal('service_fee', 10, 2)->default(0)->after('service_fee_percentage');
        });

        // Update data booking yang sudah ada agar memiliki subtotal dan service_fee yang valid
        $bookings = DB::table('bookings')->get();
        foreach ($bookings as $b) {
            $pricePerPax = (float) $b->price_per_pax_snapshot;
            $seatCount = (int) $b->seat_count;
            $subtotal = $pricePerPax > 0 && $seatCount > 0 ? round($pricePerPax * $seatCount, 2) : (float) $b->total_amount;
            $serviceFee = round($subtotal * 0.05, 2);
            $totalAmount = round($subtotal + $serviceFee, 2);
            $dpPercentage = (int) ($b->dp_percentage_snapshot ?? 100);
            $amountDue = round($totalAmount * $dpPercentage / 100, 2);

            DB::table('bookings')->where('id', $b->id)->update([
                'subtotal' => $subtotal,
                'service_fee_percentage' => 5,
                'service_fee' => $serviceFee,
                'total_amount' => $totalAmount,
                'amount_due' => $amountDue,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'service_fee_percentage', 'service_fee']);
        });
    }
};

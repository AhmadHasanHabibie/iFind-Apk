<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis membatalkan booking yang belum dibayar setelah melewati payment deadline';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = 0;

        Booking::where('status', 'awaiting_payment')
            ->where('payment_deadline', '<', now())
            ->each(function ($booking) use (&$count) {
                DB::transaction(function () use ($booking) {
                    $booking->update(['status' => 'cancelled_expired']);
                    if ($booking->slot) {
                        $slot = $booking->slot;
                        $slot->booked_seats = max(0, $slot->booked_seats - $booking->seat_count);
                        $slot->save(); // Trigger SlotObserver to update status from full to available
                    }
                });
                $count++;
            });

        $this->info("Berhasil membatalkan {$count} booking kadaluarsa.");

        return Command::SUCCESS;
    }
}

<?php

namespace App\Observers;

use App\Models\Slot;

class SlotObserver
{
    /**
     * Handle the Slot "saving" event.
     */
    public function saving(Slot $slot): void
    {
        // Jika status yang di-set staf secara manual adalah 'closed', biarkan (jangan ditimpa).
        if ($slot->status === 'closed') {
            return;
        }

        // Flow otomatis berdasarkan kapasitas dan kursi terisi
        if ($slot->booked_seats >= $slot->capacity) {
            $slot->status = 'full';
        } else {
            $slot->status = 'available';
        }
    }
}

<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $store = $user->store;

        if (! $store) {
            return view('staff.dashboard', [
                'hasStore' => false,
                'store' => null,
            ]);
        }

        $today = Carbon::today()->toDateString();

        // 1. Jumlah booking masuk hari ini berstatus pending
        $todayPendingBookings = Booking::where('store_id', $store->id)
            ->whereDate('booking_date', $today)
            ->where('status', 'pending')
            ->count();

        // 2. Jumlah booking hari ini berstatus confirmed
        $todayConfirmedBookings = Booking::where('store_id', $store->id)
            ->whereDate('booking_date', $today)
            ->where('status', 'confirmed')
            ->count();

        // 3. Total sisa kapasitas hari ini dari semua slot hari ini
        $todaySlots = Slot::where('store_id', $store->id)
            ->whereDate('date', $today)
            ->orderBy('start_time')
            ->get();

        $todayRemainingCapacity = $todaySlots->sum(function ($slot) {
            return max(0, $slot->capacity - $slot->booked_seats);
        });

        // 4. Rating rata-rata toko
        $averageRating = $store->average_rating ?? 0.0;

        // 5. 5 booking terbaru berstatus pending (keseluruhan toko)
        $recentPendingBookings = Booking::where('store_id', $store->id)
            ->where('status', 'pending')
            ->with(['user', 'slot'])
            ->latest()
            ->take(5)
            ->get();

        return view('staff.dashboard', [
            'hasStore' => true,
            'store' => $store,
            'todayPendingBookings' => $todayPendingBookings,
            'todayConfirmedBookings' => $todayConfirmedBookings,
            'todayRemainingCapacity' => $todayRemainingCapacity,
            'averageRating' => $averageRating,
            'todaySlots' => $todaySlots,
            'recentPendingBookings' => $recentPendingBookings,
        ]);
    }
}

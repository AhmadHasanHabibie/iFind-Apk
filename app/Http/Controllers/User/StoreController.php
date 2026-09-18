<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function show(Request $request, Store $store): View|JsonResponse
    {
        // Hanya toko approved & aktif yang dapat diakses
        abort_if(! $store->is_active || $store->status !== 'approved', 404, 'Toko tidak ditemukan atau sedang tidak aktif.');

        $store->load([
            'category',
            'photos',
            'facilities',
            'reviews.user' => fn($q) => $q->latest(),
        ]);

        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        $slots = $store->slots()
            ->whereDate('date', $selectedDate)
            ->orderBy('start_time')
            ->get();

        // Jika request via AJAX / JSON (misal saat date picker berubah tanpa reload)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'date' => $selectedDate,
                'slots' => $slots->map(function ($slot) {
                    $isPast = Carbon::parse($slot->date)->isPast() && ! Carbon::parse($slot->date)->isToday();
                    $isBookable = ($slot->status !== 'closed' && $slot->estimated_available > 0 && ! $isPast);

                    return [
                        'id' => $slot->id,
                        'start_time' => substr($slot->start_time, 0, 5),
                        'end_time' => substr($slot->end_time, 0, 5),
                        'capacity' => $slot->capacity,
                        'booked_seats' => $slot->booked_seats,
                        'pending_seats' => $slot->pending_seats,
                        'estimated_available' => $slot->estimated_available,
                        'status' => $slot->status,
                        'is_bookable' => $isBookable,
                    ];
                }),
            ]);
        }

        // Kalkulasi status "Buka Sekarang" / "Tutup"
        $todayDay = strtolower(Carbon::now()->format('l'));
        $todaySchedule = $store->opening_hours[$todayDay] ?? null;
        $isOpenNow = false;

        if ($todaySchedule && ! ($todaySchedule['is_closed'] ?? false)) {
            $currentTime = Carbon::now()->format('H:i');
            $openTime = $todaySchedule['open'] ?? '00:00';
            $closeTime = $todaySchedule['close'] ?? '23:59';

            if ($currentTime >= $openTime && $currentTime <= $closeTime) {
                $isOpenNow = true;
            }
        }

        return view('user.stores.show', [
            'store' => $store,
            'selectedDate' => $selectedDate,
            'slots' => $slots,
            'isOpenNow' => $isOpenNow,
            'todaySchedule' => $todaySchedule,
            'todayDay' => $todayDay,
        ]);
    }
}

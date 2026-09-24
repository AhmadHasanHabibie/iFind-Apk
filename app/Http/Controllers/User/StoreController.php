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
            'menus' => fn($q) => $q->where('is_available', true)->orderByDesc('is_recommended')->orderBy('name'),
            'reviews.user' => fn($q) => $q->latest(),
        ]);

        $isFavorited = $store->isFavoritedBy(auth()->user());

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
                'can_accept_bookings' => $store->canAcceptBookings(),
                'slots' => $slots->map(function ($slot) use ($store) {
                    $isPast = Carbon::parse($slot->date)->isPast() && ! Carbon::parse($slot->date)->isToday();
                    $isBookable = ($store->canAcceptBookings() && $slot->status !== 'closed' && $slot->available_seats > 0 && ! $isPast);

                    return [
                        'id' => $slot->id,
                        'start_time' => substr($slot->start_time, 0, 5),
                        'end_time' => substr($slot->end_time, 0, 5),
                        'capacity' => $slot->capacity,
                        'booked_seats' => $slot->booked_seats,
                        'available_seats' => $slot->available_seats,
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
            'isFavorited' => $isFavorited,
        ]);
    }
}

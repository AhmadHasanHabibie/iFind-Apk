<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;
        $statusTab = $request->input('status', 'pending');

        $query = Booking::where('store_id', $store->id)
            ->with(['user', 'slot'])
            ->latest('booking_date');

        if ($statusTab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($statusTab === 'confirmed') {
            $query->where('status', 'confirmed');
        } else {
            // Tab riwayat (completed, rejected, cancelled)
            $statusTab = 'history';
            $query->whereIn('status', ['completed', 'rejected', 'cancelled']);

            if ($request->filled('date')) {
                $query->whereDate('booking_date', $request->date);
            }
        }

        $bookings = $query->paginate(10)->withQueryString();

        // Count for badges
        $counts = [
            'pending' => Booking::where('store_id', $store->id)->where('status', 'pending')->count(),
            'confirmed' => Booking::where('store_id', $store->id)->where('status', 'confirmed')->count(),
            'history' => Booking::where('store_id', $store->id)->whereIn('status', ['completed', 'rejected', 'cancelled'])->count(),
        ];

        return view('staff.bookings.index', [
            'bookings' => $bookings,
            'statusTab' => $statusTab,
            'counts' => $counts,
            'today' => Carbon::today()->toDateString(),
        ]);
    }

    public function confirm(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if ($booking->status !== 'pending') {
            return redirect()->route('staff.bookings.index', ['status' => 'pending'])
                ->with('error', 'Hanya booking berstatus pending yang dapat dikonfirmasi.');
        }

        $slot = $booking->slot;
        $remainingSeats = max(0, $slot->capacity - $slot->booked_seats);

        // Cek apakah sisa kapasitas mencukupi
        if ($slot->booked_seats + $booking->seat_count > $slot->capacity) {
            return redirect()->route('staff.bookings.index', ['status' => 'pending'])
                ->with('error', "Kapasitas slot tidak mencukupi. Dibutuhkan {$booking->seat_count} kursi, namun sisa kapasitas hanya {$remainingSeats} kursi.");
        }

        DB::transaction(function () use ($booking, $slot) {
            $booking->status = 'confirmed';
            $booking->confirmed_at = now();
            $booking->save();

            $slot->booked_seats += $booking->seat_count;
            $slot->save(); // Trigger SlotObserver to update status
        });

        return redirect()->route('staff.bookings.index', ['status' => 'confirmed'])
            ->with('success', "Booking #{$booking->booking_code} berhasil dikonfirmasi.");
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if ($booking->status !== 'pending') {
            return redirect()->route('staff.bookings.index', ['status' => 'pending'])
                ->with('error', 'Hanya booking berstatus pending yang dapat ditolak.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:3'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan booking wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 3 karakter.',
        ]);

        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('staff.bookings.index', ['status' => 'pending'])
            ->with('success', "Booking #{$booking->booking_code} berhasil ditolak.");
    }

    public function complete(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if ($booking->status !== 'confirmed') {
            return redirect()->route('staff.bookings.index', ['status' => 'confirmed'])
                ->with('error', 'Hanya booking terkonfirmasi yang dapat diselesaikan.');
        }

        $booking->update([
            'status' => 'completed',
        ]);

        return redirect()->route('staff.bookings.index', ['status' => 'confirmed'])
            ->with('success', "Booking #{$booking->booking_code} ditandai telah selesai.");
    }
}

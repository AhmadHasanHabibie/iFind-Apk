<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Slot;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'active'); // 'active' or 'history'
        $user = $request->user();

        $activeStatuses = ['pending', 'confirmed'];
        $historyStatuses = ['completed', 'rejected', 'cancelled'];

        $bookings = $user->bookings()
            ->whereIn('status', $tab === 'history' ? $historyStatuses : $activeStatuses)
            ->with(['store.photos', 'slot', 'review'])
            ->orderByDesc('created_at')
            ->get();

        return view('user.bookings.index', [
            'bookings' => $bookings,
            'tab' => $tab,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => ['required', 'exists:stores,id'],
            'slot_id' => ['required', 'exists:slots,id'],
            'seat_count' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $store = Store::visible()->findOrFail($request->store_id);
        $slot = Slot::where('store_id', $store->id)->findOrFail($request->slot_id);

        // Validasi slot tidak boleh di masa lalu
        $slotDate = Carbon::parse($slot->date);
        if ($slotDate->isPast() && ! $slotDate->isToday()) {
            return back()->withErrors(['slot_id' => 'Jadwal slot waktu sudah lewat.'])->withInput();
        }

        // Validasi slot tidak berstatus closed
        if ($slot->status === 'closed') {
            return back()->withErrors(['slot_id' => 'Slot waktu ini sedang ditutup oleh pihak toko.'])->withInput();
        }

        // Validasi kapasitas estimasi sisa kursi
        $estimatedAvailable = $slot->estimated_available;
        if ($request->seat_count > $estimatedAvailable) {
            return back()->withErrors([
                'seat_count' => "Kapasitas slot tidak mencukupi. Sisa kursi yang tersedia saat ini: {$estimatedAvailable} kursi.",
            ])->withInput();
        }

        // Generate booking_code unik format IFD-YYYYMMDD-XXXXX
        do {
            $bookingCode = 'IFD-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Booking::where('booking_code', $bookingCode)->exists());

        Booking::create([
            'booking_code' => $bookingCode,
            'user_id' => $request->user()->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => $slot->date,
            'seat_count' => $request->seat_count,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('user.bookings.index')->with('success', 'Permintaan booking terkirim, menunggu konfirmasi toko.');
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        // Validasi kepemilikan booking
        abort_if($booking->user_id !== $request->user()->id, 403, 'Akses pemesanan ditolak.');

        // Hanya boleh batalkan status pending
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan berstatus pending yang dapat dibatalkan.');
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('user.bookings.index')->with('success', 'Pesanan booking berhasil dibatalkan.');
    }
}

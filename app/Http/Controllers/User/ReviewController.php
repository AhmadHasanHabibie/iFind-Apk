<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        // Validasi kepemilikan booking
        abort_if($booking->user_id !== $request->user()->id, 403, 'Akses ulasan ditolak.');

        // Booking harus berstatus completed
        abort_if($booking->status !== 'completed', 400, 'Hanya pesanan berstatus selesai yang dapat diberi ulasan.');

        // Cek apakah sudah pernah diberi ulasan
        abort_if($booking->review()->exists(), 400, 'Ulasan sudah pernah dikirimkan untuk pesanan ini.');

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'user_id' => $request->user()->id,
            'store_id' => $booking->store_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Hitung ulang average_rating toko
        $store = $booking->store;
        if ($store) {
            $store->update([
                'average_rating' => round($store->reviews()->avg('rating'), 1),
            ]);
        }

        return redirect()->route('user.bookings.index', ['tab' => 'history'])
            ->with('success', 'Terima kasih! Ulasan dan rating Anda berhasil disimpan.');
    }
}

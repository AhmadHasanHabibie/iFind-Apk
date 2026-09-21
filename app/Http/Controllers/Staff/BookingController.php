<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
            $query->where('status', 'pending_verification');
        } elseif ($statusTab === 'active') {
            $query->whereIn('status', ['confirmed', 'checked_in']);
        } elseif ($statusTab === 'remaining') {
            $query->where('remaining_payment_status', 'pending_verification');
        } else {
            // Tab riwayat
            $statusTab = 'history';
            $query->whereIn('status', [
                'completed',
                'rejected_invalid_payment',
                'rejected_store_full',
                'refunded',
                'cancelled_expired',
            ]);

            if ($request->filled('date')) {
                $query->whereDate('booking_date', $request->date);
            }
        }

        $bookings = $query->paginate(10)->withQueryString();

        // Counts for badges
        $counts = [
            'pending' => Booking::where('store_id', $store->id)->where('status', 'pending_verification')->count(),
            'active' => Booking::where('store_id', $store->id)->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'remaining' => Booking::where('store_id', $store->id)->where('remaining_payment_status', 'pending_verification')->count(),
            'history' => Booking::where('store_id', $store->id)->whereIn('status', [
                'completed',
                'rejected_invalid_payment',
                'rejected_store_full',
                'refunded',
                'cancelled_expired',
            ])->count(),
        ];

        // Bookings waiting for manual refund
        $unrefundedBookings = Booking::where('store_id', $store->id)
            ->where('status', 'rejected_store_full')
            ->latest()
            ->get();

        return view('staff.bookings.index', [
            'bookings' => $bookings,
            'statusTab' => $statusTab,
            'counts' => $counts,
            'unrefundedBookings' => $unrefundedBookings,
            'today' => Carbon::today()->toDateString(),
        ]);
    }

    public function confirm(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if ($booking->status !== 'pending_verification') {
            return redirect()->route('staff.bookings.index', ['status' => 'pending'])
                ->with('error', 'Hanya booking yang menunggu verifikasi yang dapat dikonfirmasi.');
        }

        $slot = $booking->slot;
        if ($slot && $booking->seat_count > $slot->capacity) {
            return redirect()->route('staff.bookings.index', ['status' => 'pending'])
                ->with('error', 'Kapasitas slot tidak mencukupi untuk jumlah kursi yang dipesan.');
        }

        // Generate unique qr_token
        do {
            $qrToken = Str::random(40);
        } while (Booking::where('qr_token', $qrToken)->exists());

        $booking->update([
            'status' => 'confirmed',
            'payment_verified_at' => now(),
            'payment_verified_by' => $request->user()->id,
            'qr_token' => $qrToken,
            'confirmed_at' => now(),
        ]);

        return redirect()->route('staff.bookings.index', ['status' => 'active'])
            ->with('success', "Booking #{$booking->booking_code} berhasil dikonfirmasi. E-Ticket QR telah diterbitkan.");
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if ($booking->status !== 'pending_verification') {
            return redirect()->route('staff.bookings.index', ['status' => 'pending'])
                ->with('error', 'Hanya booking yang menunggu verifikasi yang dapat ditolak.');
        }

        $rejectType = $request->input('reject_type', 'invalid_payment');

        if ($rejectType === 'store_full') {
            $reason = $request->input('payment_rejection_reason') ?: $request->input('rejection_reason') ?: 'Tempat penuh pada jam tersebut. Dana Anda akan segera diproses untuk pengembalian (refund).';
            $newStatus = 'rejected_store_full';
        } else {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'payment_rejection_reason' => ['required_without:rejection_reason', 'nullable', 'string', 'min:3'],
                'rejection_reason' => ['required_without:payment_rejection_reason', 'nullable', 'string', 'min:3'],
            ], [
                'payment_rejection_reason.required_without' => 'Alasan penolakan pembayaran wajib diisi.',
                'payment_rejection_reason.min' => 'Alasan penolakan minimal 3 karakter.',
                'rejection_reason.required_without' => 'Alasan penolakan pembayaran wajib diisi.',
                'rejection_reason.min' => 'Alasan penolakan minimal 3 karakter.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $reason = $request->input('payment_rejection_reason') ?: $request->input('rejection_reason');
            $newStatus = 'rejected_invalid_payment';
        }

        DB::transaction(function () use ($booking, $newStatus, $reason) {
            $booking->update([
                'status' => $newStatus,
                'payment_rejection_reason' => $reason,
            ]);

            // Lepas kembali kapasitas kursi yang ditahan
            if ($booking->slot) {
                $slot = $booking->slot;
                $slot->booked_seats = max(0, $slot->booked_seats - $booking->seat_count);
                $slot->save(); // Trigger SlotObserver otomatis
            }
        });

        $message = $newStatus === 'rejected_store_full'
            ? "Booking #{$booking->booking_code} ditolak karena tempat penuh. Mohon lakukan proses pengembalian dana manual kepada customer."
            : "Booking #{$booking->booking_code} berhasil ditolak (bukti pembayaran tidak valid).";

        return redirect()->route('staff.bookings.index', ['status' => 'pending'])
            ->with('success', $message);
    }

    public function complete(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        // Hanya aktif dari status checked_in
        if ($booking->status !== 'checked_in') {
            return redirect()->route('staff.bookings.index', ['status' => 'active'])
                ->with('error', 'Booking harus check-in terlebih dahulu via scan QR sebelum dapat diselesaikan.');
        }

        // Sisa pembayaran harus sudah lunas atau not_required
        if (! in_array($booking->remaining_payment_status, ['not_required', 'paid'])) {
            if ($request->expectsJson() || $request->ajax()) {
                abort(422, 'Sisa pembayaran belum lunas. Selesaikan pelunasan terlebih dahulu.');
            }
            return redirect()->route('staff.bookings.index', ['status' => 'active'])
                ->with('error', 'Sisa pembayaran belum lunas. Selesaikan pelunasan terlebih dahulu.');
        }

        $booking->update([
            'status' => 'completed',
        ]);

        return redirect()->route('staff.bookings.index', ['status' => 'active'])
            ->with('success', "Kunjungan booking #{$booking->booking_code} ditandai telah selesai.");
    }

    public function confirmRemaining(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if ($booking->remaining_payment_status !== 'pending_verification') {
            return back()->with('error', 'Hanya pelunasan yang menunggu verifikasi yang dapat diterima.');
        }

        $amountReceived = $request->filled('remaining_amount_received')
            ? (float) $request->input('remaining_amount_received')
            : (float) $booking->remaining_amount;

        $booking->update([
            'remaining_payment_status' => 'paid',
            'remaining_verified_at' => now(),
            'remaining_verified_by' => $request->user()->id,
            'remaining_amount_received' => $amountReceived,
        ]);

        return redirect()->route('staff.bookings.index', ['status' => 'remaining'])
            ->with('success', "Pelunasan booking #{$booking->booking_code} berhasil diterima dan diverifikasi.");
    }

    public function rejectRemaining(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if ($booking->remaining_payment_status !== 'pending_verification') {
            return back()->with('error', 'Hanya pelunasan yang menunggu verifikasi yang dapat ditolak.');
        }

        $request->validate([
            'remaining_rejection_reason' => ['required', 'string', 'min:3'],
        ], [
            'remaining_rejection_reason.required' => 'Alasan penolakan pelunasan wajib diisi.',
            'remaining_rejection_reason.min' => 'Alasan penolakan minimal 3 karakter.',
        ]);

        $booking->update([
            'remaining_payment_status' => 'unpaid',
            'remaining_rejection_reason' => $request->input('remaining_rejection_reason'),
        ]);

        return redirect()->route('staff.bookings.index', ['status' => 'remaining'])
            ->with('success', "Pelunasan booking #{$booking->booking_code} berhasil ditolak.");
    }

    public function cashRemaining(Request $request, Booking $booking): mixed
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if (! in_array($booking->remaining_payment_status, ['unpaid', 'pending_verification'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sisa pembayaran untuk booking ini sudah lunas atau tidak diperlukan.',
                ], 422);
            }
            return back()->with('error', 'Sisa pembayaran untuk booking ini sudah lunas atau tidak diperlukan.');
        }

        $request->validate([
            'remaining_amount_received' => ['nullable', 'numeric', 'min:0'],
        ]);

        $amountReceived = $request->filled('remaining_amount_received')
            ? (float) $request->input('remaining_amount_received')
            : (float) $booking->remaining_amount;

        $booking->update([
            'remaining_payment_status' => 'paid',
            'remaining_payment_method' => 'cash',
            'remaining_verified_at' => now(),
            'remaining_verified_by' => $request->user()->id,
            'remaining_amount_received' => $amountReceived,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pelunasan tunai booking #{$booking->booking_code} berhasil dikonfirmasi.",
                'data' => [
                    'booking_code' => $booking->booking_code,
                    'remaining_payment_status' => 'paid',
                    'remaining_payment_method' => 'cash',
                    'remaining_amount_received' => $amountReceived,
                    'remaining_verified_at' => now()->isoFormat('D MMM Y, HH:mm'),
                ],
            ]);
        }

        return back()->with('success', "Pelunasan tunai booking #{$booking->booking_code} berhasil dikonfirmasi.");
    }

    public function refund(Request $request, Booking $booking): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($booking->store_id !== $store->id, 403, 'Akses ditolak.');

        if ($booking->status !== 'rejected_store_full') {
            return back()->with('error', 'Hanya pesanan yang ditolak karena tempat penuh yang dapat diproses refund.');
        }

        $request->validate([
            'refund_note' => ['required', 'string', 'min:3'],
        ], [
            'refund_note.required' => 'Catatan / bukti nomor pengembalian dana wajib diisi.',
            'refund_note.min' => 'Catatan refund minimal 3 karakter.',
        ]);

        $booking->update([
            'status' => 'refunded',
            'refund_note' => $request->refund_note,
            'refunded_at' => now(),
            'refunded_by' => $request->user()->id,
        ]);

        return redirect()->route('staff.bookings.index', ['status' => 'history'])
            ->with('success', "Proses refund untuk booking #{$booking->booking_code} telah ditandai selesai.");
    }
}

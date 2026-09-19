<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        return view('staff.scan.index', [
            'store' => $store,
        ]);
    }

    public function checkIn(Request $request): JsonResponse
    {
        $token = trim($request->input('qr_token', ''));

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code token tidak boleh kosong.',
            ], 422);
        }

        // 1. Cari booking dengan qr_token yang dikirim (atau booking_code sebagai fallback)
        $booking = Booking::where('qr_token', $token)
            ->orWhere('booking_code', $token)
            ->with(['user', 'slot', 'store'])
            ->first();

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code atau token tiket tidak valid / tidak ditemukan.',
            ], 404);
        }

        $currentStore = $request->user()->store;

        // 2. Validasi booking milik toko staf sendiri
        if (! $currentStore || $booking->store_id !== $currentStore->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Tiket reservasi ini terdaftar untuk toko lain (' . ($booking->store->name ?? 'Toko Lain') . ').',
            ], 403);
        }

        // 3. Validasi status
        if ($booking->status === 'checked_in') {
            $timeFormatted = $booking->checked_in_at
                ? $booking->checked_in_at->isoFormat('D MMM Y, HH:mm')
                : 'sebelumnya';

            return response()->json([
                'success' => false,
                'message' => "Tiket ini sudah pernah check-in pada {$timeFormatted}.",
                'booking' => [
                    'booking_code' => $booking->booking_code,
                    'customer_name' => $booking->user->name ?? 'Customer',
                    'checked_in_at' => $timeFormatted,
                ],
            ], 409);
        }

        if ($booking->status !== 'confirmed') {
            $statusDescription = match ($booking->status) {
                'awaiting_payment' => 'Booking belum dibayar oleh customer.',
                'pending_verification' => 'Bukti pembayaran customer masih menunggu verifikasi staf.',
                'rejected_invalid_payment', 'rejected_store_full' => 'Booking ini telah ditolak oleh toko.',
                'refunded' => 'Booking ini telah dibatalkan & dana dikembalikan.',
                'cancelled_expired' => 'Booking ini telah kadaluarsa / dibatalkan.',
                'completed' => 'Kunjungan untuk tiket ini telah selesai.',
                default => 'Status tiket: ' . $booking->status,
            };

            return response()->json([
                'success' => false,
                'message' => "Check-in gagal. {$statusDescription}",
            ], 422);
        }

        // 4. Update status ke checked_in
        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
            'checked_in_by' => $request->user()->id,
        ]);

        $remainingToPay = max(0, (float) $booking->total_amount - (float) $booking->amount_due);

        // 5. Return JSON detail lengkap
        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Selamat datang.',
            'data' => [
                'booking_code' => $booking->booking_code,
                'customer_name' => $booking->user->name ?? 'Pelanggan',
                'customer_phone' => $booking->user->phone ?? '-',
                'seat_count' => $booking->seat_count,
                'booking_date' => Carbon::parse($booking->booking_date)->isoFormat('dddd, D MMMM Y'),
                'time_session' => substr($booking->slot->start_time ?? '', 0, 5) . ' - ' . substr($booking->slot->end_time ?? '', 0, 5),
                'total_amount' => $booking->total_amount,
                'amount_due' => $booking->amount_due,
                'remaining_to_pay' => $remainingToPay,
                'payment_status' => $remainingToPay <= 0 ? 'LUNAS' : 'SISA BAYAR DI TEMPAT',
                'remaining_formatted' => $remainingToPay <= 0 ? 'LUNAS' : 'Rp ' . number_format($remainingToPay, 0, ',', '.'),
                'checked_in_at' => now()->isoFormat('D MMM Y, HH:mm:ss'),
                'notes' => $booking->notes ?: 'Tidak ada catatan khusus.',
            ],
        ]);
    }
}

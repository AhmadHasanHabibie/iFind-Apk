<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Slot;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'active'); // 'active' or 'history'
        $user = $request->user();

        $activeStatuses = ['awaiting_payment', 'pending_verification', 'confirmed', 'checked_in'];
        $historyStatuses = ['completed', 'rejected_invalid_payment', 'rejected_store_full', 'refunded', 'cancelled_expired'];

        $bookings = $user->bookings()
            ->whereIn('status', $tab === 'history' ? $historyStatuses : $activeStatuses)
            ->with(['store.photos', 'slot', 'review'])
            ->orderByDesc('created_at')
            ->get();

        $activeCount = $user->bookings()->whereIn('status', $activeStatuses)->count();
        $historyCount = $user->bookings()->whereIn('status', $historyStatuses)->count();

        return view('user.bookings.index', [
            'bookings' => $bookings,
            'tab' => $tab,
            'activeCount' => $activeCount,
            'historyCount' => $historyCount,
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

        // Guard toko belum mengatur harga & rekening/QRIS
        if (! $store->canAcceptBookings()) {
            return back()->withErrors([
                'store_id' => 'Toko ini belum melengkapi pengaturan pembayaran dan belum dapat menerima reservasi.',
            ])->withInput();
        }

        // Validasi slot tidak boleh di masa lalu
        $slotDate = Carbon::parse($slot->date);
        if ($slotDate->isPast() && ! $slotDate->isToday()) {
            return back()->withErrors(['slot_id' => 'Jadwal slot waktu sudah lewat.'])->withInput();
        }

        // Validasi slot tidak berstatus closed
        if ($slot->status === 'closed') {
            return back()->withErrors(['slot_id' => 'Slot waktu ini sedang ditutup oleh pihak toko.'])->withInput();
        }

        // Validasi kapasitas sisa kursi yang tersedia
        $availableSeats = $slot->available_seats;
        if ($request->seat_count > $availableSeats) {
            return back()->withErrors([
                'seat_count' => "Kapasitas slot tidak mencukupi. Sisa kursi yang tersedia saat ini: {$availableSeats} kursi.",
            ])->withInput();
        }

        // Hitung nominal harga & DP
        $pricePerPax = (float) $store->price_per_pax;
        $dpPercentage = (int) ($store->dp_percentage ?? 100);
        $seatCount = (int) $request->seat_count;

        $totalAmount = round($pricePerPax * $seatCount, 2);
        $amountDue = round($totalAmount * $dpPercentage / 100, 2);
        $timeoutMinutes = (int) ($store->payment_timeout_minutes ?? 60);
        $deadline = now()->addMinutes($timeoutMinutes);

        // Generate booking_code unik format IFD-YYYYMMDD-XXXXX
        do {
            $bookingCode = 'IFD-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Booking::where('booking_code', $bookingCode)->exists());

<<<<<<< HEAD
        try {
            $booking = DB::transaction(function () use ($request, $store, $seatCount, $bookingCode, $pricePerPax, $dpPercentage, $totalAmount, $amountDue, $deadline) {
                // Kunci baris slot (pessimistic lock) agar kebal dari race condition
                $lockedSlot = Slot::where('id', $request->slot_id)
                    ->where('store_id', $store->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedSlot->status === 'closed') {
                    throw new \RuntimeException('Slot waktu ini sedang ditutup oleh pihak toko.');
                }

                if ($seatCount > $lockedSlot->available_seats) {
                    throw new \RuntimeException("Kapasitas slot tidak mencukupi. Sisa kursi saat ini: {$lockedSlot->available_seats} kursi.");
                }

                $createdBooking = Booking::create([
                    'booking_code' => $bookingCode,
                    'user_id' => $request->user()->id,
                    'store_id' => $store->id,
                    'slot_id' => $lockedSlot->id,
                    'booking_date' => $lockedSlot->date,
                    'seat_count' => $seatCount,
                    'status' => 'awaiting_payment',
                    'notes' => $request->notes,
                    'price_per_pax_snapshot' => $pricePerPax,
                    'dp_percentage_snapshot' => $dpPercentage,
                    'total_amount' => $totalAmount,
                    'amount_due' => $amountDue,
                    'payment_deadline' => $deadline,
                ]);

                // Tahan kapasitas sejak awal booking dibuat
                $lockedSlot->booked_seats += $seatCount;
                $lockedSlot->save(); // Trigger SlotObserver otomatis

                return $createdBooking;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['seat_count' => $e->getMessage()])->withInput();
        }
=======
        $booking = DB::transaction(function () use ($request, $store, $slot, $seatCount, $bookingCode, $pricePerPax, $dpPercentage, $totalAmount, $amountDue, $deadline) {
            $createdBooking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => $request->user()->id,
                'store_id' => $store->id,
                'slot_id' => $slot->id,
                'booking_date' => $slot->date,
                'seat_count' => $seatCount,
                'status' => 'awaiting_payment',
                'notes' => $request->notes,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPercentage,
                'total_amount' => $totalAmount,
                'amount_due' => $amountDue,
                'payment_deadline' => $deadline,
            ]);

            // Tahan kapasitas sejak awal booking dibuat
            $slot->booked_seats += $seatCount;
            $slot->save(); // Trigger SlotObserver otomatis

            return $createdBooking;
        });
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d

        return redirect()->route('user.bookings.payment', $booking->booking_code)
            ->with('success', 'Reservasi berhasil dibuat. Silakan selesaikan pembayaran sesuai instruksi.');
    }

    public function payment(Request $request, Booking $booking): View|RedirectResponse
    {
        abort_if($booking->user_id !== $request->user()->id, 403, 'Akses pemesanan ditolak.');

        // Jika sudah bukan awaiting_payment, arahkan ke tempat semestinya
        if ($booking->status === 'pending_verification') {
            return redirect()->route('user.bookings.index')
                ->with('info', 'Bukti transfer sudah terkirim dan sedang menunggu verifikasi toko.');
        }

        if (in_array($booking->status, ['confirmed', 'checked_in', 'completed'])) {
            return redirect()->route('user.bookings.ticket', $booking->booking_code);
        }

        if (in_array($booking->status, ['rejected_invalid_payment', 'rejected_store_full', 'refunded', 'cancelled_expired'])) {
            return redirect()->route('user.bookings.index');
        }

        $booking->load(['store', 'slot']);

        $remainingSeconds = max(0, Carbon::now()->diffInSeconds($booking->payment_deadline, false));

        return view('user.bookings.payment', [
            'booking' => $booking,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function uploadProof(Request $request, Booking $booking): RedirectResponse
    {
        abort_if($booking->user_id !== $request->user()->id, 403, 'Akses pemesanan ditolak.');

        if ($booking->status !== 'awaiting_payment') {
            return redirect()->route('user.bookings.index')
                ->with('error', 'Status pesanan tidak memungkinkan pengunggahan bukti transfer.');
        }

        // Cek apakah payment_deadline sudah lewat
        if (now()->greaterThan($booking->payment_deadline)) {
            return back()->with('error', 'Waktu pembayaran habis. Booking telah kadaluarsa dan otomatis dibatalkan.');
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'payment_proof.required' => 'File bukti transfer wajib diunggah.',
            'payment_proof.image' => 'File harus berupa gambar (JPG/PNG).',
            'payment_proof.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $path = $request->file('payment_proof')->store("payment_proofs/{$booking->id}", 'public');

        $booking->update([
            'payment_proof_path' => $path,
            'payment_uploaded_at' => now(),
            'status' => 'pending_verification',
        ]);

        return redirect()->route('user.bookings.index')
            ->with('success', 'Bukti pembayaran terkirim, menunggu verifikasi toko.');
    }

    public function ticket(Request $request, Booking $booking): View
    {
        abort_if($booking->user_id !== $request->user()->id, 403, 'Akses tiket ditolak.');

        abort_if(
            ! in_array($booking->status, ['confirmed', 'checked_in', 'completed']),
            403,
            'E-Ticket hanya dapat diakses setelah reservasi dikonfirmasi oleh toko.'
        );

        $booking->load(['store', 'slot']);

        // Generate QR code SVG inline safely
        try {
            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($booking->qr_token);
            } else {
                $qrCodeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="250" height="250" viewBox="0 0 250 250"><rect width="250" height="250" fill="#f8fafc"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="monospace" font-size="12" fill="#475569">' . htmlspecialchars($booking->qr_token) . '</text></svg>';
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('QR Code generation fallback: ' . $e->getMessage());
            $qrCodeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="250" height="250" viewBox="0 0 250 250"><rect width="250" height="250" fill="#f8fafc"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="monospace" font-size="12" fill="#475569">' . htmlspecialchars($booking->qr_token) . '</text></svg>';
        }

        return view('user.bookings.ticket', [
            'booking' => $booking,
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        abort_if($booking->user_id !== $request->user()->id, 403, 'Akses pemesanan ditolak.');

        // Hanya boleh batalkan status awaiting_payment
        if ($booking->status !== 'awaiting_payment') {
            return back()->with('error', 'Hanya pesanan yang menunggu pembayaran yang dapat dibatalkan.');
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'cancelled_expired',
            ]);

            // Lepas kembali kapasitas kursi
            $slot = $booking->slot;
            $slot->booked_seats = max(0, $slot->booked_seats - $booking->seat_count);
            $slot->save(); // Trigger SlotObserver
        });

        return redirect()->route('user.bookings.index')
            ->with('success', 'Pesanan booking berhasil dibatalkan dan kursi dilepas kembali.');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Slot;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingDummySeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();
        $user = User::where('role', 'user')->first();
        $staff = $store ? $store->user : User::where('role', 'staff')->first();

        if (! $store || ! $user) {
            return;
        }

        // Create dummy payment proof images if not exists
        if (! Storage::disk('public')->exists('payment_proofs/dummy_proof_1.jpg')) {
            $img = imagecreatetruecolor(400, 600);
            $bg = imagecolorallocate($img, 240, 243, 246);
            $text = imagecolorallocate($img, 30, 41, 59);
            imagefill($img, 0, 0, $bg);
            imagestring($img, 5, 60, 280, 'BUKTI TRANSFER DUMMY 1', $text);
            ob_start();
            imagejpeg($img);
            $content = ob_get_clean();
            imagedestroy($img);
            Storage::disk('public')->put('payment_proofs/dummy_proof_1.jpg', $content);
        }

        if (! Storage::disk('public')->exists('payment_proofs/dummy_proof_2.jpg')) {
            $img = imagecreatetruecolor(400, 600);
            $bg = imagecolorallocate($img, 240, 243, 246);
            $text = imagecolorallocate($img, 30, 41, 59);
            imagefill($img, 0, 0, $bg);
            imagestring($img, 5, 60, 280, 'BUKTI TRANSFER DUMMY 2', $text);
            ob_start();
            imagejpeg($img);
            $content = ob_get_clean();
            imagedestroy($img);
            Storage::disk('public')->put('payment_proofs/dummy_proof_2.jpg', $content);
        }

        if (! Storage::disk('public')->exists('qris/dummy/titik-temu-qris.png')) {
            $img = imagecreatetruecolor(300, 300);
            $bg = imagecolorallocate($img, 255, 255, 255);
            $text = imagecolorallocate($img, 15, 23, 42);
            imagefill($img, 0, 0, $bg);
            imagestring($img, 5, 75, 140, 'QRIS DUMMY STORE', $text);
            ob_start();
            imagepng($img);
            $content = ob_get_clean();
            imagedestroy($img);
            Storage::disk('public')->put('qris/dummy/titik-temu-qris.png', $content);
        }

        $slots = $store->slots()->orderBy('date')->orderBy('start_time')->get();

        if ($slots->count() < 10) {
            return;
        }

        $pricePerPax = $store->price_per_pax ?? 25000;
        $dpPct = $store->dp_percentage ?? 50;

        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        // 1. awaiting_payment (Future deadline, e.g. +45 mins)
        $seat1 = 2;
        $total1 = $seat1 * $pricePerPax;
        $dp1 = (int) round(($total1 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-101'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[0]->id,
                'booking_date' => $today,
                'seat_count' => $seat1,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total1,
                'amount_due' => $dp1,
                'payment_deadline' => now()->addMinutes(45),
                'status' => 'awaiting_payment',
                'notes' => 'Tolong sediakan meja dekat colokan untuk 2 laptop.',
            ]
        );

        // 2. awaiting_payment (Past deadline, e.g. -15 mins - for testing scheduler auto-cancel)
        $seat2 = 2;
        $total2 = $seat2 * $pricePerPax;
        $dp2 = (int) round(($total2 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-102'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[1]->id,
                'booking_date' => $today,
                'seat_count' => $seat2,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total2,
                'amount_due' => $dp2,
                'payment_deadline' => now()->subMinutes(15),
                'status' => 'awaiting_payment',
                'notes' => 'Menunggu konfirmasi transfer teman.',
            ]
        );

        // 3. pending_verification (1)
        $seat3 = 3;
        $total3 = $seat3 * $pricePerPax;
        $dp3 = (int) round(($total3 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-103'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[2]->id,
                'booking_date' => $today,
                'seat_count' => $seat3,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total3,
                'amount_due' => $dp3,
                'payment_deadline' => now()->addMinutes(35),
                'payment_proof_path' => 'payment_proofs/dummy_proof_1.jpg',
                'payment_uploaded_at' => now()->subMinutes(15),
                'status' => 'pending_verification',
                'notes' => 'Sudah transfer DP via BCA.',
            ]
        );

        // 4. pending_verification (2)
        $seat4 = 4;
        $total4 = $seat4 * $pricePerPax;
        $dp4 = (int) round(($total4 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-104'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[3]->id,
                'booking_date' => $tomorrow,
                'seat_count' => $seat4,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total4,
                'amount_due' => $dp4,
                'payment_deadline' => now()->addMinutes(50),
                'payment_proof_path' => 'payment_proofs/dummy_proof_2.jpg',
                'payment_uploaded_at' => now()->subMinutes(5),
                'status' => 'pending_verification',
                'notes' => 'Diskusi kelompok tugas akhir.',
            ]
        );

        // 5. completed (IF-BKG-105 for ReviewDummySeeder compatibility)
        $seat5 = 2;
        $total5 = $seat5 * $pricePerPax;
        $dp5 = (int) round(($total5 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-105'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[4]->id,
                'booking_date' => $today,
                'seat_count' => $seat5,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total5,
                'amount_due' => $dp5,
                'payment_deadline' => now()->subHours(6),
                'payment_proof_path' => 'payment_proofs/dummy_proof_1.jpg',
                'payment_uploaded_at' => now()->subHours(5),
                'payment_verified_at' => now()->subHours(4),
                'payment_verified_by' => $staff?->id,
                'confirmed_at' => now()->subHours(4),
                'qr_token' => Str::random(40),
                'checked_in_at' => now()->subHours(3),
                'checked_in_by' => $staff?->id,
                'status' => 'completed',
                'notes' => 'Terima kasih atas pelayanannya.',
            ]
        );

        // 6. confirmed (1)
        $seat6 = 2;
        $total6 = $seat6 * $pricePerPax;
        $dp6 = (int) round(($total6 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-106'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[5]->id,
                'booking_date' => $today,
                'seat_count' => $seat6,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total6,
                'amount_due' => $dp6,
                'payment_deadline' => now()->subHours(2),
                'payment_proof_path' => 'payment_proofs/dummy_proof_1.jpg',
                'payment_uploaded_at' => now()->subHours(2),
                'payment_verified_at' => now()->subHour(),
                'payment_verified_by' => $staff?->id,
                'confirmed_at' => now()->subHour(),
                'qr_token' => 'DEMO-TOKEN-CONFIRMED-106-XYZ1234567890',
                'status' => 'confirmed',
                'notes' => 'Siap datang tepat waktu.',
            ]
        );

        // 7. confirmed (2)
        $seat7 = 3;
        $total7 = $seat7 * $pricePerPax;
        $dp7 = (int) round(($total7 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-107'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[6]->id,
                'booking_date' => $tomorrow,
                'seat_count' => $seat7,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total7,
                'amount_due' => $dp7,
                'payment_deadline' => now()->subHours(1),
                'payment_proof_path' => 'payment_proofs/dummy_proof_2.jpg',
                'payment_uploaded_at' => now()->subHours(1),
                'payment_verified_at' => now()->subMinutes(30),
                'payment_verified_by' => $staff?->id,
                'confirmed_at' => now()->subMinutes(30),
                'qr_token' => 'DEMO-TOKEN-CONFIRMED-107-ABC9876543210',
                'status' => 'confirmed',
                'notes' => 'Acara temu komunitas koding.',
            ]
        );

        // 8. checked_in (1)
        $seat8 = 2;
        $total8 = $seat8 * $pricePerPax;
        $dp8 = (int) round(($total8 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-108'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[7]->id,
                'booking_date' => $today,
                'seat_count' => $seat8,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total8,
                'amount_due' => $dp8,
                'payment_deadline' => now()->subHours(3),
                'payment_proof_path' => 'payment_proofs/dummy_proof_1.jpg',
                'payment_uploaded_at' => now()->subHours(3),
                'payment_verified_at' => now()->subHours(2),
                'payment_verified_by' => $staff?->id,
                'confirmed_at' => now()->subHours(2),
                'qr_token' => 'DEMO-TOKEN-CHECKEDIN-108-LMN555666777',
                'checked_in_at' => now()->subMinutes(20),
                'checked_in_by' => $staff?->id,
                'status' => 'checked_in',
                'notes' => 'Sudah berada di meja 4.',
            ]
        );

        // 9. rejected_invalid_payment (1)
        $seat9 = 2;
        $total9 = $seat9 * $pricePerPax;
        $dp9 = (int) round(($total9 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-109'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[0]->id,
                'booking_date' => $today,
                'seat_count' => $seat9,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total9,
                'amount_due' => $dp9,
                'payment_deadline' => now()->subHours(2),
                'payment_proof_path' => 'payment_proofs/dummy_proof_1.jpg',
                'payment_uploaded_at' => now()->subHours(2),
                'payment_verified_at' => now()->subHours(1),
                'payment_verified_by' => $staff?->id,
                'payment_rejection_reason' => 'Bukti transfer tidak terbaca / nominal tidak sesuai dengan tagihan DP.',
                'status' => 'rejected_invalid_payment',
                'notes' => 'Mohon cek kembali mutasi.',
            ]
        );

        // 10. rejected_store_full (1 - pending refund)
        $seat10 = 2;
        $total10 = $seat10 * $pricePerPax;
        $dp10 = (int) round(($total10 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-110'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[1]->id,
                'booking_date' => $today,
                'seat_count' => $seat10,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total10,
                'amount_due' => $dp10,
                'payment_deadline' => now()->subHours(2),
                'payment_proof_path' => 'payment_proofs/dummy_proof_2.jpg',
                'payment_uploaded_at' => now()->subHours(2),
                'payment_verified_at' => now()->subHours(1),
                'payment_verified_by' => $staff?->id,
                'payment_rejection_reason' => 'Toko mengalami kendala teknis kelistrikan darurat pada jam tersebut.',
                'status' => 'rejected_store_full',
                'notes' => 'Butuh ruangan hening.',
            ]
        );

        // 11. refunded (1)
        $seat11 = 2;
        $total11 = $seat11 * $pricePerPax;
        $dp11 = (int) round(($total11 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-111'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[2]->id,
                'booking_date' => $today,
                'seat_count' => $seat11,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total11,
                'amount_due' => $dp11,
                'payment_deadline' => now()->subHours(4),
                'payment_proof_path' => 'payment_proofs/dummy_proof_1.jpg',
                'payment_uploaded_at' => now()->subHours(4),
                'payment_verified_at' => now()->subHours(3),
                'payment_verified_by' => $staff?->id,
                'payment_rejection_reason' => 'Kapasitas ruangan overbooked karena perpanjangan reservasi event.',
                'refund_note' => 'Dana DP Rp 25.000 telah ditransfer kembali ke rekening BCA customer.',
                'refunded_at' => now()->subMinutes(30),
                'refunded_by' => $staff?->id,
                'status' => 'refunded',
                'notes' => 'Reservasi mendesak.',
            ]
        );

        // 12. cancelled_expired (1)
        $seat12 = 1;
        $total12 = $seat12 * $pricePerPax;
        $dp12 = (int) round(($total12 * $dpPct) / 100);
        Booking::updateOrCreate(
            ['booking_code' => 'IF-BKG-112'],
            [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slots[3]->id,
                'booking_date' => $today,
                'seat_count' => $seat12,
                'price_per_pax_snapshot' => $pricePerPax,
                'dp_percentage_snapshot' => $dpPct,
                'total_amount' => $total12,
                'amount_due' => $dp12,
                'payment_deadline' => now()->subHours(2),
                'status' => 'cancelled_expired',
                'notes' => 'Lupa bayar sebelum batas waktu.',
            ]
        );

        // Synchronize booked_seats on all slots:
        // Only active statuses hold seats: awaiting_payment, pending_verification, confirmed, checked_in, completed
        $activeStatuses = ['awaiting_payment', 'pending_verification', 'confirmed', 'checked_in', 'completed'];
        foreach ($slots as $slot) {
            $heldSeats = Booking::where('slot_id', $slot->id)
                ->whereIn('status', $activeStatuses)
                ->sum('seat_count');
            $slot->booked_seats = min($slot->capacity, (int) $heldSeats);
            $slot->save(); // SlotObserver will recalculate 'available' or 'full'
        }
    }
}

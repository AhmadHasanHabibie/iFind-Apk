<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Slot;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromptFourPaymentAndTicketTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh --seed');
    }

    public function test_store_can_accept_bookings_rule_and_disabled_booking_cta()
    {
        $user = User::where('role', 'user')->first();
        $staff = User::where('role', 'staff')->first();

        // Buat toko tanpa harga & tanpa rekening/QRIS
        $unconfiguredStore = Store::create([
            'user_id' => $staff->id,
            'category_id' => Category::first()->id,
            'name' => 'Toko Belum Konfigurasi',
            'slug' => 'toko-belum-konfigurasi',
            'address' => 'Jl. Test No. 1',
            'city' => 'Jakarta',
            'status' => 'approved',
            'is_active' => true,
            'price_per_pax' => null,
            'bank_name' => null,
            'bank_account_number' => null,
            'qris_image_path' => null,
        ]);

        $this->assertFalse($unconfiguredStore->canAcceptBookings());

        // Halaman detail toko menampilkan peringatan dan menonaktifkan booking
        $response = $this->actingAs($user)->get(route('user.stores.show', $unconfiguredStore->slug));
        $response->assertStatus(200);
        $response->assertSee('Toko Belum Mengatur Pembayaran');
        $response->assertSee('Belum Menerima Booking');
    }

    public function test_staff_can_update_payment_settings_with_qris_and_bank()
    {
        Storage::fake('public');
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $store = $staff->store;

        $response = $this->actingAs($staff)->put(route('staff.store.update'), [
            'name' => $store->name,
            'category_id' => $store->category_id,
            'address' => $store->address,
            'city' => $store->city,
            'price_per_pax' => 30000,
            'dp_percentage' => 40,
            'payment_timeout_minutes' => 45,
            'bank_name' => 'Bank BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Pemilik Toko',
            'qris_image' => UploadedFile::fake()->image('my_qris.png'),
        ]);

        $response->assertRedirect(route('staff.store.edit'));
        $response->assertSessionHas('success');

        $store->refresh();
        $this->assertEquals(30000, (float) $store->price_per_pax);
        $this->assertEquals(40, $store->dp_percentage);
        $this->assertEquals(45, $store->payment_timeout_minutes);
        $this->assertEquals('Bank BCA', $store->bank_name);
        $this->assertTrue($store->canAcceptBookings());
        $this->assertNotNull($store->qris_image_path);
        Storage::disk('public')->assertExists($store->qris_image_path);
    }

    public function test_user_booking_holds_capacity_immediately_and_calculates_dp()
    {
        $user = User::where('role', 'user')->first();
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $slot = $store->slots()->where('status', 'available')->first();

        $initialBooked = $slot->booked_seats;
        $seatCount = 2;

        $response = $this->actingAs($user)->post(route('user.bookings.store'), [
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'seat_count' => $seatCount,
            'notes' => 'Testing hold capacity',
        ]);

        $booking = Booking::where('user_id', $user->id)
            ->where('slot_id', $slot->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($booking);
        $response->assertRedirect(route('user.bookings.payment', $booking->booking_code));

        // Verifikasi kalkulasi harga & DP
        $expectedTotal = (float) $store->price_per_pax * $seatCount;
        $expectedDue = round($expectedTotal * $store->dp_percentage / 100, 2);

        $this->assertEquals('awaiting_payment', $booking->status);
        $this->assertEquals($expectedTotal, (float) $booking->total_amount);
        $this->assertEquals($expectedDue, (float) $booking->amount_due);
        $this->assertEquals($store->dp_percentage, $booking->dp_percentage_snapshot);

        // Kapasitas slot langsung berkurang / booked_seats bertambah
        $slot->refresh();
        $this->assertEquals($initialBooked + $seatCount, $slot->booked_seats);
    }

    public function test_artisan_cancel_expired_bookings_releases_capacity()
    {
        $store = Store::first();
        $slot = $store->slots()->first();
        $user = User::where('role', 'user')->first();

        $slot->booked_seats = 3;
        $slot->save();

        // Buat booking yang sudah kadaluarsa
        $expiredBooking = Booking::create([
            'booking_code' => 'EXP-TEST-001',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => today(),
            'seat_count' => 2,
            'price_per_pax_snapshot' => 25000,
            'total_amount' => 50000,
            'amount_due' => 25000,
            'payment_deadline' => now()->subMinutes(10),
            'status' => 'awaiting_payment',
        ]);

        $this->artisan('bookings:cancel-expired')
            ->assertExitCode(0);

        $this->assertEquals('cancelled_expired', $expiredBooking->fresh()->status);
        // Kursi dilepas: 3 - 2 = 1
        $this->assertEquals(1, $slot->fresh()->booked_seats);
    }

    public function test_user_cannot_upload_proof_after_payment_deadline()
    {
        Storage::fake('public');
        $user = User::where('role', 'user')->first();
        $store = Store::first();
        $slot = $store->slots()->first();

        $expiredBooking = Booking::create([
            'booking_code' => 'EXP-UPLOAD-01',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => today(),
            'seat_count' => 1,
            'price_per_pax_snapshot' => 25000,
            'total_amount' => 25000,
            'amount_due' => 25000,
            'payment_deadline' => now()->subMinute(),
            'status' => 'awaiting_payment',
        ]);

        $response = $this->actingAs($user)->post(route('user.bookings.upload-proof', $expiredBooking), [
            'payment_proof' => UploadedFile::fake()->image('proof.jpg'),
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('awaiting_payment', $expiredBooking->fresh()->status);
        $this->assertNull($expiredBooking->fresh()->payment_proof_path);
    }

    public function test_staff_confirm_booking_generates_qr_token_and_keeps_seats()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $booking = Booking::where('store_id', $staff->store->id)
            ->where('status', 'pending_verification')
            ->first();

        $this->assertNotNull($booking);
        $initialSeats = $booking->slot->booked_seats;

        $response = $this->actingAs($staff)->patch(route('staff.bookings.confirm', $booking));
        $response->assertRedirect(route('staff.bookings.index', ['status' => 'active']));

        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertNotNull($booking->qr_token);
        $this->assertEquals(40, strlen($booking->qr_token));
        $this->assertEquals($staff->id, $booking->payment_verified_by);
        $this->assertNotNull($booking->payment_verified_at);

        // Kursi tetap tertahan
        $this->assertEquals($initialSeats, $booking->slot->fresh()->booked_seats);
    }

    public function test_staff_reject_booking_releases_capacity()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $booking = Booking::where('store_id', $staff->store->id)
            ->where('status', 'pending_verification')
            ->first();

        $this->assertNotNull($booking);
        $slot = $booking->slot;
        $initialSeats = $slot->booked_seats;

        $response = $this->actingAs($staff)->patch(route('staff.bookings.reject', $booking), [
            'reject_type' => 'invalid_payment',
            'rejection_reason' => 'Nominal transfer kurang dari tagihan DP.',
        ]);

        $response->assertRedirect(route('staff.bookings.index', ['status' => 'pending']));

        $booking->refresh();
        $this->assertEquals('rejected_invalid_payment', $booking->status);
        $this->assertEquals('Nominal transfer kurang dari tagihan DP.', $booking->payment_rejection_reason);

        // Kursi dilepaskan
        $this->assertEquals(max(0, $initialSeats - $booking->seat_count), $slot->fresh()->booked_seats);
    }

    public function test_staff_refund_rejected_store_full_booking()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $rejectedBooking = Booking::where('store_id', $staff->store->id)
            ->where('status', 'rejected_store_full')
            ->first();

        $this->assertNotNull($rejectedBooking);

        $response = $this->actingAs($staff)->patch(route('staff.bookings.refund', $rejectedBooking), [
            'refund_note' => 'Pengembalian via transfer BCA ke rek customer 12345678.',
        ]);

        $response->assertRedirect(route('staff.bookings.index', ['status' => 'history']));
        $response->assertSessionHas('success');

        $rejectedBooking->refresh();
        $this->assertEquals('refunded', $rejectedBooking->status);
        $this->assertEquals('Pengembalian via transfer BCA ke rek customer 12345678.', $rejectedBooking->refund_note);
        $this->assertEquals($staff->id, $rejectedBooking->refunded_by);
        $this->assertNotNull($rejectedBooking->refunded_at);
    }

    public function test_user_ticket_page_renders_inline_svg_qr_code()
    {
        $user = User::where('role', 'user')->first();
        $booking = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->first();

        $this->assertNotNull($booking);

        $response = $this->actingAs($user)->get(route('user.bookings.ticket', $booking->booking_code));
        $response->assertStatus(200);
        $response->assertSee('<svg', false);
        $response->assertSee($booking->booking_code);
        $response->assertSee('E-TICKET CHECK-IN');
    }

    public function test_staff_scan_qr_check_in_flow_and_validations()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $store = $staff->store;

        $confirmedBooking = Booking::where('store_id', $store->id)
            ->where('status', 'confirmed')
            ->first();

        $this->assertNotNull($confirmedBooking);

        // 1. Check-in berhasil
        $response = $this->actingAs($staff)->postJson(route('staff.scan.check-in'), [
            'qr_token' => $confirmedBooking->qr_token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'booking_code' => $confirmedBooking->booking_code,
                'seat_count' => $confirmedBooking->seat_count,
            ],
        ]);

        $confirmedBooking->refresh();
        $this->assertEquals('checked_in', $confirmedBooking->status);
        $this->assertEquals($staff->id, $confirmedBooking->checked_in_by);
        $this->assertNotNull($confirmedBooking->checked_in_at);

        // 2. Scan ulang (duplicate scan) harus gagal 409 Conflict
        $duplicateResponse = $this->actingAs($staff)->postJson(route('staff.scan.check-in'), [
            'qr_token' => $confirmedBooking->qr_token,
        ]);
        $duplicateResponse->assertStatus(409);
        $duplicateResponse->assertJson(['success' => false]);

        // 3. Staf toko lain mencoba scan tiket toko ini -> 403 Forbidden
        $otherStaff = User::where('role', 'staff')
            ->where('id', '!=', $staff->id)
            ->first();

        if ($otherStaff && $otherStaff->store) {
            $unauthorizedResponse = $this->actingAs($otherStaff)->postJson(route('staff.scan.check-in'), [
                'qr_token' => $confirmedBooking->qr_token,
            ]);
            $unauthorizedResponse->assertStatus(403);
        }

        // 4. Token tidak valid -> 404 Not Found
        $invalidResponse = $this->actingAs($staff)->postJson(route('staff.scan.check-in'), [
            'qr_token' => 'INVALID-TOKEN-1234567890-NON-EXISTENT',
        ]);
        $invalidResponse->assertStatus(404);
    }

    public function test_staff_cannot_complete_booking_before_checked_in()
    {
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;

        // Cari booking confirmed yang belum check-in
        $confirmedBooking = Booking::where('store_id', $store->id)
            ->where('status', 'confirmed')
            ->first();

        $this->assertNotNull($confirmedBooking);

        // Coba langsung selesaikan tanpa check-in
        $response = $this->actingAs($staff)->patch(route('staff.bookings.complete', $confirmedBooking));
        $response->assertRedirect(route('staff.bookings.index', ['status' => 'active']));
        $response->assertSessionHas('error');
        $this->assertEquals('confirmed', $confirmedBooking->fresh()->status);

        // Setelah check-in, baru bisa complete
        $confirmedBooking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
            'checked_in_by' => $staff->id,
        ]);

        $successResponse = $this->actingAs($staff)->patch(route('staff.bookings.complete', $confirmedBooking));
        $successResponse->assertRedirect(route('staff.bookings.index', ['status' => 'active']));
        $successResponse->assertSessionHas('success');
        $this->assertEquals('completed', $confirmedBooking->fresh()->status);
    }
}

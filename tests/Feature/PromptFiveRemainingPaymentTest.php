<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Slot;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromptFiveRemainingPaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh --seed');
    }

    public function test_booking_creation_initializes_remaining_payment_status_based_on_dp_percentage()
    {
        $user = User::where('role', 'user')->first();
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();

        // 1. Toko dengan DP 50% -> remaining_payment_status = 'unpaid'
        $store->update(['dp_percentage' => 50]);
        $slot1 = $store->slots()->where('status', 'available')->first();

        $response1 = $this->actingAs($user)->post(route('user.bookings.store'), [
            'store_id' => $store->id,
            'slot_id' => $slot1->id,
            'seat_count' => 2,
        ]);
        $response1->assertRedirect();

        $booking1 = Booking::where('user_id', $user->id)->latest('id')->first();
        $this->assertEquals('unpaid', $booking1->remaining_payment_status);
        $this->assertGreaterThan(0, $booking1->remaining_amount);

        // 2. Toko dengan DP 100% -> remaining_payment_status = 'not_required'
        $store->update(['dp_percentage' => 100]);
        $slot2 = $store->slots()->where('status', 'available')->where('id', '!=', $slot1->id)->first();

        $response2 = $this->actingAs($user)->post(route('user.bookings.store'), [
            'store_id' => $store->id,
            'slot_id' => $slot2->id,
            'seat_count' => 1,
        ]);
        $response2->assertRedirect();

        $booking2 = Booking::where('user_id', $user->id)->latest('id')->first();
        $this->assertEquals('not_required', $booking2->remaining_payment_status);
        $this->assertEquals(0, $booking2->remaining_amount);
    }

    public function test_ticket_page_displays_remaining_payment_card_appropriately()
    {
        $user = User::where('role', 'user')->first();

        // Booking confirmed dengan remaining unpaid
        $unpaidBooking = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('remaining_payment_status', 'unpaid')
            ->first();

        $this->assertNotNull($unpaidBooking);

        $response = $this->actingAs($user)->get(route('user.bookings.ticket', $unpaidBooking));
        $response->assertStatus(200);
        $response->assertSee('Pelunasan Sisa Pembayaran');
        $response->assertSee('Upload Bukti Transfer Pelunasan:');

        // Booking dengan remaining not_required (100% DP)
        $unpaidBooking->update(['remaining_payment_status' => 'not_required']);
        $response2 = $this->actingAs($user)->get(route('user.bookings.ticket', $unpaidBooking));
        $response2->assertStatus(200);
        $response2->assertDontSee('Upload Bukti Transfer Pelunasan:');
    }

    public function test_user_can_upload_remaining_payment_proof()
    {
        Storage::fake('public');
        $user = User::where('role', 'user')->first();

        $booking = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('remaining_payment_status', 'unpaid')
            ->first();

        $this->assertNotNull($booking);

        $file = UploadedFile::fake()->image('bukti_pelunasan.jpg', 600, 800);

        $response = $this->actingAs($user)->post(route('user.bookings.upload-remaining-proof', $booking), [
            'remaining_proof' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('pending_verification', $booking->remaining_payment_status);
        $this->assertEquals('qris_transfer', $booking->remaining_payment_method);
        $this->assertNotNull($booking->remaining_proof_path);
        $this->assertNotNull($booking->remaining_uploaded_at);
        Storage::disk('public')->assertExists($booking->remaining_proof_path);
    }

    public function test_staff_can_view_remaining_verification_tab()
    {
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;

        $response = $this->actingAs($staff)->get(route('staff.bookings.index', ['status' => 'remaining']));
        $response->assertStatus(200);
        $response->assertSee('Verifikasi Pelunasan');
        $response->assertSee('IF-BKG-113');
    }

    public function test_staff_can_confirm_remaining_payment()
    {
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;

        $booking = Booking::where('store_id', $store->id)
            ->where('remaining_payment_status', 'pending_verification')
            ->first();

        $this->assertNotNull($booking);

        $response = $this->actingAs($staff)->patch(route('staff.bookings.confirm-remaining', $booking));
        $response->assertRedirect(route('staff.bookings.index', ['status' => 'remaining']));
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('paid', $booking->remaining_payment_status);
        $this->assertNotNull($booking->remaining_verified_at);
        $this->assertEquals($staff->id, $booking->remaining_verified_by);
        $this->assertEquals($booking->remaining_amount, (float) $booking->remaining_amount_received);
    }

    public function test_staff_can_reject_remaining_payment_with_reason()
    {
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;

        $booking = Booking::where('store_id', $store->id)
            ->where('remaining_payment_status', 'pending_verification')
            ->first();

        $this->assertNotNull($booking);

        // Validation error jika alasan kosong
        $invalidResponse = $this->actingAs($staff)->patch(route('staff.bookings.reject-remaining', $booking), [
            'remaining_rejection_reason' => '',
        ]);
        $invalidResponse->assertSessionHasErrors('remaining_rejection_reason');

        // Berhasil ditolak
        $response = $this->actingAs($staff)->patch(route('staff.bookings.reject-remaining', $booking), [
            'remaining_rejection_reason' => 'Nominal transfer pelunasan kurang Rp 5.000.',
        ]);

        $response->assertRedirect(route('staff.bookings.index', ['status' => 'remaining']));
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('unpaid', $booking->remaining_payment_status);
        $this->assertEquals('Nominal transfer pelunasan kurang Rp 5.000.', $booking->remaining_rejection_reason);
    }

    public function test_staff_can_confirm_cash_remaining_at_store_via_web_and_ajax()
    {
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;

        $booking = Booking::where('store_id', $store->id)
            ->where('remaining_payment_status', 'unpaid')
            ->first();

        $this->assertNotNull($booking);

        // 1. AJAX request (seperti dipanggil dari modal Scan QR)
        $ajaxResponse = $this->actingAs($staff)->postJson(route('staff.bookings.cash-remaining', $booking), [
            'remaining_amount_received' => $booking->remaining_amount,
        ]);

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'success' => true,
        ]);

        $booking->refresh();
        $this->assertEquals('paid', $booking->remaining_payment_status);
        $this->assertEquals('cash', $booking->remaining_payment_method);
        $this->assertEquals($booking->remaining_amount, (float) $booking->remaining_amount_received);
    }

    public function test_scan_qr_checkin_returns_remaining_payment_details()
    {
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;

        $booking = Booking::where('store_id', $store->id)
            ->where('status', 'confirmed')
            ->first();

        $response = $this->actingAs($staff)->postJson(route('staff.scan.check-in'), [
            'qr_token' => $booking->qr_token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'booking_code',
                'customer_name',
                'remaining_to_pay',
                'remaining_amount',
                'remaining_payment_status',
            ],
        ]);
    }

    public function test_staff_cannot_complete_booking_if_remaining_payment_is_unpaid()
    {
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;

        // Buat booking checked_in dengan sisa belum lunas (unpaid)
        $booking = Booking::where('store_id', $store->id)
            ->where('status', 'checked_in')
            ->first();

        $booking->update([
            'remaining_payment_status' => 'unpaid',
        ]);

        // Coba complete saat remaining_payment_status = unpaid
        $response = $this->actingAs($staff)->patch(route('staff.bookings.complete', $booking));
        $response->assertRedirect(route('staff.bookings.index', ['status' => 'active']));
        $response->assertSessionHas('error');
        $this->assertEquals('checked_in', $booking->fresh()->status);

        // Tandai lunas via tunai
        $booking->update([
            'remaining_payment_status' => 'paid',
            'remaining_payment_method' => 'cash',
        ]);

        // Sekarang boleh complete
        $successResponse = $this->actingAs($staff)->patch(route('staff.bookings.complete', $booking));
        $successResponse->assertRedirect(route('staff.bookings.index', ['status' => 'active']));
        $successResponse->assertSessionHas('success');
        $this->assertEquals('completed', $booking->fresh()->status);
    }

    public function test_staff_dashboard_displays_pending_remaining_banner()
    {
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Perhatian: Pelunasan Menunggu Verifikasi');
        $response->assertSee('pelunasan menunggu verifikasi Anda');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Slot;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromptTwoStaffModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_staff_without_store_sees_cta_and_cannot_access_store_exists_routes()
    {
        // Staf approved tapi belum punya toko
        $staffWithoutStore = User::create([
            'name' => 'Staf Baru Approved',
            'email' => 'staf.notstore@ifind.id',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'is_active' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);

        // Akses dashboard: sukses dan melihat CTA
        $dashResponse = $this->actingAs($staffWithoutStore)->get(route('staff.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Lengkapi Profil Toko Sekarang');

        // Akses slots.index: dicegat oleh middleware store.exists dan diredirect ke store.create
        $slotResponse = $this->actingAs($staffWithoutStore)->get(route('staff.slots.index'));
        $slotResponse->assertRedirect(route('staff.store.create'));
        $slotResponse->assertSessionHas('warning', 'Lengkapi profil toko Anda terlebih dahulu.');
    }

    public function test_staff_can_create_store_profile_self_service()
    {
        $staffWithoutStore = User::create([
            'name' => 'Staf Kafe Mandiri',
            'email' => 'staf.mandiri@ifind.id',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'is_active' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);

        $category = Category::first();

        $createResponse = $this->actingAs($staffWithoutStore)->get(route('staff.store.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Pendaftaran Profil Toko Baru');

        // Submit form create store
        $storeResponse = $this->actingAs($staffWithoutStore)->post(route('staff.store.store'), [
            'name' => 'Kafe Mandiri Senja',
            'category_id' => $category->id,
            'description' => 'Kafe nyaman untuk kerja dan nugas.',
            'address' => 'Jl. Kebon Jeruk No. 88',
            'city' => 'Jakarta Barat',
            'phone' => '081299998888',
            'opening_hours' => [
                'monday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                'tuesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ],
        ]);

        $storeResponse->assertRedirect(route('staff.store.edit'));

        // Pastikan user_id otomatis terisi dari akun staf yang login
        $this->assertDatabaseHas('stores', [
            'user_id' => $staffWithoutStore->id,
            'name' => 'Kafe Mandiri Senja',
            'status' => 'approved',
            'is_active' => true,
        ]);

        // Akses create kembali akan otomatis redirect ke edit (mencegah 1 staf punya 2 toko)
        $recreateResponse = $this->actingAs($staffWithoutStore->fresh())->get(route('staff.store.create'));
        $recreateResponse->assertRedirect(route('staff.store.edit'));
    }

    public function test_staff_dashboard_shows_real_metrics()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Titik Temu Coffee & Study Space');
        $response->assertSee('Booking Masuk Hari Ini');
        $response->assertSee('Dikonfirmasi Hari Ini');
        $response->assertSee('Sisa Kapasitas Hari Ini');
    }

    public function test_bulk_generate_slots_without_duplicate_errors()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $store = $staff->store;

        $startDate = Carbon::today()->addDays(10)->toDateString();
        $endDate = Carbon::today()->addDays(12)->toDateString();

        $response = $this->actingAs($staff)->post(route('staff.slots.bulk-generate'), [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'start_time' => '10:00',
            'end_time' => '14:00',
            'duration_minutes' => 120, // 2 slots per day: 10-12, 12-14
            'capacity' => 10,
        ]);

        $response->assertRedirect(route('staff.slots.index'));

        // Cek bahwa slot terbuat di database
        $this->assertDatabaseHas('slots', [
            'store_id' => $store->id,
            'date' => $startDate,
            'start_time' => '10:00',
            'end_time' => '12:00',
            'capacity' => 10,
        ]);

        // Jalankan lagi dengan parameter sama, tidak boleh duplicate error (firstOrCreate skips)
        $reResponse = $this->actingAs($staff)->post(route('staff.slots.bulk-generate'), [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'start_time' => '10:00',
            'end_time' => '14:00',
            'duration_minutes' => 120,
            'capacity' => 10,
        ]);
        $reResponse->assertRedirect(route('staff.slots.index'));
    }

    public function test_booking_confirmation_capacity_check_and_observer_status_update()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $store = $staff->store;
        $user = User::where('role', 'user')->first();

        // Buat slot dengan kapasitas 4
        $slot = Slot::create([
            'store_id' => $store->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'capacity' => 4,
            'booked_seats' => 0,
            'status' => 'available',
        ]);

        // 1. Booking meminta 5 kursi (melebihi kapasitas slot 4)
        $overBooking = Booking::create([
            'booking_code' => 'OVER-001',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => $slot->date,
            'seat_count' => 5,
            'status' => 'pending',
        ]);

        $overConfirmResponse = $this->actingAs($staff)->patch(route('staff.bookings.confirm', $overBooking));
        $overConfirmResponse->assertSessionHas('error');
        $this->assertEquals('pending', $overBooking->fresh()->status);
        $this->assertEquals(0, $slot->fresh()->booked_seats);

        // 2. Booking valid sebesar 4 kursi (pas memenuhi kapasitas)
        $validBooking = Booking::create([
            'booking_code' => 'VALID-002',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => $slot->date,
            'seat_count' => 4,
            'status' => 'pending',
        ]);

        $validConfirmResponse = $this->actingAs($staff)->patch(route('staff.bookings.confirm', $validBooking));
        $validConfirmResponse->assertRedirect(route('staff.bookings.index', ['status' => 'confirmed']));

        $this->assertEquals('confirmed', $validBooking->fresh()->status);
        $this->assertEquals(4, $slot->fresh()->booked_seats);
        // Observer harus otomatis mengubah status menjadi 'full'
        $this->assertEquals('full', $slot->fresh()->status);
    }

    public function test_booking_rejection_requires_reason()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $pendingBooking = Booking::where('store_id', $staff->store->id)->where('status', 'pending')->first();

        // Reject tanpa alasan harus gagal validasi
        $failResponse = $this->actingAs($staff)->patch(route('staff.bookings.reject', $pendingBooking), [
            'rejection_reason' => '',
        ]);
        $failResponse->assertSessionHasErrors(['rejection_reason']);

        // Reject dengan alasan valid
        $successResponse = $this->actingAs($staff)->patch(route('staff.bookings.reject', $pendingBooking), [
            'rejection_reason' => 'Kafe dibooking untuk event privat.',
        ]);
        $successResponse->assertRedirect(route('staff.bookings.index', ['status' => 'pending']));

        $this->assertEquals('rejected', $pendingBooking->fresh()->status);
        $this->assertEquals('Kafe dibooking untuk event privat.', $pendingBooking->fresh()->rejection_reason);
    }

    public function test_chat_interaction_and_ajax_polling()
    {
        $staff = User::where('email', 'arief.staff@ifind.id')->first();
        $store = $staff->store;

        // Chat index
        $indexResponse = $this->actingAs($staff)->get(route('staff.chat.index'));
        $indexResponse->assertStatus(200);

        // Cari conversation customer
        $customerConv = Conversation::where('store_id', $store->id)->first();
        $this->assertNotNull($customerConv);

        // Show chat
        $showResponse = $this->actingAs($staff)->get(route('staff.chat.show', $customerConv));
        $showResponse->assertStatus(200);

        // Kirim pesan
        $sendResponse = $this->actingAs($staff)->post(route('staff.chat.send', $customerConv), [
            'message' => 'Halo pelanggan, meja Anda sudah kami siapkan.',
        ]);
        $sendResponse->assertRedirect(route('staff.chat.show', $customerConv));

        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $customerConv->id,
            'sender_id' => $staff->id,
            'message' => 'Halo pelanggan, meja Anda sudah kami siapkan.',
        ]);

        // AJAX Polling
        $pollResponse = $this->actingAs($staff)->getJson(route('staff.chat.poll', ['conversation' => $customerConv->id, 'last_id' => 0]));
        $pollResponse->assertStatus(200);
        $pollResponse->assertJsonStructure(['messages']);
    }

    public function test_multi_tenant_isolation_staff_a_cannot_modify_staff_b_data()
    {
        $staffA = User::where('email', 'arief.staff@ifind.id')->first();

        // Buat Staf B dengan toko miliknya sendiri
        $staffB = User::create([
            'name' => 'Staf Toko B',
            'email' => 'staf.b@ifind.id',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'is_active' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);

        $storeB = Store::create([
            'user_id' => $staffB->id,
            'category_id' => Category::first()->id,
            'name' => 'Toko B Independent',
            'slug' => 'toko-b-independent',
            'address' => 'Jl. Tebet No. 99',
            'city' => 'Jakarta Selatan',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $slotB = Slot::create([
            'store_id' => $storeB->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'capacity' => 10,
            'booked_seats' => 0,
            'status' => 'available',
        ]);

        $bookingB = Booking::create([
            'booking_code' => 'BKG-B-001',
            'user_id' => User::where('role', 'user')->first()->id,
            'store_id' => $storeB->id,
            'slot_id' => $slotB->id,
            'booking_date' => $slotB->date,
            'seat_count' => 2,
            'status' => 'pending',
        ]);

        // Staf A mencoba akses/edit slot Toko B -> 403 Forbidden
        $slotAccessResponse = $this->actingAs($staffA)->get(route('staff.slots.edit', $slotB));
        $slotAccessResponse->assertStatus(403);

        $slotDeleteResponse = $this->actingAs($staffA)->delete(route('staff.slots.destroy', $slotB));
        $slotDeleteResponse->assertStatus(403);

        // Staf A mencoba konfirmasi booking Toko B -> 403 Forbidden
        $bookingConfirmResponse = $this->actingAs($staffA)->patch(route('staff.bookings.confirm', $bookingB));
        $bookingConfirmResponse->assertStatus(403);
    }
}

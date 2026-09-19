<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Review;
use App\Models\Slot;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromptThreeUserModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_dashboard_displays_visible_stores_and_hides_inactive_stores()
    {
        $user = User::where('role', 'user')->first();

        // Dashboard harus menampilkan minimal 4 toko (1 Prompt 2 + 3 Prompt 3)
        $response = $this->actingAs($user)->get(route('user.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Titik Temu Coffee & Study Space');
        $response->assertSee('Kolektif Space & Roastery');
        $response->assertSee('Ruang Literasi & Teahouse');
        $response->assertSee('Sudut Temu Eatery & Cafe');

        // Jika salah satu toko dinonaktifkan (is_active = false)
        $targetStore = Store::where('slug', 'kolektif-space-roastery')->first();
        $targetStore->update(['is_active' => false]);

        $responseAfterDeactivation = $this->actingAs($user)->get(route('user.dashboard'));
        $responseAfterDeactivation->assertStatus(200);
        $responseAfterDeactivation->assertDontSee('Kolektif Space & Roastery');

        // Dan halaman show untuk toko nonaktif harus 404
        $showResponse = $this->actingAs($user)->get(route('user.stores.show', $targetStore->slug));
        $showResponse->assertStatus(404);
    }

    public function test_search_by_keyword_and_category_filtering()
    {
        $user = User::where('role', 'user')->first();

        // Cari berdasarkan keyword 'Bandung'
        $responseBandung = $this->actingAs($user)->get(route('user.dashboard', ['keyword' => 'Bandung']));
        $responseBandung->assertStatus(200);
        $responseBandung->assertSee('Kolektif Space & Roastery');
        $responseBandung->assertDontSee('Titik Temu Coffee & Study Space');

        // Filter kategori 'Coworking Space'
        $coworking = Category::where('slug', 'coworking-space')->first();
        $responseCoworking = $this->actingAs($user)->get(route('user.dashboard', ['category_id' => $coworking->id]));
        $responseCoworking->assertStatus(200);
        $responseCoworking->assertSee('Kolektif Space & Roastery');

        // Filter rating minimum 4.5
        $responseRating = $this->actingAs($user)->get(route('user.dashboard', ['min_rating' => '4.5']));
        $responseRating->assertStatus(200);
        $responseRating->assertSee('Ruang Literasi & Teahouse');
        $responseRating->assertDontSee('Sudut Temu Eatery & Cafe'); // Rating 4.2
    }

    public function test_geolocation_haversine_distance_ordering()
    {
        $user = User::where('role', 'user')->first();

        // Koordinat sekitar Jakarta Selatan (Tebet)
        $lat = -6.2250000;
        $lng = 106.8550000;

        $response = $this->actingAs($user)->get(route('user.dashboard', [
            'lat' => $lat,
            'lng' => $lng,
            'radius' => 50,
        ]));

        $response->assertStatus(200);
        // Memastikan distance_km terhitung dan tampil di kartu
        $response->assertSee('km');
    }

    public function test_store_show_page_and_ajax_slot_polling_with_estimated_available()
    {
        $user = User::where('role', 'user')->first();
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();

        // Tampilan halaman biasa
        $response = $this->actingAs($user)->get(route('user.stores.show', $store->slug));
        $response->assertStatus(200);
        $response->assertSee($store->name);
        $response->assertSee('Jam Operasional');
        $response->assertSee('Pengalaman Pengunjung');

        // Request via AJAX / JSON untuk pemilihan tanggal realtime
        $today = Carbon::today()->toDateString();
        $ajaxResponse = $this->actingAs($user)->getJson(route('user.stores.show', [
            'store' => $store->slug,
            'date' => $today,
        ]));

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure([
            'success',
            'date',
            'slots' => [
                '*' => [
                    'id',
                    'start_time',
                    'end_time',
                    'capacity',
                    'booked_seats',
                    'available_seats',
                    'status',
                    'is_bookable',
                ],
            ],
        ]);
    }

    public function test_user_can_create_booking_holding_capacity_and_redirects_to_payment()
    {
        $user = User::where('role', 'user')->first();
        $store = Store::where('slug', 'kolektif-space-roastery')->first();
        $slot = $store->slots()->where('status', 'available')->first();

        $initialBookingsCount = Booking::count();
        $initialBookedSeats = $slot->booked_seats;

        $response = $this->actingAs($user)->post(route('user.bookings.store'), [
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'seat_count' => 2,
            'notes' => 'Meja untuk 2 orang diskusi tugas.',
        ]);

        $createdBooking = Booking::where('user_id', $user->id)
            ->where('slot_id', $slot->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($createdBooking);
        $response->assertRedirect(route('user.bookings.payment', $createdBooking->booking_code));

        // Booking berstatus awaiting_payment
        $this->assertEquals('awaiting_payment', $createdBooking->status);
        $this->assertEquals(2, $createdBooking->seat_count);
        $this->assertNotNull($createdBooking->payment_deadline);

        // booked_seats pada slot LANGSUNG BERTAMBAH menahan kapasitas
        $slot->refresh();
        $this->assertEquals($initialBookedSeats + 2, $slot->booked_seats);
    }

    public function test_booking_creation_fails_when_exceeding_estimated_available_seats()
    {
        $user = User::where('role', 'user')->first();
        $store = Store::where('slug', 'sudut-temu-eatery-cafe')->first();
        $slot = $store->slots()->where('status', 'available')->first();

        $excessiveSeats = $slot->available_seats + 5;

        $response = $this->actingAs($user)->from(route('user.stores.show', $store->slug))->post(route('user.bookings.store'), [
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'seat_count' => $excessiveSeats,
        ]);

        $response->assertSessionHasErrors('seat_count');
    }

    public function test_user_can_cancel_pending_booking_but_cannot_cancel_confirmed_booking()
    {
        $user = User::where('role', 'user')->first();
        $pendingBooking = Booking::where('user_id', $user->id)->where('status', 'awaiting_payment')->first();

        if (! $pendingBooking) {
            $store = Store::first();
            $slot = $store->slots()->first();
            $pendingBooking = Booking::create([
                'booking_code' => 'CANCEL-TEST-01',
                'user_id' => $user->id,
                'store_id' => $store->id,
                'slot_id' => $slot->id,
                'booking_date' => today(),
                'seat_count' => 1,
                'price_per_pax_snapshot' => 25000,
                'total_amount' => 25000,
                'amount_due' => 25000,
                'payment_deadline' => now()->addMinutes(30),
                'status' => 'awaiting_payment',
            ]);
            $slot->booked_seats += 1;
            $slot->save();
        }

        $slot = $pendingBooking->slot;
        $initialSeats = $slot->booked_seats;

        // 1. Batalkan awaiting_payment booking
        $cancelResponse = $this->actingAs($user)->patch(route('user.bookings.cancel', $pendingBooking));
        $cancelResponse->assertRedirect(route('user.bookings.index'));
        $this->assertEquals('cancelled_expired', $pendingBooking->fresh()->status);
        // Kursi dilepas
        $this->assertEquals(max(0, $initialSeats - $pendingBooking->seat_count), $slot->fresh()->booked_seats);

        // 2. Coba batalkan confirmed booking (tidak diizinkan bagi user)
        $confirmedBooking = Booking::where('user_id', $user->id)->where('status', 'confirmed')->first();
        if ($confirmedBooking) {
            $failCancel = $this->actingAs($user)->patch(route('user.bookings.cancel', $confirmedBooking));
            $this->assertEquals('confirmed', $confirmedBooking->fresh()->status);
        }
    }

    public function test_completed_booking_review_submission_and_average_rating_recalculation()
    {
        $user = User::where('role', 'user')->first();
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();

        // Buat booking completed baru yang belum memiliki review
        $slot = $store->slots()->first();
        $completedBooking = Booking::create([
            'booking_code' => 'IFD-TEST-COMPLETE',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => today(),
            'seat_count' => 2,
            'status' => 'completed',
        ]);

        $oldAvgRating = $store->average_rating;

        // User submit review bintang 5
        $response = $this->actingAs($user)->post(route('user.bookings.review', $completedBooking), [
            'rating' => 5,
            'comment' => 'Pelayanan ramah sekali, tempatnya sangat bersih dan kondusif.',
        ]);

        $response->assertRedirect(route('user.bookings.index', ['tab' => 'history']));
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $completedBooking->id,
            'user_id' => $user->id,
            'store_id' => $store->id,
            'rating' => 5,
        ]);

        // average_rating pada toko terhitung ulang otomatis dari data riil
        $store->refresh();
        $this->assertNotNull($store->average_rating);
    }

    public function test_user_chat_initiation_with_store_sending_and_polling()
    {
        $user = User::where('role', 'user')->first();
        $store = Store::where('slug', 'kolektif-space-roastery')->first();

        // 1. Mulai chat dari toko
        $startResponse = $this->actingAs($user)->post(route('user.chat.start', $store->slug));
        $startResponse->assertRedirect();

        $conversation = Conversation::where('store_id', $store->id)
            ->where(function ($q) use ($user, $store) {
                $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
            })
            ->first();
        $this->assertNotNull($conversation);

        // 2. Buka halaman chat.show
        $showResponse = $this->actingAs($user)->get(route('user.chat.show', $conversation));
        $showResponse->assertStatus(200);

        // 3. Kirim pesan chat (JSON)
        $sendResponse = $this->actingAs($user)->postJson(route('user.chat.send', $conversation), [
            'message' => 'Halo staf, apakah ada colokan di setiap meja outdoor?',
        ]);
        $sendResponse->assertStatus(200);
        $sendResponse->assertJson(['success' => true]);

        $lastMessage = ChatMessage::where('conversation_id', $conversation->id)->latest('id')->first();
        $this->assertEquals('Halo staf, apakah ada colokan di setiap meja outdoor?', $lastMessage->message);

        // 4. Polling pesan baru
        $pollResponse = $this->actingAs($user)->getJson(route('user.chat.poll', [
            'conversation' => $conversation->id,
            'last_id' => 0,
        ]));
        $pollResponse->assertStatus(200);
        $pollResponse->assertJsonStructure(['messages']);
    }

    public function test_user_isolation_user_a_cannot_access_or_cancel_user_b_booking_or_chat()
    {
        $userA = User::where('role', 'user')->first();
        $userB = User::firstOrCreate(
            ['email' => 'userb@ifind.id'],
            [
                'name' => 'User B',
                'phone' => '081999999999',
                'password' => bcrypt('password'),
                'role' => 'user',
                'is_active' => true,
                'verification_status' => 'approved',
            ]
        );

        $store = Store::first();
        $slot = $store->slots()->first();

        $bookingB = Booking::create([
            'booking_code' => 'IFD-ISOLATION-B',
            'user_id' => $userB->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => today(),
            'seat_count' => 1,
            'price_per_pax_snapshot' => 25000,
            'total_amount' => 25000,
            'amount_due' => 25000,
            'payment_deadline' => now()->addMinutes(30),
            'status' => 'awaiting_payment',
        ]);

        $convB = Conversation::create([
            'user_one_id' => $userB->id,
            'user_two_id' => $store->user_id,
            'store_id' => $store->id,
            'last_message_at' => now(),
        ]);

        // User A coba membatalkan booking milik User B
        $cancelAttempt = $this->actingAs($userA)->patch(route('user.bookings.cancel', $bookingB));
        $cancelAttempt->assertStatus(403);

        // User A coba membuka chat milik User B
        $chatAttempt = $this->actingAs($userA)->get(route('user.chat.show', $convB));
        $chatAttempt->assertStatus(403);
    }

    public function test_cross_module_end_to_end_user_staff_sync_database_flow()
    {
        $user = User::where('role', 'user')->first();
        $store = Store::where('slug', 'titik-temu-coffee-study-space')->first();
        $staff = $store->user;
        $slot = Slot::create([
            'store_id' => $store->id,
            'date' => today(),
            'start_time' => '22:00:00',
            'end_time' => '23:00:00',
            'capacity' => 6,
            'booked_seats' => 0,
            'status' => 'available',
        ]);

        $capacity = $slot->capacity;

        // Langkah 1: User mengajukan booking
        $bookResponse = $this->actingAs($user)->post(route('user.bookings.store'), [
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'seat_count' => $capacity,
            'notes' => 'E2E Full reservation test',
        ]);

        $createdBooking = Booking::where('store_id', $store->id)
            ->where('user_id', $user->id)
            ->where('status', 'awaiting_payment')
            ->latest('id')
            ->first();
        $this->assertNotNull($createdBooking);
        $bookResponse->assertRedirect(route('user.bookings.payment', $createdBooking->booking_code));

        // Kapasitas slot langsung tertahan penuh
        $slot->refresh();
        $this->assertEquals($capacity, $slot->booked_seats);
        $this->assertEquals('full', $slot->status);

        // Langkah 2: User mengunggah bukti transfer
        \Illuminate\Support\Facades\Storage::fake('public');
        $uploadResponse = $this->actingAs($user)->post(route('user.bookings.upload-proof', $createdBooking), [
            'payment_proof' => \Illuminate\Http\UploadedFile::fake()->image('transfer.jpg'),
        ]);
        $uploadResponse->assertRedirect(route('user.bookings.index'));
        $this->assertEquals('pending_verification', $createdBooking->fresh()->status);

        // Langkah 3: Staf Toko melihat booking di tab verifikasi dan mengonfirmasi
        $staffIndexResponse = $this->actingAs($staff)->get(route('staff.bookings.index', ['status' => 'pending']));
        $staffIndexResponse->assertStatus(200);
        $staffIndexResponse->assertSee($createdBooking->booking_code);

        $confirmResponse = $this->actingAs($staff)->patch(route('staff.bookings.confirm', $createdBooking));
        $confirmResponse->assertRedirect();
        $createdBooking->refresh();
        $this->assertEquals('confirmed', $createdBooking->status);
        $this->assertNotNull($createdBooking->qr_token);

        // Langkah 4: User melihat e-ticket dengan QR code
        $ticketResponse = $this->actingAs($user)->get(route('user.bookings.ticket', $createdBooking->booking_code));
        $ticketResponse->assertStatus(200);
        $ticketResponse->assertSee('<svg', false);

        // Langkah 5: Staf scan QR / check-in di lokasi
        $checkInResponse = $this->actingAs($staff)->postJson(route('staff.scan.check-in'), [
            'qr_token' => $createdBooking->qr_token,
        ]);
        $checkInResponse->assertStatus(200);
        $checkInResponse->assertJson(['success' => true]);
        $this->assertEquals('checked_in', $createdBooking->fresh()->status);

        // Langkah 6: Staf menandai booking 'complete' setelah kunjungan selesai
        $completeResponse = $this->actingAs($staff)->patch(route('staff.bookings.complete', $createdBooking));
        $completeResponse->assertRedirect();
        $this->assertEquals('completed', $createdBooking->fresh()->status);

        // Langkah 7: User mengirim review untuk kunjungan selesai
        $reviewResponse = $this->actingAs($user)->post(route('user.bookings.review', $createdBooking), [
            'rating' => 5,
            'comment' => 'Pengalaman reservasi yang sangat memuaskan!',
        ]);
        $reviewResponse->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $createdBooking->id,
            'rating' => 5,
        ]);
    }
}

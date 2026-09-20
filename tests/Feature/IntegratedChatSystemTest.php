<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Slot;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegratedChatSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Checklist 1: Migration type & backfill
     */
    public function test_migration_and_backfill_sets_correct_types()
    {
        $customerConv = Conversation::whereNotNull('store_id')->first();
        $adminConv = Conversation::whereNull('store_id')->first();

        $this->assertNotNull($customerConv);
        $this->assertNotNull($adminConv);

        $this->assertEquals('user_staff', $customerConv->type);
        $this->assertEquals('staff_admin', $adminConv->type);
    }

    /**
     * Checklist 2: Panel pesan lama langsung tampil saat halaman dimuat (server-rendered)
     */
    public function test_server_rendered_messages_appear_on_initial_page_load()
    {
        $customerConv = Conversation::where('type', 'user_staff')->first();
        $firstMessage = $customerConv->messages()->first();
        $this->assertNotNull($firstMessage);

        // Akses dari user
        $user = $customerConv->userOne->role === 'user' ? $customerConv->userOne : $customerConv->userTwo;
        $responseUser = $this->actingAs($user)->get(route('user.chat.show', $customerConv));
        $responseUser->assertStatus(200);
        $responseUser->assertSee($firstMessage->message, false);

        // Akses dari staf
        $staff = $customerConv->userOne->role === 'staff' ? $customerConv->userOne : $customerConv->userTwo;
        $responseStaff = $this->actingAs($staff)->get(route('staff.chat.show', $customerConv));
        $responseStaff->assertStatus(200);
        $responseStaff->assertSee($firstMessage->message, false);

        // Akses admin chat show
        $adminConv = Conversation::where('type', 'staff_admin')->first();
        $adminMessage = $adminConv->messages()->first();
        $admin = User::where('role', 'admin')->first();
        $responseAdmin = $this->actingAs($admin)->get(route('admin.chat.show', $adminConv));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee($adminMessage->message, false);
    }

    /**
     * Checklist 3: Kirim pesan baru via AJAX & Web
     */
    public function test_send_message_creates_message_and_returns_clean_response()
    {
        $customerConv = Conversation::where('type', 'user_staff')->first();
        $user = $customerConv->userOne->role === 'user' ? $customerConv->userOne : $customerConv->userTwo;

        // AJAX Send
        $responseJson = $this->actingAs($user)->postJson(route('user.chat.send', $customerConv), [
            'message' => 'Pesan baru dari customer via AJAX.',
        ]);

        $responseJson->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => [
                    'message' => 'Pesan baru dari customer via AJAX.',
                    'sender_id' => $user->id,
                    'is_me' => true,
                ],
            ]);

        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $customerConv->id,
            'message' => 'Pesan baru dari customer via AJAX.',
        ]);
    }

    /**
     * Checklist 4: Polling messages
     */
    public function test_polling_returns_new_messages_and_marks_them_read()
    {
        $customerConv = Conversation::where('type', 'user_staff')->first();
        $user = $customerConv->userOne->role === 'user' ? $customerConv->userOne : $customerConv->userTwo;
        $staff = $customerConv->userOne->role === 'staff' ? $customerConv->userOne : $customerConv->userTwo;

        $lastId = $customerConv->messages()->max('id') ?? 0;

        // User kirim pesan baru
        $msg = ChatMessage::create([
            'conversation_id' => $customerConv->id,
            'sender_id' => $user->id,
            'message' => 'Pertanyaan polling test',
            'is_read' => false,
        ]);

        // Staf melakukan polling dengan after_id = lastId
        $pollResponse = $this->actingAs($staff)->getJson(route('staff.chat.poll', [
            'conversation' => $customerConv->id,
            'after_id' => $lastId,
        ]));

        $pollResponse->assertStatus(200)
            ->assertJsonFragment([
                'id' => $msg->id,
                'message' => 'Pertanyaan polling test',
                'is_me' => false,
            ]);

        // Pastikan sudah ditandai dibaca
        $this->assertTrue($msg->fresh()->is_read);
    }

    /**
     * Checklist 5: User mengirim pesan dari detail toko -> muncul di tab Chat Pelanggan staf
     */
    public function test_user_starts_chat_from_store_appears_in_staff_customer_tab()
    {
        $store = Store::where('is_active', true)->where('status', 'approved')->first();
        $user = User::where('role', 'user')->first();
        $staff = $store->user;

        // User start chat
        $startResponse = $this->actingAs($user)->post(route('user.chat.start', $store->slug));
        $startResponse->assertRedirect();

        $conv = Conversation::where('type', 'user_staff')->where('store_id', $store->id)->first();
        $this->assertNotNull($conv);

        // Staf buka index tab customer
        $staffIndex = $this->actingAs($staff)->get(route('staff.chat.index', ['tab' => 'customer']));
        $staffIndex->assertStatus(200);
        $staffIndex->assertSee($user->name);

        // Pastikan tidak tampil di tab admin staf
        $staffAdminIndex = $this->actingAs($staff)->get(route('staff.chat.index', ['tab' => 'admin']));
        $staffAdminIndex->assertStatus(200);
        $staffAdminIndex->assertDontSee($user->name . ' · Booking di ' . $store->name);
    }

    /**
     * Checklist 6: Staf klik "Chat Customer" dari booking order -> percakapan terbuka
     */
    public function test_staff_can_start_chat_with_customer_who_has_booking()
    {
        $store = Store::first();
        $staff = $store->user;
        $customer = User::where('role', 'user')->first();

        // Buat booking jika belum ada
        $slot = Slot::where('store_id', $store->id)->first();
        Booking::firstOrCreate(
            [
                'store_id' => $store->id,
                'user_id' => $customer->id,
                'slot_id' => $slot->id,
            ],
            [
                'booking_code' => 'TESTCHAT01',
                'booking_date' => now()->toDateString(),
                'seat_count' => 2,
                'status' => 'confirmed',
            ]
        );

        $response = $this->actingAs($staff)->post(route('staff.chat.start-user', $customer));
        $response->assertRedirect();

        $conv = Conversation::where('type', 'user_staff')
            ->where('store_id', $store->id)
            ->where(function ($q) use ($customer, $staff) {
                $q->where('user_one_id', $customer->id)->orWhere('user_two_id', $customer->id);
            })->first();

        $this->assertNotNull($conv);
        $response->assertRedirect(route('staff.chat.show', ['conversation' => $conv->id, 'tab' => 'customer']));
    }

    /**
     * Checklist 7: Staf coba chat User yang BELUM PERNAH booking di tokonya -> 403
     */
    public function test_staff_cannot_start_chat_with_user_who_never_booked_in_their_store()
    {
        $store = Store::first();
        $staff = $store->user;

        // Buat user baru tanpa booking
        $unrelatedUser = User::create([
            'name' => 'Unrelated Customer',
            'email' => 'unrelated.customer@ifind.id',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->post(route('staff.chat.start-user', $unrelatedUser));
        $response->assertStatus(403);
    }

    /**
     * Checklist 8 & 9: Staf Hubungi Admin & Admin Chat Staf Ini
     */
    public function test_staff_contact_admin_and_admin_chat_start_flow()
    {
        $staff = User::where('role', 'staff')->where('verification_status', 'approved')->first();
        $admin = User::where('role', 'admin')->first();

        // 1. Staf hubungi admin
        $staffContact = $this->actingAs($staff)->post(route('staff.chat.contact-admin'));
        $staffContact->assertRedirect();

        $staffAdminConv = Conversation::where('type', 'staff_admin')->first();
        $this->assertNotNull($staffAdminConv);

        // 2. Admin lihat percakapan di admin.chat.index
        $adminIndex = $this->actingAs($admin)->get(route('admin.chat.index'));
        $adminIndex->assertStatus(200);
        $adminIndex->assertSee($staff->name);

        // 3. Admin klik "Chat Staf Ini"
        $adminStart = $this->actingAs($admin)->post(route('admin.chat.start', $staff));
        $adminStart->assertRedirect(route('admin.chat.show', $staffAdminConv));
    }

    /**
     * Checklist 10: User mencoba akses percakapan staff_admin -> 403
     */
    public function test_user_cannot_access_staff_admin_conversation()
    {
        $adminConv = Conversation::where('type', 'staff_admin')->first();
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get(route('user.chat.show', $adminConv));
        $response->assertStatus(403);
    }

    /**
     * Checklist 11: Admin mencoba akses percakapan user_staff -> 403
     */
    public function test_admin_cannot_access_user_staff_conversation()
    {
        $userStaffConv = Conversation::where('type', 'user_staff')->first();
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.chat.show', $userStaffConv));
        $response->assertStatus(403);
    }

    /**
     * Checklist 12: Staf A tidak bisa mengakses percakapan milik Staf B
     */
    public function test_staff_a_cannot_access_staff_b_conversations()
    {
        $staffA = User::where('email', 'arief.staff@ifind.id')->first();

        // Buat Staf B dan Toko B
        $staffB = User::create([
            'name' => 'Staf Toko B Baru',
            'email' => 'staf.b.baru@ifind.id',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'is_active' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);

        $storeB = Store::create([
            'user_id' => $staffB->id,
            'category_id' => Category::first()->id,
            'name' => 'Kafe B Bintang',
            'slug' => 'kafe-b-bintang',
            'address' => 'Jl. Bintang No. 1',
            'city' => 'Jakarta Barat',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $user = User::where('role', 'user')->first();

        // Percakapan milik Toko B
        $convB = Conversation::create([
            'type' => 'user_staff',
            'user_one_id' => $user->id,
            'user_two_id' => $staffB->id,
            'store_id' => $storeB->id,
            'last_message_at' => now(),
        ]);

        // Staf A mencoba akses chat Toko B -> 403
        $response = $this->actingAs($staffA)->get(route('staff.chat.show', $convB));
        $response->assertStatus(403);

        // Staf A mencoba send ke chat Toko B -> 403
        $sendResponse = $this->actingAs($staffA)->post(route('staff.chat.send', $convB), [
            'message' => 'Menyusup ke chat orang lain',
        ]);
        $sendResponse->assertStatus(403);
    }

    /**
     * Checklist 13: Sisi Admin tidak menampilkan percakapan user_staff
     */
    public function test_admin_chat_index_never_contains_user_staff_conversations()
    {
        $admin = User::where('role', 'admin')->first();
        $userStaffConv = Conversation::where('type', 'user_staff')->first();

        $response = $this->actingAs($admin)->get(route('admin.chat.index'));
        $response->assertStatus(200);

        // Pastikan store name dari percakapan user_staff tidak muncul di view admin chat
        if ($userStaffConv && $userStaffConv->store) {
            $response->assertDontSee($userStaffConv->store->name . ' (');
        }
    }
}

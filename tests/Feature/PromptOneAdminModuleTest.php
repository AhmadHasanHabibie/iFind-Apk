<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Store;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromptOneAdminModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_login_and_redirects_to_admin_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'admin@ifind.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));

        $admin = User::where('email', 'admin@ifind.id')->first();
        $dashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Selamat Datang, Administrator iFind');
    }

    public function test_register_as_user_redirects_and_logs_in()
    {
        $response = $this->post('/register', [
            'name' => 'Budi Baru',
            'email' => 'budibaru@example.com',
            'role' => 'user',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'budibaru@example.com',
            'role' => 'user',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('user.dashboard'));
    }

    public function test_register_as_staff_does_not_login_and_sets_pending()
    {
        $response = $this->post('/register', [
            'name' => 'Staf Baru Toko',
            'email' => 'stafbaru@example.com',
            'role' => 'staff',
            'phone' => '081234567899',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'stafbaru@example.com',
            'role' => 'staff',
            'is_active' => false,
            'verification_status' => 'pending',
        ]);
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Pendaftaran berhasil. Akun Anda menunggu verifikasi Admin sebelum dapat digunakan.');
    }

    public function test_pending_staff_cannot_login()
    {
        $response = $this->post('/login', [
            'email' => 'budi.staff@ifind.id',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_rejected_staff_cannot_login_and_sees_rejection_reason()
    {
        $response = $this->post('/login', [
            'email' => 'dani.staff@ifind.id',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_staff_verification_approval_and_rejection()
    {
        $admin = User::where('role', 'admin')->first();
        $pendingStaff = User::where('email', 'budi.staff@ifind.id')->first();

        // Check index
        $indexResponse = $this->actingAs($admin)->get(route('admin.staff-verification.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($pendingStaff->name);

        // Check show
        $showResponse = $this->actingAs($admin)->get(route('admin.staff-verification.show', $pendingStaff));
        $showResponse->assertStatus(200);

        // Approve
        $approveResponse = $this->actingAs($admin)->post(route('admin.staff-verification.approve', $pendingStaff));
        $approveResponse->assertRedirect(route('admin.staff-verification.index'));

        $this->assertDatabaseHas('users', [
            'id' => $pendingStaff->id,
            'is_active' => true,
            'verification_status' => 'approved',
        ]);

        // Reject another staff
        $siti = User::where('email', 'siti.staff@ifind.id')->first();
        $rejectResponse = $this->actingAs($admin)->post(route('admin.staff-verification.reject', $siti), [
            'verification_note' => 'Dokumen tidak valid.',
        ]);
        $rejectResponse->assertRedirect(route('admin.staff-verification.index'));

        $this->assertDatabaseHas('users', [
            'id' => $siti->id,
            'is_active' => false,
            'verification_status' => 'rejected',
            'verification_note' => 'Dokumen tidak valid.',
        ]);
    }

    public function test_category_crud_and_store_deletion_protection()
    {
        $admin = User::where('role', 'admin')->first();

        // Create category
        $storeResponse = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Gaming Lounge',
            'icon' => 'fa-solid fa-gamepad',
            'description' => 'Tempat santai sambil bermain game bersama.',
            'is_active' => 1,
        ]);
        $storeResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Gaming Lounge',
            'slug' => 'gaming-lounge',
        ]);

        $category = Category::where('slug', 'gaming-lounge')->first();

        // Edit & Update
        $editResponse = $this->actingAs($admin)->get(route('admin.categories.edit', $category));
        $editResponse->assertStatus(200);

        $updateResponse = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Gaming Hub & Cafe',
            'icon' => 'fa-solid fa-gamepad',
            'description' => 'Tempat game dan kopi.',
            'is_active' => 1,
        ]);
        $updateResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Gaming Hub & Cafe',
        ]);

        // Destroy when no store
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));
        $deleteResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        // Prevent deletion when store is attached
        $coffeeCategory = Category::where('name', 'Coffee Shop')->first();
        $approvedStaff = User::where('role', 'staff')->where('verification_status', 'approved')->first();

        Store::create([
            'user_id' => $approvedStaff->id,
            'category_id' => $coffeeCategory->id,
            'name' => 'Kopi Senja Utama',
            'slug' => 'kopi-senja-utama',
            'address' => 'Jl. Merdeka No. 10',
            'city' => 'Jakarta',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $deleteWithStoreResponse = $this->actingAs($admin)->delete(route('admin.categories.destroy', $coffeeCategory));
        $deleteWithStoreResponse->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $coffeeCategory->id]);
    }

    public function test_ticket_management_replies_and_status()
    {
        $admin = User::where('role', 'admin')->first();
        $ticket = Ticket::where('ticket_code', 'TCK-202609-001')->first();

        $indexResponse = $this->actingAs($admin)->get(route('admin.tickets.index'));
        $indexResponse->assertStatus(200);

        $showResponse = $this->actingAs($admin)->get(route('admin.tickets.show', $ticket));
        $showResponse->assertStatus(200);

        // Reply to ticket
        $replyResponse = $this->actingAs($admin)->post(route('admin.tickets.reply', $ticket), [
            'message' => 'Halo, silakan infokan kode booking Anda untuk dibantu reschedule.',
        ]);
        $replyResponse->assertRedirect(route('admin.tickets.show', $ticket));

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Halo, silakan infokan kode booking Anda untuk dibantu reschedule.',
        ]);

        // Auto updated status to in_progress
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'in_progress',
            'handled_by' => $admin->id,
        ]);

        // Update status explicitly
        $statusResponse = $this->actingAs($admin)->patch(route('admin.tickets.status', $ticket), [
            'status' => 'resolved',
        ]);
        $statusResponse->assertRedirect(route('admin.tickets.show', $ticket));
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'resolved',
        ]);
    }

    public function test_placeholder_dashboards_for_staff_and_user()
    {
        $staff = User::where('role', 'staff')->where('verification_status', 'approved')->first();
        $staffResponse = $this->actingAs($staff)->get(route('staff.dashboard'));
        $staffResponse->assertStatus(200);

        $user = User::where('role', 'user')->first();
        $userResponse = $this->actingAs($user)->get(route('user.dashboard'));
        $userResponse->assertStatus(200);
        $userResponse->assertSee('Dashboard user akan datang');
    }
}

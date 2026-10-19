<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\PlatformSetting;
use App\Models\Slot;
use App\Models\Store;
use App\Models\TaxSettlement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaxSettlementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh --seed');
        Storage::fake('public');
    }

    private function getStaffAndStore(): array
    {
        $store = Store::whereHas('user')->first();
        $staff = $store->user;
        $slot = $store->slots()->first() ?? Slot::create([
            'store_id' => $store->id,
            'date' => Carbon::today(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'capacity' => 20,
            'booked_seats' => 0,
            'is_active' => true,
        ]);

        return [$staff, $store, $slot];
    }

    public function test_staff_can_view_tax_settlement_page_with_admin_qris_and_balance()
    {
        [$staff, $store, $slot] = $this->getStaffAndStore();
        $user = User::where('role', 'user')->first();

        // Buat booking terkonfirmasi untuk menghasilkan pajak 5%
        Booking::create([
            'booking_code' => 'IFD-TEST-TAX-01',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => Carbon::today(),
            'seat_count' => 2,
            'status' => 'confirmed',
            'subtotal' => 100000,
            'service_fee_percentage' => 5,
            'service_fee' => 5000,
            'total_amount' => 105000,
            'amount_due' => 105000,
        ]);

        $response = $this->actingAs($staff)->get(route('staff.tax-settlements.index'));

        $response->assertStatus(200);
        $response->assertSee('Setor Pajak');
        $response->assertSee('QRIS Resmi Admin iFind');
        $response->assertSee('5.000');
    }

    public function test_staff_cannot_submit_tax_settlement_without_proof_image()
    {
        [$staff, $store] = $this->getStaffAndStore();

        $response = $this->actingAs($staff)->post(route('staff.tax-settlements.store'), [
            'amount' => 5000,
            'notes' => 'Catatan tes tanpa bukti',
        ]);

        $response->assertSessionHasErrors(['proof_image']);
    }

    public function test_staff_can_submit_tax_settlement_with_proof_image()
    {
        [$staff, $store] = $this->getStaffAndStore();

        $proofFile = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 800);

        $response = $this->actingAs($staff)->post(route('staff.tax-settlements.store'), [
            'amount' => 15000,
            'proof_image' => $proofFile,
            'notes' => 'Penyetoran komisi minggu 1',
        ]);

        $response->assertRedirect(route('staff.tax-settlements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tax_settlements', [
            'store_id' => $store->id,
            'user_id' => $staff->id,
            'amount' => 15000,
            'status' => 'pending',
            'notes' => 'Penyetoran komisi minggu 1',
        ]);

        $settlement = TaxSettlement::latest()->first();
        $this->assertNotNull($settlement);
        Storage::disk('public')->assertExists($settlement->proof_path);
    }

    public function test_admin_can_view_settlements_list()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.tax-settlements.index'));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Setoran Pajak Toko');
    }

    public function test_admin_can_approve_tax_settlement_and_reduce_store_liability()
    {
        $admin = User::where('role', 'admin')->first();
        [$staff, $store, $slot] = $this->getStaffAndStore();
        $user = User::where('role', 'user')->first();

        // 1. Buat booking terkonfirmasi dengan pajak 10.000
        Booking::create([
            'booking_code' => 'IFD-TEST-TAX-02',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'slot_id' => $slot->id,
            'booking_date' => Carbon::today(),
            'seat_count' => 4,
            'status' => 'confirmed',
            'subtotal' => 200000,
            'service_fee_percentage' => 5,
            'service_fee' => 10000,
            'total_amount' => 210000,
            'amount_due' => 210000,
        ]);

        $store->refresh();
        $initialOutstanding = $store->outstanding_tax_amount;
        $this->assertGreaterThanOrEqual(10000, $initialOutstanding);

        // 2. Buat settlement pending 10.000
        $settlement = TaxSettlement::create([
            'settlement_code' => 'TAX-20261019-TEST01',
            'store_id' => $store->id,
            'user_id' => $staff->id,
            'amount' => 10000,
            'proof_path' => 'tax_settlements/proofs/dummy.jpg',
            'status' => 'pending',
        ]);

        // 3. Admin approve
        $response = $this->actingAs($admin)->post(route('admin.tax-settlements.approve', $settlement), [
            'admin_notes' => 'Dana sudah masuk ke rekening BCA admin.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $settlement->refresh();
        $this->assertEquals('approved', $settlement->status);
        $this->assertEquals($admin->id, $settlement->verified_by);
        $this->assertNotNull($settlement->verified_at);
        $this->assertEquals('Dana sudah masuk ke rekening BCA admin.', $settlement->admin_notes);

        $store->refresh();
        $this->assertEquals($initialOutstanding - 10000, $store->outstanding_tax_amount);
    }

    public function test_admin_can_reject_tax_settlement_with_reason()
    {
        $admin = User::where('role', 'admin')->first();
        [$staff, $store] = $this->getStaffAndStore();

        $settlement = TaxSettlement::create([
            'settlement_code' => 'TAX-20261019-TEST02',
            'store_id' => $store->id,
            'user_id' => $staff->id,
            'amount' => 5000,
            'proof_path' => 'tax_settlements/proofs/dummy.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.tax-settlements.reject', $settlement), [
            'rejection_reason' => 'Bukti buram dan tidak terbaca jelas.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $settlement->refresh();
        $this->assertEquals('rejected', $settlement->status);
        $this->assertEquals('Bukti buram dan tidak terbaca jelas.', $settlement->admin_notes);
    }

    public function test_admin_can_update_platform_qris_and_bank_settings()
    {
        $admin = User::where('role', 'admin')->first();

        $newQris = UploadedFile::fake()->image('new_admin_qris.png', 500, 500);

        $response = $this->actingAs($admin)->post(route('admin.tax-settlements.settings.update'), [
            'admin_qris_image' => $newQris,
            'admin_bank_name' => 'Bank Mandiri',
            'admin_bank_account_number' => '1370019283741',
            'admin_bank_account_holder' => 'PT iFind Teknologi Nusantara',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Bank Mandiri', PlatformSetting::get('admin_bank_name'));
        $this->assertEquals('1370019283741', PlatformSetting::get('admin_bank_account_number'));
        $this->assertEquals('PT iFind Teknologi Nusantara', PlatformSetting::get('admin_bank_account_holder'));
        $this->assertTrue(Storage::disk('public')->exists(PlatformSetting::get('admin_qris_path')));
    }
}

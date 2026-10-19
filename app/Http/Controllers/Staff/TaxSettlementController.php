<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\TaxSettlement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TaxSettlementController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;
        abort_if(! $store, 403, 'Toko belum dikonfigurasi.');

        $totalLiability = $store->total_tax_liability;
        $settledAmount = $store->settled_tax_amount;
        $pendingAmount = $store->pending_tax_amount;
        $outstandingAmount = $store->outstanding_tax_amount;

        $adminQrisPath = PlatformSetting::get('admin_qris_path');
        $adminQrisUrl = $adminQrisPath && Storage::disk('public')->exists($adminQrisPath)
            ? Storage::url($adminQrisPath)
            : null;

        $adminBankName = PlatformSetting::get('admin_bank_name', 'Bank Central Asia (BCA)');
        $adminBankAccountNumber = PlatformSetting::get('admin_bank_account_number', '8735029188');
        $adminBankAccountHolder = PlatformSetting::get('admin_bank_account_holder', 'PT iFind Layanan Indonesia');

        $settlements = TaxSettlement::where('store_id', $store->id)
            ->latest()
            ->paginate(10);

        return view('staff.tax-settlement.index', compact(
            'store',
            'totalLiability',
            'settledAmount',
            'pendingAmount',
            'outstandingAmount',
            'adminQrisUrl',
            'adminBankName',
            'adminBankAccountNumber',
            'adminBankAccountHolder',
            'settlements'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if(! $store, 403, 'Toko belum dikonfigurasi.');

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'amount.required' => 'Nominal setoran wajib diisi.',
            'amount.numeric' => 'Nominal setoran harus berupa angka.',
            'amount.min' => 'Nominal setoran minimal Rp 1.000.',
            'proof_image.required' => 'Bukti transfer pembayaran QRIS / Bank wajib diunggah.',
            'proof_image.image' => 'File bukti harus berupa gambar.',
            'proof_image.mimes' => 'Format bukti yang didukung: JPG, JPEG, PNG.',
            'proof_image.max' => 'Ukuran file bukti maksimal 2MB.',
        ]);

        $file = $request->file('proof_image');
        $ext = $file->getClientOriginalExtension();
        $filename = 'settlement_' . $store->id . '_' . time() . '_' . Str::random(6) . '.' . $ext;
        $path = $file->storeAs('tax_settlements/proofs', $filename, 'public');

        do {
            $code = 'TAX-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (TaxSettlement::where('settlement_code', $code)->exists());

        TaxSettlement::create([
            'settlement_code' => $code,
            'store_id' => $store->id,
            'user_id' => $request->user()->id,
            'amount' => $validated['amount'],
            'proof_path' => $path,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('staff.tax-settlements.index')
            ->with('success', 'Bukti transfer setoran pajak berhasil dikirim. Menunggu verifikasi dari Administrator iFind.');
    }
}

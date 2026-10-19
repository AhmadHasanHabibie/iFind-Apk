<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\Store;
use App\Models\TaxSettlement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TaxSettlementController extends Controller
{
    public function index(Request $request): View
    {
        $query = TaxSettlement::with(['store', 'user', 'verifiedBy'])->latest();

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Toko
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Search Kode Setoran / Nama Toko
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('settlement_code', 'like', "%{$search}%")
                    ->orWhereHas('store', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Metrik Summary
        $allSettlements = TaxSettlement::all();
        $totalApprovedAmount = $allSettlements->where('status', 'approved')->sum('amount');
        $totalPendingAmount = $allSettlements->where('status', 'pending')->sum('amount');
        $pendingCount = $allSettlements->where('status', 'pending')->count();
        $approvedCount = $allSettlements->where('status', 'approved')->count();

        $settlements = $query->paginate(15)->withQueryString();
        $stores = Store::orderBy('name')->get();

        return view('admin.tax-settlements.index', compact(
            'settlements',
            'stores',
            'totalApprovedAmount',
            'totalPendingAmount',
            'pendingCount',
            'approvedCount'
        ));
    }

    public function approve(Request $request, TaxSettlement $settlement): RedirectResponse
    {
        if ($settlement->status === 'approved') {
            return back()->with('warning', 'Setoran pajak ini sudah disetujui sebelumnya.');
        }

        $settlement->update([
            'status' => 'approved',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'admin_notes' => $request->admin_notes ?: null,
        ]);

        return back()->with('success', "Setoran pajak {$settlement->settlement_code} sebesar Rp " . number_format($settlement->amount, 0, ',', '.') . " berhasil disetujui.");
    }

    public function reject(Request $request, TaxSettlement $settlement): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan bukti setoran wajib diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $settlement->update([
            'status' => 'rejected',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'admin_notes' => $request->rejection_reason,
        ]);

        return back()->with('success', "Setoran pajak {$settlement->settlement_code} ditolak. Toko akan menerima pemberitahuan dan alasan penolakan.");
    }

    public function settings(): View
    {
        $adminQrisPath = PlatformSetting::get('admin_qris_path');
        $adminQrisUrl = $adminQrisPath && Storage::disk('public')->exists($adminQrisPath)
            ? Storage::url($adminQrisPath)
            : null;

        $adminBankName = PlatformSetting::get('admin_bank_name', 'Bank Central Asia (BCA)');
        $adminBankAccountNumber = PlatformSetting::get('admin_bank_account_number', '8735029188');
        $adminBankAccountHolder = PlatformSetting::get('admin_bank_account_holder', 'PT iFind Layanan Indonesia');

        return view('admin.tax-settlements.settings', compact(
            'adminQrisUrl',
            'adminBankName',
            'adminBankAccountNumber',
            'adminBankAccountHolder'
        ));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'admin_qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'admin_bank_name' => ['required', 'string', 'max:100'],
            'admin_bank_account_number' => ['required', 'string', 'max:50'],
            'admin_bank_account_holder' => ['required', 'string', 'max:100'],
        ], [
            'admin_qris_image.image' => 'File QRIS harus berupa gambar.',
            'admin_qris_image.mimes' => 'Format QRIS yang didukung: JPG, JPEG, PNG.',
            'admin_qris_image.max' => 'Ukuran QRIS maksimal 2MB.',
            'admin_bank_name.required' => 'Nama bank admin wajib diisi.',
            'admin_bank_account_number.required' => 'Nomor rekening admin wajib diisi.',
            'admin_bank_account_holder.required' => 'Nama pemilik rekening admin wajib diisi.',
        ]);

        if ($request->hasFile('admin_qris_image')) {
            $file = $request->file('admin_qris_image');
            $ext = $file->getClientOriginalExtension();
            $path = $file->storeAs('qris/admin', 'admin-qris-' . time() . '.' . $ext, 'public');
            PlatformSetting::set('admin_qris_path', $path);
        }

        PlatformSetting::set('admin_bank_name', $validated['admin_bank_name']);
        PlatformSetting::set('admin_bank_account_number', $validated['admin_bank_account_number']);
        PlatformSetting::set('admin_bank_account_holder', $validated['admin_bank_account_holder']);

        return back()->with('success', 'Pengaturan QRIS resmi dan rekening penerima admin berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status');
        $categoryId = $request->get('category_id');
        $search = $request->get('search');

        $query = Store::with(['user', 'category', 'photos'])
            ->withCount(['bookings', 'reviews'])
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($status && in_array($status, ['approved', 'pending', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $stores = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        $counts = [
            'all' => Store::count(),
            'approved' => Store::where('status', 'approved')->count(),
            'pending' => Store::where('status', 'pending')->count(),
            'rejected' => Store::where('status', 'rejected')->count(),
        ];

        return view('admin.stores.index', compact('stores', 'categories', 'status', 'categoryId', 'search', 'counts'));
    }

    public function show(Store $store): View
    {
        $store->load(['user', 'category', 'photos', 'facilities', 'slots' => fn($q) => $q->latest()->take(10)]);
        return view('admin.stores.show', compact('store'));
    }

    public function toggleActive(Store $store): RedirectResponse
    {
        $store->update([
            'is_active' => !$store->is_active,
        ]);

        $statusText = $store->is_active ? 'diaktifkan kembali' : 'dinonaktifkan / disuspend';

        return back()->with('success', "Toko '{$store->name}' berhasil {$statusText}.");
    }

    public function approve(Store $store): RedirectResponse
    {
        $store->update([
            'status' => 'approved',
            'is_active' => true,
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Toko '{$store->name}' berhasil disetujui (Approved).");
    }

    public function reject(Request $request, Store $store): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan toko wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $store->update([
            'status' => 'rejected',
            'is_active' => false,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Toko '{$store->name}' berhasil ditolak.");
    }
}

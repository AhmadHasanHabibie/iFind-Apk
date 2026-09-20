<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Store;
use App\Models\StorePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StoreProfileController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->store) {
            return redirect()->route('staff.store.edit');
        }

        $categories = Category::where('is_active', true)->get();
        $facilities = Facility::all();

        return view('staff.store.edit', [
            'store' => new Store(),
            'isCreate' => true,
            'categories' => $categories,
            'facilities' => $facilities,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->store) {
            return redirect()->route('staff.store.edit')
                ->with('info', 'Anda sudah memiliki toko terdaftar.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'phone' => ['nullable', 'string', 'max:25'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
            'opening_hours' => ['nullable', 'array'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'price_per_pax' => ['nullable', 'numeric', 'min:0'],
            'dp_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'payment_timeout_minutes' => ['nullable', 'integer', 'min:5', 'max:1440'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:100'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Cek validasi bank / QRIS jika ada harga
        $hasBank = ! empty($validated['bank_name']) || ! empty($validated['bank_account_number']) || ! empty($validated['bank_account_holder']);
        $hasQris = $request->hasFile('qris_image');

        if (! empty($validated['price_per_pax']) && ! $hasBank && ! $hasQris) {
            return back()->withErrors([
                'bank_name' => 'Minimal salah satu metode pembayaran wajib diisi: Informasi rekening bank ATAU upload gambar QRIS.',
            ])->withInput();
        }

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Store::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        // Format opening hours JSON
        $formattedHours = $this->buildOpeningHoursJson($request->input('opening_hours', []));

        $store = Store::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'],
            'city' => $validated['city'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'opening_hours' => $formattedHours,
            'status' => 'approved', // Auto approved for verified staff
            'is_active' => true,
            'average_rating' => 0.0,
            'price_per_pax' => $validated['price_per_pax'] ?? null,
            'dp_percentage' => $validated['dp_percentage'] ?? 100,
            'payment_timeout_minutes' => $validated['payment_timeout_minutes'] ?? 60,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_holder' => $validated['bank_account_holder'] ?? null,
        ]);

        // Handle QRIS image
        if ($request->hasFile('qris_image')) {
            $file = $request->file('qris_image');
            $ext = $file->getClientOriginalExtension();
            $path = $file->storeAs('qris', "{$store->id}.{$ext}", 'public');
            $store->update(['qris_image_path' => $path]);
        }

        // Sync facilities
        if (! empty($validated['facilities'])) {
            $store->facilities()->sync($validated['facilities']);
        }

        // Handle photos upload
        $this->handlePhotoUploads($request, $store);

        return redirect()->route('staff.store.edit')
            ->with('success', 'Profil toko Anda berhasil dibuat dan siap digunakan!');
    }

    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('staff.store.create');
        }

        $store->load(['photos', 'facilities']);
        $categories = Category::where('is_active', true)->get();
        $facilities = Facility::all();

        return view('staff.store.edit', [
            'store' => $store,
            'isCreate' => false,
            'categories' => $categories,
            'facilities' => $facilities,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('staff.store.create');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'phone' => ['nullable', 'string', 'max:25'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
            'opening_hours' => ['nullable', 'array'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'price_per_pax' => ['nullable', 'numeric', 'min:0'],
            'dp_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'payment_timeout_minutes' => ['nullable', 'integer', 'min:5', 'max:1440'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:100'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Cek jika toko punya slot aktif dan harga kosong
        $hasActiveSlots = $store->slots()->where('status', 'available')->whereDate('date', '>=', today())->exists();
        if ($hasActiveSlots && (is_null($validated['price_per_pax'] ?? null) || $validated['price_per_pax'] === '')) {
            return back()->withErrors([
                'price_per_pax' => 'Toko memiliki slot waktu aktif. Harga per orang (price_per_pax) wajib diisi.',
            ])->withInput();
        }

        // Cek minimal salah satu rekening atau QRIS terisi (jika harga diisi atau punya slot aktif)
        $hasBank = ! empty($validated['bank_name']) || ! empty($validated['bank_account_number']) || ! empty($validated['bank_account_holder']);
        $hasQris = $request->hasFile('qris_image') || ! empty($store->qris_image_path);

        if ((! empty($validated['price_per_pax']) || $hasActiveSlots) && ! $hasBank && ! $hasQris) {
            return back()->withErrors([
                'bank_name' => 'Minimal salah satu metode pembayaran wajib diisi: Informasi rekening bank ATAU upload gambar QRIS.',
            ])->withInput();
        }

        // Update slug if name changes
        $slug = $store->slug;
        if ($validated['name'] !== $store->name) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Store::where('slug', $slug)->where('id', '!=', $store->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
        }

        // Format opening hours JSON
        $formattedHours = $this->buildOpeningHoursJson($request->input('opening_hours', []));

        $updateData = [
            'name' => $validated['name'],
            'slug' => $slug,
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'],
            'city' => $validated['city'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'opening_hours' => $formattedHours,
            'price_per_pax' => $validated['price_per_pax'] ?? null,
            'dp_percentage' => $validated['dp_percentage'] ?? 100,
            'payment_timeout_minutes' => $validated['payment_timeout_minutes'] ?? 60,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_holder' => $validated['bank_account_holder'] ?? null,
        ];

        // Handle new QRIS image
        if ($request->hasFile('qris_image')) {
            $file = $request->file('qris_image');
            $ext = $file->getClientOriginalExtension();
            $path = $file->storeAs('qris', "{$store->id}.{$ext}", 'public');
            $updateData['qris_image_path'] = $path;
        }

        $store->update($updateData);

        // Sync facilities
        $store->facilities()->sync($validated['facilities'] ?? []);

        // Handle new photos upload
        $this->handlePhotoUploads($request, $store);

        return redirect()->route('staff.store.edit')
            ->with('success', 'Profil toko berhasil diperbarui.');
    }

    public function deletePhoto(Request $request, StorePhoto $photo): RedirectResponse
    {
        $user = $request->user();
        $store = $user->store;

        abort_if(! $store || $photo->store_id !== $store->id, 403, 'Akses ditolak.');

        if (Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $wasPrimary = $photo->is_primary;
        $photo->delete();

        // If primary photo was deleted, set next photo as primary if available
        if ($wasPrimary) {
            $nextPhoto = $store->photos()->first();
            if ($nextPhoto) {
                $nextPhoto->update(['is_primary' => true]);
            }
        }

        return redirect()->route('staff.store.edit')
            ->with('success', 'Foto toko berhasil dihapus.');
    }

    public function setPrimaryPhoto(Request $request, StorePhoto $photo): RedirectResponse
    {
        $user = $request->user();
        $store = $user->store;

        abort_if(! $store || $photo->store_id !== $store->id, 403, 'Akses ditolak.');

        // Set all to false, then set this to true
        $store->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return redirect()->route('staff.store.edit')
            ->with('success', 'Foto utama toko berhasil diperbarui.');
    }

    private function buildOpeningHoursJson(array $inputHours): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $formatted = [];

        foreach ($days as $day) {
            $dayData = $inputHours[$day] ?? [];
            $isClosed = isset($dayData['is_closed']) && (bool) $dayData['is_closed'];

            $formatted[$day] = [
                'open' => $isClosed ? '00:00' : ($dayData['open'] ?? '08:00'),
                'close' => $isClosed ? '00:00' : ($dayData['close'] ?? '22:00'),
                'is_closed' => $isClosed,
            ];
        }

        return $formatted;
    }

    private function handlePhotoUploads(Request $request, Store $store): void
    {
        if ($request->hasFile('photos')) {
            $hasExistingPrimary = $store->photos()->where('is_primary', true)->exists();

            foreach ($request->file('photos') as $index => $file) {
                $path = $file->store("stores/{$store->id}", 'public');

                StorePhoto::create([
                    'store_id' => $store->id,
                    'photo_path' => $path,
                    'is_primary' => ! $hasExistingPrimary && $index === 0,
                ]);

                if (! $hasExistingPrimary && $index === 0) {
                    $hasExistingPrimary = true;
                }
            }
        }
    }
}

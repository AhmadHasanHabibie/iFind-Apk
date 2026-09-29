<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Store;
use App\Models\StorePhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            'maps_url' => ['nullable', 'string'],
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

        // Auto-extract coordinates if empty but maps_url is given
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;
        if ((empty($latitude) || empty($longitude)) && ! empty($request->maps_url)) {
            $coords = $this->extractCoordinatesFromUrl($request->maps_url);
            if ($coords) {
                $latitude = $coords['lat'];
                $longitude = $coords['lng'];
            }
        }

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
            'latitude' => $latitude,
            'longitude' => $longitude,
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
            'maps_url' => ['nullable', 'string'],
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

        // Auto-extract coordinates if empty but maps_url is given
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;
        if ((empty($latitude) || empty($longitude)) && ! empty($request->maps_url)) {
            $coords = $this->extractCoordinatesFromUrl($request->maps_url);
            if ($coords) {
                $latitude = $coords['lat'];
                $longitude = $coords['lng'];
            }
        }

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
            'latitude' => $latitude,
            'longitude' => $longitude,
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

    /**
     * AJAX endpoint to parse Google Maps URL and return coordinates
     */
    public function parseMapsUrl(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|string']);
        $url = trim($request->url);

        $coords = $this->extractCoordinatesFromUrl($url);

        if ($coords) {
            return response()->json([
                'success' => true,
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
                'message' => 'Koordinat berhasil diekstrak dari Link Google Maps!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak dapat menemukan titik koordinat dari link tersebut. Pastikan link Google Maps valid (contoh: https://maps.app.goo.gl/... atau https://google.com/maps/place/...).',
        ], 422);
    }

    /**
     * Helper to extract coordinates from various Google Maps link formats
     */
    private function extractCoordinatesFromUrl(string $url): ?array
    {
        $url = trim($url);

        // Jika user menempelkan kode iframe embed (contoh: <iframe src="...">)
        if (preg_match('/<iframe\s+[^>]*src=["\']([^"\']+)["\']/i', $url, $iframeMatch)) {
            $url = html_entity_decode($iframeMatch[1]);
        }

        $decodedUrl = urldecode($url);

        // 1. Format DMS (contoh: 6°10'31.4"S 106°49'37.8"E)
        if (preg_match('/(\d+[\s°\xc2\xb0]+\d+[\s\'\xca\xbc]+[\d\.]+[\s"”\xc2\x94]*\s*[NSns])[\s,%2C\+]+(\d+[\s°\xc2\xb0]+\d+[\s\'\xca\xbc]+[\d\.]+[\s"”\xc2\x94]*\s*[EWew])/u', $decodedUrl, $dmsMatches)) {
            $lat = $this->parseDmsCoordinate($dmsMatches[1]);
            $lng = $this->parseDmsCoordinate($dmsMatches[2]);
            if ($lat !== null && $lng !== null && $this->isValidCoordinate($lat, $lng)) {
                return ['lat' => round($lat, 7), 'lng' => round($lng, 7)];
            }
        }

        // 2. Format koordinat langsung desimal (contoh: "-6.2088, 106.8456")
        if (preg_match('/^\s*(-?\d{1,2}(?:\.\d+)?)\s*,\s*(-?\d{1,3}(?:\.\d+)?)\s*$/', $url, $matches)) {
            $lat = (float) $matches[1];
            $lng = (float) $matches[2];
            if ($this->isValidCoordinate($lat, $lng)) {
                return ['lat' => $lat, 'lng' => $lng];
            }
        }

        // Cek langsung dari URL yang diberikan jika sudah mengandung parameter pin spesifik
        $directCoords = $this->extractCoordinatesFromTextPayloads([$url, $decodedUrl]);
        if ($directCoords && (str_contains($url, '!3d') || str_contains($url, '!2d') || str_contains($url, '/place/') || str_contains($url, 'query=') || str_contains($url, '?q='))) {
            return $directCoords;
        }

        $targetUrl = $url;
        $responseBody = '';
        $redirectHistory = [];

        // 3. Jika merupakan shortlink atau redirect URL (maps.app.goo.gl / goo.gl / page.link / bit.ly), ikuti redirect
        if (str_contains($url, 'goo.gl') || str_contains($url, 'maps.app.goo.gl') || str_contains($url, 'page.link') || str_contains($url, 'bit.ly') || !str_contains($url, 'google.com/maps')) {
            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                    ])
                    ->withOptions([
                        'allow_redirects' => [
                            'max' => 10,
                            'strict' => true,
                            'referer' => true,
                            'track_redirects' => true,
                        ],
                    ])
                    ->timeout(10)
                    ->get($url);

                $targetUrl = (string) $response->effectiveUri();
                $responseBody = $response->body();

                $history = $response->header('X-Guzzle-Redirect-History');
                if ($history) {
                    $redirectHistory = is_array($history) ? $history : explode(',', (string) $history);
                }

                // Cek jika dialihkan ke halaman persetujuan/consent Google
                if (str_contains($targetUrl, 'consent.google.com') && preg_match('/[?&]continue=([^&]+)/', $targetUrl, $contMatch)) {
                    $targetUrl = urldecode($contMatch[1]);
                }
            } catch (\Throwable $e) {
                Log::warning('Maps URL parse exception: ' . $e->getMessage(), ['url' => $url]);
            }
        }

        $textsToAnalyze = array_merge(
            [$targetUrl, urldecode($targetUrl), $responseBody, $url, $decodedUrl],
            $redirectHistory
        );

        $coords = $this->extractCoordinatesFromTextPayloads($textsToAnalyze);
        if ($coords) {
            return $coords;
        }

        return $directCoords;
    }

    /**
     * Parse text payloads with priority: Pin Marker > Embed Coordinates > Path > Query Params > Meta Tags > Fallback Viewport Center
     */
    private function extractCoordinatesFromTextPayloads(array $texts): ?array
    {
        $texts = array_filter(array_map('trim', $texts));

        // Prioritas 1: Marker Pin Tempat Google Maps !3d<lat>!4d<lng> (Akurasi Paling Tinggi)
        foreach ($texts as $txt) {
            if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $txt, $m)) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        // Prioritas 2: Embed pb parameter !2d<lng>!3d<lat> (Format pb embed: 2d adalah longitude, 3d adalah latitude)
        foreach ($texts as $txt) {
            if (preg_match('/!2d(-?\d+\.\d+)!3d(-?\d+\.\d+)/', $txt, $m)) {
                $lng = (float) $m[1];
                $lat = (float) $m[2];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        // Prioritas 3: Koordinat langsung di path URL /place/lat,lng atau /search/lat,lng
        foreach ($texts as $txt) {
            if (preg_match('/\/(?:place|search)\/(-?\d{1,2}\.\d+)[,\s%2C\+]+(-?\d{1,3}\.\d+)/i', $txt, $m)) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        // Prioritas 4: Query parameters (q=, query=, destination=, daddr=, ll=, markers=)
        foreach ($texts as $txt) {
            if (preg_match('/[?&](?:q|query|destination|daddr|ll|point|markers)=(-?\d{1,2}\.\d+)[,\s%2C\+]+(-?\d{1,3}\.\d+)/i', $txt, $m)) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        // Prioritas 5: URL static map / gambar peta (markers / center)
        foreach ($texts as $txt) {
            if (preg_match('/markers=(?:color:[^\|%]+\|)?(-?\d{1,2}\.\d+)[,%2C]+(-?\d{1,3}\.\d+)/i', $txt, $m)) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }
            if (preg_match('/center=(-?\d{1,2}\.\d+)[,%2C]+(-?\d{1,3}\.\d+)/i', $txt, $m)) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        // Prioritas 6: HTML metadata itemprop & JS initial state
        foreach ($texts as $txt) {
            if (preg_match('/itemprop="latitude"\s+content="(-?\d+\.\d+)"/i', $txt, $latM) &&
                preg_match('/itemprop="longitude"\s+content="(-?\d+\.\d+)"/i', $txt, $lngM)) {
                $lat = (float) $latM[1];
                $lng = (float) $lngM[1];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }

            if (preg_match('/\[null,null,(-?\d{1,2}\.\d+),(-?\d{1,3}\.\d+)\]/', $txt, $m)) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        // Prioritas 7: Format DMS dalam teks URL / metadata
        foreach ($texts as $txt) {
            if (preg_match('/(\d+[\s°\xc2\xb0]+\d+[\s\'\xca\xbc]+[\d\.]+[\s"”\xc2\x94]*\s*[NSns])[\s,%2C\+]+(\d+[\s°\xc2\xb0]+\d+[\s\'\xca\xbc]+[\d\.]+[\s"”\xc2\x94]*\s*[EWew])/u', $txt, $dmsMatches)) {
                $lat = $this->parseDmsCoordinate($dmsMatches[1]);
                $lng = $this->parseDmsCoordinate($dmsMatches[2]);
                if ($lat !== null && $lng !== null && $this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => round($lat, 7), 'lng' => round($lng, 7)];
                }
            }
        }

        // Prioritas 8: Fallback hanya jika tidak ada pin spesifik: Viewport Camera Center @lat,lng
        foreach ($texts as $txt) {
            if (preg_match('/@(-?\d{1,2}\.\d+),(-?\d{1,3}\.\d+)/', $txt, $m)) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];
                if ($this->isValidCoordinate($lat, $lng)) {
                    return ['lat' => $lat, 'lng' => $lng];
                }
            }
        }

        return null;
    }

    /**
     * Helper to validate coordinate range
     */
    private function isValidCoordinate(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180 && ($lat != 0.0 || $lng != 0.0);
    }

    /**
     * Helper to parse Degrees Minutes Seconds (DMS) string to decimal
     */
    private function parseDmsCoordinate(string $dms): ?float
    {
        if (preg_match('/(\d+)[\s°\xc2\xb0]+(\d+)[\s\'\xca\xbc]+([\d\.]+)[\s"”\xc2\x94]*\s*([NSEWnsew])/u', $dms, $m)) {
            $degrees = (float) $m[1];
            $minutes = (float) $m[2];
            $seconds = (float) $m[3];
            $direction = strtoupper($m[4]);
            $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
            if ($direction === 'S' || $direction === 'W') {
                $decimal *= -1;
            }
            return $decimal;
        }
        return null;
    }
}

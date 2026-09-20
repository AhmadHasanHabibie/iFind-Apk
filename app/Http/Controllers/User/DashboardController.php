<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
<<<<<<< HEAD
use App\Models\Facility;
=======
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
<<<<<<< HEAD
        $facilities = Facility::orderBy('name')->get();
=======
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d

        $query = Store::visible()
            ->with(['category', 'photos', 'facilities'])
            ->withCount('reviews');

        // Pencarian Keyword
        if ($request->filled('keyword')) {
            $keyword = '%' . trim($request->keyword) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('city', 'like', $keyword)
                    ->orWhere('address', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword);
            });
        }

        // Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

<<<<<<< HEAD
        // Filter Fasilitas (Checkbox multiple)
        if ($request->filled('facilities') && is_array($request->facilities)) {
            foreach ($request->facilities as $facilityId) {
                if (!empty($facilityId)) {
                    $query->whereHas('facilities', function ($q) use ($facilityId) {
                        $q->where('facilities.id', $facilityId);
                    });
                }
            }
        }

=======
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d
        // Filter Rating Minimum
        if ($request->filled('min_rating')) {
            $query->where('average_rating', '>=', (float) $request->min_rating);
        }

        // Filter & Perhitungan Jarak (Haversine) jika lokasi user tersedia
        $hasLocation = $request->filled('lat') && $request->filled('lng');
        if ($hasLocation) {
            $lat = (float) $request->lat;
            $lng = (float) $request->lng;

            // Membatasi nilai acos antara -1 dan 1 agar terhindar dari floating point precision error
            $haversine = '(6371 * acos(least(greatest(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)), -1), 1)))';

            $query->select('stores.*')
                ->selectRaw("{$haversine} AS distance_km", [$lat, $lng, $lat]);

            if ($request->filled('radius')) {
                $radius = (float) $request->radius;
                $query->havingRaw('distance_km <= ?', [$radius]);
            }

            // Letakkan store dengan koordinat null di urutan paling akhir
            $query->orderByRaw('CASE WHEN latitude IS NULL OR longitude IS NULL THEN 1 ELSE 0 END')
                ->orderBy('distance_km', 'asc');
        } else {
            // Default rekomendasi: urutkan dari average_rating tertinggi
            $query->orderByDesc('average_rating')
                ->latest();
        }

        $stores = $query->paginate(9)->withQueryString();

        return view('user.dashboard', [
            'stores' => $stores,
            'categories' => $categories,
<<<<<<< HEAD
            'facilities' => $facilities,
=======
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d
            'hasLocation' => $hasLocation,
        ]);
    }
}
<<<<<<< HEAD

=======
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d

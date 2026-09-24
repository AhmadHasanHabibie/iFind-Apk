<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $favorites = $user->favoriteStores()
            ->where('stores.is_active', true)
            ->where('stores.status', 'approved')
            ->with(['category', 'photos', 'facilities'])
            ->withCount('reviews')
            ->latest('favorites.created_at')
            ->paginate(9);

        return view('user.favorites.index', compact('favorites'));
    }

    public function toggle(Request $request, Store $store): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('store_id', $store->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
            $message = "{$store->name} dihapus dari daftar favorit.";
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'store_id' => $store->id,
            ]);
            $isFavorited = true;
            $message = "{$store->name} ditambahkan ke daftar favorit.";
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_favorited' => $isFavorited,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}

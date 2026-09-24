<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;
        $menus = $store->menus()
            ->when($request->category, fn($q, $cat) => $q->where('category', $cat))
            ->orderByDesc('is_recommended')
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('staff.menus.index', compact('store', 'menus'));
    }

    public function create(Request $request): View
    {
        $store = $request->user()->store;
        return view('staff.menus.create', compact('store'));
    }

    public function store(Request $request): RedirectResponse
    {
        $store = $request->user()->store;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:makanan,minuman,snack,paket_hemat,lainnya'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
            'is_recommended' => ['nullable', 'boolean'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store("menus/{$store->id}", 'public');
        }

        $store->menus()->create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'photo' => $photoPath,
            'is_available' => $request->boolean('is_available', true),
            'is_recommended' => $request->boolean('is_recommended', false),
        ]);

        return redirect()->route('staff.menus.index')
            ->with('success', "Menu '{$validated['name']}' berhasil ditambahkan ke katalog.");
    }

    public function edit(Request $request, Menu $menu): View
    {
        $store = $request->user()->store;
        abort_if($menu->store_id !== $store->id, 403, 'Akses menu tidak diizinkan.');

        return view('staff.menus.edit', compact('store', 'menu'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($menu->store_id !== $store->id, 403, 'Akses menu tidak diizinkan.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:makanan,minuman,snack,paket_hemat,lainnya'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
            'is_recommended' => ['nullable', 'boolean'],
        ]);

        $photoPath = $menu->photo;
        if ($request->hasFile('photo')) {
            if ($menu->photo && Storage::disk('public')->exists($menu->photo)) {
                Storage::disk('public')->delete($menu->photo);
            }
            $photoPath = $request->file('photo')->store("menus/{$store->id}", 'public');
        }

        $menu->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'photo' => $photoPath,
            'is_available' => $request->boolean('is_available', true),
            'is_recommended' => $request->boolean('is_recommended', false),
        ]);

        return redirect()->route('staff.menus.index')
            ->with('success', "Menu '{$menu->name}' berhasil diperbarui.");
    }

    public function toggleAvailable(Request $request, Menu $menu): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($menu->store_id !== $store->id, 403, 'Akses menu tidak diizinkan.');

        $menu->update(['is_available' => ! $menu->is_available]);

        $status = $menu->is_available ? 'Tersedia' : 'Habis';
        return back()->with('success', "Status menu '{$menu->name}' diubah menjadi {$status}.");
    }

    public function destroy(Request $request, Menu $menu): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($menu->store_id !== $store->id, 403, 'Akses menu tidak diizinkan.');

        if ($menu->photo && Storage::disk('public')->exists($menu->photo)) {
            Storage::disk('public')->delete($menu->photo);
        }

        $menuName = $menu->name;
        $menu->delete();

        return redirect()->route('staff.menus.index')
            ->with('success', "Menu '{$menuName}' berhasil dihapus.");
    }
}

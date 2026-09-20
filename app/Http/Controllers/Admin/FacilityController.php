<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(): View
    {
        $facilities = Facility::withCount('stores')->latest()->paginate(15);
        return view('admin.facilities.index', compact('facilities'));
    }

    public function create(): View
    {
        return view('admin.facilities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:facilities,name'],
            'icon' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama fasilitas wajib diisi.',
            'name.unique' => 'Nama fasilitas sudah ada.',
        ]);

        Facility::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: 'fa-solid fa-check',
        ]);

        return redirect()->route('admin.facilities.index')
            ->with('success', "Fasilitas '{$validated['name']}' berhasil ditambahkan.");
    }

    public function edit(Facility $facility): View
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:facilities,name,' . $facility->id],
            'icon' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama fasilitas wajib diisi.',
            'name.unique' => 'Nama fasilitas sudah ada.',
        ]);

        $facility->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: 'fa-solid fa-check',
        ]);

        return redirect()->route('admin.facilities.index')
            ->with('success', "Fasilitas '{$facility->name}' berhasil diperbarui.");
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        $name = $facility->name;
        $facility->stores()->detach();
        $facility->delete();

        return redirect()->route('admin.facilities.index')
            ->with('success', "Fasilitas '{$name}' berhasil dihapus.");
    }
}

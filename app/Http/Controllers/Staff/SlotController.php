<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Slot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SlotController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->addDays(14)->toDateString());

        $slots = Slot::where('store_id', $store->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        return view('staff.slots.index', [
            'slots' => $slots,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function create(): View
    {
        return view('staff.slots.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $store = $request->user()->store;

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ], [
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'date.after_or_equal' => 'Tanggal slot tidak boleh di masa lampau.',
        ]);

        // Cek unique (store_id, date, start_time)
        $exists = Slot::where('store_id', $store->id)
            ->where('date', $validated['date'])
            ->where('start_time', $validated['start_time'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'start_time' => 'Slot waktu pada tanggal dan jam mulai tersebut sudah ada.',
            ]);
        }

        Slot::create([
            'store_id' => $store->id,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'capacity' => $validated['capacity'],
            'booked_seats' => 0,
            'status' => 'available',
        ]);

        return redirect()->route('staff.slots.index')
            ->with('success', 'Slot waktu berhasil ditambahkan.');
    }

    public function bulkGenerate(Request $request): RedirectResponse
    {
        $store = $request->user()->store;

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'duration_minutes' => ['required', 'integer', 'in:30,60,90,120,180,240'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ], [
            'days.required' => 'Pilih minimal satu hari dalam seminggu.',
            'end_time.after' => 'Jam selesai operasional harus lebih besar dari jam mulai.',
        ]);

        $period = CarbonPeriod::create($validated['start_date'], $validated['end_date']);
        $targetDays = array_map('strtolower', $validated['days']);
        $duration = (int) $validated['duration_minutes'];
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($period as $date) {
            $dayName = strtolower($date->format('l')); // 'monday', etc.

            if (! in_array($dayName, $targetDays)) {
                continue;
            }

            $currentSlotStart = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $validated['start_time']);
            $dayEndTime = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $validated['end_time']);

            while ($currentSlotStart->copy()->addMinutes($duration)->lte($dayEndTime)) {
                $slotStartTime = $currentSlotStart->format('H:i');
                $slotEndTime = $currentSlotStart->copy()->addMinutes($duration)->format('H:i');

                $slot = Slot::firstOrCreate(
                    [
                        'store_id' => $store->id,
                        'date' => $date->format('Y-m-d'),
                        'start_time' => $slotStartTime,
                    ],
                    [
                        'end_time' => $slotEndTime,
                        'capacity' => $validated['capacity'],
                        'booked_seats' => 0,
                        'status' => 'available',
                    ]
                );

                if ($slot->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $skippedCount++;
                }

                $currentSlotStart->addMinutes($duration);
            }
        }

        return redirect()->route('staff.slots.index')
            ->with('success', "Generate slot selesai: {$createdCount} slot baru dibuat, {$skippedCount} slot dilewati karena sudah ada.");
    }

    public function edit(Slot $slot): View
    {
        $store = auth()->user()->store;
        abort_if($slot->store_id !== $store->id, 403, 'Akses ditolak.');

        return view('staff.slots.edit', compact('slot'));
    }

    public function update(Request $request, Slot $slot): RedirectResponse
    {
        $store = $request->user()->store;
        abort_if($slot->store_id !== $store->id, 403, 'Akses ditolak.');

        $validated = $request->validate([
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'in:available,closed'],
        ]);

        // Validasi: kapasitas baru tidak boleh lebih kecil dari booked_seats saat ini
        if ($validated['capacity'] < $slot->booked_seats) {
            throw ValidationException::withMessages([
                'capacity' => "Kapasitas tidak boleh lebih kecil dari kursi yang sudah terisi ({$slot->booked_seats} kursi).",
            ]);
        }

        $slot->capacity = $validated['capacity'];

        // Jika staf memilih closed, simpan closed. Jika available, biarkan Observer menentukan available/full
        if ($validated['status'] === 'closed') {
            $slot->status = 'closed';
        } else {
            $slot->status = 'available'; // Observer will update to full if booked_seats >= capacity
        }

        $slot->save();

        return redirect()->route('staff.slots.index')
            ->with('success', 'Slot waktu berhasil diperbarui.');
    }

    public function close(Slot $slot): RedirectResponse
    {
        $store = auth()->user()->store;
        abort_if($slot->store_id !== $store->id, 403, 'Akses ditolak.');

        $slot->status = 'closed';
        $slot->save();

        return redirect()->route('staff.slots.index')
            ->with('success', 'Slot waktu berhasil ditutup secara manual.');
    }

    public function destroy(Slot $slot): RedirectResponse
    {
        $store = auth()->user()->store;
        abort_if($slot->store_id !== $store->id, 403, 'Akses ditolak.');

        // Cegah hapus jika slot memiliki booking aktif (pending atau confirmed)
        $activeBookingsCount = $slot->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeBookingsCount > 0) {
            return redirect()->route('staff.slots.index')
                ->with('error', "Slot tidak dapat dihapus karena memiliki {$activeBookingsCount} booking aktif (pending/confirmed).");
        }

        $slot->delete();

        return redirect()->route('staff.slots.index')
            ->with('success', 'Slot waktu berhasil dihapus.');
    }
}

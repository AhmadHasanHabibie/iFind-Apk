<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with(['user', 'store', 'slot'])->latest('booking_date');

        // Filter Toko
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter Status
        if ($request->filled('status')) {
            if ($request->status === 'successful') {
                $query->whereIn('status', ['confirmed', 'checked_in', 'completed']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->end_date);
        }

        // Search Kode Booking atau Nama User
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('store', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Hitung Summary Metrics (Keseluruhan / Berdasarkan Filter)
        $metricQuery = clone $query;
        $allBookings = $metricQuery->get();

        $successfulBookings = $allBookings->whereIn('status', ['confirmed', 'checked_in', 'completed']);
        $totalCommission = $successfulBookings->sum('service_fee');
        $totalGross = $successfulBookings->sum('total_amount');
        $totalStoreNet = $successfulBookings->sum('subtotal');
        $totalTransactionsCount = $successfulBookings->count();

        // Komisi Pending (belum diverifikasi / masih menunggu bayar)
        $pendingCommission = $allBookings->whereIn('status', ['awaiting_payment', 'pending_verification'])->sum('service_fee');

        // Komisi Bulan ini (Global)
        $thisMonthCommission = Booking::whereIn('status', ['confirmed', 'checked_in', 'completed'])
            ->whereMonth('booking_date', Carbon::now()->month)
            ->whereYear('booking_date', Carbon::now()->year)
            ->sum('service_fee');

        $bookings = $query->paginate(15)->withQueryString();
        $stores = Store::orderBy('name')->get();

        return view('admin.commissions.index', compact(
            'bookings',
            'stores',
            'totalCommission',
            'totalGross',
            'totalStoreNet',
            'totalTransactionsCount',
            'pendingCommission',
            'thisMonthCommission'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Booking::with(['user', 'store', 'slot'])->latest('booking_date');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'successful') {
                $query->whereIn('status', ['confirmed', 'checked_in', 'completed']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->end_date);
        }

        $bookings = $query->get();

        $filename = 'rekap-biaya-layanan-komisi-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['REKAPITULASI BIAYA LAYANAN & KOMISI PLATFORM WEBSITE iFIND (5%)']);
            fputcsv($handle, ['Tanggal Ekspor: ' . now()->format('Y-m-d H:i:s') . ' WIB']);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Kode Booking',
                'Tanggal Booking',
                'Nama Pemesan',
                'Email Pemesan',
                'No. Telepon',
                'Toko / Merchant',
                'Jumlah Kursi',
                'Subtotal Toko (Rp)',
                'Biaya Layanan Website 5% (Rp)',
                'Total Bayar Pelanggan (Rp)',
                'Status Booking',
                'Status Pelunasan'
            ]);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->booking_code,
                    $b->booking_date->format('Y-m-d'),
                    $b->user->name ?? '-',
                    $b->user->email ?? '-',
                    $b->user->phone ?? '-',
                    $b->store->name ?? '-',
                    $b->seat_count,
                    $b->subtotal,
                    $b->service_fee,
                    $b->total_amount,
                    $b->status_label,
                    $b->remaining_payment_status
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

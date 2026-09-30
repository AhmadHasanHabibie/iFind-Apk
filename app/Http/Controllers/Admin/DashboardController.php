<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Store;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalStaff = User::where('role', 'staff')->count();
        $totalActiveStores = Store::where('status', 'approved')->where('is_active', true)->count();
        $totalBookings = Booking::count();
        $openTicketsCount = Ticket::where('status', 'open')->count();
        $pendingStaffCount = User::pendingStaff()->count();

        // 7 days booking trend
        $bookingDays = [];
        $bookingCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $bookingDays[] = $date->format('d M');
            $count = Booking::whereDate('booking_date', $date->format('Y-m-d'))->count();
            $bookingCounts[] = $count;
        }

        // Stores count per category
        $categories = Category::withCount('stores')->get();
        $categoryNames = $categories->pluck('name')->toArray();
        $categoryStoreCounts = $categories->pluck('stores_count')->toArray();

        // Biaya Layanan / Komisi Platform Website (5%)
        $confirmedBookingsQuery = Booking::whereIn('status', ['confirmed', 'checked_in', 'completed']);
        $totalCommission = (float) $confirmedBookingsQuery->sum('service_fee');
        $thisMonthCommission = (float) (clone $confirmedBookingsQuery)
            ->whereMonth('booking_date', Carbon::now()->month)
            ->whereYear('booking_date', Carbon::now()->year)
            ->sum('service_fee');
        $todayCommission = (float) (clone $confirmedBookingsQuery)
            ->whereDate('booking_date', Carbon::today())
            ->sum('service_fee');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStaff',
            'totalActiveStores',
            'totalBookings',
            'totalCommission',
            'thisMonthCommission',
            'todayCommission',
            'openTicketsCount',
            'pendingStaffCount',
            'bookingDays',
            'bookingCounts',
            'categoryNames',
            'categoryStoreCounts'
        ));
    }
}

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

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStaff',
            'totalActiveStores',
            'totalBookings',
            'openTicketsCount',
            'pendingStaffCount',
            'bookingDays',
            'bookingCounts',
            'categoryNames',
            'categoryStoreCounts'
        ));
    }
}

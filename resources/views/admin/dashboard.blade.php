@extends('layouts.admin')

@section('header_title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner & Alert Widget -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 rounded-2xl p-6 text-white shadow-sm flex flex-col justify-between">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    Sistem iFind v1.0
                </span>
                <h2 class="text-2xl font-bold mt-2">Selamat Datang, {{ Auth::user()->name }}!</h2>
                <p class="text-slate-300 text-sm mt-1 max-w-xl">
                    Berikut adalah ringkasan performa dan aktivitas sistem platform iFind secara real-time.
                </p>
            </div>
            <div class="flex items-center space-x-3 mt-6">
                <a href="{{ route('admin.staff-verification.index') }}" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Kelola Staf Toko
                </a>
                <a href="{{ route('admin.tickets.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition border border-slate-700">
                    Pusat Bantuan
                </a>
            </div>
        </div>

        <!-- Notification / Action Alerts Widget -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pemberitahuan Mendesak</h3>
            <div class="space-y-3 my-3">
                <div class="flex items-center justify-between p-3 rounded-xl {{ $openTicketsCount > 0 ? 'bg-rose-50 border border-rose-100' : 'bg-slate-50 border border-slate-100' }}">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $openTicketsCount > 0 ? 'bg-rose-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Tiket Bantuan Open</p>
                            <p class="text-sm font-bold {{ $openTicketsCount > 0 ? 'text-rose-600' : 'text-slate-500' }}">
                                {{ $openTicketsCount }} tiket butuh respon
                            </p>
                        </div>
                    </div>
                    @if($openTicketsCount > 0)
                        <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}" class="text-xs font-bold text-rose-600 hover:underline">Lihat</a>
                    @endif
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl {{ $pendingStaffCount > 0 ? 'bg-amber-50 border border-amber-100' : 'bg-slate-50 border border-slate-100' }}">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $pendingStaffCount > 0 ? 'bg-amber-500 text-slate-900' : 'bg-slate-200 text-slate-500' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Verifikasi Staf</p>
                            <p class="text-sm font-bold {{ $pendingStaffCount > 0 ? 'text-amber-700' : 'text-slate-500' }}">
                                {{ $pendingStaffCount }} staf pending
                            </p>
                        </div>
                    </div>
                    @if($pendingStaffCount > 0)
                        <a href="{{ route('admin.staff-verification.index', ['status' => 'pending']) }}" class="text-xs font-bold text-amber-700 hover:underline">Periksa</a>
                    @endif
                </div>
            </div>
            <span class="text-[11px] text-slate-400">Data otomatis terupdate dari database</span>
        </div>
    </div>

    <!-- 4 Key Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Users -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengguna</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalUsers) }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Role Customer (User)</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Staf Toko</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalStaff) }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Role Staf (Mitra Toko)</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <!-- Total Active Stores -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Toko Aktif</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalActiveStores) }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Status Approved & Aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Booking</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalBookings) }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Semua status booking</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 7 Days Booking Line Chart -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-base font-bold text-slate-900">Tren Booking (7 Hari Terakhir)</h4>
                    <p class="text-xs text-slate-500">Jumlah booking yang dilakukan per hari</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-teal-50 text-teal-700 border border-teal-100">Line Chart</span>
            </div>
            <div class="relative h-72">
                <canvas id="bookingTrendChart"></canvas>
            </div>
        </div>

        <!-- Stores per Category Bar Chart -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-base font-bold text-slate-900">Jumlah Toko per Kategori</h4>
                    <p class="text-xs text-slate-500">Distribusi listing tempat berdasarkan kategori</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">Bar Chart</span>
            </div>
            <div class="relative h-72">
                <canvas id="categoryDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Line Chart: Booking Trend
        const ctxBooking = document.getElementById('bookingTrendChart').getContext('2d');
        const bookingDays = @json($bookingDays);
        const bookingCounts = @json($bookingCounts);

        new Chart(ctxBooking, {
            type: 'line',
            data: {
                labels: bookingDays,
                datasets: [{
                    label: 'Booking',
                    data: bookingCounts,
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#0d9488',
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#94a3b8',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Bar Chart: Categories
        const ctxCategory = document.getElementById('categoryDistributionChart').getContext('2d');
        const categoryNames = @json($categoryNames);
        const categoryStoreCounts = @json($categoryStoreCounts);

        new Chart(ctxCategory, {
            type: 'bar',
            data: {
                labels: categoryNames,
                datasets: [{
                    label: 'Toko',
                    data: categoryStoreCounts,
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                    maxBarThickness: 32,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#94a3b8',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#64748b',
                            font: { size: 10 }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

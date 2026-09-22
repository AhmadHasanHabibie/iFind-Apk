@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Halaman Kedaluwarsa')
@section('message', 'Sesi keamanan (CSRF Token) pada halaman formulir ini telah berakhir karena tidak ada aktivitas dalam waktu tertentu. Ini demi melindungi keamanan akun Anda.')

@section('ambient_color_1', 'bg-orange-400/25')
@section('ambient_color_2', 'bg-amber-500/20')
@section('status_dot', 'bg-orange-500')
@section('accent_gradient', 'bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500')
@section('badge_bg', 'bg-orange-50 border border-orange-200/70')
@section('badge_glow', 'bg-orange-500/20')
@section('icon_class', 'fa-solid fa-hourglass-half')
@section('icon_color', 'text-orange-600')
@section('code_gradient', 'bg-gradient-to-br from-orange-600 via-amber-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-orange-50/80 border border-orange-100 text-xs text-orange-950 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-rotate text-orange-600 text-sm mt-0.5 shrink-0"></i>
    <span>Solusi mudah: Cukup klik tombol <strong>"Muat Ulang"</strong> di bawah untuk mendapatkan sesi baru, lalu kirim kembali formulir Anda.</span>
</div>
@endsection

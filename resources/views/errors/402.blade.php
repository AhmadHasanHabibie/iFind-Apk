@extends('errors.layout')

@section('code', '402')
@section('title', 'Pembayaran Diperlukan')
@section('message', 'Akses ke layanan atau fitur reservasi ini memerlukan penyelesaian transaksi pembayaran terlebih dahulu.')

@section('ambient_color_1', 'bg-emerald-400/25')
@section('ambient_color_2', 'bg-teal-500/20')
@section('status_dot', 'bg-emerald-500')
@section('accent_gradient', 'bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-600')
@section('badge_bg', 'bg-emerald-50 border border-emerald-200/70')
@section('badge_glow', 'bg-emerald-500/20')
@section('icon_class', 'fa-solid fa-credit-card')
@section('icon_color', 'text-emerald-600')
@section('code_gradient', 'bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-xs text-emerald-950 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-receipt text-emerald-600 text-sm mt-0.5 shrink-0"></i>
    <span>Silakan periksa riwayat tagihan atau booking reservasi Anda di dashboard pengguna untuk menyelesaikan pembayaran.</span>
</div>
@endsection

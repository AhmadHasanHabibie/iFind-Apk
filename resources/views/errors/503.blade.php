@extends('errors.layout')

@section('code', '503')
@section('title', 'Layanan Sedang Pemeliharaan')
@section('message', 'Layanan i-Find sedang dalam proses peningkatan sistem atau pemeliharaan berkala (Maintenance) untuk menghadirkan pengalaman yang lebih cepat dan stabil.')

@section('ambient_color_1', 'bg-cyan-400/25')
@section('ambient_color_2', 'bg-blue-500/20')
@section('status_dot', 'bg-cyan-500')
@section('accent_gradient', 'bg-gradient-to-r from-cyan-500 via-sky-500 to-blue-600')
@section('badge_bg', 'bg-cyan-50 border border-cyan-200/70')
@section('badge_glow', 'bg-cyan-500/20')
@section('icon_class', 'fa-solid fa-screwdriver-wrench')
@section('icon_color', 'text-cyan-600')
@section('code_gradient', 'bg-gradient-to-br from-cyan-600 via-sky-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-cyan-50/80 border border-cyan-100 text-xs text-cyan-950 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-clock text-cyan-600 text-sm mt-0.5 shrink-0"></i>
    <span>Pemeliharaan rutin ini biasanya memerlukan waktu beberapa menit saja. Kami akan segera kembali beroperasi penuh. Terima kasih atas kesabaran Anda!</span>
</div>
@endsection

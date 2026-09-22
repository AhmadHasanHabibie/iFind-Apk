@extends('errors.layout')

@section('code', '429')
@section('title', 'Terlalu Banyak Permintaan')
@section('message', 'Sistem proteksi i-Find mendeteksi lalu lintas permintaan yang terlalu cepat dari perangkat Anda dalam interval singkat. Harap tunggu beberapa saat.')

@section('ambient_color_1', 'bg-yellow-400/25')
@section('ambient_color_2', 'bg-amber-500/20')
@section('status_dot', 'bg-yellow-500')
@section('accent_gradient', 'bg-gradient-to-r from-yellow-500 via-amber-500 to-orange-500')
@section('badge_bg', 'bg-yellow-50 border border-yellow-200/70')
@section('badge_glow', 'bg-yellow-500/20')
@section('icon_class', 'fa-solid fa-gauge-high')
@section('icon_color', 'text-yellow-600')
@section('code_gradient', 'bg-gradient-to-br from-yellow-600 via-amber-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-yellow-50/80 border border-yellow-200/60 text-xs text-yellow-950 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-stopwatch text-yellow-600 text-sm mt-0.5 shrink-0"></i>
    <span>Fitur pembatasan kecepatan (Rate Limiting) aktif untuk menjaga performa server tetap stabil dan adil bagi seluruh pengunjung.</span>
</div>
@endsection

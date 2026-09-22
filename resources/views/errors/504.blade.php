@extends('errors.layout')

@section('code', '504')
@section('title', 'Batas Waktu Server Habis')
@section('message', 'Server perantara (gateway) tidak menerima tanggapan tepat waktu dari server upstream (Gateway Timeout). Pemrosesan permintaan memakan waktu lebih lama dari biasanya.')

@section('ambient_color_1', 'bg-amber-400/20')
@section('ambient_color_2', 'bg-stone-500/20')
@section('status_dot', 'bg-amber-500')
@section('accent_gradient', 'bg-gradient-to-r from-amber-500 via-orange-500 to-stone-600')
@section('badge_bg', 'bg-amber-50 border border-amber-200/70')
@section('badge_glow', 'bg-amber-500/20')
@section('icon_class', 'fa-solid fa-hourglass-end')
@section('icon_color', 'text-amber-600')
@section('code_gradient', 'bg-gradient-to-br from-amber-600 via-orange-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-amber-50/70 border border-amber-100 text-xs text-amber-900/80 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-wifi text-amber-600 text-sm mt-0.5 shrink-0"></i>
    <span>Koneksi jaringan mungkin sedang lambat atau server sedang memproses antrean beban tinggi. Silakan coba kembali dalam beberapa saat.</span>
</div>
@endsection

@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('message', 'Ups! Spot nongkrong, kafe, atau tautan yang Anda tuju sepertinya tersesat, telah dihapus, atau alamat URL yang dimasukkan kurang tepat.')

@section('ambient_color_1', 'bg-blue-400/25')
@section('ambient_color_2', 'bg-indigo-400/25')
@section('status_dot', 'bg-blue-500')
@section('accent_gradient', 'bg-gradient-to-r from-blue-500 via-sky-500 to-indigo-600')
@section('badge_bg', 'bg-blue-50 border border-blue-200/70')
@section('badge_glow', 'bg-blue-500/20')
@section('icon_class', 'fa-solid fa-map-location-dot')
@section('icon_color', 'text-blue-600')
@section('code_gradient', 'bg-gradient-to-br from-blue-600 via-indigo-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-blue-900/80 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-lightbulb text-blue-600 text-sm mt-0.5 shrink-0"></i>
    <span>Tips: Periksa kembali penulisan URL di browser atau gunakan tombol pencarian di halaman utama untuk menemukan spot nongkrong favorit Anda.</span>
</div>
@endsection

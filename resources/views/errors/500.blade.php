@extends('errors.layout')

@section('code', '500')
@section('title', 'Terjadi Gangguan pada Server')
@section('message', 'Terjadi kesalahan internal tak terduga pada server kami saat memproses permintaan Anda. Jangan khawatir, tim teknis i-Find telah menerima log otomatis untuk meninjau kendala ini.')

@section('ambient_color_1', 'bg-rose-400/25')
@section('ambient_color_2', 'bg-red-500/20')
@section('status_dot', 'bg-rose-500')
@section('accent_gradient', 'bg-gradient-to-r from-rose-500 via-red-500 to-amber-500')
@section('badge_bg', 'bg-rose-50 border border-rose-200/70')
@section('badge_glow', 'bg-rose-500/20')
@section('icon_class', 'fa-solid fa-server')
@section('icon_color', 'text-rose-600')
@section('code_gradient', 'bg-gradient-to-br from-rose-600 via-red-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-rose-50/70 border border-rose-100 text-xs text-rose-900/80 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-shield-halved text-rose-600 text-sm mt-0.5 shrink-0"></i>
    <span>Data dan akun Anda tetap aman. Silakan muat ulang halaman dalam beberapa detik atau kembali ke beranda utama.</span>
</div>
@endsection

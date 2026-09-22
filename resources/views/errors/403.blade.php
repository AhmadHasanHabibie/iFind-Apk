@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Halaman Dibatasi')
@section('message', 'Mohon maaf, akun Anda tidak memiliki hak akses atau izin yang diperlukan untuk membuka halaman ini. Area ini dikhususkan untuk otorisasi tertentu.')

@section('ambient_color_1', 'bg-amber-400/25')
@section('ambient_color_2', 'bg-orange-500/20')
@section('status_dot', 'bg-amber-500')
@section('accent_gradient', 'bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500')
@section('badge_bg', 'bg-amber-50 border border-amber-200/70')
@section('badge_glow', 'bg-amber-500/20')
@section('icon_class', 'fa-solid fa-shield-halved')
@section('icon_color', 'text-amber-600')
@section('code_gradient', 'bg-gradient-to-br from-amber-600 via-orange-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-amber-50/70 border border-amber-100 text-xs text-amber-900/80 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-lock text-amber-600 text-sm mt-0.5 shrink-0"></i>
    <span>Jika Anda merasa memiliki hak akses ke panel ini (misal Akun Staff atau Admin), pastikan Anda telah login menggunakan kredensial akun yang tepat.</span>
</div>
@endsection

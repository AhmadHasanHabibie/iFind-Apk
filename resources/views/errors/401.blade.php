@extends('errors.layout')

@section('code', '401')
@section('title', 'Autentikasi Diperlukan')
@section('message', 'Akses ke halaman ini membutuhkan autentikasi pengguna. Silakan masuk (login) atau daftar akun i-Find Anda terlebih dahulu.')

@section('ambient_color_1', 'bg-indigo-400/25')
@section('ambient_color_2', 'bg-violet-500/20')
@section('status_dot', 'bg-indigo-500')
@section('accent_gradient', 'bg-gradient-to-r from-indigo-500 via-violet-500 to-purple-600')
@section('badge_bg', 'bg-indigo-50 border border-indigo-200/70')
@section('badge_glow', 'bg-indigo-500/20')
@section('icon_class', 'fa-solid fa-key')
@section('icon_color', 'text-indigo-600')
@section('code_gradient', 'bg-gradient-to-br from-indigo-600 via-violet-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 flex items-center justify-center gap-3">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all">
        <i class="fa-solid fa-right-to-bracket text-xs"></i>
        <span>Masuk ke Akun Saya</span>
    </a>
    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-all">
        <i class="fa-solid fa-user-plus text-xs"></i>
        <span>Daftar Akun Baru</span>
    </a>
</div>
@endsection

@extends('errors.layout')

@section('code', '502')
@section('title', 'Kesalahan Gerbang Server')
@section('message', 'Server perantara (gateway/proxy) menerima respons yang tidak valid dari server backend. Koneksi upstream sedang mengalami hambatan sesaat.')

@section('ambient_color_1', 'bg-red-400/20')
@section('ambient_color_2', 'bg-rose-500/20')
@section('status_dot', 'bg-red-500')
@section('accent_gradient', 'bg-gradient-to-r from-red-500 via-rose-500 to-slate-700')
@section('badge_bg', 'bg-red-50 border border-red-200/70')
@section('badge_glow', 'bg-red-500/20')
@section('icon_class', 'fa-solid fa-link-slash')
@section('icon_color', 'text-red-600')
@section('code_gradient', 'bg-gradient-to-br from-red-600 via-rose-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-red-50/70 border border-red-100 text-xs text-red-900/80 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-cloud-bolt text-red-500 text-sm mt-0.5 shrink-0"></i>
    <span>Hal ini umumnya berlangsung singkat saat sistem sedang sinkronisasi ulang. Silakan coba muat ulang halaman setelah 10-15 detik.</span>
</div>
@endsection

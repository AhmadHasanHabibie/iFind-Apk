@extends('errors.layout')

@section('code', '506')
@section('title', 'Konflik Konfigurasi Server')
@section('message', 'Server mengalami kendala negosiasi konten internal (Variant Also Negotiates). Konfigurasi varian sumber daya saling berputar atau tidak dapat diselesaikan secara otomatis.')

@section('ambient_color_1', 'bg-fuchsia-400/25')
@section('ambient_color_2', 'bg-purple-500/25')
@section('status_dot', 'bg-fuchsia-500')
@section('accent_gradient', 'bg-gradient-to-r from-fuchsia-500 via-purple-500 to-indigo-600')
@section('badge_bg', 'bg-fuchsia-50 border border-fuchsia-200/70')
@section('badge_glow', 'bg-fuchsia-500/20')
@section('icon_class', 'fa-solid fa-arrows-split-up-and-left')
@section('icon_color', 'text-fuchsia-600')
@section('code_gradient', 'bg-gradient-to-br from-fuchsia-600 via-purple-700 to-slate-900 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-fuchsia-50/60 border border-fuchsia-100 text-xs text-fuchsia-900/80 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-circle-info text-fuchsia-600 text-sm mt-0.5 shrink-0"></i>
    <span>Status 506 menunjukkan server menghadapi loop negosiasi konfigurasi internal. Anda dapat memuat ulang halaman atau kembali ke beranda selagi penyesuaian diperbarui.</span>
</div>
@endsection

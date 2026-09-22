@extends('errors.layout')

@section('code', '400')
@section('title', 'Permintaan Tidak Valid')
@section('message', 'Server tidak dapat memahami atau memproses permintaan yang dikirimkan. Format parameter, input data, atau URL yang dimasukkan tidak sesuai spesifikasi.')

@section('ambient_color_1', 'bg-slate-400/20')
@section('ambient_color_2', 'bg-zinc-400/20')
@section('status_dot', 'bg-slate-500')
@section('accent_gradient', 'bg-gradient-to-r from-slate-600 via-slate-500 to-zinc-600')
@section('badge_bg', 'bg-slate-100 border border-slate-200/80')
@section('badge_glow', 'bg-slate-500/15')
@section('icon_class', 'fa-solid fa-file-circle-xmark')
@section('icon_color', 'text-slate-700')
@section('code_gradient', 'bg-gradient-to-br from-slate-700 via-slate-800 to-slate-950 bg-clip-text text-transparent')

@section('custom_content')
<div class="mb-6 px-4 py-3 rounded-2xl bg-slate-100/80 border border-slate-200 text-xs text-slate-700 flex items-start gap-2.5 text-left max-w-md mx-auto">
    <i class="fa-solid fa-circle-question text-slate-500 text-sm mt-0.5 shrink-0"></i>
    <span>Pastikan formulir diisi dengan benar tanpa karakter terlarang atau tautan lama yang sudah tidak didukung.</span>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-xs">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
                Manajemen Fasilitas Tempat
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola daftar fasilitas (WiFi, Colokan, AC, dll.) yang dapat dipilih oleh pemilik toko.</p>
        </div>
        <a href="{{ route('admin.facilities.create') }}"
           class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition group">
            <i class="fa-solid fa-plus mr-2 text-xs group-hover:rotate-90 transition-transform"></i>
            Tambah Fasilitas
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        @if($facilities->isEmpty())
            <div class="p-12 text-center">
                <p class="text-sm text-slate-500">Belum ada data fasilitas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-6">Icon</th>
                            <th class="py-4 px-6">Nama Fasilitas</th>
                            <th class="py-4 px-6">Total Toko Menggunakan</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($facilities as $facility)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-base">
                                        <i class="{{ $facility->icon }}"></i>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $facility->name }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ $facility->stores_count }} Toko
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.facilities.edit', $facility) }}"
                                           class="p-2 rounded-xl text-slate-500 hover:text-teal-600 hover:bg-teal-50 transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.facilities.destroy', $facility) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus fasilitas {{ $facility->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
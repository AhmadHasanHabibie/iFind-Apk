@extends('layouts.admin')

@section('header_title', 'Manajemen Kategori Toko')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Add Button -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kategori Tempat / Toko</h2>
            <p class="text-xs text-slate-500 mt-1">Klasifikasi tempat seperti Coffee Shop, Coworking Space, Perpustakaan, dsb.</p>
        </div>

        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition space-x-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Kategori</span>
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Nama Kategori</th>
                        <th class="py-3.5 px-6">Slug</th>
                        <th class="py-3.5 px-6">Icon Class</th>
                        <th class="py-3.5 px-6 text-center">Jumlah Toko</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm border border-teal-100">
                                        {{ strtoupper(substr($category->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $category->name }}</p>
                                        <p class="text-xs text-slate-400 line-clamp-1">{{ $category->description ?: 'Tidak ada deskripsi' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs font-mono text-slate-500">
                                {{ $category->slug }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                @if($category->icon)
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono">{{ $category->icon }}</span>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $category->stores_count }} toko
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="p-1.5 text-slate-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                          data-confirm="Apakah Anda yakin ingin menghapus kategori '{{ $category->name }}'?"
                                          data-confirm-title="Hapus Kategori"
                                          data-confirm-btn="Ya, Hapus"
                                          data-confirm-danger="true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada kategori yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

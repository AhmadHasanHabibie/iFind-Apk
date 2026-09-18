@extends('layouts.admin')

@section('header_title', 'Detail Verifikasi Staf Toko')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ rejectModalOpen: false }">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.staff-verification.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-teal-700 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Staf
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Top Profile Banner -->
        <div class="p-6 sm:p-8 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-teal-500 text-white font-extrabold text-2xl flex items-center justify-center shadow-lg shadow-teal-500/30">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-300">{{ $user->email }}</p>
                    <div class="mt-2 flex items-center space-x-2">
                        @if($user->verification_status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                                Status: Menunggu Verifikasi
                            </span>
                        @elseif($user->verification_status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                                Status: Terverifikasi / Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-400/20 text-rose-300 border border-rose-400/30">
                                Status: Ditolak
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons for Verification -->
            <div class="flex items-center space-x-3">
                @if($user->verification_status !== 'approved')
                    <form method="POST" action="{{ route('admin.staff-verification.approve', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui akun staf ini?')">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Setujui Akun</span>
                        </button>
                    </form>
                @endif

                @if($user->verification_status !== 'rejected')
                    <button type="button"
                            @click="rejectModalOpen = true"
                            class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Tolak Pendaftaran</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Detail Information Grid -->
        <div class="p-6 sm:p-8 space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">Informasi Pendaftar</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-400">Nama Lengkap</p>
                    <p class="text-base font-bold text-slate-800 mt-1">{{ $user->name }}</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-400">Alamat Email</p>
                    <p class="text-base font-bold text-slate-800 mt-1">{{ $user->email }}</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-400">Nomor Telepon / WhatsApp</p>
                    <p class="text-base font-bold text-slate-800 mt-1">{{ $user->phone ?: '-' }}</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-400">Tanggal Registrasi</p>
                    <p class="text-base font-bold text-slate-800 mt-1">{{ $user->created_at->format('d F Y, H:i') }} WIB</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-400">Status Akses Login</p>
                    <p class="text-base font-bold mt-1 {{ $user->is_active ? 'text-emerald-600' : 'text-slate-500' }}">
                        {{ $user->is_active ? 'Aktif (Dapat Login)' : 'Nonaktif (Tidak Dapat Login)' }}
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-400">Terakhir Diverifikasi</p>
                    <p class="text-base font-bold text-slate-800 mt-1">
                        {{ $user->verified_at ? $user->verified_at->format('d F Y, H:i') . ' WIB' : 'Belum pernah diverifikasi' }}
                    </p>
                </div>
            </div>

            <!-- Catatan Penolakan Jika Ada -->
            @if($user->verification_note)
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200">
                    <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Catatan Alasan Penolakan Admin</p>
                    <p class="text-sm text-rose-900 mt-1 whitespace-pre-line">{{ $user->verification_note }}</p>
                </div>
            @endif

            <!-- Toko Terkait -->
            <div class="pt-4 border-t border-slate-200">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3">Toko Terkait (Jika Sudah Dibuat)</h3>
                @if($user->store)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-slate-800">{{ $user->store->name }}</p>
                            <p class="text-xs text-slate-500">{{ $user->store->address }}, {{ $user->store->city }}</p>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-teal-50 text-teal-700 border border-teal-200">
                            Status: {{ ucfirst($user->store->status) }}
                        </span>
                    </div>
                @else
                    <p class="text-sm text-slate-500 italic">Belum ada profil toko yang dibuat oleh staf ini (dibuat di Prompt 2).</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="rejectModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100" @click.away="rejectModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-rose-600 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Tolak Pendaftaran Staf</span>
                </h3>
                <button @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.staff-verification.reject', $user) }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="verification_note" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="verification_note"
                              name="verification_note"
                              rows="4"
                              required
                              placeholder="Contoh: Dokumen atau identitas kontak tidak valid. Silakan lengkapi nomor WhatsApp aktif..."
                              class="w-full text-sm rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Alasan ini akan ditampilkan kepada calon staf saat mencoba masuk (login).</p>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm">
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

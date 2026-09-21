<div class="space-y-4">
    <div class="border-b border-slate-100 pb-2">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Foto Galeri & Etalase Toko</h3>
        <p class="text-xs text-slate-500">Unggah foto suasana tempat, meja kerja, menu, dan interior toko (JPG, PNG, maks 2MB per foto)</p>
    </div>

    <!-- Upload Input Box -->
    <div class="p-4 rounded-xl border border-dashed border-slate-300 bg-slate-50/50 hover:bg-slate-50 transition">
        <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Foto Baru (Bisa pilih sekaligus):</label>
        <input type="file"
               name="photos[]"
               multiple
               accept="image/png, image/jpeg, image/jpg"
               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
    </div>

    <!-- Existing Photos Grid -->
    @if(isset($store) && $store->photos && $store->photos->count() > 0)
        <div class="space-y-2 pt-2">
            <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider">Foto yang Sudah Terpasang ({{ $store->photos->count() }})</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach($store->photos as $photo)
                    <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-100 aspect-video flex items-center justify-center">
                        @php
                            $isRealFile = \Illuminate\Support\Facades\Storage::disk('public')->exists($photo->photo_path);
                            $photoUrl = $isRealFile ? asset('storage/' . $photo->photo_path) : 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=500&auto=format&fit=crop&q=60';
                        @endphp
                        <img src="{{ $photoUrl }}" alt="Foto Toko" class="w-full h-full object-cover">

                        @if($photo->is_primary)
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-600 text-white shadow">
                                Utama
                            </span>
                        @endif

                        <!-- Hover Action Buttons -->
                        <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center space-x-2 p-2">
                            @if(! $photo->is_primary)
                                <button type="button"
                                        onclick="document.getElementById('set-primary-form-{{ $photo->id }}').submit()"
                                        title="Jadikan Foto Utama"
                                        class="p-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-xs font-semibold">
                                    Utama
                                </button>
                            @endif

                            <button type="button"
                                    onclick="confirmAction('Apakah Anda yakin ingin menghapus foto toko ini?', () => document.getElementById('delete-photo-form-{{ $photo->id }}').submit(), 'Hapus Foto Toko', 'Ya, Hapus', 'danger')"
                                    title="Hapus Foto"
                                    class="p-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition text-xs font-semibold cursor-pointer">
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

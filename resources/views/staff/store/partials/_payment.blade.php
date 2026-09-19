<!-- 4. Pengaturan Harga & Pembayaran -->
<div class="space-y-4 pt-2" x-data="{
    price: {{ old('price_per_pax', $store->price_per_pax ?? 0) }},
    dp: {{ old('dp_percentage', $store->dp_percentage ?? 100) }},
    formatRupiah(val) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
    }
}">
    <div class="border-b border-slate-100 pb-2 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Pengaturan Harga & Pembayaran</h3>
            <p class="text-xs text-slate-500">Tentukan tarif per kursi, ketentuan DP/pelunasan, dan rekening atau QRIS penerima pembayaran.</p>
        </div>
        <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
            Manual Transfer / QRIS
        </span>
    </div>

    @if($errors->has('price_per_pax') || $errors->has('bank_name'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 space-y-1">
            @if($errors->has('price_per_pax'))
                <p class="font-bold flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errors->first('price_per_pax') }}</span>
                </p>
            @endif
            @if($errors->has('bank_name'))
                <p class="font-bold flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errors->first('bank_name') }}</span>
                </p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Harga per Pax -->
        <div>
            <label for="price_per_pax" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                Harga per Orang (Pax) <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                    Rp
                </div>
                <input type="number"
                       id="price_per_pax"
                       name="price_per_pax"
                       min="0"
                       step="1000"
                       x-model.number="price"
                       placeholder="Contoh: 25000"
                       class="w-full pl-10 text-sm font-bold rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Harga satu kursi/tempat duduk dalam 1 sesi booking.</p>
        </div>

        <!-- Persentase DP -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="dp_percentage" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Persentase DP / Uang Muka <span class="text-rose-500">*</span>
                </label>
                <span class="text-xs font-black text-blue-600" x-text="dp + '%'"></span>
            </div>
            <input type="range"
                   id="dp_percentage"
                   name="dp_percentage"
                   min="0"
                   max="100"
                   step="5"
                   x-model.number="dp"
                   class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
            <div class="flex justify-between text-[10px] text-slate-400 mt-1 font-mono">
                <span>0% (Bayar di tempat)</span>
                <span>50% (DP Separuh)</span>
                <span>100% (Bayar Lunas)</span>
            </div>
        </div>

        <!-- Batas Waktu Pembayaran (Timeout) -->
        <div>
            <label for="payment_timeout_minutes" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                Batas Waktu Upload Bukti <span class="text-rose-500">*</span>
            </label>
            <select id="payment_timeout_minutes"
                    name="payment_timeout_minutes"
                    class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                @php
                    $selectedTimeout = old('payment_timeout_minutes', $store->payment_timeout_minutes ?? 60);
                @endphp
                <option value="15" {{ $selectedTimeout == 15 ? 'selected' : '' }}>15 Menit</option>
                <option value="30" {{ $selectedTimeout == 30 ? 'selected' : '' }}>30 Menit</option>
                <option value="60" {{ $selectedTimeout == 60 ? 'selected' : '' }}>60 Menit (1 Jam)</option>
                <option value="120" {{ $selectedTimeout == 120 ? 'selected' : '' }}>120 Menit (2 Jam)</option>
            </select>
            <p class="text-[11px] text-slate-400 mt-1">Jika user tidak upload bukti, booking otomatis dibatalkan sistem.</p>
        </div>
    </div>

    <!-- Live DP Calculation Simulation Box -->
    <div class="p-4 bg-gradient-to-r from-blue-50/80 via-indigo-50/60 to-slate-50 border border-blue-100 rounded-2xl">
        <div class="flex items-center space-x-2 text-blue-900 font-bold text-xs mb-1.5">
            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Simulasi Skema Pembayaran Pelanggan (Contoh 1 Pax):</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
            <div class="bg-white/80 p-2.5 rounded-xl border border-blue-100/80">
                <span class="text-slate-500 text-[11px] block">Total Tarif per Kursi</span>
                <strong class="text-slate-900 font-extrabold text-sm" x-text="formatRupiah(price)"></strong>
            </div>
            <div class="bg-white/80 p-2.5 rounded-xl border border-blue-100/80">
                <span class="text-slate-500 text-[11px] block">Wajib Transfer di Muka</span>
                <strong class="text-blue-700 font-extrabold text-sm" x-text="formatRupiah(Math.round(price * dp / 100))"></strong>
                <span class="text-[10px] text-blue-600 block" x-text="'(' + dp + '% dari total)'"></span>
            </div>
            <div class="bg-white/80 p-2.5 rounded-xl border border-blue-100/80">
                <span class="text-slate-500 text-[11px] block">Sisa Dibayar di Lokasi (Cash)</span>
                <strong class="text-emerald-700 font-extrabold text-sm" x-text="dp >= 100 ? 'LUNAS (Rp 0)' : formatRupiah(Math.max(0, price - Math.round(price * dp / 100)))"></strong>
                <span class="text-[10px] text-slate-400 block" x-text="dp >= 100 ? 'Tidak ada sisa tagihan' : 'Dibayar manual saat tiba di toko'"></span>
            </div>
        </div>
    </div>

    <!-- Informasi Rekening Bank & Gambar QRIS -->
    <div class="pt-2 border-t border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Tujuan Pembayaran Transfer / QRIS Toko</h4>
                <p class="text-[11px] text-slate-500">Pelanggan akan mentransfer langsung ke rekening Anda atau scan gambar QRIS di bawah ini.</p>
            </div>
            <span class="text-[11px] font-semibold text-rose-500">* Minimal salah satu wajib diisi</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- 1. Rekening Bank -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                <h5 class="text-xs font-bold text-slate-800 flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span>Opsi 1: Rekening Bank Toko</span>
                </h5>

                <div>
                    <label for="bank_name" class="block text-[11px] font-bold text-slate-600 mb-1">Nama Bank</label>
                    <input type="text"
                           id="bank_name"
                           name="bank_name"
                           value="{{ old('bank_name', $store->bank_name) }}"
                           placeholder="Contoh: BCA, Mandiri, BRI, BNI"
                           class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="bank_account_number" class="block text-[11px] font-bold text-slate-600 mb-1">Nomor Rekening</label>
                    <input type="text"
                           id="bank_account_number"
                           name="bank_account_number"
                           value="{{ old('bank_account_number', $store->bank_account_number) }}"
                           placeholder="Contoh: 1234567890"
                           class="w-full text-xs font-mono rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="bank_account_holder" class="block text-[11px] font-bold text-slate-600 mb-1">Nama Pemilik Rekening (Atas Nama)</label>
                    <input type="text"
                           id="bank_account_holder"
                           name="bank_account_holder"
                           value="{{ old('bank_account_holder', $store->bank_account_holder) }}"
                           placeholder="Contoh: Titik Temu Coffee"
                           class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- 2. Gambar QRIS Toko -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                <h5 class="text-xs font-bold text-slate-800 flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <span>Opsi 2: Gambar QRIS Toko</span>
                </h5>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Upload File Gambar QRIS</label>
                    <input type="file"
                           id="qris_image"
                           name="qris_image"
                           accept="image/png,image/jpeg,image/jpg"
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-[10px] text-slate-400 mt-1">Format JPG, JPEG, atau PNG. Maksimal 2MB.</p>
                </div>

                @if(! empty($store->qris_image_path))
                    <div class="mt-2 pt-2 border-t border-slate-200/80 flex items-center space-x-3">
                        <div class="w-16 h-16 rounded-xl border border-slate-200 bg-white p-1 overflow-hidden shrink-0 shadow-xs">
                            <img src="{{ asset('storage/' . $store->qris_image_path) }}"
                                 alt="QRIS Toko"
                                 class="w-full h-full object-contain">
                        </div>
                        <div class="text-xs">
                            <span class="font-bold text-emerald-700 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>QRIS Toko Aktif</span>
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Upload file baru di atas jika ingin mengganti.</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

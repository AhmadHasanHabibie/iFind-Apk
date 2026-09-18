<!-- Review Modal Partial -->
<div x-show="reviewModalOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
     @keydown.escape.window="reviewModalOpen = false">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4"
         @click.outside="reviewModalOpen = false">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Beri Ulasan & Rating</h3>
                <p class="text-xs text-slate-500" x-text="'Pesanan: ' + activeBookingCode"></p>
            </div>
            <button type="button" @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form :action="'/user/bookings/' + activeBookingId + '/review'" method="POST" class="space-y-4">
            @csrf

            <!-- Star Rating Interactive Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    Rating Kepuasan <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2" x-data="{ hoverRating: 0 }">
                    <input type="hidden" name="rating" :value="selectedRating">
                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                        <button type="button"
                                @click="selectedRating = star"
                                @mouseenter="hoverRating = star"
                                @mouseleave="hoverRating = 0"
                                class="text-2xl transition focus:outline-hidden"
                                :class="(hoverRating ? hoverRating >= star : selectedRating >= star) ? 'text-amber-400 scale-110' : 'text-slate-200'">
                            <i class="fa-solid fa-star"></i>
                        </button>
                    </template>
                    <span class="text-xs font-bold text-slate-600 ml-2" x-text="selectedRating + ' / 5 Bintang'"></span>
                </div>
            </div>

            <!-- Comment Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Komentar & Pengalaman Anda (Opsional)
                </label>
                <textarea name="comment"
                          rows="3"
                          maxlength="1000"
                          placeholder="Ceritakan suasana, kenyamanan kursi, kecepatan WiFi, dan keramahan staf..."
                          class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end space-x-2">
                <button type="button" @click="reviewModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition">
                    Kirim Ulasan
                </button>
            </div>
        </form>
    </div>
</div>

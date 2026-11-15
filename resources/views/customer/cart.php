<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2.5 h-5 rounded-full barber-pole border border-slate-700 shadow-sm"></span>
                <span class="text-xs font-black uppercase tracking-widest text-emerald-400">Reservasi Studio</span>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Keranjang Pesanan Anda</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Periksa kembali layanan atau produk perawatan yang ingin Anda booking.</p>
        </div>
        <a href="/services" class="text-xs sm:text-sm font-bold text-emerald-400 hover:text-emerald-300 flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition btn-magnetic">
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Layanan Lain</span>
        </a>
    </div>

    <?php if (empty($cart)): ?>
    <div class="border-beam-card tilt-card-luxury p-12 text-center max-w-lg mx-auto shadow-2xl overflow-hidden">
        <div class="w-20 h-20 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-lg">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <h3 class="text-xl font-black text-white mb-2">Keranjang Anda Masih Kosong</h3>
        <p class="text-xs sm:text-sm text-slate-400 mb-6">Pilih layanan pangkas rambut atau produk perawatan kami untuk memulai pemesanan.</p>
        <a href="/services" class="inline-flex items-center space-x-2 px-6 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black rounded-2xl transition shadow-xl shadow-emerald-600/30 text-xs uppercase tracking-wider shimmer-btn btn-magnetic">
            <i class="fas fa-scissors"></i>
            <span>Lihat Katalog Layanan</span>
        </a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Item List -->
        <div class="lg:col-span-8 bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 overflow-hidden">
            <div class="divide-y divide-white/10">
                <?php foreach ($cart as $item): ?>
                <?php $cartImg = \App\Models\Item::getImageUrl($item['image_path'] ?? null, $item['name'] ?? ''); ?>
                <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-white/5 transition">
                    <div class="flex items-center space-x-4">
                        <img src="<?= htmlspecialchars($cartImg) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 rounded-2xl object-cover border border-white/10 shadow-sm flex-shrink-0">
                        <div>
                            <h4 class="font-black text-white text-base"><?= htmlspecialchars($item['name']) ?></h4>
                            <span class="text-xs text-slate-400">Harga: Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6 w-full sm:w-auto justify-between sm:justify-end">
                        <!-- Quantity modifiers -->
                        <div class="flex items-center border border-white/10 rounded-xl overflow-hidden bg-slate-950/70">
                            <form action="/cart/update" method="POST" class="inline">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="action" value="decrease">
                                <button type="submit" class="px-3 py-1.5 hover:bg-white/10 text-slate-300 text-xs transition font-bold">-</button>
                            </form>
                            <span class="px-3 py-1.5 text-xs font-bold text-white"><?= $item['quantity'] ?></span>
                            <form action="/cart/update" method="POST" class="inline">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="action" value="increase">
                                <button type="submit" class="px-3 py-1.5 hover:bg-white/10 text-slate-300 text-xs transition font-bold">+</button>
                            </form>
                        </div>

                        <!-- Subtotal item -->
                        <div class="text-right min-w-[100px]">
                            <span class="font-black text-emerald-400 text-base">
                                Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                            </span>
                        </div>

                        <!-- Delete Button -->
                        <form action="/cart/update" method="POST" class="inline">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="text-slate-400 hover:text-rose-400 transition p-2 hover:bg-rose-500/10 rounded-xl" title="Hapus Item">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Summary & Next Button with Laser Border Beam -->
        <div class="lg:col-span-4 border-beam-card tilt-card-luxury p-6 sm:p-7 sticky top-28 text-white shadow-2xl overflow-hidden">
            <h3 class="font-black text-white text-lg mb-4">Ringkasan Biaya</h3>

            <div class="space-y-3 text-sm text-slate-300 pb-4 border-b border-white/10">
                <div class="flex justify-between">
                    <span>Subtotal Layanan:</span>
                    <span class="font-bold text-white">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                </div>
                <div class="flex justify-between">
                    <span>Pajak (PPN 10%):</span>
                    <span class="font-bold text-white">Rp <?= number_format($tax, 0, ',', '.') ?></span>
                </div>
                <div class="flex justify-between text-xs text-slate-400">
                    <span>Diskon Promosi:</span>
                    <span class="font-semibold text-emerald-400">Rp 0</span>
                </div>
            </div>

            <div class="flex justify-between items-center py-4 text-base font-extrabold text-white">
                <span>Total Pembayaran:</span>
                <span class="text-2xl text-emerald-400 font-black">Rp <?= number_format($grandTotal, 0, ',', '.') ?></span>
            </div>

            <a href="/checkout" class="w-full py-4 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black rounded-2xl transition duration-300 shadow-xl shadow-emerald-600/40 text-center block text-xs uppercase tracking-wider shimmer-btn btn-magnetic">
                Lanjut ke Pembayaran <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>

            <p class="text-center text-[11px] text-slate-400 mt-3.5">
                <i class="fas fa-shield-alt text-emerald-400 mr-1"></i> Transaksi terenkripsi & reservasi aman.
            </p>
        </div>
    </div>
    <?php endif; ?>
</div>

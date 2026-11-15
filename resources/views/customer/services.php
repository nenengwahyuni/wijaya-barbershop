<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <!-- Header with reveal & high-contrast glow typography -->
    <div class="mb-12 text-center max-w-3xl mx-auto reveal">
        <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-black uppercase tracking-widest mb-4 backdrop-blur-md shadow-lg shadow-emerald-950/40">
            <span class="w-2 h-2 rounded-full bg-emerald-400 beacon-pulse"></span>
            <span>Daftar Layanan & Produk</span>
        </div>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-tight drop-shadow-md">
            Katalog <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300 animate-gradient">Pangkas & Grooming</span>
        </h1>
        <p class="text-slate-300 text-sm sm:text-base mt-4 max-w-xl mx-auto leading-relaxed font-normal">
            Pilih jenis potongan rambut, perawatan kepala relaksasi, atau produk pomade dan styling original Wijaya Barbershop.
        </p>
    </div>

    <!-- Category Filter Tabs with Dark Luxury Glassmorphism & Magnetic Hover -->
    <div class="flex flex-wrap items-center justify-center gap-3 mb-12 reveal delay-100">
        <a href="/services" 
            class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-wider transition-all duration-300 shadow-md btn-magnetic <?= empty($selectedCategory) ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-emerald-500/35 border border-emerald-400/40 scale-105 shadow-xl' : 'bg-slate-900/80 hover:bg-white/10 text-slate-200 hover:text-white border border-white/15 backdrop-blur-md hover:border-emerald-500/40' ?>">
            Semua Kategori
        </a>
        <?php foreach ($categories as $cat): ?>
        <a href="/services?category=<?= $cat['id'] ?>" 
            class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-wider transition-all duration-300 shadow-md btn-magnetic <?= ($selectedCategory == $cat['id']) ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-emerald-500/35 border border-emerald-400/40 scale-105 shadow-xl' : 'bg-slate-900/80 hover:bg-white/10 text-slate-200 hover:text-white border border-white/15 backdrop-blur-md hover:border-emerald-500/40' ?>">
            <?= htmlspecialchars($cat['category_name']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Items Grid with Staggered Scroll Reveal -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (empty($items)): ?>
        <div class="col-span-3 text-center py-20 bg-slate-900/80 rounded-3xl border border-dashed border-white/15 reveal backdrop-blur-md">
            <i class="fas fa-box-open text-5xl text-slate-400 mb-3 block"></i>
            <h3 class="text-base font-bold text-white">Belum Ada Item</h3>
            <p class="text-xs text-slate-400 mt-1">Belum ada item layanan atau produk untuk kategori ini.</p>
        </div>
        <?php endif; ?>

        <?php foreach ($items as $idx => $item): ?>
        <?php $itemImg = \App\Models\Item::getImageUrl($item['image_path'] ?? null, $item['item_name'] ?? ''); ?>
        <div class="tilt-card-luxury card-premium bg-slate-900/90 rounded-3xl border border-white/10 overflow-hidden shadow-xl shadow-black/25 flex flex-col justify-between group reveal delay-<?= min(($idx % 6 + 1) * 100, 500) ?> text-white">
            <div>
                <!-- Image Header with Item Specific Photo & Zoom Effect -->
                <div class="h-52 bg-slate-950 relative flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 bg-cover bg-center img-zoom transition-transform duration-700 group-hover:scale-110" style="background-image: url('<?= htmlspecialchars($itemImg) ?>');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-black/30"></div>
                    <div class="relative z-10 w-full px-5 flex justify-between items-end h-full pb-3.5">
                        <span class="px-3.5 py-1.5 rounded-full bg-slate-950/85 backdrop-blur-md text-emerald-400 font-extrabold text-[11px] uppercase tracking-wider border border-emerald-500/30 shadow-md">
                            <?= htmlspecialchars($item['category_name']) ?>
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/90 text-white font-black text-xs shadow-md">
                            Rp <?= number_format($item['price'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-lg font-black text-white mb-2 group-hover:text-emerald-400 transition">
                        <?= htmlspecialchars($item['item_name']) ?>
                    </h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        <?= htmlspecialchars($item['description']) ?>
                    </p>
                </div>
            </div>

            <div class="px-6 pb-6 pt-4 border-t border-white/10 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Harga</span>
                    <span class="text-xl font-black text-emerald-400">Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                </div>

                <form action="/cart/add" method="POST" class="inline">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white rounded-xl font-black text-xs transition duration-200 shadow-lg shadow-emerald-600/30 flex items-center space-x-1.5 shimmer-btn btn-magnetic">
                        <i class="fas fa-cart-plus"></i>
                        <span>+ Keranjang</span>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

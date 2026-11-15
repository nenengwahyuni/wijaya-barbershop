<div class="space-y-6">
    <!-- Top Action & Summary Bar -->
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl p-6 border border-white/10 shadow-xl shadow-black/25 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Manajemen Katalog</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Katalog Layanan & Produk Wijaya Barbershop</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola foto produk, tarif harga, deskripsi, dan status aktif item di website.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/admin/items/create" class="px-5 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition shadow-xl shadow-emerald-600/35 shimmer-btn flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Tambah Item / Foto Baru</span>
            </a>
        </div>
    </div>

    <!-- Items Table with Photo Thumbnails (Dark Frosted Glass Table) -->
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 w-12 text-center">No</th>
                        <th class="p-4 w-20 text-center">Foto</th>
                        <th class="p-4">Nama Item / Layanan</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4 text-right">Harga</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 pr-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-200">
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">
                            Belum ada data item. Klik tombol "Tambah Item / Foto Baru" di atas untuk menambahkan.
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php $no = 1; foreach ($items as $item): ?>
                    <?php $photoUrl = \App\Models\Item::getImageUrl($item['image_path'] ?? null, $item['item_name'] ?? ''); ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="p-4 pl-6 text-center text-slate-400 font-bold"><?= $no++ ?></td>
                        
                        <!-- Foto Thumbnail Item -->
                        <td class="p-4 text-center">
                            <div class="relative w-14 h-14 mx-auto rounded-2xl overflow-hidden border border-white/10 shadow-sm group">
                                <img src="<?= htmlspecialchars($photoUrl) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                <a href="/admin/items/<?= $item['id'] ?>/edit" class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs" title="Ganti Foto">
                                    <i class="fas fa-camera"></i>
                                </a>
                            </div>
                        </td>

                        <td class="p-4">
                            <strong class="font-bold text-white text-sm block"><?= htmlspecialchars($item['item_name']) ?></strong>
                            <span class="text-slate-400 text-[11px] line-clamp-1 mt-0.5"><?= htmlspecialchars($item['description'] ?? 'Tidak ada deskripsi') ?></span>
                        </td>
                        <td class="p-4 font-semibold text-slate-300">
                            <span class="px-2.5 py-1 rounded-md bg-white/10 text-slate-200 text-[10px] font-bold border border-white/5">
                                <?= htmlspecialchars($item['category_name']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right font-black text-emerald-400 text-sm">
                            Rp <?= number_format($item['price'], 0, ',', '.') ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if ($item['is_active']): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Aktif
                            </span>
                            <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/10 text-slate-400 border border-white/10">
                                Nonaktif
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 pr-6 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="/admin/items/<?= $item['id'] ?>/edit" 
                                    class="px-3 py-1.5 bg-white/10 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 border border-white/10" 
                                    title="Edit Data & Ganti Foto">
                                    <i class="fas fa-edit text-[11px]"></i>
                                    <span>Edit</span>
                                </a>
                                <form action="/admin/items/delete" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?')">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="p-1.5 bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl transition border border-rose-500/30" title="Hapus Item">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

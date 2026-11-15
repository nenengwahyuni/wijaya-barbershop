<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <!-- Form Tambah Kategori (4 Kolom) -->
    <div class="lg:col-span-4 bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 p-6 sm:p-8">
        <div class="flex items-center space-x-2 mb-1">
            <span class="w-2.5 h-5 rounded-full barber-pole border border-slate-700 shadow-sm"></span>
            <h2 class="text-lg font-black text-white">Tambah Kategori</h2>
        </div>
        <p class="text-xs text-slate-400 mb-6">Kelompokkan layanan atau produk penata rambut.</p>

        <form action="/admin/categories" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create">

            <div>
                <label for="category_name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Nama Kategori *
                </label>
                <input type="text" id="category_name" name="category_name" required placeholder="Contoh: Styling Rambut"
                    class="w-full px-4 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white text-xs transition outline-none">
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Deskripsi
                </label>
                <textarea id="description" name="description" rows="3" placeholder="Penjelasan singkat..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white text-xs transition outline-none"></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-emerald-600/30">
                Simpan Kategori
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Kategori (8 Kolom) -->
    <div class="lg:col-span-8 bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <h2 class="text-lg font-black text-white">Daftar Kategori Layanan</h2>
            <p class="text-xs text-slate-400">Semua kategori layanan & produk aktif di sistem Wijaya Barbershop.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 w-12 text-center">ID</th>
                        <th class="p-4">Nama Kategori</th>
                        <th class="p-4">Deskripsi</th>
                        <th class="p-4 text-center">Jumlah Item</th>
                        <th class="p-4 pr-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-200">
                    <?php foreach ($categories as $cat): ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="p-4 pl-6 text-center font-bold text-slate-400"><?= $cat['id'] ?></td>
                        <td class="p-4 font-bold text-white"><?= htmlspecialchars($cat['category_name']) ?></td>
                        <td class="p-4 text-slate-400"><?= htmlspecialchars($cat['description'] ?? '-') ?></td>
                        <td class="p-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-slate-200 font-bold text-[10px] border border-white/10">
                                <?= $cat['total_items'] ?? 0 ?> Item
                            </span>
                        </td>
                        <td class="p-4 pr-6 text-center">
                            <form action="/admin/categories" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="p-1.5 bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl transition border border-rose-500/30" title="Hapus Kategori">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

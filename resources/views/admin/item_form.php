<?php
$currentPhoto = $item ? \App\Models\Item::getImageUrl($item['image_path'] ?? null, $item['item_name'] ?? '') : 'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?auto=format&fit=crop&w=800&q=80';
?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="/admin/items" class="text-xs font-bold text-slate-400 hover:text-emerald-400 flex items-center space-x-1.5 transition">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Katalog Item</span>
        </a>
    </div>

    <!-- Form Container (Dark Luxury Frosted Glass) -->
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/30 p-8 text-white">
        <div class="pb-6 border-b border-white/10 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-black uppercase tracking-wider text-emerald-400 block mb-1">
                    <?= $item ? 'Edit Katalog' : 'Item Baru' ?>
                </span>
                <h1 class="text-2xl font-black text-white tracking-tight">
                    <?= $item ? 'Edit Data & Foto: ' . htmlspecialchars($item['item_name']) : 'Tambah Layanan / Produk & Foto Baru' ?>
                </h1>
                <p class="text-xs text-slate-400 mt-1">Lengkapi informasi nama, harga, kategori, dan foto katalog yang tampil di website pelanggan.</p>
            </div>
            <?php if ($item): ?>
            <span class="px-3 py-1 bg-white/10 rounded-xl text-xs font-mono font-bold text-emerald-400 border border-white/10">ID Item #<?= $item['id'] ?></span>
            <?php endif; ?>
        </div>

        <form action="<?= $item ? "/admin/items/{$item['id']}/edit" : '/admin/items/create' ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            
            <!-- SECTION FOTO ITEM & LIVE PREVIEW -->
            <div class="p-6 rounded-3xl bg-slate-950/60 border border-white/10">
                <h3 class="text-sm font-black text-white mb-4 flex items-center space-x-2">
                    <i class="fas fa-camera text-emerald-400"></i>
                    <span>Foto Katalog Layanan / Produk *</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    <!-- Live Preview Box (4 Kolom) -->
                    <div class="md:col-span-4 text-center">
                        <div class="relative w-full h-44 rounded-2xl overflow-hidden border-2 border-emerald-500/50 shadow-lg bg-slate-900">
                            <img id="live-preview-img" src="<?= htmlspecialchars($currentPhoto) ?>" alt="Preview" class="w-full h-full object-cover">
                            <div class="absolute bottom-2 left-2 right-2 px-2.5 py-1 rounded-lg bg-black/80 backdrop-blur-sm text-[10px] text-white font-bold truncate">
                                Live Preview Foto
                            </div>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-2 block">Tampilan foto yang akan dilihat pelanggan</span>
                    </div>

                    <!-- Input Pilihan Foto (8 Kolom) -->
                    <div class="md:col-span-8 space-y-4">
                        <!-- Opsi 1: Upload File Komputer/HP -->
                        <div>
                            <label for="image" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                                <i class="fas fa-upload text-emerald-400 mr-1"></i> Pilihan 1: Upload File Gambar (JPG / PNG / WEBP)
                            </label>
                            <input type="file" id="image" name="image" accept="image/*" onchange="previewLocalFile(this)"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900/80 focus:border-emerald-500 text-slate-200 text-xs transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-500/20 file:text-emerald-300 hover:file:bg-emerald-500/30">
                        </div>

                        <!-- Opsi 2: Input URL / Link Gambar -->
                        <div>
                            <label for="image_url" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                                <i class="fas fa-link text-blue-400 mr-1"></i> Pilihan 2: Atau Tempel Link URL Foto Online (Unsplash / Web)
                            </label>
                            <input type="url" id="image_url" name="image_url" oninput="previewUrlInput(this.value)"
                                value="<?= ($item && (str_starts_with($item['image_path'] ?? '', 'http://') || str_starts_with($item['image_path'] ?? '', 'https://'))) ? htmlspecialchars($item['image_path']) : '' ?>"
                                placeholder="https://images.unsplash.com/photo-..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900/80 focus:border-emerald-500 text-white text-xs outline-none font-mono">
                        </div>

                        <!-- Rekomendasi Cepat Foto Sekali Klik -->
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 block mb-2">Preset Foto Berkualitas Cepat (Klik untuk memilih):</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1622286342621-4bd786c2447c?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">✂️ Potong Dewasa</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">💈 Potong Komplit</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">👦 Potong Anak/Balita</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">🪒 Cukur Kumis/Jenggot</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1599351431202-1e0f0137899a?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">⚡ Hair Tattoo (Ukir)</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">🎨 Semir Rambut</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1519699047748-de8e457a634e?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">💆 Cuci & Creambath</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">🧴 Pomade Wijaya</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">📦 Matte Clay</button>
                                <button type="button" onclick="setPresetPhoto('https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=800&q=80')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-emerald-600 border border-white/10 text-[10px] font-bold text-slate-200 transition">🌿 Hair Tonic</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAIL INFORMASI LAYANAN / PRODUK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Nama Item -->
                <div>
                    <label for="item_name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Nama Layanan / Produk *
                    </label>
                    <input type="text" id="item_name" name="item_name" required 
                        value="<?= htmlspecialchars($item['item_name'] ?? '') ?>" 
                        placeholder="Contoh: Fade Haircut Modern"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white text-sm font-semibold transition outline-none">
                </div>

                <!-- Harga -->
                <div>
                    <label for="price" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Tarif Harga (Rp) *
                    </label>
                    <input type="number" id="price" name="price" required min="0" step="1000"
                        value="<?= htmlspecialchars($item['price'] ?? '') ?>" 
                        placeholder="Contoh: 35000"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white text-sm font-semibold transition outline-none font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Kategori Item -->
                <div>
                    <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Kategori *
                    </label>
                    <select id="category_id" name="category_id" required 
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white text-sm font-semibold transition outline-none">
                        <option value="" class="bg-slate-900 text-slate-400">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($item && $item['category_id'] == $cat['id']) ? 'selected' : '' ?> class="bg-slate-900 text-white">
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status Aktif Switch Toggle -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Status Item di Web
                    </label>
                    <label class="flex items-center space-x-3 p-3 bg-slate-950/70 border border-slate-700/80 rounded-2xl cursor-pointer hover:border-emerald-500 transition">
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (!$item || $item['is_active']) ? 'checked' : '' ?>
                            class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-700 bg-slate-900">
                        <span class="text-xs font-bold text-slate-200">
                            Aktif (Dapat Dipesan Oleh Pelanggan)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Deskripsi Item -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Deskripsi Layanan / Produk
                </label>
                <textarea id="description" name="description" rows="3" 
                    placeholder="Jelaskan detail proses layanan, fasilitas, atau manfaat produk penata rambut..."
                    class="w-full px-4 py-3 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white text-sm transition outline-none"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
            </div>

            <!-- Tombol Simpan Item -->
            <div class="pt-6 border-t border-white/10 flex items-center justify-end space-x-3">
                <a href="/admin/items" class="px-6 py-3 rounded-2xl border border-white/10 text-xs font-bold text-slate-300 hover:bg-white/10 transition">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition shadow-xl shadow-emerald-600/35 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Perubahan & Foto</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewLocalFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('live-preview-img').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewUrlInput(url) {
    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
        document.getElementById('live-preview-img').src = url;
    }
}

function setPresetPhoto(url) {
    document.getElementById('image_url').value = url;
    document.getElementById('live-preview-img').src = url;
}
</script>

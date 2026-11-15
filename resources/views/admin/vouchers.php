<?php
/**
 * View: Manajemen Voucher & Kupon Promosi (Admin / Staff)
 */
?>

<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center space-x-3">
                <span class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-ticket-alt"></i>
                </span>
                <span>Manajemen Voucher & Promo</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Kelola kupon diskon, promosi khusus, kuota pemakaian, dan masa aktif voucher untuk pelanggan.
            </p>
        </div>

        <button type="button" onclick="openVoucherModal()" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-600/30 flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Tambah Voucher Baru</span>
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <?php
        $totalVouchers = count($vouchers);
        $activeVouchers = 0;
        $totalUsed = 0;
        foreach ($vouchers as $v) {
            if ($v['is_active']) $activeVouchers++;
            $totalUsed += (int) $v['used_count'];
        }
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-3xl bg-slate-900/80 border border-white/10 shadow-lg backdrop-blur-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Total Voucher</span>
                <span class="text-3xl font-black text-white mt-1 block"><?= $totalVouchers ?></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center text-xl">
                <i class="fas fa-tags"></i>
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-slate-900/80 border border-white/10 shadow-lg backdrop-blur-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Voucher Aktif</span>
                <span class="text-3xl font-black text-emerald-400 mt-1 block"><?= $activeVouchers ?></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-slate-900/80 border border-white/10 shadow-lg backdrop-blur-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Total Penggunaan</span>
                <span class="text-3xl font-black text-amber-400 mt-1 block"><?= $totalUsed ?> <span class="text-xs font-bold text-slate-400">Kali</span></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xl">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- Table Voucher -->
    <div class="rounded-3xl bg-slate-900/80 border border-white/10 shadow-xl overflow-hidden backdrop-blur-sm">
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-base font-black text-white flex items-center space-x-2">
                <i class="fas fa-list text-emerald-400"></i>
                <span>Daftar Kode Promo & Kupon Diskon</span>
            </h3>
            <span class="text-xs text-slate-400"><?= count($vouchers) ?> Voucher Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-white/5 text-[11px] uppercase tracking-wider text-slate-400 border-b border-white/10">
                    <tr>
                        <th class="px-5 py-3.5">Kode & Judul</th>
                        <th class="px-5 py-3.5">Besaran Diskon</th>
                        <th class="px-5 py-3.5">Min. Belanja</th>
                        <th class="px-5 py-3.5">Layanan</th>
                        <th class="px-5 py-3.5">Pemakaian</th>
                        <th class="px-5 py-3.5">Kadaluarsa</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($vouchers)): ?>
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-slate-400 text-xs">
                            Belum ada voucher yang dibuat. Klik tombol <strong>Tambah Voucher Baru</strong> untuk membuat promo.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($vouchers as $v): ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-5 py-4">
                                <span class="font-mono font-black text-emerald-400 text-sm tracking-wider block"><?= htmlspecialchars($v['code']) ?></span>
                                <span class="font-bold text-white text-xs block"><?= htmlspecialchars($v['title']) ?></span>
                                <?php if (!empty($v['description'])): ?>
                                <span class="text-[11px] text-slate-400 block truncate max-w-xs"><?= htmlspecialchars($v['description']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4">
                                <?php if ($v['discount_type'] === 'percentage'): ?>
                                    <span class="font-black text-amber-400 text-sm"><?= $v['discount_value'] ?>%</span>
                                    <?php if (!empty($v['max_discount'])): ?>
                                    <span class="text-[10px] text-slate-400 block">Maks. Rp <?= number_format($v['max_discount'], 0, ',', '.') ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="font-black text-emerald-400 text-sm">Rp <?= number_format($v['discount_value'], 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 font-semibold text-xs text-slate-300">
                                Rp <?= number_format($v['min_purchase'], 0, ',', '.') ?>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-white/10 text-slate-300">
                                    <?= htmlspecialchars($v['service_type']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs">
                                <span class="font-bold text-white"><?= $v['used_count'] ?></span> / <span class="text-slate-400"><?= $v['usage_limit'] ?></span>
                                <div class="w-16 h-1.5 bg-slate-800 rounded-full mt-1 overflow-hidden">
                                    <?php $pct = min(100, round(($v['used_count'] / max(1, $v['usage_limit'])) * 100)); ?>
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: <?= $pct ?>%"></div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs font-mono text-slate-400">
                                <?= !empty($v['expires_at']) ? date('d/m/Y', strtotime($v['expires_at'])) : 'Selamanya' ?>
                            </td>
                            <td class="px-5 py-4">
                                <?php if ($v['is_active']): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Aktif</span>
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                    <span>Nonaktif</span>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <form action="/admin/vouchers/toggle" method="POST" class="inline-block">
                                    <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= $v['is_active'] ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/40 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/40 border border-emerald-500/30' ?>" title="<?= $v['is_active'] ? 'Nonaktifkan Kupon' : 'Aktifkan Kupon' ?>">
                                        <?= $v['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah Voucher Baru -->
<div id="voucher-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden animate-fade-in">
    <div class="bg-slate-900 border border-white/10 rounded-3xl max-w-lg w-full p-6 sm:p-8 text-white shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeVoucherModal()" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl font-bold">&times;</button>

        <h3 class="text-xl font-black text-white mb-1 flex items-center space-x-2">
            <i class="fas fa-ticket-alt text-emerald-400"></i>
            <span>Tambah Voucher Promo Baru</span>
        </h3>
        <p class="text-xs text-slate-400 mb-6">Buat kode diskon baru untuk menarik transaksi pelanggan.</p>

        <form action="/admin/vouchers/create" method="POST" class="space-y-4">
            <div>
                <label for="v_code" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Kode Voucher *</label>
                <input type="text" id="v_code" name="code" required uppercase placeholder="Contoh: DISKONJUMAT" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-500 text-white font-mono uppercase text-sm outline-none">
            </div>

            <div>
                <label for="v_title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Judul Promo *</label>
                <input type="text" id="v_title" name="title" required placeholder="Contoh: Diskon Spesial Hari Jumat" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-500 text-white text-sm outline-none">
            </div>

            <div>
                <label for="v_desc" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Deskripsi Singkat</label>
                <textarea id="v_desc" name="description" rows="2" placeholder="Potongan diskon pangkas rambut untuk pelanggan setia..." class="w-full px-4 py-2 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-500 text-white text-xs outline-none"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="v_type" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Tipe Diskon *</label>
                    <select id="v_type" name="discount_type" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-white/10 focus:border-emerald-500 text-white text-sm outline-none">
                        <option value="fixed">Nominal Rupiah (Rp)</option>
                        <option value="percentage">Persentase (%)</option>
                    </select>
                </div>
                <div>
                    <label for="v_val" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Nilai Diskon *</label>
                    <input type="number" id="v_val" name="discount_value" required min="1" placeholder="Contoh: 10000 atau 20" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-500 text-white text-sm outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="v_min" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Min. Belanja (Rp)</label>
                    <input type="number" id="v_min" name="min_purchase" value="0" min="0" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-500 text-white text-sm outline-none">
                </div>
                <div>
                    <label for="v_max" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Maks. Diskon (Rp, Jika %)</label>
                    <input type="number" id="v_max" name="max_discount" placeholder="Kosongkan jika bebas" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-500 text-white text-sm outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="v_limit" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Batas Kuota Pemakaian</label>
                    <input type="number" id="v_limit" name="usage_limit" value="100" min="1" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-500 text-white text-sm outline-none">
                </div>
                <div>
                    <label for="v_service" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Berlaku Untuk</label>
                    <select id="v_service" name="service_type" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-white/10 focus:border-emerald-500 text-white text-sm outline-none">
                        <option value="all">Semua Layanan</option>
                        <option value="barbershop">Pangkas di Tempat Saja</option>
                        <option value="home_service">Cukur Panggilan (Home Service)</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="v_expires" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Tanggal Kadaluarsa (Opsional)</label>
                <input type="date" id="v_expires" name="expires_at" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-white/10 focus:border-emerald-500 text-white text-sm outline-none">
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeVoucherModal()" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 text-xs font-bold transition">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-extrabold uppercase tracking-wider transition shadow-lg shadow-emerald-600/30">Simpan Voucher</button>
            </div>
        </form>
    </div>
</div>

<script>
function openVoucherModal() {
    document.getElementById('voucher-modal').classList.remove('hidden');
}
function closeVoucherModal() {
    document.getElementById('voucher-modal').classList.add('hidden');
}
</script>


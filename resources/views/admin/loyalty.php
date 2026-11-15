<?php
/**
 * View: Manajemen Loyalitas & Poin VIP Pelanggan (Admin / Staff)
 */
?>

<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center space-x-3">
                <span class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-crown"></i>
                </span>
                <span>Loyalitas & Membership VIP</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Pantau tingkat keaktifan pelanggan, peringkat belanja, saldo poin loyalitas, serta berikan reward poin apresiasi.
            </p>
        </div>

        <button type="button" onclick="openRewardModal()" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider transition shadow-lg shadow-amber-500/30 flex items-center space-x-2">
            <i class="fas fa-coins"></i>
            <span>Beri Reward Poin ke Pelanggan</span>
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <?php
        $totalCustomers = count($customers);
        $totalPointsCirculated = 0;
        $vipCount = 0;
        foreach ($leaderboard as $lb) {
            $totalPointsCirculated += (int) $lb['points'];
            if (in_array(strtolower($lb['tier'] ?? ''), ['silver', 'gold', 'platinum'])) {
                $vipCount++;
            }
        }
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-3xl bg-slate-900/80 border border-white/10 shadow-lg backdrop-blur-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Pelanggan Terdaftar</span>
                <span class="text-3xl font-black text-white mt-1 block"><?= $totalCustomers ?></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center text-xl">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-slate-900/80 border border-white/10 shadow-lg backdrop-blur-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Member Ber-Tier VIP</span>
                <span class="text-3xl font-black text-amber-400 mt-1 block"><?= $vipCount ?> <span class="text-xs font-bold text-slate-400">VIP</span></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xl">
                <i class="fas fa-gem"></i>
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-slate-900/80 border border-white/10 shadow-lg backdrop-blur-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Poin Tersirkulasi</span>
                <span class="text-3xl font-black text-emerald-400 mt-1 block"><?= number_format($totalPointsCirculated, 0, ',', '.') ?> <span class="text-xs font-bold text-slate-400">Poin</span></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl">
                <i class="fas fa-coins"></i>
            </div>
        </div>
    </div>

    <!-- Table Leaderboard / Data Loyalitas Pelanggan -->
    <div class="rounded-3xl bg-slate-900/80 border border-white/10 shadow-xl overflow-hidden backdrop-blur-sm">
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-base font-black text-white flex items-center space-x-2">
                <i class="fas fa-trophy text-amber-400"></i>
                <span>Tabel Loyalitas & Peringkat Pelanggan</span>
            </h3>
            <span class="text-xs text-slate-400">Konversi: 1 Poin = Rp 50</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-white/5 text-[11px] uppercase tracking-wider text-slate-400 border-b border-white/10">
                    <tr>
                        <th class="px-5 py-3.5 text-center">Rank</th>
                        <th class="px-5 py-3.5">Pelanggan</th>
                        <th class="px-5 py-3.5">Status Tier VIP</th>
                        <th class="px-5 py-3.5">Kunjungan</th>
                        <th class="px-5 py-3.5">Total Belanja</th>
                        <th class="px-5 py-3.5">Saldo Poin</th>
                        <th class="px-5 py-3.5">Nilai Rupiah</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($leaderboard)): ?>
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-slate-400 text-xs">
                            Belum ada catatan aktivitas loyalitas pelanggan.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $rank = 1; foreach ($leaderboard as $lb): ?>
                        <?php
                            $t = strtolower($lb['tier'] ?? 'bronze');
                            $tierBadgeClass = 'bg-slate-700/60 text-slate-300 border-slate-600/40';
                            if ($t === 'silver') {
                                $tierBadgeClass = 'bg-slate-700/60 text-slate-200 border-slate-400/50';
                            } elseif ($t === 'gold') {
                                $tierBadgeClass = 'bg-amber-500/20 text-amber-300 border-amber-400/50';
                            } elseif ($t === 'platinum') {
                                $tierBadgeClass = 'bg-cyan-500/20 text-cyan-300 border-cyan-400/50';
                            }
                        ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-5 py-4 text-center">
                                <?php if ($rank === 1): ?>
                                    <span class="w-7 h-7 rounded-full bg-amber-500 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-md shadow-amber-500/30">1</span>
                                <?php elseif ($rank === 2): ?>
                                    <span class="w-7 h-7 rounded-full bg-slate-300 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-md">2</span>
                                <?php elseif ($rank === 3): ?>
                                    <span class="w-7 h-7 rounded-full bg-amber-700 text-white font-black text-xs inline-flex items-center justify-center shadow-md">3</span>
                                <?php else: ?>
                                    <span class="font-bold text-slate-400 text-xs"><?= $rank ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-extrabold text-white text-sm block"><?= htmlspecialchars($lb['fullname']) ?></span>
                                <span class="text-xs text-slate-400 font-mono"><?= htmlspecialchars($lb['phone'] ?? '-') ?></span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border <?= $tierBadgeClass ?>">
                                    <span><?= htmlspecialchars($lb['tier'] ?? 'Bronze') ?></span>
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-xs text-slate-300">
                                <?= $lb['visit_count'] ?> kali
                            </td>
                            <td class="px-5 py-4 font-extrabold text-xs text-white">
                                Rp <?= number_format($lb['total_spend'], 0, ',', '.') ?>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-black text-amber-400 text-sm"><?= number_format($lb['points'], 0, ',', '.') ?></span>
                                <span class="text-[10px] text-slate-400 block">Poin VIP</span>
                            </td>
                            <td class="px-5 py-4 font-black text-emerald-400 text-xs">
                                Rp <?= number_format($lb['points'] * 50, 0, ',', '.') ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button type="button" onclick="quickReward(<?= $lb['user_id'] ?>, '<?= htmlspecialchars(addslashes($lb['fullname'])) ?>')" class="px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 border border-amber-500/30 text-xs font-bold transition flex items-center space-x-1 mx-auto">
                                    <i class="fas fa-gift"></i>
                                    <span>Beri Poin</span>
                                </button>
                            </td>
                        </tr>
                        <?php $rank++; endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Beri Poin Reward -->
<div id="reward-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden animate-fade-in">
    <div class="bg-slate-900 border border-white/10 rounded-3xl max-w-md w-full p-6 sm:p-8 text-white shadow-2xl relative">
        <button type="button" onclick="closeRewardModal()" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl font-bold">&times;</button>

        <h3 class="text-xl font-black text-white mb-1 flex items-center space-x-2">
            <i class="fas fa-coins text-amber-400"></i>
            <span>Beri Poin Reward Apresiasi</span>
        </h3>
        <p class="text-xs text-slate-400 mb-6">Poin langsung ditambahkan ke saldo pelanggan untuk diskon pemesanan.</p>

        <form action="/admin/loyalty/reward" method="POST" class="space-y-4">
            <div>
                <label for="reward_user_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Pilih Pelanggan *</label>
                <select id="reward_user_id" name="user_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-white/10 focus:border-amber-500 text-white text-sm outline-none">
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['fullname']) ?> (<?= htmlspecialchars($c['phone'] ?? $c['username']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="reward_points" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Jumlah Poin Bonus *</label>
                <input type="number" id="reward_points" name="points" required min="1" value="50" placeholder="Contoh: 50" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-amber-500 text-white text-sm outline-none">
                <span class="text-[11px] text-slate-400 block mt-1">50 Poin = Rp 2.500 diskon potongan belanja.</span>
            </div>

            <div>
                <label for="reward_note" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Catatan / Alasan Bonus</label>
                <input type="text" id="reward_note" name="note" value="Bonus reward apresiasi pelanggan setia dari staf kasir" placeholder="Contoh: Hadiah ulang tahun / Kompensasi" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-amber-500 text-white text-xs outline-none">
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeRewardModal()" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 text-xs font-bold transition">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider transition shadow-lg shadow-amber-500/30">Kirim Poin</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRewardModal() {
    document.getElementById('reward-modal').classList.remove('hidden');
}
function closeRewardModal() {
    document.getElementById('reward-modal').classList.add('hidden');
}
function quickReward(userId, userName) {
    const sel = document.getElementById('reward_user_id');
    if (sel) {
        sel.value = userId;
    }
    openRewardModal();
}
</script>


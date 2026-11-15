<?php
/**
 * View: Dashboard Membership VIP & Poin Loyalitas Pelanggan
 * Sesuai Fitur Manajemen Promosi & Loyalitas Pelanggan
 */
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">

    <!-- Header Section -->
    <div class="text-center max-w-3xl mx-auto mb-10">
        <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-bold uppercase tracking-widest mb-3 backdrop-blur-sm">
            <i class="fas fa-crown text-amber-400"></i>
            <span>Wijaya Exclusive Loyalty Club</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
            Program <span class="bg-gradient-to-r from-amber-400 via-emerald-400 to-teal-400 bg-clip-text text-transparent">VIP & Poin Loyalitas</span>
        </h1>
        <p class="mt-3 text-slate-300 text-sm sm:text-base leading-relaxed">
            Apresiasi eksklusif untuk pelanggan setia Wijaya Barbershop. Kumpulkan poin di setiap transaksi pangkas rambut dan nikmati diskon otomatis tanpa syarat!
        </p>
    </div>

    <?php if ($isLoggedIn && $loyalty): ?>
        <?php
            $tier = strtolower($loyalty['tier'] ?? 'bronze');
            $tierColors = [
                'bronze' => [
                    'bg' => 'from-amber-950/80 via-slate-900 to-amber-900/40',
                    'border' => 'border-amber-700/50',
                    'badge_bg' => 'bg-amber-800/40 text-amber-300 border-amber-600/40',
                    'icon' => 'fa-medal text-amber-500',
                    'glow' => 'shadow-amber-900/40'
                ],
                'silver' => [
                    'bg' => 'from-slate-800 via-slate-900 to-slate-800/80',
                    'border' => 'border-slate-400/50',
                    'badge_bg' => 'bg-slate-700/60 text-slate-200 border-slate-400/40',
                    'icon' => 'fa-shield-halved text-slate-300',
                    'glow' => 'shadow-slate-500/25'
                ],
                'gold' => [
                    'bg' => 'from-amber-900/60 via-slate-900 to-yellow-950/70',
                    'border' => 'border-amber-400/60',
                    'badge_bg' => 'bg-amber-500/20 text-amber-300 border-amber-400/50',
                    'icon' => 'fa-crown text-amber-400',
                    'glow' => 'shadow-amber-500/30'
                ],
                'platinum' => [
                    'bg' => 'from-purple-950/60 via-slate-900 to-cyan-950/60',
                    'border' => 'border-cyan-400/60',
                    'badge_bg' => 'bg-cyan-500/20 text-cyan-300 border-cyan-400/50',
                    'icon' => 'fa-gem text-cyan-300',
                    'glow' => 'shadow-cyan-500/30'
                ]
            ];
            $tStyle = $tierColors[$tier] ?? $tierColors['bronze'];
            $pointsValue = (int)($loyalty['points'] ?? 0) * 50;

            // Target tier berikutnya
            $nextTier = 'Silver';
            $nextTargetSpend = 3000;
            $currentSpend = (int) ($loyalty['total_spend'] ?? 0);
            if ($tier === 'silver') {
                $nextTier = 'Gold';
                $nextTargetSpend = 10000;
            } elseif ($tier === 'gold') {
                $nextTier = 'Platinum';
                $nextTargetSpend = 25000;
            } elseif ($tier === 'platinum') {
                $nextTier = 'Tingkat Tertinggi (VIP Sultan)';
                $nextTargetSpend = $currentSpend;
            }
            $progressPercent = $nextTargetSpend > 0 ? min(100, round(($currentSpend / $nextTargetSpend) * 100)) : 100;
        ?>

        <!-- Grid: Kartu VIP Digital + Ringkasan Keuntungan -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12 items-start">
            
            <!-- Digital Member Card (5 Kolom) -->
            <div class="lg:col-span-5">
                <div class="relative rounded-3xl p-6 sm:p-7 bg-gradient-to-tr <?= $tStyle['bg'] ?> border-2 <?= $tStyle['border'] ?> shadow-2xl <?= $tStyle['glow'] ?> overflow-hidden transition-all duration-300 hover:scale-[1.02]">
                    <!-- Metallic Pattern Overlay -->
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

                    <!-- Card Header -->
                    <div class="flex items-center justify-between pb-6 mb-6 border-b border-white/10 relative z-10">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-black shadow-lg shadow-emerald-500/30">
                                <i class="fas fa-scissors"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-white tracking-wider uppercase">Wijaya Barbershop</h3>
                                <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-widest">Digital VIP Pass</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border <?= $tStyle['badge_bg'] ?>">
                            <i class="fas <?= $tStyle['icon'] ?>"></i>
                            <span><?= htmlspecialchars($loyalty['benefits']['name'] ?? 'Bronze Member') ?></span>
                        </span>
                    </div>

                    <!-- Member Details -->
                    <div class="space-y-4 mb-6 relative z-10">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest block mb-0.5">Nama Anggota VIP</span>
                            <h4 class="text-xl sm:text-2xl font-black text-white tracking-tight truncate">
                                <?= htmlspecialchars($loyalty['fullname'] ?? 'Pelanggan Wijaya') ?>
                            </h4>
                            <span class="text-xs text-slate-400 font-mono">ID: WYJ-<?= str_pad((string)($loyalty['user_id'] ?? 0), 5, '0', STR_PAD_LEFT) ?> &bull; <?= htmlspecialchars($loyalty['phone'] ?? '-') ?></span>
                        </div>

                        <!-- Poin Counter Highlight -->
                        <div class="p-4 rounded-2xl bg-black/40 border border-white/10 backdrop-blur-md flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-amber-300 font-bold uppercase tracking-widest block">Saldo Poin Loyalitas</span>
                                <div class="flex items-baseline space-x-1.5">
                                    <span class="text-3xl font-black text-white"><?= number_format($loyalty['points'] ?? 0, 0, ',', '.') ?></span>
                                    <span class="text-xs font-extrabold text-amber-400">POIN</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Nilai Diskon Belanja</span>
                                <span class="text-base font-black text-emerald-400">Rp <?= number_format($pointsValue, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Metrics -->
                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-white/10 text-xs relative z-10">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">Total Kunjungan</span>
                            <span class="font-extrabold text-white text-sm"><?= (int)($loyalty['visit_count'] ?? 0) ?> Kali Pangkas</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 block text-[10px] uppercase">Total Transaksi</span>
                            <span class="font-extrabold text-white text-sm">Rp <?= number_format($loyalty['total_spend'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar ke Tier Berikutnya -->
                <?php if ($tier !== 'platinum'): ?>
                <div class="mt-4 p-5 rounded-3xl bg-slate-900/80 border border-slate-800 backdrop-blur-sm">
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span class="text-slate-300 flex items-center space-x-1.5">
                            <i class="fas fa-arrow-trend-up text-emerald-400"></i>
                            <span>Menuju Tier <?= $nextTier ?></span>
                        </span>
                        <span class="text-amber-400"><?= $progressPercent ?>%</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-800 rounded-full overflow-hidden p-0.5 border border-slate-700">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-amber-400 rounded-full transition-all duration-500" style="width: <?= $progressPercent ?>%"></div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2.5">
                        Belanja <strong>Rp <?= number_format(max(0, $nextTargetSpend - $currentSpend), 0, ',', '.') ?></strong> lagi untuk membuka diskon lebih besar dan keistimewaan tier <strong><?= $nextTier ?></strong>!
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Manfaat & Hak Istimewa Member Anda (7 Kolom) -->
            <div class="lg:col-span-7 space-y-5">
                <!-- Status Keuntungan Aktif -->
                <div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm">
                    <div class="flex items-center space-x-3 mb-5 pb-4 border-b border-slate-800">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center font-bold text-base">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white">Keuntungan Eksklusif Anda</h3>
                            <p class="text-xs text-slate-400">Otomatis aktif setiap Anda melakukan booking / pembayaran</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-6">
                        <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Diskon Transaksi</span>
                            <div class="flex items-baseline space-x-1">
                                <span class="text-2xl font-black text-emerald-400"><?= $loyalty['benefits']['discount_percent'] ?>%</span>
                                <span class="text-xs text-slate-300 font-bold">OFF</span>
                            </div>
                            <span class="text-[11px] text-slate-400 block mt-1">Otomatis terpotong di kasir</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Multiplier Poin</span>
                            <div class="flex items-baseline space-x-1">
                                <span class="text-2xl font-black text-amber-400"><?= $loyalty['benefits']['point_multiplier'] ?>x</span>
                                <span class="text-xs text-slate-300 font-bold">Laju Poin</span>
                            </div>
                            <span class="text-[11px] text-slate-400 block mt-1">Lebih cepat kumpul poin</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Antrean Prioritas</span>
                            <div class="flex items-baseline space-x-1">
                                <span class="text-lg font-black <?= $tier === 'platinum' ? 'text-cyan-400' : 'text-slate-400' ?>">
                                    <?= $tier === 'platinum' ? 'VIP Priority' : 'Standard' ?>
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400 block mt-1"><?= $tier === 'platinum' ? 'Jalur antrean cepat' : 'Tersedia di Platinum' ?></span>
                        </div>
                    </div>

                    <!-- Quick Booking Shortcut with VIP Benefit -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="/checkout" class="flex-1 py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-center text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-600/30 flex items-center justify-center space-x-2">
                            <i class="fas fa-cut"></i>
                            <span>Gunakan Diskon di Checkout</span>
                        </a>
                        <a href="/home-service" class="flex-1 py-3 px-4 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-center text-xs uppercase tracking-wider transition shadow-lg shadow-amber-500/30 flex items-center justify-center space-x-2">
                            <i class="fas fa-motorcycle"></i>
                            <span>Booking Cukur Panggilan</span>
                        </a>
                    </div>
                </div>

                <!-- Kupon Diskon Aktif yang Bisa Dipakai -->
                <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                        <h4 class="text-sm font-black text-white flex items-center space-x-2">
                            <i class="fas fa-ticket-alt text-emerald-400"></i>
                            <span>Kupon Promo Tersedia Untuk Anda</span>
                        </h4>
                        <a href="/promotions" class="text-xs text-emerald-400 hover:text-emerald-300 font-bold">Lihat Semua &rarr;</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php foreach (array_slice($vouchers, 0, 4) as $vc): ?>
                        <div class="p-3.5 rounded-2xl bg-slate-800/60 border border-dashed border-slate-700 flex items-center justify-between hover:border-emerald-500/50 transition group">
                            <div>
                                <span class="font-mono font-black text-emerald-400 text-sm tracking-wider block"><?= htmlspecialchars($vc['code']) ?></span>
                                <span class="text-[11px] text-slate-300 font-semibold block"><?= htmlspecialchars($vc['title']) ?></span>
                                <span class="text-[10px] text-slate-400">Min. Rp <?= number_format($vc['min_purchase'], 0, ',', '.') ?></span>
                            </div>
                            <button type="button" onclick="copyVoucherCode('<?= htmlspecialchars($vc['code']) ?>')" class="px-3 py-1.5 rounded-xl bg-slate-700/80 group-hover:bg-emerald-600 text-slate-200 group-hover:text-white text-xs font-bold transition flex items-center space-x-1">
                                <i class="fas fa-copy"></i>
                                <span>Salin</span>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Poin & Leaderboard VIP -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            <!-- Riwayat Transaksi Poin (7 Kolom) -->
            <div class="lg:col-span-7 rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-7 shadow-xl backdrop-blur-sm">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
                    <h3 class="text-base font-black text-white flex items-center space-x-2">
                        <i class="fas fa-history text-amber-400"></i>
                        <span>Buku Catatan Riwayat Poin (Ledger)</span>
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">1 Poin = Rp 50</span>
                </div>

                <?php if (empty($history)): ?>
                <div class="text-center py-10 text-slate-400 text-xs">
                    <i class="fas fa-coins text-3xl text-slate-600 mb-2 block"></i>
                    Belum ada riwayat transaksi poin. Selesaikan transaksi pertama Anda untuk mulai mengumpulkan poin!
                </div>
                <?php else: ?>
                <div class="divide-y divide-slate-800 max-h-80 overflow-y-auto pr-1">
                    <?php foreach ($history as $h): ?>
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div class="flex items-start space-x-3">
                            <span class="w-8 h-8 rounded-xl <?= $h['points'] >= 0 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' ?> flex items-center justify-center flex-shrink-0 font-bold text-xs mt-0.5">
                                <i class="fas <?= $h['points'] >= 0 ? 'fa-arrow-down' : 'fa-arrow-up' ?>"></i>
                            </span>
                            <div>
                                <span class="font-bold text-white block text-xs sm:text-sm"><?= htmlspecialchars($h['description'] ?? 'Aktivitas Loyalitas') ?></span>
                                <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y, H:i', strtotime($h['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-sm <?= ($h['points'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-rose-400' ?>">
                                <?= ($h['points'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($h['points'] ?? 0, 0, ',', '.') ?> Poin
                            </span>
                            <span class="text-[10px] text-slate-400 block font-mono">Saldo: <?= number_format($h['balance_after'] ?? ($h['points'] ?? 0), 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Top VIP Leaderboard (5 Kolom) -->
            <div class="lg:col-span-5 rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-7 shadow-xl backdrop-blur-sm">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
                    <h3 class="text-base font-black text-white flex items-center space-x-2">
                        <i class="fas fa-trophy text-amber-400"></i>
                        <span>Peringkat Pelanggan Terloyal</span>
                    </h3>
                    <span class="text-[11px] text-amber-400 font-bold uppercase tracking-wider">Top Member</span>
                </div>

                <div class="space-y-3">
                    <?php $rank = 1; foreach ($leaderboard as $lb): ?>
                    <?php
                        $medalIcon = 'fa-medal text-slate-400';
                        $medalBg = 'bg-slate-800 text-slate-300';
                        if ($rank === 1) {
                            $medalIcon = 'fa-crown text-amber-300';
                            $medalBg = 'bg-amber-500/20 text-amber-300 border border-amber-500/30';
                        } elseif ($rank === 2) {
                            $medalIcon = 'fa-medal text-slate-300';
                            $medalBg = 'bg-slate-700/60 text-slate-200 border border-slate-500/30';
                        } elseif ($rank === 3) {
                            $medalIcon = 'fa-medal text-amber-600';
                            $medalBg = 'bg-amber-900/40 text-amber-400 border border-amber-700/30';
                        }
                    ?>
                    <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-xl <?= $medalBg ?> flex items-center justify-center font-black text-xs flex-shrink-0">
                                <i class="fas <?= $medalIcon ?>"></i>
                            </span>
                            <div class="truncate">
                                <span class="font-extrabold text-white text-xs sm:text-sm block truncate"><?= htmlspecialchars($lb['fullname'] ?? $lb['username'] ?? 'Pelanggan Wijaya') ?></span>
                                <span class="text-[10px] uppercase font-bold text-emerald-400"><?= htmlspecialchars(ucfirst($lb['tier'] ?? 'Bronze')) ?> Member</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="font-black text-amber-400 text-xs sm:text-sm"><?= number_format($lb['points'] ?? 0, 0, ',', '.') ?> Pts</span>
                            <span class="text-[10px] text-slate-400 block"><?= (int)($lb['visit_count'] ?? 0) ?>x visit</span>
                        </div>
                    </div>
                    <?php $rank++; endforeach; ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Guest State: Ajakan Bergabung VIP -->
        <div class="max-w-4xl mx-auto rounded-3xl bg-gradient-to-r from-emerald-950/80 via-slate-900 to-teal-950/80 border-2 border-emerald-500/30 p-8 sm:p-12 text-center shadow-2xl mb-12 relative overflow-hidden">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center text-3xl mx-auto mb-6 shadow-xl shadow-emerald-500/30">
                <i class="fas fa-crown"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white mb-3">
                Bergabung dengan <span class="text-emerald-400">Wijaya VIP Club</span> Hari Ini!
            </h2>
            <p class="text-slate-300 text-sm sm:text-base max-w-2xl mx-auto mb-8 leading-relaxed">
                Daftar akun gratis dan langsung dapatkan tier member, kumpulkan poin loyalitas di setiap pangkas rambut atau pemesanan Home Service, dan nikmati diskon khusus hingga 15%!
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/register" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm uppercase tracking-wider transition shadow-xl shadow-emerald-600/40 flex items-center justify-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Daftar Akun Member Gratis</span>
                </a>
                <a href="/login" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-sm uppercase tracking-wider transition border border-slate-700 flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sudah Punya Akun? Masuk</span>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Perbandingan Tingkatan Tier Membership -->
    <div class="mb-14">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Tingkatan Tier & Keuntungan
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Semakin sering Anda mempercayakan penampilan Anda kepada Wijaya Barbershop, semakin mewah perlakuan dan keuntungannya.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Bronze -->
            <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 hover:border-amber-700/60 transition shadow-lg flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-950/60 text-amber-500 border border-amber-800/40 flex items-center justify-center text-xl mb-4 font-bold">
                        <i class="fas fa-medal"></i>
                    </div>
                    <span class="text-[10px] uppercase font-black tracking-widest text-amber-500 block mb-1">Tier Pemula</span>
                    <h3 class="text-xl font-black text-white mb-2">Bronze Member</h3>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">Member otomatis saat Anda pertama kali mendaftar akun pelanggan.</p>
                    
                    <ul class="space-y-2.5 text-xs text-slate-300 mb-6">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>1x Laju Perolehan Poin</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>Tukar Poin Jadi Diskon Belanja</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>Akses Voucher Promo Umum</span>
                        </li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-800 text-[11px] text-slate-400 font-semibold">
                    Syarat: Pendaftaran Akun
                </div>
            </div>

            <!-- Silver -->
            <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-700/80 hover:border-slate-400/80 transition shadow-lg flex flex-col justify-between relative overflow-hidden">
                <span class="absolute top-4 right-4 px-2.5 py-0.5 rounded-full bg-slate-700 text-slate-200 text-[10px] font-black uppercase tracking-wider">Favorit</span>
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-200 border border-slate-500/40 flex items-center justify-center text-xl mb-4 font-bold">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <span class="text-[10px] uppercase font-black tracking-widest text-slate-300 block mb-1">Diskon 5%</span>
                    <h3 class="text-xl font-black text-white mb-2">Silver Member</h3>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">Diskon otomatis langsung aktif di kasir dan checkout tanpa repot kupon.</p>
                    
                    <ul class="space-y-2.5 text-xs text-slate-300 mb-6">
                        <li class="flex items-center space-x-2 text-white font-bold">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>Diskon Otomatis 5% Setiap Order</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>1.2x Multiplier Poin Loyalitas</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>Voucher Spesial Silver Member</span>
                        </li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-800 text-[11px] text-slate-400 font-semibold">
                    Syarat: Belanja Rp 3.000 / 3x Kunjungan
                </div>
            </div>

            <!-- Gold -->
            <div class="p-6 rounded-3xl bg-slate-900/90 border border-amber-500/60 hover:border-amber-400 transition shadow-lg shadow-amber-500/10 flex flex-col justify-between relative overflow-hidden">
                <span class="absolute top-4 right-4 px-2.5 py-0.5 rounded-full bg-amber-500 text-slate-950 text-[10px] font-black uppercase tracking-wider">Populer</span>
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-400/50 flex items-center justify-center text-xl mb-4 font-bold">
                        <i class="fas fa-crown"></i>
                    </div>
                    <span class="text-[10px] uppercase font-black tracking-widest text-amber-400 block mb-1">Diskon 10%</span>
                    <h3 class="text-xl font-black text-white mb-2">Gold Member</h3>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">Perlakuan istimewa dengan potongan harga 10% dan poin super cepat.</p>
                    
                    <ul class="space-y-2.5 text-xs text-slate-300 mb-6">
                        <li class="flex items-center space-x-2 text-white font-bold">
                            <i class="fas fa-check text-amber-400"></i>
                            <span>Diskon Otomatis 10% Semua Layanan</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>1.5x Multiplier Poin Loyalitas</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>Gratis Hair Tonic / Pomade Styling</span>
                        </li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-800 text-[11px] text-amber-400 font-semibold">
                    Syarat: Belanja Rp 10.000 / 8x Kunjungan
                </div>
            </div>

            <!-- Platinum -->
            <div class="p-6 rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-cyan-950/40 border border-cyan-400/60 hover:border-cyan-300 transition shadow-lg shadow-cyan-500/15 flex flex-col justify-between relative overflow-hidden">
                <span class="absolute top-4 right-4 px-2.5 py-0.5 rounded-full bg-cyan-400 text-slate-950 text-[10px] font-black uppercase tracking-wider">VIP Sultan</span>
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 text-cyan-300 border border-cyan-400/50 flex items-center justify-center text-xl mb-4 font-bold">
                        <i class="fas fa-gem"></i>
                    </div>
                    <span class="text-[10px] uppercase font-black tracking-widest text-cyan-300 block mb-1">Diskon 15%</span>
                    <h3 class="text-xl font-black text-white mb-2">Platinum VIP</h3>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">Puncak status kehormatan di Wijaya Barbershop dengan prioritas tertinggi.</p>
                    
                    <ul class="space-y-2.5 text-xs text-slate-300 mb-6">
                        <li class="flex items-center space-x-2 text-white font-bold">
                            <i class="fas fa-check text-cyan-400"></i>
                            <span>Diskon Otomatis 15% Selamanya</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>2x Double Poin di Setiap Transaksi</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>Antrean Prioritas Fast Track</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-emerald-400"></i>
                            <span>Free Minuman Segar & Hot Towel</span>
                        </li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-800 text-[11px] text-cyan-400 font-semibold">
                    Syarat: Belanja Rp 25.000 / 20x Kunjungan
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Copy Voucher Code Helper Script -->
<script>
function copyVoucherCode(code) {
    if (!navigator.clipboard) {
        prompt('Salin kode voucher:', code);
        return;
    }
    navigator.clipboard.writeText(code).then(() => {
        alert('Kode kupon ' + code + ' berhasil disalin! Masukkan pada formulir checkout.');
    }).catch(() => {
        prompt('Salin kode voucher:', code);
    });
}
</script>


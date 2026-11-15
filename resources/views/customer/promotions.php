<?php
/**
 * View: Katalog Voucher Promo & Kupon Diskon Spesial
 * Sesuai Fitur Manajemen Promosi & Loyalitas Pelanggan
 */
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">

    <!-- Header Section -->
    <div class="text-center max-w-3xl mx-auto mb-10">
        <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-bold uppercase tracking-widest mb-3 backdrop-blur-sm">
            <i class="fas fa-tags text-emerald-400"></i>
            <span>Promo & Penawaran Spesial</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
            Katalog <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300 bg-clip-text text-transparent">Voucher & Diskon</span>
        </h1>
        <p class="mt-3 text-slate-300 text-sm sm:text-base leading-relaxed">
            Salin kode kupon di bawah ini dan masukkan pada halaman formulir checkout untuk mendapatkan potongan harga langsung!
        </p>
    </div>

    <!-- Highlight Banner Promo -->
    <div class="mb-12 p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-950/90 via-slate-900 to-teal-950/90 border-2 border-emerald-500/30 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center md:text-left z-10">
            <span class="inline-block text-[11px] font-black uppercase tracking-wider px-3 py-1 bg-amber-500 text-slate-950 rounded-xl">
                Promo Pilihan Bulan Ini
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white">Diskon Hingga 20% Untuk Semua Layanan!</h2>
            <p class="text-slate-300 text-xs sm:text-sm max-w-xl">
                Nikmati pangkas rambut premium dan grooming pria berkualitas dengan harga lebih hemat di Wijaya Barbershop Metro.
            </p>
        </div>
        <div class="flex-shrink-0 z-10">
            <a href="/membership" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs uppercase tracking-wider transition shadow-lg shadow-amber-500/30 flex items-center space-x-2">
                <i class="fas fa-crown"></i>
                <span>Cek Status VIP & Poin Anda</span>
            </a>
        </div>
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Grid Kupon Voucher -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
        <?php if (empty($vouchers)): ?>
        <div class="col-span-full text-center py-12 rounded-3xl bg-slate-900/60 border border-slate-800 text-slate-400">
            <i class="fas fa-ticket text-4xl mb-3 text-slate-600 block"></i>
            <p class="text-sm font-semibold">Saat ini belum ada voucher promo yang aktif. Nantikan promo menarik berikutnya!</p>
        </div>
        <?php else: ?>
            <?php foreach ($vouchers as $v): ?>
            <?php
                $isPercent = ($v['discount_type'] ?? 'fixed') === 'percentage';
                $serviceBadge = 'Semua Layanan';
                $serviceIcon = 'fa-scissors';
                $serviceTargetUrl = '/services';

                if ($v['service_type'] === 'home_service') {
                    $serviceBadge = 'Cukur Panggilan (Home Service)';
                    $serviceIcon = 'fa-motorcycle';
                    $serviceTargetUrl = '/home-service';
                } elseif ($v['service_type'] === 'barbershop') {
                    $serviceBadge = 'Pangkas di Tempat';
                    $serviceIcon = 'fa-shop';
                    $serviceTargetUrl = '/services';
                }
            ?>
            <div class="rounded-3xl bg-slate-900/90 border border-slate-800 hover:border-emerald-500/60 transition-all duration-300 shadow-xl overflow-hidden flex flex-col justify-between group">
                <!-- Voucher Top Half -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center space-x-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-xl">
                            <i class="fas <?= $serviceIcon ?> text-[10px]"></i>
                            <span><?= $serviceBadge ?></span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">
                            <?php if (!empty($v['expires_at'])): ?>
                                S.d. <?= date('d M Y', strtotime($v['expires_at'])) ?>
                            <?php else: ?>
                                Tanpa Batas Waktu
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="text-2xl sm:text-3xl font-black text-white">
                            <?php if ($isPercent): ?>
                                <?= $v['discount_value'] ?>% <span class="text-sm font-bold text-emerald-400">OFF</span>
                            <?php else: ?>
                                Rp <?= number_format($v['discount_value'], 0, ',', '.') ?> <span class="text-sm font-bold text-emerald-400">OFF</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <h3 class="text-base font-extrabold text-white mb-1.5"><?= htmlspecialchars($v['title']) ?></h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4"><?= htmlspecialchars($v['description'] ?? 'Gunakan kupon promo ini untuk potongan transaksi.') ?></p>

                    <!-- Syarat & Ketentuan Ringkas -->
                    <div class="space-y-1 text-[11px] text-slate-400 border-t border-slate-800/80 pt-3">
                        <div class="flex justify-between">
                            <span>Min. Belanja:</span>
                            <span class="font-bold text-slate-200">Rp <?= number_format($v['min_purchase'], 0, ',', '.') ?></span>
                        </div>
                        <?php if ($isPercent && !empty($v['max_discount'])): ?>
                        <div class="flex justify-between">
                            <span>Maks. Potongan:</span>
                            <span class="font-bold text-slate-200">Rp <?= number_format($v['max_discount'], 0, ',', '.') ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between">
                            <span>Sisa Kuota:</span>
                            <span class="font-bold text-slate-200"><?= max(0, $v['usage_limit'] - $v['used_count']) ?> Kupon</span>
                        </div>
                    </div>
                </div>

                <!-- Perforated Divider Line with Cutout Half Circles -->
                <div class="relative flex items-center justify-between px-3 py-1 bg-slate-950/40">
                    <span class="w-4 h-4 rounded-full bg-slate-950 -ml-5 border-r border-slate-800"></span>
                    <div class="w-full border-t border-dashed border-slate-700 mx-2"></div>
                    <span class="w-4 h-4 rounded-full bg-slate-950 -mr-5 border-l border-slate-800"></span>
                </div>

                <!-- Voucher Bottom Coupon Code & Action -->
                <div class="p-5 bg-slate-950/70 border-t border-slate-800/60 flex items-center justify-between gap-3">
                    <div class="flex-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-center font-mono font-black text-emerald-400 text-sm tracking-wider select-all truncate">
                        <?= htmlspecialchars($v['code']) ?>
                    </div>
                    <button type="button" onclick="copyVoucherCode('<?= htmlspecialchars($v['code']) ?>')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/30 flex items-center space-x-1.5 flex-shrink-0">
                        <i class="fas fa-copy"></i>
                        <span>Salin</span>
                    </button>
                    <a href="<?= $serviceTargetUrl ?>" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs transition" title="Gunakan Sekarang">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Panduan 3 Langkah Memakai Voucher -->
    <div class="rounded-3xl bg-slate-900/80 border border-slate-800 p-8 sm:p-10 mb-12">
        <h3 class="text-xl font-black text-white text-center mb-8">Cara Menggunakan Kupon Diskon</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-black text-lg mx-auto">
                    1
                </div>
                <h4 class="text-base font-bold text-white">Salin Kode Kupon</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Pilih voucher yang sesuai dengan kebutuhan layanan Anda lalu klik tombol <strong>Salin</strong>.</p>
            </div>
            <div class="text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-black text-lg mx-auto">
                    2
                </div>
                <h4 class="text-base font-bold text-white">Tempelkan di Formulir Checkout</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Pada halaman pemesanan / checkout, ketikkan atau tempel kode pada kolom voucher lalu klik <strong>Terapkan</strong>.</p>
            </div>
            <div class="text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-black text-lg mx-auto">
                    3
                </div>
                <h4 class="text-base font-bold text-white">Nikmati Potongan Diskon</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Total belanja Anda akan otomatis berkurang secara instan sesuai nominal diskon kupon!</p>
            </div>
        </div>
    </div>

</div>

<!-- Copy Script -->
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


<?php
/**
 * resources/views/admin/analytics.php
 * Halaman Analitik Cerdas — AI Insights Dashboard
 */

// Helper format rupiah
function fmtRp(float $v): string {
    if ($v >= 1_000_000) return 'Rp ' . number_format($v / 1_000_000, 1) . 'Jt';
    if ($v >= 1_000) return 'Rp ' . number_format($v / 1_000, 0) . 'Rb';
    return 'Rp ' . number_format($v, 0, ',', '.');
}

$revenueChart  = $revenue_chart  ?? [];
$revenueTrend  = $revenue_trend  ?? [];
$busyHours     = $busy_hours     ?? [];
$peakDays      = $peak_days      ?? [];
$topServices   = $top_services   ?? [];
$rfmSegments   = $rfm_segments   ?? [];
$churnRisk     = $churn_risk     ?? [];
$smartAlerts   = $smart_alerts   ?? [];
$avgOrder      = $avg_order      ?? 0;
$conversion    = $conversion     ?? 0;
$todayStats    = $today_stats    ?? [];
$aiRoadmap     = $ai_roadmap     ?? [];
$actionPlans   = $action_plans   ?? [];

$maxRevenue   = !empty($revenueChart) ? max(array_column($revenueChart, 'revenue')) : 1;
$maxHourCount = !empty($busyHours) ? max($busyHours) : 1;
$maxDayCount  = !empty($peakDays) ? max(array_column($peakDays, 'count')) : 1;

$trendColor = ($revenueTrend['trend'] ?? 'up') === 'up' ? 'emerald' : 'rose';
$trendIcon  = ($revenueTrend['trend'] ?? 'up') === 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
?>

<!-- ═══════════════════════════ PAGE HEADER ═══════════════════════════ -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-500 flex items-center justify-center shadow-lg">
                <i class="fas fa-brain text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Analitik Cerdas</h1>
                <p class="text-xs text-slate-400 font-medium">AI Insight Engine · Wijaya Barbershop</p>
            </div>
        </div>
    </div>
    <div class="flex items-center space-x-2">
        <span class="text-xs text-purple-400 bg-purple-500/10 border border-purple-500/30 px-3 py-1.5 rounded-full font-semibold flex items-center space-x-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
            <span>AI Aktif · Rule-Based Analytics</span>
        </span>
    </div>
</div>

<!-- ═══════════════════════════ SMART ALERTS ═══════════════════════════ -->
<?php if (!empty($smartAlerts)): ?>
<div class="mb-6 space-y-3">
    <?php foreach ($smartAlerts as $alert): ?>
    <?php
    $clr = $alert['color'];
    $clrMap = [
        'amber'  => 'amber-500/20 border-amber-500/40 text-amber-200',
        'rose'   => 'rose-500/20 border-rose-500/40 text-rose-200',
        'blue'   => 'blue-500/20 border-blue-500/40 text-blue-200',
        'orange' => 'orange-500/20 border-orange-500/40 text-orange-200',
    ];
    $cls = $clrMap[$clr] ?? 'white/10 border-white/20 text-white';
    $icnMap = ['amber'=>'text-amber-400','rose'=>'text-rose-400','blue'=>'text-blue-400','orange'=>'text-orange-400'];
    $icnCls = $icnMap[$clr] ?? 'text-white';
    ?>
    <div class="bg-<?= $cls ?> backdrop-blur-md px-4 py-3 rounded-2xl flex items-center justify-between border">
        <div class="flex items-center space-x-3">
            <i class="fas <?= $alert['icon'] ?> <?= $icnCls ?> text-lg w-5 text-center"></i>
            <div>
                <p class="font-bold text-sm"><?= htmlspecialchars($alert['title']) ?></p>
                <p class="text-xs opacity-80"><?= htmlspecialchars($alert['message']) ?></p>
            </div>
        </div>
        <?php if (!empty($alert['action'])): ?>
        <a href="<?= $alert['action'] ?>" class="ml-4 flex-shrink-0 text-xs font-bold px-3 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 transition">
            <?= htmlspecialchars($alert['action_label'] ?? 'Lihat') ?>
        </a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ═══════════════════════════ KPI CARDS ROW ═══════════════════════════ -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Revenue Trend -->
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Revenue 7 Hari</span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-<?= $trendColor ?>-500/20 text-<?= $trendColor ?>-400">
                <i class="fas <?= $trendIcon ?> mr-1"></i><?= ($revenueTrend['change_pct'] ?? 0) >= 0 ? '+' : '' ?><?= $revenueTrend['change_pct'] ?? 0 ?>%
            </span>
        </div>
        <p class="text-2xl font-black text-white"><?= fmtRp($revenueTrend['current'] ?? 0) ?></p>
        <p class="text-xs text-slate-500 mt-1">vs <?= fmtRp($revenueTrend['previous'] ?? 0) ?> minggu lalu</p>
    </div>

    <!-- Avg Order Value -->
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Rata-rata Order</span>
            <div class="w-7 h-7 rounded-lg bg-teal-500/20 flex items-center justify-center">
                <i class="fas fa-receipt text-teal-400 text-xs"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-white"><?= fmtRp($avgOrder) ?></p>
        <p class="text-xs text-slate-500 mt-1">per transaksi sukses</p>
    </div>

    <!-- Conversion Rate -->
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Konversi Bayar</span>
            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-400 text-xs"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-white"><?= $conversion ?>%</p>
        <p class="text-xs text-slate-500 mt-1">order berbayar / total order</p>
    </div>

    <!-- Today Stats -->
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Hari Ini</span>
            <div class="w-7 h-7 rounded-lg bg-amber-500/20 flex items-center justify-center">
                <i class="fas fa-sun text-amber-400 text-xs"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-white"><?= $todayStats['orders'] ?? 0 ?> <span class="text-sm font-normal text-slate-400">pesanan</span></p>
        <p class="text-xs text-slate-500 mt-1"><?= fmtRp($todayStats['revenue'] ?? 0) ?> · <?= $todayStats['new_customers'] ?? 0 ?> pelanggan baru</p>
    </div>
</div>

<!-- ═══════════════════════════ REKAPITULASI PENGHASILAN MULTI-PERIODE (KARTU RINGKASAN LENGKAP) ═══════════════════════════ -->
<?php
$recap = $revenue_recap ?? [];
$recapPeriods = $recap['periods'] ?? [];
$recapMonthly = $recap['monthly_recap'] ?? [];
$recapCharts  = $recap['charts'] ?? [];

$featuredPeriodKeys = [
    'today',
    'last_7_days',
    'prev_7_days',
    'this_month',
    'last_month',
    'last_2_months',
    'this_year',
    'last_year'
];
?>
<div class="bg-slate-800/70 backdrop-blur-md border border-white/10 rounded-3xl p-5 sm:p-6 mb-6 shadow-xl">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-white/10 gap-3">
        <div>
            <div class="flex items-center space-x-2.5">
                <span class="w-2.5 h-6 rounded-full bg-gradient-to-b from-emerald-400 to-teal-500"></span>
                <h2 class="text-base sm:text-lg font-black text-white">💰 Rekapitulasi Penghasilan Multi-Periode</h2>
            </div>
            <p class="text-xs text-slate-400 mt-1">Ringkasan penghasilan lunas dari Hari Ini, 7 Hari Lalu, Bulan Kemarin, 2 Bulan Terakhir, hingga Tahunan</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-extrabold">
                Grand Total Semua Waktu: Rp <?= number_format($recapPeriods['all_time']['revenue'] ?? 0, 0, ',', '.') ?> (<?= $recapPeriods['all_time']['orders_count'] ?? 0 ?> Order)
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($featuredPeriodKeys as $pk): ?>
        <?php if (empty($recapPeriods[$pk])) continue; $p = $recapPeriods[$pk]; ?>
        <div class="p-4 rounded-2xl bg-slate-900/75 border border-white/10 hover:border-emerald-500/40 transition flex flex-col justify-between group">
            <div>
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[11px] font-extrabold text-emerald-400 uppercase tracking-wider truncate"><?= htmlspecialchars($p['label']) ?></span>
                    <div class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-emerald-400 flex-shrink-0 group-hover:bg-emerald-500 group-hover:text-slate-950 transition">
                        <i class="fas <?= htmlspecialchars($p['icon'] ?? 'fa-wallet') ?> text-xs"></i>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-black text-white font-mono tracking-tight">
                    Rp <?= number_format($p['revenue'], 0, ',', '.') ?>
                </p>
                <p class="text-[11px] text-slate-400 mt-1 flex items-center justify-between">
                    <span><i class="fas fa-calendar-alt mr-1 text-slate-500"></i><?= htmlspecialchars($p['sublabel']) ?></span>
                    <span class="font-bold text-amber-300"><?= $p['orders_count'] ?> Order</span>
                </p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-white/5 flex items-center justify-between text-[10px] text-slate-400">
                <span>Studio: <strong class="text-slate-200"><?= $p['studio_orders'] ?></strong> · Home: <strong class="text-amber-300"><?= $p['home_orders'] ?></strong></span>
                <span class="text-emerald-400 font-bold">QRIS: <?= $p['qris_orders'] ?> | Tunai: <?= $p['tunai_orders'] ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ═══════════════════════════ REVENUE CHART MULTI-PERIODE (DIAGRAM BATANG & GARIS TREN) ═══════════════════════════ -->
<?php
$totalWeekRev = array_sum(array_column($revenueChart, 'revenue'));
$totalWeekOrders = array_sum(array_column($revenueChart, 'orders_count'));
$avgDailyRev = count($revenueChart) > 0 ? $totalWeekRev / count($revenueChart) : 0;
$peakDayItem = null;
foreach ($revenueChart as $rc) {
    if ($peakDayItem === null || $rc['revenue'] > $peakDayItem['revenue']) {
        $peakDayItem = $rc;
    }
}
?>
<div class="bg-slate-800/70 backdrop-blur-md border border-white/10 rounded-3xl p-5 sm:p-6 mb-6 shadow-xl relative overflow-hidden">
    <!-- Header & Toggle Bar Chart / Line Chart -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-4 mb-4 border-b border-white/10 gap-3">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-6 rounded-full bg-emerald-500"></span>
                <h2 class="text-base sm:text-lg font-black text-white flex items-center space-x-2">
                    <span id="dynamic-chart-title">📊 Grafik Rekap Pendapatan: 7 Hari Terakhir</span>
                </h2>
            </div>
            <p id="dynamic-chart-subtitle" class="text-xs text-slate-400 mt-1">Pilih tab periode di bawah untuk melihat grafik 7 Hari Lalu, Bulan Kemarin, 2 Bulan Terakhir, hingga Tahunan</p>
        </div>

        <!-- Mode Tampilan Switcher (Diagram Batang, Garis Tren, Kombinasi) -->
        <div class="inline-flex p-1 bg-slate-900/80 rounded-2xl border border-white/10 text-xs self-start lg:self-auto shadow-inner">
            <button type="button" id="btn-chart-bars" onclick="setAnalyticsChartMode('bars')" 
                class="px-3.5 py-1.5 rounded-xl font-extrabold transition duration-200 bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/30 flex items-center space-x-1.5">
                <i class="fas fa-chart-column text-xs"></i>
                <span>Diagram Batang</span>
            </button>
            <button type="button" id="btn-chart-lines" onclick="setAnalyticsChartMode('lines')" 
                class="px-3.5 py-1.5 rounded-xl font-bold transition duration-200 text-slate-300 hover:text-white hover:bg-slate-800 flex items-center space-x-1.5">
                <i class="fas fa-chart-line text-xs"></i>
                <span>Garis Tren</span>
            </button>
            <button type="button" id="btn-chart-combo" onclick="setAnalyticsChartMode('combo')" 
                class="px-3.5 py-1.5 rounded-xl font-bold transition duration-200 text-slate-300 hover:text-white hover:bg-slate-800 flex items-center space-x-1.5">
                <i class="fas fa-layer-group text-xs"></i>
                <span>Kombinasi</span>
            </button>
        </div>
    </div>

    <!-- Filter Periode Grafik (9 Pilihan Periode) -->
    <div class="flex flex-wrap gap-1.5 mb-5 pb-3 border-b border-white/5">
        <?php
        $periodTabs = [
            '7d'            => '7 Hari Terakhir',
            'prev7d'        => '7 Hari Lalu (Minggu Kemarin)',
            '14d'           => '14 Hari Terakhir',
            'this_month'    => 'Bulan Ini',
            'last_month'    => 'Bulan Lalu / Kemarin',
            'last_2_months' => '2 Bulan Terakhir',
            'last_6_months' => '6 Bulan Terakhir',
            'this_year'     => 'Tahun Ini (' . date('Y') . ')',
            'last_year'     => 'Tahun Lalu (' . ((int)date('Y') - 1) . ')',
        ];
        foreach ($periodTabs as $pKey => $pLabel):
        ?>
        <button type="button" id="tab-period-<?= $pKey ?>" onclick="switchRevenueChartPeriod('<?= $pKey ?>')"
            class="period-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer <?= $pKey === '7d' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md shadow-emerald-500/25' : 'bg-slate-900/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-white/10' ?>">
            <?= htmlspecialchars($pLabel) ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- Mini KPI Ringkasan Periode Aktif -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="p-3 rounded-2xl bg-slate-900/60 border border-white/5">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Total Omset Periode</span>
            <span id="kpi-period-total" class="text-base sm:text-lg font-black text-emerald-400 font-mono">Rp <?= number_format($totalWeekRev, 0, ',', '.') ?></span>
        </div>
        <div class="p-3 rounded-2xl bg-slate-900/60 border border-white/5">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Puncak Tertinggi</span>
            <span id="kpi-period-peak" class="text-sm sm:text-base font-extrabold text-white flex items-center space-x-1">
                <span><?= htmlspecialchars($peakDayItem['label'] ?? '-') ?></span>
                <span class="text-xs font-mono text-emerald-400 font-bold">(<?= fmtRp($maxRevenue) ?>)</span>
            </span>
        </div>
        <div class="p-3 rounded-2xl bg-slate-900/60 border border-white/5">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Rata-Rata Periode</span>
            <span id="kpi-period-avg" class="text-sm sm:text-base font-extrabold text-white font-mono"><?= fmtRp($avgDailyRev) ?></span>
        </div>
        <div class="p-3 rounded-2xl bg-slate-900/60 border border-white/5">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Total Transaksi Selesai</span>
            <span id="kpi-period-orders" class="text-base sm:text-lg font-black text-amber-400 font-mono"><?= $totalWeekOrders ?> <span class="text-xs text-slate-300 font-normal">Pesanan</span></span>
        </div>
    </div>

    <!-- SVG Responsive Chart Container -->
    <div class="relative w-full h-64 sm:h-72">
        <svg id="dynamic-revenue-svg" viewBox="0 0 860 250" class="w-full h-full overflow-visible" preserveAspectRatio="none"></svg>
    </div>
</div>

<!-- ═══════════════════════════ TABEL LENGKAP REKAPITULASI PENGHASILAN (PERIODE & BULANAN) ═══════════════════════════ -->
<div class="bg-slate-800/70 backdrop-blur-md border border-white/10 rounded-3xl p-5 sm:p-6 mb-6 shadow-xl overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-white/10 gap-3">
        <div>
            <div class="flex items-center space-x-2.5">
                <span class="w-2.5 h-6 rounded-full bg-amber-400"></span>
                <h2 class="text-base sm:text-lg font-black text-white">📋 Tabel Detail Rekapitulasi Penghasilan</h2>
            </div>
            <p class="text-xs text-slate-400 mt-1">Rincian pendapatan berdasarkan rentang waktu, metode pembayaran (QRIS vs Tunai), dan jenis layanan (Studio vs Home Service)</p>
        </div>
        <div class="inline-flex p-1 bg-slate-900/80 rounded-2xl border border-white/10 text-xs self-start sm:self-auto">
            <button type="button" id="btn-tab-rekap-periode" onclick="switchRecapTableTab('periode')"
                class="px-3.5 py-1.5 rounded-xl font-extrabold bg-emerald-500 text-slate-950 transition cursor-pointer">
                Rekap Per Periode Waktu
            </button>
            <button type="button" id="btn-tab-rekap-bulanan" onclick="switchRecapTableTab('bulanan')"
                class="px-3.5 py-1.5 rounded-xl font-bold text-slate-300 hover:text-white transition cursor-pointer">
                Rekap Bulanan (<?= ((int)date('Y') - 1) ?> – <?= date('Y') ?>)
            </button>
        </div>
    </div>

    <!-- TAB 1: Tabel Rekap Per Periode Waktu (Hari Ini, 7 Hari Lalu, Bulan Lalu, 2 Bulan Terakhir, Tahunan, dll) -->
    <div id="table-rekap-periode" class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 uppercase tracking-wider">
                <tr>
                    <th class="p-3.5 pl-4">Periode Rekapitulasi</th>
                    <th class="p-3.5">Rentang Waktu</th>
                    <th class="p-3.5 text-center">Total Order</th>
                    <th class="p-3.5 text-center">Studio / Home</th>
                    <th class="p-3.5 text-right">Omset QRIS</th>
                    <th class="p-3.5 text-right">Omset Tunai</th>
                    <th class="p-3.5 text-right">Rata-Rata / Order</th>
                    <th class="p-3.5 pr-4 text-right">Total Penghasilan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-slate-200">
                <?php foreach ($recapPeriods as $p): ?>
                <tr class="hover:bg-white/5 transition">
                    <td class="p-3.5 pl-4 font-bold text-white">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-7 h-7 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <i class="fas <?= htmlspecialchars($p['icon'] ?? 'fa-calendar') ?> text-xs"></i>
                            </span>
                            <span><?= htmlspecialchars($p['label']) ?></span>
                        </div>
                    </td>
                    <td class="p-3.5 text-slate-400 font-mono text-[11px]"><?= htmlspecialchars($p['sublabel']) ?></td>
                    <td class="p-3.5 text-center font-black text-amber-300"><?= $p['orders_count'] ?></td>
                    <td class="p-3.5 text-center text-[11px]">
                        <span class="px-2 py-0.5 rounded bg-slate-900 border border-white/10 text-slate-200"><?= $p['studio_orders'] ?> Studio</span>
                        <span class="px-2 py-0.5 rounded bg-amber-500/15 border border-amber-500/30 text-amber-300 ml-1"><?= $p['home_orders'] ?> Home</span>
                    </td>
                    <td class="p-3.5 text-right font-mono text-emerald-300">Rp <?= number_format($p['qris_revenue'], 0, ',', '.') ?></td>
                    <td class="p-3.5 text-right font-mono text-slate-300">Rp <?= number_format($p['tunai_revenue'], 0, ',', '.') ?></td>
                    <td class="p-3.5 text-right font-mono text-slate-300">Rp <?= number_format($p['avg_order'], 0, ',', '.') ?></td>
                    <td class="p-3.5 pr-4 text-right font-black text-emerald-400 font-mono text-sm">
                        Rp <?= number_format($p['revenue'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- TAB 2: Tabel Rekap Bulanan Lengkap (Januari - Desember 2025 & 2026) -->
    <div id="table-rekap-bulanan" class="overflow-x-auto hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 uppercase tracking-wider">
                <tr>
                    <th class="p-3.5 pl-4">Bulan & Tahun</th>
                    <th class="p-3.5 text-center">Tahun</th>
                    <th class="p-3.5 text-center">Jumlah Transaksi</th>
                    <th class="p-3.5 text-center">Studio / Home</th>
                    <th class="p-3.5 text-right">Pendapatan QRIS</th>
                    <th class="p-3.5 text-right">Pendapatan Tunai</th>
                    <th class="p-3.5 text-center">Tren Bulanan</th>
                    <th class="p-3.5 pr-4 text-right">Total Penghasilan Bulan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-slate-200">
                <?php foreach ($recapMonthly as $mr): ?>
                <tr class="hover:bg-white/5 transition">
                    <td class="p-3.5 pl-4 font-bold text-white"><?= htmlspecialchars($mr['full_label']) ?></td>
                    <td class="p-3.5 text-center font-mono text-slate-400"><?= $mr['year'] ?></td>
                    <td class="p-3.5 text-center font-black text-amber-300"><?= $mr['orders_count'] ?> Pesanan</td>
                    <td class="p-3.5 text-center text-[11px]">
                        <span class="px-2 py-0.5 rounded bg-slate-900 border border-white/10 text-slate-200"><?= $mr['studio_orders'] ?> Studio</span>
                        <span class="px-2 py-0.5 rounded bg-amber-500/15 border border-amber-500/30 text-amber-300 ml-1"><?= $mr['home_orders'] ?> Home</span>
                    </td>
                    <td class="p-3.5 text-right font-mono text-emerald-300">Rp <?= number_format($mr['qris_revenue'], 0, ',', '.') ?></td>
                    <td class="p-3.5 text-right font-mono text-slate-300">Rp <?= number_format($mr['tunai_revenue'], 0, ',', '.') ?></td>
                    <td class="p-3.5 text-center">
                        <?php $isUp = ($mr['growth_pct'] >= 0); ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $isUp ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' ?>">
                            <?= $isUp ? '+' : '' ?><?= $mr['growth_pct'] ?>%
                        </span>
                    </td>
                    <td class="p-3.5 pr-4 text-right font-black text-emerald-400 font-mono text-sm">
                        Rp <?= number_format($mr['revenue'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const RECAP_CHARTS_DATA = <?= json_encode($recapCharts) ?>;
let currentChartMode = 'bars';
let currentChartPeriod = '7d';

function fmtRpJs(v) {
    v = Number(v) || 0;
    if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1) + 'Jt';
    if (v >= 1000) return 'Rp ' + Math.round(v / 1000) + 'Rb';
    return 'Rp ' + v.toLocaleString('id-ID');
}

function fmtRpFullJs(v) {
    v = Math.round(Number(v) || 0);
    return 'Rp ' + v.toLocaleString('id-ID');
}

function renderDynamicRevenueChart() {
    const svg = document.getElementById('dynamic-revenue-svg');
    if (!svg) return;

    const chartObj = RECAP_CHARTS_DATA[currentChartPeriod] || RECAP_CHARTS_DATA['7d'];
    if (!chartObj) return;

    const series = chartObj.series || [];
    const titleEl = document.getElementById('dynamic-chart-title');
    const subEl = document.getElementById('dynamic-chart-subtitle');
    if (titleEl) titleEl.textContent = '📊 Grafik Rekap Pendapatan: ' + chartObj.title;
    if (subEl) subEl.textContent = chartObj.subtitle;

    // Update KPI boxes
    let totalRev = 0;
    let totalOrders = 0;
    let maxRev = 0;
    let peakLabel = '-';
    series.forEach(item => {
        const r = Number(item.revenue) || 0;
        const o = Number(item.orders_count) || 0;
        totalRev += r;
        totalOrders += o;
        if (r >= maxRev) {
            maxRev = r;
            peakLabel = item.label || '-';
        }
    });
    const avgRev = series.length > 0 ? totalRev / series.length : 0;

    const kpiTotal = document.getElementById('kpi-period-total');
    const kpiPeak = document.getElementById('kpi-period-peak');
    const kpiAvg = document.getElementById('kpi-period-avg');
    const kpiOrders = document.getElementById('kpi-period-orders');

    if (kpiTotal) kpiTotal.textContent = fmtRpFullJs(totalRev);
    if (kpiPeak) kpiPeak.innerHTML = `<span>${peakLabel}</span> <span class="text-xs font-mono text-emerald-400 font-bold">(${fmtRpJs(maxRev)})</span>`;
    if (kpiAvg) kpiAvg.textContent = fmtRpJs(avgRev);
    if (kpiOrders) kpiOrders.innerHTML = `${totalOrders} <span class="text-xs text-slate-300 font-normal">Pesanan</span>`;

    const chartW = 860, chartH = 250, padL = 68, padR = 24, padT = 46, padB = 44;
    const innerW = chartW - padL - padR;
    const innerH = chartH - padT - padB;
    const n = Math.max(series.length, 1);
    const barW = Math.min(48, (innerW / n) * 0.54);
    const safeMax = maxRev > 0 ? maxRev : 1;

    const points = [];
    const bars = [];
    for (let i = 0; i < n; i++) {
        const item = series[i] || { revenue: 0, orders_count: 0, label: '', formatted_date: '' };
        const cx = padL + (i + 0.5) * (innerW / n);
        const rev = Number(item.revenue) || 0;
        const ly = padT + (maxRev > 0 ? (1 - rev / safeMax) * innerH : innerH);
        points.push([cx, ly]);

        const bh = maxRev > 0 ? Math.max(6, (rev / safeMax) * innerH) : 6;
        const by = padT + (innerH - bh);
        const bx = cx - barW / 2;
        bars.push({
            cx, bx, by, bw: barW, bh, rev,
            orders: Number(item.orders_count) || 0,
            label: item.label || '',
            date: item.formatted_date || ''
        });
    }

    const polyline = points.map(p => `${p[0]},${p[1]}`).join(' ');
    const firstPt = points[0];
    const lastPt = points[points.length - 1];
    const areaPath = `M ${firstPt[0]},${padT + innerH} ` + points.map(p => `L ${p[0]},${p[1]}`).join(' ') + ` L ${lastPt[0]},${padT + innerH} Z`;

    let gridHtml = '';
    for (let gi = 0; gi <= 4; gi++) {
        const gy = padT + (gi * innerH) / 4;
        const gVal = fmtRpJs(safeMax * (1 - gi / 4));
        gridHtml += `<line x1="${padL}" y1="${gy}" x2="${chartW - padR}" y2="${gy}" stroke="rgba(255,255,255,0.08)" stroke-dasharray="3,3" stroke-width="1"/>`;
        gridHtml += `<text x="${padL - 8}" y="${gy + 4}" fill="rgba(148,163,184,0.85)" font-size="10" font-family="'Plus Jakarta Sans', sans-serif" font-weight="600" text-anchor="end">${gVal}</text>`;
    }

    let barsHtml = '';
    bars.forEach(b => {
        const isPeak = (maxRev > 0 && b.rev === maxRev);
        const badgeW = n > 10 ? 54 : 72;
        const badgeX = -badgeW / 2;
        const fontSz = n > 10 ? '9' : '10.5';
        barsHtml += `<g class="group" cursor="pointer">
            <rect x="${b.bx}" y="${b.by}" width="${b.bw}" height="${b.bh}" rx="6"
                  fill="url(#${b.rev > 0 ? 'barGradActive' : 'barGradZero'})"
                  opacity="${b.rev > 0 ? '0.92' : '0.4'}"
                  filter="${b.rev > 0 ? 'url(#emeraldGlow)' : 'none'}">
                <title>${b.label} (${b.date}): ${fmtRpFullJs(b.rev)} | ${b.orders} Pesanan</title>
            </rect>
            ${b.rev > 0 ? `<rect x="${b.bx}" y="${b.by}" width="${b.bw}" height="3" rx="1.5" fill="#a7f3d0" opacity="0.95"/>` : ''}
            ${b.rev > 0 ? `
            <g transform="translate(${b.cx}, ${b.by - 12})">
                <rect x="${badgeX}" y="-11" width="${badgeW}" height="19" rx="6" fill="#022c22" stroke="${isPeak ? '#34d399' : '#10b981'}" stroke-width="${isPeak ? '1.6' : '1.1'}"/>
                <text x="0" y="2" fill="#ffffff" font-size="${fontSz}" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" text-anchor="middle">${fmtRpJs(b.rev)}</text>
            </g>` : ''}
            ${(b.bh >= 32 && b.orders > 0 && n <= 12) ? `<text x="${b.cx}" y="${padT + innerH - 9}" fill="#ecfdf5" font-size="9" font-family="'Plus Jakarta Sans', sans-serif" font-weight="bold" text-anchor="middle">${b.orders} Ord</text>` : ''}
        </g>`;
    });

    let linesHtml = `<path d="${areaPath}" fill="url(#revGrad)"/>
        <polyline points="${polyline}" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" filter="url(#emeraldGlow)"/>`;
    bars.forEach((b, idx) => {
        const p = points[idx];
        linesHtml += `<g class="cursor-pointer">
            <circle cx="${p[0]}" cy="${p[1]}" r="5" fill="#0f172a" stroke="#10b981" stroke-width="2.5"/>
            <circle cx="${p[0]}" cy="${p[1]}" r="2.2" fill="#34d399"/>
            ${b.rev > 0 ? `<g transform="translate(${p[0]}, ${p[1] - 13})">
                <rect x="-34" y="-11" width="68" height="19" rx="6" fill="#022c22" stroke="#10b981" stroke-width="1.2"/>
                <text x="0" y="2" fill="#ffffff" font-size="9.5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" text-anchor="middle">${fmtRpJs(b.rev)}</text>
            </g>` : ''}
        </g>`;
    });

    let xLabelsHtml = '';
    bars.forEach(b => {
        const lblFont = n > 10 ? '10' : '11.5';
        xLabelsHtml += `<text x="${b.cx}" y="${chartH - padB + 16}" fill="#f8fafc" font-size="${lblFont}" font-family="'Plus Jakarta Sans', sans-serif" font-weight="800" text-anchor="middle">${b.label}</text>
            <text x="${b.cx}" y="${chartH - padB + 30}" fill="#94a3b8" font-size="9" font-family="'Plus Jakarta Sans', sans-serif" font-weight="600" text-anchor="middle">${b.date}</text>`;
    });

    const showBars = (currentChartMode === 'bars' || currentChartMode === 'combo') ? 'block' : 'none';
    const showLines = (currentChartMode === 'lines' || currentChartMode === 'combo') ? 'block' : 'none';

    svg.innerHTML = `
        <defs>
            <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#10b981" stop-opacity="0.45"/>
                <stop offset="100%" stop-color="#10b981" stop-opacity="0.02"/>
            </linearGradient>
            <linearGradient id="barGradActive" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#34d399"/>
                <stop offset="25%" stop-color="#10b981"/>
                <stop offset="100%" stop-color="#047857"/>
            </linearGradient>
            <linearGradient id="barGradZero" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#475569"/>
                <stop offset="100%" stop-color="#1e293b"/>
            </linearGradient>
            <filter id="emeraldGlow" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="3" stdDeviation="3" flood-color="#10b981" flood-opacity="0.35"/>
            </filter>
        </defs>
        ${gridHtml}
        <line x1="${padL}" y1="${padT + innerH}" x2="${chartW - padR}" y2="${padT + innerH}" stroke="rgba(255,255,255,0.2)" stroke-width="1.5"/>
        <g id="chart-layer-bars" style="display:${showBars};">${barsHtml}</g>
        <g id="chart-layer-lines" style="display:${showLines};">${linesHtml}</g>
        ${xLabelsHtml}
    `;
}

function switchRevenueChartPeriod(periodKey) {
    currentChartPeriod = periodKey;
    document.querySelectorAll('.period-tab-btn').forEach(btn => {
        btn.className = 'period-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-slate-900/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-white/10';
    });
    const activeBtn = document.getElementById('tab-period-' + periodKey);
    if (activeBtn) {
        activeBtn.className = 'period-tab-btn px-3 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/25';
    }
    renderDynamicRevenueChart();
}

function setAnalyticsChartMode(mode) {
    currentChartMode = mode;
    const btnBars = document.getElementById('btn-chart-bars');
    const btnLines = document.getElementById('btn-chart-lines');
    const btnCombo = document.getElementById('btn-chart-combo');

    const defaultClass = 'px-3.5 py-1.5 rounded-xl font-bold transition duration-200 text-slate-300 hover:text-white hover:bg-slate-800 flex items-center space-x-1.5';
    const activeClass = 'px-3.5 py-1.5 rounded-xl font-extrabold transition duration-200 bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/30 flex items-center space-x-1.5';

    [btnBars, btnLines, btnCombo].forEach(btn => { if (btn) btn.className = defaultClass; });
    if (mode === 'bars' && btnBars) btnBars.className = activeClass;
    if (mode === 'lines' && btnLines) btnLines.className = activeClass;
    if (mode === 'combo' && btnCombo) btnCombo.className = activeClass;

    renderDynamicRevenueChart();
}

function switchRecapTableTab(tab) {
    const tblPeriode = document.getElementById('table-rekap-periode');
    const tblBulanan = document.getElementById('table-rekap-bulanan');
    const btnPeriode = document.getElementById('btn-tab-rekap-periode');
    const btnBulanan = document.getElementById('btn-tab-rekap-bulanan');

    if (tab === 'periode') {
        if (tblPeriode) tblPeriode.classList.remove('hidden');
        if (tblBulanan) tblBulanan.classList.add('hidden');
        if (btnPeriode) btnPeriode.className = 'px-3.5 py-1.5 rounded-xl font-extrabold bg-emerald-500 text-slate-950 transition cursor-pointer';
        if (btnBulanan) btnBulanan.className = 'px-3.5 py-1.5 rounded-xl font-bold text-slate-300 hover:text-white transition cursor-pointer';
    } else {
        if (tblPeriode) tblPeriode.classList.add('hidden');
        if (tblBulanan) tblBulanan.classList.remove('hidden');
        if (btnBulanan) btnBulanan.className = 'px-3.5 py-1.5 rounded-xl font-extrabold bg-emerald-500 text-slate-950 transition cursor-pointer';
        if (btnPeriode) btnPeriode.className = 'px-3.5 py-1.5 rounded-xl font-bold text-slate-300 hover:text-white transition cursor-pointer';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    renderDynamicRevenueChart();
});
</script>

<!-- ═══════════════════════════ 2-COL: BUSY HOURS + TOP SERVICES ═══════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Busy Hours Heatmap -->
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-3xl p-6">
        <h2 class="text-base font-black text-white mb-1">⏰ Jam Tersibuk</h2>
        <p class="text-xs text-slate-400 mb-5">Distribusi pesanan berdasarkan jam (00–23)</p>

        <div class="grid gap-y-1.5">
            <?php foreach ([8,9,10,11,12,13,14,15,16,17,18,19,20] as $hr): ?>
            <?php
            $cnt = $busyHours[$hr] ?? 0;
            $pct = $maxHourCount > 0 ? ($cnt / $maxHourCount * 100) : 0;
            $clr = $pct >= 80 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : ($pct >= 20 ? '#6366f1' : '#1e293b'));
            ?>
            <div class="flex items-center space-x-2.5 group">
                <span class="text-xs text-slate-400 font-mono w-8 text-right"><?= sprintf('%02d', $hr) ?></span>
                <div class="flex-1 bg-slate-900/60 rounded-full h-5 relative overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700" style="width:<?= max($pct, 2) ?>%; background:<?= $clr ?>; opacity:0.85;"></div>
                </div>
                <span class="text-xs font-bold text-slate-300 w-5 text-right"><?= $cnt ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Peak day distribution -->
        <div class="mt-5 pt-4 border-t border-white/10">
            <p class="text-xs text-slate-400 mb-3 font-semibold">Hari dalam Seminggu:</p>
            <div class="flex items-end space-x-1 h-14">
                <?php foreach ($peakDays as $day): ?>
                <?php $h = $maxDayCount > 0 ? max(($day['count'] / $maxDayCount * 100), 4) : 4; ?>
                <div class="flex-1 flex flex-col items-center space-y-1 group cursor-default">
                    <span class="text-[9px] text-emerald-400 font-bold opacity-0 group-hover:opacity-100 transition"><?= $day['count'] ?></span>
                    <div class="w-full rounded-t-sm bg-gradient-to-t from-emerald-600/60 to-teal-500/40 transition-all duration-500" style="height:<?= $h ?>%"></div>
                    <span class="text-[9px] text-slate-400 font-medium"><?= mb_substr($day['day'], 0, 3) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Top Services Leaderboard -->
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-3xl p-6">
        <h2 class="text-base font-black text-white mb-1">🏆 Layanan Terpopuler</h2>
        <p class="text-xs text-slate-400 mb-5">Berdasarkan jumlah pesanan selesai</p>

        <?php if (empty($topServices)): ?>
        <div class="text-center py-8 text-slate-500">
            <i class="fas fa-chart-bar text-3xl mb-2 opacity-30"></i>
            <p class="text-sm">Belum ada data layanan</p>
        </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php $medals = ['🥇','🥈','🥉','4️⃣','5️⃣']; ?>
            <?php foreach ($topServices as $i => $svc): ?>
            <div class="group">
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center space-x-2 min-w-0">
                        <span class="text-base"><?= $medals[$i] ?? '·' ?></span>
                        <span class="text-sm text-white font-semibold truncate"><?= htmlspecialchars($svc['name'] ?? '-') ?></span>
                    </div>
                    <div class="flex items-center space-x-2 flex-shrink-0 ml-2">
                        <span class="text-xs text-emerald-400 font-bold"><?= $svc['orders'] ?> order</span>
                        <span class="text-xs text-slate-500"><?= fmtRp($svc['revenue']) ?></span>
                    </div>
                </div>
                <div class="bg-slate-900/60 rounded-full h-2 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700"
                         style="width:<?= $svc['pct'] ?? 0 ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════ RFM SEGMENTATION ═══════════════════════════ -->
<div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-3xl p-6 mb-6">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-black text-white">🧠 Segmentasi Pelanggan (RFM)</h2>
            <p class="text-xs text-slate-400 mt-0.5">Recency · Frequency · Monetary — skor kecerdasan pelanggan</p>
        </div>
        <!-- Legenda -->
        <div class="hidden md:flex items-center space-x-3 text-xs">
            <?php foreach ([
                ['Champion','emerald'],['Loyal','teal'],['At Risk','amber'],['Churn','rose'],['Prospek','blue']
            ] as [$seg, $clr]): ?>
            <span class="flex items-center space-x-1">
                <span class="w-2 h-2 rounded-full bg-<?= $clr ?>-400"></span>
                <span class="text-slate-400"><?= $seg ?></span>
            </span>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($rfmSegments)): ?>
    <div class="text-center py-10 text-slate-500">
        <i class="fas fa-users text-4xl mb-3 opacity-20"></i>
        <p class="text-sm">Belum ada data pelanggan yang cukup untuk segmentasi.</p>
        <p class="text-xs mt-1">Minimal 1 transaksi selesai diperlukan.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-slate-400 uppercase tracking-wider border-b border-white/10">
                    <th class="pb-3 text-left">Pelanggan</th>
                    <th class="pb-3 text-center">Segment</th>
                    <th class="pb-3 text-center">Recency</th>
                    <th class="pb-3 text-center">Frekuensi</th>
                    <th class="pb-3 text-right">Total Belanja</th>
                    <th class="pb-3 text-center">Skor RFM</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php foreach ($rfmSegments as $c): ?>
                <?php $clr = $c['color'] ?? 'slate'; ?>
                <tr class="hover:bg-white/5 transition">
                    <td class="py-3 pr-4">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-7 h-7 rounded-lg bg-<?= $clr ?>-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas <?= $c['icon'] ?> text-<?= $clr ?>-400 text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-white text-xs"><?= htmlspecialchars($c['fullname']) ?></p>
                                <p class="text-[10px] text-slate-500"><?= htmlspecialchars($c['phone'] ?? '-') ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 text-center">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-<?= $clr ?>-500/20 text-<?= $clr ?>-300 border border-<?= $clr ?>-500/30">
                            <?= $c['segment'] ?>
                        </span>
                    </td>
                    <td class="py-3 text-center text-xs text-slate-300"><?= $c['recency_days'] ?> hari lalu</td>
                    <td class="py-3 text-center text-xs text-slate-300"><?= $c['frequency'] ?>x order</td>
                    <td class="py-3 text-right text-xs font-bold text-emerald-400"><?= fmtRp($c['monetary']) ?></td>
                    <td class="py-3 text-center">
                        <div class="flex justify-center space-x-1">
                            <?php foreach (['R'=>$c['r_score'],'F'=>$c['f_score'],'M'=>$c['m_score']] as $dim => $sc): ?>
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-slate-700 text-slate-200"><?= $dim ?><?= $sc ?></span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════════════════════════ CHURN RISK LIST ═══════════════════════════ -->
<div id="churn" class="bg-slate-800/60 backdrop-blur-md border border-rose-500/20 rounded-3xl p-6">
    <div class="flex items-center space-x-3 mb-5">
        <div class="w-9 h-9 rounded-xl bg-rose-500/20 flex items-center justify-center">
            <i class="fas fa-user-clock text-rose-400"></i>
        </div>
        <div>
            <h2 class="text-base font-black text-white">⚠️ Pelanggan Berisiko Churn</h2>
            <p class="text-xs text-slate-400 mt-0.5">Tidak memesan dalam 14+ hari — hubungi segera!</p>
        </div>
    </div>

    <?php if (empty($churnRisk)): ?>
    <div class="text-center py-8 text-emerald-400">
        <i class="fas fa-check-circle text-3xl mb-2"></i>
        <p class="text-sm font-semibold">Tidak ada pelanggan churn! Semua aktif 🎉</p>
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($churnRisk as $c): ?>
        <div class="flex items-center justify-between p-3 bg-rose-500/10 border border-rose-500/20 rounded-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500/20 flex items-center justify-center font-black text-rose-400 text-sm">
                    <?= strtoupper(substr($c['fullname'], 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-white"><?= htmlspecialchars($c['fullname']) ?></p>
                    <p class="text-xs text-slate-400"><?= $c['days_since_last'] ?> hari tidak pesan · <?= $c['total_orders'] ?>x total · <?= fmtRp($c['total_spend'] ?? 0) ?></p>
                </div>
            </div>
            <?php if (!empty($c['phone'])): ?>
            <?php $wa = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $c['phone'])); ?>
            <a href="https://wa.me/<?= $wa ?>?text=Hai+<?= urlencode($c['fullname']) ?>%2C+kami+kangen+nih!+Ada+promo+spesial+untuk+Anda+di+Wijaya+Barbershop+%F0%9F%92%88"
               target="_blank"
               class="flex-shrink-0 px-3 py-1.5 rounded-xl bg-green-600/30 hover:bg-green-600 text-green-300 hover:text-white text-xs font-bold transition flex items-center space-x-1.5 border border-green-600/30">
                <i class="fab fa-whatsapp text-sm"></i>
                <span>Chat WA</span>
            </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════════════════════════ RENCANA AKSI STRATEGIS AI ═══════════════════════════ -->
<div class="mt-8 bg-slate-800/60 backdrop-blur-md border border-purple-500/20 rounded-3xl p-6 sm:p-7">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-500 flex items-center justify-center text-white shadow-md">
                <i class="fas fa-lightbulb text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black text-white">💡 Rencana Aksi Bisnis Berbasis AI</h2>
                <p class="text-xs text-slate-400">Rekomendasi taktis otomatis yang dihitung secara real-time dari data penjualan aktual</p>
            </div>
        </div>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 flex items-center space-x-1.5 self-start sm:self-auto">
            <i class="fas fa-bolt text-amber-400"></i>
            <span>Actionable Insights</span>
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($actionPlans as $plan): ?>
        <?php
        $clr = $plan['color'];
        $clrBorder = [
            'amber'   => 'border-amber-500/30 hover:border-amber-500/60 bg-amber-500/5',
            'rose'    => 'border-rose-500/30 hover:border-rose-500/60 bg-rose-500/5',
            'emerald' => 'border-emerald-500/30 hover:border-emerald-500/60 bg-emerald-500/5',
            'indigo'  => 'border-indigo-500/30 hover:border-indigo-500/60 bg-indigo-500/5',
        ][$clr] ?? 'border-white/10 bg-white/5';
        $clrBadge = [
            'amber'   => 'bg-amber-500/20 text-amber-300',
            'rose'    => 'bg-rose-500/20 text-rose-300',
            'emerald' => 'bg-emerald-500/20 text-emerald-300',
            'indigo'  => 'bg-indigo-500/20 text-indigo-300',
        ][$clr] ?? 'bg-white/10 text-white';
        $clrBtn = [
            'amber'   => 'bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 border-amber-500/30',
            'rose'    => 'bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white border-rose-500/30',
            'emerald' => 'bg-emerald-500/20 hover:bg-emerald-500 text-emerald-300 hover:text-white border-emerald-500/30',
            'indigo'  => 'bg-indigo-500/20 hover:bg-indigo-500 text-indigo-300 hover:text-white border-indigo-500/30',
        ][$clr] ?? 'bg-white/10 hover:bg-white/20 text-white border-white/20';
        ?>
        <div class="border rounded-2xl p-5 flex flex-col justify-between transition group <?= $clrBorder ?>">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg <?= $clrBadge ?>">
                        <?= htmlspecialchars($plan['category']) ?>
                    </span>
                    <i class="fas <?= $plan['icon'] ?> text-sm text-slate-400 group-hover:scale-110 transition"></i>
                </div>
                <h3 class="text-sm font-black text-white mb-2 leading-snug"><?= htmlspecialchars($plan['title']) ?></h3>
                <p class="text-xs text-slate-300 leading-relaxed mb-4"><?= htmlspecialchars($plan['rationale']) ?></p>
            </div>
            <div class="pt-3 border-t border-white/10 flex justify-end">
                <a href="<?= $plan['action_url'] ?>" class="text-xs font-bold px-3.5 py-2 rounded-xl transition border flex items-center space-x-1.5 <?= $clrBtn ?>">
                    <span><?= htmlspecialchars($plan['action_label']) ?></span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ═══════════════════════════ ROADMAP RENCANA KEDEPANNYA (AI 4.0) ═══════════════════════════ -->
<div class="mt-8 bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-3xl p-6 sm:p-7">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-8">
        <div>
            <div class="flex items-center space-x-2.5 mb-1">
                <span class="px-2.5 py-0.5 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-[10px] font-black uppercase tracking-widest">Roadmap Strategis</span>
                <h2 class="text-lg font-black text-white">🚀 Rencana Pengembangan Sistem Cerdas Kedepannya</h2>
            </div>
            <p class="text-xs text-slate-400">Visi digitalisasi dan roadmap evolusi kecerdasan buatan Wijaya Barbershop</p>
        </div>
        <div class="text-right hidden sm:block">
            <span class="text-xs text-purple-300 font-bold block">Grand Design AI Barbershop 4.0</span>
            <span class="text-[11px] text-slate-400">Integrasi Web, Computer Vision & IoT</span>
        </div>
    </div>

    <!-- Timeline Phases -->
    <div class="space-y-6 relative before:absolute before:inset-0 before:left-5 before:w-0.5 before:bg-gradient-to-b before:from-emerald-500 before:via-purple-500 before:to-slate-800">
        <?php foreach ($aiRoadmap as $rm): ?>
        <?php
        $badgeClass = [
            'emerald' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
            'purple'  => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
            'blue'    => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
            'amber'   => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
            'rose'    => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
        ][$rm['badge_color']] ?? 'bg-white/10 text-white border-white/20';

        $dotClass = [
            'emerald' => 'bg-emerald-400 ring-emerald-500/30',
            'purple'  => 'bg-purple-400 ring-purple-500/30',
            'blue'    => 'bg-blue-400 ring-blue-500/30',
            'amber'   => 'bg-amber-400 ring-amber-500/30',
            'rose'    => 'bg-rose-400 ring-rose-500/30',
        ][$rm['badge_color']] ?? 'bg-white ring-white/20';
        ?>
        <div class="relative pl-12 group">
            <!-- Timeline Bullet Point -->
            <div class="absolute left-3.5 top-1.5 w-3.5 h-3.5 rounded-full <?= $dotClass ?> ring-4 shadow-md transition-transform duration-300 group-hover:scale-125"></div>

            <div class="bg-slate-900/70 border border-white/10 rounded-2xl p-5 hover:border-purple-500/30 transition">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <div class="flex items-center space-x-2.5">
                        <span class="text-xs font-black text-white"><?= htmlspecialchars($rm['phase']) ?></span>
                        <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border <?= $badgeClass ?>">
                            <?= htmlspecialchars($rm['status']) ?>
                        </span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs font-mono text-slate-400">
                        <span>Progress:</span>
                        <span class="font-bold text-white"><?= $rm['progress'] ?>%</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-slate-800 rounded-full h-1.5 mb-3 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-purple-500 to-indigo-500" style="width: <?= $rm['progress'] ?>%"></div>
                </div>

                <h3 class="text-sm font-black text-white mb-1.5"><?= htmlspecialchars($rm['title']) ?></h3>
                <p class="text-xs text-slate-300 leading-relaxed mb-3.5"><?= htmlspecialchars($rm['description']) ?></p>

                <!-- Tech Tags -->
                <div class="flex flex-wrap gap-1.5">
                    <?php foreach ($rm['tech'] as $t): ?>
                    <span class="text-[10px] font-semibold bg-white/5 hover:bg-white/10 text-slate-300 px-2.5 py-1 rounded-lg border border-white/5">
                        <?= htmlspecialchars($t) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ═══════════════════════════ KALKULATOR SIMULASI PROYEKSI PERTUMBUHAN AI ═══════════════════════════ -->
<div class="mt-8 bg-gradient-to-br from-slate-900 via-purple-950/20 to-slate-900 border border-purple-500/30 rounded-3xl p-6 sm:p-7">
    <div class="flex items-center space-x-3 mb-6">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-md">
            <i class="fas fa-calculator text-lg"></i>
        </div>
        <div>
            <h2 class="text-lg font-black text-white">📊 Simulator Proyeksi Pertumbuhan AI</h2>
            <p class="text-xs text-slate-400">Simulasikan dampak implementasi fitur cerdas terhadap potensi omset bulanan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <!-- Controls -->
        <div class="space-y-5">
            <div>
                <div class="flex justify-between text-xs font-bold text-slate-300 mb-2">
                    <span>Target Kenaikan Retensi Pelanggan (Anti-Churn):</span>
                    <span id="label-retention" class="text-emerald-400 font-mono">+15%</span>
                </div>
                <input type="range" id="slider-retention" min="0" max="40" value="15" step="5"
                    oninput="updateAISimulator()"
                    class="w-full accent-emerald-500 bg-slate-800 rounded-lg cursor-pointer">
                <p class="text-[11px] text-slate-500 mt-1">Dicapai melalui sistem notifikasi WhatsApp otomatis & program loyalitas tier.</p>
            </div>

            <div>
                <div class="flex justify-between text-xs font-bold text-slate-300 mb-2">
                    <span>Peningkatan Okupansi Jam Sepi (Flash Sale Happy Hour):</span>
                    <span id="label-occupancy" class="text-purple-400 font-mono">+25%</span>
                </div>
                <input type="range" id="slider-occupancy" min="0" max="60" value="25" step="5"
                    oninput="updateAISimulator()"
                    class="w-full accent-purple-500 bg-slate-800 rounded-lg cursor-pointer">
                <p class="text-[11px] text-slate-500 mt-1">Dicapai lewat voucher promo dinamis di jam-jam sepi terdeteksi AI.</p>
            </div>

            <div>
                <div class="flex justify-between text-xs font-bold text-slate-300 mb-2">
                    <span>Konversi AI Face Shape Matcher (Cross-Selling Produk):</span>
                    <span id="label-crosssell" class="text-amber-400 font-mono">+20%</span>
                </div>
                <input type="range" id="slider-crosssell" min="0" max="50" value="20" step="5"
                    oninput="updateAISimulator()"
                    class="w-full accent-amber-500 bg-slate-800 rounded-lg cursor-pointer">
                <p class="text-[11px] text-slate-500 mt-1">Penjualan pomade & hair treatment dari rekomendasi model rambut AI.</p>
            </div>
        </div>

        <!-- Result Card -->
        <div class="bg-slate-900/90 border border-purple-500/40 rounded-2xl p-6 text-center relative overflow-hidden shadow-2xl">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <p class="text-xs uppercase tracking-widest text-purple-300 font-bold mb-2">Estimasi Potensi Peningkatan Omset</p>
            <div class="flex items-center justify-center space-x-2 my-3">
                <span id="sim-pct-growth" class="text-4xl sm:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-purple-400">+32.5%</span>
            </div>
            <p class="text-xs text-slate-400 mb-4">Proyeksi pertumbuhan bulanan bisnis</p>

            <div class="pt-4 border-t border-white/10 grid grid-cols-2 gap-3 text-left">
                <div class="p-3 bg-white/5 rounded-xl">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Tambahan Transaksi</span>
                    <span id="sim-add-orders" class="text-sm font-black text-white">+18 - 25 Pesanan / bln</span>
                </div>
                <div class="p-3 bg-white/5 rounded-xl">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Indeks Efisiensi Capster</span>
                    <span id="sim-efficiency" class="text-sm font-black text-emerald-400">92% Optimal</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateAISimulator() {
    const ret = parseInt(document.getElementById('slider-retention').value) || 0;
    const occ = parseInt(document.getElementById('slider-occupancy').value) || 0;
    const crs = parseInt(document.getElementById('slider-crosssell').value) || 0;

    document.getElementById('label-retention').textContent = '+' + ret + '%';
    document.getElementById('label-occupancy').textContent = '+' + occ + '%';
    document.getElementById('label-crosssell').textContent = '+' + crs + '%';

    // Model kombinasi pertumbuhan terbobot
    const totalGrowth = ((ret * 0.45) + (occ * 0.35) + (crs * 0.20)).toFixed(1);
    document.getElementById('sim-pct-growth').textContent = '+' + totalGrowth + '%';

    const addOrdersMin = Math.round(totalGrowth * 0.6);
    const addOrdersMax = Math.round(totalGrowth * 0.9) + 4;
    document.getElementById('sim-add-orders').textContent = '+' + addOrdersMin + ' - ' + addOrdersMax + ' Pesanan / bln';

    const efficiency = Math.min(98, Math.round(75 + (occ * 0.35)));
    document.getElementById('sim-efficiency').textContent = efficiency + '% Optimal';
}
</script>

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

<!-- ═══════════════════════════ REVENUE CHART (DIAGRAM BATANG & GARIS TREN) ═══════════════════════════ -->
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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-white/10 gap-3">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-6 rounded-full bg-emerald-500"></span>
                <h2 class="text-base sm:text-lg font-black text-white flex items-center space-x-2">
                    <span>📊 Tren & Diagram Batang Pendapatan 7 Hari</span>
                </h2>
            </div>
            <p class="text-xs text-slate-400 mt-1">Visualisasi omset harian dari pesanan selesai & lunas (termasuk label nominal jelas tanpa terpotong)</p>
        </div>

        <!-- Mode Tampilan Switcher (Diagram Batang, Garis Tren, Kombinasi) -->
        <div class="inline-flex p-1 bg-slate-900/80 rounded-2xl border border-white/10 text-xs self-start sm:self-auto shadow-inner">
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

    <!-- Mini KPI Ringkasan 7 Hari -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="p-3 rounded-2xl bg-slate-900/60 border border-white/5">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Total Omset 7 Hari</span>
            <span class="text-base sm:text-lg font-black text-emerald-400 font-mono">Rp <?= number_format($totalWeekRev, 0, ',', '.') ?></span>
        </div>
        <div class="p-3 rounded-2xl bg-slate-900/60 border border-white/5">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Hari Tertinggi</span>
            <span class="text-sm sm:text-base font-extrabold text-white flex items-center space-x-1">
                <span><?= htmlspecialchars($peakDayItem['label'] ?? '-') ?></span>
                <span class="text-xs font-mono text-emerald-400 font-bold">(<?= fmtRp($maxRevenue) ?>)</span>
            </span>
        </div>
        <div class="p-3 rounded-2xl bg-slate-900/60 border border-white/5">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Rata-Rata Harian</span>
            <span class="text-sm sm:text-base font-extrabold text-white font-mono"><?= fmtRp($avgDailyRev) ?> <span class="text-[11px] text-slate-400 font-normal">/hari</span></span>
        </div>
        <div class="p-3 rounded-2xl bg-slate-900/60 border border-white/5">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Total Transaksi Selesai</span>
            <span class="text-base sm:text-lg font-black text-amber-400 font-mono"><?= $totalWeekOrders ?> <span class="text-xs text-slate-300 font-normal">Pesanan</span></span>
        </div>
    </div>

    <!-- SVG Responsive Chart Container -->
    <div class="relative w-full h-64 sm:h-72">
        <?php
        $chartW = 860; $chartH = 250; $padL = 65; $padR = 25; $padT = 46; $padB = 42;
        $innerW = $chartW - $padL - $padR;
        $innerH = $chartH - $padT - $padB;
        $n = max(count($revenueChart), 1);
        $barW = ($innerW / $n) * 0.52;

        $points = [];
        $bars = [];
        for ($i = 0; $i < $n; $i++) {
            $cx = $padL + ($i + 0.5) * ($innerW / $n);
            $rev = (float)($revenueChart[$i]['revenue'] ?? 0);
            $ly = $padT + ($maxRevenue > 0 ? (1 - $rev / $maxRevenue) * $innerH : $innerH);
            $points[] = [$cx, $ly];

            $bh = $maxRevenue > 0 ? max(6, ($rev / $maxRevenue) * $innerH) : 6;
            $by = $padT + ($innerH - $bh);
            $bx = $cx - $barW / 2;
            $bars[] = [
                'cx' => $cx,
                'bx' => $bx,
                'by' => $by,
                'bw' => $barW,
                'bh' => $bh,
                'rev' => $rev,
                'orders' => $revenueChart[$i]['orders_count'] ?? 0,
                'label' => $revenueChart[$i]['label'] ?? '',
                'date' => $revenueChart[$i]['formatted_date'] ?? date('d/m', strtotime($revenueChart[$i]['date'] ?? 'now'))
            ];
        }
        $polyline = implode(' ', array_map(fn($p) => "{$p[0]},{$p[1]}", $points));
        $firstPt = $points[0];
        $lastPt = $points[count($points) - 1];
        $areaPath = "M {$firstPt[0]}," . ($padT + $innerH) . " " . implode(' ', array_map(fn($p) => "L {$p[0]},{$p[1]}", $points)) . " L {$lastPt[0]}," . ($padT + $innerH) . " Z";
        ?>
        <svg viewBox="0 0 <?= $chartW ?> <?= $chartH ?>" class="w-full h-full overflow-visible" preserveAspectRatio="none">
            <defs>
                <!-- Area Gradient untuk Line Chart -->
                <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.45"/>
                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.02"/>
                </linearGradient>

                <!-- Gradient untuk Diagram Batang Aktif -->
                <linearGradient id="barGradActive" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#34d399"/>
                    <stop offset="25%" stop-color="#10b981"/>
                    <stop offset="100%" stop-color="#047857"/>
                </linearGradient>

                <!-- Gradient untuk Diagram Batang Nol / Kosong -->
                <linearGradient id="barGradZero" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#475569"/>
                    <stop offset="100%" stop-color="#1e293b"/>
                </linearGradient>

                <!-- Filter Glow untuk Efek Visual Premium -->
                <filter id="emeraldGlow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="0" dy="3" stdDeviation="3" flood-color="#10b981" flood-opacity="0.35"/>
                </filter>
            </defs>

            <!-- Grid Lines Horisontal & Label Sumbu Y -->
            <?php for ($gi = 0; $gi <= 4; $gi++): ?>
            <?php $gy = $padT + $gi * $innerH / 4; ?>
            <line x1="<?= $padL ?>" y1="<?= $gy ?>" x2="<?= $chartW - $padR ?>" y2="<?= $gy ?>" stroke="rgba(255,255,255,0.08)" stroke-dasharray="3,3" stroke-width="1"/>
            <text x="<?= $padL - 8 ?>" y="<?= $gy + 4 ?>" fill="rgba(148,163,184,0.85)" font-size="10" font-family="'Plus Jakarta Sans', sans-serif" font-weight="600" text-anchor="end">
                <?= fmtRp($maxRevenue * (1 - $gi / 4)) ?>
            </text>
            <?php endfor; ?>

            <!-- Baseline Bawah -->
            <line x1="<?= $padL ?>" y1="<?= $padT + $innerH ?>" x2="<?= $chartW - $padR ?>" y2="<?= $padT + $innerH ?>" stroke="rgba(255,255,255,0.2)" stroke-width="1.5"/>

            <!-- ═══════ LAYER 1: DIAGRAM BATANG (BAR CHART) ═══════ -->
            <g id="chart-layer-bars">
                <?php foreach ($bars as $b): ?>
                <?php $isPeak = ($maxRevenue > 0 && $b['rev'] == $maxRevenue); ?>
                <g class="group" cursor="pointer">
                    <!-- Batang Diagram -->
                    <rect x="<?= $b['bx'] ?>" y="<?= $b['by'] ?>" width="<?= $b['bw'] ?>" height="<?= $b['bh'] ?>" rx="7" 
                          fill="url(#<?= $b['rev'] > 0 ? 'barGradActive' : 'barGradZero' ?>)" 
                          opacity="<?= $b['rev'] > 0 ? '0.92' : '0.4' ?>"
                          filter="<?= $b['rev'] > 0 ? 'url(#emeraldGlow)' : 'none' ?>"
                          class="transition-all duration-300 hover:opacity-100 hover:brightness-110">
                        <title><?= htmlspecialchars($b['label']) ?> (<?= $b['date'] ?>): Rp <?= number_format($b['rev'], 0, ',', '.') ?> | <?= $b['orders'] ?> Pesanan</title>
                    </rect>

                    <!-- Glowing Top Cap untuk Batang Aktif -->
                    <?php if ($b['rev'] > 0): ?>
                    <rect x="<?= $b['bx'] ?>" y="<?= $b['by'] ?>" width="<?= $b['bw'] ?>" height="3.5" rx="1.75" fill="#a7f3d0" opacity="0.95"/>
                    <?php endif; ?>

                    <!-- Label Nominal di Atas Batang (Sangat Jelas & Bebas Terpotong) -->
                    <?php if ($b['rev'] > 0): ?>
                    <g transform="translate(<?= $b['cx'] ?>, <?= $b['by'] - 13 ?>)">
                        <!-- Background Pill Badge Kontras Tinggi -->
                        <rect x="-37" y="-12" width="74" height="21" rx="7" 
                              fill="#022c22" stroke="<?= $isPeak ? '#34d399' : '#10b981' ?>" stroke-width="<?= $isPeak ? '1.8' : '1.2' ?>" 
                              filter="drop-shadow(0 2px 4px rgba(0,0,0,0.5))"/>
                        <text x="0" y="2.5" fill="#ffffff" font-size="10.5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" text-anchor="middle">
                            <?= fmtRp($b['rev']) ?>
                        </text>
                    </g>
                    <?php else: ?>
                    <text x="<?= $b['cx'] ?>" y="<?= $padT + $innerH - 8 ?>" fill="#64748b" font-size="9" font-family="'Plus Jakarta Sans', sans-serif" font-weight="bold" text-anchor="middle">
                        Rp 0
                    </text>
                    <?php endif; ?>

                    <!-- Jumlah Pesanan di Dalam Batang jika Cukup Tinggi -->
                    <?php if ($b['bh'] >= 32 && $b['orders'] > 0): ?>
                    <text x="<?= $b['cx'] ?>" y="<?= $padT + $innerH - 10 ?>" fill="#ecfdf5" font-size="9.5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="bold" text-anchor="middle" opacity="0.9">
                        <?= $b['orders'] ?> Order
                    </text>
                    <?php endif; ?>
                </g>
                <?php endforeach; ?>
            </g>

            <!-- ═══════ LAYER 2: GARIS TREN & AREA (LINE CHART) ═══════ -->
            <g id="chart-layer-lines" style="display: none;">
                <!-- Area Fill di Bawah Garis -->
                <path d="<?= $areaPath ?>" fill="url(#revGrad)"/>

                <!-- Garis Utama Tren -->
                <polyline points="<?= $polyline ?>" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" filter="url(#emeraldGlow)"/>

                <!-- Node Lingkaran & Label Nominal -->
                <?php foreach ($bars as $i => $b): ?>
                <?php $p = $points[$i]; ?>
                <g class="cursor-pointer">
                    <!-- Lingkaran Luar -->
                    <circle cx="<?= $p[0] ?>" cy="<?= $p[1] ?>" r="5.5" fill="#0f172a" stroke="#10b981" stroke-width="2.5"/>
                    <circle cx="<?= $p[0] ?>" cy="<?= $p[1] ?>" r="2.5" fill="#34d399"/>

                    <!-- Label Nominal di Atas Titik Garis (Jelas & Tidak Terpotong) -->
                    <?php if ($b['rev'] > 0): ?>
                    <g transform="translate(<?= $p[0] ?>, <?= $p[1] - 14 ?>)">
                        <rect x="-37" y="-12" width="74" height="21" rx="7" fill="#022c22" stroke="#10b981" stroke-width="1.3" filter="drop-shadow(0 2px 4px rgba(0,0,0,0.5))"/>
                        <text x="0" y="2.5" fill="#ffffff" font-size="10.5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" text-anchor="middle">
                            <?= fmtRp($b['rev']) ?>
                        </text>
                    </g>
                    <?php endif; ?>
                </g>
                <?php endforeach; ?>
            </g>

            <!-- ═══════ LABEL SUMBU X (NAMA HARI & TANGGAL) ═══════ -->
            <?php foreach ($bars as $b): ?>
            <text x="<?= $b['cx'] ?>" y="<?= $chartH - $padB + 16 ?>" fill="#f8fafc" font-size="12" font-family="'Plus Jakarta Sans', sans-serif" font-weight="800" text-anchor="middle">
                <?= htmlspecialchars($b['label']) ?>
            </text>
            <text x="<?= $b['cx'] ?>" y="<?= $chartH - $padB + 30 ?>" fill="#94a3b8" font-size="9.5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="600" text-anchor="middle">
                <?= htmlspecialchars($b['date']) ?>
            </text>
            <?php endforeach; ?>
        </svg>
    </div>
</div>

<script>
function setAnalyticsChartMode(mode) {
    const barLayer = document.getElementById('chart-layer-bars');
    const lineLayer = document.getElementById('chart-layer-lines');
    const btnBars = document.getElementById('btn-chart-bars');
    const btnLines = document.getElementById('btn-chart-lines');
    const btnCombo = document.getElementById('btn-chart-combo');

    const defaultClass = 'px-3.5 py-1.5 rounded-xl font-bold transition duration-200 text-slate-300 hover:text-white hover:bg-slate-800 flex items-center space-x-1.5';
    const activeClass = 'px-3.5 py-1.5 rounded-xl font-extrabold transition duration-200 bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/30 flex items-center space-x-1.5';

    [btnBars, btnLines, btnCombo].forEach(btn => {
        if (btn) btn.className = defaultClass;
    });

    if (mode === 'bars') {
        if (barLayer) barLayer.style.display = 'block';
        if (lineLayer) lineLayer.style.display = 'none';
        if (btnBars) btnBars.className = activeClass;
    } else if (mode === 'lines') {
        if (barLayer) barLayer.style.display = 'none';
        if (lineLayer) lineLayer.style.display = 'block';
        if (btnLines) btnLines.className = activeClass;
    } else if (mode === 'combo') {
        if (barLayer) barLayer.style.display = 'block';
        if (lineLayer) lineLayer.style.display = 'block';
        if (btnCombo) btnCombo.className = activeClass;
    }
}
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

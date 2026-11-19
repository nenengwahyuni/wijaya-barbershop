<?php
/**
 * resources/views/admin/analytics.php
 * Halaman Analitik Cerdas — AI Insights Dashboard
 */

// Helper format rupiah
function fmtRp(float $v): string {
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

<!-- ═══════════════════════════ REKAP PENGHASILAN SIMPEL (HARI INI & 7 HARI + ATUR BULAN + SIAPA SAJA YANG ORDER) ═══════════════════════════ -->
<?php
$recap = $revenue_recap ?? [];
$recapPeriods = $recap['periods'] ?? [];
$recapCharts  = $recap['charts'] ?? [];
$recapOrders  = $recap['orders_list'] ?? [];

$pToday = $recapPeriods['today'] ?? ['revenue' => 0, 'orders_count' => 0, 'sublabel' => date('d M Y')];
$p7d    = $recapPeriods['last_7_days'] ?? ['revenue' => 0, 'orders_count' => 0, 'sublabel' => ''];

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
<div class="bg-slate-800/75 backdrop-blur-md border border-white/10 rounded-3xl p-5 sm:p-6 mb-6 shadow-xl relative overflow-hidden">
    <!-- Header Utama: Hanya Hari Ini, 7 Hari, & Tombol Atur Bulan -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-4 mb-5 border-b border-white/10 gap-3">
        <div>
            <div class="flex items-center space-x-2.5">
                <span class="w-2.5 h-6 rounded-full bg-emerald-500"></span>
                <h2 id="dynamic-chart-title" class="text-base sm:text-lg font-black text-white">
                    📊 Rekap Penghasilan & Pelanggan Order (7 Hari Terakhir)
                </h2>
            </div>
            <p id="dynamic-chart-subtitle" class="text-xs text-slate-400 mt-1">
                Tampilan simpel: Lihat rekap <strong>Hari Ini</strong> atau <strong>7 Hari Terakhir</strong>, atau klik <strong>Atur Bulan</strong> untuk cek bulan lalu.
            </p>
        </div>

        <!-- 3 Tombol Utama Simpel -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" id="an-btn-today" onclick="selectAnalyticsPeriod('today')"
                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer bg-slate-900 text-slate-200 hover:bg-slate-700 border border-white/10">
                Hari Ini (Rp <?= number_format($pToday['revenue'], 0, ',', '.') ?>)
            </button>
            <button type="button" id="an-btn-7d" onclick="selectAnalyticsPeriod('7d')"
                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/25">
                7 Hari Terakhir (Rp <?= number_format($p7d['revenue'], 0, ',', '.') ?>)
            </button>
            <button type="button" onclick="document.getElementById('an-custom-filter-box').classList.toggle('hidden')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition flex items-center space-x-1.5 cursor-pointer">
                <i class="fas fa-sliders-h text-xs"></i>
                <span>Atur Bulan / Periode Lain</span>
            </button>
        </div>
    </div>

    <!-- Kotak Pengaturan Bulan / Periode (Muncul Hanya Saat Dipencet "Atur") -->
    <div id="an-custom-filter-box" class="hidden mb-5 p-4 rounded-2xl bg-slate-950/90 border border-amber-500/35 space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-xs font-bold text-slate-300 mr-1">Pilih Cepat:</span>
                <button type="button" onclick="selectAnalyticsPeriod('prev7d')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white text-xs font-semibold transition cursor-pointer">7 Hari Lalu (Minggu Kemarin)</button>
                <button type="button" onclick="selectAnalyticsPeriod('this_month')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white text-xs font-semibold transition cursor-pointer">Bulan Ini</button>
                <button type="button" onclick="selectAnalyticsPeriod('last_month')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white text-xs font-semibold transition cursor-pointer">Bulan Lalu / Kemarin</button>
                <button type="button" onclick="selectAnalyticsPeriod('last_2_months')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white text-xs font-semibold transition cursor-pointer">2 Bulan Terakhir</button>
            </div>

            <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-white/10">
                <span class="text-xs font-bold text-amber-300">Pilih Bulan & Tahun:</span>
                <select id="an-select-month" class="bg-slate-800 text-white text-xs rounded-lg px-2.5 py-1.5 border border-white/15 outline-none">
                    <option value="all">-- Semua Bulan (1 Tahun) --</option>
                    <?php
                    $mList = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    $prevMNum = (int)date('n') - 1; if ($prevMNum < 1) $prevMNum = 12;
                    foreach ($mList as $mIdx => $mName):
                    ?>
                    <option value="<?= $mIdx ?>" <?= $mIdx === $prevMNum ? 'selected' : '' ?>><?= $mName ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="an-select-year" class="bg-slate-800 text-white text-xs rounded-lg px-2.5 py-1.5 border border-white/15 outline-none">
                    <option value="<?= date('Y') ?>" selected>Tahun <?= date('Y') ?></option>
                    <option value="<?= (int)date('Y') - 1 ?>">Tahun <?= (int)date('Y') - 1 ?></option>
                </select>
                <button type="button" onclick="applyAnalyticsCustomMonth()" class="px-3.5 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs transition cursor-pointer">
                    Tampilkan Rekap
                </button>
            </div>
        </div>
    </div>

    <!-- 3 Kartu Ringkasan Periode yang Sedang Dilihat -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <div class="p-3.5 rounded-2xl bg-slate-900/75 border border-emerald-500/30">
            <span id="kpi-period-label" class="text-[10px] text-emerald-400 uppercase tracking-wider block font-extrabold">Total Penghasilan (7 Hari Terakhir)</span>
            <span id="kpi-period-total" class="text-xl font-black text-white font-mono mt-0.5 block">Rp <?= number_format($totalWeekRev, 0, ',', '.') ?></span>
        </div>
        <div class="p-3.5 rounded-2xl bg-slate-900/75 border border-white/10">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Jumlah Orang yang Order</span>
            <span id="kpi-period-orders" class="text-xl font-black text-amber-400 font-mono mt-0.5 block"><?= $totalWeekOrders ?> Pesanan</span>
        </div>
        <div class="p-3.5 rounded-2xl bg-slate-900/75 border border-white/10 flex items-center justify-between">
            <div>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Mode Grafik</span>
                <span id="kpi-period-method" class="text-xs font-bold text-slate-200 mt-0.5 block">Diagram Batang / Garis</span>
            </div>
            <div class="inline-flex p-1 bg-slate-950 rounded-xl border border-white/10 text-[11px]">
                <button type="button" id="btn-chart-bars" onclick="setAnalyticsChartMode('bars')" class="px-2.5 py-1 rounded-lg font-extrabold bg-emerald-500 text-slate-950 transition cursor-pointer">Batang</button>
                <button type="button" id="btn-chart-lines" onclick="setAnalyticsChartMode('lines')" class="px-2.5 py-1 rounded-lg font-bold text-slate-300 hover:text-white transition cursor-pointer">Garis</button>
            </div>
        </div>
    </div>

    <!-- SVG Responsive Chart Container -->
    <div id="analytics-chart-wrapper" class="relative w-full h-60 sm:h-64 mb-6">
        <svg id="dynamic-revenue-svg" viewBox="0 0 860 250" class="w-full h-full overflow-visible" preserveAspectRatio="none"></svg>
    </div>

    <!-- TABEL SIAPA SAJA YANG ORDER PADA PERIODE INI -->
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-slate-950/60">
        <div class="px-4 py-3 bg-slate-900/90 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-xs sm:text-sm font-black text-white flex items-center space-x-2">
                <i class="fas fa-users text-emerald-400"></i>
                <span id="an-orders-table-title">Daftar Siapa Saja yang Order (7 Hari Terakhir)</span>
            </h3>
            <span id="an-orders-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                <?= $totalWeekOrders ?> Pelanggan
            </span>
        </div>
        <div class="overflow-x-auto max-h-80 overflow-y-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/90 text-slate-400 font-bold border-b border-white/10 uppercase">
                    <tr>
                        <th class="p-3.5 pl-4">Kode & Waktu Order</th>
                        <th class="p-3.5">Nama Pelanggan (Siapa yang Order)</th>
                        <th class="p-3.5">Layanan yang Dipesan</th>
                        <th class="p-3.5 text-center">Metode Bayar</th>
                        <th class="p-3.5 pr-4 text-right">Nominal Bayar</th>
                    </tr>
                </thead>
                <tbody id="an-orders-tbody" class="divide-y divide-white/5 text-slate-200"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
const RECAP_CHARTS_DATA = <?= json_encode($recapCharts) ?>;
const RECAP_META = {
    today_ymd: <?= json_encode($recap['today_ymd'] ?? date('Y-m-d')) ?>,
    last_7d_start: <?= json_encode($recap['last_7d_start'] ?? date('Y-m-d', strtotime('-6 days'))) ?>,
    prev_7d_start: <?= json_encode($recap['prev_7d_start'] ?? date('Y-m-d', strtotime('-13 days'))) ?>,
    prev_7d_end: <?= json_encode($recap['prev_7d_end'] ?? date('Y-m-d', strtotime('-7 days'))) ?>,
    this_ym: <?= json_encode($recap['this_ym'] ?? date('Y-m')) ?>,
    last_ym: <?= json_encode($recap['last_ym'] ?? date('Y-m', strtotime('first day of last month'))) ?>,
    orders: <?= json_encode($recapOrders) ?>
};
const MONTH_NAMES_ID = {1:'Januari',2:'Februari',3:'Maret',4:'April',5:'Mei',6:'Juni',7:'Juli',8:'Agustus',9:'September',10:'Oktober',11:'November',12:'Desember'};
const MONTH_SHORT_ID = {1:'Jan',2:'Feb',3:'Mar',4:'Apr',5:'Mei',6:'Jun',7:'Jul',8:'Agu',9:'Sep',10:'Okt',11:'Nov',12:'Des'};

let currentChartMode = 'bars';
let activeChartSeries = (RECAP_CHARTS_DATA['7d'] && RECAP_CHARTS_DATA['7d'].series) ? RECAP_CHARTS_DATA['7d'].series : [];

function fmtRpFullJs(v) {
    v = Math.round(Number(v) || 0);
    return 'Rp ' + v.toLocaleString('id-ID');
}

function renderDynamicRevenueChart() {
    const svg = document.getElementById('dynamic-revenue-svg');
    if (!svg) return;

    const series = activeChartSeries || [];
    let maxRev = 0;
    series.forEach(item => {
        const r = Number(item.revenue) || 0;
        if (r >= maxRev) maxRev = r;
    });

    const chartW = 860, chartH = 250, padL = 75, padR = 24, padT = 46, padB = 44;
    const innerW = chartW - padL - padR;
    const innerH = chartH - padT - padB;
    const n = Math.max(series.length, 1);
    const barW = Math.min(52, (innerW / n) * 0.52);
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
        const gVal = fmtRpFullJs(safeMax * (1 - gi / 4));
        gridHtml += `<line x1="${padL}" y1="${gy}" x2="${chartW - padR}" y2="${gy}" stroke="rgba(255,255,255,0.08)" stroke-dasharray="3,3" stroke-width="1"/>`;
        gridHtml += `<text x="${padL - 8}" y="${gy + 4}" fill="rgba(148,163,184,0.85)" font-size="9.5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="600" text-anchor="end">${gVal}</text>`;
    }

    let barsHtml = '';
    bars.forEach(b => {
        const isPeak = (maxRev > 0 && b.rev === maxRev);
        const badgeW = n > 9 ? 58 : 74;
        const badgeX = -badgeW / 2;
        const fontSz = n > 9 ? '8.5' : '10';
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
                <text x="0" y="2" fill="#ffffff" font-size="${fontSz}" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" text-anchor="middle">${fmtRpFullJs(b.rev)}</text>
            </g>` : ''}
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
                <rect x="-36" y="-11" width="72" height="19" rx="6" fill="#022c22" stroke="#10b981" stroke-width="1.2"/>
                <text x="0" y="2" fill="#ffffff" font-size="9.5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" text-anchor="middle">${fmtRpFullJs(b.rev)}</text>
            </g>` : ''}
        </g>`;
    });

    let xLabelsHtml = '';
    bars.forEach(b => {
        const lblFont = n > 10 ? '10' : '11.5';
        xLabelsHtml += `<text x="${b.cx}" y="${chartH - padB + 16}" fill="#f8fafc" font-size="${lblFont}" font-family="'Plus Jakarta Sans', sans-serif" font-weight="800" text-anchor="middle">${b.label}</text>
            <text x="${b.cx}" y="${chartH - padB + 30}" fill="#94a3b8" font-size="9" font-family="'Plus Jakarta Sans', sans-serif" font-weight="600" text-anchor="middle">${b.date}</text>`;
    });

    const showBars = (currentChartMode === 'bars') ? 'block' : 'none';
    const showLines = (currentChartMode === 'lines') ? 'block' : 'none';

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

function updateAnalyticsOrdersAndKpi(filteredOrders, periodTitle, chartSeries) {
    let totalRev = 0, qrisRev = 0, tunaiRev = 0;
    filteredOrders.forEach(o => {
        const g = Number(o.grand_total) || 0;
        totalRev += g;
        if ((o.payment_method || '').toLowerCase() === 'qris') qrisRev += g; else tunaiRev += g;
    });

    document.getElementById('dynamic-chart-title').textContent = '📊 Rekap Penghasilan & Pelanggan Order (' + periodTitle + ')';
    document.getElementById('kpi-period-label').textContent = 'Total Penghasilan (' + periodTitle + ')';
    document.getElementById('kpi-period-total').textContent = fmtRpFullJs(totalRev);
    document.getElementById('kpi-period-orders').textContent = filteredOrders.length + ' Pesanan';
    document.getElementById('kpi-period-method').textContent = `QRIS: ${fmtRpFullJs(qrisRev)} · Tunai: ${fmtRpFullJs(tunaiRev)}`;
    document.getElementById('an-orders-table-title').textContent = `Daftar Siapa Saja yang Order (${periodTitle})`;
    document.getElementById('an-orders-badge').textContent = filteredOrders.length + ' Pelanggan';

    const tbody = document.getElementById('an-orders-tbody');
    if (tbody) {
        if (filteredOrders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-slate-400">Belum ada pesanan pada periode ${periodTitle}.</td></tr>`;
        } else {
            tbody.innerHTML = filteredOrders.map(o => `
                <tr class="hover:bg-white/5 transition">
                    <td class="p-3.5 pl-4 font-mono text-[11px] text-slate-300">
                        <a href="/admin/orders/${o.id}" class="text-emerald-400 font-bold hover:underline block">#${o.order_code}</a>
                        <span>${o.formatted_dt}</span>
                    </td>
                    <td class="p-3.5">
                        <strong class="text-white font-bold block text-xs">${o.customer_name}</strong>
                        <span class="text-[11px] text-slate-400 font-mono"><i class="fab fa-whatsapp text-emerald-400 mr-1"></i>${o.customer_phone}</span>
                    </td>
                    <td class="p-3.5 text-slate-200">
                        <span>${o.items_text}</span>
                        ${o.service_type === 'home_service' ? '<span class="ml-1.5 px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 text-[10px] font-bold">Home Service</span>' : '<span class="ml-1.5 px-1.5 py-0.5 rounded bg-white/5 text-slate-400 text-[10px]">Studio</span>'}
                    </td>
                    <td class="p-3.5 text-center">
                        <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 font-bold text-[10px]">${o.payment_method}</span>
                    </td>
                    <td class="p-3.5 pr-4 text-right font-mono font-black text-emerald-400 text-sm">
                        ${fmtRpFullJs(o.grand_total)}
                    </td>
                </tr>
            `).join('');
        }
    }

    activeChartSeries = chartSeries;
    renderDynamicRevenueChart();
}

function selectAnalyticsPeriod(mode) {
    const btnToday = document.getElementById('an-btn-today');
    const btn7d = document.getElementById('an-btn-7d');
    const idleCls = 'px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer bg-slate-900 text-slate-200 hover:bg-slate-700 border border-white/10';
    const actCls = 'px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/25';
    if (btnToday) btnToday.className = (mode === 'today') ? actCls : idleCls;
    if (btn7d) btn7d.className = (mode === '7d') ? actCls : idleCls;

    const all = RECAP_META.orders || [];
    let filtered = [];
    let title = '';
    let series = [];

    if (mode === 'today') {
        filtered = all.filter(o => o.date_ymd === RECAP_META.today_ymd);
        title = 'Hari Ini';
        series = filtered.map((o, idx) => ({
            label: o.customer_name.split(' ')[0],
            formatted_date: '#' + o.order_code.slice(-4),
            revenue: o.grand_total,
            orders_count: 1
        }));
        if (series.length === 0) series = [{ label: 'Hari Ini', formatted_date: RECAP_META.today_ymd, revenue: 0, orders_count: 0 }];
    } else if (mode === '7d') {
        filtered = all.filter(o => o.date_ymd >= RECAP_META.last_7d_start && o.date_ymd <= RECAP_META.today_ymd);
        title = '7 Hari Terakhir';
        series = (RECAP_CHARTS_DATA['7d'] && RECAP_CHARTS_DATA['7d'].series) || [];
    } else if (mode === 'prev7d') {
        filtered = all.filter(o => o.date_ymd >= RECAP_META.prev_7d_start && o.date_ymd <= RECAP_META.prev_7d_end);
        title = '7 Hari yang Lalu (Minggu Kemarin)';
        series = (RECAP_CHARTS_DATA['prev7d'] && RECAP_CHARTS_DATA['prev7d'].series) || [];
    } else if (mode === 'this_month') {
        filtered = all.filter(o => o.ym === RECAP_META.this_ym);
        title = 'Bulan Ini';
        series = (RECAP_CHARTS_DATA['this_month'] && RECAP_CHARTS_DATA['this_month'].series) || [];
    } else if (mode === 'last_month') {
        filtered = all.filter(o => o.ym === RECAP_META.last_ym);
        title = 'Bulan Lalu / Kemarin';
        series = (RECAP_CHARTS_DATA['last_month'] && RECAP_CHARTS_DATA['last_month'].series) || [];
    } else if (mode === 'last_2_months') {
        filtered = all.filter(o => o.ym === RECAP_META.last_ym || o.ym === RECAP_META.this_ym);
        title = '2 Bulan Terakhir';
        series = (RECAP_CHARTS_DATA['last_2_months'] && RECAP_CHARTS_DATA['last_2_months'].series) || [];
    }
    updateAnalyticsOrdersAndKpi(filtered, title, series);
}

function applyAnalyticsCustomMonth() {
    const mVal = document.getElementById('an-select-month').value;
    const yVal = Number(document.getElementById('an-select-year').value);
    const all = RECAP_META.orders || [];
    let filtered = [];
    let title = '';
    let series = [];

    if (mVal === 'all') {
        filtered = all.filter(o => Number(o.year) === yVal);
        title = 'Tahun ' + yVal;
        for (let m = 1; m <= 12; m++) {
            const mOrders = filtered.filter(o => Number(o.month) === m);
            const mRev = mOrders.reduce((s, o) => s + (Number(o.grand_total) || 0), 0);
            series.push({
                label: MONTH_SHORT_ID[m],
                formatted_date: String(yVal),
                revenue: mRev,
                orders_count: mOrders.length
            });
        }
    } else {
        const mNum = Number(mVal);
        filtered = all.filter(o => Number(o.year) === yVal && Number(o.month) === mNum);
        title = 'Bulan ' + (MONTH_NAMES_ID[mNum] || mNum) + ' ' + yVal;
        const weeks = [
            { label: 'Minggu 1', range: '01-07', min: 1, max: 7 },
            { label: 'Minggu 2', range: '08-14', min: 8, max: 14 },
            { label: 'Minggu 3', range: '15-21', min: 15, max: 21 },
            { label: 'Minggu 4+', range: '22-31', min: 22, max: 31 }
        ];
        series = weeks.map(w => {
            const wOrders = filtered.filter(o => {
                const day = Number((o.date_ymd || '').slice(-2));
                return day >= w.min && day <= w.max;
            });
            return {
                label: w.label,
                formatted_date: w.range + ' ' + (MONTH_SHORT_ID[mNum] || ''),
                revenue: wOrders.reduce((s, o) => s + (Number(o.grand_total) || 0), 0),
                orders_count: wOrders.length
            };
        });
    }
    updateAnalyticsOrdersAndKpi(filtered, title, series);
}

function setAnalyticsChartMode(mode) {
    currentChartMode = mode;
    const btnBars = document.getElementById('btn-chart-bars');
    const btnLines = document.getElementById('btn-chart-lines');
    if (btnBars) btnBars.className = (mode === 'bars') ? 'px-2.5 py-1 rounded-lg font-extrabold bg-emerald-500 text-slate-950 transition cursor-pointer' : 'px-2.5 py-1 rounded-lg font-bold text-slate-300 hover:text-white transition cursor-pointer';
    if (btnLines) btnLines.className = (mode === 'lines') ? 'px-2.5 py-1 rounded-lg font-extrabold bg-emerald-500 text-slate-950 transition cursor-pointer' : 'px-2.5 py-1 rounded-lg font-bold text-slate-300 hover:text-white transition cursor-pointer';
    renderDynamicRevenueChart();
}

document.addEventListener('DOMContentLoaded', function() {
    selectAnalyticsPeriod('7d');
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

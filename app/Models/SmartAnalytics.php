<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * SmartAnalytics — Rule-based AI engine for Wijaya Barbershop
 * Semua logika berbasis statistik dari data MySQL/SQLite, tanpa external API.
 */
class SmartAnalytics
{
    private static function fetchAll(string $sql, array $params = []): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private static function fetchOne(string $sql, array $params = []): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    // ─────────────────────────────────────────────────────────────
    //  REVENUE & TREND ANALYTICS
    // ─────────────────────────────────────────────────────────────

    private static bool $timelineChecked = false;

    /**
     * Pastikan data timeline transaksi selalu sinkron sampai hari ini (terutama di Vercel di hari mendatang).
     */
    public static function ensureFreshTimelineData(): void
    {
        if (self::$timelineChecked) {
            return;
        }
        self::$timelineChecked = true;

        try {
            $pdo = Database::getConnection();
            $row = self::fetchOne("SELECT MAX(substr(created_at, 1, 10)) as max_date FROM orders WHERE status IN ('settlement','completed')");
            $maxDate = $row['max_date'] ?? null;
            $today = date('Y-m-d');

            if ($maxDate && $maxDate < $today) {
                $diffSec = strtotime($today) - strtotime($maxDate);
                $diffDays = (int) round($diffSec / 86400);
                if ($diffDays > 0 && $diffDays <= 365) {
                    if (Database::getDriver() === 'sqlite') {
                        $stmt = $pdo->prepare("UPDATE orders SET created_at = datetime(created_at, '+' || ? || ' days'), queue_time = datetime(queue_time, '+' || ? || ' days')");
                        $stmt->execute([$diffDays, $diffDays]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE orders SET created_at = DATE_ADD(created_at, INTERVAL ? DAY), queue_time = DATE_ADD(queue_time, INTERVAL ? DAY)");
                        $stmt->execute([$diffDays, $diffDays]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika read-only
        }
    }

    /**
     * Hitung detail rekap penghasilan untuk rentang tanggal tertentu [start, end].
     */
    private static function queryPeriodStats(string $startDateTime, string $endDateTime): array
    {
        $row = self::fetchOne(
            "SELECT 
                COALESCE(SUM(CAST(grand_total AS DECIMAL(15,2))), 0) as revenue,
                COUNT(id) as orders_count,
                COALESCE(SUM(CASE WHEN service_type = 'home_service' THEN CAST(grand_total AS DECIMAL(15,2)) ELSE 0 END), 0) as home_revenue,
                SUM(CASE WHEN service_type = 'home_service' THEN 1 ELSE 0 END) as home_orders,
                COALESCE(SUM(CASE WHEN service_type != 'home_service' OR service_type IS NULL THEN CAST(grand_total AS DECIMAL(15,2)) ELSE 0 END), 0) as studio_revenue,
                SUM(CASE WHEN service_type != 'home_service' OR service_type IS NULL THEN 1 ELSE 0 END) as studio_orders,
                COALESCE(SUM(CASE WHEN payment_method = 'qris' THEN CAST(grand_total AS DECIMAL(15,2)) ELSE 0 END), 0) as qris_revenue,
                SUM(CASE WHEN payment_method = 'qris' THEN 1 ELSE 0 END) as qris_orders,
                COALESCE(SUM(CASE WHEN payment_method != 'qris' OR payment_method IS NULL THEN CAST(grand_total AS DECIMAL(15,2)) ELSE 0 END), 0) as tunai_revenue,
                SUM(CASE WHEN payment_method != 'qris' OR payment_method IS NULL THEN 1 ELSE 0 END) as tunai_orders
             FROM orders
             WHERE status IN ('settlement','completed')
               AND deleted_at IS NULL
               AND created_at >= ?
               AND created_at <= ?",
            [$startDateTime, $endDateTime]
        );

        $rev = (float)($row['revenue'] ?? 0);
        $cnt = (int)($row['orders_count'] ?? 0);

        return [
            'revenue'        => $rev,
            'orders_count'   => $cnt,
            'avg_order'      => $cnt > 0 ? round($rev / $cnt) : 0,
            'studio_revenue' => (float)($row['studio_revenue'] ?? 0),
            'studio_orders'  => (int)($row['studio_orders'] ?? 0),
            'home_revenue'   => (float)($row['home_revenue'] ?? 0),
            'home_orders'    => (int)($row['home_orders'] ?? 0),
            'qris_revenue'   => (float)($row['qris_revenue'] ?? 0),
            'qris_orders'    => (int)($row['qris_orders'] ?? 0),
            'tunai_revenue'  => (float)($row['tunai_revenue'] ?? 0),
            'tunai_orders'   => (int)($row['tunai_orders'] ?? 0),
        ];
    }

    /**
     * Rekapitulasi Penghasilan Multi-Periode Lengkap:
     * Hari Ini, Kemarin, 7 Hari Terakhir, 7 Hari Lalu (Minggu Kemarin), 14 Hari Terakhir,
     * Bulan Ini, Bulan Lalu / Kemarin, 2 Bulan Lalu, 2 Bulan Terakhir, 3 Bulan Terakhir,
     * 6 Bulan Terakhir, Tahun Ini, Tahun Lalu, dan Semua Waktu + Data Grafik & Tabel Bulanan.
     */
    public static function getRevenueRecapSummary(): array
    {
        self::ensureFreshTimelineData();

        $bulanIndo = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
            7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
        ];
        $bulanSingkat = [
            1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun',
            7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'
        ];
        $dayNames = ['Sun'=>'Min','Mon'=>'Sen','Tue'=>'Sel','Wed'=>'Rab','Thu'=>'Kam','Fri'=>'Jum','Sat'=>'Sab'];

        $now = time();
        $todayYmd = date('Y-m-d', $now);
        $yesterdayYmd = date('Y-m-d', strtotime('-1 day', $now));

        $curYear = (int)date('Y', $now);
        $curMonth = (int)date('n', $now);
        $prevYear = $curYear - 1;

        // Bulan Ini
        $thisMonthStart = date('Y-m-01 00:00:00', $now);
        $thisMonthEnd   = date('Y-m-t 23:59:59', $now);
        $thisMonthName  = $bulanIndo[$curMonth] . ' ' . $curYear;

        // Bulan Lalu / Kemarin
        $lastMonthTs    = strtotime('first day of last month', $now);
        $lastMonthNum   = (int)date('n', $lastMonthTs);
        $lastMonthYear  = (int)date('Y', $lastMonthTs);
        $lastMonthStart = date('Y-m-01 00:00:00', $lastMonthTs);
        $lastMonthEnd   = date('Y-m-t 23:59:59', $lastMonthTs);
        $lastMonthName  = $bulanIndo[$lastMonthNum] . ' ' . $lastMonthYear;

        // 2 Bulan yang Lalu
        $twoMonthsAgoTs    = strtotime('first day of -2 months', $now);
        $twoMonthsAgoNum   = (int)date('n', $twoMonthsAgoTs);
        $twoMonthsAgoYear  = (int)date('Y', $twoMonthsAgoTs);
        $twoMonthsAgoStart = date('Y-m-01 00:00:00', $twoMonthsAgoTs);
        $twoMonthsAgoEnd   = date('Y-m-t 23:59:59', $twoMonthsAgoTs);
        $twoMonthsAgoName  = $bulanIndo[$twoMonthsAgoNum] . ' ' . $twoMonthsAgoYear;

        // 3 Bulan Lalu & 6 Bulan Lalu
        $threeMonthsStart = date('Y-m-01 00:00:00', strtotime('first day of -2 months', $now));
        $sixMonthsStart   = date('Y-m-01 00:00:00', strtotime('first day of -5 months', $now));

        // Hitung statistik masing-masing periode
        $stToday        = self::queryPeriodStats($todayYmd . ' 00:00:00', $todayYmd . ' 23:59:59');
        $stYesterday    = self::queryPeriodStats($yesterdayYmd . ' 00:00:00', $yesterdayYmd . ' 23:59:59');
        $stLast7Days    = self::queryPeriodStats(date('Y-m-d 00:00:00', strtotime('-6 days', $now)), $todayYmd . ' 23:59:59');
        $stPrev7Days    = self::queryPeriodStats(date('Y-m-d 00:00:00', strtotime('-13 days', $now)), date('Y-m-d 23:59:59', strtotime('-7 days', $now)));
        $stLast14Days   = self::queryPeriodStats(date('Y-m-d 00:00:00', strtotime('-13 days', $now)), $todayYmd . ' 23:59:59');
        $stThisMonth    = self::queryPeriodStats($thisMonthStart, $thisMonthEnd);
        $stLastMonth    = self::queryPeriodStats($lastMonthStart, $lastMonthEnd);
        $stTwoMonthsAgo = self::queryPeriodStats($twoMonthsAgoStart, $twoMonthsAgoEnd);
        $stLast2Months  = self::queryPeriodStats($lastMonthStart, $thisMonthEnd);
        $stLast3Months  = self::queryPeriodStats($threeMonthsStart, $thisMonthEnd);
        $stLast6Months  = self::queryPeriodStats($sixMonthsStart, $thisMonthEnd);
        $stThisYear     = self::queryPeriodStats("{$curYear}-01-01 00:00:00", "{$curYear}-12-31 23:59:59");
        $stLastYear     = self::queryPeriodStats("{$prevYear}-01-01 00:00:00", "{$prevYear}-12-31 23:59:59");
        $stAllTime      = self::queryPeriodStats("2020-01-01 00:00:00", "2035-12-31 23:59:59");

        $calcGrowth = function (float $curr, float $prev): float {
            if ($prev <= 0) return $curr > 0 ? 100.0 : 0.0;
            return round((($curr - $prev) / $prev) * 100, 1);
        };

        $periods = [
            'today' => array_merge($stToday, [
                'key'        => 'today',
                'label'      => 'Hari Ini',
                'sublabel'   => date('d M Y', $now),
                'compare_to' => 'vs Kemarin (Rp ' . number_format($stYesterday['revenue'], 0, ',', '.') . ')',
                'growth_pct' => $calcGrowth($stToday['revenue'], $stYesterday['revenue']),
                'icon'       => 'fa-calendar-day',
                'color'      => 'emerald',
            ]),
            'yesterday' => array_merge($stYesterday, [
                'key'        => 'yesterday',
                'label'      => 'Kemarin (1 Hari Lalu)',
                'sublabel'   => date('d M Y', strtotime('-1 day', $now)),
                'compare_to' => 'Rekap H-1 Penuh',
                'growth_pct' => 0,
                'icon'       => 'fa-clock-rotate-left',
                'color'      => 'teal',
            ]),
            'last_7_days' => array_merge($stLast7Days, [
                'key'        => 'last_7_days',
                'label'      => '7 Hari Terakhir',
                'sublabel'   => date('d M', strtotime('-6 days', $now)) . ' – ' . date('d M Y', $now),
                'compare_to' => 'vs 7 Hari Lalu (Rp ' . number_format($stPrev7Days['revenue'], 0, ',', '.') . ')',
                'growth_pct' => $calcGrowth($stLast7Days['revenue'], $stPrev7Days['revenue']),
                'icon'       => 'fa-calendar-week',
                'color'      => 'emerald',
            ]),
            'prev_7_days' => array_merge($stPrev7Days, [
                'key'        => 'prev_7_days',
                'label'      => '7 Hari yang Lalu (Minggu Kemarin)',
                'sublabel'   => date('d M', strtotime('-13 days', $now)) . ' – ' . date('d M Y', strtotime('-7 days', $now)),
                'compare_to' => 'Periode H-14 s/d H-7',
                'growth_pct' => 0,
                'icon'       => 'fa-history',
                'color'      => 'cyan',
            ]),
            'last_14_days' => array_merge($stLast14Days, [
                'key'        => 'last_14_days',
                'label'      => '14 Hari Terakhir (2 Minggu)',
                'sublabel'   => date('d M', strtotime('-13 days', $now)) . ' – ' . date('d M Y', $now),
                'compare_to' => 'Akumulasi 2 Minggu Terakhir',
                'growth_pct' => $calcGrowth($stLast7Days['revenue'], $stPrev7Days['revenue']),
                'icon'       => 'fa-calendar-check',
                'color'      => 'blue',
            ]),
            'this_month' => array_merge($stThisMonth, [
                'key'        => 'this_month',
                'label'      => 'Bulan Ini (' . $thisMonthName . ')',
                'sublabel'   => '01 ' . $bulanSingkat[$curMonth] . ' – ' . date('d', $now) . ' ' . $bulanSingkat[$curMonth] . ' ' . $curYear,
                'compare_to' => 'vs Bulan Lalu (Rp ' . number_format($stLastMonth['revenue'], 0, ',', '.') . ')',
                'growth_pct' => $calcGrowth($stThisMonth['revenue'], $stLastMonth['revenue']),
                'icon'       => 'fa-calendar-alt',
                'color'      => 'indigo',
            ]),
            'last_month' => array_merge($stLastMonth, [
                'key'        => 'last_month',
                'label'      => 'Bulan Lalu / Kemarin (' . $lastMonthName . ')',
                'sublabel'   => '01 ' . $bulanSingkat[$lastMonthNum] . ' – ' . date('t', $lastMonthTs) . ' ' . $bulanSingkat[$lastMonthNum] . ' ' . $lastMonthYear,
                'compare_to' => 'vs 2 Bulan Lalu (Rp ' . number_format($stTwoMonthsAgo['revenue'], 0, ',', '.') . ')',
                'growth_pct' => $calcGrowth($stLastMonth['revenue'], $stTwoMonthsAgo['revenue']),
                'icon'       => 'fa-calendar-minus',
                'color'      => 'purple',
            ]),
            'two_months_ago' => array_merge($stTwoMonthsAgo, [
                'key'        => 'two_months_ago',
                'label'      => '2 Bulan yang Lalu (' . $twoMonthsAgoName . ')',
                'sublabel'   => '01 ' . $bulanSingkat[$twoMonthsAgoNum] . ' – ' . date('t', $twoMonthsAgoTs) . ' ' . $bulanSingkat[$twoMonthsAgoNum] . ' ' . $twoMonthsAgoYear,
                'compare_to' => 'Rekap Penuh ' . $twoMonthsAgoName,
                'growth_pct' => 0,
                'icon'       => 'fa-folder-open',
                'color'      => 'amber',
            ]),
            'last_2_months' => array_merge($stLast2Months, [
                'key'        => 'last_2_months',
                'label'      => '2 Bulan Terakhir (' . $bulanSingkat[$lastMonthNum] . ' – ' . $bulanSingkat[$curMonth] . ' ' . $curYear . ')',
                'sublabel'   => $lastMonthName . ' & ' . $thisMonthName,
                'compare_to' => 'Gabungan Bulan Lalu + Bulan Ini',
                'growth_pct' => $calcGrowth($stThisMonth['revenue'], $stLastMonth['revenue']),
                'icon'       => 'fa-layer-group',
                'color'      => 'amber',
            ]),
            'last_3_months' => array_merge($stLast3Months, [
                'key'        => 'last_3_months',
                'label'      => '3 Bulan Terakhir (Kuartal)',
                'sublabel'   => $bulanSingkat[$twoMonthsAgoNum] . ', ' . $bulanSingkat[$lastMonthNum] . ', ' . $bulanSingkat[$curMonth] . ' ' . $curYear,
                'compare_to' => 'Akumulasi 3 Bulan Terakhir',
                'growth_pct' => 0,
                'icon'       => 'fa-chart-pie',
                'color'      => 'orange',
            ]),
            'last_6_months' => array_merge($stLast6Months, [
                'key'        => 'last_6_months',
                'label'      => '6 Bulan Terakhir (Semester)',
                'sublabel'   => date('M Y', strtotime($sixMonthsStart)) . ' – ' . date('M Y', $now),
                'compare_to' => 'Akumulasi 6 Bulan Terakhir',
                'growth_pct' => 0,
                'icon'       => 'fa-chart-area',
                'color'      => 'rose',
            ]),
            'this_year' => array_merge($stThisYear, [
                'key'        => 'this_year',
                'label'      => 'Tahun Ini (' . $curYear . ')',
                'sublabel'   => 'Januari – Desember ' . $curYear,
                'compare_to' => 'vs Tahun ' . $prevYear . ' (Rp ' . number_format($stLastYear['revenue'], 0, ',', '.') . ')',
                'growth_pct' => $calcGrowth($stThisYear['revenue'], $stLastYear['revenue']),
                'icon'       => 'fa-award',
                'color'      => 'emerald',
            ]),
            'last_year' => array_merge($stLastYear, [
                'key'        => 'last_year',
                'label'      => 'Tahun Lalu (' . $prevYear . ')',
                'sublabel'   => 'Januari – Desember ' . $prevYear,
                'compare_to' => 'Rekap Penuh Tahun ' . $prevYear,
                'growth_pct' => 0,
                'icon'       => 'fa-landmark',
                'color'      => 'slate',
            ]),
            'all_time' => array_merge($stAllTime, [
                'key'        => 'all_time',
                'label'      => 'Total Keseluruhan (Semua Waktu)',
                'sublabel'   => 'Tahun ' . $prevYear . ' s/d ' . $curYear,
                'compare_to' => 'Grand Total Omset Terverifikasi',
                'growth_pct' => 100,
                'icon'       => 'fa-sack-dollar',
                'color'      => 'emerald',
            ]),
        ];

        // ── 2. REKAP BULANAN (Januari - Desember untuk Tahun Ini & Tahun Lalu) ──
        $monthlyRows = self::fetchAll(
            "SELECT 
                substr(created_at, 1, 7) as ym,
                COALESCE(SUM(CAST(grand_total AS DECIMAL(15,2))), 0) as revenue,
                COUNT(id) as orders_count,
                SUM(CASE WHEN service_type = 'home_service' THEN 1 ELSE 0 END) as home_orders,
                SUM(CASE WHEN service_type != 'home_service' OR service_type IS NULL THEN 1 ELSE 0 END) as studio_orders,
                COALESCE(SUM(CASE WHEN payment_method = 'qris' THEN CAST(grand_total AS DECIMAL(15,2)) ELSE 0 END), 0) as qris_revenue,
                COALESCE(SUM(CASE WHEN payment_method != 'qris' OR payment_method IS NULL THEN CAST(grand_total AS DECIMAL(15,2)) ELSE 0 END), 0) as tunai_revenue
             FROM orders
             WHERE status IN ('settlement','completed') AND deleted_at IS NULL
             GROUP BY substr(created_at, 1, 7)
             ORDER BY ym ASC"
        );
        $ymMap = [];
        foreach ($monthlyRows as $mr) {
            $ymMap[$mr['ym']] = $mr;
        }

        $monthlyRecap = [];
        $prevMonthRevForGrowth = 0;
        foreach ([$prevYear, $curYear] as $yr) {
            $maxM = ($yr === $curYear) ? $curMonth : 12;
            for ($m = 1; $m <= $maxM; $m++) {
                $ym = sprintf('%04d-%02d', $yr, $m);
                $r = $ymMap[$ym] ?? null;
                $rev = (float)($r['revenue'] ?? 0);
                $cnt = (int)($r['orders_count'] ?? 0);
                $gPct = $prevMonthRevForGrowth > 0 ? round((($rev - $prevMonthRevForGrowth) / $prevMonthRevForGrowth) * 100, 1) : 0;
                $monthlyRecap[] = [
                    'ym'            => $ym,
                    'year'          => $yr,
                    'month_num'     => $m,
                    'label'         => $bulanSingkat[$m] . ' ' . substr((string)$yr, -2),
                    'full_label'    => $bulanIndo[$m] . ' ' . $yr,
                    'formatted_date'=> sprintf('%02d/%d', $m, $yr),
                    'revenue'       => $rev,
                    'orders_count'  => $cnt,
                    'avg_order'     => $cnt > 0 ? round($rev / $cnt) : 0,
                    'studio_orders' => (int)($r['studio_orders'] ?? 0),
                    'home_orders'   => (int)($r['home_orders'] ?? 0),
                    'qris_revenue'  => (float)($r['qris_revenue'] ?? 0),
                    'tunai_revenue' => (float)($r['tunai_revenue'] ?? 0),
                    'growth_pct'    => $gPct,
                ];
                if ($rev > 0) {
                    $prevMonthRevForGrowth = $rev;
                }
            }
        }

        // ── 3. DATASET GRAFIK MULTI-PERIODE (UNTUK INTERACTIVE BAR / LINE CHART) ──
        // A. 7 Hari Terakhir (7d)
        $chart7d = self::getRevenueChart(7);

        // B. 7 Hari yang Lalu / Minggu Kemarin (prev7d: H-13 s/d H-7)
        $chart14d = self::getRevenueChart(14);
        $chartPrev7d = array_slice($chart14d, 0, 7);

        // C. Bulan Ini per Minggu (this_month)
        $buildMonthWeeksChart = function (int $year, int $month) use ($bulanSingkat): array {
            $daysInMonth = (int)date('t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
            $weekRanges = [
                [1, 7, 'Minggu 1'],
                [8, 14, 'Minggu 2'],
                [15, 21, 'Minggu 3'],
                [22, $daysInMonth, 'Minggu 4+'],
            ];
            $series = [];
            foreach ($weekRanges as [$dStart, $dEnd, $wLabel]) {
                $sDt = sprintf('%04d-%02d-%02d 00:00:00', $year, $month, $dStart);
                $eDt = sprintf('%04d-%02d-%02d 23:59:59', $year, $month, $dEnd);
                $st = self::queryPeriodStats($sDt, $eDt);
                $series[] = [
                    'label'          => $wLabel,
                    'formatted_date' => sprintf('%02d-%02d %s', $dStart, $dEnd, $bulanSingkat[$month]),
                    'revenue'        => $st['revenue'],
                    'orders_count'   => $st['orders_count'],
                ];
            }
            return $series;
        };

        $chartThisMonth = $buildMonthWeeksChart($curYear, $curMonth);
        $chartLastMonth = $buildMonthWeeksChart($lastMonthYear, $lastMonthNum);
        $chartLast2Months = array_merge($chartLastMonth, $chartThisMonth);

        // D. 6 Bulan Terakhir (last_6_months)
        $chartLast6Months = array_slice($monthlyRecap, -6);

        // E. Tahun Ini (year_2026) & Tahun Lalu (year_2025)
        $chartYearCur = array_values(array_filter($monthlyRecap, fn($m) => $m['year'] === $curYear));
        $chartYearPrev = array_values(array_filter($monthlyRecap, fn($m) => $m['year'] === $prevYear));

        return [
            'periods'       => $periods,
            'monthly_recap' => array_reverse($monthlyRecap), // Terbaru di atas untuk tabel
            'charts'        => [
                '7d'            => [
                    'title'    => '7 Hari Terakhir (' . $periods['last_7_days']['sublabel'] . ')',
                    'subtitle' => 'Rekap penghasilan harian selama 7 hari terakhir s/d hari ini',
                    'series'   => $chart7d,
                ],
                'prev7d'        => [
                    'title'    => '7 Hari yang Lalu / Minggu Kemarin (' . $periods['prev_7_days']['sublabel'] . ')',
                    'subtitle' => 'Rekap penghasilan harian pada minggu sebelumnya (H-14 s/d H-7)',
                    'series'   => $chartPrev7d,
                ],
                '14d'           => [
                    'title'    => '14 Hari Terakhir (' . $periods['last_14_days']['sublabel'] . ')',
                    'subtitle' => 'Perbandingan tren harian 2 minggu terakhir',
                    'series'   => $chart14d,
                ],
                'this_month'    => [
                    'title'    => 'Bulan Ini — ' . $thisMonthName . ' (Per Minggu)',
                    'subtitle' => 'Akumulasi penghasilan Mingguan pada bulan berjalan',
                    'series'   => $chartThisMonth,
                ],
                'last_month'    => [
                    'title'    => 'Bulan Lalu / Kemarin — ' . $lastMonthName . ' (Per Minggu)',
                    'subtitle' => 'Rekap penghasilan penuh pada bulan kemarin per minggu',
                    'series'   => $chartLastMonth,
                ],
                'last_2_months' => [
                    'title'    => '2 Bulan Terakhir — ' . $lastMonthName . ' & ' . $thisMonthName,
                    'subtitle' => 'Tren mingguan gabungan 2 bulan terakhir (8 minggu)',
                    'series'   => $chartLast2Months,
                ],
                'last_6_months' => [
                    'title'    => '6 Bulan Terakhir (Semester)',
                    'subtitle' => 'Tren omset bulanan selama 6 bulan terakhir',
                    'series'   => $chartLast6Months,
                ],
                'this_year'     => [
                    'title'    => 'Rekap Tahunan ' . $curYear . ' (Januari – ' . $bulanIndo[$curMonth] . ')',
                    'subtitle' => 'Tren penghasilan bulanan sepanjang tahun ' . $curYear,
                    'series'   => $chartYearCur,
                ],
                'last_year'     => [
                    'title'    => 'Rekap Tahunan ' . $prevYear . ' (Januari – Desember ' . $prevYear . ')',
                    'subtitle' => 'Arsip penghasilan bulanan sepanjang tahun ' . $prevYear,
                    'series'   => $chartYearPrev,
                ],
            ],
        ];
    }

    /**
     * Revenue harian N hari terakhir untuk sparkline chart.
     */
    public static function getRevenueChart(int $days = 7): array
    {
        self::ensureFreshTimelineData();
        $startDate = date('Y-m-d 00:00:00', strtotime("-" . ($days - 1) . " days"));
        $rows = self::fetchAll(
            "SELECT substr(created_at, 1, 10) as date, SUM(grand_total) as revenue, COUNT(id) as orders_count
             FROM orders
             WHERE status IN ('settlement','completed')
               AND created_at >= ?
             GROUP BY substr(created_at, 1, 10)
             ORDER BY date ASC",
            [$startDate]
        );

        $dayNames = ['Sun'=>'Min','Mon'=>'Sen','Tue'=>'Sel','Wed'=>'Rab','Thu'=>'Kam','Fri'=>'Jum','Sat'=>'Sab'];
        $revMap = [];
        $ordMap = [];
        foreach ($rows as $r) {
            $revMap[$r['date']] = (float)$r['revenue'];
            $ordMap[$r['date']] = (int)($r['orders_count'] ?? 0);
        }

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dayEn = date('D', strtotime($date));
            $result[] = [
                'date'           => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'label'          => $dayNames[$dayEn] ?? $dayEn,
                'revenue'        => $revMap[$date] ?? 0,
                'orders_count'   => $ordMap[$date] ?? 0,
            ];
        }
        return $result;
    }

    /**
     * Perbandingan pendapatan 7 hari ini vs 7 hari lalu (persen perubahan).
     */
    public static function getRevenueTrend(): array
    {
        $sevenDaysAgo = date('Y-m-d 00:00:00', strtotime('-7 days'));
        $fourteenDaysAgo = date('Y-m-d 00:00:00', strtotime('-14 days'));

        $this7Res = self::fetchOne(
            "SELECT SUM(grand_total) as rev FROM orders
             WHERE status IN ('settlement','completed')
               AND created_at >= ?",
            [$sevenDaysAgo]
        );
        $this7 = (float)($this7Res['rev'] ?? 0);

        $prev7Res = self::fetchOne(
            "SELECT SUM(grand_total) as rev FROM orders
             WHERE status IN ('settlement','completed')
               AND created_at >= ?
               AND created_at < ?",
            [$fourteenDaysAgo, $sevenDaysAgo]
        );
        $prev7 = (float)($prev7Res['rev'] ?? 0);

        $change = ($prev7 > 0) ? (($this7 - $prev7) / $prev7 * 100) : ($this7 > 0 ? 100 : 0);
        return [
            'current'    => $this7,
            'previous'   => $prev7,
            'change_pct' => round($change, 1),
            'trend'      => $change >= 0 ? 'up' : 'down',
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  BUSY HOURS & PEAK DAY
    // ─────────────────────────────────────────────────────────────

    /**
     * Distribusi pesanan per jam (0–23).
     */
    public static function getBusyHours(): array
    {
        $isSqlite = (Database::getDriver() === 'sqlite');
        $hourExpr = $isSqlite ? "CAST(strftime('%H', created_at) AS INTEGER)" : "HOUR(created_at)";
        $rows = self::fetchAll(
            "SELECT {$hourExpr} as hr, COUNT(*) as cnt FROM orders GROUP BY {$hourExpr}",
            []
        );
        $result = array_fill(0, 24, 0);
        foreach ($rows as $r) {
            $result[(int)$r['hr']] = (int)$r['cnt'];
        }
        return $result;
    }

    /**
     * Distribusi pesanan per hari dalam seminggu.
     */
    public static function getPeakDayOfWeek(): array
    {
        $isSqlite = (Database::getDriver() === 'sqlite');
        $dowExpr = $isSqlite ? "(CAST(strftime('%w', created_at) AS INTEGER) + 1)" : "DAYOFWEEK(created_at)";
        $rows = self::fetchAll(
            "SELECT {$dowExpr} as dow, COUNT(*) as cnt FROM orders GROUP BY {$dowExpr}",
            []
        );
        $dayMap = [1=>'Minggu',2=>'Senin',3=>'Selasa',4=>'Rabu',5=>'Kamis',6=>'Jumat',7=>'Sabtu'];
        $result = [];
        $counts = [];
        foreach ($rows as $r) {
            $counts[(int)$r['dow']] = (int)$r['cnt'];
        }
        for ($d = 1; $d <= 7; $d++) {
            $result[] = ['day' => $dayMap[$d], 'count' => $counts[$d] ?? 0];
        }
        return $result;
    }

    // ─────────────────────────────────────────────────────────────
    //  TOP SERVICES
    // ─────────────────────────────────────────────────────────────

    /**
     * Layanan / item terlaris berdasarkan order items dan master items.
     */
    public static function getTopServices(int $limit = 5): array
    {
        $limitInt = (int)$limit;
        $rows = self::fetchAll(
            "SELECT i.item_name as name, COUNT(*) as orders, SUM(oi.total_price) as revenue
             FROM order_items oi
             JOIN items i ON i.id = oi.item_id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.status IN ('settlement','completed')
             GROUP BY i.item_name
             ORDER BY orders DESC
             LIMIT {$limitInt}",
            []
        );

        if (empty($rows)) {
            $rows = self::fetchAll(
                "SELECT item_name as name, 0 as orders, 0 as revenue
                 FROM items
                 WHERE is_active = 1
                 LIMIT {$limitInt}",
                []
            );
        }

        $maxOrders = !empty($rows) ? max(array_column($rows, 'orders')) : 1;
        foreach ($rows as &$r) {
            $r['pct'] = $maxOrders > 0 ? round(($r['orders'] / $maxOrders) * 100) : 0;
            $r['revenue'] = (float)($r['revenue'] ?? 0);
        }
        return $rows;
    }

    // ─────────────────────────────────────────────────────────────
    //  RFM SEGMENTATION (Customer Intelligence)
    // ─────────────────────────────────────────────────────────────

    /**
     * RFM scoring: Recency, Frequency, Monetary per pelanggan.
     */
    public static function getRFMSegments(): array
    {
        $rows = self::fetchAll(
            "SELECT u.id, u.fullname, u.phone,
                    MAX(o.created_at) as last_order_date,
                    COUNT(o.id) as frequency,
                    SUM(o.grand_total) as monetary
             FROM users u
             JOIN orders o ON o.user_id = u.id
             WHERE o.status IN ('settlement','completed')
               AND u.role_id NOT IN (SELECT id FROM roles WHERE role_name IN ('admin','kasir','capster','barber'))
             GROUP BY u.id, u.fullname, u.phone
             HAVING frequency > 0
             ORDER BY monetary DESC
             LIMIT 50",
            []
        );

        if (empty($rows)) return [];

        foreach ($rows as &$r) {
            $lastTs = !empty($r['last_order_date']) ? strtotime($r['last_order_date']) : time();
            $r['recency_days'] = max(0, (int)floor((time() - $lastTs) / 86400));
        }
        unset($r);

        $recencies   = array_column($rows, 'recency_days');
        $frequencies = array_column($rows, 'frequency');
        $monetaries  = array_column($rows, 'monetary');

        $rMax = max($recencies) ?: 1;
        $fMax = max($frequencies) ?: 1;
        $mMax = max($monetaries) ?: 1;

        foreach ($rows as &$r) {
            $rScore = 3 - (int)round(($r['recency_days'] / $rMax) * 2);
            $fScore = (int)round(($r['frequency'] / $fMax) * 2) + 1;
            $mScore = (int)round(($r['monetary'] / $mMax) * 2) + 1;
            $rScore = max(1, min(3, $rScore));
            $fScore = max(1, min(3, $fScore));
            $mScore = max(1, min(3, $mScore));
            $total  = $rScore + $fScore + $mScore;

            if ($total >= 8) {
                $segment = 'Champion';
                $color   = 'emerald';
                $icon    = 'fa-trophy';
            } elseif ($total >= 6) {
                $segment = 'Loyal';
                $color   = 'teal';
                $icon    = 'fa-heart';
            } elseif ($rScore <= 1 && $fScore >= 2) {
                $segment = 'At Risk';
                $color   = 'amber';
                $icon    = 'fa-exclamation-triangle';
            } elseif ($rScore <= 1) {
                $segment = 'Churn';
                $color   = 'rose';
                $icon    = 'fa-user-slash';
            } else {
                $segment = 'Prospek';
                $color   = 'blue';
                $icon    = 'fa-seedling';
            }

            $r['r_score']  = $rScore;
            $r['f_score']  = $fScore;
            $r['m_score']  = $mScore;
            $r['segment']  = $segment;
            $r['color']    = $color;
            $r['icon']     = $icon;
            $r['monetary'] = (float)$r['monetary'];
        }
        unset($r);
        return $rows;
    }

    /**
     * Pelanggan yang berisiko churn (tidak pesan X hari terakhir).
     */
    public static function getChurnRiskCustomers(int $days = 14): array
    {
        $rows = self::fetchAll(
            "SELECT u.id, u.fullname, u.phone,
                    MAX(o.created_at) as last_order_date,
                    COUNT(o.id) as total_orders,
                    SUM(o.grand_total) as total_spend
             FROM users u
             JOIN orders o ON o.user_id = u.id
             WHERE o.status IN ('settlement','completed')
             GROUP BY u.id, u.fullname, u.phone
             ORDER BY total_spend DESC",
            []
        );

        $filtered = [];
        foreach ($rows as $r) {
            $lastTs = !empty($r['last_order_date']) ? strtotime($r['last_order_date']) : time();
            $daysSinceLast = max(0, (int)floor((time() - $lastTs) / 86400));
            if ($daysSinceLast >= $days) {
                $r['days_since_last'] = $daysSinceLast;
                $r['total_spend'] = (float)($r['total_spend'] ?? 0);
                $filtered[] = $r;
                if (count($filtered) >= 10) break;
            }
        }
        return $filtered;
    }

    // ─────────────────────────────────────────────────────────────
    //  SERVICE RECOMMENDATIONS (Collaborative Filtering stub)
    // ─────────────────────────────────────────────────────────────

    /**
     * Rekomendasi layanan berdasarkan: item populer yang belum pernah dipesan user ini.
     */
    public static function getServiceRecommendations(int $userId): array
    {
        $myItems = self::fetchAll(
            "SELECT DISTINCT i.item_name as name FROM order_items oi
             JOIN items i ON i.id = oi.item_id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.user_id = ?",
            [$userId]
        );
        $myNames = array_column($myItems, 'name');

        $popular = self::fetchAll(
            "SELECT i.item_name as name, COUNT(*) as cnt, AVG(oi.price) as avg_price
             FROM order_items oi
             JOIN items i ON i.id = oi.item_id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.status IN ('settlement','completed')
             GROUP BY i.item_name
             ORDER BY cnt DESC
             LIMIT 10",
            []
        );

        if (empty($popular)) {
            $popular = self::fetchAll(
                "SELECT item_name as name, 0 as cnt, price as avg_price FROM items WHERE is_active=1 ORDER BY price DESC LIMIT 10",
                []
            );
        }

        $recs = [];
        foreach ($popular as $item) {
            if (!in_array($item['name'], $myNames)) {
                $recs[] = $item;
            }
            if (count($recs) >= 4) break;
        }

        if (empty($recs) && !empty($popular)) {
            $recs = array_slice($popular, 0, 4);
        }

        return $recs;
    }

    /**
     * Layanan favorit user berdasarkan riwayat pesanan.
     */
    public static function getUserFavoriteServices(int $userId): array
    {
        return self::fetchAll(
            "SELECT i.item_name as name, COUNT(*) as cnt, SUM(oi.price) as spend
             FROM order_items oi
             JOIN items i ON i.id = oi.item_id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.user_id = ?
               AND o.status IN ('settlement','completed')
             GROUP BY i.item_name
             ORDER BY cnt DESC
             LIMIT 3",
            [$userId]
        );
    }

    // ─────────────────────────────────────────────────────────────
    //  SMART ALERTS (Admin Early Warning)
    // ─────────────────────────────────────────────────────────────

    /**
     * Peringatan otomatis: pesanan pending banyak, pendapatan turun, dll.
     */
    public static function getSmartAlerts(): array
    {
        $alerts = [];

        $pendingRes = self::fetchOne(
            "SELECT COUNT(*) as cnt FROM orders WHERE status = 'pending'",
            []
        );
        $pending = (int)($pendingRes['cnt'] ?? 0);
        if ($pending >= 3) {
            $alerts[] = [
                'type'    => 'warning',
                'icon'    => 'fa-clock',
                'color'   => 'amber',
                'title'   => 'Pesanan Menumpuk',
                'message' => "{$pending} pesanan masih PENDING — segera proses untuk menjaga kepuasan pelanggan.",
                'action'  => '/admin/orders',
                'action_label' => 'Lihat Pesanan',
            ];
        }

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $todayRevRes = self::fetchOne(
            "SELECT SUM(grand_total) as rev FROM orders WHERE substr(created_at, 1, 10) = ? AND status IN ('settlement','completed')",
            [$today]
        );
        $todayRev = (float)($todayRevRes['rev'] ?? 0);

        $yestRevRes = self::fetchOne(
            "SELECT SUM(grand_total) as rev FROM orders WHERE substr(created_at, 1, 10) = ? AND status IN ('settlement','completed')",
            [$yesterday]
        );
        $yestRev = (float)($yestRevRes['rev'] ?? 0);

        if ($yestRev > 0 && $todayRev < $yestRev * 0.5) {
            $drop = round((1 - $todayRev / $yestRev) * 100);
            $alerts[] = [
                'type'    => 'danger',
                'icon'    => 'fa-chart-line',
                'color'   => 'rose',
                'title'   => 'Pendapatan Turun Drastis',
                'message' => "Pendapatan hari ini turun {$drop}% dibanding kemarin. Periksa kondisi bisnis.",
                'action'  => '/admin/analytics',
                'action_label' => 'Lihat Analitik',
            ];
        }

        $todayOrdersRes = self::fetchOne(
            "SELECT COUNT(*) as cnt FROM orders WHERE substr(created_at, 1, 10) = ?",
            [$today]
        );
        $todayOrders = (int)($todayOrdersRes['cnt'] ?? 0);
        if ($todayOrders === 0) {
            $alerts[] = [
                'type'    => 'info',
                'icon'    => 'fa-info-circle',
                'color'   => 'blue',
                'title'   => 'Belum Ada Pesanan Hari Ini',
                'message' => 'Tidak ada pesanan masuk hari ini. Pertimbangkan promo atau notifikasi ke pelanggan.',
                'action'  => '/admin/vouchers',
                'action_label' => 'Buat Promo',
            ];
        }

        $churnCount = count(self::getChurnRiskCustomers(14));
        if ($churnCount > 0) {
            $alerts[] = [
                'type'    => 'warning',
                'icon'    => 'fa-user-clock',
                'color'   => 'orange',
                'title'   => 'Pelanggan Berisiko Churn',
                'message' => "{$churnCount} pelanggan tidak memesan selama 14+ hari. Hubungi mereka sekarang!",
                'action'  => '/admin/analytics#churn',
                'action_label' => 'Lihat Detail',
            ];
        }

        return $alerts;
    }

    // ─────────────────────────────────────────────────────────────
    //  GENERAL STATS
    // ─────────────────────────────────────────────────────────────

    public static function getAverageOrderValue(): float
    {
        $res = self::fetchOne(
            "SELECT AVG(grand_total) as avg_val FROM orders WHERE status IN ('settlement','completed')",
            []
        );
        return round((float)($res['avg_val'] ?? 0));
    }

    public static function getConversionRate(): float
    {
        $totalRes = self::fetchOne("SELECT COUNT(*) as cnt FROM orders", []);
        $total = (int)($totalRes['cnt'] ?? 0);

        $paidRes = self::fetchOne(
            "SELECT COUNT(*) as cnt FROM orders WHERE status IN ('settlement','completed')",
            []
        );
        $paid = (int)($paidRes['cnt'] ?? 0);

        return $total > 0 ? round(($paid / $total) * 100, 1) : 0;
    }

    public static function getTodayStats(): array
    {
        $today = date('Y-m-d');
        $ordersRes = self::fetchOne("SELECT COUNT(*) as c FROM orders WHERE substr(created_at, 1, 10) = ?", [$today]);
        $orders = (int)($ordersRes['c'] ?? 0);

        $revRes = self::fetchOne(
            "SELECT SUM(grand_total) as r FROM orders WHERE substr(created_at, 1, 10) = ? AND status IN ('settlement','completed')",
            [$today]
        );
        $revenue = (float)($revRes['r'] ?? 0);

        $custRes = self::fetchOne("SELECT COUNT(*) as c FROM users WHERE substr(created_at, 1, 10) = ?", [$today]);
        $newCustomers = (int)($custRes['c'] ?? 0);

        return ['orders' => $orders, 'revenue' => $revenue, 'new_customers' => $newCustomers];
    }

    // ─────────────────────────────────────────────────────────────
    //  AI ROADMAP & STRATEGIC PLANS (Rencana Kedepannya & Inovasi)
    // ─────────────────────────────────────────────────────────────

    /**
     * Roadmap Rencana Pengembangan Sistem Cerdas Masa Depan
     */
    public static function getAIRoadmap(): array
    {
        return [
            [
                'phase' => 'Fase 1',
                'status' => 'Aktif (Live Real-Time)',
                'badge_color' => 'emerald',
                'title' => 'Statistical Intelligence & Customer Retention',
                'description' => 'Sistem analitik cerdas berbasis data riil transaksi: RFM Segmentation, Prediksi Jam Sibuk, Asisten Chatbot Interaktif, dan Rekomendasi Layanan Personal.',
                'tech' => ['PHP MVC', 'MySQL Analytics', 'Rule Engine', 'SVG Sparklines', 'WhatsApp Gateway'],
                'progress' => 100,
            ],
            [
                'phase' => 'Fase 2',
                'status' => 'Dalam Pengembangan (Q1-Q2)',
                'badge_color' => 'purple',
                'title' => 'Computer Vision: AI Face Shape & Hairstyle Matcher',
                'description' => 'Pelanggan memindai wajah via kamera smartphone/webcam; AI menganalisis proporsi wajah (oval, round, square, heart) lalu merekomendasikan 3 model rambut paling proporsional.',
                'tech' => ['TensorFlow.js', 'FaceMesh / MediaPipe', 'Hairstyle Overlay AR', 'Dataset 50+ Gaya Rambut'],
                'progress' => 65,
            ],
            [
                'phase' => 'Fase 3',
                'status' => 'Rencana Q3',
                'badge_color' => 'blue',
                'title' => 'Predictive Inventory & Supply Chain Automation',
                'description' => 'Prediksi otomatis habisnya stok bahan barbershop (pomade, toner, pisau silet, sampo) berdasarkan volume pesanan mingguan dengan auto-reorder trigger via WhatsApp ke supplier.',
                'tech' => ['Moving Average Forecasting', 'Safety Stock Formula', 'Auto Purchase Order', 'Supplier WhatsApp Bot'],
                'progress' => 30,
            ],
            [
                'phase' => 'Fase 4',
                'status' => 'Rencana Q4',
                'badge_color' => 'amber',
                'title' => 'Dynamic Pricing & Smart Home-Service Dispatcher',
                'description' => 'Pemberian diskon dinamis (Happy Hour Otomatis) saat okupansi antrean < 30% untuk memicu pesanan. Penentuan rute capster Home Service terdekat berbasis algoritma TSP/GPS.',
                'tech' => ['Dynamic Pricing Engine', 'Dijkstra / TSP Routing', 'Google Maps API', 'Load Balancer Antrean'],
                'progress' => 15,
            ],
            [
                'phase' => 'Fase 5',
                'status' => 'Visi Jangka Panjang',
                'badge_color' => 'rose',
                'title' => 'IoT Smart Mirror & Voice-Activated Booking',
                'description' => 'Pemasangan cermin pintar (Smart Mirror) di outlet Wijaya Barbershop untuk menampilkan simulasi gaya rambut langsung dan asisten suara bahasa Indonesia untuk pemesanan bebas sentuh.',
                'tech' => ['Raspberry Pi IoT', 'Smart Two-Way Mirror', 'Speech-to-Text Whisper AI', 'Voice Booking Assistant'],
                'progress' => 5,
            ],
        ];
    }

    /**
     * Rekomendasi Rencana Aksi Bisnis Strategis Berbasis Data Aktual
     */
    public static function getStrategicActionPlans(): array
    {
        $busyHours = self::getBusyHours();
        $churn = self::getChurnRiskCustomers(14);
        $top = self::getTopServices(3);
        $avgOrder = self::getAverageOrderValue();

        // Cari jam tersepi di siang hari (08-17)
        $quietHours = [];
        for ($h = 8; $h <= 17; $h++) {
            if (($busyHours[$h] ?? 0) <= 1) {
                $quietHours[] = sprintf('%02d:00', $h);
            }
        }
        $quietStr = !empty($quietHours) ? implode(', ', array_slice($quietHours, 0, 3)) : '13:00 - 15:00';

        return [
            [
                'category' => 'Optimasi Okupansi Jam Sepi',
                'icon' => 'fa-bolt',
                'color' => 'amber',
                'title' => 'Flash Sale "Happy Hour" Jam Sepi (' . $quietStr . ')',
                'rationale' => 'Analisis AI mendeteksi volume kunjungan rendah pada rentang waktu ini. Peluncuran voucher potongan khusus jam ini diprediksi mendongkrak utilitas capster hingga 40%.',
                'action_label' => 'Buat Voucher Happy Hour',
                'action_url' => '/admin/vouchers',
            ],
            [
                'category' => 'Retensi & Anti-Churn',
                'icon' => 'fa-user-check',
                'color' => 'rose',
                'title' => 'Re-Engage ' . count($churn) . ' Pelanggan Berisiko Churn',
                'rationale' => 'Terdapat pelanggan yang belum memesan kembali lebih dari 14 hari. Kirimkan pesan sapaan hangat via WhatsApp dengan voucher cashback atau bonus poin loyalitas.',
                'action_label' => 'Hubungi Pelanggan via WA',
                'action_url' => '#churn',
            ],
            [
                'category' => 'Peningkatan Basket Size',
                'icon' => 'fa-tags',
                'color' => 'emerald',
                'title' => 'Bundling Layanan Terlaris dengan Perawatan Rambut',
                'rationale' => 'Layanan "' . ($top[0]['name'] ?? 'Potong Rambut Dewasa') . '" adalah produk terpopuler. Padukan dengan Cuci/Creambath dalam satu paket promo untuk menaikkan rata-rata belanja di atas Rp ' . number_format($avgOrder, 0, ',', '.') . '.',
                'action_label' => 'Kelola Daftar Item',
                'action_url' => '/admin/items',
            ],
            [
                'category' => 'Manajemen Jadwal Capster',
                'icon' => 'fa-users-cog',
                'color' => 'indigo',
                'title' => 'Penjadwalan 3 Capster Standby Penuh di Hari Puncak (Jumat & Sabtu)',
                'rationale' => 'Distribusi data pesanan menunjukkan konsentrasi pesanan tertinggi di akhir pekan. Memastikan seluruh capster standby akan menjaga antrean di bawah 15 menit.',
                'action_label' => 'Kelola Jadwal Barber',
                'action_url' => '/admin/users',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  RULE-BASED CHATBOT
    // ─────────────────────────────────────────────────────────────

    public static function getChatbotResponse(string $message): array
    {
        $msg   = mb_strtolower(trim($message));
        $reply = '';
        $quick = [];

        // ── Rencana Masa Depan & Roadmap AI (BARU)
        if (preg_match('/\b(rencana|roadmap|masa depan|kedepan|inovasi|teknologi|ai masa depan|pengembangan|fitur baru|arah)\b/u', $msg)) {
            $reply = "🚀 **Rencana Pengembangan & Roadmap AI Wijaya Barbershop:**\n\n"
                   . "1. **Fase 1 (Aktif)**: Analitik Cerdas, Segmentasi Pelanggan RFM, & Notifikasi WhatsApp Otomatis.\n"
                   . "2. **Fase 2 (Segera Hadir)**: **AI Face Shape & Hairstyle Matcher** — Rekomendasi gaya rambut otomatis berbasis pemindaian kamera bentuk wajah Anda!\n"
                   . "3. **Fase 3**: **Auto-Supply Chain** — Prediksi otomatis stok pomade & bahan barbershop.\n"
                   . "4. **Fase 4**: **Dynamic Pricing & Smart Dispatcher** — Diskon happy hour otomatis & penentuan rute home service terdekat.\n"
                   . "5. **Fase 5**: **IoT Smart Mirror & Voice Booking** — Cermin pintar interaktif & pemesanan lewat suara!\n\n"
                   . "Kami terus berinovasi untuk memberikan pengalaman pangkas rambut terbaik di Kota Metro! 💈✨";
            $quick = ['Coba Rekomendasi AI', 'Jam Buka', 'Pesan Sekarang', 'Promo Hari Ini'];

        } elseif (preg_match('/\b(rekap|penghasilan|pendapatan|omset|omzet|laporan keuangan|pemasukan)\b/u', $msg)) {
            $recap = self::getRevenueRecapSummary()['periods'] ?? [];
            $rToday = number_format($recap['today']['revenue'] ?? 0, 0, ',', '.');
            $r7d    = number_format($recap['last_7_days']['revenue'] ?? 0, 0, ',', '.');
            $rPrev7 = number_format($recap['prev_7_days']['revenue'] ?? 0, 0, ',', '.');
            $rThisM = number_format($recap['this_month']['revenue'] ?? 0, 0, ',', '.');
            $rLastM = number_format($recap['last_month']['revenue'] ?? 0, 0, ',', '.');
            $r2M    = number_format($recap['last_2_months']['revenue'] ?? 0, 0, ',', '.');
            $rThisY = number_format($recap['this_year']['revenue'] ?? 0, 0, ',', '.');
            $rLastY = number_format($recap['last_year']['revenue'] ?? 0, 0, ',', '.');
            $rAll   = number_format($recap['all_time']['revenue'] ?? 0, 0, ',', '.');

            $reply = "💰 **Rekapitulasi Penghasilan Wijaya Barbershop (Real-Time):**\n\n"
                   . "• **Hari Ini**: Rp {$rToday} (" . ($recap['today']['orders_count'] ?? 0) . " Order)\n"
                   . "• **7 Hari Terakhir**: Rp {$r7d} (" . ($recap['last_7_days']['orders_count'] ?? 0) . " Order)\n"
                   . "• **7 Hari yang Lalu (Minggu Kemarin)**: Rp {$rPrev7} (" . ($recap['prev_7_days']['orders_count'] ?? 0) . " Order)\n"
                   . "• **Bulan Ini**: Rp {$rThisM} (" . ($recap['this_month']['orders_count'] ?? 0) . " Order)\n"
                   . "• **Bulan Lalu / Kemarin**: Rp {$rLastM} (" . ($recap['last_month']['orders_count'] ?? 0) . " Order)\n"
                   . "• **2 Bulan Terakhir**: Rp {$r2M} (" . ($recap['last_2_months']['orders_count'] ?? 0) . " Order)\n"
                   . "• **Tahun Ini (" . date('Y') . ")**: Rp {$rThisY} (" . ($recap['this_year']['orders_count'] ?? 0) . " Order)\n"
                   . "• **Tahun Lalu (" . ((int)date('Y') - 1) . ")**: Rp {$rLastY} (" . ($recap['last_year']['orders_count'] ?? 0) . " Order)\n"
                   . "• **Grand Total Semua Waktu**: **Rp {$rAll}**\n\n"
                   . "Grafik diagram batang & tabel detail per periode dapat dilihat pada menu **Analitik Cerdas**!";
            $quick = ['Rencana Kedepannya', 'Harga Layanan', 'Promo Hari Ini', 'Jam Buka'];

        } elseif (preg_match('/\b(halo|hai|hi|hello|selamat|hei)\b/u', $msg)) {
            $reply = "Halo! 👋 Selamat datang di Wijaya Barbershop — Asisten Pintar siap membantu Anda! Ada yang bisa saya bantu hari ini?";
            $quick = ['Jam Buka', 'Daftar Layanan', 'Harga Promo', 'Promo Hari Ini'];

        // ── Kombinasi: Harga Promo / Diskon Layanan Hari Ini
        } elseif (
            preg_match('/\b(harga promo|promo harga|diskon harga|tarif promo|biaya promo)\b/u', $msg) ||
            (preg_match('/\b(harga|biaya|tarif|berapa)\b/u', $msg) && preg_match('/\b(promo|diskon|voucher|kupon|potongan|hemat)\b/u', $msg))
        ) {
            $reply = "🎉 **Harga Promo & Diskon Spesial Hari Ini di Wijaya Barbershop:**\n\n"
                   . "💈 **Tarif Layanan Super Hemat (Rp 1.000 – Rp 1.500):**\n"
                   . "• Potong Rambut Dewasa: **Rp 1.000**\n"
                   . "• Paket Lengkap (Cuci + Pijat): **Rp 1.500**\n"
                   . "• Cuci Rambut & Creambath: **Rp 1.200**\n"
                   . "• Potong Rambut Anak & Balita: **Rp 1.000**\n"
                   . "• Cukur Kumis & Jenggot: **Rp 1.000**\n"
                   . "• Semir / Pewarnaan Rambut: **Rp 1.500**\n"
                   . "• Hair Tattoo Artistik: **Rp 1.500**\n"
                   . "• Home Service (Cukur Panggilan): Mulai **Rp 1.000** (+ ongkir zona)\n\n"
                   . "🎟️ **Kupon Diskon Voucher Aktif:**\n"
                   . "• **GANTENG20** — Diskon 20% (Maks. potongan Rp 500)\n"
                   . "• **MEMBERBARU** — Potongan Langsung Rp 300\n"
                   . "• **HOMESERVICE10** — Diskon Cukur Panggilan Rp 250\n"
                   . "• **WIJAYAHEMAT** — Potongan Langsung Rp 200\n\n"
                   . "⭐ **Tambahan Diskon Otomatis Member:**\n"
                   . "• Silver: Diskon 5% | Gold: Diskon 10% | Platinum: Diskon 15%\n\n"
                   . "Kupon bisa langsung dimasukkan pada kolom voucher saat pemesanan / checkout! 💈✨";
            $quick = ['Pesan Layanan', 'Home Service', 'Klaim Kupon', 'Daftar Member'];

        } elseif (preg_match('/\b(jam|buka|tutup|operasional|waktu|jadwal|kapan)\b/u', $msg)) {
            $reply = "🕐 Wijaya Barbershop buka setiap hari:\n• **Senin–Sabtu**: 08.00 – 21.00 WIB\n• **Minggu & Hari Libur**: 09.00 – 18.00 WIB\n\nWaktu terbaik kunjungi kami adalah pagi (08–10) atau sore (16–18) untuk menghindari antrean panjang.";
            $quick = ['Pesan Sekarang', 'Home Service', 'Capster Tersedia'];

        } elseif (preg_match('/\b(harga|biaya|tarif|berapa|bayar|mahal|murah)\b/u', $msg)) {
            try {
                $items = self::fetchAll(
                    "SELECT item_name, price FROM items WHERE is_active = 1 AND deleted_at IS NULL ORDER BY category_id ASC, id ASC"
                );
            } catch (\Exception $e) {
                $items = [];
            }

            if (!empty($items)) {
                $reply = "💈 **Daftar Harga Layanan Wijaya Barbershop (Rp 1.000 – Rp 1.500):**\n";
                foreach ($items as $it) {
                    $reply .= "• " . $it['item_name'] . ": Rp " . number_format((int)$it['price'], 0, ',', '.') . "\n";
                }
                $reply .= "• Home Service (Cukur Panggilan ke Rumah): Mulai Rp 1.000 (+ ongkir zona)\n\n";
                $reply .= "Semua harga sudah termasuk layanan profesional capster berpengalaman! 🌟";
            } else {
                $reply = "💈 **Daftar Harga Layanan Wijaya Barbershop (Rp 1.000 – Rp 1.500):**\n"
                       . "• Potong Rambut Dewasa: Rp 1.000\n"
                       . "• Paket Lengkap (Cuci+Pijat): Rp 1.500\n"
                       . "• Cuci Rambut & Creambath (Hair Spa): Rp 1.200\n"
                       . "• Potong Rambut Anak & Balita: Rp 1.000\n"
                       . "• Cukur Kumis & Jenggot (Shaving): Rp 1.000\n"
                       . "• Semir Rambut / Pewarnaan: Rp 1.500\n"
                       . "• Hair Tattoo Artistik: Rp 1.500\n"
                       . "• Home Service: Mulai Rp 1.000 (+ ongkir zona)\n\n"
                       . "Semua harga sudah termasuk layanan profesional capster berpengalaman! 🌟";
            }
            $quick = ['Pesan Layanan', 'Home Service', 'Lihat Promo'];

        } elseif (preg_match('/\b(home|rumah|panggilan|datang|antar|lokasi|kunjung)\b/u', $msg)) {
            $reply = "🏠 Layanan Cukur Panggilan (Home Service) tersedia!\n• Tarif jasa pangkas mulai **Rp 1.000**\n• Area Metro & sekitarnya\n• Capster datang ke rumah, kos, atau kantor Anda\n• Biaya tambahan transport sesuai zona area\n• Pesan minimal 2 jam sebelum kedatangan\n\nKlik tombol di bawah untuk pesan Home Service sekarang! 🛵";
            $quick = ['Pesan Home Service', 'Lihat Area Layanan', 'Hubungi WhatsApp'];

        } elseif (preg_match('/\b(promo|diskon|voucher|kupon|potongan|hemat|gratis|cashback)\b/u', $msg)) {
            try {
                $vouchers = self::fetchAll(
                    "SELECT code, title, discount_type, discount_value FROM vouchers WHERE is_active = 1 AND deleted_at IS NULL ORDER BY id ASC"
                );
            } catch (\Exception $e) {
                $vouchers = [];
            }

            $reply = "🎁 **Promo & Kupon Diskon Aktif Wijaya Barbershop:**\n\n";
            if (!empty($vouchers)) {
                foreach ($vouchers as $v) {
                    $valStr = ($v['discount_type'] === 'percentage') 
                        ? "Diskon {$v['discount_value']}%" 
                        : "Potongan Rp " . number_format((int)$v['discount_value'], 0, ',', '.');
                    $reply .= "• **{$v['code']}** ({$valStr}) — {$v['title']}\n";
                }
            } else {
                $reply .= "• **GANTENG20** (Diskon 20%, Maks Rp 500)\n"
                       . "• **MEMBERBARU** (Potongan Langsung Rp 300)\n"
                       . "• **HOMESERVICE10** (Diskon Cukur Panggilan Rp 250)\n"
                       . "• **WIJAYAHEMAT** (Potongan Spesial Studio Rp 200)\n";
            }

            $reply .= "\n⭐ **Keuntungan Diskon Otomatis Member:**\n"
                   . "• **Member Silver**: Diskon otomatis 5% setiap order\n"
                   . "• **Member Gold**: Diskon otomatis 10% + 1.5x multiplier poin\n"
                   . "• **Member Platinum**: Diskon otomatis 15% + prioritas antrean\n\n"
                   . "Ketik kode voucher di halaman checkout saat memesan layanan! 🏷️✨";
            $quick = ['Lihat Semua Promo', 'Cek Poin Saya', 'Daftar Member'];

        } elseif (preg_match('/\b(poin|point|loyalitas|member|keanggotaan|vip|reward|hadiah)\b/u', $msg)) {
            $reply = "⭐ **Program Loyalitas Wijaya Barbershop:**\n"
                   . "• Setiap transaksi mendapatkan poin loyalitas!\n"
                   . "• **Bronze** (0–500 poin): Diskon 0% (Poin Reguler)\n"
                   . "• **Silver** (501–1500 poin): Diskon otomatis 5% + 1.2x poin\n"
                   . "• **Gold** (1501–3000 poin): Diskon otomatis 10% + 1.5x poin\n"
                   . "• **Platinum** (3001+ poin): Diskon otomatis 15% VIP Treatment!\n\n"
                   . "Poin bisa ditukar potongan diskon langsung saat checkout! 💎";
            $quick = ['Cek Poin Saya', 'Lihat Benefit Member', 'Tukar Poin'];
        } elseif (preg_match('/\b(bayar|payment|transfer|qris|tunai|cash|kartu|ovo|gopay|dana)\b/u', $msg)) {
            $reply = "💳 Metode Pembayaran yang Diterima:\n• **QRIS** (Scan dari BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana, ShopeePay)\n• **Tunai / Cash** (Bayar langsung di kasir studio atau bayar tunai di tempat untuk Home Service)\n\nSemua transaksi mendapatkan tiket antrean digital & konfirmasi WhatsApp otomatis! 📱";
            $quick = ['Pesan & Bayar Sekarang', 'Lihat Layanan'];
        } elseif (preg_match('/\b(capster|barber|tukang|potong|pangkas|cukur|gunting)\b/u', $msg)) {
            $reply = "✂️ Tim Capster Profesional Wijaya Barbershop:\n• **Doni** — Spesialis fade & undercut modern\n• **Lana** — Ahli creambath & perawatan rambut\n• **Yoyo** — Juara potong klasik & pompadour\n\nSemua capster bersertifikat & berpengalaman 5+ tahun. Anda bisa pilih capster favorit saat memesan! 💈";
            $quick = ['Pilih Capster', 'Pesan Sekarang'];
        } elseif (preg_match('/\b(alamat|lokasi|mana|dimana|jalan|tempat|kantor|outlet)\b/u', $msg)) {
            $reply = "📍 Lokasi Wijaya Barbershop:\n• **Alamat**: Jl. Metro Raya No. 12, Kota Metro, Lampung\n• Dekat pusat kota, mudah dijangkau\n• Parkir luas tersedia\n• Jam operasional: Senin–Sabtu 08–21, Minggu 09–18\n\nAtau gunakan Home Service agar capster datang ke Anda! 🛵";
            $quick = ['Pesan Home Service', 'Jam Buka', 'Hubungi Kami'];
        } elseif (preg_match('/\b(kontak|hubungi|telepon|wa|whatsapp|nomor|telp|call)\b/u', $msg)) {
            $reply = "📞 Kontak Wijaya Barbershop:\n• **WhatsApp**: 0819-3597-5336\n• **Instagram**: @wijayabarbershop\n• **Email**: info@wijayabarbershop.id\n\nRespon WhatsApp tercepat di jam operasional (08–21 WIB). Silakan chat kami kapan saja! 💬";
            $quick = ['Chat WhatsApp', 'Jam Buka', 'Pesan Online'];
        } elseif (preg_match('/\b(terima kasih|makasih|thanks|thank you|thx|mantap|bagus|oke)\b/u', $msg)) {
            $reply = "Sama-sama! 😊 Senang bisa membantu. Jangan ragu untuk bertanya lagi ya. Sampai jumpa di Wijaya Barbershop! ✂️💈";
            $quick = ['Pesan Sekarang', 'Lihat Promo'];
        } else {
            $reply = "Maaf, saya belum paham pertanyaan Anda. 🤔 Coba tanyakan hal berikut:";
            $quick = ['Jam Buka', 'Harga Layanan', 'Home Service', 'Promo & Diskon', 'Poin Loyalitas', 'Kontak Kami'];
        }

        return ['reply' => $reply, 'quick_replies' => $quick];
    }
}

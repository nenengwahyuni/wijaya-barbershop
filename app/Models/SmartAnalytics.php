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

    /**
     * Revenue harian N hari terakhir untuk sparkline chart.
     */
    public static function getRevenueChart(int $days = 7): array
    {
        $startDate = date('Y-m-d 00:00:00', strtotime("-{$days} days"));
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

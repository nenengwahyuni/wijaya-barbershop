<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Loyalty extends Model
{
    protected static string $table = 'user_loyalty';

    public static function getOrCreate(int $userId): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT ul.*, u.fullname, u.username, u.email, u.phone 
                               FROM user_loyalty ul 
                               JOIN users u ON ul.user_id = u.id 
                               WHERE ul.user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $res = $stmt->fetch();

        if ($res) {
            $res['total_spend'] = $res['total_spent'] ?? 0;
            $res['benefits'] = self::getTierBenefits($res['tier'] ?? 'bronze');
            try {
                $visitStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status IN ('completed', 'settlement', 'paid')");
                $visitStmt->execute([$userId]);
                $res['visit_count'] = (int) $visitStmt->fetchColumn();
            } catch (\Throwable $e) {
                $res['visit_count'] = 0;
            }
            return $res;
        }

        // Buat record baru dengan 25 bonus poin selamat datang
        $welcomePoints = 25;
        $insert = $pdo->prepare("INSERT INTO user_loyalty (user_id, points, lifetime_points, tier, total_spent) VALUES (?, ?, ?, 'bronze', 0)");
        $insert->execute([$userId, $welcomePoints, $welcomePoints]);

        // Catat transaksi poin bonus selamat datang
        self::logTransaction($userId, null, $welcomePoints, 'bonus', 'Bonus Poin Selamat Datang Member Wijaya Barbershop');

        return self::getOrCreate($userId);
    }

    public static function calculateTier(int $totalSpent, int $lifetimePoints): string
    {
        if ($totalSpent >= 25000 || $lifetimePoints >= 25) {
            return 'platinum';
        }
        if ($totalSpent >= 10000 || $lifetimePoints >= 10) {
            return 'gold';
        }
        if ($totalSpent >= 3000 || $lifetimePoints >= 3) {
            return 'silver';
        }
        return 'bronze';
    }

    public static function getTierBenefits(string $tier): array
    {
        $tier = strtolower($tier);
        $tiers = [
            'bronze' => [
                'name' => 'Bronze Member',
                'discount_percent' => 0,
                'point_multiplier' => 1.0,
                'min_spent' => 0,
                'next_tier' => 'Silver',
                'target_spent' => 3000,
                'badge_class' => 'bg-amber-900/30 text-amber-400 border border-amber-600/40',
                'card_bg' => 'from-amber-950 via-slate-900 to-amber-900',
                'accent' => '#b45309',
                'perks' => [
                    'Mendapatkan 1 Poin setiap belanja Rp 1.000',
                    'Akses ke voucher & promo reguler',
                    'Dapat menukarkan poin untuk diskon saat checkout'
                ]
            ],
            'silver' => [
                'name' => 'Silver Member',
                'discount_percent' => 5,
                'point_multiplier' => 1.2,
                'min_spent' => 3000,
                'next_tier' => 'Gold',
                'target_spent' => 10000,
                'badge_class' => 'bg-slate-200/20 text-slate-200 border border-slate-300/40',
                'card_bg' => 'from-slate-800 via-slate-900 to-slate-800',
                'accent' => '#cbd5e1',
                'perks' => [
                    'Diskon Otomatis 5% untuk semua layanan pangkas rambut',
                    'Mendapatkan 1.2x Poin lebih cepat',
                    'Prioritas konfirmasi jadwal reservasi'
                ]
            ],
            'gold' => [
                'name' => 'Gold Member VIP',
                'discount_percent' => 10,
                'point_multiplier' => 1.5,
                'min_spent' => 10000,
                'next_tier' => 'Platinum',
                'target_spent' => 25000,
                'badge_class' => 'bg-amber-500/20 text-amber-300 border border-amber-400/50',
                'card_bg' => 'from-amber-900 via-slate-950 to-yellow-950',
                'accent' => '#f59e0b',
                'perks' => [
                    'Diskon Otomatis 10% untuk semua layanan & produk',
                    'Mendapatkan 1.5x Poin loyalitas',
                    'Gratis konsultasi gaya rambut & styling pomade eksklusif',
                    'Akses promo rahasia hari khusus member Gold'
                ]
            ],
            'platinum' => [
                'name' => 'Platinum Royal VIP',
                'discount_percent' => 15,
                'point_multiplier' => 2.0,
                'min_spent' => 25000,
                'next_tier' => 'Maksimal',
                'target_spent' => 25000,
                'badge_class' => 'bg-emerald-500/25 text-emerald-300 border border-emerald-400/60 shadow-emerald-500/20',
                'card_bg' => 'from-emerald-950 via-slate-950 to-teal-950',
                'accent' => '#10b981',
                'perks' => [
                    'Diskon Otomatis 15% tanpa batas minimum',
                    'Double Points (2x Poin) setiap transaksi',
                    'Antrean Jalur Khusus (VIP Priority Fast Lane)',
                    'Gratis biaya transport untuk Cukur Panggilan (Home Service)'
                ]
            ]
        ];

        return $tiers[$tier] ?? $tiers['bronze'];
    }

    public static function addPoints(int $userId, int $points, string $desc, ?int $orderId = null): void
    {
        if ($points <= 0) return;
        $pdo = self::getPdo();
        $loyalty = self::getOrCreate($userId);

        $newPoints = $loyalty['points'] + $points;
        $newLifetime = $loyalty['lifetime_points'] + $points;
        $newTier = self::calculateTier($loyalty['total_spent'], $newLifetime);

        $stmt = $pdo->prepare("UPDATE user_loyalty SET points = ?, lifetime_points = ?, tier = ? WHERE user_id = ?");
        $stmt->execute([$newPoints, $newLifetime, $newTier, $userId]);

        self::logTransaction($userId, $orderId, $points, 'earn', $desc);
    }

    public static function redeemPoints(int $userId, int $points, ?int $orderId = null): bool
    {
        if ($points <= 0) return false;
        $pdo = self::getPdo();
        $loyalty = self::getOrCreate($userId);

        if ($loyalty['points'] < $points) {
            return false;
        }

        $newPoints = $loyalty['points'] - $points;
        $stmt = $pdo->prepare("UPDATE user_loyalty SET points = ? WHERE user_id = ?");
        $stmt->execute([$newPoints, $userId]);

        $potonganRp = $points * 50; // 1 poin = Rp 50
        $desc = "Penukaran {$points} Poin Loyalitas (Diskon Rp " . number_format($potonganRp, 0, ',', '.') . ")";
        self::logTransaction($userId, $orderId, -$points, 'redeem', $desc);

        return true;
    }

    public static function recordOrderSpend(int $userId, int $paidAmount, ?int $orderId = null): array
    {
        $pdo = self::getPdo();
        $loyalty = self::getOrCreate($userId);

        $newTotalSpent = $loyalty['total_spent'] + $paidAmount;
        $benefits = self::getTierBenefits($loyalty['tier']);

        // Base poin: 1 poin per Rp 1.000 dikalikan multiplier tier
        $basePoints = (int) floor($paidAmount / 1000);
        $earnedPoints = (int) round($basePoints * $benefits['point_multiplier']);

        $newLifetime = $loyalty['lifetime_points'] + $earnedPoints;
        $newPoints = $loyalty['points'] + $earnedPoints;
        $newTier = self::calculateTier($newTotalSpent, $newLifetime);

        $stmt = $pdo->prepare("UPDATE user_loyalty SET points = ?, lifetime_points = ?, tier = ?, total_spent = ? WHERE user_id = ?");
        $stmt->execute([$newPoints, $newLifetime, $newTier, $newTotalSpent, $userId]);

        if ($earnedPoints > 0) {
            self::logTransaction($userId, $orderId, $earnedPoints, 'earn', "Poin transaksi belanja senilai Rp " . number_format($paidAmount, 0, ',', '.'));
        }

        return [
            'earned_points' => $earnedPoints,
            'current_points' => $newPoints,
            'current_tier' => $newTier,
            'tier_upgraded' => ($newTier !== $loyalty['tier'])
        ];
    }

    public static function getPointHistory(int $userId, int $limit = 20): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM point_transactions WHERE user_id = ? ORDER BY id ASC");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        $running = 0;
        foreach ($rows as &$r) {
            $running += (int) ($r['points'] ?? 0);
            $r['balance_after'] = $running;
        }
        unset($r);

        $rows = array_reverse($rows);
        if ($limit > 0 && count($rows) > $limit) {
            $rows = array_slice($rows, 0, $limit);
        }

        return $rows;
    }

    public static function getLeaderboard(int $limit = 10): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT ul.*, u.fullname, u.username, u.phone,
                               COALESCE((SELECT COUNT(*) FROM orders o WHERE o.user_id = ul.user_id AND o.status IN ('completed', 'settlement', 'paid')), 0) as visit_count
                               FROM user_loyalty ul
                               JOIN users u ON ul.user_id = u.id
                               WHERE u.deleted_at IS NULL 
                                 AND u.role_id = 4 
                                 AND u.username NOT LIKE '%neneng%'
                                 AND u.fullname NOT LIKE '%neneng%'
                               ORDER BY ul.lifetime_points DESC, ul.total_spent DESC
                               LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();

        foreach ($res as &$row) {
            $row['total_spend'] = $row['total_spent'] ?? 0;
            $row['visit_count'] = (int) ($row['visit_count'] ?? 0);
            $row['benefits'] = self::getTierBenefits($row['tier'] ?? 'bronze');
        }
        return $res;
    }

    private static function logTransaction(int $userId, ?int $orderId, int $points, string $type, string $description): void
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("INSERT INTO point_transactions (user_id, order_id, points, type, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $orderId, $points, $type, $description]);
    }
}

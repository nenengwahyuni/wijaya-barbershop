<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Voucher extends Model
{
    protected static string $table = 'vouchers';

    public static function getActiveVouchers(?string $serviceType = null): array
    {
        $pdo = self::getPdo();
        $now = date('Y-m-d H:i:s');
        
        $sql = "SELECT * FROM vouchers 
                WHERE is_active = 1 
                  AND deleted_at IS NULL
                  AND (expires_at IS NULL OR expires_at > ?)
                  AND used_count < usage_limit";
        
        $params = [$now];
        if ($serviceType && $serviceType !== 'all') {
            $sql .= " AND (service_type = 'all' OR service_type = ?)";
            $params[] = $serviceType;
        }

        $sql .= " ORDER BY discount_value DESC, id ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findByCode(string $code): ?array
    {
        $pdo = self::getPdo();
        $code = strtoupper(trim($code));
        $sql = "SELECT * FROM vouchers WHERE UPPER(code) = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function validateVoucher(string $code, int $subtotal, ?int $userId = null, string $serviceType = 'barbershop'): array
    {
        $code = strtoupper(trim($code));
        if (empty($code)) {
            return ['valid' => false, 'message' => 'Silakan masukkan kode voucher.'];
        }

        $voucher = self::findByCode($code);
        if (!$voucher) {
            return ['valid' => false, 'message' => "Kode voucher '{$code}' tidak ditemukan."];
        }

        if (!$voucher['is_active']) {
            return ['valid' => false, 'message' => "Voucher '{$code}' sudah tidak aktif."];
        }

        if (!empty($voucher['expires_at']) && strtotime($voucher['expires_at']) < time()) {
            return ['valid' => false, 'message' => "Masa berlaku voucher '{$code}' telah berakhir."];
        }

        if ($voucher['used_count'] >= $voucher['usage_limit']) {
            return ['valid' => false, 'message' => "Kuota pemakaian voucher '{$code}' telah habis."];
        }

        if (!empty($voucher['service_type']) && $voucher['service_type'] !== 'all' && $voucher['service_type'] !== $serviceType) {
            $serviceName = ($voucher['service_type'] === 'home_service') ? 'Cukur Panggilan (Home Service)' : 'Pangkas di Studio';
            return ['valid' => false, 'message' => "Voucher '{$code}' hanya berlaku untuk layanan {$serviceName}."];
        }

        if ($subtotal < (int)$voucher['min_purchase']) {
            $minRp = number_format($voucher['min_purchase'], 0, ',', '.');
            return [
                'valid' => false, 
                'message' => "Voucher '{$code}' membutuhkan minimal transaksi sebesar Rp {$minRp}."
            ];
        }

        // Hitung nilai diskon
        $discount = 0;
        if ($voucher['discount_type'] === 'percentage') {
            $discount = (int) round(($subtotal * (int)$voucher['discount_value']) / 100);
            if (!empty($voucher['max_discount']) && $discount > (int)$voucher['max_discount']) {
                $discount = (int)$voucher['max_discount'];
            }
        } else {
            $discount = (int)$voucher['discount_value'];
        }

        // Diskon tidak boleh melebihi subtotal
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        return [
            'valid' => true,
            'id' => (int)$voucher['id'],
            'code' => $voucher['code'],
            'title' => $voucher['title'],
            'discount_type' => $voucher['discount_type'],
            'discount_value' => (int)$voucher['discount_value'],
            'discount' => $discount,
            'discount_formatted' => 'Rp ' . number_format($discount, 0, ',', '.'),
            'message' => "Voucher '{$voucher['code']}' berhasil dipasang! Anda hemat Rp " . number_format($discount, 0, ',', '.') . "."
        ];
    }

    public static function incrementUsage(int $id): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE vouchers SET used_count = used_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}


<?php

namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected static string $table = 'orders';

    public static function findWithDetails(int $id): ?array
    {
        $pdo = self::getPdo();
        $sql = "SELECT o.*, 
                       COALESCE(NULLIF(o.customer_name, ''), u.fullname, 'Pelanggan') as customer_name, 
                       COALESCE(NULLIF(o.customer_phone, ''), u.phone, '-') as customer_phone, 
                       u.email as customer_email,
                       b.fullname as barber_name, b.phone as barber_phone
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN users b ON o.barber_id = b.id
                WHERE o.id = ? AND o.deleted_at IS NULL
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $order['items'] = OrderItem::getByOrderId($id);
        return $order;
    }

    public static function findByCode(string $code): ?array
    {
        $pdo = self::getPdo();
        $sql = "SELECT o.*, 
                       COALESCE(NULLIF(o.customer_name, ''), u.fullname, 'Pelanggan') as customer_name, 
                       COALESCE(NULLIF(o.customer_phone, ''), u.phone, '-') as customer_phone, 
                       u.email as customer_email,
                       b.fullname as barber_name, b.phone as barber_phone
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN users b ON o.barber_id = b.id
                WHERE o.order_code = ? AND o.deleted_at IS NULL
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $order['items'] = OrderItem::getByOrderId($order['id']);
        return $order;
    }

    public static function allWithCustomer(?string $status = null, ?string $serviceType = null): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT o.*, 
                       COALESCE(NULLIF(o.customer_name, ''), u.fullname, 'Pelanggan') as customer_name, 
                       COALESCE(NULLIF(o.customer_phone, ''), u.phone, '-') as customer_phone,
                       b.fullname as barber_name, b.phone as barber_phone
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN users b ON o.barber_id = b.id
                WHERE o.deleted_at IS NULL";
        
        $params = [];
        if ($status) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }
        if ($serviceType) {
            $sql .= " AND o.service_type = ?";
            $params[] = $serviceType;
        }

        $sql .= " ORDER BY o.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getByUser(int $userId): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT o.*, b.fullname as barber_name, b.phone as barber_phone,
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                FROM orders o
                LEFT JOIN users b ON o.barber_id = b.id
                WHERE o.user_id = ? AND o.deleted_at IS NULL
                ORDER BY o.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function getTodayQueueNumber(): int
    {
        $pdo = self::getPdo();
        // Cek jumlah pesanan hari ini untuk menentukan nomor antrean berikutnya
        $today = date('Y-m-d');
        $sql = "SELECT MAX(queue_number) as max_queue 
                FROM orders 
                WHERE DATE(created_at) = ? AND deleted_at IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$today]);
        $row = $stmt->fetch();
        return ($row['max_queue'] ?? 0) + 1;
    }
}


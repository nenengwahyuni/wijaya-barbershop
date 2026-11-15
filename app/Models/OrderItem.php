<?php

namespace App\Models;

use App\Core\Model;

class OrderItem extends Model
{
    protected static string $table = 'order_items';

    public static function getByOrderId(int $orderId): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT oi.*, i.item_name, i.image_path, c.category_name 
                FROM order_items oi
                JOIN items i ON oi.item_id = i.id
                JOIN categories c ON i.category_id = c.id
                WHERE oi.order_id = ? AND oi.deleted_at IS NULL
                ORDER BY oi.id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}


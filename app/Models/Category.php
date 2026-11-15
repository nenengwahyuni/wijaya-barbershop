<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function withItemsCount(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT c.*, COUNT(i.id) as total_items
                FROM categories c
                LEFT JOIN items i ON c.id = i.category_id AND i.deleted_at IS NULL AND i.is_active = 1
                WHERE c.deleted_at IS NULL
                GROUP BY c.id
                ORDER BY c.id ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }
}


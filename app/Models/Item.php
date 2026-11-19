<?php

namespace App\Models;

use App\Core\Model;

class Item extends Model
{
    protected static string $table = 'items';

    public static function allActiveWithCategory(?int $categoryId = null): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT i.*, c.category_name 
                FROM items i
                JOIN categories c ON i.category_id = c.id
                WHERE i.deleted_at IS NULL AND i.is_active = 1";
        
        $params = [];
        if ($categoryId) {
            $sql .= " AND i.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY c.id ASC, i.id ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function allWithCategory(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT i.*, c.category_name 
                FROM items i
                JOIN categories c ON i.category_id = c.id
                WHERE i.deleted_at IS NULL
                ORDER BY i.id DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function getImageUrl(?string $imagePath = null, ?string $itemName = null): string
    {
        // 1. Jika ada file fisik lokal di folder public/uploads/
        if (!empty($imagePath)) {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                return $imagePath;
            }
            $localFile = dirname(__DIR__, 2) . '/public/uploads/' . $imagePath;
            if (file_exists($localFile) && is_file($localFile)) {
                $mtime = @filemtime($localFile) ?: 'hd2';
                return '/uploads/' . $imagePath . '?v=' . $mtime;
            }
        }

        // 2. Mapping spesifik berdasarkan nama layanan & produk (HD Unsplash Curated)
        $name = strtolower(trim((string)$itemName));
        if (str_contains($name, 'anak') || str_contains($name, 'balita') || str_contains($name, 'kid')) {
            return 'https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'lengkap') || str_contains($name, 'komplit') || str_contains($name, 'paket') || str_contains($name, 'premium') || str_contains($name, 'massage') || str_contains($name, 'eksklusif')) {
            return 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'dewasa') || str_contains($name, 'regular') || str_contains($name, 'potong') || str_contains($name, 'pangkas') || str_contains($name, 'haircut') || str_contains($name, 'fade')) {
            return 'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'tattoo') || str_contains($name, 'tato') || str_contains($name, 'ukir')) {
            return 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'shave') || str_contains($name, 'cukur') || str_contains($name, 'jenggot') || str_contains($name, 'kumis')) {
            return 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'color') || str_contains($name, 'semir') || str_contains($name, 'cat') || str_contains($name, 'pewarnaan')) {
            return 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'cream') || str_contains($name, 'creambath') || str_contains($name, 'spa') || str_contains($name, 'cuci') || str_contains($name, 'keramas')) {
            return 'https://images.unsplash.com/photo-1519699047748-de8e457a634e?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'pomade') || str_contains($name, 'waterbased') || str_contains($name, 'minyak')) {
            return 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'clay') || str_contains($name, 'matte')) {
            return 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=1200&q=90';
        }
        if (str_contains($name, 'tonic') || str_contains($name, 'tonik') || str_contains($name, 'ginseng') || str_contains($name, 'vitamin')) {
            return 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=1200&q=90';
        }

        // Default fallback foto barbershop modern
        return 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?auto=format&fit=crop&w=1200&q=90';
    }
}


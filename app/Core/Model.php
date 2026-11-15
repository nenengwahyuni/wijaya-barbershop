<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function getPdo(): PDO
    {
        return Database::getConnection();
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        $pdo = self::getPdo();
        $table = static::$table;
        $sql = "SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY {$orderBy}";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function find($id): ?array
    {
        $pdo = self::getPdo();
        $table = static::$table;
        $pk = static::$primaryKey;
        $sql = "SELECT * FROM {$table} WHERE {$pk} = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function where(string $column, $value, string $operator = '='): array
    {
        $pdo = self::getPdo();
        $table = static::$table;
        $sql = "SELECT * FROM {$table} WHERE {$column} {$operator} ? AND deleted_at IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public static function whereFirst(string $column, $value, string $operator = '='): ?array
    {
        $results = self::where($column, $value, $operator);
        return $results[0] ?? null;
    }

    public static function create(array $data): int
    {
        $pdo = self::getPdo();
        $table = static::$table;

        $columns = implode(', ', array_map(fn($col) => "`{$col}`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));

        $newId = (int) $pdo->lastInsertId();

        // Simpan order terbaru ke session cookie agar persisten di Vercel Serverless
        if ($table === 'orders' && session_status() === PHP_SESSION_ACTIVE) {
            $row = $data;
            $row['id'] = $newId;
            $row['created_at'] = $row['created_at'] ?? date('Y-m-d H:i:s');
            $_SESSION['serverless_orders'][$newId] = $row;
            if (count($_SESSION['serverless_orders']) > 3) {
                $_SESSION['serverless_orders'] = array_slice($_SESSION['serverless_orders'], -3, 3, true);
            }
            Auth::syncCookieSession();
        } elseif ($table === 'order_items' && session_status() === PHP_SESSION_ACTIVE) {
            $oid = (int) ($data['order_id'] ?? 0);
            if ($oid > 0 && isset($_SESSION['serverless_orders'][$oid])) {
                $itemRow = $data;
                $itemRow['id'] = $newId;
                $_SESSION['serverless_orders'][$oid]['_items'][] = $itemRow;
                Auth::syncCookieSession();
            }
        }

        return $newId;
    }

    public static function update($id, array $data): bool
    {
        $pdo = self::getPdo();
        $table = static::$table;
        $pk = static::$primaryKey;

        $setClause = implode(', ', array_map(fn($col) => "`{$col}` = ?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$setClause}, updated_at = CURRENT_TIMESTAMP WHERE {$pk} = ?";
        
        $params = array_values($data);
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute($params);

        if ($table === 'orders' && session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['serverless_orders'][(int)$id])) {
            foreach ($data as $k => $v) {
                $_SESSION['serverless_orders'][(int)$id][$k] = $v;
            }
            Auth::syncCookieSession();
        }

        return $res;
    }

    public static function delete($id, bool $soft = true): bool
    {
        $pdo = self::getPdo();
        $table = static::$table;
        $pk = static::$primaryKey;

        if ($soft) {
            $sql = "UPDATE {$table} SET deleted_at = CURRENT_TIMESTAMP WHERE {$pk} = ?";
        } else {
            $sql = "DELETE FROM {$table} WHERE {$pk} = ?";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}


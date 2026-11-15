<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByUsernameOrEmail(string $login): ?array
    {
        $pdo = self::getPdo();
        $loginTrimmed = trim($login);
        $sql = "SELECT u.*, r.role_name 
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE (LOWER(u.username) = LOWER(?) OR LOWER(u.email) = LOWER(?)) AND u.deleted_at IS NULL
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$loginTrimmed, $loginTrimmed]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function allWithRoles(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT u.*, r.role_name, r.description as role_description 
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.deleted_at IS NULL
                ORDER BY u.id ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function getBarbersOnDuty(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT u.* 
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE r.role_name = 'barber' AND u.on_duty = 1 AND u.deleted_at IS NULL
                ORDER BY u.fullname ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }
}


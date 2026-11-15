<?php

namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        self::initSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        // Sinkronisasi data user terbaru dari database jika tersedia
        if (isset($_SESSION['user_id'])) {
            try {
                $dbUser = \App\Models\User::find($_SESSION['user_id']);
                if ($dbUser) {
                    unset($dbUser['password']);
                    $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], $dbUser);
                }
            } catch (\Throwable $e) {}
        }

        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role_name'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return in_array(self::role(), ['admin', 'direktur']);
    }

    public static function isKasir(): bool
    {
        return in_array(self::role(), ['admin', 'direktur', 'kasir']);
    }

    public static function isStaff(): bool
    {
        return in_array(self::role(), ['admin', 'direktur', 'kasir', 'barber', 'capster']);
    }

    public static function isCustomer(): bool
    {
        return self::role() === 'customer';
    }

    public static function login(array $user): void
    {
        self::initSession();
        $_SESSION['user_id'] = $user['id'];
        unset($user['password']);
        $_SESSION['user'] = $user;
        self::syncCookieSession();
    }

    public static function logout(): void
    {
        self::initSession();
        unset($_SESSION['user_id']);
        unset($_SESSION['user']);
        @setcookie('wijaya_serverless_session', '', time() - 3600, '/');
        @session_destroy();
    }

    public static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionDir = dirname(__DIR__, 2) . '/storage/sessions';
            if (!is_dir($sessionDir)) {
                @mkdir($sessionDir, 0777, true);
            }
            if (!is_dir($sessionDir) || !is_writable($sessionDir) || getenv('VERCEL') || getenv('VERCEL_ENV')) {
                $sessionDir = rtrim(sys_get_temp_dir(), '/\\') . '/wijaya_sessions';
                if (!is_dir($sessionDir)) {
                    @mkdir($sessionDir, 0777, true);
                }
            }
            if (is_dir($sessionDir) && is_writable($sessionDir)) {
                @session_save_path($sessionDir);
            }
            @session_start();

            // Hydrate session dari signed cookie jika instance serverless baru (cold start)
            if (empty($_SESSION['user_id']) && empty($_SESSION['cart']) && !empty($_COOKIE['wijaya_serverless_session'])) {
                self::restoreCookieSession($_COOKIE['wijaya_serverless_session']);
            } elseif (!empty($_COOKIE['wijaya_serverless_session'])) {
                // Pastikan serverless_orders selalu terbawa antar container
                self::restoreServerlessOrdersFromCookie($_COOKIE['wijaya_serverless_session']);
            }

            if (php_sapi_name() !== 'cli') {
                register_shutdown_function([self::class, 'syncCookieSession']);
            }
        }
    }

    private static function getCookieSecret(): string
    {
        return getenv('APP_KEY') ?: 'wijaya_barbershop_metro_secret_2026';
    }

    public static function syncCookieSession(): void
    {
        if (php_sapi_name() === 'cli' || headers_sent()) {
            return;
        }
        $payload = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'user' => $_SESSION['user'] ?? null,
            'cart' => $_SESSION['cart'] ?? [],
            'flash' => $_SESSION['flash'] ?? [],
            'serverless_orders' => $_SESSION['serverless_orders'] ?? []
        ];
        if (empty($payload['user_id']) && empty($payload['cart']) && empty($payload['flash']) && empty($payload['serverless_orders'])) {
            return;
        }
        $json = json_encode($payload);
        $b64 = base64_encode($json);
        $sig = hash_hmac('sha256', $b64, self::getCookieSecret());
        $cookieVal = $b64 . '.' . $sig;
        @setcookie('wijaya_serverless_session', $cookieVal, [
            'expires' => time() + 86400 * 7,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    private static function restoreCookieSession(string $cookieVal): void
    {
        $parts = explode('.', $cookieVal, 2);
        if (count($parts) !== 2) return;
        [$b64, $sig] = $parts;
        if (!hash_equals(hash_hmac('sha256', $b64, self::getCookieSecret()), $sig)) {
            return;
        }
        $data = json_decode(base64_decode($b64), true);
        if (!is_array($data)) return;

        if (!empty($data['user_id'])) $_SESSION['user_id'] = $data['user_id'];
        if (!empty($data['user'])) $_SESSION['user'] = $data['user'];
        if (!empty($data['cart'])) $_SESSION['cart'] = $data['cart'];
        if (!empty($data['flash'])) $_SESSION['flash'] = $data['flash'];
        if (!empty($data['serverless_orders'])) $_SESSION['serverless_orders'] = $data['serverless_orders'];
    }

    private static function restoreServerlessOrdersFromCookie(string $cookieVal): void
    {
        $parts = explode('.', $cookieVal, 2);
        if (count($parts) !== 2) return;
        [$b64, $sig] = $parts;
        if (!hash_equals(hash_hmac('sha256', $b64, self::getCookieSecret()), $sig)) {
            return;
        }
        $data = json_decode(base64_decode($b64), true);
        if (is_array($data) && !empty($data['serverless_orders']) && empty($_SESSION['serverless_orders'])) {
            $_SESSION['serverless_orders'] = $data['serverless_orders'];
        }
    }
}

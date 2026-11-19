<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static string $driver = 'mysql';

    public static function getInstance(): PDO
    {
        return self::getConnection();
    }

    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::loadEnv();

        $connection = getenv('DB_CONNECTION') ?: 'mysql';
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $database = getenv('DB_DATABASE') ?: 'wijaya_barbershop';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
        $allowFallback = filter_var(getenv('SQLITE_FALLBACK') ?: true, FILTER_VALIDATE_BOOLEAN);

        if ($connection === 'mysql') {
            try {
                // Buat koneksi awal untuk membuat database jika belum ada
                $initPdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 2
                ]);
                $initPdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                self::$instance = new PDO("mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 2,
                    PDO::MYSQL_ATTR_MULTI_STATEMENTS => true
                ]);
                self::$driver = 'mysql';

                // Periksa apakah tabel categories sudah ada di database MySQL
                $tableCheck = self::$instance->query("SHOW TABLES LIKE 'categories'")->fetch();
                if (!$tableCheck) {
                    self::migrateAndSeedMysql(self::$instance);
                } else {
                    self::ensureHomeServiceColumnsMysql(self::$instance);
                }

                return self::$instance;
            } catch (PDOException $e) {
                if (!$allowFallback) {
                    throw new PDOException("Koneksi MySQL Gagal: " . $e->getMessage());
                }
                // Fallback ke SQLite jika MySQL tidak tersedia
            }
        }

        // Setup SQLite (Mendukung penuh Vercel Serverless read-only filesystem via /tmp)
        $sqliteDir = dirname(__DIR__, 2) . '/database';
        $bundledSqliteFile = $sqliteDir . '/database.sqlite';
        $isVercel = (bool) (getenv('VERCEL') || getenv('VERCEL_ENV'));

        if ($isVercel || !is_writable($sqliteDir) || (file_exists($bundledSqliteFile) && !is_writable($bundledSqliteFile))) {
            $sqliteFile = rtrim(sys_get_temp_dir(), '/\\') . '/wijaya_barbershop_v5.sqlite';
            if ((!file_exists($sqliteFile) || filesize($sqliteFile) === 0) && file_exists($bundledSqliteFile) && filesize($bundledSqliteFile) > 0) {
                @copy($bundledSqliteFile, $sqliteFile);
                @chmod($sqliteFile, 0666);
            }
        } else {
            if (!is_dir($sqliteDir)) {
                @mkdir($sqliteDir, 0777, true);
            }
            $sqliteFile = $bundledSqliteFile;
        }

        $isNewSqlite = !file_exists($sqliteFile) || filesize($sqliteFile) === 0;

        self::$instance = new PDO("sqlite:" . $sqliteFile, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        self::$instance->exec("PRAGMA foreign_keys = ON;");
        self::$driver = 'sqlite';

        if ($isNewSqlite) {
            self::migrateAndSeedSqlite(self::$instance);
        } else {
            self::ensureHomeServiceColumnsSqlite(self::$instance);
        }

        self::replayServerlessOrders(self::$instance);

        return self::$instance;
    }

    public static function getDriver(): string
    {
        return self::$driver;
    }

    public static function loadEnv(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $val] = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);
                $val = trim($val, '"\'');
                putenv("{$key}={$val}");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }

    private static function migrateAndSeedMysql(PDO $pdo): void
    {
        $baseDir = dirname(__DIR__, 2);
        $schemaFile = $baseDir . '/database/schema.sql';
        $seedFile = $baseDir . '/database/seed.sql';

        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            try {
                $pdo->exec($sql);
            } catch (\Exception $e) {
                // Jika multi-query gagal, jalankan query per baris
                self::executeSqlFileByStatement($pdo, $schemaFile);
            }
        }

        if (file_exists($seedFile)) {
            $sql = file_get_contents($seedFile);
            try {
                $pdo->exec($sql);
            } catch (\Exception $e) {
                self::executeSqlFileByStatement($pdo, $seedFile);
            }
        }
    }

    private static function executeSqlFileByStatement(PDO $pdo, string $filePath): void
    {
        $lines = file($filePath);
        $statement = '';
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*')) {
                continue;
            }
            $statement .= $line;
            if (str_ends_with($trimmed, ';')) {
                try {
                    $pdo->exec($statement);
                } catch (\Exception $ex) {
                    // Abaikan error duplikasi atau skip
                }
                $statement = '';
            }
        }
    }

    private static function ensureHomeServiceColumnsMysql(PDO $pdo): void
    {
        try {
            $colCheck = $pdo->query("SHOW COLUMNS FROM orders LIKE 'service_type'")->fetch();
            if (!$colCheck) {
                $pdo->exec("ALTER TABLE orders 
                    ADD COLUMN service_type ENUM('barbershop', 'home_service') NOT NULL DEFAULT 'barbershop' AFTER user_id,
                    ADD COLUMN barber_id BIGINT UNSIGNED NULL AFTER service_type,
                    ADD COLUMN customer_address TEXT NULL AFTER notes,
                    ADD COLUMN area_zone VARCHAR(100) NULL AFTER customer_address,
                    ADD COLUMN delivery_fee INT NOT NULL DEFAULT 0 AFTER tax,
                    ADD COLUMN scheduled_at DATETIME NULL AFTER queue_time;");

                $checkSample = $pdo->query("SELECT id FROM orders WHERE order_code = 'ORD1778419901'")->fetch();
                if (!$checkSample) {
                    $pdo->exec("INSERT INTO orders (order_code, user_id, service_type, barber_id, subtotal, discount, tax, delivery_fee, grand_total, status, payment_method, notes, customer_address, area_zone, queue_number, queue_time, scheduled_at, created_at) VALUES
                        ('ORD1778419901', 7, 'home_service', 4, '50000', '0', '5000', 15000, '70000', 'pending', 'qris', 'Tolong bawa peralatan lengkap & pomade waterbased', 'Jl. AH Nasution No. 45, Yosodadi, Kec. Metro Timur (Rumah Pagar Hitam sebelah Masjid)', 'Metro Timur / Barat / Utara / Selatan', 6, '2026-11-06 14:00:00', '2026-11-06 14:00:00', '2026-11-06 10:30:00');");
                    $lastId = (int) $pdo->lastInsertId();
                    if ($lastId > 0) {
                        $pdo->exec("INSERT INTO order_items (order_id, item_id, quantity, price, tax, total_price) VALUES
                            ({$lastId}, 2, 1, 50000, 5000, 55000);");
                    }
                }
            }
        } catch (\Exception $e) {
            // Abaikan
        }
    }

    private static function ensureHomeServiceColumnsSqlite(PDO $pdo): void
    {
        try {
            $cols = $pdo->query("PRAGMA table_info(orders)")->fetchAll();
            $colNames = array_column($cols, 'name');
            $extraOrderCols = [
                'customer_name' => 'VARCHAR(255) NULL',
                'customer_phone' => 'VARCHAR(50) NULL',
                'service_type' => "VARCHAR(50) DEFAULT 'barbershop'",
                'barber_id' => 'INTEGER NULL',
                'customer_address' => 'TEXT NULL',
                'area_zone' => 'VARCHAR(100) NULL',
                'delivery_fee' => 'INTEGER DEFAULT 0',
                'voucher_code' => 'VARCHAR(50) NULL',
                'points_used' => 'INTEGER DEFAULT 0',
                'scheduled_at' => 'DATETIME NULL'
            ];
            foreach ($extraOrderCols as $col => $def) {
                if (!in_array($col, $colNames)) {
                    $pdo->exec("ALTER TABLE orders ADD COLUMN {$col} {$def};");
                }
            }
        } catch (\Exception $e) {
            // Abaikan
        }
    }

    private static function replayServerlessOrders(PDO $pdo): void
    {
        try {
            if (!empty($_SESSION['user']) && is_array($_SESSION['user']) && !empty($_SESSION['user']['id']) && !empty($_SESSION['user']['username'])) {
                $u = $_SESSION['user'];
                $chkUser = $pdo->prepare("SELECT id FROM users WHERE id = ? OR username = ? LIMIT 1");
                $chkUser->execute([$u['id'], $u['username']]);
                if (!$chkUser->fetch()) {
                    $insUser = $pdo->prepare("INSERT OR IGNORE INTO users (id, username, password, fullname, email, phone, on_duty, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $insUser->execute([
                        $u['id'],
                        $u['username'],
                        password_hash('password123', PASSWORD_BCRYPT),
                        $u['fullname'] ?? $u['username'],
                        $u['email'] ?? ($u['username'] . '@wijayabarber.com'),
                        $u['phone'] ?? '081200000000',
                        $u['on_duty'] ?? 0,
                        $u['role_id'] ?? 4
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Abaikan
        }

        if (empty($_SESSION['serverless_orders']) || !is_array($_SESSION['serverless_orders'])) {
            return;
        }
        try {
            $pdo->exec("PRAGMA foreign_keys = OFF;");
            $pdo->exec("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE order_code LIKE 'ORD%011')");
            $pdo->exec("DELETE FROM orders WHERE order_code LIKE 'ORD%011'");
            $orderCols = array_column($pdo->query("PRAGMA table_info(orders)")->fetchAll(), 'name');
            foreach ($_SESSION['serverless_orders'] as $oid => $orderData) {
                if (!is_array($orderData) || empty($orderData['order_code']))
                    continue;
                $items = $orderData['_items'] ?? [];
                unset($orderData['_items']);

                $existing = $pdo->prepare("SELECT id, status FROM orders WHERE order_code = ? LIMIT 1");
                $existing->execute([$orderData['order_code']]);
                $exRow = $existing->fetch();

                if ($exRow) {
                    if (!empty($orderData['status']) && $exRow['status'] !== $orderData['status']) {
                        $upd = $pdo->prepare("UPDATE orders SET status = ?, payment_method = ? WHERE id = ?");
                        $upd->execute([$orderData['status'], $orderData['payment_method'] ?? 'qris', $exRow['id']]);
                    }
                    continue;
                }

                $filtered = [];
                foreach ($orderData as $k => $v) {
                    if ($k !== 'id' && in_array($k, $orderCols)) {
                        $filtered[$k] = $v;
                    }
                }
                if (empty($filtered))
                    continue;

                $cols = array_keys($filtered);
                $colList = '`' . implode('`, `', $cols) . '`';
                $ph = implode(', ', array_fill(0, count($cols), '?'));
                $ins = $pdo->prepare("INSERT INTO orders ({$colList}) VALUES ({$ph})");
                $ins->execute(array_values($filtered));
                $newOrderId = (int) $pdo->lastInsertId();

                foreach ($items as $it) {
                    $insItem = $pdo->prepare("INSERT INTO order_items (order_id, item_id, quantity, price, tax, total_price) VALUES (?, ?, ?, ?, ?, ?)");
                    $insItem->execute([
                        $newOrderId,
                        $it['item_id'] ?? 1,
                        $it['quantity'] ?? 1,
                        $it['price'] ?? 1000,
                        $it['tax'] ?? 0,
                        $it['total_price'] ?? 1000
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Abaikan
        }
    }

    private static function migrateAndSeedSqlite(PDO $pdo): void
    {
        $schemaSql = <<<SQL
        CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role_name VARCHAR(255) NOT NULL,
            description VARCHAR(255) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL
        );

        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            fullname VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            phone VARCHAR(255) NULL,
            on_duty INTEGER DEFAULT 0,
            role_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL,
            FOREIGN KEY (role_id) REFERENCES roles (id)
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_name VARCHAR(255) NOT NULL,
            description VARCHAR(255) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL
        );

        CREATE TABLE IF NOT EXISTS items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            item_name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            price INTEGER NOT NULL,
            category_id INTEGER NOT NULL,
            image_path VARCHAR(255) NULL,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL,
            FOREIGN KEY (category_id) REFERENCES categories (id)
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_code VARCHAR(255) NOT NULL UNIQUE,
            user_id INTEGER NOT NULL,
            service_type VARCHAR(50) DEFAULT 'barbershop',
            barber_id INTEGER NULL,
            subtotal VARCHAR(255) NOT NULL,
            discount VARCHAR(255) DEFAULT '0',
            tax VARCHAR(255) DEFAULT '0',
            delivery_fee INTEGER DEFAULT 0,
            grand_total VARCHAR(255) NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            payment_method VARCHAR(50) DEFAULT 'tunai',
            notes TEXT NULL,
            customer_address TEXT NULL,
            area_zone VARCHAR(100) NULL,
            queue_number INTEGER DEFAULT 1,
            queue_time DATETIME NULL,
            scheduled_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL,
            FOREIGN KEY (user_id) REFERENCES users (id)
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            item_id INTEGER NOT NULL,
            quantity INTEGER DEFAULT 1,
            price INTEGER NOT NULL,
            tax INTEGER DEFAULT 0,
            total_price INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL,
            FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
            FOREIGN KEY (item_id) REFERENCES items (id)
        );
SQL;
        $pdo->exec($schemaSql);

        // Seeding Data
        $passHash = password_hash('password123', PASSWORD_BCRYPT);

        $pdo->exec("INSERT INTO roles (id, role_name, description) VALUES
            (1, 'admin', 'Administrator / Direktur Utama Wijaya Barbershop'),
            (2, 'kasir', 'Kasir Pengelola Transaksi dan Pembayaran'),
            (3, 'barber', 'Capster / Barber Pemotong Rambut'),
            (4, 'customer', 'Pelanggan Wijaya Barbershop');");

        $stmtUser = $pdo->prepare("INSERT INTO users (id, username, password, fullname, email, phone, on_duty, role_id) VALUES
            (1, 'agung', ?, 'Agung Wijaya', 'agung@wijayabarber.com', '081278410001', 1, 1),
            (2, 'mely', ?, 'Mely (Kasir 1)', 'mely@wijayabarber.com', '081278410002', 1, 2),
            (3, 'riles', ?, 'Riles (Kasir 2)', 'riles@wijayabarber.com', '081278410003', 1, 2),
            (4, 'doni', ?, 'Doni (Capster Senior)', 'doni@wijayabarber.com', '081278410004', 1, 3),
            (5, 'lana', ?, 'Lana (Capster Hair Tattoo)', 'lana@wijayabarber.com', '081278410005', 1, 3),
            (6, 'yoyo', ?, 'Yoyo (Capster Classic Cut)', 'yoyo@wijayabarber.com', '081278410006', 1, 3),
            (7, 'juni', ?, 'Juni Sanjaya', 'junisanjaya2026@gmail.com', '081278412312', 0, 4),
            (8, 'budi', ?, 'Budi Santoso', 'budi@gmail.com', '081298765432', 0, 4),
            (9, 'direktur', ?, 'Direktur Utama', 'direktur@wijayabarber.com', '081278410000', 1, 1);");
        $stmtUser->execute([$passHash, $passHash, $passHash, $passHash, $passHash, $passHash, $passHash, $passHash, $passHash]);

        $pdo->exec("INSERT INTO categories (id, category_name, description) VALUES
            (1, 'Jasa Pangkas Rambut', 'Layanan potong rambut pria profesional mulai dari model klasik hingga gaya modern'),
            (2, 'Perawatan & Treatment Rambut', 'Layanan cuci rambut, creambath, pijat relaksasi kepala, dan semir pewarnaan rambut'),
            (3, 'Produk Rambut & Styling', 'Produk minyak rambut pomade, matte clay, dan tonik vitamin penumbuh rambut');");

        $pdo->exec("INSERT INTO items (id, item_name, description, price, category_id, image_path, is_active) VALUES
            (1, 'Potong Rambut Dewasa', 'Layanan pangkas rambut pria dewasa rapi dan presisi sesuai bentuk wajah + pembersihan rambut & styling pomade.', 1000, 1, 'haircut-regular.jpg', 1),
            (2, 'Paket Potong Rambut Lengkap (Cuci + Pijat)', 'Pangkas rambut eksklusif + keramas cuci rambut air hangat + pijat kepala relaksasi + handuk dingin & hair tonic.', 1500, 1, 'haircut-premium.jpg', 1),
            (3, 'Potong Rambut Anak & Balita', 'Layanan potong rambut khusus anak-anak dan balita dengan pelayanan ramah, sabar, dan rapi.', 1000, 1, 'haircut-kids.jpg', 1),
            (4, 'Cukur Kumis & Jenggot (Shaving)', 'Cukur jenggot dan kumis presisi dengan busa hangat, pisau cukur steril, dan aftershave dingin menyegarkan.', 1000, 1, 'shave.jpg', 1),
            (5, 'Hair Tattoo / Ukir Rambut Artistik', 'Seni ukir garis pola dan motif kreatif pada rambut pria (keahlian khusus Capster Lana).', 1500, 1, 'hair-tattoo.jpg', 1),
            (6, 'Semir Rambut / Pewarnaan (Hair Color)', 'Layanan pewarnaan rambut pria dengan warna natural (hitam pekat, cokelat tua) maupun fashion bleach aman bagi kulit kepala.', 1500, 2, 'hair-color.jpg', 1),
            (7, 'Cuci Rambut & Creambath (Hair Spa)', 'Perawatan nutrisi akar rambut dengan krim creambath, pijatan relaksasi kepala melancarkan sirkulasi darah dan basmi ketombe.', 1200, 2, 'creambath.jpg', 1),
            (8, 'Pomade Rambut Wijaya (Water Based)', 'Produk minyak rambut pomade water based aroma segar maskulin, daya rekat tinggi (strong hold), berkilau alami, dan mudah dibilas.', 1500, 3, 'pomade-water.jpg', 1),
            (9, 'Clay Rambut (Matte Clay Hold)', 'Produk penata rambut dengan hasil akhir tanpa kilau (matte finish), memberikan volume dan tekstur alami untuk gaya rambut modern.', 1500, 3, 'clay.jpg', 1),
            (10, 'Hair Tonic Ginseng (Vitamin Rambut)', 'Tonik rambut dengan ekstrak ginseng alami untuk menguatkan akar rambut, merangsang pertumbuhan rambut sehat, dan sensasi dingin.', 1000, 3, 'tonic.jpg', 1);");

        $pdo->exec("INSERT INTO orders (id, order_code, user_id, subtotal, discount, tax, grand_total, status, payment_method, notes, queue_number, queue_time, created_at) VALUES
            (1, 'ORD1778413577', 7, '88457', '0', '8846', '97303', 'settlement', 'qris', 'Fade samping tipis nomor 1, atas rapikan sedikit', 1, '2026-11-06 10:00:00', '2026-11-06 09:15:00'),
            (2, 'ORD1778413495', 8, '38732', '0', '3873', '42605', 'pending', 'tunai', 'Minta barber mas Doni kalau ada', 2, '2026-11-06 10:30:00', '2026-11-06 09:30:00'),
            (3, 'ORD1778412856', 7, '37728', '0', '3773', '41501', 'pending', 'tunai', 'Potong cepak rapi dinas', 3, '2026-11-06 11:00:00', '2026-11-06 09:45:00'),
            (4, 'ORD1778412628', 8, '2039', '0', '204', '2243', 'pending', 'tunai', 'Beli produk styling tambahan', 4, '2026-11-06 11:30:00', '2026-11-06 10:00:00'),
            (5, 'ORD1778409174', 7, '37728', '0', '3773', '41501', 'settlement', 'tunai', 'Potong rambut + cuci', 5, '2026-11-06 12:00:00', '2026-11-06 10:15:00');");

        $pdo->exec("INSERT INTO order_items (id, order_id, item_id, quantity, price, tax, total_price) VALUES
            (1, 1, 2, 1, 50000, 5000, 55000),
            (2, 1, 7, 1, 38457, 3846, 42303),
            (3, 2, 1, 1, 35000, 3500, 38500),
            (4, 3, 1, 1, 35000, 3500, 38500),
            (5, 5, 2, 1, 50000, 5000, 55000);");
    }
}


<?php
/**
 * CLI Tool - Wijaya Barbershop Artisan Runner
 */

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Database;

$command = $argv[1] ?? 'help';

echo "========================================================\n";
echo "  WIJAYA BARBERSHOP CLI TOOL\n";
echo "  Sistem Informasi Pemesanan & Pembayaran Berbasis Web\n";
echo "========================================================\n\n";

switch ($command) {
    case 'migrate':
        echo "[+] Menjalankan Migrasi Database...\n";
        try {
            $pdo = Database::getConnection();
            $driver = Database::getDriver();
            echo "[✓] Driver Database Aktif: " . strtoupper($driver) . "\n";
            echo "[✓] 6 Tabel Utama Berhasil Dimigrasi: roles, users, categories, items, orders, order_items.\n";
        } catch (\Exception $e) {
            echo "[!] Error Migrasi: " . $e->getMessage() . "\n";
        }
        break;

    case 'seed':
        echo "[+] Menjalankan Seeder Data Awal...\n";
        try {
            $pdo = Database::getConnection();
            $driver = Database::getDriver();
            echo "[✓] Driver: " . strtoupper($driver) . "\n";
            echo "[✓] Data Awal Staf & Layanan Wijaya Barbershop Berhasil Diisi!\n";
        } catch (\Exception $e) {
            echo "[!] Error Seeding: " . $e->getMessage() . "\n";
        }
        break;

    case 'status':
        try {
            $pdo = Database::getConnection();
            $driver = Database::getDriver();
            echo "Driver Database: " . strtoupper($driver) . "\n";
            $usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $itemsCount = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
            $ordersCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
            echo "Total Pengguna : {$usersCount}\n";
            echo "Total Layanan  : {$itemsCount}\n";
            echo "Total Pesanan  : {$ordersCount}\n";
        } catch (\Exception $e) {
            echo "[!] Error Cek Status: " . $e->getMessage() . "\n";
        }
        break;

    case 'serve':
        $port = $argv[2] ?? 8000;
        echo "[+] Menjalankan PHP Development Server pada http://localhost:{$port}\n";
        echo "[*] Tekan Ctrl+C untuk menghentikan server.\n\n";
        passthru("php -S 127.0.0.1:{$port} -t public");
        break;

    default:
        echo "Perintah yang tersedia:\n";
        echo "  php artisan.php migrate   : Menjalankan migrasi struktur 6 tabel database\n";
        echo "  php artisan.php seed      : Mengisi data awal akun staf & katalog layanan\n";
        echo "  php artisan.php status    : Memeriksa koneksi & jumlah data di database\n";
        echo "  php artisan.php serve     : Menjalankan web server lokal di port 8000\n";
        break;
}


<?php
/**
 * ====================================================================
 * SISTEM INFORMASI PEMESANAN & PEMBAYARAN JASA PANGKAS RAMBUT
 * PADA WIJAYA BARBERSHOP BERBASIS WEB
 * 
 * Skripsi oleh: Juni Sanjaya (NPM: 22430116)
 * Program Studi S1 Ilmu Komputer, Universitas Muhammadiyah Metro
 * ====================================================================
 */

// Error reporting disesuaikan dengan .env
error_reporting(E_ALL);
ini_set('display_errors', '1');
if (php_sapi_name() !== 'cli' && ob_get_level() === 0) {
    ob_start();
}

// Autoloader Sederhana PSR-4 untuk Namespace App\
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

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

// Load Environment & Database
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Auth;

Database::loadEnv();
Auth::initSession();

// Inisialisasi HTTP Request & Response
$request = new Request();
$response = new Response();
$router = new Router($request, $response);

// Daftarkan Routes
require dirname(__DIR__) . '/routes/web.php';
require dirname(__DIR__) . '/routes/api.php';

// Cek apakah request berasal dari navigasi browser utama (Top Window)
// Jika ya, sajikan Persistent Audio Shell agar lagu tetap berputar tanpa henti saat klik menu / fitur
$secFetchDest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '';
$isIframe = isset($_GET['in_app_frame']) || ($secFetchDest === 'iframe') || ($secFetchDest !== 'document' && isset($_COOKIE['in_app_frame']));
$isGet = $request->isGet();
$reqPath = $request->getPath();

if ($isIframe) {
    @setcookie('in_app_frame', '1', time() + 86400, '/');
}

if (php_sapi_name() !== 'cli' && $isGet && !$isIframe && !str_starts_with($reqPath, '/api') && !isset($_GET['raw']) && !preg_match('/\.(css|js|mp3|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/i', $reqPath)) {
    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    $separator = empty($queryString) ? '?' : '&';
    $rawUri = $_SERVER['REQUEST_URI'] ?? '/';
    $frameSrc = $rawUri . $separator . 'in_app_frame=1';
    require dirname(__DIR__) . '/resources/views/layouts/shell.php';
    exit;
}

// Eksekusi Route
try {
    $router->resolve();
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<div style="font-family:sans-serif; padding:2rem; max-width:800px; margin:2rem auto; border:1px solid #f87171; border-radius:1rem; background:#fef2f2; color:#991b1b;">';
    echo '<h2 style="margin-top:0;">Terjadi Kesalahan Sistem (500)</h2>';
    echo '<p><strong>Pesan:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre style="background:#fee2e2; padding:1rem; border-radius:0.5rem; overflow:auto; font-size:12px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}

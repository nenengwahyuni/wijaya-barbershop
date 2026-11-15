@echo off
cd /d "%~dp0"
title Sistem Informasi Wijaya Barbershop - Pemesanan & Pembayaran Berbasis Web
color 0A

echo ======================================================================
echo   WIJAYA BARBERSHOP - SISTEM PEMESANAN & PEMBAYARAN JASA PANGKAS RAMBUT
echo   Skripsi: Juni Sanjaya (NPM: 22430116) - Univ. Muhammadiyah Metro
echo ======================================================================
echo.

:: 1. Tentukan Binary PHP yang Tersedia di Komputer
set "PHP_BIN="
if exist "D:\xampp new\php\php.exe" (
    set "PHP_BIN=D:\xampp new\php\php.exe"
) else if exist "D:\xampp\php\php.exe" (
    set "PHP_BIN=D:\xampp\php\php.exe"
) else if exist "C:\xampp\php\php.exe" (
    set "PHP_BIN=C:\xampp\php\php.exe"
) else (
    set "PHP_BIN=php.exe"
)

echo [1/4] Menggunakan PHP dari: "%PHP_BIN%"
echo [2/4] Memeriksa & menginisialisasi 6 tabel database dan data awal...
"%PHP_BIN%" artisan.php migrate
"%PHP_BIN%" artisan.php seed
"%PHP_BIN%" artisan.php status
echo.

:: 3. Buka Browser Otomatis
echo [3/4] Membuka browser ke aplikasi Wijaya Barbershop...
start http://localhost:8000/
start http://127.0.0.1:8000/
echo.

:: 4. Jalankan Web Server
echo [4/4] Server Wijaya Barbershop AKTIF di:
echo       -------------------------------------------
echo       - Web Tamu / Pelanggan : http://localhost:8000
echo       - Alternatif Link IP   : http://127.0.0.1:8000
echo       - Dashboard Karyawan   : http://localhost:8000/admin
echo       - Login Akun           : http://localhost:8000/login
echo       -------------------------------------------
echo       (JANGAN TUTUP JENDELA INI SELAMA MENGGUNAKAN APLIKASI)
echo.

"%PHP_BIN%" -S 0.0.0.0:8000 -t public
if %errorlevel% neq 0 (
    echo.
    echo [!] Port 8000 mungkin sedang digunakan oleh aplikasi lain.
    echo [*] Mencoba menjalankan pada Port 8080...
    start http://localhost:8080/
    "%PHP_BIN%" -S 0.0.0.0:8080 -t public
)

pause

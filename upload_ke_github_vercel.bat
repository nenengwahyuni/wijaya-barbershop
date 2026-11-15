@echo off
cd /d "%~dp0"
title Upload Proyek Wijaya Barbershop ke GitHub & Vercel
color 0B

echo ======================================================================
echo   DEPLOYMENT KE GITHUB ^& VERCEL - WIJAYA BARBERSHOP
echo ======================================================================
echo.
echo Proyek Anda sudah 100%% siap untuk di-hosting di Vercel (PHP Serverless + SQLite).
echo.
echo Langkah 1: Buat repository baru (Kosong) di https://github.com/new
echo            Contoh nama repo: wijaya-barbershop
echo.
set /p REPO_URL="Langkah 2: Paste Link URL GitHub Anda di sini (contoh: https://github.com/username/wijaya-barbershop.git): "

if "%REPO_URL%"=="" (
    echo [!] URL Repository tidak boleh kosong.
    pause
    exit /b
)

echo.
echo [+] Menyiapkan Git & mengunggah kode ke GitHub...
git branch -M main
git remote remove origin 2>nul
git remote add origin %REPO_URL%
git add .
git commit -m "Deploy Wijaya Barbershop Ready for Vercel" 2>nul
git push -u origin main

echo.
echo ======================================================================
echo   SELESAI! Selanjutnya:
echo   1. Buka https://vercel.com/new
echo   2. Klik "Import" pada repository GitHub "wijaya-barbershop"
echo   3. Langsung klik "Deploy" (Semua konfigurasi sudah otomatis!)
echo ======================================================================
start https://vercel.com/new
pause

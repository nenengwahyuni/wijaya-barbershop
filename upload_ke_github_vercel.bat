@echo off
cd /d "%~dp0"
title Upload Proyek Wijaya Barbershop ke GitHub & Vercel
color 0B

echo ======================================================================
echo   UPLOAD OTOMATIS KE GITHUB ^& VERCEL - WIJAYA BARBERSHOP
echo   Repository: https://github.com/nenengwahyuni/wijaya-barbershop.git
echo ======================================================================
echo.
echo [1/3] Menyegarkan sesi login GitHub (jika muncul jendela Sign in with Browser, silakan klik Authorize)...
(
echo protocol=https
echo host=github.com
echo.
) | git credential-manager erase >nul 2>&1

echo [2/3] Mengunggah seluruh kode ke GitHub (nenengwahyuni/wijaya-barbershop)...
git branch -M main
git remote remove origin 2>nul
git remote add origin https://github.com/nenengwahyuni/wijaya-barbershop.git
git add .
git commit -m "Deploy Wijaya Barbershop Ready for Vercel" 2>nul
git push -u origin main

if %errorlevel% equ 0 (
    echo.
    echo ======================================================================
    echo   [BERHASIL!] Kode sudah masuk ke GitHub Anda!
    echo   Membuka halaman Vercel untuk Import ^& Deploy...
    echo   1. Di halaman Vercel yang terbuka, klik tombol "Import" di sebelah "wijaya-barbershop"
    echo   2. Langsung klik "Deploy"
    echo ======================================================================
    start https://vercel.com/new
) else (
    echo.
    echo [!] Gagal mengunggah otomatis. Pastikan Anda sudah menekan tombol "Sign in with your browser" yang muncul.
)
pause

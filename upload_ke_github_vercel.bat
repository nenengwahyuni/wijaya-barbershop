@echo off
cd /d "%~dp0"
title Update Otomatis Website Vercel - Wijaya Barbershop
color 0B

echo ======================================================================
echo   UPDATE OTOMATIS KE GITHUB ^& VERCEL - WIJAYA BARBERSHOP
echo   Live URL: https://wijaya-barbershop-app.vercel.app
echo ======================================================================
echo.
echo [1/2] Menyimpan perubahan terbaru...
git branch -M main
git add .
git commit -m "Fix logout and add 1-click account switcher for Vercel" 2>nul

echo [2/2] Mengirim update ke GitHub ^& Vercel...
git remote remove origin 2>nul
git remote add origin https://github.com/nenengwahyuni/wijaya-barbershop.git
git push -u origin main --force

git remote remove vercel_app 2>nul
git remote add vercel_app https://github.com/nenengwahyuni/wijaya-barbershop-app.git
git push -u vercel_app main --force

if %errorlevel% equ 0 (
    echo.
    echo ======================================================================
    echo   [BERHASIL!] Website Vercel Anda otomatis diperbarui dalam 15 detik!
    echo   Membuka https://wijaya-barbershop-app.vercel.app ...
    echo ======================================================================
    timeout /t 12 /nobreak >nul
    start https://wijaya-barbershop-app.vercel.app
) else (
    echo.
    echo [!] Gagal mengunggah otomatis.
)
pause

@echo off
setlocal enabledelayedexpansion
chcp 65001 >nul
title Update Otomatis SIS-SMA1LE (Aplikasi & Database)

echo ========================================================================
echo        SIS-SMA1LE - SISTEM INFORMASI & EKOSISTEM SEKOLAH TERPADU
echo         SKRIP PEMBARUAN OTOMATIS APLIKASI DAN STRUKTUR DATABASE
echo ========================================================================
echo.

cd /d "%~dp0"

:: Cari executable PHP (Laragon PHP 8.5 atau system PATH)
set "PHP_BIN="

if exist "C:\laragon\bin\php\php-8.5.10-Win32-vs17-x64\php.exe" (
    set "PHP_BIN=C:\laragon\bin\php\php-8.5.10-Win32-vs17-x64\php.exe"
) else (
    for /d %%D in (C:\laragon\bin\php\php-8.*) do (
        if exist "%%D\php.exe" set "PHP_BIN=%%D\php.exe"
    )
)

if not defined PHP_BIN (
    where php >nul 2>nul
    if %errorlevel% equ 0 (
        set "PHP_BIN=php"
    ) else (
        echo [ERROR] Executable PHP tidak ditemukan! Pastikan Laragon berjalan atau PHP terdaftar di PATH.
        echo.
        pause
        exit /b 1
    )
)

echo [INFO] Menggunakan PHP: %PHP_BIN%
echo [INFO] Menjalankan pembaruan sistem dan database...
echo.

"%PHP_BIN%" artisan app:update %*

echo.
echo ========================================================================
echo   Proses pembaruan selesai. Tekan tombol apa saja untuk menutup jendela.
echo ========================================================================
pause >nul

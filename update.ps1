# ========================================================================
#   SIS-SMA1LE - SKRIP PEMBARUAN OTOMATIS GITHUB & HOSTINGER LIVE
# ========================================================================

$phpBin = "C:\laragon\bin\php\php-8.5.10-Win32-vs17-x64\php.exe"
if (-not (Test-Path $phpBin)) {
    $phpBin = "php"
}

Write-Host "========================================================================" -ForegroundColor Cyan
Write-Host "   SIS-SMA1LE - AUTO-UPDATE GITHUB & HOSTINGER DATABASE" -ForegroundColor Green
Write-Host "========================================================================" -ForegroundColor Cyan

& $phpBin artisan app:update --push --deploy $args

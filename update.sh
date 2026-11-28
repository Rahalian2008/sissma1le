#!/usr/bin/env bash
# ========================================================================
#   SIS-SMA1LE - SISTEM INFORMASI & EKOSISTEM SEKOLAH TERPADU
#   SKRIP PEMBARUAN OTOMATIS APLIKASI DAN STRUKTUR DATABASE (LINUX / VPS)
# ========================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

echo "========================================================================"
echo "   SIS-SMA1LE - PEMBARUAN OTOMATIS APLIKASI & DATABASE"
echo "========================================================================"
echo ""

# Cari PHP binary
PHP_BIN=$(which php || true)
if [ -z "$PHP_BIN" ]; then
    echo "[ERROR] PHP tidak ditemukan di sistem PATH!"
    exit 1
fi

echo "[INFO] Menggunakan PHP: $PHP_BIN"
echo "[INFO] Menjalankan pembaruan..."
echo ""

# Jalankan command artisan app:update
"$PHP_BIN" artisan app:update "$@"

echo ""
echo "========================================================================"
echo "   Pembaruan selesai dengan sukses!"
echo "========================================================================"

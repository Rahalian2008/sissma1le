# PANDUAN DEPLOYMENT PRODUKSI & PEMELIHARAAN SISTEM
## SISTEM INFORMASI SISWA & PEMBELAJARAN SMA1LE (LARAVEL 13)
**SMA NEGERI 1 LENGKONG — KABUPATEN SUKABUMI**

---

### 1. Ikhtisar Arsitektur Produksi
Aplikasi **SIS SMA1LE** dibangun dengan fondasi teknologi terkini:
- **Framework Core**: Laravel 13.x (PHP 8.4 / 8.5 kompatibel penuh dengan PHP 8.2+)
- **Database Engine**: MySQL 8.0+ / MariaDB 10.4+ dengan Charset `utf8mb4_unicode_ci`
- **Frontend Stack**: Tailwind CSS v4 & Vanilla JS (Pre-compiled via Vite)
- **AI Integration**: Multi-provider AI Adapter (Google Gemini API dengan Fallback Cerdas Offline)
- **Target Deployment**: Shared Hosting (Hostinger / cPanel) atau VPS Cloud (Ubuntu 22.04/24.04 LTS)

---

### 2. Kebutuhan Server & Ekstensi PHP
Pastikan server hosting telah mengaktifkan opsi berikut pada panel kontrol (hPanel / cPanel):
- **PHP Version**: `8.2`, `8.3`, `8.4`, atau `8.5`
- **Ekstensi PHP Wajib**:
  - `pdo_mysql`, `mysqli`
  - `openssl`, `mbstring`, `tokenizer`
  - `xml`, `dom`, `ctype`, `json`, `bcmath`, `fileinfo`
  - `gd` atau `imagick` (Wajib untuk pemrosesan foto presensi, logo kop, dan kompresi portofolio)
  - `curl`, `zip`
- **Direktif PHP (`php.ini`) yang Disarankan**:
  - `upload_max_filesize = 25M`
  - `post_max_size = 30M`
  - `memory_limit = 256M` (atau `512M`)
  - `max_execution_time = 90`

---

### 3. Konfigurasi Berkas Lingkungan (`.env`) Produksi

Siapkan berkas `.env` pada direktori root server dengan konfigurasi berikut:

```env
APP_NAME="SISWA SMA1LE"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://sman1lengkong.sch.id

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_produksi
DB_USERNAME=user_database_produksi
DB_PASSWORD=password_database_produksi

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Konfigurasi Layanan AI Kurikulum Merdeka (Gemini API)
GEMINI_API_KEY=YOUR_GEMINI_API_KEY_HERE
GEMINI_MODEL=gemini-1.5-flash
AI_ENABLE_FALLBACK=true
```

> [!IMPORTANT]
> - Pastikan nilai `APP_DEBUG=false` untuk mencegah kebocoran informasi teknis server saat terjadi pengecualian (*exception*).
> - Nilai `AI_ENABLE_FALLBACK=true` menjamin bahwa bila kuota API eksternal habis atau jaringan terputus, sistem secara otomatis beralih ke *heuristic pedagogical fallback engine* tanpa memunculkan error 500 kepada guru.

---

### 4. Perintah Optimasi Performa Produksi (Wajib Dijalankan)

Jalankan perintah-perintah berikut melalui terminal SSH atau menu *Terminal* hPanel setelah berkas diekstrak di server:

```bash
# 1. Hubungkan folder penyimpanan berkas publik (Symlink Storage)
php artisan storage:link

# 2. Hapus sisa cache sementara sebelumnya
php artisan optimize:clear

# 3. Kompilasi dan amankan konfigurasi aplikasi ke dalam satu file cache
php artisan config:cache

# 4. Registrasi cepat seluruh rute (routing table cache)
php artisan route:cache

# 5. Pra-kompilasi seluruh template Blade ke format PHP native
php artisan view:cache
```

---

### 5. Pengaturan Izin Folder (Permissions) & Struktur Storage

Pastikan perizinan folder disetel dengan benar agar server web (Apache / LiteSpeed / Nginx) dapat membaca dan menulis berkas unggahan:

```bash
# Direktori yang membutuhkan hak tulis:
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

Struktur folder penyimpanan publik yang diinisialisasi otomatis oleh `AppServiceProvider`:
- `storage/app/public/attendance/` (Foto presensi & bukti izin sakit)
- `storage/app/public/selfies/` (Foto swafoto verifikasi presensi)
- `storage/app/public/portfolios/` (Berkas karya & artefak belajar siswa)
- `storage/app/public/branding/` (Logo sekolah & gambar Kop Surat resmi)
- `storage/app/public/avatars/` (Foto profil guru & siswa)

---

### 6. Pengaturan Jadwal Otomasi Server (Cron Job)

Aplikasi memiliki otomasi sistem presensi, batas waktu *cut-off* presensi pukul 11.00 WIB, dan penandaan ALFA otomatis. Tambahkan baris jadwal berikut pada menu **Cron Jobs** di hPanel/cPanel:

```bash
* * * * * cd /home/USERNAME/public_html && php artisan schedule:run >> /dev/null 2>&1
```
*(Sesuaikan `/home/USERNAME/public_html` dengan path direktori absolut aplikasi Anda).*

---

### 7. Strategi Backup Database & Skrip Pemulihan Bencana (DRP)

1. **Jadwal Pencadangan Otomatis**:
   - Manfaatkan fitur **Daily Auto-Backup** pada hPanel Hostinger untuk mencadangkan database MySQL dan berkas unggahan setiap malam pukul 02.00 WIB.
2. **Pencadangan Manual Sebelum Pembaruan Besar**:
   - Unduh salinan database via phpMyAdmin (`Export` format SQL dengan opsi `Quick`).
3. **Prinsip Non-Destruktif**:
   - **PERINGATAN KERAS**: Dilarang menjalankan perintah `php artisan migrate:fresh`, `migrate:reset`, atau `db:wipe` di lingkungan produksi karena akan menghapus seluruh data siswa, presensi, rapor, dan kurikulum yang sedang berjalan!
   - Setiap penambahan tabel baru wajib menggunakan skrip migrasi tersendiri (`php artisan migrate --force`).

---

### 8. Uji Kesiapan Sistem (*System Health Check*)

Setelah deployment selesai dilakukan:
1. Masuk sebagai Administrator / Tim Kurikulum.
2. Akses halaman status kesiapan: `https://domain-anda/lms/health-check` (rute: `lms.health`).
3. Pastikan seluruh indikator menampilkan status **HIJAU (SEHAT)**:
   - ✅ Status Database MySQL & Tabel Kurikulum Terdaftar
   - ✅ Status Direktori Penyimpanan Publik (Storage Link)
   - ✅ Status Mesin Pemrosesan Gambar (GD / Imagick)
   - ✅ Status Koneksi Layanan AI (Gemini API & Fallback Mock)
   - ✅ Status Rute Dokumen & Ekspor Spreadsheet

---
*Dokumen ini diterbitkan resmi oleh Tim Administrator Infrastruktur SIS SMA1LE.*


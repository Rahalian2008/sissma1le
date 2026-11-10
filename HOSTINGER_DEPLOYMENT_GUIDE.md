# Panduan Resmi Deployment SIS SMA1LE di Hostinger (hPanel)

Dokumen ini memuat panduan lengkap, langkah demi langkah, untuk mengunggah, mengonfigurasi, dan menjalankan aplikasi **SIS SMA1LE (Sistem Informasi Siswa Terpadu SMAN 1 Lengkong)** di layanan hosting **Hostinger** (Shared Hosting, Cloud Hosting, atau cPanel).

---

## Daftar Isi
1. [Kebutuhan Sistem & Versi PHP](#1-kebutuhan-sistem--versi-php)
2. [File Database MySQL Siap Pakai](#2-file-database-mysql-siap-pakai)
3. [Langkah 1: Persiapan File Sebelum Upload](#langkah-1-persiapan-file-sebelum-upload)
4. [Langkah 2: Mengunggah File ke Hostinger](#langkah-2-mengunggah-file-ke-hostinger)
5. [Langkah 3: Mengatur Document Root di hPanel](#langkah-3-mengatur-document-root-di-hpanel)
6. [Langkah 4: Membuat Database & Import SQL di phpMyAdmin](#langkah-4-membuat-database--import-sql-di-phpmyadmin)
7. [Langkah 5: Konfigurasi Berkas .env Produksi](#langkah-5-konfigurasi-berkas-env-produksi)
8. [Langkah 6: Mengatur Izin Folder (Permissions)](#langkah-6-mengatur-izin-folder-permissions)
9. [Langkah 7: Menyiapkan Cron Job (Otomasi Presensi & ALPA)](#langkah-7-menyiapkan-cron-job-otomasi-presensi--alpa)
10. [Langkah 8: Aktivasi SSL HTTPS Gratis](#langkah-8-aktivasi-ssl-https-gratis)
11. [Daftar Akun Login Bawaan](#daftar-akun-login-bawaan)
12. [Troubleshooting & Solusi Masalah Umum](#troubleshooting--solusi-masalah-umum)

---

## 1. Kebutuhan Sistem & Versi PHP

Aplikasi ini dibangun menggunakan **Laravel 12** yang modern dan efisien. Pastikan pengaturan di menu **Tingkat Lanjut -> Konfigurasi PHP** pada hPanel Hostinger memenuhi spesifikasi berikut:

- **Versi PHP**: **PHP 8.2** atau **PHP 8.3** (Rekomendasi stabil di Hostinger).
- **Ekstensi PHP yang Wajib Aktif** (Centang di tab *PHP Extensions* di hPanel):
  - `pdo_mysql` & `mysqli`
  - `openssl`
  - `mbstring`
  - `tokenizer`
  - `xml` & `dom`
  - `ctype`
  - `json`
  - `bcmath`
  - `fileinfo`
  - `gd` atau `imagick` (Penting untuk pemrosesan foto selfie presensi dan logo sekolah)
  - `curl`
  - `zip`
- **Konfigurasi Tambahan (PHP Options)**:
  - `upload_max_filesize`: minimal `10M` (disarankan `20M` untuk lampiran foto & berkas).
  - `post_max_size`: minimal `12M` (disarankan `25M`).
  - `memory_limit`: `256M` atau `512M`.
  - `max_execution_time`: `60` detik.

---

## 2. File Database MySQL Siap Pakai (Production-Ready)

Berkas database MySQL resmi aplikasi ini telah diekspor dalam format standar UTF-8 murni (`utf8mb4_unicode_ci`) tanpa statement `CREATE DATABASE` atau `USE` sehingga **100% aman diimpor ke database Hostinger apa pun via phpMyAdmin tanpa error hak akses (#1044 Access Denied)**:

Tersedia di root proyek:
1. **Format SQL Langsung:**
   ```text
   database_hostinger_sissma1le.sql (~3.2 MB)
   ```
2. **Format ZIP Siap Upload (Sangat Direkomendasikan untuk phpMyAdmin):**
   ```text
   database_hostinger_sissma1le.zip (~364 KB)
   ```
   *phpMyAdmin di Hostinger secara otomatis dapat mengekstrak dan mengimpor file .zip ini secara instan tanpa risiko timeout atau limit upload.*

**Karakteristik & Keunggulan File SQL ini**:
- Berisi **seluruh tabel lengkap (58 migrasi)** beserta seluruh relasi kunci asing (*Foreign Keys*) dan indeks performa tinggi.
- Menggunakan engine `InnoDB` dengan charset `utf8mb4_unicode_ci` yang kompatibel penuh dengan MySQL 8.0 dan MariaDB 10.x di Hostinger.
- Dilengkapi `SET FOREIGN_KEY_CHECKS=0;` di awal dan `SET FOREIGN_KEY_CHECKS=1;` di akhir agar urutan eksekusi impor tabel di phpMyAdmin tidak terhambat ketergantungan relasi.
- **Sudah terisi seluruh data master & modul terbaru**:
  - **Aplikasi Rapor Kurnas 2025**: Berdiri sendiri sesuai Permendikdasmen No. 13 Tahun 2025, integrasi nilai sumatif, capaian TP, projek kokurikuler/P5, ekstrakurikuler, catatan wali kelas, dan cetak rapor resmi.
  - **Perpustakaan Digital (E-Library)**: Buku digital PDF dengan 3D E-Reader Flip HTML, katalog buku, sirkulasi peminjaman, dan buku tamu.
  - **Website Sekolah Resmi Terpadu**: Konten dinamis sambutan kepsek, berita/artikel dengan fitur share link bergambar Open Graph WhatsApp/Sosial Media, agenda sekolah, fasilitas, profil guru/tendik, dan live counter SIS.
  - **Top 10 Leaderboard**: Peringkat prestasi siswa dan pantauan pembinaan disiplin dibatasi tepat 10 besar.
  - **LMS Pembelajaran & Kurikulum**: Bank soal, asesmen CBT, modul ajar, dan jurnal mengajar.
  - **5 Metode Presensi**: Selfie kamera langsung, barcode scanner, RFID simulator, face recognition, dan manual, lengkap dengan cut-off jam 11:00 WIB.
  - **Data Pengguna & Siswa**: Akun Super Admin, Admin, Guru, Wali Kelas, Kesiswaan, Guru BK, Siswa, dan Orang Tua terverifikasi.

---

## 3. Langkah 1: Persiapan File Sebelum Upload

Sebelum mengunggah ke Hostinger:
1. Pastikan aset frontend Vite sudah ter-compile (folder `public/build/` sudah ada di dalam proyek). Proyek ini **telah di-build secara tuntas** sehingga Anda tidak perlu menginstal Node.js di server Hostinger.
2. Pastikan file `public/hot` **TIDAK ADA**.
3. Kompres seluruh isi folder proyek menjadi format `.zip` (misalnya `sissma1le.zip`).
   > **Catatan**: Folder `node_modules/` dan folder `.git/` **tidak perlu** diikutsertakan dalam file zip agar ukuran file jauh lebih kecil dan cepat diunggah.

---

## 4. Langkah 2: Mengunggah File ke Hostinger

1. Masuk ke **hPanel Hostinger** (hpanel.hostinger.com).
2. Pilih domain / hosting Anda, lalu buka menu **Files -> File Manager**.
3. Terdapat 2 metode struktur penempatan berkas:

### Pilihan A (Sangat Direkomendasikan - Paling Rapi & Aman)
- Unggah file `sissma1le.zip` ke direktori root (satu tingkat di luar `public_html/`, misalnya di `/home/u123456789/sissma1le/`).
- Ekstrak file zip tersebut.
- Arahkan Document Root domain di hPanel ke folder `/home/u123456789/sissma1le/public`. (Lihat Langkah 3).

### Pilihan B (Langsung di dalam `public_html/`)
- Jika paket hosting Anda tidak mengizinkan pengubahan Document Root, buka folder `public_html/`.
- Unggah dan ekstrak file `sissma1le.zip` langsung di dalam `public_html/`.
- Proyek ini **sudah dilengkapi file `.htaccess` di root** yang secara otomatis mengalihkan seluruh traffic pengunjung langsung ke folder `public/` dengan proteksi penuh untuk folder internal (`app`, `storage`, `.env`, dll).

---

## 5. Langkah 3: Mengatur Document Root di hPanel

*(Hanya jika menggunakan Pilihan A)*:
1. Di hPanel Hostinger, masuk ke menu **Websites -> Kelola Website**.
2. Cari menu **Domain -> Ganti Direktori Root (Change Document Root)**.
3. Ubah direktori tujuan utama domain Anda mengarah ke:
   ```text
   sissma1le/public
   ```
4. Klik **Simpan / Perbarui**.

---

## 6. Langkah 4: Membuat Database & Import SQL di phpMyAdmin

1. Di hPanel, buka menu **Databases -> Database Manajemen**.
2. Buat database baru:
   - **Nama Database**: misalnya `sissma1le` (Hostinger akan menambahkan prefix otomatis, contoh: `u123456789_sissma1le`).
   - **Username**: misalnya `sissma1le_user` (contoh: `u123456789_user`).
   - **Password**: Buat password yang kuat dan catat password ini.
3. Klik **Buat Database**.
4. Setelah dibuat, klik tombol **Masuk ke phpMyAdmin** di samping nama database tersebut.
5. Di phpMyAdmin:
   - Pilih nama database Anda di panel sebelah kiri.
   - Klik tab **Import** di menu atas.
   - Klik tombol **Choose File** dan pilih file: `database_hostinger_ready.sql` (atau dari folder `database/deploy_hostinger_sissma1le.sql`).
   - Pastikan Character set disetel ke `utf-8`.
   - Gulir ke bawah dan klik tombol **Import** / **Kirim**.
   - Tunggu beberapa detik hingga muncul pesan hijau sukses: *"Import has been successfully finished"*.

---

## 7. Langkah 5: Konfigurasi Berkas .env Produksi

1. Di File Manager Hostinger, temukan file `.env.example`.
2. Gandakan atau ubah namanya menjadi `.env` (pastikan berawalan titik).
3. Buka dan edit file `.env` tersebut, lalu sesuaikan parameter berikut:

```env
APP_NAME="SISSMA 1 Lengkong"
APP_ENV=production
APP_KEY=base64:fJHUMXKoI5hn5KGdKhwR+5g6g+xrYSd0TnbvlCcOY8s=
APP_DEBUG=false
APP_URL=https://namadomainanda.sch.id

APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

LOG_CHANNEL=stack
LOG_LEVEL=error

# KONEKSI DATABASE MYSQL HOSTINGER
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_sissma1le
DB_USERNAME=u123456789_user
DB_PASSWORD=PasswordDatabaseAndaTadi

# DRIVER SESSION, CACHE & QUEUE
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

# PENGATURAN EMAIL SMTP HOSTINGER
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=admin@namadomainanda.sch.id
MAIL_PASSWORD=PasswordAkunEmailHostinger
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="admin@namadomainanda.sch.id"
MAIL_FROM_NAME="${APP_NAME}"

# GOOGLE GEMINI AI (Opsional - Jika ingin analisis AI aktif)
GEMINI_API_KEY=AIzaSy...
GEMINI_MODEL=gemini-2.5-flash
```

4. Simpan perubahan file `.env`.

---

## 8. Langkah 6: Mengatur Izin Folder (Permissions)

Di File Manager Hostinger:
1. Pastikan seluruh **direktori** memiliki izin `755` (`drwxr-xr-x`).
2. Pastikan seluruh **file** memiliki izin `644` (`-rw-r--r--`).
3. Khusus untuk folder penulisan berkas dan cache, pastikan izinnya **775** atau **writeable**:
   - `storage/` (dan seluruh sub-folder di dalamnya: `framework/views`, `framework/cache`, `framework/sessions`, `logs`, `app/public`).
   - `bootstrap/cache/`.

> **Catatan Symlink Foto / Berkas**:
> Aplikasi ini sudah dilengkapi sistem *fallback route storage* otomatis di `routes/web.php`. Foto selfie siswa, logo sekolah, kop surat, dan berkas bukti prestasi akan tampil secara normal tanpa eror 404 meskipun server shared hosting Hostinger tidak mengaktifkan fungsi `symlink()`.

---

## 9. Langkah 7: Menyiapkan Cron Job (Otomasi Presensi & Tata Tertib)

Sistem presensi dan tata tertib SMAN 1 Lengkong memiliki fitur otomasi terjadwal:
- Cutoff Presensi Utama, Dhuha, Dzuhur/Jum'at, Keputrian, dan Ekstrakurikuler.
- **Pukul 11:00 WIB**: Otomasi input poin prestasi Shalat Dhuha Bersama (R1.2).
- **Pukul 14:00 WIB**: Otomasi input poin pelanggaran Alpa hari ini (P1.3), Alpa 3 hari berturut-turut (P1.7), dan prestasi Shalat Dzuhur/Jum'at (R1.1).
- **Pukul 14:30 WIB**: Otomasi input poin prestasi Kegiatan Keputrian (R1.6).
- **Jum'at 23:30 WIB**: Otomasi generasi Raport Digital AI mingguan siswa.

Untuk menjalankan otomatisasi ini di Hostinger:
1. Masuk ke **hPanel -> Tingkat Lanjut -> Cron Jobs**.
2. Pilih jenis jadwal: **Kustom** (*Custom*).
3. Masukkan ekspresi jadwal setiap menit:
   ```text
   * * * * *
   ```
4. Pada kolom **Perintah (Command)**, masukkan perintah berikut (sesuaikan path folder akun Anda):
   ```bash
   /usr/bin/php /home/u123456789/sissma1le/artisan schedule:run >> /dev/null 2>&1
   ```
   *(Atau jika menggunakan `public_html`: `/usr/bin/php /home/u123456789/public_html/artisan schedule:run >> /dev/null 2>&1`)*.
5. Klik **Simpan**.

---

## 10. Langkah 8: Aktivasi SSL HTTPS Gratis

1. Di hPanel Hostinger, buka menu **Keamanan -> SSL**.
2. Pastikan sertifikat SSL gratis (Let's Encrypt) dalam status **Aktif**.
3. Aktifkan opsi **Force HTTPS** (Paksa HTTPS).
4. Aplikasi sudah diprogram di `AppServiceProvider` untuk otomatis memaksakan seluruh link URL, form, gambar, dan aset menggunakan protokol `https://` sehingga gembok hijau keamanan di browser selalu aman.

---

## 11. Daftar Akun Login Bawaan

Database yang telah diimpor telah dilengkapi dengan akun-akun pengujian resmi:

| No | Peran (Role) | Email Login | Password Default | Akses Menu Utama |
|---|---|---|---|---|
| 1 | **Super Admin** | `superadmin@sma1le.sch.id` | `password` | Kendali Penuh Seluruh Sistem & Master Data |
| 2 | **Administrator** | `admin@sma1le.sch.id` | `password` | Pengaturan Sekolah, KOP, Branding, Pengguna |
| 3 | **Kepala Sekolah** | `kepsek@sma1le.sch.id` | `password` | Supervisi Presensi, Raport Digital AI, Ekspor Laporan |
| 4 | **Guru BK** | `bk@sma1le.sch.id` | `password` | Bimbingan Konseling, Pelanggaran, Raport Digital |
| 5 | **Kesiswaan** | `kesiswaan@sma1le.sch.id` | `password` | Tata Tertib, Prestasi, Pelanggaran Poin |
| 6 | **Wali Kelas** | `walikelas@sma1le.sch.id` | `password` | Presensi Kelas, Rekap Kelas, Raport Siswa Binaan |
| 7 | **Guru Mapel** | `guru@sma1le.sch.id` | `password` | Presensi Harian, Catatan Pembinaan |
| 8 | **Siswa** | `siswa@sma1le.sch.id` | `password` | Absen Kamera Selfie, 7 Kebiasaan, Raport Pribadi |
| 9 | **Orang Tua** | `orangtua@sma1le.sch.id` | `password` | Portal Pantau Kehadiran, Rekomendasi Pendampingan |

> [!WARNING]
> **Penting**: Setelah proses deployment selesai dan Anda berhasil login pertama kali, segera ubah password akun administrator melalui menu **Profil Pengguna** demi keamanan sekolah.

---

## 12. Troubleshooting & Solusi Masalah Umum

### 1. Muncul Layar Putih / Error 500
- **Penyebab**: Izin folder `storage` atau `bootstrap/cache` belum dapat ditulis, atau file `.env` belum dikonfigurasi.
- **Solusi**: Di File Manager, klik kanan folder `storage` dan `bootstrap/cache`, pilih *Permissions*, lalu centang izin tulis (Write) untuk Group/World (`775` atau `777`). Periksa juga log eror di `storage/logs/laravel.log`.

### 2. Eror "SQLSTATE[HY000] [2002] Connection refused"
- **Penyebab**: Informasi `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, atau `DB_PASSWORD` di file `.env` tidak sesuai dengan yang terdaftar di hPanel.
- **Solusi**: Hostinger umumnya menggunakan `DB_HOST=127.0.0.1` atau `DB_HOST=localhost`. Pastikan nama database dan username menyertakan prefix (misal `u123456789_`).

### 3. Tampilan Halaman Rusak / CSS & JS Tidak Muncul
- **Penyebab**: File aset belum ter-build atau `APP_URL` di file `.env` salah.
- **Solusi**: Pastikan `APP_URL=https://domainanda.com` sama persis dengan alamat domain aktif di browser. Pastikan folder `public/build/` ada dan file `public/hot` sudah dihapus.

### 4. Mengoptimalkan Kecepatan di Server Hostinger
Jalankan perintah optimasi cache Laravel (dapat dilakukan melalui SSH Terminal hPanel atau cron job satu kali):
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Jika ada perubahan di kemudian hari, bersihkan cache dengan:
```bash
php artisan optimize:clear
```

### 5. Mengatasi Foto Selfie / Logo / KOP Surat yang Tidak Muncul (Error 403 / Gambar Pecah)
Jika Anda melihat ikon gambar pecah (*broken image*) pada foto selfie presensi atau logo sekolah:
- **Penyebab**:
  1. Pengaturan bawaan `'serve' => true` pada `config/filesystems.php` memaksakan URL bertandatangan digital (*signed URL*), sehingga URL foto publik ditolak dengan status **403 Forbidden**.
  2. File symlink `public/storage` yang diunggah dari Windows (NTFS junction) tidak terbaca di OS Linux Hostinger.
  3. Aturan penulisan `.htaccess` root memblokir akses ke URL berawalan `/storage/`.
- **Solusi Cepat 1 Langkah**:
  1. Pastikan berkas terbaru berikut telah diunggah ke server Hostinger Anda:
     - [`.htaccess`](.htaccess) (di root website)
     - [`config/filesystems.php`](config/filesystems.php)
     - [`routes/web.php`](routes/web.php)
     - [`app/Console/Commands/FixStorageCommand.php`](app/Console/Commands/FixStorageCommand.php)
  2. Buka **SSH Terminal** di hPanel Hostinger atau jalankan via Cron Job:
     ```bash
     php artisan storage:fix
     ```
     Perintah ini akan secara otomatis menghapus symlink rusak Windows, membuat folder penyimpanan fisik di `public/storage`, menyinkronkan seluruh berkas gambar, dan memastikan hak akses file (`755` folder, `644` file) terpasang sempurna.
  3. Bersihkan cache browser Anda (`Ctrl + F5`) untuk memuat ulang tampilan gambar yang kini aktif dan jernih.

---

*Aplikasi SIS SMA1LE kini siap digunakan secara optimal, cepat, aman, dan stabil di server Hostinger!*

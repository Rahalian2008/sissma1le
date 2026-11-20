# MODUL PANDUAN PENGGUNAAN APLIKASI
## SIS-SMA1LE (Sistem Informasi Siswa Terpadu SMAN 1 Lengkong)
### Berbasis Kurikulum Merdeka & Kurikulum Nasional 2025

---

## DAFTAR ISI
1. [Pendahuluan & Arsitektur Sistem](#1-pendahuluan--arsitektur-sistem)
2. [Hak Akses & Peran Pengguna (User Roles)](#2-hak-akses--peran-pengguna-user-roles)
3. [Modul Asesmen Kebutuhan Peserta Didik (AKPD Diagnostik Kelas X)](#3-modul-asesmen-kebutuhan-peserta-didik-akpd-diagnostik-kelas-x)
   - 3.1 [Alur Pengisian Mandiri oleh Siswa Kelas X (Fase E)](#31-alur-pengisian-mandiri-oleh-siswa-kelas-x-fase-e)
   - 3.2 [Melihat & Mencetak Laporan Hasil AKPD Individual](#32-melihat--mencetak-laporan-hasil-akpd-individual)
   - 3.3 [Panduan Guru BK: Rekapitulasi & Peta Profil Kebutuhan Kelas](#33-panduan-guru-bk-rekapitulasi--peta-profil-kebutuhan-kelas)
   - 3.4 [Usulan Topik Materi RPL BK 1 Lembar & Cetak Profil Kelas](#34-usulan-topik-materi-rpl-bk-1-lembar--cetak-profil-kelas)
   - 3.5 [Prosedur Reset Respon Siswa](#35-prosedur-reset-respon-siswa)
4. [Modul Asesmen Psikotes & Master AI Analisis 360°](#4-modul-asesmen-psikotes--master-ai-analisis-360)
   - 4.1 [Instrumen Tes Gaya Belajar VAK](#41-instrumen-tes-gaya-belajar-vak)
   - 4.2 [Instrumen Tes Minat Bakat Karier Holland RIASEC](#42-instrumen-tes-minat-bakat-karier-holland-riasec)
   - 4.3 [Instrumen Tes Kecerdasan Majemuk Howard Gardner](#43-instrumen-tes-kecerdasan-majemuk-howard-gardner)
   - 4.4 [Grand Asesmen Terpadu 360° (Tri-Dimensi + Rekam Jejak Karakter)](#44-grand-asesmen-terpadu-360-tri-dimensi--rekam-jejak-karakter)
   - 4.5 [Cetak Resmi A4 Laporan Psikotes (Format 2 Halaman Rapi)](#45-cetak-resmi-a4-laporan-psikotes-format-2-halaman-rapi)
5. [Modul Layanan Bimbingan Konseling Komprehensif (POP-BK)](#5-modul-layanan-bimbingan-konseling-komprehensif-pop-bk)
   - 5.1 [Ruang Curhat & Konseling Online Siswa](#51-ruang-curhat--konseling-online-siswa)
   - 5.2 [Pencatatan Sesi Konseling Individual & Kelompok](#52-pencatatan-sesi-konseling-individual--kelompok)
   - 5.3 [Sosiometri Kelas (Peta Hubungan Sosial Siswa)](#53-sosiometri-kelas-peta-hubungan-sosial-siswa)
   - 5.4 [Home Visit & Pemanggilan Orang Tua](#54-home-visit--pemanggilan-orang-tua)
   - 5.5 [Konferensi Kasus (Case Conference) & Referral](#55-konferensi-kasus-case-conference--referral)
6. [Modul Presensi & Tata Tertib Kedisiplinan Siswa](#6-modul-presensi--tata-tertib-kedisiplinan-siswa)
   - 6.1 [Metode Presensi Multi-Platform](#61-metode-presensi-multi-platform)
   - 6.2 [Sistem Poin Pelanggaran & Prestasi Siswa](#62-sistem-poin-pelanggaran--prestasi-siswa)
7. [Modul Akademik, CBT & Rapor Kurikulum Nasional 2025](#7-modul-akademik-cbt--rapor-kurikulum-nasional-2025)
8. [Panduan Cetak Dokumen Resmi & Tips Teknis (Troubleshooting)](#8-panduan-cetak-dokumen-resmi--tips-teknis-troubleshooting)

---

## 1. PENDAHULUAN & ARSITEKTUR SISTEM

**SIS-SMA1LE** adalah ekosistem digital terpadu milik **SMA Negeri 1 Lengkong** yang dirancang untuk mendukung operasional sekolah, manajemen akademik, kesiswaan, tata usaha, serta **Layanan Bimbingan dan Konseling (BK) Holistik** berbasis standar resmi:
1. **Panduan Operasional Penyelenggaraan Bimbingan dan Konseling (POP-BK)** Ditjen GTK Kemdikbudristek.
2. **Kurikulum Merdeka (Fase E & Fase F)** dan **Kurikulum Nasional 2025**.
3. **Analisis Kecerdasan Buatan (Google Gemini AI Engine & Deterministic Rule Engine)** untuk interpretasi psikometri minat bakat dan rekomendasi studi lanjut.

---

## 2. HAK AKSES & PERAN PENGGUNA (USER ROLES)

Sistem menerapkan pembatasan hak akses berbasis peran (*Role-Based Access Control*):

| Peran Pengguna | Kewenangan Utama |
|---|---|
| **Siswa (Kelas X / Fase E)** | Mengisi AKPD Diagnostik, Tes Psikotes (VAK, RIASEC, Gardner), Ruang Curhat BK, Presensi Siswa, Rekap Presensi & Poin, melihat Rapor. |
| **Siswa (Kelas XI & XII)** | Tes Psikotes Minat Bakat, Ruang Curhat BK, Presensi Siswa, Rapor, Buku Poin Pelanggaran/Prestasi. *(AKPD Diagnostik otomatis disembunyikan)*. |
| **Guru BK / Koordinator BK** | Mengelola seluruh asesmen BK (AKPD & Psikotes), Rekapitulasi Profil Kelas, Laporan Individu, RPL 1 Lembar, Ruang Curhat, Home Visit, Pemanggilan Orang Tua, Sosiometri, dan Analisis AI. |
| **Wali Kelas** | Memantau kehadiran siswa kelas binaan, melihat Profil AKPD & Psikotes kelas, input catatan rapor, pemantauan poin kedisiplinan. |
| **Kepala Sekolah** | Memantau seluruh metrik sekolah, menyetujui dokumen laporan resmi, memantau kinerja BK dan kedisiplinan siswa. |
| **Admin & Super Admin** | Mengelola data master (Guru, Siswa, Kelas, Akun), reset respon jika diperlukan, konfigurasi sistem. |

---

## 3. MODUL ASESMEN KEBUTUHAN PESERTA DIDIK (AKPD DIAGNOSTIK KELAS X)

Instrumen AKPD disusun dari **40 butir pernyataan kebutuhan** yang terbagi rata ke dalam **4 Bidang Layanan Bimbingan & Konseling**:
- **Bidang Pribadi (10 Butir):** Pengendalian emosi, resiliensi, konsep diri, kemandirian.
- **Bidang Sosial (10 Butir):** Komunikasi, adaptasi teman sebaya, anti-perundungan (*anti-bullying*).
- **Bidang Belajar (10 Butir):** Manajemen waktu, prokrastinasi tugas, strategi belajar di SMA.
- **Bidang Karier (10 Butir):** Orientasi pilihan mata pelajaran Fase F, cita-cita studi lanjut, pengenalan PTN/Kedinasan.

### 3.1 Alur Pengisian Mandiri oleh Siswa Kelas X (Fase E)
1. Siswa login ke sistem menggunakan akun siswa masing-masing melalui:
   `https://sman1lengkong.school/login`
2. Pada **Beranda Siswa**, klik shortcut card **"AKPD Diagnostik (Fase E)"** atau buka menu **Bimbingan Konseling &rarr; AKPD Diagnostik**.
3. Klik tombol **"Mulai Isi Angket AKPD"**.
4. Bacalah petunjuk pengisian:
   - Centang (**✔**) pada butir-butir pernyataan yang sedang/pernah Ananda alami dan membutuhkan bantuan/pendampingan dari Guru BK.
   - Jika pernyataan tidak dialami, biarkan kotak tidak dicentang.
5. Setelah memeriksa seluruh 4 bidang, klik tombol **"Simpan & Analisis Kebutuhan"**.
6. Sistem akan memvalidasi data dan langsung menampilkan **Laporan Profil Kebutuhan Individual**.

> [!NOTE]
> Siswa dibatasi melakukan pengisian sebanyak **1 kali (One-time submission)** demi validitas asesmen diagnostik awal tahun. Jika terjadi kesalahan isi yang mendasar, siswa dapat melapor kepada Guru BK untuk dilakukan *Reset Respons*.

---

### 3.2 Melihat & Mencetak Laporan Hasil AKPD Individual
1. Setelah mengisi, siswa maupun Guru BK dapat membuka halaman **Laporan Individu**:
   - Ditampilkan ringkasan identitas (Nama, NISN, NIS, Kelas X).
   - Terdapat **Matriks Persentase Kebutuhan 4 Bidang** dengan indikator derajat kebutuhan:
     - **Sangat Tinggi (&ge; 50%):** Prioritas Utama intervensi konseling.
     - **Sedang (26% - 49%):** Perlu pendampingan berkala.
     - **Rendah (&le; 25%):** Kondisi perkembangan stabil/mandiri.
   - Rincian butir masalah yang dialami siswa lengkap dengan kode butir dan dimensi perkembangan.
   - Rekomendasi 4 komponen POP-BK (*Layanan Dasar, Layanan Responsif, Peminatan Fase F, Dukungan Sistem*).
2. Klik tombol hijau **"Cetak Laporan Resmi A4"** di bagian atas untuk mencetak dokumen format resmi:
   - Dilengkapi Kop Surat Sekolah resmi.
   - Grafik progress bar tercetak rapi.
   - Tanda tangan resmi tripartit: **Orang Tua / Wali**, **Kepala Sekolah** (di tengah), dan **Koordinator BK** (di kanan).

---

### 3.3 Panduan Guru BK: Rekapitulasi & Peta Profil Kebutuhan Kelas
1. Masuk menggunakan akun **Guru BK** atau **Koordinator BK**.
2. Klik menu **AKPD Diagnostik**.
3. Di halaman utama, Guru BK dapat melihat:
   - Ringkasan statistik jumlah siswa yang memprioritaskan Bidang Pribadi, Sosial, Belajar, dan Karier.
   - Klik **"Peta Profil Kelas"** untuk melihat analisis per rombel.
4. Pada halaman **Profil Kelas (AKPD)**:
   - Pilih rombel pada dropdown filter (contoh: *X Merdeka 1, X Merdeka 2, dst.* atau *Rekap Seluruh Kelas X*).
   - Sistem menampilkan:
     - **Persentase Partisipasi Kelas:** Jumlah siswa yang sudah mengisi dibanding total siswa di kelas.
     - **Grafik Komparasi 4 Bidang:** Bar chart visual perbandingan rata-rata kebutuhan kelas.
     - **Top 10 Butir Masalah Tertinggi:** 10 butir pertanyaan yang paling banyak dipilih siswa di kelas tersebut.

---

### 3.4 Usulan Topik Materi RPL BK 1 Lembar & Cetak Profil Kelas
1. Pada halaman Profil Kelas, sistem secara cerdas memetakan butir masalah tertinggi kelas menjadi **Usulan Judul Materi Bimbingan Klasikal (RPL BK 1 Lembar)** untuk semester berjalan.
2. Guru BK dapat langsung mengadopsi topik ini ke dalam modul **RPL BK** aplikasi.
3. Klik tombol **"Cetak Laporan Profil Kelas A4"**:
   - Dokumen format resmi A4 siap diarsipkan untuk bukti dukung akreditasi dan supervisi mutu BK.
   - Tanda tangan resmi: **Wali Kelas**, **Kepala Sekolah**, dan **Koordinator BK**.

---

### 3.5 Prosedur Reset Respon Siswa
Jika siswa perlu mengulang pengisian karena keliru atau kondisi tertentu:
1. Guru BK atau Admin membuka halaman **Matriks AKPD** (`/bk/akpd/1`).
2. Cari nama siswa pada tabel rekapitulasi.
3. Klik ikon tempat sampah merah (**Reset Respons Siswa**).
4. Konfirmasi tindakan reset. Data respon lama dihapus, dan siswa bersangkutan dapat langsung mengisi ulang angket melalui akunnya.

---

## 4. MODUL ASESMEN PSIKOTES & MASTER AI ANALISIS 360°

Aplikasi dilengkapi modul psikotes terstandarisasi untuk pemetaan modalitas belajar, bakat minat vokasional, dan potensi kecerdasan:

### 4.1 Instrumen Tes Gaya Belajar VAK
- **Tujuan:** Mengukur preferensi modalitas sensori siswa dalam menyerap dan mengolah materi pembelajaran.
- **Dimensi:** Visual (V), Auditori (A), dan Kinestetik (K).
- **Hasil:** Dominasi modalitas (misal: Visual-Auditori atau Multimodal) beserta strategi belajar optimal di kelas dan di rumah.

### 4.2 Instrumen Tes Minat Bakat Karier Holland RIASEC
- **Tujuan:** Pemetaan minat vokasional dan kepribadian karier berdasarkan teori John L. Holland.
- **Dimensi:**
  1. **R (Realistic):** Praktis, mekanikal, teknis, luar ruangan.
  2. **I (Investigative):** Analitis, ilmiah, riset, pemecahan masalah kompleks.
  3. **A (Artistic):** Kreatif, ekspresif, desain, seni visual, sastra.
  4. **S (Social):** Edukatif, pemberdayaan manusia, pelayanan sosial, konseling.
  5. **E (Enterprising):** Kepemimpinan, bisnis, komunikasi persuasif, manajemen.
  6. **C (Conventional):** Keteraturan, administrasi, data terstruktur, akurasi.
- **Hasil:** Kode Holland 3 Huruf (contoh: **SEC**, **IAS**, **RIA**) beserta kecocokan jurusan kuliah dan profesi masa depan.

### 4.3 Instrumen Tes Kecerdasan Majemuk Howard Gardner
- **Tujuan:** Mengukur 8 spektrum kecerdasan manusia:
  - *Linguistik, Logis-Matematis, Visual-Spasial, Musikal, Kinestetik-Jasmani, Interpersonal, Intrapersonal, Naturalis*.
- **Hasil:** Profil kekuatan kognitif utama siswa dan cara mengembangkannya.

---

### 4.4 Grand Asesmen Terpadu 360° (Tri-Dimensi + Rekam Jejak Karakter)
Keunggulan utama sistem SIS-SMA1LE adalah kemampuan **mensinergikan 3 hasil tes psikotes bersama rekam jejak presensi dan kedisiplinan sekolah**:

```text
+---------------------------------------------------------------------------------+
|               GRAND ASESMEN TERPADU 360° (POP-BK & KURIKULUM MERDEKA)          |
+---------------------------------------------------------------------------------+
| 1. Matriks Tri-Dimensi Psikometri (Ilmiah):                                    |
|    - Gaya Belajar VAK                                                           |
|    - Minat Karier Holland RIASEC                                                |
|    - Kecerdasan Majemuk Howard Gardner                                          |
|                                                                                 |
| 2. Rekam Jejak Karakter & Presensi Sekolah:                                     |
|    - Persentase Kehadiran Mandiri di Sekolah                                    |
|    - Rekam Jejak Poin Tata Tertib & Komitmen Belajar                            |
|                                                                                 |
| 3. Master AI Psychological Engine:                                              |
|    - Sintesis Benang Merah Profil Siswa (Integratif, bukan simpulan parsial)    |
|    - Analisis Kekuatan (Strength Zone) & Area Stimulasi                         |
|    - Rekomendasi Pilihan Jurusan Kuliah PTN & Jalur Karier Modern               |
|    - Rekomendasi Strategi Belajar Sesuai Gaya Kognitif Siswa                    |
+---------------------------------------------------------------------------------+
```

---

### 4.5 Cetak Resmi A4 Laporan Psikotes (Format 2 Halaman Rapi)
- Format cetak dirancang presisi dengan pemisah halaman (*page-break-before*) tanpa teks yatim (*orphan text*):
  - **Halaman 1:** Kop Surat Sekolah, Identitas Siswa, Matriks Tri-Dimensi & Rekam Jejak Karakter, Profil Potensi & Area Stimulasi.
  - **Halaman 2:** Rekomendasi Jurusan PTN & Karier, Rekomendasi Gaya Belajar, Kesimpulan Konselor Pendidikan, dan Tanda Tangan Resmi:
    - **Kiri:** Mengetahui, Orang Tua / Wali Murid
    - **Tengah:** Menyetujui, Kepala SMA Negeri 1 Lengkong
    - **Kanan:** Lengkong, [Tanggal], Koordinator BK

---

## 5. MODUL LAYANAN BIMBINGAN KONSELING KOMPREHENSIF (POP-BK)

### 5.1 Ruang Curhat & Konseling Online Siswa
- Siswa dapat mengirimkan pesan curhat atau permohonan bimbingan secara rahasia dan aman.
- Guru BK menerima notifikasi di dashboard dan dapat membalas secara interaktif (real-time dialog konseling).
- Setiap percakapan terenkripsi dan hanya dapat diakses oleh siswa bersangkutan serta Guru BK penanggung jawab.

### 5.2 Pencatatan Sesi Konseling Individual & Kelompok
- Guru BK mendokumentasikan setiap sesi tatap muka:
  - Tanggal, jam, tempat, dan durasi konseling.
  - Klasifikasi masalah (Pribadi, Sosial, Belajar, Karier).
  - Deskripsi ringkas permasalahan, pendekatan teknik konseling yang digunakan, dan rencana tindak lanjut (*action plan*).
  - Status sesi: *Dalam Proses*, *Selesai / Terentaskan*, atau *Perlu Referral*.

### 5.3 Sosiometri Kelas (Peta Hubungan Sosial Siswa)
- Instrumen pilihan teman belajar/bermain untuk mendeteksi dinamika sosial kelas.
- Sistem otomatis menghitung indeks popularitas sosial, mendeteksi siswa yang terisolir (*isolated*), dan menghasilkan diagram sosiogram kelas.

### 5.4 Home Visit & Pemanggilan Orang Tua
- **Home Visit:** Pencatatan kunjungan rumah bagi siswa yang membutuhkan pendampingan khusus dilengkapi format surat tugas dan berita acara kunjungan.
- **Pemanggilan Orang Tua:** Generator surat pemanggilan resmi dengan nomor surat, waktu kehadiran, agenda pembinaan, dan cetak format standar dinas.

### 5.5 Konferensi Kasus (Case Conference) & Referral
- Dokumentasi rapat koordinasi penanganan kasus khusus yang melibatkan Guru BK, Wali Kelas, Guru Mapel, Kesiswaan, dan Kepala Sekolah.
- Formulir Alih Tangan Kasus (*Referral*) ke pihak ahli eksternal (Psikolog, Psikiater, Dokter, atau Lembaga Perlindungan Anak).

---

## 6. MODUL PRESENSI & TATA TERTIB KEDISIPLINAN SISWA

### 6.1 Metode Presensi Multi-Platform
Sistem mendukung 5 metode pencatatan kehadiran presensi:
1. **Selfie Kamera Langsung & Geofencing GPS:** Siswa melakukan presensi dari HP di dalam area radius sekolah.
2. **Barcode / QR Code Scanner:** Pemindaian kartu siswa oleh petugas piket di gerbang sekolah.
3. **RFID Card Scanner:** Pemindaian instan kartu pelajar RFID.
4. **Face Recognition (Biometrik Wajah):** Pengenalan wajah otomatis berbasis AI.
5. **Presensi Manual Kelas:** Diinput oleh Guru Piket atau Wali Kelas.
- *Cut-off Otomatis:* Siswa yang belum hadir hingga pukul 11:00 WIB otomatis tercatat **ALPA** melalui scheduled task.

### 6.2 Sistem Poin Pelanggaran & Prestasi Siswa
- Buku saku tata tertib terdigitalisasi:
  - Setiap pelanggaran menambah poin sesuai bobot pedoman tata tertib.
  - Setiap prestasi akademik / nonakademik memberikan poin penghargaan dan sertifikat digital.
  - Peringkat pembinaan disiplin otomatis terhubung dengan modul konseling BK saat poin mencapai ambang batas tertentu (SP 1, SP 2, SP 3).

---

## 7. MODUL AKADEMIK, CBT & RAPOR KURIKULUM NASIONAL 2025

- **Asesmen CBT:** Pelaksanaan ujian harian, sumatif tengah semester, dan sumatif akhir semester berbasis komputer dan mobile-friendly dengan pengacakan soal.
- **Aplikasi Rapor Kurikulum Nasional 2025:**
  - Penilaian berbasis Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP).
  - Nilai Non-Tes dan Nilai Tes terintegrasi.
  - Penilaian Projek Penguatan Profil Pelajar Pancasila (P5) dan Projek Kokurikuler.
  - Cetak Rapor Resmi 1 Halaman / Multi Halaman berstandar Permendikdasmen No. 13 Tahun 2025.

---

## 8. PANDUAN CETAK DOKUMEN RESMI & TIPS TEKNIS (TROUBLESHOOTING)

### 8.1 Tips Pengaturan Cetak Browser (Print Setup)
Agar hasil cetak dokumen (Laporan AKPD, Laporan Psikotes, Rapor, Surat Resmi) presisi dan berkualitas tinggi:
1. Gunakan browser **Google Chrome** atau **Microsoft Edge**.
2. Pada dialog cetak (*Ctrl + P*):
   - **Destination (Tujuan):** Pilih printer fisik atau *Save as PDF*.
   - **Paper Size (Ukuran Kertas):** Pilih **A4**.
   - **Margins (Batas Tepi):** Pilih **Default** atau **None** (sistem sudah menyertakan CSS margin resmi 12mm-15mm).
   - **Options (Pilihan):** Centang **Background graphics (Grafik latar belakang)** agar warna bar chart dan badge kategori tercetak sempurna.
   - **Headers and footers:** Matikan / hapus centang (agar URL browser tidak tercetak di bagian bawah).

### 8.2 Cara Mengatasi Cache Browser Jika Tampilan Belum Berubah
Jika Anda sudah memperbarui sistem namun tampilan di browser belum berubah:
1. Tekan tombol **Ctrl + F5** (Hard Refresh) di keyboard laptop/komputer Anda.
2. Pada perangkat ponsel, bersihkan cache browser melalui menu riwayat penjelajahan.

---

*Dokumen panduan ini disusun resmi untuk SMA Negeri 1 Lengkong sebagai acuan operasional digital terpadu bagi seluruh sivitas akademika.*

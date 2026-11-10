# AUDIT PROYEK (PHASE 0) — SMA1LE LEARNING SYSTEM
**Sistem Informasi & Pembelajaran Terpadu SMAN 1 Lengkong**  
**Dokumen Baseline Audit & Kesiapan Sistem**  
*Tanggal Audit: 29 Oktober 2026*

---

## 1. Identifikasi Lingkungan & Stack Teknologi

| Parameter | Spesifikasi Lingkungan Aktual | Keterangan & Status |
| :--- | :--- | :--- |
| **Framework** | **Laravel Framework 13.31.0** | Versi terbaru kompatibel dengan LTS |
| **Bahasa Pemrograman** | **PHP 8.4.25 (CLI)** (Laragon PHP 8.4 / 8.5) | Kompatibel dengan syarat Laravel 13 |
| **Node.js & NPM** | **Node.js v18.8.0** | Siap untuk kompilasi Vite / Frontend |
| **Styling & CSS** | **Tailwind CSS v4** + Vite | Sistem desain seragam, modern, responsif |
| **Database Server** | **MySQL (127.0.0.1:3306)** | Database aktif: `database_hostinger_sissma1le` |
| **Format Arsitektur** | Laravel MVC + Service Classes + Blade + Tailwind | Single unified responsive web architecture |
| **Zona Waktu & Bahasa** | `Asia/Jakarta` (WIB) / Bahasa Indonesia | Standar operasional SMAN 1 Lengkong |
| **Kondisi Proyek** | **Proyek Berjalan (Brownfield Aktif)** | Data akademik, siswa, guru, presensi telah beroperasi |

---

## 2. Ketersediaan Dokumen Sumber

| Dokumen | Status di Workspace / Download | Catatan Analisis |
| :--- | :--- | :--- |
| **MASTER_PROMPT_ANTIGRAVITY_SMA1LE_LEARNING_SYSTEM_Laravel13.pdf** | **Tersedia & Telah Dianalisis Penuh (9 Halaman)** | Memuat 9 modul utama, standar arsitektur, RBAC, dan 11 tahapan implementasi bertahap. |
| **Perencanaan Pembelajaran.docx** | **Belum Ditemukan Secara Eksak** | Dicari di root proyek dan folder `Downloads`. Ditemukan file pendukung serupa: `[Template] Rancangan Pembelajaran.docx`, `Tujuan_Pembelajaran_dan_ATP_Informatika_K...`, dan `Panduan CP Tahun 2025 Jenjang PAUD DIKDAS...`. |

> **Catatan Kepatuhan Aturan**: Sesuai instruksi Bagian 2 & 23 Master Prompt, sistem **tidak mengarang** rincian yang belum tercantum. Format baku rancangan pembelajaran ditandai sebagai **perlu konfirmasi** dari pihak kurikulum sekolah sebelum dikunci sebagai format tunggal.

---

## 3. Audit Modul LMS yang Sudah Berjalan di SISSMA1LE

Aplikasi saat ini telah memiliki fondasi LMS yang kuat dan berjalan aktif. Berikut adalah audit model dan fitur yang telah diimplementasikan:

1. **Struktur Kelas & Guru Pengampu**:
   - `CourseClass` (`course_classes`): Menghubungkan Guru, Mata Pelajaran (`Subject`), Kelas Rombel (`SchoolClass`), dan Tahun Ajaran.
   - Mendukung jadwal hari/jam, kode ruang kelas, dan bobot penilaian kustom (Kehadiran, Tugas, Kuis, UTS, UAS).
2. **Bahan Ajar & Materi Pembelajaran (`LmsMaterial`)**:
   - Mendukung tipe: Dokumen/Teks, File PDF/Slide, Video Streaming YouTube/Embed, dan Generator Konten Berbantuan AI.
   - Tracking riwayat baca siswa (`LmsMaterialRead`).
3. **Penugasan & Pengumpulan (`LmsAssignment` & `LmsAssignmentSubmission`)**:
   - Tenggat pengumpulan waktu (due date), lampiran tugas guru, instruksi pengerjaan.
   - Pengumpulan mandiri oleh siswa (file upload, link/dokumen, catatan).
   - Penilaian berkas, revisi tugas, dan umpan balik langsung dari guru.
4. **Kuis Online & CBT (`LmsQuiz`, `LmsQuizQuestion`, `LmsQuizAttempt`, `LmsQuizAnswer`)**:
   - Tipe kuis: Pilihan Ganda dan Esai.
   - Fitur Token Ujian (Attempt Token), durasi waktu pengerjaan otomatis (timer countdown).
   - Koreksi otomatis untuk Pilihan Ganda & input manual skor esai oleh guru.
   - Dilengkapi generator soal otomatis berbasis AI dan modul kartu soal/kisi-kisi.
5. **Presensi KBM Mata Pelajaran (`LmsSubjectAttendance`)**:
   - Terintegrasi dengan jadwal mengajar guru per sesi/pertemuan.
   - Mendukung metode Presensi Manual oleh Guru, QR Code Dinamis berbasis waktu, dan Verifikasi Mandiri.
6. **Forum Diskusi Kelas (`LmsDiscussion`)**:
   - Ruang tanya-jawab terstruktur antar siswa dan guru per mata pelajaran.
7. **Tatap Muka Virtual (`LmsZoomMeeting`)**:
   - Integrasi ruang video conference kelas virtual langsung dari sistem.
8. **Jurnal Mengajar Guru (`LmsTeachingJournal`)**:
   - Dokumentasi materi yang diajarkan, catatan ketertiban kelas, dan jumlah kehadiran siswa.

---

## 4. Gap Analysis: Kebutuhan Master Prompt vs Sistem Eksisting

| Modul Master Prompt | Kondisi Eksisting di SISSMA1LE | Gap / Rencana Upgrade Penyempurnaan |
| :--- | :--- | :--- |
| **Modul A — Dashboard** | Dashboard Guru, Siswa, Pimpinan, dan Admin sudah tersedia terpisah. | Perlu penambahan widget progres kelengkapan perencanaan perangkat ajar guru (CP/TP/ATP/Modul Ajar) dan ringkasan status refleksi/tindak lanjut. |
| **Modul B — Data Master Akademik** | Tahun Ajaran, Kelas, Guru, Siswa, Mapel Permendikdasmen No. 13 Tahun 2025 sudah lengkap dan aktif. | Integrasi penugasan guru dan alokasi jam tatap muka per minggu ke kalender akademik sekolah. |
| **Modul C — Perencanaan Pembelajaran** | Belum ada modul khusus pengelolaan CP, TP, ATP, Prota, Promes, dan Modul Ajar/RPP secara terstruktur. | **[UPGRADE UTAMA]** Membangun sub-sistem perencanaan: Bank CP per Fase/Mapel, perumusan TP, penyusunan ATP runtut, Prota/Promes, dan penyusunan Modul Ajar/RPP digital dengan status draf/review/publikasi. |
| **Modul D — Pelaksanaan LMS** | Sudah berjalan lengkap (Materi, Tugas, Sesi, Presensi KBM, Diskusi). | Menghubungkan setiap materi dan tugas langsung ke Tujuan Pembelajaran (TP) yang telah dirumuskan pada Modul C. |
| **Modul E — Asesmen & Nilai** | CBT, Tugas, dan Gradebook pembobotan sudah tersedia. | Menambahkan keterkaitan butir asesmen ke TP/Indikator serta rekap ketercapaian TP per siswa untuk rapor. |
| **Modul F — Jurnal, Refleksi & Portofolio** | Jurnal KBM harian guru sudah ada. | **[UPGRADE UTAMA]** Membangun form refleksi guru pasca KBM berkala, catatan ketercapaian TP, rencana tindak lanjut (remedial/pengayaan), dan portofolio digital karya siswa. |
| **Modul G — Layanan AI** | AI Generator Materi dan Kuis sudah ada. | Menyediakan AI Assistant terstandarisasi untuk draf Modul Ajar/RPP, perumusan indikator TP, saran tindak lanjut remedial, dan ringkasan kendala KBM. |
| **Modul H — Dokumen & Laporan** | Cetak Rekap KBM, Kartu Soal, dan Nilai sudah ada. | Menambahkan format cetak resmi dokumen Perencanaan (Prota, Promes, Modul Ajar) dan Dokumen Jurnal & Refleksi Guru. |
| **Modul I — Pengaturan & Keamanan** | RBAC dan Audit Log sudah berjalan. | Memastikan granularitas role Wakasek Kurikulum untuk review/persetujuan perangkat ajar guru secara fleksibel. |

---

## 5. Kesimpulan Hasil Audit

1. **Kondisi Fondasi**: Sistem dalam keadaan stabil, seluruh pengujian menu dan dashboard berjalan dengan status hijau (**Passed**).
2. **Strategi Eksekusi**: Proses peningkatan (upgrade) dilakukan secara **aditif dan non-destruktif**. Struktur tabel dan data LMS yang sedang aktif digunakan guru dan siswa **tidak akan di-reset, di-truncate, atau dihapus**.
3. **Penyelarasan**: Menghubungkan data perencanaan (Modul C) dan refleksi (Modul F) ke dalam ruang kelas LMS yang sudah beroperasi sehingga menjadi satu kesatuan alur pembelajaran yang utuh:  
   $$\text{CP} \longrightarrow \text{TP} \longrightarrow \text{ATP} \longrightarrow \text{Modul Ajar / RPP} \longrightarrow \text{Sesi KBM / Materi / Tugas / Asesmen} \longrightarrow \text{Refleksi \& Tindak Lanjut}$$


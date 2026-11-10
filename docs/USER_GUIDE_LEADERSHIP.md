# BUKU PANDUAN PENGGUNA: PIMPINAN SEKOLAH & TIM KURIKULUM
## SISTEM MANAJEMEN PEMBELAJARAN SMA1LE (KURIKULUM MERDEKA)
**KEPALA SEKOLAH • WAKASEK KURIKULUM • PENGAWAS AKADEMIK**  
**SMAN 1 LENGKONG — KABUPATEN SUKABUMI**

---

### 1. Pendahuluan
Buku panduan ini merupakan pedoman manajerial bagi Kepala Sekolah, Wakil Kepala Sekolah Bidang Kurikulum, dan Pengawas Pembina dalam mengawal mutu pendidikan, memonitor administrasi perangkat ajar guru, menyetujui modul ajar, dan melaksanakan supervisi akademik terintegrasi di SMA Negeri 1 Lengkong.

---

### 2. Dashboard Pemantauan & Matriks Kesiapan Guru
1. Masuk ke aplikasi menggunakan akun berhak akses pimpinan (`kepala_sekolah`, `wakasek_kurikulum`, atau `super_admin`).
2. Masuk ke modul **LMS Pembelajaran**, lalu pilih sub-menu **🛡️ Supervisi Akademik** (`lms/supervision`).
3. Pada halaman ini tersaji **Matriks Audit 5 Dimensi Kurikulum Merdeka** untuk setiap guru di sekolah:
   - **Dimensi 1 — Perencanaan**: Jumlah total modul ajar yang telah dibuat vs yang telah disahkan (*Approved*).
   - **Dimensi 2 — Pelaksanaan KBM**: Jumlah tatap muka yang telah dicatat pada Jurnal KBM vs yang telah diverifikasi kurikulum.
   - **Dimensi 3 — Asesmen**: Ketersediaan butir soal pada Bank Soal yang telah terhubung langsung dengan Tujuan Pembelajaran (TP).
   - **Dimensi 4 — Refleksi Mengajar**: Keaktifan guru dalam mengisi catatan refleksi evaluasi pasca KBM.
   - **Dimensi 5 — Tindak Lanjut**: Pelaksanaan program pendampingan remedial dan pengayaan siswa.
4. **Indeks Kesiapan (%)**: Setiap guru memiliki kalkulasi persentase kelengkapan administrasi otomatis (0–100%) yang memudahkan pimpinan melihat guru mana saja yang telah siap atau memerlukan pendampingan intensif.

---

### 3. Peninjauan & Pengesahan Modul Ajar (RPP) Digital Guru

Wakasek Kurikulum memiliki kewenangan legal untuk menelaah perangkat ajar guru:
1. Buka sub-menu **Perencanaan & Perangkat Ajar** (`lms/planning`), pilih tab **Modul Ajar / RPP Digital**.
2. Filter status modul ajar pada kategori **Diajukan (Submitted)**.
3. Klik tombol **Tinjau Modul Ajar** untuk membaca struktur rencana pembelajaran guru:
   - Kesesuaian materi dengan Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP).
   - Kelayakan skenario pembelajaran berdiferensiasi dan integrasi Profil Pelajar Pancasila.
   - Instrumen asesmen formatif dan lembar LKPD siswa.
4. Pada modal peninjauan, pilih salah satu keputusan:
   - **SETUJUI (APPROVE)**: Modul ajar resmi disahkan (`status = APPROVED`), dipublikasikan untuk KBM, dan tercatat nama peninjau beserta waktu persetujuan.
   - **PERLU REVISI (REVISE)**: Masukkan catatan revisi pedagogis yang konstruktif (misal: "Langkah apersepsi perlu diperkuat dengan pemantik kontekstual"). Modul ajar akan kembali ke guru penyusun untuk disempurnakan.

---

### 4. Supervisi & Verifikasi Jurnal Mengajar Harian KBM

1. Buka sub-menu **Jurnal Mengajar Guru** (`lms/journals`).
2. Pimpinan dapat memfilter jurnal berdasarkan kelas atau status ketercapaian TP.
3. Pada baris jurnal KBM yang belum diverifikasi, klik tombol **Verifikasi Jurnal**.
4. Masukkan catatan supervisi singkat jika diperlukan (misal: "Kegiatan praktikum terlaksana dengan baik, tindak lanjut remedial disetujui"), lalu klik **Sahkan Verifikasi**.
5. Jurnal akan bertanda hijau terverifikasi dan masuk ke dalam berkas pengawasan kurikulum.

---

### 5. Pelaksanaan & Pencatatan Supervisi Akademik

Kepala Sekolah atau Tim Supervisi Kurikulum dapat mencatat instrumen observasi kelas secara objektif:
1. Buka sub-menu **🛡️ Supervisi Akademik** (`lms/supervision`).
2. Klik tombol **+ Catat Hasil Supervisi Baru**.
3. Isi formulir observasi:
   - Guru yang disupervisi dan kelas pembelajaran ampunya.
   - Tanggal pelaksanaan supervisi kelas.
   - Jenis instrumen (misal: Observasi Pembelajaran Lengkap / Supervisi Klinis).
   - Penilaian Skor 4 Aspek Utama (Skala 0–100):
     1. **Skor Perencanaan Pembelajaran**: Kelengkapan modul ajar, kejelasan TP, dan kesesuaian asesmen.
     2. **Skor Pelaksanaan KBM**: Pengelolaan kelas, keterlibatan aktif siswa, diferensiasi proses, dan penguasaan materi.
     3. **Skor Asesmen Pembelajaran**: Pelaksanaan asesmen formatif selama proses KBM berlangsung.
     4. **Skor Refleksi & Evaluasi**: Kemampuan guru dalam mengevaluasi efektivitas metode mengajar.
4. **Kalkulasi Otomatis Nilai Akhir & Predikat**:
   - Sistem secara otomatis menghitung nilai rata-rata akhir (*final score*) dan mengonversinya ke predikat baku kedinasan:
     - $\ge 91$: **AMAT BAIK**
     - $81 - 90$: **BAIK**
     - $71 - 80$: **CUKUP**
     - $< 71$: **KURANG**
5. Masukkan **Catatan Temuan Lapangan** dan **Rekomendasi Pembinaan**.
6. Klik **Simpan & Sahkan Hasil Supervisi**.

---

### 6. Pencetakan Dokumen Resmi Kedinasan & Ekspor Data

1. **Cetak Lembar Hasil Supervisi Akademik Guru** (`lms/supervision/{id}/print`):
   - Dokumen format resmi A4 Portrait dengan Kop Surat Dinas Pendidikan Provinsi Jawa Barat Cabang Dinas Wilayah V SMAN 1 Lengkong, tabel rincian skor 4 aspek, predikat, catatan rekomendasi, serta kolom tanda tangan Kepala Sekolah/Pengawas dan Guru yang bersangkutan.
2. **Ekspor Matriks Kesiapan Perangkat Ajar Seluruh Sekolah** (`lms.documents.export.matrix`):
   - Buka sub-menu **Arsip & Cetak Dokumen** (`lms/documents`).
   - Klik tombol **📥 Ekspor Matriks Kesiapan (CSV/Excel)**.
   - Berkas spreadsheet akan otomatis diunduh, memuat tabel lengkap seluruh pendidik, NIP, beban rombel kelas, jumlah modul ajar disetujui, jurnal diverifikasi, butir bank soal TP, refleksi, program remedial, dan skor indeks kesiapan persentase. File siap dicetak atau dilaporkan ke Cabang Dinas Pendidikan.

---

### 7. Pengawasan Penggunaan Layanan AI

Pimpinan dapat memantau utilisasi kecerdasan buatan di lingkungan sekolah via sub-menu **Pusat Layanan AI** (`lms/ai-center`):
- Memeriksa status latensi provider AI (Google Gemini API / Fallback).
- Memantau log audit pemakaian oleh para pendidik guna memastikan penggunaan AI tetap beretika, tidak membocorkan privasi siswa (*PII scrubbing active*), dan tetap menempatkan guru sebagai penanggung jawab pedagogis utama.

---
*Dokumen ini diterbitkan resmi oleh Pimpinan SMAN 1 Lengkong.*


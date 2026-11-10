# DAFTAR KEBUTUHAN SISTEM (REQUIREMENTS)
**SMA1LE Learning System — SMAN 1 Lengkong**  
*Ref: MASTER_PROMPT_ANTIGRAVITY_SMA1LE_LEARNING_SYSTEM_Laravel13*

---

## BAGIAN A: Kebutuhan yang Bersumber Langsung dari Dokumen Master Prompt

Kebutuhan berikut diambil secara terstruktur dari 9 modul utama dokumen sumber:

### 1. Modul A — Dashboard Berbasis Peran
- **Dashboard Guru**: Menampilkan jadwal mengajar, kelas/mapel ampu, status kelengkapan perencanaan perangkat ajar, materi/tugas aktif, tenggat penilaian, ringkasan asesmen, daftar refleksi yang belum diisi, dan rencana tindak lanjut.
- **Dashboard Pimpinan (Kepsek / Wakasek Kurikulum)**: Ringkasan persentase kelengkapan perangkat ajar guru sekolah, status KBM aktif, progres penilaian siswa, metrik agregat nilai, serta daftar guru/kelas yang memerlukan pendampingan/supervisi.
- **Dashboard Siswa**: Materi pelajaran terbaru, tugas aktif dan tenggat waktu, asesmen online tersedia, status pengumpulan berkas, nilai yang telah dipublikasikan, serta progres belajar individual.
- **Dashboard Admin**: Metrik kesehatan sistem, data pengguna aktif, status konfigurasi tahun ajaran/semester, dan log aktivitas operasional.

### 2. Modul B — Data Master Akademik
- Pengelolaan Tahun Ajaran aktif dan Semester (Ganjil/Genap).
- Kalender Akademik dan alokasi hari efektif KBM.
- Data Pendidik (Guru) beserta profil dan NIP/kode guru.
- Data Siswa dan status aktif/rombongan belajar.
- Master Mata Pelajaran (mengacu pada Kepmendikdasmen No. 13 Tahun 2025).
- Penugasan Guru ke Mata Pelajaran dan Kelas (`CourseClass`).
- Jadwal pembelajaran mingguan dan alokasi ruang kelas.

### 3. Modul C — Perencanaan Pembelajaran Terpadu
- **Capaian Pembelajaran (CP)**: Penyimpanan rumusan CP per fase (Fase E untuk Kelas X, Fase F untuk Kelas XI-XII) dan elemen materi.
- **Tujuan Pembelajaran (TP)**: Perumusan TP yang diturunkan dari CP, dilengkapi indikator ketercapaian dan alokasi jam tatap muka.
- **Alur Tujuan Pembelajaran (ATP)**: Pengurutan susunan TP secara logis dan terstruktur per semester/tahun ajaran.
- **Program Tahunan (Prota) & Program Semester (Promes)**: Pemetaan distribusi jam efektif dan materi pokok per minggu efektif.
- **Modul Ajar / RPP Digital**: Penyusunan identitas modul ajar, profil pelajar, model/metode pembelajaran, sarana prasarana, langkah kegiatan belajar (awal, inti, penutup), LKPD, dan lampiran instrumen asesmen.
- **Alur Status Dokumen**: Siklus status: `Draf` $\rightarrow$ `Diajukan untuk Ditinjau` $\rightarrow$ `Perlu Revisi` $\rightarrow$ `Disetujui / Dipublikasikan`.

### 4. Modul D — Pelaksanaan Pembelajaran / LMS
- Guru dapat membuat sesi/pertemuan KBM terhubung ke modul ajar dan TP.
- Guru mengunggah bahan bacaan, dokumen, presentasi, tautan video, atau LKPD interaktif.
- Pengaturan ketersediaan materi (buka/tutup akses, tanggal rilis).
- Manajemen penugasan dengan tenggat waktu, panduan rubrik, dan batas format file.
- Siswa dapat mengunggah berkas jawaban tugas, melihat umpan balik, dan memperbaiki revisi.
- Pengisian Jurnal Mengajar harian oleh guru per sesi KBM.

### 5. Modul E — Asesmen dan Pengolahan Nilai
- **Bank Soal**: Pengelolaan soal pilihan ganda, esai, tingkat kesulitan (mudah/sedang/sukar), dan pemetaan ke TP/indikator soal.
- **Kisi-kisi Asesmen**: Generator dan tabel kisi-kisi soal terhubung ke TP dan materi ajar.
- **Asesmen Online (CBT)**: Pengerjaan tes berbasis token, timer countdown, dan koreksi otomatis untuk pilihan ganda.
- **Rubrik Penilaian**: Penilaian analitik dengan kriteria penskoran yang jelas.
- **Pengolahan Skor**: Transparansi rumus konversi nilai, pembulatan nilai, penanganan siswa susulan/remedial, dan publikasi nilai kepada siswa/orang tua.

### 6. Modul F — Administrasi, Jurnal, Portofolio, dan Refleksi
- **Jurnal Pelaksanaan KBM**: Dokumentasi keterlaksanaan materi per pertemuan.
- **Catatan Hambatan & Kondisi Kelas**: Catatan anekdotal dinamika kelas dan interaksi siswa.
- **Refleksi Guru**: Evaluasi berkala guru setelah pembelajaran terkait efektivitas strategi mengajar dan respons siswa.
- **Rencana Tindak Lanjut**: Perencanaan program remedial bagi siswa belum tuntas dan pengayaan bagi siswa yang melampaui capaian.
- **Portofolio Digital Siswa**: Arsip karya, lembar tugas terbaik, dan capaian proyek siswa.

### 7. Modul G — Layanan AI Sebagai Asisten
- Pembuatan draf indikator TP dari narasi CP.
- Generator draf modul ajar/RPP, ide aktivitas kelas, dan LKPD.
- Generator butir soal bank soal berdasarkan topik dan indikator.
- Analisis pola kesulitan belajar siswa dan rekomendasi materi remedial.
- Bantuan perumusan kalimat refleksi guru.
- *Aturan Etika*: Seluruh keluaran AI berstatus **draf**, wajib ditinjau dan diedit oleh guru, serta tidak mengambil keputusan asesmen akhir secara otomatis.

### 8. Modul H — Dokumen, Cetak, dan Laporan
- Cetak Dokumen Perangkat Ajar: Prota, Promes, ATP, Modul Ajar/RPP resmi.
- Cetak Jurnal Mengajar dan Agenda Guru.
- Cetak Rekapitulasi Nilai Asesmen Formatif & Sumatif.
- Laporan Ketercapaian TP per Siswa untuk Lampiran Rapor.
- Ringkasan Supervisi dan Monitoring Pimpinan Sekolah.

### 9. Modul I — Pengaturan & Tata Kelola Sistem
- Manajemen akun pengguna, peran (`Role`), dan izin akses (`Permission`).
- Profil identitas SMAN 1 Lengkong dan tanda tangan digital/QR verifikasi dokumen.
- Audit trail terhadap setiap aksi perubahan data penting (nilai, status draf, otorisasi).

---

## BAGIAN B: Rekomendasi Tambahan Sistem Terpadu (Enhancements)

Fitur berikut merupakan rekomendasi penguatan arsitektur yang melengkapi ekosistem SISSMA1LE yang sudah berjalan:
1. **Penyatuan Sub-Navigasi Horizontal**: Menerapkan sub-menu berbasis tab horizontal seragam dengan responsivitas `flex flex-wrap` agar konsisten di seluruh modul aplikasi.
2. **Integrasi Presensi Kelas Multi-Metode**: Menghubungkan sesi KBM LMS dengan presensi otomatis siswa via QR-code dinamis di proyektor kelas atau presensi manual guru.
3. **Penyimpanan Berkas Aman (Private Disk)**: Dokumen RPP privat, bukti tugas siswa, dan naskah bank soal disimpan di storage terlindung (`Storage::disk('local')`) dengan download route terotorisasi guna mencegah kebocoran soal dan dokumen.
4. **Adapter AI Mandiri**: Abstraksi provider AI menggunakan adapter interface sehingga model AI (Google Gemini API / OpenAI / fallback lokal) dapat dirotasi secara mulus tanpa mengubah kode aplikasi.
5. **Dashboard Supervisi Kurikulum Interaktif**: Visualisasi diagram progres penyelesaian perangkat ajar per kelompok mata pelajaran (MIPA, IPS, Bahasa, dll.).

---

## BAGIAN C: Poin yang Belum Terdefinisi & Memerlukan Konfirmasi

Sesuai arahan pada Bagian 2 Dokumen Sumber:
1. **Poin 3.8 — Format Baku Dokumen Perencanaan Pembelajaran**:
   - *Status*: **Perlu Konfirmasi**.
   - *Pertanyaan*: Apakah SMAN 1 Lengkong menetapkan format baku RPP/Modul Ajar tertentu (misal: RPP 1 Halaman Merdeka Belajar, Modul Ajar Lengkap 3 Komponen Inti BSKAP, atau format khusus sekolah)?
   - *Saran Sistem*: Sistem akan menyediakan template fleksibel standar Kurikulum Merdeka (Tujuan, Langkah Pembelajaran, Asesmen) yang dapat dicetak resmi dan diedit dinamis oleh guru.
2. **Poin 4.7 — Mekanisme Persetujuan (Approval Workflow) Perangkat Ajar**:
   - *Status*: **Perlu Konfirmasi**.
   - *Pertanyaan*: Apakah setiap Modul Ajar/RPP yang dibuat guru wajib melalui tahapan verifikasi/tanda tangan elektronik oleh Wakasek Kurikulum dan Kepala Sekolah sebelum berstatus "Disetujui", atau cukup disimpan dan langsung dapat dipakai mengajar oleh guru?
   - *Saran Sistem*: Dibuat **konfigurabel** melalui Pengaturan Kurikulum; sekolah dapat mengaktifkan mode "Validasi Bertingkat" atau mode "Publikasi Mandiri Guru".


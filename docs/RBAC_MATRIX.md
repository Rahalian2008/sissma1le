# MATRIKS PERAN DAN HAK AKSES (RBAC MATRIX)
**SMA1LE Learning System — SMAN 1 Lengkong**  
*Pemetaan Otorisasi Granular Antar Peran Pengguna*

---

## 1. Daftar Peran (Roles) dalam Sistem

1. **Super Admin (`super_admin`)**: Pemilik akses teknis penuh, konfigurasi sistem global, manajemen database, rotasi kredensial, dan audit log.
2. **Admin Sekolah (`admin`)**: Tata usaha akademik, pengelolaan data induk guru, siswa, kelas rombel, tahun ajaran, dan jadwal KBM.
3. **Wakasek Kurikulum (`wakasek_kurikulum`)**: Pemantau keterlaksanaan kurikulum, peninjau dan pemberi persetujuan draf Modul Ajar guru, verifikator bank soal, dan monitoring agregat KBM sekolah.
4. **Kepala Sekolah (`kepala_sekolah`)**: Peninjau dashboard eksekutif, rekapitulasi ketercapaian target kurikulum, kepatuhan perangkat ajar guru, dan pengesahan dokumen resmi.
5. **Guru Pengampu (`guru`)**: Pembuat CP/TP/ATP mata pelajaran, penyusun Modul Ajar/RPP, pengelola materi/tugas/kuis di ruang kelas binaan, penilai tugas, pengisi jurnal, dan penulis refleksi KBM.
6. **Wali Kelas (`wali_kelas`)**: Pemantau perkembangan hasil belajar, rekap nilai siswa di kelas binaan, dan verifikator portofolio siswa.
7. **Siswa (`siswa`)**: Peserta didik yang mengakses materi, mengumpulkan tugas, mengikuti kuis CBT online, melihat nilai milik sendiri, dan mengunggah portofolio karya.
8. **Orang Tua / Wali (`orang_tua`)**: Memantau perkembangan belajar, kehadiran KBM, dan pengumuman sekolah via Portal Orang Tua.

---

## 2. Matriks Otorisasi Granular per Modul

| Modul & Aksi Fitur | Super Admin | Admin | Wakasek Kurikulum | Kepala Sekolah | Guru Pengampu | Wali Kelas | Siswa | Orang Tua |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **A. DASHBOARD** | | | | | | | | |
| Akses Dashboard Admin & Sistem | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Akses Dashboard Pimpinan / Kurikulum | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Akses Dashboard Guru & KBM | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |
| Akses Dashboard Siswa | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| Akses Portal Orang Tua | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **B. DATA MASTER AKADEMIK** | | | | | | | | |
| Kelola Tahun Ajaran & Semester | ✅ | ✅ | 👁️ (Lihat) | 👁️ (Lihat) | 👁️ (Lihat) | 👁️ (Lihat) | ❌ | ❌ |
| Kelola Data Siswa, Guru & Rombel | ✅ | ✅ | 👁️ (Lihat) | 👁️ (Lihat) | 👁️ (Lihat) | 👁️ (Lihat) | ❌ | ❌ |
| Kelola Mapel & Penugasan Guru | ✅ | ✅ | ✅ | 👁️ (Lihat) | 👁️ (Lihat) | 👁️ (Lihat) | ❌ | ❌ |
| **C. PERENCANAAN PEMBELAJARAN** | | | | | | | | |
| Rumuskan / Input CP & TP | ✅ | ✅ | ✅ | 👁️ (Lihat) | ✅ (Mapel Sendiri) | 👁️ (Lihat) | ❌ | ❌ |
| Susun ATP & Prota/Promes | ✅ | ✅ | ✅ | 👁️ (Lihat) | ✅ (Kelas Sendiri) | 👁️ (Lihat) | ❌ | ❌ |
| Susun Modul Ajar / RPP Digital | ✅ | ✅ | 👁️ (Lihat) | 👁️ (Lihat) | ✅ (Kelas Sendiri) | 👁️ (Lihat) | ❌ | ❌ |
| Tinjau / Setujui Modul Ajar Guru | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Akses Modul Ajar Publikasi | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (Kelas Sendiri) | ❌ |
| **D. PELAKSANAAN LMS** | | | | | | | | |
| Kelola Materi, Tugas & Sesi KBM | ✅ | ✅ | 👁️ (Lihat) | 👁️ (Lihat) | ✅ (Kelas Sendiri) | 👁️ (Lihat) | ❌ | ❌ |
| Akses & Unduh Materi KBM | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (Kelas Sendiri) | ❌ |
| Pengumpulan Jawaban Tugas | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ (Tugas Sendiri) | ❌ |
| Penilaian & Koreksi Tugas | ✅ | ✅ | 👁️ (Lihat) | 👁️ (Lihat) | ✅ (Kelas Sendiri) | 👁️ (Lihat) | ❌ | ❌ |
| **E. ASESMEN & CBT** | | | | | | | | |
| Kelola Bank Soal & Kisi-kisi | ✅ | ✅ | ✅ | 👁️ (Lihat) | ✅ (Mapel Sendiri) | ❌ | ❌ | ❌ |
| Terbitkan Kuis / Ujian CBT | ✅ | ✅ | 👁️ (Lihat) | 👁️ (Lihat) | ✅ (Kelas Sendiri) | ❌ | ❌ | ❌ |
| Mengerjakan Ujian Online CBT | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ (Siswa Terdaftar) | ❌ |
| Kelola Remedial & Pengayaan | ✅ | ✅ | 👁️ (Lihat) | 👁️ (Lihat) | ✅ (Kelas Sendiri) | 👁️ (Lihat) | 👁️ (Status Sendiri) | 👁️ |
| **F. REFLEKSI & PORTOFOLIO** | | | | | | | | |
| Pengisian Refleksi KBM Guru | ✅ | ❌ | 👁️ (Lihat) | 👁️ (Lihat) | ✅ (Kelas Sendiri) | ❌ | ❌ | ❌ |
| Unggah Portofolio Siswa | ✅ | ✅ | ❌ | ❌ | ✅ (Siswa Binaan) | ✅ | ✅ (Milik Sendiri) | ❌ |
| Verifikasi Portofolio Siswa | ✅ | ✅ | ✅ | 👁️ (Lihat) | ✅ (Guru Pengampu) | ✅ | ❌ | ❌ |
| **G. LAYANAN AI** | | | | | | | | |
| Bantuan AI Draf Perencanaan/Soal | ✅ | ✅ | ✅ | ❌ | ✅ (Guru Terdaftar) | ❌ | ❌ | ❌ |
| **H. DOKUMEN & LAPORAN** | | | | | | | | |
| Cetak Dokumen RPP / Prota / Promes | ✅ | ✅ | ✅ | ✅ | ✅ (Dokumen Sendiri) | 👁️ | ❌ | ❌ |
| Cetak Rekap Supervisi Pimpinan | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **I. SISTEM & AUDIT LOG** | | | | | | | | |
| Konfigurasi Sistem & Audit Log | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## 3. Ketentuan Keamanan & Proteksi IDOR (Insecure Direct Object References)

1. **Pemeriksaan Otorisasi Sisi Server**: Tidak ada batasan akses yang hanya bergantung pada persembunyian menu visual di Blade. Setiap method controller dan route dilindungi oleh Policy atau Gates (`$this->authorize(...)`).
2. **Isolasi Data Siswa**: Siswa hanya dapat mengunduh berkas jawaban tugas, kuis, dan nilai miliknya sendiri. Akses terhadap ID siswa lain pada rute unduhan atau submission akan menghasilkan respons `HTTP 403 Forbidden`.
3. **Isolasi Kelas Guru**: Guru hanya diizinkan memodifikasi modul ajar, tugas, nilai, dan absensi pada `course_classes` yang resmi ditugaskan kepadanya (`teacher_id === Auth::user()->teacher->id`), kecuali pengguna memiliki peran administratif (`admin` / `wakasek_kurikulum` / `super_admin`).


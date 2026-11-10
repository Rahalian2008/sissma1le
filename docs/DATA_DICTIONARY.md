# KAMUS DATA (DATA DICTIONARY) — SMA1LE LEARNING SYSTEM
**Definisi Struktur Tabel, Kolom, Indeks & Konstrain Relasional**  
*SMAN 1 Lengkong — Laravel 13*

---

## 1. Tabel: `learning_outcomes` (Capaian Pembelajaran / CP)
Menyimpan rumusan resmi Capaian Pembelajaran per fase dan elemen kurikulum.

| Kolom | Tipe Data | Nullable | Default | Keterangan & Relasi |
| :--- | :--- | :---: | :---: | :--- |
| `id` | BIGINT UNSIGNED | Tidak | AUTO_INCREMENT | Primary Key |
| `subject_id` | BIGINT UNSIGNED | Tidak | - | Foreign Key $\rightarrow$ `subjects(id)` (CASCADE) |
| `phase` | VARCHAR(5) | Tidak | `'E'` | Fase Kurikulum: `E` (Kelas X) atau `F` (Kelas XI/XII) |
| `grade_level` | VARCHAR(5) | Tidak | `'X'` | Tingkat Kelas: `X`, `XI`, `XII` |
| `code` | VARCHAR(50) | Ya | NULL | Kode rujukan CP (contoh: `CP-INF-E-BK`) |
| `element` | VARCHAR(100) | Ya | NULL | Elemen capaian (contoh: *Berpikir Komputasional*) |
| `description` | TEXT | Tidak | - | Narasi lengkap rumusan Capaian Pembelajaran |
| `created_by` | BIGINT UNSIGNED | Ya | NULL | Foreign Key $\rightarrow$ `users(id)` (SET NULL) |
| `is_active` | TINYINT(1) | Tidak | `1` | Status keaktifan CP |
| `created_at` | TIMESTAMP | Ya | NULL | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | NULL | Waktu pembaruan |

---

## 2. Tabel: `learning_objectives` (Tujuan Pembelajaran / TP)
Menyimpan butir Tujuan Pembelajaran yang dirumuskan oleh guru berdasarkan CP.

| Kolom | Tipe Data | Nullable | Default | Keterangan & Relasi |
| :--- | :--- | :---: | :---: | :--- |
| `id` | BIGINT UNSIGNED | Tidak | AUTO_INCREMENT | Primary Key |
| `learning_outcome_id` | BIGINT UNSIGNED | Ya | NULL | Foreign Key $\rightarrow$ `learning_outcomes(id)` (SET NULL) |
| `subject_id` | BIGINT UNSIGNED | Tidak | - | Foreign Key $\rightarrow$ `subjects(id)` (CASCADE) |
| `teacher_id` | BIGINT UNSIGNED | Ya | NULL | Foreign Key $\rightarrow$ `teachers(id)` (SET NULL) |
| `tp_code` | VARCHAR(30) | Tidak | - | Kode TP unik per jenjang (contoh: `10.1`, `10.2`) |
| `title` | VARCHAR(255) | Tidak | - | Ringkasan judul/kalimat Tujuan Pembelajaran |
| `description` | TEXT | Tidak | - | Uraian detail kriteria keberhasilan belajar |
| `competency` | TEXT | Ya | NULL | Ranah kompetensi (kognitif, psikomotorik) |
| `scope_material` | TEXT | Ya | NULL | Ruang lingkup materi pokok terkait |
| `hours_allocation` | INT | Tidak | `2` | Alokasi jam pelajaran tatap muka (JP) |
| `target_semester` | VARCHAR(10) | Tidak | `'GANJIL'` | Semester target pembelajaran: `GANJIL`, `GENAP` |
| `is_active` | TINYINT(1) | Tidak | `1` | Status aktif TP |
| `created_at` | TIMESTAMP | Ya | NULL | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | NULL | Waktu pembaruan |

---

## 3. Tabel: `lesson_plans` (Modul Ajar / RPP Digital)
Menyimpan dokumen perencanaan pembelajaran digital guru per sesi/pertemuan KBM.

| Kolom | Tipe Data | Nullable | Default | Keterangan & Relasi |
| :--- | :--- | :---: | :---: | :--- |
| `id` | BIGINT UNSIGNED | Tidak | AUTO_INCREMENT | Primary Key |
| `course_class_id` | BIGINT UNSIGNED | Tidak | - | Foreign Key $\rightarrow$ `course_classes(id)` (CASCADE) |
| `teacher_id` | BIGINT UNSIGNED | Tidak | - | Foreign Key $\rightarrow$ `teachers(id)` (CASCADE) |
| `title` | VARCHAR(255) | Tidak | - | Judul Modul Ajar / Topik Pembelajaran |
| `meeting_number` | INT | Tidak | `1` | Pertemuan ke- (1, 2, dst.) |
| `time_allocation_minutes` | INT | Tidak | `90` | Durasi waktu tatap muka (menit) |
| `grade_level` | VARCHAR(5) | Tidak | `'X'` | Tingkatan kelas (`X`, `XI`, `XII`) |
| `semester` | VARCHAR(10) | Tidak | `'GANJIL'` | Semester (`GANJIL`, `GENAP`) |
| `target_profile` | JSON | Ya | NULL | Array dimensi Profil Pelajar Pancasila |
| `learning_model` | VARCHAR(100) | Ya | NULL | Model KBM (PBL, PJBL, Discovery Learning, dll.) |
| `learning_methods` | JSON | Ya | NULL | Array metode (Diskusi, Praktik, Presentasi) |
| `media_and_resources` | TEXT | Ya | NULL | Sumber belajar, alat peraga, dan media |
| `learning_steps` | JSON | Ya | NULL | Langkah KBM: `introduction`, `core`, `closing` |
| `assessment_plan` | JSON | Ya | NULL | Rencana asesmen: diagnostik, formatif, sumatif |
| `lkpd_content` | LONGTEXT | Ya | NULL | Konten lembar kerja peserta didik |
| `file_path` | VARCHAR(255) | Ya | NULL | Path dokumen lampiran privat di storage |
| `status` | VARCHAR(20) | Tidak | `'DRAFT'` | `DRAFT`, `SUBMITTED`, `REVISED`, `APPROVED` |
| `reviewed_by` | BIGINT UNSIGNED | Ya | NULL | Foreign Key $\rightarrow$ `users(id)` (Wakasek/Kepsek) |
| `reviewed_at` | TIMESTAMP | Ya | NULL | Waktu peninjauan kurikulum |
| `review_notes` | TEXT | Ya | NULL | Catatan evaluasi / perbaikan peninjau |
| `is_published` | TINYINT(1) | Tidak | `0` | Terbuka untuk akses siswa di ruang kelas |
| `version` | INT | Tidak | `1` | Nomor versi revisi dokumen |
| `created_at` | TIMESTAMP | Ya | NULL | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | NULL | Waktu pembaruan |

---

## 4. Tabel: `lesson_plan_tps` (Pivot Modul Ajar $\leftrightarrow$ TP)

| Kolom | Tipe Data | Nullable | Default | Keterangan & Relasi |
| :--- | :--- | :---: | :---: | :--- |
| `id` | BIGINT UNSIGNED | Tidak | AUTO_INCREMENT | Primary Key |
| `lesson_plan_id` | BIGINT UNSIGNED | Tidak | - | Foreign Key $\rightarrow$ `lesson_plans(id)` (CASCADE) |
| `learning_objective_id` | BIGINT UNSIGNED | Tidak | - | Foreign Key $\rightarrow$ `learning_objectives(id)` (CASCADE) |
| `created_at` | TIMESTAMP | Ya | NULL | Waktu pembuatan relasi |
| `updated_at` | TIMESTAMP | Ya | NULL | Waktu pembaruan |

*Indeks Unik*: `UNIQUE KEY lp_tp_unique (lesson_plan_id, learning_objective_id)`.

---

## 5. Tabel: `teacher_reflections` (Refleksi Pembelajaran Guru)

| Kolom | Tipe Data | Nullable | Default | Keterangan & Relasi |
| :--- | :--- | :---: | :---: | :--- |
| `id` | BIGINT UNSIGNED | Tidak | AUTO_INCREMENT | Primary Key |
| `course_class_id` | BIGINT UNSIGNED | Tidak | - | Foreign Key $\rightarrow$ `course_classes(id)` (CASCADE) |
| `teacher_id` | BIGINT UNSIGNED | Tidak | - | Foreign Key $\rightarrow$ `teachers(id)` (CASCADE) |
| `lesson_plan_id` | BIGINT UNSIGNED | Ya | NULL | Foreign Key $\rightarrow$ `lesson_plans(id)` (SET NULL) |
| `reflection_date` | DATE | Tidak | - | Tanggal pelaksanaan evaluasi refleksi |
| `success_notes` | TEXT | Tidak | - | Aspek KBM yang berjalan efektif dan berhasil |
| `challenges_notes` | TEXT | Tidak | - | Hambatan, miskonsepsi siswa, atau kendala |
| `follow_up_action` | TEXT | Tidak | - | Rencana aksi konkret perbaikan di KBM berikutnya |
| `status` | VARCHAR(20) | Tidak | `'SELESAI'` | `DRAFT`, `SELESAI` |
| `created_at` | TIMESTAMP | Ya | NULL | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | NULL | Waktu pembaruan |


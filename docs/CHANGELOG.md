# CHANGELOG — SMA1LE LEARNING SYSTEM

Seluruh perubahan penting pada proyek didokumentasikan di sini secara bertahap per fase.

---

## [Phase 10: Deployment & Panduan Pengguna] — 2026-10-30 (FINAL RELEASE v1.0.0)

### Added
- **Buku Panduan Pengguna Berdasarkan Peran**:
  - `docs/USER_GUIDE_TEACHER.md`: Panduan lengkap Guru Pengampu mencakup alur CP/TP/ATP, Modul Ajar, KBM & Jurnal Harian, Bank Soal L1-L3 HOTS, Remedial, Refleksi, Portofolio, dan Pusat Dokumen Kurikulum.
  - `docs/USER_GUIDE_LEADERSHIP.md`: Panduan Pimpinan Sekolah & Tim Kurikulum mencakup Matriks Audit Kesiapan 5 Dimensi, pengesahan Modul Ajar, supervisi Jurnal KBM, instrumen Supervisi Akademik, dan ekspor spreadsheet.
  - `docs/USER_GUIDE_STUDENT.md`: Panduan Siswa & Orang Tua mencakup ruang kelas LMS, materi, pengumpulan tugas, kuis CBT, presensi mapel, etalase karya portofolio digital, dan portal orang tua.
- **Panduan Teknis Produksi**:
  - `docs/PRODUCTION_DEPLOYMENT_GUIDE.md`: Dokumen arsitektur server produksi (PHP 8.2-8.5, MySQL/MariaDB), konfigurasi `.env`, symlink storage, hak akses direktori, optimasi caching (`config:cache`, `route:cache`, `view:cache`), Cron Job otomatis, dan strategi backup database non-destruktif.
- **Pengujian Kesiapan Produksi (`tests/Feature/Phase10DeploymentAndUserGuidesTest.php`)**:
  - 3 feature tests memverifikasi ketersediaan dan integritas buku panduan, endpoint kesiapan sistem LMS (`/lms/health-check`), dan eksistensi 12 tabel kurikulum pada skema database (38 assertions, lulus 100%).
- **Rilis Final Sistem Terpadu**:
  - Seluruh 11 fase (Phase 0 s.d. Phase 10) tuntas 100% (**40 feature tests passed, 364 assertions, 0 failures, 0 regressions**).

---

## [Phase 9: Hardening, Aksesibilitas & UAT] — 2026-10-30

### Added
- **Audit Aksesibilitas & Tampilan Responsif Sub-Menu**:
  - Penambahan tag semantik `<nav aria-label="...">` dengan kontainer scroll `overflow-x-auto scrollbar-thin` dan tata letak `flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap` pada 6 partial navigasi modul (`attendance`, `discipline`, `habits`, `library`, `lms`, `master`).
  - Menjamin seluruh tombol menu pada perangkat mobile/tablet tersaji dalam bilah horizontal yang dapat digeser halus tanpa terpotong (menuntaskan keluhan pengguna di awal: sub-menu terpotong).
- **Pengujian Fitur Terpadu (`tests/Feature/Phase9HardeningAccessibilityAndUatTest.php`)**:
  - Pengujian aksesibilitas dan deteksi kelas responsive `overflow-x-auto`.
  - Pengujian server-side anti-IDOR pada 7 controller mutasi kurikulum (mencegah manipulasi oleh siswa atau guru lain).
  - Pengujian UAT End-to-End siklus penuh 9 langkah Kurikulum Merdeka (CP -> TP -> Modul Ajar -> Approval Wakasek -> Bank Soal -> Jurnal KBM -> Refleksi -> Portofolio -> Supervisi -> Cetak Dokumen) lulus 100% (**3 passed, 72 assertions**).

### Security
- **Proteksi Server-Side Anti-IDOR Menyeluruh**:
  - `CurriculumPlanController`: Otorisasi ketat pada perumusan CP (`storeOutcome`) dan TP (`storeObjective`), serta penegakan `LessonPlanPolicy`.
  - `TeachingJournalController`: Validasi kepemilikan kelas guru pada pencatatan jurnal, penghapusan, verifikasi, dan cetak agenda jurnal.
  - `AssessmentController`: Otorisasi penambahan/penghapusan butir soal pada bank soal, perencanaan remedial siswa, penyelesaian remedial, dan pencetakan kisi-kisi.
  - `ReflectionController`: Pengetatan hak akses pencatatan refleksi KBM guru, penghapusan, dan pencetakan rekapitulasi refleksi.
  - `PortfolioController`: Proteksi unggah portofolio atas nama siswa lain oleh non-guru, penghapusan, verifikasi kurasi, dan pembatasan cetak karya privat siswa.
  - `SupervisionController`: Pembatasan pencatatan hasil supervisi akademik hanya untuk pimpinan/pengawas, serta proteksi kerahasiaan lembar cetak hasil supervisi antar-guru.
  - `CurriculumDocumentController`: Pembatasan ekspor matriks kesiapan guru seluruh sekolah dan pembatasan ekspor jurnal KBM guru ampu.

---

## [Phase 8: Dokumen, Cetak & Ekspor] — 2026-10-30

### Added
- **`App\Http\Controllers\CurriculumDocumentController`**: Controller pusat cetak dan arsip dokumen kurikulum merdeka terpadu (*One-Stop Printing Hub*), generator dokumen cetak Prota, Promes, ATP, serta ekspor spreadsheet CSV.
- **Dokumen Cetak Standar Kedinasan Resmi (A4 Landscape)**:
  - `resources/views/lms/documents/print_prota.blade.php`: Dokumen Program Tahunan (PROTA) resmi dengan pemetaan JP semester ganjil/genap dan lembar pengesahan.
  - `resources/views/lms/documents/print_promes.blade.php`: Dokumen Program Semester (PROMES) dengan matriks mingguan per bulan dan distribusi tatap muka KBM.
  - `resources/views/lms/documents/print_atp.blade.php`: Dokumen Alur Tujuan Pembelajaran (ATP) terhubung Elemen CP, alokasi JP, dan Profil Pelajar Pancasila.
  - Keseragaman Kop Resmi Pemerintah Daerah Provinsi Jawa Barat Dinas Pendidikan Cabang Dinas Wilayah V SMAN 1 Lengkong dan lembar tanda tangan pengesahan pimpinan sekolah.
- **Ekspor Data Spreadsheet (Format CSV Kompatibel Excel)**:
  - Ekspor matriks audit kesiapan perangkat ajar seluruh guru sekolah (`lms.documents.export.matrix`).
  - Ekspor agenda jurnal KBM harian guru (`lms.documents.export.journals`).
- **Portal Terpadu (`resources/views/lms/documents/index.blade.php`)**: Dashboard satu pintu arsip dokumen kurikulum dengan pemilih kelas & mata pelajaran ampu untuk pencetakan instan.
- **Navigasi Terpadu**: Menu `📂 Arsip & Cetak Dokumen` pada navigasi LMS (`resources/views/lms/partials/nav.blade.php`).
- **Pengujian Fitur (`tests/Feature/Phase8DocumentPrintAndExportTest.php`)**: 5 feature tests memverifikasi akses portal cetak, pratinjau cetak Prota/Promes/ATP, ekspor spreadsheet CSV matriks kesiapan dan jurnal KBM, serta proteksi hak akses pengguna (23 assertions, lulus 100%).

---

## [Phase 7: Layanan AI Terpadu] — 2026-10-30

### Added
- **Migrasi `2026_10_30_030000_create_ai_generation_logs_table.php`**: Pembuatan tabel `ai_generation_logs` untuk audit trail pemakaian AI, tracking latensi respon, estimasi token, deteksi fallback, dan metadata.
- **Model Eloquent `App\Models\AiGenerationLog`**: Relasi ke `User` dengan casting boolean, integer, dan array metadata.
- **Penyempurnaan `App\Services\Ai\AiCurriculumManager` & `MockAiAdapter`**:
  - Otomasi pencatatan log penggunaan AI pada setiap eksekusi generator.
  - Generator butir soal HOTS penalaran L3 berbasis stimulus kearifan lokal Sukabumi / Jawa Barat (`generateHotsQuestionsWithLocalContext`).
  - Generator draf modul ajar berdiferensiasi 3 pilar: konten, proses, dan produk (`generateDifferentiatedLessonPlan`).
  - Analisis kesulitan diagnostik TP dan rekomendasi metode intervensi remedial (`analyzeDiagnosticRemediation`).
  - Agregasi statistik utilisasi AI (`getUsageStatistics`).
  - Penguatan sanitasi privasi data siswa: Penapisan otomatis NIK 16 digit, NISN, dan nomor telepon.
- **`App\Http\Controllers\AiCurriculumCenterController`**: Controller pusat layanan studio AI kurikulum dengan endpoint generator HOTS, diferensiasi, diagnosis, dan pengujian koneksi.
- **Rute Web Terpadu (`routes/web.php`)**: Prefix rute `lms/ai-center` (5 rute terproteksi).
- **Antarmuka Studio AI (`resources/views/lms/ai/index.blade.php`)**: Dashboard pemantauan utilisasi AI, 3 tool generator interaktif instan, modal pratinjau & salin draf, penegasan etika status draf, dan tabel riwayat log audit AI.
- **Navigasi Terpadu**: Menu `🤖 Pusat Layanan AI` pada navigasi LMS (`resources/views/lms/partials/nav.blade.php`).
- **Pengujian Fitur (`tests/Feature/Phase7UnifiedAiServiceTest.php`)**: 5 feature tests memverifikasi akses dashboard AI, logging audit ke database, generator HOTS lokal, modul diferensiasi & diagnostik, sanitasi privasi NIK/HP, dan proteksi otorisasi pengguna (29 assertions, lulus 100%).

---

## [Phase 6: Refleksi, Portofolio & Supervisi] — 2026-10-30

### Added
- **Migrasi `2026_10_30_020000_create_academic_supervisions_table.php`**: Pembuatan tabel `academic_supervisions` untuk pencatatan instrumen observasi supervisi pimpinan/pengawas (skor perencanaan, KBM kelas, asesmen, refleksi, skor akhir terbobot, predikat, dan rekomendasi tindak lanjut).
- **Model Eloquent `App\Models\AcademicSupervision`**: Relasi ke `Teacher`, `CourseClass`, dan `supervisor` (`User`), serta casting numerik float dan date.
- **Controller Refleksi, Portofolio & Supervisi**:
  - `App\Http\Controllers\ReflectionController`: Pengelolaan evaluasi KBM guru pasca pembelajaran, asisten AI penyusun kalimat refleksi, dan cetak dokumen resmi A4 Landscape.
  - `App\Http\Controllers\PortfolioController`: Galeri etalase karya terbaik dan proyek P5 siswa, verifikasi dan kurasi karya unggulan (`is_featured`), serta cetak lembar portofolio resmi A4.
  - `App\Http\Controllers\SupervisionController`: Dashboard supervisi terpadu kurikulum & pimpinan, matriks pemantauan kesiapan 5 dimensi perangkat ajar guru, instrumen observasi kelas, dan cetak lembar hasil supervisi resmi.
- **Rute Web Terpadu (`routes/web.php`)**: Prefix `lms/reflections` (5 rute), `lms/portfolios` (5 rute), dan `lms/supervision` (4 rute) dengan perlindungan otorisasi dan anti-IDOR.
- **Antarmuka & Dokumen Cetak Standar Kedinasan**:
  - `resources/views/lms/reflections/index.blade.php` & `print.blade.php`: Portal refleksi guru, integrasi asisten AI, dan format cetak agenda refleksi A4 Landscape.
  - `resources/views/lms/portfolios/index.blade.php` & `print.blade.php`: Galeri digital portofolio karya siswa dan lembar cetak bukti belajar kurasi pendidik.
  - `resources/views/lms/supervision/index.blade.php` & `print.blade.php`: Dashboard monitoring supervisi sekolah, matriks audit kesiapan guru, dan lembar instrumen hasil supervisi A4.
- **Navigasi Terpadu**: Ditambahkan menu `💡 Refleksi Mengajar`, `🎨 Portofolio Siswa`, dan `🛡️ Supervisi Akademik` pada `resources/views/lms/partials/nav.blade.php`.
- **Pengujian Fitur (`tests/Feature/Phase6ReflectionPortfolioAndSupervisionTest.php`)**: 4 feature tests memverifikasi alur refleksi guru & AI, kurasi portofolio siswa, pencatatan supervisi pimpinan, dan proteksi anti-IDOR (37 assertions, lulus 100%).

---

## [Phase 5: Asesmen dan Pengolahan Nilai] — 2026-10-30

### Added
- **Migrasi `2026_10_30_010000_create_sma1le_assessment_and_question_bank_tables.php`**: Pembuatan tabel `question_banks` (penyimpanan butir soal terstruktur terhubung TP, level kognitif L1-L3, tipe soal pilihan ganda/esai, rubrik/kunci jawaban, dan bobot), serta penambahan kolom `learning_objective_id` pada `lms_quizzes` dan `lms_quiz_questions`.
- **Model Eloquent `App\Models\QuestionBank`**: Relasi ke `Subject`, `Teacher`, dan `LearningObjective` dengan cast JSON pada opsi jawaban.
- **Pembaruan Model `App\Models\LmsQuiz` & `App\Models\LmsQuizQuestion`**: Relasi `learningObjective()` (`belongsTo`) dan proteksi `$fillable`.
- **`App\Http\Controllers\AssessmentController`**: Pengelolaan bank soal guru, generator butir soal HOTS berbantuan AI, pencetakan dokumen kisi-kisi penulisan soal A4 landscape resmi, serta manajemen program Remedial dan Pengayaan siswa terhubung ketuntasan KKM.
- **Rute Asesmen & Remedial (`routes/web.php`)**: Prefix `lms/assessment` mencakup 8 rute (`bank`, `bank.store`, `bank.destroy`, `bank.ai_generate`, `kisi_kisi.print`, `remediation`, `remediation.store`, `remediation.complete`).
- **Antarmuka & Cetak Dokumen Asesmen**:
  - `resources/views/lms/assessment/bank.blade.php`: Portal bank soal guru dengan filter mapel, tingkat kelas, level kognitif (L1/L2/L3), tipe soal, modal input soal manual, dan modal asisten AI HOTS.
  - `resources/views/lms/assessment/print_kisi_kisi.blade.php`: Format cetak resmi A4 Landscape "KISI-KISI PENULISAN SOAL ASESMEN" dengan Kop Dinas Pendidikan Wilayah V SMAN 1 Lengkong dan lembar tanda tangan.
  - `resources/views/lms/assessment/remediation.blade.php`: Dashboard pelaksanaan program Remedial dan Pengayaan terhubung TP dan target siswa.
- **Navigasi Terpadu**: Menu `📚 Bank Soal & Kisi-Kisi` dan `🎯 Remedial & Pengayaan` pada navigasi LMS (`resources/views/lms/partials/nav.blade.php`).
- **Pengujian Fitur (`tests/Feature/Phase5AssessmentAndQuestionBankTest.php`)**: 4 feature tests memverifikasi perenderan portal bank soal, penyimpanan butir soal terhubung TP, generator soal HOTS berbantuan AI, pratinjau cetak kisi-kisi asesmen, dan alur rencana serta penyelesaian program remedial & pengayaan (27 assertions, lulus 100%).

---

## [Phase 4: Pelaksanaan Pembelajaran & LMS] — 2026-10-30

### Added
- **Migrasi `2026_10_29_234000_create_teaching_journals_table.php`**: Pembuatan tabel `teaching_journals` untuk dokumentasi harian KBM guru, ketercapaian TP, rekap presensi siswa, catatan kendala kelas, rencana tindak lanjut, dan supervisi kurikulum.
- **Model `App\Models\TeachingJournal`**: Relasi Eloquent terpadu ke `CourseClass`, `Teacher`, `LessonPlan`, `LearningObjective`, `LmsSubjectAttendance`, dan verifikator `User`.
- **`App\Http\Controllers\TeachingJournalController`**: Pengelolaan jurnal mengajar harian guru, pencatatan KBM, verifikasi supervisi pimpinan/kurikulum, dan pencetakan dokumen resmi.
- **Rute Jurnal Mengajar (`routes/web.php`)**: Prefix `lms/journals` dengan 5 rute (`index`, `store`, `destroy`, `verify`, `print`).
- **Antarmuka & Cetak Dokumen Jurnal**:
  - `resources/views/lms/journals/index.blade.php`: Portal rekapitulasi jurnal mengajar guru dengan filter kelas mapel, filter status ketercapaian, kartu statistik, dan modal pengisian jurnal.
  - `resources/views/lms/journals/print.blade.php`: Dokumen cetak resmi A4 Landscape "AGENDA & JURNAL PELAKSANAAN PEMBELAJARAN GURU" dengan Kop Resmi SMAN 1 Lengkong dan lembar pengesahan tanda tangan.
- **Penyelarasan TP pada KBM LMS**:
  - `LmsMaterial` & `LmsAssignment`: Relasi `learningObjective()` (`belongsTo`) dan penyimpanan kolom `learning_objective_id`.
  - Pembaruan form unggah materi dan buat tugas dengan dropdown pemilih Tujuan Pembelajaran (TP).
  - Tampilan kartu materi dan penugasan pada `resources/views/lms/course.blade.php` dilengkapi badge indikator `🎯 TP {tp_code}`.
  - Tab cepat `📖 Jurnal Mengajar` pada ruang kelas LMS.
  - Tautan menu navigasi `📖 Jurnal Mengajar Guru` pada `resources/views/lms/partials/nav.blade.php`.
- **Pengujian Fitur (`tests/Feature/Phase4LearningExecutionAndJournalTest.php`)**: 2 feature tests memverifikasi penautan materi dan tugas ke TP, pengisian jurnal mengajar, verifikasi supervisi oleh Wakasek Kurikulum, dan pratinjau cetak agenda guru (30 assertions, lulus 100%).

---

## [Phase 3: Perencanaan Pembelajaran] — 2026-10-29

### Added
- **`App\Http\Controllers\CurriculumPlanController`**: Controller pusat perencanaan pembelajaran dengan alur lengkap CP, TP, ATP, Modul Ajar/RPP digital, integrasi asisten AI, pengajuan draf guru, peninjauan & pengesahan Wakasek Kurikulum, serta cetak dokumen resmi.
- **Rute Perencanaan (`routes/web.php`)**: Prefix `lms/planning` dengan 9 rute terproteksi (`index`, `outcomes.store`, `objectives.store`, `objectives.ai_generate`, `lesson_plans.store`, `lesson_plans.ai_generate`, `lesson_plans.submit`, `lesson_plans.review`, `lesson_plans.print`).
- **Antarmuka Pengguna & Cetak Dokumen**:
  - `resources/views/lms/planning/index.blade.php`: Tampilan portal perencanaan modern dengan tab Capaian Pembelajaran (CP), Tujuan Pembelajaran (TP), Alur Tujuan (ATP), dan Modul Ajar / RPP Digital beserta kartu metrik status, modal interaktif, dan integrasi asisten AI.
  - `resources/views/lms/planning/print_lesson_plan.blade.php`: Lembar cetak resmi A4 Modul Ajar dengan Kop Resmi Cabang Dinas Pendidikan Wilayah V SMAN 1 Lengkong, rincian langkah KBM 3 tahap, asesmen, LKPD, dan tanda tangan pengesahan Kepala Sekolah & Wakasek Kurikulum.
- **Navigasi LMS (`resources/views/lms/partials/nav.blade.php`)**: Ditambahkan tautan `📝 Perencanaan & Perangkat Ajar` dan `🩺 Kesiapan Sistem & Health`.
- **Pengujian Fitur (`tests/Feature/Phase3CurriculumPlanningTest.php`)**: 4 unit/feature tests memverifikasi perenderan dashboard perencanaan, penyimpanan CP & TP, respon asisten AI, penyusunan draf modul ajar guru, alur pengajuan ke kurikulum, pengesahan persetujuan Wakasek Kurikulum, dan pratinjau cetak dokumen (43 assertions, lulus 100%).

---

## [Phase 2: Database, Data Master, Auth & RBAC] — 2026-10-29

### Added
- **Migrasi `2026_10_29_230000_create_sma1le_curriculum_and_planning_tables.php`**: Menghadirkan tabel `learning_outcomes`, `learning_objectives`, `learning_objective_flows`, `annual_programs`, `lesson_plans`, `lesson_plan_tps`, `teacher_reflections`, `remediation_enrichment_plans`, `student_portfolios`, serta kolom `learning_objective_id` pada `lms_materials` & `lms_assignments`.
- **Model Eloquent**: `LearningOutcome`, `LearningObjective`, `LearningObjectiveFlow`, `AnnualProgram`, `LessonPlan`, `TeacherReflection`, `RemediationEnrichmentPlan`, `StudentPortfolio`.
- **Policy `App\Policies\LessonPlanPolicy`**: Kebijakan otorisasi server-side dan perlindungan anti-IDOR untuk kepemilikan guru serta hak review Wakasek Kurikulum.
- **Seeder `CurriculumMasterSeeder`**: Injeksi Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP) resmi untuk mata pelajaran Informatika Fase E (Kelas X).
- **Dokumentasi Wajib**:
  - `docs/ERD.md`: Diagram relasi basis data Mermaid.
  - `docs/DATA_DICTIONARY.md`: Kamus data rinci definisi tabel dan tipe kolom.
  - `docs/RBAC_MATRIX.md`: Matriks hak akses pengguna untuk 8 peran di 9 modul.
- **Test `tests/Feature/Phase2DatabaseAndRbacTest.php`**: 4 unit & feature tests untuk integritas model, relasi pivot, dan kebijakan otorisasi RBAC (20 assertions, lulus 100%).

---

## [Phase 1: Inisialisasi & Fondasi] — 2026-10-29

### Added
- **`App\Contracts\AiAdapterInterface`**: Abstraksi kontrak untuk penyedia kecerdasan buatan (AI) terisolasi.
- **`App\Services\Ai\MockAiAdapter`**: Adapter draf luring deterministik untuk lingkungan pengujian dan fallback.
- **`App\Services\Ai\GeminiAiAdapter`**: Adapter Google Gemini API resmi dengan timeout, multiple model fallback, dan penanganan kegagalan terisolasi.
- **`App\Services\Ai\AiCurriculumManager`**: Pengelola orkestrasi adapter AI dengan penegakan label status draf, disclaimer etika, dan sanitasi privasi siswa (penghapusan NIK/nomor HP).
- **`App\Services\CurriculumPlanService`**: Layanan dasar siklus status perencanaan (`DRAFT`, `SUBMITTED`, `REVISED`, `APPROVED`), skema baku komponen modul ajar, dan diagnostik kesiapan sistem.
- **Rute `GET /lms/health-check` (`lms.health`)**: Endpoint diagnostik dan metrik kesiapan sistem pembelajaran.
- **`tests/Feature/Phase1FoundationAndHealthTest.php`**: 6 unit/feature tests memverifikasi health check, sanitasi privasi AI, fallback adapter, dan skema kurikulum (45 assertions, lulus 100%).

### Changed
- **`.env.example`**: Ditambahkan konfigurasi `AI_PROVIDER`, `AI_TIMEOUT_SECONDS`, dan `LMS_PLAN_APPROVAL_REQUIRED` tanpa membocorkan rahasia.
- **`config/services.php`**: Ditambahkan blok konfigurasi `ai` terpusat.
- **`docs/IMPLEMENTATION_PLAN.md`**: Diperbarui mencatat penyelesaian Phase 1.

---

## [Phase 0: Audit & Perencanaan] — 2026-10-29

### Added
- **`docs/PROJECT_AUDIT.md`**: Laporan audit lingkungan (PHP 8.4/8.5, Laravel 13, MySQL, Tailwind CSS v4) dan audit komprehensif fitur LMS berjalan.
- **`docs/REQUIREMENTS.md`**: Pemisahan kebutuhan langsung dokumen master prompt vs rekomendasi sistem terpadu.
- **`docs/ARCHITECTURE.md`**: Arsitektur keterlacakan pembelajaran dan skema relasional non-destruktif.
- **`docs/IMPLEMENTATION_PLAN.md`**: Rencana 11 fase pembangunan bertahap.

### Fixed
- **Query Error 500 Dashboard**: Memperbaiki pengurutan `orderByDesc('borrowed_at')` menjadi `orderByDesc('id')` pada `DashboardController.php`.
- **Navigasi Halaman Tahfidz**: Menghapus sub-menu presensi yang tidak relevan pada `resources/views/tahfidz/index.blade.php`.
- **Sub-Menu Terpotong**: Memperbaiki kontainer navigasi pada 6 modul (`library`, `master`, `attendance`, `discipline`, `habits`, `lms`) menjadi `flex flex-wrap` responsif.


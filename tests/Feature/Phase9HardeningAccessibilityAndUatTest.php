<?php

use App\Models\AcademicSupervision;
use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\LessonPlan;
use App\Models\QuestionBank;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentPortfolio;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherReflection;
use App\Models\TeachingJournal;
use App\Models\User;

test('sub-menu navigations have overflow-x-auto and semantic accessibility tags', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('lms.index'));
    $response->assertStatus(200);
    $response->assertSee('aria-label="Navigasi Menu LMS & Kurikulum Merdeka"', false);
    $response->assertSee('overflow-x-auto scrollbar-thin', false);

    $responseAttendance = $this->actingAs($admin)->get(route('attendance.index'));
    $responseAttendance->assertStatus(200);
    $responseAttendance->assertSee('aria-label="Navigasi Menu Presensi"', false);
    $responseAttendance->assertSee('overflow-x-auto scrollbar-thin', false);

    $responseDiscipline = $this->actingAs($admin)->get(route('achievements.index'));
    $responseDiscipline->assertStatus(200);
    $responseDiscipline->assertSee('aria-label="Navigasi Menu Tata Tertib & Prestasi"', false);
    $responseDiscipline->assertSee('overflow-x-auto scrollbar-thin', false);

    $responseHabits = $this->actingAs($admin)->get(route('habits.journal'));
    $responseHabits->assertStatus(200);
    $responseHabits->assertSee('aria-label="Navigasi Menu 7 Kebiasaan Anak Hebat"', false);
    $responseHabits->assertSee('overflow-x-auto scrollbar-thin', false);

    $responseMaster = $this->actingAs($admin)->get(route('master.school_profile'));
    $responseMaster->assertStatus(200);
    $responseMaster->assertSee('aria-label="Navigasi Menu Data Master"', false);
    $responseMaster->assertSee('overflow-x-auto scrollbar-thin', false);
});

test('server-side anti-idor protects curriculum planning, journals, assessments, and reflections', function () {
    // 1. Setup Dua Guru Berbeda
    $userTeacherA = User::factory()->create(['role' => 'guru', 'name' => 'Guru A']);
    $teacherA = Teacher::create([
        'user_id' => $userTeacherA->id,
        'name' => 'Guru A Fisika',
    ]);

    $userTeacherB = User::factory()->create(['role' => 'guru', 'name' => 'Guru B']);
    $teacherB = Teacher::create([
        'user_id' => $userTeacherB->id,
        'name' => 'Guru B Biologi',
    ]);

    // Setup Siswa
    $userStudent = User::factory()->create(['role' => 'siswa', 'name' => 'Siswa X']);
    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-99',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'nisn' => '9988776655',
        'nis' => '12345',
        'name' => 'Siswa X',
        'class_id' => $schoolClass->id,
        'gender' => 'L',
    ]);

    $subjectA = Subject::create([
        'code' => 'TEST-IDOR-FIS',
        'name' => 'Fisika IDOR',
        'grade' => 'X',
        'group' => 'Pilihan',
        'kkm' => 75,
    ]);

    $courseClassA = CourseClass::create([
        'subject_id' => $subjectA->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacherA->id,
        'is_active' => true,
    ]);

    // Siswa TIDAK boleh membuat CP atau TP
    $respStudentCp = $this->actingAs($userStudent)->post(route('lms.planning.outcomes.store'), [
        'subject_id' => $subjectA->id,
        'phase' => 'E',
        'grade_level' => 'X',
        'code' => 'CP-ILLEGAL',
        'description' => 'Upaya ilegal siswa',
    ]);
    $respStudentCp->assertStatus(403);

    $respStudentTp = $this->actingAs($userStudent)->post(route('lms.planning.objectives.store'), [
        'subject_id' => $subjectA->id,
        'tp_code' => 'TP-ILLEGAL',
        'title' => 'Upaya ilegal siswa',
        'description' => 'Upaya ilegal siswa',
        'hours_allocation' => 2,
        'target_semester' => 'GANJIL',
    ]);
    $respStudentTp->assertStatus(403);

    // Guru B TIDAK boleh membuat jurnal pada kelas Guru A
    $respJournalB = $this->actingAs($userTeacherB)->post(route('lms.journals.store'), [
        'course_class_id' => $courseClassA->id,
        'date' => now()->toDateString(),
        'meeting_number' => 1,
        'topic' => 'Topik Ilegal Guru B',
        'teaching_activities' => 'Mencoba submit jurnal kelas orang lain',
        'learning_achievement_status' => 'TERCAPAI',
    ]);
    $respJournalB->assertStatus(403);

    // Buat jurnal oleh Guru A
    $journalA = TeachingJournal::create([
        'course_class_id' => $courseClassA->id,
        'teacher_id' => $teacherA->id,
        'date' => now()->toDateString(),
        'meeting_number' => 1,
        'topic' => 'Pengukuran Besaran Fisika',
        'teaching_activities' => 'Praktikum jangka sorong',
        'learning_achievement_status' => 'TERCAPAI',
    ]);

    // Guru B TIDAK boleh menghapus jurnal milik Guru A
    $respDeleteJournalB = $this->actingAs($userTeacherB)->delete(route('lms.journals.destroy', $journalA));
    $respDeleteJournalB->assertStatus(403);

    // Guru B TIDAK boleh memverifikasi jurnal (hanya kurikulum/admin)
    $respVerifyB = $this->actingAs($userTeacherB)->post(route('lms.journals.verify', $journalA), [
        'verification_notes' => 'Verifikasi tanpa hak',
    ]);
    $respVerifyB->assertStatus(403);

    // Guru B TIDAK boleh mencetak jurnal kelas Guru A
    $respPrintJournalB = $this->actingAs($userTeacherB)->get(route('lms.journals.print', ['course_class_id' => $courseClassA->id]));
    $respPrintJournalB->assertStatus(403);

    // Buat butir soal oleh Guru A
    $questionA = QuestionBank::create([
        'subject_id' => $subjectA->id,
        'teacher_id' => $teacherA->id,
        'grade_level' => 'X',
        'topic' => 'Vektor Fisika',
        'question_type' => 'multiple_choice',
        'cognitive_level' => 'L3',
        'question_text' => 'Berapakah resultan dua vektor tegak lurus?',
        'score_weight' => 5,
        'is_active' => true,
    ]);

    // Siswa TIDAK boleh membuat butir soal
    $respStudentQ = $this->actingAs($userStudent)->post(route('lms.assessment.bank.questions.store'), [
        'subject_id' => $subjectA->id,
        'grade_level' => 'X',
        'topic' => 'Soal Siswa',
        'question_type' => 'essay',
        'cognitive_level' => 'L1',
        'question_text' => 'Pertanyaan siswa',
        'score_weight' => 5,
    ]);
    $respStudentQ->assertStatus(403);

    // Guru B TIDAK boleh menghapus soal milik Guru A
    $respDeleteQA = $this->actingAs($userTeacherB)->delete(route('lms.assessment.bank.questions.destroy', $questionA));
    $respDeleteQA->assertStatus(403);

    // Guru B TIDAK boleh membuat rencana remedial pada kelas Guru A
    $respRemedialB = $this->actingAs($userTeacherB)->post(route('lms.assessment.remediation.store'), [
        'course_class_id' => $courseClassA->id,
        'type' => 'REMEDIAL',
        'topic' => 'Remedial Ilegal',
        'student_ids' => [$student->id],
    ]);
    $respRemedialB->assertStatus(403);

    // Guru B TIDAK boleh mencatat refleksi pada kelas Guru A
    $respReflectionB = $this->actingAs($userTeacherB)->post(route('lms.reflections.store'), [
        'course_class_id' => $courseClassA->id,
        'reflection_date' => now()->toDateString(),
        'success_notes' => 'Sukses',
        'challenges_notes' => 'Kendala',
        'follow_up_action' => 'Tindak lanjut',
    ]);
    $respReflectionB->assertStatus(403);

    // Siswa TIDAK boleh mencatat refleksi guru
    $respReflectionStudent = $this->actingAs($userStudent)->post(route('lms.reflections.store'), [
        'course_class_id' => $courseClassA->id,
        'reflection_date' => now()->toDateString(),
        'success_notes' => 'Sukses Siswa',
        'challenges_notes' => 'Kendala Siswa',
        'follow_up_action' => 'Tindak lanjut Siswa',
    ]);
    $respReflectionStudent->assertStatus(403);

    // Buat portofolio untuk siswa X
    $portfolio = StudentPortfolio::create([
        'student_id' => $student->id,
        'title' => 'Laporan Praktikum Vektor',
        'category' => 'Praktikum & Eksperimen',
        'is_featured' => false,
    ]);

    // Siswa lain (Siswa Y) TIDAK boleh mencetak/melihat portofolio unfeatured siswa X
    $userStudentY = User::factory()->create(['role' => 'siswa', 'name' => 'Siswa Y']);
    $studentY = Student::create([
        'user_id' => $userStudentY->id,
        'nisn' => '1122334455',
        'nis' => '67890',
        'name' => 'Siswa Y',
        'class_id' => $schoolClass->id,
        'gender' => 'P',
    ]);

    $respPrintPortoOther = $this->actingAs($userStudentY)->get(route('lms.portfolios.print', $portfolio));
    $respPrintPortoOther->assertStatus(403);

    // Siswa TIDAK boleh memverifikasi atau mengkurasi portofolio
    $respVerifyPortoStudent = $this->actingAs($userStudent)->post(route('lms.portfolios.verify', $portfolio));
    $respVerifyPortoStudent->assertStatus(403);

    // Guru biasa TIDAK boleh membuat supervisi akademik
    $respSupervisionTeacher = $this->actingAs($userTeacherB)->post(route('lms.supervision.store'), [
        'teacher_id' => $teacherA->id,
        'supervision_date' => now()->toDateString(),
        'planning_score' => 90,
        'execution_score' => 90,
        'assessment_score' => 90,
        'reflection_score' => 90,
    ]);
    $respSupervisionTeacher->assertStatus(403);

    // Buat supervisi oleh Admin untuk Guru A
    $admin = User::factory()->create(['role' => 'admin']);
    $supervisionA = AcademicSupervision::create([
        'teacher_id' => $teacherA->id,
        'supervisor_id' => $admin->id,
        'academic_year' => '2026/2027',
        'semester' => 'GANJIL',
        'supervision_date' => now()->toDateString(),
        'planning_score' => 95,
        'execution_score' => 90,
        'assessment_score' => 92,
        'reflection_score' => 88,
        'final_score' => 91.25,
        'predicate' => 'AMAT BAIK',
        'status' => 'FINAL',
    ]);

    // Guru B TIDAK boleh melihat atau mencetak hasil supervisi Guru A
    $respPrintSupervisionB = $this->actingAs($userTeacherB)->get(route('lms.supervision.print', $supervisionA));
    $respPrintSupervisionB->assertStatus(403);

    // Guru biasa TIDAK boleh mengekspor matriks kurikulum seluruh sekolah
    $respExportMatrixTeacher = $this->actingAs($userTeacherA)->get(route('lms.documents.export.matrix'));
    $respExportMatrixTeacher->assertStatus(403);
});

test('end-to-end uat lifecycle flow: cp -> tp -> modul ajar -> approval -> question bank -> teaching journal -> reflection -> portfolio -> supervision -> document print/export', function () {
    // Role Setup
    $userKurikulum = User::factory()->create(['role' => 'wakasek_kurikulum', 'name' => 'Wakasek Kurikulum']);
    $userTeacher = User::factory()->create(['role' => 'guru', 'name' => 'Guru Kimia SMAN 1 Lengkong']);
    $teacher = Teacher::create([
        'user_id' => $userTeacher->id,
        'name' => 'Guru Kimia SMAN 1 Lengkong',
        'nip' => '198501012010011005',
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'XI-MIPA-1',
        'grade' => 'XI',
        'academic_year' => '2026/2027',
    ]);

    $userStudent = User::factory()->create(['role' => 'siswa', 'name' => 'Ahmad Santoso']);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'nisn' => '0054321987',
        'nis' => '11223',
        'name' => 'Ahmad Santoso',
        'class_id' => $schoolClass->id,
        'gender' => 'L',
    ]);

    $subject = Subject::create([
        'code' => 'KIM-XI-UAT',
        'name' => 'Kimia Fase F',
        'grade' => 'XI',
        'group' => 'Pilihan',
        'kkm' => 75,
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    // ==========================================
    // STEP 1: RUMUSAN CAPAIAN & TUJUAN PEMBELAJARAN (CP & TP)
    // ==========================================
    $outcome = LearningOutcome::create([
        'subject_id' => $subject->id,
        'phase' => 'F',
        'grade_level' => 'XI',
        'code' => 'CP-KIM-F',
        'element' => 'Pemahaman Kimia',
        'description' => 'Peserta didik mampu menerapkan konsep laju reaksi dan kesetimbangan kimia dalam kehidupan.',
        'created_by' => $userTeacher->id,
        'is_active' => true,
    ]);

    $objective = LearningObjective::create([
        'subject_id' => $subject->id,
        'learning_outcome_id' => $outcome->id,
        'teacher_id' => $teacher->id,
        'tp_code' => '11.1',
        'title' => 'Menjelaskan Faktor Penentu Laju Reaksi',
        'description' => 'Menganalisis pengaruh konsentrasi, suhu, dan katalis terhadap laju reaksi kimia.',
        'hours_allocation' => 4,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    expect($outcome->id)->not->toBeNull()
        ->and($objective->id)->not->toBeNull();

    // ==========================================
    // STEP 2: PENYUSUNAN MODUL AJAR (RPP) DIGITAL GURU
    // ==========================================
    $plan = LessonPlan::create([
        'course_class_id' => $courseClass->id,
        'teacher_id' => $teacher->id,
        'title' => 'Modul Ajar Faktor-Faktor Laju Reaksi',
        'meeting_number' => 1,
        'time_allocation_minutes' => 90,
        'grade_level' => 'XI',
        'semester' => 'GANJIL',
        'target_profile' => ['Bernalar Kritis', 'Gotong Royong'],
        'learning_model' => 'Problem-Based Learning (PBL)',
        'learning_methods' => ['Diskusi Kelompok', 'Eksperimen'],
        'learning_steps' => [
            'introduction' => 'Apersepsi fenomena pembusukan makanan pada suhu ruangan',
            'core' => 'Praktikum pengaruh suhu terhadap reaksi cuka dan soda kue',
            'closing' => 'Refleksi bersama dan penyimpulan konsep energi aktivasi',
        ],
        'status' => 'DRAFT',
        'version' => 1,
        'is_published' => false,
    ]);
    $plan->objectives()->attach($objective->id);

    // Guru mengajukan modul ajar
    $responseSubmit = $this->actingAs($userTeacher)->post(route('lms.planning.lesson_plans.submit', $plan));
    $responseSubmit->assertRedirect();
    $plan->refresh();
    expect($plan->status)->toBe('SUBMITTED');

    // ==========================================
    // STEP 3: PENINJAUAN & PENGESAHAN OLEH WAKASEK KURIKULUM
    // ==========================================
    $responseReview = $this->actingAs($userKurikulum)->post(route('lms.planning.lesson_plans.review', $plan), [
        'action' => 'APPROVE',
        'review_notes' => 'Modul ajar sangat komprehensif dan memenuhi standar Kurikulum Merdeka.',
    ]);
    $responseReview->assertRedirect();
    $plan->refresh();
    expect($plan->status)->toBe('APPROVED')
        ->and($plan->is_published)->toBeTrue()
        ->and($plan->reviewed_by)->toBe($userKurikulum->id);

    // ==========================================
    // STEP 4: PEMBUATAN BUTIR SOAL TERHUBUNG TP DI BANK SOAL
    // ==========================================
    $question = QuestionBank::create([
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'learning_objective_id' => $objective->id,
        'grade_level' => 'XI',
        'topic' => 'Pengaruh Suhu terhadap Laju Reaksi',
        'question_type' => 'multiple_choice',
        'cognitive_level' => 'L3',
        'stimulus' => 'Data percobaan suhu 30C vs 50C pada larutan Na2S2O3',
        'question_text' => 'Mengapa kenaikan suhu 10C melipatgandakan laju reaksi?',
        'options' => [
            ['key' => 'A', 'text' => 'Meningkatkan energi aktivasi'],
            ['key' => 'B', 'text' => 'Memperbesar fraksi partikel yang memiliki energi kinetik cukup'],
            ['key' => 'C', 'text' => 'Menurunkan jumlah tumbukan'],
            ['key' => 'D', 'text' => 'Membuat larutan lebih encer'],
        ],
        'correct_answer' => 'B',
        'score_weight' => 10,
        'is_active' => true,
    ]);
    expect($question->id)->not->toBeNull();

    // ==========================================
    // STEP 5: PELAKSANAAN KBM & PENCATATAN JURNAL HARIAN
    // ==========================================
    $responseJournal = $this->actingAs($userTeacher)->post(route('lms.journals.store'), [
        'course_class_id' => $courseClass->id,
        'date' => now()->toDateString(),
        'meeting_number' => 1,
        'time_slot' => '07:30 - 09:00 WIB',
        'topic' => 'Eksperimen Pengaruh Suhu dan Konsentrasi',
        'learning_objective_id' => $objective->id,
        'lesson_plan_id' => $plan->id,
        'teaching_activities' => 'Demonstrasi eksperimen dan diskusi kelompok LKPD',
        'hadir_count' => 35,
        'sakit_count' => 1,
        'izin_count' => 0,
        'alfa_count' => 0,
        'learning_achievement_status' => 'TERCAPAI',
        'classroom_dynamics_notes' => 'Siswa sangat aktif saat melihat perubahan warna endapan belerang',
    ]);
    $responseJournal->assertRedirect();

    $journal = TeachingJournal::where('course_class_id', $courseClass->id)->first();
    expect($journal)->not->toBeNull()
        ->and($journal->learning_achievement_status)->toBe('TERCAPAI');

    // ==========================================
    // STEP 6: REFLEKSI PEMBELAJARAN PASCA KBM
    // ==========================================
    $responseReflection = $this->actingAs($userTeacher)->post(route('lms.reflections.store'), [
        'course_class_id' => $courseClass->id,
        'lesson_plan_id' => $plan->id,
        'reflection_date' => now()->toDateString(),
        'success_notes' => 'Praktikum berjalan tepat waktu dan 95% siswa memahami konsep tumbukan efektif.',
        'challenges_notes' => 'Dua kelompok membutuhkan waktu lebih lama dalam mencatat stopwatch.',
        'follow_up_action' => 'Memberikan pembagian peran yang lebih spesifik pada sesi praktikum berikutnya.',
        'status' => 'SELESAI',
    ]);
    $responseReflection->assertRedirect();

    $reflection = TeacherReflection::where('course_class_id', $courseClass->id)->first();
    expect($reflection)->not->toBeNull()
        ->and($reflection->status)->toBe('SELESAI');

    // ==========================================
    // STEP 7: PORTOFOLIO KARYA BELAJAR SISWA & KURASI
    // ==========================================
    $portfolio = StudentPortfolio::create([
        'student_id' => $student->id,
        'course_class_id' => $courseClass->id,
        'learning_objective_id' => $objective->id,
        'title' => 'Laporan Ilmiah Penyelidikan Laju Reaksi',
        'description' => 'Laporan praktikum mandiri dengan grafik regresi waktu versus konsentrasi',
        'category' => 'Praktikum & Eksperimen',
        'artifact_date' => now()->toDateString(),
        'is_featured' => false,
    ]);

    // Guru mengkurasi dan menyematkan sebagai karya unggulan
    $responseCurate = $this->actingAs($userTeacher)->post(route('lms.portfolios.verify', $portfolio), [
        'toggle_featured' => true,
    ]);
    $responseCurate->assertRedirect();
    $portfolio->refresh();
    expect($portfolio->is_featured)->toBeTrue()
        ->and($portfolio->verified_by)->toBe($userTeacher->id);

    // ==========================================
    // STEP 8: SUPERVISI AKADEMIK OLEH WAKASEK KURIKULUM
    // ==========================================
    $responseSupervision = $this->actingAs($userKurikulum)->post(route('lms.supervision.store'), [
        'teacher_id' => $teacher->id,
        'course_class_id' => $courseClass->id,
        'supervision_date' => now()->toDateString(),
        'instrument_type' => 'LENGKAP',
        'planning_score' => 96,
        'execution_score' => 94,
        'assessment_score' => 92,
        'reflection_score' => 90,
        'notes' => 'KBM berjalan sangat interaktif dengan diferensiasi proses yang jelas.',
        'recommendation' => 'Pertahankan dan dapat menjadi percontohan bagi guru kimia lain di MGMP.',
    ]);
    $responseSupervision->assertRedirect();

    $supervision = AcademicSupervision::where('teacher_id', $teacher->id)->first();
    expect($supervision)->not->toBeNull()
        ->and((float) $supervision->final_score)->toBe(93.0)
        ->and($supervision->predicate)->toBe('AMAT BAIK');

    // ==========================================
    // STEP 9: CETAK & EKSPOR DOKUMEN RESMI KURIKULUM
    // ==========================================
    // Cetak Modul Ajar
    $responsePrintPlan = $this->actingAs($userTeacher)->get(route('lms.planning.lesson_plans.print', $plan));
    $responsePrintPlan->assertStatus(200);
    $responsePrintPlan->assertSee('MODUL AJAR KURIKULUM MERDEKA');
    $responsePrintPlan->assertSee('Faktor-Faktor Laju Reaksi');

    // Cetak PROTA
    $responsePrintProta = $this->actingAs($userTeacher)->get(route('lms.documents.prota.print', ['course_class_id' => $courseClass->id]));
    $responsePrintProta->assertStatus(200);
    $responsePrintProta->assertSee('PROGRAM TAHUNAN (PROTA)');
    $responsePrintProta->assertSee('Kimia Fase F');

    // Cetak PROMES
    $responsePrintPromes = $this->actingAs($userTeacher)->get(route('lms.documents.promes.print', ['course_class_id' => $courseClass->id, 'semester' => 'GANJIL']));
    $responsePrintPromes->assertStatus(200);
    $responsePrintPromes->assertSee('PROGRAM SEMESTER (PROMES)');

    // Cetak ATP
    $responsePrintAtp = $this->actingAs($userTeacher)->get(route('lms.documents.atp.print', ['course_class_id' => $courseClass->id]));
    $responsePrintAtp->assertStatus(200);
    $responsePrintAtp->assertSee('ALUR TUJUAN PEMBELAJARAN (ATP)');

    // Cetak Lembar Portofolio
    $responsePrintPorto = $this->actingAs($userTeacher)->get(route('lms.portfolios.print', $portfolio));
    $responsePrintPorto->assertStatus(200);
    $responsePrintPorto->assertSee('LEMBAR KURASI & BUKTI PORTOFOLIO', false);

    // Cetak Lembar Supervisi
    $responsePrintSupervision = $this->actingAs($userKurikulum)->get(route('lms.supervision.print', $supervision));
    $responsePrintSupervision->assertStatus(200);
    $responsePrintSupervision->assertSee('LEMBAR HASIL SUPERVISI AKADEMIK GURU');
    $responsePrintSupervision->assertSee('AMAT BAIK');

    // Ekspor CSV Matriks Kurikulum oleh Wakasek Kurikulum
    $responseExportMatrix = $this->actingAs($userKurikulum)->get(route('lms.documents.export.matrix'));
    $responseExportMatrix->assertStatus(200);
    expect($responseExportMatrix->headers->get('content-type'))->toContain('text/csv');

    // Ekspor CSV Jurnal Mengajar oleh Guru
    $responseExportJournals = $this->actingAs($userTeacher)->get(route('lms.documents.export.journals', ['course_class_id' => $courseClass->id]));
    $responseExportJournals->assertStatus(200);
    expect($responseExportJournals->headers->get('content-type'))->toContain('text/csv');
});

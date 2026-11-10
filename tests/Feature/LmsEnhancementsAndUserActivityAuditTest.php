<?php

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\CourseClass;
use App\Models\LmsQuiz;
use App\Models\LmsSubjectAttendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    SchoolSetting::updateOrCreate(['id' => 1], [
        'school_name' => 'SMA Negeri 1 Lengkong',
        'school_address' => 'Jalan Raya Tegalega, Kec. Lengkong',
        'school_city' => 'Sukabumi',
        'headmaster_name' => 'Dr. H. Juanda, M.Pd.',
        'headmaster_nip' => '197001011995121001',
    ]);
});

test('1. user dapat mengedit, toggle aktif-nonaktif, dan menghapus kelas mapel LMS', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Budi Guru Fisika',
        'nip' => '198501012010011005',
    ]);

    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'ganjil',
        'is_active' => true,
    ]);

    $schoolClass = SchoolClass::create([
        'name' => 'XII MIPA 1',
        'grade' => 12,
    ]);

    $subject = Subject::create([
        'code' => 'FIS-12',
        'name' => 'Fisika Peminatan',
        'group' => 'MIPA',
        'kkm' => 75,
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'day_of_week' => 'Senin',
        'start_time' => '07:30',
        'end_time' => '09:00',
        'kktp' => 75,
        'is_active' => true,
    ]);

    // Test toggle status
    $this->actingAs($teacherUser)
        ->post(route('lms.master.course_classes.toggle_status', $courseClass->id))
        ->assertRedirect();

    $this->assertFalse((bool) $courseClass->fresh()->is_active);

    // Test update course class
    $this->actingAs($teacherUser)
        ->put(route('lms.master.course_classes.update', $courseClass->id), [
            'kktp' => 80,
            'description' => 'Kelas Fisika Lanjutan',
            'is_active' => 1,
            'schedule_days' => ['Selasa'],
            'schedule_starts' => ['08:00'],
            'schedule_ends' => ['09:30'],
        ])
        ->assertRedirect();

    $fresh = $courseClass->fresh();
    expect($fresh->kktp)->toBe(80)
        ->and($fresh->is_active)->toBeTrue()
        ->and($fresh->description)->toBe('Kelas Fisika Lanjutan');

    // Test delete course class
    $this->actingAs($teacherUser)
        ->delete(route('lms.master.course_classes.destroy', $courseClass->id))
        ->assertRedirect();

    $this->assertDatabaseMissing('course_classes', ['id' => $courseClass->id]);
});

test('2. guru mapel pengampu dapat memperbarui KKTP pada kelas pembelajarannya', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Biologi SMAN 1',
        'nip' => '198701012011011003',
    ]);

    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'ganjil',
        'is_active' => true,
    ]);

    $schoolClass = SchoolClass::create(['name' => 'XI MIPA 2', 'grade' => 11]);
    $subject = Subject::create(['code' => 'BIO-11', 'name' => 'Biologi', 'kkm' => 70]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'kktp' => 70,
        'is_active' => true,
    ]);

    // Update KKTP
    $this->actingAs($teacherUser)
        ->put(route('lms.kktp.update', $courseClass->id), [
            'kktp' => 82,
        ])
        ->assertRedirect();

    expect($courseClass->fresh()->getEffectiveKktp())->toBe(82);
});

test('3. presensi pertemuan mapel dapat diedit, dihapus, dicetak rekap, dan mendukung absensi multi-siswa', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Kimia SMAN 1',
        'nip' => '198801012012011002',
    ]);

    $academicYear = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
    $schoolClass = SchoolClass::create(['name' => 'X MIPA 1', 'grade' => 10]);
    $subject = Subject::create(['code' => 'KIM-10', 'name' => 'Kimia', 'kkm' => 75]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    // Buat 3 Siswa
    $s1 = Student::create(['class_id' => $schoolClass->id, 'name' => 'Ahmad Santoso', 'nis' => '1001', 'nisn' => '0011223341']);
    $s2 = Student::create(['class_id' => $schoolClass->id, 'name' => 'Bunga Citra', 'nis' => '1002', 'nisn' => '0011223342']);
    $s3 = Student::create(['class_id' => $schoolClass->id, 'name' => 'Cahya Gumilar', 'nis' => '1003', 'nisn' => '0011223343']);

    $attendance = LmsSubjectAttendance::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Pertemuan 1: Konsep Mol',
        'meeting_date' => now()->toDateString(),
        'start_time' => '07:30',
        'end_time' => '09:00',
        'attendance_method' => 'both',
        'is_open' => true,
        'created_by' => $teacher->id,
    ]);

    // Test Edit Sesi Presensi
    $this->actingAs($teacherUser)
        ->put(route('lms.attendances.update', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]), [
            'title' => 'Pertemuan 1: Konsep Mol & Stoikiometri',
            'attendance_method' => 'manual',
            'notes' => 'Catatan revisi tatap muka',
        ])
        ->assertRedirect();

    expect($attendance->fresh()->title)->toBe('Pertemuan 1: Konsep Mol & Stoikiometri')
        ->and($attendance->fresh()->attendance_method)->toBe('manual');

    // Test Presensi Multi-Siswa (Aksi Massal Checklist)
    $this->actingAs($teacherUser)
        ->post(route('lms.attendances.manual', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]), [
            'selected_students' => [$s1->id, $s2->id],
            'bulk_status' => 'hadir',
            'bulk_notes' => 'Hadir tepat waktu di laboratorium',
            'statuses' => [
                $s3->id => 'sakit',
            ],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('lms_subject_attendance_logs', [
        'attendance_id' => $attendance->id,
        'student_id' => $s1->id,
        'status' => 'hadir',
    ]);
    $this->assertDatabaseHas('lms_subject_attendance_logs', [
        'attendance_id' => $attendance->id,
        'student_id' => $s2->id,
        'status' => 'hadir',
    ]);
    $this->assertDatabaseHas('lms_subject_attendance_logs', [
        'attendance_id' => $attendance->id,
        'student_id' => $s3->id,
        'status' => 'sakit',
    ]);

    // Test Cetak Rekap Pertemuan (Kepala Sekolah selalu di kiri)
    $responsePrint = $this->actingAs($teacherUser)
        ->get(route('lms.attendances.print', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]));
    $responsePrint->assertOk();
    $responsePrint->assertSee('DAFTAR PRESENSI PERTEMUAN');
    $responsePrint->assertSee('Kepala Sekolah');

    // Test Cetak Rekapitulasi Presensi Lengkap
    $responseRecap = $this->actingAs($teacherUser)
        ->get(route('lms.attendances.print_recap', $courseClass->id));
    $responseRecap->assertOk();
    $responseRecap->assertSee('REKAPITULASI PRESENSI PEMBELAJARAN');

    // Test Delete Sesi Presensi
    $this->actingAs($teacherUser)
        ->delete(route('lms.attendances.destroy', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('lms_subject_attendances', ['id' => $attendance->id]);
});

test('4. pembuatan asesmen/cbt manual mendukung komposisi bentuk soal multi-tipe', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Matematika SMAN 1',
        'nip' => '198901012013011004',
    ]);

    $academicYear = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
    $schoolClass = SchoolClass::create(['name' => 'XII MIPA 3', 'grade' => 12]);
    $subject = Subject::create(['code' => 'MAT-12', 'name' => 'Matematika Peminatan', 'kkm' => 75]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($teacherUser)
        ->post(route('lms.quizzes.store', $courseClass->id), [
            'title' => 'Asesmen Formatif Multi-Tipe Limit Fungsi',
            'quiz_type' => 'formatif',
            'duration_minutes' => 45,
            'passing_score' => 75,
            'count_multiple_choice' => 2,
            'count_complex_mc' => 1,
            'count_true_false' => 1,
            'count_matching' => 1,
            'count_short_answer' => 1,
            'count_essay' => 1,
        ]);

    $response->assertRedirect();
    $quiz = LmsQuiz::where('course_class_id', $courseClass->id)->first();
    expect($quiz)->not->toBeNull()
        ->and($quiz->questions()->count())->toBe(7);

    // Cek bahwa butir soal multi-tipe terbentuk
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'multiple_choice']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'complex_mc']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'true_false']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'matching']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'short_answer']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'essay']);
});

test('5. laporan audit log aktivitas pengguna menampilkan frekuensi login dan aktivitas guru, staff, dan siswa', function () {
    $adminUser = User::factory()->create(['role' => 'admin']);
    $guruUser = User::factory()->create(['name' => 'Guru Sejarah', 'role' => 'guru']);
    $siswaUser = User::factory()->create(['name' => 'Siswa Pintar', 'role' => 'siswa']);

    // Log beberapa sesi masuk (login)
    AuditLog::create(['user_id' => $guruUser->id, 'action' => 'LOGIN', 'ip_address' => '192.168.1.10']);
    AuditLog::create(['user_id' => $guruUser->id, 'action' => 'LOGIN', 'ip_address' => '192.168.1.10']);
    AuditLog::create(['user_id' => $guruUser->id, 'action' => 'LMS_MATERIAL_CREATED', 'ip_address' => '192.168.1.10']);

    AuditLog::create(['user_id' => $siswaUser->id, 'action' => 'LOGIN', 'ip_address' => '192.168.1.20']);
    AuditLog::create(['user_id' => $siswaUser->id, 'action' => 'LMS_ASSIGNMENT_SUBMITTED', 'ip_address' => '192.168.1.20']);

    // Akses laporan utama
    $response = $this->actingAs($adminUser)->get(route('reports.user_activity'));
    $response->assertOk();
    $response->assertSee('Frekuensi Masuk Sistem');
    $response->assertSee('Guru Sejarah');
    $response->assertSee('Siswa Pintar');

    // Akses filter guru
    $responseGuru = $this->actingAs($adminUser)->get(route('reports.user_activity', ['role' => 'guru']));
    $responseGuru->assertOk();
    $responseGuru->assertSee('Guru Sejarah');

    // Akses filter siswa
    $responseSiswa = $this->actingAs($adminUser)->get(route('reports.user_activity', ['role' => 'siswa']));
    $responseSiswa->assertOk();
    $responseSiswa->assertSee('Siswa Pintar');

    // Cetak Dokumen Resmi (Kepala Sekolah harus di sebelah KIRI)
    $responsePrint = $this->actingAs($adminUser)->get(route('reports.print_user_activity'));
    $responsePrint->assertOk();
    $responsePrint->assertSee('REKAPITULASI FREKUENSI AKSES');
    $responsePrint->assertSee('Kepala Sekolah');

    // Detail aktivitas pengguna
    $responseDetail = $this->actingAs($adminUser)->get(route('reports.user_activity_detail', $guruUser->id));
    $responseDetail->assertOk();
    $responseDetail->assertSee('Detail Aktivitas: Guru Sejarah');
    $responseDetail->assertSee('2 Kali');
});

test('6. rute /lms dapat diakses tanpa error undefined variable currentTeacher dan /reports/user-activity bebas dari kolom teacher_id', function () {
    $adminUser = User::factory()->create(['role' => 'admin']);
    $guruUser = User::factory()->create(['name' => 'Guru Biologi', 'role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $guruUser->id,
        'name' => 'Guru Biologi',
        'nip' => '198703042011011001',
    ]);

    $academicYear = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
    $schoolClass = SchoolClass::create(['name' => 'XII MIPA 2', 'grade' => 12]);
    $subject = Subject::create(['code' => 'BIO-12', 'name' => 'Biologi', 'kkm' => 75]);

    CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    // Akses LMS oleh admin tanpa error Undefined variable $currentTeacher
    $responseAdmin = $this->actingAs($adminUser)->get(route('lms.index'));
    $responseAdmin->assertOk();
    $responseAdmin->assertSee('Daftar Kelas & Mata Pelajaran', false);

    // Akses LMS oleh guru pengampu tanpa error Undefined variable $currentTeacher
    $responseGuru = $this->actingAs($guruUser)->get(route('lms.index'));
    $responseGuru->assertOk();
    $responseGuru->assertSee('Daftar Kelas & Mata Pelajaran', false);

    // Akses laporan aktivitas tanpa error QueryException
    $responseReport = $this->actingAs($adminUser)->get(route('reports.user_activity', ['role' => 'guru']));
    $responseReport->assertOk();
    $responseReport->assertSee('Guru Biologi');

    $responsePrint = $this->actingAs($adminUser)->get(route('reports.print_user_activity', ['role' => 'guru']));
    $responsePrint->assertOk();
    $responsePrint->assertSee('Guru Biologi');
});

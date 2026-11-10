<?php

use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\LessonPlan;
use App\Models\RemediationEnrichmentPlan;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentPortfolio;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherReflection;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('learning outcome and objective models relate correctly to subject and teacher', function () {
    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-SUB-01',
        'name' => 'Informatika Uji',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::first() ?? Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Pengampu Uji',
    ]);

    $outcome = LearningOutcome::create([
        'subject_id' => $subject->id,
        'phase' => 'E',
        'grade_level' => 'X',
        'code' => 'CP-TEST-01',
        'element' => 'Uji Kompetensi Dasar',
        'description' => 'Peserta didik memahami konsep logika pemrograman terstruktur.',
        'is_active' => true,
    ]);

    expect($outcome->subject->id)->toBe($subject->id);

    $objective = LearningObjective::create([
        'learning_outcome_id' => $outcome->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'tp_code' => '10.99',
        'title' => 'Memahami Struktur Kontrol Percabangan',
        'description' => 'Mampu menuliskan algoritma if-else bertingkat.',
        'hours_allocation' => 2,
        'target_semester' => 'GANJIL',
    ]);

    expect($objective->outcome->id)->toBe($outcome->id);
    expect($objective->teacher->id)->toBe($teacher->id);
    expect($outcome->objectives)->toHaveCount(1);
});

test('lesson plan can link to multiple learning objectives via pivot and defaults to draft', function () {
    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-SUB-02',
        'name' => 'Matematika Uji',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::first() ?? Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Pengampu Uji',
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-1',
        'grade' => 'X',
    ]);

    $courseClass = CourseClass::first() ?? CourseClass::create([
        'teacher_id' => $teacher->id,
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'day_of_week' => 'Senin',
        'start_time' => '07:30',
        'end_time' => '09:00',
    ]);

    $objective = LearningObjective::first() ?? LearningObjective::create([
        'subject_id' => $courseClass->subject_id,
        'tp_code' => '10.88',
        'title' => 'Tujuan Uji',
        'description' => 'Deskripsi TP Uji',
    ]);

    $plan = LessonPlan::create([
        'course_class_id' => $courseClass->id,
        'teacher_id' => $courseClass->teacher_id,
        'title' => 'Modul Ajar Pertemuan 1 - Konsep Pemrograman',
        'meeting_number' => 1,
        'time_allocation_minutes' => 90,
        'grade_level' => 'X',
        'semester' => 'GANJIL',
        'status' => 'DRAFT',
        'version' => 1,
    ]);

    $plan->objectives()->attach($objective->id);

    expect($plan->status)->toBe('DRAFT');
    expect($plan->objectives)->toHaveCount(1);
    expect($plan->courseClass->id)->toBe($courseClass->id);
});

test('lesson plan policy enforces ownership isolation and wakasek review authorization', function () {
    $teacherUser1 = User::factory()->create(['role' => 'guru']);
    $teacher1 = Teacher::create(['user_id' => $teacherUser1->id, 'name' => 'Guru Pengampu 1']);

    $teacherUser2 = User::factory()->create(['role' => 'guru']);
    $teacher2 = Teacher::create(['user_id' => $teacherUser2->id, 'name' => 'Guru Pengampu 2']);

    $wakasekUser = User::factory()->create(['role' => 'wakasek_kurikulum']);
    $adminUser = User::factory()->create(['role' => 'admin']);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-SUB-03',
        'name' => 'Biologi Uji',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-2',
        'grade' => 'X',
    ]);

    $courseClass = CourseClass::create([
        'teacher_id' => $teacher1->id,
        'subject_id' => $subject->id,
        'class_id' => $class->id,
    ]);

    $plan = LessonPlan::create([
        'course_class_id' => $courseClass->id,
        'teacher_id' => $teacher1->id,
        'title' => 'Modul Ajar Eksklusif Guru 1',
        'status' => 'DRAFT',
    ]);

    // Guru 1 (pemilik) boleh melihat dan mengupdate draf
    expect(Gate::forUser($teacherUser1)->allows('view', $plan))->toBeTrue();
    expect(Gate::forUser($teacherUser1)->allows('update', $plan))->toBeTrue();

    // Guru 2 (bukan pemilik) DITOLAK melihat dan mengupdate (Anti-IDOR)
    expect(Gate::forUser($teacherUser2)->allows('update', $plan))->toBeFalse();
    expect(Gate::forUser($teacherUser2)->allows('view', $plan))->toBeFalse();

    // Wakasek Kurikulum boleh review dan melihat
    expect(Gate::forUser($wakasekUser)->allows('view', $plan))->toBeTrue();
    expect(Gate::forUser($wakasekUser)->allows('review', $plan))->toBeTrue();

    // Guru biasa TIDAK boleh review (approval)
    expect(Gate::forUser($teacherUser1)->allows('review', $plan))->toBeFalse();
});

test('teacher reflection, remediation plan, and student portfolio models operate correctly', function () {
    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-SUB-04',
        'name' => 'Fisika Uji',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::first() ?? Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Pengampu Uji',
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-3',
        'grade' => 'X',
    ]);

    $courseClass = CourseClass::first() ?? CourseClass::create([
        'teacher_id' => $teacher->id,
        'subject_id' => $subject->id,
        'class_id' => $class->id,
    ]);

    $student = Student::first() ?? Student::factory()->create(['class_id' => $class->id]);

    $reflection = TeacherReflection::create([
        'course_class_id' => $courseClass->id,
        'teacher_id' => $teacher->id,
        'reflection_date' => now()->toDateString(),
        'success_notes' => 'Siswa sangat aktif saat sesi tanya jawab.',
        'challenges_notes' => 'Sebagian siswa lambat saat membuka tautan simulasi.',
        'follow_up_action' => 'Menyediakan ringkasan offline untuk pertemuan berikutnya.',
        'status' => 'SELESAI',
    ]);

    expect($reflection->status)->toBe('SELESAI');
    expect($reflection->courseClass->id)->toBe($courseClass->id);

    $remedial = RemediationEnrichmentPlan::create([
        'course_class_id' => $courseClass->id,
        'teacher_id' => $teacher->id,
        'type' => 'REMEDIAL',
        'topic' => 'Penguatan Logika Algoritma',
        'description' => 'Pendampingan khusus tutor sebaya untuk siswa remedi.',
        'student_ids' => [$student->id],
        'status' => 'DRAFT',
    ]);

    expect($remedial->type)->toBe('REMEDIAL');
    expect($remedial->student_ids)->toContain($student->id);

    $portfolio = StudentPortfolio::create([
        'student_id' => $student->id,
        'course_class_id' => $courseClass->id,
        'title' => 'Infografis Etika Digital Siswa',
        'category' => 'Tugas Terbaik',
        'is_featured' => true,
    ]);

    expect($portfolio->is_featured)->toBeTrue();
    expect($portfolio->student->id)->toBe($student->id);
});

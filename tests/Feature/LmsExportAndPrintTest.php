<?php

use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'SchoolMasterSeeder']);
    $this->artisan('db:seed', ['--class' => 'LmsSeeder']);

    // Set custom Kop and Headmaster name in SchoolSetting
    SchoolSetting::set('school_kop', 'kop/kop_official.png');
    SchoolSetting::set('school_name', 'SMAN 1 LELANG KELAS');
    SchoolSetting::set('headmaster_name', 'Dr. H. Sulaiman, M.Pd.');
    SchoolSetting::set('headmaster_nip', '197505122000031001');
});

test('assignment excel and print-pdf endpoints render correctly with kop and headmaster data', function () {
    $courseClass = CourseClass::first();
    $teacher = User::find($courseClass->teacher_id);
    if (! $teacher) {
        $teacher = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
        $courseClass->update(['teacher_id' => $teacher->id]);
    }

    $assignment = LmsAssignment::where('course_class_id', $courseClass->id)->first();
    if (! $assignment) {
        $assignment = LmsAssignment::create([
            'course_class_id' => $courseClass->id,
            'title' => 'Tugas Uji Coba Export',
            'topic' => 'Bab 1',
            'instructions' => 'Petunjuk tugas',
            'due_date' => now()->addDays(3),
            'max_score' => 100,
            'is_published' => true,
        ]);
    }

    $student = Student::first();
    if ($student) {
        LmsAssignmentSubmission::firstOrCreate([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
        ], [
            'score' => 95,
            'status' => 'graded',
            'submitted_at' => now(),
        ]);
    }

    // 1. Test Excel Export
    $excelResponse = $this->actingAs($teacher)->get(route('lms.assignments.export_excel', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignment->id,
    ]));

    $excelResponse->assertStatus(200);
    $excelResponse->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
    expect($excelResponse->headers->get('Content-Disposition'))->toContain('.xls');
    $excelResponse->assertSee('REKAPITULASI HASIL PENUGASAN');
    $excelResponse->assertSee($assignment->title);

    // 2. Test PDF / Print View with KOP and Headmaster
    $printResponse = $this->actingAs($teacher)->get(route('lms.assignments.print_pdf', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignment->id,
    ]));

    $printResponse->assertStatus(200);
    $printResponse->assertSee('Dr. H. Sulaiman, M.Pd.');
    $printResponse->assertSee('197505122000031001');
    $printResponse->assertSee('kop_official.png');
    $printResponse->assertSee($assignment->title);
    $printResponse->assertSee('window.print()', false);
});

test('quiz excel and print-pdf endpoints render correctly with kop and headmaster data', function () {
    $courseClass = CourseClass::first();
    $teacher = User::find($courseClass->teacher_id);
    if (! $teacher) {
        $teacher = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
        $courseClass->update(['teacher_id' => $teacher->id]);
    }

    $quiz = LmsQuiz::where('course_class_id', $courseClass->id)->first();
    if (! $quiz) {
        $quiz = LmsQuiz::create([
            'course_class_id' => $courseClass->id,
            'title' => 'Asesmen CBT Uji Coba',
            'duration_minutes' => 60,
            'passing_score' => 75,
            'is_published' => true,
        ]);
    }

    $student = Student::first();
    if ($student) {
        LmsQuizAttempt::firstOrCreate([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
        ], [
            'score' => 88,
            'started_at' => now()->subMinutes(30),
            'completed_at' => now(),
        ]);
    }

    // 1. Test Excel Export
    $excelResponse = $this->actingAs($teacher)->get(route('lms.quizzes.export_excel', [
        'courseClass' => $courseClass->id,
        'quiz' => $quiz->id,
    ]));

    $excelResponse->assertStatus(200);
    $excelResponse->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
    expect($excelResponse->headers->get('Content-Disposition'))->toContain('.xls');
    $excelResponse->assertSee('REKAPITULASI HASIL ASESMEN &amp; CBT', false);
    $excelResponse->assertSee($quiz->title);

    // 2. Test PDF / Print View with KOP and Headmaster
    $printResponse = $this->actingAs($teacher)->get(route('lms.quizzes.print_pdf', [
        'courseClass' => $courseClass->id,
        'quiz' => $quiz->id,
    ]));

    $printResponse->assertStatus(200);
    $printResponse->assertSee('Dr. H. Sulaiman, M.Pd.');
    $printResponse->assertSee('kop_official.png');
    $printResponse->assertSee($quiz->title);
    $printResponse->assertSee('window.print()', false);
});

test('gradebook excel and print-pdf endpoints render correctly with kop, headmaster signature and composite grades', function () {
    $courseClass = CourseClass::first();
    $teacher = User::find($courseClass->teacher_id);
    if (! $teacher) {
        $teacher = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
        $courseClass->update(['teacher_id' => $teacher->id]);
    }

    // 1. Test Excel Export
    $excelResponse = $this->actingAs($teacher)->get(route('lms.grades.export_excel', $courseClass->id));

    $excelResponse->assertStatus(200);
    $excelResponse->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
    expect($excelResponse->headers->get('Content-Disposition'))->toContain('.xls');
    $excelResponse->assertSee('BUKU REKAPITULASI NILAI AKHIR');
    $excelResponse->assertSee('Materi');
    $excelResponse->assertSee('Tugas');
    $excelResponse->assertSee('CBT');
    $excelResponse->assertSee('Presensi');
    $excelResponse->assertSee('Nilai Akhir');
    $excelResponse->assertSee('Status KKTP');

    // 2. Test PDF / Print View with KOP and Headmaster
    $printResponse = $this->actingAs($teacher)->get(route('lms.grades.print_pdf', $courseClass->id));

    $printResponse->assertStatus(200);
    $printResponse->assertSee('Dr. H. Sulaiman, M.Pd.');
    $printResponse->assertSee('197505122000031001');
    $printResponse->assertSee('kop_official.png');
    $printResponse->assertSee('BUKU REKAPITULASI NILAI AKHIR', false);
    $printResponse->assertSee('window.print()', false);
    $printResponse->assertSee('A4');
    $printResponse->assertSee('F4');
});

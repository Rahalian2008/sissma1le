<?php

use App\Models\AcademicYear;
use App\Models\CourseClass;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\SchoolClass;
use App\Models\SchoolHoliday;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('school holiday can target specific classes or all classes', function () {
    $classA = SchoolClass::create(['name' => 'X-1', 'grade' => 'X', 'order' => 1]);
    $classB = SchoolClass::create(['name' => 'X-2', 'grade' => 'X', 'order' => 2]);

    $allHoliday = SchoolHoliday::create([
        'name' => 'Libur Nasional',
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-01',
        'type' => 'nasional',
        'is_active' => true,
        'target_classes' => null,
    ]);

    $classAHoliday = SchoolHoliday::create([
        'name' => 'Study Tour X-1',
        'start_date' => '2026-11-05',
        'end_date' => '2026-11-05',
        'type' => 'khusus',
        'is_active' => true,
        'target_classes' => [$classA->id],
    ]);

    expect($allHoliday->appliesToClass($classA->id))->toBeTrue()
        ->and($allHoliday->appliesToClass($classB->id))->toBeTrue()
        ->and($classAHoliday->appliesToClass($classA->id))->toBeTrue()
        ->and($classAHoliday->appliesToClass($classB->id))->toBeFalse();

    expect(SchoolHoliday::getHoliday('2026-11-05', $classA->id)?->name)->toBe('Study Tour X-1')
        ->and(SchoolHoliday::getHoliday('2026-11-05', $classB->id))->toBeNull();
});

test('manual quiz creation redirects directly to quiz show with open_input to input questions', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Penilai',
        'nip' => '198701012010011001',
    ]);
    $schoolClass = SchoolClass::create(['name' => 'X-MIPA-1', 'grade' => 'X', 'order' => 1]);
    $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK']);
    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);

    $courseClass = CourseClass::create([
        'teacher_id' => $teacher->id,
        'class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'academic_year_id' => $academicYear->id,
    ]);

    $response = $this->actingAs($teacherUser)->post(route('lms.quizzes.store', $courseClass->id), [
        'title' => 'Asesmen Harian Aljabar',
        'quiz_type' => 'formatif',
        'duration_minutes' => 60,
        'passing_score' => 75,
        'start_time' => '2026-11-10 08:00:00',
        'end_time' => '2026-11-10 10:00:00',
        'shuffle_questions' => '1',
        'is_published' => '1',
    ]);

    $quiz = LmsQuiz::first();
    expect($quiz)->not->toBeNull()
        ->and($quiz->title)->toBe('Asesmen Harian Aljabar')
        ->and($quiz->duration_minutes)->toBe(60);

    $response->assertRedirect(route('lms.quizzes.show', [
        'courseClass' => $courseClass->id,
        'quiz' => $quiz->id,
        'open_input' => 1,
    ]));
    $response->assertSessionHas('open_add_question', true);
});

test('teacher can create essay question and student can submit essay answer', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Penguji',
        'nip' => '198701012010011002',
    ]);
    $schoolClass = SchoolClass::create(['name' => 'XII-IPA-1', 'grade' => 'XII', 'order' => 1]);
    $subject = Subject::create(['name' => 'Bahasa Indonesia', 'code' => 'BIND']);
    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);

    $courseClass = CourseClass::create([
        'teacher_id' => $teacher->id,
        'class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'academic_year_id' => $academicYear->id,
    ]);

    $quiz = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Asesmen Teks Argumentasi',
        'quiz_type' => 'formatif',
        'duration_minutes' => 45,
        'passing_score' => 70,
        'is_published' => true,
    ]);

    // Create essay question
    $resQ = $this->actingAs($teacherUser)->post(route('lms.quizzes.questions.store', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]), [
        'question_type' => 'essay',
        'question_text' => 'Jelaskan perbedaan fakta dan opini dalam teks argumentasi!',
        'score_weight' => 25,
        'explanation' => 'Fakta bersifat objektif dapat diverifikasi, sedangkan opini bersifat subjektif.',
    ]);
    $resQ->assertRedirect();

    $essayQ = LmsQuizQuestion::first();
    expect($essayQ)->not->toBeNull()
        ->and($essayQ->question_type)->toBe('essay')
        ->and($essayQ->score_weight)->toBe(25);

    // Student submits essay
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'name' => 'Budi Santoso',
        'nisn' => '0098765432',
        'nis' => '12345',
        'class_id' => $schoolClass->id,
        'gender' => 'L',
    ]);

    $submitRes = $this->actingAs($studentUser)->post(route('lms.quizzes.submit', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]), [
        'answers' => [
            $essayQ->id => 'Fakta adalah peristiwa yang sudah terbukti kebenarannya, opini adalah pendapat pribadi seseorang.',
        ],
    ]);
    $submitRes->assertRedirect();

    $attempt = LmsQuizAttempt::first();
    expect($attempt)->not->toBeNull()
        ->and($attempt->student_id)->toBe($student->id)
        ->and($attempt->answers[$essayQ->id]['type'])->toBe('essay')
        ->and($attempt->answers[$essayQ->id]['student_answer'])->toContain('Fakta adalah peristiwa');
});

test('print documents retrieve Kop from Logo & Kop Sekolah and headmaster info from Identitas Sekolah', function () {
    SchoolSetting::set('school_kop', 'branding/test_kop.png');
    SchoolSetting::set('headmaster_name', 'Pian Sopian, S.Pd., M.M.Pd');
    SchoolSetting::set('headmaster_nip', '198009132008011005');

    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Mapel',
        'nip' => '198701012010011003',
    ]);
    $schoolClass = SchoolClass::create(['name' => 'XI-IPA-1', 'grade' => 'XI', 'order' => 1]);
    $subject = Subject::create(['name' => 'Fisika', 'code' => 'FIS']);
    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);

    $courseClass = CourseClass::create([
        'teacher_id' => $teacher->id,
        'class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'academic_year_id' => $academicYear->id,
    ]);

    $quiz = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Asesmen Kinematika',
        'is_published' => true,
    ]);

    $res = $this->actingAs($teacherUser)->get(route('lms.quizzes.kisi_kisi', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]));
    $res->assertStatus(200);
    $res->assertSee('Pian Sopian, S.Pd., M.M.Pd');
    $res->assertSee('198009132008011005');
    $res->assertSee('Sukabumi,');
    $res->assertSee('branding/test_kop.png');
});

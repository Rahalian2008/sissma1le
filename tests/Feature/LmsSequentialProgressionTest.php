<?php

use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsMaterial;
use App\Models\LmsMaterialRead;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'SchoolMasterSeeder']);
    $this->artisan('db:seed', ['--class' => 'LmsSeeder']);
});

test('students cannot access or open material 2 before completing material 1', function () {
    $courseClass = CourseClass::first();
    $studentUser = User::where('role', 'siswa')->first();
    $student = $studentUser->student;

    // Reset reads for this student
    LmsMaterialRead::where('student_id', $student->id)->delete();

    // Create 3 materials in order
    $mat1 = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Materi Tahap 1: Pengantar Dasar',
        'topic' => 'Bab 1',
        'content_type' => 'text',
        'content_text' => 'Konten dasar materi 1.',
        'is_published' => true,
    ]);

    $mat2 = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Materi Tahap 2: Konsep Lanjutan',
        'topic' => 'Bab 2',
        'content_type' => 'text',
        'content_text' => 'Konten lanjutan materi 2.',
        'is_published' => true,
    ]);

    $mat3 = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Materi Tahap 3: Praktik & Analisis',
        'topic' => 'Bab 3',
        'content_type' => 'text',
        'content_text' => 'Konten analisis materi 3.',
        'is_published' => true,
    ]);

    // Ensure all earlier materials (if any from seeder) are marked read so mat1 is the active prerequisite
    LmsMaterial::where('course_class_id', $courseClass->id)
        ->where('id', '<', $mat1->id)
        ->get()
        ->each(fn ($m) => LmsMaterialRead::create(['material_id' => $m->id, 'student_id' => $student->id, 'read_at' => now()]));

    // 1. Siswa BISA membuka Materi 1 (Materi pertama yang belum dibaca)
    $resMat1 = $this->actingAs($studentUser)->get(route('lms.materials.show', [
        'courseClass' => $courseClass->id,
        'material' => $mat1->id,
    ]));
    $resMat1->assertStatus(200);
    $resMat1->assertSee('Materi Tahap 1: Pengantar Dasar');

    // 2. Siswa TIDAK BISA membuka Materi 2 (Harus menyelesaikan Materi 1 terlebih dahulu)
    $resMat2Blocked = $this->actingAs($studentUser)->get(route('lms.materials.show', [
        'courseClass' => $courseClass->id,
        'material' => $mat2->id,
    ]));
    $resMat2Blocked->assertRedirect(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi']));
    $resMat2Blocked->assertSessionHas('error');

    // 3. Siswa menandai Materi 1 sebagai selesai
    $resRead1 = $this->actingAs($studentUser)->post(route('lms.materials.read', [
        'courseClass' => $courseClass->id,
        'material' => $mat1->id,
    ]));
    $resRead1->assertRedirect(route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $mat2->id]));
    $resRead1->assertSessionHas('success');

    // 4. Sekarang Materi 2 telah TERBUKA dan bisa diakses
    $resMat2Open = $this->actingAs($studentUser)->get(route('lms.materials.show', [
        'courseClass' => $courseClass->id,
        'material' => $mat2->id,
    ]));
    $resMat2Open->assertStatus(200);
    $resMat2Open->assertSee('Materi Tahap 2: Konsep Lanjutan');

    // 5. Materi 3 masih TERKUNCI karena Materi 2 belum diselesaikan
    $resMat3Blocked = $this->actingAs($studentUser)->get(route('lms.materials.show', [
        'courseClass' => $courseClass->id,
        'material' => $mat3->id,
    ]));
    $resMat3Blocked->assertRedirect(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi']));
});

test('students cannot access or submit assignment 2 before submitting assignment 1', function () {
    $courseClass = CourseClass::first();
    $studentUser = User::where('role', 'siswa')->first();
    $student = $studentUser->student;

    // Reset submissions for this student in this course
    LmsAssignmentSubmission::where('student_id', $student->id)->delete();

    // Create 2 sequential assignments
    $asg1 = LmsAssignment::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Tugas 1: Studi Kasus Lapangan',
        'description' => 'Kerjakan studi kasus bab 1.',
        'due_date' => now()->addDays(7),
        'max_score' => 100,
        'submission_type' => 'text',
        'is_published' => true,
    ]);

    $asg2 = LmsAssignment::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Tugas 2: Karya Tulis Ilmiah',
        'description' => 'Kerjakan karya tulis bab 2.',
        'due_date' => now()->addDays(14),
        'max_score' => 100,
        'submission_type' => 'text',
        'is_published' => true,
    ]);

    // Ensure any earlier assignments from seeder are marked submitted so asg1 is active prerequisite
    LmsAssignment::where('course_class_id', $courseClass->id)
        ->where('id', '<', $asg1->id)
        ->get()
        ->each(fn ($a) => LmsAssignmentSubmission::create(['assignment_id' => $a->id, 'student_id' => $student->id, 'submitted_at' => now(), 'content' => 'Selesai']));

    // 1. Siswa BISA membuka Tugas 1
    $resAsg1 = $this->actingAs($studentUser)->get(route('lms.assignments.show', [
        'courseClass' => $courseClass->id,
        'assignment' => $asg1->id,
    ]));
    $resAsg1->assertStatus(200);
    $resAsg1->assertSee('Tugas 1: Studi Kasus Lapangan');

    // 2. Siswa TIDAK BISA membuka Tugas 2 (Terkunci)
    $resAsg2Blocked = $this->actingAs($studentUser)->get(route('lms.assignments.show', [
        'courseClass' => $courseClass->id,
        'assignment' => $asg2->id,
    ]));
    $resAsg2Blocked->assertRedirect(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas']));
    $resAsg2Blocked->assertSessionHas('error');

    // 3. Siswa mencoba submit Tugas 2 langsung lewat POST -> Ditolak controller
    $resSubmitBlocked = $this->actingAs($studentUser)->post(route('lms.assignments.submit', [
        'courseClass' => $courseClass->id,
        'assignment' => $asg2->id,
    ]), ['submission_text' => 'Bypass attempt']);
    $resSubmitBlocked->assertRedirect(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas']));
    $resSubmitBlocked->assertSessionHas('error');

    // 4. Siswa mengumpulkan Tugas 1
    $resSubmit1 = $this->actingAs($studentUser)->post(route('lms.assignments.submit', [
        'courseClass' => $courseClass->id,
        'assignment' => $asg1->id,
    ]), ['submission_text' => 'Jawaban sah tugas 1']);
    $resSubmit1->assertStatus(302);
    $resSubmit1->assertSessionHas('success');

    // 5. Tugas 2 sekarang TERBUKA dan bisa diakses siswa
    $resAsg2Open = $this->actingAs($studentUser)->get(route('lms.assignments.show', [
        'courseClass' => $courseClass->id,
        'assignment' => $asg2->id,
    ]));
    $resAsg2Open->assertStatus(200);
    $resAsg2Open->assertSee('Tugas 2: Karya Tulis Ilmiah');
});

test('students cannot access or take quiz 2 before completing quiz 1', function () {
    $courseClass = CourseClass::first();
    $studentUser = User::where('role', 'siswa')->first();
    $student = $studentUser->student;

    // Reset attempts
    LmsQuizAttempt::where('student_id', $student->id)->delete();

    // Create 2 sequential quizzes
    $quiz1 = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Asesmen 1: Formatif Bab 1',
        'quiz_type' => 'formatif',
        'duration_minutes' => 30,
        'passing_score' => 75,
        'is_published' => true,
    ]);
    $q1 = LmsQuizQuestion::create([
        'quiz_id' => $quiz1->id,
        'question_type' => 'multiple_choice',
        'question_text' => 'Berapa 5 + 5?',
        'options' => ['A' => '10', 'B' => '20', 'C' => '30', 'D' => '40'],
        'correct_answer' => 'A',
        'score_weight' => 100,
    ]);

    $quiz2 = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Asesmen 2: Sumatif Tengah Semester',
        'quiz_type' => 'sts',
        'duration_minutes' => 45,
        'passing_score' => 75,
        'is_published' => true,
    ]);
    $q2 = LmsQuizQuestion::create([
        'quiz_id' => $quiz2->id,
        'question_type' => 'multiple_choice',
        'question_text' => 'Ibu kota Indonesia?',
        'options' => ['A' => 'Jakarta / IKN', 'B' => 'Surabaya', 'C' => 'Bandung', 'D' => 'Medan'],
        'correct_answer' => 'A',
        'score_weight' => 100,
    ]);

    // Ensure any earlier quizzes from seeder are marked completed
    LmsQuiz::where('course_class_id', $courseClass->id)
        ->where('id', '<', $quiz1->id)
        ->get()
        ->each(fn ($q) => LmsQuizAttempt::create(['quiz_id' => $q->id, 'student_id' => $student->id, 'started_at' => now(), 'completed_at' => now(), 'score' => 90, 'is_passed' => true]));

    // 1. Siswa BISA membuka dan take Quiz 1
    $resQuiz1 = $this->actingAs($studentUser)->get(route('lms.quizzes.take', [
        'courseClass' => $courseClass->id,
        'quiz' => $quiz1->id,
    ]));
    $resQuiz1->assertStatus(200);

    // 2. Siswa TIDAK BISA take Quiz 2 (Terkunci)
    $resQuiz2Blocked = $this->actingAs($studentUser)->get(route('lms.quizzes.take', [
        'courseClass' => $courseClass->id,
        'quiz' => $quiz2->id,
    ]));
    $resQuiz2Blocked->assertRedirect(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'kuis']));
    $resQuiz2Blocked->assertSessionHas('error');

    // 3. Siswa menyelesaikan Quiz 1
    $resSubmitQuiz1 = $this->actingAs($studentUser)->post(route('lms.quizzes.submit', [
        'courseClass' => $courseClass->id,
        'quiz' => $quiz1->id,
    ]), ['answers' => [$q1->id => 'A']]);
    $resSubmitQuiz1->assertRedirect(route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $quiz1->id]));
    $resSubmitQuiz1->assertSessionHas('success');

    // 4. Quiz 2 sekarang TERBUKA dan bisa dikerjakan
    $resQuiz2Open = $this->actingAs($studentUser)->get(route('lms.quizzes.take', [
        'courseClass' => $courseClass->id,
        'quiz' => $quiz2->id,
    ]));
    $resQuiz2Open->assertStatus(200);
});

test('teachers and administrators have unrestricted access to all materials, assignments, and quizzes', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();

    $mat1 = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Materi Unrestricted 1',
        'topic' => 'Bab 1',
        'content_type' => 'text',
        'content_text' => 'Teks 1',
        'is_published' => true,
    ]);
    $mat2 = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Materi Unrestricted 2',
        'topic' => 'Bab 2',
        'content_type' => 'text',
        'content_text' => 'Teks 2',
        'is_published' => true,
    ]);

    // Teacher can view mat2 directly even if nobody has completed mat1
    $resTeacher = $this->actingAs($teacher)->get(route('lms.materials.show', [
        'courseClass' => $courseClass->id,
        'material' => $mat2->id,
    ]));
    $resTeacher->assertStatus(200);

    // Admin can view mat2 directly
    $resAdmin = $this->actingAs($admin)->get(route('lms.materials.show', [
        'courseClass' => $courseClass->id,
        'material' => $mat2->id,
    ]));
    $resAdmin->assertStatus(200);
});

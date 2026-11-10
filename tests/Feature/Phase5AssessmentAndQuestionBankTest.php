<?php

use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\QuestionBank;
use App\Models\RemediationEnrichmentPlan;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;

test('teacher can store question in question bank linked to learning objective and cognitive level', function () {
    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Informatika SMAN 1 Lengkong',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P5-SUB',
        'name' => 'Informatika P5',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $tp = LearningObjective::create([
        'subject_id' => $subject->id,
        'tp_code' => '10.P5',
        'title' => 'Menganalisis Kompleksitas Algoritma Pencarian',
        'description' => 'Mampu membandingkan kompleksitas waktu Big-O.',
        'hours_allocation' => 2,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    // Simpan butir soal ke Bank Soal
    $response = $this->actingAs($user)->post(route('lms.assessment.bank.questions.store'), [
        'subject_id' => $subject->id,
        'learning_objective_id' => $tp->id,
        'grade_level' => 'X',
        'topic' => 'Kompleksitas Algoritma Big-O',
        'question_type' => 'multiple_choice',
        'cognitive_level' => 'L3',
        'indicator' => 'Disajikan potongan kode loop bersarang, siswa mampu menentukan notasi Big-O.',
        'stimulus' => 'Perhatikan algoritma dua perulangan for bertingkat n kali.',
        'question_text' => 'Berapakah kompleksitas waktu terburuk dari algoritma tersebut?',
        'score_weight' => 2,
        'options' => [
            'A' => 'O(1)',
            'B' => 'O(n)',
            'C' => 'O(n^2)',
            'D' => 'O(log n)',
            'E' => 'O(n log n)',
        ],
        'correct_answer' => 'C',
        'explanation' => 'Loop bersarang n * n menghasilkan kompleksitas kuadratik O(n^2).',
    ]);

    $response->assertRedirect(route('lms.assessment.bank'));
    $response->assertSessionHas('success');

    $question = QuestionBank::where('topic', 'Kompleksitas Algoritma Big-O')->first();
    expect($question)->not->toBeNull();
    expect($question->cognitive_level)->toBe('L3');
    expect($question->correct_answer)->toBe('C');
    expect($question->learningObjective->tp_code)->toBe('10.P5');
});

test('ai question generator endpoint returns valid structured assessment draft', function () {
    $user = User::factory()->create(['role' => 'guru']);

    $response = $this->actingAs($user)->postJson(route('lms.assessment.bank.ai_generate'), [
        'subject_name' => 'Informatika',
        'topic' => 'Sistem Komputer dan Arsitektur CPU',
        'grade' => 'X',
        'cognitive_level' => 'L3',
        'tp_context' => 'Menjelaskan mekanisme fetch-decode-execute',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['status', 'content', 'model']);
    expect($response->json('status'))->toBe('ok');
});

test('quiz and quiz questions can be linked to learning objective and print kisi-kisi works', function () {
    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $user->id,
        'name' => 'Budi Guru, S.Pd.',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'INF-P5',
        'name' => 'Informatika',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-4',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    $tp = LearningObjective::create([
        'subject_id' => $subject->id,
        'tp_code' => '10.CBT',
        'title' => 'Evaluasi Pemahaman Logika Dasar',
        'description' => 'Mampu menjawab pertanyaan logika percabangan.',
        'hours_allocation' => 2,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    // 1. Simpan Kuis CBT terhubung TP
    $quiz = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'learning_objective_id' => $tp->id,
        'title' => 'Penilaian Harian 1: Logika Dasar',
        'quiz_type' => 'formatif',
        'duration_minutes' => 60,
        'passing_score' => 75,
        'is_published' => true,
    ]);

    expect($quiz->learning_objective_id)->toBe($tp->id);

    // 2. Simpan Butir Soal Kuis terhubung TP
    $q1 = LmsQuizQuestion::create([
        'quiz_id' => $quiz->id,
        'learning_objective_id' => $tp->id,
        'question_text' => 'Manakah operator logika untuk konjungsi?',
        'question_type' => 'multiple_choice',
        'level_kognitif' => 'L2',
        'materi_pokok' => 'Logika Proposisi',
        'indikator_soal' => 'Siswa dapat menentukan simbol operator konjungsi.',
        'options' => ['A' => 'AND', 'B' => 'OR', 'C' => 'NOT', 'D' => 'XOR'],
        'correct_answer' => 'A',
        'score_weight' => 10,
        'order_number' => 1,
    ]);

    expect($q1->learningObjective->tp_code)->toBe('10.CBT');

    // 3. Cetak Kisi-Kisi Asesmen
    $printResponse = $this->actingAs($user)->get(route('lms.assessment.kisi_kisi.print', ['quiz_id' => $quiz->id]));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('SMA NEGERI 1 LENGKONG');
    $printResponse->assertSee('KISI-KISI PENULISAN SOAL ASESMEN');
    $printResponse->assertSee('10.CBT');
    $printResponse->assertSee('Evaluasi Pemahaman Logika Dasar');
});

test('teacher can plan and complete remediation enrichment program for targeted students', function () {
    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Biologi SMAN 1 Lengkong',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'BIO-P5',
        'name' => 'Biologi',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-5',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    $student = Student::create([
        'class_id' => $schoolClass->id,
        'name' => 'Ahmad Siswa Remedial',
        'nisn' => '1234567890',
        'nis' => '12345',
        'gender' => 'L',
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    $tp = LearningObjective::create([
        'subject_id' => $subject->id,
        'tp_code' => '10.REM',
        'title' => 'Memahami Klasifikasi Makhluk Hidup',
        'description' => 'Mampu membedakan 5 kingdom.',
        'hours_allocation' => 2,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    // 1. Simpan Rencana Remedial
    $response = $this->actingAs($user)->post(route('lms.assessment.remediation.store'), [
        'course_class_id' => $courseClass->id,
        'learning_objective_id' => $tp->id,
        'type' => 'REMEDIAL',
        'topic' => 'Bimbingan Remedial 5 Kingdom',
        'description' => 'Bimbingan konsep kunci dan tes ulang setara.',
        'remedial_method' => 'Tutor sebaya dan latihan modul tambahan.',
        'scheduled_date' => now()->toDateString(),
        'student_ids' => [$student->id],
    ]);

    $response->assertRedirect(route('lms.assessment.remediation', ['course_class_id' => $courseClass->id]));
    $response->assertSessionHas('success');

    $plan = RemediationEnrichmentPlan::where('topic', 'Bimbingan Remedial 5 Kingdom')->first();
    expect($plan)->not->toBeNull();
    expect($plan->status)->toBe('PLANNED');
    expect($plan->student_ids)->toContain($student->id);

    // 2. Tandai Selesai
    $completeResponse = $this->actingAs($user)->post(route('lms.assessment.remediation.complete', $plan));
    $completeResponse->assertRedirect();

    $plan->refresh();
    expect($plan->status)->toBe('COMPLETED');
});

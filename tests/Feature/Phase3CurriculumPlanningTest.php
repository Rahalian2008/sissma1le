<?php

use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\LessonPlan;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;

test('authenticated teacher can view curriculum planning dashboard with all tabs', function () {
    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Peneliti',
    ]);

    $response = $this->actingAs($user)->get(route('lms.planning.index'));

    $response->assertStatus(200);
    $response->assertSee('Perangkat Ajar Digital');
    $response->assertSee('Modul Ajar / RPP Digital');
    $response->assertSee('Tujuan Pembelajaran (TP)');
    $response->assertSee('Capaian Pembelajaran (CP)');
    $response->assertSee('Alur Tujuan (ATP)');
});

test('teacher can store learning outcome and objective', function () {
    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Informatika SMAN 1 Lengkong',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P3-SUB',
        'name' => 'Informatika Uji P3',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    // Store CP
    $cpResponse = $this->actingAs($user)->post(route('lms.planning.outcomes.store'), [
        'subject_id' => $subject->id,
        'phase' => 'E',
        'grade_level' => 'X',
        'code' => 'CP-P3-001',
        'element' => 'Berpikir Komputasional',
        'description' => 'Peserta didik mampu menerapkan strategi algoritmik standar.',
    ]);

    $cpResponse->assertRedirect(route('lms.planning.index', ['tab' => 'outcomes']));
    $cpResponse->assertSessionHas('success');

    $this->assertDatabaseHas('learning_outcomes', [
        'code' => 'CP-P3-001',
        'subject_id' => $subject->id,
    ]);

    $outcome = LearningOutcome::where('code', 'CP-P3-001')->first();

    // Store TP
    $tpResponse = $this->actingAs($user)->post(route('lms.planning.objectives.store'), [
        'subject_id' => $subject->id,
        'learning_outcome_id' => $outcome->id,
        'tp_code' => '10.P3',
        'title' => 'Mengimplementasikan Algoritma Sorting',
        'description' => 'Mampu membandingkan bubble sort dan insertion sort.',
        'competency' => 'Menganalisis dan Mengimplementasikan',
        'scope_material' => 'Algoritma Pencarian & Pengurutan',
        'hours_allocation' => 4,
        'target_semester' => 'GANJIL',
    ]);

    $tpResponse->assertRedirect(route('lms.planning.index', ['tab' => 'objectives']));
    $tpResponse->assertSessionHas('success');

    $this->assertDatabaseHas('learning_objectives', [
        'tp_code' => '10.P3',
        'subject_id' => $subject->id,
    ]);
});

test('ai assistance endpoint returns valid draft for objective and lesson plan', function () {
    $user = User::factory()->create(['role' => 'guru']);

    // Test AI TP generator
    $aiTpResponse = $this->actingAs($user)->postJson(route('lms.planning.objectives.ai_generate'), [
        'subject_name' => 'Informatika',
        'topic' => 'Kecerdasan Buatan dan Etika Digital',
        'grade' => 'X',
    ]);

    $aiTpResponse->assertStatus(200);
    $aiTpResponse->assertJsonStructure(['status', 'content', 'model']);
    expect($aiTpResponse->json('status'))->toBe('ok');

    // Test AI Lesson Plan generator
    $aiPlanResponse = $this->actingAs($user)->postJson(route('lms.planning.lesson_plans.ai_generate'), [
        'subject_name' => 'Informatika',
        'topic' => 'Pengenalan Bahasa Pemrograman Python',
        'grade' => 'X',
        'duration' => 90,
    ]);

    $aiPlanResponse->assertStatus(200);
    $aiPlanResponse->assertJsonStructure(['status', 'content', 'model']);
    expect($aiPlanResponse->json('status'))->toBe('ok');
});

test('teacher can create lesson plan, submit for review, wakasek can approve, and print preview works', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Budi Santoso, S.Kom.',
        'nip' => '198501012010011005',
    ]);

    $kurikulumUser = User::factory()->create(['role' => 'wakasek_kurikulum']);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'INF-X',
        'name' => 'Informatika',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-1',
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
        'tp_code' => '10.A1',
        'title' => 'Memahami Struktur Algoritma',
        'description' => 'Mampu menganalisis alur logika pemrograman.',
        'hours_allocation' => 2,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    // 1. Create Lesson Plan
    $createResponse = $this->actingAs($teacherUser)->post(route('lms.planning.lesson_plans.store'), [
        'course_class_id' => $courseClass->id,
        'title' => 'Pertemuan 1: Dasar Logika Pemrograman',
        'meeting_number' => 1,
        'time_allocation_minutes' => 90,
        'grade_level' => 'X',
        'semester' => 'GANJIL',
        'learning_model' => 'Problem-Based Learning (PBL)',
        'target_profile' => ['Bernalar Kritis', 'Gotong Royong'],
        'step_introduction' => 'Guru membuka KBM dan menyampaikan salam serta motivasi.',
        'step_core' => 'Peserta didik menyelesaikan studi kasus dalam kelompok kerja.',
        'step_closing' => 'Guru dan murid menyimpulkan pembelajaran hari ini.',
        'assessment_diagnostic' => 'Pre-test 3 soal pemantik.',
        'assessment_formative' => 'Observasi diskusi kelompok.',
        'lkpd_content' => 'Lembar Kerja 1: Buat flowchart percabangan.',
        'objective_ids' => [$tp->id],
    ]);

    $createResponse->assertRedirect(route('lms.planning.index', ['tab' => 'lesson_plans']));

    $plan = LessonPlan::where('title', 'Pertemuan 1: Dasar Logika Pemrograman')->first();
    expect($plan)->not->toBeNull();
    expect($plan->status)->toBe('DRAFT');
    expect($plan->objectives)->toHaveCount(1);

    // 2. Teacher Submits for Review
    $submitResponse = $this->actingAs($teacherUser)->post(route('lms.planning.lesson_plans.submit', $plan));
    $submitResponse->assertRedirect(route('lms.planning.index', ['tab' => 'lesson_plans']));

    $plan->refresh();
    expect($plan->status)->toBe('SUBMITTED');

    // 3. Wakasek Kurikulum Approves the Lesson Plan
    $reviewResponse = $this->actingAs($kurikulumUser)->post(route('lms.planning.lesson_plans.review', $plan), [
        'action' => 'APPROVE',
        'review_notes' => 'Modul ajar telah diverifikasi sesuai standar Kurikulum Merdeka.',
    ]);
    $reviewResponse->assertRedirect(route('lms.planning.index', ['tab' => 'lesson_plans']));

    $plan->refresh();
    expect($plan->status)->toBe('APPROVED');
    expect($plan->is_published)->toBeTrue();
    expect($plan->reviewed_by)->toBe($kurikulumUser->id);

    // 4. Print Preview Document
    $printResponse = $this->actingAs($teacherUser)->get(route('lms.planning.lesson_plans.print', $plan));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('SMA NEGERI 1 LENGKONG');
    $printResponse->assertSee('MODUL AJAR KURIKULUM MERDEKA');
    $printResponse->assertSee('Pertemuan 1: Dasar Logika Pemrograman');
    $printResponse->assertSee('10.A1');
    $printResponse->assertSee('DISETUJUI KURIKULUM');
});

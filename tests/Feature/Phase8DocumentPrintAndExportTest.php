<?php

use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use App\Models\User;

test('teacher and kurikulum can access document printing hub', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Cetak Dokumen SMAN 1 Lengkong',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P8-SUB1',
        'name' => 'Matematika P8',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-12',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($teacherUser)->get(route('lms.documents.index'));
    $response->assertStatus(200);
    $response->assertSee('Pusat Cetak & Arsip Kurikulum', false);
    $response->assertSee('Program Tahunan (PROTA)');
    $response->assertSee('Program Semester (PROMES)');
});

test('teacher can print official prota, promes, and atp documents', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Informatika Dokumen',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P8-SUB2',
        'name' => 'Informatika P8',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-13',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    $outcome = LearningOutcome::create([
        'subject_id' => $subject->id,
        'phase' => 'E',
        'code' => 'CP-BK-X',
        'element' => 'Berpikir Komputasional',
        'description' => 'Mampu menerapkan strategi algoritmik standar untuk menghasilkan beberapa solusi persoalan.',
        'is_active' => true,
    ]);

    LearningObjective::create([
        'subject_id' => $subject->id,
        'learning_outcome_id' => $outcome->id,
        'tp_code' => '10.P8.1',
        'title' => 'Menganalisis Algoritma Sorting dan Searching',
        'description' => 'Mampu membandingkan efisiensi algoritma.',
        'hours_allocation' => 4,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    LearningObjective::create([
        'subject_id' => $subject->id,
        'learning_outcome_id' => $outcome->id,
        'tp_code' => '10.P8.2',
        'title' => 'Menerapkan Struktur Data Graf dan Pohon',
        'description' => 'Mampu memodelkan jaringan konektivitas.',
        'hours_allocation' => 4,
        'target_semester' => 'GENAP',
        'is_active' => true,
    ]);

    // 1. Dokumen PROTA
    $protaResponse = $this->actingAs($teacherUser)->get(route('lms.documents.prota.print', ['course_class_id' => $courseClass->id]));
    $protaResponse->assertStatus(200);
    $protaResponse->assertSee('PROGRAM TAHUNAN (PROTA)');
    $protaResponse->assertSee('10.P8.1');

    // 2. Dokumen PROMES (Ganjil & Genap)
    $promesResponse = $this->actingAs($teacherUser)->get(route('lms.documents.promes.print', ['course_class_id' => $courseClass->id, 'semester' => 'GANJIL']));
    $promesResponse->assertStatus(200);
    $promesResponse->assertSee('PROGRAM SEMESTER (PROMES)');
    $promesResponse->assertSee('SEMESTER GANJIL');

    // 3. Dokumen ATP
    $atpResponse = $this->actingAs($teacherUser)->get(route('lms.documents.atp.print', ['course_class_id' => $courseClass->id]));
    $atpResponse->assertStatus(200);
    $atpResponse->assertSee('ALUR TUJUAN PEMBELAJARAN (ATP)');
    $atpResponse->assertSee('Berpikir Komputasional');
});

test('leader can export curriculum readiness matrix to csv format', function () {
    $kurikulumUser = User::factory()->create(['role' => 'wakasek_kurikulum']);

    $response = $this->actingAs($kurikulumUser)->get(route('lms.documents.export.matrix'));
    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('text/csv');
    expect($response->headers->get('content-disposition'))->toContain('matriks_kesiapan_kurikulum_sma1le');

    $content = $response->streamedContent();
    expect($content)->toContain('Nama Pendidik');
    expect($content)->toContain('Indeks Kesiapan (%)');
});

test('teacher can export teaching journals to csv format', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Jurnal Export',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P8-SUB3',
        'name' => 'Fisika P8',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-14',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    TeachingJournal::create([
        'course_class_id' => $courseClass->id,
        'teacher_id' => $teacher->id,
        'date' => date('Y-m-d'),
        'meeting_number' => 1,
        'topic' => 'Vektor dan Kinematika Gerak Lurus',
        'teaching_activities' => 'Praktik peluncuran gerak lurus beraturan.',
        'learning_achievement_status' => 'TERCAPAI',
        'is_verified_by_kurikulum' => true,
    ]);

    $response = $this->actingAs($teacherUser)->get(route('lms.documents.export.journals'));
    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('text/csv');

    $content = $response->streamedContent();
    expect($content)->toContain('Vektor dan Kinematika Gerak Lurus');
    expect($content)->toContain('TERCAPAI');
});

test('unauthorized users like students cannot export curriculum matrix', function () {
    $studentUser = User::factory()->create(['role' => 'siswa']);

    $response = $this->actingAs($studentUser)->get(route('lms.documents.export.matrix'));
    $response->assertStatus(403);
});

<?php

use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use App\Models\User;

test('material and assignment can be linked to a learning objective (TP)', function () {
    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Informatika SMAN 1 Lengkong',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P4-SUB',
        'name' => 'Informatika P4',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-2',
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
        'tp_code' => '10.P4',
        'title' => 'Menerapkan Algoritma Pencarian Linear dan Biner',
        'description' => 'Mampu membandingkan efisiensi waktu linear search vs binary search.',
        'hours_allocation' => 2,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    // 1. Simpan Materi Pembelajaran dengan keterkaitan TP
    $materialResponse = $this->actingAs($user)->post(route('lms.materials.store', $courseClass), [
        'title' => 'Modul Pertemuan 2: Binary Search',
        'content_type' => 'text',
        'content_text' => 'Penjelasan langkah-langkah binary search pada data terurut.',
        'learning_objective_id' => $tp->id,
    ]);

    $materialResponse->assertRedirect(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi']));

    $material = LmsMaterial::where('title', 'Modul Pertemuan 2: Binary Search')->first();
    expect($material)->not->toBeNull();
    expect($material->learning_objective_id)->toBe($tp->id);
    expect($material->learningObjective->tp_code)->toBe('10.P4');

    // 2. Simpan Tugas Siswa dengan keterkaitan TP
    $assignmentResponse = $this->actingAs($user)->post(route('lms.assignments.store', $courseClass), [
        'title' => 'Latihan Implementasi Binary Search Python',
        'description' => 'Buatlah skrip fungsi binary_search(arr, target) dalam bahasa Python.',
        'due_date' => now()->addDays(7)->toDateTimeString(),
        'max_score' => 100,
        'submission_type' => 'text',
        'learning_objective_id' => $tp->id,
    ]);

    $assignmentResponse->assertRedirect();

    $assignment = LmsAssignment::where('title', 'Latihan Implementasi Binary Search Python')->first();
    expect($assignment)->not->toBeNull();
    expect($assignment->learning_objective_id)->toBe($tp->id);
    expect($assignment->learningObjective->tp_code)->toBe('10.P4');
});

test('teacher can record teaching journal, kurikulum can verify it, and print preview works', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Dewi Lestari, S.Pd.',
        'nip' => '198705122011012003',
    ]);

    $kurikulumUser = User::factory()->create(['role' => 'wakasek_kurikulum']);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'BIO-X',
        'name' => 'Biologi',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-3',
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
        'tp_code' => '10.B1',
        'title' => 'Menganalisis Peran Virus dalam Kehidupan',
        'description' => 'Mampu mengidentifikasi virus yang menguntungkan dan merugikan.',
        'hours_allocation' => 2,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    // 1. Simpan Jurnal Mengajar
    $journalResponse = $this->actingAs($teacherUser)->post(route('lms.journals.store'), [
        'course_class_id' => $courseClass->id,
        'date' => now()->toDateString(),
        'meeting_number' => 1,
        'time_slot' => '07:30 - 09:00 WIB',
        'topic' => 'Struktur dan Karakteristik Virus',
        'learning_objective_id' => $tp->id,
        'teaching_activities' => 'Apersepsi pemantik pandemi, diskusi kelompok LKPD struktur kapsid dan genom virus, serta presentasi hasil analisis.',
        'hadir_count' => 34,
        'sakit_count' => 1,
        'izin_count' => 1,
        'alfa_count' => 0,
        'learning_achievement_status' => 'TERCAPAI',
        'classroom_dynamics_notes' => 'Siswa sangat aktif saat sesi tanya jawab mengenai vaksin.',
        'follow_up_action' => 'Penugasan pembuatan infografis daur litik dan lisogenik.',
    ]);

    $journalResponse->assertRedirect(route('lms.journals.index'));
    $journalResponse->assertSessionHas('success');

    $journal = TeachingJournal::where('topic', 'Struktur dan Karakteristik Virus')->first();
    expect($journal)->not->toBeNull();
    expect($journal->meeting_number)->toBe(1);
    expect($journal->learning_achievement_status)->toBe('TERCAPAI');
    expect($journal->student_attendance_summary['hadir'])->toBe(34);
    expect($journal->is_verified_by_kurikulum)->toBeFalse();

    // 2. Buka Halaman Index Jurnal
    $indexResponse = $this->actingAs($teacherUser)->get(route('lms.journals.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Jurnal Mengajar Harian Guru');
    $indexResponse->assertSee('Struktur dan Karakteristik Virus');
    $indexResponse->assertSee('10.B1');
    $indexResponse->assertSee('TERCAPAI');

    // 3. Verifikasi oleh Wakasek Kurikulum
    $verifyResponse = $this->actingAs($kurikulumUser)->post(route('lms.journals.verify', $journal), [
        'verification_notes' => 'KBM terlaksana sangat baik sesuai sintaks saintifik.',
    ]);

    $verifyResponse->assertRedirect();
    $journal->refresh();
    expect($journal->is_verified_by_kurikulum)->toBeTrue();
    expect($journal->verified_by)->toBe($kurikulumUser->id);

    // 4. Cetak Agenda Jurnal Mengajar
    $printResponse = $this->actingAs($teacherUser)->get(route('lms.journals.print', [
        'course_class_id' => $courseClass->id,
    ]));

    $printResponse->assertStatus(200);
    $printResponse->assertSee('SMA NEGERI 1 LENGKONG');
    $printResponse->assertSee('AGENDA & JURNAL PELAKSANAAN PEMBELAJARAN GURU', false);
    $printResponse->assertSee('Struktur dan Karakteristik Virus');
    $printResponse->assertSee('10.B1');
});

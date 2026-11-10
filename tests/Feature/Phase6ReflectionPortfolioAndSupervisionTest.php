<?php

use App\Models\AcademicSupervision;
use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentPortfolio;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherReflection;
use App\Models\User;

test('teacher can view reflections, store new reflection, and generate ai draft', function () {
    $user = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $user->id,
        'name' => 'Guru Refleksi SMAN 1 Lengkong',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P6-SUB',
        'name' => 'Biologi P6',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-7',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    // 1. Tampilkan Halaman Refleksi
    $response = $this->actingAs($user)->get(route('lms.reflections.index'));
    $response->assertStatus(200);
    $response->assertSee('Refleksi Pembelajaran Guru');

    // 2. Simpan Catatan Refleksi Baru
    $storeResponse = $this->actingAs($user)->post(route('lms.reflections.store'), [
        'course_class_id' => $courseClass->id,
        'reflection_date' => date('Y-m-d'),
        'success_notes' => 'Siswa sangat antusias melakukan praktikum mikroskop dan pengamatan sel tumbuhan.',
        'challenges_notes' => 'Sebagian kelompok masih membutuhkan waktu dalam mengatur fokus lensa perbesaran 40x.',
        'follow_up_action' => 'Memberikan video panduan teknis fokus lensa sebelum praktikum pertemuan berikutnya.',
        'status' => 'SELESAI',
    ]);
    $storeResponse->assertRedirect();
    $storeResponse->assertSessionHas('success');

    $this->assertDatabaseHas('teacher_reflections', [
        'course_class_id' => $courseClass->id,
        'teacher_id' => $teacher->id,
        'status' => 'SELESAI',
    ]);

    // 3. AI Asisten Generator Refleksi
    $aiResponse = $this->actingAs($user)->postJson(route('lms.reflections.ai_generate'), [
        'topic' => 'Struktur Sel Hewan dan Tumbuhan',
        'grade' => 'X',
        'challenges' => 'fokus lensa perbesaran mikroskop',
    ]);
    $aiResponse->assertStatus(200);
    $aiResponse->assertJson(['success' => true]);

    // 4. Pratinjau Dokumen Cetak Rekap Refleksi
    $printResponse = $this->actingAs($user)->get(route('lms.reflections.print', ['course_class_id' => $courseClass->id]));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('REKAPITULASI CATATAN REFLEKSI PEMBELAJARAN GURU');
});

test('student and teacher can manage, curate, and verify digital portfolios', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Pembimbing Portofolio',
    ]);

    $studentUser = User::factory()->create(['role' => 'siswa']);
    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-8',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    $student = Student::create([
        'user_id' => $studentUser->id,
        'class_id' => $schoolClass->id,
        'name' => 'Citra Maharani',
        'nisn' => '9988776611',
        'nis' => '12099',
        'gender' => 'P',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P6-SUB2',
        'name' => 'Kimia P6',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    $tp = LearningObjective::create([
        'subject_id' => $subject->id,
        'tp_code' => '10.PORTO1',
        'title' => 'Merancang Proyek Solusi Pengolahan Sampah Plastik',
        'description' => 'Mampu mempresentasikan prototipe inovatif ramah lingkungan.',
        'hours_allocation' => 3,
        'target_semester' => 'GANJIL',
        'is_active' => true,
    ]);

    // 1. Siswa Mengunggah Portofolio
    $uploadResponse = $this->actingAs($studentUser)->post(route('lms.portfolios.store'), [
        'title' => 'Prototipe Bioplastik dari Pati Singkong',
        'category' => 'Proyek P5',
        'course_class_id' => $courseClass->id,
        'learning_objective_id' => $tp->id,
        'description' => 'Eksperimen sintesis plastik ramah lingkungan terurai hayati.',
        'external_url' => 'https://youtube.com/watch?v=sample-video-p5',
        'artifact_date' => date('Y-m-d'),
    ]);
    $uploadResponse->assertRedirect();
    $uploadResponse->assertSessionHas('success');

    $portfolio = StudentPortfolio::where('title', 'Prototipe Bioplastik dari Pati Singkong')->first();
    expect($portfolio)->not->toBeNull();
    expect($portfolio->student_id)->toBe($student->id);
    expect($portfolio->is_featured)->toBeFalse();

    // 2. Guru Membuka Galeri Portofolio & Melakukan Verifikasi / Sematkan Unggulan
    $galleryResponse = $this->actingAs($teacherUser)->get(route('lms.portfolios.index'));
    $galleryResponse->assertStatus(200);
    $galleryResponse->assertSee('Portofolio Digital Siswa');
    $galleryResponse->assertSee('Prototipe Bioplastik dari Pati Singkong');

    $verifyResponse = $this->actingAs($teacherUser)->post(route('lms.portfolios.verify', $portfolio), [
        'toggle_featured' => 1,
    ]);
    $verifyResponse->assertRedirect();
    $verifyResponse->assertSessionHas('success');

    $portfolio->refresh();
    expect($portfolio->is_featured)->toBeTrue();
    expect($portfolio->verified_by)->toBe($teacherUser->id);

    // 3. Cetak Lembar Portofolio Resmi
    $printResponse = $this->actingAs($teacherUser)->get(route('lms.portfolios.print', $portfolio));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('LEMBAR KURASI', false);
    $printResponse->assertSee('Prototipe Bioplastik dari Pati Singkong');
});

test('leader can conduct academic supervision and print supervision document', function () {
    $leader = User::factory()->create(['role' => 'kepala_sekolah']);
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Drs. Supardi, M.Pd.',
        'nip' => '197001011995011001',
    ]);

    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P6-S3',
        'name' => 'Fisika P6',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);
    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-9',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    $courseClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    // 1. Pimpinan Mengakses Dashboard Supervisi
    $response = $this->actingAs($leader)->get(route('lms.supervision.index'));
    $response->assertStatus(200);
    $response->assertSee('Supervisi Akademik & Kurikulum');
    $response->assertSee('Matriks Kesiapan Administrasi');

    // 2. Simpan Observasi Supervisi Akademik Baru
    $storeResponse = $this->actingAs($leader)->post(route('lms.supervision.store'), [
        'teacher_id' => $teacher->id,
        'course_class_id' => $courseClass->id,
        'supervision_date' => date('Y-m-d'),
        'instrument_type' => 'LENGKAP',
        'planning_score' => 90,
        'execution_score' => 92,
        'assessment_score' => 88,
        'reflection_score' => 90,
        'notes' => 'Pendidik menguasai materi secara mendalam dan kelas berjalan sangat partisipatif.',
        'recommendation' => 'Dapat dijadikan model pembelajaran diferensiasi untuk komunitas guru mapel.',
    ]);
    $storeResponse->assertRedirect();
    $storeResponse->assertSessionHas('success');

    $supervision = AcademicSupervision::where('teacher_id', $teacher->id)->first();
    expect($supervision)->not->toBeNull();
    expect($supervision->final_score)->toBe(90.0);
    expect($supervision->predicate)->toBe('BAIK');

    // 3. Cetak Hasil Supervisi Akademik
    $printResponse = $this->actingAs($leader)->get(route('lms.supervision.print', $supervision));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('INSTRUMEN & LEMBAR HASIL SUPERVISI AKADEMIK GURU', false);
    $printResponse->assertSee('Drs. Supardi, M.Pd.');
});

test('unauthorized users cannot store supervision or delete other teacher reflections', function () {
    $regularTeacher = User::factory()->create(['role' => 'guru']);
    $otherTeacherUser = User::factory()->create(['role' => 'guru']);

    $otherTeacher = Teacher::create([
        'user_id' => $otherTeacherUser->id,
        'name' => 'Guru Lain',
    ]);

    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-10',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);
    $subject = Subject::first() ?? Subject::create([
        'code' => 'TEST-P6-S4',
        'name' => 'Kimia P6B',
        'grade' => 'X',
        'group' => 'Wajib',
        'kkm' => 75,
    ]);

    $otherClass = CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $otherTeacher->id,
        'is_active' => true,
    ]);

    $reflection = TeacherReflection::create([
        'course_class_id' => $otherClass->id,
        'teacher_id' => $otherTeacher->id,
        'reflection_date' => date('Y-m-d'),
        'success_notes' => 'Catatan guru lain',
        'challenges_notes' => 'Kendala guru lain',
        'follow_up_action' => 'Tindak lanjut guru lain',
        'status' => 'SELESAI',
    ]);

    // 1. Guru biasa tidak boleh mencatat supervisi akademik pimpinan
    $supervisionAttempt = $this->actingAs($regularTeacher)->post(route('lms.supervision.store'), [
        'teacher_id' => $otherTeacher->id,
        'supervision_date' => date('Y-m-d'),
        'planning_score' => 80,
        'execution_score' => 80,
        'assessment_score' => 80,
        'reflection_score' => 80,
    ]);
    $supervisionAttempt->assertStatus(403);

    // 2. Guru biasa tidak boleh menghapus refleksi milik guru lain (Anti-IDOR)
    $deleteAttempt = $this->actingAs($regularTeacher)->delete(route('lms.reflections.destroy', $reflection));
    $deleteAttempt->assertStatus(403);
});

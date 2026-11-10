<?php

use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\LmsQuiz;
use App\Models\LmsStudentSecurityBlock;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Guru Pengampu
    $this->guruUser = User::factory()->create([
        'name' => 'Budi Santoso, M.Pd.',
        'role' => 'guru',
        'is_active' => true,
    ]);
    $this->teacher = Teacher::create([
        'user_id' => $this->guruUser->id,
        'name' => 'Budi Santoso, M.Pd.',
        'nip' => '198001012005011001',
        'is_active' => true,
    ]);

    // 2. Guru Lain (bukan pengampu)
    $this->otherGuruUser = User::factory()->create([
        'name' => 'Siti Rahma, S.Pd.',
        'role' => 'guru',
        'is_active' => true,
    ]);
    $this->otherTeacher = Teacher::create([
        'user_id' => $this->otherGuruUser->id,
        'name' => 'Siti Rahma, S.Pd.',
        'nip' => '198505052010012002',
        'is_active' => true,
    ]);

    // 3. Admin User
    $this->adminUser = User::factory()->create([
        'name' => 'Admin LMS',
        'role' => 'admin',
        'is_active' => true,
    ]);

    // 4. Kelas & Siswa
    $this->schoolClass = SchoolClass::create([
        'name' => 'XII MIPA 1',
        'grade' => 'XII',
        'is_active' => true,
    ]);

    $this->siswaUser = User::factory()->create([
        'name' => 'Ahmad Siswa',
        'role' => 'siswa',
        'is_active' => true,
    ]);
    $this->student = Student::create([
        'user_id' => $this->siswaUser->id,
        'class_id' => $this->schoolClass->id,
        'name' => 'Ahmad Siswa',
        'nis' => '12345',
        'nisn' => '0012345678',
        'gender' => 'L',
        'is_active' => true,
    ]);

    // 5. Mapel & Course Class
    $this->subject = Subject::create([
        'name' => 'Fisika Peminatan',
        'code' => 'FIS-XII',
        'group' => 'peminatan',
        'order' => 1,
        'is_active' => true,
    ]);

    $this->courseClass = CourseClass::create([
        'subject_id' => $this->subject->id,
        'teacher_id' => $this->teacher->id,
        'class_id' => $this->schoolClass->id,
        'day_of_week' => 'Senin',
        'start_time' => '07:30',
        'end_time' => '16:00',
        'kktp' => 75,
        'is_active' => true,
        'is_security_enabled' => true,
    ]);

    // 6. Materi, Tugas, dan Kuis
    $this->material = LmsMaterial::create([
        'course_class_id' => $this->courseClass->id,
        'title' => 'Gelombang Elektromagnetik',
        'topic' => 'Bab 1',
        'type' => 'document',
        'content' => 'Pengantar spektrum gelombang elektromagnetik',
        'is_published' => true,
    ]);

    $this->assignment = LmsAssignment::create([
        'course_class_id' => $this->courseClass->id,
        'title' => 'Tugas Mandiri Spektrum Cahaya',
        'topic' => 'Bab 1',
        'instructions' => 'Analisis frekuensi dan panjang gelombang cahaya tampak.',
        'due_date' => now()->addDays(3),
        'max_score' => 100,
        'is_published' => true,
    ]);

    $this->quiz = LmsQuiz::create([
        'course_class_id' => $this->courseClass->id,
        'title' => 'CBT Formatif Gelombang',
        'topic' => 'Bab 1',
        'quiz_type' => 'formatif',
        'duration_minutes' => 60,
        'passing_score' => 75,
        'is_published' => true,
    ]);
});

test('guru mapel can toggle student security feature between active and nonaktif', function () {
    // Awalnya aktif (true)
    expect($this->courseClass->isSecurityEnabled())->toBeTrue();

    // 1. Guru mapel mematikan (nonaktifkan) fitur keamanan
    $response = $this->actingAs($this->guruUser)
        ->post(route('lms.security.toggle', $this->courseClass->id));

    $response->assertRedirect();
    $this->courseClass->refresh();
    expect($this->courseClass->isSecurityEnabled())->toBeFalse();

    // 2. Guru mapel mengaktifkan kembali fitur keamanan
    $response2 = $this->actingAs($this->guruUser)
        ->post(route('lms.security.toggle', $this->courseClass->id));

    $response2->assertRedirect();
    $this->courseClass->refresh();
    expect($this->courseClass->isSecurityEnabled())->toBeTrue();
});

test('other teachers cannot toggle security on a course they do not teach', function () {
    $response = $this->actingAs($this->otherGuruUser)
        ->post(route('lms.security.toggle', $this->courseClass->id));

    $response->assertStatus(403);
});

test('admin can also toggle security on any course', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('lms.security.toggle', $this->courseClass->id));

    $response->assertRedirect();
    $this->courseClass->refresh();
    expect($this->courseClass->isSecurityEnabled())->toBeFalse();
});

test('student triggering tab switch is automatically blocked when security is enabled', function () {
    $response = $this->actingAs($this->siswaUser)
        ->postJson(route('lms.security.violation', $this->courseClass->id), [
            'blockable_type' => 'material',
            'blockable_id' => $this->material->id,
            'violation_type' => 'tab_switch',
            'reason' => 'Siswa membuka tab baru pada browser.',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'blocked' => true,
        ]);

    // Database mencatat blokir aktif
    expect(LmsStudentSecurityBlock::isStudentBlocked($this->student->id, 'material', $this->material->id))->toBeTrue();

    // Akses siswa ke materi tersebut sekarang terblokir
    $materialResponse = $this->actingAs($this->siswaUser)
        ->get(route('lms.materials.show', ['courseClass' => $this->courseClass->id, 'material' => $this->material->id]));

    $materialResponse->assertOk()
        ->assertViewIs('lms.security.blocked')
        ->assertSee('Akses Pembelajaran Diblokir');
});

test('when security is toggled off student is not blocked and can access freely', function () {
    // Matikan fitur keamanan
    $this->courseClass->update(['is_security_enabled' => false]);

    // Request pelanggaran tidak memblokir
    $response = $this->actingAs($this->siswaUser)
        ->postJson(route('lms.security.violation', $this->courseClass->id), [
            'blockable_type' => 'material',
            'blockable_id' => $this->material->id,
            'violation_type' => 'tab_switch',
        ]);

    $response->assertOk();
    expect(LmsStudentSecurityBlock::isStudentBlocked($this->student->id, 'material', $this->material->id))->toBeFalse();

    // Siswa dapat mengakses materi secara normal
    $materialResponse = $this->actingAs($this->siswaUser)
        ->get(route('lms.materials.show', ['courseClass' => $this->courseClass->id, 'material' => $this->material->id]));

    $materialResponse->assertOk()
        ->assertViewIs('lms.materials.show');
});

test('guru mapel or admin can unblock a blocked student to resume learning', function () {
    // Siswa terblokir pada tugas
    $block = LmsStudentSecurityBlock::recordBlock(
        $this->courseClass->id,
        $this->student->id,
        'assignment',
        $this->assignment->id,
        'minimize',
        'Aplikasi diminimalkan saat melihat soal'
    );

    expect(LmsStudentSecurityBlock::isStudentBlocked($this->student->id, 'assignment', $this->assignment->id))->toBeTrue();

    // Guru membuka blokir siswa
    $unblockResponse = $this->actingAs($this->guruUser)
        ->post(route('lms.security.unblock', $this->courseClass->id), [
            'block_id' => $block->id,
        ]);

    $unblockResponse->assertRedirect();

    // Blokir sudah tidak aktif
    expect(LmsStudentSecurityBlock::isStudentBlocked($this->student->id, 'assignment', $this->assignment->id))->toBeFalse();

    // Siswa dapat kembali mengakses tugas
    $assignmentResponse = $this->actingAs($this->siswaUser)
        ->get(route('lms.assignments.show', ['courseClass' => $this->courseClass->id, 'assignment' => $this->assignment->id]));

    $assignmentResponse->assertOk()
        ->assertViewIs('lms.assignments.show');
});

test('security blocking strictly does not affect other menus or modules', function () {
    // Blokir siswa pada kuis
    LmsStudentSecurityBlock::recordBlock(
        $this->courseClass->id,
        $this->student->id,
        'quiz',
        $this->quiz->id,
        'window_blur',
        'Beralih ke aplikasi lain'
    );

    // Kuis terblokir
    expect(LmsStudentSecurityBlock::isStudentBlocked($this->student->id, 'quiz', $this->quiz->id))->toBeTrue();

    // Menu lain tetap dapat diakses bebas oleh siswa:
    // 1. Dashboard
    $this->actingAs($this->siswaUser)->get(route('dashboard'))->assertOk();

    // 2. Beranda LMS
    $this->actingAs($this->siswaUser)->get(route('lms.index'))->assertOk();

    // 3. Jurnal Kebiasaan (Habits)
    $this->actingAs($this->siswaUser)->get(route('habits.journal'))->assertOk();
});

<?php

use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\Extracurricular;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    SchoolSetting::set('enable_attendance_manual', '1');

    Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator', 'level' => 1]);
    Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator', 'level' => 2]);
    Role::firstOrCreate(['name' => 'kepala_sekolah'], ['display_name' => 'Kepala Sekolah', 'level' => 3]);
    Role::firstOrCreate(['name' => 'wakasek_kesiswaan'], ['display_name' => 'Wakasek Kesiswaan', 'level' => 4]);
    Role::firstOrCreate(['name' => 'wakasek_kurikulum'], ['display_name' => 'Wakasek Kurikulum', 'level' => 4]);
    Role::firstOrCreate(['name' => 'koordinator_bk'], ['display_name' => 'Koordinator BK', 'level' => 5]);
    Role::firstOrCreate(['name' => 'guru_bk'], ['display_name' => 'Guru BK', 'level' => 5]);
    Role::firstOrCreate(['name' => 'bk'], ['display_name' => 'BK', 'level' => 5]);
    Role::firstOrCreate(['name' => 'kepala_perpustakaan'], ['display_name' => 'Kepala Perpustakaan', 'level' => 6]);
    Role::firstOrCreate(['name' => 'pustakawan'], ['display_name' => 'Pustakawan', 'level' => 7]);
    Role::firstOrCreate(['name' => 'wali_kelas'], ['display_name' => 'Wali Kelas', 'level' => 5]);
    Role::firstOrCreate(['name' => 'guru'], ['display_name' => 'Guru Mata Pelajaran', 'level' => 6]);
    Role::firstOrCreate(['name' => 'pembina_ekstra'], ['display_name' => 'Pembina Ekstrakurikuler', 'level' => 6]);
    Role::firstOrCreate(['name' => 'sekretaris_kelas'], ['display_name' => 'Sekretaris Kelas', 'level' => 8]);
    Role::firstOrCreate(['name' => 'sekretaris_ekstra'], ['display_name' => 'Sekretaris Ekstrakurikuler', 'level' => 8]);
    Role::firstOrCreate(['name' => 'siswa'], ['display_name' => 'Siswa', 'level' => 9]);

    $this->class1 = SchoolClass::create(['name' => 'X-A', 'grade' => 'X']);
    $this->class2 = SchoolClass::create(['name' => 'X-B', 'grade' => 'X']);

    $this->studentUser1 = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $this->student1 = Student::create([
        'user_id' => $this->studentUser1->id,
        'name' => 'Siswa Satu',
        'nis' => '1001',
        'nisn' => '001111',
        'class_id' => $this->class1->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $this->studentUser2 = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $this->student2 = Student::create([
        'user_id' => $this->studentUser2->id,
        'name' => 'Siswa Dua',
        'nis' => '1002',
        'nisn' => '001112',
        'class_id' => $this->class2->id,
        'gender' => 'P',
        'is_active' => true,
    ]);
});

test('req 1: wakasek kesiswaan is admin of kesiswaan and can verify, edit, and delete violations & achievements', function () {
    $wakasekKesiswaan = User::factory()->create([
        'role' => 'wakasek_kesiswaan',
        'roles' => ['wakasek_kesiswaan', 'guru'],
    ]);

    expect($wakasekKesiswaan->isKesiswaanAdmin())->toBeTrue();

    // Violation verification and deletion
    $vCat = ViolationCategory::create(['code' => 'KAT-DIS', 'name' => 'Kedisiplinan', 'points' => 10]);
    $violation = Violation::create([
        'student_id' => $this->student1->id,
        'class_id' => $this->class1->id,
        'category_id' => $vCat->id,
        'violation_code' => 'PEL-001',
        'title' => 'Terlambat Masuk',
        'points' => 5,
        'date' => now()->toDateString(),
        'status' => 'MENUNGGU_VERIFIKASI',
        'reporter_id' => $wakasekKesiswaan->id,
    ]);

    // Wakasek kesiswaan can approve
    $responseVerify = $this->actingAs($wakasekKesiswaan)->post("/violations/{$violation->id}/verify", [
        'status' => 'DIVERIFIKASI',
    ]);
    $responseVerify->assertSessionHasNoErrors();
    expect($violation->fresh()->status)->toBe('DIVERIFIKASI');

    // Wakasek kesiswaan can delete
    $responseDelete = $this->actingAs($wakasekKesiswaan)->delete("/violations/{$violation->id}");
    $responseDelete->assertSessionHasNoErrors();
    $this->assertDatabaseMissing('violations', ['id' => $violation->id]);

    // Wakasek kesiswaan sees full action buttons (detail, approve, tolak, edit, delete) on index views
    $v3 = Violation::create([
        'student_id' => $this->student1->id,
        'class_id' => $this->class1->id,
        'category_id' => $vCat->id,
        'violation_code' => 'PEL-003',
        'title' => 'Terlambat Upacara',
        'points' => 5,
        'date' => now()->toDateString(),
        'status' => 'MENUNGGU_VERIFIKASI',
        'reporter_id' => $wakasekKesiswaan->id,
    ]);
    $responseViolationsIndex = $this->actingAs($wakasekKesiswaan)->get(route('violations.index'));
    $responseViolationsIndex->assertStatus(200);
    $responseViolationsIndex->assertSee('Lihat Detail');
    $responseViolationsIndex->assertSee('Langsung Approve / Setujui');
    $responseViolationsIndex->assertSee('Tolak Laporan Pelanggaran');
    $responseViolationsIndex->assertSee('Edit Data');
    $responseViolationsIndex->assertSee('Hapus Data');

    $aCat = AchievementCategory::firstOrCreate(['code' => 'R1'], ['name' => 'Prestasi Akademik', 'points' => 10]);
    $ach = Achievement::create([
        'student_id' => $this->student1->id,
        'class_id' => $this->class1->id,
        'category_id' => $aCat->id,
        'achievement_code' => 'PRESTASI-001',
        'title' => 'Juara Olimpiade',
        'points' => 20,
        'level' => 'KABUPATEN',
        'date' => now()->toDateString(),
        'status' => 'MENUNGGU_VERIFIKASI',
        'reporter_id' => $wakasekKesiswaan->id,
    ]);
    $responseAchievementsIndex = $this->actingAs($wakasekKesiswaan)->get(route('achievements.index'));
    $responseAchievementsIndex->assertStatus(200);
    $responseAchievementsIndex->assertSee('Lihat Detail');
    $responseAchievementsIndex->assertSee('Langsung Approve / Setujui');
    $responseAchievementsIndex->assertSee('Tolak Laporan Prestasi');
    $responseAchievementsIndex->assertSee('Edit Data');
    $responseAchievementsIndex->assertSee('Hapus Data');

    // Regular guru cannot verify
    $regularGuru = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
    $violation2 = Violation::create([
        'student_id' => $this->student1->id,
        'class_id' => $this->class1->id,
        'category_id' => $vCat->id,
        'violation_code' => 'PEL-002',
        'title' => 'Seragam Tidak Rapi',
        'points' => 5,
        'date' => now()->toDateString(),
        'status' => 'MENUNGGU_VERIFIKASI',
        'reporter_id' => $regularGuru->id,
    ]);
    $responseGuruVerify = $this->actingAs($regularGuru)->post("/violations/{$violation2->id}/verify", [
        'status' => 'DIVERIFIKASI',
    ]);
    $responseGuruVerify->assertStatus(403);
});

test('req 2: wakasek kurikulum is admin of kurikulum application', function () {
    $wakasekKurikulum = User::factory()->create([
        'role' => 'wakasek_kurikulum',
        'roles' => ['wakasek_kurikulum', 'guru'],
    ]);

    expect($wakasekKurikulum->isKurikulumAdmin())->toBeTrue();
    expect($wakasekKurikulum->canAccessModule('kurikulum'))->toBeTrue();
});

test('req 3: koordinator bk is admin of bk, and kepala sekolah, wakasek kesiswaan, guru bk have full bk access', function () {
    $koordinatorBk = User::factory()->create(['role' => 'koordinator_bk', 'roles' => ['koordinator_bk', 'guru_bk']]);
    $kepalaSekolah = User::factory()->create(['role' => 'kepala_sekolah', 'roles' => ['kepala_sekolah']]);
    $wakasekKesiswaan = User::factory()->create(['role' => 'wakasek_kesiswaan', 'roles' => ['wakasek_kesiswaan']]);
    $guruBk = User::factory()->create(['role' => 'guru_bk', 'roles' => ['guru_bk']]);
    $regularGuru = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);

    expect($koordinatorBk->isBkAdmin())->toBeTrue()
        ->and($koordinatorBk->hasFullBkAccess())->toBeTrue()
        ->and($kepalaSekolah->hasFullBkAccess())->toBeTrue()
        ->and($wakasekKesiswaan->hasFullBkAccess())->toBeTrue()
        ->and($guruBk->hasFullBkAccess())->toBeTrue()
        ->and($regularGuru->hasFullBkAccess())->toBeFalse();

    // Koordinator BK can create counseling session
    $responseKoordinator = $this->actingAs($koordinatorBk)->post('/bk/konseling', [
        'student_id' => $this->student1->id,
        'session_type' => 'INDIVIDUAL',
        'field_of_service' => 'BELAJAR',
        'service_category' => 'LAYANAN_RESPONSIF',
        'date' => now()->toDateString(),
        'topic' => 'Motivasi Belajar',
        'problem_description' => 'Penurunan konsentrasi belajar',
        'status' => 'TERJADWAL',
    ]);
    $responseKoordinator->assertSessionHasNoErrors();
    $this->assertDatabaseHas('bk_counseling_sessions', ['student_id' => $this->student1->id]);

    // Regular guru is forbidden from creating counseling sessions
    $responseGuru = $this->actingAs($regularGuru)->post('/bk/konseling', [
        'student_id' => $this->student2->id,
        'session_type' => 'INDIVIDUAL',
        'field_of_service' => 'SOSIAL',
        'service_category' => 'LAYANAN_RESPONSIF',
        'date' => now()->toDateString(),
        'topic' => 'Isu Teman Sebaya',
        'problem_description' => 'Deskripsi',
        'status' => 'TERJADWAL',
    ]);
    $responseGuru->assertStatus(403);
});

test('req 4: kepala perpustakaan is admin of library application and can access library settings', function () {
    $kepalaPerpus = User::factory()->create(['role' => 'kepala_perpustakaan', 'roles' => ['kepala_perpustakaan']]);
    $regularStaff = User::factory()->create(['role' => 'staff', 'roles' => ['staff']]);

    expect($kepalaPerpus->isLibraryAdmin())->toBeTrue()
        ->and($kepalaPerpus->canManageLibrary())->toBeTrue()
        ->and($regularStaff->isLibraryAdmin())->toBeFalse();

    $responsePerpus = $this->actingAs($kepalaPerpus)->get('/library/settings');
    $responsePerpus->assertStatus(200);

    $responseStaff = $this->actingAs($regularStaff)->get('/library/settings');
    $responseStaff->assertStatus(403);
});

test('req 5: manual attendance on primary & worship only by wakasek kesiswaan, homeroom on own class, and secretary on own class', function () {
    $wakasek = User::factory()->create(['role' => 'wakasek_kesiswaan', 'roles' => ['wakasek_kesiswaan']]);

    $homeroomUser = User::factory()->create(['role' => 'wali_kelas', 'roles' => ['wali_kelas', 'guru']]);
    $teacher = Teacher::create([
        'user_id' => $homeroomUser->id,
        'name' => 'Wali Kelas 1',
        'nip' => '19800101',
        'gender' => 'L',
        'is_active' => true,
    ]);
    $this->class1->update(['homeroom_teacher_id' => $teacher->id]);

    $secretaryUser = User::factory()->create(['role' => 'sekretaris_kelas', 'roles' => ['sekretaris_kelas', 'siswa']]);
    $secStudent = Student::create([
        'user_id' => $secretaryUser->id,
        'name' => 'Sekretaris Kelas 1',
        'nis' => '1003',
        'nisn' => '001113',
        'class_id' => $this->class1->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $regularGuru = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);

    // 1. Wakasek Kesiswaan can record for any class
    expect($wakasek->canRecordAttendanceManual($this->class1->id))->toBeTrue()
        ->and($wakasek->canRecordAttendanceManual($this->class2->id))->toBeTrue();

    // 2. Wali Kelas can record only for class1
    expect($homeroomUser->canRecordAttendanceManual($this->class1->id))->toBeTrue()
        ->and($homeroomUser->canRecordAttendanceManual($this->class2->id))->toBeFalse();

    // 3. Sekretaris Kelas can record only for class1
    expect($secretaryUser->canRecordAttendanceManual($this->class1->id))->toBeTrue()
        ->and($secretaryUser->canRecordAttendanceManual($this->class2->id))->toBeFalse();

    // 4. Regular Guru cannot record manual attendance
    expect($regularGuru->canRecordAttendanceManual($this->class1->id))->toBeFalse();

    // Test POST to AttendanceController::storeManualBulk
    // Wakasek can record class 2
    $responseWakasek = $this->actingAs($wakasek)->post(route('attendance.manual.bulk'), [
        'class_id' => $this->class2->id,
        'date' => now()->toDateString(),
        'statuses' => [$this->student2->id => 'HADIR'],
    ]);
    $responseWakasek->assertSessionHasNoErrors();

    // Wali Kelas 1 rejected when attempting class 2
    $responseWaliForbidden = $this->actingAs($homeroomUser)->post(route('attendance.manual.bulk'), [
        'class_id' => $this->class2->id,
        'date' => now()->toDateString(),
        'statuses' => [$this->student2->id => 'HADIR'],
    ]);
    $responseWaliForbidden->assertStatus(403);

    // Regular Guru rejected
    $responseGuruForbidden = $this->actingAs($regularGuru)->post(route('attendance.manual.bulk'), [
        'class_id' => $this->class1->id,
        'date' => now()->toDateString(),
        'statuses' => [$this->student1->id => 'HADIR'],
    ]);
    $responseGuruForbidden->assertStatus(403);
});

test('req 6: manual extracurricular attendance only by wakasek kesiswaan, pembina ekstra, and sekretaris ekstra', function () {
    $wakasek = User::factory()->create(['role' => 'wakasek_kesiswaan', 'roles' => ['wakasek_kesiswaan']]);
    $pembina = User::factory()->create(['role' => 'pembina_ekstra', 'roles' => ['pembina_ekstra', 'guru']]);
    $sekretaris = User::factory()->create(['role' => 'sekretaris_ekstra', 'roles' => ['sekretaris_ekstra', 'siswa']]);
    $regularGuru = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
    $regularStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);

    $extra = Extracurricular::create([
        'code' => 'PRAMUKA',
        'name' => 'Pramuka',
        'category' => 'Wajib',
        'is_active' => true,
    ]);

    expect($wakasek->canRecordExtracurricularManual($extra->id))->toBeTrue()
        ->and($pembina->canRecordExtracurricularManual($extra->id))->toBeTrue()
        ->and($sekretaris->canRecordExtracurricularManual($extra->id))->toBeTrue()
        ->and($regularGuru->canRecordExtracurricularManual($extra->id))->toBeFalse()
        ->and($regularStudent->canRecordExtracurricularManual($extra->id))->toBeFalse();

    // Test POST to ExtracurricularAttendanceController::storeManual
    $responseWakasek = $this->actingAs($wakasek)->post('/extracurricular/manual', [
        'extracurricular_id' => $extra->id,
        'date' => now()->toDateString(),
        'statuses' => [$this->student1->id => 'HADIR'],
    ]);
    $responseWakasek->assertSessionHasNoErrors();

    // Regular guru rejected
    $responseGuru = $this->actingAs($regularGuru)->post('/extracurricular/manual', [
        'extracurricular_id' => $extra->id,
        'date' => now()->toDateString(),
        'statuses' => [$this->student1->id => 'HADIR'],
    ]);
    $responseGuru->assertStatus(403);
});

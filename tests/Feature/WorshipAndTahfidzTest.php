<?php

use App\Http\Controllers\DashboardController;
use App\Models\ParentGuardian;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\WorshipAttendance;
use Carbon\Carbon;

test('sesi dhuha time-lock strictly allows Thursday and Friday 06:30-07:30 only', function () {
    // Thursday 07:00 -> Allowed
    $thursdayValid = Carbon::parse('2026-10-01 07:00:00', 'Asia/Jakarta');
    $check1 = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DHUHA, $thursdayValid);
    expect($check1['allowed'])->toBeTrue();

    // Thursday 08:00 (too late) -> Rejected
    $thursdayLate = Carbon::parse('2026-10-01 08:00:00', 'Asia/Jakarta');
    $check2 = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DHUHA, $thursdayLate);
    expect($check2['allowed'])->toBeFalse()
        ->and($check2['reason'])->toContain('telah berakhir');

    // Monday 07:00 (wrong day) -> Rejected
    $monday = Carbon::parse('2026-09-28 07:00:00', 'Asia/Jakarta');
    $check3 = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DHUHA, $monday);
    expect($check3['allowed'])->toBeFalse()
        ->and($check3['reason'])->toContain('bukan hari pelaksanaan');
});

test('sesi dzuhur time-lock strictly allows Monday to Friday 11:30-13:15 only', function () {
    // Tuesday 12:00 -> Allowed
    $tuesdayValid = Carbon::parse('2026-09-29 12:00:00', 'Asia/Jakarta');
    $check1 = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DZUHUR_JUMAT, $tuesdayValid);
    expect($check1['allowed'])->toBeTrue();

    // Tuesday 14:00 (too late) -> Rejected
    $tuesdayLate = Carbon::parse('2026-09-29 14:00:00', 'Asia/Jakarta');
    $check2 = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DZUHUR_JUMAT, $tuesdayLate);
    expect($check2['allowed'])->toBeFalse()
        ->and($check2['reason'])->toContain('telah berakhir');

    // Sunday 12:00 (weekend) -> Rejected
    $sunday = Carbon::parse('2026-10-04 12:00:00', 'Asia/Jakarta');
    $check3 = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DZUHUR_JUMAT, $sunday);
    expect($check3['allowed'])->toBeFalse()
        ->and($check3['reason'])->toContain('bukan hari pelaksanaan');
});

test('sesi keputrian time-lock strictly allows Friday 11:30-13:30 only', function () {
    // Friday 12:00 -> Allowed
    $fridayValid = Carbon::parse('2026-10-02 12:00:00', 'Asia/Jakarta');
    $check1 = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_KEPUTRIAN, $fridayValid);
    expect($check1['allowed'])->toBeTrue();

    // Thursday 12:00 (wrong day) -> Rejected
    $thursday = Carbon::parse('2026-10-01 12:00:00', 'Asia/Jakarta');
    $check2 = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_KEPUTRIAN, $thursday);
    expect($check2['allowed'])->toBeFalse()
        ->and($check2['reason'])->toContain('bukan hari pelaksanaan');
});

test('worship scan enforces gender rules strictly', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::factory()->create(['role' => 'super_admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);

    $maleStudent = Student::where('gender', 'L')->first();
    $femaleStudent = Student::where('gender', 'P')->first();

    if (! $maleStudent) {
        $maleStudent = Student::create([
            'name' => 'Ahmad Santoso',
            'gender' => 'L',
            'nisn' => '9991112221',
            'nis' => '1001',
            'class_id' => $class->id,
            'is_active' => true,
        ]);
    }

    if (! $femaleStudent) {
        $femaleStudent = Student::create([
            'name' => 'Siti Nurhaliza',
            'gender' => 'P',
            'nisn' => '9991112222',
            'nis' => '1002',
            'class_id' => $class->id,
            'is_active' => true,
        ]);
    }

    // Dhuha scan with Female student -> Allowed (Dhuha applies to all students)
    Carbon::setTestNow(Carbon::parse('2026-10-01 07:00:00', 'Asia/Jakarta'));
    $response = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $femaleStudent->nisn,
    ]);
    $response->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    // Hari Jum'at: Siswi perempuan DIPERBOLEHKAN scan Dzuhur/Jum'at (berlaku untuk siswa putra & putri)
    Carbon::setTestNow(Carbon::parse('2026-10-02 12:00:00', 'Asia/Jakarta'));
    $responseDzuhurFriday = $this->actingAs($admin)->postJson(route('worship.scan', 'dzuhur-jumat'), [
        'identifier' => $femaleStudent->nisn,
        'bypass_time_lock' => true,
    ]);
    $responseDzuhurFriday->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    // Hari Kamis: Siswi perempuan juga DIPERBOLEHKAN scan Dzuhur
    Carbon::setTestNow(Carbon::parse('2026-10-01 12:00:00', 'Asia/Jakarta'));
    $responseDzuhurThursday = $this->actingAs($admin)->postJson(route('worship.scan', 'dzuhur-jumat'), [
        'identifier' => $femaleStudent->nisn,
        'bypass_time_lock' => true,
    ]);
    $responseDzuhurThursday->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    // Attempt Keputrian scan with Male student on Friday 12:00 -> Rejected (Keputrian is female only)
    Carbon::setTestNow(Carbon::parse('2026-10-02 12:00:00', 'Asia/Jakarta'));
    $response2 = $this->actingAs($admin)->postJson(route('worship.scan', 'keputrian'), [
        'identifier' => $maleStudent->nisn,
    ]);
    $response2->assertStatus(422)
        ->assertJsonFragment(['error_code' => 'GENDER_RESTRICTION']);

    // Valid Dhuha scan with Male student on Thursday 07:00 -> Success
    Carbon::setTestNow(Carbon::parse('2026-10-01 07:00:00', 'Asia/Jakarta'));
    $response3 = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $maleStudent->nisn,
    ]);
    $response3->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    // Duplicate scan on same day -> Rejected
    $response4 = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $maleStudent->nisn,
        'bypass_time_lock' => true,
    ]);
    $response4->assertStatus(422)
        ->assertJsonFragment(['error_code' => 'ALREADY_RECORDED']);

    Carbon::setTestNow();
});

test('parent dashboard hides keputrian card for male child and shows for female child', function () {
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);

    // Male child and his parent
    $maleStudent = Student::create([
        'name' => 'Budi Putra Santoso',
        'gender' => 'L',
        'nisn' => '9988112201',
        'nis' => '7701',
        'class_id' => $class->id,
        'is_active' => true,
    ]);
    $maleParentUser = User::factory()->create(['role' => 'orang_tua']);
    ParentGuardian::create([
        'user_id' => $maleParentUser->id,
        'student_id' => $maleStudent->id,
        'relationship' => 'ayah',
        'name' => 'Pak Santoso',
        'phone' => '081234567891',
    ]);

    // Female child and her parent
    $femaleStudent = Student::create([
        'name' => 'Siti Putri Rahma',
        'gender' => 'P',
        'nisn' => '9988112202',
        'nis' => '7702',
        'class_id' => $class->id,
        'is_active' => true,
    ]);
    $femaleParentUser = User::factory()->create(['role' => 'orang_tua']);
    ParentGuardian::create([
        'user_id' => $femaleParentUser->id,
        'student_id' => $femaleStudent->id,
        'relationship' => 'ibu',
        'name' => 'Ibu Rahma',
        'phone' => '081234567892',
    ]);

    // 1. Akun Orang Tua dengan anak laki-laki: Keputrian TIDAK DITAMPILKAN
    $resMaleParent = $this->actingAs($maleParentUser)->get(route('dashboard'));
    $resMaleParent->assertStatus(200);
    $resMaleParent->assertSee('Shalat Dhuha Bersama');
    $resMaleParent->assertSee('Dzuhur / Jum\'at Berjamaah', false);
    $resMaleParent->assertDontSee('Presensi Keputrian');

    // 2. Akun Orang Tua dengan anak perempuan: Keputrian DITAMPILKAN
    $resFemaleParent = $this->actingAs($femaleParentUser)->get(route('dashboard'));
    $resFemaleParent->assertStatus(200);
    $resFemaleParent->assertSee('Shalat Dhuha Bersama');
    $resFemaleParent->assertSee('Dzuhur / Jum\'at Berjamaah', false);
    $resFemaleParent->assertSee('Presensi Keputrian');

    // 3. Shalat Dzuhur berlaku untuk siswa laki-laki dan perempuan setiap hari termasuk Jumat
    Carbon::setTestNow(Carbon::parse('2026-10-02 12:00:00', 'Asia/Jakarta'));
    $resFriday = $this->actingAs($femaleParentUser)->get(route('dashboard'));
    $resFriday->assertStatus(200);
    $resFriday->assertSee('Dzuhur / Jum\'at Berjamaah', false);
    $resFriday->assertSee('Presensi Keputrian');

    Carbon::setTestNow();
});

test('tahfidz access control prevents students from creating deposits but allows teachers', function () {
    $guru = User::where('role', 'guru')->first() ?? User::factory()->create(['role' => 'guru', 'name' => 'Ustadz Abdullah']);
    $studentUser = User::where('role', 'siswa')->first() ?? User::factory()->create(['role' => 'siswa']);
    $student = Student::first();

    // Student attempts to access create form -> 403 Forbidden
    $this->actingAs($studentUser)->get(route('tahfidz.create'))
        ->assertStatus(403);

    // Teacher accesses create form -> 200 OK
    $this->actingAs($guru)->get(route('tahfidz.create'))
        ->assertStatus(200);

    // Teacher stores deposit -> Success
    $postResponse = $this->actingAs($guru)->post(route('tahfidz.store'), [
        'student_id' => $student->id,
        'surah_number' => 78,
        'ayat_range' => 'Ayat 1 s.d. 40 (Selesai)',
        'makhraj' => 'Sangat Baik',
        'tajwid' => 'Baik',
        'kelancaran' => 'Sangat Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => 'Bacaan tajwid sangat rapi dan tartil.',
        'deposit_date' => Carbon::today()->toDateString(),
        'deposit_time' => '07:30',
        'examiner_name' => $guru->name,
    ]);

    $postResponse->assertRedirect(route('tahfidz.student', $student->id));

    expect(QuranTahfidzRecord::where('student_id', $student->id)->where('surah_number', 78)->exists())->toBeTrue();
});

test('worship and tahfidz report pages load successfully', function () {
    $admin = User::where('role', 'super_admin')->first();
    $student = Student::first();

    $this->actingAs($admin)->get('/worship/dhuha')->assertStatus(200);
    $this->actingAs($admin)->get('/worship/dzuhur-jumat')->assertStatus(200);
    $this->actingAs($admin)->get('/worship/keputrian')->assertStatus(200);

    $this->actingAs($admin)->get(route('worship.report', 'dhuha'))->assertStatus(200);
    $this->actingAs($admin)->get(route('worship.report', 'dzuhur-jumat'))->assertStatus(200);
    $this->actingAs($admin)->get(route('worship.report', 'keputrian'))->assertStatus(200);
    $this->actingAs($admin)->get(route('tahfidz.report'))->assertStatus(200);

    $this->actingAs($admin)->get(route('worship.print', 'dhuha'))->assertStatus(200);
    $this->actingAs($admin)->get(route('tahfidz.print'))->assertStatus(200);
    $this->actingAs($admin)->get(route('tahfidz.print_student', $student->id))->assertStatus(200);
});

test('tahfidz index renders student progress report for siswa, sekretaris_kelas, and orang_tua', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'XII-MIPA-1'], ['grade' => '12', 'major' => 'MIPA']);

    // 1. Akun Siswa Reguler
    $userSiswa = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);
    $studentA = Student::create([
        'user_id' => $userSiswa->id,
        'class_id' => $class->id,
        'name' => 'Ahmad Santri',
        'gender' => 'L',
        'nisn' => '9991112221',
        'nis' => '9101',
        'is_active' => true,
    ]);

    // 2. Akun Sekretaris Kelas
    $userSekretaris = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa', 'sekretaris_kelas'],
    ]);
    $studentB = Student::create([
        'user_id' => $userSekretaris->id,
        'class_id' => $class->id,
        'name' => 'Budi Sekretaris',
        'gender' => 'L',
        'nisn' => '9991112222',
        'nis' => '9102',
        'is_active' => true,
    ]);

    // 3. Akun Orang Tua Siswa A
    $userOrangTua = User::factory()->create([
        'role' => 'orang_tua',
        'roles' => ['orang_tua'],
    ]);
    ParentGuardian::create([
        'user_id' => $userOrangTua->id,
        'student_id' => $studentA->id,
        'name' => 'Orang Tua Ahmad',
        'relation_type' => 'AYAH',
    ]);

    // Berikan catatan setoran untuk Siswa A
    QuranTahfidzRecord::create([
        'student_id' => $studentA->id,
        'class_id' => $class->id,
        'juz' => 30,
        'surah_number' => 78,
        'surah_name' => "An-Naba'",
        'ayat_range' => 'Ayat 1-40',
        'makhraj' => 'Sangat Baik',
        'tajwid' => 'Baik',
        'kelancaran' => 'Sangat Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => 'Hafalan sangat mutqin dan lancar.',
        'deposit_date' => Carbon::today()->toDateString(),
        'deposit_time' => '08:00',
        'examiner_name' => 'Ustadz Penguji',
    ]);

    // Test 1: Siswa reguler mengakses kontrol tahfidz (route tahfidz.index)
    // Harus langsung melihat laporan progres hafalan miliknya
    $responseSiswa = $this->actingAs($userSiswa)->get(route('tahfidz.index'));
    $responseSiswa->assertStatus(200)
        ->assertSee('Capaian Hafalan Surat Sesuai Kelas X')
        ->assertSee('Ahmad Santri')
        ->assertSee("An-Naba'")
        ->assertSee('Tabel Buku Kontrol')
        ->assertDontSee('Input Setoran Baru');

    // Test 2: Sekretaris Kelas mengakses kontrol tahfidz (route tahfidz.index)
    // Harus langsung melihat laporan progres hafalan dirinya sendiri juga
    $responseSekretaris = $this->actingAs($userSekretaris)->get(route('tahfidz.index'));
    $responseSekretaris->assertStatus(200)
        ->assertSee('Capaian Hafalan Surat Sesuai Kelas X')
        ->assertSee('Budi Sekretaris')
        ->assertSee('Sekretaris Kelas')
        ->assertSee('Tabel Buku Kontrol')
        ->assertDontSee('Input Setoran Baru');

    // Test 3: Orang Tua mengakses kontrol tahfidz (route tahfidz.index)
    // Harus langsung melihat laporan progres hafalan anandanya (Ahmad Santri)
    $responseOrangTua = $this->actingAs($userOrangTua)->get(route('tahfidz.index'));
    $responseOrangTua->assertStatus(200)
        ->assertSee('Capaian Hafalan Surat Sesuai Kelas X')
        ->assertSee('Ahmad Santri')
        ->assertSee('Tabel Buku Kontrol')
        ->assertDontSee('Input Setoran Baru');

    // Test 4: Proteksi akses - Siswa A tidak dapat mengintip Siswa B
    $this->actingAs($userSiswa)->get(route('tahfidz.student', $studentB->id))
        ->assertRedirect(route('tahfidz.student', $studentA->id));

    // Test 5: Siswa mengakses tahfidz.report dialihkan ke tahfidz.index
    $this->actingAs($userSiswa)->get(route('tahfidz.report'))
        ->assertRedirect(route('tahfidz.index'));

    // Test 6: Guru/Admin tetap melihat tabel manajemen di tahfidz.index
    $admin = User::where('role', 'super_admin')->first();
    $responseAdmin = $this->actingAs($admin)->get(route('tahfidz.index'));
    $responseAdmin->assertStatus(200)
        ->assertSee('Input Setoran Baru')
        ->assertSee('Dokumen Laporan')
        ->assertSee('Cari Siswa / Ayat / Catatan');
});

test('worship attendance displays date alongside day across views and validation', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::factory()->create(['role' => 'super_admin']);

    // 1. Time-lock validation returns current date and day-date
    $timeLock = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DHUHA);
    expect($timeLock)->toHaveKeys(['current_date', 'current_date_formatted', 'current_day_date']);

    // 2. Worship index page displays Tanggal Presensi and days
    $resDhuha = $this->actingAs($admin)->get('/worship/dhuha');
    $resDhuha->assertStatus(200)
        ->assertSee('Tanggal Presensi:')
        ->assertSee('Hari & Tanggal', false)
        ->assertSee('Waktu Sistem:');

    $resDzuhur = $this->actingAs($admin)->get('/worship/dzuhur-jumat');
    $resDzuhur->assertStatus(200)
        ->assertSee('Tanggal Presensi:')
        ->assertSee('Hari & Tanggal', false);

    $resKeputrian = $this->actingAs($admin)->get('/worship/keputrian');
    $resKeputrian->assertStatus(200)
        ->assertSee('Tanggal Presensi:')
        ->assertSee('Hari & Tanggal', false);

    // 3. Worship report displays Hari & Tanggal header
    $resReport = $this->actingAs($admin)->get(route('worship.report', 'dhuha'));
    $resReport->assertStatus(200)
        ->assertSee('Hari & Tanggal', false);
});

test('manual worship attendance does not preselect HADIR by default and handles saving', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::factory()->create(['role' => 'super_admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $testDate = '2026-10-01';
    Carbon::setTestNow(Carbon::parse('2026-10-01 07:00:00', 'Asia/Jakarta'));

    $student = Student::where('class_id', $class->id)->first();
    if (! $student) {
        $student = Student::create([
            'name' => 'Testing Manual Siswa',
            'gender' => 'L',
            'nisn' => '9999988881',
            'nis' => '8881',
            'class_id' => $class->id,
            'is_active' => true,
        ]);
    }

    // Ensure no attendance exists on this testDate
    WorshipAttendance::where('student_id', $student->id)->whereDate('date', $testDate)->delete();

    // 1. Loading page shows 'Belum Terdata' and does NOT check HADIR
    $response = $this->actingAs($admin)->get('/worship/dhuha?class_id='.$class->id.'&date='.$testDate);
    $response->assertStatus(200)
        ->assertSee('Belum Terdata')
        ->assertSee('Kosongkan Pilihan')
        ->assertSee('Tandai Semua Hadir');

    expect($response->getContent())->not->toContain('name="statuses['.$student->id.']" value="HADIR" checked');

    // 2. Submitting empty statuses redirects with warning
    $postEmpty = $this->actingAs($admin)->post(route('worship.manual', 'dhuha'), [
        'class_id' => $class->id,
        'date' => $testDate,
    ]);
    $postEmpty->assertRedirect()->assertSessionHas('warning');

    // 3. Submitting selected student status saves correctly
    $postValid = $this->actingAs($admin)->post(route('worship.manual', 'dhuha'), [
        'class_id' => $class->id,
        'date' => $testDate,
        'statuses' => [
            $student->id => 'HADIR',
        ],
    ]);
    $postValid->assertRedirect()->assertSessionHas('success');

    $record = WorshipAttendance::where('student_id', $student->id)
        ->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA)
        ->whereDate('date', $testDate)
        ->first();

    expect($record)->not->toBeNull()
        ->and($record->status)->toBe('HADIR');
});

test('worship attendance rate is 0% when no sessions have been conducted and calculates real percentage when conducted', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::factory()->create(['role' => 'super_admin']);
    $student = Student::first();

    // 1. Clear any existing records for test isolation
    WorshipAttendance::truncate();

    // 2. When no records exist (belum dilaksanakan), summary rate must be 0% NOT 100%
    $controller = app(DashboardController::class);
    $summary = $controller->getWorshipTahfidzDashboardData(null, null);

    expect($summary['dhuha']['total_records'])->toBe(0)
        ->and($summary['dhuha']['rate'])->toBe(0.0)
        ->and($summary['dzuhur']['total_records'])->toBe(0)
        ->and($summary['dzuhur']['rate'])->toBe(0.0)
        ->and($summary['keputrian']['total_records'])->toBe(0)
        ->and($summary['keputrian']['rate'])->toBe(0.0);

    // 3. Admin dashboard view must display 0% and not 100% when no sessions exist
    $response = $this->actingAs($admin)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('0%');

    // 4. When records exist (misal 1 hadir, 1 alpa dari 2 sesi) -> 50%
    WorshipAttendance::query()->delete();
    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $student->class_id,
        'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
        'date' => '2026-10-01',
        'time' => '07:00:00',
        'status' => 'HADIR',
        'recorded_by' => $admin->id,
    ]);
    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $student->class_id,
        'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
        'date' => '2026-10-02',
        'time' => '07:00:00',
        'status' => 'ALPA',
        'recorded_by' => $admin->id,
    ]);

    $summaryWithData = $controller->getWorshipTahfidzDashboardData(null, null);
    expect($summaryWithData['dhuha']['total_records'])->toBe(2)
        ->and($summaryWithData['dhuha']['hadir_records'])->toBe(1)
        ->and($summaryWithData['dhuha']['rate'])->toBe(50.0);
});

test('tahfidz Juz 30 surahs are properly classified by class grade', function () {
    $allSurahs = QuranTahfidzRecord::getJuz30Surahs();
    expect(count($allSurahs))->toBe(37);

    // Kelas X: 25 Surat (No. 90 Al-Balad s.d. 114 An-Nas)
    $gradeX = QuranTahfidzRecord::getSurahsByGrade('X');
    expect(count($gradeX))->toBe(25);
    expect(isset($gradeX[90]))->toBeTrue();
    expect($gradeX[90]['name'])->toBe('Al-Balad');
    expect($gradeX[90]['total_ayah'])->toBe(20);
    expect(isset($gradeX[114]))->toBeTrue();
    expect($gradeX[114]['name'])->toBe('An-Nas');
    expect($gradeX[114]['total_ayah'])->toBe(6);

    // Kelas XI: 7 Surat (No. 83 Al-Mutaffifin s.d. 89 Al-Fajr)
    $gradeXI = QuranTahfidzRecord::getSurahsByGrade('XI');
    expect(count($gradeXI))->toBe(7);
    expect(isset($gradeXI[83]))->toBeTrue();
    expect($gradeXI[83]['name'])->toBe('Al-Mutaffifin');
    expect($gradeXI[83]['total_ayah'])->toBe(36);
    expect(isset($gradeXI[89]))->toBeTrue();
    expect($gradeXI[89]['name'])->toBe('Al-Fajr');
    expect($gradeXI[89]['total_ayah'])->toBe(30);

    // Kelas XII: 5 Surat (No. 78 An-Naba' s.d. 82 Al-Infitar)
    $gradeXII = QuranTahfidzRecord::getSurahsByGrade('XII');
    expect(count($gradeXII))->toBe(5);
    expect(isset($gradeXII[78]))->toBeTrue();
    expect($gradeXII[78]['name'])->toBe("An-Naba'");
    expect($gradeXII[78]['total_ayah'])->toBe(40);
    expect(isset($gradeXII[82]))->toBeTrue();
    expect($gradeXII[82]['name'])->toBe('Al-Infitar');
    expect($gradeXII[82]['total_ayah'])->toBe(19);

    // Grouping helper
    $grouped = QuranTahfidzRecord::getSurahsGroupedByGrade();
    expect(count($grouped['X']['surahs']))->toBe(25);
    expect(count($grouped['XI']['surahs']))->toBe(7);
    expect(count($grouped['XII']['surahs']))->toBe(5);
});

test('tahfidz deposit supports ayat_count input on create and edit forms', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::factory()->create(['role' => 'super_admin']);
    $student = Student::first();

    // 1. Create form renders ayat_count field and class filters
    $createView = $this->actingAs($admin)->get(route('tahfidz.create', ['student_id' => $student->id]));
    $createView->assertStatus(200);
    $createView->assertSee('name="ayat_count"', false);
    $createView->assertSee('Jumlah Ayat', false);
    $createView->assertSee('Kelas X (25)', false);
    $createView->assertSee('Kelas XI (7)', false);
    $createView->assertSee('Kelas XII (5)', false);

    // 2. Store deposit with explicit ayat_count
    $postResponse = $this->actingAs($admin)->post(route('tahfidz.store'), [
        'student_id' => $student->id,
        'surah_number' => 90, // Al-Balad
        'ayat_range' => 'Ayat 1 s.d. 20 (Selesai)',
        'ayat_count' => 20,
        'makhraj' => 'Sangat Baik',
        'tajwid' => 'Sangat Baik',
        'kelancaran' => 'Sangat Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => 'Hafalan Al-Balad mutqin.',
        'deposit_date' => Carbon::today()->toDateString(),
        'deposit_time' => '07:30',
        'examiner_name' => 'Ustadz Penguji',
    ]);

    $postResponse->assertRedirect(route('tahfidz.student', $student->id));

    $record = QuranTahfidzRecord::where('student_id', $student->id)->where('surah_number', 90)->first();
    expect($record)->not->toBeNull();
    expect($record->ayat_count)->toBe(20);

    // 3. Edit form renders ayat_count field with existing value
    $editView = $this->actingAs($admin)->get(route('tahfidz.edit', $record->id));
    $editView->assertStatus(200);
    $editView->assertSee('name="ayat_count"', false);
    $editView->assertSee('value="20"', false);

    // 4. Update deposit with new ayat_count
    $putResponse = $this->actingAs($admin)->put(route('tahfidz.update', $record->id), [
        'surah_number' => 90,
        'ayat_range' => 'Ayat 1 s.d. 10',
        'ayat_count' => 10,
        'makhraj' => 'Baik',
        'tajwid' => 'Baik',
        'kelancaran' => 'Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => 'Setor separuh awal.',
        'deposit_date' => Carbon::today()->toDateString(),
        'deposit_time' => '08:00',
        'examiner_name' => 'Ustadz Penguji',
    ]);

    $putResponse->assertRedirect(route('tahfidz.student', $student->id));
    $record->refresh();
    expect($record->ayat_count)->toBe(10);
    expect($record->ayat_range)->toBe('Ayat 1 s.d. 10');
});

test('akun siswa accessing tahfidz index displays complete achievement and progress information according to class surahs', function () {
    $class = SchoolClass::updateOrCreate(['name' => 'X-1'], ['grade' => 'X', 'level' => '10']);
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'name' => 'Muhammad Hafizh',
        'nisn' => '9988776655',
        'nis' => '8899',
        'gender' => 'L',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    // Berikan setoran tuntas surat Al-Ikhlas (surat target Kelas X)
    QuranTahfidzRecord::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'juz' => 30,
        'surah_number' => 112,
        'surah_name' => 'Al-Ikhlas',
        'ayat_range' => 'Ayat 1 s.d. 4 (Selesai)',
        'ayat_count' => 4,
        'makhraj' => 'Sangat Baik',
        'tajwid' => 'Sangat Baik',
        'kelancaran' => 'Sangat Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => 'Lancar dan fasih.',
        'deposit_date' => Carbon::today()->toDateString(),
        'deposit_time' => '07:30',
        'examiner_name' => 'Ustadz Pembina',
    ]);

    $response = $this->actingAs($studentUser)->get(route('tahfidz.index'));
    $response->assertStatus(200);

    // Memverifikasi informasi pencapaian dan progres komplit sesuai surat dalam kelasnya
    $response->assertSee('Muhammad Hafizh', false);
    $response->assertSee('9988776655', false);
    $response->assertSee('Kelas X', false);
    $response->assertSee('Kurikulum Tahfidz Kelas X', false);
    $response->assertSee('Capaian Hafalan Surat Sesuai Kelas X (25 Surat)', false);
    $response->assertSee('Target Surat Kelas X', false);
    $response->assertSee('25', false);
    $response->assertSee('Al-Ikhlas', false);
    $response->assertSee('Al-Balad', false);
    $response->assertSee('An-Nas', false);
    $response->assertSee('Tabel Buku Kontrol', false);
    $response->assertSee('Kartu Visual', false);
    $response->assertSee('Riwayat Lengkap Setoran Siswa', false);
    $response->assertSee('Cetak Kartu Siswa', false);
});

test('akun wali kelas kelas X displays student deposit management and only surahs appropriate for grade X', function () {
    $classX = SchoolClass::updateOrCreate(['name' => 'X-A'], ['grade' => 'X', 'level' => '10']);
    $waliUser = User::factory()->create(['role' => 'wali_kelas']);
    $teacher = Teacher::create([
        'user_id' => $waliUser->id,
        'name' => 'Ustadzah Wali X',
        'nip' => '198501012010012001',
    ]);
    $classX->update(['homeroom_teacher_id' => $teacher->id]);

    $student = Student::create([
        'name' => 'Ahmad Kelas X',
        'nisn' => '7788990011',
        'nis' => '7711',
        'gender' => 'L',
        'class_id' => $classX->id,
        'is_active' => true,
    ]);

    // 1. Menu kontrol tahfidz untuk Wali Kelas X
    $response = $this->actingAs($waliUser)->get(route('tahfidz.index'));
    $response->assertStatus(200);

    // Memverifikasi header & identitas kelas binaan
    $response->assertSee('Setor Hafalan Siswa Kelas X-A', false);
    $response->assertSee('Wali Kelas Binaan', false);
    $response->assertSee('Daftar Siswa', false);
    $response->assertSee('Ahmad Kelas X', false);
    $response->assertSee('Setor Hafalan', false);

    // Memverifikasi surat yang tampil adalah surat target Kelas X (25 surat)
    $response->assertSee('Al-Balad', false);
    $response->assertSee('Asy-Syams', false);
    $response->assertSee('Al-Lail', false);
    $response->assertSee('An-Nas', false);
    $response->assertSee('Surat-Surat Target Kurikulum Kelas X (25 Surat)', false);

    // Memverifikasi surat kelas XI dan XII TIDAK tampil di daftar surat target kelas X
    $response->assertDontSee('Al-Mutaffifin (36 ay.)', false);
    $response->assertDontSee('An-Naba\' (40 ay.)', false);

    // 2. Form input setoran untuk Wali Kelas X
    $createResponse = $this->actingAs($waliUser)->get(route('tahfidz.create', ['student_id' => $student->id]));
    $createResponse->assertStatus(200);
    $createResponse->assertSee('Target Kurikulum Kelas X (25 Surat)', false);
    $createResponse->assertSee('Al-Balad', false);
    $createResponse->assertSee('An-Nas', false);
    // Tidak ada tombol filter untuk kelas lain pada Wali Kelas
    $createResponse->assertDontSee('id="btn-filter-XI"', false);
    $createResponse->assertDontSee('id="btn-filter-XII"', false);
});

test('akun wali kelas kelas XI only displays surahs appropriate for grade XI', function () {
    $classXI = SchoolClass::updateOrCreate(['name' => 'XI-MIPA-1'], ['grade' => 'XI', 'level' => '11']);
    $waliUserXI = User::factory()->create(['role' => 'wali_kelas']);
    $teacherXI = Teacher::create([
        'user_id' => $waliUserXI->id,
        'name' => 'Ustadz Wali XI',
        'nip' => '198601012010011002',
    ]);
    $classXI->update(['homeroom_teacher_id' => $teacherXI->id]);

    $response = $this->actingAs($waliUserXI)->get(route('tahfidz.index'));
    $response->assertStatus(200);

    $response->assertSee('Setor Hafalan Siswa Kelas XI-MIPA-1', false);
    $response->assertSee('Surat-Surat Target Kurikulum Kelas XI (7 Surat)', false);
    // Surat-surat target kelas XI
    $response->assertSee('Al-Mutaffifin', false);
    $response->assertSee('Al-Fajr', false);
    // Surat kelas X dan XII tidak ada di pill target kurikulum kelas XI
    $response->assertDontSee('Al-Balad (20 ay.)', false);
    $response->assertDontSee('An-Naba\' (40 ay.)', false);
});

test('akun wali kelas kelas XII only displays surahs appropriate for grade XII', function () {
    $classXII = SchoolClass::updateOrCreate(['name' => 'XII-IPS-1'], ['grade' => 'XII', 'level' => '12']);
    $waliUserXII = User::factory()->create(['role' => 'wali_kelas']);
    $teacherXII = Teacher::create([
        'user_id' => $waliUserXII->id,
        'name' => 'Ustadzah Wali XII',
        'nip' => '198701012010012003',
    ]);
    $classXII->update(['homeroom_teacher_id' => $teacherXII->id]);

    $response = $this->actingAs($waliUserXII)->get(route('tahfidz.index'));
    $response->assertStatus(200);

    $response->assertSee('Setor Hafalan Siswa Kelas XII-IPS-1', false);
    $response->assertSee('Surat-Surat Target Kurikulum Kelas XII (5 Surat)', false);
    // Surat-surat target kelas XII
    $response->assertSee('An-Naba&#039;', false);
    $response->assertSee('Al-Infitar', false);
    // Surat kelas X dan XI tidak ada di pill target kurikulum kelas XII
    $response->assertDontSee('Al-Balad (20 ay.)', false);
    $response->assertDontSee('Al-Mutaffifin (36 ay.)', false);
});

test('akun orang tua tahfidz qur an juz 30 only displays surahs appropriate for their child and hides global 37 surahs recap', function () {
    $classX = SchoolClass::updateOrCreate(['name' => 'X-1'], ['grade' => 'X', 'level' => '10']);
    $student = Student::create([
        'nis' => '10099',
        'nisn' => '0099887766',
        'name' => 'Ahmad Fathanah',
        'class_id' => $classX->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $parentUser = User::factory()->create(['role' => 'orang_tua']);
    ParentGuardian::create([
        'user_id' => $parentUser->id,
        'student_id' => $student->id,
        'relationship' => 'ayah',
        'name' => 'Bapak Fathanah',
        'phone' => '081234567899',
    ]);

    // Parent accesses student tahfidz page
    $response = $this->actingAs($parentUser)->get(route('tahfidz.student', $student->id));
    $response->assertStatus(200);

    // Keterangan yang tampil adalah surat yang sesuai dengan kelas anaknya (Kelas X)
    $response->assertSee('Capaian Hafalan Surat Kelas X', false);
    $response->assertSee('Capaian Hafalan Surat Sesuai Kelas X (25 Surat)', false);
    $response->assertSee('Al-Balad', false);
    $response->assertSee('An-Nas', false);

    // Accordion Rekapitulasi Global Seluruh 37 Surat Juz 30 disembunyikan untuk akun orang tua
    $response->assertDontSee('Rekapitulasi Global Seluruh 37 Surat Juz 30', false);
    $response->assertDontSee('Akumulasi Lintas Jenjang', false);
});

test('cetak 1 dokumen terpadu includes dzuhur dhuha keputrian and tahfidz, and hides keputrian for male students', function () {
    $class = SchoolClass::updateOrCreate(['name' => 'X-TEST'], ['grade' => 'X', 'level' => '10']);

    // Siswa Laki-laki
    $maleStudent = Student::create([
        'nis' => '2001',
        'nisn' => '0020010001',
        'name' => 'Budi Putra Santoso',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    // Siswi Perempuan
    $femaleStudent = Student::create([
        'nis' => '2002',
        'nisn' => '0020010002',
        'name' => 'Siti Putri Rahayu',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    $adminUser = User::where('role', 'super_admin')->first() ?? User::factory()->create(['role' => 'super_admin']);
    $today = Carbon::today()->toDateString();

    // Catat presensi ibadah untuk Budi (L)
    WorshipAttendance::create([
        'student_id' => $maleStudent->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DZUHUR_JUMAT,
        'date' => $today,
        'time' => '12:15',
        'status' => 'HADIR',
        'method' => 'MANUAL',
        'recorded_by' => $adminUser->id,
    ]);
    WorshipAttendance::create([
        'student_id' => $maleStudent->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
        'date' => $today,
        'time' => '07:05',
        'status' => 'HADIR',
        'method' => 'MANUAL',
        'recorded_by' => $adminUser->id,
    ]);

    // Catat presensi ibadah untuk Siti (P) termasuk keputrian
    WorshipAttendance::create([
        'student_id' => $femaleStudent->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DZUHUR_JUMAT,
        'date' => $today,
        'time' => '12:15',
        'status' => 'HADIR',
        'method' => 'MANUAL',
        'recorded_by' => $adminUser->id,
    ]);
    WorshipAttendance::create([
        'student_id' => $femaleStudent->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
        'date' => $today,
        'time' => '07:05',
        'status' => 'HADIR',
        'method' => 'MANUAL',
        'recorded_by' => $adminUser->id,
    ]);
    WorshipAttendance::create([
        'student_id' => $femaleStudent->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_KEPUTRIAN,
        'date' => $today,
        'time' => '11:45',
        'status' => 'HADIR',
        'method' => 'MANUAL',
        'recorded_by' => $adminUser->id,
    ]);

    // Catat setoran hafalan Al-Qur'an (Surat Al-Balad No. 90 untuk Kelas X)
    QuranTahfidzRecord::create([
        'student_id' => $maleStudent->id,
        'class_id' => $class->id,
        'surah_number' => 90,
        'surah_name' => 'Al-Balad',
        'ayat_range' => 'Ayat 1 s.d. 20',
        'ayat_count' => 20,
        'makhraj' => 'Baik',
        'tajwid' => 'Baik',
        'kelancaran' => 'Baik',
        'overall_grade' => 'Lulus',
        'deposit_date' => $today,
        'deposit_time' => '07:30',
        'examiner_name' => 'Ustadz Penguji',
    ]);

    // Akses Cetak 1 Dokumen Terpadu
    $response = $this->actingAs($adminUser)->get(route('reports.print.combined', [
        'class_id' => $class->id,
        'start_date' => $today,
        'end_date' => $today,
    ]));

    $response->assertStatus(200);

    // 1. Header Kolom Section IV PERIBADATAN & TAHFIDZ AL-QUR'AN ada
    $response->assertSee('IV. PERIBADATAN & TAHFIDZ', false);
    $response->assertSee('Dzuhur', false);
    $response->assertSee('Dhuha', false);
    $response->assertSee('Keputrian', false);
    $response->assertSee('Tahfidz', false);

    // 2. Data Siswa Laki-laki: Budi Putra Santoso
    $response->assertSee('Budi Putra Santoso', false);
    // Presensi keputrian untuk laki-laki TIDAK DITAMPILKAN (berupa tanda strip '-')
    $response->assertSee('title="Tidak berlaku untuk siswa laki-laki">-</span>', false);

    // 3. Data Siswi Perempuan: Siti Putri Rahayu
    $response->assertSee('Siti Putri Rahayu', false);
    // Presensi keputrian untuk perempuan tampil (1)
    $response->assertSee('<span class="font-bold">1</span>', false);

    // 4. Setoran Hafalan tercantum rasio surat tuntas kurikulum Kelas X (1/25)
    $response->assertSee('1/25', false);
});

test('role guru does not see worship, tahfidz, attendance, and character menus in sidebar', function () {
    $guru = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
        'name' => 'Guru Mata Pelajaran Test',
    ]);

    $wali = User::factory()->create([
        'role' => 'wali_kelas',
        'roles' => ['wali_kelas', 'guru'],
        'name' => 'Wali Kelas XI Test',
    ]);

    // Akun role guru: menu Presensi Terpadu, Karakter 7 Kebiasaan, dan Ibadah & Tahfidz DIIHILANGKAN
    $resGuru = $this->actingAs($guru)->get(route('dashboard'));
    $resGuru->assertStatus(200);
    $resGuru->assertDontSee('Presensi Terpadu');
    $resGuru->assertDontSee('Karakter (7 Kebiasaan)');
    $resGuru->assertDontSee('Ibadah & Tahfidz', false);
    $resGuru->assertDontSee('Shalat Dhuha Bersama');
    $resGuru->assertDontSee('Kontrol Tahfidz Juz 30');
    // Namun tetap memiliki menu Tata Tertib Siswa dan Raport Digital
    $resGuru->assertSee('Tata Tertib Siswa');
    $resGuru->assertSee('Raport Digital (AI)');

    // Akun role wali kelas: menu Presensi Utama dan Presensi Shalat Dhuha TETAP TAMPIL
    $resWali = $this->actingAs($wali)->get(route('dashboard'));
    $resWali->assertStatus(200);
    $resWali->assertSee('Presensi Utama');
    $resWali->assertSee('Presensi Shalat Dhuha');
});

test('wali kelas is strictly restricted to viewing and inputting students from their own class in worship attendance', function () {
    $classA = SchoolClass::firstOrCreate(['name' => 'X-TEST-A'], ['grade' => 'X']);
    $classB = SchoolClass::firstOrCreate(['name' => 'X-TEST-B'], ['grade' => 'X']);

    $userWaliA = User::factory()->create(['role' => 'wali_kelas', 'name' => 'Wali Kelas X-TEST-A']);
    $teacherA = Teacher::firstOrCreate(['user_id' => $userWaliA->id], [
        'name' => 'Wali Kelas X-TEST-A',
        'nip' => '198501012010011005',
    ]);
    $classA->update(['homeroom_teacher_id' => $teacherA->id]);

    $studentA = Student::firstOrCreate(
        ['nisn' => '99990001'],
        ['nis' => '10001', 'name' => 'Siswa Kelas A', 'class_id' => $classA->id, 'gender' => 'L', 'is_active' => true]
    );
    $studentB = Student::firstOrCreate(
        ['nisn' => '99990002'],
        ['nis' => '10002', 'name' => 'Siswa Kelas B', 'class_id' => $classB->id, 'gender' => 'L', 'is_active' => true]
    );

    // 1. Wali Kelas A di halaman index worship hanya melihat siswa kelasnya
    $responseIndex = $this->actingAs($userWaliA)->get(route('worship.index', ['activity' => 'dhuha']));
    $responseIndex->assertStatus(200);
    $responseIndex->assertSee('Siswa Kelas A');
    $responseIndex->assertDontSee('Siswa Kelas B');

    // 2. Wali Kelas A mencoba scan siswa Kelas B -> ditolak dengan CLASS_RESTRICTION (403)
    Carbon::setTestNow(Carbon::parse('2026-10-02 07:00:00', 'Asia/Jakarta'));
    $responseScan = $this->actingAs($userWaliA)->postJson(route('worship.scan', ['activity' => 'dhuha']), [
        'identifier' => $studentB->nisn,
        'method' => 'RFID',
    ]);
    Carbon::setTestNow();

    $responseScan->assertStatus(403);
    $responseScan->assertJson([
        'success' => false,
        'error_code' => 'CLASS_RESTRICTION',
    ]);

    // 3. Wali Kelas A di halaman report worship terkunci ke kelas A
    $responseReport = $this->actingAs($userWaliA)->get(route('worship.report', ['activity' => 'dhuha']));
    $responseReport->assertStatus(200);
    $responseReport->assertSee('X-TEST-A');
});

test('wali kelas is strictly restricted to their own class students in tahfidz', function () {
    $classA = SchoolClass::firstOrCreate(['name' => 'X-TAHFIDZ-A'], ['grade' => 'X']);
    $classB = SchoolClass::firstOrCreate(['name' => 'X-TAHFIDZ-B'], ['grade' => 'X']);

    $userWaliA = User::factory()->create(['role' => 'wali_kelas', 'name' => 'Wali Tahfidz A']);
    $teacherA = Teacher::firstOrCreate(['user_id' => $userWaliA->id], [
        'name' => 'Wali Tahfidz A',
        'nip' => '198702022011011002',
    ]);
    $classA->update(['homeroom_teacher_id' => $teacherA->id]);

    $studentA = Student::firstOrCreate(
        ['nisn' => '99990003'],
        ['nis' => '10003', 'name' => 'Ananda Kelas A', 'class_id' => $classA->id, 'gender' => 'L', 'is_active' => true]
    );
    $studentB = Student::firstOrCreate(
        ['nisn' => '99990004'],
        ['nis' => '10004', 'name' => 'Ananda Kelas B', 'class_id' => $classB->id, 'gender' => 'L', 'is_active' => true]
    );

    // 1. Pada form create tahfidz, dropdown hanya menampilkan siswa kelas binaannya
    $resCreate = $this->actingAs($userWaliA)->get(route('tahfidz.create'));
    $resCreate->assertStatus(200);
    $resCreate->assertSee('Ananda Kelas A');
    $resCreate->assertDontSee('Ananda Kelas B');

    // 2. Wali Kelas A mencoba simpan setoran siswa Kelas B -> ditolak dengan error flash
    $resStore = $this->actingAs($userWaliA)->post(route('tahfidz.store'), [
        'student_id' => $studentB->id,
        'surah_number' => 78,
        'ayat_range' => 'Ayat 1 s.d. 10',
        'makhraj' => 'Baik',
        'tajwid' => 'Baik',
        'kelancaran' => 'Baik',
        'overall_grade' => 'Lulus',
        'deposit_date' => Carbon::today()->toDateString(),
        'deposit_time' => '07:30',
        'examiner_name' => $userWaliA->name,
    ]);

    $resStore->assertSessionHas('error');
    expect(QuranTahfidzRecord::where('student_id', $studentB->id)->exists())->toBeFalse();

    // 3. Wali Kelas A mencoba akses halaman detail/kartu siswa Kelas B -> 403 Forbidden
    $this->actingAs($userWaliA)->get(route('tahfidz.student', $studentB->id))
        ->assertStatus(403);

    // 4. Wali Kelas A mencoba akses print kartu siswa Kelas B -> 403 Forbidden
    $this->actingAs($userWaliA)->get(route('tahfidz.print_student', $studentB->id))
        ->assertStatus(403);
});

test('worship and tahfidz print documents use headmaster data from master identity and role in signature', function () {
    SchoolSetting::set('headmaster_name', 'Dr. H. Sukardi Master Data, M.Pd.');
    SchoolSetting::set('headmaster_nip', '197001011995011001');

    $class = SchoolClass::firstOrCreate(['name' => 'XI-MIPA-1'], ['grade' => 'XI']);
    $userWali = User::factory()->create(['role' => 'wali_kelas', 'name' => 'Dra. Hj. Nurjanah, M.Pd.']);
    $teacher = Teacher::firstOrCreate(['user_id' => $userWali->id], [
        'name' => 'Dra. Hj. Nurjanah, M.Pd.',
        'nip' => '198103042008012009',
    ]);
    $class->update(['homeroom_teacher_id' => $teacher->id]);

    $student = Student::firstOrCreate(
        ['nisn' => '99990005'],
        ['nis' => '10005', 'name' => 'Siswa Print Test', 'class_id' => $class->id, 'gender' => 'L', 'is_active' => true]
    );

    // 1. Cetak Laporan Worship (Shalat Dhuha)
    $resWorshipPrint = $this->actingAs($userWali)->get(route('worship.print', [
        'activity' => 'dhuha',
        'class_id' => $class->id,
    ]));

    $resWorshipPrint->assertStatus(200);
    // Nama Kepala Sekolah dari Master Data
    $resWorshipPrint->assertSee('Dr. H. Sukardi Master Data, M.Pd.');
    $resWorshipPrint->assertSee('197001011995011001');
    // Keterangan role akun di bawah tanggal cetak
    $resWorshipPrint->assertSee('Wali Kelas XI-MIPA-1');
    $resWorshipPrint->assertSee('Dra. Hj. Nurjanah, M.Pd.');

    // 2. Cetak Laporan Rekap Tahfidz
    $resTahfidzReportPrint = $this->actingAs($userWali)->get(route('tahfidz.print_report', [
        'class_id' => $class->id,
    ]));

    $resTahfidzReportPrint->assertStatus(200);
    $resTahfidzReportPrint->assertSee('Dr. H. Sukardi Master Data, M.Pd.');
    $resTahfidzReportPrint->assertSee('197001011995011001');
    $resTahfidzReportPrint->assertSee('Wali Kelas XI-MIPA-1');

    // 3. Cetak Kartu Tahfidz Siswa
    $resTahfidzStudentPrint = $this->actingAs($userWali)->get(route('tahfidz.print_student', $student->id));
    $resTahfidzStudentPrint->assertStatus(200);
    $resTahfidzStudentPrint->assertSee('Dr. H. Sukardi Master Data, M.Pd.');
    $resTahfidzStudentPrint->assertSee('197001011995011001');
    $resTahfidzStudentPrint->assertSee('Wali Kelas XI-MIPA-1');
});

test('bk coordinator name and nip are dynamically resolved from teacher data and role bk account', function () {
    // 1. Ambil atau buat guru dengan akun role bk
    $userBk = User::where(function ($q) {
        $q->where('role', 'bk')->orWhereJsonContains('roles', 'bk');
    })->first() ?? User::factory()->create([
        'role' => 'bk',
        'roles' => ['bk', 'guru'],
    ]);

    $userBk->update([
        'name' => 'Fulanah Al-Konseli, M.Pd.',
        'username' => '198811122015022001',
        'role' => 'bk',
        'roles' => ['bk', 'guru'],
    ]);

    $teacherBk = Teacher::updateOrCreate(['user_id' => $userBk->id], [
        'name' => 'Fulanah Al-Konseli, M.Pd.',
        'nip' => '198811122015022001',
        'gender' => 'P',
        'title' => 'Koordinator BK (Bimbingan Konseling)',
        'is_active' => true,
    ]);

    $resolved = Teacher::getBkCoordinator();
    expect($resolved['name'])->toBe('Fulanah Al-Konseli, M.Pd.')
        ->and($resolved['nip'])->toBe('198811122015022001');

    // Cek juga lewat User helper
    expect(User::getBkCoordinator()['name'])->toBe('Fulanah Al-Konseli, M.Pd.');

    // 2. Pada lembar Cetak 1 Dokumen Terpadu dan Tata Tertib, penandatangan resmi adalah Kepala Sekolah dan Wakasek Bid. Kesiswaan
    $admin = User::factory()->create(['role' => 'admin']);
    $resCombined = $this->actingAs($admin)->get(route('reports.print.combined'));
    $resCombined->assertStatus(200);
    $resCombined->assertSee('Kepala Sekolah');
    $resCombined->assertSee('Wakasek Bid. Kesiswaan');

    $resDiscipline = $this->actingAs($admin)->get(route('reports.print.discipline'));
    $resDiscipline->assertStatus(200);
    $resDiscipline->assertSee('Kepala Sekolah');
    $resDiscipline->assertSee('Wakasek Bid. Kesiswaan');
});

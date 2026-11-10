<?php

use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\WorshipAttendance;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-31 09:00:00', 'Asia/Jakarta'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('extracurricular report page has official print modal with 4 period types and target selectors', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $teacher = Teacher::create([
        'user_id' => $admin->id,
        'nip' => '198599992010011001',
        'name' => 'Pembina Pramuka',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $extra = Extracurricular::create([
        'name' => 'Pramuka Uji Coba',
        'code' => 'PRAM-TEST',
        'category' => 'Wajib',
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    $class = SchoolClass::create(['name' => 'XII-MIPA-1', 'grade' => 12]);

    $response = $this->actingAs($admin)->get(route('extracurricular.report', ['extra_id' => $extra->id]));

    $response->assertOk();
    $response->assertSee('openPrintExtracurricularModal()');
    $response->assertSee('id="printExtracurricularModal"', false);
    $response->assertSee('Cetak Laporan Presensi Resmi');
    $response->assertSee('value="daily"', false);
    $response->assertSee('value="weekly"', false);
    $response->assertSee('value="monthly"', false);
    $response->assertSee('value="custom"', false);
    $response->assertSee('Buka Dokumen Cetak (A4)');
});

test('extracurricular print report works for daily, weekly, monthly, and custom periods with Kepala Sekolah on the left', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $teacher = Teacher::create([
        'user_id' => $admin->id,
        'nip' => '198599992010011002',
        'name' => 'Pembina Paskibra',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $extra = Extracurricular::create([
        'name' => 'Paskibra Uji Coba',
        'code' => 'PAS-TEST',
        'category' => 'Pilihan',
        'teacher_id' => $teacher->id,
        'is_active' => true,
    ]);

    $class = SchoolClass::create(['name' => 'XI-IPS-1', 'grade' => 11]);
    $student = Student::create([
        'nis' => '998877',
        'nisn' => '0099887766',
        'name' => 'Ahmad Fulan',
        'gender' => 'L',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    $extra->students()->attach($student->id, ['role' => 'Anggota']);

    ExtracurricularAttendance::create([
        'extracurricular_id' => $extra->id,
        'student_id' => $student->id,
        'class_id' => $class->id,
        'date' => '2026-10-31',
        'time' => '15:30:00',
        'status' => 'HADIR',
        'method' => 'RFID',
    ]);

    // 1. Daily
    $resDaily = $this->actingAs($admin)->get(route('extracurricular.print', [
        'extra_id' => $extra->id,
        'period_type' => 'daily',
        'date' => '2026-10-31',
        'class_id' => $class->id,
    ]));
    $resDaily->assertOk();
    $resDaily->assertSee('Laporan Harian');
    $resDaily->assertSee('Mengetahui,<br>', false);
    $resDaily->assertSee('Kepala Sekolah');
    $resDaily->assertSee('Ahmad Fulan');

    // 2. Weekly
    $resWeekly = $this->actingAs($admin)->get(route('extracurricular.print', [
        'extra_id' => $extra->id,
        'period_type' => 'weekly',
    ]));
    $resWeekly->assertOk();
    $resWeekly->assertSee('Laporan Mingguan');

    // 3. Monthly
    $resMonthly = $this->actingAs($admin)->get(route('extracurricular.print', [
        'extra_id' => $extra->id,
        'period_type' => 'monthly',
        'month' => 10,
        'year' => 2026,
    ]));
    $resMonthly->assertOk();
    $resMonthly->assertSee('Laporan Bulanan');

    // 4. Custom
    $resCustom = $this->actingAs($admin)->get(route('extracurricular.print', [
        'extra_id' => $extra->id,
        'period_type' => 'custom',
        'start_date' => '2026-10-01',
        'end_date' => '2026-10-31',
    ]));
    $resCustom->assertOk();
    $resCustom->assertSee('Periode:');

    // Verify Kepala Sekolah position: appears before Pembina in the table
    $content = $resDaily->getContent();
    $posKepsek = strpos($content, 'Kepala Sekolah');
    $posPembina = strpos($content, 'Pembina Ekstrakurikuler');
    expect($posKepsek)->toBeLessThan($posPembina);
});

test('worship report pages for dhuha, dzuhur_jumat, and keputrian have official print modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    SchoolClass::create(['name' => 'X-A', 'grade' => 10]);

    foreach (['dhuha', 'dzuhur_jumat', 'keputrian'] as $act) {
        $response = $this->actingAs($admin)->get(route('worship.report', ['activity' => $act]));
        $response->assertOk();
        $response->assertSee('openPrintWorshipModal()');
        $response->assertSee('id="printWorshipModal"', false);
        $response->assertSee('value="daily"', false);
        $response->assertSee('value="weekly"', false);
        $response->assertSee('value="monthly"', false);
        $response->assertSee('value="custom"', false);
        $response->assertSee('Buka Dokumen Cetak (A4)');
    }
});

test('worship print report works for dhuha, dzuhur_jumat, and keputrian with Kepala Sekolah on the left', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::create(['name' => 'X-B', 'grade' => 10]);
    $student = Student::create([
        'nis' => '998878',
        'nisn' => '0099887767',
        'name' => 'Fatimah Az-Zahra',
        'gender' => 'P',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    WorshipAttendance::create([
        'activity_type' => WorshipAttendance::ACTIVITY_KEPUTRIAN,
        'student_id' => $student->id,
        'class_id' => $class->id,
        'date' => '2026-10-31',
        'time' => '12:00:00',
        'status' => 'HADIR',
        'method' => 'QR',
    ]);

    // Monthly print for keputrian
    $resMonthly = $this->actingAs($admin)->get(route('worship.print', [
        'activity' => 'keputrian',
        'period_type' => 'monthly',
        'month' => 10,
        'year' => 2026,
        'class_id' => $class->id,
    ]));

    $resMonthly->assertOk();
    $resMonthly->assertSee('Laporan Bulanan');
    $resMonthly->assertSee('Fatimah Az-Zahra');
    $resMonthly->assertSee('Kepala Sekolah');

    // Verify Kepala Sekolah is in the first cell of signature table (on the LEFT)
    $content = $resMonthly->getContent();
    $posKepsek = strpos($content, 'Kepala Sekolah');
    $posSigner = strpos($content, 'Koordinator Keagamaan');
    expect($posKepsek)->toBeLessThan($posSigner);

    // Daily print for dhuha
    $resDhuha = $this->actingAs($admin)->get(route('worship.print', [
        'activity' => 'dhuha',
        'period_type' => 'daily',
        'date' => '2026-10-31',
    ]));
    $resDhuha->assertOk();
    $resDhuha->assertSee('Laporan Harian');

    // Weekly print for dzuhur_jumat
    $resDzuhur = $this->actingAs($admin)->get(route('worship.print', [
        'activity' => 'dzuhur_jumat',
        'period_type' => 'weekly',
    ]));
    $resDzuhur->assertOk();
    $resDzuhur->assertSee('Laporan Mingguan');
});

test('role kepala_administrasi is recognized, has administrative access, and appears in reports and management', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $userKA = User::create([
        'name' => 'Bapak Kepala Administrasi',
        'username' => '197508152005011999',
        'email' => 'ka.tu99@sman1lengkong.sch.id',
        'password' => bcrypt('password123'),
        'role' => 'kepala_administrasi',
        'roles' => ['kepala_administrasi'],
        'is_active' => true,
    ]);

    $teacher = Teacher::create([
        'user_id' => $userKA->id,
        'nip' => '197508152005011999',
        'name' => 'Bapak Kepala Administrasi',
        'gender' => 'L',
        'title' => 'Kepala Administrasi (Kepala TU)',
        'is_active' => true,
    ]);

    expect($userKA->hasRole('kepala_administrasi'))->toBeTrue()
        ->and($userKA->isKepalaAdministrasi())->toBeTrue()
        ->and($userKA->isTenagaAdministrasi())->toBeTrue()
        ->and($userKA->isTeacher())->toBeTrue();

    // Can access dashboard
    $dashRes = $this->actingAs($userKA)->get(route('dashboard'));
    $dashRes->assertOk();

    // Activity report includes kepala_administrasi under administrasi category
    $reportRes = $this->actingAs($admin)->get(route('reports.user_activity', ['role' => 'administrasi']));
    $reportRes->assertOk();
    $reportRes->assertSee('Bapak Kepala Administrasi');

    // Master data teachers page shows kepala_administrasi options
    $teachersPage = $this->actingAs($admin)->get(route('master.teachers'));
    $teachersPage->assertOk();
    $teachersPage->assertSee('value="kepala_administrasi"', false);
    $teachersPage->assertSee('Kepala Administrasi (Kepala TU)');
});

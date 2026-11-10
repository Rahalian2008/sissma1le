<?php

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;

test('parent portal page is accessible without login', function () {
    $response = $this->get(route('parent.portal'));

    $response->assertOk();
    $response->assertSee('Portal Orang Tua');
    $response->assertSee('Tanpa Login');
});

test('parent can query student data using valid nisn without login', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-A',
        'level' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::first();
    if (! $student) {
        $student = Student::create([
            'name' => 'Siswa Test Ortu',
            'nis' => '998877',
            'nisn' => '0089998877',
            'gender' => 'L',
            'class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
            'is_active' => true,
        ]);
    }

    $response = $this->get(route('parent.portal', ['nisn' => $student->nisn]));

    $response->assertOk();
    $response->assertSee($student->name);
    $response->assertSee($student->nisn);
    $response->assertSee('Presensi Hari Ini');
    $response->assertDontSee("Rekam Jejak Ibadah Berjamaah & Tahfidz Al-Qur'an", false);
    $response->assertDontSee('worship_tahfidz', false);
});

test('parent portal shows not found message for invalid nisn', function () {
    $response = $this->get(route('parent.portal', ['nisn' => '999999999999999']));

    $response->assertOk();
    $response->assertSee('Data Siswa Tidak Ditemukan');
});

test('login page contains direct link to parent portal', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee(route('parent.portal'));
    $response->assertSee('Portal Khusus Orang Tua / Wali');
});

test('parent portal shows attendance and worship tahfidz recap when queried with valid nisn', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-A',
        'level' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::first() ?? Student::create([
        'name' => 'Siswa Test Ortu',
        'nis' => '998877',
        'nisn' => '0089998877',
        'gender' => 'L',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $response = $this->get(route('parent.portal', ['nisn' => $student->nisn]));

    $response->assertOk();
    $response->assertSee('Rekapitulasi Presensi Kehadiran Anak', false);
    $response->assertSee('Pilihan Periode:', false);
    $response->assertSee('Shalat Dhuha', false);
    $response->assertSee("Dzuhur / Jum'at", false);
    $response->assertSee('Keputrian', false);
    $response->assertSee('Tahfidz Juz 30', false);
    $response->assertSee('name="nisn" value="'.$student->nisn.'"', false);
});

test('parent portal allows filtering recap period with preserved nisn', function () {
    $student = Student::first();
    $testDate = '2026-10-02';

    $response = $this->get(route('parent.portal', [
        'nisn' => $student->nisn,
        'att_period' => 'daily',
        'att_date' => $testDate,
    ]));

    $response->assertOk();
    $response->assertSee('Harian:', false);
    $response->assertSee('name="nisn" value="'.$student->nisn.'"', false);
});

test('parent portal tab 2 displays weekly, monthly, and costume custom periods with 4 stat cards and summary row without login', function () {
    $student = Student::first();

    // Seed attendance records for testing 4 stat cards
    Attendance::where('student_id', $student->id)->delete();
    Attendance::create([
        'student_id' => $student->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'class_id' => $student->class_id,
        'date' => '2026-10-05',
        'time' => '06:45:00',
        'status' => 'HADIR',
        'method' => 'QR_CODE',
    ]);
    Attendance::create([
        'student_id' => $student->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'class_id' => $student->class_id,
        'date' => '2026-10-06',
        'time' => '07:15:00',
        'status' => 'TERLAMBAT',
        'method' => 'QR_CODE',
    ]);
    Attendance::create([
        'student_id' => $student->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'class_id' => $student->class_id,
        'date' => '2026-10-07',
        'status' => 'IZIN',
        'method' => 'MANUAL',
        'notes' => 'Izin keperluan keluarga',
    ]);
    Attendance::create([
        'student_id' => $student->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'class_id' => $student->class_id,
        'date' => '2026-10-08',
        'status' => 'SAKIT',
        'method' => 'MANUAL',
        'notes' => 'Sakit demam',
    ]);
    Attendance::create([
        'student_id' => $student->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'class_id' => $student->class_id,
        'date' => '2026-10-09',
        'status' => 'ALPA',
        'method' => 'SISTEM',
    ]);

    // 1. Test Custom / Costume Range
    $responseCustom = $this->get(route('parent.portal', [
        'nisn' => $student->nisn,
        'tab' => 'attendance',
        'att_period' => 'custom',
        'att_start_date' => '2026-10-05',
        'att_end_date' => '2026-10-09',
    ]));

    $responseCustom->assertOk();
    // Tab 2 pane is rendered and visible (not hidden)
    $responseCustom->assertSee('id="tab-pane-attendance" class="portal-tab-pane  mt-6 space-y-6"', false);
    $responseCustom->assertDontSee('[x-cloak]', false);
    $responseCustom->assertDontSee('x-cloak', false);

    // Period options
    $responseCustom->assertSee('Mingguan', false);
    $responseCustom->assertSee('Bulanan', false);
    $responseCustom->assertSee('Rentang Kustom', false);

    // 4 Stat Cards
    $responseCustom->assertSee('HADIR', false);
    $responseCustom->assertSee('Total hari ananda hadir', false);
    $responseCustom->assertSee('Tepat waktu:', false);
    $responseCustom->assertSee('Terlambat:', false);

    $responseCustom->assertSee('ALPA', false);
    $responseCustom->assertSee('Jumlah hari tanpa keterangan / tidak hadir tanpa konfirmasi', false);

    $responseCustom->assertSee('IZIN', false);
    $responseCustom->assertSee('Jumlah hari izin terkonfirmasi surat/pemberitahuan orang tua', false);

    $responseCustom->assertSee('SAKIT', false);
    $responseCustom->assertSee('Jumlah hari izin sakit tercatat surat keterangan/dokter', false);

    // Summary Row
    $responseCustom->assertSee('Persentase Tingkat Kehadiran', false);
    $responseCustom->assertSee('Total Hari Efektif Sekolah', false);
    $responseCustom->assertSee('5 Hari', false); // 5 attendances created

    // Quick Action button in hero
    $responseCustom->assertSee("switchPortalTab('attendance')", false);

    // 2. Test Weekly Period
    $responseWeekly = $this->get(route('parent.portal', [
        'nisn' => $student->nisn,
        'tab' => 'attendance',
        'att_period' => 'weekly',
        'att_week_date' => '2026-10-05',
    ]));
    $responseWeekly->assertOk();
    $responseWeekly->assertSee('Mingguan:', false);

    // 3. Test Monthly Period
    $responseMonthly = $this->get(route('parent.portal', [
        'nisn' => $student->nisn,
        'tab' => 'attendance',
        'att_period' => 'monthly',
        'att_month' => '10',
        'att_year' => '2026',
    ]));
    $responseMonthly->assertOk();
    $responseMonthly->assertSee('Bulanan: Oktober 2026', false);
});

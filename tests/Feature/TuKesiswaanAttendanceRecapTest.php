<?php

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    AcademicYear::firstOrCreate(
        ['name' => '2026/2027-Ganjil'],
        [
            'semester' => 'Ganjil',
            'start_date' => Carbon::now('Asia/Jakarta')->subMonths(2)->toDateString(),
            'end_date' => Carbon::now('Asia/Jakarta')->addMonths(4)->toDateString(),
            'is_active' => true,
        ]
    );

    SchoolSetting::set('academic_year', '2026/2027');
    SchoolSetting::set('semester', 'Ganjil');
});

test('staf kesiswaan can view attendance recap index for all rombels without individual names', function () {
    $tuUser = User::factory()->create([
        'role' => 'ta_kesiswaan',
        'roles' => ['ta_kesiswaan'],
    ]);

    $classX = SchoolClass::firstOrCreate(['name' => 'X-RECAP-1'], ['grade' => 'X']);
    $classXI = SchoolClass::firstOrCreate(['name' => 'XI-RECAP-1'], ['grade' => 'XI']);

    $student1 = Student::create([
        'name' => 'Budi Santoso',
        'nis' => '1001',
        'nisn' => '001001',
        'gender' => 'L',
        'class_id' => $classX->id,
        'is_active' => true,
    ]);

    $student2 = Student::create([
        'name' => 'Siti Rahma',
        'nis' => '1002',
        'nisn' => '001002',
        'gender' => 'P',
        'class_id' => $classXI->id,
        'is_active' => true,
    ]);

    // Create attendances
    Attendance::create([
        'student_id' => $student1->id,
        'class_id' => $classX->id,
        'date' => Carbon::today()->toDateString(),
        'time' => '07:05:00',
        'status' => 'HADIR',
    ]);

    Attendance::create([
        'student_id' => $student2->id,
        'class_id' => $classXI->id,
        'date' => Carbon::today()->toDateString(),
        'time' => '07:10:00',
        'status' => 'IZIN',
    ]);

    $response = $this->actingAs($tuUser)->get(route('tu.kesiswaan.rekap_presensi.index', [
        'mode' => 'all_rombels',
        'start_date' => Carbon::today()->toDateString(),
        'end_date' => Carbon::today()->toDateString(),
    ]));

    $response->assertStatus(200);
    $response->assertSee('Rekapitulasi Kehadiran Siswa');
    $response->assertSee('Semua Rombel (Tanpa Nama)');
    $response->assertSee('X-RECAP-1');
    $response->assertSee('XI-RECAP-1');
    $response->assertSee('GRAND TOTAL SELURUH ROMBEL');
});

test('staf kesiswaan can view detailed attendance recap per rombel with student list', function () {
    $tuUser = User::factory()->create([
        'role' => 'ta_kesiswaan',
        'roles' => ['ta_kesiswaan'],
    ]);

    $class = SchoolClass::firstOrCreate(['name' => 'XII-RECAP-SPECIFIC'], ['grade' => 'XII']);

    $student = Student::create([
        'name' => 'Ahmad Fauzi',
        'nis' => '1201',
        'nisn' => '001201',
        'gender' => 'L',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    Attendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'date' => Carbon::today()->toDateString(),
        'time' => '07:00:00',
        'status' => 'HADIR',
    ]);

    $response = $this->actingAs($tuUser)->get(route('tu.kesiswaan.rekap_presensi.index', [
        'mode' => 'per_rombel',
        'class_id' => $class->id,
        'start_date' => Carbon::today()->toDateString(),
        'end_date' => Carbon::today()->toDateString(),
    ]));

    $response->assertStatus(200);
    $response->assertSee('Per Rombel (Detail Siswa)');
    $response->assertSee('Ahmad Fauzi');
    $response->assertSee('1201');
    $response->assertSee('001201');
    $response->assertSee('TOTAL REKAPITULASI KELAS XII-RECAP-SPECIFIC');
});

test('staf kesiswaan can print attendance recap for all rombels and per rombel', function () {
    $tuUser = User::factory()->create([
        'role' => 'ta_kesiswaan',
        'roles' => ['ta_kesiswaan'],
    ]);

    $class = SchoolClass::firstOrCreate(['name' => 'X-PRINT-CLASS'], ['grade' => 'X']);

    // Print all rombels
    $resPrintAll = $this->actingAs($tuUser)->get(route('tu.kesiswaan.rekap_presensi.print', [
        'mode' => 'all_rombels',
        'paper' => 'a4',
        'orientation' => 'landscape',
    ]));

    $resPrintAll->assertStatus(200);
    $resPrintAll->assertSee('LAPORAN REKAPITULASI KEHADIRAN SISWA PER ROMBONGAN BELAJAR');

    // Print per rombel
    $resPrintRombel = $this->actingAs($tuUser)->get(route('tu.kesiswaan.rekap_presensi.print', [
        'mode' => 'per_rombel',
        'class_id' => $class->id,
        'paper' => 'f4',
        'orientation' => 'landscape',
    ]));

    $resPrintRombel->assertStatus(200);
    $resPrintRombel->assertSee('LAPORAN REKAPITULASI KEHADIRAN SISWA KELAS X-PRINT-CLASS');
});

test('unauthorized users without tu kesiswaan permission are forbidden from accessing attendance recap', function () {
    $studentUser = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);

    $response = $this->actingAs($studentUser)->get(route('tu.kesiswaan.rekap_presensi.index'));
    $response->assertStatus(403);
});

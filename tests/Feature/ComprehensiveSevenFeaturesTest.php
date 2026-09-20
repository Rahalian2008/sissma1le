<?php

use App\Models\Achievement;
use App\Models\Habit;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;

beforeEach(function () {
    $this->superAdmin = User::where('role', 'super_admin')->first();
    $this->admin = User::where('role', 'admin')->first();
    $this->waliKelasUser = User::where('role', 'wali_kelas')->first();
    $this->siswaUser = User::where('role', 'siswa')->first();
    $this->orangTuaUser = User::where('role', 'orang_tua')->first();
    $this->counselorUser = User::where('role', 'bk')->first();
});

test('requirement 1: dashboard displays attendance rankings for alpa, izin, and sakit', function () {
    $response = $this->actingAs($this->admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Rangking Ketidakhadiran Siswa (Alpa, Izin, Sakit)');
    $response->assertSee('Sering Alpa (Tanpa Izin)');
    $response->assertSee('Sering Izin Resmi');
    $response->assertSee('Sering Sakit');
});

test('requirement 2: dashboard displays discipline rankings, frequent items, and threshold alerts', function () {
    $response = $this->actingAs($this->admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Rangking Siswa Berprestasi');
    $response->assertSee('Rangking Pelanggaran Tata Tertib');
    $response->assertSee('Prestasi Sering Diraih');
    $response->assertSee('Pelanggaran Sering Terjadi');
    $response->assertSee('Siswa Mendekati / Meraih Penghargaan');
    $response->assertSee('Siswa Mendekati / Mendapatkan Peringatan');
});

test('requirement 3: dashboard displays character 7 kebiasaan diagram and percentages', function () {
    $response = $this->actingAs($this->admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Diagram & Persentase Karakter (7 Kebiasaan Anak Hebat)', false);
    $response->assertSee('KONSISTENSI');
    $response->assertSee('Bangun Pagi');
    $response->assertSee('Beribadah');
    $response->assertSee('Berolahraga');
});

test('requirement 4: character report page and a4 print works with class filtering', function () {
    $response = $this->actingAs($this->admin)->get(route('habits.report'));

    $response->assertOk();
    $response->assertSee('Laporan 7 Kebiasaan Anak Indonesia Hebat');
    $response->assertSee('Rekapitulasi 7 Pilar Karakter Siswa');
    $response->assertSee('Cetak Lembar Resmi (A4)');

    // Verify print habits page
    $printResponse = $this->actingAs($this->admin)->get(route('reports.print.habits'));
    $printResponse->assertOk();
    $printResponse->assertSee('LAPORAN PERKEMBANGAN 7 KEBIASAAN ANAK INDONESIA HEBAT');
});

test('requirement 5: superadmin can configure habit target times while others are unauthorized', function () {
    $habit = Habit::first();
    expect($habit)->not->toBeNull();

    // Super admin updates target time
    $response = $this->actingAs($this->superAdmin)->post(route('habits.target_times.update'), [
        'targets' => [
            [
                'id' => $habit->id,
                'target_time' => 'Pukul 04:30 WIB (Disetting Superadmin)',
            ],
        ],
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('habits', [
        'id' => $habit->id,
        'target_time' => 'Pukul 04:30 WIB (Disetting Superadmin)',
    ]);

    // Non-superadmin receives 403 Forbidden
    $forbiddenResponse = $this->actingAs($this->admin)->post(route('habits.target_times.update'), [
        'targets' => [
            [
                'id' => $habit->id,
                'target_time' => 'Hacker Target',
            ],
        ],
    ]);
    $forbiddenResponse->assertForbidden();
});

test('requirement 5: habit summary supports both weekly and monthly review modes', function () {
    // Weekly mode
    $weeklyResponse = $this->actingAs($this->admin)->get(route('habits.summary', ['mode' => 'weekly']));
    $weeklyResponse->assertOk();
    $weeklyResponse->assertSee('Mode Mingguan');
    $weeklyResponse->assertSee('Mingguan (7 Hari)');

    // Monthly mode
    $monthlyResponse = $this->actingAs($this->admin)->get(route('habits.summary', ['mode' => 'monthly']));
    $monthlyResponse->assertOk();
    $monthlyResponse->assertSee('Mode Bulanan');
    $monthlyResponse->assertSee('Bulanan (30 Hari)');
});

test('requirement 6: report center has unified 1-document print for presensi, tata tertib, and 7 kebiasaan', function () {
    $indexResponse = $this->actingAs($this->admin)->get(route('reports.index'));
    $indexResponse->assertOk();
    $indexResponse->assertSee('Cetak 1 Dokumen (Presensi, Tata Tertib & 7 Kebiasaan)', false);

    $printCombinedResponse = $this->actingAs($this->admin)->get(route('reports.print.combined'));
    $printCombinedResponse->assertOk();
    $printCombinedResponse->assertSee('LAPORAN KOMPREHENSIF PERKEMBANGAN & KARAKTER SISWA', false);
    $printCombinedResponse->assertSee('I. REKAPITULASI PRESENSI', false);
    $printCombinedResponse->assertSee('II. TATA TERTIB SEKOLAH', false);
    $printCombinedResponse->assertSee('III. 7 KEBIASAAN ANAK HEBAT', false);
});

test('requirement 7: wali kelas is locked to homeroom class across feature views', function () {
    $teacher = $this->waliKelasUser->teacher;
    $homeroomClass = $teacher?->homeroomClass;
    expect($homeroomClass)->not->toBeNull();

    // 1. Discipline ranking view
    $rankResponse = $this->actingAs($this->waliKelasUser)->get(route('discipline.ranking'));
    $rankResponse->assertOk();
    $rankResponse->assertSee('Kelas '.$homeroomClass->name);
    $rankResponse->assertDontSee('<option value="">Semua Kelas</option>', false);

    // 2. Discipline report view
    $discReportResponse = $this->actingAs($this->waliKelasUser)->get(route('discipline.report'));
    $discReportResponse->assertOk();
    $discReportResponse->assertSee('Kelas '.$homeroomClass->name);
    $discReportResponse->assertDontSee('<option value="">Semua Kelas</option>', false);

    // 3. Habits report view
    $habitReportResponse = $this->actingAs($this->waliKelasUser)->get(route('habits.report'));
    $habitReportResponse->assertOk();
    $habitReportResponse->assertSee($homeroomClass->name);
    $habitReportResponse->assertDontSee('<option value="">-- Semua Kelas --</option>', false);

    // 4. Attendance report view
    $attReportResponse = $this->actingAs($this->waliKelasUser)->get(route('attendance.report'));
    $attReportResponse->assertOk();
    $attReportResponse->assertSee($homeroomClass->name);
    $attReportResponse->assertDontSee('<option value="">Semua Kelas</option>', false);

    // 5. Achievements index view
    $achResponse = $this->actingAs($this->waliKelasUser)->get(route('achievements.index'));
    $achResponse->assertOk();
    $achResponse->assertSee('Kelas '.$homeroomClass->name);
    $achResponse->assertDontSee('<option value="">-- Semua Kelas --</option>', false);

    // 6. Violations index view
    $vioResponse = $this->actingAs($this->waliKelasUser)->get(route('violations.index'));
    $vioResponse->assertOk();
    $vioResponse->assertSee('Kelas '.$homeroomClass->name);
    $vioResponse->assertDontSee('<option value="">-- Semua Kelas --</option>', false);
});

test('requirement 7: siswa and orang tua do not see class selector dropdowns anywhere', function () {
    // Siswa checks
    $siswaRank = $this->actingAs($this->siswaUser)->get(route('discipline.ranking'));
    $siswaRank->assertOk();
    $siswaRank->assertDontSee('name="class_id"', false);

    $siswaDisc = $this->actingAs($this->siswaUser)->get(route('discipline.report'));
    $siswaDisc->assertOk();
    $siswaDisc->assertDontSee('name="class_id"', false);

    $siswaAch = $this->actingAs($this->siswaUser)->get(route('achievements.index'));
    $siswaAch->assertOk();
    $siswaAch->assertDontSee('name="class_id"', false);

    $siswaVio = $this->actingAs($this->siswaUser)->get(route('violations.index'));
    $siswaVio->assertOk();
    $siswaVio->assertDontSee('name="class_id"', false);

    // Orang Tua checks
    $ortuRank = $this->actingAs($this->orangTuaUser)->get(route('discipline.ranking'));
    $ortuRank->assertOk();
    $ortuRank->assertDontSee('name="class_id"', false);

    $ortuDisc = $this->actingAs($this->orangTuaUser)->get(route('discipline.report'));
    $ortuDisc->assertOk();
    $ortuDisc->assertDontSee('name="class_id"', false);

    $ortuAch = $this->actingAs($this->orangTuaUser)->get(route('achievements.index'));
    $ortuAch->assertOk();
    $ortuAch->assertDontSee('name="class_id"', false);

    $ortuVio = $this->actingAs($this->orangTuaUser)->get(route('violations.index'));
    $ortuVio->assertOk();
    $ortuVio->assertDontSee('name="class_id"', false);
});

test('requirement 7: wali kelas cannot store achievements or violations for other classes', function () {
    $homeroomClass = $this->waliKelasUser->teacher?->homeroomClass;
    $otherClass = SchoolClass::where('id', '!=', $homeroomClass?->id)->first() ?? SchoolClass::create(['name' => 'XII-IPA-9', 'grade_level' => 12]);
    $otherStudent = Student::where('class_id', $otherClass->id)->first();
    if (! $otherStudent) {
        $otherStudent = Student::create([
            'nis' => '999999',
            'nisn' => '0099999999',
            'name' => 'Siswa Kelas Lain',
            'gender' => 'L',
            'class_id' => $otherClass->id,
            'is_active' => true,
        ]);
    }
    expect($otherStudent)->not->toBeNull();

    // Attempt to store achievement for other class student
    $achResponse = $this->actingAs($this->waliKelasUser)->post(route('achievements.store'), [
        'student_id' => $otherStudent->id,
        'category_id' => 1,
        'title' => 'Prestasi Ilegal Lintas Kelas',
        'level' => 'SEKOLAH',
        'points' => 25,
        'date' => now()->toDateString(),
    ]);
    $achResponse->assertForbidden();

    // Attempt to store violation for other class student
    $vioResponse = $this->actingAs($this->waliKelasUser)->post(route('violations.store'), [
        'student_id' => $otherStudent->id,
        'category_id' => 1,
        'title' => 'Pelanggaran Ilegal Lintas Kelas',
        'points' => 10,
        'date' => now()->toDateString(),
    ]);
    $vioResponse->assertForbidden();
});

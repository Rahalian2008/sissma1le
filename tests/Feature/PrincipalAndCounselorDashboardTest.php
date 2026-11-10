<?php

use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 10:00:00', 'Asia/Jakarta'));

    // Create School Class
    $this->schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'XII-MIPA-1'],
        [
            'grade' => 'XII',
            'major' => 'MIPA',
            'is_active' => true,
        ]
    );

    // Create Kepala Sekolah User & Teacher
    $this->kepalaSekolahUser = User::firstOrCreate(
        ['email' => 'kepsek.testing@sma1le.sch.id'],
        [
            'name' => 'Dr. H. Kepala Sekolah, M.Pd.',
            'username' => 'KEPSEK01',
            'password' => bcrypt('password'),
            'role' => 'kepala_sekolah',
            'roles' => ['kepala_sekolah'],
            'is_active' => true,
        ]
    );

    // Create Guru BK User & Teacher
    $this->counselorUser = User::firstOrCreate(
        ['email' => 'bk.testing@sma1le.sch.id'],
        [
            'name' => 'Siti Aminah, S.Pd., Kons.',
            'username' => 'GURUBK01',
            'password' => bcrypt('password'),
            'role' => 'bk',
            'roles' => ['bk', 'guru'],
            'is_active' => true,
        ]
    );

    // Create Sample Students
    $this->student1 = Student::firstOrCreate(
        ['nisn' => '9988776655'],
        [
            'nis' => '12001',
            'name' => 'Ahmad Santoso',
            'gender' => 'L',
            'class_id' => $this->schoolClass->id,
            'is_active' => true,
        ]
    );

    $this->student2 = Student::firstOrCreate(
        ['nisn' => '9988776656'],
        [
            'nis' => '12002',
            'name' => 'Fatimah Az-Zahra',
            'gender' => 'P',
            'class_id' => $this->schoolClass->id,
            'is_active' => true,
        ]
    );

    // Create Sample Extracurricular
    $this->ekskul = Extracurricular::firstOrCreate(
        ['name' => 'Pramuka Wajib'],
        [
            'code' => 'EKS-PRM',
            'day' => 'Jumat',
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'location' => 'Lapangan Utama',
            'is_active' => true,
        ]
    );
});

test('kepala sekolah can view dashboard with 8 controlling pillars and no CSV export', function () {
    $response = $this->actingAs($this->kepalaSekolahUser)
        ->get(route('dashboard'));

    $response->assertStatus(200);

    // Check all 8 pillars present on Principal dashboard
    $response->assertSee('Tata Tertib');
    $response->assertSee('Presensi Utama');
    $response->assertSee('Shalat Dzuhur');
    $response->assertSee('Shalat Dhuha');
    $response->assertSee('Keputrian');
    $response->assertSee('Ekstrakurikuler');
    $response->assertSee('Hafalan Juz 30');
    $response->assertSee('7 KAIH');

    // Check Controlling / Early Warning sections
    $response->assertSee('Siswa Sering Alpa');
    $response->assertSee('Siswa Ambang Batas Pembinaan');
    $response->assertSee('Pusat Kontroling & Pengawasan Eksekutif', false);
    $response->assertSee('Pantauan Kehadiran Berdasarkan Jenjang Kelas');

    // Check Official Document Quick Access
    $response->assertSee('Akses Cepat Dokumen & Rekap Laporan Resmi', false);
    $response->assertSee(route('reports.print.combined'));
    $response->assertSee(route('raport.index'));

    // Check that CSV Export button is removed from Principal Dashboard
    $response->assertDontSee('Ekspor CSV (Semua)');
    $response->assertDontSee('reports.export.csv');
});

test('kepala sekolah can access all attendance modules', function () {
    // 1. Presensi Utama
    $responseUtama = $this->actingAs($this->kepalaSekolahUser)
        ->get(route('attendance.index'));
    $responseUtama->assertStatus(200);

    // 2. Presensi Shalat Dzuhur
    $responseDzuhur = $this->actingAs($this->kepalaSekolahUser)
        ->get(route('worship.index', ['activity' => 'dzuhur']));
    $responseDzuhur->assertStatus(200);

    // 3. Presensi Shalat Dhuha
    $responseDhuha = $this->actingAs($this->kepalaSekolahUser)
        ->get(route('worship.index', ['activity' => 'dhuha']));
    $responseDhuha->assertStatus(200);

    // 4. Presensi Keputrian
    $responseKeputrian = $this->actingAs($this->kepalaSekolahUser)
        ->get(route('worship.index', ['activity' => 'keputrian']));
    $responseKeputrian->assertStatus(200);

    // 5. Presensi Ekstrakurikuler
    $responseEkskul = $this->actingAs($this->kepalaSekolahUser)
        ->get(route('extracurricular.index'));
    $responseEkskul->assertStatus(200);
});

test('guru bk can view counselor dashboard with all 8 pillars and early detection cases', function () {
    $response = $this->actingAs($this->counselorUser)
        ->get(route('dashboard'));

    $response->assertStatus(200);

    // Check all 8 pillars in Counselor dashboard
    $response->assertSee('Tata Tertib');
    $response->assertSee('Presensi Utama');
    $response->assertSee('Shalat Dzuhur');
    $response->assertSee('Shalat Dhuha');
    $response->assertSee('Keputrian');
    $response->assertSee('Ekstrakurikuler');
    $response->assertSee('Tahfidz Juz 30');
    $response->assertSee('7 KAIH');

    // Check Guidance early detection cards
    $response->assertSee('Siswa Sering Alpa');
    $response->assertSee('Siswa Sering Terlambat');
    $response->assertSee('Ambang Batas Tindakan Pembinaan');
    $response->assertSee('Daftar Siswa Mencapai Ambang Pembinaan');
});

test('guru bk can access attendance, guidance, discipline reports and habit charts', function () {
    // 1. Presensi Utama
    $this->actingAs($this->counselorUser)
        ->get(route('attendance.index'))
        ->assertStatus(200);

    // 2. Worship (Dzuhur, Dhuha, Keputrian)
    $this->actingAs($this->counselorUser)
        ->get(route('worship.index', ['activity' => 'dzuhur']))
        ->assertStatus(200);

    $this->actingAs($this->counselorUser)
        ->get(route('worship.index', ['activity' => 'keputrian']))
        ->assertStatus(200);

    // 3. Catatan Pembinaan
    $this->actingAs($this->counselorUser)
        ->get(route('guidance.index'))
        ->assertStatus(200);

    // 4. Laporan Tata Tertib
    $this->actingAs($this->counselorUser)
        ->get(route('discipline.report'))
        ->assertStatus(200);

    // 5. Grafik 7 KAIH
    $this->actingAs($this->counselorUser)
        ->get(route('habits.charts'))
        ->assertStatus(200);

    // 6. Laporan 7 KAIH
    $this->actingAs($this->counselorUser)
        ->get(route('habits.report'))
        ->assertStatus(200);
});

test('combined report includes extracurricular columns alongside all 7 other agendas', function () {
    // Attach student to extracurricular
    $this->student1->extracurriculars()->syncWithoutDetaching([
        $this->ekskul->id => ['joined_at' => '2026-07-15'],
    ]);

    // Record extracurricular attendance
    ExtracurricularAttendance::create([
        'extracurricular_id' => $this->ekskul->id,
        'student_id' => $this->student1->id,
        'date' => '2026-10-16',
        'time' => '15:45:00',
        'status' => 'hadir',
        'method' => 'manual',
    ]);

    $response = $this->actingAs($this->kepalaSekolahUser)
        ->get(route('reports.print.combined', [
            'class_id' => $this->schoolClass->id,
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
        ]));

    $response->assertStatus(200);

    // Header inspection
    $response->assertSee('V. EKSTRAKURIKULER');
    $response->assertSee('Ekskul');
    $response->assertSee('Hdr');

    // Student data row inspection
    $response->assertSee('Pramuka Wajib');
    $response->assertSee($this->student1->name);
});

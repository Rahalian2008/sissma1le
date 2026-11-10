<?php

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\Extracurricular;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    // Create active academic year
    $this->academicYear = AcademicYear::firstOrCreate(
        ['name' => '2026/2027'],
        ['semester' => 'Ganjil', 'is_active' => true]
    );

    // Create a school class
    $this->schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'XI-MIPA-1'],
        [
            'grade' => 'XI',
            'major' => 'MIPA',
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]
    );

    // Admin user
    $this->adminUser = User::firstOrCreate(
        ['email' => 'admin.test5@sma1le.sch.id'],
        [
            'name' => 'Super Administrator',
            'username' => 'ADMIN5',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin', 'super_admin'],
            'is_active' => true,
        ]
    );

    // Wali Kelas user
    $this->homeroomUser = User::firstOrCreate(
        ['email' => 'walikelas.test5@sma1le.sch.id'],
        [
            'name' => 'Wali Kelas Test',
            'username' => 'WALIKELAS5',
            'password' => bcrypt('password'),
            'role' => 'wali_kelas',
            'roles' => ['wali_kelas', 'guru'],
            'is_active' => true,
        ]
    );
    $this->homeroomTeacher = Teacher::firstOrCreate(
        ['nip' => '198101012005011005'],
        [
            'user_id' => $this->homeroomUser->id,
            'name' => 'Wali Kelas Test',
            'gender' => 'L',
            'is_active' => true,
        ]
    );
    $this->schoolClass->update(['teacher_id' => $this->homeroomTeacher->id]);

    // Pembina Ekstra user
    $this->pembinaUser = User::firstOrCreate(
        ['email' => 'pembina.test5@sma1le.sch.id'],
        [
            'name' => 'Pembina Ekstra Test',
            'username' => 'PEMBINA5',
            'password' => bcrypt('password'),
            'role' => 'pembina_ekstra',
            'roles' => ['pembina_ekstra', 'guru'],
            'is_active' => true,
        ]
    );
    $this->pembinaTeacher = Teacher::firstOrCreate(
        ['nip' => '198202022006021006'],
        [
            'user_id' => $this->pembinaUser->id,
            'name' => 'Pembina Ekstra Test',
            'gender' => 'L',
            'is_active' => true,
        ]
    );

    // Extracurricular
    $this->extra = Extracurricular::firstOrCreate(
        ['code' => 'PMR5'],
        [
            'name' => 'Palang Merah Remaja',
            'category' => 'Kemanusiaan & Kesehatan',
            'teacher_id' => $this->pembinaTeacher->id,
            'schedule_day' => 'Jumat',
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'location' => 'UKS SMA1LE',
            'is_active' => true,
        ]
    );
    $this->pembinaTeacher->extracurriculars()->syncWithoutDetaching([$this->extra->id => ['role' => 'Pembina Utama']]);

    // Female student with extracurricular
    $this->femaleUser = User::firstOrCreate(
        ['email' => 'siswi.test5@sma1le.sch.id'],
        [
            'name' => 'Aisyah Zahra Putri',
            'username' => '0098765431',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'roles' => ['siswa'],
            'is_active' => true,
        ]
    );
    $this->femaleStudent = Student::firstOrCreate(
        ['nisn' => '0098765431'],
        [
            'user_id' => $this->femaleUser->id,
            'class_id' => $this->schoolClass->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Aisyah Zahra Putri',
            'initials' => 'AZP',
            'nis' => '18001',
            'gender' => 'P',
            'is_active' => true,
        ]
    );
    $this->extra->students()->syncWithoutDetaching([$this->femaleStudent->id]);

    // Male student without extracurricular
    $this->maleUser = User::firstOrCreate(
        ['email' => 'siswa.test5@sma1le.sch.id'],
        [
            'name' => 'Muhammad Budi Pratama',
            'username' => '0098765432',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'roles' => ['siswa'],
            'is_active' => true,
        ]
    );
    $this->maleStudent = Student::firstOrCreate(
        ['nisn' => '0098765432'],
        [
            'user_id' => $this->maleUser->id,
            'class_id' => $this->schoolClass->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Muhammad Budi Pratama',
            'initials' => 'MBP',
            'nis' => '18002',
            'gender' => 'L',
            'is_active' => true,
        ]
    );
});

test('item 1: student dashboard displays quick actions with gender and extracurricular conditions', function () {
    // 1. Female student with PMR extra
    $response = $this->actingAs($this->femaleUser)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Menu Cepat Presensi Siswa');
    $response->assertSee('Presensi Utama');
    $response->assertSee('Palang Merah Remaja');
    $response->assertSee('Shalat Dzuhur');
    $response->assertSee('Shalat Dhuha');
    $response->assertSee('Khusus Siswi Muslimah');

    // 2. Male student without extra
    $maleResponse = $this->actingAs($this->maleUser)->get(route('dashboard'));
    $maleResponse->assertStatus(200);
    $maleResponse->assertSee('Presensi Utama');
    $maleResponse->assertSee('Shalat Dzuhur');
    $maleResponse->assertSee('Shalat Dhuha');
    // Ekstrakurikuler & Keputrian quick action buttons should NOT appear for male non-member
    $maleResponse->assertDontSee('Khusus Siswi Muslimah');
    $maleResponse->assertDontSee('Palang Merah Remaja');
});

test('item 2: staff dashboards have quick actions for violation, achievement, and leave approval', function () {
    // Admin dashboard
    $adminRes = $this->actingAs($this->adminUser)->get(route('dashboard'));
    $adminRes->assertStatus(200);
    $adminRes->assertSee('Input Pelanggaran');
    $adminRes->assertSee('Input Prestasi');
    $adminRes->assertSee('Pengajuan Izin');
    $adminRes->assertSee('Approve Izin');

    // Wali Kelas dashboard
    $homeroomRes = $this->actingAs($this->homeroomUser)->get(route('dashboard'));
    $homeroomRes->assertStatus(200);
    $homeroomRes->assertSee('Input Pelanggaran');
    $homeroomRes->assertSee('Input Prestasi');
    $homeroomRes->assertSee('Approve Izin');

    // Pembina Ekstra dashboard has quick access to extracurricular management
    $pembinaRes = $this->actingAs($this->pembinaUser)->get(route('dashboard'));
    $pembinaRes->assertStatus(200);
    $pembinaRes->assertSee('Kelola Ekstrakurikuler');
});

test('item 3: admin can verify violations and achievements directly beside detail view', function () {
    $achCategory = AchievementCategory::first() ?? AchievementCategory::create([
        'code' => 'R99',
        'name' => 'Akademik Khusus',
        'max_point' => 100,
    ]);
    $achievement = Achievement::create([
        'student_id' => $this->femaleStudent->id,
        'category_id' => $achCategory->id,
        'title' => 'Juara Debat Bahasa Inggris',
        'level' => 'KABUPATEN',
        'points' => 50,
        'date' => today(),
        'status' => 'MENUNGGU_VERIFIKASI',
    ]);

    $violCategory = ViolationCategory::first() ?? ViolationCategory::create([
        'code' => 'P99',
        'name' => 'Keterlambatan',
        'point' => 5,
    ]);
    $violation = Violation::create([
        'student_id' => $this->maleStudent->id,
        'category_id' => $violCategory->id,
        'title' => 'Terlambat 10 Menit',
        'points' => 5,
        'date' => today(),
        'status' => 'MENUNGGU_VERIFIKASI',
    ]);

    // Check admin index view has direct verification buttons
    $achIndexRes = $this->actingAs($this->adminUser)->get(route('achievements.index'));
    $achIndexRes->assertStatus(200);
    $achIndexRes->assertSee('Approve');
    $achIndexRes->assertSee('Tolak');

    $violIndexRes = $this->actingAs($this->adminUser)->get(route('violations.index'));
    $violIndexRes->assertStatus(200);
    $violIndexRes->assertSee('Approve');
    $violIndexRes->assertSee('Tolak');

    // Direct approve achievement
    $approveAchRes = $this->actingAs($this->adminUser)->post(route('achievements.verify', $achievement->id), [
        'status' => 'DIVERIFIKASI',
        'notes' => 'Disetujui langsung oleh admin',
    ]);
    $approveAchRes->assertRedirect();
    expect($achievement->fresh()->status)->toBe('DIVERIFIKASI');

    // Direct reject violation
    $rejectViolRes = $this->actingAs($this->adminUser)->post(route('violations.verify', $violation->id), [
        'status' => 'DITOLAK',
        'rejection_reason' => 'Bukti tidak cukup',
    ]);
    $rejectViolRes->assertRedirect();
    expect($violation->fresh()->status)->toBe('DITOLAK');
});

test('item 4: pembina ekstra can add and remove student members', function () {
    // Male student is currently not member of PMR5
    expect($this->extra->students()->where('student_id', $this->maleStudent->id)->exists())->toBeFalse();

    // Pembina adds male student to PMR5
    $addRes = $this->actingAs($this->pembinaUser)->post(route('extracurricular.members.add', $this->extra->id), [
        'student_id' => $this->maleStudent->id,
        'notes' => 'Anggota Divisi Pertolongan Pertama',
    ]);
    $addRes->assertRedirect();
    $addRes->assertSessionHas('success');
    expect($this->extra->students()->where('student_id', $this->maleStudent->id)->exists())->toBeTrue();

    // Pembina removes male student from PMR5
    $removeRes = $this->actingAs($this->pembinaUser)->delete(route('extracurricular.members.remove', [
        'id' => $this->extra->id,
        'studentId' => $this->maleStudent->id,
    ]));
    $removeRes->assertRedirect();
    $removeRes->assertSessionHas('success');
    expect($this->extra->students()->where('student_id', $this->maleStudent->id)->exists())->toBeFalse();
});

test('item 5: student model generates initials and search filters work with initials', function () {
    $student = new Student(['name' => 'Fadhil Ahmad Pratama']);
    expect($student->initials)->toBe('FAP');

    // Search by initials in MasterDataController
    $searchRes = $this->actingAs($this->adminUser)->get(route('master.students', ['search' => 'AZP']));
    $searchRes->assertStatus(200);
    $searchRes->assertSee('Aisyah Zahra Putri');
    $searchRes->assertDontSee('Muhammad Budi Pratama');
});

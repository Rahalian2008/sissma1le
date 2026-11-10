<?php

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Carbon::setTestNow(Carbon::today()->setTime(16, 0, 0));

    // Ensure teacher with pembina_ekstra user
    $this->pembinaUser = User::firstOrCreate(
        ['email' => 'pembina.test@sma1le.sch.id'],
        [
            'name' => 'Budi Santoso, S.Pd.',
            'username' => 'PEMBINA01',
            'password' => bcrypt('password'),
            'role' => 'pembina_ekstra',
            'roles' => ['pembina_ekstra', 'guru'],
            'is_active' => true,
        ]
    );

    $this->teacher = Teacher::firstOrCreate(
        ['nip' => '198501012010011005'],
        [
            'user_id' => $this->pembinaUser->id,
            'name' => 'Budi Santoso, S.Pd.',
            'gender' => 'L',
            'title' => 'Pembina Pramuka',
            'is_active' => true,
        ]
    );

    // Ensure extracurricular
    $this->extra = Extracurricular::firstOrCreate(
        ['code' => 'PRA'],
        [
            'name' => 'Gerakan Pramuka Ambalan',
            'category' => 'Kepanduan & Bela Negara',
            'teacher_id' => $this->teacher->id,
            'schedule_day' => Carbon::now()->locale('id')->translatedFormat('l'),
            'start_time' => '15:00:00',
            'end_time' => '17:30:00',
            'location' => 'Lapangan Utama',
            'is_active' => true,
            'description' => 'Kegiatan kepramukaan penegak.',
        ]
    );

    $this->teacher->extracurriculars()->syncWithoutDetaching([$this->extra->id => ['role' => 'Pembina Utama']]);

    // Ensure class & student
    $this->schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'X-E1'],
        [
            'grade' => 'X',
            'major' => 'Fase E',
            'is_active' => true,
        ]
    );

    $this->studentUser = User::firstOrCreate(
        ['email' => 'siswa.ekstra@sma1le.sch.id'],
        [
            'name' => 'Andi Pratama',
            'username' => '0071234567',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'roles' => ['siswa'],
            'is_active' => true,
        ]
    );

    $this->student = Student::firstOrCreate(
        ['nisn' => '0071234567'],
        [
            'user_id' => $this->studentUser->id,
            'class_id' => $this->schoolClass->id,
            'name' => 'Andi Pratama',
            'nis' => '12001',
            'gender' => 'L',
            'is_active' => true,
        ]
    );

    $this->extra->students()->syncWithoutDetaching([$this->student->id => ['role' => 'Anggota']]);
});

afterEach(function () {
    Carbon::setTestNow();
});

test('role pembina_ekstra is recognized on user model and can switch via quick switcher', function () {
    expect($this->pembinaUser->isPembinaEkstra())->toBeTrue();
    expect($this->pembinaUser->canManageExtracurricular())->toBeTrue();

    // Test quick role switcher allows pembina_ekstra
    $admin = User::where('role', 'super_admin')->first() ?? User::factory()->create(['role' => 'super_admin']);
    $response = $this->actingAs($admin)->post(route('quick.switch'), [
        'role' => 'pembina_ekstra',
    ]);

    $response->assertRedirect(route('dashboard'));
});

test('master extracurricular page is accessible by admin and displays extracurriculars list', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('master.extracurriculars'));

    $response->assertStatus(200);
    $response->assertSee('Data Master Ekstrakurikuler');
    $response->assertSee('Gerakan Pramuka Ambalan');
});

test('master teachers page displays pembina role badge and coached extracurricular names', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('master.teachers'));

    $response->assertStatus(200);
    $response->assertSee('PEMBINA');
});

test('admin can create and update extracurricular and manage student members', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    // 1. Create Extracurricular
    $response = $this->actingAs($admin)->post(route('master.extracurriculars.store'), [
        'code' => 'ROB',
        'name' => 'Klub Robotika & IoT',
        'category' => 'Teknologi & Multimedia',
        'teacher_id' => $this->teacher->id,
        'schedule_day' => 'Kamis',
        'start_time' => '15:30',
        'end_time' => '17:30',
        'location' => 'Lab Komputer 1',
        'is_active' => 1,
        'description' => 'Ekskul robotika dan mikroprosesor.',
    ]);

    $response->assertRedirect();
    $extra = Extracurricular::where('code', 'ROB')->first();
    expect($extra)->not->toBeNull();
    expect($extra->name)->toBe('Klub Robotika & IoT');

    // 2. Add Student Member
    $addMemberRes = $this->actingAs($admin)->post(route('master.extracurriculars.members.add', $extra->id), [
        'student_id' => $this->student->id,
        'role' => 'Ketua Divisi Hardware',
    ]);

    $addMemberRes->assertRedirect();
    expect($extra->students()->where('student_id', $this->student->id)->exists())->toBeTrue();

    // 3. Remove Student Member
    $removeRes = $this->actingAs($admin)->delete(route('master.extracurriculars.members.remove', [$extra->id, $this->student->id]));
    $removeRes->assertRedirect();
    expect($extra->students()->where('student_id', $this->student->id)->exists())->toBeFalse();
});

test('extracurricular attendance index page displays stats and tabs properly', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('extracurricular.index', ['extra_id' => $this->extra->id]));

    $response->assertStatus(200);
    $response->assertSee('Presensi Ekstrakurikuler');
    $response->assertSee('Scanner RFID');
    $response->assertSee('Presensi Kamera Selfie');
    $response->assertSee('Absen Manual Anggota');
});

test('rfid scan successfully records attendance for enrolled student and rejects non-member', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    // Scan for valid enrolled student
    $res = $this->actingAs($admin)->postJson(route('extracurricular.scan'), [
        'extracurricular_id' => $this->extra->id,
        'identifier' => $this->student->nisn,
        'method' => 'RFID',
        'bypass_time_lock' => true,
    ]);

    $res->assertStatus(200);
    $res->assertJsonPath('success', true);
    expect(ExtracurricularAttendance::where('extracurricular_id', $this->extra->id)->where('student_id', $this->student->id)->exists())->toBeTrue();

    // Create a student who is not a member of $this->extra
    $nonMember = Student::create([
        'name' => 'Siswa Luar Ekskul',
        'nisn' => '9999000111',
        'nis' => '19999',
        'gender' => 'L',
        'class_id' => $this->schoolClass->id,
        'is_active' => true,
    ]);

    $rejRes = $this->actingAs($admin)->postJson(route('extracurricular.scan'), [
        'extracurricular_id' => $this->extra->id,
        'identifier' => $nonMember->nisn,
        'method' => 'RFID',
        'bypass_time_lock' => true,
    ]);

    $rejRes->assertStatus(422);
    $rejRes->assertJsonPath('error_code', 'NOT_A_MEMBER');
});

test('manual attendance submission stores records properly for members', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post(route('extracurricular.manual'), [
        'extracurricular_id' => $this->extra->id,
        'date' => Carbon::today()->toDateString(),
        'statuses' => [
            $this->student->id => 'HADIR',
        ],
        'notes' => [
            $this->student->id => 'Latihan tepat waktu',
        ],
    ]);

    $response->assertRedirect();
    expect(ExtracurricularAttendance::where('extracurricular_id', $this->extra->id)->where('student_id', $this->student->id)->exists())->toBeTrue();
});

test('extracurricular report and print pages render without error', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    // Report page
    $repRes = $this->actingAs($admin)->get(route('extracurricular.report', ['extra_id' => $this->extra->id]));
    $repRes->assertStatus(200);
    $repRes->assertSee('Dokumen Laporan Presensi Ekstrakurikuler');

    // Print page
    $printRes = $this->actingAs($admin)->get(route('extracurricular.print', ['extra_id' => $this->extra->id]));
    $printRes->assertStatus(200);
    $printRes->assertSee('REKAPITULASI PRESENSI EKSTRAKURIKULER');
});

test('student enrolled in extracurricular sees extracurricular attendance on dashboard, sidebar, and can access attendance index', function () {
    // Andi Pratama is already enrolled in Gerakan Pramuka Ambalan in beforeEach
    $response = $this->actingAs($this->studentUser)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Presensi Ekstrakurikuler');
    $response->assertSee('Gerakan Pramuka Ambalan');
    $response->assertSee('KEANGGOTAAN EKSTRAKURIKULER');

    // Access extracurricular index
    $indexRes = $this->actingAs($this->studentUser)->get(route('extracurricular.index'));
    $indexRes->assertStatus(200);
    $indexRes->assertSee('Gerakan Pramuka Ambalan');
});

test('student NOT enrolled in any extracurricular does not see extracurricular attendance in sidebar and is redirected when accessing index', function () {
    $nonMemberUser = User::create([
        'name' => 'Siswa Non Ekskul',
        'username' => '0089998888',
        'email' => 'nonekskul@sma1le.sch.id',
        'password' => bcrypt('password'),
        'role' => 'siswa',
        'roles' => ['siswa'],
        'is_active' => true,
    ]);

    $nonMemberStudent = Student::create([
        'user_id' => $nonMemberUser->id,
        'class_id' => $this->schoolClass->id,
        'name' => 'Siswa Non Ekskul',
        'nisn' => '0089998888',
        'nis' => '12999',
        'gender' => 'L',
        'is_active' => true,
    ]);

    // Check dashboard: extracurricular section and menu should not appear
    $response = $this->actingAs($nonMemberUser)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertDontSee('b. Presensi Ekstrakurikuler');
    $response->assertDontSee('KEANGGOTAAN EKSTRAKURIKULER');

    // Direct access to extracurricular index should be redirected with explanatory notice
    $indexRes = $this->actingAs($nonMemberUser)->get(route('extracurricular.index'));
    $indexRes->assertRedirect(route('dashboard'));
    $indexRes->assertSessionHas('error', 'Presensi ekstrakurikuler hanya aktif dan dapat diakses oleh siswa yang terdaftar sebagai anggota ekstrakurikuler.');
});

test('admin can assign a student as extracurricular secretary with specific extracurricular', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin.test@sma1le.sch.id'],
        [
            'name' => 'Admin Utama',
            'username' => 'admin_test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
            'is_active' => true,
        ]
    );

    $academicYear = AcademicYear::firstOrCreate(
        ['name' => '2026/2027', 'semester' => 'Ganjil'],
        ['is_active' => true]
    );

    // Update student as secretary of $this->extra
    $response = $this->actingAs($admin)->put(route('master.students.update', $this->student->id), [
        'nis' => $this->student->nis,
        'nisn' => $this->student->nisn,
        'name' => $this->student->name,
        'gender' => $this->student->gender,
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => 1,
        'is_extra_secretary' => 1,
        'extra_secretary_id' => $this->extra->id,
    ]);

    $response->assertSessionHas('success');

    // Verify pivot role and user role
    $this->student->refresh();
    expect($this->student->user->hasRole('sekretaris_ekstra'))->toBeTrue();
    $pivotRole = $this->student->extracurriculars()->where('extracurricular_id', $this->extra->id)->first()->pivot->role;
    expect($pivotRole)->toBe('Sekretaris');

    // Secretary logs in and goes to dashboard
    $dashRes = $this->actingAs($this->student->user)->get(route('dashboard'));
    $dashRes->assertStatus(200);
    $dashRes->assertSee('ABSENKAN ANGGOTA EKSKUL');

    // Secretary goes to extracurricular index
    $secretaryRes = $this->actingAs($this->student->user)->get(route('extracurricular.index'));
    $secretaryRes->assertStatus(200);
    $secretaryRes->assertSee($this->extra->name);
    $secretaryRes->assertSee('Absen Manual Anggota');
    $secretaryRes->assertSee('Akses Sekretaris Ekskul');

    // Secretary submits manual attendance for members
    $manualRes = $this->actingAs($this->student->user)->post(route('extracurricular.manual'), [
        'extracurricular_id' => $this->extra->id,
        'date' => Carbon::today()->toDateString(),
        'statuses' => [
            $this->student->id => 'HADIR',
        ],
    ]);

    $manualRes->assertRedirect(route('extracurricular.index', [
        'extra_id' => $this->extra->id,
        'date' => Carbon::today()->toDateString(),
        'tab' => 'manual',
    ]));

    expect(ExtracurricularAttendance::where('extracurricular_id', $this->extra->id)
        ->where('student_id', $this->student->id)
        ->where('status', 'HADIR')
        ->exists())->toBeTrue();

    // Admin unchecks secretary role
    $this->actingAs($admin)->put(route('master.students.update', $this->student->id), [
        'nis' => $this->student->nis,
        'nisn' => $this->student->nisn,
        'name' => $this->student->name,
        'gender' => $this->student->gender,
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => 1,
        'is_extra_secretary' => 0,
        'extra_secretary_id' => null,
    ]);

    $this->student->refresh();
    expect($this->student->user->hasRole('sekretaris_ekstra'))->toBeFalse();
    $demotedRole = $this->student->extracurriculars()->where('extracurricular_id', $this->extra->id)->first()->pivot->role;
    expect($demotedRole)->toBe('Anggota');
});

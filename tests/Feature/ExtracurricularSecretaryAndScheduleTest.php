<?php

use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 15:30:00', 'Asia/Jakarta'));

    // 1. Setup Class
    $this->schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'XI-F1'],
        [
            'grade' => 'XI',
            'major' => 'Fase F',
            'is_active' => true,
        ]
    );

    // 2. Setup Pembina Teacher & User
    $this->pembinaUser = User::firstOrCreate(
        ['email' => 'pembina.futsal@sma1le.sch.id'],
        [
            'name' => 'Coach Bambang, S.Pd.',
            'username' => 'COACH01',
            'password' => bcrypt('password'),
            'role' => 'pembina_ekstra',
            'roles' => ['pembina_ekstra', 'guru'],
            'is_active' => true,
        ]
    );

    $this->pembinaTeacher = Teacher::firstOrCreate(
        ['nip' => '198802022015011002'],
        [
            'user_id' => $this->pembinaUser->id,
            'name' => 'Coach Bambang, S.Pd.',
            'gender' => 'L',
            'title' => 'Pembina Futsal',
            'is_active' => true,
        ]
    );

    // Other Teacher (Not Pembina of Futsal)
    $this->otherTeacherUser = User::firstOrCreate(
        ['email' => 'other.guru@sma1le.sch.id'],
        [
            'name' => 'Guru Lain, M.Pd.',
            'username' => 'GURU99',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'roles' => ['guru'],
            'is_active' => true,
        ]
    );

    $this->otherTeacher = Teacher::firstOrCreate(
        ['nip' => '199003032018022003'],
        [
            'user_id' => $this->otherTeacherUser->id,
            'name' => 'Guru Lain, M.Pd.',
            'gender' => 'P',
            'is_active' => true,
        ]
    );

    // 3. Setup Extracurricular 1 (Futsal) and Extracurricular 2 (Paskibra)
    $this->extraFutsal = Extracurricular::firstOrCreate(
        ['code' => 'FUTSAL-TEST'],
        [
            'name' => 'Futsal Club',
            'category' => 'Olahraga',
            'teacher_id' => $this->pembinaTeacher->id,
            'schedule_day' => 'senin,kamis',
            'start_time' => '15:00:00',
            'end_time' => '17:30:00',
            'location' => 'Lapangan Futsal Outdoor',
            'is_active' => true,
            'description' => 'Kegiatan ekstrakurikuler futsal.',
        ]
    );
    $this->pembinaTeacher->extracurriculars()->syncWithoutDetaching([$this->extraFutsal->id => ['role' => 'Pembina Utama']]);

    $this->extraPaskibra = Extracurricular::firstOrCreate(
        ['code' => 'PASKIBRA-TEST'],
        [
            'name' => 'Paskibra Satya',
            'category' => 'Kepanduan & Bela Negara',
            'teacher_id' => $this->otherTeacher->id,
            'schedule_day' => 'selasa,sabtu',
            'start_time' => '14:00:00',
            'end_time' => '17:00:00',
            'location' => 'Lapangan Utama',
            'is_active' => true,
            'description' => 'Pasukan Pengibar Bendera.',
        ]
    );

    // 4. Setup Students
    $this->student1User = User::firstOrCreate(
        ['email' => 'siswa1.futsal@sma1le.sch.id'],
        [
            'name' => 'Siswa Satu',
            'username' => 'NIS001',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'roles' => ['siswa'],
            'is_active' => true,
        ]
    );
    $this->student1 = Student::firstOrCreate(
        ['nis' => 'NIS001'],
        [
            'user_id' => $this->student1User->id,
            'nisn' => '0012345671',
            'name' => 'Siswa Satu',
            'class_id' => $this->schoolClass->id,
            'gender' => 'L',
            'status' => 'AKTIF',
        ]
    );

    $this->student2User = User::firstOrCreate(
        ['email' => 'siswa2.futsal@sma1le.sch.id'],
        [
            'name' => 'Siswa Dua',
            'username' => 'NIS002',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'roles' => ['siswa'],
            'is_active' => true,
        ]
    );
    $this->student2 = Student::firstOrCreate(
        ['nis' => 'NIS002'],
        [
            'user_id' => $this->student2User->id,
            'nisn' => '0012345672',
            'name' => 'Siswa Dua',
            'class_id' => $this->schoolClass->id,
            'gender' => 'L',
            'status' => 'AKTIF',
        ]
    );

    // Add students to Futsal
    $this->extraFutsal->students()->syncWithoutDetaching([
        $this->student1->id => ['role' => 'Anggota'],
        $this->student2->id => ['role' => 'Anggota'],
    ]);

    // 5. Setup Secretary Single Role (Sekretaris Ekskul Futsal)
    $this->secretarySingleUser = User::firstOrCreate(
        ['email' => 'sekretaris.single@sma1le.sch.id'],
        [
            'name' => 'Sekretaris Futsal',
            'username' => 'SEKEXTRA01',
            'password' => bcrypt('password'),
            'role' => 'sekretaris_ekstra',
            'roles' => ['sekretaris_ekstra'],
            'is_active' => true,
        ]
    );
    $this->secretarySingleStudent = Student::firstOrCreate(
        ['nis' => 'SEKEXTRA01'],
        [
            'user_id' => $this->secretarySingleUser->id,
            'nisn' => '0012345673',
            'name' => 'Sekretaris Futsal',
            'class_id' => $this->schoolClass->id,
            'gender' => 'P',
            'status' => 'AKTIF',
        ]
    );
    // Enrolled in Futsal as Sekretaris
    $this->extraFutsal->students()->syncWithoutDetaching([
        $this->secretarySingleStudent->id => ['role' => 'Sekretaris'],
    ]);

    // 6. Setup Secretary Double Role (Sekretaris Ekskul AND Sekretaris Kelas)
    $this->secretaryDoubleUser = User::firstOrCreate(
        ['email' => 'sekretaris.double@sma1le.sch.id'],
        [
            'name' => 'Sekretaris Double',
            'username' => 'SEKDBL01',
            'password' => bcrypt('password'),
            'role' => 'sekretaris_ekstra',
            'roles' => ['sekretaris_ekstra', 'sekretaris_kelas'],
            'is_active' => true,
        ]
    );
    $this->secretaryDoubleStudent = Student::firstOrCreate(
        ['nis' => 'SEKDBL01'],
        [
            'user_id' => $this->secretaryDoubleUser->id,
            'nisn' => '0012345674',
            'name' => 'Sekretaris Double',
            'class_id' => $this->schoolClass->id,
            'gender' => 'P',
            'status' => 'AKTIF',
        ]
    );
});

// Poin 1: Pembina ekstrakurikuler dikasih akses untuk mensetting jadwal ekstrakurikulernya
test('pembina can update their extracurricular schedule', function () {
    $this->actingAs($this->pembinaUser)
        ->post(route('extracurricular.schedule.update', $this->extraFutsal->id), [
            'schedule_days' => ['senin', 'rabu', 'jumat'],
            'start_time' => '15:30',
            'end_time' => '17:45',
            'location' => 'Gor Futsal Indoor',
            'description' => 'Latihan intensif persiapan turnamen.',
        ])
        ->assertRedirect(route('extracurricular.index', ['extra_id' => $this->extraFutsal->id]))
        ->assertSessionHas('success');

    $this->extraFutsal->refresh();
    expect($this->extraFutsal->schedule_day)->toContain('senin')
        ->and($this->extraFutsal->schedule_day)->toContain('jumat')
        ->and($this->extraFutsal->start_time)->toBe('15:30:00')
        ->and($this->extraFutsal->end_time)->toBe('17:45:00')
        ->and($this->extraFutsal->location)->toBe('Gor Futsal Indoor');
});

test('unauthorized teacher cannot update extracurricular schedule', function () {
    $this->actingAs($this->otherTeacherUser)
        ->post(route('extracurricular.schedule.update', $this->extraFutsal->id), [
            'schedule_days' => ['selasa'],
            'start_time' => '14:00',
            'end_time' => '16:00',
        ])
        ->assertForbidden();
});

// Poin 2: Role sekretaris ekskul hanya bisa mengabsenkan manual pada ekstrakurikuler yang diikutinya saja,
// kecuali posisinya double role menjadi sekretaris ekskul dan sekretaris kelas
test('single role sekretaris ekskul can take manual attendance for joined extracurricular', function () {
    $this->actingAs($this->secretarySingleUser)
        ->post(route('extracurricular.manual'), [
            'extracurricular_id' => $this->extraFutsal->id,
            'date' => '2026-10-19',
            'statuses' => [
                $this->student1->id => 'HADIR',
                $this->student2->id => 'IZIN',
            ],
            'bypass_time_lock' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ExtracurricularAttendance::where('extracurricular_id', $this->extraFutsal->id)
        ->where('student_id', $this->student1->id)
        ->where('status', 'HADIR')
        ->where('method', 'MANUAL')
        ->exists())->toBeTrue();
});

test('single role sekretaris ekskul CANNOT take manual attendance for non-joined extracurricular', function () {
    // secretarySingleUser is NOT a member of extraPaskibra
    $this->actingAs($this->secretarySingleUser)
        ->post(route('extracurricular.manual'), [
            'extracurricular_id' => $this->extraPaskibra->id,
            'date' => '2026-10-19',
            'statuses' => [
                $this->student1->id => 'HADIR',
            ],
            'bypass_time_lock' => true,
        ])
        ->assertForbidden();
});

test('double role sekretaris ekskul and sekretaris kelas CAN take manual attendance for ANY extracurricular', function () {
    // secretaryDoubleUser is NOT a member of extraPaskibra, but has double role
    $this->actingAs($this->secretaryDoubleUser)
        ->post(route('extracurricular.manual'), [
            'extracurricular_id' => $this->extraPaskibra->id,
            'date' => '2026-10-19',
            'statuses' => [
                $this->student1->id => 'HADIR',
            ],
            'bypass_time_lock' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ExtracurricularAttendance::where('extracurricular_id', $this->extraPaskibra->id)
        ->where('student_id', $this->student1->id)
        ->where('status', 'HADIR')
        ->where('recorded_by', $this->secretaryDoubleUser->id)
        ->exists())->toBeTrue();
});

// Poin 3 & 4: Multi-student batch manual attendance saves accurately
test('multi student batch manual attendance saves accurately with method MANUAL and recorded_by', function () {
    $this->actingAs($this->secretarySingleUser)
        ->post(route('extracurricular.manual'), [
            'extracurricular_id' => $this->extraFutsal->id,
            'date' => '2026-10-19',
            'statuses' => [
                $this->student1->id => 'TERLAMBAT',
                $this->student2->id => 'SAKIT',
            ],
            'notes' => [
                $this->student1->id => 'Ban bocor di jalan',
                $this->student2->id => 'Demam flu',
            ],
            'bypass_time_lock' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $att1 = ExtracurricularAttendance::where('extracurricular_id', $this->extraFutsal->id)
        ->where('student_id', $this->student1->id)
        ->first();

    $att2 = ExtracurricularAttendance::where('extracurricular_id', $this->extraFutsal->id)
        ->where('student_id', $this->student2->id)
        ->first();

    expect($att1)->not->toBeNull()
        ->and($att1->status)->toBe('TERLAMBAT')
        ->and($att1->method)->toBe('MANUAL')
        ->and($att1->notes)->toBe('Ban bocor di jalan')
        ->and($att1->recorded_by)->toBe($this->secretarySingleUser->id);

    expect($att2)->not->toBeNull()
        ->and($att2->status)->toBe('SAKIT')
        ->and($att2->method)->toBe('MANUAL')
        ->and($att2->notes)->toBe('Demam flu')
        ->and($att2->recorded_by)->toBe($this->secretarySingleUser->id);
});

// Poin 5: Role Sekretaris Ekskul jangan bisa mengabsenkan manual pada presensi utama,
// hanya absen manual di ekstrakurikulernya saja kecuali siswa tersebut double jabatan
// menjadi sekretaris kelas dan sekretaris ekskul maka fitur absen manual pada semua presensi menjadi aktif
test('single role sekretaris ekskul CANNOT take manual attendance on presensi utama', function () {
    // 1. canRecordAttendanceManual() on User must be false
    expect($this->secretarySingleUser->canRecordAttendanceManual())->toBeFalse();

    // 2. Cannot access manual form
    $this->actingAs($this->secretarySingleUser)
        ->get(route('attendance.manual'))
        ->assertForbidden();

    // 3. Cannot submit single manual attendance
    $this->actingAs($this->secretarySingleUser)
        ->post(route('attendance.manual.store'), [
            'student_id' => $this->student1->id,
            'date' => '2026-10-19',
            'status' => 'HADIR',
        ])
        ->assertForbidden();

    // 4. Cannot submit bulk manual attendance
    $this->actingAs($this->secretarySingleUser)
        ->post(route('attendance.manual.bulk'), [
            'class_id' => $this->schoolClass->id,
            'date' => '2026-10-19',
            'statuses' => [$this->student1->id => 'HADIR'],
        ])
        ->assertForbidden();

    // 5. Attendance index fallback from ?tab=manual to selfie
    $response = $this->actingAs($this->secretarySingleUser)
        ->get(route('attendance.index', ['tab' => 'manual']));
    $response->assertOk();
    $response->assertViewHas('canRecordManual', false);
    $response->assertViewHas('activeTab', 'selfie');
});

test('double role sekretaris ekskul and sekretaris kelas CAN take manual attendance on presensi utama', function () {
    // 1. canRecordAttendanceManual() must be true
    expect($this->secretaryDoubleUser->canRecordAttendanceManual())->toBeTrue();

    // 2. Can access manual form
    $this->actingAs($this->secretaryDoubleUser)
        ->get(route('attendance.manual'))
        ->assertOk()
        ->assertSee($this->student1->name);

    // 3. Can submit single manual attendance for classmate
    $this->actingAs($this->secretaryDoubleUser)
        ->post(route('attendance.manual.store'), [
            'student_id' => $this->student1->id,
            'date' => '2026-10-19',
            'status' => 'HADIR',
            'notes' => 'Diabsenkan manual oleh sekretaris kelas & ekskul',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // 4. Attendance index allows ?tab=manual
    $response = $this->actingAs($this->secretaryDoubleUser)
        ->get(route('attendance.index', ['tab' => 'manual']));
    $response->assertOk();
    $response->assertViewHas('canRecordManual', true);
    $response->assertViewHas('activeTab', 'manual');
});

test('single role sekretaris ekskul CANNOT take manual attendance on worship, but double role CAN', function () {
    // Single role cannot submit manual worship attendance (forbidden)
    $this->actingAs($this->secretarySingleUser)
        ->post(route('worship.manual', ['activity' => 'dzuhur']), [
            'class_id' => $this->schoolClass->id,
            'date' => '2026-10-19',
            'statuses' => [$this->student1->id => 'HADIR'],
        ])
        ->assertForbidden();

    // Double role can submit manual worship attendance within valid time window
    Carbon::setTestNow(Carbon::parse('2026-10-19 12:00:00', 'Asia/Jakarta'));

    $this->actingAs($this->secretaryDoubleUser)
        ->post(route('worship.manual', ['activity' => 'dzuhur']), [
            'class_id' => $this->schoolClass->id,
            'date' => '2026-10-19',
            'statuses' => [$this->student1->id => 'HADIR'],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
});

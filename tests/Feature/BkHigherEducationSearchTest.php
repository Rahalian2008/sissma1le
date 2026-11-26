<?php

use App\Models\BkCounselingSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\HigherEducationService;

beforeEach(function () {
    $this->bkTeacher = User::where('role', 'guru_bk')->orWhere('role', 'bk')->orWhere('role', 'koordinator_bk')->first();
    if (! $this->bkTeacher) {
        $this->bkTeacher = User::create([
            'name' => 'Guru BK Test Kampus',
            'username' => 'gurubkkampus',
            'email' => 'gurubkkampus@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru_bk',
            'is_active' => true,
        ]);
    }

    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'XII-MIPA-1', 'level' => 'XII']);
    $this->student = Student::first();
    if (! $this->student) {
        $this->student = Student::create([
            'name' => 'Konseli Karier Test',
            'nisn' => '0099112233',
            'class_id' => $class->id,
            'gender' => 'L',
            'is_active' => true,
        ]);
    }
});

test('higher education service provides comprehensive list of java universities and majors', function () {
    $universities = HigherEducationService::getJavaUniversities();
    expect($universities)->toBeArray();
    expect(count($universities))->toBeGreaterThanOrEqual(25);

    $provinces = array_unique(array_column($universities, 'province'));
    expect($provinces)->toContain('Jawa Barat');
    expect($provinces)->toContain('DKI Jakarta');
    expect($provinces)->toContain('Banten');
    expect($provinces)->toContain('Jawa Tengah');
    expect($provinces)->toContain('DI Yogyakarta');
    expect($provinces)->toContain('Jawa Timur');

    // Check specific notable universities
    $names = array_column($universities, 'name');
    expect(collect($names)->some(fn ($n) => str_contains($n, 'Institut Teknologi Bandung')))->toBeTrue();
    expect(collect($names)->some(fn ($n) => str_contains($n, 'Universitas Indonesia')))->toBeTrue();
    expect(collect($names)->some(fn ($n) => str_contains($n, 'Universitas Gadjah Mada')))->toBeTrue();
    expect(collect($names)->some(fn ($n) => str_contains($n, 'Universitas Airlangga')))->toBeTrue();
    expect(collect($names)->some(fn ($n) => str_contains($n, 'Institut Teknologi Sepuluh Nopember')))->toBeTrue();

    // Check standard majors
    $standardMajors = HigherEducationService::getAllStandardMajors();
    expect($standardMajors)->toBeArray();
    expect($standardMajors)->toHaveKey('Teknologi Informasi & Komputer');
    expect($standardMajors)->toHaveKey('Kedokteran & Kesehatan');
    expect($standardMajors)->toHaveKey('Teknik & Rekayasa');
});

test('bk create counseling view renders campus and major searchable comboboxes', function () {
    $response = $this->actingAs($this->bkTeacher)->get(route('bk.counseling.create'));

    $response->assertStatus(200);
    $response->assertSee('campus-combobox-wrapper', false);
    $response->assertSee('major-combobox-wrapper', false);
    $response->assertSee('campus_datalist', false);
    $response->assertSee('major_datalist', false);
    $response->assertSee('Institut Teknologi Bandung (ITB)', false);
    $response->assertSee('Universitas Indonesia', false);
    $response->assertSee('Cari universitas atau ketik manual...', false);
});

test('bk edit counseling view renders campus and major searchable comboboxes with existing values', function () {
    $session = BkCounselingSession::create([
        'session_code' => 'BK-TEST-CAMPUS-'.rand(1000, 9999),
        'student_id' => $this->student->id,
        'counselor_id' => $this->bkTeacher->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'KARIER',
        'session_type' => 'INDIVIDUAL',
        'service_category' => 'LAYANAN_RESPONSIF',
        'place' => 'Ruang BK',
        'topic' => 'Pilihan Kampus SNBP',
        'problem_description' => 'Konsultasi prodi prioritas.',
        'career_path' => 'KULIAH',
        'study_admission_track' => 'SNBP',
        'campus_name' => 'Universitas Padjadjaran (Unpad)',
        'study_program' => 'Pendidikan Dokter (Kedokteran)',
        'status' => 'SELESAI',
    ]);

    $response = $this->actingAs($this->bkTeacher)->get(route('bk.counseling.edit', $session));

    $response->assertStatus(200);
    $response->assertSee('Universitas Padjadjaran (Unpad)', false);
    $response->assertSee('Pendidikan Dokter (Kedokteran)', false);
    $response->assertSee('campus-combobox-wrapper', false);
    $response->assertSee('major-combobox-wrapper', false);
});

test('can store career counseling session with manual custom campus and major', function () {
    $payload = [
        'student_id' => $this->student->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'KARIER',
        'session_type' => 'INDIVIDUAL',
        'service_category' => 'PERENCANAAN_INDIVIDUAL',
        'place' => 'Ruang BK',
        'start_time' => '08:30',
        'end_time' => '09:30',
        'topic' => 'Minat Studi Lanjut Militer & Siber',
        'problem_description' => 'Siswa berencana mendaftar ke universitas pertahanan jalur beasiswa ikatan dinas.',
        'career_path' => 'KULIAH',
        'study_admission_track' => 'KEDINASAN',
        'campus_name' => 'Universitas Pertahanan RI (Unhan)',
        'study_program' => 'Teknik Rekayasa Perangkat Lunak Pertahanan',
        'status' => 'SELESAI',
    ];

    $response = $this->actingAs($this->bkTeacher)->post(route('bk.counseling.store'), $payload);

    $session = BkCounselingSession::latest('id')->first();
    $response->assertRedirect(route('bk.counseling.show', $session));

    expect($session->campus_name)->toBe('Universitas Pertahanan RI (Unhan)');
    expect($session->study_program)->toBe('Teknik Rekayasa Perangkat Lunak Pertahanan');
    expect($session->study_admission_track)->toBe('KEDINASAN');
});

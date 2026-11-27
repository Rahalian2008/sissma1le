<?php

use App\Models\BkCounselingSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFamily;
use App\Models\User;

test('bk counselor can view student directory in bk and access buku induk', function () {
    $counselor = User::factory()->create([
        'role' => 'guru_bk',
    ]);

    $class = SchoolClass::firstOrCreate(
        ['name' => 'XII MIPA 1'],
        ['grade' => 'XII', 'academic_year' => '2025/2026', 'is_active' => true]
    );

    $student = Student::create([
        'name' => 'Aditya Pratama Siswa',
        'nis' => '24251001',
        'nisn' => '0061234567',
        'gender' => 'L',
        'class_id' => $class->id,
        'current_class_level' => 'XII',
        'is_active' => true,
    ]);

    StudentFamily::create([
        'student_id' => $student->id,
        'father_name' => 'Bambang Pratama',
        'mother_name' => 'Sri Wahyuni',
        'father_occupation' => 'PNS',
        'mother_occupation' => 'Wiraswasta',
    ]);

    $this->actingAs($counselor)
        ->get(route('bk.students.index'))
        ->assertStatus(200)
        ->assertSee('Aditya Pratama Siswa')
        ->assertSee('24251001')
        ->assertSee('XII MIPA 1')
        ->assertSee('Cetak Induk');

    $this->actingAs($counselor)
        ->get(route('bk.students.buku-induk.show', $student))
        ->assertStatus(200)
        ->assertSee('Lembar Buku Induk Siswa Digital')
        ->assertSee('Aditya Pratama Siswa')
        ->assertSee('Bambang Pratama');

    $response = $this->actingAs($counselor)
        ->get(route('bk.students.buku-induk.print', $student));

    $response->assertStatus(200);
    $response->assertSee('LEMBAR BUKU INDUK SISWA');
    $response->assertSee('A. KETERANGAN TENTANG DIRI PESERTA DIDIK');
    $response->assertSee('B. KETERANGAN PENERIMAAN');
    $response->assertSee('C. KETERANGAN ORANG TUA KANDUNG');
    $response->assertSee('D. REKAPITULASI KEHADIRAN');
    $response->assertSee('E. CATATAN PRESTASI DAN PENGHARGAAN');
    $response->assertSee('F. REKAM JEJAK BIMBINGAN KONSELING');
    $response->assertSee('Aditya Pratama Siswa');
    $response->assertSee('Bambang Pratama');
});

test('profile 360 includes print buku induk button for counselor', function () {
    $counselor = User::factory()->create([
        'role' => 'koordinator_bk',
    ]);

    $class = SchoolClass::firstOrCreate(
        ['name' => 'XI IPS 2'],
        ['grade' => 'XI', 'academic_year' => '2025/2026', 'is_active' => true]
    );

    $student = Student::create([
        'name' => 'Nadia Rahmawati Siswi',
        'nis' => '24251002',
        'nisn' => '0061234568',
        'gender' => 'P',
        'class_id' => $class->id,
        'current_class_level' => 'XI',
        'is_active' => true,
    ]);

    $this->actingAs($counselor)
        ->get(route('bk.student.profile360', $student))
        ->assertStatus(200)
        ->assertSee('Cetak Buku Induk')
        ->assertSee(route('bk.students.buku-induk.print', $student));
});

test('kiosk tv json data endpoint provides real-time updated bk stats', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'XII MIPA 2'],
        ['grade' => 'XII', 'academic_year' => '2025/2026', 'is_active' => true]
    );

    $student = Student::create([
        'name' => 'Rian Hidayat Konseli',
        'nis' => '24251003',
        'nisn' => '0061234569',
        'gender' => 'L',
        'class_id' => $class->id,
        'current_class_level' => 'XII',
        'is_active' => true,
    ]);

    $counselor = User::factory()->create([
        'role' => 'guru_bk',
        'name' => 'Guru Konselor Tes',
    ]);

    BkCounselingSession::create([
        'student_id' => $student->id,
        'counselor_id' => $counselor->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'KARIER',
        'service_type' => 'Konseling Individual',
        'status' => 'SELESAI',
        'topic' => 'Persiapan SNBP dan Jurusan Teknik Informatika',
        'career_path' => 'KULIAH',
        'career_target_university' => 'Institut Teknologi Bandung',
        'career_target_major' => 'Teknik Informatika',
        'career_admission_track' => 'SNBP',
    ]);

    $response = $this->get(route('kiosk.data'));
    $response->assertStatus(200);

    $json = $response->json();
    expect($json)->toHaveKey('bk');
    expect($json['bk'])->toHaveKey('total_sessions');
    expect($json['bk'])->toHaveKey('sessions_today');
    expect($json['bk'])->toHaveKey('career_kuliah_count');
    expect($json['bk'])->toHaveKey('fields');
    expect($json['bk'])->toHaveKey('counselors');
    expect($json['bk']['sessions_today'])->toBeGreaterThanOrEqual(1);
    expect($json['bk']['career_kuliah_count'])->toBeGreaterThanOrEqual(1);
});

test('kiosk tv html page renders live bk slide with real-time badges', function () {
    $response = $this->get(route('kiosk.index'));
    $response->assertStatus(200);
    $response->assertSee('Bimbingan &amp; Konseling (BK)', false);
    $response->assertSee('bkSessionsToday');
    $response->assertSee('bkTotalSessions');
    $response->assertSee('bkCareerKuliah');
    $response->assertSee('bkLiveSummary');
    $response->assertSee('LIVE REAL-TIME');
});

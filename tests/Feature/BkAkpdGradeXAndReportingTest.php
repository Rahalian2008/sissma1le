<?php

use App\Models\BkAkpd;
use App\Models\BkAkpdQuestion;
use App\Models\BkAkpdResponse;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->classX = SchoolClass::firstOrCreate(
        ['name' => 'X Merdeka 1'],
        ['grade' => 'X', 'major' => 'Umum']
    );

    $this->classXI = SchoolClass::firstOrCreate(
        ['name' => 'XI Merdeka 1'],
        ['grade' => 'XI', 'major' => 'MIPA']
    );

    // Siswa Kelas X
    $this->userStudentX = User::factory()->create([
        'role' => 'siswa',
    ]);
    $this->studentX = Student::create([
        'user_id' => $this->userStudentX->id,
        'nis' => '990001',
        'nisn' => '0099000001',
        'name' => 'Siswa Kelas X Uji',
        'class_id' => $this->classX->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    // Siswa Kelas XI
    $this->userStudentXI = User::factory()->create([
        'role' => 'siswa',
    ]);
    $this->studentXI = Student::create([
        'user_id' => $this->userStudentXI->id,
        'nis' => '990002',
        'nisn' => '0099000002',
        'name' => 'Siswa Kelas XI Uji',
        'class_id' => $this->classXI->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    // Guru BK
    $this->counselor = User::factory()->create([
        'role' => 'guru_bk',
    ]);

    // Instrumen AKPD
    $this->akpd = BkAkpd::firstOrCreate(
        ['title' => 'Instrumen AKPD Standar POP-BK SMA (Fase E)'],
        [
            'grade_level' => 'X',
            'description' => 'Instrumen AKPD Fase E',
            'total_items' => 4,
            'is_active' => true,
        ]
    );

    // Pastikan butir pertanyaan ada
    $this->qPribadi = BkAkpdQuestion::firstOrCreate(
        ['akpd_id' => $this->akpd->id, 'code' => 'P01'],
        [
            'field_of_service' => 'PRIBADI',
            'statement' => 'Merasa belum mampu mengendalikan emosi dengan stabil.',
            'profile_dimension' => 'Kematangan Emosi',
            'sort_order' => 1,
        ]
    );

    $this->qBelajar = BkAkpdQuestion::firstOrCreate(
        ['akpd_id' => $this->akpd->id, 'code' => 'B01'],
        [
            'field_of_service' => 'BELAJAR',
            'statement' => 'Sering menunda-nunda tugas sekolah hingga menumpuk.',
            'profile_dimension' => 'Keterampilan Belajar',
            'sort_order' => 2,
        ]
    );
});

test('siswa kelas X dapat mengakses index dan mengisi AKPD', function () {
    $response = $this->actingAs($this->userStudentX)->get(route('bk.akpd.index'));
    $response->assertStatus(200);
    $response->assertSee('Angket Kebutuhan Peserta Didik (AKPD)');

    $takeResponse = $this->actingAs($this->userStudentX)->get(route('bk.akpd.take', $this->akpd));
    $takeResponse->assertStatus(200);
    $takeResponse->assertSee($this->studentX->name);
});

test('siswa kelas XI atau XII dilarang mengakses AKPD dan dialihkan dengan peringatan', function () {
    $response = $this->actingAs($this->userStudentXI)->get(route('bk.akpd.index'));
    $response->assertRedirect(route('bk.psychological.index'));
    $response->assertSessionHas('warning');

    $takeResponse = $this->actingAs($this->userStudentXI)->get(route('bk.akpd.take', $this->akpd));
    $takeResponse->assertRedirect(route('bk.psychological.index'));
    $takeResponse->assertSessionHas('warning');
});

test('siswa kelas X yang telah mengisi AKPD diarahkan ke laporan individu', function () {
    // Submit respon AKPD
    $submitResponse = $this->actingAs($this->userStudentX)->post(route('bk.akpd.submit', $this->akpd), [
        'student_id' => $this->studentX->id,
        'selected_questions' => [$this->qPribadi->id, $this->qBelajar->id],
    ]);

    $submitResponse->assertRedirect(route('bk.akpd.individual', [$this->akpd, $this->studentX]));

    // Akses laporan individu
    $indivResponse = $this->actingAs($this->userStudentX)->get(route('bk.akpd.individual', [$this->akpd, $this->studentX]));
    $indivResponse->assertStatus(200);
    $indivResponse->assertSee('Profil Kebutuhan Peserta Didik (AKPD)');
    $indivResponse->assertSee($this->studentX->name);
    $indivResponse->assertSee('Kematangan Emosi');

    // Akses cetak individu
    $printResponse = $this->actingAs($this->userStudentX)->get(route('bk.akpd.print-individual', [$this->akpd, $this->studentX]));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('Laporan Hasil Asesmen Kebutuhan Peserta Didik (AKPD) Individual');
    $printResponse->assertSee('Koordinator BK');
    $printResponse->assertSee('Kepala');
});

test('guru BK dapat melihat laporan profil kelas dan mencetak laporan kelas', function () {
    // Buat respon siswa X
    BkAkpdResponse::updateOrCreate(
        ['akpd_id' => $this->akpd->id, 'student_id' => $this->studentX->id],
        [
            'selected_question_ids' => [$this->qPribadi->id],
            'score_per_field' => [
                'PRIBADI' => ['count' => 1, 'total_field' => 10, 'percentage' => 10],
                'SOSIAL' => ['count' => 0, 'total_field' => 10, 'percentage' => 0],
                'BELAJAR' => ['count' => 0, 'total_field' => 10, 'percentage' => 0],
                'KARIER' => ['count' => 0, 'total_field' => 10, 'percentage' => 0],
            ],
            'highest_need_field' => 'PRIBADI',
            'submitted_at' => now(),
        ]
    );

    // Akses rekap kelas
    $classReportResponse = $this->actingAs($this->counselor)->get(route('bk.akpd.class-report', [$this->akpd, 'class_id' => $this->classX->id]));
    $classReportResponse->assertStatus(200);
    $classReportResponse->assertSee('Profil Kebutuhan Kelas');
    $classReportResponse->assertSee($this->classX->name);

    // Akses cetak rekap kelas
    $printClassResponse = $this->actingAs($this->counselor)->get(route('bk.akpd.print-class', [$this->akpd, 'class_id' => $this->classX->id]));
    $printClassResponse->assertStatus(200);
    $printClassResponse->assertSee('Laporan Profil Kebutuhan Peserta Didik (AKPD) Tingkat Kelas');
    $printClassResponse->assertSee('Wali Kelas');
    $printClassResponse->assertSee('Koordinator BK');
});

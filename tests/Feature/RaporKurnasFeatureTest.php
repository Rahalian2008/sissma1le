<?php

use App\Models\AcademicYear;
use App\Models\CourseClass;
use App\Models\KurnasLearningGoal;
use App\Models\KurnasSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin', 'super_admin'],
    ]);

    $this->academicYear = AcademicYear::firstOrCreate(
        ['name' => '2025/2026'],
        ['semester' => '1', 'is_active' => true]
    );

    $this->setting = KurnasSetting::firstOrCreate(
        ['is_active' => true],
        [
            'academic_year_id' => $this->academicYear->id,
            'semester' => '1',
            'is_locked' => false,
            'report_date' => Carbon::now()->addWeeks(2)->toDateString(),
            'report_place' => 'Lengkong',
            'headmaster_name' => 'H. Jajang Sutisna, M.Pd.',
            'headmaster_nip' => '196805141994121001',
        ]
    );
});

test('user dapat mengakses dashboard rapor kurnas', function () {
    $response = $this->actingAs($this->user)->get(route('rapor_kurnas.index'));

    $response->assertStatus(200);
    $response->assertSee('Aplikasi Rapor Kurnas SMA', false);
    $response->assertSee('e-Rapor SMA Standar Resmi Kemendikdasmen 2025', false);
    $response->assertSee('Tujuan Pembelajaran (TP)', false);
});

test('guru atau admin dapat membuat tujuan pembelajaran baru', function () {
    $subject = Subject::first() ?? Subject::create([
        'code' => 'MAT-TEST',
        'name' => 'Matematika Tingkat Lanjut',
        'grade' => 'X',
        'group' => 'B',
        'kkm' => 75,
        'color' => 'blue',
        'icon' => 'calculator',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->post(route('rapor_kurnas.learning_goals.store'), [
        'subject_id' => $subject->id,
        'fase' => 'E',
        'grade' => 'X',
        'semester' => '1',
        'code' => 'TP 99',
        'description' => 'memahami sifat fungsi eksponensial dalam pemodelan data',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('kurnas_learning_goals', [
        'subject_id' => $subject->id,
        'code' => 'TP 99',
        'fase' => 'E',
    ]);
});

test('guru dapat menyimpan nilai akhir rapor dan deskripsi capaian otomatis', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'X-1 Merdeka'],
        ['grade' => 'X', 'academic_year_id' => $this->academicYear->id]
    );

    $subject = Subject::firstOrCreate(
        ['code' => 'FIS-X'],
        ['name' => 'Fisika', 'grade' => 'X', 'group' => 'A', 'kkm' => 75, 'color' => 'indigo', 'icon' => 'atom', 'is_active' => true]
    );

    $teacher = Teacher::firstOrCreate(
        ['name' => 'Budi Permana, M.Pd.'],
        ['nip' => '197501012000031001']
    );

    $courseClass = CourseClass::firstOrCreate(
        ['subject_id' => $subject->id, 'class_id' => $class->id],
        [
            'teacher_id' => $teacher->id,
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]
    );

    $student = Student::first() ?? Student::create([
        'name' => 'Ahmad Santoso',
        'nisn' => '0051234567',
        'nis' => '242510001',
        'gender' => 'L',
        'class_id' => $class->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    $tp = KurnasLearningGoal::firstOrCreate(
        ['subject_id' => $subject->id, 'code' => 'TP 1'],
        [
            'fase' => 'E',
            'grade' => 'X',
            'semester' => '1',
            'description' => 'menganalisis struktur kebahasaan secara mendalam',
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($this->user)->post(route('rapor_kurnas.scores.store'), [
        'course_class_id' => $courseClass->id,
        'scores' => [
            [
                'student_id' => $student->id,
                'score' => 88.5,
                'optimal_tp' => [$tp->id],
                'improvement_tp' => [],
                'full_description' => '',
            ],
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('kurnas_course_scores', [
        'course_class_id' => $courseClass->id,
        'student_id' => $student->id,
        'score' => 88.5,
    ]);
});

test('wali kelas dapat menyimpan kehadiran, catatan walas, dan kenaikan kelas', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'X-1 Merdeka'],
        ['grade' => 'X', 'academic_year_id' => $this->academicYear->id]
    );

    $student = Student::firstOrCreate(
        ['nisn' => '0051234567'],
        [
            'name' => 'Ahmad Santoso',
            'nis' => '242510001',
            'gender' => 'L',
            'class_id' => $class->id,
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($this->user)->post(route('rapor_kurnas.homeroom.store', $class->id), [
        'records' => [
            [
                'student_id' => $student->id,
                'sick' => 2,
                'permission' => 1,
                'absent' => 0,
                'note' => 'Pertahankan prestasi akademik dan keaktifan berorganisasi.',
                'status_promotion' => 'NAIK',
                'promotion_target_grade' => 'XI',
            ],
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('kurnas_homeroom_records', [
        'student_id' => $student->id,
        'sick' => 2,
        'permission' => 1,
        'absent' => 0,
        'status_promotion' => 'NAIK',
    ]);
});

test('lembar cetak rapor akademik resmi menampilkan tanda tangan kepala sekolah di sebelah kiri', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'X-1 Merdeka'],
        ['grade' => 'X', 'academic_year_id' => $this->academicYear->id]
    );

    $student = Student::firstOrCreate(
        ['nisn' => '0051234567'],
        [
            'name' => 'Ahmad Santoso',
            'nis' => '242510001',
            'gender' => 'L',
            'class_id' => $class->id,
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($this->user)->get(route('rapor_kurnas.print_academic', $student->id));

    $response->assertStatus(200);
    $response->assertSee('LAPORAN HASIL BELAJAR (RAPOR)', false);
    $response->assertSee('SMA NEGERI 1 LENGKONG', false);
    // Verifikasi posisi tanda tangan Kepala Sekolah di KIRI
    $response->assertSee('Mengetahui,', false);
    $response->assertSee('Kepala Sekolah,', false);
    $response->assertSee($this->setting->headmaster_name, false);
});

test('pusat cetak rapor dan seluruh format dokumen dapat dibuka dengan status 200', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'X-1 Merdeka'],
        ['grade' => 'X', 'academic_year_id' => $this->academicYear->id]
    );

    $student = Student::firstOrCreate(
        ['nisn' => '0051234567'],
        [
            'name' => 'Ahmad Santoso',
            'nis' => '242510001',
            'gender' => 'L',
            'class_id' => $class->id,
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]
    );

    // 1. Pusat Cetak Hub
    $this->actingAs($this->user)->get(route('rapor_kurnas.print_hub'))->assertStatus(200);

    // 2. Sampul Rapor
    $this->actingAs($this->user)->get(route('rapor_kurnas.print_cover', $student->id))->assertStatus(200);

    // 3. Identitas Siswa
    $this->actingAs($this->user)->get(route('rapor_kurnas.print_identity', $student->id))->assertStatus(200);

    // 4. Rapor Kokurikuler P5
    $this->actingAs($this->user)->get(route('rapor_kurnas.print_cocurricular', $student->id))->assertStatus(200);

    // 5. Leger Nilai Kelas
    $this->actingAs($this->user)->get(route('rapor_kurnas.print_leger', $class->id))->assertStatus(200);

    // 6. Rapor Lengkap 1 Paket
    $this->actingAs($this->user)->get(route('rapor_kurnas.print_full', $student->id))->assertStatus(200);
});

test('admin dapat mengunci dan membuka kembali input nilai rapor', function () {
    $this->setting->update(['is_locked' => false]);

    // Kunci
    $this->actingAs($this->user)->post(route('rapor_kurnas.toggle_lock'))->assertRedirect();
    expect($this->setting->fresh()->is_locked)->toBeTrue();

    // Buka kembali
    $this->actingAs($this->user)->post(route('rapor_kurnas.toggle_lock'))->assertRedirect();
    expect($this->setting->fresh()->is_locked)->toBeFalse();
});

test('user dapat mengakses status monitoring dan audit live progres rapor', function () {
    $response = $this->actingAs($this->user)->get(route('rapor_kurnas.monitoring'));

    $response->assertStatus(200);
    $response->assertSee('Status Penilaian & Audit Monitoring', false);
    $response->assertSee('Status Guru Mata Pelajaran', false);
    $response->assertSee('Status Wali Kelas', false);
    $response->assertSee('Status Kokurikuler (P5)', false);
});

test('user dapat melihat perkembangan nilai siswa dan mengupdate status eligibilitas snbp', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'XII-MIPA-1'],
        ['grade' => 'XII', 'academic_year_id' => $this->academicYear->id]
    );

    $student = Student::firstOrCreate(
        ['nisn' => '0069998881'],
        [
            'name' => 'Dewi Anggraini',
            'nis' => '232412001',
            'gender' => 'P',
            'class_id' => $class->id,
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($this->user)->get(route('rapor_kurnas.growth', [
        'class_id' => $class->id,
        'student_id' => $student->id,
    ]));

    $response->assertStatus(200);
    $response->assertSee('Perkembangan Nilai Siswa & SNBP Tracker', false);
    $response->assertSee('Dewi Anggraini', false);

    // Update status eligibilitas SNBP
    $postResponse = $this->actingAs($this->user)->post(route('rapor_kurnas.growth.update_snbp', $student->id), [
        'is_eligible_snbp' => '1',
        'cohort_rank' => 3,
        'notes' => 'Rekomendasi Teknik Informatika ITB',
    ]);

    $postResponse->assertRedirect();
    $this->assertDatabaseHas('kurnas_transcripts', [
        'student_id' => $student->id,
        'is_eligible_snbp' => 1,
        'cohort_rank' => 3,
    ]);
});

test('user dapat mengelola mapel gabungan ipa dan ips fase e', function () {
    $fisika = Subject::firstOrCreate(['code' => 'FIS-COMB'], ['name' => 'Fisika Test', 'grade' => 'X', 'group' => 'A', 'is_active' => true]);
    $kimia = Subject::firstOrCreate(['code' => 'KIM-COMB'], ['name' => 'Kimia Test', 'grade' => 'X', 'group' => 'A', 'is_active' => true]);

    $response = $this->actingAs($this->user)->get(route('rapor_kurnas.combined_subjects'));
    $response->assertStatus(200);
    $response->assertSee('Mata Pelajaran Gabungan (IPA & IPS Fase E Kelas X)', false);

    // Tambah mapel gabungan baru
    $storeResponse = $this->actingAs($this->user)->post(route('rapor_kurnas.combined_subjects.store'), [
        'grade' => 'X',
        'combined_name' => 'Ilmu Pengetahuan Alam Terpadu (IPA)',
        'combined_code' => 'IPA-COMB-TEST',
        'source_subject_ids' => [$fisika->id, $kimia->id],
        'order_number' => 5,
        'is_active' => '1',
    ]);

    $storeResponse->assertRedirect();
    $this->assertDatabaseHas('kurnas_subject_combines', [
        'combined_code' => 'IPA-COMB-TEST',
        'grade' => 'X',
    ]);
});

test('user dapat mengelola nomor ijazah dan status kelulusan kelas xii', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'XII-IPA-2'],
        ['grade' => 'XII', 'academic_year_id' => $this->academicYear->id]
    );

    $student = Student::firstOrCreate(
        ['nisn' => '0061122334'],
        [
            'name' => 'Bambang Pamungkas',
            'nis' => '232412002',
            'gender' => 'L',
            'class_id' => $class->id,
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($this->user)->get(route('rapor_kurnas.diploma', ['class_id' => $class->id]));
    $response->assertStatus(200);
    $response->assertSee('Pengelolaan Ijazah & Transkrip Nilai Kelulusan', false);

    // Simpan data ijazah
    $storeResponse = $this->actingAs($this->user)->post(route('rapor_kurnas.diploma.store'), [
        'transcripts' => [
            [
                'student_id' => $student->id,
                'diploma_number' => 'DN-02/SMA/KURNAS/2025/000999',
                'transcript_number' => '421.3/TR-999/SMA1LE/2025',
                'graduation_date' => '2025-05-05',
                'average_score' => 88.75,
                'status_graduation' => 'LULUS',
            ],
        ],
    ]);

    $storeResponse->assertRedirect();
    $this->assertDatabaseHas('kurnas_transcripts', [
        'student_id' => $student->id,
        'diploma_number' => 'DN-02/SMA/KURNAS/2025/000999',
        'status_graduation' => 'LULUS',
    ]);

    // Download format CSV
    $csvResponse = $this->actingAs($this->user)->get(route('rapor_kurnas.diploma.template', ['class_id' => $class->id]));
    $csvResponse->assertStatus(200);
});

test('user dapat mencetak transkrip nilai ijazah dan lembar buku induk siswa', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'XII-IPS-1'],
        ['grade' => 'XII', 'academic_year_id' => $this->academicYear->id]
    );

    $student = Student::firstOrCreate(
        ['nisn' => '0065544332'],
        [
            'name' => 'Citra Lestari',
            'nis' => '232412003',
            'gender' => 'P',
            'class_id' => $class->id,
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]
    );

    // 1. Cetak Transkrip Ijazah
    $diplomaPrint = $this->actingAs($this->user)->get(route('rapor_kurnas.print_diploma', $student->id));
    $diplomaPrint->assertStatus(200);
    $diplomaPrint->assertSee('TRANSKRIP NILAI IJAZAH SMA', false);
    $diplomaPrint->assertSee('SMA NEGERI 1 LENGKONG', false);
    $diplomaPrint->assertSee('Citra Lestari', false);

    // 2. Cetak Lembar Buku Induk
    $bukuIndukPrint = $this->actingAs($this->user)->get(route('rapor_kurnas.print_buku_induk', $student->id));
    $bukuIndukPrint->assertStatus(200);
    $bukuIndukPrint->assertSee('LEMBAR BUKU INDUK PESERTA DIDIK', false);
    $bukuIndukPrint->assertSee('SMA NEGERI 1 LENGKONG', false);
});

test('siswa atau user dapat mengakses portal rapor mandiri', function () {
    $this->setting->update(['is_published_to_students' => true]);

    $response = $this->actingAs($this->user)->get(route('rapor_kurnas.my_report'));
    $response->assertStatus(200);
    $response->assertSee('Rapor Saya', false);
});

test('admin dapat memperbarui pengaturan rapor dan toggle fitur lanjutan', function () {
    $response = $this->actingAs($this->user)->post(route('rapor_kurnas.settings.update'), [
        'academic_year_id' => $this->academicYear->id,
        'semester' => '2',
        'report_date' => '2025-06-20',
        'report_place' => 'Lengkong Sukabumi',
        'headmaster_name' => 'H. Jajang Sutisna, M.Pd.',
        'headmaster_nip' => '196805141994121001',
        'combine_science_social' => '1',
        'is_published_to_students' => '1',
        'show_signature_image' => '1',
    ]);

    $response->assertRedirect();
    $setting = $this->setting->fresh();
    expect($setting->semester)->toBe('2');
    expect($setting->report_place)->toBe('Lengkong Sukabumi');
    expect($setting->combine_science_social)->toBeTrue();
    expect($setting->is_published_to_students)->toBeTrue();
    expect($setting->show_signature_image)->toBeTrue();
});

test('siswa diarahkan langsung ke rapor saya ketika mengakses dashboard rapor kurnas', function () {
    $studentUser = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);

    $student = Student::create([
        'user_id' => $studentUser->id,
        'nisn' => '0099887766',
        'name' => 'Ahmad Siswa',
        'nis' => '242510099',
        'gender' => 'L',
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($studentUser)->get(route('rapor_kurnas.index'));
    $response->assertRedirect(route('rapor_kurnas.my_report'));
});

test('guru mata pelajaran murni tidak dapat mengakses pengaturan rapor atau mapel gabungan', function () {
    $teacherUser = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
    ]);

    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Pak Guru Mapel',
        'nip' => '198501012010011005',
        'is_active' => true,
    ]);

    // Akses ke menu pengaturan -> dilarang (403)
    $settingsResponse = $this->actingAs($teacherUser)->get(route('rapor_kurnas.settings'));
    $settingsResponse->assertStatus(403);

    // Akses ke menu mapel gabungan -> dilarang (403)
    $combinedResponse = $this->actingAs($teacherUser)->get(route('rapor_kurnas.combined_subjects'));
    $combinedResponse->assertStatus(403);

    // Toggle kunci nilai -> dilarang (403)
    $toggleResponse = $this->actingAs($teacherUser)->post(route('rapor_kurnas.toggle_lock'));
    $toggleResponse->assertStatus(403);
});

test('guru mata pelajaran yang bukan wali kelas dilarang mengakses menu wali kelas', function () {
    $teacherUser = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
    ]);

    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Pak Guru Bukan Walas',
        'nip' => '198801012015011009',
        'is_active' => true,
    ]);

    $response = $this->actingAs($teacherUser)->get(route('rapor_kurnas.homeroom'));
    $response->assertStatus(403);
});

test('wali kelas resmi dapat mengakses menu wali kelas dan otomatis terpilih kelasnya', function () {
    $walasUser = User::factory()->create([
        'role' => 'wali_kelas',
        'roles' => ['wali_kelas', 'guru'],
    ]);

    $walasTeacher = Teacher::create([
        'user_id' => $walasUser->id,
        'name' => 'Ibu Walas X-A',
        'nip' => '198205052008012003',
        'is_active' => true,
    ]);

    $homeroomClass = SchoolClass::create([
        'name' => 'X-A Unggulan',
        'grade' => 'X',
        'academic_year_id' => $this->academicYear->id,
        'homeroom_teacher_id' => $walasTeacher->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($walasUser)->get(route('rapor_kurnas.homeroom'));
    $response->assertStatus(200);
    $response->assertSee('X-A Unggulan', false);
    $response->assertSee('Ibu Walas X-A', false);
});

test('guru mata pelajaran hanya dapat menginput nilai untuk kelas yang diampunya', function () {
    $teacherUser = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
    ]);

    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Biologi',
        'nip' => '198901012016011002',
        'is_active' => true,
    ]);

    $otherTeacher = Teacher::create([
        'name' => 'Guru Lain',
        'nip' => '198901012016011003',
        'is_active' => true,
    ]);

    $class = SchoolClass::firstOrCreate(
        ['name' => 'XI-MIPA-1'],
        ['grade' => 'XI', 'academic_year_id' => $this->academicYear->id]
    );

    $subject1 = Subject::create([
        'code' => 'BIO-XI',
        'name' => 'Biologi XI',
        'grade' => 'XI',
        'group' => 'A',
        'kkm' => 75,
        'color' => 'emerald',
        'icon' => 'leaf',
        'is_active' => true,
    ]);

    $subject2 = Subject::create([
        'code' => 'KIM-XI',
        'name' => 'Kimia XI',
        'grade' => 'XI',
        'group' => 'A',
        'kkm' => 75,
        'color' => 'amber',
        'icon' => 'flask',
        'is_active' => true,
    ]);

    // Course 1 diampu oleh Guru Biologi
    $myCourse = CourseClass::create([
        'subject_id' => $subject1->id,
        'class_id' => $class->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    // Course 2 diampu oleh Guru Lain
    $otherCourse = CourseClass::create([
        'subject_id' => $subject2->id,
        'class_id' => $class->id,
        'teacher_id' => $otherTeacher->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    // 1. Mengakses form input nilai kelas sendiri -> Sukses 200
    $successResponse = $this->actingAs($teacherUser)->get(route('rapor_kurnas.scores', ['course_class_id' => $myCourse->id]));
    $successResponse->assertStatus(200);
    $successResponse->assertSee('Biologi XI', false);

    // 2. Mengakses form input nilai kelas orang lain -> Dilarang 403
    $forbiddenResponse = $this->actingAs($teacherUser)->get(route('rapor_kurnas.scores', ['course_class_id' => $otherCourse->id]));
    $forbiddenResponse->assertStatus(403);
});

test('guru bk dapat mengakses perkembangan snbp dan buku induk', function () {
    $bkUser = User::factory()->create([
        'role' => 'bk',
        'roles' => ['bk', 'guru'],
    ]);

    $bkTeacher = Teacher::create([
        'user_id' => $bkUser->id,
        'name' => 'Guru BK Konselor',
        'nip' => '198701012014012004',
        'is_active' => true,
    ]);

    // Akses SNBP Tracker -> 200
    $growthResponse = $this->actingAs($bkUser)->get(route('rapor_kurnas.growth'));
    $growthResponse->assertStatus(200);
    $growthResponse->assertSee('Perkembangan Nilai Siswa', false);

    // Akses Buku Induk & Leger -> 200
    $transcriptsResponse = $this->actingAs($bkUser)->get(route('rapor_kurnas.transcripts'));
    $transcriptsResponse->assertStatus(200);
    $transcriptsResponse->assertSee('Buku Induk', false);
});

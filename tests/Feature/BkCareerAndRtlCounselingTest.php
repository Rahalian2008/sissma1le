<?php

use App\Models\BkCounselingSession;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->bkTeacher = User::where('role', 'guru_bk')->orWhere('role', 'bk')->orWhere('role', 'koordinator_bk')->first();
    if (! $this->bkTeacher) {
        $this->bkTeacher = User::create([
            'name' => 'Guru BK Test',
            'username' => 'gurubktest',
            'email' => 'gurubktest@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru_bk',
            'is_active' => true,
        ]);
    }

    $this->coordinator = User::where('role', 'koordinator_bk')->first();
    if (! $this->coordinator) {
        $this->coordinator = User::create([
            'name' => 'Koordinator BK Test',
            'username' => 'koordinatortest',
            'email' => 'koordinatortest@example.com',
            'password' => bcrypt('password'),
            'role' => 'koordinator_bk',
            'is_active' => true,
        ]);
    }

    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'XII-MIPA-1', 'level' => 'XII']);
    $this->student = Student::first();
    if (! $this->student) {
        $this->student = Student::create([
            'name' => 'Ahmad Siswa Test BK',
            'nisn' => '0099887766',
            'class_id' => $class->id,
            'gender' => 'L',
            'is_active' => true,
        ]);
    }
});

test('bk create form renders dynamic career fields and rtl date picker', function () {
    $response = $this->actingAs($this->bkTeacher)->get(route('bk.counseling.create'));

    $response->assertStatus(200);
    $response->assertSee('Bimbingan Karier', false);
    $response->assertSee('career_guidance_panel', false);
    $response->assertSee('Kuliah (Perguruan Tinggi)', false);
    $response->assertSee('Bekerja / Wirausaha', false);
    $response->assertSee('follow_up_date', false);
    $response->assertSee('Rencana Tindak Lanjut (RTL)', false);
});

test('can store career guidance session for college path with notifications', function () {
    $followUpDate = now()->addDays(5)->toDateString();

    $payload = [
        'student_id' => $this->student->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'KARIER',
        'session_type' => 'INDIVIDUAL',
        'service_category' => 'LAYANAN_RESPONSIF',
        'place' => 'Ruang Bimbingan Konseling',
        'start_time' => '09:00',
        'end_time' => '10:00',
        'topic' => 'Konsultasi Pemilihan Program Studi SNBP 2027',
        'problem_description' => 'Siswa bimbang memilih antara Teknik Informatika dan Sistem Informasi di PTN.',
        'counseling_approach' => 'Solusi Terfokus / Trait and Factor',
        'agreement_result' => 'Siswa mantap memilih Teknik Informatika ITB sebagai pilihan 1 dan Unpad pilihan 2.',
        'follow_up_plan' => 'Pertemuan berkala untuk memantau nilai rapor semester 5 dan portofolio.',
        'follow_up_date' => $followUpDate,
        'career_path' => 'KULIAH',
        'study_admission_track' => 'SNBP',
        'campus_name' => 'Institut Teknologi Bandung (ITB)',
        'study_program' => 'Teknik Informatika (STEI-R)',
        'status' => 'SELESAI',
    ];

    $response = $this->actingAs($this->bkTeacher)->post(route('bk.counseling.store'), $payload);

    $session = BkCounselingSession::latest('id')->first();
    $response->assertRedirect(route('bk.counseling.show', $session));
    $response->assertSessionHas('success');

    expect($session->student_id)->toBe($this->student->id);
    expect($session->field_of_service)->toBe('KARIER');
    expect($session->career_path)->toBe('KULIAH');
    expect($session->study_admission_track)->toBe('SNBP');
    expect($session->campus_name)->toBe('Institut Teknologi Bandung (ITB)');
    expect($session->study_program)->toBe('Teknik Informatika (STEI-R)');
    expect($session->follow_up_date->toDateString())->toBe($followUpDate);

    // Check notifications dispatched to BK staff
    $notif = Notification::where('type', 'GUIDANCE')
        ->where('title', 'like', '%RTL%')
        ->first();

    expect($notif)->not->toBeNull();
    expect($notif->message)->toContain($this->student->name);
});

test('can store career guidance session for workforce path', function () {
    $payload = [
        'student_id' => $this->student->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'KARIER',
        'session_type' => 'INDIVIDUAL',
        'service_category' => 'PERENCANAAN_INDIVIDUAL',
        'place' => 'Ruang BK',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'topic' => 'Kesiapan Memasuki Dunia Kerja Industri',
        'problem_description' => 'Siswa membutuhkan bimbingan penyusunan CV dan peluang magang kerja.',
        'counseling_approach' => 'Behavioral Counseling & Job Interview Simulation',
        'agreement_result' => 'Siswa memahami format CV ATS-friendly dan saluran rekrutmen BUMN.',
        'follow_up_plan' => 'Review draft CV dan portofolio kejuruan.',
        'career_path' => 'BEKERJA',
        'work_field' => 'Administrasi Perkantoran & IT Support PT Telkom',
        'status' => 'SELESAI',
    ];

    $response = $this->actingAs($this->bkTeacher)->post(route('bk.counseling.store'), $payload);

    $session = BkCounselingSession::latest('id')->first();
    $response->assertRedirect(route('bk.counseling.show', $session));

    $this->assertDatabaseHas('bk_counseling_sessions', [
        'student_id' => $this->student->id,
        'field_of_service' => 'KARIER',
        'career_path' => 'BEKERJA',
        'work_field' => 'Administrasi Perkantoran & IT Support PT Telkom',
        'campus_name' => null,
    ]);
});

test('can view counseling session detail with career and rtl attributes', function () {
    $session = BkCounselingSession::create([
        'session_code' => 'BK-TEST-DETAIL-'.rand(1000, 9999),
        'student_id' => $this->student->id,
        'counselor_id' => $this->bkTeacher->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'KARIER',
        'session_type' => 'INDIVIDUAL',
        'service_category' => 'LAYANAN_RESPONSIF',
        'place' => 'Ruang BK',
        'topic' => 'Detail Karier SNBT',
        'problem_description' => 'Persiapan UTBK SNBT.',
        'counseling_approach' => 'CBT',
        'agreement_result' => 'Jadwal belajar mandiri disepakati.',
        'follow_up_plan' => 'Evaluasi hasil tryout pertama.',
        'follow_up_date' => now()->addWeek()->toDateString(),
        'career_path' => 'KULIAH',
        'study_admission_track' => 'SNBT',
        'campus_name' => 'Universitas Indonesia',
        'study_program' => 'Sistem Informasi',
        'status' => 'SELESAI',
    ]);

    $response = $this->actingAs($this->bkTeacher)->get(route('bk.counseling.show', $session));

    $response->assertStatus(200);
    $response->assertSee('Universitas Indonesia', false);
    $response->assertSee('Sistem Informasi', false);
    $response->assertSee('SNBT', false);
    $response->assertSee('Rencana Tindak Lanjut (RTL)', false);
});

test('recap print route responds with official document and filtered data', function () {
    $response = $this->actingAs($this->bkTeacher)->get(route('bk.counseling.recap.print', [
        'field_of_service' => 'KARIER',
        'career_path' => 'KULIAH',
    ]));

    $response->assertStatus(200);
    $response->assertSee('REKAPITULASI DOKUMENTASI LAYANAN BIMBINGAN', false);
    $response->assertSee('POP-BK', false);
    $response->assertSee('Koordinator Bimbingan & Konseling', false);
    $response->assertSee('Kepala SMAN 1 Lengkong', false);
});

test('bk dashboard highlights upcoming rtl session', function () {
    $upcomingDate = now()->addDays(2)->toDateString();

    BkCounselingSession::create([
        'session_code' => 'BK-TEST-DASHBOARD-'.rand(1000, 9999),
        'student_id' => $this->student->id,
        'counselor_id' => $this->bkTeacher->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'BELAJAR',
        'session_type' => 'INDIVIDUAL',
        'service_category' => 'LAYANAN_RESPONSIF',
        'place' => 'Ruang BK',
        'topic' => 'Evaluasi Nilai Matematika',
        'problem_description' => 'Kesulitan materi trigonometri.',
        'counseling_approach' => 'Coaching',
        'agreement_result' => 'Mendaftar tutor sebaya.',
        'follow_up_plan' => 'Pertemuan follow up setelah ulangan harian bab trigonometri.',
        'follow_up_date' => $upcomingDate,
        'status' => 'SELESAI',
    ]);

    $response = $this->actingAs($this->bkTeacher)->get(route('bk.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Agenda Pertemuan Tindak Lanjut (RTL) Konseling', false);
    $response->assertSee($this->student->name, false);
});

test('bk edit form renders student selection, campus choices, and techniques', function () {
    $session = BkCounselingSession::create([
        'session_code' => 'BK-TEST-EDIT-'.rand(1000, 9999),
        'student_id' => $this->student->id,
        'counselor_id' => $this->bkTeacher->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'KARIER',
        'session_type' => 'INDIVIDUAL',
        'service_category' => 'LAYANAN_RESPONSIF',
        'place' => 'Ruang BK',
        'start_time' => '08:00',
        'end_time' => '09:00',
        'topic' => 'Konsultasi Multi Kampus',
        'problem_description' => 'Memilih kampus terbaik di Jawa Barat.',
        'counseling_approach' => 'Cognitive Behavior Therapy (CBT)',
        'agreement_result' => 'Mantap mendaftar SNBP.',
        'career_path' => 'KULIAH',
        'study_admission_track' => 'SNBP',
        'campus_choices' => [
            ['order' => 1, 'campus' => 'Universitas Indonesia', 'major' => 'Ilmu Komputer'],
            ['order' => 2, 'campus' => 'Institut Teknologi Bandung', 'major' => 'Teknik Informatika'],
        ],
        'status' => 'SEDANG_BERLANGSUNG',
    ]);

    $response = $this->actingAs($this->bkTeacher)->get(route('bk.counseling.edit', $session));

    $response->assertStatus(200);
    $response->assertSee('Edit Sesi Bimbingan', false);
    $response->assertSee('name="student_id"', false);
    $response->assertSee('career_guidance_panel', false);
    $response->assertSee('Universitas Indonesia', false);
    $response->assertSee('Ilmu Komputer', false);
    $response->assertSee('Pilihan Jalur Masuk Kuliah', false);
    $response->assertSee('Pendekatan / Teknik Konseling', false);
});

test('bk print official renders berita acara with multi campus choices and pop-bk technique', function () {
    $session = BkCounselingSession::create([
        'session_code' => 'BK-TEST-PRINT-'.rand(1000, 9999),
        'student_id' => $this->student->id,
        'counselor_id' => $this->bkTeacher->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'KARIER',
        'session_type' => 'INDIVIDUAL',
        'service_category' => 'LAYANAN_RESPONSIF',
        'place' => 'Ruang Konseling Khusus',
        'start_time' => '13:00',
        'end_time' => '14:00',
        'topic' => 'Finalisasi Pilihan SNBP Siswa Berprestasi',
        'problem_description' => 'Siswa menimbang prodi unggulan berdasarkan nilai rapor semester 1-5.',
        'counseling_approach' => 'Solution-Focused Brief Counseling (SFBC)',
        'agreement_result' => 'Siswa menyepakati pilihan prodi prioritas utama dan cadangan.',
        'follow_up_plan' => 'Cek kelengkapan berkas portofolio prestasi sebelum upload.',
        'follow_up_date' => now()->addDays(3)->toDateString(),
        'follow_up_status' => 'PENDING',
        'career_path' => 'KULIAH',
        'study_admission_track' => 'SNBP',
        'campus_choices' => [
            ['order' => 1, 'campus' => 'Universitas Padjadjaran', 'major' => 'Kedokteran'],
            ['order' => 2, 'campus' => 'Universitas Indonesia', 'major' => 'Farmasi'],
        ],
        'status' => 'SELESAI',
    ]);

    $response = $this->actingAs($this->bkTeacher)->get(route('bk.counseling.print', $session));

    $response->assertStatus(200);
    $response->assertSee('BERITA ACARA LAYANAN BIMBINGAN', false);
    $response->assertSee('Universitas Padjadjaran', false);
    $response->assertSee('Kedokteran', false);
    $response->assertSee('Universitas Indonesia', false);
    $response->assertSee('Farmasi', false);
    $response->assertSee('Solution-Focused Brief Counseling (SFBC)', false);
    $response->assertSee('Ruang Konseling Khusus', false);
    $response->assertSee('13:00', false);
    $response->assertSee('Kepala SMAN 1 Lengkong', false);
});

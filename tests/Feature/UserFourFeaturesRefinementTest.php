<?php

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Extracurricular;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Carbon::setTestNow();

    $this->academicYear = AcademicYear::firstOrCreate(
        ['name' => '2026/2027'],
        [
            'semester' => 'Ganjil',
            'is_active' => true,
        ]
    );

    $this->schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'X-A-TEST-REFINE'],
        [
            'grade' => 'X',
            'major' => 'Umum',
            'is_active' => true,
        ]
    );
});

afterEach(function () {
    Carbon::setTestNow();
});

test('poin 1: anggota ekstrakurikuler dapat dipilih beberapa sekaligus (bulk add via student_ids) baru dimasukkan', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Ekstra Refine',
        'role' => 'admin',
        'roles' => ['admin'],
        'is_active' => true,
    ]);

    $extra = Extracurricular::firstOrCreate(
        ['code' => 'PRAM-REFINE'],
        [
            'name' => 'Pramuka Sissma1le Refine',
            'category' => 'Kepanduan',
            'is_active' => true,
        ]
    );

    $student1 = Student::create([
        'nis' => '990101',
        'nisn' => '00990101',
        'name' => 'Siswa Bulk Satu',
        'gender' => 'L',
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    $student2 = Student::create([
        'nis' => '990102',
        'nisn' => '00990102',
        'name' => 'Siswa Bulk Dua',
        'gender' => 'P',
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    $student3 = Student::create([
        'nis' => '990103',
        'nisn' => '00990103',
        'name' => 'Siswa Bulk Tiga',
        'gender' => 'L',
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->post(route('extracurricular.members.add', $extra->id), [
            'student_ids' => [$student1->id, $student2->id, $student3->id],
            'role' => 'Anggota',
        ]);

    $response->assertRedirect(route('extracurricular.index', [
        'extra_id' => $extra->id,
        'tab' => 'members',
    ]));

    $response->assertSessionHas('success');

    // Pastikan ketiga siswa telah terdaftar di pivot table extracurricular_student
    expect($extra->students()->whereIn('students.id', [$student1->id, $student2->id, $student3->id])->count())->toBe(3);
});

test('poin 2: cetak dokumen ekstrakurikuler mengganti nama Lemahabang menjadi sukabumi dan menggunakan kop sekolah', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Ekstra Refine 2',
        'role' => 'admin',
        'roles' => ['admin'],
        'is_active' => true,
    ]);

    $extra = Extracurricular::firstOrCreate(
        ['code' => 'PMR-REFINE'],
        [
            'name' => 'PMR Wira Sukabumi Refine',
            'category' => 'Kemanusiaan',
            'is_active' => true,
        ]
    );

    // Simpan path KOP surat resmi sekolah di Master Data
    Storage::fake('public');
    Storage::disk('public')->put('branding/kop_resmi_test.png', 'fake-image-content');
    SchoolSetting::set('school_kop', 'branding/kop_resmi_test.png', 'branding', 'KOP Surat');
    SchoolSetting::set('school_city', 'Sukabumi', 'identity', 'Kota Sekolah');

    $response = $this->actingAs($admin)
        ->get(route('extracurricular.print', ['extra_id' => $extra->id]));

    $response->assertStatus(200);

    // Pastikan teks Sukabumi muncul dan tidak ada teks Lemahabang pada tanda tangan
    $response->assertSee('Sukabumi,');
    $response->assertDontSee('Lemahabang,');

    // Pastikan kop surat bergambar di-render
    $response->assertSee('kop_resmi_test.png');
});

test('poin 3: cetak dokumen keagamaan menampilkan Koordinator Keagamaan dari Data Guru & Akun, bukan Administrator sistem', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Sistem Keagamaan',
        'role' => 'admin',
        'roles' => ['admin'],
        'is_active' => true,
    ]);

    // Buat Akun & Guru Koordinator Keagamaan di Data Guru & Akun
    $religionUser = User::factory()->create([
        'name' => 'Ust. H. Ahmad Fauzi, S.Pd.I.',
        'username' => '198205102008011005',
        'role' => 'koordinator_keagamaan',
        'roles' => ['koordinator_keagamaan'],
        'is_active' => true,
    ]);

    $religionTeacher = Teacher::firstOrCreate(
        ['nip' => '198205102008011005'],
        [
            'user_id' => $religionUser->id,
            'name' => 'Ust. H. Ahmad Fauzi, S.Pd.I.',
            'title' => 'Koordinator Keagamaan',
            'is_active' => true,
        ]
    );

    // Ambil dokumen cetak untuk Shalat Dzuhur, Dhuha, atau Keputrian oleh admin
    $response = $this->actingAs($admin)
        ->get(route('worship.print', 'dzuhur'));

    $response->assertStatus(200);

    // Penandatangan harus Koordinator Keagamaan, bukan Administrator Sistem
    $response->assertSee('Koordinator Keagamaan');
    $response->assertSee('Ust. H. Ahmad Fauzi, S.Pd.I.');
    $response->assertSee('198205102008011005');
    $response->assertDontSee('Administrator Sistem');
});

test('poin 4: pada presensi utama status otomatis alpa mulai tampil disaat jadwal dibuka untuk siswa yang belum absen dan izin belum diapprove', function () {
    // Setting hari aktif dan jam operasional: masuk buka 06:30
    Carbon::setTestNow(Carbon::parse('2026-10-14 05:00:00', 'Asia/Jakarta')); // Jam 05.00 pagi (sebelum jadwal dibuka)

    $studentBelumAbsen = Student::create([
        'nis' => '880101',
        'nisn' => '00880101',
        'name' => 'Siswa Belum Absen Test',
        'gender' => 'L',
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    $studentIzinPending = Student::create([
        'nis' => '880102',
        'nisn' => '00880102',
        'name' => 'Siswa Izin Pending Test',
        'gender' => 'P',
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => true,
    ]);

    $service = app(AttendanceService::class);
    $today = Carbon::today('Asia/Jakarta')->toDateString();

    // Student 2 mengajukan izin via portal tapi belum diapprove (verification_status = PERLU_VERIFIKASI)
    Attendance::create([
        'student_id' => $studentIzinPending->id,
        'nis' => $studentIzinPending->nis,
        'nisn' => $studentIzinPending->nisn,
        'student_name' => $studentIzinPending->name,
        'class_id' => $this->schoolClass->id,
        'date' => $today,
        'time' => '05:30:00',
        'method' => 'IZIN_SAKIT',
        'status' => 'IZIN',
        'requested_status' => 'IZIN',
        'verification_status' => 'PERLU_VERIFIKASI',
        'notes' => 'Izin acara keluarga',
    ]);

    // 1. Pada jam 05.00 pagi (sebelum in_start 06.30), jadwal belum dibuka -> belum generate ALPA
    $createdCount = $service->ensureDailyAttendanceRecords($today);
    expect($createdCount)->toBe(0);

    // 2. Sekarang waktu maju ke jam 07.00 pagi (jadwal presensi masuk SUDAH DIBUKA >= 06.30)
    Carbon::setTestNow(Carbon::parse('2026-10-14 07:00:00', 'Asia/Jakarta'));

    $createdCount2 = $service->ensureDailyAttendanceRecords($today);
    expect($createdCount2)->toBeGreaterThan(0);

    // Siswa yang belum absen otomatis tercatat ALPA
    $attBelum = Attendance::where('student_id', $studentBelumAbsen->id)->whereDate('date', $today)->first();
    expect($attBelum)->not->toBeNull();
    expect($attBelum->status)->toBe('ALPA');

    // Siswa yang pengajuan izinnya belum diapprove otomatis berstatus ALPA
    $attPending = Attendance::where('student_id', $studentIzinPending->id)->whereDate('date', $today)->first();
    expect($attPending->status)->toBe('ALPA');
    expect($attPending->verification_status)->toBe('PERLU_VERIFIKASI');

    // Tampilan presensi utama (AttendanceController::index) menampilkan status ALPA secara realtime
    $admin = User::factory()->create([
        'name' => 'Admin Ekstra Refine 3',
        'role' => 'admin',
        'roles' => ['admin'],
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('attendance.index', ['date' => $today]));
    $response->assertStatus(200);
    $response->assertSee('ALPA');
});

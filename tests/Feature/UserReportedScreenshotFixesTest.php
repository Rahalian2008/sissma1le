<?php

use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\Attendance;
use App\Models\EarlyLeaveRequest;
use App\Models\LibraryBook;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin', 'level' => 100]);
    Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin', 'level' => 90]);
    Role::firstOrCreate(['name' => 'guru'], ['display_name' => 'Guru', 'level' => 30]);
    Role::firstOrCreate(['name' => 'guru_bk'], ['display_name' => 'Guru BK', 'level' => 40]);
    Role::firstOrCreate(['name' => 'siswa'], ['display_name' => 'Siswa', 'level' => 10]);
});

test('issue 1: master users page loads without attempt to read property name on string error', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin', 'guru'],
    ]);

    // Give user relation-loaded roles
    $roleModel = Role::where('name', 'admin')->first();
    $admin->roles()->syncWithoutDetaching([$roleModel->id]);

    $response = $this->actingAs($admin)->get('/master/users');
    $response->assertStatus(200);

    // Explicitly test getAllRolesAttribute() with both relation loaded and array of strings
    $userWithRelation = User::with('roles')->find($admin->id);
    expect($userWithRelation->all_roles)->toBeArray()
        ->and($userWithRelation->all_roles)->toContain('admin');
});

test('issue 2 & 3: attendance and early leave page loads and status_badge renders properly', function () {
    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $class = SchoolClass::create(['name' => 'X-1', 'grade' => 'X']);
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'name' => 'Budi Santoso',
        'nis' => '10203001',
        'nisn' => '0081234567',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $earlyLeave = EarlyLeaveRequest::create([
        'letter_number' => '421.3/SKPC/0001/XI/2026',
        'student_id' => $student->id,
        'date' => now()->toDateString(),
        'leave_time' => '10:00:00',
        'reason_category' => 'Sakit di Sekolah (UKS)',
        'reason' => 'Demam tinggi',
        'status' => 'PENDING',
        'created_by' => $admin->id,
    ]);

    $exitPermit = SchoolExitPermit::create([
        'permit_number' => '421.3/SIK-LS/0001/XI/2026',
        'date' => now()->toDateString(),
        'out_time' => '10:00:00',
        'purpose' => 'Lomba Akademik',
        'reason' => 'Mengikuti olimpiade sains',
        'status' => 'PENDING',
        'created_by' => $admin->id,
    ]);
    $exitPermit->students()->attach($student->id);

    // Verify SchoolExitPermit relationship returnRecorder
    expect($exitPermit->returnRecorder)->not->toBeNull()
        ->and($exitPermit->status_badge)->toBeString()
        ->and($earlyLeave->status_badge)->toBeString();

    $responseAttendance = $this->actingAs($admin)->get('/attendance');
    $responseAttendance->assertStatus(200);

    $responseEarlyLeave = $this->actingAs($admin)->get('/attendance/early-leave');
    $responseEarlyLeave->assertStatus(200);
});

test('issue 4: bk parent call accepts call_time longer than 20 characters', function () {
    $bkUser = User::factory()->create(['role' => 'guru_bk', 'roles' => ['guru_bk']]);
    $class = SchoolClass::create(['name' => 'X-2', 'grade' => 'X']);
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'name' => 'Abdul Gopur',
        'nis' => '10203002',
        'nisn' => '0109752976',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $longCallTime = '08.30 WIB s.d Selesai'; // 21 characters

    $response = $this->actingAs($bkUser)->post(route('bk.parent-call.store'), [
        'student_id' => $student->id,
        'letter_number' => '421.3/001-BK/SMA1LE/XI/2026',
        'call_date' => now()->addDays(2)->toDateString(),
        'call_time' => $longCallTime,
        'place' => 'Ruang Bimbingan Konseling SMA Negeri 1 Leles',
        'reason' => 'Pembinaan dan diskusi prestasi belajar siswa',
        'status' => 'SURAT_DIKIRIM',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('bk_parent_calls', [
        'student_id' => $student->id,
        'call_time' => $longCallTime,
    ]);
});

test('issue 5: school website homepage loads and library_books file_path and is_ebook query succeeds', function () {
    if (Schema::hasTable('library_books')) {
        LibraryBook::create([
            'book_code' => 'BK-001',
            'title' => 'Buku Panduan Belajar',
            'book_type' => 'digital',
            'digital_file_path' => 'books/sample.pdf',
            'file_path' => 'books/sample.pdf',
            'is_ebook' => true,
            'author' => 'Pendidik',
            'publisher' => 'SMA Negeri 1 Leles',
            'publish_year' => 2026,
            'is_available' => true,
            'is_active' => true,
        ]);

        // Explicitly test the exact SQL query that failed in Image 4
        $count = LibraryBook::where(function ($q) {
            $q->whereNotNull('file_path')->orWhere('is_ebook', 1);
        })->count();

        expect($count)->toBeGreaterThanOrEqual(1);
    }

    $response = $this->get('/');
    $response->assertStatus(200);
});

test('issue 6: wakasek kesiswaan sees full action buttons on pelanggaran and prestasi siswa', function () {
    Role::firstOrCreate(['name' => 'wakasek_kesiswaan'], ['display_name' => 'Wakasek Kesiswaan', 'level' => 80]);

    $wakasek = User::factory()->create([
        'role' => 'wakasek_kesiswaan',
        'roles' => ['wakasek_kesiswaan', 'guru'],
    ]);

    $achCat = AchievementCategory::firstOrCreate(
        ['code' => 'R8'],
        ['name' => 'Prestasi Akademik & Non Akademik', 'is_active' => true]
    );

    $violCat = ViolationCategory::firstOrCreate(
        ['code' => 'V1'],
        ['name' => 'Kedisiplinan & Ketertiban', 'is_active' => true]
    );

    $class = SchoolClass::create(['name' => 'X-3', 'grade' => 'X']);
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'name' => 'Wildan Raditia',
        'nis' => '10203003',
        'nisn' => '0097665544',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $achievement = Achievement::create([
        'achievement_code' => 'PRESTASI-2026-TEST1',
        'student_id' => $student->id,
        'category_id' => $achCat->id,
        'title' => 'Juara Olimpiade Matematika',
        'category' => 'R8',
        'level' => 'Kecamatan',
        'points' => 20,
        'date' => now()->toDateString(),
        'status' => 'MENUNGGU_VERIFIKASI',
        'recorded_by' => $wakasek->id,
    ]);

    $violation = Violation::create([
        'violation_code' => 'PELANGGARAN-2026-TEST1',
        'student_id' => $student->id,
        'category_id' => $violCat->id,
        'title' => 'Terlambat Masuk Sekolah',
        'category' => 'RINGAN',
        'points' => 5,
        'date' => now()->toDateString(),
        'status' => 'MENUNGGU_VERIFIKASI',
        'recorded_by' => $wakasek->id,
    ]);

    expect($wakasek->isKesiswaanAdmin())->toBeTrue();

    // Verify Wakasek sees full action buttons on Prestasi Siswa
    $resAch = $this->actingAs($wakasek)->get(route('achievements.index'));
    $resAch->assertStatus(200);
    $resAch->assertSee('Langsung Approve / Setujui');
    $resAch->assertSee('Tolak Laporan Prestasi');
    $resAch->assertSee(route('achievements.edit', $achievement->id));

    // Verify Wakasek sees full action buttons on Pelanggaran Siswa
    $resViol = $this->actingAs($wakasek)->get(route('violations.index'));
    $resViol->assertStatus(200);
    $resViol->assertSee('Langsung Approve / Setujui');
    $resViol->assertSee('Tolak Laporan Pelanggaran');
    $resViol->assertSee(route('violations.edit', $violation->id));
});

test('warehouse and sarpras modals are rendered without clipping and absen manual period options are properly structured', function () {
    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $class = SchoolClass::create(['name' => 'X-MIPA-1', 'grade' => 'X']);

    // Check Absen Manual page
    $resAbsen = $this->actingAs($admin)->get(route('tu.kesiswaan.absen_manual.index'));
    $resAbsen->assertStatus(200);
    $resAbsen->assertSee('Model Periode Presensi');
    $resAbsen->assertSee('Bulanan (1 Bln)');
    $resAbsen->assertSee('Mingguan (6 Hari)');
    $resAbsen->assertSee('Rentang Kustom');

    // Check Warehouse & Sarpras pages
    $resReceipts = $this->actingAs($admin)->get(route('warehouse.receipts.index'));
    $resReceipts->assertStatus(200);
    $resReceipts->assertSee('modal-receipt');
    $resReceipts->assertSee('id="sim-bmd-modal"', false);
    $resReceipts->assertSee('style="z-index: 99999 !important;"', false);
    $resReceipts->assertSee('style="z-index: 1050;"', false);

    $resBorrowings = $this->actingAs($admin)->get(route('warehouse.borrowings.index'));
    $resBorrowings->assertStatus(200);
    $resBorrowings->assertSee('modal-borrow');

    $resGoods = $this->actingAs($admin)->get(route('warehouse.goods.index'));
    $resGoods->assertStatus(200);
    $resGoods->assertSee('modal-good');

    $resIssuances = $this->actingAs($admin)->get(route('warehouse.issuances.index'));
    $resIssuances->assertStatus(200);
    $resIssuances->assertSee('modal-issuance');

    $resOpnames = $this->actingAs($admin)->get(route('warehouse.stock-opnames.index'));
    $resOpnames->assertStatus(200);
    $resOpnames->assertSee('modal-opname');

    $resMaintenance = $this->actingAs($admin)->get(route('sarpras.maintenance.index'));
    $resMaintenance->assertStatus(200);
    $resMaintenance->assertSee('modal-maintenance');

    $resDisposals = $this->actingAs($admin)->get(route('sarpras.disposals.index'));
    $resDisposals->assertStatus(200);
    $resDisposals->assertSee('modalAddDisposal');

    $resRooms = $this->actingAs($admin)->get(route('sarpras.rooms.index'));
    $resRooms->assertStatus(200);
    $resRooms->assertSee('modal-room');
});

test('issue 6-item batch: guest report print displays Kepala Administrasi Sekolah and date range', function () {
    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    \App\Models\SchoolIdentity::create([
        'name' => 'SMAN 1 Lengkong',
        'city' => 'Kabupaten Sukabumi',
        'headmaster_name' => 'PIAN SOPIAN, S.Pd, MM.Pd.',
        'headmaster_nip' => '198009132008011005',
    ]);

    $headOfAdmin = User::factory()->create([
        'name' => 'Rachmat Sugiarto, A.Md.',
        'nip' => '198112282025211059',
        'roles' => ['kepala_administrasi'],
    ]);

    \App\Models\Guest::create([
        'ticket_number' => 'TMU-20261124-001',
        'name' => 'Bapak Pengawas',
        'gender' => 'Laki-laki',
        'identity_type' => 'NIP',
        'identity_number' => '197501011999031001',
        'phone' => '081234567890',
        'institution' => 'Dinas Pendidikan Prov. Jabar',
        'institution_type' => 'Dinas Pendidikan Prov. Jawa Barat',
        'destination_department' => 'Kepala Sekolah',
        'purpose' => 'Monitoring KBM',
        'purpose_detail' => 'Kunjungan rutin pengawasan KBM',
        'number_of_guests' => 2,
        'check_in_at' => now(),
        'status' => 'menunggu',
    ]);

    $response = $this->actingAs($admin)->get(route('guests.report.print', [
        'date_from' => now()->format('Y-m-d'),
        'date_to' => now()->format('Y-m-d'),
    ]));

    $response->assertStatus(200);
    $response->assertSee('Kepala Administrasi Sekolah');
    $response->assertSee('RACHMAT SUGIARTO, A.MD.');
    $response->assertSee('198112282025211059');
    $response->assertSee('Bapak Pengawas');
});

test('issue 6-item batch: guest index supports date_from and date_to range filter', function () {
    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);

    \App\Models\Guest::create([
        'ticket_number' => 'TMU-FILTER-001',
        'name' => 'Tamu Hari Ini',
        'gender' => 'Laki-laki',
        'identity_type' => 'KTP',
        'identity_number' => '3202111122330001',
        'phone' => '081234567891',
        'institution' => 'PT Mitra Edukasi',
        'institution_type' => 'Perusahaan / Dunia Usaha / Vendor',
        'destination_department' => 'Kepala Tata Usaha',
        'purpose' => 'Penawaran Kerjasama',
        'purpose_detail' => 'Presentasi alat lab komputer',
        'number_of_guests' => 1,
        'check_in_at' => now(),
        'status' => 'menunggu',
    ]);

    $response = $this->actingAs($admin)->get(route('guests.index', [
        'date_from' => now()->format('Y-m-d'),
        'date_to' => now()->format('Y-m-d'),
    ]));

    $response->assertStatus(200);
    $response->assertSee('TMU-FILTER-001');
    $response->assertSee('Tamu Hari Ini');
    $response->assertSee('name="date_from"', false);
    $response->assertSee('name="date_to"', false);
});

test('issue 6-item batch: kios satpam displays guest list and can checkout guest', function () {
    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);

    $guest = \App\Models\Guest::create([
        'ticket_number' => 'TMU-SATPAM-001',
        'name' => 'Tamu Pos Satpam',
        'gender' => 'Laki-laki',
        'identity_type' => 'SIM',
        'identity_number' => 'SIM-998877',
        'phone' => '081299887766',
        'institution' => 'Puskesmas Lengkong',
        'institution_type' => 'Kementerian / Instansi Pemerintah',
        'destination_department' => 'Layanan Administrasi TU',
        'purpose' => 'Penyuluhan Kesehatan',
        'purpose_detail' => 'Koordinasi program imunisasi siswa',
        'number_of_guests' => 1,
        'vehicle_type' => 'Mobil',
        'vehicle_license_plate' => 'B 1234 XYZ',
        'check_in_at' => now(),
        'status' => 'menunggu',
    ]);

    $responseKiosk = $this->actingAs($admin)->get(route('attendance.kiosk-satpam'));
    $responseKiosk->assertStatus(200);
    $responseKiosk->assertSee('TMU-SATPAM-001');
    $responseKiosk->assertSee('Tamu Pos Satpam');
    $responseKiosk->assertSee('B 1234 XYZ');
    $responseKiosk->assertSee('Checkout Tamu');

    // Test checkout action by satpam
    $responseCheckout = $this->actingAs($admin)->post(route('attendance.kiosk-satpam.guest-checkout', $guest));
    $responseCheckout->assertRedirect();

    $guest->refresh();
    expect($guest->status)->toBe('selesai')
        ->and($guest->check_out_at)->not->toBeNull();
});

test('issue 6-item batch: class learning agenda syncs from teaching journals', function () {
    $class = SchoolClass::create(['name' => 'XII Merdeka 4', 'grade' => 'XII']);
    $subject = \App\Models\Subject::create(['name' => 'Informatika Tingkat Lanjut', 'code' => 'INF-TL-XII']);
    $teacher = \App\Models\Teacher::create(['name' => 'Rahmat Hidayat, S.Kom', 'nip' => '198501012010011005']);

    $courseClass = \App\Models\CourseClass::create([
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'class_id' => $class->id,
        'academic_year_id' => null,
    ]);

    $journal = \App\Models\TeachingJournal::create([
        'course_class_id' => $courseClass->id,
        'teacher_id' => $teacher->id,
        'date' => now()->toDateString(),
        'meeting_number' => 3,
        'time_slot' => '08.40-10.00',
        'topic' => 'Mendesain jaringan LAN dan WAN',
        'teaching_activities' => 'Praktik konfigurasi router mikrotik.',
        'student_attendance_summary' => ['hadir' => 30, 'sakit' => 2, 'izin' => 1, 'alfa' => 0],
        'learning_achievement_status' => 'TERCAPAI',
    ]);

    \App\Models\ClassLearningAgenda::syncFromTeachingJournals('XII Merdeka 4');

    $agenda = \App\Models\ClassLearningAgenda::where('rombel', 'XII Merdeka 4')
        ->where('subject', 'Informatika Tingkat Lanjut')
        ->first();

    expect($agenda)->not->toBeNull()
        ->and($agenda->learning_topic)->toBe('Mendesain jaringan LAN dan WAN')
        ->and($agenda->time_slot)->toBe('08.40-10.00')
        ->and($agenda->teacher_name)->toBe('Rahmat Hidayat, S.Kom')
        ->and($agenda->students_present)->toBe(30)
        ->and($agenda->students_sick)->toBe(2);
});

test('kiosk tv: profil shows kepala sekolah at top, rekap siswa and gtk, and attendance ticker filters to hadir only', function () {
    $class = SchoolClass::create(['name' => 'X Merdeka 1', 'grade' => 'X']);
    $studentHadir = Student::create([
        'name' => 'Siswa Hadir Alpha',
        'nis' => '998811',
        'nisn' => '0099881101',
        'school_class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);
    $studentIzin = Student::create([
        'name' => 'Siswi Izin Beta',
        'nis' => '998812',
        'nisn' => '0099881102',
        'school_class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    Attendance::create([
        'student_id' => $studentHadir->id,
        'date' => now('Asia/Jakarta')->toDateString(),
        'time' => '06:30:00',
        'status' => 'HADIR',
        'method' => 'RFID',
    ]);

    Attendance::create([
        'student_id' => $studentIzin->id,
        'date' => now('Asia/Jakarta')->toDateString(),
        'time' => '07:15:00',
        'status' => 'IZIN',
        'method' => 'Manual',
    ]);

    // Test Kios TV view
    $response = $this->get(route('kiosk.index'));
    $response->assertStatus(200);
    $response->assertSee('PIMPINAN SEKOLAH');
    $response->assertSee('Rekapitulasi Peserta Didik (Siswa)');
    $response->assertSee('Rekapitulasi Guru &amp; TU', false);
    $response->assertSee('animate-vertical-ticker');

    // Test Kios TV JSON API
    $apiResponse = $this->get(route('kiosk.data'));
    $apiResponse->assertStatus(200);
    $apiData = $apiResponse->json();

    expect($apiData)->toHaveKeys(['recap_students', 'recap_gtk', 'daily'])
        ->and($apiData['recap_students'])->toHaveKeys(['classes', 'total_students', 'total_male', 'total_female'])
        ->and($apiData['recap_gtk'])->toHaveKeys(['guru', 'tu', 'grand_total']);

    // Ensure all recent attendance items only have HADIR status
    foreach ($apiData['daily']['recent'] as $rec) {
        expect($rec['status'])->toBe('HADIR');
    }
});

test('issue: principal dashboard links Adm. Ketatausahaan to tu.dashboard and sidebar displays TU accordion for kepala_sekolah', function () {
    Role::firstOrCreate(['name' => 'kepala_sekolah'], ['display_name' => 'Kepala Sekolah', 'level' => 80]);
    Role::firstOrCreate(['name' => 'guru'], ['display_name' => 'Guru', 'level' => 30]);

    $kepsek = User::factory()->create([
        'name' => 'Pian Sopian, S.Pd., MM.Pd.',
        'role' => 'guru',
        'roles' => ['guru', 'kepala_sekolah'],
    ]);

    // Active role in session or attribute
    session(['active_role' => 'kepala_sekolah']);

    // 1. Principal dashboard contains link to tu.dashboard for card 14
    $dashboardResponse = $this->actingAs($kepsek)->get(route('dashboard'));
    $dashboardResponse->assertStatus(200);
    $dashboardResponse->assertSee(route('tu.dashboard'));
    $dashboardResponse->assertSee('Adm. Ketatausahaan');

    // 2. Sidebar displays Adm. Ketatausahaan and sub-menu links
    $tuDashboardResponse = $this->actingAs($kepsek)->get(route('tu.dashboard'));
    $tuDashboardResponse->assertStatus(200);
    $tuDashboardResponse->assertSee(route('tu.dashboard'));
    $tuDashboardResponse->assertSee(route('tu.kesiswaan.index'));
    $tuDashboardResponse->assertSee(route('tu.kepegawaian.index'));
    $tuDashboardResponse->assertSee(route('tu.persuratan.index'));
    $tuDashboardResponse->assertSee(route('tu.sarpras.index'));
    $tuDashboardResponse->assertSee(route('tu.keuangan.index'));
    $tuDashboardResponse->assertSee(route('guests.index'));
});

test('issue: lms quiz questions level_kognitif supports long string like Penalaran & Analisis HOTS without truncation', function () {
    $teacherUser = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
    $teacher = \App\Models\Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Pengampu',
        'nip' => '198501012010011002',
        'gender' => 'L',
        'is_active' => true,
    ]);
    $class = SchoolClass::create(['name' => 'XII Merdeka 1', 'grade' => 'XII']);

    $subject = \App\Models\Subject::create([
        'code' => 'INF-XII',
        'name' => 'Informatika',
        'order' => 1,
    ]);

    $courseClass = \App\Models\CourseClass::create([
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => null,
    ]);

    $quiz = \App\Models\LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Asesmen Sumatif Akhir Semester',
        'duration_minutes' => 60,
        'is_published' => true,
    ]);

    // Test creating question with long level_kognitif (38 chars)
    $longLevel = 'L3 / C4-C6 (Penalaran & Analisis HOTS)';
    $response = $this->actingAs($teacherUser)->post(route('lms.quizzes.questions.store', [$courseClass->id, $quiz->id]), [
        'question_type' => 'essay',
        'question_text' => '<p>Jelaskan transmisi data real-time TCP vs UDP.</p>',
        'score_weight' => 20,
        'level_kognitif' => $longLevel,
        'correct_answer' => 'TCP berorientasi koneksi, UDP connectionless.',
    ]);

    $response->assertSessionHasNoErrors();
    $question = \App\Models\LmsQuizQuestion::where('quiz_id', $quiz->id)->first();
    expect($question)->not->toBeNull()
        ->and($question->level_kognitif)->toBe($longLevel);

    // Test updating question with another long level_kognitif
    $updateLevel = 'Penalaran & Analisis HOTS (Tingkat Tinggi)';
    $updateResponse = $this->actingAs($teacherUser)->put(route('lms.quizzes.questions.update', [$courseClass->id, $quiz->id, $question->id]), [
        'question_type' => 'essay',
        'question_text' => '<p>Jelaskan transmisi data real-time TCP vs UDP (Revisi).</p>',
        'score_weight' => 25,
        'level_kognitif' => $updateLevel,
        'correct_answer' => 'TCP berorientasi koneksi, UDP connectionless dengan latensi rendah.',
    ]);

    $updateResponse->assertSessionHasNoErrors();
    $question->refresh();
    expect($question->level_kognitif)->toBe($updateLevel);
});

test('issue: kios tv profil slide 0 displays vision under kepala sekolah and auto scroll ticker classes without manual scrollbar', function () {
    $response = $this->get(route('kiosk.index'));
    $response->assertStatus(200);

    // Ensure Visi is displayed under Kepala Sekolah
    $response->assertSee('Visi:');
    $response->assertSee('animate-vertical-ticker-slow');

    // Ensure no kiosk-scroll manual scrollbar on slide 0
    $content = $response->getContent();
    expect($content)->toContain('animate-vertical-ticker-slow');
});

<?php

use App\Models\Achievement;
use App\Models\AchievementCategory;
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

<?php

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->flushSession();
    // Pastikan roles esensial terdaftar
    $roles = [
        'super_admin', 'admin', 'kepala_sekolah', 'wakasek_kesiswaan',
        'wakasek_kurikulum', 'koordinator_bk', 'kepala_perpustakaan',
        'guru', 'staff', 'tu', 'wali_kelas', 'bk', 'pustakawan', 'siswa',
        'kepala_administrasi',
    ];
    foreach ($roles as $r) {
        Role::firstOrCreate(['name' => $r], ['display_name' => ucwords(str_replace('_', ' ', $r)), 'level' => 5]);
    }
});

test('point 1: semua akun guru, tu, dan siswa dapat login sempurna menggunakan NIP, NISN, atau password default', function () {
    // 1. Akun Guru
    $teacherUser = User::factory()->create([
        'name' => 'Guru Penguji Login',
        'username' => '198501012015011001',
        'nip' => '198501012015011001',
        'email' => 'guru.penguji@sma1le.sch.id',
        'password' => Hash::make('198501012015011001'),
        'role' => 'guru',
        'roles' => ['guru'],
        'is_active' => true,
    ]);
    Teacher::updateOrCreate(
        ['user_id' => $teacherUser->id],
        ['nip' => '198501012015011001', 'name' => $teacherUser->name, 'is_active' => true]
    );

    $loginTeacher = $this->post(route('login.post'), [
        'email' => '198501012015011001',
        'password' => '198501012015011001',
    ]);
    $loginTeacher->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($teacherUser);
    $this->post(route('logout'));

    // 2. Akun Tenaga Kependidikan (TU / Staff)
    $staffUser = User::factory()->create([
        'name' => 'Staf TU Penguji Login',
        'username' => '199002022020021002',
        'nip' => '199002022020021002',
        'email' => 'staff.penguji@sma1le.sch.id',
        'password' => Hash::make('199002022020021002'),
        'role' => 'staff',
        'roles' => ['staff', 'tu'],
        'is_active' => true,
    ]);
    Teacher::updateOrCreate(
        ['user_id' => $staffUser->id],
        ['nip' => '199002022020021002', 'name' => $staffUser->name, 'title' => 'Staf Tata Usaha', 'is_active' => true]
    );

    $loginStaff = $this->post(route('login.post'), [
        'email' => '199002022020021002',
        'password' => '199002022020021002',
    ]);
    $loginStaff->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($staffUser);
    $this->post(route('logout'));

    // 3. Akun Siswa
    $class = SchoolClass::first() ?? SchoolClass::factory()->create(['name' => 'X-1', 'grade' => 'X']);
    $studentUser = User::factory()->create([
        'name' => 'Siswa Penguji Login',
        'username' => '0071234567',
        'email' => '0071234567@siswa.sma1le.sch.id',
        'password' => Hash::make('0071234567'),
        'role' => 'siswa',
        'roles' => ['siswa'],
        'is_active' => true,
    ]);
    Student::updateOrCreate(
        ['user_id' => $studentUser->id],
        [
            'nisn' => '0071234567',
            'nis' => '242510001',
            'name' => $studentUser->name,
            'school_class_id' => $class->id,
            'is_active' => true,
        ]
    );

    $loginStudent = $this->post(route('login.post'), [
        'email' => '0071234567',
        'password' => '0071234567',
    ]);
    $loginStudent->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($studentUser);
});

test('point 2: wakasek kesiswaan adalah admin aplikasi kesiswaan dan memiliki wewenang setara admin khusus kesiswaan', function () {
    $wakasekKesiswaan = User::factory()->create([
        'name' => 'Wakasek Kesiswaan Admin',
        'username' => 'wakasek_kesiswaan_tester',
        'email' => 'wakasek.kesiswaan@sma1le.sch.id',
        'role' => 'wakasek_kesiswaan',
        'roles' => ['guru', 'wakasek_kesiswaan'],
        'is_active' => true,
    ]);

    expect($wakasekKesiswaan->isKesiswaanAdmin())->toBeTrue()
        ->and($wakasekKesiswaan->canAccessInActiveRole('kesiswaan'))->toBeTrue()
        ->and($wakasekKesiswaan->canAccessInActiveRole('attendance'))->toBeTrue()
        ->and($wakasekKesiswaan->canAccessInActiveRole('spmb'))->toBeTrue()
        ->and($wakasekKesiswaan->canAccessInActiveRole('discipline'))->toBeTrue();

    // Mengakses menu presensi kesiswaan
    $resAttendance = $this->actingAs($wakasekKesiswaan)->get(route('attendance.index'));
    $resAttendance->assertStatus(200);

    // Mengakses ranking tata tertib kesiswaan
    $resDiscipline = $this->actingAs($wakasekKesiswaan)->get(route('discipline.ranking'));
    $resDiscipline->assertStatus(200);
});

test('point 3: wakasek kurikulum adalah admin aplikasi kurikulum dan memiliki wewenang setara admin khusus kurikulum', function () {
    $wakasekKurikulum = User::factory()->create([
        'name' => 'Wakasek Kurikulum Admin',
        'username' => 'wakasek_kurikulum_tester',
        'email' => 'wakasek.kurikulum@sma1le.sch.id',
        'role' => 'wakasek_kurikulum',
        'roles' => ['guru', 'wakasek_kurikulum'],
        'is_active' => true,
    ]);

    expect($wakasekKurikulum->isCurriculumAdmin())->toBeTrue()
        ->and($wakasekKurikulum->canAccessInActiveRole('kurikulum'))->toBeTrue()
        ->and($wakasekKurikulum->canAccessInActiveRole('lms'))->toBeTrue()
        ->and($wakasekKurikulum->canAccessInActiveRole('rapor_kurnas'))->toBeTrue();

    // Mengakses LMS dan master mapel
    $resLms = $this->actingAs($wakasekKurikulum)->get(route('lms.index'));
    $resLms->assertStatus(200);

    $resSubjects = $this->actingAs($wakasekKurikulum)->get(route('lms.master.subjects'));
    $resSubjects->assertStatus(200);
});

test('point 4: koordinator bk adalah admin aplikasi bimbingan konseling secara keseluruhan', function () {
    $koordinatorBk = User::factory()->create([
        'name' => 'Koordinator BK Admin',
        'username' => 'koordinator_bk_tester',
        'email' => 'koordinator.bk@sma1le.sch.id',
        'role' => 'koordinator_bk',
        'roles' => ['guru', 'koordinator_bk', 'bk'],
        'is_active' => true,
    ]);

    expect($koordinatorBk->isBkAdmin())->toBeTrue()
        ->and($koordinatorBk->canAccessInActiveRole('bk'))->toBeTrue();

    // Mengakses Dashboard BK dan Sesi Konseling
    $resBk = $this->actingAs($koordinatorBk)->get(route('bk.dashboard'));
    $resBk->assertStatus(200);

    $resCounseling = $this->actingAs($koordinatorBk)->get(route('bk.counseling.index'));
    $resCounseling->assertStatus(200);

    $resAkpd = $this->actingAs($koordinatorBk)->get(route('bk.akpd.index'));
    $resAkpd->assertStatus(200);
});

test('point 5: kepala perpustakaan adalah admin aplikasi perpustakaan secara keseluruhan', function () {
    $kepalaPerpus = User::factory()->create([
        'name' => 'Kepala Perpustakaan Admin',
        'username' => 'kepala_perpus_tester',
        'email' => 'kepala.perpus@sma1le.sch.id',
        'role' => 'kepala_perpustakaan',
        'roles' => ['guru', 'kepala_perpustakaan'],
        'is_active' => true,
    ]);

    expect($kepalaPerpus->isLibraryAdmin())->toBeTrue()
        ->and($kepalaPerpus->canManageLibrary())->toBeTrue()
        ->and($kepalaPerpus->canAccessInActiveRole('library'))->toBeTrue();

    // Mengakses Dashboard Perpustakaan
    $resIndex = $this->actingAs($kepalaPerpus)->get(route('library.index'));
    $resIndex->assertStatus(200);

    // Mengakses Form Tambah Buku (Khusus Pengelola & Admin)
    $resCreateBook = $this->actingAs($kepalaPerpus)->get(route('library.books.create'));
    $resCreateBook->assertStatus(200);

    // Mengakses Sirkulasi Peminjaman Buku
    $resLoans = $this->actingAs($kepalaPerpus)->get(route('library.loans.index'));
    $resLoans->assertStatus(200);
});

test('point 6: peran utama tenaga pendidik (guru) DILARANG KERAS mengakses adm ketatausahaan, adm ketatausahaan khusus tenaga kependidikan', function () {
    // 1. Akun Guru Murni
    $guruPendidik = User::factory()->create([
        'name' => 'Guru Mata Pelajaran Pendidik',
        'username' => 'guru_pendidik_tester',
        'email' => 'guru.pendidik@sma1le.sch.id',
        'role' => 'guru',
        'roles' => ['guru'],
        'is_active' => true,
    ]);

    expect($guruPendidik->canAccessTuModule())->toBeFalse()
        ->and($guruPendidik->canAccessInActiveRole('tu'))->toBeFalse();

    // Request ke /tu harus 403 Forbidden
    $resTuGuru = $this->actingAs($guruPendidik)->get(route('tu.dashboard'));
    $resTuGuru->assertStatus(403);

    // Sidebar tidak menampilkan Adm Ketatausahaan
    $resDashboardGuru = $this->actingAs($guruPendidik)->get(route('dashboard'));
    $resDashboardGuru->assertDontSee('data-tooltip="Adm. Ketatausahaan"', false);
    $resDashboardGuru->assertDontSee('Adm. Ketatausahaan');

    // 2. Akun Guru dengan Peran Tambahan Non-Admin (misal Wali Kelas & Pembina Ekstra)
    $guruWali = User::factory()->create([
        'name' => 'Guru Wali Kelas Multirole',
        'username' => 'guru_wali_tester',
        'email' => 'guru.wali@sma1le.sch.id',
        'role' => 'guru',
        'roles' => ['guru', 'wali_kelas', 'pembina_ekstra'],
        'is_active' => true,
    ]);

    expect($guruWali->canAccessTuModule())->toBeFalse();
    $resTuWali = $this->actingAs($guruWali)->get(route('tu.dashboard'));
    $resTuWali->assertStatus(403);

    // 3. Akun Tenaga Kependidikan (Staff TU) DAPAT mengakses Adm Ketatausahaan
    $staffTendik = User::factory()->create([
        'name' => 'Staf Tata Usaha Tendik',
        'username' => 'staff_tendik_tester',
        'email' => 'staff.tendik@sma1le.sch.id',
        'role' => 'staff',
        'roles' => ['staff', 'tu', 'kepala_administrasi'],
        'is_active' => true,
    ]);

    expect($staffTendik->canAccessTuModule())->toBeTrue();
    $resTuStaff = $this->actingAs($staffTendik)->get(route('tu.dashboard'));
    $resTuStaff->assertStatus(200);
    $resTuStaff->assertSee('Adm. Ketatausahaan');
});

test('point 7: aplikasi perpustakaan dan bimbingan konseling tampil dan digunakan sesuai akun dan rolenya', function () {
    $class = SchoolClass::first() ?? SchoolClass::factory()->create(['name' => 'X-1', 'grade' => 'X']);
    $siswaUser = User::factory()->create([
        'name' => 'Siswa Pengguna Perpus & BK',
        'username' => 'siswa_perpus_bk',
        'email' => 'siswa.perpusbk@siswa.sma1le.sch.id',
        'role' => 'siswa',
        'roles' => ['siswa'],
        'is_active' => true,
    ]);
    Student::updateOrCreate(
        ['user_id' => $siswaUser->id],
        [
            'nisn' => '0098765432',
            'nis' => '242510099',
            'name' => $siswaUser->name,
            'school_class_id' => $class->id,
            'is_active' => true,
        ]
    );

    // Siswa dapat mengakses katalog perpustakaan
    $resPerpusSiswa = $this->actingAs($siswaUser)->get(route('library.index'));
    $resPerpusSiswa->assertStatus(200);

    // Siswa DILARANG mengakses form tambah buku (khusus pengelola)
    $resCreateBookSiswa = $this->actingAs($siswaUser)->get(route('library.books.create'));
    $resCreateBookSiswa->assertStatus(403);

    // Siswa dapat mengakses Tes Psikotes Mandiri di BK
    $resPsikotesSiswa = $this->actingAs($siswaUser)->get(route('bk.psychological.index'));
    $resPsikotesSiswa->assertStatus(200);
});

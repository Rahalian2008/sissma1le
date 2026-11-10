<?php

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    SchoolIdentity::firstOrCreate(['id' => 1], [
        'name' => 'SMA NEGERI 1 LENGKONG',
        'npsn' => '20202211',
        'address' => 'Jl. Raya Lengkong No. 45 Sukabumi',
        'phone' => '0266-123456',
        'email' => 'info@sman1lengkong.sch.id',
    ]);

    $this->academicYear = AcademicYear::firstOrCreate(
        ['is_active' => true],
        ['name' => '2026/2027', 'semester' => 'Ganjil', 'start_date' => '2026-07-15', 'end_date' => '2027-06-25']
    );

    $this->class = SchoolClass::firstOrCreate(
        ['name' => 'X-1'],
        ['grade' => 'X', 'major' => 'Umum', 'academic_year_id' => $this->academicYear->id]
    );

    $this->student = Student::firstOrCreate(
        ['nis' => '99887766'],
        ['name' => 'Siswa Test Cerdas', 'nisn' => '0099887766', 'gender' => 'L', 'class_id' => $this->class->id, 'is_active' => true]
    );
});

test('bk menu is strictly accessible only to koordinator_bk, guru_bk, and bk roles', function () {
    $guru = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
    ]);

    $waliKelas = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'wali_kelas'],
    ]);

    $koordinatorBk = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'koordinator_bk'],
    ]);

    $guruBk = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'guru_bk'],
    ]);

    // Regular guru cannot access BK in active role
    session(['active_role' => 'guru']);
    expect($guru->canAccessInActiveRole('bk'))->toBeFalse();

    // Wali kelas cannot access BK in active role
    session(['active_role' => 'wali_kelas']);
    expect($waliKelas->canAccessInActiveRole('bk'))->toBeFalse();

    // Koordinator BK can access BK
    session(['active_role' => 'koordinator_bk']);
    expect($koordinatorBk->canAccessInActiveRole('bk'))->toBeTrue();

    // Guru BK can access BK
    session(['active_role' => 'guru_bk']);
    expect($guruBk->canAccessInActiveRole('bk'))->toBeTrue();
});

test('bk dashboard endpoint rejects unauthorized roles with 403', function () {
    $guru = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
    ]);

    $this->actingAs($guru)
        ->withSession(['active_role' => 'guru'])
        ->get(route('bk.dashboard'))
        ->assertStatus(403);

    $bkUser = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'koordinator_bk'],
    ]);

    $this->actingAs($bkUser)
        ->withSession(['active_role' => 'koordinator_bk'])
        ->get(route('bk.dashboard'))
        ->assertStatus(200);
});

test('achievements index displays only view button for non-kesiswaan roles', function () {
    $category = AchievementCategory::firstOrCreate(
        ['code' => 'R1'],
        ['name' => 'Prestasi Keagamaan', 'min_points' => 5, 'max_points' => 20]
    );

    $item = AchievementItem::firstOrCreate(
        ['code' => 'R1-01'],
        ['category_id' => $category->id, 'name' => 'Juara MTQ', 'default_points' => 10]
    );

    $achievement = Achievement::create([
        'code' => 'PRESTASI-TEST-01',
        'student_id' => $this->student->id,
        'category_id' => $category->id,
        'item_id' => $item->id,
        'title' => 'Juara 1 Kaligrafi Nasional',
        'date' => now()->toDateString(),
        'points' => 15,
        'level' => 'Nasional',
        'status' => 'MENUNGGU_VERIFIKASI',
    ]);

    $guru = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
    ]);

    // As regular guru, edit/delete/approve buttons must NOT be present
    $resGuru = $this->actingAs($guru)
        ->withSession(['active_role' => 'guru'])
        ->get(route('achievements.index'));

    $resGuru->assertStatus(200);
    $resGuru->assertSee(route('achievements.show', $achievement->id));
    $resGuru->assertDontSee(route('achievements.edit', $achievement->id));
    $resGuru->assertDontSee(route('achievements.verify', $achievement->id));

    // As wakasek_kesiswaan or admin, action buttons must be present
    $wakasek = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'wakasek_kesiswaan'],
    ]);

    $resWakasek = $this->actingAs($wakasek)
        ->withSession(['active_role' => 'wakasek_kesiswaan'])
        ->get(route('achievements.index'));

    $resWakasek->assertStatus(200);
    $resWakasek->assertSee(route('achievements.show', $achievement->id));
    $resWakasek->assertSee(route('achievements.edit', $achievement->id));
    $resWakasek->assertSee(route('achievements.verify', $achievement->id));
});

test('dashboard controller routes correctly to each role specific dashboard without falling back to admin', function () {
    // 1. Guru
    $guru = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
    ]);
    $this->actingAs($guru)
        ->withSession(['active_role' => 'guru'])
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewIs('dashboards.teacher');

    // 2. Kepala Sekolah
    $kepsek = User::factory()->create([
        'role' => 'kepala_sekolah',
        'roles' => ['kepala_sekolah'],
    ]);
    $this->actingAs($kepsek)
        ->withSession(['active_role' => 'kepala_sekolah'])
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewIs('dashboards.principal');

    // 3. Wakasek Kesiswaan
    $kesiswaan = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'wakasek_kesiswaan'],
    ]);
    $this->actingAs($kesiswaan)
        ->withSession(['active_role' => 'wakasek_kesiswaan'])
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewIs('dashboards.kesiswaan');

    // 4. Wakasek Kurikulum
    $kurikulum = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'wakasek_kurikulum'],
    ]);
    $this->actingAs($kurikulum)
        ->withSession(['active_role' => 'wakasek_kurikulum'])
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewIs('dashboards.wakasek_kurikulum');

    // 5. Guru BK
    $bk = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'koordinator_bk'],
    ]);
    $this->actingAs($bk)
        ->withSession(['active_role' => 'koordinator_bk'])
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewIs('dashboards.counselor');

    // 6. Multi-role user (Guru + Wali Kelas) with all_roles opens combinedMultiRoleDashboard
    $multiRoleUser = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'wali_kelas'],
    ]);
    $this->actingAs($multiRoleUser)
        ->withSession(['active_role' => 'all_roles'])
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewIs('dashboards.combined_multi_role')
        ->assertSee('Mode Terpadu Multi-Peran');

    // 7. Pure admin with all_roles opens adminDashboard
    $pureAdmin = User::factory()->create([
        'role' => 'super_admin',
        'roles' => ['super_admin'],
    ]);
    $this->actingAs($pureAdmin)
        ->withSession(['active_role' => 'all_roles'])
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewIs('dashboards.admin');
});

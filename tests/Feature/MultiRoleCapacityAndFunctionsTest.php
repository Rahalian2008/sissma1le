<?php

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;

test('pendidik multi-role capacities and dynamic views operate seamlessly without error', function () {
    $class = SchoolClass::first() ?? SchoolClass::factory()->create(['name' => 'X-1']);

    $userPendidik = User::factory()->create([
        'name' => 'Bapak Guru Teladan Multirole',
        'username' => 'pendidik_multirole_tester',
        'email' => 'pendidik_multirole@sman1lengkong.sch.id',
        'role' => 'guru',
        'roles' => ['guru', 'wali_kelas', 'pembina_ekstra', 'bk', 'wakasek_kurikulum'],
        'is_active' => true,
    ]);

    Teacher::updateOrCreate(
        ['user_id' => $userPendidik->id],
        [
            'nip' => '198501012010011088',
            'name' => $userPendidik->name,
            'is_active' => true,
        ]
    );

    // Assert multi-role capabilities on model
    expect($userPendidik->all_roles)->toContain('guru', 'wali_kelas', 'pembina_ekstra', 'bk', 'wakasek_kurikulum')
        ->and($userPendidik->isTeacher())->toBeTrue()
        ->and($userPendidik->isHomeroom())->toBeTrue()
        ->and($userPendidik->isCounselor())->toBeTrue()
        ->and($userPendidik->isWakasekKurikulum())->toBeTrue()
        ->and($userPendidik->canManageExtracurricular())->toBeTrue();

    // 1. Default role (guru): dashboard renders teacher view
    $resGuru = $this->actingAs($userPendidik)->get(route('dashboard'));
    $resGuru->assertStatus(200);

    // 2. Switch to wali_kelas: dashboard renders homeroom view
    $switchWali = $this->actingAs($userPendidik)->post(route('user.switch_active_role'), ['role' => 'wali_kelas']);
    $switchWali->assertRedirect(route('dashboard'));
    expect(session('active_role'))->toBe('wali_kelas');
    $resWali = $this->actingAs($userPendidik)->get(route('dashboard'));
    $resWali->assertStatus(200);
    $resWali->assertSee('Wali Kelas');

    // 3. Switch to bk: dashboard renders counselor view
    $switchBk = $this->actingAs($userPendidik)->post(route('user.switch_active_role'), ['role' => 'bk']);
    $switchBk->assertRedirect(route('dashboard'));
    expect(session('active_role'))->toBe('bk');
    $resBk = $this->actingAs($userPendidik)->get(route('dashboard'));
    $resBk->assertStatus(200);

    // 4. Switch to wakasek_kurikulum: dashboard renders curriculum view
    $switchKurnas = $this->actingAs($userPendidik)->post(route('user.switch_active_role'), ['role' => 'wakasek_kurikulum']);
    $switchKurnas->assertRedirect(route('dashboard'));
    expect(session('active_role'))->toBe('wakasek_kurikulum');
    $resKurnas = $this->actingAs($userPendidik)->get(route('dashboard'));
    $resKurnas->assertStatus(200);

    // 5. Switch to pembina_ekstra: dashboard redirects to extracurricular.index
    $switchEkstra = $this->actingAs($userPendidik)->post(route('user.switch_active_role'), ['role' => 'pembina_ekstra']);
    $switchEkstra->assertRedirect(route('dashboard'));
    expect(session('active_role'))->toBe('pembina_ekstra');
    $resEkstra = $this->actingAs($userPendidik)->get(route('dashboard'));
    $resEkstra->assertRedirect(route('extracurricular.index'));

    // 6. Verify cross-role access: user can access all assigned role modules without 403 or 500
    $modules = [
        route('lms.index'),
        route('extracurricular.index'),
        route('attendance.index'),
        route('bk.dashboard'),
        route('raport.index'),
    ];

    foreach ($modules as $url) {
        $resModule = $this->actingAs($userPendidik)->get($url);
        expect($resModule->status())->toBeIn([200, 302]);
    }
});

test('tendik multi-role capacities and dynamic views operate seamlessly without error', function () {
    $userTendik = User::factory()->create([
        'name' => 'Ibu Staf Tata Usaha Multirole',
        'username' => 'tendik_multirole_tester',
        'email' => 'tendik_multirole@sman1lengkong.sch.id',
        'role' => 'staff',
        'roles' => ['staff', 'kepala_administrasi', 'bendahara_bosp', 'ta_kesiswaan', 'ta_persuratan', 'tu_piket'],
        'is_active' => true,
    ]);

    // Assert tendik multi-role capabilities
    expect($userTendik->all_roles)->toContain('staff', 'kepala_administrasi', 'bendahara_bosp', 'ta_kesiswaan', 'ta_persuratan', 'tu_piket')
        ->and($userTendik->isKepalaAdministrasi())->toBeTrue()
        ->and($userTendik->isBendaharaBosp())->toBeTrue()
        ->and($userTendik->isTaKesiswaan())->toBeTrue()
        ->and($userTendik->isTaPersuratan())->toBeTrue()
        ->and($userTendik->isTuPiket())->toBeTrue();

    // 1. Switch to kepala_administrasi -> redirects to tu.dashboard
    $switchKaTu = $this->actingAs($userTendik)->post(route('user.switch_active_role'), ['role' => 'kepala_administrasi']);
    $switchKaTu->assertRedirect(route('dashboard'));
    $resKaTu = $this->actingAs($userTendik)->get(route('dashboard'));
    $resKaTu->assertRedirect(route('tu.dashboard'));

    // 2. Switch to ta_kesiswaan -> redirects to tu.kesiswaan.index
    $switchKesiswaan = $this->actingAs($userTendik)->post(route('user.switch_active_role'), ['role' => 'ta_kesiswaan']);
    $switchKesiswaan->assertRedirect(route('dashboard'));
    $resKesiswaan = $this->actingAs($userTendik)->get(route('dashboard'));
    $resKesiswaan->assertRedirect(route('tu.kesiswaan.index'));

    // 3. Switch to ta_persuratan -> redirects to tu.persuratan.index
    $switchPersuratan = $this->actingAs($userTendik)->post(route('user.switch_active_role'), ['role' => 'ta_persuratan']);
    $switchPersuratan->assertRedirect(route('dashboard'));
    $resPersuratan = $this->actingAs($userTendik)->get(route('dashboard'));
    $resPersuratan->assertRedirect(route('tu.persuratan.index'));

    // 4. Switch to bendahara_bosp -> redirects to tu.keuangan.index
    $switchKeuangan = $this->actingAs($userTendik)->post(route('user.switch_active_role'), ['role' => 'bendahara_bosp']);
    $switchKeuangan->assertRedirect(route('dashboard'));
    $resKeuangan = $this->actingAs($userTendik)->get(route('dashboard'));
    $resKeuangan->assertRedirect(route('tu.keuangan.index'));

    // 5. Switch to tu_piket -> redirects to attendance.index
    $switchPiket = $this->actingAs($userTendik)->post(route('user.switch_active_role'), ['role' => 'tu_piket']);
    $switchPiket->assertRedirect(route('dashboard'));
    $resPiket = $this->actingAs($userTendik)->get(route('dashboard'));
    $resPiket->assertRedirect(route('attendance.index'));

    // 6. Verify TU submenus open without 500
    $tuRoutes = [
        route('tu.dashboard'),
        route('tu.kesiswaan.index'),
        route('tu.kepegawaian.index'),
        route('tu.persuratan.index'),
        route('tu.sarpras.index'),
        route('tu.keuangan.index'),
        route('spmb.index'),
    ];

    foreach ($tuRoutes as $url) {
        $resTu = $this->actingAs($userTendik)->get($url);
        expect($resTu->status())->toBeIn([200, 302]);
    }
});

test('perpustakaan roles route cleanly to library index', function () {
    $userPustaka = User::factory()->create([
        'name' => 'Kepala Perpustakaan Hebat',
        'username' => 'kepala_pustaka_tester',
        'email' => 'pustaka@sman1lengkong.sch.id',
        'role' => 'guru',
        'roles' => ['guru', 'kepala_perpustakaan'],
        'is_active' => true,
    ]);

    expect($userPustaka->isKepalaPerpustakaan())->toBeTrue()
        ->and($userPustaka->canManageLibrary())->toBeTrue();

    // Switch to kepala_perpustakaan
    $this->actingAs($userPustaka)->post(route('user.switch_active_role'), ['role' => 'kepala_perpustakaan']);
    $res = $this->actingAs($userPustaka)->get(route('dashboard'));
    $res->assertRedirect(route('library.index'));

    $resLibrary = $this->actingAs($userPustaka)->get(route('library.index'));
    $resLibrary->assertStatus(200);
});

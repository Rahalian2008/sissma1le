<?php

use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;

test('a user with 3 roles can access all functions of all 3 assigned roles without error', function () {
    // 1. Create or fetch roles
    $roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator', 'level' => 2]);
    $roleKesiswaan = Role::firstOrCreate(['name' => 'wakasek_kesiswaan'], ['display_name' => 'Wakasek Kesiswaan', 'level' => 4]);
    $roleGuru = Role::firstOrCreate(['name' => 'guru'], ['display_name' => 'Guru Mata Pelajaran', 'level' => 6]);

    // 2. Create 3-role user (e.g. Winda Dwi Atyani)
    $user = User::factory()->create([
        'name' => 'Ibu Winda Multi Role',
        'username' => 'winda_test_multirole',
        'email' => 'winda_test@sman1lengkong.sch.id',
        'role' => 'admin',
        'roles' => ['guru', 'wakasek_kesiswaan', 'admin'],
        'is_active' => true,
        'active_role_id' => $roleAdmin->id,
    ]);
    $user->roles()->sync([$roleAdmin->id, $roleKesiswaan->id, $roleGuru->id]);

    $teacher = Teacher::updateOrCreate(
        ['nip' => '197911162008012007'],
        [
            'user_id' => $user->id,
            'name' => $user->name,
            'is_active' => true,
        ]
    );

    // Assert user model recognizes all 3 roles
    expect($user->all_roles)->toContain('admin', 'wakasek_kesiswaan', 'guru')
        ->and($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasRole('wakasek_kesiswaan'))->toBeTrue()
        ->and($user->hasRole('guru'))->toBeTrue()
        ->and($user->isAdmin())->toBeTrue()
        ->and($user->isTeacher())->toBeTrue()
        ->and($user->isStudentAffairs())->toBeTrue();

    // 3. Test default dashboard access (admin)
    $resDefault = $this->actingAs($user)->get(route('dashboard'));
    $resDefault->assertStatus(200);

    // 4. Test switching active role to wakasek_kesiswaan
    $switchKesiswaan = $this->actingAs($user)->post(route('user.switch_active_role'), [
        'role' => 'wakasek_kesiswaan',
    ]);
    $switchKesiswaan->assertRedirect(route('dashboard'));
    expect(session('active_role'))->toBe('wakasek_kesiswaan');

    $resKesiswaan = $this->actingAs($user)->get(route('dashboard'));
    $resKesiswaan->assertStatus(200);
    $resKesiswaan->assertSee('Kesiswaan');

    // 5. Test switching active role to guru
    $switchGuru = $this->actingAs($user)->post(route('user.switch_active_role'), [
        'role' => 'guru',
    ]);
    $switchGuru->assertRedirect(route('dashboard'));
    expect(session('active_role'))->toBe('guru');

    $resGuru = $this->actingAs($user)->get(route('dashboard'));
    $resGuru->assertStatus(200);
    $resGuru->assertSee('Guru');

    // 6. Test switching back to admin
    $switchAdmin = $this->actingAs($user)->post(route('user.switch_active_role'), [
        'role' => 'admin',
    ]);
    $switchAdmin->assertRedirect(route('dashboard'));
    expect(session('active_role'))->toBe('admin');

    $resAdmin = $this->actingAs($user)->get(route('dashboard'));
    $resAdmin->assertStatus(200);

    // 7. Test invalid role switch rejection
    $switchInvalid = $this->actingAs($user)->post(route('user.switch_active_role'), [
        'role' => 'super_admin',
    ]);
    $switchInvalid->assertSessionHas('error');
});

test('user management controller supports creating and updating users with 3 roles seamlessly', function () {
    $roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator', 'level' => 2]);
    $roleTu = Role::firstOrCreate(['name' => 'tu'], ['display_name' => 'Staf Tata Usaha', 'level' => 7]);
    $roleStaff = Role::firstOrCreate(['name' => 'staff'], ['display_name' => 'Staf Pegawai', 'level' => 7]);

    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'roles' => ['super_admin'],
        'is_active' => true,
    ]);

    // Store a new user with 3 roles
    $postResponse = $this->actingAs($superAdmin)->post(route('users.store'), [
        'name' => 'Rachmat Sugiarto Test',
        'username' => '198112282025211059_test',
        'nip' => '198112282025211059',
        'email' => 'rachmat_test@sman1lengkong.sch.id',
        'password' => 'password123',
        'phone' => '081234567890',
        'role_ids' => [$roleAdmin->id, $roleTu->id, $roleStaff->id],
    ]);
    $postResponse->assertRedirect();
    $postResponse->assertSessionHas('success');

    $createdUser = User::where('username', '198112282025211059_test')->first();
    expect($createdUser)->not->toBeNull()
        ->and($createdUser->all_roles)->toContain('admin', 'tu', 'staff')
        ->and($createdUser->hasRole('admin'))->toBeTrue()
        ->and($createdUser->hasRole('tu'))->toBeTrue()
        ->and($createdUser->hasRole('staff'))->toBeTrue()
        ->and($createdUser->active_role_id)->toBe($roleAdmin->id);

    // Update user: change to different combination of 3 roles (e.g. tu, bendahara, staff)
    $roleBendahara = Role::firstOrCreate(['name' => 'bendahara'], ['display_name' => 'Bendahara Sekolah', 'level' => 6]);

    $putResponse = $this->actingAs($superAdmin)->put(route('users.update', $createdUser), [
        'name' => 'Rachmat Sugiarto Updated',
        'username' => '198112282025211059_test',
        'nip' => '198112282025211059',
        'email' => 'rachmat_test@sman1lengkong.sch.id',
        'phone' => '081234567890',
        'is_active' => '1',
        'role_ids' => [$roleTu->id, $roleBendahara->id, $roleStaff->id],
    ]);
    $putResponse->assertRedirect();
    $putResponse->assertSessionHas('success');

    $createdUser->refresh();
    expect($createdUser->all_roles)->toContain('tu', 'bendahara', 'staff')
        ->and($createdUser->hasRole('tu'))->toBeTrue()
        ->and($createdUser->hasRole('bendahara'))->toBeTrue()
        ->and($createdUser->hasRole('staff'))->toBeTrue();
});

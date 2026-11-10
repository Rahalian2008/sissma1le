<?php

use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('primary role is strictly mapped and additional roles are synchronized', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'username' => 'superadmin_test',
        'email' => 'admin_test@sman1lengkong.sch.id',
    ]);

    // 1. Create a Teacher (Tenaga Pendidik) with additional roles: kepala_sekolah, wali_kelas, bk, pembina_ekstra
    $response = $this->actingAs($admin)->post(route('master.teachers.store'), [
        'name' => 'Dr. H. Guru Teladan, M.Pd.',
        'nip' => '198501012010011099',
        'gender' => 'L',
        'title' => 'Guru Pengajar Utama',
        'phone' => '081234567899',
        'role' => 'guru',
        'roles' => ['kepala_sekolah', 'wali_kelas', 'bk', 'pembina_ekstra'],
        'email' => 'guruteladan@sman1lengkong.sch.id',
        'username' => 'guruteladan',
        'password' => 'password123',
    ]);

    $response->assertSessionHasNoErrors();
    $teacher = Teacher::where('nip', '198501012010011099')->first();
    expect($teacher)->not->toBeNull();
    expect($teacher->user)->not->toBeNull();
    expect($teacher->user->role)->toBe('guru');
    expect($teacher->user->roles)->toContain('guru');
    expect($teacher->user->roles)->toContain('kepala_sekolah');
    expect($teacher->user->roles)->toContain('wali_kelas');
    expect($teacher->user->roles)->toContain('bk');
    expect($teacher->user->roles)->toContain('pembina_ekstra');

    // 2. Update Teacher to Staff (Tenaga Kependidikan) with roles: kepala_administrasi, bendahara_bosp, operator_dapodik
    $updateResponse = $this->actingAs($admin)->put(route('master.teachers.update', $teacher), [
        'name' => 'Dr. H. Guru Teladan, M.Pd.',
        'nip' => '198501012010011099',
        'gender' => 'L',
        'title' => 'Staf Tata Usaha',
        'phone' => '081234567899',
        'role' => 'staff',
        'roles' => ['kepala_administrasi', 'bendahara_bosp', 'operator_dapodik'],
        'email' => 'guruteladan@sman1lengkong.sch.id',
        'is_active' => '1',
    ]);

    $updateResponse->assertSessionHasNoErrors();
    $teacher->refresh();
    expect($teacher->user->role)->toBe('staff');
    expect($teacher->user->roles)->toContain('staff');
    expect($teacher->user->roles)->toContain('kepala_administrasi');
    expect($teacher->user->roles)->toContain('bendahara_bosp');
    expect($teacher->user->roles)->toContain('operator_dapodik');

    // 3. Create a User via UserController with role staff and multiple roles
    $userStoreResponse = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Staf Keuangan Hebat',
        'username' => 'stafkeuangan',
        'nip' => '199002022015022088',
        'email' => 'stafkeuangan@sman1lengkong.sch.id',
        'password' => 'password123',
        'phone' => '089876543210',
        'role' => 'staff',
        'roles' => ['bendahara_bosp', 'bendahara_bopd', 'ta_sarpras'],
    ]);

    $userStoreResponse->assertSessionHasNoErrors();
    $newUser = User::where('username', 'stafkeuangan')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->role)->toBe('staff');
    expect($newUser->roles)->toContain('staff');
    expect($newUser->roles)->toContain('bendahara_bosp');
    expect($newUser->roles)->toContain('bendahara_bopd');
    expect($newUser->roles)->toContain('ta_sarpras');
});

test('role switching route works across assigned roles for multi-role user', function () {
    $roleGuru = Role::where('name', 'guru')->first();
    $roleKepsek = Role::where('name', 'kepala_sekolah')->first();
    $roleWali = Role::where('name', 'wali_kelas')->first();

    $user = User::factory()->create([
        'username' => 'multirole_tester',
        'role' => 'guru',
        'roles' => ['guru', 'kepala_sekolah', 'wali_kelas'],
        'active_role_id' => $roleGuru->id,
    ]);
    $user->roles()->sync([$roleGuru->id, $roleKepsek->id, $roleWali->id]);

    $response = $this->actingAs($user)->post(route('user.switch_active_role'), [
        'role' => 'kepala_sekolah',
    ]);

    $response->assertRedirect();
    $user->refresh();
    expect($user->active_role_id)->toBe($roleKepsek->id);

    $response2 = $this->actingAs($user)->post(route('user.switch_active_role'), [
        'role' => 'wali_kelas',
    ]);

    $response2->assertRedirect();
    $user->refresh();
    expect($user->active_role_id)->toBe($roleWali->id);
});

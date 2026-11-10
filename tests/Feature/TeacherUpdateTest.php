<?php

use App\Models\Teacher;
use App\Models\User;

test('updating teacher syncs employee record without error', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $user = User::firstOrCreate(
        ['email' => 'guru_update_test@sman1lengkong.sch.id'],
        ['name' => 'Guru Test Update', 'password' => bcrypt('password'), 'role' => 'guru', 'is_active' => true]
    );

    $teacher = Teacher::firstOrCreate(
        ['user_id' => $user->id],
        [
            'nip' => '198501012010011099',
            'nuptk' => '1234567890123499',
            'name' => 'Guru Test Update',
            'gender' => 'L',
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($admin)->put(route('master.teachers.update', $teacher), [
        'name' => 'Guru Test Update Updated',
        'email' => 'guru_update_test@sman1lengkong.sch.id',
        'role' => 'guru',
        'roles' => ['guru'],
        'nip' => '198501012010011099',
        'nuptk' => '1234567890123499',
        'nik' => '3202111111110099',
        'gender' => 'L',
        'employment_type' => 'Guru',
        'employment_status' => 'PNS',
        'is_active' => 1,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertDatabaseHas('teachers', [
        'id' => $teacher->id,
        'name' => 'Guru Test Update Updated',
    ]);

    $this->assertDatabaseHas('employees', [
        'user_id' => $user->id,
        'nip' => '198501012010011099',
        'name' => strtoupper('Guru Test Update Updated'),
    ]);
});

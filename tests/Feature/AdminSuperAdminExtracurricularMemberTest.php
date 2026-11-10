<?php

use App\Models\Extracurricular;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::today()->setTime(16, 0, 0));

    // Admin user
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.test.member@sma1le.sch.id'],
        [
            'name' => 'Admin Penguji Ekstra',
            'username' => 'adm_eks_test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
            'is_active' => true,
        ]
    );

    // Super Admin user
    $this->superAdmin = User::firstOrCreate(
        ['email' => 'superadmin.test.member@sma1le.sch.id'],
        [
            'name' => 'Super Admin Penguji Ekstra',
            'username' => 'sa_eks_test',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'roles' => ['super_admin'],
            'is_active' => true,
        ]
    );

    // Teacher
    $this->teacherUser = User::firstOrCreate(
        ['email' => 'guru.test.member@sma1le.sch.id'],
        [
            'name' => 'Guru Pembina Test',
            'username' => 'guru_eks_test',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'roles' => ['guru', 'pembina_ekstra'],
            'is_active' => true,
        ]
    );

    $this->teacher = Teacher::firstOrCreate(
        ['nip' => '198901012015011001'],
        [
            'user_id' => $this->teacherUser->id,
            'name' => 'Guru Pembina Test',
            'gender' => 'L',
            'title' => 'Pembina PMR',
            'is_active' => true,
        ]
    );

    // Extracurricular
    $this->extra = Extracurricular::firstOrCreate(
        ['code' => 'PMR_TEST'],
        [
            'name' => 'Palang Merah Remaja Unit Test',
            'category' => 'Kesehatan & Kemanusiaan',
            'teacher_id' => $this->teacher->id,
            'schedule_day' => Carbon::now()->locale('id')->translatedFormat('l'),
            'start_time' => '15:00:00',
            'end_time' => '17:00:00',
            'location' => 'Ruang UKS',
            'is_active' => true,
            'description' => 'Ekskul PMR untuk pengujian.',
        ]
    );

    // School Class
    $this->schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'X-TEST'],
        [
            'grade' => 'X',
            'major' => 'Fase E',
            'is_active' => true,
        ]
    );

    // Two Test Students
    $this->studentUser1 = User::firstOrCreate(
        ['email' => 'siswa1.test.member@sma1le.sch.id'],
        [
            'name' => 'Siswa Penguji Satu',
            'username' => '0098765431',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'roles' => ['siswa'],
            'is_active' => true,
        ]
    );

    $this->student1 = Student::firstOrCreate(
        ['nisn' => '0098765431'],
        [
            'user_id' => $this->studentUser1->id,
            'class_id' => $this->schoolClass->id,
            'name' => 'Siswa Penguji Satu',
            'nis' => '19001',
            'gender' => 'L',
            'is_active' => true,
        ]
    );

    $this->studentUser2 = User::firstOrCreate(
        ['email' => 'siswa2.test.member@sma1le.sch.id'],
        [
            'name' => 'Siswa Penguji Dua',
            'username' => '0098765432',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'roles' => ['siswa'],
            'is_active' => true,
        ]
    );

    $this->student2 = Student::firstOrCreate(
        ['nisn' => '0098765432'],
        [
            'user_id' => $this->studentUser2->id,
            'class_id' => $this->schoolClass->id,
            'name' => 'Siswa Penguji Dua',
            'nis' => '19002',
            'gender' => 'P',
            'is_active' => true,
        ]
    );
});

afterEach(function () {
    Carbon::setTestNow();
});

test('admin can add, update role, and remove extracurricular member via master route', function () {
    // 1. Admin adds student1
    $response = $this->actingAs($this->admin)->post(route('master.extracurriculars.members.add', $this->extra->id), [
        'student_id' => $this->student1->id,
        'role' => 'Anggota',
    ]);

    $response->assertRedirect(route('master.extracurriculars', ['open_members' => $this->extra->id]));
    $response->assertSessionHas('success');
    expect($this->extra->students()->where('student_id', $this->student1->id)->exists())->toBeTrue();
    expect($this->extra->students()->where('student_id', $this->student1->id)->first()->pivot->role)->toBe('Anggota');

    // 2. Admin updates student1 role to Sekretaris
    $roleResponse = $this->actingAs($this->admin)->put(route('master.extracurriculars.members.role', [$this->extra->id, $this->student1->id]), [
        'role' => 'Sekretaris',
    ]);

    $roleResponse->assertRedirect(route('master.extracurriculars', ['open_members' => $this->extra->id]));
    $this->studentUser1->refresh();
    expect($this->studentUser1->hasRole('sekretaris_ekstra'))->toBeTrue();

    // 3. Admin removes student1
    $removeResponse = $this->actingAs($this->admin)->delete(route('master.extracurriculars.members.remove', [$this->extra->id, $this->student1->id]));
    $removeResponse->assertRedirect(route('master.extracurriculars', ['open_members' => $this->extra->id]));
    expect($this->extra->students()->where('student_id', $this->student1->id)->exists())->toBeFalse();
});

test('super admin can add, update role, and remove extracurricular member via master route', function () {
    // 1. Super Admin adds student2
    $response = $this->actingAs($this->superAdmin)->post(route('master.extracurriculars.members.add', $this->extra->id), [
        'student_id' => $this->student2->id,
        'role' => 'Ketua',
    ]);

    $response->assertRedirect(route('master.extracurriculars', ['open_members' => $this->extra->id]));
    $response->assertSessionHas('success');
    expect($this->extra->students()->where('student_id', $this->student2->id)->exists())->toBeTrue();
    expect($this->extra->students()->where('student_id', $this->student2->id)->first()->pivot->role)->toBe('Ketua');

    // 2. Super Admin updates role
    $roleResponse = $this->actingAs($this->superAdmin)->put(route('master.extracurriculars.members.role', [$this->extra->id, $this->student2->id]), [
        'role' => 'Bendahara',
    ]);

    $roleResponse->assertRedirect(route('master.extracurriculars', ['open_members' => $this->extra->id]));
    expect($this->extra->students()->where('student_id', $this->student2->id)->first()->pivot->role)->toBe('Bendahara');

    // 3. Super Admin removes member
    $removeResponse = $this->actingAs($this->superAdmin)->delete(route('master.extracurriculars.members.remove', [$this->extra->id, $this->student2->id]));
    $removeResponse->assertRedirect(route('master.extracurriculars', ['open_members' => $this->extra->id]));
    expect($this->extra->students()->where('student_id', $this->student2->id)->exists())->toBeFalse();
});

test('admin can add, update role, and remove extracurricular member via attendance route', function () {
    // 1. Admin adds student1 via /extracurricular/{id}/members
    $response = $this->actingAs($this->admin)->post(route('extracurricular.members.add', $this->extra->id), [
        'student_id' => $this->student1->id,
        'role' => 'Anggota',
    ]);

    $response->assertRedirect(route('extracurricular.index', [
        'extra_id' => $this->extra->id,
        'tab' => 'members',
    ]));
    $response->assertSessionHas('success');
    expect($this->extra->students()->where('student_id', $this->student1->id)->exists())->toBeTrue();

    // 2. Admin updates role via /extracurricular/{id}/members/{studentId}/role
    $roleResponse = $this->actingAs($this->admin)->put(route('extracurricular.members.role', [$this->extra->id, $this->student1->id]), [
        'role' => 'Sekretaris',
    ]);

    $roleResponse->assertRedirect(route('extracurricular.index', [
        'extra_id' => $this->extra->id,
        'tab' => 'members',
    ]));
    $this->studentUser1->refresh();
    expect($this->studentUser1->hasRole('sekretaris_ekstra'))->toBeTrue();

    // 3. Admin removes member via /extracurricular/{id}/members/{studentId}
    $removeResponse = $this->actingAs($this->admin)->delete(route('extracurricular.members.remove', [$this->extra->id, $this->student1->id]));
    $removeResponse->assertRedirect(route('extracurricular.index', [
        'extra_id' => $this->extra->id,
        'tab' => 'members',
    ]));
    expect($this->extra->students()->where('student_id', $this->student1->id)->exists())->toBeFalse();
});

test('super admin can add, update role, and remove extracurricular member via attendance route', function () {
    // 1. Super Admin adds student2 via /extracurricular/{id}/members
    $response = $this->actingAs($this->superAdmin)->post(route('extracurricular.members.add', $this->extra->id), [
        'student_id' => $this->student2->id,
        'role' => 'Ketua',
    ]);

    $response->assertRedirect(route('extracurricular.index', [
        'extra_id' => $this->extra->id,
        'tab' => 'members',
    ]));
    $response->assertSessionHas('success');
    expect($this->extra->students()->where('student_id', $this->student2->id)->exists())->toBeTrue();

    // 2. Super Admin updates role
    $roleResponse = $this->actingAs($this->superAdmin)->put(route('extracurricular.members.role', [$this->extra->id, $this->student2->id]), [
        'role' => 'Wakil Ketua',
    ]);

    $roleResponse->assertRedirect(route('extracurricular.index', [
        'extra_id' => $this->extra->id,
        'tab' => 'members',
    ]));
    expect($this->extra->students()->where('student_id', $this->student2->id)->first()->pivot->role)->toBe('Wakil Ketua');

    // 3. Super Admin removes member
    $removeResponse = $this->actingAs($this->superAdmin)->delete(route('extracurricular.members.remove', [$this->extra->id, $this->student2->id]));
    $removeResponse->assertRedirect(route('extracurricular.index', [
        'extra_id' => $this->extra->id,
        'tab' => 'members',
    ]));
    expect($this->extra->students()->where('student_id', $this->student2->id)->exists())->toBeFalse();
});

test('admin and super admin see member management controls on extracurricular and master pages', function () {
    // Check for admin
    $adminExtraRes = $this->actingAs($this->admin)->get(route('extracurricular.index', ['extra_id' => $this->extra->id]));
    $adminExtraRes->assertStatus(200);
    $adminExtraRes->assertSee('Kelola Anggota');
    $adminExtraRes->assertSee('Tambah Anggota Siswa ke Ekstrakurikuler');

    $adminMasterRes = $this->actingAs($this->admin)->get(route('master.extracurriculars'));
    $adminMasterRes->assertStatus(200);
    $adminMasterRes->assertSee('+ Kelola Anggota');
    $adminMasterRes->assertSee('Kelola Anggota Ekstra:');

    // Check for super admin
    $saExtraRes = $this->actingAs($this->superAdmin)->get(route('extracurricular.index', ['extra_id' => $this->extra->id]));
    $saExtraRes->assertStatus(200);
    $saExtraRes->assertSee('Kelola Anggota');
    $saExtraRes->assertSee('Tambah Anggota Siswa ke Ekstrakurikuler');

    $saMasterRes = $this->actingAs($this->superAdmin)->get(route('master.extracurriculars'));
    $saMasterRes->assertStatus(200);
    $saMasterRes->assertSee('+ Kelola Anggota');
    $saMasterRes->assertSee('Kelola Anggota Ekstra:');
});

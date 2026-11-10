<?php

use App\Models\Employee;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

test('master data students supports rfid uids and family data', function () {
    $students = Student::all();
    expect($students->count())->toBeGreaterThan(0);

    // If running on production MySQL, should be exactly 798
    if (config('database.default') === 'mysql') {
        expect(Student::count())->toBe(798);
        expect(Student::whereNotNull('rfid_uid')->where('rfid_uid', '!=', '')->count())->toBe(798);
    }
});

test('master data teachers supports teachers and staff', function () {
    $teachers = Teacher::all();
    expect($teachers->count())->toBeGreaterThan(0);

    if (config('database.default') === 'mysql') {
        expect(Teacher::count())->toBeGreaterThanOrEqual(34);
    }
});

test('adm kesiswaan can pull from master and clear on demand', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $masterCount = Student::where('is_active', true)->count();
    expect($masterCount)->toBeGreaterThan(0);

    // 1. Initial / clear state
    $this->actingAs($admin)->post(route('tu.kesiswaan.clear'))
        ->assertRedirect(route('tu.kesiswaan.index'))
        ->assertSessionHas('success');

    expect(Student::where('is_tu_registered', true)->count())->toBe(0);

    // Page loads and shows empty state with pull button
    $response = $this->actingAs($admin)->get(route('tu.kesiswaan.index'));
    $response->assertStatus(200);
    $response->assertSee('Data di Administrasi Kesiswaan Saat Ini Sedang Dikosongkan');
    $response->assertSee('Tarik Data dari Master');

    // 2. Pull from master
    $this->actingAs($admin)->post(route('tu.kesiswaan.pull_from_master'))
        ->assertRedirect(route('tu.kesiswaan.index'))
        ->assertSessionHas('success');

    expect(Student::where('is_tu_registered', true)->count())->toBe($masterCount);

    // Page loads and shows active students
    $responseAfter = $this->actingAs($admin)->get(route('tu.kesiswaan.index'));
    $responseAfter->assertStatus(200);
    $responseAfter->assertSee((string) $masterCount);

    // 3. Re-clear as requested
    $this->actingAs($admin)->post(route('tu.kesiswaan.clear'))
        ->assertRedirect(route('tu.kesiswaan.index'));
    expect(Student::where('is_tu_registered', true)->count())->toBe(0);
});

test('adm kepegawaian can pull from master and clear on demand', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $masterTeacherCount = Teacher::where('is_active', true)->count();
    expect($masterTeacherCount)->toBeGreaterThan(0);

    // 1. Initial / clear state
    $this->actingAs($admin)->post(route('tu.kepegawaian.clear'))
        ->assertRedirect(route('tu.kepegawaian.index'))
        ->assertSessionHas('success');

    expect(Employee::count())->toBe(0);

    // Page loads and shows empty state with pull button
    $response = $this->actingAs($admin)->get(route('tu.kepegawaian.index'));
    $response->assertStatus(200);
    $response->assertSee('Data di Administrasi Kepegawaian Saat Ini Sedang Dikosongkan');
    $response->assertSee('Tarik Data dari Master');

    // 2. Pull from master
    $this->actingAs($admin)->post(route('tu.kepegawaian.pull_from_master'))
        ->assertRedirect(route('tu.kepegawaian.index'))
        ->assertSessionHas('success');

    $syncedEmployeeCount = Employee::count();
    expect($syncedEmployeeCount)->toBeGreaterThan(0);

    // Page loads and shows employees
    $responseAfter = $this->actingAs($admin)->get(route('tu.kepegawaian.index'));
    $responseAfter->assertStatus(200);
    $responseAfter->assertSee((string) $syncedEmployeeCount);

    // 3. Re-clear as requested
    $this->actingAs($admin)->post(route('tu.kepegawaian.clear'))
        ->assertRedirect(route('tu.kepegawaian.index'));
    expect(Employee::count())->toBe(0);
});

test('master data teacher can be updated via PUT /master/teachers/{id} and syncs employee record without class not found error', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $teacher = Teacher::with('user')->first();
    expect($teacher)->not->toBeNull();
    expect($teacher->user)->not->toBeNull();

    $response = $this->actingAs($admin)->put(route('master.teachers.update', $teacher), [
        'name' => $teacher->name,
        'nip' => $teacher->nip,
        'nuptk' => $teacher->nuptk,
        'nik' => $teacher->nik,
        'title_prefix' => $teacher->title_prefix,
        'title_suffix' => $teacher->title_suffix,
        'gender' => $teacher->gender ?: 'L',
        'birth_place' => $teacher->birth_place ?: 'Sukabumi',
        'birth_date' => $teacher->birth_date ? $teacher->birth_date->format('Y-m-d') : '1980-01-01',
        'employment_type' => $teacher->employment_type ?: 'Guru',
        'employment_status' => $teacher->employment_status ?: 'PNS',
        'rank_group' => $teacher->rank_group ?: 'IV/a',
        'position' => $teacher->position ?: 'Guru Ahli Madya',
        'teaching_subject' => $teacher->teaching_subject ?: 'Matematika',
        'role' => $teacher->user->role ?: 'teacher',
        'roles' => is_array($teacher->user->roles) ? $teacher->user->roles : ['teacher'],
        'email' => $teacher->user->email,
        'is_active' => 1,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify employee record exists and matches
    $employee = Employee::where('user_id', $teacher->user->id)->first();
    expect($employee)->not->toBeNull();
    expect($employee->name)->toBe(mb_strtoupper($teacher->name));
});

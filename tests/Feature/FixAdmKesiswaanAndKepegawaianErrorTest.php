<?php

use App\Models\Employee;
use App\Models\EmployeeHistory;
use App\Models\Student;
use App\Models\User;

test('test reported routes for 500 error diagnostics', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $routes = [
        'students.index' => route('students.index'),
        'buku-induk.index' => route('buku-induk.index'),
        'students.cohort.index' => route('students.cohort.index'),
        'employees.buku-induk.index' => route('employees.buku-induk.index'),
        'employees.kontrol-pangkat.index' => route('employees.kontrol-pangkat.index'),
        'employees.kontrol-kgb.index' => route('employees.kontrol-kgb.index'),
        'employees.kontrol-pppk.index' => route('employees.kontrol-pppk.index'),
        'employees.kontrol-cuti.index' => route('employees.kontrol-cuti.index'),
        'employees.kontrol-pensiun.index' => route('employees.kontrol-pensiun.index'),
    ];

    $errors = [];

    foreach ($routes as $name => $url) {
        try {
            $response = $this->actingAs($admin)->get($url);
            if ($response->status() !== 200) {
                $exception = $response->exception ? $response->exception->getMessage().' in '.$response->exception->getFile().':'.$response->exception->getLine() : 'No exception message';
                $errors[$name] = "Status {$response->status()}: {$exception}";
            }
        } catch (Throwable $e) {
            $errors[$name] = 'Exception '.get_class($e).': '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine();
        }
    }

    if (! empty($errors)) {
        dump($errors);
    }

    expect($errors)->toBeEmpty();
});

test('buku-induk and cohort render without error even when student admission_date is string', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    Student::create([
        'nisn' => '9999999991',
        'nis' => '99991',
        'name' => 'Siswa Test String Admission Date',
        'gender' => 'L',
        'status' => 'Aktif',
        'admission_date' => '2024-07-15',
        'is_active' => true,
    ]);

    $res1 = $this->actingAs($admin)->get(route('buku-induk.index'));
    $res1->assertStatus(200);
    $res1->assertSee('Siswa Test String Admission Date');

    $res2 = $this->actingAs($admin)->get(route('students.cohort.index'));
    $res2->assertStatus(200);
    $res2->assertSee('Siswa Test String Admission Date');
});

test('employees buku-induk and kontrol-pangkat render and query employee_histories relation without error', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $emp = Employee::create([
        'nip' => '198001012005011009',
        'name' => 'Pegawai Test History',
        'employment_type' => 'Guru',
        'employment_status' => 'PNS',
        'rank_group' => 'III/a',
        'rank_tmt' => '2022-04-01',
        'is_active' => true,
    ]);

    EmployeeHistory::create([
        'employee_id' => $emp->id,
        'history_type' => 'Pangkat',
        'title' => 'Kenaikan Pangkat Penata Muda',
        'start_date' => '2022-04-01',
    ]);

    $res1 = $this->actingAs($admin)->get(route('employees.buku-induk.index'));
    $res1->assertStatus(200);

    $res2 = $this->actingAs($admin)->get(route('employees.kontrol-pangkat.index'));
    $res2->assertStatus(200);
});

<?php

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\Role;
use App\Models\SchoolIdentity;
use App\Models\Teacher;
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

    $this->admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);
});

test('admin can create teacher with satpam keamanan status and position', function () {
    $response = $this->actingAs($this->admin)->post(route('master.teachers.store'), [
        'name' => 'Ahmad Suhendar',
        'gender' => 'L',
        'role' => 'satpam',
        'roles' => ['satpam'],
        'email' => 'ahmad.satpam@sman1lengkong.sch.id',
        'employment_type' => 'Tenaga Keamanan',
        'employment_status' => 'Satpam/Keamanan',
        'position' => 'Satpam/Keamanan',
        'additional_tasks' => 'Petugas Keamanan Gerbang & Pos Satpam',
        'phone' => '081234567899',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('teachers', [
        'name' => 'Ahmad Suhendar',
        'employment_type' => 'Tenaga Keamanan',
        'employment_status' => 'Satpam/Keamanan',
        'position' => 'Satpam/Keamanan',
        'additional_tasks' => 'Petugas Keamanan Gerbang & Pos Satpam',
    ]);

    $this->assertDatabaseHas('users', [
        'name' => 'Ahmad Suhendar',
        'email' => 'ahmad.satpam@sman1lengkong.sch.id',
        'role' => 'satpam',
    ]);

    $this->assertDatabaseHas('employees', [
        'employment_status' => 'Satpam/Keamanan',
        'position' => 'Satpam/Keamanan',
    ]);
});

test('master data teachers index can filter by satpam category and status', function () {
    $user = User::factory()->create([
        'role' => 'satpam',
        'roles' => ['satpam'],
    ]);

    $teacher = Teacher::create([
        'user_id' => $user->id,
        'name' => 'Bambang Sudibyo',
        'gender' => 'L',
        'employment_type' => 'Tenaga Keamanan',
        'employment_status' => 'Satpam/Keamanan',
        'position' => 'Komandan Regu (Danru) Satpam',
        'is_active' => true,
    ]);

    $resAll = $this->actingAs($this->admin)->get(route('master.teachers'));
    $resAll->assertStatus(200);
    $resAll->assertSee('Bambang Sudibyo');
    $resAll->assertSee('Satpam/Keamanan');

    $resSatpamType = $this->actingAs($this->admin)->get(route('master.teachers', ['type' => 'satpam']));
    $resSatpamType->assertStatus(200);
    $resSatpamType->assertSee('Bambang Sudibyo');

    $resSatpamStatus = $this->actingAs($this->admin)->get(route('master.teachers', ['status' => 'Satpam/Keamanan']));
    $resSatpamStatus->assertStatus(200);
    $resSatpamStatus->assertSee('Bambang Sudibyo');
});

test('user with satpam role redirects to pos satpam kiosk on dashboard', function () {
    $satpamUser = User::factory()->create([
        'role' => 'satpam',
        'roles' => ['satpam'],
    ]);

    $this->actingAs($satpamUser)
        ->withSession(['active_role' => 'satpam'])
        ->get(route('dashboard'))
        ->assertRedirect(route('attendance.kiosk-satpam'));
});

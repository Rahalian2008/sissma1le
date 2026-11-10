<?php

use App\Models\AttendanceLocation;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Carbon::setTestNow(Carbon::today()->setTime(6, 15, 0));

    SchoolSetting::set('school_lat', '-7.13013342752238');
    SchoolSetting::set('school_lng', '106.7038263897621');
    SchoolSetting::set('geofence_radius_meters', 100);
    SchoolSetting::set('enable_attendance_selfie', '1');
    SchoolSetting::set('attendance_senin_is_active', '1');
    SchoolSetting::set('attendance_senin_in_start', '05:45');
    SchoolSetting::set('attendance_senin_in_on_time', '06:30');
    SchoolSetting::set('attendance_senin_in_late_cutoff', '11:00');
    SchoolSetting::set('attendance_senin_out_start', '15:00');
    SchoolSetting::set('attendance_senin_out_end', '21:00');

    AttendanceLocation::firstOrCreate(
        ['is_active' => true],
        [
            'name' => 'SMA Negeri 1 Lengkong',
            'latitude' => -7.13013342752238,
            'longitude' => 106.7038263897621,
            'radius_meters' => 100,
        ]
    );
});

afterEach(function () {
    Carbon::setTestNow();
});

test('admin can view radius toggle on settings page', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('master.settings'));

    $response->assertStatus(200);
    $response->assertSee('Titik Koordinat GPS');
    $response->assertSee('enable_geofence_radius');
    $response->assertSee('Radius Aktif');
    $response->assertSee('Radius Nonaktif');
});

test('admin can toggle geofence radius on and off via master.settings.update', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    // 1. Matikan fitur radius
    $response = $this->actingAs($admin)->post(route('master.settings.update'), [
        'enable_geofence_radius' => '0',
        'school_lat' => '-7.13013342752238',
        'school_lng' => '106.7038263897621',
        'geofence_radius_meters' => '100',
    ]);

    $response->assertRedirect();
    expect(SchoolSetting::get('enable_geofence_radius'))->toBe('0');
    expect(AttendanceLocation::first()->is_active)->toBeFalse();

    // 2. Aktifkan kembali fitur radius
    $response = $this->actingAs($admin)->post(route('master.settings.update'), [
        'enable_geofence_radius' => '1',
        'school_lat' => '-7.13013342752238',
        'school_lng' => '106.7038263897621',
        'geofence_radius_meters' => '100',
    ]);

    $response->assertRedirect();
    expect(SchoolSetting::get('enable_geofence_radius'))->toBe('1');
    expect(AttendanceLocation::first()->is_active)->toBeTrue();
});

test('selfie attendance rejects outside radius when geofence is active', function () {
    SchoolSetting::set('enable_geofence_radius', '1');

    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'name' => 'Budi Radius',
        'nisn' => '9988776601',
        'nis' => '12301',
        'gender' => 'L',
        'class_id' => $class->id,
        'user_id' => $studentUser->id,
        'is_active' => true,
    ]);

    $attendanceService = app(AttendanceService::class);

    // Far away coordinates (e.g. Jakarta ~100km away)
    $result = $attendanceService->processSelfieAttendance($student, [
        'selfie_image' => 'data:image/jpeg;base64,'.base64_encode('dummy_image_data'),
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'accuracy' => 10,
    ]);

    expect($result['success'])->toBeFalse();
    expect($result['error_code'])->toBe('OUT_OF_RADIUS');
    expect($result['message'])->toContain('Anda berada di luar area presensi sekolah');
});

test('selfie attendance allows outside radius when geofence is inactive', function () {
    SchoolSetting::set('enable_geofence_radius', '0');

    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'name' => 'Siti Bebas',
        'nisn' => '9988776602',
        'nis' => '12302',
        'gender' => 'P',
        'class_id' => $class->id,
        'user_id' => $studentUser->id,
        'is_active' => true,
    ]);

    $attendanceService = app(AttendanceService::class);

    // Far away coordinates (Jakarta ~100km away)
    $result = $attendanceService->processSelfieAttendance($student, [
        'selfie_image' => 'data:image/jpeg;base64,'.base64_encode('dummy_image_data'),
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'accuracy' => 10,
    ]);

    expect($result['success'])->toBeTrue();
    expect($result['status'])->toBe('HADIR');
    expect($result['verification_status'])->toBe('VALID');
});

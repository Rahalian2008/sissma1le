<?php

use App\Models\SchoolHoliday;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;

beforeEach(function () {
    // Clean up any test holidays before each test
    SchoolHoliday::where('name', 'like', 'TEST_%')->delete();
});

test('admin can view master holidays page with statistics and list', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/master/holidays');

    $response->assertStatus(200);
    $response->assertSee('Hari Libur & Tanggal Merah', false);
    $response->assertSee('Tambah Hari Libur', false);
    $response->assertSee('Import CSV', false);
});

test('unauthorized roles cannot access master holidays management', function (string $role) {
    $user = User::where('role', $role)->first();
    if (! $user) {
        $user = User::factory()->create(['role' => $role]);
    }

    $response = $this->actingAs($user)->get('/master/holidays');

    $response->assertStatus(403);
})->with([
    'siswa',
    'orang_tua',
    'guru',
    'wali_kelas',
    'kepala_sekolah',
]);

test('admin can store, update, and delete a school holiday', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    // 1. Create Holiday
    $createResponse = $this->actingAs($admin)->post('/master/holidays', [
        'name' => 'TEST_Libur Akhir Semester',
        'start_date' => '2026-12-20',
        'end_date' => '2026-12-31',
        'type' => 'sekolah',
        'description' => 'Liburan jeda semester ganjil',
    ]);

    $createResponse->assertRedirect();
    $createResponse->assertSessionHas('success');

    $holiday = SchoolHoliday::where('name', 'TEST_Libur Akhir Semester')->first();
    expect($holiday)->not->toBeNull();
    expect($holiday->type)->toBe('sekolah');
    expect($holiday->days_count)->toBe(12);

    // 2. Update Holiday
    $updateResponse = $this->actingAs($admin)->put("/master/holidays/{$holiday->id}", [
        'name' => 'TEST_Libur Semester Ganjil Diperpanjang',
        'start_date' => '2026-12-20',
        'end_date' => '2027-01-02',
        'type' => 'sekolah',
        'description' => 'Diperpanjang hingga 2 Januari',
        'is_active' => '1',
    ]);

    $updateResponse->assertRedirect();
    $updateResponse->assertSessionHas('success');

    $holiday->refresh();
    expect($holiday->name)->toBe('TEST_Libur Semester Ganjil Diperpanjang');
    expect($holiday->end_date->format('Y-m-d'))->toBe('2027-01-02');

    // 3. Delete Holiday
    $deleteResponse = $this->actingAs($admin)->delete("/master/holidays/{$holiday->id}");
    $deleteResponse->assertRedirect();
    $deleteResponse->assertSessionHas('success');

    expect(SchoolHoliday::find($holiday->id))->toBeNull();
});

test('admin can generate common national holidays for a given year', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/master/holidays/generate-common', [
        'year' => 2027,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $holidays2027 = SchoolHoliday::whereYear('start_date', 2027)->count();
    expect($holidays2027)->toBeGreaterThanOrEqual(15);
});

test('AttendanceService rejects selfie attendance on school holidays', function () {
    $today = Carbon::today()->format('Y-m-d');

    // Create a holiday for today
    $holiday = SchoolHoliday::create([
        'name' => 'TEST_Hari Libur Nasional Hari Ini',
        'start_date' => $today,
        'end_date' => $today,
        'type' => 'nasional',
        'description' => 'Uji coba penolakan presensi otomatis saat tanggal merah',
        'is_active' => true,
    ]);

    $student = Student::first();
    expect($student)->not->toBeNull();

    $service = app(AttendanceService::class);
    $result = $service->processSelfieAttendance(
        $student,
        [
            'selfie_image' => 'data:image/jpeg;base64,'.base64_encode('fake-image-content'),
            'latitude' => -6.885833,
            'longitude' => 107.536111,
        ]
    );

    expect($result['success'])->toBeFalse();
    expect($result['error_code'])->toBe('HOLIDAY_OFF');
    expect($result['message'])->toContain('libur');

    $holiday->delete();
});

test('AttendanceService rejects QR and RFID attendance on school holidays', function () {
    $today = Carbon::today()->format('Y-m-d');

    $holiday = SchoolHoliday::create([
        'name' => 'TEST_Hari Libur Khusus Sekolah',
        'start_date' => $today,
        'end_date' => $today,
        'type' => 'khusus',
        'description' => 'Uji coba penolakan QR dan RFID saat libur',
        'is_active' => true,
    ]);

    $student = Student::first();
    expect($student)->not->toBeNull();

    $service = app(AttendanceService::class);

    // Test QR rejection
    $qrResult = $service->processQrAttendance($student, 'DUMMY_SESSION_TOKEN');
    expect($qrResult['success'])->toBeFalse();
    expect($qrResult['error_code'])->toBe('HOLIDAY_OFF');

    // Test RFID rejection
    $rfidResult = $service->processRfidAttendance($student->rfid_uid ?? 'TEST_UID_123', 'GATE-01');
    expect($rfidResult['success'])->toBeFalse();
    expect($rfidResult['error_code'])->toBe('HOLIDAY_OFF');

    $holiday->delete();
});

test('AttendanceService rejects leave submission on official holidays', function () {
    $today = Carbon::today()->format('Y-m-d');

    $holiday = SchoolHoliday::create([
        'name' => 'TEST_Hari Libur Resmi',
        'start_date' => $today,
        'end_date' => $today,
        'type' => 'nasional',
        'description' => 'Hari Libur Resmi',
        'is_active' => true,
    ]);

    $student = Student::first();
    expect($student)->not->toBeNull();

    $service = app(AttendanceService::class);
    $result = $service->submitLeaveRequest($student, [
        'type' => 'izin',
        'target_date' => $today,
        'reason' => 'Pergi ke luar kota',
    ]);

    expect($result['success'])->toBeFalse();
    expect($result['error_code'])->toBe('TARGET_DATE_IS_HOLIDAY');
    expect($result['message'])->toContain('hari libur');

    $holiday->delete();
});

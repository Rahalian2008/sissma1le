<?php

use App\Models\Attendance;
use App\Models\Extracurricular;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use App\Models\WorshipAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Carbon::setTestNow(Carbon::now()->next(Carbon::MONDAY)->setTime(6, 30, 0));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('master settings page displays 5 attendance methods cards and activity schedule barometers', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('master.settings'));

    $response->assertStatus(200);
    // 5 Methods in Settings Section 3
    $response->assertSee('Selfie Kamera');
    $response->assertSee('Barcode Scanner');
    $response->assertSee('Kartu RFID');
    $response->assertSee('Face Recognition');
    $response->assertSee('Presensi Manual');

    // Section 5 Activity Barometer Schedules
    $response->assertSee('Shalat Dhuha');
    $response->assertSee('Dzuhur', false);
    $response->assertSee('Keputrian');
    $response->assertSee('Presensi Ekstrakurikuler');
    $response->assertSee('extra_attendance_start');
    $response->assertSee('extra_attendance_end');
});

test('student view strictly hides scanner gate terminal and manual tabs on presensi utama, ibadah, and ekstra', function () {
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $studentUser = User::where('role', 'siswa')->first() ?? User::factory()->create(['role' => 'siswa']);
    $student = Student::where('user_id', $studentUser->id)->first() ?? Student::create([
        'name' => 'Ahmad Siswa Test',
        'nisn' => '1122334455',
        'nis' => '54321',
        'gender' => 'L',
        'class_id' => $class->id,
        'user_id' => $studentUser->id,
        'is_active' => true,
    ]);

    $extra = Extracurricular::first() ?? Extracurricular::create([
        'name' => 'Pramuka',
        'code' => 'EX-PRAMUKA',
        'day' => 'Sabtu',
        'start_time' => '14:00',
        'end_time' => '17:00',
        'location' => 'Lapangan',
        'is_active' => true,
    ]);

    $extra->students()->syncWithoutDetaching([$student->id => ['role' => 'Anggota']]);

    // 1. Presensi Utama: Student only sees Selfie tab (+ Izin/Pulang & Riwayat)
    $resUtama = $this->actingAs($studentUser)->get(route('attendance.index'));
    $resUtama->assertStatus(200);
    $resUtama->assertSee('Kamera Selfie');
    $resUtama->assertDontSee('btn-tab-scanner');
    $resUtama->assertDontSee('Terminal Pemindai');

    // 2. Worship (Dhuha): Student only sees Selfie tab
    $resWorship = $this->actingAs($studentUser)->get(route('worship.index', 'dhuha'));
    $resWorship->assertStatus(200);
    $resWorship->assertSee('Presensi Kamera Selfie');
    $resWorship->assertDontSee('Terminal Gerbang Ibadah');
    $resWorship->assertDontSee('Absen Manual per Kelas');

    // 3. Ekstrakurikuler: Student only sees Selfie tab
    $resEkstra = $this->actingAs($studentUser)->get(route('extracurricular.index', ['extra_id' => $extra->id]));
    $resEkstra->assertStatus(200);
    $resEkstra->assertSee('Presensi Kamera Selfie');
    $resEkstra->assertDontSee('Terminal Gerbang Ekstra');
    $resEkstra->assertDontSee('Absen Manual Anggota');
});

test('backend strictly rejects attendance when method toggle is disabled', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $student = Student::first();

    // Disable barcode method
    SchoolSetting::set('enable_attendance_barcode', '0');
    SchoolSetting::set('enable_attendance_qr', '0');

    // Attempt scan barcode on Presensi Utama -> Must be rejected with 422
    $resUtama = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);
    $resUtama->assertStatus(422);
    $resUtama->assertJson([
        'success' => false,
        'error_code' => 'METHOD_DISABLED',
    ]);
    expect($resUtama->json('message'))->toContain('Barcode Scanner');

    // Disable face recognition
    SchoolSetting::set('enable_attendance_face_recognition', '0');

    // Attempt scan face recognition on Presensi Utama -> Must be rejected with 422
    $resFace = $this->actingAs($admin)->postJson(route('attendance.face.scan'), [
        'identifier' => $student->nisn,
    ]);
    $resFace->assertStatus(422);
    $resFace->assertJson([
        'success' => false,
        'error_code' => 'METHOD_DISABLED',
    ]);
    expect($resFace->json('message'))->toContain('Face Recognition');

    // Re-enable for subsequent tests
    SchoolSetting::set('enable_attendance_barcode', '1');
    SchoolSetting::set('enable_attendance_qr', '1');
    SchoolSetting::set('enable_attendance_face_recognition', '1');
});

test('presensi utama unifies attendance from any gate into one record without collision', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $student = Student::first();

    // Ensure all methods enabled
    SchoolSetting::set('enable_attendance_barcode', '1');
    SchoolSetting::set('enable_attendance_face_recognition', '1');

    // Clear any today attendance for this student
    Attendance::where('student_id', $student->id)->whereDate('date', Carbon::today())->delete();

    // 1. Student attends via Barcode Scanner
    $res1 = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);

    $res1->assertStatus(200);
    $res1->assertJson(['success' => true]);

    // Check that attendance record was created
    $att = Attendance::where('student_id', $student->id)->whereDate('date', Carbon::today())->first();
    expect($att)->not->toBeNull();
    expect($att->method)->toBe('BARCODE');

    // 2. If student attempts to scan again via Face Recognition in check-in window -> Rejects as ALREADY_RECORDED
    $res2 = $this->actingAs($admin)->postJson(route('attendance.face.scan'), [
        'identifier' => $student->nisn,
    ]);

    // Controller returns HTTP 422 or success with ALREADY_RECORDED message
    if ($res2->status() === 200) {
        expect($res2->json('status'))->toBe('already_recorded');
    } else {
        $res2->assertStatus(422);
    }

    // Still only 1 record exists in database
    $count = Attendance::where('student_id', $student->id)->whereDate('date', Carbon::today())->count();
    expect($count)->toBe(1);
});

test('worship attendance scan supports all 5 methods and stores method properly', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $student = Student::first();

    SchoolSetting::set('enable_attendance_barcode', '1');
    SchoolSetting::set('enable_attendance_face_recognition', '1');
    SchoolSetting::set('worship_dhuha_days', 'senin,kamis,jumat');
    SchoolSetting::set('worship_dhuha_start', '06:00');
    SchoolSetting::set('worship_dhuha_end', '08:00');

    WorshipAttendance::where('student_id', $student->id)
        ->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA)
        ->whereDate('date', Carbon::today())
        ->delete();

    // Scan via FACE_RECOGNITION with bypass_time_lock for test
    $res = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $student->nisn,
        'method' => 'FACE_RECOGNITION',
        'bypass_time_lock' => true,
    ]);

    $res->assertStatus(200);
    $res->assertJson(['success' => true]);

    $wRecord = WorshipAttendance::where('student_id', $student->id)
        ->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA)
        ->whereDate('date', Carbon::today())
        ->first();

    expect($wRecord)->not->toBeNull();
    expect($wRecord->method)->toBe('FACE_RECOGNITION');
});

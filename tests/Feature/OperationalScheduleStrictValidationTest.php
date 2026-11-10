<?php

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use App\Models\WorshipAttendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    // Pastikan Primary Location Geofence aktif tersedia
    AttendanceLocation::query()->delete();
    AttendanceLocation::create([
        'name' => 'Kampus SMAN 1 Lengkong',
        'latitude' => -7.130133,
        'longitude' => 106.703826,
        'radius_meters' => 5000,
        'is_active' => true,
    ]);

    SchoolSetting::set('school_lat', '-7.130133');
    SchoolSetting::set('school_lng', '106.703826');
    SchoolSetting::set('geofence_radius_meters', '5000');

    // Aktifkan ke-5 metode presensi di master setting
    SchoolSetting::set('enable_attendance_selfie', '1');
    SchoolSetting::set('enable_attendance_barcode', '1');
    SchoolSetting::set('enable_attendance_qr', '1');
    SchoolSetting::set('enable_attendance_rfid', '1');
    SchoolSetting::set('enable_attendance_face_recognition', '1');
    SchoolSetting::set('enable_attendance_manual', '1');
});

afterEach(function () {
    Carbon::setTestNow();
});

test('presensi utama: seluruh 5 metode presensi wajib tunduk ketat pada jam operasional harian (sesi masuk & pulang)', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-MIPA-1', 'level' => '10']);

    $student = Student::first() ?? Student::create([
        'name' => 'Budi Santoso',
        'nisn' => '9988776655',
        'nis' => '12345',
        'rfid_uid' => 'RFID-BUDI-01',
        'gender' => 'L',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    if (! $student->rfid_uid) {
        $student->update(['rfid_uid' => 'RFID-BUDI-01']);
    }

    // 1. Setting Jadwal Hari Senin: Masuk (06:00 - 06:45), Pulang (15:00 - 17:00)
    SchoolSetting::set('attendance_senin_is_active', '1');
    SchoolSetting::set('attendance_senin_in_start', '06:00');
    SchoolSetting::set('attendance_senin_in_on_time', '06:30');
    SchoolSetting::set('attendance_senin_in_late_cutoff', '06:45');
    SchoolSetting::set('attendance_senin_out_start', '15:00');
    SchoolSetting::set('attendance_senin_out_end', '17:00');

    $mondayDate = Carbon::now()->next(Carbon::MONDAY)->toDateString();
    $validBase64Selfie = 'data:image/png;base64,'.base64_encode(str_repeat('A', 200));

    // A. DI LUAR JADWAL: Terlalu pagi (05:45 WIB) -> Semua metode WAJIB DITOLAK
    Carbon::setTestNow(Carbon::parse("{$mondayDate} 05:45:00", 'Asia/Jakarta'));
    Attendance::where('student_id', $student->id)->whereDate('date', $mondayDate)->delete();

    // 1. Selfie
    $resSelfieEarly = $this->actingAs($admin)->postJson(route('attendance.selfie.store'), [
        'student_id' => $student->id,
        'selfie_image' => $validBase64Selfie,
        'latitude' => -7.130133,
        'longitude' => 106.703826,
    ]);
    $resSelfieEarly->assertStatus(422);
    expect($resSelfieEarly->json('message'))->toContain('belum dibuka');

    // 2. Barcode Scanner
    $resBarcodeEarly = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);
    $resBarcodeEarly->assertStatus(422);
    expect($resBarcodeEarly->json('message'))->toContain('belum dibuka');

    // 3. RFID
    $resRfidEarly = $this->actingAs($admin)->postJson(route('attendance.rfid.scan'), [
        'rfid_uid' => $student->rfid_uid,
    ]);
    $resRfidEarly->assertStatus(422);
    expect($resRfidEarly->json('message'))->toContain('belum dibuka');

    // 4. Face Recognition
    $resFaceEarly = $this->actingAs($admin)->postJson(route('attendance.face.scan'), [
        'identifier' => $student->nisn,
    ]);
    $resFaceEarly->assertStatus(422);
    expect($resFaceEarly->json('message'))->toContain('belum dibuka');

    // 5. Presensi Manual
    $attService = app(AttendanceService::class);
    $manualEarly = $attService->recordManualAttendance([
        'student_id' => $student->id,
        'date' => $mondayDate,
        'time' => '05:45:00',
        'status' => 'HADIR',
    ], $admin);
    expect($manualEarly['success'])->toBeFalse();
    expect($manualEarly['message'])->toContain('belum dibuka');

    // B. DI LUAR JADWAL: Melewati batas akhir masuk (07:15 WIB, Cutoff 06:45) -> Semua metode WAJIB DITOLAK
    Carbon::setTestNow(Carbon::parse("{$mondayDate} 07:15:00", 'Asia/Jakarta'));
    Attendance::where('student_id', $student->id)->whereDate('date', $mondayDate)->delete();

    // 1. Selfie
    $resSelfieLate = $this->actingAs($admin)->postJson(route('attendance.selfie.store'), [
        'student_id' => $student->id,
        'selfie_image' => $validBase64Selfie,
        'latitude' => -7.130133,
        'longitude' => 106.703826,
    ]);
    $resSelfieLate->assertStatus(422);
    expect($resSelfieLate->json('message'))->toContain('Batas akhir presensi masuk');

    // 2. Barcode Scanner
    $resBarcodeLate = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);
    $resBarcodeLate->assertStatus(422);
    expect($resBarcodeLate->json('message'))->toContain('Batas akhir presensi masuk');

    // 3. RFID
    $resRfidLate = $this->actingAs($admin)->postJson(route('attendance.rfid.scan'), [
        'rfid_uid' => $student->rfid_uid,
    ]);
    $resRfidLate->assertStatus(422);
    expect($resRfidLate->json('message'))->toContain('Batas akhir presensi masuk');

    // 4. Face Recognition
    $resFaceLate = $this->actingAs($admin)->postJson(route('attendance.face.scan'), [
        'identifier' => $student->nisn,
    ]);
    $resFaceLate->assertStatus(422);
    expect($resFaceLate->json('message'))->toContain('Batas akhir presensi masuk');

    // 5. Presensi Manual
    $manualLate = $attService->recordManualAttendance([
        'student_id' => $student->id,
        'date' => $mondayDate,
        'time' => '07:15:00',
        'status' => 'HADIR',
    ], $admin);
    expect($manualLate['success'])->toBeFalse();
    expect($manualLate['message'])->toContain('Batas akhir presensi masuk');

    // C. DALAM JADWAL OPERASIONAL MASUK (06:20 WIB) -> Berhasil untuk semua metode
    Carbon::setTestNow(Carbon::parse("{$mondayDate} 06:20:00", 'Asia/Jakarta'));

    // Uji Barcode
    Attendance::where('student_id', $student->id)->whereDate('date', $mondayDate)->delete();
    $resBarcodeValid = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);
    $resBarcodeValid->assertStatus(200);
    expect($resBarcodeValid->json('success'))->toBeTrue();

    // Uji RFID
    Attendance::where('student_id', $student->id)->whereDate('date', $mondayDate)->delete();
    $resRfidValid = $this->actingAs($admin)->postJson(route('attendance.rfid.scan'), [
        'rfid_uid' => $student->rfid_uid,
    ]);
    $resRfidValid->assertStatus(200);
    expect($resRfidValid->json('success'))->toBeTrue();

    // Uji Face Recognition
    Attendance::where('student_id', $student->id)->whereDate('date', $mondayDate)->delete();
    $resFaceValid = $this->actingAs($admin)->postJson(route('attendance.face.scan'), [
        'identifier' => $student->nisn,
    ]);
    $resFaceValid->assertStatus(200);
    expect($resFaceValid->json('success'))->toBeTrue();

    // Uji Selfie
    Attendance::where('student_id', $student->id)->whereDate('date', $mondayDate)->delete();
    $resSelfieValid = $this->actingAs($admin)->postJson(route('attendance.selfie.store'), [
        'student_id' => $student->id,
        'selfie_image' => $validBase64Selfie,
        'latitude' => -7.130133,
        'longitude' => 106.703826,
    ]);
    $resSelfieValid->assertStatus(200);
    expect($resSelfieValid->json('success'))->toBeTrue();

    // D. SESI PULANG: Di luar jadwal vs Di dalam jadwal
    // 1. Terlalu awal sebelum jam pulang (14:15 WIB, buka 15:00 WIB) -> Ditolak
    Carbon::setTestNow(Carbon::parse("{$mondayDate} 14:15:00", 'Asia/Jakarta'));
    $resOutEarly = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);
    $resOutEarly->assertStatus(422);
    expect($resOutEarly->json('message'))->toContain('Sesi presensi pulang dibuka');

    // 2. Terlambat setelah batas akhir pulang (17:30 WIB, tutup 17:00 WIB) -> Ditolak
    Carbon::setTestNow(Carbon::parse("{$mondayDate} 17:30:00", 'Asia/Jakarta'));
    $resOutLate = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);
    $resOutLate->assertStatus(422);
    expect($resOutLate->json('message'))->toContain('telah berakhir');

    // 3. Tepat pada jam operasional pulang (15:30 WIB) -> Berhasil
    Carbon::setTestNow(Carbon::parse("{$mondayDate} 15:30:00", 'Asia/Jakarta'));
    $resOutValid = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);
    $resOutValid->assertStatus(200);
    expect($resOutValid->json('session'))->toBe('checkout');
    expect($resOutValid->json('success'))->toBeTrue();

    // E. HARI TIDAK AKTIF / LIBUR (Minggu) -> Semua metode WAJIB DITOLAK
    $sundayDate = Carbon::now()->next(Carbon::SUNDAY)->toDateString();
    Carbon::setTestNow(Carbon::parse("{$sundayDate} 06:30:00", 'Asia/Jakarta'));
    Attendance::where('student_id', $student->id)->whereDate('date', $sundayDate)->delete();

    $resSunday = $this->actingAs($admin)->postJson(route('attendance.barcode.scan'), [
        'barcode' => $student->nisn,
    ]);
    $resSunday->assertStatus(422);
    expect($resSunday->json('error_code'))->toBe('DAY_OFF');
});

test('presensi keagamaan (dhuha, dzuhur/jumat, keputrian): wajib tunduk ketat pada setting hari, mulai buka, dan selesai tutup (batas akhir)', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-MIPA-1', 'level' => '10']);

    $studentMale = Student::create([
        'name' => 'Ahmad Santri L',
        'nisn' => '9988771122',
        'nis' => '11223',
        'rfid_uid' => 'RFID-AHMAD-01',
        'gender' => 'L',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    $studentFemale = Student::create([
        'name' => 'Fatimah Santri P',
        'nisn' => '9988773344',
        'nis' => '33445',
        'rfid_uid' => 'RFID-FATIMAH-01',
        'gender' => 'P',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    // 1. Shalat Dhuha: Hari Kamis & Jumat, Jam 06:30 - 08:00 WIB
    SchoolSetting::set('worship_dhuha_days', 'kamis,jumat');
    SchoolSetting::set('worship_dhuha_start', '06:30');
    SchoolSetting::set('worship_dhuha_end', '08:00');

    // A. Uji Hari Salah: Senin jam 07:00 WIB -> WAJIB DITOLAK (WRONG_DAY)
    $monday = Carbon::now()->next(Carbon::MONDAY)->setTime(7, 0, 0);
    Carbon::setTestNow($monday);

    $resDhuhaWrongDay = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $studentMale->nisn,
        'method' => 'RFID',
    ]);
    $resDhuhaWrongDay->assertStatus(422);
    expect($resDhuhaWrongDay->json('error_code'))->toBe('TIME_LOCKED');
    expect($resDhuhaWrongDay->json('message'))->toContain('bukan hari pelaksanaan');

    // B. Uji Jam Salah: Kamis jam 06:10 WIB (Belum buka) -> WAJIB DITOLAK (WRONG_TIME)
    $thursdayEarly = Carbon::now()->next(Carbon::THURSDAY)->setTime(6, 10, 0);
    Carbon::setTestNow($thursdayEarly);

    $resDhuhaEarly = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $studentMale->nisn,
        'method' => 'BARCODE',
    ]);
    $resDhuhaEarly->assertStatus(422);
    expect($resDhuhaEarly->json('error_code'))->toBe('TIME_LOCKED');
    expect($resDhuhaEarly->json('message'))->toContain('belum dibuka');

    // C. Uji Jam Salah: Kamis jam 08:15 WIB (Melewati Batas Akhir) -> WAJIB DITOLAK (WRONG_TIME)
    $thursdayLate = Carbon::now()->next(Carbon::THURSDAY)->setTime(8, 15, 0);
    Carbon::setTestNow($thursdayLate);

    $resDhuhaLate = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $studentMale->nisn,
        'method' => 'FACE_RECOGNITION',
    ]);
    $resDhuhaLate->assertStatus(422);
    expect($resDhuhaLate->json('error_code'))->toBe('TIME_LOCKED');
    expect($resDhuhaLate->json('message'))->toContain('telah berakhir pada pukul 08:00 WIB (Batas Akhir)');

    // D. Uji Jam Tepat: Kamis jam 07:15 WIB -> BERHASIL untuk semua metode
    $thursdayValid = Carbon::now()->next(Carbon::THURSDAY)->setTime(7, 15, 0);
    Carbon::setTestNow($thursdayValid);

    // 1. RFID
    $resDhuhaRfid = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $studentMale->rfid_uid,
        'method' => 'RFID',
    ]);
    $resDhuhaRfid->assertStatus(200);
    expect($resDhuhaRfid->json('success'))->toBeTrue();

    // 2. Barcode
    WorshipAttendance::where('student_id', $studentMale->id)->delete();
    $resDhuhaBarcode = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $studentMale->nisn,
        'method' => 'BARCODE',
    ]);
    $resDhuhaBarcode->assertStatus(200);
    expect($resDhuhaBarcode->json('success'))->toBeTrue();

    // 3. Face Recognition
    WorshipAttendance::where('student_id', $studentMale->id)->delete();
    $resDhuhaFace = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $studentMale->nisn,
        'method' => 'FACE_RECOGNITION',
    ]);
    $resDhuhaFace->assertStatus(200);
    expect($resDhuhaFace->json('success'))->toBeTrue();

    // 4. Selfie
    WorshipAttendance::where('student_id', $studentMale->id)->delete();
    $validBase64Selfie = 'data:image/png;base64,'.base64_encode(str_repeat('A', 200));
    $resDhuhaSelfie = $this->actingAs($admin)->postJson(route('worship.selfie', 'dhuha'), [
        'student_id' => $studentMale->id,
        'selfie_image' => $validBase64Selfie,
        'latitude' => -7.130133,
        'longitude' => 106.703826,
    ]);
    $resDhuhaSelfie->assertStatus(200);
    expect($resDhuhaSelfie->json('success'))->toBeTrue();

    // 5. Presensi Manual: Pada jam valid berhasil, pada jam luar batas ditolak
    $resManualValid = $this->actingAs($admin)->post(route('worship.manual', 'dhuha'), [
        'class_id' => $class->id,
        'date' => $thursdayValid->toDateString(),
        'statuses' => [$studentMale->id => 'HADIR'],
    ]);
    $resManualValid->assertSessionHasNoErrors();
    expect(session('error'))->toBeNull();

    // Presensi Manual di luar jam operasional (Kamis 09:00 WIB) -> Ditolak
    Carbon::setTestNow(Carbon::now()->next(Carbon::THURSDAY)->setTime(9, 0, 0));
    $resManualLate = $this->actingAs($admin)->post(route('worship.manual', 'dhuha'), [
        'class_id' => $class->id,
        'date' => Carbon::now()->next(Carbon::THURSDAY)->toDateString(),
        'statuses' => [$studentMale->id => 'HADIR'],
    ]);
    expect(session('error'))->toContain('telah berakhir pada pukul 08:00 WIB (Batas Akhir)');

    // 2. Presensi Keputrian: Hari Jumat, Jam 11:45 - 13:00 WIB (Khusus Siswi Putri)
    SchoolSetting::set('worship_keputrian_days', 'jumat');
    SchoolSetting::set('worship_keputrian_start', '11:45');
    SchoolSetting::set('worship_keputrian_end', '13:00');

    // Jumat jam 13:15 WIB (Melewati Batas Akhir) -> Ditolak
    $fridayLate = Carbon::now()->next(Carbon::FRIDAY)->setTime(13, 15, 0);
    Carbon::setTestNow($fridayLate);
    $resKeputrianLate = $this->actingAs($admin)->postJson(route('worship.scan', 'keputrian'), [
        'identifier' => $studentFemale->nisn,
        'method' => 'RFID',
    ]);
    $resKeputrianLate->assertStatus(422);
    expect($resKeputrianLate->json('message'))->toContain('telah berakhir pada pukul 13:00 WIB (Batas Akhir)');

    // Jumat jam 12:15 WIB (Dalam Jadwal) -> Berhasil
    $fridayValid = Carbon::now()->next(Carbon::FRIDAY)->setTime(12, 15, 0);
    Carbon::setTestNow($fridayValid);
    WorshipAttendance::where('student_id', $studentFemale->id)->delete();
    $resKeputrianValid = $this->actingAs($admin)->postJson(route('worship.scan', 'keputrian'), [
        'identifier' => $studentFemale->nisn,
        'method' => 'RFID',
    ]);
    $resKeputrianValid->assertStatus(200);
    expect($resKeputrianValid->json('success'))->toBeTrue();
});

test('presensi ekstrakurikuler: wajib tunduk ketat pada setting hari, mulai buka, dan selesai tutup (batas akhir) tanpa toleransi/bypass', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-MIPA-1', 'level' => '10']);

    $student = Student::create([
        'name' => 'Randi Pramuka',
        'nisn' => '8877665544',
        'nis' => '44556',
        'rfid_uid' => 'RFID-RANDI-01',
        'gender' => 'L',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    $extra = Extracurricular::create([
        'code' => 'EX-PASKIBRA-TEST',
        'name' => 'Paskibra Sekolah',
        'category' => 'Kepemimpinan',
        'schedule_day' => 'sabtu',
        'start_time' => '14:00',
        'end_time' => '17:00',
        'is_active' => true,
    ]);

    // Daftarkan siswa ke ekstrakurikuler
    $extra->students()->syncWithoutDetaching([$student->id => ['role' => 'Anggota']]);

    // A. Hari Salah: Rabu jam 15:00 WIB -> WAJIB DITOLAK (WRONG_DAY)
    $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->setTime(15, 0, 0);
    Carbon::setTestNow($wednesday);

    $resWrongDay = $this->actingAs($admin)->postJson(route('extracurricular.scan'), [
        'extracurricular_id' => $extra->id,
        'identifier' => $student->nisn,
        'method' => 'RFID',
    ]);
    $resWrongDay->assertStatus(422);
    expect($resWrongDay->json('error_code'))->toBe('TIME_LOCKED');
    expect($resWrongDay->json('message'))->toContain('bukan hari operasional');

    // B. Jam Salah: Sabtu jam 13:45 WIB (Sebelum Mulai Buka 14:00) -> WAJIB DITOLAK
    $saturdayEarly = Carbon::now()->next(Carbon::SATURDAY)->setTime(13, 45, 0);
    Carbon::setTestNow($saturdayEarly);

    $resEarly = $this->actingAs($admin)->postJson(route('extracurricular.scan'), [
        'extracurricular_id' => $extra->id,
        'identifier' => $student->nisn,
        'method' => 'BARCODE',
    ]);
    $resEarly->assertStatus(422);
    expect($resEarly->json('error_code'))->toBe('TIME_LOCKED');
    expect($resEarly->json('message'))->toContain('hanya dibuka pada jam operasional kegiatan [14:00 - 17:00 WIB');

    // C. Jam Salah: Sabtu jam 17:05 WIB (Setelah Selesai Tutup Batas Akhir 17:00) -> WAJIB DITOLAK
    $saturdayLate = Carbon::now()->next(Carbon::SATURDAY)->setTime(17, 5, 0);
    Carbon::setTestNow($saturdayLate);

    $resLate = $this->actingAs($admin)->postJson(route('extracurricular.scan'), [
        'extracurricular_id' => $extra->id,
        'identifier' => $student->nisn,
        'method' => 'FACE_RECOGNITION',
    ]);
    $resLate->assertStatus(422);
    expect($resLate->json('error_code'))->toBe('TIME_LOCKED');
    expect($resLate->json('message'))->toContain('hanya dibuka pada jam operasional kegiatan [14:00 - 17:00 WIB');

    // D. Presensi Manual saat di luar jam operasional (Sabtu 17:15 WIB) -> Ditolak
    $resManualLate = $this->actingAs($admin)->post(route('extracurricular.manual'), [
        'extracurricular_id' => $extra->id,
        'date' => $saturdayLate->toDateString(),
        'statuses' => [$student->id => 'HADIR'],
    ]);
    expect(session('error'))->toContain('hanya dibuka pada jam operasional kegiatan [14:00 - 17:00 WIB');

    // E. Jam Tepat: Sabtu jam 15:30 WIB -> BERHASIL untuk semua metode
    $saturdayValid = Carbon::now()->next(Carbon::SATURDAY)->setTime(15, 30, 0);
    Carbon::setTestNow($saturdayValid);

    // 1. RFID
    $resRfid = $this->actingAs($admin)->postJson(route('extracurricular.scan'), [
        'extracurricular_id' => $extra->id,
        'identifier' => $student->rfid_uid,
        'method' => 'RFID',
    ]);
    $resRfid->assertStatus(200);
    expect($resRfid->json('success'))->toBeTrue();

    // 2. Barcode
    ExtracurricularAttendance::where('student_id', $student->id)->delete();
    $resBarcode = $this->actingAs($admin)->postJson(route('extracurricular.scan'), [
        'extracurricular_id' => $extra->id,
        'identifier' => $student->nisn,
        'method' => 'BARCODE',
    ]);
    $resBarcode->assertStatus(200);
    expect($resBarcode->json('success'))->toBeTrue();

    // 3. Face Recognition
    ExtracurricularAttendance::where('student_id', $student->id)->delete();
    $resFace = $this->actingAs($admin)->postJson(route('extracurricular.scan'), [
        'extracurricular_id' => $extra->id,
        'identifier' => $student->nisn,
        'method' => 'FACE_RECOGNITION',
    ]);
    $resFace->assertStatus(200);
    expect($resFace->json('success'))->toBeTrue();

    // 4. Selfie
    ExtracurricularAttendance::where('student_id', $student->id)->delete();
    $validBase64Selfie = 'data:image/png;base64,'.base64_encode(str_repeat('A', 200));
    $resSelfie = $this->actingAs($admin)->postJson(route('extracurricular.selfie'), [
        'extracurricular_id' => $extra->id,
        'student_id' => $student->id,
        'selfie_image' => $validBase64Selfie,
        'latitude' => -7.130133,
        'longitude' => 106.703826,
    ]);
    $resSelfie->assertStatus(200);
    expect($resSelfie->json('success'))->toBeTrue();

    // 5. Presensi Manual saat dalam jam operasional -> Berhasil
    ExtracurricularAttendance::where('student_id', $student->id)->delete();
    $resManualValid = $this->actingAs($admin)->post(route('extracurricular.manual'), [
        'extracurricular_id' => $extra->id,
        'date' => $saturdayValid->toDateString(),
        'statuses' => [$student->id => 'HADIR'],
    ]);
    expect(session('error'))->toBeNull();
    $savedRecord = ExtracurricularAttendance::where('student_id', $student->id)
        ->where('extracurricular_id', $extra->id)
        ->whereDate('date', $saturdayValid->toDateString())
        ->first();
    expect($savedRecord)->not->toBeNull();
    expect($savedRecord->method)->toBe('MANUAL');
});

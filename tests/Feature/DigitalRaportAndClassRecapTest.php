<?php

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ParentGuardian;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentAiRaport;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AiStudentAnalysisService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    SchoolSetting::set('school_name', 'SMA Negeri 1 Lengkong');
    SchoolSetting::set('gemini_api_key', '');
    config(['services.gemini.api_key' => '']);
});

test('class attendance recap calculates accurately without individual student names and print view returns 200', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $classA = SchoolClass::create([
        'name' => 'X-RECAP-A',
        'grade' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $classB = SchoolClass::create([
        'name' => 'X-RECAP-B',
        'grade' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $studentA1 = Student::create([
        'name' => 'Siswa A1 Rahasia',
        'nis' => '881101',
        'nisn' => '00881101',
        'gender' => 'L',
        'class_id' => $classA->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $studentA2 = Student::create([
        'name' => 'Siswa A2 Rahasia',
        'nis' => '881102',
        'nisn' => '00881102',
        'gender' => 'P',
        'class_id' => $classA->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $studentB1 = Student::create([
        'name' => 'Siswa B1 Rahasia',
        'nis' => '882201',
        'nisn' => '00882201',
        'gender' => 'L',
        'class_id' => $classB->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $today = Carbon::today('Asia/Jakarta')->toDateString();

    // Create attendance records
    Attendance::create([
        'student_id' => $studentA1->id,
        'student_name' => $studentA1->name,
        'nis' => $studentA1->nis,
        'nisn' => $studentA1->nisn,
        'class_id' => $classA->id,
        'date' => $today,
        'time' => '06:55:00',
        'status' => 'HADIR',
        'method' => 'RFID',
    ]);

    Attendance::create([
        'student_id' => $studentA2->id,
        'student_name' => $studentA2->name,
        'nis' => $studentA2->nis,
        'nisn' => $studentA2->nisn,
        'class_id' => $classA->id,
        'date' => $today,
        'time' => '07:15:00',
        'status' => 'TERLAMBAT',
        'method' => 'SELFIE',
    ]);

    Attendance::create([
        'student_id' => $studentB1->id,
        'student_name' => $studentB1->name,
        'nis' => $studentB1->nis,
        'nisn' => $studentB1->nisn,
        'class_id' => $classB->id,
        'date' => $today,
        'status' => 'IZIN',
        'method' => 'MANUAL',
    ]);

    $service = app(ReportService::class);
    $recap = $service->getClassAttendanceRecap($today, $today);

    expect($recap['class_recaps'])->not->toBeEmpty();

    $recapA = $recap['class_recaps']->firstWhere('class_id', $classA->id);
    expect($recapA)->not->toBeNull();
    expect($recapA['student_count'])->toBe(2);
    expect($recapA['hadir'])->toBe(1);
    expect($recapA['terlambat'])->toBe(1);
    expect($recapA['percentage'])->toBe(100.0);

    $recapB = $recap['class_recaps']->firstWhere('class_id', $classB->id);
    expect($recapB)->not->toBeNull();
    expect($recapB['student_count'])->toBe(1);
    expect($recapB['izin'])->toBe(1);

    // Test print view
    $admin = User::first() ?? User::create([
        'name' => 'Admin Testing',
        'email' => 'admin_test@sma1le.sch.id',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('reports.print.class_recap', [
        'start_date' => $today,
        'end_date' => $today,
    ]));

    $response->assertOk();
    $response->assertSee('REKAPITULASI KEHADIRAN SISWA SEMUA ROMBONGAN BELAJAR');
    $response->assertSee('X-RECAP-A');
    $response->assertSee('X-RECAP-B');
    // Verify it doesn't display individual student names
    $response->assertDontSee('Siswa A1 Rahasia');
    $response->assertDontSee('Siswa B1 Rahasia');
    $response->assertSee('JUMLAH TOTAL KESELURUHAN');
});

test('digital raport is accessible across all roles with proper permission scoping', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::create([
        'name' => 'XI-RAPORT-1',
        'grade' => '11',
        'academic_year_id' => $academicYear->id,
    ]);

    $studentUser = User::create([
        'name' => 'Ahmad Siswa Raport',
        'email' => 'ahmad_raport@siswa.sch.id',
        'password' => bcrypt('password'),
        'role' => 'siswa',
        'is_active' => true,
    ]);

    $student = Student::create([
        'user_id' => $studentUser->id,
        'name' => 'Ahmad Siswa Raport',
        'nis' => '771101',
        'nisn' => '00771101',
        'gender' => 'L',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $otherStudent = Student::create([
        'name' => 'Bintang Siswa Lain',
        'nis' => '771102',
        'nisn' => '00771102',
        'gender' => 'L',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    // 1. Siswa Access
    $siswaRes = $this->actingAs($studentUser)->get(route('raport.index'));
    $siswaRes->assertOk();
    $siswaRes->assertSee('Ahmad Siswa Raport');
    $siswaRes->assertSee('Target Refleksi');

    // Siswa cannot print other student
    $forbiddenPrint = $this->actingAs($studentUser)->get(route('raport.print', $otherStudent->id));
    $forbiddenPrint->assertForbidden();

    // Siswa cannot print their own report (forbidden per requirements)
    $validPrint = $this->actingAs($studentUser)->get(route('raport.print', $student->id));
    $validPrint->assertForbidden();

    // 2. Orang Tua Access
    $parentUser = User::create([
        'name' => 'Ibu Ahmad Raport',
        'email' => 'ibu_ahmad@ortu.sch.id',
        'password' => bcrypt('password'),
        'role' => 'orang_tua',
        'is_active' => true,
    ]);

    ParentGuardian::create([
        'user_id' => $parentUser->id,
        'student_id' => $student->id,
        'name' => 'Ibu Ahmad Raport',
        'relationship' => 'Ibu Kandung',
    ]);

    $ortuRes = $this->actingAs($parentUser)->get(route('raport.index'));
    $ortuRes->assertOk();
    $ortuRes->assertSee('Ahmad Siswa Raport');
    $ortuRes->assertSee('Saran Khusus Pendampingan Orang Tua');

    // 3. Wali Kelas Access
    $teacherUser = User::create([
        'name' => 'Pak Guru Wali',
        'email' => 'wali_guru@sma1le.sch.id',
        'password' => bcrypt('password'),
        'role' => 'wali_kelas',
        'is_active' => true,
    ]);

    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Pak Guru Wali',
        'nip' => '198001012010011001',
    ]);

    $class->update(['homeroom_teacher_id' => $teacher->id]);

    $waliRes = $this->actingAs($teacherUser)->get(route('raport.index'));
    $waliRes->assertOk();
    $waliRes->assertSee('Panduan Tindakan Pedagogis Wali Kelas');

    // 4. Admin Access
    $admin = User::where('role', 'admin')->first();
    $adminRes = $this->actingAs($admin)->get(route('raport.index', ['student_id' => $student->id]));
    $adminRes->assertOk();
    $adminRes->assertSee('Ahmad Siswa Raport');
    $adminRes->assertSee('Tinjauan Kebijakan');
});

test('digital raport filters by date range and search keyword', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::create([
        'name' => 'XII-FILTER-1',
        'grade' => '12',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::create([
        'name' => 'Citra Lestari Filter',
        'nis' => '661101',
        'nisn' => '00661101',
        'gender' => 'P',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $admin = User::where('role', 'admin')->first();

    // Search by name
    $searchRes = $this->actingAs($admin)->get(route('raport.index', [
        'search' => 'Citra',
        'student_id' => $student->id,
    ]));
    $searchRes->assertOk();
    $searchRes->assertSee('Citra Lestari Filter');

    // Filter by specific date range
    $dateRes = $this->actingAs($admin)->get(route('raport.index', [
        'student_id' => $student->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-20',
    ]));
    $dateRes->assertOk();
    $dateRes->assertSee('01 Sep 2026 s.d. 20 Sep 2026');
});

test('ai student analysis assigns 0% and warning sentiment when 7 kebiasaan is not filled', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::create([
        'name' => 'X-NOHABIT-1',
        'grade' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::create([
        'name' => 'Siswa Belum Isi Habit',
        'nis' => '991101',
        'nisn' => '00991101',
        'gender' => 'L',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $analysisService = app(AiStudentAnalysisService::class);
    $analysis = $analysisService->analyzeStudent($student, '2026-09-01', '2026-09-23');

    expect($analysis['metrics']['habit_score'])->toBe(0.0);
    expect($analysis['metrics']['habit_status'])->toBe('BELUM_TERPANTAU');

    $habitConclusion = collect($analysis['kesimpulan'])->firstWhere('dimension', 'Karakter 7 Kebiasaan');
    expect($habitConclusion)->not->toBeNull();
    expect($habitConclusion['sentiment'])->toBe('warning');
    expect($habitConclusion['insight'])->toContain('belum memiliki catatan pengisian jurnal');
});

test('generate all ai raport endpoint and command generate latest 7 pillars report successfully', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::create([
        'name' => 'X-GENAI-1',
        'grade' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::create([
        'name' => 'Siswa Test GenAI',
        'nis' => '881101',
        'nisn' => '00881101',
        'gender' => 'P',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $admin = User::where('role', 'admin')->first();

    // Call generate-ai endpoint
    $response = $this->actingAs($admin)->post(route('raport.generate_ai'));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify student_ai_raports entry exists
    $aiRaport = StudentAiRaport::where('student_id', $student->id)->first();
    expect($aiRaport)->not->toBeNull();
    expect($aiRaport->context_data)->toHaveKey('pillars');
    expect($aiRaport->context_data['pillars'])->toHaveKey('presensi_harian');
    expect($aiRaport->context_data['pillars'])->toHaveKey('poin_prestasi');
    expect($aiRaport->context_data['pillars'])->toHaveKey('poin_pelanggaran');
    expect($aiRaport->context_data['pillars'])->toHaveKey('tujuh_kegiatan_hebat');
    expect($aiRaport->context_data['pillars'])->toHaveKey('shalat_dzuhur_jumat');
    expect($aiRaport->context_data['pillars'])->toHaveKey('presensi_keputrian');
    expect($aiRaport->context_data['pillars'])->toHaveKey('tahfidz_juz_30');

    // Run artisan command
    $this->artisan('raport:generate-ai')->assertSuccessful();
});

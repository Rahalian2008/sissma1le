<?php

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\AiStudentAnalysisService;
use App\Services\ReportService;

beforeEach(function () {
    // Pastikan tahun ajaran aktif tersedia
    AcademicYear::firstOrCreate(
        ['is_active' => true],
        ['name' => '2026/2027', 'semester' => 'Ganjil', 'start_date' => '2026-07-15', 'end_date' => '2026-12-20']
    );
});

test('dossier is directed to latest student report with full 8 assessment pillars', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $student = Student::with('schoolClass')->where('is_active', true)->first();
    expect($student)->not->toBeNull();

    // Pastikan raport AI ter-generate
    $aiService = app(AiStudentAnalysisService::class);
    $aiService->generateAndSaveStudentReport($student, $admin->id, 'MANUAL');

    // 1. Akses reports.student.dossier
    $response = $this->actingAs($admin)->get(route('reports.student.dossier', $student->id));
    $response->assertStatus(200);

    // Harus menampilkan seluruh 8 pilar standar penilaian
    $response->assertSee('1. Presensi Harian', false);
    $response->assertSee('2. Poin Prestasi', false);
    $response->assertSee('3. Pelanggaran', false);
    $response->assertSee('4. 7 Anak Hebat', false);
    $response->assertSee('5. Shalat Dzuhur (Putra & Putri)', false);
    $response->assertSee('6. Shalat Dhuha', false);
    $response->assertSee('7. Keputrian', false);
    $response->assertSee('8. Tahfidz Juz 30', false);
    $response->assertSee('8 PILAR STANDAR PENILAIAN', false);
    $response->assertSee($student->name);
});

test('dossier print route renders official raport print format with kop and 8 pillars', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $student = Student::with('schoolClass')->where('is_active', true)->first();
    expect($student)->not->toBeNull();

    $responseA4 = $this->actingAs($admin)->get(route('reports.student.print', ['student' => $student->id, 'paper' => 'a4']));
    $responseA4->assertStatus(200);
    $responseA4->assertSee('RAPORT DIGITAL & BERKAS DOSSIER PERKEMBANGAN SISWA', false);
    $responseA4->assertSee('REKAPITULASI 8 PILAR PERKEMBANGAN SISWA', false);
    $responseA4->assertSee($student->name);

    $responseF4 = $this->actingAs($admin)->get(route('reports.student.print', ['student' => $student->id, 'paper' => 'f4']));
    $responseF4->assertStatus(200);
    $responseF4->assertSee('F4 / Folio', false);
});

test('report service getStudentProgressDossier returns enriched latest ai raport, worship, and tahfidz data', function () {
    $student = Student::with('schoolClass')->where('is_active', true)->first();
    expect($student)->not->toBeNull();

    $reportService = app(ReportService::class);
    $dossier = $reportService->getStudentProgressDossier($student);

    expect($dossier)->toHaveKeys([
        'student',
        'latest_ai_raport',
        'ai_analysis',
        'attendances',
        'achievements',
        'violations',
        'worship_summary',
        'tahfidz_summary',
    ]);

    expect($dossier['latest_ai_raport'])->not->toBeNull();
    expect($dossier['worship_summary'])->toHaveKeys(['total', 'hadir', 'dhuha', 'dzuhur', 'keputrian', 'rate']);
    expect($dossier['tahfidz_summary'])->toHaveKeys(['completed_surahs_count', 'total_juz30', 'student_grade', 'progress_pct', 'status']);
});

test('homeroom teacher is restricted to their own class students for dossier', function () {
    $classes = SchoolClass::with('homeroomTeacher.user')->whereHas('homeroomTeacher.user')->take(2)->get();
    if ($classes->count() < 2) {
        test()->markTestSkipped('Need at least 2 classes with homeroom teachers for this test.');
    }

    $classA = $classes[0];
    $classB = $classes[1];

    $homeroomUser = $classA->homeroomTeacher->user;
    expect($homeroomUser)->not->toBeNull();

    $studentA = Student::where('class_id', $classA->id)->first();
    $studentB = Student::where('class_id', $classB->id)->first();

    if ($studentA && $studentB) {
        // Can access student from own class
        $resA = $this->actingAs($homeroomUser)->get(route('reports.student.dossier', $studentA->id));
        $resA->assertStatus(200);

        // Cannot access student from other class
        $resB = $this->actingAs($homeroomUser)->get(route('reports.student.dossier', $studentB->id));
        $resB->assertStatus(403);
    }
});

<?php

use App\Models\BkCounselingSession;
use App\Models\BkHomeVisit;
use App\Models\BkParentCall;
use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestResult;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\BkPsychologicalTestService;
use Database\Seeders\BkPsychologicalTestSeeder;

beforeEach(function () {
    $this->seed(BkPsychologicalTestSeeder::class);

    $this->admin = User::where('role', 'super_admin')->first()
        ?? User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $this->counselor = User::where('role', 'bk')->first()
        ?? User::factory()->create(['role' => 'bk']);

    $this->student = Student::first();
    if (! $this->student) {
        $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => 'X']);
        $this->student = Student::create([
            'name' => 'Siswa Uji Coba BK',
            'nisn' => '0098765432',
            'class_id' => $class->id,
            'gender' => 'L',
        ]);
    }

    $this->studentUser = User::where('role', 'siswa')->first();
    if (! $this->studentUser) {
        $this->studentUser = User::factory()->create([
            'role' => 'siswa',
            'student_id' => $this->student->id,
        ]);
    } else {
        $this->studentUser->update(['student_id' => $this->student->id]);
    }
});

test('counselor and admin can access bk dashboard and all sub-features', function () {
    $response = $this->actingAs($this->counselor)->get(route('bk.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Bimbingan Konseling (BK)', false);

    $routes = [
        route('bk.counseling.index'),
        route('bk.home-visit.index'),
        route('bk.parent-call.index'),
        route('bk.sociometry.index'),
        route('bk.psychological.index'),
        route('bk.psychological.class-report'),
    ];

    foreach ($routes as $url) {
        $res = $this->actingAs($this->counselor)->get($url);
        $res->assertStatus(200);
    }
});

test('counselor can record counseling session and view print view', function () {
    $payload = [
        'student_id' => $this->student->id,
        'date' => now()->toDateString(),
        'field_of_service' => 'BELAJAR',
        'service_category' => 'LAYANAN_RESPONSIF',
        'session_type' => 'INDIVIDUAL',
        'counseling_approach' => 'Solution-Focused Brief Therapy (SFBT)',
        'topic' => 'Mengatasi prokrastinasi belajar menjelang asesmen sumatif',
        'problem_description' => 'Siswa merasa kesulitan membagi waktu antara kegiatan ekstrakurikuler dan belajar mandiri di rumah.',
        'agreement_result' => 'Siswa berkomitmen membatasi screen time maksimal 1 jam pada malam hari.',
        'follow_up_plan' => 'Evaluasi kembali 1 minggu ke depan bersama wali kelas.',
        'status' => 'SEDANG_BERLANGSUNG',
        'place' => 'Ruang Konseling BK',
    ];

    $response = $this->actingAs($this->counselor)->post(route('bk.counseling.store'), $payload);
    $response->assertRedirect();

    $session = BkCounselingSession::where('student_id', $this->student->id)->latest('id')->first();
    expect($session)->not->toBeNull();
    expect($session->field_of_service)->toBe('BELAJAR');
    expect($session->session_code)->toStartWith('BK-');

    $detailRes = $this->actingAs($this->counselor)->get(route('bk.counseling.show', $session));
    $detailRes->assertStatus(200);
    $detailRes->assertSee($session->session_code);

    $printRes = $this->actingAs($this->counselor)->get(route('bk.counseling.print', $session));
    $printRes->assertStatus(200);
    $printRes->assertSee('BERITA ACARA LAYANAN BIMBINGAN', false);
});

test('counselor can record home visit and print official report', function () {
    $payload = [
        'student_id' => $this->student->id,
        'visit_date' => now()->toDateString(),
        'address_visited' => 'Kp. Babakan RT 02 RW 04, Leles, Garut',
        'parent_name' => 'Bapak & Ibu Wali Siswa',
        'case_background' => 'Siswa tidak hadir 3 hari berturut-turut tanpa keterangan.',
        'family_condition' => 'Hubungan orang tua dan anak harmonis, orang tua sibuk bekerja di luar kota.',
        'visit_result' => 'Orang tua baru mengetahui absennya siswa dan menyepakati pengawasan lebih ketat.',
        'parent_commitment' => 'Orang tua akan memantau kehadiran siswa setiap pagi melalui portal wali.',
        'status' => 'TERLAKSANA',
    ];

    $response = $this->actingAs($this->counselor)->post(route('bk.home-visit.store'), $payload);
    $response->assertRedirect();

    $visit = BkHomeVisit::where('student_id', $this->student->id)->latest('id')->first();
    expect($visit)->not->toBeNull();
    expect($visit->visit_code)->toStartWith('HV-');

    $printRes = $this->actingAs($this->counselor)->get(route('bk.home-visit.print', $visit));
    $printRes->assertStatus(200);
    $printRes->assertSee('KUNJUNGAN RUMAH (HOME VISIT)', false);
});

test('counselor can create parent call and print invitation letter and berita acara', function () {
    $payload = [
        'student_id' => $this->student->id,
        'call_date' => now()->addDays(2)->toDateString(),
        'call_time' => '09:00',
        'place' => 'Ruang Tamu BK SMAN 1 Leles',
        'reason' => 'Konsultasi perkembangan kedisiplinan dan capaian pembelajaran semester berjalan.',
        'status' => 'SURAT_DIKIRIM',
    ];

    $response = $this->actingAs($this->counselor)->post(route('bk.parent-call.store'), $payload);
    $response->assertRedirect();

    $call = BkParentCall::where('student_id', $this->student->id)->latest('id')->first();
    expect($call)->not->toBeNull();
    expect($call->call_code)->toStartWith('PGL-');

    $letterRes = $this->actingAs($this->counselor)->get(route('bk.parent-call.print-letter', $call));
    $letterRes->assertStatus(200);
    $letterRes->assertSee('SURAT PANGGILAN ORANG TUA', false);

    $baRes = $this->actingAs($this->counselor)->get(route('bk.parent-call.print-berita-acara', $call));
    $baRes->assertStatus(200);
    $baRes->assertSee('BERITA ACARA PERTEMUAN DENGAN ORANG TUA', false);
});

test('psychological test service calculates VAK and RIASEC scores accurately', function () {
    $service = app(BkPsychologicalTestService::class);
    $vakTest = BkPsychologicalTest::where('slug', 'vak')->first();
    $riasecTest = BkPsychologicalTest::where('slug', 'riasec')->first();

    expect($vakTest)->not->toBeNull();
    expect($riasecTest)->not->toBeNull();

    // Jawaban simulasi dominan visual untuk VAK
    $answersVak = [];
    foreach ($vakTest->questions as $q) {
        $answersVak[$q->id] = ($q->dimension === 'VISUAL') ? 4 : 1;
    }

    $vakResult = $service->evaluateTest($vakTest, $this->student, $answersVak);
    expect($vakResult->dominant_dimension)->toBe('VISUAL');
    expect($vakResult->profile_label)->toContain('Visual');
    expect($vakResult->career_recommendations)->toBeArray();
    expect($vakResult->dimension_scores['percentages']['VISUAL'])->toBeGreaterThan(50);

    // Jawaban simulasi dominan R-I-A untuk Holland RIASEC
    $answersRiasec = [];
    foreach ($riasecTest->questions as $q) {
        $answersRiasec[$q->id] = match ($q->dimension) {
            'R' => 4,
            'I' => 3,
            'A' => 3,
            default => 1,
        };
    }

    $riasecResult = $service->evaluateTest($riasecTest, $this->student, $answersRiasec);
    expect($riasecResult->dominant_dimension)->toBe('R');
    expect($riasecResult->holland_code)->toStartWith('R');
    expect($riasecResult->career_recommendations)->toBeArray();
    expect(count($riasecResult->career_recommendations))->toBeGreaterThan(0);
});

test('student can take psychological test and view result and print document', function () {
    $vakTest = BkPsychologicalTest::where('slug', 'vak')->first();

    $takeRes = $this->actingAs($this->studentUser)->get(route('bk.psychological.take', $vakTest->slug));
    $takeRes->assertStatus(200);
    $takeRes->assertSee($vakTest->name);

    $answers = [];
    foreach ($vakTest->questions as $q) {
        $answers[$q->id] = 3;
    }

    $submitRes = $this->actingAs($this->studentUser)->post(route('bk.psychological.submit', $vakTest->slug), [
        'answers' => $answers,
    ]);

    $submitRes->assertRedirect();

    $result = BkPsychologicalTestResult::where('student_id', $this->student->id)
        ->where('test_id', $vakTest->id)
        ->latest('id')
        ->first();

    expect($result)->not->toBeNull();

    $resultPage = $this->actingAs($this->studentUser)->get(route('bk.psychological.result', $result));
    $resultPage->assertStatus(200);
    $resultPage->assertSee($result->profile_label);

    $printPage = $this->actingAs($this->studentUser)->get(route('bk.psychological.print', $result));
    $printPage->assertStatus(200);
    $printPage->assertSee('LAPORAN HASIL ASESMEN PSIKOLOGIS PESERTA DIDIK', false);
});

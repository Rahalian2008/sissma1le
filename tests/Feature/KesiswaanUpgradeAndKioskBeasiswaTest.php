<?php

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->withoutMiddleware();

    $this->admin = User::where('role', 'super_admin')->first()
        ?? User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $this->wakasekKesiswaan = User::where('role', 'wakasek_kesiswaan')->first()
        ?? User::factory()->create(['role' => 'wakasek_kesiswaan']);

    $this->student = Student::first();
    if (! $this->student) {
        $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => 'X']);
        $this->student = Student::create([
            'name' => 'Siswa Beasiswa Test',
            'nisn' => '0098765499',
            'class_id' => $class->id,
            'gender' => 'L',
        ]);
    }
});

test('wakasek kesiswaan and admin can access scholarship index and print with official kop', function () {
    $res = $this->actingAs($this->wakasekKesiswaan)->get(route('students.scholarships.index'));
    $res->assertStatus(200);
    $res->assertSee('Buku Bantuan & Beasiswa Siswa', false);
    $res->assertSee('Tanggal Usulan', false);
    $res->assertSee('Tanggal SK', false);
    $res->assertSee('Tanggal Cair', false);

    $printRes = $this->actingAs($this->wakasekKesiswaan)->get(route('students.scholarships.print'));
    $printRes->assertStatus(200);
    $printRes->assertSee('DAFTAR REKAPITULASI PENERIMA BEASISWA', false);
    $printRes->assertSee('Wakil Kepala Sekolah Bidang Kesiswaan', false);
});

test('wakasek kesiswaan can create scholarship with date fields', function () {
    $data = [
        'student_id' => $this->student->id,
        'scholarship_name' => 'PIP Kemendikbudristek Test',
        'source' => 'Pemerintah Pusat',
        'amount' => 1800000,
        'period' => '2026/2027',
        'status' => 'SK',
        'sk_number' => 'SK/PIP/2026/001',
        'proposal_date' => '2026-08-01',
        'sk_date' => '2026-09-15',
        'disbursement_date' => '2026-10-01',
    ];

    $res = $this->actingAs($this->wakasekKesiswaan)
        ->from(route('students.scholarships.index'))
        ->post(route('students.scholarships.store'), $data);

    $res->assertRedirect(route('students.scholarships.index'));
    $res->assertSessionHasNoErrors();

    $this->assertDatabaseHas('student_scholarships', [
        'student_id' => $this->student->id,
        'scholarship_name' => 'PIP Kemendikbudristek Test',
        'sk_number' => 'SK/PIP/2026/001',
        'status' => 'SK',
    ]);
});

test('adm kesiswaan modules tracer alumni, prestasi, pelanggaran, and bk counseling are accessible', function () {
    $routes = [
        route('tu.kesiswaan.tracer_alumni.index'),
        route('tu.kesiswaan.tracer_alumni.print'),
        route('tu.kesiswaan.achievements.index'),
        route('tu.kesiswaan.achievements.print'),
        route('tu.kesiswaan.violations.index'),
        route('tu.kesiswaan.violations.print'),
        route('tu.kesiswaan.bk_counseling.index'),
        route('tu.kesiswaan.bk_counseling.print'),
    ];

    foreach ($routes as $url) {
        $res = $this->actingAs($this->admin)->get($url);
        $res->assertStatus(200);
    }
});

test('kiosk controller provides scholarship data and renders slide 14 beasiswa and slide 15 tamu', function () {
    $dataRes = $this->get(route('kiosk.data'));
    $dataRes->assertStatus(200);
    $json = $dataRes->json();
    expect($json)->toHaveKey('scholarships');
    expect($json['scholarships'])->toHaveKeys(['total_recipients', 'pip_count', 'ketm_count', 'recent', 'pic']);

    $indexRes = $this->get(route('kiosk.index'));
    $indexRes->assertStatus(200);
    $indexRes->assertSee('id="slide-14"', false);
    $indexRes->assertSee('id="slide-15"', false);
    $indexRes->assertSee('15. Beasiswa', false);
    $indexRes->assertSee('16. Tamu', false);
    $indexRes->assertSee('const TOTAL_SLIDES = 16;', false);
});

test('website content agenda supports countdown and feeds dynamic countdown into kiosk tv', function () {
    // 1. Tambah Agenda dengan is_countdown = true
    $payload = [
        'agenda_title' => 'Simulasi UTBK SNBT 2027',
        'agenda_date' => now()->addDays(15)->format('Y-m-d'),
        'agenda_tag' => 'Target Kelulusan',
        'agenda_description' => 'Uji coba asesmen terstandar nasional untuk kelas XII',
        'agenda_is_countdown' => '1',
        'agenda_countdown_badge' => 'SNBT Resmi',
    ];

    $res = $this->actingAs($this->admin)->post(route('master.website_content.agenda.store'), $payload);
    $res->assertRedirect();
    $res->assertSessionHas('success');

    // 2. Akses kiosk data
    $dataRes = $this->get(route('kiosk.data'));
    $dataRes->assertStatus(200);
    $json = $dataRes->json();

    expect($json)->toHaveKey('agenda_countdowns');
    $countdowns = collect($json['agenda_countdowns']);
    $item = $countdowns->firstWhere('title', 'Simulasi UTBK SNBT 2027');
    expect($item)->not->toBeNull();
    expect($item['days_left'])->toBe(15);
    expect($item['badge'])->toBe('SNBT Resmi');

    // 3. Akses Kiosk TV Index view
    $viewRes = $this->get(route('kiosk.index'));
    $viewRes->assertStatus(200);
    $viewRes->assertSee('id="kioskCountdownList"', false);
    $viewRes->assertSee('Simulasi UTBK SNBT 2027', false);
    $viewRes->assertSee('SNBT Resmi', false);
});

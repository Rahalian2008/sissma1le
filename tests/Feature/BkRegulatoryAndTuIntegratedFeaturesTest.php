<?php

use App\Models\BkAkpd;
use App\Models\BkRpl;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\BkRegulatoryDataSeeder;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->seed(BkRegulatoryDataSeeder::class);

    $this->admin = User::where('role', 'super_admin')->first()
        ?? User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $this->student = Student::first();
    if (! $this->student) {
        $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => 'X']);
        $this->student = Student::create([
            'name' => 'Siswa Regulatory Test',
            'nisn' => '0098765432',
            'class_id' => $class->id,
            'gender' => 'L',
            'is_active' => true,
        ]);
    }
});

test('guru bk or admin can access akpd diagnostic modules and take instrument', function () {
    $akpd = BkAkpd::first();
    expect($akpd)->not->toBeNull();

    // 1. Index
    $res = $this->actingAs($this->admin)->get(route('bk.akpd.index'));
    $res->assertStatus(200);
    $res->assertSee('AKPD Diagnostik', false);

    // 2. Show
    $resShow = $this->actingAs($this->admin)->get(route('bk.akpd.show', $akpd));
    $resShow->assertStatus(200);
    $resShow->assertSee($akpd->title, false);

    // 3. Take
    $resTake = $this->actingAs($this->admin)->get(route('bk.akpd.take', $akpd));
    $resTake->assertStatus(200);
    $resTake->assertSee('Instrumen AKPD', false);

    // 4. Print Analysis
    $resPrint = $this->actingAs($this->admin)->get(route('bk.akpd.print-analysis', $akpd));
    $resPrint->assertStatus(200);
    $resPrint->assertSee('PROFIL KEBUTUHAN PESERTA DIDIK', false);
});

test('guru bk or admin can access rpl bk 1 lembar kurikulum merdeka', function () {
    $rpl = BkRpl::first();
    expect($rpl)->not->toBeNull();

    // 1. Index
    $res = $this->actingAs($this->admin)->get(route('bk.rpl.index'));
    $res->assertStatus(200);
    $res->assertSee('RPL BK 1 Lembar', false);

    // 2. Create
    $resCreate = $this->actingAs($this->admin)->get(route('bk.rpl.create'));
    $resCreate->assertStatus(200);

    // 3. Show
    $resShow = $this->actingAs($this->admin)->get(route('bk.rpl.show', $rpl));
    $resShow->assertStatus(200);
    $resShow->assertSee($rpl->topic, false);

    // 4. Print
    $resPrint = $this->actingAs($this->admin)->get(route('bk.rpl.print', $rpl));
    $resPrint->assertStatus(200);
    $resPrint->assertSee('RENCANA PELAKSANAAN LAYANAN', false);
});

test('guru bk or admin can access case conference and referral modules', function () {
    // 1. Case Conference Index & Create
    $resCC = $this->actingAs($this->admin)->get(route('bk.case-conference.index'));
    $resCC->assertStatus(200);
    $resCC->assertSee('Konferensi Kasus', false);

    $resCCCreate = $this->actingAs($this->admin)->get(route('bk.case-conference.create'));
    $resCCCreate->assertStatus(200);

    // 2. Referral Index & Create
    $resRef = $this->actingAs($this->admin)->get(route('bk.referral.index'));
    $resRef->assertStatus(200);
    $resRef->assertSee('Alih Tangan Kasus', false);

    $resRefCreate = $this->actingAs($this->admin)->get(route('bk.referral.create'));
    $resRefCreate->assertStatus(200);
});

test('tu integrated routes for kohort rombel and beasiswa function correctly', function () {
    // 1. Kohort Siswa
    $resCohort = $this->actingAs($this->admin)->get(route('students.cohort.index'));
    $resCohort->assertStatus(200);
    $resCohort->assertSee('Kohort', false);

    // 2. Penataan Rombel & Kenaikan Kelas Massal
    $resRombel = $this->actingAs($this->admin)->get(route('students.rombel.index'));
    $resRombel->assertStatus(200);
    $resRombel->assertSee('Rombel', false);

    // 3. Beasiswa Siswa PIP KETM
    $resScholarship = $this->actingAs($this->admin)->get(route('students.scholarships.index'));
    $resScholarship->assertStatus(200);
    $resScholarship->assertSee('Beasiswa', false);
});

test('tu integrated routes for logistik atk sppd and arsip function correctly', function () {
    // 1. Logistik ATK: Penerimaan, Pengeluaran, Peminjaman, Opname (jika tabel warehouse tersedia di database)
    if (Schema::hasTable('goods_receipts')) {
        $this->actingAs($this->admin)->get(route('warehouse.receipts.index'))->assertStatus(200);
    }
    if (Schema::hasTable('goods_issuances')) {
        $this->actingAs($this->admin)->get(route('warehouse.issuances.index'))->assertStatus(200);
    }
    if (Schema::hasTable('goods_borrowings')) {
        $this->actingAs($this->admin)->get(route('warehouse.borrowings.index'))->assertStatus(200);
    }
    if (Schema::hasTable('goods_stock_opnames')) {
        $this->actingAs($this->admin)->get(route('warehouse.stock-opnames.index'))->assertStatus(200);
    }

    // 2. Persuratan: SPPD Dinas, Klasifikasi Surat, Arsip Digital
    if (Schema::hasTable('official_trips')) {
        $this->actingAs($this->admin)->get(route('letters.sppd.index'))->assertStatus(200);
    }
    if (Schema::hasTable('letter_classifications')) {
        $this->actingAs($this->admin)->get(route('letters.classifications.index'))->assertStatus(200);
    }
    if (Schema::hasTable('archives')) {
        $this->actingAs($this->admin)->get(route('archives.index'))->assertStatus(200);
    }

    // 3. Kepegawaian: DUK & DSO
    if (Schema::hasTable('employees')) {
        $this->actingAs($this->admin)->get(route('duk.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('dso.index'))->assertStatus(200);
    }
});

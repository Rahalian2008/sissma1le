<?php

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::where('role', 'super_admin')->first()
        ?? User::where('role', 'admin')->first()
        ?? User::factory()->create(['role' => 'admin']);

    $this->student = Student::first();
    if (! $this->student) {
        $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => 'X']);
        $this->student = Student::create([
            'name' => 'Siswa Test',
            'nisn' => '0098765431',
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
    }
});

test('admin can access all 5 adm ketatausahaan sub menus', function () {
    $submenus = [
        route('tu.kesiswaan.index') => 'Administrasi Kesiswaan',
        route('tu.kepegawaian.index') => 'Administrasi Kepegawaian',
        route('tu.persuratan.index') => 'Tata Naskah',
        route('tu.sarpras.index') => 'Sarana Prasarana',
        route('tu.keuangan.index') => 'Administrasi Keuangan Sekolah',
    ];

    foreach ($submenus as $url => $title) {
        $res = $this->actingAs($this->admin)->get($url);
        $res->assertStatus(200);
        $res->assertSee($title, false);
    }
});

test('adm kesiswaan page displays core modules and statistics', function () {
    $res = $this->actingAs($this->admin)->get(route('tu.kesiswaan.index'));
    $res->assertStatus(200);
    $res->assertSee('Buku Induk', false);
    $res->assertSee('Buku Mutasi', false);
    $res->assertSee('Klaper Siswa', false);
    $res->assertSee('PPDB Baru', false);
});

test('adm kepegawaian page displays personnel records and duk', function () {
    $res = $this->actingAs($this->admin)->get(route('tu.kepegawaian.index'));
    $res->assertStatus(200);
    $res->assertSee('Buku Induk PTK', false);
    $res->assertSee('Daftar Urut Kepangkatan (DUK)', false);
    $res->assertSee('Struktur DSO', false);
});

test('adm persuratan page displays letters agenda and disposition system', function () {
    $res = $this->actingAs($this->admin)->get(route('tu.persuratan.index'));
    $res->assertStatus(200);
    $res->assertSee('Catat Surat Masuk', false);
    $res->assertSee('Buat Surat Keluar', false);
    $res->assertSee('Disposisi Berjalan', false);
    $res->assertSee('SPPD Dinas', false);
});

test('adm sarpras page displays regional asset inventory and warehouse logistics', function () {
    $res = $this->actingAs($this->admin)->get(route('tu.sarpras.index'));
    $res->assertStatus(200);
    $res->assertSee('Katalog KIB Aset', false);
    $res->assertSee('KIR Ruangan', false);
    $res->assertSee('KIB A-F', false);
    $res->assertSee('Buku BII', false);
});

test('adm keuangan page displays general ledger and cash transactions', function () {
    $res = $this->actingAs($this->admin)->get(route('tu.keuangan.index'));
    $res->assertStatus(200);
    $res->assertSee('Buku Kas Umum (BKU)', false);
    $res->assertSee('RKAS Anggaran', false);
    $res->assertSee('Buku BKU', false);
    $res->assertSee('Buku Pajak', false);
});

test('unauthorized student is restricted from accessing administrative submenus', function () {
    $res = $this->actingAs($this->studentUser)->get(route('tu.keuangan.index'));
    expect(in_array($res->status(), [302, 403]))->toBeTrue();
});

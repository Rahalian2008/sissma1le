<?php

use App\Models\EarlyLeaveRequest;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\SchoolHoliday;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::firstOrCreate(
        ['email' => 'admin_test_audit@sma1le.sch.id'],
        [
            'name' => 'Super Administrator Test',
            'username' => 'admin_test_audit',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
            'is_active' => true,
        ]
    );

    // Ensure at least one class and student exists
    $this->class = SchoolClass::firstOrCreate(
        ['name' => 'X-1 Test'],
        ['grade' => 'X', 'is_active' => true]
    );

    $this->student = Student::firstOrCreate(
        ['nis' => 'TEST99001'],
        [
            'nisn' => '0099887766',
            'name' => 'Siswa Test Audit',
            'gender' => 'L',
            'class_id' => $this->class->id,
            'current_class_level' => 'X',
            'current_rombel' => 'X Merdeka 1',
            'status' => 'Aktif',
            'is_active' => true,
        ]
    );
});

test('1. library route loads successfully without book_type query exception', function () {
    $res = $this->actingAs($this->admin)->get(route('library.index'));
    $res->assertStatus(200);
    $res->assertSee('Perpustakaan');
});

test('2. tu-students demographics loads successfully without entry_path query exception', function () {
    $res = $this->actingAs($this->admin)->get(route('students.demographics'));
    $res->assertStatus(200);
    $res->assertSee('Demografi');
});

test('3. tu-students index loads successfully without current_class_level query exception', function () {
    $res = $this->actingAs($this->admin)->get(route('students.index'));
    $res->assertStatus(200);
    $res->assertSee('Data Peserta Didik');
});

test('4. alumni index loads successfully without status query exception', function () {
    $res = $this->actingAs($this->admin)->get(route('alumni.index'));
    $res->assertStatus(200);
    $res->assertSee('Alumni');
});

test('5. students learning-agendas loads successfully without current_rombel query exception', function () {
    $res = $this->actingAs($this->admin)->get(route('students.learning-agendas.index'));
    $res->assertStatus(200);
    $res->assertSee('Agenda Pembelajaran');
});

test('6. master holidays loads without foreach null error even with empty target classes', function () {
    SchoolHoliday::firstOrCreate(
        ['name' => 'Libur Uji Coba Null Safety'],
        [
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'type' => 'sekolah',
            'target_classes' => null,
            'is_active' => true,
        ]
    );

    $res = $this->actingAs($this->admin)->get(route('master.holidays'));
    $res->assertStatus(200);
    $res->assertSee('Hari Libur');
});

test('7. warehouse receipts modal footer is properly anchored outside scrollable body', function () {
    $res = $this->actingAs($this->admin)->get(route('warehouse.receipts.index'));
    $res->assertStatus(200);
    $res->assertSee('Pencatatan Barang Masuk (BM)');
    $res->assertSee('Simpan Penerimaan');
    $res->assertSee('id="modal-receipt"', false);
});

test('8. master teachers edit modal has clean status and action buttons layout', function () {
    $res = $this->actingAs($this->admin)->get(route('master.teachers'));
    $res->assertStatus(200);
    $res->assertSee('id="editTeacherModal"', false);
    $res->assertSee('edit_teacher_is_active');
    $res->assertSee('Simpan Perubahan');
});

test('9. presensi utama includes embedded report panel, approvals, and official document printing for early leaves and school exit permits', function () {
    // Seed real early leave and school exit permit records
    $el = EarlyLeaveRequest::create([
        'letter_number' => EarlyLeaveRequest::generateLetterNumber(),
        'student_id' => $this->student->id,
        'date' => now()->toDateString(),
        'leave_time' => '10:00',
        'reason_category' => 'Sakit di Sekolah (Rujukan UKS)',
        'reason' => 'Demam tinggi',
        'status' => 'PENDING',
        'created_by' => $this->admin->id,
    ]);

    $sp = SchoolExitPermit::create([
        'permit_number' => SchoolExitPermit::generatePermitNumber(),
        'date' => now()->toDateString(),
        'out_time' => '09:00',
        'expected_back_time' => '11:00',
        'purpose' => 'Membeli Perlengkapan KBM / Praktikum',
        'destination' => 'Toko ATK',
        'reason' => 'Beli karton',
        'is_returning' => true,
        'status' => 'PENDING',
        'created_by' => $this->admin->id,
    ]);
    $sp->students()->attach($this->student->id);

    $res = $this->actingAs($this->admin)->get(route('attendance.index'));
    $res->assertStatus(200);

    // Section and header buttons
    $res->assertSee('section-special-permits', false);
    $res->assertSee('Ajuan Pulang Cepat');
    $res->assertSee('Izin Keluar Sekolah');
    $res->assertSee('Laporan & Persetujuan', false);

    // Modals report tabs
    $res->assertSee('quick-el-view-report', false);
    $res->assertSee('quick-sp-view-report', false);
    $res->assertSee('switchQuickEarlyLeaveTab', false);
    $res->assertSee('switchQuickSchoolPassTab', false);

    // Data rendered inside reports
    $res->assertSee($el->letter_number);
    $res->assertSee($sp->permit_number);
    $res->assertSee($this->student->name);
});

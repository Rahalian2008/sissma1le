<?php

use App\Models\AcademicYear;
use App\Models\EarlyLeaveRequest;
use App\Models\PicketSchedule;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    PicketSchedule::query()->delete();

    SchoolIdentity::firstOrCreate(['id' => 1], [
        'name' => 'SMA NEGERI 1 LENGKONG',
        'npsn' => '20202211',
        'address' => 'Jl. Raya Lengkong No. 45 Sukabumi',
        'phone' => '0266-123456',
        'email' => 'info@sman1lengkong.sch.id',
        'headmaster_name' => 'Drs. H. Ahmad Fauzi, M.Pd.',
        'headmaster_nip' => '196805121994031005',
    ]);

    $this->academicYear = AcademicYear::firstOrCreate(
        ['is_active' => true],
        ['name' => '2026/2027', 'semester' => 'Ganjil', 'start_date' => '2026-07-15', 'end_date' => '2027-06-25']
    );

    $this->homeroomTeacher = Teacher::create([
        'name' => 'Dra. Hj. Siti Rohmah, M.Pd.',
        'nip' => '197508102000032001',
        'gender' => 'P',
        'title' => 'Wali Kelas X-1',
        'is_active' => true,
    ]);

    $this->class = SchoolClass::create([
        'name' => 'X-1',
        'grade' => 'X',
        'major' => 'Umum',
        'academic_year_id' => $this->academicYear->id,
        'homeroom_teacher_id' => $this->homeroomTeacher->id,
    ]);

    $this->student = Student::create([
        'name' => 'Ahmad Maulana Mansur',
        'nis' => '10203001',
        'nisn' => '0051234567',
        'gender' => 'L',
        'class_id' => $this->class->id,
        'is_active' => true,
    ]);

    $this->adminUser = User::create([
        'name' => 'Super Administrator',
        'email' => 'admin.picket.test@sman1lengkong.sch.id',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'roles' => ['admin', 'super_admin'],
    ]);

    $this->guruPiketTeacher = Teacher::create([
        'name' => 'Drs. Joko Susilo, M.Pd.',
        'nip' => '197802142005011003',
        'gender' => 'L',
        'title' => 'Guru Fisika & Guru Piket',
        'is_active' => true,
    ]);

    $this->picketUser = User::create([
        'name' => 'Drs. Joko Susilo, M.Pd.',
        'email' => 'joko.piket@sman1lengkong.sch.id',
        'password' => bcrypt('password'),
        'role' => 'guru',
        'roles' => ['guru', 'guru_piket', 'petugas_piket'],
    ]);

    $this->guruPiketTeacher->update(['user_id' => $this->picketUser->id]);
});

test('attendance index contains grouped action buttons including picket schedule and early leave', function () {
    $res = $this->actingAs($this->adminUser)->get(route('attendance.index'));
    $res->assertStatus(200);

    // Verify all 7 buttons are present in the header
    $res->assertSee('Izin Keluar Sekolah');
    $res->assertSee('Izin Pulang Cepat');
    $res->assertSee('Jadwal Petugas Piket');
    $res->assertSee('Laporan & Persetujuan', false);
    $res->assertSee('Cetak Rekap Kelas (Tanpa Nama)');
    $res->assertSee('Dokumen Laporan');
    $res->assertSee('Cetak Rekap & Kartu', false);
});

test('admin can manage picket schedules and print weekly picket roster', function () {
    $todayName = PicketSchedule::indonesianDayName(Carbon::now());
    $dayNumbers = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];

    // Store a picket schedule
    $storeRes = $this->actingAs($this->adminUser)->post(route('attendance.picket-schedules.store'), [
        'day_name' => $todayName,
        'teacher_id' => $this->guruPiketTeacher->id,
        'person_name' => $this->guruPiketTeacher->name,
        'role_type' => 'GURU_PIKET',
        'location' => 'Lobi Utama & Gerbang Barat',
        'start_time' => '06:30',
        'end_time' => '15:30',
        'notes' => 'Piket ketertiban dan perizinan keluar sekolah',
    ]);

    $storeRes->assertSessionHas('success');

    $picket = PicketSchedule::where('person_name', $this->guruPiketTeacher->name)->first();
    expect($picket)->not->toBeNull();
    expect($picket->day_name)->toBe($todayName);
    expect($picket->day_number)->toBe($dayNumbers[$todayName] ?? 1);

    // Print weekly picket schedule
    $printRes = $this->actingAs($this->adminUser)->get(route('attendance.picket-schedules.print'));
    $printRes->assertStatus(200);
    $printRes->assertSee('JADWAL PETUGAS PIKET KETERTIBAN', false);
    $printRes->assertSee($this->guruPiketTeacher->name);
    $printRes->assertSee('Lobi Utama', false);
});

test('early leave letter has no letter number and has 4 required signatures with return notice', function () {
    $todayName = PicketSchedule::indonesianDayName(Carbon::now());
    PicketSchedule::create([
        'day_name' => $todayName,
        'day_number' => 1,
        'teacher_id' => $this->guruPiketTeacher->id,
        'person_name' => $this->guruPiketTeacher->name,
        'role_type' => 'GURU_PIKET',
        'location' => 'Pos Piket',
        'start_time' => '06:30',
        'end_time' => '15:30',
        'is_active' => true,
    ]);

    $earlyLeave = EarlyLeaveRequest::create([
        'student_id' => $this->student->id,
        'letter_number' => '421.3/SKPC/0099/XI/2026',
        'date' => Carbon::now()->toDateString(),
        'leave_time' => '11:45',
        'reason_category' => 'Sakit di Sekolah (Rujukan UKS)',
        'reason' => 'Mengalami demam tinggi dan mimisan di kelas.',
        'pickup_name' => 'H. Mansur (Ayah Kandung)',
        'pickup_relationship' => 'Orang Tua (Ayah)',
        'pickup_phone' => '081234567899',
        'status' => 'APPROVED',
        'approved_by' => $this->picketUser->id,
        'approved_at' => Carbon::now(),
    ]);

    $printRes = $this->actingAs($this->picketUser)->get(route('attendance.early-leave.print', $earlyLeave->id));
    $printRes->assertStatus(200);

    // 1. Must NOT have letter number
    $printRes->assertDontSee('Nomor:');
    $printRes->assertDontSee('421.3/SKPC/0099/XI/2026');
    $printRes->assertSee('SURAT IZIN PULANG CEPAT');

    // 2. Signatures: Student, Parent, Homeroom Teacher, Picket Officer
    $printRes->assertSee('Pemohon (Siswa)');
    $printRes->assertSee('Ahmad Maulana Mansur');
    $printRes->assertSee('Orang Tua / Wali Siswa');
    $printRes->assertSee('H. Mansur (Ayah Kandung)');
    $printRes->assertSee('Wali Kelas X-1');
    $printRes->assertSee('Dra. Hj. Siti Rohmah, M.Pd.');
    $printRes->assertSee('Petugas Piket');
    $printRes->assertSee('Drs. Joko Susilo, M.Pd.');

    // 3. Official archive return notice
    $printRes->assertSee('wajib ditandatangani oleh Orang Tua/Wali Siswa');
    $printRes->assertSee('dikembalikan ke sekolah');
});

test('school pass letter has no letter number and has 3 required signatures without principal', function () {
    $todayName = PicketSchedule::indonesianDayName(Carbon::now());
    PicketSchedule::create([
        'day_name' => $todayName,
        'day_number' => 1,
        'teacher_id' => $this->guruPiketTeacher->id,
        'person_name' => $this->guruPiketTeacher->name,
        'role_type' => 'GURU_PIKET',
        'location' => 'Pos Piket',
        'start_time' => '06:30',
        'end_time' => '15:30',
        'is_active' => true,
    ]);

    $permit = SchoolExitPermit::create([
        'permit_number' => '421.3/SIK-LS/0099/XI/2026',
        'date' => Carbon::now()->toDateString(),
        'out_time' => '10:00',
        'expected_back_time' => '11:30',
        'purpose' => 'Tugas OSIS / Delegasi Lomba',
        'destination' => 'Kantor Kecamatan Lengkong',
        'reason' => 'Mengantarkan berkas undangan kegiatan HUT SMA1LE.',
        'accompanied_by' => 'Mandiri (Dispensasi KBM)',
        'is_returning' => true,
        'status' => 'APPROVED',
        'approved_by' => $this->picketUser->id,
        'approved_at' => Carbon::now(),
    ]);

    $permit->students()->attach($this->student->id);

    $printRes = $this->actingAs($this->picketUser)->get(route('attendance.school-pass.print', $permit->id));
    $printRes->assertStatus(200);

    // 1. Must NOT have letter number
    $printRes->assertDontSee('Nomor:');
    $printRes->assertDontSee('421.3/SIK-LS/0099/XI/2026');

    // 2. Signatures: Student, Homeroom Teacher, Picket Officer
    $printRes->assertSee('Pemohon (Siswa)');
    $printRes->assertSee('Ahmad Maulana Mansur');
    $printRes->assertSee('Wali Kelas X-1');
    $printRes->assertSee('Dra. Hj. Siti Rohmah, M.Pd.');
    $printRes->assertSee('Petugas Piket');
    $printRes->assertSee('Drs. Joko Susilo, M.Pd.');

    // 3. Must NOT have principal signature
    $printRes->assertDontSee('Kepala Sekolah');
});

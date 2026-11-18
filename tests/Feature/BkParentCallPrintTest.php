<?php

use App\Models\BkParentCall;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Str;

test('bk parent call print letter displays coordinator bk and homeroom teacher', function () {
    $rand = Str::lower(Str::random(6));
    $homeroomUser = User::create([
        'name' => 'Wali Kelas X-1 S.Pd.',
        'email' => "walikelas_{$rand}@sman1lengkong.sch.id",
        'username' => "wali_{$rand}",
        'nip' => '198501012010011999',
        'role' => 'guru',
        'roles' => ['guru', 'wali_kelas'],
        'password' => bcrypt('password'),
    ]);

    $homeroomTeacher = Teacher::create([
        'user_id' => $homeroomUser->id,
        'name' => 'Wali Kelas X-1',
        'title_suffix' => 'S.Pd.',
        'nip' => '198501012010011999',
        'employment_type' => 'Guru',
        'is_active' => true,
    ]);

    $class = SchoolClass::create([
        'name' => 'X Merdeka 1',
        'grade' => 'X',
        'major' => 'Umum',
        'homeroom_teacher_id' => $homeroomTeacher->id,
    ]);

    $student = Student::create([
        'nis' => '99'.rand(1000, 9999),
        'nisn' => '00'.rand(10000000, 99999999),
        'name' => 'Siswa Panggilan BK Test',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $admin = User::where('role', 'admin')->first() ?? User::create([
        'name' => 'Admin Test BK',
        'email' => "admin_bk_{$rand}@sman1lengkong.sch.id",
        'username' => "admin_bk_{$rand}",
        'role' => 'admin',
        'password' => bcrypt('password'),
    ]);

    $parentCall = BkParentCall::create([
        'call_code' => 'PGL-'.rand(1000, 9999),
        'student_id' => $student->id,
        'counselor_id' => $admin->id,
        'letter_number' => '421.3/001-BK/SMA1LE/XI/2026',
        'call_date' => now()->toDateString(),
        'call_time' => '08:30 WIB',
        'place' => 'Ruang BK',
        'reason' => 'Musyawarah pembinaan',
        'status' => 'SURAT_DIKIRIM',
    ]);

    $bkCoordinator = Teacher::getBkCoordinator();

    $response = $this->actingAs($admin)
        ->get(route('bk.parent-call.print-letter', $parentCall));

    $response->assertStatus(200);

    // Verifikasi Koordinator BK di tanda tangan
    $response->assertSee('Koordinator BK');
    $response->assertSee($bkCoordinator['name']);
    $response->assertSee($bkCoordinator['nip']);

    // Verifikasi Wali Kelas di tanda tangan
    $response->assertSee('Wali Kelas X Merdeka 1');
    $response->assertSee('Wali Kelas X-1, S.Pd.');
    $response->assertSee('198501012010011999');

    // Verifikasi tidak ada lagi Wakasek Kesiswaan di surat panggilan
    $response->assertDontSee('Wakasek Bidang Kesiswaan');

    // Verifikasi struktur 1 lembar
    $response->assertSee('printable-sheet');
    $response->assertSee('page-break-inside: avoid');
});

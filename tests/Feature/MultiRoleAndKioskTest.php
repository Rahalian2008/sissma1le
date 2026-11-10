<?php

use App\Models\Attendance;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 07:00:00', 'Asia/Jakarta'));
});

test('user model supports multiple roles via roles column and all_roles accessor', function () {
    $user = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa', 'sekretaris_kelas', 'koordinator_keagamaan'],
    ]);

    expect($user->all_roles)->toContain('siswa', 'sekretaris_kelas', 'koordinator_keagamaan')
        ->and($user->hasRole('sekretaris_kelas'))->toBeTrue()
        ->and($user->hasRole('koordinator_keagamaan'))->toBeTrue()
        ->and($user->hasRole('guru'))->toBeFalse()
        ->and($user->hasAnyRole(['guru', 'sekretaris_kelas']))->toBeTrue()
        ->and($user->canRecordAttendanceManual())->toBeTrue()
        ->and($user->canManageTahfidz())->toBeTrue();
});

test('sekretaris_kelas can access manual attendance scoped strictly to own class', function () {
    $classA = SchoolClass::firstOrCreate(['name' => 'X-A'], ['grade' => '10', 'major' => 'Umum']);
    $classB = SchoolClass::firstOrCreate(['name' => 'X-B'], ['grade' => '10', 'major' => 'Umum']);

    $userSec = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa', 'sekretaris_kelas'],
    ]);

    $studentSec = Student::create([
        'user_id' => $userSec->id,
        'class_id' => $classA->id,
        'name' => 'Sekretaris A',
        'gender' => 'L',
        'nisn' => '8880001111',
        'nis' => '8001',
        'status' => 'aktif',
    ]);

    $studentInClassA = Student::create([
        'class_id' => $classA->id,
        'name' => 'Teman Sekelas A',
        'gender' => 'L',
        'nisn' => '8880002222',
        'nis' => '8002',
        'status' => 'aktif',
    ]);

    $studentInClassB = Student::create([
        'class_id' => $classB->id,
        'name' => 'Siswa Kelas B',
        'gender' => 'L',
        'nisn' => '8880003333',
        'nis' => '8003',
        'status' => 'aktif',
    ]);

    $response = $this->actingAs($userSec)->get(route('attendance.manual'));
    $response->assertStatus(200);

    // Can record for student in own class A
    $postResponseA = $this->actingAs($userSec)->post(route('attendance.manual.store'), [
        'student_id' => $studentInClassA->id,
        'date' => Carbon::now('Asia/Jakarta')->toDateString(),
        'status' => 'HADIR',
        'notes' => 'Diabsenkan oleh sekretaris kelas',
    ]);
    $postResponseA->assertRedirect();
    expect(Attendance::where('student_id', $studentInClassA->id)->exists())->toBeTrue();

    // Cannot record for student in class B (redirects with error)
    $postResponseB = $this->actingAs($userSec)->post(route('attendance.manual.store'), [
        'student_id' => $studentInClassB->id,
        'date' => Carbon::now('Asia/Jakarta')->toDateString(),
        'status' => 'HADIR',
        'notes' => 'Coba absen kelas lain',
    ]);
    $postResponseB->assertRedirect();
    $postResponseB->assertSessionHas('error');
    expect(Attendance::where('student_id', $studentInClassB->id)->exists())->toBeFalse();
});

test('koordinator_keagamaan can create, edit, update and delete tahfidz deposit records', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'XII-MIPA-1'], ['grade' => '12', 'major' => 'MIPA']);
    $student = Student::create([
        'class_id' => $class->id,
        'name' => 'Santri Tahfidz',
        'gender' => 'L',
        'nisn' => '8880004444',
        'nis' => '8004',
        'status' => 'aktif',
    ]);

    $koord = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa', 'koordinator_keagamaan'],
    ]);

    // Store new deposit
    $storeResponse = $this->actingAs($koord)->post(route('tahfidz.store'), [
        'student_id' => $student->id,
        'surah_number' => 78,
        'ayat_range' => '1-20',
        'makhraj' => 'Sangat Baik',
        'tajwid' => 'Baik',
        'kelancaran' => 'Sangat Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => 'Hafalan lancar dan tajwid tepat.',
        'deposit_date' => Carbon::now('Asia/Jakarta')->toDateString(),
        'deposit_time' => '07:15',
        'examiner_name' => 'Ustadz Koordinator',
    ]);
    $storeResponse->assertRedirect();

    $record = QuranTahfidzRecord::where('student_id', $student->id)->latest()->first();
    expect($record)->not->toBeNull()
        ->and($record->surah_name)->toBe("An-Naba'")
        ->and($record->overall_grade)->toBe('Lulus');

    // Edit view
    $editResponse = $this->actingAs($koord)->get(route('tahfidz.edit', $record->id));
    $editResponse->assertStatus(200);

    // Update
    $updateResponse = $this->actingAs($koord)->put(route('tahfidz.update', $record->id), [
        'surah_number' => 78,
        'ayat_range' => '1-40',
        'makhraj' => 'Sangat Baik',
        'tajwid' => 'Sangat Baik',
        'kelancaran' => 'Sangat Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => 'Selesai 1 surat penuh An-Naba.',
        'deposit_date' => Carbon::now('Asia/Jakarta')->toDateString(),
        'deposit_time' => '07:20',
        'examiner_name' => 'Ustadz Koordinator',
    ]);
    $updateResponse->assertRedirect();

    $record->refresh();
    expect($record->ayat_range)->toBe('1-40');

    // Delete
    $deleteResponse = $this->actingAs($koord)->delete(route('tahfidz.destroy', $record->id));
    $deleteResponse->assertRedirect();
    expect(QuranTahfidzRecord::find($record->id))->toBeNull();
});

test('digital raport synthesizes 6 pillars and supports a4 and f4 print outputs', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'XI-IPA-1'], ['grade' => '11', 'major' => 'IPA']);
    $student = Student::create([
        'class_id' => $class->id,
        'name' => 'Siswa Raport Digital',
        'gender' => 'L',
        'nisn' => '8880005555',
        'nis' => '8005',
        'status' => 'aktif',
    ]);

    $admin = User::factory()->create(['role' => 'admin']);

    // Raport index
    $indexResponse = $this->actingAs($admin)->get(route('raport.index', ['student_id' => $student->id]));
    $indexResponse->assertStatus(200);

    // Raport detail show
    $showResponse = $this->actingAs($admin)->get(route('raport.show', $student->id));
    $showResponse->assertStatus(200)
        ->assertSee('Ibadah Bersama')
        ->assertSee('Tahfidz Juz 30');

    // Raport Print A4
    $printA4 = $this->actingAs($admin)->get(route('raport.print', ['student' => $student->id, 'paper' => 'a4']));
    $printA4->assertStatus(200)
        ->assertSee('210mm', false);

    // Raport Print F4
    $printF4 = $this->actingAs($admin)->get(route('raport.print', ['student' => $student->id, 'paper' => 'f4']));
    $printF4->assertStatus(200)
        ->assertSee('215mm 330mm', false)
        ->assertSee('215mm', false);
});

test('kiosk display responds with 6 widgets and json polling endpoint', function () {
    // Kiosk view (Public / Lobby Display)
    $response = $this->get(route('kiosk.index'));
    $response->assertStatus(200)
        ->assertSee('Presensi Kehadiran Harian', false)
        ->assertSee('Shalat Dhuha Bersama', false)
        ->assertSee('Shalat Dzuhur', false)
        ->assertSee('Presensi Keputrian', false)
        ->assertSee('Setor Hafalan Juz 30', false)
        ->assertSee('Ranking Poin Prestasi', false);

    // Kiosk JSON Data endpoint
    $jsonResponse = $this->get(route('kiosk.data'));
    $jsonResponse->assertStatus(200)
        ->assertJsonStructure([
            'timestamp' => ['date', 'time', 'day_name'],
            'daily' => ['total_students', 'recorded', 'hadir', 'terlambat', 'izin', 'sakit', 'alpa', 'percentage', 'recent'],
            'dhuha' => ['is_open', 'message', 'schedule', 'total_today', 'recent'],
            'dzuhur' => ['is_open', 'message', 'schedule', 'total_today', 'recent'],
            'keputrian' => ['is_open', 'message', 'schedule', 'total_today', 'recent'],
            'tahfidz' => ['total_today', 'passed_today', 'recent'],
            'ranking',
            'ticker',
        ]);
});

test('reports index and print views support a4 and f4 papers', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Reports index
    $res = $this->actingAs($admin)->get(route('reports.index'));
    $res->assertStatus(200)
        ->assertSee('Generator Cetak Kustom')
        ->assertSee('A4')
        ->assertSee('F4');

    // Combined Print A4 & F4
    $printCombA4 = $this->actingAs($admin)->get(route('reports.print.combined', ['paper' => 'a4']));
    $printCombA4->assertStatus(200)->assertSee('A4 landscape', false);

    $printCombF4 = $this->actingAs($admin)->get(route('reports.print.combined', ['paper' => 'f4']));
    $printCombF4->assertStatus(200)->assertSee('330mm 215mm landscape', false);

    // Tahfidz Report Print A4 & F4
    $printTahfidzA4 = $this->actingAs($admin)->get(route('tahfidz.print', ['paper' => 'a4']));
    $printTahfidzA4->assertStatus(200)->assertSee('210mm', false);

    $printTahfidzF4 = $this->actingAs($admin)->get(route('tahfidz.print', ['paper' => 'f4']));
    $printTahfidzF4->assertStatus(200)->assertSee('215mm 330mm', false);
});

test('approved leave is preserved and protected during bulk manual attendance', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-LEAVE'], ['grade' => '10', 'major' => 'Umum']);
    $student1 = Student::create([
        'class_id' => $class->id,
        'name' => 'Siswa Izin Sah',
        'gender' => 'L',
        'nisn' => '9990001111',
        'nis' => '9001',
        'is_active' => true,
    ]);

    $student2 = Student::create([
        'class_id' => $class->id,
        'name' => 'Siswa Hadir Biasa',
        'gender' => 'L',
        'nisn' => '9990002222',
        'nis' => '9002',
        'is_active' => true,
    ]);

    $today = Carbon::now('Asia/Jakarta')->toDateString();

    // Student 1 has officially approved leave
    Attendance::create([
        'student_id' => $student1->id,
        'class_id' => $class->id,
        'nis' => $student1->nis,
        'nisn' => $student1->nisn,
        'student_name' => $student1->name,
        'date' => $today,
        'time' => '07:00:00',
        'method' => 'IZIN_SAKIT',
        'status' => 'IZIN',
        'verification_status' => 'DIVERIFIKASI',
        'notes' => 'Surat izin dokter sah.',
    ]);

    $admin = User::factory()->create(['role' => 'admin']);

    // Teacher or secretary submits bulk attendance where blanket HADIR is submitted without custom note for student 1
    $postResponse = $this->actingAs($admin)->post(route('attendance.manual.bulk'), [
        'class_id' => $class->id,
        'date' => $today,
        'statuses' => [
            $student1->id => 'HADIR',
            $student2->id => 'HADIR',
        ],
        'notes' => [
            $student1->id => '',
            $student2->id => '',
        ],
    ]);
    $postResponse->assertRedirect();

    // Verify student 1's approved leave was protected and NOT overwritten
    $att1 = Attendance::where('student_id', $student1->id)->whereDate('date', $today)->first();
    expect($att1->status)->toBe('IZIN')
        ->and($att1->verification_status)->toBe('DIVERIFIKASI')
        ->and($att1->method)->toBe('IZIN_SAKIT');

    // Verify student 2 was marked HADIR
    $att2 = Attendance::where('student_id', $student2->id)->whereDate('date', $today)->first();
    expect($att2->status)->toBe('HADIR');
});

test('dashboards display worship and tahfidz summaries and parent portal renders worship progress', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'XII-TEST-DASH'], ['grade' => '12', 'major' => 'IPA']);
    $student = Student::create([
        'class_id' => $class->id,
        'name' => 'Ananda Berbakat',
        'gender' => 'L',
        'nisn' => '9990003333',
        'nis' => '9003',
        'is_active' => true,
    ]);

    $admin = User::factory()->create(['role' => 'admin']);

    // Admin dashboard contains worship & tahfidz summary
    $adminResponse = $this->actingAs($admin)->get(route('dashboard'));
    $adminResponse->assertStatus(200)
        ->assertSee('Ringkasan Presensi Ibadah & Progres Tahfidz Al-Qur\'an', false);

    // Parent Portal contains worship & tahfidz tab and summary
    $portalResponse = $this->get(route('parent.portal', ['nisn' => $student->nisn]));
    $portalResponse->assertStatus(200)
        ->assertSee('Ringkasan Presensi Ibadah & Progres Tahfidz Al-Qur\'an', false)
        ->assertSee('Ibadah & Tahfidz', false)
        ->assertSee('Shalat Dhuha Bersama', false)
        ->assertSee('Dzuhur / Jum\'at Berjamaah', false);
});

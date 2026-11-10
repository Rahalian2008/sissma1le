<?php

use App\Models\Attendance;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->parentUser = User::where('role', 'orang_tua')->first();
    if (! $this->parentUser) {
        $this->parentUser = User::factory()->create([
            'role' => 'orang_tua',
            'name' => 'Orang Tua Uji Coba',
            'email' => 'orangtua_test@example.com',
        ]);
    }

    // Pastikan parentProfile untuk user ini ada dan terhubung dengan student
    $parentProfile = ParentGuardian::where('user_id', $this->parentUser->id)->first();
    if (! $parentProfile) {
        $existingParent = ParentGuardian::first();
        if ($existingParent) {
            $existingParent->update(['user_id' => $this->parentUser->id]);
            $parentProfile = $existingParent;
        } else {
            $student = Student::where('is_active', true)->first() ?? Student::factory()->create(['is_active' => true]);
            $parentProfile = ParentGuardian::create([
                'user_id' => $this->parentUser->id,
                'student_id' => $student->id,
                'name' => 'Wali Siswa Test',
                'relation_type' => 'AYAH',
            ]);
        }
    }

    $this->student = $parentProfile->student ?? Student::first();
    expect($this->student)->not->toBeNull();
});

test('parent dashboard renders default monthly attendance recap correctly', function () {
    $response = $this->actingAs($this->parentUser)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Rekapitulasi Presensi Kehadiran Anak', false);
    $response->assertSee('Pilihan Periode', false);
    $response->assertSee('Bulanan', false);
    $response->assertSee('% Kehadiran', false);
    $response->assertSee('Hadir (H)', false);
    $response->assertSee('Terlambat (T)', false);
    $response->assertSee('Izin (I)', false);
    $response->assertSee('Sakit (S)', false);
    $response->assertSee('Alpa (A)', false);
    $response->assertSee('Catatan & Riwayat Kehadiran', false);
});

test('parent dashboard filters attendance recap by daily period', function () {
    $testDate = '2026-10-02';

    // Insert attendance for student on test date
    Attendance::updateOrCreate(
        [
            'student_id' => $this->student->id,
            'date' => $testDate,
        ],
        [
            'nis' => $this->student->nis,
            'nisn' => $this->student->nisn,
            'student_name' => $this->student->name,
            'class_id' => $this->student->class_id,
            'status' => 'HADIR',
            'time' => '06:45:00',
            'method' => 'RFID',
            'notes' => 'Hadir tepat waktu pagi',
        ]
    );

    $response = $this->actingAs($this->parentUser)->get("/dashboard?att_period=daily&att_date={$testDate}");

    $response->assertStatus(200);
    $response->assertSee('Harian:', false);
    $response->assertSee('Hadir tepat waktu pagi', false);
    $response->assertSee('RFID', false);
});

test('parent dashboard filters attendance recap by weekly period', function () {
    $refDate = '2026-10-02';
    $monDate = Carbon::parse($refDate)->startOfWeek(Carbon::MONDAY)->toDateString();

    Attendance::updateOrCreate(
        [
            'student_id' => $this->student->id,
            'date' => $monDate,
        ],
        [
            'nis' => $this->student->nis,
            'nisn' => $this->student->nisn,
            'student_name' => $this->student->name,
            'class_id' => $this->student->class_id,
            'status' => 'TERLAMBAT',
            'time' => '07:15:00',
            'method' => 'QR_SCAN',
            'notes' => 'Terlambat 15 menit',
        ]
    );

    $response = $this->actingAs($this->parentUser)->get("/dashboard?att_period=weekly&att_week_date={$refDate}");

    $response->assertStatus(200);
    $response->assertSee('Mingguan:', false);
    $response->assertSee('Terlambat 15 menit', false);
});

test('parent dashboard filters attendance recap by custom costume date range', function () {
    $startDate = '2026-08-15';
    $endDate = '2026-08-20';
    $customSampleDate = '2026-08-17';

    Attendance::updateOrCreate(
        [
            'student_id' => $this->student->id,
            'date' => $customSampleDate,
        ],
        [
            'nis' => $this->student->nis,
            'nisn' => $this->student->nisn,
            'student_name' => $this->student->name,
            'class_id' => $this->student->class_id,
            'status' => 'IZIN',
            'time' => '07:00:00',
            'method' => 'IZIN_SAKIT',
            'notes' => 'Izin upacara kemerdekaan di kelurahan',
        ]
    );

    $response = $this->actingAs($this->parentUser)->get("/dashboard?att_period=custom&att_start_date={$startDate}&att_end_date={$endDate}");

    $response->assertStatus(200);
    $response->assertSee('Kustom:', false);
    $response->assertSee('Costume / Kustom', false);
    $response->assertSee('Izin upacara kemerdekaan di kelurahan', false);
});

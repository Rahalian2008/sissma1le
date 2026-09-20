<?php

use App\Models\Achievement;
use App\Models\AchievementItem;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationItem;

test('achievement and violation points are strictly separated', function () {
    $student = Student::first();
    $achItem = AchievementItem::first();
    $violItem = ViolationItem::first();

    // Reset any existing test records for this student
    $student->achievements()->delete();
    $student->violations()->delete();

    // Add 80 achievement points
    Achievement::create([
        'student_id' => $student->id,
        'category_id' => $achItem->category_id,
        'item_id' => $achItem->id,
        'title' => 'Juara 1 OSN Matematika Tingkat Kabupaten',
        'level' => 'KABUPATEN',
        'points' => 80,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);

    // Add 35 violation points
    Violation::create([
        'student_id' => $student->id,
        'category_id' => $violItem->category_id,
        'item_id' => $violItem->id,
        'title' => 'Terlambat masuk sekolah berulang',
        'points' => 35,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);

    // Refresh model
    $student->refresh();

    // Verify STRICT separation
    expect($student->totalAchievementPoints())->toBe(80);
    expect($student->totalViolationPoints())->toBe(35);

    // Ensure there is NO net calculation or deduction
    expect($student->totalAchievementPoints() - $student->totalViolationPoints())->not->toBe($student->totalAchievementPoints());
});

test('reward tiers activate accurately at 125, 176, and 200 points', function () {
    $student = Student::first();
    $item = AchievementItem::first();

    // Scenario 1: Below 125 points
    $student->achievements()->delete();
    Achievement::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Prestasi 100 Poin',
        'level' => 'SEKOLAH',
        'points' => 100,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->reward_status['is_eligible'])->toBeFalse();
    expect($student->reward_status['level'])->toContain('Menuju Ambang Penghargaan');

    // Scenario 2: 125 - 175 points (Siswa Berprestasi)
    $student->achievements()->delete();
    Achievement::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Prestasi 125 Poin',
        'level' => 'KABUPATEN',
        'points' => 125,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->reward_status['is_eligible'])->toBeTrue();
    expect($student->reward_status['level'])->toBe('Siswa Berprestasi');

    // Scenario 3: 176 - 199 points (Siswa Berprestasi Utama)
    $student->achievements()->delete();
    Achievement::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Prestasi 180 Poin',
        'level' => 'PROVINSI',
        'points' => 180,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->reward_status['is_eligible'])->toBeTrue();
    expect($student->reward_status['level'])->toBe('Siswa Berprestasi Utama');

    // Scenario 4: >= 200 points (Anugerah Waluya Utama)
    $student->achievements()->delete();
    Achievement::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Prestasi 210 Poin',
        'level' => 'NASIONAL',
        'points' => 210,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->reward_status['is_eligible'])->toBeTrue();
    expect($student->reward_status['level'])->toBe('Anugerah Waluya Utama');
});

test('guidance thresholds trigger action needed at 75, 125, 176, and 200 points', function () {
    $student = Student::first();
    $item = ViolationItem::first();

    // Scenario 1: 75 points (Bimbingan Wali Kelas)
    $student->violations()->delete();
    Violation::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Pelanggaran 75 Poin',
        'points' => 75,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->guidance_status['action_needed'])->toBeTrue();
    expect($student->guidance_status['stage'])->toBe('BIMBINGAN_WALI_KELAS');

    // Scenario 2: 125 points (SP 1 - BK)
    $student->violations()->delete();
    Violation::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Pelanggaran 125 Poin',
        'points' => 125,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->guidance_status['action_needed'])->toBeTrue();
    expect($student->guidance_status['stage'])->toBe('SP1_BK');

    // Scenario 3: 176 points (SP 2 - BK)
    $student->violations()->delete();
    Violation::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Pelanggaran 176 Poin',
        'points' => 176,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->guidance_status['action_needed'])->toBeTrue();
    expect($student->guidance_status['stage'])->toBe('SP2_BK');

    // Scenario 4: 200 points (SP 3 - BK & Kesiswaan)
    $student->violations()->delete();
    Violation::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Pelanggaran 200 Poin',
        'points' => 200,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->guidance_status['action_needed'])->toBeTrue();
    expect($student->guidance_status['stage'])->toBe('SP3_BK_KESISWAAN');

    // Scenario 5: > 200 points (Rapat Khusus Sekolah / Sidang Pleno)
    $student->violations()->delete();
    Violation::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Pelanggaran 220 Poin',
        'points' => 220,
        'date' => today(),
        'status' => 'DIVERIFIKASI',
    ]);
    expect($student->guidance_status['action_needed'])->toBeTrue();
    expect($student->guidance_status['stage'])->toBe('RAPAT_KHUSUS');
});

test('teacher can record achievement and admin/counselor can verify it', function () {
    $teacherUser = User::where('role', 'guru')->first();
    $adminUser = User::where('role', 'admin')->first();
    $student = Student::first();
    $item = AchievementItem::first();

    $response = $this->actingAs($teacherUser)->post('/achievements', [
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'item_id' => $item->id,
        'title' => 'Lomba Karya Tulis Ilmiah Remaja Tingkat Provinsi',
        'level' => 'PROVINSI',
        'points' => 75,
        'date' => today()->toDateString(),
        'description' => 'Meraih peringkat ke-2 kategori IPA Terapan',
    ]);

    $response->assertRedirect('/achievements');

    $achievement = Achievement::where('student_id', $student->id)
        ->where('title', 'Lomba Karya Tulis Ilmiah Remaja Tingkat Provinsi')
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->status)->toBe('MENUNGGU_VERIFIKASI');

    // Verify as Admin
    $verifyResponse = $this->actingAs($adminUser)->post("/achievements/{$achievement->id}/verify", [
        'status' => 'DIVERIFIKASI',
        'verification_notes' => 'Sertifikat dan piagam asli telah diperiksa dan sah.',
    ]);

    $verifyResponse->assertRedirect();
    $achievement->refresh();
    expect($achievement->status)->toBe('DIVERIFIKASI');
});

test('siswa and orang tua are forbidden from creating achievements or violations', function () {
    $siswaUser = User::where('role', 'siswa')->first();
    $orangTuaUser = User::where('role', 'orang_tua')->first();

    // Siswa attempts create and store
    $this->actingAs($siswaUser)->get('/achievements/create')->assertForbidden();
    $this->actingAs($siswaUser)->post('/achievements', [])->assertForbidden();
    $this->actingAs($siswaUser)->get('/violations/create')->assertForbidden();
    $this->actingAs($siswaUser)->post('/violations', [])->assertForbidden();

    // Orang Tua attempts create and store
    $this->actingAs($orangTuaUser)->get('/achievements/create')->assertForbidden();
    $this->actingAs($orangTuaUser)->post('/achievements', [])->assertForbidden();
    $this->actingAs($orangTuaUser)->get('/violations/create')->assertForbidden();
    $this->actingAs($orangTuaUser)->post('/violations', [])->assertForbidden();
});

<?php

use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\HabitNote;
use App\Models\HabitSummary;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

test('student can log and toggle daily 7 habit items', function () {
    $user = User::where('role', 'siswa')->first();
    $student = Student::where('user_id', $user->id)->first();
    $habit = Habit::first();

    // 1. Mark as completed
    $response = $this->actingAs($user)->postJson('/habits/log', [
        'habit_id' => $habit->id,
        'date' => today()->toDateString(),
        'is_completed' => true,
        'activity_name' => 'Bangun pukul 04:30 dan salat subuh berjamaah',
        'notes' => 'Merasa berenergi dan siap belajar',
    ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $log = HabitDailyLog::where('student_id', $student->id)
        ->where('habit_id', $habit->id)
        ->whereDate('date', today())
        ->first();

    expect($log)->not->toBeNull();
    expect($log->is_completed)->toBeTrue();

    // 2. Toggle to uncompleted
    $toggleResponse = $this->actingAs($user)->postJson('/habits/log', [
        'habit_id' => $habit->id,
        'date' => today()->toDateString(),
        'is_completed' => false,
    ]);

    $toggleResponse->assertStatus(200);
    $log->refresh();
    expect($log->is_completed)->toBeFalse();
});

test('completing habits automatically updates weekly summary score and status', function () {
    $user = User::where('role', 'siswa')->first();
    $student = Student::where('user_id', $user->id)->first();
    $habits = Habit::take(5)->get();
    foreach ($habits as $habit) {
        $habit->update(['is_time_restricted' => false]);
        $res = $this->actingAs($user)->postJson('/habits/log', [
            'habit_id' => $habit->id,
            'date' => Carbon::today('Asia/Jakarta')->toDateString(),
            'is_completed' => true,
        ]);
        $res->assertJson(['success' => true]);
    }

    $summary = HabitSummary::where('student_id', $student->id)
        ->where('period_type', 'WEEKLY')
        ->latest('id')
        ->first();

    expect($summary)->not->toBeNull();
    expect($summary->completed_count)->toBeGreaterThanOrEqual(5);
    expect($summary->score_percentage)->toBeGreaterThan(0);
});

test('homeroom teacher can provide encouraging guidance note on student habit journal', function () {
    $homeroomUser = User::where('role', 'wali_kelas')->first();
    $student = Student::first();

    $response = $this->actingAs($homeroomUser)->post('/habits/note', [
        'student_id' => $student->id,
        'date' => today()->toDateString(),
        'comment' => 'Pertahankan kedisiplinan bangun pagi dan gemar membacanya, sangat membanggakan!',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $note = HabitNote::where('student_id', $student->id)
        ->where('author_id', $homeroomUser->id)
        ->whereDate('date', today())
        ->first();

    expect($note)->not->toBeNull();
    expect($note->author_role)->toBe('wali_kelas');
    expect($note->comment)->toContain('Pertahankan kedisiplinan');
});

test('student cannot complete time-restricted habit outside allowed time window', function () {
    $user = User::where('role', 'siswa')->first();
    $habit = Habit::first();

    $habit->update([
        'start_time' => '04:30',
        'end_time' => '05:30',
        'is_time_restricted' => true,
    ]);

    // Freeze time to 14:00 (outside allowed window)
    Carbon::setTestNow(Carbon::parse('2026-09-19 14:00:00', 'Asia/Jakarta'));

    $response = $this->actingAs($user)->postJson('/habits/log', [
        'habit_id' => $habit->id,
        'date' => '2026-09-19',
        'is_completed' => true,
        'activity_name' => 'Bangun Pagi',
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('success', false);
    $response->assertJsonPath('message', "Pembiasaan '{$habit->name}' hanya dapat diceklis pada rentang waktu 04:30 - 05:30 WIB. Saat ini: Waktu pelaksanaan telah lewat (04:30 - 05:30 WIB).");

    Carbon::setTestNow(); // reset
});

test('student can complete time-restricted habit inside allowed time window with reflection', function () {
    $user = User::where('role', 'siswa')->first();
    $student = Student::where('user_id', $user->id)->first();
    $habit = Habit::first();

    $habit->update([
        'start_time' => '04:30',
        'end_time' => '05:30',
        'is_time_restricted' => true,
    ]);

    // Freeze time to 05:00 (inside allowed window)
    Carbon::setTestNow(Carbon::parse('2026-09-19 05:00:00', 'Asia/Jakarta'));

    $response = $this->actingAs($user)->postJson('/habits/log', [
        'habit_id' => $habit->id,
        'date' => '2026-09-19',
        'is_completed' => true,
        'activity_name' => 'Bangun pagi dan langsung merapikan kasur',
        'reflection' => 'Badan terasa lebih segar dan siap sholat subuh.',
    ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $log = HabitDailyLog::where('student_id', $student->id)
        ->where('habit_id', $habit->id)
        ->whereDate('date', '2026-09-19')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->is_completed)->toBeTrue();
    expect($log->activity_name)->toBe('Bangun pagi dan langsung merapikan kasur');
    expect($log->reflection)->toBe('Badan terasa lebih segar dan siap sholat subuh.');

    Carbon::setTestNow(); // reset
});

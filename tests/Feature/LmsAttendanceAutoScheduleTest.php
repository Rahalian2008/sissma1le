<?php

use App\Models\CourseClass;
use App\Models\LmsSubjectAttendance;
use App\Models\Teacher;
use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'SchoolMasterSeeder']);
    $this->artisan('db:seed', ['--class' => 'LmsSeeder']);
});

test('attendance schedule can be automatic and pulls from course class learning schedule', function () {
    $courseClass = CourseClass::first();
    $teacher = User::find($courseClass->teacher_id);
    if (! $teacher) {
        $teacher = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
        $courseClass->update(['teacher_id' => $teacher->id]);
    }

    // Set course class learning schedule: Selasa & Kamis
    $courseClass->update([
        'day_of_week' => 'Selasa, Kamis',
        'start_time' => '08:00',
        'end_time' => '09:30',
        'schedules' => [
            ['day' => 'Selasa', 'start_time' => '08:00', 'end_time' => '09:30'],
            ['day' => 'Kamis', 'start_time' => '10:00', 'end_time' => '11:30'],
        ],
    ]);

    // 1. Create attendance with schedule_type = auto
    $response = $this->actingAs($teacher)->post(route('lms.attendances.store', $courseClass->id), [
        'title' => 'Presensi Otomatis Mapel',
        'attendance_method' => 'both',
        'schedule_type' => 'auto',
    ]);

    $response->assertSessionHas('success');

    $attendance = LmsSubjectAttendance::where('course_class_id', $courseClass->id)
        ->where('title', 'Presensi Otomatis Mapel')
        ->first();

    expect($attendance)->not->toBeNull();
    expect($attendance->schedule_type)->toBe('auto');
    expect($attendance->isAutoSchedule())->toBeTrue();
    expect($attendance->isManualSchedule())->toBeFalse();

    // The schedules list should match the course class learning schedule
    $schedules = $attendance->getRecurringSchedulesList();
    expect($schedules)->toHaveCount(2);
    expect($schedules[0]['day'])->toBe('Selasa');
    expect($schedules[0]['start_time'])->toBe('08:00');
    expect($schedules[1]['day'])->toBe('Kamis');
    expect($schedules[1]['start_time'])->toBe('10:00');
});

test('attendance schedule can be manual with custom days and times', function () {
    $courseClass = CourseClass::first();
    $teacher = User::find($courseClass->teacher_id);
    if (! $teacher) {
        $teacher = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
        $courseClass->update(['teacher_id' => $teacher->id]);
    }

    // 2. Create attendance with schedule_type = manual
    $response = $this->actingAs($teacher)->post(route('lms.attendances.store', $courseClass->id), [
        'title' => 'Presensi Manual Mapel Khusus',
        'attendance_method' => 'manual',
        'schedule_type' => 'manual',
        'attendance_days' => ['Jumat', 'Sabtu'],
        'attendance_starts' => ['13:00', '14:00'],
        'attendance_ends' => ['14:30', '15:30'],
    ]);

    $response->assertSessionHas('success');

    $attendance = LmsSubjectAttendance::where('course_class_id', $courseClass->id)
        ->where('title', 'Presensi Manual Mapel Khusus')
        ->first();

    expect($attendance)->not->toBeNull();
    expect($attendance->schedule_type)->toBe('manual');
    expect($attendance->isManualSchedule())->toBeTrue();
    expect($attendance->isAutoSchedule())->toBeFalse();

    $schedules = $attendance->getRecurringSchedulesList();
    expect($schedules)->toHaveCount(2);
    expect($schedules[0]['day'])->toBe('Jumat');
    expect($schedules[0]['start_time'])->toBe('13:00');
    expect($schedules[1]['day'])->toBe('Sabtu');
    expect($schedules[1]['start_time'])->toBe('14:00');
});

test('updating course class learning schedule automatically syncs automatic attendances', function () {
    $courseClass = CourseClass::first();
    $teacherUser = User::find($courseClass->teacher_id);
    if (! $teacherUser) {
        $teacherUser = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
        $courseClass->update(['teacher_id' => $teacherUser->id]);
    }
    $teacherModel = Teacher::first();

    // Create an automatic attendance first
    $attendance = LmsSubjectAttendance::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Presensi Rutin Awal',
        'meeting_date' => now()->toDateString(),
        'start_time' => '07:30',
        'end_time' => '09:00',
        'attendance_method' => 'both',
        'schedule_type' => 'auto',
        'is_open' => true,
        'is_recurring' => true,
        'recurring_days' => ['Senin'],
        'recurring_schedules' => [
            ['day' => 'Senin', 'start_time' => '07:30', 'end_time' => '09:00'],
        ],
        'created_by' => $teacherModel->id,
    ]);

    // Admin / Guru updates the course class learning schedule in Tugaskan Diri/Guru
    $admin = User::first() ?? User::factory()->create();
    $admin->update(['roles' => ['admin']]);

    $response = $this->actingAs($admin)->post(route('lms.master.course_classes.store'), [
        'subject_id' => $courseClass->subject_id,
        'class_id' => $courseClass->class_id,
        'academic_year_id' => $courseClass->academic_year_id,
        'teacher_id' => $courseClass->teacher_id,
        'schedule_days' => ['Rabu', 'Jumat'],
        'schedule_starts' => ['08:30', '09:15'],
        'schedule_ends' => ['10:00', '10:45'],
    ]);

    $response->assertSessionHas('success');

    // The automatic attendance should be synced to Rabu & Jumat
    $attendance->refresh();
    expect($attendance->recurring_days)->toContain('Rabu');
    expect($attendance->recurring_days)->toContain('Jumat');

    $schedules = $attendance->getRecurringSchedulesList();
    expect($schedules[0]['day'])->toBe('Rabu');
    expect($schedules[0]['start_time'])->toBe('08:30');
    expect($schedules[1]['day'])->toBe('Jumat');
    expect($schedules[1]['start_time'])->toBe('09:15');
});

test('course page tab=presensi renders schedule mode selection and class schedule banner', function () {
    $courseClass = CourseClass::first();
    $teacher = User::find($courseClass->teacher_id);
    if (! $teacher) {
        $teacher = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
        $courseClass->update(['teacher_id' => $teacher->id]);
    }

    $response = $this->actingAs($teacher)->get(route('lms.course', [
        'courseClass' => $courseClass->id,
        'tab' => 'presensi',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Presensi Pertemuan Mata Pelajaran');
    $response->assertSee('Jadwal Pembelajaran Kelas Belajar');
    $response->assertSee('Presensi Otomatis (Jadwal Kelas)');
    $response->assertSee('Tugaskan Diri / Tugaskan Guru Pengampu ke Kelas');
    $response->assertSee('Pilihan Pengaturan Jadwal Presensi');
    $response->assertSee('Otomatis');
    $response->assertSee('Manual');
    $response->assertSee('name="schedule_type"', false);
});

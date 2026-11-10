<?php

use App\Http\Controllers\LmsController;
use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\LmsSubjectAttendance;
use App\Models\LmsSubjectAttendanceLog;
use App\Models\Student;
use App\Services\LmsProgressionService;
use Carbon\Carbon;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'SchoolMasterSeeder']);
    $this->artisan('db:seed', ['--class' => 'LmsSeeder']);
});

test('course class supports multi-day schedules in a week', function () {
    $courseClass = CourseClass::first();
    expect($courseClass)->not->toBeNull();

    // Assign multi-day schedule (e.g. 2 days a week: Senin 07:30 - 09:00 & Selasa 09:45 - 10:20)
    $courseClass->update([
        'schedules' => [
            [
                'day' => 'Senin',
                'start_time' => '07:30',
                'end_time' => '09:00',
            ],
            [
                'day' => 'Selasa',
                'start_time' => '09:45',
                'end_time' => '10:20',
            ],
        ],
    ]);

    $courseClass->refresh();
    $list = $courseClass->getSchedulesList();
    expect($list)->toHaveCount(2)
        ->and($list[0]['day'])->toBe('Senin')
        ->and($list[0]['start_time'])->toBe('07:30')
        ->and($list[1]['day'])->toBe('Selasa')
        ->and($list[1]['end_time'])->toBe('10:20');

    $text = $courseClass->schedule_text;
    expect($text)->toContain('Senin')
        ->and($text)->toContain('Selasa')
        ->and($text)->toContain('07:30 - 09:00 WIB')
        ->and($text)->toContain('09:45 - 10:20 WIB');
});

test('course class active check matches current day and time', function () {
    $courseClass = CourseClass::first();

    // Configure schedule for a known day/time
    $courseClass->update([
        'schedules' => [
            [
                'day' => 'Rabu',
                'start_time' => '08:00',
                'end_time' => '10:00',
            ],
        ],
    ]);

    // Test on Wednesday at 08:30 (should be active)
    Carbon::setTestNow(Carbon::parse('2026-10-21 08:30:00', 'Asia/Jakarta')); // 2026-10-21 is Wednesday
    expect($courseClass->isCurrentlyActiveForStudent())->toBeTrue();

    // Test on Wednesday at 10:05 (time over, should NOT be active)
    Carbon::setTestNow(Carbon::parse('2026-10-21 10:05:00', 'Asia/Jakarta'));
    expect($courseClass->isCurrentlyActiveForStudent())->toBeFalse();

    // Test on Thursday at 08:30 (different day, should NOT be active)
    Carbon::setTestNow(Carbon::parse('2026-10-22 08:30:00', 'Asia/Jakarta'));
    expect($courseClass->isCurrentlyActiveForStudent())->toBeFalse();

    Carbon::setTestNow(); // Reset mock
});

test('teacher can create recurring multi-day attendance session without dates', function () {
    $courseClass = CourseClass::first();
    $teacherUser = $courseClass->teacher->user;

    // Create recurring attendance session with 2 recurring days in a week
    $response = $this->actingAs($teacherUser)->post(route('lms.attendances.store', $courseClass->id), [
        'title' => 'Presensi Rutin Mingguan Fisika',
        'notes' => 'Presensi kehadiran wajib setiap sesi mapel',
        'attendance_days' => ['Senin', 'Selasa'],
        'attendance_starts' => ['07:30', '09:45'],
        'attendance_ends' => ['09:00', '10:20'],
        'attendance_method' => 'selfie',
    ]);

    $response->assertStatus(302);

    $attendance = LmsSubjectAttendance::where('course_class_id', $courseClass->id)
        ->where('is_recurring', true)
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->is_recurring)->toBeTrue()
        ->and($attendance->recurring_days)->toContain('Senin')
        ->and($attendance->recurring_days)->toContain('Selasa')
        ->and($attendance->formatted_days)->toContain('Senin')
        ->and($attendance->formatted_days)->toContain('Selasa');
});

test('sync recurring attendances automatically generates meeting instance on matching day', function () {
    $courseClass = CourseClass::first();

    // Create recurring attendance parent
    $parent = LmsSubjectAttendance::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Presensi Sesi Mapel',
        'meeting_date' => Carbon::now('Asia/Jakarta')->toDateString(),
        'is_recurring' => true,
        'recurring_days' => ['Rabu'],
        'recurring_schedules' => [
            [
                'day' => 'Rabu',
                'start_time' => '08:00',
                'end_time' => '10:00',
            ],
        ],
        'attendance_method' => 'selfie',
        'is_open' => true,
        'created_by' => $courseClass->teacher_id,
    ]);

    // Freeze time to Wednesday 08:30
    Carbon::setTestNow(Carbon::parse('2026-10-21 08:30:00', 'Asia/Jakarta'));

    $controller = app(LmsController::class);
    $controller->syncRecurringAttendances($courseClass);

    // Verify child instance for today is created
    $child = LmsSubjectAttendance::where('parent_attendance_id', $parent->id)->first();
    expect($child)->not->toBeNull()
        ->and($child->meeting_date->toDateString())->toBe('2026-10-21')
        ->and($child->is_open)->toBeTrue();

    Carbon::setTestNow(); // Reset mock
});

test('material is locked before start_time and accessible after start_time', function () {
    $courseClass = CourseClass::first();
    $student = Student::first();
    expect($student)->not->toBeNull();
    $courseClass->update(['class_id' => $student->class_id]);

    // Clear any existing materials for this courseClass to isolate schedule time gating
    LmsMaterial::where('course_class_id', $courseClass->id)->delete();

    // Create material scheduled for tomorrow at 10:00
    $material = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Materi Hukum Gravitasi Newton Terjadwal',
        'topic' => 'Fisika Dinamika',
        'content_type' => 'text',
        'start_time' => Carbon::parse('2026-10-23 10:00:00', 'Asia/Jakarta'),
        'is_published' => true,
    ]);

    // Test before start_time (today: 2026-10-22 09:00:00)
    Carbon::setTestNow(Carbon::parse('2026-10-22 09:00:00', 'Asia/Jakarta'));
    expect($material->isAvailable())->toBeFalse();

    $access = LmsProgressionService::checkMaterialAccess($courseClass, $material, $student);
    expect($access['allowed'])->toBeFalse()
        ->and($access['message'])->toContain('10:00');

    // Attempting to visit show page should redirect back with error
    $response = $this->actingAs($student->user)->get(route('lms.materials.show', [
        'courseClass' => $courseClass->id,
        'material' => $material->id,
    ]));
    $response->assertStatus(302);
    $response->assertSessionHas('error');

    // Test after start_time has arrived (2026-10-23 10:05:00)
    Carbon::setTestNow(Carbon::parse('2026-10-23 10:05:00', 'Asia/Jakarta'));
    expect($material->isAvailable())->toBeTrue();

    $accessAfter = LmsProgressionService::checkMaterialAccess($courseClass, $material, $student);
    expect($accessAfter['allowed'])->toBeTrue();

    // Test long after start_time (e.g. next month) - material remains accessible
    Carbon::setTestNow(Carbon::parse('2026-11-15 10:00:00', 'Asia/Jakarta'));
    expect($material->isAvailable())->toBeTrue();
    $accessPast = LmsProgressionService::checkMaterialAccess($courseClass, $material, $student);
    expect($accessPast['allowed'])->toBeTrue();

    Carbon::setTestNow();
});

test('assignment is locked before start_time and accessible after start_time even after due_date', function () {
    $courseClass = CourseClass::first();
    $student = Student::first();
    expect($student)->not->toBeNull();
    $courseClass->update(['class_id' => $student->class_id]);

    // Clear any existing assignments for this courseClass to isolate schedule time gating
    LmsAssignment::where('course_class_id', $courseClass->id)->delete();

    // Create assignment scheduled from 2026-10-23 08:00 to 2026-10-25 23:59
    $assignment = LmsAssignment::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Tugas Mandiri Dinamika Gerak',
        'topic' => 'Fisika Dinamika',
        'instructions' => 'Kerjakan soal terlampir',
        'start_time' => Carbon::parse('2026-10-23 08:00:00', 'Asia/Jakarta'),
        'due_date' => Carbon::parse('2026-10-25 23:59:00', 'Asia/Jakarta'),
        'max_score' => 100,
        'is_published' => true,
    ]);

    // Before start_time
    Carbon::setTestNow(Carbon::parse('2026-10-22 14:00:00', 'Asia/Jakarta'));
    expect($assignment->isAvailable())->toBeFalse();

    $accessBefore = LmsProgressionService::checkAssignmentAccess($courseClass, $assignment, $student);
    expect($accessBefore['allowed'])->toBeFalse()
        ->and($accessBefore['message'])->toContain('08:00');

    // After start_time but before due_date (active period)
    Carbon::setTestNow(Carbon::parse('2026-10-24 10:00:00', 'Asia/Jakarta'));
    expect($assignment->isAvailable())->toBeTrue();

    $accessDuring = LmsProgressionService::checkAssignmentAccess($courseClass, $assignment, $student);
    expect($accessDuring['allowed'])->toBeTrue();

    // After due_date has expired (e.g. 2026-10-26 10:00:00)
    // Requirement: "Sekalipun waktu sudah lewat materi atau penugasan akan tetap tampil dan bisa diakses"
    Carbon::setTestNow(Carbon::parse('2026-10-26 10:00:00', 'Asia/Jakarta'));
    expect($assignment->isAvailable())->toBeTrue();

    $accessExpired = LmsProgressionService::checkAssignmentAccess($courseClass, $assignment, $student);
    expect($accessExpired['allowed'])->toBeTrue();

    $response = $this->actingAs($student->user)->get(route('lms.assignments.show', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignment->id,
    ]));
    $response->assertStatus(200);

    Carbon::setTestNow();
});

test('sekretaris kelas and teacher can record manual attendance with 4 statuses while regular student cannot', function () {
    $courseClass = CourseClass::first();
    $students = Student::take(2)->get();
    expect($students->count())->toBeGreaterThanOrEqual(2);

    $student1 = $students[0];
    $student2 = $students[1];

    $student1->update(['class_id' => $courseClass->class_id]);
    $student2->update(['class_id' => $courseClass->class_id]);

    // Assign 'sekretaris_kelas' role to student1
    $student1->user->update([
        'roles' => array_unique(array_merge($student1->user->roles ?? [], ['sekretaris_kelas'])),
    ]);
    expect($student1->user->hasRole('sekretaris_kelas'))->toBeTrue();

    // Create an open attendance session
    $attendance = LmsSubjectAttendance::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Pertemuan Tatap Muka Mapel',
        'meeting_date' => now()->toDateString(),
        'start_time' => '07:30',
        'end_time' => '09:00',
        'attendance_method' => 'both',
        'is_open' => true,
        'created_by' => $courseClass->teacher_id,
    ]);

    // 1. Regular student (student2) tries to record manual attendance -> 403 Forbidden
    $responseRegular = $this->actingAs($student2->user)->post(route('lms.attendances.manual', [
        'courseClass' => $courseClass->id,
        'attendance' => $attendance->id,
    ]), [
        'statuses' => [
            $student1->id => 'hadir',
            $student2->id => 'hadir',
        ],
    ]);
    $responseRegular->assertStatus(403);

    // 2. Sekretaris Kelas (student1) records manual attendance with 4 statuses: Hadir, Izin, Sakit, Alpa
    $responseSekretaris = $this->actingAs($student1->user)->post(route('lms.attendances.manual', [
        'courseClass' => $courseClass->id,
        'attendance' => $attendance->id,
    ]), [
        'statuses' => [
            $student1->id => 'hadir',
            $student2->id => 'sakit',
        ],
        'notes' => [
            $student1->id => 'Tepat waktu',
            $student2->id => 'Demam flu',
        ],
    ]);

    $responseSekretaris->assertStatus(302);
    $responseSekretaris->assertSessionHas('success');

    $log1 = LmsSubjectAttendanceLog::where('attendance_id', $attendance->id)
        ->where('student_id', $student1->id)
        ->first();
    expect($log1)->not->toBeNull()
        ->and($log1->status)->toBe('hadir')
        ->and($log1->verified_method)->toBe('manual');

    $log2 = LmsSubjectAttendanceLog::where('attendance_id', $attendance->id)
        ->where('student_id', $student2->id)
        ->first();
    expect($log2)->not->toBeNull()
        ->and($log2->status)->toBe('sakit')
        ->and($log2->notes)->toBe('Demam flu');

    // 3. Teacher can also record/update manual attendance with izin and alpa
    $teacherUser = $courseClass->teacher->user;
    $responseTeacher = $this->actingAs($teacherUser)->post(route('lms.attendances.manual', [
        'courseClass' => $courseClass->id,
        'attendance' => $attendance->id,
    ]), [
        'statuses' => [
            $student1->id => 'izin',
            $student2->id => 'alpa',
        ],
    ]);
    $responseTeacher->assertStatus(302);

    expect($log1->fresh()->status)->toBe('izin');
    expect($log2->fresh()->status)->toBe('alpa');
});

<?php

use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsMaterial;
use App\Models\LmsMaterialRead;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsSubjectAttendance;
use App\Models\LmsSubjectAttendanceLog;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'SchoolMasterSeeder']);
    $this->artisan('db:seed', ['--class' => 'LmsSeeder']);
});

test('gradebook calculates materials 100 on finish, assignments average, quizzes average, and attendance points', function () {
    $courseClass = CourseClass::first();
    $student = Student::first();
    expect($student)->not->toBeNull();
    $courseClass->update(['class_id' => $student->class_id]);

    // Set custom weights: 10% Materi, 30% Tugas, 40% Kuis, 20% Presensi
    $courseClass->update([
        'weight_material' => 10,
        'weight_assignment' => 30,
        'weight_quiz' => 40,
        'weight_attendance' => 20,
    ]);

    // 1. Setup 2 Materials: student reads 1, unread 1 => avg = (100 + 0) / 2 = 50
    LmsMaterial::where('course_class_id', $courseClass->id)->delete();
    $m1 = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Materi 1 Fisika',
        'content_type' => 'text',
        'is_published' => true,
    ]);
    $m2 = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Materi 2 Fisika',
        'content_type' => 'text',
        'is_published' => true,
    ]);
    LmsMaterialRead::create([
        'material_id' => $m1->id,
        'student_id' => $student->id,
        'read_at' => now(),
    ]);

    // 2. Setup 2 Assignments: scored 80 and 90 => avg = 85
    LmsAssignment::where('course_class_id', $courseClass->id)->delete();
    $a1 = LmsAssignment::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Tugas 1',
        'topic' => 'Topik 1',
        'instructions' => 'Petunjuk tugas 1',
        'due_date' => now()->addDays(7),
        'is_published' => true,
    ]);
    $a2 = LmsAssignment::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Tugas 2',
        'topic' => 'Topik 2',
        'instructions' => 'Petunjuk tugas 2',
        'due_date' => now()->addDays(7),
        'is_published' => true,
    ]);
    LmsAssignmentSubmission::create([
        'assignment_id' => $a1->id,
        'student_id' => $student->id,
        'score' => 80,
        'submitted_at' => now(),
    ]);
    LmsAssignmentSubmission::create([
        'assignment_id' => $a2->id,
        'student_id' => $student->id,
        'score' => 90,
        'submitted_at' => now(),
    ]);

    // 3. Setup 2 Quizzes: scored 70 and 80 => avg = 75
    LmsQuiz::where('course_class_id', $courseClass->id)->delete();
    $q1 = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Kuis 1',
        'is_published' => true,
    ]);
    $q2 = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Kuis 2',
        'is_published' => true,
    ]);
    LmsQuizAttempt::create([
        'quiz_id' => $q1->id,
        'student_id' => $student->id,
        'score' => 70,
        'started_at' => now()->subMinutes(30),
        'completed_at' => now(),
    ]);
    LmsQuizAttempt::create([
        'quiz_id' => $q2->id,
        'student_id' => $student->id,
        'score' => 80,
        'started_at' => now()->subMinutes(30),
        'completed_at' => now(),
    ]);

    // 4. Setup 4 Attendance Meetings:
    // Session 1: Hadir (100)
    // Session 2: Izin (50)
    // Session 3: Sakit (50)
    // Session 4: Alpa / no log (0)
    // Total points = 100 + 50 + 50 + 0 = 200, avg = 200 / 4 = 50
    LmsSubjectAttendance::where('course_class_id', $courseClass->id)->delete();
    $teacherId = $courseClass->teacher_id ?? User::first()->id;
    $att1 = LmsSubjectAttendance::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Pertemuan 1',
        'meeting_date' => now()->subDays(3)->toDateString(),
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_open' => false,
        'is_recurring' => false,
        'created_by' => $teacherId,
    ]);
    $att2 = LmsSubjectAttendance::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Pertemuan 2',
        'meeting_date' => now()->subDays(2)->toDateString(),
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_open' => false,
        'is_recurring' => false,
        'created_by' => $teacherId,
    ]);
    $att3 = LmsSubjectAttendance::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Pertemuan 3',
        'meeting_date' => now()->subDays(1)->toDateString(),
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_open' => false,
        'is_recurring' => false,
        'created_by' => $teacherId,
    ]);
    $att4 = LmsSubjectAttendance::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Pertemuan 4',
        'meeting_date' => now()->toDateString(),
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_open' => false,
        'is_recurring' => false,
        'created_by' => $teacherId,
    ]);

    LmsSubjectAttendanceLog::create([
        'attendance_id' => $att1->id,
        'student_id' => $student->id,
        'status' => 'hadir',
        'method' => 'manual',
        'recorded_at' => now(),
    ]);
    LmsSubjectAttendanceLog::create([
        'attendance_id' => $att2->id,
        'student_id' => $student->id,
        'status' => 'izin',
        'method' => 'manual',
        'recorded_at' => now(),
    ]);
    LmsSubjectAttendanceLog::create([
        'attendance_id' => $att3->id,
        'student_id' => $student->id,
        'status' => 'sakit',
        'method' => 'manual',
        'recorded_at' => now(),
    ]);
    // att4 has no log => counts as Alpa (0 points)

    // Expected final score:
    // 10% * 50 (Materi) = 5
    // 30% * 85 (Tugas) = 25.5
    // 40% * 75 (Kuis) = 30
    // 20% * 50 (Presensi) = 10
    // Total = 5 + 25.5 + 30 + 10 = 70.5
    $admin = User::first() ?? User::factory()->create();
    $admin->update(['roles' => ['admin']]);
    $response = $this->actingAs($admin)->get(route('lms.grades', $courseClass->id));
    $response->assertStatus(200);

    $viewData = $response->viewData('gradebook');
    $stData = $viewData[$student->id];

    expect($stData['material_avg'])->toBe(50.0)
        ->and($stData['assignment_avg'])->toBe(85.0)
        ->and($stData['quiz_avg'])->toBe(75.0)
        ->and($stData['attendance_avg'])->toBe(50.0)
        ->and($stData['final_score'])->toBe(70.5)
        ->and($stData['predicate'])->toBe('C');
});

test('teacher can update grade weights via manual form with validation', function () {
    $courseClass = CourseClass::first();
    $teacher = User::find($courseClass->teacher_id);
    if (! $teacher) {
        $teacher = User::factory()->create();
        $courseClass->update(['teacher_id' => $teacher->id]);
    }

    // Attempt invalid total weights (not 100%)
    $response = $this->actingAs($teacher)->post(route('lms.grades.weights', $courseClass->id), [
        'weight_material' => 20,
        'weight_assignment' => 20,
        'weight_quiz' => 20,
        'weight_attendance' => 20, // sum = 80
    ]);
    $response->assertSessionHas('error');

    // Valid total weights (sum = 100%)
    $responseValid = $this->actingAs($teacher)->post(route('lms.grades.weights', $courseClass->id), [
        'weight_material' => 15,
        'weight_assignment' => 25,
        'weight_quiz' => 35,
        'weight_attendance' => 25, // sum = 100
    ]);
    $responseValid->assertSessionHas('success');

    $courseClass->refresh();
    expect($courseClass->getWeightMaterial())->toBe(15.0)
        ->and($courseClass->getWeightAssignment())->toBe(25.0)
        ->and($courseClass->getWeightQuiz())->toBe(35.0)
        ->and($courseClass->getWeightAttendance())->toBe(25.0);
});

test('course page with tab=nilai renders successfully without blade syntax error and shows form for teacher and admin', function () {
    $courseClass = CourseClass::first();
    $admin = User::first() ?? User::factory()->create();
    $admin->update(['roles' => ['admin']]);

    // Admin can see the editable form and submit button
    $responseAdmin = $this->actingAs($admin)->get(route('lms.course', [
        'courseClass' => $courseClass->id,
        'tab' => 'nilai',
    ]));

    $responseAdmin->assertStatus(200);
    $responseAdmin->assertSee('Pemantauan Ketuntasan Siswa');
    $responseAdmin->assertSee('Pengaturan Bobot Persentase Penilaian Akhir');
    $responseAdmin->assertSee('Simpan Bobot');
    $responseAdmin->assertSee('name="weight_material"', false);

    // Guru can also see the editable form and submit button
    $guru = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
    $responseGuru = $this->actingAs($guru)->get(route('lms.course', [
        'courseClass' => $courseClass->id,
        'tab' => 'nilai',
    ]));
    $responseGuru->assertStatus(200);
    $responseGuru->assertSee('Simpan Bobot');
    $responseGuru->assertSee('name="weight_material"', false);

    // On standalone gradebook page (route lms.grades) both also see the form
    $resGradebook = $this->actingAs($guru)->get(route('lms.grades', $courseClass->id));
    $resGradebook->assertStatus(200);
    $resGradebook->assertSee('Simpan Bobot');
    $resGradebook->assertSee('name="weight_material"', false);
});

<?php

use App\Models\AcademicYear;
use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsDiscussion;
use App\Models\LmsMaterial;
use App\Models\LmsMaterialRead;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\LmsSubjectAttendance;
use App\Models\LmsZoomMeeting;
use App\Models\LmsZoomParticipant;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\LmsAiCurriculumService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'SchoolMasterSeeder']);
    $this->artisan('db:seed', ['--class' => 'LmsSeeder']);
});

test('admin, teacher, and student can access LMS dashboard', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    $teacher = User::where('role', 'guru')->first();
    $student = User::where('role', 'siswa')->first();

    // Admin access
    $resAdmin = $this->actingAs($admin)->get(route('lms.index'));
    $resAdmin->assertStatus(200);
    $resAdmin->assertSee('Learning Management System (LMS)');
    $resAdmin->assertSee('Daftar Kelas & Mata Pelajaran', false);

    // Teacher access
    $resTeacher = $this->actingAs($teacher)->get(route('lms.index'));
    $resTeacher->assertStatus(200);
    $resTeacher->assertSee('Learning Management System (LMS)');

    // Student access
    $resStudent = $this->actingAs($student)->get(route('lms.index'));
    $resStudent->assertStatus(200);
    $resStudent->assertSee('Learning Management System (LMS)');
});

test('user can view course hub with materials, assignments, quizzes, and gradebook', function () {
    $courseClass = CourseClass::first();
    expect($courseClass)->not->toBeNull();

    $teacher = User::where('role', 'guru')->first();

    $response = $this->actingAs($teacher)->get(route('lms.course', $courseClass->id));
    $response->assertStatus(200);
    $response->assertSee($courseClass->subject->name);
    $response->assertSee('Materi Pelajaran');
    $response->assertSee('Tugas & PR', false);
    $response->assertSee('Asesmen & CBT', false);
    $response->assertSee('Presensi Mapel');
    $response->assertSee('Pemantauan Nilai');
    $response->assertSee('Forum Diskusi');

    // Test each tab renders cleanly without errors
    foreach (['materi', 'tugas', 'kuis', 'presensi', 'nilai', 'diskusi', 'zoom'] as $tab) {
        $this->actingAs($teacher)->get(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => $tab]))->assertStatus(200);
    }

    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    $student = User::where('role', 'siswa')->first();

    $this->actingAs($admin)->get(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi']))->assertStatus(200);
    $this->actingAs($student)->get(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi']))->assertStatus(200);
});

test('teacher can upload material and student can mark it as read', function () {
    Storage::fake('public');
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();
    $studentUser = User::where('role', 'siswa')->first();

    $file = UploadedFile::fake()->create('modul-fisika.pdf', 500, 'application/pdf');

    // Teacher uploads material
    $uploadRes = $this->actingAs($teacher)->post(route('lms.materials.store', $courseClass->id), [
        'title' => 'Modul 2: Energi Potensial dan Usaha',
        'topic' => 'Bab 2: Dinamika Gerak',
        'content_type' => 'document',
        'description' => 'Pelajari konsep energi dan gaya konservatif',
        'attachment' => $file,
    ]);

    $uploadRes->assertRedirect(route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi']));

    $material = LmsMaterial::where('title', 'Modul 2: Energi Potensial dan Usaha')->first();
    expect($material)->not->toBeNull();

    // Mark previous materials as read so sequential progression allows opening this new material
    foreach (LmsMaterial::where('course_class_id', $courseClass->id)->where('id', '<', $material->id)->get() as $prevMat) {
        LmsMaterialRead::firstOrCreate(['material_id' => $prevMat->id, 'student_id' => $studentUser->student->id], ['read_at' => now()]);
    }

    // Student views material
    $viewRes = $this->actingAs($studentUser)->get(route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $material->id]));
    $viewRes->assertStatus(200);
    $viewRes->assertSee('Modul 2: Energi Potensial dan Usaha');

    // Student marks as read
    $readRes = $this->actingAs($studentUser)->post(route('lms.materials.read', ['courseClass' => $courseClass->id, 'material' => $material->id]));
    $readRes->assertStatus(302);

    expect($material->reads()->where('student_id', $studentUser->student->id)->exists())->toBeTrue();
});

test('student can submit assignment and teacher can grade it', function () {
    Storage::fake('public');
    $courseClass = CourseClass::first();
    $assignment = LmsAssignment::where('course_class_id', $courseClass->id)->first();
    expect($assignment)->not->toBeNull();

    $studentUser = User::where('role', 'siswa')->first();
    $teacherUser = User::where('role', 'guru')->first();

    // Student submits text and fake file
    $submitFile = UploadedFile::fake()->create('jawaban-tugas.pdf', 300, 'application/pdf');

    $submitRes = $this->actingAs($studentUser)->post(route('lms.assignments.submit', ['courseClass' => $courseClass->id, 'assignment' => $assignment->id]), [
        'submission_text' => 'Ini adalah jawaban tugas lengkap dari praktikum.',
        'submission_file' => $submitFile,
    ]);

    $submitRes->assertStatus(302);

    $submission = LmsAssignmentSubmission::where('assignment_id', $assignment->id)
        ->where('student_id', $studentUser->student->id)
        ->first();

    expect($submission)->not->toBeNull()
        ->and($submission->status)->toBe('submitted');

    // Teacher grades submission
    $gradeRes = $this->actingAs($teacherUser)->put(route('lms.assignments.grade', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignment->id,
        'submission' => $submission->id,
    ]), [
        'score' => 95,
        'feedback' => 'Analisis dan perhitungan sangat rapi dan tepat!',
    ]);

    $gradeRes->assertStatus(302);

    $submission->refresh();
    expect($submission->score)->toEqual(95)
        ->and($submission->status)->toBe('graded')
        ->and($submission->feedback)->toBe('Analisis dan perhitungan sangat rapi dan tepat!');
});

test('student can take quiz and multiple choice is auto-graded', function () {
    $courseClass = CourseClass::first();
    $quiz = LmsQuiz::where('course_class_id', $courseClass->id)->with('questions')->first();
    expect($quiz)->not->toBeNull()
        ->and($quiz->questions->count())->toBeGreaterThan(0);

    $studentUser = User::where('role', 'siswa')->first();

    // Take quiz page
    $takeRes = $this->actingAs($studentUser)->get(route('lms.quizzes.take', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]));
    $takeRes->assertStatus(200);
    $takeRes->assertSee($quiz->title);

    // Prepare answers: answer all correctly
    $answers = [];
    foreach ($quiz->questions as $q) {
        $answers[$q->id] = $q->correct_answer ?: 'A';
    }

    $submitRes = $this->actingAs($studentUser)->post(route('lms.quizzes.submit', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]), [
        'answers' => $answers,
    ]);

    $submitRes->assertRedirect(route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]));

    $attempt = $quiz->attempts()->where('student_id', $studentUser->student->id)->latest()->first();
    expect($attempt)->not->toBeNull()
        ->and($attempt->completed_at)->not->toBeNull()
        ->and($attempt->score)->toBeGreaterThan(50);
});

test('teacher can create quiz with or without start and end times', function () {
    $courseClass = CourseClass::first();
    $teacherUser = User::where('role', 'guru')->first();

    // Create quiz without start_time and end_time (like default modal)
    $resWithoutDates = $this->actingAs($teacherUser)->post(route('lms.quizzes.store', $courseClass->id), [
        'title' => 'Kuis Mandiri: Fluida Statis',
        'quiz_type' => 'kuis',
        'duration_minutes' => 30,
        'passing_score' => 75,
        'instructions' => 'Kerjakan kuis dengan cermat.',
    ]);
    $resWithoutDates->assertStatus(302);

    $quiz1 = LmsQuiz::where('title', 'Kuis Mandiri: Fluida Statis')->first();
    expect($quiz1)->not->toBeNull()
        ->and($quiz1->start_time)->toBeNull()
        ->and($quiz1->end_time)->toBeNull();

    // Create quiz with start_time and end_time
    $resWithDates = $this->actingAs($teacherUser)->post(route('lms.quizzes.store', $courseClass->id), [
        'title' => 'Ulangan Harian 2: Fluida Dinamis',
        'quiz_type' => 'ulangan_harian',
        'duration_minutes' => 60,
        'passing_score' => 75,
        'start_time' => now()->format('Y-m-d\TH:i'),
        'end_time' => now()->addDays(2)->format('Y-m-d\TH:i'),
        'is_randomized' => 1,
    ]);
    $resWithDates->assertStatus(302);

    $quiz2 = LmsQuiz::where('title', 'Ulangan Harian 2: Fluida Dinamis')->first();
    expect($quiz2)->not->toBeNull()
        ->and($quiz2->start_time)->not->toBeNull()
        ->and($quiz2->is_randomized)->toBeTrue();
});

test('subject attendance supports manual batch input and student selfie verification', function () {
    Storage::fake('public');
    $courseClass = CourseClass::first();
    $teacherUser = User::where('role', 'guru')->first();
    $studentUser = User::where('role', 'siswa')->first();

    // Teacher opens attendance session
    $openRes = $this->actingAs($teacherUser)->post(route('lms.attendances.store', $courseClass->id), [
        'title' => 'Pertemuan 3: Hukum Gravitasi Semesta Newton',
        'meeting_date' => date('Y-m-d'),
        'start_time' => '08:00',
        'end_time' => '09:30',
        'attendance_method' => 'both',
        'notes' => 'Praktikum daring interaktif',
    ]);

    $openRes->assertStatus(302);
    $attendance = LmsSubjectAttendance::where('title', 'Pertemuan 3: Hukum Gravitasi Semesta Newton')->first();
    expect($attendance)->not->toBeNull()
        ->and($attendance->is_open)->toBeTrue();

    // Student submits selfie photo
    $selfieFile = UploadedFile::fake()->create('selfie-siswa.jpg', 200, 'image/jpeg');
    $selfieRes = $this->actingAs($studentUser)->post(route('lms.attendances.selfie', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]), [
        'selfie_file' => $selfieFile,
    ]);
    $selfieRes->assertStatus(302);

    $log = $attendance->logs()->where('student_id', $studentUser->student->id)->first();
    expect($log)->not->toBeNull()
        ->and($log->status)->toBe('hadir')
        ->and($log->verified_method)->toBe('selfie');

    // Teacher records manual batch statuses
    $students = $courseClass->schoolClass->students;
    $statuses = [];
    foreach ($students as $st) {
        $statuses[$st->id] = 'hadir';
    }

    $manualRes = $this->actingAs($teacherUser)->post(route('lms.attendances.manual', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]), [
        'statuses' => $statuses,
    ]);
    $manualRes->assertStatus(302);
});

test('gradebook computes averages and supports CSV export', function () {
    $courseClass = CourseClass::first();
    $teacherUser = User::where('role', 'guru')->first();

    // Gradebook view
    $gradeRes = $this->actingAs($teacherUser)->get(route('lms.grades', $courseClass->id));
    $gradeRes->assertStatus(200);
    $gradeRes->assertSee('Buku Rekap Nilai Siswa (Gradebook)');

    // CSV export
    $exportRes = $this->actingAs($teacherUser)->get(route('lms.grades.export', $courseClass->id));
    $exportRes->assertStatus(200);
    expect($exportRes->headers->get('content-type'))->toContain('text/csv');
});

test('class discussion allows creating topics and replies', function () {
    $courseClass = CourseClass::first();
    $teacherUser = User::where('role', 'guru')->first();
    $studentUser = User::where('role', 'siswa')->first();

    // Teacher creates pinned discussion
    $discRes = $this->actingAs($teacherUser)->post(route('lms.discussions.store', $courseClass->id), [
        'title' => 'Pengumuman Jadwal Remedial dan Pengayaan',
        'message' => 'Bagi siswa yang nilainya di bawah KKM, jadwal remedial akan dilaksanakan hari Jumat pukul 14:00.',
        'is_pinned' => '1',
    ]);
    $discRes->assertStatus(302);

    $discussion = LmsDiscussion::where('title', 'Pengumuman Jadwal Remedial dan Pengayaan')->first();
    expect($discussion)->not->toBeNull()
        ->and($discussion->is_pinned)->toBeTrue();

    // Student replies
    $replyRes = $this->actingAs($studentUser)->post(route('lms.discussions.reply', ['courseClass' => $courseClass->id, 'discussion' => $discussion->id]), [
        'message' => 'Baik Pak guru, terima kasih informasinya.',
    ]);
    $replyRes->assertStatus(302);

    expect($discussion->replies()->count())->toBe(1);
});

test('curriculum administrator can manage master subjects and course class assignments', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::where('role', 'admin')->first();

    // Master subjects page
    $masterRes = $this->actingAs($admin)->get(route('lms.master.subjects'));
    $masterRes->assertStatus(200);
    $masterRes->assertSee('Master Mata Pelajaran', false);

    // Create new SMA Subject (Permendikdasmen No. 13 Tahun 2025)
    $createSubRes = $this->actingAs($admin)->post(route('lms.master.subjects.store'), [
        'code' => 'ASTR-SMA',
        'name' => 'Astronomi & Astrofisika',
        'grade' => 'Fase F (Kelas XII)',
        'group' => 'Mata Pelajaran Pilihan',
        'kkm' => 78,
    ]);
    $createSubRes->assertStatus(302);

    $subject = Subject::where('code', 'ASTR-SMA')->first();
    expect($subject)->not->toBeNull()
        ->and($subject->name)->toBe('Astronomi & Astrofisika')
        ->and($subject->group)->toBe('Mata Pelajaran Pilihan');

    // Test Update Subject
    $updateSubRes = $this->actingAs($admin)->put(route('lms.master.subjects.update', $subject->id), [
        'code' => 'ASTR-SMA',
        'name' => 'Astrofisika & Kosmologi Modern',
        'grade' => 'Fase F (Kelas XII)',
        'group' => 'Mata Pelajaran Pilihan',
        'kkm' => 80,
    ]);
    $updateSubRes->assertStatus(302);
    expect($subject->fresh()->name)->toBe('Astrofisika & Kosmologi Modern');
});

test('teacher can view master subjects page and self-assign course classes to classes', function () {
    $teacherUser = User::where('role', 'guru')->has('teacher')->first();
    expect($teacherUser)->not->toBeNull();
    $teacher = $teacherUser->teacher;
    expect($teacher)->not->toBeNull();

    $subject = Subject::first();
    $schoolClass = SchoolClass::latest('id')->first();
    $academicYear = AcademicYear::first();

    // Teacher can access master subjects page
    $response = $this->actingAs($teacherUser)->get(route('lms.master.subjects'));
    $response->assertStatus(200);
    $response->assertSee('Tugaskan Diri ke Kelas');

    // Teacher self-assigns course class
    $assignResponse = $this->actingAs($teacherUser)->post(route('lms.master.course_classes.store'), [
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'day_of_week' => 'Rabu',
        'start_time' => '08:00',
        'end_time' => '09:30',
        'description' => 'Kelas belajar mandiri oleh guru pengampu',
    ]);
    $assignResponse->assertStatus(302);
    $assignResponse->assertSessionHas('success');

    $courseClass = CourseClass::where('subject_id', $subject->id)
        ->where('class_id', $schoolClass->id)
        ->first();

    expect($courseClass)->not->toBeNull()
        ->and($courseClass->teacher_id)->toBe($teacher->id);

    // Teacher can delete their own assigned course class
    $deleteRes = $this->actingAs($teacherUser)->delete(route('lms.master.course_classes.destroy', $courseClass->id));
    $deleteRes->assertStatus(302);
    $deleteRes->assertSessionHas('success');

    expect(CourseClass::find($courseClass->id))->toBeNull();
});

test('teacher cannot assign or delete course classes of other teachers', function () {
    $teacherUsers = User::where('role', 'guru')->has('teacher')->take(2)->get();
    if ($teacherUsers->count() < 2) {
        $otherTeacher = Teacher::create([
            'nip' => '999888777',
            'name' => 'Guru Penguji Lainnya',
            'gender' => 'L',
            'is_active' => true,
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Guru Penguji Lainnya',
            'role' => 'guru',
            'roles' => ['guru'],
        ]);
        $otherTeacher->update(['user_id' => $otherUser->id]);
        $teacherUsers = collect([$teacherUsers->first(), $otherUser]);
    }

    $teacher1User = $teacherUsers[0];
    $teacher2User = $teacherUsers[1];
    $teacher2 = $teacher2User->teacher;

    $subject = Subject::first();
    $schoolClass = SchoolClass::first();
    $academicYear = AcademicYear::first();

    // Create course class owned by teacher 2
    $courseClass2 = CourseClass::updateOrCreate([
        'subject_id' => $subject->id,
        'class_id' => $schoolClass->id,
        'academic_year_id' => $academicYear->id,
    ], [
        'teacher_id' => $teacher2->id,
        'day_of_week' => 'Kamis',
        'start_time' => '10:00',
        'end_time' => '11:30',
        'is_active' => true,
    ]);

    // Teacher 1 attempts to delete Teacher 2's course class
    $res = $this->actingAs($teacher1User)->delete(route('lms.master.course_classes.destroy', $courseClass2->id));
    $res->assertStatus(302);
    $res->assertSessionHas('error');

    expect(CourseClass::find($courseClass2->id))->not->toBeNull();
});

test('student is prohibited from accessing master subjects page', function () {
    $studentUser = User::where('role', 'siswa')->first();
    $res = $this->actingAs($studentUser)->get(route('lms.master.subjects'));
    $res->assertStatus(403);
});

test('teacher only sees their own taught subjects and assigned classes on master subjects page', function () {
    $teacherUser = User::where('role', 'guru')->has('teacher')->first();
    $teacher = $teacherUser->teacher;

    // Create another teacher who teaches another course
    $otherTeacher = Teacher::create([
        'nip' => '888777666',
        'name' => 'Dr. Budi Santoso, M.Pd.',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $allSubjects = Subject::take(2)->get();
    $subject1 = $allSubjects[0]; // Taught by $teacher
    $subject2 = $allSubjects[1]; // Taught by $otherTeacher

    $schoolClass = SchoolClass::first();
    $academicYear = AcademicYear::first();

    // Assign class 1 to our teacher
    $myClass = CourseClass::updateOrCreate([
        'subject_id' => $subject1->id,
        'class_id' => $schoolClass->id,
        'academic_year_id' => $academicYear->id,
    ], [
        'teacher_id' => $teacher->id,
        'day_of_week' => 'Senin',
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_active' => true,
    ]);

    // Assign class 2 to other teacher with unique day
    $otherClass = CourseClass::create([
        'subject_id' => $subject2->id,
        'class_id' => $schoolClass->id,
        'academic_year_id' => $academicYear->id,
        'teacher_id' => $otherTeacher->id,
        'day_of_week' => 'Jumat',
        'start_time' => '09:30',
        'end_time' => '11:00',
        'is_active' => true,
    ]);

    // Teacher visits master subjects page
    $response = $this->actingAs($teacherUser)->get(route('lms.master.subjects'));
    $response->assertStatus(200);

    // Assert teacher sees their own taught subject and class
    $response->assertSee($subject1->name);
    $response->assertSee('Alokasi & Penugasan Kelas yang Anda Ampu');
    $response->assertSee('Mata Pelajaran yang Anda Ampu');

    // Assert other teacher's name is NOT visible in the teacher's class allocation
    $response->assertDontSee($otherTeacher->name);

    // Admin access sees both teachers and all subjects
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    $adminResponse = $this->actingAs($admin)->get(route('lms.master.subjects'));
    $adminResponse->assertStatus(200);
    $adminResponse->assertSee($otherTeacher->name);
    $adminResponse->assertSee('Alokasi & Penugasan Guru Pengampu Kelas');
});

test('admin can generate standard subjects according to Permendikdasmen 13/2025', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();

    $response = $this->actingAs($admin)->post(route('lms.master.subjects.generate_standard'));
    $response->assertStatus(302);
    $response->assertSessionHas('success');

    expect(Subject::where('code', 'PAI-SMA')->exists())->toBeTrue()
        ->and(Subject::where('code', 'BIO-SMA')->exists())->toBeTrue()
        ->and(Subject::where('code', 'KIM-SMA')->exists())->toBeTrue()
        ->and(Subject::where('code', 'SND-SMA')->exists())->toBeTrue()
        ->and(Subject::count())->toBeGreaterThanOrEqual(26);
});

test('teacher can generate AI interactive material with content types matching manual upload based on Permendikdasmen 13/2025', function () {
    Storage::fake('public');
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();
    $studentUser = User::where('role', 'siswa')->first();

    // 1. Generate dengan content_type = video dan URL video
    $videoRes = $this->actingAs($teacher)->post(route('lms.materials.ai_generate', $courseClass->id), [
        'topic' => 'Hukum Kekekalan Energi dan Usaha',
        'content_type' => 'video',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'target_fase' => 'Fase E (Kelas X SMA)',
        'description' => 'Tonton video simulasi lalu pelajari kartu konsep dan selesaikan cek pemahaman instan.',
        'auto_publish' => 1,
    ]);

    $videoRes->assertStatus(302);
    $videoRes->assertSessionHas('success');

    $videoMaterial = LmsMaterial::where('course_class_id', $courseClass->id)
        ->where('topic', 'Hukum Kekekalan Energi dan Usaha')
        ->latest('id')
        ->first();

    expect($videoMaterial)->not->toBeNull()
        ->and($videoMaterial->content_type)->toBe('video')
        ->and($videoMaterial->external_url)->toBe('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        ->and($videoMaterial->description)->toContain('Tonton video simulasi');

    $content = json_decode($videoMaterial->content, true);
    expect($content)->toBeArray()
        ->and($content['capaian_pembelajaran'])->not->toBeEmpty()
        ->and($content['tujuan_pembelajaran'])->toBeArray()
        ->and($content['uraian_materi'])->toBeArray()->toHaveCount(3)
        ->and($content['uraian_materi'][0]['subjudul'])->toContain('Fondasi Konseptual')
        ->and($content['uraian_materi'][0]['subjudul'])->not->toContain('Sub-Bab')
        ->and($content['uraian_materi'][0]['konten_lengkap'])->not->toBeEmpty()
        ->and($content['uraian_materi'][0]['analogi'])->not->toBeEmpty()
        ->and($content['uraian_materi'][0]['poin_kunci'])->toBeArray()
        ->and($content['kartu_konsep'])->toBeArray()
        ->and($content['cek_pemahaman'])->toBeArray();

    // Mark previous materials as read so sequential progression allows opening this new material
    foreach (LmsMaterial::where('course_class_id', $courseClass->id)->where('id', '<', $videoMaterial->id)->get() as $prevMat) {
        LmsMaterialRead::firstOrCreate(['material_id' => $prevMat->id, 'student_id' => $studentUser->student->id], ['read_at' => now()]);
    }

    // Student views the interactive video material
    $showRes = $this->actingAs($studentUser)->get(route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $videoMaterial->id]));
    $showRes->assertStatus(200);
    $showRes->assertSee('Permendikdasmen No. 13 Tahun 2025');
    $showRes->assertSee('Penjelasan & Uraian Materi Terperinci', false);
    $showRes->assertSee('Fondasi Konseptual');
    $showRes->assertDontSee('Sub-Bab 1');
    $showRes->assertDontSee('Perbesar Gambar');
    $showRes->assertDontSee('Keterangan Gambar:');
    $showRes->assertSee('Analogi Sederhana (Mudah Dipahami)');
    $showRes->assertSee('Poin Kunci Penguasaan');
    $showRes->assertSee('Pemutar Video Pembelajaran Interaktif');
    $showRes->assertSee('Kartu Konsep Interaktif (Flashcards)');
    $showRes->assertSee('Cek Pemahaman Konsep (Interaktif)');
    $showRes->assertSee('Capaian Pembelajaran (CP)');
    $showRes->assertSee('Arahan Khusus Guru Pengampu');

    // 2. Generate dengan content_type = document dan berkas lampiran
    $fakePdf = UploadedFile::fake()->create('modul-suhu-kalor.pdf', 800, 'application/pdf');
    $docRes = $this->actingAs($teacher)->post(route('lms.materials.ai_generate', $courseClass->id), [
        'topic' => 'Termodinamika dan Kalor',
        'content_type' => 'document',
        'attachment' => $fakePdf,
        'target_fase' => 'Fase F (Kelas XI - XII SMA)',
        'auto_publish' => 1,
    ]);

    $docRes->assertStatus(302);
    $docMaterial = LmsMaterial::where('course_class_id', $courseClass->id)
        ->where('topic', 'Termodinamika dan Kalor')
        ->latest('id')
        ->first();

    expect($docMaterial)->not->toBeNull()
        ->and($docMaterial->content_type)->toBe('document')
        ->and($docMaterial->file_path)->not->toBeNull();

    // Mark previous material as read so sequential progression allows opening this document material
    LmsMaterialRead::firstOrCreate(['material_id' => $videoMaterial->id, 'student_id' => $studentUser->student->id], ['read_at' => now()]);

    $docShowRes = $this->actingAs($studentUser)->get(route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $docMaterial->id]));
    $docShowRes->assertStatus(200);
    $docShowRes->assertSee('DOKUMEN MODUL');
    $docShowRes->assertSee('Unduh Modul');

    // 3. Verifikasi bank gambar objek nyata (buku, komputer, dll) tanpa ilustrasi
    $bookImages = LmsAiCurriculumService::resolveEducationalImages('Bahasa Indonesia', 'Membaca Buku dan Telaah Pustaka');
    expect($bookImages)->toHaveCount(3)
        ->and($bookImages[0]['caption'])->toContain('Buku')
        ->and($bookImages[0]['alt'])->toContain('buku')
        ->and(strtolower($bookImages[0]['caption']))->not->toContain('ilustrasi');

    $laptopImages = LmsAiCurriculumService::resolveEducationalImages('Informatika', 'Perangkat Komputer dan Laptop');
    expect($laptopImages)->toHaveCount(3)
        ->and($laptopImages[0]['caption'])->toContain('Komputer')
        ->and(strtolower($laptopImages[0]['caption']))->not->toContain('ilustrasi');
});

test('teacher can generate AI assignment and PR mandiri with KKTP rubrics based on Permendikdasmen 13/2025', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();

    $response = $this->actingAs($teacher)->post(route('lms.assignments.ai_generate', $courseClass->id), [
        'topic' => 'Eksperimen Gerak Parabola',
        'task_type' => 'pr_mandiri',
        'due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
        'auto_publish' => 1,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');

    $assignment = LmsAssignment::where('course_class_id', $courseClass->id)
        ->latest('id')
        ->first();

    expect($assignment)->not->toBeNull()
        ->and($assignment->title)->toContain('Eksperimen Gerak Parabola')
        ->and($assignment->instructions)->toContain('KKTP')
        ->and($assignment->max_score)->toBe(100);
});

test('teacher can generate AI CBT assessment package with HOTS questions based on Permendikdasmen 13/2025', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();

    $response = $this->actingAs($teacher)->post(route('lms.quizzes.ai_generate', $courseClass->id), [
        'topic' => 'Kinematika Gerak Lurus',
        'quiz_type' => 'sumatif_materi',
        'duration_minutes' => 60,
        'question_count' => 5,
        'auto_publish' => 1,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');

    $quiz = LmsQuiz::where('course_class_id', $courseClass->id)
        ->where('title', 'like', '%Kinematika Gerak Lurus%')
        ->latest()
        ->first();

    expect($quiz)->not->toBeNull()
        ->and($quiz->duration_minutes)->toBe(60)
        ->and($quiz->questions()->count())->toBe(5);

    $firstQuestion = $quiz->questions()->first();
    expect($firstQuestion->options)->toBeArray()
        ->and($firstQuestion->correct_answer)->toBeIn(['A', 'B', 'C', 'D', 'E']);
});

test('teacher can manage CBT questions manually and generate additional AI questions according to Permendikdasmen 13/2025', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();

    $quiz = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Asesmen Formatif Fisika Dasar',
        'quiz_type' => 'formatif',
        'duration_minutes' => 45,
        'passing_score' => 75,
        'is_published' => true,
    ]);

    // 1. Tambah butir soal manual
    $storeRes = $this->actingAs($teacher)->post(route('lms.quizzes.questions.store', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]), [
        'question_text' => 'Sebuah benda bermassa 2 kg bergerak dengan percepatan 3 m/s2. Berapakah resultan gaya yang bekerja?',
        'option_a' => '6 N',
        'option_b' => '5 N',
        'option_c' => '1.5 N',
        'option_d' => '12 N',
        'option_e' => '10 N',
        'correct_answer' => 'A',
        'score_weight' => 10,
        'explanation' => 'Sesuai Hukum II Newton: F = m x a = 2 kg x 3 m/s2 = 6 N.',
    ]);

    $storeRes->assertStatus(302);
    $storeRes->assertSessionHas('success');

    $manualQ = LmsQuizQuestion::where('quiz_id', $quiz->id)->first();
    expect($manualQ)->not->toBeNull()
        ->and($manualQ->correct_answer)->toBe('A')
        ->and($manualQ->pembahasan ?? $manualQ->question_text)->toContain('Newton');

    // 2. Generate tambahan butir soal AI HOTS
    $genRes = $this->actingAs($teacher)->post(route('lms.quizzes.questions.ai_generate', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]), [
        'topic' => 'Dinamika Partikel dan Gaya',
        'count' => 3,
        'difficulty' => 'HOTS',
    ]);

    $genRes->assertStatus(302);
    $genRes->assertSessionHas('success');

    expect($quiz->questions()->count())->toBe(4);

    // 3. Hapus soal
    $delRes = $this->actingAs($teacher)->delete(route('lms.quizzes.questions.destroy', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id, 'question' => $manualQ->id]));
    $delRes->assertStatus(302);
    $delRes->assertSessionHas('success');

    expect($quiz->questions()->count())->toBe(3)
        ->and(LmsQuizQuestion::find($manualQ->id))->toBeNull();
});

test('teacher can generate multi-type CBT questions up to 50 items and export Kisi-Kisi and Kartu Soal documents', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();

    // 1. Generate CBT with custom multi-type composition (Total 20 items: 5 PG, 5 PGK, 5 B/S, 5 Isian)
    $genRes = $this->actingAs($teacher)->post(route('lms.quizzes.ai_generate', $courseClass->id), [
        'topic' => 'Termodinamika & Hukum Gas Ideal',
        'quiz_type' => 'sumatif_materi',
        'duration_minutes' => 90,
        'count_multiple_choice' => 5,
        'count_complex_multiple_choice' => 5,
        'count_true_false' => 5,
        'count_short_answer' => 5,
        'count_matching' => 0,
        'is_randomized' => 1,
        'auto_publish' => 1,
    ]);

    $genRes->assertStatus(302);
    $genRes->assertSessionHas('success');

    $quiz = LmsQuiz::where('course_class_id', $courseClass->id)
        ->where('title', 'like', '%Termodinamika%')
        ->latest()
        ->first();

    expect($quiz)->not->toBeNull()
        ->and($quiz->questions()->count())->toBe(20);

    // Verify question types are stored properly
    $types = $quiz->questions()->pluck('question_type')->unique()->toArray();
    expect($types)->toContain('multiple_choice')
        ->and($types)->toContain('complex_multiple_choice')
        ->and($types)->toContain('true_false')
        ->and($types)->toContain('short_answer');

    // Verify metadata for Permendikdasmen 13/2025
    $sampleQ = $quiz->questions()->first();
    expect($sampleQ->indikator_soal)->not->toBeNull()
        ->and($sampleQ->level_kognitif)->not->toBeNull()
        ->and($sampleQ->materi_pokok)->not->toBeNull()
        ->and($sampleQ->pembahasan)->not->toBeNull();

    // 2. Akses Dokumen Kisi-Kisi Soal
    $kisiRes = $this->actingAs($teacher)->get(route('lms.quizzes.kisi_kisi', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]));
    $kisiRes->assertStatus(200);
    $kisiRes->assertSee('KISI-KISI PENULISAN SOAL ASESMEN PEMBELAJARAN');
    $kisiRes->assertSee('Permendikdasmen No. 13 Tahun 2025');
    $kisiRes->assertSee('Capaian Pembelajaran (CP)');
    $kisiRes->assertSee('Indikator Soal');
    $kisiRes->assertSee('Level Kognitif');

    // 3. Akses Dokumen Kartu Soal
    $kartuRes = $this->actingAs($teacher)->get(route('lms.quizzes.kartu_soal', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]));
    $kartuRes->assertStatus(200);
    $kartuRes->assertSee('KARTU SOAL ASESMEN PEMBELAJARAN');
    $kartuRes->assertSee('Kunci Jawaban / Pedoman Penskoran:');
    $kartuRes->assertSee('Pembahasan Ilmiah');

    // 4. Test student evaluation across different question types
    $studentUser = User::where('role', 'siswa')->first();
    $pgQ = $quiz->questions()->where('question_type', 'multiple_choice')->first();
    $tfQ = $quiz->questions()->where('question_type', 'true_false')->first();
    $cmcQ = $quiz->questions()->where('question_type', 'complex_multiple_choice')->first();
    $saQ = $quiz->questions()->where('question_type', 'short_answer')->first();

    $answers = [
        $pgQ->id => $pgQ->correct_answer,
        $tfQ->id => $tfQ->correct_answer,
        $cmcQ->id => json_decode($cmcQ->correct_answer, true) ?: [$cmcQ->correct_answer],
        $saQ->id => $saQ->correct_answer,
    ];

    // Mark earlier quizzes as completed so sequential progression unlocks this quiz
    foreach (LmsQuiz::where('course_class_id', $courseClass->id)->where('id', '<', $quiz->id)->get() as $prevQuiz) {
        LmsQuizAttempt::firstOrCreate(
            ['quiz_id' => $prevQuiz->id, 'student_id' => $studentUser->student->id],
            ['started_at' => now(), 'completed_at' => now(), 'score' => 85, 'is_passed' => true]
        );
    }

    $submitRes = $this->actingAs($studentUser)->post(route('lms.quizzes.submit', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]), [
        'answers' => $answers,
    ]);

    $submitRes->assertStatus(302);
    $submitRes->assertSessionHas('success');

    $attempt = LmsQuizAttempt::where('quiz_id', $quiz->id)
        ->where('student_id', $studentUser->student->id)
        ->first();

    expect($attempt)->not->toBeNull()
        ->and($attempt->score)->toBeGreaterThan(0)
        ->and($attempt->completed_at)->not->toBeNull();
});

test('teacher can configure assignment submission types and allowed file formats', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();

    // 1. Guru membuat tugas manual dengan format file PDF dan PPT saja
    $res = $this->actingAs($teacher)->post(route('lms.assignments.store', $courseClass->id), [
        'title' => 'Tugas Presentasi Dinamika Rotasi',
        'description' => 'Silakan buat presentasi PPT atau dokumen laporan PDF.',
        'due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
        'max_score' => 100,
        'submission_type' => 'file',
        'allowed_file_types' => ['pdf', 'ppt'],
    ]);

    $res->assertStatus(302);
    $assignment = LmsAssignment::where('title', 'Tugas Presentasi Dinamika Rotasi')->first();
    expect($assignment)->not->toBeNull()
        ->and($assignment->submission_type)->toBe('file')
        ->and($assignment->allowed_file_types)->toBe(['pdf', 'ppt'])
        ->and($assignment->allowed_mimes_rule)->toBe('pdf,ppt,pptx');

    // 2. Guru membuat tugas melalui AI Generator dengan format Hanya Teks
    $resAi = $this->actingAs($teacher)->post(route('lms.assignments.ai_generate', $courseClass->id), [
        'topic' => 'Refleksi Filosofi Hukum Termodinamika',
        'task_type' => 'pr_mandiri',
        'submission_type' => 'text',
    ]);

    $resAi->assertStatus(302);
    $assignmentAi = LmsAssignment::where('topic', 'Refleksi Filosofi Hukum Termodinamika')->first();
    expect($assignmentAi)->not->toBeNull()
        ->and($assignmentAi->submission_type)->toBe('text')
        ->and($assignmentAi->allowed_file_types)->toBeNull();
});

test('assignment validation strictly enforces text or allowed file types for student submissions', function () {
    Storage::fake('public');
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();
    $studentUser = User::where('role', 'siswa')->first();

    // 1. Buat tugas dengan ketentuan HANYA BERKAS PDF & GAMBAR (PPT dilarang)
    $assignmentFileOnly = LmsAssignment::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Laporan Hasil Pengamatan (PDF/Gambar)',
        'instructions' => 'Kumpulkan laporan berupa PDF atau foto grafik.',
        'due_date' => now()->addDays(3),
        'max_score' => 100,
        'submission_type' => 'file',
        'allowed_file_types' => ['pdf', 'image'],
        'allow_late' => true,
        'is_published' => true,
    ]);

    // Mark previous assignments as submitted so sequential progression unlocks this assignment
    foreach (LmsAssignment::where('course_class_id', $courseClass->id)->where('id', '<', $assignmentFileOnly->id)->get() as $prevAsg) {
        LmsAssignmentSubmission::firstOrCreate(
            ['assignment_id' => $prevAsg->id, 'student_id' => $studentUser->student->id],
            ['submitted_at' => now(), 'content' => 'Tugas awal', 'is_late' => false]
        );
    }

    // Siswa coba kirim teks saja tanpa berkas -> Gagal validasi
    $resNoFile = $this->actingAs($studentUser)->post(route('lms.assignments.submit', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignmentFileOnly->id,
    ]), [
        'submission_text' => 'Ini teks jawaban saya tanpa file.',
    ]);
    $resNoFile->assertSessionHasErrors(['submission_file']);

    // Siswa coba kirim file dengan ekstensi dilarang (misal file .zip atau .ppt) -> Gagal validasi mimes
    $invalidPptFile = UploadedFile::fake()->create('slide-presentasi.ppt', 200, 'application/vnd.ms-powerpoint');
    $resInvalidFormat = $this->actingAs($studentUser)->post(route('lms.assignments.submit', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignmentFileOnly->id,
    ]), [
        'submission_file' => $invalidPptFile,
    ]);
    $resInvalidFormat->assertSessionHasErrors(['submission_file']);

    // Siswa kirim file sesuai format yang diizinkan (misal PDF) -> Berhasil!
    $validPdfFile = UploadedFile::fake()->create('laporan-resmi.pdf', 300, 'application/pdf');
    $resValidPdf = $this->actingAs($studentUser)->post(route('lms.assignments.submit', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignmentFileOnly->id,
    ]), [
        'submission_file' => $validPdfFile,
    ]);
    $resValidPdf->assertStatus(302);
    $resValidPdf->assertSessionHas('success');

    // 2. Buat tugas dengan ketentuan HANYA TEKS (berkas dilarang)
    $assignmentTextOnly = LmsAssignment::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Esai Analisis Kritis',
        'instructions' => 'Tuliskan esai langsung di sistem.',
        'due_date' => now()->addDays(3),
        'max_score' => 100,
        'submission_type' => 'text',
        'allowed_file_types' => null,
        'allow_late' => true,
        'is_published' => true,
    ]);

    // Siswa mencoba melampirkan berkas pada tugas text-only -> Ditolak
    $resTextWithFile = $this->actingAs($studentUser)->post(route('lms.assignments.submit', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignmentTextOnly->id,
    ]), [
        'submission_text' => 'Esai saya',
        'submission_file' => $validPdfFile,
    ]);
    $resTextWithFile->assertSessionHas('error');

    // Siswa mengirimkan teks saja -> Berhasil!
    $resValidText = $this->actingAs($studentUser)->post(route('lms.assignments.submit', [
        'courseClass' => $courseClass->id,
        'assignment' => $assignmentTextOnly->id,
    ]), [
        'submission_text' => 'Ini adalah jawaban esai analitis mendalam mengenai materi yang dibahas.',
    ]);
    $resValidText->assertStatus(302);
    $resValidText->assertSessionHas('success');
});

test('teacher can preview source class contents and import materials, assignments, and quizzes with questions', function () {
    $teacherUser = User::where('role', 'guru')->first();
    $teacher = $teacherUser->teacher;
    $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
    $subject = Subject::first();
    $classes = SchoolClass::take(2)->get();

    // Buat Kelas A (Sumber) dan Kelas B (Target)
    $classA = CourseClass::create([
        'academic_year_id' => $academicYear->id,
        'class_id' => $classes[0]->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'Senin',
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_active' => true,
    ]);

    $classB = CourseClass::create([
        'academic_year_id' => $academicYear->id,
        'class_id' => $classes[1]->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'Selasa',
        'start_time' => '09:30',
        'end_time' => '11:00',
        'is_active' => true,
    ]);

    // Isi konten di Kelas A
    $matA = LmsMaterial::create([
        'course_class_id' => $classA->id,
        'title' => 'Materi Bab 1 Kelas A',
        'topic' => 'Bab 1',
        'type' => 'document',
        'content' => 'Penjelasan rinci materi bab 1',
        'is_published' => true,
    ]);

    $asgA = LmsAssignment::create([
        'course_class_id' => $classA->id,
        'title' => 'Tugas 1 Analisis',
        'topic' => 'Bab 1',
        'instructions' => 'Kerjakan tugas analisis ini dengan teliti.',
        'due_date' => now()->addDays(5),
        'max_score' => 100,
        'submission_type' => 'both',
        'allowed_file_types' => ['pdf', 'ppt'],
        'allow_late' => true,
        'is_published' => true,
    ]);

    $quizA = LmsQuiz::create([
        'course_class_id' => $classA->id,
        'title' => 'Asesmen Formatif Bab 1',
        'quiz_type' => 'cbt',
        'description' => 'Ujian daring bab 1',
        'duration_minutes' => 45,
        'passing_score' => 75,
        'is_randomized' => true,
        'is_published' => true,
    ]);

    LmsQuizQuestion::create([
        'quiz_id' => $quizA->id,
        'question_text' => 'Apa definisi hukum Newton I?',
        'question_type' => 'multiple_choice',
        'options' => ['A' => 'Inersia', 'B' => 'Aksi Reaksi', 'C' => 'Gaya', 'D' => 'Gravitasi'],
        'correct_answer' => 'A',
        'score_weight' => 50,
        'order_number' => 1,
    ]);

    LmsQuizQuestion::create([
        'quiz_id' => $quizA->id,
        'question_text' => 'Pilihlah dua pernyataan yang benar mengenai gerak melingkar beraturan!',
        'question_type' => 'complex_multiple_choice',
        'options' => [
            'A' => 'Kecepatan sudut konstan',
            'B' => 'Kelajuan linear berubah',
            'C' => 'Percepatan sentripetal menuju pusat lingkaran',
            'D' => 'Gaya sentripetal menjauhi pusat',
        ],
        'correct_answer' => ['A', 'C'],
        'score_weight' => 50,
        'order_number' => 2,
    ]);

    // 1. Uji endpoint getSourceClassContents via AJAX
    $resJson = $this->actingAs($teacherUser)->getJson(route('lms.source_contents', [
        'courseClass' => $classB->id,
        'sourceClass' => $classA->id,
    ]));

    $resJson->assertStatus(200);
    $resJson->assertJsonStructure([
        'source_class' => ['id', 'subject_name', 'school_class_name', 'is_same_subject'],
        'materials',
        'assignments',
        'quizzes',
    ]);
    expect($resJson->json('materials'))->toHaveCount(1);
    expect($resJson->json('assignments'))->toHaveCount(1);
    expect($resJson->json('quizzes'))->toHaveCount(1);

    // 2. Salin konten terpilih dari Kelas A ke Kelas B
    $resImport = $this->actingAs($teacherUser)->post(route('lms.import_from_class', $classB->id), [
        'source_class_id' => $classA->id,
        'material_ids' => [$matA->id],
        'assignment_ids' => [$asgA->id],
        'quiz_ids' => [$quizA->id],
        'publish_status' => 'published',
        'assignment_due_mode' => 'add_7_days',
    ]);

    $resImport->assertStatus(302);
    $resImport->assertSessionHas('success');

    // Verifikasi data masuk di Kelas B
    $importedMat = LmsMaterial::where('course_class_id', $classB->id)->where('title', 'Materi Bab 1 Kelas A')->first();
    expect($importedMat)->not->toBeNull();
    expect($importedMat->is_published)->toBeTrue();

    $importedAsg = LmsAssignment::where('course_class_id', $classB->id)->where('title', 'Tugas 1 Analisis')->first();
    expect($importedAsg)->not->toBeNull();
    expect($importedAsg->is_published)->toBeTrue();

    $importedQuiz = LmsQuiz::where('course_class_id', $classB->id)->where('title', 'Asesmen Formatif Bab 1')->first();
    expect($importedQuiz)->not->toBeNull();
    expect($importedQuiz->questions)->toHaveCount(2);

    $importedQ1 = $importedQuiz->questions()->where('order_number', 1)->first();
    expect($importedQ1->correct_answer)->toBe('A');

    $importedQ2 = $importedQuiz->questions()->where('order_number', 2)->first();
    expect($importedQ2->question_type)->toBe('complex_multiple_choice');
    expect(json_decode($importedQ2->correct_answer, true))->toBe(['A', 'C']);
});

test('teacher can copy a single item to multiple target classes', function () {
    $teacherUser = User::where('role', 'guru')->first();
    $teacher = $teacherUser->teacher;
    $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
    $subject = Subject::first();
    $classes = SchoolClass::take(3)->get();

    $class1 = CourseClass::create([
        'academic_year_id' => $academicYear->id,
        'class_id' => $classes[0]->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'Senin',
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_active' => true,
    ]);

    $class2 = CourseClass::create([
        'academic_year_id' => $academicYear->id,
        'class_id' => $classes[1]->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'Selasa',
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_active' => true,
    ]);

    $class3 = CourseClass::create([
        'academic_year_id' => $academicYear->id,
        'class_id' => $classes[2]->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'Rabu',
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_active' => true,
    ]);

    $material = LmsMaterial::create([
        'course_class_id' => $class1->id,
        'title' => 'Materi Multi Kelas',
        'topic' => 'Bab Umum',
        'type' => 'document',
        'content' => 'Konten bersama',
        'is_published' => true,
    ]);

    $resCopy = $this->actingAs($teacherUser)->post(route('lms.copy_to_classes', $class1->id), [
        'item_type' => 'material',
        'item_id' => $material->id,
        'target_class_ids' => [$class2->id, $class3->id],
        'publish_status' => 'published',
    ]);

    $resCopy->assertStatus(302);
    $resCopy->assertSessionHas('success');

    expect(LmsMaterial::where('course_class_id', $class2->id)->where('title', 'Materi Multi Kelas')->exists())->toBeTrue();
    expect(LmsMaterial::where('course_class_id', $class3->id)->where('title', 'Materi Multi Kelas')->exists())->toBeTrue();
});

test('teacher can export and import full LMS package JSON', function () {
    $teacherUser = User::where('role', 'guru')->first();
    $teacher = $teacherUser->teacher;
    $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
    $subject = Subject::first();
    $classes = SchoolClass::take(2)->get();

    $classOrig = CourseClass::create([
        'academic_year_id' => $academicYear->id,
        'class_id' => $classes[0]->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'Kamis',
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_active' => true,
    ]);

    $classDest = CourseClass::create([
        'academic_year_id' => $academicYear->id,
        'class_id' => $classes[1]->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'Jumat',
        'start_time' => '07:30',
        'end_time' => '09:00',
        'is_active' => true,
    ]);

    LmsMaterial::create([
        'course_class_id' => $classOrig->id,
        'title' => 'Materi Paket JSON',
        'topic' => 'Bab 3',
        'type' => 'document',
        'content' => 'Isi materi paket',
        'is_published' => true,
    ]);

    $quiz = LmsQuiz::create([
        'course_class_id' => $classOrig->id,
        'title' => 'Kuis Paket JSON',
        'quiz_type' => 'cbt',
        'duration_minutes' => 30,
        'passing_score' => 75,
        'is_randomized' => true,
        'is_published' => true,
    ]);

    LmsQuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Soal dari paket',
        'question_type' => 'true_false',
        'options' => ['A' => 'Benar', 'B' => 'Salah'],
        'correct_answer' => 'A',
        'score_weight' => 100,
        'order_number' => 1,
    ]);

    // 1. Ekspor paket
    $resExport = $this->actingAs($teacherUser)->get(route('lms.export_package', $classOrig->id));
    $resExport->assertStatus(200);
    $resExport->assertHeader('Content-Type', 'application/json');

    $jsonContent = $resExport->getContent();
    $decoded = json_decode($jsonContent, true);
    expect($decoded['app'])->toBe('SISSMA1LE_LMS');
    expect($decoded['contents']['materials'])->toHaveCount(1);
    expect($decoded['contents']['quizzes'])->toHaveCount(1);

    // 2. Impor paket ke Kelas Tujuan
    $packageFile = UploadedFile::fake()->createWithContent('paket-export.json', $jsonContent);

    $resImport = $this->actingAs($teacherUser)->post(route('lms.import_package', $classDest->id), [
        'package_file' => $packageFile,
        'publish_status' => 'published',
        'assignment_due_days' => 14,
    ]);

    $resImport->assertStatus(302);
    $resImport->assertSessionHas('success');

    expect(LmsMaterial::where('course_class_id', $classDest->id)->where('title', 'Materi Paket JSON')->exists())->toBeTrue();
    $destQuiz = LmsQuiz::where('course_class_id', $classDest->id)->where('title', 'Kuis Paket JSON')->first();
    expect($destQuiz)->not->toBeNull();
    expect($destQuiz->questions)->toHaveCount(1);
    expect($destQuiz->questions()->first()->question_text)->toBe('Soal dari paket');
});

test('teacher can create zoom meeting with auto attendance and toggle its status', function () {
    $courseClass = CourseClass::with(['teacher.user', 'schoolClass'])->first();
    $teacherUser = $courseClass->teacher->user;

    $meetingUrl = 'https://us05web.zoom.us/j/1234567890?pwd=samplepassword';

    $response = $this->actingAs($teacherUser)->post(route('lms.zoom.store', $courseClass->id), [
        'title' => 'Tatap Muka Virtual Bab 1',
        'topic' => 'Pengenalan Mata Pelajaran',
        'agenda' => 'Orientasi pembelajaran dan kontrak belajar daring.',
        'meeting_url' => $meetingUrl,
        'meeting_id' => '123 456 7890',
        'passcode' => 'SMA1LE',
        'start_time' => now()->format('Y-m-d H:i:s'),
        'end_time' => now()->addMinutes(90)->format('Y-m-d H:i:s'),
        'status' => 'live',
        'auto_create_attendance' => 1,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');

    $meeting = LmsZoomMeeting::where('course_class_id', $courseClass->id)->latest()->first();
    expect($meeting)->not->toBeNull();
    expect($meeting->title)->toBe('Tatap Muka Virtual Bab 1');
    expect($meeting->status)->toBe('live');
    expect($meeting->is_active)->toBeTrue();

    // Pastikan presensi mapel otomatis terbuat
    $attendance = LmsSubjectAttendance::where('course_class_id', $courseClass->id)
        ->where('title', 'like', '%Tatap Muka Zoom: Tatap Muka Virtual Bab 1%')
        ->first();
    expect($attendance)->not->toBeNull();

    // Toggle status menjadi ended
    $resToggle = $this->actingAs($teacherUser)->post(route('lms.zoom.toggle_status', ['courseClass' => $courseClass->id, 'meeting' => $meeting->id]));
    $resToggle->assertStatus(302);
    $meeting->refresh();
    expect($meeting->status)->toBe('ended');
    expect($meeting->is_active)->toBeFalse();

    // Toggle kembali menjadi live
    $this->actingAs($teacherUser)->post(route('lms.zoom.toggle_status', ['courseClass' => $courseClass->id, 'meeting' => $meeting->id]));
    $meeting->refresh();
    expect($meeting->status)->toBe('live');
    expect($meeting->is_active)->toBeTrue();
});

test('student in that class rombel can join zoom meeting and attendance participant is recorded', function () {
    $courseClass = CourseClass::with(['teacher.user'])->first();
    $teacherUser = $courseClass->teacher->user;

    // Pastikan ada siswa aktif di kelas rombel ini dengan akun user
    $studentUser = User::where('role', 'siswa')->first();
    $student = $studentUser->student;
    $student->class_id = $courseClass->class_id;
    $student->save();

    // Buat Zoom meeting aktif
    $meeting = LmsZoomMeeting::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Sesi Tatap Muka Live',
        'meeting_url' => 'https://us05web.zoom.us/j/999888777?pwd=secret',
        'meeting_id' => '999 888 777',
        'start_time' => now(),
        'status' => 'live',
        'is_active' => true,
        'user_id' => $teacherUser->id,
    ]);

    // Siswa di kelas rombel ini mengakses link join
    $resJoin = $this->actingAs($studentUser)->get(route('lms.zoom.join', ['courseClass' => $courseClass->id, 'meeting' => $meeting->id]));

    // Diarahkan langsung ke URL Zoom
    $resJoin->assertRedirect($meeting->meeting_url);

    // Kehadiran siswa tercatat di lms_zoom_participants
    $participant = LmsZoomParticipant::where('zoom_meeting_id', $meeting->id)
        ->where('student_id', $student->id)
        ->first();

    expect($participant)->not->toBeNull();
    expect($meeting->isAttendedBy($student->id))->toBeTrue();
});

test('student from another class rombel is strictly forbidden (403) from joining zoom meet', function () {
    $courseClass = CourseClass::with(['teacher.user'])->first();
    $teacherUser = $courseClass->teacher->user;

    $meeting = LmsZoomMeeting::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Sesi Privat Rombel Tertentu',
        'meeting_url' => 'https://us05web.zoom.us/j/privatezoom',
        'start_time' => now(),
        'status' => 'live',
        'is_active' => true,
        'user_id' => $teacherUser->id,
    ]);

    // Setup siswa dari kelas lain
    $otherClass = SchoolClass::where('id', '!=', $courseClass->class_id)->first();
    $otherStudentUser = User::where('role', 'siswa')->first();
    $otherStudent = $otherStudentUser->student;
    $otherStudent->class_id = $otherClass->id;
    $otherStudent->save();

    // Percobaan join oleh siswa dari rombel lain harus DITOLAK 403 Forbidden
    $response = $this->actingAs($otherStudentUser)->get(route('lms.zoom.join', ['courseClass' => $courseClass->id, 'meeting' => $meeting->id]));
    $response->assertStatus(403);

    // Pastikan tidak ada data participant tercatat untuk siswa asing
    expect(LmsZoomParticipant::where('zoom_meeting_id', $meeting->id)->where('student_id', $otherStudent->id)->exists())->toBeFalse();
});

test('teacher who does not teach the course is forbidden (403) from managing or joining zoom meet', function () {
    $courseClass = CourseClass::with(['teacher.user'])->first();

    // Cari atau buat akun guru reguler (bukan admin) yang tidak mengampu kelas ini
    $otherTeacherUser = User::where('role', 'guru')
        ->whereDoesntHave('teacher', fn ($q) => $q->where('id', $courseClass->teacher_id))
        ->first();

    if (! $otherTeacherUser) {
        $otherTeacherUser = User::factory()->create([
            'role' => 'guru',
            'password' => bcrypt('password'),
        ]);
        $otherTeacher = Teacher::create([
            'user_id' => $otherTeacherUser->id,
            'nip' => '199999999999999999',
            'name' => 'Guru Lain Mapel Berbeda',
            'gender' => 'L',
            'is_active' => true,
        ]);
    } else {
        $otherTeacher = $otherTeacherUser->teacher;
    }

    // Percobaan membuat zoom meeting oleh guru lain harus DITOLAK 403
    $resStore = $this->actingAs($otherTeacherUser)->post(route('lms.zoom.store', $courseClass->id), [
        'title' => 'Unauthorized Meeting',
        'meeting_url' => 'https://zoom.us/j/12345',
        'start_time' => now()->format('Y-m-d H:i:s'),
    ]);
    $resStore->assertStatus(403);

    // Buat meeting oleh guru pengampu sah
    $meeting = LmsZoomMeeting::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Official Class Meeting',
        'meeting_url' => 'https://us05web.zoom.us/j/official',
        'start_time' => now(),
        'status' => 'live',
        'is_active' => true,
        'user_id' => $courseClass->teacher->user->id,
    ]);

    // Guru lain mencoba join ke sesi kelas yang bukan diampunya harus DITOLAK 403
    $resJoin = $this->actingAs($otherTeacherUser)->get(route('lms.zoom.join', ['courseClass' => $courseClass->id, 'meeting' => $meeting->id]));
    $resJoin->assertStatus(403);
});

test('document material (PDF, PPT, DOC) embeds in-page viewer with finish confirmation button', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();
    $studentUser = User::where('role', 'siswa')->first();

    // Mark previous materials as read if any
    $previousMaterials = LmsMaterial::where('course_class_id', $courseClass->id)->get();
    foreach ($previousMaterials as $pm) {
        LmsMaterialRead::firstOrCreate(['material_id' => $pm->id, 'student_id' => $studentUser->student->id], ['read_at' => now()]);
    }

    // Create PDF material
    $material = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Modul PDF Fisika Modern',
        'topic' => 'Fisika Kuantum',
        'type' => 'document',
        'file_path' => 'lms/materials/sample_modul.pdf',
        'file_name' => 'sample_modul.pdf',
        'file_size' => 102400,
        'is_published' => true,
    ]);

    expect($material->isEmbeddedDocument())->toBeTrue()
        ->and($material->getDocumentExtension())->toBe('pdf');

    $response = $this->actingAs($studentUser)->get(route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $material->id]));
    $response->assertStatus(200);
    $response->assertSee('DOKUMEN MODUL');
    $response->assertSee('doc-viewer-frame');
    $response->assertSee('Klik Finish / Selesai Membaca Dokumen');

    // Click finish button (post to read route)
    $finishRes = $this->actingAs($studentUser)->post(route('lms.materials.read', ['courseClass' => $courseClass->id, 'material' => $material->id]));
    $finishRes->assertStatus(302);

    expect($material->isReadBy($studentUser->student->id))->toBeTrue();
});

test('video material features strict anti-cheat player and allows teacher to configure in-video questions with rewind points', function () {
    $courseClass = CourseClass::first();
    $teacherUser = $courseClass->teacher->user;
    $studentUser = User::where('role', 'siswa')->first();

    // Mark previous materials as read if any
    $previousMaterials = LmsMaterial::where('course_class_id', $courseClass->id)->get();
    foreach ($previousMaterials as $pm) {
        LmsMaterialRead::firstOrCreate(['material_id' => $pm->id, 'student_id' => $studentUser->student->id], ['read_at' => now()]);
    }

    // Create video material with YouTube URL
    $material = LmsMaterial::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Video Pembelajaran Interaktif Termodinamika',
        'topic' => 'Termodinamika',
        'type' => 'video',
        'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'is_published' => true,
    ]);

    expect($material->isVideoContent())->toBeTrue();

    // Teacher adds an in-video question at minute 2:30 (150s), rewind to minute 1:00 (60s)
    $qRes = $this->actingAs($teacherUser)->post(route('lms.materials.video_questions.store', ['courseClass' => $courseClass->id, 'material' => $material->id]), [
        'timestamp_minute' => 2,
        'timestamp_second' => 30,
        'question' => 'Apa hukum kekekalan energi pada Termodinamika?',
        'option_a' => 'Hukum Termodinamika I',
        'option_b' => 'Hukum Termodinamika II',
        'option_c' => 'Hukum Newton',
        'option_d' => 'Hukum Ohm',
        'correct_answer' => 'A',
        'rewind_minute' => 1,
        'rewind_second' => 0,
        'explanation' => 'Hukum Termodinamika I menyatakan bahwa energi tidak dapat diciptakan atau dimusnahkan.',
    ]);

    $qRes->assertStatus(302);

    $material->refresh();
    expect($material->video_questions)->toHaveCount(1)
        ->and($material->video_questions[0]['timestamp_seconds'])->toBe(150)
        ->and($material->video_questions[0]['rewind_seconds'])->toBe(60)
        ->and($material->video_questions[0]['correct_answer'])->toBe('A');

    // Student visits video material page
    $studentShow = $this->actingAs($studentUser)->get(route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $material->id]));
    $studentShow->assertStatus(200);
    $studentShow->assertSee('video-player-container');
    $studentShow->assertSee('video-strict-toast');
    $studentShow->assertSee('video-question-overlay');
    $studentShow->assertSee('video-finish-overlay');
    $studentShow->assertSee('Klik Finish / Selesaikan Materi Video Ini');

    // Teacher can delete the video question
    $delRes = $this->actingAs($teacherUser)->delete(route('lms.materials.video_questions.destroy', [
        'courseClass' => $courseClass->id,
        'material' => $material->id,
        'questionIndex' => 0,
    ]));
    $delRes->assertStatus(302);

    $material->refresh();
    expect($material->video_questions)->toHaveCount(0);
});

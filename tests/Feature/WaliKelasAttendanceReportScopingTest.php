<?php

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;

beforeEach(function () {
    AcademicYear::firstOrCreate(
        ['is_active' => true],
        ['name' => '2026/2027', 'semester' => 'Ganjil', 'start_date' => '2026-07-15', 'end_date' => '2026-12-20']
    );
});

function getTwoClassesWithWaliKelas(): array
{
    $classes = SchoolClass::with('homeroomTeacher.user')->whereHas('homeroomTeacher.user')->take(2)->get();
    if ($classes->count() < 2) {
        $classes = SchoolClass::take(2)->get();
    }

    $classA = $classes[0];
    $classB = $classes[1];

    $teacherA = $classA->homeroomTeacher;
    if (! $teacherA) {
        $teacherA = Teacher::create([
            'nip' => '198501012010011001',
            'name' => 'Guru Wali Kelas A',
            'gender' => 'L',
            'is_active' => true,
        ]);
        $classA->update(['homeroom_teacher_id' => $teacherA->id]);
    }

    $userA = $teacherA->user;
    if (! $userA) {
        $userA = User::create([
            'name' => $teacherA->name,
            'username' => $teacherA->nip,
            'email' => 'wali_a_'.uniqid().'@sman1lengkong.sch.id',
            'password' => bcrypt('password'),
            'role' => 'wali_kelas',
            'roles' => ['wali_kelas'],
            'is_active' => true,
        ]);
        $teacherA->update(['user_id' => $userA->id]);
    } else {
        $userA->update(['role' => 'wali_kelas', 'roles' => ['wali_kelas']]);
    }

    return [$classA, $classB, $userA];
}

test('presensi utama report only shows homeroom class for wali kelas', function () {
    [$classA, $classB, $userA] = getTwoClassesWithWaliKelas();

    // 1. attendance.report
    $response = $this->actingAs($userA)->get(route('attendance.report'));
    $response->assertStatus(200);

    $viewClasses = $response->viewData('classes');
    expect($viewClasses->pluck('id'))->toContain($classA->id);
    expect($viewClasses->pluck('id'))->not->toContain($classB->id);

    // Option of class B should not exist in the dropdown
    $content = $response->getContent();
    expect($content)->toContain('Kelas '.$classA->name);
    expect($content)->not->toContain('Kelas '.$classB->name);

    // 2. reports.index (type=attendance)
    $responseReports = $this->actingAs($userA)->get(route('reports.index', ['type' => 'attendance']));
    $responseReports->assertStatus(200);

    $viewClassesReports = $responseReports->viewData('classes');
    expect($viewClassesReports->pluck('id'))->toContain($classA->id);
    expect($viewClassesReports->pluck('id'))->not->toContain($classB->id);
    expect($responseReports->getContent())->toContain('Kelas '.$classA->name);
    expect($responseReports->getContent())->not->toContain('Kelas '.$classB->name);

    // 3. reports.print.attendance
    $responsePrint = $this->actingAs($userA)->get(route('reports.print.attendance'));
    $responsePrint->assertStatus(200);
    expect($responsePrint->getContent())->toContain('Kelas '.$classA->name);
    expect($responsePrint->getContent())->not->toContain('Kelas '.$classB->name);
});

test('presensi ekstrakurikuler report and print only show and scope to homeroom class for wali kelas', function () {
    [$classA, $classB, $userA] = getTwoClassesWithWaliKelas();

    $extra = Extracurricular::where('is_active', true)->first();
    if (! $extra) {
        $extra = Extracurricular::create([
            'code' => 'PRAMUKA',
            'name' => 'Pramuka Wajib',
            'category' => 'Praja Muda Karana',
            'is_active' => true,
        ]);
    }

    // 1. extracurricular.report
    $response = $this->actingAs($userA)->get(route('extracurricular.report', ['extra_id' => $extra->id]));
    $response->assertStatus(200);

    $viewClasses = $response->viewData('classes');
    expect($viewClasses->pluck('id'))->toContain($classA->id);
    expect($viewClasses->pluck('id'))->not->toContain($classB->id);

    $content = $response->getContent();
    expect($content)->toContain('Kelas '.$classA->name);
    expect($content)->not->toContain('Kelas '.$classB->name);
    expect($content)->not->toContain('Semua Rombel');

    // 2. extracurricular.print: attempting to request class B must be locked to class A
    $responsePrint = $this->actingAs($userA)->get(route('extracurricular.print', [
        'extra_id' => $extra->id,
        'class_id' => $classB->id,
    ]));
    $responsePrint->assertStatus(200);
    $selectedClass = $responsePrint->viewData('selectedClass');
    expect($selectedClass)->not->toBeNull();
    expect($selectedClass->id)->toBe($classA->id);
});

test('presensi ibadah (keputrian, dzuhur-jumat, dhuha) only show and scope to homeroom class for wali kelas', function () {
    [$classA, $classB, $userA] = getTwoClassesWithWaliKelas();

    $activities = ['keputrian', 'dzuhur-jumat', 'dhuha'];

    foreach ($activities as $act) {
        // 1. worship.report
        $response = $this->actingAs($userA)->get(route('worship.report', $act));
        $response->assertStatus(200);

        $viewClasses = $response->viewData('classes');
        expect($viewClasses->pluck('id'))->toContain($classA->id);
        expect($viewClasses->pluck('id'))->not->toContain($classB->id);

        $content = $response->getContent();
        expect($content)->toContain('Kelas '.$classA->name);
        expect($content)->not->toContain('Kelas '.$classB->name);
        expect($content)->not->toContain('Semua Kelas');

        // 2. worship.print: attempting to pass class B must lock to class A
        $responsePrint = $this->actingAs($userA)->get(route('worship.print', [
            'activity' => $act,
            'class_id' => $classB->id,
        ]));
        $responsePrint->assertStatus(200);
        $selectedClass = $responsePrint->viewData('selectedClass');
        expect($selectedClass)->not->toBeNull();
        expect($selectedClass->id)->toBe($classA->id);
    }
});

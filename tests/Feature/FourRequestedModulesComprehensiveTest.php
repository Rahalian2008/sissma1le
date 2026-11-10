<?php

use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestQuestion;
use App\Models\BkPsychologicalTestResult;
use App\Models\CourseClass;
use App\Models\Employee;
use App\Models\LibraryCategory;
use App\Models\Student;
use App\Models\User;

test('1. BK psikotes: questions exist, can be taken by user, and produces clear executive summary and recommendations', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    // 1. Ensure test and questions are populated
    $response = $this->actingAs($admin)->get(route('bk.psychological.index'));
    $response->assertStatus(200);

    $test = BkPsychologicalTest::where('slug', 'vak')->first();
    expect($test)->not->toBeNull();
    expect(BkPsychologicalTestQuestion::where('test_id', $test->id)->count())->toBeGreaterThan(0);

    // 2. Take test page loads with questions
    $takeResponse = $this->actingAs($admin)->get(route('bk.psychological.take', 'vak'));
    $takeResponse->assertStatus(200);
    $takeResponse->assertSee($test->name);

    // 3. Submit test answers
    $student = Student::where('is_active', true)->first();
    expect($student)->not->toBeNull();

    $questions = BkPsychologicalTestQuestion::where('test_id', $test->id)->get();
    $answers = [];
    foreach ($questions as $q) {
        $answers[$q->id] = rand(2, 4);
    }

    $submitResponse = $this->actingAs($admin)->post(route('bk.psychological.submit', 'vak'), [
        'student_id' => $student->id,
        'answers' => $answers,
    ]);

    $submitResponse->assertRedirect();
    $result = BkPsychologicalTestResult::latest('id')->first();
    expect($result)->not->toBeNull();
    expect($result->dominant_dimension)->not->toBeEmpty();
    expect($result->analysis_summary)->not->toBeEmpty();
    expect($result->study_recommendations)->not->toBeEmpty();

    // 4. Result view loads and shows summary and conclusion
    $resultResponse = $this->actingAs($admin)->get(route('bk.psychological.result', $result));
    $resultResponse->assertStatus(200);
    $resultResponse->assertSee('Kesimpulan');
    $resultResponse->assertSee($result->dominant_dimension);
});

test('2. Perpustakaan: DDC 000 - 900 categories are populated and load in catalog', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $response = $this->actingAs($admin)->get(route('library.index'));
    $response->assertStatus(200);

    // Verify 10 main classes
    $ddcCodes = ['000', '100', '200', '300', '400', '500', '600', '700', '800', '900'];
    foreach ($ddcCodes as $code) {
        expect(LibraryCategory::where('ddc_code', $code)->exists())->toBeTrue();
    }

    // Books catalogue loads categories
    $booksResponse = $this->actingAs($admin)->get(route('library.books.index'));
    $booksResponse->assertStatus(200);
    $booksResponse->assertViewHas('categories');
});

test('3. Rapor Kurnas: mapel per kelas are populated and sync endpoint works', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    // 1. Scores input view loads course classes
    $scoresResponse = $this->actingAs($admin)->get(route('rapor_kurnas.scores'));
    $scoresResponse->assertStatus(200);
    $scoresResponse->assertViewHas('courseClasses');

    expect(CourseClass::count())->toBeGreaterThan(0);

    // 2. Sync course classes endpoint works
    $syncResponse = $this->actingAs($admin)->post(route('rapor_kurnas.sync_course_classes'));
    $syncResponse->assertRedirect();
    $syncResponse->assertSessionHas('success');
});

test('4. Adm Ketatausahaan: all 5 submenus render 200 OK and employees are populated', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    // 1. TU Central Dashboard
    $this->actingAs($admin)->get(route('adm_tu.index'))->assertStatus(200);

    // 2. Kesiswaan
    $this->actingAs($admin)->get(route('tu.kesiswaan.index'))->assertStatus(200);

    // 3. Kepegawaian
    $this->actingAs($admin)->get(route('tu.kepegawaian.index'))->assertStatus(200);
    expect(Employee::count())->toBeGreaterThan(0);

    // 4. Persuratan
    $this->actingAs($admin)->get(route('tu.persuratan.index'))->assertStatus(200);

    // 5. Sarpras
    $this->actingAs($admin)->get(route('tu.sarpras.index'))->assertStatus(200);

    // 6. Keuangan
    $this->actingAs($admin)->get(route('tu.keuangan.index'))->assertStatus(200);
});

<?php

use App\Models\BkAkpd;
use App\Models\BkAkpdQuestion;
use App\Models\BkAkpdResponse;
use App\Models\BkConsultation;
use App\Models\BkConsultationMessage;
use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestQuestion;
use App\Models\BkPsychologicalTestResult;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'XII-MIPA-1',
        'grade' => 'XII',
        'major' => 'MIPA',
    ]);

    $this->studentUser = User::factory()->create([
        'role' => 'siswa',
    ]);

    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'nis' => '999901',
        'nisn' => '0099990001',
        'name' => 'Siswa Test Satu Kali',
        'class_id' => $this->class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $this->bkUser = User::factory()->create([
        'role' => 'guru_bk',
    ]);

    $this->adminUser = User::factory()->create([
        'role' => 'admin',
    ]);
});

test('/bk/konseling/create is reachable and does not return 404 for BK counselor', function () {
    $response = $this->actingAs($this->bkUser)
        ->get('/bk/konseling/create');

    $response->assertStatus(200);
});

test('voice note upload is saved as AUDIO and not VIDEO', function () {
    $consultation = BkConsultation::create([
        'consultation_code' => 'CRH-'.now()->format('ymd').'-0001',
        'student_id' => $this->student->id,
        'topic' => 'Tes Konseling Suara',
        'category' => 'Pribadi',
        'status' => 'OPEN',
    ]);

    // File audio/webm yang biasa dikirim oleh Chrome MediaRecorder
    $voiceFile = UploadedFile::fake()->create('voicenote_1731940000.webm', 25, 'video/webm');

    $response = $this->actingAs($this->studentUser)
        ->postJson(route('bk.curhat.message', $consultation), [
            'attachment' => $voiceFile,
        ]);

    $response->assertStatus(200);

    $savedMessage = BkConsultationMessage::where('consultation_id', $consultation->id)->latest()->first();

    expect($savedMessage)->not->toBeNull();
    expect($savedMessage->attachment_type)->toBe('AUDIO');
    expect($savedMessage->is_audio)->toBeTrue();
    expect($savedMessage->is_video)->toBeFalse();
});

test('student can only take psikotes 1 time and can retake only after BK/admin reset', function () {
    $test = BkPsychologicalTest::firstOrCreate(
        ['slug' => 'vak'],
        [
            'name' => 'Tes Gaya Belajar VAK',
            'category' => 'Gaya Belajar',
            'short_desc' => 'Deskripsi tes VAK',
            'instructions' => 'Petunjuk pengerjaan',
            'is_active' => true,
        ]
    );

    BkPsychologicalTestQuestion::firstOrCreate(
        ['test_id' => $test->id, 'question_number' => 1],
        [
            'dimension' => 'VISUAL',
            'question_text' => 'Soal 1',
            'options' => ['A' => 'Visual 1', 'B' => 'Auditori 1'],
        ]
    );

    // Pertama kali: siswa bisa akses take page
    $response = $this->actingAs($this->studentUser)
        ->get(route('bk.psychological.take', 'vak'));
    $response->assertStatus(200);

    // Buat hasil tes untuk siswa
    $result = BkPsychologicalTestResult::create([
        'student_id' => $this->student->id,
        'test_id' => $test->id,
        'dominant_dimension' => 'VISUAL',
        'profile_label' => 'Gaya Belajar Visual',
        'dimension_scores' => ['VISUAL' => 10, 'AUDITORI' => 5],
        'analysis_summary' => 'Ringkasan profil',
        'answers' => [1 => 4],
        'taken_at' => now(),
    ]);

    // Kedua kali: siswa mencoba mengakses take lagi, harus diarahkan ke result dengan peringatan 1 kali
    $responseRepeat = $this->actingAs($this->studentUser)
        ->get(route('bk.psychological.take', 'vak'));
    $responseRepeat->assertRedirect(route('bk.psychological.result', $result));
    $responseRepeat->assertSessionHas('warning');

    // Coba submit lagi lewat POST, harus diarahkan ke result dengan peringatan
    $responseSubmit = $this->actingAs($this->studentUser)
        ->post(route('bk.psychological.submit', 'vak'), [
            'answers' => [1 => 3],
        ]);
    $responseSubmit->assertRedirect(route('bk.psychological.result', $result));
    $responseSubmit->assertSessionHas('warning');

    // Guru BK melakukan reset (delete result)
    $responseReset = $this->actingAs($this->bkUser)
        ->delete(route('bk.psychological.destroy', $result));
    $responseReset->assertRedirect(route('bk.psychological.index'));
    $responseReset->assertSessionHas('success');

    // Setelah di-reset, siswa bisa mengambil tes lagi
    $responseAfterReset = $this->actingAs($this->studentUser)
        ->get(route('bk.psychological.take', 'vak'));
    $responseAfterReset->assertStatus(200);
});

test('student can only submit AKPD 1 time and can retake only after BK/admin reset', function () {
    $akpd = BkAkpd::create([
        'title' => 'AKPD Kelas XII 2026/2027',
        'grade_level' => 'XII',
        'description' => 'Angket Kebutuhan Peserta Didik',
        'is_active' => true,
    ]);

    $question = BkAkpdQuestion::create([
        'akpd_id' => $akpd->id,
        'code' => 'K01',
        'statement' => 'Saya merasa bingung memilih jurusan kuliah',
        'field_of_service' => 'KARIER',
        'sort_order' => 1,
    ]);

    // Akses pertama kali
    $response = $this->actingAs($this->studentUser)
        ->get(route('bk.akpd.take', $akpd));
    $response->assertStatus(200);

    // Buat respons untuk siswa
    $akpdResponse = BkAkpdResponse::create([
        'akpd_id' => $akpd->id,
        'student_id' => $this->student->id,
        'selected_question_ids' => [$question->id],
        'score_per_field' => ['KARIER' => ['percentage' => 100]],
        'highest_need_field' => 'KARIER',
        'submitted_at' => now(),
    ]);

    // Coba akses take lagi sebagai siswa -> harus diredirect dengan warning
    $responseRepeat = $this->actingAs($this->studentUser)
        ->get(route('bk.akpd.take', $akpd));
    $responseRepeat->assertRedirect(route('bk.akpd.index'));
    $responseRepeat->assertSessionHas('warning');

    // Coba submit lagi lewat POST -> harus ditolak dan diredirect dengan warning
    $responseSubmit = $this->actingAs($this->studentUser)
        ->post(route('bk.akpd.submit', $akpd), [
            'student_id' => $this->student->id,
            'selected_questions' => [$question->id],
        ]);
    $responseSubmit->assertRedirect(route('bk.akpd.index'));
    $responseSubmit->assertSessionHas('warning');

    // Guru BK melakukan reset (delete response)
    $responseReset = $this->actingAs($this->bkUser)
        ->delete(route('bk.akpd.response.destroy', ['akpd' => $akpd->id, 'response' => $akpdResponse->id]));
    $responseReset->assertRedirect(route('bk.akpd.show', $akpd));
    $responseReset->assertSessionHas('success');

    // Setelah di-reset, siswa dapat mengakses take lagi
    $responseAfterReset = $this->actingAs($this->studentUser)
        ->get(route('bk.akpd.take', $akpd));
    $responseAfterReset->assertStatus(200);
});

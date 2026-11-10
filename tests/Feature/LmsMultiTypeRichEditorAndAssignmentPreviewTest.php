<?php

use App\Models\AcademicYear;
use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    SchoolSetting::updateOrCreate(['id' => 1], [
        'school_name' => 'SMA Negeri 1 Lengkong',
        'school_address' => 'Jalan Raya Tegalega, Kec. Lengkong',
        'school_city' => 'Sukabumi',
        'headmaster_name' => 'Dr. H. Juanda, M.Pd.',
        'headmaster_nip' => '197001011995121001',
    ]);
});

test('1. CBT manual creation preserves multi-type question composition and redirects to question input', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru CBT Lengkong',
        'nip' => '19850101'.fake()->unique()->numerify('######'),
    ]);

    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'ganjil',
        'is_active' => true,
    ]);

    $schoolClass = SchoolClass::create([
        'name' => 'XII IPA 1',
        'grade' => '12',
        'academic_year_id' => $academicYear->id,
    ]);

    $subject = Subject::create([
        'name' => 'Fisika & Kimia',
        'code' => 'FSK-12',
    ]);

    $courseClass = CourseClass::create([
        'class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($teacherUser)->post(route('lms.quizzes.store', $courseClass->id), [
        'title' => 'Asesmen Sumatif Multi-Tipe',
        'quiz_type' => 'formatif',
        'passing_score' => 75,
        'creation_method' => 'manual',
        'duration_minutes' => 90,
        'instructions' => 'Kerjakan dengan teliti seluruh butir soal multi-tipe.',
        'question_types' => [
            'multiple_choice',
            'complex_multiple_choice',
            'true_false',
            'matching',
            'short_answer',
            'essay',
        ],
        'count_multiple_choice' => 10,
        'count_complex_multiple_choice' => 5,
        'count_true_false' => 5,
        'count_matching' => 5,
        'count_short_answer' => 5,
        'count_essay' => 5,
        'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
    ]);

    $quiz = LmsQuiz::where('title', 'Asesmen Sumatif Multi-Tipe')->first();
    expect($quiz)->not->toBeNull();
    expect($quiz->questions()->count())->toBe(35);

    // Cek bahwa butir soal multi-tipe terbentuk dan siap diedit
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'multiple_choice']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'complex_mc']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'true_false']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'matching']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'short_answer']);
    $this->assertDatabaseHas('lms_quiz_questions', ['quiz_id' => $quiz->id, 'question_type' => 'essay']);

    // Redirects immediately to quiz show / question form
    $response->assertRedirect(route('lms.quizzes.show', [$courseClass->id, $quiz->id]));
});

test('2. Teacher can update registered questions and answer keys', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Biologi',
        'nip' => '19870101'.fake()->unique()->numerify('######'),
    ]);

    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'ganjil',
        'is_active' => true,
    ]);

    $schoolClass = SchoolClass::create([
        'name' => 'XI IPA 2',
        'grade' => '11',
        'academic_year_id' => $academicYear->id,
    ]);

    $subject = Subject::create([
        'name' => 'Biologi',
        'code' => 'BIO-11',
    ]);

    $courseClass = CourseClass::create([
        'class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $quiz = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Kuis Sel Tumbuhan',
        'duration_minutes' => 45,
        'is_published' => true,
    ]);

    $question = LmsQuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_type' => 'multiple_choice',
        'question_text' => 'Soal Awal: Organel fotosintesis adalah...',
        'options' => ['A' => 'Mitokondria', 'B' => 'Kloroplas', 'C' => 'Ribosom', 'D' => 'Vakuola'],
        'correct_answer' => 'A', // Salah dulu
        'score_weight' => 2,
    ]);

    // Update soal dan kunci jawaban yang benar
    $updateResponse = $this->actingAs($teacherUser)->put(
        route('lms.quizzes.questions.update', [$courseClass->id, $quiz->id, $question->id]),
        [
            'question_type' => 'multiple_choice',
            'question_text' => 'Soal Diperbarui: Organel utama fotosintesis pada tumbuhan adalah...',
            'stimulus' => 'Perhatikan struktur plastida pada klorofil',
            'weight' => 5,
            'option_a' => 'Mitokondria',
            'option_b' => 'Kloroplas',
            'option_c' => 'Ribosom',
            'option_d' => 'Badan Golgi',
            'option_e' => 'Retikulum Endoplasma',
            'correct_answer' => 'B', // Kunci diperbaiki ke B
        ]
    );

    $updateResponse->assertRedirect();
    $updateResponse->assertSessionHas('success');

    $question->refresh();
    expect($question->question_text)->toContain('Organel utama fotosintesis');
    expect($question->correct_answer)->toBe('B');
    expect($question->score_weight)->toBe(5);
    expect($question->options)->toHaveKey('E');
});

test('3. Rich editor endpoints for image upload and Quran ayah retrieval function accurately', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru PAI & Kimia',
        'nip' => '19890101'.fake()->unique()->numerify('######'),
    ]);

    Storage::fake('public');

    // 3a. Upload Image
    $imageFile = UploadedFile::fake()->image('diagram_kimia.png', 600, 400);
    $uploadResponse = $this->actingAs($teacherUser)->post(route('lms.editor.upload_image'), [
        'image' => $imageFile,
    ]);

    $uploadResponse->assertOk();
    $uploadResponse->assertJsonStructure(['success', 'url', 'filename']);
    $uploadData = $uploadResponse->json();
    expect($uploadData['success'])->toBeTrue();
    expect($uploadData['url'])->toContain('/storage/lms/editor/');

    // 3b. Quran Ayah API
    $quranResponse = $this->actingAs($teacherUser)->get(route('lms.quran.ayah', [
        'surah' => 1,
        'ayah' => 2,
    ]));

    $quranResponse->assertOk();
    $quranData = $quranResponse->json();
    expect($quranData['success'])->toBeTrue();
    expect($quranData['data']['arabic'])->toContain('الْحَمْدُ لِلَّهِ');
    expect($quranData['data']['translation'])->toContain('Segala puji bagi Allah');
});

test('4. renderRichText correctly renders images, Quran ayah quotes, and symbols', function () {
    $sampleText = "Soal PAI: Bacalah ayat berikut:\n[AYAH: 1:1]\nReaksi: H2O -> H+ + OH-";
    $rendered = LmsQuizQuestion::renderRichText($sampleText);

    expect($rendered)->toContain('بِسْمِ اللَّهِ');
    expect($rendered)->toContain('quran-quote-box');
});

test('5. Assignment show page provides both Lihat and Download for file and text submissions', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Matematika & Fisika',
        'nip' => '19820101'.fake()->unique()->numerify('######'),
    ]);

    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'ganjil',
        'is_active' => true,
    ]);

    $schoolClass = SchoolClass::create([
        'name' => 'XII MIPA 3',
        'grade' => '12',
        'academic_year_id' => $academicYear->id,
    ]);

    $subject = Subject::create([
        'name' => 'Fisika Gelombang',
        'code' => 'FSK-GEL',
    ]);

    $courseClass = CourseClass::create([
        'class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'school_class_id' => $schoolClass->id,
        'name' => 'Ahmad Santoso',
        'nis' => '12345',
        'nisn' => '008'.fake()->unique()->numerify('#######'),
        'gender' => 'L',
    ]);

    $assignment = LmsAssignment::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Tugas Praktikum Gelombang Elektromagnetik',
        'instructions' => 'Hitunglah panjang gelombang dari $\lambda = c / f$ dengan konstanta $c = 3 \times 10^8 m/s$.',
        'submission_type' => 'both',
        'due_date' => now()->addDays(5),
        'max_score' => 100,
    ]);

    // Siswa submit berkas dan teks
    $submission = LmsAssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'file_path' => 'lms/assignments/laporan_gelombang.pdf',
        'file_name' => 'laporan_gelombang.pdf',
        'submission_text' => 'Ini adalah penjelasan analisis gelombang elektromagnetik dari hasil pengamatan.',
        'submitted_at' => now(),
    ]);

    // Akses halaman tugas oleh guru
    $response = $this->actingAs($teacherUser)->get(route('lms.assignments.show', [$courseClass->id, $assignment->id]));
    $response->assertOk();

    // Verifikasi ada tombol "Lihat" dan "Download" untuk berkas
    $response->assertSee('previewSubmissionFile', false);
    $response->assertSee('Lihat');
    $response->assertSee('Download');

    // Verifikasi ada tombol "Lihat Teks" dan "Download (.txt)" untuk jawaban teks
    $response->assertSee('previewSubmissionText', false);
    $response->assertSee('Lihat Teks');
    $response->assertSee('Download (.txt)');

    // Verifikasi modal preview berkas dan modal preview teks tersedia
    $response->assertSee('modal-preview-submission-file');
    $response->assertSee('modal-preview-submission-text');

    // Verifikasi rich editor modals tersedia
    $response->assertSee('modal-rich-image-picker');
    $response->assertSee('modal-rich-symbol-palette');
    $response->assertSee('modal-rich-arabic-keyboard');
    $response->assertSee('modal-rich-quran-picker');
});

test('6. CBT question options, keys, and cards render rich math, chemistry, arabic and image markup unescaped', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Sains & Agama',
        'nip' => '19890101'.fake()->unique()->numerify('######'),
    ]);

    $academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'ganjil',
        'is_active' => true,
    ]);

    $schoolClass = SchoolClass::create([
        'name' => 'XII MIPA 1',
        'grade' => '12',
        'academic_year_id' => $academicYear->id,
    ]);

    $subject = Subject::create([
        'name' => 'Sains Terpadu',
        'code' => 'SAINS-12',
    ]);

    $courseClass = CourseClass::create([
        'class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $quiz = LmsQuiz::create([
        'course_class_id' => $courseClass->id,
        'title' => 'Asesmen Sains & Matematika Lanjut',
        'duration_minutes' => 60,
        'is_published' => true,
    ]);

    // Buat soal pilihan ganda dengan rumus matematika pada opsi
    $question1 = LmsQuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_type' => 'multiple_choice',
        'question_text' => 'Berapakah nilai dari integral $\int 2x dx$?',
        'options' => [
            'A' => 'x² + C',
            'B' => '2x² + C',
            'C' => '½ x² + C',
            'D' => 'x³ + C',
            'E' => 'x + C',
        ],
        'correct_answer' => 'A',
        'pembahasan' => 'Gunakan rumus dasar integral: $\int a x^n dx = (a/(n+1)) x^{n+1} + C$.',
        'score_weight' => 20,
        'order_number' => 1,
    ]);

    // Buat soal menjodohkan dengan rumus kimia
    $question2 = LmsQuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_type' => 'matching',
        'question_text' => 'Jodohkan nama senyawa dengan rumus kimianya:',
        'options' => [
            'premises' => ['1' => 'Asam Sulfat', '2' => 'Natrium Hidroksida'],
            'choices' => ['1' => 'H₂SO₄', '2' => 'NaOH'],
        ],
        'correct_answer' => json_encode(['1' => '1', '2' => '2']),
        'score_weight' => 20,
        'order_number' => 2,
    ]);

    // Buat soal markdown image
    $question3 = LmsQuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_type' => 'short_answer',
        'question_text' => "Perhatikan gambar berikut:\n\n![Diagram Sel](http://sissma1le.test:8080/storage/lms/editor/cell.png)\n\nApakah nama organel X?",
        'correct_answer' => 'Mitokondria',
        'score_weight' => 20,
        'order_number' => 3,
    ]);

    // 1. Tampilan Show oleh Guru
    $showResp = $this->actingAs($teacherUser)->get(route('lms.quizzes.show', [$courseClass->id, $quiz->id]));
    $showResp->assertOk();
    $showResp->assertSee('x² + C', false);
    $showResp->assertSee('½ x² + C', false);
    $showResp->assertSee('H₂SO₄', false);
    $showResp->assertSee('NaOH', false);
    $showResp->assertSee('http://sissma1le.test:8080/storage/lms/editor/cell.png', false);

    // 2. Tampilan Kartu Soal (Cetak)
    $kartuResp = $this->actingAs($teacherUser)->get(route('lms.quizzes.kartu_soal', [$courseClass->id, $quiz->id]));
    $kartuResp->assertOk();
    $kartuResp->assertSee('x² + C', false);
    $kartuResp->assertSee('H₂SO₄', false);
    $kartuResp->assertSee('http://sissma1le.test:8080/storage/lms/editor/cell.png', false);
    // Posisi Kepala Sekolah harus di kiri
    $kartuResp->assertSee('Kepala Sekolah');
    $setting = SchoolSetting::first();
    if ($setting && $setting->headmaster_name) {
        $kartuResp->assertSee($setting->headmaster_name);
    }

    // 3. Tampilan CBT Siswa (Take)
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'school_class_id' => $schoolClass->id,
        'name' => 'Siswa CBT',
        'nis' => '99887',
        'nisn' => '008'.fake()->unique()->numerify('#######'),
        'gender' => 'L',
    ]);

    $takeResp = $this->actingAs($studentUser)->get(route('lms.quizzes.take', [$courseClass->id, $quiz->id]));
    $takeResp->assertOk();
    $takeResp->assertSee('x² + C', false);
    $takeResp->assertSee('H₂SO₄', false);
    $takeResp->assertSee('http://sissma1le.test:8080/storage/lms/editor/cell.png', false);
});

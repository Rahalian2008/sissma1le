<?php

namespace Tests\Feature;

use App\Models\CbtExam;
use App\Models\CbtExamQuestion;
use App\Models\CbtSession;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\CbtAiService;
use App\Services\CbtGeofenceService;
use App\Services\CbtGradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CbtExamTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $teacherUser;

    protected Teacher $teacher;

    protected User $studentUser;

    protected Student $student;

    protected SchoolClass $schoolClass;

    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolClass = SchoolClass::create([
            'name' => 'X-1',
            'grade' => '10',
            'major' => 'Umum',
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->teacherUser = User::factory()->create([
            'name' => 'Guru Pengampu',
            'role' => 'guru',
        ]);
        $this->teacher = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'name' => 'Guru Pengampu',
            'nip' => '198501012010011001',
            'is_active' => true,
        ]);

        $this->studentUser = User::factory()->create([
            'name' => 'Ahmad Siswa',
            'role' => 'siswa',
        ]);
        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'name' => 'Ahmad Siswa',
            'nis' => '20261001',
            'nisn' => '0081234567',
            'class_id' => $this->schoolClass->id,
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'name' => 'Matematika',
            'code' => 'MTK-X',
            'is_active' => true,
        ]);

        // Setup GPS sekolah
        SchoolSetting::set('school_lat', -7.130133);
        SchoolSetting::set('school_lng', 106.703826);
        SchoolSetting::set('geofence_radius_meters', 100);
    }

    public function test_admin_can_access_cbt_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('cbt.index'));

        $response->assertStatus(200);
        $response->assertSee('Aplikasi Ujian Online');
    }

    public function test_teacher_can_create_new_cbt_exam_with_security_and_geofence(): void
    {
        $payload = [
            'title' => 'Asesmen Akhir Semester Matematika',
            'exam_code' => 'AAS-MTK-01',
            'exam_type' => 'AAS',
            'token' => 'MTK26A',
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'duration_minutes' => 90,
            'kkm' => 75,
            'status' => 'published',
            'class_ids' => [$this->schoolClass->id],
            'enable_security_block' => '1',
            'max_violations' => 3,
            'enable_geofence' => '1',
            'randomize_questions' => '1',
            'show_result_to_student' => '1',
        ];

        $response = $this->actingAs($this->teacherUser)->post(route('cbt.exams.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('cbt_exams', [
            'exam_code' => 'AAS-MTK-01',
            'token' => 'MTK26A',
            'enable_security_block' => true,
            'enable_geofence' => true,
        ]);

        $exam = CbtExam::where('exam_code', 'AAS-MTK-01')->first();
        $this->assertTrue($exam->classes->contains($this->schoolClass->id));
    }

    public function test_manual_question_creation_and_auto_grading_for_all_types(): void
    {
        $exam = CbtExam::create([
            'title' => 'Ujian Diagnostik',
            'exam_code' => 'DIAG-01',
            'subject_id' => $this->subject->id,
            'duration_minutes' => 60,
            'status' => 'published',
        ]);

        // 1. Pilihan Ganda (Single Choice)
        $q1 = CbtExamQuestion::create([
            'cbt_exam_id' => $exam->id,
            'question_number' => 1,
            'question_type' => 'multiple_choice',
            'question_text' => 'Berapakah 5 + 7?',
            'options' => [
                ['key' => 'A', 'text' => '10'],
                ['key' => 'B', 'text' => '12'],
                ['key' => 'C', 'text' => '14'],
            ],
            'correct_answer' => 'B',
            'score_weight' => 10,
        ]);

        // 2. Benar / Salah (True False)
        $q2 = CbtExamQuestion::create([
            'cbt_exam_id' => $exam->id,
            'question_number' => 2,
            'question_type' => 'true_false',
            'question_text' => 'Tentukan benar/salah:',
            'options' => [
                ['statement' => 'Matahari terbit di timur'],
                ['statement' => '2 adalah bilangan ganjil'],
            ],
            'correct_answer' => [
                '0' => 'true',
                '1' => 'false',
            ],
            'score_weight' => 10,
        ]);

        $this->assertEquals(2, $exam->questions()->count());

        // Test Grading Service
        $session = CbtSession::create([
            'cbt_exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'status' => 'in_progress',
        ]);

        $gradingService = app(CbtGradingService::class);

        // Jawaban Benar Soal 1
        $ans1 = $session->answers()->create([
            'cbt_exam_question_id' => $q1->id,
            'answer' => 'B',
        ]);
        $gradingService->gradeAnswer($ans1);
        $ans1->refresh();
        $this->assertTrue((bool) $ans1->is_correct);
        $this->assertEquals(10, $ans1->score_earned);

        // Jawaban Benar Soal 2
        $ans2 = $session->answers()->create([
            'cbt_exam_question_id' => $q2->id,
            'answer' => ['0' => 'true', '1' => 'false'],
        ]);
        $gradingService->gradeAnswer($ans2);
        $ans2->refresh();
        $this->assertTrue((bool) $ans2->is_correct);

        // Recalculate Session
        $gradingService->recalculateSession($session);
        $session->refresh();
        $this->assertEquals(100.0, (float) $session->final_grade);
    }

    public function test_ai_question_generation_fallback(): void
    {
        $aiService = app(CbtAiService::class);
        $questions = $aiService->generateQuestions([
            'subject' => 'Matematika',
            'topic' => 'Aljabar dan Fungsi Linear',
            'count' => 3,
            'question_types' => ['multiple_choice', 'true_false', 'short_answer'],
            'cognitive_focus' => 'Campuran (HOTS)',
        ]);

        $this->assertIsArray($questions);
        $this->assertNotEmpty($questions);
        $this->assertArrayHasKey('question_text', $questions[0]);
    }

    public function test_student_portal_and_token_validation(): void
    {
        $exam = CbtExam::create([
            'title' => 'Ujian Ber-Token',
            'exam_code' => 'TOK-01',
            'token' => 'PASS99',
            'subject_id' => $this->subject->id,
            'duration_minutes' => 60,
            'status' => 'published',
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addMinutes(120),
        ]);
        $exam->classes()->attach($this->schoolClass->id);

        // Akses portal
        $response = $this->actingAs($this->studentUser)->get(route('cbt.student.index'));
        $response->assertStatus(200);
        $response->assertSee('Ujian Ber-Token');

        // Start dengan token salah -> Ditolak
        $failResponse = $this->actingAs($this->studentUser)->post(route('cbt.student.start', $exam->id), [
            'token' => 'SALAH1',
        ]);
        $failResponse->assertSessionHas('error');

        // Start dengan token benar -> Diterima
        $successResponse = $this->actingAs($this->studentUser)->post(route('cbt.student.start', $exam->id), [
            'token' => 'PASS99',
            'latitude' => -7.130133,
            'longitude' => 106.703826,
        ]);
        $successResponse->assertRedirect(route('cbt.student.take', $exam->id));
        $this->assertDatabaseHas('cbt_sessions', [
            'cbt_exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_geofence_service_validation(): void
    {
        $exam = CbtExam::create([
            'title' => 'Ujian Geofence',
            'exam_code' => 'GEO-01',
            'enable_geofence' => true,
            'school_latitude' => -7.130133,
            'school_longitude' => 106.703826,
            'geofence_radius_meters' => 100,
        ]);

        $geofence = app(CbtGeofenceService::class);

        // Dalam radius sekolah (~10 meter)
        $inside = $geofence->verifyLocation($exam, -7.130150, 106.703850);
        $this->assertTrue($inside['is_allowed']);

        // Di luar radius sekolah (Jakarta ~100 km)
        $outside = $geofence->verifyLocation($exam, -6.200000, 106.816666);
        $this->assertFalse($outside['is_allowed']);
    }

    public function test_anti_cheat_violation_and_auto_block(): void
    {
        $exam = CbtExam::create([
            'title' => 'Ujian Anti-Cheat',
            'exam_code' => 'CHEAT-01',
            'enable_security_block' => true,
            'max_violations' => 2,
            'duration_minutes' => 45,
            'status' => 'published',
        ]);

        $session = CbtSession::create([
            'cbt_exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'status' => 'in_progress',
            'violation_count' => 0,
        ]);

        // Pelanggaran 1: Peringatan
        $resp1 = $this->actingAs($this->studentUser)->postJson(route('cbt.student.report_violation', $exam->id), [
            'type' => 'tab_switch',
            'description' => 'Pindah ke Google Chrome tab lain',
        ]);
        $resp1->assertJson([
            'success' => true,
            'violation_count' => 1,
            'is_blocked' => false,
        ]);

        // Pelanggaran 2: Mencapai batas max -> Auto Block
        $resp2 = $this->actingAs($this->studentUser)->postJson(route('cbt.student.report_violation', $exam->id), [
            'type' => 'tab_switch',
            'description' => 'Minimize window',
        ]);
        $resp2->assertJson([
            'success' => true,
            'violation_count' => 2,
            'is_blocked' => true,
        ]);

        $session->refresh();
        $this->assertTrue((bool) $session->is_blocked);
        $this->assertEquals('blocked', $session->status);

        // Guru / Pengawas Membuka Blokir
        $unblockResp = $this->actingAs($this->teacherUser)->post(route('cbt.exams.unblock', [$exam->id, $session->id]));
        $unblockResp->assertSessionHas('success');

        $session->refresh();
        $this->assertFalse((bool) $session->is_blocked);
        $this->assertEquals('in_progress', $session->status);
    }

    public function test_official_documents_generation(): void
    {
        $exam = CbtExam::create([
            'title' => 'Penilaian Sumatif Akhir',
            'exam_code' => 'PSA-01',
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'duration_minutes' => 90,
            'status' => 'published',
            'kkm' => 75,
        ]);
        $exam->classes()->attach($this->schoolClass->id);

        CbtExamQuestion::create([
            'cbt_exam_id' => $exam->id,
            'question_number' => 1,
            'question_type' => 'multiple_choice',
            'question_text' => 'Soal Dokumen 1',
            'options' => [['key' => 'A', 'text' => 'Opsi A']],
            'correct_answer' => 'A',
            'cognitive_level' => 'L2',
            'score_weight' => 10,
        ]);

        // 1. Kisi-kisi
        $respKisi = $this->actingAs($this->adminUser)->get(route('cbt.documents.kisi_kisi', $exam->id));
        $respKisi->assertStatus(200);
        $respKisi->assertSee('KISI-KISI PENULISAN SOAL ASESMEN');

        // 2. Kartu Soal
        $respKartu = $this->actingAs($this->adminUser)->get(route('cbt.documents.kartu_soal', $exam->id));
        $respKartu->assertStatus(200);
        $respKartu->assertSee('KARTU SOAL NOMOR');

        // 3. Kartu Peserta (Dengan kredensial login CBT)
        $respPeserta = $this->actingAs($this->adminUser)->get(route('cbt.documents.kartu_peserta', $exam->id));
        $respPeserta->assertStatus(200);
        $respPeserta->assertSee('KARTU PESERTA ASESMEN UJIAN ONLINE');
        $respPeserta->assertSee($this->student->name);

        // 4. Daftar Hadir
        $respHadir = $this->actingAs($this->adminUser)->get(route('cbt.documents.daftar_hadir', $exam->id));
        $respHadir->assertStatus(200);
        $respHadir->assertSee('DAFTAR HADIR PESERTA ASESMEN');

        // 5. Berita Acara
        $respBap = $this->actingAs($this->adminUser)->get(route('cbt.documents.berita_acara', $exam->id));
        $respBap->assertStatus(200);
        $respBap->assertSee('BERITA ACARA PELAKSANAAN');
    }
}

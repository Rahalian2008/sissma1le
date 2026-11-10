<?php

namespace Tests\Feature;

use App\Models\CourseClass;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\LibraryVisitor;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryGuestBookReportsAndLmsRealGradeTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $teacherUser;

    protected Teacher $teacher;

    protected SchoolClass $schoolClass;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->schoolClass = SchoolClass::create([
            'name' => 'X-1',
            'grade' => 10,
            'academic_year' => '2026/2027',
        ]);

        $this->teacherUser = User::factory()->create([
            'role' => 'guru',
        ]);

        $this->teacher = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'name' => 'Budi Santoso, S.Pd.',
            'nip' => '198501012010011001',
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'class_id' => $this->schoolClass->id,
            'name' => 'Ahmad Siswa',
            'nisn' => '0012345678',
            'nis' => '12345',
            'gender' => 'L',
            'is_active' => true,
        ]);
    }

    public function test_public_guest_book_page_can_be_accessed_without_login(): void
    {
        $response = $this->get('/perpustakaan/buku-tamu');

        $response->assertStatus(200);
        $response->assertSeeText('Buku Tamu Perpustakaan');
        $response->assertSeeText('Kiosk Buku Tamu Mandiri');
        $response->assertSeeText('Log Pengunjung Hari Ini');
        $response->assertDontSee('kop_dark');
    }

    public function test_guest_book_lookup_returns_student_and_teacher_data(): void
    {
        // Lookup Siswa
        $studentLookup = $this->getJson('/perpustakaan/buku-tamu/lookup?type=siswa&code=0012345678');
        $studentLookup->assertStatus(200)
            ->assertJson([
                'found' => true,
                'name' => 'Ahmad Siswa',
                'visitor_type' => 'siswa',
            ]);

        // Lookup Guru
        $teacherLookup = $this->getJson('/perpustakaan/buku-tamu/lookup?type=guru&code=198501012010011001');
        $teacherLookup->assertStatus(200)
            ->assertJson([
                'found' => true,
                'name' => 'Budi Santoso, S.Pd.',
                'visitor_type' => 'guru',
            ]);

        // Lookup Not Found
        $notFoundLookup = $this->getJson('/perpustakaan/buku-tamu/lookup?type=siswa&code=9999999999');
        $notFoundLookup->assertStatus(200)
            ->assertJson([
                'found' => false,
            ]);
    }

    public function test_guest_book_submission_records_visitor_and_returns_success(): void
    {
        $payload = [
            'visitor_type' => 'siswa',
            'identity_number' => '0012345678',
            'name' => 'Ahmad Siswa',
            'class_or_institution' => 'Kelas X-1',
            'purpose' => 'Membaca Buku',
            'satisfaction_rating' => 'Sangat Nyaman',
            'notes' => 'Mencari buku biologi sel.',
        ];

        $response = $this->postJson('/perpustakaan/buku-tamu', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('library_visitors', [
            'identity_number' => '0012345678',
            'name' => 'Ahmad Siswa',
            'purpose' => 'Membaca Buku',
            'visitor_type' => 'siswa',
            'student_id' => $this->student->id,
        ]);
    }

    public function test_library_member_can_be_updated_and_deleted_with_active_loan_prevention(): void
    {
        $category = LibraryCategory::create([
            'name' => 'Ilmu Pengetahuan',
            'ddc_code' => '500',
        ]);

        $book = LibraryBook::create([
            'category_id' => $category->id,
            'book_code' => 'BK-500-001',
            'title' => 'Fisika Dasar',
            'author' => 'Prof. Halliday',
            'total_copies' => 5,
            'available_copies' => 4,
            'is_active' => true,
        ]);

        $member = LibraryMember::create([
            'member_code' => 'MBR-S-001',
            'member_type' => 'siswa',
            'student_id' => $this->student->id,
            'name' => 'Ahmad Siswa',
            'identity_number' => '0012345678',
            'joined_at' => now(),
            'is_active' => true,
        ]);

        // 1. Update Member
        $updateResponse = $this->actingAs($this->adminUser)->put("/library/members/{$member->id}", [
            'member_type' => 'siswa',
            'name' => 'Ahmad Siswa Updated',
            'identity_number' => '0012345678',
            'phone' => '081234567890',
            'email' => 'ahmad@example.com',
            'address' => 'Lengkong Sukabumi',
            'is_active' => '1',
            'notes' => 'Anggota teladan',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('library_members', [
            'id' => $member->id,
            'name' => 'Ahmad Siswa Updated',
            'phone' => '081234567890',
        ]);

        // 2. Active Loan Prevention on Delete
        $loan = LibraryLoan::create([
            'loan_code' => 'PINJAM-001',
            'book_id' => $book->id,
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'DIPINJAM',
        ]);

        $deleteAttempt = $this->actingAs($this->adminUser)->delete("/library/members/{$member->id}");
        $deleteAttempt->assertSessionHas('error');
        $this->assertDatabaseHas('library_members', ['id' => $member->id]);

        // 3. Delete succeeds after book returned
        $loan->update(['status' => 'KEMBALI', 'return_date' => now()->toDateString()]);

        $deleteSuccess = $this->actingAs($this->adminUser)->delete("/library/members/{$member->id}");
        $deleteSuccess->assertSessionHas('success');
        $this->assertDatabaseMissing('library_members', ['id' => $member->id]);
    }

    public function test_visitor_and_loans_custom_reports_and_print_views(): void
    {
        // Create visitor & loan
        LibraryVisitor::create([
            'visitor_type' => 'siswa',
            'student_id' => $this->student->id,
            'name' => 'Ahmad Siswa',
            'identity_number' => '0012345678',
            'purpose' => 'Membaca Buku',
            'visited_at' => now(),
        ]);

        $category = LibraryCategory::create(['name' => 'Kimia', 'ddc_code' => '540']);
        $book = LibraryBook::create([
            'category_id' => $category->id,
            'book_code' => 'BK-540-001',
            'title' => 'Kimia Organik',
            'author' => 'Dr. Sutrisno',
            'total_copies' => 2,
            'available_copies' => 1,
            'is_active' => true,
        ]);

        $member = LibraryMember::create([
            'member_code' => 'MBR-S-002',
            'member_type' => 'siswa',
            'name' => 'Ahmad Siswa',
            'joined_at' => now(),
            'is_active' => true,
        ]);

        LibraryLoan::create([
            'loan_code' => 'PINJAM-002',
            'book_id' => $book->id,
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'DIPINJAM',
        ]);

        // 1. Visitor Report & Print
        $visitorReport = $this->actingAs($this->adminUser)->get('/library/reports/visitors?period=daily');
        $visitorReport->assertStatus(200)->assertSeeText('Laporan Buku Tamu Pengunjung');

        $visitorPrint = $this->actingAs($this->adminUser)->get('/library/reports/visitors/print?period=daily');
        $visitorPrint->assertStatus(200)
            ->assertSeeText('LAPORAN BUKU TAMU PENGUNJUNG PERPUSTAKAAN')
            ->assertSeeText('Kepala Sekolah')
            ->assertSeeText('Pengelola / Kepala Perpustakaan');

        // 2. Loans Report & Print
        $loansReport = $this->actingAs($this->adminUser)->get('/library/reports/loans?period=monthly');
        $loansReport->assertStatus(200)->assertSeeText('Laporan Sirkulasi Buku');

        $loansPrint = $this->actingAs($this->adminUser)->get('/library/reports/loans/print?period=monthly');
        $loansPrint->assertStatus(200)
            ->assertSeeText('LAPORAN SIRKULASI PEMINJAMAN & PENGEMBALIAN BUKU')
            ->assertSeeText('Kepala Sekolah')
            ->assertSeeText('Pengelola / Kepala Perpustakaan');
    }

    public function test_lms_gradebook_realism_null_when_not_done_and_accurate_weights_when_completed(): void
    {
        $subject = Subject::create([
            'name' => 'Biologi',
            'code' => 'BIO-10',
            'kkm' => 75,
        ]);

        $courseClass = CourseClass::create([
            'subject_id' => $subject->id,
            'class_id' => $this->schoolClass->id,
            'teacher_id' => $this->teacher->id,
            'weight_material' => 20,
            'weight_assignment' => 30,
            'weight_quiz' => 40,
            'weight_attendance' => 10,
        ]);

        // Siswa 2 (mengerjakan hingga selesai)
        $student2 = Student::create([
            'class_id' => $this->schoolClass->id,
            'name' => 'Bambang Siswa',
            'nisn' => '0098765432',
            'nis' => '54321',
            'gender' => 'L',
            'is_active' => true,
        ]);

        $assignment = LmsAssignment::create([
            'course_class_id' => $courseClass->id,
            'title' => 'Tugas 1 Biologi Sel',
            'instructions' => 'Kerjakan tugas biologi dengan teliti.',
            'due_date' => now()->addDays(3),
            'is_published' => true,
        ]);

        $quiz = LmsQuiz::create([
            'course_class_id' => $courseClass->id,
            'title' => 'Kuis 1 Biologi Sel',
            'duration_minutes' => 60,
            'is_published' => true,
        ]);

        // Siswa 2 mengumpulkan tugas dan kuis
        LmsAssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student2->id,
            'submitted_at' => now(),
            'score' => 80.0,
            'graded_at' => now(),
        ]);

        LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student2->id,
            'started_at' => now()->subMinutes(30),
            'completed_at' => now(),
            'score' => 90.0,
        ]);

        // Siswa 1 (Ahmad) TIDAK mengerjakan tugas dan TIDAK mengerjakan kuis

        $response = $this->actingAs($this->teacherUser)->get("/lms/courses/{$courseClass->id}/grades");
        $response->assertStatus(200);

        // Siswa 1 yang belum mengerjakan tidak boleh mendapatkan nilai 0 palsu / predikat palsu
        $response->assertSeeText('Ahmad Siswa');
        $response->assertSeeText('Belum Selesai');

        // Siswa 2 yang selesai mengerjakan harus memiliki nilai
        $response->assertSeeText('Bambang Siswa');
        // Rerata tugas Bambang: 80, Rerata kuis: 90
        $response->assertSeeText('Rerata: 80');
        $response->assertSeeText('Rerata: 90');
    }
}

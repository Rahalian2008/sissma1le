<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Attendance;
use App\Models\BkCounselingSession;
use App\Models\Disposition;
use App\Models\EarlyLeaveRequest;
use App\Models\Employee;
use App\Models\Good;
use App\Models\Guest;
use App\Models\IncomingLetter;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\OutgoingLetter;
use App\Models\PicketSchedule;
use App\Models\SchoolBell;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\Sppd;
use App\Models\Student;
use App\Models\StudentLegalization;
use App\Models\User;
use App\Models\WorshipAttendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveQueryExceptionPreventionTest extends TestCase
{
    use RefreshDatabase;

    public function test_library_book_saves_safely_with_null_slug_and_pages_count(): void
    {
        $book = LibraryBook::create([
            'book_code' => 'BOOK-TEST-001',
            'title' => 'Buku Uji Coba Keamanan Database',
            'author' => 'Penulis Uji Coba',
            'pages_count' => null,
            'slug' => null,
            'book_type' => 'fisik',
            'total_copies' => 3,
            'available_copies' => 3,
        ]);

        $this->assertNotNull($book->id);
        $this->assertEquals(0, $book->pages_count);
        $this->assertNotEmpty($book->slug);
    }

    public function test_library_loan_creates_safely_with_auto_generated_fields(): void
    {
        $user = User::factory()->create();
        $book = LibraryBook::create([
            'book_code' => 'BOOK-TEST-002',
            'title' => 'Buku Pinjaman',
            'author' => 'Penulis',
            'pages_count' => 100,
        ]);
        $member = LibraryMember::create([
            'member_code' => 'MEM-001',
            'name' => 'Siswa Penguji',
            'user_id' => $user->id,
            'joined_at' => now(),
        ]);

        $loan = LibraryLoan::create([
            'book_id' => $book->id,
            'member_id' => $member->id,
            'user_id' => $user->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ]);

        $this->assertNotNull($loan->id);
        $this->assertNotEmpty($loan->loan_code);
        $this->assertEquals('Siswa Penguji', $loan->borrower_name);
        $this->assertEquals('MEM-001', $loan->borrower_identifier);
    }

    public function test_early_leave_request_and_school_exit_permit_auto_generate_numbers(): void
    {
        $class = SchoolClass::create(['name' => 'XII-MIPA-1', 'grade' => 'XII']);
        $student = Student::create([
            'name' => 'Siswa Izin',
            'nis' => '12345',
            'nisn' => '54321',
            'class_id' => $class->id,
        ]);

        $leave = EarlyLeaveRequest::create([
            'student_id' => $student->id,
            'reason' => 'Sakit demam',
        ]);

        $this->assertNotNull($leave->id);
        $this->assertNotEmpty($leave->letter_number);
        $this->assertEquals('PENDING', $leave->status);

        $permit = SchoolExitPermit::create([
            'purpose' => 'Lomba Akademik',
            'reason' => 'Perwakilan sekolah',
        ]);

        $this->assertNotNull($permit->id);
        $this->assertNotEmpty($permit->permit_number);
        $this->assertEquals('PENDING', $permit->status);
    }

    public function test_guest_and_student_legalization_auto_populate_ticket_and_receipt(): void
    {
        $guest = Guest::create([
            'name' => 'Tamu Dinas',
        ]);

        $this->assertNotNull($guest->id);
        $this->assertNotEmpty($guest->ticket_number);
        $this->assertEquals('Tata Usaha', $guest->destination_department);

        $legalization = StudentLegalization::create([
            'applicant_name' => 'Alumni Sukses',
        ]);

        $this->assertNotNull($legalization->id);
        $this->assertNotEmpty($legalization->receipt_number);
        $this->assertEquals('Ijazah', $legalization->document_type);
    }

    public function test_picket_schedule_and_school_bell_create_safely(): void
    {
        $picket = PicketSchedule::create([
            'day_name' => 'Selasa',
            'person_name' => 'Guru Piket Pagi',
        ]);

        $this->assertNotNull($picket->id);
        $this->assertEquals(2, $picket->day_number);

        $bell = SchoolBell::create([
            'title' => 'Bel Istirahat',
            'time' => '10:00',
        ]);

        $this->assertNotNull($bell->id);
        $this->assertEquals('chime', $bell->sound_type);
        $this->assertTrue($bell->is_active);
    }

    public function test_attendance_and_worship_auto_backfill_student_info(): void
    {
        $class = SchoolClass::create(['name' => 'XI-F-1', 'grade' => 'XI']);
        $student = Student::create([
            'name' => 'Siswa Rajin',
            'nis' => '99001',
            'nisn' => '88001',
            'class_id' => $class->id,
        ]);

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'HADIR',
        ]);

        $this->assertNotNull($attendance->id);
        $this->assertEquals('99001', $attendance->nis);
        $this->assertEquals('Siswa Rajin', $attendance->student_name);
        $this->assertEquals($class->id, $attendance->class_id);

        $worship = WorshipAttendance::create([
            'student_id' => $student->id,
            'activity_type' => 'DHUHA',
            'date' => now()->toDateString(),
        ]);

        $this->assertNotNull($worship->id);
        $this->assertEquals($class->id, $worship->class_id);
        $this->assertEquals('HADIR', $worship->status);
    }

    public function test_letters_sppd_and_disposition_auto_generate_numbers(): void
    {
        $incoming = IncomingLetter::create([
            'sender' => 'Kantor Cabang Dinas',
            'subject' => 'Undangan Rapat Koordinasi',
        ]);

        $this->assertNotNull($incoming->id);
        $this->assertNotEmpty($incoming->agenda_number);

        $outgoing = OutgoingLetter::create([
            'recipient' => 'Kepala Cabang Dinas',
            'subject' => 'Laporan Bulanan',
        ]);

        $this->assertNotNull($outgoing->id);
        $this->assertNotEmpty($outgoing->agenda_number);
        $this->assertNotEmpty($outgoing->letter_number);

        $disp = Disposition::create([
            'incoming_letter_id' => $incoming->id,
            'instructions' => 'Hadir mewakili kepala sekolah',
        ]);

        $this->assertNotNull($disp->id);
        $this->assertEquals('Waka / Staf Terkait', $disp->target_role_or_user);

        $employee = Employee::create(['name' => 'Pegawai Dinas']);
        $sppd = Sppd::create([
            'employee_id' => $employee->id,
            'assignment_purpose' => 'Mengikuti Pelatihan Kurikulum',
        ]);

        $this->assertNotNull($sppd->id);
        $this->assertNotEmpty($sppd->sppd_number);
    }

    public function test_goods_assets_and_bk_sessions_create_safely(): void
    {
        $good = Good::create(['name' => 'Spidol Whiteboard']);
        $this->assertNotNull($good->id);
        $this->assertNotEmpty($good->code);

        $asset = Asset::create(['name' => 'Proyektor LCD Epson']);
        $this->assertNotNull($asset->id);
        $this->assertNotEmpty($asset->code);

        $student = Student::create(['name' => 'Siswa Konseling', 'nis' => '7788', 'nisn' => '8877']);
        $bk = BkCounselingSession::create([
            'student_id' => $student->id,
            'topic' => 'Bimbingan Karier Perguruan Tinggi',
        ]);
        $this->assertNotNull($bk->id);
        $this->assertNotEmpty($bk->session_code);
    }
}

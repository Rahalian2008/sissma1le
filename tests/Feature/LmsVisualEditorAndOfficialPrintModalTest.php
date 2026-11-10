<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsVisualEditorAndOfficialPrintModalTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $teacherUser;

    protected Teacher $teacher;

    protected SchoolClass $schoolClass;

    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        SchoolSetting::set('school_name', 'SMAN 1 LENGKONG');
        SchoolSetting::set('headmaster_name', 'Drs. H. Sukardi, M.Pd.');
        SchoolSetting::set('headmaster_nip', '19680512 199403 1 004');

        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'name' => 'Super Administrator',
            'username' => 'superadmin',
        ]);

        $this->teacherUser = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Mata Pelajaran',
            'username' => 'gurumapel',
        ]);

        $this->teacher = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'nip' => '198501012010011001',
            'name' => 'Guru Mata Pelajaran',
            'gender' => 'L',
            'is_active' => true,
        ]);

        $this->schoolClass = SchoolClass::create([
            'name' => 'XII MIPA 1',
            'grade' => '12',
            'major' => 'MIPA',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'name' => 'Fisika',
            'code' => 'FSK-XII',
            'is_active' => true,
        ]);
    }

    /**
     * Test 1: LMS Index page renders properly without undefined variable $currentTeacher error
     */
    public function test_lms_index_page_loads_safely_without_current_teacher_error(): void
    {
        // Akses sebagai Super Admin
        $response = $this->actingAs($this->superAdmin)->get(route('lms.index'));
        $response->assertStatus(200);
        $response->assertSee('Learning Management System (LMS)');

        // Akses sebagai Guru
        $responseGuru = $this->actingAs($this->teacherUser)->get(route('lms.index'));
        $responseGuru->assertStatus(200);
        $responseGuru->assertSee('Learning Management System (LMS)');
    }

    /**
     * Test 2: User Activity Report runs without SQL column 'teacher_id' not found error
     */
    public function test_user_activity_report_loads_safely_without_teacher_id_sql_error(): void
    {
        // 1. Filter default
        $response = $this->actingAs($this->superAdmin)->get(route('reports.user_activity'));
        $response->assertStatus(200);
        $response->assertSeeText('Laporan Aktivitas & Frekuensi Akses Pengguna');

        // 2. Filter khusus Guru
        $responseGuru = $this->actingAs($this->superAdmin)->get(route('reports.user_activity', ['role' => 'guru']));
        $responseGuru->assertStatus(200);

        // 3. Filter khusus Tenaga Administrasi
        $responseStaff = $this->actingAs($this->superAdmin)->get(route('reports.user_activity', ['role' => 'administrasi']));
        $responseStaff->assertStatus(200);

        // 4. Cetak Resmi Rekapitulasi (Kepala Sekolah WAJIB di Kiri)
        $responsePrint = $this->actingAs($this->superAdmin)->get(route('reports.print_user_activity', ['role' => 'all']));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSeeText('REKAPITULASI FREKUENSI AKSES & AKTIVITAS PENGGUNA APLIKASI');
        $responsePrint->assertSee('Kepala Sekolah');
        $responsePrint->assertSee('Drs. H. Sukardi, M.Pd.');
    }

    /**
     * Test 3: Worship & Extracurricular official print modals and print endpoints
     */
    public function test_worship_and_extracurricular_official_print_modals_and_templates(): void
    {
        // 1. Ekstrakurikuler Index & Report
        $extra = Extracurricular::create([
            'name' => 'Paskibra',
            'code' => 'PASKIBRA',
            'category' => 'Kepemimpinan',
            'day' => 'Jumat',
            'is_active' => true,
        ]);

        $resExtraIndex = $this->actingAs($this->superAdmin)->get(route('extracurricular.index', ['extra_id' => $extra->id]));
        $resExtraIndex->assertStatus(200);
        $resExtraIndex->assertSee('openPrintExtracurricularModal');
        $resExtraIndex->assertSee('printExtracurricularModal');

        $resExtraReport = $this->actingAs($this->superAdmin)->get(route('extracurricular.report', ['extra_id' => $extra->id]));
        $resExtraReport->assertStatus(200);
        $resExtraReport->assertSee('printExtracurricularModal');

        // Cetak Rekap Resmi Ekstrakurikuler (Kepala Sekolah WAJIB di Kiri)
        $resExtraPrint = $this->actingAs($this->superAdmin)->get(route('extracurricular.print', [
            'extra_id' => $extra->id,
            'period_type' => 'daily',
            'date' => Carbon::today()->toDateString(),
        ]));
        $resExtraPrint->assertStatus(200);
        $resExtraPrint->assertSee('REKAPITULASI PRESENSI EKSTRAKURIKULER');
        $resExtraPrint->assertSee('Kepala Sekolah');

        // 2. Presensi Ibadah (Dhuha, Dzuhur, Keputrian)
        foreach (['dhuha', 'dzuhur', 'keputrian'] as $act) {
            $resWorshipIndex = $this->actingAs($this->superAdmin)->get(route('worship.index', $act));
            $resWorshipIndex->assertStatus(200);
            $resWorshipIndex->assertSee('openPrintWorshipModal');
            $resWorshipIndex->assertSee('printWorshipModal');

            $resWorshipReport = $this->actingAs($this->superAdmin)->get(route('worship.report', $act));
            $resWorshipReport->assertStatus(200);
            $resWorshipReport->assertSee('printWorshipModal');

            $resWorshipPrint = $this->actingAs($this->superAdmin)->get(route('worship.print', [
                'activity' => $act,
                'period_type' => 'daily',
                'date' => Carbon::today()->toDateString(),
            ]));
            $resWorshipPrint->assertStatus(200);
            $resWorshipPrint->assertSee('Kepala Sekolah');
        }
    }

    /**
     * Test 4: Role Kepala Administrasi in quick switch, role assignment, and master teachers
     */
    public function test_kepala_administrasi_role_is_fully_integrated(): void
    {
        // Siapkan akun untuk Kepala Administrasi
        $ktuUser = User::factory()->create([
            'name' => 'Kepala Tata Usaha',
            'username' => 'kepala_tu',
            'role' => 'kepala_administrasi',
        ]);

        // 1. Quick Switch ke kepala_administrasi
        $switchRes = $this->actingAs($this->superAdmin)->post(route('quick.switch'), [
            'role' => 'kepala_administrasi',
        ]);
        $switchRes->assertRedirect();
        $this->assertEquals('kepala_administrasi', session('active_role'));

        // 2. Master Data Pendidik memuat role Kepala Administrasi
        $resMaster = $this->actingAs($this->superAdmin)->get(route('master.teachers'));
        $resMaster->assertStatus(200);
        $resMaster->assertSee('Kepala Administrasi (Kepala TU)');
    }

    /**
     * Test 5: Library cataloging guidelines modal and label/barcode printing
     */
    public function test_library_guidelines_modal_and_label_printing(): void
    {
        $cat = LibraryCategory::create([
            'name' => 'Ilmu Murni & Sains',
            'ddc_code' => '500',
            'is_active' => true,
        ]);

        $book = LibraryBook::create([
            'category_id' => $cat->id,
            'title' => 'Fisika Kuantum Modern',
            'author' => 'Albert Einstein',
            'publisher' => 'Pustaka Sains',
            'publication_year' => 2024,
            'isbn' => '978-602-001-2',
            'book_code' => '530-EIN-f-c1',
            'call_number' => "530\nEIN\nf",
            'total_copies' => 5,
            'available_copies' => 5,
            'is_digital_flip' => true,
            'is_active' => true,
        ]);

        // 1. Index Buku memuat Panduan dan Cetak Label
        $resIndex = $this->actingAs($this->superAdmin)->get(route('library.books.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('modalPanduanKatalogisasi');
        $resIndex->assertSeeText('Cetak Label & Barcode');

        // 2. Cetak Lembar Label & Barcode (A4 & F4)
        $resPrint = $this->actingAs($this->superAdmin)->get(route('library.books.print_labels', ['paper' => 'a4']));
        $resPrint->assertStatus(200);
        $resPrint->assertSee('SMAN 1 LENGKONG');
        $resPrint->assertSee('530-EIN-f-c1');

        // 3. E-Book Flipbook Reader 3D
        $resFlip = $this->actingAs($this->superAdmin)->get(route('library.books.flipbook', $book));
        $resFlip->assertStatus(200);
        $resFlip->assertSee('Flip HTML 3D');
        $resFlip->assertSee('Fisika Kuantum Modern');
    }
}

<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\AttendanceLocation;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Database\Seeders\SchoolMasterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefinedSystemFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $studentUser;

    protected Student $student;

    protected SchoolClass $schoolClass;

    protected AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SchoolMasterSeeder::class);

        $this->adminUser = User::where('role', 'admin')->first();
        $this->studentUser = User::where('role', 'siswa')->first();
        $this->student = Student::first();
        $this->schoolClass = SchoolClass::first();
        $this->academicYear = AcademicYear::where('is_active', true)->first();
    }

    /**
     * Test 1: Setting Pilihan Metode Absen & Laporan Presensi Real-Time
     */
    public function test_attendance_report_loads_successfully_with_filters(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('attendance.report'));
        $response->assertOk();
        $response->assertSee('Laporan Presensi');
        $response->assertSee('Rekapitulasi');

        // Test with live search query
        $searchResponse = $this->actingAs($this->adminUser)->get(route('attendance.report', ['search' => $this->student->name]));
        $searchResponse->assertOk();
        $searchResponse->assertSee($this->student->name);
    }

    public function test_disabled_attendance_method_is_blocked_by_attendance_service(): void
    {
        // Disable selfie attendance in settings
        SchoolSetting::set('enable_attendance_selfie', '0');

        $location = AttendanceLocation::primary();
        $service = app(AttendanceService::class);

        $result = $service->processSelfieAttendance(
            $this->student,
            [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'selfie_image' => 'data:image/jpeg;base64,placeholder',
            ]
        );

        $this->assertFalse($result['success']);
        $this->assertEquals('METHOD_DISABLED', $result['error_code']);
    }

    /**
     * Test 2 & 3: Pemisahan Tata Tertib (Rangking & Laporan) dan Karakter (Diagram & Persentase)
     */
    public function test_discipline_ranking_leaderboard_loads_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('discipline.ranking'));
        $response->assertOk();
        $response->assertSee('Papan Peringkat Siswa Berprestasi');
        $response->assertSee('TATA TERTIB & PRESTASI', false);
    }

    public function test_discipline_report_loads_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('discipline.report'));
        $response->assertOk();
        $response->assertSee('Rekapitulasi Prestasi & Pelanggaran Siswa', false);
        $response->assertSee('LAPORAN TATA TERTIB');
    }

    public function test_habits_charts_diagram_and_percentages_load_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('habits.charts'));
        $response->assertOk();
        $response->assertSee('Diagram & Persentase Karakter Hebat', false);
        $response->assertSee('KARAKTER & 7 KEBIASAAN', false);
        $response->assertSee('Peringkat Konsistensi Siswa dalam 7 Kebiasaan');
    }

    /**
     * Test 4: Master Data CRUD (Sekolah, Kelas, Siswa, Guru & Role, Tahun Pelajaran, Butir)
     */
    public function test_master_school_profile_can_be_viewed_and_updated(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('master.school_profile'));
        $response->assertOk();
        $response->assertSee('Identitas Resmi SMA Negeri 1 Lengkong');

        $updateResponse = $this->actingAs($this->adminUser)->post(route('master.school_profile.update'), [
            'school_name' => 'SMA Negeri 1 Lengkong Terpadu',
            'school_npsn' => '20539999',
            'school_nss' => '301051408999',
            'school_accreditation' => 'A (Unggul)',
            'headmaster_name' => 'Drs. H. Bambang Subagyo, M.Pd.',
            'headmaster_nip' => '196803121994121002',
            'school_address' => 'Jl. Pendidikan No. 100 Lengkong',
            'school_phone' => '(0358) 559999',
            'school_email' => 'admin@sman1lengkong.sch.id',
            'school_website' => 'https://sman1lengkong.sch.id',
        ]);

        $updateResponse->assertRedirect();
        $this->assertEquals('SMA Negeri 1 Lengkong Terpadu', SchoolSetting::get('school_name'));
        $this->assertEquals('20539999', SchoolSetting::get('school_npsn'));
    }

    public function test_master_classes_crud_operations(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('master.classes'));
        $response->assertOk();

        // Create
        $storeResponse = $this->actingAs($this->adminUser)->post(route('master.classes.store'), [
            'name' => 'X-TEST',
            'grade' => 'X',
            'major' => 'MIPA',
            'academic_year_id' => $this->academicYear->id,
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('school_classes', ['name' => 'X-TEST']);

        $class = SchoolClass::where('name', 'X-TEST')->first();

        // Update
        $updateResponse = $this->actingAs($this->adminUser)->put(route('master.classes.update', $class->id), [
            'name' => 'X-TEST-UPDATED',
            'grade' => 'X',
            'major' => 'MIPA Unggulan',
            'academic_year_id' => $this->academicYear->id,
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('school_classes', ['name' => 'X-TEST-UPDATED']);

        // Delete
        $deleteResponse = $this->actingAs($this->adminUser)->delete(route('master.classes.destroy', $class->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('school_classes', ['name' => 'X-TEST-UPDATED']);
    }

    public function test_master_students_crud_and_rfid_assignment(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('master.students'));
        $response->assertOk();

        // Create student with RFID UID
        $storeResponse = $this->actingAs($this->adminUser)->post(route('master.students.store'), [
            'nis' => '99991',
            'nisn' => '0099999991',
            'name' => 'Budi Sanjaya Test',
            'gender' => 'L',
            'class_id' => $this->schoolClass->id,
            'academic_year_id' => $this->academicYear->id,
            'rfid_uid' => 'CARD-TEST-999',
            'phone' => '08129999999',
            'address' => 'Lengkong Lor',
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('students', ['nis' => '99991', 'rfid_uid' => 'CARD-TEST-999']);

        $student = Student::where('nis', '99991')->first();

        // Update
        $updateResponse = $this->actingAs($this->adminUser)->put(route('master.students.update', $student->id), [
            'nis' => '99991',
            'nisn' => '0099999991',
            'name' => 'Budi Sanjaya Updated',
            'gender' => 'L',
            'class_id' => $this->schoolClass->id,
            'academic_year_id' => $this->academicYear->id,
            'rfid_uid' => 'CARD-TEST-MODIFIED',
            'phone' => '08129999999',
            'address' => 'Lengkong Kidul',
            'is_active' => true,
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('students', ['rfid_uid' => 'CARD-TEST-MODIFIED']);
    }

    public function test_master_teachers_crud_and_login_role_assignment(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('master.teachers'));
        $response->assertOk();

        // Create teacher with guru role
        $storeResponse = $this->actingAs($this->adminUser)->post(route('master.teachers.store'), [
            'nip' => '198801012015011009',
            'name' => 'Siti Rahmawati, S.Pd.',
            'gender' => 'P',
            'title' => 'Guru Bimbingan Konseling',
            'phone' => '085200001111',
            'role' => 'guru',
            'email' => 'siti.bk@sman1lengkong.sch.id',
            'username' => 'siti_bk',
            'password' => 'password123',
        ]);
        $storeResponse->assertRedirect();

        $teacher = Teacher::where('nip', '198801012015011009')->first();
        $this->assertNotNull($teacher);
        $this->assertEquals('guru', $teacher->user->role);

        // Update role to wali_kelas
        $updateResponse = $this->actingAs($this->adminUser)->put(route('master.teachers.update', $teacher->id), [
            'nip' => '198801012015011009',
            'name' => 'Siti Rahmawati, M.Pd.',
            'gender' => 'P',
            'title' => 'Wali Kelas X-1',
            'phone' => '085200001111',
            'role' => 'wali_kelas',
            'email' => 'siti.bk@sman1lengkong.sch.id',
            'is_active' => true,
        ]);
        $updateResponse->assertRedirect();

        $teacher->refresh();
        $this->assertEquals('wali_kelas', $teacher->user->role);
    }

    public function test_master_academic_years_can_be_managed_and_activated(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('master.academic_years'));
        $response->assertOk();

        // Add
        $storeResponse = $this->actingAs($this->adminUser)->post(route('master.academic_years.store'), [
            'name' => '2027/2028',
            'semester' => 'Ganjil',
            'start_date' => '2027-07-15',
            'end_date' => '2027-12-20',
            'is_active' => false,
        ]);
        $storeResponse->assertRedirect();

        $ay = AcademicYear::where('name', '2027/2028')->first();
        $this->assertNotNull($ay);
        $this->assertFalse($ay->is_active);

        // Activate
        $activeResponse = $this->actingAs($this->adminUser)->post(route('master.academic_years.set_active', $ay->id));
        $activeResponse->assertRedirect();

        $ay->refresh();
        $this->assertTrue($ay->is_active);
    }

    public function test_master_rulebook_categories_edit_and_add(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('master.categories'));
        $response->assertOk();

        $achCat = AchievementCategory::first();

        // Add Achievement Item
        $addAch = $this->actingAs($this->adminUser)->post(route('master.achievement_item.store'), [
            'category_id' => $achCat->id,
            'code' => 'R-TEST-1',
            'name' => 'Juara 1 Lomba Coding Siswa',
            'default_points' => 50,
            'level' => 'PROVINSI',
        ]);
        $addAch->assertRedirect();
        $this->assertDatabaseHas('achievement_items', ['code' => 'R-TEST-1']);

        $achItem = AchievementItem::where('code', 'R-TEST-1')->first();

        // Update Achievement Item
        $updateAch = $this->actingAs($this->adminUser)->put(route('master.achievement_item.update', $achItem->id), [
            'category_id' => $achCat->id,
            'code' => 'R-TEST-1-REV',
            'name' => 'Juara 1 Lomba Coding Nasional',
            'default_points' => 75,
            'level' => 'NASIONAL',
        ]);
        $updateAch->assertRedirect();
        $this->assertDatabaseHas('achievement_items', ['code' => 'R-TEST-1-REV', 'default_points' => 75]);

        // Delete Achievement Item
        $delAch = $this->actingAs($this->adminUser)->delete(route('master.achievement_item.destroy', $achItem->id));
        $delAch->assertRedirect();
        $this->assertDatabaseMissing('achievement_items', ['code' => 'R-TEST-1-REV']);
    }

    /**
     * Test 5: Dashboard Realtime Presensi Diagram & Rangking Prestasi Widgets
     */
    public function test_admin_dashboard_shows_realtime_presensi_and_rankings(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('dashboard'));
        $response->assertOk();

        // Realtime Presensi Widget assertions
        $response->assertSee('Realtime Presensi Siswa Hari Ini');
        $response->assertSee('Diagram Proporsi Kehadiran Hari Ini');
        $response->assertSee('Hadir');
        $response->assertSee('Izin');
        $response->assertSee('Sakit');
        $response->assertSee('Alpa');

        // Realtime Prestasi & Pelanggaran Widget assertions
        $response->assertSee('Rangking Siswa Berprestasi');
        $response->assertSee('Rekapitulasi Tata Tertib');
        $response->assertSee('Total Prestasi');
        $response->assertSee('Total Pelanggaran');
    }

    /**
     * Test 6: User Profile view and updates via both PUT and POST methods
     */
    public function test_user_profile_can_be_viewed_and_updated_via_both_put_and_post(): void
    {
        // 1. View profile
        $viewResponse = $this->actingAs($this->adminUser)->get(route('profile'));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Perbarui Informasi Profil');
        $viewResponse->assertSee($this->adminUser->name);

        // 2. Update via POST (form submission fallback)
        $postResponse = $this->actingAs($this->adminUser)->post(route('profile.update'), [
            'name' => 'Administrator Baru SMAN 1',
            'phone' => '081234567890',
        ]);
        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success', 'Profil Anda berhasil diperbarui.');
        $this->assertEquals('Administrator Baru SMAN 1', $this->adminUser->fresh()->name);
        $this->assertEquals('081234567890', $this->adminUser->fresh()->phone);

        // 3. Update via PUT (standard REST method spoofing)
        $putResponse = $this->actingAs($this->adminUser)->put(route('profile.update'), [
            'name' => 'Administrator Lengkong Updated',
            'phone' => '089876543210',
        ]);
        $putResponse->assertRedirect();
        $putResponse->assertSessionHas('success', 'Profil Anda berhasil diperbarui.');
        $this->assertEquals('Administrator Lengkong Updated', $this->adminUser->fresh()->name);
        $this->assertEquals('089876543210', $this->adminUser->fresh()->phone);
    }

    /**
     * Test 7: Identitas Pokok Bentuk Pendidikan & KOP Surat Dinamis
     */
    public function test_dynamic_kop_surat_and_school_profile_with_education_form(): void
    {
        $updateResponse = $this->actingAs($this->adminUser)->post(route('master.school_profile.update'), [
            'school_name' => 'SMA Negeri 1 Lengkong Modern',
            'school_npsn' => '20539876',
            'school_nss' => '301051408888',
            'school_accreditation' => 'A (Unggul Paripurna)',
            'school_education_form' => 'Sekolah Menengah Kejuruan (SMK)',
            'headmaster_name' => 'Drs. H. Bambang Subagyo, M.Pd.',
            'headmaster_nip' => '196803121994121002',
            'school_address' => 'Jl. Lengkong No. 88',
            'school_phone' => '(0358) 551234',
            'school_email' => 'info@sman1lengkong.sch.id',
            'school_website' => 'https://sman1lengkong.sch.id',
        ]);

        $updateResponse->assertRedirect();
        $this->assertEquals('Sekolah Menengah Kejuruan (SMK)', SchoolSetting::get('school_education_form'));
        $this->assertEquals('20539876', SchoolSetting::get('school_npsn'));
        $this->assertEquals('301051408888', SchoolSetting::get('school_nss'));
        $this->assertEquals('A (Unggul Paripurna)', SchoolSetting::get('school_accreditation'));

        // Test Print View reflects dynamic NPSN & NSS
        $printResponse = $this->actingAs($this->adminUser)->get(route('reports.print.attendance'));
        $printResponse->assertOk();
        $printResponse->assertSee('20539876');
        $printResponse->assertSee('301051408888');
        $printResponse->assertSee('A (Unggul Paripurna)');
    }

    /**
     * Test 8: Presensi 2 Sesi (Masuk & Pulang) dan Otomatis VALID
     */
    public function test_selfie_attendance_has_checkin_and_checkout_with_valid_status(): void
    {
        $location = AttendanceLocation::primary();
        $service = app(AttendanceService::class);

        // 1. Sesi Masuk (Senin Pagi)
        Carbon::setTestNow(Carbon::parse('2026-09-21 06:45:00'));
        $checkinResult = $service->processSelfieAttendance($this->student, [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'selfie_image' => 'data:image/jpeg;base64,'.base64_encode('dummy_image_in'),
            'session_type' => 'checkin',
        ]);

        $this->assertTrue($checkinResult['success']);
        $this->assertEquals('VALID', $checkinResult['verification_status']);
        $this->assertEquals('checkin', $checkinResult['session']);
        $this->assertNotNull($checkinResult['attendance']->time);

        // 2. Sesi Pulang (Senin Sore Jam Pulang)
        Carbon::setTestNow(Carbon::parse('2026-09-21 15:30:00'));
        $checkoutResult = $service->processSelfieAttendance($this->student, [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'selfie_image' => 'data:image/jpeg;base64,'.base64_encode('dummy_image_out'),
            'session_type' => 'checkout',
        ]);

        $this->assertTrue($checkoutResult['success']);
        $this->assertEquals('VALID', $checkoutResult['verification_status']);
        $this->assertEquals('checkout', $checkoutResult['session']);
        $this->assertNotNull($checkoutResult['attendance']->time_out);
        $this->assertEquals('SELFIE', $checkoutResult['attendance']->out_method);

        Carbon::setTestNow();
    }

    /**
     * Test 9: Riwayat Presensi dengan Filter Kelas & Tampilan Lengkap
     */
    public function test_attendance_history_loads_with_class_filter(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('attendance.history', ['class_id' => $this->schoolClass->id]));
        $response->assertOk();
        $response->assertSee('Riwayat Presensi Per Kelas');
        $response->assertSee('Pilih Kelas');
        $response->assertSee($this->schoolClass->name);
    }

    /**
     * Test 10: Role Switcher Khusus Super Admin & Proteksi Keamanan
     */
    public function test_super_admin_role_switcher_access_and_restriction(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        if (! $superAdmin) {
            $superAdmin = User::create([
                'name' => 'Super Administrator',
                'username' => 'superadmin_test',
                'email' => 'superadmin_test@sman1lengkong.sch.id',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]);
        }

        // 1. Super Admin melihat role switcher di dashboard
        $dashboardResponse = $this->actingAs($superAdmin)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Peralihan Peran Pengguna (Khusus Super Admin)');

        // 2. Super Admin dapat beralih peran ke siswa
        $switchResponse = $this->actingAs($superAdmin)->post(route('quick.switch'), ['role' => 'siswa']);
        $switchResponse->assertRedirect(route('dashboard'));
        $switchResponse->assertSessionHas('switched_from_super_admin', true);
        $this->assertEquals('siswa', auth()->user()->role);

        // 3. Pada mode simulasi, dapat beralih kembali ke super_admin
        $switchBack = $this->post(route('quick.switch'), ['role' => 'super_admin']);
        $switchBack->assertRedirect(route('dashboard'));
        $this->assertEquals('super_admin', auth()->user()->role);

        // 4. Akun Siswa biasa tidak melihat widget role switcher pada dashboard mereka
        $studentUser = User::where('role', 'siswa')->first();
        $studentDashboard = $this->actingAs($studentUser)->get(route('dashboard'));
        $studentDashboard->assertOk();
        $studentDashboard->assertDontSee('Peralihan Peran Pengguna (Khusus Super Admin)');
        $this->assertEquals('siswa', auth()->user()->role);
    }
}

<?php

namespace Tests\Feature;

use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\SchoolMasterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingAndLogoKopTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $superAdminUser;

    protected User $teacherUser;

    protected User $studentUser;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SchoolMasterSeeder::class);

        $this->adminUser = User::where('role', 'admin')->first();
        $this->superAdminUser = User::where('role', 'super_admin')->first();
        $this->teacherUser = User::where('role', 'guru')->first();
        $this->studentUser = User::where('role', 'siswa')->first();
        $this->student = Student::first();
    }

    /**
     * Hak akses halaman branding terbatas untuk Admin dan Super Admin.
     */
    public function test_branding_page_access_restricted_to_admin_and_super_admin(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('master.branding'));
        $response->assertOk();
        $response->assertSee('Logo Aplikasi');
        $response->assertSee('Default (1LE)');

        $responseSuper = $this->actingAs($this->superAdminUser)->get(route('master.branding'));
        $responseSuper->assertOk();

        $responseGuru = $this->actingAs($this->teacherUser)->get(route('master.branding'));
        $responseGuru->assertForbidden();

        $responseSiswa = $this->actingAs($this->studentUser)->get(route('master.branding'));
        $responseSiswa->assertForbidden();
    }

    /**
     * Upload dan reset logo aplikasi berhasil, menggantikan badge 1LE di navbar/sidebar.
     */
    public function test_admin_can_upload_and_delete_app_logo(): void
    {
        Storage::fake('public');

        // Pastikan default sebelum upload menampilkan 1LE
        $resBefore = $this->actingAs($this->adminUser)->get(route('dashboard'));
        $resBefore->assertOk();
        $resBefore->assertSee('1LE');

        // Upload Logo (Valid 1x1 PNG binary without GD dependency)
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $logoFile = UploadedFile::fake()->createWithContent('custom_school_logo.png', $pngContent);

        $uploadResponse = $this->actingAs($this->adminUser)->post(route('master.branding.logo.update'), [
            'logo' => $logoFile,
        ]);

        $uploadResponse->assertRedirect();
        $uploadResponse->assertSessionHas('success');

        $savedLogoPath = SchoolSetting::get('school_logo');
        $this->assertNotNull($savedLogoPath);
        Storage::disk('public')->assertExists($savedLogoPath);

        // Akses dashboard: harus menampilkan logo URL
        $logoUrl = Storage::disk('public')->url($savedLogoPath);
        $resAfter = $this->actingAs($this->adminUser)->get(route('dashboard'));
        $resAfter->assertOk();
        $resAfter->assertSee($logoUrl);

        // Akses halaman login sebagai tamu: harus menampilkan logo kustom
        auth()->logout();
        $resLogin = $this->get(route('login'));
        $resLogin->assertOk();
        $resLogin->assertSee($logoUrl);

        // Re-authenticate untuk aksi selanjutnya
        $this->actingAs($this->adminUser);

        // Reset / Hapus Logo
        $deleteResponse = $this->actingAs($this->adminUser)->delete(route('master.branding.logo.destroy'));
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');

        $this->assertNull(SchoolSetting::get('school_logo'));
        Storage::disk('public')->assertMissing($savedLogoPath);

        // Dashboard kembali menampilkan 1LE
        $resReset = $this->actingAs($this->adminUser)->get(route('dashboard'));
        $resReset->assertOk();
        $resReset->assertSee('1LE');
    }

    /**
     * Upload dan reset KOP Surat resmi sekolah berhasil diterapkan ke seluruh dokumen cetak.
     */
    public function test_admin_can_upload_and_delete_kop_surat(): void
    {
        Storage::fake('public');

        // Upload KOP Surat (Valid 1x1 PNG binary without GD dependency)
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $kopFile = UploadedFile::fake()->createWithContent('kop_surat_resmi.png', $pngContent);

        $uploadResponse = $this->actingAs($this->adminUser)->post(route('master.branding.kop.update'), [
            'kop' => $kopFile,
        ]);

        $uploadResponse->assertRedirect();
        $uploadResponse->assertSessionHas('success');

        $savedKopPath = SchoolSetting::get('school_kop');
        $this->assertNotNull($savedKopPath);
        Storage::disk('public')->assertExists($savedKopPath);

        $kopUrl = Storage::disk('public')->url($savedKopPath);

        // Cetak Laporan Terpadu: Harus menampilkan gambar KOP
        $resCombined = $this->actingAs($this->adminUser)->get(route('reports.print.combined'));
        $resCombined->assertOk();
        $resCombined->assertSee($kopUrl);
        $resCombined->assertSee('kop-image-wrapper');

        // Cetak Presensi: Harus menampilkan gambar KOP
        $resAtt = $this->actingAs($this->adminUser)->get(route('reports.print.attendance'));
        $resAtt->assertOk();
        $resAtt->assertSee($kopUrl);

        // Cetak Tata Tertib: Harus menampilkan gambar KOP
        $resDisc = $this->actingAs($this->adminUser)->get(route('reports.print.discipline'));
        $resDisc->assertOk();
        $resDisc->assertSee($kopUrl);

        // Cetak 7 Kebiasaan: Harus menampilkan gambar KOP
        $resHabits = $this->actingAs($this->adminUser)->get(route('reports.print.habits'));
        $resHabits->assertOk();
        $resHabits->assertSee($kopUrl);

        // Cetak Dokumen Siswa: Harus menampilkan gambar KOP
        $resStudent = $this->actingAs($this->adminUser)->get(route('reports.student.print', $this->student));
        $resStudent->assertOk();
        $resStudent->assertSee($kopUrl);

        // Reset / Hapus KOP
        $deleteResponse = $this->actingAs($this->adminUser)->delete(route('master.branding.kop.destroy'));
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');

        $this->assertNull(SchoolSetting::get('school_kop'));
        Storage::disk('public')->assertMissing($savedKopPath);

        // Kembali ke teks standar Jawa Barat
        $resFallback = $this->actingAs($this->adminUser)->get(route('reports.print.combined'));
        $resFallback->assertOk();
        $resFallback->assertSee('PEMERINTAH PROVINSI JAWA BARAT');
    }

    /**
     * Pastikan semua berkas cetak menggunakan PROVINSI JAWA BARAT dan TIDAK ADA PROVINSI JAWA TIMUR.
     */
    public function test_all_print_views_use_provinsi_jawa_barat_not_jawa_timur(): void
    {
        // 1. Dokumen Terpadu (Presensi, Tatib, Karakter)
        $resCombined = $this->actingAs($this->adminUser)->get(route('reports.print.combined'));
        $resCombined->assertOk();
        $resCombined->assertSee('PEMERINTAH PROVINSI JAWA BARAT');
        $resCombined->assertDontSee('PEMERINTAH PROVINSI JAWA TIMUR');

        // 2. Berkas Perkembangan Siswa Terpadu
        $resStudent = $this->actingAs($this->adminUser)->get(route('reports.student.print', $this->student));
        $resStudent->assertOk();
        $resStudent->assertSee('PEMERINTAH PROVINSI JAWA BARAT');
        $resStudent->assertDontSee('PEMERINTAH PROVINSI JAWA TIMUR');

        // 3. Cetak Presensi
        $resAtt = $this->actingAs($this->adminUser)->get(route('reports.print.attendance'));
        $resAtt->assertOk();
        $resAtt->assertSee('PEMERINTAH PROVINSI JAWA BARAT');
        $resAtt->assertDontSee('PEMERINTAH PROVINSI JAWA TIMUR');

        // 4. Cetak Tata Tertib
        $resDisc = $this->actingAs($this->adminUser)->get(route('reports.print.discipline'));
        $resDisc->assertOk();
        $resDisc->assertSee('PEMERINTAH PROVINSI JAWA BARAT');
        $resDisc->assertDontSee('PEMERINTAH PROVINSI JAWA TIMUR');

        // 5. Cetak 7 Kebiasaan
        $resHabits = $this->actingAs($this->adminUser)->get(route('reports.print.habits'));
        $resHabits->assertOk();
        $resHabits->assertSee('PEMERINTAH PROVINSI JAWA BARAT');
        $resHabits->assertDontSee('PEMERINTAH PROVINSI JAWA TIMUR');
    }

    /**
     * Admin dapat mengubah nama aplikasi dan tagline, terefleksi di dashboard & login.
     */
    public function test_admin_can_update_app_name_and_tagline(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('master.branding.app_name.update'), [
            'app_name' => 'PORTAL AKADEMIK SMAN 1 LENGKONG',
            'app_tagline' => 'Sistem Informasi Sekolah Modern & Terpadu',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('PORTAL AKADEMIK SMAN 1 LENGKONG', SchoolSetting::get('app_name'));
        $this->assertEquals('Sistem Informasi Sekolah Modern & Terpadu', SchoolSetting::get('app_tagline'));

        // Cek dashboard
        $resDash = $this->actingAs($this->adminUser)->get(route('dashboard'));
        $resDash->assertOk();
        $resDash->assertSee('PORTAL AKADEMIK SMAN 1 LENGKONG');

        // Cek halaman login
        auth()->logout();
        $resLogin = $this->get(route('login'));
        $resLogin->assertOk();
        $resLogin->assertSee('PORTAL AKADEMIK SMAN 1 LENGKONG');
        $resLogin->assertSee('Sistem Informasi Sekolah Modern & Terpadu');
    }

    /**
     * Semua dokumen cetak resmi menggunakan Cabang Dinas Wilayah V, Sukabumi, dan email/website resmi.
     */
    public function test_all_print_views_use_wilayah_v_sukabumi_and_no_nganjuk(): void
    {
        // 1. Presensi
        $resAtt = $this->actingAs($this->adminUser)->get(route('reports.print.attendance'));
        $resAtt->assertOk();
        $resAtt->assertSee('CABANG DINAS PENDIDIKAN WILAYAH V');
        $resAtt->assertSee('Sukabumi');
        $resAtt->assertSee('sman1lengkong@gmail.com');
        $resAtt->assertDontSee('WILAYAH VIII');
        $resAtt->assertDontSee('Nganjuk');

        // 2. Tata Tertib
        $resDisc = $this->actingAs($this->adminUser)->get(route('reports.print.discipline'));
        $resDisc->assertOk();
        $resDisc->assertSee('CABANG DINAS PENDIDIKAN WILAYAH V');
        $resDisc->assertSee('Sukabumi');
        $resDisc->assertSee('sman1lengkong@gmail.com');
        $resDisc->assertDontSee('WILAYAH VIII');
        $resDisc->assertDontSee('Nganjuk');

        // 3. Dokumen Terpadu
        $resComb = $this->actingAs($this->adminUser)->get(route('reports.print.combined'));
        $resComb->assertOk();
        $resComb->assertSee('CABANG DINAS PENDIDIKAN WILAYAH V');
        $resComb->assertSee('Sukabumi');
        $resComb->assertDontSee('Nganjuk');

        // 4. Berkas Kebiasaan
        $resHab = $this->actingAs($this->adminUser)->get(route('reports.print.habits'));
        $resHab->assertOk();
        $resHab->assertSee('CABANG DINAS PENDIDIKAN WILAYAH V');
        $resHab->assertSee('Sukabumi');
        $resHab->assertDontSee('WILAYAH VIII');
        $resHab->assertDontSee('Nganjuk');

        // 5. Berkas Siswa
        $resStud = $this->actingAs($this->adminUser)->get(route('reports.student.print', $this->student));
        $resStud->assertOk();
        $resStud->assertSee('CABANG DINAS PENDIDIKAN WILAYAH V');
        $resStud->assertSee('Sukabumi');
        $resStud->assertDontSee('CABANG DINAS PENDIDIKAN WILAYAH VIII');
    }

    /**
     * Cetak presensi dan tata tertib mendukung filter periode harian, mingguan, dan bulanan.
     */
    public function test_print_reports_support_period_filters(): void
    {
        // Daily
        $resDaily = $this->actingAs($this->adminUser)->get(route('reports.print.attendance', [
            'period_type' => 'daily',
            'date' => '2026-09-19',
        ]));
        $resDaily->assertOk();
        $resDaily->assertSee('Laporan Harian');

        // Weekly
        $resWeekly = $this->actingAs($this->adminUser)->get(route('reports.print.attendance', [
            'period_type' => 'weekly',
        ]));
        $resWeekly->assertOk();
        $resWeekly->assertSee('Laporan Mingguan');

        // Monthly
        $resMonthly = $this->actingAs($this->adminUser)->get(route('reports.print.discipline', [
            'period_type' => 'monthly',
            'month' => 9,
            'year' => 2026,
        ]));
        $resMonthly->assertOk();
        $resMonthly->assertSee('Laporan Bulanan');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->adminUser = User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_templates_can_be_downloaded_for_all_modules(): void
    {
        $templates = [
            'master.students.template',
            'master.teachers.template',
            'master.classes.template',
            'master.academic_years.template',
            'master.achievement_item.template',
            'master.violation_item.template',
            'achievements.template',
            'violations.template',
            'guidance.template',
        ];

        foreach ($templates as $route) {
            $response = $this->actingAs($this->adminUser)->get(route($route));
            $response->assertOk();
            $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        }
    }

    protected function createTestCsvUpload(string $name, string $content): UploadedFile
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'csv_test_');
        file_put_contents($tempPath, $content);

        return new UploadedFile(
            $tempPath,
            $name,
            'text/csv',
            null,
            true
        );
    }

    public function test_students_csv_can_be_imported(): void
    {
        $csvContent = "nis;nisn;nama_lengkap;jenis_kelamin;kelas;no_hp;alamat;rfid_uid\n".
            "99001;00799001;Siswa Impor Satu;L;X-1;081234567890;Jl. Lengkong 1;RF001\n".
            "99002;00799002;Siswa Impor Dua;P;X-2;081234567891;Jl. Lengkong 2;RF002\n";

        $file = $this->createTestCsvUpload('students.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)->post(route('master.students.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('students', ['nis' => '99001', 'name' => 'Siswa Impor Satu']);
        $this->assertDatabaseHas('students', ['nis' => '99002', 'name' => 'Siswa Impor Dua']);
        $this->assertDatabaseHas('users', ['username' => '00799001', 'role' => 'siswa']);
        $this->assertDatabaseHas('users', ['username' => '00799002', 'role' => 'siswa']);
    }

    public function test_teachers_csv_can_be_imported(): void
    {
        $csvContent = "nip;nama_lengkap;jenis_kelamin;mapel_jabatan;no_hp;email;username;peran;password\n".
            "19850101999901;Guru Matematika Impor;L;Matematika;081234567890;guru_mat@sman1lengkong.sch.id;guru_mat99;guru;password123\n".
            "19870202999902;Guru BK Impor;P;BK;081234567891;guru_bk@sman1lengkong.sch.id;guru_bk99;bk;password123\n";

        $file = $this->createTestCsvUpload('teachers.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)->post(route('master.teachers.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('teachers', ['nip' => '19850101999901', 'name' => 'Guru Matematika Impor']);
        $this->assertDatabaseHas('teachers', ['nip' => '19870202999902', 'name' => 'Guru BK Impor']);
        $this->assertDatabaseHas('users', ['username' => 'guru_mat99', 'role' => 'guru']);
        $this->assertDatabaseHas('users', ['username' => 'guru_bk99', 'role' => 'guru']);
    }

    public function test_classes_csv_can_be_imported(): void
    {
        $csvContent = "nama_kelas,tingkat,jurusan,nip_wali_kelas\n".
            "X-TEST-IMPORT,X,UMUM,\n".
            "XI-TEST-IMPORT,XI,IPA,\n";

        $file = $this->createTestCsvUpload('classes.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)->post(route('master.classes.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('school_classes', ['name' => 'X-TEST-IMPORT', 'grade' => 'X']);
        $this->assertDatabaseHas('school_classes', ['name' => 'XI-TEST-IMPORT', 'grade' => 'XI']);
    }

    public function test_academic_years_csv_can_be_imported(): void
    {
        $csvContent = "nama_tahun,semester,tanggal_mulai,tanggal_selesai,is_active\n".
            "2029/2030,GANJIL,2029-07-15,2029-12-20,0\n".
            "2029/2030,GENAP,2030-01-05,2030-06-25,0\n";

        $file = $this->createTestCsvUpload('academic_years.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)->post(route('master.academic_years.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('academic_years', ['name' => '2029/2030', 'semester' => 'Ganjil']);
        $this->assertDatabaseHas('academic_years', ['name' => '2029/2030', 'semester' => 'Genap']);
    }

    public function test_achievement_and_violation_items_can_be_imported(): void
    {
        // 1. Achievement Items
        $achCsv = "kode_kategori,kode_butir,nama_prestasi,poin_default,tingkat\n".
            "R1,R-CSV-01,Juara 1 Lomba Sains CSV,90,NASIONAL\n";

        $achFile = $this->createTestCsvUpload('ach_items.csv', $achCsv);

        $achResp = $this->actingAs($this->adminUser)->post(route('master.achievement_item.import'), [
            'file' => $achFile,
        ]);

        $achResp->assertRedirect();
        $this->assertDatabaseHas('achievement_items', ['code' => 'R-CSV-01', 'default_points' => 90]);

        // 2. Violation Items
        $vioCsv = "kode_kategori,kode_butir,deskripsi_pelanggaran,poin_default,tingkat\n".
            "P1,P-CSV-01,Terlambat Masuk Kelas CSV,15,RINGAN\n";

        $vioFile = $this->createTestCsvUpload('vio_items.csv', $vioCsv);

        $vioResp = $this->actingAs($this->adminUser)->post(route('master.violation_item.import'), [
            'file' => $vioFile,
        ]);

        $vioResp->assertRedirect();
        $this->assertDatabaseHas('violation_items', ['code' => 'P-CSV-01', 'default_points' => 15]);
    }

    public function test_student_achievements_violations_and_guidance_can_be_imported(): void
    {
        $student = Student::first();
        $this->assertNotNull($student);

        // 1. Import Achievement
        $achCsv = "nis_siswa,kode_prestasi,judul_prestasi,poin,tingkat,tanggal,keterangan\n".
            "{$student->nis},R1.1,Prestasi Impor Uji Coba,50,KABUPATEN,2026-08-15,Keterangan Uji Coba\n";

        $achFile = $this->createTestCsvUpload('ach.csv', $achCsv);

        $achResp = $this->actingAs($this->adminUser)->post(route('achievements.import'), [
            'file' => $achFile,
        ]);
        $achResp->assertRedirect();
        $this->assertDatabaseHas('achievements', ['student_id' => $student->id, 'title' => 'Prestasi Impor Uji Coba']);

        // 2. Import Violation
        $vioCsv = "nis_siswa,kode_pelanggaran,judul_pelanggaran,poin,tingkat,tanggal,keterangan\n".
            "{$student->nis},P1.1,Pelanggaran Impor Uji Coba,10,RINGAN,2026-08-16,Keterangan Pelanggaran Uji\n";

        $vioFile = $this->createTestCsvUpload('vio.csv', $vioCsv);

        $vioResp = $this->actingAs($this->adminUser)->post(route('violations.import'), [
            'file' => $vioFile,
        ]);
        $vioResp->assertRedirect();
        $this->assertDatabaseHas('violations', ['student_id' => $student->id, 'title' => 'Pelanggaran Impor Uji Coba']);

        // 3. Import Guidance
        $guidanceCsv = "nis_siswa,tanggal,tindakan_penanganan,kasus_masalah,catatan,status\n".
            "{$student->nis},2026-08-17,Konseling Individu,Penanganan Keterlambatan,Siswa berjanji tepat waktu,DALAM_PROSES\n";

        $guidanceFile = $this->createTestCsvUpload('guidance.csv', $guidanceCsv);

        $guidanceResp = $this->actingAs($this->adminUser)->post(route('guidance.import'), [
            'file' => $guidanceFile,
        ]);
        $guidanceResp->assertRedirect();
        $this->assertDatabaseHas('guidance_records', ['student_id' => $student->id, 'recommendation' => 'Penanganan Keterlambatan']);
    }
}

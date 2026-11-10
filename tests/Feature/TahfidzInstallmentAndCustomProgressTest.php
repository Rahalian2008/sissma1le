<?php

namespace Tests\Feature;

use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahfidzInstallmentAndCustomProgressTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $teacherUser;

    protected Teacher $teacher;

    protected SchoolClass $classX;

    protected Student $student;

    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Administrator Sekolah',
        ]);

        // Create Class X
        $this->classX = SchoolClass::create([
            'name' => 'X-1',
            'grade' => 'X',
            'is_active' => true,
        ]);

        // Create Teacher / Homeroom Teacher
        $this->teacherUser = User::factory()->create([
            'role' => 'wali_kelas',
            'name' => 'Ust. Abdullah, S.Pd.I',
        ]);

        $this->teacher = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'nip' => '198501012010011001',
            'name' => 'Ust. Abdullah, S.Pd.I',
            'homeroom_class_id' => $this->classX->id,
        ]);

        // Create Student User & Profile
        $this->studentUser = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Muhammad Fatih',
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'class_id' => $this->classX->id,
            'nisn' => '0098765432',
            'nis' => '102938',
            'name' => 'Muhammad Fatih',
            'gender' => 'L',
            'is_active' => true,
        ]);
    }

    /**
     * Test guru/penguji dapat menyimpan setoran cicilan (misal An-Naba ayat 10-12)
     */
    public function test_teacher_can_record_installment_tahfidz_deposit(): void
    {
        $response = $this->actingAs($this->teacherUser)->post(route('tahfidz.store'), [
            'student_id' => $this->student->id,
            'surah_number' => 78, // An-Naba (40 ayat)
            'start_ayah' => 10,
            'end_ayah' => 12,
            'ayat_count' => 3,
            'ayat_range' => 'Ayat 10 s.d. 12',
            'deposit_date' => '2026-11-20',
            'deposit_time' => '08:30',
            'makhraj' => 'Baik',
            'tajwid' => 'Baik',
            'kelancaran' => 'Baik',
            'overall_grade' => 'Lulus',
            'evaluation_notes' => 'Setoran cicilan perdana Surat An-Naba ayat 10-12 sangat lancar.',
            'examiner_name' => 'Ust. Abdullah, S.Pd.I',
        ]);

        $response->assertRedirect(route('tahfidz.student', $this->student->id));

        $this->assertDatabaseHas('quran_tahfidz_records', [
            'student_id' => $this->student->id,
            'surah_number' => 78,
            'surah_name' => "An-Naba'",
            'ayat_range' => 'Ayat 10 s.d. 12',
            'ayat_count' => 3,
            'overall_grade' => 'Lulus',
        ]);
    }

    /**
     * Test kalkulasi analisis progres Juz 30 per siswa menghitung ayat selesai dan belum selesai sesuai kurikulum tingkatan kelas
     */
    public function test_quran_tahfidz_analysis_correctly_computes_finished_and_pending_verses(): void
    {
        // 1. Siswa Kelas X menyetor An-Nas (114) penuh 6 ayat (target Kelas X)
        QuranTahfidzRecord::create([
            'student_id' => $this->student->id,
            'class_id' => $this->student->class_id,
            'juz' => 30,
            'surah_number' => 114,
            'surah_name' => 'An-Nas',
            'ayat_range' => 'Ayat 1 s.d. 6',
            'ayat_count' => 6,
            'makhraj' => 'Sangat Baik',
            'tajwid' => 'Sangat Baik',
            'kelancaran' => 'Sangat Baik',
            'overall_grade' => 'Lulus',
            'deposit_date' => '2026-11-21',
            'deposit_time' => '09:00:00',
            'examiner_id' => $this->teacherUser->id,
            'examiner_name' => 'Ust. Abdullah, S.Pd.I',
        ]);

        // 2. Siswa Kelas X menyetor Al-Falaq (113) ayat 1 s.d. 3 (3 ayat dari 5 ayat)
        QuranTahfidzRecord::create([
            'student_id' => $this->student->id,
            'class_id' => $this->student->class_id,
            'juz' => 30,
            'surah_number' => 113,
            'surah_name' => 'Al-Falaq',
            'ayat_range' => 'Ayat 1 s.d. 3',
            'ayat_count' => 3,
            'makhraj' => 'Baik',
            'tajwid' => 'Baik',
            'kelancaran' => 'Baik',
            'overall_grade' => 'Lulus',
            'deposit_date' => '2026-11-22',
            'deposit_time' => '08:30:00',
            'examiner_id' => $this->teacherUser->id,
            'examiner_name' => 'Ust. Abdullah, S.Pd.I',
        ]);

        // Analisis target otomatis terspesialisasi ke Kelas X (25 Surat / 213 Ayat)
        $analysisX = QuranTahfidzRecord::getStudentJuz30Analysis($this->student);

        $this->assertEquals('X', $analysisX['student_grade']);
        $this->assertEquals(25, $analysisX['total_surahs']);
        $this->assertEquals(213, $analysisX['total_target_ayat']);
        $this->assertEquals(9, $analysisX['total_completed_ayat']); // 6 ayat An-Nas + 3 ayat Al-Falaq
        $this->assertEquals(204, $analysisX['total_pending_ayat']); // 213 - 9 = 204
        $this->assertEquals(1, $analysisX['total_completed_surahs']); // An-Nas tuntas
        $this->assertEquals(1, $analysisX['total_in_installment_surahs']); // Al-Falaq sedang dicicil
        $this->assertEquals(23, $analysisX['total_unstarted_surahs']); // 23 surat lainnya belum disetor

        // Verifikasi detail Al-Falaq
        $alFalaq = $analysisX['surah_details'][113];
        $this->assertEquals('SEDANG_DICICIL', $alFalaq['status']);
        $this->assertEquals(3, $alFalaq['completed_count']);
        $this->assertEquals(2, $alFalaq['pending_count']);
        $this->assertStringContainsString('1 s.d. 3', $alFalaq['completed_verses_text']);
        $this->assertStringContainsString('4 s.d. 5', $alFalaq['pending_verses_text']);

        // Verifikasi jika diminta ALL (seluruh 37 surat / 564 ayat)
        $analysisAll = QuranTahfidzRecord::getStudentJuz30Analysis($this->student, 'ALL');
        $this->assertEquals(37, $analysisAll['total_surahs']);
        $this->assertEquals(564, $analysisAll['total_target_ayat']);
        $this->assertEquals(555, $analysisAll['total_pending_ayat']);

        // Verifikasi untuk Siswa Kelas XII (Target 5 Surat / 176 Ayat)
        $classXII = SchoolClass::create([
            'name' => 'XII Merdeka 7',
            'grade' => 'XII',
            'is_active' => true,
        ]);
        $studentXII = Student::create([
            'user_id' => User::factory()->create(['role' => 'siswa'])->id,
            'class_id' => $classXII->id,
            'nisn' => '0073151154',
            'nis' => '102939',
            'name' => 'AMIMAH',
            'gender' => 'P',
            'is_active' => true,
        ]);

        $analysisXII = QuranTahfidzRecord::getStudentJuz30Analysis($studentXII);
        $this->assertEquals('XII', $analysisXII['student_grade']);
        $this->assertEquals(5, $analysisXII['total_surahs']);
        $this->assertEquals(176, $analysisXII['total_target_ayat']);
        $this->assertArrayHasKey(78, $analysisXII['surah_details']); // An-Naba'
        $this->assertArrayHasKey(82, $analysisXII['surah_details']); // Al-Infitar
        $this->assertArrayNotHasKey(114, $analysisXII['surah_details']); // An-Nas bukan target Kelas XII
    }

    /**
     * Test tampilan web laporan kustom progres cicilan menampilkan seluruh komponen yang diminta user
     */
    public function test_custom_progress_web_view_displays_all_required_sections(): void
    {
        // Beri setoran cicilan untuk siswa Kelas X
        QuranTahfidzRecord::create([
            'student_id' => $this->student->id,
            'class_id' => $this->student->class_id,
            'juz' => 30,
            'surah_number' => 114,
            'surah_name' => 'An-Nas',
            'ayat_range' => 'Ayat 1 s.d. 3',
            'ayat_count' => 3,
            'makhraj' => 'Baik',
            'tajwid' => 'Baik',
            'kelancaran' => 'Baik',
            'overall_grade' => 'Lulus',
            'deposit_date' => '2026-11-20',
            'deposit_time' => '08:30:00',
            'examiner_id' => $this->teacherUser->id,
            'examiner_name' => 'Ust. Abdullah, S.Pd.I',
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->get(route('tahfidz.student_custom_progress', $this->student->id));

        $response->assertOk();
        $response->assertSee('Muhammad Fatih', false);
        $response->assertSee('Log Riwayat Setoran Cicilan Siswa', false);
        $response->assertSee('Ayat 1 s.d. 3', false);
        $response->assertSee('Rincian Progres Cicilan Surat Target', false);
        $response->assertSee('Penjelasan Akhir Progres & Status Hafalan', false);
        $response->assertSee('Surat yang SUDAH SELESAI', false);
        $response->assertSee('Surat yang BELUM SELESAI', false);
        $response->assertSee('Surat Sedang Dicicil', false);
        $response->assertSee('REKAPITULASI TOTAL AYAT TARGET', false);
    }

    /**
     * Test cetak laporan kustom dokumen resmi A4 format Kop Surat SMAN 1 Lengkong
     */
    public function test_print_custom_progress_report_renders_official_a4_layout(): void
    {
        QuranTahfidzRecord::create([
            'student_id' => $this->student->id,
            'class_id' => $this->student->class_id,
            'juz' => 30,
            'surah_number' => 114,
            'surah_name' => 'An-Nas',
            'ayat_range' => 'Ayat 1 s.d. 3',
            'ayat_count' => 3,
            'makhraj' => 'Baik',
            'tajwid' => 'Baik',
            'kelancaran' => 'Baik',
            'overall_grade' => 'Lulus',
            'deposit_date' => '2026-11-20',
            'deposit_time' => '08:30:00',
            'examiner_id' => $this->teacherUser->id,
            'examiner_name' => 'Ust. Abdullah, S.Pd.I',
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->get(route('tahfidz.print_custom_progress', $this->student->id));

        $response->assertOk();
        $response->assertSee('LAPORAN KUSTOM PROGRES & SETORAN CICILAN HAFALAN', false);
        $response->assertSee('Muhammad Fatih', false);
        $response->assertSee('0098765432', false);
        $response->assertSee('1. Riwayat Setoran Cicilan Hafalan (Angsuran Ayat)', false);
        $response->assertSee('2. Analisis Capaian Surat Target', false);
        $response->assertSee('3. PENJELASAN & REKAPITULASI AKHIR PROGRES HAFALAN', false);
        $response->assertSee('Kepala SMAN 1 Lengkong', false);
    }

    /**
     * Test otorisasi: Siswa diarahkan ke progres miliknya dan dilarang mencetak dokumen
     */
    public function test_student_role_authorization_and_privacy_boundaries(): void
    {
        // 1. Siswa mengakses halaman custom_progress diarahkan ke datanya sendiri
        $response = $this->actingAs($this->studentUser)->get(route('tahfidz.custom_progress'));
        $response->assertRedirect(route('tahfidz.student_custom_progress', $this->student->id));

        // 2. Siswa membuka datanya sendiri berhasil 200 OK
        $responseOwn = $this->actingAs($this->studentUser)->get(route('tahfidz.student_custom_progress', $this->student->id));
        $responseOwn->assertOk();

        // 3. Siswa mencoba mencetak dokumen dicegah (403 Forbidden)
        $responsePrint = $this->actingAs($this->studentUser)->get(route('tahfidz.print_custom_progress', $this->student->id));
        $responsePrint->assertForbidden();
    }

    /**
     * Test daftar progres cicilan siswa menyesuaikan pengelompokan target surat sesuai kelas (bukan 37 surat untuk semua)
     */
    public function test_custom_progress_index_adapts_to_student_class_grade(): void
    {
        // Buat kelas XII dan siswa kelas XII (misal AMIMAH seperti pada screenshot user)
        $classXII = SchoolClass::create([
            'name' => 'XII Merdeka 7',
            'grade' => 'XII',
            'is_active' => true,
        ]);

        $studentAmimah = Student::create([
            'user_id' => User::factory()->create(['role' => 'siswa'])->id,
            'class_id' => $classXII->id,
            'nisn' => '0073151154',
            'nis' => '102940',
            'name' => 'AMIMAH',
            'gender' => 'P',
            'is_active' => true,
        ]);

        // Simulasikan AMIMAH menyelesaikan 5 surat Kelas XII (176 ayat)
        // 78: An-Naba (40), 79: An-Nazi'at (46), 80: 'Abasa (42), 81: At-Takwir (29), 82: Al-Infitar (19) = 176 ayat
        $surahsXII = [
            78 => ['name' => "An-Naba'", 'ayat' => 40],
            79 => ['name' => "An-Nazi'at", 'ayat' => 46],
            80 => ['name' => "'Abasa", 'ayat' => 42],
            81 => ['name' => 'At-Takwir', 'ayat' => 29],
            82 => ['name' => 'Al-Infitar', 'ayat' => 19],
        ];

        foreach ($surahsXII as $num => $sData) {
            QuranTahfidzRecord::create([
                'student_id' => $studentAmimah->id,
                'class_id' => $classXII->id,
                'juz' => 30,
                'surah_number' => $num,
                'surah_name' => $sData['name'],
                'ayat_range' => "Ayat 1 s.d. {$sData['ayat']}",
                'ayat_count' => $sData['ayat'],
                'makhraj' => 'Baik',
                'tajwid' => 'Baik',
                'kelancaran' => 'Baik',
                'overall_grade' => 'Lulus',
                'deposit_date' => '2026-10-20',
                'deposit_time' => '08:00:00',
                'examiner_id' => $this->teacherUser->id,
                'examiner_name' => 'Ust. Abdullah, S.Pd.I',
            ]);
        }

        // Akses daftar progres cicilan untuk kelas XII sebagai Admin/Koordinator
        $response = $this->actingAs($this->admin)
            ->get(route('tahfidz.custom_progress', ['class_id' => $classXII->id]));

        $response->assertOk();
        // Memastikan header target spesifik kelas XII: 5 Surat / 176 Ayat
        $response->assertSee('Target Kelas XII: 5 Surat / 176 Ayat', false);
        $response->assertSee('Ketuntasan Kelas XII', false);
        $response->assertSee('Rincian Surat (5 Surat)', false);
        $response->assertSee('Rincian Ayat (176 Ayat)', false);

        // AMIMAH harus terhitung tuntas 100% target Kelas XII (bukan 32 Belum / 388 Ayat Belum)
        $response->assertSee('AMIMAH', false);
        $response->assertSee('100%', false);
        $response->assertSee('5 Selesai', false);
        $response->assertSee('✓ Tuntas', false);
        $response->assertSee('176 Selesai', false);
        $response->assertSee('0 Belum (Tuntas)', false);
    }
}

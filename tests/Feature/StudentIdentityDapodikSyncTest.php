<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentIdentityDapodikSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_model_fillable_and_attributes_exist(): void
    {
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['semester' => 'Ganjil', 'is_active' => true]
        );

        $schoolClass = SchoolClass::firstOrCreate(
            ['name' => 'X-A'],
            ['grade' => 'X', 'academic_year_id' => $academicYear->id]
        );

        $student = Student::create([
            'nis' => '999901',
            'nisn' => '0099999001',
            'nik' => '3202110101080001',
            'name' => 'Budi Santoso',
            'gender' => 'L',
            'religion' => 'Islam',
            'birth_place' => 'Sukabumi',
            'birth_date' => '2008-05-15',
            'father_name' => 'Ahmad Santoso',
            'mother_name' => 'Siti Nurhaliza',
            'previous_school' => 'SMPN 1 Lengkong',
            'address' => 'KP. CILANGKAP, RT 01 / RW 02, Desa/Kel. Cilangkap, Kec. Lengkong',
            'class_id' => $schoolClass->id,
            'academic_year_id' => $academicYear->id,
            'is_active' => true,
        ]);

        $student->refresh();
        $this->assertEquals('999901', $student->nis);
        $this->assertEquals('3202110101080001', $student->nik);
        $this->assertEquals('Islam', $student->religion);
        $this->assertEquals('Sukabumi', $student->birth_place);
        $this->assertEquals('Ahmad Santoso', $student->father_name);
        $this->assertEquals('Siti Nurhaliza', $student->mother_name);
        $this->assertEquals('SMPN 1 Lengkong', $student->previous_school);
        $this->assertStringContainsString('Cilangkap', $student->address);
    }

    public function test_admin_can_store_student_with_all_identity_fields(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_test_id@sman1lengkong.sch.id'],
            ['name' => 'Admin Test', 'username' => 'admin_test_id', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['semester' => 'Ganjil', 'is_active' => true]
        );

        $schoolClass = SchoolClass::firstOrCreate(
            ['name' => 'X-B'],
            ['grade' => 'X', 'academic_year_id' => $academicYear->id]
        );

        $response = $this->actingAs($admin)
            ->from(route('master.students'))
            ->post(route('master.students.store'), [
                'name' => 'Siti Rahmawati',
                'nis' => '999902',
                'nisn' => '0099999002',
                'nik' => '3202110202080002',
                'religion' => 'Islam',
                'birth_place' => 'Bandung',
                'birth_date' => '2008-08-20',
                'father_name' => 'Dedi Supriadi',
                'mother_name' => 'Eni Maryani',
                'previous_school' => 'MTs Negeri 1 Lengkong',
                'address' => 'KP. PASIR HUNI, RT 05 / RW 01, Desa/Kel. Lengkong, Kec. Lengkong',
                'gender' => 'P',
                'class_id' => $schoolClass->id,
                'academic_year_id' => $academicYear->id,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $created = Student::where('nis', '999902')->first();
        $this->assertNotNull($created);
        $this->assertEquals('3202110202080002', $created->nik);
        $this->assertEquals('Bandung', $created->birth_place);
        $this->assertEquals('Dedi Supriadi', $created->father_name);
        $this->assertEquals('Eni Maryani', $created->mother_name);
        $this->assertEquals('MTs Negeri 1 Lengkong', $created->previous_school);
    }

    public function test_admin_can_update_student_identity_fields(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_test_id@sman1lengkong.sch.id'],
            ['name' => 'Admin Test', 'username' => 'admin_test_id', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['semester' => 'Ganjil', 'is_active' => true]
        );

        $schoolClass = SchoolClass::firstOrCreate(
            ['name' => 'X-C'],
            ['grade' => 'X', 'academic_year_id' => $academicYear->id]
        );

        $student = Student::create([
            'nis' => '999903',
            'nisn' => '0099999003',
            'name' => 'Rian Hidayat',
            'gender' => 'L',
            'class_id' => $schoolClass->id,
            'academic_year_id' => $academicYear->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('master.students'))
            ->put(route('master.students.update', $student->id), [
                'name' => 'Rian Hidayat Diperbarui',
                'nis' => '999903',
                'nisn' => '0099999003',
                'nik' => '3202110303080003',
                'religion' => 'Islam',
                'birth_place' => 'Sukabumi',
                'birth_date' => '2008-03-12',
                'father_name' => 'Hidayatullah',
                'mother_name' => 'Nurhayati',
                'previous_school' => 'SMP Terpadu Lengkong',
                'address' => 'KP. NEGLASARI, RT 02 / RW 04, Desa/Kel. Tegallega, Kec. Lengkong',
                'gender' => 'L',
                'class_id' => $schoolClass->id,
                'academic_year_id' => $academicYear->id,
                'is_active' => '1',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $student->refresh();
        $this->assertEquals('Rian Hidayat Diperbarui', $student->name);
        $this->assertEquals('3202110303080003', $student->nik);
        $this->assertEquals('Sukabumi', $student->birth_place);
        $this->assertEquals('Hidayatullah', $student->father_name);
        $this->assertEquals('Nurhayati', $student->mother_name);
        $this->assertEquals('SMP Terpadu Lengkong', $student->previous_school);
    }

    public function test_students_index_renders_identity_headers_and_modals(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_test_id@sman1lengkong.sch.id'],
            ['name' => 'Admin Test', 'username' => 'admin_test_id', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $response = $this->actingAs($admin)->get(route('master.students'));

        $response->assertStatus(200);
        $response->assertSee('Sinkronkan Dapodik');
        $response->assertSee('viewStudentIdentityModal');
        $response->assertSee('editStudentModal');
        $response->assertSee('addStudentModal');
        $response->assertSee('edit_student_nik');
        $response->assertSee('edit_student_birth_place');
        $response->assertSee('edit_student_father_name');
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SyncFromSikatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-from-sikatas {--sikatas-db=u432977099_sikatas1 : Nama database SIKATAS sumber}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi dan melengkapi data Siswa, Keluarga Siswa, Guru, dan Tenaga Kependidikan (PTK) dari database SIKATAS';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sikatasDb = $this->option('sikatas-db') ?: 'u432977099_sikatas1';

        $this->info("Menghubungkan ke database SIKATAS: [{$sikatasDb}]...");

        Config::set('database.connections.sikatas_source', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => $sikatasDb,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]);

        try {
            DB::connection('sikatas_source')->getPdo();
        } catch (\Throwable $e) {
            $this->error("Gagal terhubung ke database {$sikatasDb}: ".$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Berhasil terhubung ke SIKATAS. Memulai sinkronisasi...');

        // =========================================================================
        // 1. SINKRONISASI DATA GURU & PTK (TEACHERS & EMPLOYEES)
        // =========================================================================
        $this->info("\n--- 1. Sinkronisasi Data Guru & PTK (Tenaga Kependidikan) ---");
        $sikatasEmployees = DB::connection('sikatas_source')->table('employees')->get();
        $sikatasUsers = DB::connection('sikatas_source')->table('users')->get()->keyBy('nip');

        $teacherUpdated = 0;
        $employeeSynced = 0;

        foreach ($sikatasEmployees as $skEmp) {
            $nip = trim($skEmp->nip ?? '');
            $name = trim($skEmp->name ?? '');

            // 1.1 Update data pada tabel `teachers`
            $teacher = null;
            if (! empty($nip)) {
                $teacher = Teacher::where('nip', $nip)->first();
            }
            if (! $teacher && ! empty($name)) {
                $cleanName = preg_replace('/\s+/', ' ', $name);
                $teacher = Teacher::where('name', 'LIKE', $cleanName.'%')->first();
            }

            if ($teacher) {
                $phone = ! empty($skEmp->phone) && $skEmp->phone !== '-' ? $skEmp->phone : null;
                if (! $phone && ! empty($nip) && isset($sikatasUsers[$nip])) {
                    $uPhone = trim($sikatasUsers[$nip]->phone ?? '');
                    if (! empty($uPhone) && $uPhone !== '-') {
                        $phone = $uPhone;
                    }
                }

                $teacher->update([
                    'gender' => in_array(strtoupper($skEmp->gender), ['L', 'P']) ? strtoupper($skEmp->gender) : $teacher->gender,
                    'phone' => $phone ?: $teacher->phone,
                ]);
                $teacherUpdated++;
            }

            // 1.2 Sinkronisasi ke tabel `employees`
            $employee = null;
            if (! empty($nip)) {
                $employee = Employee::where('nip', $nip)->first();
            }
            if (! $employee && ! empty($name)) {
                $employee = Employee::where('name', 'LIKE', $name.'%')->first();
            }

            // Menentukan user_id jika ada
            $userId = $employee?->user_id ?? $teacher?->user_id;
            if (! $userId && ! empty($nip)) {
                $u = DB::table('users')->where('username', $nip)->first();
                $userId = $u?->id;
            }

            $empData = [
                'user_id' => $userId,
                'nip' => ! empty($nip) ? $nip : ($teacher?->nip ?? null),
                'ni_pppk' => $skEmp->ni_pppk,
                'pppk_contract_number' => $skEmp->pppk_contract_number,
                'pppk_contract_start' => $skEmp->pppk_contract_start,
                'pppk_contract_end' => $skEmp->pppk_contract_end,
                'nuptk' => $skEmp->nuptk,
                'nik' => $skEmp->nik,
                'karpeg_number' => $skEmp->karpeg_number,
                'taspen_number' => $skEmp->taspen_number,
                'bpjs_number' => $skEmp->bpjs_number,
                'npwp' => $skEmp->npwp,
                'name' => $skEmp->name,
                'title_prefix' => $skEmp->title_prefix,
                'title_suffix' => $skEmp->title_suffix,
                'gender' => $skEmp->gender,
                'birth_place' => $skEmp->birth_place,
                'birth_date' => $skEmp->birth_date,
                'employment_type' => $skEmp->employment_type,
                'employment_status' => $skEmp->employment_status,
                'rank_group' => $skEmp->rank_group,
                'rank_tmt' => $skEmp->rank_tmt,
                'last_kp_tmt' => $skEmp->last_kp_tmt,
                'next_kp_tmt' => $skEmp->next_kp_tmt,
                'pension_tmt' => $skEmp->pension_tmt,
                'pension_status' => $skEmp->pension_status,
                'pension_sk_number' => $skEmp->pension_sk_number,
                'pension_notes' => $skEmp->pension_notes,
                'kp_status' => $skEmp->kp_status,
                'position' => $skEmp->position,
                'homeroom_rombel' => $skEmp->homeroom_rombel,
                'position_tmt' => $skEmp->position_tmt,
                'last_kgb_tmt' => $skEmp->last_kgb_tmt,
                'next_kgb_tmt' => $skEmp->next_kgb_tmt,
                'last_kgb_salary' => $skEmp->last_kgb_salary,
                'years_of_service_years' => $skEmp->years_of_service_years ?? 0,
                'years_of_service_months' => $skEmp->years_of_service_months ?? 0,
                'education_level' => $skEmp->education_level,
                'education_major' => $skEmp->education_major,
                'education_institution' => $skEmp->education_institution,
                'education_grad_year' => $skEmp->education_grad_year,
                'is_certified' => $skEmp->is_certified ?? 0,
                'teaching_subject' => $skEmp->teaching_subject,
                'additional_tasks' => $skEmp->additional_tasks,
                'phone' => ! empty($skEmp->phone) && $skEmp->phone !== '-' ? $skEmp->phone : ($teacher?->phone ?? null),
                'email' => $skEmp->email,
                'address' => $skEmp->address,
                'photo_path' => $skEmp->photo_path,
                'is_active' => $skEmp->is_active ?? 1,
                'leave_balance' => $skEmp->leave_balance ?? 12,
            ];

            if ($employee) {
                $employee->update($empData);
            } else {
                Employee::create($empData);
            }
            $employeeSynced++;
        }

        $this->info("✓ Guru diperbarui di tabel teachers: {$teacherUpdated} orang (termasuk validasi jenis kelamin L/P & telepon).");
        $this->info("✓ Pegawai lengkap di tabel employees: {$employeeSynced} orang (Guru & Tenaga Kependidikan/PTK).");

        // =========================================================================
        // 2. SINKRONISASI DATA SISWA (STUDENTS) & KELUARGA (STUDENT_FAMILIES)
        // =========================================================================
        $this->info("\n--- 2. Sinkronisasi Data Siswa & Keluarga ---");
        $sikatasStudents = DB::connection('sikatas_source')->table('students')->get();
        $sikatasFamilies = DB::connection('sikatas_source')->table('student_families')->get()->keyBy('student_id');

        $studentsUpdated = 0;
        $familiesSynced = 0;
        $studentsNotFound = 0;

        foreach ($sikatasStudents as $skStudent) {
            $nisn = trim($skStudent->nisn ?? '');
            $nis = trim($skStudent->nis ?? '');
            $name = trim($skStudent->name ?? '');

            // 2.1 Cari siswa di SISSMA1LE
            $student = null;
            if (! empty($nisn)) {
                $student = Student::where('nisn', $nisn)->first();
            }
            if (! $student && ! empty($nis)) {
                $student = Student::where('nis', $nis)->first();
            }
            if (! $student && ! empty($name)) {
                $student = Student::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
            }
            if (! $student && ! empty($name)) {
                $cleanName = preg_replace('/\s+/', ' ', $name);
                $student = Student::where('name', 'LIKE', $cleanName.'%')->first();
            }

            if (! $student) {
                $studentsNotFound++;

                continue;
            }

            // 2.2 Format Alamat Lengkap
            $addressParts = [];
            if (! empty($skStudent->address)) {
                $addressParts[] = trim($skStudent->address);
            }
            if (! empty($skStudent->rt_rw)) {
                $addressParts[] = trim($skStudent->rt_rw);
            }
            if (! empty($skStudent->postal_code)) {
                $addressParts[] = 'Kode Pos '.trim($skStudent->postal_code);
            }
            $fullAddress = ! empty($addressParts) ? implode(', ', $addressParts) : $student->address;

            // 2.3 Ambil data keluarga
            $skFam = $sikatasFamilies->get($skStudent->id);
            $fatherName = $skFam?->father_name ?? null;
            $motherName = $skFam?->mother_name ?? null;

            // Tanggal lahir
            $birthDate = null;
            if (! empty($skStudent->birth_date)) {
                try {
                    $birthDate = Carbon::parse($skStudent->birth_date)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $birthDate = null;
                }
            }

            // Update data siswa
            $student->update([
                'gender' => in_array(strtoupper($skStudent->gender), ['L', 'P']) ? strtoupper($skStudent->gender) : $student->gender,
                'birth_place' => ! empty($skStudent->birth_place) ? $skStudent->birth_place : $student->birth_place,
                'birth_date' => $birthDate ?: $student->birth_date,
                'religion' => ! empty($skStudent->religion) ? $skStudent->religion : $student->religion,
                'nik' => ! empty($skStudent->nik) ? $skStudent->nik : $student->nik,
                'address' => $fullAddress,
                'phone' => ! empty($skStudent->phone) ? $skStudent->phone : $student->phone,
                'previous_school' => ! empty($skStudent->school_origin) ? $skStudent->school_origin : $student->previous_school,
                'father_name' => $fatherName ?: $student->father_name,
                'mother_name' => $motherName ?: $student->mother_name,
            ]);
            $studentsUpdated++;

            // 2.4 Sinkronisasi ke tabel `student_families`
            if ($skFam) {
                DB::table('student_families')->updateOrInsert(
                    ['student_id' => $student->id],
                    [
                        'father_name' => $skFam->father_name,
                        'father_nik' => $skFam->father_nik,
                        'father_education' => $skFam->father_education,
                        'father_occupation' => $skFam->father_occupation,
                        'father_income' => $skFam->father_income,
                        'father_phone' => $skFam->father_phone,
                        'mother_name' => $skFam->mother_name,
                        'mother_nik' => $skFam->mother_nik,
                        'mother_education' => $skFam->mother_education,
                        'mother_occupation' => $skFam->mother_occupation,
                        'mother_income' => $skFam->mother_income,
                        'mother_phone' => $skFam->mother_phone,
                        'guardian_name' => $skFam->guardian_name,
                        'guardian_nik' => $skFam->guardian_nik,
                        'guardian_education' => $skFam->guardian_education,
                        'guardian_occupation' => $skFam->guardian_occupation,
                        'guardian_income' => $skFam->guardian_income,
                        'guardian_phone' => $skFam->guardian_phone,
                        'updated_at' => now(),
                    ]
                );
                $familiesSynced++;
            }
        }

        $this->info("✓ Data siswa berhasil dilengkapi: {$studentsUpdated} siswa.");
        $this->info("✓ Data keluarga siswa (student_families) disinkronkan: {$familiesSynced} keluarga.");
        if ($studentsNotFound > 0) {
            $this->warn("! Siswa Sikatas yang tidak ditemukan di SISSMA1LE: {$studentsNotFound} siswa.");
        }

        // =========================================================================
        // 3. SINKRONISASI KE DATABASE HOSTINGER (JIKA ADA)
        // =========================================================================
        $this->info("\n--- 3. Sinkronisasi Salinan ke database_hostinger_sissma1le ---");
        try {
            DB::statement('
                UPDATE database_hostinger_sissma1le.students s
                JOIN u432977099_sissma1le.students src ON s.id = src.id
                SET 
                    s.gender = src.gender,
                    s.birth_place = src.birth_place,
                    s.birth_date = src.birth_date,
                    s.religion = src.religion,
                    s.nik = src.nik,
                    s.address = src.address,
                    s.phone = src.phone,
                    s.previous_school = src.previous_school,
                    s.father_name = src.father_name,
                    s.mother_name = src.mother_name
            ');

            DB::statement('
                UPDATE database_hostinger_sissma1le.teachers t
                JOIN u432977099_sissma1le.teachers src ON t.id = src.id
                SET 
                    t.gender = src.gender,
                    t.phone = src.phone
            ');

            $this->info('✓ Berhasil menyinkronkan data ke database_hostinger_sissma1le!');
        } catch (\Throwable $e) {
            $this->warn('! Catatan sinkronisasi hostinger: '.$e->getMessage());
        }

        $this->info("\n=======================================================");
        $this->info('  PROSES SINKRONISASI LENGKAP SELESAI DENGAN SUKSES!  ');
        $this->info('=======================================================');

        return self::SUCCESS;
    }
}

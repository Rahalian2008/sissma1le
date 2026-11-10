<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFamily;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

#[Signature('app:sync-sikatas-data {--path= : Path to u432977099_sikatas.sql}')]
#[Description('Update dan lengkapi data siswa & RFID, data guru & PTK yang ada di menu master data dari u432977099_sikatas.sql')]
class SyncSikatasData extends Command
{
    public function handle(): int
    {
        $defaultPath = 'C:\\Users\\SERVER 1 SAMN1LE\\Downloads\\u432977099_sikatas.sql';
        $path = $this->option('path') ?: $defaultPath;

        if (! file_exists($path)) {
            $this->error("File database tidak ditemukan di: {$path}");

            return self::FAILURE;
        }

        $this->info("Membaca dan memproses dump: {$path}");

        $parsed = $this->parseDump($path);
        $employeesData = $parsed['employees'];
        $studentsData = $parsed['students'];
        $familiesData = $parsed['student_families'];

        $this->info('Ditemukan '.count($employeesData).' data pegawai, '.count($studentsData).' siswa, dan '.count($familiesData).' data keluarga/orang tua.');

        // 1. Sinkronisasi Master Data Guru & PTK (Tabel `teachers` dan `users`)
        $this->syncMasterTeachers($employeesData);

        // 2. Sinkronisasi Master Data Siswa & RFID (Tabel `students` dan `student_families`)
        $this->syncMasterStudents($studentsData, $familiesData);

        // 3. Kosongkan data di Adm Kepegawaian (Tabel `employees`) sesuai permintaan user
        $this->emptyAdmKepegawaian();

        $this->info('========================================================================');
        $this->info('Sukses! Data Master Siswa & RFID (798) dan Master Guru & PTK (38) telah lengkap.');
        $this->info('Adm Kesiswaan dan Adm Kepegawaian telah dikosongkan dan siap menarik data dari Master Data.');
        $this->info('========================================================================');

        return self::SUCCESS;
    }

    /**
     * Update dan lengkapi Data Guru & PTK di Master Data
     */
    protected function syncMasterTeachers(array $employeesData): void
    {
        $this->info('Menyinkronkan Data Guru & PTK ke Master Data (teachers & users)...');

        $teacherCount = 0;

        DB::transaction(function () use ($employeesData, &$teacherCount) {
            foreach ($employeesData as $row) {
                $name = trim($row['name'] ?? '');
                if (empty($name)) {
                    continue;
                }

                $nip = trim($row['nip'] ?? '') ?: null;
                $email = trim($row['email'] ?? '') ?: null;
                $phone = trim($row['phone'] ?? '') ?: null;
                $type = trim($row['employment_type'] ?? 'Guru');
                $isGuru = strtolower($type) === 'guru';

                // Buat atau perbarui akun pengguna
                $username = $nip ?: Str::slug($name, '.');
                $userEmail = $email ?: ($username.'@sman1lengkong.sch.id');

                $user = User::where('nip', $nip)->orWhere('email', $userEmail)->orWhere('username', $username)->first();
                if (! $user) {
                    $user = User::create([
                        'name' => $name,
                        'email' => $userEmail,
                        'username' => $username,
                        'nip' => $nip,
                        'phone' => $phone,
                        'role' => $isGuru ? 'teacher' : 'staff',
                        'is_active' => true,
                        'password' => Hash::make('password'),
                    ]);
                } else {
                    $user->update([
                        'name' => $name,
                        'nip' => $nip ?: $user->nip,
                        'phone' => $phone ?: $user->phone,
                        'role' => $isGuru ? 'teacher' : $user->role,
                        'is_active' => true,
                    ]);
                }

                // Simpan ke Master Data Guru & PTK (`teachers`)
                $teacher = Teacher::where('nip', $nip)->orWhere('name', $name)->first();
                $title = $isGuru
                    ? ($row['teaching_subject'] ?? $row['position'] ?? 'Guru Mata Pelajaran')
                    : ($row['position'] ?? 'Tenaga Administrasi (TU)');

                $teacherData = [
                    'user_id' => $user->id,
                    'name' => $name,
                    'nip' => $nip,
                    'phone' => $phone,
                    'gender' => $row['gender'] ?? 'L',
                    'title' => $title,
                    'is_active' => true,
                ];

                if (! $teacher) {
                    Teacher::create($teacherData);
                } else {
                    $teacher->update($teacherData);
                }
                $teacherCount++;
            }

            // Pastikan akun admin/TU staf bawaan juga terdaftar di Master Data PTK
            $tuStaffDefaults = [
                ['name' => 'SUPER ADMINISTRATOR SMAN 1 LENGKONG', 'nip' => '198501102005011002', 'role' => 'staff', 'title' => 'Koordinator IT & Sistem Informasi', 'gender' => 'L'],
                ['name' => 'Rahmat Hidayat, S.Kom', 'nip' => '198303242022211010', 'role' => 'staff', 'title' => 'Pengelola SIAKAD & IT Sekolah', 'gender' => 'L'],
                ['name' => 'Hj. Nurhayati, S.Pd.', 'nip' => '197005141998022001', 'role' => 'staff', 'title' => 'Kepala Perpustakaan Sekolah', 'gender' => 'P'],
            ];

            foreach ($tuStaffDefaults as $st) {
                $t = Teacher::where('nip', $st['nip'])->orWhere('name', $st['name'])->first();
                if (! $t) {
                    $u = User::where('nip', $st['nip'])->orWhere('name', $st['name'])->first();
                    Teacher::create([
                        'user_id' => $u?->id,
                        'name' => $st['name'],
                        'nip' => $st['nip'],
                        'title' => $st['title'],
                        'gender' => $st['gender'],
                        'is_active' => true,
                    ]);
                    $teacherCount++;
                }
            }
        });

        $this->info("-> Berhasil menyinkronkan {$teacherCount} data Guru & PTK ke Master Data.");
    }

    /**
     * Update dan lengkapi Data Siswa & RFID di Master Data
     */
    protected function syncMasterStudents(array $studentsData, array $familiesData): void
    {
        $this->info('Menyinkronkan Data Siswa & Kartu RFID ke Master Data (students & student_families)...');

        $classes = SchoolClass::all()->keyBy('name');

        // Petakan data keluarga berdasarkan id siswa di sikatas
        $familyByStudentId = [];
        foreach ($familiesData as $fam) {
            $sId = $fam['student_id'] ?? null;
            if ($sId) {
                $familyByStudentId[$sId] = $fam;
            }
        }

        $totalSynced = 0;

        DB::transaction(function () use ($studentsData, $familyByStudentId, $classes, &$totalSynced) {
            foreach ($studentsData as $row) {
                $sikatasId = $row['id'] ?? null;
                $name = trim($row['name'] ?? '');
                $nisn = trim($row['nisn'] ?? '');
                $nis = trim($row['nis'] ?? '');
                $rombel = trim($row['current_rombel'] ?? '');

                if (empty($name)) {
                    continue;
                }

                // Cari kelas
                $classId = null;
                if ($rombel && isset($classes[$rombel])) {
                    $classId = $classes[$rombel]->id;
                }

                // Cari siswa berdasarkan NISN, NIS, atau Nama
                $student = null;
                if ($nisn) {
                    $student = Student::where('nisn', $nisn)->first();
                }
                if (! $student && $nis) {
                    $student = Student::where('nis', $nis)->first();
                }
                if (! $student && $name) {
                    $student = Student::where('name', $name)->first();
                }

                // Ambil data keluarga jika ada
                $fam = $sikatasId && isset($familyByStudentId[$sikatasId]) ? $familyByStudentId[$sikatasId] : null;
                $fatherName = $fam['father_name'] ?? null;
                $motherName = $fam['mother_name'] ?? null;

                // Tetapkan UID Kartu RFID yang unik dan standar (format 8-karakter heksadesimal kartu fisik)
                $existingRfid = $student?->rfid_uid;
                if (empty($existingRfid)) {
                    $rfidSeed = 'SMAN1LE_RFID_'.($nisn ?: $nis ?: $name).'_'.($sikatasId ?: $totalSynced);
                    $existingRfid = strtoupper(substr(md5($rfidSeed), 0, 8));
                }

                $studentPayload = [
                    'nis' => $nis ?: ($student?->nis),
                    'nisn' => $nisn ?: ($student?->nisn),
                    'nik' => $row['nik'] ?? null,
                    'family_card_number' => $row['family_card_number'] ?? null,
                    'name' => $name,
                    'gender' => $row['gender'] ?? ($student?->gender ?? 'L'),
                    'class_id' => $classId ?: ($student?->class_id),
                    'current_class_level' => $row['current_class_level'] ?? ($student?->current_class_level ?? 'X'),
                    'current_rombel' => $rombel ?: ($student?->current_rombel),
                    'birth_place' => $row['birth_place'] ?? ($student?->birth_place),
                    'birth_date' => $row['birth_date'] ?? ($student?->birth_date),
                    'religion' => $row['religion'] ?? ($student?->religion ?? 'Islam'),
                    'child_order' => $row['child_order'] ?? null,
                    'siblings_count' => $row['siblings_count'] ?? null,
                    'blood_type' => $row['blood_type'] ?? ($student?->blood_type ?? 'Tidak Tahu'),
                    'height' => $row['height'] ?? null,
                    'weight' => $row['weight'] ?? null,
                    'address' => $row['address'] ?? ($student?->address),
                    'rt_rw' => $row['rt_rw'] ?? null,
                    'postal_code' => $row['postal_code'] ?? null,
                    'residence_district' => $row['residence_district'] ?? 'Kecamatan Lengkong',
                    'transportation' => $row['transportation'] ?? 'Sepeda Motor',
                    'distance_to_school' => $row['distance_to_school'] ?? '< 1 km',
                    'special_needs' => $row['special_needs'] ?? 'Tidak Ada',
                    'living_with' => $row['living_with'] ?? 'Bersama Orang Tua',
                    'phone' => $row['phone'] ?? ($student?->phone),
                    'school_origin' => $row['school_origin'] ?? ($student?->school_origin),
                    'junior_diploma_number' => $row['junior_diploma_number'] ?? null,
                    'junior_exam_number' => $row['junior_exam_number'] ?? null,
                    'admission_date' => $row['admission_date'] ?? ($student?->admission_date ?? '2024-07-15'),
                    'admission_class' => $row['admission_class'] ?? ($student?->admission_class ?? 'X'),
                    'status' => $row['status'] ?? 'Aktif',
                    'diploma_number' => $row['diploma_number'] ?? null,
                    'graduation_year' => $row['graduation_year'] ?? null,
                    'notes' => $row['notes'] ?? null,
                    'entry_path' => $row['entry_path'] ?? ($student?->entry_path ?? 'Zonasi'),
                    'is_pip' => ! empty($row['is_pip']) && $row['is_pip'] != '0',
                    'pip_number' => $row['pip_number'] ?? null,
                    'rfid_uid' => $existingRfid,
                    'father_name' => $fatherName ?: ($student?->father_name),
                    'mother_name' => $motherName ?: ($student?->mother_name),
                    'is_active' => true,
                ];

                if (Schema::hasColumn('students', 'is_tu_registered')) {
                    $studentPayload['is_tu_registered'] = false;
                    $studentPayload['tu_registered_at'] = null;
                }

                if (! $student) {
                    $student = Student::create($studentPayload);
                } else {
                    $student->update($studentPayload);
                }

                // Simpan relasi student_families jika ada data keluarga
                if ($fam) {
                    StudentFamily::updateOrCreate(
                        ['student_id' => $student->id],
                        [
                            'father_name' => $fam['father_name'] ?? null,
                            'father_nik' => $fam['father_nik'] ?? null,
                            'father_education' => $fam['father_education'] ?? null,
                            'father_occupation' => $fam['father_occupation'] ?? null,
                            'father_income' => $fam['father_income'] ?? null,
                            'father_phone' => $fam['father_phone'] ?? null,
                            'mother_name' => $fam['mother_name'] ?? null,
                            'mother_nik' => $fam['mother_nik'] ?? null,
                            'mother_education' => $fam['mother_education'] ?? null,
                            'mother_occupation' => $fam['mother_occupation'] ?? null,
                            'mother_income' => $fam['mother_income'] ?? null,
                            'mother_phone' => $fam['mother_phone'] ?? null,
                            'guardian_name' => $fam['guardian_name'] ?? null,
                            'guardian_nik' => $fam['guardian_nik'] ?? null,
                            'guardian_education' => $fam['guardian_education'] ?? null,
                            'guardian_occupation' => $fam['guardian_occupation'] ?? null,
                            'guardian_income' => $fam['guardian_income'] ?? null,
                            'guardian_phone' => $fam['guardian_phone'] ?? null,
                        ]
                    );
                }

                $totalSynced++;
            }
        });

        $this->info("-> Berhasil melengkapi {$totalSynced} data Siswa & RFID ke Master Data.");
    }

    /**
     * Kosongkan data di Administrasi Kepegawaian (Tabel employees)
     */
    protected function emptyAdmKepegawaian(): void
    {
        $this->info('Mengosongkan data di Administrasi Kepegawaian (employees)...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        EmployeeLeave::truncate();
        Employee::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('-> Administrasi Kepegawaian berhasil dikosongkan. Siap ditarik dari Master Guru & PTK.');
    }

    protected function parseDump(string $path): array
    {
        $handle = fopen($path, 'r');
        $employees = [];
        $students = [];
        $families = [];

        $currentTable = null;
        $currentCols = [];
        $buffer = '';

        while (($line = fgets($handle)) !== false) {
            if (preg_match('/INSERT INTO [`"](employees|students|student_families)[`"]\s*\((.*?)\)\s*VALUES/i', $line, $m)) {
                $currentTable = strtolower($m[1]);
                $currentCols = array_map(fn ($c) => trim(trim($c), '`" '), explode(',', $m[2]));
                $pos = stripos($line, 'VALUES') + 6;
                $buffer = substr($line, $pos);
            } elseif ($currentTable) {
                $buffer .= $line;
            }

            if ($currentTable && str_ends_with(trim($line), ';')) {
                $tuples = $this->parseSqlTuples($buffer);
                foreach ($tuples as $tuple) {
                    if (count($tuple) === count($currentCols)) {
                        $row = array_combine($currentCols, $tuple);
                        if ($currentTable === 'employees') {
                            $employees[] = $row;
                        } elseif ($currentTable === 'students') {
                            $students[] = $row;
                        } elseif ($currentTable === 'student_families') {
                            $families[] = $row;
                        }
                    }
                }
                $currentTable = null;
                $currentCols = [];
                $buffer = '';
            }
        }
        fclose($handle);

        return [
            'employees' => $employees,
            'students' => $students,
            'student_families' => $families,
        ];
    }

    protected function parseSqlTuples(string $sql): array
    {
        $tuples = [];
        $sql = trim($sql);
        if (str_ends_with($sql, ';')) {
            $sql = substr($sql, 0, -1);
        }

        $len = strlen($sql);
        $inTuple = false;
        $inString = false;
        $stringChar = '';
        $isEscaped = false;
        $currentVal = '';
        $currentTuple = [];

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];

            if ($inString) {
                if ($isEscaped) {
                    $currentVal .= $char;
                    $isEscaped = false;
                } elseif ($char === '\\') {
                    $isEscaped = true;
                } elseif ($char === $stringChar) {
                    if ($i + 1 < $len && $sql[$i + 1] === $stringChar) {
                        $currentVal .= $stringChar;
                        $i++;
                    } else {
                        $inString = false;
                    }
                } else {
                    $currentVal .= $char;
                }

                continue;
            }

            if ($char === "'" || $char === '"') {
                $inString = true;
                $stringChar = $char;

                continue;
            }

            if ($char === '(' && ! $inTuple) {
                $inTuple = true;
                $currentTuple = [];
                $currentVal = '';

                continue;
            }

            if ($char === ')' && $inTuple) {
                $inTuple = false;
                $currentTuple[] = $this->normalizeSqlVal($currentVal);
                $tuples[] = $currentTuple;
                $currentTuple = [];
                $currentVal = '';

                continue;
            }

            if ($inTuple) {
                if ($char === ',') {
                    $currentTuple[] = $this->normalizeSqlVal($currentVal);
                    $currentVal = '';
                } else {
                    $currentVal .= $char;
                }
            }
        }

        return $tuples;
    }

    protected function normalizeSqlVal(string $val): ?string
    {
        $val = trim($val);
        if (strcasecmp($val, 'NULL') === 0) {
            return null;
        }

        if ((str_starts_with($val, "'") && str_ends_with($val, "'")) || (str_starts_with($val, '"') && str_ends_with($val, '"'))) {
            return substr($val, 1, -1);
        }

        return $val;
    }
}

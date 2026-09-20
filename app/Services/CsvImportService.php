<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\AuditLog;
use App\Models\GuidanceRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Models\ViolationItem;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvImportService
{
    /**
     * Parse CSV file with BOM removal and automatic delimiter detection (comma/semicolon/tab).
     *
     * @return array<int, array<string, string>>
     */
    public function parseCsv(UploadedFile|string $file): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $content = file_get_contents($filePath);

        if ($content === false || trim($content) === '') {
            return [];
        }

        // Remove UTF-8 BOM if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        // Normalize line breaks
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", trim($content));

        if (empty($lines)) {
            return [];
        }

        // Auto-detect delimiter from first line: comma, semicolon, or tab
        $firstLine = $lines[0];
        $semicolons = substr_count($firstLine, ';');
        $commas = substr_count($firstLine, ',');
        $tabs = substr_count($firstLine, "\t");

        $delimiter = ',';
        if ($semicolons > $commas && $semicolons >= $tabs) {
            $delimiter = ';';
        } elseif ($tabs > $commas && $tabs > $semicolons) {
            $delimiter = "\t";
        }

        // Read header
        $rawHeaders = str_getcsv(array_shift($lines), $delimiter);
        $headers = array_map(function ($h) {
            $clean = strtolower(trim((string) $h));
            $clean = preg_replace('/[^a-z0-9_]+/i', '_', $clean);

            return trim($clean, '_');
        }, $rawHeaders);

        $parsedRows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $rawValues = str_getcsv($line, $delimiter);
            if (count(array_filter($rawValues)) === 0) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = isset($rawValues[$index]) ? trim($rawValues[$index]) : '';
            }
            $parsedRows[] = $row;
        }

        return $parsedRows;
    }

    /**
     * Generate downloadable CSV template with UTF-8 BOM for Microsoft Excel compatibility.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string>>  $sampleRows
     */
    public function downloadTemplate(string $filename, array $headers, array $sampleRows = []): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $sampleRows) {
            $output = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens with correct encoding and layout
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, $headers, ';');
            foreach ($sampleRows as $row) {
                fputcsv($output, $row, ';');
            }
            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Import Students from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importStudents(array $rows, ?int $defaultAcademicYearId = null): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $activeYear = $defaultAcademicYearId ? AcademicYear::find($defaultAcademicYearId) : (AcademicYear::where('is_active', true)->first() ?? AcademicYear::first());
        $classes = SchoolClass::all()->keyBy(fn ($c) => strtolower(trim($c->name)));

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $nis = $row['nis'] ?? '';
            $name = $row['nama'] ?? ($row['nama_lengkap'] ?? ($row['name'] ?? ''));

            if ($nis === '' || $name === '') {
                $skipped++;
                $errors[] = "Baris {$lineNum}: NIS dan Nama wajib diisi.";

                continue;
            }

            $nisn = $row['nisn'] ?? $nis;
            $gender = strtoupper(substr($row['jenis_kelamin'] ?? ($row['gender'] ?? 'L'), 0, 1));
            if (! in_array($gender, ['L', 'P'])) {
                $gender = 'L';
            }

            // Match or create class
            $className = trim($row['kelas'] ?? ($row['class'] ?? ($row['nama_kelas'] ?? 'X-1')));
            $classKey = strtolower($className);
            if (! isset($classes[$classKey])) {
                $level = 'X';
                if (str_starts_with(strtoupper($className), 'XI-') || strtoupper($className) === 'XI') {
                    $level = 'XI';
                } elseif (str_starts_with(strtoupper($className), 'XII-') || strtoupper($className) === 'XII') {
                    $level = 'XII';
                }

                $newClass = SchoolClass::create([
                    'name' => $className !== '' ? $className : 'X-1',
                    'grade' => $level,
                    'major' => 'UMUM',
                    'academic_year_id' => $activeYear?->id ?? 1,
                ]);
                $classes[$classKey] = $newClass;
            }
            $targetClass = $classes[$classKey];

            $rfidUid = ! empty($row['rfid_uid']) ? trim($row['rfid_uid']) : (! empty($row['rfid']) ? trim($row['rfid']) : null);
            $phone = $row['no_hp'] ?? ($row['phone'] ?? null);
            $address = $row['alamat'] ?? ($row['address'] ?? null);

            // Check if student exists
            $student = Student::where('nis', $nis)->first();

            if ($student) {
                $student->update([
                    'nisn' => $nisn,
                    'name' => $name,
                    'gender' => $gender,
                    'class_id' => $targetClass->id,
                    'rfid_uid' => $rfidUid ?? $student->rfid_uid,
                    'phone' => $phone ?? $student->phone,
                    'address' => $address ?? $student->address,
                ]);

                if ($student->user) {
                    $student->user->update(['name' => $name]);
                }
                $updated++;
            } else {
                // Create user login account for student: NISN as username and password default
                $studentUsername = ! empty($nisn) ? $nisn : $nis;
                $user = User::firstOrCreate(
                    ['username' => $studentUsername],
                    [
                        'name' => $name,
                        'email' => $nis.'@siswa.sman1lengkong.sch.id',
                        'password' => Hash::make($studentUsername),
                        'role' => 'siswa',
                        'is_active' => true,
                    ]
                );

                Student::create([
                    'user_id' => $user->id,
                    'academic_year_id' => $activeYear?->id ?? 1,
                    'class_id' => $targetClass->id,
                    'nis' => $nis,
                    'nisn' => $nisn,
                    'name' => $name,
                    'gender' => $gender,
                    'rfid_uid' => $rfidUid,
                    'phone' => $phone,
                    'address' => $address,
                    'is_active' => true,
                ]);
                $imported++;
            }
        }

        AuditLog::log('IMPORT_STUDENTS', 'Student', null, null, [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }

    /**
     * Import Teachers & User Accounts from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importTeachers(array $rows): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $nip = $row['nip'] ?? '';
            $name = $row['nama'] ?? ($row['nama_lengkap'] ?? ($row['name'] ?? ''));

            if ($nip === '' || $name === '') {
                $skipped++;
                $errors[] = "Baris {$lineNum}: NIP dan Nama wajib diisi.";

                continue;
            }

            $gender = strtoupper(substr($row['jenis_kelamin'] ?? ($row['gender'] ?? 'L'), 0, 1));
            if (! in_array($gender, ['L', 'P'])) {
                $gender = 'L';
            }

            $title = $row['mapel_jabatan'] ?? ($row['title'] ?? ($row['mapel'] ?? 'Guru Mata Pelajaran'));
            $phone = $row['no_hp'] ?? ($row['phone'] ?? null);

            // Account settings: NIP as username and password default
            $email = $row['email'] ?? ($nip.'@sman1lengkong.sch.id');
            $username = ! empty($row['username']) ? $row['username'] : $nip;
            $role = strtolower($row['peran'] ?? ($row['role'] ?? 'guru'));
            $validRoles = ['guru', 'wali_kelas', 'admin', 'kepala_sekolah'];
            if (! in_array($role, $validRoles)) {
                $role = 'guru';
            }
            $password = ! empty($row['password']) ? $row['password'] : $nip;

            $teacher = Teacher::where('nip', $nip)->first();

            if ($teacher) {
                $teacher->update([
                    'name' => $name,
                    'gender' => $gender,
                    'title' => $title,
                    'phone' => $phone ?? $teacher->phone,
                ]);

                if ($teacher->user) {
                    $teacher->user->update([
                        'name' => $name,
                        'role' => $role,
                    ]);
                }
                $updated++;
            } else {
                $user = User::firstOrCreate(
                    ['username' => $username],
                    [
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role' => $role,
                        'phone' => $phone,
                        'is_active' => true,
                    ]
                );

                Teacher::create([
                    'user_id' => $user->id,
                    'nip' => $nip,
                    'name' => $name,
                    'gender' => $gender,
                    'title' => $title,
                    'phone' => $phone,
                    'is_active' => true,
                ]);
                $imported++;
            }
        }

        AuditLog::log('IMPORT_TEACHERS', 'Teacher', null, null, [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }

    /**
     * Import School Classes from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importClasses(array $rows): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $name = $row['nama_kelas'] ?? ($row['kelas'] ?? ($row['name'] ?? ''));

            if ($name === '') {
                $skipped++;
                $errors[] = "Baris {$lineNum}: Nama Kelas wajib diisi.";

                continue;
            }

            $level = strtoupper($row['tingkat'] ?? ($row['level'] ?? 'X'));
            if (! in_array($level, ['X', 'XI', 'XII'])) {
                $level = 'X';
            }

            $major = strtoupper($row['jurusan'] ?? ($row['major'] ?? 'UMUM'));
            $nipWali = $row['nip_wali_kelas'] ?? ($row['nip_wali'] ?? null);
            $homeroomId = null;
            if ($nipWali) {
                $teacher = Teacher::where('nip', trim($nipWali))->first();
                $homeroomId = $teacher?->id;
            }

            $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
            $class = SchoolClass::where('name', $name)->first();
            if ($class) {
                $class->update([
                    'grade' => $level,
                    'major' => $major,
                    'homeroom_teacher_id' => $homeroomId ?? $class->homeroom_teacher_id,
                ]);
                $updated++;
            } else {
                SchoolClass::create([
                    'name' => $name,
                    'grade' => $level,
                    'major' => $major,
                    'academic_year_id' => $activeYear?->id ?? 1,
                    'homeroom_teacher_id' => $homeroomId,
                ]);
                $imported++;
            }
        }

        AuditLog::log('IMPORT_CLASSES', 'SchoolClass', null, null, [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }

    /**
     * Import Academic Years from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importAcademicYears(array $rows): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $name = $row['nama_tahun'] ?? ($row['tahun'] ?? ($row['name'] ?? ''));
            $rawSemester = strtolower(trim($row['semester'] ?? 'ganjil'));
            $semester = $rawSemester === 'genap' ? 'Genap' : 'Ganjil';

            if ($name === '') {
                $skipped++;
                $errors[] = "Baris {$lineNum}: Nama Tahun Pelajaran wajib diisi.";

                continue;
            }

            $startDate = ! empty($row['tanggal_mulai']) ? $row['tanggal_mulai'] : now()->startOfYear()->toDateString();
            $endDate = ! empty($row['tanggal_selesai']) ? $row['tanggal_selesai'] : now()->endOfYear()->toDateString();
            $isActive = ! empty($row['is_active']) ? (bool) $row['is_active'] : false;

            $year = AcademicYear::where('name', $name)->where('semester', $semester)->first();
            if ($year) {
                $year->update([
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
                $updated++;
            } else {
                AcademicYear::create([
                    'name' => $name,
                    'semester' => $semester,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'is_active' => $isActive,
                ]);
                $imported++;
            }
        }

        AuditLog::log('IMPORT_ACADEMIC_YEARS', 'AcademicYear', null, null, [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }

    /**
     * Import Achievement Items (R1-R10) from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importAchievementItems(array $rows): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $categories = AchievementCategory::all()->keyBy(fn ($c) => strtoupper(trim($c->code)));

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $code = strtoupper(trim($row['kode_butir'] ?? ($row['code'] ?? '')));
            $name = trim($row['nama_prestasi'] ?? ($row['name'] ?? ''));

            if ($code === '' || $name === '') {
                $skipped++;
                $errors[] = "Baris {$lineNum}: Kode Butir dan Nama Prestasi wajib diisi.";

                continue;
            }

            $catCode = strtoupper(trim($row['kode_kategori'] ?? ($row['category_code'] ?? 'R1')));
            if (! isset($categories[$catCode])) {
                $category = AchievementCategory::firstOrCreate(['code' => $catCode], ['name' => "Kategori {$catCode}"]);
                $categories[$catCode] = $category;
            }
            $targetCategory = $categories[$catCode];

            $points = (int) ($row['poin_default'] ?? ($row['points'] ?? 10));
            $level = strtoupper($row['tingkat'] ?? ($row['level'] ?? 'SEKOLAH'));
            $validLevels = ['SEKOLAH', 'KECAMATAN', 'KABUPATEN', 'PROVINSI', 'NASIONAL', 'INTERNASIONAL'];
            if (! in_array($level, $validLevels)) {
                $level = 'SEKOLAH';
            }

            $item = AchievementItem::where('code', $code)->first();
            if ($item) {
                $item->update([
                    'category_id' => $targetCategory->id,
                    'name' => $name,
                    'default_points' => $points,
                    'level' => $level,
                ]);
                $updated++;
            } else {
                AchievementItem::create([
                    'category_id' => $targetCategory->id,
                    'code' => $code,
                    'name' => $name,
                    'default_points' => $points,
                    'level' => $level,
                    'is_active' => true,
                ]);
                $imported++;
            }
        }

        AuditLog::log('IMPORT_ACHIEVEMENT_ITEMS', 'AchievementItem', null, null, [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }

    /**
     * Import Violation Items (P1-P6) from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importViolationItems(array $rows): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $categories = ViolationCategory::all()->keyBy(fn ($c) => strtoupper(trim($c->code)));

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $code = strtoupper(trim($row['kode_butir'] ?? ($row['code'] ?? '')));
            $desc = trim($row['deskripsi_pelanggaran'] ?? ($row['nama'] ?? ($row['name'] ?? ($row['description'] ?? ($row['deskripsi'] ?? '')))));
            $recommendation = ! empty($row['rekomendasi_pembinaan']) ? trim($row['rekomendasi_pembinaan']) : (! empty($row['rekomendasi']) ? trim($row['rekomendasi']) : null);

            if ($code === '' || $desc === '') {
                $skipped++;
                $errors[] = "Baris {$lineNum}: Kode Butir dan Deskripsi Pelanggaran wajib diisi.";

                continue;
            }

            $catCode = strtoupper(trim($row['kode_kategori'] ?? ($row['category_code'] ?? 'P1')));
            if (! isset($categories[$catCode])) {
                $category = ViolationCategory::firstOrCreate(['code' => $catCode], ['name' => "Kategori {$catCode}"]);
                $categories[$catCode] = $category;
            }
            $targetCategory = $categories[$catCode];

            $points = (int) ($row['poin_default'] ?? ($row['points'] ?? 5));

            $item = ViolationItem::where('code', $code)->first();
            if ($item) {
                $item->update([
                    'category_id' => $targetCategory->id,
                    'name' => $desc,
                    'default_points' => $points,
                    'guidance_recommendation' => $recommendation ?? $item->guidance_recommendation,
                ]);
                $updated++;
            } else {
                ViolationItem::create([
                    'category_id' => $targetCategory->id,
                    'code' => $code,
                    'name' => $desc,
                    'default_points' => $points,
                    'guidance_recommendation' => $recommendation,
                ]);
                $imported++;
            }
        }

        AuditLog::log('IMPORT_VIOLATION_ITEMS', 'ViolationItem', null, null, [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }

    /**
     * Import Student Achievements from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importAchievements(array $rows, int $userId): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $students = Student::all()->keyBy('nis');
        $achievementItems = AchievementItem::all()->keyBy(fn ($i) => strtoupper(trim($i->code)));
        $firstCategory = AchievementCategory::first();

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $nis = trim($row['nis_siswa'] ?? ($row['nis'] ?? ''));

            if ($nis === '' || ! isset($students[$nis])) {
                $skipped++;
                $errors[] = "Baris {$lineNum}: Siswa dengan NIS '{$nis}' tidak ditemukan.";

                continue;
            }

            $student = $students[$nis];
            $itemCode = strtoupper(trim($row['kode_prestasi'] ?? ($row['kode_butir'] ?? ($row['code'] ?? ''))));
            $item = $achievementItems[$itemCode] ?? null;

            $title = trim($row['judul_prestasi'] ?? ($row['keterangan'] ?? ($row['title'] ?? ($item?->name ?? 'Prestasi Siswa'))));
            $points = ! empty($row['poin']) ? (int) $row['poin'] : ($item?->default_points ?? 10);
            $level = strtoupper(trim($row['tingkat'] ?? ($item?->level ?? 'SEKOLAH')));
            $date = ! empty($row['tanggal']) ? Carbon::parse($row['tanggal'])->toDateString() : now()->toDateString();
            $desc = $row['keterangan'] ?? ($row['deskripsi'] ?? 'Impor massal prestasi siswa.');

            Achievement::create([
                'student_id' => $student->id,
                'class_id' => $student->class_id,
                'category_id' => $item?->category_id ?? $firstCategory?->id ?? 1,
                'item_id' => $item?->id,
                'title' => $title,
                'points' => $points,
                'level' => $level,
                'date' => $date,
                'description' => $desc,
                'reporter_id' => $userId,
                'verifier_id' => $userId,
                'status' => 'DIVERIFIKASI',
            ]);
            $imported++;
        }

        AuditLog::log('IMPORT_ACHIEVEMENTS', 'Achievement', null, null, [
            'imported' => $imported,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }

    /**
     * Import Student Violations from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importViolations(array $rows, int $userId): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $students = Student::all()->keyBy('nis');
        $violationItems = ViolationItem::all()->keyBy(fn ($i) => strtoupper(trim($i->code)));
        $firstCategory = ViolationCategory::first();

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $nis = trim($row['nis_siswa'] ?? ($row['nis'] ?? ''));

            if ($nis === '' || ! isset($students[$nis])) {
                $skipped++;
                $errors[] = "Baris {$lineNum}: Siswa dengan NIS '{$nis}' tidak ditemukan.";

                continue;
            }

            $student = $students[$nis];
            $itemCode = strtoupper(trim($row['kode_pelanggaran'] ?? ($row['kode_butir'] ?? ($row['code'] ?? ''))));
            $item = $violationItems[$itemCode] ?? null;

            $title = trim($row['judul_pelanggaran'] ?? ($row['keterangan'] ?? ($row['title'] ?? ($item?->description ?? 'Pelanggaran Tata Tertib'))));
            $points = ! empty($row['poin']) ? (int) $row['poin'] : ($item?->default_points ?? 5);
            $level = strtoupper(trim($row['tingkat'] ?? ($item?->level ?? 'RINGAN')));
            $date = ! empty($row['tanggal']) ? Carbon::parse($row['tanggal'])->toDateString() : now()->toDateString();
            $desc = $row['keterangan'] ?? ($row['deskripsi'] ?? 'Impor massal pelanggaran siswa.');

            Violation::create([
                'student_id' => $student->id,
                'class_id' => $student->class_id,
                'category_id' => $item?->category_id ?? $firstCategory?->id ?? 1,
                'item_id' => $item?->id,
                'title' => $title,
                'points' => $points,
                'level' => $level,
                'date' => $date,
                'description' => $desc,
                'reporter_id' => $userId,
                'verifier_id' => $userId,
                'status' => 'DIVERIFIKASI',
            ]);
            $imported++;
        }

        AuditLog::log('IMPORT_VIOLATIONS', 'Violation', null, null, [
            'imported' => $imported,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }

    /**
     * Import Guidance Records from parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array{imported: int, updated: int, skipped: int, errors: array<string>}
     */
    public function importGuidance(array $rows, int $userId): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $students = Student::all()->keyBy('nis');

        foreach ($rows as $idx => $row) {
            $lineNum = $idx + 2;
            $nis = trim($row['nis_siswa'] ?? ($row['nis'] ?? ''));

            if ($nis === '' || ! isset($students[$nis])) {
                $skipped++;
                $errors[] = "Baris {$lineNum}: Siswa dengan NIS '{$nis}' tidak ditemukan.";

                continue;
            }

            $student = $students[$nis];
            $date = ! empty($row['tanggal']) ? Carbon::parse($row['tanggal'])->toDateString() : now()->toDateString();

            $rawType = strtoupper(trim($row['tindakan_penanganan'] ?? ($row['follow_up_type'] ?? ($row['tindakan'] ?? ''))));
            $followUpType = 'KONSELING_RUTIN';
            if (str_contains($rawType, 'SP3')) {
                $followUpType = 'SP3_BK_KESISWAAN';
            } elseif (str_contains($rawType, 'SP2')) {
                $followUpType = 'SP2_BK';
            } elseif (str_contains($rawType, 'SP1')) {
                $followUpType = 'SP1_BK';
            } elseif (str_contains($rawType, 'RAPAT')) {
                $followUpType = 'RAPAT_KHUSUS';
            } elseif (str_contains($rawType, 'WALI')) {
                $followUpType = 'BIMBINGAN_WALI_KELAS';
            } elseif (in_array($rawType, ['BIMBINGAN_WALI_KELAS', 'SP1_BK', 'SP2_BK', 'SP3_BK_KESISWAAN', 'RAPAT_KHUSUS', 'KONSELING_RUTIN'])) {
                $followUpType = $rawType;
            }

            $recommendation = trim($row['kasus_masalah'] ?? ($row['rekomendasi'] ?? ($row['recommendation'] ?? 'Pembinaan Terpadu BK')));
            $notes = trim($row['catatan'] ?? ($row['notes'] ?? 'Impor data catatan pembinaan.'));
            $status = strtoupper(trim($row['status'] ?? 'DALAM_PROSES'));
            if (! in_array($status, ['DALAM_PROSES', 'SELESAI', 'PEMANTAUAN_LANJUTAN'])) {
                $status = 'DALAM_PROSES';
            }

            GuidanceRecord::create([
                'student_id' => $student->id,
                'counselor_id' => $userId,
                'date' => $date,
                'follow_up_type' => $followUpType,
                'recommendation' => $recommendation,
                'notes' => $notes,
                'status' => $status,
            ]);
            $imported++;
        }

        AuditLog::log('IMPORT_GUIDANCE', 'GuidanceRecord', null, null, [
            'imported' => $imported,
            'skipped' => $skipped,
        ]);

        return compact('imported', 'updated', 'skipped', 'errors');
    }
}

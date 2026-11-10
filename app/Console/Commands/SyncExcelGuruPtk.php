<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Role;
use App\Models\SchoolSetting;
use App\Models\Teacher;
use App\Models\User;
use DateTime;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use ZipArchive;

#[Signature('app:sync-excel-guru-ptk {--guru= : Path to Data guru.xlsx} {--ptk= : Path to Data PTK.xlsx} {--sync-employees : Also populate employees table immediately}')]
#[Description('Lengkapi dan sinkronisasi Master Data Guru & PTK dari file Data guru.xlsx dan Data PTK.xlsx')]
class SyncExcelGuruPtk extends Command
{
    public function handle(): int
    {
        $guruPath = $this->option('guru')
            ?: (file_exists('C:\\Users\\SERVER 1 SAMN1LE\\Documents\\Data guru.xlsx')
                ? 'C:\\Users\\SERVER 1 SAMN1LE\\Documents\\Data guru.xlsx'
                : storage_path('app/master_data/Data guru.xlsx'));

        $ptkPath = $this->option('ptk')
            ?: (file_exists('C:\\Users\\SERVER 1 SAMN1LE\\Documents\\Data PTK.xlsx')
                ? 'C:\\Users\\SERVER 1 SAMN1LE\\Documents\\Data PTK.xlsx'
                : storage_path('app/master_data/Data PTK.xlsx'));

        if (! file_exists($guruPath)) {
            $this->error("File Data Guru tidak ditemukan di: {$guruPath}");

            return self::FAILURE;
        }

        if (! file_exists($ptkPath)) {
            $this->error("File Data PTK tidak ditemukan di: {$ptkPath}");

            return self::FAILURE;
        }

        $this->info('========================================================================');
        $this->info('SINKRONISASI MASTER DATA GURU & PTK DARI DOKUMEN RESMI TP 2026/2027');
        $this->info("Sumber Data Guru : {$guruPath}");
        $this->info("Sumber Data PTK  : {$ptkPath}");
        $this->info('========================================================================');

        $guruRows = $this->readXlsx($guruPath);
        $ptkRows = $this->readXlsx($ptkPath);

        // 1. Process Data Guru
        $syncedGuruCount = $this->processGuruRows($guruRows);

        // 2. Process Data PTK
        $syncedPtkCount = $this->processPtkRows($ptkRows);

        // 3. Process System Accounts (Super Admin, Admin TU, Kepala Perpus)
        $this->processSystemAccounts();

        // 4. Update School Setting for Headmaster
        $this->updateHeadmasterSetting();

        $this->info('========================================================================');
        $this->info("BERHASIL: {$syncedGuruCount} Guru dan {$syncedPtkCount} Tenaga Kependidikan (PTK)");
        $this->info('Master Data Guru & PTK serta akun login telah terupdate lengkap sesuai dokumen!');
        $this->info('========================================================================');

        return self::SUCCESS;
    }

    /**
     * Membaca worksheet sheet1 dari file .xlsx tanpa dependensi luar
     *
     * @return array<int, array<string, string>>
     */
    protected function readXlsx(string $filePath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            throw new Exception("Tidak dapat membuka file {$filePath}");
        }

        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            throw new Exception("Worksheet sheet1.xml tidak ditemukan di {$filePath}");
        }

        $xml = simplexml_load_string($sheetXml);
        $rows = [];
        if ($xml && isset($xml->sheetData->row)) {
            foreach ($xml->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $c) {
                    $colRef = preg_replace('/[0-9]/', '', (string) $c['r']);
                    $val = '';
                    if (isset($c->v)) {
                        $rawVal = (string) $c->v;
                        if (isset($c['t']) && (string) $c['t'] === 's') {
                            $val = $sharedStrings[(int) $rawVal] ?? '';
                        } else {
                            $val = $rawVal;
                        }
                    } elseif (isset($c->is->t)) {
                        $val = (string) $c->is->t;
                    }
                    $rowData[$colRef] = trim($val);
                }
                $rows[] = $rowData;
            }
        }

        $zip->close();

        return $rows;
    }

    /**
     * Process all rows from Data guru.xlsx
     *
     * @param  array<int, array<string, string>>  $rows
     */
    protected function processGuruRows(array $rows): int
    {
        $count = 0;
        $totalRows = count($rows);

        for ($i = 7; $i < $totalRows; $i++) {
            $r = $rows[$i] ?? [];
            $no = trim($r['A'] ?? '');
            $rawName = trim($r['B'] ?? '');
            if (empty($no) && empty($rawName)) {
                continue;
            }

            $nip = trim($r['C'] ?? '');
            if ($nip === '-' || empty($nip)) {
                $nip = null;
            }

            [$birthPlace, $birthDate] = $this->parseTtl($r['D'] ?? '');
            $jabatan = trim($r['E'] ?? '');
            $gol = trim($r['F'] ?? '');
            if ($gol === '-') {
                $gol = null;
            }

            $tmt = $this->parseDateValue($r['G'] ?? '');
            $mulaiKerja = $this->parseDateValue($r['H'] ?? '');

            $mkTh = is_numeric($r['I'] ?? null) ? (int) $r['I'] : null;
            $mkBln = is_numeric($r['J'] ?? null) ? (int) $r['J'] : null;

            $mapel = trim($r['M'] ?? '');
            $pendidikan = trim($r['N'] ?? '');
            $kampus = trim($r['O'] ?? '');
            $jurusan = trim($r['P'] ?? '');
            $tahunLulus = trim($r['Q'] ?? '');

            // Hitung Jenis Kelamin dari NIP (digit ke-15) atau default
            $gender = 'L';
            if ($nip && strlen($nip) >= 15) {
                $genderChar = substr($nip, 14, 1);
                $gender = ($genderChar === '2') ? 'P' : 'L';
            }

            [$cleanName, $prefix, $suffix] = $this->splitNameTitles($rawName);

            // Tentukan Judul / Jabatan di Master Data Guru
            $isKepsek = ($no == '1' || str_contains(strtolower($jabatan), 'kepala sekolah'));
            $title = $isKepsek ? 'Kepala Sekolah' : (! empty($mapel) ? "Guru {$mapel}" : 'Guru Mata Pelajaran');

            // 1. Cari / Update Guru di tabel teachers
            $teacher = $this->findExistingTeacher($nip, $cleanName, $rawName);

            // 2. Cari / Update Akun User Login
            $username = $nip ?: Str::slug($cleanName, '.');
            $email = $nip ? "{$nip}@guru.sman1lengkong.sch.id" : Str::slug($cleanName, '').'@guru.sman1lengkong.sch.id';

            $user = null;
            if ($teacher && $teacher->user_id) {
                $user = User::find($teacher->user_id);
            }
            if (! $user && $nip) {
                $user = User::where('username', $nip)->orWhere('nip', $nip)->first();
            }
            if (! $user) {
                $user = User::where('name', 'like', "%{$cleanName}%")->first();
            }

            $role = $isKepsek ? 'kepala_sekolah' : ($cleanName === 'Winda Dwi Atyani' ? 'wakasek_kesiswaan' : 'guru');
            $roles = $isKepsek ? ['kepala_sekolah', 'guru'] : ($cleanName === 'Winda Dwi Atyani' ? ['wakasek_kesiswaan', 'guru'] : ['guru']);

            if (! $user) {
                $user = User::create([
                    'name' => $rawName,
                    'nip' => $nip,
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make($nip ?: 'sman1lengkong'),
                    'role' => $role,
                    'roles' => $roles,
                    'is_active' => true,
                ]);
            } else {
                $currentRoles = is_array($user->roles) ? $user->roles : (is_string($user->roles) ? json_decode($user->roles, true) ?? [] : []);
                if (! in_array($role, $currentRoles, true)) {
                    $currentRoles[] = $role;
                }
                $user->updateQuietly([
                    'name' => $rawName,
                    'nip' => $nip ?: $user->nip,
                    'role' => $role,
                    'roles' => array_values(array_unique(array_filter($currentRoles))),
                ]);
            }

            $this->syncUserRoleModels($user, $role, $roles);

            // Update Teacher Master
            $teacherData = [
                'user_id' => $user->id,
                'name' => $rawName,
                'title_prefix' => $prefix,
                'title_suffix' => $suffix,
                'nip' => $nip,
                'gender' => $gender,
                'birth_place' => $birthPlace,
                'birth_date' => $birthDate,
                'title' => $title,
                'position' => $title,
                'employment_type' => 'Guru',
                'employment_status' => $jabatan ?: ($gol ? 'PNS' : 'GTT'),
                'rank_group' => $gol,
                'rank_tmt' => $tmt,
                'position_tmt' => $mulaiKerja,
                'years_of_service_years' => $mkTh,
                'years_of_service_months' => $mkBln,
                'teaching_subject' => $mapel,
                'education_level' => $pendidikan,
                'education_institution' => $kampus,
                'education_major' => $jurusan,
                'education_grad_year' => $tahunLulus,
                'is_active' => true,
            ];

            if ($teacher) {
                $teacher->update($teacherData);
            } else {
                $teacher = Teacher::create($teacherData);
            }

            // Update / Create Employee (Kepegawaian)
            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $nip,
                    'name' => $cleanName,
                    'title_prefix' => $prefix,
                    'title_suffix' => $suffix,
                    'gender' => $gender,
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'employment_type' => 'Guru',
                    'employment_status' => $jabatan ?: ($gol ? 'PNS' : 'GTT'),
                    'rank_group' => $gol,
                    'rank_tmt' => $tmt,
                    'position_tmt' => $mulaiKerja,
                    'years_of_service_years' => $mkTh,
                    'years_of_service_months' => $mkBln,
                    'position' => $title,
                    'teaching_subject' => $mapel,
                    'education_level' => $pendidikan,
                    'education_institution' => $kampus,
                    'education_major' => $jurusan,
                    'education_grad_year' => $tahunLulus,
                    'is_active' => true,
                    'leave_balance' => 12,
                ]
            );

            $this->line("  ✓ Guru #{$no}: {$rawName} ({$title}) [NIP: ".($nip ?: '-').']');
            $count++;
        }

        return $count;
    }

    /**
     * Process all rows from Data PTK.xlsx
     *
     * @param  array<int, array<string, string>>  $rows
     */
    protected function processPtkRows(array $rows): int
    {
        $count = 0;
        $totalRows = count($rows);

        for ($i = 7; $i < $totalRows; $i++) {
            $r = $rows[$i] ?? [];
            $no = trim($r['A'] ?? '');
            $rawName = trim($r['B'] ?? '');
            if (empty($no) && empty($rawName)) {
                continue;
            }

            $nip = trim($r['C'] ?? '');
            if ($nip === '-' || empty($nip)) {
                $nip = null;
            }

            [$birthPlace, $birthDate] = $this->parseTtl($r['D'] ?? '');
            $gender = strtoupper(trim($r['E'] ?? 'L'));
            $status = trim($r['F'] ?? '');
            $jabatan = trim(str_replace("\n", ' ', $r['G'] ?? ''));
            $gol = trim($r['H'] ?? '');
            if ($gol === '-') {
                $gol = null;
            }

            $tmt = $this->parseDateValue($r['I'] ?? '');
            $mulaiKerja = $this->parseDateValue($r['J'] ?? '');

            $mkTh = is_numeric($r['K'] ?? null) ? (int) $r['K'] : null;
            $mkBln = is_numeric($r['L'] ?? null) ? (int) $r['L'] : null;

            $pendidikan = trim($r['O'] ?? '');
            $kampus = trim($r['P'] ?? '');
            $jurusan = trim($r['Q'] ?? '');
            $tahunLulus = trim($r['R'] ?? '');

            [$cleanName, $prefix, $suffix] = $this->splitNameTitles($rawName);

            // Tentukan Peran Akun Sistem untuk Tendik
            $role = 'staff';
            $roles = ['staff'];

            $jLower = strtolower($jabatan);
            if (str_contains($jLower, 'kepegawaian') || str_contains($jLower, 'dapodik')) {
                $roles = ['staff', 'ta_kepegawaian', 'operator'];
            } elseif (str_contains($jLower, 'kesiswaan')) {
                $roles = ['staff', 'ta_kesiswaan'];
            } elseif (str_contains($jLower, 'sarana') || str_contains($jLower, 'sarpras')) {
                $roles = ['staff', 'ta_sarpras'];
            } elseif (str_contains($jLower, 'bendahara') || str_contains($jLower, 'bosp')) {
                $roles = ['staff', 'bendahara'];
            } elseif (str_contains($jLower, 'pustakawan') || str_contains($jLower, 'persuratan')) {
                $roles = ['staff', 'pustakawan', 'ta_persuratan'];
            }

            // 1. Cari / Update di teachers
            $teacher = $this->findExistingTeacher($nip, $cleanName, $rawName);

            // 2. Akun User Login
            $username = $nip ?: Str::slug($cleanName, '.');
            $email = $nip ? "{$nip}@staff.sman1lengkong.sch.id" : Str::slug($cleanName, '').'@staff.sman1lengkong.sch.id';

            $user = null;
            if ($teacher && $teacher->user_id && $teacher->user_id > 3) {
                $user = User::find($teacher->user_id);
            }
            if (! $user && $nip) {
                $user = User::where('username', $nip)->orWhere('nip', $nip)->first();
            }
            if (! $user) {
                $user = User::where('name', 'like', "%{$cleanName}%")->whereNotIn('id', [1, 2, 3])->first();
            }

            if (! $user) {
                $user = User::create([
                    'name' => $rawName,
                    'nip' => $nip,
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make($nip ?: 'sman1lengkong'),
                    'role' => $role,
                    'roles' => $roles,
                    'is_active' => true,
                ]);
            } else {
                $currentRoles = is_array($user->roles) ? $user->roles : (is_string($user->roles) ? json_decode($user->roles, true) ?? [] : []);
                $mergedRoles = array_values(array_unique(array_filter(array_merge($currentRoles, $roles))));
                $user->updateQuietly([
                    'name' => $rawName,
                    'nip' => $nip ?: $user->nip,
                    'role' => $user->role ?: $role,
                    'roles' => $mergedRoles,
                ]);
            }

            $this->syncUserRoleModels($user, $role, $roles);

            // Update Teacher Master
            $teacherData = [
                'user_id' => $user->id,
                'name' => $rawName,
                'title_prefix' => $prefix,
                'title_suffix' => $suffix,
                'nip' => $nip,
                'gender' => $gender,
                'birth_place' => $birthPlace,
                'birth_date' => $birthDate,
                'title' => $jabatan,
                'position' => $jabatan,
                'employment_type' => 'Tenaga Kependidikan',
                'employment_status' => $status ?: 'PPPK PW',
                'rank_group' => $gol,
                'rank_tmt' => $tmt,
                'position_tmt' => $mulaiKerja,
                'years_of_service_years' => $mkTh,
                'years_of_service_months' => $mkBln,
                'teaching_subject' => null,
                'education_level' => $pendidikan,
                'education_institution' => $kampus,
                'education_major' => $jurusan,
                'education_grad_year' => $tahunLulus,
                'is_active' => true,
            ];

            if ($teacher) {
                $teacher->update($teacherData);
            } else {
                $teacher = Teacher::create($teacherData);
            }

            // Update / Create Employee (Kepegawaian)
            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $nip,
                    'name' => $cleanName,
                    'title_prefix' => $prefix,
                    'title_suffix' => $suffix,
                    'gender' => $gender,
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'employment_type' => 'Tenaga Kependidikan',
                    'employment_status' => $status ?: 'PPPK PW',
                    'rank_group' => $gol,
                    'rank_tmt' => $tmt,
                    'position_tmt' => $mulaiKerja,
                    'years_of_service_years' => $mkTh,
                    'years_of_service_months' => $mkBln,
                    'position' => $jabatan,
                    'teaching_subject' => null,
                    'education_level' => $pendidikan,
                    'education_institution' => $kampus,
                    'education_major' => $jurusan,
                    'education_grad_year' => $tahunLulus,
                    'is_active' => true,
                    'leave_balance' => 12,
                ]
            );

            $this->line("  ✓ PTK #{$no}: {$rawName} ({$jabatan}) [NIP: ".($nip ?: '-').']');
            $count++;
        }

        return $count;
    }

    /**
     * Memastikan akun sistem terhubung rapi ke Teacher dan Employee
     */
    protected function processSystemAccounts(): void
    {
        // 1. Kepala Tata Usaha (RH ASENG)
        $tuTeacher = Teacher::where('name', 'like', '%RH ASENG%')->first();
        if ($tuTeacher && $tuTeacher->user_id) {
            $tuTeacher->update([
                'name' => 'RH Aseng, S.Pd.',
                'title_suffix' => 'S.Pd.',
                'employment_type' => 'Tenaga Kependidikan',
                'employment_status' => 'PNS',
                'rank_group' => 'III/d',
                'position' => 'Kepala Tata Usaha',
                'education_level' => 'S1',
            ]);

            Employee::updateOrCreate(
                ['user_id' => $tuTeacher->user_id],
                [
                    'nip' => $tuTeacher->nip,
                    'name' => 'RH Aseng, S.Pd.',
                    'title_suffix' => 'S.Pd.',
                    'gender' => 'L',
                    'employment_type' => 'Tenaga Kependidikan',
                    'employment_status' => 'PNS',
                    'rank_group' => 'III/d',
                    'position' => 'Kepala Tata Usaha',
                    'education_level' => 'S1',
                    'is_active' => true,
                    'leave_balance' => 12,
                ]
            );
        }

        // 2. Kepala Perpustakaan (Hj. Nurhayati, S.Pd.)
        $perpusTeacher = Teacher::where('name', 'like', '%Nurhayati%')->where('title', 'like', '%Perpustakaan%')->first();
        if ($perpusTeacher) {
            $perpusTeacher->update([
                'name' => 'Hj. Nurhayati, S.Pd.',
                'title_prefix' => 'Hj.',
                'title_suffix' => 'S.Pd.',
                'employment_type' => 'Tenaga Kependidikan',
                'employment_status' => 'PNS',
                'rank_group' => 'III/d',
                'position' => 'Kepala Perpustakaan Sekolah',
                'education_level' => 'S1',
            ]);

            $user = $perpusTeacher->user_id ? User::find($perpusTeacher->user_id) : User::where('name', 'like', '%Hj. Nurhayati%')->first();
            if ($user) {
                Employee::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $perpusTeacher->nip,
                        'name' => 'Hj. Nurhayati',
                        'title_prefix' => 'Hj.',
                        'title_suffix' => 'S.Pd.',
                        'gender' => 'P',
                        'employment_type' => 'Tenaga Kependidikan',
                        'employment_status' => 'PNS',
                        'rank_group' => 'III/d',
                        'position' => 'Kepala Perpustakaan Sekolah',
                        'education_level' => 'S1',
                        'is_active' => true,
                        'leave_balance' => 12,
                    ]
                );
            }
        }
    }

    /**
     * Update school setting for headmaster
     */
    protected function updateHeadmasterSetting(): void
    {
        SchoolSetting::updateOrCreate(
            ['key' => 'headmaster_name'],
            ['value' => 'Pian Sopian, S.Pd., MM.Pd.', 'group' => 'general', 'is_editable' => true]
        );

        SchoolSetting::updateOrCreate(
            ['key' => 'headmaster_nip'],
            ['value' => '198009132008011005', 'group' => 'general', 'is_editable' => true]
        );
    }

    /**
     * Mencari teacher yang sudah ada berdasarkan NIP atau nama
     */
    protected function findExistingTeacher(?string $nip, string $cleanName, string $rawName): ?Teacher
    {
        if ($nip) {
            $t = Teacher::where('nip', $nip)->first();
            if ($t) {
                return $t;
            }
        }

        $norm = strtoupper(preg_replace('/[^a-zA-Z]/', '', $cleanName));
        $all = Teacher::all();
        foreach ($all as $item) {
            $itemNorm = strtoupper(preg_replace('/[^a-zA-Z]/', '', $item->name));
            if ($itemNorm === $norm || str_contains($itemNorm, $norm) || str_contains($norm, $itemNorm)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Sinkronisasi tabel pivot roles
     *
     * @param  array<int, string>  $roles
     */
    protected function syncUserRoleModels(User $user, string $primaryRole, array $roles): void
    {
        $roleModels = Role::whereIn('name', $roles)->get();
        if ($roleModels->isNotEmpty()) {
            $user->roles()->sync($roleModels->pluck('id'));
            $primaryRoleModel = $roleModels->firstWhere('name', $primaryRole) ?? $roleModels->first();
            if ($primaryRoleModel) {
                $user->updateQuietly(['active_role_id' => $primaryRoleModel->id]);
            }
        }
    }

    /**
     * Parse serial number atau formatted string date ke Y-m-d
     */
    protected function parseDateValue(mixed $val): ?string
    {
        if (empty($val) || $val === '-') {
            return null;
        }

        $val = trim((string) $val);
        if (is_numeric($val)) {
            $timestamp = ((int) $val - 25569) * 86400;

            return gmdate('Y-m-d', $timestamp);
        }

        if (preg_match('/^(\d{1,2})[-|\/](\d{1,2})[-|\/](\d{4})$/', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        try {
            $d = new DateTime($val);

            return $d->format('Y-m-d');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Parse Tempat Tanggal Lahir (e.g. "Bandung, 13-09-1980")
     *
     * @return array{0: ?string, 1: ?string}
     */
    protected function parseTtl(mixed $ttl): array
    {
        if (empty($ttl)) {
            return [null, null];
        }

        $parts = explode(',', (string) $ttl, 2);
        $place = trim($parts[0] ?? '');
        $dateStr = trim($parts[1] ?? '');
        $date = $this->parseDateValue($dateStr);

        return [$place ?: null, $date];
    }

    /**
     * Memisahkan prefix, nama bersih, dan gelar suffix
     *
     * @return array{0: string, 1: ?string, 2: ?string}
     */
    protected function splitNameTitles(string $rawName): array
    {
        $rawName = trim($rawName);
        $prefix = null;
        $suffix = null;

        $knownPrefixes = ['Drs.', 'Dra.', 'Dr.', 'Prof.', 'Ir.', 'H.', 'Hj.'];
        foreach ($knownPrefixes as $p) {
            if (str_starts_with($rawName, $p.' ')) {
                $prefix = $p;
                $rawName = trim(substr($rawName, strlen($p)));
                break;
            }
        }

        if (str_contains($rawName, ',')) {
            $parts = explode(',', $rawName, 2);
            $cleanName = trim($parts[0]);
            $suffix = trim($parts[1]);
        } else {
            if (preg_match('/^(.*?)\s+(SE,\s*SP\..*)$/i', $rawName, $m)) {
                $cleanName = trim($m[1]);
                $suffix = trim($m[2]);
            } else {
                $cleanName = $rawName;
            }
        }

        return [$cleanName, $prefix, $suffix];
    }
}

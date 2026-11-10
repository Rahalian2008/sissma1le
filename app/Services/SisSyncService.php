<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentAttendance;
use App\Models\StudentViolation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SisSyncService
{
    protected string $baseUrl;

    protected string $username;

    protected string $password;

    protected ?string $cookieFile = null;

    protected bool $authenticated = false;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.sis_sma1le.url', 'https://sissma1le.sman1lengkong.school'), '/');
        $this->username = (string) config('services.sis_sma1le.username', 'senks');
        $this->password = (string) config('services.sis_sma1le.password', '12345678');
        $this->cookieFile = storage_path('framework/cache/sis_sma1le_cookie.txt');
    }

    /**
     * Authenticate session with SIS-SMA1LE
     */
    public function authenticate(bool $forceRefresh = false): bool
    {
        if ($this->authenticated && ! $forceRefresh) {
            return true;
        }

        if ($forceRefresh && file_exists($this->cookieFile)) {
            @unlink($this->cookieFile);
            $this->authenticated = false;
        }

        // Test existing cookie if not force refresh
        if (! $forceRefresh && file_exists($this->cookieFile)) {
            $checkHtml = $this->requestGet($this->baseUrl.'/dashboard');
            if (stripos($checkHtml, 'Dashboard') !== false && (stripos($checkHtml, 'Presensi Selfie') !== false || stripos($checkHtml, 'senks') !== false)) {
                $this->authenticated = true;

                return true;
            }
        }

        // Get login page to extract CSRF token
        $loginHtml = $this->requestGet($this->baseUrl.'/login');
        if (! preg_match('/name="_token" value="([^"]+)"/', $loginHtml, $matchToken)) {
            Log::warning('SisSyncService: Failed to retrieve CSRF token from login page.');

            return false;
        }

        $token = $matchToken[1];

        // Post login form
        $postData = [
            '_token' => $token,
            'email' => $this->username,
            'password' => $this->password,
        ];

        $responseHtml = $this->requestPost($this->baseUrl.'/login', $postData);

        if (stripos($responseHtml, 'Dashboard') !== false || stripos($responseHtml, 'Presensi Selfie') !== false) {
            $this->authenticated = true;

            return true;
        }

        // Double check by requesting dashboard with saved cookie
        $dashboardHtml = $this->requestGet($this->baseUrl.'/dashboard');
        if (stripos($dashboardHtml, 'Dashboard') !== false || stripos($dashboardHtml, 'Presensi Selfie') !== false) {
            $this->authenticated = true;

            return true;
        }

        Log::warning('SisSyncService: Authentication failed for user '.$this->username);

        return false;
    }

    /**
     * Test connection and retrieve remote status
     */
    public function testConnection(): array
    {
        $startTime = microtime(true);
        $connected = $this->authenticate(true);
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        if (! $connected) {
            return [
                'status' => 'error',
                'message' => 'Gagal terhubung ke SIS-SMA1LE. Periksa URL atau kredensial akun.',
                'url' => $this->baseUrl,
                'latency_ms' => $duration,
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Berhasil terhubung ke SIS-SMA1LE (Super Admin Terverifikasi).',
            'url' => $this->baseUrl,
            'latency_ms' => $duration,
        ];
    }

    /**
     * Synchronize Attendance from SIS-SMA1LE
     *
     * @param  string|null  $date  Y-m-d format
     */
    public function syncAttendance(?string $date = null, int $maxPages = 2): array
    {
        if (! $this->authenticate()) {
            return ['status' => 'error', 'message' => 'Autentikasi ke SIS-SMA1LE gagal.'];
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $synced = 0;
        $createdStudents = 0;
        $records = [];

        for ($page = 1; $page <= $maxPages; $page++) {
            $query = ['page' => $page];
            if ($date) {
                $query['date'] = Carbon::parse($date)->format('Y-m-d');
            }

            $url = $this->baseUrl.'/attendance/history?'.http_build_query($query);
            $html = $this->requestGet($url);

            preg_match_all('/<tr[^>]*class="[^"]*hover:[^"]*"[^>]*>(.*?)<\/tr>/si', $html, $rowMatches);

            if (empty($rowMatches[1])) {
                break;
            }

            foreach ($rowMatches[1] as $rowHtml) {
                preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $rowHtml, $tdMatches);
                $tds = $tdMatches[1] ?? [];
                if (count($tds) < 6) {
                    continue;
                }

                // 1. Date & Times
                preg_match('/<div class="font-bold[^"]*">([^<]+)<\/div>/i', $tds[0], $mDate);
                preg_match('/Masuk:\s*([^<]+)/i', $tds[0], $mIn);
                preg_match('/Pulang:\s*([^<]+)/i', $tds[0], $mOut);
                $rawDate = trim($mDate[1] ?? '');
                $timeIn = trim($mIn[1] ?? '-');
                $timeOut = trim($mOut[1] ?? '-');

                // Parse d/m/Y to Y-m-d
                $recordDate = null;
                if ($rawDate) {
                    try {
                        $recordDate = Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');
                    } catch (\Throwable) {
                        $recordDate = Carbon::parse($rawDate)->format('Y-m-d');
                    }
                }
                if (! $recordDate) {
                    continue;
                }

                // 2. Student & Class & NIS/NISN
                preg_match('/<div class="font-bold[^"]*">([^<]+)<\/div>/i', $tds[1], $mName);
                preg_match('/<span class="[^"]*font-extrabold[^"]*">([^<]+)<\/span>/i', $tds[1], $mRombel);
                preg_match('/NIS:\s*([0-9]+)/i', $tds[1], $mNis);
                $studentName = trim($mName[1] ?? '');
                $rombel = trim($mRombel[1] ?? 'Umum');
                $nis = trim($mNis[1] ?? '');

                if (empty($studentName) && empty($nis)) {
                    continue;
                }

                // 3. Method & Status
                $method = trim(strip_tags($tds[4] ?? 'SELFIE'));
                $rawStatus = strtoupper(trim(strip_tags($tds[5] ?? 'HADIR')));

                // Map status to SIKATAS enum
                $mappedStatus = match ($rawStatus) {
                    'HADIR', 'TERLAMBAT' => 'Hadir',
                    'SAKIT' => 'Sakit',
                    'IZIN' => 'Izin',
                    'DISPENSASI' => 'Dispensasi',
                    default => 'Alpa',
                };

                // Notes
                $notes = "Metode: {$method} | Masuk: {$timeIn} | Pulang: {$timeOut}";
                if ($rawStatus === 'TERLAMBAT') {
                    $notes = "Terlambat | {$notes}";
                }

                // Match or auto-create student
                $studentResult = $this->findOrCreateStudent($nis, $studentName, $rombel);
                $student = $studentResult['student'];
                if ($studentResult['created']) {
                    $createdStudents++;
                }

                $classLevel = explode(' ', $rombel)[0] ?? 'X';

                // Save or update StudentAttendance
                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'date' => $recordDate,
                    ],
                    [
                        'academic_year_id' => $activeYear?->id,
                        'class_level' => $classLevel,
                        'rombel' => $student->current_rombel ?? $rombel,
                        'status' => $mappedStatus,
                        'notes' => $notes,
                        'recorded_by_user_id' => Auth::id() ?? User::first()?->id,
                    ]
                );

                $synced++;
                $records[] = [
                    'student_name' => $student->name,
                    'nis' => $student->nis,
                    'date' => $recordDate,
                    'status' => $mappedStatus,
                    'notes' => $notes,
                ];
            }
        }

        AuditTrailService::log('create', 'absensi', "Sinkronisasi {$synced} data presensi siswa dari SIS-SMA1LE");

        return [
            'status' => 'success',
            'message' => "Berhasil menarik {$synced} rekaman presensi dari SIS-SMA1LE.",
            'synced_count' => $synced,
            'created_students' => $createdStudents,
            'records' => array_slice($records, 0, 15),
        ];
    }

    /**
     * Synchronize Achievements from SIS-SMA1LE
     */
    public function syncAchievements(): array
    {
        if (! $this->authenticate()) {
            return ['status' => 'error', 'message' => 'Autentikasi ke SIS-SMA1LE gagal.'];
        }

        $html = $this->requestGet($this->baseUrl.'/achievements');
        preg_match_all('/<tr[^>]*class="[^"]*hover:[^"]*"[^>]*>(.*?)<\/tr>/si', $html, $matches);

        if (empty($matches[1])) {
            return [
                'status' => 'success',
                'message' => 'Tidak ada data prestasi yang ditemukan di SIS-SMA1LE.',
                'synced_count' => 0,
                'created_students' => 0,
            ];
        }

        $synced = 0;
        $createdStudents = 0;
        $records = [];

        foreach ($matches[1] as $rowHtml) {
            preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $rowHtml, $tdMatches);
            $tds = $tdMatches[1] ?? [];
            if (count($tds) < 6) {
                continue;
            }

            // 1. Date & Code
            preg_match('/<div class="font-bold[^"]*">([^<]+)<\/div>/i', $tds[0], $mDate);
            preg_match('/<div class="text-\[[0-9]+px\][^"]*">([^<]+)<\/div>/i', $tds[0], $mCode);
            $rawDate = trim($mDate[1] ?? '');
            $code = trim($mCode[1] ?? '');

            $recordDate = null;
            if ($rawDate) {
                try {
                    $recordDate = Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');
                } catch (\Throwable) {
                    $recordDate = Carbon::parse($rawDate)->format('Y-m-d');
                }
            }
            if (! $recordDate) {
                $recordDate = date('Y-m-d');
            }

            // 2. Student & NIS & Class
            preg_match('/<div class="font-bold[^"]*">([^<]+)<\/div>/i', $tds[1], $mName);
            preg_match('/NIS:\s*([0-9]+)/i', $tds[1], $mNis);
            preg_match('/Kelas\s+([^•<]+)/i', $tds[1], $mClass);
            $studentName = trim($mName[1] ?? '');
            $nis = trim($mNis[1] ?? '');
            $rombel = trim($mClass[1] ?? '');

            // 3. Achievement text
            preg_match('/<span[^>]*class="[^"]*rounded[^"]*">\s*([A-Z0-9]+)\s*<\/span>\s*<span[^>]*class="font-bold[^"]*">([^<]+)<\/span>/si', $tds[2], $mAch);
            $achCode = trim($mAch[1] ?? '');
            $achName = trim($mAch[2] ?? trim(strip_tags($tds[2])));

            // 4. Level & Points & Status
            $level = trim(strip_tags($tds[3] ?? 'Sekolah'));
            $points = trim(strip_tags($tds[4] ?? '+5'));
            $status = trim(strip_tags($tds[5] ?? 'DIVERIFIKASI'));

            $studentResult = $this->findOrCreateStudent($nis, $studentName, $rombel);
            $student = $studentResult['student'];
            if ($studentResult['created']) {
                $createdStudents++;
            }

            $fullName = $achCode ? "[{$achCode}] {$achName}" : $achName;

            StudentAchievement::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'name' => $fullName,
                    'date' => $recordDate,
                ],
                [
                    'category' => 'Pembiasaan & Karakter',
                    'level' => ucfirst(strtolower($level)),
                    'rank' => "Poin {$points} ({$status})",
                    'organizer' => 'SMA Negeri 1 Lengkong (SIS)',
                    'notes' => "Kode: {$code} | Ref: {$achCode}",
                ]
            );

            $synced++;
            $records[] = [
                'student_name' => $student->name,
                'name' => $fullName,
                'date' => $recordDate,
                'points' => $points,
            ];
        }

        AuditTrailService::log('create', 'prestasi', "Sinkronisasi {$synced} data prestasi siswa dari SIS-SMA1LE");

        return [
            'status' => 'success',
            'message' => "Berhasil menarik {$synced} rekaman prestasi siswa dari SIS-SMA1LE.",
            'synced_count' => $synced,
            'created_students' => $createdStudents,
            'records' => $records,
        ];
    }

    /**
     * Synchronize Violations from SIS-SMA1LE
     */
    public function syncViolations(): array
    {
        if (! $this->authenticate()) {
            return ['status' => 'error', 'message' => 'Autentikasi ke SIS-SMA1LE gagal.'];
        }

        $html = $this->requestGet($this->baseUrl.'/violations');
        preg_match_all('/<tr[^>]*class="[^"]*hover:[^"]*"[^>]*>(.*?)<\/tr>/si', $html, $matches);

        if (empty($matches[1])) {
            return [
                'status' => 'success',
                'message' => 'Tidak ada data pelanggaran baru di SIS-SMA1LE saat ini (0 catatan).',
                'synced_count' => 0,
                'created_students' => 0,
            ];
        }

        $synced = 0;
        $createdStudents = 0;
        $records = [];

        foreach ($matches[1] as $rowHtml) {
            preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $rowHtml, $tdMatches);
            $tds = $tdMatches[1] ?? [];
            if (count($tds) < 6) {
                continue;
            }

            // 1. Date & Code
            preg_match('/<div class="font-bold[^"]*">([^<]+)<\/div>/i', $tds[0], $mDate);
            preg_match('/<div class="text-\[[0-9]+px\][^"]*">([^<]+)<\/div>/i', $tds[0], $mCode);
            $rawDate = trim($mDate[1] ?? '');
            $code = trim($mCode[1] ?? '');

            $recordDate = null;
            if ($rawDate) {
                try {
                    $recordDate = Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');
                } catch (\Throwable) {
                    $recordDate = Carbon::parse($rawDate)->format('Y-m-d');
                }
            }
            if (! $recordDate) {
                $recordDate = date('Y-m-d');
            }

            // 2. Student & NIS & Class
            preg_match('/<div class="font-bold[^"]*">([^<]+)<\/div>/i', $tds[1], $mName);
            preg_match('/NIS:\s*([0-9]+)/i', $tds[1], $mNis);
            preg_match('/Kelas\s+([^•<]+)/i', $tds[1], $mClass);
            $studentName = trim($mName[1] ?? '');
            $nis = trim($mNis[1] ?? '');
            $rombel = trim($mClass[1] ?? '');

            // 3. Violation Name
            $violationText = trim(strip_tags($tds[2] ?? 'Pelanggaran Tata Tertib'));

            // 4. Location & Points & Status
            $location = trim(strip_tags($tds[3] ?? '-'));
            $rawPoints = trim(strip_tags($tds[4] ?? '5'));
            $points = (int) preg_replace('/[^0-9]/', '', $rawPoints);
            if ($points <= 0) {
                $points = 5;
            }
            $resolutionStatus = trim(strip_tags($tds[5] ?? 'Dalam Pembinaan'));

            $studentResult = $this->findOrCreateStudent($nis, $studentName, $rombel);
            $student = $studentResult['student'];
            if ($studentResult['created']) {
                $createdStudents++;
            }

            StudentViolation::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'violation_name' => $violationText,
                    'violation_date' => $recordDate,
                ],
                [
                    'points' => $points,
                    'chronology' => "Lokasi: {$location} | Kode SIS: {$code}",
                    'action_taken' => 'Peringatan dan pembinaan guru piket/BK',
                    'resolution_status' => $resolutionStatus ?: 'Dalam Pembinaan',
                    'officer_name' => 'Guru Piket / Tim Tatib SIS',
                ]
            );

            $synced++;
            $records[] = [
                'student_name' => $student->name,
                'violation_name' => $violationText,
                'date' => $recordDate,
                'points' => $points,
            ];
        }

        AuditTrailService::log('create', 'pelanggaran', "Sinkronisasi {$synced} data pelanggaran siswa dari SIS-SMA1LE");

        return [
            'status' => 'success',
            'message' => "Berhasil menarik {$synced} rekaman pelanggaran dari SIS-SMA1LE.",
            'synced_count' => $synced,
            'created_students' => $createdStudents,
            'records' => $records,
        ];
    }

    /**
     * Synchronize Master Student Data from SIS-SMA1LE
     */
    public function syncMasterStudents(int $maxPages = 3): array
    {
        if (! $this->authenticate()) {
            return ['status' => 'error', 'message' => 'Autentikasi ke SIS-SMA1LE gagal.'];
        }

        $synced = 0;
        $created = 0;

        for ($page = 1; $page <= $maxPages; $page++) {
            $url = $this->baseUrl."/master/students?page={$page}";
            $html = $this->requestGet($url);

            preg_match_all('/<tr[^>]*class="[^"]*hover:[^"]*"[^>]*>(.*?)<\/tr>/si', $html, $rowMatches);
            if (empty($rowMatches[1])) {
                break;
            }

            foreach ($rowMatches[1] as $rowHtml) {
                preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $rowHtml, $tdMatches);
                $tds = $tdMatches[1] ?? [];
                if (count($tds) < 5) {
                    continue;
                }

                // Name & Birthplace
                preg_match('/<div class="font-bold[^"]*">([^<]+)<\/div>/i', $tds[0], $mName);
                $studentName = trim($mName[1] ?? '');

                // NIS & NISN
                preg_match('/NIS:\s*([0-9]+)/i', $tds[1], $mNis);
                preg_match('/NISN:\s*([0-9]+)/i', $tds[1], $mNisn);
                $nis = trim($mNis[1] ?? '');
                $nisn = trim($mNisn[1] ?? '');

                // Gender
                $genderText = trim(strip_tags($tds[2] ?? 'L'));
                $gender = (stripos($genderText, 'Perempuan') !== false || $genderText === 'P') ? 'P' : 'L';

                // Class
                $classText = trim(strip_tags($tds[3] ?? 'X'));
                $level = explode(' ', $classText)[0] ?? 'X';

                if (empty($studentName) && empty($nis)) {
                    continue;
                }

                $lookup = $nisn ?: $nis;
                $res = $this->findOrCreateStudent($lookup, $studentName, $classText, $gender, $level);
                if ($res['created']) {
                    $created++;
                }
                $synced++;
            }
        }

        AuditTrailService::log('create', 'siswa', "Sinkronisasi {$synced} master siswa dari SIS-SMA1LE ({$created} siswa baru ditambahkan ke Buku Induk)");

        return [
            'status' => 'success',
            'message' => "Sinkronisasi Master Siswa selesai: {$synced} siswa diproses, {$created} siswa baru ditambahkan ke Buku Induk.",
            'synced_count' => $synced,
            'created_count' => $created,
        ];
    }

    /**
     * Find existing student or create a new student record
     */
    public function findOrCreateStudent(string $nisnOrNis, string $name, ?string $rombel = null, string $gender = 'L', ?string $level = null): array
    {
        $student = null;

        if (! empty($nisnOrNis)) {
            $student = Student::where('nisn', $nisnOrNis)
                ->orWhere('nis', $nisnOrNis)
                ->first();
        }

        if (! $student && ! empty($name)) {
            $student = Student::where('name', $name)
                ->orWhere('name', 'like', "%{$name}%")
                ->first();
        }

        if ($student) {
            // Update rombel if missing
            if ($rombel && empty($student->current_rombel)) {
                $student->update(['current_rombel' => $rombel]);
            }

            return ['student' => $student, 'created' => false];
        }

        // Auto-create new student in SIKATAS Buku Induk
        $classLevel = $level ?? (explode(' ', (string) $rombel)[0] ?? 'X');
        $newNis = $nisnOrNis ?: ('SIS-'.rand(10000, 99999));
        $newNisn = (strlen($nisnOrNis) === 10) ? $nisnOrNis : null;

        $student = Student::create([
            'nis' => $newNis,
            'nisn' => $newNisn,
            'name' => $name,
            'gender' => $gender,
            'birth_place' => 'Sukabumi',
            'birth_date' => '2008-01-01',
            'religion' => 'Islam',
            'current_class_level' => $classLevel,
            'current_rombel' => $rombel ?: "{$classLevel} Merdeka 1",
            'status' => 'Aktif',
            'address' => 'Lengkong, Sukabumi',
        ]);

        return ['student' => $student, 'created' => true];
    }

    /**
     * Internal cURL GET helper
     */
    protected function requestGet(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) SIKATAS-Bridge/1.0');
        $res = curl_exec($ch);
        curl_close($ch);

        return is_string($res) ? $res : '';
    }

    /**
     * Internal cURL POST helper
     */
    protected function requestPost(string $url, array $data): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) SIKATAS-Bridge/1.0');
        $res = curl_exec($ch);
        curl_close($ch);

        return is_string($res) ? $res : '';
    }
}

<?php

namespace App\Services;

use App\Models\CbtExam;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Support\Facades\Schema;

class CbtDocumentService
{
    /**
     * Ambil metadata identitas resmi sekolah untuk kop surat dan dokumen ujian.
     *
     * @return array{
     *     name: string,
     *     npsn: string,
     *     address: string,
     *     headmaster_name: string,
     *     headmaster_nip: string,
     *     logo_url: string|null,
     *     kop_image_url: string|null,
     *     city: string,
     *     province: string
     * }
     */
    public function getSchoolMetadata(): array
    {
        $identity = SchoolIdentity::first();

        $logoPath = SchoolSetting::get('school_logo');
        $kopPath = SchoolSetting::get('school_kop') ?: SchoolSetting::get('kop_surat_image');

        return [
            'name' => $identity?->name ?? SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'npsn' => $identity?->npsn ?? SchoolSetting::get('school_npsn', '20202265'),
            'address' => $identity?->address ?? SchoolSetting::get('school_address', 'Jl. Raya Lengkong No. 1, Desa Lengkong'),
            'headmaster_name' => $identity?->headmaster_name ?? SchoolSetting::get('headmaster_name', 'Pian Sopian, S.Pd., M.M.Pd'),
            'headmaster_nip' => $identity?->headmaster_nip ?? SchoolSetting::get('headmaster_nip', '198009132008011005'),
            'logo_url' => $logoPath ? asset('storage/'.$logoPath) : ($identity?->logo_path ? asset('storage/'.$identity->logo_path) : null),
            'kop_image_url' => $kopPath ? asset('storage/'.$kopPath) : null,
            'city' => $identity?->city ?? 'Kabupaten Sukabumi',
            'province' => $identity?->province ?? 'Jawa Barat',
        ];
    }

    /**
     * Siapkan seluruh data untuk Cetak Dokumen Kisi-Kisi Soal.
     */
    public function prepareKisiKisiData(CbtExam $exam): array
    {
        $exam->loadMissing(['subject', 'teacher.user', 'questions', 'classes']);

        return [
            'school' => $this->getSchoolMetadata(),
            'exam' => $exam,
            'questions' => $exam->questions,
            'classes' => $exam->classes,
            'summary' => [
                'total_questions' => $exam->questions->count(),
                'multiple_choice' => $exam->questions->where('question_type', 'multiple_choice')->count(),
                'complex' => $exam->questions->where('question_type', 'complex_multiple_choice')->count(),
                'true_false' => $exam->questions->where('question_type', 'true_false')->count(),
                'matching' => $exam->questions->where('question_type', 'matching')->count(),
                'short_answer' => $exam->questions->where('question_type', 'short_answer')->count(),
                'essay' => $exam->questions->where('question_type', 'essay')->count(),
                'l1' => $exam->questions->where('cognitive_level', 'L1')->count(),
                'l2' => $exam->questions->where('cognitive_level', 'L2')->count(),
                'l3' => $exam->questions->where('cognitive_level', 'L3')->count(),
            ],
        ];
    }

    /**
     * Siapkan seluruh data untuk Cetak Dokumen Kartu Soal.
     */
    public function prepareKartuSoalData(CbtExam $exam): array
    {
        $exam->loadMissing(['subject', 'teacher.user', 'questions', 'classes']);

        return [
            'school' => $this->getSchoolMetadata(),
            'exam' => $exam,
            'questions' => $exam->questions,
        ];
    }

    /**
     * Ambil daftar ruang unik yang tersedia untuk paket ujian ini.
     *
     * @return array<int, string>
     */
    public function getAvailableRooms(CbtExam $exam): array
    {
        $sessionRooms = [];

        if (Schema::hasTable('cbt_sessions') && Schema::hasColumn('cbt_sessions', 'room_name')) {
            $sessionRooms = $exam->sessions()
                ->whereNotNull('room_name')
                ->where('room_name', '!=', '')
                ->distinct()
                ->pluck('room_name')
                ->toArray();
        }

        $configuredRooms = (Schema::hasTable('cbt_exams') && Schema::hasColumn('cbt_exams', 'available_rooms') && is_array($exam->available_rooms))
            ? $exam->available_rooms
            : [];
        $merged = array_unique(array_merge($configuredRooms, $sessionRooms));
        sort($merged);

        return ! empty($merged) ? array_values($merged) : ['Ruang 01'];
    }

    /**
     * Otomatis membagi siswa ke ruang ujian (bisa selang-seling rombel / zig-zag maupun bertahap).
     *
     * @param  array<int, string>  $rooms
     */
    public function distributeStudentsToRooms(CbtExam $exam, array $rooms, int $capacity = 20, string $mode = 'zigzag'): int
    {
        $exam->loadMissing(['classes.students']);
        $classes = $exam->classes;

        if (empty($rooms)) {
            $rooms = ['Ruang 01'];
        }

        // Ambil seluruh siswa aktif dari rombel yang dipilih
        $studentsByClass = [];
        foreach ($classes as $cls) {
            $activeStudents = $cls->students()->where('is_active', true)->orderBy('name')->get();
            if ($activeStudents->isNotEmpty()) {
                $studentsByClass[$cls->id] = $activeStudents->values()->all();
            }
        }

        $allStudentsSorted = [];
        if ($mode === 'zigzag' && count($studentsByClass) > 1) {
            // Selang-seling siswa per rombel agar dalam satu ruang terdapat campuran rombel berbeda (misal 10A dan 11A)
            $classKeys = array_keys($studentsByClass);
            $maxCount = max(array_map('count', $studentsByClass));
            for ($i = 0; $i < $maxCount; $i++) {
                foreach ($classKeys as $cKey) {
                    if (isset($studentsByClass[$cKey][$i])) {
                        $allStudentsSorted[] = $studentsByClass[$cKey][$i];
                    }
                }
            }
        } else {
            foreach ($studentsByClass as $sList) {
                foreach ($sList as $s) {
                    $allStudentsSorted[] = $s;
                }
            }
        }

        $assignedCount = 0;
        $roomIndex = 0;
        $currentRoomCount = 0;

        foreach ($allStudentsSorted as $student) {
            $currentRoom = $rooms[$roomIndex % count($rooms)];

            $session = $exam->sessions()->firstOrCreate(
                ['student_id' => $student->id],
                [
                    'status' => 'not_started',
                    'remaining_seconds' => $exam->duration_minutes * 60,
                ]
            );

            $session->ensureCredentials();
            if (Schema::hasTable('cbt_sessions') && Schema::hasColumn('cbt_sessions', 'room_name')) {
                $session->update([
                    'room_name' => $currentRoom,
                ]);
            }

            $assignedCount++;
            $currentRoomCount++;

            if ($currentRoomCount >= $capacity && ($roomIndex + 1) < count($rooms)) {
                $roomIndex++;
                $currentRoomCount = 0;
            }
        }

        $exam->update([
            'available_rooms' => array_values(array_unique($rooms)),
        ]);

        return $assignedCount;
    }

    /**
     * Siapkan seluruh data untuk Cetak Kartu Peserta Ujian (per rombel atau seluruh peserta).
     */
    public function prepareKartuPesertaData(CbtExam $exam, ?int $classId = null, ?string $roomName = null): array
    {
        $exam->loadMissing(['subject', 'classes']);

        // Query siswa dari kelas terkait
        $query = Student::with(['schoolClass', 'user'])->where('is_active', true);

        if ($classId) {
            $query->where('class_id', $classId);
        } else {
            $classIds = $exam->classes->pluck('id')->toArray();
            if (! empty($classIds)) {
                $query->whereIn('class_id', $classIds);
            }
        }

        $students = $query->orderBy('class_id')->orderBy('name')->get();

        // Pastikan setiap siswa memiliki sesi CBT / akun peserta
        $cards = [];
        $hasRoomCol = Schema::hasTable('cbt_sessions') && Schema::hasColumn('cbt_sessions', 'room_name');

        foreach ($students as $student) {
            $session = $exam->sessions()->firstOrCreate(
                ['student_id' => $student->id],
                [
                    'status' => 'not_started',
                    'remaining_seconds' => $exam->duration_minutes * 60,
                ]
            );

            $session->ensureCredentials();

            // Jika filter per ruang aktif, lewati yang tidak sesuai ruang
            if ($hasRoomCol && $roomName && ($session->room_name ?? null) !== $roomName) {
                continue;
            }

            $roomLabel = ($hasRoomCol && ! empty($session->room_name))
                ? $session->room_name
                : ('Ruang '.($student->schoolClass?->name ?? '01'));

            $cards[] = [
                'student' => $student,
                'session' => $session,
                'participant_number' => 'UJN-'.$student->nis.'-'.sprintf('%03d', $exam->id),
                'username' => $session->cbt_username,
                'password' => $session->cbt_password,
                'token' => $exam->token,
                'room' => $roomLabel,
            ];
        }

        return [
            'school' => $this->getSchoolMetadata(),
            'exam' => $exam,
            'cards' => $cards,
            'selectedClass' => $classId ? SchoolClass::find($classId) : null,
            'selectedRoom' => $roomName,
            'availableRooms' => $this->getAvailableRooms($exam),
        ];
    }

    /**
     * Siapkan data untuk Cetak Daftar Hadir Ujian.
     */
    public function prepareDaftarHadirData(CbtExam $exam, ?int $classId = null, ?string $roomName = null, bool $isManual = false): array
    {
        $exam->loadMissing(['subject', 'teacher.user', 'classes']);

        $availableRooms = $this->getAvailableRooms($exam);
        $selectedClass = $classId ? SchoolClass::find($classId) : null;
        $hasRoomCol = Schema::hasTable('cbt_sessions') && Schema::hasColumn('cbt_sessions', 'room_name');

        if ($isManual) {
            // Generate 30 baris manual kosong (blanko untuk pengawas)
            $rows = [];
            for ($i = 1; $i <= 30; $i++) {
                $rows[] = [
                    'no' => $i,
                    'participant_number' => '.........................',
                    'student' => null,
                    'nis' => '...................',
                    'name' => '..................................................................',
                    'class_name' => '............',
                    'has_attended' => false,
                    'session' => null,
                ];
            }

            return [
                'school' => $this->getSchoolMetadata(),
                'exam' => $exam,
                'selectedClass' => $selectedClass,
                'selectedRoom' => $roomName ?: ($availableRooms[0] ?? 'Ruang 01'),
                'availableRooms' => $availableRooms,
                'is_manual' => true,
                'rows' => $rows,
                'stats' => [
                    'total' => 30,
                    'present' => 0,
                    'absent' => 0,
                ],
            ];
        }

        $sessionQuery = $exam->sessions()->with(['student.schoolClass']);
        if ($hasRoomCol && $roomName) {
            $sessionQuery->where('room_name', $roomName);
        }
        if ($classId) {
            $sessionQuery->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $sessions = $sessionQuery->get();

        // Jika belum ada sesi terdaftar sama sekali, inisialisasi sesi dari rombel ujian
        if ($sessions->isEmpty()) {
            $this->prepareKartuPesertaData($exam, $classId, $roomName);
            $sessions = $sessionQuery->get();
        }

        $rows = [];
        foreach ($sessions as $index => $sess) {
            $student = $sess->student;
            if (! $student) {
                continue;
            }

            $hasAttended = in_array($sess->status, ['in_progress', 'submitted', 'blocked', 'timed_out']);

            $rows[] = [
                'no' => $index + 1,
                'student' => $student,
                'class_name' => $student->schoolClass?->name ?? '-',
                'participant_number' => 'UJN-'.$student->nis.'-'.sprintf('%03d', $exam->id),
                'has_attended' => $hasAttended,
                'session' => $sess,
            ];
        }

        return [
            'school' => $this->getSchoolMetadata(),
            'exam' => $exam,
            'selectedClass' => $selectedClass,
            'selectedRoom' => $roomName,
            'availableRooms' => $availableRooms,
            'is_manual' => false,
            'rows' => $rows,
            'stats' => [
                'total' => count($rows),
                'present' => count(array_filter($rows, fn ($r) => $r['has_attended'])),
                'absent' => count(array_filter($rows, fn ($r) => ! $r['has_attended'])),
            ],
        ];
    }

    /**
     * Siapkan data untuk Cetak Berita Acara Pelaksanaan Ujian (BAP).
     */
    public function prepareBeritaAcaraData(CbtExam $exam, ?int $classId = null, ?string $roomName = null, bool $isManual = false): array
    {
        $exam->loadMissing(['subject', 'teacher.user', 'classes']);

        $availableRooms = $this->getAvailableRooms($exam);
        $selectedClass = $classId ? SchoolClass::find($classId) : null;
        $hasRoomCol = Schema::hasTable('cbt_sessions') && Schema::hasColumn('cbt_sessions', 'room_name');

        if ($isManual) {
            return [
                'school' => $this->getSchoolMetadata(),
                'exam' => $exam,
                'selectedClass' => $selectedClass,
                'selectedRoom' => $roomName ?: ($availableRooms[0] ?? 'Ruang 01'),
                'availableRooms' => $availableRooms,
                'is_manual' => true,
                'total_registered' => '......',
                'total_present' => '......',
                'total_absent' => '......',
                'absent_students' => [],
            ];
        }

        $sessionQuery = $exam->sessions()->with(['student.schoolClass']);
        if ($hasRoomCol && $roomName) {
            $sessionQuery->where('room_name', $roomName);
        }
        if ($classId) {
            $sessionQuery->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $sessions = $sessionQuery->get();

        $presentList = [];
        $absentList = [];

        foreach ($sessions as $sess) {
            $student = $sess->student;
            if (! $student) {
                continue;
            }

            $isSubmittedOrOngoing = in_array($sess->status, ['in_progress', 'submitted', 'blocked', 'timed_out']);

            if ($isSubmittedOrOngoing) {
                $presentList[] = $student;
            } else {
                $absentList[] = $student;
            }
        }

        return [
            'school' => $this->getSchoolMetadata(),
            'exam' => $exam,
            'selectedClass' => $selectedClass,
            'selectedRoom' => $roomName,
            'availableRooms' => $availableRooms,
            'is_manual' => false,
            'total_registered' => count($sessions),
            'total_present' => count($presentList),
            'total_absent' => count($absentList),
            'absent_students' => $absentList,
        ];
    }
}

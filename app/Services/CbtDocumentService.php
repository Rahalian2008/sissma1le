<?php

namespace App\Services;

use App\Models\CbtExam;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use App\Models\Student;

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
     * Siapkan seluruh data untuk Cetak Kartu Peserta Ujian (per rombel atau seluruh peserta).
     */
    public function prepareKartuPesertaData(CbtExam $exam, ?int $classId = null): array
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
        foreach ($students as $student) {
            $session = $exam->sessions()->firstOrCreate(
                ['student_id' => $student->id],
                [
                    'status' => 'not_started',
                    'remaining_seconds' => $exam->duration_minutes * 60,
                ]
            );

            $session->ensureCredentials();

            $cards[] = [
                'student' => $student,
                'session' => $session,
                'participant_number' => 'UJN-'.$student->nis.'-'.sprintf('%03d', $exam->id),
                'username' => $session->cbt_username,
                'password' => $session->cbt_password,
                'token' => $exam->token,
                'room' => 'Ruang '.$student->schoolClass?->name,
            ];
        }

        return [
            'school' => $this->getSchoolMetadata(),
            'exam' => $exam,
            'cards' => $cards,
            'selectedClass' => $classId ? SchoolClass::find($classId) : null,
        ];
    }

    /**
     * Siapkan data untuk Cetak Daftar Hadir Ujian.
     */
    public function prepareDaftarHadirData(CbtExam $exam, ?int $classId = null): array
    {
        $exam->loadMissing(['subject', 'teacher.user', 'classes']);

        $query = Student::with(['schoolClass'])->where('is_active', true);

        if ($classId) {
            $query->where('class_id', $classId);
            $selectedClass = SchoolClass::find($classId);
        } else {
            $classIds = $exam->classes->pluck('id')->toArray();
            if (! empty($classIds)) {
                $query->whereIn('class_id', $classIds);
            }
            $selectedClass = $exam->classes->first();
        }

        $students = $query->orderBy('name')->get();

        // Ambil sesi ujian untuk cek status kehadiran
        $sessions = $exam->sessions()->whereIn('student_id', $students->pluck('id'))->get()->keyBy('student_id');

        $rows = [];
        foreach ($students as $index => $student) {
            $sess = $sessions->get($student->id);
            $hasAttended = $sess && in_array($sess->status, ['in_progress', 'submitted', 'blocked', 'timed_out']);

            $rows[] = [
                'no' => $index + 1,
                'student' => $student,
                'participant_number' => 'UJN-'.$student->nis.'-'.sprintf('%03d', $exam->id),
                'has_attended' => $hasAttended,
                'session' => $sess,
            ];
        }

        return [
            'school' => $this->getSchoolMetadata(),
            'exam' => $exam,
            'selectedClass' => $selectedClass,
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
    public function prepareBeritaAcaraData(CbtExam $exam, ?int $classId = null): array
    {
        $exam->loadMissing(['subject', 'teacher.user', 'classes']);

        $query = Student::with('schoolClass')->where('is_active', true);

        if ($classId) {
            $query->where('class_id', $classId);
            $selectedClass = SchoolClass::find($classId);
        } else {
            $classIds = $exam->classes->pluck('id')->toArray();
            if (! empty($classIds)) {
                $query->whereIn('class_id', $classIds);
            }
            $selectedClass = $exam->classes->first();
        }

        $students = $query->orderBy('name')->get();
        $sessions = $exam->sessions()->whereIn('student_id', $students->pluck('id'))->get()->keyBy('student_id');

        $presentList = [];
        $absentList = [];

        foreach ($students as $student) {
            $sess = $sessions->get($student->id);
            $isSubmittedOrOngoing = $sess && in_array($sess->status, ['in_progress', 'submitted', 'blocked', 'timed_out']);

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
            'total_registered' => $students->count(),
            'total_present' => count($presentList),
            'total_absent' => count($absentList),
            'absent_students' => $absentList,
        ];
    }
}

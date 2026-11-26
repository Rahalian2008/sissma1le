<?php

namespace App\Http\Controllers;

use App\Models\BkCounselingSession;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\HigherEducationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BkCounselingSessionController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);

        $hasCareerPath = Schema::hasColumn('bk_counseling_sessions', 'career_path');
        $hasFollowUpDate = Schema::hasColumn('bk_counseling_sessions', 'follow_up_date');

        $query = BkCounselingSession::with(['student.schoolClass', 'counselor'])
            ->latest('date');

        if ($isHomeroomOnly) {
            $homeroomClass = $user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first();
            if ($homeroomClass) {
                $query->whereHas('student', fn ($sq) => $sq->where('class_id', $homeroomClass->id));
            }
        }

        if ($request->filled('field_of_service')) {
            $query->where('field_of_service', $request->field_of_service);
        }

        if ($hasCareerPath && $request->filled('career_path')) {
            $query->where('career_path', $request->career_path);
        }

        if ($request->filled('session_type')) {
            $query->where('session_type', $request->session_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn ($sq) => $sq->where('class_id', $request->class_id));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $hasCareerPath) {
                $q->where('topic', 'like', "%{$search}%")
                    ->orWhere('session_code', 'like', "%{$search}%");

                if ($hasCareerPath) {
                    $q->orWhere('campus_name', 'like', "%{$search}%")
                        ->orWhere('study_program', 'like', "%{$search}%")
                        ->orWhere('work_field', 'like', "%{$search}%");
                }

                $q->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
            });
        }

        $sessions = $query->paginate(15)->withQueryString();
        $classes = SchoolClass::orderBy('name')->get();

        // Statistik Cepat untuk Baris Header (Resilient jika kolom baru belum ter-migrate)
        $summary = [
            'total' => BkCounselingSession::count(),
            'career' => BkCounselingSession::where('field_of_service', 'KARIER')->count(),
            'college' => $hasCareerPath ? BkCounselingSession::where('field_of_service', 'KARIER')->where('career_path', 'KULIAH')->count() : 0,
            'work' => $hasCareerPath ? BkCounselingSession::where('field_of_service', 'KARIER')->where('career_path', 'BEKERJA')->count() : 0,
            'upcoming_rtl' => $hasFollowUpDate ? BkCounselingSession::whereNotNull('follow_up_date')->whereDate('follow_up_date', '>=', Carbon::today())->count() : 0,
        ];

        return view('bk.counseling.index', compact('sessions', 'classes', 'summary'));
    }

    public function create(Request $request): View
    {
        $selectedStudent = $request->filled('student_id') ? Student::with('schoolClass')->find($request->student_id) : null;
        $classes = SchoolClass::with('students')->orderBy('name')->get();
        $students = Student::where('is_active', true)->orderBy('name')->get();
        $universities = HigherEducationService::getJavaUniversities();
        $standardMajors = HigherEducationService::getAllStandardMajors();

        return view('bk.counseling.create', compact('selectedStudent', 'classes', 'students', 'universities', 'standardMajors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'session_type' => ['required', 'in:INDIVIDUAL,KELOMPOK,KLASIKAL,MEDIASI,KONFERENSI_KASUS,KOLABORASI,REFERRAL'],
            'field_of_service' => ['required', 'in:PRIBADI,SOSIAL,BELAJAR,KARIER'],
            'career_path' => ['nullable', 'string', 'in:KULIAH,BEKERJA'],
            'study_admission_track' => ['nullable', 'string', 'max:50'],
            'campus_name' => ['nullable', 'string', 'max:191'],
            'study_program' => ['nullable', 'string', 'max:191'],
            'campus_choices' => ['nullable', 'array'],
            'campus_choices.*.campus' => ['nullable', 'string', 'max:191'],
            'campus_choices.*.major' => ['nullable', 'string', 'max:191'],
            'campus_choices.*.order' => ['nullable', 'integer'],
            'work_field' => ['nullable', 'string', 'max:191'],
            'service_category' => ['required', 'in:LAYANAN_DASAR,LAYANAN_RESPONSIF,PERENCANAAN_INDIVIDUAL,DUKUNGAN_SISTEM'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'place' => ['nullable', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
            'problem_description' => ['required', 'string'],
            'counseling_approach' => ['nullable', 'string', 'max:255'],
            'agreement_result' => ['nullable', 'string'],
            'follow_up_plan' => ['nullable', 'string'],
            'follow_up_date' => ['nullable', 'date'],
            'follow_up_status' => ['nullable', 'string', 'in:PENDING,SELESAI'],
            'status' => ['required', 'in:TERJADWAL,SEDANG_BERLANGSUNG,SELESAI,DIRUJUK'],
            'is_confidential' => ['nullable', 'boolean'],
        ]);

        // Sanitasi field peminatan karier sesuai pilihan & olah pilihan perguruan tinggi
        if ($validated['field_of_service'] !== 'KARIER') {
            $validated['career_path'] = null;
            $validated['study_admission_track'] = null;
            $validated['campus_name'] = null;
            $validated['study_program'] = null;
            $validated['campus_choices'] = null;
            $validated['work_field'] = null;
        } elseif (($validated['career_path'] ?? null) === 'KULIAH') {
            $validated['work_field'] = null;

            // Olah pilihan multi-kampus & prodi
            $rawChoices = $request->input('campus_choices', []);
            $cleanedChoices = [];
            if (is_array($rawChoices)) {
                foreach ($rawChoices as $idx => $choice) {
                    $cName = trim($choice['campus'] ?? '');
                    $mName = trim($choice['major'] ?? '');
                    if ($cName !== '' || $mName !== '') {
                        $cleanedChoices[] = [
                            'order' => (int) ($choice['order'] ?? ($idx + 1)),
                            'campus' => $cName,
                            'major' => $mName,
                        ];
                    }
                }
            }

            if (! empty($cleanedChoices)) {
                $validated['campus_choices'] = $cleanedChoices;
                $validated['campus_name'] = $cleanedChoices[0]['campus'] ?? null;
                $validated['study_program'] = $cleanedChoices[0]['major'] ?? null;
            } elseif (! empty($validated['campus_name']) || ! empty($validated['study_program'])) {
                $validated['campus_choices'] = [
                    [
                        'order' => 1,
                        'campus' => $validated['campus_name'] ?? '',
                        'major' => $validated['study_program'] ?? '',
                    ],
                ];
            } else {
                $validated['campus_choices'] = null;
            }
        } elseif (($validated['career_path'] ?? null) === 'BEKERJA') {
            $validated['study_admission_track'] = null;
            $validated['campus_name'] = null;
            $validated['study_program'] = null;
            $validated['campus_choices'] = null;
        }

        $sessionCode = 'BK-'.date('Ym').'-'.strtoupper(Str::random(5));
        while (BkCounselingSession::where('session_code', $sessionCode)->exists()) {
            $sessionCode = 'BK-'.date('Ym').'-'.strtoupper(Str::random(5));
        }

        // Amankan attributes hanya untuk kolom yang benar-benar ada di tabel (resilient)
        $tableColumns = Schema::getColumnListing('bk_counseling_sessions');
        $attributesToSave = array_intersect_key([
            ...$validated,
            'session_code' => $sessionCode,
            'counselor_id' => Auth::id(),
            'place' => (! empty($validated['place'])) ? $validated['place'] : 'Ruang Bimbingan Konseling',
            'is_confidential' => $request->boolean('is_confidential', true),
            'follow_up_status' => $validated['follow_up_status'] ?? 'PENDING',
        ], array_flip($tableColumns));

        $session = BkCounselingSession::create($attributesToSave);

        // Kirim Notifikasi RTL ke Koordinator BK dan Guru BK jika tanggal pertemuan diisi
        if (! empty($session->follow_up_date)) {
            $this->sendFollowUpNotifications($session);
        }

        return redirect()->route('bk.counseling.show', $session)
            ->with('success', "Sesi Bimbingan & Konseling [{$session->session_code}] berhasil dicatat.");
    }

    public function show(BkCounselingSession $session): View
    {
        $user = Auth::user();
        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        if ($isHomeroomOnly) {
            $homeroomClass = $user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first();
            if ($homeroomClass && $session->student && $session->student->class_id !== $homeroomClass->id) {
                abort(403, 'Akses terbatas untuk catatan konseling siswa kelas binaan Anda.');
            }
        }

        $session->load(['student.schoolClass', 'counselor']);

        return view('bk.counseling.show', compact('session'));
    }

    public function edit(BkCounselingSession $session): View
    {
        $session->load(['student.schoolClass']);
        $classes = SchoolClass::with('students')->orderBy('name')->get();
        $students = Student::where('is_active', true)->orderBy('name')->get();
        $universities = HigherEducationService::getJavaUniversities();
        $standardMajors = HigherEducationService::getAllStandardMajors();

        return view('bk.counseling.edit', compact('session', 'classes', 'students', 'universities', 'standardMajors'));
    }

    public function update(Request $request, BkCounselingSession $session): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['sometimes', 'exists:students,id'],
            'session_type' => ['required', 'in:INDIVIDUAL,KELOMPOK,KLASIKAL,MEDIASI,KONFERENSI_KASUS,KOLABORASI,REFERRAL'],
            'field_of_service' => ['required', 'in:PRIBADI,SOSIAL,BELAJAR,KARIER'],
            'career_path' => ['nullable', 'string', 'in:KULIAH,BEKERJA'],
            'study_admission_track' => ['nullable', 'string', 'max:50'],
            'campus_name' => ['nullable', 'string', 'max:191'],
            'study_program' => ['nullable', 'string', 'max:191'],
            'campus_choices' => ['nullable', 'array'],
            'campus_choices.*.campus' => ['nullable', 'string', 'max:191'],
            'campus_choices.*.major' => ['nullable', 'string', 'max:191'],
            'campus_choices.*.order' => ['nullable', 'integer'],
            'work_field' => ['nullable', 'string', 'max:191'],
            'service_category' => ['required', 'in:LAYANAN_DASAR,LAYANAN_RESPONSIF,PERENCANAAN_INDIVIDUAL,DUKUNGAN_SISTEM'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'place' => ['nullable', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
            'problem_description' => ['required', 'string'],
            'counseling_approach' => ['nullable', 'string', 'max:255'],
            'agreement_result' => ['nullable', 'string'],
            'follow_up_plan' => ['nullable', 'string'],
            'follow_up_date' => ['nullable', 'date'],
            'follow_up_status' => ['nullable', 'string', 'in:PENDING,SELESAI'],
            'status' => ['required', 'in:TERJADWAL,SEDANG_BERLANGSUNG,SELESAI,DIRUJUK'],
            'is_confidential' => ['nullable', 'boolean'],
        ]);

        if ($validated['field_of_service'] !== 'KARIER') {
            $validated['career_path'] = null;
            $validated['study_admission_track'] = null;
            $validated['campus_name'] = null;
            $validated['study_program'] = null;
            $validated['campus_choices'] = null;
            $validated['work_field'] = null;
        } elseif (($validated['career_path'] ?? null) === 'KULIAH') {
            $validated['work_field'] = null;

            $rawChoices = $request->input('campus_choices', []);
            $cleanedChoices = [];
            if (is_array($rawChoices)) {
                foreach ($rawChoices as $idx => $choice) {
                    $cName = trim($choice['campus'] ?? '');
                    $mName = trim($choice['major'] ?? '');
                    if ($cName !== '' || $mName !== '') {
                        $cleanedChoices[] = [
                            'order' => (int) ($choice['order'] ?? ($idx + 1)),
                            'campus' => $cName,
                            'major' => $mName,
                        ];
                    }
                }
            }

            if (! empty($cleanedChoices)) {
                $validated['campus_choices'] = $cleanedChoices;
                $validated['campus_name'] = $cleanedChoices[0]['campus'] ?? null;
                $validated['study_program'] = $cleanedChoices[0]['major'] ?? null;
            } elseif (! empty($validated['campus_name']) || ! empty($validated['study_program'])) {
                $validated['campus_choices'] = [
                    [
                        'order' => 1,
                        'campus' => $validated['campus_name'] ?? '',
                        'major' => $validated['study_program'] ?? '',
                    ],
                ];
            } else {
                $validated['campus_choices'] = null;
            }
        } elseif (($validated['career_path'] ?? null) === 'BEKERJA') {
            $validated['study_admission_track'] = null;
            $validated['campus_name'] = null;
            $validated['study_program'] = null;
            $validated['campus_choices'] = null;
        }

        $previousFollowUpDate = $session->follow_up_date?->toDateString();

        $tableColumns = Schema::getColumnListing('bk_counseling_sessions');
        $attributesToUpdate = array_intersect_key([
            ...$validated,
            'is_confidential' => $request->boolean('is_confidential', true),
        ], array_flip($tableColumns));

        $session->update($attributesToUpdate);

        // Kirim notifikasi jika tanggal RTL baru ditentukan atau berubah
        if (! empty($session->follow_up_date) && $session->follow_up_date->toDateString() !== $previousFollowUpDate) {
            $this->sendFollowUpNotifications($session);
        }

        return redirect()->route('bk.counseling.show', $session)
            ->with('success', 'Catatan sesi bimbingan konseling berhasil diperbarui.');
    }

    public function destroy(BkCounselingSession $session): RedirectResponse
    {
        $session->delete();

        return redirect()->route('bk.counseling.index')
            ->with('success', 'Catatan sesi konseling berhasil dihapus.');
    }

    public function printOfficial(BkCounselingSession $session): View
    {
        $session->load(['student.schoolClass', 'counselor']);
        $school = SchoolIdentity::first();
        $headmaster = Teacher::getHeadmaster();
        $bkCoordinator = Teacher::getBkCoordinator();

        return view('bk.counseling.print', compact('session', 'school', 'headmaster', 'bkCoordinator'));
    }

    /**
     * Cetak Rekapitulasi Layanan Konseling (Keseluruhan atau Custom Filter)
     */
    public function printRecap(Request $request): View
    {
        $user = Auth::user();
        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);

        $query = BkCounselingSession::with(['student.schoolClass', 'counselor'])
            ->orderBy('date', $request->query('sort', 'asc'));

        if ($isHomeroomOnly) {
            $homeroomClass = $user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first();
            if ($homeroomClass) {
                $query->whereHas('student', fn ($sq) => $sq->where('class_id', $homeroomClass->id));
            }
        }

        $filters = [
            'field_of_service' => $request->query('field_of_service'),
            'career_path' => $request->query('career_path'),
            'session_type' => $request->query('session_type'),
            'status' => $request->query('status'),
            'class_id' => $request->query('class_id'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'paper_size' => in_array($request->query('paper_size'), ['A4', 'F4']) ? $request->query('paper_size') : 'A4',
        ];

        $hasCareerPath = Schema::hasColumn('bk_counseling_sessions', 'career_path');

        if (! empty($filters['field_of_service'])) {
            $query->where('field_of_service', $filters['field_of_service']);
        }

        if ($hasCareerPath && ! empty($filters['career_path'])) {
            $query->where('career_path', $filters['career_path']);
        }

        if (! empty($filters['session_type'])) {
            $query->where('session_type', $filters['session_type']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['class_id'])) {
            $query->whereHas('student', fn ($sq) => $sq->where('class_id', $filters['class_id']));
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        $sessions = $query->get();
        $school = SchoolIdentity::first();
        $headmaster = Teacher::getHeadmaster();
        $bkCoordinator = Teacher::getBkCoordinator();
        $selectedClass = ! empty($filters['class_id']) ? SchoolClass::find($filters['class_id']) : null;

        // Statistik Ringkas Rekap
        $stats = [
            'total' => $sessions->count(),
            'unique_students' => $sessions->pluck('student_id')->unique()->count(),
            'pribadi' => $sessions->where('field_of_service', 'PRIBADI')->count(),
            'sosial' => $sessions->where('field_of_service', 'SOSIAL')->count(),
            'belajar' => $sessions->where('field_of_service', 'BELAJAR')->count(),
            'karier' => $sessions->where('field_of_service', 'KARIER')->count(),
            'kuliah' => $hasCareerPath ? $sessions->where('field_of_service', 'KARIER')->where('career_path', 'KULIAH')->count() : 0,
            'bekerja' => $hasCareerPath ? $sessions->where('field_of_service', 'KARIER')->where('career_path', 'BEKERJA')->count() : 0,
            'selesai' => $sessions->where('status', 'SELESAI')->count(),
            'terjadwal' => $sessions->where('status', 'TERJADWAL')->count(),
        ];

        return view('bk.counseling.recap-print', compact('sessions', 'school', 'headmaster', 'bkCoordinator', 'filters', 'selectedClass', 'stats'));
    }

    /**
     * Kirim Notifikasi RTL ke Koordinator BK dan Guru BK
     */
    protected function sendFollowUpNotifications(BkCounselingSession $session): void
    {
        if (empty($session->follow_up_date)) {
            return;
        }

        $studentName = $session->student?->name ?? 'Siswa';
        $className = $session->student?->schoolClass?->name ?? '-';
        $formattedDate = Carbon::parse($session->follow_up_date)->translatedFormat('l, d F Y');

        $title = "📅 Jadwal Pertemuan Tindak Lanjut (RTL) BK: {$studentName}";
        $message = "Pertemuan lanjutan konseling untuk {$studentName} ({$className}) direncanakan pada {$formattedDate}. Topik: {$session->topic}.";
        $linkUrl = route('bk.counseling.show', $session);

        // Ambil akun Koordinator BK, Guru BK, dan Konselor yang bersangkutan
        $bkUsers = User::where(function ($q) use ($session) {
            $q->whereIn('role', ['koordinator_bk', 'guru_bk', 'bk'])
                ->orWhereJsonContains('roles', 'koordinator_bk')
                ->orWhereJsonContains('roles', 'guru_bk')
                ->orWhereJsonContains('roles', 'bk');

            if ($session->counselor_id) {
                $q->orWhere('id', $session->counselor_id);
            }
        })->get()->unique('id');

        foreach ($bkUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => 'GUIDANCE',
                'link_url' => $linkUrl,
                'is_read' => false,
            ]);
        }
    }
}

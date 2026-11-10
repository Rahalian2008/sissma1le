<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use App\Services\DisciplineAutomationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuranTahfidzController extends Controller
{
    /**
     * Cek apakah user berwenang menginput/mengelola setoran hafalan
     * (Wali Kelas, Koordinator Keagamaan, Guru PAI, Admin, Super Admin)
     */
    protected function checkAuthorizedExaminer(): void
    {
        $user = Auth::user();
        $isAuthorized = $user?->canManageTahfidz();

        if (! $isAuthorized) {
            abort(403, 'Akses ditolak: Form input setoran hanya dapat diakses oleh akun Wali Kelas atau Koordinator Keagamaan yang terotentikasi.');
        }
    }

    /**
     * Resolusi data siswa yang terkait dengan user (siswa, sekretaris kelas, atau orang tua)
     */
    protected function resolveStudentForUser(?User $user): ?Student
    {
        if (! $user) {
            return null;
        }

        if ($user->isStudent()) {
            return $user->student ?? Student::where('user_id', $user->id)->first();
        }

        if ($user->isParent()) {
            return $user->parentProfile?->student
                ?? Student::whereHas('parents', fn ($q) => $q->where('user_id', $user->id))->first();
        }

        return null;
    }

    /**
     * Cek apakah user berwenang melihat data semua siswa atau terbatas pada diri/anaknya
     * (Role siswa, sekretaris kelas, dan orang tua)
     */
    protected function isRestrictedToSingleStudent(?User $user): bool
    {
        if (! $user) {
            return true;
        }

        return ($user->isStudent() || $user->isParent())
            && ! $user->canManageTahfidz()
            && ! $user->hasAnyRole(['admin', 'super_admin', 'kepala_sekolah', 'guru', 'wali_kelas', 'koordinator_keagamaan']);
    }

    /**
     * Daftar Riwayat & Manajemen Setoran Tahfidz
     * Untuk siswa, sekretaris kelas, dan orang tua langsung menampilkan laporan hafalan siswa terkait.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Pada akun siswa, role sekretaris kelas, dan orang tua:
        // menu Kontrol Tahfidz langsung menampilkan laporan progres penyelesaian hafalan siswa tersebut
        if ($this->isRestrictedToSingleStudent($user)) {
            $student = $this->resolveStudentForUser($user);

            if (! $student) {
                return view('tahfidz.student_not_linked', compact('user'));
            }

            return $this->student($student);
        }

        $role = $user->role ?? 'guest';
        $isHomeroom = $user->hasRole('wali_kelas');
        $homeroomClass = $isHomeroom ? $user->teacher?->homeroomClass : null;
        $homeroomGrade = $homeroomClass ? QuranTahfidzRecord::normalizeGrade($homeroomClass->grade ?? $homeroomClass->name) : null;

        $selectedClassId = $request->query('class_id');
        $selectedStudentId = $request->query('student_id');
        $search = $request->query('search');
        $surahFilter = $request->query('surah');

        // Akun Wali Kelas bisa melihat semua kelas seperti admin, tetapi kita biarkan $classes berisi semua kelas
        $classes = SchoolClass::orderBy('name')->get();
        $allSurahs = QuranTahfidzRecord::getJuz30Surahs();
        $surahs = ($isHomeroom && $homeroomGrade)
            ? QuranTahfidzRecord::getSurahsByGrade($homeroomGrade)
            : $allSurahs;

        // Siswa untuk filter dropdown / manajemen kelas (otomatis kelas binaan jika Wali Kelas)
        $classStudents = collect();
        $targetClassIdForStudents = $selectedClassId ?: ($isHomeroom && $homeroomClass ? $homeroomClass->id : null);
        if ($targetClassIdForStudents) {
            $classStudents = Student::where('class_id', $targetClassIdForStudents)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $query = QuranTahfidzRecord::with(['student.schoolClass', 'examiner'])
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->when($selectedStudentId, fn ($q) => $q->where('student_id', $selectedStudentId))
            ->when($surahFilter, fn ($q) => $q->where('surah_name', $surahFilter))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('surah_name', 'like', "%{$search}%")
                        ->orWhere('ayat_range', 'like', "%{$search}%")
                        ->orWhere('evaluation_notes', 'like', "%{$search}%")
                        ->orWhereHas('student', fn ($s) => $s->where('name', 'like', "%{$search}%")->orWhere('initials', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
                });
            });

        // Removed isolation based on $isHomeroom since they have admin view

        $records = $query->orderByDesc('deposit_date')->orderByDesc('deposit_time')->paginate(20)->withQueryString();

        // Global Statistik (Selalu dihitung untuk Admin View)
        $globalTotalSetoran = QuranTahfidzRecord::count();
        $globalTotalLulus = QuranTahfidzRecord::where('overall_grade', 'Lulus')->count();
        $globalTotalMengulang = QuranTahfidzRecord::where('overall_grade', 'Mengulang')->count();
        $globalDistinctStudents = QuranTahfidzRecord::distinct('student_id')->count('student_id');

        // Statistik Wali Kelas
        if ($isHomeroom && $homeroomClass) {
            $baseStatQuery = QuranTahfidzRecord::where('class_id', $homeroomClass->id)
                ->when($homeroomGrade, fn ($q) => $q->whereIn('surah_number', array_keys($surahs)));
            $totalSetoran = (clone $baseStatQuery)->count();
            $totalLulus = (clone $baseStatQuery)->where('overall_grade', 'Lulus')->count();
            $totalMengulang = (clone $baseStatQuery)->where('overall_grade', 'Mengulang')->count();
            $distinctStudents = (clone $baseStatQuery)->distinct('student_id')->count('student_id');
        } else {
            $totalSetoran = $globalTotalSetoran;
            $totalLulus = $globalTotalLulus;
            $totalMengulang = $globalTotalMengulang;
            $distinctStudents = $globalDistinctStudents;
        }

        // Rangkuman progres setor hafalan per siswa kelas (khusus untuk Akun Wali Kelas)
        $studentProgressList = [];
        if ($isHomeroom && $homeroomClass) {
            $targetSurahNumbers = array_keys($surahs);
            $classRecords = QuranTahfidzRecord::where('class_id', $homeroomClass->id)->get();
            $groupedRecords = $classRecords->groupBy('student_id');

            foreach ($classStudents as $st) {
                $stRecords = $groupedRecords->get($st->id, collect());
                $passedSurahs = $stRecords->where('overall_grade', 'Lulus')
                    ->whereIn('surah_number', $targetSurahNumbers)
                    ->pluck('surah_number')
                    ->unique()
                    ->count();

                $latestRecord = $stRecords->sortByDesc('deposit_date')->sortByDesc('deposit_time')->first();
                $percentage = count($targetSurahNumbers) > 0 ? round(($passedSurahs / count($targetSurahNumbers)) * 100, 1) : 0;

                $studentProgressList[] = [
                    'student' => $st,
                    'passed_count' => $passedSurahs,
                    'total_target' => count($targetSurahNumbers),
                    'percentage' => $percentage,
                    'latest_record' => $latestRecord,
                    'is_complete' => $passedSurahs >= count($targetSurahNumbers),
                ];
            }
        }

        return view('tahfidz.index', compact(
            'records',
            'classes',
            'classStudents',
            'selectedClassId',
            'selectedStudentId',
            'search',
            'surahFilter',
            'surahs',
            'totalSetoran',
            'totalLulus',
            'totalMengulang',
            'distinctStudents',
            'globalTotalSetoran',
            'globalTotalLulus',
            'globalTotalMengulang',
            'globalDistinctStudents',
            'isHomeroom',
            'homeroomClass',
            'homeroomGrade',
            'studentProgressList'
        ));
    }

    /**
     * Form Input Setoran Hafalan Baru
     * HANYA DAPAT DIAKSES OLEH WALI KELAS / KOORDINATOR KEAGAMAAN / GURU / ADMIN
     */
    public function create(Request $request): View
    {
        $this->checkAuthorizedExaminer();

        $user = Auth::user();
        $isHomeroom = $user->role === 'wali_kelas';
        $homeroomClass = $isHomeroom ? $user->teacher?->homeroomClass : null;
        $homeroomGrade = $homeroomClass ? QuranTahfidzRecord::normalizeGrade($homeroomClass->grade ?? $homeroomClass->name) : null;

        if ($isHomeroom && $homeroomClass) {
            $selectedClassId = $homeroomClass->id;
            $classes = collect([$homeroomClass]);
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->query('class_id');
        }

        $studentsQuery = Student::with('schoolClass')->where('is_active', true);
        if ($selectedClassId) {
            $studentsQuery->where('class_id', $selectedClassId);
        }
        $students = $studentsQuery->orderBy('name')->get();

        $selectedStudent = $request->query('student_id') ? Student::with('schoolClass')->find($request->query('student_id')) : null;
        if ($isHomeroom && $homeroomClass && $selectedStudent && $selectedStudent->class_id !== $homeroomClass->id) {
            $selectedStudent = null;
        }

        if ($isHomeroom && $homeroomGrade) {
            // Untuk Wali Kelas: Nama surat yang tampil HANYA surat yang sesuai dengan kelasnya
            $surahs = QuranTahfidzRecord::getSurahsByGrade($homeroomGrade);
            $groupedSurahs = [
                $homeroomGrade => [
                    'label' => 'Target Kelas '.$homeroomGrade,
                    'grade' => $homeroomGrade,
                    'surahs' => $surahs,
                ],
            ];
            $studentGrade = $homeroomGrade;
        } else {
            $surahs = QuranTahfidzRecord::getJuz30Surahs();
            $groupedSurahs = QuranTahfidzRecord::getSurahsGroupedByGrade();
            $studentGrade = $selectedStudent?->getNormalizedGrade();
        }

        $defaultExaminerName = $user->teacher?->name ?: $user->name;

        return view('tahfidz.create', compact(
            'classes',
            'students',
            'selectedClassId',
            'selectedStudent',
            'surahs',
            'groupedSurahs',
            'studentGrade',
            'defaultExaminerName',
            'isHomeroom',
            'homeroomClass',
            'homeroomGrade'
        ));
    }

    /**
     * Simpan Data Setoran Hafalan
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorizedExaminer();

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'surah_number' => ['required', 'integer', 'min:78', 'max:114'],
            'ayat_range' => ['nullable', 'string', 'max:100'],
            'start_ayah' => ['nullable', 'integer', 'min:1', 'max:286'],
            'end_ayah' => ['nullable', 'integer', 'min:1', 'max:286'],
            'ayat_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'makhraj' => ['required', 'in:Sangat Baik,Baik,Cukup,Mengulang'],
            'tajwid' => ['required', 'in:Sangat Baik,Baik,Cukup,Mengulang'],
            'kelancaran' => ['required', 'in:Sangat Baik,Baik,Cukup,Mengulang'],
            'overall_grade' => ['required', 'in:Lulus,Mengulang'],
            'evaluation_notes' => ['nullable', 'string', 'max:1000'],
            'deposit_date' => ['required', 'date'],
            'deposit_time' => ['required', 'string'],
            'examiner_name' => ['required', 'string', 'max:150'],
        ]);

        $student = Student::with('schoolClass')->findOrFail($validated['student_id']);
        $user = Auth::user();
        $isHomeroom = $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan']);
        $homeroomClass = $isHomeroom ? $user->teacher?->homeroomClass : null;
        if ($isHomeroom && $homeroomClass && $student->class_id !== $homeroomClass->id) {
            return back()->with('error', "Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat menginput setoran hafalan untuk siswa kelas binaan Anda.")->withInput();
        }
        $surahs = QuranTahfidzRecord::getJuz30Surahs();
        $surahInfo = $surahs[$validated['surah_number']] ?? null;
        $surahName = $surahInfo ? $surahInfo['name'] : 'Surat Ke-'.$validated['surah_number'];
        $totalSurahAyah = (int) ($surahInfo['total_ayah'] ?? 40);

        // Dukungan sistem setoran cicilan (angsuran ayat)
        $startAyah = $request->filled('start_ayah') ? (int) $request->input('start_ayah') : null;
        $endAyah = $request->filled('end_ayah') ? (int) $request->input('end_ayah') : null;

        $ayatRange = trim($validated['ayat_range'] ?? '');
        if (empty($ayatRange) && $startAyah !== null) {
            if ($endAyah !== null && $endAyah !== $startAyah) {
                $minAyah = min($startAyah, $endAyah);
                $maxAyah = max($startAyah, $endAyah);
                $ayatRange = "Ayat {$minAyah} s.d. {$maxAyah}";
            } else {
                $ayatRange = "Ayat {$startAyah}";
            }
        }

        if (empty($ayatRange)) {
            $ayatRange = "Ayat 1 s.d. {$totalSurahAyah}";
        }

        $ayatCount = $request->filled('ayat_count') ? (int) $request->input('ayat_count') : null;
        if (! $ayatCount) {
            if ($startAyah !== null && $endAyah !== null) {
                $ayatCount = abs($endAyah - $startAyah) + 1;
            } else {
                $parsed = QuranTahfidzRecord::parseAyatRange($ayatRange, $totalSurahAyah);
                if (! empty($parsed)) {
                    $ayatCount = count($parsed);
                } elseif (preg_match('/(?:s\.d\.|-)\s*(\d+)/i', $ayatRange, $matches)) {
                    $ayatCount = (int) $matches[1];
                } elseif ($surahInfo) {
                    $ayatCount = $totalSurahAyah;
                }
            }
        }

        $user = Auth::user();

        $record = QuranTahfidzRecord::create([
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'juz' => 30,
            'surah_number' => $validated['surah_number'],
            'surah_name' => $surahName,
            'ayat_range' => $ayatRange,
            'ayat_count' => $ayatCount,
            'makhraj' => $validated['makhraj'],
            'tajwid' => $validated['tajwid'],
            'kelancaran' => $validated['kelancaran'],
            'overall_grade' => $validated['overall_grade'],
            'evaluation_notes' => $validated['evaluation_notes'] ?? null,
            'deposit_date' => $validated['deposit_date'],
            'deposit_time' => $validated['deposit_time'],
            'examiner_id' => $user->id,
            'examiner_name' => $validated['examiner_name'],
        ]);

        if (($record->ayat_count ?? 0) >= 20) {
            app(DisciplineAutomationService::class)->recordTahfidzAchievement($record);
        }

        AuditLog::log('TAHFIDZ_DEPOSIT_RECORDED', 'QuranTahfidzRecord', $record->id, null, [
            'student' => $student->name,
            'surah' => $surahName,
            'ayat_count' => $ayatCount,
            'ayat_range' => $ayatRange,
            'grade' => $validated['overall_grade'],
        ]);

        return redirect()->route('tahfidz.student', $student->id)
            ->with('success', "Setoran hafalan Surat {$surahName} ({$ayatRange}, {$ayatCount} Ayat) untuk siswa {$student->name} berhasil dicatat!");
    }

    /**
     * Buku Kontrol Digital Siswa (Digital Tahfidz Progress Card Juz 30)
     * Untuk siswa, sekretaris kelas, dan orang tua: hanya dapat melihat data siswa bersangkutan.
     */
    public function student(Student $student): View|RedirectResponse
    {
        $user = Auth::user();

        // Validasi akses privat bagi akun siswa, sekretaris kelas, atau orang tua
        if ($this->isRestrictedToSingleStudent($user)) {
            $allowedStudent = $this->resolveStudentForUser($user);

            if (! $allowedStudent) {
                return view('tahfidz.student_not_linked', compact('user'));
            }

            // Jika mencoba membuka profil siswa lain, arahkan ke data miliknya sendiri
            if ($allowedStudent->id !== $student->id) {
                return redirect()->route('tahfidz.student', $allowedStudent->id);
            }
        }

        // Validasi akses wali kelas: hanya siswa kelas binaannya
        if ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                abort(403, "Akses ditolak: Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat mengakses data siswa kelas binaan Anda.");
            }
        }

        $student->loadMissing(['schoolClass.homeroomTeacher', 'user']);

        $surahs = QuranTahfidzRecord::getJuz30Surahs();
        $records = $student->tahfidzRecords()->with('examiner')->orderByDesc('deposit_date')->orderByDesc('deposit_time')->get();

        // Peta status kelulusan tiap surat
        $surahStatus = [];
        foreach ($surahs as $num => $s) {
            $surahRecords = $records->where('surah_number', $num);
            $latestForSurah = $surahRecords->first();
            $passedRecord = $surahRecords->firstWhere('overall_grade', 'Lulus');
            $isLulus = $passedRecord !== null;
            $hasDeposited = $surahRecords->isNotEmpty();

            $surahStatus[$num] = [
                'has_deposited' => $hasDeposited,
                'is_lulus' => $isLulus,
                'latest' => $latestForSurah,
                'passed' => $passedRecord,
                'deposit_count' => $surahRecords->count(),
                'records' => $surahRecords,
            ];
        }

        $totalSurahs = count($surahs);
        $completedSurahs = collect($surahStatus)->where('is_lulus', true)->count();
        $mengulangSurahs = collect($surahStatus)->where('has_deposited', true)->where('is_lulus', false)->count();
        $belumSetorSurahs = $totalSurahs - collect($surahStatus)->where('has_deposited', true)->count();
        $completionPercentage = round(($completedSurahs / $totalSurahs) * 100, 1);
        $isOwnReport = $this->isRestrictedToSingleStudent($user);

        $studentGrade = $student->getNormalizedGrade() ?: 'X';
        $groupedSurahs = QuranTahfidzRecord::getSurahsGroupedByGrade();
        $targetGradeSurahs = QuranTahfidzRecord::getSurahsByGrade($studentGrade);
        $targetGradeTotal = count($targetGradeSurahs);
        $targetGradeCompleted = 0;
        $targetGradeMengulang = 0;
        $targetGradeBelumSetor = 0;

        $targetGradeTotalAyat = 0;
        $targetGradeCompletedAyat = 0;

        foreach ($targetGradeSurahs as $num => $s) {
            $ayahCount = (int) ($s['total_ayah'] ?? 0);
            $targetGradeTotalAyat += $ayahCount;

            if (! empty($surahStatus[$num]['is_lulus'])) {
                $targetGradeCompleted++;
                $targetGradeCompletedAyat += $ayahCount;
            } elseif (! empty($surahStatus[$num]['has_deposited'])) {
                $targetGradeMengulang++;
                $depositedAyat = (int) ($surahStatus[$num]['latest']?->ayat_count ?? 0);
                if ($depositedAyat > 0) {
                    $targetGradeCompletedAyat += min($depositedAyat, $ayahCount);
                }
            } else {
                $targetGradeBelumSetor++;
            }
        }

        $targetGradePercentage = $targetGradeTotal > 0 ? round(($targetGradeCompleted / $targetGradeTotal) * 100, 1) : 0;
        $targetGradeAyatPercentage = $targetGradeTotalAyat > 0 ? round(($targetGradeCompletedAyat / $targetGradeTotalAyat) * 100, 1) : 0;

        return view('tahfidz.student', compact(
            'student',
            'surahs',
            'records',
            'surahStatus',
            'completedSurahs',
            'mengulangSurahs',
            'belumSetorSurahs',
            'totalSurahs',
            'completionPercentage',
            'studentGrade',
            'groupedSurahs',
            'targetGradeSurahs',
            'targetGradeTotal',
            'targetGradeCompleted',
            'targetGradeMengulang',
            'targetGradeBelumSetor',
            'targetGradePercentage',
            'targetGradeTotalAyat',
            'targetGradeCompletedAyat',
            'targetGradeAyatPercentage',
            'isOwnReport'
        ));
    }

    /**
     * Cetak Kartu Buku Kontrol Digital Siswa (Juz 30)
     */
    public function printStudent(Student $student): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('siswa') || $user->role === 'siswa') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencetak dokumen.');
        }

        // Validasi akses cetak privat bagi akun siswa, sekretaris kelas, atau orang tua
        if ($this->isRestrictedToSingleStudent($user)) {
            $allowedStudent = $this->resolveStudentForUser($user);

            if (! $allowedStudent) {
                return view('tahfidz.student_not_linked', compact('user'));
            }

            if ($allowedStudent->id !== $student->id) {
                return redirect()->route('tahfidz.print_student', $allowedStudent->id);
            }
        }

        // Validasi akses wali kelas: hanya siswa kelas binaannya
        if ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                abort(403, "Akses ditolak: Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat mencetak data siswa kelas binaan Anda.");
            }
        }

        $student->loadMissing(['schoolClass.homeroomTeacher', 'user']);

        $surahs = QuranTahfidzRecord::getJuz30Surahs();
        $records = $student->tahfidzRecords()->with('examiner')->orderBy('deposit_date')->orderBy('deposit_time')->get();

        $surahStatus = [];
        foreach ($surahs as $num => $s) {
            $surahRecords = $records->where('surah_number', $num);
            $latestForSurah = $surahRecords->last();
            $passedRecord = $surahRecords->firstWhere('overall_grade', 'Lulus');
            $isLulus = $passedRecord !== null;
            $hasDeposited = $surahRecords->isNotEmpty();

            $surahStatus[$num] = [
                'has_deposited' => $hasDeposited,
                'is_lulus' => $isLulus,
                'latest' => $latestForSurah,
                'passed' => $passedRecord,
                'deposit_count' => $surahRecords->count(),
            ];
        }

        $totalSurahs = count($surahs);
        $completedSurahs = collect($surahStatus)->where('is_lulus', true)->count();
        $completionPercentage = round(($completedSurahs / $totalSurahs) * 100, 1);
        $school = SchoolSetting::allAsAssoc();

        $studentGrade = $student->getNormalizedGrade() ?: 'X';
        $groupedSurahs = QuranTahfidzRecord::getSurahsGroupedByGrade();
        $targetGradeSurahs = QuranTahfidzRecord::getSurahsByGrade($studentGrade);
        $targetGradeTotal = count($targetGradeSurahs);
        $targetGradeCompleted = 0;
        $targetGradeMengulang = 0;
        $targetGradeBelumSetor = 0;

        $targetGradeTotalAyat = 0;
        $targetGradeCompletedAyat = 0;

        foreach ($targetGradeSurahs as $num => $s) {
            $ayahCount = (int) ($s['total_ayah'] ?? 0);
            $targetGradeTotalAyat += $ayahCount;

            if (! empty($surahStatus[$num]['is_lulus'])) {
                $targetGradeCompleted++;
                $targetGradeCompletedAyat += $ayahCount;
            } elseif (! empty($surahStatus[$num]['has_deposited'])) {
                $targetGradeMengulang++;
                $depositedAyat = (int) ($surahStatus[$num]['latest']?->ayat_count ?? 0);
                if ($depositedAyat > 0) {
                    $targetGradeCompletedAyat += min($depositedAyat, $ayahCount);
                }
            } else {
                $targetGradeBelumSetor++;
            }
        }

        $targetGradePercentage = $targetGradeTotal > 0 ? round(($targetGradeCompleted / $targetGradeTotal) * 100, 1) : 0;
        $targetGradeAyatPercentage = $targetGradeTotalAyat > 0 ? round(($targetGradeCompletedAyat / $targetGradeTotalAyat) * 100, 1) : 0;

        $school = SchoolSetting::allAsAssoc();
        $headmasterName = SchoolSetting::get('headmaster_name') ?: ($school['headmaster_name'] ?? 'Drs. H. Ahmad Sudrajat, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip') ?: ($school['headmaster_nip'] ?? '-');

        return view('tahfidz.print_student', compact(
            'student',
            'surahs',
            'records',
            'surahStatus',
            'completedSurahs',
            'totalSurahs',
            'completionPercentage',
            'school',
            'headmasterName',
            'headmasterNip',
            'studentGrade',
            'groupedSurahs',
            'targetGradeSurahs',
            'targetGradeTotal',
            'targetGradeCompleted',
            'targetGradeMengulang',
            'targetGradeBelumSetor',
            'targetGradePercentage',
            'targetGradeTotalAyat',
            'targetGradeCompletedAyat',
            'targetGradeAyatPercentage'
        ));
    }

    /**
     * Laporan Rekapitulasi Tahfidz
     */
    public function report(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Akun siswa, sekretaris kelas, dan orang tua diarahkan ke laporan hafalan mereka
        if ($this->isRestrictedToSingleStudent($user)) {
            return redirect()->route('tahfidz.index');
        }

        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']);

        if ($isWaliKelas) {
            $classes = $user->getHomeroomClasses();
            $requestedClassId = $request->query('class_id');
            if ($requestedClassId && $classes->contains('id', (int) $requestedClassId)) {
                $selectedClassId = (int) $requestedClassId;
            } else {
                $selectedClassId = $classes->first()?->id;
            }
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->query('class_id');
        }

        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $gradeFilter = $request->query('grade');

        $query = QuranTahfidzRecord::with(['student.schoolClass', 'examiner'])
            ->whereBetween('deposit_date', [$startDate, $endDate])
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->when($gradeFilter, fn ($q) => $q->where('overall_grade', $gradeFilter));

        $records = $query->orderByDesc('deposit_date')->orderByDesc('deposit_time')->paginate(25)->withQueryString();

        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;

        $stats = [
            'total' => QuranTahfidzRecord::whereBetween('deposit_date', [$startDate, $endDate])->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))->count(),
            'lulus' => QuranTahfidzRecord::whereBetween('deposit_date', [$startDate, $endDate])->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))->where('overall_grade', 'Lulus')->count(),
            'mengulang' => QuranTahfidzRecord::whereBetween('deposit_date', [$startDate, $endDate])->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))->where('overall_grade', 'Mengulang')->count(),
            'students_count' => QuranTahfidzRecord::whereBetween('deposit_date', [$startDate, $endDate])->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))->distinct('student_id')->count('student_id'),
        ];

        return view('tahfidz.report', compact(
            'classes',
            'selectedClass',
            'selectedClassId',
            'startDate',
            'endDate',
            'gradeFilter',
            'records',
            'stats'
        ));
    }

    /**
     * Cetak Berkas Dokumen Laporan Rekapitulasi Tahfidz (Format A4 KOP Resmi)
     */
    public function printReport(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('siswa') || $user->role === 'siswa') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencetak dokumen.');
        }

        // Akun siswa, sekretaris kelas, dan orang tua diarahkan ke laporan hafalan mereka
        if ($this->isRestrictedToSingleStudent($user)) {
            return redirect()->route('tahfidz.index');
        }

        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']);
        $selectedClassId = $request->query('class_id');
        if ($isWaliKelas) {
            $homeroomClasses = $user->getHomeroomClasses();
            if ($selectedClassId && $homeroomClasses->contains('id', (int) $selectedClassId)) {
                $selectedClassId = (int) $selectedClassId;
            } else {
                $selectedClassId = $homeroomClasses->first()?->id;
            }
        }

        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $gradeFilter = $request->query('grade');

        $query = QuranTahfidzRecord::with(['student.schoolClass', 'examiner'])
            ->whereBetween('deposit_date', [$startDate, $endDate])
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->when($gradeFilter, fn ($q) => $q->where('overall_grade', $gradeFilter));

        $records = $query->orderBy('deposit_date')->orderBy('deposit_time')->get();

        $selectedClass = $selectedClassId ? SchoolClass::with('homeroomTeacher')->find($selectedClassId) : null;
        $school = SchoolSetting::allAsAssoc();
        $headmasterName = SchoolSetting::get('headmaster_name') ?: ($school['headmaster_name'] ?? 'Drs. H. Ahmad Sudrajat, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip') ?: ($school['headmaster_nip'] ?? '-');

        $stats = [
            'total' => $records->count(),
            'lulus' => $records->where('overall_grade', 'Lulus')->count(),
            'mengulang' => $records->where('overall_grade', 'Mengulang')->count(),
            'students_count' => $records->pluck('student_id')->unique()->count(),
        ];

        return view('tahfidz.print_report', compact(
            'selectedClass',
            'startDate',
            'endDate',
            'records',
            'school',
            'headmasterName',
            'headmasterNip',
            'stats'
        ));
    }

    /**
     * Form Edit Catatan Setoran Hafalan (Koordinator Keagamaan, Wali Kelas, Guru, Admin)
     */
    public function edit(QuranTahfidzRecord $tahfidz): View
    {
        $user = Auth::user();
        if (! $user || ! $user->canManageTahfidz()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengubah data setoran.');
        }

        $isHomeroom = $user->role === 'wali_kelas';
        $homeroomClass = $isHomeroom ? $user->teacher?->homeroomClass : null;
        $homeroomGrade = $homeroomClass ? QuranTahfidzRecord::normalizeGrade($homeroomClass->grade ?? $homeroomClass->name) : null;

        $student = $tahfidz->student;
        if ($isHomeroom && $homeroomClass && $student && $student->class_id !== $homeroomClass->id) {
            abort(403, "Akses Ditolak: Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat mengubah data siswa binaan Anda.");
        }

        $studentGrade = $student?->getNormalizedGrade() ?: $homeroomGrade;

        if ($isHomeroom && $homeroomGrade) {
            $surahs = QuranTahfidzRecord::getSurahsByGrade($homeroomGrade);
            if (! isset($surahs[$tahfidz->surah_number])) {
                $allSurahs = QuranTahfidzRecord::getJuz30Surahs();
                if (isset($allSurahs[$tahfidz->surah_number])) {
                    $surahs[$tahfidz->surah_number] = $allSurahs[$tahfidz->surah_number];
                }
            }
            $groupedSurahs = [
                $homeroomGrade => [
                    'label' => 'Target Kelas '.$homeroomGrade,
                    'grade' => $homeroomGrade,
                    'surahs' => $surahs,
                ],
            ];
        } else {
            $surahs = QuranTahfidzRecord::getJuz30Surahs();
            $groupedSurahs = QuranTahfidzRecord::getSurahsGroupedByGrade();
        }

        return view('tahfidz.edit', compact(
            'tahfidz',
            'student',
            'surahs',
            'groupedSurahs',
            'studentGrade',
            'isHomeroom',
            'homeroomClass',
            'homeroomGrade'
        ));
    }

    /**
     * Simpan Pembaruan Catatan Setoran Hafalan
     */
    public function update(Request $request, QuranTahfidzRecord $tahfidz): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->canManageTahfidz()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengubah data setoran.');
        }

        $student = $tahfidz->student;
        $isHomeroom = $user->role === 'wali_kelas';
        $homeroomClass = $isHomeroom ? $user->teacher?->homeroomClass : null;
        if ($isHomeroom && $homeroomClass && $student && $student->class_id !== $homeroomClass->id) {
            abort(403, "Akses Ditolak: Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat mengubah data siswa binaan Anda.");
        }

        $validated = $request->validate([
            'surah_number' => ['required', 'integer', 'between:78,114'],
            'ayat_range' => ['nullable', 'string', 'max:100'],
            'start_ayah' => ['nullable', 'integer', 'min:1', 'max:286'],
            'end_ayah' => ['nullable', 'integer', 'min:1', 'max:286'],
            'ayat_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'makhraj' => ['required', 'in:Sangat Baik,Baik,Cukup,Mengulang'],
            'tajwid' => ['required', 'in:Sangat Baik,Baik,Cukup,Mengulang'],
            'kelancaran' => ['required', 'in:Sangat Baik,Baik,Cukup,Mengulang'],
            'overall_grade' => ['required', 'in:Lulus,Mengulang'],
            'evaluation_notes' => ['nullable', 'string', 'max:1000'],
            'deposit_date' => ['required', 'date'],
            'deposit_time' => ['nullable', 'date_format:H:i'],
            'examiner_name' => ['nullable', 'string', 'max:255'],
        ]);

        $surahs = QuranTahfidzRecord::getJuz30Surahs();
        $surahMeta = $surahs[$validated['surah_number']] ?? null;
        $surahName = $surahMeta ? $surahMeta['name'] : 'Surat Ke-'.$validated['surah_number'];
        $totalSurahAyah = (int) ($surahMeta['total_ayah'] ?? 40);

        // Dukungan sistem setoran cicilan (angsuran ayat)
        $startAyah = $request->filled('start_ayah') ? (int) $request->input('start_ayah') : null;
        $endAyah = $request->filled('end_ayah') ? (int) $request->input('end_ayah') : null;

        $ayatRange = trim($validated['ayat_range'] ?? '');
        if (empty($ayatRange) && $startAyah !== null) {
            if ($endAyah !== null && $endAyah !== $startAyah) {
                $minAyah = min($startAyah, $endAyah);
                $maxAyah = max($startAyah, $endAyah);
                $ayatRange = "Ayat {$minAyah} s.d. {$maxAyah}";
            } else {
                $ayatRange = "Ayat {$startAyah}";
            }
        }

        if (empty($ayatRange)) {
            $ayatRange = $tahfidz->ayat_range ?: "Ayat 1 s.d. {$totalSurahAyah}";
        }

        $ayatCount = $request->filled('ayat_count') ? (int) $request->input('ayat_count') : null;
        if (! $ayatCount) {
            if ($startAyah !== null && $endAyah !== null) {
                $ayatCount = abs($endAyah - $startAyah) + 1;
            } else {
                $parsed = QuranTahfidzRecord::parseAyatRange($ayatRange, $totalSurahAyah);
                if (! empty($parsed)) {
                    $ayatCount = count($parsed);
                } elseif (preg_match('/(?:s\.d\.|-)\s*(\d+)/i', $ayatRange, $matches)) {
                    $ayatCount = (int) $matches[1];
                } elseif ($surahMeta) {
                    $ayatCount = $totalSurahAyah;
                }
            }
        }

        $tahfidz->update([
            'surah_number' => $validated['surah_number'],
            'surah_name' => $surahName,
            'ayat_range' => $ayatRange,
            'ayat_count' => $ayatCount,
            'makhraj' => $validated['makhraj'],
            'tajwid' => $validated['tajwid'],
            'kelancaran' => $validated['kelancaran'],
            'overall_grade' => $validated['overall_grade'],
            'evaluation_notes' => $validated['evaluation_notes'] ?? null,
            'deposit_date' => $validated['deposit_date'],
            'deposit_time' => ! empty($validated['deposit_time']) ? $validated['deposit_time'].':00' : $tahfidz->deposit_time,
            'examiner_name' => $validated['examiner_name'] ?? Auth::user()->name,
        ]);

        if (($tahfidz->ayat_count ?? 0) >= 20) {
            app(DisciplineAutomationService::class)->recordTahfidzAchievement($tahfidz);
        }

        AuditLog::log('TAHFIDZ_RECORD_UPDATED', 'QuranTahfidzRecord', $tahfidz->id, null, [
            'student' => $tahfidz->student?->name,
            'surah' => $surahName,
            'ayat_count' => $ayatCount,
            'ayat_range' => $ayatRange,
            'grade' => $validated['overall_grade'],
            'updated_by' => Auth::user()->name,
        ]);

        return redirect()->route('tahfidz.student', $tahfidz->student_id)
            ->with('success', "Catatan setoran {$surahName} berhasil diperbarui.");
    }

    /**
     * Tampilan Indeks / Filter Laporan Kustom Progres Cicilan Hafalan Siswa
     */
    public function customProgressReport(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Akun siswa / orang tua langsung diarahkan ke laporan progres miliknya
        if ($this->isRestrictedToSingleStudent($user)) {
            $student = $this->resolveStudentForUser($user);
            if (! $student) {
                return view('tahfidz.student_not_linked', compact('user'));
            }

            return redirect()->route('tahfidz.student_custom_progress', $student->id);
        }

        $selectedClassId = $request->query('class_id');
        $selectedStudentId = $request->query('student_id');
        $search = $request->query('search');

        // Jika user langsung memilih satu siswa tertentu, redirect ke laporan progres detail siswa
        if ($selectedStudentId) {
            $targetStudent = Student::find($selectedStudentId);
            if ($targetStudent) {
                return redirect()->route('tahfidz.student_custom_progress', $targetStudent->id);
            }
        }

        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan']);

        if ($isWaliKelas) {
            $classes = $user->getHomeroomClasses();
            if ($selectedClassId && $classes->contains('id', (int) $selectedClassId)) {
                $selectedClassId = (int) $selectedClassId;
            } else {
                $selectedClassId = $classes->first()?->id;
            }
        } else {
            $classes = SchoolClass::orderBy('name')->get();
        }

        $studentsQuery = Student::with(['schoolClass', 'tahfidzRecords'])
            ->where('is_active', true)
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->when($search, fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%")));

        $students = $studentsQuery->orderBy('name')->paginate(20)->withQueryString();

        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;
        $classGrade = $selectedClass ? QuranTahfidzRecord::normalizeGrade($selectedClass->grade ?? $selectedClass->name) : null;
        $activeGrade = $request->query('grade') ?: $classGrade;

        // Ringkasan analisis untuk setiap siswa pada halaman saat ini disesuaikan dengan tingkat kelas
        $studentAnalyses = [];
        foreach ($students as $student) {
            $studentGrade = $activeGrade ?: (QuranTahfidzRecord::normalizeGrade($student->getNormalizedGrade()) ?: 'X');
            $studentAnalyses[$student->id] = QuranTahfidzRecord::getStudentJuz30Analysis($student, $studentGrade);
        }

        // Info target kelas aktif
        $targetGradeInfo = null;
        if ($activeGrade) {
            $gradeSurahs = QuranTahfidzRecord::getSurahsByGrade($activeGrade);
            $targetGradeInfo = [
                'grade' => $activeGrade,
                'label' => 'Kelas '.$activeGrade,
                'total_surahs' => count($gradeSurahs),
                'total_ayat' => array_sum(array_column($gradeSurahs, 'total_ayah')),
            ];
        }

        return view('tahfidz.custom_progress_index', compact(
            'classes',
            'selectedClass',
            'selectedClassId',
            'search',
            'students',
            'studentAnalyses',
            'activeGrade',
            'targetGradeInfo'
        ));
    }

    /**
     * Laporan Kustom Detail Progres Cicilan Hafalan Siswa Sesuai Tingkat Kelas
     */
    public function studentProgressReport(Request $request, Student $student): View|RedirectResponse
    {
        $user = Auth::user();

        // Validasi akses privat bagi akun siswa / orang tua
        if ($this->isRestrictedToSingleStudent($user)) {
            $allowedStudent = $this->resolveStudentForUser($user);
            if (! $allowedStudent) {
                return view('tahfidz.student_not_linked', compact('user'));
            }
            if ($allowedStudent->id !== $student->id) {
                return redirect()->route('tahfidz.student_custom_progress', $allowedStudent->id);
            }
        }

        // Validasi akses wali kelas
        if ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                abort(403, "Akses ditolak: Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat mengakses data siswa kelas binaan Anda.");
            }
        }

        $student->loadMissing(['schoolClass.homeroomTeacher', 'user']);

        $gradeParam = $request->query('grade');
        $studentGrade = QuranTahfidzRecord::normalizeGrade($gradeParam ?? $student->getNormalizedGrade()) ?: 'X';
        $analysis = QuranTahfidzRecord::getStudentJuz30Analysis($student, $studentGrade);
        $records = $student->tahfidzRecords()->with('examiner')->orderByDesc('deposit_date')->orderByDesc('deposit_time')->get();

        return view('tahfidz.custom_progress', array_merge($analysis, [
            'student' => $student,
            'records' => $records,
            'studentGrade' => $studentGrade,
            'isOwnReport' => $this->isRestrictedToSingleStudent($user),
        ]));
    }

    /**
     * Cetak Laporan Kustom Detail Progres Cicilan Hafalan Siswa (Format A4 KOP Resmi SMAN 1 Lengkong)
     */
    public function printCustomProgressReport(Request $request, Student $student): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('siswa') || $user->role === 'siswa') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencetak dokumen.');
        }

        if ($this->isRestrictedToSingleStudent($user)) {
            $allowedStudent = $this->resolveStudentForUser($user);
            if (! $allowedStudent) {
                return view('tahfidz.student_not_linked', compact('user'));
            }
            if ($allowedStudent->id !== $student->id) {
                return redirect()->route('tahfidz.print_custom_progress', $allowedStudent->id);
            }
        }

        if ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                abort(403, "Akses ditolak: Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat mencetak data siswa kelas binaan Anda.");
            }
        }

        $student->loadMissing(['schoolClass.homeroomTeacher', 'user']);

        $gradeParam = $request->query('grade');
        $studentGrade = QuranTahfidzRecord::normalizeGrade($gradeParam ?? $student->getNormalizedGrade()) ?: 'X';
        $analysis = QuranTahfidzRecord::getStudentJuz30Analysis($student, $studentGrade);
        $records = $student->tahfidzRecords()->with('examiner')->orderBy('deposit_date')->orderBy('deposit_time')->get();

        $school = SchoolSetting::allAsAssoc();
        $headmasterName = SchoolSetting::get('headmaster_name') ?: ($school['headmaster_name'] ?? 'Drs. H. Ahmad Sudrajat, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip') ?: ($school['headmaster_nip'] ?? '-');

        return view('tahfidz.print_custom_progress', array_merge($analysis, [
            'student' => $student,
            'records' => $records,
            'studentGrade' => $studentGrade,
            'school' => $school,
            'headmasterName' => $headmasterName,
            'headmasterNip' => $headmasterNip,
        ]));
    }

    /**
     * Hapus Data Setoran (Khusus Penguji / Koordinator Keagamaan / Admin)
     */
    public function destroy(QuranTahfidzRecord $tahfidz): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->canManageTahfidz()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk menghapus data setoran.');
        }

        $student = $tahfidz->student;
        $isHomeroom = $user->role === 'wali_kelas';
        $homeroomClass = $isHomeroom ? $user->teacher?->homeroomClass : null;
        if ($isHomeroom && $homeroomClass && $student && $student->class_id !== $homeroomClass->id) {
            abort(403, "Akses Ditolak: Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat menghapus data siswa binaan Anda.");
        }

        $studentId = $tahfidz->student_id;
        $tahfidz->delete();

        return redirect()->route('tahfidz.student', $studentId)
            ->with('success', 'Catatan setoran hafalan berhasil dihapus.');
    }
}

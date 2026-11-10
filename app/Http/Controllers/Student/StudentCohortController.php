<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentCohortController extends Controller
{
    public function index(Request $request): View
    {
        $data = $this->prepareCohortData($request);

        return view('students.cohort.index', $data);
    }

    public function print(Request $request): View
    {
        $data = $this->prepareCohortData($request);
        $data['school'] = SchoolIdentity::current();

        return view('students.cohort.print', $data);
    }

    /**
     * @return array<string, mixed>
     */
    protected function prepareCohortData(Request $request): array
    {
        $hasAdmissionDate = Schema::hasColumn('students', 'admission_date');
        $dateCol = $hasAdmissionDate ? 'admission_date' : 'created_at';
        $driver = DB::connection()->getDriverName();
        $yearExpr = $driver === 'sqlite'
            ? "CAST(strftime('%Y', {$dateCol}) AS INTEGER) as intake_year"
            : "YEAR({$dateCol}) as intake_year";

        $cohortYears = Student::query()
            ->whereNotNull($dateCol)
            ->selectRaw($yearExpr)
            ->distinct()
            ->orderByDesc('intake_year')
            ->pluck('intake_year')
            ->filter()
            ->values()
            ->toArray();

        $currentYear = (int) date('Y');
        if (empty($cohortYears)) {
            $cohortYears = [$currentYear, $currentYear - 1, $currentYear - 2, $currentYear - 3];
        }

        // Selected cohort year
        $selectedYear = (int) $request->query('cohort', $cohortYears[0] ?? $currentYear);
        if (! in_array($selectedYear, $cohortYears)) {
            $selectedYear = $cohortYears[0];
        }

        $cohortLabel = "{$selectedYear}/".($selectedYear + 1);

        // 2. Query students for selected cohort
        $students = Student::with('schoolClass')
            ->whereYear($dateCol, $selectedYear)
            ->orderBy('name')
            ->get();

        // If no students for this exact year, find all students whose year matches or fallback
        if ($students->isEmpty() && ! empty($cohortYears)) {
            $selectedYear = $cohortYears[0];
            $cohortLabel = "{$selectedYear}/".($selectedYear + 1);
            $students = Student::with('schoolClass')
                ->whereYear($dateCol, $selectedYear)
                ->orderBy('name')
                ->get();
        }

        if ($students->isEmpty()) {
            $students = Student::with('schoolClass')->take(30)->get();
        }

        // 3. Calculate statistics for selected cohort
        $initialTotal = $students->count();
        $initialMale = $students->where('gender', 'L')->count();
        $initialFemale = $students->where('gender', 'P')->count();

        // Flow breakdown:
        $hasCurrentClassLevel = Schema::hasColumn('students', 'current_class_level');
        $hasStatus = Schema::hasColumn('students', 'status');

        $inClassX = $students->filter(function ($s) use ($hasCurrentClassLevel, $hasStatus) {
            $level = $hasCurrentClassLevel ? ($s->current_class_level ?? '') : ($s->schoolClass?->grade ?? $s->schoolClass?->level ?? '');
            $active = $hasStatus ? ($s->status === 'Aktif') : (bool) $s->is_active;

            return (str_contains((string) $level, '10') || str_contains((string) $level, 'X')) && $active;
        })->count();

        $inClassXI = $students->filter(function ($s) use ($hasCurrentClassLevel, $hasStatus) {
            $level = $hasCurrentClassLevel ? ($s->current_class_level ?? '') : ($s->schoolClass?->grade ?? $s->schoolClass?->level ?? '');
            $active = $hasStatus ? ($s->status === 'Aktif') : (bool) $s->is_active;

            return (str_contains((string) $level, '11') || str_contains((string) $level, 'XI')) && $active;
        })->count();

        $inClassXII = $students->filter(function ($s) use ($hasCurrentClassLevel, $hasStatus) {
            $level = $hasCurrentClassLevel ? ($s->current_class_level ?? '') : ($s->schoolClass?->grade ?? $s->schoolClass?->level ?? '');
            $active = $hasStatus ? ($s->status === 'Aktif') : (bool) $s->is_active;

            return (str_contains((string) $level, '12') || str_contains((string) $level, 'XII')) && $active;
        })->count();

        // Lulus
        $graduated = $students->filter(function ($s) use ($hasStatus) {
            return $hasStatus ? ($s->status === 'Lulus') : false;
        })->count();
        $graduatedMale = $students->filter(function ($s) use ($hasStatus) {
            return ($hasStatus ? ($s->status === 'Lulus') : false) && $s->gender === 'L';
        })->count();
        $graduatedFemale = $students->filter(function ($s) use ($hasStatus) {
            return ($hasStatus ? ($s->status === 'Lulus') : false) && $s->gender === 'P';
        })->count();

        // Mutasi Keluar
        $mutatedOut = $students->filter(function ($s) use ($hasStatus) {
            return $hasStatus ? ($s->status === 'Mutasi Keluar') : false;
        })->count();
        $mutatedOutMale = $students->filter(function ($s) use ($hasStatus) {
            return ($hasStatus ? ($s->status === 'Mutasi Keluar') : false) && $s->gender === 'L';
        })->count();
        $mutatedOutFemale = $students->filter(function ($s) use ($hasStatus) {
            return ($hasStatus ? ($s->status === 'Mutasi Keluar') : false) && $s->gender === 'P';
        })->count();

        // Drop out / Keluar
        $dropout = $students->filter(function ($s) use ($hasStatus) {
            return $hasStatus ? in_array($s->status, ['Mengundurkan Diri', 'Dikeluarkan', 'Drop Out']) : false;
        })->count();
        $dropoutMale = $students->filter(function ($s) use ($hasStatus) {
            return ($hasStatus ? in_array($s->status, ['Mengundurkan Diri', 'Dikeluarkan', 'Drop Out']) : false) && $s->gender === 'L';
        })->count();
        $dropoutFemale = $students->filter(function ($s) use ($hasStatus) {
            return ($hasStatus ? in_array($s->status, ['Mengundurkan Diri', 'Dikeluarkan', 'Drop Out']) : false) && $s->gender === 'P';
        })->count();

        // Surviving (still active in school or already graduated)
        $surviving = $inClassX + $inClassXI + $inClassXII + $graduated;
        if ($surviving === 0 && $initialTotal > 0) {
            $surviving = $initialTotal;
        }

        // Key Educational Efficiency Indicators
        $divisor = max($initialTotal, 1);
        $survivalRate = round(($surviving / $divisor) * 100, 1);
        $graduationRate = round(($graduated / $divisor) * 100, 1);
        $dropoutRate = round(($dropout / $divisor) * 100, 1);
        $mutationRate = round(($mutatedOut / $divisor) * 100, 1);

        // 4. Multi-year comparison table for all available cohorts
        $allCohortsSummary = [];
        foreach ($cohortYears as $yr) {
            $cohortStudents = Student::query()->whereYear($dateCol, $yr)->get();
            $tot = $cohortStudents->count();
            $lulus = $cohortStudents->filter(fn ($s) => $hasStatus ? ($s->status === 'Lulus') : false)->count();
            $aktif = $cohortStudents->filter(fn ($s) => $hasStatus ? ($s->status === 'Aktif') : (bool) $s->is_active)->count();
            $mutasi = $cohortStudents->filter(fn ($s) => $hasStatus ? ($s->status === 'Mutasi Keluar') : false)->count();
            $do = $cohortStudents->filter(fn ($s) => $hasStatus ? in_array($s->status, ['Mengundurkan Diri', 'Dikeluarkan', 'Drop Out']) : false)->count();
            $bertahan = $aktif + $lulus;

            $d = max($tot, 1);
            $allCohortsSummary[] = [
                'year' => $yr,
                'label' => "{$yr}/".($yr + 1),
                'total_entry' => $tot,
                'male_entry' => $cohortStudents->where('gender', 'L')->count(),
                'female_entry' => $cohortStudents->where('gender', 'P')->count(),
                'active' => $aktif,
                'graduated' => $lulus,
                'mutated_out' => $mutasi,
                'dropout' => $do,
                'survival_rate' => round(($bertahan / $d) * 100, 1),
                'graduation_rate' => round(($lulus / $d) * 100, 1),
            ];
        }

        return [
            'cohortYears' => $cohortYears,
            'selectedYear' => $selectedYear,
            'cohortLabel' => $cohortLabel,
            'students' => $students,
            'initialTotal' => $initialTotal,
            'initialMale' => $initialMale,
            'initialFemale' => $initialFemale,
            'inClassX' => $inClassX,
            'inClassXI' => $inClassXI,
            'inClassXII' => $inClassXII,
            'graduated' => $graduated,
            'graduatedMale' => $graduatedMale,
            'graduatedFemale' => $graduatedFemale,
            'mutatedOut' => $mutatedOut,
            'mutatedOutMale' => $mutatedOutMale,
            'mutatedOutFemale' => $mutatedOutFemale,
            'dropout' => $dropout,
            'dropoutMale' => $dropoutMale,
            'dropoutFemale' => $dropoutFemale,
            'surviving' => $surviving,
            'survivalRate' => $survivalRate,
            'graduationRate' => $graduationRate,
            'dropoutRate' => $dropoutRate,
            'mutationRate' => $mutationRate,
            'allCohortsSummary' => $allCohortsSummary,
        ];
    }
}

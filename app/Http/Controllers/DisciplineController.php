<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Services\DisciplineAutomationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DisciplineController extends Controller
{
    /**
     * Papan Peringkat (Ranking) Prestasi & Pantauan Tata Tertib
     */
    public function ranking(Request $request): View
    {
        $user = Auth::user();
        $search = $request->input('search');
        $myChildStudentId = null;

        if ($user->role === 'wali_kelas') {
            $class = $user->teacher?->homeroomClass;
            $classId = $class?->id;
            $classes = $class ? collect([$class]) : collect();
        } elseif ($user->role === 'orang_tua') {
            $myChild = $user->parentProfile?->student;
            $myChildStudentId = $myChild?->id;
            $classId = $request->input('class_id');
            $classes = SchoolClass::orderBy('name')->get();
        } elseif ($user->role === 'siswa') {
            $myChildStudentId = $user->student?->id;
            $classId = $request->input('class_id');
            $classes = SchoolClass::orderBy('name')->get();
        } else {
            $classId = $request->input('class_id');
            $classes = SchoolClass::orderBy('name')->get();
        }

        // 1. Leaderboard Siswa Berprestasi (Ordered by total verified achievement points desc)
        $studentsQuery = Student::with(['schoolClass', 'achievements', 'violations'])
            ->where('is_active', true);

        if ($classId) {
            $studentsQuery->where('class_id', $classId);
        }

        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('initials', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $allStudents = $studentsQuery->get();

        // 1. Calculate achievement leaderboard (Ordered by total verified achievement points desc)
        $allAchievers = $allStudents->map(function ($student) {
            $pts = $student->totalAchievementPoints();

            return [
                'student' => $student,
                'points' => $pts,
                'reward_status' => $student->reward_status,
                'count' => $student->achievements->where('status', 'DIVERIFIKASI')->count(),
            ];
        })->sortByDesc('points')->values();

        // Identify child position if user is student or parent
        $myChildRank = null;
        $myChildData = null;
        if ($myChildStudentId) {
            foreach ($allAchievers as $index => $item) {
                if ($item['student']->id === $myChildStudentId) {
                    $myChildRank = $index + 1;
                    $myChildData = $item;
                    break;
                }
            }
        }

        // Limit to Top 10 as requested
        $achievers = $allAchievers->take(10);

        // 2. Calculate violation & guidance watchlist (STRICTLY SEPARATED, Top 10 only)
        $guidanceWatchlist = $allStudents->map(function ($student) {
            $pts = $student->totalViolationPoints();

            return [
                'student' => $student,
                'points' => $pts,
                'guidance_status' => $student->guidance_status,
                'count' => $student->violations->where('status', 'DIVERIFIKASI')->count(),
            ];
        })->filter(fn ($item) => $item['points'] >= 75 || $item['count'] > 0)
            ->sortByDesc('points')->values()
            ->take(10);

        return view('discipline.ranking', compact('classes', 'achievers', 'guidanceWatchlist', 'classId', 'search', 'myChildStudentId', 'myChildRank', 'myChildData'));
    }

    /**
     * Laporan Terpadu Tata Tertib (Prestasi & Pelanggaran)
     */
    public function report(Request $request): View
    {
        $user = Auth::user();
        $search = $request->input('search');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        if ($user->role === 'wali_kelas') {
            $class = $user->teacher?->homeroomClass;
            $classId = $class?->id;
            $classes = $class ? collect([$class]) : collect();
        } elseif ($user->role === 'siswa') {
            $classId = $user->student?->class_id;
            $classes = collect();
        } elseif ($user->role === 'orang_tua') {
            $classId = $user->parentProfile?->student?->class_id;
            $classes = collect();
        } else {
            $classId = $request->input('class_id');
            $classes = SchoolClass::all();
        }

        $achievementCats = AchievementCategory::all();
        $violationCats = ViolationCategory::all();

        // Query Prestasi
        $achievementsQuery = Achievement::with(['student.schoolClass', 'category'])
            ->where('status', 'DIVERIFIKASI')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($classId) {
            $achievementsQuery->where('class_id', $classId);
        }

        if ($search) {
            $achievementsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('initials', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
            });
        }

        $achievements = $achievementsQuery->orderByDesc('date')->get();

        // Query Pelanggaran (STRICTLY SEPARATED)
        $violationsQuery = Violation::with(['student.schoolClass', 'category'])
            ->where('status', 'DIVERIFIKASI')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($classId) {
            $violationsQuery->where('class_id', $classId);
        }

        if ($search) {
            $violationsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('initials', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
            });
        }

        $violations = $violationsQuery->orderByDesc('date')->get();

        // Summary Statistics
        $stats = [
            'total_achievements' => $achievements->count(),
            'total_achievement_points' => $achievements->sum('points'),
            'total_violations' => $violations->count(),
            'total_violation_points' => $violations->sum('points'),
            'top_achievers_count' => $achievements->unique('student_id')->count(),
            'students_with_violations' => $violations->unique('student_id')->count(),
        ];

        return view('discipline.report', compact(
            'classes',
            'achievementCats',
            'violationCats',
            'achievements',
            'violations',
            'stats',
            'classId',
            'startDate',
            'endDate',
            'search'
        ));
    }

    /**
     * Sinkronisasi Otomatisasi Tata Tertib & Prestasi Secara Instan (On-Demand)
     * Hanya dapat diakses oleh akun admin dan wali kelas.
     * Jika wali kelas yang mengeksekusi, hanya nama siswa kelas binaannya saja yang dieksekusi.
     */
    public function syncAutomations(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $isAdmin = $user->isAdmin()
            || $user->hasAnyRole(['super_admin', 'admin'])
            || in_array($user->role, ['super_admin', 'admin'], true);

        $isHomeroom = $user->isHomeroom()
            || $user->hasRole('wali_kelas')
            || $user->role === 'wali_kelas';

        if (! $isAdmin && ! $isHomeroom) {
            abort(403, 'Akses ditolak. Fitur sinkronisasi otomasi hanya dapat diakses oleh akun admin dan wali kelas.');
        }

        $targetClassId = null;
        $classNotice = '';

        if (! $isAdmin && $isHomeroom) {
            $homeroomClasses = $user->getHomeroomClasses();
            if ($homeroomClasses->isEmpty() && $user->teacher?->homeroomClass) {
                $homeroomClasses = collect([$user->teacher->homeroomClass]);
            }

            if ($homeroomClasses->isEmpty()) {
                return back()->with('error', 'Gagal menyinkronkan otomasi: Anda belum terdaftar sebagai wali kelas pada kelas binaan manapun.');
            }

            $targetClassId = $homeroomClasses->pluck('id')->toArray();
            $classNames = $homeroomClasses->pluck('name')->implode(', ');
            $classNotice = " (Kelas {$classNames})";
        } elseif ($isAdmin && $request->filled('class_id')) {
            $targetClassId = (int) $request->input('class_id');
            $schoolClass = SchoolClass::find($targetClassId);
            if ($schoolClass) {
                $classNotice = " (Kelas {$schoolClass->name})";
            }
        }

        $targetDate = $request->input('date') ?: Carbon::today('Asia/Jakarta')->toDateString();
        $result = app(DisciplineAutomationService::class)->syncDailyAutomations($targetDate, $targetClassId, $user->id);

        return back()->with('success', 'Sinkronisasi Otomasi Tata Tertib & Prestasi'.$classNotice.' berhasil dijalankan! '.$result['summary']);
    }
}

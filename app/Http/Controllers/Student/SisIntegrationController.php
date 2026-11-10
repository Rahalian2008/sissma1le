<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentAttendance;
use App\Models\StudentViolation;
use App\Services\DapodikImportService;
use App\Services\SisSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SisIntegrationController extends Controller
{
    public function __construct(
        protected SisSyncService $sisService
    ) {}

    /**
     * Display the SIS Integration Dashboard
     */
    public function index(): View
    {
        $connection = $this->sisService->testConnection();

        $stats = [
            'total_students' => Student::count(),
            'today_attendances' => StudentAttendance::where('date', date('Y-m-d'))->count(),
            'total_achievements' => StudentAchievement::count(),
            'total_violations' => StudentViolation::count(),
        ];

        $recentAttendances = StudentAttendance::with('student')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $recentAchievements = StudentAchievement::with('student')
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        $recentViolations = StudentViolation::with('student')
            ->orderBy('violation_date', 'desc')
            ->take(5)
            ->get();

        return view('sis.index', compact(
            'connection',
            'stats',
            'recentAttendances',
            'recentAchievements',
            'recentViolations'
        ));
    }

    /**
     * Test live connection to SIS-SMA1LE
     */
    public function test(): JsonResponse
    {
        $result = $this->sisService->testConnection();

        return response()->json($result);
    }

    /**
     * Sync attendance from SIS-SMA1LE
     */
    public function syncAttendance(Request $request): RedirectResponse
    {
        $date = $request->input('date', date('Y-m-d'));
        $pages = (int) $request->input('pages', 3);

        $result = $this->sisService->syncAttendance($date, $pages);

        if ($result['status'] === 'success') {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message'] ?? 'Gagal melakukan sinkronisasi presensi.');
    }

    /**
     * Sync achievements from SIS-SMA1LE
     */
    public function syncAchievements(Request $request): RedirectResponse
    {
        $result = $this->sisService->syncAchievements();

        if ($result['status'] === 'success') {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message'] ?? 'Gagal melakukan sinkronisasi prestasi.');
    }

    /**
     * Sync violations from SIS-SMA1LE
     */
    public function syncViolations(Request $request): RedirectResponse
    {
        $result = $this->sisService->syncViolations();

        if ($result['status'] === 'success') {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message'] ?? 'Gagal melakukan sinkronisasi pelanggaran.');
    }

    /**
     * Sync master students from SIS-SMA1LE
     */
    public function syncStudents(Request $request): RedirectResponse
    {
        $pages = (int) $request->input('pages', 5);
        $result = $this->sisService->syncMasterStudents($pages);

        if ($result['status'] === 'success') {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message'] ?? 'Gagal melakukan sinkronisasi master siswa.');
    }

    /**
     * Import / Update student identities & families from Dapodik Excel file
     */
    public function syncDapodik(Request $request, DapodikImportService $dapodikService): RedirectResponse
    {
        $filePath = null;

        if ($request->hasFile('dapodik_file')) {
            $file = $request->file('dapodik_file');
            $filePath = $file->getRealPath();
        } else {
            $defaultPaths = [
                'C:\\Users\\RAHALIAN\\Downloads\\rekap daftar_pd-SMAN 1 LENGKONG-2026-08-20. (3).xlsx',
                'C:\\Users\\RAHALIAN\\Downloads\\rekap daftar_pd-SMAN 1 LENGKONG-2026-08-20. (2).xlsx',
                'C:\\Users\\RAHALIAN\\Downloads\\rekap daftar_pd-SMAN 1 LENGKONG-2026-08-20. (1).xlsx',
                'C:\\Users\\RAHALIAN\\Downloads\\rekap daftar_pd-SMAN 1 LENGKONG-2026-08-20..xlsx',
                base_path('database/data/rekap_daftar_pd.xlsx'),
            ];

            foreach ($defaultPaths as $path) {
                if (file_exists($path)) {
                    $filePath = $path;
                    break;
                }
            }
        }

        if (! $filePath || ! file_exists($filePath)) {
            return back()->with('error', 'Berkas Dapodik tidak ditemukan pada folder Downloads atau unggahan.');
        }

        $result = $dapodikService->importFromFile($filePath);

        if ($result['status'] === 'success') {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message'] ?? 'Gagal memproses berkas Dapodik.');
    }
}

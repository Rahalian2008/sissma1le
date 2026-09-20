<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\SchoolSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPortalController extends Controller
{
    /**
     * Portal Orang Tua Publik (Tanpa Login, Berbasis NISN Siswa)
     */
    public function index(Request $request): View
    {
        $nisn = trim($request->input('nisn', $request->query('nisn', '')));
        $student = null;
        $todayAttendance = null;
        $recentAttendances = collect();
        $attendanceStats = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0,
        ];
        $habits = collect();
        $todayHabitLogs = collect();
        $achievements = collect();
        $violations = collect();
        $guidanceRecords = collect();
        $errorMessage = null;

        $schoolSetting = SchoolSetting::first();
        $schoolName = $schoolSetting->school_name ?? config('app.name', 'SISSMA1LE');

        if (! empty($nisn)) {
            $student = Student::with(['schoolClass', 'academicYear'])
                ->where('nisn', $nisn)
                ->first();

            if ($student) {
                $today = Carbon::today('Asia/Jakarta')->toDateString();

                // Presensi Hari Ini
                $todayAttendance = Attendance::where('student_id', $student->id)
                    ->where('date', $today)
                    ->first();

                // Riwayat Presensi 30 Hari Terakhir
                $recentAttendances = Attendance::where('student_id', $student->id)
                    ->orderByDesc('date')
                    ->take(30)
                    ->get();

                // Statistik Kehadiran
                $attendanceStats = [
                    'hadir' => $recentAttendances->where('status', 'HADIR')->count(),
                    'terlambat' => $recentAttendances->where('status', 'TERLAMBAT')->count(),
                    'izin' => $recentAttendances->where('status', 'IZIN')->count(),
                    'sakit' => $recentAttendances->where('status', 'SAKIT')->count(),
                    'alfa' => $recentAttendances->where('status', 'ALFA')->count(),
                ];

                // 7 Kebiasaan Anak Hebat Hari Ini
                $habits = Habit::orderBy('order_number')->get();
                $todayHabitLogs = HabitDailyLog::where('student_id', $student->id)
                    ->where('date', $today)
                    ->get()
                    ->keyBy('habit_id');

                // Prestasi Terverifikasi
                $achievements = $student->achievements()
                    ->where('status', 'DIVERIFIKASI')
                    ->orderByDesc('date')
                    ->get();

                // Pelanggaran Terverifikasi
                $violations = $student->violations()
                    ->where('status', 'DIVERIFIKASI')
                    ->orderByDesc('date')
                    ->get();

                // Catatan Bimbingan / Pembinaan
                $guidanceRecords = $student->guidanceRecords()
                    ->orderByDesc('date')
                    ->get();
            } else {
                $errorMessage = "Siswa dengan NISN '{$nisn}' tidak ditemukan. Mohon periksa kembali nomor NISN yang Anda masukkan atau hubungi wali kelas/pihak sekolah.";
            }
        }

        return view('public.parent_portal', compact(
            'nisn',
            'student',
            'todayAttendance',
            'recentAttendances',
            'attendanceStats',
            'habits',
            'todayHabitLogs',
            'achievements',
            'violations',
            'guidanceRecords',
            'errorMessage',
            'schoolSetting',
            'schoolName'
        ));
    }
}

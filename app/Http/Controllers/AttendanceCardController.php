<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceCardController extends Controller
{
    /**
     * Tampilan Pusat Kelola & Cetak Kartu Absensi Siswa
     */
    public function index(Request $request): View
    {
        if (Auth::check() && (Auth::user()->hasRole('siswa') || Auth::user()->role === 'siswa')) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses dokumen.');
        }

        $user = Auth::user();
        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']);

        if ($isWaliKelas) {
            $classes = $user->getHomeroomClasses();
            $requestedClassId = $request->input('class_id');
            if ($requestedClassId && $classes->contains('id', (int) $requestedClassId)) {
                $classId = (int) $requestedClassId;
            } else {
                $classId = $classes->first()?->id;
            }
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $classId = $request->input('class_id');
        }

        $search = $request->input('search');
        $mode = $request->input('mode', 'combo'); // 'qr', 'rfid', 'combo'

        $query = Student::with('schoolClass')->where('is_active', true);

        if ($classId) {
            $query->where('class_id', $classId);
        } elseif ($isWaliKelas && $classes->isNotEmpty()) {
            $query->whereIn('class_id', $classes->pluck('id'));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('initials', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('rfid_uid', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(24)->withQueryString();

        $stats = [
            'total_students' => Student::where('is_active', true)->when($classId, fn ($q) => $q->where('class_id', $classId))->count(),
            'with_rfid' => Student::where('is_active', true)->when($classId, fn ($q) => $q->where('class_id', $classId))->whereNotNull('rfid_uid')->where('rfid_uid', '!=', '')->count(),
            'with_nisn' => Student::where('is_active', true)->when($classId, fn ($q) => $q->where('class_id', $classId))->whereNotNull('nisn')->where('nisn', '!=', '')->count(),
        ];

        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        return view('attendance.cards.index', compact('classes', 'students', 'classId', 'selectedClass', 'search', 'mode', 'stats'));
    }

    /**
     * Halaman Cetak Lembar Kartu Absensi (A4 Grid / CR80 ID Card Standard)
     */
    public function print(Request $request): View
    {
        if (Auth::check() && (Auth::user()->hasRole('siswa') || Auth::user()->role === 'siswa')) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencetak dokumen.');
        }

        $user = Auth::user();
        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']);

        $classId = $request->input('class_id');
        if ($isWaliKelas) {
            $homeroomClasses = $user->getHomeroomClasses();
            if ($classId && $homeroomClasses->contains('id', (int) $classId)) {
                $classId = (int) $classId;
            } else {
                $classId = $homeroomClasses->first()?->id;
            }
        }
        $studentId = $request->input('student_id');
        $mode = $request->input('mode', 'combo'); // 'qr', 'rfid', 'combo'
        $side = $request->input('side', 'both'); // 'front', 'back', 'both'

        $query = Student::with('schoolClass')->where('is_active', true);

        if ($studentId) {
            $query->where('id', $studentId);
        } elseif ($classId) {
            $query->where('class_id', $classId);
        } elseif ($isWaliKelas && isset($homeroomClasses) && $homeroomClasses->isNotEmpty()) {
            $query->whereIn('class_id', $homeroomClasses->pluck('id'));
        }

        $students = $query->orderBy('name')->get();

        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        $school = [
            'name' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'province' => SchoolSetting::get('school_province', 'JAWA BARAT'),
            'branch' => SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V'),
            'address' => SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kec. Lengkong, Kab. Sukabumi'),
            'npsn' => SchoolSetting::get('school_npsn', '20202265'),
            'headmaster_name' => SchoolSetting::get('headmaster_name', 'Pian Sopian, S.Pd., M.M.Pd'),
            'headmaster_nip' => SchoolSetting::get('headmaster_nip', '198009132008011005'),
            'logo' => SchoolSetting::get('school_logo'),
            'year' => '2026/2027',
        ];

        return view('attendance.cards.print', compact('students', 'selectedClass', 'mode', 'side', 'school'));
    }

    /**
     * Cetak Kartu Individual untuk Siswa Tertentu
     */
    public function single(Student $student, Request $request): View
    {
        if (Auth::check() && (Auth::user()->hasRole('siswa') || Auth::user()->role === 'siswa')) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencetak dokumen.');
        }

        $mode = $request->input('mode', 'combo');
        $side = $request->input('side', 'both');

        $students = collect([$student->load('schoolClass')]);
        $selectedClass = $student->schoolClass;

        $school = [
            'name' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'province' => SchoolSetting::get('school_province', 'JAWA BARAT'),
            'branch' => SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V'),
            'address' => SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kec. Lengkong, Kab. Sukabumi'),
            'npsn' => SchoolSetting::get('school_npsn', '20202265'),
            'headmaster_name' => SchoolSetting::get('headmaster_name', 'Pian Sopian, S.Pd., M.M.Pd'),
            'headmaster_nip' => SchoolSetting::get('headmaster_nip', '198009132008011005'),
            'logo' => SchoolSetting::get('school_logo'),
            'year' => '2026/2027',
        ];

        return view('attendance.cards.print', compact('students', 'selectedClass', 'mode', 'side', 'school'));
    }
}

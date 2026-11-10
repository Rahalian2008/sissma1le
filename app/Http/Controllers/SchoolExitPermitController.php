<?php

namespace App\Http\Controllers;

use App\Models\PicketSchedule;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolExitPermitController extends Controller
{
    /**
     * Tampilkan Daftar Surat Izin Keluar Lingkungan Sekolah (Sementara)
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $school = SchoolIdentity::current();
        $isStaff = $user->canManageSchoolPermits();
        $isStudent = ($user->hasRole('siswa') || $user->role === 'siswa') && ! $isStaff;

        $student = null;
        if ($isStudent) {
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
        }

        $query = SchoolExitPermit::with(['students.schoolClass', 'approver', 'creator']);

        if ($isStudent) {
            if ($student) {
                $query->whereHas('students', fn ($q) => $q->where('students.id', $student->id));
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif (($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'kesiswaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass) {
                $query->whereHas('students', fn ($q) => $q->where('class_id', $homeroomClass->id));
            }
        }

        // Filter Status
        if ($status = $request->query('status')) {
            if ($status !== 'ALL') {
                $query->where('status', $status);
            }
        }

        // Filter Tanggal
        if ($date = $request->query('date')) {
            $query->whereDate('date', $date);
        }

        // Pencarian Nomor Surat / Tujuan / Alasan / Nama Siswa
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('permit_number', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('students', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%");
                    });
            });
        }

        $permits = $query->orderByDesc('date')->orderByDesc('out_time')->paginate(15)->withQueryString();

        // Statistik Real-time
        $baseStatQuery = SchoolExitPermit::query();
        if ($isStudent && $student) {
            $baseStatQuery->whereHas('students', fn ($q) => $q->where('students.id', $student->id));
        }

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $stats = [
            'pending' => (clone $baseStatQuery)->where('status', 'PENDING')->count(),
            'approved_outside' => (clone $baseStatQuery)->where('status', 'APPROVED')->whereNull('actual_back_time')->count(),
            'returned' => (clone $baseStatQuery)->where('status', 'RETURNED')->count(),
            'today' => (clone $baseStatQuery)->whereDate('date', $today)->count(),
        ];

        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();
        $allStudents = Student::where('is_active', true)->with('schoolClass')->orderBy('name')->get();

        return view('attendance.school_pass.index', compact(
            'permits',
            'school',
            'stats',
            'classes',
            'allStudents',
            'isStaff',
            'isStudent',
            'student'
        ));
    }

    /**
     * Simpan Ajuan Izin Keluar Lingkungan Sekolah Baru (Bisa > 1 Siswa / Rombongan)
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isStaff = $user->canManageSchoolPermits();

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'date' => ['required', 'date'],
            'out_time' => ['required'],
            'expected_back_time' => ['nullable'],
            'purpose' => ['required', 'string', 'max:150'],
            'destination' => ['nullable', 'string', 'max:200'],
            'reason' => ['required', 'string', 'max:1000'],
            'accompanied_by' => ['nullable', 'string', 'max:150'],
            'is_returning' => ['nullable', 'boolean'],
            'direct_approve' => ['nullable', 'boolean'],
        ]);

        $dateCarbon = Carbon::parse($validated['date']);
        $permitNumber = SchoolExitPermit::generatePermitNumber($dateCarbon);

        $requesterType = 'SISWA';
        if ($user->hasRole('wali_kelas')) {
            $requesterType = 'WALI_KELAS';
        } elseif ($user->hasRole('guru') || $user->hasRole('guru_piket')) {
            $requesterType = 'GURU_PIKET';
        } elseif ($user->hasRole('tu') || $user->hasRole('staff') || $user->hasRole('tu_piket')) {
            $requesterType = 'TU_PIKET';
        } elseif ($user->hasAnyRole(['admin', 'super_admin'])) {
            $requesterType = 'ADMIN';
        }

        $shouldDirectApprove = $isStaff && $request->boolean('direct_approve', true);

        $permit = SchoolExitPermit::create([
            'permit_number' => $permitNumber,
            'date' => $validated['date'],
            'out_time' => $validated['out_time'],
            'expected_back_time' => $validated['expected_back_time'] ?? null,
            'purpose' => $validated['purpose'],
            'destination' => $validated['destination'] ?? null,
            'reason' => $validated['reason'],
            'accompanied_by' => $validated['accompanied_by'] ?? null,
            'is_returning' => $request->boolean('is_returning', true),
            'status' => $shouldDirectApprove ? 'APPROVED' : 'PENDING',
            'requester_type' => $requesterType,
            'created_by' => $user->id,
            'approved_by' => $shouldDirectApprove ? $user->id : null,
            'approved_at' => $shouldDirectApprove ? now() : null,
            'qr_code_token' => Str::uuid()->toString(),
        ]);

        // Hubungkan seluruh siswa yang diajukan (Mendukung > 1 siswa)
        $permit->students()->sync($validated['student_ids']);

        $studentCount = count($validated['student_ids']);
        AuditTrailService::log('create', 'school_exit_permit', "Membuat surat izin keluar lingkungan sekolah ({$studentCount} siswa) No. {$permitNumber}");

        $msg = $shouldDirectApprove
            ? "Surat Izin Keluar Lingkungan Sekolah ({$permitNumber}) untuk {$studentCount} siswa berhasil disetujui & siap dicetak!"
            : "Ajuan izin keluar lingkungan sekolah ({$studentCount} siswa) berhasil diajukan dan sedang menunggu persetujuan Guru Piket / TU Piket.";

        $redirect = back()->with('success', $msg);
        if ($shouldDirectApprove) {
            $redirect->with('print_url', route('attendance.school-pass.print', $permit->id))
                ->with('print_title', "Surat Izin Keluar Sekolah ({$permitNumber})");
        }

        return $redirect;
    }

    /**
     * Setujui Ajuan Izin Keluar Lingkungan Sekolah & Terbitkan Surat Izin Resmi
     */
    public function approve(Request $request, SchoolExitPermit $permit): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits()) {
            abort(403, 'Anda tidak memiliki hak untuk menyetujui izin keluar sekolah.');
        }

        $permit->update([
            'status' => 'APPROVED',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('notes'),
        ]);

        AuditTrailService::log('update', 'school_exit_permit', "Menyetujui izin keluar lingkungan sekolah No: {$permit->permit_number}");

        return back()
            ->with('success', "Izin keluar sekolah berhasil disetujui! Surat Izin No. {$permit->permit_number} siap dicetak.")
            ->with('print_url', route('attendance.school-pass.print', $permit->id))
            ->with('print_title', "Surat Izin Keluar Sekolah ({$permit->permit_number})");
    }

    /**
     * Konfirmasi Siswa Telah Kembali ke Lingkungan Sekolah
     */
    public function markReturned(Request $request, SchoolExitPermit $permit): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits()) {
            abort(403, 'Anda tidak memiliki wewenang untuk memperbarui status kepulangan siswa.');
        }

        $actualTime = $request->input('actual_back_time', now('Asia/Jakarta')->format('H:i'));

        $permit->update([
            'status' => 'RETURNED',
            'actual_back_time' => $actualTime,
            'security_checked_in_at' => now(),
            'approval_notes' => $request->input('notes') ?? $permit->approval_notes,
        ]);

        AuditTrailService::log('update', 'school_exit_permit', "Mengonfirmasi siswa telah kembali ke sekolah untuk izin No: {$permit->permit_number} pada {$actualTime} WIB");

        return back()->with('success', "Siswa telah dikonfirmasi kembali ke lingkungan sekolah pada pukul {$actualTime} WIB.");
    }

    /**
     * Tolak Ajuan Izin Keluar Lingkungan Sekolah
     */
    public function reject(Request $request, SchoolExitPermit $permit): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits()) {
            abort(403, 'Anda tidak memiliki hak untuk menolak izin keluar sekolah.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $permit->update([
            'status' => 'REJECTED',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $validated['reason'],
        ]);

        AuditTrailService::log('update', 'school_exit_permit', "Menolak izin keluar sekolah No: {$permit->permit_number} - Alasan: {$validated['reason']}");

        return back()->with('info', "Ajuan izin keluar lingkungan sekolah No: {$permit->permit_number} telah ditolak.");
    }

    /**
     * Cetak Surat Izin Keluar Lingkungan Sekolah Resmi (Ber-Kop Sekolah dari Master Data)
     */
    public function printLetter(SchoolExitPermit $permit): View
    {
        $school = SchoolIdentity::current();
        $permit->load(['students.schoolClass.homeroomTeacher', 'approver.teacher', 'creator.teacher']);

        $firstStudent = $permit->students->first();
        $homeroomTeacher = $firstStudent?->schoolClass?->homeroomTeacher;

        // Ambil Petugas Piket yang bertugas pada hari tersebut
        $picketDayName = PicketSchedule::indonesianDayName($permit->date);
        $todayPickets = PicketSchedule::active()
            ->with(['teacher', 'user.teacher'])
            ->where('day_name', $picketDayName)
            ->get();

        $picketOfficer = $todayPickets->firstWhere('role_type', 'GURU_PIKET')
            ?? $todayPickets->first()
            ?? ($permit->approver?->hasAnyRole(['guru_piket', 'tu_piket', 'petugas_piket']) ? $permit->approver : null);

        AuditTrailService::log('print', 'school_exit_permit', "Mencetak Surat Izin Keluar Lingkungan Sekolah siswa {$firstStudent?->name}");

        return view('attendance.school_pass.print_letter', compact('permit', 'school', 'homeroomTeacher', 'picketOfficer', 'todayPickets', 'firstStudent'));
    }

    /**
     * Edit Izin Keluar Sekolah
     */
    public function edit(SchoolExitPermit $permit): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits() && $permit->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit data ini.');
        }

        $school = SchoolIdentity::current();
        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();
        $allStudents = Student::where('is_active', true)->with('schoolClass')->orderBy('name')->get();

        return view('attendance.school_pass.edit', compact('permit', 'school', 'classes', 'allStudents'));
    }

    /**
     * Update Izin Keluar Sekolah
     */
    public function update(Request $request, SchoolExitPermit $permit): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits() && $permit->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit data ini.');
        }

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'date' => ['required', 'date'],
            'out_time' => ['required'],
            'expected_back_time' => ['nullable'],
            'purpose' => ['required', 'string', 'max:150'],
            'destination' => ['nullable', 'string', 'max:200'],
            'reason' => ['required', 'string', 'max:1000'],
            'accompanied_by' => ['nullable', 'string', 'max:150'],
            'is_returning' => ['nullable', 'boolean'],
        ]);

        $permit->update([
            'date' => $validated['date'],
            'out_time' => $validated['out_time'],
            'expected_back_time' => $validated['expected_back_time'] ?? null,
            'purpose' => $validated['purpose'],
            'destination' => $validated['destination'] ?? null,
            'reason' => $validated['reason'],
            'accompanied_by' => $validated['accompanied_by'] ?? null,
            'is_returning' => $request->boolean('is_returning', true),
        ]);

        $permit->students()->sync($validated['student_ids']);

        AuditTrailService::log('update', 'school_exit_permit', "Mengubah data izin keluar sekolah No: {$permit->permit_number}");

        return redirect()->route('attendance.school-pass.index')->with('success', "Data surat izin keluar sekolah No. {$permit->permit_number} berhasil diperbarui.");
    }

    /**
     * Hapus Data Izin Keluar Sekolah
     */
    public function destroy(SchoolExitPermit $permit): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits() && $permit->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus data ini.');
        }

        $permitNum = $permit->permit_number;
        $permit->students()->detach();
        $permit->delete();

        AuditTrailService::log('delete', 'school_exit_permit', "Menghapus izin keluar sekolah No. {$permitNum}");

        return back()->with('success', "Surat izin keluar sekolah No. {$permitNum} berhasil dihapus.");
    }

    /**
     * Halaman Rekapitulasi Izin Keluar Sekolah
     */
    public function recap(Request $request): View
    {
        return $this->printRecap($request);
    }

    /**
     * Cetak Rekapitulasi Laporan Izin Keluar Sekolah Sementara (Ber-Kop Sekolah dari Master Data)
     * Tanda tangan: Kepala Sekolah (kiri) & Wakasek Kesiswaan (kanan)
     */
    public function printRecap(Request $request): View
    {
        $school = SchoolIdentity::current();
        $user = Auth::user();

        $startDate = $request->query('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now('Asia/Jakarta')->toDateString());
        $classId = $request->query('class_id');
        $status = $request->query('status');

        $query = SchoolExitPermit::with(['students.schoolClass', 'approver', 'creator'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($classId) {
            $query->whereHas('students', fn ($q) => $q->where('class_id', $classId));
        }

        if ($status && $status !== 'ALL') {
            $query->where('status', $status);
        }

        $records = $query->orderBy('date')->orderBy('out_time')->get();

        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        // Signatures: Kepala Sekolah di kiri, Wakasek Kesiswaan di kanan
        $principal = User::where('role', 'kepala_sekolah')
            ->orWhereJsonContains('roles', 'kepala_sekolah')
            ->first();
        $principalName = $principal?->name ?? ($school?->headmaster_name ?? 'Pian Sopian, S.Pd., MM.Pd.');
        $principalNip = $principal?->nip ?? ($school?->headmaster_nip ?? '198009132008011005');

        $wakasekKesiswaan = User::where('role', 'wakasek_kesiswaan')
            ->orWhere('role', 'kesiswaan')
            ->orWhereJsonContains('roles', 'wakasek_kesiswaan')
            ->orWhereJsonContains('roles', 'kesiswaan')
            ->first();
        $wakasekName = $wakasekKesiswaan?->name ?? 'Winda Dwi Atyani, S.Pd., M.Pd.';
        $wakasekNip = $wakasekKesiswaan?->nip ?? ($wakasekKesiswaan?->teacher?->nip ?? '197911162008012007');

        AuditTrailService::log('print', 'school_exit_permit', "Mencetak Rekapitulasi Laporan Izin Keluar Sekolah Periode {$startDate} s/d {$endDate}");

        return view('attendance.school_pass.print_recap', compact(
            'records',
            'school',
            'startDate',
            'endDate',
            'selectedClass',
            'status',
            'principalName',
            'principalNip',
            'wakasekName',
            'wakasekNip'
        ));
    }
}

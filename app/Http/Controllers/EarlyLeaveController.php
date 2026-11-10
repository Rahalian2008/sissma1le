<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EarlyLeaveRequest;
use App\Models\PicketSchedule;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EarlyLeaveController extends Controller
{
    /**
     * Tampilkan Daftar Ajuan Pulang Cepat Siswa
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

        $query = EarlyLeaveRequest::with(['student.schoolClass', 'approver', 'creator']);

        if ($isStudent) {
            if ($student) {
                $query->where('student_id', $student->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif (($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'kesiswaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass) {
                $query->whereHas('student', fn ($q) => $q->where('class_id', $homeroomClass->id));
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

        // Filter Kelas
        if ($classId = $request->query('class_id')) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
        }

        // Pencarian Nama / NIS / No. Surat
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('letter_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhere('pickup_name', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->orderByDesc('date')->orderByDesc('leave_time')->paginate(15)->withQueryString();

        // Statistik Real-time
        $baseStatQuery = EarlyLeaveRequest::query();
        if ($isStudent && $student) {
            $baseStatQuery->where('student_id', $student->id);
        }

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $stats = [
            'pending' => (clone $baseStatQuery)->where('status', 'PENDING')->count(),
            'approved' => (clone $baseStatQuery)->where('status', 'APPROVED')->count(),
            'rejected' => (clone $baseStatQuery)->where('status', 'REJECTED')->count(),
            'today' => (clone $baseStatQuery)->whereDate('date', $today)->count(),
        ];

        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();
        $allStudents = Student::where('is_active', true)->with('schoolClass')->orderBy('name')->get();

        return view('attendance.early_leave.index', compact(
            'requests',
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
     * Simpan Ajuan Pulang Cepat Baru (Mandiri oleh Siswa atau Manual oleh Wali Kelas / Guru Piket / TU Piket)
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isStaff = $user->canManageSchoolPermits();

        $validated = $request->validate([
            'student_id' => [$isStaff ? 'required' : 'nullable', 'exists:students,id'],
            'date' => ['required', 'date'],
            'leave_time' => ['required'],
            'reason_category' => ['required', 'string', 'max:100'],
            'reason' => ['required', 'string', 'max:1000'],
            'pickup_name' => ['nullable', 'string', 'max:150'],
            'pickup_relationship' => ['nullable', 'string', 'max:100'],
            'pickup_phone' => ['nullable', 'string', 'max:50'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'direct_approve' => ['nullable', 'boolean'],
        ]);

        $studentId = $validated['student_id'] ?? null;
        if (! $studentId) {
            $currentStudent = $user->student ?? Student::where('user_id', $user->id)->first();
            if (! $currentStudent) {
                return back()->with('error', 'Profil data siswa Anda tidak ditemukan.');
            }
            $studentId = $currentStudent->id;
        }

        $student = Student::findOrFail($studentId);

        // Upload lampiran jika ada
        $attachmentPath = null;
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $attachmentPath = $request->file('attachment')->store('early_leaves', 'public');
        }

        $dateCarbon = Carbon::parse($validated['date']);
        $letterNumber = EarlyLeaveRequest::generateLetterNumber($dateCarbon);

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

        $earlyLeave = EarlyLeaveRequest::create([
            'letter_number' => $letterNumber,
            'student_id' => $student->id,
            'date' => $validated['date'],
            'leave_time' => $validated['leave_time'],
            'reason_category' => $validated['reason_category'],
            'reason' => $validated['reason'],
            'pickup_name' => $validated['pickup_name'] ?? null,
            'pickup_relationship' => $validated['pickup_relationship'] ?? null,
            'pickup_phone' => $validated['pickup_phone'] ?? null,
            'attachment_path' => $attachmentPath,
            'status' => $shouldDirectApprove ? 'APPROVED' : 'PENDING',
            'requester_type' => $requesterType,
            'created_by' => $user->id,
            'approved_by' => $shouldDirectApprove ? $user->id : null,
            'approved_at' => $shouldDirectApprove ? now() : null,
            'qr_code_token' => Str::uuid()->toString(),
        ]);

        if ($shouldDirectApprove) {
            $this->syncWithAttendance($earlyLeave, $user);
        }

        AuditTrailService::log('create', 'early_leave', "Membuat ajuan pulang cepat untuk {$student->name} ({$student->nis}) No. Surat: {$letterNumber}");

        $msg = $shouldDirectApprove
            ? "Ajuan pulang cepat untuk {$student->name} berhasil disetujui & Surat Keterangan resmi ({$letterNumber}) siap dicetak!"
            : "Ajuan pulang cepat untuk {$student->name} berhasil diajukan dan sedang menunggu persetujuan Guru Piket / Wali Kelas.";

        $redirect = back()->with('success', $msg);
        if ($shouldDirectApprove) {
            $redirect->with('print_url', route('attendance.early-leave.print', $earlyLeave->id))
                ->with('print_title', "Surat Keterangan Pulang Cepat ({$letterNumber})");
        }

        return $redirect;
    }

    /**
     * Setujui Ajuan Pulang Cepat & Otomatis Buatkan Surat Keterangan Pulang Cepat Resmi
     */
    public function approve(Request $request, EarlyLeaveRequest $earlyLeave): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits()) {
            abort(403, 'Anda tidak memiliki hak untuk menyetujui ajuan pulang cepat.');
        }

        $earlyLeave->update([
            'status' => 'APPROVED',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('notes'),
        ]);

        $this->syncWithAttendance($earlyLeave, $user);

        AuditTrailService::log('update', 'early_leave', "Menyetujui ajuan pulang cepat {$earlyLeave->student?->name} No: {$earlyLeave->letter_number}");

        return back()
            ->with('success', "Ajuan pulang cepat disetujui! Surat Keterangan Pulang Cepat No. {$earlyLeave->letter_number} telah diterbitkan dan dapat dicetak.")
            ->with('print_url', route('attendance.early-leave.print', $earlyLeave->id))
            ->with('print_title', "Surat Keterangan Pulang Cepat ({$earlyLeave->letter_number})");
    }

    /**
     * Tolak Ajuan Pulang Cepat
     */
    public function reject(Request $request, EarlyLeaveRequest $earlyLeave): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits()) {
            abort(403, 'Anda tidak memiliki hak untuk menolak ajuan pulang cepat.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $earlyLeave->update([
            'status' => 'REJECTED',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $validated['reason'],
        ]);

        AuditTrailService::log('update', 'early_leave', "Menolak ajuan pulang cepat {$earlyLeave->student?->name} No: {$earlyLeave->letter_number} - Alasan: {$validated['reason']}");

        return back()->with('info', "Ajuan pulang cepat untuk {$earlyLeave->student?->name} telah ditolak.");
    }

    /**
     * Cetak Surat Keterangan Pulang Cepat Resmi Sekolah (Ber-Kop Sekolah dari Master Data)
     */
    public function printLetter(EarlyLeaveRequest $earlyLeave): View
    {
        $school = SchoolIdentity::current();
        $earlyLeave->load(['student.schoolClass.homeroomTeacher', 'approver.teacher', 'creator.teacher']);

        $homeroomTeacher = $earlyLeave->student?->schoolClass?->homeroomTeacher;

        // Ambil Petugas Piket yang bertugas pada hari tersebut
        $picketDayName = PicketSchedule::indonesianDayName($earlyLeave->date);
        $todayPickets = PicketSchedule::active()
            ->with(['teacher', 'user.teacher'])
            ->where('day_name', $picketDayName)
            ->get();

        $picketOfficer = $todayPickets->firstWhere('role_type', 'GURU_PIKET')
            ?? $todayPickets->first()
            ?? ($earlyLeave->approver?->hasAnyRole(['guru_piket', 'tu_piket', 'petugas_piket']) ? $earlyLeave->approver : null);

        AuditTrailService::log('print', 'early_leave', "Mencetak Surat Keterangan Pulang Cepat siswa {$earlyLeave->student?->name}");

        return view('attendance.early_leave.print_letter', compact('earlyLeave', 'school', 'homeroomTeacher', 'picketOfficer', 'todayPickets'));
    }

    /**
     * Edit Ajuan Pulang Cepat
     */
    public function edit(EarlyLeaveRequest $earlyLeave): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits() && $earlyLeave->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit data ini.');
        }

        $school = SchoolIdentity::current();
        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();
        $allStudents = Student::where('is_active', true)->with('schoolClass')->orderBy('name')->get();

        return view('attendance.early_leave.edit', compact('earlyLeave', 'school', 'classes', 'allStudents'));
    }

    /**
     * Update Ajuan Pulang Cepat
     */
    public function update(Request $request, EarlyLeaveRequest $earlyLeave): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits() && $earlyLeave->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit data ini.');
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'leave_time' => ['required'],
            'reason_category' => ['required', 'string', 'max:100'],
            'reason' => ['required', 'string', 'max:1000'],
            'pickup_name' => ['nullable', 'string', 'max:150'],
            'pickup_relationship' => ['nullable', 'string', 'max:100'],
            'pickup_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $earlyLeave->update($validated);

        if ($earlyLeave->status === 'APPROVED') {
            $this->syncWithAttendance($earlyLeave, $user);
        }

        AuditTrailService::log('update', 'early_leave', "Mengubah data ajuan pulang cepat {$earlyLeave->student?->name} No: {$earlyLeave->letter_number}");

        return redirect()->route('attendance.early-leave.index')->with('success', "Data ajuan pulang cepat No. {$earlyLeave->letter_number} berhasil diperbarui.");
    }

    /**
     * Hapus Data Ajuan Pulang Cepat
     */
    public function destroy(EarlyLeaveRequest $earlyLeave): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits() && $earlyLeave->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus data ini.');
        }

        $studentName = $earlyLeave->student?->name ?? 'Siswa';
        $letterNum = $earlyLeave->letter_number;

        // Reset status presensi jika sudah di-approve
        if ($earlyLeave->attendance_id) {
            $att = Attendance::find($earlyLeave->attendance_id);
            if ($att) {
                if ($att->status === 'PULANG_CEPAT') {
                    $att->delete();
                } else {
                    $att->update([
                        'is_early_leave' => false,
                        'early_leave_time' => null,
                        'early_leave_reason' => null,
                    ]);
                }
            }
        }

        $earlyLeave->delete();

        AuditTrailService::log('delete', 'early_leave', "Menghapus ajuan pulang cepat {$studentName} No. {$letterNum}");

        return back()->with('success', "Ajuan pulang cepat No. {$letterNum} untuk {$studentName} berhasil dihapus.");
    }

    /**
     * Halaman Rekapitulasi Ajuan Pulang Cepat Siswa
     */
    public function recap(Request $request): View
    {
        return $this->printRecap($request);
    }

    /**
     * Cetak Rekapitulasi Laporan Ajuan Pulang Cepat Siswa (Ber-Kop Sekolah dari Master Data)
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

        $query = EarlyLeaveRequest::with(['student.schoolClass', 'approver', 'creator'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($classId) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
        }

        if ($status && $status !== 'ALL') {
            $query->where('status', $status);
        }

        $records = $query->orderBy('date')->orderBy('leave_time')->get();

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

        AuditTrailService::log('print', 'early_leave', "Mencetak Rekapitulasi Laporan Ajuan Pulang Cepat Periode {$startDate} s/d {$endDate}");

        return view('attendance.early_leave.print_recap', compact(
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

    /**
     * Sinkronisasi data ke tabel Presensi Siswa Harian
     */
    private function syncWithAttendance(EarlyLeaveRequest $earlyLeave, $approverUser): void
    {
        $student = $earlyLeave->student;
        $dateStr = $earlyLeave->date->toDateString();

        $attendance = Attendance::firstOrNew([
            'student_id' => $student->id,
            'date' => $dateStr,
        ]);

        $attendance->fill([
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'student_name' => $student->name,
            'class_id' => $student->class_id,
            'time' => $attendance->time ?? $earlyLeave->leave_time,
            'method' => $attendance->method ?? 'MANUAL',
            'is_early_leave' => true,
            'early_leave_time' => $earlyLeave->leave_time,
            'early_leave_reason' => "[Pulang Cepat Resmi: {$earlyLeave->reason_category}] {$earlyLeave->reason}",
            'status' => 'PULANG_CEPAT',
            'verification_status' => 'DIVERIFIKASI',
            'approved_by' => $approverUser->id,
            'approved_at' => now(),
            'notes' => "Surat Keterangan Pulang Cepat No: {$earlyLeave->letter_number}. Penjemput: {$earlyLeave->pickup_name} ({$earlyLeave->pickup_relationship}).",
        ]);

        $attendance->save();
        $earlyLeave->update(['attendance_id' => $attendance->id]);
    }
}

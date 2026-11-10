<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Services\CsvImportService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViolationController extends Controller
{
    public function __construct(
        protected CsvImportService $csvImportService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Violation::with(['student.schoolClass', 'category', 'item', 'reporter', 'verifier'])
            ->orderByDesc('date');

        $classId = null;
        if ($user->isStudent()) {
            $student = $user->student ?? Student::first();
            $query->where('student_id', $student->id);
            $classes = collect();
        } elseif ($user->isParent()) {
            $student = $user->parentProfile?->student ?? Student::first();
            $query->where('student_id', $student->id);
            $classes = collect();
        } elseif ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'kesiswaan', 'wakasek_kesiswaan', 'kepala_sekolah'])) {
            $class = $user->teacher?->homeroomClass;
            $classId = $class?->id;
            $classes = $class ? collect([$class]) : collect();
            if ($class) {
                $query->where(function ($q) use ($class, $user) {
                    $q->where('class_id', $class->id)
                        ->orWhere('reporter_id', $user->id);
                });
            }
        } else {
            $classes = SchoolClass::all();
            $classId = $request->input('class_id');
            if ($classId) {
                $query->where('class_id', $classId);
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('initials', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%");
                    });
            });
        }

        $violations = $query->paginate(15)->withQueryString();
        $categories = ViolationCategory::all();

        $studentContext = null;
        if ($user->isStudent() || $user->isParent()) {
            $studentContext = $user->isStudent() ? ($user->student ?? Student::first()) : ($user->parentProfile?->student ?? Student::first());
        }

        return view('violations.index', compact('violations', 'categories', 'classes', 'classId', 'studentContext'));
    }

    public function create(): View
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan', 'wakasek_kesiswaan', 'bk', 'pembina_ekstra', 'koordinator_keagamaan'])) {
            abort(403, 'Akun ini tidak memiliki hak akses untuk mencatat pelanggaran (Read-Only).');
        }

        $categories = ViolationCategory::with('items')->get();

        $classes = SchoolClass::all();
        $students = Student::with('schoolClass')->where('is_active', true)->get();

        return view('violations.create', compact('categories', 'students', 'classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan', 'wakasek_kesiswaan', 'bk', 'pembina_ekstra', 'koordinator_keagamaan'])) {
            abort(403, 'Akun ini tidak memiliki hak akses untuk mencatat pelanggaran.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'category_id' => ['required', 'exists:violation_categories,id'],
            'item_id' => ['nullable', 'exists:violation_items,id'],
            'title' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1', 'max:200'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'chronology' => ['nullable', 'string'],
            'guidance_notes' => ['nullable', 'string'],
            'evidence_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Validasi duplikasi: Siswa tidak dapat menerima poin pelanggaran ganda di hari yang sama dengan jenis pelanggaran yang sama
        $duplicateQuery = Violation::where('student_id', $student->id)
            ->whereDate('date', $validated['date'])
            ->where('status', '!=', 'DITOLAK');

        if (! empty($validated['item_id'])) {
            $duplicateQuery->where(function ($q) use ($validated) {
                $q->where('item_id', $validated['item_id'])
                    ->orWhere(function ($sub) use ($validated) {
                        $sub->where('category_id', $validated['category_id'])
                            ->whereRaw('LOWER(TRIM(title)) = ?', [strtolower(trim($validated['title']))]);
                    });
            });
        } else {
            $duplicateQuery->where('category_id', $validated['category_id'])
                ->whereRaw('LOWER(TRIM(title)) = ?', [strtolower(trim($validated['title']))]);
        }

        $existingViolation = $duplicateQuery->first();

        if ($existingViolation) {
            $formattedDate = Carbon::parse($validated['date'])->translatedFormat('d F Y');
            $errMessage = "Pelanggaran jenis '{$existingViolation->title}' sudah pernah dicatat untuk siswa {$student->name} pada tanggal {$formattedDate}. Poin pelanggaran sejenis tidak dapat diinput ganda di hari yang sama.";

            return back()->withInput()->with('error', $errMessage)->withErrors(['title' => $errMessage]);
        }

        $evidencePath = null;

        if ($request->hasFile('evidence_file')) {
            $evidencePath = $request->file('evidence_file')->store('violations', 'public');
        }

        $initialStatus = $user->isKesiswaanAdmin() ? 'DIVERIFIKASI' : 'MENUNGGU_VERIFIKASI';

        $violation = Violation::create([
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'category_id' => $validated['category_id'],
            'item_id' => $validated['item_id'] ?? null,
            'violation_code' => 'PELANGGARAN-'.date('Y').'-'.strtoupper(Str::random(6)),
            'title' => $validated['title'],
            'points' => $validated['points'],
            'date' => $validated['date'],
            'time' => $validated['time'] ?? now()->format('H:i:s'),
            'location' => $validated['location'] ?? null,
            'chronology' => $validated['chronology'] ?? null,
            'guidance_notes' => $validated['guidance_notes'] ?? null,
            'evidence_path' => $evidencePath,
            'reporter_id' => $user->id,
            'status' => $initialStatus,
            'verified_by' => ($initialStatus === 'DIVERIFIKASI') ? $user->id : null,
            'verified_at' => ($initialStatus === 'DIVERIFIKASI') ? now() : null,
        ]);

        AuditLog::log('INPUT_PELANGGARAN', 'Violation', $violation->id, null, [
            'student' => $student->name,
            'points' => $violation->points,
            'status' => $initialStatus,
        ]);

        if ($initialStatus === 'DIVERIFIKASI') {
            $this->checkGuidanceThresholds($student, $violation);
        } else {
            $adminUsers = User::where(function ($q) {
                $q->whereIn('role', ['admin', 'super_admin'])
                    ->orWhereJsonContains('roles', 'admin')
                    ->orWhereJsonContains('roles', 'super_admin');
            })->get();
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Laporan Pelanggaran Menunggu Persetujuan',
                    'message' => "{$user->name} mencatat pelanggaran '{$violation->title}' untuk siswa {$student->name}. Menunggu persetujuan admin.",
                    'type' => 'VIOLATION',
                    'link_url' => '/violations/'.$violation->id,
                ]);
            }
        }

        $flashMessage = ($initialStatus === 'DIVERIFIKASI')
            ? 'Data pelanggaran siswa berhasil dicatat dan diverifikasi.'
            : 'Data pelanggaran siswa berhasil dicatat dan berstatus pending (menunggu verifikasi/approve Admin).';

        return redirect()->route('violations.index')->with('success', $flashMessage);
    }

    public function show(Violation $violation): View
    {
        $violation->load(['student.schoolClass', 'category', 'item', 'reporter', 'verifier', 'guidanceRecords']);

        return view('violations.show', compact('violation'));
    }

    public function verify(Request $request, Violation $violation): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isKesiswaanAdmin()) {
            abort(403, 'Hanya Admin, Super Admin, atau Wakasek Kesiswaan yang berwenang untuk menyetujui (approve) atau menolak laporan pelanggaran.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:DIVERIFIKASI,DITOLAK'],
            'rejection_reason' => ['nullable', 'string', 'required_if:status,DITOLAK'],
        ]);

        $violation->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        AuditLog::log('VERIFY_PELANGGARAN', 'Violation', $violation->id, null, [
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === 'DIVERIFIKASI') {
            $this->checkGuidanceThresholds($violation->student, $violation);
        }

        if ($violation->reporter_id && $violation->reporter_id !== $user->id) {
            Notification::create([
                'user_id' => $violation->reporter_id,
                'title' => ($validated['status'] === 'DIVERIFIKASI') ? 'Laporan Pelanggaran Disetujui' : 'Laporan Pelanggaran Ditolak',
                'message' => ($validated['status'] === 'DIVERIFIKASI')
                    ? "Laporan pelanggaran siswa {$violation->student?->name} ({$violation->title}) telah disetujui (approve) oleh Admin."
                    : "Laporan pelanggaran siswa {$violation->student?->name} ({$violation->title}) ditolak: ".($validated['rejection_reason'] ?? '-'),
                'type' => 'VIOLATION',
                'link_url' => '/violations/'.$violation->id,
            ]);
        }

        return back()->with('success', 'Verifikasi pelanggaran berhasil diperbarui.');
    }

    public function edit(Violation $violation): View
    {
        $user = Auth::user();
        if (! $user->isKesiswaanAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin, Super Admin, atau Wakasek Kesiswaan yang dapat mengubah data pelanggaran.');
        }

        $categories = ViolationCategory::with('items')->get();
        $classes = SchoolClass::all();
        $students = Student::with('schoolClass')->where('is_active', true)->get();

        return view('violations.edit', compact('violation', 'categories', 'students', 'classes'));
    }

    public function update(Request $request, Violation $violation): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isKesiswaanAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin, Super Admin, atau Wakasek Kesiswaan yang dapat mengubah data pelanggaran.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'category_id' => ['required', 'exists:violation_categories,id'],
            'item_id' => ['nullable', 'exists:violation_items,id'],
            'title' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1', 'max:200'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'chronology' => ['nullable', 'string'],
            'guidance_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:MENUNGGU_VERIFIKASI,DIVERIFIKASI,DITOLAK'],
        ]);

        $violation->update([
            'student_id' => $validated['student_id'],
            'category_id' => $validated['category_id'],
            'item_id' => $validated['item_id'] ?? null,
            'title' => $validated['title'],
            'points' => $validated['points'],
            'date' => $validated['date'],
            'time' => $validated['time'] ? $validated['time'].':00' : $violation->time,
            'location' => $validated['location'] ?? null,
            'chronology' => $validated['chronology'] ?? null,
            'guidance_notes' => $validated['guidance_notes'] ?? null,
            'status' => $validated['status'],
        ]);

        AuditLog::log('UPDATE_PELANGGARAN', 'Violation', $violation->id, null, [
            'title' => $violation->title,
            'points' => $violation->points,
            'status' => $violation->status,
        ]);

        return redirect()->route('violations.index')->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function destroy(Violation $violation): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isKesiswaanAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin, Super Admin, atau Wakasek Kesiswaan yang dapat menghapus data pelanggaran.');
        }

        $title = $violation->title;
        $violation->delete();
        AuditLog::log('DELETE_PELANGGARAN', 'Violation', $violation->id);

        return redirect()->route('violations.index')->with('success', "Data pelanggaran {$title} berhasil dihapus.");
    }

    /**
     * Memeriksa ambang pembinaan dan memicu NOTIFIKASI PERLU TINDAKAN (Bukan hukuman otomatis oleh AI)
     */
    protected function checkGuidanceThresholds(Student $student, Violation $violation): void
    {
        $totalPoints = $student->totalViolationPoints();
        $guidance = $student->guidance_status;

        if ($guidance['action_needed']) {
            $actionTitle = 'NOTIFIKASI PERLU TINDAKAN: '.$guidance['level'];
            $message = "Siswa {$student->name} ({$student->schoolClass?->name}) telah mencapai akumulasi {$totalPoints} poin pelanggaran. Diperlukan tindakan: {$guidance['badge']}.";

            // Kirim notifikasi ke BK
            $bkUsers = User::where(function ($q) {
                $q->where('role', 'bk')->orWhereJsonContains('roles', 'bk');
            })->get();
            foreach ($bkUsers as $bk) {
                Notification::create([
                    'user_id' => $bk->id,
                    'title' => $actionTitle,
                    'message' => $message,
                    'type' => 'GUIDANCE',
                    'link_url' => '/counselor/dashboard',
                ]);
            }

            // Kirim notifikasi ke Kesiswaan
            $kesiswaanUsers = User::where(function ($q) {
                $q->whereIn('role', ['kesiswaan', 'wakasek_kesiswaan'])
                    ->orWhereJsonContains('roles', 'kesiswaan')
                    ->orWhereJsonContains('roles', 'wakasek_kesiswaan');
            })->get();
            foreach ($kesiswaanUsers as $ks) {
                Notification::create([
                    'user_id' => $ks->id,
                    'title' => $actionTitle,
                    'message' => $message,
                    'type' => 'GUIDANCE',
                    'link_url' => '/kesiswaan/dashboard',
                ]);
            }

            // Kirim notifikasi ke Orang Tua
            $parent = $student->parents()->first();
            if ($parent && $parent->user_id) {
                Notification::create([
                    'user_id' => $parent->user_id,
                    'title' => 'Pemberitahuan Pembinaan Sekolah: '.$guidance['level'],
                    'message' => "Ananda {$student->name} mencapai batas poin pelanggaran ({$totalPoints} poin). Mohon berkoordinasi dengan pihak sekolah.",
                    'type' => 'GUIDANCE',
                    'link_url' => '/parent/dashboard',
                ]);
            }
        }
    }

    public function template(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_pelanggaran_siswa_sman1lengkong.csv',
            ['nis_siswa', 'kode_pelanggaran', 'judul_pelanggaran', 'poin', 'tingkat', 'tanggal', 'keterangan'],
            [
                ['12001', 'P1.1', 'Terlambat masuk sekolah lebih dari 15 menit', '5', 'RINGAN', '2026-08-18', 'Kesiangan karena hujan'],
                ['12002', 'P2.1', 'Tidak mengenakan atribut seragam lengkap', '5', 'RINGAN', '2026-08-19', 'Tidak memakai dasi'],
            ]
        );
    }

    public function import(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isKepalaSekolah() && ! $user->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $rows = $this->csvImportService->parseCsv($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $result = $this->csvImportService->importViolations($rows, Auth::id());

        return back()->with('success', "Impor Pelanggaran Berhasil: {$result['imported']} data pelanggaran siswa berhasil dicatat, {$result['skipped']} dilewati.");
    }
}

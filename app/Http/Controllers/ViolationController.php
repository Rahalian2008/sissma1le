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
        if ($user->role === 'siswa') {
            $student = $user->student ?? Student::first();
            $query->where('student_id', $student->id);
            $classes = collect();
        } elseif ($user->role === 'orang_tua') {
            $student = $user->parentProfile?->student ?? Student::first();
            $query->where('student_id', $student->id);
            $classes = collect();
        } elseif ($user->role === 'wali_kelas') {
            $class = $user->teacher?->homeroomClass;
            $classId = $class?->id;
            $classes = $class ? collect([$class]) : collect();
            if ($class) {
                $query->where('class_id', $class->id);
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
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        $violations = $query->paginate(15)->withQueryString();
        $categories = ViolationCategory::all();

        $studentContext = null;
        if (in_array($user->role, ['siswa', 'orang_tua'])) {
            $studentContext = $user->role === 'siswa' ? ($user->student ?? Student::first()) : ($user->parentProfile?->student ?? Student::first());
        }

        return view('violations.index', compact('violations', 'categories', 'classes', 'classId', 'studentContext'));
    }

    public function create(): View
    {
        $user = Auth::user();
        if (in_array($user->role, ['siswa', 'orang_tua', 'kepala_sekolah'])) {
            abort(403, 'Akun ini tidak memiliki hak akses untuk mencatat pelanggaran (Read-Only).');
        }

        $categories = ViolationCategory::with('items')->get();

        if ($user->role === 'wali_kelas') {
            $class = $user->teacher?->homeroomClass;
            $classes = $class ? collect([$class]) : collect();
            $students = $class ? $class->students()->with('schoolClass')->where('is_active', true)->get() : collect();
        } else {
            $classes = SchoolClass::all();
            $students = Student::with('schoolClass')->where('is_active', true)->get();
        }

        return view('violations.create', compact('categories', 'students', 'classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (in_array($user->role, ['siswa', 'orang_tua', 'kepala_sekolah'])) {
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

        if ($user->role === 'wali_kelas' && $user->teacher?->homeroomClass && $student->class_id !== $user->teacher->homeroomClass->id) {
            abort(403, 'Wali kelas hanya dapat menginput data untuk kelas binaannya.');
        }

        $evidencePath = null;

        if ($request->hasFile('evidence_file')) {
            $evidencePath = $request->file('evidence_file')->store('violations', 'public');
        }

        $initialStatus = in_array($user->role, ['admin', 'super_admin', 'wali_kelas']) ? 'DIVERIFIKASI' : 'MENUNGGU_VERIFIKASI';

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
        }

        return redirect()->route('violations.index')->with('success', 'Data pelanggaran siswa berhasil dicatat.');
    }

    public function show(Violation $violation): View
    {
        $violation->load(['student.schoolClass', 'category', 'item', 'reporter', 'verifier', 'guidanceRecords']);

        return view('violations.show', compact('violation'));
    }

    public function verify(Request $request, Violation $violation): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring dan tidak melakukan verifikasi.');
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

        return back()->with('success', 'Verifikasi pelanggaran berhasil diperbarui.');
    }

    public function destroy(Violation $violation): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
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
            $bkUsers = User::where('role', 'bk')->get();
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
            $kesiswaanUsers = User::where('role', 'kesiswaan')->get();
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
        if ($user->role === 'kepala_sekolah') {
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

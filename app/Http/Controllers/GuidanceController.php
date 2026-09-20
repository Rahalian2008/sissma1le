<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\GuidanceRecord;
use App\Models\Notification;
use App\Models\Student;
use App\Services\CsvImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuidanceController extends Controller
{
    public function __construct(
        protected CsvImportService $csvImportService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = GuidanceRecord::with(['student.schoolClass', 'counselor', 'violation'])
            ->orderByDesc('date');

        if ($user->role === 'wali_kelas') {
            $class = $user->teacher?->homeroomClass;
            if ($class) {
                $query->whereHas('student', fn ($sq) => $sq->where('class_id', $class->id));
            }
        } elseif ($user->role === 'siswa') {
            $student = $user->student;
            $query->where('student_id', $student?->id ?? -1);
        } elseif ($user->role === 'orang_tua') {
            $student = $user->parentProfile?->student;
            $query->where('student_id', $student?->id ?? -1);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('follow_up_type')) {
            $query->where('follow_up_type', $request->follow_up_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recommendation', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        $records = $query->paginate(15)->withQueryString();

        return view('guidance.index', compact('records'));
    }

    public function create(Request $request): View
    {
        $user = Auth::user();
        if (in_array($user->role, ['siswa', 'orang_tua', 'kepala_sekolah'])) {
            abort(403, 'Akun ini tidak memiliki hak akses untuk mencatat pembinaan (Read-Only).');
        }

        if ($user->role === 'wali_kelas') {
            $class = $user->teacher?->homeroomClass;
            $students = $class
                ? $class->students()->with(['schoolClass', 'violations'])->where('is_active', true)->get()
                : collect();
        } else {
            $students = Student::with(['schoolClass', 'violations'])->where('is_active', true)->get();
        }

        $selectedStudent = $request->has('student_id') ? Student::find($request->student_id) : null;
        $violations = $selectedStudent ? $selectedStudent->violations()->where('status', 'DIVERIFIKASI')->get() : collect();

        return view('guidance.create', compact('students', 'selectedStudent', 'violations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (in_array($user->role, ['siswa', 'orang_tua', 'kepala_sekolah'])) {
            abort(403, 'Akun ini tidak memiliki hak akses untuk mencatat pembinaan.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'violation_id' => ['nullable', 'exists:violations,id'],
            'date' => ['required', 'date'],
            'follow_up_type' => ['required', 'in:BIMBINGAN_WALI_KELAS,SP1_BK,SP2_BK,SP3_BK_KESISWAAN,RAPAT_KHUSUS,KONSELING_RUTIN'],
            'recommendation' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'agreement_letter_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        if ($user->role === 'wali_kelas' && $user->teacher?->homeroomClass && $student->class_id !== $user->teacher->homeroomClass->id) {
            abort(403, 'Wali kelas hanya dapat membina siswa di kelas binaannya.');
        }
        $letterPath = null;

        if ($request->hasFile('agreement_letter_file')) {
            $letterPath = $request->file('agreement_letter_file')->store('guidance_letters', 'public');
        }

        $record = GuidanceRecord::create([
            'student_id' => $student->id,
            'counselor_id' => Auth::id(),
            'violation_id' => $validated['violation_id'] ?? null,
            'date' => $validated['date'],
            'follow_up_type' => $validated['follow_up_type'],
            'agreement_letter_path' => $letterPath,
            'recommendation' => $validated['recommendation'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'DALAM_PROSES',
        ]);

        AuditLog::log('CREATE_GUIDANCE_RECORD', 'GuidanceRecord', $record->id, null, [
            'student' => $student->name,
            'type' => $record->follow_up_type,
        ]);

        // Notifikasi orang tua
        $parent = $student->parents()->first();
        if ($parent && $parent->user_id) {
            Notification::create([
                'user_id' => $parent->user_id,
                'title' => 'Catatan Pembinaan Siswa Baru',
                'message' => "Telah dibuat catatan pembinaan ({$record->followUpLabel()}) untuk ananda {$student->name}.",
                'type' => 'GUIDANCE',
                'link_url' => '/parent/dashboard',
            ]);
        }

        return redirect()->route('guidance.index')->with('success', 'Catatan pembinaan siswa berhasil disimpan.');
    }

    public function show(GuidanceRecord $guidance): View
    {
        $guidance->load(['student.schoolClass', 'counselor', 'violation']);

        return view('guidance.show', compact('guidance'));
    }

    public function update(Request $request, GuidanceRecord $guidance): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:DALAM_PROSES,SELESAI,PEMANTAUAN_LANJUTAN'],
            'notes' => ['nullable', 'string'],
        ]);

        $guidance->update($validated);

        AuditLog::log('UPDATE_GUIDANCE_STATUS', 'GuidanceRecord', $guidance->id, null, [
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Status pembinaan berhasil diperbarui.');
    }

    public function destroy(GuidanceRecord $guidance): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        $guidance->delete();
        AuditLog::log('DELETE_GUIDANCE_RECORD', 'GuidanceRecord', $guidance->id);

        return redirect()->route('guidance.index')->with('success', 'Catatan pembinaan berhasil dihapus.');
    }

    public function template(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_catatan_pembinaan_sman1lengkong.csv',
            ['nis_siswa', 'tanggal', 'tindakan_penanganan', 'kasus_masalah', 'catatan', 'status'],
            [
                ['12001', '2026-08-21', 'Bimbingan Konseling Individu', 'Keterlambatan masuk sekolah', 'Siswa berkomitmen hadir tepat waktu', 'DALAM_PROSES'],
                ['12002', '2026-08-22', 'Mediasi Wali Kelas & Guru BK', 'Konseling motivasi belajar', 'Orang tua telah dihubungi dan mendukung', 'SELESAI'],
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

        $result = $this->csvImportService->importGuidance($rows, Auth::id());

        return back()->with('success', "Impor Pembinaan Berhasil: {$result['imported']} catatan pembinaan siswa berhasil disimpan, {$result['skipped']} dilewati.");
    }
}

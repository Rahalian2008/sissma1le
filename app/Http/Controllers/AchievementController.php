<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\CsvImportService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AchievementController extends Controller
{
    public function __construct(
        protected CsvImportService $csvImportService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Achievement::with(['student.schoolClass', 'category', 'item', 'reporter', 'verifier'])
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

        $achievements = $query->paginate(15)->withQueryString();
        $categories = AchievementCategory::all();

        // Student's total points and reward status if student/parent
        $studentContext = null;
        if ($user->isStudent() || $user->isParent()) {
            $studentContext = $user->isStudent() ? ($user->student ?? Student::first()) : ($user->parentProfile?->student ?? Student::first());
        }

        return view('achievements.index', compact('achievements', 'categories', 'classes', 'classId', 'studentContext'));
    }

    public function create(): View
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan', 'wakasek_kesiswaan', 'bk', 'pembina_ekstra', 'koordinator_keagamaan'])) {
            abort(403, 'Akun ini tidak memiliki kewenangan untuk mencatat data prestasi (Read-Only).');
        }

        $categories = AchievementCategory::with('items')->get();

        $classes = SchoolClass::all();
        $students = Student::with('schoolClass')->where('is_active', true)->get();

        return view('achievements.create', compact('categories', 'students', 'classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan', 'wakasek_kesiswaan', 'bk', 'pembina_ekstra', 'koordinator_keagamaan'])) {
            abort(403, 'Akun ini tidak memiliki hak akses untuk menginput data prestasi.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'category_id' => ['required', 'exists:achievement_categories,id'],
            'item_id' => ['nullable', 'exists:achievement_items,id'],
            'title' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:SEKOLAH,KECAMATAN,KABUPATEN,PROVINSI,NASIONAL,INTERNASIONAL'],
            'points' => ['required', 'integer', 'min:1', 'max:500'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'certificate_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Validasi duplikasi: Siswa tidak dapat menerima poin prestasi ganda di hari yang sama dengan jenis prestasi yang sama
        $duplicateQuery = Achievement::where('student_id', $student->id)
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

        $existingAchievement = $duplicateQuery->first();

        if ($existingAchievement) {
            $formattedDate = Carbon::parse($validated['date'])->translatedFormat('d F Y');
            $errMessage = "Prestasi jenis '{$existingAchievement->title}' sudah pernah dicatat untuk siswa {$student->name} pada tanggal {$formattedDate}. Poin prestasi sejenis tidak dapat diinput ganda di hari yang sama.";

            return back()->withInput()->with('error', $errMessage)->withErrors(['title' => $errMessage]);
        }

        $certificatePath = null;

        if ($request->hasFile('certificate_file')) {
            $certificatePath = $request->file('certificate_file')->store('achievements', 'public');
        }

        $initialStatus = $user->isKesiswaanAdmin() ? 'DIVERIFIKASI' : 'MENUNGGU_VERIFIKASI';

        $achievement = Achievement::create([
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'category_id' => $validated['category_id'],
            'item_id' => $validated['item_id'] ?? null,
            'achievement_code' => 'PRESTASI-'.date('Y').'-'.strtoupper(Str::random(6)),
            'title' => $validated['title'],
            'level' => $validated['level'],
            'points' => $validated['points'],
            'date' => $validated['date'],
            'description' => $validated['description'] ?? null,
            'certificate_path' => $certificatePath,
            'reporter_id' => $user->id,
            'status' => $initialStatus,
            'verified_by' => ($initialStatus === 'DIVERIFIKASI') ? $user->id : null,
            'verified_at' => ($initialStatus === 'DIVERIFIKASI') ? now() : null,
        ]);

        AuditLog::log('INPUT_PRESTASI', 'Achievement', $achievement->id, null, [
            'student' => $student->name,
            'points' => $achievement->points,
            'status' => $initialStatus,
        ]);

        if ($initialStatus === 'DIVERIFIKASI') {
            // Notifikasi siswa
            if ($student->user_id) {
                Notification::create([
                    'user_id' => $student->user_id,
                    'title' => 'Prestasi Baru Diterima!',
                    'message' => "Prestasi '{$achievement->title}' telah terverifikasi (+{$achievement->points} Poin).",
                    'type' => 'ACHIEVEMENT',
                    'link_url' => '/achievements',
                ]);
            }
        } else {
            $adminUsers = User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Pencatatan Prestasi Menunggu Persetujuan',
                    'message' => "{$user->name} mencatat prestasi '{$achievement->title}' untuk siswa {$student->name}. Menunggu persetujuan admin.",
                    'type' => 'ACHIEVEMENT',
                    'link_url' => '/achievements/'.$achievement->id,
                ]);
            }
        }

        $flashMessage = ($initialStatus === 'DIVERIFIKASI')
            ? 'Data prestasi siswa berhasil disimpan dan diverifikasi.'
            : 'Data prestasi siswa berhasil dicatat dan berstatus pending (menunggu verifikasi/approve Admin).';

        return redirect()->route('achievements.index')->with('success', $flashMessage);
    }

    public function show(Achievement $achievement): View
    {
        $achievement->load(['student.schoolClass', 'category', 'item', 'reporter', 'verifier']);

        return view('achievements.show', compact('achievement'));
    }

    public function verify(Request $request, Achievement $achievement): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isKesiswaanAdmin()) {
            abort(403, 'Hanya Admin, Super Admin, atau Wakasek Kesiswaan yang berwenang untuk menyetujui (approve) atau menolak laporan prestasi.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:DIVERIFIKASI,DITOLAK'],
            'rejection_reason' => ['nullable', 'string', 'required_if:status,DITOLAK'],
        ]);

        $achievement->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        AuditLog::log('VERIFY_PRESTASI', 'Achievement', $achievement->id, null, [
            'status' => $validated['status'],
        ]);

        // Notifikasi ke siswa & orang tua
        $student = $achievement->student;
        if ($student && $student->user_id) {
            Notification::create([
                'user_id' => $student->user_id,
                'title' => ($validated['status'] === 'DIVERIFIKASI') ? 'Prestasi Telah Diverifikasi!' : 'Prestasi Ditolak',
                'message' => ($validated['status'] === 'DIVERIFIKASI')
                    ? "Prestasi '{$achievement->title}' diverifikasi (+{$achievement->points} Poin)."
                    : "Prestasi '{$achievement->title}' tidak disetujui: ".($validated['rejection_reason'] ?? '-'),
                'type' => 'ACHIEVEMENT',
                'link_url' => '/achievements',
            ]);
        }

        // Notifikasi ke pelapor (guru/wali kelas) jika bukan admin sendiri
        if ($achievement->reporter_id && $achievement->reporter_id !== $user->id) {
            Notification::create([
                'user_id' => $achievement->reporter_id,
                'title' => ($validated['status'] === 'DIVERIFIKASI') ? 'Laporan Prestasi Disetujui' : 'Laporan Prestasi Ditolak',
                'message' => ($validated['status'] === 'DIVERIFIKASI')
                    ? "Pencatatan prestasi siswa {$achievement->student?->name} ({$achievement->title}) telah disetujui (approve) oleh Admin."
                    : "Pencatatan prestasi siswa {$achievement->student?->name} ({$achievement->title}) ditolak: ".($validated['rejection_reason'] ?? '-'),
                'type' => 'ACHIEVEMENT',
                'link_url' => '/achievements/'.$achievement->id,
            ]);
        }

        return back()->with('success', 'Verifikasi prestasi berhasil diperbarui.');
    }

    public function edit(Achievement $achievement): View
    {
        $user = Auth::user();
        if (! $user->isKesiswaanAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin, Super Admin, atau Wakasek Kesiswaan yang dapat mengubah data prestasi.');
        }

        $categories = AchievementCategory::with('items')->get();
        $classes = SchoolClass::all();
        $students = Student::with('schoolClass')->where('is_active', true)->get();

        return view('achievements.edit', compact('achievement', 'categories', 'students', 'classes'));
    }

    public function update(Request $request, Achievement $achievement): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isKesiswaanAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin, Super Admin, atau Wakasek Kesiswaan yang dapat mengubah data prestasi.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'category_id' => ['required', 'exists:achievement_categories,id'],
            'item_id' => ['nullable', 'exists:achievement_items,id'],
            'title' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1', 'max:500'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'level' => ['required', 'string', 'in:SEKOLAH,KABUPATEN,PROVINSI,NASIONAL,INTERNASIONAL'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:MENUNGGU_VERIFIKASI,DIVERIFIKASI,DITOLAK'],
        ]);

        $achievement->update([
            'student_id' => $validated['student_id'],
            'category_id' => $validated['category_id'],
            'item_id' => $validated['item_id'] ?? null,
            'title' => $validated['title'],
            'points' => $validated['points'],
            'date' => $validated['date'],
            'time' => $validated['time'] ? $validated['time'].':00' : $achievement->time,
            'level' => $validated['level'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        AuditLog::log('UPDATE_PRESTASI', 'Achievement', $achievement->id, null, [
            'title' => $achievement->title,
            'points' => $achievement->points,
            'status' => $achievement->status,
        ]);

        return redirect()->route('achievements.index')->with('success', 'Data prestasi berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isKesiswaanAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin, Super Admin, atau Wakasek Kesiswaan yang dapat menghapus data prestasi.');
        }

        $title = $achievement->title;
        $achievement->delete();
        AuditLog::log('DELETE_PRESTASI', 'Achievement', $achievement->id);

        return redirect()->route('achievements.index')->with('success', "Data prestasi {$title} berhasil dihapus.");
    }

    public function template(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_prestasi_siswa_sman1lengkong.csv',
            ['nis_siswa', 'kode_prestasi', 'judul_prestasi', 'poin', 'tingkat', 'tanggal', 'keterangan'],
            [
                ['12001', 'R1.1', 'Juara 1 Lomba Sains Tingkat Nasional', '100', 'NASIONAL', '2026-08-17', 'Mewakili sekolah di ajang OSN'],
                ['12002', 'R2.1', 'Juara 1 Turnamen Futsal Pelajar', '40', 'KABUPATEN', '2026-08-20', 'Piala Bupati Cup'],
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

        $result = $this->csvImportService->importAchievements($rows, Auth::id());

        return back()->with('success', "Impor Prestasi Berhasil: {$result['imported']} data prestasi siswa berhasil dicatat, {$result['skipped']} dilewati.");
    }
}

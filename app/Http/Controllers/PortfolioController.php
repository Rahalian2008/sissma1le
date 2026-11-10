<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\Student;
use App\Models\StudentPortfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * Galeri & Kurasi Portofolio Digital Siswa.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isStudent = $user->isStudent() && $user->student;
        $isTeacher = $user->isTeacher() || $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah', 'wali_kelas']);

        $category = $request->query('category');
        $classId = $request->query('course_class_id');
        $search = $request->query('search');
        $featuredOnly = $request->boolean('featured');

        $query = StudentPortfolio::with(['student.schoolClass', 'courseClass.subject', 'learningObjective', 'verifier'])
            ->latest('created_at');

        // Jika siswa, tampilkan portofolio miliknya sendiri ATAU portofolio unggulan dari rekan sekelas
        if ($isStudent && ! $isTeacher) {
            $myStudentId = $user->student->id;
            $myClassId = $user->student->class_id;

            $query->where(function ($q) use ($myStudentId, $myClassId) {
                $q->where('student_id', $myStudentId)
                    ->orWhere(function ($sub) use ($myClassId) {
                        $sub->where('is_featured', true)
                            ->whereHas('student', function ($sq) use ($myClassId) {
                                $sq->where('class_id', $myClassId);
                            });
                    });
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($classId) {
            $query->where('course_class_id', $classId);
        }

        if ($featuredOnly) {
            $query->where('is_featured', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $portfolios = $query->paginate(12)->withQueryString();

        // Statistik Portofolio
        $stats = [
            'total' => StudentPortfolio::count(),
            'featured' => StudentPortfolio::where('is_featured', true)->count(),
            'verified' => StudentPortfolio::whereNotNull('verified_by')->count(),
            'proyek_p5' => StudentPortfolio::where('category', 'Proyek P5')->count(),
        ];

        $categories = [
            'Tugas Terbaik',
            'Proyek P5',
            'Prestasi Belajar',
            'Karya Tulis',
            'Video Pembelajaran',
            'Karya Seni & Desain',
            'Praktikum & Eksperimen',
            'Lain-lain',
        ];

        $courseClasses = CourseClass::with(['subject', 'schoolClass'])
            ->where('is_active', true)
            ->get();

        $students = collect();
        if ($isTeacher) {
            $students = Student::where('is_active', true)->orderBy('name')->take(200)->get();
        }

        $objectives = LearningObjective::orderBy('tp_code')->get();

        return view('lms.portfolios.index', compact(
            'portfolios',
            'categories',
            'courseClasses',
            'students',
            'objectives',
            'stats',
            'category',
            'classId',
            'search',
            'featuredOnly',
            'isStudent',
            'isTeacher'
        ));
    }

    /**
     * Simpan portofolio / karya belajar baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => 'required|string|max:50',
            'course_class_id' => 'nullable|exists:course_classes,id',
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'external_url' => 'nullable|url|max:500',
            'artifact_file' => 'nullable|file|max:20480', // Maks 20MB
            'artifact_date' => 'nullable|date',
            'student_id' => 'nullable|exists:students,id',
            'is_featured' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $studentId = null;

        if ($user->isStudent() && $user->student) {
            $studentId = $user->student->id;
        } elseif (! empty($validated['student_id'])) {
            if (! $user->isTeacher() && ! $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah'])) {
                abort(403, 'Hanya pendidik yang dapat mengunggah portofolio untuk siswa lain.');
            }
            $studentId = $validated['student_id'];
        } else {
            return redirect()->back()->withErrors(['student_id' => 'Identitas siswa pemilik karya wajib disertakan.']);
        }

        $filePath = null;
        if ($request->hasFile('artifact_file')) {
            $filePath = $request->file('artifact_file')->store('portfolios', 'public');
        }

        StudentPortfolio::create([
            'student_id' => $studentId,
            'course_class_id' => $validated['course_class_id'] ?? null,
            'learning_objective_id' => $validated['learning_objective_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'file_path' => $filePath,
            'external_url' => $validated['external_url'] ?? null,
            'artifact_date' => $validated['artifact_date'] ?? now()->toDateString(),
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'verified_by' => (! $user->isStudent() && $user->isTeacher()) ? $user->id : null,
        ]);

        return redirect()->back()->with('success', 'Portofolio karya belajar berhasil ditambahkan ke etalase digital.');
    }

    /**
     * Kurasi dan verifikasi portofolio karya siswa oleh Guru / Pimpinan.
     */
    public function verify(Request $request, StudentPortfolio $portfolio): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isTeacher() && ! $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah', 'wali_kelas'])) {
            abort(403, 'Hanya pendidik yang berhak melakukan verifikasi portofolio karya.');
        }

        $toggleFeatured = $request->has('toggle_featured') ? ! $portfolio->is_featured : $portfolio->is_featured;

        $portfolio->update([
            'verified_by' => $user->id,
            'is_featured' => $toggleFeatured,
        ]);

        $statusText = $portfolio->is_featured ? 'telah disematkan sebagai Karya Unggulan' : 'telah diverifikasi pendidik';

        return redirect()->back()->with('success', "Portofolio '{$portfolio->title}' {$statusText}.");
    }

    /**
     * Hapus portofolio karya.
     */
    public function destroy(StudentPortfolio $portfolio): RedirectResponse
    {
        $user = Auth::user();
        $canDelete = false;

        if ($user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah'])) {
            $canDelete = true;
        } elseif ($user->student && $user->student->id === $portfolio->student_id && ! $portfolio->verified_by) {
            $canDelete = true;
        } elseif ($user->teacher && $portfolio->courseClass && $portfolio->courseClass->teacher_id === $user->teacher->id) {
            $canDelete = true;
        }

        if (! $canDelete) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus portofolio ini.');
        }

        if ($portfolio->file_path && Storage::disk('public')->exists($portfolio->file_path)) {
            Storage::disk('public')->delete($portfolio->file_path);
        }

        $portfolio->delete();

        return redirect()->back()->with('success', 'Portofolio karya berhasil dihapus.');
    }

    /**
     * Cetak Lembar Kurasi & Sertifikat Portofolio Karya Siswa (A4).
     */
    public function print(StudentPortfolio $portfolio): View
    {
        $user = Auth::user();
        if ($user->isStudent() && $user->student && $user->student->id !== $portfolio->student_id && ! $portfolio->is_featured) {
            abort(403, 'Anda tidak berhak melihat portofolio siswa lain.');
        }

        $portfolio->load(['student.schoolClass', 'courseClass.subject', 'learningObjective', 'verifier']);

        return view('lms.portfolios.print', compact('portfolio'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BkTracerAlumnus;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\AuditTrailService;
use App\Services\HigherEducationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkTracerAlumniController extends Controller
{
    /**
     * Tampilan Formulir Online Publik Kuesioner Tracer Alumni
     */
    public function publicForm(): View
    {
        $school = SchoolIdentity::first();
        $universities = HigherEducationService::getJavaUniversities();
        $standardMajors = HigherEducationService::getAllStandardMajors();

        $currentYear = (int) date('Y');
        $graduationYears = range($currentYear, $currentYear - 10);

        return view('bk.tracer.public_form', compact('school', 'universities', 'standardMajors', 'graduationYears'));
    }

    /**
     * Simpan Kuesioner Tracer Alumni dari Form Publik
     */
    public function publicStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'nisn' => ['nullable', 'string', 'max:30'],
            'nis' => ['nullable', 'string', 'max:30'],
            'gender' => ['required', 'in:L,P'],
            'graduation_year' => ['required', 'string', 'max:10'],
            'last_class' => ['nullable', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'current_address' => ['nullable', 'string'],
            'current_city' => ['nullable', 'string', 'max:100'],

            'activity_status' => ['required', 'in:KULIAH,BEKERJA,WIRAUSAHA,KURSUS_PELATIHAN,MENCARI_KERJA'],

            // Kuliah
            'college_level' => ['nullable', 'string', 'max:30'],
            'campus_name' => ['nullable', 'string', 'max:191'],
            'study_program' => ['nullable', 'string', 'max:191'],
            'admission_track' => ['nullable', 'string', 'max:50'],
            'major_alignment' => ['nullable', 'string', 'max:50'],

            // Bekerja
            'company_name' => ['nullable', 'string', 'max:191'],
            'work_field' => ['nullable', 'string', 'max:100'],
            'job_position' => ['nullable', 'string', 'max:100'],
            'monthly_income_range' => ['nullable', 'string', 'max:50'],
            'job_waiting_time' => ['nullable', 'string', 'max:50'],

            // Wirausaha
            'business_name' => ['nullable', 'string', 'max:191'],
            'business_field' => ['nullable', 'string', 'max:100'],
            'business_revenue_range' => ['nullable', 'string', 'max:50'],
            'employee_count' => ['nullable', 'integer', 'min:0'],

            // Evaluasi BK
            'bk_service_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'bk_career_guidance_impact' => ['nullable', 'string'],
            'school_feedback' => ['nullable', 'string'],
        ]);

        // Coba tautkan dengan student_id jika NISN atau Nama cocok
        $matchedStudent = null;
        if (! empty($validated['nisn'])) {
            $matchedStudent = Student::where('nisn', $validated['nisn'])->first();
        }
        if (! $matchedStudent && ! empty($validated['full_name'])) {
            $matchedStudent = Student::where('name', 'LIKE', $validated['full_name'])->first();
        }

        $tracer = BkTracerAlumnus::create([
            ...$validated,
            'student_id' => $matchedStudent?->id,
            'is_verified' => true,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('bk.tracer.public_success')
            ->with('tracer_id', $tracer->id)
            ->with('alumni_name', $tracer->full_name);
    }

    /**
     * Halaman Sukses Kuesioner Tracer Alumni
     */
    public function publicSuccess(): View
    {
        $school = SchoolIdentity::first();
        $tracerId = session('tracer_id');
        $tracer = $tracerId ? BkTracerAlumnus::find($tracerId) : null;

        return view('bk.tracer.public_success', compact('school', 'tracer'));
    }

    /**
     * Dashboard Data Tracer Alumni (Guru BK / Manajemen Sekolah)
     */
    public function index(Request $request): View
    {
        $query = BkTracerAlumnus::query()->latest();

        if ($year = $request->query('graduation_year')) {
            $query->where('graduation_year', $year);
        }

        if ($status = $request->query('activity_status')) {
            $query->where('activity_status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('nisn', 'LIKE', "%{$search}%")
                    ->orWhere('campus_name', 'LIKE', "%{$search}%")
                    ->orWhere('company_name', 'LIKE', "%{$search}%")
                    ->orWhere('business_name', 'LIKE', "%{$search}%")
                    ->orWhere('study_program', 'LIKE', "%{$search}%");
            });
        }

        // Statistik Keseluruhan / Filter
        $totalAlumni = BkTracerAlumnus::count();
        $collegeCount = BkTracerAlumnus::where('activity_status', 'KULIAH')->count();
        $workCount = BkTracerAlumnus::where('activity_status', 'BEKERJA')->count();
        $businessCount = BkTracerAlumnus::where('activity_status', 'WIRAUSAHA')->count();
        $trainingCount = BkTracerAlumnus::where('activity_status', 'KURSUS_PELATIHAN')->count();
        $seekingCount = BkTracerAlumnus::where('activity_status', 'MENCARI_KERJA')->count();

        $alumni = $query->paginate(20)->withQueryString();

        $graduationYears = BkTracerAlumnus::select('graduation_year')
            ->distinct()
            ->orderByDesc('graduation_year')
            ->pluck('graduation_year')
            ->toArray();

        if (empty($graduationYears)) {
            $graduationYears = range((int) date('Y'), (int) date('Y') - 5);
        }

        return view('bk.tracer.index', compact(
            'alumni',
            'totalAlumni',
            'collegeCount',
            'workCount',
            'businessCount',
            'trainingCount',
            'seekingCount',
            'graduationYears'
        ));
    }

    /**
     * Detail Data Tracer Alumni
     */
    public function show(BkTracerAlumnus $tracer): View
    {
        $tracer->load('student');

        return view('bk.tracer.show', compact('tracer'));
    }

    /**
     * Edit Data Tracer Alumni
     */
    public function edit(BkTracerAlumnus $tracer): View
    {
        $universities = HigherEducationService::getJavaUniversities();
        $standardMajors = HigherEducationService::getAllStandardMajors();

        return view('bk.tracer.edit', compact('tracer', 'universities', 'standardMajors'));
    }

    /**
     * Update Data Tracer Alumni
     */
    public function update(Request $request, BkTracerAlumnus $tracer): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'nisn' => ['nullable', 'string', 'max:30'],
            'nis' => ['nullable', 'string', 'max:30'],
            'gender' => ['required', 'in:L,P'],
            'graduation_year' => ['required', 'string', 'max:10'],
            'last_class' => ['nullable', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'current_address' => ['nullable', 'string'],
            'current_city' => ['nullable', 'string', 'max:100'],
            'activity_status' => ['required', 'in:KULIAH,BEKERJA,WIRAUSAHA,KURSUS_PELATIHAN,MENCARI_KERJA'],
            'college_level' => ['nullable', 'string', 'max:30'],
            'campus_name' => ['nullable', 'string', 'max:191'],
            'study_program' => ['nullable', 'string', 'max:191'],
            'admission_track' => ['nullable', 'string', 'max:50'],
            'major_alignment' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:191'],
            'work_field' => ['nullable', 'string', 'max:100'],
            'job_position' => ['nullable', 'string', 'max:100'],
            'monthly_income_range' => ['nullable', 'string', 'max:50'],
            'job_waiting_time' => ['nullable', 'string', 'max:50'],
            'business_name' => ['nullable', 'string', 'max:191'],
            'business_field' => ['nullable', 'string', 'max:100'],
            'business_revenue_range' => ['nullable', 'string', 'max:50'],
            'employee_count' => ['nullable', 'integer', 'min:0'],
            'bk_service_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'bk_career_guidance_impact' => ['nullable', 'string'],
            'school_feedback' => ['nullable', 'string'],
        ]);

        $tracer->update($validated);

        if (Auth::check()) {
            AuditTrailService::log('update', 'bk_tracer_alumni', "Memperbarui data tracer alumni {$tracer->full_name} (Lulus: {$tracer->graduation_year})");
        }

        return redirect()->route('bk.tracer.index')->with('success', "Data tracer alumni {$tracer->full_name} berhasil diperbarui.");
    }

    /**
     * Hapus Data Tracer Alumni
     */
    public function destroy(BkTracerAlumnus $tracer): RedirectResponse
    {
        $name = $tracer->full_name;
        $tracer->delete();

        if (Auth::check()) {
            AuditTrailService::log('delete', 'bk_tracer_alumni', "Menghapus data tracer alumni {$name}");
        }

        return redirect()->route('bk.tracer.index')->with('success', "Data tracer alumni {$name} berhasil dihapus.");
    }

    /**
     * Cetak Laporan Resmi Tracer Study Alumni (Format Standar Akreditasi & Kedinasan)
     */
    public function printReport(Request $request): View
    {
        $query = BkTracerAlumnus::query()->orderBy('graduation_year', 'desc')->orderBy('full_name', 'asc');

        if ($year = $request->query('graduation_year')) {
            $query->where('graduation_year', $year);
        }

        if ($status = $request->query('activity_status')) {
            $query->where('activity_status', $status);
        }

        $alumni = $query->get();
        $school = SchoolIdentity::first();
        $headmaster = Teacher::getHeadmaster();
        $bkCoordinator = Teacher::getBkCoordinator();

        // Statistik Khusus untuk Filter Cetak
        $total = $alumni->count();
        $kuliahCount = $alumni->where('activity_status', 'KULIAH')->count();
        $kerjaCount = $alumni->where('activity_status', 'BEKERJA')->count();
        $wirausahaCount = $alumni->where('activity_status', 'WIRAUSAHA')->count();
        $kursusCount = $alumni->where('activity_status', 'KURSUS_PELATIHAN')->count();
        $mencariCount = $alumni->where('activity_status', 'MENCARI_KERJA')->count();

        $filterYear = $request->query('graduation_year');
        $filterStatus = $request->query('activity_status');

        return view('bk.tracer.print', compact(
            'alumni',
            'school',
            'headmaster',
            'bkCoordinator',
            'total',
            'kuliahCount',
            'kerjaCount',
            'wirausahaCount',
            'kursusCount',
            'mencariCount',
            'filterYear',
            'filterStatus'
        ));
    }
}

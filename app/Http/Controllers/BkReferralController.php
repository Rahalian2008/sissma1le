<?php

namespace App\Http\Controllers;

use App\Models\BkReferral;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkReferralController extends Controller
{
    public function index(Request $request): View
    {
        $query = BkReferral::with(['student.schoolClass', 'counselor'])->latest('referral_date');

        if ($request->filled('category')) {
            $query->where('expert_category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('target_expert_name', 'like', "%{$search}%")
                    ->orWhere('target_institution', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
            });
        }

        $referrals = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => BkReferral::count(),
            'sedang_dirujuk' => BkReferral::where('status', 'SEDANG_DIRUJUK')->count(),
            'selesai' => BkReferral::where('status', 'SELESAI')->count(),
            'psikolog' => BkReferral::where('expert_category', 'PSIKOLOG_KLINIS')->count(),
            'psikiater' => BkReferral::where('expert_category', 'PSIKIATER')->count(),
        ];

        return view('bk.referral.index', compact('referrals', 'stats'));
    }

    public function create(): View
    {
        $students = Student::active()->with('schoolClass')->orderBy('name')->get();
        if ($students->isEmpty()) {
            $students = Student::with('schoolClass')->take(50)->get();
        }

        return view('bk.referral.create', compact('students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'referral_date' => ['required', 'date'],
            'target_expert_name' => ['required', 'string', 'max:255'],
            'target_institution' => ['required', 'string', 'max:255'],
            'expert_category' => ['required', 'in:PSIKOLOG_KLINIS,PSIKIATER,DOKTER_SPESIALIS,DINAS_SOSIAL_PPA,KEPOLISIAN_HUKUM,LAINNYA'],
            'problem_description' => ['required', 'string'],
            'previous_interventions' => ['required', 'string'],
            'referral_reason' => ['required', 'string'],
            'expected_support' => ['required', 'string'],
            'headmaster_approval' => ['required', 'in:MENUNGGU,DISETUJUI,DITOLAK'],
            'status' => ['required', 'in:SURAT_DIBUAT,SEDANG_DIRUJUK,KEMBALI_KE_SEKOLAH,SELESAI'],
        ]);

        $year = date('Y');
        $count = BkReferral::whereYear('created_at', $year)->count() + 1;
        $validated['referral_code'] = sprintf('REF-BK-%s-%03d', $year, $count);
        $validated['counselor_id'] = Auth::id();

        $referral = BkReferral::create($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('create', 'rujukan_bk', "Mencatat Alih Tangan Kasus siswa {$student?->name} ke {$referral->target_expert_name} ({$referral->referral_code})");

        return redirect()->route('bk.referral.show', $referral)->with('success', "Surat Pengantar Alih Tangan Kasus untuk {$student?->name} berhasil dibuat.");
    }

    public function show(BkReferral $referral): View
    {
        $school = SchoolIdentity::current();
        $referral->load(['student.schoolClass', 'counselor']);

        return view('bk.referral.show', compact('referral', 'school'));
    }

    public function edit(BkReferral $referral): View
    {
        $referral->load(['student.schoolClass']);
        $students = Student::active()->with('schoolClass')->orderBy('name')->get();

        return view('bk.referral.edit', compact('referral', 'students'));
    }

    public function update(Request $request, BkReferral $referral): RedirectResponse
    {
        $validated = $request->validate([
            'referral_date' => ['required', 'date'],
            'target_expert_name' => ['required', 'string', 'max:255'],
            'target_institution' => ['required', 'string', 'max:255'],
            'expert_category' => ['required', 'in:PSIKOLOG_KLINIS,PSIKIATER,DOKTER_SPESIALIS,DINAS_SOSIAL_PPA,KEPOLISIAN_HUKUM,LAINNYA'],
            'problem_description' => ['required', 'string'],
            'previous_interventions' => ['required', 'string'],
            'referral_reason' => ['required', 'string'],
            'expected_support' => ['required', 'string'],
            'expert_feedback_notes' => ['nullable', 'string'],
            'headmaster_approval' => ['required', 'in:MENUNGGU,DISETUJUI,DITOLAK'],
            'status' => ['required', 'in:SURAT_DIBUAT,SEDANG_DIRUJUK,KEMBALI_KE_SEKOLAH,SELESAI'],
        ]);

        $referral->update($validated);

        AuditTrailService::log('update', 'rujukan_bk', "Memperbarui Alih Tangan Kasus ID #{$referral->id}");

        return redirect()->route('bk.referral.show', $referral)->with('success', 'Data Alih Tangan Kasus berhasil diperbarui.');
    }

    public function destroy(BkReferral $referral): RedirectResponse
    {
        $code = $referral->referral_code;
        $referral->delete();

        AuditTrailService::log('delete', 'rujukan_bk', "Menghapus Alih Tangan Kasus {$code}");

        return redirect()->route('bk.referral.index')->with('success', 'Data Alih Tangan Kasus berhasil dihapus.');
    }

    public function print(BkReferral $referral): View
    {
        $school = SchoolIdentity::current();
        $referral->load(['student.schoolClass', 'counselor']);

        AuditTrailService::log('print', 'rujukan_bk', "Mencetak Surat Pengantar Alih Tangan Kasus: {$referral->referral_code}");

        return view('bk.referral.print', compact('referral', 'school'));
    }
}

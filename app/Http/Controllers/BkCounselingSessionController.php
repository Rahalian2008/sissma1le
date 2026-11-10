<?php

namespace App\Http\Controllers;

use App\Models\BkCounselingSession;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BkCounselingSessionController extends Controller
{
    public function index(Request $request): View
    {
        $query = BkCounselingSession::with(['student.schoolClass', 'counselor'])
            ->latest('date');

        if ($request->filled('field_of_service')) {
            $query->where('field_of_service', $request->field_of_service);
        }

        if ($request->filled('session_type')) {
            $query->where('session_type', $request->session_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                    ->orWhere('session_code', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
            });
        }

        $sessions = $query->paginate(15)->withQueryString();

        return view('bk.counseling.index', compact('sessions'));
    }

    public function create(Request $request): View
    {
        $selectedStudent = $request->filled('student_id') ? Student::with('schoolClass')->find($request->student_id) : null;
        $classes = SchoolClass::with('students')->orderBy('name')->get();
        $students = Student::where('is_active', true)->orderBy('name')->get();

        return view('bk.counseling.create', compact('selectedStudent', 'classes', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'session_type' => ['required', 'in:INDIVIDUAL,KELOMPOK,KLASIKAL,MEDIASI,KONFERENSI_KASUS,KOLABORASI,REFERRAL'],
            'field_of_service' => ['required', 'in:PRIBADI,SOSIAL,BELAJAR,KARIER'],
            'service_category' => ['required', 'in:LAYANAN_DASAR,LAYANAN_RESPONSIF,PERENCANAAN_INDIVIDUAL,DUKUNGAN_SISTEM'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'place' => ['nullable', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
            'problem_description' => ['required', 'string'],
            'counseling_approach' => ['nullable', 'string', 'max:255'],
            'agreement_result' => ['nullable', 'string'],
            'follow_up_plan' => ['nullable', 'string'],
            'status' => ['required', 'in:TERJADWAL,SEDANG_BERLANGSUNG,SELESAI,DIRUJUK'],
            'is_confidential' => ['nullable', 'boolean'],
        ]);

        $sessionCode = 'BK-'.date('Ym').'-'.strtoupper(Str::random(5));
        while (BkCounselingSession::where('session_code', $sessionCode)->exists()) {
            $sessionCode = 'BK-'.date('Ym').'-'.strtoupper(Str::random(5));
        }

        $session = BkCounselingSession::create([
            ...$validated,
            'session_code' => $sessionCode,
            'counselor_id' => Auth::id(),
            'place' => (! empty($validated['place'])) ? $validated['place'] : 'Ruang Bimbingan Konseling',
            'is_confidential' => $request->boolean('is_confidential', true),
        ]);

        return redirect()->route('bk.counseling.show', $session)
            ->with('success', "Sesi Bimbingan & Konseling [{$session->session_code}] berhasil dicatat.");
    }

    public function show(BkCounselingSession $session): View
    {
        $session->load(['student.schoolClass', 'counselor']);

        return view('bk.counseling.show', compact('session'));
    }

    public function edit(BkCounselingSession $session): View
    {
        $session->load(['student.schoolClass']);
        $students = Student::where('is_active', true)->orderBy('name')->get();

        return view('bk.counseling.edit', compact('session', 'students'));
    }

    public function update(Request $request, BkCounselingSession $session): RedirectResponse
    {
        $validated = $request->validate([
            'session_type' => ['required', 'in:INDIVIDUAL,KELOMPOK,KLASIKAL,MEDIASI,KONFERENSI_KASUS,KOLABORASI,REFERRAL'],
            'field_of_service' => ['required', 'in:PRIBADI,SOSIAL,BELAJAR,KARIER'],
            'service_category' => ['required', 'in:LAYANAN_DASAR,LAYANAN_RESPONSIF,PERENCANAAN_INDIVIDUAL,DUKUNGAN_SISTEM'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'place' => ['nullable', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
            'problem_description' => ['required', 'string'],
            'counseling_approach' => ['nullable', 'string', 'max:255'],
            'agreement_result' => ['nullable', 'string'],
            'follow_up_plan' => ['nullable', 'string'],
            'status' => ['required', 'in:TERJADWAL,SEDANG_BERLANGSUNG,SELESAI,DIRUJUK'],
            'is_confidential' => ['nullable', 'boolean'],
        ]);

        $session->update([
            ...$validated,
            'is_confidential' => $request->boolean('is_confidential', true),
        ]);

        return redirect()->route('bk.counseling.show', $session)
            ->with('success', 'Catatan sesi bimbingan konseling berhasil diperbarui.');
    }

    public function destroy(BkCounselingSession $session): RedirectResponse
    {
        $session->delete();

        return redirect()->route('bk.counseling.index')
            ->with('success', 'Catatan sesi konseling berhasil dihapus.');
    }

    public function printOfficial(BkCounselingSession $session): View
    {
        $session->load(['student.schoolClass', 'counselor']);
        $school = SchoolIdentity::first();

        return view('bk.counseling.print', compact('session', 'school'));
    }
}

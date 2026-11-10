<?php

namespace App\Http\Controllers;

use App\Models\BkHomeVisit;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BkHomeVisitController extends Controller
{
    public function index(Request $request): View
    {
        $query = BkHomeVisit::with(['student.schoolClass', 'counselor', 'homeroom'])
            ->latest('visit_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('visit_code', 'like', "%{$search}%")
                    ->orWhere('parent_name', 'like', "%{$search}%")
                    ->orWhere('letter_number', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
            });
        }

        $visits = $query->paginate(15)->withQueryString();

        return view('bk.home_visit.index', compact('visits'));
    }

    public function create(Request $request): View
    {
        $selectedStudent = $request->filled('student_id') ? Student::with('schoolClass')->find($request->student_id) : null;
        $students = Student::where('is_active', true)->orderBy('name')->get();
        $homerooms = User::where('role', 'wali_kelas')->orWhereJsonContains('roles', 'wali_kelas')->get();

        return view('bk.home_visit.create', compact('selectedStudent', 'students', 'homerooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'homeroom_id' => ['nullable', 'exists:users,id'],
            'letter_number' => ['nullable', 'string', 'max:100'],
            'visit_date' => ['required', 'date'],
            'address_visited' => ['nullable', 'string'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'case_background' => ['required', 'string'],
            'family_condition' => ['nullable', 'string'],
            'parent_commitment' => ['nullable', 'string'],
            'visit_result' => ['nullable', 'string'],
            'status' => ['required', 'in:RENCANA,TERLAKSANA,BATAL'],
        ]);

        $visitCode = 'HV-'.date('Ym').'-'.strtoupper(Str::random(5));
        while (BkHomeVisit::where('visit_code', $visitCode)->exists()) {
            $visitCode = 'HV-'.date('Ym').'-'.strtoupper(Str::random(5));
        }

        $visit = BkHomeVisit::create([
            ...$validated,
            'visit_code' => $visitCode,
            'counselor_id' => Auth::id(),
        ]);

        return redirect()->route('bk.home-visit.show', $visit)
            ->with('success', "Data Kunjungan Rumah [{$visit->visit_code}] berhasil disimpan.");
    }

    public function show(BkHomeVisit $visit): View
    {
        $visit->load(['student.schoolClass', 'counselor', 'homeroom']);

        return view('bk.home_visit.show', compact('visit'));
    }

    public function edit(BkHomeVisit $visit): View
    {
        $visit->load(['student.schoolClass']);
        $students = Student::where('is_active', true)->orderBy('name')->get();
        $homerooms = User::where('role', 'wali_kelas')->orWhereJsonContains('roles', 'wali_kelas')->get();

        return view('bk.home_visit.edit', compact('visit', 'students', 'homerooms'));
    }

    public function update(Request $request, BkHomeVisit $visit): RedirectResponse
    {
        $validated = $request->validate([
            'homeroom_id' => ['nullable', 'exists:users,id'],
            'letter_number' => ['nullable', 'string', 'max:100'],
            'visit_date' => ['required', 'date'],
            'address_visited' => ['nullable', 'string'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'case_background' => ['required', 'string'],
            'family_condition' => ['nullable', 'string'],
            'parent_commitment' => ['nullable', 'string'],
            'visit_result' => ['nullable', 'string'],
            'status' => ['required', 'in:RENCANA,TERLAKSANA,BATAL'],
        ]);

        $visit->update($validated);

        return redirect()->route('bk.home-visit.show', $visit)
            ->with('success', 'Data Kunjungan Rumah berhasil diperbarui.');
    }

    public function destroy(BkHomeVisit $visit): RedirectResponse
    {
        $visit->delete();

        return redirect()->route('bk.home-visit.index')
            ->with('success', 'Data kunjungan rumah berhasil dihapus.');
    }

    public function printOfficial(BkHomeVisit $visit): View
    {
        $visit->load(['student.schoolClass', 'counselor', 'homeroom']);
        $school = SchoolIdentity::first();

        return view('bk.home_visit.print', compact('visit', 'school'));
    }
}

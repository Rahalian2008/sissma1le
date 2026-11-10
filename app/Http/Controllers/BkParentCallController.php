<?php

namespace App\Http\Controllers;

use App\Models\BkParentCall;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BkParentCallController extends Controller
{
    public function index(Request $request): View
    {
        $query = BkParentCall::with(['student.schoolClass', 'counselor'])
            ->latest('call_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('call_code', 'like', "%{$search}%")
                    ->orWhere('letter_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
            });
        }

        $calls = $query->paginate(15)->withQueryString();

        return view('bk.parent_call.index', compact('calls'));
    }

    public function create(Request $request): View
    {
        $selectedStudent = $request->filled('student_id') ? Student::with(['schoolClass', 'violations'])->find($request->student_id) : null;
        $students = Student::where('is_active', true)->orderBy('name')->get();

        // Rekomendasi nomor surat otomatis
        $year = date('Y');
        $monthRom = match ((int) date('m')) {
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        };
        $nextNumber = BkParentCall::whereYear('created_at', $year)->count() + 1;
        $suggestedNumber = sprintf('421.3/%03d-BK/SMA1LE/%s/%d', $nextNumber, $monthRom, $year);

        return view('bk.parent_call.create', compact('selectedStudent', 'students', 'suggestedNumber'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'letter_number' => ['nullable', 'string', 'max:100'],
            'call_date' => ['required', 'date'],
            'call_time' => ['nullable', 'string', 'max:100'],
            'place' => ['nullable', 'string', 'max:255'],
            'reason' => ['required', 'string'],
            'status' => ['required', 'in:SURAT_DIKIRIM,HADIR,TIDAK_HADIR,SELESAI'],
            'meeting_notes' => ['nullable', 'string'],
            'parent_agreement' => ['nullable', 'string'],
        ]);

        $callCode = 'PGL-'.date('Ym').'-'.strtoupper(Str::random(5));
        while (BkParentCall::where('call_code', $callCode)->exists()) {
            $callCode = 'PGL-'.date('Ym').'-'.strtoupper(Str::random(5));
        }

        $call = BkParentCall::create([
            ...$validated,
            'call_code' => $callCode,
            'counselor_id' => Auth::id(),
            'place' => $validated['place'] ?: 'Ruang Bimbingan Konseling SMA Negeri 1 Leles',
        ]);

        return redirect()->route('bk.parent-call.show', $call)
            ->with('success', "Surat Pemanggilan Orang Tua [{$call->call_code}] berhasil dibuat.");
    }

    public function show(BkParentCall $parentCall): View
    {
        $parentCall->load(['student.schoolClass', 'counselor']);

        return view('bk.parent_call.show', compact('parentCall'));
    }

    public function edit(BkParentCall $parentCall): View
    {
        $parentCall->load(['student.schoolClass']);
        $students = Student::where('is_active', true)->orderBy('name')->get();

        return view('bk.parent_call.edit', compact('parentCall', 'students'));
    }

    public function update(Request $request, BkParentCall $parentCall): RedirectResponse
    {
        $validated = $request->validate([
            'letter_number' => ['nullable', 'string', 'max:100'],
            'call_date' => ['required', 'date'],
            'call_time' => ['nullable', 'string', 'max:100'],
            'place' => ['nullable', 'string', 'max:255'],
            'reason' => ['required', 'string'],
            'status' => ['required', 'in:SURAT_DIKIRIM,HADIR,TIDAK_HADIR,SELESAI'],
            'meeting_notes' => ['nullable', 'string'],
            'parent_agreement' => ['nullable', 'string'],
        ]);

        $parentCall->update($validated);

        return redirect()->route('bk.parent-call.show', $parentCall)
            ->with('success', 'Data Pemanggilan Orang Tua berhasil diperbarui.');
    }

    public function destroy(BkParentCall $parentCall): RedirectResponse
    {
        $parentCall->delete();

        return redirect()->route('bk.parent-call.index')
            ->with('success', 'Data pemanggilan orang tua berhasil dihapus.');
    }

    public function printLetter(BkParentCall $parentCall): View
    {
        $parentCall->load(['student.schoolClass', 'counselor']);
        $school = SchoolIdentity::first();
        $headmaster = Teacher::getHeadmaster();
        $wakasekKesiswaan = Teacher::getWakasekKesiswaan();

        return view('bk.parent_call.print_letter', compact('parentCall', 'school', 'headmaster', 'wakasekKesiswaan'));
    }

    public function printBeritaAcara(BkParentCall $parentCall): View
    {
        $parentCall->load(['student.schoolClass', 'counselor']);
        $school = SchoolIdentity::first();

        return view('bk.parent_call.print_berita_acara', compact('parentCall', 'school'));
    }
}

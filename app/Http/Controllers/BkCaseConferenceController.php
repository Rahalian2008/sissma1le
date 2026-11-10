<?php

namespace App\Http\Controllers;

use App\Models\BkCaseConference;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkCaseConferenceController extends Controller
{
    public function index(Request $request): View
    {
        $query = BkCaseConference::with(['student.schoolClass', 'counselor'])->latest('conference_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_title', 'like', "%{$search}%")
                    ->orWhere('conference_code', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
            });
        }

        $conferences = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => BkCaseConference::count(),
            'selesai' => BkCaseConference::where('status', 'SELESAI')->count(),
            'terlaksana' => BkCaseConference::where('status', 'TERLAKSANA')->count(),
            'terjadwal' => BkCaseConference::where('status', 'TERJADWAL')->count(),
        ];

        return view('bk.case_conference.index', compact('conferences', 'stats'));
    }

    public function create(): View
    {
        $students = Student::active()->with('schoolClass')->orderBy('name')->get();
        if ($students->isEmpty()) {
            $students = Student::with('schoolClass')->take(50)->get();
        }

        return view('bk.case_conference.create', compact('students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'case_title' => ['required', 'string', 'max:255'],
            'conference_date' => ['required', 'date'],
            'conference_time' => ['nullable', 'string', 'max:30'],
            'place' => ['required', 'string', 'max:255'],
            'leader_name' => ['required', 'string', 'max:255'],
            'case_background' => ['required', 'string'],
            'facts_and_findings' => ['required', 'string'],
            'discussion_dynamics' => ['nullable', 'string'],
            'agreed_decisions' => ['required', 'string'],
            'follow_up_commitments' => ['required', 'string'],
            'status' => ['required', 'in:TERJADWAL,TERLAKSANA,DITINDAKLANJUTI,SELESAI'],
            'attendees_input' => ['nullable', 'string'],
        ]);

        $year = date('Y');
        $count = BkCaseConference::whereYear('created_at', $year)->count() + 1;
        $validated['conference_code'] = sprintf('KK-BK-%s-%03d', $year, $count);
        $validated['counselor_id'] = Auth::id();

        // Parse attendees
        $attendees = [];
        if (! empty($validated['attendees_input'])) {
            $lines = explode("\n", $validated['attendees_input']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (! empty($trimmed)) {
                    $parts = array_map('trim', explode('|', $trimmed));
                    $attendees[] = [
                        'name' => $parts[0] ?? $trimmed,
                        'role' => $parts[1] ?? 'Peserta Rapat',
                        'notes' => $parts[2] ?? '-',
                    ];
                }
            }
        }

        if (empty($attendees)) {
            $attendees = [
                ['name' => $validated['leader_name'], 'role' => 'Pimpinan Sidang', 'notes' => 'Hadir'],
                ['name' => Auth::user()?->name ?? 'Guru BK', 'role' => 'Guru BK / Konselor', 'notes' => 'Hadir'],
                ['name' => 'Wali Kelas', 'role' => 'Wali Kelas Siswa', 'notes' => 'Hadir'],
                ['name' => 'Orang Tua / Wali Siswa', 'role' => 'Orang Tua / Wali Murid', 'notes' => 'Hadir'],
            ];
        }

        $validated['attendees'] = $attendees;
        unset($validated['attendees_input']);

        $conf = BkCaseConference::create($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('create', 'konferensi_kasus', "Mencatat Konferensi Kasus siswa {$student?->name}: {$conf->case_title} ({$conf->conference_code})");

        return redirect()->route('bk.case-conference.show', $conf)->with('success', "Konferensi Kasus untuk {$student?->name} berhasil dicatat.");
    }

    public function show(BkCaseConference $caseConference): View
    {
        $school = SchoolIdentity::current();
        $caseConference->load(['student.schoolClass', 'counselor']);

        return view('bk.case_conference.show', compact('caseConference', 'school'));
    }

    public function edit(BkCaseConference $caseConference): View
    {
        $caseConference->load(['student.schoolClass']);
        $students = Student::active()->with('schoolClass')->orderBy('name')->get();

        return view('bk.case_conference.edit', compact('caseConference', 'students'));
    }

    public function update(Request $request, BkCaseConference $caseConference): RedirectResponse
    {
        $validated = $request->validate([
            'case_title' => ['required', 'string', 'max:255'],
            'conference_date' => ['required', 'date'],
            'conference_time' => ['nullable', 'string', 'max:30'],
            'place' => ['required', 'string', 'max:255'],
            'leader_name' => ['required', 'string', 'max:255'],
            'case_background' => ['required', 'string'],
            'facts_and_findings' => ['required', 'string'],
            'discussion_dynamics' => ['nullable', 'string'],
            'agreed_decisions' => ['required', 'string'],
            'follow_up_commitments' => ['required', 'string'],
            'status' => ['required', 'in:TERJADWAL,TERLAKSANA,DITINDAKLANJUTI,SELESAI'],
        ]);

        $caseConference->update($validated);

        AuditTrailService::log('update', 'konferensi_kasus', "Memperbarui Konferensi Kasus ID #{$caseConference->id}");

        return redirect()->route('bk.case-conference.show', $caseConference)->with('success', 'Data Konferensi Kasus berhasil diperbarui.');
    }

    public function destroy(BkCaseConference $caseConference): RedirectResponse
    {
        $title = $caseConference->case_title;
        $caseConference->delete();

        AuditTrailService::log('delete', 'konferensi_kasus', "Menghapus Konferensi Kasus {$title}");

        return redirect()->route('bk.case-conference.index')->with('success', 'Data Konferensi Kasus berhasil dihapus.');
    }

    public function print(BkCaseConference $caseConference): View
    {
        $school = SchoolIdentity::current();
        $caseConference->load(['student.schoolClass', 'counselor']);

        AuditTrailService::log('print', 'konferensi_kasus', "Mencetak Berita Acara Konferensi Kasus: {$caseConference->case_title}");

        return view('bk.case_conference.print', compact('caseConference', 'school'));
    }
}

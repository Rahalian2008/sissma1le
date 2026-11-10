<?php

namespace App\Http\Controllers;

use App\Models\BkSociometry;
use App\Models\BkSociometryResponse;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkSociometryController extends Controller
{
    public function index(): View
    {
        $sociometries = BkSociometry::with(['schoolClass', 'counselor'])
            ->withCount('responses')
            ->latest('date')
            ->paginate(15);

        return view('bk.sociometry.index', compact('sociometries'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('bk.sociometry.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:school_classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'theme' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $sociometry = BkSociometry::create([
            ...$validated,
            'counselor_id' => Auth::id(),
            'status' => 'AKTIF',
        ]);

        return redirect()->route('bk.sociometry.show', $sociometry)
            ->with('success', 'Instrumen Sosiometri kelas berhasil dibuat.');
    }

    public function show(BkSociometry $sociometry): View
    {
        $sociometry->load(['schoolClass.students', 'counselor', 'responses.student', 'responses.firstChoice', 'responses.secondChoice', 'responses.thirdChoice']);

        // Kalkulasi popularitas pilihan siswa di kelas
        $classStudents = $sociometry->schoolClass?->students ?: collect();
        $popularityScores = [];
        foreach ($classStudents as $s) {
            $popularityScores[$s->id] = [
                'student' => $s,
                'first_count' => 0,
                'second_count' => 0,
                'third_count' => 0,
                'total_score' => 0,
            ];
        }

        foreach ($sociometry->responses as $r) {
            if ($r->first_choice_id && isset($popularityScores[$r->first_choice_id])) {
                $popularityScores[$r->first_choice_id]['first_count']++;
                $popularityScores[$r->first_choice_id]['total_score'] += 3;
            }
            if ($r->second_choice_id && isset($popularityScores[$r->second_choice_id])) {
                $popularityScores[$r->second_choice_id]['second_count']++;
                $popularityScores[$r->second_choice_id]['total_score'] += 2;
            }
            if ($r->third_choice_id && isset($popularityScores[$r->third_choice_id])) {
                $popularityScores[$r->third_choice_id]['third_count']++;
                $popularityScores[$r->third_choice_id]['total_score'] += 1;
            }
        }

        uasort($popularityScores, fn ($a, $b) => $b['total_score'] <=> $a['total_score']);

        return view('bk.sociometry.show', compact('sociometry', 'popularityScores'));
    }

    public function print(BkSociometry $sociometry): View
    {
        $sociometry->load(['schoolClass.students', 'counselor', 'responses.student', 'responses.firstChoice', 'responses.secondChoice', 'responses.thirdChoice']);

        $classStudents = $sociometry->schoolClass?->students ?: collect();
        $popularityScores = [];
        foreach ($classStudents as $s) {
            $popularityScores[$s->id] = [
                'student' => $s,
                'first_count' => 0,
                'second_count' => 0,
                'third_count' => 0,
                'total_score' => 0,
            ];
        }

        foreach ($sociometry->responses as $r) {
            if ($r->first_choice_id && isset($popularityScores[$r->first_choice_id])) {
                $popularityScores[$r->first_choice_id]['first_count']++;
                $popularityScores[$r->first_choice_id]['total_score'] += 3;
            }
            if ($r->second_choice_id && isset($popularityScores[$r->second_choice_id])) {
                $popularityScores[$r->second_choice_id]['second_count']++;
                $popularityScores[$r->second_choice_id]['total_score'] += 2;
            }
            if ($r->third_choice_id && isset($popularityScores[$r->third_choice_id])) {
                $popularityScores[$r->third_choice_id]['third_count']++;
                $popularityScores[$r->third_choice_id]['total_score'] += 1;
            }
        }

        uasort($popularityScores, fn ($a, $b) => $b['total_score'] <=> $a['total_score']);

        $school = SchoolIdentity::current();

        return view('bk.sociometry.print', compact('sociometry', 'popularityScores', 'school'));
    }

    public function storeResponse(Request $request, BkSociometry $sociometry): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'first_choice_id' => ['nullable', 'exists:students,id', 'different:student_id'],
            'second_choice_id' => ['nullable', 'exists:students,id', 'different:student_id', 'different:first_choice_id'],
            'third_choice_id' => ['nullable', 'exists:students,id', 'different:student_id', 'different:first_choice_id', 'different:second_choice_id'],
            'reasons' => ['nullable', 'string'],
        ]);

        BkSociometryResponse::updateOrCreate(
            ['sociometry_id' => $sociometry->id, 'student_id' => $validated['student_id']],
            $validated
        );

        return redirect()->route('bk.sociometry.show', $sociometry)
            ->with('success', 'Pilihan sosiometri siswa berhasil dicatat.');
    }

    public function destroy(BkSociometry $sociometry): RedirectResponse
    {
        $sociometry->delete();

        return redirect()->route('bk.sociometry.index')
            ->with('success', 'Instrumen sosiometri berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BkAkpd;
use App\Models\BkAkpdQuestion;
use App\Models\BkAkpdResponse;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkAkpdController extends Controller
{
    public function index(Request $request): View
    {
        $akpds = BkAkpd::withCount(['questions', 'responses'])->latest()->get();
        $totalStudents = Student::active()->count();
        $totalResponses = BkAkpdResponse::count();

        // Needs breakdown across all responses
        $fieldsCount = [
            'PRIBADI' => BkAkpdResponse::where('highest_need_field', 'PRIBADI')->count(),
            'SOSIAL' => BkAkpdResponse::where('highest_need_field', 'SOSIAL')->count(),
            'BELAJAR' => BkAkpdResponse::where('highest_need_field', 'BELAJAR')->count(),
            'KARIER' => BkAkpdResponse::where('highest_need_field', 'KARIER')->count(),
        ];

        return view('bk.akpd.index', compact('akpds', 'totalStudents', 'totalResponses', 'fieldsCount'));
    }

    public function show(Request $request, BkAkpd $akpd): View
    {
        $akpd->load(['questions', 'responses.student.schoolClass']);
        $classes = SchoolClass::orderBy('name')->get();

        $selectedClassId = $request->query('class_id');
        $responsesQuery = $akpd->responses();

        if ($selectedClassId) {
            $responsesQuery->whereHas('student', fn ($q) => $q->where('class_id', $selectedClassId));
        }

        $responses = $responsesQuery->with('student.schoolClass')->latest()->paginate(20)->withQueryString();

        // Calculate item frequencies (Top 10 highest chosen items)
        $allSelected = [];
        foreach ($akpd->responses as $resp) {
            $ids = is_array($resp->selected_question_ids) ? $resp->selected_question_ids : [];
            foreach ($ids as $id) {
                $allSelected[$id] = ($allSelected[$id] ?? 0) + 1;
            }
        }

        arsort($allSelected);
        $topQuestionIds = array_slice(array_keys($allSelected), 0, 10);
        $topQuestions = BkAkpdQuestion::whereIn('id', $topQuestionIds)->get()->map(function ($q) use ($allSelected, $akpd) {
            $q->chosen_count = $allSelected[$q->id] ?? 0;
            $divisor = max($akpd->responses->count(), 1);
            $q->percentage = round(($q->chosen_count / $divisor) * 100, 1);

            return $q;
        })->sortByDesc('chosen_count');

        return view('bk.akpd.show', compact('akpd', 'responses', 'classes', 'selectedClassId', 'topQuestions'));
    }

    public function take(BkAkpd $akpd): View
    {
        $akpd->load('questions');
        $user = Auth::user();

        // Determine student
        $student = null;
        if ($user && $user->role === 'siswa') {
            $student = Student::where('user_id', $user->id)->first() ?? Student::where('id', $user->student_id)->first();
        }

        if (! $student) {
            $student = Student::first();
        }

        $existing = BkAkpdResponse::where('akpd_id', $akpd->id)
            ->where('student_id', $student?->id)
            ->first();

        return view('bk.akpd.take', compact('akpd', 'student', 'existing'));
    }

    public function submit(Request $request, BkAkpd $akpd): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'selected_questions' => ['nullable', 'array'],
            'selected_questions.*' => ['integer', 'exists:bk_akpd_questions,id'],
        ]);

        $selectedIds = $validated['selected_questions'] ?? [];

        // Calculate breakdown by field
        $questions = BkAkpdQuestion::whereIn('id', $selectedIds)->get();
        $totalByField = [
            'PRIBADI' => $akpd->questions()->where('field_of_service', 'PRIBADI')->count() ?: 1,
            'SOSIAL' => $akpd->questions()->where('field_of_service', 'SOSIAL')->count() ?: 1,
            'BELAJAR' => $akpd->questions()->where('field_of_service', 'BELAJAR')->count() ?: 1,
            'KARIER' => $akpd->questions()->where('field_of_service', 'KARIER')->count() ?: 1,
        ];

        $chosenByField = [
            'PRIBADI' => $questions->where('field_of_service', 'PRIBADI')->count(),
            'SOSIAL' => $questions->where('field_of_service', 'SOSIAL')->count(),
            'BELAJAR' => $questions->where('field_of_service', 'BELAJAR')->count(),
            'KARIER' => $questions->where('field_of_service', 'KARIER')->count(),
        ];

        $pctByField = [];
        foreach ($chosenByField as $field => $count) {
            $pctByField[$field] = [
                'count' => $count,
                'total_field' => $totalByField[$field],
                'percentage' => round(($count / $totalByField[$field]) * 100, 1),
            ];
        }

        // Determine highest need
        uasort($pctByField, fn ($a, $b) => $b['percentage'] <=> $a['percentage']);
        $highestField = array_key_first($pctByField) ?? 'PRIBADI';

        BkAkpdResponse::updateOrCreate(
            [
                'akpd_id' => $akpd->id,
                'student_id' => $validated['student_id'],
            ],
            [
                'selected_question_ids' => $selectedIds,
                'score_per_field' => $pctByField,
                'highest_need_field' => $highestField,
                'submitted_at' => now(),
            ]
        );

        $student = Student::find($validated['student_id']);
        AuditTrailService::log('create', 'akpd', "Siswa {$student?->name} mengisi instrumen AKPD {$akpd->title}");

        return redirect()->route('bk.akpd.show', $akpd)->with('success', "Asesmen AKPD untuk {$student?->name} berhasil disimpan. Kebutuhan prioritas tertinggi: Bidang {$highestField}.");
    }

    public function printAnalysis(Request $request, BkAkpd $akpd): View
    {
        $school = SchoolIdentity::current();
        $akpd->load(['questions', 'responses.student.schoolClass']);

        $selectedClassId = $request->query('class_id');
        $className = 'Semua Kelas';

        $responses = $akpd->responses;
        if ($selectedClassId) {
            $cls = SchoolClass::find($selectedClassId);
            $className = $cls?->name ?? 'Kelas Terpilih';
            $responses = $responses->filter(fn ($r) => $r->student?->class_id == $selectedClassId);
        }

        // Aggregate statistics
        $totalRespondents = $responses->count();
        $fieldAverages = [
            'PRIBADI' => 0,
            'SOSIAL' => 0,
            'BELAJAR' => 0,
            'KARIER' => 0,
        ];

        foreach ($responses as $resp) {
            $scores = $resp->score_per_field ?? [];
            foreach ($fieldAverages as $f => $val) {
                $fieldAverages[$f] += $scores[$f]['percentage'] ?? 0;
            }
        }

        if ($totalRespondents > 0) {
            foreach ($fieldAverages as $f => $sum) {
                $fieldAverages[$f] = round($sum / $totalRespondents, 1);
            }
        }

        // Top 10 items
        $allSelected = [];
        foreach ($responses as $resp) {
            $ids = is_array($resp->selected_question_ids) ? $resp->selected_question_ids : [];
            foreach ($ids as $id) {
                $allSelected[$id] = ($allSelected[$id] ?? 0) + 1;
            }
        }

        arsort($allSelected);
        $topQuestionIds = array_slice(array_keys($allSelected), 0, 10);
        $topQuestions = BkAkpdQuestion::whereIn('id', $topQuestionIds)->get()->map(function ($q) use ($allSelected, $totalRespondents) {
            $q->chosen_count = $allSelected[$q->id] ?? 0;
            $divisor = max($totalRespondents, 1);
            $q->percentage = round(($q->chosen_count / $divisor) * 100, 1);

            return $q;
        })->sortByDesc('chosen_count');

        AuditTrailService::log('print', 'akpd', "Mencetak Laporan Analisis Kebutuhan AKPD {$akpd->title}");

        return view('bk.akpd.print_analysis', compact('school', 'akpd', 'className', 'totalRespondents', 'fieldAverages', 'topQuestions'));
    }
}

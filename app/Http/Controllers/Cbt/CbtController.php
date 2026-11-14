<?php

namespace App\Http\Controllers\Cbt;

use App\Http\Controllers\Controller;
use App\Models\CbtExam;
use App\Models\CbtExamQuestion;
use App\Models\CbtSession;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\CbtAiService;
use App\Services\CbtDocumentService;
use App\Services\CbtGradingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CbtController extends Controller
{
    /**
     * Dashboard Utama Manajemen Ujian Online (CBT).
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Jika siswa, arahkan langsung ke portal ujian siswa
        if ($user->isStudent()) {
            return redirect()->route('cbt.student.index');
        }

        $query = CbtExam::with(['subject', 'teacher.user', 'classes'])
            ->withCount(['questions', 'sessions']);

        // Filter berdasarkan peran pengguna
        if ($user->role === 'guru') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        } elseif ($user->role === 'wali_kelas') {
            $homeroomClass = SchoolClass::where('homeroom_teacher_id', $user->teacher?->id)->first();
            if ($homeroomClass) {
                $query->whereHas('classes', function ($q) use ($homeroomClass) {
                    $q->where('school_classes.id', $homeroomClass->id);
                });
            }
        }

        // Filter pencarian & tipe
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('exam_code', 'like', "%{$q}%")
                    ->orWhere('token', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('exam_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $exams = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total_exams' => CbtExam::count(),
            'total_questions' => CbtExamQuestion::count(),
            'active_exams' => CbtExam::where('status', 'published')->count(),
            'total_participants' => CbtSession::count(),
            'blocked_participants' => CbtSession::where('is_blocked', true)->count(),
        ];

        return view('cbt.index', compact('exams', 'stats'));
    }

    /**
     * Form Buat Paket Ujian Baru.
     */
    public function create(): View
    {
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->get();
        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();

        $suggestedCode = 'CBT-'.strtoupper(Str::random(4)).'-'.date('Y');
        $suggestedToken = strtoupper(Str::random(5));

        return view('cbt.exams.create', compact('subjects', 'teachers', 'classes', 'suggestedCode', 'suggestedToken'));
    }

    /**
     * Simpan Paket Ujian Baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_code' => 'required|string|max:50|unique:cbt_exams,exam_code',
            'exam_type' => 'required|string|max:50',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'duration_minutes' => 'required|integer|min:5|max:360',
            'token' => 'nullable|string|max:20',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'pass_percentage' => 'nullable|numeric|min:0|max:100',
            'max_violations' => 'nullable|integer|min:1|max:10',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:school_classes,id',
            'description' => 'nullable|string',
        ]);

        $exam = CbtExam::create([
            'title' => $validated['title'],
            'exam_code' => strtoupper($validated['exam_code']),
            'exam_type' => $validated['exam_type'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'] ?? Auth::user()->teacher?->id,
            'duration_minutes' => $validated['duration_minutes'],
            'token' => strtoupper($validated['token'] ?: Str::random(5)),
            'require_token' => $request->boolean('require_token'),
            'start_time' => $validated['start_time'] ?? now(),
            'end_time' => $validated['end_time'] ?? now()->addDays(7),
            'randomize_questions' => $request->boolean('randomize_questions'),
            'randomize_options' => $request->boolean('randomize_options'),
            'show_result_to_student' => $request->boolean('show_result_to_student'),
            'enable_security_block' => $request->boolean('enable_security_block', true),
            'max_violations' => $validated['max_violations'] ?? 3,
            'enable_geofence' => $request->boolean('enable_geofence', true),
            'geofence_latitude' => $request->filled('geofence_latitude') ? (float) $request->geofence_latitude : null,
            'geofence_longitude' => $request->filled('geofence_longitude') ? (float) $request->geofence_longitude : null,
            'geofence_radius' => $request->filled('geofence_radius') ? (int) $request->geofence_radius : 100,
            'pass_percentage' => $validated['pass_percentage'] ?? 75.00,
            'status' => 'published',
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $exam->classes()->sync($validated['class_ids']);

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', "Paket ujian '{$exam->title}' berhasil dibuat. Silakan tambahkan butir soal secara manual, generate AI, atau import.");
    }

    /**
     * Detail Paket Ujian & Manajer Soal.
     */
    public function show(CbtExam $exam, CbtDocumentService $docService): View
    {
        $exam->load(['subject', 'teacher.user', 'classes.students', 'questions']);
        $sessions = $exam->sessions()->with(['student.schoolClass'])->get();
        $availableRooms = $docService->getAvailableRooms($exam);

        return view('cbt.exams.show', compact('exam', 'sessions', 'availableRooms'));
    }

    /**
     * Form Edit Ujian.
     */
    public function edit(CbtExam $exam): View
    {
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->get();
        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();

        return view('cbt.exams.edit', compact('exam', 'subjects', 'teachers', 'classes'));
    }

    /**
     * Update Paket Ujian.
     */
    public function update(Request $request, CbtExam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_code' => "required|string|max:50|unique:cbt_exams,exam_code,{$exam->id}",
            'exam_type' => 'required|string|max:50',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'duration_minutes' => 'required|integer|min:5|max:360',
            'token' => 'nullable|string|max:20',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'pass_percentage' => 'nullable|numeric|min:0|max:100',
            'max_violations' => 'nullable|integer|min:1|max:10',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:school_classes,id',
            'status' => 'required|in:draft,published,ongoing,archived',
            'description' => 'nullable|string',
        ]);

        $exam->update([
            'title' => $validated['title'],
            'exam_code' => strtoupper($validated['exam_code']),
            'exam_type' => $validated['exam_type'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'] ?? $exam->teacher_id,
            'duration_minutes' => $validated['duration_minutes'],
            'token' => strtoupper($validated['token'] ?: $exam->token),
            'require_token' => $request->boolean('require_token'),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'randomize_questions' => $request->boolean('randomize_questions'),
            'randomize_options' => $request->boolean('randomize_options'),
            'show_result_to_student' => $request->boolean('show_result_to_student'),
            'enable_security_block' => $request->boolean('enable_security_block'),
            'max_violations' => $validated['max_violations'] ?? 3,
            'enable_geofence' => $request->boolean('enable_geofence'),
            'geofence_latitude' => $request->filled('geofence_latitude') ? (float) $request->geofence_latitude : null,
            'geofence_longitude' => $request->filled('geofence_longitude') ? (float) $request->geofence_longitude : null,
            'geofence_radius' => $request->filled('geofence_radius') ? (int) $request->geofence_radius : 100,
            'pass_percentage' => $validated['pass_percentage'] ?? 75.00,
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
        ]);

        $exam->classes()->sync($validated['class_ids']);

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', 'Konfigurasi ujian berhasil diperbarui.');
    }

    /**
     * Hapus Paket Ujian.
     */
    public function destroy(CbtExam $exam): RedirectResponse
    {
        $exam->classes()->detach();
        $exam->questions()->delete();
        $exam->sessions()->delete();
        $exam->delete();

        return redirect()->route('cbt.index')
            ->with('success', 'Paket ujian berhasil dihapus.');
    }

    /**
     * Download Template Format Import Soal (CSV).
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_soal_cbt.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'question_type',
                'question_text',
                'stimulus',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'option_e',
                'correct_answer',
                'score_weight',
                'cognitive_level',
                'difficulty_level',
                'explanation',
                'learning_objective',
                'indicator',
            ]);

            // Contoh Soal 1: Matematika (LaTeX KaTeX)
            fputcsv($handle, [
                'multiple_choice',
                'Tentukan himpunan penyelesaian dari persamaan kuadrat $x^2 - 5x + 6 = 0$!',
                'Disajikan persamaan kuadrat aljabar',
                'x = 2 atau x = 3',
                'x = -2 atau x = -3',
                'x = 1 atau x = 6',
                'x = -1 atau x = -6',
                'x = 0 atau x = 5',
                'A',
                '1.0',
                'L2',
                'sedang',
                'Faktorisasi $(x-2)(x-3)=0$, sehingga $x=2$ atau $x=3$.',
                'Persamaan Kuadrat',
                'Peserta didik dapat menentukan akar persamaan kuadrat',
            ]);

            // Contoh Soal 2: Kimia (mhchem)
            fputcsv($handle, [
                'multiple_choice',
                'Pada reaksi kimia $\\ce{2H2 + O2 -> 2H2O}$, berapakah perbandingan volume gas hidrogen terhadap oksigen pada suhu dan tekanan yang sama?',
                'Hukum Perbandingan Volume Gay-Lussac',
                '2 : 1',
                '1 : 2',
                '1 : 1',
                '2 : 3',
                '3 : 2',
                'A',
                '1.0',
                'L2',
                'sedang',
                'Koefisien reaksi menyatakan perbandingan volume gas: 2 volume H2 bereaksi dengan 1 volume O2.',
                'Stoikiometri Gas',
                'Menentukan perbandingan volume gas reaksi kimia',
            ]);

            // Contoh Soal 3: Fisika
            fputcsv($handle, [
                'multiple_choice',
                'Gelombang elektromagnetik memiliki panjang gelombang $\\lambda = 500\\text{ nm}$. Berapakah frekuensi gelombang tersebut jika cepat rambat cahaya $c = 3 \\times 10^8\\text{ m/s}$?',
                'Konsep Gelombang Elektromagnetik',
                '$6 \\times 10^{14}\\text{ Hz}$',
                '$3 \\times 10^{14}\\text{ Hz}$',
                '$1.5 \\times 10^{14}\\text{ Hz}$',
                '$5 \\times 10^{14}\\text{ Hz}$',
                '$6 \\times 10^{15}\\text{ Hz}$',
                'A',
                '1.5',
                'L3',
                'sukar',
                '$f = \\frac{c}{\\lambda} = \\frac{3 \\times 10^8}{500 \\times 10^{-9}} = 6 \\times 10^{14}\\text{ Hz}$',
                'Gelombang Cahaya',
                'Menghitung frekuensi gelombang berdasarkan panjang gelombang',
            ]);

            // Contoh Soal 4: Biologi (Genetika)
            fputcsv($handle, [
                'multiple_choice',
                'Pada persilangan dihibrid ercis berbiji bulat kuning (BbKk) dengan sesamanya, persentase kemungkinan fenotipe bulat hijau adalah...',
                'Hukum Mendel II',
                '18.75% (3/16)',
                '56.25% (9/16)',
                '6.25% (1/16)',
                '25.00% (4/16)',
                '50.00% (8/16)',
                'A',
                '1.0',
                'L2',
                'sedang',
                'Rasio fenotipe F2 dihibrid adalah 9:3:3:1. Bagian bulat hijau adalah 3/16 atau 18.75%.',
                'Pewarisan Sifat',
                'Menghitung persentase fenotipe keturunan persilangan dihibrid',
            ]);

            // Contoh Soal 5: Isian Singkat
            fputcsv($handle, [
                'short_answer',
                'Zat hijau daun pada tumbuhan yang berfungsi menyerap energi cahaya matahari untuk fotosintesis disebut...',
                'Fisiologi Tumbuhan',
                '',
                '',
                '',
                '',
                '',
                'Klorofil',
                '1.0',
                'L1',
                'mudah',
                'Klorofil adalah pigmen fotosintesis.',
                'Fotosintesis',
                'Menyebutkan organel/pigmen fotosintesis',
            ]);

            // Contoh Soal 6: Uraian / Esai
            fputcsv($handle, [
                'essay',
                'Jelaskan perbedaan mendasar antara pembelahan sel secara mitosis dan meiosis beserta fungsinya bagi organisme!',
                'Pembelahan Sel',
                '',
                '',
                '',
                '',
                '',
                'Mitosis: menghasilkan 2 sel anak identik diploid (2n) untuk pertumbuhan & regenerasi. Meiosis: menghasilkan 4 sel anak haploid (n) untuk gametogenesis.',
                '2.0',
                'L3',
                'sukar',
                'Kriteria penilaian mencakup jumlah sel anak, sifat kromosom, dan tujuan biologis.',
                'Pembelahan Sel',
                'Menganalisis perbedaan mitosis dan meiosis',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Tambah Butir Soal Baru (Entri Manual).
     */
    public function storeQuestion(Request $request, CbtExam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'question_type' => 'required|in:multiple_choice,complex_multiple_choice,true_false,matching,short_answer,essay',
            'stimulus' => 'nullable|string',
            'question_text' => 'required|string',
            'score_weight' => 'required|numeric|min:0.1',
            'learning_objective' => 'nullable|string',
            'indicator' => 'nullable|string',
            'cognitive_level' => 'required|in:L1,L2,L3',
            'difficulty_level' => 'required|in:mudah,sedang,sukar',
            'question_image' => 'nullable|image|max:5120',
        ]);

        $mediaPath = null;
        if ($request->hasFile('question_image')) {
            $mediaPath = $request->file('question_image')->store('cbt_questions', 'public');
        } elseif ($request->filled('existing_media_path')) {
            $mediaPath = $request->input('existing_media_path');
        }

        $options = null;
        $correctAnswer = null;

        switch ($validated['question_type']) {
            case 'multiple_choice':
                $options = [
                    'A' => $request->input('option_a', ''),
                    'B' => $request->input('option_b', ''),
                    'C' => $request->input('option_c', ''),
                    'D' => $request->input('option_d', ''),
                    'E' => $request->input('option_e', ''),
                ];
                $correctAnswer = $request->input('mc_correct_answer', 'A');
                break;

            case 'complex_multiple_choice':
                $opts = $request->input('cmc_options', []);
                $options = is_array($opts) ? $opts : [];
                $correctAnswer = $request->input('cmc_correct_answer', []);
                break;

            case 'true_false':
                $statements = $request->input('tf_statements', []);
                $keys = $request->input('tf_keys', []);
                $formattedOpts = [];
                $formattedKeys = [];
                foreach ($statements as $idx => $st) {
                    $sid = 'S'.($idx + 1);
                    $formattedOpts[] = ['id' => $sid, 'statement' => $st];
                    $formattedKeys[] = ['id' => $sid, 'is_true' => ! empty($keys[$idx])];
                }
                $options = $formattedOpts;
                $correctAnswer = $formattedKeys;
                break;

            case 'matching':
                $premises = $request->input('match_premises', []);
                $responses = $request->input('match_responses', []);
                $pairs = $request->input('match_pairs', []);
                $options = [
                    'premises' => array_map(fn ($p, $idx) => ['id' => $idx + 1, 'text' => $p], $premises, array_keys($premises)),
                    'responses' => array_map(fn ($r, $idx) => ['id' => chr(65 + $idx), 'text' => $r], $responses, array_keys($responses)),
                ];
                $correctAnswer = [];
                foreach ($pairs as $pid => $rid) {
                    $correctAnswer[] = ['premise_id' => (int) $pid, 'response_id' => $rid];
                }
                break;

            case 'short_answer':
                $options = null;
                $correctAnswer = trim($request->input('short_correct_answer', ''));
                break;

            case 'essay':
                $options = null;
                $correctAnswer = $request->input('essay_rubric', '');
                break;
        }

        $nextNum = ($exam->questions()->max('question_number') ?? 0) + 1;

        $exam->questions()->create([
            'question_number' => $nextNum,
            'question_type' => $validated['question_type'],
            'stimulus' => $validated['stimulus'] ?? null,
            'media_path' => $mediaPath,
            'question_text' => $validated['question_text'],
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => $request->input('explanation'),
            'score_weight' => $validated['score_weight'],
            'learning_objective' => $validated['learning_objective'] ?? null,
            'indicator' => $validated['indicator'] ?? null,
            'cognitive_level' => $validated['cognitive_level'],
            'difficulty_level' => $validated['difficulty_level'],
        ]);

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', 'Butir soal baru berhasil ditambahkan.');
    }

    /**
     * Hapus Butir Soal.
     */
    public function destroyQuestion(CbtExam $exam, CbtExamQuestion $question): RedirectResponse
    {
        $question->delete();

        // Susun ulang penomoran soal
        $questions = $exam->questions()->orderBy('question_number')->get();
        foreach ($questions as $idx => $q) {
            $q->update(['question_number' => $idx + 1]);
        }

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Generate Soal Otomatis Berbasis AI (Gemini).
     */
    public function generateAi(Request $request, CbtExam $exam, CbtAiService $aiService): RedirectResponse
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'required|integer|min:1|max:20',
            'question_types' => 'required|array',
            'cognitive_focus' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        $subjectName = $exam->subject?->name ?? 'Mata Pelajaran';
        $gradeName = $exam->classes->first()?->grade ?? 'X';

        $generated = $aiService->generateQuestions([
            'subject' => $subjectName,
            'grade' => "Kelas {$gradeName}",
            'topic' => $request->topic,
            'question_types' => $request->question_types,
            'count' => (int) $request->count,
            'cognitive_focus' => $request->cognitive_focus ?? 'Campuran L1, L2, L3 (HOTS)',
            'additional_notes' => $request->additional_notes,
        ]);

        $startNum = ($exam->questions()->max('question_number') ?? 0) + 1;
        $savedCount = 0;

        foreach ($generated as $idx => $qData) {
            $exam->questions()->create([
                'question_number' => $startNum + $idx,
                'question_type' => $qData['question_type'] ?? 'multiple_choice',
                'stimulus' => $qData['stimulus'] ?? null,
                'question_text' => $qData['question_text'] ?? '',
                'options' => $qData['options'] ?? null,
                'correct_answer' => $qData['correct_answer'] ?? null,
                'explanation' => $qData['explanation'] ?? null,
                'score_weight' => $qData['score_weight'] ?? 1.0,
                'learning_objective' => $qData['learning_objective'] ?? $request->topic,
                'indicator' => $qData['indicator'] ?? null,
                'cognitive_level' => $qData['cognitive_level'] ?? 'L2',
                'difficulty_level' => $qData['difficulty_level'] ?? 'sedang',
            ]);
            $savedCount++;
        }

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', "✨ AI berhasil men-generate dan menambahkan {$savedCount} butir soal ke dalam paket ujian!");
    }

    /**
     * Import Soal dari Format CSV atau JSON.
     */
    public function importQuestions(Request $request, CbtExam $exam): RedirectResponse
    {
        $request->validate([
            'import_file' => 'nullable|file|mimes:csv,txt|max:5120',
            'import_data' => 'nullable|string',
        ]);

        $items = [];

        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle !== false) {
                $header = fgetcsv($handle);
                if ($header !== false) {
                    $cleanHeader = array_map(function ($col) {
                        return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $col)));
                    }, $header);

                    while (($row = fgetcsv($handle)) !== false) {
                        if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                            continue;
                        }
                        $rowAssoc = [];
                        foreach ($cleanHeader as $idx => $key) {
                            $rowAssoc[$key] = $row[$idx] ?? '';
                        }

                        $type = $rowAssoc['question_type'] ?? 'multiple_choice';
                        $qText = $rowAssoc['question_text'] ?? '';
                        if (empty($qText)) {
                            continue;
                        }

                        $options = null;
                        $correctAnswer = $rowAssoc['correct_answer'] ?? null;

                        if ($type === 'multiple_choice') {
                            $options = [
                                'A' => $rowAssoc['option_a'] ?? '',
                                'B' => $rowAssoc['option_b'] ?? '',
                                'C' => $rowAssoc['option_c'] ?? '',
                                'D' => $rowAssoc['option_d'] ?? '',
                                'E' => $rowAssoc['option_e'] ?? '',
                            ];
                        } elseif ($type === 'complex_multiple_choice') {
                            $options = array_filter([
                                'A' => $rowAssoc['option_a'] ?? '',
                                'B' => $rowAssoc['option_b'] ?? '',
                                'C' => $rowAssoc['option_c'] ?? '',
                                'D' => $rowAssoc['option_d'] ?? '',
                                'E' => $rowAssoc['option_e'] ?? '',
                            ]);
                            if (is_string($correctAnswer)) {
                                $correctAnswer = array_map('trim', explode(',', $correctAnswer));
                            }
                        }

                        $items[] = [
                            'question_type' => $type,
                            'stimulus' => ! empty($rowAssoc['stimulus']) ? $rowAssoc['stimulus'] : null,
                            'question_text' => $qText,
                            'options' => $options,
                            'correct_answer' => $correctAnswer,
                            'score_weight' => ! empty($rowAssoc['score_weight']) ? (float) $rowAssoc['score_weight'] : 1.0,
                            'learning_objective' => $rowAssoc['learning_objective'] ?? null,
                            'indicator' => $rowAssoc['indicator'] ?? null,
                            'cognitive_level' => in_array(strtoupper($rowAssoc['cognitive_level'] ?? ''), ['L1', 'L2', 'L3']) ? strtoupper($rowAssoc['cognitive_level']) : 'L2',
                            'difficulty_level' => in_array(strtolower($rowAssoc['difficulty_level'] ?? ''), ['mudah', 'sedang', 'sukar']) ? strtolower($rowAssoc['difficulty_level']) : 'sedang',
                            'explanation' => $rowAssoc['explanation'] ?? null,
                        ];
                    }
                }
                fclose($handle);
            }
        } elseif ($request->filled('import_data')) {
            $raw = trim($request->import_data);
            $decoded = json_decode($raw, true);

            if (! is_array($decoded)) {
                return back()->with('error', 'Format data tidak valid. Pastikan data berformat JSON sesuai template.');
            }

            $items = isset($decoded['questions']) && is_array($decoded['questions']) ? $decoded['questions'] : $decoded;
        } else {
            return back()->with('error', 'Silakan pilih file CSV atau tempel teks JSON untuk mengimpor soal.');
        }

        if (empty($items)) {
            return back()->with('error', 'Tidak ada butir soal yang valid ditemukan.');
        }

        $startNum = ($exam->questions()->max('question_number') ?? 0) + 1;
        $count = 0;

        foreach ($items as $item) {
            if (empty($item['question_text'])) {
                continue;
            }

            $exam->questions()->create([
                'question_number' => $startNum + $count,
                'question_type' => $item['question_type'] ?? 'multiple_choice',
                'stimulus' => $item['stimulus'] ?? null,
                'question_text' => $item['question_text'],
                'options' => $item['options'] ?? null,
                'correct_answer' => $item['correct_answer'] ?? null,
                'explanation' => $item['explanation'] ?? null,
                'score_weight' => $item['score_weight'] ?? 1.0,
                'learning_objective' => $item['learning_objective'] ?? null,
                'indicator' => $item['indicator'] ?? null,
                'cognitive_level' => $item['cognitive_level'] ?? 'L2',
                'difficulty_level' => $item['difficulty_level'] ?? 'sedang',
            ]);
            $count++;
        }

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', "✨ Berhasil mengimpor {$count} butir soal ke dalam paket ujian!");
    }

    /**
     * Live Monitoring Ujian untuk Pengawas & Guru.
     */
    public function monitor(CbtExam $exam): View
    {
        $exam->load(['classes', 'subject']);
        $sessions = $exam->sessions()
            ->with(['student.schoolClass', 'violations'])
            ->latest('updated_at')
            ->get();

        $stats = [
            'total' => $sessions->count(),
            'in_progress' => $sessions->where('status', 'in_progress')->count(),
            'submitted' => $sessions->where('status', 'submitted')->count(),
            'blocked' => $sessions->where('is_blocked', true)->count(),
            'not_started' => $sessions->where('status', 'not_started')->count(),
        ];

        return view('cbt.exams.monitor', compact('exam', 'sessions', 'stats'));
    }

    /**
     * Buka Blokir Siswa yang Terkena Auto-Block Keamanan.
     */
    public function unblockStudent(CbtExam $exam, CbtSession $session): JsonResponse|RedirectResponse
    {
        $session->update([
            'is_blocked' => false,
            'status' => 'in_progress',
            'unblocked_at' => now(),
            'unblocked_by' => Auth::id(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Blokir siswa berhasil dibuka.',
            ]);
        }

        return back()->with('success', "Blokir ujian untuk siswa {$session->student?->name} berhasil dibuka.");
    }

    /**
     * Distribusi Siswa ke Ruang Ujian (Otomatis Zig-Zag Rombel / Sequential).
     */
    public function distributeRooms(Request $request, CbtExam $exam, CbtDocumentService $docService): RedirectResponse
    {
        $request->validate([
            'rooms' => 'required|string',
            'capacity' => 'required|integer|min:1|max:100',
            'mode' => 'required|in:zigzag,sequential',
        ]);

        $roomNames = array_filter(array_map('trim', explode(',', $request->rooms)));
        if (empty($roomNames)) {
            $roomNames = ['Ruang 01'];
        }

        $count = $docService->distributeStudentsToRooms(
            $exam,
            $roomNames,
            (int) $request->capacity,
            $request->mode
        );

        return back()->with('success', "✨ Berhasil mendistribusikan {$count} siswa ke dalam ".count($roomNames).' ruang ujian.');
    }

    /**
     * Toggle Akses Masuk Siswa ke Ujian (Aktif / Nonaktif Perorangan).
     */
    public function toggleStudentAccess(Request $request, CbtExam $exam, CbtSession $session): JsonResponse|RedirectResponse
    {
        $session->update([
            'is_allowed' => ! $session->is_allowed,
        ]);

        $statusText = $session->is_allowed ? 'diaktifkan (diizinkan)' : 'dinonaktifkan (dikunci)';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_allowed' => $session->is_allowed,
                'message' => "Akses ujian untuk {$session->student?->name} berhasil {$statusText}.",
            ]);
        }

        return back()->with('success', "Akses ujian untuk siswa {$session->student?->name} berhasil {$statusText}.");
    }

    /**
     * Update Ruang Ujian Siswa.
     */
    public function updateStudentRoom(Request $request, CbtExam $exam, CbtSession $session): JsonResponse|RedirectResponse
    {
        $request->validate([
            'room_name' => 'required|string|max:50',
        ]);

        $session->update([
            'room_name' => trim($request->room_name),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'room_name' => $session->room_name,
                'message' => "Ruang ujian {$session->student?->name} diubah ke {$session->room_name}.",
            ]);
        }

        return back()->with('success', "Ruang ujian untuk {$session->student?->name} berhasil diubah ke {$session->room_name}.");
    }

    /**
     * Update Akses Masal Siswa (Allow All / Block All).
     */
    public function bulkUpdateAccess(Request $request, CbtExam $exam): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:allow_all,block_all',
            'room_name' => 'nullable|string',
        ]);

        $isAllowed = $request->action === 'allow_all';
        $query = $exam->sessions();

        if ($request->filled('room_name')) {
            $query->where('room_name', $request->room_name);
        }

        $query->update(['is_allowed' => $isAllowed]);

        $statusText = $isAllowed ? 'diaktifkan' : 'dinonaktifkan';
        $roomInfo = $request->filled('room_name') ? " pada {$request->room_name}" : '';

        return back()->with('success', "Akses ujian siswa{$roomInfo} berhasil {$statusText}.");
    }

    /**
     * Rekap Hasil Nilai Ujian & Penilaian Manual Esai.
     */
    public function results(CbtExam $exam, CbtGradingService $grading): View
    {
        $exam->load(['questions', 'classes', 'subject']);
        $sessions = $exam->sessions()
            ->with(['student.schoolClass', 'answers.question'])
            ->orderByDesc('final_grade')
            ->get();

        // Pastikan kalkulasi nilai sudah ter-update
        foreach ($sessions as $s) {
            if ($s->status === 'submitted' && ! $s->is_graded) {
                $grading->recalculateSession($s);
            }
        }

        $avgGrade = $sessions->where('status', 'submitted')->avg('final_grade') ?? 0;
        $highest = $sessions->where('status', 'submitted')->max('final_grade') ?? 0;
        $lowest = $sessions->where('status', 'submitted')->min('final_grade') ?? 0;
        $passCount = $sessions->filter(fn ($s) => $s->isPassed())->count();

        return view('cbt.exams.results', compact('exam', 'sessions', 'avgGrade', 'highest', 'lowest', 'passCount'));
    }

    /**
     * Simpan Nilai Koreksi Manual Esai oleh Guru.
     */
    public function gradeEssay(Request $request, CbtExam $exam, CbtSession $session, CbtGradingService $grading): RedirectResponse
    {
        $scores = $request->input('essay_scores', []);
        $feedbacks = $request->input('essay_feedbacks', []);

        foreach ($scores as $ansId => $score) {
            $answer = $session->answers()->find($ansId);
            if ($answer) {
                $maxWeight = (float) ($answer->question?->score_weight ?? 1);
                $finalScore = min($maxWeight, max(0.0, (float) $score));
                $answer->update([
                    'score_earned' => $finalScore,
                    'is_correct' => $finalScore >= ($maxWeight * 0.5),
                    'teacher_feedback' => $feedbacks[$ansId] ?? null,
                ]);
            }
        }

        $grading->recalculateSession($session);

        return back()->with('success', "Koreksi nilai esai untuk {$session->student?->name} berhasil disimpan.");
    }

    // =========================================================================
    // DOKUMEN KURIKULUM & ADMINISTRASI UJIAN (CETAK RESMI)
    // =========================================================================

    public function printKisiKisi(CbtExam $exam, CbtDocumentService $docService): View
    {
        $data = $docService->prepareKisiKisiData($exam);

        return view('cbt.documents.kisi_kisi', $data);
    }

    public function printKartuSoal(CbtExam $exam, CbtDocumentService $docService): View
    {
        $data = $docService->prepareKartuSoalData($exam);

        return view('cbt.documents.kartu_soal', $data);
    }

    public function printKartuPeserta(Request $request, CbtExam $exam, CbtDocumentService $docService): View
    {
        $classId = $request->filled('class_id') ? (int) $request->class_id : null;
        $roomName = $request->filled('room') ? trim($request->room) : null;
        $data = $docService->prepareKartuPesertaData($exam, $classId, $roomName);

        return view('cbt.documents.kartu_peserta', $data);
    }

    public function printDaftarHadir(Request $request, CbtExam $exam, CbtDocumentService $docService): View
    {
        $classId = $request->filled('class_id') ? (int) $request->class_id : null;
        $roomName = $request->filled('room') ? trim($request->room) : null;
        $isManual = $request->boolean('is_manual');
        $data = $docService->prepareDaftarHadirData($exam, $classId, $roomName, $isManual);

        return view('cbt.documents.daftar_hadir', $data);
    }

    public function printBeritaAcara(Request $request, CbtExam $exam, CbtDocumentService $docService): View
    {
        $classId = $request->filled('class_id') ? (int) $request->class_id : null;
        $roomName = $request->filled('room') ? trim($request->room) : null;
        $isManual = $request->boolean('is_manual');
        $data = $docService->prepareBeritaAcaraData($exam, $classId, $roomName, $isManual);

        return view('cbt.documents.berita_acara', $data);
    }
}

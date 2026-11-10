<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ReferenceItem;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentRombelController extends Controller
{
    public function index(Request $request): View
    {
        $hasCurrentRombel = Schema::hasColumn('students', 'current_rombel');

        $rombels = ReferenceItem::where('category', 'rombel')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();

        if (empty($rombels)) {
            $rombels = SchoolClass::orderBy('name')->pluck('name')->toArray();
        }

        if (empty($rombels) && $hasCurrentRombel) {
            $rombels = Student::distinct()->whereNotNull('current_rombel')->pluck('current_rombel')->toArray();
        }

        if (empty($rombels)) {
            $rombels = ['X Merdeka 1', 'X Merdeka 2', 'XI Merdeka 1', 'XII Merdeka 1'];
        }

        $selectedRombel = $request->query('rombel', $rombels[0] ?? 'X Merdeka 1');

        if ($hasCurrentRombel) {
            $students = Student::active()
                ->where('current_rombel', $selectedRombel)
                ->orderBy('name')
                ->get();
        } else {
            $students = Student::active()
                ->whereHas('schoolClass', fn ($q) => $q->where('name', $selectedRombel))
                ->orderBy('name')
                ->get();

            if ($students->isEmpty()) {
                $students = Student::active()->where('class_id', SchoolClass::where('name', $selectedRombel)->value('id'))->orderBy('name')->get();
            }
        }

        // Rombel stats summary
        $rombelStats = [];
        foreach ($rombels as $r) {
            if ($hasCurrentRombel) {
                $total = Student::active()->where('current_rombel', $r)->count();
                $male = Student::active()->where('current_rombel', $r)->where('gender', 'L')->count();
                $female = Student::active()->where('current_rombel', $r)->where('gender', 'P')->count();
            } else {
                $total = Student::active()->whereHas('schoolClass', fn ($q) => $q->where('name', $r))->count();
                $male = Student::active()->whereHas('schoolClass', fn ($q) => $q->where('name', $r))->where('gender', 'L')->count();
                $female = Student::active()->whereHas('schoolClass', fn ($q) => $q->where('name', $r))->where('gender', 'P')->count();
            }
            $rombelStats[$r] = [
                'total' => $total,
                'male' => $male,
                'female' => $female,
            ];
        }

        // Wali kelas lookup
        $waliKelas = Employee::getWaliKelasForRombel($selectedRombel);

        return view('students.rombel.index', compact('rombels', 'selectedRombel', 'students', 'rombelStats', 'waliKelas'));
    }

    /**
     * Pindahkan rombel untuk satu siswa
     */
    public function moveStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'target_rombel' => ['required', 'string', 'max:50'],
            'target_class_level' => ['nullable', 'string', 'max:10'],
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $oldRombel = $student->schoolClass?->name ?? ($student->current_rombel ?? '-');
        $classLevel = ! empty($validated['target_class_level'])
            ? $validated['target_class_level']
            : (explode(' ', $validated['target_rombel'])[0] ?? ($student->current_class_level ?? 'X'));

        $updateData = [];
        $targetClass = SchoolClass::where('name', $validated['target_rombel'])->first();
        if ($targetClass) {
            $updateData['class_id'] = $targetClass->id;
        }
        if (Schema::hasColumn('students', 'current_rombel')) {
            $updateData['current_rombel'] = $validated['target_rombel'];
        }
        if (Schema::hasColumn('students', 'current_class_level')) {
            $updateData['current_class_level'] = $classLevel;
        }

        if (! empty($updateData)) {
            $student->update($updateData);
        }

        AuditTrailService::log('update', 'rombel', "Memindahkan rombel siswa {$student->name}: {$oldRombel} -> {$validated['target_rombel']}");

        return back()->with('success', "Siswa {$student->name} berhasil dipindahkan ke rombel {$validated['target_rombel']}.");
    }

    /**
     * Kenaikan Kelas Massal Per Rombel
     */
    public function promoteRombel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_rombel' => ['required', 'string'],
            'action_type' => ['required', 'in:promote,graduate'],
            'target_rombel' => ['nullable', 'string'],
            'target_class_level' => ['nullable', 'string'],
        ]);

        $sourceRombel = $validated['source_rombel'];
        $hasCurrentRombel = Schema::hasColumn('students', 'current_rombel');

        if ($hasCurrentRombel) {
            $students = Student::active()->where('current_rombel', $sourceRombel)->get();
        } else {
            $students = Student::active()->whereHas('schoolClass', fn ($q) => $q->where('name', $sourceRombel))->get();
        }

        if ($students->isEmpty()) {
            return back()->with('error', "Tidak ada siswa aktif pada rombel {$sourceRombel}.");
        }

        $count = $students->count();

        if ($validated['action_type'] === 'graduate') {
            foreach ($students as $s) {
                $up = [];
                if (Schema::hasColumn('students', 'status')) {
                    $up['status'] = 'Lulus';
                }
                if (Schema::hasColumn('students', 'is_active')) {
                    $up['is_active'] = false;
                }
                if (Schema::hasColumn('students', 'graduation_year')) {
                    $up['graduation_year'] = date('Y');
                }
                if (! empty($up)) {
                    $s->update($up);
                }
            }
            $msg = "{$count} siswa rombel {$sourceRombel} berhasil diluluskan secara massal dan masuk ke data kelulusan.";
        } else {
            $targetRombel = $validated['target_rombel'];
            $targetLevel = ! empty($validated['target_class_level'])
                ? $validated['target_class_level']
                : (explode(' ', (string) $targetRombel)[0] ?? 'XI');

            $targetClass = SchoolClass::where('name', $targetRombel)->first();

            foreach ($students as $s) {
                $up = [];
                if ($targetClass) {
                    $up['class_id'] = $targetClass->id;
                }
                if (Schema::hasColumn('students', 'current_rombel')) {
                    $up['current_rombel'] = $targetRombel;
                }
                if (Schema::hasColumn('students', 'current_class_level')) {
                    $up['current_class_level'] = $targetLevel;
                }
                if (! empty($up)) {
                    $s->update($up);
                }
            }
            $msg = "{$count} siswa berhasil dinaikkan dari {$sourceRombel} ke {$targetRombel}.";
        }

        AuditTrailService::log('update', 'kenaikan_kelas', "Proses kenaikan kelas/kelulusan massal rombel {$sourceRombel}: {$count} siswa");

        return back()->with('success', $msg);
    }

    /**
     * Cetak Daftar Siswa Per Rombongan Belajar (Format Resmi Disdik Jabar)
     */
    public function printRombel(Request $request): View
    {
        $school = SchoolIdentity::current();
        $rombel = $request->query('rombel', 'X Merdeka 1');
        $hasCurrentRombel = Schema::hasColumn('students', 'current_rombel');

        if ($hasCurrentRombel) {
            $students = Student::active()
                ->where('current_rombel', $rombel)
                ->orderBy('name')
                ->get();
        } else {
            $students = Student::active()
                ->whereHas('schoolClass', fn ($q) => $q->where('name', $rombel))
                ->orderBy('name')
                ->get();
        }

        $waliKelas = Employee::getWaliKelasForRombel($rombel);

        AuditTrailService::log('print', 'rombel', "Mencetak Daftar Siswa Rombel {$rombel}");

        return view('students.rombel.print', compact('school', 'rombel', 'students', 'waliKelas'));
    }
}

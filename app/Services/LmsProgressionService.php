<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\LmsQuiz;
use App\Models\Student;
use Illuminate\Support\Collection;

class LmsProgressionService
{
    /**
     * Memeriksa apakah siswa diizinkan mengakses materi tertentu secara berurutan.
     *
     * @return array{allowed: bool, prerequisite: ?LmsMaterial, message: ?string}
     */
    public static function checkMaterialAccess(CourseClass $courseClass, LmsMaterial $material, ?Student $student): array
    {
        if (! $student) {
            return ['allowed' => true, 'prerequisite' => null, 'message' => null];
        }

        // Validasi waktu dimulai materi (jika disetel di masa depan)
        if ($material->start_time && now()->isBefore($material->start_time)) {
            return [
                'allowed' => false,
                'prerequisite' => null,
                'message' => "Materi '{$material->title}' baru dapat diakses mulai ".$material->start_time->translatedFormat('d F Y, H:i').' WIB.',
            ];
        }

        $allMaterials = LmsMaterial::where('course_class_id', $courseClass->id)
            ->where('is_published', true)
            ->orderBy('id', 'asc')
            ->get();

        $blockingPrerequisite = null;

        foreach ($allMaterials as $item) {
            if ($item->id === $material->id) {
                if ($blockingPrerequisite) {
                    return [
                        'allowed' => false,
                        'prerequisite' => $blockingPrerequisite,
                        'message' => "Materi '{$material->title}' masih terkunci. Anda harus menyelesaikan materi '{$blockingPrerequisite->title}' terlebih dahulu.",
                    ];
                }

                return ['allowed' => true, 'prerequisite' => null, 'message' => null];
            }

            $isCompleted = $item->reads()
                ->where('student_id', $student->id)
                ->exists();

            if (! $isCompleted && ! $blockingPrerequisite) {
                $blockingPrerequisite = $item;
            }
        }

        return ['allowed' => true, 'prerequisite' => null, 'message' => null];
    }

    /**
     * Memeriksa apakah siswa diizinkan mengakses tugas tertentu secara berurutan dan sesuai waktu mulai.
     * Sekalipun batas waktu sudah lewat, penugasan tetap dapat dibuka/diakses.
     *
     * @return array{allowed: bool, prerequisite: ?LmsAssignment, message: ?string}
     */
    public static function checkAssignmentAccess(CourseClass $courseClass, LmsAssignment $assignment, ?Student $student): array
    {
        if (! $student) {
            return ['allowed' => true, 'prerequisite' => null, 'message' => null];
        }

        // Validasi waktu mulai penugasan (jika disetel di masa depan)
        if ($assignment->start_time && now()->isBefore($assignment->start_time)) {
            return [
                'allowed' => false,
                'prerequisite' => null,
                'message' => "Tugas & PR '{$assignment->title}' baru dapat diakses mulai ".$assignment->start_time->translatedFormat('d F Y, H:i').' WIB.',
            ];
        }

        $allAssignments = LmsAssignment::where('course_class_id', $courseClass->id)
            ->where('is_published', true)
            ->orderBy('id', 'asc')
            ->get();

        $blockingPrerequisite = null;

        foreach ($allAssignments as $item) {
            if ($item->id === $assignment->id) {
                if ($blockingPrerequisite) {
                    return [
                        'allowed' => false,
                        'prerequisite' => $blockingPrerequisite,
                        'message' => "Tugas & PR '{$assignment->title}' masih terkunci. Anda harus menyelesaikan dan mengumpulkan tugas '{$blockingPrerequisite->title}' terlebih dahulu.",
                    ];
                }

                return ['allowed' => true, 'prerequisite' => null, 'message' => null];
            }

            $isCompleted = $item->submissions()
                ->where('student_id', $student->id)
                ->exists();

            if (! $isCompleted && ! $blockingPrerequisite) {
                $blockingPrerequisite = $item;
            }
        }

        return ['allowed' => true, 'prerequisite' => null, 'message' => null];
    }

    /**
     * Memeriksa apakah siswa diizinkan mengakses asesmen/kuis CBT tertentu secara berurutan.
     *
     * @return array{allowed: bool, prerequisite: ?LmsQuiz, message: ?string}
     */
    public static function checkQuizAccess(CourseClass $courseClass, LmsQuiz $quiz, ?Student $student): array
    {
        if (! $student) {
            return ['allowed' => true, 'prerequisite' => null, 'message' => null];
        }

        $allQuizzes = LmsQuiz::where('course_class_id', $courseClass->id)
            ->where('is_published', true)
            ->orderBy('id', 'asc')
            ->get();

        $blockingPrerequisite = null;

        foreach ($allQuizzes as $item) {
            if ($item->id === $quiz->id) {
                if ($blockingPrerequisite) {
                    return [
                        'allowed' => false,
                        'prerequisite' => $blockingPrerequisite,
                        'message' => "Asesmen & CBT '{$quiz->title}' masih terkunci. Anda harus menyelesaikan asesmen '{$blockingPrerequisite->title}' terlebih dahulu.",
                    ];
                }

                return ['allowed' => true, 'prerequisite' => null, 'message' => null];
            }

            $isCompleted = $item->attempts()
                ->where('student_id', $student->id)
                ->whereNotNull('completed_at')
                ->exists();

            if (! $isCompleted && ! $blockingPrerequisite) {
                $blockingPrerequisite = $item;
            }
        }

        return ['allowed' => true, 'prerequisite' => null, 'message' => null];
    }

    /**
     * Menyematkan metadata progres berurutan (sequence, unlocked, completed, prerequisite) ke koleksi materi.
     *
     * @param  iterable<LmsMaterial>  $materials
     */
    public static function decorateMaterialsWithProgression(iterable $materials, ?Student $student): Collection
    {
        $collection = collect($materials);
        $blockingPrerequisite = null;
        $seq = 1;

        foreach ($collection as $mat) {
            $mat->sequence_num = $seq++;

            if (! $student) {
                $mat->is_unlocked = true;
                $mat->is_completed = false;
                $mat->prerequisite_title = null;

                continue;
            }

            $isCompleted = $mat->reads->where('student_id', $student->id)->isNotEmpty();
            $mat->is_completed = $isCompleted;

            $isTimeLocked = $mat->start_time && now()->isBefore($mat->start_time);
            $mat->is_time_locked = $isTimeLocked;

            if ($blockingPrerequisite) {
                $mat->is_unlocked = false;
                $mat->prerequisite_title = $blockingPrerequisite->title;
            } elseif ($isTimeLocked) {
                $mat->is_unlocked = false;
                $mat->prerequisite_title = null;
                $mat->time_lock_message = 'Materi baru dapat dibuka pada '.$mat->formattedStartTime().' WIB';
            } else {
                $mat->is_unlocked = true;
                $mat->prerequisite_title = null;

                if (! $isCompleted) {
                    $blockingPrerequisite = $mat;
                }
            }
        }

        return $collection;
    }

    /**
     * Menyematkan metadata progres berurutan ke koleksi tugas & PR.
     *
     * @param  iterable<LmsAssignment>  $assignments
     */
    public static function decorateAssignmentsWithProgression(iterable $assignments, ?Student $student): Collection
    {
        $collection = collect($assignments);
        $blockingPrerequisite = null;
        $seq = 1;

        foreach ($collection as $asg) {
            $asg->sequence_num = $seq++;

            if (! $student) {
                $asg->is_unlocked = true;
                $asg->is_completed = false;
                $asg->prerequisite_title = null;

                continue;
            }

            $isCompleted = $asg->submissions->where('student_id', $student->id)->isNotEmpty();
            $asg->is_completed = $isCompleted;

            $isTimeLocked = $asg->start_time && now()->isBefore($asg->start_time);
            $asg->is_time_locked = $isTimeLocked;

            if ($blockingPrerequisite) {
                $asg->is_unlocked = false;
                $asg->prerequisite_title = $blockingPrerequisite->title;
            } elseif ($isTimeLocked) {
                $asg->is_unlocked = false;
                $asg->prerequisite_title = null;
                $asg->time_lock_message = 'Tugas baru dapat dibuka pada '.$asg->formattedStartTime().' WIB';
            } else {
                $asg->is_unlocked = true;
                $asg->prerequisite_title = null;

                if (! $isCompleted) {
                    $blockingPrerequisite = $asg;
                }
            }
        }

        return $collection;
    }

    /**
     * Menyematkan metadata progres berurutan ke koleksi Asesmen & CBT.
     *
     * @param  iterable<LmsQuiz>  $quizzes
     */
    public static function decorateQuizzesWithProgression(iterable $quizzes, ?Student $student): Collection
    {
        $collection = collect($quizzes);
        $blockingPrerequisite = null;
        $seq = 1;

        foreach ($collection as $quiz) {
            $quiz->sequence_num = $seq++;

            if (! $student) {
                $quiz->is_unlocked = true;
                $quiz->is_completed = false;
                $quiz->prerequisite_title = null;

                continue;
            }

            $isCompleted = $quiz->attempts->where('student_id', $student->id)->whereNotNull('completed_at')->isNotEmpty();
            $quiz->is_completed = $isCompleted;

            if ($blockingPrerequisite) {
                $quiz->is_unlocked = false;
                $quiz->prerequisite_title = $blockingPrerequisite->title;
            } else {
                $quiz->is_unlocked = true;
                $quiz->prerequisite_title = null;

                if (! $isCompleted) {
                    $blockingPrerequisite = $quiz;
                }
            }
        }

        return $collection;
    }

    /**
     * Dapatkan materi berikutnya yang berurutan setelah materi saat ini.
     */
    public static function getNextMaterial(CourseClass $courseClass, LmsMaterial $currentMaterial, ?Student $student): ?LmsMaterial
    {
        $allMaterials = LmsMaterial::where('course_class_id', $courseClass->id)
            ->where('is_published', true)
            ->orderBy('id', 'asc')
            ->get();

        $foundCurrent = false;
        foreach ($allMaterials as $item) {
            if ($foundCurrent) {
                return $item;
            }
            if ($item->id === $currentMaterial->id) {
                $foundCurrent = true;
            }
        }

        return null;
    }

    /**
     * Dapatkan tugas berikutnya yang berurutan setelah tugas saat ini.
     */
    public static function getNextAssignment(CourseClass $courseClass, LmsAssignment $currentAssignment, ?Student $student): ?LmsAssignment
    {
        $allAssignments = LmsAssignment::where('course_class_id', $courseClass->id)
            ->where('is_published', true)
            ->orderBy('id', 'asc')
            ->get();

        $foundCurrent = false;
        foreach ($allAssignments as $item) {
            if ($foundCurrent) {
                return $item;
            }
            if ($item->id === $currentAssignment->id) {
                $foundCurrent = true;
            }
        }

        return null;
    }

    /**
     * Dapatkan kuis berikutnya yang berurutan setelah kuis saat ini.
     */
    public static function getNextQuiz(CourseClass $courseClass, LmsQuiz $currentQuiz, ?Student $student): ?LmsQuiz
    {
        $allQuizzes = LmsQuiz::where('course_class_id', $courseClass->id)
            ->where('is_published', true)
            ->orderBy('id', 'asc')
            ->get();

        $foundCurrent = false;
        foreach ($allQuizzes as $item) {
            if ($foundCurrent) {
                return $item;
            }
            if ($item->id === $currentQuiz->id) {
                $foundCurrent = true;
            }
        }

        return null;
    }
}

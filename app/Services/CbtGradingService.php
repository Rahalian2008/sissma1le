<?php

namespace App\Services;

use App\Models\CbtAnswer;
use App\Models\CbtSession;

class CbtGradingService
{
    /**
     * Otomatis mengoreksi dan memberikan skor pada jawaban butir soal.
     */
    public function gradeAnswer(CbtAnswer $answer): void
    {
        $question = $answer->question;
        if (! $question) {
            return;
        }

        $userAns = $answer->answer;
        $correctAns = $question->correct_answer;
        $maxScore = (float) $question->score_weight;

        $isCorrect = false;
        $earnedScore = 0.0;

        switch ($question->question_type) {
            case 'multiple_choice':
                // Single string comparison (e.g. 'A')
                $userVal = is_string($userAns) ? trim($userAns) : (is_array($userAns) ? reset($userAns) : '');
                $keyVal = is_string($correctAns) ? trim($correctAns) : (is_array($correctAns) ? reset($correctAns) : '');
                if (strcasecmp((string) $userVal, (string) $keyVal) === 0 && ! empty($userVal)) {
                    $isCorrect = true;
                    $earnedScore = $maxScore;
                }
                break;

            case 'complex_multiple_choice':
                // Multi-select array comparison (e.g. ['A', 'C'])
                $userArr = is_array($userAns) ? array_map('strval', $userAns) : (empty($userAns) ? [] : [strval($userAns)]);
                $keyArr = is_array($correctAns) ? array_map('strval', $correctAns) : (empty($correctAns) ? [] : [strval($correctAns)]);

                sort($userArr);
                sort($keyArr);

                if (! empty($keyArr) && $userArr === $keyArr) {
                    $isCorrect = true;
                    $earnedScore = $maxScore;
                } elseif (! empty($keyArr) && ! empty($userArr)) {
                    // Partial scoring: hitung irisan jawaban benar dikurangi jawaban salah
                    $correctPicks = count(array_intersect($userArr, $keyArr));
                    $wrongPicks = count(array_diff($userArr, $keyArr));
                    $netRatio = max(0, ($correctPicks - ($wrongPicks * 0.5)) / count($keyArr));
                    $earnedScore = round($netRatio * $maxScore, 2);
                    $isCorrect = $earnedScore >= ($maxScore * 0.5);
                }
                break;

            case 'true_false':
                // Dukung format list of objects: [{'id': 'S1', 'is_true': true}] maupun key-value: ['0' => 'true', '1' => 'false']
                $userMap = [];
                if (is_array($userAns)) {
                    foreach ($userAns as $k => $item) {
                        if (is_array($item) && isset($item['id'])) {
                            $userMap[$item['id']] = filter_var($item['is_true'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        } else {
                            $userMap[$k] = filter_var(is_array($item) ? ($item['is_true'] ?? false) : $item, FILTER_VALIDATE_BOOLEAN);
                        }
                    }
                }

                $keyMap = [];
                if (is_array($correctAns)) {
                    foreach ($correctAns as $k => $item) {
                        if (is_array($item) && isset($item['id'])) {
                            $keyMap[$item['id']] = filter_var($item['is_true'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        } else {
                            $keyMap[$k] = filter_var(is_array($item) ? ($item['is_true'] ?? false) : $item, FILTER_VALIDATE_BOOLEAN);
                        }
                    }
                }

                if (! empty($keyMap)) {
                    $matched = 0;
                    foreach ($keyMap as $sid => $expectedBool) {
                        if (isset($userMap[$sid]) && $userMap[$sid] === $expectedBool) {
                            $matched++;
                        }
                    }
                    $ratio = $matched / count($keyMap);
                    $earnedScore = round($ratio * $maxScore, 2);
                    $isCorrect = $matched === count($keyMap);
                }
                break;

            case 'matching':
                // Matching pairs: [{'premise_id': 1, 'response_id': 'A'}, ...]
                $userPairs = [];
                if (is_array($userAns)) {
                    foreach ($userAns as $p) {
                        if (isset($p['premise_id'])) {
                            $userPairs[$p['premise_id']] = strval($p['response_id'] ?? '');
                        }
                    }
                }

                $keyPairs = [];
                if (is_array($correctAns)) {
                    foreach ($correctAns as $p) {
                        if (isset($p['premise_id'])) {
                            $keyPairs[$p['premise_id']] = strval($p['response_id'] ?? '');
                        }
                    }
                }

                if (! empty($keyPairs)) {
                    $matched = 0;
                    foreach ($keyPairs as $pid => $expectedResp) {
                        if (isset($userPairs[$pid]) && strcasecmp($userPairs[$pid], $expectedResp) === 0 && ! empty($expectedResp)) {
                            $matched++;
                        }
                    }
                    $ratio = $matched / count($keyPairs);
                    $earnedScore = round($ratio * $maxScore, 2);
                    $isCorrect = $matched === count($keyPairs);
                }
                break;

            case 'short_answer':
                // Case-insensitive trimmed string match
                $cleanUser = trim(strtolower(strval(is_array($userAns) ? reset($userAns) : $userAns)));
                $cleanKey = trim(strtolower(strval(is_array($correctAns) ? reset($correctAns) : $correctAns)));

                if (! empty($cleanUser) && ($cleanUser === $cleanKey || str_contains($cleanKey, $cleanUser))) {
                    $isCorrect = true;
                    $earnedScore = $maxScore;
                }
                break;

            case 'essay':
            default:
                // Esai / Uraian butuh koreksi manual guru, biarkan is_correct = null jika belum dikoreksi
                // Jika sudah ada teacher score sebelumnya, pertahankan
                return;
        }

        $answer->update([
            'is_correct' => $isCorrect,
            'score_earned' => $earnedScore,
        ]);
    }

    /**
     * Hitung rekap nilai akhir untuk sesi ujian siswa.
     */
    public function recalculateSession(CbtSession $session): void
    {
        $exam = $session->exam;
        if (! $exam) {
            return;
        }

        $questions = $exam->questions;
        $maxPossibleScore = (float) $questions->sum('score_weight');
        if ($maxPossibleScore <= 0) {
            $maxPossibleScore = max(1.0, (float) $questions->count());
        }

        // Otomatis koreksi seluruh jawaban objektif
        $answers = $session->answers()->with('question')->get();
        foreach ($answers as $ans) {
            if ($ans->question && $ans->question->question_type !== 'essay') {
                $this->gradeAnswer($ans);
            }
        }

        // Reload jawaban setelah dihitung
        $freshAnswers = $session->answers()->get();
        $totalScoreEarned = (float) $freshAnswers->sum('score_earned');

        // Nilai skala 0 - 100
        $finalGrade = round(($totalScoreEarned / $maxPossibleScore) * 100, 2);
        $finalGrade = min(100.0, max(0.0, $finalGrade));

        // Cek apakah semua soal (termasuk essay) sudah dinilai
        $hasUnevaluatedEssay = $questions->where('question_type', 'essay')->count() > 0 &&
            $freshAnswers->where('is_correct', null)->count() > 0;

        $session->update([
            'total_score' => $totalScoreEarned,
            'max_possible_score' => $maxPossibleScore,
            'final_grade' => $finalGrade,
            'is_graded' => ! $hasUnevaluatedEssay,
            'graded_at' => now(),
        ]);
    }
}

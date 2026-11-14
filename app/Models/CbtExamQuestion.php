<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_exam_id',
        'question_bank_id',
        'question_number',
        'question_type',
        'stimulus',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
        'score_weight',
        'learning_objective',
        'indicator',
        'cognitive_level',
        'difficulty_level',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'array',
            'score_weight' => 'decimal:2',
            'question_number' => 'integer',
        ];
    }

    protected $appends = [
        'type_label',
        'cognitive_label',
        'difficulty_label',
        'normalized_options',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CbtAnswer::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->question_type) {
            'complex_multiple_choice' => 'Pilihan Ganda Kompleks',
            'true_false' => 'Benar / Salah',
            'matching' => 'Menjodohkan',
            'short_answer' => 'Isian Singkat',
            'essay' => 'Uraian / Esai',
            default => 'Pilihan Ganda',
        };
    }

    public function getCognitiveLabelAttribute(): string
    {
        return match ($this->cognitive_level) {
            'L1' => 'L1 (Pemahaman / C1-C2)',
            'L3' => 'L3 (Penalaran / HOTS)',
            default => 'L2 (Aplikasi / C3)',
        };
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match (strtolower($this->difficulty_level ?? 'sedang')) {
            'mudah' => 'Mudah',
            'sukar', 'sulit' => 'Sukar',
            default => 'Sedang',
        };
    }

    /**
     * Normalisasi opsi jawaban agar konsisten untuk berbagai format penyimpanan (JSON key-value, list of objects, dsb)
     *
     * @return array<int, array<string, mixed>>
     */
    public function getNormalizedOptionsAttribute(): array
    {
        $opts = $this->options;
        if (empty($opts)) {
            return [];
        }

        if (is_string($opts)) {
            $decoded = json_decode($opts, true);
            $opts = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }

        if (! is_array($opts)) {
            return [];
        }

        $normalized = [];

        if ($this->question_type === 'multiple_choice' || $this->question_type === 'complex_multiple_choice') {
            foreach ($opts as $key => $val) {
                if (is_array($val)) {
                    $k = $val['key'] ?? (is_numeric($key) ? chr(65 + (int) $key) : $key);
                    $t = $val['text'] ?? ($val['label'] ?? json_encode($val));
                } else {
                    $k = is_numeric($key) ? chr(65 + (int) $key) : $key;
                    $t = strval($val);
                }
                $normalized[] = ['key' => strval($k), 'text' => $t];
            }

            return $normalized;
        }

        if ($this->question_type === 'true_false') {
            foreach ($opts as $key => $val) {
                if (is_array($val)) {
                    $st = $val['statement'] ?? ($val['text'] ?? json_encode($val));
                } else {
                    $st = strval($val);
                }
                $normalized[] = ['statement' => $st];
            }

            return $normalized;
        }

        if ($this->question_type === 'matching') {
            if (isset($opts['premises']) && is_array($opts['premises'])) {
                foreach ($opts['premises'] as $i => $prem) {
                    $p = is_array($prem) ? ($prem['text'] ?? json_encode($prem)) : strval($prem);
                    $r = isset($opts['responses'][$i]) ? (is_array($opts['responses'][$i]) ? ($opts['responses'][$i]['text'] ?? json_encode($opts['responses'][$i])) : strval($opts['responses'][$i])) : '';
                    $normalized[] = ['premise' => $p, 'response' => $r];
                }

                return $normalized;
            }

            foreach ($opts as $val) {
                if (is_array($val)) {
                    $p = $val['premise'] ?? ($val['left'] ?? json_encode($val));
                    $r = $val['response'] ?? ($val['right'] ?? '');
                } else {
                    $p = strval($val);
                    $r = '';
                }
                $normalized[] = ['premise' => $p, 'response' => $r];
            }

            return $normalized;
        }

        return $opts;
    }
}

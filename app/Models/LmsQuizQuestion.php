<?php

namespace App\Models;

use App\Services\LmsQuranAyahService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'learning_objective_id',
        'question_text',
        'stimulus',
        'question_type',
        'indikator_soal',
        'level_kognitif',
        'materi_pokok',
        'options',
        'correct_answer',
        'pembahasan',
        'score_weight',
        'order_number',
    ];

    public function getTypeLabelAttribute(): string
    {
        return match ($this->question_type) {
            'multiple_choice' => 'Pilihan Ganda',
            'complex_multiple_choice', 'complex_mc' => 'Pilihan Ganda Kompleks',
            'true_false' => 'Benar / Salah',
            'matching' => 'Menjodohkan',
            'short_answer' => 'Isian Singkat',
            'essay', 'uraian' => 'Uraian / Esai',
            default => ucwords(str_replace('_', ' ', $this->question_type)),
        };
    }

    public function setCorrectAnswerAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['correct_answer'] = json_encode(array_values($value));
        } else {
            $this->attributes['correct_answer'] = $value;
        }
    }

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'score_weight' => 'integer',
            'order_number' => 'integer',
        ];
    }

    public function getFormattedQuestionTextAttribute(): string
    {
        return static::renderRichText($this->question_text);
    }

    public static function renderRichText(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Resolusi tag shortcode [AYAH: surah:ayah] jika guru mengetikkannya secara manual
        if (preg_match_all('/\[AYAH:\s*(\d+):([0-9\-]+)\]/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $surahNum = (int) $match[1];
                $ayahNum = $match[2];
                $ayahData = LmsQuranAyahService::getAyahText($surahNum, $ayahNum);
                $quranHtml = '<div class="quran-quote-box p-4 my-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-slate-800 dark:text-slate-100" dir="rtl">'
                    .'<div class="font-arabic text-xl sm:text-2xl leading-loose font-bold text-emerald-950 dark:text-emerald-100 mb-2">'.e($ayahData['arabic']).'</div>'
                    .'<div class="text-xs text-slate-600 dark:text-slate-300 font-sans italic" dir="ltr"><strong>Artinya:</strong> "'.e($ayahData['translation']).'" ('.e($ayahData['reference']).')</div>'
                    .'</div>';
                $text = str_replace($match[0], $quranHtml, $text);
            }
        }

        // Format Markdown Images ![Caption](url)
        if (preg_match_all('/!\[(.*?)\]\((.*?)\)/i', $text, $imgMatches, PREG_SET_ORDER)) {
            foreach ($imgMatches as $im) {
                $caption = trim($im[1]);
                $src = trim($im[2]);
                $captionHtml = ! empty($caption) ? '<p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 italic font-medium">'.e($caption).'</p>' : '';
                $imgHtml = '<div class="my-3 text-center">'
                    .'<img src="'.e($src).'" alt="'.e($caption ?: 'Gambar Soal').'" class="mx-auto rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm max-h-[420px] max-w-full" style="object-fit: contain;">'
                    .$captionHtml
                    .'</div>';
                $text = str_replace($im[0], $imgHtml, $text);
            }
        }

        // Format Markdown Bold **text**
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);

        // Format Markdown Italic *text* (hindari salah match pada **)
        $text = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $text);

        // Jika sudah mengandung tag HTML aman (img, div, b, i, u, sup, sub, p, span, table, blockquote, strong, em)
        if (preg_match('/<(?:img|div|b|i|u|sup|sub|p|span|table|h[1-6]|ul|ol|li|blockquote|strong|em)\b/i', $text)) {
            return $text;
        }

        return nl2br(e($text));
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LmsQuiz::class);
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }
}

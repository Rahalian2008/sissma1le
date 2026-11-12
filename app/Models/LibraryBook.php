<?php

namespace App\Models;

use App\Services\BarcodeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_code',
        'title',
        'slug',
        'author',
        'publisher',
        'publish_year',
        'isbn',
        'category_id',
        'ddc_code',
        'call_number',
        'rack_location',
        'total_copies',
        'available_copies',
        'book_type',
        'cover_image',
        'description',
        'pages_count',
        'language',
        'source',
        'digital_file_path',
        'file_path',
        'is_ebook',
        'is_digital_flip',
        'digital_content',
        'views_count',
        'loans_count',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::saving(function (LibraryBook $book) {
            if (empty($book->slug)) {
                $book->slug = static::generateUniqueSlug($book->title ?? $book->book_code ?? 'buku', $book->id);
            }
            if ($book->pages_count === null) {
                $book->pages_count = 0;
            }
            if (array_key_exists('page_count', $book->attributes) && $book->attributes['page_count'] === null) {
                $book->attributes['page_count'] = $book->pages_count ?? 0;
            }
            if (array_key_exists('category_name', $book->attributes) && empty($book->attributes['category_name'])) {
                $book->attributes['category_name'] = 'Umum';
            }
            if (array_key_exists('collection_type', $book->attributes) && empty($book->attributes['collection_type'])) {
                $book->attributes['collection_type'] = $book->book_type === 'digital' ? 'digital' : ($book->book_type === 'fisik' ? 'physical' : 'both');
            }
            if (array_key_exists('curriculum', $book->attributes) && empty($book->attributes['curriculum'])) {
                $book->attributes['curriculum'] = 'Kurikulum Merdeka';
            }
            if (array_key_exists('language', $book->attributes) && empty($book->attributes['language'])) {
                $book->attributes['language'] = 'Indonesia';
            }
        });
    }

    /**
     * Hasilkan slug unik untuk buku
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        if (empty($baseSlug)) {
            $baseSlug = 'buku';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function casts(): array
    {
        return [
            'publish_year' => 'integer',
            'total_copies' => 'integer',
            'available_copies' => 'integer',
            'pages_count' => 'integer',
            'is_ebook' => 'boolean',
            'is_digital_flip' => 'boolean',
            'digital_content' => 'array',
            'views_count' => 'integer',
            'loans_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(LibraryLoan::class, 'book_id');
    }

    public function activeLoans(): HasMany
    {
        return $this->hasMany(LibraryLoan::class, 'book_id')->whereIn('status', ['DIPINJAM', 'TERLAMBAT']);
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image && (Storage::disk('public')->exists($this->cover_image) || file_exists(public_path('storage/'.$this->cover_image)))) {
            return asset('storage/'.$this->cover_image);
        }

        return asset('images/default-book-cover.svg');
    }

    public function getDigitalFileUrlAttribute(): ?string
    {
        $path = $this->digital_file_path ?: ($this->file_path ?? null);
        if ($path && (Storage::disk('public')->exists($path) || file_exists(public_path('storage/'.$path)))) {
            return asset('storage/'.$path);
        }

        return null;
    }

    public function getFilePathAttribute(): ?string
    {
        return $this->attributes['file_path'] ?? ($this->attributes['digital_file_path'] ?? null);
    }

    public function getIsEbookAttribute(): bool
    {
        if (isset($this->attributes['is_ebook'])) {
            return (bool) $this->attributes['is_ebook'];
        }

        return ($this->attributes['book_type'] ?? '') === 'digital' || ! empty($this->attributes['is_digital_flip']);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->book_type === 'digital' || $this->available_copies > 0;
    }

    /**
     * Mengambil 3 huruf kapital pertama tajuk pengarang (Aturan Cutter / Perpusnas)
     */
    public function getAuthorCutterCodeAttribute(): string
    {
        $author = trim(preg_replace('/[^a-zA-Z\s]/', '', $this->author ?? ''));
        if ($author === '') {
            $fallback = preg_replace('/[^a-zA-Z]/', '', $this->title ?? 'AAA');

            return strtoupper(substr($fallback, 0, 3) ?: 'AAA');
        }

        $words = array_values(array_filter(explode(' ', $author)));
        if (count($words) === 1) {
            return strtoupper(str_pad(substr($words[0], 0, 3), 3, 'X'));
        }

        // Aturan tajuk nama pengarang: Ambil nama keluarga/belakang jika >= 3 huruf, atau kata pertama
        $lastWord = end($words);
        if (strlen($lastWord) >= 3) {
            return strtoupper(substr($lastWord, 0, 3));
        }

        return strtoupper(str_pad(substr($words[0], 0, 3), 3, 'X'));
    }

    /**
     * Mengambil 1 huruf kecil pertama dari judul (Abaikan kata sandang The, A, An, Si, Sang)
     */
    public function getTitleFirstLetterAttribute(): string
    {
        $title = trim($this->title ?? '');
        // Hapus kata sandang di awal judul
        $clean = preg_replace('/^(the|a|an|si|sang)\s+/i', '', $title);
        $clean = preg_replace('/[^a-zA-Z]/', '', $clean);

        return strtolower(substr($clean, 0, 1) ?: 'a');
    }

    /**
     * Memecah elemen Call Number sesuai standar label punggung buku Perpusnas (3-4 baris)
     */
    public function getCallNumberPartsAttribute(): array
    {
        $ddc = $this->ddc_code ?: ($this->category?->ddc_code ?: '000');
        $authorCode = $this->author_cutter_code;
        $titleLetter = $this->title_first_letter;

        if (! empty($this->call_number)) {
            $tokens = preg_split('/\s+/', trim($this->call_number));
            if (count($tokens) >= 3) {
                return [
                    'ddc' => $tokens[0],
                    'author' => strtoupper($tokens[1]),
                    'title_letter' => strtolower($tokens[2]),
                    'copy' => $tokens[3] ?? 'c.1',
                ];
            }
        }

        return [
            'ddc' => $ddc,
            'author' => $authorCode,
            'title_letter' => $titleLetter,
            'copy' => 'c.1',
        ];
    }

    /**
     * Format otomatis Nomor Panggil (Call Number)
     */
    public static function formatCallNumber(string $ddc, string $author, string $title, ?string $copy = 'c.1'): string
    {
        $cleanAuthor = trim(preg_replace('/[^a-zA-Z\s]/', '', $author));
        $words = array_values(array_filter(explode(' ', $cleanAuthor)));
        $authorCode = 'AAA';
        if (! empty($words)) {
            $lastWord = end($words);
            $authorCode = strtoupper(str_pad(substr(strlen($lastWord) >= 3 ? $lastWord : $words[0], 0, 3), 3, 'X'));
        }

        $cleanTitle = preg_replace('/^(the|a|an|si|sang)\s+/i', '', trim($title));
        $cleanTitle = preg_replace('/[^a-zA-Z]/', '', $cleanTitle);
        $titleLetter = strtolower(substr($cleanTitle, 0, 1) ?: 'a');

        $result = trim($ddc).' '.$authorCode.' '.$titleLetter;
        if (! empty($copy)) {
            $result .= ' '.trim($copy);
        }

        return $result;
    }

    /**
     * Barcode SVG Standar Code 128
     */
    public function getBarcodeSvgAttribute(): string
    {
        $code = $this->book_code ?: 'LIB-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);

        return BarcodeService::generateCode128Svg($code, 38, 1.3);
    }
}

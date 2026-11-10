<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'grade',
        'group',
        'kkm',
        'color',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'kkm' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    /**
     * Daftar Master Mata Pelajaran SMA sesuai Permendikdasmen No. 13 Tahun 2025
     *
     * @return array<int, array{code: string, name: string, grade: string, group: string, kkm: int, color: string, icon: string}>
     */
    public static function getPermendikdasmen13Subjects(): array
    {
        return [
            // KELOMPOK 1: MATA PELAJARAN WAJIB (UMUM)
            [
                'code' => 'PAI-SMA',
                'name' => 'Pendidikan Agama Islam dan Budi Pekerti',
                'grade' => 'Semua Tingkat',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'emerald',
                'icon' => 'book',
            ],
            [
                'code' => 'PPN-SMA',
                'name' => 'Pendidikan Pancasila',
                'grade' => 'Semua Tingkat',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'rose',
                'icon' => 'shield',
            ],
            [
                'code' => 'IND-SMA',
                'name' => 'Bahasa Indonesia',
                'grade' => 'Semua Tingkat',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'blue',
                'icon' => 'book-open',
            ],
            [
                'code' => 'MAT-SMA',
                'name' => 'Matematika',
                'grade' => 'Semua Tingkat',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'indigo',
                'icon' => 'calculator',
            ],
            [
                'code' => 'ING-SMA',
                'name' => 'Bahasa Inggris',
                'grade' => 'Semua Tingkat',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'sky',
                'icon' => 'globe',
            ],
            [
                'code' => 'PJK-SMA',
                'name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)',
                'grade' => 'Semua Tingkat',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'orange',
                'icon' => 'activity',
            ],
            [
                'code' => 'SEJ-SMA',
                'name' => 'Sejarah',
                'grade' => 'Semua Tingkat',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'amber',
                'icon' => 'clock',
            ],
            [
                'code' => 'SNB-SMA',
                'name' => 'Seni dan Budaya',
                'grade' => 'Semua Tingkat',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'purple',
                'icon' => 'palette',
            ],
            [
                'code' => 'INF-SMA',
                'name' => 'Informatika',
                'grade' => 'Fase E (Kelas X)',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'cyan',
                'icon' => 'cpu',
            ],
            [
                'code' => 'IPA-SMA',
                'name' => 'Ilmu Pengetahuan Alam (IPA Terintegrasi)',
                'grade' => 'Fase E (Kelas X)',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'teal',
                'icon' => 'beaker',
            ],
            [
                'code' => 'IPS-SMA',
                'name' => 'Ilmu Pengetahuan Sosial (IPS Terintegrasi)',
                'grade' => 'Fase E (Kelas X)',
                'group' => 'Mata Pelajaran Wajib',
                'kkm' => 75,
                'color' => 'rose',
                'icon' => 'users',
            ],

            // KELOMPOK 2: MATA PELAJARAN PILIHAN (FASE F KELAS XI & XII)
            [
                'code' => 'FIS-SMA',
                'name' => 'Fisika',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'sky',
                'icon' => 'atom',
            ],
            [
                'code' => 'KIM-SMA',
                'name' => 'Kimia',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'violet',
                'icon' => 'flask',
            ],
            [
                'code' => 'BIO-SMA',
                'name' => 'Biologi',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'emerald',
                'icon' => 'dna',
            ],
            [
                'code' => 'INFL-SMA',
                'name' => 'Informatika Tingkat Lanjut',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'blue',
                'icon' => 'terminal',
            ],
            [
                'code' => 'MATL-SMA',
                'name' => 'Matematika Tingkat Lanjut',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'indigo',
                'icon' => 'function',
            ],
            [
                'code' => 'SOS-SMA',
                'name' => 'Sosiologi',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'amber',
                'icon' => 'users',
            ],
            [
                'code' => 'EKO-SMA',
                'name' => 'Ekonomi',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'green',
                'icon' => 'trending-up',
            ],
            [
                'code' => 'GEO-SMA',
                'name' => 'Geografi',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'teal',
                'icon' => 'map',
            ],
            [
                'code' => 'ANT-SMA',
                'name' => 'Antropologi',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'yellow',
                'icon' => 'globe',
            ],
            [
                'code' => 'INDL-SMA',
                'name' => 'Bahasa Indonesia Tingkat Lanjut',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'rose',
                'icon' => 'feather',
            ],
            [
                'code' => 'INGL-SMA',
                'name' => 'Bahasa Inggris Tingkat Lanjut',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'cyan',
                'icon' => 'message-circle',
            ],
            [
                'code' => 'BAS-SMA',
                'name' => 'Bahasa Asing Pilihan (Jepang / Arab / Jerman / Mandarin)',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'fuchsia',
                'icon' => 'translate',
            ],
            [
                'code' => 'PKW-SMA',
                'name' => 'Prakarya dan Kewirausahaan (PKWU)',
                'grade' => 'Fase F (Kelas XI-XII)',
                'group' => 'Mata Pelajaran Pilihan',
                'kkm' => 75,
                'color' => 'amber',
                'icon' => 'briefcase',
            ],

            // KELOMPOK 3: MUATAN LOKAL
            [
                'code' => 'SND-SMA',
                'name' => 'Bahasa dan Sastra Sunda',
                'grade' => 'Semua Tingkat',
                'group' => 'Muatan Lokal',
                'kkm' => 75,
                'color' => 'emerald',
                'icon' => 'feather',
            ],
            [
                'code' => 'PLH-SMA',
                'name' => 'Pendidikan Lingkungan Hidup (PLH)',
                'grade' => 'Semua Tingkat',
                'group' => 'Muatan Lokal',
                'kkm' => 75,
                'color' => 'teal',
                'icon' => 'leaf',
            ],
        ];
    }

    /**
     * Generate / sinkronisasi seluruh mata pelajaran Permendikdasmen No. 13 Tahun 2025 ke database
     *
     * @return int Jumlah mata pelajaran yang tersimpan
     */
    public static function seedPermendikdasmenSubjects(): int
    {
        $subjects = self::getPermendikdasmen13Subjects();
        foreach ($subjects as $sData) {
            self::updateOrCreate(
                ['code' => $sData['code']],
                array_merge($sData, ['is_active' => true])
            );
        }

        return count($subjects);
    }
}

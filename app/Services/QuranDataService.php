<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuranDataService
{
    /**
     * Daftar lengkap 114 Surat Al-Qur'an (Surah 1 s.d. 114)
     *
     * @return array<int, array{number: int, name: string, arabic: string, translation: string, ayah_count: int, type: string, juz: int, audio_url: string, audio_fallback: string}>
     */
    public static function getAllSurahs(): array
    {
        $raw = [
            1 => ['Al-Fatihah', 'الفاتحة', 'Pembukaan', 7, 'Makkiyah', 1],
            2 => ['Al-Baqarah', 'البقرة', 'Sapi Betina', 286, 'Madaniyah', 1],
            3 => ["Ali 'Imran", 'آل عمران', 'Keluarga Imran', 200, 'Madaniyah', 3],
            4 => ["An-Nisa'", 'النساء', 'Wanita', 176, 'Madaniyah', 4],
            5 => ["Al-Ma'idah", 'المائدة', 'Jamuan Hidangan', 120, 'Madaniyah', 6],
            6 => ["Al-An'am", 'الأنعام', 'Hewan Ternak', 165, 'Makkiyah', 7],
            7 => ["Al-A'raf", 'الأعراف', 'Tempat-Tempat Tertinggi', 206, 'Makkiyah', 8],
            8 => ['Al-Anfal', 'الأنفال', 'Rampasan Perang', 75, 'Madaniyah', 9],
            9 => ['At-Taubah', 'التوبة', 'Pengampunan', 129, 'Madaniyah', 10],
            10 => ['Yunus', 'يونس', 'Nabi Yunus', 109, 'Makkiyah', 11],
            11 => ['Hud', 'هود', 'Nabi Hud', 123, 'Makkiyah', 11],
            12 => ['Yusuf', 'يوسف', 'Nabi Yusuf', 111, 'Makkiyah', 12],
            13 => ["Ar-Ra'd", 'الرعد', 'Guruh / Petir', 43, 'Madaniyah', 13],
            14 => ['Ibrahim', 'إبراهيم', 'Nabi Ibrahim', 52, 'Makkiyah', 13],
            15 => ['Al-Hijr', 'الحجر', 'Bukit Hijr', 99, 'Makkiyah', 14],
            16 => ['An-Nahl', 'النحل', 'Lebah', 128, 'Makkiyah', 14],
            17 => ['Al-Isra\'', 'الإسراء', 'Memperjalankan Malam Hari', 111, 'Makkiyah', 15],
            18 => ['Al-Kahf', 'الكهف', 'Gua', 110, 'Makkiyah', 15],
            19 => ['Maryam', 'مريم', 'Siti Maryam', 98, 'Makkiyah', 16],
            20 => ['Taha', 'طه', 'Thaha', 135, 'Makkiyah', 16],
            21 => ['Al-Anbiya\'', 'الأنبياء', 'Para Nabi', 112, 'Makkiyah', 17],
            22 => ['Al-Hajj', 'الحج', 'Haji', 78, 'Madaniyah', 17],
            23 => ['Al-Mu\'minun', 'المؤمنون', 'Orang-Orang Mukmin', 118, 'Makkiyah', 18],
            24 => ['An-Nur', 'النور', 'Cahaya', 64, 'Madaniyah', 18],
            25 => ['Al-Furqan', 'الفرقان', 'Pembeda Benar dan Salah', 77, 'Makkiyah', 18],
            26 => ["Asy-Syu'ara'", 'الشعراء', 'Para Penyair', 227, 'Makkiyah', 19],
            27 => ['An-Naml', 'النمل', 'Semut', 93, 'Makkiyah', 19],
            28 => ['Al-Qasas', 'القصص', 'Kisah-Kisah', 88, 'Makkiyah', 20],
            29 => ["Al-'Ankabut", 'العنكبوت', 'Laba-Laba', 69, 'Makkiyah', 20],
            30 => ['Ar-Rum', 'الروم', 'Bangsa Romawi', 60, 'Makkiyah', 21],
            31 => ['Luqman', 'لقمان', 'Keluarga Luqman', 34, 'Makkiyah', 21],
            32 => ['As-Sajdah', 'السجدة', 'Sujud', 30, 'Makkiyah', 21],
            33 => ['Al-Ahzab', 'الأحزاب', 'Golongan Bersekutu', 73, 'Madaniyah', 21],
            34 => ["Saba'", 'سبأ', 'Kaum Saba', 54, 'Makkiyah', 22],
            35 => ['Fatir', 'فاطر', 'Pencipta', 45, 'Makkiyah', 22],
            36 => ['Yasin', 'يس', 'Yasin', 83, 'Makkiyah', 22],
            37 => ['As-Saffat', 'الصافات', 'Barisan-Barisan', 182, 'Makkiyah', 23],
            38 => ['Sad', 'ص', 'Shad', 88, 'Makkiyah', 23],
            39 => ['Az-Zumar', 'الزمر', 'Rombongan', 75, 'Makkiyah', 23],
            40 => ['Ghafir', 'غافر', 'Maha Pengampun', 85, 'Makkiyah', 24],
            41 => ['Fussilat', 'فصلت', 'Dijelaskan Secara Terperinci', 54, 'Makkiyah', 24],
            42 => ['Asy-Syura', 'الشورى', 'Musyawarah', 53, 'Makkiyah', 25],
            43 => ['Az-Zukhruf', 'الزخرف', 'Perhiasan Emas', 89, 'Makkiyah', 25],
            44 => ['Ad-Dukhan', 'الدخان', 'Kabut / Asap', 59, 'Makkiyah', 25],
            45 => ['Al-Jasiyah', 'الجاثية', 'Berlutut', 37, 'Makkiyah', 25],
            46 => ['Al-Ahqaf', 'الأحقاف', 'Bukit-Bukit Pasir', 35, 'Makkiyah', 26],
            47 => ['Muhammad', 'محمد', 'Nabi Muhammad SAW', 38, 'Madaniyah', 26],
            48 => ['Al-Fath', 'الفتح', 'Kemenangan Nyata', 29, 'Madaniyah', 26],
            49 => ['Al-Hujurat', 'الحجرات', 'Kamar-Kamar', 18, 'Madaniyah', 26],
            50 => ['Qaf', 'ق', 'Qaf', 45, 'Makkiyah', 26],
            51 => ['Az-Zariyat', 'الذاريات', 'Angin yang Menerbangkan', 60, 'Makkiyah', 26],
            52 => ['At-Tur', 'الطور', 'Bukit Tursina', 49, 'Makkiyah', 27],
            53 => ['An-Najm', 'النجم', 'Bintang', 62, 'Makkiyah', 27],
            54 => ['Al-Qamar', 'القمر', 'Bulan', 55, 'Makkiyah', 27],
            55 => ['Ar-Rahman', 'الرحمن', 'Maha Pemurah', 78, 'Madaniyah', 27],
            56 => ["Al-Waqi'ah", 'الواقعة', 'Hari Kiamat', 96, 'Makkiyah', 27],
            57 => ['Al-Hadid', 'الحديد', 'Besi', 29, 'Madaniyah', 27],
            58 => ['Al-Mujadilah', 'المجادلة', 'Gugatan Wanita', 22, 'Madaniyah', 28],
            59 => ['Al-Hasyr', 'الحشر', 'Pengusiran', 24, 'Madaniyah', 28],
            60 => ['Al-Mumtahanah', 'الممتحنة', 'Wanita yang Diuji', 13, 'Madaniyah', 28],
            61 => ['As-Saff', 'الصف', 'Satu Barisan', 14, 'Madaniyah', 28],
            62 => ["Al-Jumu'ah", 'الجمعة', 'Hari Jumat', 11, 'Madaniyah', 28],
            63 => ['Al-Munafiqun', 'المنافقون', 'Orang-Orang Munafik', 11, 'Madaniyah', 28],
            64 => ['At-Tagabun', 'التغابن', 'Hari Dinampakkan Kesalahan', 18, 'Madaniyah', 28],
            65 => ['At-Talaq', 'الطلاق', 'Perceraian', 12, 'Madaniyah', 28],
            66 => ['At-Tahrim', 'التحريم', 'Pengharaman', 12, 'Madaniyah', 28],
            67 => ['Al-Mulk', 'الملك', 'Kerajaan / Kekuasaan', 30, 'Makkiyah', 29],
            68 => ['Al-Qalam', 'القلم', 'Pena', 52, 'Makkiyah', 29],
            69 => ['Al-Haqqah', 'الحاقة', 'Hari Kiamat Nyata', 52, 'Makkiyah', 29],
            70 => ["Al-Ma'arij", 'المعارج', 'Tempat-Tempat Naik', 44, 'Makkiyah', 29],
            71 => ['Nuh', 'نوح', 'Nabi Nuh', 28, 'Makkiyah', 29],
            72 => ['Al-Jinn', 'الجن', 'Jin', 28, 'Makkiyah', 29],
            73 => ['Al-Muzzammil', 'المزمل', 'Orang yang Berselimut', 20, 'Makkiyah', 29],
            74 => ['Al-Muddassir', 'المدثر', 'Orang yang Berkemul', 56, 'Makkiyah', 29],
            75 => ['Al-Qiyamah', 'القيامة', 'Hari Berbangkit', 40, 'Makkiyah', 29],
            76 => ['Al-Insan', 'الإنسان', 'Manusia', 31, 'Madaniyah', 29],
            77 => ['Al-Mursalat', 'المرسلات', 'Malaikat yang Diutus', 50, 'Makkiyah', 29],
            78 => ["An-Naba'", 'النبأ', 'Berita Besar', 40, 'Makkiyah', 30],
            79 => ["An-Nazi'at", 'النازعات', 'Malaikat yang Mencabut', 46, 'Makkiyah', 30],
            80 => ["'Abasa", 'عبس', 'Bermuka Masam', 42, 'Makkiyah', 30],
            81 => ['At-Takwir', 'التكوير', 'Menggulung Matahari', 29, 'Makkiyah', 30],
            82 => ['Al-Infitar', 'الانفطار', 'Terbelah', 19, 'Makkiyah', 30],
            83 => ['Al-Mutaffifin', 'المطففين', 'Orang Curang', 36, 'Makkiyah', 30],
            84 => ['Al-Insyiqaq', 'الانشقاق', 'Terbelah Dua', 25, 'Makkiyah', 30],
            85 => ['Al-Buruj', 'البروج', 'Gugusan Bintang', 22, 'Makkiyah', 30],
            86 => ['At-Tariq', 'الطارق', 'Yang Datang di Malam Hari', 17, 'Makkiyah', 30],
            87 => ["Al-A'la", 'الأعلى', 'Maha Tinggi', 19, 'Makkiyah', 30],
            88 => ['Al-Gasyiyah', 'الغاشية', 'Hari Pembalasan', 26, 'Makkiyah', 30],
            89 => ['Al-Fajr', 'الفجر', 'Waktu Fajar', 30, 'Makkiyah', 30],
            90 => ['Al-Balad', 'البلد', 'Negeri Makkah', 20, 'Makkiyah', 30],
            91 => ['Asy-Syams', 'الشمس', 'Matahari', 15, 'Makkiyah', 30],
            92 => ['Al-Lail', 'الليل', 'Malam', 21, 'Makkiyah', 30],
            93 => ['Ad-Duha', 'الضحى', 'Waktu Dhuha', 11, 'Makkiyah', 30],
            94 => ['Asy-Syarh', 'الشرح', 'Kelapangan Dada', 8, 'Makkiyah', 30],
            95 => ['At-Tin', 'التين', 'Buah Tin', 8, 'Makkiyah', 30],
            96 => ["Al-'Alaq", 'العلق', 'Segumpal Darah', 19, 'Makkiyah', 30],
            97 => ['Al-Qadr', 'القدر', 'Kemuliaan', 5, 'Makkiyah', 30],
            98 => ['Al-Bayyinah', 'البينة', 'Bukti Nyata', 8, 'Madaniyah', 30],
            99 => ['Az-Zalzalah', 'الزلزلة', 'Keguncangan Dahsyat', 8, 'Madaniyah', 30],
            100 => ["Al-'Adiyat", 'العاديات', 'Kuda yang Berlari Kencang', 11, 'Makkiyah', 30],
            101 => ["Al-Qari'ah", 'القارعة', 'Hari Kiamat yang Menggetarkan', 11, 'Makkiyah', 30],
            102 => ['At-Takasur', 'التكاثر', 'Bermegah-Megahan', 8, 'Makkiyah', 30],
            103 => ["Al-'Asr", 'العصر', 'Demi Masa', 3, 'Makkiyah', 30],
            104 => ['Al-Humazah', 'الهمزة', 'Pengumpat', 9, 'Makkiyah', 30],
            105 => ['Al-Fil', 'الفيل', 'Gajah', 5, 'Makkiyah', 30],
            106 => ['Quraisy', 'قريش', 'Suku Quraisy', 4, 'Makkiyah', 30],
            107 => ["Al-Ma'un", 'الماعون', 'Barang Berguna', 7, 'Makkiyah', 30],
            108 => ['Al-Kausar', 'الكوثر', 'Nikmat Berlimpah', 3, 'Makkiyah', 30],
            109 => ['Al-Kafirun', 'الكافرون', 'Orang-Orang Kafir', 6, 'Makkiyah', 30],
            110 => ['An-Nasr', 'النصر', 'Pertolongan Allah', 3, 'Madaniyah', 30],
            111 => ['Al-Lahab', 'اللهب', 'Gejolak Api', 5, 'Makkiyah', 30],
            112 => ['Al-Ikhlas', 'الإخلاص', 'Kemurnian Keesaan Allah', 4, 'Makkiyah', 30],
            113 => ['Al-Falaq', 'الفلق', 'Waktu Subuh', 5, 'Makkiyah', 30],
            114 => ['An-Nas', 'الناس', 'Umat Manusia', 6, 'Makkiyah', 30],
        ];

        $surahs = [];
        foreach ($raw as $num => $info) {
            $formattedNum = sprintf('%03d', $num);
            $surahs[$num] = [
                'number' => $num,
                'number_arabic' => self::toArabicDigits($num),
                'name' => $info[0],
                'arabic' => $info[1],
                'translation' => $info[2],
                'ayah_count' => $info[3],
                'ayah_count_arabic' => self::toArabicDigits($info[3]),
                'type' => $info[4],
                'juz' => $info[5],
                'audio_url' => "https://cdn.islamic.network/quran/audio-surah/128/ar.alafasy/{$num}.mp3",
                'audio_fallback' => "https://download.quranicaudio.com/quran/mishari_rashid_al-3afasi/{$formattedNum}.mp3",
            ];
        }

        return $surahs;
    }

    /**
     * Dapatkan detail surah berdasarkan nomor (1 - 114)
     *
     * @return array{number: int, name: string, arabic: string, translation: string, ayah_count: int, type: string, juz: int, audio_url: string, audio_fallback: string}|null
     */
    public static function getSurah(int $number): ?array
    {
        $all = self::getAllSurahs();

        return $all[$number] ?? null;
    }

    /**
     * Dapatkan daftar surah pilihan sekolah (Juz 30 + Surah Populer: Yasin, Al-Waqi'ah, Ar-Rahman, Al-Mulk, Al-Kahfi)
     *
     * @return array<int, array{number: int, name: string, arabic: string, translation: string, ayah_count: int, type: string, juz: int, audio_url: string, audio_fallback: string}>
     */
    public static function getFeaturedSurahs(): array
    {
        $all = self::getAllSurahs();
        $featuredNumbers = [1, 18, 36, 55, 56, 67, 78, 93, 112, 113, 114];

        return array_intersect_key($all, array_flip($featuredNumbers));
    }

    /**
     * Convert western/latin numbers (0-9) to Eastern Arabic numerals (٠-٩).
     */
    public static function toArabicDigits(int|string $number): string
    {
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        return str_replace($western, $eastern, (string) $number);
    }

    /**
     * Ambil data lengkap ayat-ayat surah beserta teks Arab, Latin, arti Bahasa Indonesia, dan audio.
     * Menggunakan cache 30 hari untuk performa super cepat (0 ms latency setelah di-cache).
     *
     * @return array{number: int, name: string, arabic: string, translation: string, revelation: string, ayah_count: int, description: string, audio_full: string, audio_options: array, prev_surah: ?array, next_surah: ?array, verses: array}|null
     */
    public static function getSurahDetail(int $number): ?array
    {
        if ($number < 1 || $number > 114) {
            return null;
        }

        return Cache::remember('quran_surah_detail_v5_'.$number, now()->addDays(30), function () use ($number) {
            $baseMeta = self::getSurah($number);

            // 1. Coba ambil dari EQuran API v2 (Sumber Resmi Kemenag RI)
            try {
                $response = Http::withoutVerifying()
                    ->timeout(7)
                    ->get("https://equran.id/api/v2/surat/{$number}");

                if ($response->successful()) {
                    $d = $response->json('data');
                    if ($d && isset($d['ayat']) && is_array($d['ayat'])) {
                        $verses = [];
                        foreach ($d['ayat'] as $v) {
                            $audioUrl = $v['audio']['05'] ?? ($v['audio']['01'] ?? (is_array($v['audio']) ? reset($v['audio']) : null));
                            $ayahNum = (int) ($v['nomorAyat'] ?? 1);
                            $verses[] = [
                                'number' => $ayahNum,
                                'number_arabic' => self::toArabicDigits($ayahNum),
                                'arabic' => $v['teksArab'] ?? '',
                                'latin' => $v['teksLatin'] ?? '',
                                'translation' => $v['teksIndonesia'] ?? '',
                                'audio' => $audioUrl,
                                'audio_options' => $v['audio'] ?? [],
                            ];
                        }

                        $prev = null;
                        if (! empty($d['suratSebelumnya']) && is_array($d['suratSebelumnya'])) {
                            $prev = [
                                'number' => (int) $d['suratSebelumnya']['nomor'],
                                'name' => $d['suratSebelumnya']['namaLatin'] ?? '',
                            ];
                        } elseif ($number > 1) {
                            $prevMeta = self::getSurah($number - 1);
                            if ($prevMeta) {
                                $prev = ['number' => $number - 1, 'name' => $prevMeta['name']];
                            }
                        }

                        $next = null;
                        if (! empty($d['suratSelanjutnya']) && is_array($d['suratSelanjutnya'])) {
                            $next = [
                                'number' => (int) $d['suratSelanjutnya']['nomor'],
                                'name' => $d['suratSelanjutnya']['namaLatin'] ?? '',
                            ];
                        } elseif ($number < 114) {
                            $nextMeta = self::getSurah($number + 1);
                            if ($nextMeta) {
                                $next = ['number' => $number + 1, 'name' => $nextMeta['name']];
                            }
                        }

                        $fullAudio = $d['audioFull']['05'] ?? ($d['audioFull']['01'] ?? ($baseMeta['audio_url'] ?? ''));
                        $rawPlace = $d['tempatTurun'] ?? ($baseMeta['type'] ?? 'Makkiyah');
                        $normalizedRev = (stripos($rawPlace, 'mekah') !== false || stripos($rawPlace, 'makki') !== false) ? 'Makkiyah' : 'Madaniyah';

                        return [
                            'number' => (int) $d['nomor'],
                            'number_arabic' => self::toArabicDigits((int) $d['nomor']),
                            'name' => $d['namaLatin'] ?? $baseMeta['name'],
                            'arabic' => $d['nama'] ?? $baseMeta['arabic'],
                            'translation' => $d['arti'] ?? $baseMeta['translation'],
                            'revelation' => $normalizedRev,
                            'ayah_count' => (int) ($d['jumlahAyat'] ?? count($verses)),
                            'ayah_count_arabic' => self::toArabicDigits((int) ($d['jumlahAyat'] ?? count($verses))),
                            'description' => strip_tags($d['deskripsi'] ?? ''),
                            'audio_full' => $fullAudio,
                            'audio_options' => $d['audioFull'] ?? [],
                            'prev_surah' => $prev,
                            'next_surah' => $next,
                            'verses' => $verses,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("EQuran API error for surah {$number}: ".$e->getMessage());
            }

            // 2. Fallback cadangan: Quran API ID
            try {
                $response2 = Http::withoutVerifying()
                    ->timeout(6)
                    ->get("https://quran-api-id.vercel.app/surahs/{$number}");

                if ($response2->successful()) {
                    $d2 = $response2->json();
                    if ($d2 && isset($d2['ayahs']) && is_array($d2['ayahs'])) {
                        $verses = [];
                        foreach ($d2['ayahs'] as $v) {
                            $ayahNum = (int) ($v['number']['inSurah'] ?? 1);
                            $verses[] = [
                                'number' => $ayahNum,
                                'number_arabic' => self::toArabicDigits($ayahNum),
                                'arabic' => $v['arab'] ?? '',
                                'latin' => $v['translation']['id'] ?? '',
                                'translation' => $v['translation']['id'] ?? '',
                                'audio' => $v['audio']['alafasy'] ?? null,
                                'audio_options' => [],
                            ];
                        }

                        $surahNumber = (int) ($d2['number'] ?? $number);
                        $ayahCount = (int) ($d2['ayahCount'] ?? count($verses));

                        return [
                            'number' => $surahNumber,
                            'number_arabic' => self::toArabicDigits($surahNumber),
                            'name' => $d2['name'] ?? $baseMeta['name'],
                            'arabic' => $d2['asma']['ar']['short'] ?? $baseMeta['arabic'],
                            'translation' => $d2['asma']['id']['short'] ?? $baseMeta['translation'],
                            'revelation' => $d2['type']['id'] ?? $baseMeta['type'],
                            'ayah_count' => $ayahCount,
                            'ayah_count_arabic' => self::toArabicDigits($ayahCount),
                            'description' => strip_tags($d2['tafsir']['id'] ?? ''),
                            'audio_full' => $baseMeta['audio_url'] ?? '',
                            'audio_options' => [],
                            'prev_surah' => $number > 1 ? ['number' => $number - 1, 'name' => self::getSurah($number - 1)['name'] ?? ''] : null,
                            'next_surah' => $number < 114 ? ['number' => $number + 1, 'name' => self::getSurah($number + 1)['name'] ?? ''] : null,
                            'verses' => $verses,
                        ];
                    }
                }
            } catch (\Throwable $e2) {
                Log::warning("Backup Quran API error for surah {$number}: ".$e2->getMessage());
            }

            // 3. Fallback jika offline: Kembalikan metadata dasar dengan template
            if ($baseMeta) {
                return [
                    'number' => $baseMeta['number'],
                    'name' => $baseMeta['name'],
                    'arabic' => $baseMeta['arabic'],
                    'translation' => $baseMeta['translation'],
                    'revelation' => $baseMeta['type'],
                    'ayah_count' => $baseMeta['ayah_count'],
                    'description' => "Surat {$baseMeta['name']} ({$baseMeta['translation']}) merupakan surat ke-{$baseMeta['number']} dalam Al-Qur'an dan terdiri atas {$baseMeta['ayah_count']} ayat.",
                    'audio_full' => $baseMeta['audio_url'],
                    'audio_options' => ['alafasy' => $baseMeta['audio_url']],
                    'prev_surah' => $number > 1 ? ['number' => $number - 1, 'name' => self::getSurah($number - 1)['name'] ?? ''] : null,
                    'next_surah' => $number < 114 ? ['number' => $number + 1, 'name' => self::getSurah($number + 1)['name'] ?? ''] : null,
                    'verses' => [],
                ];
            }

            return null;
        });
    }

    /**
     * Dapatkan tafsir surah per ayat (Tafsir Ringkas Kemenag RI)
     *
     * @return array<int, string>|null
     */
    public static function getSurahTafsir(int $number): ?array
    {
        if ($number < 1 || $number > 114) {
            return null;
        }

        return Cache::remember('quran_surah_tafsir_v3_'.$number, now()->addDays(30), function () use ($number) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(6)
                    ->get("https://equran.id/api/v2/tafsir/{$number}");

                if ($response->successful()) {
                    $d = $response->json('data');
                    if ($d && isset($d['tafsir']) && is_array($d['tafsir'])) {
                        $map = [];
                        foreach ($d['tafsir'] as $t) {
                            $map[(int) $t['ayat']] = $t['teks'];
                        }

                        return $map;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal mengambil tafsir surah {$number}: ".$e->getMessage());
            }

            return [];
        });
    }
}

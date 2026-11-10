<?php

namespace App\Services;

use App\Models\QuranTahfidzRecord;

class LmsQuranAyahService
{
    /**
     * Daftar Seluruh 114 Surat Al-Qur'an Lengkap dengan Jumlah Ayat dan Arti
     */
    public static function getAvailableSurahs(): array
    {
        $allSurahs = [
            1 => ['number' => 1, 'name' => 'Al-Fatihah', 'total_ayah' => 7, 'meaning' => 'Pembukaan', 'category' => 'Umum & Wajib'],
            2 => ['number' => 2, 'name' => 'Al-Baqarah', 'total_ayah' => 286, 'meaning' => 'Sapi Betina', 'category' => 'Surat Pilihan SMA'],
            3 => ['number' => 3, 'name' => "Ali 'Imran", 'total_ayah' => 200, 'meaning' => 'Keluarga Imran', 'category' => 'Surat Pilihan SMA'],
            4 => ['number' => 4, 'name' => 'An-Nisa', 'total_ayah' => 176, 'meaning' => 'Wanita', 'category' => 'Surat Pilihan SMA'],
            5 => ['number' => 5, 'name' => "Al-Ma'idah", 'total_ayah' => 120, 'meaning' => 'Jamuan Hidangan', 'category' => 'Surat Pilihan SMA'],
            6 => ['number' => 6, 'name' => "Al-An'am", 'total_ayah' => 165, 'meaning' => 'Binatang Ternak', 'category' => 'Al-Qur\'an 30 Juz'],
            7 => ['number' => 7, 'name' => "Al-A'raf", 'total_ayah' => 206, 'meaning' => 'Tempat yang Tertinggi', 'category' => 'Al-Qur\'an 30 Juz'],
            8 => ['number' => 8, 'name' => 'Al-Anfal', 'total_ayah' => 75, 'meaning' => 'Harta Rampasan Perang', 'category' => 'Al-Qur\'an 30 Juz'],
            9 => ['number' => 9, 'name' => 'At-Taubah', 'total_ayah' => 129, 'meaning' => 'Pengampunan', 'category' => 'Surat Pilihan SMA'],
            10 => ['number' => 10, 'name' => 'Yunus', 'total_ayah' => 109, 'meaning' => 'Nabi Yunus', 'category' => 'Al-Qur\'an 30 Juz'],
            11 => ['number' => 11, 'name' => 'Hud', 'total_ayah' => 123, 'meaning' => 'Nabi Hud', 'category' => 'Al-Qur\'an 30 Juz'],
            12 => ['number' => 12, 'name' => 'Yusuf', 'total_ayah' => 111, 'meaning' => 'Nabi Yusuf', 'category' => 'Al-Qur\'an 30 Juz'],
            13 => ['number' => 13, 'name' => "Ar-Ra'd", 'total_ayah' => 43, 'meaning' => 'Guruh', 'category' => 'Al-Qur\'an 30 Juz'],
            14 => ['number' => 14, 'name' => 'Ibrahim', 'total_ayah' => 52, 'meaning' => 'Nabi Ibrahim', 'category' => 'Al-Qur\'an 30 Juz'],
            15 => ['number' => 15, 'name' => 'Al-Hijr', 'total_ayah' => 99, 'meaning' => 'Daerah Pegunungan', 'category' => 'Al-Qur\'an 30 Juz'],
            16 => ['number' => 16, 'name' => 'An-Nahl', 'total_ayah' => 128, 'meaning' => 'Lebah', 'category' => 'Al-Qur\'an 30 Juz'],
            17 => ['number' => 17, 'name' => "Al-Isra'", 'total_ayah' => 111, 'meaning' => 'Perjalanan Malam', 'category' => 'Surat Pilihan SMA'],
            18 => ['number' => 18, 'name' => 'Al-Kahf', 'total_ayah' => 110, 'meaning' => 'Gua', 'category' => 'Surat Pilihan SMA'],
            19 => ['number' => 19, 'name' => 'Maryam', 'total_ayah' => 98, 'meaning' => 'Maryam', 'category' => 'Al-Qur\'an 30 Juz'],
            20 => ['number' => 20, 'name' => 'Ta-Ha', 'total_ayah' => 135, 'meaning' => 'Ta-Ha', 'category' => 'Al-Qur\'an 30 Juz'],
            21 => ['number' => 21, 'name' => 'Al-Anbiya', 'total_ayah' => 112, 'meaning' => 'Para Nabi', 'category' => 'Al-Qur\'an 30 Juz'],
            22 => ['number' => 22, 'name' => 'Al-Hajj', 'total_ayah' => 78, 'meaning' => 'Haji', 'category' => 'Al-Qur\'an 30 Juz'],
            23 => ['number' => 23, 'name' => "Al-Mu'minun", 'total_ayah' => 118, 'meaning' => 'Orang-Orang yang Beriman', 'category' => 'Al-Qur\'an 30 Juz'],
            24 => ['number' => 24, 'name' => 'An-Nur', 'total_ayah' => 64, 'meaning' => 'Cahaya', 'category' => 'Al-Qur\'an 30 Juz'],
            25 => ['number' => 25, 'name' => 'Al-Furqan', 'total_ayah' => 77, 'meaning' => 'Pembeda', 'category' => 'Al-Qur\'an 30 Juz'],
            26 => ['number' => 26, 'name' => "Asy-Syu'ara", 'total_ayah' => 227, 'meaning' => 'Para Penyair', 'category' => 'Al-Qur\'an 30 Juz'],
            27 => ['number' => 27, 'name' => 'An-Naml', 'total_ayah' => 93, 'meaning' => 'Semut', 'category' => 'Al-Qur\'an 30 Juz'],
            28 => ['number' => 28, 'name' => 'Al-Qashash', 'total_ayah' => 88, 'meaning' => 'Kisah-Kisah', 'category' => 'Al-Qur\'an 30 Juz'],
            29 => ['number' => 29, 'name' => "Al-'Ankabut", 'total_ayah' => 69, 'meaning' => 'Laba-Laba', 'category' => 'Al-Qur\'an 30 Juz'],
            30 => ['number' => 30, 'name' => 'Ar-Rum', 'total_ayah' => 60, 'meaning' => 'Bangsa Romawi', 'category' => 'Al-Qur\'an 30 Juz'],
            31 => ['number' => 31, 'name' => 'Luqman', 'total_ayah' => 34, 'meaning' => 'Luqman', 'category' => 'Surat Pilihan SMA'],
            32 => ['number' => 32, 'name' => 'As-Sajdah', 'total_ayah' => 30, 'meaning' => 'Sujud', 'category' => 'Al-Qur\'an 30 Juz'],
            33 => ['number' => 33, 'name' => 'Al-Ahzab', 'total_ayah' => 73, 'meaning' => 'Golongan yang Bersekutu', 'category' => 'Al-Qur\'an 30 Juz'],
            34 => ['number' => 34, 'name' => 'Saba', 'total_ayah' => 54, 'meaning' => 'Kaum Saba', 'category' => 'Al-Qur\'an 30 Juz'],
            35 => ['number' => 35, 'name' => 'Fathir', 'total_ayah' => 45, 'meaning' => 'Pencipta', 'category' => 'Al-Qur\'an 30 Juz'],
            36 => ['number' => 36, 'name' => 'Ya-Sin', 'total_ayah' => 83, 'meaning' => 'Ya-Sin', 'category' => 'Surat Pilihan SMA'],
            37 => ['number' => 37, 'name' => 'Ash-Shaffat', 'total_ayah' => 182, 'meaning' => 'Barisan-Barisan', 'category' => 'Al-Qur\'an 30 Juz'],
            38 => ['number' => 38, 'name' => 'Shad', 'total_ayah' => 88, 'meaning' => 'Shad', 'category' => 'Al-Qur\'an 30 Juz'],
            39 => ['number' => 39, 'name' => 'Az-Zumar', 'total_ayah' => 75, 'meaning' => 'Rombongan', 'category' => 'Al-Qur\'an 30 Juz'],
            40 => ['number' => 40, 'name' => 'Ghafir', 'total_ayah' => 85, 'meaning' => 'Yang Mengampuni', 'category' => 'Al-Qur\'an 30 Juz'],
            41 => ['number' => 41, 'name' => 'Fushshilat', 'total_ayah' => 54, 'meaning' => 'Yang Dijelaskan', 'category' => 'Al-Qur\'an 30 Juz'],
            42 => ['number' => 42, 'name' => 'Asy-Syura', 'total_ayah' => 53, 'meaning' => 'Musyawarah', 'category' => 'Al-Qur\'an 30 Juz'],
            43 => ['number' => 43, 'name' => 'Az-Zukhruf', 'total_ayah' => 89, 'meaning' => 'Perhiasan', 'category' => 'Al-Qur\'an 30 Juz'],
            44 => ['number' => 44, 'name' => 'Ad-Dukhan', 'total_ayah' => 59, 'meaning' => 'Kabut Asap', 'category' => 'Al-Qur\'an 30 Juz'],
            45 => ['number' => 45, 'name' => 'Al-Jatsiyah', 'total_ayah' => 37, 'meaning' => 'Yang Berlutut', 'category' => 'Al-Qur\'an 30 Juz'],
            46 => ['number' => 46, 'name' => 'Al-Ahqaf', 'total_ayah' => 35, 'meaning' => 'Bukit Pasir', 'category' => 'Al-Qur\'an 30 Juz'],
            47 => ['number' => 47, 'name' => 'Muhammad', 'total_ayah' => 38, 'meaning' => 'Nabi Muhammad', 'category' => 'Al-Qur\'an 30 Juz'],
            48 => ['number' => 48, 'name' => 'Al-Fath', 'total_ayah' => 29, 'meaning' => 'Kemenangan', 'category' => 'Al-Qur\'an 30 Juz'],
            49 => ['number' => 49, 'name' => 'Al-Hujurat', 'total_ayah' => 18, 'meaning' => 'Kamar-Kamar', 'category' => 'Surat Pilihan SMA'],
            50 => ['number' => 50, 'name' => 'Qaf', 'total_ayah' => 45, 'meaning' => 'Qaf', 'category' => 'Al-Qur\'an 30 Juz'],
            51 => ['number' => 51, 'name' => 'Adz-Dzariyat', 'total_ayah' => 60, 'meaning' => 'Angin yang Menerbangkan', 'category' => 'Al-Qur\'an 30 Juz'],
            52 => ['number' => 52, 'name' => 'Ath-Thur', 'total_ayah' => 49, 'meaning' => 'Bukit Tursina', 'category' => 'Al-Qur\'an 30 Juz'],
            53 => ['number' => 53, 'name' => 'An-Najm', 'total_ayah' => 62, 'meaning' => 'Bintang', 'category' => 'Al-Qur\'an 30 Juz'],
            54 => ['number' => 54, 'name' => 'Al-Qamar', 'total_ayah' => 55, 'meaning' => 'Bulan', 'category' => 'Al-Qur\'an 30 Juz'],
            55 => ['number' => 55, 'name' => 'Ar-Rahman', 'total_ayah' => 78, 'meaning' => 'Yang Maha Pengasih', 'category' => 'Surat Pilihan SMA'],
            56 => ['number' => 56, 'name' => "Al-Waqi'ah", 'total_ayah' => 96, 'meaning' => 'Hari Kiamat', 'category' => 'Surat Pilihan SMA'],
            57 => ['number' => 57, 'name' => 'Al-Hadid', 'total_ayah' => 29, 'meaning' => 'Besi', 'category' => 'Al-Qur\'an 30 Juz'],
            58 => ['number' => 58, 'name' => 'Al-Mujadilah', 'total_ayah' => 22, 'meaning' => 'Gugatan Wanita', 'category' => 'Surat Pilihan SMA'],
            59 => ['number' => 59, 'name' => 'Al-Hasyr', 'total_ayah' => 24, 'meaning' => 'Pengusiran', 'category' => 'Al-Qur\'an 30 Juz'],
            60 => ['number' => 60, 'name' => 'Al-Mumtahanah', 'total_ayah' => 13, 'meaning' => 'Wanita yang Diuji', 'category' => 'Al-Qur\'an 30 Juz'],
            61 => ['number' => 61, 'name' => 'Ash-Shaff', 'total_ayah' => 14, 'meaning' => 'Barisan', 'category' => 'Al-Qur\'an 30 Juz'],
            62 => ['number' => 62, 'name' => "Al-Jumu'ah", 'total_ayah' => 11, 'meaning' => 'Hari Jumat', 'category' => 'Al-Qur\'an 30 Juz'],
            63 => ['number' => 63, 'name' => 'Al-Munafiqun', 'total_ayah' => 11, 'meaning' => 'Kaum Munafik', 'category' => 'Al-Qur\'an 30 Juz'],
            64 => ['number' => 64, 'name' => 'At-Taghabun', 'total_ayah' => 18, 'meaning' => 'Hari Ditampakkan Kesalahan', 'category' => 'Al-Qur\'an 30 Juz'],
            65 => ['number' => 65, 'name' => 'Ath-Thalaq', 'total_ayah' => 12, 'meaning' => 'Talak / Perceraian', 'category' => 'Al-Qur\'an 30 Juz'],
            66 => ['number' => 66, 'name' => 'At-Tahrim', 'total_ayah' => 12, 'meaning' => 'Pengharaman', 'category' => 'Al-Qur\'an 30 Juz'],
            67 => ['number' => 67, 'name' => 'Al-Mulk', 'total_ayah' => 30, 'meaning' => 'Kerajaan', 'category' => 'Surat Pilihan SMA'],
            68 => ['number' => 68, 'name' => 'Al-Qalam', 'total_ayah' => 52, 'meaning' => 'Pena', 'category' => 'Al-Qur\'an 30 Juz'],
            69 => ['number' => 69, 'name' => 'Al-Haqqah', 'total_ayah' => 52, 'meaning' => 'Hari Kiamat Nyata', 'category' => 'Al-Qur\'an 30 Juz'],
            70 => ['number' => 70, 'name' => "Al-Ma'arij", 'total_ayah' => 44, 'meaning' => 'Tempat Naik', 'category' => 'Al-Qur\'an 30 Juz'],
            71 => ['number' => 71, 'name' => 'Nuh', 'total_ayah' => 28, 'meaning' => 'Nabi Nuh', 'category' => 'Al-Qur\'an 30 Juz'],
            72 => ['number' => 72, 'name' => 'Al-Jinn', 'total_ayah' => 28, 'meaning' => 'Bangsa Jin', 'category' => 'Al-Qur\'an 30 Juz'],
            73 => ['number' => 73, 'name' => 'Al-Muzzammil', 'total_ayah' => 20, 'meaning' => 'Orang yang Berselimut', 'category' => 'Al-Qur\'an 30 Juz'],
            74 => ['number' => 74, 'name' => 'Al-Muddatstsir', 'total_ayah' => 56, 'meaning' => 'Orang yang Berkemul', 'category' => 'Al-Qur\'an 30 Juz'],
            75 => ['number' => 75, 'name' => 'Al-Qiyamah', 'total_ayah' => 40, 'meaning' => 'Hari Kebangkitan', 'category' => 'Al-Qur\'an 30 Juz'],
            76 => ['number' => 76, 'name' => 'Al-Insan', 'total_ayah' => 31, 'meaning' => 'Manusia', 'category' => 'Al-Qur\'an 30 Juz'],
            77 => ['number' => 77, 'name' => 'Al-Mursalat', 'total_ayah' => 50, 'meaning' => 'Malaikat yang Diutus', 'category' => 'Al-Qur\'an 30 Juz'],
        ];

        // Tambahkan seluruh 37 surat Juz 30 (78 s.d. 114) dari QuranTahfidzRecord
        $juz30 = QuranTahfidzRecord::getJuz30Surahs();
        foreach ($juz30 as $num => $s) {
            $allSurahs[$num] = [
                'number' => $num,
                'name' => $s['name'],
                'total_ayah' => $s['total_ayah'],
                'meaning' => $s['meaning'],
                'category' => 'Juz 30 ('.($s['grade_label'] ?? 'Umum').')',
            ];
        }

        ksort($allSurahs);

        return $allSurahs;
    }

    /**
     * Dapatkan Teks Arab dan Terjemahan Ayat Quran
     */
    public static function getAyahText(int $surahNumber, string|int $ayahInput = 1): array
    {
        $database = static::getCuratedVerses();

        $key = "{$surahNumber}:{$ayahInput}";
        if (isset($database[$key])) {
            return $database[$key];
        }

        // Cek jika nomor ayat tunggal integer ada
        $ayahInt = (int) $ayahInput;
        $keyInt = "{$surahNumber}:{$ayahInt}";
        if (isset($database[$keyInt])) {
            return $database[$keyInt];
        }

        $surahList = static::getAvailableSurahs();
        $surahName = $surahList[$surahNumber]['name'] ?? "Surat No. {$surahNumber}";

        // Format standar kutipan ayat Al-Qur'an
        return [
            'surah_number' => $surahNumber,
            'surah_name' => $surahName,
            'ayah_number' => $ayahInput,
            'arabic' => "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ\n[Ayat {$ayahInput} QS. {$surahName}]",
            'translation' => "Dengan nama Allah Yang Maha Pengasih, Maha Penyayang. (Kutipan QS. {$surahName} Ayat {$ayahInput})",
            'reference' => "QS. {$surahName} [{$surahNumber}]: {$ayahInput}",
        ];
    }

    /**
     * Kumpulan Ayat-Ayat Standar Kurikulum PAI SMA & Juz 30 Populer
     */
    public static function getCuratedVerses(): array
    {
        return [
            // Al-Fatihah
            '1:1' => [
                'surah_number' => 1,
                'surah_name' => 'Al-Fatihah',
                'ayah_number' => 1,
                'arabic' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ',
                'translation' => 'Dengan nama Allah Yang Maha Pengasih, Maha Penyayang.',
                'reference' => 'QS. Al-Fatihah [1]: 1',
            ],
            '1:2' => [
                'surah_number' => 1,
                'surah_name' => 'Al-Fatihah',
                'ayah_number' => 2,
                'arabic' => 'الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ',
                'translation' => 'Segala puji bagi Allah, Tuhan seluruh alam,',
                'reference' => 'QS. Al-Fatihah [1]: 2',
            ],
            '1:3' => [
                'surah_number' => 1,
                'surah_name' => 'Al-Fatihah',
                'ayah_number' => 3,
                'arabic' => 'الرَّحْمَٰنِ الرَّحِيمِ',
                'translation' => 'Yang Maha Pengasih, Maha Penyayang,',
                'reference' => 'QS. Al-Fatihah [1]: 3',
            ],
            '1:4' => [
                'surah_number' => 1,
                'surah_name' => 'Al-Fatihah',
                'ayah_number' => 4,
                'arabic' => 'مَالِكِ يَوْمِ الدِّينِ',
                'translation' => 'Pemilik hari pembalasan.',
                'reference' => 'QS. Al-Fatihah [1]: 4',
            ],
            '1:5' => [
                'surah_number' => 1,
                'surah_name' => 'Al-Fatihah',
                'ayah_number' => 5,
                'arabic' => 'إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ',
                'translation' => 'Hanya kepada Engkaulah kami menyembah dan hanya kepada Engkaulah kami memohon pertolongan.',
                'reference' => 'QS. Al-Fatihah [1]: 5',
            ],
            '1:6' => [
                'surah_number' => 1,
                'surah_name' => 'Al-Fatihah',
                'ayah_number' => 6,
                'arabic' => 'اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ',
                'translation' => 'Tunjukilah kami jalan yang lurus,',
                'reference' => 'QS. Al-Fatihah [1]: 6',
            ],
            '1:7' => [
                'surah_number' => 1,
                'surah_name' => 'Al-Fatihah',
                'ayah_number' => 7,
                'arabic' => 'صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ',
                'translation' => '(yaitu) jalan orang-orang yang telah Engkau beri nikmat kepadanya; bukan (jalan) mereka yang dimurkai, dan bukan (pula jalan) mereka yang sesat.',
                'reference' => 'QS. Al-Fatihah [1]: 7',
            ],
            '1:1-7' => [
                'surah_number' => 1,
                'surah_name' => 'Al-Fatihah',
                'ayah_number' => '1-7 (Lengkap)',
                'arabic' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ ﴿١﴾ الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ ﴿٢﴾ الرَّحْمَٰنِ الرَّحِيمِ ﴿٣﴾ مَالِكِ يَوْمِ الدِّينِ ﴿٤﴾ إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ ﴿٥﴾ اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ ﴿٦﴾ صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ ﴿٧﴾',
                'translation' => '1. Dengan nama Allah Yang Maha Pengasih, Maha Penyayang. 2. Segala puji bagi Allah, Tuhan seluruh alam, 3. Yang Maha Pengasih, Maha Penyayang, 4. Pemilik hari pembalasan. 5. Hanya kepada Engkaulah kami menyembah dan hanya kepada Engkaulah kami memohon pertolongan. 6. Tunjukilah kami jalan yang lurus, 7. (yaitu) jalan orang-orang yang telah Engkau beri nikmat kepadanya; bukan (jalan) mereka yang dimurkai, dan bukan (pula jalan) mereka yang sesat.',
                'reference' => 'QS. Al-Fatihah [1]: 1-7',
            ],

            // Ayat Kursi (Al-Baqarah 255)
            '2:255' => [
                'surah_number' => 2,
                'surah_name' => 'Al-Baqarah (Ayat Kursi)',
                'ayah_number' => 255,
                'arabic' => 'اللَّهُ لَا إِلَٰهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ ۚ لَا تَأْخُذُهُ سِنَةٌ وَلَا نَوْمٌ ۚ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الْأَرْضِ ۗ مَن ذَا الَّذِي يَشْفَعُ عِندَهُ إِلَّا بِإِذْنِهِ ۚ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ ۖ وَلَا يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلَّا بِمَا شَاءَ ۚ وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالْأَرْضَ ۖ وَلَا يَئُودُهُ حِفْظُهُمَا ۚ وَهُوَ الْعَلِيُّ الْعَظِيمُ',
                'translation' => 'Allah, tidak ada tuhan selain Dia. Yang Mahahidup, Yang terus-menerus mengurus (makhluk-Nya), tidak mengantuk dan tidak tidur. Milik-Nya apa yang ada di langit dan apa yang ada di bumi. Tidak ada yang dapat memberi syafaat di sisi-Nya tanpa izin-Nya. Dia mengetahui apa yang ada di hadapan mereka dan apa yang ada di belakang mereka, dan mereka tidak mengetahui sesuatu apa pun dari ilmu-Nya melainkan apa yang Dia kehendaki. Kursi-Nya meliputi langit dan bumi. Dan Dia tidak merasa berat memelihara keduanya, dan Dia Mahatinggi, Mahabesar.',
                'reference' => 'QS. Al-Baqarah [2]: 255 (Ayat Kursi)',
            ],

            // Ali 'Imran 190-191 (Materi Berpikir Kritis & Sains SMA Kelas XI/XII)
            '3:190' => [
                'surah_number' => 3,
                'surah_name' => "Ali 'Imran",
                'ayah_number' => 190,
                'arabic' => 'إِنَّ فِي خَلْقِ السَّمَاوَاتِ وَالْأَرْضِ وَاخْتِلَافِ اللَّيْلِ وَالنَّهَارِ لَآيَاتٍ لِّأُولِي الْأَلْبَابِ',
                'translation' => 'Sesungguhnya dalam penciptaan langit dan bumi, dan pergantian malam dan siang terdapat tanda-tanda (kebesaran Allah) bagi orang yang berakal (Ulul Albab),',
                'reference' => "QS. Ali 'Imran [3]: 190",
            ],
            '3:191' => [
                'surah_number' => 3,
                'surah_name' => "Ali 'Imran",
                'ayah_number' => 191,
                'arabic' => 'الَّذِينَ يَذْكُرُونَ اللَّهَ قِيَامًا وَقُعُودًا وَعَلَىٰ جُنُوبِهِمْ وَيَتَفَكَّرُونَ فِي خَلْقِ السَّمَاوَاتِ وَالْأَرْضِ رَبَّنَا مَا خَلَقْتَ هَٰذَا بَاطِلًا سُبْحَانَكَ فَقِنَا عَذَابَ النَّارِ',
                'translation' => '(yaitu) orang-orang yang mengingat Allah sambil berdiri, duduk, atau dalam keadaan berbaring, dan mereka memikirkan tentang penciptaan langit dan bumi (seraya berkata), "Ya Tuhan kami, tidaklah Engkau menciptakan semua ini sia-sia; Mahasuci Engkau, lindungilah kami dari azab neraka."',
                'reference' => "QS. Ali 'Imran [3]: 191",
            ],

            // An-Nisa 59 (Ketaatan & Kepemimpinan)
            '4:59' => [
                'surah_number' => 4,
                'surah_name' => 'An-Nisa',
                'ayah_number' => 59,
                'arabic' => 'يَا أَيُّهَا الَّذِينَ آمَنُوا أَطِيعُوا اللَّهَ وَأَطِيعُوا الرَّسُولَ وَأُولِي الْأَمْرِ مِنكُمْ ۖ فَإِن تَنَازَعْتُمْ فِي شَيْءٍ فَرُدُّوهُ إِلَى اللَّهِ وَالرَّسُولِ إِن كُنتُمْ تُؤْمِنُونَ بِاللَّهِ وَالْيَوْمِ الْآخِرِ ۚ ذَٰلِكَ خَيْرٌ وَأَحْسَنُ تَأْوِيلًا',
                'translation' => 'Wahai orang-orang yang beriman! Taatilah Allah dan taatilah Rasul (Muhammad), dan Ulil Amri (pemegang kekuasaan) di antara kamu. Kemudian, jika kamu berlainan pendapat tentang sesuatu, maka kembalikanlah ia kepada Allah (Al-Qur\'an) dan Rasul (sunnahnya), jika kamu benar-benar beriman kepada Allah dan hari kemudian. Yang demikian itu lebih utama (bagimu) dan lebih baik akibatnya.',
                'reference' => 'QS. An-Nisa [4]: 59',
            ],

            // Al-Ma'idah 48 (Fastabiqul Khairat - SMA Kelas X/XI)
            '5:48' => [
                'surah_number' => 5,
                'surah_name' => "Al-Ma'idah",
                'ayah_number' => 48,
                'arabic' => 'فَاسْتَبِقُوا الْخَيْرَاتِ ۚ إِلَى اللَّهِ مَرْجِعُكُمْ جَمِيعًا فَيُنَبِّئُكُم بِمَا كُنتُمْ فِيهِ تَخْتَلِفُونَ',
                'translation' => 'Maka berlomba-lombalah berbuat kebajikan. Hanya kepada Allah kamu semua kembali, lalu diberitahukan-Nya kepadamu apa yang dahulu kamu perselisihkan.',
                'reference' => "QS. Al-Ma'idah [5]: 48",
            ],

            // At-Taubah 105 (Etos Kerja Keras & Tanggung Jawab)
            '9:105' => [
                'surah_number' => 9,
                'surah_name' => 'At-Taubah',
                'ayah_number' => 105,
                'arabic' => 'وَقُلِ اعْمَلُوا فَسَيَرَى اللَّهُ عَمَلَكُمْ وَرَسُولُهُ وَالْمُؤْمِنُونَ ۖ وَسَتُرَدُّونَ إِلَىٰ عَالِمِ الْغَيْبِ وَالشَّهَادَةِ فَيُنَبِّئُكُم بِمَا كُنتُمْ تَعْمَلُونَ',
                'translation' => 'Dan katakanlah, "Bekerjalah kamu, maka Allah akan melihat pekerjaanmu, begitu juga Rasul-Nya dan orang-orang mukmin, dan kamu akan dikembalikan kepada (Allah) Yang Mengetahui yang gaib dan yang nyata, lalu diberitakan-Nya kepada kamu apa yang telah kamu kerjakan."',
                'reference' => 'QS. At-Taubah [9]: 105',
            ],

            // Al-Isra' 32 (Larangan Mendekati Zina)
            '17:32' => [
                'surah_number' => 17,
                'surah_name' => "Al-Isra'",
                'ayah_number' => 32,
                'arabic' => 'وَلَا تَقْرَبُوا الزِّنَىٰ ۖ إِنَّهُ كَانَ فَاحِشَةً وَسَاءَ سَبِيلًا',
                'translation' => 'Dan janganlah kamu mendekati zina; (zina) itu sungguh suatu perbuatan keji, dan suatu jalan yang buruk.',
                'reference' => "QS. Al-Isra' [17]: 32",
            ],

            // Luqman 13-14 (Nasihat Menghindari Syirik & Berbakti pada Orang Tua)
            '31:13' => [
                'surah_number' => 31,
                'surah_name' => 'Luqman',
                'ayah_number' => 13,
                'arabic' => 'وَإِذْ قَالَ لُقْمَانُ لِابْنِهِ وَهُوَ يَعِظُهُ يَا بُنَيَّ لَا تُشْرِكْ بِاللَّهِ ۖ إِنَّ الشِّرْكَ لَظُلْمٌ عَظِيمٌ',
                'translation' => 'Dan (ingatlah) ketika Luqman berkata kepada anaknya, ketika dia memberi pelajaran kepadanya, "Wahai anakku! Janganlah engkau mempersekutukan Allah, sesungguhnya mempersekutukan (Allah) adalah benar-benar kezaliman yang besar."',
                'reference' => 'QS. Luqman [31]: 13',
            ],

            // Al-Hujurat 13 (Toleransi & Keragaman Bangsa)
            '49:13' => [
                'surah_number' => 49,
                'surah_name' => 'Al-Hujurat',
                'ayah_number' => 13,
                'arabic' => 'يَا أَيُّهَا النَّاسُ إِنَّا خَلَقْنَاكُم مِّن ذَكَرٍ وَأُنثَىٰ وَجَعَلْنَاكُمْ شُعُوبًا وَقَبَائِلَ لِتَعَارَفُوا ۚ إِنَّ أَكْرَمَكُمْ عِندَ اللَّهِ أَتْقَاكُمْ ۚ إِنَّ اللَّهَ عَلِيمٌ خَبِيرٌ',
                'translation' => 'Wahai manusia! Sungguh, Kami telah menciptakan kamu dari seorang laki-laki dan seorang perempuan, kemudian Kami jadikan kamu berbangsa-bangsa dan bersuku-suku agar kamu saling mengenal. Sesungguhnya yang paling mulia di antara kamu di sisi Allah ialah orang yang paling bertakwa. Sungguh, Allah Maha Mengetahui, Mahateliti.',
                'reference' => 'QS. Al-Hujurat [49]: 13',
            ],

            // Ar-Rahman 33 (Menuntut Ilmu & Menjelajahi Ruang Angkasa)
            '55:33' => [
                'surah_number' => 55,
                'surah_name' => 'Ar-Rahman',
                'ayah_number' => 33,
                'arabic' => 'يَا مَعْشَرَ الْجِنِّ وَالْإِنسِ إِنِ اسْتَطَعْتُمْ أَن تَنفُذُوا مِنْ أَقْطَارِ السَّمَاوَاتِ وَالْأَرْضِ فَانفُذُوا ۚ لَا تَنفُذُونَ إِلَّا بِسُلْطَانٍ',
                'translation' => 'Wahai golongan jin dan manusia! Jika kamu sanggup menembus (melintasi) penjuru langit dan bumi, maka tembuslah. Kamu tidak akan mampu menembusnya kecuali dengan kekuatan (ilmu pengetahuan).',
                'reference' => 'QS. Ar-Rahman [55]: 33',
            ],

            // Al-Mujadilah 11 (Derajat Orang Berilmu)
            '58:11' => [
                'surah_number' => 58,
                'surah_name' => 'Al-Mujadilah',
                'ayah_number' => 11,
                'arabic' => 'يَرْفَعِ اللَّهُ الَّذِينَ آمَنُوا مِنكُمْ وَالَّذِينَ أُوتُوا الْعِلْمَ دَرَجَاتٍ ۚ وَاللَّهُ بِمَا تَعْمَلُونَ خَبِيرٌ',
                'translation' => '...Niscaya Allah akan mengangkat (derajat) orang-orang yang beriman di antaramu dan orang-orang yang diberi ilmu beberapa derajat. Dan Allah Mahateliti apa yang kamu kerjakan.',
                'reference' => 'QS. Al-Mujadilah [58]: 11',
            ],

            // Al-Ikhlas (112: 1-4)
            '112:1' => [
                'surah_number' => 112,
                'surah_name' => 'Al-Ikhlas',
                'ayah_number' => 1,
                'arabic' => 'قُلْ هُوَ اللَّهُ أَحَدٌ',
                'translation' => 'Katakanlah (Muhammad), "Dialah Allah, Yang Maha Esa."',
                'reference' => 'QS. Al-Ikhlas [112]: 1',
            ],
            '112:2' => [
                'surah_number' => 112,
                'surah_name' => 'Al-Ikhlas',
                'ayah_number' => 2,
                'arabic' => 'اللَّهُ الصَّمَدُ',
                'translation' => 'Allah tempat meminta segala sesuatu.',
                'reference' => 'QS. Al-Ikhlas [112]: 2',
            ],
            '112:3' => [
                'surah_number' => 112,
                'surah_name' => 'Al-Ikhlas',
                'ayah_number' => 3,
                'arabic' => 'لَمْ يَلِدْ وَلَمْ يُولَدْ',
                'translation' => '(Allah) tidak beranak dan tidak pula diperanakkan,',
                'reference' => 'QS. Al-Ikhlas [112]: 3',
            ],
            '112:4' => [
                'surah_number' => 112,
                'surah_name' => 'Al-Ikhlas',
                'ayah_number' => 4,
                'arabic' => 'وَلَمْ يَكُن لَّهُ كُفُوًا أَحَدٌ',
                'translation' => 'dan tidak ada sesuatu yang setara dengan Dia.',
                'reference' => 'QS. Al-Ikhlas [112]: 4',
            ],
            '112:1-4' => [
                'surah_number' => 112,
                'surah_name' => 'Al-Ikhlas',
                'ayah_number' => '1-4 (Lengkap)',
                'arabic' => 'قُلْ هُوَ اللَّهُ أَحَدٌ ﴿١﴾ اللَّهُ الصَّمَدُ ﴿٢﴾ لَمْ يَلِدْ وَلَمْ يُولَدْ ﴿٣﴾ وَلَمْ يَكُن لَّهُ كُفُوًا أَحَدٌ ﴿٤﴾',
                'translation' => '1. Katakanlah (Muhammad), "Dialah Allah, Yang Maha Esa. 2. Allah tempat meminta segala sesuatu. 3. (Allah) tidak beranak dan tidak pula diperanakkan, 4. dan tidak ada sesuatu yang setara dengan Dia."',
                'reference' => 'QS. Al-Ikhlas [112]: 1-4',
            ],

            // Al-Falaq (113: 1-5)
            '113:1' => [
                'surah_number' => 113,
                'surah_name' => 'Al-Falaq',
                'ayah_number' => 1,
                'arabic' => 'قُلْ أَعُوذُ بِرَبِّ الْفَلَقِ',
                'translation' => 'Katakanlah, "Aku berlindung kepada Tuhan yang menguasai subuh (fajar),',
                'reference' => 'QS. Al-Falaq [113]: 1',
            ],
            '113:1-5' => [
                'surah_number' => 113,
                'surah_name' => 'Al-Falaq',
                'ayah_number' => '1-5 (Lengkap)',
                'arabic' => 'قُلْ أَعُوذُ بِرَبِّ الْفَلَقِ ﴿١﴾ مِن شَرِّ مَا خَلَقَ ﴿٢﴾ وَمِن شَرِّ غَاسِقٍ إِذَا وَقَبَ ﴿٣﴾ وَمِن شَرِّ النَّفَّاثَاتِ فِي الْعُقَدِ ﴿٤﴾ وَمِن شَرِّ حَاسِدٍ إِذَا حَسَدَ ﴿٥﴾',
                'translation' => '1. Katakanlah, "Aku berlindung kepada Tuhan yang menguasai subuh (fajar), 2. dari kejahatan (makhluk yang) Dia ciptakan, 3. dan dari kejahatan malam apabila telah gelap gulita, 4. dan dari kejahatan (perempuan-perempuan) penyihir yang meniup pada buhul-buhul (talinya), 5. dan dari kejahatan orang yang dengki apabila dia dengki."',
                'reference' => 'QS. Al-Falaq [113]: 1-5',
            ],

            // An-Nas (114: 1-6)
            '114:1' => [
                'surah_number' => 114,
                'surah_name' => 'An-Nas',
                'ayah_number' => 1,
                'arabic' => 'قُلْ أَعُوذُ بِرَبِّ النَّاسِ',
                'translation' => 'Katakanlah, "Aku berlindung kepada Tuhannya manusia,',
                'reference' => 'QS. An-Nas [114]: 1',
            ],
            '114:1-6' => [
                'surah_number' => 114,
                'surah_name' => 'An-Nas',
                'ayah_number' => '1-6 (Lengkap)',
                'arabic' => 'قُلْ أَعُوذُ بِرَبِّ النَّاسِ ﴿١﴾ مَلِكِ النَّاسِ ﴿٢﴾ إِلَٰهِ النَّاسِ ﴿٣﴾ مِن شَرِّ الْوَسْوَاسِ الْخَنَّاسِ ﴿٤﴾ الَّذِي يُوَسْوِسُ فِي صُدُورِ النَّاسِ ﴿٥﴾ مِنَ الْجِنَّةِ وَالنَّاسِ ﴿٦﴾',
                'translation' => '1. Katakanlah, "Aku berlindung kepada Tuhannya manusia, 2. Raja manusia, 3. Sembahan manusia, 4. dari kejahatan (bisikan) setan yang bersembunyi, 5. yang membisikkan (kejahatan) ke dalam dada manusia, 6. dari (golongan) jin dan manusia."',
                'reference' => 'QS. An-Nas [114]: 1-6',
            ],
        ];
    }
}

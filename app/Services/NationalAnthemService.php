<?php

namespace App\Services;

class NationalAnthemService
{
    /**
     * Daftar 10 Lagu Nasional Resmi untuk Bel Sekolah, Upacara & Apel Pagi.
     *
     * @return array<int, array{
     *     id: string,
     *     title: string,
     *     composer: string,
     *     tempo: string,
     *     category: string,
     *     usage: string,
     *     audio_url: string,
     *     audio_fallback: string,
     *     lyrics: string,
     *     melody_notes: array<int, array{f: float, d: float}>
     * }>
     */
    public static function getAnthems(): array
    {
        return self::getNationalAnthems();
    }

    public static function getNationalAnthems(): array
    {
        return [
            [
                'id' => 'indonesia-raya',
                'title' => 'Indonesia Raya',
                'composer' => 'Wage Rudolf Supratman (1928)',
                'tempo' => 'Maestoso (104 BPM)',
                'category' => 'Lagu Kebangsaan',
                'usage' => 'Upacara Bendera Senin & Lagu Wajib Pukul 10.00 WIB',
                'audio_url' => '/audio/lagu_nasional/indonesia_raya.mp3?v=20261111',
                'audio_fallback' => 'https://upload.wikimedia.org/wikipedia/commons/transcoded/6/6f/Indonesia_Raya_Simfoni_dan_Vokal_1_Bait.ogg/Indonesia_Raya_Simfoni_dan_Vokal_1_Bait.ogg.mp3',
                'lyrics' => "Indonesia tanah airku, tanah tumpah darahku\nDi sanalah aku berdiri, jadi pandu ibuku\nIndonesia kebangsaanku, bangsa dan tanah airku\nMarilah kita berseru: Indonesia bersatu!\nHiduplah tanahku, hiduplah negeriku\nBangsaku, rakyatku, semuanya\nBangunlah jiwanya, bangunlah badannya\nUntuk Indonesia Raya!\n\nIndonesia Raya, merdeka! Merdeka!\nTanahku, negeriku yang kucinta\nIndonesia Raya, merdeka! Merdeka!\nHiduplah Indonesia Raya!",
                'melody_notes' => [
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 440.00, 'd' => 0.5], // A4
                    ['f' => 392.00, 'd' => 0.75], // G4
                    ['f' => 329.63, 'd' => 0.5], // E4
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 440.00, 'd' => 0.5], // A4
                    ['f' => 523.25, 'd' => 1.0], // C5
                ],
            ],
            [
                'id' => 'indonesia-pusaka',
                'title' => 'Indonesia Pusaka',
                'composer' => 'Ismail Marzuki (1949)',
                'tempo' => 'Andante Cantabile (76 BPM)',
                'category' => 'Lagu Wajib Nasional',
                'usage' => 'Refleksi Karakter, Istirahat Siang & Penutup KBM',
                'audio_url' => '/audio/lagu_nasional/indonesia_pusaka.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Indonesia%20Pusaka.mp3',
                'lyrics' => "Indonesia tanah air beta, pusaka abadi nan jaya\nIndonesia sejak dulu kala tetap dipuja-puja bangsa\nDi sana tempat lahir beta, dibuaian dibesarkan bunda\nTempat berlindung di hari tua, sampai akhir menutup mata.",
                'melody_notes' => [
                    ['f' => 261.63, 'd' => 0.6], // C4
                    ['f' => 329.63, 'd' => 0.6], // E4
                    ['f' => 392.00, 'd' => 0.6], // G4
                    ['f' => 440.00, 'd' => 0.8], // A4
                    ['f' => 392.00, 'd' => 0.6], // G4
                    ['f' => 329.63, 'd' => 0.6], // E4
                    ['f' => 261.63, 'd' => 1.2], // C4
                ],
            ],
            [
                'id' => 'hari-merdeka',
                'title' => 'Hari Merdeka',
                'composer' => 'H. Mutahar (1946)',
                'tempo' => 'Marcia Maestoso (116 BPM)',
                'category' => 'Lagu Perjuangan',
                'usage' => 'Peringatan Hari Kemerdekaan & Apel Semangat Pagi',
                'audio_url' => '/audio/lagu_nasional/hari_merdeka.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Hari%20Merdeka.mp3',
                'lyrics' => "Tujuh belas Agustus tahun empat lima\nItulah hari kemerdekaan kita\nHari merdeka nusa dan bangsa\nHari lahirnya bangsa Indonesia\nMerdeka!\nSekali merdeka tetap merdeka\nSelama hayat masih dikandung badan\nKita tetap setia, tetap sedia\nMempertahankan Indonesia\nKita tetap setia, tetap sedia\nMembela negara kita!",
                'melody_notes' => [
                    ['f' => 523.25, 'd' => 0.4], // C5
                    ['f' => 523.25, 'd' => 0.4], // C5
                    ['f' => 523.25, 'd' => 0.4], // C5
                    ['f' => 392.00, 'd' => 0.4], // G4
                    ['f' => 440.00, 'd' => 0.4], // A4
                    ['f' => 493.88, 'd' => 0.4], // B4
                    ['f' => 523.25, 'd' => 0.8], // C5
                ],
            ],
            [
                'id' => 'bagimu-negeri',
                'title' => 'Bagimu Negeri',
                'composer' => 'Kusbini (1942)',
                'tempo' => 'Largo Religioso (66 BPM)',
                'category' => 'Lagu Kebangsaan',
                'usage' => 'Mengheningkan Cipta, Doa Bersama & Penutup Upacara',
                'audio_url' => '/audio/lagu_nasional/bagimu_negeri.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Bagimu%20Negeri.mp3',
                'lyrics' => "Padamu negeri kami berjanji\nPadamu negeri kami berbakti\nPadamu negeri kami mengabdi\nBagimu negeri jiwa raga kami.",
                'melody_notes' => [
                    ['f' => 392.00, 'd' => 0.8], // G4
                    ['f' => 440.00, 'd' => 0.8], // A4
                    ['f' => 523.25, 'd' => 1.0], // C5
                    ['f' => 493.88, 'd' => 0.8], // B4
                    ['f' => 440.00, 'd' => 0.8], // A4
                    ['f' => 392.00, 'd' => 1.4], // G4
                ],
            ],
            [
                'id' => 'berkibarlah-benderaku',
                'title' => 'Berkibarlah Benderaku',
                'composer' => 'Ibu Sud (1947)',
                'tempo' => 'Marcia Animato (112 BPM)',
                'category' => 'Lagu Perjuangan',
                'usage' => 'Pengibaran Bendera & Pembakar Semangat KBM',
                'audio_url' => '/audio/lagu_nasional/berkibarlah_benderaku.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Berkibarlah%20Benderaku.mp3',
                'lyrics' => "Berkibarlah benderaku, lambang suci gagah perwira\nDi seluruh pantai Indonesia, kau tetap pujaan bangsa\nSiapa berani menurunkan engkau, serentak rakyatmu membela\nSang merah putih yang perwira, berkibarlah selama-lamanya!",
                'melody_notes' => [
                    ['f' => 261.63, 'd' => 0.5], // C4
                    ['f' => 329.63, 'd' => 0.5], // E4
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 523.25, 'd' => 0.8], // C5
                    ['f' => 493.88, 'd' => 0.5], // B4
                    ['f' => 440.00, 'd' => 0.5], // A4
                    ['f' => 392.00, 'd' => 1.0], // G4
                ],
            ],
            [
                'id' => 'maju-indonesia',
                'title' => 'Maju Indonesia',
                'composer' => 'Alfred Simanjuntak (1943)',
                'tempo' => 'Marcia Energico (110 BPM)',
                'category' => 'Mars Pelajar & Pemuda',
                'usage' => 'Hari Sumpah Pemuda & Mars Penyemangat Pagi',
                'audio_url' => '/audio/lagu_nasional/bangun_pemudi_pemuda.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Bangun%20Pemudi%20Pemuda.mp3',
                'lyrics' => "Bangun pemudi pemuda Indonesia\nTangan bajumu singsingkan untuk negara\nMasa depanmu membina menjadi tanggunganmu\nTerhadap nusa, terhadap nusa\nSudi tetap berusaha jujur dan ikhlas\nTak usah banyak bicara trus kerja keras\nHati teguh dan lurus pikir tetap jernih\nBertingkah laku halus hai putra negeri!",
                'melody_notes' => [
                    ['f' => 261.63, 'd' => 0.5], // C4
                    ['f' => 261.63, 'd' => 0.5], // C4
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 440.00, 'd' => 0.5], // A4
                    ['f' => 440.00, 'd' => 0.5], // A4
                    ['f' => 392.00, 'd' => 1.0], // G4
                ],
            ],
            [
                'id' => 'maju-tak-gentar',
                'title' => 'Maju Tak Gentar',
                'composer' => 'Cornel Simanjuntak (1945)',
                'tempo' => 'Con Brio (114 BPM)',
                'category' => 'Lagu Perjuangan',
                'usage' => 'Latihan Baris-Berbaris (PBB) & Senam Pagi',
                'audio_url' => '/audio/lagu_nasional/maju_tak_gentar.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Maju%20Tak%20Gentar.mp3',
                'lyrics' => "Maju tak gentar membela yang benar\nMaju tak gentar hak kita diserang\nMaju serentak mengusir penyerang\nMaju serentak tentu kita menang\nBergerak bergerak serentak serentak\nMenerkam menerjang terjang\nTak gentar tak gentar menyerang menyerang\nMajulah majulah menang!",
                'melody_notes' => [
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 440.00, 'd' => 0.5], // A4
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 329.63, 'd' => 0.5], // E4
                    ['f' => 261.63, 'd' => 1.0], // C4
                ],
            ],
            [
                'id' => 'tanah-airku',
                'title' => 'Tanah Airku',
                'composer' => 'Ibu Sud (1927)',
                'tempo' => 'Lento Espressivo (72 BPM)',
                'category' => 'Lagu Nasional',
                'usage' => 'Pelepasan Siswa, Refleksi Cinta Tanah Air & Pulang',
                'audio_url' => '/audio/lagu_nasional/tanah_airku.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Tanah%20Air%20Indonesia.mp3',
                'lyrics' => "Tanah airku tidak kulupakan, kan terkenang selama hidupku\nBiarpun saya pergi jauh, tidak kan hilang dari kalbu\nTanahku yang kucintai, engkau kuhargai.\nWalaupun banyak neg'ri kujalani, yang masyhur permai dikata orang\nTetapi kampung dan rumahku, di sanalah kurasa senang\nTanahku tak kulupakan, engkau kubanggakan.",
                'melody_notes' => [
                    ['f' => 261.63, 'd' => 0.7], // C4
                    ['f' => 329.63, 'd' => 0.7], // E4
                    ['f' => 392.00, 'd' => 0.7], // G4
                    ['f' => 440.00, 'd' => 0.9], // A4
                    ['f' => 392.00, 'd' => 0.7], // G4
                    ['f' => 329.63, 'd' => 1.2], // E4
                ],
            ],
            [
                'id' => 'satu-nusa-satu-bangsa',
                'title' => 'Satu Nusa Satu Bangsa',
                'composer' => 'Liberty Manik (1947)',
                'tempo' => 'Andante Maestoso (80 BPM)',
                'category' => 'Lagu Nasional',
                'usage' => 'Peringatan Hari Besar Nasional & Toleransi Kebhinekaan',
                'audio_url' => '/audio/lagu_nasional/satu_nusa_satu_bangsa.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Satu%20Nusa%20Satu%20Bangsa.mp3',
                'lyrics' => "Satu nusa, satu bangsa, satu bahasa kita\nTanah air pasti jaya untuk selama-lamanya\nIndonesia pusaka, Indonesia tercinta\nNusa bangsa dan bahasa kita bela bersama.",
                'melody_notes' => [
                    ['f' => 261.63, 'd' => 0.6], // C4
                    ['f' => 293.66, 'd' => 0.6], // D4
                    ['f' => 329.63, 'd' => 0.6], // E4
                    ['f' => 392.00, 'd' => 1.0], // G4
                    ['f' => 329.63, 'd' => 0.6], // E4
                    ['f' => 261.63, 'd' => 1.0], // C4
                ],
            ],
            [
                'id' => 'garuda-pancasila',
                'title' => 'Garuda Pancasila',
                'composer' => 'Prohar Sudharnoto (1956)',
                'tempo' => 'Marcia Vivace (112 BPM)',
                'category' => 'Mars Ideologi Bangsa',
                'usage' => 'Peringatan Hari Lahir Pancasila & Penguatan P5',
                'audio_url' => '/audio/lagu_nasional/garuda_pancasila.mp3',
                'audio_fallback' => 'https://archive.org/download/LaguWajibNasionalLengkap/Garuda%20Pancasila.mp3',
                'lyrics' => "Garuda Pancasila, akulah pendukungmu\nPatriot proklamasi, sedia berkorban untukmu\nPancasila dasar negara, rakyat adil makmur sentosa\nPribadi bangsaku\nAyo maju maju, ayo maju maju, ayo maju maju!",
                'melody_notes' => [
                    ['f' => 261.63, 'd' => 0.5], // C4
                    ['f' => 261.63, 'd' => 0.5], // C4
                    ['f' => 329.63, 'd' => 0.5], // E4
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 523.25, 'd' => 0.8], // C5
                    ['f' => 392.00, 'd' => 0.5], // G4
                    ['f' => 329.63, 'd' => 0.5], // E4
                    ['f' => 261.63, 'd' => 1.0], // C4
                ],
            ],
        ];
    }

    /**
     * Daftar Soundboard & Bunyi Instan Sekolah (Minimal 12 Jenis Suara Siap Bunyi).
     *
     * @return array<int, array{
     *     id: string,
     *     name: string,
     *     icon: string,
     *     category: string,
     *     sound_type: string,
     *     description: string,
     *     voice_text: string|null,
     *     color: string,
     *     badge: string
     * }>
     */
    public static function getInstantSounds(): array
    {
        return [
            [
                'id' => 'chime-westminster',
                'name' => 'Westminster Chime (4-Nada)',
                'icon' => '🔔',
                'category' => 'Bel Jam Pelajaran',
                'sound_type' => 'westminster',
                'description' => 'Melodi klasik 4 nada bel sekolah bergengsi (E - C - D - G).',
                'voice_text' => null,
                'color' => 'indigo',
                'badge' => 'Standar Sekolah',
            ],
            [
                'id' => 'chime-3tone',
                'name' => '3-Tone Ding-Dang-Dong',
                'icon' => '🎵',
                'category' => 'Bel Pengumuman',
                'sound_type' => 'threetone',
                'description' => 'Nada 3 ketukan ceria pengantar pengumuman sentral ruang piket.',
                'voice_text' => null,
                'color' => 'blue',
                'badge' => 'Panggilan Siswa',
            ],
            [
                'id' => 'chime-2tone',
                'name' => '2-Tone Ding-Dong',
                'icon' => '🛎️',
                'category' => 'Bel Interkom',
                'sound_type' => 'twotone',
                'description' => 'Nada ganda singkat untuk memanggil staf TU, kurikulum, atau guru.',
                'voice_text' => null,
                'color' => 'teal',
                'badge' => 'Ruang Guru/TU',
            ],
            [
                'id' => 'siren-emergency',
                'name' => 'Sirine Siaga Kebakaran / Evakuasi',
                'icon' => '🚨',
                'category' => 'Tanggap Darurat',
                'sound_type' => 'siren_fire',
                'description' => 'Gelombang saw-wave melengking tanda evakuasi menuju titik kumpul.',
                'voice_text' => 'Perhatian, seluruh warga sekolah harap segera menuju titik kumpul darurat dengan tenang dan tertib.',
                'color' => 'rose',
                'badge' => 'Alarm Darurat',
            ],
            [
                'id' => 'siren-earthquake',
                'name' => 'Sirine Siaga Gempa Bumi',
                'icon' => '⚠️',
                'category' => 'Tanggap Darurat',
                'sound_type' => 'siren_earthquake',
                'description' => 'Sirine pulsa cepat berdenyut: Lindungi kepala, jauhi kaca dan berlindung.',
                'voice_text' => 'Peringatan gempa bumi! Berlindung di bawah meja dan lindungi kepala Anda.',
                'color' => 'amber',
                'badge' => 'Mitigasi Gempa',
            ],
            [
                'id' => 'whistle-picket',
                'name' => 'Peluit Lapangan / Apel Baris',
                'icon' => '📢',
                'category' => 'Tata Tertib Lapangan',
                'sound_type' => 'whistle',
                'description' => 'Tiga tiupan peluit tajam mengumpulkan barisan upacara dan apel.',
                'voice_text' => null,
                'color' => 'cyan',
                'badge' => 'Piket Lapangan',
            ],
            [
                'id' => 'trumpet-fanfare',
                'name' => 'Trompet Kehormatan (Fanfare)',
                'icon' => '🎺',
                'category' => 'Upacara & Seremoni',
                'sound_type' => 'fanfare',
                'description' => 'Nada trompet agung pengiring pengibaran sang merah putih atau piala.',
                'voice_text' => null,
                'color' => 'yellow',
                'badge' => 'Seremoni',
            ],
            [
                'id' => 'ceremonial-gong',
                'name' => 'Gong Sakral Pembukaan Acara',
                'icon' => '🥁',
                'category' => 'Upacara & Seremoni',
                'sound_type' => 'gong',
                'description' => 'Dentuman gong berfrekuensi rendah 110Hz dengan gema megah.',
                'voice_text' => null,
                'color' => 'purple',
                'badge' => 'Peresmian Event',
            ],
            [
                'id' => 'voice-start-lesson',
                'name' => 'Bel Masuk KBM + Narasi Suara',
                'icon' => '📚',
                'category' => 'Otomasi KBM',
                'sound_type' => 'voice_start',
                'description' => 'Chime pembuka disusul ucapan selamat belajar jam pelajaran pertama.',
                'voice_text' => 'Perhatian, bel tanda masuk telah berbunyi. Jam pelajaran pertama segera dimulai. Seluruh siswa silakan bersiap di ruang kelas masing-masing. Selamat belajar.',
                'color' => 'emerald',
                'badge' => 'Otomatis Masuk',
            ],
            [
                'id' => 'voice-break-time',
                'name' => 'Bel Waktu Istirahat + Narasi',
                'icon' => '☕',
                'category' => 'Otomasi KBM',
                'sound_type' => 'voice_break',
                'description' => 'Chime santai mengumumkan istirahat jajan, santai dan tetap jaga kebersihan.',
                'voice_text' => 'Perhatian, saatnya waktu istirahat. Selamat beristirahat dan jangan lupa selalu menjaga kebersihan lingkungan sekolah.',
                'color' => 'blue',
                'badge' => 'Istirahat 1 & 2',
            ],
            [
                'id' => 'voice-home-time',
                'name' => 'Bel Pulang Sekolah + Doa Pulang',
                'icon' => '🏠',
                'category' => 'Otomasi KBM',
                'sound_type' => 'voice_home',
                'description' => 'Chime meriah penutup KBM harian, pesan keselamatan dan doa pulang.',
                'voice_text' => 'Perhatian, kegiatan belajar mengajar hari ini telah selesai. Seluruh siswa dimohon berdoa sebelum meninggalkan kelas. Selamat jalan dan berhati-hati di jalan.',
                'color' => 'indigo',
                'badge' => 'Penutup KBM',
            ],
            [
                'id' => 'voice-dzuhur-prayer',
                'name' => 'Pengingat Shalat Dzuhur Berjamaah',
                'icon' => '🕌',
                'category' => 'Ibadah & Karakter',
                'sound_type' => 'voice_prayer',
                'description' => 'Pengingat santun persiapan wudhu dan menuju masjid untuk shalat berjamaah.',
                'voice_text' => 'Panggilan waktu shalat Dzuhur telah tiba. Seluruh siswa dan dewan guru muslim dipersilakan mengambil wudhu dan menuju masjid sekolah untuk melaksanakan Shalat Dzuhur berjamaah.',
                'color' => 'teal',
                'badge' => 'Ibadah Shalat',
            ],
        ];
    }
}

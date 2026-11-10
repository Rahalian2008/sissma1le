<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LmsAiCurriculumService
{
    /**
     * Dapatkan API Key Gemini yang tersimpan di SchoolSetting atau config services.
     */
    protected static function getGeminiApiKey(): ?string
    {
        $key = trim((string) (SchoolSetting::get('gemini_api_key') ?: config('services.gemini.api_key', '')));

        return ! empty($key) ? $key : null;
    }

    /**
     * Panggil Gemini API dengan prompt dan parsing JSON.
     * Fallback ke null jika offline/gagal.
     */
    protected static function callGemini(string $prompt, float $temperature = 0.5): ?array
    {
        if (app()->runningUnitTests()) {
            return null;
        }

        $apiKey = self::getGeminiApiKey();
        if (! $apiKey) {
            return null;
        }

        $modelsToTry = [
            SchoolSetting::get('gemini_model') ?: config('services.gemini.model', 'gemini-2.5-flash'),
            'gemini-2.5-flash',
            'gemini-flash-latest',
            'gemini-3.5-flash-lite',
        ];

        foreach (array_unique(array_filter($modelsToTry)) as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                $response = Http::timeout(25)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $prompt]],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => $temperature,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if ($response->successful()) {
                    $candidates = $response->json('candidates');
                    $rawText = $candidates[0]['content']['parts'][0]['text'] ?? null;
                    if ($rawText) {
                        $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                        $parsed = json_decode($cleanJson, true);
                        if (is_array($parsed)) {
                            return $parsed;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini API call failed for LMS model {$model}: ".$e->getMessage());
            }
        }

        return null;
    }

    // =========================================================================
    // 1. GENERATE MATERI PEMBELAJARAN INTERAKTIF (PERMENDIKDASMEN 13/2025)
    // =========================================================================

    /**
     * Resolusi bank foto dan gambar asli beresolusi tinggi berdasarkan objek materi, topik, dan mata pelajaran.
     */
    public static function resolveEducationalImages(string $subjectName, string $topic): array
    {
        $text = strtolower($subjectName.' '.$topic);

        // 1. Objek Konkret: Buku / Perpustakaan / Membaca / Literatur / Teks
        if (preg_match('/buku|perpustakaan|pustaka|membaca|baca|kamus|novel|literatur|literasi|teks|artikel/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Buku-Buku Referensi dan Sumber Belajar',
                    'alt' => 'Tumpukan buku-buku tebal untuk belajar dan membaca',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Halaman Buku Terbuka untuk Membaca dan Menelaah Teks Materi',
                    'alt' => 'Buku terbuka di atas meja belajar',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Rak Koleksi Buku di Perpustakaan Sekolah',
                    'alt' => 'Deretan buku tersusun rapi di rak perpustakaan',
                ],
            ];
        }

        // 2. Objek Konkret: Komputer / Laptop / Perangkat / Pemrograman
        if (preg_match('/komputer|laptop|pc|monitor|perangkat|layar|software|hardware|koding|coding|program|algoritma|aplikasi/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Komputer Laptop untuk Praktik dan Pembelajaran Digital',
                    'alt' => 'Komputer laptop di atas meja belajar',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Layar Monitor Komputer Menampilkan Kode Program',
                    'alt' => 'Layar kerja monitor komputer menampilkan sintaks kode',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Perangkat Server Komputer dan Kabel Jaringan Internet',
                    'alt' => 'Rak server komputer dan instalasi kabel data',
                ],
            ];
        }

        // 3. Objek Konkret: Mobil / Kendaraan / Transportasi / Mesin
        if (preg_match('/mobil|kendaraan|transportasi|sepeda|motor|mesin|jalan|kecepatan|laju/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Mobil dan Kendaraan Transportasi di Jalan Raya',
                    'alt' => 'Mobil berjalan di jalan raya untuk memahami gerak',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Kendaraan Mobil untuk Mempelajari Percepatan dan Laju Gerak',
                    'alt' => 'Kendaraan mobil melaju cepat',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1636466497217-26a8cbeaf0aa?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Bola-Bola Pendulum Gerak dan Hukum Kekekalan Energi',
                    'alt' => 'Ayunan bola pendulum logam fisika',
                ],
            ];
        }

        // 4. Objek Konkret: Tanaman / Tumbuhan / Daun / Pohon / Bunga / Hutan
        if (preg_match('/tanaman|tumbuhan|pohon|daun|bunga|flora|botani|hutan|kebun|akar/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Tanaman Hijau dan Struktur Daun Tumbuhan',
                    'alt' => 'Tanaman hijau dengan daun segar',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Pepohonan dan Keanekaragaman Tumbuhan Hutan',
                    'alt' => 'Pepohonan hijau di hutan alami',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Alat Mikroskop Laboratorium untuk Mengamati Sel Daun Tumbuhan',
                    'alt' => 'Mikroskop laboratorium biologi',
                ],
            ];
        }

        // 5. Objek Konkret: Mikroskop & Sel / Laboratorium
        if (preg_match('/mikroskop|sel|jaringan sel/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Alat Mikroskop Laboratorium untuk Pengamatan Sel',
                    'alt' => 'Mikroskop laboratorium mengamati preparat sel',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1530497610245-94d3c16cda28?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Model Molekul DNA dalam Inti Sel Hidup',
                    'alt' => 'Model struktur heliks DNA',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Tabung Reaksi dan Peralatan Kimia Laboratorium',
                    'alt' => 'Peralatan kaca laboratorium kimia',
                ],
            ];
        }

        // 6. Objek Konkret: Planet / Bumi / Tata Surya / Antariksa
        if (preg_match('/planet|bumi|matahari|bulan|tatasurya|angkasa|bintang|astronomi|orbit/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1614728894747-a83421e2b9c9?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Planet Bumi dari Luar Angkasa',
                    'alt' => 'Planet bumi terlihat dari antariksa',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Bumi, Atmosfer, dan Luar Angkasa',
                    'alt' => 'Citra bumi dan atmosfer dari orbit',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Lanskap Pegunungan dan Permukaan Bumi',
                    'alt' => 'Lanskap daratan bumi alami',
                ],
            ];
        }

        // 7. Objek Konkret: Uang / Pasar / Transaksi / Bank
        if (preg_match('/uang|pasar|belanja|harga|bank|perdagangan|modal|keuangan|rupiah/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1580519542036-c47de6196ba5?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Uang Kertas dan Transaksi Finansial Ekonomi',
                    'alt' => 'Uang kertas dan koin perniagaan',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Catatan Keuangan dan Pembukuan Ekonomi',
                    'alt' => 'Catatan laporan keuangan',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Aktivitas Pergudangan dan Distribusi Barang Perniagaan',
                    'alt' => 'Gudang distribusi dan perdagangan',
                ],
            ];
        }

        // 8. Objek Konkret: Candi / Bangunan Sejarah / Monumen
        if (preg_match('/candi|museum|sejarah|monumen|prasasti|arkeologi|peninggalan/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Bangunan Candi Bersejarah Warisan Budaya Nusantara',
                    'alt' => 'Candi bersejarah warisan nusantara',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Manuskrip Kuno dan Dokumen Catatan Sejarah Masa Lalu',
                    'alt' => 'Manuskrip dan arsip sejarah kuno',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1447069387593-a5de0862481e?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Bola Dunia Antik dan Peta Navigasi Penjelajahan',
                    'alt' => 'Globe bola dunia antik dan kompas',
                ],
            ];
        }

        // 9. Berdasarkan Mata Pelajaran Standar
        if (preg_match('/fisika|physics|gerak|gaya|newton|energi|kinematik|dinamik|gravitasi|mekanika|listrik|magnet|optik|gelombang|termodinamika/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1636466497217-26a8cbeaf0aa?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Bola Pendulum Logam untuk Mempelajari Momentum dan Gaya',
                    'alt' => 'Ayunan bola pendulum dinamika gerak',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1507413245164-6160d8298b31?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Sorotan Sinar Laser untuk Mengamati Sifat Cahaya dan Energi',
                    'alt' => 'Sinar laser dalam eksperimen optik',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Planet Bumi di Luar Angkasa dan Medan Gravitasi',
                    'alt' => 'Citra satelit bumi di ruang angkasa',
                ],
            ];
        }

        if (preg_match('/kimia|chem|unsur|molekul|senyawa|larutan|asam|basa|reaksi|stoikiometri|redoks|periodik/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Tabung Reaksi dan Cairan Larutan Kimia di Laboratorium',
                    'alt' => 'Tabung reaksi kimia dengan cairan larutan berwarna',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Alat Gelas Laboratorium dan Model Struktur Molekul',
                    'alt' => 'Peralatan gelas laboratorium kimia',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1603126857599-f6e157fa2fe6?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Alat Takar Volumetrik untuk Mengukur Reaksi Kimia',
                    'alt' => 'Pengukuran larutan kimia di laboratorium',
                ],
            ];
        }

        if (preg_match('/biologi|bio|genetik|dna|hereditas|ekosistem|hewan|tumbuhan|fisiologi/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Alat Mikroskop Laboratorium untuk Pengamatan Biologi',
                    'alt' => 'Mikroskop laboratorium biologi',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1530497610245-94d3c16cda28?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Model Molekul DNA dalam Kajian Genetika',
                    'alt' => 'Model fisik molekul DNA',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Hutan Hijau Alami dan Ekosistem Lingkungan Hidup',
                    'alt' => 'Ekosistem hutan dan keanekaragaman hayati',
                ],
            ];
        }

        if (preg_match('/matematik|math|aljabar|kalkulus|geometri|trigonometri|statistik|peluang|fungsi|vektor|matriks/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Papan Tulis Berisi Rumus Matematika dan Perhitungan Aljabar',
                    'alt' => 'Papan tulis berisi rumus dan persamaan matematika',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Penggaris Busur dan Alat Pengukur Geometri Bidang',
                    'alt' => 'Penggaris busur dan alat ukur matematika',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Tampilan Grafik dan Diagram Data Statistik',
                    'alt' => 'Grafik batang dan data statistik',
                ],
            ];
        }

        if (preg_match('/informatika|komputer|coding|pemrograman|jaringan|internet|cyber|software|hardware|ai/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Layar Komputer Menampilkan Kode Pemrograman',
                    'alt' => 'Layar komputer berisi sintaks kode program',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Perangkat Server dan Kabel Jaringan Komputer',
                    'alt' => 'Rak server pusat data',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Papan Sirkuit Elektronik dan Chip Mikroprosesor Komputer',
                    'alt' => 'Papan sirkuit printed circuit board mikroprosesor',
                ],
            ];
        }

        if (preg_match('/geografi|geo|peta|bumi|litosfer|atmosfer|hidrosfer|vulkanik|cuaca|iklim|lingkungan/i', $text)) {
            return [
                [
                    'url' => 'https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 1: Peta Wilayah Geografi dan Alat Kompas Navigasi',
                    'alt' => 'Peta topografi wilayah dengan kompas',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 2: Lanskap Permukaan Bumi dan Pegunungan Alami',
                    'alt' => 'Pemandangan lanskap geomorfologi pegunungan',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1000&q=80',
                    'caption' => 'Gambar 3: Citra Permukaan Bumi dan Lapisan Atmosfer dari Angkasa',
                    'alt' => 'Bumi dari luar angkasa',
                ],
            ];
        }

        // Gambar default foto objek nyata bermutu tinggi
        return [
            [
                'url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1000&q=80',
                'caption' => "Gambar 1: Buku dan Sumber Bacaan Terkait {$topic}",
                'alt' => "Buku referensi pembelajaran {$topic}",
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1507413245164-6160d8298b31?auto=format&fit=crop&w=1000&q=80',
                'caption' => "Gambar 2: Pengamatan dan Praktik Langsung Materi {$topic}",
                'alt' => "Peralatan pengamatan dalam bidang {$subjectName}",
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1000&q=80',
                'caption' => "Gambar 3: Objek Nyata dalam Kehidupan Sehari-hari Terkait {$topic}",
                'alt' => 'Penerapan nyata dalam sains dan kemasyarakatan',
            ],
        ];
    }

    public static function generateInteractiveMaterial(
        CourseClass $courseClass,
        string $topic,
        ?string $targetFase = null,
        string $contentType = 'text',
        ?string $videoUrl = null,
        ?string $teacherNotes = null,
        ?string $elemen = null,
        ?string $capaianPembelajaran = null,
        ?string $tujuanPembelajaran = null,
        ?string $alurTujuanPembelajaran = null
    ): array {
        $subject = $courseClass->subject;
        $subjectName = $subject->name;
        $fase = $targetFase ?: ($subject->grade ?: 'Fase E / F (SMA)');
        $schoolClassName = $courseClass->schoolClass->name;

        $typeLabels = [
            'document' => 'Dokumen / Modul Bacaan Esensial (PDF/PPT/DOC)',
            'video' => 'Video Pembelajaran Interaktif (Multimedia & Pengamatan Visual)',
            'text' => 'Rangkuman & Artikel Teks Interaktif',
            'link' => 'Tautan Luar / Sumber Belajar Digital Web Edukasi',
        ];
        $contentTypeLabel = $typeLabels[$contentType] ?? 'Rangkuman & Artikel Teks Interaktif';

        $extraContext = "\n- Tipe Konten: {$contentTypeLabel}";
        if ($contentType === 'video') {
            $extraContext .= "\n  Pedoman Tipe Video: Uraikan materi secara terperinci dengan panduan fokus observasi tayangan video, studi fenomena visual, kartu konsep, dan cek pemahaman terkait konten visual.";
            if ($videoUrl) {
                $extraContext .= "\n  Tautan Video Acuan: {$videoUrl}";
            }
        } elseif ($contentType === 'document') {
            $extraContext .= "\n  Pedoman Tipe Dokumen: Uraikan materi secara terperinci sebagai panduan telaah literasi mendalam (deep reading guide) dari modul/dokumen, pembedahan subbab esensial, kartu konsep, dan studi kasus.";
        } elseif ($contentType === 'link') {
            $extraContext .= "\n  Pedoman Tipe Tautan: Uraikan materi secara terperinci sebagai panduan penelusuran mandiri berbasis web (guided web-inquiry), poin telaah kritis, kartu konsep, dan pemecahan masalah.";
            if ($videoUrl) {
                $extraContext .= "\n  Tautan Sumber Acuan: {$videoUrl}";
            }
        } else {
            $extraContext .= "\n  Pedoman Tipe Teks Interaktif: Uraikan materi secara utuh, bernas, runtut, interaktif, sangat terperinci, dan mendalam sesuai standar Kurikulum Nasional Permendikdasmen No. 13 Tahun 2025.";
        }

        if ($teacherNotes) {
            $extraContext .= "\n- Arahan Khusus Guru Pengampu: {$teacherNotes}";
        }

        $elemenContext = $elemen ? trim($elemen) : "Pemahaman {$subjectName} & Keterampilan Proses";
        $cpContext = $capaianPembelajaran ? trim($capaianPembelajaran) : "Peserta didik memahami dan menganalisis konsep {$topic} secara mendalam sesuai regulasi Permendikdasmen No. 13 Tahun 2025 jenjang {$fase}.";
        $tpContext = $tujuanPembelajaran ? trim($tujuanPembelajaran) : "1. Mengidentifikasi dan menjelaskan konsep esensial {$topic}.\n2. Menganalisis fenomena kontekstual berbasis konsep {$topic}.\n3. Menyelesaikan permasalahan nyata menggunakan prinsip {$topic}.";
        $atpContext = $alurTujuanPembelajaran ? trim($alurTujuanPembelajaran) : 'Tahap 1: Penguasaan konsep dasar -> Tahap 2: Analisis mekanisme & hubungan antarvariabel -> Tahap 3: Implementasi kontekstual & pemecahan masalah.';

        $prompt = <<<PROMPT
Anda adalah Pakar Kurikulum Nasional Kemendikdasmen RI yang menguasai regulasi terbaru Permendikdasmen No. 13 Tahun 2025 untuk jenjang SMA.
Tugas Anda: Buatlah rancangan "Modul Pembelajaran Interaktif Lengkap dan Terperinci" untuk:
- Mata Pelajaran: {$subjectName}
- Rombel / Tingkat: {$schoolClassName}
- Target Fase Kurikulum Nasional: {$fase}
- Elemen: {$elemenContext}
- Materi Pembelajaran: {$topic}
- Capaian Pembelajaran (CP) dari Guru: {$cpContext}
- Tujuan Pembelajaran (TP) dari Guru: {$tpContext}
- Alur Tujuan Pembelajaran (ATP) dari Guru: {$atpContext}{$extraContext}

INSTRUKSI UTAMA & MUTLAK:
AI WAJIB MENGENERATE MATERI PEMBELAJARAN YANG HASILNYA SEPENUHNYA BERDASARKAN DAN DITURUNKAN SECARA KETAT DARI FORM DI ATAS (Elemen, Capaian Pembelajaran, Tujuan Pembelajaran, Alur Tujuan Pembelajaran, Materi, dan Target Fase).
1. BUKAN HANYA RANGKUMAN SINGKAT, MELAINKAN PENJELASAN MATERI SECARA TERPERINCI, LENGKAP, SISTEMATIS, DAN MUDAH DIFAHAMI OLEH SISWA SMA.
2. TIDAK PERLU ADA GAMBAR/FOTO/ILUSTRASI. Fokus sajikan penjelasan materi yang mendalam, bernas, kaya argumen ilmiah, dan analogi dunia nyata yang membumi.
3. HILANGKAN PENAMAAN / LABEL "Sub-Bab", "Sub-Bab 1", "Sub-Bab 2", dsb. Sajikan judul bahasan langsung sesuai topik bahasan yang elegan.
4. Capaian Pembelajaran (CP) dalam modul HARUS konsisten dengan CP yang diinputkan guru.
5. Tujuan Pembelajaran (TP) dalam modul HARUS mencakup butir-butir TP yang diinputkan guru.
6. Uraian materi harus menyajikan konten terperinci yang membimbing siswa menuntaskan Alur Tujuan Pembelajaran (ATP) yang ditentukan.

Format keluaran HARUS berupa objek JSON valid dengan kunci-kunci berikut (seluruh teks dalam Bahasa Indonesia formal, edukatif, dan inspiratif):
{
  "title": "Judul modul interaktif yang elegan dan sesuai CP",
  "topic": "{$topic}",
  "materi": "{$topic}",
  "elemen": "{$elemenContext}",
  "target_fase": "{$fase}",
  "tipe_konten": "{$contentType}",
  "catatan_guru": "Catatan ringkas instruksi belajar dari guru",
  "capaian_pembelajaran": "{$cpContext}",
  "tujuan_pembelajaran": [
    "Tujuan pembelajaran 1 berbasis ABCD / kompetensi dari form guru",
    "Tujuan pembelajaran 2",
    "Tujuan pembelajaran 3"
  ],
  "alur_tujuan_pembelajaran": "{$atpContext}",
  "profil_pelajar_pancasila": [
    "Bernalar Kritis: Menemukan pola dan memecahkan permasalahan kontekstual",
    "Mandiri: Mengembangkan pemahaman konsep secara terarah",
    "Kreatif: Mengaitkan materi dengan fenomena nyata"
  ],
  "ringkasan_materi": "Ringkasan eksekutif 2-3 paragraf pengantar modul sebagai orientasi awal peserta didik terhadap materi {$topic}.",
  "uraian_materi": [
    {
      "nomor": 1,
      "subjudul": "Fondasi Konseptual & Landasan Teoretis",
      "konten_lengkap": "Penjelasan terperinci dan mendalam minimal 2-3 paragraf komprehensif yang menguraikan asal-usul konsep, definisi ilmiah, prinsip kerja, pembuktian logis, dan batasan penerapan.",
      "analogi": "Analogi sederhana dari dunia nyata yang menghubungkan konsep teoretis ini dengan peristiwa yang biasa dialami siswa sehari-hari sehingga sangat mudah dipahami.",
      "poin_kunci": [
        "Poin penting 1 yang wajib diingat siswa",
        "Poin penting 2 mengenai korelasi antar variabel",
        "Poin penting 3 mengenai hukum atau rumus dasar"
      ]
    },
    {
      "nomor": 2,
      "subjudul": "Analisis Mekanisme, Hubungan Sebab-Akibat & Variabel Kunci",
      "konten_lengkap": "Penjelasan terperinci mengenai mekanisme proses yang terjadi, bagaimana variabel-variabel saling mempengaruhi, rumus matematis atau dalil keilmuan jika ada, serta analisis mendalam fenomena terkait.",
      "analogi": "Analogi kehidupan nyata kedua untuk memperjelas mekanisme interaksi antar variabel secara visual.",
      "poin_kunci": [
        "Poin penting 1 tentang mekanisme proses",
        "Poin penting 2 tentang faktor penentu keberhasilan atau laju sistem"
      ]
    },
    {
      "nomor": 3,
      "subjudul": "Implementasi Kontekstual, Studi Rekayasa & Solusi Masalah Nyata",
      "konten_lengkap": "Penjelasan terperinci mengenai penerapan materi ini pada teknologi modern, peradaban masa kini, riset ilmiah mutakhir, dan dampaknya bagi kehidupan masyarakat Indonesia serta global.",
      "analogi": "Analogi ketiga atau perumpamaan konkret mengenai pemanfaatan teknologi terkait.",
      "poin_kunci": [
        "Poin penting 1 mengenai pemanfaatan praktis di era modern",
        "Poin penting 2 mengenai antisipasi kendala dan solusi inovatif"
      ]
    }
  ],
  "kartu_konsep": [
    {"istilah": "Istilah 1", "definisi": "Penjelasan inti konsep 1", "contoh": "Contoh dalam kehidupan sehari-hari"},
    {"istilah": "Istilah 2", "definisi": "Penjelasan inti konsep 2", "contoh": "Contoh dalam kehidupan sehari-hari"},
    {"istilah": "Istilah 3", "definisi": "Penjelasan inti konsep 3", "contoh": "Contoh dalam kehidupan sehari-hari"},
    {"istilah": "Istilah 4", "definisi": "Penjelasan inti konsep 4", "contoh": "Contoh dalam kehidupan sehari-hari"}
  ],
  "cek_pemahaman": [
    {
      "pertanyaan": "Pertanyaan interaktif 1 untuk menguji pemahaman konsep siswa",
      "opsi": {"A": "Pilihan A", "B": "Pilihan B", "C": "Pilihan C", "D": "Pilihan D"},
      "kunci": "A",
      "penjelasan": "Pembahasan ilmiah mengapa jawaban ini benar"
    },
    {
      "pertanyaan": "Pertanyaan interaktif 2 berbasis studi fenomena",
      "opsi": {"A": "Pilihan A", "B": "Pilihan B", "C": "Pilihan C", "D": "Pilihan D"},
      "kunci": "B",
      "penjelasan": "Pembahasan ilmiah mengapa jawaban ini benar"
    }
  ],
  "studi_kasus": "Studi kasus kontekstual nyata di Indonesia atau sains dunia yang mengajak siswa mengaplikasikan materi ini.",
  "glosarium": [
    {"kata": "Kata Kunci 1", "arti": "Definisi singkat"},
    {"kata": "Kata Kunci 2", "arti": "Definisi singkat"}
  ],
  "refleksi_siswa": "Pertanyaan pemantik refleksi diri untuk siswa setelah menuntaskan pembelajaran ini."
}
PROMPT;

        $onlineResult = self::callGemini($prompt, 0.4);
        if ($onlineResult && isset($onlineResult['title'], $onlineResult['ringkasan_materi'])) {
            $onlineResult['tipe_konten'] = $contentType;
            $onlineResult['elemen'] = $onlineResult['elemen'] ?? $elemenContext;
            $onlineResult['target_fase'] = $onlineResult['target_fase'] ?? $fase;
            $onlineResult['materi'] = $onlineResult['materi'] ?? $topic;
            $onlineResult['capaian_pembelajaran'] = $capaianPembelajaran ?: ($onlineResult['capaian_pembelajaran'] ?? $cpContext);
            $onlineResult['alur_tujuan_pembelajaran'] = $alurTujuanPembelajaran ?: ($onlineResult['alur_tujuan_pembelajaran'] ?? $atpContext);

            if ($tujuanPembelajaran && (! isset($onlineResult['tujuan_pembelajaran']) || empty($onlineResult['tujuan_pembelajaran']))) {
                $onlineResult['tujuan_pembelajaran'] = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $tujuanPembelajaran))));
            }

            if ($teacherNotes) {
                $onlineResult['catatan_guru'] = $teacherNotes;
            }

            // Bersihkan label Sub-Bab dan hapus gambar dari uraian materi
            if (! empty($onlineResult['uraian_materi']) && is_array($onlineResult['uraian_materi'])) {
                foreach ($onlineResult['uraian_materi'] as &$chapter) {
                    if (! empty($chapter['subjudul'])) {
                        $chapter['subjudul'] = trim(preg_replace('/^Sub-Bab\s*\d+\s*[:\-–]?\s*/i', '', $chapter['subjudul']));
                    }
                    unset($chapter['gambar']);
                }
                unset($chapter);
            } else {
                // Jika AI hanya mengembalikan ringkasan, lengkapi dengan uraian terperinci
                $fallback = self::buildFallbackInteractiveMaterial($subjectName, $fase, $topic, $contentType, $videoUrl, $teacherNotes, $elemen, $capaianPembelajaran, $tujuanPembelajaran, $alurTujuanPembelajaran);
                $onlineResult['uraian_materi'] = $fallback['uraian_materi'];
            }

            return $onlineResult;
        }

        // Pedagogical Fallback Generator (Permendikdasmen No. 13 Tahun 2025)
        return self::buildFallbackInteractiveMaterial($subjectName, $fase, $topic, $contentType, $videoUrl, $teacherNotes, $elemen, $capaianPembelajaran, $tujuanPembelajaran, $alurTujuanPembelajaran);
    }

    protected static function buildFallbackInteractiveMaterial(
        string $subjectName,
        string $fase,
        string $topic,
        string $contentType = 'text',
        ?string $videoUrl = null,
        ?string $teacherNotes = null,
        ?string $elemen = null,
        ?string $capaianPembelajaran = null,
        ?string $tujuanPembelajaran = null,
        ?string $alurTujuanPembelajaran = null
    ): array {
        $prefix = match ($contentType) {
            'video' => 'Modul Video Interaktif',
            'document' => 'Modul Dokumen Pembelajaran',
            'link' => 'Modul Eksplorasi Digital',
            default => 'Modul Interaktif',
        };

        $contextIntro = match ($contentType) {
            'video' => "Modul video pembelajaran ini menyajikan eksplorasi fenomena visual dan kajian interaktif untuk materi {$topic}. Peserta didik diarahkan untuk mengamati tayangan, menelaah variabel fisis/keilmuan, dan menguji pemahaman konsep secara langsung.",
            'document' => "Dokumen modul pembelajaran ini dirancang terstruktur guna memandu literasi mendalam (deep reading) siswa pada kajian {$topic}, dilengkapi pembahasan terperinci, kartu konsep esensial, dan latihan bernalar kritis.",
            'link' => "Modul berbasis tautan digital ini memfasilitasi penelusuran terpandu (guided inquiry) peserta didik terhadap data dan artikel edukasi daring terkait {$topic}.",
            default => "Materi {$topic} merupakan bagian integral dari penguasaan {$subjectName} jenjang SMA. Dalam kurikulum Permendikdasmen No. 13 Tahun 2025, pembelajaran diarahkan untuk membangun pemahaman konseptual yang mendalam (deep learning) dan kemampuan transfer pengetahuan, bukan sekadar hafalan rumus atau teori.",
        };

        $tpArray = [];
        if ($tujuanPembelajaran) {
            $tpLines = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $tujuanPembelajaran))));
            foreach ($tpLines as $line) {
                $tpArray[] = preg_replace('/^\d+[\.\)]\s*/', '', $line);
            }
        }
        if (empty($tpArray)) {
            $tpArray = [
                "Mengidentifikasi dan mendeskripsikan prinsip utama pada materi {$topic} secara terstruktur dan komprehensif.",
                "Menganalisis hubungan sebab-akibat, dinamika variabel, dan aplikasi konsep {$topic} dalam pemecahan masalah kontekstual.",
                'Mengembangkan pemikiran analitis dan bernalar kritis melalui studi kasus serta evaluasi konsep bermakna.',
            ];
        }

        $uraianMateri = [
            [
                'nomor' => 1,
                'subjudul' => "Fondasi Konseptual & Landasan Ilmiah {$topic}",
                'konten_lengkap' => "Kajian mengenai {$topic} berakar pada kebutuhan untuk memahami pola keteraturan dalam bidang {$subjectName}. Pada tingkat SMA, pemahaman konsep tidak lagi sebatas mengenali definisi tekstual, melainkan menelusuri bagaimana fenomena ini terbentuk dari interaksi elemen-elemen fundamental.\n\nDalam regulasi Permendikdasmen No. 13 Tahun 2025, setiap peserta didik diarahkan untuk mengidentifikasi variabel batas, mengamati kondisi awal suatu sistem, dan mengenali parameter apa saja yang memicu terjadinya perubahan. Penguasaan fondasi teoretis ini menjadi pijakan utama sebelum melangkah pada kalkulasi matematis atau analisis sebab-akibat yang lebih kompleks.",
                'analogi' => "Bayangkan sebuah rumah megah yang kokoh; fondasi di bawah tanah tidak terlihat langsung dari luar, namun tanpa fondasi yang presisi, seluruh struktur di atasnya akan rentan roboh saat diterpa guncangan. Begitu pula pemahaman konsep dasar {$topic} ini.",
                'poin_kunci' => [
                    "Identifikasi kondisi batas dan asumsi ideal yang berlaku pada materi {$topic}.",
                    'Pemisahan antara konsep esensial dengan variabel pengganggu dalam eksperimen atau fenomena nyata.',
                    'Koneksi logis antara hukum dasar keilmuan dengan fenomena yang diamati secara visual.',
                ],
            ],
            [
                'nomor' => 2,
                'subjudul' => 'Analisis Mekanisme, Hubungan Variabel & Dinamika Proses',
                'konten_lengkap' => "Setelah memahami fondasi dasar, tahapan berikutnya adalah mengurai mekanisme internal yang menggerakkan sistem pada materi {$topic}. Hubungan antarvariabel dalam kajian ini umumnya mengikuti kaidah proporsionalitas—baik berbanding lurus, berbanding terbalik, maupun eksponensial tergantung pada kondisi interaksi sistem.\n\nPeserta didik dilatih untuk membaca pola perubahan: jika satu variabel dinaikkan secara bertahap, bagaimana respon dari variabel lainnya? Analisis kuantitatif dan kualitatif ini mengasah nalar kritis tingkat tinggi (HOTS), sehingga siswa mampu memprediksi luaran sistem secara ilmiah dan akurat tanpa tebak-tebakan.",
                'analogi' => 'Ibarat mengatur volume air dan diameter kran pada selang taman; jika diameter kran dipersempit sementara debit pompa tetap, maka tekanan semburan akan melonjak tajam. Hubungan saling mempengaruhi antarvariabel inilah yang dipelajari pada bab ini.',
                'poin_kunci' => [
                    'Pola hubungan kausalitas (sebab-akibat) yang mengatur perilaku sistem.',
                    'Teknik mengisolasi variabel bebas, variabel terikat, dan variabel kontrol dalam penyelidikan.',
                    'Interpretasi grafik atau data numerik untuk memvalidasi hipotesis ilmiah.',
                ],
            ],
            [
                'nomor' => 3,
                'subjudul' => 'Implementasi Kontekstual, Studi Rekayasa & Relevansi Masa Depan',
                'konten_lengkap' => "Kurikulum Nasional Permendikdasmen No. 13 Tahun 2025 menekankan pembelajaran bermakna (meaningful learning). Teori mengenai {$topic} bukan sekadar bahan ujian di kelas, melainkan teknologi dan prinsip yang telah merevolusi berbagai sektor kehidupan nyata di Indonesia dan kancah global.\n\nMulai dari efisiensi energi di sektor transportasi, rekayasa material cerdas, otomatisasi sistem produksi, hingga mitigasi bencana lingkungan—semuanya bertumpu pada optimasi prinsip-prinsip ini. Dengan memahami bab ini secara komprehensif, siswa dibekali kecakapan berpikir solutif guna merespons tantangan abad ke-21.",
                'analogi' => 'Seperti seorang arsitek yang merancang jembatan layang modern; ia tidak hanya menghafal kekuatan beton, melainkan memperhitungkan beban dinamis kendaraan, hembusan angin badai, hingga estetika kota agar jembatan berfungsi optimal dan aman selama puluhan tahun.',
                'poin_kunci' => [
                    "Aplikasi nyata prinsip {$topic} dalam kemajuan teknologi industri 4.0 dan kecerdasan buatan.",
                    'Pendekatan interdisipliner: memadukan sains, teknologi, rekayasa, dan matematika (STEM).',
                    'Etika pemanfaatan keilmuan dan keberlanjutan lingkungan hidup bagi generasi mendatang.',
                ],
            ],
        ];

        return [
            'title' => "{$prefix} {$subjectName}: {$topic}",
            'topic' => $topic,
            'materi' => $topic,
            'elemen' => $elemen ?: "Pemahaman {$subjectName}",
            'target_fase' => $fase,
            'tipe_konten' => $contentType,
            'catatan_guru' => $teacherNotes,
            'capaian_pembelajaran' => $capaianPembelajaran ?: "Peserta didik mampu menganalisis konsep dasar {$topic}, menghubungkan prinsip keilmuan dengan fenomena kontekstual, serta menunjukkan penguasaan kompetensi sesuai Capaian Pembelajaran Permendikdasmen No. 13 Tahun 2025 ({$fase}).",
            'tujuan_pembelajaran' => $tpArray,
            'alur_tujuan_pembelajaran' => $alurTujuanPembelajaran ?: 'Pengenalan Konsep -> Analisis Variabel -> Sintesis Solusi Masalah Nyata',
            'profil_pelajar_pancasila' => [
                'Bernalar Kritis: Menganalisis informasi, mengevaluasi bukti empiris, dan menarik kesimpulan berbasis data ilmiah.',
                'Mandiri: Memiliki prakarsa atas pengembangan diri dan ketekunan dalam belajar terpandu.',
                'Kreatif & Inovatif: Mengaitkan materi dengan solusi nyata bagi masyarakat dan kemajuan teknologi.',
            ],
            'ringkasan_materi' => "{$contextIntro}\n\nPada pembelajaran ini, siswa diajak mengamati fenomena sekitar, menelaah variabel yang berpengaruh, dan merumuskan penalaran logis. Pendekatan kontekstual memastikan bahwa setiap konsep memiliki relevansi langsung dengan kemajuan teknologi dan kehidupan bermasyarakat.\n\nModul ini menyajikan uraian terperinci lengkap dengan analogi mudah dipahami, kartu konsep interaktif, serta evaluasi mandiri.",
            'uraian_materi' => $uraianMateri,
            'kartu_konsep' => [
                [
                    'istilah' => 'Konsep Esensial',
                    'definisi' => "Prinsip utama yang melandasi kajian {$topic} dalam bidang {$subjectName}.",
                    'contoh' => 'Penerapan pada sistem kehidupan, industri, dan lingkungan hidup.',
                ],
                [
                    'istilah' => 'Analisis Variabel',
                    'definisi' => 'Identifikasi faktor-faktor yang saling mempengaruhi dalam suatu model pemecahan masalah.',
                    'contoh' => 'Menghitung hubungan antara input dan output pada sistem eksperimen.',
                ],
                [
                    'istilah' => 'Model Kontekstual',
                    'definisi' => 'Representasi ilmiah untuk menyederhanakan fenomena nyata agar dapat dianalisis secara akurat.',
                    'contoh' => 'Simulasi grafis atau perumusan matematis dari gejala alam atau sosial.',
                ],
                [
                    'istilah' => 'Evaluasi Kritis',
                    'definisi' => 'Proses memverifikasi hasil analisis berdasarkan bukti empiris dan teori yang sahih.',
                    'contoh' => 'Membandingkan data teoritis dengan hasil pengamatan lapangan.',
                ],
            ],
            'cek_pemahaman' => [
                [
                    'pertanyaan' => "Berdasarkan prinsip utama pada materi {$topic}, langkah pertama yang paling tepat dilakukan dalam menganalisis suatu permasalahan kontekstual adalah...",
                    'opsi' => [
                        'A' => 'Mengidentifikasi variabel dan kondisi batas permasalahan secara terstruktur',
                        'B' => 'Langsung mencari kesimpulan akhir tanpa verifikasi data awal',
                        'C' => 'Mengabaikan teori dasar dan mengandalkan asumsi subjektif',
                        'D' => 'Menghindari analisis fenomena nyata karena terlalu kompleks',
                    ],
                    'kunci' => 'A',
                    'penjelasan' => 'Sesuai kaidah penalaran ilmiah pada Permendikdasmen 13/2025, langkah fundamental adalah mengenali variabel serta batasan masalah sebelum melakukan pemodelan dan penarikan kesimpulan.',
                ],
                [
                    'pertanyaan' => "Mengapa pemahaman kontekstual pada materi {$topic} penting diterapkan dalam kehidupan sehari-hari?",
                    'opsi' => [
                        'A' => 'Hanya untuk memenuhi syarat nilai angka di buku rapor',
                        'B' => 'Agar siswa mampu mengaitkan teori ilmiah dengan solusi permasalahan nyata di masyarakat',
                        'C' => 'Karena konsep ini tidak berhubungan dengan kemajuan teknologi modern',
                        'D' => 'Untuk menghafalkan seluruh definisi istilah tanpa praktik analitis',
                    ],
                    'kunci' => 'B',
                    'penjelasan' => 'Kurikulum Merdeka dan Permendikdasmen No. 13 Tahun 2025 menekankan pembelajaran bermakna (meaningful learning) di mana konsep keilmuan ditujukan untuk memecahkan tantangan kehidupan nyata.',
                ],
            ],
            'studi_kasus' => "Sebuah tim inovasi di sekolah sedang merancang solusi berbasis konsep {$topic}. Mereka menemukan adanya kendala efisiensi dalam implementasi di lapangan. Sebagai peserta didik yang telah mempelajari bab ini, diskusikan bagaimana prinsip-prinsip utama {$topic} dapat dioptimalkan untuk memecahkan permasalahan efisiensi tersebut.",
            'glosarium' => [
                ['kata' => 'Deep Learning', 'arti' => 'Pembelajaran mendalam yang fokus pada pemahaman konsep dan penalaran tinggi'],
                ['kata' => 'Kontekstual', 'arti' => 'Keterkaitan materi pembelajaran dengan situasi dunia nyata peserta didik'],
            ],
            'refleksi_siswa' => "Apa bagian dari materi {$topic} yang paling menarik bagi Anda, dan bagaimana Anda dapat menerapkannya dalam kehidupan sehari-hari?",
        ];
    }

    // =========================================================================
    // 2. GENERATE TUGAS AUTENTIK / PR MANDIRI (PERMENDIKDASMEN 13/2025)
    // =========================================================================

    public static function generateAssignment(
        CourseClass $courseClass,
        string $topic,
        string $taskType = 'tugas_autentik',
        ?string $elemen = null,
        ?string $capaianPembelajaran = null,
        ?string $tujuanPembelajaran = null,
        ?string $alurTujuanPembelajaran = null,
        ?string $targetFase = null
    ): array {
        $subjectName = $courseClass->subject->name;
        $className = $courseClass->schoolClass->name;
        $grade = $targetFase ?: ($courseClass->subject->grade ?: 'Fase E / F (SMA)');

        $typeLabel = match ($taskType) {
            'pr_mandiri' => 'Tugas & Pekerjaan Rumah (PR) Mandiri Terstruktur',
            'studi_kasus' => 'Penugasan Analisis Studi Kasus Kontekstual',
            'proyek_mini' => 'Penugasan Proyek Mini Berbasis Masalah (PBL)',
            default => 'Penugasan Pembelajaran Autentik Bermakna',
        };

        $elemenContext = $elemen ? trim($elemen) : "Pemahaman {$subjectName}";
        $cpContext = $capaianPembelajaran ? trim($capaianPembelajaran) : "Peserta didik mampu menganalisis konsep {$topic} dan mengaplikasikannya dalam pemecahan masalah kontekstual sesuai regulasi Permendikdasmen No. 13 Tahun 2025 ({$grade}).";
        $tpContext = $tujuanPembelajaran ? trim($tujuanPembelajaran) : "Menganalisis prinsip {$topic}, mengevaluasi variabel fisis/keilmuan, dan menyusun laporan berbasis bukti valid.";
        $atpContext = $alurTujuanPembelajaran ? trim($alurTujuanPembelajaran) : 'Pengenalan materi -> Penyelidikan mandiri/kelompok -> Penyusunan solusi analitis.';

        $prompt = <<<PROMPT
Anda adalah Pakar Kurikulum Nasional Permendikdasmen No. 13 Tahun 2025 untuk jenjang SMA.
Tugas Anda: Buatlah instrumen penugasan/PR autentik berkualitas yang HASILNYA SEPENUHNYA BERDASARKAN DARI FORM KURIKULUM NASIONAL BERIKUT:
- Mata Pelajaran: {$subjectName}
- Kelas: {$className}
- Target Fase Kurikulum Nasional: {$grade}
- Elemen: {$elemenContext}
- Materi Penugasan: {$topic}
- Capaian Pembelajaran (CP) dari Guru: {$cpContext}
- Tujuan Pembelajaran (TP) dari Guru: {$tpContext}
- Alur Tujuan Pembelajaran (ATP) dari Guru: {$atpContext}
- Jenis Penugasan: {$typeLabel}

INSTRUKSI UTAMA & MUTLAK:
AI WAJIB MENGENERATE PENUGASAN & PR YANG HASILNYA SEPENUHNYA BERDASARKAN FORM YANG DIISI GURU DI ATAS:
1. Judul penugasan harus mencerminkan materi {$topic} dan Elemen {$elemenContext}.
2. Tujuan penugasan harus secara eksplisit menguji pencapaian Tujuan Pembelajaran (TP) dan Capaian Pembelajaran (CP) di atas.
3. Langkah-langkah instruksi kerja siswa harus memandu siswa mengikuti tahapan Alur Tujuan Pembelajaran (ATP).
4. Rubrik Penilaian KKTP (Kriteria Ketercapaian Tujuan Pembelajaran) wajib mengevaluasi penguasaan indikator TP tersebut dalam 4 tingkatan standar: Baru Berkembang (<70), Layak (70-79), Cakap (80-89), Mahir (90-100).

Kembalikan dalam JSON valid dengan struktur:
{
  "title": "Judul penugasan yang spesifik dan jelas",
  "topic": "{$topic}",
  "materi": "{$topic}",
  "elemen": "{$elemenContext}",
  "target_fase": "{$grade}",
  "capaian_pembelajaran": "{$cpContext}",
  "tujuan_pembelajaran": "{$tpContext}",
  "alur_tujuan_pembelajaran": "{$atpContext}",
  "instructions": "Instruksi pengerjaan lengkap (tujuan berbasis TP, langkah-langkah kerja berurutan mengikuti ATP, format luaran tugas, serta ketentuan pengumpulan)",
  "rubrik_penilaian": "Rubrik Penilaian KKTP (Kriteria Ketercapaian Tujuan Pembelajaran) dalam 4 tingkatan: Baru Berkembang (<70), Layak (70-79), Cakap (80-89), Mahir (90-100)",
  "max_score": 100,
  "formatted_text": "Teks gabungan instruksi dan rubrik yang siap dimasukkan ke form LMS"
}
PROMPT;

        $onlineResult = self::callGemini($prompt, 0.4);
        if ($onlineResult && isset($onlineResult['title'], $onlineResult['instructions'])) {
            if (! str_contains($onlineResult['title'], $topic)) {
                $onlineResult['title'] = "{$typeLabel} {$topic}: {$onlineResult['title']}";
            }

            if (! str_contains($onlineResult['instructions'], 'KKTP')) {
                if (str_contains($onlineResult['instructions'], 'Rubrik Penilaian:')) {
                    $onlineResult['instructions'] = str_replace('Rubrik Penilaian:', "Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) & Rubrik Penilaian:\n", $onlineResult['instructions']);
                } else {
                    $onlineResult['instructions'] .= "\n\nKriteria Ketercapaian Tujuan Pembelajaran (KKTP): Berbasis 4 tingkatan standar Permendikdasmen No. 13 Tahun 2025.";
                }
            }

            $onlineResult['elemen'] = $elemenContext;
            $onlineResult['target_fase'] = $grade;
            $onlineResult['materi'] = $topic;
            $onlineResult['capaian_pembelajaran'] = $cpContext;
            $onlineResult['tujuan_pembelajaran'] = $tpContext;
            $onlineResult['alur_tujuan_pembelajaran'] = $atpContext;

            return $onlineResult;
        }

        // Fallback Assignment Generator
        $instructions = "PENUGASAN {$typeLabel}\nMATA PELAJARAN: {$subjectName}\nTARGET FASE: {$grade}\nELEMEN: {$elemenContext}\nMATERI: {$topic}\n\nA. CAPAIAN & TUJUAN PEMBELAJARAN (CP/TP/ATP):\n- Capaian Pembelajaran: {$cpContext}\n- Tujuan Pembelajaran: {$tpContext}\n- Alur Tujuan Pembelajaran: {$atpContext}\n\nB. PETUNJUK PENGERJAAN TUGAS:\n1. Pelajari kembali materi modul dan bahan ajar {$topic}.\n2. Kerjakan tugas secara sistematis mengikuti tahapan penyelidikan ilmiah dan penalaran analitis.\n3. Format penyerahan: File PDF atau ketik langsung pada form pengumpulan LMS.\n4. Kumpulkan sebelum batas waktu (deadline) yang telah ditetapkan guru.\n\nC. KRITERIA KETERCAPAIAN TUJUAN PEMBELAJARAN (KKTP):\n- Mahir (90-100): Analisis mendalam, menyertakan solusi inovatif, data lengkap, dan pembuktian konsep TP tercapai sempurna.\n- Cakap (80-89): Analisis tepat, konsep materi dipahami dengan baik, dan argumentasi logis sesuai TP.\n- Layak (70-79): Konsep dasar terpenuhi namun penjelasan masih membutuhkan penguatan pada bagian analitis.\n- Baru Berkembang (<70): Masih memerlukan bimbingan intensif guru dalam memahami kompetensi dasar materi.";

        return [
            'title' => "{$typeLabel}: {$topic}",
            'topic' => $topic,
            'materi' => $topic,
            'elemen' => $elemenContext,
            'target_fase' => $grade,
            'capaian_pembelajaran' => $cpContext,
            'tujuan_pembelajaran' => $tpContext,
            'alur_tujuan_pembelajaran' => $atpContext,
            'instructions' => $instructions,
            'rubrik_penilaian' => 'KKTP: Baru Berkembang (<70), Layak (70-79), Cakap (80-89), Mahir (90-100)',
            'max_score' => 100,
            'formatted_text' => $instructions,
        ];
    }

    // =========================================================================
    // 3. GENERATE PAKET ASESMEN CBT (PERMENDIKDASMEN 13/2025)
    // =========================================================================

    public static function generateCbtHeader(
        CourseClass $courseClass,
        string $topic,
        string $quizType = 'formatif',
        int $duration = 60,
        ?string $elemen = null,
        ?string $capaianPembelajaran = null,
        ?string $tujuanPembelajaran = null,
        ?string $alurTujuanPembelajaran = null,
        ?string $targetFase = null
    ): array {
        $subjectName = $courseClass->subject->name;
        $kkm = $courseClass->subject->kkm ?? 75;
        $fase = $targetFase ?: ($courseClass->subject->grade ?: 'Fase E / F (SMA)');

        $typeTitle = match ($quizType) {
            'formatif' => 'Asesmen Formatif (CBT Cek Penguasaan Materi)',
            'sumatif_materi' => 'Asesmen Sumatif Lingkup Materi',
            'sts' => 'Sumatif Tengah Semester (STS)',
            'sas' => 'Sumatif Akhir Semester (SAS)',
            default => 'Asesmen Pembelajaran Daring (CBT)',
        };

        $curriculumHeader = '';
        if ($elemen || $capaianPembelajaran || $tujuanPembelajaran) {
            $curriculumHeader = "\n\nINFORMASI KURIKULUM NASIONAL ({$fase}):";
            if ($elemen) {
                $curriculumHeader .= "\n- Elemen: {$elemen}";
            }
            if ($capaianPembelajaran) {
                $curriculumHeader .= "\n- Capaian Pembelajaran: {$capaianPembelajaran}";
            }
            if ($tujuanPembelajaran) {
                $curriculumHeader .= "\n- Tujuan Pembelajaran Diukur: {$tujuanPembelajaran}";
            }
            if ($alurTujuanPembelajaran) {
                $curriculumHeader .= "\n- Alur Tujuan Pembelajaran: {$alurTujuanPembelajaran}";
            }
        }

        $instructions = "PETUNJUK ASESMEN CBT (PERMENDIKDASMEN NO. 13 TAHUN 2025):\n1. Kerjakan asesmen ini secara mandiri, jujur, dan bertanggung jawab.\n2. Setiap butir soal dirancang menguji penalaran kritis (HOTS) dan pemahaman konsep mendalam berbasis Kurikulum Nasional.\n3. Pilihlah satu opsi jawaban (A, B, C, D, atau E) atau selesaikan instruksi butir soal sesuai tipe butir asesmen.\n4. Waktu pengerjaan adalah {$duration} menit. Pastikan menekan tombol 'Selesai & Kumpulkan' sebelum waktu habis.\n5. Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) asesmen ini adalah {$kkm}.{$curriculumHeader}";

        return [
            'title' => "{$typeTitle}: {$topic}",
            'topic' => $topic,
            'materi' => $topic,
            'elemen' => $elemen,
            'target_fase' => $fase,
            'capaian_pembelajaran' => $capaianPembelajaran,
            'tujuan_pembelajaran' => $tujuanPembelajaran,
            'alur_tujuan_pembelajaran' => $alurTujuanPembelajaran,
            'quiz_type' => $quizType,
            'duration_minutes' => $duration,
            'passing_score' => $kkm,
            'instructions' => $instructions,
        ];
    }

    // =========================================================================
    // 4. GENERATE BUTIR SOAL CBT STANDAR PERMENDIKDASMEN 13/2025 & AKM
    // (Mendukung: Pilihan Ganda, PG Kompleks, Benar/Salah, Menjodohkan, Isian Singkat)
    // =========================================================================

    public static function generateQuestionsForQuiz(
        LmsQuiz $quiz,
        string $topic,
        int|array $composition = 5,
        string $difficulty = 'HOTS',
        ?string $elemen = null,
        ?string $capaianPembelajaran = null,
        ?string $tujuanPembelajaran = null,
        ?string $alurTujuanPembelajaran = null,
        ?string $targetFase = null
    ): array {
        $courseClass = $quiz->courseClass;
        $subjectName = $courseClass->subject->name;
        $grade = $targetFase ?: ($courseClass->subject->grade ?: 'Fase E / F (SMA)');

        $elemenContext = $elemen ? trim($elemen) : "Pemahaman {$subjectName}";
        $cpContext = $capaianPembelajaran ? trim($capaianPembelajaran) : "Peserta didik menganalisis dan mengaplikasikan konsep {$topic} sesuai standar Permendikdasmen No. 13 Tahun 2025 jenjang {$grade}.";
        $tpContext = $tujuanPembelajaran ? trim($tujuanPembelajaran) : "Menguji penguasaan konsep esensial materi {$topic}, penalaran berbasis stimulus kontekstual, dan pemecahan masalah ilmiah.";
        $atpContext = $alurTujuanPembelajaran ? trim($alurTujuanPembelajaran) : 'Penguasaan Konsep -> Penalaran Hubungan Antarvariabel -> Aplikasi Pemecahan Masalah.';

        // Normalisasi komposisi butir soal per tipe
        $distribution = self::normalizeQuestionDistribution($composition);
        $totalCount = array_sum($distribution);
        if ($totalCount < 1) {
            $distribution = ['multiple_choice' => 5];
            $totalCount = 5;
        }

        $typeIndoNames = [
            'multiple_choice' => 'Pilihan Ganda (A s/d E)',
            'complex_multiple_choice' => 'Pilihan Ganda Kompleks (Multi Jawaban Benar)',
            'true_false' => 'Benar / Salah',
            'matching' => 'Menjodohkan (Premis vs Pilihan)',
            'short_answer' => 'Isian Singkat',
            'essay' => 'Uraian / Esai',
        ];
        $typeSummaryStr = [];
        foreach ($distribution as $type => $c) {
            if ($c > 0 && isset($typeIndoNames[$type])) {
                $typeSummaryStr[] = "- {$typeIndoNames[$type]}: {$c} butir soal";
            }
        }
        $rincianTipe = implode("\n", $typeSummaryStr);

        $prompt = <<<PROMPT
Bertindaklah sebagai Tim Ahli Penyusun Soal Standar Nasional Kemendikdasmen RI (Permendikdasmen No. 13 Tahun 2025 & AKM Asesmen Nasional).
Buatkan {$totalCount} butir soal Asesmen CBT yang HASILNYA SEPENUHNYA BERDASARKAN DARI FORM KURIKULUM NASIONAL BERIKUT:
- Mata Pelajaran: {$subjectName}
- Tingkat / Target Fase: {$grade}
- Elemen: {$elemenContext}
- Materi Pembelajaran: {$topic}
- Capaian Pembelajaran (CP) dari Guru: {$cpContext}
- Tujuan Pembelajaran (TP) dari Guru: {$tpContext}
- Alur Tujuan Pembelajaran (ATP) dari Guru: {$atpContext}
- Level Kesukaran: {$difficulty}
- Petunjuk Multi-Materi: Jika materi di atas mencakup beberapa ruang lingkup materi (dipisahkan koma atau baris baru), distribusikan {$totalCount} butir soal secara proporsional ke SELURUH ruang lingkup materi tersebut.
- Komposisi Tipe Soal yang WAJIB DIBUATKAN:
{$rincianTipe}

INSTRUKSI UTAMA & MUTLAK:
AI WAJIB MENGENERATE ASESMEN CBT YANG BUTIR SOALNYA SECARA KETAT MENGUJI KOMPETENSI PADA FORM DI ATAS:
1. Soal berstandar Asesmen Nasional (AKM/{$difficulty} Level C3-C5), memiliki stimulus kontekstual bermakna (data, fenomena alam/sosial, rekayasa teknologi, atau kasus nyata) yang relevan dengan Materi dan TP.
2. Setiap butir soal WAJIB dilengkapi:
   - "indikator_soal": Indikator kisi-kisi resmi yang spesifik mengukur pencapaian Tujuan Pembelajaran (TP) di atas.
   - "level_kognitif": Level kognitif (L1/L2/L3 atau C3/C4/C5).
   - "materi_pokok": Diisi materi spesifik/elemen terkait soal tersebut.
   - "pembahasan": Uraian ilmiah mendalam mengenai konsep TP/CP terkait.
3. Tipe soal HARUS SESUAI dengan rincian di atas:
   - 'multiple_choice': Pilihan ganda dengan 5 opsi (A, B, C, D, E), 1 jawaban benar.
   - 'complex_multiple_choice': Pilihan ganda kompleks, opsi A s/d E dengan 2 atau 3 pernyataan benar (kunci berupa array ["A", "C"]).
   - 'true_false': Pernyataan analitis ilmiah dengan pilihan {"A": "Benar", "B": "Salah"}.
   - 'matching': Menjodohkan premis dengan pasangan opsi yang tepat {"premises": {"1": "...", "2": "..."}, "choices": {"A": "...", "B": "..."}}.
   - 'short_answer': Isian singkat berupa istilah/konsep kunci definitif.
   - 'essay': Uraian/esai mendalam (membutuhkan penjabaran konsep terstruktur, penalaran komprehensif, atau solusi analitis dengan rubrik pedoman penskoran).

Kembalikan HANYA dalam JSON valid berupa array objek:
[
  {
    "question_type": "multiple_choice",
    "stimulus": "Teks stimulus kasus/fenomena nyata kontekstual...",
    "question_text": "Rumusan pertanyaan analitis yang jelas...",
    "indikator_soal": "Disajikan stimulus ..., peserta didik dapat [kompetensi TP] ...",
    "level_kognitif": "L3 / C4 (Penalaran)",
    "materi_pokok": "{$topic}",
    "options": {
      "A": "Pernyataan opsi A",
      "B": "Pernyataan opsi B",
      "C": "Pernyataan opsi C",
      "D": "Pernyataan opsi D",
      "E": "Pernyataan opsi E"
    },
    "correct_answer": "A",
    "score_weight": 10,
    "pembahasan": "Penjelasan mengapa kunci tersebut benar berbasis konsep CP dan TP terkait."
  }
]
PROMPT;

        $onlineResult = self::callGemini($prompt, 0.35);
        if ($onlineResult && is_array($onlineResult) && count($onlineResult) > 0 && isset($onlineResult[0]['question_text'])) {
            return self::saveGeneratedQuestions($quiz, $onlineResult, $topic);
        }

        // Fallback question generation komprehensif hingga 50 butir soal
        $fallbackQuestions = self::buildFallbackQuestions(
            $subjectName,
            $grade,
            $topic,
            $distribution,
            $elemenContext,
            $cpContext,
            $tpContext,
            $atpContext
        );

        return self::saveGeneratedQuestions($quiz, $fallbackQuestions, $topic);
    }

    protected static function normalizeQuestionDistribution(int|array $composition): array
    {
        $defaults = [
            'multiple_choice' => 0,
            'complex_multiple_choice' => 0,
            'true_false' => 0,
            'matching' => 0,
            'short_answer' => 0,
            'essay' => 0,
        ];

        if (is_int($composition)) {
            $total = max(1, min(50, $composition));
            $defaults['multiple_choice'] = $total;

            return $defaults;
        }

        if (is_array($composition)) {
            $total = 0;
            foreach ($defaults as $type => $val) {
                if (isset($composition[$type])) {
                    $cnt = max(0, min(50, (int) $composition[$type]));
                    $defaults[$type] = $cnt;
                    $total += $cnt;
                }
            }

            if ($total === 0) {
                $totalGiven = isset($composition['total']) ? (int) $composition['total'] : 5;
                $defaults['multiple_choice'] = max(1, min(50, $totalGiven));
            } elseif ($total > 50) {
                // Skala proporsional agar tidak melebihi 50
                $factor = 50 / $total;
                foreach ($defaults as $k => $v) {
                    $defaults[$k] = max(1, (int) floor($v * $factor));
                }
            }
        }

        return $defaults;
    }

    protected static function saveGeneratedQuestions(LmsQuiz $quiz, array $questionsData, string $defaultTopic = ''): array
    {
        $currentOrder = LmsQuizQuestion::where('quiz_id', $quiz->id)->max('order_number') ?? 0;
        $saved = [];

        foreach ($questionsData as $q) {
            $currentOrder++;
            $type = $q['question_type'] ?? 'multiple_choice';
            $validTypes = ['multiple_choice', 'complex_multiple_choice', 'true_false', 'matching', 'short_answer', 'essay'];
            if (! in_array($type, $validTypes, true)) {
                $type = 'multiple_choice';
            }

            $options = $q['options'] ?? null;
            $correct = $q['correct_answer'] ?? 'A';
            if (is_array($correct)) {
                $correctStr = json_encode($correct, JSON_UNESCAPED_UNICODE);
            } else {
                $correctStr = trim((string) $correct);
            }

            $questionText = trim((string) ($q['question_text'] ?? 'Pertanyaan pemahaman konsep'));
            $stimulus = ! empty($q['stimulus']) ? trim((string) $q['stimulus']) : null;
            $explanation = ! empty($q['pembahasan']) ? trim((string) $q['pembahasan']) : (! empty($q['explanation']) ? trim((string) $q['explanation']) : null);
            $indikator = ! empty($q['indikator_soal']) ? trim((string) $q['indikator_soal']) : 'Peserta didik mampu menganalisis konsep dan penerapan materi '.($defaultTopic ?: $quiz->title);
            $level = ! empty($q['level_kognitif']) ? trim((string) $q['level_kognitif']) : 'L3 / C4 (Penalaran)';
            $materi = ! empty($q['materi_pokok']) ? trim((string) $q['materi_pokok']) : ($defaultTopic ?: $quiz->title);

            $saved[] = LmsQuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => $questionText,
                'stimulus' => $stimulus,
                'question_type' => $type,
                'indikator_soal' => $indikator,
                'level_kognitif' => $level,
                'materi_pokok' => $materi,
                'options' => $options,
                'correct_answer' => $correctStr,
                'pembahasan' => $explanation,
                'score_weight' => (int) ($q['score_weight'] ?? 10),
                'order_number' => $currentOrder,
            ]);
        }

        return $saved;
    }

    protected static function buildFallbackQuestions(
        string $subjectName,
        string $grade,
        string $topic,
        array $distribution,
        ?string $elemen = null,
        ?string $capaianPembelajaran = null,
        ?string $tujuanPembelajaran = null,
        ?string $alurTujuanPembelajaran = null
    ): array {
        $questions = [];
        $itemCounter = 1;
        $topicsList = array_values(array_filter(array_map('trim', preg_split('/[,;\r\n]+/', $topic))));
        if (empty($topicsList)) {
            $topicsList = [$topic];
        }

        $elemenContext = $elemen ? trim($elemen) : "Pemahaman {$subjectName}";
        $tpContext = $tujuanPembelajaran ? trim($tujuanPembelajaran) : "menganalisis konsep {$topic} secara mendalam";

        foreach ($distribution as $type => $count) {
            for ($k = 1; $k <= $count; $k++) {
                $num = $itemCounter++;
                $subTopic = $topicsList[($num - 1) % count($topicsList)];
                if ($type === 'multiple_choice') {
                    $questions[] = [
                        'question_type' => 'multiple_choice',
                        'stimulus' => "Stimulus {$num}: Dalam penyelidikan sains dan kajian '{$subTopic}' pada {$grade} (Elemen: {$elemenContext}), seorang peneliti mengamati interaksi antar variabel fenomena untuk mencapai tujuan: {$tpContext}.",
                        'question_text' => "Berdasarkan stimulus tersebut, jika variabel utama pada materi '{$subTopic}' ditingkatkan secara proporsional, manakah kesimpulan analisis yang paling tepat dan sahih?",
                        'indikator_soal' => "Disajikan stimulus fenomena {$subTopic} (Elemen: {$elemenContext}), peserta didik dapat {$tpContext} secara terukur dan tepat.",
                        'level_kognitif' => 'L3 / C4 (Penalaran)',
                        'materi_pokok' => $subTopic,
                        'options' => [
                            'A' => "Sistem memberikan respon proporsional sesuai kaidah hukum dasar {$subTopic}",
                            'B' => 'Tidak terjadi perubahan kondisi sama sekali karena variabel terisolasi',
                            'C' => 'Respon sistem bersifat acak tanpa mengikuti pola keteraturan ilmiah',
                            'D' => 'Variabel kontrol berubah drastis melebihi batas toleransi eksperimen',
                            'E' => 'Kaidah keilmuan tidak dapat diaplikasikan pada fenomena kontekstual tersebut',
                        ],
                        'correct_answer' => 'A',
                        'score_weight' => 10,
                        'pembahasan' => "Sesuai kaidah kurikulum Permendikdasmen No. 13 Tahun 2025 jenjang {$grade} (Elemen: {$elemenContext}), hubungan variabel dalam kajian {$subTopic} relevan dengan tujuan pembelajaran: {$tpContext}.",
                    ];
                } elseif ($type === 'complex_multiple_choice') {
                    $questions[] = [
                        'question_type' => 'complex_multiple_choice',
                        'stimulus' => "Stimulus {$num}: Pengamatan mendalam fenomena {$subTopic} (Elemen: {$elemenContext}) menunjukkan adanya beberapa faktor penentu yang mempengaruhi efisiensi dan stabilitas sistem kontekstual.",
                        'question_text' => 'Berdasarkan stimulus di atas, manakah pernyataan-pernyataan berikut yang BENAR? (Pilihlah lebih dari satu jawaban yang benar):',
                        'indikator_soal' => "Disajikan data kajian {$subTopic}, peserta didik dapat mengidentifikasi beberapa faktor penentu keberhasilan sistem dengan bernalar kritis.",
                        'level_kognitif' => 'L3 / C5 (Penalaran Kritis)',
                        'materi_pokok' => $subTopic,
                        'options' => [
                            'A' => "Pengendalian kondisi awal sangat menentukan stabilitas hasil akhir sistem {$subTopic}",
                            'B' => 'Semua variabel dapat diabaikan tanpa mempengaruhi keandalan data pengamatan',
                            'C' => "Penerapan prinsip {$subTopic} dapat meningkatkan efisiensi energi dan sumber daya",
                            'D' => 'Pengukuran berulang tidak diperlukan karena hipotesis awal selalu mutlak',
                            'E' => 'Teknologi modern memanfaatkan prinsip ini untuk otomatisasi sistem berkelanjutan',
                        ],
                        'correct_answer' => json_encode(['A', 'C', 'E']),
                        'score_weight' => 15,
                        'pembahasan' => 'Pernyataan A, C, dan E merupakan prinsip esensial dalam pembelajaran mendalam (deep learning) Kurikulum Nasional Permendikdasmen 13/2025.',
                    ];
                } elseif ($type === 'true_false') {
                    $questions[] = [
                        'question_type' => 'true_false',
                        'stimulus' => "Stimulus {$num}: Dalam perkembangan teknologi mutakhir, pemahaman konsep {$subTopic} diaplikasikan untuk merekayasa solusi atas permasalahan kontekstual di masyarakat.",
                        'question_text' => "Pernyataan: 'Penerapan prinsip {$subTopic} secara terpadu mampu mengoptimalkan efisiensi energi dan mengurangi dampak risiko lingkungan secara signifikan.'\nTentukan apakah pernyataan ilmiah tersebut BENAR atau SALAH:",
                        'indikator_soal' => "Disajikan pernyataan telaah fenomena {$subTopic}, peserta didik dapat menilai kebenaran argumen ilmiah secara presisi.",
                        'level_kognitif' => 'L2 / C3 (Aplikasi)',
                        'materi_pokok' => $subTopic,
                        'options' => [
                            'A' => 'Benar',
                            'B' => 'Salah',
                        ],
                        'correct_answer' => 'Benar',
                        'score_weight' => 10,
                        'pembahasan' => 'Pernyataan tersebut benar karena orientasi Kurikulum Nasional menekankan kontribusi sains dan teknologi bagi keberlanjutan lingkungan hidup.',
                    ];
                } elseif ($type === 'matching') {
                    $questions[] = [
                        'question_type' => 'matching',
                        'stimulus' => "Stimulus {$num}: Materi {$subTopic} (Elemen: {$elemenContext}) memiliki pilar-pilar konseptual yang saling bertaut antara teori dasar, dinamika proses, dan implementasi nyata.",
                        'question_text' => 'Jodohkanlah elemen konsep pada Kolom Kiri dengan definisi/karakteristik yang tepat pada Kolom Kanan:',
                        'indikator_soal' => "Disajikan matriks konsep {$subTopic}, peserta didik dapat memasangkan istilah keilmuan dengan karakteristik kontekstualnya.",
                        'level_kognitif' => 'L2 / C3 (Aplikasi)',
                        'materi_pokok' => $subTopic,
                        'options' => [
                            'premises' => [
                                '1' => "Landasan Konseptual {$subTopic}",
                                '2' => 'Dinamika Mekanisme Sistem',
                                '3' => 'Implementasi Solutif di Masyarakat',
                            ],
                            'choices' => [
                                'A' => 'Aplikasi teknologi modern dalam memecahkan masalah kontekstual',
                                'B' => 'Pondasi teoretis dan hukum ilmiah yang mendasari fenomena',
                                'C' => 'Hubungan sebab-akibat dan interaksi timbal balik antarvariabel',
                            ],
                        ],
                        'correct_answer' => json_encode(['1' => 'B', '2' => 'C', '3' => 'A']),
                        'score_weight' => 15,
                        'pembahasan' => 'Pasangan yang benar: 1-B (Fondasi Teoretis), 2-C (Hubungan Antarvariabel), dan 3-A (Aplikasi Kontekstual).',
                    ];
                } elseif ($type === 'short_answer') {
                    $questions[] = [
                        'question_type' => 'short_answer',
                        'stimulus' => "Stimulus {$num}: Dalam evaluasi ketercapaian tujuan pembelajaran materi {$subTopic}, terdapat satu parameter utama yang menjadi tolak ukur keberhasilan integrasi sistem.",
                        'question_text' => "Berdasarkan stimulus di atas, tuliskan secara singkat istilah ilmiah untuk prinsip keteraturan yang menjaga kestabilan pada kajian {$subTopic}:",
                        'indikator_soal' => "Disajikan telaah kasus {$subTopic}, peserta didik dapat mengemukakan istilah/konsep kunci secara mandiri dan akurat.",
                        'level_kognitif' => 'L1 / C2 (Pemahaman Konseptual)',
                        'materi_pokok' => $subTopic,
                        'options' => [
                            'petunjuk' => 'Ketikkan kata/istilah kunci yang tepat dengan ejaan baku bahasa Indonesia',
                        ],
                        'correct_answer' => 'Keseimbangan Sistem',
                        'score_weight' => 10,
                        'pembahasan' => "Istilah ilmiah yang tepat adalah 'Keseimbangan Sistem' (atau ekuilibrium) yang menjadi fondasi utama dalam kajian {$subTopic}.",
                    ];
                } elseif ($type === 'essay') {
                    $questions[] = [
                        'question_type' => 'essay',
                        'stimulus' => "Stimulus {$num}: Dalam kehidupan nyata dan perkembangan teknologi kontemporer, permasalahan kontekstual mengenai materi '{$subTopic}' (Elemen: {$elemenContext}) menuntut pemecahan masalah kritis, komprehensif, dan berbasis kaidah ilmiah.",
                        'question_text' => "Berdasarkan stimulus tersebut, analisislah faktor-faktor penyebab terjadinya permasalahan di atas, jelaskan mekanisme keterkaitan antarvariabelnya, serta rumuskan minimal 2 rekomendasi solusi yang aplikatif sesuai konsep {$subTopic}!",
                        'indikator_soal' => "Disajikan stimulus kasus fenomena {$subTopic}, peserta didik dapat menganalisis permasalahan secara mendalam dan merumuskan solusi berbasis prinsip ilmiah dalam bentuk uraian analitis.",
                        'level_kognitif' => 'L3 / C5 (Penalaran & Sintesis)',
                        'materi_pokok' => $subTopic,
                        'options' => [
                            'petunjuk' => 'Tuliskan uraian analitis yang runut dan jelas, mencakup: (1) Faktor penyebab, (2) Mekanisme ilmiah keterkaitan variabel, dan (3) Rekomendasi solusi konkret.',
                        ],
                        'correct_answer' => "Pedoman Penskoran & Kunci Jawaban Esensial:\n1. Identifikasi faktor penyebab secara tepat (Bobot 30%)\n2. Penjelasan mekanisme ilmiah dan hubungan sebab-akibat (Bobot 40%)\n3. Perumusan minimal 2 solusi aplikatif yang logis dan relevan (Bobot 30%)",
                        'score_weight' => 20,
                        'pembahasan' => "Soal uraian HOTS menguji kemampuan peserta didik dalam menganalisis fenomena kompleks materi {$subTopic}, berpikir kritis dalam memecahkan masalah, dan mengomunikasikan gagasannya secara ilmiah.",
                    ];
                }
            }
        }

        return $questions;
    }
}

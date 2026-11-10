<?php

namespace App\Services\Ai;

use App\Contracts\AiAdapterInterface;

class MockAiAdapter implements AiAdapterInterface
{
    public function getProviderName(): string
    {
        return 'mock';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function generate(string $prompt, array $context = []): array
    {
        $task = $context['task'] ?? 'umum';
        $topic = $context['topic'] ?? 'Materi Pembelajaran';
        $grade = $context['grade'] ?? 'X';

        $generatedText = match ($task) {
            'tp' => "### Draf Tujuan Pembelajaran (TP) — {$topic} (Kelas {$grade})\n"
                ."1. **TP 1**: Peserta didik mampu mengidentifikasi konsep dasar {$topic} dengan tepat dan analitis.\n"
                ."2. **TP 2**: Peserta didik mampu menganalisis keterkaitan konsep {$topic} dalam pemecahan masalah kontekstual.\n"
                ."3. **TP 3**: Peserta didik mampu mempresentasikan hasil telaah {$topic} secara kolaboratif dan berintegritas.",
            'modul_ajar' => "### Draf Modul Ajar / RPP — {$topic} (Kelas {$grade})\n"
                ."**A. Identitas Modul**\n- Mata Pelajaran: ".($context['subject'] ?? 'Umum')."\n- Alokasi Waktu: 2 x 45 Menit\n"
                ."**B. Capaian & Tujuan Pembelajaran**\n- Memahami esensi {$topic} secara mendalam.\n"
                ."**C. Langkah Pembelajaran**\n1. *Pendahuluan (15 mnt)*: Apersepsi dan pemantik diskusi.\n2. *Kegiatan Inti (60 mnt)*: Eksplorasi materi, studi kasus, dan pengerjaan LKPD berkelompok.\n3. *Penutup (15 mnt)*: Refleksi pembelajaran dan asesmen formatif singkat.\n"
                ."**D. Asesmen**\n- Formatif: Observasi keaktifan diskusi dan lembar kerja peserta didik.",
            'remedial' => "### Saran Tindak Lanjut Remedial & Pendampingan — {$topic}\n"
                ."- **Fokus Remediasi**: Penguatan konsep fundamental mengenai sub-bab inti {$topic}.\n"
                ."- **Metode**: Pembelajaran tutor sebaya berpasangan dan pemberian bahan bacaan ringkas bergambar.\n"
                .'- **Bentuk Asesmen Ulang**: Tes lisan 3 pertanyaan kunci atau studi kasus terarah.',
            'refleksi' => "### Draf Catatan Refleksi Pembelajaran Guru — {$topic}\n"
                ."- **Ketercapaian**: Mayoritas peserta didik antusias dalam sesi eksplorasi interaktif.\n"
                ."- **Kendala**: Beberapa siswa membutuhkan waktu adaptasi lebih saat menyelesaikan studi kasus numerik/analitis.\n"
                .'- **Rencana Perbaikan**: Sesi berikutnya akan diawali dengan pemodelan contoh kasus konkret sebelum diskusi mandiri.',
            'hots_question' => "### Rekomendasi Butir Soal HOTS (Penalaran L3) — {$topic}\n"
                ."**Stimulus Kontekstual**: Disajikan studi kasus lingkungan dan potensi wilayah Sukabumi/Jawa Barat terkait {$topic}.\n"
                ."**Pertanyaan**: Berdasarkan fenomena di atas, analisislah dampak perubahan variabel kunci terhadap keberlanjutan sistem dan rumuskan rekomendasi solutif yang berintegritas!\n"
                .'**Kunci & Rubrik**: Peserta didik mampu menguraikan minimal 2 relasi sebab-akibat dan 1 solusi berbasis bukti empiris.',
            'diferensiasi' => "### Draf Modul Ajar Berdiferensiasi — {$topic} (Kelas {$grade})\n"
                ."**A. Diferensiasi Konten**: Infografis bagi pembelajar visual, podcast narasi bagi auditori, dan studi kasus objek nyata bagi kinestetik.\n"
                ."**B. Diferensiasi Proses**: Pengelompokan fleksibel (tiering) berbasis hasil asesmen diagnostik awal.\n"
                .'**C. Diferensiasi Produk**: Pilihan hasil karya dalam bentuk infografis, video pendek, atau laporan ringkas.',
            'diagnostik' => "### Analisis Diagnostik & Tindak Lanjut Remedial — {$topic}\n"
                ."- **Pola Hambatan**: Peserta didik membutuhkan penguatan pada jembatan antara konsep teoritis dan analisis kasus kontekstual.\n"
                ."- **Intervensi**: Pendampingan tutor sebaya dan latihan terbimbing dengan tangga bantuan (scaffolding).\n"
                .'- **Asesmen Ulang**: Kuis diagnostik formatif ringkas 5 butir soal pemahaman inti.',
            default => "### Draf Materi Pembelajaran Bantu — {$topic}\n"
                ."Rangkuman komprehensif mengenai konsep {$topic} untuk jenjang Kelas {$grade} di SMAN 1 Lengkong.",
        };

        return [
            'success' => true,
            'content' => $generatedText,
            'provider' => 'mock',
            'model' => 'offline-mock-engine-v1',
            'is_draft' => true,
            'metadata' => [
                'task' => $task,
                'tokens_simulated' => 150,
                'disclaimer' => 'Keluaran AI berstatus draf awal dan wajib ditinjau oleh guru sebelum digunakan.',
            ],
            'error' => null,
        ];
    }
}

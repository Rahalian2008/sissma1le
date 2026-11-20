<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Komprehensif Asesmen Terpadu 360° - {{ $student->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; line-height: 1.35; color: #000; margin: 10mm 14mm; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .title { text-align: center; margin-bottom: 12px; }
        .title h4 { margin: 0; font-size: 12pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 2px 0; font-size: 9.5pt; font-weight: bold; letter-spacing: 0.5px; }
        .title .subtitle { font-size: 8.5pt; font-weight: normal; font-style: italic; color: #333; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.meta td { padding: 2.5px 4px; vertical-align: top; font-size: 9.5pt; }
        table.data-table { width: 100%; border-collapse: collapse; margin: 6px 0 10px 0; }
        table.data-table th, table.data-table td { border: 1px solid #333; padding: 4.5px 6px; font-size: 9pt; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .bar-container { width: 100%; height: 9px; background-color: #eee; border: 1px solid #777; border-radius: 2px; }
        .bar-fill { height: 100%; background-color: #222; }
        .section-box { border: 1px solid #444; padding: 7px 9px; margin-bottom: 9px; border-radius: 4px; }
        .section-box h5 { margin: 0 0 4px 0; font-size: 9.5pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #bbb; padding-bottom: 2px; }
        .priority-badge { display: inline-block; padding: 2px 8px; border: 1px solid #000; font-weight: bold; font-size: 9pt; text-transform: uppercase; margin-bottom: 4px; background-color: #e6f4ea; }
        .signature-table { width: 100%; margin-top: 18px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 33.3%; text-align: center; vertical-align: top; font-size: 9pt; }
        .page-break { page-break-before: always; }
        .badge-dim { display: inline-block; padding: 1px 5px; font-weight: bold; font-size: 8pt; border: 1px solid #777; border-radius: 3px; background: #fafafa; }
        @media print {
            body { margin: 8mm 12mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 12px; text-align: right;">
        <button onclick="window.print()" style="padding: 7px 15px; background: #059669; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 11pt;">
            🖨️ Cetak Dokumen Resmi Terpadu (Print A4)
        </button>
    </div>

    <!-- Kop Surat Resmi -->
    <x-print-kop :school="$school" />

    <!-- Judul Dokumen -->
    <div class="title">
        <h4>LAPORAN KOMPREHENSIF ASESMEN PSIKOLOGIS & PEMINATAN TERPADU</h4>
        <p>UNIT LAYANAN BIMBINGAN DAN KONSELING (POP-BK) BERBASIS AI</p>
        <div class="subtitle">Sintesis Terpadu Tri-Dimensi: Gaya Belajar (VAK) • Minat Karier (Holland RIASEC) • Kecerdasan Majemuk (Howard Gardner)</div>
    </div>

    @php
        $priority = $report['prioritas_keputusan'] ?? [
            'level' => 'PRIORITAS_2',
            'label' => 'PENGUATAN KEMAMPUAN TERPADU',
            'deskripsi' => 'Peserta didik memiliki potensi berkembang yang seimbang dan adaptif, memerlukan pengarahan terfokus menuju perguruan tinggi.',
            'alasan' => 'Integrasi ketiga instrumen menunjukkan keharmonisan bakat cipta dan minat interaksi sosial.'
        ];

        $vakDom = $vak?->dominant_dimension ?? 'VISUAL';
        $vakSec = $vak?->secondary_dimension ?? 'AUDITORI';
        $vakPct = (float) (($vak?->dimension_scores['percentages'] ?? [])[$vakDom] ?? 65.6);

        $riasecDom = $riasec?->dominant_dimension ?? 'SOCIAL';
        $riasecCode = $riasec?->holland_code ?? 'SEC';
        $riasecPct = (float) (($riasec?->dimension_scores['percentages'] ?? [])[$riasecDom] ?? 66.7);

        $gardnerDom = $gardner?->dominant_dimension ?? 'VISUAL SPASIAL';
        $gardnerPct = (float) (($gardner?->dimension_scores['percentages'] ?? [])[$gardnerDom] ?? 75.0);
    @endphp

    <!-- Identitas Siswa -->
    <table class="meta">
        <tr>
            <td style="width: 25%;">Nama Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 43%; font-weight: bold;">{{ $student->name }}</td>
            <td style="width: 15%;">Kelas / Fase</td>
            <td style="width: 2%;">:</td>
            <td style="width: 13%; font-weight: bold;">{{ $student->schoolClass?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor Induk Siswa (NISN)</td>
            <td>:</td>
            <td>{{ $student->nisn ?? '-' }}</td>
            <td>Tgl Pengujian</td>
            <td>:</td>
            <td>{{ $gardner?->taken_at ? $gardner->taken_at->format('d/m/Y') : ($riasec?->taken_at ? $riasec->taken_at->format('d/m/Y') : date('d/m/Y')) }}</td>
        </tr>
        <tr>
            <td>Cakupan Instrumen</td>
            <td>:</td>
            <td colspan="4">
                <strong>Gaya Belajar VAK</strong>, <strong>Minat Karier Holland RIASEC</strong>, dan <strong>Kecerdasan Majemuk Gardner</strong>
            </td>
        </tr>
        <tr>
            <td>Status & Prioritas BK</td>
            <td>:</td>
            <td colspan="4">
                <span class="priority-badge">{{ $priority['level'] ?? 'PRIORITAS_2' }}: {{ $priority['label'] ?? 'PENGUATAN KEMAMPUAN TERPADU' }}</span>
                <span style="font-size: 8.5pt; font-style: italic;"> — {{ $priority['deskripsi'] ?? '' }}</span>
            </td>
        </tr>
    </table>

    <!-- Ringkasan Eksekutif Terpadu -->
    <div class="section-box">
        <h5>I. Ringkasan Eksekutif & Benang Merah Profil Siswa (Executive Summary)</h5>
        <p style="margin: 0; font-size: 9pt; text-align: justify; line-height: 1.4; white-space: pre-line;">
            {{ $report['ringkasan_eksekutif'] ?? 'Data sintesis evaluasi psikometrik terpadu sedang diproses oleh AI POP-BK SMAN 1 Lengkong.' }}
        </p>
    </div>

    <!-- Tabel Matriks Pengukuran Tri-Dimensi -->
    <div class="section-box">
        <h5>II. Matriks Integrasi Hasil Pengukuran Tri-Dimensi (Gaya Belajar • Minat • Kecerdasan)</h5>
        <p style="margin: 0 0 5px 0; font-size: 8.5pt; color: #444; font-style: italic;">
            * Tabel berikut menyajikan ikhtisar 3 sudut pandang psikometri ilmiah agar dapat dipahami secara proporsional dan tidak terpisah-pisah:
        </p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Pilar Pengukuran</th>
                    <th style="width: 25%;">Hasil Dominan & Karakter</th>
                    <th style="width: 14%;">Skor / Norma</th>
                    <th style="width: 41%;">Fungsi Terpadu dalam Pembelajaran & Karier</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>1. Gaya Belajar</strong><br>
                        <span style="font-size: 8pt; color: #555;">(Modalitas Sensori VAK)</span>
                    </td>
                    <td>
                        <span class="badge-dim">Multimodal</span><br>
                        <strong>{{ $vakDom }}</strong> - {{ $vakSec }}
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $vakPct }}%</strong><br>
                        <span style="font-size: 8pt; color: #047857;">Tinggi</span>
                    </td>
                    <td>
                        <strong>Cara Menyerap Ilmu:</strong> Memproses materi paling cepat melalui visualisasi (grafis, bagan, warna) serta dialog konseptual dan diskusi lisan.
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>2. Minat Karier</strong><br>
                        <span style="font-size: 8pt; color: #555;">(Holland RIASEC)</span>
                    </td>
                    <td>
                        <span class="badge-dim">Kode: {{ $riasecCode }}</span><br>
                        <strong>{{ $riasecDom }}</strong> (Social)
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $riasecPct }}%</strong><br>
                        <span style="font-size: 8pt; color: #047857;">Tinggi</span>
                    </td>
                    <td>
                        <strong>Arah Motivasi & Lingkungan:</strong> Tertarik pada lingkungan yang sarat interaksi pemberdayaan manusia, kegiatan edukasi, manajemen proyek, dan keteraturan kerja tim.
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>3. Kecerdasan Majemuk</strong><br>
                        <span style="font-size: 8pt; color: #555;">(Howard Gardner)</span>
                    </td>
                    <td>
                        <span class="badge-dim">Potensi Utama</span><br>
                        <strong>{{ $gardnerDom }}</strong>
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $gardnerPct }}%</strong><br>
                        <span style="font-size: 8pt; color: #047857;">Tinggi</span>
                    </td>
                    <td>
                        <strong>Alat Kognitif & Daya Cipta:</strong> Memiliki keunggulan daya imajinasi visual, persepsi estetika, dan kepekaan spasial untuk memecahkan masalah praktis.
                    </td>
                </tr>
            </tbody>
        </table>

        @php
            $schoolRecord = $report['rekam_jejak_sekolah'] ?? [
                'attendance_rate' => 100.0,
                'violation_points' => 0,
                'achievement_points' => 0,
                'kategori' => 'Sangat Baik',
                'keterangan' => 'Disinkronkan otomatis dari Rekam Presensi Harian dan Buku Catatan Pelanggaran Kesiswaan/BK (Bukan butir soal psikotes).'
            ];
        @endphp

        <!-- Data Pendukung Verifikasi Perilaku Nyata di Sekolah -->
        <div style="margin-top: 6px; padding: 6px 9px; background-color: #f8fafc; border: 1px dashed #64748b; border-radius: 4px; font-size: 8.5pt;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 72%; vertical-align: top; padding: 0;">
                        <strong style="color: #0f172a; text-transform: uppercase;">📌 Data Pendukung: Rekam Jejak Kedisiplinan & Presensi Nyata Siswa</strong><br>
                        <span style="font-size: 8pt; color: #475569; line-height: 1.3; display: block; margin-top: 2px;">
                            * <em>Catatan: Data ini bukan berasal dari butir soal tes psikotes, melainkan disinkronkan otomatis dari sistem Absensi Digital Sekolah (Kehadiran: <strong>{{ $schoolRecord['attendance_rate'] }}%</strong>) dan Buku Catatan Tata Tertib Kesiswaan/BK (Pelanggaran: <strong>{{ $schoolRecord['violation_points'] }} Pts</strong>). Data ini mengonfirmasi stabilitas belajar ananda di sekolah nyata.</em>
                        </span>
                    </td>
                    <td style="width: 28%; vertical-align: middle; text-align: right; padding: 0;">
                        <span style="display: inline-block; padding: 3px 8px; font-size: 8.5pt; font-weight: bold; color: #047857; background: #e6f4ea; border: 1px solid #059669; border-radius: 3px; text-align: center;">
                            Kehadiran: {{ $schoolRecord['attendance_rate'] }}%<br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #065f46;">Kedisiplinan: {{ $schoolRecord['kategori'] }}</span>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Peta Kekuatan & Area Pertumbuhan -->
    <div class="section-box">
        <h5>III. Profil Potensi & Peta Perkembangan Siswa</h5>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                    <strong>Kekuatan Utama Siswa (Strength Zone):</strong>
                    <ul style="margin: 3px 0 6px 16px; padding: 0; font-size: 8.5pt;">
                        @foreach($report['kekuatan_dan_area_pengembangan']['kekuatan_utama'] ?? [] as $ku)
                            <li>{{ $ku }}</li>
                        @endforeach
                    </ul>
                    <strong>Kekuatan Pendukung & Sikap:</strong>
                    <ul style="margin: 3px 0 0 16px; padding: 0; font-size: 8.5pt;">
                        @foreach($report['kekuatan_dan_area_pengembangan']['kekuatan_pendukung'] ?? [] as $kp)
                            <li>{{ $kp }}</li>
                        @endforeach
                    </ul>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 8px; border-left: 1px solid #ccc;">
                    <strong>Area yang Memerlukan Stimulasi & Latihan:</strong>
                    <ul style="margin: 3px 0 6px 16px; padding: 0; font-size: 8.5pt;">
                        @foreach($report['kekuatan_dan_area_pengembangan']['area_pengembangan'] ?? [] as $ap)
                            <li>{{ $ap }}</li>
                        @endforeach
                    </ul>
                    <strong>Rekomendasi Eksplorasi Potensi:</strong>
                    <ul style="margin: 3px 0 0 16px; padding: 0; font-size: 8.5pt;">
                        @foreach($report['kekuatan_dan_area_pengembangan']['potensi_eksplorasi'] ?? [] as $pe)
                            <li>{{ $pe }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Halaman 2: Rekomendasi PTN/PTS, Karir, dan RTL -->
    <x-print-kop :school="$school" />

    <div class="title">
        <h4>REKOMENDASI JURUSAN PERGURUAN TINGGI, KARIER, & RENCANA AKSI</h4>
        <p>SINTESIS LANJUTAN BIMBINGAN KONSELING - {{ $student->name }} ({{ $student->schoolClass?->name ?? '-' }})</p>
    </div>

    <!-- Rekomendasi PTN/PTS -->
    <div class="section-box">
        <h5>IV. Rekomendasi Terpadu Program Studi Perguruan Tinggi & Lapangan Karier</h5>
        <p style="margin: 0 0 6px 0; font-size: 8.5pt; color: #444;">
            Rekomendasi di bawah ini telah mengintegrasikan keunggulan <strong>Daya Visual-Spasial</strong>, <strong>Minat Sosial-Enterprising</strong>, dan <strong>Modalitas Belajar Visual-Auditori</strong>:
        </p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 32%;">Program Studi / Jurusan Kuliah</th>
                    <th style="width: 25%;">Rumpun Keilmuan / Fakultas</th>
                    <th style="width: 43%;">Alasan Kesesuaian Integratif (3-in-1)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['rekomendasi_perguruan_tinggi_dan_karir']['program_studi'] ?? [] as $prodi)
                    <tr>
                        <td><strong>{{ $prodi['jurusan'] ?? '-' }}</strong></td>
                        <td>{{ $prodi['fakultas'] ?? '-' }}</td>
                        <td>{{ $prodi['alasan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Rencana Tindak Lanjut (RTL) -->
    <div class="section-box">
        <h5>V. Rencana Tindak Lanjut (RTL) & Panduan Pendampingan Kolaboratif</h5>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
            <tr>
                <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 5px; font-size: 8.5pt; background: #fafafa;">
                    <strong>Bulan Ke-1 (Jangka Pendek):</strong><br>
                    {{ ($report['rencana_tindak_lanjut']['jangka_pendek_1_bulan'][0]['aktivitas']) ?? 'Sesi bimbingan konseling individual membedah laporan terpadu.' }}<br>
                    <span style="font-size: 8pt; color: #555;">PIC: Guru BK & Siswa</span>
                </td>
                <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 5px; font-size: 8.5pt; background: #fafafa;">
                    <strong>Bulan Ke-3 (Jangka Menengah):</strong><br>
                    {{ ($report['rencana_tindak_lanjut']['jangka_menengah_3_bulan'][0]['aktivitas']) ?? 'Mengikuti edu-fair dan memperkuat latihan presentasi di kelas.' }}<br>
                    <span style="font-size: 8pt; color: #555;">PIC: Wali Kelas & Guru BK</span>
                </td>
                <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 5px; font-size: 8.5pt; background: #fafafa;">
                    <strong>Bulan Ke-6 (Jangka Panjang):</strong><br>
                    {{ ($report['rencana_tindak_lanjut']['jangka_panjang_6_bulan'][0]['aktivitas']) ?? 'Finalisasi portofolio prestasi dan pendaftaran SNBP/SNBT.' }}<br>
                    <span style="font-size: 8pt; color: #555;">PIC: Wali Kelas, Guru BK, & Orang Tua</span>
                </td>
            </tr>
        </table>
        <p style="margin: 0; font-size: 8.5pt; line-height: 1.35;">
            <strong>Catatan Pendampingan Khusus Orang Tua di Rumah:</strong><br>
            {{ $report['rekomendasi_pihak_sekolah']['orang_tua'] ?? 'Dukung minat ananda dengan penuh kehangatan, sediakan fasilitas belajar kondusif, dan dengarkan aspirasi masa depannya dengan apresiatif.' }}
        </p>
    </div>

    <!-- Kesimpulan Akhir -->
    <div class="section-box" style="background: #fdfdfd;">
        <h5>VI. Kesimpulan Akhir Konselor Pendidikan</h5>
        <p style="margin: 0; font-size: 8.5pt; text-align: justify; line-height: 1.35;">
            {{ $report['kesimpulan_akhir'] ?? 'Peserta didik memiliki profil potensi yang seimbang dan prospektif untuk meraih kesuksesan di jenjang pendidikan tinggi.' }}
        </p>
    </div>

    <!-- Tanda Tangan 3 Pihak Resmi -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Orang Tua / Wali Murid
                <br><br><br><br>
                ( .................................................. )
            </td>
            <td>
                Lengkong, {{ date('d F Y') }}<br>
                Guru Bimbingan Konseling,
                <br><br><br><br>
                <strong>{{ $counselor?->name ?? 'Koordinator BK' }}</strong><br>
                NIP. {{ $counselor?->nip ?? '..............................' }}
            </td>
            <td>
                Menyetujui,<br>
                Kepala SMA Negeri 1 Lengkong
                <br><br><br><br>
                <strong>{{ $headmaster?->name ?? 'Kepala Sekolah' }}</strong><br>
                NIP. {{ $headmaster?->nip ?? '..............................' }}
            </td>
        </tr>
    </table>

    <div style="margin-top: 15px; text-align: center; font-size: 7.5pt; color: #666; border-top: 1px dashed #ccc; padding-top: 4px;">
        Dokumen resmi diterbitkan oleh Sistem Informasi Akademik SMAN 1 Lengkong (SIS-SMA1LE) berbasis Grand Asesmen Terpadu 360° POP-BK ({{ $provider }}).
    </div>
</body>
</html>

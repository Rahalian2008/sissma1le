<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kisi-Kisi Penulisan Soal Asesmen - SMAN 1 Lengkong</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #111827;
            background-color: #f3f4f6;
            margin: 0;
            padding: 15px 0;
        }
        .page {
            background: #ffffff;
            width: 297mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 15mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-kop h4 {
            font-size: 10.5pt;
            margin: 0;
            font-weight: normal;
        }
        .header-kop h3 {
            font-size: 11.5pt;
            margin: 2px 0;
            font-weight: bold;
        }
        .header-kop h2 {
            font-size: 13.5pt;
            margin: 2px 0;
            font-weight: bold;
        }
        .header-kop p {
            font-size: 8.5pt;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        .title-doc {
            text-align: center;
            margin: 10px 0 14px 0;
        }
        .title-doc h1 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .title-doc span {
            font-size: 9.5pt;
            display: block;
            margin-top: 2px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            font-size: 9.5pt;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8.5pt;
        }
        .matrix-table th, .matrix-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .matrix-table th {
            background-color: #f3f4f6;
            text-align: center;
            font-weight: bold;
        }
        .signatures {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .sign-space {
            height: 55px;
        }
        .no-print-bar {
            position: fixed;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 8px;
            z-index: 9999;
        }
        .btn {
            background-color: #4f46e5;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-secondary {
            background-color: #4b5563;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button onclick="window.print()" class="btn">
            <span>🖨️</span>
            <span>Cetak Kisi-Kisi (Landscape)</span>
        </button>
        <a href="{{ route('lms.assessment.bank') }}" class="btn btn-secondary">
            <span>⬅️</span>
            <span>Kembali ke Bank Soal</span>
        </a>
    </div>

    <div class="page">
        <!-- Kop Surat Resmi -->
        <div class="header-kop">
            <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
            <h3>DINAS PENDIDIKAN</h3>
            <h3>CABANG DINAS PENDIDIKAN WILAYAH V</h3>
            <h2>SMA NEGERI 1 LENGKONG</h2>
            <p>Jl. Raya Lengkong No. 45, Lengkong, Kec. Lengkong, Kabupaten Sukabumi, Jawa Barat 43174</p>
        </div>

        <!-- Judul -->
        <div class="title-doc">
            <h1>KISI-KISI PENULISAN SOAL ASESMEN SUMATIF / FORMATIF</h1>
            <span>Tahun Ajaran 2026/2027 • Standar BSKAP Permendikdasmen No. 13/2025</span>
        </div>

        <!-- Identitas Dokumen -->
        <div class="info-grid">
            <div class="info-col">
                <div>Satuan Pendidikan: <strong>SMA Negeri 1 Lengkong</strong></div>
                <div>Mata Pelajaran: <strong>{{ $quiz->courseClass->subject->name ?? 'Semua Mata Pelajaran' }}</strong></div>
                <div>Kelas / Fase: <strong>{{ $quiz->courseClass->schoolClass->name ?? 'Kelas X / XI / XII' }} (Fase {{ ($quiz->courseClass->schoolClass->grade ?? 'X') === 'X' ? 'E' : 'F' }})</strong></div>
            </div>
            <div class="info-col">
                <div>Bentuk Soal: Pilihan Ganda &amp; Uraian HOTS</div>
                <div>Alokasi Waktu: {{ $quiz->duration_minutes ?? 90 }} Menit</div>
                <div>Penyusun / Guru: <strong>{{ $quiz->courseClass->teacher->user->name ?? $quiz->courseClass->teacher->name ?? 'Guru Pengampu' }}</strong></div>
            </div>
        </div>

        <!-- Tabel Matriks Kisi-Kisi -->
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No.</th>
                    <th style="width: 20%;">Capaian &amp; Tujuan Pembelajaran (TP)</th>
                    <th style="width: 20%;">Materi Pokok / Bahasan</th>
                    <th style="width: 26%;">Indikator Butir Soal</th>
                    <th style="width: 10%;">Level Kognitif</th>
                    <th style="width: 12%;">Bentuk Soal</th>
                    <th style="width: 8%;">No. Soal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $idx => $item)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>
                            @if(isset($item->learningObjective))
                                <strong>[TP {{ $item->learningObjective->tp_code }}]</strong><br>
                                {{ $item->learningObjective->title }}
                            @else
                                {{ $quiz->learningObjective->title ?? 'Kompetensi Sesuai Kurikulum Merdeka' }}
                            @endif
                        </td>
                        <td>
                            {{ $item->topic ?? $item->materi_pokok ?? $quiz->topic ?? 'Materi Inti' }}
                        </td>
                        <td>
                            {{ $item->indicator ?? $item->indikator_soal ?? 'Disajikan wacana masalah, peserta didik mampu menganalisis solusi yang tepat.' }}
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $item->cognitive_level ?? $item->level_kognitif ?? 'L2' }}
                        </td>
                        <td style="text-align: center;">
                            {{ ($item->question_type ?? '') === 'essay' || ($item->question_type ?? '') === 'uraian' ? 'Uraian' : 'Pilihan Ganda' }}
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $idx + 1 }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 12px; font-style: italic;">
                            Belum ada butir soal yang dipetakan pada kisi-kisi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Lembar Pengesahan -->
        <table class="signatures">
            <tr>
                <td>
                    Mengetahui,<br>
                    Wakil Kepala Sekolah Bidang Kurikulum
                    <div class="sign-space"></div>
                    <strong>{{ $settings['wakasek_kurikulum_name'] ?? 'Wakasek Kurikulum SMAN 1 Lengkong' }}</strong><br>
                    <span>NIP. {{ $settings['wakasek_kurikulum_nip'] ?? '-' }}</span>
                </td>
                <td>
                    Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Guru Penyusun Kisi-Kisi,
                    <div class="sign-space"></div>
                    <strong>{{ $quiz->courseClass->teacher->user->name ?? $quiz->courseClass->teacher->name ?? 'Guru Pengampu' }}</strong><br>
                    <span>NIP. {{ $quiz->courseClass->teacher->nip ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda & Jurnal Mengajar Guru - SMAN 1 Lengkong</title>
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
            margin-bottom: 14px;
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
            letter-spacing: 0.5px;
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
        .journal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8.5pt;
        }
        .journal-table th, .journal-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
        }
        .journal-table th {
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
            width: 33.3%;
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
            <span>Cetak Agenda Guru (Landscape)</span>
        </button>
        <a href="{{ route('lms.journals.index') }}" class="btn btn-secondary">
            <span>⬅️</span>
            <span>Kembali ke Jurnal</span>
        </a>
    </div>

    <div class="page">
        <!-- Kop Surat -->
        <div class="header-kop">
            <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
            <h3>DINAS PENDIDIKAN</h3>
            <h3>CABANG DINAS PENDIDIKAN WILAYAH V</h3>
            <h2>SMA NEGERI 1 LENGKONG</h2>
            <p>Jl. Raya Lengkong No. 45, Lengkong, Kec. Lengkong, Kabupaten Sukabumi, Jawa Barat 43174</p>
        </div>

        <!-- Judul -->
        <div class="title-doc">
            <h1>AGENDA & JURNAL PELAKSANAAN PEMBELAJARAN GURU</h1>
            <span>Tahun Ajaran 2026/2027 • Kurikulum Merdeka</span>
        </div>

        <!-- Identitas Guru & Kelas -->
        <div class="info-grid">
            <div class="info-col">
                <div>Nama Guru: <strong>{{ $targetClass->teacher->user->name ?? $targetClass->teacher->name ?? $teacher?->name ?? 'Guru Pengampu' }}</strong></div>
                <div>NIP: {{ $targetClass->teacher->nip ?? $teacher?->nip ?? '-' }}</div>
                <div>Mata Pelajaran: <strong>{{ $targetClass->subject->name ?? 'Semua Mapel Ampu' }}</strong></div>
            </div>
            <div class="info-col">
                <div>Satuan Pendidikan: SMA Negeri 1 Lengkong</div>
                <div>Kelas / Fase: {{ $targetClass->schoolClass->name ?? '-' }} (Fase {{ ($targetClass->schoolClass->grade ?? 'X') === 'X' ? 'E' : 'F' }})</div>
                <div>Semester: Ganjil / Genap</div>
            </div>
        </div>

        <!-- Tabel Jurnal KBM -->
        <table class="journal-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 10%;">Hari / Tgl</th>
                    <th style="width: 4%;">Pert.</th>
                    <th style="width: 8%;">Jam Ke-</th>
                    <th style="width: 18%;">Materi Pokok &amp; TP</th>
                    <th style="width: 25%;">Uraian Kegiatan Pembelajaran</th>
                    <th style="width: 10%;">Absensi Siswa</th>
                    <th style="width: 8%;">Ketercapaian</th>
                    <th style="width: 14%;">Catatan &amp; Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($journals as $index => $jrn)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ $jrn->date->translatedFormat('d/m/Y') }}</td>
                        <td style="text-align: center; font-weight: bold;">#{{ $jrn->meeting_number }}</td>
                        <td style="text-align: center;">{{ $jrn->time_slot }}</td>
                        <td>
                            <strong>{{ $jrn->topic }}</strong>
                            @if($jrn->learningObjective)
                                <div style="font-size: 8pt; color: #4338ca; margin-top: 2px;">
                                    [TP {{ $jrn->learningObjective->tp_code }}] {{ $jrn->learningObjective->title }}
                                </div>
                            @endif
                        </td>
                        <td style="white-space: pre-line;">{{ $jrn->teaching_activities }}</td>
                        <td style="text-align: center;">{{ $jrn->attendance_summary_text }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ str_replace('_', ' ', $jrn->learning_achievement_status) }}</td>
                        <td>
                            @if($jrn->classroom_dynamics_notes)
                                <div><strong>Kendala:</strong> {{ $jrn->classroom_dynamics_notes }}</div>
                            @endif
                            @if($jrn->follow_up_action)
                                <div style="margin-top: 2px;"><strong>Tindak Lanjut:</strong> {{ $jrn->follow_up_action }}</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 12px; font-style: italic;">
                            Belum ada catatan jurnal mengajar yang tersimpan pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Lembar Pengesahan (Kepala Sekolah Selalu di Kiri) -->
        <table class="signatures">
            <tr>
                <td></td>
                <td></td>
                <td>
                    Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Guru Mata Pelajaran,
                </td>
            </tr>
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala SMAN 1 Lengkong
                    <div class="sign-space"></div>
                    <strong>{{ $settings['school_principal'] ?? 'Drs. H. Kepala Sekolah, M.Pd.' }}</strong><br>
                    <span>NIP. {{ $settings['principal_nip'] ?? '-' }}</span>
                </td>
                <td>
                    Mengetahui,<br>
                    Wakil Kepala Sekolah Bidang Kurikulum
                    <div class="sign-space"></div>
                    <strong>{{ $settings['wakasek_kurikulum_name'] ?? 'Wakasek Kurikulum SMAN 1 Lengkong' }}</strong><br>
                    <span>NIP. {{ $settings['wakasek_kurikulum_nip'] ?? '-' }}</span>
                </td>
                <td>
                    Penyusun Jurnal KBM,
                    <div class="sign-space"></div>
                    <strong>{{ $targetClass->teacher->user->name ?? $targetClass->teacher->name ?? $teacher?->name ?? 'Guru Pengampu' }}</strong><br>
                    <span>NIP. {{ $targetClass->teacher->nip ?? $teacher?->nip ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>


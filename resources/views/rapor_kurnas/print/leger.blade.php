<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leger Nilai Kelas {{ $schoolClass->name }} - SMAN 1 Lengkong</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 8pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .page {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
        }
        .header h3 {
            margin: 2px 0;
            font-size: 11pt;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 9pt;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 1px 3px;
        }
        .leger-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }
        .leger-table th, .leger-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
        }
        .leger-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-left {
            text-align: left !important;
        }
        .sig-table {
            width: 100%;
            margin-top: 20px;
            font-size: 8.5pt;
        }
        .sig-table td {
            text-align: center;
            vertical-align: top;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 15px; right: 15px; z-index: 9999; display: flex; gap: 8px; background: rgba(255,255,255,0.9); padding: 8px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
        <button onclick="window.print()" style="background: #4f46e5; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Leger
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #1e293b; border: none; padding: 8px 12px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <div class="header">
            <h2>BUKU LEGER NILAI HASIL BELAJAR PESERTA DIDIK</h2>
            <h3>SMA NEGERI 1 LENGKONG</h3>
            <p>KURIKULUM NASIONAL / KURIKULUM MERDEKA - TAHUN PELAJARAN {{ $setting->academicYear->name ?? '2025/2026' }}</p>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 12%;"><strong>Kelas:</strong> {{ $schoolClass->name }}</td>
                <td style="width: 15%;"><strong>Tingkat:</strong> {{ $schoolClass->grade }}</td>
                <td style="width: 18%;"><strong>Semester:</strong> {{ $setting->semester == '1' ? '1 (Ganjil)' : '2 (Genap)' }}</td>
                <td style="width: 30%;"><strong>Wali Kelas:</strong> {{ $schoolClass->homeroomTeacher->name ?? '-' }}</td>
                <td style="text-align: right;"><strong>Jumlah Siswa:</strong> {{ count($students) }}</td>
            </tr>
        </table>

        @php
            // Hitung total nilai dan ranking
            $studentTotals = [];
            foreach ($students as $st) {
                $stScores = $scores->get($st->id) ?? collect();
                $tot = 0;
                $cnt = 0;
                foreach ($courseClasses as $cc) {
                    $sc = $stScores->firstWhere('course_class_id', $cc->id);
                    if ($sc && is_numeric($sc->score)) {
                        $tot += $sc->score;
                        $cnt++;
                    }
                }
                $avg = $cnt > 0 ? round($tot / $cnt, 2) : 0;
                $studentTotals[$st->id] = ['total' => $tot, 'avg' => $avg, 'count' => $cnt];
            }

            // Urutkan untuk rank
            $sortedAverages = collect($studentTotals)->sortByDesc('avg');
            $ranks = [];
            $curRank = 1;
            foreach ($sortedAverages as $sId => $stData) {
                $ranks[$sId] = $curRank++;
            }
        @endphp

        <table class="leger-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25px;">No</th>
                    <th rowspan="2" style="width: 70px;">NISN</th>
                    <th rowspan="2" class="text-left" style="width: 140px;">Nama Peserta Didik</th>
                    <th colspan="{{ count($courseClasses) }}">Mata Pelajaran</th>
                    <th rowspan="2" style="width: 45px;">Jumlah</th>
                    <th rowspan="2" style="width: 40px;">Rerata</th>
                    <th rowspan="2" style="width: 30px;">Rank</th>
                    <th colspan="3">Absensi</th>
                    <th rowspan="2" style="width: 50px;">Status</th>
                </tr>
                <tr>
                    @foreach($courseClasses as $cc)
                        <th style="font-size: 6.5pt; max-width: 50px; overflow: hidden;" title="{{ $cc->subject->name ?? '-' }}">
                            {{ Str::limit($cc->subject->name ?? '-', 8) }}
                        </th>
                    @endforeach
                    <th style="width: 20px;">S</th>
                    <th style="width: 20px;">I</th>
                    <th style="width: 20px;">A</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $idx => $st)
                    @php
                        $stScores = $scores->get($st->id) ?? collect();
                        $totData = $studentTotals[$st->id] ?? ['total' => 0, 'avg' => 0];
                        $hr = $homerooms->get($st->id);
                    @endphp
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td style="font-family: monospace;">{{ $st->nisn ?? '-' }}</td>
                        <td class="text-left font-bold" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">
                            {{ $st->name }}
                        </td>
                        @foreach($courseClasses as $cc)
                            @php
                                $sc = $stScores->firstWhere('course_class_id', $cc->id);
                            @endphp
                            <td>
                                {{ $sc && is_numeric($sc->score) ? number_format($sc->score, 0) : '-' }}
                            </td>
                        @endforeach
                        <td style="font-weight: bold;">{{ number_format($totData['total'], 0) }}</td>
                        <td style="font-weight: bold; background: #fafafa;">{{ number_format($totData['avg'], 1) }}</td>
                        <td style="font-weight: bold; color: #4338ca;">{{ $ranks[$st->id] ?? '-' }}</td>
                        <td>{{ $hr->sick ?? 0 }}</td>
                        <td>{{ $hr->permission ?? 0 }}</td>
                        <td>{{ $hr->absent ?? 0 }}</td>
                        <td style="font-size: 7pt; font-weight: bold;">
                            {{ $hr->status_promotion ?? (($schoolClass->grade ?? 'X') === 'XII' ? 'LULUS' : 'NAIK') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TANDA TANGAN (KEPALA SEKOLAH WAJIB DI KIRI!) -->
        <table class="sig-table">
            <tr>
                <td style="width: 50%;">
                    <p style="margin: 0;">Mengetahui,</p>
                    <p style="margin: 2px 0 50px 0;">Kepala Sekolah,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        {{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}
                    </p>
                    <p style="margin: 2px 0 0 0;">
                        NIP. {{ $setting->headmaster_nip ?? '196805141994121001' }}
                    </p>
                </td>
                <td style="width: 50%;">
                    <p style="margin: 0;">{{ $setting->report_place ?? 'Lengkong' }}, {{ Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</p>
                    <p style="margin: 2px 0 50px 0;">Wali Kelas,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        {{ $schoolClass->homeroomTeacher->name ?? '..........................................' }}
                    </p>
                    <p style="margin: 2px 0 0 0;">
                        NIP. {{ $schoolClass->homeroomTeacher->nip ?? '-' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

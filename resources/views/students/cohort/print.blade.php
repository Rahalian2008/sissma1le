<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Analisis Kohort Siswa Angkatan {{ $cohortLabel }} - {{ $school->name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.5pt;
            line-height: 1.25;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 12px;
            margin-top: 6px;
        }
        .title-doc h3 {
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .title-doc p {
            font-size: 8.5pt;
            margin: 3px 0 0 0;
        }
        table.cohort-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 10px;
        }
        table.cohort-table th, table.cohort-table td {
            border: 1px solid #000;
            padding: 3px 4px;
        }
        table.cohort-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .indicator-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border: 1px solid #94a3b8;
            padding: 6px 10px;
            background: #f8fafc;
            font-size: 8pt;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        .print-btn-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            font-size: 13px;
            margin-bottom: 12px;
        }
        .print-btn-bar button {
            background: #2563eb;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .signature-table {
            width: 100%;
            margin-top: 14px;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Analisis Kohort Siswa Angkatan ' . $cohortLabel" orientation="landscape" defaultPaper="f4" margin="8mm 10mm" />

    <div class="printable-sheet landscape-sheet" style="padding: 6px 12px;">
        <!-- KOP SURAT RESMI (UNIVERSAL) -->
        <x-print-kop :school="$school" />

        <div class="title-doc">
            <h3>TABEL ANALISIS KOHORT PESERTA DIDIK (STUDENT COHORT FLOW ANALYSIS)</h3>
            <p>Angkatan Masuk: <strong>{{ $cohortLabel }} (Tahun {{ $selectedYear }})</strong> &bull; Jenjang: <strong>Sekolah Menengah Atas (SMA - 3 Tahun)</strong> &bull; Satuan Pendidikan: <strong>{{ $school->name }}</strong></p>
        </div>

        <!-- Indikator Efisiensi Internal -->
        <div class="indicator-box">
            <div>Total Masuk Awal: <strong>{{ $initialTotal }} Siswa</strong> (L: {{ $initialMale }}, P: {{ $initialFemale }})</div>
            <div>Angka Bertahan (Survival Rate): <strong>{{ $survivalRate }}%</strong> ({{ $surviving }} Siswa)</div>
            <div>Tingkat Kelulusan (Graduation Rate): <strong>{{ $graduationRate }}%</strong> ({{ $graduated }} Siswa)</div>
            <div>Mutasi Keluar: <strong>{{ $mutationRate }}%</strong> ({{ $mutatedOut }} Siswa)</div>
            <div>Putus Sekolah (Dropout Rate): <strong>{{ $dropoutRate }}%</strong> ({{ $dropout }} Siswa)</div>
        </div>

        <!-- Tabel 1: Matriks Aliran Perkembangan Kohort -->
        <div style="font-weight: bold; margin-bottom: 4px; font-size: 8.5pt;">I. MATRIKS ALUR PERKEMBANGAN ANGKATAN {{ $cohortLabel }}</div>
        <table class="cohort-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 35px;">No</th>
                    <th rowspan="2" style="text-align: left; padding-left: 6px;">Fase Perkembangan Pendidikan (Alur Kohort)</th>
                    <th colspan="3">Jumlah Siswa</th>
                    <th rowspan="2" style="width: 80px;">Rasio (%)</th>
                    <th rowspan="2" style="text-align: left; padding-left: 6px;">Dasar Status / Keterangan Administrasi</th>
                </tr>
                <tr>
                    <th style="width: 50px;">L</th>
                    <th style="width: 50px;">P</th>
                    <th style="width: 60px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td style="text-align: center;">1</td>
                    <td style="padding-left: 6px;">Siswa Baru Diterima Awal (Tahun I - Tingkat X)</td>
                    <td style="text-align: center;">{{ $initialMale }}</td>
                    <td style="text-align: center;">{{ $initialFemale }}</td>
                    <td style="text-align: center;">{{ $initialTotal }}</td>
                    <td style="text-align: right; font-family: monospace; padding-right: 6px;">100.0%</td>
                    <td style="padding-left: 6px;">Diterima resmi pada PPDB Kelas X Tahun Ajaran {{ $cohortLabel }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td style="padding-left: 16px;">&bull; Masih aktif belajar di Tingkat X</td>
                    <td style="text-align: center;">{{ $students->where('current_class_level', 'X')->where('gender', 'L')->where('status', 'Aktif')->count() }}</td>
                    <td style="text-align: center;">{{ $students->where('current_class_level', 'X')->where('gender', 'P')->where('status', 'Aktif')->count() }}</td>
                    <td style="text-align: center;">{{ $inClassX }}</td>
                    <td style="text-align: right; font-family: monospace; padding-right: 6px;">{{ round(($inClassX / max($initialTotal, 1)) * 100, 1) }}%</td>
                    <td style="padding-left: 6px;">Siswa tahun pertama berjalan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td style="padding-left: 16px;">&bull; Berhasil naik ke Tingkat XI (Tahun II)</td>
                    <td style="text-align: center;">{{ $students->whereIn('current_class_level', ['XI', 'XII'])->where('gender', 'L')->count() + $graduatedMale }}</td>
                    <td style="text-align: center;">{{ $students->whereIn('current_class_level', ['XI', 'XII'])->where('gender', 'P')->count() + $graduatedFemale }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $inClassXI + $inClassXII + $graduated }}</td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold; padding-right: 6px;">{{ round((($inClassXI + $inClassXII + $graduated) / max($initialTotal, 1)) * 100, 1) }}%</td>
                    <td style="padding-left: 6px;">Naik tingkat ke kelas XI secara reguler</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td style="padding-left: 16px;">&bull; Berhasil naik ke Tingkat XII (Tahun III)</td>
                    <td style="text-align: center;">{{ $students->where('current_class_level', 'XII')->where('gender', 'L')->count() + $graduatedMale }}</td>
                    <td style="text-align: center;">{{ $students->where('current_class_level', 'XII')->where('gender', 'P')->count() + $graduatedFemale }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $inClassXII + $graduated }}</td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold; padding-right: 6px;">{{ round((($inClassXII + $graduated) / max($initialTotal, 1)) * 100, 1) }}%</td>
                    <td style="padding-left: 6px;">Mencapai tingkat akhir pendidikan menengah</td>
                </tr>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td style="text-align: center;">5</td>
                    <td style="padding-left: 6px;">Lulus Tepat Waktu (Menyelesaikan 3 Tahun SMA)</td>
                    <td style="text-align: center;">{{ $graduatedMale }}</td>
                    <td style="text-align: center;">{{ $graduatedFemale }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $graduated }}</td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold; padding-right: 6px;">{{ $graduationRate }}%</td>
                    <td style="padding-left: 6px;">Menyelesaikan seluruh kurikulum pendidikan dan memiliki Ijazah</td>
                </tr>
                <tr>
                    <td style="text-align: center;">6</td>
                    <td style="padding-left: 6px;">Mutasi Keluar Sekolah (Pindah Sekolah)</td>
                    <td style="text-align: center;">{{ $mutatedOutMale }}</td>
                    <td style="text-align: center;">{{ $mutatedOutFemale }}</td>
                    <td style="text-align: center;">{{ $mutatedOut }}</td>
                    <td style="text-align: right; font-family: monospace; padding-right: 6px;">{{ $mutationRate }}%</td>
                    <td style="padding-left: 6px;">Mutasi keluar resmi dengan surat keterangan pindah sekolah</td>
                </tr>
                <tr>
                    <td style="text-align: center;">7</td>
                    <td style="padding-left: 6px;">Putus Sekolah / Mengundurkan Diri (Drop Out)</td>
                    <td style="text-align: center;">{{ $dropoutMale }}</td>
                    <td style="text-align: center;">{{ $dropoutFemale }}</td>
                    <td style="text-align: center;">{{ $dropout }}</td>
                    <td style="text-align: right; font-family: monospace; padding-right: 6px;">{{ $dropoutRate }}%</td>
                    <td style="padding-left: 6px;">Keluar permanen sebelum menuntaskan jenjang SMA</td>
                </tr>
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="2" style="text-align: right; padding-right: 8px;">ANGKA BERTAHAN (SURVIVAL RATE):</td>
                    <td style="text-align: center;">{{ $students->whereIn('status', ['Aktif', 'Lulus'])->where('gender', 'L')->count() }}</td>
                    <td style="text-align: center;">{{ $students->whereIn('status', ['Aktif', 'Lulus'])->where('gender', 'P')->count() }}</td>
                    <td style="text-align: center;">{{ $surviving }}</td>
                    <td style="text-align: right; font-family: monospace; padding-right: 6px;">{{ $survivalRate }}%</td>
                    <td style="padding-left: 6px;">Siswa aktif belajar dan lulus dari angkatan masuk {{ $cohortLabel }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Tabel 2: Rekapitulasi Roster Siswa Angkatan -->
        <div style="font-weight: bold; margin-bottom: 4px; margin-top: 10px; font-size: 8.5pt;">II. DAFTAR PESERTA DIDIK DALAM ANGKATAN {{ $cohortLabel }}</div>
        <table class="cohort-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 70px;">NIS</th>
                    <th style="width: 80px;">NISN</th>
                    <th style="text-align: left; padding-left: 6px;">Nama Lengkap Siswa</th>
                    <th style="width: 30px;">L/P</th>
                    <th style="width: 90px;">Rombel Awal</th>
                    <th style="width: 90px;">Tingkat/Rombel Akhir</th>
                    <th style="width: 85px;">Tanggal Masuk</th>
                    <th style="width: 85px;">Status Terkini</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $idx => $st)
                    <tr>
                        <td style="text-align: center; font-family: monospace;">{{ $idx + 1 }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $st->nis }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $st->nisn ?? '-' }}</td>
                        <td style="padding-left: 6px; font-weight: 600;">{{ $st->name }}</td>
                        <td style="text-align: center;">{{ $st->gender }}</td>
                        <td style="text-align: center;">{{ $st->admission_class ?? 'Kelas X' }}</td>
                        <td style="text-align: center;">{{ $st->current_rombel ?? ('Tingkat '.$st->current_class_level) }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $st->admission_date ? \Carbon\Carbon::parse($st->admission_date)->format('d/m/Y') : '-' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $st->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: #64748b; padding: 10px;">Tidak ada data siswa untuk angkatan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Blok Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    Mengetahui,<br>
                    Kepala {{ $school->name }}<br><br><br><br>
                    <strong><u>{{ $school->headmaster_name }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip }}
                </td>
                <td style="width: 50%;">
                    {{ $school->city ?? 'Kota Mandiri' }}, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Wakil Kepala Sekolah Bidang Kesiswaan,<br><br><br><br>
                    <strong><u>Dewi Anggraeni, S.Pd.</u></strong><br>
                    NIP. 19850315 201001 2 018
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

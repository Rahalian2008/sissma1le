<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Rekap Nilai Siswa - {{ $courseClass->subject->name }} - {{ $courseClass->schoolClass->name }} [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;

        $wm = $courseClass->getWeightMaterial();
        $wa = $courseClass->getWeightAssignment();
        $wq = $courseClass->getWeightQuiz();
        $wt = $courseClass->getWeightAttendance();
        $kkm = $courseClass->subject->kkm ?? 75;
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '330mm 215mm' : 'A4 landscape' }};
            margin: {{ $isF4 ? '1.0cm 1.4cm' : '1.2cm 1.5cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '10pt' : '9.5pt' }};
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 10px 18px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .paper-toggle-group {
            display: inline-flex;
            align-items: center;
            background: #0f172a;
            border-radius: 6px;
            padding: 2px;
            border: 1px solid #334155;
        }
        .paper-toggle-btn {
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            text-decoration: none;
            color: #94a3b8;
            transition: 0.2s;
        }
        .paper-toggle-btn.active {
            background: #3b82f6;
            color: #fff;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            border: none;
            transition: 0.2s;
        }
        .btn-print:hover { background: #059669; }
        .btn-back {
            background: #475569;
            color: #fff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }
        .btn-back:hover { background: #334155; }
        .btn-excel {
            background: #16a34a;
            color: #fff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            border: none;
        }
        .btn-excel:hover { background: #15803d; }

        .doc-container {
            max-width: {{ $isF4 ? '330mm' : '297mm' }};
            margin: 0 auto;
            padding: 10px 20px;
        }
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 125px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-header h4 { margin: 0; font-size: 11pt; font-weight: normal; }
        .kop-header h3 { margin: 2px 0; font-size: 13pt; font-weight: bold; }
        .kop-header h2 { margin: 2px 0; font-size: 15pt; font-weight: 900; }
        .kop-header p { margin: 2px 0 0 0; font-size: 8.5pt; color: #222; }

        .doc-title {
            text-align: center;
            margin: 12px 0 10px 0;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            color: #333;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }

        .summary-box {
            display: flex;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 12px;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 4.5px 5px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8pt;
        }
        table.data-table td.center { text-align: center; }
        table.data-table td.right { text-align: right; }

        .signatures {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
            font-size: 9.5pt;
        }
        .signatures td {
            vertical-align: top;
            padding: 0 25px;
        }

        @media print {
            .no-print-bar { display: none !important; }
            body { background: none; }
            .doc-container { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Controls Bar (No Print) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="javascript:history.back()" class="btn-back">⬅ Kembali</a>
            <span style="font-weight: bold; font-size: 14px;">BUKU NILAI: {{ $courseClass->subject->name }} - {{ $courseClass->schoolClass->name }}</span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4 Landscape
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4 / Folio Landscape
                </a>
            </div>

            <a href="{{ route('lms.grades.export_excel', $courseClass->id) }}" class="btn-excel">
                📊 Unduh Excel
            </a>

            <button onclick="window.print()" class="btn-print">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Printable Content -->
    <div class="doc-container">

        <!-- KOP Surat Resmi -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
            </div>
        @else
            <div class="kop-header">
                <h4>PEMERINTAH PROVINSI JAWA BARAT</h4>
                <h3>DINAS PENDIDIKAN</h3>
                <h4>CABANG DINAS PENDIDIKAN WILAYAH V</h4>
                <h2>{{ strtoupper($schoolName) }}</h2>
                <p>NPSN: {{ $schoolNpsn }} • NSS: {{ $schoolNss }} • Akreditasi: {{ $schoolAccreditation }}</p>
                <p>{{ $schoolAddress }} • Email: {{ $schoolEmail }}</p>
            </div>
        @endif

        <!-- Document Title -->
        <div class="doc-title">
            <h3>BUKU REKAPITULASI NILAI AKHIR & PEMANTAUAN KETUNTASAN SISWA (GRADEBOOK)</h3>
            <p>Sistem Informasi & Pembelajaran Digital (LMS) {{ $schoolName }}</p>
        </div>

        <!-- Meta Table -->
        <table class="meta-table">
            <tr>
                <td style="width: 16%; font-weight: bold;">Mata Pelajaran</td>
                <td style="width: 2%;">:</td>
                <td style="width: 34%;">{{ $courseClass->subject->name }}</td>
                <td style="width: 18%; font-weight: bold;">Standar KKTP Mapel</td>
                <td style="width: 2%;">:</td>
                <td style="width: 28%;"><strong>{{ $kkm }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Kelas / Rombel</td>
                <td>:</td>
                <td>{{ $courseClass->schoolClass->name }}</td>
                <td style="font-weight: bold;">Tahun Ajaran</td>
                <td>:</td>
                <td>{{ $courseClass->academicYear->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Guru Pengampu</td>
                <td>:</td>
                <td>{{ $courseClass->teacher->name }}</td>
                <td style="font-weight: bold;">Bobot Penilaian</td>
                <td>:</td>
                <td>
                    Materi ({{ $wm }}%), Tugas ({{ $wa }}%), CBT ({{ $wq }}%), Presensi ({{ $wt }}%)
                </td>
            </tr>
        </table>

        <!-- Summary Statistics -->
        <div class="summary-box">
            <span><strong>Total Siswa:</strong> {{ count($students) }} Siswa</span>
            <span><strong>Rata-rata Nilai Akhir Kelas:</strong> {{ $avgFinalScore }}</span>
            <span><strong>Tuntas (≥ {{ $kkm }}):</strong> {{ $masteredCount }} Siswa</span>
            <span><strong>Belum Tuntas (Remedial):</strong> {{ $unmasteredCount }} Siswa</span>
        </div>

        <!-- Grades Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th style="width: 85px;">NISN</th>
                    <th>Nama Lengkap Siswa</th>
                    <th style="width: 80px;">Rerata Materi ({{ $wm }}%)</th>
                    <th style="width: 80px;">Rerata Tugas ({{ $wa }}%)</th>
                    <th style="width: 80px;">Rerata CBT ({{ $wq }}%)</th>
                    <th style="width: 80px;">Rerata Presensi ({{ $wt }}%)</th>
                    <th style="width: 65px;">Nilai Akhir</th>
                    <th style="width: 55px;">Predikat</th>
                    <th style="width: 90px;">Status KKTP</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($students as $st)
                    @php
                        $grade = $gradebook[$st->id] ?? null;
                    @endphp
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td class="center">{{ $st->nisn }}</td>
                        <td><strong>{{ $st->name }}</strong></td>
                        <td class="center">{{ $grade['material_avg'] ?? '-' }}</td>
                        <td class="center">{{ $grade['assignment_avg'] ?? '-' }}</td>
                        <td class="center">{{ $grade['quiz_avg'] ?? '-' }}</td>
                        <td class="center">{{ $grade['attendance_avg'] ?? '-' }}</td>
                        <td class="center" style="font-weight: bold; font-size: 9.5pt; color: #4338ca;">
                            {{ $grade['final_score'] ?? '-' }}
                        </td>
                        <td class="center" style="font-weight: bold;">
                            {{ $grade['predicate'] ?? '-' }}
                        </td>
                        <td class="center">
                            @if(isset($grade['is_mastered']) && $grade['is_mastered'] !== null)
                                @if($grade['is_mastered'])
                                    <span style="color: #16a34a; font-weight: bold;">TUNTAS</span>
                                @else
                                    <span style="color: #dc2626; font-weight: bold;">REMEDIAL</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signatures -->
        <table class="signatures">
            <tr>
                <td style="width: 50%; text-align: left; padding-left: 20px;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $headmaster['name'] }}</span><br>
                    NIP. {{ $headmaster['nip'] }}
                </td>
                <td style="width: 50%; text-align: right; padding-right: 20px;">
                    {{ $schoolCity }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Guru Mata Pelajaran</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $courseClass->teacher->name }}</span><br>
                    NIP. {{ $courseClass->teacher->nip ?: '-' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($mode === 'all_rombels')
            Rekap Kehadiran Semua Rombel ({{ $startDate->format('d-m-Y') }} sd {{ $endDate->format('d-m-Y') }})
        @else
            Rekap Kehadiran {{ $selectedClass?->name ?? 'Kelas' }} ({{ $startDate->format('d-m-Y') }} sd {{ $endDate->format('d-m-Y') }})
        @endif
    </title>
    @php
        $isF4 = strtolower(request('paper', $paper ?? 'a4')) === 'f4';
        $isLandscape = strtolower(request('orientation', $orientation ?? 'landscape')) === 'landscape';
        $actualKopPath = !empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop')));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '330mm 215mm' : 'A4' }} {{ $isLandscape ? 'landscape' : 'portrait' }};
            margin: 10mm 12mm 10mm 12mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #0f172a;
            font-size: 9pt;
            line-height: 1.25;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* Controls Bar (Layar Saja, Tersembunyi saat Cetak) */
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25);
        }
        .no-print-bar .controls-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .no-print-bar .controls-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .paper-toggle {
            display: inline-flex;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 2px;
        }
        .paper-toggle a {
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 6px;
        }
        .paper-toggle a.active {
            background: #4f46e5;
            color: #ffffff;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-print {
            background: #4f46e5;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #4338ca;
        }
        .btn-back {
            background: #334155;
            color: #cbd5e1;
        }
        .btn-back:hover {
            background: #475569;
            color: #ffffff;
        }

        /* Container Dokumen */
        .page-sheet {
            width: 100%;
            padding: 10px 14px;
        }

        /* KOP Resmi */
        .kop-wrapper {
            margin-bottom: 8px;
        }
        .kop-img {
            width: 100%;
            max-height: 85px;
            object-fit: contain;
            display: block;
        }
        .kop-text-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            text-align: center;
            border-bottom: 2.5px double #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .kop-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            margin: 1px 0;
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-text p {
            margin: 0;
            font-size: 8pt;
            font-family: Arial, sans-serif;
            color: #334155;
        }

        /* Judul Dokumen */
        .doc-title-wrapper {
            text-align: center;
            margin: 8px 0 12px 0;
        }
        .doc-title {
            font-size: 11.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            text-decoration: underline;
        }
        .doc-subtitle {
            font-size: 9pt;
            font-weight: 600;
            margin: 3px 0 0 0;
            color: #1e293b;
        }

        /* Metadata */
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8.5pt;
            font-family: Arial, sans-serif;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .meta-label {
            width: 120px;
            font-weight: bold;
            color: #334155;
        }

        /* Tabel Presensi Grid */
        .attendance-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            font-family: Arial, sans-serif;
            margin-bottom: 12px;
        }
        .attendance-grid th,
        .attendance-grid td {
            border: 1px solid #1e293b;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .attendance-grid th {
            background-color: #f1f5f9;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .attendance-grid td.center {
            text-align: center;
        }
        .attendance-grid td.bold {
            font-weight: bold;
        }
        .attendance-grid tr.footer-row td {
            background-color: #e2e8f0;
            font-weight: 900;
            border-top: 2px solid #000;
        }

        /* Signature Section */
        .signature-wrapper {
            margin-top: 18px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 38%;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }
        .sig-role {
            font-size: 9pt;
            margin-bottom: 48px;
            line-height: 1.25;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9.5pt;
        }
        .sig-nip {
            font-size: 8.5pt;
            margin-top: 1px;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background: #fff;
            }
            .page-sheet {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Kontrol Layar (Tidak Tercetak) -->
    <div class="no-print-bar">
        <div class="controls-left">
            <span style="font-weight: 800; font-size: 13px;">🖨️ Cetak Rekapitulasi Presensi Siswa</span>
            <span style="color: #64748b;">|</span>
            <span style="font-size: 12px; color: #94a3b8;">
                {{ $mode === 'all_rombels' ? 'Semua Rombel (Tanpa Nama)' : 'Per Rombel (' . ($selectedClass?->name ?? 'Kelas') . ')' }}
            </span>
        </div>
        <div class="controls-right">
            <!-- Pilihan Kertas -->
            <div class="paper-toggle">
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', array_merge(request()->query(), ['paper' => 'a4'])) }}" class="{{ !$isF4 ? 'active' : '' }}">A4</a>
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', array_merge(request()->query(), ['paper' => 'f4'])) }}" class="{{ $isF4 ? 'active' : '' }}">F4</a>
            </div>
            <!-- Pilihan Orientasi -->
            <div class="paper-toggle">
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', array_merge(request()->query(), ['orientation' => 'landscape'])) }}" class="{{ $isLandscape ? 'active' : '' }}">Landscape</a>
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', array_merge(request()->query(), ['orientation' => 'portrait'])) }}" class="{{ !$isLandscape ? 'active' : '' }}">Portrait</a>
            </div>
            <!-- Toggle KOP -->
            <div class="paper-toggle">
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', array_merge(request()->query(), ['kop' => '1'])) }}" class="{{ $showKop ? 'active' : '' }}">Pakai Kop</a>
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', array_merge(request()->query(), ['kop' => '0'])) }}" class="{{ !$showKop ? 'active' : '' }}">Tanpa Kop</a>
            </div>
            <button onclick="window.print()" class="btn-action btn-print">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / PDF</span>
            </button>
            <a href="{{ route('tu.kesiswaan.rekap_presensi.index', request()->query()) }}" class="btn-action btn-back">
                <span>✕ Kembali</span>
            </a>
        </div>
    </div>

    <!-- Lembar Dokumen Siap Cetak -->
    <div class="page-sheet">
        @if($showKop)
            <div class="kop-wrapper">
                @if($actualKopUrl)
                    <img src="{{ $actualKopUrl }}" alt="KOP Surat" class="kop-img">
                @else
                    <div class="kop-text-container">
                        @if(!empty($school?->logo_url))
                            <img src="{{ $school->logo_url }}" alt="Logo" class="kop-logo">
                        @endif
                        <div class="kop-text">
                            <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
                            <h4>DINAS PENDIDIKAN</h4>
                            <h2>{{ $school?->name ?? 'SMA NEGERI 1 LENGKONG' }}</h2>
                            <p>{{ $school?->address ?? 'Jl. Raya Lengkong No. 24, Kab. Sukabumi, Jawa Barat' }} • Telp: {{ $school?->phone ?? '(0266) -' }} • Email: {{ $school?->email ?? 'info@sman1lengkong.sch.id' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="doc-title-wrapper">
            <h1 class="doc-title">
                @if($mode === 'all_rombels')
                    LAPORAN REKAPITULASI KEHADIRAN SISWA PER ROMBONGAN BELAJAR
                @else
                    LAPORAN REKAPITULASI KEHADIRAN SISWA KELAS {{ strtoupper($selectedClass?->name ?? 'ROMBEL') }}
                @endif
            </h1>
            <p class="doc-subtitle">
                Periode: {{ $startDate->translatedFormat('d F Y') }} s.d. {{ $endDate->translatedFormat('d F Y') }}
                • Tahun Ajaran: {{ $academicYear }} (Semester {{ $semester }})
            </p>
        </div>

        @if($mode === 'per_rombel' && $selectedClass)
            <table class="meta-table">
                <tr>
                    <td class="meta-label">Rombongan Belajar</td>
                    <td>: <strong>{{ $selectedClass->name }}</strong> (Tingkat {{ $selectedClass->grade }})</td>
                    <td class="meta-label">Total Peserta Didik</td>
                    <td>: <strong>{{ count($studentsRecap) }} Siswa</strong></td>
                </tr>
                <tr>
                    <td class="meta-label">Wali Kelas</td>
                    <td>: {{ $selectedClass->homeroomTeacher?->name ?? 'Belum Ditentukan' }}</td>
                    <td class="meta-label">NIP Wali Kelas</td>
                    <td>: {{ $selectedClass->homeroomTeacher?->nip ?: '-' }}</td>
                </tr>
            </table>
        @endif

        @if($mode === 'all_rombels')
            <!-- TABEL SEMUA ROMBEL TANPA NAMA -->
            <table class="attendance-grid">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kelas / Rombel</th>
                        <th style="width: 60px;">Tingkat</th>
                        <th>Wali Kelas</th>
                        <th style="width: 65px;">Jml Siswa</th>
                        <th style="width: 65px;">Hadir (H)</th>
                        <th style="width: 60px;">Sakit (S)</th>
                        <th style="width: 60px;">Izin (I)</th>
                        <th style="width: 60px;">Alpa (A)</th>
                        <th style="width: 70px;">Terlambat</th>
                        <th style="width: 80px;">Total Catatan</th>
                        <th style="width: 75px;">% Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rombelsRecap as $idx => $r)
                        <tr>
                            <td class="center">{{ $idx + 1 }}</td>
                            <td class="bold">{{ $r['class']->name }}</td>
                            <td class="center">{{ $r['class']->grade }}</td>
                            <td>{{ $r['class']->homeroomTeacher?->name ?? '-' }}</td>
                            <td class="center">{{ $r['student_count'] }}</td>
                            <td class="center bold">{{ $r['hadir'] }}</td>
                            <td class="center">{{ $r['sakit'] }}</td>
                            <td class="center">{{ $r['izin'] }}</td>
                            <td class="center">{{ $r['alpa'] }}</td>
                            <td class="center">{{ $r['terlambat'] }}</td>
                            <td class="center">{{ $r['total_records'] }}</td>
                            <td class="center bold">{{ $r['percentage'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="center">Tidak ada data kehadiran untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="footer-row">
                        <td colspan="4" style="text-align: right; padding-right: 10px;">TOTAL KESELURUHAN:</td>
                        <td class="center">{{ number_format($schoolTotal['total_students']) }}</td>
                        <td class="center">{{ number_format($schoolTotal['hadir']) }}</td>
                        <td class="center">{{ number_format($schoolTotal['sakit']) }}</td>
                        <td class="center">{{ number_format($schoolTotal['izin']) }}</td>
                        <td class="center">{{ number_format($schoolTotal['alpa']) }}</td>
                        <td class="center">{{ number_format($schoolTotal['terlambat']) }}</td>
                        <td class="center">{{ number_format($schoolTotal['total_records']) }}</td>
                        <td class="center">{{ $schoolTotal['percentage'] }}%</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <!-- TABEL PER ROMBEL DETAIL SISWA -->
            <table class="attendance-grid">
                <thead>
                    <tr>
                        <th style="width: 28px;">No</th>
                        <th style="width: 75px;">NIS</th>
                        <th style="width: 85px;">NISN</th>
                        <th>Nama Lengkap Siswa</th>
                        <th style="width: 35px;">L/P</th>
                        <th style="width: 60px;">Hadir (H)</th>
                        <th style="width: 55px;">Sakit (S)</th>
                        <th style="width: 55px;">Izin (I)</th>
                        <th style="width: 55px;">Alpa (A)</th>
                        <th style="width: 65px;">Terlambat</th>
                        <th style="width: 70px;">Total Hari</th>
                        <th style="width: 65px;">% Hadir</th>
                        <th style="width: 100px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentsRecap as $idx => $st)
                        <tr>
                            <td class="center">{{ $idx + 1 }}</td>
                            <td class="center" style="font-family: monospace;">{{ $st['student']->nis ?? '-' }}</td>
                            <td class="center" style="font-family: monospace;">{{ $st['student']->nisn ?? '-' }}</td>
                            <td class="bold">{{ $st['student']->name }}</td>
                            <td class="center">{{ $st['student']->gender ?? '-' }}</td>
                            <td class="center bold">{{ $st['hadir'] }}</td>
                            <td class="center">{{ $st['sakit'] }}</td>
                            <td class="center">{{ $st['izin'] }}</td>
                            <td class="center">{{ $st['alpa'] }}</td>
                            <td class="center">{{ $st['terlambat'] }}</td>
                            <td class="center">{{ $st['total_records'] }}</td>
                            <td class="center bold">{{ $st['percentage'] }}%</td>
                            <td class="center" style="font-size: 8pt;">
                                @if($st['total_records'] === 0)
                                    Belum Ada Data
                                @elseif($st['percentage'] >= 90)
                                    Sangat Baik (A)
                                @elseif($st['percentage'] >= 80)
                                    Baik (B)
                                @elseif($st['percentage'] >= 70)
                                    Cukup (C)
                                @else
                                    Perlu Pembinaan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="center">Tidak ada data siswa untuk kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="footer-row">
                        <td colspan="5" style="text-align: right; padding-right: 10px;">TOTAL KELAS:</td>
                        <td class="center">{{ number_format($rombelSummary['hadir']) }}</td>
                        <td class="center">{{ number_format($rombelSummary['sakit']) }}</td>
                        <td class="center">{{ number_format($rombelSummary['izin']) }}</td>
                        <td class="center">{{ number_format($rombelSummary['alpa']) }}</td>
                        <td class="center">{{ number_format($rombelSummary['terlambat']) }}</td>
                        <td class="center">{{ number_format($rombelSummary['total_records']) }}</td>
                        <td class="center">{{ $rombelSummary['percentage'] }}%</td>
                        <td class="center">-</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Area Tanda Tangan Resmi -->
        <div class="signature-wrapper">
            <div class="sig-box">
                <div class="sig-role">
                    Mengetahui,<br>
                    Kepala Sekolah
                </div>
                <div class="sig-name">{{ $headmasterName }}</div>
                <div class="sig-nip">NIP. {{ $headmasterNip }}</div>
            </div>

            <div class="sig-box">
                <div class="sig-role">
                    Lengkong, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    @if($mode === 'per_rombel' && $selectedClass?->homeroomTeacher)
                        Wali Kelas {{ $selectedClass->name }}
                    @else
                        Wakil Kepala Sekolah Bidang Kesiswaan
                    @endif
                </div>
                @if($mode === 'per_rombel' && $selectedClass?->homeroomTeacher)
                    <div class="sig-name">{{ $selectedClass->homeroomTeacher->name }}</div>
                    <div class="sig-nip">NIP. {{ $selectedClass->homeroomTeacher->nip ?: '-' }}</div>
                @else
                    <div class="sig-name">{{ $wakasekName }}</div>
                    <div class="sig-nip">NIP. {{ $wakasekNip }}</div>
                @endif
            </div>
        </div>
    </div>

</body>
</html>

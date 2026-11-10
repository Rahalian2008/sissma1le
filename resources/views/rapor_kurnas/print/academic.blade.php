<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Akademik - {{ $student->name }} - SMAN 1 Lengkong</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .page {
            width: 100%;
            max-width: 185mm;
            margin: 0 auto;
        }
        /* Kop Dinas */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-logo {
            width: 75px;
            text-align: center;
        }
        .kop-logo img {
            width: 65px;
            height: auto;
        }
        .kop-text {
            text-align: center;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .kop-text h3 {
            margin: 1px 0;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            margin: 2px 0;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .kop-text p {
            margin: 0;
            font-size: 8.5pt;
        }

        /* Identity Bar */
        .id-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        .id-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        /* Score Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        .report-table th, .report-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .report-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .group-header {
            background-color: #e6e6e6;
            font-weight: bold;
            padding: 4px 6px;
        }
        .text-center {
            text-align: center;
        }
        .desc-cell {
            text-align: justify;
            line-height: 1.25;
            font-size: 8.5pt;
        }

        /* Sub-tables for Extras, Absence, Note */
        .sub-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        .sub-table th, .sub-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .sub-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .box-note {
            border: 1px solid #000;
            padding: 8px 10px;
            margin-bottom: 10px;
            font-size: 9pt;
            min-height: 25px;
        }

        .box-promotion {
            border: 2px solid #000;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 9.5pt;
            background-color: #fbfbfb;
        }

        /* Signatures Layout */
        .sig-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .sig-table td {
            vertical-align: top;
            text-align: center;
            padding: 0 5px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Toolbar -->
    <div class="no-print" style="position: fixed; top: 15px; right: 15px; z-index: 9999; display: flex; gap: 8px; background: rgba(255,255,255,0.9); padding: 8px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
        <button onclick="window.print()" style="background: #4f46e5; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #1e293b; border: none; padding: 8px 12px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <!-- KOP SEKOLAH DINAS RESMI -->
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <img src="{{ asset('images/logo-jabar.png') }}" onerror="this.src='{{ asset('images/logo-sma1.png') }}'" alt="Logo Pemda">
                </td>
                <td class="kop-text">
                    <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
                    <h3>DINAS PENDIDIKAN</h3>
                    <h4>CABANG DINAS PENDIDIKAN WILAYAH V</h4>
                    <h2>SMA NEGERI 1 LENGKONG</h2>
                    <p>Jl. Raya Tegallega Kec. Lengkong Kabupaten Sukabumi</p>
                    <p>Email: sman1lengkong@gmail.com | Website: https://sman1lengkong.school</p>
                </td>
                <td class="kop-logo">
                    <img src="{{ asset('images/logo-sma1.png') }}" onerror="this.src='{{ asset('images/logo-jabar.png') }}'" alt="Logo Sekolah">
                </td>
            </tr>
        </table>

        <!-- IDENTITAS LAPORAN -->
        <div style="text-align: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 12pt; font-weight: bold; text-decoration: underline;">
                LAPORAN HASIL BELAJAR (RAPOR)
            </h3>
            <p style="margin: 2px 0 0 0; font-size: 9.5pt;">KURIKULUM NASIONAL / MERDEKA</p>
        </div>

        <table class="id-table">
            <tr>
                <td style="width: 18%;">Nama Peserta Didik</td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;"><strong>{{ strtoupper($student->name) }}</strong></td>
                <td style="width: 18%;">Kelas</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">{{ $schoolClass->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>NIS / NISN</td>
                <td>:</td>
                <td>{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
                <td>Fase / Semester</td>
                <td>:</td>
                <td>{{ ($schoolClass->grade ?? 'X') === 'X' ? 'E' : 'F' }} / {{ $setting->semester == '1' ? '1 (Ganjil)' : '2 (Genap)' }}</td>
            </tr>
            <tr>
                <td>Nama Sekolah</td>
                <td>:</td>
                <td>SMA Negeri 1 Lengkong</td>
                <td>Tahun Pelajaran</td>
                <td>:</td>
                <td>{{ $setting->academicYear->name ?? '2025/2026' }}</td>
            </tr>
        </table>

        <!-- TABEL NILAI CAPAIAN HASIL BELAJAR -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 32%;">Mata Pelajaran</th>
                    <th style="width: 12%;">Nilai Akhir</th>
                    <th style="width: 50%;">Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. KELOMPOK MATA PELAJARAN UMUM -->
                @php
                    $groupA = $subjectGroups->where('group_type', 'A');
                    $groupB = $subjectGroups->where('group_type', 'B');
                    $groupC = $subjectGroups->where('group_type', 'C');
                    $scoresBySubject = $scores->keyBy(function($item) {
                        return $item->courseClass->subject_id ?? 0;
                    });
                    $no = 1;
                @endphp

                @if($groupA->isNotEmpty())
                    <tr>
                        <td colspan="4" class="group-header">A. Kelompok Mata Pelajaran Umum</td>
                    </tr>
                    @foreach($groupA as $item)
                        @php
                            $sc = $scoresBySubject->get($item->subject_id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                            <td class="text-center" style="font-weight: bold; font-size: 10pt;">
                                {{ $sc ? number_format($sc->score, 0) : '-' }}
                            </td>
                            <td class="desc-cell">
                                {{ $sc?->full_description ?? 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan baik.' }}
                            </td>
                        </tr>
                    @endforeach
                @endif

                <!-- 2. KELOMPOK MATA PELAJARAN PILIHAN -->
                @if($groupB->isNotEmpty())
                    <tr>
                        <td colspan="4" class="group-header">B. Kelompok Mata Pelajaran Pilihan</td>
                    </tr>
                    @foreach($groupB as $item)
                        @php
                            $sc = $scoresBySubject->get($item->subject_id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                            <td class="text-center" style="font-weight: bold; font-size: 10pt;">
                                {{ $sc ? number_format($sc->score, 0) : '-' }}
                            </td>
                            <td class="desc-cell">
                                {{ $sc?->full_description ?? 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan baik.' }}
                            </td>
                        </tr>
                    @endforeach
                @endif

                <!-- 3. KELOMPOK MUATAN LOKAL -->
                @if($groupC->isNotEmpty())
                    <tr>
                        <td colspan="4" class="group-header">C. Muatan Lokal</td>
                    </tr>
                    @foreach($groupC as $item)
                        @php
                            $sc = $scoresBySubject->get($item->subject_id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                            <td class="text-center" style="font-weight: bold; font-size: 10pt;">
                                {{ $sc ? number_format($sc->score, 0) : '-' }}
                            </td>
                            <td class="desc-cell">
                                {{ $sc?->full_description ?? 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan baik.' }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <!-- EKSTRAKURIKULER & KETIDAKHADIRAN -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <!-- Tabel Ekstrakurikuler -->
                <td style="width: 58%; vertical-align: top; padding-right: 5px;">
                    <table class="sub-table" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th style="width: 32%;">Kegiatan Ekstrakurikuler</th>
                                <th style="width: 20%;">Predikat</th>
                                <th style="width: 40%;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($extracurriculars as $eIdx => $extra)
                                <tr>
                                    <td class="text-center">{{ $eIdx + 1 }}</td>
                                    <td>{{ $extra->extracurricular->name ?? '-' }}</td>
                                    <td class="text-center font-bold">{{ $extra->predicate }}</td>
                                    <td style="font-size: 8pt;">{{ $extra->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Pramuka Wajib</td>
                                    <td class="text-center font-bold">Baik</td>
                                    <td style="font-size: 8pt;">Aktif mengikuti kegiatan kepramukaan dan kepemimpinan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>

                <!-- Tabel Ketidakhadiran -->
                <td style="width: 42%; vertical-align: top; padding-left: 5px;">
                    <table class="sub-table" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th colspan="2">Ketidakhadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 70%;">Sakit (S)</td>
                                <td class="text-center font-bold" style="width: 30%;">{{ $homeroom->sick ?? 0 }} hari</td>
                            </tr>
                            <tr>
                                <td>Izin (I)</td>
                                <td class="text-center font-bold">{{ $homeroom->permission ?? 0 }} hari</td>
                            </tr>
                            <tr>
                                <td>Tanpa Keterangan (A)</td>
                                <td class="text-center font-bold">{{ $homeroom->absent ?? 0 }} hari</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- CATATAN WALI KELAS -->
        <div style="font-size: 9pt; font-weight: bold; margin-bottom: 2px;">Catatan Wali Kelas:</div>
        <div class="box-note">
            <em>{{ $homeroom->note ?? 'Pertahankan prestasi belajar dan tingkatkan kedisiplinan serta partisipasi aktif dalam kegiatan sekolah.' }}</em>
        </div>

        <!-- KEPUTUSAN KENAIKAN / KELULUSAN -->
        @if($setting->semester == '2')
            <div class="box-promotion">
                <strong>Keputusan:</strong><br>
                Berdasarkan pencapaian seluruh kompetensi pada Tahun Pelajaran {{ $setting->academicYear->name ?? '2025/2026' }}, peserta didik dinyatakan:<br>
                <div style="margin-top: 4px; font-size: 11pt; font-weight: bold; text-align: center;">
                    @if(($schoolClass->grade ?? 'X') === 'XII')
                        {{ ($homeroom->status_promotion ?? 'LULUS') === 'LULUS' ? 'LULUS DARI SATUAN PENDIDIKAN' : 'TIDAK LULUS' }}
                    @else
                        {{ ($homeroom->status_promotion ?? 'NAIK') === 'NAIK' ? 'NAIK KE KELAS ' . ($homeroom->promotion_target_grade ?? (($schoolClass->grade ?? 'X') === 'X' ? 'XI' : 'XII')) : 'TINGGAL DI KELAS' }}
                    @endif
                </div>
            </div>
        @endif

        <!-- TANDA TANGAN RESMI DINAS -->
        <!-- ATURAN RESMI: KEPALA SEKOLAH WAJIB DI KIRI! -->
        <table class="sig-table">
            <tr>
                <!-- KIRI: KEPALA SEKOLAH (ATURAN KEMENDIKBUD / USER WAJIB DI KIRI) -->
                <td style="width: 38%;">
                    <p style="margin: 0;">Mengetahui,</p>
                    <p style="margin: 2px 0 55px 0;">Kepala Sekolah,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        {{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}
                    </p>
                    <p style="margin: 2px 0 0 0;">
                        NIP. {{ $setting->headmaster_nip ?? '196805141994121001' }}
                    </p>
                </td>

                <!-- TENGAH: ORANG TUA / WALI -->
                <td style="width: 28%;">
                    <p style="margin: 0;">Mengetahui,</p>
                    <p style="margin: 2px 0 55px 0;">Orang Tua / Wali,</p>
                    <p style="margin: 0; text-decoration: underline;">
                        ..........................................
                    </p>
                </td>

                <!-- KANAN: WALI KELAS -->
                <td style="width: 34%;">
                    <p style="margin: 0;">{{ $setting->report_place ?? 'Lengkong' }}, {{ Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</p>
                    <p style="margin: 2px 0 55px 0;">Wali Kelas,</p>
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


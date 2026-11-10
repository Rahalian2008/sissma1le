<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Kokurikuler P5 - {{ $student->name }} - SMAN 1 Lengkong</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
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
            width: 70px;
            text-align: center;
        }
        .kop-logo img {
            width: 60px;
            height: auto;
        }
        .kop-text {
            text-align: center;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 10.5pt;
            font-weight: bold;
        }
        .kop-text h3 {
            margin: 1px 0;
            font-size: 11.5pt;
            font-weight: bold;
        }
        .kop-text h2 {
            margin: 2px 0;
            font-size: 13.5pt;
            font-weight: bold;
        }
        .kop-text p {
            margin: 0;
            font-size: 8.5pt;
        }
        .id-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .id-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .project-card {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .p-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 8px;
            font-size: 9pt;
        }
        .p-table th, .p-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .p-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .sig-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .sig-table td {
            vertical-align: top;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 15px; right: 15px; z-index: 9999; display: flex; gap: 8px;">
        <button onclick="window.print()" style="background: #4f46e5; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Rapor P5
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #1e293b; border: none; padding: 8px 12px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <!-- KOP SEKOLAH -->
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

        <div style="text-align: center; margin-bottom: 12px;">
            <h3 style="margin: 0; font-size: 12pt; font-weight: bold; text-decoration: underline;">
                RAPOR PROJEK PENGUATAN PROFIL PELAJAR PANCASILA (P5)
            </h3>
            <p style="margin: 2px 0 0 0; font-size: 9.5pt;">TAHUN PELAJARAN {{ $setting->academicYear->name ?? '2025/2026' }}</p>
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
        </table>

        <!-- DAFTAR PROJEK KOKURIKULER -->
        @forelse($projects as $pIdx => $project)
            @php
                $pScore = $scores->get($project->id);
                $dimScores = $pScore?->dimension_scores ?? [];
            @endphp
            <div class="project-card">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #666; padding-bottom: 5px; margin-bottom: 6px;">
                    <strong style="font-size: 10.5pt;">PROJEK {{ $pIdx + 1 }}: {{ strtoupper($project->title) }}</strong>
                    <span style="font-size: 9pt; background: #eee; padding: 2px 8px; border-radius: 3px;">Tema: {{ $project->theme }}</span>
                </div>
                <p style="font-size: 8.5pt; text-align: justify; margin: 4px 0 8px 0; color: #333;">
                    {{ $project->description }}
                </p>

                <table class="p-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Dimensi Profil Pelajar Pancasila</th>
                            <th style="width: 12.5%;">MB</th>
                            <th style="width: 12.5%;">SB</th>
                            <th style="width: 12.5%;">BSH</th>
                            <th style="width: 12.5%;">SAB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->target_dimensions ?? [] as $dim)
                            @php
                                $val = $dimScores[$dim] ?? 'BSH';
                            @endphp
                            <tr>
                                <td>{{ $dim }}</td>
                                <td style="text-align: center; font-weight: bold;">{{ $val === 'MB' ? '✓' : '' }}</td>
                                <td style="text-align: center; font-weight: bold;">{{ $val === 'SB' ? '✓' : '' }}</td>
                                <td style="text-align: center; font-weight: bold;">{{ $val === 'BSH' ? '✓' : '' }}</td>
                                <td style="text-align: center; font-weight: bold;">{{ $val === 'SAB' ? '✓' : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="font-size: 8.5pt; margin-top: 4px;">
                    <strong>Catatan Proses Projek:</strong>
                    <p style="margin: 2px 0 0 0; font-style: italic;">
                        {{ $pScore?->process_notes ?? 'Peserta didik aktif bergotong royong, menunjukkan kreativitas, dan bernalar kritis dalam menyelesaikan seluruh tahapan karya projek.' }}
                    </p>
                </div>
            </div>
        @empty
            <div style="border: 1px dashed #999; padding: 25px; text-align: center; color: #666;">
                Belum ada kegiatan projek kokurikuler yang aktif untuk tingkat ini.
            </div>
        @endforelse

        <!-- Keterangan Predikat -->
        <div style="font-size: 8pt; color: #555; margin-top: 5px;">
            <em>Keterangan: MB = Mulai Berkembang | SB = Sedang Berkembang | BSH = Berkembang Sesuai Harapan | SAB = Sangat Berkembang</em>
        </div>

        <!-- TANDA TANGAN (KEPALA SEKOLAH DI KIRI!) -->
        <table class="sig-table">
            <tr>
                <td style="width: 50%;">
                    <p style="margin: 0;">Mengetahui,</p>
                    <p style="margin: 2px 0 55px 0;">Kepala Sekolah,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        {{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}
                    </p>
                    <p style="margin: 2px 0 0 0;">
                        NIP. {{ $setting->headmaster_nip ?? '196805141994121001' }}
                    </p>
                </td>
                <td style="width: 50%;">
                    <p style="margin: 0;">{{ $setting->report_place ?? 'Lengkong' }}, {{ Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</p>
                    <p style="margin: 2px 0 55px 0;">Koordinator Projek,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        {{ $projects->first()?->coordinator->name ?? 'Tim Fasilitator P5' }}
                    </p>
                    <p style="margin: 2px 0 0 0;">
                        NIP. {{ $projects->first()?->coordinator->nip ?? '-' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>


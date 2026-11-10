<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Digital Perkembangan Siswa - {{ $student->name }} ({{ $student->nis }}) [{{ strtoupper($paper ?? 'a4') }}]</title>
    @php
        $isF4 = ($paper ?? request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : '210mm 297mm' }} portrait;
            margin: {{ $isF4 ? '1.0cm 1.4cm' : '1.0cm 1.3cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '9.5pt' : '9pt' }};
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 18px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            font-weight: bold;
            padding: 7px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back {
            background: #334155;
            color: #fff;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
        }
        .paper-toggle-group {
            display: inline-flex;
            align-items: center;
            background: #1e293b;
            border-radius: 6px;
            padding: 2px;
            border: 1px solid #334155;
        }
        .paper-toggle-btn {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .paper-toggle-btn.active {
            background: #6366f1;
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .paper-toggle-btn.inactive {
            color: #94a3b8;
        }
        .doc-wrapper {
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 0 auto;
            padding: 5px 15px;
            box-sizing: border-box;
        }
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 3px double #000;
            padding-bottom: 4px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 110px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .kop-header h4 {
            margin: 0;
            font-size: 10pt;
            font-weight: normal;
        }
        .kop-header h3 {
            margin: 1px 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .kop-header h2 {
            margin: 1px 0;
            font-size: 14pt;
            font-weight: 900;
        }
        .kop-header p {
            margin: 1px 0 0 0;
            font-size: 8pt;
            color: #222;
        }
        .doc-title {
            text-align: center;
            margin: 8px 0 10px 0;
        }
        .doc-title h1 {
            margin: 0;
            font-size: 11.5pt;
            text-decoration: underline;
            font-weight: bold;
            text-transform: uppercase;
        }
        .doc-title span {
            font-size: 8.5pt;
            font-style: italic;
        }
        table.meta-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 9pt;
        }
        table.meta-table td {
            padding: 1.5px 3px;
            vertical-align: top;
        }
        .section-title {
            font-weight: bold;
            font-size: 9.5pt;
            margin-top: 10px;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-left: 3px solid #1e293b;
            padding-left: 6px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 8px 0;
            font-size: 8.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 3.5px 5px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .ai-box {
            border: 1px solid #64748b;
            padding: 6px 8px;
            background: #fafafa;
            font-size: 8.5pt;
            margin-bottom: 8px;
            border-radius: 4px;
            line-height: 1.35;
        }
        .recom-box {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            margin-bottom: 6px;
            border-radius: 4px;
            background: #f8fafc;
            font-size: 8.5pt;
        }
        .recom-box h5 {
            margin: 0 0 3px 0;
            font-size: 8.5pt;
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            margin-top: 14px;
            page-break-inside: avoid;
            font-size: 8.5pt;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            padding: 4px;
        }
        .signature-space {
            height: 48px;
        }
        @media print {
            .no-print-bar { display: none !important; }
            .doc-wrapper { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Bar Kontrol Navigasi & Pilihan Ukuran Kertas -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            @if(!empty($fromPortal) || request()->has('from_portal') || !auth()->check())
                <a href="{{ route('parent.portal', ['nisn' => $student->nisn]) }}" class="btn-back">← Kembali ke Portal Orang Tua</a>
            @else
                <a href="{{ route('raport.index', ['student_id' => $student->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn-back">← Kembali</a>
            @endif
            <span>Raport: <strong>{{ $student->name }} ({{ $student->nis }})</strong></span>
        </div>

        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}" title="Format Standar Internasional">
                    📄 Kertas A4 (210 × 297 mm)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}" title="Format HVS Folio Indonesia">
                    📜 Kertas F4 / Folio (215 × 330 mm)
                </a>
            </div>
            <button type="button" onclick="window.print()" class="btn-print">
                🖨️ Cetak Dokumen ({{ $isF4 ? 'F4 / Folio' : 'A4' }})
            </button>
        </div>
    </div>

    <div class="doc-wrapper">
        <!-- KOP SURAT RESMI -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat Resmi {{ $schoolName ?? 'Sekolah' }}">
            </div>
        @else
            <div class="kop-header">
                <h4>PEMERINTAH PROVINSI {{ $schoolProvince ?? 'JAWA BARAT' }}</h4>
                <h3>DINAS PENDIDIKAN</h3>
                <h4>{{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</h4>
                <h2>{{ strtoupper($schoolName ?? 'SMA NEGERI 1 LENGKONG') }}</h2>
                <p>{{ $schoolAddress ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat' }}</p>
                <p>Website: {{ $schoolWebsite ?? 'https://sman1lengkong.sch.id' }} • Email: {{ $schoolEmail ?? 'sman1lengkong@gmail.com' }}</p>
            </div>
        @endif

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title">
            <h1>RAPORT DIGITAL & BERKAS DOSSIER PERKEMBANGAN SISWA</h1>
            <span>Standar Penilaian Terpadu 8 Pilar & Evaluasi Multidimensi Berbasis AI (Format Cetak {{ $isF4 ? 'F4 / Folio' : 'A4' }})</span>
        </div>

        <!-- BIODATA SISWA -->
        <table class="meta-table">
            <tr>
                <td style="width: 16%;">Nama Siswa</td>
                <td style="width: 2%;">:</td>
                <td style="width: 42%; font-weight: bold;">{{ $student->name }}</td>
                <td style="width: 16%;">Tahun Pelajaran</td>
                <td style="width: 2%;">:</td>
                <td style="width: 22%;">{{ $student->academicYear?->name ?? '2026/2027' }}</td>
            </tr>
            <tr>
                <td>NIS / NISN</td>
                <td>:</td>
                <td>{{ $student->nis }} / {{ $student->nisn }}</td>
                <td>Kelas / Rombel</td>
                <td>:</td>
                <td>Kelas {{ $student->schoolClass?->name ?? '-' }} ({{ $student->schoolClass?->major ?? 'Umum' }})</td>
            </tr>
            <tr>
                <td>Wali Kelas</td>
                <td>:</td>
                <td>{{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }}</td>
                <td>Periode Evaluasi</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</td>
            </tr>
        </table>

        @php
            $pillars = $latestAiRaport?->context_data['pillars'] ?? null;
            $isFemale = $pillars['presensi_keputrian']['is_female'] ?? (strtolower($student->gender ?? '') === 'p' || strtolower($student->gender ?? '') === 'perempuan');

            // Presensi Harian
            $attTotal = $pillars['presensi_harian']['total_attendance'] ?? $student->attendances()->count();
            $attRate = ($attTotal == 0) ? 0.0 : ($pillars['presensi_harian']['rate'] ?? ($analysis['metrics']['attendance_rate'] ?? 0.0));
            $attHadir = $pillars['presensi_harian']['hadir'] ?? $student->attendances()->where('status', 'HADIR')->count();
            $attLate = $pillars['presensi_harian']['terlambat'] ?? ($analysis['metrics']['late_count'] ?? 0);
            $attIzin = $pillars['presensi_harian']['izin'] ?? $student->attendances()->where('status', 'IZIN')->count();
            $attSakit = $pillars['presensi_harian']['sakit'] ?? $student->attendances()->where('status', 'SAKIT')->count();
            $attAlpa = $pillars['presensi_harian']['alpa'] ?? $student->attendances()->where('status', 'ALPA')->count();

            // Poin Prestasi
            $achPoints = $pillars['poin_prestasi']['total_points'] ?? $student->totalAchievementPoints();
            $achTier = $pillars['poin_prestasi']['tier'] ?? ($analysis['metrics']['reward_tier'] ?? 'Dasar');

            // Poin Pelanggaran
            $violPoints = $pillars['poin_pelanggaran']['total_points'] ?? $student->totalViolationPoints();
            $violTier = $pillars['poin_pelanggaran']['tier'] ?? ($analysis['metrics']['guidance_tier'] ?? 'Aman');

            // 7 Anak Hebat
            $habitScore = $pillars['tujuh_kegiatan_hebat']['average_score'] ?? ($analysis['metrics']['habit_score'] ?? 0.0);
            $habitStatus = $pillars['tujuh_kegiatan_hebat']['status'] ?? ($analysis['metrics']['habit_status'] ?? 'Perlu Pendampingan');
            $habitDays = $pillars['tujuh_kegiatan_hebat']['logged_days'] ?? ($analysis['metrics']['habit_logged_days'] ?? 0);

            // Shalat Dzuhur / Jumat (Putra & Putri)
            $dzuhurTotal = $pillars['shalat_dzuhur_jumat']['total_sessions'] ?? ($analysis['metrics']['worship_dzuhur_total'] ?? ($analysis['metrics']['dzuhur_total'] ?? 0));
            $dzuhurHadir = $pillars['shalat_dzuhur_jumat']['hadir_sessions'] ?? ($analysis['metrics']['worship_dzuhur_hadir'] ?? ($analysis['metrics']['dzuhur_hadir'] ?? 0));
            $dzuhurRate = $pillars['shalat_dzuhur_jumat']['rate'] ?? ($analysis['metrics']['worship_dzuhur_rate'] ?? ($analysis['metrics']['dzuhur_rate'] ?? 0.0));

            // Shalat Dhuha
            $dhuhaTotal = $pillars['shalat_dhuha']['total_sessions'] ?? ($analysis['metrics']['worship_dhuha_total'] ?? 0);
            $dhuhaHadir = $pillars['shalat_dhuha']['hadir_sessions'] ?? ($analysis['metrics']['worship_dhuha_hadir'] ?? 0);
            $dhuhaRate = $pillars['shalat_dhuha']['rate'] ?? ($analysis['metrics']['worship_dhuha_rate'] ?? 0.0);

            // Keputrian
            $keputrianTotal = $pillars['presensi_keputrian']['total_sessions'] ?? ($analysis['metrics']['worship_keputrian_total'] ?? ($analysis['metrics']['keputrian_total'] ?? 0));
            $keputrianHadir = $pillars['presensi_keputrian']['hadir_sessions'] ?? ($analysis['metrics']['worship_keputrian_hadir'] ?? ($analysis['metrics']['keputrian_hadir'] ?? 0));
            $keputrianRate = $pillars['presensi_keputrian']['rate'] ?? ($analysis['metrics']['worship_keputrian_rate'] ?? ($analysis['metrics']['keputrian_rate'] ?? 0.0));

            // Tahfidz Juz 30
            $tahfidzCompleted = $pillars['tahfidz_juz_30']['completed_surahs_count'] ?? ($analysis['metrics']['completed_surahs_count'] ?? 0);
            $tahfidzTarget = $pillars['tahfidz_juz_30']['target_surahs_count'] ?? ($tahfidzSummary['total_juz30'] ?? 37);
            $tahfidzPct = $pillars['tahfidz_juz_30']['progress_pct'] ?? ($analysis['metrics']['tahfidz_progress_pct'] ?? 0.0);
            $tahfidzStatus = $pillars['tahfidz_juz_30']['status'] ?? ($analysis['metrics']['tahfidz_status'] ?? 'Pemula');
            $tahfidzDeposits = $pillars['tahfidz_juz_30']['total_deposits'] ?? ($tahfidzSummary['period_count'] ?? 0);

            // Ekstrakurikuler
            $extraJoined = $pillars['presensi_ekstrakurikuler']['has_joined'] ?? ($analysis['metrics']['has_extracurricular'] ?? $student->extracurriculars()->exists());
            $extraTotal = $pillars['presensi_ekstrakurikuler']['total_sessions'] ?? ($analysis['metrics']['extracurricular_total'] ?? $student->extracurricularAttendances()->count());
            $extraHadir = $pillars['presensi_ekstrakurikuler']['hadir_sessions'] ?? ($analysis['metrics']['extracurricular_hadir'] ?? $student->extracurricularAttendances()->whereIn('status', ['HADIR', 'TERLAMBAT'])->count());
            $extraRate = $pillars['presensi_ekstrakurikuler']['rate'] ?? ($latestAiRaport?->extracurricular_rate ?? ($analysis['metrics']['extracurricular_rate'] ?? 0.0));
            $extraNotes = $pillars['presensi_ekstrakurikuler']['notes'] ?? ($latestAiRaport?->extracurricular_notes ?? ($analysis['metrics']['extracurricular_notes'] ?? ''));
            $extraNames = $pillars['presensi_ekstrakurikuler']['joined_names'] ?? ($analysis['metrics']['joined_extracurricular_names'] ?? $student->extracurriculars->pluck('name')->all());
        @endphp

        <!-- I. REKAPITULASI 8 PILAR PERKEMBANGAN SISWA & EKSTRAKURIKULER (RINGKASAN KESELURUHAN WAKTU) -->
        <div class="section-title">I. REKAPITULASI 8 PILAR PERKEMBANGAN SISWA & EKSTRAKURIKULER (RINGKASAN KESELURUHAN WAKTU)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 11%;">1. Presensi Harian</th>
                    <th style="width: 11%;">2. Poin Prestasi</th>
                    <th style="width: 11%;">3. Poin Pelanggaran</th>
                    <th style="width: 11%;">4. 7 Kegiatan Hebat</th>
                    <th style="width: 12%;">5. Shalat Dzuhur (Putra & Putri)</th>
                    <th style="width: 11%;">6. Shalat Dhuha</th>
                    <th style="width: 11%;">7. Presensi Keputrian</th>
                    <th style="width: 11%;">8. Tahfidz Juz 30</th>
                    <th style="width: 11%;">9. Ekstrakurikuler</th>
                </tr>
            </thead>
            <tbody>
                <tr style="text-align: center; font-weight: bold; vertical-align: top;">
                    <td>
                        @if($attTotal == 0)
                            <span style="color: #64748b; font-size: 8.5pt;">0.0% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Belum Terlaksana)</span>
                        @else
                            <span style="color: #059669; font-size: 8.5pt;">{{ $attRate }}% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #444;">
                                H: {{ $attHadir }}, T: {{ $attLate }}, I: {{ $attIzin }}, S: {{ $attSakit }}, A: {{ $attAlpa }}
                            </span>
                        @endif
                    </td>
                    <td style="color: #b45309;">
                        <span style="font-size: 8.5pt;">+{{ $achPoints }} Poin</span><br>
                        <span style="font-size: 7.5pt; font-weight: normal; color: #555;">
                            {{ $achTier }}
                        </span>
                    </td>
                    <td style="color: #b91c1c;">
                        <span style="font-size: 8.5pt;">{{ $violPoints }} Poin</span><br>
                        <span style="font-size: 7.5pt; font-weight: normal; color: #555;">
                            {{ $violTier }}
                        </span>
                    </td>
                    <td style="color: #0d9488;">
                        @if($habitDays == 0)
                            <span style="color: #64748b; font-size: 8.5pt;">0.0%</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Belum Mengisi)</span>
                        @else
                            <span style="font-size: 8.5pt;">{{ $habitScore }}%</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #555;">
                                {{ $habitStatus }}
                            </span>
                        @endif
                    </td>
                    <td style="color: #4f46e5;">
                        @if($dzuhurTotal == 0)
                            <span style="color: #64748b; font-size: 8.5pt;">0.0% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Belum Ada Sesi)</span>
                        @else
                            <span style="font-size: 8.5pt;">{{ $dzuhurRate }}% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #555;">
                                {{ $dzuhurHadir }}/{{ $dzuhurTotal }} Sesi (Putra & Putri)
                            </span>
                        @endif
                    </td>
                    <td style="color: #d97706;">
                        @if($dhuhaTotal == 0)
                            <span style="color: #64748b; font-size: 8.5pt;">0.0% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Belum Ada Sesi)</span>
                        @else
                            <span style="font-size: 8.5pt;">{{ $dhuhaRate }}% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #555;">
                                {{ $dhuhaHadir }}/{{ $dhuhaTotal }} Sesi
                            </span>
                        @endif
                    </td>
                    <td style="color: #db2777;">
                        @if(!$isFemale)
                            <span style="color: #64748b; font-size: 8.5pt;">Khusus Siswi</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Dikecualikan)</span>
                        @elseif($keputrianTotal == 0)
                            <span style="color: #64748b; font-size: 8.5pt;">0.0% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Belum Ada Sesi)</span>
                        @else
                            <span style="font-size: 8.5pt;">{{ $keputrianRate }}% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #555;">
                                {{ $keputrianHadir }}/{{ $keputrianTotal }} Sesi
                            </span>
                        @endif
                    </td>
                    <td style="color: #059669;">
                        @if($tahfidzDeposits == 0 && $tahfidzCompleted == 0)
                            <span style="color: #64748b; font-size: 8.5pt;">0 Surat (0.0%)</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Belum Setoran)</span>
                        @else
                            <span style="font-size: 8.5pt;">{{ $tahfidzCompleted }} / {{ $tahfidzTarget }} Surat</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #555;">
                                {{ $tahfidzPct }}% ({{ $tahfidzStatus }})
                            </span>
                        @endif
                    </td>
                    <td style="color: #7c3aed;">
                        @if(!$extraJoined)
                            <span style="color: #64748b; font-size: 8pt;">Tidak Mengikuti</span><br>
                            <span style="font-size: 7pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Tidak Ada Ekskul)</span>
                        @elseif($extraTotal == 0)
                            <span style="color: #64748b; font-size: 8.5pt;">0.0% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #94a3b8; font-style: italic;">(Belum Ada Sesi)</span>
                        @else
                            <span style="font-size: 8.5pt;">{{ $extraRate }}% Hadir</span><br>
                            <span style="font-size: 7.5pt; font-weight: normal; color: #555;">
                                {{ $extraHadir }}/{{ $extraTotal }} Sesi
                            </span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">II. RINGKASAN EVALUASI PERKEMBANGAN (AI SYSTEM SUMMARY)</div>
        <div class="ai-box" style="line-height: 1.4;">
            <div style="margin-bottom: 8px; border-bottom: 1px dashed #ccc; padding-bottom: 6px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <strong>Indeks Karakter Komposit: <span style="font-size: 1.1em; color: #4338ca;">{{ $latestAiRaport?->indeks_karakter ?? ($analysis['metrics']['development_index'] ?? 0) }} / 100</span></strong>
                    <span style="font-size: 0.85em; background: #e0e7ff; color: #3730a3; padding: 1px 6px; border-radius: 4px; font-weight: bold; margin-left: 6px;">{{ $latestAiRaport?->predikat_integritas ?? 'BAIK' }}</span>
                </div>
                <span style="font-size: 0.8em; color: #64748b;">
                    Update Terakhir: {{ $latestAiRaport?->generated_at ? \Carbon\Carbon::parse($latestAiRaport->generated_at)->translatedFormat('d M Y H:i') : \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d M Y H:i') }} WIB (Otomatis Terjadwal Tiap Jum'at 23:30 WIB)
                </span>
            </div>
            <p style="margin: 0 0 6px 0;"><strong>📊 Ringkasan Komprehensif (Keseluruhan Waktu):</strong><br/>{{ $latestAiRaport?->ringkasan_komprehensif ?? ($analysis['ringkasan_komprehensif'] ?? '-') }}</p>
            <p style="margin: 0 0 6px 0;"><strong>🌟 Profil Pelajar Pancasila:</strong><br/>{{ $latestAiRaport?->catatan_profil_pelajar ?? ($analysis['catatan_profil_pelajar'] ?? '-') }}</p>
            <p style="margin: 0 0 6px 0;"><strong>📝 Penilaian Terpadu Siswa:</strong><br/>{{ $latestAiRaport?->penilaian_siswa ?? ($analysis['penilaian_siswa'] ?? '-') }}</p>
            <p style="margin: 0 0 6px 0;"><strong>📈 Catatan Perkembangan & Evaluasi:</strong><br/>{{ $latestAiRaport?->perkembangan ?? ($analysis['perkembangan'] ?? '-') }}</p>
        </div>

        <!-- III. CATATAN IBADAH BERSAMA & TAHFIDZ AL-QUR'AN JUZ 30 -->
        <div class="section-title">III. KEGIATAN IBADAH BERJAMAAH & TAHFIDZ AL-QUR'AN JUZ 30</div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 8.5pt;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                    <div style="border: 1px solid #333; padding: 4px;">
                        <strong style="text-decoration: underline;">Presensi Ibadah Bersama (Terakhir):</strong>
                        <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 8pt;">
                            <tr style="background: #f1f5f9;">
                                <th style="border: 1px solid #666; padding: 2px;">Tanggal</th>
                                <th style="border: 1px solid #666; padding: 2px;">Kegiatan</th>
                                <th style="border: 1px solid #666; padding: 2px;">Waktu</th>
                                <th style="border: 1px solid #666; padding: 2px;">Status</th>
                            </tr>
                            @forelse($worshipRecords->take(4) as $w)
                                <tr>
                                    <td style="border: 1px solid #666; padding: 2px; text-align: center;">{{ \Carbon\Carbon::parse($w->date)->locale('id')->translatedFormat('D, d/m/y') }}</td>
                                    <td style="border: 1px solid #666; padding: 2px;">{{ $w->activity_type === 'DHUHA' ? 'Shalat Dhuha' : ($w->activity_type === 'KEPUTRIAN' ? 'Keputrian' : 'Dzuhur/Jumat') }}</td>
                                    <td style="border: 1px solid #666; padding: 2px; text-align: center;">{{ substr($w->time, 0, 5) }}</td>
                                    <td style="border: 1px solid #666; padding: 2px; text-align: center; font-weight: bold; color: {{ in_array($w->status, ['HADIR', 'TERLAMBAT']) ? '#059669' : '#dc2626' }};">{{ $w->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="border: 1px solid #666; text-align: center; font-style: italic;">Tidak ada catatan ibadah pada periode ini.</td></tr>
                            @endforelse
                        </table>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                    <div style="border: 1px solid #333; padding: 4px;">
                        <strong style="text-decoration: underline;">Setoran Tahfidz Qur'an Juz 30:</strong>
                        <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 8pt;">
                            <tr style="background: #f1f5f9;">
                                <th style="border: 1px solid #666; padding: 2px;">Tanggal</th>
                                <th style="border: 1px solid #666; padding: 2px;">Surat / Ayat</th>
                                <th style="border: 1px solid #666; padding: 2px;">Rubrik</th>
                                <th style="border: 1px solid #666; padding: 2px;">Hasil</th>
                            </tr>
                            @forelse($tahfidzRecords->take(4) as $t)
                                <tr>
                                    <td style="border: 1px solid #666; padding: 2px; text-align: center;">{{ $t->deposit_date->format('d/m/y') }}</td>
                                    <td style="border: 1px solid #666; padding: 2px;">QS. {{ $t->surah_name }} ({{ $t->ayat_range }})</td>
                                    <td style="border: 1px solid #666; padding: 2px; text-align: center;">M:{{ substr($t->makhraj, 0, 1) }} T:{{ substr($t->tajwid, 0, 1) }} K:{{ substr($t->kelancaran, 0, 1) }}</td>
                                    <td style="border: 1px solid #666; padding: 2px; text-align: center; font-weight: bold; color: {{ $t->overall_grade === 'Lulus' ? '#059669' : '#d97706' }};">{{ $t->overall_grade }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="border: 1px solid #666; text-align: center; font-style: italic;">Belum ada setoran tahfidz pada periode ini.</td></tr>
                            @endforelse
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- IV. CATATAN PRESTASI TERVERIFIKASI DALAM PERIODE -->
        <div class="section-title">IV. DAFTAR PRESTASI TERVERIFIKASI (PERIODE EVALUASI)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 12%;">Kategori</th>
                    <th>Judul Capaian Prestasi</th>
                    <th style="width: 15%;">Tingkat</th>
                    <th style="width: 10%;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodAchievements->take(5) as $idx => $ach)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center;">{{ $ach->date->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ $ach->category?->code ?? '-' }}</td>
                        <td>{{ $ach->title }}</td>
                        <td style="text-align: center;">{{ $ach->level }}</td>
                        <td style="text-align: center; font-weight: bold; color: #b45309;">+{{ $ach->points }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; font-style: italic; color: #555;">
                            Tidak ada catatan prestasi yang terverifikasi pada periode evaluasi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- V. CATATAN PELANGGARAN TATA TERTIB DALAM PERIODE -->
        <div class="section-title">V. DAFTAR PELANGGARAN TATA TERTIB (PERIODE EVALUASI)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 12%;">Kategori</th>
                    <th>Bentuk Pelanggaran Tata Tertib</th>
                    <th style="width: 20%;">Lokasi</th>
                    <th style="width: 10%;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodViolations->take(5) as $idx => $viol)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center;">{{ $viol->date->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ $viol->category?->code ?? '-' }}</td>
                        <td>{{ $viol->title }}</td>
                        <td>{{ $viol->location ?? '-' }}</td>
                        <td style="text-align: center; font-weight: bold; color: #b91c1c;">{{ $viol->points }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; font-style: italic; color: #047857;">
                            ✨ Disiplin Bersih — Tidak ada catatan pelanggaran tata tertib pada periode evaluasi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- VI. REKOMENDASI SARAN PENDAMPINGAN TERARAH -->
        @php
            $recom = $latestAiRaport?->rekomendasi ?? ($analysis['rekomendasi'] ?? []);
        @endphp
        <div class="section-title">VI. REKOMENDASI SARAN PENDAMPINGAN TERARAH (ACTION PLANS)</div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 8.5pt;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                    <div class="recom-box">
                        <h5>👨‍👩‍👧 Rekomendasi Bagi Orang Tua / Wali di Rumah:</h5>
                        <ul style="margin: 0; padding-left: 16px;">
                            @foreach($recom['orang_tua']['actions'] ?? [
                                'Dampingi rutinitas istirahat dan jam belajar mandiri di rumah.',
                                'Bangun komunikasi positif untuk mendengarkan refleksi keseharian anak.'
                            ] as $act)
                                <li>{{ $act }}</li>
                            @endforeach
                        </ul>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                    <div class="recom-box">
                        <h5>🎒 Rekomendasi Bagi Siswa (Refleksi Mandiri):</h5>
                        <ul style="margin: 0; padding-left: 16px;">
                            @foreach($recom['siswa']['actions'] ?? [
                                'Pertahankan kebiasaan positif dan tingkatkan komitmen ketepatan waktu.',
                                'Tingkatkan rutinitas murajaah hafalan Juz 30 dan ibadah berjamaah tepat waktu.'
                            ] as $act)
                                <li>{{ $act }}</li>
                            @endforeach
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top; padding-top: 3px;">
                    <div class="recom-box">
                        <h5>👨‍🏫 Rekomendasi Bagi Wali Kelas & Koordinator Keagamaan:</h5>
                        <ul style="margin: 0; padding-left: 16px;">
                            @foreach($recom['wali_kelas']['actions'] ?? ['Lakukan pembinaan berkala di kelas.'] as $act)
                                <li>{{ $act }}</li>
                            @endforeach
                            @foreach($recom['koordinator_keagamaan']['actions'] ?? ['Dampingi setoran hafalan Juz 30 secara teratur.'] as $act)
                                <li>{{ $act }}</li>
                            @endforeach
                        </ul>
                    </div>
                </td>
            </tr>
        </table>

        <!-- VII. PENGESAHAN DOKUMEN TIGA PIHAK (Kepala Sekolah Selalu di Kiri) -->
        <table class="signature-table">
            <tr>
                <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10px;">
                    Mengetahui,<br>
                    Kepala Sekolah,
                    <div class="signature-space"></div>
                    <strong><span style="text-decoration: underline;">{{ $headmasterName ?? \App\Models\Teacher::getHeadmaster()['name'] }}</span></strong><br>
                    NIP. {{ $headmasterNip ?? \App\Models\Teacher::getHeadmaster()['nip'] }}
                </td>
                <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10px;">
                    Mengetahui,<br>
                    Orang Tua / Wali Siswa,
                    <div class="signature-space"></div>
                    <strong>( .............................................................. )</strong>
                </td>
                <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10px;">
                    {{ $schoolCity ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                    Wali Kelas {{ $student->schoolClass?->name ?? '' }},
                    <div class="signature-space"></div>
                    <strong><span style="text-decoration: underline;">{{ $student->schoolClass?->homeroomTeacher?->name ?? '( .............................................................. )' }}</span></strong><br>
                    NIP. {{ $student->schoolClass?->homeroomTeacher?->nip ?? '........................................................' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

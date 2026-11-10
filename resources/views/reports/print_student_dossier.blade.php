<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berkas Perkembangan Siswa - {{ $student->name }} ({{ $student->nis }}) [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
            margin: {{ $isF4 ? '1.0cm 1.4cm' : '1.2cm 1.5cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '10.5pt' : '10pt' }};
            line-height: 1.35;
            background: #fff;
            margin: 0;
            padding: 0;
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
        }
        .paper-toggle-btn.inactive {
            color: #94a3b8;
        }
        .paper-toggle-btn.inactive:hover {
            color: #f8fafc;
            background: #1e293b;
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
        .doc-container {
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 0 auto;
            padding: 15px 25px;
        }
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 130px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: normal;
            letter-spacing: 0.5px;
        }
        .header h2 {
            margin: 2px 0;
            font-size: 15pt;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 9.5pt;
            color: #333;
        }
        .doc-title {
            text-align: center;
            margin: 14px 0 16px 0;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 12pt;
            text-decoration: underline;
            font-weight: bold;
        }
        .doc-title span {
            font-size: 10pt;
        }
        table.meta-table {
            width: 100%;
            margin-bottom: 14px;
            font-size: 10.5pt;
        }
        table.meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px 0;
            font-size: 10pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 14px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .ai-summary-box {
            border: 1px solid #777;
            padding: 8px 10px;
            background: #fafafa;
            font-size: 10pt;
            margin-bottom: 14px;
            border-radius: 4px;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
            font-size: 10pt;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 4px;
        }
        .sign-space {
            height: 60px;
        }
        @media print {
            .no-print, .no-print-bar { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Controls Bar (No Print) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="javascript:history.back()" class="btn-back">⬅ Kembali</a>
            <span style="font-weight: bold; font-size: 14px;">BERKAS PERKEMBANGAN SISWA TERPADU</span>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4 (210×297mm)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4 / Folio (215×330mm)
                </a>
            </div>

            <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak ({{ $isF4 ? 'F4 / Folio' : 'A4' }})</button>
        </div>
    </div>

    <!-- Printable Paper Area -->
    <div class="doc-container">
        <!-- Official School Header / Kop Surat -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
            </div>
    @else
        <div class="header">
            <h3>PEMERINTAH PROVINSI {{ $schoolProvince ?? 'JAWA BARAT' }}</h3>
            <h3>DINAS PENDIDIKAN</h3>
            <h4 style="margin: 2px 0; font-size: 11pt; font-weight: normal;">{{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</h4>
            <h2>{{ strtoupper($schoolName) }}</h2>
            <p>NPSN: {{ $schoolNpsn ?? '20539123' }} • NSS: {{ $schoolNss ?? '301051408001' }} • Akreditasi: {{ $schoolAccreditation ?? 'A (Unggul)' }}</p>
            <p>{{ $schoolAddress ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat' }} • Email: {{ $schoolEmail ?? 'sman1lengkong@gmail.com' }} • Web: {{ $schoolWebsite ?? 'https://sman1lengkong.sch.id' }}</p>
        </div>
    @endif

    <div class="doc-title">
        <h4>LAPORAN PERKEMBANGAN SISWA TERPADU</h4>
        <span>Tahun Ajaran {{ $student->academicYear?->name ?? '2026/2027' }} — Semester Ganjil</span>
    </div>

    <!-- Biodata Siswa -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%;">Nama Lengkap</td>
            <td style="width: 2%;">:</td>
            <td style="width: 40%; font-weight: bold;">{{ $student->name }}</td>
            <td style="width: 18%;">Tahun Pelajaran</td>
            <td style="width: 2%;">:</td>
            <td style="width: 20%;">{{ $student->academicYear?->name ?? '2026/2027' }}</td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td>:</td>
            <td>{{ $student->nis }} / {{ $student->nisn }}</td>
            <td>Kelas / Jurusan</td>
            <td>:</td>
            <td>{{ $student->schoolClass?->name ?? '-' }} / {{ $student->schoolClass?->major ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td>Wali Kelas</td>
            <td>:</td>
            <td>{{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }}</td>
            <td>Status Karakter</td>
            <td>:</td>
            <td>{{ $dossier['ai_analysis']['metrics']['habit_status'] }}</td>
        </tr>
    </table>

    @php
        $latestAi = $dossier['latest_ai_raport'] ?? $student->aiRaport;
        $ws = $dossier['worship_summary'] ?? [];
        $ts = $dossier['tahfidz_summary'] ?? [];
        $isFemale = in_array(strtoupper((string) $student->gender), ['P', 'PEREMPUAN', 'FEMALE'], true);
    @endphp

    <!-- Ringkasan Nilai dan Metrik 8 Pilar Standar Penilaian -->
    <div class="section-title">I. REKAPITULASI 8 PILAR STANDAR PENILAIAN PERKEMBANGAN SISWA</div>
    <table class="data-table" style="font-size: 8pt;">
        <thead>
            <tr>
                <th style="width: 12%;">1. Presensi</th>
                <th style="width: 12%;">2. Prestasi</th>
                <th style="width: 12%;">3. Pelanggaran</th>
                <th style="width: 12%;">4. 7 Karakter</th>
                <th style="width: 14%;">5. Shalat Dzuhur</th>
                <th style="width: 12%;">6. Shalat Dhuha</th>
                <th style="width: 12%;">7. Keputrian</th>
                <th style="width: 14%;">8. Tahfidz Juz 30</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-align: center; font-weight: bold; vertical-align: top;">
                <td style="color: #047857;">
                    {{ $latestAi?->attendance_rate ?? $student->attendancePercentage() }}% Hadir
                </td>
                <td style="color: #b45309;">
                    +{{ $student->totalAchievementPoints() }} Pts<br/>
                    <span style="font-size: 7pt; font-weight: normal; color: #555;">{{ $latestAi?->reward_tier ?? ($dossier['reward_status']['level'] ?? '-') }}</span>
                </td>
                <td style="color: #b91c1c;">
                    {{ $student->totalViolationPoints() }} Pts<br/>
                    <span style="font-size: 7pt; font-weight: normal; color: #555;">{{ $latestAi?->guidance_tier ?? ($dossier['guidance_status']['level'] ?? '-') }}</span>
                </td>
                <td style="color: #0d9488;">
                    {{ $latestAi?->habit_score ?? ($dossier['ai_analysis']['metrics']['habit_score'] ?? 0) }}%<br/>
                    <span style="font-size: 7pt; font-weight: normal; color: #555;">{{ str_replace('_', ' ', $latestAi?->habit_status ?? ($dossier['ai_analysis']['metrics']['habit_status'] ?? '-')) }}</span>
                </td>
                <td style="color: #4f46e5;">
                    {{ $latestAi?->worship_dzuhur_rate ?? ($ws['rate'] ?? 0) }}% Hadir<br/>
                    <span style="font-size: 7pt; font-weight: normal; color: #555;">Putra & Putri</span>
                </td>
                <td style="color: #d97706;">
                    {{ $latestAi?->worship_dhuha_rate ?? ($dossier['ai_analysis']['metrics']['worship_dhuha_rate'] ?? 0) }}% Hadir
                </td>
                <td style="color: #db2777;">
                    @if(!$isFemale)
                        <span style="color: #94a3b8; font-size: 7.5pt; font-style: italic;">Khusus Siswi</span>
                    @else
                        {{ $latestAi?->worship_keputrian_rate ?? ($dossier['ai_analysis']['metrics']['worship_keputrian_rate'] ?? 0) }}% Hadir
                    @endif
                </td>
                <td style="color: #059669;">
                    {{ $latestAi?->tahfidz_completed_count ?? ($ts['completed_surahs_count'] ?? 0) }}/{{ $latestAi?->tahfidz_target_count ?? ($ts['total_juz30'] ?? 37) }} Surat<br/>
                    <span style="font-size: 7pt; font-weight: normal; color: #555;">({{ $latestAi?->tahfidz_status ?? ($ts['status'] ?? 'Pemula') }})</span>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- AI Synthesis Summary -->
    <div class="section-title">II. RINGKASAN EVALUASI PERKEMBANGAN (AI SYSTEM SUMMARY)</div>
    <div class="ai-summary-box" style="line-height: 1.4;">
        <p style="margin: 0 0 6px 0;"><strong>📊 Ringkasan Komprehensif:</strong><br/>{{ $dossier['ai_analysis']['ringkasan_komprehensif'] ?? '-' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>🌟 Profil Pelajar Pancasila:</strong><br/>{{ $dossier['ai_analysis']['catatan_profil_pelajar'] ?? '-' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>📝 Penilaian Terpadu:</strong><br/>{{ $dossier['ai_analysis']['penilaian_siswa'] ?? '-' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>📈 Tren Perkembangan:</strong><br/>{{ $dossier['ai_analysis']['perkembangan'] ?? '-' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>🔍 Analisis Masalah:</strong><br/>{{ $dossier['ai_analysis']['analisis_masalah'] ?? '-' }}</p>
        <p style="margin: 0 0 0 0;"><strong>💡 Strategi Pemecahan:</strong><br/>{{ $dossier['ai_analysis']['pemecahan_masalah'] ?? '-' }}</p>
    </div>

    <!-- Catatan Prestasi -->
    <div class="section-title">III. DAFTAR PRESTASI TERVERIFIKASI (KATEGORI R1 - R10)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 12%;">Kategori</th>
                <th>Nama Capaian Prestasi</th>
                <th style="width: 15%;">Tingkat</th>
                <th style="width: 10%;">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dossier['achievements'] as $idx => $ach)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $ach->date->format('d/m/Y') }}</td>
                    <td>{{ $ach->category->code }}</td>
                    <td>{{ $ach->title }}</td>
                    <td>{{ $ach->level }}</td>
                    <td style="text-align: center; font-weight: bold;">+{{ $ach->points }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; font-style: italic;">Belum ada catatan prestasi yang terverifikasi.</td>
                </tr>
            @endforelse
            <tr style="font-weight: bold; background: #fcfcfc;">
                <td colspan="5" style="text-align: right;">TOTAL POIN PRESTASI KUMULATIF:</td>
                <td style="text-align: center;">{{ $student->totalAchievementPoints() }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Catatan Pelanggaran Tata Tertib -->
    <div class="section-title">IV. DAFTAR PELANGGARAN TATA TERTIB (KATEGORI P1 - P6)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 12%;">Kategori</th>
                <th>Bentuk Pelanggaran</th>
                <th style="width: 20%;">Lokasi</th>
                <th style="width: 10%;">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dossier['violations'] as $idx => $viol)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $viol->date->format('d/m/Y') }}</td>
                    <td>{{ $viol->category->code }}</td>
                    <td>{{ $viol->title }}</td>
                    <td>{{ $viol->location ?? '-' }}</td>
                    <td style="text-align: center; font-weight: bold; color: #b91c1c;">{{ $viol->points }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; font-style: italic;">Tidak ada catatan pelanggaran tata tertib (Disiplin Bersih).</td>
                </tr>
            @endforelse
            <tr style="font-weight: bold; background: #fcfcfc;">
                <td colspan="5" style="text-align: right;">TOTAL POIN PELANGGARAN KUMULATIF:</td>
                <td style="text-align: center; color: #b91c1c;">{{ $student->totalViolationPoints() }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tanda Tangan Pengesahan (Kepala Sekolah Selalu di Kiri) -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah,
                <div class="sign-space"></div>
                <strong>{{ $headmasterName ?? \App\Models\SchoolSetting::get('headmaster_name', 'Pian Sopian, S.Pd., M.M.Pd') }}</strong><br>
                NIP. {{ $headmasterNip ?? \App\Models\SchoolSetting::get('headmaster_nip', '198009132008011005') }}
            </td>
            <td>
                Mengetahui,<br>
                Orang Tua / Wali Siswa,
                <div class="sign-space"></div>
                <strong>( .................................................. )</strong>
            </td>
            <td>
                {{ $schoolCity ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Wali Kelas,
                <div class="sign-space"></div>
                <strong>{{ $student->schoolClass?->homeroomTeacher?->name ?? '( .................................................. )' }}</strong><br>
                NIP. {{ $student->schoolClass?->homeroomTeacher?->nip ?? '........................' }}
            </td>
        </tr>
    </table>

    </div>

</body>
</html>

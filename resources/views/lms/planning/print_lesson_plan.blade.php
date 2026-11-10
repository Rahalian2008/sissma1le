<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul Ajar Pertemuan {{ $plan->meeting_number }} - {{ $plan->title }} - SMAN 1 Lengkong</title>
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #111827;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px 0;
        }
        .page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 15mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header-kop h4 {
            font-size: 11pt;
            margin: 0;
            font-weight: normal;
            letter-spacing: 0.5px;
        }
        .header-kop h3 {
            font-size: 12pt;
            margin: 2px 0;
            font-weight: bold;
        }
        .header-kop h2 {
            font-size: 14pt;
            margin: 2px 0;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header-kop p {
            font-size: 9pt;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        .title-doc {
            text-align: center;
            margin: 16px 0;
        }
        .title-doc h1 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .title-doc span {
            font-size: 10pt;
            display: block;
            margin-top: 4px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9pt;
            font-weight: bold;
            border: 1px solid #000;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .info-table, .step-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
            font-size: 10.5pt;
        }
        .info-table td.label {
            width: 28%;
            font-weight: normal;
        }
        .info-table td.separator {
            width: 2%;
        }
        .info-table td.value {
            width: 70%;
        }
        .content-box {
            padding: 6px 10px;
            background-color: #fafafa;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 10.5pt;
            white-space: pre-line;
            margin-bottom: 8px;
            line-height: 1.5;
        }
        .signatures {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signatures td {
            width: 33.3%;
            text-align: center;
            vertical-align: top;
            font-size: 10.5pt;
        }
        .sign-space {
            height: 65px;
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
        .btn:hover {
            opacity: 0.9;
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

    <!-- Non-print floating action toolbar -->
    <div class="no-print-bar">
        <button onclick="window.print()" class="btn">
            <span>🖨️</span>
            <span>Cetak Dokumen Modul Ajar</span>
        </button>
        <a href="{{ route('lms.planning.index', ['tab' => 'lesson_plans']) }}" class="btn btn-secondary">
            <span>⬅️</span>
            <span>Kembali ke Pusat Perencanaan</span>
        </a>
    </div>

    <div class="page">
        <!-- Kop Surat Resmi Sekolah -->
        <div class="header-kop">
            <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
            <h3>DINAS PENDIDIKAN</h3>
            <h3>CABANG DINAS PENDIDIKAN WILAYAH V</h3>
            <h2>SMA NEGERI 1 LENGKONG</h2>
            <p>Jl. Raya Lengkong No. 45, Lengkong, Kec. Lengkong, Kabupaten Sukabumi, Jawa Barat 43174</p>
        </div>

        <!-- Judul Dokumen -->
        <div class="title-doc">
            <h1>MODUL AJAR KURIKULUM MERDEKA</h1>
            <span>Tahun Ajaran 2026/2027 • Standar BSKAP Permendikdasmen No. 13/2025</span>
            <div>
                <span class="status-badge">
                    Status: {{ $plan->status === 'APPROVED' ? 'DISETUJUI KURIKULUM (KBM AKTIF)' : ($plan->status === 'SUBMITTED' ? 'MENUNGGU VERIFIKASI' : ($plan->status === 'REVISED' ? 'PERLU REVISI' : 'DRAF GURU')) }}
                </span>
            </div>
        </div>

        <!-- I. INFORMASI UMUM -->
        <div class="section-title">I. INFORMASI UMUM</div>
        <table class="info-table">
            <tr>
                <td class="label">Nama Penyusun</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $plan->teacher->user->name ?? $plan->teacher->name ?? 'Guru Pengampu' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Satuan Pendidikan</td>
                <td class="separator">:</td>
                <td class="value">SMA Negeri 1 Lengkong</td>
            </tr>
            <tr>
                <td class="label">Mata Pelajaran</td>
                <td class="separator">:</td>
                <td class="value">{{ $plan->courseClass->subject->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Fase / Kelas / Semester</td>
                <td class="separator">:</td>
                <td class="value">Fase {{ $plan->grade_level === 'X' ? 'E' : 'F' }} / Kelas {{ $plan->courseClass->schoolClass->name ?? $plan->grade_level }} / Semester {{ $plan->semester }}</td>
            </tr>
            <tr>
                <td class="label">Pertemuan & Alokasi Waktu</td>
                <td class="separator">:</td>
                <td class="value">Pertemuan Ke-{{ $plan->meeting_number }} ({{ $plan->time_allocation_minutes }} Menit / 2 JP)</td>
            </tr>
            <tr>
                <td class="label">Topik / Tema Pembelajaran</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $plan->title }}</strong></td>
            </tr>
            <tr>
                <td class="label">Model & Metode Pembelajaran</td>
                <td class="separator">:</td>
                <td class="value">{{ $plan->learning_model ?? 'Problem-Based Learning (PBL)' }} (Diskusi Kelompok, Praktik, Presentasi)</td>
            </tr>
            <tr>
                <td class="label">Profil Pelajar Pancasila</td>
                <td class="separator">:</td>
                <td class="value">
                    @if(!empty($plan->target_profile))
                        {{ is_array($plan->target_profile) ? implode(', ', $plan->target_profile) : $plan->target_profile }}
                    @else
                        Bernalar Kritis, Kreatif, Gotong Royong, Mandiri
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Media & Sumber Belajar</td>
                <td class="separator">:</td>
                <td class="value">{{ $plan->media_and_resources ?? 'LCD Proyektor, Modul LMS SMAN 1 Lengkong, Perangkat Komputer/Laptop, Internet' }}</td>
            </tr>
        </table>

        <!-- II. KOMPONEN INTI -->
        <div class="section-title">II. KOMPONEN INTI</div>

        <div style="font-weight: bold; margin-bottom: 4px;">A. Capaian & Tujuan Pembelajaran (TP) yang Disasar:</div>
        @if($plan->objectives->isNotEmpty())
            <ul style="margin: 0 0 10px 20px; padding: 0;">
                @foreach($plan->objectives as $tp)
                    <li style="margin-bottom: 4px;">
                        <strong>[TP {{ $tp->tp_code }}]</strong> {{ $tp->title }} &mdash; <em>{{ $tp->description }}</em>
                    </li>
                @endforeach
            </ul>
        @else
            <p style="margin: 0 0 10px 0; font-style: italic; color: #4b5563;">
                Mengacu pada capaian pembelajaran fase {{ $plan->grade_level === 'X' ? 'E' : 'F' }} pada mata pelajaran terkait.
            </p>
        @endif

        <div style="font-weight: bold; margin-bottom: 4px;">B. Skenario Langkah-Langkah Pembelajaran:</div>
        
        <table class="info-table" style="border: 1px solid #d1d5db; margin-top: 6px;">
            <tr style="background-color: #f3f4f6; font-weight: bold; border-bottom: 1px solid #d1d5db;">
                <td style="width: 25%; padding: 6px; border-right: 1px solid #d1d5db;">Tahapan KBM</td>
                <td style="width: 75%; padding: 6px;">Aktivitas Pembelajaran (Guru & Peserta Didik)</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 6px; font-weight: bold; border-right: 1px solid #d1d5db;">
                    1. Pendahuluan<br><span style="font-weight: normal; font-size: 9pt;">(15 Menit)</span>
                </td>
                <td style="padding: 6px; white-space: pre-line;">{{ $plan->learning_steps['introduction'] ?? 'Apersepsi, salam, presensi, penyampaian tujuan pembelajaran.' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 6px; font-weight: bold; border-right: 1px solid #d1d5db;">
                    2. Kegiatan Inti<br><span style="font-weight: normal; font-size: 9pt;">(60 Menit)</span>
                </td>
                <td style="padding: 6px; white-space: pre-line;">{{ $plan->learning_steps['core'] ?? 'Pelaksanaan sintaks model pembelajaran, diskusi, penugasan lembar kerja, verifikasi hasil.' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px; font-weight: bold; border-right: 1px solid #d1d5db;">
                    3. Penutup<br><span style="font-weight: normal; font-size: 9pt;">(15 Menit)</span>
                </td>
                <td style="padding: 6px; white-space: pre-line;">{{ $plan->learning_steps['closing'] ?? 'Refleksi peserta didik, kesimpulan materi, penyampaian KBM selanjutnya, doa penutup.' }}</td>
            </tr>
        </table>

        <div style="font-weight: bold; margin-top: 10px; margin-bottom: 4px;">C. Rencana Asesmen:</div>
        <table class="info-table" style="border: 1px solid #d1d5db; margin-bottom: 12px;">
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="width: 25%; padding: 5px; font-weight: bold; border-right: 1px solid #d1d5db;">Asesmen Diagnostik</td>
                <td style="padding: 5px;">{{ $plan->assessment_plan['diagnostic'] ?? 'Tes formatif awal non-kognitif dan kognitif awal sebelum pembelajaran dimulai.' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 5px; font-weight: bold; border-right: 1px solid #d1d5db;">Asesmen Formatif</td>
                <td style="padding: 5px;">{{ $plan->assessment_plan['formative'] ?? 'Penilaian observasi keterlibatan diskusi dan rubrik kerja lembar kerja peserta didik (LKPD).' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px; font-weight: bold; border-right: 1px solid #d1d5db;">Asesmen Sumatif</td>
                <td style="padding: 5px;">{{ $plan->assessment_plan['summative'] ?? 'Tes tertulis CBT / kuis daring pada akhir lingkup materi via LMS SMAN 1 Lengkong.' }}</td>
            </tr>
        </table>

        <!-- III. LAMPIRAN RINGKAS -->
        @if($plan->lkpd_content)
            <div class="section-title">III. LAMPIRAN: LEMBAR KERJA PESERTA DIDIK (LKPD)</div>
            <div class="content-box">
                {{ $plan->lkpd_content }}
            </div>
        @endif

        @if($plan->review_notes)
            <div style="margin-top: 10px; padding: 6px 10px; background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 4px; font-size: 9.5pt;">
                <strong>Catatan Tim Verifikator Kurikulum:</strong> {{ $plan->review_notes }}
                <div style="font-size: 8.5pt; color: #78350f; margin-top: 2px;">
                    Diverifikasi oleh: {{ $plan->reviewer->name ?? 'Wakasek Kurikulum' }} pada {{ $plan->reviewed_at ? \Carbon\Carbon::parse($plan->reviewed_at)->translatedFormat('d F Y H:i') : '-' }}
                </div>
            </div>
        @endif

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
                    Penyusun Modul Ajar,
                    <div class="sign-space"></div>
                    <strong>{{ $plan->teacher->user->name ?? $plan->teacher->name ?? 'Guru Pengampu' }}</strong><br>
                    <span>NIP. {{ $plan->teacher->nip ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>


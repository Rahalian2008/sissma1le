<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Hasil Supervisi Akademik - {{ $supervision->teacher->name ?? 'Guru' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.35;
            color: #111827;
            background-color: #f3f4f6;
            margin: 0;
            padding: 15px 0;
        }
        .page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
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
            font-size: 10pt;
            margin: 0;
            font-weight: normal;
        }
        .header-kop h3 {
            font-size: 11pt;
            margin: 2px 0;
            font-weight: bold;
        }
        .header-kop h2 {
            font-size: 13pt;
            margin: 2px 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header-kop p {
            font-size: 8pt;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        .title-doc {
            text-align: center;
            margin: 14px 0 16px 0;
        }
        .title-doc h1 {
            font-size: 12.5pt;
            font-weight: bold;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title-doc p {
            font-size: 9.5pt;
            margin: 4px 0 0 0;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9.5pt;
        }
        .score-table th, .score-table td {
            border: 1px solid #1f2937;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .score-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
        }
        .section-box {
            border: 1px solid #1f2937;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        .section-box h3 {
            font-size: 10pt;
            font-weight: bold;
            margin: 0 0 6px 0;
            border-bottom: 1px dashed #d1d5db;
            padding-bottom: 4px;
        }
        .signature-table {
            width: 100%;
            margin-top: 24px;
            page-break-inside: avoid;
            font-size: 9.5pt;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
            width: 50%;
        }
        .sign-space {
            height: 60px;
        }
        .btn-print-bar {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-print {
            background-color: #2563eb;
            color: #fff;
            padding: 8px 18px;
            font-size: 10pt;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .page {
                box-shadow: none;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .btn-print-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="btn-print-bar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Lembar Hasil Supervisi Akademik (A4)</button>
    </div>

    <div class="page">
        <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
        <x-print-kop :school="$school ?? null" />

        <!-- Judul Dokumen -->
        <div class="title-doc">
            <h1>INSTRUMEN & LEMBAR HASIL SUPERVISI AKADEMIK GURU</h1>
            <p>KURIKULUM MERDEKA • TAHUN AJARAN {{ $supervision->academic_year }} (SEMESTER {{ $supervision->semester }})</p>
        </div>

        <!-- Identitas Supervisi -->
        <table class="info-table">
            <tr>
                <td style="width: 25%;"><strong>Nama Guru Terobservasi</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;"><strong>{{ $supervision->teacher->name ?? '-' }}</strong></td>
                <td style="width: 15%;"><strong>Tanggal Observasi</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 16%;">{{ $supervision->supervision_date ? $supervision->supervision_date->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td><strong>NIP / NUPTK</strong></td>
                <td>:</td>
                <td>{{ $supervision->teacher->nip ?? '-' }}</td>
                <td><strong>Mata Pelajaran</strong></td>
                <td>:</td>
                <td>{{ $supervision->courseClass->subject->name ?? 'Lintas Mapel Ampu' }}</td>
            </tr>
            <tr>
                <td><strong>Pangkat / Golongan</strong></td>
                <td>:</td>
                <td>{{ $supervision->teacher->rank ?? 'Penata Muda / Guru' }}</td>
                <td><strong>Kelas Teramati</strong></td>
                <td>:</td>
                <td>{{ $supervision->courseClass->schoolClass->name ?? 'Semua Tingkat' }}</td>
            </tr>
            <tr>
                <td><strong>Nama Supervisor</strong></td>
                <td>:</td>
                <td><strong>{{ $supervision->supervisor->name ?? 'Pimpinan / Pengawas' }}</strong></td>
                <td><strong>Jenis Instrumen</strong></td>
                <td>:</td>
                <td>{{ $supervision->instrument_type }}</td>
            </tr>
        </table>

        <!-- Tabel Penilaian 4 Pilar -->
        <table class="score-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 50%;">Dimensi / Aspek Supervisi Akademik</th>
                    <th style="width: 16%;">Skor (0-100)</th>
                    <th style="width: 28%;">Keterangan Ketercapaian</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>
                        <strong>Perencanaan Pembelajaran (Modul Ajar / RPP Digital)</strong><br>
                        <span style="font-size: 8pt; color: #4b5563;">Kesesuaian CP/TP, sintaks KBM 3 tahap, diferensiasi, dan LKPD.</span>
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $supervision->planning_score }}</td>
                    <td>{{ $supervision->planning_score >= 85 ? 'Sangat Lengkap & Tervalidasi' : 'Cukup Lengkap' }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>
                        <strong>Pelaksanaan KBM di Kelas & Presensi Jurnal Mengajar</strong><br>
                        <span style="font-size: 8pt; color: #4b5563;">Interaksi kelas, pemanfaatan media LMS, ketercapaian TP, catatan dinamika.</span>
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $supervision->execution_score }}</td>
                    <td>{{ $supervision->execution_score >= 85 ? 'Interaktif & Terstruktur' : 'Berjalan Baik' }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td>
                        <strong>Asesmen Hasil Belajar, Bank Soal HOTS & Remedial</strong><br>
                        <span style="font-size: 8pt; color: #4b5563;">Kisi-kisi soal terhubung TP, level kognitif L1-L3, dan tindak lanjut remedial.</span>
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $supervision->assessment_score }}</td>
                    <td>{{ $supervision->assessment_score >= 85 ? 'Autentik & Terpetakan TP' : 'Sesuai Standar' }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td>
                        <strong>Refleksi Pembelajaran Guru & Portofolio Siswa</strong><br>
                        <span style="font-size: 8pt; color: #4b5563;">Dokumentasi kendala belajar, tindak lanjut perbaikan, kurasi karya siswa.</span>
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $supervision->reflection_score }}</td>
                    <td>{{ $supervision->reflection_score >= 85 ? 'Reflektif & Berkelanjutan' : 'Terlaksana Cukup' }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="2" style="text-align: right; padding: 8px;">NILAI AKHIR RATA-RATA SUPERVISI:</td>
                    <td style="text-align: center; font-size: 11pt; color: #1e3a8a;">{{ $supervision->final_score }}</td>
                    <td style="text-align: center; font-size: 10.5pt; color: #15803d;">
                        PREDIKAT: {{ $supervision->predicate }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Catatan Hasil Pengamatan -->
        <div class="section-box">
            <h3>CATATAN PENGAMATAN SUPERVISOR</h3>
            <p style="text-align: justify; margin: 4px 0;">
                {{ $supervision->notes ?: 'Pendidik telah melaksanakan pembelajaran sesuai kaidah Kurikulum Merdeka dengan perangkat pembelajaran yang lengkap dan terverifikasi.' }}
            </p>
        </div>

        <!-- Rekomendasi Tindak Lanjut -->
        <div class="section-box">
            <h3>REKOMENDASI TINDAK LANJUT PIMPINAN</h3>
            <p style="text-align: justify; margin: 4px 0;">
                {{ $supervision->recommendation ?: 'Pertahankan kualitas penyusunan asesmen berbasis TP dan lanjutkan fasilitasi kurasi portofolio karya unggulan peserta didik.' }}
            </p>
        </div>

        <!-- Pengesahan Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala {{ $school?->name ?? 'SMAN 1 Lengkong' }} / Supervisor</strong>
                    <div class="sign-space"></div>
                    <strong>{{ $supervision->supervisor->name ?? ($school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.') }}</strong><br>
                    <span>NIP. {{ $supervision->supervisor->nip ?? ($school?->headmaster_nip ?: '198009132008011005') }}</span>
                </td>
                <td>
                    Lengkong, {{ $supervision->supervision_date ? $supervision->supervision_date->format('d F Y') : date('d F Y') }}<br>
                    <strong>Guru yang Disupervisi</strong>
                    <div class="sign-space"></div>
                    <strong>{{ $supervision->teacher->name ?? 'Guru Pengampu' }}</strong><br>
                    <span>NIP. {{ $supervision->teacher->nip ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>


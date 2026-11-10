<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RPL BK 1 Lembar Kurikulum Merdeka - {{ $rpl->rpl_code }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #111; line-height: 1.25; }
        .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 12px; }
        .kop h2 { margin: 0; font-size: 12pt; text-transform: uppercase; }
        .kop h3 { margin: 1px 0; font-size: 11pt; text-transform: uppercase; font-weight: normal; }
        .kop p { margin: 1px 0; font-size: 8.5pt; }
        .title { text-align: center; font-weight: bold; font-size: 11pt; text-transform: uppercase; margin-bottom: 10px; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9.5pt; }
        .meta-table td { padding: 2px 4px; vertical-align: top; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 9.5pt; margin-top: 8px; margin-bottom: 3px; background-color: #f2f2f2; padding: 2px 5px; border-left: 3px solid #000; }
        .content-box { font-size: 9.5pt; margin-left: 8px; line-height: 1.3; }
        .content-box p { margin: 3px 0; }
        .ttd-container { margin-top: 25px; display: flex; justify-content: space-between; font-size: 9.5pt; }
        .ttd-box { width: 220px; text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <x-print-kop :school="$school" />

    <div class="title" style="margin-top: 14px;">
        RENCANA PELAKSANAAN LAYANAN (RPL) BIMBINGAN DAN KONSELING<br>
        <span style="font-weight: normal; font-size: 9.5pt;">Format 1 Lembar Kurikulum Merdeka - No. Reg: {{ $rpl->rpl_code }}</span>
    </div>

    <table class="meta-table">
        <tr>
            <td width="20%"><strong>Komponen Layanan</strong></td>
            <td width="2%">:</td>
            <td width="38%">{{ str_replace('_', ' ', $rpl->service_component) }}</td>
            <td width="18%"><strong>Sasaran Layanan</strong></td>
            <td width="2%">:</td>
            <td width="20%">{{ $rpl->grade_target }}</td>
        </tr>
        <tr>
            <td><strong>Bidang Layanan</strong></td>
            <td>:</td>
            <td>Bidang {{ $rpl->field_of_service }}</td>
            <td><strong>Semester / Waktu</strong></td>
            <td>:</td>
            <td>{{ $rpl->semester }} / {{ $rpl->time_allocation }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Layanan</strong></td>
            <td>:</td>
            <td>Bimbingan {{ $rpl->service_type }}</td>
            <td><strong>Profil Pelajar</strong></td>
            <td>:</td>
            <td>{{ $rpl->profile_pelajar_pancasila ?? 'Mandiri & Bernalar Kritis' }}</td>
        </tr>
        <tr>
            <td><strong>Topik / Tema</strong></td>
            <td>:</td>
            <td colspan="4"><strong>{{ $rpl->topic }}</strong></td>
        </tr>
    </table>

    <div class="section-title">A. TUJUAN LAYANAN (LEARNING OBJECTIVES)</div>
    <div class="content-box">
        {!! nl2br(e($rpl->learning_objectives)) !!}
    </div>

    <div class="section-title">B. KEGIATAN LAYANAN</div>
    <div class="content-box">
        <p><strong>1. Materi & Metode:</strong> {{ $rpl->materials_summary ?? '-' }} (Metode: {{ $rpl->method_and_media ?? '-' }})</p>
        <p><strong>2. Langkah Kegiatan:</strong></p>
        <div style="margin-left: 12px;">
            <p><strong>a. Tahap Awal (Pendahuluan):</strong> {{ $rpl->step_introduction ?? '-' }}</p>
            <p><strong>b. Tahap Inti:</strong> {{ $rpl->step_core ?? '-' }}</p>
            <p><strong>c. Tahap Penutup:</strong> {{ $rpl->step_closing ?? '-' }}</p>
        </div>
    </div>

    <div class="section-title">C. EVALUASI LAYANAN</div>
    <div class="content-box">
        <p><strong>1. Evaluasi Proses:</strong> {{ $rpl->evaluation_process ?? '-' }}</p>
        <p><strong>2. Evaluasi Hasil:</strong> {{ $rpl->evaluation_result ?? '-' }}</p>
    </div>

    <div class="ttd-container" style="display: flex; justify-content: space-between;">
        <div class="ttd-box">
            <p>Mengetahui,<br>Kepala Sekolah,</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? 'Drs. H. Pimpinan, M.Pd.' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? '19700101 199501 1 001' }}</p>
        </div>
        <div class="ttd-box">
            <p>Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Guru Bimbingan Konseling,</p>
            <br><br><br>
            <p><strong><u>{{ $rpl->counselor?->name ?? 'Guru BK' }}</u></strong><br>
            NIP. {{ $rpl->counselor?->nip ?? '-' }}</p>
        </div>
    </div>
</body>
</html>


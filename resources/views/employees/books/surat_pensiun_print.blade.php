<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar Usul Pensiun BUP - {{ $employee->formatted_name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 30px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9.5pt; font-style: italic; }
        .row { display: flex; margin-bottom: 5px; }
        .label { width: 220px; }
        .colon { width: 20px; text-align: center; }
        .value { flex: 1; font-weight: bold; }
        .box { border: 1px solid #000; padding: 12px 15px; margin: 15px 0; background-color: #fafafa; border-radius: 4px; }
        .signatures { margin-top: 35px; width: 100%; display: flex; justify-content: flex-end; page-break-inside: avoid; }
        .sig-block { width: 45%; text-align: center; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Surat Usul Pensiun: ' . $employee->formatted_name" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 10.5pt;">
            <div>
                <div>Nomor : <strong>800 / {{ str_pad((string)$employee->id, 3, '0', STR_PAD_LEFT) }} / KPG.11 - {{ $school->name ?? 'SMAN 1 Lengkong' }} / {{ \App\Services\LetterNumberService::toRoman((int)date('m')) }} / {{ date('Y') }}</strong></div>
            <div>Lampiran : 1 (satu) Berkas</div>
            <div>Perihal : <strong>Usul Pemberhentian Dengan Hormat Karena Mencapai Batas Usia Pensiun (BUP)</strong></div>
        </div>
        <div>
            <div>Lengkong, {{ now()->translatedFormat('d F Y') }}</div>
            <div style="margin-top: 10px;">
                Kepada Yth.<br>
                Kepala Cabang Dinas Pendidikan Wilayah IX<br>
                Provinsi Jawa Barat<br>
                di Tempat
            </div>
        </div>
    </div>

    <p style="text-align: justify;">
        Dengan hormat,<br>
        Bersama ini kami sampaikan usulan pemberhentian dengan hormat sebagai Pegawai Negeri Sipil karena mencapai Batas Usia Pensiun (BUP) atas nama pegawai yang tersebut di bawah ini:
    </p>

    <div class="box">
        <div class="row">
            <div class="label">Nama Lengkap Pegawai</div>
            <div class="colon">:</div>
            <div class="value">{{ $employee->formatted_name }}</div>
        </div>
        <div class="row">
            <div class="label">NIP</div>
            <div class="colon">:</div>
            <div class="value">{{ $employee->nip ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="label">Pangkat / Golongan Ruang</div>
            <div class="colon">:</div>
            <div class="value">{{ $employee->rank_group ?? '-' }} (TMT: {{ $employee->rank_tmt ? $employee->rank_tmt->format('d/m/Y') : '-' }})</div>
        </div>
        <div class="row">
            <div class="label">Jabatan Terakhir</div>
            <div class="colon">:</div>
            <div class="value">{{ $employee->position ?? $employee->employment_type }}</div>
        </div>
        <div class="row">
            <div class="label">Tempat, Tanggal Lahir</div>
            <div class="colon">:</div>
            <div class="value">{{ $employee->birth_place ?? 'Lengkong' }}, {{ $employee->birth_date ? $employee->birth_date->translatedFormat('d F Y') : '-' }}</div>
        </div>
        <div class="row">
            <div class="label">Batas Usia Pensiun (BUP)</div>
            <div class="colon">:</div>
            <div class="value">{{ $employee->retirement_age }} Tahun</div>
        </div>
        <div class="row">
            <div class="label">TMT Pensiun Terhitung Mulai</div>
            <div class="colon">:</div>
            <div class="value" style="color: #991b1b;">
                {{ $employee->calculated_pension_tmt ? $employee->calculated_pension_tmt->translatedFormat('d F Y') : '-' }}
            </div>
        </div>
        <div class="row">
            <div class="label">Unit Kerja / Sekolah</div>
            <div class="colon">:</div>
            <div class="value">{{ $school->name }}</div>
        </div>
    </div>

    <p style="text-align: justify;">
        Sebagai bahan pertimbangan dan kelengkapan administrasi pensiun, kami lampirkan dokumen persyaratan berkas pensiun yang bersangkutan sesuai ketentuan Badan Kepegawaian Negara (BKN) dan Badan Kepegawaian Daerah (BKD) Provinsi Jawa Barat.
    </p>

    <p style="text-align: justify;">
        Demikian surat usulan ini kami sampaikan, atas perhatian dan kerja sama yang baik kami ucapkan terima kasih.
    </p>

    <div class="signatures">
        <div class="sig-block">
            Kepala Sekolah,<br><br><br><br><br>
            <strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}
        </div>
    </div>
    </div>
</body>
</html>

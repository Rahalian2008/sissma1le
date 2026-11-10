<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Pindah Sekolah - {{ $mutation->student?->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 30px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9.5pt; font-style: italic; }
        .title { text-align: center; margin-bottom: 25px; }
        .title h4 { margin: 0; font-size: 12.5pt; text-transform: uppercase; font-weight: bold; text-decoration: underline; }
        .title p { margin: 3px 0 0 0; font-size: 10.5pt; }
        .row { display: flex; margin-bottom: 6px; }
        .label { width: 230px; }
        .colon { width: 20px; text-align: center; }
        .value { flex: 1; font-weight: bold; }
        .signatures { margin-top: 35px; width: 100%; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sig-block { width: 45%; text-align: center; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
</head>
<body>
    <x-print-toolbar :title="'Surat Keterangan Pindah: ' . ($mutation->student?->name ?? '')" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

        <div class="title">
            <h4>SURAT KETERANGAN PINDAH SEKOLAH</h4>
            <p>Nomor: <strong>{{ $mutation->document_number ?: ('421.3 / ' . str_pad((string)$mutation->id, 3, '0', STR_PAD_LEFT) . ' / PK.11.01.01 - ' . ($school->name ?? 'SMAN 1 Lengkong') . ' / ' . \App\Services\LetterNumberService::toRoman((int)date('m')) . ' / ' . date('Y')) }}</strong></p>
        </div>

    <p>Yang bertanda tangan di bawah ini Kepala <strong>{{ $school->name }}</strong>, Kabupaten Sukabumi, Provinsi Jawa Barat, menerangkan bahwa:</p>

    <div style="margin-left: 20px; margin-bottom: 15px;">
        <div class="row">
            <div class="label">1. Nama Peserta Didik</div>
            <div class="colon">:</div>
            <div class="value">{{ $mutation->student?->name }}</div>
        </div>
        <div class="row">
            <div class="label">2. Nomor Induk Siswa (NIS)</div>
            <div class="colon">:</div>
            <div class="value">{{ $mutation->student?->nis }}</div>
        </div>
        <div class="row">
            <div class="label">3. NISN</div>
            <div class="colon">:</div>
            <div class="value">{{ $mutation->student?->nisn ?: '-' }}</div>
        </div>
        <div class="row">
            <div class="label">4. Tempat, Tanggal Lahir</div>
            <div class="colon">:</div>
            <div class="value" style="font-weight: normal;">{{ $mutation->student?->birth_place ?: '-' }}, {{ $mutation->student?->birth_date ? $mutation->student?->birth_date->format('d F Y') : '-' }}</div>
        </div>
        <div class="row">
            <div class="label">5. Jenis Kelamin</div>
            <div class="colon">:</div>
            <div class="value" style="font-weight: normal;">{{ $mutation->student?->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
        </div>
        <div class="row">
            <div class="label">6. Tingkat / Rombel Terakhir</div>
            <div class="colon">:</div>
            <div class="value">{{ $mutation->student?->current_class_level }} ({{ $mutation->student?->current_rombel ?: '-' }})</div>
        </div>
        <div class="row">
            <div class="label">7. Nama Orang Tua / Wali</div>
            <div class="colon">:</div>
            <div class="value" style="font-weight: normal;">{{ $mutation->student?->family?->father_name ?? $mutation->student?->family?->mother_name ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="label">8. Alamat Tempat Tinggal</div>
            <div class="colon">:</div>
            <div class="value" style="font-weight: normal;">{{ $mutation->student?->address ?: '-' }}</div>
        </div>
    </div>

    <p>Sesuai dengan surat permohonan pindah sekolah dari orang tua/wali peserta didik yang bersangkutan, terhitung sejak tanggal <strong>{{ $mutation->mutation_date ? $mutation->mutation_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong> dinyatakan telah pindah dari {{ $school->name }} ke:</p>

    <div style="margin-left: 20px; margin-bottom: 15px;">
        <div class="row">
            <div class="label">Sekolah Tujuan</div>
            <div class="colon">:</div>
            <div class="value" style="color: #1e3a8a;">{{ $mutation->school_target_or_origin ?: '..........................................................' }}</div>
        </div>
        <div class="row">
            <div class="label">Alasan Kepindahan</div>
            <div class="colon">:</div>
            <div class="value" style="font-weight: normal;">{{ $mutation->reason ?: 'Mengikuti domisili kepindahan orang tua/wali' }}</div>
        </div>
    </div>

    <p>Bersama surat keterangan ini, disertakan dokumen siswa yang bersangkutan berupa:</p>
    <ol style="margin-top: -5px; margin-bottom: 20px; font-size: 10.5pt;">
        <li>Buku Laporan Penilaian Hasil Belajar (Rapor Asli) peserta didik.</li>
        <li>Surat Permohonan Pindah Sekolah dari Orang Tua/Wali Siswa.</li>
        <li>Surat Keterangan Berkelakuan Baik dari Sekolah Asal.</li>
        <li>Formulir Validasi Mutasi Dapodik Kemendikbudristek.</li>
    </ol>

    <p>Demikian surat keterangan pindah sekolah ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

    <div class="signatures">
        <div class="sig-block" style="text-align: left; padding-left: 20px;">
            <div style="display: inline-block; text-align: center;">
                Verifikasi Dokumen Digital:<br>
                <div style="margin-top: 10px;">
                    {!! $qrCodeSvg !!}
                </div>
                <span style="font-size: 8pt; color: #555;">Scan QR Code untuk verifikasi keaslian surat</span>
            </div>
        </div>
        <div class="sig-block">
            Lengkong, {{ $mutation->mutation_date ? $mutation->mutation_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
            Kepala Sekolah,<br><br><br><br>
            <strong><u>{{ $school->headmaster_name ?? '....................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? '....................................' }}
        </div>
    </div>
    </div>
</body>
</html>

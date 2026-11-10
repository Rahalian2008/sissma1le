<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Rekonsiliasi Kas - {{ $month }}/{{ $year }}</title>
    <style>
        @page { size: portrait; margin: 15mm 15mm 15mm 15mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; }
        .header h3 { margin: 0; font-size: 11.5pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 12.5pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .doc-title { text-align: center; margin: 12px 0 16px 0; }
        .doc-title h4 { margin: 0; font-size: 12.5pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; }
        .doc-title p { margin: 3px 0; font-size: 10pt; font-family: monospace; }
        .statement { text-align: justify; margin-bottom: 12px; font-size: 10.5pt; line-height: 1.5; }
        
        table.meta-table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        table.meta-table td { padding: 4px 6px; font-size: 10.5pt; vertical-align: top; }
        
        .signatures { margin-top: 35px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 280px; text-align: center; font-size: 10.5pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Berita Acara Rekonsiliasi Kas: ' . $month . '/' . $year" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <x-print-kop :school="$school" />

        <div class="doc-title">
            <h4>BERITA ACARA PEMERIKSAAN KAS & REGISTER PENUTUPAN BUKU KAS UMUM</h4>
            <p>Nomor: <strong>900 / {{ str_pad((string)$month, 3, '0', STR_PAD_LEFT) }} / KU.03.01 - {{ $school->name ?? 'SMAN 1 Lengkong' }} / {{ \App\Services\LetterNumberService::toRoman((int)$month) }} / {{ $year }}</strong></p>
        </div>

    <div class="statement">
        Pada hari ini, tanggal terakhir bulan <strong>{{ DateTime::createFromFormat('!m', (int)$month)->format('F') }} Tahun {{ $year }}</strong>, bertempat di {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}, kami yang bertanda tangan di bawah ini Kepala Sekolah selaku Kuasa Pengguna Anggaran (KPA) telah melakukan pemeriksaan setempat dan penutupan kas terhadap pembukuan yang diselenggarakan oleh Bendahara Pengeluaran Sekolah.
    </div>

    <div class="statement">
        Berdasarkan hasil pemeriksaan pembukuan dan kas fisik pada tanggal penutupan kas tersebut, diperoleh hasil sebagai berikut:
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 250px;"><strong>I. Saldo Menurut Buku Kas Umum (BKU)</strong></td>
            <td style="width: 15px;">:</td>
            <td style="font-weight: bold; font-family: monospace; font-size: 11pt;">Rp {{ number_format($bkuBalance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td style="font-style: italic; font-size: 9.5pt; color: #333;">(Terbilang: {{ $terbilangBku }})</td>
        </tr>
        <tr><td colspan="3" style="height: 8px;"></td></tr>
        <tr>
            <td colspan="3"><strong>II. Saldo Kas Nyata / Fisik Terdiri Dari:</strong></td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">1. Uang Kas Tunai di Brankas</td>
            <td>:</td>
            <td style="font-family: monospace; font-weight: bold;">Rp {{ number_format($cashBalance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td style="font-style: italic; font-size: 9.5pt; color: #333;">(Terbilang: {{ $terbilangCash }})</td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">2. Saldo Simpanan di Rekening Bank</td>
            <td>:</td>
            <td style="font-family: monospace; font-weight: bold;">Rp {{ number_format($bankBalance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td style="font-style: italic; font-size: 9.5pt; color: #333;">(Terbilang: {{ $terbilangBank }})</td>
        </tr>
        <tr><td colspan="3" style="height: 8px;"></td></tr>
        <tr style="border-top: 1px dashed #666;">
            <td><strong>Jumlah Kas Nyata (Tunai + Bank)</strong></td>
            <td>:</td>
            <td style="font-weight: bold; font-family: monospace; font-size: 11pt;">Rp {{ number_format($cashBalance + $bankBalance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>III. Selisih Kas (I - II)</strong></td>
            <td>:</td>
            <td style="font-weight: bold; font-family: monospace; color: {{ ($bkuBalance - ($cashBalance + $bankBalance)) === 0.0 ? '#006600' : '#990000' }};">
                Rp {{ number_format($bkuBalance - ($cashBalance + $bankBalance), 0, ',', '.') }} (NIHIL / SEIMBANG)
            </td>
        </tr>
    </table>

    <div class="statement">
        <strong>KESIMPULAN:</strong><br>
        Setelah dilakukan verifikasi silang antara bukti transaksi, Buku Pembantu Kas Tunai, rekening koran bank, dan Buku Kas Umum (BKU), keadaan pembukuan dinyatakan <strong>LENGKAP, TERTIB, DAN SAH</strong> tanpa adanya selisih perhitungan (nihil).
    </div>

    <div class="statement">
        Demikian Berita Acara Pemeriksaan Kas dan Register Penutupan BKU ini dibuat dalam rangkap secukupnya untuk dipergunakan sebagai bahan pertanggungjawaban keuangan satuan pendidikan.
    </div>

    <div class="signatures">
        <div class="sig-box float-left">
            <p>Yang Diperiksa,<br><strong>Bendahara Pengeluaran Sekolah</strong></p>
            <br><br><br>
            <p><strong><u>{{ Auth::user()->name ?? 'Bendahara Sekolah' }}</u></strong><br>
            NIP. ................................................</p>
        </div>

        <div class="sig-box float-right">
            <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ now()->translatedFormat('d F Y') }}<br>Yang Memeriksa,<br><strong>Kepala Sekolah selaku KPA</strong></p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>
    </div>
</body>
</html>

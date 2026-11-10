<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penghapusan Aset - {{ $disposal->disposal_number }}</title>
    <style>
        @page { size: portrait; margin: 15mm 15mm 15mm 15mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; }
        .header h3 { margin: 0; font-size: 11.5pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 12.5pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .doc-title { text-align: center; margin: 12px 0 16px 0; }
        .doc-title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; }
        .doc-title p { margin: 3px 0; font-size: 10pt; font-family: monospace; }
        .statement { text-align: justify; margin-bottom: 12px; font-size: 11pt; line-height: 1.5; }
        table.data-table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 5px 7px; font-size: 10pt; vertical-align: top; }
        table.data-table th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signatures { margin-top: 30px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 280px; text-align: center; font-size: 10.5pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }
        @media print {
            .no-print { display: none; }
        }
</head>
<body>
    <x-print-toolbar :title="'Berita Acara Penghapusan Aset: ' . $disposal->disposal_number" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <x-print-kop :school="$school" />

        <div class="doc-title">
            <h4>BERITA ACARA USULAN PENGHAPUSAN BARANG MILIK DAERAH (BMD)</h4>
            <p>Nomor: <strong>{{ $disposal->disposal_number ?: ('000 / ' . str_pad((string)$disposal->id, 3, '0', STR_PAD_LEFT) . ' / PL.06 - ' . ($school->name ?? 'SMAN 1 Lengkong') . ' / ' . \App\Services\LetterNumberService::toRoman((int)$disposal->disposal_date->format('m')) . ' / ' . $disposal->disposal_date->format('Y')) }}</strong></p>
        </div>

    <div class="statement">
        Pada hari ini, <strong>{{ $disposal->disposal_date->translatedFormat('l') }}</strong> tanggal <strong>{{ $disposal->disposal_date->translatedFormat('d F Y') }}</strong>, bertempat di {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}, kami yang bertanda tangan di bawah ini Tim Peneliti/Pemeriksa Barang Inventaris Sekolah telah melakukan penelitian dan pemeriksaan fisik terhadap Barang Milik Daerah (BMD) yang diusulkan untuk dihapuskan:
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th>Nama / Jenis Barang</th>
                <th style="width: 140px;">Kode Registrasi & KIB</th>
                <th style="width: 60px;">Tahun</th>
                <th style="width: 100px;">Nilai Perolehan</th>
                <th style="width: 90px;">Kondisi Fisik</th>
                <th style="width: 100px;">Taksiran Residu</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td>
                    <strong>{{ $disposal->asset->name }}</strong><br>
                    <span style="font-size: 9pt; color: #333;">Merk/Tipe: {{ $disposal->asset->brand_model ?? '-' }}</span><br>
                    <span style="font-size: 9pt; color: #333;">No. Seri: {{ $disposal->asset->serial_number ?? '-' }}</span>
                </td>
                <td>
                    <strong>{{ $disposal->asset->code }}</strong><br>
                    <span style="font-size: 9pt;">KIB {{ $disposal->asset->kib_type }}</span><br>
                    @if($disposal->asset->sim_bmd_code)
                    <span style="font-size: 8.5pt; font-family: monospace;">SIPD e-BMD: {{ $disposal->asset->sim_bmd_code }}</span>
                    @endif
                </td>
                <td class="text-center">{{ $disposal->asset->procurement_year }}</td>
                <td class="text-right">Rp {{ number_format($disposal->asset->price, 0, ',', '.') }}</td>
                <td class="text-center" style="font-weight: bold; color: #900;">{{ $disposal->reason }}</td>
                <td class="text-right">Rp {{ number_format($disposal->estimated_scrap_value, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="statement">
        <strong>A. KRONOLOGI / ALASAN TEKNIS KERUSAKAN:</strong><br>
        {{ $disposal->chronology_description }}
    </div>

    <div class="statement">
        <strong>B. KESIMPULAN DAN REKOMENDASI PEMERIKSAAN:</strong><br>
        Berdasarkan hasil pemeriksaan fisik, barang tersebut dinyatakan <strong>{{ strtoupper($disposal->reason) }}</strong> dan secara teknis serta ekonomis sudah tidak layak dipergunakan lagi dalam mendukung kegiatan belajar mengajar / kedinasan satuan pendidikan. Dengan ini diusulkan kepada Kepala Dinas Pendidikan Provinsi Jawa Barat / BPKAD untuk dilakukan proses <strong>PENGHAPUSAN / AFKIR DARI BUKU INVENTARIS BARANG MILIK DAERAH (BMD)</strong>.
    </div>

    <div class="statement">
        Demikian Berita Acara ini dibuat dalam rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
    </div>

    <div class="signatures">
        <div class="sig-box float-left">
            <p>Mengetahui,<br>Kepala Sekolah,</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
        </div>
        <div class="sig-box float-right">
            <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ $disposal->disposal_date->translatedFormat('d F Y') }}<br>Pengurus Barang / Pengusul,</p>
            <br><br><br>
            <p><strong><u>{{ $disposal->proposed_by_name }}</u></strong><br>
            Tim Pemeriksa: {{ $disposal->inspector_names ?? 'Pengurus Barang' }}</p>
        </div>
        <div class="clear"></div>
    </div>
    </div>
</body>
</html>

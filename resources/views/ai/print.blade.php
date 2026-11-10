<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kajian AI - {{ $aiReport->module }} - SIKATAS SMA1LE</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .paper-f4 .section-content { line-height: 1.5; font-size: 10.5pt; }
        .paper-a4 .section-content { line-height: 1.35; font-size: 10pt; }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 15px;
        }
        .header-kop h1 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-kop h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .header-kop p {
            font-size: 9pt;
            margin: 1px 0;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-doc h3 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .meta-info {
            font-size: 9.5pt;
            margin-bottom: 15px;
            border: 1px solid #999;
            padding: 6px 10px;
            background: #fafafa;
        }
        .section-box {
            margin-bottom: 12px;
        }
        .section-header {
            font-weight: bold;
            font-size: 10.5pt;
            background: #eef2ff;
            border-left: 4px solid #3730a3;
            padding: 3px 8px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .section-content {
            font-size: 10pt;
            text-align: justify;
            padding-left: 12px;
            white-space: pre-line;
        }
        table.metrics-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 5px;
        }
        table.metrics-table th, table.metrics-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        table.metrics-table th {
            background-color: #f1f5f9;
            text-align: left;
            font-weight: bold;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <x-print-toolbar orientation="portrait" default-size="f4" title="Kajian Analisis Eksekutif AI (#AI-{{ str_pad($aiReport->id, 4, '0', STR_PAD_LEFT) }})" />

    <div style="padding: 10px;">
        <x-print-kop :school="$school" />

        <div class="title-doc">
            <h3>LAPORAN KAJIAN ANALISIS EKSEKUTIF KETATAUSAHAAN</h3>
            <p style="font-size: 9.5pt; margin: 3px 0 0 0;">Nomor Registrasi Sistem: <strong>AI/{{ $aiReport->created_at->format('Y') }}/{{ str_pad($aiReport->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
        </div>

        <div class="meta-info">
            <table style="width: 100%; font-size: 9pt;">
                <tr>
                    <td style="width: 15%; font-weight: bold;">Modul Analisis</td>
                    <td style="width: 35%;">: {{ ucwords(str_replace('_', ' ', $aiReport->module)) }}</td>
                    <td style="width: 18%; font-weight: bold;">Tanggal Diterbitkan</td>
                    <td style="width: 32%;">: {{ $aiReport->created_at->translatedFormat('d F Y H:i') }} WIB</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Periode Data</td>
                    <td>: {{ $aiReport->period }}</td>
                    <td style="font-weight: bold;">Integritas Data</td>
                    <td>: Real-Time Live Ground-Truth Database</td>
                </tr>
            </table>
        </div>

        <!-- 1. Ringkasan Eksekutif -->
        <div class="section-box">
            <div class="section-header">1. Ringkasan Eksekutif (Executive Summary)</div>
            <div class="section-content">{{ $aiReport->executive_summary }}</div>
        </div>

        <!-- 2. Temuan Kunci -->
        <div class="section-box">
            <div class="section-header">2. Temuan Kunci (Key Findings)</div>
            <div class="section-content">{{ $aiReport->key_findings }}</div>
        </div>

        <!-- 3. Kesimpulan -->
        <div class="section-box">
            <div class="section-header">3. Kesimpulan Manajerial (Conclusions)</div>
            <div class="section-content">{{ $aiReport->conclusions }}</div>
        </div>

        <!-- 4. Permasalahan & Titik Kritis -->
        <div class="section-box">
            <div class="section-header">4. Identifikasi Permasalahan Kritis (Critical Issues Identified)</div>
            <div class="section-content">{{ $aiReport->issues_identified }}</div>
        </div>

        <!-- 5. Rekomendasi Solutif -->
        <div class="section-box">
            <div class="section-header">5. Rekomendasi Strategis (Strategic Recommendations)</div>
            <div class="section-content">{{ $aiReport->recommendations }}</div>
        </div>

        <!-- 6. Rencana Tindak Lanjut -->
        <div class="section-box">
            <div class="section-header">6. Rencana Tindak Lanjut (Action Plan)</div>
            <div class="section-content">{{ $aiReport->action_plan }}</div>
        </div>

        <!-- 7. Data Metrik Pendukung -->
        @php
            $supporting = is_array($aiReport->supporting_data) ? $aiReport->supporting_data : (is_string($aiReport->supporting_data) ? json_decode($aiReport->supporting_data, true) : []);
        @endphp
        @if(!empty($supporting))
        <div class="section-box">
            <div class="section-header">7. Data Metrik Faktis Pendukung (Ground-Truth Database Metrics)</div>
            <table class="metrics-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Indikator / Metrik Faktual</th>
                        <th style="width: 50%;">Nilai Tercatat Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supporting as $k => $v)
                        <tr>
                            <td>{{ ucwords(str_replace('_', ' ', $k)) }}</td>
                            <td style="font-weight: bold;">
                                @if(is_numeric($v))
                                    @if(str_contains($k, 'amount') || str_contains($k, 'income') || str_contains($k, 'expense') || str_contains($k, 'balance'))
                                        Rp {{ number_format((float)$v, 0, ',', '.') }}
                                    @elseif(str_contains($k, 'pct'))
                                        {{ $v }}%
                                    @else
                                        {{ number_format((float)$v, 0, ',', '.') }}
                                    @endif
                                @else
                                    {{ $v }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @php
            $headOfTu = \App\Models\Employee::where('position', 'like', '%Kepala Tata Usaha%')->first();
        @endphp
        <table class="signatures">
            <tr>
                <td style="width: 50%;">
                    Mengetahui / Mengesahkan,<br>
                    Kepala Sekolah<br><br><br><br>
                    <strong><u>{{ $school->headmaster_name }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip }}
                </td>
                <td style="width: 50%;">
                    {{ $school->city ?? 'Kota' }}, {{ $aiReport->created_at->translatedFormat('d F Y') }}<br>
                    Kepala Tata Usaha<br><br><br><br>
                    <strong><u>{{ $headOfTu?->formatted_name ?? 'Kepala Tata Usaha' }}</u></strong><br>
                    NIP. {{ $headOfTu?->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

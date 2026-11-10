<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Induk Inventaris (BII) SIPD e-BMD - {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 8.5pt; color: #000; margin: 0; padding: 0; }
        .instansi-header { text-align: left; margin-bottom: 6px; font-size: 8pt; text-transform: uppercase; line-height: 1.3; }
        .doc-title { text-align: center; margin: 6px 0 10px 0; }
        .doc-title h4 { margin: 0; font-size: 11.5pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        .doc-title p { margin: 2px 0; font-size: 8pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #000; padding: 3.5px 5px; font-size: 7.5pt; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; font-size: 7.5pt; }
        .signatures { margin-top: 20px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 320px; text-align: center; font-size: 8.5pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }

        /* Rekap box */
        .rekap-container { margin: 8px 0 12px 0; page-break-inside: avoid; }
        .rekap-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
        .rekap-table th { background-color: #e2e8f0; font-size: 7.5pt; }
        .rekap-table td { font-size: 7.5pt; }

        /* Badge Kondisi */
        .badge-cond { display: inline-block; padding: 1px 4px; font-size: 7pt; font-weight: bold; border-radius: 3px; }
        .cond-baik { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .cond-ringan { background: #e0f2fe; color: #075985; border: 1px solid #7dd3fc; }
        .cond-sedang { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .cond-berat { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .cond-hilang { background: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe; }

        @media print {
            .no-print { display: none !important; }
            body { font-size: 8pt; }
            .printable-sheet { padding: 0 !important; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Induk Inventaris (BII)'" orientation="landscape" defaultPaper="f4" />

    <!-- Action Bar / Opsi Sortir & Filter (Hanya tampil di layar pratinjau / no-print) -->
    <div class="no-print" style="max-width: 1100px; margin: 10px auto; padding: 10px 16px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span style="font-weight: 700; color: #0f172a; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Urutan Dokumen (Sort):</span>
            <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['sort_by' => 'default'])) }}"
               style="padding: 5px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; {{ (!request('sort_by') || request('sort_by') === 'default') ? 'background: #0f172a; color: #ffffff;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' }}">
                1. Keseluruhan (Standar KIB)
            </a>
            <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['sort_by' => 'condition'])) }}"
               style="padding: 5px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; {{ request('sort_by') === 'condition' ? 'background: #2563eb; color: #ffffff;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' }}">
                2. Berdasarkan Kondisi Aset (Baik s.d. Hilang)
            </a>
        </div>

        <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
            <span style="font-weight: 700; color: #0f172a; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Kondisi Aset:</span>
            <a href="{{ route('sarpras.bibi.print', array_merge(request()->except('condition'), ['condition' => 'all'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ (!request('condition') || request('condition') === 'all') ? 'background: #334155; color: #ffffff;' : 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;' }}">
                Semua ({{ $totalItemsCount }})
            </a>
            <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Baik'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ request('condition') === 'Baik' ? 'background: #059669; color: #ffffff;' : 'background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;' }}">
                Baik ({{ $conditionSummary['Baik']['count'] }})
            </a>
            <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Rusak Ringan'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ request('condition') === 'Rusak Ringan' ? 'background: #0284c7; color: #ffffff;' : 'background: #f0f9ff; color: #075985; border: 1px solid #bae6fd;' }}">
                Rusak Ringan ({{ $conditionSummary['Rusak Ringan']['count'] }})
            </a>
            <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Rusak Sedang'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ request('condition') === 'Rusak Sedang' ? 'background: #d97706; color: #ffffff;' : 'background: #fffbeb; color: #92400e; border: 1px solid #fde68a;' }}">
                Rusak Sedang ({{ $conditionSummary['Rusak Sedang']['count'] }})
            </a>
            <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Rusak Berat'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ request('condition') === 'Rusak Berat' ? 'background: #e11d48; color: #ffffff;' : 'background: #fff1f2; color: #991b1b; border: 1px solid #fecdd3;' }}">
                Rusak Berat ({{ $conditionSummary['Rusak Berat']['count'] }})
            </a>
            <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Hilang'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ request('condition') === 'Hilang' ? 'background: #9333ea; color: #ffffff;' : 'background: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff;' }}">
                Hilang ({{ $conditionSummary['Hilang']['count'] }})
            </a>
        </div>
    </div>

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <x-print-kop :school="$school" />

        <div class="instansi-header">
            <table style="width: 100%; border: none; font-size: 8pt;">
                <tr style="border: none;">
                    <td style="border: none; width: 60%; vertical-align: top; padding: 0;">
                        <div><strong>PROVINSI</strong> : {{ strtoupper($school->province ?? 'JAWA BARAT') }}</div>
                        <div><strong>SKPD</strong> : DINAS PENDIDIKAN</div>
                        <div><strong>CABANG DINAS</strong> : {{ strtoupper($school->branch_office ?? 'CABANG DINAS PENDIDIKAN WILAYAH V') }}</div>
                        <div><strong>KABUPATEN</strong> : {{ strtoupper($school->city ?? 'KABUPATEN SUKABUMI') }}</div>
                        <div><strong>SATUAN PENDIDIKAN</strong> : {{ strtoupper($school->name ?? 'SMA NEGERI 1 LENGKONG') }}</div>
                    </td>
                    <td style="border: none; width: 40%; vertical-align: top; text-align: right; padding: 0;">
                        <div><strong>SISTEM ASET</strong> : SIPD e-BMD</div>
                        <div><strong>KODE UPB / SEKOLAH</strong> : {{ $school->npsn ?? '20202265' }}</div>
                        <div><strong>TAHUN ANGGARAN</strong> : {{ $selectedYear ?? date('Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="doc-title">
            <h4>BUKU INDUK INVENTARIS (BII) - SIPD e-BMD</h4>
            <p>
                Satuan Pendidikan: {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} | NPSN: {{ $school->npsn ?? '20202265' }}
                @if($selectedYear) | Tahun Perolehan: {{ $selectedYear }} @endif
                @if($selectedKib) | KIB {{ $selectedKib }} @endif
                @if($selectedCondition && $selectedCondition !== 'all') | Filter Kondisi: <strong>{{ $selectedCondition }}</strong> @else | Keseluruhan Kondisi @endif
                @if($sortBy === 'condition' || $sortBy === 'kondisi') | Urutan: <strong>Berdasarkan Kondisi Aset</strong> @else | Urutan: <strong>Standar Keseluruhan (KIB/Reg)</strong> @endif
            </p>
        </div>

        <!-- REKAPITULASI KONDISI ASET INVENTARIS (BII) -->
        <div class="rekap-container">
            <div class="rekap-title">
                Rekapitulasi Kondisi Barang Inventaris (BII):
            </div>
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No.</th>
                        <th style="text-align: left; width: 180px;">Kondisi Aset Inventaris</th>
                        <th style="width: 90px; text-align: center;">Jumlah (Unit)</th>
                        <th style="width: 150px; text-align: right;">Total Nilai Perolehan (Rp)</th>
                        <th style="width: 80px; text-align: center;">Proporsi (%)</th>
                        <th style="text-align: left;">Status / Keterangan Operasional</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td><strong>Baik (B)</strong></td>
                        <td class="text-center font-bold">{{ $conditionSummary['Baik']['count'] }} Unit</td>
                        <td class="text-right font-mono">Rp {{ number_format($conditionSummary['Baik']['value'], 0, ',', '.') }}</td>
                        <td class="text-center font-mono">{{ $conditionSummary['Baik']['percentage'] }}%</td>
                        <td>Layak pakai, siap operasional pembelajaran & administrasi</td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td><strong>Rusak Ringan (RR)</strong></td>
                        <td class="text-center font-bold">{{ $conditionSummary['Rusak Ringan']['count'] }} Unit</td>
                        <td class="text-right font-mono">Rp {{ number_format($conditionSummary['Rusak Ringan']['value'], 0, ',', '.') }}</td>
                        <td class="text-center font-mono">{{ $conditionSummary['Rusak Ringan']['percentage'] }}%</td>
                        <td>Dapat difungsikan / pemeliharaan berkala</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td><strong>Rusak Sedang (RS)</strong></td>
                        <td class="text-center font-bold">{{ $conditionSummary['Rusak Sedang']['count'] }} Unit</td>
                        <td class="text-right font-mono">Rp {{ number_format($conditionSummary['Rusak Sedang']['value'], 0, ',', '.') }}</td>
                        <td class="text-center font-mono">{{ $conditionSummary['Rusak Sedang']['percentage'] }}%</td>
                        <td>Perlu perbaikan atau penggantian suku cadang utama</td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                        <td><strong>Rusak Berat (RB)</strong></td>
                        <td class="text-center font-bold">{{ $conditionSummary['Rusak Berat']['count'] }} Unit</td>
                        <td class="text-right font-mono">Rp {{ number_format($conditionSummary['Rusak Berat']['value'], 0, ',', '.') }}</td>
                        <td class="text-center font-mono">{{ $conditionSummary['Rusak Berat']['percentage'] }}%</td>
                        <td>Tidak dapat digunakan / usulan penghapusan (afkir BMD)</td>
                    </tr>
                    <tr>
                        <td class="text-center">5</td>
                        <td><strong>Hilang (H)</strong></td>
                        <td class="text-center font-bold">{{ $conditionSummary['Hilang']['count'] }} Unit</td>
                        <td class="text-right font-mono">Rp {{ number_format($conditionSummary['Hilang']['value'], 0, ',', '.') }}</td>
                        <td class="text-center font-mono">{{ $conditionSummary['Hilang']['percentage'] }}%</td>
                        <td>Proses penelusuran / Berita Acara Kehilangan / TGR</td>
                    </tr>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td colspan="2" class="text-right" style="padding-right: 8px;">TOTAL REKAPITULASI ASET:</td>
                        <td class="text-center font-mono font-bold">{{ $totalItemsCount }} Unit</td>
                        <td class="text-right font-mono font-bold">Rp {{ number_format($totalItemsValue, 0, ',', '.') }}</td>
                        <td class="text-center font-mono font-bold">100%</td>
                        <td>Total keseluruhan barang inventaris tercatat</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TABEL RINCIAN BUKU INDUK INVENTARIS (BII) -->
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No.</th>
                    <th style="width: 35px;">KIB</th>
                    <th style="width: 135px;">Kodefikasi Barang (SIPD e-BMD)</th>
                    <th style="width: 60px;">No. Reg</th>
                    <th>Nama / Jenis Barang Inventaris</th>
                    <th style="width: 125px;">Merk / Model / Spesifikasi</th>
                    <th style="width: 85px;">Nomor Seri/Pabrik</th>
                    <th style="width: 40px;">Tahun</th>
                    <th style="width: 80px;">Asal Usul</th>
                    <th style="width: 95px;">Harga Perolehan (Rp)</th>
                    <th style="width: 80px;">Kondisi Aset</th>
                    <th style="width: 95px;">Ruangan (KIR)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $runningTotal = 0; 
                    $currentConditionSection = null;
                @endphp
                @forelse($assets as $idx => $asset)
                @php $runningTotal += (float)$asset->total_price; @endphp

                {{-- Subheader pembatas per kondisi jika di-sort berdasarkan kondisi aset --}}
                @if(($sortBy === 'condition' || $sortBy === 'kondisi') && $currentConditionSection !== $asset->condition)
                    @php $currentConditionSection = $asset->condition; @endphp
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td colspan="12" style="padding: 4px 8px; font-size: 8pt; color: #0f172a; text-transform: uppercase;">
                            &bull; KELOMPOK KONDISI ASET: {{ $currentConditionSection }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold">KIB {{ $asset->kib_type }}</td>
                    <td class="font-mono">
                        <strong>{{ $asset->sim_bmd_code ?? $asset->code }}</strong>
                    </td>
                    <td class="text-center font-mono">
                        {{ $asset->register_range ?? $asset->register_number }}
                    </td>
                    <td>
                        <strong>{{ $asset->name }}</strong>
                        @if(($asset->quantity ?? 1) > 1)
                            <div style="font-size: 7pt; color: #2563eb; font-weight: bold;">Jumlah: {{ $asset->quantity }} {{ $asset->unit ?? 'Unit' }}</div>
                        @endif
                    </td>
                    <td>
                        {{ $asset->brand_model ?? '-' }}
                        @if($asset->specification)
                        <div style="font-size: 7pt; color: #555;">{{ Str::limit($asset->specification, 55) }}</div>
                        @endif
                    </td>
                    <td class="font-mono text-center">{{ $asset->serial_number ?? '-' }}</td>
                    <td class="text-center">{{ $asset->procurement_year }}</td>
                    <td>{{ $asset->funding_source }}</td>
                    <td class="text-right font-mono">
                        {{ number_format($asset->total_price ?? $asset->price, 0, ',', '.') }}
                        @if(($asset->quantity ?? 1) > 1)
                            <div style="font-size: 6.5pt; color: #777;">(@ {{ number_format($asset->price, 0, ',', '.') }})</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge-cond 
                            @if($asset->condition === 'Baik') cond-baik
                            @elseif($asset->condition === 'Rusak Ringan') cond-ringan
                            @elseif($asset->condition === 'Rusak Sedang') cond-sedang
                            @elseif($asset->condition === 'Rusak Berat') cond-berat
                            @elseif($asset->condition === 'Hilang') cond-hilang
                            @else cond-sedang @endif">
                            {{ $asset->condition }}
                        </span>
                    </td>
                    <td>{{ $asset->room?->name ?? 'Gudang' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 15px;">Tidak ada data aset inventaris pada kriteria ini.</td>
                </tr>
                @endforelse

                @if($assets->count() > 0)
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="9" class="text-right" style="padding-right: 10px;">JUMLAH TOTAL NILAI PEROLEHAN:</td>
                    <td class="text-right font-mono">Rp {{ number_format($runningTotal, 0, ',', '.') }}</td>
                    <td colspan="2" class="text-center">{{ $assets->sum(fn($a) => $a->quantity ?? 1) }} Unit Terdaftar</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-box float-left">
                <p>Mengetahui,<br>{{ auth()->user()?->getRoleDisplayName() ?? 'Pengurus Barang Milik Daerah (SIPD e-BMD)' }},</p>
                <br><br><br>
                <p><strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Operasional') }}</u></strong><br>
                NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}</p>
            </div>
            <div class="sig-box float-right">
                <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ now()->translatedFormat('d F Y') }}<br>Kepala Sekolah selaku Pengguna Barang,</p>
                <br><br><br>
                <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
                NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>

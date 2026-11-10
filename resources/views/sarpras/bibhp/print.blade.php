<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Induk Barang Habis Pakai (BIBHP) - {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</title>
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
        .signatures { margin-top: 25px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 340px; text-align: center; font-size: 8.5pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }

        /* Badge Status */
        .badge-status { display: inline-block; padding: 1px 4px; font-size: 6.5pt; font-weight: bold; border-radius: 3px; }
        .status-aman { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .status-menipis { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .status-habis { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        @media print {
            .no-print { display: none !important; }
            body { font-size: 8pt; }
            .printable-sheet { padding: 0 !important; }
            @page {
                size: A4 landscape;
                margin: 10mm 8mm 10mm 8mm;
            }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Induk Barang Habis Pakai (BIBHP)'" orientation="landscape" defaultPaper="f4" />

    <!-- Action Bar / Opsi Sortir & Filter (Hanya tampil di layar pratinjau / no-print) -->
    <div class="no-print" style="max-width: 1100px; margin: 10px auto; padding: 10px 16px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span style="font-weight: 700; color: #0f172a; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Urutan Dokumen (Sort):</span>
            <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['sort_by' => 'default'])) }}"
               style="padding: 5px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; {{ (!request('sort_by') || request('sort_by') === 'default') ? 'background: #0f172a; color: #ffffff;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' }}">
                Standar Kategori
            </a>
            <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['sort_by' => 'value_desc'])) }}"
               style="padding: 5px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; {{ request('sort_by') === 'value_desc' ? 'background: #2563eb; color: #ffffff;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' }}">
                Nilai Persediaan Tertinggi
            </a>
            <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['sort_by' => 'name'])) }}"
               style="padding: 5px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; {{ request('sort_by') === 'name' ? 'background: #0d9488; color: #ffffff;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' }}">
                Nama Barang (A-Z)
            </a>
        </div>

        <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
            <span style="font-weight: 700; color: #0f172a; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Filter Status:</span>
            <a href="{{ route('sarpras.bibhp.print', array_merge(request()->except('status'), ['status' => 'all'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ (!request('status') || request('status') === 'all') ? 'background: #334155; color: #ffffff;' : 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;' }}">
                Semua Item
            </a>
            <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['status' => 'menipis'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ request('status') === 'menipis' ? 'background: #d97706; color: #ffffff;' : 'background: #fffbeb; color: #92400e; border: 1px solid #fde68a;' }}">
                Hanya Stok Menipis
            </a>
            <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['status' => 'habis'])) }}"
               style="padding: 4px 8px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600; {{ request('status') === 'habis' ? 'background: #e11d48; color: #ffffff;' : 'background: #fff1f2; color: #991b1b; border: 1px solid #fecdd3;' }}">
                Hanya Stok Habis
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
                        <div><strong>UNIT KERJA</strong> : {{ strtoupper($school->name ?? 'SMA NEGERI 1 LENGKONG') }}</div>
                    </td>
                    <td style="border: none; width: 40%; vertical-align: top; text-align: right; padding: 0;">
                        <div><strong>KODE AKUN</strong> : 1.1.7 (ASET LANCAR - PERSEDIAAN)</div>
                        <div><strong>STANDAR</strong> : PERMENDAGRI NO. 108/2016 & 47/2021</div>
                        <div><strong>TANGGAL CETAK</strong> : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="doc-title">
            <h4>BUKU INDUK BARANG HABIS PAKAI / NON-INVENTARIS</h4>
            <p><strong>REKAPITULASI POSISI STOK DAN NERACA SALDO PERSEDIAAN BAHAN HABIS PAKAI (BHP)</strong></p>
            <p style="font-size: 7.5pt; color: #333;">Klasifikasi Alat Tulis Kantor, Bahan Komputer, Barang Cetakan, Alat Kebersihan, Bahan Praktikum Lab, dan Obat-Obatan UKS</p>
        </div>

        <!-- Tabel Rekapitulasi Data Barang Habis Pakai -->
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25px;">NO</th>
                    <th rowspan="2" style="width: 110px;">KODEFIKASI<br>(PERMENDAGRI 1.1.7)</th>
                    <th rowspan="2">NAMA BARANG HABIS PAKAI & SPESIFIKASI</th>
                    <th rowspan="2" style="width: 85px;">KATEGORI</th>
                    <th rowspan="2" style="width: 45px;">SATUAN</th>
                    <th rowspan="2" style="width: 80px;">LOKASI GUDANG</th>
                    <th colspan="3" style="width: 150px;">MUTASI STOK FISIK</th>
                    <th rowspan="2" style="width: 80px;">HARGA SATUAN<br>(Rp)</th>
                    <th rowspan="2" style="width: 95px;">NILAI PERSEDIAAN<br>(Rp)</th>
                    <th rowspan="2" style="width: 65px;">STATUS</th>
                </tr>
                <tr>
                    <th style="width: 45px;">MASUK</th>
                    <th style="width: 45px;">KELUAR</th>
                    <th style="width: 60px; background-color: #e2e8f0;">SISA AKHIR</th>
                </tr>
                <tr style="background-color: #fafafa; font-size: 6.5pt; color: #555;">
                    <th style="padding: 1px;">1</th>
                    <th style="padding: 1px;">2</th>
                    <th style="padding: 1px;">3</th>
                    <th style="padding: 1px;">4</th>
                    <th style="padding: 1px;">5</th>
                    <th style="padding: 1px;">6</th>
                    <th style="padding: 1px;">7</th>
                    <th style="padding: 1px;">8</th>
                    <th style="padding: 1px;">9 = (7 - 8)</th>
                    <th style="padding: 1px;">10</th>
                    <th style="padding: 1px;">11 = (9 x 10)</th>
                    <th style="padding: 1px;">12</th>
                </tr>
            </thead>
            <tbody>
                @forelse($goods as $index => $good)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono text-center" style="white-space: nowrap; font-weight: bold;">
                        {{ $good->code }}
                    </td>
                    <td>
                        <strong>{{ $good->name }}</strong>
                        @if($good->sim_bmd_code && $good->sim_bmd_code !== $good->code)
                            <span style="font-size: 6.5pt; color: #555; font-family: monospace;"> [SIPD: {{ $good->sim_bmd_code }}]</span>
                        @endif
                    </td>
                    <td>{{ $good->category }}</td>
                    <td class="text-center">{{ $good->unit }}</td>
                    <td>{{ $good->storage_location ?: 'Gudang Utama' }}</td>
                    <td class="text-right font-mono">{{ number_format($good->total_received, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($good->total_issued, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: bold; background-color: #f8fafc;">
                        {{ number_format($good->current_stock, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-mono">
                        {{ number_format($good->latest_unit_price, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-mono" style="font-weight: bold;">
                        {{ number_format($good->inventory_value, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if($good->stock_status === 'Aman')
                            <span class="badge-status status-aman">Aman</span>
                        @elseif($good->stock_status === 'Menipis')
                            <span class="badge-status status-menipis">Menipis</span>
                        @else
                            <span class="badge-status status-habis">Habis</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 20px;">
                        <em>Tidak ada data barang persediaan habis pakai yang ditemukan sesuai kriteria filter.</em>
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="8" class="text-right" style="padding: 6px;">
                        JUMLAH TOTAL PERSEDIAAN BAHAN HABIS PAKAI ({{ $totalItemsCount }} ITEM):
                    </td>
                    <td class="text-right font-mono" style="font-size: 8.5pt;">
                        {{ number_format($grandTotalQty, 0, ',', '.') }}
                    </td>
                    <td></td>
                    <td class="text-right font-mono" style="font-size: 8.5pt; color: #047857;">
                        Rp {{ number_format($grandTotalValue, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Kolom Tanda Tangan Resmi Sesuai Permendagri 47/2021 -->
        <div class="signatures">
            <div class="sig-box float-left">
                <p>Mengetahui / Menyetujui,<br><strong>KEPALA {{ strtoupper($school->name ?? 'SMA NEGERI 1 LENGKONG') }}</strong></p>
                <div style="height: 60px;"></div>
                <p>
                    <strong style="text-decoration: underline;">{{ strtoupper($school->headmaster_name ?? 'NAMA KEPALA SEKOLAH, M.Pd.') }}</strong><br>
                    <span>NIP. {{ $school->headmaster_nip ?? '19700101 199501 1 001' }}</span>
                </p>
            </div>

            <div class="sig-box float-right">
                <p>
                    {{ $school->city ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>{{ strtoupper(auth()->user()?->getRoleDisplayName() ?? 'PENGURUS BARANG / PENGELOLA PERSEDIAAN') }}</strong>
                </p>
                <div style="height: 60px;"></div>
                <p>
                    <strong style="text-decoration: underline;">{{ strtoupper(auth()->user()?->getOfficialName() ?? 'PETUGAS LOGISTIK & PERSEDIAAN') }}</strong><br>
                    <span>NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}</span>
                </p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>

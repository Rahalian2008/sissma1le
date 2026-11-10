@extends('layouts.app')

@section('title', 'Penerimaan Barang Masuk (BM)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Penerimaan Barang Masuk (BM)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemilahan otomatis: Barang Habis Pakai (stok gudang) & Barang Inventaris (otomatis masuk KIB-KIR dengan kode SIPD e-BMD).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('sarpras.assets.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Lihat KIB-KIR Aset
            </a>
            <button onclick="openReceiptModal('habis_pakai')" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Barang Masuk
            </button>
        </div>
    </div>

    <!-- Receipts Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Bukti (BM)</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Klasifikasi & Jenis</th>
                        <th class="px-5 py-3.5">Item Barang & Alokasi</th>
                        <th class="px-5 py-3.5">Supplier / Vendor</th>
                        <th class="px-5 py-3.5">Sumber Dana</th>
                        <th class="px-5 py-3.5 text-right">Total Nilai</th>
                        <th class="px-5 py-3.5">Penerima</th>
                        <th class="px-5 py-3.5 text-right">Aksi & Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($receipts as $rcp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-emerald-700">
                                {{ $rcp->receipt_number }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $rcp->receipt_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $hasInventaris = $rcp->items->contains('item_type', 'inventaris');
                                @endphp
                                @if($hasInventaris)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        Inventaris (KIB-KIR)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        Habis Pakai (Gudang)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @foreach($rcp->items as $item)
                                    <div class="space-y-0.5">
                                        <div class="font-semibold text-slate-900">
                                            {{ $item->good?->name ?? 'Barang' }}
                                            <span class="text-emerald-700 font-mono font-bold">+{{ $item->quantity }} {{ $item->good?->unit ?? 'Unit' }}</span>
                                        </div>
                                        @if($item->item_type === 'inventaris')
                                            <div class="flex items-center gap-1.5 flex-wrap text-[11px] text-slate-500">
                                                <span class="px-1.5 py-0.2 bg-purple-100 text-purple-800 rounded font-semibold">KIB {{ $item->kib_type ?? 'B' }}</span>
                                                <span class="font-mono text-slate-600 font-medium">SIPD e-BMD: {{ $item->sim_bmd_code }}</span>
                                                @if($item->room || $item->asset?->room)
                                                    <span class="text-blue-600 font-medium">📍 KIR: {{ $item->room?->name ?? $item->asset?->room?->name }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">
                                {{ $rcp->supplier_name ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $rcp->funding_source }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($rcp->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $rcp->receiver?->name ?? 'Petugas Gudang' }}
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openEditReceiptModal({{ json_encode($rcp) }})" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-semibold rounded-lg text-[11px] transition inline-flex items-center gap-1 cursor-pointer" title="Edit Penerimaan Barang">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                    @if($rcp->isPaid())
                                        <span class="px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold rounded-lg text-[10px]" title="Sudah dibukukan di BKU">
                                            ✓ Lunas BKU
                                        </span>
                                    @else
                                        <a href="{{ route('finance.transactions.index') }}" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-semibold rounded-lg text-[11px] transition inline-flex items-center gap-1" title="Belum dibukukan ke BKU - Klik untuk buat SPJ Kas">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            SPJ Kas
                                        </a>
                                    @endif
                                    <a href="{{ route('warehouse.receipts.print', $rcp) }}" target="_blank" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-[11px] transition inline-flex items-center gap-1" title="Cetak Berita Acara Serah Terima (BAST)">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak BAST
                                    </a>
                                    <form action="{{ route('warehouse.receipts.archive', $rcp) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg text-[11px] transition inline-flex items-center gap-1 cursor-pointer" title="Simpan BAST ke Kearsipan Digital">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                            Arsip
                                        </button>
                                    </form>
                                    <form action="{{ route('warehouse.receipts.destroy', $rcp) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus transaksi penerimaan barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Penerimaan Barang">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-8 text-center text-slate-400">
                                Belum ada transaksi penerimaan barang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($receipts->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Catat Penerimaan Barang Masuk (Multi-Klasifikasi: Habis Pakai vs Inventaris KIB-KIR) -->
<div id="modal-receipt" class="hidden fixed inset-0 z-[100] bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 md:p-6 overflow-hidden">
    <div class="relative bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col h-[92vh] max-h-[92vh] overflow-hidden">
        <!-- Header (Fixed Top) -->
        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight">Pencatatan Barang Masuk (BM)</h2>
                    <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemilahan fleksibel & cepat: Barang Habis Pakai (ATK) atau Inventaris KIB-KIR (SIM BMD).</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modal-receipt').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('warehouse.receipts.store') }}" method="POST" id="form-receipt" class="flex flex-col flex-1 min-h-0 m-0 overflow-hidden">
            @csrf
            <input type="hidden" name="item_type" id="input-item-type" value="habis_pakai">

            <!-- Body (Scrollable with clean custom scrollbar) -->
            <div class="p-4 sm:p-6 overflow-y-auto flex-1 min-h-0 space-y-4 text-xs overscroll-contain focus:outline-none">
                <!-- Segmented Tab Picker: Habis Pakai vs Inventaris -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-1.5 bg-slate-100 dark:bg-slate-800 rounded-2xl">
                    <button type="button" id="tab-btn-habis-pakai" onclick="switchItemType('habis_pakai')" class="py-2.5 px-3 text-xs sm:text-sm font-bold rounded-xl transition flex items-center justify-center gap-2 bg-white dark:bg-slate-700 text-emerald-700 dark:text-emerald-300 shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span>1. Barang Habis Pakai (ATK)</span>
                    </button>
                    <button type="button" id="tab-btn-inventaris" onclick="switchItemType('inventaris')" class="py-2.5 px-3 text-xs sm:text-sm font-bold rounded-xl transition flex items-center justify-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>2. Barang Inventaris (KIB - KIR)</span>
                    </button>
                </div>

                <!-- Section 1: Formulir Barang Habis Pakai (ATK) -->
                <div id="section-habis-pakai" class="space-y-4">
                    <div class="bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-900/60 p-4 rounded-2xl flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-emerald-950 dark:text-emerald-200 text-xs sm:text-sm">Alur Barang Habis Pakai (BHP / ATK Sekolah)</div>
                            <div class="text-[11px] text-emerald-800 dark:text-emerald-300 leading-relaxed mt-0.5">Barang akan langsung masuk ke kartu persediaan gudang sekolah (Akun 1.1.7) dan menambah stok aktif secara otomatis.</div>
                        </div>
                    </div>

                    <!-- Preset Cepat BHP -->
                    <div class="space-y-2 p-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 rounded-2xl">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                Pilih Cepat Barang Persediaan Populer (1-Klik):
                            </span>
                            <span class="text-[10px] text-emerald-700 bg-emerald-100/70 dark:bg-emerald-900/50 px-2.5 py-0.5 rounded-full font-bold">Otomatis Terisi</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="fillQuickBhp('Kertas HVS A4 75/80 Gsm (Sinar Dunia/PaperOne)', 'BHP-HVS-A4', 'Alat Tulis Kantor (ATK)', 'Rim', 52000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-emerald-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                📄 Kertas HVS A4
                            </button>
                            <button type="button" onclick="fillQuickBhp('Spidol Whiteboard Marker Snowman Hitam/Biru', 'BHP-SPIDOL-WB', 'Alat Tulis Kantor (ATK)', 'Buah', 8500)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-emerald-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                🖊️ Spidol Whiteboard
                            </button>
                            <button type="button" onclick="fillQuickBhp('Toner Printer Laser / Tinta Epson 003 Original', 'BHP-TINTA-PRINT', 'Bahan Komputer & Cetak', 'Botol', 95000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-emerald-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                🖨️ Tinta / Toner Printer
                            </button>
                            <button type="button" onclick="fillQuickBhp('Map Snelhecter & Ordner Folio Arsip Sekolah', 'BHP-MAP-ORD', 'Alat Tulis Kantor (ATK)', 'Buah', 15000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-emerald-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                📁 Map & Ordner
                            </button>
                            <button type="button" onclick="fillQuickBhp('Pulpen / Ballpoint Pen Faster / Standard 1 Pack', 'BHP-PULPEN-PK', 'Alat Tulis Kantor (ATK)', 'Paket', 24000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-emerald-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                ✏️ Pulpen (Pack)
                            </button>
                            <button type="button" onclick="fillQuickBhp('Buku Folio Bergaris Hardcover 100 Lembar', 'BHP-BUKU-FOLIO', 'Alat Tulis Kantor (ATK)', 'Buku', 22000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-emerald-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                📒 Buku Folio
                            </button>
                        </div>
                    </div>

                    <!-- Dual Option: Pilih dari Gudang ATAU Ketik Baru Langsung -->
                    <div class="space-y-3 p-4 bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700/60">
                            <span class="font-bold text-slate-800 dark:text-slate-200">Identitas Barang Persediaan</span>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="toggleBhpNewMode(false)" id="btn-mode-existing-bhp" class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 rounded-lg text-[11px] font-bold transition">
                                    📋 Pilih dari Gudang
                                </button>
                                <button type="button" onclick="toggleBhpNewMode(true)" id="btn-mode-new-bhp" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold hover:bg-emerald-50 transition">
                                    ✍️ + Ketik Item Baru
                                </button>
                            </div>
                        </div>

                        <!-- Opsi 1: Dropdown Barang Gudang -->
                        <div id="container-existing-bhp">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block font-semibold text-slate-700 dark:text-slate-300">Pilih dari Daftar Persediaan Sekolah <span class="text-rose-500">*</span></label>
                                <button type="button" onclick="openReceiptBhpCatalog()" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg text-[11px] font-bold shadow-2xs transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <span>Cari Katalog BHP (Akun 1.1.7)</span>
                                </button>
                            </div>
                            <select name="good_id" id="field-good-id" onchange="onGoodIdChanged(this)" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- Pilih Barang Persediaan Gudang (Atau pilih dari Preset Cepat di atas) --</option>
                                @foreach($goods->where('item_type', '!=', 'inventaris') as $g)
                                    <option value="{{ $g->id }}" data-code="{{ $g->code }}" data-name="{{ $g->name }}">{{ $g->name }} ({{ $g->code }}) — Stok: {{ $g->current_stock }} {{ $g->unit }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Opsi 2: Input Manual Item Baru Langsung -->
                        <div id="container-new-bhp" class="hidden space-y-3">
                            <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-xl text-[11px] text-amber-800 dark:text-amber-300">
                                💡 Barang baru ini otomatis akan didaftarkan ke master barang persediaan gudang tanpa perlu membuka menu terpisah.
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Barang Baru <span class="text-rose-500">*</span></label>
                                    <input type="text" name="new_bhp_name" id="receipt-new-bhp-name" placeholder="Contoh: Penghapus Whiteboard Joyko Magnetik" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Persediaan</label>
                                    <select name="new_bhp_category" id="receipt-new-bhp-category" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-emerald-500">
                                        <option value="Alat Tulis Kantor (ATK)" selected>Alat Tulis Kantor (ATK)</option>
                                        <option value="Bahan Komputer & Cetak">Bahan Komputer & Cetak</option>
                                        <option value="Bahan Praktik & Laboratorium">Bahan Praktik & Laboratorium</option>
                                        <option value="Alat Kebersihan & Rumah Tangga">Alat Kebersihan & Rumah Tangga</option>
                                        <option value="Perlengkapan Sekolah & Kegiatan">Perlengkapan Sekolah & Kegiatan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs untuk kode & unit BHP dari modal katalog -->
                        <input type="hidden" name="new_bhp_code" id="receipt-new-bhp-code">
                        <input type="hidden" name="new_bhp_unit" id="receipt-new-bhp-unit">

                        <div id="receipt-bhp-selected-badge" class="hidden p-2.5 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-emerald-900 dark:text-emerald-200 font-semibold" id="receipt-bhp-selected-text"></span>
                            </div>
                            <button type="button" onclick="clearReceiptBhpSelection()" class="text-emerald-700 dark:text-emerald-300 hover:text-emerald-950 font-bold px-2 py-0.5 rounded hover:bg-emerald-100 dark:hover:bg-emerald-900 transition cursor-pointer">Batal (✕)</button>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Formulir Barang Inventaris (KIB - KIR - SIM BMD) -->
                <div id="section-inventaris" class="hidden space-y-4">
                    <div class="bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-900/60 p-4 rounded-2xl flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-indigo-950 dark:text-indigo-200 text-xs sm:text-sm">Alur Otomatisasi KIB, KIR & SIPD e-BMD Jawa Barat</div>
                            <div class="text-[11px] text-indigo-800 dark:text-indigo-300 leading-relaxed mt-0.5">Barang inventaris otomatis teregistrasi ke KIB A-F, dialokasikan ke KIR, dan diberikan kodefikasi SIPD e-BMD (Permendagri No. 108/2016) beserta QR Code verifikasi.</div>
                        </div>
                    </div>

                    <!-- Preset Cepat Inventaris Sekolah (1-Klik Isi Form) -->
                    <div class="space-y-2 p-3.5 bg-indigo-50/50 dark:bg-slate-800/50 border border-indigo-100 dark:border-slate-700/60 rounded-2xl">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                Pilih Cepat Inventaris Populer (1-Klik):
                            </span>
                            <span class="text-[10px] text-indigo-700 dark:text-indigo-300 bg-indigo-100/80 dark:bg-indigo-900/50 px-2.5 py-0.5 rounded-full font-bold">Otomatis KIB & Kode</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="fillQuickPreset('Laptop Asus ExpertBook Core i5', '1.3.2.10.01.02.002', 'B', 'Unit', 'Asus ExpertBook B1400', 'RAM 16GB, SSD 512GB, Windows 11 Pro', 1, 8500000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-indigo-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                💻 Laptop Asus i5
                            </button>
                            <button type="button" onclick="fillQuickPreset('PC Komputer All-in-One Core i5', '1.3.2.10.01.02.001', 'B', 'Unit', 'Lenovo / HP AIO', 'Core i5, RAM 16GB, SSD 512GB, Layar 23.8 inch', 1, 9500000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-indigo-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                🖥️ PC All-in-One
                            </button>
                            <button type="button" onclick="fillQuickPreset('Meja Kursi Siswa Satu Set', '1.3.2.05.01.01.002', 'B', 'Set', 'Olympic / Informa School', 'Rangka besi hollow, top table kayu lapis HPL', 36, 650000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-indigo-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                🪑 Meja Kursi Siswa (Set)
                            </button>
                            <button type="button" onclick="fillQuickPreset('Proyektor LCD / Smart IFP 65 Inch', '1.3.2.10.02.01.003', 'B', 'Unit', 'Epson / ViewSonic IFP', 'Interactive Flat Panel 4K Touchscreen', 1, 15000000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-indigo-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                📽️ Proyektor / IFP
                            </button>
                            <button type="button" onclick="fillQuickPreset('AC Split Wall 1.5 PK', '1.3.2.05.02.01.004', 'B', 'Unit', 'Daikin / Panasonic Inverter', 'Kapasitas 1.5 PK, hemat listrik', 1, 4500000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-indigo-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                ❄️ AC Split 1.5 PK
                            </button>
                            <button type="button" onclick="fillQuickPreset('Buku Teks Pelajaran Kurikulum Merdeka', '1.3.5.01.01.01.001', 'E', 'Buku', 'Kemendikbudristek', 'Buku Siswa & Guru Kelas X/XI/XII SMA', 100, 75000)" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-medium text-slate-700 dark:text-slate-200 hover:text-indigo-700 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                📚 Buku Paket Merdeka
                            </button>
                        </div>
                    </div>

                    <!-- Card Identitas & Kodefikasi Barang -->
                    <div class="p-4 bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl space-y-3.5">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700/60">
                            <span class="font-bold text-slate-800 dark:text-slate-200">Identitas & Kodefikasi SIPD e-BMD</span>
                            <button type="button" onclick="openSimBmdModal()" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg text-[11px] font-bold shadow-2xs transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span>Cari di Katalog SIPD e-BMD</span>
                            </button>
                        </div>

                        <!-- Dropdown Kodefikasi SIPD e-BMD -->
                        <div>
                            <select id="preset-sim-bmd" onchange="applySimBmdPreset(this.value)" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-medium text-xs focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih dari Katalog Kodefikasi Barang SIPD e-BMD (Atau isi manual di bawah) --</option>
                                @foreach(($sipdBmdCodes ?? $simBmdCodes) as $code => $info)
                                    <option value="{{ $code }}" data-name="{{ $info['name'] }}" data-kib="{{ $info['kib'] }}" data-unit="{{ $info['unit'] }}">
                                        [{{ $code }}] {{ $info['name'] }} (KIB-{{ $info['kib'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Barang Inventaris <span class="text-rose-500">*</span></label>
                                <input type="text" name="good_name" id="field-good-name" placeholder="Contoh: Laptop Asus Core i5 / Meja Siswa" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300">Kodefikasi SIPD e-BMD</label>
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">Otomatis jika kosong</span>
                                </div>
                                <input type="text" name="sim_bmd_code" id="field-sim-bmd-code" placeholder="1.3.2.10.01.02.002 (Otomatis dibuat jika kosong)" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl font-mono text-xs focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Klasifikasi KIB <span class="text-rose-500">*</span></label>
                                <select name="kib_type" id="field-kib-type" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                                    @foreach($kibCategories as $kKey => $kVal)
                                        <option value="{{ $kKey }}" {{ $kKey === 'B' ? 'selected' : '' }}>{{ $kVal['name'] }} - {{ $kVal['title'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300">Penempatan Ruangan (KIR)</label>
                                    <button type="button" onclick="openInlineQuickAddRoom()" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center gap-0.5 hover:underline cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        + Ruang Cepat
                                    </button>
                                </div>
                                <select name="room_id" id="field-room-id" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                                    <option value="" selected>📦 Gudang Transit (Belum Dialokasikan)</option>
                                    <optgroup label="🏛️ Ruangan Sekolah & Laboratorium">
                                        @foreach($rooms as $r)
                                            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->code }}) @if($r->person_in_charge) — PJ: {{ $r->person_in_charge }}@endif</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kondisi Fisik Awal <span class="text-rose-500">*</span></label>
                                <select name="condition" id="field-condition" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                                    <option value="Baik" selected>Baik (Siap Pakai)</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Sedang">Rusak Sedang</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Merek / Model / Tipe</label>
                                <input type="text" name="brand_model" id="field-brand-model" placeholder="Asus ExpertBook B1400 / Olympic" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Seri / Pabrik (Jika ada)</label>
                                <input type="text" name="serial_number" id="field-serial-number" placeholder="SN: 83921938210 (Boleh kosong)" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl font-mono text-xs focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rincian Spesifikasi / Catatan Teknis</label>
                            <input type="text" name="specification" id="field-specification" placeholder="RAM 16GB, SSD 512GB, Windows 11 Pro, Layar 14 inch" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Bagian Bersama: Kuantitas, Biaya, Tanggal & Dokumen Transaksi -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl space-y-3.5">
                    <span class="font-bold text-slate-800 dark:text-slate-200 block pb-1 border-b border-slate-200/80 dark:border-slate-700/60">
                        Kuantitas, Pembiayaan & Dokumen Penerimaan
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Unit (Qty) <span class="text-rose-500">*</span></label>
                            <input type="number" name="quantity" id="field-quantity" value="1" min="1" required oninput="calculateReceiptTotal()" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Satuan (Rp) <span class="text-slate-400 font-normal text-[10px]">(Boleh 0)</span></label>
                            <input type="number" name="unit_price" id="field-unit-price" value="50000" min="0" oninput="calculateReceiptTotal()" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-mono focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan Ukuran</label>
                            <input type="text" name="unit" id="field-unit" list="unit-list-receipt" value="Unit" placeholder="Unit, Buah, Rim, Pcs" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                            <datalist id="unit-list-receipt">
                                @foreach($units as $u)
                                    <option value="{{ $u->name }}">{{ $u->code ?? '' }} - {{ $u->name }}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <!-- Live Total Card -->
                    <div class="p-3.5 bg-gradient-to-r from-slate-100 via-emerald-50 to-emerald-100/60 dark:from-slate-800 dark:via-emerald-950/30 dark:to-emerald-900/30 border border-emerald-200/80 dark:border-emerald-800/60 rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-slate-700 dark:text-slate-300 font-bold text-xs">Total Nilai Transaksi:</span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xs text-slate-500 font-mono" id="receipt-calc-formula">1 × Rp 50.000 =</span>
                            <span class="font-mono font-extrabold text-sm sm:text-lg text-emerald-700 dark:text-emerald-400" id="receipt-live-total">Rp 50.000</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Toko / Supplier / Rekanan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="supplier_name" placeholder="Contoh: CV. Mitra Mandiri Edukasi (Boleh kosong)" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Terima <span class="text-rose-500">*</span></label>
                            <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Sumber Pendanaan <span class="text-rose-500">*</span></label>
                            <select name="funding_source" required class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500">
                                @if(isset($fundingSources) && count($fundingSources) > 0)
                                    @foreach($fundingSources as $fs)
                                        <option value="{{ $fs->name }}" {{ str_contains($fs->name, 'BOS Reguler') ? 'selected' : '' }}>{{ $fs->name }}</option>
                                    @endforeach
                                @else
                                    <option value="BOS Reguler" selected>BOS Reguler</option>
                                    <option value="BOPD Jawa Barat">BOPD Jawa Barat</option>
                                    <option value="BOS Kinerja">BOS Kinerja</option>
                                    <option value="Komite Sekolah">Komite Sekolah</option>
                                    <option value="DAK Fisik">DAK Fisik</option>
                                    <option value="Bantuan Pemerintah / Hibah">Bantuan Pemerintah / Hibah</option>
                                    <option value="Lainnya">Lainnya / Kas Sekolah</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Faktur / Nomor BAST</label>
                            <input type="text" name="notes" placeholder="BAST-2026/04/SMA1LE atau No. Faktur" class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div> <!-- Closes scrollable body container -->

            <!-- Modal Footer (Always Sticky at Bottom) -->
            <div class="flex items-center justify-between p-3.5 sm:p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 shrink-0 z-10 sticky bottom-0">
                <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                    <span class="text-slate-400 font-medium">Total Penerimaan:</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white text-sm sm:text-base" id="footer-live-total">Rp 50.000</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <button type="button" onclick="document.getElementById('modal-receipt').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer transition">Batal</button>
                    <button type="submit" id="submit-btn" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold cursor-pointer shadow-xs transition">Simpan Penerimaan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Mini Modal Quick Add Room Inline -->
<div id="modal-quick-add-room" class="hidden fixed inset-0 z-[120] bg-slate-900/60 backdrop-blur-xs overflow-y-auto p-3 sm:p-4">
    <div class="min-h-full flex items-center justify-center py-2 sm:py-4">
        <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-800 p-5 space-y-3.5">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Ruangan Cepat
            </h3>
            <button type="button" onclick="closeInlineQuickAddRoom()" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
        </div>
        <div class="space-y-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Ruang *</label>
                <input type="text" id="quick-room-code" placeholder="Contoh: R-LAB-MULTIMEDIA" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-xl font-mono uppercase bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Ruangan *</label>
                <input type="text" id="quick-room-name" placeholder="Contoh: Ruang Multimedia & Robotik" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas</label>
                    <input type="number" id="quick-room-capacity" value="36" min="1" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kondisi</label>
                    <select id="quick-room-condition" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                        <option value="Baik" selected>Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                    </select>
                </div>
            </div>
            <div id="quick-room-error" class="hidden text-rose-600 text-[11px] font-semibold"></div>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeInlineQuickAddRoom()" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
            <button type="button" onclick="submitInlineQuickAddRoom()" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold cursor-pointer shadow-xs">Simpan Ruang</button>
        </div>
    </div>
    </div>
</div>

<script>
function toggleBhpNewMode(isNew) {
    const containerExisting = document.getElementById('container-existing-bhp');
    const containerNew = document.getElementById('container-new-bhp');
    const btnExisting = document.getElementById('btn-mode-existing-bhp');
    const btnNew = document.getElementById('btn-mode-new-bhp');
    const fieldGoodId = document.getElementById('field-good-id');
    const inputNewName = document.getElementById('receipt-new-bhp-name');

    if (isNew) {
        containerExisting.classList.add('hidden');
        containerNew.classList.remove('hidden');
        btnNew.className = "px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 rounded-lg text-[11px] font-bold transition";
        btnExisting.className = "px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold hover:bg-emerald-50 transition";
        if (fieldGoodId) fieldGoodId.value = '';
        if (inputNewName) inputNewName.focus();
    } else {
        containerExisting.classList.remove('hidden');
        containerNew.classList.add('hidden');
        btnExisting.className = "px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 rounded-lg text-[11px] font-bold transition";
        btnNew.className = "px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold hover:bg-emerald-50 transition";
    }
}

function openReceiptModal(type) {
    document.getElementById('modal-receipt').classList.remove('hidden');
    switchItemType(type || 'habis_pakai');
    calculateReceiptTotal();
}

function switchItemType(type) {
    const inputType = document.getElementById('input-item-type');
    const secHabis = document.getElementById('section-habis-pakai');
    const secInv = document.getElementById('section-inventaris');
    const btnHabis = document.getElementById('tab-btn-habis-pakai');
    const btnInv = document.getElementById('tab-btn-inventaris');
    const submitBtn = document.getElementById('submit-btn');

    const fieldGoodId = document.getElementById('field-good-id');
    const fieldGoodName = document.getElementById('field-good-name');
    const fieldSimBmd = document.getElementById('field-sim-bmd-code');
    const fieldRoomId = document.getElementById('field-room-id');

    inputType.value = type;

    if (type === 'inventaris') {
        secHabis.classList.add('hidden');
        secInv.classList.remove('hidden');

        btnHabis.className = "py-2.5 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer";
        btnInv.className = "py-2.5 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 bg-white dark:bg-slate-700 text-indigo-700 dark:text-indigo-300 shadow-xs cursor-pointer";

        submitBtn.className = "px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold cursor-pointer shadow-xs";
        submitBtn.innerText = "Simpan & Daftarkan KIB-KIR";

        fieldGoodId.removeAttribute('required');
        fieldGoodName.setAttribute('required', 'required');
        // sim_bmd_code and room_id are intentionally NOT required for flexible filling
        fieldSimBmd.removeAttribute('required');
        fieldRoomId.removeAttribute('required');
    } else {
        secHabis.classList.remove('hidden');
        secInv.classList.add('hidden');

        btnHabis.className = "py-2.5 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 bg-white dark:bg-slate-700 text-emerald-700 dark:text-emerald-300 shadow-xs cursor-pointer";
        btnInv.className = "py-2.5 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer";

        submitBtn.className = "px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold cursor-pointer shadow-xs";
        submitBtn.innerText = "Simpan Penerimaan Gudang";

        fieldGoodId.removeAttribute('required'); // Handled flexibly with new BHP fallback
        fieldGoodName.removeAttribute('required');
        fieldSimBmd.removeAttribute('required');
        fieldRoomId.removeAttribute('required');
    }
    calculateReceiptTotal();
}

function calculateReceiptTotal() {
    const qtyInput = document.getElementById('field-quantity');
    const priceInput = document.getElementById('field-unit-price');
    const liveTotal = document.getElementById('receipt-live-total');
    const formulaEl = document.getElementById('receipt-calc-formula');
    const footerTotal = document.getElementById('footer-live-total');

    const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
    const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
    const total = qty * price;

    const formattedTotal = 'Rp ' + total.toLocaleString('id-ID');
    const formattedPrice = 'Rp ' + price.toLocaleString('id-ID');

    if (liveTotal) liveTotal.innerText = formattedTotal;
    if (footerTotal) footerTotal.innerText = formattedTotal;
    if (formulaEl) formulaEl.innerText = `${qty} × ${formattedPrice} =`;
}

function fillQuickPreset(name, code, kib, unit, brand, spec, qty, price) {
    document.getElementById('field-good-name').value = name;
    document.getElementById('field-sim-bmd-code').value = code;
    document.getElementById('field-kib-type').value = kib;
    document.getElementById('field-unit').value = unit;
    if (brand) document.getElementById('field-brand-model').value = brand;
    if (spec) document.getElementById('field-specification').value = spec;
    if (qty) document.getElementById('field-quantity').value = qty;
    if (price) document.getElementById('field-unit-price').value = price;
    calculateReceiptTotal();
}

function fillQuickBhp(name, code, category, unit, price) {
    document.getElementById('receipt-new-bhp-code').value = code;
    document.getElementById('receipt-new-bhp-name').value = name;
    document.getElementById('receipt-new-bhp-category').value = category;
    document.getElementById('receipt-new-bhp-unit').value = unit;
    document.getElementById('field-unit').value = unit;
    if (price) document.getElementById('field-unit-price').value = price;

    const badge = document.getElementById('receipt-bhp-selected-badge');
    const badgeText = document.getElementById('receipt-bhp-selected-text');
    badgeText.innerHTML = `Preset Cepat Terpilih: <strong class="text-emerald-950 dark:text-emerald-200 font-mono">[${code}] ${name}</strong> &bull; Satuan: <strong>${unit}</strong>`;
    badge.classList.remove('hidden');

    calculateReceiptTotal();
}

function openInlineQuickAddRoom() {
    document.getElementById('modal-quick-add-room').classList.remove('hidden');
    document.getElementById('quick-room-error').classList.add('hidden');
    document.getElementById('quick-room-code').focus();
}

function closeInlineQuickAddRoom() {
    document.getElementById('modal-quick-add-room').classList.add('hidden');
}

function submitInlineQuickAddRoom() {
    const code = document.getElementById('quick-room-code').value.trim();
    const name = document.getElementById('quick-room-name').value.trim();
    const capacity = document.getElementById('quick-room-capacity').value.trim();
    const condition = document.getElementById('quick-room-condition').value.trim();
    const errorEl = document.getElementById('quick-room-error');

    if (!code || !name) {
        errorEl.innerText = 'Kode dan nama ruangan wajib diisi.';
        errorEl.classList.remove('hidden');
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    fetch('{{ route('sarpras.rooms.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
            code: code,
            name: name,
            capacity: parseInt(capacity) || 36,
            condition: condition || 'Baik',
            person_in_charge: 'Pengelola Sarpras'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.room) {
            const select = document.getElementById('field-room-id');
            const opt = new Option(`${data.room.name} (${data.room.code})`, data.room.id, true, true);
            select.add(opt);
            select.value = data.room.id;
            closeInlineQuickAddRoom();
            document.getElementById('quick-room-code').value = '';
            document.getElementById('quick-room-name').value = '';
        } else {
            errorEl.innerText = data.message || 'Gagal menyimpan ruangan.';
            errorEl.classList.remove('hidden');
        }
    })
    .catch(err => {
        errorEl.innerText = 'Terjadi kesalahan sistem saat menyimpan ruang.';
        errorEl.classList.remove('hidden');
    });
}

function applySimBmdPreset(code) {
    if (!code) return;
    const select = document.getElementById('preset-sim-bmd');
    const option = select.options[select.selectedIndex];
    if (!option) return;

    const name = option.getAttribute('data-name');
    const kib = option.getAttribute('data-kib');
    const unit = option.getAttribute('data-unit');

    document.getElementById('field-good-name').value = name || '';
    document.getElementById('field-sim-bmd-code').value = code || '';
    if (kib) {
        document.getElementById('field-kib-type').value = kib;
    }
    if (unit) {
        document.getElementById('field-unit').value = unit;
    }
}

function openReceiptBhpCatalog() {
    openConsumableModal();
    // Override handler when user selects an item in consumable modal
    const originalSelect = window.selectConsumableItem;
    window.selectConsumableItem = function(code) {
        originalSelect(code);
        applyReceiptBhpItem(code);
    };
}

function applyReceiptBhpItem(code) {
    const raw = window.consumableCatalogData ? window.consumableCatalogData[code] : null;
    if (!raw) return;

    const select = document.getElementById('field-good-id');
    let found = false;

    // Check if item already exists in existing goods select
    for (let i = 0; i < select.options.length; i++) {
        const opt = select.options[i];
        const optCode = opt.getAttribute('data-code');
        const optName = opt.getAttribute('data-name');
        if (optCode === code || (optName && optName.toLowerCase() === raw.name.toLowerCase())) {
            select.selectedIndex = i;
            found = true;
            break;
        }
    }

    if (!found) {
        // Create dynamic option for this newly chosen catalog item
        const newOpt = new Option(`[Katalog BHP Baru] ${raw.name} (${code})`, '', true, true);
        select.add(newOpt);
        select.value = '';
    }

    // Set hidden fields
    document.getElementById('receipt-new-bhp-code').value = code;
    document.getElementById('receipt-new-bhp-name').value = raw.name;
    document.getElementById('receipt-new-bhp-category').value = raw.category || 'Alat Tulis Kantor';
    document.getElementById('receipt-new-bhp-unit').value = raw.unit || 'Buah';

    // Show badge
    const badge = document.getElementById('receipt-bhp-selected-badge');
    const badgeText = document.getElementById('receipt-bhp-selected-text');
    badgeText.innerHTML = `Terpilih dari Katalog BHP: <strong class="text-emerald-950 font-mono">[${code}] ${raw.name}</strong> &bull; Satuan: <strong>${raw.unit}</strong>`;
    badge.classList.remove('hidden');
}

function clearReceiptBhpSelection() {
    document.getElementById('receipt-new-bhp-code').value = '';
    document.getElementById('receipt-new-bhp-name').value = '';
    document.getElementById('receipt-new-bhp-category').value = '';
    document.getElementById('receipt-new-bhp-unit').value = '';
    document.getElementById('receipt-bhp-selected-badge').classList.add('hidden');
    document.getElementById('field-good-id').value = '';
}

function onGoodIdChanged(select) {
    if (select.value) {
        document.getElementById('receipt-new-bhp-code').value = '';
        document.getElementById('receipt-new-bhp-name').value = '';
        document.getElementById('receipt-new-bhp-category').value = '';
        document.getElementById('receipt-new-bhp-unit').value = '';
        document.getElementById('receipt-bhp-selected-badge').classList.add('hidden');
    }
}

function openEditReceiptModal(rcp) {
    document.getElementById('form-edit-receipt').action = '/warehouse/receipts/' + rcp.id;
    document.getElementById('edit-receipt-title').innerText = 'Edit Penerimaan ' + rcp.receipt_number;
    document.getElementById('edit-supplier-name').value = rcp.supplier_name || '';
    document.getElementById('edit-receipt-date').value = rcp.receipt_date ? rcp.receipt_date.substring(0, 10) : '';
    document.getElementById('edit-funding-source').value = rcp.funding_source || 'BOS Reguler';
    document.getElementById('edit-receipt-notes').value = rcp.notes || '';
    document.getElementById('modal-edit-receipt').classList.remove('hidden');
}
</script>

<!-- Modal Edit Penerimaan Barang -->
<div id="modal-edit-receipt" class="hidden fixed inset-0 z-[100] bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-4 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <div>
                <h3 id="edit-receipt-title" class="font-bold text-slate-900 dark:text-white text-base">Edit Data Penerimaan Barang</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui informasi rekanan, tanggal & sumber dana</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-receipt').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="form-edit-receipt" method="POST" class="flex flex-col flex-1 min-h-0 m-0">
            @csrf
            @method('PUT')
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto min-h-0 flex-1">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Toko / Penyedia (Supplier) <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="text" id="edit-supplier-name" name="supplier_name" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Penerimaan *</label>
                        <input type="date" id="edit-receipt-date" name="receipt_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Sumber Dana *</label>
                        <select id="edit-funding-source" name="funding_source" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                            @foreach($fundingSources as $fs)
                                <option value="{{ $fs->name }}">{{ $fs->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan</label>
                    <textarea id="edit-receipt-notes" name="notes" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-edit-receipt').classList.add('hidden')" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold shadow-sm cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<x-sim-bmd-search-modal 
    targetNameId="field-good-name" 
    targetCodeId="field-sim-bmd-code" 
    targetKibId="field-kib-type" 
    targetUnitId="field-unit" 
    presetSelectId="preset-sim-bmd" 
/>

<x-consumable-search-modal />
@endpush

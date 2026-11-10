@extends('layouts.app')

@section('title', 'Transaksi Kas & Bukti Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">Keuangan Sekolah Terpadu</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Juknis BOSP & BOPD Jabar</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Transaksi Kas & SPJ Belanja</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan kas masuk & keluar, bukti kwitansi (SPJ), pemotongan pajak, serta keterkaitan langsung dengan logistik BAST gudang dan pengadaan aset sarpras.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.bku.index') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Buku Kas Umum (BKU)
            </a>
            <button onclick="document.getElementById('modal-trans').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Transaksi Kas
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('finance.transactions.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <select name="type" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-medium">
                <option value="">Semua Jenis Transaksi</option>
                <option value="Penerimaan" {{ request('type') === 'Penerimaan' ? 'selected' : '' }}>Penerimaan (Kas Masuk)</option>
                <option value="Pengeluaran" {{ request('type') === 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran (Kas Keluar)</option>
            </select>
            <select name="source" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-medium">
                <option value="">Semua Sumber Dana</option>
                <option value="BOSP Reguler" {{ request('source') === 'BOSP Reguler' ? 'selected' : '' }}>BOSP Reguler</option>
                <option value="BOSP Kinerja" {{ request('source') === 'BOSP Kinerja' ? 'selected' : '' }}>BOSP Kinerja</option>
                <option value="BOPD Pemprov" {{ request('source') === 'BOPD Pemprov' ? 'selected' : '' }}>BOPD Pemprov</option>
                <option value="Komite Sekolah" {{ request('source') === 'Komite Sekolah' ? 'selected' : '' }}>Komite Sekolah</option>
            </select>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5">No. Bukti & Tanggal</th>
                        <th class="px-4 py-3.5">Jenis & Sumber</th>
                        <th class="px-5 py-3.5">Uraian Transaksi & Keterkaitan</th>
                        <th class="px-4 py-3.5">Pihak Penerima / Pembayar</th>
                        <th class="px-4 py-3.5 text-right">Debit (Masuk)</th>
                        <th class="px-4 py-3.5 text-right">Kredit (Keluar)</th>
                        <th class="px-4 py-3.5 text-center">Pajak</th>
                        <th class="px-4 py-3.5 text-center">Aksi Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $t)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3.5">
                                <div class="font-mono font-bold text-slate-900">{{ $t->evidence_number }}</div>
                                <div class="text-[11px] text-slate-500">{{ $t->transaction_date?->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($t->type === 'Penerimaan') bg-emerald-100 text-emerald-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $t->type }}
                                </span>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $t->payment_method }} &bull; {{ $t->funding_source }}</div>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="font-mono text-[10px] font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                        {{ $t->account_code ?? ($t->budgetItem?->account_code ?? '5.1.02') }}
                                    </span>
                                    @if($t->budgetItem)
                                        <span class="text-[10px] text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded font-medium">{{ $t->budgetItem->program_name }}</span>
                                    @endif
                                </div>
                                <div class="font-bold text-slate-900 line-clamp-1">{{ $t->description }}</div>
                                @if($t->budgetItem)
                                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">{{ $t->budgetItem->activity_name }}</div>
                                @endif
                                
                                {{-- Keterkaitan Logistik Gudang (BAST) & Sarpras (Aset) --}}
                                @if($t->goodsReceipt)
                                    <div class="mt-1 flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            BAST: {{ $t->goodsReceipt->receipt_number }}
                                        </span>
                                    </div>
                                @elseif($t->asset)
                                    <div class="mt-1 flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                            <svg class="w-3 h-3 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                                            Aset: {{ $t->asset->name }} ({{ $t->asset->code }})
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-slate-800 font-medium">
                                {{ $t->recipient_or_payer }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-700">
                                {{ $t->debit > 0 ? 'Rp ' . number_format($t->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-rose-700">
                                {{ $t->credit > 0 ? 'Rp ' . number_format($t->credit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($t->taxes->isNotEmpty())
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        {{ $t->taxes->first()->tax_type }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('finance.transactions.receipt.print', $t) }}" target="_blank" title="Cetak Kuitansi SPJ" class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <form action="{{ route('finance.transactions.receipt.archive', $t) }}" method="POST" class="inline" onsubmit="return confirm('Simpan Kuitansi SPJ ini ke modul Kearsipan Digital?')">
                                        @csrf
                                        <button type="submit" title="1-Klik Simpan ke Arsip Digital" class="p-1.5 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                        </button>
                                    </form>
                                    <button type="button" onclick="openEditTransModal({{ json_encode($t) }})" title="Edit Transaksi" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('finance.transactions.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus transaksi kas ini? Saldo BKU akan otomatis dihitung ulang.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Transaksi" class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Belum ada transaksi kas yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Catat Transaksi -->
<div id="modal-trans" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-xl my-8">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Catat Transaksi Kas & SPJ Belanja</h2>
                <p class="text-xs text-slate-500">Mendukung keterkaitan dengan logistik BAST gudang & pengadaan aset.</p>
            </div>
            <button onclick="document.getElementById('modal-trans').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('finance.transactions.store') }}" method="POST" class="space-y-4 text-xs mt-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Transaksi <span class="text-rose-500">*</span></label>
                    <select name="type" id="select_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold bg-slate-50">
                        <option value="Pengeluaran">Pengeluaran (Kas Keluar / SPJ)</option>
                        <option value="Penerimaan">Penerimaan (Kas Masuk / Droping)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            {{-- Keterkaitan Logistik Gudang (BAST) & Sarpras --}}
            <div class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-200/80 space-y-3">
                <div class="font-bold text-blue-900 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    Tautan Pembayaran Logistik / Aset Sarpras (Opsional)
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-slate-600 mb-1">Penerimaan Barang Gudang (BAST)</label>
                        <select name="goods_receipt_id" id="select_goods_receipt" onchange="autoFillFromReceipt(this)" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg bg-white">
                            <option value="">-- Bukan Belanja Logistik Gudang --</option>
                            @foreach($goodsReceipts as $gr)
                                <option value="{{ $gr->id }}" 
                                        data-supplier="{{ $gr->supplier_name ?: 'Penyedia' }}" 
                                        data-amount="{{ (int)$gr->total_amount }}" 
                                        data-notes="{{ $gr->notes ?? 'Pengadaan barang persediaan logistik gudang' }}"
                                        data-receipt-num="{{ $gr->receipt_number }}">
                                    {{ $gr->receipt_number }} - {{ $gr->supplier_name ?: 'Penyedia' }} (Rp {{ number_format($gr->total_amount, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-600 mb-1">Pengadaan Aset Sarpras (BMD)</label>
                        <select name="asset_id" id="select_asset" onchange="autoFillFromAsset(this)" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg bg-white">
                            <option value="">-- Bukan Belanja Modal Aset --</option>
                            @foreach($assets as $ast)
                                <option value="{{ $ast->id }}" 
                                        data-name="{{ $ast->name }}" 
                                        data-price="{{ (int)$ast->price }}"
                                        data-code="{{ $ast->code }}">
                                    {{ $ast->code }} - {{ $ast->name }} (Rp {{ number_format($ast->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Dana <span class="text-rose-500">*</span></label>
                    <select name="funding_source" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="BOSP Reguler">BOSP Reguler</option>
                        <option value="BOSP Kinerja">BOSP Kinerja</option>
                        <option value="BOPD Pemprov">BOPD Pemprov</option>
                        <option value="Hibah">Hibah</option>
                        <option value="Komite Sekolah">Komite Sekolah</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <select name="payment_method" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Transfer Bank">Transfer Bank (Non-Tunai)</option>
                        <option value="Tunai">Kas Tunai</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alokasi Sub Kegiatan RKAS (Jika Pengeluaran)</label>
                <select name="budget_item_id" id="select_budget_item" onchange="autoFillAccountCodeFromBudget(this, 'input_trans_account_code')" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Tidak Terkait Sub Kegiatan / Umum --</option>
                    @foreach($budgetItems as $bi)
                        <option value="{{ $bi->id }}" data-account-code="{{ $bi->account_code }}">{{ $bi->activity_name }} ({{ $bi->account_code ?? '5.1.02' }} - Pagu: Rp {{ number_format($bi->allocated_amount, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-semibold text-slate-700">Kode Rekening Keuangan (BOSP / SIPD / BAS)</label>
                    <button type="button" onclick="openAccountCodeModal('account-code-trans-modal', {targetCodeId: 'input_trans_account_code', targetActivityId: 'input_description'})" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[11px] font-bold border border-emerald-200 cursor-pointer transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari Kode Rekening</span>
                    </button>
                </div>
                <input type="text" name="account_code" id="input_trans_account_code" placeholder="5.1.02.01.01.0024" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                <span class="text-[10px] text-slate-400 mt-1 block">💡 Otomatis terisi jika memilih Sub Kegiatan RKAS di atas, atau dapat dicari via katalog & diisi manual secara bebas.</span>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Uraian Keperluan Transaksi <span class="text-rose-500">*</span></label>
                <textarea name="description" id="input_description" rows="2" placeholder="Pembelian ATK persiapan Asesmen Nasional..." required class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Penerima / Pembayar <span class="text-rose-500">*</span></label>
                    <input type="text" name="recipient_or_payer" id="input_recipient" placeholder="Toko Buku Berkah Mandiri" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal Transaksi (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" id="input_amount" value="1500000" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-slate-600 mb-1">Nomor SPK / Surat Pesanan (Opsional)</label>
                    <input type="text" name="spk_number" placeholder="SPK/2026/015" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-medium text-slate-600 mb-1">Nomor Faktur / Nota Toko (Opsional)</label>
                    <input type="text" name="invoice_number" placeholder="INV-2026-99" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
                </div>
            </div>

            <!-- Tax Section -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                    <input type="checkbox" name="has_tax" value="1" onchange="document.getElementById('tax-inputs').classList.toggle('hidden', !this.checked)" class="w-4 h-4 text-blue-600 rounded">
                    <span>Transaksi ini dikenakan Pungutan Pajak</span>
                </label>
                <div id="tax-inputs" class="hidden grid grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Jenis Pajak</label>
                        <select name="tax_type" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
                            <option value="PPN 11%">PPN (11%)</option>
                            <option value="PPh Pasal 21">PPh Pasal 21</option>
                            <option value="PPh Pasal 22">PPh Pasal 22</option>
                            <option value="PPh Pasal 23">PPh Pasal 23</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Nominal Pajak Dipotong (Rp)</label>
                        <input type="number" name="tax_amount" value="0" min="0" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-trans').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Transaksi Kas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Transaksi Kas -->
<div id="modal-edit-trans" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-xl my-8">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Edit Transaksi Kas</h2>
                <p class="text-xs text-slate-500 font-mono" id="edit-trans-evidence"></p>
            </div>
            <button onclick="document.getElementById('modal-edit-trans').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="form-edit-trans" method="POST" class="space-y-4 text-xs mt-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Transaksi <span class="text-rose-500">*</span></label>
                    <select name="type" id="edit_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold bg-slate-50">
                        <option value="Pengeluaran">Pengeluaran (Kas Keluar / SPJ)</option>
                        <option value="Penerimaan">Penerimaan (Kas Masuk / Droping)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" id="edit_transaction_date" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Dana <span class="text-rose-500">*</span></label>
                    <select name="funding_source" id="edit_funding_source" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="BOSP Reguler">BOSP Reguler</option>
                        <option value="BOSP Kinerja">BOSP Kinerja</option>
                        <option value="BOPD Pemprov">BOPD Pemprov</option>
                        <option value="Hibah">Hibah</option>
                        <option value="Komite Sekolah">Komite Sekolah</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <select name="payment_method" id="edit_payment_method" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Transfer Bank">Transfer Bank (Non-Tunai)</option>
                        <option value="Tunai">Kas Tunai</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alokasi Sub Kegiatan RKAS (Jika Pengeluaran)</label>
                <select name="budget_item_id" id="edit_budget_item_id" onchange="autoFillAccountCodeFromBudget(this, 'edit_trans_account_code')" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Tidak Terkait Sub Kegiatan / Umum --</option>
                    @foreach($budgetItems as $bi)
                        <option value="{{ $bi->id }}" data-account-code="{{ $bi->account_code }}">{{ $bi->activity_name }} ({{ $bi->account_code ?? '5.1.02' }} - Pagu: Rp {{ number_format($bi->allocated_amount, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-semibold text-slate-700">Kode Rekening Keuangan (BOSP / SIPD / BAS)</label>
                    <button type="button" onclick="openAccountCodeModal('account-code-trans-modal', {targetCodeId: 'edit_trans_account_code', targetActivityId: 'edit_description'})" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[11px] font-bold border border-emerald-200 cursor-pointer transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari Kode Rekening</span>
                    </button>
                </div>
                <input type="text" name="account_code" id="edit_trans_account_code" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                <span class="text-[10px] text-slate-400 mt-1 block">💡 Otomatis terisi jika memilih Sub Kegiatan RKAS di atas, atau dapat dicari via katalog & diisi manual secara bebas.</span>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Uraian Keperluan Transaksi <span class="text-rose-500">*</span></label>
                <textarea name="description" id="edit_description" rows="2" required class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Penerima / Pembayar <span class="text-rose-500">*</span></label>
                    <input type="text" name="recipient_or_payer" id="edit_recipient_or_payer" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal Transaksi (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" id="edit_amount" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-slate-600 mb-1">Nomor SPK / Surat Pesanan (Opsional)</label>
                    <input type="text" name="spk_number" id="edit_spk_number" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-medium text-slate-600 mb-1">Nomor Faktur / Nota Toko (Opsional)</label>
                    <input type="text" name="invoice_number" id="edit_invoice_number" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-edit-trans').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditTransModal(t) {
        document.getElementById('edit-trans-evidence').innerText = 'No. Bukti: ' + t.evidence_number;
        document.getElementById('form-edit-trans').action = "{{ url('finance/transactions') }}/" + t.id;
        document.getElementById('edit_type').value = t.type || 'Pengeluaran';
        document.getElementById('edit_transaction_date').value = t.transaction_date ? t.transaction_date.substring(0, 10) : '';
        document.getElementById('edit_funding_source').value = t.funding_source || 'BOSP Reguler';
        document.getElementById('edit_payment_method').value = t.payment_method || 'Transfer Bank';
        document.getElementById('edit_budget_item_id').value = t.budget_item_id || '';
        document.getElementById('edit_trans_account_code').value = t.account_code || (t.budget_item ? t.budget_item.account_code : '');
        document.getElementById('edit_description').value = t.description || '';
        document.getElementById('edit_recipient_or_payer').value = t.recipient_or_payer || '';
        document.getElementById('edit_amount').value = (t.type === 'Penerimaan' ? parseFloat(t.debit) : parseFloat(t.credit)) || 0;
        document.getElementById('edit_spk_number').value = t.spk_number || '';
        document.getElementById('edit_invoice_number').value = t.invoice_number || '';
        document.getElementById('modal-edit-trans').classList.remove('hidden');
    }

    function autoFillAccountCodeFromBudget(select, targetInputId) {
        const opt = select.options[select.selectedIndex];
        if (opt && opt.dataset.accountCode) {
            document.getElementById(targetInputId).value = opt.dataset.accountCode;
        }
    }

    function autoFillFromReceipt(select) {
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('input_recipient').value = opt.dataset.supplier || '';
            document.getElementById('input_amount').value = opt.dataset.amount || 0;
            document.getElementById('input_description').value = 'Pembayaran pengadaan barang persediaan sesuai BAST No: ' + (opt.dataset.receiptNum || '') + ' (' + (opt.dataset.notes || '') + ')';
            document.getElementById('select_type').value = 'Pengeluaran';
            // Reset asset select
            document.getElementById('select_asset').value = '';
        }
    }

    function autoFillFromAsset(select) {
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('input_recipient').value = 'Penyedia Pengadaan Aset';
            document.getElementById('input_amount').value = opt.dataset.price || 0;
            document.getElementById('input_description').value = 'Belanja modal pengadaan aset inventaris: ' + (opt.dataset.name || '') + ' (' + (opt.dataset.code || '') + ')';
            document.getElementById('select_type').value = 'Pengeluaran';
            // Reset receipt select
            document.getElementById('select_goods_receipt').value = '';
        }
    }
</script>

<x-account-code-search-modal modal-id="account-code-trans-modal" target-code-id="input_trans_account_code" target-activity-id="input_description" />
@endpush

@extends('layouts.app')

@section('title', 'Kewajiban Perpajakan Sekolah')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Kewajiban Perpajakan (PPN & PPh)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemungutan pajak atas belanja barang/jasa, pembuatan Kode Billing DJP, dan pencatatan NTPN bukti setor kas negara.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.taxes.print') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Pembantu Pajak
            </a>
            <button onclick="document.getElementById('modal-tax').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Input Pungutan Pajak
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Total Pajak Terpotong</span>
            <span class="text-xl font-black text-slate-900 font-mono mt-1 block">Rp {{ number_format($totalTaxes, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Sudah Disetor ke Kas Negara</span>
            <span class="text-xl font-black text-emerald-700 font-mono mt-1 block">Rp {{ number_format($depositedTaxes, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Belum Disetor (Hutang Pajak)</span>
            <span class="text-xl font-black text-amber-600 font-mono mt-1 block">Rp {{ number_format($pendingTaxes, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Tax Records Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Jenis Pajak</th>
                        <th class="px-5 py-3.5">Transaksi Belanja Terkait</th>
                        <th class="px-5 py-3.5 text-right">Dasar Pengenaan Pajak (DPP)</th>
                        <th class="px-5 py-3.5 text-center">Tarif</th>
                        <th class="px-5 py-3.5 text-right">Nominal Pajak</th>
                        <th class="px-5 py-3.5">Kode Billing & NTPN</th>
                        <th class="px-5 py-3.5">Status Setor</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($taxes as $tx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    {{ $tx->tax_type }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                @if($tx->transaction)
                                    <div class="font-semibold text-slate-800 line-clamp-1">{{ $tx->transaction->description }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $tx->transaction->evidence_number }}</div>
                                @else
                                    <span class="text-slate-400">Pajak Langsung / Umum</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono text-slate-800">
                                Rp {{ number_format($tx->tax_base, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-800 font-mono">
                                {{ $tx->rate_percentage }}%
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($tx->tax_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 font-mono text-[11px]">
                                <div>Billing: <strong class="text-slate-800">{{ $tx->billing_code ?? '-' }}</strong></div>
                                <div>NTPN: <strong class="text-emerald-700">{{ $tx->ntpn ?? '-' }}</strong></div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($tx->status === 'Disetor')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Disetor ({{ $tx->deposit_date?->format('d/m/Y') }})
                                    </span>
                                @else
                                    <form action="{{ route('finance.taxes.status', $tx) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="ntpn" placeholder="Isi NTPN..." required class="w-24 px-2 py-1 border border-slate-300 rounded text-[10px] font-mono">
                                        <input type="hidden" name="status" value="Disetor">
                                        <input type="hidden" name="deposit_date" value="{{ date('Y-m-d') }}">
                                        <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold cursor-pointer">
                                            Setor
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="openEditTaxModal({{ json_encode($tx) }})" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Edit Pajak">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('finance.taxes.destroy', $tx) }}" method="POST" class="inline" onsubmit="return confirm('Hapus catatan pajak ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Pajak">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Belum ada catatan pemotongan pajak.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($taxes->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $taxes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Input Pajak -->
<div id="modal-tax" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Input Pungutan Pajak</h2>
        <p class="text-xs text-slate-500 mb-4">Pencatatan kewajiban pajak atas transaksi belanja sekolah.</p>

        <form action="{{ route('finance.taxes.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Transaksi Kas Terkait</label>
                <select name="cash_transaction_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Tidak Terkait / Input Mandiri --</option>
                    @foreach($transactions as $trx)
                        <option value="{{ $trx->id }}">{{ $trx->evidence_number }} - {{ $trx->description }} (Rp {{ number_format($trx->credit, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Pajak <span class="text-rose-500">*</span></label>
                    <select name="tax_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="PPN 11%">PPN (11%)</option>
                        <option value="PPh Pasal 21">PPh Pasal 21 (Honorarium)</option>
                        <option value="PPh Pasal 22">PPh Pasal 22 (Belanja Barang)</option>
                        <option value="PPh Pasal 23">PPh Pasal 23 (Jasa / Sewa)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tarif (%) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="rate_percentage" value="11" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Dasar Pengenaan (DPP) <span class="text-rose-500">*</span></label>
                    <input type="number" name="tax_base" value="10000000" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal Pajak (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="tax_amount" value="1100000" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Billing DJP</label>
                    <input type="text" name="billing_code" placeholder="012345678901234" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Pungutan <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Dipotong">Dipotong (Belum Disetor)</option>
                        <option value="Disetor">Sudah Disetor ke Kas Negara</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-tax').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Pajak</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pajak -->
<div id="modal-edit-tax" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Catatan Pajak</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui rincian potongan atau setoran pajak.</p>

        <form id="form-edit-tax" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Pajak <span class="text-rose-500">*</span></label>
                    <select name="tax_type" id="edit_tax_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="PPN 11%">PPN (11%)</option>
                        <option value="PPh Pasal 21">PPh Pasal 21 (Honorarium)</option>
                        <option value="PPh Pasal 22">PPh Pasal 22 (Belanja Barang)</option>
                        <option value="PPh Pasal 23">PPh Pasal 23 (Jasa / Sewa)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tarif (%) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="rate_percentage" id="edit_rate_percentage" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Dasar Pengenaan (DPP) <span class="text-rose-500">*</span></label>
                    <input type="number" name="tax_base" id="edit_tax_base" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal Pajak (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="tax_amount" id="edit_tax_amount" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Billing DJP</label>
                    <input type="text" name="billing_code" id="edit_billing_code" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NTPN (Bukti Setor)</label>
                    <input type="text" name="ntpn" id="edit_ntpn" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Setor</label>
                    <input type="date" name="deposit_date" id="edit_deposit_date" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Pungutan <span class="text-rose-500">*</span></label>
                    <select name="status" id="edit_status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Dipotong">Dipotong (Belum Disetor)</option>
                        <option value="Disetor">Sudah Disetor ke Kas Negara</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-tax').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditTaxModal(tx) {
    document.getElementById('form-edit-tax').action = `/finance/taxes/${tx.id}`;
    document.getElementById('edit_tax_type').value = tx.tax_type;
    document.getElementById('edit_rate_percentage').value = tx.rate_percentage;
    document.getElementById('edit_tax_base').value = tx.tax_base;
    document.getElementById('edit_tax_amount').value = tx.tax_amount;
    document.getElementById('edit_billing_code').value = tx.billing_code || '';
    document.getElementById('edit_ntpn').value = tx.ntpn || '';
    document.getElementById('edit_deposit_date').value = tx.deposit_date ? tx.deposit_date.substring(0, 10) : '';
    document.getElementById('edit_status').value = tx.status;
    document.getElementById('modal-edit-tax').classList.remove('hidden');
}
</script>
@endpush

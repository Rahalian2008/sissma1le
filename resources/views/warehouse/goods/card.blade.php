@extends('layouts.app')

@section('title', 'Kartu Stok Barang - ' . $good->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('warehouse.goods.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Kartu Stok Persediaan Barang</h1>
                <p class="text-xs text-slate-500 font-mono">{{ $good->code }} &bull; {{ $good->name }}</p>
            </div>
        </div>
        <div>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Kartu Stok
            </button>
        </div>
    </div>

    <!-- Good Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="text-slate-400 block text-[11px]">Kode Barang:</span>
            <span class="font-mono font-bold text-slate-900 text-sm">{{ $good->code }}</span>
        </div>
        <div>
            <span class="text-slate-400 block text-[11px]">Nama Item:</span>
            <span class="font-bold text-slate-900 text-sm">{{ $good->name }}</span>
        </div>
        <div>
            <span class="text-slate-400 block text-[11px]">Stok Fisik Tersedia:</span>
            <span class="font-bold text-emerald-700 text-base font-mono">{{ $good->current_stock }} {{ $good->unit }}</span>
        </div>
        <div>
            <span class="text-slate-400 block text-[11px]">Lokasi Rak:</span>
            <span class="font-semibold text-slate-800 text-sm">{{ $good->storage_location ?? '-' }}</span>
        </div>
    </div>

    <!-- Mutations History / Stock Card Ledger -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Mutasi Keluar / Masuk Barang
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">No. Bukti / Dokumen</th>
                        <th class="px-5 py-3.5">Pihak / Keterangan</th>
                        <th class="px-5 py-3.5 text-center">Masuk (+)</th>
                        <th class="px-5 py-3.5 text-center">Keluar (-)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($receipts as $r)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 text-slate-600 font-mono">{{ $r->receipt?->receipt_date?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 font-mono font-bold text-emerald-700">{{ $r->receipt?->receipt_number }}</td>
                            <td class="px-5 py-3 text-slate-800">Penerimaan dari {{ $r->receipt?->supplier_name ?: 'Pengadaan Barang' }}</td>
                            <td class="px-5 py-3 text-center font-mono font-bold text-emerald-700">+{{ $r->quantity }}</td>
                            <td class="px-5 py-3 text-center text-slate-400 font-mono">-</td>
                        </tr>
                    @endforeach
                    @foreach($issuances as $i)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 text-slate-600 font-mono">{{ $i->issuance?->issuance_date?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 font-mono font-bold text-rose-700">{{ $i->issuance?->issuance_number }}</td>
                            <td class="px-5 py-3 text-slate-800">Pengeluaran untuk {{ $i->issuance?->recipient_name }} ({{ $i->issuance?->purpose }})</td>
                            <td class="px-5 py-3 text-center text-slate-400 font-mono">-</td>
                            <td class="px-5 py-3 text-center font-mono font-bold text-rose-700">-{{ $i->quantity }}</td>
                        </tr>
                    @endforeach
                    @if($receipts->isEmpty() && $issuances->isEmpty())
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                Belum ada riwayat transaksi mutasi untuk barang ini.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

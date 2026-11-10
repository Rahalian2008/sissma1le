@extends('layouts.app')

@section('title', 'Detail Aset - ' . $asset->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('sarpras.assets.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ $asset->name }}</h1>
                <p class="text-xs text-slate-500 font-mono">{{ $asset->code }} &bull; Ruang: {{ $asset->room?->name ?? 'Gudang Utama' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if(isset($siblingAssets) && $siblingAssets->count() > 1)
                <a href="{{ route('sarpras.assets.labels.print', ['goods_receipt_id' => $asset->goods_receipt_id]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Semua Label Batch Ini ({{ $siblingAssets->count() }} Stiker)
                </a>
            @endif
            <a href="{{ route('sarpras.assets.label', $asset) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                Cetak Label QR Satuan
            </a>
            @if($asset->condition !== 'Dihapus/Afkir')
            <a href="{{ route('sarpras.disposals.index', ['asset_id' => $asset->id]) }}" class="inline-flex items-center px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl border border-rose-200 shadow-xs transition" title="Ajukan Usulan Penghapusan Barang Milik Daerah (BMD)">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Afkir / Hapus BMD
            </a>
            @endif
            <a href="{{ route('sarpras.assets.edit', $asset) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Asset Detail Card -->
    <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-slate-200 space-y-6 text-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                    @if($asset->condition === 'Baik') bg-emerald-100 text-emerald-800
                    @elseif($asset->condition === 'Rusak Ringan') bg-blue-100 text-blue-800
                    @else bg-amber-100 text-amber-800 @endif">
                    Kondisi: {{ $asset->condition }}
                </span>
                <h2 class="text-xl font-bold text-slate-900 mt-2">{{ $asset->name }}</h2>
                <p class="text-slate-500 font-mono mt-0.5">Kode Inventaris KIB: <strong class="text-slate-800">{{ $asset->code }}</strong></p>
            </div>
            <div class="text-center shrink-0">
                <div class="p-2 border border-slate-200 rounded-2xl inline-block bg-slate-50">
                    {!! $qrCodeSvg !!}
                </div>
                <div class="text-[9px] text-slate-400 font-mono mt-1">VERIFIKASI FISIK ASET</div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Merek & Model:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $asset->brand_model ?? '-' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Nomor Seri Pabrik:</span>
                <span class="font-mono font-bold text-slate-800 text-sm">{{ $asset->serial_number ?? '-' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Lokasi Ruangan:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $asset->room?->name ?? 'Belum Ditentukan' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Tahun Perolehan:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $asset->procurement_year }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Sumber Pendanaan:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $asset->funding_source }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Nilai Perolehan:</span>
                <span class="font-mono font-bold text-emerald-700 text-sm">Rp {{ number_format($asset->price, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($asset->notes)
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-slate-400 block text-[11px] mb-1">Spesifikasi & Keterangan:</span>
                <p class="text-slate-700 leading-relaxed">{{ $asset->notes }}</p>
            </div>
        @endif

        <!-- Maintenance History -->
        <div class="pt-6 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    Riwayat Servis & Pemeliharaan
                </h3>
            </div>

            @if($asset->maintenances->isEmpty())
                <p class="text-slate-400 py-3 text-center">Belum ada riwayat perbaikan untuk aset ini.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($asset->maintenances as $m)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800">{{ $m->issue_description }}</span>
                                <div class="text-[11px] text-slate-500">Biaya: Rp {{ number_format($m->actual_cost ?? $m->estimated_cost, 0, ',', '.') }} &bull; Teknisi: {{ $m->technician_or_vendor ?? 'Internal' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">{{ $m->status }}</span>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $m->request_date?->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if(isset($siblingAssets) && $siblingAssets->count() > 1)
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Daftar Unit Fisik dalam Entrian Batch Ini</h3>
                    <p class="text-slate-500 text-[11px] mt-0.5">Total {{ $siblingAssets->count() }} unit terdaftar bersamaan (No. Reg: {{ $siblingAssets->first()->register_number }} s.d. {{ $siblingAssets->last()->register_number }})</p>
                </div>
                <a href="{{ route('sarpras.assets.labels.print', ['goods_receipt_id' => $asset->goods_receipt_id]) }}" target="_blank" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg border border-indigo-200 transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Semua Label Batch ({{ $siblingAssets->count() }})
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2">
                @foreach($siblingAssets as $s)
                    <a href="{{ route('sarpras.assets.show', $s) }}" class="p-2 rounded-xl border text-center transition {{ $s->id === $asset->id ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200' }}">
                        <div class="font-mono text-xs">Reg {{ $s->register_number }}</div>
                        @if($s->serial_number)
                            <div class="text-[9px] opacity-75 truncate">{{ $s->serial_number }}</div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

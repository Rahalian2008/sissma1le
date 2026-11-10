@extends('layouts.app')

@section('title', 'Administrasi KIR (Kartu Inventaris Ruangan)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Kartu Inventaris Ruangan (KIR)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Administrasi pencatatan penempatan fisik barang inventaris per ruangan sekolah sesuai standar SIPD e-BMD (Permendagri No. 108/2016).</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('sarpras.rooms.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Kelola & Tambah Ruang Baru
            </a>
            @if($selectedRoom)
                <a href="{{ route('sarpras.assets.labels.print', ['room_id' => $selectedRoom->id]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Cetak Semua Label KIR Ruangan Ini
                </a>
                <a href="{{ route('sarpras.kir.print', $selectedRoom) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak KIR Resmi: {{ $selectedRoom->name }}
                </a>
            @endif
        </div>
    </div>

    <!-- Room Selector Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5">
        @foreach($rooms as $rm)
            <a href="{{ route('sarpras.kir.index', ['room_id' => $rm->id]) }}" 
               class="p-3 rounded-2xl border text-left transition flex flex-col justify-between {{ ($selectedRoom?->id === $rm->id) ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-500/20' : 'bg-white text-slate-800 border-slate-200 hover:border-indigo-300 hover:bg-slate-50' }}">
                <div>
                    <div class="text-[10px] font-mono uppercase tracking-wider {{ ($selectedRoom?->id === $rm->id) ? 'text-indigo-200' : 'text-slate-400' }}">{{ $rm->code }}</div>
                    <div class="font-bold text-xs line-clamp-1 mt-0.5 leading-snug">{{ $rm->name }}</div>
                </div>
                <div class="mt-2.5 pt-2 border-t {{ ($selectedRoom?->id === $rm->id) ? 'border-white/20 text-indigo-100' : 'border-slate-100 text-slate-500' }} flex items-center justify-between text-[11px]">
                    <span>{{ $rm->assets_count }} Aset</span>
                    <span class="text-[10px]">PJ: {{ Str::limit($rm->person_in_charge ?? '-', 10) }}</span>
                </div>
            </a>
        @endforeach

        <a href="{{ route('sarpras.rooms.index') }}" 
           class="p-3 rounded-2xl border-2 border-dashed border-blue-300 dark:border-blue-700 hover:border-blue-500 bg-blue-50/60 dark:bg-blue-950/20 hover:bg-blue-50 text-blue-700 dark:text-blue-300 transition flex flex-col justify-center items-center text-center gap-1 group">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-base group-hover:scale-110 transition-transform shadow-xs">
                +
            </div>
            <div class="font-bold text-xs mt-1">Buat Ruang Baru</div>
            <div class="text-[10px] text-blue-600/80 dark:text-blue-400 font-medium">Tambah untuk KIR</div>
        </a>
    </div>

    @if($selectedRoom)
        <!-- Room Detail Card & Assets in KIR -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">{{ $selectedRoom->name }}</h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700">{{ $selectedRoom->code }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Penanggung Jawab: <strong class="text-slate-800">{{ $selectedRoom->person_in_charge ?? 'Belum Ditentukan' }}</strong> • 
                            Kapasitas: <span class="font-medium">{{ $selectedRoom->capacity }} Orang</span> • 
                            Kondisi Ruang: <span class="font-semibold text-emerald-600">{{ $selectedRoom->condition }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start md:self-auto">
                    <div class="text-right text-xs">
                        <div class="text-slate-400">Total Nilai Inventaris Ruangan</div>
                        <div class="font-mono font-bold text-slate-900 text-sm">Rp {{ number_format($assets->sum('total_price'), 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Assets Table in this KIR -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Kode Barang SIPD e-BMD</th>
                            <th class="px-4 py-3">Register</th>
                            <th class="px-4 py-3">Nama / Jenis Barang</th>
                            <th class="px-4 py-3 text-center">Jumlah</th>
                            <th class="px-4 py-3">Merek / Tipe / No Seri</th>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3">KIB</th>
                            <th class="px-4 py-3">Kondisi</th>
                            <th class="px-4 py-3 text-right">Harga Satuan</th>
                            <th class="px-4 py-3 text-center">Label QR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assets as $idx => $ast)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-indigo-700">
                                    {{ $ast->sim_bmd_code ?? $ast->code }}
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                    {{ $ast->register_range ?? $ast->register_number }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('sarpras.assets.show', $ast->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                        {{ $ast->name }}
                                    </a>
                                    @if($ast->specification)
                                        <div class="text-[11px] text-slate-500 line-clamp-1">{{ $ast->specification }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ ($ast->quantity ?? 1) > 1 ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $ast->quantity ?? 1 }} {{ $ast->unit ?? 'Unit' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <div>{{ $ast->brand_model ?? '-' }}</div>
                                    @if($ast->serial_number)
                                        <div class="font-mono text-[10px] text-slate-400">SN: {{ $ast->serial_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-600">{{ $ast->procurement_year }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-purple-100 text-purple-800">
                                        KIB {{ $ast->kib_type ?? 'B' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $ast->condition === 'Baik' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $ast->condition }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($ast->price, 0, ',', '.') }}
                                    @if(($ast->quantity ?? 1) > 1)
                                        <div class="text-[10px] text-slate-400 font-normal">Total: Rp {{ number_format($ast->total_price, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if(!empty($ast->asset_ids) && count($ast->asset_ids) > 1)
                                        <a href="{{ route('sarpras.assets.labels.print', ['asset_ids' => implode(',', $ast->asset_ids)]) }}" target="_blank" class="p-1 text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-0.5" title="Cetak Semua Label Unit ({{ count($ast->asset_ids) }} Label)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                            <span class="text-[10px] font-bold">({{ count($ast->asset_ids) }})</span>
                                        </a>
                                    @else
                                        <a href="{{ route('sarpras.assets.label', $ast->id) }}" target="_blank" class="p-1 text-slate-400 hover:text-indigo-600 inline-block" title="Cetak Label Barcode/QR">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada barang inventaris yang dialokasikan di ruangan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl p-8 text-center border border-slate-200">
            <p class="text-slate-500 text-xs">Belum ada data ruangan yang terdaftar.</p>
        </div>
    @endif
</div>
@endsection

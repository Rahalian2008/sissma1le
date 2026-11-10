@extends('layouts.app')

@section('title', 'Pemeliharaan & Perbaikan Sarpras')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Pemeliharaan & Perbaikan Sarpras</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengajuan perbaikan aset rusak, estimasi biaya, alokasi teknisi/vendor, dan pelaporan perbaikan selesai.</p>
        </div>
        <div>
            <button onclick="document.getElementById('modal-maintenance').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ajukan Perbaikan Aset
            </button>
        </div>
    </div>

    <!-- Maintenance Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal Ajuan</th>
                        <th class="px-5 py-3.5">Aset yang Mengalami Kendala</th>
                        <th class="px-5 py-3.5">Uraian Kerusakan</th>
                        <th class="px-5 py-3.5 text-right">Biaya (Estimasi / Realisasi)</th>
                        <th class="px-5 py-3.5">Teknisi / Vendor</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($maintenances as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $m->request_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $m->asset?->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $m->asset?->code }} &bull; {{ $m->asset?->room?->name }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-800 max-w-xs">
                                <span class="line-clamp-2 font-medium">{{ $m->issue_description }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono">
                                <div class="font-bold text-slate-900">Rp {{ number_format($m->actual_cost ?? $m->estimated_cost, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-slate-400">Est: Rp {{ number_format($m->estimated_cost, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 font-semibold">
                                {{ $m->technician_or_vendor ?? 'Tim Sarpras Internal' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($m->status === 'Selesai') bg-emerald-100 text-emerald-800
                                    @elseif($m->status === 'Sedang Dikerjakan') bg-blue-100 text-blue-800
                                    @elseif($m->status === 'Disetujui') bg-indigo-100 text-indigo-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $m->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <form action="{{ route('sarpras.maintenance.status', $m) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="px-2 py-1 bg-slate-50 border border-slate-300 rounded-lg text-[11px]">
                                        <option value="Disetujui" {{ $m->status === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="Sedang Dikerjakan" {{ $m->status === 'Sedang Dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                                        <option value="Selesai" {{ $m->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="Ditolak" {{ $m->status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                    <input type="hidden" name="actual_cost" value="{{ $m->estimated_cost }}">
                                    <input type="hidden" name="technician_or_vendor" value="Teknisi Resmi">
                                    <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold text-[10px] cursor-pointer">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada jadwal atau riwayat pemeliharaan sarpras.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($maintenances->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $maintenances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Ajukan Pemeliharaan -->
<div id="modal-maintenance" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-[100] p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[90vh] overflow-hidden my-auto">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Ajukan Pemeliharaan Aset</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Input data aset bermasalah untuk ditindaklanjuti tim sarpras.</p>
            </div>
            <button onclick="document.getElementById('modal-maintenance').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('sarpras.maintenance.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden m-0">
            @csrf
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Aset Bermasalah <span class="text-rose-500">*</span></label>
                    <select name="asset_id" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                        <option value="">-- Pilih Aset --</option>
                        @foreach ($assets as $a)
                            <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->code }}) - Kondisi: {{ $a->condition }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pengajuan <span class="text-rose-500">*</span></label>
                        <input type="date" name="request_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Estimasi Biaya (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="estimated_cost" value="250000" min="0" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Uraian Gejala / Kerusakan <span class="text-rose-500">*</span></label>
                    <textarea name="issue_description" rows="2" placeholder="Jelaskan kondisi kerusakan yang dialami..." required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl"></textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rencana Tindakan / Perbaikan</label>
                    <input type="text" name="action_plan" placeholder="Penggantian baterai / servis mesin pendingin..." class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-maintenance').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold cursor-pointer shadow-xs">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endpush

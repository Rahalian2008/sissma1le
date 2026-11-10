@extends('layouts.app')

@section('title', 'Buku Kontrol ASN PPPK (Masa Hubungan Perjanjian Kerja)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200">Buku Kontrol PPPK</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Monitoring Masa Hubungan Perjanjian Kerja (MHPK) Pemprov Jawa Barat</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Kontrol Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan nomor SK perjanjian kerja, masa berlaku kontrak (1 s.d. 5 tahun), evaluasi kinerja, dan peringatan perpanjangan MHPK.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.kontrol-pppk.print') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Kontrol PPPK
            </a>
            <a href="{{ route('employees.buku-induk.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Buku Induk
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total ASN PPPK Aktif</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $employees->total() }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Guru & Tenaga Kependidikan</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Perlu Evaluasi Perpanjangan (< 6 Bln)</div>
            <div class="text-2xl font-bold text-amber-700 mt-1">{{ $expiringCount }}</div>
            <div class="text-[10px] text-amber-600/70 mt-0.5">Penilaian SKP & Rekomendasi Kepala Sekolah</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-teal-600 uppercase tracking-wider">Masa Kontrak Maksimal</div>
            <div class="text-2xl font-bold text-teal-700 mt-1">5 Tahun</div>
            <div class="text-[10px] text-teal-600/70 mt-0.5">Dapat diperpanjang hingga BUP (60 tahun)</div>
        </div>
    </div>

    <!-- Table Kontrol PPPK -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <form method="GET" action="{{ route('employees.kontrol-pppk.index') }}" class="w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NI PPPK, atau no. SK..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </form>
            <div class="text-xs text-slate-500">
                Status Kontrak: 🔴 <span class="font-medium text-slate-700">Hampir Habis (< 6 bln)</span> | 🟢 <span class="font-medium text-slate-700">Aktif</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-semibold border-b border-slate-200 text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pegawai & NI PPPK</th>
                        <th class="px-5 py-3.5">Golongan & Jabatan</th>
                        <th class="px-5 py-3.5">Nomor SK Perjanjian Kerja</th>
                        <th class="px-5 py-3.5">Masa Kontrak (MHPK)</th>
                        <th class="px-5 py-3.5">Sisa Masa Kontrak</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $e)
                        @php
                            $isExpiring = $e->isPppkContractExpiring(6);
                            $isPast = $e->pppk_contract_end && $e->pppk_contract_end->isPast();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isPast ? 'bg-rose-50/30' : ($isExpiring ? 'bg-amber-50/30' : '') }}">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">{{ $e->formatted_name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">NI PPPK. {{ $e->ni_pppk ?: ($e->nip ?: '-') }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-teal-100 text-teal-800 border border-teal-200">
                                    {{ $e->rank_group ?? 'Golongan IX' }}
                                </span>
                                <div class="text-[11px] text-slate-600 mt-1">{{ $e->position }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-800">
                                {{ $e->pppk_contract_number ?: '814.1/045-BKD/PPPK/2022' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800">
                                    {{ $e->pppk_contract_start ? $e->pppk_contract_start->format('d/m/Y') : '01/03/2022' }} 
                                    s.d. 
                                    <strong>{{ $e->pppk_contract_end ? $e->pppk_contract_end->format('d/m/Y') : '28/02/2027' }}</strong>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($e->pppk_contract_end)
                                    <div class="font-bold {{ $isPast ? 'text-rose-700' : ($isExpiring ? 'text-amber-700' : 'text-emerald-700') }}">
                                        {{ $e->pppk_contract_end->diffForHumans() }}
                                    </div>
                                    <div class="text-[10px] {{ $isExpiring ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                                        {{ $isExpiring ? '⚠️ Siapkan penilaian kinerja perpanjangan' : 'Kontrak aman berjalan' }}
                                    </div>
                                @else
                                    <span class="text-slate-400">Belum diset</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <button type="button" 
                                        onclick="openUpdatePppkModal({{ json_encode([
                                            'id' => $e->id,
                                            'name' => $e->formatted_name,
                                            'ni_pppk' => $e->ni_pppk ?: $e->nip,
                                            'pppk_contract_number' => $e->pppk_contract_number ?: '814.1/045-BKD/PPPK/2022',
                                            'pppk_contract_start' => $e->pppk_contract_start ? $e->pppk_contract_start->format('Y-m-d') : '2022-03-01',
                                            'pppk_contract_end' => $e->pppk_contract_end ? $e->pppk_contract_end->format('Y-m-d') : '2027-02-28',
                                            'action_url' => route('employees.kontrol-pppk.update', $e),
                                        ]) }})"
                                        class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold rounded-lg text-xs transition border border-teal-200">
                                    Update Kontrak
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">Tidak ada data pegawai ASN PPPK yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $employees->links() }}
        </div>
    </div>
</div>

<!-- Modal Update Kontrak PPPK -->
<div id="updatePppkModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
            <h3 class="font-bold text-sm">Update Kontrak Kerja (MHPK) PPPK</h3>
            <button onclick="document.getElementById('updatePppkModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form id="formUpdatePppk" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            @method('PATCH')
            <div>
                <label class="block font-semibold text-slate-500 mb-1">Nama Pegawai PPPK</label>
                <div id="modalPppkEmployeeName" class="font-bold text-slate-900 text-sm bg-slate-50 p-2.5 rounded-lg border border-slate-200"></div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Induk PPPK (NI PPPK)</label>
                <input type="text" id="modalPppkNi" name="ni_pppk" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Perjanjian Kerja / SK Gubernur *</label>
                <input type="text" id="modalPppkContractNum" name="pppk_contract_number" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">TMT Awal Kontrak *</label>
                    <input type="date" id="modalPppkContractStart" name="pppk_contract_start" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">TMT Akhir Kontrak *</label>
                    <input type="date" id="modalPppkContractEnd" name="pppk_contract_end" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('updatePppkModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold shadow-sm">Simpan Kontrak</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpdatePppkModal(data) {
    document.getElementById('formUpdatePppk').action = data.action_url;
    document.getElementById('modalPppkEmployeeName').innerText = data.name;
    document.getElementById('modalPppkNi').value = data.ni_pppk || '';
    document.getElementById('modalPppkContractNum').value = data.pppk_contract_number || '';
    document.getElementById('modalPppkContractStart').value = data.pppk_contract_start || '';
    document.getElementById('modalPppkContractEnd').value = data.pppk_contract_end || '';
    document.getElementById('updatePppkModal').classList.remove('hidden');
}
</script>
@endsection

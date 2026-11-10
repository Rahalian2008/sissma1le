@extends('layouts.app')

@section('title', 'Buku Kontrol Kenaikan Gaji Berkala (KGB)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Buku Kontrol KGB</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Dasar PP No. 5/2024 & PermenPAN-RB No. 7/2023 (Siklus 2 Tahunan)</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Kontrol Kenaikan Gaji Berkala (KGB) PNS & PPPK</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan jadwal berkala 2 tahun sekali, perhitungan gaji pokok baru, dan penerbitan Surat Pemberitahuan KGB ke Cabang Dinas & BPKAD.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.kontrol-kgb.print') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Kontrol KGB
            </a>
            <a href="{{ route('employees.buku-induk.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Buku Induk
            </a>
        </div>
    </div>

    <!-- Alert / Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Pegawai PNS & PPPK</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $employees->total() }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Berhak Kenaikan Gaji Berkala</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Jatuh Tempo KGB (Dalam 3 Bulan)</div>
            <div class="text-2xl font-bold text-rose-700 mt-1">{{ $dueSoonKgb }}</div>
            <div class="text-[10px] text-rose-600/70 mt-0.5">Segera terbitkan Surat Pemberitahuan KGB</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Siklus Berkala</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1">2 Tahun</div>
            <div class="text-[10px] text-emerald-600/70 mt-0.5">Syarat: Predikat Kinerja (SKP) Baik</div>
        </div>
    </div>

    <!-- Table Kontrol KGB -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <form method="GET" action="{{ route('employees.kontrol-kgb.index') }}" class="w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NIP, atau NI PPPK..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </form>
            <div class="text-xs text-slate-500">
                🔴 <span class="font-medium text-slate-700">Waktunya Terbit SK KGB</span> | 🟢 <span class="font-medium text-slate-700">Belum Jatuh Tempo</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-semibold border-b border-slate-200 text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pegawai & Status</th>
                        <th class="px-5 py-3.5">Pangkat / Golongan</th>
                        <th class="px-5 py-3.5">TMT KGB Terakhir</th>
                        <th class="px-5 py-3.5">Gaji Pokok Terakhir</th>
                        <th class="px-5 py-3.5">Jadwal KGB Berikutnya</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $e)
                        @php
                            $isDue = $e->isKgbDue(3);
                            $isPast = $e->next_kgb_tmt && $e->next_kgb_tmt->isPast();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isPast ? 'bg-rose-50/30' : ($isDue ? 'bg-amber-50/30' : '') }}">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">{{ $e->formatted_name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">
                                    {{ $e->nip ? 'NIP. '.$e->nip : ($e->ni_pppk ? 'NI PPPK. '.$e->ni_pppk : '-') }}
                                </div>
                                <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] font-bold {{ $e->isPns() ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $e->employment_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-800">{{ $e->rank_group ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $e->position }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $e->last_kgb_tmt ? $e->last_kgb_tmt->format('d F Y') : '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $e->last_kgb_tmt ? $e->last_kgb_tmt->diffForHumans() : '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-800">
                                Rp {{ number_format($e->last_kgb_salary ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold {{ $isPast ? 'text-rose-700' : ($isDue ? 'text-amber-700' : 'text-slate-800') }}">
                                    {{ $e->next_kgb_tmt ? $e->next_kgb_tmt->format('d F Y') : '-' }}
                                </div>
                                <div class="text-[10px] {{ $isPast ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
                                    @if($isPast)
                                        ⚠️ Sudah jatuh tempo (Segera proses)
                                    @elseif($isDue)
                                        ⏳ Jatuh tempo dalam 3 bulan
                                    @else
                                        Terjadwal tertib
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('employees.kontrol-kgb.surat', $e) }}" target="_blank" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold rounded-lg text-xs transition border border-emerald-200 inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Surat KGB
                                </a>
                                <button type="button" 
                                        onclick="openUpdateKgbModal({{ json_encode([
                                            'id' => $e->id,
                                            'name' => $e->formatted_name,
                                            'last_kgb_tmt' => $e->last_kgb_tmt ? $e->last_kgb_tmt->format('Y-m-d') : '',
                                            'last_kgb_salary' => (float)($e->last_kgb_salary ?? 0),
                                            'action_url' => route('employees.kontrol-kgb.update', $e),
                                        ]) }})"
                                        class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-xs transition border border-blue-200">
                                    Update KGB
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">Tidak ada data pegawai yang sesuai filter.</td>
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

<!-- Modal Update KGB -->
<div id="updateKgbModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
            <h3 class="font-bold text-sm">Update Kenaikan Gaji Berkala (KGB)</h3>
            <button onclick="document.getElementById('updateKgbModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form id="formUpdateKgb" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            @method('PATCH')
            <div>
                <label class="block font-semibold text-slate-500 mb-1">Nama Pegawai</label>
                <div id="modalKgbEmployeeName" class="font-bold text-slate-900 text-sm bg-slate-50 p-2.5 rounded-lg border border-slate-200"></div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">TMT KGB Baru *</label>
                <input type="date" id="modalKgbDate" name="last_kgb_tmt" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <p class="text-[10px] text-slate-400 mt-1">Sistem otomatis menjadwalkan KGB berikutnya 2 tahun dari tanggal ini.</p>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Gaji Pokok Baru (Rp) *</label>
                <input type="number" id="modalKgbSalary" name="last_kgb_salary" required min="0" step="1000" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Contoh: 3850000">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Pemberitahuan KGB</label>
                <input type="text" name="sk_number" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Contoh: 822.2/089-SMA.1/KGB/2026">
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('updateKgbModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow-sm">Simpan & Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpdateKgbModal(data) {
    document.getElementById('formUpdateKgb').action = data.action_url;
    document.getElementById('modalKgbEmployeeName').innerText = data.name;
    document.getElementById('modalKgbDate').value = data.last_kgb_tmt;
    document.getElementById('modalKgbSalary').value = data.last_kgb_salary;
    document.getElementById('updateKgbModal').classList.remove('hidden');
}
</script>
@endsection

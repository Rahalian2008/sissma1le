@extends('layouts.app')

@section('title', 'Buku Kontrol Pensiun Pegawai (BUP)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">Buku Kontrol BUP</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Batas Usia Pensiun ASN (Guru: 60 Thn | Tendik: 58 Thn)</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Kontrol Penjagaan Pensiun (BUP) Pegawai</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengawasan masa purna tugas ASN, penyiapan berkas usulan pemberhentian dengan hormat ke BKD & Cabang Dinas Wilayah IX.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.kontrol-pensiun.print', ['type' => request('type')]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Penjagaan BUP
            </a>
            <a href="{{ route('employees.buku-induk.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Buku Induk
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total ASN PNS/PPPK</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($employees->total()) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pegawai Terdaftar</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-amber-200 bg-amber-50/20">
            <div class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Masa Persiapan (MPP)</div>
            <div class="text-2xl font-bold text-amber-800 mt-1">{{ $mppCount }}</div>
            <div class="text-[10px] text-amber-600/80 mt-0.5">Sisa 1 s.d. 2 Tahun</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-rose-200 bg-rose-50/20">
            <div class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider">Jatuh Tempo Pensiun</div>
            <div class="text-2xl font-bold text-rose-800 mt-1">{{ $dueSoonCount }}</div>
            <div class="text-[10px] text-rose-600/80 mt-0.5">Dalam 12 Bulan / Tahun Berjalan</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-purple-200 bg-purple-50/20">
            <div class="text-[11px] font-semibold text-purple-700 uppercase tracking-wider">Purna Tugas / Pensiun</div>
            <div class="text-2xl font-bold text-purple-800 mt-1">{{ $retiredCount }}</div>
            <div class="text-[10px] text-purple-600/80 mt-0.5">Telah Masuk Masa Pensiun</div>
        </div>
    </div>

    <!-- Table Kontrol Pensiun -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <form method="GET" action="{{ route('employees.kontrol-pensiun.index') }}" class="w-full md:w-auto flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NIP, atau golongan..." class="px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 w-64">
                
                <select name="type" onchange="this.form.submit()" class="px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white">
                    <option value="">Semua Formasi PTK</option>
                    <option value="Guru" {{ request('type') === 'Guru' ? 'selected' : '' }}>Guru (BUP 60 Thn)</option>
                    <option value="Tenaga Kependidikan" {{ request('type') === 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan (BUP 58 Thn)</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white">
                    <option value="">Semua Status Pensiun</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Masih Aktif</option>
                    <option value="Masa Persiapan Pensiun" {{ request('status') === 'Masa Persiapan Pensiun' ? 'selected' : '' }}>Masa Persiapan (MPP)</option>
                    <option value="Usulan Berkas BKD" {{ request('status') === 'Usulan Berkas BKD' ? 'selected' : '' }}>Usulan Berkas BKD</option>
                    <option value="SK Diterbitkan" {{ request('status') === 'SK Diterbitkan' ? 'selected' : '' }}>SK Terbit</option>
                    <option value="Pensiun" {{ request('status') === 'Pensiun' ? 'selected' : '' }}>Sudah Pensiun</option>
                </select>

                <button type="submit" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold">Filter</button>
            </form>
            <div class="text-xs text-slate-500">
                Peringatan: 🔴 <span class="font-medium text-slate-700">&lt; 12 Bln</span> | 🟡 <span class="font-medium text-slate-700">1-2 Thn (MPP)</span> | 🟢 <span class="font-medium text-slate-700">&gt; 2 Thn</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Nama Pegawai & NIP</th>
                        <th class="px-4 py-3">Jabatan & Gol.</th>
                        <th class="px-4 py-3 text-center">Tgl Lahir / BUP</th>
                        <th class="px-4 py-3 text-center">TMT Pensiun</th>
                        <th class="px-4 py-3 text-center">Sisa Waktu</th>
                        <th class="px-4 py-3 text-center">Status Usulan</th>
                        <th class="px-4 py-3 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $index => $emp)
                        @php
                            $calcTmt = $emp->calculated_pension_tmt;
                            $remMonths = $emp->remaining_pension_months;
                            $isDue = $emp->isPensionDue(12);
                            $isMpp = $emp->isPensionMpp(24);
                            $isRetired = $emp->pension_status === 'Pensiun' || ! $emp->is_active;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isDue ? 'bg-rose-50/30' : ($isMpp ? 'bg-amber-50/20' : '') }}">
                            <td class="px-4 py-3 text-center text-slate-400">{{ $employees->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                    {{ $emp->formatted_name }}
                                    @if($isRetired)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-700">Pensiun</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    NIP. {{ $emp->nip ?? ($emp->ni_pppk ?? '-') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800">{{ $emp->position ?? ($emp->employment_type ?? '-') }}</div>
                                <div class="text-[11px] text-slate-500">
                                    Gol. {{ $emp->rank_group ?? '-' }} &bull; {{ $emp->employment_status }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="font-medium text-slate-800">
                                    {{ $emp->birth_date ? $emp->birth_date->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-[10px] text-purple-700 font-semibold">
                                    BUP: {{ $emp->retirement_age }} Thn
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-mono">
                                @if($calcTmt)
                                    <div class="font-bold {{ $isDue ? 'text-rose-700' : 'text-slate-800' }}">
                                        {{ $calcTmt->format('d/m/Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">1 {{ $calcTmt->translatedFormat('F Y') }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($isRetired)
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-slate-100 text-slate-600">Purna Tugas</span>
                                @elseif($remMonths !== null)
                                    @if($remMonths <= 0)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200 animate-pulse">Melewati BUP</span>
                                    @elseif($isDue)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                            🔴 {{ $remMonths }} Bulan lagi
                                        </span>
                                    @elseif($isMpp)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                            🟡 {{ $remMonths }} Bulan lagi
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-emerald-100 text-emerald-800">
                                            🟢 {{ floor($remMonths / 12) }} Thn {{ $remMonths % 12 }} Bln
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border
                                    {{ $emp->pension_status === 'Pensiun' ? 'bg-purple-100 text-purple-800 border-purple-200' : 
                                       ($emp->pension_status === 'SK Diterbitkan' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 
                                       ($emp->pension_status === 'Usulan Berkas BKD' ? 'bg-blue-100 text-blue-800 border-blue-200' : 
                                       ($emp->pension_status === 'Masa Persiapan Pensiun' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200'))) }}">
                                    {{ $emp->pension_status ?: 'Belum Waktunya' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" 
                                            onclick="openUpdatePensiunModal({{ json_encode([
                                                'name' => $emp->formatted_name,
                                                'nip' => $emp->nip ?? '-',
                                                'status' => $emp->pension_status ?: 'Belum Waktunya',
                                                'pension_tmt' => $calcTmt ? $calcTmt->toDateString() : '',
                                                'sk_number' => $emp->pension_sk_number ?? '',
                                                'notes' => $emp->pension_notes ?? '',
                                                'action_url' => route('employees.kontrol-pensiun.update', $emp)
                                            ]) }})"
                                            class="px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold rounded-lg text-xs transition border border-purple-200">
                                        Update
                                    </button>
                                    <a href="{{ route('employees.kontrol-pensiun.surat', $emp) }}" target="_blank" title="Cetak Surat Pengantar Pensiun" class="p-1 text-slate-500 hover:text-purple-600 bg-slate-50 hover:bg-purple-50 rounded-lg border border-slate-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">Tidak ada data pegawai yang sesuai kriteria filter.</td>
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

<!-- Modal Update Status Pensiun -->
<div id="updatePensiunModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 bg-purple-900 text-white flex items-center justify-between">
            <h3 class="font-bold text-sm">Update Status Penjagaan Pensiun (BUP)</h3>
            <button onclick="document.getElementById('updatePensiunModal').classList.add('hidden')" class="text-purple-200 hover:text-white">&times;</button>
        </div>
        <form id="formUpdatePensiun" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            @method('PATCH')
            <div>
                <label class="block font-semibold text-slate-500 mb-1">Nama Pegawai</label>
                <div id="modalPensiunEmployeeName" class="font-bold text-slate-900 text-sm bg-purple-50/50 p-2.5 rounded-lg border border-purple-200"></div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Usulan Pensiun *</label>
                <select id="modalPensiunStatus" name="pension_status" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="Belum Waktunya">Belum Waktunya</option>
                    <option value="Masa Persiapan Pensiun">Masa Persiapan Pensiun (MPP)</option>
                    <option value="Pemberkasan Dokumen">Pemberkasan Dokumen Pensiun</option>
                    <option value="Usulan Berkas BKD">Usulan Berkas Dikirim ke BKD / Cabdin</option>
                    <option value="SK Diterbitkan">SK Pensiun BKN Diterbitkan</option>
                    <option value="Pensiun">Resmi Purna Tugas / Pensiun (Non-aktifkan)</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Memilih status "Pensiun" otomatis memutakhirkan status aktif pegawai dan mencatat riwayat pensiun.</p>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">TMT Pensiun Resmi</label>
                <input type="date" id="modalPensiunTmt" name="pension_tmt" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor SK Pensiun BKN (Jika Sudah Terbit)</label>
                <input type="text" id="modalPensiunSkNumber" name="pension_sk_number" placeholder="Contoh: 00123/23201/KEP/05/26" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan / Keterangan Tambahan</label>
                <textarea id="modalPensiunNotes" name="pension_notes" rows="2" placeholder="Catatan berkas, nomor usul, atau hak Taspen..." class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('updatePensiunModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold shadow-sm">Simpan Status</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpdatePensiunModal(data) {
    document.getElementById('formUpdatePensiun').action = data.action_url;
    document.getElementById('modalPensiunEmployeeName').innerText = data.name + ' (' + data.nip + ')';
    document.getElementById('modalPensiunStatus').value = data.status;
    document.getElementById('modalPensiunTmt').value = data.pension_tmt;
    document.getElementById('modalPensiunSkNumber').value = data.sk_number;
    document.getElementById('modalPensiunNotes').value = data.notes;
    document.getElementById('updatePensiunModal').classList.remove('hidden');
}
</script>
@endsection

@extends('layouts.app')

@section('title', 'Buku Kontrol Kenaikan Pangkat (KP) PNS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Buku Kontrol KP</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Monitoring Usulan KP Periode April & Oktober</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Kontrol Kenaikan Pangkat (KP) Pegawai Negeri Sipil</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan siklus 4 tahunan / jabatan fungsional, estimasi jatuh tempo, dan manajemen berkas usulan ke BKD & Dinas Pendidikan Jawa Barat.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.kontrol-pangkat.print') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Kontrol KP
            </a>
            <a href="{{ route('employees.buku-induk.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Buku Induk
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Pegawai PNS Terdata</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $employees->total() }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Seluruh ASN PNS Aktif</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Jatuh Tempo Dalam 6 Bulan</div>
            <div class="text-2xl font-bold text-amber-700 mt-1">{{ $dueSoonCount }}</div>
            <div class="text-[10px] text-amber-600/70 mt-0.5">Penyiapan berkas DUPAK / SKP</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Siap / Melewati Estimasi</div>
            <div class="text-2xl font-bold text-rose-700 mt-1">{{ $eligibleCount }}</div>
            <div class="text-[10px] text-rose-600/70 mt-0.5">Prioritas pengusulan ke Cabdin Wil. IX</div>
        </div>
    </div>

    <!-- Table Kontrol KP -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <form method="GET" action="{{ route('employees.kontrol-pangkat.index') }}" class="w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NIP, atau golongan..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </form>
            <div class="text-xs text-slate-500">
                Peringatan: 🔴 <span class="font-medium text-slate-700">Waktunya Usul KP</span> | 🟢 <span class="font-medium text-slate-700">Belum Waktunya</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-semibold border-b border-slate-200 text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pegawai & NIP</th>
                        <th class="px-5 py-3.5">Pangkat / Golongan Saat Ini</th>
                        <th class="px-5 py-3.5">TMT Pangkat Terakhir</th>
                        <th class="px-5 py-3.5">Estimasi Jadwal KP Berikutnya</th>
                        <th class="px-5 py-3.5">Status Usulan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $e)
                        @php
                            $isDue = $e->isKpDue(6);
                            $isPast = $e->next_kp_tmt && $e->next_kp_tmt->isPast();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isPast ? 'bg-rose-50/30' : ($isDue ? 'bg-amber-50/30' : '') }}">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">{{ $e->formatted_name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">NIP. {{ $e->nip }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $e->position }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $e->rank_group ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $e->rank_tmt ? $e->rank_tmt->format('d F Y') : '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $e->rank_tmt ? $e->rank_tmt->diffForHumans() : '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold {{ $isPast ? 'text-rose-700' : ($isDue ? 'text-amber-700' : 'text-slate-800') }}">
                                    {{ $e->next_kp_tmt ? $e->next_kp_tmt->format('d F Y') : '-' }}
                                </div>
                                <div class="text-[10px] {{ $isPast ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
                                    @if($isPast)
                                        ⚠️ Sudah waktunya kenaikan pangkat
                                    @elseif($isDue)
                                        ⏳ Jatuh tempo dalam waktu dekat
                                    @else
                                        Masa dinas reguler
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $e->kp_status === 'SK Terbit' ? 'bg-emerald-100 text-emerald-800' : ($e->kp_status === 'Siap Diusulkan' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $e->kp_status ?? 'Belum Waktunya' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <button type="button" 
                                        onclick="openUpdateKpModal({{ json_encode([
                                            'id' => $e->id,
                                            'name' => $e->formatted_name,
                                            'status' => $e->kp_status ?? 'Belum Waktunya',
                                            'next_kp_tmt' => $e->next_kp_tmt ? $e->next_kp_tmt->format('Y-m-d') : '',
                                            'action_url' => route('employees.kontrol-pangkat.update', $e),
                                        ]) }})"
                                        class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold rounded-lg text-xs transition border border-amber-200">
                                    Status
                                </button>
                                <button type="button"
                                        onclick="openApplySkModal({{ json_encode([
                                            'id' => $e->id,
                                            'name' => $e->formatted_name,
                                            'current_rank' => $e->rank_group ?? '-',
                                            'action_url' => route('employees.kontrol-pangkat.apply-sk', $e),
                                        ]) }})"
                                        class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-xs transition shadow-xs inline-flex items-center gap-1">
                                    <span>📜 Terapkan SK</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">Tidak ada data pegawai PNS yang sesuai filter.</td>
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

<!-- Modal Update Status KP -->
<div id="updateKpModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
            <h3 class="font-bold text-sm">Update Status Usulan Kenaikan Pangkat</h3>
            <button onclick="document.getElementById('updateKpModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form id="formUpdateKp" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            @method('PATCH')
            <div>
                <label class="block font-semibold text-slate-500 mb-1">Nama Pegawai</label>
                <div id="modalKpEmployeeName" class="font-bold text-slate-900 text-sm bg-slate-50 p-2.5 rounded-lg border border-slate-200"></div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Usulan Kenaikan Pangkat *</label>
                <select id="modalKpStatus" name="kp_status" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    <option value="Belum Waktunya">Belum Waktunya</option>
                    <option value="Penyiapan Berkas">Penyiapan Berkas (DUPAK/SKP)</option>
                    <option value="Siap Diusulkan">Siap Diusulkan ke Cabdin Wil. IX</option>
                    <option value="Berkas Dikirim ke Cabdin">Berkas Dikirim ke Cabang Dinas</option>
                    <option value="Diproses BKD / BKN">Diproses BKD Jabar / BKN</option>
                    <option value="SK Terbit">SK Kenaikan Pangkat Terbit</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Estimasi TMT KP Berikutnya</label>
                <input type="date" id="modalKpNextDate" name="next_kp_tmt" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('updateKpModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Terapkan SK Kenaikan Pangkat Baru (Integrasi Otomatis) -->
<div id="applySkModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
        <div class="px-5 py-4 bg-emerald-700 text-white flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm">Terapkan SK Kenaikan Pangkat Baru</h3>
                <p class="text-[11px] text-emerald-100">Integrasi otomatis: Update golongan, catat riwayat, dan reschedule jadwal KP (+4 thn) & KGB (+2 thn).</p>
            </div>
            <button onclick="document.getElementById('applySkModal').classList.add('hidden')" class="text-emerald-200 hover:text-white">&times;</button>
        </div>
        <form id="formApplySk" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-500 mb-1">Pegawai</label>
                <div class="flex items-center justify-between bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                    <span id="modalApplyEmpName" class="font-bold text-slate-900 text-sm"></span>
                    <span class="text-xs text-slate-500">Golongan Saat Ini: <strong id="modalApplyCurrentRank" class="text-emerald-700"></strong></span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Golongan Ruang Baru *</label>
                    <select name="new_rank_group" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-semibold text-slate-800">
                        <option value="">-- Pilih Golongan Baru --</option>
                        <optgroup label="Golongan II (Pengatur)">
                            <option value="II/a">II/a (Pengatur Muda)</option>
                            <option value="II/b">II/b (Pengatur Muda Tk. I)</option>
                            <option value="II/c">II/c (Pengatur)</option>
                            <option value="II/d">II/d (Pengatur Tk. I)</option>
                        </optgroup>
                        <optgroup label="Golongan III (Penata)">
                            <option value="III/a">III/a (Penata Muda)</option>
                            <option value="III/b">III/b (Penata Muda Tk. I)</option>
                            <option value="III/c">III/c (Penata)</option>
                            <option value="III/d">III/d (Penata Tk. I)</option>
                        </optgroup>
                        <optgroup label="Golongan IV (Pembina)">
                            <option value="IV/a">IV/a (Pembina)</option>
                            <option value="IV/b">IV/b (Pembina Tk. I)</option>
                            <option value="IV/c">IV/c (Pembina Utama Muda)</option>
                            <option value="IV/d">IV/d (Pembina Utama Madya)</option>
                            <option value="IV/e">IV/e (Pembina Utama)</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">TMT Pangkat Baru *</label>
                    <input type="date" name="rank_tmt" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor SK Kenaikan Pangkat (BKN/BKD) *</label>
                <input type="text" name="sk_number" required placeholder="Contoh: 823.3/Kep.102-BKD/2026" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan angka kredit atau pertimbangan teknis BKN..." class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>
            <div class="bg-amber-50 p-2.5 rounded-lg border border-amber-200 text-amber-800 text-[11px] leading-relaxed">
                ℹ️ <strong>Sistem Otomatis:</strong> Setelah SK diterapkan, jadwal Kenaikan Gaji Berkala (KGB) berikutnya akan di-reset otomatis ke 2 tahun mendatang dan Kenaikan Pangkat berikutnya ke 4 tahun mendatang.
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('applySkModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow-sm">Terapkan SK & Mutakhirkan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpdateKpModal(data) {
    document.getElementById('formUpdateKp').action = data.action_url;
    document.getElementById('modalKpEmployeeName').innerText = data.name;
    document.getElementById('modalKpStatus').value = data.status;
    document.getElementById('modalKpNextDate').value = data.next_kp_tmt;
    document.getElementById('updateKpModal').classList.remove('hidden');
}

function openApplySkModal(data) {
    document.getElementById('formApplySk').action = data.action_url;
    document.getElementById('modalApplyEmpName').innerText = data.name;
    document.getElementById('modalApplyCurrentRank').innerText = data.current_rank;
    document.getElementById('applySkModal').classList.remove('hidden');
}
</script>
@endsection

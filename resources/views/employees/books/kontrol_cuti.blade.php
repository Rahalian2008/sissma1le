@extends('layouts.app')

@section('title', 'Buku Kontrol Cuti Pegawai (PNS & PPPK)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">Buku Kontrol Cuti</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Peraturan BKN No. 24/2017 & No. 7/2021 (Hak Cuti 12 Hari Kerja)</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Kontrol & Manajemen Cuti Pegawai</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan hak cuti tahunan, cuti sakit, melahirkan, alasan penting, dan penerbitan Surat Izin Cuti resmi.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.kontrol-cuti.print', ['year' => $year]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Cuti {{ $year }}
            </a>
            <button onclick="document.getElementById('modalTambahCuti').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Permohonan Cuti
            </button>
        </div>
    </div>

    <!-- Overview & Riwayat Terkini -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Saldo Cuti Pegawai Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                <h2 class="font-bold text-sm text-slate-900">Rekap Saldo Cuti Tahunan Pegawai (Tahun {{ $year }})</h2>
                <form method="GET" action="{{ route('employees.kontrol-cuti.index') }}">
                    <select name="year" onchange="this.form.submit()" class="px-2.5 py-1 text-xs border border-slate-300 rounded-lg">
                        @for($y = date('Y') + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-semibold border-b border-slate-200 text-[11px]">
                        <tr>
                            <th class="px-5 py-3">Nama Pegawai & NIP</th>
                            <th class="px-5 py-3 text-center">Hak Cuti</th>
                            <th class="px-5 py-3 text-center">Terpakai</th>
                            <th class="px-5 py-3 text-center">Sisa Saldo</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($employees as $e)
                            @php
                                $used = $e->usedLeaveDays($year);
                                $remaining = $e->remainingLeaveBalance($year);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3">
                                    <div class="font-bold text-slate-900">{{ $e->formatted_name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $e->nip ?: ($e->ni_pppk ?: $e->employment_status) }}</div>
                                </td>
                                <td class="px-5 py-3 text-center font-bold text-slate-700">12 Hari</td>
                                <td class="px-5 py-3 text-center font-semibold text-amber-700">{{ $used }} Hari</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $remaining <= 2 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $remaining }} Hari
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <button type="button" 
                                            onclick="openLeaveForEmployee({{ json_encode(['id' => $e->id, 'name' => $e->formatted_name]) }})"
                                            class="text-purple-600 hover:text-purple-800 font-semibold hover:underline text-xs">
                                        + Cuti
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-100">
                {{ $employees->links() }}
            </div>
        </div>

        <!-- 10 Cuti Terakhir Terbit -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-sm text-slate-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Surat Izin Cuti Diterbitkan
                </h2>
                <div class="space-y-2.5 max-h-[480px] overflow-y-auto">
                    @forelse($recentLeaves as $l)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 truncate max-w-[170px]">{{ $l->employee?->formatted_name }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">{{ $l->leave_type }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                {{ $l->start_date->format('d/m/Y') }} s.d. {{ $l->end_date->format('d/m/Y') }} ({{ $l->days_count }} Hari)
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 italic">Alasan: {{ $l->reason }}</div>
                            <div class="mt-2 text-right">
                                <a href="{{ route('employees.kontrol-cuti.surat', $l) }}" target="_blank" class="inline-flex items-center text-[11px] font-bold text-purple-700 hover:text-purple-900 hover:underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak Surat Izin Cuti
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Belum ada surat izin cuti yang diterbitkan pada tahun {{ $year }}.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Cuti -->
<div id="modalTambahCuti" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
            <h3 class="font-bold text-sm">Catat Permohonan Cuti Pegawai</h3>
            <button onclick="document.getElementById('modalTambahCuti').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('employees.kontrol-cuti.store') }}" method="POST" class="p-5 space-y-3.5 text-xs overflow-y-auto">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Pegawai Pemohon *</label>
                <select id="modalCutiEmployeeSelect" name="employee_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    <option value="">-- Pilih Guru / Tendik --</option>
                    @foreach($allEmployeesList as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->formatted_name }} ({{ $emp->employment_status }} - {{ $emp->position }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Cuti *</label>
                <select name="leave_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-semibold">
                    <option value="Cuti Tahunan">Cuti Tahunan (12 Hari Kerja / Tahun)</option>
                    <option value="Cuti Sakit">Cuti Sakit (Lampirkan Surat Dokter)</option>
                    <option value="Cuti Melahirkan">Cuti Melahirkan (Maksimal 3 Bulan)</option>
                    <option value="Cuti Karena Alasan Penting">Cuti Karena Alasan Penting (Keluarga Sakit Keras/Meninggal/Menikah)</option>
                    <option value="Cuti Besar">Cuti Besar (Masa Kerja > 5 Tahun)</option>
                    <option value="Cuti Bersama">Cuti Bersama (Sesuai Ketetapan Pemerintah)</option>
                    <option value="Cuti di Luar Tanggungan Negara">Cuti di Luar Tanggungan Negara (CLTN)</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai Cuti *</label>
                    <input type="date" id="cutiStartDate" name="start_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg" onchange="calculateLeaveDays()">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai Cuti *</label>
                    <input type="date" id="cutiEndDate" name="end_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg" onchange="calculateLeaveDays()">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Jumlah Hari Kerja *</label>
                <input type="number" id="cutiDaysCount" name="days_count" required min="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg" value="1">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alasan Pengajuan Cuti *</label>
                <textarea name="reason" rows="2" required class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Tuliskan alasan permohonan cuti secara rinci..."></textarea>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alamat Selama Menjalankan Cuti</label>
                <input type="text" name="address_during_leave" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Contoh: Jl. Sukajadi No. 12, Bandung (Telp: 08123456789)">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pegawai Pengganti / Pelaksana Harian</label>
                <select name="replacement_employee_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    <option value="">-- Pilih Rekan Pengganti (Opsional) --</option>
                    @foreach($allEmployeesList as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->formatted_name }} ({{ $emp->position }})</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalTambahCuti').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold shadow-sm">Terbitkan Izin Cuti</button>
            </div>
        </form>
    </div>
</div>

<script>
function openLeaveForEmployee(data) {
    document.getElementById('modalCutiEmployeeSelect').value = data.id;
    document.getElementById('modalTambahCuti').classList.remove('hidden');
}

function calculateLeaveDays() {
    const startVal = document.getElementById('cutiStartDate').value;
    const endVal = document.getElementById('cutiEndDate').value;
    if (!startVal || !endVal) return;

    const start = new Date(startVal);
    const end = new Date(endVal);
    if (end < start) return;

    // Hitung hari kerja (abaikan Sabtu & Minggu)
    let count = 0;
    let cur = new Date(start);
    while (cur <= end) {
        const day = cur.getDay();
        if (day !== 0 && day !== 6) { // Non-weekend
            count++;
        }
        cur.setDate(cur.getDate() + 1);
    }
    document.getElementById('cutiDaysCount').value = Math.max(1, count);
}
</script>
@endsection

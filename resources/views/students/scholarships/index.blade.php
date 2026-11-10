@extends('layouts.app')

@section('title', 'Administrasi Beasiswa & Bantuan Siswa (PIP / KETM)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Buku Bantuan & Beasiswa Siswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengelolaan program Program Indonesia Pintar (PIP), KETM Pemprov Jabar, dan Beasiswa Prestasi.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('students.scholarships.print') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Rekapitulasi
            </a>
            <button onclick="document.getElementById('modal-scholarship').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Penerima Beasiswa
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-xs text-slate-500 font-medium">Total Penerima</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ number_format($stats['total']) }} <span class="text-xs font-normal text-slate-500">Siswa</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-xs text-slate-500 font-medium">Program PIP</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['pip']) }} <span class="text-xs font-normal text-slate-500">Siswa</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-xs text-slate-500 font-medium">KETM Pemprov</div>
            <div class="text-2xl font-black text-purple-600 mt-1">{{ number_format($stats['ketm']) }} <span class="text-xs font-normal text-slate-500">Siswa</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-xs text-slate-500 font-medium">Total Dana Tersalurkan</div>
            <div class="text-xl font-black text-emerald-600 mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('students.scholarships.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Siswa, NISN, atau Program..." class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <select name="type" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Semua Jenis Beasiswa --</option>
                    <option value="PIP" {{ request('type') == 'PIP' ? 'selected' : '' }}>PIP Kemdikbud</option>
                    <option value="KETM" {{ request('type') == 'KETM' ? 'selected' : '' }}>BPMU / KETM Jabar</option>
                    <option value="Prestasi" {{ request('type') == 'Prestasi' ? 'selected' : '' }}>Beasiswa Prestasi</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Semua Status --</option>
                    <option value="Usulan" {{ request('status') == 'Usulan' ? 'selected' : '' }}>Usulan Sekolah</option>
                    <option value="SK Ditetapkan" {{ request('status') == 'SK Ditetapkan' ? 'selected' : '' }}>SK Ditetapkan</option>
                    <option value="Cair" {{ request('status') == 'Cair' ? 'selected' : '' }}>Sudah Cair / Diterima</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition">Filter</button>
                <a href="{{ route('students.scholarships.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-800 font-bold uppercase text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Siswa & NISN</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Program Beasiswa</th>
                        <th class="py-3 px-4">Sumber Dana</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($scholarships as $index => $sch)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">{{ $scholarships->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $sch->student?->name ?? 'Siswa Tidak Ditemukan' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $sch->student?->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-700">
                                {{ $sch->student?->schoolClass?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-indigo-700">
                                {{ $sch->scholarship_name }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $sch->source }}
                            </td>
                            <td class="py-3 px-4 font-bold text-emerald-700 text-right">
                                Rp {{ number_format($sch->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $sch->period }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $sch->status === 'Cair' || $sch->status === 'Tersalurkan' ? 'bg-emerald-100 text-emerald-800' : ($sch->status === 'SK Ditetapkan' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $sch->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('students.scholarships.destroy', $sch) }}" method="POST" onsubmit="return confirm('Hapus data beasiswa ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                Belum ada data beasiswa/bantuan siswa yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($scholarships->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $scholarships->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Penerima -->
<div id="modal-scholarship" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Catat Penerima Beasiswa / Bantuan</h2>
        <p class="text-xs text-slate-500 mb-4">Input data siswa penerima bantuan Program PIP, KETM, atau Prestasi.</p>

        <form action="{{ route('students.scholarships.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Cari / Pilih Siswa --</option>
                    @foreach ($students as $stu)
                        <option value="{{ $stu->id }}">{{ $stu->name }} ({{ $stu->schoolClass?->name ?? 'No Class' }} - NISN: {{ $stu->nisn ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Program <span class="text-rose-500">*</span></label>
                    <input type="text" name="scholarship_name" placeholder="Contoh: PIP Kemdikbud Tahap 1" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Dana <span class="text-rose-500">*</span></label>
                    <input type="text" name="source" placeholder="APBN / APBD Jabar / BAZNAS" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal Bantuan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" placeholder="1800000" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Periode / Tahun Anggaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="period" value="{{ date('Y') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Penyaluran <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="Usulan">Usulan Sekolah</option>
                    <option value="SK Ditetapkan">SK Ditetapkan</option>
                    <option value="Cair" selected>Cair / Diterima Siswa</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-scholarship').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold cursor-pointer">Simpan Beasiswa</button>
            </div>
        </form>
    </div>
</div>
@endsection


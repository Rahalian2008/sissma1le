@extends('layouts.app')

@section('title', 'Mutasi & Perubahan Status Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-600 text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </span>
                Mutasi Peserta Didik
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Buku kendali mutasi masuk/keluar, penerbitan surat pindah sekolah, dan pengesahan status kelulusan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('mutations.print-book', ['type' => request('type')]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Mutasi
            </a>
            <button onclick="document.getElementById('modal-mutation').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Mutasi Siswa
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
            <span class="text-xs text-slate-500 font-medium">Total Catatan Mutasi</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total'] ?? $mutations->total()) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-emerald-100 shadow-2xs">
            <span class="text-xs text-emerald-600 font-medium">Siswa Masuk (Pindahan)</span>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['masuk'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-amber-100 shadow-2xs">
            <span class="text-xs text-amber-600 font-medium">Siswa Keluar / Pindah</span>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ number_format($stats['keluar'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-blue-100 shadow-2xs">
            <span class="text-xs text-blue-600 font-medium">Tamat / Lulus</span>
            <div class="text-2xl font-black text-blue-700 mt-1">{{ number_format($stats['lulus'] ?? 0) }}</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('mutations.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3 text-xs">
            <select name="type" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                <option value="">Semua Jenis Mutasi</option>
                <option value="Siswa Masuk" {{ request('type') === 'Siswa Masuk' ? 'selected' : '' }}>Siswa Masuk (Pindahan)</option>
                <option value="Pindah Sekolah" {{ request('type') === 'Pindah Sekolah' || request('type') === 'Siswa Keluar' ? 'selected' : '' }}>Pindah Sekolah (Keluar)</option>
                <option value="Mengundurkan Diri" {{ request('type') === 'Mengundurkan Diri' ? 'selected' : '' }}>Mengundurkan Diri</option>
                <option value="Lulus" {{ request('type') === 'Lulus' ? 'selected' : '' }}>Lulus</option>
            </select>

            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No Surat / Sekolah..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-xl text-xs bg-slate-50">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </form>
    </div>

    <!-- Mutations Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">Jenis Mutasi</th>
                        <th class="px-5 py-3.5">No. Surat Mutasi</th>
                        <th class="px-5 py-3.5">Sekolah Asal / Tujuan</th>
                        <th class="px-5 py-3.5">Alasan</th>
                        <th class="px-5 py-3.5 text-right">Aksi & Surat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mutations as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $m->mutation_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $m->student?->name ?? 'Siswa tidak ditemukan' }}
                                <div class="text-[11px] text-slate-400 font-mono font-normal">NIS: {{ $m->student?->nis }} &bull; {{ $m->student?->current_rombel ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if(str_contains($m->mutation_type, 'Masuk')) bg-emerald-100 text-emerald-800
                                    @elseif(str_contains($m->mutation_type, 'Lulus')) bg-blue-100 text-blue-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $m->mutation_type }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-700 font-semibold">
                                {{ $m->document_number ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-800">
                                {{ $m->school_target_or_origin ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $m->reason ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('mutations.print-letter', $m) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg text-slate-600 hover:text-blue-700 hover:bg-blue-50 border border-slate-200 transition text-[11px] font-semibold" title="Cetak Surat Pindah Resmi">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Surat Pindah
                                </a>
                                <button type="button" onclick="openEditMutation({{ json_encode([
                                    'id' => $m->id,
                                    'student_name' => $m->student?->name,
                                    'mutation_type' => $m->mutation_type,
                                    'mutation_date' => $m->mutation_date?->format('Y-m-d'),
                                    'document_number' => $m->document_number,
                                    'school_target_or_origin' => $m->school_target_or_origin,
                                    'reason' => $m->reason,
                                    'notes' => $m->notes,
                                ]) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Edit Mutasi">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('mutations.destroy', $m) }}" method="POST" onsubmit="return confirm('Hapus catatan mutasi ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus Mutasi">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada riwayat mutasi siswa yang sesuai kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($mutations->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $mutations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Catat Mutasi -->
<div id="modal-mutation" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Catat Mutasi Siswa</h2>
        <p class="text-xs text-slate-500 mb-4">Pilih siswa dan tentukan jenis perpindahan.</p>

        <form action="{{ route('mutations.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Pilih Peserta Didik --</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (NIS: {{ $s->nis }} - Kelas {{ $s->current_class_level }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Mutasi <span class="text-rose-500">*</span></label>
                    <select name="mutation_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Pindah Sekolah">Pindah Sekolah (Keluar)</option>
                        <option value="Siswa Keluar">Siswa Keluar</option>
                        <option value="Mengundurkan Diri">Mengundurkan Diri</option>
                        <option value="Lulus">Lulus</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Mutasi <span class="text-rose-500">*</span></label>
                    <input type="date" name="mutation_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Keputusan / Surat Pindah</label>
                <input type="text" name="document_number" placeholder="Contoh: 421/045/SMA1/2026" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Sekolah Tujuan / Asal</label>
                <input type="text" name="school_target_or_origin" placeholder="Nama sekolah tujuan pindah..." class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alasan Perpindahan</label>
                <textarea name="reason" rows="2" placeholder="Alasan orang tua pindah tugas, domisili, dsb..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-mutation').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Mutasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Mutasi -->
<div id="modal-edit-mutation" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 id="edit-mut-title" class="text-base font-bold text-slate-900 mb-1">Edit Catatan Mutasi</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui tanggal, jenis, atau nomor surat mutasi siswa.</p>

        <form id="form-edit-mutation" method="POST" class="space-y-3.5 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Mutasi <span class="text-rose-500">*</span></label>
                    <select id="edit-mutation-type" name="mutation_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Pindah Sekolah">Pindah Sekolah (Keluar)</option>
                        <option value="Siswa Keluar">Siswa Keluar</option>
                        <option value="Mengundurkan Diri">Mengundurkan Diri</option>
                        <option value="Lulus">Lulus</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Mutasi <span class="text-rose-500">*</span></label>
                    <input type="date" id="edit-mutation-date" name="mutation_date" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Keputusan / Surat Pindah</label>
                <input type="text" id="edit-document-number" name="document_number" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Sekolah Tujuan / Asal</label>
                <input type="text" id="edit-school-target" name="school_target_or_origin" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alasan Perpindahan</label>
                <textarea id="edit-mutation-reason" name="reason" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-mutation').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditMutation(data) {
    document.getElementById('form-edit-mutation').action = '/mutations/' + data.id;
    document.getElementById('edit-mut-title').innerText = 'Edit Mutasi Siswa: ' + data.student_name;
    document.getElementById('edit-mutation-type').value = data.mutation_type || 'Pindah Sekolah';
    document.getElementById('edit-mutation-date').value = data.mutation_date || '';
    document.getElementById('edit-document-number').value = data.document_number || '';
    document.getElementById('edit-school-target').value = data.school_target_or_origin || '';
    document.getElementById('edit-mutation-reason').value = data.reason || '';
    document.getElementById('modal-edit-mutation').classList.remove('hidden');
}
</script>
@endsection

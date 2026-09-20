@extends('layouts.app')

@section('title', 'Data Kelas - Master Data SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-bold">
                MASTER DATA SEKOLAH
            </span>
            <h1 class="text-xl sm:text-3xl font-black text-white mt-2">Data Rombongan Belajar (Kelas)</h1>
            <p class="text-xs text-slate-300 mt-1">Kelola rombel kelas, tingkat tingkatan (X, XI, XII), wali kelas pendamping, dan alokasi tahun pelajaran.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <button onclick="document.getElementById('importClassModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Impor Kelas
            </button>
            <button onclick="document.getElementById('addClassModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Tambah Kelas Baru
            </button>
        </div>
    </div>

    <!-- Table of Classes -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">Daftar Seluruh Rombel Kelas Aktif</h2>
            <span class="px-3 py-1 bg-slate-100 text-slate-700 font-bold text-xs rounded-full">
                {{ $classes->count() }} Rombel Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Nama Rombel</th>
                        <th class="p-4">Tingkat</th>
                        <th class="p-4">Konsentrasi / Jurusan</th>
                        <th class="p-4">Wali Kelas</th>
                        <th class="p-4">Tahun Pelajaran</th>
                        <th class="p-4 text-center">Jumlah Siswa</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($classes as $c)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-black text-slate-800 text-sm">
                                Kelas {{ $c->name }}
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-lg border border-indigo-100">
                                    Kelas {{ $c->grade }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-600 font-medium">
                                {{ $c->major ?? 'Reguler / Umum' }}
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $c->homeroomTeacher->name ?? '-' }}</div>
                                @if($c->homeroomTeacher?->nip)
                                    <div class="text-[10px] text-slate-400 font-mono">NIP: {{ $c->homeroomTeacher->nip }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-600">
                                {{ $c->academicYear->name ?? '-' }} ({{ $c->academicYear->semester ?? '-' }})
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-800 font-bold rounded-lg">
                                    {{ $c->students->count() }} Siswa
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="editClass({{ json_encode($c) }})" class="p-1.5 bg-slate-100 hover:bg-indigo-50 text-indigo-600 rounded-lg transition" title="Edit Rombel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.classes.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus rombel kelas {{ $c->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 text-rose-600 rounded-lg transition" title="Hapus Rombel">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Belum ada rombel kelas yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Impor Data Kelas -->
<div id="importClassModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Impor Rombel Kelas</h3>
                    <p class="text-[11px] text-slate-400">Unggah file CSV daftar rombel kelas sekolah</p>
                </div>
            </div>
            <button onclick="document.getElementById('importClassModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span>Format File & Panduan:</span>
                <a href="{{ route('master.classes.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-600 hover:bg-sky-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Kolom wajib: <code class="bg-white px-1.5 py-0.5 rounded text-sky-700 font-mono">nama_kelas</code> (contoh: X-1, XI-MIPA-1), <code class="bg-white px-1.5 py-0.5 rounded text-sky-700 font-mono">tingkat</code> (X/XI/XII).
                Kolom opsional: <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">jurusan</code> (UMUM/IPA/IPS/BAHASA), <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">nip_wali_kelas</code>.
            </p>
        </div>

        <form action="{{ route('master.classes.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-600 file:text-white hover:file:bg-sky-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importClassModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-bold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div id="addClassModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Tambah Rombel Kelas Baru</h3>
            <button onclick="document.getElementById('addClassModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('master.classes.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Rombel Kelas <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: X-A, XI-IPA 1, XII-IPS"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tingkat <span class="text-rose-500">*</span></label>
                    <select name="grade" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                        <option value="X">Kelas X (10)</option>
                        <option value="XI">Kelas XI (11)</option>
                        <option value="XII">Kelas XII (12)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jurusan / Peminatan</label>
                    <input type="text" name="major" placeholder="Contoh: MIPA, IPS, Umum"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Wali Kelas Pendamping</label>
                <select name="homeroom_teacher_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                    <option value="">-- Belum Ditentukan --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} (NIP: {{ $t->nip }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                <select name="academic_year_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>{{ $ay->name }} - Semester {{ $ay->semester }} {{ $ay->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addClassModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-xs">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kelas -->
<div id="editClassModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Edit Data Rombel Kelas</h3>
            <button onclick="document.getElementById('editClassModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="editClassForm" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Rombel Kelas <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_name" required
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tingkat <span class="text-rose-500">*</span></label>
                    <select name="grade" id="edit_grade" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                        <option value="X">Kelas X (10)</option>
                        <option value="XI">Kelas XI (11)</option>
                        <option value="XII">Kelas XII (12)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jurusan / Peminatan</label>
                    <input type="text" name="major" id="edit_major"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Wali Kelas Pendamping</label>
                <select name="homeroom_teacher_id" id="edit_homeroom_teacher_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                    <option value="">-- Belum Ditentukan --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} (NIP: {{ $t->nip }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                <select name="academic_year_id" id="edit_academic_year_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}">{{ $ay->name }} - Semester {{ $ay->semester }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editClassModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editClass(data) {
        document.getElementById('editClassForm').action = '/master/classes/' + data.id;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_grade').value = data.grade;
        document.getElementById('edit_major').value = data.major || '';
        document.getElementById('edit_homeroom_teacher_id').value = data.homeroom_teacher_id || '';
        document.getElementById('edit_academic_year_id').value = data.academic_year_id;
        document.getElementById('editClassModal').classList.remove('hidden');
    }
</script>
@endsection

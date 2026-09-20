@extends('layouts.app')

@section('title', 'Data Siswa - Master Data SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-sky-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-full text-xs font-bold">
                MASTER DATA SEKOLAH
            </span>
            <h1 class="text-xl sm:text-3xl font-black text-white mt-2">Data Induk Siswa & Kartu RFID</h1>
            <p class="text-xs text-slate-300 mt-1">Kelola data identitas siswa, nomor induk, penetapan rombel, dan penetapan UID kartu presensi RFID.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <button onclick="document.getElementById('importStudentModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Impor Siswa
            </button>
            <button onclick="document.getElementById('addStudentModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                + Tambah Siswa Baru
            </button>
        </div>
    </div>

    <!-- Filter & Live Typing Search Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('master.students') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Cari Siswa Cepat (Ketik Nama / NIS / NISN / RFID)</label>
                <div class="relative">
                    <input type="text" name="search" id="studentSearchInput" value="{{ $search }}"
                        placeholder="Ketik untuk menyaring nama, NIS, NISN, atau UID kartu..."
                        class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-sky-500 focus:bg-white text-xs transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Filter Rombel Kelas</label>
                <div class="flex gap-2">
                    <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-sky-500 text-xs">
                        <option value="">Semua Rombel</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                    @if($search || $classId)
                        <a href="{{ route('master.students') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold flex items-center justify-center">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table of Students -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Siswa SMA Negeri 1 Lengkong</h2>
                <span class="table-swipe-cue">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Geser Tabel →
                </span>
            </div>
            <span class="px-3 py-1 bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 font-bold text-xs rounded-full self-start sm:self-auto">
                Total {{ $students->total() }} Siswa
            </span>
        </div>

        <div class="responsive-table-container">
            <table class="w-full text-xs text-left" id="studentMasterTable">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3">Siswa</th>
                        <th class="p-3">NIS / NISN</th>
                        <th class="p-3">Gender</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Tahun Pelajaran</th>
                        <th class="p-3">UID Kartu RFID</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $s)
                        <tr class="hover:bg-slate-50 transition student-row" data-search="{{ strtolower($s->name . ' ' . $s->nis . ' ' . $s->nisn . ' ' . $s->rfid_uid . ' ' . ($s->schoolClass->name ?? '')) }}">
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $s->name }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $s->address ?? 'Alamat belum diatur' }}</div>
                            </td>
                            <td class="p-3 font-mono">
                                <div class="font-bold text-slate-700">NIS: {{ $s->nis }}</div>
                                <div class="text-[10px] text-slate-400">NISN: {{ $s->nisn }}</div>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $s->gender === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $s->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-bold rounded-lg">
                                    {{ $s->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600">
                                {{ $s->academicYear->name ?? '-' }}
                            </td>
                            <td class="p-3 font-mono">
                                @if($s->rfid_uid)
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md font-bold text-[10px]">
                                        {{ $s->rfid_uid }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px] italic">Belum assign RFID</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="editStudent({{ json_encode($s) }})" class="p-1.5 bg-slate-100 hover:bg-sky-50 text-sky-600 rounded-lg transition" title="Edit Siswa & RFID">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.students.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus siswa {{ $s->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 text-rose-600 rounded-lg transition" title="Hapus Siswa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                Tidak ada data siswa yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Impor Data Siswa -->
<div id="importStudentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Impor Massal Data Siswa</h3>
                    <p class="text-[11px] text-slate-400">Unggah file CSV / Excel data siswa</p>
                </div>
            </div>
            <button onclick="document.getElementById('importStudentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span>Format File & Panduan:</span>
                <a href="{{ route('master.students.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Kolom wajib: <code class="bg-white px-1.5 py-0.5 rounded text-indigo-600 font-mono">nis</code>, <code class="bg-white px-1.5 py-0.5 rounded text-indigo-600 font-mono">nama_lengkap</code>, <code class="bg-white px-1.5 py-0.5 rounded text-indigo-600 font-mono">jenis_kelamin</code> (L/P), <code class="bg-white px-1.5 py-0.5 rounded text-indigo-600 font-mono">kelas</code>. Kolom opsional: <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">nisn</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">no_hp</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">alamat</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">rfid_uid</code>.
            </p>
            <p class="text-[11px] text-emerald-700 bg-emerald-50 p-2 rounded-xl font-medium">
                Setiap siswa yang diimpor akan otomatis dibuatkan akun login dengan username NIS dan password default <code class="font-bold">password</code>.
            </p>
        </div>

        <form action="{{ route('master.students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importStudentModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-bold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div id="addStudentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Tambah Data Siswa Baru</h3>
            <button onclick="document.getElementById('addStudentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('master.students.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Nama lengkap..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-sky-500 font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIS <span class="text-rose-500">*</span></label>
                    <input type="text" name="nis" required placeholder="Contoh: 12050" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NISN <span class="text-rose-500">*</span></label>
                    <input type="text" name="nisn" required placeholder="10 digit NISN..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-mono">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Rombel Kelas <span class="text-rose-500">*</span></label>
                    <select name="class_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                    <select name="academic_year_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>{{ $ay->name }} ({{ $ay->semester }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">UID Kartu RFID</label>
                    <input type="text" name="rfid_uid" placeholder="Contoh: E2801170..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-mono text-emerald-700 font-bold">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor HP / WhatsApp</label>
                <input type="text" name="phone" placeholder="08..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Alamat Tempat Tinggal</label>
                <textarea name="address" rows="2" placeholder="Alamat..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none"></textarea>
            </div>
            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addStudentModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl shadow-xs">Simpan Siswa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Siswa -->
<div id="editStudentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Edit Data Siswa & RFID</h3>
            <button onclick="document.getElementById('editStudentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="editStudentForm" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_student_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIS <span class="text-rose-500">*</span></label>
                    <input type="text" name="nis" id="edit_student_nis" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NISN <span class="text-rose-500">*</span></label>
                    <input type="text" name="nisn" id="edit_student_nisn" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-mono">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" id="edit_student_gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Rombel Kelas <span class="text-rose-500">*</span></label>
                    <select name="class_id" id="edit_student_class_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                    <select name="academic_year_id" id="edit_student_academic_year_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}">{{ $ay->name }} ({{ $ay->semester }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">UID Kartu RFID</label>
                    <input type="text" name="rfid_uid" id="edit_student_rfid_uid" placeholder="Assign UID..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-mono text-emerald-700 font-bold">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor HP / WA</label>
                    <input type="text" name="phone" id="edit_student_phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Keaktifan</label>
                    <select name="is_active" id="edit_student_is_active" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Alamat</label>
                <textarea name="address" id="edit_student_address" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none"></textarea>
            </div>
            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editStudentModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Live typing instant filter for student rows on current page
    document.getElementById('studentSearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.student-row').forEach(row => {
            const text = row.getAttribute('data-search') || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    function editStudent(s) {
        document.getElementById('editStudentForm').action = '/master/students/' + s.id;
        document.getElementById('edit_student_name').value = s.name;
        document.getElementById('edit_student_nis').value = s.nis;
        document.getElementById('edit_student_nisn').value = s.nisn;
        document.getElementById('edit_student_gender').value = s.gender;
        document.getElementById('edit_student_class_id').value = s.class_id;
        document.getElementById('edit_student_academic_year_id').value = s.academic_year_id;
        document.getElementById('edit_student_rfid_uid').value = s.rfid_uid || '';
        document.getElementById('edit_student_phone').value = s.phone || '';
        document.getElementById('edit_student_address').value = s.address || '';
        document.getElementById('edit_student_is_active').value = s.is_active ? '1' : '0';
        document.getElementById('editStudentModal').classList.remove('hidden');
    }
</script>
@endsection

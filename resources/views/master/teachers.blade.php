@extends('layouts.app')

@section('title', 'Data Guru & Role Akun - Master Data SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold">
                MASTER DATA SEKOLAH
            </span>
            <h1 class="text-xl sm:text-3xl font-black text-white mt-2">Data Guru & Penetapan Peran Akun</h1>
            <p class="text-xs text-slate-300 mt-1">Kelola data tenaga pendidik, informasi kepegawaian (NIP), dan hak akses peran sistem (Guru, Wali Kelas, BK, Kesiswaan, Admin).</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <button onclick="document.getElementById('importTeacherModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Impor Guru
            </button>
            <button onclick="document.getElementById('addTeacherModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                + Tambah Guru & Akun
            </button>
        </div>
    </div>

    <!-- Live Search by Typing -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('master.teachers') }}" class="flex gap-3 text-xs">
            <div class="relative flex-1">
                <input type="text" name="search" id="teacherSearchInput" value="{{ $search }}"
                    placeholder="Ketik untuk menyaring nama guru, NIP, gelar, email, atau username akun login..."
                    class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500 focus:bg-white text-xs transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            @if($search)
                <a href="{{ route('master.teachers') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold flex items-center justify-center">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table of Teachers -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">Daftar Tenaga Pendidik & Hak Akses Akun</h2>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
                {{ $teachers->count() }} Guru Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" id="teacherMasterTable">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3">Guru / Pegawai</th>
                        <th class="p-3">NIP</th>
                        <th class="p-3">Jabatan / Mapel</th>
                        <th class="p-3">Akun Login (Username & Email)</th>
                        <th class="p-3 text-center">Peran Akun Sistem</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachers as $t)
                        <tr class="hover:bg-slate-50 transition teacher-row" data-search="{{ strtolower($t->name . ' ' . $t->nip . ' ' . $t->title . ' ' . ($t->user->username ?? '') . ' ' . ($t->user->email ?? '') . ' ' . ($t->user->role ?? '')) }}">
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $t->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">No. Telp: {{ $t->phone ?? '-' }}</div>
                            </td>
                            <td class="p-3 font-mono font-semibold text-slate-700">
                                {{ $t->nip }}
                            </td>
                            <td class="p-3 text-slate-600">
                                <span class="font-medium text-slate-800">{{ $t->title ?? 'Guru Pengajar' }}</span>
                                @if($t->homeroomClass)
                                    <div class="text-[10px] text-indigo-600 font-bold mt-0.5">Wali Kelas {{ $t->homeroomClass->name }}</div>
                                @endif
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-700">{{ $t->user->username ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $t->user->email ?? '-' }}</div>
                            </td>
                            <td class="p-3 text-center">
                                @php
                                    $role = $t->user->role ?? 'guru';
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider
                                    {{ match($role) {
                                        'super_admin' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                        'admin' => 'bg-red-100 text-red-800 border border-red-200',
                                        'kepala_sekolah' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                        'wali_kelas' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                        default => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    } }}">
                                    {{ str_replace('_', ' ', $role) }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $t->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="editTeacher({{ json_encode($t) }})" class="p-1.5 bg-slate-100 hover:bg-emerald-50 text-emerald-600 rounded-lg transition" title="Edit Data & Role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.teachers.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data guru {{ $t->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 text-rose-600 rounded-lg transition" title="Hapus Guru">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Tidak ada data guru yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Impor Data Guru -->
<div id="importTeacherModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Impor Massal Data Guru & Akun</h3>
                    <p class="text-[11px] text-slate-400">Unggah file CSV data guru & buat akun login otomatis</p>
                </div>
            </div>
            <button onclick="document.getElementById('importTeacherModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span>Format File & Panduan:</span>
                <a href="{{ route('master.teachers.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-600 hover:bg-teal-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Kolom wajib: <code class="bg-white px-1.5 py-0.5 rounded text-teal-700 font-mono">nip</code>, <code class="bg-white px-1.5 py-0.5 rounded text-teal-700 font-mono">nama_lengkap</code>, <code class="bg-white px-1.5 py-0.5 rounded text-teal-700 font-mono">jenis_kelamin</code> (L/P), <code class="bg-white px-1.5 py-0.5 rounded text-teal-700 font-mono">peran</code> (guru/wali_kelas/bk/kesiswaan/admin).
                Kolom opsional: <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">mapel_jabatan</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">no_hp</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">email</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">username</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">password</code>.
            </p>
            <p class="text-[11px] text-teal-800 bg-teal-50 p-2 rounded-xl font-medium">
                Jika kolom password dikosongkan, kata sandi bawaan login adalah <code class="font-bold">password</code>.
            </p>
        </div>

        <form action="{{ route('master.teachers.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importTeacherModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-bold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Guru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Guru -->
<div id="addTeacherModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Tambah Data Guru & Buat Akun Login</h3>
            <button onclick="document.getElementById('addTeacherModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('master.teachers.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Drs. Bambang Supriyadi, M.Pd." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIP <span class="text-rose-500">*</span></label>
                    <input type="text" name="nip" required placeholder="Contoh: 198001..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jabatan / Mapel</label>
                    <input type="text" name="title" placeholder="Contoh: Guru Matematika" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon / WA</label>
                    <input type="text" name="phone" placeholder="08..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
            </div>
            <div class="p-3 bg-emerald-50/70 border border-emerald-200/60 rounded-2xl space-y-3">
                <span class="font-bold text-emerald-900 text-xs block">Pengaturan Akun Login Sistem:</span>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peran Akun (Role) <span class="text-rose-500">*</span></label>
                    <select name="role" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none font-bold text-emerald-800">
                        <option value="guru">Guru Pengajar (Default)</option>
                        <option value="wali_kelas">Wali Kelas</option>
                        <option value="kepala_sekolah">Kepala Sekolah (Monitoring Eksekutif)</option>
                        <option value="admin">Administrator Sekolah</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Username Login</label>
                        <input type="text" name="username" placeholder="Otomatis NIP jika kosong" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="email@sekolah.sch.id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Password Awal</label>
                    <input type="password" name="password" placeholder="Otomatis NIP jika kosong" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none">
                </div>
            </div>
            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addTeacherModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-xs">Simpan Guru & Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Guru -->
<div id="editTeacherModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Edit Data Guru & Role Akun</h3>
            <button onclick="document.getElementById('editTeacherModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="editTeacherForm" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_teacher_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIP <span class="text-rose-500">*</span></label>
                    <input type="text" name="nip" id="edit_teacher_nip" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" id="edit_teacher_gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jabatan / Mapel</label>
                    <input type="text" name="title" id="edit_teacher_title" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon / WA</label>
                    <input type="text" name="phone" id="edit_teacher_phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
            </div>
            <div class="p-3 bg-emerald-50/70 border border-emerald-200/60 rounded-2xl space-y-3">
                <span class="font-bold text-emerald-900 text-xs block">Pengaturan Akun Login Sistem:</span>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peran Akun Login (Role) <span class="text-rose-500">*</span></label>
                    <select name="role" id="edit_teacher_role" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none font-bold text-emerald-800">
                        <option value="guru">Guru Pengajar</option>
                        <option value="wali_kelas">Wali Kelas</option>
                        <option value="kepala_sekolah">Kepala Sekolah (Monitoring Eksekutif)</option>
                        <option value="admin">Administrator Sekolah</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Akun <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="edit_teacher_email" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ubah Password (Kosongkan jika tidak diganti)</label>
                    <input type="password" name="password" placeholder="Password baru..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Keaktifan</label>
                <select name="is_active" id="edit_teacher_is_active" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editTeacherModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Live typing instant filter for teacher rows
    document.getElementById('teacherSearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.teacher-row').forEach(row => {
            const text = row.getAttribute('data-search') || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    function editTeacher(t) {
        document.getElementById('editTeacherForm').action = '/master/teachers/' + t.id;
        document.getElementById('edit_teacher_name').value = t.name;
        document.getElementById('edit_teacher_nip').value = t.nip;
        document.getElementById('edit_teacher_gender').value = t.gender;
        document.getElementById('edit_teacher_title').value = t.title || '';
        document.getElementById('edit_teacher_phone').value = t.phone || '';
        document.getElementById('edit_teacher_role').value = t.user ? t.user.role : 'guru';
        document.getElementById('edit_teacher_email').value = t.user ? t.user.email : '';
        document.getElementById('edit_teacher_is_active').value = t.is_active ? '1' : '0';
        document.getElementById('editTeacherModal').classList.remove('hidden');
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'Data Siswa - Master Data SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    @include('master.partials.nav')
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
            <a href="{{ route('attendance.cards.index') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Cetak Kartu Siswa (RFID/QR)
            </a>
            <form action="{{ route('master.students.sync_dapodik') }}" method="POST" class="inline" onsubmit="return confirm('Mulai sinkronisasi data identitas siswa dari file Dapodik Download?')">
                @csrf
                <button type="submit" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer" title="Sinkronkan data identitas siswa (TTL, NIK, Agama, Alamat, Ortu, Sekolah Asal) dari file Dapodik">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Sinkronkan Dapodik</span>
                </button>
            </form>
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
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('master.students') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
            <div class="md:col-span-2">
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Siswa Cepat (Ketik Nama / Inisial / NIS / NISN / RFID)</label>
                <div class="relative">
                    <input type="text" name="search" id="studentSearchInput" value="{{ $search }}"
                        placeholder="Ketik untuk menyaring nama, inisial, NIS, NISN, atau UID kartu..."
                        class="w-full pl-9 pr-8 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-sky-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if($search)
                        <button type="button" onclick="clearStudentMasterSearch()" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold">✕</button>
                    @endif
                </div>

                <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                <div class="mt-2 flex items-center gap-1 overflow-x-auto pb-1 pt-0.5 text-xs no-scrollbar">
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 shrink-0 mr-1">Inisial:</span>
                    <button type="button" onclick="filterMasterStudentByInitial('')"
                        class="px-2 py-0.5 rounded-lg {{ empty($search) ? 'bg-sky-600 text-white font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700' }} text-[11px] shrink-0 transition shadow-xs">
                        Semua
                    </button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterMasterStudentByInitial('{{ $char }}')"
                            class="px-2 py-0.5 rounded-lg {{ strtoupper($search ?? '') === $char ? 'bg-sky-600 text-white font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700' }} text-[11px] shrink-0 transition">
                            {{ $char }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Filter Tahun Pelajaran</label>
                <div class="flex gap-2">
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-sky-500 text-xs text-slate-800 dark:text-slate-200">
                        <option value="">Semua Tahun</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $academicYearId == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Filter Rombel Kelas</label>
                <div class="flex gap-2">
                    <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-sky-500 text-xs text-slate-800 dark:text-slate-200">
                        <option value="">Semua Rombel</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                    @if($search || $classId || $academicYearId)
                        <a href="{{ route('master.students') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold flex items-center justify-center transition">Reset</a>
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
                        <th class="p-3">Siswa & Identitas</th>
                        <th class="p-3">NIS / NISN / NIK</th>
                        <th class="p-3">Gender & Agama</th>
                        <th class="p-3">Kelas & Asal Sekolah</th>
                        <th class="p-3">Alamat Lengkap</th>
                        <th class="p-3">UID Kartu RFID</th>
                        <th class="p-3 text-center">Face Recognition</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $s)
                        <tr class="hover:bg-slate-50 transition student-row" data-search="{{ strtolower($s->name . ' ' . ($s->initials ?? '') . ' ' . $s->nis . ' ' . $s->nisn . ' ' . ($s->nik ?? '') . ' ' . $s->rfid_uid . ' ' . ($s->schoolClass->name ?? '') . ' ' . ($s->religion ?? '') . ' ' . ($s->previous_school ?? '') . ' ' . ($s->address ?? '')) }}">
                            <td class="p-3">
                                <div class="font-bold text-slate-800 dark:text-slate-100 text-sm flex items-center gap-1.5">
                                    <span>{{ $s->name }}</span>
                                    <button type="button" onclick="viewStudentIdentity({{ json_encode($s) }})" class="text-sky-600 hover:text-sky-700 transition" title="Lihat Profil & Identitas Lengkap">
                                        <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </div>
                                @if($s->birth_place || $s->birth_date)
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5 flex items-center gap-1">
                                        <span>🎂</span>
                                        <span>{{ $s->birth_place ? $s->birth_place . ', ' : '' }}{{ $s->birth_date ? $s->birth_date->format('d/m/Y') : '-' }}</span>
                                    </div>
                                @endif
                                @if($s->father_name || $s->mother_name)
                                    <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-xs" title="Ayah: {{ $s->father_name ?: '-' }} | Ibu: {{ $s->mother_name ?: '-' }}">
                                        👨‍👩‍👦 {{ $s->father_name ?: '-' }} / {{ $s->mother_name ?: '-' }}
                                    </div>
                                @endif
                                @if($s->user && ($s->user->role === 'sekretaris_kelas' || in_array('sekretaris_kelas', (array)($s->user->roles ?? []))))
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[9px] font-black bg-indigo-100 text-indigo-700 border border-indigo-200 uppercase tracking-wide">Sekretaris Kelas</span>
                                @endif
                                @php
                                    $extraSecPivot = $s->extracurriculars->first(function($ex) {
                                        return in_array($ex->pivot->role ?? '', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true);
                                    });
                                @endphp
                                @if(($s->user && ($s->user->role === 'sekretaris_ekstra' || in_array('sekretaris_ekstra', (array)($s->user->roles ?? [])))) || $extraSecPivot)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[9px] font-black bg-cyan-100 text-cyan-700 border border-cyan-200 uppercase tracking-wide">
                                        Sekretaris Ekskul{{ $extraSecPivot ? ': ' . $extraSecPivot->name : '' }}
                                    </span>
                                @endif
                                @if($s->extracurriculars->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($s->extracurriculars as $stEx)
                                            @php
                                                $role = $stEx->pivot->role ?? 'Anggota';
                                                $isSec = in_array($role, ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true);
                                            @endphp
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold {{ $isSec ? 'bg-cyan-100 text-cyan-800 border border-cyan-300 dark:bg-cyan-950/60 dark:text-cyan-300 dark:border-cyan-700' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
                                                ⚡ {{ $stEx->name }}{{ $role !== 'Anggota' ? ' (' . $role . ')' : '' }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="p-3 font-mono">
                                <div class="font-bold text-slate-700 dark:text-slate-300">NIS: {{ $s->nis }}</div>
                                <div class="text-[10px] text-slate-400">NISN: {{ $s->nisn }}</div>
                                @if($s->nik)
                                    <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">NIK: {{ $s->nik }}</div>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $s->gender === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $s->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                                @if($s->religion)
                                    <span class="block mt-1 text-[10px] font-semibold text-slate-600 dark:text-slate-400">
                                        Agama: <strong>{{ $s->religion }}</strong>
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-lg inline-block">
                                    {{ $s->schoolClass->name ?? '-' }}
                                </span>
                                @if($s->previous_school)
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 truncate max-w-[150px]" title="Sekolah Asal: {{ $s->previous_school }}">
                                        🏫 {{ $s->previous_school }}
                                    </div>
                                @endif
                                <div class="text-[9px] text-slate-400 mt-0.5">{{ $s->academicYear->name ?? '-' }}</div>
                            </td>
                            <td class="p-3">
                                @if($s->address)
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300 max-w-xs leading-relaxed line-clamp-2" title="{{ $s->address }}">
                                        📍 {{ $s->address }}
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[10px] italic">Alamat belum diatur</span>
                                @endif
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
                            <td class="p-3 text-center" id="face-cell-{{ $s->id }}">
                                @if($s->hasFaceRegistered())
                                    <div class="inline-flex items-center gap-2">
                                        @if($s->face_photo_url)
                                            <img src="{{ $s->face_photo_url }}" alt="{{ $s->name }}" class="w-8 h-8 rounded-full object-cover border-2 border-emerald-500 shadow-xs shrink-0">
                                        @else
                                            <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">👤</span>
                                        @endif
                                        <div class="text-left">
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md font-bold text-[10px] inline-flex items-center gap-1">
                                                ✓ Terdaftar
                                            </span>
                                            @if($s->face_registered_at)
                                                <div class="text-[9px] text-slate-400 mt-0.5">{{ $s->face_registered_at->format('d/m/Y') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg font-bold text-[10px] inline-flex items-center gap-1">
                                        Belum Ada
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="viewStudentIdentity({{ json_encode($s) }})" class="p-1.5 bg-slate-100 hover:bg-sky-50 text-sky-600 rounded-lg transition cursor-pointer" title="Lihat Identitas Siswa Lengkap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button type="button" onclick="openFaceEnrollmentModal({{ json_encode([
                                        'id' => $s->id,
                                        'name' => $s->name,
                                        'nis' => $s->nis,
                                        'nisn' => $s->nisn,
                                        'class_name' => $s->schoolClass->name ?? '-',
                                        'has_face' => $s->hasFaceRegistered(),
                                        'face_url' => $s->face_photo_url,
                                        'registered_at' => $s->face_registered_at?->format('d/m/Y H:i'),
                                    ]) }})" class="p-1.5 {{ $s->hasFaceRegistered() ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' : 'bg-slate-100 hover:bg-indigo-50 text-indigo-600' }} rounded-lg transition cursor-pointer" title="Rekam / Ubah Wajah Siswa (Face Recognition)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                    <a href="{{ route('attendance.cards.single', $s->id) }}" target="_blank" class="p-1.5 bg-slate-100 hover:bg-indigo-50 text-indigo-600 rounded-lg transition" title="Cetak Kartu Absensi (RFID & QR)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </a>
                                    <button onclick="editStudent({{ json_encode($s) }})" class="p-1.5 bg-slate-100 hover:bg-sky-50 text-sky-600 rounded-lg transition cursor-pointer" title="Edit Siswa & RFID">
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
                            <td colspan="9" class="p-8 text-center text-slate-400">
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
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Impor Massal Data Siswa</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Unggah file CSV / Excel data siswa</p>
                </div>
            </div>
            <button onclick="document.getElementById('importStudentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700 dark:text-slate-200">
                <span>Format File & Panduan:</span>
                <a href="{{ route('master.students.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                Kolom wajib: <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-indigo-600 dark:text-indigo-400 font-mono">nis</code>, <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-indigo-600 dark:text-indigo-400 font-mono">nama_lengkap</code>, <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-indigo-600 dark:text-indigo-400 font-mono">jenis_kelamin</code> (L/P), <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-indigo-600 dark:text-indigo-400 font-mono">kelas</code>. Kolom opsional: <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">nisn</code>, <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">no_hp</code>, <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">alamat</code>, <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">rfid_uid</code>.
            </p>
            <p class="text-[11px] text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 p-2 rounded-xl font-medium">
                Setiap siswa yang diimpor akan otomatis dibuatkan akun login dengan username NIS dan password default <code class="font-bold">password</code>.
            </p>
        </div>

        <form action="{{ route('master.students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('importStudentModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-bold transition">
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
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Tambah Data Siswa Baru</h3>
            <button onclick="document.getElementById('addStudentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
        </div>
        <form action="{{ route('master.students.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Nama lengkap..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-sky-500 font-bold text-slate-800 dark:text-slate-100">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIS <span class="text-rose-500">*</span></label>
                    <input type="text" name="nis" required placeholder="Contoh: 12050" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NISN <span class="text-rose-500">*</span></label>
                    <input type="text" name="nisn" required placeholder="10 digit NISN..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono text-slate-800 dark:text-slate-100">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" name="nik" maxlength="20" placeholder="16 digit NIK..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Agama</label>
                    <select name="religion" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <option value="Islam" selected>Islam</option>
                        <option value="Kristen Protestan">Kristen Protestan</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Khonghucu">Khonghucu</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" placeholder="Contoh: Sukabumi" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ayah</label>
                    <input type="text" name="father_name" placeholder="Nama ayah kandung..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ibu</label>
                    <input type="text" name="mother_name" placeholder="Nama ibu kandung..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Sekolah Asal</label>
                <input type="text" name="previous_school" placeholder="Contoh: SMPN 1 Lengkong / MTs..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rombel Kelas <span class="text-rose-500">*</span></label>
                    <select name="class_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                    <select name="academic_year_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">UID Kartu RFID</label>
                    <input type="text" name="rfid_uid" placeholder="Contoh: E2801170..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono text-emerald-700 dark:text-emerald-400 font-bold">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="p-3 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/60 dark:border-indigo-800 rounded-2xl">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs select-none">
                        <input type="checkbox" name="is_secretary" value="1" class="w-4 h-4 rounded text-indigo-600 focus:ring-0">
                        <span>Tunjuk Sebagai <strong>Sekretaris Kelas</strong> (Dapat absen rekan sekelas)</span>
                    </label>
                </div>
                <div class="p-3 bg-cyan-50/70 dark:bg-cyan-950/40 border border-cyan-200/60 dark:border-cyan-800 rounded-2xl">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs select-none">
                        <input type="checkbox" name="is_extra_secretary" id="add_student_is_extra_secretary" value="1" onchange="toggleAddExtraSecretarySelect(this)" class="w-4 h-4 rounded text-cyan-600 focus:ring-0">
                        <span>Tunjuk Sebagai <strong>Sekretaris Ekskul</strong> (Dapat absen anggota ekstra)</span>
                    </label>
                    <div id="add_extra_secretary_wrapper" class="mt-2.5 pt-2 border-t border-cyan-200/60 dark:border-cyan-800/60 hidden">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Pilih Ekstrakurikuler yang Dipegang <span class="text-rose-500">*</span>
                        </label>
                        <select name="extra_secretary_id" id="add_student_extra_secretary_id" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-cyan-300 dark:border-cyan-700 rounded-xl outline-none text-xs text-slate-800 dark:text-slate-200 font-medium">
                            <option value="">-- Pilih Ekstrakurikuler --</option>
                            @foreach($extracurriculars as $ex)
                                <option value="{{ $ex->id }}">{{ $ex->name }} ({{ $ex->category ?? 'Ekskul' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="p-3 bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800 rounded-2xl sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs select-none">
                        <input type="checkbox" name="is_adm_web" value="1" class="w-4 h-4 rounded text-amber-600 focus:ring-0">
                        <span>Tunjuk Sebagai <strong>Admin Web (adm_web)</strong> (Khusus siswa pengelola Berita & Kabar Website Sekolah)</span>
                    </label>
                </div>
            </div>
            @if(isset($extracurriculars) && $extracurriculars->isNotEmpty())
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Keanggotaan Ekstrakurikuler <span class="text-[11px] font-normal text-cyan-700 dark:text-cyan-400 font-semibold">(Siswa bisa mengikuti lebih dari 1 ekskul)</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-800 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-36 overflow-y-auto">
                    @foreach($extracurriculars as $ex)
                    <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700 dark:text-slate-300 hover:text-cyan-700 dark:hover:text-cyan-400">
                        <input type="checkbox" name="extracurricular_ids[]" value="{{ $ex->id }}" class="w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                        <span class="truncate">{{ $ex->name }} <span class="text-[9px] text-slate-400 dark:text-slate-500">({{ $ex->category ?? 'Ekskul' }})</span></span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor HP / WhatsApp</label>
                <input type="text" name="phone" placeholder="08..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Tempat Tinggal</label>
                <textarea name="address" rows="2" placeholder="Alamat..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100"></textarea>
            </div>
            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('addStudentModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl shadow-xs transition">Simpan Siswa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Siswa -->
<div id="editStudentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Edit Data Siswa & RFID</h3>
            <button onclick="document.getElementById('editStudentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
        </div>
        <form id="editStudentForm" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_student_name" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-800 dark:text-slate-100">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIS <span class="text-rose-500">*</span></label>
                    <input type="text" name="nis" id="edit_student_nis" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NISN <span class="text-rose-500">*</span></label>
                    <input type="text" name="nisn" id="edit_student_nisn" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono text-slate-800 dark:text-slate-100">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" name="nik" id="edit_student_nik" maxlength="20" placeholder="16 digit NIK..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Agama</label>
                    <select name="religion" id="edit_student_religion" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <option value="Islam">Islam</option>
                        <option value="Kristen Protestan">Kristen Protestan</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Khonghucu">Khonghucu</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="edit_student_birth_place" placeholder="Tempat lahir..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="edit_student_birth_date" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ayah</label>
                    <input type="text" name="father_name" id="edit_student_father_name" placeholder="Nama ayah..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ibu</label>
                    <input type="text" name="mother_name" id="edit_student_mother_name" placeholder="Nama ibu..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Sekolah Asal</label>
                <input type="text" name="previous_school" id="edit_student_previous_school" placeholder="Asal SMP / MTs..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" id="edit_student_gender" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rombel Kelas <span class="text-rose-500">*</span></label>
                    <select name="class_id" id="edit_student_class_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                    <select name="academic_year_id" id="edit_student_academic_year_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">UID Kartu RFID</label>
                    <input type="text" name="rfid_uid" id="edit_student_rfid_uid" placeholder="Assign UID..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono text-emerald-700 dark:text-emerald-400 font-bold">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="p-3 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/60 dark:border-indigo-800 rounded-2xl">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs select-none">
                        <input type="checkbox" name="is_secretary" value="1" id="edit_student_is_secretary" class="w-4 h-4 rounded text-indigo-600 focus:ring-0">
                        <span>Tunjuk Sebagai <strong>Sekretaris Kelas</strong> (Dapat absen rekan sekelas)</span>
                    </label>
                </div>
                <div class="p-3 bg-cyan-50/70 dark:bg-cyan-950/40 border border-cyan-200/60 dark:border-cyan-800 rounded-2xl">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs select-none">
                        <input type="checkbox" name="is_extra_secretary" value="1" id="edit_student_is_extra_secretary" onchange="toggleEditExtraSecretarySelect(this)" class="w-4 h-4 rounded text-cyan-600 focus:ring-0">
                        <span>Tunjuk Sebagai <strong>Sekretaris Ekskul</strong> (Dapat absen anggota ekstra)</span>
                    </label>
                    <div id="edit_extra_secretary_wrapper" class="mt-2.5 pt-2 border-t border-cyan-200/60 dark:border-cyan-800/60 hidden">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Pilih Ekstrakurikuler yang Dipegang <span class="text-rose-500">*</span>
                        </label>
                        <select name="extra_secretary_id" id="edit_student_extra_secretary_id" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-cyan-300 dark:border-cyan-700 rounded-xl outline-none text-xs text-slate-800 dark:text-slate-200 font-medium">
                            <option value="">-- Pilih Ekstrakurikuler --</option>
                            @foreach($extracurriculars as $ex)
                                <option value="{{ $ex->id }}">{{ $ex->name }} ({{ $ex->category ?? 'Ekskul' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="p-3 bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800 rounded-2xl sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs select-none">
                        <input type="checkbox" name="is_adm_web" value="1" id="edit_student_is_adm_web" class="w-4 h-4 rounded text-amber-600 focus:ring-0">
                        <span>Tunjuk Sebagai <strong>Admin Web (adm_web)</strong> (Khusus siswa pengelola Berita & Kabar Website Sekolah)</span>
                    </label>
                </div>
            </div>
            @if(isset($extracurriculars) && $extracurriculars->isNotEmpty())
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Keanggotaan Ekstrakurikuler <span class="text-[11px] font-normal text-cyan-700 dark:text-cyan-400 font-semibold">(Siswa bisa mengikuti lebih dari 1 ekskul)</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-800 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-36 overflow-y-auto">
                    @foreach($extracurriculars as $ex)
                    <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700 dark:text-slate-300 hover:text-cyan-700 dark:hover:text-cyan-400">
                        <input type="checkbox" name="extracurricular_ids[]" value="{{ $ex->id }}" class="edit-student-extra-cb w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                        <span class="truncate">{{ $ex->name }} <span class="text-[9px] text-slate-400 dark:text-slate-500">({{ $ex->category ?? 'Ekskul' }})</span></span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor HP / WA</label>
                    <input type="text" name="phone" id="edit_student_phone" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Keaktifan</label>
                    <select name="is_active" id="edit_student_is_active" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <!-- Wajah Face Recognition Status & Action -->
            <div class="p-3 bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-200/60 dark:border-indigo-800 rounded-2xl flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div id="edit_student_face_preview_box" class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden border border-indigo-200 dark:border-indigo-700 shadow-2xs">
                        👤
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-slate-100 text-xs">Wajah Face Recognition</div>
                        <div id="edit_student_face_status_text" class="text-[11px] text-slate-500 dark:text-slate-400">Memeriksa status wajah...</div>
                    </div>
                </div>
                <button type="button" id="edit_student_record_face_btn" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <span>📸</span>
                    <span>Rekam Wajah</span>
                </button>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat</label>
                <textarea name="address" id="edit_student_address" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100"></textarea>
            </div>
            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('editStudentModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold cursor-pointer transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl shadow-xs cursor-pointer transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Identitas Lengkap Siswa (Sinkron Dapodik) -->
<div id="viewStudentIdentityModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-xl w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 flex items-center justify-center font-bold text-sm shrink-0">
                    📋
                </div>
                <div>
                    <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Profil & Identitas Lengkap Siswa</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Data Master Siswa Terintegrasi Dapodik Kemendikbudristek</p>
                </div>
            </div>
            <button onclick="document.getElementById('viewStudentIdentityModal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-sm">&times;</button>
        </div>

        <!-- Banner Profil Ringkas -->
        <div class="p-4 bg-gradient-to-r from-sky-500/10 via-indigo-500/10 to-teal-500/10 dark:from-sky-950/40 dark:to-indigo-950/40 rounded-2xl border border-sky-200/60 dark:border-sky-800/60 flex items-center gap-3.5">
            <div id="v_student_avatar_box" class="w-14 h-14 rounded-2xl bg-sky-600 text-white font-black text-xl flex items-center justify-center shrink-0 shadow-md overflow-hidden border-2 border-white dark:border-slate-800">
                A
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h4 id="v_student_name" class="font-black text-slate-900 dark:text-slate-100 text-base truncate">Nama Siswa</h4>
                    <span id="v_student_status_badge" class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Aktif</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium">
                    <span class="px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 font-bold border border-slate-200 dark:border-slate-700 text-[11px]" id="v_student_class">Kelas -</span>
                    <span class="text-slate-400">&bull;</span>
                    <span>NIS: <strong class="font-mono text-slate-800 dark:text-slate-100" id="v_student_nis">-</strong></span>
                    <span class="text-slate-400">&bull;</span>
                    <span>NISN: <strong class="font-mono text-sky-700 dark:text-sky-400" id="v_student_nisn">-</strong></span>
                </div>
            </div>
        </div>

        <!-- Grid Rincian Identitas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <!-- Data Pribadi -->
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2.5">
                <div class="font-black text-slate-800 dark:text-slate-100 text-xs flex items-center gap-1.5 border-b border-slate-200/60 dark:border-slate-700/60 pb-1.5">
                    <span>👤</span>
                    <span>Data Pribadi & Kependudukan</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NIK (KTP/KK)</span>
                    <span id="v_student_nik" class="font-mono font-bold text-slate-800 dark:text-slate-200 text-xs">-</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tempat, Tanggal Lahir</span>
                    <span id="v_student_ttl" class="font-bold text-slate-800 dark:text-slate-200 text-xs">-</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Kelamin & Agama</span>
                    <span id="v_student_gender_religion" class="font-semibold text-slate-800 dark:text-slate-200 text-xs">-</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Telepon / WhatsApp</span>
                    <span id="v_student_phone" class="font-semibold text-slate-800 dark:text-slate-200 text-xs">-</span>
                </div>
            </div>

            <!-- Data Keluarga & Pendidikan -->
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2.5">
                <div class="font-black text-slate-800 dark:text-slate-100 text-xs flex items-center gap-1.5 border-b border-slate-200/60 dark:border-slate-700/60 pb-1.5">
                    <span>👨‍👩‍👦</span>
                    <span>Orang Tua & Pendidikan Asal</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Ayah Kandung</span>
                    <span id="v_student_father" class="font-bold text-slate-800 dark:text-slate-200 text-xs">-</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Ibu Kandung</span>
                    <span id="v_student_mother" class="font-bold text-slate-800 dark:text-slate-200 text-xs">-</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sekolah Asal (SMP / MTs)</span>
                    <span id="v_student_previous_school" class="font-bold text-slate-800 dark:text-slate-200 text-xs">-</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kartu RFID / Face Recognition</span>
                    <span id="v_student_rfid_face" class="font-semibold text-slate-800 dark:text-slate-200 text-xs">-</span>
                </div>
            </div>
        </div>

        <!-- Alamat Lengkap -->
        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alamat Lengkap (Alamat, RT, RW, Kelurahan, Kecamatan)</span>
            <p id="v_student_address" class="font-medium text-slate-800 dark:text-slate-200 text-xs leading-relaxed">-</p>
        </div>

        <!-- Footer Buttons -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
            <button type="button" onclick="document.getElementById('viewStudentIdentityModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition cursor-pointer">
                Tutup
            </button>
            <div class="flex items-center gap-2">
                <a id="v_student_print_card_btn" href="#" target="_blank" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Cetak Kartu Siswa</span>
                </a>
                <button type="button" id="v_student_edit_btn" onclick="openEditFromView()" class="px-3.5 py-2 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Siswa</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rekam Wajah Siswa (Face Recognition Enrollment) -->
<div id="faceEnrollmentModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[92vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-base shrink-0">
                    👤
                </div>
                <div>
                    <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Rekam Wajah Siswa</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Dasar Verifikasi Presensi Face Recognition</p>
                </div>
            </div>
            <button type="button" onclick="closeFaceEnrollmentModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-base cursor-pointer transition">&times;</button>
        </div>

        <!-- Info Siswa -->
        <div class="p-3.5 bg-gradient-to-r from-indigo-50 to-sky-50 dark:from-indigo-950/40 dark:to-sky-950/40 rounded-2xl border border-indigo-100/80 dark:border-indigo-800/80 flex items-center justify-between gap-2 text-xs">
            <div>
                <div class="font-black text-slate-900 dark:text-slate-100 text-sm" id="fem_student_name">-</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    NISN: <span class="font-mono font-bold text-indigo-700 dark:text-indigo-400" id="fem_student_nisn">-</span> &bull; Kelas: <span class="font-bold text-slate-700 dark:text-slate-300" id="fem_student_class">-</span>
                </div>
            </div>
            <div id="fem_current_badge">
                <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 rounded-lg font-bold text-[10px]">
                    Belum Ada
                </span>
            </div>
        </div>

        <!-- Tab: Kamera Langsung vs Unggah Foto -->
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2.5">
            <button type="button" id="fem_tab_cam_btn" onclick="switchFaceTab('camera')" class="px-3.5 py-1.5 rounded-xl font-bold text-xs bg-indigo-600 text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>📷</span>
                <span>Kamera Langsung (Webcam)</span>
            </button>
            <button type="button" id="fem_tab_upload_btn" onclick="switchFaceTab('upload')" class="px-3.5 py-1.5 rounded-xl font-bold text-xs bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition flex items-center gap-1.5 cursor-pointer">
                <span>📁</span>
                <span>Unggah File Foto</span>
            </button>
        </div>

        <!-- Panel 1: Kamera Langsung -->
        <div id="fem_panel_camera" class="space-y-3">
            <div class="relative w-full aspect-4/3 bg-slate-950 rounded-2xl overflow-hidden flex items-center justify-center shadow-inner border border-slate-800">
                <video id="faceCamVideo" autoplay playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                <canvas id="faceCaptureCanvas" class="hidden"></canvas>
                <img id="faceCapturedImage" class="hidden w-full h-full object-cover" alt="Captured Face">

                <!-- Oval Guide Overlay -->
                <div id="faceOvalOverlay" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center">
                    <div class="w-44 h-56 border-2 border-dashed border-white/80 rounded-[50%] shadow-[0_0_0_9999px_rgba(15,23,42,0.45)] flex items-center justify-center">
                        <div class="w-40 h-52 border border-emerald-400/50 rounded-[50%]"></div>
                    </div>
                    <span class="mt-2 px-2.5 py-1 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold rounded-full">
                        Posisikan wajah tegak di dalam garis oval
                    </span>
                </div>

                <!-- Status message over video -->
                <div id="faceCamLoading" class="absolute inset-0 bg-slate-900 flex flex-col items-center justify-center text-white text-xs gap-2">
                    <div class="w-6 h-6 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
                    <span>Menyiapkan kamera webcam...</span>
                </div>
            </div>

            <!-- Camera Controls -->
            <div class="flex items-center justify-between gap-2 pt-1">
                <button type="button" id="faceStartCamBtn" onclick="initFaceCamera()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition cursor-pointer flex items-center gap-1.5">
                    <span>🔄</span>
                    <span>Nyalakan Kamera</span>
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" id="faceRetakeBtn" onclick="retakeFacePhoto()" class="hidden px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition cursor-pointer">
                        Foto Ulang
                    </button>
                    <button type="button" id="faceCaptureBtn" onclick="captureFaceSnapshot()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>📸</span>
                        <span>Jepret / Ambil Wajah</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel 2: Unggah Foto -->
        <div id="fem_panel_upload" class="space-y-3 hidden">
            <div class="p-5 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl text-center space-y-3 bg-slate-50/50 dark:bg-slate-800/40">
                <input type="file" id="faceFileInput" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewFaceFile(this)" class="hidden">
                <div id="faceUploadPreviewBox" class="w-36 h-36 mx-auto rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden shadow-xs">
                    <span class="text-4xl text-slate-300 dark:text-slate-600">👤</span>
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('faceFileInput').click()" class="px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-400 font-bold text-xs rounded-xl border border-indigo-200 dark:border-indigo-700 shadow-2xs transition cursor-pointer">
                        Pilih Foto Wajah Siswa
                    </button>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5">Mendukung format JPG, PNG, WEBP (maks 5MB). Pastikan wajah terlihat jelas menghadap depan.</p>
                </div>
            </div>
        </div>

        <!-- Current Registered Photo Section -->
        <div id="fem_current_photo_section" class="p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl flex items-center justify-between gap-3 text-xs hidden">
            <div class="flex items-center gap-2.5">
                <img id="fem_current_img" src="" class="w-10 h-10 rounded-xl object-cover border border-emerald-400">
                <div>
                    <div class="font-bold text-slate-800 dark:text-slate-100 text-[11px]">Wajah Saat Ini Terdaftar</div>
                    <div class="text-[10px] text-slate-400 dark:text-slate-500" id="fem_registered_time">-</div>
                </div>
            </div>
            <button type="button" onclick="deleteCurrentFace()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 font-bold text-[11px] rounded-xl border border-rose-200 dark:border-rose-800 transition cursor-pointer">
                Hapus Wajah
            </button>
        </div>

        <!-- Action Buttons -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
            <button type="button" onclick="closeFaceEnrollmentModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition cursor-pointer">
                Tutup
            </button>
            <button type="button" id="faceSaveEnrollmentBtn" onclick="saveFaceEnrollment()" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold rounded-xl text-xs shadow-xs transition cursor-pointer flex items-center gap-2 disabled:opacity-50">
                <span>✓</span>
                <span>Simpan Sebagai Dasar Absensi Wajah</span>
            </button>
        </div>
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

    function toggleAddExtraSecretarySelect(checkbox) {
        const wrapper = document.getElementById('add_extra_secretary_wrapper');
        const select = document.getElementById('add_student_extra_secretary_id');
        if (!wrapper) return;
        if (checkbox.checked) {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
            if (select) select.value = '';
        }
    }

    function toggleEditExtraSecretarySelect(checkbox) {
        const wrapper = document.getElementById('edit_extra_secretary_wrapper');
        const select = document.getElementById('edit_student_extra_secretary_id');
        if (!wrapper) return;
        if (checkbox.checked) {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
            if (select) select.value = '';
        }
    }

    function editStudent(s) {
        document.getElementById('editStudentForm').action = '/master/students/' + s.id;
        document.getElementById('edit_student_name').value = s.name;
        document.getElementById('edit_student_nis').value = s.nis;
        document.getElementById('edit_student_nisn').value = s.nisn;
        document.getElementById('edit_student_gender').value = s.gender;
        document.getElementById('edit_student_class_id').value = s.class_id;
        document.getElementById('edit_student_academic_year_id').value = s.academic_year_id;
        document.getElementById('edit_student_nik').value = s.nik || '';
        document.getElementById('edit_student_religion').value = s.religion || 'Islam';
        document.getElementById('edit_student_birth_place').value = s.birth_place || '';
        document.getElementById('edit_student_birth_date').value = s.birth_date ? s.birth_date.substring(0, 10) : '';
        document.getElementById('edit_student_father_name').value = s.father_name || '';
        document.getElementById('edit_student_mother_name').value = s.mother_name || '';
        document.getElementById('edit_student_previous_school').value = s.previous_school || '';
        document.getElementById('edit_student_rfid_uid').value = s.rfid_uid || '';
        document.getElementById('edit_student_phone').value = s.phone || '';
        document.getElementById('edit_student_address').value = s.address || '';
        document.getElementById('edit_student_is_active').value = s.is_active ? '1' : '0';
        
        const userRoles = s.user ? (Array.isArray(s.user.roles) ? s.user.roles : (s.user.roles ? JSON.parse(s.user.roles) : [s.user.role])) : [];
        const isSecretary = userRoles.includes('sekretaris_kelas') || (s.user && s.user.role === 'sekretaris_kelas');
        const secInput = document.getElementById('edit_student_is_secretary');
        if (secInput) {
            secInput.checked = isSecretary;
        }

        const isAdmWeb = userRoles.includes('adm_web') || (s.user && s.user.role === 'adm_web');
        const admWebInput = document.getElementById('edit_student_is_adm_web');
        if (admWebInput) {
            admWebInput.checked = isAdmWeb;
        }

        // Ekstrakurikuler Sekretaris logic
        let secretaryExtraId = '';
        if (s.extracurriculars && Array.isArray(s.extracurriculars)) {
            const secEx = s.extracurriculars.find(e => {
                const role = e.pivot ? e.pivot.role : null;
                return role && ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'].includes(role);
            });
            if (secEx) {
                secretaryExtraId = secEx.id;
            }
        }

        const isExtraSecretary = userRoles.includes('sekretaris_ekstra') || (s.user && s.user.role === 'sekretaris_ekstra') || Boolean(secretaryExtraId);
        const extraSecInput = document.getElementById('edit_student_is_extra_secretary');
        if (extraSecInput) {
            extraSecInput.checked = isExtraSecretary;
        }

        const extraSecSelect = document.getElementById('edit_student_extra_secretary_id');
        if (extraSecSelect) {
            extraSecSelect.value = secretaryExtraId || '';
        }

        const extraSecWrapper = document.getElementById('edit_extra_secretary_wrapper');
        if (extraSecWrapper) {
            if (isExtraSecretary) {
                extraSecWrapper.classList.remove('hidden');
            } else {
                extraSecWrapper.classList.add('hidden');
            }
        }

        // Setup Multi-Ekstrakurikuler checkboxes in Edit Modal
        const enrolledExtraIds = (s.extracurriculars && Array.isArray(s.extracurriculars))
            ? s.extracurriculars.map(e => parseInt(e.id))
            : [];
        document.querySelectorAll('.edit-student-extra-cb').forEach(cb => {
            cb.checked = enrolledExtraIds.includes(parseInt(cb.value));
        });

        // Setup Face Preview in Edit Modal
        const hasFace = Boolean(s.face_photo_path || s.photo);
        const faceUrl = s.face_photo_url || (s.face_photo_path ? ('/storage/' + s.face_photo_path) : (s.photo ? ('/storage/' + s.photo) : null));
        const previewBox = document.getElementById('edit_student_face_preview_box');
        const statusText = document.getElementById('edit_student_face_status_text');
        if (hasFace && faceUrl) {
            previewBox.innerHTML = `<img src="${faceUrl}" class="w-full h-full object-cover">`;
            statusText.innerHTML = '<span class="text-emerald-600 font-bold">✓ Wajah Terdaftar</span>';
        } else {
            previewBox.innerHTML = '👤';
            statusText.innerHTML = '<span class="text-amber-600 font-bold">Belum Ada Wajah</span>';
        }

        document.getElementById('edit_student_record_face_btn').onclick = function() {
            document.getElementById('editStudentModal').classList.add('hidden');
            openFaceEnrollmentModal({
                id: s.id,
                name: s.name,
                nis: s.nis,
                nisn: s.nisn,
                class_name: (s.school_class && s.school_class.name) ? s.school_class.name : '-',
                has_face: hasFace,
                face_url: faceUrl,
                registered_at: s.face_registered_at || null,
            });
        };

        document.getElementById('editStudentModal').classList.remove('hidden');
    }

    let currentViewingStudent = null;

    function viewStudentIdentity(s) {
        currentViewingStudent = s;

        // Avatar
        const avatarBox = document.getElementById('v_student_avatar_box');
        if (s.face_photo_url || s.photo || s.face_photo_path) {
            const photoUrl = s.face_photo_url || ('/storage/' + (s.face_photo_path || s.photo));
            avatarBox.innerHTML = `<img src="${photoUrl}" alt="${s.name}" class="w-full h-full object-cover">`;
        } else {
            avatarBox.textContent = (s.name || 'S').trim().charAt(0).toUpperCase();
        }

        document.getElementById('v_student_name').textContent = s.name || '-';
        
        // Status Badge
        const statusBadge = document.getElementById('v_student_status_badge');
        if (s.is_active) {
            statusBadge.className = 'px-2 py-0.5 rounded-md font-bold text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
            statusBadge.textContent = 'Aktif';
        } else {
            statusBadge.className = 'px-2 py-0.5 rounded-md font-bold text-[10px] bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400';
            statusBadge.textContent = 'Nonaktif';
        }

        document.getElementById('v_student_class').textContent = s.school_class?.name ? ('Kelas ' + s.school_class.name) : '-';
        document.getElementById('v_student_nis').textContent = s.nis || '-';
        document.getElementById('v_student_nisn').textContent = s.nisn || '-';
        document.getElementById('v_student_nik').textContent = s.nik || '- (Belum Diisi)';

        // TTL & Usia
        let ttlText = '-';
        if (s.birth_place || s.birth_date) {
            const parts = [];
            if (s.birth_place) parts.push(s.birth_place);
            if (s.birth_date) {
                const d = new Date(s.birth_date);
                if (!isNaN(d.getTime())) {
                    const formattedDate = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    // Hitung Usia
                    const today = new Date();
                    let age = today.getFullYear() - d.getFullYear();
                    const m = today.getMonth() - d.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < d.getDate())) {
                        age--;
                    }
                    parts.push(`${formattedDate} (${age} Tahun)`);
                } else {
                    parts.push(s.birth_date);
                }
            }
            ttlText = parts.join(', ');
        }
        document.getElementById('v_student_ttl').textContent = ttlText;

        const genderLabel = s.gender === 'L' ? 'Laki-laki (L)' : (s.gender === 'P' ? 'Perempuan (P)' : (s.gender || '-'));
        const religionLabel = s.religion || 'Islam';
        document.getElementById('v_student_gender_religion').textContent = `${genderLabel} • Agama ${religionLabel}`;
        document.getElementById('v_student_phone').textContent = s.phone || '-';

        document.getElementById('v_student_father').textContent = s.father_name || '- (Belum Ada Data)';
        document.getElementById('v_student_mother').textContent = s.mother_name || '- (Belum Ada Data)';
        document.getElementById('v_student_previous_school').textContent = s.previous_school || '- (Belum Ada Data)';

        // RFID & Face
        const rfidLabel = s.rfid_uid ? `RFID: ${s.rfid_uid}` : 'RFID: Belum Ada Kartu';
        const faceLabel = (s.face_photo_path || s.photo) ? '✓ Face Recognition Aktif' : 'Wajah Belum Direkam';
        document.getElementById('v_student_rfid_face').textContent = `${rfidLabel} • ${faceLabel}`;

        document.getElementById('v_student_address').textContent = s.address || '- (Alamat Belum Dilengkapi)';

        // Button Cetak Kartu
        document.getElementById('v_student_print_card_btn').href = `/attendance/cards/${s.id}`;

        document.getElementById('viewStudentIdentityModal').classList.remove('hidden');
    }

    function openEditFromView() {
        if (!currentViewingStudent) return;
        document.getElementById('viewStudentIdentityModal').classList.add('hidden');
        editStudent(currentViewingStudent);
    }

    // --- Face Enrollment Modal Script & Live Camera Engine ---
    let currentEnrollStudent = null;
    let activeFaceStream = null;
    let capturedFaceBase64 = null;
    let currentFaceTab = 'camera';

    function openFaceEnrollmentModal(data) {
        currentEnrollStudent = data;
        capturedFaceBase64 = null;

        document.getElementById('fem_student_name').textContent = data.name;
        document.getElementById('fem_student_nisn').textContent = data.nisn || data.nis || '-';
        document.getElementById('fem_student_class').textContent = data.class_name || '-';

        const badgeContainer = document.getElementById('fem_current_badge');
        const currentPhotoSection = document.getElementById('fem_current_photo_section');

        if (data.has_face && data.face_url) {
            badgeContainer.innerHTML = `
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-[10px] flex items-center gap-1">
                    ✓ Terdaftar
                </span>
            `;
            document.getElementById('fem_current_img').src = data.face_url;
            document.getElementById('fem_registered_time').textContent = data.registered_at ? `Tercatat: ${data.registered_at}` : 'Sudah terdaftar';
            currentPhotoSection.classList.remove('hidden');
        } else {
            badgeContainer.innerHTML = `
                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-lg font-bold text-[10px]">
                    Belum Ada
                </span>
            `;
            currentPhotoSection.classList.add('hidden');
        }

        // Reset camera state
        retakeFacePhoto();
        switchFaceTab('camera');

        document.getElementById('faceEnrollmentModal').classList.remove('hidden');
    }

    function closeFaceEnrollmentModal() {
        stopFaceCamera();
        document.getElementById('faceEnrollmentModal').classList.add('hidden');
    }

    function switchFaceTab(tab) {
        currentFaceTab = tab;
        const camBtn = document.getElementById('fem_tab_cam_btn');
        const uploadBtn = document.getElementById('fem_tab_upload_btn');
        const panelCam = document.getElementById('fem_panel_camera');
        const panelUpload = document.getElementById('fem_panel_upload');

        const activeClass = 'px-3.5 py-1.5 rounded-xl font-bold text-xs bg-indigo-600 text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer';
        const inactiveClass = 'px-3.5 py-1.5 rounded-xl font-bold text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-1.5 cursor-pointer';

        if (tab === 'camera') {
            camBtn.className = activeClass;
            uploadBtn.className = inactiveClass;
            panelCam.classList.remove('hidden');
            panelUpload.classList.add('hidden');
            initFaceCamera();
        } else {
            camBtn.className = inactiveClass;
            uploadBtn.className = activeClass;
            panelCam.classList.add('hidden');
            panelUpload.classList.remove('hidden');
            stopFaceCamera();
        }
    }

    async function initFaceCamera() {
        const video = document.getElementById('faceCamVideo');
        const loading = document.getElementById('faceCamLoading');

        if (activeFaceStream) {
            loading.classList.add('hidden');
            return;
        }

        loading.classList.remove('hidden');

        try {
            activeFaceStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                },
                audio: false
            });

            video.srcObject = activeFaceStream;
            video.onloadedmetadata = () => {
                loading.classList.add('hidden');
            };
        } catch (err) {
            loading.classList.remove('hidden');
            loading.innerHTML = `
                <span class="text-rose-400 font-bold">Gagal membuka kamera: ${err.name}</span>
                <span class="text-[10px] text-slate-300 text-center px-4">Pastikan browser diberi izin akses kamera atau gunakan tab <strong>"Unggah File Foto"</strong>.</span>
            `;
        }
    }

    function stopFaceCamera() {
        if (activeFaceStream) {
            activeFaceStream.getTracks().forEach(track => track.stop());
            activeFaceStream = null;
        }
    }

    function captureFaceSnapshot() {
        const video = document.getElementById('faceCamVideo');
        const canvas = document.getElementById('faceCaptureCanvas');
        const imgPreview = document.getElementById('faceCapturedImage');
        const ovalOverlay = document.getElementById('faceOvalOverlay');

        if (!video.videoWidth || !video.videoHeight) {
            alert('Kamera belum siap mengambil gambar. Silakan tunggu kamera aktif.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');

        // Draw mirror-adjusted image
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        capturedFaceBase64 = canvas.toDataURL('image/jpeg', 0.92);

        imgPreview.src = capturedFaceBase64;
        imgPreview.classList.remove('hidden');
        video.classList.add('hidden');
        ovalOverlay.classList.add('hidden');

        document.getElementById('faceRetakeBtn').classList.remove('hidden');
        document.getElementById('faceCaptureBtn').classList.add('hidden');
    }

    function retakeFacePhoto() {
        capturedFaceBase64 = null;
        const video = document.getElementById('faceCamVideo');
        const imgPreview = document.getElementById('faceCapturedImage');
        const ovalOverlay = document.getElementById('faceOvalOverlay');

        imgPreview.classList.add('hidden');
        video.classList.remove('hidden');
        ovalOverlay.classList.remove('hidden');

        document.getElementById('faceRetakeBtn').classList.add('hidden');
        document.getElementById('faceCaptureBtn').classList.remove('hidden');
    }

    function previewFaceFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('faceUploadPreviewBox').innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function saveFaceEnrollment() {
        if (!currentEnrollStudent) return;

        const saveBtn = document.getElementById('faceSaveEnrollmentBtn');
        const originalBtnText = saveBtn.innerHTML;

        const formData = new FormData();
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (currentFaceTab === 'camera') {
            if (!capturedFaceBase64) {
                alert('Silakan jepret/ambil foto wajah terlebih dahulu menggunakan kamera.');
                return;
            }
            formData.append('face_image', capturedFaceBase64);
        } else {
            const fileInput = document.getElementById('faceFileInput');
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Silakan pilih berkas foto wajah terlebih dahulu.');
                return;
            }
            formData.append('face_photo', fileInput.files[0]);
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span>⏳</span><span>Menyimpan Wajah...</span>';

        try {
            const res = await fetch(`/master/students/${currentEnrollStudent.id}/record-face`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok && data.success) {
                // Update cell in table
                const cell = document.getElementById(`face-cell-${currentEnrollStudent.id}`);
                if (cell) {
                    cell.innerHTML = `
                        <div class="inline-flex items-center gap-2">
                            <img src="${data.face_photo_url}" alt="${currentEnrollStudent.name}" class="w-8 h-8 rounded-full object-cover border-2 border-emerald-500 shadow-xs shrink-0">
                            <div class="text-left">
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md font-bold text-[10px] inline-flex items-center gap-1">
                                    ✓ Terdaftar
                                </span>
                                <div class="text-[9px] text-slate-400 mt-0.5">${data.face_registered_at || 'Baru Saja'}</div>
                            </div>
                        </div>
                    `;
                }

                alert(data.message || 'Wajah siswa berhasil direkam dan didaftarkan sebagai dasar absen Face Recognition.');
                closeFaceEnrollmentModal();
            } else {
                alert(data.message || 'Gagal menyimpan wajah siswa.');
            }
        } catch (err) {
            alert('Terjadi kesalahan saat menyimpan foto wajah: ' + err.message);
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnText;
        }
    }

    async function deleteCurrentFace() {
        if (!currentEnrollStudent) return;
        if (!confirm(`Hapus rekaman wajah siswa ${currentEnrollStudent.name}?`)) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const res = await fetch(`/master/students/${currentEnrollStudent.id}/delete-face`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (res.ok && data.success) {
                const cell = document.getElementById(`face-cell-${currentEnrollStudent.id}`);
                if (cell) {
                    cell.innerHTML = `
                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg font-bold text-[10px] inline-flex items-center gap-1">
                            Belum Ada
                        </span>
                    `;
                }

                document.getElementById('fem_current_photo_section').classList.add('hidden');
                document.getElementById('fem_current_badge').innerHTML = `
                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-lg font-bold text-[10px]">
                        Belum Ada
                    </span>
                `;

                alert(data.message || 'Data rekaman wajah berhasil dihapus.');
            } else {
                alert(data.message || 'Gagal menghapus rekaman wajah.');
            }
        } catch (err) {
            alert('Terjadi kesalahan saat menghapus foto wajah: ' + err.message);
        }
    }

    function filterMasterStudentByInitial(letter) {
        const input = document.getElementById('studentSearchInput');
        if (input) {
            input.value = letter;
            input.form.submit();
        }
    }

    function clearStudentMasterSearch() {
        const input = document.getElementById('studentSearchInput');
        if (input) {
            input.value = '';
            input.form.submit();
        }
    }
</script>
@endsection

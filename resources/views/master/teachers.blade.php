@extends('layouts.app')

@section('title', 'Data Guru & Tenaga Administrasi (TU) - Master Data SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    @include('master.partials.nav')
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold">
                MASTER DATA SEKOLAH
            </span>
            <h1 class="text-xl sm:text-3xl font-black text-white mt-2">Data Guru, Tenaga Administrasi & Kepegawaian</h1>
            <p class="text-xs text-slate-300 mt-1">Basis data tunggal Guru & PTK: Terhubung otomatis 1-to-1 dengan Adm. Kepegawaian, Buku Induk Pegawai, NIP/Golongan, dan Akun Sistem Login.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <button onclick="document.getElementById('importTeacherModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Impor Guru & Pegawai
            </button>
            <button onclick="document.getElementById('addTeacherModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                + Tambah Guru / Pegawai & Akun
            </button>
        </div>
    </div>

    <!-- Filter by Type Tabs & Live Search by Typing -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
                <span class="text-slate-400 dark:text-slate-500 mr-1 text-[11px] uppercase tracking-wider">Kategori:</span>
                <a href="{{ route('master.teachers', array_filter(['search' => $search, 'status' => $status])) }}" 
                   class="px-3 py-1.5 rounded-xl transition {{ empty($type) ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                   Semua PTK ({{ $totalPtk ?? $teachers->count() }})
                </a>
                <a href="{{ route('master.teachers', array_filter(['type' => 'guru', 'search' => $search, 'status' => $status])) }}" 
                   class="px-3 py-1.5 rounded-xl transition {{ ($type ?? '') === 'guru' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                   👨‍🏫 Tenaga Pendidik (Guru) ({{ $totalGuru ?? 0 }})
                </a>
                <a href="{{ route('master.teachers', array_filter(['type' => 'tendik', 'search' => $search, 'status' => $status])) }}" 
                   class="px-3 py-1.5 rounded-xl transition {{ ($type ?? '') === 'tendik' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                   💼 Tenaga Kependidikan (TU) ({{ $totalTendik ?? 0 }})
                </a>
            </div>
            <div class="text-[11px] text-slate-400 font-medium">
                Tampilan & Struktur Database Identik dengan Meja Kerja Adm. Kepegawaian
            </div>
        </div>

        <form method="GET" action="{{ route('master.teachers') }}" class="flex flex-wrap gap-2 sm:gap-3 text-xs">
            @if(!empty($type))
                <input type="hidden" name="type" value="{{ $type }}">
            @endif
            <div class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl outline-none font-medium">
                    <option value="">-- Semua Status Kepegawaian --</option>
                    <option value="PNS" {{ ($status ?? '') === 'PNS' ? 'selected' : '' }}>PNS</option>
                    <option value="PPPK" {{ ($status ?? '') === 'PPPK' ? 'selected' : '' }}>PPPK / PPPK PW</option>
                    <option value="Honorer" {{ ($status ?? '') === 'Honorer' ? 'selected' : '' }}>GTT / Honorer / NON ASN</option>
                </select>
            </div>
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" id="teacherSearchInput" value="{{ $search }}"
                    placeholder="Cari nama pegawai, NIP, mapel, jabatan, gol/ruang, status, kampus, jurusan, username, email..."
                    class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-800 text-xs transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            @if($search || !empty($type) || !empty($status))
                <a href="{{ route('master.teachers') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold flex items-center justify-center">Reset Filter</a>
            @endif
        </form>
    </div>

    <!-- Table of Teachers & PTK -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Daftar Tenaga Pendidik & Tenaga Administrasi</h2>
                <span class="table-swipe-cue">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Geser Tabel →
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 font-bold text-xs rounded-full self-start sm:self-auto">
                    {{ $teachers->count() }} Pendidik & Pegawai
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" id="teacherMasterTable">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Pegawai / PTK</th>
                        <th class="py-3 px-4">NIP / NUPTK</th>
                        <th class="py-3 px-4">Jabatan & Tugas</th>
                        <th class="py-3 px-4">Status & Gol/Ruang</th>
                        <th class="py-3 px-4">Pendidikan</th>
                        <th class="py-3 px-4">Akun Login & Peran Sistem</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($teachers as $t)
                        @php
                            $empType = $t->employment_type ?? ($t->user?->employee?->employment_type ?? (str_contains(strtolower($t->title ?? ''), 'tata usaha') || in_array($t->user?->role ?? '', ['staff', 'admin', 'super_admin']) ? 'Tenaga Kependidikan' : 'Guru'));
                            $empStatus = $t->employment_status ?? ($t->user?->employee?->employment_status ?? 'PNS');
                            $rankGroup = $t->rank_group ?? ($t->user?->employee?->rank_group ?? '-');
                            $mkTh = $t->years_of_service_years ?? ($t->user?->employee?->years_of_service_years ?? null);
                            $mkBln = $t->years_of_service_months ?? ($t->user?->employee?->years_of_service_months ?? null);
                            $eduLevel = $t->education_level ?? ($t->user?->employee?->education_level ?? '-');
                            $eduMajor = $t->education_major ?? ($t->user?->employee?->education_major ?? '');
                            $eduCampus = $t->education_institution ?? ($t->user?->employee?->education_institution ?? '');
                            $eduYear = $t->education_grad_year ?? ($t->user?->employee?->education_grad_year ?? '');
                            $posName = $t->position ?: ($t->title ?: ($empType === 'Guru' ? 'Guru Mata Pelajaran' : 'Staf Tata Usaha'));
                            $teachingSubj = $t->teaching_subject ?: ($t->user?->employee?->teaching_subject ?? '');
                            $ttl = ($t->birth_place ? $t->birth_place . ', ' : '') . ($t->birth_date ? $t->birth_date->format('d/m/Y') : '');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition teacher-row" 
                            data-search="{{ strtolower($t->name . ' ' . $t->nip . ' ' . $t->nuptk . ' ' . $posName . ' ' . $teachingSubj . ' ' . $rankGroup . ' ' . $empStatus . ' ' . $eduMajor . ' ' . $eduCampus . ' ' . ($t->user->username ?? '') . ' ' . ($t->user->email ?? '') . ' ' . ($t->user->role ?? '')) }}">
                            <!-- 1. Nama Pegawai / PTK -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $t->name }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $empType === 'Guru' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-teal-100 text-teal-700 dark:bg-teal-950/80 dark:text-teal-300 border border-teal-200 dark:border-teal-800' }}">
                                        {{ $empType === 'Guru' ? 'Guru' : 'Tendik / TU' }}
                                    </span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold {{ $t->gender === 'P' ? 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' : 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300' }}">
                                        {{ $t->gender === 'P' ? 'P' : 'L' }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5 flex items-center gap-2 flex-wrap">
                                    @if($ttl)
                                        <span>TTL: {{ $ttl }}</span>
                                    @endif
                                    @if($t->phone)
                                        <span>Telp: {{ $t->phone }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. NIP / NUPTK -->
                            <td class="py-3 px-4 font-mono font-semibold text-slate-700 dark:text-slate-300">
                                <div>{{ $t->nip ?: '-' }}</div>
                                @if($t->nuptk)
                                    <div class="text-[10px] text-slate-400 font-normal">NUPTK: {{ $t->nuptk }}</div>
                                @endif
                            </td>

                            <!-- 3. Jabatan & Tugas -->
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                <span class="font-medium text-slate-800 dark:text-slate-200 block">{{ $posName }}</span>
                                @if($teachingSubj && $teachingSubj !== $posName)
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">Mapel: {{ $teachingSubj }}</div>
                                @endif
                                @if($t->homeroomClass)
                                    <div class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold mt-0.5">Wali Kelas {{ $t->homeroomClass->name }}</div>
                                @endif
                                @if($t->extracurricular_names)
                                    <div class="text-[10px] text-cyan-600 dark:text-cyan-400 font-bold mt-0.5">Pembina: {{ $t->extracurricular_names }}</div>
                                @endif
                            </td>

                            <!-- 4. Status & Gol/Ruang -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                        {{ $empStatus === 'PNS' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20' : (str_contains($empStatus, 'PPPK') ? 'bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 border border-cyan-500/20' : 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20') }}">
                                        {{ $empStatus }} {{ $rankGroup && $rankGroup !== '-' ? "({$rankGroup})" : '' }}
                                    </span>
                                </div>
                                @if(!is_null($mkTh))
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">
                                        Masa Kerja: {{ $mkTh }} Th {{ $mkBln ?? 0 }} Bln
                                    </div>
                                @endif
                            </td>

                            <!-- 5. Pendidikan -->
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $eduLevel ?: '-' }} {{ $eduMajor ? "- {$eduMajor}" : '' }}
                                </div>
                                @if($eduCampus)
                                    <div class="text-[10px] text-slate-400 truncate max-w-[200px]" title="{{ $eduCampus }} ({{ $eduYear }})">
                                        {{ $eduCampus }} {{ $eduYear ? "({$eduYear})" : '' }}
                                    </div>
                                @endif
                            </td>

                            <!-- 6. Akun Login & Peran Sistem -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800 dark:text-slate-100 font-mono text-[11px]">{{ $t->user->username ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $t->user->email ?? '-' }}</div>
                                @php
                                    $userRoles = $t->user ? $t->user->all_roles : ['guru'];
                                @endphp
                                <div class="flex flex-wrap gap-1 mt-1 max-w-[220px]">
                                    @foreach($userRoles as $uRole)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold uppercase tracking-wider
                                            {{ match($uRole) {
                                                'super_admin' => 'bg-purple-100 dark:bg-purple-950/80 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800',
                                                'admin' => 'bg-red-100 dark:bg-red-950/80 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800',
                                                'kepala_sekolah' => 'bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800',
                                                'wakasek_kesiswaan', 'kesiswaan' => 'bg-amber-100 dark:bg-amber-950/80 text-amber-900 dark:text-amber-200 border border-amber-300 dark:border-amber-700',
                                                'wakasek_kurikulum' => 'bg-sky-100 dark:bg-sky-950/80 text-sky-900 dark:text-sky-200 border border-sky-300 dark:border-sky-700',
                                                'wakasek_humas' => 'bg-pink-100 dark:bg-pink-950/80 text-pink-900 dark:text-pink-200 border border-pink-300 dark:border-pink-700',
                                                'wakasek_sarpras' => 'bg-teal-100 dark:bg-teal-950/80 text-teal-900 dark:text-teal-200 border border-teal-300 dark:border-teal-700',
                                                'wali_kelas' => 'bg-indigo-100 dark:bg-indigo-950/80 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800',
                                                'koordinator_keagamaan' => 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800',
                                                'pembina_ekstra' => 'bg-cyan-100 dark:bg-cyan-950/80 text-cyan-800 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800',
                                                'bk' => 'bg-orange-100 dark:bg-orange-950/80 text-orange-800 dark:text-orange-300 border border-orange-200 dark:border-orange-800',
                                                'kepala_administrasi' => 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700',
                                                'tenaga_administrasi' => 'bg-teal-100 dark:bg-teal-950/80 text-teal-800 dark:text-teal-300 border border-teal-300 dark:border-teal-700',
                                                'kepala_perpustakaan' => 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700',
                                                'pengelola_perpustakaan', 'pustakawan' => 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700',
                                                'ta_kesiswaan' => 'bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 border border-blue-300 dark:border-blue-700',
                                                'ta_kepegawaian' => 'bg-violet-100 dark:bg-violet-950/80 text-violet-800 dark:text-violet-300 border border-violet-300 dark:border-violet-700',
                                                'ta_persuratan' => 'bg-sky-100 dark:bg-sky-950/80 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-700',
                                                'bendahara' => 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700',
                                                'ta_sarpras' => 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700',
                                                'operator' => 'bg-purple-100 dark:bg-purple-950/80 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-700',
                                                default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700',
                                            } }}">
                                            {{ str_replace('_', ' ', $uRole) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- 7. Status Keaktifan -->
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $t->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                                    {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <!-- 8. Aksi -->
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($t->user?->employee)
                                        <a href="{{ route('employees.buku-induk.show', $t->user->employee) }}" class="p-1.5 bg-teal-50 dark:bg-teal-950/50 hover:bg-teal-100 text-teal-600 dark:text-teal-400 rounded-lg transition" title="Buku Induk Pegawai">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </a>
                                    @endif
                                    <button onclick="editTeacher({{ json_encode($t) }})" class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-lg transition cursor-pointer" title="Edit Data & Role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.teachers.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data guru {{ $t->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-lg transition cursor-pointer" title="Hapus Guru">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                Belum ada data guru atau pegawai yang terdaftar.
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
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-teal-100 dark:bg-teal-950 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Impor Massal Data Guru, Pegawai & Akun</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Unggah file CSV data guru & tenaga administrasi (TU) serta buat akun otomatis</p>
                </div>
            </div>
            <button onclick="document.getElementById('importTeacherModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold cursor-pointer">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700 dark:text-slate-300">
                <span>Format File & Panduan:</span>
                <a href="{{ route('master.teachers.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-600 hover:bg-teal-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                Kolom wajib: <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-teal-700 dark:text-teal-400 font-mono">nip</code>, <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-teal-700 dark:text-teal-400 font-mono">nama_lengkap</code>, <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-teal-700 dark:text-teal-400 font-mono">jenis_kelamin</code> (L/P), <code class="bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-teal-700 dark:text-teal-400 font-mono">peran</code>.
            </p>
        </div>

        <form action="{{ route('master.teachers.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('importTeacherModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-bold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Guru & Pegawai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Guru & Pegawai (Lengkap dengan Data Kepegawaian & Akun) -->
<div id="addTeacherModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-3 sm:p-6 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[92vh] overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <div>
                <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Tambah Data Guru / Pegawai & Akun Sistem</h3>
                <p class="text-[11px] text-slate-400">Biodata lengkap, identitas kepegawaian, riwayat pendidikan, dan peran akun</p>
            </div>
            <button onclick="document.getElementById('addTeacherModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer font-bold">✕</button>
        </div>
        <form action="{{ route('master.teachers.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
            @csrf
            <div class="p-6 overflow-y-auto flex-1 min-h-0 space-y-5 text-xs">
                <!-- 1. BIODATA PRIBADI -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 inline-flex items-center justify-center text-[10px] font-black">1</span>
                        Identitas & Biodata Pribadi
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required placeholder="Contoh: Drs. Bambang Supriyadi, M.Pd." class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none font-bold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select name="gender" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" name="nip" placeholder="18 digit atau kosongkan" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                            <input type="text" name="birth_place" placeholder="Kota/Kabupaten" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon / WA</label>
                            <input type="text" name="phone" placeholder="08..." class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Tinggal</label>
                            <input type="text" name="address" placeholder="Alamat domisili" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                        </div>
                    </div>
                </div>

                <!-- 2. DATA KEPEGAWAIAN & JABATAN -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 inline-flex items-center justify-center text-[10px] font-black">2</span>
                        Status Kepegawaian & Penugasan
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis PTK <span class="text-rose-500">*</span></label>
                            <select name="employment_type" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold">
                                <option value="Guru">Tenaga Pendidik (Guru)</option>
                                <option value="Tenaga Kependidikan">Tenaga Kependidikan (Tendik / TU)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kepegawaian</label>
                            <select name="employment_status" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                                <option value="PNS">PNS</option>
                                <option value="PPPK">PPPK</option>
                                <option value="PPPK PW">PPPK PW</option>
                                <option value="GTT">GTT / Honorer</option>
                                <option value="NON ASN">NON ASN</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Golongan / Ruang</label>
                            <input type="text" name="rank_group" placeholder="IV/a, III/d, IX, dll" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none font-semibold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">TMT Golongan</label>
                            <input type="date" name="rank_tmt" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jabatan / Tugas</label>
                            <input type="text" name="position" placeholder="Kepala Sekolah / Guru Matematika / Staf Kepegawaian" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-medium">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Pelajaran (Mapel)</label>
                            <input type="text" name="teaching_subject" placeholder="Biologi / Fisika / dll" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Mulai Kerja (TMT)</label>
                            <input type="date" name="position_tmt" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Masa Kerja (Tahun)</label>
                            <input type="number" name="years_of_service_years" min="0" placeholder="0" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Masa Kerja (Bulan)</label>
                            <input type="number" name="years_of_service_months" min="0" max="11" placeholder="0" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tugas Tambahan</label>
                            <input type="text" name="additional_tasks" placeholder="Wakasek, Pembina, dll" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                </div>

                <!-- 3. RIWAYAT PENDIDIKAN TERAKHIR -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 inline-flex items-center justify-center text-[10px] font-black">3</span>
                        Riwayat Pendidikan Terakhir
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenjang Program</label>
                            <select name="education_level" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                                <option value="S2/A.IV">S2 / A.IV</option>
                                <option value="S1/A.IV">S1 / A.IV</option>
                                <option value="S1" selected>S1</option>
                                <option value="D3">D3</option>
                                <option value="SMA">SMA / SMK / Paket C</option>
                                <option value="SMP">SMP</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kampus / Sekolah</label>
                            <input type="text" name="education_institution" placeholder="Contoh: UPI Bandung / SMAN 1 Lengkong" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Lulus</label>
                            <input type="text" name="education_grad_year" placeholder="Contoh: 2018" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jurusan / Program Studi</label>
                        <input type="text" name="education_major" placeholder="Contoh: Pendidikan Matematika / Administrasi Kepegawaian" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                    </div>
                </div>

                <!-- 4. PENGATURAN AKUN LOGIN & MULTI-ROLE -->
                <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/60 rounded-2xl space-y-3">
                    <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-200 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 inline-flex items-center justify-center text-[10px] font-black">4</span>
                        Pengaturan Akun Login & Hak Akses Multi-Role
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Peran Utama (Primary Role) <span class="text-rose-500">*</span></label>
                            <select name="role" id="add_teacher_role" onchange="handleTeacherPrimaryRoleChange('add')" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-emerald-800 dark:text-emerald-300">
                                <option value="guru" selected>Tenaga Pendidik (Guru)</option>
                                <option value="staff">Tenaga Kependidikan (Staf TU)</option>
                                <option value="admin">Administrator Sekolah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Username Login</label>
                            <input type="text" name="username" placeholder="Otomatis NIP jika kosong" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Email Akun <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" required placeholder="email@sman1lengkong.sch.id" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Password Awal Akun</label>
                        <input type="password" name="password" placeholder="Otomatis NIP / sman1lengkong jika kosong" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                    </div>

                    <!-- Peran Tambahan Tenaga Pendidik -->
                    <div id="add_pendidik_roles_box" class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 text-[11px]">Peran Tambahan Guru (Centang yang sesuai):</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-44 overflow-y-auto">
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-blue-700 dark:text-blue-400">
                                <input type="checkbox" name="roles[]" value="kepala_sekolah" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-0">
                                <span>Kepala Sekolah</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-sky-700 dark:text-sky-400">
                                <input type="checkbox" name="roles[]" value="wakasek_kurikulum" class="w-3.5 h-3.5 rounded text-sky-600 focus:ring-0">
                                <span>Wakasek Kurikulum</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700 dark:text-amber-400">
                                <input type="checkbox" name="roles[]" value="wakasek_kesiswaan" class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                                <span>Wakasek Kesiswaan</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-pink-700 dark:text-pink-400">
                                <input type="checkbox" name="roles[]" value="wakasek_humas" class="w-3.5 h-3.5 rounded text-pink-600 focus:ring-0">
                                <span>Wakasek Humas</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-teal-700 dark:text-teal-400">
                                <input type="checkbox" name="roles[]" value="wakasek_sarpras" class="w-3.5 h-3.5 rounded text-teal-600 focus:ring-0">
                                <span>Wakasek Sarpras</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-indigo-700 dark:text-indigo-400">
                                <input type="checkbox" name="roles[]" value="wali_kelas" class="w-3.5 h-3.5 rounded text-indigo-600 focus:ring-0">
                                <span>Wali Kelas</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-orange-700 dark:text-orange-400">
                                <input type="checkbox" name="roles[]" value="bk" class="w-3.5 h-3.5 rounded text-orange-600 focus:ring-0">
                                <span>Guru BK</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-cyan-700 dark:text-cyan-400">
                                <input type="checkbox" name="roles[]" value="pembina_ekstra" class="w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                                <span>Pembina Ekstrakurikuler</span>
                            </label>
                        </div>
                    </div>

                    <!-- Peran Tambahan Tenaga Kependidikan -->
                    <div id="add_tendik_roles_box" class="hidden space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 text-[11px]">Peran Urusan Tenaga Administrasi (TU):</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-44 overflow-y-auto">
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-800 dark:text-emerald-300">
                                <input type="checkbox" name="roles[]" value="kepala_administrasi" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                                <span>Kepala Urusan Tata Usaha</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-purple-700 dark:text-purple-400">
                                <input type="checkbox" name="roles[]" value="operator" class="w-3.5 h-3.5 rounded text-purple-600 focus:ring-0">
                                <span>Operator Dapodik / SIM</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-violet-700 dark:text-violet-400">
                                <input type="checkbox" name="roles[]" value="ta_kepegawaian" class="w-3.5 h-3.5 rounded text-violet-600 focus:ring-0">
                                <span>Adm. Kepegawaian</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-blue-700 dark:text-blue-400">
                                <input type="checkbox" name="roles[]" value="ta_kesiswaan" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-0">
                                <span>Adm. Kesiswaan</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700 dark:text-amber-400">
                                <input type="checkbox" name="roles[]" value="ta_sarpras" class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                                <span>Adm. Sarana Prasarana</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700 dark:text-emerald-400">
                                <input type="checkbox" name="roles[]" value="bendahara" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                                <span>Bendahara BOSP / Sekolah</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-sky-700 dark:text-sky-400">
                                <input type="checkbox" name="roles[]" value="ta_persuratan" class="w-3.5 h-3.5 rounded text-sky-600 focus:ring-0">
                                <span>Adm. Persuratan & Arsip</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-cyan-700 dark:text-cyan-400">
                                <input type="checkbox" name="roles[]" value="pustakawan" class="w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                                <span>Pustakawan / Perpustakaan</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="document.getElementById('addTeacherModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-xl font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-xs cursor-pointer">Simpan Guru & Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Guru & Pegawai (Lengkap dengan Data Kepegawaian & Akun) -->
<div id="editTeacherModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-3 sm:p-6 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[92vh] overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <div>
                <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Edit Data Guru / Pegawai & Role Akun</h3>
                <p class="text-[11px] text-slate-400">Perbarui biodata, identitas kepegawaian, jabatan, dan peran akun login sistem</p>
            </div>
            <button onclick="document.getElementById('editTeacherModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer font-bold">✕</button>
        </div>
        <form id="editTeacherForm" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
            @csrf
            @method('PUT')
            <div class="p-6 overflow-y-auto flex-1 min-h-0 space-y-5 text-xs">
                <!-- 1. BIODATA PRIBADI -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 inline-flex items-center justify-center text-[10px] font-black">1</span>
                        Identitas & Biodata Pribadi
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="edit_teacher_name" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none font-bold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select name="gender" id="edit_teacher_gender" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" name="nip" id="edit_teacher_nip" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                            <input type="text" name="birth_place" id="edit_teacher_birth_place" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="edit_teacher_birth_date" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon / WA</label>
                            <input type="text" name="phone" id="edit_teacher_phone" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Tinggal</label>
                            <input type="text" name="address" id="edit_teacher_address" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                </div>

                <!-- 2. STATUS KEPEGAWAIAN & JABATAN -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 inline-flex items-center justify-center text-[10px] font-black">2</span>
                        Status Kepegawaian & Penugasan
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis PTK <span class="text-rose-500">*</span></label>
                            <select name="employment_type" id="edit_teacher_employment_type" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold">
                                <option value="Guru">Tenaga Pendidik (Guru)</option>
                                <option value="Tenaga Kependidikan">Tenaga Kependidikan (Tendik / TU)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kepegawaian</label>
                            <select name="employment_status" id="edit_teacher_employment_status" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                                <option value="PNS">PNS</option>
                                <option value="PPPK">PPPK</option>
                                <option value="PPPK PW">PPPK PW</option>
                                <option value="GTT">GTT / Honorer</option>
                                <option value="NON ASN">NON ASN</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Golongan / Ruang</label>
                            <input type="text" name="rank_group" id="edit_teacher_rank_group" placeholder="IV/a, III/d, IX" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-semibold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">TMT Golongan</label>
                            <input type="date" name="rank_tmt" id="edit_teacher_rank_tmt" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jabatan / Tugas</label>
                            <input type="text" name="position" id="edit_teacher_position" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-medium">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Pelajaran (Mapel)</label>
                            <input type="text" name="teaching_subject" id="edit_teacher_teaching_subject" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Mulai Kerja (TMT)</label>
                            <input type="date" name="position_tmt" id="edit_teacher_position_tmt" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Masa Kerja (Tahun)</label>
                            <input type="number" name="years_of_service_years" id="edit_teacher_mk_th" min="0" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Masa Kerja (Bulan)</label>
                            <input type="number" name="years_of_service_months" id="edit_teacher_mk_bln" min="0" max="11" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tugas Tambahan</label>
                            <input type="text" name="additional_tasks" id="edit_teacher_additional_tasks" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    @if(isset($extracurriculars) && $extracurriculars->isNotEmpty())
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1 text-[11px]">Penugasan Ekstrakurikuler (Bina Ekstra):</label>
                        <div class="grid grid-cols-2 gap-2 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-32 overflow-y-auto">
                            @foreach($extracurriculars as $ekstra)
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700 dark:text-slate-300 hover:text-cyan-700 dark:hover:text-cyan-400">
                                <input type="checkbox" name="extracurricular_ids[]" value="{{ $ekstra->id }}" id="edit_ekstra_{{ $ekstra->id }}" class="edit-teacher-ekstra-cb w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                                <span class="truncate">{{ $ekstra->name }} <span class="text-[9px] text-slate-400 dark:text-slate-500">({{ $ekstra->category }})</span></span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- 3. RIWAYAT PENDIDIKAN TERAKHIR -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 inline-flex items-center justify-center text-[10px] font-black">3</span>
                        Riwayat Pendidikan Terakhir
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenjang Program</label>
                            <select name="education_level" id="edit_teacher_education_level" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                                <option value="S2/A.IV">S2 / A.IV</option>
                                <option value="S1/A.IV">S1 / A.IV</option>
                                <option value="S1">S1</option>
                                <option value="D3">D3</option>
                                <option value="SMA">SMA / SMK / Paket C</option>
                                <option value="SMP">SMP</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kampus / Sekolah</label>
                            <input type="text" name="education_institution" id="edit_teacher_education_institution" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Lulus</label>
                            <input type="text" name="education_grad_year" id="edit_teacher_education_grad_year" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jurusan / Program Studi</label>
                        <input type="text" name="education_major" id="edit_teacher_education_major" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                    </div>
                </div>

                <!-- 4. PENGATURAN AKUN LOGIN & MULTI-ROLE -->
                <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/60 rounded-2xl space-y-3">
                    <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-200 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 inline-flex items-center justify-center text-[10px] font-black">4</span>
                        Pengaturan Akun Login & Hak Akses Multi-Role
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Peran Utama (Primary) <span class="text-rose-500">*</span></label>
                            <select name="role" id="edit_teacher_role" onchange="handleTeacherPrimaryRoleChange('edit')" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-emerald-800 dark:text-emerald-300">
                                <option value="guru">Tenaga Pendidik (Guru)</option>
                                <option value="staff">Tenaga Kependidikan (Staf TU)</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Email Akun <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" id="edit_teacher_email" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ubah Password</label>
                            <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Keaktifan</label>
                            <select name="is_active" id="edit_teacher_is_active" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-medium">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Peran Tambahan Tenaga Pendidik -->
                    <div id="edit_pendidik_roles_box" class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 text-[11px]">Peran Tambahan Guru (Centang yang sesuai):</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-44 overflow-y-auto">
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-blue-700 dark:text-blue-400">
                                <input type="checkbox" name="roles[]" value="kepala_sekolah" id="edit_role_kepala_sekolah" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-0">
                                <span>Kepala Sekolah</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-sky-700 dark:text-sky-400">
                                <input type="checkbox" name="roles[]" value="wakasek_kurikulum" id="edit_role_wakasek_kurikulum" class="w-3.5 h-3.5 rounded text-sky-600 focus:ring-0">
                                <span>Wakasek Kurikulum</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700 dark:text-amber-400">
                                <input type="checkbox" name="roles[]" value="wakasek_kesiswaan" id="edit_role_wakasek_kesiswaan" class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                                <span>Wakasek Kesiswaan</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-pink-700 dark:text-pink-400">
                                <input type="checkbox" name="roles[]" value="wakasek_humas" id="edit_role_wakasek_humas" class="w-3.5 h-3.5 rounded text-pink-600 focus:ring-0">
                                <span>Wakasek Humas</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-teal-700 dark:text-teal-400">
                                <input type="checkbox" name="roles[]" value="wakasek_sarpras" id="edit_role_wakasek_sarpras" class="w-3.5 h-3.5 rounded text-teal-600 focus:ring-0">
                                <span>Wakasek Sarpras</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-indigo-700 dark:text-indigo-400">
                                <input type="checkbox" name="roles[]" value="wali_kelas" id="edit_role_wali_kelas" class="w-3.5 h-3.5 rounded text-indigo-600 focus:ring-0">
                                <span>Wali Kelas</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-orange-700 dark:text-orange-400">
                                <input type="checkbox" name="roles[]" value="bk" id="edit_role_bk" class="w-3.5 h-3.5 rounded text-orange-600 focus:ring-0">
                                <span>Guru BK</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-cyan-700 dark:text-cyan-400">
                                <input type="checkbox" name="roles[]" value="pembina_ekstra" id="edit_role_pembina_ekstra" class="w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                                <span>Pembina Ekstrakurikuler</span>
                            </label>
                        </div>
                    </div>

                    <!-- Peran Tambahan Tenaga Kependidikan -->
                    <div id="edit_tendik_roles_box" class="hidden space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 text-[11px]">Peran Urusan Tenaga Administrasi (TU):</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-44 overflow-y-auto">
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-800 dark:text-emerald-300">
                                <input type="checkbox" name="roles[]" value="kepala_administrasi" id="edit_role_kepala_administrasi" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                                <span>Kepala Urusan Tata Usaha</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-purple-700 dark:text-purple-400">
                                <input type="checkbox" name="roles[]" value="operator" id="edit_role_operator_dapodik" class="w-3.5 h-3.5 rounded text-purple-600 focus:ring-0">
                                <span>Operator Dapodik / SIM</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-violet-700 dark:text-violet-400">
                                <input type="checkbox" name="roles[]" value="ta_kepegawaian" id="edit_role_ta_kepegawaian" class="w-3.5 h-3.5 rounded text-violet-600 focus:ring-0">
                                <span>Adm. Kepegawaian</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-blue-700 dark:text-blue-400">
                                <input type="checkbox" name="roles[]" value="ta_kesiswaan" id="edit_role_ta_kesiswaan" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-0">
                                <span>Adm. Kesiswaan</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700 dark:text-amber-400">
                                <input type="checkbox" name="roles[]" value="ta_sarpras" id="edit_role_ta_sarpras" class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                                <span>Adm. Sarana Prasarana</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700 dark:text-emerald-400">
                                <input type="checkbox" name="roles[]" value="bendahara" id="edit_role_bendahara_bosp" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                                <span>Bendahara BOSP / Sekolah</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-sky-700 dark:text-sky-400">
                                <input type="checkbox" name="roles[]" value="ta_persuratan" id="edit_role_ta_persuratan" class="w-3.5 h-3.5 rounded text-sky-600 focus:ring-0">
                                <span>Adm. Persuratan & Arsip</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-cyan-700 dark:text-cyan-400">
                                <input type="checkbox" name="roles[]" value="pustakawan" id="edit_role_tendik_pengelola_perpustakaan" class="w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                                <span>Pustakawan / Perpustakaan</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="document.getElementById('editTeacherModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-xs cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function handleTeacherPrimaryRoleChange(prefix) {
        const roleSelect = document.getElementById(prefix === 'edit' ? 'edit_teacher_role' : 'add_teacher_role');
        const role = roleSelect ? roleSelect.value : 'guru';
        const pendidikBox = document.getElementById(prefix + '_pendidik_roles_box');
        const tendikBox = document.getElementById(prefix + '_tendik_roles_box');

        if (pendidikBox) pendidikBox.classList.add('hidden');
        if (tendikBox) tendikBox.classList.add('hidden');

        if (role === 'guru') {
            if (pendidikBox) pendidikBox.classList.remove('hidden');
        } else if (role === 'staff') {
            if (tendikBox) tendikBox.classList.remove('hidden');
        }
    }

    // Live typing instant filter for teacher rows
    const searchInput = document.getElementById('teacherSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.teacher-row').forEach(row => {
                const text = row.getAttribute('data-search') || '';
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    function editTeacher(t) {
        document.getElementById('editTeacherForm').action = '/master/teachers/' + t.id;
        document.getElementById('edit_teacher_name').value = t.name || '';
        document.getElementById('edit_teacher_nip').value = t.nip || '';
        document.getElementById('edit_teacher_gender').value = t.gender || 'L';
        document.getElementById('edit_teacher_birth_place').value = t.birth_place || '';
        document.getElementById('edit_teacher_birth_date').value = t.birth_date ? t.birth_date.substring(0, 10) : '';
        document.getElementById('edit_teacher_phone').value = t.phone || '';
        document.getElementById('edit_teacher_address').value = t.address || '';

        document.getElementById('edit_teacher_employment_type').value = t.employment_type || (t.user && t.user.role === 'staff' ? 'Tenaga Kependidikan' : 'Guru');
        document.getElementById('edit_teacher_employment_status').value = t.employment_status || 'PNS';
        document.getElementById('edit_teacher_rank_group').value = t.rank_group || '';
        document.getElementById('edit_teacher_rank_tmt').value = t.rank_tmt ? t.rank_tmt.substring(0, 10) : '';
        document.getElementById('edit_teacher_position').value = t.position || t.title || '';
        document.getElementById('edit_teacher_teaching_subject').value = t.teaching_subject || '';
        document.getElementById('edit_teacher_position_tmt').value = t.position_tmt ? t.position_tmt.substring(0, 10) : '';
        document.getElementById('edit_teacher_mk_th').value = t.years_of_service_years ?? '';
        document.getElementById('edit_teacher_mk_bln').value = t.years_of_service_months ?? '';
        document.getElementById('edit_teacher_additional_tasks').value = t.additional_tasks || '';

        document.getElementById('edit_teacher_education_level').value = t.education_level || 'S1';
        document.getElementById('edit_teacher_education_institution').value = t.education_institution || '';
        document.getElementById('edit_teacher_education_major').value = t.education_major || '';
        document.getElementById('edit_teacher_education_grad_year').value = t.education_grad_year || '';

        const rawRole = t.user ? t.user.role : 'guru';
        const userRoles = t.user ? (Array.isArray(t.user.roles) ? t.user.roles : (t.user.roles ? JSON.parse(t.user.roles) : [rawRole])) : ['guru'];

        let mappedPrimaryRole = 'guru';
        if (rawRole === 'admin' || userRoles.includes('admin')) {
            mappedPrimaryRole = 'admin';
        } else if (['staff', 'tenaga_administrasi', 'kepala_administrasi', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'operator_dapodik', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_sarpras', 'ta_persuratan', 'tu_piket', 'bendahara', 'operator'].includes(rawRole)) {
            mappedPrimaryRole = 'staff';
        } else {
            mappedPrimaryRole = 'guru';
        }

        document.getElementById('edit_teacher_role').value = mappedPrimaryRole;
        handleTeacherPrimaryRoleChange('edit');
        document.getElementById('edit_teacher_email').value = t.user ? t.user.email : '';
        document.getElementById('edit_teacher_is_active').value = t.is_active ? '1' : '0';

        // Clear all checkboxes
        document.querySelectorAll('#editTeacherModal input[type="checkbox"][name="roles[]"]').forEach(cb => cb.checked = false);

        // Check assigned roles
        userRoles.forEach(r => {
            let el = document.getElementById('edit_role_' + r);
            if (el) el.checked = true;
            if (r === 'bendahara') {
                const bosp = document.getElementById('edit_role_bendahara_bosp');
                if (bosp) bosp.checked = true;
            }
            if (r === 'operator') {
                const op = document.getElementById('edit_role_operator_dapodik');
                if (op) op.checked = true;
            }
        });

        // Set checkboxes for extracurriculars
        document.querySelectorAll('.edit-teacher-ekstra-cb').forEach(cb => cb.checked = false);
        const assignedEkstraIds = [];
        if (t.extracurriculars && Array.isArray(t.extracurriculars)) {
            t.extracurriculars.forEach(e => assignedEkstraIds.push(e.id));
        }
        if (t.led_extracurriculars && Array.isArray(t.led_extracurriculars)) {
            t.led_extracurriculars.forEach(e => {
                if (!assignedEkstraIds.includes(e.id)) assignedEkstraIds.push(e.id);
            });
        }
        assignedEkstraIds.forEach(id => {
            const cb = document.getElementById('edit_ekstra_' + id);
            if (cb) cb.checked = true;
        });

        document.getElementById('editTeacherModal').classList.remove('hidden');
    }
</script>
@endsection

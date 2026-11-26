@extends('layouts.app')

@section('title', 'Catat Sesi Bimbingan & Konseling - BK')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.counseling.index') }}" class="hover:text-emerald-600 font-medium">Layanan Konseling</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Catat Sesi Baru</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Form Layanan Bimbingan & Konseling</h1>
            <p class="text-xs text-slate-400 mt-1">Dokumentasi operasional konseling berstandar Panduan Operasional Penyelenggaraan BK (POP-BK)</p>
        </div>

        <form action="{{ route('bk.counseling.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Siswa & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa Konseli *</label>
                    <select name="student_id" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ (old('student_id') ?? $selectedStudent?->id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->schoolClass?->name ?: 'Kelas -' }}) - NISN: {{ $s->nisn }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Konseling *</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Bidang Layanan & Komponen Program -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bidang Layanan BK *</label>
                    <select name="field_of_service" id="field_of_service" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="PRIBADI" {{ old('field_of_service') == 'PRIBADI' ? 'selected' : '' }}>Bimbingan Pribadi</option>
                        <option value="SOSIAL" {{ old('field_of_service') == 'SOSIAL' ? 'selected' : '' }}>Bimbingan Sosial</option>
                        <option value="BELAJAR" {{ old('field_of_service') == 'BELAJAR' ? 'selected' : '' }}>Bimbingan Belajar</option>
                        <option value="KARIER" {{ old('field_of_service') == 'KARIER' ? 'selected' : '' }}>Bimbingan Karier</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Layanan *</label>
                    <select name="session_type" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="INDIVIDUAL" {{ old('session_type') == 'INDIVIDUAL' ? 'selected' : '' }}>Konseling Individual</option>
                        <option value="KELOMPOK" {{ old('session_type') == 'KELOMPOK' ? 'selected' : '' }}>Konseling Kelompok</option>
                        <option value="KLASIKAL" {{ old('session_type') == 'KLASIKAL' ? 'selected' : '' }}>Bimbingan Klasikal</option>
                        <option value="MEDIASI" {{ old('session_type') == 'MEDIASI' ? 'selected' : '' }}>Mediasi</option>
                        <option value="KONFERENSI_KASUS" {{ old('session_type') == 'KONFERENSI_KASUS' ? 'selected' : '' }}>Konferensi Kasus</option>
                        <option value="REFERRAL" {{ old('session_type') == 'REFERRAL' ? 'selected' : '' }}>Alih Tangan Kasus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Komponen Program *</label>
                    <select name="service_category" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="LAYANAN_RESPONSIF" {{ old('service_category') == 'LAYANAN_RESPONSIF' ? 'selected' : '' }}>Layanan Responsif</option>
                        <option value="LAYANAN_DASAR" {{ old('service_category') == 'LAYANAN_DASAR' ? 'selected' : '' }}>Layanan Dasar</option>
                        <option value="PERENCANAAN_INDIVIDUAL" {{ old('service_category') == 'PERENCANAAN_INDIVIDUAL' ? 'selected' : '' }}>Perencanaan Individual</option>
                        <option value="DUKUNGAN_SISTEM" {{ old('service_category') == 'DUKUNGAN_SISTEM' ? 'selected' : '' }}>Dukungan Sistem</option>
                    </select>
                </div>
            </div>

            <!-- Panel Khusus Bimbingan Karier (Tampil Otomatis saat Bimbingan Karier Dipilih) -->
            <div id="career_guidance_panel" class="hidden p-4 sm:p-5 rounded-2xl bg-sky-50/80 dark:bg-sky-950/20 border border-sky-200 dark:border-sky-900/50 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-sky-200/60 dark:border-sky-900/40">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                            🎓
                        </span>
                        <div>
                            <h3 class="text-xs font-bold text-sky-950 dark:text-sky-300">Peminatan Bimbingan Karier & Arah Masa Depan Siswa</h3>
                            <p class="text-[11px] text-sky-700 dark:text-sky-400">Pilihan rencana studi lanjut (Kuliah) atau bekerja pasca kelulusan</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900/60 text-sky-700 dark:text-sky-300 border border-sky-300 dark:border-sky-800">
                        Bimbingan Karier
                    </span>
                </div>

                <!-- Pilihan 1: Kuliah atau Bekerja -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Rencana Arah Pasca Kelulusan *
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md">
                        <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer hover:border-sky-500 transition has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/60 dark:has-[:checked]:bg-sky-900/30 has-[:checked]:ring-1 has-[:checked]:ring-sky-500">
                            <input type="radio" name="career_path" value="KULIAH" id="career_path_kuliah" {{ old('career_path') === 'KULIAH' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>🏛️</span> Kuliah (Perguruan Tinggi)
                            </span>
                        </label>
                        <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer hover:border-sky-500 transition has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/60 dark:has-[:checked]:bg-sky-900/30 has-[:checked]:ring-1 has-[:checked]:ring-sky-500">
                            <input type="radio" name="career_path" value="BEKERJA" id="career_path_bekerja" {{ old('career_path') === 'BEKERJA' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>💼</span> Bekerja / Wirausaha
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Sub-Pilihan 1: Jika Memilih Kuliah -->
                <div id="college_detail_group" class="hidden space-y-3 pt-3 border-t border-sky-100 dark:border-sky-900/30">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                        <!-- Pilihan Jalur Kuliah (3 cols) -->
                        <div class="lg:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Pilihan Jalur Kuliah *
                            </label>
                            <select name="study_admission_track" id="study_admission_track" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-sky-500">
                                <option value="">-- Pilih Jalur Kuliah --</option>
                                <option value="SNBP" {{ old('study_admission_track') == 'SNBP' ? 'selected' : '' }}>SNBP (Prestasi / Rapor)</option>
                                <option value="SNBT" {{ old('study_admission_track') == 'SNBT' ? 'selected' : '' }}>SNBT (Tes UTBK)</option>
                                <option value="SPMB" {{ old('study_admission_track') == 'SPMB' ? 'selected' : '' }}>SPMB (Politeknik / Vokasi)</option>
                                <option value="MANDIRI" {{ old('study_admission_track') == 'MANDIRI' ? 'selected' : '' }}>Mandiri (PTN / PTS)</option>
                                <option value="KEDINASAN" {{ old('study_admission_track') == 'KEDINASAN' ? 'selected' : '' }}>Sekolah Kedinasan</option>
                                <option value="BEASISWA" {{ old('study_admission_track') == 'BEASISWA' ? 'selected' : '' }}>Beasiswa Luar / Dalam Negeri</option>
                            </select>
                        </div>

                        <!-- Nama Perguruan Tinggi Target (5 cols) -->
                        <div class="lg:col-span-5 relative" id="campus-combobox-wrapper">
                            <div class="flex items-center justify-between mb-1">
                                <label for="campus_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Nama Perguruan Tinggi Target *
                                </label>
                                <span class="text-[10px] text-sky-600 dark:text-sky-400 font-semibold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                    PTN / PTS Se-Jawa
                                </span>
                            </div>

                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>

                                <input type="text"
                                       name="campus_name"
                                       id="campus_name"
                                       autocomplete="off"
                                       list="campus_datalist"
                                       value="{{ old('campus_name') }}"
                                       placeholder="Cari universitas atau ketik manual..."
                                       class="w-full text-xs pl-8 pr-20 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-sky-500 transition-colors">

                                <div class="absolute right-1.5 flex items-center gap-1">
                                    <button type="button"
                                            id="btn-clear-campus"
                                            class="p-1 text-slate-400 hover:text-rose-500 rounded-md transition hidden"
                                            title="Hapus isian">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <button type="button"
                                            id="btn-toggle-campus"
                                            class="px-2 py-1 text-[11px] bg-sky-100 hover:bg-sky-200 dark:bg-sky-900/40 dark:hover:bg-sky-800 text-sky-700 dark:text-sky-300 rounded-lg border border-sky-200 dark:border-sky-800 transition flex items-center gap-1 font-semibold">
                                        <span>Cari</span>
                                        <svg id="chevron-campus" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Datalist Fallback Native Autocomplete -->
                            <datalist id="campus_datalist">
                                @if(isset($universities))
                                    @foreach($universities as $u)
                                        <option value="{{ $u['name'] }}">{{ $u['short_name'] }} - {{ $u['province'] }} ({{ $u['type'] }})</option>
                                    @endforeach
                                @endif
                            </datalist>

                            <!-- Dropdown Panel Popover Interaktif -->
                            <div id="campus_dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-3 max-h-80 flex flex-col">
                                <!-- Search & Filter Header -->
                                <div class="space-y-2 mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <div class="relative">
                                        <input type="text"
                                               id="campus_filter_search"
                                               placeholder="Ketik nama, singkatan (ITB, UI, UGM...), atau kota..."
                                               class="w-full text-xs pl-7 pr-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                        <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                                    </div>
                                    <!-- Filter Pills -->
                                    <div class="flex items-center gap-1 overflow-x-auto pb-1 text-[10px] no-scrollbar">
                                        <button type="button" data-filter="all" class="campus-pill px-2 py-0.5 rounded-full bg-sky-600 text-white font-bold whitespace-nowrap">Semua</button>
                                        <button type="button" data-filter="Jawa Barat" class="campus-pill px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-50 whitespace-nowrap">Jawa Barat</button>
                                        <button type="button" data-filter="DKI Jakarta" class="campus-pill px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-50 whitespace-nowrap">DKI Jakarta</button>
                                        <button type="button" data-filter="Banten" class="campus-pill px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-50 whitespace-nowrap">Banten</button>
                                        <button type="button" data-filter="Jawa Tengah" class="campus-pill px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-50 whitespace-nowrap">Jateng</button>
                                        <button type="button" data-filter="DI Yogyakarta" class="campus-pill px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-50 whitespace-nowrap">DIY Jogja</button>
                                        <button type="button" data-filter="Jawa Timur" class="campus-pill px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-50 whitespace-nowrap">Jatim</button>
                                    </div>
                                </div>

                                <!-- Campus List Items -->
                                <div id="campus_items_container" class="overflow-y-auto space-y-1 pr-1 flex-1 text-xs max-h-52">
                                    <!-- Injected via JavaScript -->
                                </div>

                                <!-- Manual Entry Note Footer -->
                                <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                                    <span>💡 Tidak ada di daftar? Langsung ketik manual di kolom input.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Jurusan / Program Studi (4 cols) -->
                        <div class="lg:col-span-4 relative" id="major-combobox-wrapper">
                            <div class="flex items-center justify-between mb-1">
                                <label for="study_program" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Jurusan / Program Studi *
                                </label>
                                <span id="major-source-badge" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Daftar Prodi
                                </span>
                            </div>

                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </span>

                                <input type="text"
                                       name="study_program"
                                       id="study_program"
                                       autocomplete="off"
                                       list="major_datalist"
                                       value="{{ old('study_program') }}"
                                       placeholder="Cari jurusan atau ketik manual..."
                                       class="w-full text-xs pl-8 pr-20 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-sky-500 transition-colors">

                                <div class="absolute right-1.5 flex items-center gap-1">
                                    <button type="button"
                                            id="btn-clear-major"
                                            class="p-1 text-slate-400 hover:text-rose-500 rounded-md transition hidden"
                                            title="Hapus isian">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <button type="button"
                                            id="btn-toggle-major"
                                            class="px-2 py-1 text-[11px] bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:hover:bg-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-lg border border-emerald-200 dark:border-emerald-800 transition flex items-center gap-1 font-semibold">
                                        <span>Cari</span>
                                        <svg id="chevron-major" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Datalist Fallback Native Autocomplete -->
                            <datalist id="major_datalist">
                                @if(isset($standardMajors))
                                    @foreach($standardMajors as $group => $majors)
                                        @foreach($majors as $m)
                                            <option value="{{ $m }}">{{ $group }}</option>
                                        @endforeach
                                    @endforeach
                                @endif
                            </datalist>

                            <!-- Dropdown Panel Popover Interaktif -->
                            <div id="major_dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-3 max-h-80 flex flex-col">
                                <!-- Search & Info Header -->
                                <div class="space-y-1.5 mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span id="major_dropdown_context" class="font-bold text-slate-700 dark:text-slate-300 truncate">Semua Program Studi</span>
                                        <button type="button" id="btn-show-all-majors" class="text-sky-600 dark:text-sky-400 hover:underline font-semibold hidden">Tampilkan Semua</button>
                                    </div>
                                    <div class="relative">
                                        <input type="text"
                                               id="major_filter_search"
                                               placeholder="Ketik nama jurusan (Informatika, Kedokteran...)"
                                               class="w-full text-xs pl-7 pr-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                        <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                                    </div>
                                </div>

                                <!-- Major List Items -->
                                <div id="major_items_container" class="overflow-y-auto space-y-1 pr-1 flex-1 text-xs max-h-52">
                                    <!-- Injected via JavaScript -->
                                </div>

                                <!-- Manual Entry Note Footer -->
                                <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                                    <span>💡 Tidak ada di daftar? Langsung ketik nama prodi manual di kolom input.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Pilihan 2: Jika Memilih Bekerja -->
                <div id="work_detail_group" class="hidden space-y-3 pt-3 border-t border-sky-100 dark:border-sky-900/30">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Bidang Pekerjaan / Perusahaan / Rencana Kerja yang Diminati
                        </label>
                        <input type="text"
                               name="work_field"
                               id="work_field"
                               value="{{ old('work_field') }}"
                               placeholder="Contoh: Teknisi Jaringan IT, Industri Otomotif, Perbankan, BUMN, atau Wirausaha Kuliner..."
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Tempat & Waktu -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Sesi</label>
                    <input type="text" name="place" value="{{ old('place', 'Ruang Bimbingan Konseling') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Mulai</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Selesai</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Topik & Deskripsi Masalah -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Pokok Bahasan *</label>
                <input type="text" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Konsultasi pemilihan jalur masuk PTN dan peminatan prodi saintek" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Latar Belakang & Deskripsi Masalah *</label>
                <textarea name="problem_description" rows="3" required placeholder="Uraikan keluhan, gejala, atau latar belakang konseling secara komprehensif..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('problem_description') }}</textarea>
            </div>

            <!-- Pendekatan & Kesepakatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pendekatan / Teknik Konseling</label>
                    <input type="text" name="counseling_approach" value="{{ old('counseling_approach') }}" placeholder="Contoh: Cognitive Behavioral Therapy (CBT) / Solution-Focused" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Sesi</label>
                    <select name="status" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="SELESAI" {{ old('status') == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                        <option value="SEDANG_BERLANGSUNG" {{ old('status') == 'SEDANG_BERLANGSUNG' ? 'selected' : '' }}>Sedang Berlangsung / Pemantauan</option>
                        <option value="TERJADWAL" {{ old('status') == 'TERJADWAL' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="DIRUJUK" {{ old('status') == 'DIRUJUK' ? 'selected' : '' }}>Dirujuk / Alih Tangan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Hasil Konseling & Komitmen Siswa</label>
                <textarea name="agreement_result" rows="2" placeholder="Komitmen tindakan nyata yang disepakati oleh siswa..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('agreement_result') }}</textarea>
            </div>

            <!-- Rencana Tindak Lanjut (RTL) & Tanggal Rencana Pertemuan -->
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📅</span>
                        <div>
                            <h3 class="text-xs font-bold text-amber-900 dark:text-amber-300">Rencana Tindak Lanjut (RTL) & Jadwal Pertemuan Lanjutan</h3>
                            <p class="text-[11px] text-amber-700 dark:text-amber-400">Tentukan rencana pemantauan serta tanggal pertemuan sesi tindak lanjut berikutnya</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                        Notifikasi Otomatis
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Rencana Pertemuan Berikutnya (Follow-Up RTL)
                        </label>
                        <input type="date"
                               name="follow_up_date"
                               id="follow_up_date"
                               value="{{ old('follow_up_date') }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tanggal ini otomatis terkirim sebagai notifikasi pengingat ke akun Koordinator BK & Guru BK.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Status Tindak Lanjut
                        </label>
                        <select name="follow_up_status" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                            <option value="PENDING" {{ old('follow_up_status') === 'PENDING' ? 'selected' : '' }}>Menunggu Jadwal Pertemuan (Pending)</option>
                            <option value="SELESAI" {{ old('follow_up_status') === 'SELESAI' ? 'selected' : '' }}>Selesai Ditindaklanjuti</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Uraian Rencana Tindak Lanjut (RTL)</label>
                    <textarea name="follow_up_plan" rows="2" placeholder="Langkah pemantauan bersama wali kelas, orang tua, konsultasi lanjutan dengan Guru BK, atau latihan soal/persiapan tes..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('follow_up_plan') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_confidential" value="1" id="is_confidential" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="is_confidential" class="text-xs text-slate-600 dark:text-slate-400">
                    Jaga Asas Kerahasiaan BK (Hanya dapat diakses oleh Guru BK dan Kepala Sekolah)
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <a href="{{ route('bk.counseling.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20">
                    Simpan Sesi Konseling
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldSelect = document.getElementById('field_of_service');
    const careerPanel = document.getElementById('career_guidance_panel');
    const radioKuliah = document.getElementById('career_path_kuliah');
    const radioBekerja = document.getElementById('career_path_bekerja');
    const collegeGroup = document.getElementById('college_detail_group');
    const workGroup = document.getElementById('work_detail_group');

    function toggleCareerPanel() {
        if (!fieldSelect || !careerPanel) return;
        if (fieldSelect.value === 'KARIER') {
            careerPanel.classList.remove('hidden');
            togglePathDetails();
        } else {
            careerPanel.classList.add('hidden');
        }
    }

    function togglePathDetails() {
        if (!radioKuliah || !radioBekerja) return;
        if (radioKuliah.checked) {
            collegeGroup.classList.remove('hidden');
            workGroup.classList.add('hidden');
        } else if (radioBekerja.checked) {
            workGroup.classList.remove('hidden');
            collegeGroup.classList.add('hidden');
        } else {
            collegeGroup.classList.add('hidden');
            workGroup.classList.add('hidden');
        }
    }

    if (fieldSelect) {
        fieldSelect.addEventListener('change', toggleCareerPanel);
    }
    if (radioKuliah) {
        radioKuliah.addEventListener('change', togglePathDetails);
    }
    if (radioBekerja) {
        radioBekerja.addEventListener('change', togglePathDetails);
    }

    // Inisialisasi kondisi awal form
    toggleCareerPanel();

    // ========================================================
    // LOGIKA SEARCHABLE COMBOBOX PERGURUAN TINGGI & JURUSAN SE-JAWA
    // ========================================================
    const universitiesData = @json($universities ?? []);
    const standardMajorsData = @json($standardMajors ?? []);

    const campusInput = document.getElementById('campus_name');
    const campusDropdown = document.getElementById('campus_dropdown');
    const btnToggleCampus = document.getElementById('btn-toggle-campus');
    const btnClearCampus = document.getElementById('btn-clear-campus');
    const chevronCampus = document.getElementById('chevron-campus');
    const campusSearch = document.getElementById('campus_filter_search');
    const campusItemsContainer = document.getElementById('campus_items_container');
    const campusPills = document.querySelectorAll('.campus-pill');

    const majorInput = document.getElementById('study_program');
    const majorDropdown = document.getElementById('major_dropdown');
    const btnToggleMajor = document.getElementById('btn-toggle-major');
    const btnClearMajor = document.getElementById('btn-clear-major');
    const chevronMajor = document.getElementById('chevron-major');
    const majorSearch = document.getElementById('major_filter_search');
    const majorItemsContainer = document.getElementById('major_items_container');
    const majorContextLabel = document.getElementById('major_dropdown_context');
    const btnShowAllMajors = document.getElementById('btn-show-all-majors');
    const majorSourceBadge = document.getElementById('major-source-badge');

    let currentCampusFilterProvince = 'all';
    let selectedUniversity = null;

    // Deteksi awal jika ada isian sebelumnya (old value)
    if (campusInput && campusInput.value.trim() !== '') {
        btnClearCampus?.classList.remove('hidden');
        selectedUniversity = universitiesData.find(u => u.name.toLowerCase() === campusInput.value.trim().toLowerCase());
    }
    if (majorInput && majorInput.value.trim() !== '') {
        btnClearMajor?.classList.remove('hidden');
    }

    // Render Daftar Perguruan Tinggi
    function renderCampusList() {
        if (!campusItemsContainer) return;
        const query = (campusSearch?.value || '').toLowerCase().trim();

        const filtered = universitiesData.filter(u => {
            const matchProvince = currentCampusFilterProvince === 'all' || u.province.toLowerCase() === currentCampusFilterProvince.toLowerCase();
            const matchQuery = !query ||
                u.name.toLowerCase().includes(query) ||
                u.short_name.toLowerCase().includes(query) ||
                u.province.toLowerCase().includes(query) ||
                u.type.toLowerCase().includes(query);
            return matchProvince && matchQuery;
        });

        if (filtered.length === 0) {
            campusItemsContainer.innerHTML = `
                <div class="p-3 text-center text-slate-400 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <p class="font-semibold text-xs">Perguruan tinggi tidak ditemukan.</p>
                    <p class="text-[11px] mt-1 text-sky-600 dark:text-sky-400 cursor-pointer hover:underline font-bold" id="btn-use-custom-campus">
                        + Gunakan nama "${escapeHtml(campusSearch?.value || '')}" secara manual
                    </p>
                </div>
            `;
            document.getElementById('btn-use-custom-campus')?.addEventListener('click', function() {
                selectCampus(campusSearch.value.trim());
            });
            return;
        }

        let html = '';
        filtered.forEach(u => {
            const isSelected = campusInput.value.trim().toLowerCase() === u.name.toLowerCase();
            const typeBadgeColor = u.type === 'PTN' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300' :
                                  (u.type === 'Politeknik' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300' :
                                  (u.type === 'Kedinasan' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300' :
                                  (u.type === 'PTKIN' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' :
                                  'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300')));

            html += `
                <div class="campus-item p-2 rounded-xl cursor-pointer transition flex items-center justify-between gap-2 ${isSelected ? 'bg-sky-50 dark:bg-sky-950/60 border border-sky-300 dark:border-sky-800 font-bold' : 'hover:bg-slate-100 dark:hover:bg-slate-800/70'}" data-name="${escapeHtml(u.name)}">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-bold text-slate-800 dark:text-slate-100 text-xs">${escapeHtml(u.name)}</span>
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold ${typeBadgeColor}">${u.type}</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5">${u.province} &bull; ${u.majors ? u.majors.length : 0} Program Studi Terdata</p>
                    </div>
                    <span class="text-xs text-sky-600 font-bold shrink-0">&rarr;</span>
                </div>
            `;
        });
        campusItemsContainer.innerHTML = html;

        // Pasang event click tiap item
        campusItemsContainer.querySelectorAll('.campus-item').forEach(el => {
            el.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                selectCampus(name);
            });
        });
    }

    function selectCampus(name) {
        campusInput.value = name;
        btnClearCampus?.classList.remove('hidden');
        closeCampusDropdown();

        // Cari data universitas terpilih
        selectedUniversity = universitiesData.find(u => u.name.toLowerCase() === name.toLowerCase());

        // Update list jurusan sesuai kampus yang dipilih
        if (selectedUniversity && selectedUniversity.majors && selectedUniversity.majors.length > 0) {
            majorContextLabel.textContent = `Prodi di ${selectedUniversity.short_name || selectedUniversity.name}`;
            btnShowAllMajors?.classList.remove('hidden');
            majorSourceBadge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> ${selectedUniversity.short_name || 'Kampus'}`;
        } else {
            majorContextLabel.textContent = 'Semua Program Studi Terakreditasi';
            btnShowAllMajors?.classList.add('hidden');
            majorSourceBadge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Nasional`;
        }
        renderMajorList();
    }

    function openCampusDropdown() {
        closeMajorDropdown();
        campusDropdown?.classList.remove('hidden');
        chevronCampus?.classList.add('rotate-180');
        renderCampusList();
        setTimeout(() => campusSearch?.focus(), 50);
    }

    function closeCampusDropdown() {
        campusDropdown?.classList.add('hidden');
        chevronCampus?.classList.remove('rotate-180');
    }

    btnToggleCampus?.addEventListener('click', function(e) {
        e.stopPropagation();
        if (campusDropdown?.classList.contains('hidden')) {
            openCampusDropdown();
        } else {
            closeCampusDropdown();
        }
    });

    btnClearCampus?.addEventListener('click', function() {
        campusInput.value = '';
        btnClearCampus.classList.add('hidden');
        selectedUniversity = null;
        majorContextLabel.textContent = 'Semua Program Studi Terakreditasi';
        btnShowAllMajors?.classList.add('hidden');
        majorSourceBadge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Nasional`;
        renderMajorList();
        campusInput.focus();
    });

    campusInput?.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            btnClearCampus?.classList.remove('hidden');
        } else {
            btnClearCampus?.classList.add('hidden');
        }
    });

    campusSearch?.addEventListener('input', renderCampusList);

    campusPills.forEach(pill => {
        pill.addEventListener('click', function() {
            campusPills.forEach(p => {
                p.classList.remove('bg-sky-600', 'text-white', 'font-bold');
                p.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
            });
            this.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
            this.classList.add('bg-sky-600', 'text-white', 'font-bold');
            currentCampusFilterProvince = this.getAttribute('data-filter') || 'all';
            renderCampusList();
        });
    });

    // ========================================================
    // RENDER DAFTAR JURUSAN / PROGRAM STUDI
    // ========================================================
    let showOnlyCampusMajors = true;

    function renderMajorList() {
        if (!majorItemsContainer) return;
        const query = (majorSearch?.value || '').toLowerCase().trim();

        let majorsList = [];

        if (selectedUniversity && selectedUniversity.majors && showOnlyCampusMajors) {
            majorsList = selectedUniversity.majors.map(m => ({ name: m, group: selectedUniversity.short_name || 'Kampus' }));
        } else {
            // Gabungkan dari standardMajorsData
            Object.keys(standardMajorsData).forEach(group => {
                standardMajorsData[group].forEach(m => {
                    majorsList.push({ name: m, group: group });
                });
            });
        }

        const filtered = majorsList.filter(item => {
            return !query || item.name.toLowerCase().includes(query) || item.group.toLowerCase().includes(query);
        });

        if (filtered.length === 0) {
            majorItemsContainer.innerHTML = `
                <div class="p-3 text-center text-slate-400 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <p class="font-semibold text-xs">Jurusan tidak ditemukan.</p>
                    <p class="text-[11px] mt-1 text-emerald-600 dark:text-emerald-400 cursor-pointer hover:underline font-bold" id="btn-use-custom-major">
                        + Gunakan nama "${escapeHtml(majorSearch?.value || '')}" secara manual
                    </p>
                </div>
            `;
            document.getElementById('btn-use-custom-major')?.addEventListener('click', function() {
                selectMajor(majorSearch.value.trim());
            });
            return;
        }

        let html = '';
        filtered.forEach(item => {
            const isSelected = majorInput.value.trim().toLowerCase() === item.name.toLowerCase();
            html += `
                <div class="major-item p-2 rounded-xl cursor-pointer transition flex items-center justify-between gap-2 ${isSelected ? 'bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 font-bold' : 'hover:bg-slate-100 dark:hover:bg-slate-800/70'}" data-name="${escapeHtml(item.name)}">
                    <div class="min-w-0">
                        <span class="font-bold text-slate-800 dark:text-slate-100 text-xs block">${escapeHtml(item.name)}</span>
                        <span class="text-[10px] text-slate-400 block">${item.group}</span>
                    </div>
                    <span class="text-xs text-emerald-600 font-bold shrink-0">&check;</span>
                </div>
            `;
        });
        majorItemsContainer.innerHTML = html;

        majorItemsContainer.querySelectorAll('.major-item').forEach(el => {
            el.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                selectMajor(name);
            });
        });
    }

    function selectMajor(name) {
        majorInput.value = name;
        btnClearMajor?.classList.remove('hidden');
        closeMajorDropdown();
    }

    function openMajorDropdown() {
        closeCampusDropdown();
        majorDropdown?.classList.remove('hidden');
        chevronMajor?.classList.add('rotate-180');
        renderMajorList();
        setTimeout(() => majorSearch?.focus(), 50);
    }

    function closeMajorDropdown() {
        majorDropdown?.classList.add('hidden');
        chevronMajor?.classList.remove('rotate-180');
    }

    btnToggleMajor?.addEventListener('click', function(e) {
        e.stopPropagation();
        if (majorDropdown?.classList.contains('hidden')) {
            openMajorDropdown();
        } else {
            closeMajorDropdown();
        }
    });

    btnClearMajor?.addEventListener('click', function() {
        majorInput.value = '';
        btnClearMajor.classList.add('hidden');
        majorInput.focus();
    });

    majorInput?.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            btnClearMajor?.classList.remove('hidden');
        } else {
            btnClearMajor?.classList.add('hidden');
        }
    });

    majorSearch?.addEventListener('input', renderMajorList);

    btnShowAllMajors?.addEventListener('click', function() {
        showOnlyCampusMajors = !showOnlyCampusMajors;
        if (showOnlyCampusMajors && selectedUniversity) {
            majorContextLabel.textContent = `Prodi di ${selectedUniversity.short_name || selectedUniversity.name}`;
            this.textContent = 'Tampilkan Semua';
        } else {
            majorContextLabel.textContent = 'Semua Program Studi Nasional';
            this.textContent = 'Kembali ke Kampus Terpilih';
        }
        renderMajorList();
    });

    // Tutup dropdown jika klik di luar area
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#campus-combobox-wrapper')) {
            closeCampusDropdown();
        }
        if (!e.target.closest('#major-combobox-wrapper')) {
            closeMajorDropdown();
        }
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endsection

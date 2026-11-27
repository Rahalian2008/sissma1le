@extends('layouts.app')

@section('title', 'Edit Data Tracer Alumni - ' . $tracer->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @include('bk.partials.nav')

    <!-- Header & Navigation -->
    <div class="flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.tracer.index') }}" class="hover:text-indigo-600 font-medium">Tracer Alumni</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Edit Data Standar</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Edit Data Tracer Alumni</h1>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi kuesioner standar Kemdikbudristek &amp; DIKTI</p>
        </div>
        <a href="{{ route('bk.tracer.show', $tracer) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            Batal
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs">
        <div class="font-bold mb-1">Terdapat kesalahan pengisian data:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('bk.tracer.update', $tracer) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Identitas & Latar Belakang Alumni -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center text-xs font-black">1</span>
                <span>Identitas &amp; Latar Belakang Alumni</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Nama Lengkap *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $tracer->full_name) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $tracer->nisn) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">NIS Sekolah</label>
                    <input type="text" name="nis" value="{{ old('nis', $tracer->nis) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Jenis Kelamin *</label>
                    <select name="gender" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                        <option value="L" {{ old('gender', $tracer->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $tracer->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Tahun Lulus *</label>
                    <input type="text" name="graduation_year" value="{{ old('graduation_year', $tracer->graduation_year) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Peminatan / Jurusan SMA *</label>
                    <input list="high_school_major_list" type="text" name="high_school_major" value="{{ old('high_school_major', $tracer->high_school_major) }}" placeholder="MIPA / IPS / Kurikulum Merdeka"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                    <datalist id="high_school_major_list">
                        <option value="MIPA (Matematika dan Ilmu Pengetahuan Alam)"></option>
                        <option value="IPS (Ilmu Pengetahuan Sosial)"></option>
                        <option value="Bahasa dan Budaya"></option>
                        <option value="Kurikulum Merdeka (Peminatan Mandiri)"></option>
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Sumber Utama Pembiayaan Sekolah</label>
                    <select name="study_funding_source" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                        <option value="Biaya sendiri / keluarga" {{ old('study_funding_source', $tracer->study_funding_source) === 'Biaya sendiri / keluarga' ? 'selected' : '' }}>Biaya sendiri / keluarga</option>
                        <option value="Beasiswa pemerintah (KIP / PIP)" {{ old('study_funding_source', $tracer->study_funding_source) === 'Beasiswa pemerintah (KIP / PIP)' ? 'selected' : '' }}>Beasiswa pemerintah (KIP / PIP)</option>
                        <option value="Beasiswa swasta / yayasan" {{ old('study_funding_source', $tracer->study_funding_source) === 'Beasiswa swasta / yayasan' ? 'selected' : '' }}>Beasiswa swasta / yayasan</option>
                        <option value="Lainnya" {{ old('study_funding_source', $tracer->study_funding_source) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Rombel / Kelas Terakhir</label>
                    <input type="text" name="last_class" value="{{ old('last_class', $tracer->last_class) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">No. WhatsApp / HP *</label>
                    <input type="text" name="phone" value="{{ old('phone', $tracer->phone) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Email Aktif</label>
                    <input type="email" name="email" value="{{ old('email', $tracer->email) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Kota Domisili Saat Ini</label>
                    <input type="text" name="current_city" value="{{ old('current_city', $tracer->current_city) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Bagian 2: Status Aktivitas Saat Ini -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-600 flex items-center justify-center text-xs font-black">2</span>
                <span>Status Kegiatan Saat Ini</span>
            </h2>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Pilih Status Aktivitas Utama *</label>
                <select name="activity_status" id="activity_status_select" required onchange="toggleEditSections()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                    <option value="KULIAH" {{ old('activity_status', $tracer->activity_status) === 'KULIAH' ? 'selected' : '' }}>🎓 Melanjutkan Pendidikan (Kuliah / Studi Lanjut)</option>
                    <option value="BEKERJA" {{ old('activity_status', $tracer->activity_status) === 'BEKERJA' ? 'selected' : '' }}>💼 Bekerja (Full Time / Part Time)</option>
                    <option value="WIRAUSAHA" {{ old('activity_status', $tracer->activity_status) === 'WIRAUSAHA' ? 'selected' : '' }}>🚀 Wiraswasta / Berwirausaha Mandiri</option>
                    <option value="BELUM_MEMUNGKINKAN" {{ old('activity_status', $tracer->activity_status) === 'BELUM_MEMUNGKINKAN' ? 'selected' : '' }}>🏠 Belum Memungkinkan Bekerja (Mengurus Keluarga / Kondisi Lain)</option>
                    <option value="KURSUS_PELATIHAN" {{ old('activity_status', $tracer->activity_status) === 'KURSUS_PELATIHAN' ? 'selected' : '' }}>📚 Kursus / Pelatihan Keterampilan</option>
                    <option value="MENCARI_KERJA" {{ old('activity_status', $tracer->activity_status) === 'MENCARI_KERJA' ? 'selected' : '' }}>🔍 Tidak Bekerja Tetapi Sedang Mencari Kerja</option>
                </select>
            </div>

            <!-- Detail Kuliah / Studi Lanjut -->
            <div id="edit-kuliah" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400">Rincian Studi Lanjut (Perguruan Tinggi)</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Jenjang Pendidikan</label>
                        <select name="college_level" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="S1" {{ old('college_level', $tracer->college_level) === 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                            <option value="D4" {{ old('college_level', $tracer->college_level) === 'D4' ? 'selected' : '' }}>D4 (Sarjana Terapan)</option>
                            <option value="D3" {{ old('college_level', $tracer->college_level) === 'D3' ? 'selected' : '' }}>D3 (Diploma)</option>
                            <option value="Kedinasan" {{ old('college_level', $tracer->college_level) === 'Kedinasan' ? 'selected' : '' }}>Sekolah Kedinasan</option>
                            <option value="Profesi" {{ old('college_level', $tracer->college_level) === 'Profesi' ? 'selected' : '' }}>Pendidikan Profesi</option>
                            <option value="S2" {{ old('college_level', $tracer->college_level) === 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Jalur Masuk</label>
                        <input type="text" name="admission_track" value="{{ old('admission_track', $tracer->admission_track) }}" placeholder="SNBP / SNBT / Mandiri / Prestasi"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Perguruan Tinggi</label>
                        <input list="universities_list" type="text" name="campus_name" value="{{ old('campus_name', $tracer->campus_name) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                        <datalist id="universities_list">
                            @foreach($universities as $uni)
                                <option value="{{ $uni['name'] }}">{{ $uni['short_name'] }} - {{ $uni['province'] }} ({{ $uni['type'] }})</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Program Studi / Jurusan</label>
                        <input list="majors_list" type="text" name="study_program" value="{{ old('study_program', $tracer->study_program) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                        <datalist id="majors_list">
                            @foreach($standardMajors as $group => $majors)
                                @foreach($majors as $m)
                                    <option value="{{ $m }}">{{ $group }}</option>
                                @endforeach
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Tanggal Mulai Masuk Kuliah</label>
                        <input type="text" name="college_start_date" value="{{ old('college_start_date', $tracer->college_start_date) }}" placeholder="Bulan / Tahun (misal: September 2025)"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Sumber Biaya Pendidikan Kuliah</label>
                        <select name="college_funding_source" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="Mandiri / Biaya Sendiri" {{ old('college_funding_source', $tracer->college_funding_source) === 'Mandiri / Biaya Sendiri' ? 'selected' : '' }}>Mandiri / Biaya Sendiri</option>
                            <option value="Beasiswa Pemerintah (KIP-K / LPDP)" {{ old('college_funding_source', $tracer->college_funding_source) === 'Beasiswa Pemerintah (KIP-K / LPDP)' ? 'selected' : '' }}>Beasiswa Pemerintah (KIP-K / LPDP)</option>
                            <option value="Beasiswa Swasta / Yayasan" {{ old('college_funding_source', $tracer->college_funding_source) === 'Beasiswa Swasta / Yayasan' ? 'selected' : '' }}>Beasiswa Swasta / Yayasan</option>
                            <option value="Ikatan Dinas" {{ old('college_funding_source', $tracer->college_funding_source) === 'Ikatan Dinas' ? 'selected' : '' }}>Ikatan Dinas</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Detail Bekerja & Transisi Dunia Kerja -->
            <div id="edit-bekerja" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800 hidden">
                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Rincian Pekerjaan &amp; Transisi ke Dunia Kerja</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Perusahaan / Instansi</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $tracer->company_name) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Jenis Instansi / Tempat Kerja</label>
                        <select name="company_type" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="Perusahaan swasta nasional / multinasional" {{ old('company_type', $tracer->company_type) === 'Perusahaan swasta nasional / multinasional' ? 'selected' : '' }}>Perusahaan swasta nasional / multinasional</option>
                            <option value="Instansi pemerintah / ASN" {{ old('company_type', $tracer->company_type) === 'Instansi pemerintah / ASN' ? 'selected' : '' }}>Instansi pemerintah / ASN</option>
                            <option value="BUMN / BUMD" {{ old('company_type', $tracer->company_type) === 'BUMN / BUMD' ? 'selected' : '' }}>BUMN / BUMD</option>
                            <option value="Organisasi non-profit / LSM" {{ old('company_type', $tracer->company_type) === 'Organisasi non-profit / LSM' ? 'selected' : '' }}>Organisasi non-profit / LSM</option>
                            <option value="Wiraswasta / Usaha sendiri" {{ old('company_type', $tracer->company_type) === 'Wiraswasta / Usaha sendiri' ? 'selected' : '' }}>Wiraswasta / Usaha sendiri</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Posisi / Jabatan</label>
                        <input type="text" name="job_position" value="{{ old('job_position', $tracer->job_position) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Sifat Pekerjaan</label>
                        <select name="work_type" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="Full Time" {{ old('work_type', $tracer->work_type) === 'Full Time' ? 'selected' : '' }}>Penuh Waktu (Full Time)</option>
                            <option value="Part Time" {{ old('work_type', $tracer->work_type) === 'Part Time' ? 'selected' : '' }}>Paruh Waktu (Part Time)</option>
                            <option value="Kontrak / Proyek" {{ old('work_type', $tracer->work_type) === 'Kontrak / Proyek' ? 'selected' : '' }}>Kontrak / Freelance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Lokasi Instansi (Kota / Provinsi)</label>
                        <input type="text" name="company_location" value="{{ old('company_location', $tracer->company_location) }}" placeholder="Kota / Provinsi"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Kisaran Pendapatan (Take Home Pay)</label>
                        <input type="text" name="monthly_income_range" value="{{ old('monthly_income_range', $tracer->monthly_income_range) }}" placeholder="Rp 2.500.000 - Rp 5.000.000"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Kapan Mulai Mencari Kerja</label>
                        <select name="job_search_start" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="Sebelum lulus" {{ old('job_search_start', $tracer->job_search_start) === 'Sebelum lulus' ? 'selected' : '' }}>Sebelum lulus</option>
                            <option value="Setelah lulus" {{ old('job_search_start', $tracer->job_search_start) === 'Setelah lulus' ? 'selected' : '' }}>Setelah lulus</option>
                            <option value="Tidak mencari kerja" {{ old('job_search_start', $tracer->job_search_start) === 'Tidak mencari kerja' ? 'selected' : '' }}>Tidak mencari kerja</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Waktu Tunggu Dapat Kerja</label>
                        <select name="job_waiting_time" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="Kurang dari 3 bulan" {{ old('job_waiting_time', $tracer->job_waiting_time) === 'Kurang dari 3 bulan' ? 'selected' : '' }}>Kurang dari 3 bulan</option>
                            <option value="3 - 6 bulan" {{ old('job_waiting_time', $tracer->job_waiting_time) === '3 - 6 bulan' ? 'selected' : '' }}>3 - 6 bulan</option>
                            <option value="Lebih dari 6 bulan" {{ old('job_waiting_time', $tracer->job_waiting_time) === 'Lebih dari 6 bulan' ? 'selected' : '' }}>Lebih dari 6 bulan</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Cara Mendapatkan Pekerjaan</label>
                        <select name="job_search_method" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="Bursa kerja / Job Fair" {{ old('job_search_method', $tracer->job_search_method) === 'Bursa kerja / Job Fair' ? 'selected' : '' }}>Bursa kerja / Job Fair</option>
                            <option value="Relasi / Guru / Alumni" {{ old('job_search_method', $tracer->job_search_method) === 'Relasi / Guru / Alumni' ? 'selected' : '' }}>Relasi / Guru / Alumni</option>
                            <option value="Iklan lowongan online / LinkedIn" {{ old('job_search_method', $tracer->job_search_method) === 'Iklan lowongan online / LinkedIn' ? 'selected' : '' }}>Iklan lowongan online / LinkedIn</option>
                            <option value="Magang / PKL sebelumnya" {{ old('job_search_method', $tracer->job_search_method) === 'Magang / PKL sebelumnya' ? 'selected' : '' }}>Magang / PKL sebelumnya</option>
                            <option value="Membangun usaha sendiri" {{ old('job_search_method', $tracer->job_search_method) === 'Membangun usaha sendiri' ? 'selected' : '' }}>Membangun usaha sendiri</option>
                        </select>
                    </div>
                </div>

                <!-- Relevansi Pendidikan -->
                <div class="pt-3 border-t border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Relevansi Pendidikan dengan Pekerjaan</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Keeratan Bidang Studi dengan Pekerjaan</label>
                            <select name="job_major_relevance" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                                <option value="Sangat Erat" {{ old('job_major_relevance', $tracer->job_major_relevance) === 'Sangat Erat' ? 'selected' : '' }}>Sangat Erat</option>
                                <option value="Erat" {{ old('job_major_relevance', $tracer->job_major_relevance) === 'Erat' ? 'selected' : '' }}>Erat</option>
                                <option value="Cukup Erat" {{ old('job_major_relevance', $tracer->job_major_relevance) === 'Cukup Erat' ? 'selected' : '' }}>Cukup Erat</option>
                                <option value="Kurang Erat" {{ old('job_major_relevance', $tracer->job_major_relevance) === 'Kurang Erat' ? 'selected' : '' }}>Kurang Erat</option>
                                <option value="Tidak Sama Sekali" {{ old('job_major_relevance', $tracer->job_major_relevance) === 'Tidak Sama Sekali' ? 'selected' : '' }}>Tidak Sama Sekali</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Tingkat Pendidikan Paling Sesuai</label>
                            <select name="job_education_level_match" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                                <option value="Tingkat yang sama" {{ old('job_education_level_match', $tracer->job_education_level_match) === 'Tingkat yang sama' ? 'selected' : '' }}>Tingkat yang sama</option>
                                <option value="Setingkat lebih tinggi" {{ old('job_education_level_match', $tracer->job_education_level_match) === 'Setingkat lebih tinggi' ? 'selected' : '' }}>Setingkat lebih tinggi</option>
                                <option value="Setingkat lebih rendah" {{ old('job_education_level_match', $tracer->job_education_level_match) === 'Setingkat lebih rendah' ? 'selected' : '' }}>Setingkat lebih rendah</option>
                                <option value="Tidak perlu pendidikan tinggi" {{ old('job_education_level_match', $tracer->job_education_level_match) === 'Tidak perlu pendidikan tinggi' ? 'selected' : '' }}>Tidak perlu pendidikan tinggi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Wirausaha -->
            <div id="edit-wirausaha" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800 hidden">
                <div class="text-xs font-bold text-amber-600 dark:text-amber-400">Rincian Usaha Mandiri &amp; Wiraswasta</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Usaha / Brand</label>
                        <input type="text" name="business_name" value="{{ old('business_name', $tracer->business_name) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Bidang Usaha</label>
                        <input type="text" name="business_field" value="{{ old('business_field', $tracer->business_field) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Estimasi Omzet Bulanan</label>
                        <input type="text" name="business_revenue_range" value="{{ old('business_revenue_range', $tracer->business_revenue_range) }}" placeholder="Contoh: Rp 5.000.000 - Rp 15.000.000"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Jumlah Tenaga Kerja / Karyawan</label>
                        <input type="number" min="0" name="employee_count" value="{{ old('employee_count', $tracer->employee_count ?? 0) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian 3: Penilaian Kompetensi & Evaluasi Pembelajaran (Standar DIKTI) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-xs font-black">3</span>
                <span>Evaluasi Kompetensi, Pembelajaran &amp; Masukan Sekolah</span>
            </h2>

            <!-- 6 Kompetensi Lulusan -->
            <div class="mb-5 pb-5 border-b border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Penilaian 6 Kompetensi Lulusan (Skala 1 - 5 Bintang)</label>
                @php
                    $stdCompetencies = \App\Models\BkTracerAlumnus::standardCompetencies();
                    $evalCompetencies = (array) ($tracer->competencies_evaluation ?? []);
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($stdCompetencies as $cKey => $cLabel)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 text-xs">
                            <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $cLabel }}</span>
                            <select name="competencies[{{ $cKey }}]" class="px-2 py-1 rounded-lg text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-indigo-600">
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ (int)($evalCompetencies[$cKey] ?? 4) === $i ? 'selected' : '' }}>{{ $i }} ⭐</option>
                                @endfor
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 5 Metode Pembelajaran -->
            <div class="mb-5 pb-5 border-b border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Evaluasi Penekanan Metode Pembelajaran (Skala 1 - 5)</label>
                @php
                    $stdMethods = \App\Models\BkTracerAlumnus::standardLearningMethods();
                    $evalMethods = (array) ($tracer->learning_methods_evaluation ?? []);
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($stdMethods as $mKey => $mLabel)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 text-xs">
                            <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $mLabel }}</span>
                            <select name="learning_methods[{{ $mKey }}]" class="px-2 py-1 rounded-lg text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-teal-600">
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ (int)($evalMethods[$mKey] ?? 4) === $i ? 'selected' : '' }}>Skala {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Hal yang Paling Perlu Ditingkatkan di Sekolah (Saran Standar Kemdikbudristek)</label>
                    <textarea name="improvement_suggestions" rows="2" placeholder="Tuliskan fasilitas, kurikulum, atau kompetensi yang perlu ditingkatkan..."
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">{{ old('improvement_suggestions', $tracer->improvement_suggestions) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Rating Kepuasan Layanan BK (1 - 5 Bintang)</label>
                    <input type="number" min="1" max="5" name="bk_service_rating" value="{{ old('bk_service_rating', $tracer->bk_service_rating) }}"
                           class="w-24 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Dampak Bimbingan Karier &amp; Konseling BK</label>
                    <textarea name="bk_career_guidance_impact" rows="2"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">{{ old('bk_career_guidance_impact', $tracer->bk_career_guidance_impact) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Pesan &amp; Saran Tambahan untuk Almamater</label>
                    <textarea name="school_feedback" rows="2"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">{{ old('school_feedback', $tracer->school_feedback) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('bk.tracer.show', $tracer) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                Simpan Perubahan Standar
            </button>
        </div>
    </form>
</div>

<script>
    function toggleEditSections() {
        const val = document.getElementById('activity_status_select').value;
        const k = document.getElementById('edit-kuliah');
        const b = document.getElementById('edit-bekerja');
        const w = document.getElementById('edit-wirausaha');

        if (k) k.classList.add('hidden');
        if (b) b.classList.add('hidden');
        if (w) w.classList.add('hidden');

        if (val === 'KULIAH' && k) k.classList.remove('hidden');
        else if (val === 'BEKERJA' && b) b.classList.remove('hidden');
        else if (val === 'WIRAUSAHA' && w) w.classList.remove('hidden');
    }
    document.addEventListener('DOMContentLoaded', toggleEditSections);
</script>
@endsection
